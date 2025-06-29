
<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			<span style='color:green;'><b>MOHON CEK TERLEBIH DAHULU BERAT DAN COST SATUAN, <span style='color:red;'>UNTUK MENIMINALISIR KESALAHAN</span></b></span><br>
		</p>
	</div>
	<div class="form-group row">
		<div class='col-sm-3 '>
		   <label class='label-control'>Approve Action</label>
		   <select name='status' id='status' class='form-control input-md'>
				<option value='0'>Select Action</option>
				<option value='Y'>APPROVE</option>
				<option value='N'>REVISI</option>
			</select>
			<?php
			echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
			?>
		</div>
		<div class='col-sm-4 '>
			<label class='label-control'>Perubahan</label>          
			<?php
				echo form_textarea(array('id'=>'perubahan','name'=>'perubahan','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Perubahan'));
			?>		
		</div>
		<div class='col-sm-5 '>
			<div id='HideReject'>
				<label class='label-control'>Reject Reason</label>          
				<?php
					echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Revision reason'));
				?>		
			</div>
		</div>
		
	</div>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 0px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'approve_set_price')).' ';
	?>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center" colspan='2' width='16%'>Item Product</th>
				<th class="text-center" width='6%'>Dim 1</th>
				<th class="text-center" width='6%'>Dim 2</th>
				<th class="text-center" width='6%'>Liner</th>
				<th class="text-center" width='8%'>Pressure</th>
				<th class="text-center" width='15%'>Specification</th>
				<th class="text-center" width='10%'>Weight</th>
				<th class="text-center" width='7%'>Qty</th>
				<th class="text-center" width='8%'>Unit</th>
				<th class="text-center" width='10%'>Unit Price</th>
				<th class="text-center" width='10%'>Total Price</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$SUM = 0;
			$no = 0;
			$SUM_MAT = 0;
			foreach($getDetail AS $val => $valx){
				$no++;

				$id_milik 	= $valx['id'];
				$id_category= $valx['id_category'];
				$qty 		= $valx['qty'];
				$diameter_1 = $valx['diameter_1'];
				$diameter_2 = $valx['diameter_2'];
				$series 	= explode('-',$valx['series']);
				$liner 		= $series[1];
				$pressure 	= $series[0];
				$man_power					= $valx['man_power'];
				$man_hours					= $valx['man_hours'];
				$id_mesin					= $valx['id_mesin'];
				$total_time					= $valx['total_time'];

				$SUMMARY 	= getEstimasi_Product($id_milik,$id_category);
				$TotalBerat	= (!empty($SUMMARY['est_mat']))?$SUMMARY['est_mat'] * $qty:0;
				$TotalPrice	= (!empty($SUMMARY['est_price']))?$SUMMARY['est_price'] * $qty:0;

				$direct_labour 				= $man_hours * $valx['pe_direct_labour'] * $qty;
				$indirect_labour 			= $man_hours * $valx['pe_indirect_labour'] * $qty;
				$machine 					= $total_time * $valx['pe_machine'] * $qty;
				$mould_mandrill 			= $valx['pe_mould_mandrill'] * $qty;
				$consumable 				= $TotalBerat * $valx['pe_consumable'];

				$cost_process 				= $direct_labour + $indirect_labour + $machine + $mould_mandrill + $consumable;

				$foh_consumable 			= ($cost_process + $TotalPrice) * ($valx['pe_foh_consumable']/100);
				$foh_depresiasi 			= ($cost_process + $TotalPrice) * ($valx['pe_foh_depresiasi']/100);
				$biaya_gaji_non_produksi 	= ($cost_process + $TotalPrice) * ($valx['pe_biaya_gaji_non_produksi']/100);
				$biaya_non_produksi 		= ($cost_process + $TotalPrice) * ($valx['pe_biaya_non_produksi']/100);
				$biaya_rutin_bulanan 		= ($cost_process + $TotalPrice) * ($valx['pe_biaya_rutin_bulanan']/100);

				$est_harga		= round(($TotalPrice + $direct_labour + $indirect_labour + $machine + $mould_mandrill + $consumable + $foh_consumable + $foh_depresiasi + $biaya_gaji_non_produksi + $biaya_non_produksi + $biaya_rutin_bulanan) / $qty,2);

				
				$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
					
				$getProfit 	= $this->db->query("SELECT extra, persen FROM cost_project_detail WHERE caregory_sub='".$id_milik."' AND id_bq='".$id_bq."'")->result_array();
				$persen 	= (!empty($getProfit[0]['persen']))?floatval($getProfit[0]['persen']):0;
				$extra 		= (!empty($getProfit[0]['extra']))?floatval($getProfit[0]['extra']):0;
				
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $qty;
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
				
				$nego		= $HrgTot * ($NegoPersen/100);
				$dataSum	= $HrgTot + $nego;
				
				$SUM += $dataSum;
				
				if($id_category == 'pipe' OR $id_category == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				$SUM_MAT += $TotalBerat;
				echo "<tr>";
					echo "<td colspan='2'>".strtoupper($id_category)."</td>";
					echo "<td align='right'>".number_format($diameter_1)."</td>";
					echo "<td align='right'>".number_format($diameter_2)."</td>";
					echo "<td align='center'>".$liner."</td>";
					echo "<td align='center'>".$pressure."</td>";
					echo "<td align='left'>".spec_bq($id_milik)."</td>";
					echo "<td align='right'>".number_format($TotalBerat,3)." Kg</td>";
					echo "<td align='center'>".$qty."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum / $qty,2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='7'><b>TOTAL OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM_MAT,3);?> Kg</b></td>
				<td colspan='3'></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		<?php
		$SUM_NONFRP = 0;
		$SUM_BAUT = 0;
		if(!empty($rest_baut)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td class='bg-blue' colspan='12'><b>BAUT</b></td>";
				echo "</tr>";
				echo "<tr class='bg-blue'>";
					echo "<th class='text-center' colspan='5'>Material Name</th>";
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
					echo "<td colspan='5'>".strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx."</td>";
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
				echo "<td colspan='11'><b>TOTAL BAUT</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_BAUT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_PLATE = 0;
		if(!empty($rest_plate)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td class='bg-blue' colspan='12'><b>PLATE</b></td>";
				echo "</tr>";
				echo "<tr class='bg-blue'>";
					echo "<th class='text-center' colspan='5'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
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

				$SUM_PLATE += $price_total;
				echo "<tr>";
					echo "<td colspan='5'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
					echo "<td align='right'>".number_format($valx['berat'],2)."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($price,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($price_total,2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='11'><b>TOTAL PLATE</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_PLATE, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_GASKET = 0;
		if(!empty($rest_gasket)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td class='bg-blue' colspan='12'><b>GASKET</b></td>";
				echo "</tr>";
				echo "<tr class='bg-blue'>";
					echo "<th class='text-center' colspan='5'>Material Name</th>";
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
					echo "<td colspan='5'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
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
				echo "<td colspan='11'><b>TOTAL GASKET</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_GASKET, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_LAINNYA = 0;
		if(!empty($rest_lainnya)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td  class='bg-blue' colspan='12'><b>LAINNYA</b></td>";
				echo "</tr>";
				echo "<tr class='bg-blue'>";
					echo "<th class='text-center' colspan='5'>Material Name</th>";
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
					echo "<td colspan='5'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi)."</td>";
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
				echo "<td colspan='11'><b>TOTAL LAINNYA</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_LAINNYA, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_MAT = 0;
		if(!empty($material)){
			echo "<tbody>";
				echo "<tr class='bg-blue'>";
					echo "<td class='text-left' colspan='12'><b>MATERIAL</b></td>";
				echo "</tr>";
				echo "<tr  class='bg-blue'>";
					echo "<th class='text-center' colspan='4'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
					echo "<th class='text-center' colspan='2'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			foreach($material AS $val => $valx){
				$persen = get_persen($id_bq, $valx['id_material'], $valx['id']);
				$extra 	= get_extra($id_bq,  $valx['id_material'], $valx['id']);
				$price 			= $valx['total_price'] + ($valx['total_price'] * ($persen/100));
				$price_total 	= $price + ($price * ($extra/100));

				$SUM_MAT += $price_total;
				echo "<tr>";
					echo "<td colspan='5'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
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
				echo "<td colspan='11'><b>TOTAL MATERIAL</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		?>
		<?php
		$SUM1=0;
		if(!empty($getEngCost)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>ENGINEERING COST</b></td>
			</tr>
			<tr  class='bg-blue'>
				<th class="text-center" colspan='8'>Item Product</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no1=0;
			$SUM1=0;
			foreach($getEngCost AS $val => $valx){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='8'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL ENGINEERING COST</b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$SUM2=0;
		if(!empty($getPackCost)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>PACKING COST</b></td>
			</tr>
			<tr  class='bg-blue'>
				<th class="text-center" colspan='10'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no2=0;
			$SUM2=0;
			foreach($getPackCost AS $val => $valx){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='10'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL PACKING COST</b></td>
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$SUM3=0;
		if(!empty($getTruck)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr  class='bg-blue'>
				<th class="text-center" colspan='7'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Fumigation</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no3=0;
			$SUM3=0;
			foreach($getTruck AS $val => $valx){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['type'])."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$SUM4=0;
		if(!empty($getVia)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr  class='bg-blue'>
				<th class="text-center">Item Product</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='3'>Tujuan</th>
				<th class="text-center" colspan='2'>Kendaraan</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no4=0;
			$SUM4=0;
			foreach($getVia AS $val => $valx){
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
					echo "<td>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td align='left' colspan='3'>".$Areax."</td>";
					echo "<td align='left' colspan='3'>".$Tujuanx."</td>";
					echo "<td align='left' colspan='2'>".$Kendaraanx."</td>";
					echo "<td align='center'>".$Qty4."</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<?php 
		$SUM_OTHER = 0;
		if(!empty($otherArray)) { ?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>OTHER</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center" colspan='9'>Description</th>
				<th class="text-center">Unit Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$nomor = 0;
			
			foreach ($otherArray as $key => $value) { $nomor++;
				$SUM_OTHER += $value['price_total'];
				echo "<tr>";
					echo "<td align='left' colspan='9'>".$value['caregory_sub']."</td>";
					echo "<td align='right'>".number_format($value['price'],2)."</td>";
					echo "<td align='center'>".number_format($value['qty'],2)."</td>";
					echo "<td align='right'>".number_format($value['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<th colspan='11'>TOTAL OTHER</th>
				<th class="text-right"><?=number_format($SUM_OTHER,2);?></th>
			</tr>
		</tbody>
		<?php } ?>
		<tfoot>
			<tr class='HeaderHr'>
				<th align='left' colspan='10'>TOTAL</th>
				<th align='center' style='text-align:center;'>USD</th>
				<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_BAUT + $SUM_PLATE + $SUM_GASKET + $SUM_LAINNYA + $SUM_OTHER, 2);?></th>
			</tr>
			<?php
				if(!empty($non_frp)){
					echo "<tr class='HeaderHr'>";
						echo "<th align='left' colspan='10'></th>";
						echo "<th align='center' style='text-align:center;'>IDR</th>";
						echo "<th align='right' style='text-align:right;'>".number_format($SUM_NONFRP, 2)."</th>";
					echo "</tr>";
				}
			?>
		</tfoot>
	</table>
</div>

<script>
	swal.close();

	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
			}
		});
	});

</script>