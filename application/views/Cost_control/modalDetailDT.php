<?php
	$id_product = $this->uri->segment(3);
	$id_milik 	= $this->uri->segment(4);
	// echo $id_product;
	$qHeader		= "SELECT * FROM bq_component_header WHERE id_milik='".$id_milik."' AND id_product='".$id_product."'";
	$qDetail1		= "SELECT * FROM bq_component_detail WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$qDetail2		= "SELECT * FROM bq_component_detail WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$qDetail3		= "SELECT * FROM bq_component_detail WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$detailResin1	= "SELECT * FROM bq_component_detail WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT * FROM bq_component_detail WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT * FROM bq_component_detail WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
	$qDetailPlus1	= "SELECT * FROM bq_component_detail_plus WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000'";
	$qDetailPlus2	= "SELECT * FROM bq_component_detail_plus WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000'";
	$qDetailPlus3	= "SELECT * FROM bq_component_detail_plus WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000'";
	$qDetailPlus4	= "SELECT * FROM bq_component_detail_plus WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000'";
	$qDetailAdd1	= "SELECT * FROM bq_component_detail_add WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB'";
	$qDetailAdd2	= "SELECT * FROM bq_component_detail_add WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS'";
	$qDetailAdd3	= "SELECT * FROM bq_component_detail_add WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS'";
	$qDetailAdd4	= "SELECT * FROM bq_component_detail_add WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='TOPCOAT'";
	$qFooter1		= "SELECT * FROM bq_component_footer WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='LINER THIKNESS / CB'";
	$qFooter2		= "SELECT * FROM bq_component_footer WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='STRUKTUR THICKNESS'";
	$qFooter3		= "SELECT * FROM bq_component_footer WHERE id_milik='".$id_milik."' AND id_product='".$id_product."' AND detail_name='EXTERNAL LAYER THICKNESS'";
	// $qTiming		= "SELECT * FROM bq_component_time WHERE id_milik='".$id_milik."' AND id_product='".$id_product."'";
	// echo $qDetail1;
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
	$restFooter1		= $this->db->query($qFooter1)->result_array();
	$restFooter2		= $this->db->query($qFooter2)->result_array();
	$restFooter3		= $this->db->query($qFooter3)->result_array();
	// $restTiming			= $this->db->query($qTiming)->result_array();
	
	$qCustomer			= "SELECT nm_customer, produk_jual FROM customer WHERE id_customer='".$restHeader[0]['standart_by']."' ";
	$restCustomer		= $this->db->query($qCustomer)->result_array();

?>
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left"><u>Component ID</u></td>
						<td class="text-left" colspan='5'><b><?= $id_product; ?></b></td>
					</tr>
					<tr>
						<td class="text-left"><u>Component GROUP</u></td>
						<td class="text-left" colspan='5'><?= strtoupper($restHeader[0]['parent_product']." || ".$restHeader[0]['resin_sistem']." || ".$restHeader[0]['pressure']." BAR || ".$restHeader[0]['diameter']." MM || ".$restHeader[0]['liner']." MM | ".$restHeader[0]['stiffness']." || ".$restHeader[0]['criminal_barier']." || ".$restHeader[0]['vacum_rate']." || ".$restHeader[0]['aplikasi_product']); ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Custom By</u></td>
						<td class="text-left" colspan='5'><?= $restCustomer[0]['nm_customer']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Product Name</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['nm_product']); ?></td>
						<?php
							if($restHeader[0]['parent_product'] == 'pipe' ){
								$judulLabel	= "Length";
								$isiData	= $restHeader[0]['panjang']." mm";
								$d1Label	= "";
								$d2Isi		= "";
								$persen		= "";
							}
							elseif($restHeader[0]['parent_product'] == 'end cap' ){
								$judulLabel	= "Radius";
								$isiData	= $restHeader[0]['radius']." derajat";
								$d1Label	= "";
								$d2Isi		= "";
								$persen		= "";
							}
							elseif($restHeader[0]['parent_product'] == 'concentric reducer' OR $restHeader[0]['parent_product'] == 'eccentric reducer' ){
								$judulLabel	= "Length";
								$isiData	= $restHeader[0]['panjang']." mm";
								$d1Label	= " 1 / Diameter 2";
								$d2Isi		= " / ".$restHeader[0]['diameter2']." mm";
								$persen		= "";
							}
							elseif($restHeader[0]['parent_product'] == 'elbow mould' OR $restHeader[0]['parent_product'] == 'elbow mitter' ){
								$judulLabel	= "Radius / Angle";
								$isiData	= $restHeader[0]['radius']." derajat / ".$restHeader[0]['angle']." derajat";
								$d1Label	= "/ Type";
								$d2Isi		= ($restHeader[0]['type_elbow'] == 'LR')?' / Long Radius':' / Short Radius';
								$persen		= "(%)";
							}
						?>
						<td class="text-left" width='15%'><u>Diameter <?= $d1Label;?></u></td> 
						<td class="text-left" width='15%'><?= $restHeader[0]['diameter']; ?> mm <?= $d2Isi;?></td>
						
						<td class="text-left"><u>Thickness Pipe (Design)</u></td>
						<td class="text-left"><?= $restHeader[0]['design']; ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Standard Tolerance By</u></td>
						<td class="text-left"><?= strtoupper($restHeader[0]['standart_toleransi']); ?></td>
						<td class="text-left"><u><?= $judulLabel;?></u></td>
						<td class="text-left"><?= $isiData;?></td>
						<td class="text-left"><u>Min/Max</u></td>
						<td class="text-left"><?= $restHeader[0]['min_toleransi']." / ".$restHeader[0]['max_toleransi']; ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Product Application</u></td>
						<td class="text-left"><?= strtoupper($restHeader[0]['aplikasi_product']); ?></td>
						<td class="text-left" width='15%'><u>Waste <?= $persen;?></u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['waste']; ?></td>
						<td class="text-left"><u>Thickness Pipe (EST)</u></td>
						<td class="text-left"><?= $restHeader[0]['est']; ?></td>
					</tr>
					<?php
					if($restHeader[0]['status'] == 'REJECTED'){
					?>
					<tr>
						<td class="text-left" style='color:red;'><u>Reject Reason</u></td>
						<td class="text-left" style='color:red;' colspan='5'><?= strtoupper($restHeader[0]['approve_reason']); ?></td>
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
						<td class="text-right" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= $valx['value'];?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<!--
						<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= $valx['fak_pengali'];?></td>
						<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= $valx['bw'];?></td>
						<td class="text-right" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= $valx['jumlah'];?></td>
						-->
						<td class="text-right" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= $valx['layer'];?></td>
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right">0.0000</td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if($NumDetailAdd1 > 0){
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
							<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
							<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
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
						<td class="text-right" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= $valx['value'];?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= $valx['fak_pengali'];?></td>
						<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= $valx['bw'];?></td>
						<td class="text-right" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= $valx['jumlah'];?></td>
						<td class="text-right" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= $valx['layer'];?></td>
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right">0.0000</td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if($NumDetailAdd2 > 0){
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
							<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
							<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
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
						<td class="text-right" style='background-color: <?=$bgC1;?>; color: <?= $Cl1;?>'><?= $valx['value'];?></td>
						<td class="text-right" style='background-color: <?=$bgC2;?>; color: <?= $Cl2;?>'><?= $valx['thickness'];?></td>
						<!--
						<td class="text-right" style='background-color: <?=$bgC3;?>; color: <?= $Cl3;?>'><?= $valx['fak_pengali'];?></td>
						<td class="text-right" style='background-color: <?=$bgC4;?>; color: <?= $Cl4;?>'><?= $valx['bw'];?></td>
						<td class="text-right" style='background-color: <?=$bgC5;?>; color: <?= $Cl5;?>'><?= $valx['jumlah'];?></td>
						-->
						<td class="text-right" style='background-color: <?=$bgC6;?>; color: <?= $Cl6;?>'><?= $valx['layer'];?></td>
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right">0.0000</td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					<?php
					if($NumDetailAdd3 > 0){
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
							<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
							<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
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
						<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
						<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
						<td class="text-right" style='background-color: <?=$bgC0;?>; color: <?= $Cl0;?>'><?= $valx['last_cost'];?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				<tbody>
					
					<?php
					if($NumDetailAdd4 > 0){
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
							<td class="text-right" style='background-color: <?=$bgC7;?>; color: <?= $Cl7;?>'><?= $valx['containing'];?></td>
							<td class="text-right" style='background-color: <?=$bgC8;?>; color: <?= $Cl8;?>'><?= $valx['perse'];?></td>
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
	?>