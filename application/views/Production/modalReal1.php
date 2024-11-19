<?php
	$id_product = $this->uri->segment(3);
	$id_produksi = $this->uri->segment(4);
	$idProducktion = $this->uri->segment(5);
	$id_milik = $this->uri->segment(6);
	// echo "Milik=>".$id_milik;
	
	$qProduksi		= "SELECT * FROM production_header WHERE id_produksi='".$id_produksi."' ";
	$restProduksi	= $this->db->query($qProduksi)->result_array();
	
	$qHeader			= "SELECT * FROM bq_component_header WHERE id_product='".$id_product."'";
	$restHeader			= $this->db->query($qHeader)->result_array();
	
	//LINER
	$qDetail1			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$restDetail1		= $this->db->query($qDetail1)->result_array(); 
	$qDetailResin1		= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
	$restDetailResin1	= $this->db->query($qDetailResin1)->result_array(); 
	$qDetailPlus1		= "SELECT * FROM bq_component_detail_plus WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
	$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
	
	//STRUKTURE
	$qDetail2			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$restDetail2		= $this->db->query($qDetail2)->result_array();
	$qDetailResin2		= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
	$restDetailResin2	= $this->db->query($qDetailResin2)->result_array();
	$qDetailPlus2		= "SELECT * FROM bq_component_detail_plus WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
	$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
	
	//EXTERNAL
	$qDetail3			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	$restDetail3		= $this->db->query($qDetail3)->result_array();
	$NumDetail3			= $this->db->query($qDetail3)->num_rows();
	$qDetailResin3		= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1";
	$restDetailResin3	= $this->db->query($qDetailResin3)->result_array();
	$qDetailPlus3		= "SELECT * FROM bq_component_detail_plus WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
	$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
	
	//TOPCOAT
	$qDetailPlus4		= "SELECT * FROM bq_component_detail_plus WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001')";
	$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
?>
	<input type='text' name='id_produksi' class='THide' value='<?= $id_produksi;?>'>
	<input type='text' name='product' class='THide' value='<?= $id_product;?>'>
	<input type='text' name='id_production_detail' class='THide' value='<?= $idProducktion;?>'>
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left"width='13%'><u>IPP Number</u></td>
						<td class="text-left" colspan='5'><b><?= $restProduksi[0]['no_ipp']; ?></b></td>
					</tr>
					<!--
					<tr>
						<td class="text-left" width='20%'><u>Product Name</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['nm_product']); ?></td>
						<td class="text-left" width='15%'><u>Diameter</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['diameter']; ?></td>
						<td class="text-left" width='15%'><u>Width</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['panjang']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Standard Tolerance By</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['standart_toleransi']); ?></td>
						<td class="text-left" width='15%'><u>Max</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['max_toleransi']; ?></td>
						<td class="text-left" width='15%'><u>Min</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['min_toleransi']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Product Application</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['aplikasi_product']); ?></td>
						<td class="text-left" width='15%'><u>Thickness Pipe (Design)</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['design']; ?></td>
						<td class="text-left" width='15%'><u>Thickness Pipe (EST)</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['est']; ?></td>
					</tr>
					-->
				</tbody>
			</table>
		</div>
	</div>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead>
					<tr class='bg-blue' align='center'>
						<td width='13%'>Material</td>
						<td width='5%'>Layer</td>
						<td width='27%'>Tipe Material</td>
						<td width='9%'>Qty</td>
						<td width='16%'>Lot/Batch Num</td>
						<td width='12%'>Actual Type</td>
						<td width='8%'>Layer</td>
						<td width='8%'>Terpakai</td>
					</tr>
					<tr>
						<td class="text-left" colspan='8'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
					</tr>
				<thead>
				<tbody id='restDetail1'>
					<?php
					$no1 = 0;
					foreach($restDetail1 AS $val => $valx){
						$no1++;
						?>
						<tr id='tr_<?= $no1;?>'>
							<td class="text-left">
								<?= $valx['nm_category'];?>
								<input type='text' name='DetailUtama[<?=$no1;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailUtama[<?=$no1;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-center">
								<?php 
								if($valx['layer'] > 0){
									echo floatval($valx['layer']);
								}
								else{
									echo "-";
								}
								?>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][batch_number]' id='batch_number_<?= $no1;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][actual_type]' id='actual_type_<?= $no1;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][layer]' id='layer_<?= $no1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][material_terpakai]' id='material_terpakai_<?= $no1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</tbody>";
				echo "<tbody id='restDetailResin1'>";
					$no2 = 0;
					foreach($restDetailResin1 AS $val => $valx){
						$no2++;
						?>
						<tr id='tr_<?= $no2;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailResin[<?=$no2;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailResin[<?=$no2;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailResin[<?=$no2;?>][batch_number]' id='batch_number_<?= $no2;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right" colspan='2'><input type='text' name='DetailResin[<?=$no2;?>][actual_type]' id='actual_type_<?= $no2;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailResin[<?=$no2;?>][material_terpakai]' id='material_terpakai_<?= $no2;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</tbody>";
				echo "<tbody id='restDetailPlus1'>";
					$no3 = 0;
					foreach($restDetailPlus1 AS $val => $valx){
						$no3++;
						?>
						<tr id='tr_<?= $no3;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailPlus[<?=$no3;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailPlus[<?=$no3;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailPlus[<?=$no3;?>][batch_number]' id='batch_number_<?= $no3;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right" colspan='2'><input type='text' name='DetailPlus[<?=$no3;?>][actual_type]' id='actual_type_<?= $no3;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailPlus[<?=$no3;?>][material_terpakai]' id='material_terpakai_<?= $no3;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</tbody>";
					?>
					<tr>
						<td class="text-left" colspan='8'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
					</tr>
					<?php
				echo "<tbody id='restDetail2'>";
					$no5 = 0;
					foreach($restDetail2 AS $val => $valx){
						$no5++;
						$benang = "";
						if($valx['id_category'] == 'TYP-0005'){
							$benang = "<br><br> Bn: ".floatval($valx['jumlah'])."<br><br> Bw: ".floatval($valx['bw']);
						}
						?>
						<tr id='tr_<?= $no5;?>'>
							<td class="text-left">
								<?= $valx['nm_category'];?>
								<input type='text' name='DetailUtama2[<?=$no5;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailUtama2[<?=$no5;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-center"><?= floatval($valx['layer']).$benang;?></td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailUtama2[<?=$no5;?>][batch_number]' id='batch_number_<?= $no5;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailUtama2[<?=$no5;?>][actual_type]' id='actual_type_<?= $no5;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right">
								<input type='text' name='DetailUtama2[<?=$no5;?>][layer]' id='layer_<?= $no5;?>' class='form-control input-sm numberOnly' autocomplete='off'>
								<?php
									if($valx['id_category'] == 'TYP-0005'){
										?>
											<input type='text' style='margin-top: 5px;' placeholder='Benang' name='DetailUtama2[<?=$no5;?>][benang]' id='benang_<?= $no5;?>' class='form-control input-sm numberOnly' autocomplete='off'>
											<input type='text' style='margin-top: 5px;' placeholder='Bandwidch' name='DetailUtama2[<?=$no5;?>][bw]' id='bw_<?= $no5;?>' class='form-control input-sm numberOnly' autocomplete='off'>
										<?php
									}
								?>
							</td>
							<td class="text-right"><input type='text' name='DetailUtama2[<?=$no5;?>][material_terpakai]' id='material_terpakai_<?= $no5;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</tbody>";
				echo "<tbody id='restDetailResin2'>";
					$no6 = 0;
					foreach($restDetailResin2 AS $val => $valx){
						$no6++;
						?>
						<tr id='tr_<?= $no6;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailResin2[<?=$no6;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailResin2[<?=$no6;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailResin2[<?=$no6;?>][batch_number]' id='batch_number_<?= $no6;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right" colspan='2'><input type='text' name='DetailResin2[<?=$no6;?>][actual_type]' id='actual_type_<?= $no6;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailResin2[<?=$no6;?>][material_terpakai]' id='material_terpakai_<?= $no6;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</tbody>";
				echo "<tbody id='restDetailPlus2'>";
					$no7 = 0;
					foreach($restDetailPlus2 AS $val => $valx){
						$no7++;
						?>
						<tr id='tr_<?= $no7;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailPlus2[<?=$no7;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailPlus2[<?=$no7;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailPlus2[<?=$no7;?>][batch_number]' id='batch_number_<?= $no7;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right" colspan='2'><input type='text' name='DetailPlus2[<?=$no7;?>][actual_type]' id='actual_type_<?= $no7;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailPlus2[<?=$no7;?>][material_terpakai]' id='material_terpakai_<?= $no7;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</tbody>";
				
				if($NumDetail3 > 0){
						?>
						<tr>
							<td class="text-left" colspan='8'><b><?= $restDetail3[0]['detail_name']; ?></b></td>
						</tr>
						<?php
					echo "<tbody id='restDetail3'>";
						$no9 = 0;
						foreach($restDetail3 AS $val => $valx){
							$no9++;
							?>
							<tr id='tr_<?= $no9;?>'>
								<td class="text-left">
									<?= $valx['nm_category'];?>
									<input type='text' name='DetailUtama3[<?=$no9;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailUtama3[<?=$no9;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								</td>
								<td class="text-right"><?= $valx['layer'];?></td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][batch_number]' id='batch_number_<?= $no9;?>' class='form-control input-sm' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][actual_type]' id='actual_type_<?= $no9;?>' class='form-control input-sm' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][layer]' id='layer_<?= $no9;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][material_terpakai]' id='material_terpakai_<?= $no9;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
							</tr>
							<?php
						}
					echo "</tbody>";
					echo "<tbody id='restDetailResin3'>";
						$no10 = 0;
						foreach($restDetailResin3 AS $val => $valx){
							$no10++;
							?>
							<tr id='tr_<?= $no10;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailResin3[<?=$no10;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailResin3[<?=$no10;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
								<td class="text-right"><input type='text' name='DetailResin3[<?=$no10;?>][batch_number]' id='batch_number_<?= $no10;?>' class='form-control input-sm' autocomplete='off'></td>
								<td class="text-right" colspan='2'><input type='text' name='DetailResin3[<?=$no10;?>][actual_type]' id='actual_type_<?= $no10;?>' class='form-control input-sm' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailResin3[<?=$no10;?>][material_terpakai]' id='material_terpakai_<?= $no10;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
							</tr>
							<?php
						}
					echo "</tbody>";
					echo "<tbody id='restDetailPlus3'>";
						$no11 = 0;
						foreach($restDetailPlus3 AS $val => $valx){
							$no11++;
							?>
							<tr id='tr_<?= $no11;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailPlus3[<?=$no11;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailPlus3[<?=$no11;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
								<td class="text-right"><input type='text' name='DetailPlus3[<?=$no11;?>][batch_number]' id='batch_number_<?= $no11;?>' class='form-control input-sm' autocomplete='off'></td>
								<td class="text-right" colspan='2'><input type='text' name='DetailPlus3[<?=$no11;?>][actual_type]' id='actual_type_<?= $no11;?>' class='form-control input-sm' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailPlus3[<?=$no11;?>][material_terpakai]' id='material_terpakai_<?= $no11;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
							</tr>
							<?php
						}
					echo "</tbody>";
				}
					?>
			</table>
		</div>
	</div>
	
	
	<div class="box box-danger">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='12'><b><?= $restDetailPlus4[0]['detail_name']; ?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td width='18%'>Material</td>
						<td width='27%'>Tipe Material</td>
						<td width='9%'>Qty</td>
						<td width='16%'>Lot/Batch Num</td>
						<td width='20%'>Actual Type</td>
						<td width='8%'>Terpakai</td>
					</tr>
					<?php
				echo "<tbody id='restDetailPlus4'>";
					$no13 = 0;
					foreach($restDetailPlus4 AS $val => $valx){
						$no13++;
						?>
						<tr id='tr_<?= $no13;?>'>
							<td class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailPlus4[<?=$no13;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailPlus4[<?=$no13;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailPlus4[<?=$no13;?>][batch_number]' id='batch_number_<?= $no13;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailPlus4[<?=$no13;?>][actual_type]' id='actual_type_<?= $no13;?>' class='form-control input-sm' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailPlus4[<?=$no13;?>][material_terpakai]' id='material_terpakai_<?= $no13;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						</tr>
						<?php
					}
				echo "</body>";
				?>
			</table>
		</div>
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					
					<tr>
						<td width='25%'><b>Thickness Est</b></td>
						<td width='25%'><b><?= $restHeader[0]['est'];?></b></td>
						<td width='25%'><b>Thickness Real</b></td>
						<td width='25%'><input type='text' name='est_real' id='est_real' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
				</tbody>	
			</table>
		</div>
		<div class='box-footer'>
			<button type='button' id='updateRealMat1' class='btn btn-md btn-primary'>Update</button>
			<?php
			// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','id'=>'updateRealMat'));
			// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
		</div>
	</div>
	
	<script>
		$(".THide").hide();
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		
		
	</script>