<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		$this->load->model('Jurnal_model');
		// Your own constructor code
		if (!$this->session->userdata('isORIlogin')) {
			redirect('login');
		}
	}

	public function quality()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/quality';
		// $Arr_Akses			= getAcccesmenu($controller);

		// if($Arr_Akses['read'] !='1'){
		// 	$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		// 	redirect(site_url('dashboard'));
		// }
		$tanda = $this->uri->segment(3);
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Quality Control',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'tanda'			=> $tanda,
			// 'akses_menu'	=> $Arr_Akses
		);

		history('View data quality control');
		$this->load->view('Qc/index', $data);
	}

	public function server_side_qc()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/quality';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc(
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

			// $detail = "<button class='btn btn-sm btn-success Perbandingan' title='Detail Production' data-id_product='".$row['id_product']."' data-id_milik='".$row['id_milik']."' data-id_produksi='".$row['id_produksi']."' data-id_pro_detail='".$row['id']."' data-qty_awal='".$row['qty_min']."' data-qty_akhir='".$row['qty_max']."'><i class='fa fa-eye'></i></button>";
			$check	= "<button class='btn btn-sm btn-success check_real' style='margin-bottom:2px' title='Release To Costing' data-kode_spk='" . $row['kode_spk'] . "' data-id_produksi='" . $row['id_produksi'] . "' data-id_milik='" . $row['id_milik'] . "' data-id_pro_detail='" . $row['id'] . "'><i class='fa fa-check'></i></button>";
			$edit	= "<button type='button' class='btn btn-sm btn-warning detail' style='margin-bottom:2px' title='Detail' data-id='" . $row['id_milik'] . "'><i class='fa fa-eye'></i></button>";
			$qr	= "<button type='button' class='btn btn-sm btn-default qr' style='margin-bottom:2px;padding:2px 6px' title='QR Code' data-kode_spk='" . $row['kode_spk'] . "' data-id_produksi='" . $row['id_produksi'] . "' data-id_milik='" . $row['id_milik'] . "' data-id_pro_detail='" . $row['id'] . "'><i class='fa fa-qrcode fa-2x'></i></button>";

			$NOMOR_SO = explode('-', $row['product_code']);
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$PRODUCT_NAME 	= (!empty($row['id_deadstok']))?$row['product_deadstok']:$row['id_category'];
			$nestedData[]	= "<div align='left'>" . strtoupper($PRODUCT_NAME) . "</div>";
			$nestedData[]	= "<div align='center'>" . $NOMOR_SO[0] . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['nm_customer']) . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['project']) . "</div>";
			$nestedData[]	= "<div align='left'>" . spec_bq2($row['id_milik']) . "</div>";
			// $nestedData[]	= "<div align='right'>" . number_format($row['length']) . "</div>";
			// $nestedData[]	= "<div align='center'>" . number_format($row['thickness'], 1) . "</div>";
			$get_split_code = $this->db->select('product_code, product_ke')->get_where('production_detail', array('kode_spk' => $row['kode_spk'], 'id_milik' => $row['id_milik'], 'id_produksi' => $row['id_produksi'], 'fg_date' => NULL, 'upload_real' => 'Y', 'upload_real2' => 'Y', 'upload_date' => $row['upload_date']))->result_array();
			$ArrGroup = [];
			foreach ($get_split_code as $key => $value) {
				$IMPLODE = explode('.', $value['product_code']);
				$ArrGroup[] = $IMPLODE[0] . '.' . $value['product_ke'];
			}
			// print_r($ArrGroup); exit;
			$explode = implode('<br>', $ArrGroup);

			// $nestedData[]	= "<div align='left'></div>";
			$nestedData[]	= "<div align='center'>" . COUNT($get_split_code) . "</div>";

			$date_produksi_MIN = (!empty($row['min_date_produksi']) and $row['min_date_produksi'] != '0000-00-00') ? date('d-M-Y', strtotime($row['min_date_produksi'])) : 'not set';
			$date_produksi_MAX = (!empty($row['max_date_produksi']) and $row['max_date_produksi'] != '0000-00-00') ? date('d-M-Y', strtotime($row['max_date_produksi'])) : 'not set';

			$date_produksi = $date_produksi_MIN . '<br>sd<br>' . $date_produksi_MAX;
			if ($date_produksi_MIN == $date_produksi_MAX) {
				$date_produksi = $date_produksi_MIN;
			}

			$nestedData[]	= "<div align='center'>" . $date_produksi . "</div>";
			$nestedData[]	= "<div align='center'>
									" . $check . "
									" . $edit . "
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

	public function query_data_qc($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";
		$group_by = "GROUP BY
		a.id_produksi,
		a.kode_spk,
		a.id_milik,
		a.upload_date";
		if ($status == 'pipe') {
			$where = " AND c.id_category='pipe' ";
		}
		if ($status == 'cutting') {
			// $where = " AND d.id IS NOT NULL AND c.id_category='pipe' ";
			$where = " AND a.sts_cutting='Z' AND (c.id_category='pipe' OR d.id_deadstok IS NOT NULL) ";
			// $group_by = "";
		}
		if ($status == 'fitting') {
			$where = " AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' AND c.id_category NOT IN " . NotInProduct() . " ";
		}

		//AND a.release_to_costing_date IS NULL 

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				MIN(product_ke) AS qty_min,
				MAX(product_ke) AS qty_max,
                b.spk1_cost,
                b.spk2_cost,
				z.project,
				z.nm_customer,
                c.diameter_1,
                c.length,
                c.thickness,
				b.qty AS tot_qty,
				MIN(a.closing_produksi_date) AS min_date_produksi,
				MAX(a.closing_produksi_date) AS max_date_produksi,
				d.id_category AS product_deadstok,
				d.id_deadstok AS id_deadstok
			FROM
				production_detail a
                LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
                LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
                LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
                LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke,
				(SELECT @row:=0) r
		    WHERE 1=1 
                AND a.upload_real = 'Y' 
                AND a.upload_real2 = 'Y' 
                AND a.kode_spk IS NOT NULL
				AND a.kode_spk != 'deadstok'
				AND a.fg_date IS NULL
				AND a.closing_produksi_date IS NOT NULL
                " . $where . "
                " . $where2 . "
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.project LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			" . $group_by . "
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_spk',
			2 => 'id_category'
		);

		$sql .= " ORDER BY a.closing_produksi_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function real_send()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_produksi	= $data['id_produksi'];
		$id_product		= $data['id_product'];
		$id_milik		= $data['id_milik']; //uniq production detail
		$id_milik2		= $data['id_milik2']; //id milik product 
		$tanda			= $data['status'];
		$time_uniq		= $data['time_uniq'];
		$id_spk			= $data['id_spk'];
		$first_id		= $data['first_id'];
		$dateTime 		= date('Y-m-d H:i:s');

		$get_update = $this->db->get_where('production_detail', array('id' => $id_milik2, 'lock_qc' => 'N'))->result();
		$kode_spk           = $get_update[0]->kode_spk;
		$print_merge_date   = $get_update[0]->print_merge_date;
		// echo $kode_spk;

		$ArrFlagRelease = [
			'lock_qc' => 'Y',
			'start_qc_by' => $data_session['ORI_User']['username'],
			'start_qc_date' => $dateTime
		];

		$ArrFlagSPKParsial = [
			'lock_qc_by' => $data_session['ORI_User']['username'],
			'lock_qc_date' => $dateTime
		];

		$ArrFlagSPK = [
			'spk1_cost' => 'Y',
			'spk1_costby' => $data_session['ORI_User']['username'],
			'spk1_costdate' => $dateTime,
			'spk2_cost' => 'Y',
			'spk2_costby' => $data_session['ORI_User']['username'],
			'spk2_costdate' => $dateTime
		];

		$HelpDet3 	= "tmp_production_real_detail";
		$HelpDet4 	= "tmp_production_real_detail_plus";
		$HelpDet5 	= "tmp_production_real_detail_add";

		$restDetail1	    = $this->db->get_where($HelpDet3, array('id_product' => $id_product, 'id_production_detail' => $id_milik2))->result_array();
		$restDetail2	    = $this->db->get_where($HelpDet4, array('id_product' => $id_product, 'id_production_detail' => $id_milik2))->result_array();
		$restDetail3	    = $this->db->get_where($HelpDet5, array('id_product' => $id_product, 'id_production_detail' => $id_milik2))->result_array();

		$ArrDetail = array();
		if (!empty($restDetail1)) {
			foreach ($restDetail1 as $val => $valx) {
				$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
				$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
				$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
				$ArrDetail[$val]['id_product'] = $valx['id_product'];
				$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
				$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
				$ArrDetail[$val]['benang'] = $valx['benang'];
				$ArrDetail[$val]['bw'] = $valx['bw'];
				$ArrDetail[$val]['layer'] = $valx['layer'];
				$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrDetail[$val]['status'] = $valx['status'];
				$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrDetail[$val]['status_date'] = $dateTime;
				$ArrDetail[$val]['catatan_programmer'] = $valx['catatan_programmer'];
				$ArrDetail[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
				$ArrDetail[$val]['spk'] = $valx['spk'];
				$ArrDetail[$val]['id_spk'] = $valx['id_spk'];
				$ArrDetail[$val]['updated_by'] = $valx['updated_by'];
				$ArrDetail[$val]['updated_date'] = $valx['updated_date'];
			}
		}
		$ArrPlus = array();
		if (!empty($restDetail2)) {
			foreach ($restDetail2 as $val => $valx) {
				$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
				$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
				$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
				$ArrPlus[$val]['id_product'] = $valx['id_product'];
				$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
				$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
				$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrPlus[$val]['status'] = $valx['status'];
				$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrPlus[$val]['status_date'] = $dateTime;
				$ArrPlus[$val]['catatan_programmer'] = $valx['catatan_programmer'];
				$ArrPlus[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
				$ArrPlus[$val]['spk'] = $valx['spk'];
				$ArrPlus[$val]['id_spk'] = $valx['id_spk'];
				$ArrPlus[$val]['updated_by'] = $valx['updated_by'];
				$ArrPlus[$val]['updated_date'] = $valx['updated_date'];
			}
		}
		$ArrAdd = array();
		if (!empty($restDetail3)) {
			foreach ($restDetail3 as $val => $valx) {
				$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
				$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
				$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
				$ArrAdd[$val]['id_product'] = $valx['id_product'];
				$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
				$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
				$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
				$ArrAdd[$val]['status'] = $valx['status'];
				$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
				$ArrAdd[$val]['status_date'] = $dateTime;
				$ArrAdd[$val]['catatan_programmer'] = $valx['catatan_programmer'];
				$ArrAdd[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
				$ArrAdd[$val]['spk'] = $valx['spk'];
				$ArrAdd[$val]['id_spk'] = $valx['id_spk'];
				$ArrAdd[$val]['updated_by'] = $valx['updated_by'];
				$ArrAdd[$val]['updated_date'] = $valx['updated_date'];
			}
		}

		//DATA SPK
		$kode_spk		= $data['kode_spk'];
		$start_time		= date('Y-m-d H:i:s', strtotime($data['start_time']));
		$finish_time	= date('Y-m-d H:i:s', strtotime($data['finish_time']));
		$cycletime		= str_replace(',', '', $data['cycletime']);
		$total_time		= str_replace(',', '', $data['total_time']);
		$productivity	= str_replace(',', '', $data['productivity']);
		$file_name		= '';
		$next_process	= $data['next_process'];

		$detail			= $data['detail'];

		$ArrEditHeader = [
			'start_time' 	=> $start_time,
			'finish_time' 	=> $finish_time,
			'cycletime' 	=> $cycletime,
			'total_time' 	=> $total_time,
			'productivity' 	=> $productivity,
			'next_process' 	=> $next_process
		];

		//UPLOAD DOCUMENT
		if (!empty($_FILES["upload_spk"]["name"])) {
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
			$name_file      = 'spk_' . date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
			$file_name    	= $name_file . "." . $imageFileType;

			if (!empty($_FILES["upload_spk"]["tmp_name"])) {
				$terupload = move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEditHeader = [
				'start_time' => $start_time,
				'finish_time' => $finish_time,
				'cycletime' => $cycletime,
				'total_time' => $total_time,
				'productivity' => $productivity,
				'upload_spk' => $file_name,
				'next_process' => $next_process
			];
		}

		//DETAIL
		$ArrDetail2x = [];
		foreach ($detail as $key => $value) {
			$ArrDetail2x[$key]['id'] = $value['id'];
			$ArrDetail2x[$key]['status'] = $value['status'];
			$ArrDetail2x[$key]['daycode'] = $value['daycode'];
			$ArrDetail2x[$key]['keterangan'] = $value['ket'];

			//UPLOAD
			$nm_detail = 'inspeksi_' . $value['id'];
			$file_name2			= '';
			if (!empty($_FILES[$nm_detail]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'inspeksi_' . $value['id'] . '_' . date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
				$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name2    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES[$nm_detail]["tmp_name"])) {
					$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
				}
				$ArrDetail2x[$key]['inspeksi'] = $file_name2;
			}
		}

		//check apakah pernah closing ?
		$check_closing = $this->db->get_where('production_detail', array('id' => $id_milik2, 'closing_produksi_date' => NULL))->result();

		// print_r($ArrDetail);
		// print_r($ArrPlus);
		// print_r($ArrAdd);
		// exit;

		$this->db->trans_start();
		if (!empty($check_closing)) {
			if (!empty($ArrDetail)) {
				$this->db->insert_batch('production_real_detail', $ArrDetail);
			}
			if (!empty($ArrPlus)) {
				$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			}
			if (!empty($ArrAdd)) {
				$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			}
		}

		$QUERY_GET = "SELECT
						a.id_produksi AS id_produksi,
						b.id_category AS id_category,
						a.id_product AS id_product,
						b.qty_awal AS product_ke,
						b.qty_akhir AS qty_akhir,
						b.qty AS qty,
						a.status_by AS status_by,
						a.updated_date AS status_date,
						a.id_production_detail AS id_production_detail,
						a.id AS id,
						a.id_spk AS id_spk,
						b.id_milik AS id_milik 
					FROM
						(
							production_real_detail a
							LEFT JOIN update_real_list b ON ((
									a.id_production_detail = b.id 
								))) 
						WHERE 
							a.id_production_detail = '".$valx['id_production_detail']."'
							AND a.updated_date = '".$valx['updated_date']."'
					GROUP BY
						cast( a.updated_date AS DATE ),
						a.id_production_detail 
					ORDER BY
						a.updated_date DESC";
						// echo $QUERY_GET; exit;
		$getData = $this->db->query($QUERY_GET)->result_array();
		
		if(!empty($getData)){
			$ArrWIP = array(
				'id_produksi' => $getData[0]['id_produksi'],
				'id_milik' => $getData[0]['id_milik'],
				'id_production_detail' => $getData[0]['id_production_detail'],
				'qty_akhir' => $getData[0]['qty_akhir'],
				'product_ke' => $getData[0]['product_ke'],
				'id_category' => $getData[0]['id_category'],
				'id_product' => $getData[0]['id_product'],
				'status_date' => $getData[0]['status_date'],
				'qty' => $getData[0]['qty'],
				'insert_date' => $dateTime
			);

			$this->save_report_fg_closing($ArrWIP);

		}

		//Update FLAG
		if (!empty($kode_spk)) {
			$this->db->where('kode_spk', $kode_spk);
			$this->db->where('id_milik', $id_milik);
			$this->db->update('production_spk', $ArrFlagSPK);

			$this->db->where('kode_spk', $kode_spk);
			$this->db->where('created_date', $time_uniq);
			$this->db->where('id_spk', $id_spk);
			$this->db->update('production_spk_parsial', $ArrFlagSPKParsial);

			$this->db->where('id_produksi', $id_produksi);
			$this->db->where('id_milik', $id_milik);
			$this->db->where('kode_spk', $kode_spk);
			$this->db->where('print_merge_date', $print_merge_date);
			$this->db->update('production_detail', $ArrFlagRelease);
		}

		$this->db->where('kode_spk', $kode_spk);
		$this->db->where('no_ipp', str_replace('PRO-', '', $id_produksi));
		$this->db->where('id_milik', $id_milik);
		$this->db->update('production_spk', $ArrEditHeader);

		$this->db->update_batch('production_detail', $ArrDetail2x, 'id');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0,
				'tanda'     => $tanda,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Success process data. Thanks ...',
				'status'	=> 1,
				'tanda'     => $tanda,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
			history('Release QC = ' . $data['id_produksi'] . ' / ' . $data['id_milik'] . ' / ' . $data['id_product']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_report_fg_closing($ArrData){

		$sqlkurs	= "select * from ms_kurs where tanggal <='".date('Y-m-d')."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

        $restCh		= $this->db->select('jalur')->get_where('production_header',array('id_produksi'=>$ArrData['id_produksi']))->result_array();
        
		if(!empty($restCh[0]['jalur'])){
			$HelpDet 	= "estimasi_cost_and_mat";
			$HelpDet2 	= "banding_mat_pro";
			$HelpDet3 	= "bq_product";
			if($restCh[0]['jalur'] == 'FD'){
				$HelpDet = "so_estimasi_cost_and_mat";
				$HelpDet2 	= "banding_so_mat_pro";
				$HelpDet3 	= "bq_product_fd";
			}

			$sqlBy 		= " SELECT
								b.diameter AS diameter,
								b.diameter2 AS diameter2,
								b.pressure AS pressure,
								b.liner AS liner,
								b.man_hours AS man_hours,
								a.direct_labour AS direct_labour,
								a.indirect_labour AS indirect_labour,
								a.machine AS machine,
								a.mould_mandrill AS mould_mandrill,
								a.consumable AS consumable,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) AS foh_consumable,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) AS foh_depresiasi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) AS biaya_gaji_non_produksi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) AS biaya_non_produksi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) AS biaya_rutin_bulanan 
							FROM
								".$HelpDet." a
								INNER JOIN ". $HelpDet3." b ON a.id_milik = b.id
							WHERE id_milik='".$ArrData['id_milik']."' LIMIT 1";
			
			$restBy		= $this->db->query($sqlBy)->result_array();
			
			$sqlBan     = "SELECT * FROM ".$HelpDet2." WHERE id_detail='".$ArrData['id_production_detail']."' LIMIT 1";
			$restBan	= $this->db->query($sqlBan)->result_array();
			// echo $sqlEst."<br>";
			$jumTot     = ($ArrData['qty_akhir'] - $ArrData['product_ke']) + 1;
			
			$sqlInsertDet = "INSERT INTO laporan_per_hari_group
								(id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
								qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
								real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
								consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
								biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs)
								VALUE
								('".$ArrData['id_produksi']."','".$ArrData['id_category']."','".$ArrData['id_product']."',
								'".$restBy[0]['diameter']."','".$restBy[0]['diameter2']."','".$restBy[0]['pressure']."',
								'".$restBy[0]['liner']."','".$ArrData['status_date']."','".$ArrData['product_ke']."',
								'".$ArrData['qty_akhir']."','".$ArrData['qty']."','".date('Y-m-d',strtotime($ArrData['status_date']))."','".$ArrData['id_production_detail']."',
								'".$ArrData['id_milik']."','".$restBan[0]['est_material'] * $jumTot."','".$restBan[0]['est_harga'] * $jumTot."',
								'".$restBan[0]['real_material']."','".$restBan[0]['real_harga']."','".$restBy[0]['direct_labour'] * $jumTot."',
								'".$restBy[0]['indirect_labour'] * $jumTot."','".$restBy[0]['machine'] * $jumTot."',
								'".$restBy[0]['mould_mandrill'] * $jumTot."','".$restBy[0]['consumable'] * $jumTot."',
								'".$restBy[0]['foh_consumable'] * $jumTot."','".$restBy[0]['foh_depresiasi'] * $jumTot."',
								'".$restBy[0]['biaya_gaji_non_produksi'] * $jumTot."','".$restBy[0]['biaya_non_produksi'] * $jumTot."',
								'".$restBy[0]['biaya_rutin_bulanan'] * $jumTot."','system','".$ArrData['insert_date']."','".$restBy[0]['man_hours'] * $jumTot."','".$restBan[0]['real_harga_rp']."','".$kurs."')
							";
			// echo $sqlInsertDet;
			// exit;
			$this->db->query($sqlInsertDet);
		}
	}

	public function real_send_upload_dokumen()
	{
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;
		$id_produksi			= $data['id_produksi'];
		$id_product				= $data['id_product'];
		$id_milik				= $data['id_milik']; //uniq production detail
		$id_milik2				= $data['id_milik2']; //id milik product
		$tanda				    = $data['status'];
		$first_id				= $data['first_id'];
		$dateTime = date('Y-m-d H:i:s');

		//DATA SPK
		$kode_spk				= $data['kode_spk'];

		$start_time				= date('Y-m-d H:i:s', strtotime($data['start_time']));
		$finish_time			= date('Y-m-d H:i:s', strtotime($data['finish_time']));
		$cycletime				= str_replace(',', '', $data['cycletime']);
		$total_time				= str_replace(',', '', $data['total_time']);
		$productivity			= str_replace(',', '', $data['productivity']);
		$file_name			= '';
		$next_process			= $data['next_process'];

		$detail				    = $data['detail'];
		$dateTime = date('Y-m-d H:i:s');

		$ArrEditHeader = [
			'start_time' => $start_time,
			'finish_time' => $finish_time,
			'cycletime' => $cycletime,
			'total_time' => $total_time,
			'productivity' => $productivity,
			'next_process' => $next_process
		];

		//UPLOAD DOCUMENT
		if (!empty($_FILES["upload_spk"]["name"])) {
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
			$name_file      = 'spk_' . date('Ymdhis');
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
				'cycletime' => $cycletime,
				'total_time' => $total_time,
				'productivity' => $productivity,
				'upload_spk' => $file_name,
				'next_process' => $next_process
			];
		}

		//DETAIL
		$ArrDetail2x = [];
		foreach ($detail as $key => $value) {
			$ArrDetail2x[$key]['id'] = $value['id'];
			$ArrDetail2x[$key]['status'] = $value['status'];
			$ArrDetail2x[$key]['daycode'] = $value['daycode'];
			$ArrDetail2x[$key]['keterangan'] = $value['ket'];

			//UPLOAD
			$nm_detail = 'inspeksi_' . $value['id'];
			$file_name2			= '';
			if (!empty($_FILES[$nm_detail]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'inspeksi_' . $value['id'] . '_' . date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
				$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name2    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES[$nm_detail]["tmp_name"])) {
					// if($imageFileType <> 'pdf'){
					// 	$Arr_Data	= array(
					// 		'pesan'		=>'Hanya file pdf yang diperbolehkan !!!',
					// 		'status'	=> 0
					// 	);
					// 	echo json_encode($Arr_Data);
					// 	return false;
					// }
					// if($imageFileType == 'pdf'){
					$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					// if ($terupload) {
					//     echo "Upload berhasil!<br/>";
					// } else {
					//     echo "Upload Gagal!";
					// }
					// }
				}

				$ArrDetail2x[$key]['inspeksi'] = $file_name2;
			}
		}

		// print_r($ArrDetail);
		// print_r($ArrPlus);
		// print_r($ArrAdd);
		// exit;

		$this->db->trans_start();
		$this->db->where('kode_spk', $kode_spk);
		$this->db->where('no_ipp', str_replace('PRO-', '', $id_produksi));
		$this->db->where('id_milik', $id_milik);
		$this->db->update('production_spk', $ArrEditHeader);

		$this->db->update_batch('production_detail', $ArrDetail2x, 'id');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0,
				'tanda'     => $tanda,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Success process data. Thanks ...',
				'status'	=> 1,
				'tanda'     => $tanda,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
			history('Upload dokumen QC = ' . $data['id_produksi'] . ' / ' . $data['id_milik'] . ' / ' . $data['id_product']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function real_send_release_fg()
	{
		// ini_set('memory_limit', '255M');
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$tanda			= $data['status'];
		$datetime 		= date('Y-m-d H:i:s');
		$datetimeNm 	= date('Ymdhis');
		$detail			= $data['check'];
		$detail_data	= $data['detail'];
		$id_produksi	= $data['id_produksi'];
		$no_ipp			= str_replace('PRO-', '', $data['id_produksi']);
		$id_product		= $data['id_product'];
		$id_milik		= $data['id_milik'];
		$id_milik2		= $data['id_milik2'];
		$time_uniq				= $data['time_uniq'];
		$first_id				= $data['first_id'];


		$panjang_split = 0;
		foreach ($detail as $key => $value) {
			$panjang_split += $data['total_cutting_' . $key];
		}

		// echo $panjang_split;
		// print_r($detail);
		// print_r($detail_data);
		$UpdateData = [];
		$ArrHistFG = [];
		$ArrIdPro = [];
		foreach ($detail as $key => $value) {
			$ArrIdPro[] = $value;
			$UpdateData[$key]['id'] = $value;
			$UpdateData[$key]['status'] = $detail_data[$key]['status'];
			$UpdateData[$key]['daycode'] = $detail_data[$key]['daycode'];
			$UpdateData[$key]['keterangan'] = $detail_data[$key]['ket'];
			$UpdateData[$key]['qc_pass_date'] = (!empty($detail_data[$key]['qc_pass_date'])) ? date('Y-m-d', strtotime($detail_data[$key]['qc_pass_date'])) : NULL;
			$UpdateData[$key]['fg_by'] = $data_session['ORI_User']['username'];
			$UpdateData[$key]['fg_date'] = $datetime;
			$UpdateData[$key]['resin'] = $detail_data[$key]['resin'];
			$UpdateData[$key]['release_to_costing_by'] = $data_session['ORI_User']['username'];
			$UpdateData[$key]['release_to_costing_date'] = $datetime;

			$ArrHistFG[$key]['tipe_product'] = $tanda;
			$ArrHistFG[$key]['id_product'] = $value;
			$ArrHistFG[$key]['id_milik'] = $id_milik;
			$ArrHistFG[$key]['tipe'] = 'in';
			$ArrHistFG[$key]['kode'] = $detail_data[$key]['daycode'];
			$ArrHistFG[$key]['tanggal'] = date('Y-m-d');
			$ArrHistFG[$key]['keterangan'] = 'qc product '.$tanda;
			$ArrHistFG[$key]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrHistFG[$key]['hist_date'] = $datetime;


			//UPLOAD DOCUMENT
			if (!empty($_FILES["inspeksi_" . $value]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'inspeksi_qc_' . $value . '_' . $datetimeNm;
				$target_file    = $target_dir . basename($_FILES["inspeksi_" . $value]["name"]);
				$name_file_ori  = basename($_FILES["inspeksi_" . $value]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES["inspeksi_" . $value]["tmp_name"])) {
					move_uploaded_file($_FILES["inspeksi_" . $value]["tmp_name"], $nama_upload);
					$UpdateData[$key]['inspeksi'] = $file_name;
				}
			}
		}

		//NEW FG
		$getDetailProduksi	= $this->db->select('start_qc_date, id_milik')->get_where('production_detail',array('id'=>$value))->result_array();
		$start_qc_date 		= (!empty($getDetailProduksi[0]['start_qc_date']))?$getDetailProduksi[0]['start_qc_date']:null;

		$ArrInsertFGNilai = [];
		if(!empty($start_qc_date)){
			$getDetailFG	= $this->db->get_where('laporan_per_hari_group',array('insert_date'=>$start_qc_date,'id_milik'=>$getDetailProduksi[0]['id_milik']))->result_array();
			$QTY_GROUP_SPK 	= $getDetailFG[0]['qty_akhir'] - $getDetailFG[0]['qty_awal'] + 1;
			$QTY_QC			= COUNT($detail);
			
			$ArrInsertFGNilai[0]['id_produksi']				= $getDetailFG[0]['id_produksi'];
			$ArrInsertFGNilai[0]['id_category']				= $getDetailFG[0]['id_category'];
			$ArrInsertFGNilai[0]['id_product']				= $getDetailFG[0]['id_product'];
			$ArrInsertFGNilai[0]['diameter']				= $getDetailFG[0]['diameter'];
			$ArrInsertFGNilai[0]['diameter2']				= $getDetailFG[0]['diameter2'];
			$ArrInsertFGNilai[0]['pressure']				= $getDetailFG[0]['pressure'];
			$ArrInsertFGNilai[0]['liner']					= $getDetailFG[0]['liner'];
			$ArrInsertFGNilai[0]['status_date']				= $getDetailFG[0]['insert_date']; // insert dari group
			$ArrInsertFGNilai[0]['qty_awal']				= $QTY_QC; //qty qc
			$ArrInsertFGNilai[0]['qty_akhir']				= $QTY_GROUP_SPK; //qtc spk
			$ArrInsertFGNilai[0]['qty']						= $getDetailFG[0]['qty'];
			$ArrInsertFGNilai[0]['date']					= $getDetailFG[0]['date'];
			$ArrInsertFGNilai[0]['id_production_detail']	= $getDetailFG[0]['id_production_detail'];
			$ArrInsertFGNilai[0]['id_milik']				= $getDetailFG[0]['id_milik'];
			$ArrInsertFGNilai[0]['man_hours']				= $getDetailFG[0]['man_hours'];
			$ArrInsertFGNilai[0]['kurs']					= $getDetailFG[0]['kurs'];
			$ArrInsertFGNilai[0]['insert_by']				= $data_session['ORI_User']['username'];
			$ArrInsertFGNilai[0]['insert_date']				= $datetime;

			$ArrInsertFGNilai[0]['est_material']			= (!empty($getDetailFG[0]['est_material']) AND $getDetailFG[0]['est_material'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['est_material'] : 0;
			$ArrInsertFGNilai[0]['est_harga']				= (!empty($getDetailFG[0]['est_harga']) AND $getDetailFG[0]['est_harga'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['est_harga'] : 0;
			$ArrInsertFGNilai[0]['real_material']			= (!empty($getDetailFG[0]['real_material']) AND $getDetailFG[0]['real_material'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['real_material'] : 0;
			$ArrInsertFGNilai[0]['real_harga']				= (!empty($getDetailFG[0]['real_harga']) AND $getDetailFG[0]['real_harga'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['real_harga'] : 0;
			$ArrInsertFGNilai[0]['direct_labour']			= (!empty($getDetailFG[0]['direct_labour']) AND $getDetailFG[0]['direct_labour'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['direct_labour'] : 0;
			$ArrInsertFGNilai[0]['indirect_labour']			= (!empty($getDetailFG[0]['indirect_labour']) AND $getDetailFG[0]['indirect_labour'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['indirect_labour'] : 0;
			$ArrInsertFGNilai[0]['machine']					= (!empty($getDetailFG[0]['machine']) AND $getDetailFG[0]['machine'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['machine'] : 0;
			$ArrInsertFGNilai[0]['mould_mandrill']			= (!empty($getDetailFG[0]['mould_mandrill']) AND $getDetailFG[0]['mould_mandrill'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['mould_mandrill'] : 0;
			$ArrInsertFGNilai[0]['consumable']				= (!empty($getDetailFG[0]['consumable']) AND $getDetailFG[0]['consumable'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['consumable'] : 0;
			$ArrInsertFGNilai[0]['foh_consumable']			= (!empty($getDetailFG[0]['foh_consumable']) AND $getDetailFG[0]['foh_consumable'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['foh_consumable'] : 0;
			$ArrInsertFGNilai[0]['foh_depresiasi']			= (!empty($getDetailFG[0]['foh_depresiasi']) AND $getDetailFG[0]['foh_depresiasi'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['foh_depresiasi'] : 0;
			$ArrInsertFGNilai[0]['biaya_gaji_non_produksi']	= (!empty($getDetailFG[0]['biaya_gaji_non_produksi']) AND $getDetailFG[0]['biaya_gaji_non_produksi'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['biaya_gaji_non_produksi'] : 0;
			$ArrInsertFGNilai[0]['biaya_non_produksi']		= (!empty($getDetailFG[0]['biaya_non_produksi']) AND $getDetailFG[0]['biaya_non_produksi'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['biaya_non_produksi'] : 0;
			$ArrInsertFGNilai[0]['biaya_rutin_bulanan']		= (!empty($getDetailFG[0]['biaya_rutin_bulanan']) AND $getDetailFG[0]['biaya_rutin_bulanan'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['biaya_rutin_bulanan'] : 0;
			$ArrInsertFGNilai[0]['real_harga_rp']			= (!empty($getDetailFG[0]['real_harga_rp']) AND $getDetailFG[0]['real_harga_rp'] > 0)?$QTY_QC / $QTY_GROUP_SPK * $getDetailFG[0]['real_harga_rp'] : 0;
		}

		//END NEW FG

		$panjang		= get_name('so_detail_header', 'length', 'id', $id_milik);
		$kode_spk		= $data['kode_spk'];

		$kode_pro = $kode_spk . '/' . $time_uniq;

		$total_qty		= $data['total_qty'];
		$total_cek		= COUNT($data['check']);
		$first_id		= $data['first_id'];

		$ArrEditHeader = [
			'fg_by' => $data_session['ORI_User']['username'],
			'fg_date' => $datetime,
			'release_to_costing_by' => $data_session['ORI_User']['username'],
			'release_to_costing_date' => $datetime
		];

		$id_gudang_wip = 14;
		$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);

		$id_gudang_fg = 15;
		$kode_gudang_fg = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_fg);

		$SUM_DETAIL = $this->db->select('batch_number, SUM(material_terpakai) AS berat')->from('production_real_detail')->where('catatan_programmer', $kode_pro)->where('id_production_detail', $first_id)->group_by('batch_number')->get()->result_array();
		$SUM_PLUS 	= $this->db->select('batch_number, SUM(material_terpakai) AS berat')->from('production_real_detail_plus')->where('catatan_programmer', $kode_pro)->where('id_production_detail', $first_id)->group_by('batch_number')->get()->result_array();
		$SUM_ADD 	= $this->db->select('batch_number, SUM(material_terpakai) AS berat')->from('production_real_detail_add')->where('catatan_programmer', $kode_pro)->where('id_production_detail', $first_id)->group_by('batch_number')->get()->result_array();

		$ArrMerge = array_merge($SUM_DETAIL, $SUM_PLUS, $SUM_ADD);
		$temp = [];
		foreach ($ArrMerge as $val => $value) {
			if (!array_key_exists($value['batch_number'], $temp)) {
				$temp[$value['batch_number']] = 0;
			}
			if ($panjang_split < 1) {
				$temp[$value['batch_number']] += $value['berat'] / $total_qty * $total_cek;
			} else {
				$temp[$value['batch_number']] += ($value['berat'] / $total_qty * $total_cek) / $panjang * $panjang_split;
			}
		}
		// uploadnnya masing masing soalnnya,
		// print_r($temp);
		// exit;

		//UPDATE STOCK
		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock', array('id_gudang' => $id_gudang_wip, 'id_material' => $key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);

			if (!empty($rest_pusat)) {
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang_wip;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_fg;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_fg;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan stock WIP ' . $no_ipp . ' / ' . $id_product . ' (' . $total_cek . ')';
				$ArrHist[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$key]['update_date'] 		= $datetime;
			} else {
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='" . $key . "' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang_wip;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_fg;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_fg;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan stock WIP (insert new) ' . $no_ipp . ' / ' . $id_product . ' (' . $total_cek . ')';
				$ArrHistInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP

			$rest_pusat = $this->db->get_where('warehouse_stock', array('id_gudang' => $id_gudang_fg, 'id_material' => $key))->result();

			if (!empty($rest_pusat)) {
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value;
				$ArrStock2[$key]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_fg;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_fg;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_fg;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_fg;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk;
				$ArrHist2[$key]['jumlah_mat'] 		= $value;
				$ArrHist2[$key]['ket'] 				= 'penambahan stock FG ' . $no_ipp . ' / ' . $id_product . ' (' . $total_cek . ')';
				$ArrHist2[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist2[$key]['update_date'] 		= $datetime;
			} else {
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='" . $key . "' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_fg;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_fg;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value;
				$ArrStockInsert2[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_fg;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_fg;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang_wip;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_fg;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_fg;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan stock FG (insert new) ' . $no_ipp . ' / ' . $id_product . ' (' . $total_cek . ')';
				$ArrHistInsert2[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrMerge);
		// exit;

		// exit;

		$this->db->trans_start();
		// $this->db->where_in('id',$detail);
		// $this->db->update('production_detail',$ArrEditHeader);
		if (!empty($detail)) {
			//insert_jurnal_qc($detail, $kode_pro);
		}

		if (!empty($ArrHistFG)) {
			$this->db->insert_batch('history_product_fg', $ArrHistFG);
		}

		$this->db->update_batch('production_detail', $UpdateData, 'id');

		if (!empty($ArrStock)) {
			$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
		}
		if (!empty($ArrHist)) {
			$this->db->insert_batch('warehouse_history', $ArrHist);
		}

		if (!empty($ArrStockInsert)) {
			$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
		}
		if (!empty($ArrHistInsert)) {
			$this->db->insert_batch('warehouse_history', $ArrHistInsert);
		}

		if (!empty($ArrStock2)) {
			$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
		}
		if (!empty($ArrHist2)) {
			$this->db->insert_batch('warehouse_history', $ArrHist2);
		}

		if (!empty($ArrStockInsert2)) {
			$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
		}
		if (!empty($ArrHistInsert2)) {
			$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
		}

		if(!empty($ArrInsertFGNilai)){
			$this->db->insert_batch('laporan_per_hari_action', $ArrInsertFGNilai);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0,
				'tanda'     => $tanda,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Success process data. Thanks ...',
				'status'	=> 1,
				'tanda'     => $tanda,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
			history('Release QC to FG = ' . $data['id_produksi'] . ' / ' . $data['id_milik'] . ' / ' . $data['id_product']);
			$this->close_jurnal_finish_good($ArrIdPro,$kode_pro,$id_milik2);
		}
		echo json_encode($Arr_Kembali);
	}

	public function modalEditReal()
	{
		$kode_spk 	= $this->uri->segment(3);
		$id_produksi = $this->uri->segment(4);
		$id_milik 	= $this->uri->segment(5);
		$id_pro_detail 	= $this->uri->segment(6);

		$get_time = get_name('production_detail', 'upload_date', 'id', $id_pro_detail);

		$get_split_code = $this->db->order_by('id', 'ASC')->get_where('production_detail', array('kode_spk' => $kode_spk, 'id_milik' => $id_milik, 'id_produksi' => $id_produksi, 'upload_real' => 'Y', 'upload_real2' => 'Y', 'upload_date' => $get_time))->result_array();
		$get_spk = $this->db->get_where('production_spk', array('kode_spk' => $kode_spk, 'id_milik' => $id_milik, 'no_ipp' => str_replace('PRO-', '', $id_produksi)))->result();
		$costcenter 	= $this->db->get_where('costcenter', array('deleted' => 'N', 'id_dept' => '10'))->result_array();

		$ArrGroup = [];
		foreach ($get_split_code as $key => $value) {
			$IMPLODE = explode('.', $value['product_code']);
			$ArrGroup[] = $IMPLODE[0] . '.' . $value['product_ke'];
		}
		// print_r($get_spk); exit;
		$explode = implode(', ', $ArrGroup);
		$data = [
			'get_split_code' => $get_split_code,
			'costcenter' => $costcenter,
			'kode_spk' => $kode_spk,
			'get_spk' => $get_spk,
			'id_produksi' => $id_produksi,
			'id_product' => $get_split_code[0]['id_product'], 
			'id_milik' => $id_milik,
			'id_milik2' => $get_split_code[0]['id'],
			'kode_product' => $explode,
			'time_uniq' => $get_time,
			'first_id' => $id_pro_detail
		];
		$this->load->view('Qc/modalEditReal', $data);
	}

	public function real_before_send()
	{
		$data 					= $this->input->post();
		$data_session			= $this->session->userdata;

		$kode_spk				= $data['kode_spk'];
		$id_produksi			= $data['id_produksi'];
		$id_milik				= $data['id_milik'];

		$start_time				= date('Y-m-d H:i:s', strtotime($data['start_time']));
		$finish_time			= date('Y-m-d H:i:s', strtotime($data['finish_time']));
		$cycletime				= str_replace(',', '', $data['cycletime']);
		$total_time				= str_replace(',', '', $data['total_time']);
		$productivity			= str_replace(',', '', $data['productivity']);
		$file_name			= '';
		$next_process			= $data['next_process'];

		$detail				    = $data['detail'];
		$dateTime = date('Y-m-d H:i:s');

		$ArrEditHeader = [
			'start_time' => $start_time,
			'finish_time' => $finish_time,
			'cycletime' => $cycletime,
			'total_time' => $total_time,
			'productivity' => $productivity,
			'next_process' => $next_process
		];

		//UPLOAD DOCUMENT
		if (!empty($_FILES["upload_spk"]["name"])) {
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
			$name_file      = 'spk_' . date('Ymdhis');
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
				'cycletime' => $cycletime,
				'total_time' => $total_time,
				'productivity' => $productivity,
				'upload_spk' => $file_name,
				'next_process' => $next_process
			];
		}

		//DETAIL
		$ArrDetail = [];
		foreach ($detail as $key => $value) {
			$ArrDetail[$key]['id'] = $value['id'];
			$ArrDetail[$key]['status'] = $value['status'];
			$ArrDetail[$key]['daycode'] = $value['daycode'];
			$ArrDetail[$key]['keterangan'] = $value['ket'];

			//UPLOAD
			$nm_detail = 'inspeksi_' . $value['id'];
			$file_name2			= '';
			if (!empty($_FILES[$nm_detail]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'inspeksi_' . $value['id'] . '_' . date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
				$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name2    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES[$nm_detail]["tmp_name"])) {
					// if($imageFileType <> 'pdf'){
					// 	$Arr_Data	= array(
					// 		'pesan'		=>'Hanya file pdf yang diperbolehkan !!!',
					// 		'status'	=> 0
					// 	);
					// 	echo json_encode($Arr_Data);
					// 	return false;
					// }
					// if($imageFileType == 'pdf'){
					$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					// if ($terupload) {
					//     echo "Upload berhasil!<br/>";
					// } else {
					//     echo "Upload Gagal!";
					// }
					// }
				}

				$ArrDetail[$key]['inspeksi'] = $file_name2;
			}
		}



		// print_r($ArrEditHeader);
		// print_r($ArrDetail);
		// exit;

		$this->db->trans_start();
		$this->db->where('kode_spk', $kode_spk);
		$this->db->where('no_ipp', str_replace('PRO-', '', $id_produksi));
		$this->db->where('id_milik', $id_milik);
		$this->db->update('production_spk', $ArrEditHeader);

		$this->db->update_batch('production_detail', $ArrDetail, 'id');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Success process data. Thanks ...',
				'status'	=> 1,
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
			);
			history('QC Check temp = ' . $data['kode_spk'] . ' / ' . $data['id_produksi'] . ' / ' . $data['id_milik']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function spool()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/spool';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Quality Control Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data qc spool');
		$this->load->view('Qc/spool', $data);
	}

	public function server_side_spool()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spool(
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
			$release = "";
			if (empty($row['lock_spool_date'])) {
				$edit = "<a href='" . base_url('ppic/edit_spool/' . $row['spool_induk']) . "' class='btn btn-sm btn-primary' title='Edit'><i class='fa fa-edit'></i></a>";
				$release = "<button type='button' class='btn btn-sm btn-success lock_spool' data-spool='" . $row['spool_induk'] . "' title='Lock Spool'><i class='fa fa-check'></i></button>";
			}
			$view = "<a href='" . base_url('ppic/view_spool/' . $row['spool_induk']) . "' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";

			$check	= "<button class='btn btn-sm btn-success check_real' title='Release QC' data-spool_induk='" . $row['spool_induk'] . "'><i class='fa fa-check'></i></button>";


			$get_split_ipp = $this->db->select('id_produksi, id_milik, kode_spool, product_code, product_ke, cutting_ke, no_drawing, id_category AS nm_product, no_spk, COUNT(id) AS qty, sts, length, status_tanki, nm_tanki')->group_by('sts, id_milik')->order_by('id','asc')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
			$ArrNo_Spool = [];
			$ArrNo_IPP = [];
			$ArrNo_Drawing = [];
			$ArrNo_SPK = [];
			foreach ($get_split_ipp as $key => $value) { $key++;

				$no_spk 		= $value['no_spk'];
				$ArrNo_IPP[] 	= str_replace('PRO-','',$value['id_produksi']);
				$ArrNo_Spool[] 	= $value['kode_spool'];

                $ArrNo_Drawing[] = $value['no_drawing'];
				
				$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
				
				$IMPLODE = explode('-', $value['kode_spool']);

				$sts = $value['sts'];

				$product 	= strtoupper($value['nm_product']).', '. spec_bq2($value['id_milik']);
				if($sts == 'cut'){
					$product 	= strtoupper($value['nm_product']).', '. spec_bq2($value['id_milik']).', cut '.number_format($value['length']);
				}
				if($value['status_tanki'] == 'tanki'){
					$product 	= strtoupper($value['nm_tanki']);
				}

				$no = sprintf('%02s', $key);

				$ArrNo_SPK[] = $no.'. <span class="text-bold text-primary">['.$IMPLODE[0].'/'.$no_spk.']</span> <span class="text-bold text-success">'.strtoupper($sts).'</span><span class="text-bold"> ['.$value['qty'].' pcs]</span> '.$product;
			}
			// print_r($ArrGroup); exit;
			$explode_spo = implode('<br>',array_unique($ArrNo_Spool));
			$explode_ipp = implode('<br>',array_unique($ArrNo_IPP));
			$explode_drawing = implode('<br>',array_unique($ArrNo_Drawing));
			$explode_spk = implode('<br>',$ArrNo_SPK);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['spool_induk'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $explode_spo . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_drawing . "</div>";
			$nestedData[]	= "<div align='center'>" . $explode_ipp . "</div>";
			$nestedData[]	= "<div align='left'>" . $explode_spk . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['spool_by'] . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y H:i:s', strtotime($row['spool_date'])) . "</div>";
			$nestedData[]	= "<div align='center'>
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

	public function query_data_spool($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";
		// if($no_ipp <> 0){
		// 	$where = " AND a.id_produksi='".$no_ipp."' ";
		// }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				spool_group a,
				(SELECT @row:=0) r
			WHERE 1=1 " . $where . " " . $where2 . "
				AND a.lock_spool_date IS NOT NULL
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.kode_spool LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.spool_induk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY
				a.spool_induk
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_spool',
			2 => 'kode_spk',
			3 => 'kode_spk',
			4 => 'kode_spk'
		);

		$sql .= " ORDER BY a.spool_induk DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalEditReal_Spool()
	{
		$spool_induk 	= $this->uri->segment(3);

		$get_split_code = $this->db->order_by('kode_spool', 'ASC')->get_where('spool_group', array('spool_induk' => $spool_induk))->result_array();
		$get_spk = $this->db->get_where('spool', array('spool_induk' => $spool_induk))->result();
		$costcenter 	= $this->db->get_where('costcenter', array('deleted' => 'N', 'id_dept' => '10'))->result_array();

		$ArrGroup = [];
		foreach ($get_split_code as $key => $value) {
			$ArrGroup[] = $value['kode_spool'];
		}
		// print_r($get_spk); exit;
		$explode = implode(', ', array_unique($ArrGroup));

		$result = $this->db->group_by('kode_spool')->order_by('kode_spool', 'asc')->get_where('spool_group_all', array('spool_induk' => $spool_induk))->result_array();
		$data = [
			'get_split_code' => $get_split_code,
			'costcenter' => $costcenter,
			'get_spk' => $get_spk,
			'result' => $result,
			'spool_induk' => $spool_induk,
			'kode_product' => $explode,
			'tanki_model' => $this->tanki_model
		];
		$this->load->view('Qc/modalEditReal_Spool', $data);
	}

	public function real_send_spool()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$spool_induk	= $data['spool_induk'];
		$dateTime 		= date('Y-m-d H:i:s');
		$dateTimeLabel	= date('Ymdhis');

		$ArrFlagRelease = [
			'release_spool_by' => $data_session['ORI_User']['username'],
			'release_spool_date' => $dateTime
		];

		//DATA SPK
		$start_time		= date('Y-m-d H:i:s', strtotime($data['start_time']));
		$finish_time	= date('Y-m-d H:i:s', strtotime($data['finish_time']));
		$cycletime		= str_replace(',', '', $data['cycletime']);
		$total_time		= str_replace(',', '', $data['total_time']);
		$productivity	= str_replace(',', '', $data['productivity']);
		$file_name		= '';
		$next_process	= $data['next_process'];

		$detail			= $data['detail'];
		$detail_spool	= $data['detail_spool'];

		$ArrEditHeader = [
			'start_time' => $start_time,
			'finish_time' => $finish_time,
			'cycletime' => $cycletime,
			'total_time' => $total_time,
			'productivity' => $productivity,
			'next_process' => $next_process
		];

		//UPLOAD DOCUMENT
		if (!empty($_FILES["upload_spk"]["name"])) {
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
			$name_file      = 'spkspool_' . $dateTimeLabel;
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
			$file_name    	= $name_file . "." . $imageFileType;

			if (!empty($_FILES["upload_spk"]["tmp_name"])) {
				$terupload = move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEditHeader = [
				'start_time' => $start_time,
				'finish_time' => $finish_time,
				'cycletime' => $cycletime,
				'total_time' => $total_time,
				'productivity' => $productivity,
				'upload_spk' => $file_name,
				'next_process' => $next_process
			];
		}

		//DETAIL
		$ArrDetail = [];
		$ArrDetail2 = [];
		$ArrKeyJurnal = [];
		foreach ($detail as $key => $value) {
			$EXPLODE 	= explode('-', $value['id']);
			$id_pro 	= $EXPLODE[0];
			$status 	= $EXPLODE[1];

			if ($status == 'loose') {
				$ArrKeyJurnal[] = $id_pro;
				$ArrDetail[$key]['id'] = $id_pro;
				$ArrDetail[$key]['sp_status'] = $value['sp_status'];
				$ArrDetail[$key]['sp_daycode'] = $value['sp_daycode'];
				$ArrDetail[$key]['sp_keterangan'] = $value['sp_ket'];

				//UPLOAD
				$nm_detail = 'inspeksi_' . $value['id'];
				$file_name2			= '';
				if (!empty($_FILES[$nm_detail]["name"])) {
					$target_dir     = "assets/file/produksi/";
					$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
					$name_file      = 'spool_inspeksi_' . $value['id'] . '_' . $dateTimeLabel;
					$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
					$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
					$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
					$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
					$file_name2    	= $name_file . "." . $imageFileType;

					if (!empty($_FILES[$nm_detail]["tmp_name"])) {
						$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					}

					$ArrDetail[$key]['sp_inspeksi'] = $file_name2;
				}
			}

			if ($status == 'cut') {
				$ArrDetail2[$key]['id'] = $id_pro;
				$ArrDetail2[$key]['sp_status'] = $value['sp_status'];
				$ArrDetail2[$key]['sp_daycode'] = $value['sp_daycode'];
				$ArrDetail2[$key]['sp_keterangan'] = $value['sp_ket'];

				//UPLOAD
				$nm_detail = 'inspeksi_' . $value['id'];
				$file_name2			= '';
				if (!empty($_FILES[$nm_detail]["name"])) {
					$target_dir     = "assets/file/produksi/";
					$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
					$name_file      = 'spool_inspeksi_' . $value['id'] . '_' . $dateTimeLabel;
					$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
					$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
					$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
					$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
					$file_name2    	= $name_file . "." . $imageFileType;

					if (!empty($_FILES[$nm_detail]["tmp_name"])) {
						$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					}

					$ArrDetail2[$key]['sp_inspeksi'] = $file_name2;
				}
			}
		}

		//DETAIL SPOOL
		$ArrDetailSpool = [];
		foreach ($detail_spool as $key => $value) {
			$ArrDetailSpool[$key]['kode_spool'] 			= $value['id'];
			$ArrDetailSpool[$key]['sp_group_status'] 		= 'Y';
			$ArrDetailSpool[$key]['sp_group_daycode'] 		= $value['sp_daycode'];
			$ArrDetailSpool[$key]['sp_group_keterangan'] 	= $value['sp_ket'];

			//UPLOAD
			$nm_detail = 'inspeksi_spool_' . $value['id'];
			$file_name2			= '';
			if (!empty($_FILES[$nm_detail]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'spool_group_inspeksi_' . $value['id'] . '_' . $dateTimeLabel;
				$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
				$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name2    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES[$nm_detail]["tmp_name"])) {
					$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
				}

				$ArrDetailSpool[$key]['sp_group_inspeksi'] = $file_name2;
			}
		}
		// print_r($ArrEditHeader);
		// print_r($ArrDetail);
		// exit;

		$this->db->trans_start();

		if (!empty($ArrKeyJurnal)) {
			insert_jurnal_qc_spool($ArrKeyJurnal, $spool_induk);
		}
		//Update FLAG
		$this->db->where('spool_induk', $spool_induk);
		$this->db->update('production_detail', $ArrFlagRelease);

		$this->db->where('spool_induk', $spool_induk);
		$this->db->update('so_cutting_detail', $ArrFlagRelease);

		$this->db->where('spool_induk', $spool_induk);
		$this->db->update('deadstok', $ArrFlagRelease);

		$this->db->where('spool_induk', $spool_induk);
		$this->db->update('spool', $ArrEditHeader);

		if (!empty($ArrDetail)) {
			$this->db->update_batch('production_detail', $ArrDetail, 'id');
		}

		if (!empty($ArrDetail2)) {
			$this->db->update_batch('so_cutting_detail', $ArrDetail2, 'id');
		}

		if (!empty($ArrDetailSpool)) {
			$this->db->where('spool_induk', $spool_induk);
			$this->db->update_batch('production_detail', $ArrDetailSpool, 'kode_spool');
		}

		if (!empty($ArrDetailSpool)) {
			$this->db->where('spool_induk', $spool_induk);
			$this->db->update_batch('so_cutting_detail', $ArrDetailSpool, 'kode_spool');
		}

		if (!empty($ArrDetailSpool)) {
			$this->db->where('spool_induk', $spool_induk);
			$this->db->update_batch('deadstok', $ArrDetailSpool, 'kode_spool');
		}
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
			history('Release QC SPOOL = ' . $spool_induk);
			$this->SpoolToFG_Report($spool_induk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function real_before_send_spool()
	{
		$data 				= $this->input->post();
		$data_session		= $this->session->userdata;

		$spool_induk		= $data['spool_induk'];

		$start_time			= date('Y-m-d H:i:s', strtotime($data['start_time']));
		$finish_time		= date('Y-m-d H:i:s', strtotime($data['finish_time']));
		$cycletime			= str_replace(',', '', $data['cycletime']);
		$total_time			= str_replace(',', '', $data['total_time']);
		$productivity		= str_replace(',', '', $data['productivity']);
		$file_name			= '';
		$next_process		= $data['next_process'];

		$detail				= $data['detail'];
		$detail_spool		= $data['detail_spool'];
		$dateTime 			= date('Y-m-d H:i:s');
		$dateTimeLabel 		= date('Ymdhis');

		$ArrEditHeader = [
			'start_time' 	=> $start_time,
			'finish_time' 	=> $finish_time,
			'cycletime' 	=> $cycletime,
			'total_time' 	=> $total_time,
			'productivity' 	=> $productivity,
			'next_process' 	=> $next_process
		];

		//UPLOAD DOCUMENT
		if (!empty($_FILES["upload_spk"]["name"])) {
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
			$name_file      = 'spkspool_' . $dateTimeLabel;
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
			$file_name    	= $name_file . "." . $imageFileType;

			if (!empty($_FILES["upload_spk"]["tmp_name"])) {
				$terupload = move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEditHeader = [
				'start_time' => $start_time,
				'finish_time' => $finish_time,
				'cycletime' => $cycletime,
				'total_time' => $total_time,
				'productivity' => $productivity,
				'upload_spk' => $file_name,
				'next_process' => $next_process
			];
		}

		//DETAIL
		$ArrDetail = [];
		$ArrDetail2 = [];

		foreach ($detail as $key => $value) {
			$EXPLODE 	= explode('-', $value['id']);
			$id_pro 	= $EXPLODE[0];
			$status 	= $EXPLODE[1];

			if ($status == 'loose') {
				$ArrDetail[$key]['id'] = $id_pro;
				$ArrDetail[$key]['sp_status'] = $value['sp_status'];
				$ArrDetail[$key]['sp_daycode'] = $value['sp_daycode'];
				$ArrDetail[$key]['sp_keterangan'] = $value['sp_ket'];

				//UPLOAD
				$nm_detail = 'inspeksi_' . $value['id'];
				$file_name2			= '';
				if (!empty($_FILES[$nm_detail]["name"])) {
					$target_dir     = "assets/file/produksi/";
					$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
					$name_file      = 'spool_inspeksi_' . $value['id'] . '_' . $dateTime;
					$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
					$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
					$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
					$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
					$file_name2    	= $name_file . "." . $imageFileType;

					if (!empty($_FILES[$nm_detail]["tmp_name"])) {
						$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					}

					$ArrDetail[$key]['sp_inspeksi'] = $file_name2;
				}
			}

			if ($status == 'cut') {
				$ArrDetail2[$key]['id'] = $id_pro;
				$ArrDetail2[$key]['sp_status'] = $value['sp_status'];
				$ArrDetail2[$key]['sp_daycode'] = $value['sp_daycode'];
				$ArrDetail2[$key]['sp_keterangan'] = $value['sp_ket'];

				//UPLOAD
				$nm_detail = 'inspeksi_' . $value['id'];
				$file_name2			= '';
				if (!empty($_FILES[$nm_detail]["name"])) {
					$target_dir     = "assets/file/produksi/";
					$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
					$name_file      = 'spool_inspeksi_' . $value['id'] . '_' . $dateTime;
					$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
					$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
					$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
					$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
					$file_name2    	= $name_file . "." . $imageFileType;

					if (!empty($_FILES[$nm_detail]["tmp_name"])) {
						$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
					}

					$ArrDetail2[$key]['sp_inspeksi'] = $file_name2;
				}
			}
		}

		//DETAIL SPOOL
		$ArrDetailSpool = [];
		foreach ($detail_spool as $key => $value) {
			$ArrDetailSpool[$key]['kode_spool'] 			= $value['id'];
			$ArrDetailSpool[$key]['sp_group_status'] 		= 'Y';
			$ArrDetailSpool[$key]['sp_group_daycode'] 		= $value['sp_daycode'];
			$ArrDetailSpool[$key]['sp_group_keterangan'] 	= $value['sp_ket'];

			//UPLOAD
			$nm_detail = 'inspeksi_spool_' . $value['id'];
			$file_name2			= '';
			if (!empty($_FILES[$nm_detail]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'spool_group_inspeksi_' . $value['id'] . '_' . $dateTimeLabel;
				$target_file    = $target_dir . basename($_FILES[$nm_detail]["name"]);
				$name_file_ori  = basename($_FILES[$nm_detail]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name2    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES[$nm_detail]["tmp_name"])) {
					$terupload = move_uploaded_file($_FILES[$nm_detail]["tmp_name"], $nama_upload);
				}

				$ArrDetailSpool[$key]['sp_group_inspeksi'] = $file_name2;
			}
		}

		// print_r($ArrEditHeader);
		// print_r($ArrDetail);
		// exit;

		$this->db->trans_start();
		$this->db->where('spool_induk', $spool_induk);
		$this->db->update('spool', $ArrEditHeader);
		if (!empty($ArrDetail)) {
			$this->db->update_batch('production_detail', $ArrDetail, 'id');
		}

		if (!empty($ArrDetail2)) {
			$this->db->update_batch('so_cutting_detail', $ArrDetail2, 'id');
		}

		if (!empty($ArrDetailSpool)) {
			$this->db->where('spool_induk', $spool_induk);
			$this->db->update_batch('production_detail', $ArrDetailSpool, 'kode_spool');
		}

		if (!empty($ArrDetailSpool)) {
			$this->db->where('spool_induk', $spool_induk);
			$this->db->update_batch('so_cutting_detail', $ArrDetailSpool, 'kode_spool');
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0,
				'spool_induk'     => $spool_induk
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Success process data. Thanks ...',
				'status'	=> 1,
				'spool_induk'     => $spool_induk
			);
			history('QC Check temp spool = ' . $data['spool_induk']);
		}
		echo json_encode($Arr_Kembali);
	}

	public function reject_spool()
	{
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$spool 			= $data['spool_induk'];
		$reason_reject 	= $data['reason_reject'];

		$ArrUpdate = [
			'lock_spool_by' => NULL,
			'lock_spool_date' => NULL,
		];

		$ArrReject = [
			'reason_reject' => $reason_reject,
			'reject_by' => $username,
			'reject_date' => $dateTime
		];
		// print_r($ArrUpdate);
		// exit;

		$this->db->trans_start();
		$this->db->where('spool_induk', $spool);
		$this->db->update('production_detail', $ArrUpdate);

		$this->db->where('spool_induk', $spool);
		$this->db->update('spool', $ArrReject);
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
			history('Reject lock spool from QC : ' . $spool);
		}

		echo json_encode($Arr_Kembali);
	}


	//REPORT QC
	public function report()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/report';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$tanda = $this->uri->segment(3);
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$IPP 		= $this->db->group_by('id_produksi')->get_where('production_detail', array('status' => '1'))->result_array();
		$NO_SPK 	= $this->db->group_by('no_spk')->get_where('production_detail', array('status' => '1'))->result_array();
		$PRODUCT 	= $this->db->order_by('id_category')->group_by('id_category')->get_where('production_detail', array('status' => '1'))->result_array();

		$data = array(
			'title'			=> 'Report Quality Control',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'tanda'			=> $tanda,
			'IPP'			=> $IPP,
			'NO_SPK'		=> $NO_SPK,
			'PRODUCT'		=> $PRODUCT,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data report quality control');
		$this->load->view('Qc/report', $data);
	}

	public function server_side_qc_report()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/quality';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc_report(
			$requestData['status'],
			$requestData['no_ipp'],
			$requestData['no_spk'],
			$requestData['product'],
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
		$GET_DET_IPP = get_detail_ipp();
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

			$no_ipp 		= $row['no_ipp'];
			$tandaTanki = substr($no_ipp,0,4);
			if($tandaTanki != 'IPPT'){
				$spec 			= spec_bq2($row['id_milik']);
				$nm_customer 	= (!empty($GET_DET_IPP[$no_ipp]['nm_customer']))?$GET_DET_IPP[$no_ipp]['nm_customer']:'';
				$nm_project 	= (!empty($GET_DET_IPP[$no_ipp]['nm_project']))?$GET_DET_IPP[$no_ipp]['nm_project']:'';
				$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
				$nm_product		= $row['id_category'];
				$no_po			= $row['no_po'];
			}
			else{
				$spec 			= $this->tanki_model->get_spec($row['id_milik']);
				$GET_DET_TANKI	= $this->tanki_model->get_ipp_detail($no_ipp);
				$nm_customer 	= (!empty($GET_DET_TANKI['customer']))?$GET_DET_TANKI['customer']:'';
				$nm_project 	= (!empty($GET_DET_TANKI['nm_project']))?$GET_DET_TANKI['nm_project']:'';
				$no_so 			= (!empty($GET_DET_TANKI['no_so']))?$GET_DET_TANKI['no_so']:'';
				$no_po 			= (!empty($GET_DET_TANKI['no_po']))?$GET_DET_TANKI['no_po']:'';
				$nm_product		= $row['id_product'];
			}

			//assets/file/produksi/
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($nm_customer) . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($nm_project) . "</div>";
			$nestedData[]	= "<div align='center'>" . $no_so . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $no_po . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($nm_product) . "</div>";
			$nestedData[]	= "<div align='left'>" . $spec . "</div>";
			$upload_spk		= (!empty($row['inspeksi'])) ? $row['inspeksi'] : '';
			$link = '';
			if (!empty($upload_spk)) {
				$link = "<br><a href='" . base_url('assets/file/produksi/' . $upload_spk) . "' target='_blank' title='Download' data-role='qtip'>Download</a>";
			}
			$edit	= "<button class='btn btn-sm btn-success edit' title='Edit' data-id='" . $row['id'] . "'><i class='fa fa-edit'></i></button>";

			$nestedData[]	= "<div align='left'>" . strtoupper($row['daycode']) . $link . "</div>";
			$datePass = (!empty($row['qc_pass_date'])) ? date('d-M-Y', strtotime($row['qc_pass_date'])) : '';
			$dateRelease = (!empty($row['release_to_costing_date'])) ? date('d-M-Y', strtotime($row['release_to_costing_date'])) : '';
			$nestedData[]	= "<div align='center'>" . $datePass . "</div>";
			$nestedData[]	= "<div align='center'>" . $dateRelease . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['keterangan']) . "</div>";
			$nestedData[]	= "<div align='center'>" . $edit . "</div>";

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

	public function query_data_qc_report($status, $no_ipp, $no_spk, $product, $tgl_awal, $tgl_akhir, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";

		$WHERE_NOIPP = '';
		if ($no_ipp <> '0') {
			$WHERE_NOIPP = " AND a.id_produksi='" . $no_ipp . "' ";
		}

		$WHERE_PRODUCT = '';
		if ($product <> '0') {
			$WHERE_PRODUCT = " AND a.id_category='" . $product . "' ";
		}

		$WHERE_NOSPK = '';
		if ($no_spk <> '0') {
			$WHERE_NOSPK = " AND a.no_spk='" . $no_spk . "' ";
		}

		$WHERE_DATERANGE = '';
		if ($tgl_awal <> '0') {
			$tgl_label_aw = date('Y-m-d', strtotime($tgl_awal));
			$tgl_label_ak = date('Y-m-d', strtotime($tgl_akhir));

			$WHERE_DATERANGE = " AND DATE(a.fg_date) BETWEEN  '" . $tgl_label_aw . "' AND '" . $tgl_label_ak . "'";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
				z.project,
				z.nm_customer,
				x.no_po
			FROM
				production_detail a
                LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
                LEFT JOIN billing_so x ON REPLACE(a.id_produksi, 'PRO-', '') = x.no_ipp,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.status = '1'
                " . $where . "
                " . $where2 . "
                " . $WHERE_NOIPP . "
                " . $WHERE_PRODUCT . "
                " . $WHERE_NOSPK . "
                " . $WHERE_DATERANGE . "
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.daycode LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.project LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR x.no_po LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'z.nm_customer',
			2 => 'z.project',
			3 => 'product_code',
			4 => 'no_spk',
			5 => 'x.no_po',
			6 => 'id_category',
			8 => 'daycode',
			9 => 'qc_pass_date',
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalEditReport($id = null)
	{
		if ($this->input->post()) {
			$data 		= $this->input->post();
			$id			= $data['id'];
			$daycode	= $data['daycode'];
			$qc_pass	= $data['qc_pass'];
			$keterangan	= $data['keterangan'];

			if (!empty($_FILES["upload_spk"]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'inspeksi_qc_edit_' . $id . '_' . date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES["upload_spk"]["tmp_name"])) {
					move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
					$ArrUpdate = [
						'daycode' => $daycode,
						'keterangan' => $keterangan,
						'inspeksi' => $file_name,
						'qc_pass_date' => $qc_pass
					];
				} else {
					$ArrUpdate = [
						'daycode' => $daycode,
						'keterangan' => $keterangan,
						'qc_pass_date' => $qc_pass
					];
				}
			} else {
				$ArrUpdate = [
					'daycode' => $daycode,
					'keterangan' => $keterangan,
					'qc_pass_date' => $qc_pass
				];
			}

			$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('production_detail', $ArrUpdate);
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
				history('Upload report QC ' . $id);
			}
			echo json_encode($Arr_Kembali);
		} else {
			$get_detail = $this->db->get_where('production_detail', array('id' => $id))->result();

			$data = [
				'get_detail' => $get_detail,
				'id' => $id
			];
			$this->load->view('Qc/modalEditReport', $data);
		}
	}

	public function download_excel()
	{
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$no_ipp		= $this->uri->segment(3);
		$no_spk		= $this->uri->segment(4);
		$product	= str_replace('%20', ' ', $this->uri->segment(5));
		$tgl_awal	= $this->uri->segment(6);
		$tgl_akhir	= $this->uri->segment(7);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'e0e0e0'),
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
				'color' => array('rgb' => 'e0e0e0'),
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
					'color' => array('rgb' => '000000')
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
					'color' => array('rgb' => '000000')
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
					'color' => array('rgb' => '000000')
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

		$Arr_Bulan	= array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row + 1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$LABEL = 'ALL DATE';
		if ($tgl_awal <> '0') {
			$LABEL = date('d M Y', strtotime($tgl_awal)) . ' - ' . date('d M Y', strtotime($tgl_akhir));
		}

		$sheet->setCellValue('A' . $Row, 'REPORT QC (' . $LABEL . ')');
		$sheet->getStyle('A' . $Row . ':' . $Col_Akhir . $NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A' . $Row . ':' . $Col_Akhir . $NewRow);

		$NewRow	= $NewRow + 2;
		$NextRow = $NewRow + 1;

		$sheet->setCellValue('A' . $NewRow, 'No');
		$sheet->getStyle('A' . $NewRow . ':A' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A' . $NewRow . ':A' . $NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B' . $NewRow, 'Customer');
		$sheet->getStyle('B' . $NewRow . ':B' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B' . $NewRow . ':B' . $NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('C' . $NewRow, 'Project');
		$sheet->getStyle('C' . $NewRow . ':C' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C' . $NewRow . ':C' . $NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D' . $NewRow, 'No SO');
		$sheet->getStyle('D' . $NewRow . ':D' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D' . $NewRow . ':D' . $NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);

		$sheet->setCellValue('E' . $NewRow, 'No SPK');
		$sheet->getStyle('E' . $NewRow . ':E' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E' . $NewRow . ':E' . $NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);

		$sheet->setCellValue('F' . $NewRow, 'No PO');
		$sheet->getStyle('F' . $NewRow . ':F' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F' . $NewRow . ':F' . $NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);

		$sheet->setCellValue('G' . $NewRow, 'Product');
		$sheet->getStyle('G' . $NewRow . ':G' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G' . $NewRow . ':G' . $NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);

		$sheet->setCellValue('H' . $NewRow, 'Spec');
		$sheet->getStyle('H' . $NewRow . ':H' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H' . $NewRow . ':H' . $NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);

		$sheet->setCellValue('I' . $NewRow, 'Daycode');
		$sheet->getStyle('I' . $NewRow . ':I' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I' . $NewRow . ':I' . $NextRow);
		$sheet->getColumnDimension('I')->setWidth(16);

		$sheet->setCellValue('J' . $NewRow, 'QC Pass Date');
		$sheet->getStyle('J' . $NewRow . ':J' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J' . $NewRow . ':J' . $NextRow);
		$sheet->getColumnDimension('J')->setWidth(16);

		$sheet->setCellValue('K' . $NewRow, 'Harga FG');
		$sheet->getStyle('K' . $NewRow . ':K' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K' . $NewRow . ':K' . $NextRow);
		$sheet->getColumnDimension('K')->setWidth(16);
		$where2 = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";

		$WHERE_NOIPP = '';
		if ($no_ipp <> '0') {
			$WHERE_NOIPP = " AND a.id_produksi='" . $no_ipp . "' ";
		}

		$WHERE_PRODUCT = '';
		if ($product <> '0') {
			$WHERE_PRODUCT = " AND a.id_category='" . $product . "' ";
		}

		$WHERE_NOSPK = '';
		if ($no_spk <> '0') {
			$WHERE_NOSPK = " AND a.no_spk='" . $no_spk . "' ";
		}

		$WHERE_DATERANGE = '';
		if ($tgl_awal <> '0') {
			$tgl_label_aw = date('Y-m-d', strtotime($tgl_awal));
			$tgl_label_ak = date('Y-m-d', strtotime($tgl_akhir));

			$WHERE_DATERANGE = " AND a.fg_date BETWEEN  '" . $tgl_label_aw . "' AND '" . $tgl_label_ak . "'";
		}

		$sql = "
			SELECT
				a.*,
				z.project,
				z.nm_customer,
				REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
				x.no_po
			FROM
				production_detail a
                LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
                LEFT JOIN billing_so x ON REPLACE(a.id_produksi, 'PRO-', '') = x.no_ipp
		    WHERE 1=1 
				AND a.status = '1'
                " . $where2 . "
                " . $WHERE_NOIPP . "
                " . $WHERE_PRODUCT . "
                " . $WHERE_NOSPK . "
                " . $WHERE_DATERANGE . "
		";

		$restDetail1	= $this->db->query($sql)->result_array();
		$GET_DET_IPP = get_detail_ipp();
		if ($restDetail1) {
			$awal_row	= $NextRow;
			$no = 0;
			foreach ($restDetail1 as $key => $row_Cek) {
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$no_ipp 		= $row_Cek['no_ipp'];
				$tandaTanki = substr($no_ipp,0,4);
				if($tandaTanki != 'IPPT'){
					$spec 			= spec_bq2($row_Cek['id_milik']);
					$nm_customer 	= (!empty($GET_DET_IPP[$no_ipp]['nm_customer']))?$GET_DET_IPP[$no_ipp]['nm_customer']:'';
					$nm_project 	= (!empty($GET_DET_IPP[$no_ipp]['nm_project']))?$GET_DET_IPP[$no_ipp]['nm_project']:'';
					$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
					$nm_product		= $row_Cek['id_category'];
					$no_po			= $row_Cek['no_po'];
				}
				else{
					$spec 			= $this->tanki_model->get_spec($row_Cek['id_milik']);
					$GET_DET_TANKI	= $this->tanki_model->get_ipp_detail($no_ipp);
					$nm_customer 	= (!empty($GET_DET_TANKI['customer']))?$GET_DET_TANKI['customer']:'';
					$nm_project 	= (!empty($GET_DET_TANKI['nm_project']))?$GET_DET_TANKI['nm_project']:'';
					$no_so 			= (!empty($GET_DET_TANKI['no_so']))?$GET_DET_TANKI['no_so']:'';
					$no_po 			= (!empty($GET_DET_TANKI['no_po']))?$GET_DET_TANKI['no_po']:'';
					$nm_product		= $row_Cek['id_product'];
				}

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $detail_name);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_customer);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_project);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$NOMOR_SO = explode('-', $row_Cek['product_code']);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_so);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$no_spk	= $row_Cek['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_spk);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_po);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_product);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $spec);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$sp_daycode	= $row_Cek['daycode'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $sp_daycode);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$datePass = (!empty($row_Cek['fg_date'])) ? date('d-M-Y', strtotime($row_Cek['qc_pass_date'])) : '';

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $datePass);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$fg = $row_Cek['finish_good'];

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $fg);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);
			}
		}


		$sheet->setTitle('Report QC');
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
		header('Content-Disposition: attachment;filename="REPORT QC ' . date('YmdHis') . '.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	//PROGRESS QC
	public function progress()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/progress';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$tanda = $this->uri->segment(3);
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$IPP 		= $this->db->group_by('id_produksi')->get_where('production_detail', array('status' => '1'))->result_array();
		$NO_SPK 	= $this->db->group_by('no_spk')->get_where('production_detail', array('status' => '1'))->result_array();
		$PRODUCT 	= $this->db->order_by('id_category')->group_by('id_category')->get_where('production_detail', array('status' => '1'))->result_array();

		$data = array(
			'title'			=> 'Progress Quality Control',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'tanda'			=> $tanda,
			'IPP'			=> $IPP,
			'NO_SPK'		=> $NO_SPK,
			'PRODUCT'		=> $PRODUCT,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data report quality control progress');
		$this->load->view('Qc/progress', $data);
	}

	public function server_side_qc_progress()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/quality';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc_progress(
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

			//assets/file/produksi/
			$NOMOR_SO = explode('-', $row['product_code']);
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['nm_customer'].$row['customer_tanki']) . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['project'].$row['project_tanki']) . "</div>";
			$nestedData[]	= "<div align='center'>" . $NOMOR_SO[0] . "</div>";

			$edit	= "<button type='button' class='btn btn-sm btn-success detail' title='Detail' data-id='" . $row['id_produksi'] . "'><i class='fa fa-eye'></i></button>";
			$nestedData[]	= "<div align='center'>" . $edit . "</div>";

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

	public function query_data_qc_progress($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";

		$WHERE_NOIPP = '';
		if ($no_ipp <> '0') {
			$WHERE_NOIPP = " AND a.id_produksi='" . $no_ipp . "' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				z.project,
				z.nm_customer,
				y.customer as customer_tanki,
				y.project as project_tanki
			FROM
				production_detail a
                LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
                LEFT JOIN planning_tanki y ON REPLACE(a.id_produksi, 'PRO-', '') = y.no_ipp,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.qc_pass_date IS NOT NULL
                " . $where . "
                " . $where2 . "
                " . $WHERE_NOIPP . "
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.project LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.nm_customer LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR y.customer LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR y.project LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.id_produksi
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'nm_customer',
			2 => 'project',
			3 => 'product_code'
		);

		$sql .= " ORDER BY a.id DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalDetailQC($id = null)
	{
		$get_detail = $this->db->group_by('a.id_milik')->select('a.*')->get_where('production_detail a', array('a.id_milik' => $id))->result_array();

		$data = [
			'get_detail' => $get_detail,
			'id' => $id
		];
		$this->load->view('Qc/modalDetailQC', $data);
	}

	public function modalCreateQR()
	{
		$kode_spk 	= $this->uri->segment(3);
		$id_produksi = $this->uri->segment(4);
		$id_milik 	= $this->uri->segment(5);
		$id_pro_detail 	= $this->uri->segment(6);

		$get_time = get_name('production_detail', 'upload_date', 'id', $id_pro_detail);

		$get_split_code = $this->db->order_by('id', 'ASC')->get_where('production_detail', array('kode_spk' => $kode_spk, 'id_milik' => $id_milik, 'id_produksi' => $id_produksi, 'upload_real' => 'Y', 'upload_real2' => 'Y', 'upload_date' => $get_time))->result_array();
		$get_spk = $this->db->get_where('production_spk', array('kode_spk' => $kode_spk, 'id_milik' => $id_milik, 'no_ipp' => str_replace('PRO-', '', $id_produksi)))->result();
		$costcenter 	= $this->db->get_where('costcenter', array('deleted' => 'N', 'id_dept' => '10'))->result_array();

		$ArrGroup = [];
		foreach ($get_split_code as $key => $value) {
			$IMPLODE = explode('.', $value['product_code']);
			$ArrGroup[] = $IMPLODE[0] . '.' . $value['product_ke'];
		}
		// print_r($get_spk); exit;
		$explode = implode(', ', $ArrGroup);
		$data = [
			'get_split_code' => $get_split_code,
			'costcenter' => $costcenter,
			'kode_spk' => $kode_spk,
			'get_spk' => $get_spk,
			'id_produksi' => $id_produksi,
			'id_product' => $get_split_code[0]['id_product'],
			'id_milik' => $id_milik,
			'id_milik2' => $get_split_code[0]['id'],
			'kode_product' => $explode,
			'time_uniq' => $get_time,
			'first_id' => $id_pro_detail
		];
		$this->load->view('Qc/modalCreateQR', $data);
	}

	public function print_qrcode($idmilik)
	{
		// production_detail a
		// LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
		// LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
		// LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
		// LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke,
		// a.*,
		// MIN(product_ke) AS qty_min,
		// MAX(product_ke) AS qty_max,
		// b.spk1_cost,
		// b.spk2_cost,
		// z.project,
		// z.nm_customer,
		// c.diameter_1,
		// c.length,
		// c.thickness,
		// b.qty AS tot_qty
		$products = $this->db->select('a.*,z.nm_customer')->from('production_detail a')
			->join('production_spk b', 'a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik', 'left')
			->join('production z', 'REPLACE(a.id_produksi, "PRO-", "") = z.no_ipp', 'left')
			->where_in('a.id', explode("-", $idmilik))
			->get()->result();

		$ArrProducts = [];
		foreach ($products as $product) {
			$ArrProducts[$product->id] = $product;
		}
		$explode = explode("-", $idmilik);
		foreach ($explode as $key => $code) {
			$img = file_get_contents(base_url('qrcodegen/generate/' . $code . '/' . $code), '');
		}

		$data = [
			'explode' 	=> $explode,
			'products' 	=> $ArrProducts,
		];
		$this->load->view('Qc/print_qrcode', $data);
	}

	public function modalDetailQCIPP($id = null)
	{
		$get_detail = $this->db->group_by('a.id_milik')->select('a.*')->get_where('production_detail a', array('a.id_produksi' => $id))->result_array();

		$data = [
			'get_detail' => $get_detail,
			'id' => $id,
			'tanki_model' 		=> $this->tanki_model
		];
		$this->load->view('Qc/modalDetailQC', $data);
	}

	//REPORT SPOOL
	public function report_spool()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/report';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$tanda = $this->uri->segment(3);
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$IPP 		= $this->db->group_by('id_produksi')->get_where('production_detail', array('status' => '1'))->result_array();
		$NO_SPK 	= $this->db->group_by('no_spk')->get_where('production_detail', array('status' => '1'))->result_array();
		$PRODUCT 	= $this->db->order_by('id_category')->group_by('id_category')->get_where('production_detail', array('status' => '1'))->result_array();

		$data = array(
			'title'			=> 'Report QC Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'tanda'			=> $tanda,
			'IPP'			=> $IPP,
			'NO_SPK'		=> $NO_SPK,
			'PRODUCT'		=> $PRODUCT,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data report spool quality control');
		$this->load->view('Qc/report_spool', $data);
	}

	public function server_side_qc_report_spool()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/quality';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc_report_spool(
			$requestData['status'],
			$requestData['no_ipp'],
			$requestData['no_spk'],
			$requestData['product'],
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
		$GET_DET_IPP = get_detail_ipp();
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

			$no_ipp 		= $row['no_ipp'];
			$tandaTanki = substr($no_ipp,0,4);
			if($tandaTanki != 'IPPT'){
				$spec 			= spec_bq2($row['id_milik']);
				$nm_customer 	= (!empty($GET_DET_IPP[$no_ipp]['nm_customer']))?$GET_DET_IPP[$no_ipp]['nm_customer']:'';
				$nm_project 	= (!empty($GET_DET_IPP[$no_ipp]['nm_project']))?$GET_DET_IPP[$no_ipp]['nm_project']:'';
				$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
				$nm_product		= $row['id_category'];
			}
			else{
				$spec 			= $this->tanki_model->get_spec($row['id_milik']);
				$GET_DET_TANKI	= $this->tanki_model->get_ipp_detail($no_ipp);
				$nm_customer 	= (!empty($GET_DET_TANKI['customer']))?$GET_DET_TANKI['customer']:'';
				$nm_project 	= (!empty($GET_DET_TANKI['nm_project']))?$GET_DET_TANKI['nm_project']:'';
				$no_so 			= (!empty($GET_DET_TANKI['no_so']))?$GET_DET_TANKI['no_so']:'';
				$nm_product		= $row['nm_tanki'];
			}

			//assets/file/produksi/
			$NOMOR_SO = explode('-', $row['product_code']);
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($nm_customer) . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($nm_project) . "</div>";
			$nestedData[]	= "<div align='center'>" . $NOMOR_SO[0] . "</div>";
			$upload_spk		= (!empty($row['sp_group_inspeksi'])) ? $row['sp_group_inspeksi'] : '';
			$link = '';
			if (!empty($upload_spk)) {
				$link = "<br><a href='" . base_url('assets/file/produksi/' . $upload_spk) . "' target='_blank' title='Download' data-role='qtip'>Download</a>";
			}
			$edit	= "<button class='btn btn-sm btn-success edit' title='Edit' data-id='" . $row['spool_induk'] . "'><i class='fa fa-edit'></i></button>";
			$detail	= "<span class='text-bold text-green detail_spool' title='Detail' data-id='" . $row['spool_induk'] . "'>DETAIL SPOOL</span>";

			$nestedData[]	= "<div align='left'>" . strtoupper($row['sp_group_daycode']) . $link . "</div>";
			// $datePass = (!empty($row['sp_group_pass_date']))?date('d-M-Y', strtotime($row['sp_group_pass_date'])):'';
			// $nestedData[]	= "<div align='center'>".$datePass."</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['sp_group_keterangan']) . "</div>";
			$nestedData[]	= "<div align='center'>" . strtoupper($row['spool_induk']) . "</div>";
			$nestedData[]	= "<div align='center'>" . $detail . "</div>";
			$nestedData[]	= "<div align='center'>" . $edit . "</div>";

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

	public function query_data_qc_report_spool($status, $no_ipp, $no_spk, $product, $tgl_awal, $tgl_akhir, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";

		$WHERE_NOIPP = '';
		if ($no_ipp <> '0') {
			$WHERE_NOIPP = " AND a.id_produksi='" . $no_ipp . "' ";
		}

		$WHERE_PRODUCT = '';
		if ($product <> '0') {
			$WHERE_PRODUCT = " AND a.id_category='" . $product . "' ";
		}

		$WHERE_NOSPK = '';
		if ($no_spk <> '0') {
			$WHERE_NOSPK = " AND a.no_spk='" . $no_spk . "' ";
		}

		$WHERE_DATERANGE = '';
		if ($tgl_awal <> '0') {
			$tgl_label_aw = date('Y-m-d', strtotime($tgl_awal));
			$tgl_label_ak = date('Y-m-d', strtotime($tgl_akhir));

			$WHERE_DATERANGE = " AND DATE(a.release_spool_date) BETWEEN  '" . $tgl_label_aw . "' AND '" . $tgl_label_ak . "'";
		}
		// LEFT JOIN so_cutting_detail e ON d.id=e.id_header,
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
				z.project,
				z.nm_customer
			FROM
				production_detail a
                LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp,(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.sp_group_status = 'Y'
				AND a.spool_induk IS NOT NULL
                " . $where . "
                " . $where2 . "
                " . $WHERE_NOIPP . "
                " . $WHERE_PRODUCT . "
                " . $WHERE_NOSPK . "
                " . $WHERE_DATERANGE . "
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.spool_induk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.sp_group_daycode LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.sp_group_keterangan LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR z.project LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.spool_induk
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'z.nm_customer',
			2 => 'z.project',
			3 => 'product_code'
		);

		$sql .= " ORDER BY a.release_to_costing_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modalEditReportSpool($id = null)
	{
		if ($this->input->post()) {
			$data 		= $this->input->post();
			$id			= $data['id'];
			$daycode	= $data['daycode'];
			// $qc_pass	= $data['qc_pass'];
			$keterangan	= $data['keterangan'];

			if (!empty($_FILES["upload_spk"]["name"])) {
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT'] . "/assets/file/produksi/";
				$name_file      = 'inspeksi_qc_spool_edit_' . $id . '_' . date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				$nama_upload    = $target_dir_u . $name_file . "." . $imageFileType;
				$file_name    	= $name_file . "." . $imageFileType;

				if (!empty($_FILES["upload_spk"]["tmp_name"])) {
					move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
					$ArrUpdate = [
						'sp_group_daycode' => $daycode,
						'sp_group_keterangan' => $keterangan,
						'sp_group_inspeksi' => $file_name,
						// 'sp_group_pass_date' => $qc_pass
					];
				} else {
					$ArrUpdate = [
						'sp_group_daycode' => $daycode,
						'sp_group_keterangan' => $keterangan,
						// 'sp_group_pass_date' => $qc_pass
					];
				}
			} else {
				$ArrUpdate = [
					'sp_group_daycode' => $daycode,
					'sp_group_keterangan' => $keterangan,
					// 'sp_group_pass_date' => $qc_pass
				];
			}

			$this->db->trans_start();
			$this->db->where('spool_induk', $id);
			$this->db->update('production_detail', $ArrUpdate);

			$this->db->where('spool_induk', $id);
			$this->db->update('so_cutting_detail', $ArrUpdate);
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
				history('Upload report QC ' . $id);
			}
			echo json_encode($Arr_Kembali);
		} else {
			$get_detail = $this->db->get_where('production_detail', array('spool_induk' => $id))->result();

			$data = [
				'get_detail' => $get_detail,
				'id' => $id
			];
			$this->load->view('Qc/modalEditReportSpool', $data);
		}
	}

	public function modal_detail_spool($id = null)
	{
		$QUERY = "SELECT
					a.*,
					REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
					COUNT(a.id) AS total_qty,
					z.project,
					z.nm_customer,
					c.diameter_1,
					c.diameter_2,
					c.length,
					c.thickness
				FROM
					production_detail a
					LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
					LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
					LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke
				WHERE 1=1 
					AND a.sp_group_status = 'Y'
					AND a.spool_induk = '$id'
				GROUP BY
					a.id_milik";
		$get_detail = $this->db->query($QUERY)->result_array();

		$data = [
			'get_detail' => $get_detail,
			'id' => $id
		];
		$this->load->view('Qc/modal_detail_spool', $data);
	}

	public function download_excel_qc($tanda = null)
	{
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		$this->load->library("PHPExcel");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'e0e0e0'),
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
				'color' => array('rgb' => 'e0e0e0'),
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
					'color' => array('rgb' => '000000')
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
					'color' => array('rgb' => '000000')
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
					'color' => array('rgb' => '000000')
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

		$Arr_Bulan	= array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row + 1;
		$Col_Akhir	= $Cols	= getColsChar(9);

		$sheet->setCellValue('A' . $Row, 'LIST QC (' . strtoupper($tanda) . ')');
		$sheet->getStyle('A' . $Row . ':' . $Col_Akhir . $NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A' . $Row . ':' . $Col_Akhir . $NewRow);

		$NewRow	= $NewRow + 2;
		$NextRow = $NewRow + 1;

		$sheet->setCellValue('A' . $NewRow, 'No');
		$sheet->getStyle('A' . $NewRow . ':A' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A' . $NewRow . ':A' . $NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);

		$sheet->setCellValue('B' . $NewRow, 'No SPK');
		$sheet->getStyle('B' . $NewRow . ':B' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B' . $NewRow . ':B' . $NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);

		$sheet->setCellValue('C' . $NewRow, 'Product');
		$sheet->getStyle('C' . $NewRow . ':C' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C' . $NewRow . ':C' . $NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D' . $NewRow, 'No SO');
		$sheet->getStyle('D' . $NewRow . ':D' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D' . $NewRow . ':D' . $NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);

		$sheet->setCellValue('E' . $NewRow, 'Customer');
		$sheet->getStyle('E' . $NewRow . ':E' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E' . $NewRow . ':E' . $NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);

		$sheet->setCellValue('F' . $NewRow, 'Project');
		$sheet->getStyle('F' . $NewRow . ':F' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F' . $NewRow . ':F' . $NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);

		$sheet->setCellValue('G' . $NewRow, 'Spec');
		$sheet->getStyle('G' . $NewRow . ':G' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G' . $NewRow . ':G' . $NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);

		$sheet->setCellValue('H' . $NewRow, 'Qty');
		$sheet->getStyle('H' . $NewRow . ':H' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H' . $NewRow . ':H' . $NextRow);
		$sheet->getColumnDimension('H')->setWidth(16);

		$sheet->setCellValue('I' . $NewRow, 'Tgl Produksi');
		$sheet->getStyle('I' . $NewRow . ':I' . $NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I' . $NewRow . ':I' . $NextRow);
		$sheet->getColumnDimension('I')->setWidth(16);


		$where = "";
		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";
		$group_by = "GROUP BY
		a.id_produksi,
		a.kode_spk,
		a.id_milik,
		a.upload_date";
		if ($tanda == 'pipe') {
			$where = " AND (a.sts_cutting='N' OR a.sts_cutting IS NULL) AND c.id_category='pipe' ";
		}
		if ($tanda == 'cutting') {
			$where = " AND a.sts_cutting='Y' AND c.id_category='pipe' ";
		}
		if ($tanda == 'fitting') {
			$where = " AND c.cutting='N' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' AND c.id_category NOT IN " . NotInProduct() . " ";
		}

		//AND a.release_to_costing_date IS NULL 

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				MIN(product_ke) AS qty_min,
				MAX(product_ke) AS qty_max,
                b.spk1_cost,
                b.spk2_cost,
				z.project,
				z.nm_customer,
                c.diameter_1,
                c.length,
                c.thickness,
				b.qty AS tot_qty,
				MIN(a.closing_produksi_date) AS min_date_produksi,
				MAX(a.closing_produksi_date) AS max_date_produksi
			FROM
				production_detail a
                LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
                LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
                LEFT JOIN production z ON REPLACE(a.id_produksi, 'PRO-', '') = z.no_ipp
                LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke,
				(SELECT @row:=0) r
		    WHERE 1=1 
                AND a.upload_real = 'Y' 
                AND a.upload_real2 = 'Y' 
                AND a.kode_spk IS NOT NULL
                AND a.kode_spk != 'deadstok'
				AND a.fg_date IS NULL
				AND a.closing_produksi_date IS NOT NULL
                " . $where . "
                " . $where2 . "
			" . $group_by . "
			ORDER BY a.closing_produksi_date DESC
		";

		$restDetail1	= $this->db->query($sql)->result_array();

		if ($restDetail1) {
			$awal_row	= $NextRow;
			$no = 0;
			foreach ($restDetail1 as $key => $row_Cek) {
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $detail_name);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$no_spk	= $row_Cek['no_spk'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_spk);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);
				$awal_col++;

				$id_category	= $row_Cek['id_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $id_category);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$NOMOR_SO = explode('-', $row_Cek['product_code']);

				$awal_col++;
				$NOMORSO	= $NOMOR_SO[0];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $NOMORSO);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_customer	= strtoupper($row_Cek['nm_customer']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_customer);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$project	= strtoupper($row_Cek['project']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $project);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_milik		= spec_bq2($row_Cek['id_milik']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $id_milik);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$get_split_code = $this->db->select('product_code, product_ke')->get_where('production_detail', array('kode_spk' => $row_Cek['kode_spk'], 'id_milik' => $row_Cek['id_milik'], 'id_produksi' => $row_Cek['id_produksi'], 'fg_date' => NULL, 'upload_real' => 'Y', 'upload_real2' => 'Y', 'upload_date' => $row_Cek['upload_date']))->result_array();

				$awal_col++;
				$id_milik		= COUNT($get_split_code);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $id_milik);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray2);

				$date_produksi_MIN = (!empty($row_Cek['min_date_produksi']) and $row_Cek['min_date_produksi'] != '0000-00-00') ? date('d-M-Y', strtotime($row_Cek['min_date_produksi'])) : 'not set';
				$date_produksi_MAX = (!empty($row_Cek['max_date_produksi']) and $row_Cek['max_date_produksi'] != '0000-00-00') ? date('d-M-Y', strtotime($row_Cek['max_date_produksi'])) : 'not set';

				$date_produksi = $date_produksi_MIN . '<br>sd<br>' . $date_produksi_MAX;
				if ($date_produksi_MIN == $date_produksi_MAX) {
					$date_produksi = $date_produksi_MIN;
				}
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $date_produksi);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray2);
			}
		}


		$sheet->setTitle('LIST QC ' . strtoupper($tanda));
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
		header('Content-Disposition: attachment;filename="list-qc-' . $tanda . '.xls"');
		//unduh file
		$objWriter->save("php://output");
	}


	// QC FIELD JOINT
	public function field_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/field_joint';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Quality Control Field Joint',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data qc field joint');
		$this->load->view('Qc/field_joint', $data);
	}

	public function server_side_field_joint()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/field_joint';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_field_joint(
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

			$check	= "<button class='btn btn-sm btn-success check_real' title='Release QC' data-kode_trans='" . $row['kode_trans'] . "'><i class='fa fa-check'></i></button>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['so_number'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['qty'] . "</div>";
			$nestedData[]	= "<div align='center'>
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

	public function query_data_field_joint($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";
		$where2 = "";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.so_number
			FROM
				outgoing_field_joint a
				LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
				(SELECT @row:=0) r
			WHERE 1=1 " . $where . " " . $where2 . "
				AND a.qc_date IS NULL
				AND a.deleted_date IS NULL
				AND (
					a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.so_number',
			2 => 'no_ipp',
			3 => 'no_spk',
			4 => 'qty'
		);

		$sql .= " ORDER BY a.created_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_qc_field_joint()
	{
		$kode_trans 	= $this->uri->segment(3);
		$header = $this->db->get_where('outgoing_field_joint', array('kode_trans' => $kode_trans))->result_array();
		$result = $this->db->get_where('outgoing_field_joint_detail', array('kode_trans' => $kode_trans))->result_array();
		$data = [
			'kode_trans' => $kode_trans,
			'header' => $header,
			'result' => $result,
		];
		$this->load->view('Qc/modal_qc_field_joint', $data);
	}

	public function process_qc_field_joint()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$kode_trans		= $data['kode_trans'];
		$dateTime 		= date('Y-m-d H:i:s');

		$ArrFlagRelease = [
			'qc_by' => $data_session['ORI_User']['username'],
			'qc_date' => $dateTime
		];

		$this->db->trans_start();
		$this->db->where('kode_trans', $kode_trans);
		$this->db->update('outgoing_field_joint', $ArrFlagRelease);
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
			history('Release QC Field Joint = ' . $kode_trans);
		}
		echo json_encode($Arr_Kembali);
	}

	public function process_reject_qc_field_joint()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$kode_trans		= $data['kode_trans'];
		$dateTime 		= date('Y-m-d H:i:s');

		//PROSES DETAIL
		$getDetail = $this->db->get_where('outgoing_field_joint_detail', array('kode_trans' => $kode_trans))->result_array();
		$ArrUpdate = [];
		$ArrMaterial = [];
		foreach ($getDetail as $key => $value) {
			$restWhDetail	= $this->db->get_where('request_outgoing', array('kode_trans' => $kode_trans, 'id_material' => $value['id_material']))->result();

			//update request outgoing
			if(!empty($restWhDetail[0]->id)){
			$ArrUpdate[$key]['id'] 			= $restWhDetail[0]->id;
			$ArrUpdate[$key]['qty_out'] 	= $restWhDetail[0]->qty_out - $value['qty'];
			}

			$ArrMaterial[$key]['id_material'] 	= $value['id_material'];
			$ArrMaterial[$key]['gudang'] 	    = $value['id_gudang'];
			$ArrMaterial[$key]['qty'] 	        = $value['qty'];
		}

		//GROUPING UPDATE MATERIAL PER GUDANG
		$ArrGrouping = [];
		foreach ($ArrMaterial as $key => $value) {
			$ArrGrouping[$value['gudang']][$key]['id'] = $value['id_material'];
			$ArrGrouping[$value['gudang']][$key]['qty'] = $value['qty'];
		}

		$gudang_dari = 15;
		foreach ($ArrGrouping as $key => $value) {
			move_warehouse($value, $gudang_dari, $key, $kode_trans);
		}

		$ArrFlagRelease = [
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => $dateTime
		];

		$ArrFlagReleaseAdjustment = [
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => $dateTime,
			'status_id' => '0'
		];


		// exit;

		$this->db->trans_start();
		$this->db->where('kode_trans', $kode_trans);
		$this->db->update('outgoing_field_joint', $ArrFlagRelease);

		$this->db->where('kode_trans', $kode_trans);
		$this->db->update('warehouse_adjustment', $ArrFlagReleaseAdjustment);
		if(!empty($ArrUpdate)){
		$this->db->update_batch('request_outgoing', $ArrUpdate, 'id');
		}
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
			history('Reject QC Field Joint = ' . $kode_trans);
		}
		echo json_encode($Arr_Kembali);
	}

	public function download_excel_qc_spool()
	{
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		$this->load->library("PHPExcel");
		$objPHPExcel	= new PHPExcel();

		$whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

		$Arr_Bulan	= array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$where2 = " AND a.id_produksi NOT IN " . filter_not_in() . " ";

		$sql = "SELECT
					a.spool_induk AS kode_spool,
					a.product_code AS no_so,
					a.no_spk AS no_spk,
					a.id_category AS product,
					a.status_tanki,
					a.nm_tanki,
					b.nm_customer AS customer,
					b.project AS project,
					a.id_milik AS id_milik,
					COUNT(id) AS qty,
					a.no_drawing AS no_drawing,
					a.id_produksi
				FROM
					spool_group a
					LEFT JOIN production b ON REPLACE(a.id_produksi,'PRO-','')=b.no_ipp
				WHERE 1=1
					AND a.lock_spool_date IS NOT NULL
					" . $where2 . "
				GROUP BY
					a.sts, a.kode_spool, a.id_milik ORDER BY a.spool_induk DESC
		";
		$restDetail1	= $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row + 1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A' . $Row, 'QUALITY CONTROL SPOOL');
		$sheet->getStyle('A' . $Row . ':' . $Col_Akhir . $NewRow)->applyFromArray($mainTitle);
		$sheet->mergeCells('A' . $Row . ':' . $Col_Akhir . $NewRow);

		$NewRow	= $NewRow + 2;
		$NextRow = $NewRow;

		$sheet->setCellValue('A' . $NewRow, '#');
		$sheet->getStyle('A' . $NewRow . ':A' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('A' . $NewRow . ':A' . $NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B' . $NewRow, 'No Spool');
		$sheet->getStyle('B' . $NewRow . ':B' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('B' . $NewRow . ':B' . $NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C' . $NewRow, 'No SO');
		$sheet->getStyle('C' . $NewRow . ':C' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('C' . $NewRow . ':C' . $NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D' . $NewRow, 'No SPK');
		$sheet->getStyle('D' . $NewRow . ':D' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('D' . $NewRow . ':D' . $NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E' . $NewRow, 'Product');
		$sheet->getStyle('E' . $NewRow . ':E' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('E' . $NewRow . ':E' . $NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F' . $NewRow, 'Customer');
		$sheet->getStyle('F' . $NewRow . ':F' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('F' . $NewRow . ':F' . $NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sheet->setCellValue('G' . $NewRow, 'Project');
		$sheet->getStyle('G' . $NewRow . ':G' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('G' . $NewRow . ':G' . $NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H' . $NewRow, 'Spec');
		$sheet->getStyle('H' . $NewRow . ':H' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('H' . $NewRow . ':H' . $NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I' . $NewRow, 'QTY');
		$sheet->getStyle('I' . $NewRow . ':I' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('I' . $NewRow . ':I' . $NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J' . $NewRow, 'No Drawing');
		$sheet->getStyle('J' . $NewRow . ':J' . $NextRow)->applyFromArray($tableHeader);
		$sheet->mergeCells('J' . $NewRow . ':J' . $NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$DETAIL_IPP 		= get_detail_ipp();
		$DETAIL_IPP_TANKI 	= $this->tanki_model->get_detail_ipp_tanki();
		// echo $qDetail1; exit;

		if ($restDetail1) {
			$awal_row	= $NextRow;
			$no = 0;
			foreach ($restDetail1 as $key => $row_Cek) {
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $detail_name);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$kode_spool	= strtoupper($row_Cek['kode_spool']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $kode_spool);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_so	= strtoupper($row_Cek['no_so']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_so);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_spk	= strtoupper($row_Cek['no_spk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_spk);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$nm_product = ($row_Cek['status_tanki'] == 'tanki')?$row_Cek['nm_tanki']:$row_Cek['product'];
				$spec 		= ($row_Cek['status_tanki'] == 'tanki')?$this->tanki_model->get_spec($row_Cek['id_milik']):spec_bq2($row_Cek['id_milik']);

				$no_ipp = str_replace('PRO-','',$row_Cek['id_produksi']);
				$nm_customer = $row_Cek['customer'];
				$nm_project = $row_Cek['project'];
				if($row_Cek['status_tanki'] == 'tanki'){
					$nm_customer 	= (!empty($DETAIL_IPP_TANKI[$no_ipp]['nm_customer']))?$DETAIL_IPP_TANKI[$no_ipp]['nm_customer']:'';
					$nm_project 	= (!empty($DETAIL_IPP_TANKI[$no_ipp]['project']))?$DETAIL_IPP_TANKI[$no_ipp]['project']:'';
				}

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_product);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$customer	= strtoupper($nm_customer);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $customer);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$project	= strtoupper($nm_project);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $project);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $spec);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $qty);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$no_drawing	= $row_Cek['no_drawing'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_drawing);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($tableBodyLeft);
			}
		}


		$sheet->setTitle('QC SPOOL');
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
		header('Content-Disposition: attachment;filename="qc-spool.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function download_excel_qc_spool_report($tgl_awal = null, $tgl_akhir = null)
	{
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		$this->load->library("PHPExcel");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000')
				)
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
				'color' => array('rgb' => 'D9D9D9'),
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
					'color' => array('rgb' => '000000')
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
					'color' => array('rgb' => '000000')
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
					'color' => array('rgb' => '000000')
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

		$Arr_Bulan	= array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$WHERE_DATERANGE = '';
		if ($tgl_awal <> '0') {
			$tgl_label_aw = date('Y-m-d', strtotime($tgl_awal));
			$tgl_label_ak = date('Y-m-d', strtotime($tgl_akhir));

			$WHERE_DATERANGE = " AND a.qc_date BETWEEN  '" . $tgl_label_aw . "' AND '" . $tgl_label_ak . "'";
		}

		$GET_DET_IPP = get_detail_ipp();

		$sql = "SELECT
					a.spool_induk AS kode_spool,
					a.no_so AS no_so,
					a.no_spk AS no_spk,
					a.product AS product,
					b.nm_customer AS customer,
					b.project AS project,
					a.id_milik AS id_milik,
					COUNT(id) AS qty,
					a.no_ipp AS no_ipp,
					a.length AS length_cutting,
					a.nm_tanki
				FROM
					spool_report a
					LEFT JOIN production b on a.no_ipp=b.no_ipp
				WHERE 1=1
					" . $WHERE_DATERANGE . "
				GROUP BY
					a.sts, a.id_milik, a.spool_induk ORDER BY a.spool_induk DESC
		";
		$restDetail1	= $this->db->query($sql)->result_array();

		$Row		= 1;
		$NewRow		= $Row + 1;
		$Col_Akhir	= $Cols	= getColsChar(10);
		$sheet->setCellValue('A' . $Row, 'REPORT QUALITY CONTROL SPOOL');
		$sheet->getStyle('A' . $Row . ':' . $Col_Akhir . $NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A' . $Row . ':' . $Col_Akhir . $NewRow);

		$NewRow	= $NewRow + 2;
		$NextRow = $NewRow;

		$sheet->setCellValue('A' . $NewRow, '#');
		$sheet->getStyle('A' . $NewRow . ':A' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('A' . $NewRow . ':A' . $NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B' . $NewRow, 'No Spool');
		$sheet->getStyle('B' . $NewRow . ':B' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('B' . $NewRow . ':B' . $NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C' . $NewRow, 'No SO');
		$sheet->getStyle('C' . $NewRow . ':C' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('C' . $NewRow . ':C' . $NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D' . $NewRow, 'No SPK');
		$sheet->getStyle('D' . $NewRow . ':D' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('D' . $NewRow . ':D' . $NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E' . $NewRow, 'Product');
		$sheet->getStyle('E' . $NewRow . ':E' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('E' . $NewRow . ':E' . $NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);

		$sheet->setCellValue('F' . $NewRow, 'Customer');
		$sheet->getStyle('F' . $NewRow . ':F' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('F' . $NewRow . ':F' . $NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sheet->setCellValue('G' . $NewRow, 'Project');
		$sheet->getStyle('G' . $NewRow . ':G' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('G' . $NewRow . ':G' . $NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H' . $NewRow, 'Spec');
		$sheet->getStyle('H' . $NewRow . ':H' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('H' . $NewRow . ':H' . $NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I' . $NewRow, 'QTY');
		$sheet->getStyle('I' . $NewRow . ':I' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('I' . $NewRow . ':I' . $NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J' . $NewRow, 'Length Cutting');
		$sheet->getStyle('J' . $NewRow . ':J' . $NextRow)->applyFromArray($styleArray3);
		$sheet->mergeCells('J' . $NewRow . ':J' . $NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$DETAIL_IPP = get_detail_ipp();
		// echo $qDetail1; exit;

		if ($restDetail1) {
			$awal_row	= $NextRow;
			$no = 0;
			foreach ($restDetail1 as $key => $row_Cek) {
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$no_ipp 		= $row_Cek['no_ipp'];
				$tandaTanki = substr($no_ipp,0,4);
				if($tandaTanki != 'IPPT'){
					$spec 			= spec_bq2($row_Cek['id_milik']);
					$nm_customer 	= $row_Cek['customer'];
					$nm_project 	= $row_Cek['project'];
					$no_so 			= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
					$nm_product		= $row_Cek['product'];
				}
				else{
					$spec 			= $this->tanki_model->get_spec($row_Cek['id_milik']);
					$GET_DET_TANKI	= $this->tanki_model->get_ipp_detail($no_ipp);
					$nm_customer 	= (!empty($GET_DET_TANKI['customer']))?$GET_DET_TANKI['customer']:'';
					$nm_project 	= (!empty($GET_DET_TANKI['nm_project']))?$GET_DET_TANKI['nm_project']:'';
					$no_so 			= (!empty($GET_DET_TANKI['no_so']))?$GET_DET_TANKI['no_so']:'';
					$nm_product		= $row_Cek['nm_tanki'];
				}

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $detail_name);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_spool	= strtoupper($row_Cek['kode_spool']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $kode_spool);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_so);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$no_spk	= strtoupper($row_Cek['no_spk']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $no_spk);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_product);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_customer);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $nm_project);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $spec);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$qty	= $row_Cek['qty'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $qty);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$length_cutting	= $row_Cek['length_cutting'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols . $awal_row, $length_cutting);
				$sheet->getStyle($Cols . $awal_row)->applyFromArray($styleArray3);
			}
		}


		$sheet->setTitle('QC REPORT SPOOL');
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
		header('Content-Disposition: attachment;filename="qc-report-spool.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function close_jurnal_finish_good($ArrIdPro, $kode_trans, $id_pro_det){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		
		//GROUP DATA
		$ArrGroup = [];
		$ArrOutWIP = [];
		if(!empty($ArrIdPro)){
			foreach ($ArrIdPro as $value) {
				$getSummary = $this->db->select('*')->get_where('data_erp_wip_group',array('kode_trans'=>$kode_trans,'id_pro_det'=>$id_pro_det))->result_array();

				$qty 		= (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:0;

				$ArrGroup[$value]['tanggal'] = date('Y-m-d');
				$ArrGroup[$value]['keterangan'] = 'WIP to Finish Good';
				$ArrGroup[$value]['no_so'] 	= (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
				$ArrGroup[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
				$ArrGroup[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
				$ArrGroup[$value]['kode_trans'] = $kode_trans;
				$ArrGroup[$value]['id_pro_det'] = $id_pro_det;
				$ArrGroup[$value]['qty'] = 1;

				//$nilai_wip 		= (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
				$material 		= (!empty($getSummary[0]['material']))?$getSummary[0]['material']:0;
				$wip_direct 	= (!empty($getSummary[0]['wip_direct']))?$getSummary[0]['wip_direct']:0;
				$wip_indirect 	= (!empty($getSummary[0]['wip_indirect']))?$getSummary[0]['wip_indirect']:0;
				$wip_consumable = (!empty($getSummary[0]['wip_consumable']))?$getSummary[0]['wip_consumable']:0;
				$wip_foh 		= (!empty($getSummary[0]['wip_foh']))?$getSummary[0]['wip_foh']:0;
				$id_trans 		= (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:0;
				
				$nilai_wip 		= round($material)+round($wip_direct)+round($wip_indirect)+round($wip_consumable)+round($wip_foh);
				$ArrGroup[$value]['nilai_wip'] = ($nilai_wip > 0 AND $qty > 0)?round($nilai_wip/$qty):0;
				$ArrGroup[$value]['material'] = ($material > 0 AND $qty > 0)?round($material/$qty):0;
				$ArrGroup[$value]['wip_direct'] =  ($wip_direct > 0 AND $qty > 0)?round($wip_direct/$qty):0;
				$ArrGroup[$value]['wip_indirect'] =  ($wip_indirect > 0 AND $qty > 0)?round($wip_indirect/$qty):0;
				$ArrGroup[$value]['wip_consumable'] =  ($wip_consumable > 0 AND $qty > 0)?round($wip_consumable/$qty):0;
				$ArrGroup[$value]['wip_foh'] =  ($wip_foh > 0 AND $qty > 0)?round($wip_foh/$qty):0;
				$ArrGroup[$value]['created_by'] = $username;
				$ArrGroup[$value]['created_date'] = $datetime;
				$ArrGroup[$value]['id_trans'] = $id_trans;


				//tambahan finish good
				$getDetail = $this->db->get_where('production_detail',array('id'=>$value))->result_array();
				$ArrGroup[$value]['id_pro'] = $value;
				$ArrGroup[$value]['qty_ke'] = (!empty($getDetail[0]['product_ke']))?$getDetail[0]['product_ke']:0;

				$nilai_unit = 0;
				if($nilai_wip > 0 AND $qty > 0){
					$nilai_unit = $nilai_wip / $qty;
				}
				$ArrGroup[$value]['nilai_unit'] = $nilai_unit;
				
			}
			//Out WIP
			$getSummary = $this->db->select('*')->get_where('data_erp_wip_group',array('kode_trans'=>$kode_trans,'id_pro_det'=>$id_pro_det))->result_array();
			if(!empty($getSummary)){
				$qty         = (!empty($getSummary[0]['qty']))?$getSummary[0]['qty']:0;
				$qty_fg     = COUNT($ArrIdPro);
				$ArrOutWIP[$value]['tanggal'] = date('Y-m-d');
				$ArrOutWIP[$value]['keterangan'] = 'WIP to Finish Good';
				$ArrOutWIP[$value]['no_so']     = (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
				$ArrOutWIP[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
				$ArrOutWIP[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
				$ArrOutWIP[$value]['kode_trans'] = $kode_trans;
				$ArrOutWIP[$value]['id_pro_det'] = $id_pro_det;
				$ArrOutWIP[$value]['qty'] = $qty_fg;
				$ArrOutWIP[$value]['jenis'] = 'out';

				$nilai_wip         = (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip'] / $qty * $qty_fg:0;
				$material         = (!empty($getSummary[0]['material']))?$getSummary[0]['material'] / $qty * $qty_fg:0;
				$wip_direct     = (!empty($getSummary[0]['wip_direct']))?$getSummary[0]['wip_direct'] / $qty * $qty_fg:0;
				$wip_indirect     = (!empty($getSummary[0]['wip_indirect']))?$getSummary[0]['wip_indirect'] / $qty * $qty_fg:0;
				$wip_consumable = (!empty($getSummary[0]['wip_consumable']))?$getSummary[0]['wip_consumable'] / $qty * $qty_fg:0;
				$wip_foh         = (!empty($getSummary[0]['wip_foh']))?$getSummary[0]['wip_foh'] / $qty * $qty_fg:0;
				$id_trans         = (!empty($getSummary[0]['id_trans']))?$getSummary[0]['id_trans']:0;

				$ArrOutWIP[$value]['nilai_wip'] = round($nilai_wip);
				$ArrOutWIP[$value]['material'] = round($material);
				$ArrOutWIP[$value]['wip_direct'] =  round($wip_direct);
				$ArrOutWIP[$value]['wip_indirect'] =  round($wip_indirect);
				$ArrOutWIP[$value]['wip_consumable'] =  round($wip_consumable);
				$ArrOutWIP[$value]['wip_foh'] =  round($wip_foh);
				$ArrOutWIP[$value]['created_by'] = $username;
				$ArrOutWIP[$value]['created_date'] = $datetime;
				$ArrOutWIP[$value]['id_trans'] = $id_trans;
			}
		}

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_fg',$ArrGroup);
			
		}
		if(!empty($ArrOutWIP)){
			$this->db->insert_batch('data_erp_wip_group',$ArrOutWIP);

			$this->jurnalFG($id_trans,$datetime);
		}


	}
	
	
	function jurnalFG($idtrans,$datetime){
		
		$data_session	= $this->session->userdata; 
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	
		   
			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,kode_trans,id_trans,qty, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE id_trans ='".$idtrans."' AND tanggal ='".$Date."' AND created_date='".$datetime."'")->result();
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			$qty_n = 0;  
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;
				$fg_txt         ='FINISHED GOOD'; 
				$wip_txt         ='COGS';	
				$spasi       = ',';
				$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$keterangan1  = $fg_txt.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
				$keterangan2  = $wip_txt.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so;
				$id          = $data->id_trans;
				$noso 		 = $data->no_so;
               	$no_request  = $data->no_spk;	
				
				$wip           	= $data->wip;
				$material      	= $data->material;
				$wip_direct    	= $data->wip_direct;
				$wip_indirect  	= $data->wip_indirect;
				$wip_foh       	= $data->wip_foh;
				$wip_consumable = $data->wip_consumable;
				$finishgood    	= $data->finishgood;
				$cogs          	= $material+$wip_direct+$wip_indirect+$wip_foh+$wip_consumable;
				
				$totalfg        = $cogs;
				
				
				
				if ($nm_material=='pipe'){			
				$coa_wip 		='1103-03-02';	
				}else{
				$coa_wip 		='1103-03-03';						
				}					
			    $debit  		= $totalfg;	
				
				$coa_material	='5101-01-01';
				$coa_direct 	='5101-03-01';
				$coa_indirect 	='5101-04-01';
				$coa_foh 		='5101-05-01';
				$coa_consumable ='5101-02-01';
				
				$coacogs 		='5103-01-01';
				$coafg   		='1103-04-01';
                				
				
								
				     $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_material,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => $material,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					  $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_direct,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => $wip_direct,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					  $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_indirect,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => $wip_indirect,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_foh,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => $wip_foh,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_consumable,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => $wip_consumable,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => 0,
					  'kredit'        => $cogs,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					 
					 
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coafg,
					  'keterangan'    => $keterangan1,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => $cogs,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 );
					 
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $coacogs,
					  'keterangan'    => $keterangan2,
					  'no_reff'       => $id.$spasi.$noso,
					  'debet'         => 0,
					  'kredit'        => $cogs,
					  'jenis_jurnal'  => 'WIP-Finishgood',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
					  
					 $kode_trans = $data->kode_trans;
					 $nospk      = $data->no_spk;
					 $qty        = $data->qty;
				
					$this->db->query("UPDATE  warehouse_stock_wip SET qty = qty-1  WHERE no_so ='".$noso."' AND kode_trans ='".$kode_trans."'  AND no_spk ='".$nospk."' AND product ='".$nm_material."'");
			   $qty_n++;
			}
			
				
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='wip finishgood' and no_reff ='$id' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'WIP-Finishgood'.$keterangan;
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalfg, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$this->db->insert(DBACC.'.javh',$dataJVhead);
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
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);


			$wipgroup = $this->db->query("SELECT * FROM data_erp_fg WHERE id_trans ='".$idtrans."' limit 1")->row();	
			$kodetrans = $wipgroup->kode_trans;
			$Date      = $wipgroup->tanggal;
			$so        = $wipgroup->no_so;
			$spk       = $wipgroup->no_spk;
			$product   = $wipgroup->product;


			$stokwip = $this->db->query("SELECT
										`data_erp_wip_group`.`id` AS `id`,
										`data_erp_wip_group`.`tanggal` AS `tanggal`,
										`data_erp_wip_group`.`keterangan` AS `keterangan`,
										`data_erp_wip_group`.`no_so` AS `no_so`,
										`data_erp_wip_group`.`product` AS `product`,
										`data_erp_wip_group`.`no_spk` AS `no_spk`,
										`data_erp_wip_group`.`kode_trans` AS `kode_trans`,
										`data_erp_wip_group`.`id_pro_det` AS `id_pro_det`,
										sum(`data_erp_wip_group`.`qty`) AS `total`,
										`data_erp_wip_group`.`nilai_wip` AS `nilai_wip`,
										`data_erp_wip_group`.`material` AS `material`,
										`data_erp_wip_group`.`wip_direct` AS `wip_direct`,
										`data_erp_wip_group`.`wip_indirect` AS `wip_indirect`,
										`data_erp_wip_group`.`wip_consumable` AS `wip_consumable`,
										`data_erp_wip_group`.`wip_foh` AS `wip_foh`,
										`data_erp_wip_group`.`created_by` AS `created_by`,
										`data_erp_wip_group`.`created_date` AS `created_date`,
										`data_erp_wip_group`.`id_trans` AS `id_trans`,
										`data_erp_wip_group`.`jenis` AS `jenis`,
										`data_erp_wip_group`.`id_material` AS `id_material`,
										`data_erp_wip_group`.`nm_material` AS `nm_material`,
										`data_erp_wip_group`.`qty_mat` AS `qty_mat`,
										`data_erp_wip_group`.`cost_book` AS `cost_book`,
										`data_erp_wip_group`.`gudang` AS `gudang`,
										`data_erp_wip_group`.`kode_spool` AS `kode_spool` 
										FROM
										`data_erp_wip_group` 
										WHERE
										(`data_erp_wip_group`.`kode_trans` = '".$kodetrans."') 
										AND (`data_erp_wip_group`.`jenis`='out')
										AND (`data_erp_wip_group`.`tanggal` = '".$Date."')
										GROUP BY kode_trans,no_spk,product,no_so")->result();

			
			$cekstok = $this->db->query("SELECT * FROM warehouse_stock_fg WHERE kode_trans ='".$kodetrans."' 
			AND no_so ='".$so."' AND no_spk ='".$spk."' AND product ='".$product."'")->row();

		


			
			if(!empty($cekstok)){
            foreach ($stokwip as $vals) {
			$qty = 	$vals->total;
            $this->db->query("UPDATE  warehouse_stock_fg SET qty = qty+$qty_n  WHERE no_so ='".$so."' AND kode_trans ='".$kodetrans."'  AND no_spk ='".$spk."' AND product ='".$product."' ");
			}
			}else{
			$datastokfg=array();
			foreach ($stokwip as $vals) {
			$datastokfg = array(
						'tanggal' => $tgl_voucher,
						'keterangan' => 'WIP To FG',
						'no_so' => $vals->no_so,
						'product' => $vals->product,
						'no_spk' => $vals->no_spk,
						'kode_trans' => $vals->kode_trans,
						'id_pro_det' => $vals->id_pro_det,
						'qty' => 1,
						'nilai_wip' => $vals->nilai_wip,
						'material' => $vals->material,
						'wip_direct' =>  $vals->wip_direct,
						'wip_indirect' =>  $vals->wip_indirect,
						'wip_consumable' =>  $vals->wip_consumable,
						'wip_foh' =>  $vals->wip_foh,
						'created_by' => $vals->created_by,
						'created_date' => $vals->created_date,
						'id_trans' => $vals->id_trans,
						);

			$this->db->insert('warehouse_stock_fg',$datastokfg);
			}
			
		}


		  
		}

	public function SpoolToFG_Report($kode)
	{
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		$username 		= $data_session['ORI_User']['username'];

		$getQCDeadstockModif = $this->db->get_where('data_erp_wip_group',array('kode_spool'=>$kode,'jenis'=>'in spool'))->result_array();
		$ArrIN_WIP_MATERIAL = [];
		$ArrIN_FG_MATERIAL = [];
		foreach ($getQCDeadstockModif as $key => $value) {
			$ArrIN_WIP_MATERIAL[$key]['tanggal'] = date('Y-m-d');
			$ArrIN_WIP_MATERIAL[$key]['keterangan'] = 'WIP to Finish Good (Spool)';
			$ArrIN_WIP_MATERIAL[$key]['no_so'] = $value['no_so'];
			$ArrIN_WIP_MATERIAL[$key]['product'] = $value['product'];
			$ArrIN_WIP_MATERIAL[$key]['no_spk'] = $value['no_spk'];
			$ArrIN_WIP_MATERIAL[$key]['kode_trans'] = $kode;
			$ArrIN_WIP_MATERIAL[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrIN_WIP_MATERIAL[$key]['qty'] = $value['qty'];
			$ArrIN_WIP_MATERIAL[$key]['nilai_wip'] = $value['nilai_wip'];
			$ArrIN_WIP_MATERIAL[$key]['material'] = $value['material'];
			$ArrIN_WIP_MATERIAL[$key]['wip_direct'] =  $value['wip_direct'];
			$ArrIN_WIP_MATERIAL[$key]['wip_indirect'] =  $value['wip_indirect'];
			$ArrIN_WIP_MATERIAL[$key]['wip_consumable'] =  $value['wip_consumable'];
			$ArrIN_WIP_MATERIAL[$key]['wip_foh'] =  $value['wip_foh'];
			$ArrIN_WIP_MATERIAL[$key]['created_by'] = $username;
			$ArrIN_WIP_MATERIAL[$key]['created_date'] = $dateTime;
			$ArrIN_WIP_MATERIAL[$key]['id_trans'] =  $value['id_trans'];
			$ArrIN_WIP_MATERIAL[$key]['jenis'] =  'out spool';
			$ArrIN_WIP_MATERIAL[$key]['id_material'] =  $value['id_material'];
			$ArrIN_WIP_MATERIAL[$key]['nm_material'] = $value['nm_material'];
			$ArrIN_WIP_MATERIAL[$key]['qty_mat'] =  $value['qty_mat'];
			$ArrIN_WIP_MATERIAL[$key]['cost_book'] =  $value['cost_book'];
			$ArrIN_WIP_MATERIAL[$key]['gudang'] =  $value['gudang'];
			$ArrIN_WIP_MATERIAL[$key]['kode_spool'] =  $kode;
		}

		$getQCDeadstockModif = $this->db->get_where('data_erp_wip_group',array('kode_spool'=>$kode,'jenis'=>'in spool'))->result_array();
		foreach ($getQCDeadstockModif as $key => $value) {
			$ArrIN_FG_MATERIAL[$key]['tanggal'] = date('Y-m-d');
			$ArrIN_FG_MATERIAL[$key]['keterangan'] = 'WIP to Finish Good (Spool)';
			$ArrIN_FG_MATERIAL[$key]['no_so'] = $value['no_so'];
			$ArrIN_FG_MATERIAL[$key]['product'] = $value['product'];
			$ArrIN_FG_MATERIAL[$key]['no_spk'] = $value['no_spk'];
			$ArrIN_FG_MATERIAL[$key]['kode_trans'] = $kode;
			$ArrIN_FG_MATERIAL[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrIN_FG_MATERIAL[$key]['qty'] = $value['qty'];
			$ArrIN_FG_MATERIAL[$key]['nilai_unit'] = $value['nilai_wip'];
			$ArrIN_FG_MATERIAL[$key]['nilai_wip'] = $value['nilai_wip'];
			$ArrIN_FG_MATERIAL[$key]['material'] = $value['material'];
			$ArrIN_FG_MATERIAL[$key]['wip_direct'] =  $value['wip_direct'];
			$ArrIN_FG_MATERIAL[$key]['wip_indirect'] =  $value['wip_indirect'];
			$ArrIN_FG_MATERIAL[$key]['wip_consumable'] =  $value['wip_consumable'];
			$ArrIN_FG_MATERIAL[$key]['wip_foh'] =  $value['wip_foh'];
			$ArrIN_FG_MATERIAL[$key]['created_by'] = $username;
			$ArrIN_FG_MATERIAL[$key]['created_date'] = $dateTime;
			$ArrIN_FG_MATERIAL[$key]['id_trans'] =  $value['id_trans'];
			$ArrIN_FG_MATERIAL[$key]['id_pro'] =  null;
			$ArrIN_FG_MATERIAL[$key]['qty_ke'] =  null;
			$ArrIN_FG_MATERIAL[$key]['jenis'] =  'in';
			$ArrIN_FG_MATERIAL[$key]['id_material'] =  $value['id_material'];
			$ArrIN_FG_MATERIAL[$key]['nm_material'] = $value['nm_material'];
			$ArrIN_FG_MATERIAL[$key]['qty_mat'] =  $value['qty_mat'];
			$ArrIN_FG_MATERIAL[$key]['cost_book'] =  $value['cost_book'];
			$ArrIN_FG_MATERIAL[$key]['gudang'] =  $value['gudang'];
			$ArrIN_FG_MATERIAL[$key]['kode_spool'] =  $kode;
		}

		// print_r($ArrIN_WIP_MATERIAL);
		// print_r($ArrIN_FG_MATERIAL);
		// exit;

		$this->db->trans_start();
			if(!empty($ArrIN_WIP_MATERIAL)){
				$this->db->insert_batch('data_erp_wip_group',$ArrIN_WIP_MATERIAL);
			}

			if(!empty($ArrIN_FG_MATERIAL)){
				$this->db->insert_batch('data_erp_fg',$ArrIN_FG_MATERIAL);
				$this->jurnalIntoFG($kode);
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}
	}

	function jurnalIntoFG($kode){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
		$idtrans = str_replace('-','',$kode);

		
		$fg = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE id_trans ='".$idtrans."' AND tanggal ='".$Date."' AND jenis='in'")->result();
		
		$totalfg =0;
			
		$det_Jurnaltes = [];
			
		foreach($fg AS $data){
			
			$nm_material = $data->product;	
			$tgl_voucher = $data->tanggal;
			$fg_txt         ='FINISHED GOOD'; 
			$wip_txt         ='COGS';	
			$spasi       = ',';
			$keterangan  = $data->keterangan.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
			$keterangan1  = $fg_txt.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so; 
			$keterangan2  = $wip_txt.$spasi.$data->product.$spasi.$data->no_spk.$spasi.$data->no_so;
			$id          = $data->id_trans;
			$noso 		 = ','.$data->no_so;
			$no_request  = $data->no_spk;	
			
			$wip           	= $data->wip;
			$material      	= $data->material;
			$wip_direct    	= $data->wip_direct;
			$wip_indirect  	= $data->wip_indirect;
			$wip_foh       	= $data->wip_foh;
			$wip_consumable = $data->wip_consumable;
			$finishgood    	= $data->finishgood;
			$cogs          	= $material+$wip_direct+$wip_indirect+$wip_foh+$wip_consumable;
			
			$totalfg        = $finishgood;
			if ($nm_material=='pipe'){			
			$coa_wip 		='1103-03-02';	
			}else{
			$coa_wip 		='1103-03-03';						
			}					
			$coafg   		='1103-04-01';
							
					$det_Jurnaltes[]  = array(
					'nomor'         => '',
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => $coafg,
					'keterangan'    => $keterangan1,
					'no_reff'       => $id.$noso,
					'debet'         => $finishgood,
					'kredit'        => 0,
					'jenis_jurnal'  => 'WIP to Fg Spool tanki',
					'no_request'    => $no_request,
					'stspos'		  =>1
					
					); 	
					
					$det_Jurnaltes[]  = array(
					'nomor'         => '',
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => $coa_wip,
					'keterangan'    => $keterangan1,
					'no_reff'       => $id.$noso,
					'debet'         => 0,
					'kredit'        => $finishgood,
					'jenis_jurnal'  => 'WIP to Fg Spool tanki',
					'no_request'    => $no_request,
					'stspos'		  =>1
					
					); 		
			
		}

		
				
			
		
		$this->db->query("delete from jurnaltras WHERE jenis_jurnal='finishgood part to WIP' and no_reff ='$idtrans' AND tanggal ='".$Date."'"); 
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
		
		
		
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
		$Bln	= substr($tgl_voucher,5,2);
		$Thn	= substr($tgl_voucher,0,4);
		$idlaporan = $id;
		$Keterangan_INV = 'WIP to Fg Spool tanki'.$keterangan;
		$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalfg, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
		$this->db->insert(DBACC.'.javh',$dataJVhead);
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
			$this->db->insert(DBACC.'.jurnal',$datadetail);
		}
		unset($det_Jurnaltes);unset($datadetail);
		
	}

	public function so_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/so_material';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'QC SO Material',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data qc so material');
		$this->load->view('Qc/so_material', $data);
	}

	public function server_side_so_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/so_material';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_so_material(
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

			$check	= "<button class='btn btn-sm btn-success check_real' title='Release QC' data-kode_trans='" . $row['kode_trans'] . "'><i class='fa fa-check'></i></button>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['so_number'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			// $nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['qty'] . "</div>";
			$nestedData[]	= "<div align='center'>
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

	public function query_data_so_material($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		$where2 = "";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.so_number
			FROM
				outgoing_so_material a
				LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp)=b.id_bq,
				(SELECT @row:=0) r
			WHERE 1=1 " . $where . " " . $where2 . "
				AND a.qc_date IS NULL
				AND a.deleted_date IS NULL
				AND (
					a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.so_number',
			2 => 'no_ipp',
			3 => 'qty'
		);

		$sql .= " ORDER BY a.created_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_qc_so_material(){
		$kode_trans 	= $this->uri->segment(3);
		$header = $this->db->get_where('outgoing_so_material', array('kode_trans' => $kode_trans))->result_array();
		$result = $this->db->get_where('outgoing_so_material_detail', array('kode_trans' => $kode_trans))->result_array();
		$data = [
			'kode_trans' => $kode_trans,
			'header' => $header,
			'result' => $result,
		];
		$this->load->view('Qc/modal_qc_so_material', $data);
	}

	public function process_qc_so_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$kode_trans		= $data['kode_trans'];
		$dateTime 		= date('Y-m-d H:i:s');

		$ArrFlagRelease = [
			'qc_by' => $data_session['ORI_User']['username'],
			'qc_date' => $dateTime
		];

		$this->db->trans_start();
		$this->db->where('kode_trans', $kode_trans);
		$this->db->update('outgoing_so_material', $ArrFlagRelease);
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
			history('Release QC SO Material = ' . $kode_trans);
		}
		echo json_encode($Arr_Kembali);
	}

	public function process_reject_qc_so_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$kode_trans		= $data['kode_trans'];
		$dateTime 		= date('Y-m-d H:i:s');

		//PROSES DETAIL
		$getDetail = $this->db->get_where('outgoing_so_material_detail', array('kode_trans' => $kode_trans))->result_array();
		$ArrUpdate = [];
		$ArrMaterial = [];
		foreach ($getDetail as $key => $value) {
			$ArrMaterial[$key]['id_material'] 	= $value['id_material'];
			$ArrMaterial[$key]['gudang'] 	    = $value['id_gudang'];
			$ArrMaterial[$key]['qty'] 	        = $value['qty'];
		}

		//GROUPING UPDATE MATERIAL PER GUDANG
		$ArrGrouping = [];
		foreach ($ArrMaterial as $key => $value) {
			$ArrGrouping[$value['gudang']][$key]['id'] = $value['id_material'];
			$ArrGrouping[$value['gudang']][$key]['qty'] = $value['qty'];
		}

		$gudang_dari = 15;
		foreach ($ArrGrouping as $key => $value) {
			move_warehouse($value, $gudang_dari, $key, $kode_trans);
		}

		$ArrFlagRelease = [
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => $dateTime
		];

		$ArrFlagReleaseAdjustment = [
			'deleted_by' => $data_session['ORI_User']['username'],
			'deleted_date' => $dateTime,
			'status_id' => '0'
		];


		// exit;

		$this->db->trans_start();
			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('outgoing_so_material', $ArrFlagRelease);

			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('warehouse_adjustment', $ArrFlagReleaseAdjustment);
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
			history('Reject QC SO Material = ' . $kode_trans);
		}
		echo json_encode($Arr_Kembali);
	}
}
