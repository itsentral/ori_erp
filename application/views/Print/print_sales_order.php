<?php

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
	// include $sroot. "/application/libraries/PHPMailer/PHPMailerAutoload.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php"; 
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');
	
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	// echo $sql_header;
	echo "<table class='gridtable2' border='0' width='100%' cellpadding='2'>";
		echo "<tr>";
			echo "<td class='header_style_company' width='65%'>".$data_iden[0]['nama_resmi']."</td>";
			echo "<td class='header_style_company bold color_req' colspan='2'>SALES ORDER</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat'>".strtoupper($data_iden[0]['alamat_baris1'])."</td>";
			echo "<td class='header_style_alamat' width='18%'>Sales Order</td>";
			echo "<td class='header_style_alamat'>:&nbsp;&nbsp;&nbsp;".get_nomor_so($no_ipp)."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat'>".strtoupper($data_iden[0]['alamat_baris2'])."</td>";
			echo "<td class='header_style_alamat'>Sales Order Date</td>";
			echo "<td class='header_style_alamat'>:&nbsp;&nbsp;&nbsp;".date('d F Y',strtotime($data_header[0]['updated_date']))."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat'>".strtoupper($data_iden[0]['alamat_baris3'])."</td>";
			echo "<td class='header_style_alamat'>&nbsp;</td>"; 
			echo "<td class='header_style_alamat'></td>";
		echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "<table border='0' width='100%' cellpadding='0'>";
		echo "<tr>";
			echo "<td width='44%' style='vertical-align:top;'>";
				echo "<table class='default' border='0' width='100%' cellpadding='2'>";
					echo "<tr>";
						echo "<td colspan='3' class='header_style2 bold'>BUYER</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan='3'>".strtoupper(get_name('customer','nm_customer','id_customer',$data_header[0]['kode_customer']))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan='3'>".strtoupper(get_name('customer','alamat','id_customer',$data_header[0]['kode_customer']))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td width='20%'>PHONE</td>";
						echo "<td width='5%'>:</td>";
						echo "<td>".strtoupper(get_name('customer','telpon','id_customer',$data_header[0]['kode_customer']))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>FAX</td>";
						echo "<td>:</td>";
						echo "<td>".strtoupper(get_name('customer','fax','id_customer',$data_header[0]['kode_customer']))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>EMAIL</td>";
						echo "<td>:</td>";
						echo "<td>".strtoupper(get_name('customer','email','id_customer',$data_header[0]['kode_customer']))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td height='20px'></td>";
						echo "<td></td>";
						echo "<td></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>RESIN</td>";
						echo "<td>:</td>";
						echo "<td>".strtoupper(get_resin('BQ-'.$no_ipp))."</td>";
					echo "</tr>";
				echo "</table>";
			echo "</td>";
			echo "<td width='6%'></td>";
			echo "<td width='44%' style='vertical-align:top;'>";
				echo "<table class='default' width='100%' cellpadding='2'>";
					echo "<tr>";
						echo "<td class='header_style2 bold'>DELIVERY ADDRESS</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".strtoupper(get_name('production_delivery','metode_delivery','no_ipp',$no_ipp))."</td>";
					echo "</tr>";
					echo "<tr>";
						$kirim_ipp = get_name('production_delivery','address_delivery','no_ipp',$no_ipp);
						$alamat_pengirirman = (!empty($data_header[0]['alamat_pengiriman']))?$data_header[0]['alamat_pengiriman']:$kirim_ipp;
						echo "<td>".strtoupper($alamat_pengirirman)."</td>";
					echo "</tr>";
				echo "</table>";
				echo "<table class='default' width='100%' cellpadding='2'>";
					echo "<tr>";
						echo "<td colspan='3' class='header_style2 bold'>ATTN</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan='3'>".strtoupper(get_name('customer','nm_customer','id_customer',$data_header[0]['kode_customer']))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td width='23%'>DELIVERY DATE</td>";
						echo "<td width='5%'>:</td>";
						echo "<td>".date('d F Y',strtotime($date_delivery))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>QUOTATION</td>";
						echo "<td>:</td>";
						echo "<td>".strtoupper(get_name('cost_project_header_sales','quo_number','id_bq','BQ-'.$no_ipp))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>IPP</td>";
						echo "<td>:</td>";
						echo "<td>".$no_ipp."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>NO PO</td>";
						echo "<td>:</td>";
						$no_po = (!empty($data_header[0]['no_po']))?$data_header[0]['no_po']:'-';
						echo "<td>".$no_po."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>TGL PO</td>";
						echo "<td>:</td>";
						$tgl_po = (!empty($data_header[0]['tgl_po']))?date('d F Y',strtotime($data_header[0]['tgl_po'])):'-';
						echo "<td>".$tgl_po."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>PROJECT</td>";
						echo "<td>:</td>";
						$project_name = (!empty($data_header[0]['project']))?$data_header[0]['project']:get_name('production','project','no_ipp',$no_ipp);
						echo "<td>".strtoupper($project_name)."</td>";
					echo "</tr>";
				echo "</table>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "<table class='gridtable' width='100%' border='0' cellpadding='2'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th style='text-align: left' width='5%'>NO</th>";
				echo "<th style='text-align: left' width='20%'>ORI ITEM</th>";
				echo "<th style='text-align: left' width='15%'>ITEM NO</th>";
				echo "<th style='text-align: left'>PO DESCRIPTION</th>";
				echo "<th style='text-align: right' width='8%'>QTY</th>";
				echo "<th style='text-align: left' width='6%'>UNIT</th>";
				echo "<th style='text-align: right' width='12%'>UNIT PRICE</th>";
				echo "<th style='text-align: right' width='14%'>TOTAL PRICE</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
			$no = 0;
			$SUM = 0;
			$matauang="Rp. ";
			foreach($data_product AS $val => $valx2){ $no++;
				$SUM += $valx2['total_deal_idr'];
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td align='left'>".strtoupper($valx2['product'])."</td>";
					echo "<td align='left'>".strtoupper($valx2['customer_item'])."</td>";
					echo "<td align='left'>".strtoupper($valx2['desc'])."</td>";
					echo "<td align='right'>".number_format($valx2['qty'],2)."</td>";
					echo "<td align='left'>".strtoupper($valx2['unit'])."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'] / $valx2['qty'] )."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'])."</td>";
				echo "</tr>";
			}
			foreach($data_material AS $val => $valx2){ $no++;
				$SUM += $valx2['total_deal_idr'];
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td align='left'>".strtoupper($valx2['nm_material'])."</td>";
					echo "<td align='left'>".strtoupper($valx2['customer_item'])."</td>";
					echo "<td align='left'>".strtoupper($valx2['desc'])."</td>";
					echo "<td align='right'>".number_format($valx2['qty'],2)."</td>";
					echo "<td align='left'>".strtoupper($valx2['satuan'])."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'] / $valx2['qty'])."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'])."</td>";
				echo "</tr>";
			}
			foreach($data_nonfrp AS $val => $valx2){ $no++;
				$SUM += $valx2['total_deal_idr'];
				$get_detail = $this->db->get_where('accessories', array('id'=>$valx2['id_material']))->result();
				$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
				$nama_acc = strtoupper($valx2['nm_material'].' - '.$valx2['spec']);
				if($valx2['category'] == 'baut'){
					$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
				}
				if($valx2['category'] == 'plate'){
					$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
				}
				if($valx2['category'] == 'gasket'){
					$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
				}
				if($valx2['category'] == 'lainnya'){
					$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
				}
				
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td align='left' colspan='2'>".$nama_acc."</td>";
					// echo "<td align='left'></td>";
					echo "<td align='left'>".strtoupper($valx2['desc'])."</td>";
					echo "<td align='right'>".number_format($valx2['qty'],2)."</td>";
					echo "<td align='left'>".strtoupper($valx2['satuan'])."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'] / $valx2['qty'] )."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'])."</td>";
				echo "</tr>";
			}
			foreach($data_packing AS $val => $valx2){ $no++;
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td align='left' colspan='2'>PACKING</td>";
					echo "<td align='left' colspan='2'>".strtoupper($valx2['desc'])."</td>";
					echo "<td align='left'>".strtoupper($valx2['satuan'])."</td>";
					echo "<td align='right'></td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'])."</td>";
				echo "</tr>";
			}
			foreach($data_shipping AS $val => $valx2){ $no++;
				$SUM += $valx2['total_deal_idr'];
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td align='left' colspan='2'>TRUCKING ".
														strtoupper(get_name('cost_project_detail','category','id',$valx2['id_milik']))." ".
														strtoupper(get_name('cost_project_detail','caregory_sub','id',$valx2['id_milik']))." (AREA ".
														strtoupper(get_name('cost_project_detail','area','id',$valx2['id_milik']))." - ".
														strtoupper(get_name('cost_project_detail','tujuan','id',$valx2['id_milik'])).") / ".
														strtoupper(get_name('truck','nama_truck','id',get_name('cost_project_detail','kendaraan','id',$valx2['id_milik'])))." ".	
														"</td>";
					echo "<td align='left'>".strtoupper($valx2['desc'])."</td>";
					echo "<td align='right'>".number_format($valx2['qty'],2)."</td>";
					echo "<td align='left'>".strtoupper($valx2['satuan'])."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'] / $valx2['qty'] )."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'])."</td>";
				echo "</tr>";
			}
			$SUM_OTHER = 0;
			foreach($data_other AS $val => $valx2){ $no++;
				$SUM_OTHER += $valx2['total_deal_idr'];
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td align='left' colspan='3'>".strtoupper($valx2['desc'])."</td>";
					echo "<td align='right'>".number_format($valx2['qty'],2)."</td>";
					echo "<td align='left'></td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr']/$valx2['qty'],2)."</td>";
					echo "<td align='right'>".$matauang.number_format($valx2['total_deal_idr'],2)."</td>";
				echo "</tr>";
			}
			$max = 10;
			$sisa = $max - $no;
			for($a=1; $a<=$sisa; $a++){
				echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
			}
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>PRODUCT</b></td>";
				echo "<td align='right' nowrap><b>".$matauang.number_format($data_total[0]['product_idr'] + $data_total[0]['mat_idr'] + $data_total[0]['acc_idr'])."</b></td>";
			echo "</tr>";
			if($data_total[0]['pack_idr'] > 0){
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>PACKING</b></td>";
				echo "<td align='right' nowrap><b>".$matauang.number_format($data_total[0]['pack_idr'])."</b></td>";
			echo "</tr>";
			}
			if($data_total[0]['ship_idr'] > 0){
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>TRUCKING</b></td>";
				echo "<td align='right'><b>".$matauang.number_format($data_total[0]['ship_idr'])."</b></td>";
			echo "</tr>";
			}
			if($SUM_OTHER > 0){
				echo "<tr>";
					echo "<td colspan='5'></td>";
					echo "<td colspan='2'><b>OTHER</b></td>";
					echo "<td align='right'><b>".$matauang.number_format($SUM_OTHER,2)."</b></td>";
				echo "</tr>";
				}
			if($data_total[0]['eng_idr'] > 0){
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>ENGENEERING</b></td>";
				echo "<td align='right' nowrap><b>".$matauang.number_format($data_total[0]['eng_idr'])."</b></td>";
			echo "</tr>";
			}
			if($data_total[0]['mat_idr'] > 0){
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>MATERIAL</b></td>";
				echo "<td align='right'><b>".$matauang.number_format($data_total[0]['mat_idr'])."</b></td>";
			echo "</tr>";
			}
			if($data_total[0]['acc_idr'] > 0){
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>ACCESSORIES</b></td>";
				echo "<td align='right'><b>".$matauang.number_format($data_total[0]['acc_idr'])."</b></td>";
			echo "</tr>";
			}
			$SUM_IDR = $data_header[0]['total_deal_idr'] ;
			if($data_total[0]['diskon'] > 0){
			echo "<tr>";
				echo "<td colspan='5'></td>";
				echo "<td colspan='2'><b>DISCOUNT (".$data_header[0]['diskon']."%)</b></td>";
				echo "<td align='right'><b>".$matauang.number_format($SUM_IDR - $data_header[0]['total_deal_idr'])."</b></td>";
			echo "</tr>";
			}
			echo "<tr>";
				echo "<td colspan='5'><b>Kurs : ".$matauang.number_format($data_header[0]['kurs_usd_dipakai'])."</b></td>";
				echo "<td colspan='2'><b>SUBTOTAL</b></td>";
				echo "<td align='right' nowrap><b>".$matauang.number_format($data_header[0]['total_deal_idr'])."</b></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='8'>Catatan : ".ucfirst($data_header[0]['catatan'])."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='8'>&nbsp;</td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	echo "<p class='bold'>TERM OF PAYMENT</p>"; 
	echo "<table class='gridtable' width='100%' border='0' cellpadding='2'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th style='text-align: left' width='10%'>PAYMENT</th>";
				echo "<th style='text-align: center' width='10%'>VALUE (%)</th>";
				echo "<th style='text-align: right' width='16%'>VALUE</th>";
				echo "<th style='text-align: left'>CONDITION</th>";
				echo "<th style='text-align: right' width='12%'>EST INVOICING</th>";
				echo "<th style='text-align: left' width='22%'>PERSYARATAN</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach($data_top AS $val => $valx2){
			echo "<tr>";
				echo "<td align='left'>".strtoupper($valx2['group_top'])."</td>";
				echo "<td align='center'>".number_format($valx2['progress'])." %</td>";
				echo "<td align='right'>".$matauang.number_format($valx2['value_idr'])."</td>";
				echo "<td align='left'>".strtoupper($valx2['keterangan'])."</td>";
				echo "<td align='center'>".date('d-M-Y', strtotime($valx2['jatuh_tempo']))."</td>";
				echo "<td align='left'>".strtoupper($valx2['syarat'])."</td>";
			echo "</tr>";
		}
		echo "</tbody>";
	echo "</table>";
	echo "<br>";
	echo "<p>Plan Delivery : Terlampir</p>";
	echo "<table class='gridtable4' width='100%' border='0' cellpadding='2'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td align='center' width='25%'>Create By</td>";
				echo "<td align='center' width='25%'>Knows</td>";
				echo "<td align='center' width='25%'>Approved By</td>";
				echo "<td align='center' width='25%'>Accepted</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td height='50px;'>&nbsp;</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td align='center' height='30px;'>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>";
				echo "<td align='center'>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>";
				echo "<td align='center'>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>";
				echo "<td align='center'>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	echo "<pagebreak />";
	echo "<p class='bold'>PLAN DELIVERY</p>"; 
	echo "<table class='gridtable3' width='100%' border='0' cellpadding='2'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th align='left' width='4%'>NO</th>";
				echo "<th align='left' width='20%'>ITEM PRODUCT</th>";
				echo "<th align='right' width='7%'>DIM 1</th>";
				echo "<th align='right' width='7%'>DIM 2</th>";
				echo "<th align='center' width='7%'>LIN</th>";
				echo "<th align='center' width='7%'>PRE</th>";
				echo "<th align='left' width='15%'>SPECIFICATION</th>";
				echo "<th align='right' width='7%'>QTY</th>";
				echo "<th align='right' width='7%'>SUM DELIVERY</th>";
				echo "<th align='right' width='7%'>QTY DELIVERY</th>";
				echo "<th align='right' width='12%'>DELIVERY DATE</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
			$SUM = 0;
			$no = 0;
			foreach($data_product AS $val => $valx){
				$no++;
				$count 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$no_ipp."' AND id_milik='".$valx['id_milik']."'")->num_rows();
				$each 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$no_ipp."' AND id_milik='".$valx['id_milik']."'")->result_array();
				$sum 	= $this->db->query("SELECT SUM(qty_delivery) AS total FROM scheduling_master WHERE no_ipp = '".$no_ipp."' AND id_milik='".$valx['id_milik']."'")->result();
				
				echo "<tr class='baris_".$no."'>";
					echo "<td rowspan='".$count."'>".$no."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' >".strtoupper($valx['product'])."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='right'>".number_format($valx['dim1'])."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='right'>".number_format($valx['dim2'])."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='center'>".$valx['liner']."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='center'>".$valx['pressure']."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='right' rowspan='".$count."' class='id_".$no."'><div id='qty_del_".$no."'>".$valx['qty']."</div></td>";
					echo "<td align='right' rowspan='".$count."' class='id_".$no."'><div id='tot_qty_del_".$no."'>".$sum[0]->total."</div></td>";
					echo "<td align='right'>".$each[0]['qty_delivery']."</td>";
					echo "<td align='right'>".date('d-M-Y', strtotime($each[0]['delivery_date']))."</td>";
				echo "</tr>";
				
				if($count > 1){
					$nox = 0;
					for($a=2; $a<=$count; $a++){ $nox++;
						echo "<tr>";
							echo "<td align='right'>".$each[$nox]['qty_delivery']."</td>";
							echo "<td align='right'>".date('d-M-Y', strtotime($each[$nox]['delivery_date']))."</td>";
						echo "</tr>";
					}
				}
			}
			foreach($data_nonfrp_delivery AS $val => $valx){
				$no++;

				$id_milik = get_name('so_bf_acc_and_mat','id_milik','id', $valx['id_milik']);

				$count 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$no_ipp."' AND id_milik='".$id_milik."'")->num_rows();
				$each 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$no_ipp."' AND id_milik='".$id_milik."'")->result_array();
				$sum 	= $this->db->query("SELECT SUM(qty_delivery) AS total FROM scheduling_master WHERE no_ipp = '".$no_ipp."' AND id_milik='".$id_milik."'")->result();
				
				

				$name = get_name('raw_materials','nm_material','id_material',$valx['id_material']);
				$category = 'MATERIAL';
				if($valx['category'] <> 'mat'){
					$name = get_name_acc($valx['id_material']);
					
					$category = 'BOLT & NUT';
					if($valx['category'] == 'plate'){
						$category = 'PLATE';
					}
					if($valx['category'] == 'gasket'){
						$category = 'GASKET';
					}
					if($valx['category'] == 'lainnya'){
						$category = 'LAINNYA';
					}
				}
				echo "<tr class='baris_".$no."'>";
					echo "<td rowspan='".$count."'>".$no."</td>";
					echo "<td rowspan='".$count."' colspan='4' class='id_".$no."' >".strtoupper($name)."</td>";
					// echo "<td rowspan='".$count."' class='id_".$no."' align='right'></td>";
					// echo "<td rowspan='".$count."' class='id_".$no."' align='right'></td>";
					// echo "<td rowspan='".$count."' class='id_".$no."' align='center'></td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='left'>".strtoupper($valx['satuan'])."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='left'>".$category."</td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='right'><div id='qty_del_".$no."'>".$valx['qty']."</div></td>";
					echo "<td rowspan='".$count."' class='id_".$no."' align='right'><div id='tot_qty_del_".$no."'>".$sum[0]->total."</div></td>";
					echo "<td align='right'>".$each[0]['qty_delivery']."</td>";
					echo "<td align='right'>".date('d-M-Y', strtotime($each[0]['delivery_date']))."</td>";
				echo "</tr>";
				
				if($count > 1){
					$nox = 0;
					for($a=2; $a<=$count; $a++){ $nox++;
						echo "<tr>";
							echo "<td align='right'>".$each[$nox]['qty_delivery']."</td>";
							echo "<td align='right'>".date('d-M-Y', strtotime($each[$nox]['delivery_date']))."</td>";
						echo "</tr>";
					}
				}
			}
		echo "</tbody>";
	echo "</table>";
	?>
	<style type="text/css">
	@page {
		margin-top: 0.4 cm;
		margin-left: 0.4 cm;
		margin-right: 0.4 cm;
		margin-bottom: 0.4 cm;
		margin-footer: 0 cm
	}
	
	.bold{
		font-weight: bold;
	}
	
	.color_req{
		color: #0049a8;
	}
	
	.header_style_company{
		padding: 15px;
		color: black;
		font-size: 20px;
	}
	
	.header_style_alamat{
		padding: 10px;
		color: black;
		font-size: 10px;
	}
	
	.header_style2{
		background-color: #0049a8;
		color: white;
		font-size: 10px;
		padding: 8px;
	}
	
	
	
	table.default {
		font-family: Arial, Helvetica, sans-serif;
		font-size:9px;
		padding: 0px;
	}
	
	p{
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
	}
	
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
	}
	.font{
		font-family: verdana,arial,sans-serif;
		font-size:14px;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
		color:#333333;
		border: 1px solid #dddddd;
		border-collapse: collapse;
	}
	table.gridtable th {
		padding: 6px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable th.head {
		padding: 6px; 
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable tr:nth-child(even) {
		background-color: #f2f2f2;
	}
	table.gridtable td {
		padding: 6px;
	}
	table.gridtable td.cols {
		padding: 6px;
	}


	table.gridtable2 {
		font-family: Arial, Helvetica, sans-serif;
		font-size:9px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable2 th {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}

	#hrnew {
		border: 0;
		border-bottom: 1px dashed #ccc;
		background: #999;
	}
	
	table.gridtable3 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
		color:#333333;
		border-width: 1px;
		border-color: #dddddd;
		border-collapse: collapse;
	}
	table.gridtable3 td {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #dddddd;
	}
	table.gridtable3 td.cols {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #dddddd;
	}
	table.gridtable3 th {
		padding: 6px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable3 th.head {
		padding: 6px; 
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	
	table.gridtable4 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
		color:#333333;
	}
	table.gridtable4 td {
		padding: 3px;
		border-color: #dddddd;
	}
	table.gridtable4 td.cols {
		padding: 3px;
	}
	</style>


	<?php

	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($no_ipp);
	$mpdf->setFooter('{PAGENO}');
	$mpdf->AddPageByArray([
		'margin-left' => 5,
		'margin-right' => 5,
		'margin-top' => 10,
		'margin-bottom' => 10,
	]);
	$mpdf->WriteHTML($html);
	$mpdf->Output('sales-order-price-idr-'.$no_ipp.'.pdf' ,'I');


