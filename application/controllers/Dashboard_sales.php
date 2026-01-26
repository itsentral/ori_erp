<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_sales extends CI_Controller {	
	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('dashboard_model');
		$this->load->model('api_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}		
	}

	public function index($year=null) {
		if($year == null){
			$year = date('Y');
		}

		$ttl_inv = $this->db->query("SELECT sum(total_invoice) ttl_inv from ( SELECT sum(total_invoice_idr) total_invoice FROM tr_invoice_header where year(tgl_invoice)='".$year."'
		union
		select sum(total_invoice_idr) from tr_invoice_np_header where status=1 and year(tgl_invoice)='".$year."'
		) ttl_inv")->row(); 
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Sales',

			'year'		=> $year,
			'ttl_inv'		=> $ttl_inv,
			'late_enggenering'	=> $this->api_model->api_late_enginnering_count(),
			'late_costing'		=> $this->api_model->api_late_costing_count(),
			'late_quotation'	=> $this->api_model->api_late_quotation_count(),
			'total_quotation'	=> $this->api_model->api_total_quotation_count($year),
			'total_so'			=> $this->api_model->api_total_so_count($year)
		);
		$this->load->view('Dashboard/sales',$data);
		
	}

	function list_invoice_dash($year){
		$ttl_inv = $this->db->query("select * from (SELECT no_invoice,tgl_invoice,nm_customer,base_cur,total_invoice total_invoice_usd,total_invoice_idr, so_number,no_ipp, 'prod' jenis FROM tr_invoice_header where year(tgl_invoice)='".$year."'
		union
		select no_invoice,tgl_invoice,nm_customer,base_cur,total_invoice_usd,total_invoice_idr,'NON PRODUCT' so_number,'NON PRODUCT' no_ipp,'nonp' jenis from tr_invoice_np_header where status=1 and year(tgl_invoice)='".$year."')
		as list_invoice order by tgl_invoice
		")->result(); $i=0;
		echo "<table class='table'><tr><th>No</th><th>No Invoice</th><th>Tanggal Invoice</th><th>Customer</th><th>Nilai Invoice</th><th>No SO</th><th>No IPP</th></tr>";
		foreach ($ttl_inv as $keys=>$val){ $i++;
			echo "<tr><td>".$i."</td><td>".$val->no_invoice."</td><td>".date("d-m-Y", strtotime($val->tgl_invoice))."</td><td>".$val->nm_customer."</td>";
			if($val->base_cur=='IDR'){
				echo "<td align='right'>".$val->base_cur." ".number_format($val->total_invoice_idr)."</td>";
			}else{
				echo "<td align='right'>".$val->base_cur." ".number_format($val->total_invoice_usd)."</td>";
			}
			if($val->jenis=='prod'){
				echo "<td>".$val->so_number."</td>";
				echo "<td>".$val->no_ipp."</td>";
			}else{
				echo "<td colspan=2>".$val->so_number."</td>";
			}
			echo "</tr>";
		}
		echo "</table>
		<script>
			$(document).ready(function(){
				swal.close();
			});
		</script>
		";
		die();
	}
}
