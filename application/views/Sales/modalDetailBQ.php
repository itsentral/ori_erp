<div class="table-responsive">
<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
	<tbody>
		<tr>
			<th class="text-center" colspan='2' width='16%'></th>
			<th class="text-center" width='6%'></th>
			<th class="text-center" width='6%'></th>
			<th class="text-center" width='6%'></th>
			<th class="text-center" width='8%'></th>
			<th class="text-center" width='15%'></th>
			<th class="text-center" width='10%'></th>
			<th class="text-center" width='8%'></th>
			<th class="text-center" width='7%'></th>
			<th class="text-center" width='9%'></th>
			<th class="text-center" width='11%'></th>
		</tr>
	</tbody>
	<?php
	$SUM = 0;
	if(!empty($getDetail)){
	?>
		<tbody>
			<tr>
				<td class="text-left headX HeaderHr" colspan='12'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='2' width='16%'>Item Product</th>
				<th class="text-center" width='6%'>Dim 1</th>
				<th class="text-center" width='6%'>Dim 2</th>
				<th class="text-center" width='6%'>Liner</th>
				<th class="text-center" width='8%'>Pressure</th>
				<th class="text-center" width='15%'>Specification</th>
				<th class="text-center" width='10%'>Qty</th>
				<th class="text-center" width='8%'>Unit</th>
				<th class="text-center" width='7%'>Weight (Kg)</th>
				<th class="text-center" width='9%'>Unit Price</th>
				<th class="text-center" width='11%'>Total Price (USD)</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$SUM = 0;
			$no = 0;
			foreach($getDetail AS $val => $valx){
				$no++;
				$dataSum = 0;
				if($valx['qty'] <> 0){
					$dataSum	= $valx['cost'];
				}
				$SUM += $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='2'>".strtoupper($valx['id_category'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".substr($valx['series'],6,5)."</td>";
					echo "<td align='center'>".substr($valx['series'],3,2)."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($valx['est_material'],2)." Kg</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
	<?php
	}
	$SUM_NONFRP = 0;
	if(!empty($non_frp)){
		echo "<tbody>";
			echo "<tr>";
				echo "<td class='text-left headX HeaderHr' colspan='12'><b>BQ NON FRP</b></td>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='8'>Material Name</th>";
				echo "<th class='text-center'>Qty/Berat</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		$nomorx = 0;
		foreach($non_frp AS $val => $valx){ $nomorx++;
			$SUM_NONFRP += $valx['price_total'];
			
			$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
			$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
			$nama_acc = "";
			if($valx['category'] == 'baut'){
				$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
			}
			if($valx['category'] == 'plate'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($valx['category'] == 'gasket'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($valx['category'] == 'lainnya'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
			}
				
			$qty = $valx['qty'];
			$satuan = $valx['option_type'];
			if($valx['category'] == 'plate'){
				$qty = $valx['weight'];
				$satuan = '1';
			}
			
			echo "<tr>";
				echo "<td colspan='8'>".$nama_acc."</td>";
				echo "<td align='right'>".number_format($qty,2)."</td>";
				echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
				echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
				echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
			echo "</tr>";
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='11'><b>TOTAL BQ NON FRP</b></td> ";
			echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
		echo "</tr>";
		echo "</tbody>";
	}
	$SUM_MAT = 0;
	if(!empty($material)){
		echo "<tbody>";
			echo "<tr>";
				echo "<td class='text-left headX HeaderHr' colspan='12'><b>MATERIAL</b></td>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='8'>Material Name</th>";
				echo "<th class='text-center'>Weight</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		foreach($material AS $val => $valx){
			$SUM_MAT += $valx['price_total'];
			echo "<tr>";
				echo "<td colspan='8'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
				echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
				echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
				echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
				echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
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
		<tr>
			<td class="text-left headX HeaderHr" colspan='12'><b>ENGINEERING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
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
		<tr>
			<td class="text-left headX HeaderHr" colspan='12'><b>PACKING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
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
		<tr>
			<td class="text-left headX HeaderHr" colspan='12'><b>TRUCKING EXPORT</b></td>
		</tr>
		<tr class='bg-bluexyz'>
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
		<tr>
			<td class="text-left headX HeaderHr" colspan='12'><b>TRUCKING LOKAL</b></td>
		</tr>
		<tr class='bg-bluexyz'>
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
			<tr>
				<td class="text-left headX HeaderHr" colspan='12'><b>OTHER</b></td>
			</tr>
			<tr class='bg-bluexyz'>
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
			<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP + $SUM_OTHER, 2);?></th>
		</tr>
		<?php
			// if(!empty($non_frp)){
				// echo "<tr class='HeaderHr'>";
					// echo "<th align='left' colspan='10'></th>";
					// echo "<th align='center' style='text-align:center;'>IDR</th>";
					// echo "<th align='right' style='text-align:right;'>".number_format($SUM_NONFRP, 2)."</th>";
				// echo "</tr>";
			// }
		?>
	</tfoot>
</table>
</div>
<style>
.HeaderHr{
	background-color: #ce4c00;
    color: white;
}

.bg-bluexyz{
	background-color: #05b3a3 !important;
	color : white;
}

</style>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>