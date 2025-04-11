<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_revised extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
        if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function report_revised(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Revision Costing Report',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Report Revision Costing');
		$this->load->view('Report_revised/report_revised',$data);
	}
	
	public function report_costing(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Revision Enggenering Report',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Report Revision Enggenering');
		$this->load->view('Report_revised/report_costing',$data);
	}

	public function report_sales_order(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Sales Order Report',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Report Sales Order');
		$this->load->view('Report_revised/report_sales_order',$data);
	}

    public function daily_report(){
		$dateC = date('Y-m-d');
		$date = date('Y-m-d', strtotime('-1 days', strtotime($dateC)));
		// echo $date; exit;
        // $date = date('2020-03-11'); 
        $sqlHeader = "SELECT a.*, b.id_milik FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date)='".$date."' ";
        $restHeader = $this->db->query($sqlHeader)->result_array();
        if(!empty($restHeader)){
            // echo $sqlHeader;
            $ArrDay = array();
            $Sum_est_mat        = 0;
            $Sum_est_harga      = 0;
            $Sum_real_mat       = 0;
            $Sum_real_harga     = 0;
            $Sum_direct_labour  = 0;
            $Sum_in_labour      = 0;
            $Sum_machine        = 0;
            $Sum_mould_mandrill = 0;
            $Sum_consumable     = 0;
            $Sum_foh_consumable = 0;
            $Sum_foh_depresiasi = 0;
            $Sum_by_gaji        = 0;
            $Sum_by_non_pro     = 0;
            $Sum_by_rutin       = 0;
            foreach($restHeader AS $val=>$valx){
                $sqlCh = "SELECT jalur FROM production_header WHERE id_produksi='".$valx['id_produksi']."' ";
                $restCh		= $this->db->query($sqlCh)->result_array();
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
                                WHERE id_milik='".$valx['id_milik']."' LIMIT 1";

                $restBy     = $this->db->query($sqlBy)->result_array();
                
                $sqlBan     = "SELECT * FROM ".$HelpDet2." WHERE id_detail='".$valx['id_production_detail']."' LIMIT 1";
                $restBan    = $this->db->query($sqlBan)->result_array();
                // echo $sqlEst."<br>";
                $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;
                
                $Sum_est_mat        += $restBan[0]['est_material'] * $jumTot;
                $Sum_est_harga      += $restBan[0]['est_harga'] * $jumTot;
                $Sum_real_mat       += $restBan[0]['real_material'];
                $Sum_real_harga     += $restBan[0]['real_harga'];
                $Sum_direct_labour  += $restBy[0]['direct_labour'] * $jumTot;
                $Sum_in_labour      += $restBy[0]['indirect_labour'] * $jumTot;
                $Sum_machine        += $restBy[0]['machine'] * $jumTot;
                $Sum_mould_mandrill += $restBy[0]['mould_mandrill'] * $jumTot;
                $Sum_consumable     += $restBy[0]['consumable'] * $jumTot;
                $Sum_foh_consumable += $restBy[0]['foh_consumable'] * $jumTot;
                $Sum_foh_depresiasi += $restBy[0]['foh_depresiasi'] * $jumTot;
                $Sum_by_gaji        += $restBy[0]['biaya_gaji_non_produksi'] * $jumTot;
                $Sum_by_non_pro     += $restBy[0]['biaya_non_produksi'] * $jumTot;
                $Sum_by_rutin       += $restBy[0]['biaya_rutin_bulanan'] * $jumTot;

                $ArrDay[$val]['id_produksi']            = $valx['id_produksi'];
                $ArrDay[$val]['id_category']            = $valx['id_category'];
                $ArrDay[$val]['id_product']             = $valx['id_product'];
                $ArrDay[$val]['diameter']               = $restBy[0]['diameter'];
                $ArrDay[$val]['diameter2']              = $restBy[0]['diameter2'];
                $ArrDay[$val]['pressure']               = $restBy[0]['pressure'];
                $ArrDay[$val]['liner']                  = $restBy[0]['liner'];
                $ArrDay[$val]['status_date']            = $valx['status_date'];
                $ArrDay[$val]['qty_awal']               = $valx['product_ke'];
                $ArrDay[$val]['qty_akhir']              = $valx['qty_akhir'];
                $ArrDay[$val]['qty']                    = $valx['qty'];
                $ArrDay[$val]['date']                   = $date;
                $ArrDay[$val]['id_production_detail']   = $valx['id_production_detail'];
                $ArrDay[$val]['id_milik']               = $valx['id_milik'];
                $ArrDay[$val]['est_material']           = $restBan[0]['est_material'] * $jumTot;
                $ArrDay[$val]['est_harga']              = $restBan[0]['est_harga'] * $jumTot;
                $ArrDay[$val]['real_material']          = $restBan[0]['real_material'];
                $ArrDay[$val]['real_harga']             = $restBan[0]['real_harga'];

                $ArrDay[$val]['direct_labour']          = $restBy[0]['direct_labour'] * $jumTot;
                $ArrDay[$val]['indirect_labour']        = $restBy[0]['indirect_labour'] * $jumTot;
                $ArrDay[$val]['machine']                = $restBy[0]['machine'] * $jumTot;
                $ArrDay[$val]['mould_mandrill']         = $restBy[0]['mould_mandrill'] * $jumTot;
                $ArrDay[$val]['consumable']             = $restBy[0]['consumable'] * $jumTot;
                $ArrDay[$val]['foh_consumable']         = $restBy[0]['foh_consumable'] * $jumTot;
                $ArrDay[$val]['foh_depresiasi']         = $restBy[0]['foh_depresiasi'] * $jumTot;
                $ArrDay[$val]['biaya_gaji_non_produksi']= $restBy[0]['biaya_gaji_non_produksi'] * $jumTot;
                $ArrDay[$val]['biaya_non_produksi']     = $restBy[0]['biaya_non_produksi'] * $jumTot;
                $ArrDay[$val]['biaya_rutin_bulanan']    = $restBy[0]['biaya_rutin_bulanan'] * $jumTot;

                $ArrDay[$val]['insert_by']              = 'system';
                $ArrDay[$val]['insert_date']            = date('Y-m-d H:i:s');

            }

            $ArrDayMonth = array(
                'date' => $date,
                'est_material' => $Sum_est_mat,
                'est_harga' => $Sum_est_harga,
                'real_material' => $Sum_real_mat,
                'real_harga' => $Sum_real_harga,
                'direct_labour' => $Sum_direct_labour,
                'indirect_labour' => $Sum_in_labour,
                'machine' => $Sum_machine,
                'mould_mandrill' => $Sum_mould_mandrill,
                'consumable' => $Sum_consumable,
                'foh_consumable' => $Sum_foh_consumable,
                'foh_depresiasi' => $Sum_foh_depresiasi,
                'biaya_gaji_non_produksi' => $Sum_by_gaji,
                'biaya_non_produksi' => $Sum_by_non_pro,
                'biaya_rutin_bulanan' => $Sum_by_rutin,
                'insert_by' => 'system',
                'insert_date' => date('Y-m-d H:i:s')

            );

            // echo "<pre>";
            // print_r($ArrDay);
            // print_r($ArrDayMonth);
            // exit;
            $this->db->trans_start();
                $this->db->delete('laporan_per_bulan', array('date' => $date));
                $this->db->delete('laporan_per_hari', array('date' => $date));

                $this->db->insert('laporan_per_bulan', $ArrDayMonth);
                $this->db->insert_batch('laporan_per_hari', $ArrDay);
            $this->db->trans_complete();

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $ArrHistF	= array(
                    'date'			=> $date,
                    'status'		=> 'FAILED',
                    'insert_by'		=> 'system',
                    'insert_date'	=> date('Y-m-d H:i:s')
                );
                $this->db->insert('laporan_status', $ArrHistF);
                echo "Failed Insert Data";
            }
            else{
                $this->db->trans_commit();
                $ArrHistS	= array(
                    'date'			=> $date,
                    'status'		=> 'SUCCESS',
                    'insert_by'		=> 'system',
                    'insert_date'	=> date('Y-m-d H:i:s') 
                );
                $this->db->insert('laporan_status', $ArrHistS);
                echo "Success Insert Data";
            }
        }
        else{
            $ArrHistE	= array(
                'date'			=> $date,
                'status'		=> 'EMPTY',
                'insert_by'		=> 'system',
                'insert_date'	=> date('Y-m-d H:i:s') 
            );
            $this->db->insert('laporan_status', $ArrHistE);
            echo "No Data Insert Data";
        }
    }
	
	public function getDataJSON(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
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
            
            $nestedData 	= array();
            $nestedData[]	= "<div align='center'>".$nomor."</div>";
            $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_project'])."</div>";
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#32a875'><b>".strtoupper($row['max_rev'])."</span></b></div>";
            $view	= "<button type='button' class='btn btn-sm btn-info detail_cos' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			
            $nestedData[]	= "<div align='center'>".$view."</div>";
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
       //MAX(revised_no) AS max_rev
		$sql = "
			SELECT
				*
			FROM
				laporan_revised
		    WHERE 1=1 AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'nm_customer',
            3 => 'nm_project'
			
		);

		$sql .= "ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }
	
	public function getDataJSON_costing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON_costing(
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
            $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_project'])."</div>";
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#32a875'><b>".strtoupper($row['max_rev'])."</span></b></div>";
            $view	= "<button type='button' class='btn btn-sm btn-info detail' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			
            $nestedData[]	= "<div align='center'>".$view."</div>";
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

	public function queryDataJSON_costing($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       //MAX(revised_no) AS max_rev
		$sql = "
			SELECT
				*
			FROM
				laporan_costing
		    WHERE 1=1 AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'nm_customer',
            3 => 'nm_project'
			
		);

		$sql .= "ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }
    
    public function getDataJSONDetail(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail(
            $requestData['id_bq'],
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
			
			$restHelp = $this->db->query("SELECT SUM(unit_price * qty) AS unit_price, SUM(total_price) AS total_price, SUM(total_price_last) AS total_price_last FROM laporan_revised_detail WHERE id_bq='".$row['id_bq']."' AND revised_no='".$row['revised_no']."' ")->result();

            $view	= "<button type='button' class='btn btn-sm btn-info detail_costing' title='Look Data' data-rev='".$row['revised_no']."' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf	= "&nbsp;<a href='".base_url('penawaran/print_penawaran3/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
            $nestedData 	= array();
            $nestedData[]	= "<div align='center'>".$view." ".$pdf."</div>"; 
            $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
            $nestedData[]	= "<div align='center'><b>".strtoupper($row['revised_no'])."</b></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['perubahan'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['revisi'])."</div>";
			
			$nestedData[]	= "<div align='right'>".number_format($row['price_project'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_material'],3)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_harga'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(eng_cost($row['id_bq'], $row['revised_no']),2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(packing_cost($row['id_bq'], $row['revised_no']),2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(truck_cost($row['id_bq'], $row['revised_no']),2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(($restHelp[0]->total_price - $restHelp[0]->unit_price),2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(($restHelp[0]->total_price_last - $restHelp[0]->total_price),2)."</div>";
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

	public function queryDataJSONDetail($id_bq, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				laporan_revised_header
		    WHERE 1=1 AND id_bq='".$id_bq."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'nm_customer',
            3 => 'nm_project'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function getDataJSONDetail_costing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail_costing(
            $requestData['id_bq'],
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
			
			$restHelp = $this->db->query("SELECT SUM(unit_price * qty) AS unit_price, SUM(total_price) AS total_price, SUM(total_price_last) AS total_price_last FROM laporan_revised_detail WHERE id_bq='".$row['id_bq']."' AND revised_no='".$row['revised_no']."' ")->result();
			$pdf	= "";
            $view	= "<button type='button' class='btn btn-sm btn-info detail_eng' title='Look Data' data-rev='".$row['revised_no']."' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			// $pdf	= "&nbsp;<a href='".base_url('penawaran/print_penawaran3/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
            $nestedData 	= array();
            $nestedData[]	= "<div align='center'>".$view." ".$pdf."</div>"; 
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['insert_date']))."</div>";
            $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
            $nestedData[]	= "<div align='center'><b>".strtoupper($row['revised_no'])."</b></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['perubahan'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['revisi'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price_project'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_material'],3)." Kg</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['est_harga'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
			
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

	public function queryDataJSONDetail_costing($id_bq, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				laporan_costing_header
		    WHERE 1=1 AND id_bq='".$id_bq."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'nm_customer',
            3 => 'nm_project'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function getDataJSONDetail2(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail2(
			$requestData['id_bq'],
			$requestData['rev'],
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
            $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf	= "&nbsp;<a href='".base_url('penawaran/print_penawaran3/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
            $nestedData 	= array();
            // $nestedData[]	= "<div align='center'>".$pdf."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['product_parent'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['id_product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['series'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter2'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['qty'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_material'],3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_harga'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price_last'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(($row['total_price'] - ($row['unit_price'] * $row['qty'])),2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format(($row['total_price_last'] - $row['total_price']),2)."</div>";
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

	public function queryDataJSONDetail2($id_bq, $rev, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				laporan_revised_detail
		    WHERE 1=1 AND id_bq='".$id_bq."' AND revised_no='".$rev."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'product_parent'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function getDataJSONDetail2_costing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail2_costing(
			$requestData['id_bq'],
			$requestData['rev'],
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
            $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf	= "&nbsp;<a href='".base_url('penawaran/print_penawaran3/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
            $nestedData 	= array();
            // $nestedData[]	= "<div align='center'>".$pdf."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['product_parent'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['id_product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['series'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter2'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['qty'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_material'],3)." Kg</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['est_harga'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price_last'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
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

	public function queryDataJSONDetail2_costing($id_bq, $rev, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				laporan_costing_detail
		    WHERE 1=1 AND id_bq='".$id_bq."' AND revised_no='".$rev."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'product_parent'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
    
    public function modalDetail(){
		$this->load->view('Report_revised/report_revised_detail');
	}
	
	public function modalDetail_costing(){
		$this->load->view('Report_revised/report_costing_detail');
	}
	
	public function modalDetail2(){
		$this->load->view('Report_revised/report_revised_detail_detail');
    }
	
	public function modalDetail2_costing(){
		$this->load->view('Report_revised/report_costing_detail_detail');
    }
    
    public function excel_lap_bulanan(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$tanggal = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
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
				'color' => array('rgb'=>'e0e0e0'),
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
					  'color' => array('rgb'=>'000000')
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
					  'color' => array('rgb'=>'000000')
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
					  'color' => array('rgb'=>'000000')
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
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(21);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'id_produksi');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'id_category');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'id_product');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'diameter');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(16);
		
		$sheet->setCellValue('E'.$NewRow, 'diameter2');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(16);
		
		$sheet->setCellValue('F'.$NewRow, 'pressure');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(16);
		
		$sheet->setCellValue('G'.$NewRow, 'liner');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(16);
		
		$sheet->setCellValue('H'.$NewRow, 'est_material');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
        $sheet->getColumnDimension('H')->setWidth(16);
        
        $sheet->setCellValue('I'.$NewRow, 'est_harga');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'real_material');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'real_harga');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'direct_labour');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
        $sheet->getColumnDimension('L')->setWidth(16);
        
        $sheet->setCellValue('M'.$NewRow, 'indirect_labour');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
        $sheet->getColumnDimension('M')->setWidth(16);
        
        $sheet->setCellValue('N'.$NewRow, 'machine');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
        $sheet->getColumnDimension('N')->setWidth(16);
        
        $sheet->setCellValue('O'.$NewRow, 'mould_mandrill');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
        $sheet->getColumnDimension('O')->setWidth(16);
        
        $sheet->setCellValue('P'.$NewRow, 'consumable');
		$sheet->getStyle('P'.$NewRow.':P'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('P'.$NewRow.':P'.$NextRow);
        $sheet->getColumnDimension('P')->setWidth(16);
        
        $sheet->setCellValue('Q'.$NewRow, 'foh_consumable');
		$sheet->getStyle('Q'.$NewRow.':Q'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('Q'.$NewRow.':Q'.$NextRow);
        $sheet->getColumnDimension('Q')->setWidth(16);
        
        $sheet->setCellValue('R'.$NewRow, 'foh_depresiasi');
		$sheet->getStyle('R'.$NewRow.':R'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('R'.$NewRow.':R'.$NextRow);
        $sheet->getColumnDimension('R')->setWidth(16);
        
        $sheet->setCellValue('S'.$NewRow, 'biaya_gaji_non_produksi');
		$sheet->getStyle('S'.$NewRow.':S'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('S'.$NewRow.':S'.$NextRow);
        $sheet->getColumnDimension('S')->setWidth(16);
        
        $sheet->setCellValue('T'.$NewRow, 'biaya_non_produksi');
		$sheet->getStyle('T'.$NewRow.':T'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('T'.$NewRow.':T'.$NextRow);
        $sheet->getColumnDimension('T')->setWidth(16);
        
        $sheet->setCellValue('U'.$NewRow, 'biaya_rutin_bulanan');
		$sheet->getStyle('U'.$NewRow.':U'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('U'.$NewRow.':U'.$NextRow);
		$sheet->getColumnDimension('U')->setWidth(16);
		
		$qSupplier	    = "	SELECT * FROM laporan_per_hari WHERE `date` = '".$tanggal."' ORDER BY id_produksi ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $row_Cek['id_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_category	= $row_Cek['id_category'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_product	= $row_Cek['id_product'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$diameter	= $row_Cek['diameter'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$diameter2	= $row_Cek['diameter2'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $diameter2);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$pressure	= $row_Cek['pressure'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $pressure);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$liner	= $row_Cek['liner'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $liner);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$est_material	= $row_Cek['est_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$est_harga	= $row_Cek['est_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$real_material	= $row_Cek['real_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$real_harga	= $row_Cek['real_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle('Report Produksi '.date('d-m-Y', strtotime($tanggal)));
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
		header('Content-Disposition: attachment;filename="Report Produksi '.date('d-m-Y', strtotime($tanggal)).'_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_project(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		// $tanggal = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
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
				'color' => array('rgb'=>'e0e0e0'),
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
					  'color' => array('rgb'=>'000000')
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
					  'color' => array('rgb'=>'000000')
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
					  'color' => array('rgb'=>'000000')
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
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(15);
		$sheet->setCellValue('A'.$Row, 'LAPORAN PRODUKSI PER DAY');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'date');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'est_material');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
        $sheet->getColumnDimension('B')->setWidth(16);
        
        $sheet->setCellValue('C'.$NewRow, 'est_harga');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
        $sheet->getColumnDimension('C')->setWidth(16);
        
        $sheet->setCellValue('D'.$NewRow, 'real_material');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
        $sheet->getColumnDimension('D')->setWidth(16);
        
        $sheet->setCellValue('E'.$NewRow, 'real_harga');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
        $sheet->getColumnDimension('E')->setWidth(16);
        
        $sheet->setCellValue('F'.$NewRow, 'direct_labour');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
        $sheet->getColumnDimension('F')->setWidth(16);
        
        $sheet->setCellValue('G'.$NewRow, 'indirect_labour');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
        $sheet->getColumnDimension('G')->setWidth(16);
        
        $sheet->setCellValue('H'.$NewRow, 'machine');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
        $sheet->getColumnDimension('H')->setWidth(16);
        
        $sheet->setCellValue('I'.$NewRow, 'mould_mandrill');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
        $sheet->getColumnDimension('I')->setWidth(16);
        
        $sheet->setCellValue('J'.$NewRow, 'consumable');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
        $sheet->getColumnDimension('J')->setWidth(16);
        
        $sheet->setCellValue('K'.$NewRow, 'foh_consumable');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
        $sheet->getColumnDimension('K')->setWidth(16);
        
        $sheet->setCellValue('L'.$NewRow, 'foh_depresiasi');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
        $sheet->getColumnDimension('L')->setWidth(16);
        
        $sheet->setCellValue('M'.$NewRow, 'biaya_gaji_non_produksi');
		$sheet->getStyle('M'.$NewRow.':M'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('M'.$NewRow.':M'.$NextRow);
        $sheet->getColumnDimension('M')->setWidth(16);
        
        $sheet->setCellValue('N'.$NewRow, 'biaya_non_produksi');
		$sheet->getStyle('N'.$NewRow.':N'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('N'.$NewRow.':N'.$NextRow);
        $sheet->getColumnDimension('N')->setWidth(16);
        
        $sheet->setCellValue('O'.$NewRow, 'biaya_rutin_bulanan');
		$sheet->getStyle('O'.$NewRow.':O'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('O'.$NewRow.':O'.$NextRow);
		$sheet->getColumnDimension('O')->setWidth(16);
		
		$qSupplier	    = "	SELECT * FROM laporan_per_bulan ORDER BY `date` ASC ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $row_Cek['date'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['est_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$est_harga	= $row_Cek['est_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$real_material	= $row_Cek['real_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$real_harga	= $row_Cek['real_harga'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $real_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$direct_labour	= $row_Cek['direct_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $direct_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$indirect_labour	= $row_Cek['indirect_labour'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $indirect_labour);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$machine	= $row_Cek['machine'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $machine);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$mould_mandrill	= $row_Cek['mould_mandrill'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $mould_mandrill);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$consumable	= $row_Cek['consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$foh_consumable	= $row_Cek['foh_consumable'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_consumable);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$foh_depresiasi	= $row_Cek['foh_depresiasi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $foh_depresiasi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_gaji_non_produksi	= $row_Cek['biaya_gaji_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_gaji_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_non_produksi	= $row_Cek['biaya_non_produksi'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_non_produksi);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
                $awal_col++;
				$biaya_rutin_bulanan	= $row_Cek['biaya_rutin_bulanan'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $biaya_rutin_bulanan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}
		
		
		$sheet->setTitle('Report Produksi Per Day');
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
		header('Content-Disposition: attachment;filename="Report Produksi Per Day '.'_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function excel_material(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_bq = $this->uri->segment(3);

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();
		
		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'e0e0e0'),
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
				'color' => array('rgb'=>'e0e0e0'),
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
					  'color' => array('rgb'=>'000000')
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
					  'color' => array('rgb'=>'000000')
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
					  'color' => array('rgb'=>'000000')
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
		 
		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
		$sheet 		= $objPHPExcel->getActiveSheet();
		
		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(3);
		$sheet->setCellValue('A'.$Row, 'MATERIAL PLANNING '.$id_bq);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Material Name');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
        $sheet->getColumnDimension('B')->setWidth(70);
        
        $sheet->setCellValue('C'.$NewRow, 'Est Mat');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
        $sheet->getColumnDimension('C')->setWidth(16);
		
		$qSupplier 		= "SELECT * FROM so_estimasi_total_material WHERE id_bq='".$id_bq."' ";
		$restDetail1	= $this->db->query($qSupplier)->result_array();
        // echo "<pre>";
        // print_r($restDetail1);        
        // exit;
		
		if($restDetail1){
			$awal_row	= $NextRow;
			$no=0;
			foreach($restDetail1 as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				$awal_col++;
				$id_produksi	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_produksi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$est_material	= $row_Cek['nm_material'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_material);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
                
                $awal_col++;
				$est_harga	= $row_Cek['last_cost'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $est_harga);
                $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
                
			}
		}
		
		
		$sheet->setTitle('Material Planning');
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
		header('Content-Disposition: attachment;filename="Material Planning '.$id_bq.''.'_'.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}
	
	public function getDataJSONSO(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONSO(
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
				$produ = (!empty($row['so_number']))?$row['no_ipp']:$row['no_ipp'];
			$nestedData[]	= "<div align='center'>".$produ."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
				$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = 'BQ-".$row['no_ipp']."' GROUP BY series")->result_array();
				$dtListArray = array();
				foreach($ListBQipp AS $val => $valx){
					$dtListArray[$val] = $valx['series'];
				}
				$dtImplode	= "".implode(", ", $dtListArray)."";
			// $nestedData[]	= "<div align='left'>".strtoupper(strtolower($dtImplode))."</div>";
			// $nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
			// $nestedData[]	= "<div align='right' style='margin-right:15px;'>".number_format($row['sum_sales_order'],2)."</div>";
			// $warna = Color_status($row['status']);
			
			// $nestedData[]	= "<div align='left'><span class='badge' style='background-color:".$warna."'>".$row['status']."</span></div>";
					$viewX	= "<button class='btn btn-sm btn-success detail_so' title='Look Data' data-id_bq='BQ-".$row['no_ipp']."'><i class='fa fa-eye'></i></button>";
		$nestedData[]	= "<div align='center'>
									".$viewX."
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

	public function queryDataJSONSO($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.so_number
			FROM
				table_sales_order a LEFT JOIN so_bf_header b ON a.no_ipp=b.no_ipp
		    WHERE 1=1
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'nm_customer' 
		);

		$sql .= " ORDER BY b.created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function getDataJSONDetailSO(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetailSO(
			$requestData['id_bq'], 
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
            // $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            // $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			// $pdf	= "&nbsp;<a href='".base_url('penawaran/print_penawaran3/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
			$nestedData 	= array();
			$peFOH = $this->db->query("SELECT * FROM so_bf_detail_header WHERE id_milik='".$row['id_milik']."' LIMIT 1")->result();
            // $nestedData[]	= "<div align='center'>".$pdf."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['parent_product'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['id_product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['series'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter_1'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter_2'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['qty'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['sum_mat2'],3)." Kg</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_harga2'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['total_price_last'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['direct_labour'] * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'] * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['machine'] * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'] * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['consumable'] * $row['qty'],2)."</div>";
			$SUMFOH = ($row['direct_labour']+$row['indirect_labour']+$row['machine']+$row['mould_mandrill']+$row['consumable']+$row['est_harga']);
			$nestedData[]	= "<div align='right'>".number_format($SUMFOH * ($peFOH[0]->pe_foh_consumable / 100) * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($SUMFOH * ($peFOH[0]->pe_foh_depresiasi / 100) * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($SUMFOH * ($peFOH[0]->pe_biaya_gaji_non_produksi / 100) * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($SUMFOH * ($peFOH[0]->pe_biaya_non_produksi / 100) * $row['qty'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($SUMFOH * ($peFOH[0]->pe_biaya_rutin_bulanan / 100),2) * $row['qty']."</div>";   
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

	public function queryDataJSONDetailSO($id_bq, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				so_bf_estimasi_cost_and_mat
		    WHERE 1=1 AND id_bq='".$id_bq."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function modalDetailSO(){
		$this->load->view('Report_revised/report_sales_order_detail');
    }
	
	
	//===========================================================================================================================
	//=====================================================REPORT QUOTATION======================================================
	//===========================================================================================================================
	public function report_quotation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		// echo $controller;
		// print_r($Arr_Akses); exit;
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Revision Quotation Report',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Report Revision Quotation');
		$this->load->view('Report_revised/report_quotation',$data);
	}
	
	public function modal_detail_quotation(){
		$this->load->view('Report_revised/report_quotation_detail');
	}
	
	public function modal_detail_quotation_detail(){
		$this->load->view('Report_revised/report_quotation_detail_detail');
    }
    
	public function getDataJSON_quo(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON_quo(
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
            $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_project'])."</div>";
			$nestedData[]	= "<div align='center'><span class='badge' style='background-color:#32a875'><b>".strtoupper($row['max_rev'])."</span></b></div>";
            $view	= "<button type='button' class='btn btn-sm btn-info detail_quo' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			
            $nestedData[]	= "<div align='center'>".$view."</div>";
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

	public function queryDataJSON_quo($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       //MAX(revised_no) AS max_rev
		$sql = "
			SELECT
				*
			FROM
				laporan_revised
		    WHERE 1=1 AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'nm_customer',
            3 => 'nm_project'
			
		);

		$sql .= "ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
    }
	
	public function getDataJSONDetail_quo(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail_quo(
            $requestData['id_bq'],
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
			
			$restHelp = $this->db->query("SELECT SUM(unit_price * qty) AS unit_price, SUM(total_price) AS total_price, SUM(total_price_last) AS total_price_last FROM laporan_revised_detail WHERE id_bq='".$row['id_bq']."' AND revised_no='".$row['revised_no']."' ")->result();
			$view	= "<button type='button' class='btn btn-sm btn-info detail_quotation' title='Look Data' data-rev='".$row['revised_no']."' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf	= "&nbsp;<a href='".base_url('report_revised/hist_print_quotation/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
            $nestedData 	= array();
            $nestedData[]	= "<div align='center'>".$view." ".$pdf."</div>"; 
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['insert_date']))."</div>";
            $nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
            $nestedData[]	= "<div align='center'><b>".strtoupper($row['revised_no'])."</b></div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['perubahan'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['revisi'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price_project'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_material'],3)." Kg</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['est_harga'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
			
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

	public function queryDataJSONDetail_quo($id_bq, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				laporan_revised_header
		    WHERE 1=1 AND id_bq='".$id_bq."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR nm_project LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'nm_customer',
            3 => 'nm_project'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function getDataJSONDetail2_quo(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSONDetail2_quo(
			$requestData['id_bq'],
			$requestData['rev'],
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
            $view	= "<button type='button' class='btn btn-sm btn-warning' id='detail' title='Look Data' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
			// $pdf	= "&nbsp;<a href='".base_url('cron/pfd_lap_bulanan/'.$row['date'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Sales Order' ><i class='fa fa-print'></i></a>";
            $excel	= "&nbsp;<a href='".base_url('cron/excel_lap_bulanan/'.$row['id_bq'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Report Produksi' ><i class='fa fa-file-excel-o'></i></a>";
			$pdf	= "&nbsp;<a href='".base_url('penawaran/print_penawaran3/'.$row['id_bq'].'/'.$row['revised_no'])."' target='_blank' class='btn btn-sm btn-success'  title='Print Penawaran Revised' ><i class='fa fa-print'></i></a>";
            $nestedData 	= array();
            // $nestedData[]	= "<div align='center'>".$pdf."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['product_parent'])."</div>";
            $nestedData[]	= "<div align='left'>".strtoupper($row['id_product'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['series'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter'])."</div>";
			$nestedData[]	= "<div align='right'>".strtoupper($row['diameter2'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['qty'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_material'],3)." Kg</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['est_harga'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['total_price'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_price_last'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['direct_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['indirect_labour'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['machine'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['mould_mandrill'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_consumable'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['foh_depresiasi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_gaji_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_non_produksi'],2)."</div>";
			// $nestedData[]	= "<div align='right'>".number_format($row['biaya_rutin_bulanan'],2)."</div>";
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

	public function queryDataJSONDetail2_quo($id_bq, $rev, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
       
		$sql = "
			SELECT
				*
			FROM
				laporan_revised_detail
		    WHERE 1=1 AND id_bq='".$id_bq."' AND revised_no='".$rev."' AND (
				id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
            1 => 'id_bq',
            2 => 'product_parent'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
    
	public function hist_print_quotation(){
		$id_bq	= $this->uri->segment(3);
		$rev	= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'id_bq' => $id_bq,
			'rev'	=> $rev
		);
		history('Print history penawaran '.$id_bq.', Rev.'.$rev);
		$this->load->view('Print/print_hist_penawaran', $data);
	}
    

}