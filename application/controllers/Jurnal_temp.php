<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal_temp extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
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
			'title'			=> 'Material Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal material');
		$this->load->view('Jurnal_temp/index',$data);
	}

	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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

            $ket = '';
            if($row['keterangan'] == 'wip'){
                $EXPLODE = explode('-',$row['hub_product']);
                $det = $this->db->get_where('so_detail_header',array('id'=>$EXPLODE[0]))->result_array();
                $ket = ' - '.$det[0]['id_category'].' / SPEC('.spec_fd($EXPLODE[0], 'so_detail_header').') / '.$det[0]['no_spk'];
            }

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['category']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower('<b>'.$row['keterangan'].'</b>'.$ket))."</div>";
            $DEBIT = ($row['posisi'] == 'DEBIT')?number_format($row['amount']):'';
            $KREDIT = ($row['posisi'] == 'KREDIT')?number_format($row['amount']):'';
			$nestedData[]	= "<div align='right'>".$DEBIT."</div>";
			$nestedData[]	= "<div align='right'>".$KREDIT."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['updated_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['updated_date']))."</div>";
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
				jurnal_temp a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND(
				a.category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.id',
			2 => 'a.id',
			3 => 'a.id',
			4 => 'a.id',
			5 => 'a.id'
		);

		$sql .= " ORDER BY a.updated_date DESC, a.posisi ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//JURNAL MATERIAL
	public function material($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material/'.$id;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$gudang_awal = 'Gd. Pusat';
		$gudang_akhir = 'Subgudang';
		if($id == '2'){
			$gudang_awal = 'Subgudang';
			$gudang_akhir = 'Gd. Produksi';
		}
		if($id == '3'){
			$gudang_awal = 'Gd. Pusat';
			$gudang_akhir = 'Gd. Origa';
		}

		if($id == '4'){
			$gudang_awal = 'Retur Dari';
			$gudang_akhir = 'Retur Ke';
		}

		if($id == '5'){
			$gudang_awal = 'Material';
			$gudang_akhir = 'Finish Good';
		}

		$JUDUL = $gudang_awal.' - '.$gudang_akhir;
		if($id == '4'){
			$JUDUL = 'RETUR MATERIAL';
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'JURNAL ['.strtoupper($JUDUL).']',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'id'			=> $id,
			'gudang_awal'	=> $gudang_awal,
			'gudang_akhir'	=> $gudang_akhir,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal material');
		$this->load->view('Jurnal_temp/material',$data);
	}

	public function server_side_material(){
		$requestData	= $_REQUEST;
		$id   			= $requestData['id'];

		$fetch			= $this->query_server_side_material(
			$requestData['id'],
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id']."'>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['cost_book'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'] * -1,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'],2)."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
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

	public function query_server_side_material($id, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';
		if($id == '1'){
			$where = " AND a.category = 'transfer pusat - subgudang' ";
		}
		if($id == '2'){
			$where = " AND a.category = 'transfer subgudang - produksi' ";
		}
		if($id == '3'){
			$where = " AND a.category = 'gudang pusat - origa' ";
		}

		if($id == '4'){
			$where = " AND a.category = 'retur material' ";
		}

		if($id == '5'){
			$where = " AND a.category = 'material to FG' ";
		}

		$where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NULL ".$where."  ".$where_by." AND a.qty > 0
			AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_material_close(){
		$requestData	= $_REQUEST;
		$id   			= $requestData['id'];
		$fetch			= $this->query_server_side_material_close(
			$requestData['id'],
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['qty'],4)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['cost_book'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'] * -1,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'],2)."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";

			if($id==1 and $row['status_jurnal'] != 1){
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='Input Jurnal Pindah gudang' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-pencil'></i>
				</a>
					  </div>";
			} else if($id==2 and $row['status_jurnal'] != 1){
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-success btn-sm view3' href='javascript:void(0)' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-pencil'></i>
				</a>
					  </div>";
			} else if($id==3 and $row['status_jurnal'] != 1){
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-warning btn-sm view4' href='javascript:void(0)' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-pencil'></i>
				</a>
					  </div>";
			} else if($id==4 and $row['status_jurnal'] != 1){
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-warning btn-sm view5' href='javascript:void(0)' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-pencil'></i>
				</a>
					  </div>";
			} else if($id==5 and $row['status_jurnal'] != 1){
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-warning btn-sm view5' href='javascript:void(0)' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-pencil'></i>
				</a>
					  </div>";
			}
			else {
			$nestedData[] =
					  "<div align='center' data-id_material='" . $row['id'] . "'>
					  <i class='btn btn-success btn-sm '>Sudah Dijurnal</i>
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

	public function query_server_side_material_close($id, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';
		if($id == '1'){
			$where = " AND a.category = 'transfer pusat - subgudang' ";
		}
		if($id == '2'){
			$where = " AND a.category = 'transfer subgudang - produksi' ";
		}
		if($id == '3'){
			$where = " AND a.category = 'gudang pusat - origa' ";
		}
		if($id == '4'){
			$where = " AND a.category = 'retur material' ";
		}
		if($id == '5'){
			$where = " AND a.category = 'material to FG' ";
		}

		$where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NOT NULL ".$where."  ".$where_by." AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$gudang = $data['gudang'];
		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('id',$check);
			$this->db->update('jurnal',$ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'gudang'	=> $gudang
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'gudang'	=> $gudang
			);
			history('Closing jurnal : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	//JURNAL PRODUCT
	public function product($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/product/'.$id;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$gudang_awal = 'WIP';
		$gudang_akhir = 'Finish Good';
		if($id == '2'){
			$gudang_awal = 'Finish Good';
			$gudang_akhir = 'Transit';
		}
		if($id == '3'){
			$gudang_awal = 'Transit';
			$gudang_akhir = 'Customer';
		}
		if($id == '4'){
			$gudang_awal = 'Finish Good';
			$gudang_akhir = 'Cutting WIP';
		}
		if($id == '5'){
			$gudang_awal = 'Cutting WIP';
			$gudang_akhir = 'Finish Good';
		}
		if($id == '6'){
			$gudang_awal = 'Finish Good';
			$gudang_akhir = 'Spooling WIP';
		}
		if($id == '7'){
			$gudang_awal = 'Spooling WIP';
			$gudang_akhir = 'Finish Good';
		}

		$JUDUL = $gudang_awal.' - '.$gudang_akhir;

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'JURNAL ['.strtoupper($JUDUL).']',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'id'			=> $id,
			'gudang_awal'	=> $gudang_awal,
			'gudang_akhir'	=> $gudang_akhir,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal product');
		$this->load->view('Jurnal_temp/product',$data);
	}

	public function server_side_product(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_product(
			$requestData['id'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$id = $requestData['id'];
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

			$check = "<input type='checkbox' name='check[$nomor]' id='check_1_[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id']."'>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$row['id']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_so'])."</div>";
			if($id == '6' OR $id == '7'){
				$nestedData[]	= "<div align='left'>".strtoupper($row['product_spool'])."</div>";
			}
			else{
				$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			}
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			if($id == '6' OR $id == '7'){
				$nestedData[]	= "<div align='right'>".number_format($row['nilaiSum'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['nilaiSum'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['nilaiSum'] * -1)."</div>";
			}
			else{
				$nestedData[]	= "<div align='right'>".number_format($row['cost_book'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'] * -1)."</div>";
			}
			// $nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
			/*
			if($id == '1'){
			$nestedData[] =
				  "<div align='center'>
				  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal WIP - FINISH GOOD' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
			}
			if($id == '2'){
				$nestedData[] =
				  "<div align='center'>
				  <a class='btn btn-warning btn-sm view3' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
			}
			if($id == '3'){
				$nestedData[] =
				  "<div align='center'>
				  <a class='btn btn-warning btn-sm view4' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
			}
			*/


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

	public function query_server_side_product($id, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$groupBy = '';
		$fieldSUM = '';
		if($id == '1'){
			$where = " AND a.category = 'quality control' ";
		}
		if($id == '2'){
			$where = " AND a.category = 'delivery' ";
		}
		if($id == '3'){
			$where = " AND a.category = 'diterima customer' ";
		}
		if($id == '4'){
			$where = " AND a.category = 'cutting loose' ";
		}
		if($id == '5'){
			$where = " AND a.category = 'quality control cutting' ";
		}
		if($id == '6'){
			$where = " AND a.category = 'wip spooling' ";
			$groupBy = 'GROUP BY a.kode_trans';
			$fieldSUM = 'SUM(a.total_nilai) AS nilaiSum, GROUP_CONCAT(a.product SEPARATOR "<br>") AS product_spool,';
		}
		if($id == '7'){
			$where = " AND a.category = 'quality control spooling' ";
			$groupBy = 'GROUP BY a.kode_trans';
			$fieldSUM = 'SUM(a.total_nilai) AS nilaiSum, GROUP_CONCAT(a.product SEPARATOR "<br>") AS product_spool,';
		}

		$where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				".$fieldSUM."
				a.*
			FROM
				jurnal_product a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND approval_date IS NULL ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		".$groupBy;
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_product_close(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_product_close(
			$requestData['id'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$id = $requestData['id'];
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
			$check = "";
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_so'])."</div>";
			if($id == '6' OR $id == '7'){
				$nestedData[]	= "<div align='left'>".strtoupper($row['product_spool'])."</div>";
			}
			else{
				$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			}
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			if($id == '6' OR $id == '7'){
				$nestedData[]	= "<div align='right'>".number_format($row['nilaiSum'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['nilaiSum'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['nilaiSum'] * -1)."</div>";
			}
			else{
				$nestedData[]	= "<div align='right'>".number_format($row['cost_book'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
				$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'] * -1)."</div>";
			}
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_surat_jalan'])."</div>";
			if($row['status_jurnal']==0){
				if($id == '1'){
				  $nestedData[] =
				  "<div align='center' id='row_".$row['id']."'>
				  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal WIP - FINISH GOOD' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  <a class='btn btn-danger btn-sm view21' href='javascript:void(0)' title='Post Jurnal WIP - FINISH GOOD' data-id_material='" . $row['id'] . "' ><i class='fa fa-check-square'></i>
				  </a>
				  <input type='checkbox' name='check_2[$nomor]' id='check_2_[$nomor]' class='chk_personal_2' data-nomor='".$nomor."' value='".$row['id']."'>
				  </div>
				  ";
				}
				if($id == '2'){
				  $nestedData[] =
				  "<div align='center'>
				  <a class='btn btn-warning btn-sm view3' href='javascript:void(0)' title='View Jurnal FINISH GOOD - TRANSIT' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
				}
				if($id == '3'){
				  $nestedData[] =
				  "<div align='center'>
				  <a class='btn btn-warning btn-sm view4' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
				}
				if($id == '4'){
					$nestedData[] = "<div align='center'>
				  <a class='btn btn-warning btn-sm viewfc' href='javascript:void(0)' title='View Jurnal FINISH GOOD - WIP CUTTING' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
				  }
				if($id == '5'){
					$nestedData[] = "<div align='center'>
				  <a class='btn btn-warning btn-sm viewcf' href='javascript:void(0)' title='View Jurnal WIP CUTTING - FINISH GOOD' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
				  }
				if($id == '6'){
					$nestedData[] = "<div align='center'>
				  <a class='btn btn-warning btn-sm viewfs' href='javascript:void(0)' title='View Jurnal FINISH GOOD - WIP SPOOLING' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
				  }
				if($id == '7'){
					$nestedData[] = "<div align='center'>
				  <a class='btn btn-warning btn-sm viewsf' href='javascript:void(0)' title='View Jurnal WIP SPOOLING - FINISH GOOD' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
				  </a>
				  </div>";
				  }
			}else{
				if($id == '1'){
					$nestedData[] =
					  "<div align='center'>
						 <a class='btn btn-default btn-sm view25' href='javascript:void(0)' title='View Jurnal' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['no_so'] . "' data-nm_vendor='". $row['product'] . "' ><i class='fa fa-eye'></i>
						</a>
					</div>";
				}else{
					$nestedData[] =
					  "<div align='center'>
					  <i class='btn btn-success btn-sm '>Sudah Dijurnal</i>
					  </div>";					
				}
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

	public function query_server_side_product_close($id, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$groupBy = '';
		$fieldSUM = '';
		if($id == '1'){
			$where = " AND a.category = 'quality control' ";
		}
		if($id == '2'){
			$where = " AND a.category = 'delivery' ";
		}
		if($id == '3'){
			$where = " AND a.category = 'diterima customer' ";
		}
		if($id == '4'){
			$where = " AND a.category = 'cutting loose' ";
		}
		if($id == '5'){
			$where = " AND a.category = 'quality control cutting' ";
		}
		if($id == '6'){
			$where = " AND a.category = 'wip spooling' ";
			$groupBy = 'GROUP BY a.kode_trans';
			$fieldSUM = 'SUM(a.total_nilai) AS nilaiSum, GROUP_CONCAT(a.product SEPARATOR "<br>") AS product_spool,';
		}
		if($id == '7'){
			$where = " AND a.category = 'quality control spooling' ";
			$groupBy = 'GROUP BY a.kode_trans';
			$fieldSUM = 'SUM(a.total_nilai) AS nilaiSum, GROUP_CONCAT(a.product SEPARATOR "<br>") AS product_spool,';
		}

		$where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				".$fieldSUM."
				a.*
			FROM
				jurnal_product a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND approval_date IS NOT NULL ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				or a.no_surat_jalan like '%".$this->db->escape_like_str($like_value)."%'
	        )
		".$groupBy;
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_product(){
		$data 			= $this->input->post();

		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$gudang = $data['gudang'];
		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('id',$check);
			$this->db->update('jurnal_product',$ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0,
				'gudang'	=> $gudang
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1,
				'gudang'	=> $gudang
			);
			history('Closing jurnal product : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	//JURNAL MATERIAL GROUP
	public function material_wip(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material_wip';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$gudang_awal = 'Gd. Produksi';
		$gudang_akhir = 'WIP';

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'JURNAL [GD. PRODUKSI - WIP]',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'gudang_awal'	=> $gudang_awal,
			'gudang_akhir'	=> $gudang_akhir,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal material wip');
		$this->load->view('Jurnal_temp/material_wip',$data);
	}

	public function server_side_material_wip(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_material_wip(
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id_milik']."-".$row['id_spk']."-".$row['no_ipp']."'>";

			// $list_material		= $this->db->query("SELECT nm_material, qty, cost_book, total_nilai FROM jurnal WHERE id_milik='".$row['id_milik']."' AND id_spk='".$row['id_spk']."'")->result_array();

			// $arr_mat = array();
			// $arr_qty = array();
			// $arr_price = array();
			// $arr_total = array();
			// $arr_all = array();
			// foreach($list_material AS $val => $valx){ $val++;
			// 	$arr_all[$val] = "<b>".$val.'. '.$valx['nm_material']."</b><span class='text-blue text-bold'><br>Qty Berat: ".number_format($valx['qty'],4)." kg</span><span class='text-green text-bold'><br>Cost Book: ".number_format($valx['cost_book'],2)."</span><span class='text-red text-bold'><br>Total Nilai: ".number_format($valx['total_nilai'],2)."</span>" ;
			// }
			// $dt_mat	= implode("<br>", $arr_mat);
			// $dt_qty	= implode("<br>", $arr_qty);
			// $dt_price	= implode("<br>", $arr_price);
			// $dt_total	= implode("<br>", $arr_total);
			// $dt_all	= implode("<br>", $arr_all);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal_max']))."</div>";
			$nestedData[]	= "<div align='left'></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_sum'])."</div>";
			// $nestedData[]	= "<div align='left'>".$dt_all."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-id_milik='".$row['id_milik']."' data-id_spk='".$row['id_spk']."'>DETAIL MATERIAL</span></div>";
			// $nestedData[]	= "<div align='left'>".$dt_mat."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_price."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_total."</div>";
			//			$nestedData[]	= "<div align='right'>".number_format($row['total_sum'] * -1)."</div>";
			//			$nestedData[]	= "<div align='right'>".number_format($row['total_sum'])."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_sum']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-eye'></i>
				</a>
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

	public function query_server_side_material_wip($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		$where = " AND a.category = 'laporan produksi' ";
		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				a.tanggal AS tanggal_max,
				SUM(a.total_nilai) AS total_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND a.status_id = '1' AND a.approval_date IS NULL ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.id_milik, a.id_spk,a.tanggal
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY (a.tanggal) DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_material_close_wip(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_material_close_wip(
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

			// $list_material		= $this->db->query("SELECT nm_material, qty, cost_book, total_nilai FROM jurnal WHERE id_milik='".$row['id_milik']."' AND id_spk='".$row['id_spk']."'")->result_array();

			// $arr_mat = array();
			// $arr_qty = array();
			// $arr_price = array();
			// $arr_total = array();
			// $arr_all = array();
			// foreach($list_material AS $val => $valx){ $val++;
			// 	// $arr_mat[$val] = $valx['nm_material'];
			// 	// $arr_pr[$val] =  number_format($valx['qty'],4);
			// 	// $arr_qty[$val] = number_format($valx['cost_book'],2);
			// 	// $arr_total[$val] = number_format($valx['total_nilai'],2);
			// 	$arr_all[$val] = "<b>".$val.'. '.$valx['nm_material']."</b><span class='text-blue text-bold'><br>Qty Berat: ".number_format($valx['qty'],4)." kg</span><span class='text-green text-bold'><br>Cost Book: ".number_format($valx['cost_book'],2)."</span><span class='text-red text-bold'><br>Total Nilai: ".number_format($valx['total_nilai'],2)."</span>" ;
			// }
			// $dt_mat	= implode("<br>", $arr_mat);
			// $dt_qty	= implode("<br>", $arr_qty);
			// $dt_price	= implode("<br>", $arr_price);
			// $dt_total	= implode("<br>", $arr_total);
			// $dt_all	= implode("<br>", $arr_all);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal_max']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_sum'])."</div>";
			// $nestedData[]	= "<div align='left'>".$dt_all."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-id_milik='".$row['id_milik']."' data-id_spk='".$row['id_spk']."'>DETAIL MATERIAL</span></div>";
			// $nestedData[]	= "<div align='left'>".$dt_mat."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_qty."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_price."</div>";
			// $nestedData[]	= "<div align='right'>".$dt_total."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['total_sum'] * -1)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['total_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			$nestedData[]	= "<div align='right'>".$row['kd_jurnal']."</div>";
			IF($row['status_jurnal'] == '0'){
			$nestedData[] =
					"<div align='center' id='row_".$nomor."'>
					  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_milik='" . $row['id_milik'] . "' data-total_sum='".$row['total_sum']."' data-id_spk='". $row['id_spk'] . "' data-tanggal='". $row['tanggal'] . "' ><i class='fa fa-eye'></i>
					  </a>
					  <input type='checkbox' name='check_2[$nomor]' id='check_2_[$nomor]' class='chk_personal_2' data-nomor='".$nomor."' value='".$nomor."' data-id_milik='" . $row['id_milik'] . "' data-total_sum='".$row['total_sum']."' data-id_spk='". $row['id_spk'] . "' data-tanggal='". $row['tanggal'] . "'>
					</div>";
			}else{
				$nestedData[]="
				<div align='center'>
				  <a class='btn btn-success btn-sm view9' href='javascript:void(0)' title='View Jurnal WIPx' data-id_milik='" . $row['id_milik'] . "' data-total_sum='".$row['total_sum']."' data-id_spk='". $row['id_spk'] . "' data-tanggal='". $row['tanggal'] . "' >Sudah Dijurnal
				  </a>
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

	public function query_server_side_material_close_wip($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		$where = " AND a.category = 'laporan produksi' ";
		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				(a.tanggal) AS tanggal_max,
				SUM(a.total_nilai) AS total_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NOT NULL ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.id_milik, a.id_spk,a.tanggal
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY MAX(a.tanggal) DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_wip(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];
		$ArrayTemp = [];
		$nomor = 0;
		$this->db->trans_start();
		$nmrjrnl = $this->db->query("SELECT max(id) as kodejurnal FROM jurnal_wip")->row();
		$kodejurnal = $nmrjrnl->kodejurnal+1;
		foreach ($check as $keyX => $valueX) {
			$total =0;
			$EXPLODE = explode('-',$valueX);
			$id_milik 	= $EXPLODE[0];
			$id_spk 	= $EXPLODE[1];
			$no_ipp 	= $EXPLODE[2];
			//			$getData = $this->db->get_where('jurnal',array('category'=>'laporan produksi','no_ipp'=>$no_ipp,'id_spk'=>$id_spk,'id_milik'=>$id_milik))->result_array();
			$getData = $this->db->query("SELECT a.*,b.coa FROM jurnal a join product_parent b on a.product=b.product_parent WHERE a.id_milik='$id_milik' AND a.id_spk='$id_spk' AND a.no_ipp = '$no_ipp' AND a.category = 'laporan produksi'")->result_array();
			foreach ($getData as $key => $value) { $nomor++;
			    $total  = ($total+$value['total_nilai']);
				$ArrayTemp[$nomor]['id'] = $value['id'];
				$ArrayTemp[$nomor]['approval_by'] = $username;
				$ArrayTemp[$nomor]['approval_date'] = $datetime;
				$ArrayTemp[$nomor]['kd_jurnal'] = $kodejurnal;
				$no_so=$value['no_so'];
				$category=$value['category'];
				$gudang_dari=$value['gudang_dari'];
				$gudang_ke=$value['gudang_ke'];
				$no_ipp=$value['no_ipp'];
				$no_so=$value['no_so'];
				$product=$value['product'];
				$id_milik=$value['id_milik'];
				$id_spk=$value['id_spk'];
				$coa=$value['coa'];
			}
			$det_Jurnaltes2= array(
				  'kd_jurnal'	=> $kodejurnal,
				  'tanggal'		=> date('Y-m-d'),
				  'total_nilai'	=> $total,
				  'category'	=> $category,
				  'gudang_dari'	=> $gudang_dari,
				  'gudang_ke'	=> $gudang_ke,
				  'no_ipp'		=> $no_ipp,
				  'no_so'		=> $no_so,
				  'product'		=> $product,
				  'id_milik'	=> $id_milik,
				  'id_spk'		=> $id_spk,
				  'coa'			=> $coa,
				  'created_by'	=> $username,
				  'created_date'=> $datetime,
			);
			$this->db->insert('jurnal_wip',$det_Jurnaltes2);
			$kodejurnal = ($kodejurnal+1);
		}
		if(!empty($ArrayTemp)){
			$this->db->update_batch('jurnal',$ArrayTemp,'id');
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	public function modal_detail_wip($id_milik=null,$id_spk=null){

		$list_material		= $this->db->query("SELECT nm_material, qty, cost_book, total_nilai FROM jurnal WHERE id_milik='$id_milik' AND id_spk='$id_spk' AND status_id = '1' AND category = 'laporan produksi'")->result_array();

		$data = array(
			'result' 	=> $list_material

		);
		$this->load->view('Jurnal_temp/modal_detail_wip', $data);
	}

	public function modal_detail_outgoing_stock($kode_trans=null){

		$list_material		= $this->db->query("SELECT nm_material, qty, cost_book, total_nilai FROM jurnal WHERE kode_trans='$kode_trans' AND status_id = '1'")->result_array();

		$data = array(
			'result' 	=> $list_material

		);
		$this->load->view('Jurnal_temp/modal_detail_outgoing_stock', $data);
	}

	//JURNAL ASSETS
	public function assets(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/assets';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'JURNAL ASSETS',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal assets');
		$this->load->view('Jurnal_temp/assets',$data);
	}

	public function server_side_assets(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_assets(
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id']."'>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(get_name('users','nm_lengkap','username',$row['created_by']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
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

	public function query_server_side_assets($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NULL AND a.category='assets' ".$where."  ".$where_by."
			AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_assets_close(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_assets_close(
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_material'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";

			IF($row['status_jurnal'] == 0){
			$nestedData[] =
					"<div align='center'>
					  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal Assets' data-id_material='" . $row['id_detail'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['tanggal'] . "' data-nm_vendor='". $row['id'] . "' ><i class='fa fa-eye'></i>
					  </a>
					</div>";
			}else{
				$nestedData[]="<i class='btn btn-success btn-sm '>Sudah Dijurnal</i>";
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

	public function query_server_side_assets_close($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NOT NULL AND a.category='assets' ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_assets(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('id',$check);
			$this->db->update('jurnal',$ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal assets : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	//JURNAL STOCK
	public function stok($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stok';
		// echo $controller;
		// exit;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Outgoing Stock Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'id'			=> $id,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal stok');
		$this->load->view('Jurnal_temp/stok',$data);
	}

	public function server_side_stok(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_stok(
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['kode_trans']."'>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('costcenter','nm_costcenter','id',$row['gudang_ke']))."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
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

	public function query_server_side_stok($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NULL AND a.category='outgoing stok' ".$where."  ".$where_by."
			AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_stok_close(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_stok_close(
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('costcenter','nm_costcenter','id',$row['gudang_ke']))."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			if($row['status_jurnal'] ==0){				
				$nestedData[] =
				  "<div align='center' id='row_".$nomor."'>
					<a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='Jurnal Outgoing Stock' data-id_material='" . $row['kode_trans'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['tanggal'] . "' data-nm_vendor='". $row['kode_trans'] . "' ><i class='fa fa-pencil'></i>
					</a>".($row['total_nilai_sum']>0?"
					<input type='checkbox' name='check_2[$nomor]' id='check_2_[$nomor]' value='".$nomor."' class='chk_personal_2' data-id_material='" . $row['kode_trans'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['tanggal'] . "' data-nm_vendor='". $row['kode_trans'] . "'>":"")."
				  </div>";
			} else {
				$nestedData[] =
				  "<div align='center'><a class='btn btn-warning btn-sm viewonly' href='javascript:void(0)' title='Jurnal Outgoing Stock' data-id='" . $row['kode_trans'] . "' >
				  <i class='btn btn-success btn-sm '>Sudah Dijurnal</i></a>
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

	public function query_server_side_stok_close($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NOT NULL AND a.category='outgoing stok' ".$where."  ".$where_by." AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%' or
				a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_stok(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('kode_trans',$check);
			$this->db->update('jurnal',$ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal stok : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	//JURNAL STOCK
	public function stok_incoming($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/stok_incoming';
		// echo $controller;
		// exit;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Incoming Stock Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'id'			=> $id,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal stok');
		$this->load->view('Jurnal_temp/stok_incoming',$data);
	}

	public function server_side_stok_incoming(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_stok_incoming(
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['kode_trans']."'>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('costcenter','nm_costcenter','id',$row['gudang_ke']))."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
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

	public function query_server_side_stok_incoming($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				b.no_ipp AS no_po,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a
				LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans,
                (SELECT @row:=0) r
		    WHERE  1=1 AND a.status_id = '1' AND a.approval_date IS NULL AND a.category='incoming stok' ".$where."  ".$where_by."
			AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				or b.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_stok_incoming_close(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_stok_incoming_close(
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			$nestedData[]	= "<div align='left'>".strtoupper(get_name('costcenter','nm_costcenter','id',$row['gudang_ke']))."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			if($row['status_jurnal'] ==0){
				$nestedData[] =
					  "<div align='center'>
						<a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='Jurnal Incoming Stock' data-id_material='" . $row['kode_trans'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['tanggal'] . "' data-nm_vendor='". $row['kode_trans'] . "' ><i class='fa fa-pencil'></i>
						</a>
					  </div>";
			} else {
				$nestedData[] =
					  "<div align='center'>
					  <i class='btn btn-success btn-sm '>Sudah Dijurnal</i>
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

	public function query_server_side_stok_incoming_close($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				b.no_ipp AS no_po,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a
				LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans,
                (SELECT @row:=0) r
		    WHERE  1=1 AND a.status_id = '1' AND a.approval_date IS NOT NULL AND a.category='incoming stok' ".$where."  ".$where_by." AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				or a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				or b.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_stok_incoming(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('kode_trans',$check);
			$this->db->update('jurnal',$ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal stok : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	//JURNAL STOCK
	public function department_incoming($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/department_incoming';
		// echo $controller;
		// exit;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Pembelian Non-Material & Jasa >> Jurnal Incoming Department',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'id'			=> $id,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal department');
		$this->load->view('Jurnal_temp/department_incoming',$data);
	}

	public function server_side_department_incoming(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_department_incoming(
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['kode_trans']."'>";

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			// $nestedData[]	= "<div align='left'>".strtoupper(get_name('costcenter','nm_costcenter','id',$row['gudang_ke']))."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
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

	public function query_server_side_department_incoming($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				a.no_ipp AS no_po,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NULL AND a.category='incoming department' ".$where."  ".$where_by."
			AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans,no_ipp
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_department_incoming_close(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_department_incoming_close(
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			if($row['status_jurnal'] != 1){
				$nestedData[] =
					  "<div align='center'>
						<a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='Jurnal Incoming Department' data-id_material='" . $row['kode_trans'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['tanggal'] . "' data-nm_vendor='". $row['kode_trans'] . "' data-no_po='".$row['no_po']."'><i class='fa fa-pencil'></i>
						</a>
					  </div>";
			} else {
			$nestedData[] =
					  "<div align='center'>
					  <i class='btn btn-success btn-sm '>Sudah Dijurnal</i>
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

	public function query_server_side_department_incoming_close($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				a.no_ipp AS no_po,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NOT NULL AND a.category='incoming department' ".$where."  ".$where_by." AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				or a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				or a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans,no_ipp
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_department_incoming(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('kode_trans',$check);
			$this->db->update('jurnal',$ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal department : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	//JURNAL INCOMING ASSET
	public function asset_incoming($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/asset_incoming';
		// echo $controller;
		// exit;
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Incoming Assets Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'id'			=> $id,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal asset');
		$this->load->view('Jurnal_temp/asset_incoming',$data);
	}

	public function server_side_asset_incoming(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_asset_incoming(
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

			$check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['kode_trans']."'>";
			
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			// $nestedData[]	= "<div align='left'>".strtoupper(get_name('costcenter','nm_costcenter','id',$row['gudang_ke']))."</div>";
			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
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

	public function query_server_side_asset_incoming($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				b.no_ipp AS no_po,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a
				LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans,
                (SELECT @row:=0) r
		    WHERE  1=1 AND a.status_id = '1' AND a.approval_date IS NULL AND a.category='incoming asset' ".$where."  ".$where_by."
			AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_asset_incoming_close(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_asset_incoming_close(
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
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_po'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'] * -1)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_nilai_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$row['kode_trans']."'>DETAIL BARANG</span></div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			if($row['status_jurnal'] != 1){
				$nestedData[] =
					  "<div align='center'>
						<a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='Jurnal Incoming Assets' data-id_material='" . $row['id_material'] . "' data-id_total='".$row['total_nilai']."' data-id_vendor='". $row['tanggal'] . "' data-nm_vendor='". $row['kode_trans'] . "' ><i class='fa fa-pencil'></i>
						</a>
					  </div>";
			} else {
			$nestedData[] =
					  "<div align='center'>
					  <i class='btn btn-success btn-sm '>Sudah Dijurnal</i>
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

	public function query_server_side_asset_incoming_close($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		// $where_by = "AND a.created_by != 'json'";

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				b.no_ipp AS no_po,
				SUM(total_nilai) AS total_nilai_sum
			FROM
				jurnal a
				LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans,
                (SELECT @row:=0) r
		    WHERE  1=1 AND a.status_id = '1' AND a.approval_date IS NOT NULL AND a.category='incoming asset' ".$where."  ".$where_by." 
			AND(
				a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.kode_trans
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_asset_incoming(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];

		$ArrUpdate = [
			'approval_by' => $username,
			'approval_date' => $datetime
		];
		// exit;
		$this->db->trans_start();
			$this->db->where_in('kode_trans',$check);
			$this->db->update('jurnal',$ArrUpdate);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal asset : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}
	// new agus
	//JURNAL MATERIAL FG
	public function material_fg(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/material_fg';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$gudang_awal = 'Material';
		$gudang_akhir = 'Finish Good';

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'JURNAL [MATERIAL - FINISH GOOD]',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'gudang_awal'	=> $gudang_awal,
			'gudang_akhir'	=> $gudang_akhir,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data jurnal material fg');
		$this->load->view('Jurnal_temp/material_fg',$data);
	}

	public function server_side_material_fg(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_material_fg(
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

			$check = '';//"<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='".$nomor."' value='".$row['id_milik']."-".$row['id_spk']."-".$row['no_ipp']."'>";


			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal_max']))."</div>";
			$nestedData[]	= "<div align='left'></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_sum'])."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-id_milik='".$row['id_milik']."' data-id_spk='".$row['id_spk']."'>DETAIL MATERIAL</span></div>";

			$nestedData[]	= "<div align='left'>".get_name('users','nm_lengkap','username',$row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'>".$check."</div>";
			$nestedData[] =
					  "<div align='center'>
					  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_material='" . $row['id'] . "' data-id_total='".$row['total_sum']."' data-id_vendor='". $row['cost_book'] . "' data-nm_vendor='". $row['cost_book'] . "' ><i class='fa fa-eye'></i>
				</a>
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

	public function query_server_side_material_fg($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		$where = " AND a.category = 'laporan produksi' ";
		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				MAX(a.tanggal) AS tanggal_max,
				SUM(a.total_nilai) AS total_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND a.status_id = '1' AND a.approval_date IS NULL ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.id_milik, a.id_spk
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY MAX(a.tanggal) DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_material_close_fg(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_server_side_material_close_wip(
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

			// $list_material		= $this->db->query("SELECT nm_material, qty, cost_book, total_nilai FROM jurnal WHERE id_milik='".$row['id_milik']."' AND id_spk='".$row['id_spk']."'")->result_array();

			// $arr_mat = array();
			// $arr_qty = array();
			// $arr_price = array();
			// $arr_total = array();
			// $arr_all = array();
			// foreach($list_material AS $val => $valx){ $val++;
			// 	// $arr_mat[$val] = $valx['nm_material'];
			// 	// $arr_pr[$val] =  number_format($valx['qty'],4);
			// 	// $arr_qty[$val] = number_format($valx['cost_book'],2);
			// 	// $arr_total[$val] = number_format($valx['total_nilai'],2);
			// 	$arr_all[$val] = "<b>".$val.'. '.$valx['nm_material']."</b><span class='text-blue text-bold'><br>Qty Berat: ".number_format($valx['qty'],4)." kg</span><span class='text-green text-bold'><br>Cost Book: ".number_format($valx['cost_book'],2)."</span><span class='text-red text-bold'><br>Total Nilai: ".number_format($valx['total_nilai'],2)."</span>" ;
			// }
			// $dt_mat	= implode("<br>", $arr_mat);
			// $dt_qty	= implode("<br>", $arr_qty);
			// $dt_price	= implode("<br>", $arr_price);
			// $dt_total	= implode("<br>", $arr_total);
			// $dt_all	= implode("<br>", $arr_all);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row['tanggal_max']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['kode_trans']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['spec'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_sum'])."</div>";
			// $nestedData[]	= "<div align='left'>".$dt_all."</div>";
			$nestedData[]	= "<div align='center'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-id_milik='".$row['id_milik']."' data-id_spk='".$row['id_spk']."'>DETAIL MATERIAL</span></div>";

			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s',strtotime($row['approval_date']))."</div>";
			$nestedData[]	= "<div align='right'>".$row['kd_jurnal']."</div>";
			IF($row['status_jurnal'] == 0){
			$nestedData[] =
					"<div align='center'>
					  <a class='btn btn-warning btn-sm view2' href='javascript:void(0)' title='View Jurnal Pindah gudang' data-id_milik='" . $row['id_milik'] . "' data-total_sum='".$row['total_sum']."' data-id_spk='". $row['id_spk'] . "' data-tanggal='". $row['tanggal'] . "' ><i class='fa fa-eye'></i>
					  </a>
					</div>";
			}else{
				$nestedData[]="
				<div align='center'>
				  <a class='btn btn-success btn-sm view9' href='javascript:void(0)' title='View Jurnal WIP' data-id_milik='" . $row['id_milik'] . "' data-total_sum='".$row['total_sum']."' data-id_spk='". $row['id_spk'] . "' data-tanggal='". $row['tanggal'] . "' >Sudah Dijurnal
				  </a>
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

	public function query_server_side_material_close_fg($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = '';
		$where_by = '';

		$where = " AND a.category = 'laporan produksi' ";
		// $where_by = "AND a.created_by != 'json'";
		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				MAX(a.tanggal) AS tanggal_max,
				SUM(a.total_nilai) AS total_sum
			FROM
				jurnal a,
                (SELECT @row:=0) r
		    WHERE  1=1 AND status_id = '1' AND approval_date IS NOT NULL ".$where."  ".$where_by." AND(
				a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY a.id_milik, a.id_spk
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor'
		);

		$sql .= " ORDER BY MAX(a.tanggal) DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_jurnal_material_fg(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$check 	= $data['check'];
		$ArrayTemp = [];
		$nomor = 0;
		$this->db->trans_start();
		$nmrjrnl = $this->db->query("SELECT max(id) as kodejurnal FROM jurnal_wip")->row();
		$kodejurnal = $nmrjrnl->kodejurnal+1;
		foreach ($check as $keyX => $valueX) {
			$total =0;
			$EXPLODE = explode('-',$valueX);
			$id_milik 	= $EXPLODE[0];
			$id_spk 	= $EXPLODE[1];
			$no_ipp 	= $EXPLODE[2];
			//			$getData = $this->db->get_where('jurnal',array('category'=>'laporan produksi','no_ipp'=>$no_ipp,'id_spk'=>$id_spk,'id_milik'=>$id_milik))->result_array();
			$getData = $this->db->query("SELECT a.*,b.coa FROM jurnal a join product_parent b on a.product=b.product_parent WHERE a.id_milik='$id_milik' AND a.id_spk='$id_spk' AND a.no_ipp = '$no_ipp' AND a.category = 'laporan produksi'")->result_array();
			foreach ($getData as $key => $value) { $nomor++;
			    $total  = ($total+$value['total_nilai']);
				$ArrayTemp[$nomor]['id'] = $value['id'];
				$ArrayTemp[$nomor]['approval_by'] = $username;
				$ArrayTemp[$nomor]['approval_date'] = $datetime;
				$ArrayTemp[$nomor]['kd_jurnal'] = $kodejurnal;
				$no_so=$value['no_so'];
				$category=$value['category'];
				$gudang_dari=$value['gudang_dari'];
				$gudang_ke=$value['gudang_ke'];
				$no_ipp=$value['no_ipp'];
				$no_so=$value['no_so'];
				$product=$value['product'];
				$id_milik=$value['id_milik'];
				$id_spk=$value['id_spk'];
				$coa=$value['coa'];
			}
			$det_Jurnaltes2= array(
				  'kd_jurnal'	=> $kodejurnal,
				  'tanggal'		=> date('Y-m-d'),
				  'total_nilai'	=> $total,
				  'category'	=> $category,
				  'gudang_dari'	=> $gudang_dari,
				  'gudang_ke'	=> $gudang_ke,
				  'no_ipp'		=> $no_ipp,
				  'no_so'		=> $no_so,
				  'product'		=> $product,
				  'id_milik'	=> $id_milik,
				  'id_spk'		=> $id_spk,
				  'coa'			=> $coa,
				  'created_by'	=> $username,
				  'created_date'=> $datetime,
			);
			$this->db->insert('jurnal_wip',$det_Jurnaltes2);
			$kodejurnal = ($kodejurnal+1);
		}
		if(!empty($ArrayTemp)){
			$this->db->update_batch('jurnal',$ArrayTemp,'id');
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
			history('Closing jurnal : '.json_encode($check));
		}
		echo json_encode($Arr_Data);
	}

	public function saved_jurnal_depresiasiX(){
		$username = 'sam';
		$datetime = date('Y-m-d H:i:s');
		
		$this->db->trans_start();

		
		$bulan=date("11");
		$tahun=date("2024");
		
		$DATE_NOW	= date('Y-m-d');
		$date    = '2024-11-25';

		$sqlHeader	= "select * from asset_jurnal_temp WHERE tanggal='".$date."'";
		$Q_Awal	= $this->db->query($sqlHeader)->result();

		//echo $sqlHeader."<hr>";

				
			$det_Jurnaltes1=array();
			$jenis_jurnal = 'DEPRESIASI';
			$nomor_jurnal = $jenis_jurnal . $tahun.$bulan . rand(100, 999);
			$payment_date= '2024-11-30';
			foreach($Q_Awal AS $val => $valx){
				

				$sqlinsert="insert into jurnaltras (nomor, tanggal, tipe, no_perkiraan, keterangan, no_request, debet, kredit, jenis_jurnal)
				VALUE 
				('".$nomor_jurnal."','".$payment_date."','JV','".$valx->no_perkiraan."','".$valx->keterangan."','-','".$valx->debet."','".$valx->kredit."','".$valx->tipe."')";
				$this->db->query($sqlinsert);

		//		echo $sqlinsert.'<hr>';

		   }

			$nocab	= 'A';
			$Cabang	= '101';
			$bulan_Proses	= date('Y',strtotime($payment_date));
			$Urut			= 1;
			$Pros_Cab		= $this->db->query("SELECT subcab,nomorJC FROM ".DBACC.".pastibisa_tb_cabang WHERE nocab='".$Cabang."' limit 1");
			$det_Cab		= $Pros_Cab->row();
			
			
			if($det_Cab){
				$nocab		= $det_Cab ->subcab;
				$Urut		= intval($det_Cab ->nomorJC) + 1;
			}
			$Format			= $Cabang.'-'.$nocab.'JV'.date('y',strtotime($payment_date));
			$Nomor_JV		= $Format.str_pad($Urut, 5, "0", STR_PAD_LEFT);
			$this->db->query("UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=(nomorJC + 1),lastupdate='".date("Y-m-d")."' WHERE nocab='".$Cabang."'");


			$Bln	= substr($payment_date,5,2);
			$Thn	= substr($payment_date,0,4);
			$Q_Detail = "select * from jurnaltras where jenis_jurnal='JV' and stspos='0' and nomor='".$nomor_jurnal."'";
			$DtJurnal = $this->db->query($Q_Detail)->result();
			
            $total = 0;
			foreach($DtJurnal AS $keys => $vals){
				$total += $vals->debet;
			
				$sqlinsert1="insert into ".DBACC.".jurnal (nomor, tipe, tanggal, no_reff, no_perkiraan, keterangan, debet, kredit )
				VALUE 
				('".$Nomor_JV."','JV','".$payment_date."','".$vals->no_request."','".$vals->no_perkiraan."','".$vals->keterangan."','".$vals->debet."','".$vals->kredit."')";
				$this->db->query($sqlinsert1);
			}

			$sqlinsert="insert into ".DBACC.".javh (nomor, tgl, jml, kdcab, jenis, keterangan, bulan, tahun, user_id, ho_valid )
			VALUE 
			('".$Nomor_JV."','".$payment_date."','".$total."','101','JV','Depresiasi ".$Bln." - ".$Thn."','".$Bln."','".$Thn."','sam','')";
			$this->db->query($sqlinsert);

		
		
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
		
	
	 }

}
