<?php
class Invoicing_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//=========================================================================================================================
	//==================================================PLAN TAGIH=============================================================
	//=========================================================================================================================

	public function create_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))); 
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT create_by, create_date FROM table_sales_order ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$ListIPP 			= $this->db->query("SELECT * FROM so_bf_header ORDER BY no_ipp ASC")->result_array();

		$list_so = $this->db->query("SELECT
										b.so_number
									FROM
										billing_top a
										LEFT JOIN so_bf_header b ON a.no_po=b.no_ipp GROUP BY b.so_number")->result_array();
		$list_cust = $this->db->query("SELECT
										c.id_customer,
										c.nm_customer
									FROM
										billing_top a
										LEFT JOIN so_bf_header b ON a.no_po=b.no_ipp
										LEFT JOIN production c ON a.no_po=c.no_ipp GROUP BY c.id_customer")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Plan Tagih',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP,
			'list_so'		=> $list_so,
			'list_cust'		=> $list_cust

		);
		history('View data plan tagih');
		$this->load->view('Invoicing/create_new',$data);
	}

	public function get_data_json_create_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_create_new(
			$requestData['no_so'],
			$requestData['customer'],
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
			// print_r($row);
			// exit;

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
				$produ = (!empty($row['so_number']))?$row['so_number']:$row['no_po'];
				$no_Po = (!empty($row['no_po2']))?$row['no_po2']:'-';
			$nestedData[]	= "<div align='left'>".$produ."</div>";
			$nestedData[]	= "<div align='left'>".$no_Po."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['keterangan'])."</div>";
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['jatuh_tempo']))."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['value_usd'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['value_idr'],2)."</div>";
			$nestedData[]	= "<div align='left'><span class='badge'>".$row['group_top']."</span></div>";
					$viewX	= "<button class='btn btn-sm btn-warning create_invoice' title='Create Invoice' data-id_bq='".$row['id']."'><i class='fa fa-list'></i></button>";

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

	public function query_data_create_new($no_so, $customer, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_so = "";
		if($no_so <> '0'){
			$where_no_so = " AND b.so_number='".$no_so."' ";
		}

		$where_cust = "";
		if($customer <> '0'){
			$where_cust = " AND c.id_customer='".$customer."' ";
		}
		//(a.proses_inv='0' AND a.group_top <>'progress') OR (d.persentase_progress <> '100' AND a.group_top ='progress')
		$sql = "
			SELECT
				a.*,
				b.so_number,
				c.nm_customer,
				d.project,
				d.no_po AS no_po2
			FROM
				billing_top a
				LEFT JOIN so_bf_header b ON a.no_po=b.no_ipp
				LEFT JOIN production c ON a.no_po=c.no_ipp
				LEFT JOIN billing_so d ON a.no_po=d.no_ipp

		    WHERE 1=1 ".$where_no_so." ".$where_cust."
					AND ((a.proses_inv = '0' AND a.group_top <>'progress')
					OR (d.persentase_progress <> '100' AND a.group_top ='progress'))

				AND (
				a.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR d.no_po LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR d.project LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.jatuh_tempo LIKE '%".$this->db->escape_like_str($like_value)."%'

	        )


		";
		// echo $sql; exit;
		// OR a.project LIKE '%".$this->db->escape_like_str($like_value)."%'
		// OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_po',
			2 => 'no_po2',
			3 => 'project',
			4 => 'nm_customer',
			5 => 'keterangan',
			6 => 'jatuh_tempo',
			7 => 'value_usd',
			8 => 'value_idr'

		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function create_invoice(){
		$id    = $this->uri->segment(3);
		$getBq = $this->db->query("SELECT * FROM billing_top WHERE id='$id'")->row();

	    $jenis  = $getBq->group_top;
		$id_bq1 = $getBq->no_po;
		$id_bq  ='BQ-'.$id_bq1;

		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$getHeader	= $this->db->query($qSupplier)->result();

		$qMatr 		= "SELECT a.* FROM billing_so_product a
						WHERE
							a.no_ipp = '".$id_bq1."'
						ORDER BY
							a.id_milik ASC";
		$getDetail	= $this->db->query($qMatr)->result_array();

		$engC 		= "SELECT a.* FROM billing_so_total a WHERE a.no_ipp='".$id_bq1."' ORDER BY a.id ASC ";
		$getEngCost	= $this->db->query($engC)->result_array();

		$engCPC 	= "SELECT a.* FROM billing_so_total a WHERE a.no_ipp='".$id_bq1."' ORDER BY a.id ASC";
		$getPackCost	= $this->db->query($engCPC)->result_array();
		// echo $engCPC;
		$gTruck 	= "SELECT a.* FROM billing_so_total a WHERE a.no_ipp='".$id_bq1."' ORDER BY a.id ASC";
		$getTruck	= $this->db->query($gTruck)->result_array();

		$engCPCV 	= "SELECT
							b.*,
							c.*
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id
						WHERE
							 b.category = 'lokal'
							AND b.id_bq = '".$id_bq."'
							AND b.price_total <> 0
						ORDER BY
							b.id ASC ";
		$getVia	= $this->db->query($engCPCV)->result_array();

		$sql_non_frp 	= "	SELECT
								a.*
							FROM
								billing_so_add a
							WHERE
								a.category='acc'
								AND a.no_ipp='".$id_bq1."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();

		$sql_material 	= "	SELECT
								a.*

							FROM
								billing_so_add a

							WHERE
								a.category='mat'
								AND a.no_ipp='".$id_bq1."' ";
		$material		= $this->db->query($sql_material)->result_array();

		$list_top	= $this->db->get_where('list_help', array('group_by'=>'top invoice'))->result_array();

		$data = array(
			'id_bq' 		=> $id_bq,
			'id' 			=> $id,
			'jenis'			=>$jenis,
			'ipp' 			=> $ipp,
			'getDetail' 	=> $getDetail,
			'getEngCost' 	=> $getEngCost,
			'getPackCost' 	=> $getPackCost,
			'getTruck' 		=> $getTruck,
			'getVia' 		=> $getVia,
			'non_frp'		=> $non_frp,
			'material'		=> $material,
			'list_top'		=> $list_top
		);



		$this->load->view('Invoicing/create_invoice', $data);
	}

	public function save_invoice(){
		$id 					= $this->input->post('id');
		$Tgl_Invoice			= $this->input->post('tanggal_invoice');
		$Bulan_Invoice			= date('n',strtotime($Tgl_Invoice));
		$Tahun_Invoice			= date('Y',strtotime($Tgl_Invoice));
		$data_session 		    = $this->session->userdata;

		$no_ipp                 = $this->input->post('no_ipp');
		$no_invoice 			= $this->invoicing_model->gen_invoice($no_ipp);
		$id_customer			= $this->input->post('id_customer');
		$nm_customer			= $this->input->post('nm_customer');
		$no_bq                  = 'BQ-'.$no_ipp;
		$kurs                   = $this->input->post('kurs');
		$jenis_invoice 			= $this->input->post('jenis_invoice');
		$total_invoice          = $this->input->post('total_invoice_hidden');
		$total_invoice_idr      = $this->input->post('total_invoice_hidden')*$kurs;
		$total_um               = $this->input->post('down_payment_hidden');
		$total_um_idr           = $this->input->post('down_payment_hidden')*$kurs;
		$um_persen				= $this->input->post('um_persen');
		$um_persen2				= $this->input->post('um_persen2');
		$umpersen				= $this->input->post('umpersen');
		$total_gab_product      = ($this->input->post('tot_product_hidden'))+($this->input->post('total_material_hidden'))+($this->input->post('total_bq_nf_hidden'));
		$total_gab_product_idr  = ($this->input->post('tot_product_hidden')*$kurs)+($this->input->post('total_material_hidden')*$kurs)+($this->input->post('total_bq_nf_hidden')*$kurs);

		$retensi_non_ppn 	= str_replace(',','',$this->input->post('potongan_retensi_hidden'));
		$retensi_ppn 		= str_replace(',','',$this->input->post('potongan_retensi_hidden2'));

		$retensi_FIX = 0;

		if($retensi_non_ppn <= 0 ){
			$retensi_FIX = $retensi_ppn;
		}

		if($retensi_ppn <= 0 ){
			$retensi_FIX = $retensi_non_ppn;
		}

		$retensi				=  $retensi_FIX;
		$retensi_idr		    =  $retensi_FIX * $kurs;

		if($jenis_invoice=='uang muka'){
		$progress = $um_persen;
		}
		elseif($jenis_invoice=='progress'){
		$progress = $umpersen;
		}
		else{
		$progress = 0;
		}

		$totaluangmuka2 = 0;
		$totaluangmuka2_idr = 0;

		if($jenis_invoice=='uang muka' && $um_persen2 != 0){
		$totaluangmuka2 = $this->input->post('grand_total_hidden') - $this->input->post('down_payment_hidden');
		$totaluangmuka2_idr = $totaluangmuka2*$kurs;
		}
		if($jenis_invoice=='progress' && $um_persen2 != 0){
		$totaluangmuka2 = $this->input->post('down_payment_hidden2');
		$totaluangmuka2_idr = $totaluangmuka2*$kurs;
		}

		//INSERT DATABASE TR INVOICE HEADER
		$headerinv = array(
		    'id_bq' 		     		=> $no_bq,
		    'no_ipp' 		     		=> $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'persentase' 		        => $progress,
			'total_product'	         	=> $this->input->post('tot_product_hidden'),
			'total_product_idr'	        => $this->input->post('tot_product_hidden')*$kurs,
			'total_gab_product'	        => $total_gab_product,
			'total_gab_product_idr'	    => $total_gab_product_idr,
			'total_material'	        => $this->input->post('total_material_hidden'),
			'total_material_idr'	    => $this->input->post('total_material_hidden')*$kurs,
			'total_bq'	                => $this->input->post('total_bq_nf_hidden'),
			'total_bq_idr'	            => $this->input->post('total_bq_nf_hidden')*$kurs,
			'total_enginering'	        => $this->input->post('total_enginering_hidden'),
			'total_enginering_idr'	    => $this->input->post('total_enginering_hidden')*$kurs,
		    'total_packing'	            => $this->input->post('total_packing_hidden'),
			'total_packing_idr'	        => $this->input->post('total_packing_hidden')*$kurs,
			'total_trucking'	        => $this->input->post('total_trucking_hidden'),
			'total_trucking_idr'	    => $this->input->post('total_trucking_hidden')*$kurs,
			'total_dpp_usd'	            => $this->input->post('grand_total_hidden'),
			'total_dpp_rp'	            => $this->input->post('grand_total_hidden')*$kurs,
			'total_diskon'	            => $this->input->post('diskon_hidden'),
			'total_diskon_idr'	        => $this->input->post('diskon_hidden')*$kurs,
			'total_retensi'	            => $this->input->post('potongan_retensi_hidden'),
			'total_retensi_idr'	        => $this->input->post('potongan_retensi_hidden')*$kurs,
			'total_ppn'	                => $this->input->post('ppn_hidden'),
			'total_ppn_idr'	            => $this->input->post('ppn_hidden')*$kurs,
			'total_invoice'	            => $this->input->post('total_invoice_hidden'),
			'total_invoice_idr'	        => $this->input->post('total_invoice_hidden')*$kurs,
			'total_um'	                => $this->input->post('down_payment_hidden'),
			'total_um_idr'	            => $this->input->post('down_payment_hidden')*$kurs,
			'kurs_jual'	                => $this->input->post('kurs'),
			'no_po'	                    => $this->input->post('nomor_po'),
			'no_faktur'	                => $this->input->post('nomor_faktur'),
			'no_pajak'	                => $this->input->post('nomor_pajak'),
			'payment_term'	            => $this->input->post('top'),
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s'),
			'total_um2'	                => $totaluangmuka2,
			'total_um_idr2'	            => $totaluangmuka2_idr,
			'id_top'	            	=> $id
		);

		$this->db->trans_begin();

		$this->db->insert('tr_invoice_header',$headerinv);


		if($jenis_invoice!='retensi'){


		if(!empty($_POST['data1'])){
		foreach($_POST['data1'] as $d1){


		$nm_material          = $d1['material_name1'];
		$product_cust          = $d1['product_cust'];
		$diameter_1           = $d1['diameter_1'];
		$diameter_2      	  = $d1['diameter_2'];
		$liner                = $d1['liner'];
		$pressure             = $d1['pressure'];
		$id_milik             = $d1['id_milik'];
		$harga_sat_hidden     = $d1['harga_sat_hidden'];
		$qty                  = $d1['qty'];
		$unit1                = $d1['unit1'];
		$harga_tot_hidden     = $d1['harga_tot_hidden'];

			$detailInv1 = array(
		    'id_bq' 		     	    => $no_bq,
		    'no_ipp' 		     	    => $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'nm_material'	         	=> $nm_material,
			'product_cust'	         	=> $product_cust,
			'dim_1'	                    => $diameter_1,
			'dim_2'	                    => $diameter_2,
			'liner'	                    => $liner,
		    'pressure'	                => $pressure,
			'spesifikasi'	            => $id_milik,
			'unit'	                    => $unit1,
			'harga_satuan'	            => $harga_sat_hidden,
			'harga_satuan_idr'	        => $harga_sat_hidden*$kurs,
			'qty'	                    => $qty,
			'harga_total'	            => $harga_tot_hidden,
			'harga_total_idr'	        => $harga_tot_hidden*$kurs,
			'kategori_detail'	        => 'PRODUCT',
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s')

		     );

            $this->db->insert('tr_invoice_detail',$detailInv1);

        }
		}


		if(!empty($_POST['data2'])){

		foreach($_POST['data2'] as $d2){


		$material_name2          = $d2['material_name2'];
		$harga_sat2_hidden    = $d2['harga_sat2_hidden'];
		$qty2                 = $d2['qty2'];
		$unit2                = $d2['unit2'];
		$harga_tot2_hidden     = $d2['harga_tot2_hidden'];

			$detailInv2 = array(
		    'id_bq' 		     	    => $no_bq,
		    'no_ipp' 		     	    => $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'nm_material'	         	=> $material_name2,
			'dim_1'	                    => '-',
			'dim_2'	                    => '-',
			'liner'	                    => '-',
		    'pressure'	                => '-',
			'spesifikasi'	            => '-',
			'unit'	                    => $unit2,
			'harga_satuan'	            => $harga_sat2_hidden,
			'harga_satuan_idr'	        => $harga_sat2_hidden*$kurs,
			'qty'	                    => $qty2,
			'harga_total'	            => $harga_tot2_hidden,
			'harga_total_idr'	        => $harga_tot2_hidden*$kurs,
			'kategori_detail'	        => 'BQ',
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s')

		     );

            $this->db->insert('tr_invoice_detail',$detailInv2);

        }

		}

		if(!empty($_POST['data3'])){

		foreach($_POST['data3'] as $d3){


		$material_name3          = $d3['material_name3'];
		$harga_sat3_hidden       = $d3['harga_sat3_hidden'];
		$qty3                    = $d3['qty3'];
		$unit3                   = $d3['unit3'];
		$harga_tot3_hidden       = $d3['harga_tot3_hidden'];

			$detailInv3 = array(
		    'id_bq' 		     	    => $no_bq,
		    'no_ipp' 		     	    => $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'nm_material'	         	=> $material_name3,
			'dim_1'	                    => '-',
			'dim_2'	                    => '-',
			'liner'	                    => '-',
		    'pressure'	                => '-',
			'spesifikasi'	            => '-',
			'unit'	                    => $unit3,
			'harga_satuan'	            => $harga_sat3_hidden,
			'harga_satuan_idr'	        => $harga_sat3_hidden*$kurs,
			'qty'	                    => $qty3,
			'harga_total'	            => $harga_tot3_hidden,
			'harga_total_idr'	        => $harga_tot3_hidden*$kurs,
			'kategori_detail'	        => 'MATERIAL',
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s')

		     );

            $this->db->insert('tr_invoice_detail',$detailInv3);

        }

		}

		if(!empty($_POST['data4'])){

		foreach($_POST['data4'] as $d4){
		$material_name4          = $d4['material_name4'];
		$harga_sat4_hidden       = 0;
		$qty4                    = 0;
		$unit4                   = $d4['unit4'];
		$harga_tot4_hidden       = $d4['harga_tot4_hidden'];

			$detailInv4 = array(
		    'id_bq' 		     	    => $no_bq,
		    'no_ipp' 		     	    => $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'nm_material'	         	=> $material_name4,
			'dim_1'	                    => '-',
			'dim_2'	                    => '-',
			'liner'	                    => '-',
		    'pressure'	                => '-',
			'spesifikasi'	            => '-',
			'unit'	                    => $unit4,
			'harga_satuan'	            => '0',
			'harga_satuan_idr'	        => '0',
			'qty'	                    => '0',
			'harga_total'	            => $harga_tot4_hidden,
			'harga_total_idr'	        => $harga_tot4_hidden*$kurs,
			'kategori_detail'	        => 'ENGINERING',
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s')

		     );

            $this->db->insert('tr_invoice_detail',$detailInv4);

        }
		}

		if(!empty($_POST['data5'])){

		foreach($_POST['data5'] as $d5){
		$material_name5          = $d5['material_name5'];
		$unit5                   = $d5['unit5'];
		$harga_tot5_hidden       = $d5['harga_tot5_hidden'];

			$detailInv5 = array(
		    'id_bq' 		     	    => $no_bq,
		    'no_ipp' 		     	    => $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'nm_material'	         	=> $material_name5,
			'dim_1'	                    => '-',
			'dim_2'	                    => '-',
			'liner'	                    => '-',
		    'pressure'	                => '-',
			'spesifikasi'	            => '-',
			'unit'	                    => $unit5,
			'harga_satuan'	            => '0',
			'harga_satuan_idr'	        => '0',
			'qty'	                    => '0',
			'harga_total'	            => $harga_tot5_hidden,
			'harga_total_idr'	        => $harga_tot5_hidden*$kurs,
			'kategori_detail'	        => 'PACKING',
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s')

		     );

            $this->db->insert('tr_invoice_detail',$detailInv5);

        }
		}

		if(!empty($_POST['data6'])){
		foreach($_POST['data6'] as $d6){
		$material_name6          = $d6['material_name6'];
		$harga_sat6_hidden       = 0;
		$qty6                    = 0;
		$unit6                   = $d6['unit6'];
		$harga_tot6_hidden       = $d6['harga_tot6_hidden'];

			$detailInv6 = array(
		    'id_bq' 		     	    => $no_bq,
		    'no_ipp' 		     	    => $this->input->post('no_ipp'),
			'so_number' 		     	=> $this->input->post('no_so'),
			'no_invoice' 		     	=> $no_invoice,
			'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
			'id_customer'	 	      	=> $this->input->post('id_customer'),
			'nm_customer' 		      	=> $this->input->post('nm_customer'),
			'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
			'nm_material'	         	=> $material_name6,
			'dim_1'	                    => '-',
			'dim_2'	                    => '-',
			'liner'	                    => '-',
		    'pressure'	                => '-',
			'spesifikasi'	            => '-',
			'unit'	                    => $unit6,
			'harga_satuan'	            => '0',
			'harga_satuan_idr'	        => '0',
			'qty'	                    => '0',
			'harga_total'	            => $harga_tot6_hidden,
			'harga_total_idr'	        => $harga_tot6_hidden*$kurs,
			'kategori_detail'	        => 'TRUCKING',
			'created_by' 	            => $data_session['ORI_User']['username'],
			'created_date' 	            => date('Y-m-d H:i:s')

		     );

            $this->db->insert('tr_invoice_detail',$detailInv6);

        }
		}

		}

		if($jenis_invoice=='retensi'){

			if(!empty($_POST['data8'])){
				foreach($_POST['data8'] as $d8){
				$material_name8          = $d8['material_name8'];
				$harga_sat8_hidden       = 0;
				$qty8                    = 0;
				$unit8                   = $d8['unit8'];
				$harga_tot8_hidden       = $d8['harga_tot8_hidden'];

					$detailInv8 = array(
					'id_bq' 		     	    => $no_bq,
					'no_ipp' 		     	    => $this->input->post('no_ipp'),
					'so_number' 		     	=> $this->input->post('no_so'),
					'no_invoice' 		     	=> $no_invoice,
					'tgl_invoice'      		    => $this->input->post('tanggal_invoice'),
					'id_customer'	 	      	=> $this->input->post('id_customer'),
					'nm_customer' 		      	=> $this->input->post('nm_customer'),
					'jenis_invoice' 		    => $this->input->post('jenis_invoice'),
					'nm_material'	         	=> $material_name8,
					'dim_1'	                    => '-',
					'dim_2'	                    => '-',
					'liner'	                    => '-',
					'pressure'	                => '-',
					'spesifikasi'	            => '-',
					'unit'	                    => $unit8,
					'harga_satuan'	            => '0',
					'harga_satuan_idr'	        => '0',
					'qty'	                    => '0',
					'harga_total'	            => $harga_tot8_hidden,
					'harga_total_idr'	        => $harga_tot8_hidden*$kurs,
					'kategori_detail'	        => 'TRUCKING',
					'created_by' 	            => $data_session['ORI_User']['username'],
					'created_date' 	            => date('Y-m-d H:i:s')

					 );

					$this->db->insert('tr_invoice_detail',$detailInv8);

				}
			}
		}

		if($jenis_invoice=='uang muka' && $um_persen2 < 1){

		$update_um1	 = "UPDATE billing_so SET uang_muka_persen= (uang_muka_persen + $um_persen), uang_muka= (uang_muka + $total_invoice), uang_muka_idr=(uang_muka_idr + $total_invoice_idr) WHERE no_ipp='$no_ipp'";
        $this->db->query($update_um1);

		}

		if($jenis_invoice=='uang muka' && $um_persen2 > 0){

		$update_um1	 = "UPDATE billing_so SET uang_muka_persen2=(uang_muka_persen2 + $um_persen2), uang_muka2=(uang_muka2 + $total_invoice), uang_muka_idr2=(uang_muka_idr2 + $total_invoice_idr), retensi=(retensi + $retensi), retensi_idr=(retensi_idr + $retensi_idr), retensi_um=(retensi_um + $retensi), retensi_um_idr=(retensi_um_idr + $retensi_idr) WHERE no_ipp='$no_ipp'";
        $this->db->query($update_um1);

		}

		if($jenis_invoice=='progress'){

		$update_um2	 = "UPDATE billing_so SET uang_muka= (uang_muka - $total_um), uang_muka_idr=(uang_muka_idr - $total_um_idr), uang_muka_invoice=(uang_muka_invoice + $total_um), uang_muka_invoice_idr=(uang_muka_invoice_idr + $total_um_idr), persentase_progress=$umpersen, retensi= (retensi + $retensi), retensi_idr= (retensi_idr + $retensi_idr) WHERE no_ipp='$no_ipp'";
        $this->db->query($update_um2);

		}

		$update_um	 = "UPDATE billing_top SET proses_inv='1' WHERE id='$id'";
        $this->db->query($update_um);



		//ARWANT
		$ArrUpdateQty = array();
		$nox = 0;
		if(!empty($_POST['data1'])){
			foreach($_POST['data1'] as $d1){ $nox++;
				$ArrUpdateQty[$nox]['id']		= $d1['id'];
				$ArrUpdateQty[$nox]['qty_inv']	= $d1['qty_sudah'] + $d1['qty'];
			}
		}

		$this->db->trans_start();
		
			$this->db->update_batch('billing_so_product', $ArrUpdateQty, 'id');
		
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			 $this->db->trans_rollback();
			 $Arr_Return		= array(
					'status'		=> 2,
					'pesan'			=> 'Save Process Failed. Please Try Again...'
			   );
		}else{
			 $this->db->trans_commit();
			 $Arr_Return		= array(
				'status'		=> 1,
				'pesan'			=> 'Save Process Success. Thank You & Have A Nice Day...'
		   );
		}
		echo json_encode($Arr_Return);
	}

	//=========================================================================================================================
	//====================================================PIUTANG==============================================================
	//=========================================================================================================================

	public function list_inv(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$menu_akses			= $this->master_model->getMenu();
		$getBy				= "SELECT create_by, create_date FROM table_sales_order ORDER BY create_date DESC LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$ListIPP 			= $this->db->query("SELECT * FROM so_bf_header ORDER BY no_ipp ASC")->result_array();
		$list_so = $this->db->query("SELECT
										b.so_number
									FROM
										tr_invoice_header b GROUP BY b.so_number")->result_array();
		$list_cust = $this->db->query("SELECT
										c.id_customer,
										c.nm_customer
									FROM
										tr_invoice_header c GROUP BY c.id_customer")->result_array();
		$data = array(
			'title'			=> 'Indeks Of Invoice',
			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'ListIPP'		=> $ListIPP,
			'list_so'		=> $list_so,
			'list_cust'		=> $list_cust
		);
		history('View Data Invoice');
		$this->load->view('Invoicing/list_invoice',$data);
	}

	public function get_data_json_inv(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_inv(
			$requestData['no_so'],
			$requestData['customer'],
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
			$nestedData[]	= "<div align='right'>".date('d-M-Y', strtotime($row['tgl_invoice']))."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_invoice']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_invoice'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['total_invoice_idr'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['jenis_invoice'])." (".number_format($row['persentase'])."%)</div>";
            $nestedData[]	= "<div align='left'>".$row['delivery_no']."</div>";
				$print_jurnal	= "";
				$print_idr		= "";
				$create 		= "";

				if($row['proses_print']=='0'){
					$print_jurnal	= " <button class='btn btn-sm bg-purple uploadfile' title='Upload Invoice' data-no_invoice='".$row['id_invoice']."'><i class='fa fa-cloud-upload'></i></button> ";
					$print_jurnal	.= " <a href='".base_url('invoicing/excels/'.$row['id_invoice'])."' class='btn btn-sm bg-maroon' title='Export Excel'><i class='fa fa-file-excel-o'></i></a> ";
					$print_jurnal	.= "<button class='btn btn-sm btn-primary print'  title='Post Jurnal' data-inv='".$row['id_invoice']."'><i class='fa fa-clipboard'></i></button>";
				}
				if($row['proses_print']=='1'){
					$create	= "<button class='btn btn-sm btn-success terima' title='Create Penerimaan' data-inv='".$row['no_invoice']."'><i class='fa fa-list'></i></button>";
				}

				$detail		= "<button class='btn btn-sm btn-warning detail' title='View Invoice' data-no_invoice='".$row['id_invoice']."'><i class='fa fa-eye'></i></button>";
				if($row['file_inv']!=''){
					$detail		.= " <a href='".base_url('assets/invoice/'.$row['file_inv'])."' class='btn btn-sm bg-navy' title='Download Invoice' download><i class='fa fa-cloud-download'></i></a> ";
				}
				if($row['base_cur']=='USD'){
					$print_usd	= "&nbsp;<a href='".base_url('invoicing/print_invoice_usd/'.$row['id_invoice'])."' target='_blank' class='btn btn-sm btn-success' title='Print Invoice USD' ><i class='fa fa-print'></i><b>&nbsp;&nbsp;USD</b></a>";
					$print_idr = "";
				}else{
					$print_usd = "";
					$print_idr = "&nbsp;<a href='".base_url('invoicing/print_invoice_fix/'.$row['id_invoice'])."' target='_blank' class='btn btn-sm btn-info' title='Print Invoice IDR' ><i class='fa fa-print'></i><b>&nbsp;&nbsp;IDR</b></a>";
				}

			$nestedData[]	= "<div align='left'>
									".$detail."
									".$print_jurnal."
									".$create."
									".$print_idr."
									".$print_usd."
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

	public function query_data_inv($no_so, $customer, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_so = "";
		if($no_so <> '0'){
			$where_no_so = " AND a.so_number='".$no_so."' ";
		}

		$where_cust = "";
		if($customer <> '0'){
			$where_cust = " AND a.id_customer='".$customer."' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*, b.delivery_no
			FROM
				tr_invoice_header a,
				(SELECT @row:=0) r

			INNER JOIN penagihan b ON b.id=a.id_penagihan
		    WHERE 1=1 ".$where_no_so." ".$where_cust."
				AND (
				a.no_invoice LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.delivery_no LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'tgl_invoice',
			2 => 'no_invoice',
			3 => 'so_number',
			4 => 'nm_customer',
			5 => 'total_invoice',
			6 => 'total_invoice_idr'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function modal_detail_invoice(){
		$id = $this->uri->segment(3);
		$no_invoice = get_name('tr_invoice_header','no_invoice','id_invoice',$id);

		$data_header 	= $this->db->get_where('tr_invoice_header', array('no_invoice' => $no_invoice))->row();
		$alamat_cust 	= $this->db->get_where('customer', array('id_customer' => $data_header->id_customer))->row();
		$so          	= $this->db->get_where('billing_so', array('no_ipp' => str_replace('BQ-','',$data_header->id_bq)))->row();
		$getDetail 		= $this->db->get_where('tr_invoice_detail', array('no_invoice' => $no_invoice, 'kategori_detail'=>'PRODUCT'))->result_array();

		$data = array(
			'no_invoice' => $no_invoice,
			'data_header' => $data_header,
			'alamat_cust' => $alamat_cust,
			'getDetail'		=> $getDetail,
			'so' => $so
		);
		if($data_header->base_cur=='IDR'){
			$this->load->view('Invoicing/view_invoice_idr', $data);
		}else{
			$this->load->view('Invoicing/view_invoice', $data);
		}
	}











	public function gen_invoice($no_ipp){
		$LocInt	= substr($no_ipp, -1, 1);
		$m = date('m');
		$Y = date('Y');
		$qIPP			= "SELECT MAX(no_invoice) as maxP FROM tr_invoice_header WHERE no_invoice LIKE 'PC_/__%/".$Y."' ";
		$numrowIPP		= $this->db->query($qIPP)->num_rows();
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 3);
		$urutan2++;
		$urut2			= sprintf('%03s',$urutan2);
		$no_invoice		= "PC".$LocInt."/".$m.$urut2."/".$Y;

		return $no_invoice;
    }

	public function get_data($kunci,$tabel){
		if($kunci !=''){
        $this->db->where($kunci);
        $query=$this->db->get($tabel);
		}
		else{
		$query=$this->db->get($tabel);
		}
        return $query->result();
    }

	public function GetInvoiceHeader($idpo){
		$this->db->select('a.*');
		$this->db->from('tr_invoice_header a');
		// $this->db->join('ms_customer k', 'a.id_klien_inv=k.id_klien', 'left');
		// $this->db->join('ms_bank j', 'a.bank_id=j.id', 'left');
		$this->db->where('a.no_invoice', $idpo);
		//$this->db->order_by('a.id desc');
		$query = $this->db->get();
		if($query->num_rows() != 0)
		{
			return $query->result();
		} else {
			return false;
		}

		//k.nama_klien,k.brand, k.alamat, k.telpon, k.fax, k.email, k.alamat_npwp, k.nama_npwp, j.bank_nama, j.bank_cabang, j.bank_ac, j.bank_an
	}

	public function GetInvoiceDetail($idpo){
		$this->db->select('a.*');
		$this->db->from('tr_invoice_detail a');
		// $this->db->join('ms_customer k', 'a.id_klien_inv=k.id_klien', 'left');
		// $this->db->join('ms_bank j', 'a.bank_id=j.id', 'left');
		$this->db->where('a.no_invoice', $idpo);
		//$this->db->order_by('a.id desc');
		$query = $this->db->get();
		if($query->num_rows() != 0)
		{
			return $query->result();
		} else {
			return false;
		}

		//k.nama_klien,k.brand, k.alamat, k.telpon, k.fax, k.email, k.alamat_npwp, k.nama_npwp, j.bank_nama, j.bank_cabang, j.bank_ac, j.bank_an
	}



}