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

		$this->db->trans_start();
		$this->db->where('id_product', $id_product);
		$this->db->where('id_milik', $id_milik);
		$this->db->where('process_next', '1');
		$this->db->update('deadstok', $ArrUpdateDeadstok);


		$this->db->where('id_product_deadstok', $id_product);
		$this->db->where('id_milik', $id_milik);
		$this->db->where('booking_date !=', NULL);
		$this->db->update('production_detail', $ArrUpdateProduksi);
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

		$this->db->trans_start();
            $this->db->where('qc_date', NULL);
            $this->db->where('kode', $kode);
            $this->db->update('deadstok_modif', $ArrFlagRelease);
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

		$ArrFlagRelease = [
			'status' => 'REJECTED'
		];

		$checkIdDeadstok	= $this->db->get_where('deadstok_modif',array('kode'=>$kode))->result_array();
		$id_deadstok 		= (!empty($checkIdDeadstok[0]['id_deadstok']))?$checkIdDeadstok[0]['id_deadstok']:0;

		$getDeadstok		= $this->db->get_where('deadstok',array('id'=>$id_deadstok))->result_array();
		$id_product 		= (!empty($getDeadstok[0]['id_product']))?$getDeadstok[0]['id_product']:0;
		$id_milik 			= (!empty($getDeadstok[0]['id_milik']))?$getDeadstok[0]['id_milik']:0;

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

			$this->db->where('id_product_deadstok', $id_product);
			$this->db->where('id_milik', $id_milik);
			$this->db->where('booking_date !=', NULL);
			$this->db->update('production_detail', $ArrUpdateProduksi);
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

}