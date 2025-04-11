<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_finance extends CI_Controller {	
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

	public function index() {
		//FINANCE
		$sum_material 		= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND flag_active = 'Y' ")->num_rows();
		$waiting_approval 	= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND flag_active = 'Y' AND app_price_sup = 'Y'")->num_rows();
		$filter_data 		= $this->db->query("SELECT * FROM raw_materials WHERE `delete` = 'N' AND flag_active = 'Y' ")->result();
		
		$expired = [];
		$hampir_exp = [];
		$price_oke = [];
			
		foreach($filter_data as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->exp_price_ref_est;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke[] = $datas->id_material;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp[] = $datas->id_material;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired[] = $datas->id_material;
			}
		}

		//Aksesoris
		$sum_material_acc 		= $this->db->query("SELECT * FROM accessories WHERE `deleted` = 'N' ")->num_rows();
		$waiting_approval_acc 	= $this->db->query("SELECT * FROM accessories WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_acc 		= $this->db->query("SELECT * FROM accessories WHERE `deleted` = 'N' ")->result();
		
		$expired_acc = [];
		$hampir_exp_acc = [];
		$price_oke_acc = [];
			
		foreach($filter_data_acc as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->exp_price_ref_est;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_acc[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_acc[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_acc[] = $datas->id;
			}
		}

		//Transport
		$sum_material_trans 		= $this->db->query("SELECT * FROM cost_trucking ")->num_rows();
		$waiting_approval_trans 	= $this->db->query("SELECT * FROM cost_trucking WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_trans 		= $this->db->query("SELECT * FROM cost_trucking ")->result();
		
		$expired_trans = [];
		$hampir_exp_trans = [];
		$price_oke_trans = [];
			
		foreach($filter_data_trans as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->expired;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_trans[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_trans[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_trans[] = $datas->id;
			}
		}

		//eksport
		$sum_material_trans2 		= $this->db->query("SELECT * FROM cost_export_trans WHERE deleted='N' ")->num_rows();
		$waiting_approval_trans2 	= $this->db->query("SELECT * FROM cost_export_trans WHERE app_price_sup = 'Y'")->num_rows();
		$filter_data_trans2 		= $this->db->query("SELECT * FROM cost_export_trans WHERE deleted='N' ")->result();
		
		$expired_trans2 = [];
		$hampir_exp_trans2 = [];
		$price_oke_trans2 = [];
			
		foreach($filter_data_trans2 as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->expired;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_trans2[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_trans2[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_trans2[] = $datas->id;
			}
		}

		//Rutin
		$sum_material_rutin 		= $this->db->query("SELECT a.*, b.* FROM con_nonmat_new a LEFT JOIN price_ref b ON a.code_group=b.code_group AND b.deleted_date IS NULL WHERE a.status='1' AND a.deleted_date IS NULL AND a.code_group LIKE 'CN%' AND a.category_awal != 9")->num_rows();
		$waiting_approval_rutin 	= $this->db->query("SELECT a.*, b.* FROM con_nonmat_new a LEFT JOIN price_ref b ON a.code_group=b.code_group AND b.deleted_date IS NULL WHERE a.status='1' AND a.deleted_date IS NULL AND a.code_group LIKE 'CN%' AND a.category_awal != 9 AND b.app_price_sup = 'Y'")->num_rows();
		$filter_data_rutin 			= $this->db->query("SELECT a.*, b.* FROM con_nonmat_new a LEFT JOIN price_ref b ON a.code_group=b.code_group AND b.deleted_date IS NULL WHERE a.status='1' AND a.deleted_date IS NULL AND a.code_group LIKE 'CN%' AND a.category_awal != 9")->result();
		
		$expired_rutin = [];
		$hampir_exp_rutin = [];
		$price_oke_rutin = [];
			
		foreach($filter_data_rutin as $datas){
			$date_now 	= date('Y-m-d');
			$date_exp 	= $datas->expired;

			$tgl1x = new DateTime($date_now);
			$tgl2x = new DateTime($date_exp);
			$selisihx = $tgl2x->diff($tgl1x)->days + 1;

			$date_expv 	= date('d M Y', strtotime($date_exp));
			$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
			
			if($tgl2x < $tgl1x){
				$price_oke_rutin[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx <= 7){
				$hampir_exp_rutin[] = $datas->id;
			}
			if($tgl2x >= $tgl1x AND $selisihx > 7){
				$expired_rutin[] = $datas->id;
			}
		}

		$list_expired = $this->db
								->select('
									a.nm_material,
									SUM(a.qty_stock) AS stock,
									a.expired,
									a.id_gudang
									')
								->from('warehouse_stock_expired a')
								->join('raw_materials b', 'a.id_material=b.id_material','join')
								->where('b.id_category','TYP-0001')
								->where('a.id_material <>','MTL-2105003')
								->where('a.qty_stock >',0)
								->group_by('a.id_material, a.expired, a.id_gudang')
								->order_by('a.nm_material','asc')
								->order_by('a.expired','asc')
								->get()
								->result_array();

		$ttl_inv = $this->db->query("SELECT sum(total_invoice) ttl_inv from ( SELECT count(id_invoice) total_invoice FROM tr_invoice_header where year(tgl_invoice)='".date("Y")."'
		union
		select count(id_invoice) from tr_invoice_np_header where status=1 and year(tgl_invoice)='".date("Y")."'
		) ttl_inv")->row(); 
		
		$data			= array(
			'action'	=>'index',
			'title'		=>'Dashboard Finance',

			'ttl_inv'		=> $ttl_inv,
			'sum_material'		=> $sum_material,
			'waiting_approval' 	=> $waiting_approval,
			'expired'			=> COUNT($expired),
			'hampir_exp'		=> COUNT($hampir_exp),
			'price_oke'			=> COUNT($price_oke),

			'sum_material_acc'		=> $sum_material_acc,
			'waiting_approval_acc' 	=> $waiting_approval_acc,
			'expired_acc'			=> COUNT($expired_acc),
			'hampir_exp_acc'		=> COUNT($hampir_exp_acc),
			'price_oke_acc'			=> COUNT($price_oke_acc),

			'sum_material_trans'		=> $sum_material_trans + $sum_material_trans2,
			'waiting_approval_trans' 	=> $waiting_approval_trans + $waiting_approval_trans2,
			'expired_trans'				=> COUNT($expired_trans) + COUNT($expired_trans2),
			'hampir_exp_trans'			=> COUNT($hampir_exp_trans) + COUNT($hampir_exp_trans2),
			'price_oke_trans'			=> COUNT($price_oke_trans) + COUNT($price_oke_trans2),

			'sum_material_rutin'		=> $sum_material_rutin,
			'waiting_approval_rutin' 	=> $waiting_approval_rutin,
			'expired_rutin'				=> COUNT($expired_rutin),
			'hampir_exp_rutin'			=> COUNT($hampir_exp_rutin),
			'price_oke_rutin'			=> COUNT($price_oke_rutin),


			'late_enggenering'	=> $this->api_model->api_late_enginnering_count(),
			'late_costing'		=> $this->api_model->api_late_costing_count(),
			'late_quotation'	=> $this->api_model->api_late_quotation_count(),
			'total_quotation'	=> $this->api_model->api_total_quotation_count(),
			'total_so'			=> $this->api_model->api_total_so_count(),
			'api_app_bq'		=> COUNT($this->api_model->api_app_bq()),
			'api_app_est'		=> COUNT($this->api_model->api_app_est()),
			'api_app_est_fd'	=> COUNT($this->api_model->api_app_est_fd()),
			'api_app_est_fd_parsial'	=> COUNT($this->api_model->api_app_est_fd_parsial()),
			'list_category'		=> $this->db->get_where('raw_categories', array('flag_active'=>'Y'))->result_array(),
			'list_expired'	=> $list_expired
		);
		$this->load->view('Dashboard/finance',$data);
		
	}
}
