
<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<tr>
			<th class="text-center" colspan='2' width='22%'></th>
			<th class="text-center" width='6%'></th>
			<th class="text-center" width='6%'></th>
			<th class="text-center" width='6%'></th>
			<th class="text-center" width='8%'></th>
			<th class="text-center" width='17%'></th>
			<th class="text-center" width='10%'></th>
			<th class="text-center" width='7%'></th>
			<th class="text-center" width='8%'></th>
			<th class="text-center" width='11%'></th>
		</tr>
	<?php
	$SUM = 0;
	if(!empty($getDetail)){ ?>
		<tbody>
			<tr>
				<td class="text-left headX HeaderHr" colspan='11'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='2' width='22%'>Item Product</th>
				<th class="text-center" width='6%'>Dim 1</th>
				<th class="text-center" width='6%'>Dim 2</th>
				<th class="text-center" width='6%'>Liner</th>
				<th class="text-center" width='8%'>Pressure</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='10%'>Unit Price</th>
				<th class="text-center" width='7%'>Qty</th>
				<th class="text-center" width='8%'>Unit</th>
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
					$dataSum	= $valx['total_deal_usd'];
				}
				$SUM += $dataSum;
				
				if($valx['product'] == 'pipe' OR $valx['product'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
                $SERIES = (!empty($GET_DET_SO[$valx['id_milik']]['series']))?$GET_DET_SO[$valx['id_milik']]['series']:'';
				echo "<tr>";
					echo "<td colspan='2'>".strtoupper($valx['product'])."</td>";
					echo "<td align='right'>".number_format($valx['dim1'])."</td>";
					echo "<td align='right'>".number_format($valx['dim2'])."</td>";
					echo "<td align='center'>".substr($SERIES,6,5)."</td>";
					echo "<td align='center'>".substr($SERIES,3,2)."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
	<?php
	}
	$SUM_NONFRP = 0;
	if(!empty($non_frp)){
		echo "<tbody>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='11'><b>BQ NON FRP</b></td>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Qty</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		foreach($non_frp AS $val => $valx){
            $QTY            = $valx['qty'];
			$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
			$TOTAL_PRICE    = $UNIT_PRICE * $QTY;

			$SUM_NONFRP += $TOTAL_PRICE;
			
			echo "<tr>";
				echo "<td colspan='7'>".get_name_acc($valx['id_material'])."</td>";
				echo "<td align='right'>".number_format($QTY,2)."</td>";
				echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
				echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
				echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
			echo "</tr>";
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='10'><b>TOTAL BQ NON FRP</b></td> ";
			echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
		echo "</tr>";
		echo "</tbody>";
	}
	$SUM_MAT = 0;
	if(!empty($material)){
		echo "<tbody>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='11'><b>MATERIAL</b></td>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Weight</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		foreach($material AS $val => $valx){
            $QTY            = $valx['qty'];
			$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
			$TOTAL_PRICE    = $UNIT_PRICE * $QTY;

			$SUM_MAT += $TOTAL_PRICE;
			echo "<tr>";
				echo "<td colspan='7'>".strtoupper($valx['nm_material'])."</td>";
				echo "<td align='right'>".number_format($QTY,2)."</td>";
				echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
				echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
				echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
			echo "</tr>";
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='10'><b>TOTAL MATERIAL</b></td> ";
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
			<td class="text-left headX HeaderHr" colspan='11'><b>ENGINEERING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='7'>Item Product</th>
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
			$QTY            = $valx['qty'];
			$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
			$TOTAL_PRICE    = $UNIT_PRICE * $QTY;
			$SUM1           += $TOTAL_PRICE;
			$no1++;
			echo "<tr>";
				echo "<td colspan='7'>".get_name('cost_project_detail','caregory_sub','id',$valx['id_milik'])."</td>";
				echo "<td align='center'>".$QTY."</td>";
				echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
				echo "<td align='right'>".$UNIT_PRICE."</td>";
				echo "<td align='right'>".$TOTAL_PRICE."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
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
			<td class="text-left headX HeaderHr" colspan='11'><b>PACKING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='9'>Category</th>
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
			$UNIT_PRICE     = $valx['total_deal_usd'];
			$TOTAL_PRICE    = $UNIT_PRICE;
			$SUM2 += $TOTAL_PRICE;
			echo "<tr>";
                echo "<td colspan='9'>".get_name('cost_project_detail','caregory_sub','id',$valx['id_milik'])."</td>";
				echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
				echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL PACKING COST</b></td>
			<td align='right'><b><?= number_format($SUM2,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	$SUM3=0;
	$SUM4=0;
	if(!empty($getTruck)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>SHIPPING</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center">Item Product</th>
			<th class="text-center" colspan='3'>Area</th>
			<th class="text-center" colspan='2'>Tujuan</th>
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
		foreach($getTruck AS $val => $valx){
            $QTY            = $valx['qty'];
			$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
			$TOTAL_PRICE    = $UNIT_PRICE * $QTY;

			$SUM4 += $TOTAL_PRICE;

            $CATEGORY_SUB   = (!empty($GET_TRUCKING[$valx['id_milik']]['category_sub']))?$GET_TRUCKING[$valx['id_milik']]['category_sub']:'-';
            $AREA_TRUCK     = (!empty($GET_TRUCKING[$valx['id_milik']]['area']))?$GET_TRUCKING[$valx['id_milik']]['area']:'-';
            $TUJUAN_TRUCK   = (!empty($GET_TRUCKING[$valx['id_milik']]['tujuan']))?$GET_TRUCKING[$valx['id_milik']]['tujuan']:'-';
            $VEHICLE_TRUCK   = (!empty($GET_TRUCKING[$valx['id_milik']]['kendaraan']))?$GET_TRUCKING[$valx['id_milik']]['kendaraan']:'';

			$Areax      = ($AREA_TRUCK == '0')?'-':strtoupper($AREA_TRUCK);
			$Tujuanx    = ($TUJUAN_TRUCK == '0')?'-':strtoupper($TUJUAN_TRUCK);
			if(strtolower($CATEGORY_SUB) == 'via laut' || strtolower($CATEGORY_SUB) == 'via darat'){
				$Kendaraanx = ($VEHICLE_TRUCK == '')?'-':strtoupper(get_name('truck','nama_truck','id',$VEHICLE_TRUCK));
			}
			else{
				$Kendaraanx = strtoupper($VEHICLE_TRUCK);
			}
			
			$no4++;
			echo "<tr>";
				echo "<td>".strtoupper($CATEGORY_SUB)."</td>";
				echo "<td align='left' colspan='3'>".$Areax."</td>";
				echo "<td align='left' colspan='2'>".$Tujuanx."</td>";
				echo "<td align='left' colspan='2'>".$Kendaraanx."</td>";
				echo "<td align='center'>".$QTY."</td>";
				echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
				echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
			<td align='right'><b><?= number_format($SUM4,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	$SUM5=0;
	if(!empty($other)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>OTHERS</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center">Item Desc</th>
			<th class="text-center" colspan='3'>Area</th>
			<th class="text-center" colspan='2'>Tujuan</th>
			<th class="text-center" colspan='2'>Kendaraan</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no5=0;
		$SUM5=0;
		foreach($other AS $val => $valx){
            $QTY            = $valx['qty'];
			$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
			$TOTAL_PRICE    = $UNIT_PRICE * $QTY;

			$SUM5 += $TOTAL_PRICE;

            $CATEGORY_SUB   = $valx['desc'];
			$no5++;
			echo "<tr>";
				echo "<td colspan='8'>".strtoupper($CATEGORY_SUB)."</td>";
				echo "<td align='center'>".$QTY."</td>";
				echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
				echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL OTHERS</b></td>
			<td align='right'><b><?= number_format($SUM5,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	?>
	<tfoot>
		<tr class='HeaderHr'>
			<th align='left' colspan='9'>TOTAL</th>
			<th align='center' style='text-align:center;'>USD</th>
			<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM5 + $SUM_MAT + $SUM_NONFRP, 2);?></th>
		</tr>
		<?php
			// if(!empty($non_frp)){
				// echo "<tr class='HeaderHr'>";
					// echo "<th align='left' colspan='9'></th>";
					// echo "<th align='center' style='text-align:center;'>IDR</th>";
					// echo "<th align='right' style='text-align:right;'>".number_format($SUM_NONFRP, 2)."</th>";
				// echo "</tr>";
			// }
		?>
	</tfoot>
</table>

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