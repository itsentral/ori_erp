<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wip_pipe_cutting extends CI_Controller {

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
		$data_ipp = $this->db->group_by('id_bq')->get_where('so_cutting_header',array('sts_confirm'=>'Y','sts_closing'=>'N'))->result_array();
        
		$data = array(
			'title'			=> 'WIP Pipe Cutting',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'data_ipp'		=> $data_ipp,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data wip pipe cutting');
		$this->load->view('Wip_pipe_cutting/index',$data);
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

            $sts_confirm = ($row['sts_closing'] == 'Y')?"<span class='badge bg-green'>Closing</span>":"<span class='badge bg-blue'>Waiting</span>";
            $nestedData[]	= "<div align='center'>".$sts_confirm."</div>";

            $cutting = "";
            $lock = "";
            $edit = "";
            $print = "";
            $view = "";

				// if($row['app'] == 'Y' AND $row['sts_confirm'] == 'N'){
					$cutting = "&nbsp;<a href='".base_url('wip_pipe_cutting/confirm/'.$row['id'])."' class='btn btn-sm btn-success' title='Closing'><i class='fa fa-check'></i></a>";
                    // $print = "&nbsp;<a href='".base_url('ppic/print_cutting/'.$row['id'])."' target='_blank' class='btn btn-sm btn-warning' title='Print SPK Cutting'><i class='fa fa-print'></i></a>";
				// }
				// if($row['cutting'] == 'Y'){
					$view = "<a href='".base_url('wip_pipe_cutting/detail/'.$row['id'])."' class='btn btn-sm btn-default' title='View'><i class='fa fa-eye'></i></a>";
				// }

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
		    WHERE  1=1 ".$where." ".$where2." AND a.cutting = 'Y' AND a.app = 'Y' AND a.sts_confirm = 'Y' AND a.sts_closing = 'N' AND(
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

		$sql .= " ORDER BY a.confirm_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
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
		$this->load->view('Wip_pipe_cutting/detail',$data);
	}

    public function confirm($id=null){
        if($this->input->post()){
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

            $id = $data['id'];

            $ArrUpdate = [
                'sts_closing' => 'Y',
                'closing_by' => $data_session['ORI_User']['username'],
                'closing_date' => $dateTime
            ];

			//New Data
			$get_header 	= $this->db->get_where('so_cutting_header', array('id'=>$id))->result();
			if(empty($get_header[0]->id_deadstok)){
				$id_produksi 	= str_replace('BQ-','PRO-',$get_header[0]->id_bq);
				$id_milik 		= $get_header[0]->id_milik;

				$urut_nomor = $get_header[0]->qty_ke;
				$nomor_so = get_nomor_so(str_replace('BQ-','',$get_header[0]->id_bq));
				$kode_urut = substr(get_name('so_detail_header','no_komponen','id',$id_milik),-3);

				$nomor_spk = get_name('so_detail_header','no_spk','id',$id_milik);
				$product_code = $nomor_so.'-'.$kode_urut.'.'.$urut_nomor;

				$getDataPro = $this->db
										->select('id')
										->get_where('production_detail',array(
											'id_produksi' => $id_produksi,
											'id_milik' => $id_milik,
											'product_ke' => $urut_nomor
										))
										->result();
				$ID_proDet 		= (!empty($getDataPro[0]->id))?$getDataPro[0]->id:0;
				$getAmountFG	= $this->db
										->select('nilai_unit AS finish_good')
										->get_where('data_erp_fg',array(
											'id_pro_det' => $ID_proDet,
											'jenis' => 'in'
										))
										->result();
				$nilaiFGLoose 	= (!empty($getAmountFG[0]->finish_good))?$getAmountFG[0]->finish_good:0;

				$getCuttingList = $this->db->get_where('so_cutting_detail', array('id_header'=>$id))->result_array();
				$getCuttingSum 	= $this->db->select('SUM(length_split) AS total_cutting')->get_where('so_cutting_detail', array('id_header'=>$id))->result();
				$panjangCutting = (!empty($getCuttingSum[0]->total_cutting))?$getCuttingSum[0]->total_cutting:0;
			}
			else{
				$getAmountFG = $this->db
										->select('finish_good')
										->get_where('deadstok',array(
											'id' => $get_header[0]->id_deadstok
										))
										->result();
				$nilaiFGLoose = (!empty($getAmountFG[0]->finish_good))?$getAmountFG[0]->finish_good:0;

				$getCuttingList = $this->db->get_where('so_cutting_detail', array('id_header'=>$id))->result_array();
				$getCuttingSum 	= $this->db->select('SUM(length_split) AS total_cutting')->get_where('so_cutting_detail', array('id_header'=>$id))->result();
				$panjangCutting = (!empty($getCuttingSum[0]->total_cutting))?$getCuttingSum[0]->total_cutting:0;
				$ID_proDet = $id;
			}

			$ArrCutting = [];
			$ArrHistFG = [];
			foreach ($getCuttingList as $key => $value) {
				$FinishGood = 0;
				if($value['length_split'] > 0 AND $value['length'] > 0 AND $nilaiFGLoose > 0 AND $panjangCutting > 0){
					$FinishGood = $value['length_split'] / $panjangCutting * $nilaiFGLoose;
				}
				$ArrCutting[$key]['id'] 			= $value['id'];
				$ArrCutting[$key]['finish_good'] 	= $FinishGood;

				$ArrHistFG[$key]['tipe_product'] = 'cutting';
				$ArrHistFG[$key]['id_product'] = $value['id'];
				$ArrHistFG[$key]['id_milik'] = $value['id_milik'];
				$ArrHistFG[$key]['tipe'] = 'in';
				$ArrHistFG[$key]['kode'] = $value['id_header'];
				$ArrHistFG[$key]['tanggal'] = date('Y-m-d');
				$ArrHistFG[$key]['keterangan'] = 'lock pipe cutting';
				$ArrHistFG[$key]['hist_by'] = $data_session['ORI_User']['username'];
				$ArrHistFG[$key]['hist_date'] = $dateTime;
			}
			
			$this->db->trans_start();
                $this->db->where('id',$id);
                $this->db->update('so_cutting_header', $ArrUpdate);

				if (!empty($ArrHistFG)) {
					$this->db->insert_batch('history_product_fg', $ArrHistFG);
				}

				if(empty($get_header[0]->id_deadstok)){
					$this->db->where('id_produksi', $id_produksi);
					$this->db->where('id_milik', $id_milik);
					$this->db->where('product_ke', $urut_nomor);
					$this->db->update('production_detail', array('no_spk'=>$nomor_spk,'product_code_cut'=>$product_code,'urut_product_cut'=>$urut_nomor));
				}
			
				if(!empty($ArrCutting)){
					$this->db->update_batch('so_cutting_detail', $ArrCutting, 'id');

					insert_jurnal_cutting($ArrCutting, $id); // id = id header cutting 
					$this->jurnalOuttoWipcutting($ID_proDet);

					
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
				history('Closing spk cutting '.$id);
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
                'title'			=> 'Confirm Cutting Pipe',
                'action'		=> 'index',
                'detail'		=> $detail,
                'row_group'		=> $data_Group,
                'akses_menu'	=> $Arr_Akses
            );
            $this->load->view('Wip_pipe_cutting/confirm',$data);
        }
	}


	function jurnalOuttoWipcutting($kode){
		
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		$Date		    = date('Y-m-d'); 
		
		
	        

			$wip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_wip as finishgood  FROM data_erp_wip_group WHERE id_pro_det ='".$kode."' AND tanggal ='".$Date."' AND jenis LIKE 'in cutting%'")->result();
			
            if(!empty($wip)){
            $jurnalwip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_wip as finishgood  FROM data_erp_wip_group WHERE id_pro_det ='".$kode."' AND tanggal ='".$Date."' AND jenis LIKE 'in cutting%'")->result();
			
			} else {
             $jurnalwip = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_wip as finishgood  FROM data_erp_wip_group WHERE id_trans ='".$kode."' AND tanggal ='".$Date."' AND jenis LIKE 'in cutting%'")->result();
			}

			$totalwip =0;
			  
			$det_Jurnaltes = [];
			  
			foreach($jurnalwip AS $data){
				
				$idtrans = $data->id_trans;

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
				
				$totalwip        = $finishgood;
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
					  'no_reff'       => $idtrans,
					  'debet'         => $finishgood,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'Finishgood To WIP Cutting',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 ); 			
				
			}
			
		   
			$fg = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE id_pro ='".$kode."' AND tanggal ='".$Date."' AND jenis LIKE 'out cutting%'")->result();
			
			if(!empty($fg)){
            $jurnalfg = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE id_pro ='".$kode."' AND tanggal ='".$Date."' AND jenis LIKE 'out cutting%'")->result();
			
			} else {
             $jurnalfg = $this->db->query("SELECT tanggal,keterangan,product,no_so,no_spk,id_trans, nilai_wip as wip, material as material, wip_direct as wip_direct, wip_indirect as wip_indirect,  wip_foh as wip_foh, wip_consumable as wip_consumable, nilai_unit as finishgood  FROM data_erp_fg WHERE id_trans ='".$kode."' AND tanggal ='".$Date."' AND jenis LIKE 'out cutting%'")->result();
			}
			  
			foreach($jurnalfg AS $data){
				
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
				$idtrans = $data->id_trans;
				
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
					  'no_reff'       => $idtrans,
					  'debet'         => 0,
					  'kredit'        => $finishgood,
					  'jenis_jurnal'  => 'Finishgood To WIP Cutting',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					  
					 ); 			
				
			}

			print_r($det_Jurnaltes);
			exit;

			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='finishgood part to WIP' and no_reff ='$kode' AND tanggal ='".$Date."'"); 
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'Finishgood To WIP Cutting'.$keterangan;
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