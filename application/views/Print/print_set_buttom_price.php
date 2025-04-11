<?php
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qBQ 		= "SELECT * FROM production WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
	$dHeaderBQ	= $this->db->query($qBQ)->result_array();
	
	$data1BTS 	= SQL_Quo_Edit($id_bq);
	$result1BTS	= $this->db->query($data1BTS)->result_array();
	
	$data2BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
	$result2BTS	= $this->db->query($data2BTS)->result_array();
	
	$data3BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
	$result3BTS	= $this->db->query($data3BTS)->result_array();
	
	$data4BTS 	= "SELECT a.*, b.*,(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".str_replace('BQ-','',$id_bq)."')) as country_name FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' ORDER BY a.urut ASC ";
	$result4BTS	= $this->db->query($data4BTS)->result_array();
	
	$data5BTS 	= "SELECT b.*, c.* FROM cost_project_detail b LEFT JOIN truck c ON b.kendaraan = c.id WHERE b.category = 'lokal' AND b.id_bq = '".$id_bq."' ORDER BY b.id ASC";
	$result5BTS	= $this->db->query($data5BTS)->result_array();
	
	// $rest_non_frp	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'acc'))->result_array();
	$rest_mat		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'mat'))->result_array();
	$rest_baut		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'baut'))->result_array();
	$rest_plate		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'plate'))->result_array();
	$rest_gasket	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'gasket'))->result_array();
	$rest_lainnya	= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq, 'category'=>'lainnya'))->result_array();

	$otherArray		= $this->db->get_where('cost_project_detail', array('id_bq'=>$id_bq, 'category'=>'other'))->result_array();
	
	echo "<htmlpageheader>";
	// exit;
	?>
	<table class='header_style' border='0' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td><b><h2>Selling Price</h2></b></td>
		</tr>
		</thead>
	</table>
	<br>
	
	<table class='header_style2' border='0' width='100%' cellpadding='2'>
		<tr>
			<td width='100px' align='center' style='vertical-align:top;' rowspan='7'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='120' width='110' ></td>
			<td rowspan='7' width='20px'></td>
			<td colspan='3' style='background-color: #ffffff !important;color: #0e5ca9;'><b><h3>PT  ORI POLYTEC COMPOSITES</h3></b></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td width='100px'>IPP No</td>
			<td width='15px'>:</td>
			<td><?= str_replace('BQ-','',$id_bq); ?></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ[0]['project'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Customer</td>
			<td>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ[0]['nm_customer'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>&nbsp;</td>
			<td style='vertical-align:top;'></td> 
			<td style='vertical-align:top;'></td>
		
		</tr>
	</table>
	<br>
	<?php echo "<htmlpageheader>";?>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3'>Item Product</th>
				<th class="text-center" width='5%'>Dim 1</th>
				<th class="text-center" width='5%'>Dim 2</th>
				<th class="text-center" width='6%'>Series</th>
				<th class="text-center" width='11%'>Specification</th>
				<th class="text-center" width='5%'>Qty</th>
				<th class="text-center" width='7%'>COGS</th>
				<th class="text-center" width='6%'>Profit (%)</th>
				<th class="text-center" width='6%'>Profit ($)</th>
				<th class="text-center" width='8%'>Bottom Price</th>
				<th class="text-center" width='6%'>Allow (%)</th>
				<th class="text-center" width='6%'>Allow ($)</th>
				<th class="text-center" width='8%'>Selling Price</th>
				<th class="text-center" width='8%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM = 0;
			$SUMx = 0;
			$SUM0 = 0;
			$no = 0;
			$HPP_Tot = 0;
			foreach($result1BTS AS $val => $valx){
				$no++;
				$persen 	= (!empty($valx['persen']))?$valx['persen']:0;
				$extra 		= (!empty($valx['extra']))?$valx['extra']:0; 
				
				$est_harga 	= (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
				
				$HPP_Tot 	+= $est_harga * $valx['qty'];
				
				$dataSum	= $HrgTot;
				
				$SUM0 		+= $est_harga;
				$SUMx 		+= $HrgTot2;
				$SUM 		+= $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['parent_product'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='right'>".number_format($est_harga,2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format(($HrgTot2/$valx['qty']) - $est_harga,2)."</td>";
					echo "<td align='right'>".number_format($HrgTot2/$valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format(($HrgTot2/$valx['qty']) * ($extra/100),2)."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='8'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM0,2);?></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b><?= number_format($SUMx,2);?></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
			<?php
				$net_profit = 0;
				if($SUMx > 0){
					$net_profit = ($SUMx - $HPP_Tot)/$SUMx;
				}
				
				?>
			<tr class='FootColor'>
				<td colspan='8'><b></b></td>
				<td align='right'><b>Net Profit</b></td>
				<td align='center'><b><?= number_format($net_profit,2) * 100;?> %</b></td>
				<td align='right'><b></b></td>
				<td align='right'><b><?= number_format($SUMx - $HPP_Tot,2);?></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
			</tr>
		</tbody>
		<?php
		$SUM_NONFRP = 0;
		// if(!empty($rest_non_frp)){
			// echo "<tbody>";
				// echo "<tr>";
					// echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>BILL OF QUANTITY NON FRP</b></td>";
				// echo "</tr>";
				// echo "<tr class='bg-bluexyz'>";
					// echo "<th class='text-center' colspan='5'>Material Name</th>";
					// echo "<th class='text-center'>Qty</th>";
					// echo "<th class='text-center' colspan='2'>Satuan</th>";
					// echo "<th class='text-center'>Unit</th>";
					// echo "<th class='text-center'>Profit</th>";
					// echo "<th class='text-center'>Unit Price</th>";
					// echo "<th class='text-center'>Allow</th>";
					// echo "<th class='text-center'>Total Price</th>";
				// echo "</tr>";
			// echo "</tbody>";
			// echo "<tbody class='body_x'>";
			
			// foreach($rest_non_frp AS $val => $valx){
				// $SUM_NONFRP += $valx['price_total'];
				// echo "<tr>";
					// echo "<td colspan='5'>".strtoupper(get_name('con_nonmat_new', 'material_name', 'code_group', $valx['caregory_sub']))."</td>";
					// echo "<td align='center'>".number_format($valx['qty'])."</td>";
					// echo "<td align='center' colspan='2'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					// echo "<td align='right'>".number_format($valx['unit_price'])."</td>";
					// echo "<td align='right'>".number_format($valx['persen'],2)."</td>";
					// echo "<td align='right'>".number_format($valx['price'])."</td>";
					// echo "<td align='right'>".number_format($valx['extra'],2)."</td>";
					// echo "<td align='right'>".number_format($valx['price_total'])."</td>";
				// echo "</tr>";
			// }
			// echo "<tr class='FootColor'>";
				// echo "<td colspan='11'><b>TOTAL BILL OF QUANTITY NON FRP</b></td> ";
				// echo "<td align='center'><b>IDR</b></td> ";
				// echo "<td align='right'><b>".number_format($SUM_NONFRP)."</b></td>";
			// echo "</tr>";
			// echo "</tbody>";
		// }
		$SUM_BAUT = 0;
		if(!empty($rest_baut)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>BAUT</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='9'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_baut AS $val => $valx){
				
				$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
				$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
				
				$persen = get_persen($id_bq, $valx['id_material'], $valx['id']);
				$extra 	= get_extra($id_bq,  $valx['id_material'], $valx['id']);
				$price 			= $valx['total_price'] + ($valx['total_price'] * ($persen/100));
				$price_total 	= $price + ($price * ($extra/100));

				$SUM_BAUT += $price_total;
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx."</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($price,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($price_total,2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='15'><b>TOTAL BAUT</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_BAUT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_PLATE = 0;
		if(!empty($rest_plate)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>PLATE</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='9'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_plate AS $val => $valx){
				$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
				$persen = get_persen($id_bq, $valx['id_material'], $valx['id']);
				$extra 	= get_extra($id_bq,  $valx['id_material'], $valx['id']);
				$price 			= $valx['total_price'] + ($valx['total_price'] * ($persen/100));
				$price_total 	= $price + ($price * ($extra/100));

				$QTY = ($valx['berat'] > 0)?$valx['berat']:$valx['qty'];

				$SUM_PLATE += $price_total;
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
					echo "<td align='right'>".number_format($QTY,2)."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($price,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($price_total,2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='15'><b>TOTAL PLATE</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_PLATE, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_GASKET = 0;
		if(!empty($rest_gasket)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>GASKET</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='9'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_gasket AS $val => $valx){
				
				$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
				$persen = get_persen($id_bq, $valx['id_material'], $valx['id']);
				$extra 	= get_extra($id_bq,  $valx['id_material'], $valx['id']);
				$price 			= $valx['total_price'] + ($valx['total_price'] * ($persen/100));
				$price_total 	= $price + ($price * ($extra/100));

				$SUM_GASKET += $price_total;
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($price,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($price_total,2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='15'><b>TOTAL GASKET</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_GASKET, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_LAINNYA = 0;
		if(!empty($rest_lainnya)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>LAINNYA</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='9'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_lainnya AS $val => $valx){
				$get_detail = $this->db->select('nama, material, spesifikasi, standart, ukuran_standart, dimensi')->get_where('accessories', array('id'=>$valx['id_material']))->result();
				$persen = get_persen($id_bq, $valx['id_material'], $valx['id']);
				$extra 	= get_extra($id_bq,  $valx['id_material'], $valx['id']);
				$price 			= $valx['total_price'] + ($valx['total_price'] * ($persen/100));
				$price_total 	= $price + ($price * ($extra/100));

				$SUM_LAINNYA += $price_total;
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi)."</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($price,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($price_total,2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='15'><b>TOTAL LAINNYA</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_LAINNYA, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_MAT = 0;
		if(!empty($rest_mat)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>MATERIAL</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='9'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_mat AS $val => $valx){
				$persen = get_persen($id_bq, $valx['id_material'], $valx['id']);
				$extra 	= get_extra($id_bq,  $valx['id_material'], $valx['id']);
				$price 			= $valx['total_price'] + ($valx['total_price'] * ($persen/100));
				$price_total 	= $price + ($price * ($extra/100));

				$SUM_MAT += $price_total;
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
					echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($price,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($price_total,2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='15'><b>TOTAL MATERIAL</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		?>
		<?php
		if(!empty($result2BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='11'>Test Name</th>
				<th class="text-center">Opt</th>
				<th class="text-center">Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no1=0;
			$SUM1=0;
			foreach($result2BTS AS $val => $valx){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';

				$Opt 	= ($valx['option_type'] == 'Y')?'YES':'NO';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='11'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Opt."</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='15'><b>TOTAL ENGINEERING COST</b></td> 
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if(!empty($result3BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='14'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no2=0;
			$SUM2=0;
			foreach($result3BTS AS $val => $valx){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='14'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='15'><b>TOTAL PACKING COST</b></td> 
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if(!empty($result4BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='8'>Country</th>
				<th class="text-center" colspan='4'>Shipping</th>
				<th class="text-center" colspan='2'>Unit Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no3=0;
			$SUM3=0;
			foreach($result4BTS AS $val => $valx){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='8'>".strtoupper($valx['country_name']);
					echo "</td>";
					echo "<td align='center' colspan='4'>".strtoupper($valx['caregory_sub'])."</td>";
					
					echo "<td align='center'>".$valx['option_type']."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='15'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if(!empty($result5BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Via</th>
				<th class="text-center" colspan='5'>Area</th>
				<th class="text-center" colspan='3'>Destination</th>
				<th class="text-center" colspan='4'>Vehicle</th>
				
				<th class="text-center">Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no4=0;
			$SUM4=0;
			foreach($result5BTS AS $val => $valx){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
					$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				}
				else{
					$Kendaraanx = strtoupper($valx['kendaraan']);
				}
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='5'>".$Areax."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Tujuanx."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='4'>".$Kendaraanx."</td>";
					
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='15'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<?php
		if(!empty($otherArray)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='16'><b>OTHER</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='13'>Description</th>
				<th class="text-center">Unit Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM_OTHER = 0;
			foreach($otherArray AS $val => $value){
				$SUM_OTHER += $value['price_total'];
				
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'  colspan='13'>".strtoupper($value['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='right'>".number_format($value['price'],2)."</td>";
					echo "<td style='vertical-align:top' align='center'>".number_format($value['qty'],2)."</td>";
					echo "<td style='vertical-align:top' align='right'>".number_format($value['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='15'><b>TOTAL OTHER</b></td>
				<td align='right'><b><?= number_format($SUM_OTHER,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<tfoot>
			<tr>
				<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='15'>TOTAL QUOTATION</th>
				<!--<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>USD</th>-->
				<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_BAUT + $SUM_PLATE + $SUM_GASKET + $SUM_LAINNYA + $SUM_OTHER, 2);?></th>
			</tr>
			<?php
			// if(!empty($rest_non_frp)){
				// echo "<tr>";
					// echo "<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='11'></th>";
					// echo "<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>IDR</th>";
					// echo "<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'>".number_format($SUM_NONFRP, 2)."</th>";
				// echo "</tr>";
			// }
			?>
		</tfoot>
		
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}
	
	#header{
		position:fixed;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
	}
	.font{
		font-family: verdana,arial,sans-serif;
		font-size:14px;
	}
	.fontheader{
		font-family: verdana,arial,sans-serif;
		font-size:13px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
	}
	
	.headX{
		background-color: #0e5ca9 !important;
		color: white;
	}
	
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #ea572b;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
	}
	
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	$html = ob_get_contents(); 
	ob_end_clean(); 
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Selling Price '.str_replace('BQ-', '', $id_bq));
	// $mpdf->AddPage('L');
	// $mpdf->SetDisplayMode('fullpage');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("SELLING PRICE ".str_replace('BQ-', '', $id_bq)." ".date('d/m/Y/H/i/s').".pdf" ,'I');