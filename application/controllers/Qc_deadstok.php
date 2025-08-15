<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc_deadstok extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
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
			'title'			=> 'Quality Control Deadstok',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data qc deadstok');
		$this->load->view('Deadstok/index_qc', $data);
	}

	public function server_side_qc_deadstok()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc_deadstok(
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

			$check	= "<button class='btn btn-sm btn-success check_real' title='Release QC' data-id_product='".$row['id_product']."' data-id_milik='".$row['id_milik']."'><i class='fa fa-check'></i></button>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_so'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
            $product_name = $row['product_name'].', '.$row['type_std'].' '.$row['resin'].', '.$row['product_spec'].' x '.number_format($row['length']);
			$nestedData[]	= "<div align='left'>".$product_name."</div>";
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

	public function query_data_qc_deadstok($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
                COUNT(id) AS qty_qc
			FROM
				deadstok a,
				(SELECT @row:=0) r
			WHERE 1=1 
				AND a.qc_date IS NULL
				AND a.deleted_date IS NULL
                AND a.id_booking IS NOT NULL
				AND a.process_next = '1'
				AND (
					a.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.no_so LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
            GROUP BY a.id_product, a.id_milik
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_so',
			2 => 'no_ipp',
			3 => 'no_spk'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_qc_deadstok()
	{
		$id_product 	= $this->uri->segment(3);
		$id_milik 	    = $this->uri->segment(4);

		$result = $this->db->get_where('deadstok', array('id_product'=>$id_product,'id_milik'=>$id_milik,'process_next'=>'1'))->result_array();
		$data = [
			'result' => $result,
			'id_product' => $id_product,
			'kode_trans' => $id_product,
			'id_milik' => $id_milik,
		];
		$this->load->view('Deadstok/modal_qc_deadstok', $data);
	}

	public function process_qc_deadstok()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_product		= $data['id_product'];
		$id_milik		= $data['id_milik'];
		$dateTime 		= date('Y-m-d H:i:s');
		$username 		= $data_session['ORI_User']['username'];

		$ArrFlagRelease = [
			'qc_by' => $username,
			'qc_date' => $dateTime
		];

		$this->db->trans_start();
            $this->db->where('process_next', '1');
            $this->db->where('qc_date', NULL);
            $this->db->where('id_milik', $id_milik);
            $this->db->where('id_product', $id_product);
            $this->db->update('deadstok', $ArrFlagRelease);
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
			history('Release QC Deadstok :'.$id_product.'/'.$id_milik);
		}
		echo json_encode($Arr_Kembali);
	}

	public function process_reject_qc_deadstok()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$id_product		= $data['id_product'];
		$id_milik		= $data['id_milik'];
		$dateTime 		= date('Y-m-d H:i:s');

		//PROSES DETAIL
		// $getDetail = $this->db->get_where('outgoing_field_joint_detail', array('kode_trans' => $kode_trans))->result_array();
		
		$ArrUpdateDeadstok = [
			'id_booking' => NULL,
			'process_next' => NULL,
			'id_milik' => NULL,
			'no_so' => NULL,
			'no_spk' => NULL,
			'no_ipp' => NULL,
			'id_booking' => NULL,
		];

		$ArrUpdateProduksi = [
			'id_product_deadstok' => NULL,
			'id_deadstok_dipakai' => NULL,
			'lock_deadstok' => '0',
			'booking_by' => NULL,
			'booking_date' => NULL,
			'kode_booking_deadstok' => NULL,
			'upload_real' => 'N',
			'upload_real2' => 'N',
			'kode_spk' => NULL,
			'fg_date' => NULL,
			'closing_produksi_date' => NULL,
			'no_spk' => NULL,
			'product_code' => NULL,
		];

		// print_r($data);
		// exit;

		$getIdDeadstok = $this->db->get_where('deadstok',array('id_product'=>$id_product,'id_milik'=>$id_milik,'process_next'=>'1'))->result_array();
		$ArrID = [];
		foreach ($getIdDeadstok as $key => $value) {
			$ArrID[] = $value['id'];
		}

		$this->db->trans_start();
		$this->db->where('id_product', $id_product);
		$this->db->where('id_milik', $id_milik);
		$this->db->where('process_next', '1');
		$this->db->update('deadstok', $ArrUpdateDeadstok);

		if(!empty($ArrID)){
			$this->db->where_in('id_deadstok_dipakai', $ArrID);
			$this->db->where('id_product_deadstok', $id_product);
			$this->db->where('id_milik', $id_milik);
			$this->db->where('booking_date !=', NULL);
			$this->db->update('production_detail', $ArrUpdateProduksi);
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
			history('Reject QC deadstok: '.$id_product.'/'.$id_milik);
		}
		echo json_encode($Arr_Kembali);
	}

	//QC modifikasi
	public function server_side_qc_deadstok_modif()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_qc_deadstok_modif(
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

			$check	= "<button class='btn btn-sm btn-success check_real_modif' title='Release QC' data-kode='".$row['kode']."'><i class='fa fa-check'></i></button>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_so'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_ipp'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
            $product_name = $row['product_name'].', '.$row['product_spec'];
			$nestedData[]	= "<div align='left'>".$product_name."</div>";
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

	public function query_data_qc_deadstok_modif($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
                COUNT(a.id) AS qty_qc,
				b.no_ipp,
				b.no_spk,
				b.no_so,
				b.product_name,
				b.product_spec
			FROM
				deadstok_modif a
				LEFT JOIN deadstok b ON a.id_deadstok=b.id,
				(SELECT @row:=0) r
			WHERE 1=1 
				AND a.qc_date IS NULL
				AND a.deleted_date IS NULL
				AND a.status != 'REJECTED'
				AND a.status_close_produksi = 'Y'
				AND (
					b.no_ipp LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.no_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.no_so LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
            GROUP BY a.kode
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'b.no_so',
			2 => 'b.no_ipp',
			3 => 'b.no_spk'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_qc_deadstok_modif()
	{
		$kode 	= $this->uri->segment(3);

		$result = $this->db
						->select('a.*,b.no_spk,b.no_ipp,b.no_so,b.product_name, b.product_spec')
						->join('deadstok b','a.id_deadstok=b.id','left')
						->get_where('deadstok_modif a', array('a.kode'=>$kode))
						->result_array();
		$data = [
			'result' => $result,
			'kode' => $kode
		];
		$this->load->view('Deadstok/modal_qc_deadstok_modif', $data);
	}

	public function process_qc_deadstok_modif()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$kode			= $data['kode'];
		$dateTime 		= date('Y-m-d H:i:s');
		$username 		= $data_session['ORI_User']['username'];

		$ArrFlagRelease = [
			'qc_by' => $username,
			'qc_date' => $dateTime
		];

		$checkIdDeadstok	= $this->db->get_where('deadstok_modif',array('kode'=>$kode))->result_array();
		$kode_spk 			= (!empty($checkIdDeadstok[0]['kode_spk']))?$checkIdDeadstok[0]['kode_spk']:null;

		$getQCDeadstockModif = $this->db->get_where('data_erp_wip_group',array('kode_trans'=>$kode_spk))->result_array();
		$ArrIN_WIP_MATERIAL = [];
		$ArrIN_FG_MATERIAL = [];
		foreach ($getQCDeadstockModif as $key => $value) {
			$ArrIN_WIP_MATERIAL[$key]['tanggal'] = date('Y-m-d');
			$ArrIN_WIP_MATERIAL[$key]['keterangan'] = 'WIP to Finish Good (Deadstock Modif)';
			$ArrIN_WIP_MATERIAL[$key]['no_so'] = $value['no_so'];
			$ArrIN_WIP_MATERIAL[$key]['product'] = $value['product'];
			$ArrIN_WIP_MATERIAL[$key]['no_spk'] = $value['no_spk'];
			$ArrIN_WIP_MATERIAL[$key]['kode_trans'] = $value['kode_trans'];
			$ArrIN_WIP_MATERIAL[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrIN_WIP_MATERIAL[$key]['qty'] = 1;
			$ArrIN_WIP_MATERIAL[$key]['nilai_wip'] = $value['nilai_wip'];
			$ArrIN_WIP_MATERIAL[$key]['material'] = $value['material'];
			$ArrIN_WIP_MATERIAL[$key]['wip_direct'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['wip_indirect'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['wip_consumable'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['wip_foh'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['created_by'] = $username;
			$ArrIN_WIP_MATERIAL[$key]['created_date'] = $dateTime;
			$ArrIN_WIP_MATERIAL[$key]['id_trans'] =  $value['id_trans'];
			$ArrIN_WIP_MATERIAL[$key]['jenis'] =  'out deadstok modif';

			$ArrIN_WIP_MATERIAL[$key]['id_material'] =  $value['id_material'];
			$ArrIN_WIP_MATERIAL[$key]['nm_material'] = $value['nm_material'];
			$ArrIN_WIP_MATERIAL[$key]['qty_mat'] =  $value['qty_mat'];
			$ArrIN_WIP_MATERIAL[$key]['cost_book'] =  $value['cost_book'];
			$ArrIN_WIP_MATERIAL[$key]['gudang'] =  $value['gudang'];

			$ArrIN_FG_MATERIAL[$key]['tanggal'] = date('Y-m-d');
			$ArrIN_FG_MATERIAL[$key]['keterangan'] = 'WIP to Finish Good (Deadstock Modif)';
			$ArrIN_FG_MATERIAL[$key]['no_so'] = $value['no_so'];
			$ArrIN_FG_MATERIAL[$key]['product'] = $value['product'];
			$ArrIN_FG_MATERIAL[$key]['no_spk'] = $value['no_spk'];
			$ArrIN_FG_MATERIAL[$key]['kode_trans'] = $value['kode_trans'];
			$ArrIN_FG_MATERIAL[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrIN_FG_MATERIAL[$key]['qty'] = 1;
			$ArrIN_FG_MATERIAL[$key]['nilai_unit'] = $value['nilai_wip'];
			$ArrIN_FG_MATERIAL[$key]['nilai_wip'] = $value['nilai_wip'];
			$ArrIN_FG_MATERIAL[$key]['material'] = $value['material'];
			$ArrIN_FG_MATERIAL[$key]['wip_direct'] =  0;
			$ArrIN_FG_MATERIAL[$key]['wip_indirect'] =  0;
			$ArrIN_FG_MATERIAL[$key]['wip_consumable'] =  0;
			$ArrIN_FG_MATERIAL[$key]['wip_foh'] =  0;
			$ArrIN_FG_MATERIAL[$key]['created_by'] = $username;
			$ArrIN_FG_MATERIAL[$key]['created_date'] = $dateTime;
			$ArrIN_FG_MATERIAL[$key]['id_trans'] =  $value['id_trans'];
			$ArrIN_FG_MATERIAL[$key]['jenis'] =  'in deadstok modif';

			$ArrIN_FG_MATERIAL[$key]['id_material'] =  $value['id_material'];
			$ArrIN_FG_MATERIAL[$key]['nm_material'] = $value['nm_material'];
			$ArrIN_FG_MATERIAL[$key]['qty_mat'] =  $value['qty_mat'];
			$ArrIN_FG_MATERIAL[$key]['cost_book'] =  $value['cost_book'];
			$ArrIN_FG_MATERIAL[$key]['gudang'] =  $value['gudang'];
		}

		// print_r($ArrIN_WIP_MATERIAL);
		// print_r($ArrIN_FG_MATERIAL);
		// exit;

		$this->db->trans_start();
            $this->db->where('qc_date', NULL);
            $this->db->where('kode', $kode);
            $this->db->update('deadstok_modif', $ArrFlagRelease);

			if(!empty($ArrIN_WIP_MATERIAL)){
				$this->db->insert_batch('data_erp_wip_group',$ArrIN_WIP_MATERIAL);
			}

			if(!empty($ArrIN_FG_MATERIAL)){
				$this->db->insert_batch('data_erp_fg',$ArrIN_FG_MATERIAL);
				$this->jurnalFG($kode_spk);
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
			history('Release QC Deadstok modifikasi :'.$kode);
		}
		echo json_encode($Arr_Kembali);
	}

	public function process_reject_qc_deadstok_modif()
	{
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$kode			= $data['kode'];
		$dateTime 		= date('Y-m-d H:i:s');
		$username 		= $data_session['ORI_User']['username'];

		$ArrUpdateDeadstok = [
			'id_booking' => NULL,
			'process_next' => NULL,
			'id_milik' => NULL,
			'no_so' => NULL,
			'no_spk' => NULL,
			'no_ipp' => NULL
		];

		$ArrUpdateProduksi = [
			'id_product_deadstok' => NULL,
			'id_deadstok_dipakai' => NULL,
			'lock_deadstok' => '0',
			'booking_by' => NULL,
			'booking_date' => NULL,
			'kode_booking_deadstok' => NULL,
			'upload_real' => 'N',
			'upload_real2' => 'N',
			'kode_spk' => NULL,
			'fg_date' => NULL,
			'closing_produksi_date' => NULL,
			'no_spk' => NULL,
			'product_code' => NULL,
		];

		$ArrFlagRelease = [
			'status' => 'REJECTED'
		];

		$checkIdDeadstok	= $this->db->get_where('deadstok_modif',array('kode'=>$kode))->result_array();
		$id_deadstok 		= (!empty($checkIdDeadstok[0]['id_deadstok']))?$checkIdDeadstok[0]['id_deadstok']:0;
		$kode_spk 			= (!empty($checkIdDeadstok[0]['kode_spk']))?$checkIdDeadstok[0]['kode_spk']:null;

		$getDeadstok		= $this->db->get_where('deadstok',array('id'=>$id_deadstok))->result_array();
		$id_product 		= (!empty($getDeadstok[0]['id_product']))?$getDeadstok[0]['id_product']:0;
		$id_milik 			= (!empty($getDeadstok[0]['id_milik']))?$getDeadstok[0]['id_milik']:0;

		$getIdDeadstok = $this->db->get_where('deadstok',array('id_product'=>$id_product,'id_milik'=>$id_milik,'process_next'=>'4'))->result_array();
		$ArrID = [];
		foreach ($getIdDeadstok as $key => $value) {
			$ArrID[] = $value['id'];
		}

		$getQCDeadstockModif = $this->db->get_where('data_erp_wip_group',array('kode_trans'=>$kode_spk))->result_array();
		$ArrIN_WIP_MATERIAL = [];
		foreach ($getQCDeadstockModif as $key => $value) {
			$ArrIN_WIP_MATERIAL[$key]['tanggal'] = date('Y-m-d');
			$ArrIN_WIP_MATERIAL[$key]['keterangan'] = 'WIP to Reject (Deadstock Modif)';
			$ArrIN_WIP_MATERIAL[$key]['no_so'] = $value['no_so'];
			$ArrIN_WIP_MATERIAL[$key]['product'] = $value['product'];
			$ArrIN_WIP_MATERIAL[$key]['no_spk'] = $value['no_spk'];
			$ArrIN_WIP_MATERIAL[$key]['kode_trans'] = $value['kode_trans'];
			$ArrIN_WIP_MATERIAL[$key]['id_pro_det'] = $value['id_pro_det'];
			$ArrIN_WIP_MATERIAL[$key]['qty'] = 1;
			$ArrIN_WIP_MATERIAL[$key]['nilai_wip'] = $value['nilai_wip'];
			$ArrIN_WIP_MATERIAL[$key]['material'] = $value['material'];
			$ArrIN_WIP_MATERIAL[$key]['wip_direct'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['wip_indirect'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['wip_consumable'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['wip_foh'] =  0;
			$ArrIN_WIP_MATERIAL[$key]['created_by'] = $username;
			$ArrIN_WIP_MATERIAL[$key]['created_date'] = $dateTime;
			$ArrIN_WIP_MATERIAL[$key]['id_trans'] =  $value['id_trans'];
			$ArrIN_WIP_MATERIAL[$key]['jenis'] =  'out deadstok modif';

			$ArrIN_WIP_MATERIAL[$key]['id_material'] =  $value['id_material'];
			$ArrIN_WIP_MATERIAL[$key]['nm_material'] = $value['nm_material'];
			$ArrIN_WIP_MATERIAL[$key]['qty_mat'] =  $value['qty_mat'];
			$ArrIN_WIP_MATERIAL[$key]['cost_book'] =  $value['cost_book'];
			$ArrIN_WIP_MATERIAL[$key]['gudang'] =  $value['gudang'];
		}

		// print_r($getDeadstok);
		// exit;

		$this->db->trans_start();
			$this->db->where('qc_date', NULL);
			$this->db->where('kode', $kode);
			$this->db->update('deadstok_modif', $ArrFlagRelease);

			$this->db->where('id_product', $id_product);
			$this->db->where('id_milik', $id_milik);
			$this->db->where('process_next', '4');
			$this->db->update('deadstok', $ArrUpdateDeadstok);

			if(!empty($ArrIN_WIP_MATERIAL)){
				$this->db->insert_batch('data_erp_wip_group',$ArrIN_WIP_MATERIAL);
			}

			if(!empty($ArrID)){
				$this->db->where_in('id_deadstok_dipakai', $ArrID);
				$this->db->where('id_product_deadstok', $id_product);
				$this->db->where('id_milik', $id_milik);
				$this->db->where('booking_date !=', NULL);
				$this->db->update('production_detail', $ArrUpdateProduksi);
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
			history('Reject QC deadstok modifikasi: '.$kode);
		}
		echo json_encode($Arr_Kembali);
	}

	function jurnalFG($idtrans){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	        //$idtrans = str_replace('-','',$kode);

			
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
					  'jenis_jurnal'  => 'WIP to Fg deadstock',
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
					  'jenis_jurnal'  => 'WIP to Fg deadstock',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 ); 		
				
			}

			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='finishgood deadstock to WIP' and no_reff ='$idtrans' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'WIP to Fg deadstock'.$keterangan;
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

}