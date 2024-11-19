
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b>LINER THICKNESS</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Material Est</td>
						<td class="text-right" width='10%'>Cost Est</td>
						<td class="text-right" width='10%'>Material Real</td>
						<td class="text-right" width='10%'>Cost Real</td>
						<td class="text-right" width='10%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg1	= 0;
					$sumTotDet1Pr1	= 0;
					$sumTotDet1Kg	= 0;
					$sumTotDet1Pr	= 0;
					foreach($restDetail1 AS $val => $valx){
						$sumTotDet1Kg1 += $valx['est_material'];
						$sumTotDet1Pr1 += $valx['est_harga'];
						$sumTotDet1Kg += $valx['real_material'];
						$sumTotDet1Pr += $valx['real_harga'];
				
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
						<?php
					}
					$sumTotRes1Kg1	= 0;
					$sumTotRes1Pr1	= 0;
					$sumTotRes1Kg	= 0;
					$sumTotRes1Pr	= 0; 
					foreach($restResin1 AS $val => $valx){
						$sumTotRes1Kg1 += $valx['est_material'];
						$sumTotRes1Pr1 += $valx['est_harga'];
						$sumTotRes1Kg += $valx['real_material'];
						$sumTotRes1Pr += $valx['real_harga'];
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
					<?php
					}
					$TotLinerKg1	= $sumTotDet1Kg1 + $sumTotRes1Kg1;
					$TotLinerPr1	= $sumTotDet1Pr1 + $sumTotRes1Pr1;
					
					$TotLinerKg		= $sumTotDet1Kg + $sumTotRes1Kg;
					$TotLinerPr		= $sumTotDet1Pr + $sumTotRes1Pr;
					
					$TotLinerKgPr 		= 0;
					
					if($TotLinerKg1 != 0){
						$TotLinerKgPr 	= number_format((($TotLinerPr/$TotLinerPr1)*100));
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL LINER</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg1, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr1, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= $TotLinerKgPr;?> %</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$TotLinerKg2N11	= 0;
	$TotLinerPr2N11	= 0;
	$TotLinerKg2N1	= 0;
	$TotLinerPr2N1	= 0;
	
	$TotLinerKg2N21	= 0;
	$TotLinerPr2N21	= 0;
	$TotLinerKg2N2	= 0;
	$TotLinerPr2N2	= 0;
	if(!empty($restDetail2N1)){
		?>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='5'><b>NECK 1 THICKNESS</b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Material Est</td>
							<td class="text-right" width='10%'>Cost Est</td>
							<td class="text-right" width='10%'>Material Real</td>
							<td class="text-right" width='10%'>Cost Real</td>
							<td class="text-right" width='10%'>Selisih</td>
						</tr>
						<?php
						$sumTotDet1Kg2N11	= 0;
						$sumTotDet1Pr2N11	= 0;
						$sumTotDet1Kg2N1	= 0;
						$sumTotDet1Pr2N1	= 0;
						foreach($restDetail2N1 AS $val => $valx){
							$sumTotDet1Kg2N11 += $valx['est_material'];
							$sumTotDet1Pr2N11 += $valx['est_harga'];
							$sumTotDet1Kg2N1 += $valx['real_material'];
							$sumTotDet1Pr2N1 += $valx['real_harga'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
						</tr>
							<?php
						}
						$sumTotRes1Kg2N11	= 0;
						$sumTotRes1Pr2N11	= 0;
						$sumTotRes1Kg2N1	= 0;
						$sumTotRes1Pr2N1	= 0;
						foreach($restResin2N1 AS $val => $valx){
							$sumTotRes1Kg2N11 += $valx['est_material'];
							$sumTotRes1Pr2N11 += $valx['est_harga'];
							$sumTotRes1Kg2N1 += $valx['real_material'];
							$sumTotRes1Pr2N1 += $valx['real_harga'];
						?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
						</tr>
						<?php
						}
						$TotLinerKg2N11	= $sumTotDet1Kg2N11 + $sumTotRes1Kg2N11;
						$TotLinerPr2N11	= $sumTotDet1Pr2N11 + $sumTotRes1Pr2N11;
						$TotLinerKg2N1	= $sumTotDet1Kg2N1 + $sumTotRes1Kg2N1;
						$TotLinerPr2N1	= $sumTotDet1Pr2N1 + $sumTotRes1Pr2N1;
						
						$TotLinerKgPrN1 		= 0;
					
						if($TotLinerKg2N11 != 0){
							$TotLinerKgPrN1 	= number_format((($TotLinerKg2N1/$TotLinerKg2N11)*100));
						}
						?>
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL NECK 1 THICKNESS</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2N11, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2N11, 2);?></b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2N1, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2N1, 2);?></b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= $TotLinerKgPrN1;?> %</b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='5'><b>NECK 2 THICKNESS</b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Material Est</td>
							<td class="text-right" width='10%'>Cost Est</td>
							<td class="text-right" width='10%'>Material Real</td>
							<td class="text-right" width='10%'>Cost Real</td>
							<td class="text-right" width='10%'>Selisih</td>
						</tr>
						<?php
						$sumTotDet1Kg2N21	= 0;
						$sumTotDet1Pr2N21	= 0;
						$sumTotDet1Kg2N2	= 0;
						$sumTotDet1Pr2N2	= 0;
						foreach($restDetail2N2 AS $val => $valx){
							$sumTotDet1Kg2N21 += $valx['est_material'];
							$sumTotDet1Pr2N21 += $valx['est_harga'];
							$sumTotDet1Kg2N2 += $valx['real_material'];
							$sumTotDet1Pr2N2 += $valx['real_harga'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
						</tr>
							<?php
						}
						$sumTotRes1Kg2N21	= 0;
						$sumTotRes1Pr2N21	= 0;
						$sumTotRes1Kg2N2	= 0;
						$sumTotRes1Pr2N2	= 0;
						foreach($restResin2N2 AS $val => $valx){
							$sumTotRes1Kg2N21 += $valx['est_material'];
							$sumTotRes1Pr2N21 += $valx['est_harga'];
							$sumTotRes1Kg2N2 += $valx['real_material'];
							$sumTotRes1Pr2N2 += $valx['real_harga'];
						?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
							<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
							<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
						</tr>
						<?php
						}
						$TotLinerKg2N21	= $sumTotDet1Kg2N21 + $sumTotRes1Kg2N21;
						$TotLinerPr2N21	= $sumTotDet1Pr2N21 + $sumTotRes1Pr2N21;
						$TotLinerKg2N2	= $sumTotDet1Kg2N2 + $sumTotRes1Kg2N2;
						$TotLinerPr2N2	= $sumTotDet1Pr2N2 + $sumTotRes1Pr2N2;
						
						$TotLinerKgPrN2 		= 0;
					
						if($TotLinerKg2N21 != 0){
							$TotLinerKgPrN2 	= number_format((($TotLinerKg2N2/$TotLinerKg2N21)*100));
						}
						
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL NECK 2 THICKNESS</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2N21, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2N21, 2);?></b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2N2, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2N2, 2);?></b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= $TotLinerKgPrN2;?> %</b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<?php } ?>
	
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b>STRUCTURE THICKNESS</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Material Est</td>
						<td class="text-right" width='10%'>Cost Est</td>
						<td class="text-right" width='10%'>Material Real</td>
						<td class="text-right" width='10%'>Cost Real</td>
						<td class="text-right" width='10%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg21	= 0;
					$sumTotDet1Pr21	= 0;
					$sumTotDet1Kg2	= 0;
					$sumTotDet1Pr2	= 0;
					foreach($restDetail2 AS $val => $valx){
						$sumTotDet1Kg21 += $valx['est_material'];
						$sumTotDet1Pr21 += $valx['est_harga'];
						$sumTotDet1Kg2 += $valx['real_material'];
						$sumTotDet1Pr2 += $valx['real_harga'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
						<?php
					}
					$sumTotRes1Kg21	= 0;
					$sumTotRes1Pr21	= 0;
					$sumTotRes1Kg2	= 0;
					$sumTotRes1Pr2	= 0;
					foreach($restResin2 AS $val => $valx){
						$sumTotRes1Kg21 += $valx['est_material'];
						$sumTotRes1Pr21 += $valx['est_harga'];
						$sumTotRes1Kg2 += $valx['real_material'];
						$sumTotRes1Pr2 += $valx['real_harga'];
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
					<?php
					}
					$TotLinerKg21	= $sumTotDet1Kg21 + $sumTotRes1Kg21;
					$TotLinerPr21	= $sumTotDet1Pr21 + $sumTotRes1Pr21;
					$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotRes1Kg2;
					$TotLinerPr2	= $sumTotDet1Pr2 + $sumTotRes1Pr2;
					
					$TotLinerKgPrN21 		= 0;
				
					if($TotLinerKg21 != 0){
						$TotLinerKgPrN21 	= number_format((($TotLinerKg2/$TotLinerKg21)*100));
					}
						
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL STRUCTURE</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg21, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr21, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= $TotLinerKgPrN21;?> %</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php 
	$TotLinerKg31	= 0;
	$TotLinerPr31	= 0;
	$TotLinerKg3	= 0;
	$TotLinerPr3	= 0;
	if(!empty($restDetail3)){ ?>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b>EXTERNAL THICKNESS</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Material Est</td>
						<td class="text-right" width='10%'>Cost Est</td>
						<td class="text-right" width='10%'>Material Real</td>
						<td class="text-right" width='10%'>Cost Real</td>
						<td class="text-right" width='10%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg31	= 0;
					$sumTotDet1Pr31	= 0;
					$sumTotDet1Kg3	= 0;
					$sumTotDet1Pr3	= 0;
					foreach($restDetail3 AS $val => $valx){
						$sumTotDet1Kg31 += $valx['est_material'];
						$sumTotDet1Pr31 += $valx['est_harga'];
						$sumTotDet1Kg3 += $valx['real_material'];
						$sumTotDet1Pr3 += $valx['real_harga'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
						<?php
					}
					$sumTotRes1Kg31	= 0;
					$sumTotRes1Pr31	= 0;
					$sumTotRes1Kg3	= 0;
					$sumTotRes1Pr3	= 0;
					foreach($restResin3 AS $val => $valx){
						$sumTotRes1Kg31 += $valx['est_material'];
						$sumTotRes1Pr31 += $valx['est_harga'];
						$sumTotRes1Kg3 += $valx['real_material'];
						$sumTotRes1Pr3 += $valx['real_harga'];
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
					<?php
					}
					$TotLinerKg31	= $sumTotDet1Kg31 + $sumTotRes1Kg31;
					$TotLinerPr31	= $sumTotDet1Pr31 + $sumTotRes1Pr31;
					$TotLinerKg3	= $sumTotDet1Kg3 + $sumTotRes1Kg3;
					$TotLinerPr3	= $sumTotDet1Pr3 + $sumTotRes1Pr3;
					
					$TotLinerKgPr31 		= 0;
				
					if($TotLinerKg31 != 0){
						$TotLinerKgPr31 	= number_format((($TotLinerKg3/$TotLinerKg31)*100));
					}
					
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL EXTERNAL</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg31, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr31, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg3, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr3, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= $TotLinerKgPr31;?> %</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b>TOPCOAT</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Material Est</td>
						<td class="text-right" width='10%'>Cost Est</td>
						<td class="text-right" width='10%'>Material Real</td>
						<td class="text-right" width='10%'>Cost Real</td>
						<td class="text-right" width='10%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg41	= 0;
					$sumTotDet1Pr41	= 0;
					$sumTotDet1Kg4	= 0;
					$sumTotDet1Pr4	= 0;
					foreach($restDetail4 AS $val => $valx){
						$sumTotDet1Kg41 += $valx['est_material'];
						$sumTotDet1Pr41 += $valx['est_harga'];
						$sumTotDet1Kg4 += $valx['real_material'];
						$sumTotDet1Pr4 += $valx['real_harga'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
						<td class="text-right" style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
						<td class="text-right" style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
					</tr>
						<?php
					}
					$TotLinerKg41	= $sumTotDet1Kg41;
					$TotLinerPr41	= $sumTotDet1Pr41;
					$TotLinerKg4	= $sumTotDet1Kg4;
					$TotLinerPr4	= $sumTotDet1Pr4;
					if($TotLinerPr4 == 0 AND $TotLinerPr41 == 0){
						$hasilx = 0;
					}
					else{
						$hasilx = (($TotLinerPr4/$TotLinerPr41)*100);
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL TOPCOAT</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg41, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr41, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg4, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr4, 2);?></b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx);?> %</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<?php
					$TotFinalEstMat		= $TotLinerKg1 + $TotLinerKg2N11 + $TotLinerKg2N21 + $TotLinerKg21 + $TotLinerKg31 + $TotLinerKg41;
					$TotFinalEstCost	= $TotLinerPr1 + $TotLinerPr2N11 + $TotLinerPr2N21 + $TotLinerPr21 + $TotLinerPr31 + $TotLinerPr41;
					$TotFinalRealMat	= $TotLinerKg + $TotLinerKg2N1 + $TotLinerKg2N2 + $TotLinerKg2 + $TotLinerKg3 + $TotLinerKg4;
					$TotFinalRealCost	= $TotLinerPr + $TotLinerPr2N1 + $TotLinerPr2N2 + $TotLinerPr2 + $TotLinerPr3 + $TotLinerPr4;
					
					if($TotFinalRealCost == 0 AND $TotFinalEstCost == 0){
						$hasilxFinal = 0;
					}
					else{
						$hasilxFinal = (($TotFinalRealCost/$TotFinalEstCost)*100);
					}
					?>
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan=2'><b>SUM TOTAL</b></td>
						<td class="text-right" width='10%' style='background-color: #4edcc1;'><b><?= number_format((!empty($TotFinalEstMat)?$TotFinalEstMat:0), 3);?> Kg</b></td>
						<td class="text-right" width='10%' style='background-color: #4edcc1;'><b><?= number_format((!empty($TotFinalEstCost)?$TotFinalEstCost:0), 2);?></b></td>
						<td class="text-right" width='10%' style='background-color: #4edcc1;'><b><?= number_format((!empty($TotFinalRealMat)?$TotFinalRealMat:0), 3);?> Kg</b></td>
						<td class="text-right" width='10%' style='background-color: #4edcc1;'><b><?= number_format((!empty($TotFinalRealCost)?$TotFinalRealCost:0), 2);?></b></td>
						<td class="text-right" width='10%' style='background-color: #4edcc1;'><b><?= number_format($hasilxFinal);?> %</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	
	<script>
		$(document).ready(function(){
			swal.close();
		});
	</script>
	