<?php
	$id_product = $this->uri->segment(3);
	$id_milik 	= $this->uri->segment(4);
	$qty 		= floatval($this->uri->segment(5));
	$id_bq 		= $this->uri->segment(6); 	
	// echo $id_bq;
	$qHeader		= "SELECT * FROM bq_component_header WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND id_bq='".$id_bq."'";
	$restHeader		= $this->db->query($qHeader)->result_array();
	
	if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint'){
		$qDetail1		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$qDetail2		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$qDetail3		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
		$detailResin1	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin3	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$qDetailPlus1	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus2	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus3	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailPlus4	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd1	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd2	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd3	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
		$qDetailAdd4	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
		
		// echo $detailResin2;
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
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
		if($restHeader[0]['parent_product'] == 'flange mould' OR $restHeader[0]['parent_product'] == 'flange slongsong' OR $restHeader[0]['parent_product'] == 'colar' OR $restHeader[0]['parent_product'] == 'colar slongsong'){
			$qDetail2N1		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
			$qDetail2N2		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
			
			$qDetailPlus2N1	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000'";
			$qDetailPlus2N2	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_plus a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000'";
			
			$qDetailAdd2N1	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 1'";
			$qDetailAdd2N2	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 2'";
			
			$detailResin2N1	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
			$detailResin2N2	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
			
			$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
			$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
			
			$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
			$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
			
			$restDetailAdd2N1		= $this->db->query($qDetailAdd2N1)->result_array();
			$restDetailAdd2N2		= $this->db->query($qDetailAdd2N2)->result_array();
			
			$restResin2N1			= $this->db->query($detailResin2N1)->result_array();
			$restResin2N2			= $this->db->query($detailResin2N2)->result_array();
		}
	}
	
	
	//JOINT
	if($restHeader[0]['parent_product'] == 'shop joint' OR $restHeader[0]['parent_product'] == 'branch joint' OR $restHeader[0]['parent_product'] == 'field joint'){
		$qDetail1Joint		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='GLASS' AND a.id_material <> 'MTL-1903000'";
		$qDetail2Joint		= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000'";
		$qDetail2JointAdd	= "SELECT a.*, a.price_mat AS price_ref_estimation FROM bq_component_detail_add a WHERE a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.id_bq='".$id_bq."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000'";
		
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
	<?php
		if($restHeader[0]['parent_product'] != 'shop joint' AND $restHeader[0]['parent_product'] != 'branch joint' AND $restHeader[0]['parent_product'] != 'field joint'){
			$TotLiner	= 0;
			$TotLinerKg	= 0;
			$TotLinerPr	= 0;
			if(!empty($restDetail1)){
	?>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
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
					$sumTotDet1Pr	= 0;
					foreach($restDetail1 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet1 += $TotPrice;
						$sumTotDet1Kg += $valx['last_cost'] * $qty;
						$sumTotDet1Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes1	= 0;
					$sumTotRes1Kg	= 0;
					$sumTotRes1Pr	= 0;
					foreach($restResin1 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes1 += $TotPrice;
						$sumTotRes1Kg += $valx['last_cost'] * $qty;
						$sumTotRes1Pr += $valx['price_ref_estimation'];
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus1 = 0;
					$sumTotPlus1Kg = 0;
					$sumTotPlus1Pr = 0;
					foreach($restDetailPlus1 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus1 += $TotPrice;
						$sumTotPlus1Kg += $valx['last_cost'] * $qty;
						$sumTotPlus1Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd1 = 0;
					$sumTotAdd1Kg = 0;
					$sumTotAdd1Pr = 0;
					if(!empty($restDetailAdd1)){
						$sumTotAdd1 = 0;
						$sumTotAdd1Kg = 0;
						$sumTotAdd1Pr = 0;
						foreach($restDetailAdd1 AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd1 += $TotPrice;
							$sumTotAdd1Kg += $valx['last_cost'] * $qty;
							$sumTotAdd1Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotLiner	= $sumTotDet1 + $sumTotRes1 + $sumTotAdd1 + $sumTotPlus1;
					$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg + $sumTotAdd1Kg + $sumTotPlus1Kg;
					$TotLinerPr	= $sumTotDet1Pr + $sumTotRes1Pr + $sumTotAdd1Pr + $sumTotPlus1Pr;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL LINER PRICE</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr, 2);?></b></td>
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
	$TotStructurePrN1	= 0;
	$TotStructureN2		= 0;
	$TotStructureKgN2	= 0;
	$TotStructurePrN2	= 0;
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
								<td class="text-right" width='10%'>Price (USD)</td>
								<td class="text-right" width='10%'>Sub Price (USD)</td>
							</tr>
							<?php
							$sumTotDet2N1 = 0;
							$sumTotDet2KgN1 = 0;
							$sumTotDet2PrN1 = 0;
							foreach($restDetail2N1 AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotDet2N1 += $TotPrice;
								$sumTotDet2KgN1 += $valx['last_cost'] * $qty;
								$sumTotDet2PrN1 += $valx['price_ref_estimation'];
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
							$sumTotRes2N1 = 0;
							$sumTotRes2KgN1 = 0;
							$sumTotRes2PrN1 = 0;
							foreach($restResin2N1 AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotRes2N1 += $TotPrice;
								$sumTotRes2KgN1 += $valx['last_cost'] * $qty;
								$sumTotRes2PrN1 += $valx['price_ref_estimation'];
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
							<?php
							}
							$sumTotPlus2N1 = 0;
							$sumTotPlus2KgN1 = 0;
							$sumTotPlus2PrN1 = 0;
							foreach($restDetailPlus2N1 AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotPlus2N1 += $TotPrice;
								$sumTotPlus2KgN1 += $valx['last_cost'] * $qty;
								$sumTotPlus2PrN1 += $valx['price_ref_estimation'];
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
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
							$sumTotAdd2PrN1 = 0;
							if(!empty($restDetailAdd2N1)){
								$sumTotAdd2N1 = 0;
								foreach($restDetailAdd2N1 AS $val => $valx){
									$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
									$warna 	= "";
									$backg	= "#2bff9d";
									if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
										$warna 	= "white";
										$backg	= "black";
									}
									$sumTotAdd2N1 += $TotPrice;
									$sumTotAdd2KgN1 += $valx['last_cost'] * $qty;
									$sumTotAdd2PrN1 += $valx['price_ref_estimation'];
									?>
								<tr>
									<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
									<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
									<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
									<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
									<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
								</tr>
									<?php
								}
							}
							$TotStructureN1	+= $sumTotDet2N1 + $sumTotRes2N1 + $sumTotAdd2N1 + $sumTotPlus2N1;
							$TotStructureKgN1	+= $sumTotDet2KgN1 + $sumTotRes2KgN1 + $sumTotAdd2KgN1 + $sumTotPlus2KgN1;
							$TotStructurePrN1	+= $sumTotDet2PrN1 + $sumTotRes2PrN1 + $sumTotAdd2PrN1 + $sumTotPlus2PrN1;
							?> 
							
							<tr style='background-color: #4edcc1;'>
								<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 1 PRICE</b></td>
								<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN1, 3);?> Kg</b></td>
								<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotStructurePrN1, 2);?></b></td>
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
								<td class="text-left" colspan='12'><b><?= $restDetail2N2[0]['detail_name']; ?></b></td>
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
							$sumTotDet2PrN2 = 0;
							foreach($restDetail2N2 AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotDet2N2 += $TotPrice;
								$sumTotDet2KgN2 += $valx['last_cost'] * $qty;
								$sumTotDet2PrN2 += $valx['price_ref_estimation'];
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
							$sumTotRes2N2 = 0;
							$sumTotRes2KgN2 = 0;
							$sumTotRes2PrN2 = 0;
							foreach($restResin2N2 AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotRes2N2 += $TotPrice;
								$sumTotRes2KgN2 += $valx['last_cost'] * $qty;
								$sumTotRes2PrN2 += $valx['price_ref_estimation'];
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
							<?php
							}
							$sumTotPlus2N2 = 0;
							$sumTotPlus2KgN2 = 0;
							$sumTotPlus2PrN2 = 0;
							foreach($restDetailPlus2N2 AS $val => $valx){
								$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
								$warna 	= "";
								$backg	= "#2bff9d";
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotPlus2N2 += $TotPrice;
								$sumTotPlus2KgN2 += $valx['last_cost'] * $qty;
								$sumTotPlus2PrN2 += $valx['price_ref_estimation'];
								?>
							<tr>
								<td class="text-left"><?= $valx['nm_category'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
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
							$sumTotAdd2PrN2 = 0;
							if(!empty($restDetailAdd2N2)){
								$sumTotAdd2 = 0;
								foreach($restDetailAdd2N2 AS $val => $valx){
									$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
									$warna 	= "";
									$backg	= "#2bff9d";
									if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
										$warna 	= "white";
										$backg	= "black";
									}
									$sumTotAdd2N2 += $TotPrice;
									$sumTotAdd2KgN2 += $valx['last_cost'] * $qty;
									$sumTotAdd2PrN2 += $valx['price_ref_estimation'];
									?>
								<tr>
									<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
									<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
									<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
									<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
									<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
								</tr>
									<?php
								}
							}
							$TotStructureN2	+= $sumTotDet2N2 + $sumTotRes2N2 + $sumTotAdd2N2 + $sumTotPlus2N2;
							$TotStructureKgN2	+= $sumTotDet2KgN2 + $sumTotRes2KgN2 + $sumTotAdd2KgN2 + $sumTotPlus2KgN2;
							$TotStructurePrN2	+= $sumTotDet2PrN2 + $sumTotRes2PrN2 + $sumTotAdd2PrN2 + $sumTotPlus2PrN2;
							?> 
							
							<tr style='background-color: #4edcc1;'>
								<td class="text-left" colspan='2'><b>TOTAL STRUCTURE PRICE</b></td>
								<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN2, 3);?> Kg</b></td>
								<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotStructurePrN2, 2);?></b></td>
								<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructureN2, 2);?></b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php
	}
	?>
	
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
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
					$sumTotDet2Pr = 0;
					foreach($restDetail2 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet2 += $TotPrice;
						$sumTotDet2Kg += $valx['last_cost'] * $qty;
						$sumTotDet2Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes2 = 0;
					$sumTotRes2Kg = 0;
					$sumTotRes2Pr = 0;
					foreach($restResin2 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes2 += $TotPrice;
						$sumTotRes2Kg += $valx['last_cost'] * $qty;
						$sumTotRes2Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?>Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus2 = 0;
					$sumTotPlus2Kg = 0;
					$sumTotPlus2Pr = 0;
					foreach($restDetailPlus2 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus2 += $TotPrice;
						$sumTotPlus2Kg += $valx['last_cost'] * $qty;
						$sumTotPlus2Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					$sumTotAdd2 = 0;
					$sumTotAdd2Kg = 0;
					$sumTotAdd2Pr = 0;
					if(!empty($restDetailAdd2)){
						$sumTotAdd2 = 0;
						foreach($restDetailAdd2 AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd2 += $TotPrice;
							$sumTotAdd2Kg += $valx['last_cost'] * $qty;
							$sumTotAdd2Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotStructure	= $sumTotDet2 + $sumTotRes2 + $sumTotAdd2 + $sumTotPlus2;
					$TotStructureKg	= $sumTotDet2Kg + $sumTotRes2Kg + $sumTotAdd2Kg + $sumTotPlus2Kg;
					$TotStructurePr	= $sumTotDet2Pr + $sumTotRes2Pr + $sumTotAdd2Pr + $sumTotPlus2Pr;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL STRUCTURE PRICE</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotStructurePr, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructure, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$TotExternal = 0;
	$TotExternalKg = 0;
	$TotExternalPr = 0;
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
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
					</tr>
					<?php
					$sumTotDet3 =0;
					$sumTotDet3Kg =0;
					$sumTotDet3Pr =0;
					foreach($restDetail3 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet3 += $TotPrice;
						$sumTotDet3Kg += $valx['last_cost'] * $qty;
						$sumTotDet3Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes3 =0;
					$sumTotRes3Kg =0;
					$sumTotRes3Pr =0;
					foreach($restResin3 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes3 += $TotPrice;
						$sumTotRes3Kg += $valx['last_cost'] * $qty;
						$sumTotRes3Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotPlus3 =0;
					$sumTotPlus3Kg =0;
					$sumTotPlus3Pr =0;
					foreach($restDetailPlus3 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus3 += $TotPrice;
						$sumTotPlus3Kg += $valx['last_cost'] * $qty;
						$sumTotPlus3Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					
					$sumTotAdd3 =0;
					$sumTotAdd3Kg =0;
					$sumTotAdd3Pr =0;
					if(!empty($restDetailAdd3)){
						$sumTotAdd3 =0;
						$sumTotAdd3Kg =0;
						$sumTotAdd3Pr =0;
						foreach($restDetailAdd3 AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd3 += $TotPrice;
							$sumTotAdd3Kg += $valx['last_cost'] * $qty;
							$sumTotAdd3Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotExternal	= $sumTotDet3 + $sumTotRes3 + $sumTotAdd3 + $sumTotPlus3;
					$TotExternalKg	= $sumTotDet3Kg + $sumTotRes3Kg + $sumTotAdd3Kg + $sumTotPlus3Kg;
					$TotExternalPr	= $sumTotDet3Pr + $sumTotRes3Pr + $sumTotAdd3Pr + $sumTotPlus3Pr;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL EXTERNAL PRICE</b></td>
						<td class="text-right"><b><?= number_format($TotExternalKg, 3);?> Kg</b></td>
						<td class="text-right"><b><?= number_format($TotExternalPr, 2);?></b></td>
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
	$TotCoatPr = 0;
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
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
					</tr>
					<?php
					$sumTotPlus4 = 0;
					$sumTotPlus4Kg = 0;
					$sumTotPlus4Pr = 0;
					foreach($restDetailPlus4 AS $val => $valx){
						$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus4 += $TotPrice;
						$sumTotPlus4Kg += $valx['last_cost'] * $qty;
						$sumTotPlus4Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd4 = 0;
					$sumTotAdd4Kg = 0;
					$sumTotAdd4Pr = 0;
					if(!empty($restDetailAdd4)){
						$sumTotAdd4 = 0;
						$sumTotAdd4Kg = 0;
						$sumTotAdd4Pr = 0;
						foreach($restDetailAdd4 AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd4 += $TotPrice;
							$sumTotAdd4Kg += $valx['last_cost'] * $qty;
							$sumTotAdd4Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotCoat	= $sumTotAdd4 + $sumTotPlus4;
					$TotCoatKg	= $sumTotAdd4Kg + $sumTotPlus4Kg;
					$TotCoatPr	= $sumTotAdd4Pr + $sumTotPlus4Pr;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL TOPCOAT PRICE</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotCoatKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotCoatPr, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotCoat, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
		}
	?>
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr style='background-color: #4edcc1; font-size: 14px; color:black;'>
						<td class="text-left" width='15%'><b>TOTAL ALL</b></td>
						<td class="text-left"></td>
						<td class="text-right" width='10%' style='background-color: bisque;'><b><?= number_format($TotLinerKg + $TotStructureKg + $TotStructureKgN1 + $TotStructureKgN2 + $TotExternalKg + $TotCoatKg, 3);?> Kg</b></td>
						<td class="text-right" width='10%' style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr + $TotStructurePr + $TotStructurePrN1 + $TotStructurePrN2 + $TotExternalPr + $TotCoatPr, 2);?></b></td>
						<td class="text-right" width='10%' style='background-color: #ffc4da;'><b><?= number_format($TotLiner + $TotStructure + $TotStructureN1 + $TotStructureN2 + $TotExternal + $TotCoat, 2);?></b></td>
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
						$sumTotDet1Pr	= 0;
						foreach($restDetail1Joint AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet1 += $TotPrice;
							$sumTotDet1Kg += $valx['last_cost'] * $qty;
							$sumTotDet1Pr += $valx['price_ref_estimation'];
							
							$qName			= "SELECT category FROM raw_categories WHERE id_category='".$valx['id_category']."' ";
							$restName		= $this->db->query($qName)->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($restName[0]['category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
						
						$TotLiner	= $sumTotDet1;
						$TotLinerKg	= $sumTotDet1Kg;
						$TotLinerPr	= $sumTotDet1Pr;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL GLASS</b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr, 2);?></b></td>
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
						$sumTotDet1Pr2	= 0;
						foreach($restDetail2Joint AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet12 += $TotPrice;
							$sumTotDet1Kg2 += $valx['last_cost'] * $qty;
							$sumTotDet1Pr2 += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
						//Add
						$sumTotDet12Add	= 0;
						$sumTotDet1Kg2Add	= 0;
						$sumTotDet1Pr2Add	= 0;
						foreach($restDetail2JointAdd AS $val => $valx){
							$TotPrice	= ($valx['last_cost'] * $qty) * ($valx['price_ref_estimation']);
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet12Add += $TotPrice;
							$sumTotDet1Kg2Add += $valx['last_cost'] * $qty;
							$sumTotDet1Pr2Add += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
						
						$TotLiner2	= $sumTotDet12 + $sumTotDet12Add;
						$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotDet1Kg2Add;
						$TotLinerPr2	= $sumTotDet1Pr2 + $sumTotDet1Pr2Add;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL <?= $restDetail2Joint[0]['detail_name']; ?></b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg2, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr2, 2);?></b></td>
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
						<td class="text-right" width='10%' style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr + $TotLinerPr2, 2);?></b></td>
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