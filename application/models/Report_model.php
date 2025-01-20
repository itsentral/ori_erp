<?php
class Report_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

    public function quotation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/quotation";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$menu_akses	    = $this->master_model->getMenu();
        $ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
		
		$data = array(
			'title'			=> 'Report Quotation',
			'action'		=> 'quotation',
			'data_menu'		=> $menu_akses,
            'cust'			=> $ListCustomer,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data report quotation');
		$this->load->view('Report/quotation',$data);
	}

    public function server_side_quotation(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/quotation";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_quotation(
            $requestData['cust'],
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
			$nestedData[]	= "<div align='right'>".number_format(get_total_by_revised($row['id_bq'])['total_project'],2)."</div>";
					
                $approve_new    = "<button type='button' class='btn btn-sm btn-success detail' title='Detail' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
                $detail_cost 	= "&nbsp;<a href='".site_url('report_costing/excel_report_costing/'.$row['id_bq'])."' target='_blank' data-no_ipp='".$row['id_bq']."' class='btn btn-sm btn-primary' title='Report Costing' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
            $nestedData[]	= "<div align='center'>
									".$approve_new.$detail_cost."
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

	public function query_data_json_quotation($cust, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

        $where_cust = '';
        if($cust <> '0'){
            $where_cust = " AND b.id_customer = '".$cust."' ";
        }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.ref_quo,
				b.status AS sts_ipp,
				b.sts_price_quo
			FROM
				bq_header a 
				LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 
                    b.status IN (
                        'ALREADY ESTIMATED PRICE',
                        'WAITING SALES ORDER',
                        'WAITING APPROVE SO',
                        'ALREADY SALES ORDER',
                        'WAITING FINAL DRAWING',
                        'WAITING APPROVE FINAL DRAWING',
                        'ALREADY FINAL DRAWING',
                        'PARTIAL PROCESS',
                        'WAITING MATERIAL PLANNING',
                        'WAITING PRODUCTION',
                        'PROCESS PRODUCTION',
                        'FINISH'
                    )
                ".$where_cust."
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
            2 => 'nm_customer',
            3 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function detail_quo(){
		$id_bq = $this->uri->segment(3); 
		
		//NEW APPROVED
		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$get_resin_pipa = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','pipa')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrResinPipa[] = $value['nm_material'];
		}
		$ArrHargaPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrHargaPipa[] = $value['price_mat'];
		}
		$resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrResinPipa):'#';
		$harga_resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrHargaPipa):'#';
		
		$get_resin_flange = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','flange')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrResinFlange[] = $value['nm_material'];
		}
		$ArrHargaFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrHargaFlange[] = $value['price_mat'];
		}
		$resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrResinFlange):'#';
		$harga_resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrHargaFlange):'#';
		
		$get_resin_fitting = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing',NULL)
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrResinFitting[] = $value['nm_material'];
		}
		$ArrHargaFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrHargaFitting[] = $value['price_mat'];
		}
		$resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrResinFitting):'#';
		$harga_resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrHargaFitting):'#';
		
		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrResinBW[] = $value['nm_material'];
		}
		$ArrHargaBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrHargaBW[] = $value['price_mat'];
		}
		$resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrResinBW):'#';
		$harga_resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrHargaBW):'#';
		
		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrResinField[] = $value['nm_material'];
		}
		$ArrHargaField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrHargaField[] = $value['price_mat'];
		}
		$resin_field = (!empty($get_resin_field))?implode('<br>',$ArrResinField):'#';
		$harga_resin_field = (!empty($get_resin_field))?implode('<br>',$ArrHargaField):'#';
		
		$data = array(
			'id_bq'			=> $id_bq,
			'revised_no'	=> $revised_no,
			'resin_pipa'			=> $resin_pipa,
			'harga_resin_pipa'		=> $harga_resin_pipa,
			'resin_flange'			=> $resin_flange,
			'harga_resin_flange'	=> $harga_resin_flange,
			'resin_fitting'			=> $resin_fitting,
			'harga_resin_fitting'	=> $harga_resin_fitting,
			'resin_bw'				=> $resin_bw,
			'harga_resin_bw'		=> $harga_resin_bw,
			'resin_field'			=> $resin_field,
			'harga_resin_field'		=> $harga_resin_field
		);

		$this->load->view('Report/detail_quo', $data);
	}

	//SALES ORDER
	public function sales_order(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/sales_order";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$menu_akses	    = $this->master_model->getMenu();
        $ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
		
		$data = array(
			'title'			=> 'Report Sales Order',
			'action'		=> 'sales_order',
			'data_menu'		=> $menu_akses,
            'cust'			=> $ListCustomer,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data report sales_order');
		$this->load->view('Report/sales_order',$data);
	}

    public function server_side_sales_order(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/sales_order";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_sales_order(
            $requestData['cust'],
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
            $nestedData[]	= "<div align='center'><span class='badge bg-blue'>".strtoupper(strtolower($row['ref_quo']))."</span></div>";
			$nestedData[]	= "<div align='right'>".number_format(get_total_by_revised($row['id_bq'])['total_project'],2)."</div>";

				$get_deal = $this->db->select('total_deal_usd')->get_where('billing_so',array('no_ipp'=>$row['no_ipp']))->result();
				$deal_usd = (!empty($get_deal))?$get_deal[0]->total_deal_usd:0;
			$nestedData[]	= "<div align='right'>".number_format($deal_usd,2)."</div>";

                $approve_new    = "<button type='button' class='btn btn-sm btn-success detail' title='Detail' data-id_bq='".$row['id_bq']."'><i class='fa fa-eye'></i></button>";
                $detail_cost 	= "&nbsp;<a href='".site_url('report_costing/excel_report_costing_so/'.$row['id_bq'])."' target='_blank' data-no_ipp='".$row['id_bq']."' class='btn btn-sm btn-primary' title='Report Costing' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
            $nestedData[]	= "<div align='center'>
									".$approve_new.$detail_cost."
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

	public function query_data_json_sales_order($cust, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

        $where_cust = '';
        if($cust <> '0'){
            $where_cust = " AND b.id_customer = '".$cust."' ";
        }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_customer,
				b.project,
				b.ref_quo,
				b.status AS sts_ipp,
				b.sts_price_quo,
				c.so_number
			FROM
				bq_header a 
				LEFT JOIN so_number c ON a.id_bq = c.id_bq
				LEFT JOIN production b ON a.no_ipp = b.no_ipp,
				(SELECT @row:=0) r
		    WHERE 
                    b.status IN (
                        'WAITING FINAL DRAWING',
                        'WAITING APPROVE FINAL DRAWING',
                        'ALREADY FINAL DRAWING',
                        'PARTIAL PROCESS',
                        'WAITING MATERIAL PLANNING',
                        'WAITING PRODUCTION',
                        'PROCESS PRODUCTION',
                        'FINISH'
                    )
                ".$where_cust."
				AND (
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.ket LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
            2 => 'nm_customer',
            3 => 'project',
            4 => 'so_number'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function detail_so(){
		$id_bq = $this->uri->segment(3); 
		$no_ipp = str_replace('BQ-','',$this->uri->segment(3)); 
		
		//NEW APPROVED
		$get_revisi_max = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>$id_bq))->result();
		$revised_no = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

		$get_resin_pipa = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','pipa')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrResinPipa[] = $value['nm_material'];
		}
		$ArrHargaPipa = [];
		foreach ($get_resin_pipa as $key => $value) {
			$ArrHargaPipa[] = $value['price_mat'];
		}
		$resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrResinPipa):'#';
		$harga_resin_pipa = (!empty($get_resin_pipa))?implode('<br>',$ArrHargaPipa):'#';
		
		$get_resin_flange = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','flange')
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrResinFlange[] = $value['nm_material'];
		}
		$ArrHargaFlange = [];
		foreach ($get_resin_flange as $key => $value) {
			$ArrHargaFlange[] = $value['price_mat'];
		}
		$resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrResinFlange):'#';
		$harga_resin_flange = (!empty($get_resin_flange))?implode('<br>',$ArrHargaFlange):'#';
		
		$get_resin_fitting = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing',NULL)
						->where('c.id_category','TYP-0001')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrResinFitting[] = $value['nm_material'];
		}
		$ArrHargaFitting = [];
		foreach ($get_resin_fitting as $key => $value) {
			$ArrHargaFitting[] = $value['price_mat'];
		}
		$resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrResinFitting):'#';
		$harga_resin_fitting = (!empty($get_resin_fitting))?implode('<br>',$ArrHargaFitting):'#';
		
		$get_resin_bw = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','bw')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrResinBW[] = $value['nm_material'];
		}
		$ArrHargaBW = [];
		foreach ($get_resin_bw as $key => $value) {
			$ArrHargaBW[] = $value['price_mat'];
		}
		$resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrResinBW):'#';
		$harga_resin_bw = (!empty($get_resin_bw))?implode('<br>',$ArrHargaBW):'#';
		
		$get_resin_field = $this->db
		  				->select('c.nm_material, c.price_mat')
						->from('bq_detail_header a')
						->join('product_parent b','a.id_category = b.product_parent','left')
						->join('bq_component_detail c','a.id = c.id_milik','left')
						->where('a.id_bq',$id_bq)
						->where('b.type_costing','field')
						->where('c.id_category','TYP-0001')
						->where('c.id_material <>','0')
						->group_by('c.id_material')
						->get()
						->result_array();
		$ArrResinField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrResinField[] = $value['nm_material'];
		}
		$ArrHargaField = [];
		foreach ($get_resin_field as $key => $value) {
			$ArrHargaField[] = $value['price_mat'];
		}
		$resin_field = (!empty($get_resin_field))?implode('<br>',$ArrResinField):'#';
		$harga_resin_field = (!empty($get_resin_field))?implode('<br>',$ArrHargaField):'#';

		$get_deal = $this->db->select('total_deal_usd')->get_where('billing_so',array('no_ipp'=>$no_ipp))->result();
		$deal_usd = (!empty($get_deal))?$get_deal[0]->total_deal_usd:0;
		
		$data = array(
			'id_bq'			=> $id_bq,
			'revised_no'	=> $revised_no,
			'resin_pipa'			=> $resin_pipa,
			'harga_resin_pipa'		=> $harga_resin_pipa,
			'resin_flange'			=> $resin_flange,
			'harga_resin_flange'	=> $harga_resin_flange,
			'resin_fitting'			=> $resin_fitting,
			'harga_resin_fitting'	=> $harga_resin_fitting,
			'resin_bw'				=> $resin_bw,
			'harga_resin_bw'		=> $harga_resin_bw,
			'resin_field'			=> $resin_field,
			'harga_resin_field'		=> $harga_resin_field,
			'deal_usd'				=> $deal_usd
		);

		$this->load->view('Report/detail_so', $data);
	}

	//history_revisi
	public function history_revisi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/history_revisi";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$menu_akses	    = $this->master_model->getMenu();
        $ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();
		
		$data = array(
			'title'			=> 'History Revisi',
			'action'		=> 'history_revisi',
			'data_menu'		=> $menu_akses,
            'cust'			=> $ListCustomer,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data report history_revisi');
		$this->load->view('Report/history_revisi',$data);
	}

    public function server_side_history_revisi(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/history_revisi";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_history_revisi(
            $requestData['cust'],
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$get_rev_est = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_costing_header',array('id_bq'=>'BQ-'.$row['no_ipp']))->result();
			$get_rev_cos = $this->db->select('MAX(revised_no) AS revised')->get_where('laporan_revised_header',array('id_bq'=>'BQ-'.$row['no_ipp']))->result();

			$rev_est = (!empty($get_rev_est))?$get_rev_est[0]->revised:0;
			$rev_cos = (!empty($get_rev_cos))?$get_rev_cos[0]->revised:0;

			$rev_quo = "";
			if($get_rev_cos[0]->revised != null){
				$rev_quo = "<span class='text-yellow font-cs detail_quo' data-id_bq='BQ-".$row['no_ipp']."'><b>".$row['ref_quo']."</b></span>";
			}

			$rev_so = "";
			if($get_rev_cos[0]->revised != null){
				$rev_so = "<span class='text-purple font-cs detail_so' data-id_bq='BQ-".$row['no_ipp']."'><b>0</b></span>";
			}

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
            $nestedData[]	= "<div align='center'><span class='text-blue font-cs detail_est' data-id_bq='BQ-".$row['no_ipp']."'><b>".$rev_est."</b></span></div>";
			$nestedData[]	= "<div align='center'><span class='text-green font-cs detail_cos' data-id_bq='BQ-".$row['no_ipp']."'><b>".$rev_cos."</b></span></div>";
			$nestedData[]	= "<div align='center'>".$rev_quo."</div>";
			$nestedData[]	= "<div align='center'>".$rev_so."</div>";
			
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

	public function query_data_json_history_revisi($cust, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

        $where_cust = '';
        if($cust <> '0'){
            $where_cust = " AND b.id_customer = '".$cust."' ";
        }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				b.*
			FROM
				production b,
				(SELECT @row:=0) r
		    WHERE 
                    b.status NOT IN (
                        'CANCELED',
						'WAITING IPP RELEASE',
                        'CLOSE'
                    )
                ".$where_cust."
				AND (
				b.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
            2 => 'nm_customer',
            3 => 'project'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	//SALES ORDER
	public function hist_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/hist_material";
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$menu_akses	    = $this->master_model->getMenu();
        $ListCustomer	= $this->db->query("SELECT id_material, nm_material FROM raw_materials WHERE `delete` <> 'Y' ORDER BY nm_material ASC")->result_array();
		
		$data = array(
			'title'			=> 'History Price Material',
			'action'		=> 'hist_material',
			'data_menu'		=> $menu_akses,
            'cust'			=> $ListCustomer,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data hist price material');
		$this->load->view('Report/hist_material',$data);
	}

    public function server_side_hist_material(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/hist_material";
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_hist_material(
            $requestData['cust'],
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
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array(); 
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_material']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price_ref_estimation'],3)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['exp_price_ref_est']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['price_from_supplier'],3)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($row['exp_price_ref_sup']))."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['modified_by'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y H:i:s', strtotime($row['modified_date']))."</div>";

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

	public function query_data_json_hist_material($cust, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

        $where_cust = '';
        if($cust <> ''){
            $where_cust = " AND a.id_material = '".$cust."' ";
        }

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				hist_raw_materials a,
				(SELECT @row:=0) r
		    WHERE 1=1
                ".$where_cust."
				AND (
				a.idmaterial LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_material LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'idmaterial',
            2 => 'nm_material'
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function excel_hist_material(){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$id_material = $this->uri->segment(3);

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
				'color' => array('rgb'=>'D9D9D9'),
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
		$Col_Akhir	= $Cols	= getColsChar(8);
		$sheet->setCellValue('A'.$Row, 'HISTORY PRICE REFERENCE & PRICE FROM MATERIAL');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);
		
		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;
		
		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$sheet->setCellValue('B'.$NewRow, 'Nama Material');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		
		$sheet->setCellValue('C'.$NewRow, 'Price Ref.');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		
		$sheet->setCellValue('D'.$NewRow, 'Expired');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		
		$sheet->setCellValue('E'.$NewRow, 'Price From. Sup.');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		
		$sheet->setCellValue('F'.$NewRow, 'Expired');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		
		$sheet->setCellValue('G'.$NewRow, 'Hist By');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		
		$sheet->setCellValue('H'.$NewRow, 'Hist Date');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		
		$where_cust = '';
		if($id_material <> ''){
			$where_cust = " AND a.id_material='".$id_material."' ";
		}
		$sql = "
			SELECT
				a.*
			FROM
				hist_raw_materials a
		    WHERE 1=1
                ".$where_cust."
			ORDER BY a.id ASC
		";
		// echo $sql; exit;
		$result		= $this->db->query($sql)->result_array();
		
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $valx){
				$no++;
				$awal_row++;
				$awal_col	= 0;
				
				
				$awal_col++;
				$nomorx		= $no;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomorx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_bqx		= strtoupper($valx['nm_material']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_bqx);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_category	= $valx['price_ref_estimation'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$id_milik	= $valx['exp_price_ref_est'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_milik);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty	= $valx['price_from_supplier'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
				
				$awal_col++;
				$id_product	= $valx['exp_price_ref_sup'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$qty	= $valx['modified_by'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $qty);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
				$awal_col++;
				$id_product	= $valx['modified_date'];
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_product);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);
				
			}
			
		}
		
		
		history('Download history price supplier '.$id_material);
		
		$sheet->setTitle('History');
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
		header('Content-Disposition: attachment;filename="Price history '.date('YmdHis').'.xls"');
		//unduh file
		$objWriter->save("php://output");
	}


	// SYAMSUDIN 02-01-2025
	public function index_hutangidr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Report >> Hutang IDR ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View AP');
		$this->load->view('Report/hutangidr',$data);
	}

	public function get_data_json_hutangidr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/purchase_order";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_hutangidr(
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
            
			
				$hutang  =	$row['kredit'];
				$dp     =	0;
				$unbill =	0;
				$bayar  =	$row['debet'];
				$saldo =	$hutang - $bayar;
				
			 
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_reff']."</div>";
			$nestedData[]	= "<div align='left'>".$row['tanggal']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nama_supplier']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($hutang,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($bayar,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($saldo,2)."</div>";
			
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

	public function query_data_json_hutangidr($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					sum(a.kredit) as kredit, sum(a.debet) as debet, a.id_supplier, a.nama_supplier, a.no_reff, a.tanggal	FROM
					tr_kartu_hutang a
				WHERE 1=1 AND a.no_perkiraan='2101-01-01'
				AND(
					a.no_reff LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nama_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.no_reff ";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_reff',
			2 => 'nama_supplier'
		);

		$sql .= " ORDER BY a.tanggal DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	// SYAMSUDIN 03-01-2025
	public function index_hutangusd(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Report >> Hutang USD ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View AP');
		$this->load->view('Report/hutangusd',$data);
	}

	public function get_data_json_hutangusd(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/purchase_order";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_hutangusd(
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
            
			
				$hutang  =	$row['kredit'];
				$dp     =	0;
				$unbill =	0;
				$bayar  =	$row['debet'];
				$saldo =	$hutang - $bayar;

				$hutangusd  =	$row['kredit_usd'];
				$dpusd     =	0;
				$unbillusd =	0;
				$bayarusd  =	$row['debet_usd'];
				$saldousd =	$hutangusd - $bayarusd;
				
			 
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_reff']."</div>";
			$nestedData[]	= "<div align='left'>".$row['tanggal']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nama_supplier']."</div>";
			$nestedData[]	= "<div align='right'>".$hutang."</div>";
			$nestedData[]	= "<div align='right'>".$bayar."</div>";
			$nestedData[]	= "<div align='right'>".$saldo."</div>";
			$nestedData[]	= "<div align='right'>".$hutangusd."</div>";
			$nestedData[]	= "<div align='right'>".$bayarusd."</div>";
			$nestedData[]	= "<div align='right'>".$saldousd."</div>";
			
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

	public function query_data_json_hutangusd($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					sum(a.kredit) as kredit, sum(a.debet) as debet, sum(a.kredit_usd) as kredit_usd, sum(a.debet_usd) as debet_usd, a.id_supplier, a.nama_supplier, a.no_reff, a.tanggal	FROM
					tr_kartu_hutang a
				WHERE 1=1 AND a.no_perkiraan='2101-01-04'
				AND(
					a.no_reff LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nama_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.no_reff ";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_reff',
			2 => 'nama_supplier'
		);

		$sql .= " ORDER BY a.tanggal DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	// SYAMSUDIN 20-01-2025
	public function index_unbillidr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Report >>  Unbill IDR ',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View AP');
		$this->load->view('Report/unbill_idr',$data);
	}

	public function get_data_json_unbillidr(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/purchase_order";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_unbillidr(
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
            
			
				$hutang  =	$row['kredit'];
				$dp     =	0;
				$unbill =	0;
				$bayar  =	$row['debet'];
				$saldo =	$hutang - $bayar;
				
			 
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_reff']."</div>";
			$nestedData[]	= "<div align='left'>".$row['tanggal']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nama_supplier']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($hutang,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($bayar,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($saldo,2)."</div>";
			
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
	public function query_data_json_unbillidr($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					sum(a.kredit) as kredit, sum(a.debet) as debet, a.id_supplier, a.nama_supplier, a.no_reff, a.tanggal	FROM
					tr_kartu_hutang a
				WHERE 1=1 AND a.no_perkiraan='2101-01-03'
				AND(
					a.no_reff LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nama_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.no_reff ";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_reff',
			2 => 'nama_supplier'
		);

		$sql .= " ORDER BY a.tanggal DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function index_unbillusd(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Report >> Unbill USD',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View AP');
		$this->load->view('Report/unbill_usd',$data);
	}

	public function get_data_json_unbillusd(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/purchase_order";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_unbillusd(
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
            
			
				$hutang  =	$row['kredit'];
				$dp     =	0;
				$unbill =	0;
				$bayar  =	$row['debet'];
				$saldo =	$hutang - $bayar;
				
			 
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_reff']."</div>";
			$nestedData[]	= "<div align='left'>".$row['tanggal']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nama_supplier']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($hutang,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($bayar,2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($saldo,2)."</div>";
			
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
	public function query_data_json_unbillusd($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					sum(a.kredit) as kredit, sum(a.debet) as debet, a.id_supplier, a.nama_supplier, a.no_reff, a.tanggal	FROM
					tr_kartu_hutang a
				WHERE 1=1 AND a.no_perkiraan='2101-01-05'
				AND(
					a.no_reff LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.nama_supplier LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.no_reff ";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_reff',
			2 => 'nama_supplier'
		);

		$sql .= " ORDER BY a.tanggal DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
}
?>