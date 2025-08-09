<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ppic extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

    //==================================================================================================================
    //===========================================SPK CUTTING============================================================
    //==================================================================================================================

    public function spk_cutting(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_cutting';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data_ipp = $this->db->group_by('id_bq')->get('so_cutting_header')->result_array();
        
		$data = array(
			'title'			=> 'SPK Cutting',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'data_ipp'		=> $data_ipp,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data spk cutting');
		$this->load->view('Ppic/spk_cutting',$data);
	}

    public function server_side_spk_cutting(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_cutting';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_spk_cutting(
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

			$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header'=>$row['id']))->result_array();
			
			$sum_split = 0;
			$cuttingx = [];
			foreach ($result_cutting as $key => $value) {
				$cuttingx[] = number_format($value['length_split']);
				$sum_split += $value['length_split'];
			}

			// $created_date = (!empty($result_cutting))?date('d-M-Y', strtotime($result_cutting[0]['created_date'])):'';
			$created_date = (!empty($row['cutting_date']))?date('d-M-Y', strtotime($row['cutting_date'])):'';

			$product_code = '';
			$no_spk = $row['no_spk'];
			$thickness = $row['thickness'];
			if($row['thickness'] < 999){
				$get_detail	= $this->db->get_where('production_detail', array('id_produksi'=>str_replace('BQ-','PRO-',$row['id_bq']), 'id_milik'=>$row['id_milik']))->result();
				if(!empty($get_detail[0]->product_code)){
					$EXP = explode('.',$get_detail[0]->product_code);
					$product_code = $EXP[0].'.'.$row['qty_ke'];
				}
			}
			else{
				$get_detail	= $this->db->get_where('deadstok', array('id'=>$row['id_deadstok']))->result();
				if(!empty($get_detail[0]->no_so)){
					$product_code = $get_detail[0]->no_so;
					$no_spk = $get_detail[0]->no_spk;
					$thickness = '';
				}
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper(str_replace('BQ-','',$row['id_bq']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper(strtolower($row['id_category']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter_1'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['length'])."</div>";
			$nestedData[]	= "<div align='center'>".$thickness."</div>";
			$nestedData[]	= "<div align='left'>".$product_code."</div>";
			$nestedData[]	= "<div align='center'>".$no_spk."</div>";
			$nestedData[]	= "<div align='left'>".implode("/",$cuttingx)."</div>";
			$nestedData[]	= "<div align='center'>".$created_date."</div>";
			$nestedData[]	= "<div align='center'>".$row['qty_ke']." / ".$row['qty']."</div>";
            $check = "<span class='text-green'><i class='fa fa-check'></i></span>";
            if($row['app'] == 'N'){
                $check = "<input type='checkbox' name='check[$nomor]' class='chk_personal' value='".$row['id']."' >";
            }
			if(empty($row['release_to_costing_date'])){
				$check = "";
			}
            $nestedData[]	= "<div align='center'>".$check."</div>";

            $cutting = "";
            $lock = "";
            $edit = "";
            $print = "";
            $view = "";

			if(!empty($row['sts_fg'])){
				if($row['app'] == 'N'){
					$cutting = "&nbsp;<a href='".base_url('ppic/cutting/'.$row['id'])."' class='btn btn-sm btn-success' title='Cutting'><i class='fa fa-scissors'></i></a>";
				}
				if($row['cutting'] == 'Y'){
					$view = "<a href='".base_url('ppic/cutting_view/'.$row['id'])."' class='btn btn-sm btn-default' title='View'><i class='fa fa-eye'></i></a>";
					if($row['app'] == 'N'){
						$lock = "&nbsp;<button type='button' class='btn btn-sm btn-info lock_split' data-id='".$row['id']."' title='Lock Cutting'><i class='fa fa-check'></i></button>";
					}
					if($row['app'] == 'Y'){
						$edit = "&nbsp;<button type='button' class='btn btn-sm btn-primary edit_spk' data-id='".$row['id']."' title='Edit SPK'><i class='fa fa-edit'></i></button>";
						$print = "&nbsp;<a href='".base_url('ppic/print_cutting/'.$row['id'])."' target='_blank' class='btn btn-sm btn-warning' title='Print SPK Cutting'><i class='fa fa-print'></i></a>";
					}
				}

				$nestedData[]	= "<div align='left'>".$view.$cutting.$lock.$edit.$print."</div>";
			}
			else{
				if($row['thickness'] < 999 AND $row['id_deadstok'] == null){
					$nestedData[]	= "<div align='left'><span class='badge bg-red'>Waiting Start QC</span></div>";
				}
				else{
					if($row['app'] == 'N'){
						$cutting = "&nbsp;<a href='".base_url('ppic/cutting/'.$row['id'])."' class='btn btn-sm btn-success' title='Cutting'><i class='fa fa-scissors'></i></a>";
					}
					if($row['cutting'] == 'Y'){
						$view = "<a href='".base_url('ppic/cutting_view/'.$row['id'])."' class='btn btn-sm btn-default' title='View'><i class='fa fa-eye'></i></a>";
						if($row['app'] == 'N'){
							$lock = "&nbsp;<button type='button' class='btn btn-sm btn-info lock_split' data-id='".$row['id']."' title='Lock Cutting'><i class='fa fa-check'></i></button>";
						}
						if($row['app'] == 'Y'){
							$edit = "&nbsp;<button type='button' class='btn btn-sm btn-primary edit_spk' data-id='".$row['id']."' title='Edit SPK'><i class='fa fa-edit'></i></button>";
							$print = "&nbsp;<a href='".base_url('ppic/print_cutting/'.$row['id'])."' target='_blank' class='btn btn-sm btn-warning' title='Print SPK Cutting'><i class='fa fa-print'></i></a>";
						}
					}
	
					$nestedData[]	= "<div align='left'>".$view.$cutting.$lock.$edit.$print."</div>";
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

	public function query_spk_cutting($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		if($no_ipp <> '0'){
			$where = " AND a.id_bq='".$no_ipp."' ";
		}
		$WHERE_NOT = str_replace('PRO-','BQ-',filter_not_in());
		$where2 = " AND a.id_bq NOT IN ".$WHERE_NOT." ";

		// AND (c.finish_good > 0 OR a.id_deadstok IS NOT NULL)

		$sql = "
			SELECT
                (@row:=@row+1) AS nomor,
				a.*,
				b.no_spk,
				c.release_to_costing_date,
				c.status AS sts_fg
			FROM
                so_cutting_header a
				LEFT JOIN so_detail_header b ON a.id_milik=b.id
				LEFT JOIN production_detail c ON a.id_milik=c.id_milik AND a.qty_ke=c.product_ke,
				(SELECT @row:=0) r
		    WHERE  1=1 ".$where." ".$where2." AND ((c.kode_delivery IS NULL AND c.sts_cutting = 'Y' AND c.release_to_costing_date IS NOT NULL) OR a.id_deadstok IS NOT NULL ) 
			AND(
				a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'id_category',
			3 => 'diameter_1',
			4 => 'length',
			5 => 'thickness'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function cutting_multiple(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_cutting';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('ppic'));
		}
        $data 		= $this->input->post();
        $check = $data['check'];
        $id_list = implode("-",$check);
        
		$Arr_Kembali	= array(
			'status' => 1,
			'id_list'	=> $id_list
		);

		echo json_encode($Arr_Kembali);
	}

    public function cutting($id=null){
        if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			$detail 		= $data['detail'];
			
			// print_r($data); exit;
			
			$ArrDetail = array();
			$ArrDetail2 = array();
            $Where_in = [];
			if(!empty($data['detail'])){
				foreach($detail AS $val => $valx){
					$cutting_ke = 0;
					$lengthFull = str_replace(',','',$valx['length']);
					foreach($valx['detail'] AS $val2 => $valx2){
						$length = str_replace(',','',$valx2['length']);
						if(!empty($length)){ 
							$cutting_ke++;

							$lengthFull -= $length;

							$ArrDetail[$val.$val2]['id_bq'] 			= $valx['id_bq'];
							$ArrDetail[$val.$val2]['id_header'] 		= $valx['id_header'];
							$ArrDetail[$val.$val2]['id_milik'] 			= $valx['id_milik'];
							$ArrDetail[$val.$val2]['id_category'] 		= $valx['id_category'];
							$ArrDetail[$val.$val2]['diameter_1'] 		= $valx['diameter'];
							$ArrDetail[$val.$val2]['length'] 			= $valx['length'];
							$ArrDetail[$val.$val2]['spool_drawing'] 	= $valx2['spool'];
							$ArrDetail[$val.$val2]['length_split'] 		= $length;
							$ArrDetail[$val.$val2]['cutting_ke'] 		= $cutting_ke;
							$ArrDetail[$val.$val2]['created_by'] 		= $data_session['ORI_User']['username'];
							$ArrDetail[$val.$val2]['created_date'] 		= $dateTime;

                            $Where_in[] = $valx['id_header'];
						}
					}
					if($lengthFull > 0){
						$ArrDetail[$val.$val2.'999']['id_bq'] 			= $valx['id_bq'];
						$ArrDetail[$val.$val2.'999']['id_header'] 		= $valx['id_header'];
						$ArrDetail[$val.$val2.'999']['id_milik'] 		= $valx['id_milik'];
						$ArrDetail[$val.$val2.'999']['id_category'] 	= $valx['id_category'];
						$ArrDetail[$val.$val2.'999']['diameter_1'] 		= $valx['diameter'];
						$ArrDetail[$val.$val2.'999']['length'] 			= $valx['length'];
						$ArrDetail[$val.$val2.'999']['spool_drawing'] 	= 'sisa cutting';
						$ArrDetail[$val.$val2.'999']['length_split'] 	= $lengthFull;
						$ArrDetail[$val.$val2.'999']['cutting_ke'] 		= $cutting_ke + 1;
						$ArrDetail[$val.$val2.'999']['created_by'] 		= $data_session['ORI_User']['username'];
						$ArrDetail[$val.$val2.'999']['created_date'] 	= $dateTime;
					}
				}
			}
			
            $ArrUpdate = [
                'cutting' => 'Y',
                'cutting_by' => $data_session['ORI_User']['username'],
                'cutting_date' => $dateTime
            ];
			
			// print_r($ArrDetail);
			// exit;
			
			$this->db->trans_start();
				$this->db->where_in('id_header',$Where_in);
				$this->db->delete('so_cutting_detail');
				
				if(!empty($ArrDetail)){
                    $this->db->where_in('id',$Where_in);
				    $this->db->update('so_cutting_header', $ArrUpdate);

					$this->db->insert_batch('so_cutting_detail',$ArrDetail);
				}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Success. Thanks ...',
					'status'	=> 1
				);
				history('Create spk cutting '.json_encode($Where_in));
			}

			echo json_encode($Arr_Kembali);
		}
		else{
            $controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_cutting';
            $Arr_Akses			= getAcccesmenu($controller);
            
            if($Arr_Akses['create'] !='1'){
                $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
                redirect(site_url('ppic'));
            }

            $data_Group	= $this->master_model->getArray('groups',array(),'id','name');

            $arrayList = explode("-", $id);

            $detail = $this->db
                                ->select('a.*, b.id_product')
                                ->from('so_cutting_header a')
                                ->join('so_detail_header b','a.id_milik=b.id', 'left')
                                ->where_in('a.id', $arrayList)
                                ->get()
                                ->result_array();

            // print_r($arrayList);
            
            $data = array(
                'title'			=> 'Cutting Pipe',
                'action'		=> 'index',
                'detail'		=> $detail,
                'row_group'		=> $data_Group,
                'akses_menu'	=> $Arr_Akses
            );
            $this->load->view('Ppic/cutting',$data);
        }
	}

	public function cutting_view($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_cutting';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('ppic'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');

		$arrayList = explode("-", $id);

		$detail = $this->db
							->select('a.*, b.id_product')
							->from('so_cutting_header a')
							->join('so_detail_header b','a.id_milik=b.id', 'left')
							->where_in('a.id', $arrayList)
							->get()
							->result_array();

		// print_r($arrayList);
		
		$data = array(
			'title'			=> 'Detail Cutting Pipe',
			'action'		=> 'index',
			'detail'		=> $detail,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Ppic/cutting_view',$data);
	}

	public function lock_split(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$ArrUpdate = array(
			'app' => 'Y',
			'app_by' => $data_session['ORI_User']['username'],
			'app_date' => $dateTime
		);

		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('so_cutting_header', $ArrUpdate);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Proccess data failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Proccess data success. Thanks ...',
				'status'	=> 1
			);
			history('Lock cutting : '.$id);
		}

		echo json_encode($Arr_Kembali);
	}

	public function edit_spk_cutting(){
		if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			$id 			= $data['id'];
			$mesin 			= $data['mesin'];
			$tanggal 		= date('Y-m-d', strtotime($data['tanggal']));
			$unit_ct 		= str_replace(',','',$data['unit_ct']);
			$unit_mp 		= str_replace(',','',$data['unit_mp']);
			$tt_ct 			= str_replace(',','',$data['tt_ct']);
			$tt_mp 			= str_replace(',','',$data['tt_mp']);
			$detail 		= json_encode($data['detail']);

			$ArrUpdate = [
				'mesin' => $mesin,
				'tanggal' => $tanggal,
				'unit_ct' => $unit_ct,
				'unit_mp' => $unit_mp,
				'tt_ct' => $tt_ct,
				'tt_mp' => $tt_mp,
				'tahapan' => $detail,
				'spk_by' => $data_session['ORI_User']['username'],
				'spk_date' => $dateTime
			];

			$this->db->trans_start();
				$this->db->where('id',$id);
				$this->db->update('so_cutting_header', $ArrUpdate);
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Process Success. Thanks ...',
					'status'	=> 1
				);
				history('Create set spk cutting '.$id);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$id		= $this->uri->segment(3);
			$result	= $this->db->get_where('so_cutting_header', array('id'=>$id))->result();
			$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header'=>$id))->result_array();

			$mesin	= $this->db->get_where('machine', array('sts_mesin'=>'Y'))->result_array();

			$sum_split = 0;
			foreach ($result_cutting as $key => $value) {
				$cutting[] = number_format($value['length_split']);
				$sum_split += $value['length_split'];
			}

			$data = array(
				'mesin' 	=> $mesin,
				'id' 		=> $id,
				'result' 	=> $result,
				'product'	=> $result[0]->id_category.' DN-'.$result[0]->diameter_1.'-'.$result[0]->thickness,
				'qty_order' => $result[0]->qty,
				'cutting' => implode("/",$cutting),
				'sum_split' => number_format($sum_split),
			);
			
			$this->load->view('Ppic/edit_spk_cutting', $data);
		}
	}

	public function print_cutting(){
		$id				= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result	= $this->db->get_where('so_cutting_header', array('id'=>$id))->result();
		$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header'=>$id))->result_array();
		
		$id_produksi = str_replace('BQ-','PRO-',$result[0]->id_bq);
		$id_milik = $result[0]->id_milik;
		$urut_nomor = $result[0]->qty_ke;

		$result_pro	= $this->db->get_where('production_detail', array('id_produksi'=>$id_produksi,'id_milik'=>$id_milik,'product_ke'=>$urut_nomor))->result();

		$sum_split = 0;
		foreach ($result_cutting as $key => $value) {
			$cutting[] = number_format($value['length_split']);
			$sum_split += $value['length_split'];
		}
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id' => $id,
			'result' => $result,
			'result_pro' => $result_pro,
			'result_cutting' => $result_cutting,
			'product'	=> $result[0]->id_category.' DN-'.$result[0]->diameter_1.'-'.$result[0]->thickness,
			'qty_order' => $result[0]->qty,
			'cutting' => implode("/",$cutting),
			'sum_split' => number_format($sum_split)
		);
		history('print cutting '.$id); 
		$this->load->view('Print/print_cutting', $data);
	}

	//==================================================================================================================
    //=============================================== SPOOL ============================================================
    //==================================================================================================================

	public function spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data spool');
		$this->load->view('Ppic/spool',$data);
	}

	public function server_side_spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
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


			$edit = "";
			$print = "";
			$release = "";
			$status = "";
			if(empty($row['lock_spool_date'])){
				$status = "<span class='badge bg-blue'>OPEN</span>";
				$edit = "<a href='".base_url('ppic/edit_spool/'.$row['spool_induk'])."' class='btn btn-sm btn-primary' title='Edit'><i class='fa fa-edit'></i></a>";
				$print = "<a href='".base_url('ppic/print_spool/'.$row['spool_induk'])."' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				$release = "<button type='button' class='btn btn-sm btn-success lock_spool' data-spool='".$row['spool_induk']."' title='Lock Spool'><i class='fa fa-check'></i></button>";
			}
			if(!empty($row['lock_spool_date'])){
				if(!empty($row['release_spool_date'])){
					$status = "<span class='badge bg-green'>CLOSE</span>";
				}
				else{
					$status = "<span class='badge bg-yellow'>WAITING QC</span>";
				}
			}
			$view = "<a href='".base_url('ppic/view_spool/'.$row['spool_induk'])."' class='btn btn-sm btn-warning' title='Detail'><i class='fa fa-eye'></i></a>";


			$get_split_ipp = $this->db->select('id_produksi, id_milik, kode_spool, product_code, product_ke, cutting_ke, no_drawing, id_category AS nm_product, no_spk, COUNT(id) AS qty, sts, length, status_tanki, nm_tanki')->group_by('sts, id_milik, length')->order_by('id','asc')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
			$ArrNo_Spool = [];
			$ArrNo_IPP = [];
			$ArrNo_Drawing = [];
			$ArrNo_SPK = [];
			$ArrNo_SO = [];
			foreach ($get_split_ipp as $key => $value) { $key++;

				$no_spk 		= $value['no_spk'];
				$ArrNo_IPP[] 	= str_replace('PRO-','',$value['id_produksi']);
				$ArrNo_Spool[] 	= $value['kode_spool'];

                $ArrNo_Drawing[] = $value['no_drawing'];
				
				$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
				
				$IMPLODE = explode('-', $value['product_code']);

				$sts = $value['sts'];

				$product 	= strtoupper($value['nm_product']).', '. spec_bq2($value['id_milik']);
				if($sts == 'cut'){
					$product 	= strtoupper($value['nm_product']).', '. spec_bq2($value['id_milik']).', cut '.number_format($value['length']);
				}
				if($value['status_tanki'] == 'tanki'){
					$product 	= strtoupper($value['nm_tanki']);
				}

				$no = sprintf('%02s', $key);

				$ArrNo_SO[] = $IMPLODE[0];

				$ArrNo_SPK[] = $no.'. <span class="text-bold text-primary">['.$IMPLODE[0].'/'.$no_spk.']</span> <span class="text-bold text-success">'.strtoupper($sts).'</span><span class="text-bold"> ['.$value['qty'].' pcs]</span> '.$product;
			}
			// print_r($ArrGroup); exit;
			$explode_spo = implode('<br>',array_unique($ArrNo_Spool));
			$explode_ipp = implode('<br>',array_unique($ArrNo_IPP));
			$explode_drawing = implode('<br>',array_unique($ArrNo_Drawing));
			$explode_spk = implode('<br>',$ArrNo_SPK);
			$explode_so = implode('<br>',array_unique($ArrNo_SO));

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['spool_induk']."</div>";
			$nestedData[]	= "<div align='center'>".$explode_spo."</div>";
			$nestedData[]	= "<div align='left'>".$explode_drawing."</div>";
			$nestedData[]	= "<div align='center'>".$explode_ipp." <b><br>OR<br></b>".$explode_so."</div>";
			$nestedData[]	= "<div align='left'>".$explode_spk."</div>";
			$nestedData[]	= "<div align='center'>".$row['spool_by']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['spool_date']))."</div>";
			$nestedData[]	= "<div align='left'>".$status."</div>";
			$nestedData[]	= "<div align='left'>
									".$view."
									".$edit."
									".$print."
									".$release."
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

	public function query_data_spool($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		// if($no_ipp <> 0){
		// 	$where = " AND a.id_produksi='".$no_ipp."' ";
		// }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				spool_group_all a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." ".$where2."
				AND (
					a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
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

		$sql .= " ORDER BY a.spool_induk DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function add_spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data_list = $this->db->query(" SELECT
                                            a.id_produksi
                                        FROM
                                            production_detail a 
                                        WHERE
											a.release_to_costing_date IS NOT NULL
											AND a.kode_spool IS NULL
											AND a.release_spool_date IS NULL
                                        GROUP BY
                                            a.id_produksi 
                                        ORDER BY
                                            a.id_produksi ASC")->result_array();
		$data_spool = $this->db->query(" SELECT
											a.spool_induk
										FROM
											production_detail a 
										WHERE
											a.spool_induk IS NOT NULL
											AND a.release_spool_date IS NULL
										GROUP BY
											a.spool_induk 
										ORDER BY
											a.spool_induk ASC")->result_array();
		$data = array(
			'title'			=> 'Add Spool',
			'action'		=> 'index',
			'list_ipp'		=> $data_list,
			'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Ppic/add_spool',$data);
	}

	public function server_side_request(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
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

			$sisa_spk = $this->db->select('COUNT(id) AS sisa_spk')->get_where('production_detail',array('kode_spool'=>NULL,'release_to_costing_date <>'=>NULL,'id_milik'=>$row['id_milik'],'id_produksi'=>$row['id_produksi']))->result();

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".spec_bq2($row['id_milik'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green sisa_spk'>".$sisa_spk[0]->sisa_spk."</span></div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk_".$row['id_milik']."' class='form-control text-center qty_spk input-sm maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' style='width:100px;'><script>$('.maskMoney').maskMoney();</script>
									<input type='hidden' name='ipp_".$row['id_milik']."' value='".$row['no_ipp']."'>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$row['id_milik']."' ></div>";
			
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

	public function query_data_request($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		if($no_ipp <> '0'){
			$where = " AND a.id_produksi='".$no_ipp."' ";
		}
		//(SELECT COUNT(b.id) FROM production_detail b WHERE b.kode_spk IS NULL AND b.id_milik=a.id_milik AND b.id_produksi=a.id_produksi)
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id,
				REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
				a.id_produksi,
				a.id_milik,
				a.id_category AS product,
				a.id_product,
				a.qty,
				b.no_spk
			FROM
				production_detail a
				LEFT JOIN so_detail_header b ON a.id_milik = b.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = b.id_bq,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where."
				AND a.release_to_costing_date IS NOT NULL
				AND a.kode_spool IS NULL
				AND (
					a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
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

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_spool(){
		$data 		= $this->input->post();

		$check = $data['check'];
		$spool_induk = $data['spool_induk'];
		$kode_spool = $data['kode_spool'];
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		// echo $spool_induk.'<br>';
		//pengurutan kode
		if($spool_induk == '0'){
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(spool_induk) as maxP FROM max_spool_induk WHERE spool_induk LIKE 'SP-".$YM."%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$spool_induk	= "SP-".$YM.$urut2;
		}

		// echo $spool_induk.'<br><br>';

		$get_detail_produksi = $this->db
									->select('*')
									->from('so_detail_header')
									->where_in('id', $check)
									->get()
									->result_array();
		$ArrListProduksi = [];
		$InsertKode = [];
		foreach ($get_detail_produksi as $key => $value) {
			$id_qty = "spk_".$value['id'];
			$QTY 	= $data[$id_qty];
			if($QTY > 0){
				$no_ipp = $data["ipp_".$value['id']];
				$no_pro = "PRO-".$no_ipp;
				
				if($kode_spool == '0'){
					$nomor_so = get_nomor_so($no_ipp);
					$srcPlant		= "SELECT MAX(kode_spool) as maxP FROM production_detail WHERE spool_induk = '".$spool_induk."' AND kode_spool LIKE '".$nomor_so."-SP-%' ";
					$resultPlant	= $this->db->query($srcPlant)->result_array();
					$angkaUrut2		= $resultPlant[0]['maxP'];
					$urutan2		= (int)substr($angkaUrut2, 13, 2);
					$urutan2++;
					$urut2			= sprintf('%02s',$urutan2);
					$kode_spool 	= $nomor_so.'-SP-'.$urut2;
				}

				// echo $kode_spool.'<br>';
				$qUpdate 	= $this->db->query("UPDATE 
													production_detail
												SET 
													spool_induk='$spool_induk',
													kode_spool='$kode_spool',
													spool_by='$username',
													spool_date='$datetime'
												WHERE 
													id_milik='".$value['id']."'
													AND id_produksi= '".$no_pro."'
													AND release_to_costing_date IS NOT NULL
													AND kode_spool IS NULL
													AND spool_induk IS NULL
													AND lock_spool_date IS NULL
												ORDER BY 
													id ASC 
												LIMIT $QTY");
				// echo $qUpdate."<br>";
			}
		}
		// exit;
		$Arr_Kembali	= array(
			'status' => 1,
			'spool_induk'	=> $spool_induk
		);

		echo json_encode($Arr_Kembali);
	}

	public function getDetailSpool(){
		$spool_induk 		= $this->input->post('spool_induk');

		$restSup	= $this->db->select('kode_spool')->group_by('kode_spool')->get_where('spool_group',array('spool_induk'=>$spool_induk))->result_array();

		$option	= "<option value='0'>Kode Spool Baru</option>";
		if(!empty($restSup)){
			foreach($restSup AS $val => $valx){
				$option .= "<option value='".$valx['kode_spool']."'>".strtoupper($valx['kode_spool'])."</option>";
			}
		}
		else{
			$option	= "<option value='0'>List Empty</option>";
		}

		$ArrJson	= array(
			'option' => $option
		);
		echo json_encode($ArrJson);
	}

	public function edit_spool($kode_spool){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['update'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$result = $this->db->group_by('kode_spool')->order_by('kode_spool','asc')->get_where('spool_group', array('spool_induk'=>$kode_spool))->result_array();
		$no_drawing = get_name('spool_group','no_drawing','spool_induk', $kode_spool);
		$getNmTaki 	= $this->db->group_by('nm_tanki')->get_where('production_detail',array('spool_induk'=>$kode_spool,'nm_tanki <>'=>null))->result_array();
		$nm_tanki 	= (!empty($getNmTaki[0]['nm_tanki']))?$getNmTaki[0]['nm_tanki']:'';
        $data = array(
			'title'			=> 'Edit Spool',
			'action'		=> 'index',
			'tanki_model'		=> $this->tanki_model,
			'row_group'		=> $data_Group,
			'result'		=> $result,
			'spool_induk'		=> $kode_spool,
			'nm_tanki'		=> $nm_tanki,
			'no_drawing'		=> $no_drawing,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Ppic/edit_spool',$data);
	}

	public function view_spool($kode_spool){
		
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$result 	= $this->db->group_by('kode_spool')->order_by('kode_spool','asc')->get_where('spool_group_all', array('spool_induk'=>$kode_spool))->result_array();
		$getNmTaki 	= $this->db->group_by('nm_tanki')->get_where('production_detail',array('spool_induk'=>$kode_spool,'nm_tanki <>'=>null))->result_array();
		$nm_tanki 	= (!empty($getNmTaki[0]['nm_tanki']))?$getNmTaki[0]['nm_tanki']:'';
		$data = array(
			'title'			=> 'Detail Spool',
			'action'		=> 'index',
			'result'		=> $result,
			'tanki_model'		=> $this->tanki_model,
			'spool_induk'		=> $kode_spool,
			'nm_tanki'		=> $nm_tanki,
		);
		$this->load->view('Ppic/view_spool',$data);
	}

	public function delete_spool(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$check 		= $data['check'];
		
		$ArrUpdate = [];
		$ArrUpdate2 = [];
		$ArrUpdate3 = [];
		$ArrUpdate4 = [];
		$kode_id = [];
		$ArrReportFG = [];
		foreach ($check as $value) {
			$EXPLODE 	= explode('-',$value);
			$id_pro 	= $EXPLODE[0];
			$status 	= $EXPLODE[1];

			if($status == 'loose'){
				$ArrUpdate[$value]['id'] = $id_pro;
				$ArrUpdate[$value]['spool_induk'] = NULL;
				$ArrUpdate[$value]['kode_spool'] = NULL;

				$getFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro'=>$id_pro,'jenis'=>'in'))->result_array();
				$ID_FG = (!empty($getFG[0]['id']))?$getFG[0]['id']:null;
				if(!empty($ID_FG)){
					$ArrReportFG[] = $ID_FG;
				}
			}

			if($status == 'cut'){
				$ArrUpdate2[$value]['id'] = $id_pro;
				$ArrUpdate2[$value]['spool_induk'] = NULL;
				$ArrUpdate2[$value]['kode_spool'] = NULL;

				$getFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro'=>$id_cut,'jenis'=>'in cutting'))->result_array();
				$ID_FG = (!empty($getFG[0]['id']))?$getFG[0]['id']:null;
				if(!empty($ID_FG)){
					$ArrReportFG[] = $ID_FG;
				}
			}

			if($status == 'loose_deadstok'){
				$ArrUpdate3[$value]['id'] = $id_pro;
				$ArrUpdate3[$value]['spool_induk'] = NULL;
				$ArrUpdate3[$value]['kode_spool'] = NULL;

				$getFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro_det'=>$id_pro,'jenis'=>'in deadstok'))->result_array();
				$ID_FG = (!empty($getFG[0]['id']))?$getFG[0]['id']:null;
				if(!empty($ID_FG)){
					$ArrReportFG[] = $ID_FG;
				}
			}

			if($status == 'loose_deadstok_modif'){
				$ArrUpdate4[$value]['id'] = $id_pro;
				$ArrUpdate4[$value]['spool_induk'] = NULL;
				$ArrUpdate4[$value]['kode_spool'] = NULL;

				$getDeadstockModif = $this->db->get_where('deadstok_modif',array('id'=>$id_pro))->result_array();
				$idDeadstock = (!empty($getDeadstockModif[0]['id_deadstok']))?$getDeadstockModif[0]['id_deadstok']:null;
				$idKodeSPK = (!empty($getDeadstockModif[0]['kode_spk']))?$getDeadstockModif[0]['kode_spk']:null;

				$ArrayGetFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro_det'=>$idDeadstock,'kode_trans'=>$idKodeSPK,'jenis'=>'in deadstok modif'))->result_array();
				
				if(!empty($ArrayGetFG)){
					foreach ($ArrayGetFG as $keyFGDM => $valueFGDM) {
						$ArrReportFG[] = $valueFGDM['id'];
					}
				}
			}

			$kode_id = $value;
		}
		
		// print_r($ArrReportFG);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_detail',$ArrUpdate,'id');
			}
			if(!empty($ArrUpdate2)){
				$this->db->update_batch('so_cutting_detail',$ArrUpdate2,'id');
			}
			if(!empty($ArrUpdate3)){
				$this->db->update_batch('deadstok',$ArrUpdate3,'id');
			}
			if(!empty($ArrUpdate4)){
				$this->db->update_batch('deadstok_modif',$ArrUpdate4,'id');
			}

			if(!empty($ArrReportFG)){
				$this->RemovesaveSpoolERP($data['kd_spoolx'],$ArrReportFG);
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Delete sebagian spool '.json_encode($kode_id));
		}

		echo json_encode($Arr_Kembali);
	}

    public function update_no_drawing(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$no_drawing     = $data['no_drawing'];
		$kd_spool 		= $data['kd_spool'];
		$nm_tanki 		= $data['nm_tanki'];
		
		$this->db->trans_start();
			$this->db->where('spool_induk', $kd_spool);
			$this->db->update('production_detail', array('no_drawing'=>$no_drawing,'nm_tanki'=>$nm_tanki));

            $this->db->where('spool_induk', $kd_spool);
			$this->db->update('so_cutting_detail', array('no_drawing'=>$no_drawing));

			$this->db->where('spool_induk', $kd_spool);
			$this->db->update('deadstok', array('no_drawing'=>$no_drawing));
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Update no drawing : '.$kd_spool);
		}

		echo json_encode($Arr_Kembali);
	}

	public function release_spool(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$username 		= $this->session->userdata['ORI_User']['username'];
		$spool 			= $data['spool'];
		
		$ArrUpdate = [
			'lock_spool_by' => $username,
			'lock_spool_date' => $dateTime
		];

		$get_spool = $this->db->group_by('kode_spool')->get_where('spool_group',array('spool_induk'=>$spool))->result_array();
		$ARrInsert = [];
		foreach ($get_spool as $key => $value) {
			$ARrInsert[$key]['spool_induk'] = $value['spool_induk'];
			$ARrInsert[$key]['kode_spool'] = $value['kode_spool'];
		}
		// print_r($ArrUpdate);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ARrInsert)){
				$this->db->where('spool_induk',$spool);
				$this->db->delete('spool');

				$this->db->insert_batch('spool',$ARrInsert);
			}

			$this->db->where('spool_induk',$spool);
			$this->db->update('production_detail',$ArrUpdate);

			$this->db->where('spool_induk',$spool);
			$this->db->update('so_cutting_detail',$ArrUpdate);

			$this->db->where('spool_induk',$spool);
			$this->db->update('deadstok',$ArrUpdate);

			insert_jurnal_spool($spool);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1
			);
			history('Lock spool '.$spool);

			$this->SpoolToWIP_Report($spool);
		}

		echo json_encode($Arr_Kembali);
	}

	public function print_spool($spool_induk){
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$result = $this->db->group_by('kode_spool')->order_by('kode_spool','asc')->get_where('spool_group', array('spool_induk'=>$spool_induk))->result_array();
		$query = "	SELECT
						a.id_category,
						a.id_milik,
						a.kode_spool,
						b.nm_material,
						SUM( b.last_cost ) AS berat 
					FROM
						spool_group a
						LEFT JOIN so_estimasi_total b ON a.id_milik = b.id_milik 
						AND b.id_material != '0' 
					WHERE
						a.spool_induk = '".$spool_induk."' 
						AND a.id_category = 'shop joint' 
					GROUP BY
						b.id_material";
		// echo $query;
		// exit;
		$result_material = $this->db->query($query)->result_array();
		$data = array(
			'Nama_Beda' 	=> $Nama_Beda,
			'printby' 		=> $printby,
			'result'		=> $result,
			'result_material'		=> $result_material,
			'spool_induk'	=> $spool_induk,
			'tanki_model'	=> $this->tanki_model,
		);
		$this->load->view('Print/print_spool',$data);
	}



	//NEW SPOOl
	public function add_spool_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data_list = $this->db->query(" SELECT
                                            a.id_produksi,
											a.product_code
                                        FROM
                                            production_detail a 
                                        WHERE
											(a.release_to_costing_date IS NOT NULL OR a.id_category = 'shop joint')
											AND a.kode_spool IS NULL
											AND a.release_spool_date IS NULL
											$where2
                                        GROUP BY
                                            a.id_produksi 
                                        ORDER BY
                                            a.id_produksi ASC")->result_array();
		$data_spool = $this->db->query(" SELECT
											a.spool_induk
										FROM
											spool_group a 
										WHERE
											a.spool_induk IS NOT NULL
											AND a.lock_spool_date IS NULL
											$where2
										GROUP BY
											a.spool_induk 
										ORDER BY
											a.spool_induk ASC")->result_array();

		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$this->db->where('insert_by',$username);
		$this->db->delete('spool_temp');

		$data = array(
			'title'			=> 'Add Spool',
			'action'		=> 'index',
			'list_ipp'		=> $data_list,
			'data_spool'	=> $data_spool,
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Ppic/add_spool_new',$data);
	}

	public function server_side_request_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request_new(
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

		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$SpoolSelect 	= $this->checkSpoolSelected($username);

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

			$NOMOR_SO = explode('-',$row['product_code']);

            $customer = get_name('production','nm_customer','no_ipp', $row['no_ipp']);
            $project = get_name('production','project','no_ipp', $row['no_ipp']);
			
			$LENGTH = number_format($row['length']);
			$value_check = $row['id']."-H".$row['id_cutting']."-C".$row['id_split'];

			$selected = (!empty($SpoolSelect[$value_check]))?'checked':'';

			$CHECK = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$value_check."' $selected >";
			if(!empty($row['id_cutting'])){
				$LENGTH = "<span class='text-red'><b>Belum Dicutting</b></span>";
				$CHECK = "<span cla";
				if(!empty($row['id_split'])){
					$LENGTH = $row['length_split'];
					$CHECK = "<input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$value_check."' $selected >";
				}
			}

			$spec = spec_bq2($row['id_milik']);

			$product_name = $row['id_category'];
			if($row['sts_tanki'] == 'tanki'){
				$product_name = $row['id_product'];
				$customer = $this->tanki_model->get_ipp_detail($row['no_ipp'])['customer'];
				$project = $this->tanki_model->get_ipp_detail($row['no_ipp'])['nm_project'];
				$spec = $this->tanki_model->get_spec($row['id_milik']);
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('PRO-','',$row['id_produksi'])."</div>";
			$nestedData[]	= "<div align='center'>".$NOMOR_SO[0]."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($product_name)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($customer)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($project)."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='right'>".$LENGTH."</div>";
			
			$CUTTING_KE = (!empty($row['cutting_ke']))?'.'.$row['cutting_ke']:'';

			$FG_DATE = (!empty($row['fg_date']))?'<br><i>FG Date: '.date('d-M-Y H:i',strtotime($row['fg_date'])).'</i>':'';
							
			$IMPLODE = explode('.', $row['product_code']);
			$product_code = $IMPLODE[0].'.'.$row['product_ke'].$CUTTING_KE;
			$nestedData[]	= "<div align='left'>".strtoupper($row['spool_drawing'])."</div>";
			$nestedData[]	= "<div align='left'>".$product_code.$FG_DATE."</div>";
			$nestedData[]	= "<div align='center'>".$CHECK."</div>";
			
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

	public function query_data_request_new($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$IN_LIST = DirectFinishGood();
		$where = "";
		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		if($no_ipp <> '0'){
			$where = " AND a.id_produksi='".$no_ipp."' ";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
                b.spk1_cost,
                b.spk2_cost,
                b.no_ipp,
                c.diameter_1,
                c.diameter_2,
                c.thickness,
				c.length,
				b.qty AS tot_qty,
				d.id AS id_cutting,
				e.id AS id_split,
				e.length_split,
				e.cutting_ke,
				e.spool_drawing,
				a.product_code_cut AS sts_tanki
			FROM
				production_detail a
                LEFT JOIN production_spk b ON a.kode_spk = b.kode_spk AND a.id_milik=b.id_milik
                LEFT JOIN so_detail_header c ON a.id_milik = c.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = c.id_bq
                LEFT JOIN so_cutting_header d ON a.id_milik = d.id_milik AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = d.id_bq AND a.product_ke = d.qty_ke AND d.app='Y'
				LEFT JOIN so_cutting_detail e ON d.id = e.id_header,
				(SELECT @row:=0) r
		    WHERE 1=1 
                AND a.kode_spk IS NOT NULL
				AND a.kode_spool IS NULL
				AND e.kode_spool IS NULL
				".$where."
                ".$where2."
				AND ((a.release_to_costing_date IS NOT NULL AND a.fg_date IS NOT NULL) OR (a.id_category IN ".$IN_LIST." AND a.id > 112573) )
                
				AND (
					a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR e.spool_drawing LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_spool_new(){
		$data 			= $this->input->post();

		$data_session	= $this->session->userdata;
		$username 		= $data_session['ORI_User']['username'];
		$dataList 		= $this->db->get_where('spool_temp',['insert_by'=>$username])->result_array();
		$ArrayList = [];
		foreach ($dataList as $key => $value) {
			$ArrayList[$key] = $value['key_spool'];
		}

		// print_r($ArrayList);
		// print_r($data['check']);
		// exit;

		if(!empty($ArrayList)){
			$check 			= $ArrayList;
		}
		// if(!empty($data['check'])){
		// 	$check 			= $data['check'];
		// }
		if(!empty($data['check2'])){
			$check2 		= $data['check2'];
		}
		if(!empty($data['check3'])){
			$check3 		= $data['check3'];
		}

		if(!empty($data['check4'])){
			$check4 		= $data['check4'];
		}

		$spool_induk 	= $data['spool_induk'];
		$kode_spool 	= $data['kode_spool'];
		$no_drawing 	= $data['no_drawing'];
		$nm_tanki 		= $data['nm_tanki'];
		$no_ipp 		= str_replace('PRO-','',$data['no_ipp']);
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');
		
		//pengurutan kode
		if($spool_induk == '0'){
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(spool_induk) as maxP FROM max_spool_induk WHERE spool_induk LIKE 'SP-".$YM."%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$spool_induk	= "SP-".$YM.$urut2;
		}

		$GET_DET_IPP = $this->tanki_model->get_ipp_detail($no_ipp);
		$nomor_tanki 		= $GET_DET_IPP['no_so'];

		$ArrUpdateProduksi = [];
		$ArrUpdateDeadstok = [];
		$ArrUpdateDeadstokModif = [];
		$ArrUpdateCutting = [];
		$nomorx = 0;
		$ArrReportFG = [];
		if(!empty($data['check'])){
			foreach ($check as $value) { $nomorx++;
				if($kode_spool == '0'){
					$nomor_so 		= (!empty(get_nomor_so($no_ipp)))?get_nomor_so($no_ipp):$nomor_tanki;
					$srcPlant		= "SELECT MAX(kode_spool) as maxP FROM production_detail WHERE spool_induk = '".$spool_induk."' AND kode_spool LIKE '".$nomor_so."-SP-%' ";
					$resultPlant	= $this->db->query($srcPlant)->result_array();
					$angkaUrut2		= $resultPlant[0]['maxP'];
					$urutan2		= (int)substr($angkaUrut2, 13, 2);
					$urutan2++;
					$urut2			= sprintf('%02s',$urutan2);
					$kode_spool 	= $nomor_so.'-SP-'.$urut2;
				}	

				$EXPLODE 	= explode('-',$value);
				$id_pro 	= $EXPLODE[0];
				$id_cut = null;
				if(!empty($EXPLODE[1])){
					$id_hea 	= str_replace('H','',$EXPLODE[1]);
					$id_cut 	= str_replace('C','',$EXPLODE[2]);
				}
				
				if(empty($id_cut)){
					$ArrUpdateProduksi[$nomorx]['id'] = $id_pro;
					$ArrUpdateProduksi[$nomorx]['spool_induk'] = $spool_induk;
					$ArrUpdateProduksi[$nomorx]['kode_spool'] = $kode_spool;
					$ArrUpdateProduksi[$nomorx]['nm_tanki'] = $nm_tanki;
					$ArrUpdateProduksi[$nomorx]['no_drawing'] = $no_drawing;
					$ArrUpdateProduksi[$nomorx]['spool_by'] = $username;
					$ArrUpdateProduksi[$nomorx]['spool_date'] = $datetime;

					$getFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro'=>$id_pro,'jenis'=>'in'))->result_array();
					$ID_FG = (!empty($getFG[0]['id']))?$getFG[0]['id']:null;
					if(!empty($ID_FG)){
						$ArrReportFG[] = $ID_FG;
					}
				}

				if(!empty($id_cut)){
					$ArrUpdateCutting[$nomorx]['id'] = $id_cut;
					$ArrUpdateCutting[$nomorx]['spool_induk'] = $spool_induk;
					$ArrUpdateCutting[$nomorx]['kode_spool'] = $kode_spool;
					$ArrUpdateCutting[$nomorx]['no_drawing'] = $no_drawing;
					$ArrUpdateCutting[$nomorx]['spool_by'] = $username;
					$ArrUpdateCutting[$nomorx]['spool_date'] = $datetime;

					$getFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro'=>$id_cut,'id_pro_det'=>$id_pro,'jenis'=>'in cutting'))->result_array();
					$ID_FG = (!empty($getFG[0]['id']))?$getFG[0]['id']:null;
					if(!empty($ID_FG)){
						$ArrReportFG[] = $ID_FG;
					}
				}
				
			}
		}
		
		//JOINNYA
		if(!empty($data['check2'])){
			$get_detail_produksi = $this->db
										->select('*')
										->from('so_detail_header')
										->where_in('id', $check2)
										->get()
										->result_array();

			foreach ($get_detail_produksi as $key => $value) {
				$id_qty = "spk2_".$value['id'];
				$QTY 	= $data[$id_qty];
				if($QTY > 0){
					$no_ipp = $data["ipp2_".$value['id']];
					$no_pro = "PRO-".$no_ipp;
					
					if($kode_spool == '0'){
						$nomor_so = get_nomor_so($no_ipp);
						$srcPlant		= "SELECT MAX(kode_spool) as maxP FROM production_detail WHERE spool_induk = '".$spool_induk."' AND kode_spool LIKE '".$nomor_so."-SP-%' ";
						$resultPlant	= $this->db->query($srcPlant)->result_array();
						$angkaUrut2		= $resultPlant[0]['maxP'];
						$urutan2		= (int)substr($angkaUrut2, 13, 2);
						$urutan2++;
						$urut2			= sprintf('%02s',$urutan2);
						$kode_spool 	= $nomor_so.'-SP-'.$urut2;
					}

					// echo $kode_spool.'<br>';
					$qUpdate 	= $this->db->query("UPDATE 
														production_detail
													SET 
														spool_induk='$spool_induk',
														kode_spool='$kode_spool',
														no_drawing='$no_drawing',
														spool_by='$username',
														spool_date='$datetime'
													WHERE 
														id_milik='".$value['id']."'
														AND id_produksi= '".$no_pro."'
														AND kode_spool IS NULL
														AND spool_induk IS NULL
														AND lock_spool_date IS NULL
													ORDER BY 
														id ASC 
													LIMIT $QTY");
					// echo $qUpdate."<br>";
				}
			}
		}

		//DEADSTOKNYA
		if(!empty($data['check3'])){
			foreach ($check3 as $value) { $nomorx++;
				if($kode_spool == '0'){
					$nomor_so 		= get_nomor_so($no_ipp);
					$srcPlant		= "SELECT MAX(kode_spool) as maxP FROM production_detail WHERE spool_induk = '".$spool_induk."' AND kode_spool LIKE '".$nomor_so."-SP-%' ";
					$resultPlant	= $this->db->query($srcPlant)->result_array();
					$angkaUrut2		= $resultPlant[0]['maxP'];
					$urutan2		= (int)substr($angkaUrut2, 13, 2);
					$urutan2++;
					$urut2			= sprintf('%02s',$urutan2);
					$kode_spool 	= $nomor_so.'-SP-'.$urut2;
				}	

				$ArrUpdateDeadstok[$nomorx]['id'] = $value;
				$ArrUpdateDeadstok[$nomorx]['spool_induk'] = $spool_induk;
				$ArrUpdateDeadstok[$nomorx]['kode_spool'] = $kode_spool;
				$ArrUpdateDeadstok[$nomorx]['no_drawing'] = $no_drawing;
				$ArrUpdateDeadstok[$nomorx]['spool_by'] = $username;
				$ArrUpdateDeadstok[$nomorx]['spool_date'] = $datetime;

				$getFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro_det'=>$value,'jenis'=>'in deadstok'))->result_array();
				$ID_FG = (!empty($getFG[0]['id']))?$getFG[0]['id']:null;
				if(!empty($ID_FG)){
					$ArrReportFG[] = $ID_FG;
				}
			}
		}

		if(!empty($data['check4'])){
			foreach ($check4 as $value) { $nomorx++;
				if($kode_spool == '0'){
					$nomor_so 		= get_nomor_so($no_ipp);
					$srcPlant		= "SELECT MAX(kode_spool) as maxP FROM production_detail WHERE spool_induk = '".$spool_induk."' AND kode_spool LIKE '".$nomor_so."-SP-%' ";
					$resultPlant	= $this->db->query($srcPlant)->result_array();
					$angkaUrut2		= $resultPlant[0]['maxP'];
					$urutan2		= (int)substr($angkaUrut2, 13, 2);
					$urutan2++;
					$urut2			= sprintf('%02s',$urutan2);
					$kode_spool 	= $nomor_so.'-SP-'.$urut2;
				}	

				$ArrUpdateDeadstokModif[$nomorx]['id'] = $value;
				$ArrUpdateDeadstokModif[$nomorx]['spool_induk'] = $spool_induk;
				$ArrUpdateDeadstokModif[$nomorx]['kode_spool'] = $kode_spool;
				$ArrUpdateDeadstokModif[$nomorx]['no_drawing'] = $no_drawing;
				$ArrUpdateDeadstokModif[$nomorx]['spool_by'] = $username;
				$ArrUpdateDeadstokModif[$nomorx]['spool_date'] = $datetime;

				$getDeadstockModif = $this->db->get_where('deadstok_modif',array('id'=>$value))->result_array();
				$idDeadstock = (!empty($getDeadstockModif[0]['id_deadstok']))?$getDeadstockModif[0]['id_deadstok']:null;
				$idKodeSPK = (!empty($getDeadstockModif[0]['kode_spk']))?$getDeadstockModif[0]['kode_spk']:null;

				$ArrayGetFG = $this->db->order_by('id','desc')->get_where('data_erp_fg',array('id_pro_det'=>$idDeadstock,'kode_trans'=>$idKodeSPK,'jenis'=>'in deadstok modif'))->result_array();
				
				if(!empty($ArrayGetFG)){
					foreach ($ArrayGetFG as $keyFGDM => $valueFGDM) {
						$ArrReportFG[] = $valueFGDM['id'];
					}
				}
			}
		}

		// print_r($ArrUpdateProduksi);
		// print_r($ArrUpdateCutting);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrUpdateProduksi)){
				$this->db->update_batch('production_detail',$ArrUpdateProduksi,'id');
			}
			if(!empty($ArrUpdateCutting)){
				$this->db->update_batch('so_cutting_detail',$ArrUpdateCutting,'id');
			}
			if(!empty($ArrUpdateDeadstok)){
				$this->db->update_batch('deadstok',$ArrUpdateDeadstok,'id');
			}
			if(!empty($ArrUpdateDeadstokModif)){
				$this->db->update_batch('deadstok_modif',$ArrUpdateDeadstokModif,'id');
			}

			if(!empty($ArrReportFG)){
				$this->saveSpoolERP($spool_induk,$ArrReportFG);
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'spool_induk'	=> $spool_induk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thanks ...',
				'status'	=> 1,
				'spool_induk'	=> $spool_induk
			);
			if(!empty($data['check'])){
				history('Create spool :'.json_encode($check));
			}
			if(!empty($data['check2'])){
				history('Create spool joint :'.json_encode($check2));
			}
			
		}

		echo json_encode($Arr_Kembali);
	}
	
	//SPOOL JOINT
	public function server_side_request_new_joint(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request_new_joint(
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
			
			$NOMOR_SO = explode('-',$row['product_code']);

            $customer = get_name('production','nm_customer','no_ipp', $row['no_ipp']);
            $project = get_name('production','project','no_ipp', $row['no_ipp']);

			$sisa_spk = $this->db->select('COUNT(id) AS sisa_spk')->get_where('production_detail',array('kode_spool'=>NULL,'kode_spk <>'=>NULL,'id_milik'=>$row['id_milik'],'id_produksi'=>$row['id_produksi']))->result();

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product'])."</div>";
			
			$nestedData[]	= "<div align='center'>".$NOMOR_SO[0]."</div>";
			$nestedData[]	= "<div align='left'>".$customer."</div>";
			$nestedData[]	= "<div align='left'>".$project."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter_1'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['diameter_2'])."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green sisa_spk'>".$sisa_spk[0]->sisa_spk."</span></div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk2_".$row['id_milik']."' class='form-control text-center qty_spk input-sm maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' style='width:100px;'><script>$('.maskMoney').maskMoney();</script>
									<input type='hidden' name='ipp2_".$row['id_milik']."' value='".$row['no_ipp']."'>
								</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check2[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$row['id_milik']."' ></div>";
			
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

	public function query_data_request_new_joint($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		if($no_ipp <> '0'){
			$where = " AND a.id_produksi='".$no_ipp."' ";
		}
		//(SELECT COUNT(b.id) FROM production_detail b WHERE b.kode_spk IS NULL AND b.id_milik=a.id_milik AND b.id_produksi=a.id_produksi)
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id,
				REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
				a.id_produksi,
				a.id_milik,
				a.id_category AS product,
				a.id_product,
				a.qty,
				a.product_code,
				b.no_spk,
				b.diameter_1,
                b.diameter_2
			FROM
				production_detail a
				LEFT JOIN so_detail_header b ON a.id_milik = b.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = b.id_bq,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." ".$where2."
				AND a.kode_spk IS NOT NULL
				AND a.kode_spool IS NULL
				AND a.id_category IN ".DirectFinishGood()."
				AND (
					a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
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
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


	//PRINT SPK
	public function spk_produksi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_produksi';
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'SPK Produksi',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data spk produksi only print');
		$this->load->view('Ppic/spk_produksi',$data);
	}

	public function server_side_spk_produksi(){
		$requestData	= $_REQUEST;
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spk_produksi';
		$Arr_Akses			= getAcccesmenu($controller);
		
		$fetch			= $this->query_data_spk_produksi(
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
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
				$so_number = (!empty($row['so_number2']))?$row['so_number2']:$row['no_ipp'];
			$nestedData[]	= "<div align='center'>".$so_number."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";

			$class = Color_status($row['sts_produksi']);
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:".$class."'>".$row['sts_produksi']."</span></div>";
				$create = "";	
				if($Arr_Akses['create']=='1'){
					$create = "<button class='btn btn-sm btn-primary detail_spk' title='Detail Production' data-id_produksi='".$row['id_produksi']."'><i class='fa fa-eye'></i></button>";
				}
			$nestedData[]	= "<div align='center'>
									".$create."
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

	public function query_data_spk_produksi($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.project,
				c.so_number AS so_number2
			FROM
				production_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp
				LEFT JOIN so_number c ON a.no_ipp = REPLACE(c.id_bq, 'BQ-', ''),
                (SELECT @row:=0) r
		    WHERE a.deleted = 'N' 
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'c.so_number',
			3 => 'b.project',
			4 => 'created_date'
		);

		$sql .= " ORDER BY a.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_detail_spk(){
		$id_produksi= $this->uri->segment(3);
		$id_bq 		= "BQ-".str_replace('PRO-','',$id_produksi);
		
		$row		= $this->db->get_where('production_header', array('id_produksi'=>$id_produksi))->result_array();

		$HelpDet 	= "bq_detail_header";
		$help2 = "";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$help2 = " b.id_milik AS id_milik2,";
		}

		$Disb 	= "";
		if($row[0]['sts_produksi'] == 'FINISH'){
			$Disb = "disabled";
		}

		$qDetail	= "	SELECT
							a.*,
							b.no_komponen,
							b.approve AS app_so,
							b.id_category AS comp,
							b.id AS id_uniq,
							b.id_bq_header
						FROM
							production_detail a
							LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		// echo $qDetail;
		$rowD		= $this->db->query($qDetail)->result_array();
		
		// $sql_mat 	= "SELECT * FROM production_acc_and_mat WHERE id_bq='".$id_bq."' AND category = 'mat' ";
		$rest_mat 	= $this->db->get_where('production_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array(); 
		$rest_acc 	= $this->db->get_where('production_acc_and_mat', array('id_bq'=>$id_bq, 'category <>'=>'mat'))->result_array(); 
		
		$data = array(
			'id_produksi' 	=> $id_produksi,
			'id_bq' 		=> $id_bq,
			'rest_mat' 		=> $rest_mat,
			'rest_acc' 		=> $rest_acc,
			'row' 			=> $row,
			'rowD' 			=> $rowD,
			'HelpDet'		=> $HelpDet,
			'Disb'			=> $Disb,
			'jalur'			=> $row[0]['jalur']
		);
		
		$this->load->view('Ppic/modal_detail_spk', $data);
	}

	public function print_spk_produksi_satuan(){
		$id_uniq		= $this->uri->segment(3);
		$id_produksi	= $this->uri->segment(4);
		$id_bq 			= "BQ-".str_replace('PRO-','',$id_produksi);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $this->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		$help2 = "";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$help2 = " b.id_milik AS id_milik2,";
		}
		
		$qDetail	= "	SELECT
							a.*,
							b.no_komponen,
							b.id_category AS comp,
							b.id AS id_uniq
						FROM
							production_detail a
							LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		$rowD		= $this->db->query($qDetail)->result_array();

		$detail_product = $this->db->select('id_product,id_category')->get_where('production_detail',array('id_produksi'=>$id_produksi,'id_milik'=>$id_uniq))->result();
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_milik' => $id_uniq,
			'id_bq' => $id_bq,
			'kode_product' => $detail_product[0]->id_product,
			'kode_produksi' => $id_produksi,
			'detail_product' => $detail_product,
			'rowD' => $rowD,
			'qty' => 1,
			'HelpDet' => $HelpDet,
		);
		history('Print progress produksi : id_milik '.$id_uniq); 
		$this->load->view('Print/print_spk_produksi_satuan', $data);
	}

	//SPOOL DEADSTOK
	public function server_side_request_deadstok(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request_deadstok(
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
			
            $customer = get_name('production','nm_customer','no_ipp', $row['no_ipp']);
            $project = get_name('production','project','no_ipp', $row['no_ipp']);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";

			$nestedData[]	= "<div align='left'>".strtoupper($row['product_name'].', '.$row['type_std'].' '.$row['resin'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product_spec'].' x '.number_format($row['length']))."</div>";
			$nestedData[]	= "<div align='left'>".$customer."</div>";
			$nestedData[]	= "<div align='left'>".$project."</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check3[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$row['id']."' ></div>";
			
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

	public function query_data_request_deadstok($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		if($no_ipp <> '0'){
			$where = " AND a.no_ipp='".str_replace('PRO-','',$no_ipp)."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				deadstok a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where."
				AND a.process_next = '2'
				AND a.spool_induk IS NULL
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'no_spk',
			3 => 'no_so',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//SPOOL DEADSTOK MODIF
	public function server_side_request_deadstok_modif(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request_deadstok_modif(
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
			
            $customer = get_name('production','nm_customer','no_ipp', $row['no_ipp']);
            $project = get_name('production','project','no_ipp', $row['no_ipp']);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_so']."</div>";

			$nestedData[]	= "<div align='left'>".strtoupper($row['product_name'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product_spec'].' x '.number_format($row['length']))."</div>";
			$nestedData[]	= "<div align='left'>".$customer."</div>";
			$nestedData[]	= "<div align='left'>".$project."</div>";
			$nestedData[]	= "<div align='center'><input type='checkbox' name='check4[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$row['id_modif']."' ></div>";
			
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

	public function query_data_request_deadstok_modif($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		if($no_ipp <> '0'){
			$where = " AND a.no_ipp='".str_replace('PRO-','',$no_ipp)."' ";
		}
		
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				z.id AS id_modif
			FROM
				deadstok_modif z
				LEFT JOIN deadstok a ON z.id_deadstok = a.id,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where."
				AND z.kode_delivery IS NULL
				AND z.spool_induk IS NULL
				AND z.status_close_produksi = 'Y'
				AND z.cutting = 'N'
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'no_spk',
			3 => 'no_so',
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function saveSpoolERP($kode_spool,$ArrFG){
		$CheckSpool = $this->db->get_where('spool_data_erp',array('kode_spool'=>$kode_spool))->result_array();
		$ArrFG_Before = [];
		if(!empty($CheckSpool)){
			$id_fg = $CheckSpool[0]['id_fg'];
			$ArrFG_Before = explode(',',$id_fg);
		}

		$ArrMerge = array_merge($ArrFG,$ArrFG_Before);
		$ArrImplode = implode(',',$ArrMerge);
		// echo $ArrImplode;
		// exit;
		if(!empty($CheckSpool)){
			$this->db->where('kode_spool',$kode_spool);
			$this->db->update('spool_data_erp',['id_fg'=>$ArrImplode]);
		}
		if(empty($CheckSpool)){
			$this->db->insert('spool_data_erp',['kode_spool'=>$kode_spool,'id_fg'=>$ArrImplode]);
		}
	}

	public function RemovesaveSpoolERP($kode_spool,$ArrRemove){
		$CheckSpool = $this->db->get_where('spool_data_erp',array('kode_spool'=>$kode_spool))->result_array();
		$ArrFG_Before = [];
		if(!empty($CheckSpool)){
			$id_fg = $CheckSpool[0]['id_fg'];
			$ArrFG_Before = explode(',',$id_fg);
		}

		$ArrMerge = array_diff($ArrFG_Before,$ArrRemove);
		$ArrImplode = implode(',',$ArrMerge);
		// echo $ArrImplode;
		// exit;
		if(!empty($CheckSpool)){
			$this->db->where('kode_spool',$kode_spool);
			$this->db->update('spool_data_erp',['id_fg'=>$ArrImplode]);
		}
	}

	public function SpoolToWIP_Report($kode)
	{
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		$username 		= $data_session['ORI_User']['username'];

		$CheckID_FG	= $this->db->get_where('spool_data_erp',array('kode_spool'=>$kode))->result_array();
		$id_fg 		= (!empty($CheckID_FG[0]['id_fg']))?explode(',',$CheckID_FG[0]['id_fg']):[];
		
		if(!empty($id_fg)){
			$getQCDeadstockModif = $this->db->where_in('id',$id_fg)->get_where('data_erp_fg')->result_array();
			$ArrIN_WIP_MATERIAL = [];
			$ArrIN_FG_MATERIAL = [];
			foreach ($getQCDeadstockModif as $key => $value) {
				$ArrIN_WIP_MATERIAL[$key]['tanggal'] = date('Y-m-d');
				$ArrIN_WIP_MATERIAL[$key]['keterangan'] = 'Finish Good to WIP (Spool)';
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
				$ArrIN_WIP_MATERIAL[$key]['jenis'] =  'in spool';
				$ArrIN_WIP_MATERIAL[$key]['id_material'] =  $value['id_material'];
				$ArrIN_WIP_MATERIAL[$key]['nm_material'] = $value['nm_material'];
				$ArrIN_WIP_MATERIAL[$key]['qty_mat'] =  $value['qty_mat'];
				$ArrIN_WIP_MATERIAL[$key]['cost_book'] =  $value['cost_book'];
				$ArrIN_WIP_MATERIAL[$key]['gudang'] =  $value['gudang'];
				$ArrIN_WIP_MATERIAL[$key]['kode_spool'] =  $kode;

				$ArrIN_FG_MATERIAL[$key]['tanggal'] = date('Y-m-d');
				$ArrIN_FG_MATERIAL[$key]['keterangan'] = 'Finish Good to WIP (Spool)';
				$ArrIN_FG_MATERIAL[$key]['no_so'] = $value['no_so'];
				$ArrIN_FG_MATERIAL[$key]['product'] = $value['product'];
				$ArrIN_FG_MATERIAL[$key]['no_spk'] = $value['no_spk'];
				$ArrIN_FG_MATERIAL[$key]['kode_trans'] = $value['kode_trans'];
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
				$ArrIN_FG_MATERIAL[$key]['id_pro'] =  $value['id_pro'];
				$ArrIN_FG_MATERIAL[$key]['qty_ke'] =  $value['qty_ke'];
				$ArrIN_FG_MATERIAL[$key]['nilai_unit'] =  $value['nilai_unit'];
				$ArrIN_FG_MATERIAL[$key]['jenis'] =  'out spool';
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
			//GROUPING

			$ArrIN_WIP_Spool=[];
			if(!empty($ArrIN_WIP_MATERIAL)){
				$nilai_wip = 0;
				$material = 0;
				$wip_direct = 0;
				$wip_indirect = 0;
				$wip_consumable = 0;
				$wip_foh = 0;

				$no_so = [];
				$no_spk = [];
				$no_id_trans = [];
				foreach ($ArrIN_WIP_MATERIAL as $key => $value) {
					// if(!empty($value['id_trans']) AND $value['id_trans'] > 0){
						$nilai_wip += $value['nilai_wip'];
						$material += $value['material'];
						$wip_direct += $value['wip_direct'];
						$wip_indirect += $value['wip_indirect'];
						$wip_consumable += $value['wip_consumable'];
						$wip_foh += $value['wip_foh'];

						$no_so[] = $value['no_so'];
						$no_spk[] = $value['no_spk'];
						$no_id_trans[] = $value['id_trans'];

						$NmProductSpool = 'Spool '.$kode;
						$TandaTanki = substr($value['no_so'],0,3);
						if($TandaTanki=='SOC'){
							$NmProductSpool = 'Tanki '.$kode;
						}
					// }
				}

				$Implode_noSO = null;
				if(!empty($no_so)){
					$Implode_noSO = implode(',',array_unique($no_so));
				}

				$Implode_noSPK = null;
				if(!empty($no_spk)){
					$Implode_noSPK = implode(',',array_unique($no_spk));
				}

				$Implode_noIDTrans = null;
				if(!empty($no_id_trans)){
					$Implode_noIDTrans = implode(',',array_unique($no_id_trans));
				}

				$ArrIN_WIP_Spool[0]['tanggal'] = date('Y-m-d');
				$ArrIN_WIP_Spool[0]['keterangan'] = 'Finish Good to WIP (Spool)';
				$ArrIN_WIP_Spool[0]['no_so'] = $Implode_noSO;
				$ArrIN_WIP_Spool[0]['product'] = $NmProductSpool;
				$ArrIN_WIP_Spool[0]['no_spk'] = $Implode_noSPK;
				$ArrIN_WIP_Spool[0]['kode_trans'] = $kode;
				$ArrIN_WIP_Spool[0]['id_pro_det'] = null;
				$ArrIN_WIP_Spool[0]['qty'] = 1;
				$ArrIN_WIP_Spool[0]['nilai_wip'] = $nilai_wip;
				$ArrIN_WIP_Spool[0]['material'] = $material;
				$ArrIN_WIP_Spool[0]['wip_direct'] =  $wip_direct;
				$ArrIN_WIP_Spool[0]['wip_indirect'] =  $wip_indirect;
				$ArrIN_WIP_Spool[0]['wip_consumable'] =  $wip_consumable;
				$ArrIN_WIP_Spool[0]['wip_foh'] =  $wip_foh;
				$ArrIN_WIP_Spool[0]['created_by'] = $username;
				$ArrIN_WIP_Spool[0]['created_date'] = $dateTime;
				$ArrIN_WIP_Spool[0]['id_trans'] =  str_replace('-','',$kode);
				$ArrIN_WIP_Spool[0]['jenis'] =  'in spool';
				$ArrIN_WIP_Spool[0]['id_material'] =  null;
				$ArrIN_WIP_Spool[0]['nm_material'] = null;
				$ArrIN_WIP_Spool[0]['qty_mat'] =  null;
				$ArrIN_WIP_Spool[0]['cost_book'] =  null;
				$ArrIN_WIP_Spool[0]['gudang'] =  null;
				$ArrIN_WIP_Spool[0]['kode_spool'] =  $kode;
			}

			$this->db->trans_start();
				if(!empty($ArrIN_WIP_Spool)){
					$this->db->insert_batch('data_erp_wip_group',$ArrIN_WIP_Spool);
				}

				if(!empty($ArrIN_FG_MATERIAL)){
					$this->db->insert_batch('data_erp_fg',$ArrIN_FG_MATERIAL);

					$this->jurnalOuttoWip($kode);
				}

				
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
			}
		}
	}

	function jurnalOuttoWip($kode){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	        $idtrans = str_replace('-','',$kode);

			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE kode_spool ='".$idtrans."' AND tanggal ='".$Date."' AND jenis='in spool'")->result();
			
			$totalwip =0;
			  
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->product;	
				$tgl_voucher = $data->tanggal;
				$fg_txt         ='FINISHED GOOD'; 
				$wip_txt         ='WIP';	
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
				
				$totalfg        = $cogs;
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
					  'no_perkiraan'  => $coa_wip,
					  'keterangan'    => $keterangan2,
					  'no_reff'       => $id.$noso,
					  'debet'         => $finishgood,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'Finishgood Part To WIP',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 ); 			
				
			}
			
		   
			$fg = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE kode_spool ='".$idtrans."' AND tanggal ='".$Date."' AND jenis='out spool'")->result();
			
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
				
				$totalfg        = $cogs;
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
					  'debet'         => 0,
					  'kredit'        => $finishgood,
					  'jenis_jurnal'  => 'Finishgood Part To WIP',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 ); 			
				
			}

			
			        
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='finishgood part to WIP' and no_reff ='$id' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'Finishgood Part To WIP'.$keterangan;
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

	public function jurnal_spool_manual(){
		    $data_session	= $this->session->userdata;
			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			$tgl            = date('Y-m-d');
			
			$dataspool = $this->db->query("select * from dataspool_jurnal")->result();
			foreach($dataspool AS $record){
                $kd_trans = $record->kd_trans;
				$datatemp = $this->db->query("select * from jurnal_temp WHERE kode_trans = '$kd_trans' AND updated_date LIKE '2024%' AND category LIKE '%spool%' AND posisi='DEBIT'")->result();				
				$nilai=0;
				$total=0;

				foreach($datatemp AS $datasp){	
						$tgl_spool     =$datasp->updated_date;
						$posisi        =$datasp->posisi;
						$keterangan    =$datasp->keterangan;
						$category      =$datasp->category;
						$gudang        =$datasp->gudang;

						$tgl_voucher = substr($tgl_spool,0,10);
						$Bln	  = substr($tgl_voucher,5,2);
						$Thn	  = substr($tgl_voucher,0,4);
                        $this->load->model('jurnal_model');
				        $Nomor_JV = $this->jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
						if($keterangan=='wip'){
							$nokir ='1103-03-05';
						}elseif($keterangan=='finish good'){
                            $nokir ='1103-04-01';
						}
                        $nilai = round($datasp->amount);
						$total += $nilai;

						if($gudang=='15'){
								
									$det_Jurnaltes1 = array(
									'nomor'         => $Nomor_JV,
									'tanggal'       => $tgl_voucher,
									'tipe'          => 'JV',
									'no_perkiraan'  => '1103-04-01',
									'keterangan'    => $category.' '.'FINISH GOOD',
									'no_reff'       => $kd_trans,
									'debet'         => $nilai,
									'kredit'        => 0,
									'created_on'    => $DateTime,
									);

									$det_Jurnaltes2 = array(
										'nomor'         => $Nomor_JV,
										'tanggal'       => $tgl_voucher,
										'tipe'          => 'JV',
										'no_perkiraan'  => '1103-03-05',
										'keterangan'    => $category.' '.'WIP',
										'no_reff'       => $kd_trans,
										'debet'         => 0,
										'kredit'        => $nilai,
										'created_on'    => $DateTime,
										);
								
								
								
							}elseif($gudang=='14'){

							
									$det_Jurnaltes1 = array(
									'nomor'         => $Nomor_JV,
									'tanggal'       => $tgl_voucher,
									'tipe'          => 'JV',
									'no_perkiraan'  => '1103-03-05',
									'keterangan'    => $category.' '.'WIP',
									'no_reff'       => $kd_trans,
									'debet'         => $nilai,
									'kredit'        => 0,
									'created_on'    => $DateTime,
									);

									$det_Jurnaltes2 = array(
										'nomor'         => $Nomor_JV,
										'tanggal'       => $tgl_voucher,
										'tipe'          => 'JV',
										'no_perkiraan'  => '1103-04-01',
										'keterangan'    => $category.' '.'FINISH GOOD',
										'no_reff'       => $kd_trans,
										'debet'         => 0,
										'kredit'        => $nilai,
										'created_on'    => $DateTime,
										);
							

							}

						$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $category, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $kd_trans, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
						$this->db->insert(DBACC.'.javh',$dataJVhead);						
							$this->db->insert(DBACC.'.jurnal',$det_Jurnaltes1);				
							$this->db->insert(DBACC.'.jurnal',$det_Jurnaltes2);
						
								
				}

				
                
			}
	}



	public function saveTempSpool(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');

		$username 		= $data_session['ORI_User']['username'];
		$dataspool     	= $data['dataspool'];
		$checked 		= $data['checked'];

		$tanda = ($checked=='true')?'insert':'delete';

		$ArrInsert = [
			'key_spool' => $dataspool,
			'insert_by' => $username
		];
		
		$this->db->trans_start();
			if($checked == 'true'){
				$this->db->insert('spool_temp', $ArrInsert);
			}
			else{
				$this->db->where('key_spool',$dataspool);
				$this->db->where('insert_by',$username);
				$this->db->delete('spool_temp');
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Failed Process !!!',
				'alert'		=>'Product gagal ditambahkan! Try Again !',
				'status'	=> 2
			);
		}
		else{
			$this->db->trans_commit();
			$dataList = $this->db->get_where('spool_temp',['insert_by'=>$username])->result_array();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success ['.$tanda.']',
				'alert'		=>'['.COUNT($dataList).'] Product berhasil ditambahkan!',
				'status'	=> 1
			);
		}

		echo json_encode($Arr_Kembali);
	}

	public function checkSpoolSelected($username){
		$dataList = $this->db->get_where('spool_temp',['insert_by'=>$username])->result_array();
		$ArrayList = [];
		foreach ($dataList as $key => $value) {
			$ArrayList[$value['key_spool']] = TRUE;
		}

		return $ArrayList;
	}

}