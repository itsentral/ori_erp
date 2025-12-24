<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');		
		$this->load->model('Acc_model');
		$this->load->model('Jurnal_model');
		$this->load->model('All_model');

		$this->kode_trs = $this->All_model->GetAutoGenerate('id_trans');
		// Your own constructor code
		if (!$this->session->userdata('isORIlogin')) {
			redirect('login');
		}
	}

	public function index()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Delivery',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data delivery');
		$this->load->view('Delivery/index', $data);
	}

	public function server_side_delivery()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_delivery(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
		$GET_USERNAME 		= get_detail_user();
		$GET_DET_FD 		= get_detailFinalDrawing();
		$GET_SALES_ORDER 	= get_detail_ipp();
		$tanki_model = $this->tanki_model;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}


			$edit = "";
			$edit_print = "";
			$print = "";
			$print2 = "";
			$excel = "";
			$release = "";
			if (empty($row['lock_delivery_date'])) {
				$edit = "<a href='" . base_url('delivery/edit_delivery/' . $row['kode_delivery']) . "' class='btn btn-sm btn-primary' title='Edit'><i class='fa fa-edit'></i></a>";
				$print = "<a href='" . base_url('delivery/print_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				// $print2 = "<a href='" . base_url('delivery/print_delivery_draft/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-default' title='Print LX'><i class='fa fa-print'></i></a>";
				$excel = "<a href='" . base_url('delivery/delivery_xls/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-default' title='Excel'><i class='fa fa-file-excel-o'></i></a>";
				$release = "<button type='button' class='btn btn-sm btn-success lock_spool' data-spool='" . $row['kode_delivery'] . "' title='Lock Delivery'><i class='fa fa-check'></i></button>";
				// $edit_print = "<button type='button' class='btn btn-sm bg-purple edit_print' data-kode_delivery='".$row['kode_delivery']."' title='Edit Surat Jalan'><i class='fa fa-file'></i></button>";
			}
			$view = "<a href='" . base_url('delivery/view_delivery/' . $row['kode_delivery']) . "' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";
			// $print2 = "<a href='" . base_url('delivery/print_preview/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-info' title='Preview'><i class='fa fa-search'></i></a>";
					
			$GetSPEC 		= $this->detailDelivery($row['kode_delivery']);

			$explode_ipp 	= $GetSPEC['explode_ipp'];
			$explode_nd 	= $GetSPEC['explode_nd'];
			$explode_spk 	= $GetSPEC['explode_spk'];
			// $explode_ls 	= implode('<br>',$ArrNo_LS);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_delivery'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['nomor_sj'] . "</div>";
			// $nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>" . $explode_nd . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			// $nestedData[]	= "<div align='left'>".$explode_ls."</div>";
			$update_by 	= strtolower($row['updated_by']);
			$NM_LENGKAP = (!empty($GET_USERNAME[$update_by]['nm_lengkap'])) ? $GET_USERNAME[$update_by]['nm_lengkap'] : $update_by;
			$nestedData[]	= "<div align='center'>" . strtoupper($NM_LENGKAP) . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i', strtotime($row['updated_date'])) . "</div>";
			$nestedData[]	= "<div align='left'>
									" . $view . "
									" . $edit . "
									" . $print . "
									" . $print2 . "
									" . $excel . "
									" . $release . "
									" . $edit_print . "
								</div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_delivery($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		// if($no_ipp <> 0){
		// 	$where = " AND a.id_produksi='".$no_ipp."' ";
		// }

		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				delivery_product a
				LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery,
				(SELECT @row:=0) r
		    WHERE 1=1 " . $where . " 
				AND a.release_delivery_date IS NULL
				AND (
					a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.nomor_sj LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.no_drawing LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.sts_product LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.sts LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.kode_delivery
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_delivery'
		);

		$sql .= " ORDER BY a.updated_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	/* CREATE DELIVERY */
	public function create()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if (
			$Arr_Akses['read'] != '1'
		) {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$dataSOPro = $this->db->select('a.id_produksi,b.so_number')
			->from('production_detail a')
			->join('so_number b', "REPLACE(a.id_produksi,'PRO-','BQ-')=b.id_bq", 'left')
			->where(['a.release_to_costing_date !=' => null, 'a.kode_delivery' => null, 'a.lock_delivery_date' => null])
			->group_by('a.id_produksi')
			->get()->result();

		$dataSOCut = $this->db->select('a.id_bq as id_produksi,b.so_number')
			->from('so_cutting_detail a')
			->join('so_number b', 'a.id_bq=b.id_bq', 'left')
			->where(['a.kode_delivery' => null, 'a.lock_delivery_date' => null])
			->group_by('a.id_bq')
			->get()->result();

		$dataSOTanki = $this->db->select('a.id_produksi,b.no_so AS so_number')
			->from('production_detail a')
			->join('warehouse_adjustment b', "a.kode_spk=b.kode_spk", 'left')
			->where(['a.release_to_costing_date !=' => null, 'a.kode_delivery' => null, 'a.lock_delivery_date' => null, 'a.product_code_cut' => 'tanki'])
			->group_by('a.id_produksi')
			->get()->result();

		$dataSO = array_merge($dataSOPro, $dataSOCut, $dataSOTanki);

		$data = array(
			'title'			=> 'Add Delivery',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'dataSO' 		=> $dataSO,
			'akses_menu'	=> $Arr_Akses
		);

		$this->load->view('Delivery/create_delivery', $data);
	}

	public function saveDelivery()
	{
		$data 			= $this->input->post();
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		$category 		= 'loose';

		//pengurutan kode
		$YM				= date('y');
		$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
		$resultPlant	= $this->db->query($srcPlant)->result_array();
		$angkaUrut2		= $resultPlant[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 4);
		$urutan2++;
		$urut2			= sprintf('%04s', $urutan2);
		$kode_delivery	= "DV-" . $YM . $urut2;

		// exit;
		$this->db->trans_start();
		$this->save_header_delivery($kode_delivery);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_header_delivery($kode_delivery = null)
	{
		$post 			= $this->input->post();
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];

		$ArrUpdate = [
			'kode_delivery' => $kode_delivery,
			'nomor_sj' => $post['nomor_sj'],
			'alamat' => $post['alamat'],
			'project' => $post['project'],
			'delivery_date' => $post['delivery_date'],
			'fm_no' => 'FM-C4.1-02',
			'list_ipp' => json_encode($post['list_so']),
			'issue_date' => 'Jan 18th, 2016',
			'created_by' => $username,
			'created_date' => $dateTime,
			'updated_by' => $username,
			'updated_date' => $dateTime
		];

		$CHECK = $this->db->get_where('delivery_product', array('kode_delivery' => $kode_delivery))->result();
		if (!empty($CHECK)) {
			$rev = get_name('delivery_product', 'rev', 'kode_delivery', $kode_delivery);
			$ArrUpdate2 = [
				'updated_by' => $username,
				'updated_date' => $dateTime,
				'rev' => $rev + 1
			];
		}

		$this->db->trans_start();
		if (empty($CHECK)) {
			$this->db->insert('delivery_product', $ArrUpdate);
		} else {
			$this->db->where('kode_delivery', $kode_delivery);
			$this->db->update('delivery_product', $ArrUpdate2);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			history('Create delivery ' . $kode_delivery);
		}
	}

	public function save_detail_delivery()
	{
		$post 			= $this->input->post();
		$kode_delivery	= $post['kode_delivery'];
		$datetime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$explodeCut = explode("-", $post['qr_code']);
		$ArrHistFG = [];

		if ($explodeCut[0] == 'cut') {
			$checkCut  = $this->db->where('id', $explodeCut[1])->where([
				'kode_delivery' 				=> NULL,
				'lock_delivery_date' 			=> NULL
			])->get('so_cutting_detail')->row();

			if ($checkCut) {
				$this->db->query("UPDATE 
										so_cutting_detail
									SET 
										kode_delivery='$kode_delivery',
										delivery_by='$username',
										delivery_date='$datetime'
									WHERE 
									id = '$explodeCut[1]'
										AND kode_delivery IS NULL
										AND lock_delivery_date IS NULL");

				$ArrHistFG[0]['tipe_product'] = 'cutting';
				$ArrHistFG[0]['id_product'] = $explodeCut[1];
				$ArrHistFG[0]['id_milik'] = $checkCut->id_milik;
				$ArrHistFG[0]['tipe'] = 'out';
				$ArrHistFG[0]['kode'] = $kode_delivery;
				$ArrHistFG[0]['tanggal'] = date('Y-m-d');
				$ArrHistFG[0]['keterangan'] = 'delivery pipe cutting';
				$ArrHistFG[0]['hist_by'] = $username;
				$ArrHistFG[0]['hist_date'] = $datetime;

			}
		} else {
			$check  = $this->db->where_in('id_produksi', $post['list_so'])->where([
				'id' 							=> $post['qr_code'],
				'release_to_costing_date !=' 	=> NULL,
				'kode_delivery' 				=> NULL,
				'spool_induk' 					=> NULL,
				'lock_delivery_date' 			=> NULL
			])->get('production_detail')->row();

			if ($check) {

				$ArrHistFG[0]['tipe_product'] = 'pipe fitting';
				$ArrHistFG[0]['id_product'] = $post['qr_code'];
				$ArrHistFG[0]['id_milik'] = $check->id_milik;
				$ArrHistFG[0]['tipe'] = 'out';
				$ArrHistFG[0]['kode'] = $kode_delivery;
				$ArrHistFG[0]['tanggal'] = date('Y-m-d');
				$ArrHistFG[0]['keterangan'] = 'delivery pipe fitting';
				$ArrHistFG[0]['hist_by'] = $username;
				$ArrHistFG[0]['hist_date'] = $datetime;
				// exit;
				$no_pro = $check->id_produksi;
				$this->db->trans_start();
				$this->db->update(
					'production_detail',
					[
						'kode_delivery' 	=> $kode_delivery,
						'delivery_by' 		=> $username,
						'delivery_date' 	=> $datetime,
					],
					[
						'id' 							=> $check->id,
						'id_produksi' 					=> $no_pro,
						'release_to_costing_date !=' 	=> NULL,
						'kode_delivery' 				=> NULL,
						'spool_induk' 					=> NULL,
						'lock_delivery_date' 			=> NULL
					]
				);
			} else {
				$check2  = $this->db->where_in('id_produksi', $post['list_so'])->where([
					'spool_induk' 					=> $post['qr_code'],
					// 'release_to_costing_date !=' 	=> NULL,
					'kode_delivery' 				=> NULL,
					'lock_delivery_date' 			=> NULL
				])->get('production_detail')->row();

				if ($check2) {
					$no_pro = $check2->id_produksi;
					$this->db->update(
						'production_detail',
						[
							'kode_delivery' 	=> $kode_delivery,
							'delivery_by' 		=> $username,
							'delivery_date' 	=> $datetime,
						],
						[
							'spool_induk' 					=> $post['qr_code'],
							'id_produksi' 					=> $no_pro,
							// 'release_to_costing_date !=' 	=> NULL,
							'kode_delivery' 				=> NULL,
							'lock_delivery_date' 			=> NULL
						]
					);
				} else {
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=> 'Process Failed. QR Code not listed or Diference SO ...',
						'status'	=> 2,
						// 'kode_delivery'	=> $kode_delivery
					);
				}

				$ArrSO = [];
				foreach ($post['list_so'] as $so) {
					$ArrSO[] = str_replace("PRO", "BQ", $so);
				}

				$check3  = $this->db->where_in('id_bq', $ArrSO)->where([
					'spool_induk' 					=> $post['qr_code'],
					'kode_delivery' 				=> NULL,
					'lock_delivery_date' 			=> NULL
				])->get('so_cutting_detail')->row();

				if ($check3) {
					$no_pro = $check3->id_bq;
					$this->db->query("UPDATE 
										so_cutting_detail
									SET 
										kode_delivery='$kode_delivery',
										delivery_by='$username',
										delivery_date='$datetime'
									WHERE 
										spool_induk='" . $post['qr_code'] . "'
										AND id_bq= '" . str_replace('PRO-', 'BQ-', $no_pro) . "'
										AND kode_spool= '" . $check3->kode_spool . "'
										AND kode_delivery IS NULL
										AND lock_delivery_date IS NULL");
				}
			}
		}

		//DEADSTOK
		$this->db->query("	UPDATE 
								deadstok
							SET 
								kode_delivery='$kode_delivery',
								delivery_by='$username',
								delivery_date='$datetime'
							WHERE 
								spool_induk='" . $post['qr_code'] . "'
								AND kode_delivery IS NULL
								AND lock_delivery_date IS NULL");
		if(!empty($post['list_so'])){
			$this->insert_delivery($kode_delivery, $post['list_so']);
		}
		if (!empty($ArrHistFG)) {
			$this->db->insert_batch('history_product_fg', $ArrHistFG);
		}
		$this->insert_detail_delivery($kode_delivery, $datetime);
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery ' . $kode_delivery);
		}
		$this->db->trans_complete();
		echo json_encode($Arr_Kembali);
	}

	public function load_so()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if (
			$Arr_Akses['read'] != '1'
		) {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$dataSO = $this->db->select('a.id_produksi,a.qty, b.so_number,b.so_date')
			->from('production_detail a')
			->join('so_number b', "REPLACE(a.id_produksi,'PRO-','BQ-')=b.id_bq", 'left')
			->where(['a.release_to_costing_date !=' => null, 'a.kode_delivery' => null, 'a.lock_delivery_date' => null])
			->group_by('a.id_produksi')
			->get()->result();

		$data = [
			'dataSO' 		=> $dataSO,
			'akses_menu'	=> $Arr_Akses
		];

		$this->load->view('Delivery/load_so', $data);
	}

	/* loadDataSS */
	public function loadDataSS($kode_delivery)
	{
		$result_1 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts_product' => NULL))->result_array();
		$result_2 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts' => 'cut'))->result_array();
		$result 	= array_merge($result_1,$result_2);
		$result3 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts_product' => 'so material'))->result_array();
		$result4 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts_product' => 'so material', 'product_code' => 'field joint'))->result_array();
		$result2 	= $this->db->order_by('id', 'asc')->group_by('spool_induk')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk !=' => NULL))->result_array();


		$result_print1 = $this->db
							->group_by('a.id_milik')
							->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
							->join('production_detail b','a.id_pro=b.id','left')
							->where('(a.berat > 0 OR a.berat IS NULL)')
							->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'a.sts_product' => NULL, 'a.sts !=' => 'loose_dead'))->result_array();

		$result_print3 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik')->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product, "" AS product_tanki')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts' => 'loose_dead'))->result_array();
		$result_print2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product, "" AS product_tanki')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$result_print = array_merge($result_print1, $result_print2, $result_print3);
		$data = array(
			'result'			=> $result,
			'result2'			=> $result2,
			'result3'			=> $result3,
			'result4'			=> $result4,
			'result_print'		=> $result_print,
			'kode_delivery'		=> $kode_delivery,
			'tanki_model' 		=> $this->tanki_model
		);
		$this->load->view('Delivery/load-data2', $data);
	}


	public function updateDelivery()
	{
		$post 			= $this->input->post();
		$kode_delivery  = $post['kode_delivery'];
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$rev 			= get_name('delivery_product', 'rev', 'kode_delivery', $kode_delivery);

		$ArrUpdate = [
			'nomor_sj' 			=> $post['nomor_sj'],
			'alamat' 			=> $post['alamat'],
			'project' 			=> $post['project'],
			'delivery_date' 	=> $post['delivery_date'],
			'fm_no' 			=> 'FM-C4.1-02',
			'list_ipp' 			=> (isset($post['list_so']) && $post['list_so']) ? json_encode($post['list_so']) : null,
			'issue_date' 		=> 'Jan 18th, 2016',
			'updated_by' 		=> $username,
			'updated_date' 		=> $dateTime,
			'rev' 				=> $rev + 1
		];

		$this->db->trans_start();
		// $this->db->update(
		// 	'production_detail',
		// 	[
		// 		'kode_delivery' 	=> null,
		// 		'delivery_by' 		=> null,
		// 		'delivery_date' 	=> null,
		// 	],
		// 	[
		// 		'kode_delivery' => $kode_delivery
		// 	]
		// );
		// if (isset($post['list_so']) && $post['list_so'] != null) {
		// 	$this->db->where_not_in('id_produksi', $post['list_so']);
		// 	$this->db->where(['kode_delivery' => $kode_delivery]);
		// 	$this->db->delete('delivery_product_detail');
		// }

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product', $ArrUpdate);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}
	/* ========================== */

	//DELIVERY LOOSE
	public function add()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data_list = $this->db->query(" SELECT
                                            a.id_produksi,
											b.so_number
                                        FROM
                                            production_detail a 
											LEFT JOIN so_number b ON REPLACE(a.id_produksi,'PRO-','BQ-')=b.id_bq
                                        WHERE
											a.release_to_costing_date IS NOT NULL
											AND a.kode_delivery IS NULL
											AND a.lock_delivery_date IS NULL
                                        GROUP BY
                                            a.id_produksi 
                                        ORDER BY
                                            a.id_produksi ASC")->result_array();
		$data = array(
			'title'			=> 'Add Delivery',
			'action'		=> 'index',
			'list_ipp'		=> $data_list,
			// 'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add', $data);
	}

	public function server_side_request()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request(
			$requestData['no_ipp'],
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
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$sisa_spk = $this->db->select('COUNT(id) AS sisa_spk')->get_where('production_detail', array('kode_delivery' => NULL, 'spool_induk' => NULL, 'release_to_costing_date <>' => NULL, 'id_milik' => $row['id_milik'], 'id_produksi' => $row['id_produksi']))->result();

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['id_milik'], 'category' => 'loose'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';
			$QTY = '';
			if ($checked == 'checked') {
				$QTY = $CHECK[0]->qty;
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['id_category']) . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['product_code'] . "</div>";
			$nestedData[]	= "<div align='left'>" . spec_bq2($row['id_milik']) . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['id_product'] . "</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green sisa_spk'>" . $sisa_spk[0]->sisa_spk . "</span></div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' id='spk_" . $row['id_milik'] . "' name='spk_" . $row['id_milik'] . "' class='form-control text-center qty_spk input-sm maskMoney changeTemp' data-id_milik='" . $row['id_milik'] . "' value='$QTY' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' style='width:60px;'><script>$('.maskMoney').maskMoney();</script>
									<input type='hidden' id='ipp_" . $row['id_milik'] . "' name='ipp_" . $row['id_milik'] . "' value='" . $row['no_ipp'] . "'>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal changeTemp' $checked data-nomor='$nomor' data-id_milik='" . $row['id_milik'] . "' value='" . $row['id_milik'] . "' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_request($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$where = " AND a.id_produksi='" . $no_ipp . "' ";
		//(SELECT COUNT(b.id) FROM production_detail b WHERE b.kode_spk IS NULL AND b.id_milik=a.id_milik AND b.id_produksi=a.id_produksi)
		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					b.spk1_cost,
					b.spk2_cost,
					b.no_ipp,
					c.diameter_1,
					c.length,
					c.thickness,
					b.qty AS tot_qty
				FROM
					production_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq,
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y' 
					AND (a.sts_cutting != 'Y' OR a.sts_cutting IS NULL)
					AND b.spk1_cost = 'Y' 
					AND b.spk2_cost = 'Y' 
					AND a.kode_spk IS NOT NULL 
					AND a.release_to_costing_date IS NOT NULL
					AND a.spool_induk IS NULL
					AND a.kode_delivery IS NULL
					" . $where . "
					AND (
						a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
				GROUP BY
					a.id_milik
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'product',
			3 => 'no_spk',
			4 => 'id_milik',
			5 => 'id_product'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_delivery()
	{
		$data 			= $this->input->post();
		$check 			= $data['check'];
		$kode_delivery 	= $data['kode_delivery'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		$category 		= 'loose';
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', $category)->get()->result_array();
		foreach ($get_detail_produksi as $key => $value) {
			$QTY 	= (int)$value['qty'];
			if ($QTY > 0) {
				$no_ipp = $value['no_ipp'];
				$no_pro = "PRO-" . $no_ipp;

				$qUpdate 	= $this->db->query("UPDATE 
													production_detail
												SET 
													kode_delivery='$kode_delivery',
													delivery_by='$username',
													delivery_date='$datetime'
												WHERE 
													id_milik='" . $value['id_uniq'] . "'
													AND id_produksi= '" . $no_pro . "'
													AND release_to_costing_date IS NOT NULL
													AND kode_delivery IS NULL
													AND spool_induk IS NULL
													AND lock_delivery_date IS NULL
												ORDER BY 
													id ASC 
												LIMIT $QTY");
			}
		}


		// exit;
		$this->db->trans_start();
		$this->insert_delivery($kode_delivery);
		$this->insert_detail_delivery($kode_delivery, $datetime);

		$this->db->where('created_by', $username);
		$this->db->where('category', $category);
		$this->db->delete('delivery_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	//DELIVERY CUTTING
	public function add_cutting()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data_list = $this->db->query(" SELECT
                                            a.id_produksi,
											b.so_number
                                        FROM
                                            production_detail a 
											LEFT JOIN so_number b ON REPLACE(a.id_produksi,'PRO-','BQ-')=b.id_bq
                                        WHERE
											a.release_to_costing_date IS NOT NULL
											AND a.kode_delivery IS NULL
											AND a.lock_delivery_date IS NULL
                                        GROUP BY
                                            a.id_produksi 
                                        ORDER BY
                                            a.id_produksi ASC")->result_array();
		// $data_spool = $this->db->query(" SELECT
		// 									a.kode_delivery
		// 								FROM
		// 									delivery_group a 
		// 								WHERE
		// 									a.kode_delivery IS NOT NULL
		// 									AND a.lock_delivery_date IS NOT NULL
		// 								GROUP BY
		// 									a.kode_delivery 
		// 								ORDER BY
		// 									a.kode_delivery ASC")->result_array();
		$data = array(
			'title'			=> 'Add Delivery',
			'action'		=> 'index',
			'list_ipp'		=> $data_list,
			// 'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add_cutting', $data);
	}

	public function server_side_request_cutting()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request_cutting(
			$requestData['no_ipp'],
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
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$IMPLODE = explode('.', $row['product_code']);
			$product_code = $IMPLODE[0] . '.' . $row['product_ke'] . "." . $row['cutting_ke'];

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['id_cutting'], 'category' => 'cutting'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . str_replace('PRO-', '', $row['id_produksi']) . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['id_category']) . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $product_code . "</div>";
			$nestedData[]	= "<div align='left'>" . number_format($row['diameter_1']) . " x " . number_format($row['length']) . " x " . number_format($row['thickness'], 2) . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['id_product'] . "</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' $checked data-nomor='$nomor' value='" . $row['id_cutting'] . "' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_request_cutting($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$where = " AND a.id_produksi='" . $no_ipp . "' ";

		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					c.diameter_1,
					c.thickness,
					f.length_split AS length, 
					f.cutting_ke,
					f.id AS id_cutting
				FROM
					production_detail a
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke
					LEFT JOIN so_cutting_detail f ON d.id = f.id_header,
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.sts_cutting = 'Y'
					AND a.upload_real = 'Y'
					AND a.upload_real2 = 'Y'
					AND a.kode_spk IS NOT NULL 
					AND a.release_to_costing_date IS NOT NULL
					AND a.spool_induk IS NULL
					AND a.kode_delivery IS NULL
					AND (f.lock_delivery_date IS NULL AND f.spool_date IS NULL)
					AND d.id IS NOT NULL 
					AND c.id_category='pipe' 
					AND d.app = 'Y'
					" . $where . "
					AND (
						a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi',
			2 => 'product',
			3 => 'no_spk',
			4 => 'id_milik',
			5 => 'id_product'
		);

		$sql .= " ORDER BY a.release_to_costing_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_delivery_cutting()
	{
		$data 		= $this->input->post();


		$kode_delivery 	= $data['kode_delivery'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		$category		= 'cutting';

		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', $category)->get()->result_array();
		$ArrUpdate = [];
		$check = [];
		foreach ($get_detail_produksi as $key => $value) {
			$ArrUpdate[$key]['id'] = $value['id_uniq'];
			$ArrUpdate[$key]['kode_delivery'] = $kode_delivery;
			$ArrUpdate[$key]['delivery_by'] = $username;
			$ArrUpdate[$key]['delivery_date'] = $datetime;
			$check[] = $value['id_uniq'];
		}



		//INSERT DETAIL
		$getInsert = $this->db
			->select('*')
			->from('delivery_group')
			->where_in('id', $check)
			->where('sts', 'cut')
			->get()
			->result_array();
		$ArrInsert = [];
		foreach ($getInsert as $key => $value) {
			$IMPLODE = explode('.', $value['product_code']);
			$product_code = $IMPLODE[0] . '.' . $value['product_ke'];
			$Cutting_ke = '';
			if ($value['sts'] == 'cut') {
				$Cutting_ke = "." . $value['cutting_ke'];
				$product_code = $IMPLODE[0] . '.' . $value['product_ke'] . $Cutting_ke;
			}

			$ArrInsert[$key]['kode_delivery'] = $kode_delivery;
			$ArrInsert[$key]['id_uniq'] = $value['id'];
			$ArrInsert[$key]['id_pro'] = $value['id_pro'];
			$ArrInsert[$key]['product'] = $value['id_category'];
			$ArrInsert[$key]['id_milik'] = $value['id_milik'];
			$ArrInsert[$key]['id_produksi'] = $value['id_produksi'];
			$ArrInsert[$key]['spool_induk'] = $value['spool_induk'];
			$ArrInsert[$key]['kode_spool'] = $value['kode_spool'];
			$ArrInsert[$key]['product_code'] = $product_code;
			$ArrInsert[$key]['no_spk'] = $value['no_spk'];
			$ArrInsert[$key]['product_ke'] = $value['product_ke'];
			$ArrInsert[$key]['kode_spk'] = $value['kode_spk'];
			$ArrInsert[$key]['length'] = $value['length'];
			$ArrInsert[$key]['cutting_ke'] = $value['cutting_ke'];
			$ArrInsert[$key]['no_drawing'] = $value['no_drawing'];
			$ArrInsert[$key]['upload_date'] = $value['upload_date'];
			$ArrInsert[$key]['sts'] = $value['sts'];
			// agus
			$nilai_cogs=0;
			$result	= $this->db->query("select * from so_cutting_detail where kode_delivery='".$kode_delivery."' and id='".$value['id']."'")->result_array();
			if(!empty($result)){
				foreach ($result as $datascd) {
					$id_header=$datascd['id_header'];
					$id_milik=$value['id_milik'];
					$dtfg	= $this->db->query("select b.finish_good from so_cutting_header a join production_detail b on a.id_milik=b.id_milik and a.qty_ke=b.product_ke where a.id='".$id_header."' and b.id_milik='".$id_milik."' limit 1")->row();
					$hargattlfg=$dtfg->finish_good;
					$length=$datascd['length'];
					$length_split=$datascd['length_split'];
					$nilai_cogs=round(($dtfg->finish_good*$length_split/$length));
					$this->db->query("update so_cutting_detail set finish_good='".$nilai_cogs."' where id ='".$datascd['id']."'");
				}
			}
			$ArrInsert[$key]['nilai_cogs'] = $nilai_cogs;
			// end agus

			$ArrInsert[$key]['updated_by'] = $username;
			$ArrInsert[$key]['updated_date'] = $datetime;
		}

		// print_r($ArrInsert);
		// exit;
		// exit;

		$this->db->trans_start();
		$this->db->update_batch('so_cutting_detail', $ArrUpdate, 'id');
		if (!empty($ArrInsert)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert);
		}
		$this->insert_delivery($kode_delivery);

		$this->db->where('created_by', $username);
		$this->db->where('category', $category);
		$this->db->delete('delivery_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	//DELIVERY SPOOL
	public function add_spool()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data_list = $this->db->query(" SELECT
                                            a.id_produksi,
											b.so_number
                                        FROM
                                            production_detail a 
											LEFT JOIN so_number b ON REPLACE(a.id_produksi,'PRO-','BQ-')=b.id_bq
                                        WHERE
											a.release_to_costing_date IS NOT NULL
											AND a.kode_delivery IS NULL
											AND a.lock_delivery_date IS NULL
                                        GROUP BY
                                            a.id_produksi 
                                        ORDER BY
                                            a.id_produksi ASC")->result_array();
		// $data_spool = $this->db->query(" SELECT
		// 									a.kode_delivery
		// 								FROM
		// 									delivery_group a 
		// 								WHERE
		// 									a.kode_delivery IS NOT NULL
		// 									AND a.lock_delivery_date IS NOT NULL
		// 								GROUP BY
		// 									a.kode_delivery 
		// 								ORDER BY
		// 									a.kode_delivery ASC")->result_array();
		$data = array(
			'title'			=> 'Add Delivery Spool',
			'action'		=> 'index',
			'list_ipp'		=> $data_list,
			// 'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add_spool', $data);
	}

	public function server_side_request_spool()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request_spool(
			$requestData['no_ipp'],
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
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$get_split_ipp = $this->db->select('id_produksi, id_milik, kode_spool, product_code, product_ke, id_category, no_spk, length, sts, cutting_ke, COUNT(id) AS qty')->group_by('id_milik')->order_by('id', 'asc')->get_where('spool_group_release', array('spool_induk' => $row['spool_induk'], 'kode_spool' => $row['kode_spool']))->result_array();
			$ArrNo_Spool = [];
			$ArrNo_IPP = [];
			$ArrNo_SPK = [];
			$ArrNo_ID = [];
			foreach ($get_split_ipp as $key => $value) {
				$key++;

				$LENGTH = '';
				if ($value['id_category'] == 'pipe') {
					$no_spk_list = $this->db->select('length')->get_where('so_detail_header', array('id' => $value['id_milik']))->result();
					$LENGTH = ($value['sts'] == 'cut') ? number_format($value['length']) : number_format($no_spk_list[0]->length);
				}

				$ArrNo_IPP[] = $key . '. ' . strtoupper($value['id_category'] . ' ' . $LENGTH) . ', (' . $value['qty'] . ')';
				$ArrNo_Spool[] = $key . '. ' . strtoupper(spec_bq3($value['id_milik']));

				$CUTTING_KE = (!empty($value['cutting_ke'])) ? '.' . $value['cutting_ke'] : '';

				$IMPLODE = explode('.', $value['product_code']);
				$ArrNo_SPK[] = $key . '. ' . $value['no_spk'];
				$ArrNo_ID[] = $key . '. ' . $IMPLODE[0] . '.' . $value['product_ke'] . $CUTTING_KE;
			}
			// print_r($ArrGroup); exit;
			$explode_spo = implode('<br>', $ArrNo_Spool);
			$explode_ipp = implode('<br>', $ArrNo_IPP);
			$explode_spk = implode('<br>', $ArrNo_SPK);
			$explode_id = implode('<br>', $ArrNo_ID);

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['spool_induk'] . "&" . $row['kode_spool'], 'category' => 'spool'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['spool_induk'] . " - " . $row['kode_spool'] . "<br>" . $row['no_drawing'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_ipp . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spo . "</div>";
			// $nestedData[]	= "<div align='left'>".$explode_id."</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' $checked data-nomor='$nomor' value='" . $row['spool_induk'] . "&" . $row['kode_spool'] . "' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_request_spool($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$where = " AND a.id_produksi='" . $no_ipp . "' ";
		//(SELECT COUNT(b.id) FROM production_detail b WHERE b.kode_spk IS NULL AND b.id_milik=a.id_milik AND b.id_produksi=a.id_produksi)
		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*
				FROM
					spool_group_release a,
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.release_spool_date IS NOT NULL
					AND a.kode_delivery IS NULL
					" . $where . "
					AND (
						a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
				GROUP BY
					a.spool_induk,
					a.kode_spool
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_spool'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_delivery_spool()
	{
		$data 		= $this->input->post();

		$check 			= $data['check'];
		$kode_delivery 	= $data['kode_delivery'];
		$no_pro 		= $data['no_ipp'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		$category		= 'spool';
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		// print_r($check);
		// exit;
		$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', $category)->get()->result_array();
		foreach ($get_detail_produksi as $valx => $value) {
			$EXPLODE = explode('&', $value['id_uniq']);

			$kode_induk = $EXPLODE[0];
			$kode_spool = $EXPLODE[1];

			$qUpdate 	= $this->db->query("UPDATE 
													production_detail
												SET 
													kode_delivery='$kode_delivery',
													delivery_by='$username',
													delivery_date='$datetime'
												WHERE 
													spool_induk='" . $kode_induk . "'
													AND id_produksi= '" . $no_pro . "'
													AND kode_spool= '" . $kode_spool . "'
													AND release_to_costing_date IS NOT NULL
													AND kode_delivery IS NULL
													AND lock_delivery_date IS NULL");

			$qUpdate 	= $this->db->query("UPDATE 
													so_cutting_detail
												SET 
													kode_delivery='$kode_delivery',
													delivery_by='$username',
													delivery_date='$datetime'
												WHERE 
													spool_induk='" . $kode_induk . "'
													AND id_bq= '" . str_replace('PRO-', 'BQ-', $no_pro) . "'
													AND kode_spool= '" . $kode_spool . "'
													AND kode_delivery IS NULL
													AND lock_delivery_date IS NULL");
		}
		// exit;
		$this->insert_delivery($kode_delivery);
		$this->insert_detail_delivery($kode_delivery, $datetime);

		$this->db->where('created_by', $username);
		$this->db->where('category', $category);
		$this->db->delete('delivery_temp');

		history('Create data delivery spool ' . $kode_delivery);
		$Arr_Kembali	= array(
			'status' => 1,
			'kode_delivery'	=> $kode_delivery
		);

		echo json_encode($Arr_Kembali);
	}


	//GENERAL
	public function getDetaildelivery()
	{
		$username 	= $this->session->userdata['ORI_User']['username'];
		$no_ipp 		= $this->input->post('no_ipp');
		$category 		= $this->input->post('category');
		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";
		$restSup = $this->db->query(" SELECT
											a.kode_delivery
										FROM
											delivery_product a 
											LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
										WHERE
											a.lock_delivery_date IS NULL " . $where2 . "
										GROUP BY a.kode_delivery
										ORDER BY
											a.kode_delivery ASC")->result_array();
		// $restSup	= $this->db->select('kode_delivery')->get_where('delivery_product',array('lock_delivery_date'=>NULL))->result_array();

		$option	= "<option value='0'>Buat Baru Delivery</option>";
		if (!empty($restSup)) {
			foreach ($restSup as $val => $valx) {
				$option .= "<option value='" . $valx['kode_delivery'] . "'>Tambahkan ke Delivery : " . strtoupper($valx['kode_delivery']) . "</option>";
			}
		}

		$this->db->where('created_by', $username);
		$this->db->where('category', $category);
		$this->db->delete('delivery_temp');

		$ArrJson	= array(
			'option' => $option,
			'no_ipp' => $no_ipp
		);
		echo json_encode($ArrJson);
	}

	public function edit_delivery($kode_delivery)
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['update'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$header 	= $this->db->get_where('delivery_product', array('kode_delivery' => $kode_delivery))->result();
		$dataSOPro = $this->db->select('a.id_produksi,b.so_number')
			->from('production_detail a')
			->join('so_number b', "REPLACE(a.id_produksi,'PRO-','BQ-')=b.id_bq", 'left')
			->where(['a.release_to_costing_date !=' => null])
			// ['a.release_to_costing_date !=' => null, 'a.kode_delivery' => null, 'a.lock_delivery_date' => null]
			->group_by('a.id_produksi')
			->get()->result();

		$dataSOCut = $this->db->select("REPLACE(a.id_bq,'BQ-','PRO-') as id_produksi,b.so_number")
			->from('so_cutting_detail a')
			->join('so_number b', 'a.id_bq=b.id_bq', 'left')
			->where(['a.kode_delivery' => null, 'a.lock_delivery_date' => null])
			->group_by('a.id_bq')
			->get()->result();

		$dataSOMat = $this->db->select("REPLACE(a.no_ipp,'BQ-','PRO-') as id_produksi,b.so_number")
			->from('warehouse_adjustment_detail a')
			->join('so_number b', 'a.no_ipp=b.id_bq', 'left')
			->where(['a.lot_number !=' => null])
			->group_by('a.no_ipp')
			->get()->result();

		$dataSOTanki = $this->db->select('a.id_produksi,b.no_so AS so_number')
			->from('production_detail a')
			->join('warehouse_adjustment b', "a.kode_spk=b.kode_spk", 'left')
			->where(['a.release_to_costing_date !=' => null, 'a.product_code_cut' => 'tanki'])
			->group_by('a.id_produksi')
			->get()->result();

		// $dataSO = ($dataSOPro);
		$dataSO = array_merge($dataSOPro, $dataSOMat, $dataSOTanki);
		// $dataSO = (array_replace($dataSOPro, $dataSOCut, $dataSOMat));
		// $dataSO = array_unique(array_merge($dataSOPro, $dataSOCut, $dataSOMat));

		$result_1 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts_product' => NULL, 'sts !=' => 'loose_dead'))->result_array();
		$result_2 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts' => 'cut'))->result_array();
		$result 	= array_merge($result_1,$result_2);
		$result3 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts_product' => 'so material'))->result_array();
		$result4 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk' => NULL, 'sts_product' => 'field joint'))->result_array();
		$result2 	= $this->db->order_by('id', 'asc')->group_by('spool_induk')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk !=' => NULL))->result_array();
		$result5 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('sts_product' => 'deadstok','kode_delivery' => $kode_delivery))->result_array();
		$result6 	= $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('sts_product' => 'aksesoris','kode_delivery' => $kode_delivery))->result_array();

		$result_print1 = $this->db
							->group_by('a.id_milik')
							->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
							->join('production_detail b','a.id_pro=b.id','left')
							->where('(a.berat > 0 OR a.berat IS NULL)')
							->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'a.sts_product' => NULL, 'a.sts !=' => 'loose_dead'))->result_array();

		$result_print3 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik')->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product, "" AS product_tanki')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts' => 'loose_dead'))->result_array();
		$result_print2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product, "" AS product_tanki')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$result_print = array_merge($result_print1, $result_print2, $result_print3);
		$data = array(
			'title'				=> 'Edit Delivery',
			'action'			=> 'index',
			'GET_DESC_DEAL'		=> get_descDealSO(),
			'GET_ID_MILIK'		=> get_idMilikSODeal(),
			'row_group'			=> $data_Group,
			'header'			=> $header,
			'result'			=> $result,
			'result2'			=> $result2,
			'result3'			=> $result3,
			'result4'			=> $result4,
			'result5'			=> $result5,
			'result6'			=> $result6,
			'dataSO' 			=> $dataSO,
			'result_print'		=> $result_print,
			'kode_delivery'		=> $kode_delivery,
			'akses_menu'		=> $Arr_Akses,
			'tanki_model' 		=> $this->tanki_model
		);
		$this->load->view('Delivery/edit_delivery', $data);
	}

	public function view_delivery($kode_delivery)
	{

		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$result = $this->db
						->select('a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
						->order_by('b.id', 'asc')
						->where('(a.berat > 0 OR a.berat IS NULL)')
						->join('production_detail b','a.id_pro=b.id','left')
						->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery))->result_array();
		$data = array(
			'title'			=> 'Detail Delivery ' . $kode_delivery,
			'action'		=> 'index',
			'result'		=> $result,
			'kode_delivery'		=> $kode_delivery,
			'tanki_model' 		=> $this->tanki_model
		);
		$this->load->view('Delivery/view_delivery', $data);
	}

	public function delete_delivery()
	{
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		$kode_delivery = $data['kode_delivery'];

		$ArrHistFG = [];

		if (!empty($data['check'])) {
			$check = $data['check'];
		}

		if (!empty($data['check_cut'])) {
			$check_cut = $data['check_cut'];
		}

		if (!empty($data['check2'])) {
			$check2 = $data['check2'];
		}

		if (!empty($data['check3'])) {
			$check3 = $data['check3'];
		}

		if (!empty($data['check4'])) {
			$check4 = $data['check4'];
		}

		if (!empty($data['check5'])) {
			$check5 = $data['check5'];
		}

		if (!empty($data['check6'])) {
			$check6 = $data['check6'];
		}

		$ArrDelete = [];
		$ArrUpdate = [];
		$kode_id = [];
		if (!empty($data['check'])) {
			foreach ($check as $value) {
				$EXPLODE = explode('&', $value);

				$id_uniq = $EXPLODE[0];
				$ID = $EXPLODE[1];

				$ArrUpdate[$value]['id'] = $id_uniq;
				$ArrUpdate[$value]['kode_delivery'] = NULL;
				$ArrUpdate[$value]['delivery_by'] = NULL;
				$ArrUpdate[$value]['delivery_date'] = NULL;

				$getDetail = $this->db->get_where('production_detail',array('id'=>$id_uniq))->result();

				$ArrHistFG[$value.'cut']['tipe_product'] = 'pipe fitting';
				$ArrHistFG[$value.'cut']['id_product'] = $id_uniq;
				$ArrHistFG[$value.'cut']['id_milik'] = $getDetail[0]->id_milik;
				$ArrHistFG[$value.'cut']['tipe'] = 'in';
				$ArrHistFG[$value.'cut']['kode'] = $kode_delivery;
				$ArrHistFG[$value.'cut']['tanggal'] = date('Y-m-d');
				$ArrHistFG[$value.'cut']['keterangan'] = 'delete pipe fitting in delivery';
				$ArrHistFG[$value.'cut']['hist_by'] = $username;
				$ArrHistFG[$value.'cut']['hist_date'] = $datetime;

				$kode_id = $value;

				$ArrDelete[] = $ID;
			}
		}

		//SO MATERIAL
		$ArrUpdateMAT = [];
		if (!empty($data['check3'])) {
			foreach ($check3 as $value) {
				$EXPLODE = explode('&', $value);

				$id_uniq = $EXPLODE[0];
				$ID = $EXPLODE[1];

				$ArrUpdateMAT[$value]['id'] = $id_uniq;
				$ArrUpdateMAT[$value]['lot_number'] = NULL;
				$ArrUpdateMAT[$value]['proccess_by'] = NULL;
				$ArrUpdateMAT[$value]['proccess_date'] = NULL;

				$ArrDelete[] = $ID;
			}
		}

		//SO MATERIAL JOINT
		$ArrUpdateJoint = [];
		$ArrDeleteFieldJoint = [];
		if (!empty($data['check4'])) {
			foreach ($check4 as $value) {
				$EXPLODE = explode('&', $value);

				$id_uniq = $EXPLODE[0];
				$ID = $EXPLODE[1];

				$ArrUpdateJoint[$value]['id'] = $id_uniq;
				$ArrUpdateJoint[$value]['kode_delivery'] = NULL;
				$ArrUpdateJoint[$value]['delivery_by'] = NULL;
				$ArrUpdateJoint[$value]['delivery_date'] = NULL;

				$ArrDeleteFieldJoint[] = $ID;
			}
		}

		// echo "<pre>";
		// print_r($ArrUpdateJoint);
		// print_r($ArrDeleteFieldJoint);
		// exit;

		$ArrUpdateCut = [];
		if (!empty($data['check_cut'])) {
			foreach ($check_cut as $value) {
				$EXPLODE = explode('&', $value);

				$id_uniq = $EXPLODE[0];
				$ID = $EXPLODE[1];

				$ArrUpdateCut[$value]['id'] = $id_uniq;
				$ArrUpdateCut[$value]['kode_delivery'] = NULL;
				$ArrUpdateCut[$value]['delivery_by'] = NULL;
				$ArrUpdateCut[$value]['delivery_date'] = NULL;

				$getCutting = $this->db->get_where('so_cutting_detail',array('id'=>$id_uniq))->result();

				$ArrHistFG[$value.'cut']['tipe_product'] = 'cutting';
				$ArrHistFG[$value.'cut']['id_product'] = $id_uniq;
				$ArrHistFG[$value.'cut']['id_milik'] = $getCutting[0]->id_milik;
				$ArrHistFG[$value.'cut']['tipe'] = 'in';
				$ArrHistFG[$value.'cut']['kode'] = $kode_delivery;
				$ArrHistFG[$value.'cut']['tanggal'] = date('Y-m-d');
				$ArrHistFG[$value.'cut']['keterangan'] = 'delete pipe cutting in delivery';
				$ArrHistFG[$value.'cut']['hist_by'] = $username;
				$ArrHistFG[$value.'cut']['hist_date'] = $datetime;

				$ArrDelete[] = $ID;
			}
		}

		//spool
		$ArrUpdate2 = [];
		$ArrUpdate3 = [];
		$kode_id2 = [];
		if (!empty($data['check2'])) {
			$key2 = 0;
			foreach ($check2 as $value2) {
				$key2++;
				$EXPLODE = explode('&', $value2);

				$kode_induk = $EXPLODE[0];
				$kode_spool = $EXPLODE[1];

				$data_spool = $this->db->get_where('delivery_product_detail', array('spool_induk' => $kode_induk, 'kode_spool' => $kode_spool))->result_array();

				foreach ($data_spool as $key => $valX) {
					$key++;
					if ($valX['sts'] == 'loose') {
						$ArrUpdate2[$key2 . $key]['id'] = $valX['id_uniq'];
						$ArrUpdate2[$key2 . $key]['kode_delivery'] = NULL;
						$ArrUpdate2[$key2 . $key]['delivery_by'] = NULL;
						$ArrUpdate2[$key2 . $key]['delivery_date'] = NULL;
					}

					if ($valX['sts'] == 'cut') {
						$ArrUpdate3[$key2 . $key]['id'] = $valX['id_uniq'];
						$ArrUpdate3[$key2 . $key]['kode_delivery'] = NULL;
						$ArrUpdate3[$key2 . $key]['delivery_by'] = NULL;
						$ArrUpdate3[$key2 . $key]['delivery_date'] = NULL;
					}

					$ArrDelete[] = $valX['id'];
				}
			}
		}

		// Update Rev
		$rev = get_name('delivery_product', 'rev', 'kode_delivery', $kode_delivery);
		$ArrUpdateRev = array(
			'rev' => $rev + 1
		);
		// print_r($ArrDelete);
		// exit;

		//SO DEADSTOK
		$ArrDeadstok = [];
		$ArrDeadstokModif = [];
		if (!empty($data['check5'])) {
			foreach ($check5 as $value) {
				$EXPLODE = explode('&', $value);

				$id_uniq = $EXPLODE[0];
				$ID = $EXPLODE[1];
				$AsalDeadstok = $EXPLODE[2];
				if($AsalDeadstok == 'loose_dead'){
					$ArrDeadstok[$value]['id'] = $id_uniq;
					$ArrDeadstok[$value]['kode_delivery'] = NULL;
					$ArrDeadstok[$value]['delivery_by'] = NULL;
					$ArrDeadstok[$value]['delivery_date'] = NULL;
				}
				else{
					$ArrDeadstokModif[$value]['id'] = $id_uniq;
					$ArrDeadstokModif[$value]['kode_delivery'] = NULL;
					$ArrDeadstokModif[$value]['delivery_by'] = NULL;
					$ArrDeadstokModif[$value]['delivery_date'] = NULL;
				}

				$ArrDelete[] = $ID;
			}
		}

		//SO AKSESORIS
		$ArrAKsesoris = [];
		if (!empty($data['check6'])) {
			foreach ($check6 as $value) {
				$EXPLODE 	= explode('&', $value);

				$id_uniq 	= $EXPLODE[0];
				$ID 		= $EXPLODE[1];

				if(!array_key_exists($id_uniq, $ArrAKsesoris)){
					$ArrAKsesoris[$id_uniq]['qty'] = 0;
				}

				$resultData = $this->db->get_where('delivery_product_detail',array('id'=>$ID))->result();
				$QTY 		= $resultData[0]->berat;

				$ArrAKsesoris[$id_uniq]['id'] = $id_uniq;
				$ArrAKsesoris[$id_uniq]['qty'] += $QTY;

				$ArrDelete[] = $ID;
			}
		}

		$ArrUpdateAcc = [];
		if(!empty($ArrAKsesoris)){
			foreach ($ArrAKsesoris as $key => $value) {
				$resultData = $this->db->get_where('request_accessories',array('id'=>$key))->result();
				$QTY_FULL 	= $resultData[0]->qty_delivery;

				$ArrUpdateAcc[$key]['id'] = $key;
				$ArrUpdateAcc[$key]['qty_delivery'] = $QTY_FULL - $value['qty'];
			}
		}

		$this->db->trans_start();
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('production_detail', $ArrUpdate, 'id');
		}

		if (!empty($ArrUpdate2)) {
			$this->db->update_batch('production_detail', $ArrUpdate2, 'id');
		}

		if (!empty($ArrUpdate3)) {
			$this->db->update_batch('so_cutting_detail', $ArrUpdate3, 'id');
		}

		if (!empty($ArrUpdateCut)) {
			$this->db->update_batch('so_cutting_detail', $ArrUpdateCut, 'id');
		}

		if (!empty($ArrDeadstok)) {
			$this->db->update_batch('deadstok', $ArrDeadstok, 'id');
		}

		if (!empty($ArrDeadstokModif)) {
			$this->db->update_batch('deadstok_modif', $ArrDeadstokModif, 'id');
		}

		if (!empty($ArrUpdateAcc)) {
			$this->db->update_batch('request_accessories', $ArrUpdateAcc, 'id');
		}

		if (!empty($ArrUpdateMAT)) {
			$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdateMAT, 'id');
		}

		if (!empty($ArrUpdateJoint)) {
			$this->db->update_batch('outgoing_field_joint', $ArrUpdateJoint, 'id');
		}

		if (!empty($ArrDelete)) {
			$this->db->where_in('id', $ArrDelete);
			$this->db->delete('delivery_product_detail');
		}

		if (!empty($ArrDeleteFieldJoint)) {
			$this->db->where_in('id', $ArrDeleteFieldJoint);
			$this->db->delete('delivery_product_detail');
		}

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product', $ArrUpdateRev);

		if (!empty($ArrHistFG)) {
			$this->db->insert_batch('history_product_fg', $ArrHistFG);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Delete sebagian delivery ' . json_encode($kode_id));
		}

		echo json_encode($Arr_Kembali);
	}

	public function update_print()
	{
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$kode_delivery 	= $data['kode_delivery'];
		$nomor_sj 		= $data['nomor_sj'];
		$delivery_date 		= (!empty($data['delivery_date'])) ? date('Y-m-d', strtotime($data['delivery_date'])) : NULL;
		$alamat 		= $data['alamat'];
		$project 		= $data['project'];
		$edit_desc 		= (!empty($data['edit_desc'])) ? $data['edit_desc'] : array();
		$edit_desc_mat 	= (!empty($data['edit_desc_mat'])) ? $data['edit_desc_mat'] : array();

		$ArrUpdate = [
			'delivery_date' => $delivery_date,
			'nomor_sj' => $nomor_sj,
			'alamat' => $alamat,
			'project' => $project,
		];

		$ArrEdit = [];
		if (!empty($edit_desc)) {
			foreach ($edit_desc as $key => $value) {
				$ArrEdit[$key]['id_milik'] = $value['id_milik'];
				$ArrEdit[$key]['desc'] = $value['desc'];
			}
		}

		$ArrEditMat = [];
		if (!empty($edit_desc_mat)) {
			foreach ($edit_desc_mat as $key => $value) {
				$ArrEditMat[$key]['id_uniq'] = $value['id_milik'];
				$ArrEditMat[$key]['desc'] = $value['desc'];
			}
		}


		//SAMPAI SINI
		// print_r($ArrEdit);
		// exit;
		$this->db->trans_start();
		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product', $ArrUpdate);

		if (!empty($ArrEdit)) {
			$this->db->where('kode_delivery', $kode_delivery);
			$this->db->update_batch('delivery_product_detail', $ArrEdit, 'id_milik');
		}

		if (!empty($ArrEditMat)) {
			$this->db->where('kode_delivery', $kode_delivery);
			$this->db->update_batch('delivery_product_detail', $ArrEditMat, 'id_uniq');
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Update print sj delivery : ' . $kode_delivery);
		}

		echo json_encode($Arr_Kembali);
	}

	public function release_delivery()
	{
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$kode_delivery 	= $data['spool'];

		$ArrUpdate = [
			'lock_delivery_by' => $username,
			'lock_delivery_date' => $dateTime
		];

		$ArrUpdate2 = [
			'posisi' => 'TRANSIT'
		];

		//MOVE MATERIAL
		$getProduct = $this->db
			->select('a.*, c.length AS length_awal, e.qty AS tot_qty')
			->from('delivery_product_detail a')
			->join('production_spk b', 'a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik', 'left')
			->join('production_spk_parsial e', "b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'", 'left')
			->join('so_detail_header c', "a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq", 'left')
			->where('kode_delivery', $kode_delivery)
			->get()
			->result_array();
		$ArrMaterial = [];
		$ArrKeyJurnal = [];
		$nomor = 0;
		$SUM = 0;
		foreach ($getProduct as $key => $row) {
			$get_ID_PRO = $this->db->select('id, upload_date')->get_where('production_detail', array('id_milik' => $row['id_milik'], 'kode_spk' => $row['kode_spk'], 'upload_date' => $row['upload_date']))->result();
			$ID_PRO = (!empty($get_ID_PRO[0]->id)) ? $get_ID_PRO[0]->id : 0;

			$MAT_DETAIL = $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
			$MAT_PLUS 	= $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail_plus', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
			$MAT_ADD 	= $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail_add', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
			$MAT_ALL	= array_merge($MAT_DETAIL, $MAT_PLUS, $MAT_ADD);


			foreach ($MAT_ALL as $key2 => $value2) {
				$nomor++;

				$TOT_QTY = $row['tot_qty'];
				$TOTMAT = 0;
				if ($TOT_QTY > 0) {
					$TOTMAT = ($value2['berat']) / $TOT_QTY;
				}

				$TOT_MATERIAL = $TOTMAT;
				if ($row['sts'] == 'cut') {
					$TOT_MATERIAL = ($TOTMAT / $row['length_awal']) * $row['length'];
				}

				$ArrMaterial[$nomor]['id'] = $value2['material'];
				$ArrMaterial[$nomor]['qty'] = $TOT_MATERIAL;

				$SUM += $TOT_MATERIAL;
			}

			if ($row['sts'] == 'loose') {
				$ArrKeyJurnal[] = $row['id_uniq'];
			}
		}
		// move_warehouse($ArrMaterial, 15, 20, $kode_delivery);
		//SAMPAI SINI
		// exit;
		$this->db->trans_start();
		if (!empty($ArrKeyJurnal)) {
			insert_jurnal_delivery($ArrKeyJurnal, $kode_delivery);
		}

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product', $ArrUpdate);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product_detail', $ArrUpdate2);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('production_detail', $ArrUpdate);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('so_cutting_detail', $ArrUpdate);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('deadstok', $ArrUpdate);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1
			);
			move_warehouse_fg($ArrMaterial, 15, 20, $kode_delivery);
			$this->close_jurnal_in_transit($kode_delivery);
			history('Lock release delivery ' . $kode_delivery);
		}

		echo json_encode($Arr_Kembali);
	}

	public function print_delivery2($kode_delivery)
	{
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/', $data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result1 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => NULL))->result_array();
		$result2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$result3 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'field joint'))->result_array();
		$result = array_merge($result1, $result2, $result3);

		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'result'		=> $result,
			'kode_delivery'	=> $kode_delivery,
			'GET_DESC_DEAL'	=> get_descDealSO(),
			'GET_ID_MILIK'	=> get_idMilikSODeal(),
		);
		$this->load->view('Print/print_delivery', $data);
	}

	public function print_delivery($kode_delivery)
	{
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/', $data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		$result1 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik, a.sts')->select('COUNT(a.id_milik) AS qty_product, a.*, "product" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => NULL,'spool_induk'=>NULL))->result_array();
		$result2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*, "material" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material','spool_induk'=>NULL))->result_array();
		$result3 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*, "field joint" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'field joint','spool_induk'=>NULL))->result_array();
		$result5 = $this->db->order_by('a.id', 'asc')->group_by('a.id_pro')->select('COUNT(a.id_milik) AS qty_product, a.*, "deadstok" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'deadstok', 'spool_induk'=>NULL))->result_array();
		$result6 = $this->db->order_by('a.id', 'asc')->group_by('a.product')->select('SUM(a.berat) AS qty_product, a.*, "aksesoris" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts' => 'aksesoris'))->result_array();
		$result4 = $this->db->order_by('a.id', 'asc')->group_by('a.spool_induk, a.kode_spool')->select('COUNT(a.id_milik) AS qty_product, a.*, "spool" AS type_product')->where('(berat > 0 OR berat IS NULL)')->not_like('id_produksi', 'IPPT')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'spool_induk !='=>NULL))->result_array();
		$result7 = $this->db->order_by('a.id', 'asc')->group_by('a.spool_induk, a.kode_spool')->select('"1" AS qty_product, a.*, "spool_tanki" AS type_product')->where('(berat > 0 OR berat IS NULL)')->like('id_produksi', 'IPPT')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'spool_induk !='=>NULL))->result_array();
		$result = array_merge($result1, $result2, $result3, $result4, $result5, $result6, $result7);
		// echo '<pre>';
		// print_r($result);
		// exit;
		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'result'		=> $result,
			'kode_delivery'	=> $kode_delivery,
			'GET_DESC_DEAL'	=> get_descDealSO(),
			'GET_ID_MILIK'	=> get_idMilikSODeal(),
			'tanki_model' 		=> $this->tanki_model
		);
		$this->load->view('Print/print_delivery2', $data);
	}
	
	
	public function print_preview($kode_delivery)
	{
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/', $data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result1 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik, a.sts')->select('COUNT(a.id_milik) AS qty_product, a.*, "product" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => NULL,'spool_induk'=>NULL))->result_array();
		$result2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*, "material" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material','spool_induk'=>NULL))->result_array();
		$result3 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*, "field joint" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'field joint','spool_induk'=>NULL))->result_array();
		$result5 = $this->db->order_by('a.id', 'asc')->group_by('a.id_pro')->select('COUNT(a.id_milik) AS qty_product, a.*, "deadstok" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'deadstok', 'spool_induk'=>NULL))->result_array();
		$result6 = $this->db->order_by('a.id', 'asc')->group_by('a.product')->select('SUM(a.berat) AS qty_product, a.*, "aksesoris" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts' => 'aksesoris'))->result_array();
		$result4 = $this->db->order_by('a.id', 'asc')->group_by('a.spool_induk, a.kode_spool')->select('COUNT(a.id_milik) AS qty_product, a.*, "spool" AS type_product')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'spool_induk !='=>NULL))->result_array();
		$result = array_merge($result1, $result2, $result3, $result4, $result5, $result6);
		// echo '<pre>';
		// print_r($result);
		// exit;
		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'result'		=> $result,
			'kode_delivery'	=> $kode_delivery,
			'GET_DESC_DEAL'	=> get_descDealSO(),
			'GET_ID_MILIK'	=> get_idMilikSODeal(),
			'tanki_model' 		=> $this->tanki_model
		);
		$this->load->view('Print/print_delivery_preview', $data);
	}


	public function print_delivery_draft($kode_delivery)
	{
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/', $data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result1 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => NULL))->result_array();
		$result2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$result = array_merge($result1, $result2);

		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'result'		=> $result,
			'kode_delivery'	=> $kode_delivery,
			'GET_DESC_DEAL'	=> get_descDealSO(),
			'GET_ID_MILIK'	=> get_idMilikSODeal(),
		);
		$this->load->view('Print/print_delivery_draft', $data);
	}

	public function insert_delivery($kode_delivery = null, $list_ipp = null)
	{

		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];

		$ArrUpdate = [
			'kode_delivery' => $kode_delivery,
			'fm_no' => 'FM-C4.1-02',
			'issue_date' => 'Jan 18th, 2016',
			'created_by' => $username,
			'created_date' => $dateTime,
			'updated_by' => $username,
			'updated_date' => $dateTime,
			'list_ipp' => json_encode($list_ipp)
		];

		$CHECK = $this->db->get_where('delivery_product', array('kode_delivery' => $kode_delivery))->result();
		if (!empty($CHECK)) {
			$rev = get_name('delivery_product', 'rev', 'kode_delivery', $kode_delivery);
			$ArrUpdate2 = [
				'updated_by' => $username,
				'updated_date' => $dateTime,
				'rev' => $rev + 1,
				'list_ipp' => json_encode($list_ipp)
			];
		}

		$this->db->trans_start();
		if (empty($CHECK)) {
			$this->db->insert('delivery_product', $ArrUpdate);
		} else {
			$this->db->where('kode_delivery', $kode_delivery);
			$this->db->update('delivery_product', $ArrUpdate2);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			history('Create delivery ' . $kode_delivery);
		}
		$this->db->trans_complete();
	}

	public function insert_detail_delivery($kode_delivery = null, $time_update = null)
	{
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$datetime		= $time_update;
		$username 		= $this->session->userdata['ORI_User']['username'];

		//INSERT DETAIL
		$getInsert = $this->db
			->select('*')
			->from('delivery_group')
			->where('kode_delivery', $kode_delivery)
			->where('delivery_date', $time_update)
			->get()
			->result_array();
		$ArrInsert = [];
		foreach ($getInsert as $key => $value) {
			$IMPLODE = explode('.', $value['product_code']);
			$product_code = $IMPLODE[0] . '.' . $value['product_ke'];
			$Cutting_ke = '';
			if ($value['sts'] == 'cut') {
				$Cutting_ke = "." . $value['cutting_ke'];
				$product_code = $IMPLODE[0] . '.' . $value['product_ke'] . $Cutting_ke;

				$ArrInsert[$key]['sts_product'] = $value['sts'];
			}
			if ($value['id_milik'] == null) {
				$product_code = $value['dead_no_so'];
			}

			$ArrInsert[$key]['kode_delivery'] = $kode_delivery;
			$ArrInsert[$key]['id_uniq'] = $value['id'];
			$ArrInsert[$key]['id_pro'] = $value['id_pro'];
			$ArrInsert[$key]['product'] = $value['id_category'];
			$ArrInsert[$key]['id_milik'] = $value['id_milik'];
			$ArrInsert[$key]['id_produksi'] = $value['id_produksi'];
			$ArrInsert[$key]['spool_induk'] = $value['spool_induk'];
			$ArrInsert[$key]['kode_spool'] = $value['kode_spool'];
			$ArrInsert[$key]['product_code'] = $product_code;
			$ArrInsert[$key]['no_spk'] = (!empty($value['no_spk']))?$value['no_spk']:$value['dead_no_spk'];
			$ArrInsert[$key]['product_ke'] = $value['product_ke'];
			$ArrInsert[$key]['kode_spk'] = $value['kode_spk'];
			$ArrInsert[$key]['length'] = $value['length'];
			$ArrInsert[$key]['cutting_ke'] = $value['cutting_ke'];
			$ArrInsert[$key]['no_drawing'] = $value['no_drawing'];
			$ArrInsert[$key]['upload_date'] = $value['upload_date'];
			$ArrInsert[$key]['sts'] = (empty($value['id_milik']))?'cut':$value['sts'];
			$ArrInsert[$key]['sts_product'] = (empty($value['id_milik']))?'cut deadstock':null;
			//agus
			$ArrInsert[$key]['nilai_cogs'] = $value['finish_good'];
			$ArrInsert[$key]['updated_by'] = $username;
			$ArrInsert[$key]['updated_date'] = $datetime;
		}
		// exit;
		$this->db->trans_start();
		if (!empty($ArrInsert)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			history('Create data detail delivery ' . $kode_delivery);
		}
	}

	//DELIVERY
	public function received()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Confirm Delivery',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data confirm delivery received');
		$this->load->view('Delivery/received', $data);
	}

	public function server_side_received()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_received(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
		$GET_USERNAME 		= get_detail_user();
		$GET_DET_FD 		= get_detailFinalDrawing();
		$GET_SALES_ORDER 	= get_detail_ipp();
		$GET_MATERIAL = get_detail_material();
		$tanki_model = $this->tanki_model;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$check = "";
			$print = "";
			$release = "";
			if (empty($row['confirm_date'])) {
				$print = "<a href='" . base_url('delivery/print_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				if (empty($row['material'])) {
					$release = "<button type='button' class='btn btn-sm btn-danger back_to_delivery' data-spool='" . $row['kode_delivery'] . "' title='Reject Delivery'><i class='fa fa-reply'></i></button>";
				}
				$check	= "<button class='btn btn-sm btn-success check_real' title='Already Received' data-kode_delivery='" . $row['kode_delivery'] . "'><i class='fa fa-check'></i></button>";
			}
			if (!empty($row['confirm_date'])) {
				$check	= "<button class='btn btn-sm btn-primary check_real' title='Detail Received' data-kode_delivery='" . $row['kode_delivery'] . "'><i class='fa fa-file'></i></button>";
			}
			$view = "<a href='" . base_url('delivery/view_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";


			$GetSPEC 		= $this->detailDelivery($row['kode_delivery']);

			$explode_ipp 	= $GetSPEC['explode_ipp'];
			$explode_nd 	= $GetSPEC['explode_nd'];
			$explode_spk 	= $GetSPEC['explode_spk'];
			// $explode_ls 	= implode('<br>',$ArrNo_LS);


			if (!empty($row['material'])) {
				$explode_ipp = str_replace('BQ-', '', $row['id_produksi']);
				$get_split_ipp = $this->db->select('product, berat')->get_where('delivery_product_detail', array('kode_delivery' => $row['kode_delivery'], 'sts_product' => 'so material'))->result_array();
				foreach ($get_split_ipp as $key => $value) {
					if ($value['product'] > 0) {
						$key++;
						$ArrNo_SPK[] = $key . '. ' . $GET_MATERIAL[$value['product']]['nm_material'] . ' / MATERIAL ORIGA';
					}
				}
				$explode_spk = implode('<br>', $ArrNo_SPK);
				$explode_ls = 'MATERIAL ORIGA';
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_delivery'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['nomor_sj'] . "</div>";
			// $nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>" . $explode_nd . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			// $nestedData[]	= "<div align='left'>".$explode_ls."</div>";
			$update_by 	= strtolower($row['updated_by']);
			$NM_LENGKAP = (!empty($GET_USERNAME[$update_by]['nm_lengkap'])) ? $GET_USERNAME[$update_by]['nm_lengkap'] : $update_by;
			$nestedData[]	= "<div align='center'>" . strtoupper($NM_LENGKAP) . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i', strtotime($row['updated_date'])) . "</div>";
			$nestedData[]	= "<div align='left'>
									" . $view . "
									" . $print . "
									" . $release . "
									" . $check . "
								</div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_received($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		// if($no_ipp <> 0){
		// 	$where = " AND a.id_produksi='".$no_ipp."' ";
		// }

		$where2 = " AND (b.id_produksi NOT IN " . filter_not_in() . " OR b.id_produksi IS NULL) ";

		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					b.id_produksi
				FROM
					delivery_product a
					LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery,
					(SELECT @row:=0) r
				WHERE 1=1 " . $where . " " . $where2 . "
					AND a.lock_delivery_date IS NOT NULL
					AND b.posisi <> 'CUSTOMER'
					AND (
						a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.nomor_sj LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR b.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
				GROUP BY
					a.kode_delivery
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_delivery'
		);

		$sql .= " ORDER BY a.lock_delivery_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function reject_delivery()
	{
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$kode_delivery 	= $data['spool'];

		$ArrUpdate = [
			'lock_delivery_by' => NULL,
			'lock_delivery_date' => NULL
		];

		$ArrUpdate2 = [
			'posisi' => 'FINISH GOOD'
		];

		//MOVE MATERIAL
		$getProduct = $this->db
			->select('a.*, c.length AS length_awal, e.qty AS tot_qty')
			->from('delivery_product_detail a')
			->join('production_spk b', 'a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik', 'left')
			->join('production_spk_parsial e', "b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'", 'left')
			->join('so_detail_header c', "a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq", 'left')
			->where('kode_delivery', $kode_delivery)
			->get()
			->result_array();
		$ArrMaterial = [];
		$ArrKeyJurnal = [];
		$nomor = 0;
		$SUM = 0;
		foreach ($getProduct as $key => $row) {
			$get_ID_PRO = $this->db->select('id, upload_date')->get_where('production_detail', array('id_milik' => $row['id_milik'], 'kode_spk' => $row['kode_spk'], 'upload_date' => $row['upload_date']))->result();
			$ID_PRO = (!empty($get_ID_PRO[0]->id)) ? $get_ID_PRO[0]->id : 0;

			$MAT_DETAIL = $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
			$MAT_PLUS 	= $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail_plus', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
			$MAT_ADD 	= $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail_add', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
			$MAT_ALL	= array_merge($MAT_DETAIL, $MAT_PLUS, $MAT_ADD);

			foreach ($MAT_ALL as $key2 => $value2) {
				$nomor++;

				$TOT_QTY = $row['tot_qty'];
				$TOTMAT = 0;
				if ($TOT_QTY > 0) {
					$TOTMAT = ($value2['berat']) / $TOT_QTY;
				}

				$TOT_MATERIAL = $TOTMAT;
				if ($row['sts'] == 'cut') {
					$TOT_MATERIAL = ($TOTMAT / $row['length_awal']) * $row['length'];
				}

				$ArrMaterial[$nomor]['id'] = $value2['material'];
				$ArrMaterial[$nomor]['qty'] = $TOT_MATERIAL;

				$SUM += $TOT_MATERIAL;
			}

			if ($row['sts'] == 'loose') {
				$ArrKeyJurnal[] = $row['id_uniq'];
			}
		}
		move_warehouse_fg($ArrMaterial, 20, 15, $kode_delivery);
		//SAMPAI SINI
		// exit;

		$this->db->trans_start();
		if (!empty($ArrKeyJurnal)) {
			insert_jurnal_delivery_reject($ArrKeyJurnal, $kode_delivery);
		}

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product', $ArrUpdate);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product_detail', $ArrUpdate2);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('production_detail', $ArrUpdate);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('so_cutting_detail', $ArrUpdate);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1
			);
			$this->close_jurnal_in_transit_reject_to_fg($kode_delivery);
			history('Reject release delivery ' . $kode_delivery);
		}

		echo json_encode($Arr_Kembali);
	}

	public function confirm_delivery()
	{
		$kode_delivery 	= $this->uri->segment(3);

		$header = $this->db->order_by('kode_spool', 'asc')->get_where('production_detail', array('kode_delivery' => $kode_delivery))->result_array();
		$get_spk = $this->db->get_where('delivery_product', array('kode_delivery' => $kode_delivery))->result();

		$result = $this->db
							->select('a.*, COUNT(id) AS qtyCount')
							->group_by('a.id_milik')
							->order_by('a.id', 'asc')
							->group_start()
							->where_not_in('sts_product',['so material','field joint'])
							->or_where('sts_product',NULL)
							->group_end()
							->get_where('delivery_product_detail a', 
								array(
									'a.kode_delivery' => $kode_delivery, 
									'a.spool_induk' => NULL
									)
								)->result_array();
		$result2 = $this->db->order_by('id', 'asc')->group_by('kode_spool')->group_by('spool_induk')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'spool_induk <>' => NULL))->result_array();
		$result3 = $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$result4 = $this->db->order_by('id', 'asc')->get_where('delivery_product_detail', array('kode_delivery' => $kode_delivery, 'sts_product' => 'field joint'))->result_array();
		$data = [
			'kode_delivery' => $kode_delivery,
			'header' => $header,
			'result' => $result,
			'result2' => $result2,
			'result3' => $result3,
			'result4' => $result4,
			'get_spk' => $get_spk,
			'GET_MATERIAL' => get_detail_material(),
			'tanki_model' 		=> $this->tanki_model
		];
		$this->load->view('Delivery/confirm_delivery', $data);
	}

	public function sukses_delivery()
	{
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$kode_delivery			= $data['kode_delivery'];
		$dateTime = date('Y-m-d H:i:s');

		$ArrFlagRelease = [
			'release_delivery_by' => $data_session['ORI_User']['username'],
			'release_delivery_date' => $dateTime
		];

		//DATA SPK
		$start_time				= date('Y-m-d', strtotime($data['start_time']));
		$finish_time			= date('Y-m-d', strtotime($data['finish_time']));
		$ekspedisi				= str_replace(',', '', $data['ekspedisi']);
		$diterima_oleh			= str_replace(',', '', $data['diterima_oleh']);
		$file_name				= '';

		$dateTime = date('Y-m-d H:i:s');

		$ArrEditHeader = [
			'start_time' => $start_time,
			'finish_time' => $finish_time,
			'ekspedisi' => $ekspedisi,
			'diterima_oleh' => $diterima_oleh,
			'confirm_by' => $data_session['ORI_User']['username'],
			'confirm_date' => $dateTime
		];

		//UPLOAD DOCUMENT
		if (!empty($_FILES["upload_spk"]["name"])) {
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "ori_dummy/assets/file/produksi/";
			$name_file      = $kode_delivery . '_delivery_' . date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
			$file_name    	= $name_file . "." . $imageFileType;

			if (!empty($_FILES["upload_spk"]["tmp_name"])) {
				// if($imageFileType <> 'pdf'){
				// 	$Arr_Data	= array(
				// 		'pesan'		=>'Hanya file pdf yang diperbolehkan !!!',
				// 		'status'	=> 0
				// 	);
				// 	echo json_encode($Arr_Data);
				// 	return false;
				// }
				// if($imageFileType == 'pdf'){
				$terupload = move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
				// if ($terupload) {
				//     echo "Upload berhasil!<br/>";
				// } else {
				//     echo "Upload Gagal!";
				// }
				// }
			}

			$ArrEditHeader = [
				'start_time' => $start_time,
				'finish_time' => $finish_time,
				'ekspedisi' => $ekspedisi,
				'diterima_oleh' => $diterima_oleh,
				'upload_spk' => $file_name,
				'confirm_by' => $data_session['ORI_User']['username'],
				'confirm_date' => $dateTime
			];
		}

		$ArrUpdate2 = [
			'posisi' => 'CUSTOMER'
		];

		//MOVE MATERIAL
		$getProduct = $this->db
			->select('a.*, c.length AS length_awal, e.qty AS tot_qty, z.material AS sts_mtl')
			->from('delivery_product_detail a')
			->join('production_spk b', 'a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik', 'left')
			->join('production_spk_parsial e', "b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'", 'left')
			->join('so_detail_header c', "a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq", 'left')
			->join('delivery_product z', "a.kode_delivery = z.kode_delivery", 'left')
			->where('a.kode_delivery', $kode_delivery)
			->get()
			->result_array();
		$ArrMaterial = [];
		$ArrKeyJurnal = [];
		$nomor = 0;
		$SUM = 0;
		if (!empty($getProduct)) {
			foreach ($getProduct as $key => $row) {
				if ($row['sts_mtl'] != 'Y') {
					$get_ID_PRO = $this->db->select('id, upload_date')->get_where('production_detail', array('id_milik' => $row['id_milik'], 'kode_spk' => $row['kode_spk'], 'upload_date' => $row['upload_date']))->result();
					$ID_PRO = (!empty($get_ID_PRO[0]->id)) ? $get_ID_PRO[0]->id : 0;

					$MAT_DETAIL = $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
					$MAT_PLUS 	= $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail_plus', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
					$MAT_ADD 	= $this->db->select('actual_type AS material, material_terpakai AS berat')->get_where('tmp_production_real_detail_add', array('catatan_programmer' => $row['kode_spk'] . '/' . $row['upload_date'], 'id_production_detail' => $ID_PRO, 'actual_type LIKE ' => 'MTL-%'))->result_array();
					$MAT_ALL	= array_merge($MAT_DETAIL, $MAT_PLUS, $MAT_ADD);

					foreach ($MAT_ALL as $key2 => $value2) {
						$nomor++;

						$TOT_QTY = $row['tot_qty'];
						$TOTMAT = 0;
						if ($TOT_QTY > 0) {
							$TOTMAT = ($value2['berat']) / $TOT_QTY;
						}

						$TOT_MATERIAL = $TOTMAT;
						if ($row['sts'] == 'cut') {
							$TOT_MATERIAL = ($TOTMAT / $row['length_awal']) * $row['length'];
						}

						$ArrMaterial[$nomor]['id'] = $value2['material'];
						$ArrMaterial[$nomor]['qty'] = $TOT_MATERIAL;

						$SUM += $TOT_MATERIAL;
					}

					if ($row['sts'] == 'loose') {
						$ArrKeyJurnal[] = $row['id_uniq'];
					}
				}
			}
			if (!empty($ArrMaterial)) {
				move_warehouse_fg($ArrMaterial, 20, 21, $kode_delivery);
			}
		}
		//SAMPAI SINI
		// exit;

		// print_r($ArrFlagRelease);
		// print_r($ArrEditHeader);
		// exit;

		$this->db->trans_start();

		if (!empty($ArrKeyJurnal)) {
			insert_jurnal_delivery_confirm($ArrKeyJurnal, $kode_delivery);
		}
		//Update FLAG
		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('production_detail', $ArrFlagRelease);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('so_cutting_detail', $ArrFlagRelease);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product', $ArrEditHeader);

		$this->db->where('kode_delivery', $kode_delivery);
		$this->db->update('delivery_product_detail', $ArrUpdate2);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Success process data. Thanks ...',
				'status'	=> 1
			);
			$this->close_jurnal_in_customer($kode_delivery);
			history('Delivery confirm = ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	//RECEIVED
	public function terkirim()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Delivered Delivery',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data terkirim delivery received');
		$this->load->view('Delivery/terkirim', $data);
	}

	public function server_side_terkirim()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_terkirim(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
		$GET_USERNAME 		= get_detail_user();
		$GET_DET_FD 		= get_detailFinalDrawing();
		$GET_SALES_ORDER 	= get_detail_ipp();
		$tanki_model = $this->tanki_model;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}


			$check = "";
			$print = "";
			$release = "";
			if (empty($row['confirm_date'])) {
				$print = "<a href='" . base_url('delivery/print_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				$release = "<button type='button' class='btn btn-sm btn-danger back_to_delivery' data-spool='" . $row['kode_delivery'] . "' title='Reject Delivery'><i class='fa fa-reply'></i></button>";
				$check	= "<button class='btn btn-sm btn-success check_real' title='Already Received' data-kode_delivery='" . $row['kode_delivery'] . "'><i class='fa fa-check'></i></button>";
			}
			if (!empty($row['confirm_date'])) {
				$check	= "<button class='btn btn-sm btn-primary check_real' title='Detail Received' data-kode_delivery='" . $row['kode_delivery'] . "'><i class='fa fa-file'></i></button>";
			}
			$view = "<a href='" . base_url('delivery/view_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";


			$GetSPEC 		= $this->detailDelivery($row['kode_delivery']);

			$explode_ipp 	= $GetSPEC['explode_ipp'];
			$explode_nd 	= $GetSPEC['explode_nd'];
			$explode_spk 	= $GetSPEC['explode_spk'];

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_delivery'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['nomor_sj'] . "</div>";
			// $nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>" . $explode_nd . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			// $nestedData[]	= "<div align='left'>".$explode_ls."</div>";
			$update_by 	= strtolower($row['updated_by']);
			$NM_LENGKAP = (!empty($GET_USERNAME[$update_by]['nm_lengkap'])) ? $GET_USERNAME[$update_by]['nm_lengkap'] : $update_by;
			$nestedData[]	= "<div align='center'>" . strtoupper($NM_LENGKAP) . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i', strtotime($row['updated_date'])) . "</div>";
			$nestedData[]	= "<div align='left'>
									" . $view . "
									" . $print . "
									" . $release . "
									" . $check . "
								</div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_terkirim($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		// if($no_ipp <> 0){
		// 	$where = " AND a.id_produksi='".$no_ipp."' ";
		// }
		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";

		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*
				FROM
					delivery_product a
					LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery,
					(SELECT @row:=0) r
				WHERE 1=1 " . $where . " " . $where2 . "
					AND b.posisi = 'CUSTOMER'
					AND (
						a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.nomor_sj LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR b.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						)
				GROUP BY
					a.kode_delivery
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_delivery'
		);

		$sql .= " ORDER BY a.confirm_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//TRANSIT
	public function transit()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Transit Delivery',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data delivery transit');
		$this->load->view('Delivery/transit', $data);
	}

	public function server_side_transit()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_transit(
			$requestData['status'],
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

		$GET_IPP = get_detail_ipp();
		$GET_MATERIAL = get_detail_material();
		$GET_FD = get_detail_final_drawing();

		$FLAG = $requestData['status'];
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$NO_IPP 		= str_replace('PRO-', '', $row['id_produksi']);
			$nm_customer 	= (!empty($GET_IPP[$NO_IPP]['nm_customer']))?$GET_IPP[$NO_IPP]['nm_customer']:'';
			$nm_project 	= (!empty($GET_IPP[$NO_IPP]['nm_project']))?$GET_IPP[$NO_IPP]['nm_project']:'';
			$so_number 		= (!empty($GET_IPP[$NO_IPP]['so_number']))?$GET_IPP[$NO_IPP]['so_number']:'';
			$no_spk 		= (!empty($GET_FD[$row['id_milik']]['no_spk']))?$GET_FD[$row['id_milik']]['no_spk']:'';
			$LENGTH 		= ($row['sts'] == 'cut') ? number_format($row['length']) : '';

			$get_ID_PRO = $this->db->select('id, upload_date')->get_where('production_detail', array('id_milik' => $row['id_milik'], 'kode_spk' => $row['kode_spk'], 'upload_date' => $row['upload_date']))->result();
			$ID_PRO = (!empty($get_ID_PRO[0]->id))?$get_ID_PRO[0]->id:0;
			$upload_date = (!empty($get_ID_PRO[0]->upload_date))?$get_ID_PRO[0]->upload_date:0;

			$SUM_DETAIL = $this->db->select('SUM(material_terpakai) AS berat')->get_where('production_real_detail', array('catatan_programmer' => $row['kode_spk'] . '/' . $upload_date, 'id_production_detail' => $ID_PRO))->result();
			$SUM_PLUS = $this->db->select('SUM(material_terpakai) AS berat')->get_where('tmp_production_real_detail_plus', array('catatan_programmer' => $row['kode_spk'] . '/' . $upload_date, 'id_production_detail' => $ID_PRO))->result();
			$SUM_ADD = $this->db->select('SUM(material_terpakai) AS berat')->get_where('tmp_production_real_detail_add', array('catatan_programmer' => $row['kode_spk'] . '/' . $upload_date, 'id_production_detail' => $ID_PRO))->result();

			$TOT_QTY = $row['tot_qty'];
			$TOTMAT = 0;
			if ($TOT_QTY > 0) {
				$TOTMAT = ($SUM_DETAIL[0]->berat + $SUM_PLUS[0]->berat + $SUM_ADD[0]->berat) / $TOT_QTY;
			}
			$TOT_MATERIAL = $TOTMAT;
			$status = 'fitting';
			if ($row['sts'] == 'cut') {
				$status = 'cutting';
				$TOT_MATERIAL = ($TOTMAT / $row['length_awal']) * $row['length'];
			}

			$product = $row['product'];
			$spec = spec_bq2($row['id_milik']);
			$berat = $TOT_MATERIAL;
			$qty = $row['qty_count'];
			if ($row['sts_product'] == 'so material') {
				$product = (!empty($GET_MATERIAL[$row['product']]['nm_material']))?$GET_MATERIAL[$row['product']]['nm_material']:'';
				$spec = '';
				$berat = $row['berat_mat'];
				$qty = '';
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['kode_delivery']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($product)."</div>";
			$nestedData[]	= "<div align='left'>".$so_number."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_project)."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='right'>".$LENGTH."</div>";
			$nestedData[]	= "<div align='center'>".$qty."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat, 4)."</div>";
			$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-warning look_history' data-statusx='" . $status . "' data-length='" . $row['length'] . "' data-length_awal='" . $row['length_awal'] . "' data-kode_spk='" . $row['kode_spk'] . "' data-id_production_detail='" . $ID_PRO . "' data-category='2' data-qty='" . $TOT_QTY . "'><i class='fa fa-eye'></i></button></div>";


			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_transit($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		// if($no_ipp <> 0){
		$where = " AND a.posisi='TRANSIT' ";
		// }

		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";

		$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					e.qty AS tot_qty,
					c.length AS length_awal,
					COUNT(a.id) AS qty_count,
					SUM(a.berat) AS berat_mat
				FROM
					delivery_product_detail a
					LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
                	LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq,
					(SELECT @row:=0) r
				WHERE 1=1 " . $where . " " . $where2 . "
					AND (
						a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
				GROUP BY
					sts_product, id_milik, product
					
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_delivery'
		);

		$sql .= " ORDER BY a.updated_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//TRANSIT
	public function customer()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Customer Delivery',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data delivery customer');
		$this->load->view('Delivery/customer', $data);
	}

	public function server_side_customer()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_customer(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
		$GET_USERNAME 		= get_detail_user();
		$GET_DET_FD 		= get_detailFinalDrawing();
		$GET_SALES_ORDER 	= get_detail_ipp();
		$tanki_model = $this->tanki_model;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}


			$edit = "";
			$edit_print = "";
			$print = "";
			$print2 = "";
			$excel = "";
			$release = "";
			if (empty($row['lock_delivery_date'])) {
				$edit = "<a href='" . base_url('delivery/edit_delivery/' . $row['kode_delivery']) . "' class='btn btn-sm btn-primary' title='Edit'><i class='fa fa-edit'></i></a>";
				$print = "<a href='" . base_url('delivery/print_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				$print2 = "<a href='" . base_url('delivery/print_delivery_draft/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-default' title='Print LX'><i class='fa fa-print'></i></a>";
				$excel = "<a href='" . base_url('delivery/delivery_xls/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-default' title='Excel'><i class='fa fa-file-excel-o'></i></a>";
				$release = "<button type='button' class='btn btn-sm btn-success lock_spool' data-spool='" . $row['kode_delivery'] . "' title='Lock Delivery'><i class='fa fa-check'></i></button>";
				// $edit_print = "<button type='button' class='btn btn-sm bg-purple edit_print' data-kode_delivery='".$row['kode_delivery']."' title='Edit Surat Jalan'><i class='fa fa-file'></i></button>";
			}
			$view = "<a href='" . base_url('delivery/view_delivery/' . $row['kode_delivery']) . "' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";
			$get_split_ipp1 = $this->db
									->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
									->group_by('a.id_milik, a.sts')
									->order_by('a.spool_induk', 'asc')
									->order_by('a.kode_spool', 'asc')
									->where('(a.berat > 0 OR a.berat IS NULL)')
									->join('production_detail b','a.id_pro=b.id','left')
									->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'a.sts_product' => NULL))->result_array();
			$get_split_ipp2 = $this->db->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where('(a.berat > 0 OR a.berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'sts_product' => 'so material'))->result_array();
			$get_split_ipp3 = $this->db->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where('(a.berat > 0 OR a.berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'sts_product' => 'field joint'))->result_array();
			$get_split_ipp4 = $this->db->select('COUNT(a.berat) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_pro')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where("(a.berat > 0 OR a.berat IS NULL OR a.sts = 'loose_dead')")->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'sts_product' => 'deadstok'))->result_array();
			$get_split_ipp = array_merge($get_split_ipp1, $get_split_ipp2, $get_split_ipp3, $get_split_ipp4);
			$ArrNo_IPP = [];
			$ArrNo_SPK = [];
			$ArrNo_LS = [];
			$ArrNo_Drawing = [];
			foreach ($get_split_ipp as $key => $value) {
				$key++;
				$no_spk 		= $value['no_spk'];
				$NO_IPP 		= str_replace(['PRO-', 'BQ-'], '', $value['id_produksi']);
				$NO_SO 			= (!empty($GET_SALES_ORDER[$NO_IPP]['so_number'])) ? $GET_SALES_ORDER[$NO_IPP]['so_number'] : '';
				$ArrNo_IPP[]	= $NO_SO;
				if (!empty($value['no_drawing'])) {
					$ArrNo_Drawing[] = $value['no_drawing'];
				}

				$CUTTING_KE = (!empty($value['cutting_ke'])) ? '.' . $value['cutting_ke'] : '';
				$IMPLODE = explode('.', $value['product_code']);
				$ID_PRX_ADD = $IMPLODE[0] . '.' . $value['product_ke'] . $CUTTING_KE . '/' . $no_spk;
				if ($value['sts_product'] == 'so material') {
					if ($value['berat'] > 0) {
						$ID_PRX_ADD = strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
					}
				}

				if ($value['sts_product'] == 'field joint') {
					if ($value['berat'] > 0) {
						$ID_PRX_ADD = strtoupper(get_name('so_number', 'so_number', 'id_bq', str_replace('PRO-', 'BQ-', $value['id_produksi']))) . '/' . $no_spk;
					}
				}

				$series 	= (!empty($GET_DET_FD[$value['id_milik']]['series'])) ? $GET_DET_FD[$value['id_milik']]['series'] : '';
				$product 	= strtoupper($value['product']) . ", " . $series . ", DIA " . spec_bq2($value['id_milik']);
				$SATUAN 	= ' pcs';
				$QTY 		= $value['qty_product'];

				if ($value['sts_product'] == 'deadstok') {
					$ID_PRX_ADD = $value['product_code'].'/'.$value['no_spk'];
					$product 	= strtoupper($value['product']) . ", DIA " . $value['product_code'].' x '.$value['length'];
				}

				if ($value['sts'] == 'loose_dead') {
					$ID_PRX_ADD = $value['product_code'].'/'.$value['no_spk'];
					$product 	= strtoupper($value['product']) . ", DIA " . $value['kode_spk'].' x '.$value['length'];
				}

				if ($value['type_product'] == 'tanki') {
					$spec = $tanki_model->get_spec($value['id_milik']);
					$product 	= strtoupper($value['product_tanki']) . ", " . $spec;
				}

				$ID_MILIK 	= (!empty($GET_ID_MILIK[$value['id_milik']])) ? $GET_ID_MILIK[$value['id_milik']] : '';
				if ($value['sts_product'] == 'so material') {
					$product 	= strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
					$SATUAN 	= ' kg';
					$QTY 		= number_format($value['berat'], 2);
					$ID_MILIK 	= '';
				}

				if ($value['sts_product'] == 'field joint') {
					$SATUAN     = ' kit';
					$QTY         = number_format($value['berat']);
				}

				if ($value['sts_product'] == 'deadstok') {
					$QTY         = number_format($value['qty_product']);
				}

				$ID_PRX = "[<b>" . $QTY . $SATUAN . "</b>][<b>" . $ID_PRX_ADD . "</b>], " . $product;

				$QNOSO = $this->db->get_where('so_number', ['id_bq' => str_replace("PRO", "BQ", $value['id_produksi'])])->row();
				$NOSO = (!empty($QNOSO->so_number))?$QNOSO->so_number:'-';

				//Category
				$loose_spool = (!empty($value['spool_induk'])) ? $value['spool_induk'] . '-' . $value['kode_spool'] : 'LOOSE';
				if ($value['sts_product'] == 'so material') {
					$loose_spool = $NOSO;
				}
				if ($value['sts_product'] == 'field joint') {
					$loose_spool = "FIELD JOINT";
				}
				if ($value['sts'] == 'cut' and empty($value['spool_induk'])) {
					$loose_spool = "LOOSE PIPE CUTTING";
				}
				if ($value['sts_product'] == 'deadstok') {
					$loose_spool = "DEADSTOCK";
				}
				$ArrNo_LS[] = $key . '. ' . $loose_spool;

				$ArrNo_SPK[] = $key . ".<span class='text-bold text-blue'>" . $loose_spool . "</span> " . $ID_PRX;
			}
			// print_r($ArrGroup); exit;
			$explode_ipp 	= implode('<br>', array_unique($ArrNo_IPP));
			$explode_nd 	= implode('<br>', array_unique($ArrNo_Drawing));
			$explode_spk 	= implode('<br>', $ArrNo_SPK);
			// $explode_ls 	= implode('<br>',$ArrNo_LS);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_delivery'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['nomor_sj'] . "</div>";
			// $nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>" . $explode_nd . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			// $nestedData[]	= "<div align='left'>".$explode_ls."</div>";
			$update_by 	= strtolower($row['updated_by']);
			$NM_LENGKAP = (!empty($GET_USERNAME[$update_by]['nm_lengkap'])) ? $GET_USERNAME[$update_by]['nm_lengkap'] : $update_by;
			$nestedData[]	= "<div align='center'>" . strtoupper($NM_LENGKAP) . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i', strtotime($row['updated_date'])) . "</div>";
			// $nestedData[]	= "<div align='left'>
			// 						" . $view . "
			// 						" . $edit . "
			// 						" . $print . "
			// 						" . $print2 . "
			// 						" . $excel . "
			// 						" . $release . "
			// 						" . $edit_print . "
			// 					</div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_customer($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = " AND b.posisi='CUSTOMER' ";
		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				delivery_product a
				LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery,
				(SELECT @row:=0) r
		    WHERE 1=1 " . $where . " 
				AND a.release_delivery_date IS NULL
				AND b.sts_invoice = 0
				AND (
					a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.nomor_sj LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.no_drawing LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.sts_product LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.sts LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.kode_delivery
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_delivery'
		);

		$sql .= " ORDER BY a.updated_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//DELIVERY SO MATERIAL
	public function add_material()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$username 		= $this->session->userdata['ORI_User']['username'];

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";
		$data_spool = $this->db->query(" SELECT
											a.kode_delivery
										FROM
											delivery_product a 
											LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
										WHERE
											a.lock_delivery_date IS NULL
											GROUP BY a.kode_delivery
										ORDER BY
											a.kode_delivery ASC")->result_array();

		$this->db->where('created_by', $username);
		$this->db->where('category', 'material');
		$this->db->delete('delivery_temp');

		$this->db->where('created_by', $username);
		$this->db->where('category', 'field');
		$this->db->delete('delivery_temp');

		$no_sales_order = $this->db->query("SELECT
												a.id_bq,
												b.so_number
											FROM
												request_outgoing a 
												LEFT JOIN so_number b ON a.id_bq = b.id_bq
											WHERE 
												a.kode_delivery IS NULL 
												AND a.kode_trans IS NOT NULL
												AND a.kode_trans NOT IN ".getFiledJoint()."
											GROUP BY a.id_bq
											ORDER BY
												a.id_bq ASC
											")->result_array();
		$no_spk_list = $this->db->query("	SELECT
												a.id_milik,
												b.no_spk
											FROM
												request_outgoing a 
												LEFT JOIN so_detail_header b ON a.id_milik = b.id
											WHERE a.kode_delivery IS NULL AND a.kode_trans IS NOT NULL
											GROUP BY a.id_milik
											ORDER BY
												a.id_milik ASC
											")->result_array();

		$data = array(
			'title'			=> 'Add Delivery (SO Material)',
			'action'		=> 'index',
			'data_spool'	=> $data_spool,
			'no_sales_order'	=> $no_sales_order,
			'no_spk_list'	=> $no_spk_list,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add_material', $data);
	}

	public function server_side_so_material()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_so_material(
			$requestData['no_so'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		$GET_NO_SO = get_detail_ipp();
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['id_uniq'], 'category' => 'material'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_trans'] . "</div>";
			$NO_IPP 		= str_replace('BQ-', '', $row['no_bq']);
			$SALES_ORDER 	= (!empty($GET_NO_SO[$NO_IPP]['so_number'])) ? $GET_NO_SO[$NO_IPP]['so_number'] : $NO_IPP;
			$nestedData[]	= "<div align='center'>" . $SALES_ORDER . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['nm_material'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['nm_category'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['note'] . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y', strtotime($row['created_date'])) . "</div>";
			$nestedData[]	= "<div align='center'>" . number_format($row['qty_oke'], 4) . " kg
									<input type='hidden' name='spk_" . $row['id_uniq'] . "' class='form-control text-center qty_spk input-sm' value='" . $row['qty_oke'] . "'>
									<input type='hidden' name='ipp_" . $row['id_uniq'] . "' value='" . $row['no_bq'] . "'>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' $checked class='chk_personal  chk_material' data-nomor='$nomor' data-qty='" . $row['qty_oke'] . "' value='" . $row['id_uniq'] . "' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_so_material($no_so, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		// $sql = "SELECT
		// 			(@row:=@row+1) AS nomor,
		// 			z.qty_oke,
		// 			z.kode_trans,
		// 			z.nm_material,
		// 			z.nm_category,
		// 			z.id AS id_uniq,
		// 			a.note,
		// 			a.created_date,
		// 			(SELECT c.no_ipp FROM warehouse_adjustment c WHERE a.no_ipp=c.kode_trans) AS no_bq,
		// 			b.so_number
		// 		FROM
		// 			warehouse_adjustment_detail z
		// 			LEFT JOIN  warehouse_adjustment a ON a.kode_trans=z.kode_trans
		// 			LEFT JOIN so_number b ON (SELECT c.no_ipp FROM warehouse_adjustment c WHERE a.no_ipp=c.kode_trans) = b.id_bq,
		// 			(SELECT @row:=0) r
		// 		WHERE 1=1 
		// 			AND (a.category = 'outgoing subgudang' OR a.id_gudang_ke = '23')
		// 			AND z.qty_oke > 0
		// 			AND z.proccess_date IS NULL
		// 			AND (
		// 					((SELECT c.no_ipp FROM warehouse_adjustment c WHERE a.no_ipp=c.kode_trans) LIKE 'BQ-IPP%') 
		// 					OR (a.id_gudang_ke = '23' AND a.category = 'outgoing pusat')  
		// 					OR a.category = 'outgoing subgudang'
		// 				)
		// 			AND (
		// 				z.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 				OR a.note LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			)
		// ";

		$where = '';
		if ($no_so != '0') {
			$where = " AND (a.no_ipp LIKE '%" . $no_so . "%' OR c.no_ipp LIKE '%" . $no_so . "%') ";
		}
		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					z.qty_oke,
					z.kode_trans,
					z.nm_material,
					z.nm_category,
					z.id AS id_uniq,
					a.note,
					a.created_date,
					a.no_ipp AS no_bq
				FROM
					warehouse_adjustment_detail z
					LEFT JOIN  warehouse_adjustment a ON a.kode_trans=z.kode_trans
					LEFT JOIN  warehouse_adjustment c ON a.no_ipp=c.kode_trans,
					(SELECT @row:=0) r
				WHERE 1=1 " . $where . "
					AND (a.category = 'outgoing subgudang' OR a.id_gudang_ke = '23')
					AND z.qty_oke > 0
					AND z.proccess_date IS NULL
					AND z.kode_trans NOT IN ".getFiledJoint()."
					AND (
							(c.no_ipp LIKE 'BQ-IPP%') 
							OR (a.id_gudang_ke = '23' AND a.category = 'outgoing pusat')  
							OR a.category = 'outgoing subgudang'
						)
					AND (
						z.kode_trans LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.note LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		// $columns_order_by = array(
		// 	0 => 'nomor',
		// 	1 => 'z.kode_trans',
		// 	2 => 'b.so_number',
		// 	3 => 'z.nm_material',
		// 	4 => 'z.nm_material',
		// 	5 => 'a.note'
		// );

		$columns_order_by = array(
			0 => 'nomor',
			1 => 'z.kode_trans',
			2 => 'z.kode_trans',
			3 => 'z.nm_material',
			4 => 'z.nm_material',
			5 => 'a.note'
		);

		$sql .= " ORDER BY z.id DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_so_material_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_so_material_joint(
			$requestData['no_ipp'],
			$requestData['id_milik'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		$GET_MATERIAL = get_detail_material();
		$GET_NO_SO = get_detail_ipp();
		$GET_DETAIL_FD = get_detail_final_drawing();
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_trans'] . "</div>";
			$NO_IPP 		= str_replace('BQ-', '', $row['id_bq']);
			$ID_MILIK 		= $row['id_milik'];
			$SALES_ORDER 	= (!empty($row['id_bq'])) ? $row['id_bq'] : $NO_IPP;
			$PRODUCT 		= (!empty($GET_DETAIL_FD[$ID_MILIK]['product'])) ? $GET_DETAIL_FD[$ID_MILIK]['product'] : $ID_MILIK;
			$NO_SPK 		= (!empty($GET_DETAIL_FD[$ID_MILIK]['no_spk'])) ? $GET_DETAIL_FD[$ID_MILIK]['no_spk'] : $ID_MILIK;
			$nestedData[]	= "<div align='center'>" . $SALES_ORDER . "</div>";
			$nestedData[]	= "<div align='center'>" . strtoupper($PRODUCT) . "</div>";
			$nestedData[]	= "<div align='center'>" . $NO_SPK . "</div>";
			$NM_MATERIAL 	= (!empty($GET_MATERIAL[$row['id_material']]['nm_material'])) ? $GET_MATERIAL[$row['id_material']]['nm_material'] : '';
			$nestedData[]	= "<div align='left'>" . $NM_MATERIAL . "</div>";
			// $nestedData[]	= "<div align='left'>".$row['nm_category']."</div>";
			$nestedData[]	= "<div align='left'>" . $row['outgoing_to'] . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y', strtotime($row['approve_date'])) . "</div>";
			$nestedData[]	= "<div align='center'>" . number_format($row['qty_out'], 4) . "
									<input type='hidden' name='spkfield_" . $row['id'] . "' class='form-control text-center qty_spk input-sm' value='" . $row['qty_out'] . "'>
									<input type='hidden' name='ippfield_" . $row['id'] . "' value='" . $row['id_bq'] . "'>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check2[$nomor]' class='chk_personal chk_joint' data-nomor='$nomor' data-no_ipp='" . $row['id_bq'] . "' data-qty='" . $row['qty_out'] . "' value='" . $row['id'] . "' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_so_material_joint($no_ipp, $id_milik, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		// $sql = "
		// 	SELECT
		// 		(@row:=@row+1) AS nomor,
		// 		a.*,
		// 		UPPER(z.note) AS outgoing_to,
		// 		b.so_number,
		// 		UPPER(y.nm_material) AS nm_material,
		// 		UPPER(y.nm_category) AS nm_category
		// 	FROM
		// 		request_outgoing a
		// 		LEFT JOIN warehouse_adjustment z ON a.kode_trans = z.kode_trans
		// 		LEFT JOIN so_number b ON a.id_bq = b.id_bq
		// 		LEFT JOIN raw_materials y ON a.id_material = y.id_material,
		// 		(SELECT @row:=0) r
		//     WHERE 1=1 
		// 		AND a.kode_delivery IS NULL
		// 		AND a.kode_trans IS NOT NULL
		// 		AND (
		// 			a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR a.id_material LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR y.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR y.nm_category LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 			OR z.note LIKE '%".$this->db->escape_like_str($like_value)."%'
		// 		)
		// ";
		$WHERE_NO_SPK = "";
		$WHERE_NO_SO = "";

		if ($no_ipp != '0') {
			$WHERE_NO_SO = "AND a.id_bq = '" . $no_ipp . "'";
		}
		if ($id_milik != '0') {
			$WHERE_NO_SPK = "AND a.id_milik = '" . $id_milik . "'";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.so_number,
				UPPER(z.note) AS outgoing_to
			FROM
				request_outgoing a
				LEFT JOIN so_number b ON a.id_bq = b.id_bq
				LEFT JOIN warehouse_adjustment z ON a.kode_trans = z.kode_trans,
				(SELECT @row:=0) r
		    WHERE 1=1 " . $WHERE_NO_SO . " " . $WHERE_NO_SPK . " AND a.qty_out > 0
				AND a.kode_delivery IS NULL
				AND a.kode_trans IS NOT NULL
				AND a.kode_trans NOT IN ".getFiledJoint()."
				AND (
					a.kode_trans LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_bq LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_material LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.note LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		// $columns_order_by = array(
		// 	0 => 'nomor',
		// 	1 => 'a.kode_trans',
		// 	2 => 'b.so_number',
		// 	3 => 'y.nm_material',
		// 	4 => 'y.nm_category',
		// 	5 => 'z.note',
		// 	6 => 'a.approve_date',
		// 	7 => 'a.qty_out'
		// );

		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.kode_trans',
			2 => 'a.id_bq',
			3 => 'a.id_material',
			4 => 'a.id_material',
			5 => 'z.note',
			6 => 'a.approve_date',
			7 => 'a.qty_out'
		);

		$sql .= " ORDER BY a.approve_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_material()
	{
		$data 		= $this->input->post();

		$check 			= (!empty($data['check'])) ? $data['check'] : array();
		$check2 		= (!empty($data['check2'])) ? $data['check2'] : array();
		$kode_delivery 	= $data['kode_delivery'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		// print_r($data);
		// exit;
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		// echo $kode_delivery.'<br><br>';
		$ArrInsert = [];
		$ArrUpdate = [];
		$ArrInsert2 = [];
		$ArrUpdate2 = [];
		if (!empty($check)) {
			$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', 'material')->get()->result_array();
			if (!empty($get_detail_produksi)) {
				foreach ($get_detail_produksi as $key => $value) {
					$ID_UNIQ 	= $value['id_uniq'];
					$QTY		= $value['qty'];
					if ($QTY > 0) {
						$GET_DETAIL 	= $this->db->get_where('warehouse_adjustment_detail', array('id' => $ID_UNIQ))->result();
						$KODE_TRANS 	= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->no_ipp : 0;
						$ID_PO_DET 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->id_po_detail : 0;
						$ID_MATERIAL 	= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->id_material : 0;

						$ArrInsert[$key]['kode_delivery'] 	= $kode_delivery;
						$ArrInsert[$key]['id_uniq'] 		= $ID_UNIQ;
						$ArrInsert[$key]['id_pro'] 			= $ID_PO_DET;
						$ArrInsert[$key]['product'] 		= $ID_MATERIAL;
						$ArrInsert[$key]['berat'] 			= $QTY;
						$ArrInsert[$key]['id_milik'] 		= get_name('warehouse_adjustment_detail', 'id_po_detail', 'id', $ID_PO_DET);
						$id_bq = get_name('warehouse_adjustment', 'no_ipp', 'kode_trans', $KODE_TRANS);
						$ArrInsert[$key]['id_produksi'] 	= str_replace('BQ-', 'PRO-', $id_bq);
						$ArrInsert[$key]['sts_product'] 	= 'so material';
						$ArrInsert[$key]['no_drawing'] 		= get_name('so_number', 'so_number', 'id_bq', $id_bq);
						$ArrInsert[$key]['updated_by'] 		= $username;
						$ArrInsert[$key]['updated_date'] 	= $datetime;
						// agus
						// cari nilai di jurnal
						$nilai_cogs=0;
						if($ID_MATERIAL!=0){
							$dtJurnal=$this->db->query("select total_nilai from jurnal where kode_trans='".$GET_DETAIL[0]->kode_trans."' and id_material='".$ID_MATERIAL."' limit 1")->row();
							if(!empty($dtJurnal)) $nilai_cogs=$dtJurnal->total_nilai;
							$ArrInsert[$key]['nilai_cogs']	= $nilai_cogs;							
						}
						$ArrUpdate[$key]['id'] 				= $ID_UNIQ;
						$ArrUpdate[$key]['lot_number'] 		= $kode_delivery;
						$ArrUpdate[$key]['proccess_by'] 	= $username;
						$ArrUpdate[$key]['proccess_date'] 	= $datetime;

						//
					}
				}
			}
		}

		$list_ipp = array_column($ArrInsert, 'id_produksi');
		if (!empty($check2)) {
			$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', 'field')->get()->result_array();
			if (!empty($get_detail_produksi)) {
				foreach ($get_detail_produksi as $key => $value) {
					$ID_UNIQ 	= $value['id_uniq'];
					$QTY		= $value['qty'];
					$ID_BQ		= $value['no_ipp'];
					if ($QTY > 0) {
						$GET_DETAIL 	= $this->db->get_where('request_outgoing', array('id' => $ID_UNIQ))->result();
						$ID_MATERIAL 	= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->id_material : 0;
						$ID_MILIK 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->id_milik : 0;

						$ArrInsert2[$key]['kode_delivery'] 	= $kode_delivery;
						$ArrInsert2[$key]['id_uniq'] 		= $ID_UNIQ;
						$ArrInsert2[$key]['id_pro'] 		= $ID_UNIQ;
						$ArrInsert2[$key]['product'] 		= $ID_MATERIAL;
						$ArrInsert2[$key]['berat'] 			= $QTY;
						$ArrInsert2[$key]['id_milik'] 		= $ID_MILIK;
						$ArrInsert2[$key]['id_produksi'] 	= str_replace('BQ-', 'PRO-', $ID_BQ);
						$ArrInsert2[$key]['sts_product'] 	= 'so material';
						$ArrInsert2[$key]['product_code'] 	= 'field joint';
						$ArrInsert2[$key]['no_drawing'] 	= get_name('so_number', 'so_number', 'id_bq', $ID_BQ);
						$ArrInsert2[$key]['updated_by'] 	= $username;
						$ArrInsert2[$key]['updated_date'] 	= $datetime;
						// agus
						// cari nilai di jurnal
						$nilai_cogs=0;
						if($ID_MATERIAL!=0){
							$dtJurnal=$this->db->query("select total_nilai from jurnal where kode_trans='".$GET_DETAIL[0]->kode_trans."' and id_material='".$ID_MATERIAL."' limit 1")->row();
							if(!empty($dtJurnal)) $nilai_cogs=$dtJurnal->total_nilai;
							$ArrInsert2[$key]['nilai_cogs']	= $nilai_cogs;							
						}
						$ArrUpdate2[$key]['id'] 			= $ID_UNIQ;
						$ArrUpdate2[$key]['kode_delivery'] 	= $kode_delivery;
						$ArrUpdate2[$key]['proccess_by'] 	= $username;
						$ArrUpdate2[$key]['proccess_date'] 	= $datetime;
					}
				}
			}
		}

		// print_r($ArrInsert2);
		// print_r($ArrUpdate2);
		// exit;
		$this->db->trans_start();
		$this->insert_delivery($kode_delivery, $list_ipp);
		if (!empty($ArrInsert)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert);
		}
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdate, 'id');
		}
		if (!empty($ArrInsert2)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert2);
		}
		if (!empty($ArrUpdate2)) {
			$this->db->update_batch('request_outgoing', $ArrUpdate2, 'id');
		}

		$this->db->where('created_by', $username);
		$this->db->where('category', 'material');
		$this->db->delete('delivery_temp');

		$this->db->where('created_by', $username);
		$this->db->where('category', 'field');
		$this->db->delete('delivery_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	public function changeDeliveryTemp()
	{
		$username 	= $this->session->userdata['ORI_User']['username'];
		$id_milik 	= $this->input->post('id_milik');
		$no_ipp 	= $this->input->post('no_ipp');
		$qty 		= str_replace(',', '', $this->input->post('qty'));
		$category 	= $this->input->post('category');
		$checked 	= $this->input->post('check');

		$ArrData = [
			'id_uniq' => $id_milik,
			'category' => $category,
			'no_ipp' => $no_ipp,
			'qty' => $qty,
			'created_by' => $username
		];
		// print_r($ArrData);
		//CHECK
		$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $id_milik, 'category' => $category))->result();

		$this->db->trans_start();
		if ($checked == 'true') {
			if (empty($CHECK)) {
				$this->db->insert('delivery_temp', $ArrData);
			} else {
				$this->db->where('id_uniq', $id_milik);
				$this->db->update('delivery_temp', $ArrData);
			}
		} else {
			$this->db->where('id_uniq', $id_milik);
			$this->db->delete('delivery_temp');
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'	=> 'Failed !!!',
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'	=> 'Success !!!',
			);
		}
		echo json_encode($Arr_Kembali);
	}

	public function modal_edit_surat_jalan()
	{
		if ($this->input->post()) {
			$data_session	= $this->session->userdata;
			$data	= $this->input->post();

			$no_po 		= $data['no_po'];
			$detail 	= $data['detail'];
			if (!empty($data['detail_po'])) {
				$detail_po 	= $data['detail_po'];
			}

			$ArrHeader = array(
				'incoterms' 	=> strtolower($data['incoterms']),
				'request_date' 	=> date('Y-m-d', strtotime($data['request_date'])),
				'tax' 			=> str_replace(',', '', $data['tax']),
				'top' 			=> strtolower($data['top']),
				'remarks' 		=> strtolower($data['remarks']),
				'buyer' 		=> strtolower($data['buyer']),
				'mata_uang' 	=> $data['current'],
				'updated_by' 	=> $data_session['ORI_User']['username'],
				'updated_date' 	=> date('Y-m-d H:i:s')
			);

			$ArrEdit = array();
			foreach ($detail as $val => $valx) {
				$ArrEdit[$val]['id'] = $valx['id'];
				$ArrEdit[$val]['qty_po'] = $valx['qty'];
			}

			$ArrEditPO = array();
			$no = 0;
			if (!empty($data['detail_po'])) {
				foreach ($detail_po as $val => $valx) {
					$no++;
					if (!empty($valx['progress'])) {
						$ArrEditPO[$val]['no_po'] 		= $no_po;
						$ArrEditPO[$val]['category'] 	= 'pembelian material';
						$ArrEditPO[$val]['term'] 		= $no;
						$ArrEditPO[$val]['group_top'] 	= $valx['group_top'];
						$ArrEditPO[$val]['progress'] 	= str_replace(',', '', $valx['progress']);
						$ArrEditPO[$val]['value_usd'] 	= str_replace(',', '', $valx['value_usd']);
						$ArrEditPO[$val]['value_idr'] 	= str_replace(',', '', $valx['value_idr']);
						$ArrEditPO[$val]['keterangan'] 	= strtolower($valx['keterangan']);
						$ArrEditPO[$val]['jatuh_tempo'] = $valx['jatuh_tempo'];
						$ArrEditPO[$val]['syarat'] 		= strtolower($valx['syarat']);
						$ArrEditPO[$val]['created_by'] 	= $data_session['ORI_User']['username'];
						$ArrEditPO[$val]['created_date'] = date('Y-m-d H:i:s');
					}
				}
			}

			$hist_top 		= $this->db->query("SELECT * FROM billing_top WHERE no_po='" . $no_po . "'")->result_array();
			$ArrEditPOHist 	= array();
			if (!empty($hist_top)) {
				foreach ($hist_top as $val => $valx) {
					$ArrEditPOHist[$val]['no_po'] 		= $valx['no_po'];
					$ArrEditPOHist[$val]['category'] 	= $valx['category'];
					$ArrEditPOHist[$val]['term'] 		= $valx['term'];
					$ArrEditPOHist[$val]['progress'] 	= $valx['progress'];
					$ArrEditPOHist[$val]['value_usd'] 	= $valx['value_usd'];
					$ArrEditPOHist[$val]['value_idr'] 	= $valx['value_idr'];
					$ArrEditPOHist[$val]['keterangan'] 	= $valx['keterangan'];
					$ArrEditPOHist[$val]['jatuh_tempo'] = $valx['jatuh_tempo'];
					$ArrEditPOHist[$val]['syarat'] 		= $valx['syarat'];
					$ArrEditPOHist[$val]['created_by'] 	= $valx['created_by'];
					$ArrEditPOHist[$val]['created_date'] = $valx['created_date'];
					$ArrEditPOHist[$val]['hist_by'] 	= $data_session['ORI_User']['username'];
					$ArrEditPOHist[$val]['hist_date']	= date('Y-m-d H:i:s');
				}
			}

			// print_r($ArrHeader);
			// print_r($ArrEdit);
			// print_r($ArrEditPO);
			// print_r($ArrEditPOHist);
			exit;

			$this->db->trans_start();
			$this->db->where('no_po', $data['no_po']);
			$this->db->update('tran_material_po_header', $ArrHeader);

			// $this->db->update_batch('tran_material_po_detail', $ArrEdit, 'id');

			$this->db->where('no_po', $data['no_po']);
			$this->db->delete('billing_top');

			if (!empty($ArrEditPO)) {
				$this->db->insert_batch('billing_top', $ArrEditPO);
			}

			if (!empty($ArrEditPOHist)) {
				$this->db->insert_batch('hist_billing_top', $ArrEditPOHist);
			}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=> 'Save data failed. Please try again later ...',
					'status'	=> 0
				);
			} else {
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=> 'Save data success. Thanks ...',
					'status'	=> 1
				);
				history('Edit PO custom TOP : ' . $data['no_po']);
			}
			echo json_encode($Arr_Data);
		} else {
			$kode_delivery 		= $this->uri->segment(3);
			$result		= $this->db->get_where('tran_material_po_header', array('no_po' => $no_po))->result();
			$data_kurs 	= $this->db->limit(1)->get_where('kurs', array('kode_dari' => 'USD'))->result();
			$get_RFQ = get_name('tran_material_rfq_detail', 'no_rfq', 'no_po', $no_po);
			$result_RFQ	= $this->db->get_where('tran_material_rfq_header', array('no_rfq' => $get_RFQ))->result();

			$sql_detail = "SELECT a.*, b.nm_supplier FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='" . $no_po . "'";

			if ($result[0]->status != 'DELETED') {
				$sql_detail = "SELECT a.*, b.nm_supplier FROM tran_material_po_detail a LEFT JOIN tran_material_po_header b ON a.no_po=b.no_po WHERE a.no_po='" . $no_po . "' AND a.deleted='N'";
			}
			$result_det		= $this->db->query($sql_detail)->result_array();

			$data_top		= $this->db->get_where('billing_top', array('no_po' => $no_po))->result_array();

			$payment = $this->db->get_where('list_help', array('group_by' => 'top'))->result_array();

			$data = array(
				'kode_delivery' 		=> $kode_delivery,
				'data_rfq' 	=> $result_RFQ,
				'data_kurs' => $data_kurs,
				'data_top' => $data_top,
				'payment' => $payment,
				'result' => $result_det
			);

			$this->load->view('Delivery/modal_edit_surat_jalan', $data);
		}
	}

	public function delivery_xls($kode_delivery)
	{
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];

		$data_url		= base_url();
		$Split_Beda		= explode('/', $data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result1 = $this->db->order_by('a.id', 'asc')->group_by('a.id_milik')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => NULL))->result_array();
		$result2 = $this->db->order_by('a.id', 'asc')->group_by('a.id_uniq')->select('COUNT(a.id_milik) AS qty_product, a.*')->where('(berat > 0 OR berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$result = array_merge($result1, $result2);

		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'result'		=> $result,
			'kode_delivery'	=> $kode_delivery,
			'GET_DESC_DEAL'	=> get_descDealSO(),
			'GET_ID_MILIK'	=> get_idMilikSODeal(),
		);
		/*		$this->load->view('Print/print_delivery_xls',$data);
*/
		$dataxls = $this->load->view('Print/print_delivery_xls', $data, TRUE);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Surat-Jalan-' . $kode_delivery . '.xls"');
		header('Cache-Control: max-age=0');
		echo $dataxls;
		die();
	}

	//DELIVERY ADD MATERIAL
	public function add_field_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$username 		= $this->session->userdata['ORI_User']['username'];

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";
		$data_spool = $this->db->query(" SELECT
											a.kode_delivery
										FROM
											delivery_product a 
											LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
										WHERE
											a.lock_delivery_date IS NULL " . $where2 . "
											GROUP BY a.kode_delivery
										ORDER BY
											a.kode_delivery ASC")->result_array();

		$this->db->where('created_by', $username);
		$this->db->where('category', 'field joint');
		$this->db->delete('delivery_temp');

		$data = array(
			'title'			=> 'Add Delivery (Field Joint)',
			'action'		=> 'index',
			'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add_field_joint', $data);
	}

	public function server_side_field_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_field_joint(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['id'], 'category' => 'field joint'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['so_number'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='center'>" . strtoupper($row['product']) . "</div>";
			$nestedData[]	= "<div align='center'>" . spec_bq2($row['id_milik']) . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['qty'] . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y', strtotime($row['qc_date'])) . "
									<input type='hidden' name='spk_" . $row['id'] . "' class='form-control text-center qty_spk input-sm' value='" . $row['qty'] . "'>
									<input type='hidden' name='ipp_" . $row['id'] . "' value='" . $row['no_ipp'] . "'>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' $checked class='chk_personal  chk_material' data-nomor='$nomor' data-qty='" . $row['qty'] . "' data-no_ipp='" . $row['no_ipp'] . "' value='" . $row['id'] . "' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_field_joint($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					b.so_number,
					c.id_category AS product
				FROM
					outgoing_field_joint a
					LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp) = b.id_bq
					LEFT JOIN so_detail_header c ON a.id_milik = c.id,
					(SELECT @row:=0) r
				WHERE 1=1 
					AND a.qty > 0
					AND a.kode_delivery IS NULL
					AND a.qc_date IS NOT NULL
					AND (
						a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
						OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();

		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.so_number',
			2 => 'a.no_spk',
			3 => 'a.id_milik',
			4 => 'a.id_milik',
			5 => 'a.qty',
			6 => 'a.qc_date'
		);

		$sql .= " ORDER BY a.id DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_field_joint()
	{
		$data 		= $this->input->post();

		$check 			= (!empty($data['check'])) ? $data['check'] : array();
		$kode_delivery 	= $data['kode_delivery'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		// print_r($data);
		// exit;
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		// echo $kode_delivery.'<br><br>';
		$ArrInsert = [];
		$ArrUpdate = [];
		if (!empty($check)) {
			$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', 'field joint')->get()->result_array();
			if (!empty($get_detail_produksi)) {
				foreach ($get_detail_produksi as $key => $value) {
					$ID_UNIQ 	= $value['id_uniq'];
					$QTY		= $value['qty'];
					if ($QTY > 0) {
						$GET_DETAIL 	= $this->db->get_where('outgoing_field_joint', array('id' => $ID_UNIQ))->result();
						$NO_IPP 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->no_ipp : 0;
						$NO_BQ 			= 'BQ-'.$NO_IPP;
						$ID_MILIK 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->id_milik : 0;
						$NO_SPK 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]->no_spk : 0;

						$GET_NM_PRODUCT = $this->db->get_where('so_detail_header', array('id' => $ID_MILIK))->result();
						$NM_PRODUCT 	= (!empty($GET_NM_PRODUCT[0]->id_category)) ? $GET_NM_PRODUCT[0]->id_category : 'field joint';

						$ArrInsert[$key]['kode_delivery'] 	= $kode_delivery;
						$ArrInsert[$key]['id_uniq'] 		= $ID_UNIQ;
						$ArrInsert[$key]['id_pro'] 			= $ID_UNIQ;
						$ArrInsert[$key]['product'] 		= $NM_PRODUCT;
						$ArrInsert[$key]['berat'] 			= $QTY;
						$ArrInsert[$key]['id_milik'] 		= $ID_MILIK;
						$ArrInsert[$key]['no_spk'] 			= $NO_SPK;
						$ArrInsert[$key]['id_produksi'] 	= 'PRO-' . $NO_IPP;
						$ArrInsert[$key]['sts_product'] 	= 'field joint';
						$ArrInsert[$key]['sts'] 			= 'field joint';
						$ArrInsert[$key]['no_drawing']      = get_name('so_number', 'so_number', 'id_bq', $NO_BQ);
						$ArrInsert[$key]['updated_by'] 		= $username;
						$ArrInsert[$key]['updated_date'] 	= $datetime;
						// agus
						// cari nilai di jurnal
						$nilai_cogs=0;
						if($ID_MILIK!=0){
							$dtJurnal=$this->db->query("select sum(total_nilai) ttl_nilai from jurnal where kode_trans='".$GET_DETAIL[0]->kode_trans."'")->row();
							if(!empty($dtJurnal)) $nilai_cogs=$dtJurnal->ttl_nilai;
							$ArrInsert[$key]['nilai_cogs']	= $nilai_cogs;							
						}
						$ArrUpdate[$key]['id'] 				= $ID_UNIQ;
						$ArrUpdate[$key]['kode_delivery'] 	= $kode_delivery;
						$ArrUpdate[$key]['delivery_by'] 	= $username;
						$ArrUpdate[$key]['delivery_date'] 	= $datetime;
					}
				}
			}
		}

		// print_r($ArrInsert);
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
		$this->insert_delivery($kode_delivery);
		if (!empty($ArrInsert)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert);
		}
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('outgoing_field_joint', $ArrUpdate, 'id');
		}

		$this->db->where('created_by', $username);
		$this->db->where('category', 'field joint');
		$this->db->delete('delivery_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery field joint ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	//DELIVERY ADD DEADSTOK
	public function add_deadstok()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$username 		= $this->session->userdata['ORI_User']['username'];

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$where2 = " AND (b.id_produksi NOT IN " . filter_not_in() . " OR b.id_produksi IS NULL) ";
		$data_spool = $this->db->query(" SELECT
											a.kode_delivery
										FROM
											delivery_product a 
											LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
										WHERE
											a.lock_delivery_date IS NULL " . $where2 . "
											GROUP BY a.kode_delivery
										ORDER BY
											a.kode_delivery ASC")->result_array();

		$this->db->where('created_by', $username);
		$this->db->where('category', 'deadstok');
		$this->db->delete('delivery_temp');

		$data = array(
			'title'			=> 'Add Delivery (Deadstok)',
			'action'		=> 'index',
			'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add_deadstok', $data);
	}

	public function server_side_deadstok()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_deadstok(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['id_product'], 'category' => 'deadstock'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name'].", ".$row['type_std']." ".$row['resin']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']." x ".number_format($row['length'])."</div>";
			$nestedData[]	= "<div align='center' class='qty_stock'>".number_format($row['qty_stock'])."</div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk_" . $row['id_product'] . "' style='width:80px;' class='form-control text-center qty_delivery input-md numberOnly0'><script>$('.numberOnly0').autoNumeric('init', {mDec: '0', aPad: false});</script>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' $checked class='chk_personal  chk_material' data-nomor='$nomor' value='".$row['id_product']."' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_deadstok($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(qty) AS qty_stock
				FROM
					deadstok a,
					(SELECT @row:=0) r
				WHERE 
					a.deleted_date IS NULL
					AND a.kode_delivery IS NULL
					AND a.qc_date IS NOT NULL
					AND a.id_booking IS NOT NULL
					AND a.process_next = '1'
				AND(
					a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.type_std LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.resin LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.id_product, a.id_milik
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();

		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_so',
			2 => 'no_spk',
			3 => 'product_name',
			4 => 'product_spec'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_deadstok_modif()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_deadstok_modif(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['kode'], 'category' => 'deadstock'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']."</div>";
			$nestedData[]	= "<div align='center' class='qty_stock'>".number_format($row['qty_stock'])."</div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk_" . $row['kode'] . "' style='width:80px;' class='form-control text-center qty_delivery input-md numberOnly0'><script>$('.numberOnly0').autoNumeric('init', {mDec: '0', aPad: false});</script>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' $checked class='chk_personal  chk_material' data-nomor='$nomor' value='".$row['kode']."' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_deadstok_modif($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(z.id) AS qty_stock,
					z.kode
				FROM
					deadstok_modif z
					LEFT JOIN deadstok a ON z.id_deadstok = a.id,
					(SELECT @row:=0) r
				WHERE 
					z.deleted_date IS NULL
					AND z.kode_delivery IS NULL
					AND z.qc_date IS NOT NULL
				AND(
					a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.type_std LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.resin LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY z.kode
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();

		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_so',
			2 => 'no_spk',
			3 => 'product_name',
			4 => 'product_spec'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_deadstok()
	{
		$data 		= $this->input->post();

		$check 			= (!empty($data['check'])) ? $data['check'] : array();
		$kode_delivery 	= $data['kode_delivery'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		// print_r($data);
		// exit;
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		// echo $kode_delivery.'<br><br>';
		$ArrInsert = [];
		$ArrUpdate = [];
		$ArrUpdate2 = [];
		if (!empty($check)) {
			$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', 'deadstok')->get()->result_array();
			if (!empty($get_detail_produksi)) {
				foreach ($get_detail_produksi as $key => $value) {
					$ID_UNIQ 	= $value['id_uniq'];
					$QTY		= $value['qty'];

					$TANDA = substr($ID_UNIQ,0,3);
					if ($QTY > 0) {
						if($TANDA != 'DMF'){
							$GET_DETAIL 	= $this->db->limit($QTY)->get_where('deadstok', array('id_product'=>$ID_UNIQ,'kode_delivery'=>NULL,'deleted_date'=>NULL,'process_next'=>'1'))->result_array();
							$PRODUCT_NAME 	= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['product_name'].', '.$GET_DETAIL[0]['type_std'].' '.$GET_DETAIL[0]['resin'] : 0;
							$product_spec 	= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['product_spec'] : 0;
							$no_spk 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['no_spk'] : 0;
							$length 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['length'] : 0;
							$no_so 			= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['no_so'] : 0;
							$no_ipp 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['no_ipp'] : 0;

							if(!empty($GET_DETAIL)){
								foreach ($GET_DETAIL as $key2 => $value2) {
									$UNIQ = $key.'-'.$key2;
									$ArrUpdate[$UNIQ]['id'] 			= $value2['id'];
									$ArrUpdate[$UNIQ]['kode_delivery'] 	= $kode_delivery;
									$ArrUpdate[$UNIQ]['delivery_by'] 	= $username;
									$ArrUpdate[$UNIQ]['delivery_date'] 	= $datetime;

									$ArrInsert[$UNIQ]['kode_delivery'] 	= $kode_delivery;
									$ArrInsert[$UNIQ]['id_uniq'] 		= $value2['id'];
									$ArrInsert[$UNIQ]['id_pro'] 		= $ID_UNIQ;
									$ArrInsert[$UNIQ]['product'] 		= $PRODUCT_NAME;
									$ArrInsert[$UNIQ]['berat'] 			= $QTY;
									$ArrInsert[$UNIQ]['id_milik'] 		= $ID_UNIQ;
									$ArrInsert[$UNIQ]['no_spk'] 		= $no_spk;
									$ArrInsert[$UNIQ]['spool_induk'] 	= NULL;
									$ArrInsert[$UNIQ]['kode_spool'] 	= NULL;
									$ArrInsert[$UNIQ]['kode_spk'] 		= $product_spec;
									$ArrInsert[$UNIQ]['product_code'] 	= $no_so;
									$ArrInsert[$UNIQ]['length'] 		= $length;
									$ArrInsert[$UNIQ]['id_produksi'] 	= $no_ipp;
									$ArrInsert[$UNIQ]['sts'] 			= 'loose_dead';
									$ArrInsert[$UNIQ]['sts_product'] 	= 'deadstok';
									$ArrInsert[$UNIQ]['no_drawing']     = NULL;
									$ArrInsert[$UNIQ]['updated_by'] 	= $username;
									$ArrInsert[$UNIQ]['updated_date'] 	= $datetime;
								}
							}
						}
						else{
							$GET_DETAIL 	= $this->db->limit($QTY)->get_where('deadstok_modif', array('kode'=>$ID_UNIQ,'kode_delivery'=>NULL,'deleted_date'=>NULL))->result_array();
							
							if(!empty($GET_DETAIL)){
								foreach ($GET_DETAIL as $key2 => $value2) {
									$GET_DEADSTOK 	= $this->db->get_where('deadstok', array('id'=>$value2['id_deadstok']))->result_array();
									$PRODUCT_NAME 	= (!empty($GET_DEADSTOK)) ? $GET_DEADSTOK[0]['product_name'] : 0;
									$product_spec 	= (!empty($GET_DEADSTOK)) ? ', '.$GET_DEADSTOK[0]['product_spec'] : 0;
									$no_spk 		= (!empty($GET_DEADSTOK)) ? $GET_DEADSTOK[0]['no_spk'] : 0;
									$length 		= (!empty($GET_DEADSTOK)) ? $GET_DEADSTOK[0]['length'] : 0;
									$no_so 			= (!empty($GET_DEADSTOK)) ? $GET_DEADSTOK[0]['no_so'] : 0;
									$no_ipp 		= (!empty($GET_DEADSTOK)) ? $GET_DEADSTOK[0]['no_ipp'] : 0;

									$UNIQ = $key.'--'.$key2;
									$ArrUpdate2[$UNIQ]['id'] 			= $value2['id'];
									$ArrUpdate2[$UNIQ]['kode_delivery'] 	= $kode_delivery;
									$ArrUpdate2[$UNIQ]['delivery_by'] 	= $username;
									$ArrUpdate2[$UNIQ]['delivery_date'] 	= $datetime;

									$ArrInsert[$UNIQ]['kode_delivery'] 	= $kode_delivery;
									$ArrInsert[$UNIQ]['id_uniq'] 		= $value2['id'];
									$ArrInsert[$UNIQ]['id_pro'] 		= $value2['id'];
									$ArrInsert[$UNIQ]['product'] 		= $PRODUCT_NAME.$product_spec;
									$ArrInsert[$UNIQ]['berat'] 			= $QTY;
									$ArrInsert[$UNIQ]['id_milik'] 		= $value2['id_deadstok'];
									$ArrInsert[$UNIQ]['no_spk'] 		= $no_spk;
									$ArrInsert[$UNIQ]['spool_induk'] 	= NULL;
									$ArrInsert[$UNIQ]['kode_spool'] 	= NULL;
									$ArrInsert[$UNIQ]['kode_spk'] 		= $ID_UNIQ;
									$ArrInsert[$UNIQ]['product_code'] 	= $no_so;
									$ArrInsert[$UNIQ]['length'] 		= $length;
									$ArrInsert[$UNIQ]['id_produksi'] 	= $no_ipp;
									$ArrInsert[$UNIQ]['sts'] 			= 'loose_dead_modif';
									$ArrInsert[$UNIQ]['sts_product'] 	= 'deadstok';
									$ArrInsert[$UNIQ]['no_drawing']     = NULL;
									$ArrInsert[$UNIQ]['updated_by'] 	= $username;
									$ArrInsert[$UNIQ]['updated_date'] 	= $datetime;
								}
							}
						}
						
					}
				}
			}
		}

		// print_r($ArrInsert);
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
		$this->insert_delivery($kode_delivery);
		if (!empty($ArrInsert)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert);
		}
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('deadstok', $ArrUpdate, 'id');
		}

		if (!empty($ArrUpdate2)) {
			$this->db->update_batch('deadstok_modif', $ArrUpdate2, 'id');
		}

		$this->db->where('created_by', $username);
		$this->db->where('category', 'deadstok');
		$this->db->delete('delivery_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery deadstok ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}

	//TRANSIT
	public function cogs()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Gudang COGS',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data delivery cogs');
		$this->load->view('Delivery/cogs', $data);
	}

	public function server_side_cogs()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_cogs(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
		$GET_USERNAME 		= get_detail_user();
		$GET_DET_FD 		= get_detailFinalDrawing();
		$GET_SALES_ORDER 	= get_detail_ipp();
		$tanki_model = $this->tanki_model;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}


			$edit = "";
			$edit_print = "";
			$print = "";
			$print2 = "";
			$excel = "";
			$release = "";
			if (empty($row['lock_delivery_date'])) {
				$edit = "<a href='" . base_url('delivery/edit_delivery/' . $row['kode_delivery']) . "' class='btn btn-sm btn-primary' title='Edit'><i class='fa fa-edit'></i></a>";
				$print = "<a href='" . base_url('delivery/print_delivery/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				$print2 = "<a href='" . base_url('delivery/print_delivery_draft/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-default' title='Print LX'><i class='fa fa-print'></i></a>";
				$excel = "<a href='" . base_url('delivery/delivery_xls/' . $row['kode_delivery']) . "' target='_blank' class='btn btn-sm btn-default' title='Excel'><i class='fa fa-file-excel-o'></i></a>";
				$release = "<button type='button' class='btn btn-sm btn-success lock_spool' data-spool='" . $row['kode_delivery'] . "' title='Lock Delivery'><i class='fa fa-check'></i></button>";
				// $edit_print = "<button type='button' class='btn btn-sm bg-purple edit_print' data-kode_delivery='".$row['kode_delivery']."' title='Edit Surat Jalan'><i class='fa fa-file'></i></button>";
			}
			$view = "<a href='" . base_url('delivery/view_delivery/' . $row['kode_delivery']) . "' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";
			$get_split_ipp1 = $this->db
									->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
									->group_by('a.id_milik, a.sts')
									->order_by('a.spool_induk', 'asc')
									->order_by('a.kode_spool', 'asc')
									->where('(a.berat > 0 OR a.berat IS NULL)')
									->join('production_detail b','a.id_pro=b.id','left')
									->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'a.sts_product' => NULL))->result_array();
			$get_split_ipp2 = $this->db->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where('(a.berat > 0 OR a.berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'sts_product' => 'so material'))->result_array();
			$get_split_ipp3 = $this->db->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where('(a.berat > 0 OR a.berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'sts_product' => 'field joint'))->result_array();
			$get_split_ipp4 = $this->db->select('COUNT(a.berat) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_pro')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where("(a.berat > 0 OR a.berat IS NULL OR a.sts = 'loose_dead')")->get_where('delivery_product_detail a', array('a.kode_delivery' => $row['kode_delivery'], 'sts_product' => 'deadstok'))->result_array();
			$get_split_ipp = array_merge($get_split_ipp1, $get_split_ipp2, $get_split_ipp3, $get_split_ipp4);
			$ArrNo_IPP = [];
			$ArrNo_SPK = [];
			$ArrNo_LS = [];
			$ArrNo_Drawing = [];
			foreach ($get_split_ipp as $key => $value) {
				$key++;
				$no_spk 		= $value['no_spk'];
				$NO_IPP 		= str_replace(['PRO-', 'BQ-'], '', $value['id_produksi']);
				$NO_SO 			= (!empty($GET_SALES_ORDER[$NO_IPP]['so_number'])) ? $GET_SALES_ORDER[$NO_IPP]['so_number'] : '';
				$ArrNo_IPP[]	= $NO_SO;
				if (!empty($value['no_drawing'])) {
					$ArrNo_Drawing[] = $value['no_drawing'];
				}

				$CUTTING_KE = (!empty($value['cutting_ke'])) ? '.' . $value['cutting_ke'] : '';
				$IMPLODE = explode('.', $value['product_code']);
				$ID_PRX_ADD = $IMPLODE[0] . '.' . $value['product_ke'] . $CUTTING_KE . '/' . $no_spk;
				if ($value['sts_product'] == 'so material') {
					if ($value['berat'] > 0) {
						$ID_PRX_ADD = strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
					}
				}

				if ($value['sts_product'] == 'field joint') {
					if ($value['berat'] > 0) {
						$ID_PRX_ADD = strtoupper(get_name('so_number', 'so_number', 'id_bq', str_replace('PRO-', 'BQ-', $value['id_produksi']))) . '/' . $no_spk;
					}
				}

				$series 	= (!empty($GET_DET_FD[$value['id_milik']]['series'])) ? $GET_DET_FD[$value['id_milik']]['series'] : '';
				$product 	= strtoupper($value['product']) . ", " . $series . ", DIA " . spec_bq2($value['id_milik']);
				$SATUAN 	= ' pcs';
				$QTY 		= $value['qty_product'];

				if ($value['sts_product'] == 'deadstok') {
					$ID_PRX_ADD = $value['product_code'].'/'.$value['no_spk'];
					$product 	= strtoupper($value['product']) . ", DIA " . $value['product_code'].' x '.$value['length'];
				}

				if ($value['sts'] == 'loose_dead') {
					$ID_PRX_ADD = $value['product_code'].'/'.$value['no_spk'];
					$product 	= strtoupper($value['product']) . ", DIA " . $value['kode_spk'].' x '.$value['length'];
				}

				if ($value['type_product'] == 'tanki') {
					$spec = $tanki_model->get_spec($value['id_milik']);
					$product 	= strtoupper($value['product_tanki']) . ", " . $spec;
				}

				$ID_MILIK 	= (!empty($GET_ID_MILIK[$value['id_milik']])) ? $GET_ID_MILIK[$value['id_milik']] : '';
				if ($value['sts_product'] == 'so material') {
					$product 	= strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
					$SATUAN 	= ' kg';
					$QTY 		= number_format($value['berat'], 2);
					$ID_MILIK 	= '';
				}

				if ($value['sts_product'] == 'field joint') {
					$SATUAN     = ' kit';
					$QTY         = number_format($value['berat']);
				}

				if ($value['sts_product'] == 'deadstok') {
					$QTY         = number_format($value['qty_product']);
				}

				$ID_PRX = "[<b>" . $QTY . $SATUAN . "</b>][<b>" . $ID_PRX_ADD . "</b>], " . $product;

				$QNOSO = $this->db->get_where('so_number', ['id_bq' => str_replace("PRO", "BQ", $value['id_produksi'])])->row();
				$NOSO = (!empty($QNOSO->so_number))?$QNOSO->so_number:'-';

				//Category
				$loose_spool = (!empty($value['spool_induk'])) ? $value['spool_induk'] . '-' . $value['kode_spool'] : 'LOOSE';
				if ($value['sts_product'] == 'so material') {
					$loose_spool = $NOSO;
				}
				if ($value['sts_product'] == 'field joint') {
					$loose_spool = "FIELD JOINT";
				}
				if ($value['sts'] == 'cut' and empty($value['spool_induk'])) {
					$loose_spool = "LOOSE PIPE CUTTING";
				}
				if ($value['sts_product'] == 'deadstok') {
					$loose_spool = "DEADSTOCK";
				}
				$ArrNo_LS[] = $key . '. ' . $loose_spool;

				$ArrNo_SPK[] = $key . ".<span class='text-bold text-blue'>" . $loose_spool . "</span> " . $ID_PRX;
			}
			// print_r($ArrGroup); exit;
			$explode_ipp 	= implode('<br>', array_unique($ArrNo_IPP));
			$explode_nd 	= implode('<br>', array_unique($ArrNo_Drawing));
			$explode_spk 	= implode('<br>', $ArrNo_SPK);
			// $explode_ls 	= implode('<br>',$ArrNo_LS);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_delivery'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['nomor_sj'] . "</div>";
			// $nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>" . $explode_nd . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			// $nestedData[]	= "<div align='left'>".$explode_ls."</div>";
			$update_by 	= strtolower($row['updated_by']);
			$NM_LENGKAP = (!empty($GET_USERNAME[$update_by]['nm_lengkap'])) ? $GET_USERNAME[$update_by]['nm_lengkap'] : $update_by;
			$nestedData[]	= "<div align='center'>" . strtoupper($NM_LENGKAP) . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i', strtotime($row['updated_date'])) . "</div>";
			// $nestedData[]	= "<div align='left'>
			// 						" . $view . "
			// 						" . $edit . "
			// 						" . $print . "
			// 						" . $print2 . "
			// 						" . $excel . "
			// 						" . $release . "
			// 						" . $edit_print . "
			// 					</div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_cogs($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = " AND b.posisi='CUSTOMER' ";
		$where2 = " AND b.id_produksi NOT IN " . filter_not_in() . " ";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				delivery_product a
				LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery,
				(SELECT @row:=0) r
		    WHERE 1=1 " . $where . " 
				AND a.release_delivery_date IS NULL
				AND b.sts_invoice = 1
				AND (
					a.kode_delivery LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.nomor_sj LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.no_drawing LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.sts_product LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.sts LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.kode_delivery
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_delivery'
		);

		$sql .= " ORDER BY a.updated_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//DELIVERY AKSESORIS
	public function add_aksesoris()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$username 		= $this->session->userdata['ORI_User']['username'];

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$where2 = " AND (b.id_produksi NOT IN " . filter_not_in() . " OR b.id_produksi IS NULL) ";
		$data_spool = $this->db->query(" SELECT
											a.kode_delivery
										FROM
											delivery_product a 
											LEFT JOIN delivery_product_detail b ON a.kode_delivery=b.kode_delivery
										WHERE
											a.lock_delivery_date IS NULL " . $where2 . "
											GROUP BY a.kode_delivery
										ORDER BY
											a.kode_delivery ASC")->result_array();

		$this->db->where('created_by', $username);
		$this->db->where('category', 'aksesoris');
		$this->db->delete('delivery_temp');

		$data = array(
			'title'			=> 'Add Item Project',
			'action'		=> 'index',
			'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Delivery/add_aksesoris', $data);
	}

	public function server_side_aksesoris()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_aksesoris(
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$CHECK = $this->db->get_where('delivery_temp', array('id_uniq' => $row['id_material']."-".$row['no_ipp'], 'category' => 'aksesoris'))->result();
			$checked = (!empty($CHECK)) ? 'checked' : '';
			$QTY = (!empty($CHECK)) ? $CHECK[0]->qty : '';
			$TandaTanki = substr($row['no_ipp'],0,4);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			if($TandaTanki == 'IPPT'){
				$nestedData[]	= "<div align='center'>".$row['so_number_tanki']."</div>";
				$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer_tanki'])."</div>";
				$nestedData[]	= "<div align='left'>".strtoupper($row['project_tanki'])."</div>";
				$id_material_tanki = (!empty($row['id_material_tanki']))?$row['id_material_tanki']:$row['id_material_tanki2'];
				$nestedData[]	= "<div align='left'>".get_name_acc($id_material_tanki)."</div>";
			}
			else{
				$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
				$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
				$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
				$nestedData[]	= "<div align='left'>".get_name_acc($row['id_material'])."</div>";
			}
			
			
			$nestedData[]	= "<div align='center' class='qty_stock'>".number_format($row['qty_fg'],2)."</div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk_".$row['id']."' data-id='".$row['id']."' style='width:80px;' class='form-control text-center qty_delivery input-md numberOnly0' value='".$QTY."'><script>$('.numberOnly0').autoNumeric('init', {mDec: '2', aPad: false});</script>
								</div>";
			// $nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' ".$checked." class='chk_personal  chk_material' data-nomor='$nomor' value='".$row['id']."' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_aksesoris($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id,
				a.kode,
				a.no_ipp,
                a.created_by,
                a.created_date,
				b.so_number,
				x.no_so AS so_number_tanki,
                c.nm_customer,
				x.customer AS nm_customer_tanki,
                c.project,
				x.project AS project_tanki,
                d.id_material,
				z.id AS id_material_tanki,
				z1.id AS id_material_tanki2,
                SUM(a.qty_out-a.qty_delivery) AS qty_fg
			FROM
				request_accessories a
                LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp) = b.id_bq
				LEFT JOIN planning_tanki x ON a.no_ipp=x.no_ipp
                LEFT JOIN production c ON a.no_ipp = c.no_ipp
                LEFT JOIN so_acc_and_mat d ON a.id_milik = d.id
                LEFT JOIN planning_tanki_detail y ON a.id_milik = y.id
                LEFT JOIN accessories e ON d.id_material = e.id
                LEFT JOIN accessories z ON y.id_material = z.id_acc_tanki
                LEFT JOIN accessories z1 ON y.id_material = z1.id,
				(SELECT @row:=0) r
		    WHERE a.deleted_date IS NULL AND a.qty_out > 0
                AND (
                    a.kode LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.project LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR e.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR x.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
            GROUP BY a.id
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.so_number',
			2 => 'c.nm_customer',
			3 => 'c.project',
			4 => 'e.nama',
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_aksesoris()
	{
		$data 		= $this->input->post();

		$kode_delivery 	= $data['kode_delivery'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		// print_r($data);
		// exit;
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if ($kode_delivery == '0') {
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-" . $YM . "%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s', $urutan2);
			$kode_delivery	= "DV-" . $YM . $urut2;
		}

		// echo $kode_delivery.'<br><br>';
		$GET_DET_IPP = get_detail_ipp();
		$ArrInsert = [];
		$ArrUpdate = [];
		$get_detail_produksi = $this->db->select('*')->from('delivery_temp')->where('created_by', $username)->where('category', 'aksesoris')->get()->result_array();
		if (!empty($get_detail_produksi)) {
			foreach ($get_detail_produksi as $key => $value) {
				$ID_UNIQ 	= $value['id_uniq'];
				$QTY		= $value['qty'];
				if ($QTY > 0) {
					$CheckAsal 	= $this->db->get_where('request_accessories',array('id'=>$ID_UNIQ))->result_array();
					$NoIPP 		= $CheckAsal[0]['no_ipp'];
					$CheckTanki = substr($NoIPP,0,4);
					if($CheckTanki == 'IPPT'){
						$GET_DETAIL 	= $this->db
						->select('a.*, c.id as id_material, d.id as id_material2')
						->join('planning_tanki_detail b','a.id_milik=b.id')
						->join('accessories c','b.id_material=c.id_acc_tanki','left')
						->join('accessories d','b.id_material=d.id','left')
						->get_where('request_accessories a', array('a.id'=>$ID_UNIQ))->result_array();
					}
					else{
						$GET_DETAIL 	= $this->db
						->select('a.*, b.id_material')
						->join('so_acc_and_mat b','a.id_milik=b.id')
						->get_where('request_accessories a', array('a.id'=>$ID_UNIQ))->result_array();
					}
					
					$id_milik 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['id_milik'] : 0;
					$id_material 	= (!empty($GET_DETAIL[0]['id_material'])) ? $GET_DETAIL[0]['id_material'] : $GET_DETAIL[0]['id_material2'];
					$no_ipp 		= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['no_ipp'] : 0;
					$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:0;
					$qty_delivery 	= (!empty($GET_DETAIL)) ? $GET_DETAIL[0]['qty_delivery'] : 0;

					if(!empty($GET_DETAIL)){
						foreach ($GET_DETAIL as $key2 => $value2) {
							$UNIQ = $key.'-'.$key2;
							$ArrUpdate[$UNIQ]['id'] 			= $value2['id'];
							$ArrUpdate[$UNIQ]['qty_delivery'] 	= $qty_delivery + $QTY;

							$ArrInsert[$UNIQ]['kode_delivery'] 	= $kode_delivery;
							$ArrInsert[$UNIQ]['id_uniq'] 		= $value2['id'];
							$ArrInsert[$UNIQ]['id_pro'] 		= $ID_UNIQ;
							$ArrInsert[$UNIQ]['product'] 		= $id_material;
							$ArrInsert[$UNIQ]['no_drawing'] 	= get_name_acc($id_material);
							$ArrInsert[$UNIQ]['berat'] 			= $QTY;
							$ArrInsert[$UNIQ]['id_milik'] 		= $id_milik;
							$ArrInsert[$UNIQ]['no_spk'] 		= NULL;
							$ArrInsert[$UNIQ]['spool_induk'] 	= NULL;
							$ArrInsert[$UNIQ]['kode_spool'] 	= NULL;
							$ArrInsert[$UNIQ]['kode_spk'] 		= NULL;
							$ArrInsert[$UNIQ]['product_code'] 	= $no_so;
							$ArrInsert[$UNIQ]['length'] 		= NULL;
							$ArrInsert[$UNIQ]['id_produksi'] 	= $no_ipp;
							$ArrInsert[$UNIQ]['sts'] 			= 'aksesoris';
							$ArrInsert[$UNIQ]['sts_product'] 	= 'aksesoris';
							$ArrInsert[$UNIQ]['updated_by'] 	= $username;
							$ArrInsert[$UNIQ]['updated_date'] 	= $datetime;
						}
					}
					
				}
			}
		}

		// print_r($ArrInsert);
		// print_r($ArrUpdate);
		// exit;
		$this->db->trans_start();
		$this->insert_delivery($kode_delivery);
		if (!empty($ArrInsert)) {
			$this->db->insert_batch('delivery_product_detail', $ArrInsert);
		}
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('request_accessories', $ArrUpdate, 'id');
		}

		$this->db->where('created_by', $username);
		$this->db->where('category', 'aksesoris');
		$this->db->delete('delivery_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_delivery'	=> $kode_delivery
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_delivery'	=> $kode_delivery
			);
			history('Create data delivery aksesoris ' . $kode_delivery);
		}
		echo json_encode($Arr_Kembali);
	}
	
	public function jurnal_delivery(){
		    $CI 	=& get_instance();
		
		    $kodejurnal='JV006';
			
			$username 		= $this->session->userdata['ORI_User']['username'];
		    $datetime 		= date('Y-m-d H:i:s');
		
		    $datajurnal = $CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join view_ms_category_part b on a.product=b.id WHERE a.category='delivery' and a.status_jurnal='1' and a.no_surat_jalan ='DV-240201'" )->result_array();
			
		  foreach($datajurnal as $keys => $values) {
			$id=$values['id_detail'];
			$idmilik =$values['id_milik'];
			$tgl_voucher = $values['tanggal'];
			
			$idjurnal = $values['id'];
			$no_request = $idjurnal;

			$dataproductiondetail=$CI->db->query("select * from production_detail where id='".$id."' and id_milik ='".$idmilik."' limit 1")->row();
			if($dataproductiondetail->finish_good==0){
				$datasodetailheader = $CI->db->query("SELECT * FROM view_bq_detail_detail WHERE id ='".$idmilik."' limit 1" )->row();
				$kurs=1;
				$sqlkurs="select * from ms_kurs where tanggal <='".$tgl_voucher."' and mata_uang='USD' order by tanggal desc limit 1";
				$dtkurs	= $CI->db->query($sqlkurs)->row();
				if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
				$wip_material=$values['total_nilai'];
				$pe_direct_labour=($datasodetailheader->pe_direct_labour*$kurs);
				$foh=(($datasodetailheader->pe_machine + $datasodetailheader->pe_mould_mandrill + $datasodetailheader->pe_foh_depresiasi + $datasodetailheader->pe_biaya_rutin_bulanan + $datasodetailheader->pe_foh_consumable)*$kurs);
				$pe_indirect_labour=($datasodetailheader->pe_indirect_labour*$kurs);
				$pe_consumable=($datasodetailheader->pe_consumable*$kurs);
				$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);

				$CI->db->query("update production_detail set wip_kurs='".$kurs."', wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."', wip_il='".$pe_indirect_labour."', wip_consumable='".$pe_consumable."', finish_good='".$finish_good."' WHERE id='".$id."' and id_milik ='".$idmilik."' limit 1" );
				$totalall=$finish_good;
			}else{
				$totalall=$dataproductiondetail->finish_good;
			}
			$no_spk=$values['id_spk'];
			$ket ='FINISH GOOD - TRANSIT TANKI';
			$Keterangan_INV=($ket).' ('.$values['no_so'].' - '.$values['product'].' - '.$no_spk.' - '.$values['no_surat_jalan'].')';
			$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
			$det_Jurnaltes = [];
			foreach($datajurnal AS $record){
				$tabel  = $record->menu;
				$posisi = $record->posisi;
				$field  = $record->field;
				$nokir  = $record->no_perkiraan;

				$totalall2 = (!empty($totalall))?$totalall:0;
				$param  = 'id';
				if ($posisi=='D'){
					$value_param  = $idjurnal;
					$val = $this->Acc_model->GetData($tabel,$field,$param,$value_param);
					$nilaibayar = $val[0]->$field;
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $Keterangan_INV,
					  'no_reff'       => $no_request,
					  'debet'         => $totalall2,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'finish good intransit',
					  'no_request'    => $no_request,
					  'stspos'		=>1
					 );
				} elseif ($posisi=='K'){
					$coa = 	$CI->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join view_ms_category_part b on a.product=b.id WHERE a.id ='$idjurnal'")->result();
					$nokir=$coa[0]->coa_fg;
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $Keterangan_INV,
					  'no_reff'       => $no_request,
					  'debet'         => 0,
					  'kredit'        => $totalall2,
					  'jenis_jurnal'  => 'finish good intransit',
					  'no_request'    => $no_request,
					  'stspos'		=>1
					 );
				}
			}
			$CI->db->query("delete from jurnaltras WHERE jenis_jurnal='finish good intransit' and no_reff ='$id'");
			$CI->db->insert_batch('jurnaltras',$det_Jurnaltes);
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall2, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.'-'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $username, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$CI->db->insert(DBACC.'.javh',$dataJVhead);
			$datadetail=array();
			foreach ($det_Jurnaltes as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tgl_voucher,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $vals['keterangan'],
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					);
				$CI->db->insert(DBACC.'.jurnal',$datadetail);
			}
			$CI->db->query("UPDATE jurnal_product SET status_jurnal='1',approval_by='".$username."',approval_date='".$datetime."' WHERE id ='$id'");
			unset($det_Jurnaltes);unset($datadetail);
			
			print_r($Nomor_JV);
			print_r($no_request);
			echo"<br>";
			
		  }
		
	}

	public function close_jurnal_in_transit($kode_delivery){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		//GROUP DATA
		$ArrGroup = [];
		$ArrGroupOut = [];
		$ArrIdPro = $this->db->get_where('delivery_product_detail',array('kode_delivery'=>$kode_delivery,'sts'=>'loose','spool_induk'=>NULL))->result_array();
		if(!empty($ArrIdPro)){
			foreach ($ArrIdPro as $value => $valx) {
				$getSummary = $this->db->select('*')->get_where('data_erp_fg',array('id_pro'=>$valx['id_pro']))->result_array();


				$ArrGroup[$value]['tanggal'] = date('Y-m-d');
				$ArrGroup[$value]['keterangan'] = 'Finish Good to In Transit';
				$ArrGroup[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
				$ArrGroup[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
				$ArrGroup[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
				$ArrGroup[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
				$ArrGroup[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
				$ArrGroup[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
				$ArrGroup[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
				$ArrGroup[$value]['created_by'] = $username;
				$ArrGroup[$value]['created_date'] = $datetime;
				$ArrGroup[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
				$ArrGroup[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
				$ArrGroup[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
				$ArrGroup[$value]['kode_delivery'] = $kode_delivery;
				$id_trans         = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:$this->kode_trs;

				$ArrGroupOut[$value]['tanggal'] = date('Y-m-d');
				$ArrGroupOut[$value]['keterangan'] = 'Finish Good to In Transit';
				$ArrGroupOut[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
				$ArrGroupOut[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
				$ArrGroupOut[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
				$ArrGroupOut[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
				$ArrGroupOut[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
				$ArrGroupOut[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
				$ArrGroupOut[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_unit']))?$getSummary[0]['nilai_unit']:0;
				$ArrGroupOut[$value]['nilai_wip'] = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
				$ArrGroupOut[$value]['material'] = (!empty($getSummary[0]['material']))?$getSummary[0]['material']:0;
				$ArrGroupOut[$value]['wip_direct'] = (!empty($getSummary[0]['wip_direct']))?$getSummary[0]['wip_direct']:0;
				$ArrGroupOut[$value]['wip_indirect'] = (!empty($getSummary[0]['wip_indirect']))?$getSummary[0]['wip_indirect']:0;
				$ArrGroupOut[$value]['wip_consumable'] = (!empty($getSummary[0]['wip_consumable']))?$getSummary[0]['wip_consumable']:0;
				$ArrGroupOut[$value]['wip_foh'] = (!empty($getSummary[0]['wip_foh']))?$getSummary[0]['wip_foh']:0;
				$ArrGroupOut[$value]['created_by'] = $username;
				$ArrGroupOut[$value]['created_date'] = $datetime;
				$ArrGroupOut[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
				$ArrGroupOut[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
				$ArrGroupOut[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
				$ArrGroupOut[$value]['kode_delivery'] = $kode_delivery;
				$ArrGroupOut[$value]['jenis'] = 'out';
			}
		}

		$ArrGroupMaterial = [];
		$ArrGroupOutMaterial = [];
		$ListIN = ['so material','field joint','deadstok','cut','cut deadstock'];
		$ArrayDeliveryMaterial = $this->db->where_in('sts_product',$ListIN)->get_where('delivery_product_detail',array('kode_delivery'=>$kode_delivery,'spool_induk'=>NULL))->result_array();
		if(!empty($ArrayDeliveryMaterial)){
			foreach ($ArrayDeliveryMaterial as $value => $valx) {
				if($valx['sts_product'] == 'so material'){
					$getDetOutgoing = $this->db->select('*')->get_where('warehouse_adjustment_detail',array('id'=>$valx['id_uniq']))->result_array();
					$kode_trans 	= (!empty($getDetOutgoing[0]['kode_trans']))?$getDetOutgoing[0]['kode_trans']:0;
					$id_material 	= (!empty($getDetOutgoing[0]['id_material']))?$getDetOutgoing[0]['id_material']:0;

					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('kode_trans'=>$kode_trans,'id_material'=>$id_material))->result_array();
				}

				if($valx['sts_product'] == 'field joint'){
					$getDetOutgoing = $this->db->select('*')->get_where('outgoing_field_joint',array('id'=>$valx['id_uniq']))->result_array();
					$kode_trans 	= (!empty($getDetOutgoing[0]['kode_trans']))?$getDetOutgoing[0]['kode_trans']:0;
					$no_spk 		= (!empty($getDetOutgoing[0]['no_spk']))?$getDetOutgoing[0]['no_spk']:0;

					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('kode_trans'=>$kode_trans,'no_spk'=>$no_spk))->result_array();
				}

				if($valx['sts_product'] == 'deadstok' AND $valx['sts'] != 'loose_dead_modif'){
					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro_det'=>$valx['id_uniq']))->result_array();
				}

				if($valx['sts_product'] == 'cut'){
					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro'=>$valx['id_uniq'],'id_pro_det'=>$valx['id_pro']))->result_array();
				}

				if($valx['sts_product'] == 'cut deadstock'){
					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro'=>$valx['id_uniq'],'jenis'=>'in cutting deadstok'))->result_array();
				}

				if($valx['sts_product'] == 'deadstok' AND $valx['sts'] == 'loose_dead_modif'){
					$GetKodeSPK 	= $this->db->select('*')->get_where('deadstok_modif',array('id'=>$valx['id_uniq']))->result_array();
					$getSummaryMax 	= $this->db->select('*')->order_by('id','desc')->get_where('data_erp_fg',array('id_pro_det'=>$valx['id_milik'],'kode_trans'=>$GetKodeSPK[0]['kode_spk']))->result_array();
					$getSummary 	= $this->db->select('*')->get_where('data_erp_fg',array('id_pro_det'=>$valx['id_milik'],'kode_trans'=>$GetKodeSPK[0]['kode_spk'],'created_date'=>$getSummaryMax[0]['created_date']))->result_array();
					foreach ($getSummary as $key => $value2x) {
						$UNIQ2 = $value.'-'.$key;
						$ArrGroupMaterial[$UNIQ2]['tanggal'] = date('Y-m-d');
						$ArrGroupMaterial[$UNIQ2]['keterangan'] = 'Finish Good to In Transit';
						$ArrGroupMaterial[$UNIQ2]['no_so'] 	= $value2x['no_so'];
						$ArrGroupMaterial[$UNIQ2]['product'] = $value2x['product'];
						$ArrGroupMaterial[$UNIQ2]['no_spk'] = $value2x['no_spk'];
						$ArrGroupMaterial[$UNIQ2]['kode_trans'] = $value2x['kode_trans'];
						$ArrGroupMaterial[$UNIQ2]['id_pro_det'] = $value2x['id_pro_det'];
						$ArrGroupMaterial[$UNIQ2]['qty'] = $value2x['qty'];
						$ArrGroupMaterial[$UNIQ2]['nilai_unit'] = $value2x['nilai_wip'];
						$ArrGroupMaterial[$UNIQ2]['created_by'] = $username;
						$ArrGroupMaterial[$UNIQ2]['created_date'] = $datetime;
						$ArrGroupMaterial[$UNIQ2]['id_trans'] = $value2x['id_trans'];
						$ArrGroupMaterial[$UNIQ2]['id_pro'] = $value2x['id_pro'];
						$ArrGroupMaterial[$UNIQ2]['qty_ke'] = $value2x['qty_ke'];
						$ArrGroupMaterial[$UNIQ2]['kode_delivery'] = $kode_delivery;
						$ArrGroupMaterial[$UNIQ2]['id_material'] = $value2x['id_material'];
						$ArrGroupMaterial[$UNIQ2]['nm_material'] = $value2x['nm_material'];
						$ArrGroupMaterial[$UNIQ2]['qty_mat'] = $value2x['qty_mat'];
						$ArrGroupMaterial[$UNIQ2]['cost_book'] = $value2x['cost_book'];
						$ArrGroupMaterial[$UNIQ2]['gudang'] = $value2x['gudang'];

						$ArrGroupOutMaterial[$UNIQ2]['tanggal'] = date('Y-m-d');
						$ArrGroupOutMaterial[$UNIQ2]['keterangan'] = 'Finish Good to In Transit';
						$ArrGroupOutMaterial[$UNIQ2]['no_so'] 	= $value2x['no_so'];
						$ArrGroupOutMaterial[$UNIQ2]['product'] = $value2x['product'];
						$ArrGroupOutMaterial[$UNIQ2]['no_spk'] = $value2x['no_spk'];
						$ArrGroupOutMaterial[$UNIQ2]['kode_trans'] = $value2x['kode_trans'];
						$ArrGroupOutMaterial[$UNIQ2]['id_pro_det'] = $value2x['id_pro_det'];
						$ArrGroupOutMaterial[$UNIQ2]['qty'] = $value2x['qty'];
						$ArrGroupOutMaterial[$UNIQ2]['nilai_unit'] = $value2x['nilai_unit'];
						$ArrGroupOutMaterial[$UNIQ2]['nilai_wip'] = $value2x['nilai_wip'];
						$ArrGroupOutMaterial[$UNIQ2]['material'] = $value2x['material'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_direct'] = $value2x['wip_direct'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_indirect'] = $value2x['wip_indirect'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_consumable'] = $value2x['wip_consumable'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_foh'] = $value2x['wip_foh'];
						$ArrGroupOutMaterial[$UNIQ2]['created_by'] = $username;
						$ArrGroupOutMaterial[$UNIQ2]['created_date'] = $datetime;
						$ArrGroupOutMaterial[$UNIQ2]['id_trans'] = $value2x['id_trans'];
						$ArrGroupOutMaterial[$UNIQ2]['id_pro'] = $value2x['id_pro'];
						$ArrGroupOutMaterial[$UNIQ2]['qty_ke'] = $value2x['qty_ke'];
						$ArrGroupOutMaterial[$UNIQ2]['kode_delivery'] = $kode_delivery;
						$ArrGroupOutMaterial[$UNIQ2]['jenis'] = 'out';
						$ArrGroupOutMaterial[$UNIQ2]['id_material'] = $value2x['id_material'];
						$ArrGroupOutMaterial[$UNIQ2]['nm_material'] = $value2x['nm_material'];
						$ArrGroupOutMaterial[$UNIQ2]['qty_mat'] = $value2x['qty_mat'];
						$ArrGroupOutMaterial[$UNIQ2]['cost_book'] = $value2x['cost_book'];
						$ArrGroupOutMaterial[$UNIQ2]['gudang'] = $value2x['gudang'];

						$id_trans = $value2x['id_trans'];
					}
				}
				else{
					$ArrGroupMaterial[$value]['tanggal'] = date('Y-m-d');
					$ArrGroupMaterial[$value]['keterangan'] = 'Finish Good to In Transit';
					$ArrGroupMaterial[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroupMaterial[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroupMaterial[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroupMaterial[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
					$ArrGroupMaterial[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
					$ArrGroupMaterial[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
					$ArrGroupMaterial[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
					$ArrGroupMaterial[$value]['created_by'] = $username;
					$ArrGroupMaterial[$value]['created_date'] = $datetime;
					$ArrGroupMaterial[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
					$ArrGroupMaterial[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
					$ArrGroupMaterial[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
					$ArrGroupMaterial[$value]['kode_delivery'] = $kode_delivery;
					$ArrGroupMaterial[$value]['id_material'] = (!empty($getSummary[0]['id_material']))?$getSummary[0]['id_material']:0;
					$ArrGroupMaterial[$value]['nm_material'] = (!empty($getSummary[0]['nm_material']))?$getSummary[0]['nm_material']:0;
					$ArrGroupMaterial[$value]['qty_mat'] = (!empty($getSummary[0]['qty_mat']))?$getSummary[0]['qty_mat']:0;
					$ArrGroupMaterial[$value]['cost_book'] = (!empty($getSummary[0]['cost_book']))?$getSummary[0]['cost_book']:0;
					$ArrGroupMaterial[$value]['gudang'] = (!empty($getSummary[0]['gudang']))?$getSummary[0]['gudang']:0;

					$ArrGroupOutMaterial[$value]['tanggal'] = date('Y-m-d');
					$ArrGroupOutMaterial[$value]['keterangan'] = 'Finish Good to In Transit';
					$ArrGroupOutMaterial[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroupOutMaterial[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroupOutMaterial[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroupOutMaterial[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
					$ArrGroupOutMaterial[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
					$ArrGroupOutMaterial[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
					$ArrGroupOutMaterial[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_unit']))?$getSummary[0]['nilai_unit']:0;
					$ArrGroupOutMaterial[$value]['nilai_wip'] = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
					$ArrGroupOutMaterial[$value]['material'] = (!empty($getSummary[0]['material']))?$getSummary[0]['material']:0;
					$ArrGroupOutMaterial[$value]['wip_direct'] = (!empty($getSummary[0]['wip_direct']))?$getSummary[0]['wip_direct']:0;
					$ArrGroupOutMaterial[$value]['wip_indirect'] = (!empty($getSummary[0]['wip_indirect']))?$getSummary[0]['wip_indirect']:0;
					$ArrGroupOutMaterial[$value]['wip_consumable'] = (!empty($getSummary[0]['wip_consumable']))?$getSummary[0]['wip_consumable']:0;
					$ArrGroupOutMaterial[$value]['wip_foh'] = (!empty($getSummary[0]['wip_foh']))?$getSummary[0]['wip_foh']:0;
					$ArrGroupOutMaterial[$value]['created_by'] = $username;
					$ArrGroupOutMaterial[$value]['created_date'] = $datetime;
					$ArrGroupOutMaterial[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
					$ArrGroupOutMaterial[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
					$ArrGroupOutMaterial[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
					$ArrGroupOutMaterial[$value]['kode_delivery'] = $kode_delivery;
					$ArrGroupOutMaterial[$value]['jenis'] = 'out';
					$ArrGroupOutMaterial[$value]['id_material'] = (!empty($getSummary[0]['id_material']))?$getSummary[0]['id_material']:0;
					$ArrGroupOutMaterial[$value]['nm_material'] = (!empty($getSummary[0]['nm_material']))?$getSummary[0]['nm_material']:0;
					$ArrGroupOutMaterial[$value]['qty_mat'] = (!empty($getSummary[0]['qty_mat']))?$getSummary[0]['qty_mat']:0;
					$ArrGroupOutMaterial[$value]['cost_book'] = (!empty($getSummary[0]['cost_book']))?$getSummary[0]['cost_book']:0;
					$ArrGroupOutMaterial[$value]['gudang'] = (!empty($getSummary[0]['gudang']))?$getSummary[0]['gudang']:0;

					$id_trans = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:$this->kode_trs;
				}
			}
		}

		//DATA SPOOL
		//GROUP DATA
		$ArrGroupSpool = [];
		$ArrGroupOutSpool = [];
		$ArrProSpool = $this->db->select('spool_induk')->group_by('spool_induk')->get_where('delivery_product_detail',array('kode_delivery'=>$kode_delivery,'spool_induk !='=>NULL))->result_array();
		$ArrSpool = [];
		foreach ($ArrProSpool as $key => $value) {
			$ArrSpool[] = $value['spool_induk'];
		}

		if(!empty($ArrSpool)){
			$getAllSpool = $this->db->where_in('kode_spool',$ArrSpool)->get_where('data_erp_fg',array('jenis'=>'in','keterangan'=>'WIP to Finish Good (Spool)'))->result_array();
			if(!empty($getAllSpool)){
				foreach ($getAllSpool as $value => $valx) {
					$ArrGroupSpool[$value]['tanggal'] = date('Y-m-d');
					$ArrGroupSpool[$value]['keterangan'] = 'Finish Good to In Transit';
					$ArrGroupSpool[$value]['no_so'] 	= $valx['no_so'];
					$ArrGroupSpool[$value]['product'] = $valx['product'];
					$ArrGroupSpool[$value]['no_spk'] = $valx['no_spk'];
					$ArrGroupSpool[$value]['kode_trans'] = $valx['kode_trans'];
					$ArrGroupSpool[$value]['id_pro_det'] = $valx['id_pro_det'];
					$ArrGroupSpool[$value]['qty'] = $valx['qty'];
					$ArrGroupSpool[$value]['nilai_unit'] = $valx['nilai_wip'];
					$ArrGroupSpool[$value]['created_by'] = $username;
					$ArrGroupSpool[$value]['created_date'] = $datetime;
					$ArrGroupSpool[$value]['id_trans'] = $valx['id_trans'];
					$ArrGroupSpool[$value]['id_pro'] = $valx['id_pro'];
					$ArrGroupSpool[$value]['qty_ke'] = $valx['qty_ke'];
					$ArrGroupSpool[$value]['kode_delivery'] = $kode_delivery;
					$ArrGroupSpool[$value]['id_material'] = $valx['id_material'];
					$ArrGroupSpool[$value]['nm_material'] = $valx['nm_material'];
					$ArrGroupSpool[$value]['qty_mat'] = $valx['qty_mat'];
					$ArrGroupSpool[$value]['cost_book'] = $valx['cost_book'];
					$ArrGroupSpool[$value]['gudang'] = $valx['gudang'];
					$ArrGroupSpool[$value]['kode_spool'] = $valx['kode_spool'];

					$ArrGroupOutSpool[$value]['tanggal'] = date('Y-m-d');
					$ArrGroupOutSpool[$value]['keterangan'] = 'Finish Good to In Transit';
					$ArrGroupOutSpool[$value]['no_so'] 	= $valx['no_so'];
					$ArrGroupOutSpool[$value]['product'] = $valx['product'];
					$ArrGroupOutSpool[$value]['no_spk'] = $valx['no_spk'];
					$ArrGroupOutSpool[$value]['kode_trans'] = $valx['kode_trans'];
					$ArrGroupOutSpool[$value]['id_pro_det'] = $valx['id_pro_det'];
					$ArrGroupOutSpool[$value]['qty'] = $valx['qty'];
					$ArrGroupOutSpool[$value]['nilai_unit'] = $valx['nilai_unit'];
					$ArrGroupOutSpool[$value]['nilai_wip'] = $valx['nilai_wip'];
					$ArrGroupOutSpool[$value]['material'] = $valx['material'];
					$ArrGroupOutSpool[$value]['wip_direct'] = $valx['wip_direct'];
					$ArrGroupOutSpool[$value]['wip_indirect'] = $valx['wip_indirect'];
					$ArrGroupOutSpool[$value]['wip_consumable'] = $valx['wip_consumable'];
					$ArrGroupOutSpool[$value]['wip_foh'] = $valx['wip_foh'];
					$ArrGroupOutSpool[$value]['created_by'] = $username;
					$ArrGroupOutSpool[$value]['created_date'] = $datetime;
					$ArrGroupOutSpool[$value]['id_trans'] = $valx['id_trans'];
					$ArrGroupOutSpool[$value]['id_pro'] = $valx['id_pro'];
					$ArrGroupOutSpool[$value]['qty_ke'] = $valx['qty_ke'];
					$ArrGroupOutSpool[$value]['kode_delivery'] = $kode_delivery;
					$ArrGroupOutSpool[$value]['jenis'] = 'out';
					$ArrGroupOutSpool[$value]['id_material'] = $valx['id_material'];
					$ArrGroupOutSpool[$value]['nm_material'] = $valx['nm_material'];
					$ArrGroupOutSpool[$value]['qty_mat'] = $valx['qty_mat'];
					$ArrGroupOutSpool[$value]['cost_book'] = $valx['cost_book'];
					$ArrGroupOutSpool[$value]['gudang'] = $valx['gudang'];
					$ArrGroupOutSpool[$value]['kode_spool'] = $valx['kode_spool'];

					$id_trans = $valx['id_trans'];
				}
			}
		}

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroup);
			$this->jurnalIntransit($id_trans);
		}

		if(!empty($ArrGroupOut)){
			$this->db->insert_batch('data_erp_fg',$ArrGroupOut);
		}
		
		if(!empty($ArrGroupMaterial)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroupMaterial);
			$this->jurnalIntransit($id_trans);
		}

		if(!empty($ArrGroupOutMaterial)){
			$this->db->insert_batch('data_erp_fg',$ArrGroupOutMaterial);
		}

		if(!empty($ArrGroupSpool)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroupSpool);
			$this->jurnalIntransit($id_trans);
		}

		if(!empty($ArrGroupOutSpool)){
			$this->db->insert_batch('data_erp_fg',$ArrGroupOutSpool);
		}
	}

	public function close_jurnal_in_customer($kode_delivery){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		
		//GROUP DATA
		$GetDate = $this->db->select('MAX(created_date) AS created_date')->get_where('data_erp_in_transit',array('kode_delivery'=>$kode_delivery))->result_array();
		$created_date = (!empty($GetDate[0]['created_date']))?$GetDate[0]['created_date']:null;
		$ArrGroup = [];
		$ArrGroupOut = [];
		$ArrIdPro = $this->db->get_where('data_erp_in_transit',array('kode_delivery'=>$kode_delivery,'created_date'=>$created_date))->result_array();
		
		if(!empty($ArrIdPro)){
			foreach ($ArrIdPro as $value => $valx) {
				$ArrGroup[$value]['tanggal'] = date('Y-m-d');
				$ArrGroup[$value]['keterangan'] = 'In Transit to Customer';
				$ArrGroup[$value]['no_so'] 	= $valx['no_so'];
				$ArrGroup[$value]['product'] = $valx['product'];
				$ArrGroup[$value]['no_spk'] = $valx['no_spk'];
				$ArrGroup[$value]['kode_trans'] = $valx['kode_trans'];
				$ArrGroup[$value]['id_pro_det'] = $valx['id_pro_det'];
				$ArrGroup[$value]['qty'] = $valx['qty'];
				$ArrGroup[$value]['nilai_unit'] = $valx['nilai_unit'];
				$ArrGroup[$value]['created_by'] = $username;
				$ArrGroup[$value]['created_date'] = $datetime;
				$ArrGroup[$value]['id_trans'] = $valx['id_trans'];
				$ArrGroup[$value]['id_pro'] = $valx['id_pro'];
				$ArrGroup[$value]['qty_ke'] = $valx['qty_ke'];
				$ArrGroup[$value]['kode_delivery'] = $valx['kode_delivery'];
				$ArrGroup[$value]['id_material'] = $valx['id_material'];
				$ArrGroup[$value]['nm_material'] = $valx['nm_material'];
				$ArrGroup[$value]['qty_mat'] = $valx['qty_mat'];
				$ArrGroup[$value]['cost_book'] = $valx['cost_book'];
				$ArrGroup[$value]['gudang'] = $valx['gudang'];
				$ArrGroup[$value]['kode_spool'] = $valx['kode_spool'];

				$id_trans         = $valx['id_trans'];


				$ArrGroupOut[$value]['tanggal'] = date('Y-m-d');
				$ArrGroupOut[$value]['keterangan'] = 'In Transit to Customer';
				$ArrGroupOut[$value]['no_so'] 	= $valx['no_so'];
				$ArrGroupOut[$value]['product'] = $valx['product'];
				$ArrGroupOut[$value]['no_spk'] = $valx['no_spk'];
				$ArrGroupOut[$value]['kode_trans'] = $valx['kode_trans'];
				$ArrGroupOut[$value]['id_pro_det'] = $valx['id_pro_det'];
				$ArrGroupOut[$value]['qty'] = $valx['qty'];
				$ArrGroupOut[$value]['nilai_unit'] = $valx['nilai_unit'];
				$ArrGroupOut[$value]['created_by'] = $username;
				$ArrGroupOut[$value]['created_date'] = $datetime;
				$ArrGroupOut[$value]['id_trans'] = $valx['id_trans'];
				$ArrGroupOut[$value]['id_pro'] = $valx['id_pro'];
				$ArrGroupOut[$value]['qty_ke'] = $valx['qty_ke'];
				$ArrGroupOut[$value]['kode_delivery'] = $valx['kode_delivery'];
				$ArrGroupOut[$value]['jenis'] = 'out';
				$ArrGroupOut[$value]['id_material'] = $valx['id_material'];
				$ArrGroupOut[$value]['nm_material'] = $valx['nm_material'];
				$ArrGroupOut[$value]['qty_mat'] = $valx['qty_mat'];
				$ArrGroupOut[$value]['cost_book'] = $valx['cost_book'];
				$ArrGroupOut[$value]['gudang'] = $valx['gudang'];
				$ArrGroupOut[$value]['kode_spool'] = $valx['kode_spool'];
			} 
		}

		// print_r($ArrGroup);
		// print_r($ArrGroupOut);
		// exit;

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_in_customer',$ArrGroup);
			
		}
		if(!empty($ArrGroupOut)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroupOut);
		}

		$this->jurnalIntransitCustomer($id_trans);
	}

	public function close_jurnal_in_transit_reject_to_fg($kode_delivery){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		
		//GROUP DATA
		$ArrGroup = [];
		$ArrGroupOut = [];
		$ArrIdPro = $this->db->get_where('delivery_product_detail',array('kode_delivery'=>$kode_delivery,'sts'=>'loose','spool_induk'=>NULL))->result_array();
		if(!empty($ArrIdPro)){
			foreach ($ArrIdPro as $value => $valx) {
				$getSummary = $this->db->select('*')->get_where('data_erp_fg',array('id_pro'=>$valx['id_pro']))->result_array();

				$ArrGroup[$value]['tanggal'] = date('Y-m-d');
				$ArrGroup[$value]['keterangan'] = 'In Transit to Finish Good Reject';
				$ArrGroup[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
				$ArrGroup[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
				$ArrGroup[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
				$ArrGroup[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
				$ArrGroup[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
				$ArrGroup[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
				$ArrGroup[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_unit']))?$getSummary[0]['nilai_unit']:0;
				$ArrGroup[$value]['created_by'] = $username;
				$ArrGroup[$value]['created_date'] = $datetime;
				$ArrGroup[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
				$ArrGroup[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
				$ArrGroup[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
				$ArrGroup[$value]['kode_delivery'] = $kode_delivery;
				$ArrGroup[$value]['jenis'] = 'out';

				$ArrGroupOut[$value]['tanggal'] = date('Y-m-d');
				$ArrGroupOut[$value]['keterangan'] = 'In Transit to Finish Good Reject';
				$ArrGroupOut[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
				$ArrGroupOut[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
				$ArrGroupOut[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
				$ArrGroupOut[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
				$ArrGroupOut[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
				$ArrGroupOut[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
				$ArrGroupOut[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_unit']))?$getSummary[0]['nilai_unit']:0;
				$ArrGroupOut[$value]['nilai_wip'] = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
				$ArrGroupOut[$value]['material'] = (!empty($getSummary[0]['material']))?$getSummary[0]['material']:0;
				$ArrGroupOut[$value]['wip_direct'] = (!empty($getSummary[0]['wip_direct']))?$getSummary[0]['wip_direct']:0;
				$ArrGroupOut[$value]['wip_indirect'] = (!empty($getSummary[0]['wip_indirect']))?$getSummary[0]['wip_indirect']:0;
				$ArrGroupOut[$value]['wip_consumable'] = (!empty($getSummary[0]['wip_consumable']))?$getSummary[0]['wip_consumable']:0;
				$ArrGroupOut[$value]['wip_foh'] = (!empty($getSummary[0]['wip_foh']))?$getSummary[0]['wip_foh']:0;
				$ArrGroupOut[$value]['created_by'] = $username;
				$ArrGroupOut[$value]['created_date'] = $datetime;
				$ArrGroupOut[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
				$ArrGroupOut[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
				$ArrGroupOut[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
				$ArrGroupOut[$value]['kode_delivery'] = $kode_delivery;
				
			}
		}

		$ArrGroupMaterial = [];
		$ArrGroupOutMaterial = [];
		$ListIN = ['so material','field joint','deadstok','cut','cut deadstock'];
		$ArrayDeliveryMaterial = $this->db->where_in('sts_product',$ListIN)->get_where('delivery_product_detail',array('kode_delivery'=>$kode_delivery,'spool_induk'=>NULL))->result_array();
		if(!empty($ArrayDeliveryMaterial)){
			foreach ($ArrayDeliveryMaterial as $value => $valx) {
				if($valx['sts_product'] == 'so material'){
					$getDetOutgoing = $this->db->select('*')->get_where('warehouse_adjustment_detail',array('id'=>$valx['id_uniq']))->result_array();
					$kode_trans 	= (!empty($getDetOutgoing[0]['kode_trans']))?$getDetOutgoing[0]['kode_trans']:0;
					$id_material 	= (!empty($getDetOutgoing[0]['id_material']))?$getDetOutgoing[0]['id_material']:0;

					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('kode_trans'=>$kode_trans,'id_material'=>$id_material))->result_array();
				}

				if($valx['sts_product'] == 'field joint'){
					$getDetOutgoing = $this->db->select('*')->get_where('outgoing_field_joint',array('id'=>$valx['id_uniq']))->result_array();
					$kode_trans 	= (!empty($getDetOutgoing[0]['kode_trans']))?$getDetOutgoing[0]['kode_trans']:0;
					$no_spk 		= (!empty($getDetOutgoing[0]['no_spk']))?$getDetOutgoing[0]['no_spk']:0;

					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('kode_trans'=>$kode_trans,'no_spk'=>$no_spk))->result_array();
				}

				if($valx['sts_product'] == 'deadstok' AND $valx['sts'] != 'loose_dead_modif'){
					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro_det'=>$valx['id_uniq']))->result_array();
				}

				if($valx['sts_product'] == 'cut'){
					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro'=>$valx['id_uniq'],'id_pro_det'=>$valx['id_pro']))->result_array();
				}

				if($valx['sts_product'] == 'cut deadstock'){
					$getSummary 	= $this->db->select('*')->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro'=>$valx['id_uniq'],'jenis'=>'in cutting deadstok'))->result_array();
				}

				if($valx['sts_product'] == 'deadstok' AND $valx['sts'] == 'loose_dead_modif'){
					$GetKodeSPK 	= $this->db->select('*')->get_where('deadstok_modif',array('id'=>$valx['id_uniq']))->result_array();
					$getSummaryMax 	= $this->db->select('*')->order_by('id','desc')->get_where('data_erp_fg',array('id_pro_det'=>$valx['id_milik'],'kode_trans'=>$GetKodeSPK[0]['kode_spk']))->result_array();
					$getSummary 	= $this->db->select('*')->get_where('data_erp_fg',array('id_pro_det'=>$valx['id_milik'],'kode_trans'=>$GetKodeSPK[0]['kode_spk'],'created_date'=>$getSummaryMax[0]['created_date']))->result_array();
					foreach ($getSummary as $key => $value2x) {
						$UNIQ2 = $value.'-'.$key;
						$ArrGroupMaterial[$UNIQ2]['tanggal'] = date('Y-m-d');
						$ArrGroupMaterial[$UNIQ2]['keterangan'] = 'In Transit to Finish Good Reject';
						$ArrGroupMaterial[$UNIQ2]['no_so'] 	= $value2x['no_so'];
						$ArrGroupMaterial[$UNIQ2]['product'] = $value2x['product'];
						$ArrGroupMaterial[$UNIQ2]['no_spk'] = $value2x['no_spk'];
						$ArrGroupMaterial[$UNIQ2]['kode_trans'] = $value2x['kode_trans'];
						$ArrGroupMaterial[$UNIQ2]['id_pro_det'] = $value2x['id_pro_det'];
						$ArrGroupMaterial[$UNIQ2]['qty'] = $value2x['qty'];
						$ArrGroupMaterial[$UNIQ2]['nilai_unit'] = $value2x['nilai_unit'];
						$ArrGroupMaterial[$UNIQ2]['created_by'] = $username;
						$ArrGroupMaterial[$UNIQ2]['created_date'] = $datetime;
						$ArrGroupMaterial[$UNIQ2]['id_trans'] = $value2x['id_trans'];
						$ArrGroupMaterial[$UNIQ2]['id_pro'] = $value2x['id_pro'];
						$ArrGroupMaterial[$UNIQ2]['qty_ke'] = $value2x['qty_ke'];
						$ArrGroupMaterial[$UNIQ2]['kode_delivery'] = $kode_delivery;
						$ArrGroupMaterial[$UNIQ2]['id_material'] = $value2x['id_material'];
						$ArrGroupMaterial[$UNIQ2]['nm_material'] = $value2x['nm_material'];
						$ArrGroupMaterial[$UNIQ2]['qty_mat'] = $value2x['qty_mat'];
						$ArrGroupMaterial[$UNIQ2]['cost_book'] = $value2x['cost_book'];
						$ArrGroupMaterial[$UNIQ2]['gudang'] = $value2x['gudang'];
						$ArrGroupMaterial[$UNIQ2]['jenis'] = 'out';

						$ArrGroupOutMaterial[$UNIQ2]['tanggal'] = date('Y-m-d');
						$ArrGroupOutMaterial[$UNIQ2]['keterangan'] = 'In Transit to Finish Good Reject';
						$ArrGroupOutMaterial[$UNIQ2]['no_so'] 	= $value2x['no_so'];
						$ArrGroupOutMaterial[$UNIQ2]['product'] = $value2x['product'];
						$ArrGroupOutMaterial[$UNIQ2]['no_spk'] = $value2x['no_spk'];
						$ArrGroupOutMaterial[$UNIQ2]['kode_trans'] = $value2x['kode_trans'];
						$ArrGroupOutMaterial[$UNIQ2]['id_pro_det'] = $value2x['id_pro_det'];
						$ArrGroupOutMaterial[$UNIQ2]['qty'] = $value2x['qty'];
						$ArrGroupOutMaterial[$UNIQ2]['nilai_unit'] = $value2x['nilai_unit'];
						$ArrGroupOutMaterial[$UNIQ2]['nilai_wip'] = $value2x['nilai_wip'];
						$ArrGroupOutMaterial[$UNIQ2]['material'] = $value2x['material'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_direct'] = $value2x['wip_direct'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_indirect'] = $value2x['wip_indirect'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_consumable'] = $value2x['wip_consumable'];
						$ArrGroupOutMaterial[$UNIQ2]['wip_foh'] = $value2x['wip_foh'];
						$ArrGroupOutMaterial[$UNIQ2]['created_by'] = $username;
						$ArrGroupOutMaterial[$UNIQ2]['created_date'] = $datetime;
						$ArrGroupOutMaterial[$UNIQ2]['id_trans'] = $value2x['id_trans'];
						$ArrGroupOutMaterial[$UNIQ2]['id_pro'] = $value2x['id_pro'];
						$ArrGroupOutMaterial[$UNIQ2]['qty_ke'] = $value2x['qty_ke'];
						$ArrGroupOutMaterial[$UNIQ2]['kode_delivery'] = $kode_delivery;
						$ArrGroupOutMaterial[$UNIQ2]['id_material'] = $value2x['id_material'];
						$ArrGroupOutMaterial[$UNIQ2]['nm_material'] = $value2x['nm_material'];
						$ArrGroupOutMaterial[$UNIQ2]['qty_mat'] = $value2x['qty_mat'];
						$ArrGroupOutMaterial[$UNIQ2]['cost_book'] = $value2x['cost_book'];
						$ArrGroupOutMaterial[$UNIQ2]['gudang'] = $value2x['gudang'];
					}
				}
				else{
					$ArrGroupMaterial[$value]['tanggal'] = date('Y-m-d');
					$ArrGroupMaterial[$value]['keterangan'] = 'In Transit to Finish Good Reject';
					$ArrGroupMaterial[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroupMaterial[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroupMaterial[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroupMaterial[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
					$ArrGroupMaterial[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
					$ArrGroupMaterial[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
					$ArrGroupMaterial[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_unit']))?$getSummary[0]['nilai_unit']:0;
					$ArrGroupMaterial[$value]['created_by'] = $username;
					$ArrGroupMaterial[$value]['created_date'] = $datetime;
					$ArrGroupMaterial[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
					$ArrGroupMaterial[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
					$ArrGroupMaterial[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
					$ArrGroupMaterial[$value]['kode_delivery'] = $kode_delivery;
					$ArrGroupMaterial[$value]['id_material'] = (!empty($getSummary[0]['id_material']))?$getSummary[0]['id_material']:0;
					$ArrGroupMaterial[$value]['nm_material'] = (!empty($getSummary[0]['nm_material']))?$getSummary[0]['nm_material']:0;
					$ArrGroupMaterial[$value]['qty_mat'] = (!empty($getSummary[0]['qty_mat']))?$getSummary[0]['qty_mat']:0;
					$ArrGroupMaterial[$value]['cost_book'] = (!empty($getSummary[0]['cost_book']))?$getSummary[0]['cost_book']:0;
					$ArrGroupMaterial[$value]['gudang'] = (!empty($getSummary[0]['gudang']))?$getSummary[0]['gudang']:0;
					$ArrGroupMaterial[$value]['jenis'] = 'out';

					$ArrGroupOutMaterial[$value]['tanggal'] = date('Y-m-d');
					$ArrGroupOutMaterial[$value]['keterangan'] = 'In Transit to Finish Good Reject';
					$ArrGroupOutMaterial[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroupOutMaterial[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroupOutMaterial[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroupOutMaterial[$value]['kode_trans'] = (!empty($getSummary[0]['kode_trans']))?$getSummary[0]['kode_trans']:NULL;
					$ArrGroupOutMaterial[$value]['id_pro_det'] = (!empty($getSummary[0]['id_pro_det']))?$getSummary[0]['id_pro_det']:NULL;
					$ArrGroupOutMaterial[$value]['qty'] = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:NULL;
					$ArrGroupOutMaterial[$value]['nilai_unit'] = (!empty($getSummary[0]['nilai_unit']))?$getSummary[0]['nilai_unit']:0;
					$ArrGroupOutMaterial[$value]['nilai_wip'] = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
					$ArrGroupOutMaterial[$value]['material'] = (!empty($getSummary[0]['material']))?$getSummary[0]['material']:0;
					$ArrGroupOutMaterial[$value]['wip_direct'] = (!empty($getSummary[0]['wip_direct']))?$getSummary[0]['wip_direct']:0;
					$ArrGroupOutMaterial[$value]['wip_indirect'] = (!empty($getSummary[0]['wip_indirect']))?$getSummary[0]['wip_indirect']:0;
					$ArrGroupOutMaterial[$value]['wip_consumable'] = (!empty($getSummary[0]['wip_consumable']))?$getSummary[0]['wip_consumable']:0;
					$ArrGroupOutMaterial[$value]['wip_foh'] = (!empty($getSummary[0]['wip_foh']))?$getSummary[0]['wip_foh']:0;
					$ArrGroupOutMaterial[$value]['created_by'] = $username;
					$ArrGroupOutMaterial[$value]['created_date'] = $datetime;
					$ArrGroupOutMaterial[$value]['id_trans'] = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:NULL;
					$ArrGroupOutMaterial[$value]['id_pro'] = (!empty($getSummary[0]['id_pro']))?$getSummary[0]['id_pro']:0;
					$ArrGroupOutMaterial[$value]['qty_ke'] = (!empty($getSummary[0]['qty_ke']))?$getSummary[0]['qty_ke']:0;
					$ArrGroupOutMaterial[$value]['kode_delivery'] = $kode_delivery;
					$ArrGroupOutMaterial[$value]['id_material'] = (!empty($getSummary[0]['id_material']))?$getSummary[0]['id_material']:0;
					$ArrGroupOutMaterial[$value]['nm_material'] = (!empty($getSummary[0]['nm_material']))?$getSummary[0]['nm_material']:0;
					$ArrGroupOutMaterial[$value]['qty_mat'] = (!empty($getSummary[0]['qty_mat']))?$getSummary[0]['qty_mat']:0;
					$ArrGroupOutMaterial[$value]['cost_book'] = (!empty($getSummary[0]['cost_book']))?$getSummary[0]['cost_book']:0;
					$ArrGroupOutMaterial[$value]['gudang'] = (!empty($getSummary[0]['gudang']))?$getSummary[0]['gudang']:0;
					}
			}
		}

		//DATA SPOOL
		//GROUP DATA
		$ArrGroupSpool = [];
		$ArrGroupOutSpool = [];
		$ArrProSpool = $this->db->select('spool_induk')->group_by('spool_induk')->get_where('delivery_product_detail',array('kode_delivery'=>$kode_delivery,'spool_induk !='=>NULL))->result_array();
		$ArrSpool = [];
		foreach ($ArrProSpool as $key => $value) {
			$ArrSpool[] = $value['spool_induk'];
		}

		$getAllSpool = $this->db->where_in('kode_spool',$ArrSpool)->get_where('data_erp_fg',array('jenis'=>'in','keterangan'=>'WIP to Finish Good (Spool)'))->result_array();
		if(!empty($getAllSpool)){
			foreach ($getAllSpool as $value => $valx) {
				$ArrGroupSpool[$value]['tanggal'] = date('Y-m-d');
				$ArrGroupSpool[$value]['keterangan'] = 'In Transit to Finish Good Reject';
				$ArrGroupSpool[$value]['no_so'] 	= $valx['no_so'];
				$ArrGroupSpool[$value]['product'] = $valx['product'];
				$ArrGroupSpool[$value]['no_spk'] = $valx['no_spk'];
				$ArrGroupSpool[$value]['kode_trans'] = $valx['kode_trans'];
				$ArrGroupSpool[$value]['id_pro_det'] = $valx['id_pro_det'];
				$ArrGroupSpool[$value]['qty'] = $valx['qty'];
				$ArrGroupSpool[$value]['nilai_unit'] = $valx['nilai_unit'];
				$ArrGroupSpool[$value]['created_by'] = $username;
				$ArrGroupSpool[$value]['created_date'] = $datetime;
				$ArrGroupSpool[$value]['id_trans'] = $valx['id_trans'];
				$ArrGroupSpool[$value]['id_pro'] = $valx['id_pro'];
				$ArrGroupSpool[$value]['qty_ke'] = $valx['qty_ke'];
				$ArrGroupSpool[$value]['kode_delivery'] = $kode_delivery;
				$ArrGroupSpool[$value]['id_material'] = $valx['id_material'];
				$ArrGroupSpool[$value]['nm_material'] = $valx['nm_material'];
				$ArrGroupSpool[$value]['qty_mat'] = $valx['qty_mat'];
				$ArrGroupSpool[$value]['cost_book'] = $valx['cost_book'];
				$ArrGroupSpool[$value]['gudang'] = $valx['gudang'];
				$ArrGroupSpool[$value]['kode_spool'] = $valx['kode_spool'];
				$ArrGroupSpool[$value]['jenis'] = 'out';

				$ArrGroupOutSpool[$value]['tanggal'] = date('Y-m-d');
				$ArrGroupOutSpool[$value]['keterangan'] = 'In Transit to Finish Good Reject';
				$ArrGroupOutSpool[$value]['no_so'] 	= $valx['no_so'];
				$ArrGroupOutSpool[$value]['product'] = $valx['product'];
				$ArrGroupOutSpool[$value]['no_spk'] = $valx['no_spk'];
				$ArrGroupOutSpool[$value]['kode_trans'] = $valx['kode_trans'];
				$ArrGroupOutSpool[$value]['id_pro_det'] = $valx['id_pro_det'];
				$ArrGroupOutSpool[$value]['qty'] = $valx['qty'];
				$ArrGroupOutSpool[$value]['nilai_unit'] = $valx['nilai_unit'];
				$ArrGroupOutSpool[$value]['nilai_wip'] = $valx['nilai_wip'];
				$ArrGroupOutSpool[$value]['material'] = $valx['material'];
				$ArrGroupOutSpool[$value]['wip_direct'] = $valx['wip_direct'];
				$ArrGroupOutSpool[$value]['wip_indirect'] = $valx['wip_indirect'];
				$ArrGroupOutSpool[$value]['wip_consumable'] = $valx['wip_consumable'];
				$ArrGroupOutSpool[$value]['wip_foh'] = $valx['wip_foh'];
				$ArrGroupOutSpool[$value]['created_by'] = $username;
				$ArrGroupOutSpool[$value]['created_date'] = $datetime;
				$ArrGroupOutSpool[$value]['id_trans'] = $valx['id_trans'];
				$ArrGroupOutSpool[$value]['id_pro'] = $valx['id_pro'];
				$ArrGroupOutSpool[$value]['qty_ke'] = $valx['qty_ke'];
				$ArrGroupOutSpool[$value]['kode_delivery'] = $kode_delivery;
				$ArrGroupOutSpool[$value]['id_material'] = $valx['id_material'];
				$ArrGroupOutSpool[$value]['nm_material'] = $valx['nm_material'];
				$ArrGroupOutSpool[$value]['qty_mat'] = $valx['qty_mat'];
				$ArrGroupOutSpool[$value]['cost_book'] = $valx['cost_book'];
				$ArrGroupOutSpool[$value]['gudang'] = $valx['gudang'];
				$ArrGroupOutSpool[$value]['kode_spool'] = $valx['kode_spool'];
			}
		}

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroup);
		}

		if(!empty($ArrGroupOut)){
			$this->db->insert_batch('data_erp_fg',$ArrGroupOut);
		}
		
		if(!empty($ArrGroupMaterial)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroupMaterial);
		}

		if(!empty($ArrGroupOutMaterial)){
			$this->db->insert_batch('data_erp_fg',$ArrGroupOutMaterial);
		}

		if(!empty($ArrGroupSpool)){
			$this->db->insert_batch('data_erp_in_transit',$ArrGroupSpool);
		}

		if(!empty($ArrGroupOutSpool)){
			$this->db->insert_batch('data_erp_fg',$ArrGroupOutSpool);
		}
	}

	public function detailDelivery($kode_delivery){
		$GET_DET_FD 		= get_detailFinalDrawing();
		$GET_SALES_ORDER 	= get_detail_ipp();
		$tanki_model = $this->tanki_model;

		$get_split_ipp1 = $this->db
								->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
								->group_by('a.id_milik, a.sts, a.spool_induk')
								->order_by('a.spool_induk', 'asc')
								->order_by('a.kode_spool', 'asc')
								->where('(a.berat > 0 OR a.berat IS NULL)')
								->join('production_detail b','a.id_pro=b.id','left')
								->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'a.sts_product' => NULL,'a.kode_spk !='=>'deadstok'))->result_array();
		$get_split_ipp2 = $this->db->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where('(a.berat > 0 OR a.berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'so material'))->result_array();
		$get_split_ipp3 = $this->db->select('COUNT(a.id_milik) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where('(a.berat > 0 OR a.berat IS NULL)')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'field joint'))->result_array();
		$get_split_ipp4 = $this->db->select('COUNT(a.berat) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_pro')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where("(a.berat > 0 OR a.berat IS NULL OR a.sts = 'loose_dead')")->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'deadstok'))->result_array();
		$get_split_ipp4a = $this->db->select('COUNT(a.id) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_milik')->order_by('a.id', 'asc')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'kode_spk' => 'deadstok'))->result_array();
		$get_split_ipp4b = $this->db->select('COUNT(a.id) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_uniq')->order_by('a.id', 'asc')->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts' => 'cut', 'spool_induk' => null))->result_array();
		$get_split_ipp5 = $this->db->select('SUM(a.berat) AS qty_product, a.*, "" AS type_product,"" AS product_tanki')->group_by('a.id_pro')->order_by('a.spool_induk', 'asc')->order_by('a.kode_spool', 'asc')->order_by('a.id', 'asc')->where("(a.berat > 0 OR a.berat IS NULL)")->get_where('delivery_product_detail a', array('a.kode_delivery' => $kode_delivery, 'sts_product' => 'aksesoris'))->result_array();
		$get_split_ipp = array_merge($get_split_ipp1, $get_split_ipp2, $get_split_ipp3, $get_split_ipp4, $get_split_ipp4a, $get_split_ipp5, $get_split_ipp4b);
		$ArrNo_IPP = [];
		$ArrNo_SPK = [];
		$ArrNo_LS = [];
		$ArrNo_Drawing = [];
		foreach ($get_split_ipp as $key => $value) {
			$key++;
			$no_spk 		= $value['no_spk'];
			$NO_IPP 		= str_replace(['PRO-', 'BQ-'], '', $value['id_produksi']);
			$NO_SO 			= (!empty($GET_SALES_ORDER[$NO_IPP]['so_number'])) ? $GET_SALES_ORDER[$NO_IPP]['so_number'] : '';
			$ArrNo_IPP[]	= $NO_SO;
			if (!empty($value['no_drawing'])) {
				if ($value['sts_product'] != 'aksesoris') {
				$ArrNo_Drawing[] = $value['no_drawing'];
				}
			}

			$CUTTING_KE = (!empty($value['cutting_ke'])) ? '.' . $value['cutting_ke'] : '';
			$IMPLODE = explode('.', $value['product_code']);
			$ID_PRX_ADD = $IMPLODE[0] . '.' . $value['product_ke'] . $CUTTING_KE . '/' . $no_spk;
			if ($value['sts_product'] == 'so material') {
				if ($value['berat'] > 0) {
					$ID_PRX_ADD = strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
				}
			}

			if ($value['sts_product'] == 'field joint') {
				if ($value['berat'] > 0) {
					$ID_PRX_ADD = strtoupper(get_name('so_number', 'so_number', 'id_bq', str_replace('PRO-', 'BQ-', $value['id_produksi']))) . '/' . $no_spk;
				}
			}

			$series 	= (!empty($GET_DET_FD[$value['id_milik']]['series'])) ? $GET_DET_FD[$value['id_milik']]['series'] : '';
			$product 	= strtoupper($value['product']) . ", " . $series . ", DIA " . spec_bq2($value['id_milik']);
			$SATUAN 	= ' pcs';
			$QTY 		= $value['qty_product'];

			if ($value['sts_product'] == 'deadstok') {
				$ID_PRX_ADD = $value['product_code'].'/'.$value['no_spk'];
				$product 	= strtoupper($value['product']) . ", DIA " . $value['product_code'].' x '.$value['length'];
			}

			if ($value['sts'] == 'loose_dead') {
				$ID_PRX_ADD = $value['product_code'].'/'.$value['no_spk'];
				$product 	= strtoupper($value['product']) . ", DIA " . $value['kode_spk'].' x '.$value['length'];
			}

			if ($value['type_product'] == 'tanki') {
				$spec = $tanki_model->get_spec($value['id_milik']);
				$product 	= strtoupper($value['product_tanki']) . ", " . $spec;
			}

			if ($value['sts_product'] == 'so material') {
				$product 	= strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
				$SATUAN 	= ' kg';
				$QTY 		= number_format($value['berat'], 2);
			}

			if ($value['sts_product'] == 'field joint') {
				$SATUAN     = ' kit';
				$QTY         = number_format($value['berat']);
			}

			if ($value['sts_product'] == 'deadstok') {
				$QTY         = number_format($value['qty_product']);
				$product 	= strtoupper($value['product']) . ", " .$value['kode_spk']." x ".$value['length'];
			}
			if ($value['sts_product'] == 'aksesoris') {
				$QTY         	= number_format($value['qty_product'],2);
				$ID_PRX_ADD 	= $value['product_code'];
				$product 		= strtoupper($value['no_drawing']);
			}

			$ID_PRX = "[<b>" . $QTY . $SATUAN . "</b>][<b>" . $ID_PRX_ADD . "</b>], " . $product;

			$QNOSO = $this->db->get_where('so_number', ['id_bq' => str_replace("PRO", "BQ", $value['id_produksi'])])->row();
			$NOSO = (!empty($QNOSO->so_number))?$QNOSO->so_number:'-';

			//Category
			$loose_spool = (!empty($value['spool_induk'])) ? $value['spool_induk'] . '-' . $value['kode_spool'] : 'LOOSE';
			if ($value['sts_product'] == 'so material') {
				$loose_spool = $NOSO;
			}
			if ($value['sts_product'] == 'field joint') {
				$loose_spool = "FIELD JOINT";
			}
			if ($value['sts'] == 'cut' and empty($value['spool_induk'])) {
				$loose_spool = "PIPE CUTTING";
				$ID_PRX = "[<b>" . $QTY . $SATUAN . "</b>][<b>" . $ID_PRX_ADD . "</b>], " . $product.', '.$value['length'];
			}
			if ($value['sts_product'] == 'cut deadstock' and empty($value['spool_induk'])) {
				$loose_spool = "DEADSTOCK CUTTING";
				$ID_PRX = "[<b>" . $QTY . $SATUAN . "</b>][<b>".$value['product_code']."/".$value['no_spk']."</b>], " . $value['product'].', '.$value['length'];
			}
			if ($value['sts_product'] == 'deadstok' OR $value['kode_spk'] == 'deadstok') {
				$loose_spool = "DEADSTOCK";
			}
			if ($value['sts_product'] == 'aksesoris') {
				$loose_spool = "AKSESORIS	";
			}
			$ArrNo_LS[] = $key . '. ' . $loose_spool;

			$ArrNo_SPK[] = $key . ".<span class='text-bold text-blue'>" . $loose_spool . "</span> " . $ID_PRX;
		}
		// print_r($ArrGroup); exit;
		$explode_ipp 	= implode('<br>', array_unique($ArrNo_IPP));
		$explode_nd 	= implode('<br>', array_unique($ArrNo_Drawing));
		$explode_spk 	= implode('<br>', $ArrNo_SPK);

		$ArrReturn = [
			'explode_ipp' => $explode_ipp,
			'explode_nd' => $explode_nd,
			'explode_spk' => $explode_spk
		];

		return $ArrReturn;
	}

	//SYAMSUDIN 20/03/2024

	function jurnalIntransit($idtrans){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	
		   
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_unit as finishgood  FROM data_erp_in_transit WHERE id_trans ='".$idtrans."' AND tanggal ='".$Date."' AND jenis = 'in'")->result();
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$spasi       = ',';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$id          = $data->id_trans;
               	$no_request  = $data->no_spk;	
				
				
				$finishgood    	= $data->finishgood;
				
				
				
				
				if ($nm_material=='pipe'){			
				$coa_wip 		='1103-03-02';	
				}else{
				$coa_wip 		='1103-03-03';						
				}					
			    				
				$coaintransit		='1103-04-06';
				$coafg   		    ='1103-04-01';
                				
				
								
				    			 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coaintransit,
					  'keterangan'    => 'FINISHED GOOD - INTRANSIT',
					  'no_reff'       => $id,
					  'debet'         => $finishgood,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coafg,
					  'keterangan'    => 'FINISHED GOOD - INTRANSIT',
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $finishgood,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					  	
				
				
			}
			
			if(!empty($id)){
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='Finishgood-Intransit' and no_reff ='$id' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			}
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'FINISHED GOOD - INTRANSIT'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $finishgood, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$this->db->insert(DBACC.'.javh',$dataJVhead);
			$datadetail=array();
			foreach ($det_Jurnaltes as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tgl_voucher,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $Keterangan_INV,
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					'created_on'		=> date('Y-m-d H:i:s'),
					'created_by'		=> 'intransit',
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
		  
		}



		//SYAMSUDIN 20/03/2024

	function jurnalIntransitCustomer($idtrans){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_unit as finishgood  FROM data_erp_in_transit WHERE id_trans ='".$idtrans."' AND tanggal ='".$Date."' AND jenis = 'out'")->result();
			
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$spasi       = ',';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$id          = $data->id_trans;
               	$no_request  = $data->no_spk;	
				
				
				$finishgood    	= $data->finishgood;
				
				
				
				
				if ($nm_material=='pipe'){			
				$coa_wip 		='1103-03-02';	
				}else{
				$coa_wip 		='1103-03-03';						
				}					
			    				
				$coaintransit		='1103-04-06';
				$coacustomer   		    ='1103-04-07';
                				
				
								
				    			 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coacustomer,
					  'keterangan'    => 'INTRANSIT-CUSTOMER',
					  'no_reff'       => $idtrans,
					  'debet'         => $finishgood,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coaintransit,
					  'keterangan'    => 'INTRANSIT-CUSTOMER',
					  'no_reff'       => $idtrans,
					  'debet'         => 0,
					  'kredit'        => $finishgood,
					  'jenis_jurnal'  => 'Finishgood-Intransit',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					  	
				
				
			}
			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='Finishgood-Intransit' and no_reff ='$idtrans' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'INTRANSIT-CUSTOMER'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $finishgood, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$this->db->insert(DBACC.'.javh',$dataJVhead);
			$datadetail=array();
			foreach ($det_Jurnaltes as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tgl_voucher,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $Keterangan_INV,
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					'created_on'		=> date('Y-m-d H:i:s'),
					'created_by'		=> 'customer'
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
		  
		}
	
}
