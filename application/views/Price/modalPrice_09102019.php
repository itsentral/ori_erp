<?php
	$id_product = $this->uri->segment(3);
	// echo $id_product;
	$qHeader		= "SELECT * FROM component_header WHERE id_product='".$id_product."'";
	$qDetail1		= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail2		= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail3		= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$detailResin1	= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$qDetailPlus1	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus2	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus3	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus4	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd1	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB'";
	$qDetailAdd2	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS'";
	$qDetailAdd3	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS'";
	$qDetailAdd4	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='TOPCOAT'";
	
	$restHeader		= $this->db->query($qHeader)->result_array();
	$restDetail1	= $this->db->query($qDetail1)->result_array();
	$restDetail2	= $this->db->query($qDetail2)->result_array();
	$restDetail3	= $this->db->query($qDetail3)->result_array();
	$numRows3		= $this->db->query($qDetail3)->num_rows();
	$restResin1			= $this->db->query($detailResin1)->result_array();
	$restResin2			= $this->db->query($detailResin2)->result_array();
	$restResin3			= $this->db->query($detailResin3)->result_array();
	$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
	$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
	$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
	$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
	$NumDetailPlus4		= $this->db->query($qDetailPlus4)->num_rows();
	$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
	$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
	$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
	$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
	$NumDetailAdd1		= $this->db->query($qDetailAdd1)->num_rows();
	$NumDetailAdd2		= $this->db->query($qDetailAdd2)->num_rows();
	$NumDetailAdd3		= $this->db->query($qDetailAdd3)->num_rows();
	$NumDetailAdd4		= $this->db->query($qDetailAdd4)->num_rows();
	
	

?>
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left"><u>Component ID</u></td>
						<td class="text-left" colspan='5'><b><?= $id_product; ?></b><input type='hidden' name='id_product' id='id_product' value='<?= $id_product;?>'></td>
					</tr>
					<tr>
						<td class="text-left"><u>Component GROUP</u></td>
						<td class="text-left" colspan='5'><?= strtoupper($restHeader[0]['parent_product']." || ".$restHeader[0]['resin_sistem']." || ".$restHeader[0]['pressure']." BAR || ".$restHeader[0]['diameter']." MM || ".$restHeader[0]['liner']." MM | ".$restHeader[0]['stiffness']." || ".$restHeader[0]['criminal_barier']." || ".$restHeader[0]['vacum_rate']." || ".$restHeader[0]['aplikasi_product']); ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Product Name</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['nm_product']); ?></td>
						<td class="text-left" width='15%'><u>Diameter</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['diameter']; ?> mm</td>
						<td class="text-left" width='15%'><u>Length</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['panjang']; ?> mm</td>
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
	<span style='color: #ff0808;'><b>* Check semua harga, bila sudah sesuai klik tombol submit</b></span>
	<div style='float: right;'>
	<b>Update price per material : </b><button type='button' id='saved' name='saved' class='btn btn-success btn-sm' style='min-width:100px; margin-bottom: 5px;'>SUBMIT</button>
	</div>
	<br><br>
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
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet1 += $TotPrice;
						$sumTotDet1Kg += $valx['last_cost'];
						$sumTotDet1Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes1	= 0;
					$sumTotRes1Kg	= 0;
					$sumTotRes1Pr	= 0;
					foreach($restResin1 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes1 += $TotPrice;
						$sumTotRes1Kg += $valx['last_cost'];
						$sumTotRes1Pr += $valx['price_ref_estimation'];
					?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus1 = 0;
					$sumTotPlus1Kg = 0;
					$sumTotPlus1Pr = 0;
					foreach($restDetailPlus1 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus1 += $TotPrice;
						$sumTotPlus1Kg += $valx['last_cost'];
						$sumTotPlus1Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd1 = 0;
					$sumTotAdd1Kg = 0;
					$sumTotAdd1Pr = 0;
					if($NumDetailAdd1 > 0){
						$sumTotAdd1 = 0;
						$sumTotAdd1Kg = 0;
						$sumTotAdd1Pr = 0;
						foreach($restDetailAdd1 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd1 += $TotPrice;
							$sumTotAdd1Kg += $valx['last_cost'];
							$sumTotAdd1Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
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
						<td class="text-right" style='background-color: bisque;'><b><?= $TotLinerKg;?> Kg</b></td>
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
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet2 += $TotPrice;
						$sumTotDet2Kg += $valx['last_cost'];
						$sumTotDet2Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes2 = 0;
					$sumTotRes2Kg = 0;
					$sumTotRes2Pr = 0;
					foreach($restResin2 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes2 += $TotPrice;
						$sumTotRes2Kg += $valx['last_cost'];
						$sumTotRes2Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?>Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
					<?php
					}
					$sumTotPlus2 = 0;
					$sumTotPlus2Kg = 0;
					$sumTotPlus2Pr = 0;
					foreach($restDetailPlus2 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus2 += $TotPrice;
						$sumTotPlus2Kg += $valx['last_cost'];
						$sumTotPlus2Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
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
							$sumTotAdd2Kg += $valx['last_cost'];
							$sumTotAdd2Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
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
						<td class="text-right" style='background-color: bisque;'><b><?= $TotStructureKg;?> Kg</b></td>
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
	if($numRows3 > 0){
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
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet3 += $TotPrice;
						$sumTotDet3Kg += $valx['last_cost'];
						$sumTotDet3Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotRes3 =0;
					$sumTotRes3Kg =0;
					$sumTotRes3Pr =0;
					foreach($restResin3 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotRes3 += $TotPrice;
						$sumTotRes3Kg += $valx['last_cost'];
						$sumTotRes3Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotPlus3 =0;
					$sumTotPlus3Kg =0;
					$sumTotPlus3Pr =0;
					foreach($restDetailPlus3 AS $val => $valx){
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus3 += $TotPrice;
						$sumTotPlus3Kg += $valx['last_cost'];
						$sumTotPlus3Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					
					$sumTotAdd3 =0;
					$sumTotAdd3Kg =0;
					$sumTotAdd3Pr =0;
					if($NumDetailAdd3 > 0){
						$sumTotAdd3 =0;
						$sumTotAdd3Kg =0;
						$sumTotAdd3Pr =0;
						foreach($restDetailAdd3 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd3 += $TotPrice;
							$sumTotAdd3Kg += $valx['last_cost'];
							$sumTotAdd3Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
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
						<td class="text-right"></td>
						<td class="text-right"></td>
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
	if($NumDetailPlus4 > 0){
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
						$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotPlus4 += $TotPrice;
						$sumTotPlus4Kg += $valx['last_cost'];
						$sumTotPlus4Pr += $valx['price_ref_estimation'];
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_ref_estimation'], 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
					$sumTotAdd4 = 0;
					$sumTotAdd4Kg = 0;
					$sumTotAdd4Pr = 0;
					if($NumDetailAdd4 > 0){
						$sumTotAdd4 = 0;
						$sumTotAdd4Kg = 0;
						$sumTotAdd4Pr = 0;
						foreach($restDetailAdd4 AS $val => $valx){
							$TotPrice	= $valx['last_cost'] * $valx['price_ref_estimation'];
							$warna 	= "";
							$backg	= "#2bff9d";
							if($valx['price_ref_estimation'] == 0 || $valx['price_ref_estimation'] == null || $valx['price_ref_estimation'] == ''){
								$warna 	= "white";
								$backg	= "black";
							}
							$sumTotAdd4 += $TotPrice;
							$sumTotAdd4Kg += $valx['last_cost'];
							$sumTotAdd4Pr += $valx['price_ref_estimation'];
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-right" style='background-color: bisque;'><?= $valx['last_cost'];?> Kg</td>
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
						<td class="text-right" style='background-color: bisque;'><b><?= $TotCoatKg;?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotCoatPr, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotCoat, 2);?></b></td>
					</tr>
					<tr>
						<td class="text-left" colspan='5' height='50px'></td>
					</tr>
					<tr style='background-color: #4edcc1; font-size: 18px; color:black;'>
						<td class="text-left" colspan='2'><b>TOTAL ALL</b></td>
						<td class="text-right" style='background-color: bisque;'><b><?= $TotLinerKg + $TotStructureKg + $TotExternalKg + $TotCoatKg;?> Kg</b></td>
						<td class="text-right" style='background-color: #2bff9d;'><b><?= number_format($TotLinerPr + $TotStructurePr + $TotExternalPr + $TotCoatPr, 2);?></b></td>
						<td class="text-right" style='background-color: #ffc4da;'><b><?= number_format($TotLiner + $TotStructure + $TotExternal + $TotCoat, 2);?><input type='hidden' id='product_price' name='product_price' value='<?= number_format($TotLiner + $TotStructure + $TotExternal + $TotCoat, 2);?>' ></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
		}
	?>
	<div style='float: right;'>
		<b>Update price per material : </b><button type='button' id='saved' name='saved' class='btn btn-success btn-sm' style='min-width:100px; margin-bottom: 5px;'>SUBMIT</button>
	</div><br><br>
	
	
	<script>
		$(document).on('click', '#saved', function(){
		
			swal({
			  title: "Are you sure?",
			  text: "You will not be able to process again this data!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/updatePrice',
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){											
								swal({
									  title	: "Save Success!",
									  text	: data.pesan,
									  type	: "success",
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								window.location.href = base_url + active_controller+'/ajukan_price';
							}
							else if(data.status == 0){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							}
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 7000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
	
	</script>