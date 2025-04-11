
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left"><u>Component Id</u></td>
						<td class="text-left" colspan='5'><b><?= $restHeader[0]['id_product']; ?></b></td>
					</tr>
					<?php
					$T1 = "";
					$T2 = "";
					$T3 = "";
					if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint'){
						$T1 = "| Estimasi";
						$T2 = " | ".floatval($restHeader[0]['est'])." mm";
						$T3 = " || ".$restHeader[0]['stiffness']." || ".$restHeader[0]['criminal_barier']." || ".$restHeader[0]['vacum_rate']." || ".$restHeader[0]['aplikasi_product'];
					}
					?>
					<tr>
						<td class="text-left"><u>Component Group</u></td>
						<td class="text-left" colspan='5'><?= strtoupper($restHeader[0]['parent_product'].$T3); ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Spesification</u></td>
						<td class="text-left" colspan='5'><?= spec_bq($restHeader[0]['id_milik']);?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Thickness Design <?=$T1;?></u></td>
						<td class="text-left" width='20%'><?= (substr($restHeader[0]['parent_product'],-5)=='joint')?floatval($restHeader[0]['pipe_thickness']):floatval($restHeader[0]['design'])." mm"; ?> <?=$T2;?></td>

						<td class="text-left" width='15%'></td>
						<td class="text-left" width='15%'></td>

						<td class="text-left"><u></u></td>
						<td class="text-left"></td>
					</tr>
					<?php
					if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint')
					{
						?>
						<tr>
							<td class="text-left"><u>Max Min Tolerance</u></td>
							<td class="text-left"><?= isset($minmax)?$minmax_v:($restHeader[0]['max_toleransi'] * 100)." % / ".($restHeader[0]['min_toleransi'] * 100)."%"; ?></td>
							<td class="text-left"><u></u></td>
							<td class="text-left"></td>
							<td class="text-left"></td>
							<td class="text-left"></td>
						</tr>
						<tr>
							<td class="text-left"><u>Waste</u></td>
							<td class="text-left"><?=floatval($restHeader[0]['waste']); ?></td>
							<td class="text-left" width='15%'></td>
							<td class="text-left" width='15%'></td>
							<td class="text-left"><u></u></td>
							<td class="text-left"></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Total/Kg</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Sub Total/Kg</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
				</thead>
				<?php
					$SUM_W = 0;
					$SUM_P = 0;
					foreach($detail AS $val => $valx){
						$SUM_W += $valx['last_weight'] * $qty;
						$SUM_P += ($valx['last_weight'] * $valx['price_mat']) * $qty;
						echo "<tr>";
							echo "<td>".$valx['nm_category']."</td>";
							echo "<td>".$valx['nm_material']."</td>";
							echo "<td class='text-right'>".number_format($valx['last_weight'],3)."</td>";
							echo "<td class='text-right'>".number_format($valx['price_mat'],2)."</td>";
							echo "<td class='text-right'>".number_format($valx['last_weight'] * $qty,3)." Kg</td>";
							echo "<td class='text-right'>".number_format(($valx['last_weight'] * $valx['price_mat']) * $qty,2)."</td>";
						echo "<tr>";
					}
					echo "<tr>";
						echo "<td colspan='4'><b>TOTAL</b></td>";
						echo "<td class='text-right'><b>".number_format($SUM_W,3)." Kg</b></td>";
						echo "<td class='text-right'><b>".number_format($SUM_P,2)."</b></td>";
					echo "</tr>";
				?>
			</table>
		</div>
	</div>
	
	
	<script>
		$(document).ready(function(){
			swal.close();
		});
	</script> 