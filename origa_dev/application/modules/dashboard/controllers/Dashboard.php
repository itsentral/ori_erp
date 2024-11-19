<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {
/*
 * @author Yunaz
 * @copyright Copyright (c) 2016, Yunaz
 *
 * This is controller for Penerimaan
 */
	public function __construct()
	{
		parent::__construct();

        $this->load->model('dashboard/dashboard_model');

        $this->template->page_icon('fa fa-dashboard');
	}

	public function index(){
		$this->template->title('Dashboard');
		$no_so = $this->uri->segment(3);
		$no_so2 = $this->uri->segment(4);
		// echo $no_so;
		if(empty($no_so)){
			$sql_beet = $this->db->query("SELECT no_so FROM sales_order_header WHERE delivery_date >= DATE(NOW()) LIMIT 1")->result();
			$no_so = $sql_beet[0]->no_so;
		}
		if(empty($no_so2)){
			$sql_beet = $this->db->query("SELECT no_so FROM sales_order_header WHERE delivery_date >= DATE(NOW()) LIMIT 1")->result();
			$no_so2 = $sql_beet[0]->no_so;
		}

		// $no_so = 'SO200022';

		$sql_order1 	= "SELECT SUM(a.qty_propose - a.qty_delivery) AS qty_order FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so."' AND b.id_category1 <> 'I2000002'";
		$rest_order1 	= $this->db->query($sql_order1)->result();

		$sql_order2 	= "SELECT SUM(a.qty_propose - a.qty_delivery) AS qty_order FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so2."' AND b.id_category1 = 'I2000002'";
		$rest_order2 	= $this->db->query($sql_order2)->result();

		$sql_propose1 	= "SELECT SUM(a.qty_order - a.qty_delivery) AS qty_propose FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so."' AND b.id_category1 <> 'I2000002'";
		$rest_propose1 	= $this->db->query($sql_propose1)->result();

		$sql_propose2 	= "SELECT SUM(a.qty_order - a.qty_delivery) AS qty_propose FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so2."' AND b.id_category1 = 'I2000002'";
		$rest_propose2 	= $this->db->query($sql_propose2)->result();

		$sql_fg1 	= "SELECT
									SUM( IF(a.qty_stock > c.qty_order,c.qty_order,a.qty_stock ) ) AS qty_stock,
									SUM(a.qty_stock) AS qty_over
								FROM
									warehouse_product a
									LEFT JOIN ms_inventory_category2 b ON a.id_product = b.id_category2
									LEFT JOIN sales_order_detail c ON a.id_product = c.product
								WHERE
									a.category = 'product'
									AND b.id_category1 <> 'I2000002'
									AND c.qty_order > 0
									AND c.no_so = '".$no_so."'";
		$rest_fg1 	= $this->db->query($sql_fg1)->result();

		$sql_fg2 	= "SELECT
									SUM( IF(a.qty_stock > c.qty_order,c.qty_order,a.qty_stock ) ) AS qty_stock,
									SUM(a.qty_stock) AS qty_over
								FROM
									warehouse_product a
									LEFT JOIN ms_inventory_category2 b ON a.id_product = b.id_category2
									LEFT JOIN sales_order_detail c ON a.id_product = c.product
								WHERE
									a.category = 'product'
									AND b.id_category1 = 'I2000002'
									AND c.qty_order > 0
									AND c.no_so = '".$no_so2."'";
		$rest_fg2 	= $this->db->query($sql_fg2)->result();

		$qty_order1 = ($rest_order1[0]->qty_order > 0)?$rest_order1[0]->qty_order :0;
		$qty_order2 = ($rest_order2[0]->qty_order > 0)?$rest_order2[0]->qty_order :0;

		$qty_propose1 = ($rest_propose1[0]->qty_propose > 0)?$rest_propose1[0]->qty_propose :0;
		$qty_propose2 = ($rest_propose2[0]->qty_propose > 0)?$rest_propose2[0]->qty_propose :0;

		$qtyfg1 = ($rest_fg1[0]->qty_stock > 0)?$rest_fg1[0]->qty_stock :0;
		$qtyfg2 = ($rest_fg2[0]->qty_stock > 0)?$rest_fg2[0]->qty_stock :0;

		$qtybal1 = $qty_propose1 - $qtyfg1;
		$qtybal2 = $qty_propose2 - $qtyfg2;

		$progres1 = 0;
		$progres2 = 0;
		if($qtyfg1 > 0 AND $qty_propose1 > 0){
			$progres1 = ($qtyfg1 / $qty_propose1) * 100;
		}
		if($qtyfg2 > 0 AND $qty_propose2 > 0){
			$progres2 = ($qtyfg2 / $qty_propose2) * 100;
		}

		$over1 = $rest_fg1[0]->qty_over - $qty_propose1;
		$over2 = $rest_fg2[0]->qty_over - $qty_propose2;
		if($rest_fg1[0]->qty_over - $qty_propose1 < 0){
			$over1 = 0;
		}
		if($rest_fg2[0]->qty_over - $qty_propose2 < 0){
			$over2 = 0;
		}

		// echo $rest_fg1[0]->qty_stock;

		$data = array(
			'qty_order1' => $qty_order1,
			'qty_order2' => $qty_order2,
			'qty_propose1' => $qty_propose1,
			'qty_propose2' => $qty_propose2,
			'qtyfg1' => $qtyfg1,
			'qtyfg2' => $qtyfg2,
			'qtybal1' => $qtybal1,
			'qtybal2' => $qtybal2,
			'progres1' => $progres1,
			'progres2' => $progres2,
			'no_so' => $no_so,
			'no_so2' => $no_so2,
			'over1' => $over1,
			'over2' => $over2
		);

		$this->template->render('index', $data);

	}
	## JSON DATA DASHBOARD
	public function get_add(){
		$id 	= $this->uri->segment(3);
		$sql_beet = $this->db->query("SELECT a.no_so, a.delivery_date, b.name_customer FROM sales_order_header a LEFT JOIN master_customer b ON a.code_cust=b.id_customer WHERE a.delivery_date >= DATE(NOW())")->result_array();

		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select id='noso_".$id."' class='chosen_select form-control input-sm inline-blockd chosen_select salesorder' data-type='exis'>";
				$d_Header .= "<option value='0'>Select Sales order</option>";
				foreach ($sql_beet AS $val => $valx){
						$d_Header .= "<option value='".$valx['no_so']."'>".$valx['no_so']."  [".strtoupper(date('d-M-Y', strtotime($valx['delivery_date'])))."] / ".strtoupper($valx['name_customer'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";

			$d_Header .= "<td align='left'>EXISTING <button type='button' class='btn btn-sm btn-danger delPart' title='Delete' style='float:right;'><i class='fa fa-close'></i></button></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' class='order' id='exis_order_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' class='propose' id='exis_propose_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right; 'class='fg' id='exis_fg_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right; 'class='bal' id='exis_balance_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' id='exis_progress_".$id."'></div></td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='exis_".$id."'>";
			$d_Header .= "<td  colspan='7' align='left'><button type='button' class='btn btn-sm btn-primary addPart' title='ADD EXISTING' style='min-width:150px;'><i class='fa fa-plus'></i>&nbsp;&nbsp;ADD EXISTING</button></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

	public function get_add2(){
		$id 	= $this->uri->segment(3);
		$sql_beet = $this->db->query("SELECT a.no_so, a.delivery_date, b.name_customer FROM sales_order_header a LEFT JOIN master_customer b ON a.code_cust=b.id_customer WHERE a.delivery_date >= DATE(NOW())")->result_array();

		$d_Header = "";
		$d_Header .= "<tr class='header2_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select id='noso2_".$id."' class='chosen_select form-control input-sm inline-blockd chosen_select salesorder' data-type='doha'>";
				$d_Header .= "<option value='0'>Select Sales order</option>";
				foreach ($sql_beet AS $val => $valx){
						$d_Header .= "<option value='".$valx['no_so']."'>".$valx['no_so']."  [".strtoupper(date('d-M-Y', strtotime($valx['delivery_date'])))."] / ".strtoupper($valx['name_customer'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";

			$d_Header .= "<td align='left'>DOHA <button type='button' class='btn btn-sm btn-danger delPart' title='Delete' style='float:right;'><i class='fa fa-close'></i></button></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' class='order' id='doha_order_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' class='propose' id='doha_propose_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' class='fg' id='doha_fg_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' class='bal' id='doha_balance_".$id."'></div></td>";
			$d_Header .= "<td align='left'><div style='text-align:right;' id='doha_progress_".$id."'></div></td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='doha_".$id."'>";
			$d_Header .= "<td  colspan='7' align='left'><button type='button' class='btn btn-sm btn-success addPart2' title='ADD DOHA' style='min-width:150px;'><i class='fa fa-plus'></i>&nbsp;&nbsp;ADD DOHA</button></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

	public function get_result(){
		$id 	= $this->uri->segment(3);
		$no_so 	= $this->uri->segment(4);

		$nomor 	= $this->input->post('nomor');

		if($id == 'exis'){
			$over 	= $this->input->post('over1');
			$query2	 = $this->db->query("SELECT SUM(a.qty_stock) AS stock FROM warehouse_product a LEFT JOIN ms_inventory_category2 b ON a.id_product=b.id_category2 WHERE a.category = 'product' AND b.id_category1 <> 'I2000002' ")->result();
			$qty2_ = (!empty($query2[0]->qty_stock))?$query2[0]->qty_stock:0;
			$sql_order1 		= "SELECT SUM(a.qty_propose - a.qty_delivery) AS qty_order FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so."' AND b.id_category1 <> 'I2000002'";
			$rest_order1 		= $this->db->query($sql_order1)->result();
			$sql_propose1 	= "SELECT SUM(a.qty_order - a.qty_delivery) AS qty_propose FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so."' AND b.id_category1 <> 'I2000002'";
			$rest_propose1 	= $this->db->query($sql_propose1)->result();
			$sql_fg1 				= "SELECT
													SUM( IF(a.qty_stock > c.qty_order,c.qty_order,a.qty_stock ) ) AS qty_stock,
													SUM(a.qty_stock) AS qty_over
												FROM
													warehouse_product a
													LEFT JOIN ms_inventory_category2 b ON a.id_product = b.id_category2
													LEFT JOIN sales_order_detail c ON a.id_product = c.product
												WHERE
													a.category = 'product'
													AND b.id_category1 <> 'I2000002'
													AND c.qty_order > 0
													AND c.no_so = '".$no_so."'";
			// echo $sql_fg1;
			$rest_fg1 		= $this->db->query($sql_fg1)->result();
			$qty_order1 	= ($rest_order1[0]->qty_order > 0)?$rest_order1[0]->qty_order :0;
			$qty_propose1 = ($rest_propose1[0]->qty_propose > 0)?$rest_propose1[0]->qty_propose :0;
			$qtyfg1 			= ($rest_fg1[0]->qty_stock > 0)?$rest_fg1[0]->qty_stock :0;
			$qtybal1 			= $qty_propose1 - $qtyfg1;

			$over1 = $rest_fg1[0]->qty_over - $qty_propose1;
			if($rest_fg1[0]->qty_over - $qty_propose1 < 0){
				$over1 = 0;
			}
		}

		if($id == 'doha'){
			$over 	= $this->input->post('over2');
			$query2	 = $this->db->query("SELECT SUM(a.qty_stock) AS stock FROM warehouse_product a LEFT JOIN ms_inventory_category2 b ON a.id_product=b.id_category2 WHERE a.category = 'product' AND b.id_category1 = 'I2000002' ")->result();
			$qty2_ = (!empty($query2[0]->qty_stock))?$query2[0]->qty_stock:0;
			$sql_order1 		= "SELECT SUM(a.qty_propose - a.qty_delivery) AS qty_order FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so."' AND b.id_category1 = 'I2000002'";
			$rest_order1 		= $this->db->query($sql_order1)->result();
			$sql_propose1 	= "SELECT SUM(a.qty_order - a.qty_delivery) AS qty_propose FROM sales_order_detail a LEFT JOIN ms_inventory_category2 b ON a.product=b.id_category2 WHERE a.no_so = '".$no_so."' AND b.id_category1 = 'I2000002'";
			$rest_propose1 	= $this->db->query($sql_propose1)->result();
			$sql_fg1 				= "SELECT
													SUM( IF(a.qty_stock > c.qty_order,c.qty_order,a.qty_stock ) ) AS qty_stock,
													SUM(a.qty_stock) AS qty_over
												FROM
													warehouse_product a
													LEFT JOIN ms_inventory_category2 b ON a.id_product = b.id_category2
													LEFT JOIN sales_order_detail c ON a.id_product = c.product
												WHERE
													a.category = 'product'
													AND b.id_category1 = 'I2000002'
													AND c.qty_order > 0
													AND c.no_so = '".$no_so."'";
			$rest_fg1 		= $this->db->query($sql_fg1)->result();
			$qty_order1 	= ($rest_order1[0]->qty_order > 0)?$rest_order1[0]->qty_order :0;
			$qty_propose1 = ($rest_propose1[0]->qty_propose > 0)?$rest_propose1[0]->qty_propose :0;
			$qtyfg1 			= ($rest_fg1[0]->qty_stock > 0)?$rest_fg1[0]->qty_stock :0;
			$qtybal1 			= $qty_propose1 - $qtyfg1;

			$over1 = $rest_fg1[0]->qty_over - $qty_propose1;
			if($rest_fg1[0]->qty_over - $qty_propose1 < 0){
				$over1 = 0;
			}
		}


		if($nomor <> '1'){
			$qtyfg1 = $over;
			if($over < 0){
				$qtyfg1 = 0;
			}

			$qtybal1 			= $qty_propose1 - $qtyfg1;
		}

		$progres1 		= 0;
		if($qtyfg1 > 0 AND $qty_propose1 > 0){
			$progres1 	= ($qtyfg1 / $qty_propose1) * 100;
		}

		// echo $qtyfg1;
		echo json_encode(array(
			'order' 		=> number_format($qty_order1),
			'propose' 	=> number_format($qty_propose1),
			'fg' 				=> number_format($qtyfg1),
			'bal' 			=> number_format($qtybal1),
			'progres' 	=> number_format($progres1,2).' %',
			'no_so' 		=> $no_so,
			'id'				=> $id,
			'over' 			=> $over1,
			'nomor'			=> $nomor
		));
	}

}
