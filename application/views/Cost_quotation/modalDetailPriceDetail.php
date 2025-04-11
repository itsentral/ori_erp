<?php
	$id_product = $this->uri->segment(3);
	$id_production = $this->uri->segment(4);
	$id_milik = $this->uri->segment(5);
	// echo $id_production; AND (c.material_terpakai <> '0' AND c.material_terpakai <> '00')
	$qHeader		= "SELECT * FROM bq_component_header WHERE id_product='".$id_product."' AND id_milik='".$id_milik."'"; 
	$qDetail1		= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$qDetail2		= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$qDetail2N1		= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$qDetail2N2		= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$qDetail3		= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$detailResin1	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2N1	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2N2	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$qDetailPlus1	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND c.id_production_detail = '".$id_production."'  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail";
	$qDetailPlus2	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus2N1	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND c.id_production_detail = '".$id_production."'  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus2N2	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND c.id_production_detail = '".$id_production."'  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus3	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus4	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND c.id_production_detail = '".$id_production."'  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailAdd1	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail ";
	$qDetailAdd2	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail ";
	$qDetailAdd2N1	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail ";
	$qDetailAdd2N2	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail ";
	$qDetailAdd3	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail ";
	$qDetailAdd4	= "SELECT a.nm_category, a.nm_material, a.last_cost,  a.detail_name, b.price_ref_estimation, c.material_terpakai FROM bq_component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND c.id_production_detail = '".$id_production."' GROUP BY a.id_detail ";
	// echo $detailResin2;  
	$restHeader		= $this->db->query($qHeader)->result_array();
	$restDetail1	= $this->db->query($qDetail1)->result_array();
	$restDetail2	= $this->db->query($qDetail2)->result_array();
	$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
	$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
	$restDetail3	= $this->db->query($qDetail3)->result_array();
	$numRows3		= $this->db->query($qDetail3)->num_rows();
	$numRows2N1		= $this->db->query($qDetail2N1)->num_rows();
	$numRows2N2		= $this->db->query($qDetail2N2)->num_rows();
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
	$NumDetailPlus4		= $this->db->query($qDetailPlus4)->num_rows();
	$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
	$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
	$restDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->result_array();
	$restDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->result_array();
	$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
	$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
	$NumDetailAdd1		= $this->db->query($qDetailAdd1)->num_rows();
	$NumDetailAdd2		= $this->db->query($qDetailAdd2)->num_rows();
	$NumDetailAdd2N1	= $this->db->query($qDetailAdd2N1)->num_rows();
	$NumDetailAdd2N2	= $this->db->query($qDetailAdd2N2)->num_rows();
	$NumDetailAdd3		= $this->db->query($qDetailAdd3)->num_rows();
	$NumDetailAdd4		= $this->db->query($qDetailAdd4)->num_rows();
	
	// $qCustomer			= "SELECT nm_customer, produk_jual FROM customer WHERE id_customer='".$restHeader[0]['customer_real']."' ";
	// $restCustomer		= $this->db->query($qCustomer)->result_array();
	
	$panjang = $restHeader[0]['panjang'];
	
	if($restHeader[0]['parent_product'] == 'pipe' OR $restHeader[0]['parent_product'] == 'pipe slongsong'){
		$panjang = floatval($restHeader[0]['panjang'])- 400;
	}

?>
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" width='20%'><u>Product Name</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['nm_product']); ?></td>
						<td class="text-left" width='15%'><u>Diameter</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['diameter']; ?> mm</td>
						<td class="text-left" width='15%'><u>Length</u></td>
						<td class="text-left" width='15%'><?= $panjang; ?> mm</td>
					</tr>
					<tr>
						<td class="text-left"><u>Standard Tolerance By</u></td>
						<td class="text-left"><?= strtoupper($restHeader[0]['standart_toleransi']); ?></td>
						<td class="text-left"><u>Max</u></td>
						<td class="text-left"><?= $restHeader[0]['max_toleransi']; ?></td>
						<td class="text-left"><u>Min</u></td>
						<td class="text-left"><?= $restHeader[0]['min_toleransi']; ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Product Application</u></td>
						<td class="text-left"><?= strtoupper($restHeader[0]['aplikasi_product']); ?></td>
						<td class="text-left"><u>Thickness Pipe (Design)</u></td>
						<td class="text-left"><?= $restHeader[0]['design']; ?></td>
						<td class="text-left"><u>Thickness Pipe (EST)</u></td>
						<td class="text-left"><?= $restHeader[0]['est']; ?></td>
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
						<td class="text-left" colspan='5'><b>LINER THIKNESS / CB</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Total/Kg (Est)</td>
						<td class="text-right" width='10%'>Sub (USD) (Est)</td>
						<td class="text-right" width='10%'>Total/Kg (Real)</td>
						<td class="text-right" width='10%'>Sub (USD) (Real)</td>
					</tr>
					<?php
					$sumTotDet1	= 0;
					$sumTotDet12	= 0;
					$sumTotDet1Kg	= 0;
					$sumTotDet1Pr	= 0;
					$sumTotDet1Kg2	= 0;
					foreach($restDetail1 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet1 += $TotPrice;
						$sumTotDet12 += $TotPrice2;
						$sumTotDet1Kg += $valx['last_cost'];
						$sumTotDet1Pr += $valx['price_ref_estimation'];
						$sumTotDet1Kg2 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'], 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'], 3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes1	= 0;
					$sumTotRes12	= 0;
					$sumTotRes1Kg	= 0;
					$sumTotRes1Pr	= 0;
					$sumTotRes1Kg2	= 0;
					$sumTotRes1Pr2	= 0;
					foreach($restResin1 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes1 += $TotPrice;
						$sumTotRes12 += $TotPrice2;
						$sumTotRes1Kg += $valx['last_cost'];
						$sumTotRes1Pr += $valx['price_ref_estimation'];
						$sumTotRes1Kg2 += $valx['material_terpakai'];
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus1 = 0;
					$sumTotPlus12 = 0;
					$sumTotPlus1Kg = 0;
					$sumTotPlus1Pr = 0;
					$sumTotPlus1Kg2 = 0;
					foreach($restDetailPlus1 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus1 += $TotPrice;
						$sumTotPlus12 += $TotPrice2;
						$sumTotPlus1Kg += $valx['last_cost'];
						$sumTotPlus1Pr += $valx['price_ref_estimation'];
						$sumTotPlus1Kg2 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<?php
							$dataHelp = (!empty($valx['material_terpakai']))?$valx['material_terpakai']:0;
						
						?>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($dataHelp,3);?> Kg</td> 
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd1 = 0;
					$sumTotAdd12 = 0;
					$sumTotAdd1Kg = 0;
					$sumTotAdd1Pr = 0;
					$sumTotAdd1Kg2 = 0;
					if($NumDetailAdd1 > 0){
						$sumTotAdd1 = 0;
						$sumTotAdd1Kg = 0;
						$sumTotAdd1Kg2 = 0;
						$sumTotAdd1Pr = 0;
						foreach($restDetailAdd1 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd1 += $TotPrice;
							$sumTotAdd12 += $TotPrice2;
							$sumTotAdd1Kg += $valx['last_cost'];
							$sumTotAdd1Pr += $valx['price_ref_estimation'];
							$sumTotAdd1Kg2 += $valx['material_terpakai'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<?php
								$dataHelpx = (!empty($valx['material_terpakai']))?$valx['material_terpakai']:0;
							
							?>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($dataHelpx,3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotLiner	= $sumTotDet1 + $sumTotRes1 + $sumTotAdd1 + $sumTotPlus1;
					$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg + $sumTotAdd1Kg + $sumTotPlus1Kg;
					$TotLinerPr	= $sumTotDet1Pr + $sumTotRes1Pr + $sumTotAdd1Pr + $sumTotPlus1Pr;
					$TotLiner2	= $sumTotDet12 + $sumTotRes12 + $sumTotAdd12 + $sumTotPlus12;
					$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotRes1Kg2 + $sumTotAdd1Kg2 + $sumTotPlus1Kg2;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL LINER PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr, 2);?></b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLiner, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLinerKg2,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLiner2, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$TotStructureN1 = 0;
	$TotStructure2N1 = 0;
	$TotStructureKgN1 = 0;
	$TotStructurePrN1 = 0;
	$TotStructureKg2N1 = 0;
	
	$TotStructureN2 = 0;
	$TotStructure2N2 = 0;
	$TotStructureKgN2 = 0;
	$TotStructurePrN2 = 0;
	$TotStructureKg2N2 = 0;
	if($numRows2N1 > 0){
	?>
		<div class="box box-success">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='12'><b>STRUKTUR NECK 1</b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Price (USD)</td>
							<td class="text-right" width='10%'>Total/Kg (Est)</td>
							<td class="text-right" width='10%'>Sub (USD) (Est)</td>
							<td class="text-right" width='10%'>Total/Kg (Real)</td>
							<td class="text-right" width='10%'>Sub (USD) (Real)</td>
						</tr>
						<?php
						$sumTotDet2N1 = 0;
						$sumTotDet22N1 = 0;
						$sumTotDet2KgN1 = 0;
						$sumTotDet2PrN1 = 0;
						$sumTotDet2Kg2N1 = 0;
						$sumTotDet2Pr2N1 = 0;
						foreach($restDetail2N1 AS $val => $valx){ 
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet2N1 += $TotPrice;
							$sumTotDet22N1 += $TotPrice2;
							$sumTotDet2KgN1 += $valx['last_cost'];
							$sumTotDet2PrN1 += $valx['price_ref_estimation'];
							$sumTotDet2Kg2N1 += $valx['material_terpakai']; 
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
						$sumTotRes2N1 = 0;
						$sumTotRes22N1 = 0;
						$sumTotRes2KgN1 = 0;
						$sumTotRes2PrN1 = 0;
						$sumTotRes2Kg2N1 = 0;
						foreach($restResin2N1 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotRes2N1 += $TotPrice;
							$sumTotRes22N1 += $TotPrice2;
							$sumTotRes2KgN1 += $valx['last_cost'];
							$sumTotRes2PrN1 += $valx['price_ref_estimation'];
							$sumTotRes2Kg2N1 += $valx['material_terpakai'];
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
						<?php
						}
						$sumTotPlus2N1 = 0;
						$sumTotPlus22N1 = 0;
						$sumTotPlus2KgN1 = 0;
						$sumTotPlus2Kg2N1 = 0;
						$sumTotPlus2PrN1 = 0;
						foreach($restDetailPlus2N1 AS $val => $valx){
							$detMat	= (!empty($valx['material_terpakai']))? $valx['material_terpakai']: 0; 
							
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $detMat * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotPlus2N1 += $TotPrice;
							$sumTotPlus22N1 += $TotPrice2;
							$sumTotPlus2KgN1 += $valx['last_cost'];
							$sumTotPlus2PrN1 += $valx['price_ref_estimation'];
							$sumTotPlus2Kg2N1 += $detMat;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat,3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
						?>
					</tbody>
					<tbody>
						<?php
						$sumTotAdd2N1 = 0;
						$sumTotAdd22N1 = 0;
						$sumTotAdd2KgN1 = 0;
						$sumTotAdd2PrN1 = 0;
						$sumTotAdd2Kg2N1 = 0;
						if($NumDetailAdd2N1 > 0){
							$sumTotAdd2N1 = 0;
							foreach($restDetailAdd2N1 AS $val => $valx){
								$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
								$warna 	= "";
								$backg	= "#2bff9d"; 
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotAdd2N1 += $TotPrice;
								$sumTotAdd22N1 += $TotPrice2;
								$sumTotAdd2KgN1 += $valx['last_cost'];
								$sumTotAdd2PrN1 += $valx['price_ref_estimation'];
								$sumTotAdd2Kg2N1 += $valx['material_terpakai'];
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
								
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
						}
						$TotStructureN1		= $sumTotDet2N1 + $sumTotRes2N1 + $sumTotAdd2N1 + $sumTotPlus2N1;
						$TotStructureKgN1	= $sumTotDet2KgN1 + $sumTotRes2KgN1 + $sumTotAdd2KgN1 + $sumTotPlus2KgN1;
						$TotStructurePrN1	= $sumTotDet2PrN1 + $sumTotRes2PrN1 + $sumTotAdd2PrN1 + $sumTotPlus2PrN1;
						
						$TotStructure2N1	= $sumTotDet22N1 + $sumTotRes22N1 + $sumTotAdd22N1 + $sumTotPlus22N1;
						$TotStructureKg2N1	= $sumTotDet2Kg2N1 + $sumTotRes2Kg2N1 + $sumTotAdd2Kg2N1 + $sumTotPlus2Kg2N1;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 1 PRICE</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotStructurePrN1, 2);?></b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN1,3);?> Kg</b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureN1, 2);?></b></td>
							<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructureKg2N1,3);?> Kg</b></td>
							<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructure2N1, 2);?></b></td>
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
							<td class="text-left" colspan='12'><b>STRUKTUR NECK 2</b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='15%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-right" width='10%'>Price (USD)</td>
							<td class="text-right" width='10%'>Total/Kg (Est)</td>
							<td class="text-right" width='10%'>Sub (USD) (Est)</td>
							<td class="text-right" width='10%'>Total/Kg (Real)</td>
							<td class="text-right" width='10%'>Sub (USD) (Real)</td>
						</tr>
						<?php
						$sumTotDet2N2 = 0;
						$sumTotDet22N2 = 0;
						$sumTotDet2KgN2 = 0;
						$sumTotDet2PrN2 = 0;
						$sumTotDet2Kg2N2 = 0;
						$sumTotDet2Pr2N2 = 0;
						foreach($restDetail2N2 AS $val => $valx){ 
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet2N2 += $TotPrice;
							$sumTotDet22N2 += $TotPrice2;
							$sumTotDet2KgN2 += $valx['last_cost'];
							$sumTotDet2PrN2 += $valx['price_ref_estimation'];
							$sumTotDet2Kg2N2 += $valx['material_terpakai']; 
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
						$sumTotRes2N2 = 0;
						$sumTotRes22N2 = 0;
						$sumTotRes2KgN2 = 0;
						$sumTotRes2PrN2 = 0;
						$sumTotRes2Kg2N2 = 0;
						foreach($restResin2N2 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotRes2N2 += $TotPrice;
							$sumTotRes22N2 += $TotPrice2;
							$sumTotRes2KgN2 += $valx['last_cost'];
							$sumTotRes2PrN2 += $valx['price_ref_estimation'];
							$sumTotRes2Kg2N2 += $valx['material_terpakai'];
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
						<?php
						}
						$sumTotPlus2N2 = 0;
						$sumTotPlus22N2 = 0;
						$sumTotPlus2KgN2 = 0;
						$sumTotPlus2Kg2N2 = 0;
						$sumTotPlus2PrN2 = 0;
						foreach($restDetailPlus2N2 AS $val => $valx){
							$detMat	= (!empty($valx['material_terpakai']))? $valx['material_terpakai']: 0; 
							
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice2	= $detMat * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotPlus2N2 += $TotPrice;
							$sumTotPlus22N2 += $TotPrice2;
							$sumTotPlus2KgN2 += $valx['last_cost'];
							$sumTotPlus2PrN2 += $valx['price_ref_estimation'];
							$sumTotPlus2Kg2N2 += $detMat;
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat,3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
						?>
					</tbody>
					<tbody>
						<?php
						$sumTotAdd2N2 = 0;
						$sumTotAdd22N2 = 0;
						$sumTotAdd2KgN2 = 0;
						$sumTotAdd2PrN2 = 0;
						$sumTotAdd2Kg2N2 = 0;
						if($NumDetailAdd2N1 > 0){
							$sumTotAdd2N2 = 0;
							foreach($restDetailAdd2N2 AS $val => $valx){
								$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
								$warna 	= "";
								$backg	= "#2bff9d"; 
								if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
									$warna 	= "white";
									$backg	= "black";
								}
								$sumTotAdd2N2 += $TotPrice;
								$sumTotAdd22N2 += $TotPrice2;
								$sumTotAdd2KgN2 += $valx['last_cost'];
								$sumTotAdd2PrN2 += $valx['price_ref_estimation'];
								$sumTotAdd2Kg2N2 += $valx['material_terpakai'];
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
								
								<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
								<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
								<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
							</tr>
								<?php
							}
						}
						$TotStructureN2		= $sumTotDet2N2 + $sumTotRes2N2 + $sumTotAdd2N2 + $sumTotPlus2N2;
						$TotStructureKgN2	= $sumTotDet2KgN2 + $sumTotRes2KgN2 + $sumTotAdd2KgN2 + $sumTotPlus2KgN2;
						$TotStructurePrN2	= $sumTotDet2PrN2 + $sumTotRes2PrN2 + $sumTotAdd2PrN2 + $sumTotPlus2PrN2;
						
						$TotStructure2N2	= $sumTotDet22N2 + $sumTotRes22N2 + $sumTotAdd22N2 + $sumTotPlus22N2;
						$TotStructureKg2N2	= $sumTotDet2Kg2N2 + $sumTotRes2Kg2N2 + $sumTotAdd2Kg2N2 + $sumTotPlus2Kg2N2;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 2 PRICE</b></td>
							<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotStructurePrN2, 2);?></b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKgN2,3);?> Kg</b></td>
							<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureN2, 2);?></b></td>
							<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructureKg2N2,3);?> Kg</b></td>
							<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructure2N2, 2);?></b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
	
	
	<?php } ?>
	<div class="box box-success">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b>STRUKTUR THICKNESS</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Total/Kg (Est)</td>
						<td class="text-right" width='10%'>Sub (USD) (Est)</td>
						<td class="text-right" width='10%'>Total/Kg (Real)</td>
						<td class="text-right" width='10%'>Sub (USD) (Real)</td>
					</tr>
					<?php
					$sumTotDet2 = 0;
					$sumTotDet22 = 0;
					$sumTotDet2Kg = 0;
					$sumTotDet2Pr = 0;
					$sumTotDet2Kg2 = 0;
					$sumTotDet2Pr2 = 0;
					foreach($restDetail2 AS $val => $valx){ 
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet2 += $TotPrice;
						$sumTotDet22 += $TotPrice2;
						$sumTotDet2Kg += $valx['last_cost'];
						$sumTotDet2Pr += $valx['price_ref_estimation'];
						$sumTotDet2Kg2 += $valx['material_terpakai'];
						
						$detMatDet2	= (!empty($valx['material_terpakai']))? $valx['material_terpakai']: 0; 
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMatDet2,3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes2 = 0;
					$sumTotRes22 = 0;
					$sumTotRes2Kg = 0;
					$sumTotRes2Pr = 0;
					$sumTotRes2Kg2 = 0;
					foreach($restResin2 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice2	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes2 += $TotPrice;
						$sumTotRes22 += $TotPrice2;
						$sumTotRes2Kg += $valx['last_cost'];
						$sumTotRes2Pr += $valx['price_ref_estimation'];
						$sumTotRes2Kg2 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus2 = 0;
					$sumTotPlus22 = 0;
					$sumTotPlus2Kg = 0;
					$sumTotPlus2Kg2 = 0;
					$sumTotPlus2Pr = 0;
					foreach($restDetailPlus2 AS $val => $valx){
						$detMat	= (!empty($valx['material_terpakai']))? $valx['material_terpakai']: 0; 
						
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice2	= $detMat * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus2 += $TotPrice;
						$sumTotPlus22 += $TotPrice2;
						$sumTotPlus2Kg += $valx['last_cost'];
						$sumTotPlus2Pr += $valx['price_ref_estimation'];
						$sumTotPlus2Kg2 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat,3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					$sumTotAdd2 = 0;
					$sumTotAdd22 = 0;
					$sumTotAdd2Kg = 0;
					$sumTotAdd2Pr = 0;
					$sumTotAdd2Kg2 = 0;
					if($NumDetailAdd2 > 0){
						$sumTotAdd2 = 0;
						foreach($restDetailAdd2 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d"; 
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd2 += $TotPrice;
							$sumTotAdd22 += $TotPrice2;
							$sumTotAdd2Kg += $valx['last_cost'];
							$sumTotAdd2Pr += $valx['price_ref_estimation'];
							$sumTotAdd2Kg2 += $valx['material_terpakai'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotStructure	= $sumTotDet2 + $sumTotRes2 + $sumTotAdd2 + $sumTotPlus2;
					$TotStructureKg	= $sumTotDet2Kg + $sumTotRes2Kg + $sumTotAdd2Kg + $sumTotPlus2Kg;
					$TotStructurePr	= $sumTotDet2Pr + $sumTotRes2Pr + $sumTotAdd2Pr + $sumTotPlus2Pr;
					
					$TotStructure2	= $sumTotDet22 + $sumTotRes22 + $sumTotAdd22 + $sumTotPlus22;
					$TotStructureKg2	= $sumTotDet2Kg2 + $sumTotRes2Kg2 + $sumTotAdd2Kg2 + $sumTotPlus2Kg2;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL STRUCTURE PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotStructurePr, 2);?></b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructureKg,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotStructure, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructureKg2,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotStructure2, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$TotExternal = 0;
	$TotExternal3 = 0;
	$TotExternalKg = 0;
	$TotExternalPr = 0;
	$TotExternalKg3 = 0;
	if($numRows3 > 0){
	?>
	<div class="box box-warning">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b>EXTERNAL THICKNESS</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Total/Kg</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
						<td class="text-right" width='10%'>Total/Kg</td>
						<td class="text-right" width='10%'>Sub Price (USD)</td>
					</tr>
					<?php
					$sumTotDet3 =0;
					$sumTotDet33 =0;
					$sumTotDet3Kg =0;
					$sumTotDet3Kg3 =0;
					$sumTotDet3Pr =0;
					foreach($restDetail3 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice3	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet3 += $TotPrice;
						$sumTotDet33 += $TotPrice3;
						$sumTotDet3Kg += $valx['last_cost'];
						$sumTotDet3Pr += $valx['price_ref_estimation'];
						$sumTotDet3Kg3 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right"  style='background-color: bisque;'><?= number_format($valx['last_cost'], 3);?> Kg</td>
						<td class="text-right"  style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'], 3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice3, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes3 =0;
					$sumTotRes33 =0;
					$sumTotRes3Kg =0;
					$sumTotRes3Kg3 =0;
					$sumTotRes3Pr =0;
					foreach($restResin3 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice3	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes3 += $TotPrice;
						$sumTotRes33 += $TotPrice3;
						$sumTotRes3Kg += $valx['last_cost'];
						$sumTotRes3Pr += $valx['price_ref_estimation'];
						$sumTotRes3Kg3 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						
						<td class="text-right"  style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right"  style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice3, 2);?></td>
					</tr>
						<?php
					}
					$sumTotPlus3 =0;
					$sumTotPlus33 =0;
					$sumTotPlus3Kg =0;
					$sumTotPlus3Kg3 =0;
					$sumTotPlus3Pr =0;
					foreach($restDetailPlus3 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice3	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus3 += $TotPrice;
						$sumTotPlus33 += $TotPrice3;
						$sumTotPlus3Kg += $valx['last_cost'];
						$sumTotPlus3Pr += $valx['price_ref_estimation'];
						$sumTotPlus3Kg3 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						
						<td class="text-right"  style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right"  style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice3, 2);?></td>
					</tr>
						<?php
					}
					
					$sumTotAdd3 =0;
					$sumTotAdd33 =0;
					$sumTotAdd3Kg =0;
					$sumTotAdd3Kg3 =0;
					$sumTotAdd3Pr =0;
					if($NumDetailAdd3 > 0){
						$sumTotAdd3 =0;
						$sumTotAdd3Kg =0;
						$sumTotAdd3Kg3 =0;
						$sumTotAdd3Pr =0;
						foreach($restDetailAdd3 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice3	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd3 += $TotPrice;
							$sumTotAdd33 += $TotPrice3;
							$sumTotAdd3Kg += $valx['last_cost'];
							$sumTotAdd3Pr += $valx['price_ref_estimation'];
							$sumTotAdd3Kg3 += $valx['material_terpakai'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td> 
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							
							<td class="text-right"  style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right"  style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice3, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotExternal	= $sumTotDet3 + $sumTotRes3 + $sumTotAdd3 + $sumTotPlus3;
					$TotExternalKg	= $sumTotDet3Kg + $sumTotRes3Kg + $sumTotAdd3Kg + $sumTotPlus3Kg;
					$TotExternalPr	= $sumTotDet3Pr + $sumTotRes3Pr + $sumTotAdd3Pr + $sumTotPlus3Pr;
					
					$TotExternal3	= $sumTotDet33 + $sumTotRes33 + $sumTotAdd33 + $sumTotPlus33;
					$TotExternalKg3	= $sumTotDet3Kg3 + $sumTotRes3Kg3 + $sumTotAdd3Kg3 + $sumTotPlus3Kg3;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL EXTERNAL PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotExternalPr, 2);?></b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotExternalKg,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotExternal, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotExternalKg3,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotExternal3, 2);?></b></td>
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
	if($NumDetailPlus4 > 0){
	?>
	<div class="box box-danger">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b>TOP COAT</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='15%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-right" width='10%'>Price (USD)</td>
						<td class="text-right" width='10%'>Total/Kg (Est)</td>
						<td class="text-right" width='10%'>Sub (USD) (Est)</td>
						<td class="text-right" width='10%'>Total/Kg (Real)</td>
						<td class="text-right" width='10%'>Sub (USD) (Real</td>
					</tr>
					<?php
					$sumTotPlus4 = 0;
					$sumTotPlus44 = 0;
					$sumTotPlus4Kg = 0;
					$sumTotPlus4Kg4 = 0;
					$sumTotPlus4Pr = 0;
					foreach($restDetailPlus4 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$TotPrice4	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus4 += $TotPrice;
						$sumTotPlus44 += $TotPrice4;
						$sumTotPlus4Kg += $valx['last_cost'];
						$sumTotPlus4Pr += $valx['price_ref_estimation'];
						$sumTotPlus4Kg4 += $valx['material_terpakai'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= (!empty($valx['material_terpakai']))?number_format($valx['material_terpakai'],3):number_format(0);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice4, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd4 = 0;
					$sumTotAdd4Kg = 0;
					$sumTotAdd44 = 0;
					$sumTotAdd4Kg4 = 0;
					$sumTotAdd4Pr = 0;
					if($NumDetailAdd4 > 0){
						$sumTotAdd4 = 0;
						$sumTotAdd4Kg = 0;
						$sumTotAdd44 = 0;
						$sumTotAdd4Kg4 = 0;
						$sumTotAdd4Pr = 0;
						foreach($restDetailAdd4 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$TotPrice4	= $valx['material_terpakai'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd4 += $TotPrice;
							$sumTotAdd44 += $TotPrice4;
							$sumTotAdd4Kg += $valx['last_cost'];
							$sumTotAdd4Pr += $valx['price_ref_estimation'];
							$sumTotAdd4Kg4 += $valx['material_terpakai'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['last_cost'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice4, 2);?></td>
						</tr>
							<?php
						}
					}
					$TotCoat	= $sumTotAdd4 + $sumTotPlus4;
					$TotCoatKg	= $sumTotAdd4Kg + $sumTotPlus4Kg;
					$TotCoatPr	= $sumTotAdd4Pr + $sumTotPlus4Pr;
					$TotCoat4	= $sumTotAdd44 + $sumTotPlus44;
					$TotCoatKg4	= $sumTotAdd4Kg4 + $sumTotPlus4Kg4;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL TOPCOAT PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotCoatPr, 2);?></b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotCoatKg,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotCoat, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotCoatKg4,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotCoat4, 2);?></b></td>
					</tr>
					<tr>
						<td class="text-left" colspan='5' height='50px'></td>
					</tr>
					<tr style='background-color: #4edcc1; font-size: 15px; color: black;'>
						<td class="text-left" colspan='2'><b>TOTAL ALL</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr + $TotStructurePr+ $TotStructurePrN1+ $TotStructurePrN2 + $TotExternalPr + $TotCoatPr, 2);?></b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg + $TotStructureKg + $TotStructureKgN1 + $TotStructureKgN2 + $TotExternalKg + $TotCoatKg,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLiner + $TotStructure + $TotStructureN1 + $TotStructureN2 + $TotExternal + $TotCoat, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLinerKg2 + $TotStructureKg2 + $TotStructureKg2N1 + $TotStructureKg2N2 + $TotExternalKg3 + $TotCoatKg4,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLiner2 + $TotStructure2 + $TotStructure2N1 + $TotStructure2N2 + $TotExternal3 + $TotCoat4, 2);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
		}
	?>
	<script>
		$(document).ready(function(){
			swal.close();
		});
	</script>