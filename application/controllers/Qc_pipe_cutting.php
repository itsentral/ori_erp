<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qc_pipe_cutting extends CI_Controller {

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

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data_ipp = $this->db->group_by('id_bq')->get_where('so_cutting_header',array('sts_closing'=>'Y','qc_date'=>NULL))->result_array();
        
		$data = array(
			'title'			=> 'QC Pipe Cutting',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'data_ipp'		=> $data_ipp,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data qc pipe cutting');
		$this->load->view('Qc_pipe_cutting/index',$data);
	}

    public function server_side_spk_cutting(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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

            $sts_confirm = ($row['sts_closing'] == 'Y' AND $row['qc_date'] != NULL)?"<span class='badge bg-green'>Confirm</span>":"<span class='badge bg-blue'>Waiting QC</span>";
            $nestedData[]	= "<div align='center'>".$sts_confirm."</div>";

            $cutting = "";
            $lock = "";
            $edit = "";
            $print = "";
            $view = "";

				if($row['sts_closing'] == 'Y' AND $row['qc_date'] == NULL){
					$cutting = "&nbsp;<a href='".base_url($this->uri->segment(1).'/confirm/'.$row['id'])."' class='btn btn-sm btn-success' title='QC'><i class='fa fa-check'></i></a>";
                    // $print = "&nbsp;<a href='".base_url('ppic/print_cutting/'.$row['id'])."' target='_blank' class='btn btn-sm btn-warning' title='Print SPK Cutting'><i class='fa fa-print'></i></a>";
				}
				if($row['cutting'] == 'Y'){
					$view = "<a href='".base_url($this->uri->segment(1).'/detail/'.$row['id'])."' class='btn btn-sm btn-default' title='View'><i class='fa fa-eye'></i></a>";
				}

				$nestedData[]	= "<div align='left'>".$view.$cutting.$lock.$edit.$print."</div>";
			

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
		    WHERE  1=1 ".$where." ".$where2." AND a.sts_closing = 'Y' AND a.qc_date IS NULL AND(
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

		$sql .= " ORDER BY a.app_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function detail($id=null){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
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
		$this->load->view('Qc_pipe_cutting/detail',$data);
	}

    public function confirm($id=null){
        if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

            $id = $data['id'];
            $qc_status = $data['qc_status'];
            $qc_daycode = $data['qc_daycode'];
            $qc_pass_date = date('Y-m-d',strtotime($data['qc_pass_date']));
            $qc_keterangan = $data['qc_keterangan'];

            $ArrUpdate = [
                'qc_by' => $data_session['ORI_User']['username'],
                'qc_date' => $dateTime
            ];

			$ArrUpdateDetail = [
                'qc_status' => $qc_status,
                'qc_daycode' => $qc_daycode,
                'qc_pass_date' => $qc_pass_date,
                'qc_keterangan' => $qc_keterangan,
                'qc_by' => $data_session['ORI_User']['username'],
                'qc_date' => $dateTime
            ];
			
			$this->db->trans_start();
                $this->db->where('id',$id);
                $this->db->update('so_cutting_header', $ArrUpdate);

				$this->db->where('id_header',$id);
                $this->db->update('so_cutting_detail', $ArrUpdateDetail);
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
				history('QC spk cutting '.$id);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
            $controller			= ucfirst(strtolower($this->uri->segment(1)));
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
                'title'			=> 'QC Cutting Pipe',
                'action'		=> 'index',
                'detail'		=> $detail,
                'row_group'		=> $data_Group,
                'akses_menu'	=> $Arr_Akses
            );
            $this->load->view('Qc_pipe_cutting/confirm',$data);
        }
	}

}