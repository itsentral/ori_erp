<?php
	$id_milik = $this->uri->segment(3);
	$id_produksi = $this->uri->segment(4);
	$no_ipp = str_replace('PRO-','',$id_produksi);
	$qty_awal 	= floatval($this->uri->segment(5));
	$qty_akhir 	= floatval($this->uri->segment(6));
	$qty_total = $this->uri->segment(7);
	$id_product = $this->uri->segment(8);
	$qty = ($qty_akhir - $qty_awal) + 1;
	
	$HelpDet 	= "so_component_header";
	$HelpDet2 	= "so_component_detail";
	$HelpDet3 	= "so_component_detail_plus";
	$HelpDet4 	= "so_component_detail_add";
	// echo $id_production; AND (c.material_terpakai <> '0' AND c.material_terpakai <> '00')
	$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."' AND id_milik='".$id_milik."'";
	$restHeader		= $this->db->query($qHeader)->result_array();
// echo $qHeader;
	
	$qDetail1		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'LINER THIKNESS / CB' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'LINER THIKNESS / CB' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail2		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail3		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail4		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail5		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
							UNION
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
							";
		$qDetail6		= 	"
							(SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
							UNION
							(SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
							UNION
							(SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
							";
		// echo $qDetail1; exit;
		$restDetail1	= $this->db->query($qDetail1)->result_array();
		$restDetail2	= $this->db->query($qDetail2)->result_array();
		$restDetail3	= $this->db->query($qDetail3)->result_array();
		$restDetail4	= $this->db->query($qDetail4)->result_array();
		$restDetail5	= $this->db->query($qDetail5)->result_array();
		$restDetail6	= $this->db->query($qDetail6)->result_array();

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
					if ($restHeader[0]['parent_product']!='branch joint' && 
						$restHeader[0]['parent_product']!='field joint' && 
						$restHeader[0]['parent_product']!='puddle flange' && 
						$restHeader[0]['parent_product']!='shop joint'){
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
						<td class="text-left" colspan='5'><?= spec_base_on_component($id_milik, $HelpDet);?></td>
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
					if ($restHeader[0]['parent_product']!='branch joint' && 
						$restHeader[0]['parent_product']!='field joint' &&
						$restHeader[0]['parent_product']!='puddle flange' && 
						$restHeader[0]['parent_product']!='shop joint')
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
					$sumTotDet1Kg2	= 0;
					foreach($restDetail1 AS $val => $valx){
						$detMatBf	= (!empty($valx['real_material']))? str_replace(',','.',$valx['real_material']): 0;
						$detMat		= $detMatBf / $qty_total * $qty;
						$TotPrice	= $valx['est_material'] * $valx['est_harga'];
						$TotPrice2	= $detMat * $valx['est_harga'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['est_harga'] == 0 || $valx['est_harga'] == null || $valx['est_harga'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet1 += $TotPrice;
						$sumTotDet12 += $TotPrice2;
						$sumTotDet1Kg += $valx['est_material'];
						$sumTotDet1Kg2 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['est_harga'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'], 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat, 3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
						<?php
					}
					$TotLiner		= $sumTotDet1;
					$TotLinerKg		= $sumTotDet1Kg;
					$TotLiner2		= $sumTotDet12;
					$TotLinerKg2	= $sumTotDet1Kg2;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL LINER PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'></td>
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
	if(!empty($restDetail2)){
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
						foreach($restDetail2 AS $val => $valx){ 
							$detMatBf	= (!empty($valx['real_material']))? str_replace(',','.',$valx['real_material']): 0;
							$detMat		= $detMatBf / $qty_total * $qty;
							$TotPrice	= $valx['est_material'] * $valx['est_harga'];
							$TotPrice2	= $detMat * $valx['est_harga'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['est_harga'] == 0 || $valx['est_harga'] == null || $valx['est_harga'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet2N1 += $TotPrice;
							$sumTotDet22N1 += $TotPrice2;
							$sumTotDet2KgN1 += $valx['est_material'];
							$sumTotDet2PrN1 += $valx['est_harga'];
							$sumTotDet2Kg2N1 += $detMat; 
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['est_harga'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat,3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
						
						$TotStructureN1		= $sumTotDet2N1;
						$TotStructureKgN1	= $sumTotDet2KgN1;
						
						$TotStructure2N1	= $sumTotDet22N1;
						$TotStructureKg2N1	= $sumTotDet2Kg2N1;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 1 PRICE</b></td>
							<td class="text-right" style='background-color: #2bff9d;'></td>
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
						foreach($restDetail3 AS $val => $valx){ 
							$detMatBf	= (!empty($valx['real_material']))? str_replace(',','.',$valx['real_material']): 0;
							$detMat		= $detMatBf / $qty_total * $qty;
							$TotPrice	= $valx['est_material'] * $valx['est_harga'];
							$TotPrice2	= $detMat * $valx['est_harga'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['est_harga'] == 0 || $valx['est_harga'] == null || $valx['est_harga'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotDet2N2 += $TotPrice;
							$sumTotDet22N2 += $TotPrice2;
							$sumTotDet2KgN2 += $valx['est_material'];
							$sumTotDet2PrN2 += $valx['est_harga'];
							$sumTotDet2Kg2N2 += $detMat; 
							?>
						<tr>
							<td class="text-left"><?= $valx['nm_category'];?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['est_harga'], 2);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'],3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat,3);?> Kg</td>
							<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
						</tr>
							<?php
						}
						$TotStructureN2		= $sumTotDet2N2;
						$TotStructureKgN2	= $sumTotDet2KgN2;
						$TotStructure2N2	= $sumTotDet22N2;
						$TotStructureKg2N2	= $sumTotDet2Kg2N2;
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='2'><b>TOTAL STRUCTURE NECK 2 PRICE</b></td>
							<td class="text-right" style='background-color: #2bff9d;'></td>
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
					foreach($restDetail4 AS $val => $valx){ 
						$detMatBf	= (!empty($valx['real_material']))? str_replace(',','.',$valx['real_material']): 0;
						$detMat		= $detMatBf / $qty_total * $qty;
						$TotPrice	= $valx['est_material'] * $valx['est_harga'];
						$TotPrice2	= $detMat * $valx['est_harga'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['est_harga'] == 0 || $valx['est_harga'] == null || $valx['est_harga'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet2 += $TotPrice;
						$sumTotDet22 += $TotPrice2;
						$sumTotDet2Kg += $valx['est_material'];
						$sumTotDet2Pr += $valx['est_harga'];
						$sumTotDet2Kg2 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['est_harga'], 2);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat, 3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice2, 2);?></td>
					</tr>
						<?php
					}
					$TotStructure	= $sumTotDet2;
					$TotStructureKg	= $sumTotDet2Kg;
					
					$TotStructure2	= $sumTotDet22;
					$TotStructureKg2	= $sumTotDet2Kg2;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL STRUCTURE PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'></td>
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
	if(!empty($restDetail5)){
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
					foreach($restDetail5 AS $val => $valx){
						$detMatBf	= (!empty($valx['real_material']))? str_replace(',','.',$valx['real_material']): 0;
						$detMat		= $detMatBf / $qty_total * $qty;
						$TotPrice	= $valx['est_material'] * $valx['est_harga'];
						$TotPrice3	= $detMat * $valx['est_harga'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['est_harga'] == 0 || $valx['est_harga'] == null || $valx['est_harga'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet3 += $TotPrice;
						$sumTotDet33 += $TotPrice3;
						$sumTotDet3Kg += $valx['est_material'];
						$sumTotDet3Pr += $valx['est_harga'];
						$sumTotDet3Kg3 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['est_harga'], 2);?></td>
						<td class="text-right"  style='background-color: bisque;'><?= number_format($valx['est_material'], 3);?> Kg</td>
						<td class="text-right"  style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat, 3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice3, 2);?></td>
					</tr>
						<?php
					}
					$TotExternal	= $sumTotDet3;
					$TotExternalKg	= $sumTotDet3Kg;
					$TotExternal3	= $sumTotDet33;
					$TotExternalKg3	= $sumTotDet3Kg3;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL EXTERNAL PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'></td>
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
	if(!empty($restDetail6)){
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
						<td class="text-right" width='10%'>Sub (USD) (Real)</td>
					</tr>
					<?php
					$est_material = 0;
					$est_price = 0;
					$real_material = 0;
					$real_price = 0;
					foreach($restDetail6 AS $val => $valx){
						$detMatBf	= (!empty($valx['real_material']))? str_replace(',','.',$valx['real_material']): 0;
						$detMat		= $detMatBf / $qty_total * $qty;
						$TotPrice	= $valx['est_material'] * $valx['est_harga'];
						$TotPrice4	= $detMat * $valx['est_harga'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['est_harga'] == 0 || $valx['est_harga'] == null || $valx['est_harga'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$est_material += $valx['est_material'];
						$est_price += $TotPrice;
						$real_material += $detMat;
						$real_price += $TotPrice4;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['est_harga'], 2);?></td>
						
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($detMat,3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice4, 2);?></td>
					</tr>
						<?php
					}
					
					$TC_SUM_est_material	= $est_material;
					$TC_SUM_est_price	= $est_price;
					$TC_SUM_real_material	= $real_material;
					$TC_SUM_real_price	= $real_price;
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='2'><b>TOTAL TOPCOAT PRICE</b></td>
						<td class="text-right" style='background-color: #2bff9d;'></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TC_SUM_est_material,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TC_SUM_est_price, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TC_SUM_real_material,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TC_SUM_real_price, 2);?></b></td>
					</tr>
					<tr>
						<td class="text-left" colspan='5' height='50px'></td>
					</tr>
					<tr style='background-color: #4edcc1; font-size: 15px; color: black;'>
						<td class="text-left" colspan='2'><b>TOTAL ALL</b></td>
						<td class="text-right" style='background-color: #2bff9d;'></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLinerKg + $TotStructureKg + $TotStructureKgN1 + $TotStructureKgN2 + $TotExternalKg + $TC_SUM_est_material,3);?> Kg</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= number_format($TotLiner + $TotStructure + $TotStructureN1 + $TotStructureN2 + $TotExternal + $TC_SUM_est_price, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLinerKg2 + $TotStructureKg2 + $TotStructureKg2N1 + $TotStructureKg2N2 + $TotExternalKg3 + $TC_SUM_real_material,3);?> Kg</b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLiner2 + $TotStructure2 + $TotStructure2N1 + $TotStructure2N2 + $TotExternal3 + $TC_SUM_real_price, 2);?></b></td>
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