<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qc_pipe_cutting extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
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
			$UserName		= $data_session['ORI_User']['username'];

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

			//SaveReport
			$getCuttingDetail	= $this->db->get_where('so_cutting_detail',array('id_header'=>$id))->result_array();
			$ArrFG_IN = [];
			$ArrWIP_OUT = [];

			$ArrFG_IN_DEADSTOCK = [];
			$ArrWIP_OUT_DEADSTOCK = [];
			foreach ($getCuttingDetail as $key => $value) {
				$GetDetCut	= $this->db->get_where('data_erp_wip_group',array('id_trans'=>$value['id'],'jenis'=>'in cutting'))->result_array();
				
				if(!empty($GetDetCut)){
					$idprodet = $GetDetCut[0]['id_pro_det'];
					$ArrFG_IN[$key]['tanggal'] 		= date('Y-m-d');
					$ArrFG_IN[$key]['keterangan'] 	= 'WIP to Finish Good (Cutting)';
					$ArrFG_IN[$key]['no_so'] 		= $GetDetCut[0]['no_so'];
					$ArrFG_IN[$key]['product'] 		= $GetDetCut[0]['product'];
					$ArrFG_IN[$key]['no_spk'] 		= $GetDetCut[0]['no_spk'];
					$ArrFG_IN[$key]['kode_trans'] 	= $GetDetCut[0]['kode_trans'];
					$ArrFG_IN[$key]['id_pro_det'] 	= $GetDetCut[0]['id_pro_det'];
					$ArrFG_IN[$key]['qty'] 			= 1;
					$ArrFG_IN[$key]['nilai_unit'] 	= $GetDetCut[0]['nilai_wip'];
					$ArrFG_IN[$key]['nilai_wip'] 	= $GetDetCut[0]['nilai_wip'];
					$ArrFG_IN[$key]['material'] 		= $GetDetCut[0]['material'];
					$ArrFG_IN[$key]['wip_direct'] 		= $GetDetCut[0]['wip_direct'];
					$ArrFG_IN[$key]['wip_indirect'] 	= $GetDetCut[0]['wip_indirect'];
					$ArrFG_IN[$key]['wip_consumable'] 	= $GetDetCut[0]['wip_consumable'];
					$ArrFG_IN[$key]['wip_foh'] 			= $GetDetCut[0]['wip_foh'];
					$ArrFG_IN[$key]['created_by'] = $UserName;
					$ArrFG_IN[$key]['created_date'] = $dateTime;
					$ArrFG_IN[$key]['jenis'] = 'in cutting';
					$ArrFG_IN[$key]['id_trans'] = $GetDetCut[0]['id_trans'];
					$ArrFG_IN[$key]['id_pro'] = $GetDetCut[0]['id_trans'];

					$ArrWIP_OUT[$key]['tanggal'] 		= date('Y-m-d');
					$ArrWIP_OUT[$key]['keterangan'] 	= 'WIP to Finish Good (Cutting)';
					$ArrWIP_OUT[$key]['no_so'] 			= $GetDetCut[0]['no_so'];
					$ArrWIP_OUT[$key]['product'] 		= $GetDetCut[0]['product'];
					$ArrWIP_OUT[$key]['no_spk'] 		= $GetDetCut[0]['no_spk'];
					$ArrWIP_OUT[$key]['kode_trans'] 	= $GetDetCut[0]['kode_trans'];
					$ArrWIP_OUT[$key]['id_pro_det'] 	= $GetDetCut[0]['id_pro_det'];
					$ArrWIP_OUT[$key]['qty'] 			= 1;
					$ArrWIP_OUT[$key]['nilai_wip'] 		= $GetDetCut[0]['nilai_wip'];
					$ArrWIP_OUT[$key]['material'] 		= $GetDetCut[0]['material'];
					$ArrWIP_OUT[$key]['wip_direct'] 	= $GetDetCut[0]['wip_direct'];
					$ArrWIP_OUT[$key]['wip_indirect'] 	= $GetDetCut[0]['wip_indirect'];
					$ArrWIP_OUT[$key]['wip_consumable'] 	= $GetDetCut[0]['wip_consumable'];
					$ArrWIP_OUT[$key]['wip_foh'] 			= $GetDetCut[0]['wip_foh'];
					$ArrWIP_OUT[$key]['created_by'] = $UserName;
					$ArrWIP_OUT[$key]['created_date'] = $dateTime;
					$ArrWIP_OUT[$key]['jenis'] = 'out cutting';
					$ArrWIP_OUT[$key]['id_trans'] = $GetDetCut[0]['id_trans'];
				}
				//Deadstock
				$GetDetCut_Deadstock	= $this->db->get_where('data_erp_wip_group',array('id_trans'=>$value['id'],'jenis'=>'in cutting deadstok'))->result_array();
				
				if(!empty($GetDetCut_Deadstock)){
					$idprodet = $GetDetCut_Deadstock[0]['id_pro_det'];
					$ArrFG_IN_DEADSTOCK[$key]['tanggal'] 		= date('Y-m-d');
					$ArrFG_IN_DEADSTOCK[$key]['keterangan'] 	= 'WIP to Finish Good (Cutting Deadstock)';
					$ArrFG_IN_DEADSTOCK[$key]['no_so'] 		= $GetDetCut_Deadstock[0]['no_so'];
					$ArrFG_IN_DEADSTOCK[$key]['product'] 		= $GetDetCut_Deadstock[0]['product'];
					$ArrFG_IN_DEADSTOCK[$key]['no_spk'] 		= $GetDetCut_Deadstock[0]['no_spk'];
					$ArrFG_IN_DEADSTOCK[$key]['kode_trans'] 	= $GetDetCut_Deadstock[0]['kode_trans'];
					$ArrFG_IN_DEADSTOCK[$key]['id_pro_det'] 	= $GetDetCut_Deadstock[0]['id_pro_det'];
					$ArrFG_IN_DEADSTOCK[$key]['qty'] 			= 1;
					$ArrFG_IN_DEADSTOCK[$key]['nilai_unit'] 	= $GetDetCut_Deadstock[0]['nilai_wip'];
					$ArrFG_IN_DEADSTOCK[$key]['nilai_wip'] 	= $GetDetCut_Deadstock[0]['nilai_wip'];
					$ArrFG_IN_DEADSTOCK[$key]['material'] 		= $GetDetCut_Deadstock[0]['material'];
					$ArrFG_IN_DEADSTOCK[$key]['wip_direct'] 		= $GetDetCut_Deadstock[0]['wip_direct'];
					$ArrFG_IN_DEADSTOCK[$key]['wip_indirect'] 	= $GetDetCut_Deadstock[0]['wip_indirect'];
					$ArrFG_IN_DEADSTOCK[$key]['wip_consumable'] 	= $GetDetCut_Deadstock[0]['wip_consumable'];
					$ArrFG_IN_DEADSTOCK[$key]['wip_foh'] 			= $GetDetCut_Deadstock[0]['wip_foh'];
					$ArrFG_IN_DEADSTOCK[$key]['created_by'] = $UserName;
					$ArrFG_IN_DEADSTOCK[$key]['created_date'] = $dateTime;
					$ArrFG_IN_DEADSTOCK[$key]['jenis'] = 'in cutting deadstok';
					$ArrFG_IN_DEADSTOCK[$key]['id_trans'] = $GetDetCut_Deadstock[0]['id_trans'];
					$ArrFG_IN_DEADSTOCK[$key]['id_pro'] = $GetDetCut_Deadstock[0]['id_trans'];

					$ArrWIP_OUT_DEADSTOCK[$key]['tanggal'] 		= date('Y-m-d');
					$ArrWIP_OUT_DEADSTOCK[$key]['keterangan'] 	= 'WIP to Finish Good (Cutting Deadstock)';
					$ArrWIP_OUT_DEADSTOCK[$key]['no_so'] 			= $GetDetCut_Deadstock[0]['no_so'];
					$ArrWIP_OUT_DEADSTOCK[$key]['product'] 		= $GetDetCut_Deadstock[0]['product'];
					$ArrWIP_OUT_DEADSTOCK[$key]['no_spk'] 		= $GetDetCut_Deadstock[0]['no_spk'];
					$ArrWIP_OUT_DEADSTOCK[$key]['kode_trans'] 	= $GetDetCut_Deadstock[0]['kode_trans'];
					$ArrWIP_OUT_DEADSTOCK[$key]['id_pro_det'] 	= $GetDetCut_Deadstock[0]['id_pro_det'];
					$ArrWIP_OUT_DEADSTOCK[$key]['qty'] 			= 1;
					$ArrWIP_OUT_DEADSTOCK[$key]['nilai_wip'] 		= $GetDetCut_Deadstock[0]['nilai_wip'];
					$ArrWIP_OUT_DEADSTOCK[$key]['material'] 		= $GetDetCut_Deadstock[0]['material'];
					$ArrWIP_OUT_DEADSTOCK[$key]['wip_direct'] 	= $GetDetCut_Deadstock[0]['wip_direct'];
					$ArrWIP_OUT_DEADSTOCK[$key]['wip_indirect'] 	= $GetDetCut_Deadstock[0]['wip_indirect'];
					$ArrWIP_OUT_DEADSTOCK[$key]['wip_consumable'] 	= $GetDetCut_Deadstock[0]['wip_consumable'];
					$ArrWIP_OUT_DEADSTOCK[$key]['wip_foh'] 			= $GetDetCut_Deadstock[0]['wip_foh'];
					$ArrWIP_OUT_DEADSTOCK[$key]['created_by'] = $UserName;
					$ArrWIP_OUT_DEADSTOCK[$key]['created_date'] = $dateTime;
					$ArrWIP_OUT_DEADSTOCK[$key]['jenis'] = 'out cutting deadstok';
					$ArrWIP_OUT_DEADSTOCK[$key]['id_trans'] = $GetDetCut_Deadstock[0]['id_trans'];
				}
			}

			// print_r($ArrFG_IN);
			// print_r($ArrWIP_OUT);
			// exit;
			
			$this->db->trans_start();
                $this->db->where('id',$id);
                $this->db->update('so_cutting_header', $ArrUpdate);

				$this->db->where('id_header',$id);
                $this->db->update('so_cutting_detail', $ArrUpdateDetail);

				if(!empty($ArrFG_IN)){
					$this->db->insert_batch('data_erp_fg',$ArrFG_IN);
				}
		
				if(!empty($ArrWIP_OUT)){
					$this->db->insert_batch('data_erp_wip_group',$ArrWIP_OUT);
				}

				if(!empty($ArrFG_IN_DEADSTOCK)){
					$this->db->insert_batch('data_erp_fg',$ArrFG_IN_DEADSTOCK);
				}
		
				if(!empty($ArrWIP_OUT_DEADSTOCK)){
					$this->db->insert_batch('data_erp_wip_group',$ArrWIP_OUT_DEADSTOCK);
				}

				$this->jurnalIntoFGcutting($idprodet);

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

	function jurnalIntoFGcutting($idprodet){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	        $idtrans = $idprodet;

			
			$fg = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans,kode_trans,qty, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE id_pro_det ='".$idtrans."' AND tanggal ='".$Date."' AND jenis LIKE 'in cutting%'")->result();
			
			$totalfg =0;
			  
			$det_Jurnaltes = [];
			   $qty_n=0;
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
					  'jenis_jurnal'  => 'WIP to Finish Good (Cutting)',
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
					  'jenis_jurnal'  => 'WIP to Finish Good (Cutting)',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 ); 		


					$kode_trans = $data->kode_trans;
					$nospk      = $data->no_spk;
					$qty        = $data->qty;

					$this->db->query("UPDATE  warehouse_stock_wipx SET qty = qty-1  WHERE no_so ='".$noso."' AND kode_trans ='".$kode_trans."'  AND no_spk ='".$nospk."' AND product ='".$nm_material."'");
			  		 $qty_n++;
				
			}

			
			        
		
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='finishgood part to WIP' and no_reff ='$idtrans' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'WIP to Finish Good (Cutting)'.$keterangan;
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

			$wipgroup = $this->db->query("SELECT * FROM data_erp_fg WHERE id_pro_det ='".$idtrans."' limit 1")->row();	
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
										(`data_erp_wip_group`.`id_pro_det` = '".$idprodet."')
										AND (`data_erp_wip_group`.`jenis`='out cutting')
										AND (`data_erp_wip_group`.`keterangan` = 'WIP to Finish Good (Cutting)')
										GROUP BY kode_trans,no_spk,product,no_so")->result();

			
			// $cekstok = $this->db->query("SELECT * FROM warehouse_stock_fg WHERE kode_trans ='".$kodetrans."' 
			// AND no_so ='".$so."' AND no_spk ='".$spk."' AND product ='".$product."'")->row();

		


			
			// if(!empty($cekstok)){
            // foreach ($stokwip as $vals) {
			// $qty = 	$vals->total;
            // $this->db->query("UPDATE  warehouse_stock_fg SET qty = qty+$qty_n  WHERE no_so ='".$so."' AND kode_trans ='".$kodetrans."'  AND no_spk ='".$spk."' AND product ='".$product."' ");
			// }
			// }else{
			$datastokfg=array();
			foreach ($stokwip as $vals) {
			$datastokfg = array(
						'tanggal' => $tgl_voucher,
						'keterangan' => 'WIP to Finish Good (Cutting)',
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

			
		//}
		  
	}

}