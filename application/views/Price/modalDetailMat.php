<?php
	$id_product = $this->uri->segment(3);
	$id_milik 	= $this->uri->segment(4);
	$qty 		= floatval($this->uri->segment(5));
	$length 	= $this->uri->segment(6);
	$id_bq 		= $this->uri->segment(7); 	
	// echo $id_bq;
	//JOINT
	if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'branch joint' OR $restHeader[0]['parent_product'] == 'field joint'){
		$qDetail1Joint			= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='GLASS' AND a.id_material <> 'MTL-1903000'";
		$qDetail2Joint			= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000'";
		$qDetail2JointAdd		= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000'";
		
		$restDetail1Joint		= $this->db->query($qDetail1Joint)->result_array();
		$restDetail2Joint		= $this->db->query($qDetail2Joint)->result_array();
		$restDetail2JointAdd	= $this->db->query($qDetail2JointAdd)->result_array();
	}
	
?>
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
						<td class="text-left" colspan='5'><?= spec_bq($id_milik);?></td>
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
						
						if ($restHeader[0]['parent_product'] == 'flange mould' || $restHeader[0]['parent_product'] == 'flange slongsong' || $restHeader[0]['parent_product'] == 'colar' || $restHeader[0]['parent_product'] == 'colar slongsong')
						{
						?>
						<tr>
							<td class="text-left"><u>Length | OD | BCD</u></td>
							<td class="text-left" colspan='5'><?=floatval($restHeader[0]['panjang_neck_1']); ?> | <?=floatval($restHeader[0]['flange_od']); ?> | <?=floatval($restHeader[0]['flange_bcd']); ?></td>
						</tr>
						<?php } ?>
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
	<?php
		if($restHeader[0]['parent_product'] != 'shop joint' AND $restHeader[0]['parent_product'] != 'branch joint' AND $restHeader[0]['parent_product'] != 'field joint'){
	
	if(!empty($restDetail['LINER THIKNESS / CB']) OR !empty($restDetailAdd['LINER THIKNESS / CB']) OR !empty($restResin['LINER THIKNESS / CB'])){
		$judul = "";
		if(!empty($restResin['LINER THIKNESS / CB'])){
			$judul = $restResin['LINER THIKNESS / CB'][0]['detail_name'];
		}
		if(!empty($restDetail['LINER THIKNESS / CB'])){
			$judul = $restDetail['LINER THIKNESS / CB'][0]['detail_name'];
		}
		if(!empty($restDetailAdd['LINER THIKNESS / CB'])){
			$judul = $restDetailAdd['LINER THIKNESS / CB'][0]['detail_name'];
		}
	?>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b><?= $judul; ?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Total/Kg</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
					</tr>
					<?php
					$sumTotDet1	= 0;
					$sumTotDet1Kg	= 0;
					foreach($restDetail['LINER THIKNESS / CB'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet1 += $TotPrice;
						$sumTotDet1Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes1	= 0;
					$sumTotRes1Kg	= 0;
					foreach($restResin['LINER THIKNESS / CB'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes1 += $TotPrice;
						$sumTotRes1Kg += $valx['last_cost'] * $qty;
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus1 = 0;
					$sumTotPlus1Kg = 0;
					foreach($restDetailPlus['LINER THIKNESS / CB'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus1 += $TotPrice;
						$sumTotPlus1Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd1 = 0;
					$sumTotAdd1Kg = 0;
					if(!empty($restDetailAdd['LINER THIKNESS / CB'])){
						$sumTotAdd1 = 0;
						$sumTotAdd1Kg = 0;
						$sumTotAdd1Pr = 0;
						foreach($restDetailAdd['LINER THIKNESS / CB'] AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd1 += $TotPrice;
							$sumTotAdd1Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotLiner	= $sumTotDet1 + $sumTotRes1 + $sumTotAdd1 + $sumTotPlus1;
					$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg + $sumTotAdd1Kg + $sumTotPlus1Kg;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL LINER PRICE</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
						<td class="text-right"  style='background-color: #ffc4da;'><b><?= number_format($TotLiner, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- FLANGE -->
	<?php
	}

	$TotStructureN1		= 0;
	$TotStructureKgN1	= 0;
	$TotStructureN2		= 0;
	$TotStructureKgN2	= 0;
	if($restHeader[0]['parent_product'] == 'flange mould' OR $restHeader[0]['parent_product'] == 'flange slongsong' OR $restHeader[0]['parent_product'] == 'colar' OR $restHeader[0]['parent_product'] == 'colar slongsong'){
		?>
			<div class="box box-success">
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody>
							<tr>
								<td class="text-left" colspan='12'><b><?= $restDetail['STRUKTUR NECK 1'][0]['detail_name']; ?></b></td>
							</tr>
							<tr class='bg-blue'>
								<td class="text-left" width='15%'>Category Name</td>
								<td class="text-left">Material Name</td>
								<td class="text-right" width='10%'>Total/Kg</td>
								<td class="text-right" width='10%'>Price (USD)</td>
								<td class="text-right" width='10%'>Sub Price (USD)</td>
							</tr>
							<?php
							$sumTotDet2N1 = 0;
							$sumTotDet2KgN1 = 0;
							foreach($restDetail['STRUKTUR NECK 1'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotDet2N1 += $TotPrice;
								$sumTotDet2KgN1 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
							$sumTotRes2N1 = 0;
							$sumTotRes2KgN1 = 0;
							foreach($restResin['STRUKTUR NECK 1'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotRes2N1 += $TotPrice;
								$sumTotRes2KgN1 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
							<?php
							}
							$sumTotPlus2N1 = 0;
							$sumTotPlus2KgN1 = 0;
							foreach($restDetailPlus['STRUKTUR NECK 1'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotPlus2N1 += $TotPrice;
								$sumTotPlus2KgN1 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
							?>
						</tbody>
						<tbody>
							<?php
							$sumTotAdd2N1 = 0;
							$sumTotAdd2KgN1 = 0;
							if(!empty($restDetailAdd['STRUKTUR NECK 1'])){
								$sumTotAdd2N1 = 0;
								foreach($restDetailAdd['STRUKTUR NECK 1'] AS $val => $valx){
									$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
									$warna 	= "";
									$backg	= "#2bff9d";
									if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
										$warna 	= "white";
										$backg	= "black";
									}
									$sumTotAdd2N1 += $TotPrice;
									$sumTotAdd2KgN1 += $valx['last_cost'] * $qty;
									?>
								<tr>
									<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
									<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
									<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
									<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
									<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
								</tr>
									<?php
								}
							}
							$TotStructureN1	+= $sumTotDet2N1 + $sumTotRes2N1 + $sumTotAdd2N1 + $sumTotPlus2N1;
							$TotStructureKgN1	+= $sumTotDet2KgN1 + $sumTotRes2KgN1 + $sumTotAdd2KgN1 + $sumTotPlus2KgN1;
							?> 
							
							<tr style='background-color: #4edcc1;'>
								<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 1 PRICE</b></td>
								<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN1, 3);?> Kg</b></td>
								<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
								<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructureN1, 2);?></b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="box box-success">
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody>
							<tr>
								<td class="text-left" colspan='12'><b><?= $restDetail['STRUKTUR NECK 2'][0]['detail_name']; ?></b></td>
							</tr>
							<tr class='bg-blue'>
								<td class="text-left" width='15%'>Category Name</td>
								<td class="text-left">Material Name</td>
								<td class="text-right" width='10%'>Total/Kg</td>
								<td class="text-right" width='10%'>Price (USD)</td>
								<td class="text-right" width='10%'>Sub Price (USD)</td>
							</tr>
							<?php
							$sumTotDet2N2 = 0;
							$sumTotDet2KgN2 = 0;
							foreach($restDetail['STRUKTUR NECK 2'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotDet2N2 += $TotPrice;
								$sumTotDet2KgN2 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
							$sumTotRes2N2 = 0;
							$sumTotRes2KgN2 = 0;
							foreach($restResin['STRUKTUR NECK 2'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotRes2N2 += $TotPrice;
								$sumTotRes2KgN2 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
							<?php
							}
							$sumTotPlus2N2 = 0;
							$sumTotPlus2KgN2 = 0;
							foreach($restDetailPlus['STRUKTUR NECK 2'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotPlus2N2 += $TotPrice;
								$sumTotPlus2KgN2 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
							?>
						</tbody>
						<tbody>
							<?php
							$sumTotAdd2N2 = 0;
							$sumTotAdd2KgN2 = 0;
							if(!empty($restDetailAdd['STRUKTUR NECK 2'])){
								$sumTotAdd2 = 0;
								foreach($restDetailAdd['STRUKTUR NECK 2'] AS $val => $valx){
									$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
									$warna 	= "";
									$backg	= "#2bff9d";
									if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
										$warna 	= "white";
										$backg	= "black";
									}
									$sumTotAdd2N2 += $TotPrice;
									$sumTotAdd2KgN2 += $valx['last_cost'] * $qty;
									?>
								<tr>
									<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
									<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
									<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
									<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
									<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
								</tr>
									<?php
								}
							}
							$TotStructureN2	+= $sumTotDet2N2 + $sumTotRes2N2 + $sumTotAdd2N2 + $sumTotPlus2N2;
							$TotStructureKgN2	+= $sumTotDet2KgN2 + $sumTotRes2KgN2 + $sumTotAdd2KgN2 + $sumTotPlus2KgN2;
							?> 
							
							<tr style='background-color: #4edcc1;'>
								<td class="text-left" colspan='2'><b>TOTAL STRUCTURE PRICE</b></td>
								<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN2, 3);?> Kg</b></td>
								<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
								<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructureN2, 2);?></b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php
	}
	$TotStructure	= 0;
	$TotStructureKg	= 0;
	if(!empty($restDetail['STRUKTUR THICKNESS']) OR !empty($restResin['STRUKTUR THICKNESS'])){
		if(!empty($restDetail['STRUKTUR THICKNESS'])){
			$judul2 = $restDetail['STRUKTUR THICKNESS'][0]['detail_name'];
		}
		if(!empty($restResin['STRUKTUR THICKNESS'])){
			$judul2 = $restResin['STRUKTUR THICKNESS'][0]['detail_name'];
		}
		
		?>
		
		<div class="box box-success">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='12'><b><?= $judul2; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Total/Kg</td>
							<td class="text-right" width='10%'>Price (USD)</td>
							<td class="text-right" width='10%'>Sub Price (USD)</td>
						</tr>
						<?php
						$sumTotDet2 = 0;
						$sumTotDet2Kg = 0;
						foreach($restDetail['STRUKTUR THICKNESS'] AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet2 += $TotPrice;
							$sumTotDet2Kg += $valx['last_cost'] * $qty;
							?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
							<?php
						}
						$sumTotRes2 = 0;
						$sumTotRes2Kg = 0;
						foreach($restResin['STRUKTUR THICKNESS'] AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotRes2 += $TotPrice;
							$sumTotRes2Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
						<?php
						}
						$sumTotPlus2 = 0;
						$sumTotPlus2Kg = 0;
						foreach($restDetailPlus['STRUKTUR THICKNESS'] AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotPlus2 += $TotPrice;
							$sumTotPlus2Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}

						$sumTotAdd2 = 0;
						$sumTotAdd2Kg = 0;
						if(!empty($restDetailAdd['STRUKTUR THICKNESS'])){
							$sumTotAdd2 = 0;
							foreach($restDetailAdd['STRUKTUR THICKNESS'] AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotAdd2 += $TotPrice;
								$sumTotAdd2Kg += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
						}
						$TotStructure	= $sumTotDet2 + $sumTotRes2 + $sumTotAdd2 + $sumTotPlus2;
						$TotStructureKg	= $sumTotDet2Kg + $sumTotRes2Kg + $sumTotAdd2Kg + $sumTotPlus2Kg;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL STRUCTURE PRICE</b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKg, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
							<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructure, 2);?></b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
	$TotExternal = 0;
	$TotExternalKg = 0;
	$TotExternalPr = 0;
	if(!empty($restDetail['EXTERNAL LAYER THICKNESS'])){
	?>
	<div class="box box-warning">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b><?= $restDetail['EXTERNAL LAYER THICKNESS'][0]['detail_name']; ?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Total/Kg</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
					</tr>
					<?php
					$sumTotDet3 =0;
					$sumTotDet3Kg =0;
					foreach($restDetail['EXTERNAL LAYER THICKNESS'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet3 += $TotPrice;
						$sumTotDet3Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes3 =0;
					$sumTotRes3Kg =0;
					foreach($restResin['EXTERNAL LAYER THICKNESS'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes3 += $TotPrice;
						$sumTotRes3Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotPlus3 =0;
					$sumTotPlus3Kg =0;
					foreach($restDetailPlus['EXTERNAL LAYER THICKNESS'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus3 += $TotPrice;
						$sumTotPlus3Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					
					$sumTotAdd3 =0;
					$sumTotAdd3Kg =0;
					if(!empty($restDetailAdd['EXTERNAL LAYER THICKNESS'])){
						$sumTotAdd3 =0;
						$sumTotAdd3Kg =0;
						foreach($restDetailAdd['EXTERNAL LAYER THICKNESS'] AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd3 += $TotPrice;
							$sumTotAdd3Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotExternal	= $sumTotDet3 + $sumTotRes3 + $sumTotAdd3 + $sumTotPlus3;
					$TotExternalKg	= $sumTotDet3Kg + $sumTotRes3Kg + $sumTotAdd3Kg + $sumTotPlus3Kg;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL EXTERNAL PRICE</b></td>
						<td class="text-right"><b><?= number_format($TotExternalKg, 3);?> Kg</b></td>
						<td class="text-right"><b></b></td>
						<td class="text-right"><b><?= number_format($TotExternal, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	}
	$TotCoat = 0;
	$TotCoatKg = 0;
	if(!empty($restDetailPlus['TOPCOAT'])){
	?>
	<div class="box box-danger">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b><?= $restDetailPlus['TOPCOAT'][0]['detail_name']; ?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Total/Kg</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
					</tr>
					<?php
					$sumTotPlus4 = 0;
					$sumTotPlus4Kg = 0;
					foreach($restDetailPlus['TOPCOAT'] AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus4 += $TotPrice;
						$sumTotPlus4Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd4 = 0;
					$sumTotAdd4Kg = 0;
					if(!empty($restDetailAdd['TOPCOAT'])){
						$sumTotAdd4 = 0;
						$sumTotAdd4Kg = 0;
						foreach($restDetailAdd['TOPCOAT'] AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd4 += $TotPrice;
							$sumTotAdd4Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotCoat	= $sumTotAdd4 + $sumTotPlus4;
					$TotCoatKg	= $sumTotAdd4Kg + $sumTotPlus4Kg;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL TOPCOAT PRICE</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotCoatKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotCoat, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
		}
	
	$TotLinerKgx 	= (!empty($TotLinerKg))?$TotLinerKg:0;
	$TotLinerx 		= (!empty($TotLiner))?$TotLiner:0;
	?>
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr style='background-color: #4edcc1; font-size: 14px; color:black;'>
						<td class="text-left" width='15%'><b>TOTAL ALL</b></td>
						<td class="text-left"></td>
						<td class="text-right" width='10%' style='background-color: bisque;'><b><?= number_format($TotLinerKgx + $TotStructureKg + $TotStructureKgN1 + $TotStructureKgN2 + $TotExternalKg + $TotCoatKg, 3);?> Kg</b></td>
						<td class="text-right" width='10%' style='background-color: #2bff9d;'><b></b></td>
						<td class="text-right" width='10%' style='background-color: #ffc4da;'><b><?= number_format($TotLinerx + $TotStructure + $TotStructureN1 + $TotStructureN2 + $TotExternal + $TotCoat, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php }
		if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'branch joint' OR $restHeader[0]['parent_product'] == 'field joint'){
	?>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='5'><b><?= $restDetail1Joint[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Total/Kg</td>
							<td class="text-right" width='10%'>Price (USD)</td>
							<td class="text-right" width='10%'>Sub Price (USD)</td>
						</tr>
						<?php
						$sumTotDet1	= 0;
						$sumTotDet1Kg	= 0;
						foreach($restDetail1Joint AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet1 += $TotPrice;
							$sumTotDet1Kg += $valx['last_cost'] * $qty;
							
							$qName			= "SELECT category FROM raw_categories WHERE id_category='".$valx['id_category']."' ";
							$restName		= $this->db->query($qName)->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($restName[0]['category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
						
						$TotLiner	= $sumTotDet1;
						$TotLinerKg	= $sumTotDet1Kg;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL GLASS</b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
							<td class="text-right"  style='background-color: #ffc4da;'><b><?= number_format($TotLiner, 2);?></b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="box box-success">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='5'><b><?= $restDetail2Joint[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Total/Kg</td>
							<td class="text-right" width='10%'>Price (USD)</td>
							<td class="text-right" width='10%'>Sub Price (USD)</td>
						</tr>
						<?php
						$sumTotDet12	= 0;
						$sumTotDet1Kg2	= 0;
						foreach($restDetail2Joint AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet12 += $TotPrice;
							$sumTotDet1Kg2 += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
						
						
						$sumTotDet12Add	= 0;
						$sumTotDet1Kg2Add	= 0;
						foreach($restDetail2JointAdd AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_mat']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet12Add += $TotPrice;
							$sumTotDet1Kg2Add += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
						
						$TotLiner2	= $sumTotDet12 + $sumTotDet12Add;
						$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotDet1Kg2Add;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL <?= $restDetail2Joint[0]['detail_name']; ?></b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg2, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b></b></td>
							<td class="text-right"  style='background-color: #ffc4da;'><b><?= number_format($TotLiner2, 2);?></b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr style='background-color: #4edcc1; font-size: 14px; color:black;'>
						<td class="text-left" width='15%'><b>TOTAL ALL</b></td>
						<td class="text-left"></td>
						<td class="text-right" width='10%' style='background-color: bisque;'><b><?= number_format($TotLinerKg + $TotLinerKg2, 3);?> Kg</b></td>
						<td class="text-right" width='10%' style='background-color: #2bff9d;'><b></b></td>
						<td class="text-right" width='10%' style='background-color: #ffc4da;'><b><?= number_format($TotLiner + $TotLiner2, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	<script>
		$(document).ready(function(){
			swal.close();
		});
	</script> 