<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	date_default_timezone_set("Asia/Bangkok");

    function akses_server_side(){
		$Arr_Balik	= array(
			'hostname'	=> 'localhost',
			'hostuser'	=> 'sentral',
			'hostpass'	=> 'Sentral@2024**',
			'hostdb'	=> 'sentralsistem'
		);
		return $Arr_Balik;
	}

	function estimasi_down_down($id_bq){
		$CI 		=& get_instance();
		$resutArray	= $CI->db
						->select('id_bq, id_milik, id_material, MAX(last_cost) AS last_cost, detail_name, price_mat')
						->from('bq_component_detail')
						->where('id_category','TYP-0001')
						->where('id_material <>','MTL-1903000')
						->where('id_bq',$id_bq)
						->not_like('id_product', '_J%')
						->group_by('detail_name, id_milik, id_material')
						->get()
						->result_array();
		return $resutArray;
	}

	function estimasi_total($id_bq){
		$CI 		=& get_instance();

		$ArrayDown	= estimasi_down_down($id_bq);
		$resutArrayDown = [];
		if(!empty($ArrayDown)){
			foreach ($ArrayDown as $key => $value) {
				$UNIQ = $value['id_milik'].'-'.$value['detail_name'];

				// if(!array_key_exists($UNIQ['last_cost'], $resutArrayDown)) {
				// 	$resutArrayDown[$UNIQ]['last_cost'] = 0;
				// }

				$resutArrayDown[$UNIQ]['id_bq'] 		= $value['id_bq'];
				$resutArrayDown[$UNIQ]['id_milik'] 		= $value['id_milik'];
				$resutArrayDown[$UNIQ]['id_material'] 	= $value['id_material'];
				$resutArrayDown[$UNIQ]['last_cost'] 	= $value['last_cost'];
				$resutArrayDown[$UNIQ]['detail_name'] 	= $value['detail_name'];
				$resutArrayDown[$UNIQ]['price_mat'] 	= $value['price_mat'];
			}
		}

		$resutArrayPlus	= $CI->db
								->select('id_bq, id_milik, id_material, last_cost, detail_name, price_mat')
								->from('bq_component_detail_plus')
								->where('id_category','TYP-0001')
								->where('id_material <>','MTL-1903000')
								->where('id_bq',$id_bq)
								->get()
								->result_array();
		$resutArrayDetail = $CI->db
								->select('id_bq, id_milik, id_material, last_cost, detail_name, price_mat')
								->from('bq_component_detail')
								->where('id_category','TYP-0001')
								->where('id_material <>','MTL-1903000')
								->where('id_bq',$id_bq)
								->not_like('id_product', '_J%')
								->get()
								->result_array();
		
		$resutArrayDetailTop = $CI->db
								->select('id_bq, id_milik, id_material, last_cost, detail_name, price_mat')
								->from('bq_component_detail')
								->where('id_category <>','TYP-0030')
								->where('id_category <>','TYP-0001')
								->where('id_material <>','MTL-1903000')
								->where('id_bq',$id_bq)
								->get()
								->result_array();

		$resutArrayPlusTop = $CI->db
								->select('id_bq, id_milik, id_material, last_cost, detail_name, price_mat')
								->from('bq_component_detail_plus')
								->where('id_category <>','TYP-0030')
								->where('id_category <>','TYP-0001')
								->where('id_material <>','MTL-1903000')
								->where('id_bq',$id_bq)
								->get()
								->result_array();

		$resutArrayAddTop = $CI->db
								->select('id_bq, id_milik, id_material, last_cost, detail_name, price_mat')
								->from('bq_component_detail_add')
								->where('id_category <>','TYP-0030')
								->where('id_material <>','MTL-1903000')
								->where('id_bq',$id_bq)
								->get()
								->result_array();
		
		$ArrMerge = array_merge($resutArrayDown, $resutArrayPlus, $resutArrayDetail, $resutArrayDetailTop, $resutArrayPlusTop, $resutArrayAddTop);

		return $ArrMerge;
	}

	function estimasi_total_summary($id_bq){
		$CI 		=& get_instance();
		$resutArray	= estimasi_total($id_bq);

		$getBqDetail = $CI->db->get_where('so_bf_detail_header',array('id_bq',$id_bq))->result_array();
		$ArrGetQty = [];
		foreach ($getBqDetail as $key => $value) {
			$ArrGetQty[$value['id_milik']] = $value['qty'];
		}

		$ArrResult = [];
		foreach ($resutArray as $key => $value) {
			$ArrResult[$key]['id_bq'] = $value['id_bq'];
			$ArrResult[$key]['id_material'] = $value['id_material'];
			$ArrResult[$key]['nm_material'] = $value['nm_material'];

			$qty = (!empty($ArrGetQty[$value['id_milik']]))?$ArrGetQty[$value['id_milik']]:0;
			$ArrResult[$key]['last_cost'] = $value['last_cost'] * $qty;
		}

		return $ArrResult;
	}