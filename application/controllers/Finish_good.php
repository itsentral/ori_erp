<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Finish_good extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if (!$this->session->userdata('isORIlogin')) {
			redirect('login');
		}
	}

	public function index()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
		// $Arr_Akses			= getAcccesmenu($controller);

		// if($Arr_Akses['read'] !='1'){
		// 	$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		// 	redirect(site_url('dashboard'));
		// }
		$tanda = $this->uri->segment(3);
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Finish Good',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'tanda'			=> $tanda,
			// 'akses_menu'	=> $Arr_Akses
		);
		history('View data finish good');
		$this->load->view('Finish_good/index', $data);
	}

	public function server_side_qc()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
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

			$update_spk_1 = "";
			$update_spk_2 = "";
			$print_spk = "";
			$view_spk = "";
			$NOMOR_SO = explode('-', $row['product_code']);
			$no_ipp = str_replace('PRO-', '', $row['id_produksi']);
			$customer = get_name('production', 'nm_customer', 'no_ipp', $no_ipp);
			$project = get_name('production', 'project', 'no_ipp', $no_ipp);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['no_spk'] . "</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['id_category']) . "</div>";
			$nestedData[]	= "<div align='center'>" . $NOMOR_SO[0] . "</div>";
			$nestedData[]	= "<div align='left'>" . $customer . "</div>";
			$nestedData[]	= "<div align='left'>" . $project . "</div>";
			if ($FLAG == 'cutting') {
			$nestedData[]	= "<div align='left'>" . spec_bq2($row['id_milik']) . ", cut: " . number_format($row['length']) . "</div>";
			}
			else{
				$nestedData[]	= "<div align='left'>" . spec_bq2($row['id_milik']) . "</div>";
			}
			$IMPLODE = explode('.', $row['product_code']);

			$Cutting_ke = '';
			if ($FLAG == 'cutting') {
				$Cutting_ke = "." . $row['cutting_ke'];
			}

			$product_code = $IMPLODE[0] . '.' . $row['product_ke'] . $Cutting_ke;
			$nestedData[]	= "<div align='center'>" . $product_code . "</div>";

			$get_ID_PRO = $this->db->select('id, upload_date')->get_where('production_detail', array('id_milik' => $row['id_milik'], 'kode_spk' => $row['kode_spk'], 'upload_date' => $row['upload_date']))->result();
			$ID_PRO = $get_ID_PRO[0]->id;

			$SUM_DETAIL = $this->db->select('SUM(material_terpakai) AS berat')->get_where('production_real_detail', array('catatan_programmer' => $row['kode_spk'] . '/' . $get_ID_PRO[0]->upload_date, 'id_production_detail' => $ID_PRO))->result();
			$SUM_PLUS = $this->db->select('SUM(material_terpakai) AS berat')->get_where('tmp_production_real_detail_plus', array('catatan_programmer' => $row['kode_spk'] . '/' . $get_ID_PRO[0]->upload_date, 'id_production_detail' => $ID_PRO))->result();
			$SUM_ADD = $this->db->select('SUM(material_terpakai) AS berat')->get_where('tmp_production_real_detail_add', array('catatan_programmer' => $row['kode_spk'] . '/' . $get_ID_PRO[0]->upload_date, 'id_production_detail' => $ID_PRO))->result();

			$TOT_QTY = $row['tot_qty'];
			$TOTMAT = 0;
			if ($TOT_QTY > 0) {
				$TOTMAT = ($SUM_DETAIL[0]->berat + $SUM_PLUS[0]->berat + $SUM_ADD[0]->berat) / $TOT_QTY;
			}

			$TOT_MATERIAL = $TOTMAT;
			if ($FLAG == 'cutting') {
				$TOT_MATERIAL = ($TOTMAT / $row['length_awal']) * $row['length'];
			}
			$nestedData[]	= "<div align='right'>" . number_format($TOT_MATERIAL, 4) . "</div>";
			if ($FLAG == 'cutting') {
				$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-warning look_history' data-length='" . $row['length'] . "' data-length_awal='" . $row['length_awal'] . "' data-kode_spk='" . $row['kode_spk'] . "' data-id_production_detail='" . $ID_PRO . "' data-category='2' data-qty='" . $TOT_QTY . "' style='margin-bottom:2px'><i class='fa fa-eye'></i></button>
								<button type='button' target='_blank' class='btn btn-sm btn-default qr-cutting' data-id_cutting='cut-" . $row['id_cutting'] . "' data-id_produksi='" . $row['id_produksi'] . "' data-id_milik='" . $row['id_milik'] . "' data-kode_spk='" . $row['kode_spk'] . "' data-id_pro_detail='" . $ID_PRO . "' style='margin-bottom:2px;padding:2px 6px'><i class='fa fa-qrcode fa-2x'></i></button></div>
								";
			} else {
				$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-warning look_history' data-length='" . $row['length'] . "' data-length_awal='" . $row['length_awal'] . "' data-kode_spk='" . $row['kode_spk'] . "' data-id_production_detail='" . $ID_PRO . "' data-category='2' data-qty='" . $TOT_QTY . "' style='margin-bottom:2px'><i class='fa fa-eye'></i></button>
								<button type='button' class='btn btn-sm btn-default qr' data-id_produksi='" . $row['id_produksi'] . "' data-id_milik='" . $row['id_milik'] . "' data-kode_spk='" . $row['kode_spk'] . "' data-id_pro_detail='" . $ID_PRO . "' style='margin-bottom:2px;padding:2px 6px'><i class='fa fa-qrcode fa-2x'></i></button></div>
								";
			}

			if ($FLAG == 'pipe') {
				if ($row['sts_cutting'] == 'N') {
					$nestedData[]	= "<div align='center'><input type='checkbox' name='check[" . $row['id'] . "]' class='chk_personal' value='" . $row['id'] . "'></div>";
				} else {
					$nestedData[]	= "<div align='center'></div>";
				}
			}
			if ($FLAG == 'cutting') {
				$ck = ($row['flag_qr_cutting'] == 'Y') ? '<i class="fa fa-check text-success"></i>' : '-';
				$nestedData[] = "<div class='text-center'>" . $ck . "</div>";
			}
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
		$LEFT_JOIN = "";
		$FIELD_CUTTING = "";
		if ($status == 'pipe') {
			$where = " AND c.id_category='pipe'  AND a.sts_cutting != 'Y' ";
			$LEFT_JOIN = ",";
			$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,NULL as id_cutting,NULL as flag_qr_cutting,";
		}
		if ($status == 'cutting') {
			$where = " AND d.id IS NOT NULL AND (c.id_category='pipe' OR d.id_deadstok IS NOT NULL) AND d.app = 'Y' AND f.qc_date IS NOT NULL AND (f.kode_delivery IS NULL AND f.spool_induk IS NULL) ";
			$LEFT_JOIN = " LEFT JOIN so_cutting_detail f ON d.id = f.id_header,";
			$FIELD_CUTTING = "f.length AS length_awal, f.length_split AS length, f.cutting_ke,f.id as id_cutting,f.flag_qr as flag_qr_cutting,";
		}
		if ($status == 'fitting') {
			$where = " AND c.cutting='N' AND c.id_category!='pipe' AND c.id_category!='pipe slongsong' ";
			$LEFT_JOIN = ",";
			$FIELD_CUTTING = "0 AS length_awal, c.length, NULL AS cutting_ke,NULL as id_cutting,NULL as flag_qr_cutting,";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
                b.spk1_cost,
                b.spk2_cost,
                b.no_ipp,
                c.diameter_1,
                c.thickness,
				" . $FIELD_CUTTING . "
				e.qty AS tot_qty
			FROM
				production_detail a
                LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
                LEFT JOIN production_spk_parsial e ON b.id = e.id_spk AND a.upload_date=e.created_date AND e.spk = '1'
                LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
                LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke" . $LEFT_JOIN . "
				(SELECT @row:=0) r
		    WHERE 1=1 
                AND a.upload_real = 'Y'
                AND a.upload_real2 = 'Y' 
                AND (b.spk1_cost = 'Y' OR d.id_deadstok IS NOT NULL)
                AND (b.spk2_cost = 'Y' OR d.id_deadstok IS NOT NULL)
                AND a.kode_spk IS NOT NULL 
				AND a.kode_delivery IS NULL
				AND a.kode_spool IS NULL
				AND a.release_to_costing_date IS NOT NULL
				AND a.fg_date IS NOT NULL
				AND a.kode_delivery IS NULL
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
			1 => 'no_spk',
			2 => 'id_category',
			3 => 'kode_spk'
		);

		$sql .= " ORDER BY a.release_to_costing_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//SPOOL
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
			'title'			=> 'Finish Good Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data finish good spool');
		$this->load->view('Finish_good/spool', $data);
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

			$view = "<a href='" . base_url('finish_good/view_spool/' . $row['spool_induk']) . "' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";
			$qr = "<button type='button' class='btn btn-sm btn-default qr' data-id_produksi='" . $row['id_produksi'] . "' data-id_milik='" . $row['id_milik'] . "' data-kode_spk='" . $row['kode_spk'] . "' data-id_pro_detail='" . $row['spool_induk'] . "' style='margin-bottom:2px;padding:2px 6px'><i class='fa fa-qrcode fa-2x'></i></button>";

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
									" . $view . " " . $qr . "
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
		// if($no_ipp <button> 0){
		// 	$where = " AND a.id_produksi='".$no_ipp."' ";
		// }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				spool_group_release a,
				(SELECT @row:=0) r
			WHERE 1=1 " . $where . "
				AND a.kode_delivery IS NULL
				AND (
					a.kode_spk LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.kode_spool LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.product_code LIKE '%" . $this->db->escape_like_str($like_value) . "%'
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

	public function view_spool($kode_spool)
	{

		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$result = $this->db->group_by('kode_spool')->order_by('kode_spool', 'asc')->get_where('spool_group_release', array('spool_induk' => $kode_spool))->result_array();
		$data = array(
			'title'			=> 'Detail Spool',
			'action'		=> 'index',
			'result'		=> $result,
			'tanki_model'		=> $this->tanki_model,
			'spool_induk'		=> $kode_spool,
		);
		$this->load->view('Finish_good/view_spool', $data);
	}


	/* QR CODE */
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
		$this->load->view('Finish_good/modalCreateQR', $data);
	}

	public function print_qrcode($idmilik, $logo, $size)
	{
		$products = $this->db->select('a.*,z.nm_customer')->from('production_detail a')
			->join('production_spk b', 'a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik', 'left')
			->join('production z', 'REPLACE(a.id_produksi, "PRO-", "") = z.no_ipp', 'left')
			->where_in('a.id', explode("-", $idmilik))
			->get()->result();
		$ArrProducts = [];
		$IPP = [];
		foreach ($products as $product) {
			$ArrProducts[$product->id] = $product;
			$IPP[] = explode("-", $product->id_produksi)[1];
		}

		$explode = explode("-", $idmilik);
		$this->db->where_in('id', $explode)->update('production_detail', ['flag_qr' => 'Y', 'date_qr' => date('Y-m-d H:i:s')]);
		foreach ($explode as $key => $code) {
			$img = file_get_contents(base_url('qrcodegen/generate/' . $code . '/' . $code), '');
		}

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp IN ('" . implode("','", $IPP) . "') ";
		$row		= $this->db->query($qSupplier)->result();

		$ITEMS = [];
		foreach ($row as $r) {
			$ITEMS[$r->no_ipp] = $r;
		}


		$detail = $this->db->where_in('SUBSTR(id_bq,4,10)', $IPP)->get_where('so_detail_header')->result();

		foreach ($detail as $dtl) {
			$ArrDN[$dtl->id] = $dtl;
		}

		$sql 		= "	SELECT * FROM production_req_sp WHERE no_ipp IN ('" . implode("','", $IPP) . "') ";
		$prod		= $this->db->query($sql)->result();
		$ArrProd 	= [];
		foreach ($prod as $p) {
			$ArrProd[$p->no_ipp] = $p;
		}

		$path = base_url('assets/images/');
		if ($logo == 'ORI') {
			$LOGO = $path . 'ori_logo3_bw.jpg';
		} else if ($logo == 'NOV') {
			$LOGO = $path . 'nov_logo_bw.jpg';
		} else {
			$LOGO = '';
		}


		$data = [
			'explode' 			=> $explode,
			'products' 			=> $ArrProducts,
			'ITEMS' 			=> $ITEMS,
			'DN' 				=> $ArrDN,
			'logo' 				=> $LOGO,
			'size' 				=> $size,
			'ArrProd' 			=> $ArrProd,
		];

		$this->load->view('Finish_good/print_qrcode', $data);
	}

	public function print_qrcode_cutting($idCutting, $logo, $size)
	{
		$idCutt = str_replace("cut-", "", $idCutting);
		$cutting_dtl = $this->db->get_where('so_cutting_detail', ['id' => $idCutt])->row();

		$products = $this->db->select('a.*,z.nm_customer,b.so_number')->from('so_cutting_detail a')
			->join('so_number b', 'a.id_bq = b.id_bq', 'left')
			->join('production z', 'REPLACE(a.id_bq, "BQ-", "") = z.no_ipp', 'left')
			->where('a.id', $idCutt)
			->get()->row();


		$this->db->where('id', $idCutt)->update('so_cutting_detail', ['flag_qr' => 'Y', 'date_qr' => date('Y-m-d H:i:s')]);
		$img = file_get_contents(base_url('qrcodegen/generate/' . $idCutting . '/' . $idCutting), '');

		$IPP[] = explode("-", $products->id_bq)[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp IN ('" . implode("','", $IPP) . "') ";
		$proj		= $this->db->query($qSupplier)->row();

		$path = base_url('assets/images/');
		if ($logo == 'ORI') {
			$LOGO = $path . 'ori_logo3_bw.jpg';
		} else if ($logo == 'NOV') {
			$LOGO = $path . 'nov_logo_bw.jpg';
		} else {
			$LOGO = '';
		}
		$sql 		= "	SELECT * FROM production_req_sp WHERE no_ipp IN ('" . implode("','", $IPP) . "') ";
		$prod		= $this->db->query($sql)->result();
		$ArrProd 	= [];
		foreach ($prod as $p) {
			$ArrProd[$p->no_ipp] = $p;
		}

		$detail = $this->db->where_in('SUBSTR(id_bq,4,10)', $IPP)->get_where('so_detail_header')->result();
		$ArrDN = [];

		foreach ($detail as $dtl) {
			$ArrDN[$dtl->id] = $dtl;
		}
		$data = [
			'products' 	=> $products,
			'logo' 		=> $LOGO,
			'proj' 		=> $proj,
			'size' 		=> $size,
			'DN' 		=> $ArrDN,
			'ArrProd' 	=> $ArrProd,
		];

		$this->load->view('Finish_good/print_qrcode_cutting', $data);
	}

	public function print_qrcode_spool($id_spool, $logo, $size)
	{

		$products = $this->db->select('a.*,z.nm_customer')->from('spool_group_release a')
			->join('production z', 'REPLACE(a.id_produksi, "PRO-", "") = z.no_ipp', 'left')
			->where(['spool_induk'=> $id_spool, 'no_drawing !='=>''])
			->get()->row();
		$img = file_get_contents(base_url('qrcodegen/generate/' . $id_spool . '/' . $id_spool), '');

		$IPP[] = explode("-", $products->id_produksi)[1];
		$qSupplier 	= "SELECT * FROM production WHERE no_ipp IN ('" . implode("','", $IPP) . "') ";
		$proj		= $this->db->query($qSupplier)->row();

		$path = base_url('assets/images/');
		if ($logo == 'ORI') {
			$LOGO = $path . 'ori_logo3_bw.jpg';
		} else if ($logo == 'NOV') {
			$LOGO = $path . 'nov_logo_bw.jpg';
		} else {
			$LOGO = '';
		}
		$sql 		= "	SELECT * FROM production_req_sp WHERE no_ipp IN ('" . implode("','", $IPP) . "') ";
		// echo $sql;exit;
		$prod		= $this->db->query($sql)->result();
		$ArrProd 	= [];
		foreach ($prod as $p) {
			$ArrProd[$p->no_ipp] = $p;
		}

		$detail = $this->db->where_in('SUBSTR(id_bq,4,10)', $IPP)->get_where('so_detail_header')->result();
		foreach ($detail as $dtl) {
			$ArrDN[$dtl->id] = $dtl;
		}

		$dycode = $this->db->get_where('production_detail',['spool_induk'=>$id_spool])->row();
		foreach ($detail as $dtl) {
			$ArrDN[$dtl->id] = $dtl;
		}

		$data = [
			'spools' 	=> $products,
			'logo' 		=> $LOGO,
			'proj' 		=> $proj,
			'size' 		=> $size,
			'tanki_model'		=> $this->tanki_model,
			// 'DN' 				=> $ArrDN,
			'ArrProd' 	=> $ArrProd,
			'dycode' 	=> $dycode,
		];
		$this->load->view('Finish_good/print_qrcode_spool', $data);
	}

	// modal QR Spool
	public function modalCreateQRSpool()
	{

		$id_pro_detail 	= $this->uri->segment(3);
		$data = [
			'id_pro_detail' => $id_pro_detail
		];
		$this->load->view('Finish_good/modalCreateQRSpool', $data);
	}

	// modal QR Cutting
	public function modalCreateQRCutting()
	{

		$idCutting 	= $this->uri->segment(3);
		$data = [
			'idCutting' => $idCutting
		];
		$this->load->view('Finish_good/modalCreateQRCutting', $data);
	}
	/* END QR CODE */


	public function create_cutting()
	{
		$data 		= $this->input->post();

		$check 			= $data['check'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$get_detail_produksi = $this->db
			->select('*')
			->from('production_detail')
			->where_in('id', $check)
			->where('sts_cutting', 'N')
			->get()
			->result_array();

		$ArrUpdate = [];
		$ArrCutting = [];
		$ArrHistFG = [];
		foreach ($get_detail_produksi as $value => $valx) {
			$ArrUpdate[$value]['id'] 			= $valx['id'];;
			$ArrUpdate[$value]['sts_cutting'] 	= 'Y';

			$ArrCutting[$value]['id_milik'] 	= $valx['id_milik'];
			$ArrCutting[$value]['id_pro_det'] 	= $valx['id'];
			$ArrCutting[$value]['id_bq'] 		= str_replace('PRO-', 'BQ-', $valx['id_produksi']);
			$ArrCutting[$value]['id_category'] 	= $valx['id_category'];
			$ArrCutting[$value]['qty'] 			= $valx['qty'];
			$ArrCutting[$value]['qty_ke'] 		= $valx['product_ke'];

			$get_det = $this->db->get_where('so_detail_header', array('id' => $valx['id_milik']))->result();

			$ArrCutting[$value]['diameter_1'] 	= $get_det[0]->diameter_1;
			$ArrCutting[$value]['diameter_2'] 	= $get_det[0]->diameter_2;
			$ArrCutting[$value]['length'] 		= $get_det[0]->length;
			$ArrCutting[$value]['thickness'] 	= $get_det[0]->thickness;
			$ArrCutting[$value]['created_by'] 	= $username;
			$ArrCutting[$value]['created_date'] = $datetime;

			$ArrHistFG[$value]['tipe_product'] = 'pipe';
			$ArrHistFG[$value]['id_product'] = $valx['id'];
			$ArrHistFG[$value]['id_milik'] = $valx['id_milik'];
			$ArrHistFG[$value]['tipe'] = 'out';
			$ArrHistFG[$value]['kode'] = $valx['daycode'];
			$ArrHistFG[$value]['tanggal'] = date('Y-m-d');
			$ArrHistFG[$value]['keterangan'] = 'pipe to cutting';
			$ArrHistFG[$value]['hist_by'] = $username;
			$ArrHistFG[$value]['hist_date'] = $datetime;
		}
		// print_r($ArrUpdate);
		// print_r($ArrCutting);
		// exit;

		$this->db->trans_start();
		if (!empty($ArrUpdate)) {
			$this->db->update_batch('production_detail', $ArrUpdate, 'id');
		}
		if (!empty($ArrCutting)) {
			$this->db->insert_batch('so_cutting_header', $ArrCutting);
		}
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
			history('Create cutting via finish good delivery :' . json_encode($check));
		}

		echo json_encode($Arr_Kembali);
	}


	public function so_material()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1))) . '/so_material';
		$Arr_Akses			= getAcccesmenu($controller);

		if ($Arr_Akses['read'] != '1') {
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$tanda = $this->uri->segment(3);
		$data_Group	= $this->master_model->getArray('groups', array(), 'id', 'name');
		$data = array(
			'title'			=> 'Finish Good (SO Material)',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'tanda'			=> $tanda,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data finish good so material');
		$this->load->view('Finish_good/so_material', $data);
	}

	public function server_side_so_material()
	{
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_so_material(
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

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['kode_trans'] . "</div>";
			$nestedData[]	= "<div align='center'>" . $row['so_number'] . "</div>";
			$nestedData[]	= "<div align='left'>" . $row['note'] . "</div>";
			$nestedData[]	= "<div align='center'>" . date('d-M-Y', strtotime($row['created_date'])) . "</div>";
			$get_SUM = $this->db->select('SUM(qty_oke) AS total_berat')->get_where('warehouse_adjustment_detail', array('kode_trans' => $row['kode_trans']))->result();
			$nestedData[]	= "<div align='center'>" . number_format($get_SUM[0]->total_berat, 2) . " kg</div>";
			$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-warning look_history' data-kode_trans='" . $row['kode_trans'] . "'><i class='fa fa-eye'></i></button></div>";
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

	public function query_data_so_material($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$where = "";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				(SELECT c.no_ipp FROM warehouse_adjustment c WHERE a.no_ipp=c.kode_trans) AS no_bq,
				b.so_number
			FROM
				warehouse_adjustment a
				LEFT JOIN so_number b ON (SELECT c.no_ipp FROM warehouse_adjustment c WHERE a.no_ipp=c.kode_trans) = b.id_bq,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.category = 'outgoing subgudang'
				AND (SELECT c.no_ipp FROM warehouse_adjustment c WHERE a.no_ipp=c.kode_trans) LIKE 'BQ-IPP%'
                " . $where . "
				AND (
					a.kode_trans LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR b.so_number LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.note LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'b.so_number',
			3 => 'note'
		);

		$sql .= " ORDER BY a.created_date DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function detail_berat($kode_trans)
	{
		$detail_mat = $this->db->select('nm_material, nm_category, qty_oke')->get_where('warehouse_adjustment_detail', array('kode_trans' => $kode_trans))->result_array();
		$data = array(
			'detail'	=> $detail_mat
		);
		$this->load->view('Finish_good/detail_berat', $data);
	}

	public function server_side_cutting_deadstock()
	{
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_cutting_deadstock(
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

			$no_ipp 	= str_replace('BQ-', '', $row['id_bq']);
			$customer 	= get_name('production', 'nm_customer', 'no_ipp', $no_ipp);
			$project 	= get_name('production', 'project', 'no_ipp', $no_ipp);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>" . strtoupper($row['id_category']) . ", ".$row['length_split']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";
			$nestedData[]	= "<div align='left'>" . $customer . "</div>";
			$nestedData[]	= "<div align='left'>" . $project . "</div>";

			$nestedData[]	= "<div align='center'>
							<button type='button' target='_blank' class='btn btn-sm btn-default qr-cutting' data-id_cutting='cut-" . $row['id'] . "' style='margin-bottom:2px;padding:2px 6px'><i class='fa fa-qrcode fa-2x'></i></button></div>
							";
	
			$ck = ($row['flag_qr'] == 'Y') ? '<i class="fa fa-check text-success"></i>' : '-';
			$nestedData[] = "<div class='text-center'>" . $ck . "</div>";
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

	public function query_data_cutting_deadstock($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_so,
				b.no_spk
			FROM
				so_cutting_detail a
				LEFT JOIN data_erp_fg b ON a.id=b.id_trans AND b.jenis='in cutting deadstok',
				(SELECT @row:=0) r
		    WHERE 1=1 
                AND a.qc_date IS NOT NULL AND (a.kode_delivery IS NULL AND a.spool_induk IS NULL)
				AND (
					a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
					OR a.id_category LIKE '%" . $this->db->escape_like_str($like_value) . "%'
				)
			GROUP BY a.id
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_category',
			2 => 'id_category',
			3 => 'id_category'
		);

		$sql .= " ORDER BY a.id DESC, " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
}
