<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc_tanki extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('tanki_model');
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
			'title'			=> 'Quality Control Tanki',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data qc tanki');
		$this->load->view('Tanki/index_qc', $data);
	}

	public function server_side_qc_tanki()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc_tanki(
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

			$check	= "<button class='btn btn-sm btn-success check_real' title='Release QC' data-id_milik='".$row['id_milik']."' data-kode_spk='".$row['kode_spk']."' data-id_pro='".$row['id']."'><i class='fa fa-check'></i></button>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_so'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['id_product'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['qty_qc'] . "</div>";
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

	public function query_data_qc_tanki($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "AND a.product_code_cut = 'tanki'";
		$group_by = "GROUP BY
		a.id_produksi,
		a.kode_spk,
		a.id_milik,
		a.upload_date";
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				MIN(a.product_ke) AS qty_min,
				MAX(a.product_ke) AS qty_max,
                b.spk1_cost,
                b.spk2_cost,
				b.qty AS tot_qty,
				MIN(a.closing_produksi_date) AS min_date_produksi,
				MAX(a.closing_produksi_date) AS max_date_produksi,
                SUBSTRING(a.product_code,1,9) as no_so,
                REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
                COUNT(a.id) AS qty_qc
			FROM
				production_detail a
                LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik,
				(SELECT @row:=0) r
		    WHERE 1=1 
                AND a.upload_real = 'Y' 
                AND a.upload_real2 = 'Y' 
                AND a.kode_spk IS NOT NULL
				AND a.fg_date IS NULL
				AND a.closing_produksi_date IS NOT NULL
                ".$where."
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_produksi LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			" . $group_by . "
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'product_code',
			2 => 'id_produksi',
			3 => 'no_spk',
			4 => 'id_product'
		);

		$sql .= " ORDER BY a.closing_produksi_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function modal_qc_tanki()
	{
		$id_milik 	= $this->uri->segment(3);
		$kode_spk 	= $this->uri->segment(4);
		$id_pro_detail 	= $this->uri->segment(5);

		$get_time = get_name('production_detail', 'upload_date', 'id', $id_pro_detail);

		$get_split_code = $this->db->order_by('id', 'ASC')->get_where('production_detail', array('kode_spk' => $kode_spk, 'id_milik' => $id_milik, 'upload_real' => 'Y', 'upload_real2' => 'Y', 'upload_date' => $get_time))->result_array();
		$get_spk = $this->db->get_where('production_spk', array('kode_spk' => $kode_spk, 'id_milik' => $id_milik))->result();
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
			'id_produksi' => $get_split_code[0]['id_produksi'],
			'id_product' => $get_split_code[0]['id_product'],
			'id_milik' => $id_milik,
			'id_milik2' => $get_split_code[0]['id'],
			'kode_product' => $explode,
			'time_uniq' => $get_time,
			'first_id' => $id_pro_detail,
			'tanki_model' => $this->tanki_model,
		];
		$this->load->view('Tanki/modal_qc_tanki', $data);
	}

    public function process_qc_tanki()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_produksi	= $data['id_produksi'];
		$id_product		= $data['id_product'];
		$id_milik		= $data['id_milik']; //uniq production detail
		$id_milik2		= $data['id_milik2']; //id milik product
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

		$restDetail1	    = $this->db->get_where($HelpDet3, array('id_product' => 'tanki', 'id_production_detail' => $id_milik2))->result_array();
		$restDetail2	    = $this->db->get_where($HelpDet4, array('id_product' => 'tanki', 'id_production_detail' => $id_milik2))->result_array();
		$restDetail3	    = $this->db->get_where($HelpDet5, array('id_product' => 'tanki', 'id_production_detail' => $id_milik2))->result_array();

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
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
			history('Release QC = ' . $data['id_produksi'] . ' / ' . $data['id_milik'] . ' / ' . $data['id_product']);
		}
		echo json_encode($Arr_Kembali);
	}

    public function real_send_release_fg()
	{
		// ini_set('memory_limit', '255M');
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$datetime 		= date('Y-m-d H:i:s');
		$datetimeNm 	= date('Ymdhis');
		$detail			= $data['check'];
		$detail_data	= $data['detail'];
		$id_produksi	= $data['id_produksi'];
		$no_ipp			= str_replace('PRO-', '', $data['id_produksi']);
		$id_product		= $data['id_product'];
		$id_milik		= $data['id_milik'];
		$time_uniq		= $data['time_uniq'];
		$first_id		= $data['first_id'];

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

			$ArrHistFG[$key]['tipe_product'] = 'tanki';
			$ArrHistFG[$key]['id_product'] = $value;
			$ArrHistFG[$key]['id_milik'] = $id_milik;
			$ArrHistFG[$key]['tipe'] = 'in';
			$ArrHistFG[$key]['kode'] = $detail_data[$key]['daycode'];
			$ArrHistFG[$key]['tanggal'] = date('Y-m-d');
			$ArrHistFG[$key]['keterangan'] = 'qc product tanki';
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
			$temp[$value['batch_number']] += $value['berat'] / $total_qty * $total_cek;
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
		// if (!empty($detail)) {
		// 	insert_jurnal_qc($detail, $kode_pro);
		// }

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
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Failed process data. Please try again later ...',
				'status'	=> 0,
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
				'kode_spk'     => $kode_spk,
				'id_produksi'     => $id_produksi,
				'id_milik'     => $id_milik,
				'id_pro_detail' => $first_id
			);
			history('Release QC to FG = ' . $data['id_produksi'] . ' / ' . $data['id_milik'] . ' / ' . $data['id_product']);
			$this->close_jurnal_finish_good($ArrIdPro,$kode_pro,$first_id);
		}
		echo json_encode($Arr_Kembali);
	}

	public function close_jurnal_finish_good($ArrIdPro, $kode_trans, $id_pro_det){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		
		//GROUP DATA
		$ArrGroup = [];
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
				$ArrGroup[$value]['qty'] = $qty;

				// $nilai_wip 		= (!empty($getSummary[0]['nilai_wip']))?$getSummary[0]['nilai_wip']:0;
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
		}

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_fg',$ArrGroup);
		}


	}

}