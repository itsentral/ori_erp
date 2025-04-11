<?php
	$id_bq				= $this->uri->segment(3);
	$id_milik			= $this->uri->segment(4);
	// echo $id_product;
	$qHeader		= "SELECT * FROM so_component_header WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ";
	// echo $qHeader;
	$qDetail1		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$qDetail2		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$qDetail2N1		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$qDetail2N2		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$qDetail3		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$detailResin1	= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$detailResin2N1	= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$detailResin2N2	= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	
	$detailResin3	= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$qDetailPlus1	= "SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000'";
	$qDetailPlus2	= "SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000'";
	$qDetailPlus2N1	= "SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000'";
	$qDetailPlus2N2	= "SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000'";
	
	$qDetailPlus3	= "SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000'";
	$qDetailPlus4	= "SELECT * FROM so_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000'";
	$qDetailAdd1	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='LINER THIKNESS / CB'";
	$qDetailAdd2	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS'";
	$qDetailAdd2N1	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1'";
	$qDetailAdd2N2	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 2'";
	
	$qDetailAdd3	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS'";
	$qDetailAdd4	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='TOPCOAT'";
	$qFooter1		= "SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='LINER THIKNESS / CB'";
	$qFooter2		= "SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS'";
	$qFooter2N1		= "SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1'";
	$qFooter2N2		= "SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 2'";
	
	$qFooter3		= "SELECT * FROM so_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS'";
	// $qTiming		= "SELECT * FROM so_component_time WHERE id_product='".$id_product."'";
	// echo $qDetail2;
	$restHeader		= $this->db->query($qHeader)->result_array();
	
	if ($restHeader[0]['parent_product']=='branch joint' || $restHeader[0]['parent_product']=='field joint' || $restHeader[0]['parent_product']=='shop joint')
	{
		$qDetail1		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='GLASS' AND id_material <> 'MTL-1903000'";
		$qDetail2		= "SELECT * FROM so_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000'";
		$qDetail2Add	= "SELECT * FROM so_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000'";
		$qDetail3		= "SELECT * FROM so_component_lamination WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='Inside Lamination' ";
		$qDetail4		= "SELECT * FROM so_component_lamination WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='Outside Lamination' AND width > 0";

		// echo $qDetail4;

		$restDetail1		= $this->db->query($qDetail1)->result_array();
		$restDetail2		= $this->db->query($qDetail2)->result_array();
		$restDetail2Add		= $this->db->query($qDetail2Add)->result_array();
		$restDetail3		= $this->db->query($qDetail3)->result_array();
		$restDetail4		= $this->db->query($qDetail4)->result_array();
	}
	else{
		$restDetail1		= $this->db->query($qDetail1)->result_array();
		$restDetail2		= $this->db->query($qDetail2)->result_array();
		$restDetail2N1		= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2		= $this->db->query($qDetail2N2)->result_array();
		$restDetail3		= $this->db->query($qDetail3)->result_array();
		$restResin1			= $this->db->query($detailResin1)->result_array();
		$restResin2			= $this->db->query($detailResin2)->result_array();
		$restResin2N1		= $this->db->query($detailResin2N1)->result_array();
		$restResin2N2		= $this->db->query($detailResin2N2)->result_array();
		$restResin3			= $this->db->query($detailResin3)->result_array();
		$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
		$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
		$restDetailPlus2N1	= $this->db->query($qDetailPlus2N1)->result_array();
		$restDetailPlus2N2	= $this->db->query($qDetailPlus2N2)->result_array();
		$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
		$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
		
		$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
		$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
		$restDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->result_array();
		$restDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->result_array();
		$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
		$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
		
		$restFooter1		= $this->db->query($qFooter1)->result_array();
		$restFooter2		= $this->db->query($qFooter2)->result_array();
		$restFooter2N1		= $this->db->query($qFooter2N1)->result_array();
		$restFooter2N2		= $this->db->query($qFooter2N2)->result_array();
		$restFooter3		= $this->db->query($qFooter3)->result_array();
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
						$T2 = floatval($restHeader[0]['est'])." mm";
						$T3 = " || ".$restHeader[0]['stiffness']." || ".$restHeader[0]['criminal_barier']." || ".$restHeader[0]['vacum_rate']." || ".$restHeader[0]['aplikasi_product'];
					}
					?>
					<tr>
						<td class="text-left"><u>Component Group</u></td>
						<td class="text-left" colspan='5'><?= strtoupper($restHeader[0]['parent_product'].$T3); ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Spesification</u></td>
						<td class="text-left" colspan='5'><?= spec_bq2($id_milik);?></td>
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
				<tbody>
				<?php
					if ($restHeader[0]['parent_product']=='branch joint' || $restHeader[0]['parent_product']=='field joint' || $restHeader[0]['parent_product']=='shop joint')
					{
						?>
						<tr>
							<td class="text-left" colspan='11'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='24%' colspan="2">Category Name</td>
							<td class="text-left" colspan='5'>Material Name</td>
							<td class="text-center" width='6%'>Value</td>
							<td class="text-right" width='6%'>Resin Content</td>
							<td class="text-right" width='6%'>Thickness</td>
							<td class="text-right" width='6%'>Total</td>
						</tr>
						<?php
						foreach($restDetail1 AS $val => $valx){
							$bgC1	= 'transparent';
							$Cl1	= 'black';
							$bgC2	= 'transparent';
							$Cl2	= 'black';
							$bgC3	= 'transparent';
							$Cl3	= 'black';
							$bgC4	= 'transparent';
							$Cl4	= 'black';
							$bgC5	= 'transparent';
							$Cl5	= 'black';
							$bgC6	= 'transparent';
							$Cl6	= 'black';
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['area_weight'] > 0){
								$bgC1	= 'black';
								$Cl1	= 'white';
							}
							if($valx['resin_content'] > 0){
								$bgC2	= 'black';
								$Cl2	= 'white';
							}
							if($valx['thickness'] > 0){
								$bgC3	= 'black';
								$Cl3	= 'white';
							}
							if($valx['material_weight'] > 0){
								$bgC4	= 'black';
								$Cl4	= 'white';
							}

							?>
							<tr>
								<td class="text-left" colspan="2"><?= $valx['nm_category'];?></td>
								<td class="text-left" colspan='5'><?= $valx['nm_material'];?></td>
								<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= number_format($valx['area_weight']);?></td>
								<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= number_format($valx['resin_content'],4);?></td>
								<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= number_format($valx['thickness'],4);?></td>
								<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= number_format($valx['material_weight'],3);?></td>
							</tr>
							<?php
						}
							?>
						<tr>
							<td class="text-left" colspan='11'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='24%' colspan="2">Category Name</td>
							<td class="text-left" colspan='7'>Material Name</td>
							<td class="text-center" width='6%'>Percentage</td>
							<td class="text-right" width='6%'>Total</td>
						</tr>
							<?php
						foreach($restDetail2 AS $val => $valx){
							$bgC1	= 'transparent';
							$Cl1	= 'black';
							$bgC2	= 'transparent';
							$Cl2	= 'black';

							if($valx['percentage'] > 0){
								$bgC1	= 'black';
								$Cl1	= 'white';
							}
							if($valx['material_weight'] > 0){
								$bgC2	= 'black';
								$Cl2	= 'white';
							}

							?>
							<tr>
								<td class="text-left" colspan="2"><?= $valx['nm_category'];?></td>
								<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td> 
								<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['percentage']);?></td>
								<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= number_format($valx['material_weight'], 3);?></td>
							</tr>
							<?php
						}
						foreach($restDetail2Add AS $val => $valx){
							$bgC1	= 'transparent';
							$Cl1	= 'black';
							$bgC2	= 'transparent';
							$Cl2	= 'black';

							if($valx['perse'] > 0){
								$bgC1	= 'black';
								$Cl1	= 'white';
							}
							if($valx['last_cost'] > 0){
								$bgC2	= 'black';
								$Cl2	= 'white';
							}

							?>
							<tr>
								<td class="text-left" colspan="2"><?= $valx['nm_category'];?></td>
								<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td> 
								<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['perse']);?></td>
								<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= number_format($valx['last_cost'], 3);?></td>
							</tr>
							<?php
						}
							?>
						<tr class='bg-blue'>
							<td class="text-left" width='5%'>Lapisan</td>
							<td class="text-left" width='10%'>Std Glass</td>
							<td class="text-center" width='10%'>Width</td>
							<td class="text-right" width='5%'>Stage</td>
		
							<td class="text-center" width='10%'>Glass Conf.</td>
							<td class="text-center" width='20%' colspan="2">Thickness</td>
							
							<td class="text-right" width='10%'>Glass Length</td>
							<td class="text-right" width='10%'>Weight Veil</td>
							<td class="text-right" width='10%'>Weight Csm</td>
							<td class="text-right" width='10%'>Weight WR</td>
						</tr>
						<tr>
							<td class="text-left" colspan='11'><b><?= strtoupper($restDetail3[0]['detail_name']); ?></b></td>
						</tr>
							<?php
						foreach($restDetail3 AS $val => $valx){
							$bgC1	= 'transparent';
							$Cl1	= 'black';
							$bgC2	= 'transparent';
							$Cl2	= 'black';
							$bgC3	= 'transparent';
							$Cl3	= 'black';
							$bgC4	= 'transparent';
							$Cl4	= 'black';
							$bgC5	= 'transparent';
							$Cl5	= 'black';
							$bgC6	= 'transparent';
							$Cl6	= 'black';
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							if($valx['width'] > 0){
								$bgC1	= 'black';
								$Cl1	= 'white';
							}
							if($valx['thickness_1'] > 0){
								$bgC2	= 'black';
								$Cl2	= 'white';
							}
							if($valx['thickness_2'] > 0){
								$bgC3	= 'black';
								$Cl3	= 'white';
							}
							if($valx['glass_length'] > 0){
								$bgC4	= 'black';
								$Cl4	= 'white';
							}
							if($valx['weight_veil'] > 0){
								$bgC5	= 'black';
								$Cl5	= 'white';
							}
							if($valx['weight_csm'] > 0){
								$bgC6	= 'black';
								$Cl6	= 'white';
							}
							if($valx['weight_wr'] > 0){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}

							?>
							<tr>
								<td class="text-left"><?= $valx['lapisan'];?></td>
								<td class="text-left"><?= $valx['std_glass'];?></td>
								<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['width']);?></td>
								<td class="text-right"></td>
								<td class="text-left"><?= $valx['glass'];?></td>
								<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= number_format($valx['thickness_1'],4);?></td>
								<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= number_format($valx['thickness_2'],4);?></td>
								<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= number_format($valx['glass_length'],3);?></td>
								<td class="text-right" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= number_format($valx['weight_veil'],3);?></td>
								<td class="text-right" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= number_format($valx['weight_csm'],3);?></td>
								<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= number_format($valx['weight_wr'],3);?></td>
							</tr>
							<?php
						}
							?>
						<tr>
							<td class="text-left" colspan='11'><b><?= $restDetail4[0]['detail_name']; ?></b></td>
						</tr>
							<?php
						foreach($restDetail4 AS $val => $valx){
							$bgC1	= 'transparent';
							$Cl1	= 'black';
							$bgC2	= 'transparent';
							$Cl2	= 'black';
							$bgC3	= 'transparent';
							$Cl3	= 'black';
							$bgC4	= 'transparent';
							$Cl4	= 'black';
							$bgC5	= 'transparent';
							$Cl5	= 'black';
							$bgC6	= 'transparent';
							$Cl6	= 'black';
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							if($valx['width'] > 0){
								$bgC1	= 'black';
								$Cl1	= 'white';
							}
							if($valx['thickness_1'] > 0){
								$bgC2	= 'black';
								$Cl2	= 'white';
							}
							if($valx['thickness_2'] > 0){
								$bgC3	= 'black';
								$Cl3	= 'white';
							}
							if($valx['glass_length'] > 0){
								$bgC4	= 'black';
								$Cl4	= 'white';
							}
							if($valx['weight_veil'] > 0){
								$bgC5	= 'black';
								$Cl5	= 'white';
							}
							if($valx['weight_csm'] > 0){
								$bgC6	= 'black';
								$Cl6	= 'white';
							}
							if($valx['weight_wr'] > 0){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}

							?>
							<tr>
								<td class="text-left"><?= $valx['lapisan'];?></td>
								<td class="text-left"><?= $valx['std_glass'];?></td>
								<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['width']);?></td>
								<td class="text-left"></td>
								<td class="text-center"><?= $valx['glass'];?></td>
								<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= number_format($valx['thickness_1'],4);?></td>
								<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= number_format($valx['thickness_2'],4);?></td>
								<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= number_format($valx['glass_length'],3);?></td>
								<td class="text-right" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= number_format($valx['weight_veil'],3);?></td>
								<td class="text-right" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= number_format($valx['weight_csm'],3);?></td>
								<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= number_format($valx['weight_wr'],3);?></td>
							</tr>
							<?php
						}

					}
					else
					{
							?>
						<tr>
							<td class="text-left" colspan='12'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='12%'>Category Name</td>
							<td class="text-left" colspan='4'>Material Name</td>
							<td class="text-right" width='6%'>Value</td>
							<td class="text-right" width='6%'>Thickness</td>
							<td class="text-right" width='6%'>Multiplier</td>
							<!--
							<td class="text-right" width='6%'>BW</td>
							<td class="text-right" width='6%'>Sum</td>
							<td class="text-right" width='6%'>Layer</td>
							-->
							<td class="text-right" width='6%'>Containing</td>
							<td class="text-right" width='6%'>Thickness</td>
							<td class="text-right" width='6%'>Total</td>
						</tr>
						<?php
						foreach($restDetail1 AS $val => $valx){
							$bgC1	= 'transparent';
							$Cl1	= 'black';
							$bgC2	= 'transparent';
							$Cl2	= 'black';
							$bgC3	= 'transparent';
							$Cl3	= 'black';
							$bgC4	= 'transparent';
							$Cl4	= 'black';
							$bgC5	= 'transparent';
							$Cl5	= 'black';
							$bgC6	= 'transparent';
							$Cl6	= 'black';
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['value'] != '0.00'){
								$bgC1	= 'black';
								$Cl1	= 'white';
							}
							if($valx['thickness'] != '0.0000'){
								$bgC2	= 'black';
								$Cl2	= 'white';
							}
							if($valx['fak_pengali'] != '0.00'){
								$bgC3	= 'black';
								$Cl3	= 'white';
							}
							if($valx['bw'] != '0.00'){
								$bgC4	= 'black';
								$Cl4	= 'white';
							}
							if($valx['jumlah'] != '0.00'){
								$bgC5	= 'black';
								$Cl5	= 'white';
							}
							if($valx['layer'] != '0.00'){
								$bgC6	= 'black';
								$Cl6	= 'white';
							}
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['total_thickness'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left" colspan='4'><?= $valx['nm_material'];?></td>
							<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['value']);?></td>
							<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>

							<td class="text-center" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= floatval($valx['layer']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['total_thickness'];?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
						?>
					</tbody>
					<tbody>
						<tr class='bg-blue'>
							<td class="text-left">Category Name</td>
							<td class="text-left" colspan='7'>Material Name</td>
							<td class="text-right">Containing</td>
							<td class="text-right">Perse</td>
							<td class="text-right">Total</td>
						</tr>
						<?php
						foreach($restResin1 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
						?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
							<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-right"></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
						<?php
						}
						
						foreach($restDetailPlus1 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
				</tbody>
				<?php
				if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint')
				{
					?>
					<tbody>
					<?php
					if(!empty($restDetailAdd1)){
						?>
						<tr>
							<td style='background-color: burlywood;' class="text-left" colspan='12'><b>Additional Material</b></td>
						</tr>
						<?php
						foreach($restDetailAdd1 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left" colspan='7'><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>TOTAL LINER THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter1[0]['total'];?></td>
						<td class="text-left" colspan='1'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MIN LINER THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter1[0]['min'];?></td>
						<td class="text-left" colspan='1'></td>
					</tr>
					<tr>
						<td class="text-left"  colspan='6'></td>
						<td class="text-left" colspan='3'><b>MAX LINER THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter1[0]['max'];?></td>
						<td class="text-left" colspan='1'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='9'></td>
						<?php
							if($restFooter1[0]['hasil'] == 'OK'){
								$color= '#3aa717';
							}
							else{
								$color= '#a72417';
							}
						?>
						<td class="text-center" colspan='2' style='background-color: <?= $color;?>; color:white; font-weight: bold;'><?= $restFooter1[0]['hasil'];?></td>
					</tr>
				</tbody>
				<?php
				}
				?>
			</table>
		</div>
	</div>
	<?php
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
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='6%'>Value</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Multiplier</td>
						<td class="text-right" width='6%'>BW</td>
						<td class="text-right" width='6%'>Sum</td>
						<td class="text-right" width='6%'>Layer</td>
						<td class="text-right" width='6%'>Containing</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Total</td>
					</tr>
					<?php
					foreach($restDetail2N1 AS $val => $valx){
						$bgC1	= 'transparent';
						$Cl1	= 'black';
						$bgC2	= 'transparent';
						$Cl2	= 'black';
						$bgC3	= 'transparent';
						$Cl3	= 'black';
						$bgC4	= 'transparent';
						$Cl4	= 'black';
						$bgC5	= 'transparent';
						$Cl5	= 'black';
						$bgC6	= 'transparent';
						$Cl6	= 'black';
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['value'] != '0.00'){
							$bgC1	= 'black';
							$Cl1	= 'white';
						}
						if($valx['thickness'] != '0.0000'){
							$bgC2	= 'black';
							$Cl2	= 'white';
						}
						if($valx['fak_pengali'] != '0.00'){
							$bgC3	= 'black';
							$Cl3	= 'white';
						}
						if($valx['bw'] != '0.00'){
							$bgC4	= 'black';
							$Cl4	= 'white';
						}
						if($valx['jumlah'] != '0.00'){
							$bgC5	= 'black';
							$Cl5	= 'white';
						}
						if($valx['layer'] != '0.00'){
							$bgC6	= 'black';
							$Cl6	= 'white';
						}
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['total_thickness'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['value']);?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<td class="text-center" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= floatval($valx['fak_pengali']);?></td>
						<td class="text-center" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= floatval($valx['bw']);?></td>
						<td class="text-center" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= floatval($valx['jumlah']);?></td>
						<td class="text-center" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= floatval($valx['layer']);?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['total_thickness'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<tr class='bg-blue'>
						<td class="text-left">Category Name</td>
						<td class="text-left" colspan='7'>Material Name</td>
						<td class="text-right">Containing</td>
						<td class="text-right">Perse</td>
						<td class="text-right">Total</td>
					</tr>
					<?php
					foreach($restResin2N1 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center">0</td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
					<?php 
					}
					
					foreach($restDetailPlus2N1 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['perse'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if(!empty($restDetailAdd2N1)){
						?>
						<tr>
							<td style='background-color: burlywood;' class="text-left" colspan='12'><b>Additional Material</b></td>
						</tr>
						<?php
						foreach($restDetailAdd2N1 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left" colspan='7'><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>TOTAL STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2N1[0]['total'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MIN STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2N1[0]['min'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MAX STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2N1[0]['max'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='9'></td>
						<?php
							if($restFooter2[0]['hasil'] == 'OK'){
								$color= '#3aa717';
							}
							else{
								$color= '#a72417';
							}
						?>
						<td class="text-center" colspan='2' style='background-color: <?= $color;?>; color:white; font-weight: bold;'><?= $restFooter2N1[0]['hasil'];?></td>
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
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='6%'>Value</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Multiplier</td>
						<td class="text-right" width='6%'>BW</td>
						<td class="text-right" width='6%'>Sum</td>
						<td class="text-right" width='6%'>Layer</td>
						<td class="text-right" width='6%'>Containing</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Total</td>
					</tr>
					<?php
					foreach($restDetail2N2 AS $val => $valx){
						$bgC1	= 'transparent';
						$Cl1	= 'black';
						$bgC2	= 'transparent';
						$Cl2	= 'black';
						$bgC3	= 'transparent';
						$Cl3	= 'black';
						$bgC4	= 'transparent';
						$Cl4	= 'black';
						$bgC5	= 'transparent';
						$Cl5	= 'black';
						$bgC6	= 'transparent';
						$Cl6	= 'black';
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['value'] != '0.00'){
							$bgC1	= 'black';
							$Cl1	= 'white';
						}
						if($valx['thickness'] != '0.0000'){
							$bgC2	= 'black';
							$Cl2	= 'white';
						}
						if($valx['fak_pengali'] != '0.00'){
							$bgC3	= 'black';
							$Cl3	= 'white';
						}
						if($valx['bw'] != '0.00'){
							$bgC4	= 'black';
							$Cl4	= 'white';
						}
						if($valx['jumlah'] != '0.00'){
							$bgC5	= 'black';
							$Cl5	= 'white';
						}
						if($valx['layer'] != '0.00'){
							$bgC6	= 'black';
							$Cl6	= 'white';
						}
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['total_thickness'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['value']);?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<td class="text-center" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= floatval($valx['fak_pengali']);?></td>
						<td class="text-center" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= floatval($valx['bw']);?></td>
						<td class="text-center" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= floatval($valx['jumlah']);?></td>
						<td class="text-center" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= floatval($valx['layer']);?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['total_thickness'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<tr class='bg-blue'>
						<td class="text-left">Category Name</td>
						<td class="text-left" colspan='7'>Material Name</td>
						<td class="text-right">Containing</td>
						<td class="text-right">Perse</td>
						<td class="text-right">Total</td>
					</tr>
					<?php
					foreach($restResin2N2 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center">0</td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
					<?php 
					}
					
					foreach($restDetailPlus2N2 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['perse'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if(!empty($restDetailAdd2N2)){
						?>
						<tr>
							<td style='background-color: burlywood;' class="text-left" colspan='12'><b>Additional Material</b></td>
						</tr>
						<?php
						foreach($restDetailAdd2N2 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left" colspan='7'><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>TOTAL STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2N2[0]['total'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MIN STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2N2[0]['min'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MAX STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2N2[0]['max'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='9'></td>
						<?php
							if($restFooter2[0]['hasil'] == 'OK'){
								$color= '#3aa717';
							}
							else{
								$color= '#a72417';
							}
						?>
						<td class="text-center" colspan='2' style='background-color: <?= $color;?>; color:white; font-weight: bold;'><?= $restFooter2N2[0]['hasil'];?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php
	}
	if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint'){
		?>
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='6%'>Value</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Multiplier</td>
						<td class="text-right" width='6%'>BW</td>
						<td class="text-right" width='6%'>Sum</td>
						<td class="text-right" width='6%'>Layer</td>
						<td class="text-right" width='6%'>Containing</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Total</td>
					</tr>
					<?php
					foreach($restDetail2 AS $val => $valx){
						$bgC1	= 'transparent';
						$Cl1	= 'black';
						$bgC2	= 'transparent';
						$Cl2	= 'black';
						$bgC3	= 'transparent';
						$Cl3	= 'black';
						$bgC4	= 'transparent';
						$Cl4	= 'black';
						$bgC5	= 'transparent';
						$Cl5	= 'black';
						$bgC6	= 'transparent';
						$Cl6	= 'black';
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['value'] != '0.00'){
							$bgC1	= 'black';
							$Cl1	= 'white';
						}
						if($valx['thickness'] != '0.0000'){
							$bgC2	= 'black';
							$Cl2	= 'white';
						}
						if($valx['fak_pengali'] != '0.00'){
							$bgC3	= 'black';
							$Cl3	= 'white';
						}
						if($valx['bw'] != '0.00'){
							$bgC4	= 'black';
							$Cl4	= 'white';
						}
						if($valx['jumlah'] != '0.00'){
							$bgC5	= 'black';
							$Cl5	= 'white';
						}
						if($valx['layer'] != '0.00'){
							$bgC6	= 'black';
							$Cl6	= 'white';
						}
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['total_thickness'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['value']);?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<td class="text-center" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= floatval($valx['fak_pengali']);?></td>
						<td class="text-center" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= floatval($valx['bw']);?></td>
						<td class="text-center" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= floatval($valx['jumlah']);?></td>
						<td class="text-center" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= floatval($valx['layer']);?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['total_thickness'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<tr class='bg-blue'>
						<td class="text-left">Category Name</td>
						<td class="text-left" colspan='7'>Material Name</td>
						<td class="text-right">Containing</td>
						<td class="text-right">Perse</td>
						<td class="text-right">Total</td>
					</tr>
					<?php
					foreach($restResin2 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center">0</td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
					<?php 
					}
					
					foreach($restDetailPlus2 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['perse'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if(!empty($restDetailAdd2)){
						?>
						<tr>
							<td style='background-color: burlywood;' class="text-left" colspan='12'><b>Additional Material</b></td>
						</tr>
						<?php
						foreach($restDetailAdd2 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left" colspan='7'><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>TOTAL STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2[0]['total'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MIN STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2[0]['min'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MAX STRUKTURE THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter2[0]['max'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='9'></td>
						<?php
							if($restFooter2[0]['hasil'] == 'OK'){
								$color= '#3aa717';
							}
							else{
								$color= '#a72417';
							}
						?>
						<td class="text-center" colspan='2' style='background-color: <?= $color;?>; color:white; font-weight: bold;'><?= $restFooter2[0]['hasil'];?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	
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
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left" colspan='4'>Material Name</td>
						<td class="text-right" width='6%'>Value</td>
						<td class="text-right" width='6%'>Thickness</td>
						<!--
						<td class="text-right" width='6%'>Multiplier</td>
						<td class="text-right" width='6%'>BW</td>
						<td class="text-right" width='6%'>Sum</td>
						-->
						<td class="text-right" width='6%'>Layer</td>
						<td class="text-right" width='6%'>Containing</td>
						<td class="text-right" width='6%'>Thickness</td>
						<td class="text-right" width='6%'>Total</td>
					</tr>
					<?php
					foreach($restDetail3 AS $val => $valx){
						$bgC1	= 'transparent';
						$Cl1	= 'black';
						$bgC2	= 'transparent';
						$Cl2	= 'black';
						$bgC3	= 'transparent';
						$Cl3	= 'black';
						$bgC4	= 'transparent';
						$Cl4	= 'black';
						$bgC5	= 'transparent';
						$Cl5	= 'black';
						$bgC6	= 'transparent';
						$Cl6	= 'black';
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['value'] != '0.00'){
							$bgC1	= 'black';
							$Cl1	= 'white';
						}
						if($valx['thickness'] != '0.0000'){
							$bgC2	= 'black';
							$Cl2	= 'white';
						}
						if($valx['fak_pengali'] != '0.00'){
							$bgC3	= 'black';
							$Cl3	= 'white';
						}
						if($valx['bw'] != '0.00'){
							$bgC4	= 'black';
							$Cl4	= 'white';
						}
						if($valx['jumlah'] != '0.00'){
							$bgC5	= 'black';
							$Cl5	= 'white';
						}
						if($valx['layer'] != '0.00'){
							$bgC6	= 'black';
							$Cl6	= 'white';
						}
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['total_thickness'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left" colspan='4'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= floatval($valx['value']);?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<!--
						<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= floatval($valx['fak_pengali']);?></td>
						<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= floatval($valx['bw']);?></td>
						<td class="text-right" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= floatval($valx['jumlah']);?></td>
						-->
						<td class="text-center" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= floatval($valx['layer']);?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['total_thickness'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<tr class='bg-blue'>
						<td class="text-left">Category Name</td>
						<td class="text-left" colspan='7'>Material Name</td>
						<td class="text-right">Containing</td>
						<td class="text-right">Perse</td>
						<td class="text-right">Total</td>
					</tr>
					<?php
					foreach($restResin3 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-right">0</td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					foreach($restDetailPlus3 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['perse'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if(!empty($restDetailAdd3)){
						?>
						<tr>
							<td style='background-color: burlywood;' class="text-left" colspan='12'><b>Additional Material</b></td>
						</tr>
						<?php
						foreach($restDetailAdd3 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left" colspan='7'><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>TOTAL EXTERNAL THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter3[0]['total'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MIN EXTERNAL THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter3[0]['min'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'></td>
						<td class="text-left" colspan='3'><b>MAX EXTERNAL THICKNESS</b></td>
						<td class="text-right" style='background-color: black; color:white;' width='6%'><?= $restFooter3[0]['max'];?></td>
						<td class="text-left" colspan='2'></td>
					</tr>
					<tr>
						<td class="text-left" colspan='9'></td>
						<?php
							if($restFooter3[0]['hasil'] == 'OK'){
								$color= '#3aa717';
							}
							else{
								$color= '#a72417';
							}
						?>
						<td class="text-center" colspan='2' style='background-color: <?= $color;?>; color:white; font-weight: bold;'><?php $F3 = ($restFooter3[0]['hasil'] != '' || $restFooter3[0]['hasil'] != null)?$restFooter3[0]['hasil']:'OK'; echo $F3;?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	}
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
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left" colspan='7'>Material Name</td>
						<td class="text-right" width='6%'>Containing</td>
						<td class="text-right" width='6%'>Perse</td>
						<td class="text-right" width='6%'>Total</td>
					</tr>
					<?php
					foreach($restDetailPlus4 AS $val => $valx){
						$bgC7	= 'transparent';
						$Cl7	= 'black';
						$bgC8	= 'transparent';
						$Cl8	= 'black';
						$bgC0	= 'transparent';
						$Cl0	= 'black';
						if($valx['containing'] != '0.000'){
							$bgC7	= 'black';
							$Cl7	= 'white';
						}
						if($valx['perse'] != '0.0000'){
							$bgC8	= 'black';
							$Cl8	= 'white';
						}
						if($valx['last_cost'] != '0.000'){
							$bgC0	= 'black';
							$Cl0	= 'white';
						}
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left" colspan='7'><?= $valx['nm_material'];?></td>
						<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
						<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					
					<?php
					if(!empty($restDetailAdd4)){
						?>
						<tr>
							<td style='background-color: burlywood;' class="text-left" colspan='12'><b>Additional Material</b></td>
						</tr>
						<?php
						foreach($restDetailAdd4 AS $val => $valx){
							$bgC7	= 'transparent';
							$Cl7	= 'black';
							$bgC8	= 'transparent';
							$Cl8	= 'black';
							$bgC0	= 'transparent';
							$Cl0	= 'black';
							if($valx['containing'] != '0.000'){
								$bgC7	= 'black';
								$Cl7	= 'white';
							}
							if($valx['perse'] != '0.0000'){
								$bgC8	= 'black';
								$Cl8	= 'white';
							}
							if($valx['last_cost'] != '0.000'){
								$bgC0	= 'black';
								$Cl0	= 'white';
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left" colspan='7'><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-center" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= floatval($valx['containing']);?></td>
							<td class="text-center" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= floatval($valx['perse']);?></td>
							<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
						</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
		}
		}
	?>
	<script>
		$(document).ready(function(){
			swal.close();
		});
	</script>