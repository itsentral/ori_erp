<?php
	$id_product = $this->uri->segment(3);
	$id_milik 	= $this->uri->segment(4);
	$qty 		= floatval($this->uri->segment(5)); 
	$length 	= $this->uri->segment(6); 
	// echo $id_product;
	$qDetail1		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail2		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail3		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$detailResin1	= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$qDetailPlus1	= "SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus2	= "SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus3	= "SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus4	= "SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd1	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd2	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd3	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd4	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
	// echo $qDetailAdd1;
	//tambahan flange mould /slongsong
	$qDetail2N1		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail2N2		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetailPlus2N1	= "SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus2N2	= "SELECT a.* FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd2N1	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd2N2	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000'";
	$detailResin2N1	= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2N2	= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	
	
	// echo $qHeader;
	$restHeader		= $this->db->get_where('bq_component_header',array('id_product'=>$id_product,'id_milik'=>$id_milik))->result_array(); 
	
	if ($restHeader[0]['parent_product']=='branch joint' || $restHeader[0]['parent_product']=='field joint' || $restHeader[0]['parent_product']=='shop joint')
	{
		$qDetail1		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='GLASS'";
		$qDetail2		= "SELECT a.* FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000' ";
		$qDetailAdd2	= "SELECT a.* FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000' ";
	
		$restDetail1		= $this->db->query($qDetail1)->result_array();
		$restDetail2		= $this->db->query($qDetail2)->result_array();
		$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
	}
	else{
		$restDetail1		= $this->db->query($qDetail1)->result_array();
		$restDetail2		= $this->db->query($qDetail2)->result_array();
		$restDetail3		= $this->db->query($qDetail3)->result_array();
		$restResin1			= $this->db->query($detailResin1)->result_array();
		$restResin2			= $this->db->query($detailResin2)->result_array();
		$restResin3			= $this->db->query($detailResin3)->result_array();
		$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
		$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
		$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
		$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
		$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
		$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
		$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
		$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
		
		//tambahan flange mould /slongsong
		$restDetail2N1		= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2		= $this->db->query($qDetail2N2)->result_array();
		$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
		$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
		$restDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->result_array();
		$restDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->result_array();
		$restResin2N1		= $this->db->query($detailResin2N1)->result_array();
		$restResin2N2		= $this->db->query($detailResin2N2)->result_array();
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
							<td class="text-left"><u>Length 1 | Length 2</u></td>
							<td class="text-left" colspan='5'><?=floatval($restHeader[0]['panjang_neck_1']); ?> mm | <?=floatval($restHeader[0]['panjang_neck_2']); ?> mm</td>
						</tr>
						<tr>
							<td class="text-left"><u>Thickness N1 | N2</u></td>
							<td class="text-left" colspan='5'><?=floatval($restHeader[0]['design_neck_1']); ?> mm | <?=floatval($restHeader[0]['design_neck_2']); ?> mm</td>
						</tr>
						<tr>
							<td class="text-left"><u>Estimasi N1 | N2</u></td>
							<td class="text-left" colspan='5'><?=floatval($restHeader[0]['est_neck_1']); ?> mm | <?=floatval($restHeader[0]['est_neck_2']); ?> mm</td>
						</tr>
						<tr>
							<td class="text-left"><u>OD | BCD | N | ØH</u></td>
							<td class="text-left" colspan='5'><?=floatval($restHeader[0]['flange_od']); ?> | <?=floatval($restHeader[0]['flange_bcd']); ?> | <?=floatval($restHeader[0]['flange_n']); ?> | <?=floatval($restHeader[0]['flange_oh']); ?></td>
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
					<?php }
					else{
						?>
						<tr>
							<td class="text-left"><u>Overlap</u></td>
							<td class="text-left"><?=floatval($restHeader[0]['panjang']); ?></td>
							<td class="text-left" width='15%'></td>
							<td class="text-left" width='15%'></td>
							<td class="text-left"><u></u></td>
							<td class="text-left"></td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php 
	$TotLinerKg = 0;
	if(!empty($restDetail1)){ ?>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Namee</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Total/Kg</td>
					</tr>
					<?php
					$sumTotDet1Kg	= 0;
					foreach($restDetail1 AS $val => $valx){
						$sumTotDet1Kg += $valx['last_cost'] * $qty;
						?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
						<?php
					}
					if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint')
					{
						$sumTotRes1Kg	= 0;
						foreach($restResin1 AS $val => $valx){
							$sumTotRes1Kg += $valx['last_cost'] * $qty;
						?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
						<?php
						}
						$sumTotPlus1Kg = 0;
						foreach($restDetailPlus1 AS $val => $valx){
							$sumTotPlus1Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
							<?php
						}
						$sumTotAdd1Kg = 0;
						if(!empty($restDetailAdd1)){
							$sumTotAdd1Kg = 0;
							foreach($restDetailAdd1 AS $val => $valx){
								$sumTotAdd1Kg += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
						}
						$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg + $sumTotAdd1Kg + $sumTotPlus1Kg;
						$Lbl = "LINER";
					}
					else{
						$TotLinerKg	= $sumTotDet1Kg;
						$Lbl = "GLASS";
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL <?=$Lbl;?> </b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	<!-- FLANGE -->
	<?php
	$TotStructureKgN1	= 0;
	$TotStructureKgN2	= 0;
	if($restHeader[0]['parent_product'] == 'flange mould' OR $restHeader[0]['parent_product'] == 'flange slongsong' OR $restHeader[0]['parent_product'] == 'colar' OR $restHeader[0]['parent_product'] == 'colar slongsong'){
		?>
			<div class="box box-success">
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody>
							<tr>
								<td class="text-left" colspan='12'><b><?= $restDetail2N1[0]['detail_name']; ?></b></td>
							</tr>
							<tr class='bg-blue'>
								<td class="text-left" width='15%'>Category Name</td>
								<td class="text-left">Material Name</td>
								<td class="text-right" width='10%'>Total/Kg</td>
							</tr>
							<?php
							$sumTotDet2KgN1 = 0;
							foreach($restDetail2N1 AS $val => $valx){
								$sumTotDet2KgN1 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
							$sumTotRes2KgN1 = 0;
							foreach($restResin2N1 AS $val => $valx){
								$sumTotRes2KgN1 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
							</tr>
							<?php
							}
							$sumTotPlus2KgN1 = 0;
							foreach($restDetailPlus2N1 AS $val => $valx){
								$sumTotPlus2KgN1 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
							?>
						</tbody>
						<tbody>
							<?php
							$sumTotAdd2KgN1 = 0;
							if(!empty($restDetailAdd2N1)){
								foreach($restDetailAdd2N1 AS $val => $valx){
									$sumTotAdd2KgN1 += $valx['last_cost'] * $qty;
									?>
								<tr>
									<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
									<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
									<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								</tr>
									<?php
								}
							}
							$TotStructureKgN1	+= $sumTotDet2KgN1 + $sumTotRes2KgN1 + $sumTotAdd2KgN1 + $sumTotPlus2KgN1;
							?> 
							
							<tr style='background-color: #4edcc1;'>
								<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 1</b></td>
								<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN1, 3);?> Kg</b></td>
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
								<td class="text-left" colspan='12'><b><?= $restDetail2N2[0]['detail_name']; ?></b></td>
							</tr>
							<tr class='bg-blue'>
								<td class="text-left" width='15%'>Category Name</td>
								<td class="text-left">Material Name</td>
								<td class="text-right" width='10%'>Total/Kg</td>
							</tr>
							<?php
							$sumTotDet2KgN2 = 0;
							foreach($restDetail2N2 AS $val => $valx){
								$sumTotDet2KgN2 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
							$sumTotRes2KgN2 = 0;
							foreach($restResin2N2 AS $val => $valx){
								$sumTotRes2KgN2 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
							</tr>
							<?php
							}
							$sumTotPlus2KgN2 = 0;
							foreach($restDetailPlus2N2 AS $val => $valx){
								$sumTotPlus2KgN2 += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
							?>
						</tbody>
						<tbody>
							<?php
							$sumTotAdd2KgN2 = 0;
							if(!empty($restDetailAdd2N2)){
								foreach($restDetailAdd2N2 AS $val => $valx){
									$sumTotAdd2KgN2 += $valx['last_cost'] * $qty;
									?>
								<tr>
									<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
									<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
									<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								</tr>
									<?php
								}
							}
							$TotStructureKgN2	+= $sumTotDet2KgN2 + $sumTotRes2KgN2 + $sumTotAdd2KgN2 + $sumTotPlus2KgN2;
							?> 
							
							<tr style='background-color: #4edcc1;'>
								<td class="text-left" colspan='2'><b>TOTAL STRUCTURE</b></td>
								<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN2, 3);?> Kg</b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php
	}
	
	$judul2 = "";
	if(!empty($restDetail2)){
		$judul2 = $restDetail2[0]['detail_name'];
	}
	if(!empty($restResin2)){
		$judul2 = $restResin2[0]['detail_name'];
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
					</tr>
					<?php
					$sumTotDet2Kg = 0;
					foreach($restDetail2 AS $val => $valx){
						$sumTotDet2Kg += $valx['last_cost'] * $qty;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					</tr>
						<?php
					}
					if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint')
					{
						$sumTotRes2Kg = 0;
						foreach($restResin2 AS $val => $valx){
							$sumTotRes2Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
						</tr>
						<?php
						}
						$sumTotPlus2Kg = 0;
						foreach($restDetailPlus2 AS $val => $valx){
							$sumTotPlus2Kg += $valx['last_cost'] * $qty;
							?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
						}
						?>
						</tbody>
						<tbody>
						<?php
						$sumTotAdd2Kg = 0;
						if(!empty($restDetailAdd2)){
							foreach($restDetailAdd2 AS $val => $valx){
								$sumTotAdd2Kg += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
						}
					
						$TotStructureKg	= $sumTotDet2Kg + $sumTotRes2Kg + $sumTotAdd2Kg + $sumTotPlus2Kg;
						
						$Lbl2 = "STRUCTURE"; 
					}
					else{
						$sumTotAdd2Kg = 0;
						if(!empty($restDetailAdd2)){
							foreach($restDetailAdd2 AS $val => $valx){
								$sumTotAdd2Kg += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
						}
						$TotStructureKg	= $sumTotDet2Kg + $sumTotAdd2Kg;
						$Lbl2 = "RESON AND ADD";
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL <?=$Lbl2;?></b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKg, 3);?> Kg</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint')
	{
		$TotExternalKg = 0;
		if(!empty($restDetail3)){
		?>
		<div class="box box-warning">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='12'><b><?= $restDetail3[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Total/Kg</td>
						</tr>
						<?php
						$sumTotDet3Kg =0;
						foreach($restDetail3 AS $val => $valx){
							$sumTotDet3Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
							<?php
						}
						$sumTotRes3Kg =0;
						foreach($restResin3 AS $val => $valx){
							$sumTotRes3Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
							<?php
						}
						$sumTotPlus3Kg =0;
						foreach($restDetailPlus3 AS $val => $valx){
							$sumTotPlus3Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
							<?php
						}
						
						$sumTotAdd3Kg =0;
						if(!empty($restDetailAdd3)){
							$sumTotAdd3Kg =0;
							foreach($restDetailAdd3 AS $val => $valx){
								$sumTotAdd3Kg += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
						}
						$TotExternalKg	= $sumTotDet3Kg + $sumTotRes3Kg + $sumTotAdd3Kg + $sumTotPlus3Kg;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL EXTERNAL</b></td>
							<td class="text-right"><b><?= number_format($TotExternalKg, 3);?> Kg</b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		}
		$TotCoatKg = 0;
		if(!empty($restDetailPlus4)){
		?>
		<div class="box box-danger">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='12'><b><?= $restDetailPlus4[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Total/Kg</td>
						</tr>
						<?php
						$sumTotPlus4Kg = 0;
						foreach($restDetailPlus4 AS $val => $valx){
							$sumTotPlus4Kg += $valx['last_cost'] * $qty;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						</tr>
							<?php
						}
						$sumTotAdd4Kg = 0;
						if(!empty($restDetailAdd4)){
							$sumTotAdd4Kg = 0;
							foreach($restDetailAdd4 AS $val => $valx){
								$sumTotAdd4Kg += $valx['last_cost'] * $qty;
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							</tr>
								<?php
							}
						}
						$TotCoatKg	= $sumTotAdd4Kg + $sumTotPlus4Kg;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL TOPCOAT</b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotCoatKg, 3);?> Kg</b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		}
	}
	?>
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr style='background-color: #4edcc1; font-size: 14px; color:black;'>
						<td class="text-left" width='15%'><b>TOTAL ALL</b></td>
						<td class="text-left"></td>
						<?php
						if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint')
						{
							?>
						<td class="text-right" width='10%' style='background-color: bisque;'><b><?= number_format($TotLinerKg + $TotStructureKg + $TotStructureKgN1 + $TotStructureKgN2 + $TotExternalKg + $TotCoatKg, 3);?> Kg</b></td>
						<?php
						}
						else{
						?>
						<td class="text-right" width='10%' style='background-color: bisque;'><b><?= number_format($TotLinerKg + $TotStructureKg, 3);?> Kg</b></td>
						<?php } ?>
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