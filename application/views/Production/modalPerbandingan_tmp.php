<?php
	$id_product 	= $this->uri->segment(3);
	$id_milik 		= $this->uri->segment(4);
	$id_produksi 	= $this->uri->segment(5);
	$qty_awal 	= floatval($this->uri->segment(6));
	$qty_akhir 	= floatval($this->uri->segment(7));

	$qty_total = ($qty_akhir - $qty_awal) + 1;
	// echo $id_milik;
	$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
	$row		= $this->db->query($qSupplier)->result_array();

	$HelpDet 	= "bq_component_header";
	$HelpDet2 	= "tmp_banding_mat";
	if($row[0]['jalur'] == 'FD'){
		$HelpDet = "so_component_header";
		$HelpDet2 	= "tmp_fd_banding_mat";
	}
	
	$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
	$restHeader		= $this->db->query($qHeader)->result_array();
	$product_parent = $restHeader[0]['parent_product'];
	
	$qDetail1		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
	if($product_parent == 'shop joint' OR $product_parent == 'field joint' OR $product_parent == 'branch joint'){
		$qDetail1		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='GLASS' ORDER BY id_real ASC";
	}
	$qDetail2		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
	if($product_parent == 'shop joint' OR $product_parent == 'field joint' OR $product_parent == 'branch joint'){
		$qDetail2		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='RESIN AND ADD' ORDER BY id_real ASC";
	}
	$restDetail1	= $this->db->query($qDetail1)->result_array();
	$restDetail2	= $this->db->query($qDetail2)->result_array();

	$qDetail3		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
	$qDetail4		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
	$detailResin1	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";

	$restDetail3	= $this->db->query($qDetail3)->result_array(); 
	$restDetail4	= $this->db->query($qDetail4)->result_array();
	$restResin1		= $this->db->query($detailResin1)->result_array();
	$restResin2		= $this->db->query($detailResin2)->result_array();
	$restResin3		= $this->db->query($detailResin3)->result_array(); 
	
	//tambahan flange mould /slongsong
	$restDetail2N1	= array();
	$restDetail2N2	= array();
	$restResin2N1	= array();
	$restResin2N2	= array();

	if($product_parent == 'flange mould' OR $product_parent == 'flange slongsong' OR $product_parent == 'colar' OR $product_parent == 'colar slongsong'){
		$qDetail2N1		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
		$qDetail2N2		= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001'";
		$detailResin2N1	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		$detailResin2N2	= "SELECT a.* FROM ".$HelpDet2." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.type_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
		
		$restDetail2N1	= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2	= $this->db->query($qDetail2N2)->result_array();
		$restResin2N1	= $this->db->query($detailResin2N1)->result_array();
		$restResin2N2	= $this->db->query($detailResin2N2)->result_array(); 
	}
	$judul1 = "LINER THICKNESS";
	if($product_parent == 'shop joint' OR $product_parent == 'field joint' OR $product_parent == 'branch joint'){
		$judul1 = "GLASS";
	}
	
	$judul2 = "STRUCTURE THICKNESS";
	if($product_parent == 'shop joint' OR $product_parent == 'field joint' OR $product_parent == 'branch joint'){
		$judul2 = "RESIN AND ADD";
	}
?>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b><?=$judul1;?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-right" width='8%'>Estimasi</td>
						<td class="text-right" width='8%'>Aktual</td>
						<td class="text-right" width='8%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg	= 0;
					$sumTotDet1Pr	= 0;
					foreach($restDetail1 AS $val => $valx){
						$sumTotDet1Kg += $valx['est_material'] * $qty_total; 
						$sumTotDet1Pr += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);
						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}
						//aktual
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}
						//batch
						$nm_batch = get_name('raw_materials', 'nm_material', 'id_material', $valx['batch_number']);
						if($nm_batch == '-'){
							$nm_batch = $valx['batch_number'];
						}
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($nm_batch);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
						<?php
					}
					$sumTotRes1Kg	= 0;
					$sumTotRes1Pr	= 0;
					foreach($restResin1 AS $val => $valx){
						$sumTotRes1Kg += $valx['est_material'] * $qty_total;
						$sumTotRes1Pr += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);
						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}

						//resin
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}

					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($valx['batch_number']);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
					<?php
					}
					
					$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg;
					$TotLinerPr	= $sumTotDet1Pr + $sumTotRes1Pr;
					
					if($TotLinerKg == 0 AND $TotLinerPr == 0){
						$hasilx1 = 0;
					}
					else{
						$hasilx1 = ((($TotLinerPr/$TotLinerKg)*100)-100);
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='4'><b>TOTAL LINER</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr, 2);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx1, 2);?> %</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	if(!empty($restDetail2N1)){
		?>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<tbody>
						<tr>
							<td class="text-left" colspan='5'><b>STRUCTURE NECK 1 THICKNESS</b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='12%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-center">Batch Number</td>
							<td class="text-center">Actual Type</td>
							<td class="text-right" width='8%'>Estimasi</td>
							<td class="text-right" width='8%'>Aktual</td>
							<td class="text-right" width='8%'>Selisih</td>
						</tr>
						<?php
						$sumTotDet1Kg2N1	= 0;
						$sumTotDet1Pr2N1	= 0;
						foreach($restDetail2N1 AS $val => $valx){
							$sumTotDet1Kg2N1 += $valx['est_material'] * $qty_total;
							$sumTotDet1Pr2N1 += $valx['real_material'];
							$realMat = ck_replace($valx['real_material']);

							if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
								$selisih = 0;
							}
							else{
								$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
							}
							//aktual
							$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
							if($nm_material == '-'){
								$nm_material = $valx['actual_type'];
							}
							//batch
							$nm_batch = get_name('raw_materials', 'nm_material', 'id_material', $valx['batch_number']);
							if($nm_batch == '-'){
								$nm_batch = $valx['batch_number'];
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left"><?= strtoupper($nm_batch);?></td>
							<td class="text-left"><?= strtoupper($nm_material);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
						</tr>
							<?php
						}
						$sumTotRes1Kg2N1	= 0;
						$sumTotRes1Pr2N1	= 0;
						foreach($restResin2N1 AS $val => $valx){
							$sumTotRes1Kg2N1 += $valx['est_material'] * $qty_total;
							$sumTotRes1Pr2N1 += $valx['real_material'];
							$realMat = ck_replace($valx['real_material']);

							if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
								$selisih = 0;
							}
							else{
								$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
							}
							//resin
							$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
							if($nm_material == '-'){
								$nm_material = $valx['actual_type'];
							}
						?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left"><?= strtoupper($valx['batch_number']);?></td>
							<td class="text-left"><?= strtoupper($nm_material);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
						</tr>
						<?php
						}
						
						$TotLinerKg2N1	= $sumTotDet1Kg2N1 + $sumTotRes1Kg2N1;
						$TotLinerPr2N1	= $sumTotDet1Pr2N1 + $sumTotRes1Pr2N1;
						
						if($TotLinerKg2N1 == 0 AND $TotLinerPr2N1 == 0){
							$hasilx2N1 = 0;
						}
						else{
							$hasilx2N1 = ((($TotLinerPr2N1/$TotLinerKg2N1)*100)-100);
						}
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='4'><b>TOTAL STRUCTURE NECK 1</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2N1, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2N1, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx2N1, 2);?> %</b></td>
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
							<td class="text-left" colspan='5'><b>STRUCTURE NECK 2 THICKNESS</b></td>
						</tr>
						<tr class='bg-blue'>
							<td class="text-left" width='12%'>Category Name</td>
							<td class="text-left">Material Name</td>
							<td class="text-center">Batch Number</td>
							<td class="text-center">Actual Type</td>
							<td class="text-right" width='8%'>Estimasi</td>
							<td class="text-right" width='8%'>Aktual</td>
							<td class="text-right" width='8%'>Selisih</td>
						</tr>
						<?php
						$sumTotDet1Kg2N2	= 0;
						$sumTotDet1Pr2N2	= 0;
						foreach($restDetail2N2 AS $val => $valx){
							$sumTotDet1Kg2N2 += $valx['est_material'] * $qty_total;
							$sumTotDet1Pr2N2 += $valx['real_material'];
							$realMat = ck_replace($valx['real_material']);

							if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
								$selisih = 0;
							}
							else{
								$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
							}
							//aktual
							$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
							if($nm_material == '-'){
								$nm_material = $valx['actual_type'];
							}
							//batch
							$nm_batch = get_name('raw_materials', 'nm_material', 'id_material', $valx['batch_number']);
							if($nm_batch == '-'){
								$nm_batch = $valx['batch_number'];
							}
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left"><?= strtoupper($nm_batch);?></td>
							<td class="text-left"><?= strtoupper($nm_material);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
						</tr>
							<?php
						}
						$sumTotRes1Kg2N2	= 0;
						$sumTotRes1Pr2N2	= 0;
						foreach($restResin2N2 AS $val => $valx){
							$sumTotRes1Kg2N2 += $valx['est_material'] * $qty_total;
							$sumTotRes1Pr2N2 += $valx['real_material'];
							$realMat = ck_replace($valx['real_material']);

							if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
								$selisih = 0;
							}
							else{
								$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
							}
							//resin
							$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
							if($nm_material == '-'){
								$nm_material = $valx['actual_type'];
							}
						?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left"><?= strtoupper($valx['batch_number']);?></td>
							<td class="text-left"><?= strtoupper($nm_material);?></td>
							<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
							<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
						</tr>
						<?php
						}
						
						$TotLinerKg2N2	= $sumTotDet1Kg2N2 + $sumTotRes1Kg2N2;
						$TotLinerPr2N2	= $sumTotDet1Pr2N2 + $sumTotRes1Pr2N2;
						
						if($TotLinerKg2N2 == 0 AND $TotLinerPr2N2 == 0){
							$hasilx2N2 = 0;
						}
						else{
							$hasilx2N2 = ((($TotLinerPr2N2/$TotLinerKg2N2)*100)-100);
						}
						?> 
						
						<tr style='background-color: #4edcc1;'>
							<td class="text-left" colspan='4'><b>TOTAL STRUCTURE NECK 2</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2N2, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2N2, 3);?> Kg</b></td>
							<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx2N2, 2);?> %</b></td>
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
						<td class="text-left" colspan='5'><b><?=$judul2;?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-right" width='8%'>Estimasi</td>
						<td class="text-right" width='8%'>Aktual</td>
						<td class="text-right" width='8%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg2	= 0;
					$sumTotDet1Pr2	= 0;
					foreach($restDetail2 AS $val => $valx){
						$sumTotDet1Kg2 += $valx['est_material'] * $qty_total;
						$sumTotDet1Pr2 += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);

						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}
						//aktual
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}
						//batch
						$nm_batch = get_name('raw_materials', 'nm_material', 'id_material', $valx['batch_number']);
						if($nm_batch == '-'){
							$nm_batch = $valx['batch_number'];
						}
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($nm_batch);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
						<?php
					}
					$sumTotRes1Kg2	= 0;
					$sumTotRes1Pr2	= 0;
					foreach($restResin2 AS $val => $valx){
						$sumTotRes1Kg2 += $valx['est_material'] * $qty_total;
						$sumTotRes1Pr2 += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);

						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}
						//resin
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($valx['batch_number']);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
					<?php
					}
					
					$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotRes1Kg2;
					$TotLinerPr2	= $sumTotDet1Pr2 + $sumTotRes1Pr2;
					
					if($TotLinerKg2 == 0 AND $TotLinerPr2 == 0){
						$hasilx2 = 0;
					}
					else{
						$hasilx2 = ((($TotLinerPr2/$TotLinerKg2)*100)-100);
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='4'><b>TOTAL STRUCTURE</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg2, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr2, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx2, 2);?> %</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php if(!empty($restDetail3)){ ?>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b>EXTERNAL THICKNESS</b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-right" width='8%'>Estimasi</td>
						<td class="text-right" width='8%'>Aktual</td>
						<td class="text-right" width='8%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg3	= 0;
					$sumTotDet1Pr3	= 0;
					foreach($restDetail3 AS $val => $valx){
						$sumTotDet1Kg3 += $valx['est_material'] * $qty_total;
						$sumTotDet1Pr3 += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);

						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}
						//aktual
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}
						//batch
						$nm_batch = get_name('raw_materials', 'nm_material', 'id_material', $valx['batch_number']);
						if($nm_batch == '-'){
							$nm_batch = $valx['batch_number'];
						}
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($nm_batch);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
						<?php
					}
					$sumTotRes1Kg3	= 0;
					$sumTotRes1Pr3	= 0;
					foreach($restResin3 AS $val => $valx){
						$sumTotRes1Kg3 += $valx['est_material'] * $qty_total;
						$sumTotRes1Pr3 += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);

						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}
						//resin
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($valx['batch_number']);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
					<?php
					}
					
					$TotLinerKg3	= $sumTotDet1Kg3 + $sumTotRes1Kg3;
					$TotLinerPr3	= $sumTotDet1Pr3 + $sumTotRes1Pr3;
					
					if($TotLinerKg3 == 0 AND $TotLinerPr3 == 0){
						$hasilx3 = 0;
					}
					else{
						$hasilx3 = ((($TotLinerPr3/$TotLinerKg3)*100)-100);
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='4'><b>TOTAL EXTERNAL</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg3, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr3, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx3, 2);?> %</b></td>
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
						<td class="text-left" width='12%'>Category Name</td>
						<td class="text-left">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-right" width='8%'>Estimasi</td>
						<td class="text-right" width='8%'>Aktual</td>
						<td class="text-right" width='8%'>Selisih</td>
					</tr>
					<?php
					$sumTotDet1Kg4	= 0;
					$sumTotDet1Pr4	= 0;
					foreach($restDetail4 AS $val => $valx){
						$sumTotDet1Kg4 += $valx['est_material'] * $qty_total;
						$sumTotDet1Pr4 += $valx['real_material'];
						$realMat = ck_replace($valx['real_material']);

						if(($valx['est_material'] * $qty_total) == 0 OR $realMat == 0){
							$selisih = 0;
						}
						else{
							$selisih = (($realMat) / ($valx['est_material'] * $qty_total)) * 100 - 100;
						}
						//aktual
						$nm_material = get_name('raw_materials', 'nm_material', 'id_material', $valx['actual_type']);
						if($nm_material == '-'){
							$nm_material = $valx['actual_type'];
						}
						//batch
						$nm_batch = get_name('raw_materials', 'nm_material', 'id_material', $valx['batch_number']);
						if($nm_batch == '-'){
							$nm_batch = $valx['batch_number'];
						}
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left"><?= strtoupper($nm_batch);?></td>
						<td class="text-left"><?= strtoupper($nm_material);?></td>
						<td class="text-right" style='background-color: bisque;'><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;'><?=  number_format(((!empty($selisih))?$selisih:'0'),2);?> %</td>
					</tr>
						<?php
					}
					
					$TotLinerKg4	= $sumTotDet1Kg4;
					$TotLinerPr4	= $sumTotDet1Pr4;
					if($TotLinerKg4 == 0 AND $TotLinerPr4 == 0){
						$hasilx = 0;
					}
					else{
						$hasilx = ((($TotLinerPr4/$TotLinerKg4)*100)-100);
					}
					?> 
					
					<tr style='background-color: #4edcc1;'>
						<td class="text-left" colspan='4'><b>TOTAL TOPCOAT</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerKg4, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($TotLinerPr4, 3);?> Kg</b></td>
						<td class="text-right" style='background-color: #4edcc1;'><b><?= number_format($hasilx, 2);?> %</b></td>
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
	