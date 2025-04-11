<?php
	$id_product 	= $this->uri->segment(3);
	$id_milik 		= $this->uri->segment(4);
	$id_produksi 	= $this->uri->segment(5);
	$qty_awal 	= $this->uri->segment(6);
	$qty_akhir 	= $this->uri->segment(7);
	$id_milik2 	= $this->uri->segment(8);

	$qty_total = ($qty_akhir - $qty_awal) + 1; 
	
	$checkSO 	= "	SELECT * FROM production_real_detail WHERE id_production_detail = '".$id_milik."' ";
	$restChkSO	= $this->db->query($checkSO)->num_rows();
	if($restChkSO > 0){
		?>
		<div class='note'>
			<p>
				<strong>Info!</strong><br> 
				Data sudah di costing, please update data.<br>
			</p>
		</div>
		<?php
	}
	else{

	$qChMix	= "	SELECT a.* FROM update_real_list_mixing a
					WHERE a.id_milik = '".$id_milik2."' 
					AND (
							('".$qty_awal."' BETWEEN a.qty_awal AND a.qty_akhir )
							OR ('".$qty_akhir."' BETWEEN a.qty_awal AND a.qty_akhir )
						) LIMIT 1
					";
	// echo $id_milik."<br>";
	// echo $id_milik2."<br>";
	$rowMix		= $this->db->query($qChMix)->result_array();
	$qty_awal2 	= floatval($rowMix[0]['qty_awal']);
	$qty_akhir2 = floatval($rowMix[0]['qty_akhir']);
	$qty_total2 = ($qty_akhir2 - $qty_awal2) + 1;
	$id_mixing 	= $rowMix[0]['id'];

	// echo $id_mixing;

	$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
	$row		= $this->db->query($qSupplier)->result_array();

	$HelpDet 	= "bq_component_header";
	$HelpDet2 	= "tmp_banding_mat";
	$HelpDet3 	= "tmp_banding_mat_detail";
	$HelpDet4 	= "tmp_banding_mat_plus";
	$HelpDet5 	= "tmp_banding_mat_add";
	if($row[0]['jalur'] == 'FD'){
		$HelpDet = "so_component_header";
		$HelpDet2 	= "tmp_fd_banding_mat";
		$HelpDet3 	= "tmp_fd_banding_mat_detail";
		$HelpDet4 	= "tmp_fd_banding_mat_plus";
		$HelpDet5 	= "tmp_fd_banding_mat_add";
	}
	
	$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
	$restHeader		= $this->db->query($qHeader)->result_array();
	$product_parent = $restHeader[0]['parent_product'];

	$qDetail1		= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' ORDER BY id_real ASC";
	if($product_parent == 'shop joint' OR $product_parent == 'field joint' OR $product_parent == 'branch joint'){
		$qDetail1		= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='GLASS' ORDER BY id_real ASC";
	}
	$qDetail2		= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' ORDER BY id_real ASC";
	if($product_parent == 'shop joint' OR $product_parent == 'field joint' OR $product_parent == 'branch joint'){
		$qDetail2		= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='RESIN AND ADD' ORDER BY id_real ASC";
	}
	$qDetail3		= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' ORDER BY id_real ASC";
	$restDetail1	= $this->db->query($qDetail1)->result_array();
	$restDetail2	= $this->db->query($qDetail2)->result_array();
	$restDetail3	= $this->db->query($qDetail3)->result_array();
	
	$qDetailPlus1	= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='LINER THIKNESS / CB' ORDER BY id_real ASC";
	$qDetailPlus2	= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='STRUKTUR THICKNESS' ORDER BY id_real ASC";
	$qDetailPlus3	= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='EXTERNAL LAYER THICKNESS' ORDER BY id_real ASC";
	$qDetailPlus4	= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='TOPCOAT' ORDER BY id_real ASC";
	$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
	$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
	$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
	$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();

	$qDetailAdd1	= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='LINER THIKNESS / CB' ORDER BY id_real ASC";
	$qDetailAdd2	= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='STRUKTUR THICKNESS' ORDER BY id_real ASC";
	$qDetailAdd3	= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='EXTERNAL LAYER THICKNESS' ORDER BY id_real ASC";
	$qDetailAdd4	= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='TOPCOAT' ORDER BY id_real ASC";
	$restDetailAdd1	= $this->db->query($qDetailAdd1)->result_array();
	$restDetailAdd2	= $this->db->query($qDetailAdd2)->result_array();
	$restDetailAdd3	= $this->db->query($qDetailAdd3)->result_array();
	$restDetailAdd4	= $this->db->query($qDetailAdd4)->result_array();
	
	//tambahan flange mould /slongsong
	$restDetail2N1	= array();
	$restDetail2N2	= array();
	$restDetail2N1Plus	= array();
	$restDetail2N2Plus	= array();
	$restDetail2N1Add	= array();
	$restDetail2N2Add	= array();

	if($product_parent == 'flange mould' OR $product_parent == 'flange slongsong' OR $product_parent == 'colar' OR $product_parent == 'colar slongsong'){
		$qDetail2N1			= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' ORDER BY id_real ASC";
		$qDetail2N2			= "SELECT a.* FROM ".$HelpDet3." a WHERE a.id_product='".$id_product."' AND a.id_detail='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' ORDER BY id_real ASC";
		$restDetail2N1		= $this->db->query($qDetail2N1)->result_array();
		$restDetail2N2		= $this->db->query($qDetail2N2)->result_array();

		$qDetail2N1Plus		= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='STRUKTUR NECK 1' ORDER BY id_real ASC";
		$qDetail2N2Plus		= "SELECT a.* FROM ".$HelpDet4." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='STRUKTUR NECK 2' ORDER BY id_real ASC";
		$restDetail2N1Plus	= $this->db->query($qDetail2N1Plus)->result_array();
		$restDetail2N2Plus	= $this->db->query($qDetail2N2Plus)->result_array();

		$qDetail2N1Add		= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='STRUKTUR NECK 1' ORDER BY id_real ASC";
		$qDetail2N2Add		= "SELECT a.* FROM ".$HelpDet5." a WHERE a.id_product='".$id_product."' AND (a.id_detail='".$id_mixing."' OR a.id_detail='".$id_milik."') AND a.detail_name='STRUKTUR NECK 2' ORDER BY id_real ASC";
		$restDetail2N1Add	= $this->db->query($qDetail2N1Add)->result_array();
		$restDetail2N2Add	= $this->db->query($qDetail2N2Add)->result_array();
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
		<input type='hidden' name='id_milik' value='<?= $id_milik;?>'>
		<input type='hidden' name='id_milik2' value='<?= $id_milik2;?>'>
		<input type='hidden' name='id_produksi' value='<?= $id_produksi;?>'>
		<input type='hidden' name='id_product' value='<?= $id_product;?>'>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" colspan='5'><b><?=$judul1;?></b></td>
					</tr>
					<tr class='bg-blue'>
						<td class="text-center" width='12%'>Category Name</td>
						<td class="text-center">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-center" width='8%'>Estimasi</td>
						<td class="text-center" width='8%'>Aktual</td>
					</tr>
					<?php
					//Liner Detail
					$no1=0;
					foreach($restDetail1 AS $val => $valx){ $no1++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailLiner[<?=$no1;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailLiner[<?=$no1;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailLiner[<?=$no1;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailLiner[<?=$no1;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailLiner[<?=$no1;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Plus
					$no2=0;
					foreach($restDetailPlus1 AS $val => $valx){ $no2++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
				
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>

						<?php
						if($valx['type_category'] == 'TYP-0002'){
							?>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailLinerPlus[<?=$no2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailLinerPlus[<?=$no2;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' class='form-control' name='DetailLinerPlus[<?=$no2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailLinerPlus[<?=$no2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<?php 
						}
						else{
							?>
							<td class="text-left">
								<select name='DetailLinerPlus[<?=$no2;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' class='form-control' name='DetailLinerPlus[<?=$no2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailLinerPlus[<?=$no2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailLinerPlus[<?=$no2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
						<?php
						} 
						?>

						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailLinerPlus[<?=$no2;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Add
					$no4=0;
					if(!empty($restDetailAdd1)){
						foreach($restDetailAdd1 AS $val => $valx){ $no4++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left">
								<select name='DetailLinerAdd[<?=$no4;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailLinerAdd[<?=$no4;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailLinerAdd[<?=$no4;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailLinerAdd[<?=$no4;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailLinerAdd[<?=$no4;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
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
							<td class="text-center" width='12%'>Category Name</td>
							<td class="text-center">Material Name</td>
							<td class="text-center">Batch Number</td>
							<td class="text-center">Actual Type</td>
							<td class="text-center" width='8%'>Estimasi</td>
							<td class="text-center" width='8%'>Aktual</td>
						</tr>
						<?php
						//Liner Detail
						$no12N1=0;
						foreach($restDetail2N1 AS $val => $valx){ $no12N1++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailN1[<?=$no12N1;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
							</td>
							<td class="text-left">
								<select name='DetailN1[<?=$no12N1;?>][actual_type]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailN1[<?=$no12N1;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
								<input type='hidden' name='DetailN1[<?=$no12N1;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailN1[<?=$no12N1;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
						//Liner Plus
						$no22N1=0;
						foreach($restDetail2N1Plus AS $val => $valx){ $no22N1++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<?php
							if($valx['type_category'] == 'TYP-0002'){
								?>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailN1Plus[<?=$no22N1;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
							</td>
							<td class="text-left">
								<select name='DetailN1Plus[<?=$no22N1;?>][actual_type]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' class='form-control' name='DetailN1Plus[<?=$no22N1;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
								<input type='hidden' name='DetailN1Plus[<?=$no22N1;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<?php 
							}
							else{
								?>
								<td class="text-left">
									<select name='DetailN1Plus[<?=$no22N1;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' class='form-control' name='DetailN1Plus[<?=$no22N1;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
								</td>
								<td class="text-left">
									<input type='text' class='form-control' name='DetailN1Plus[<?=$no22N1;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
									<input type='hidden' name='DetailN1Plus[<?=$no22N1;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
								</td>
							<?php
							} 
							?>
							<!-- <td class="text-left">
								<input type='text' name='DetailN1Plus[<?=$no22N1;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
							</td>
							<td class="text-left">
								<input type='text' name='DetailN1Plus[<?=$no22N1;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailN1Plus[<?=$no22N1;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td> -->
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailN1Plus[<?=$no22N1;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
						//Liner Add
						$no42N1=0;
						if(!empty($restDetail2N1Add)){
							foreach($restDetail2N1Add AS $val => $valx){ $no42N1++;
								$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-left">
									<select name='DetailN1Add[<?=$no42N1;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailN1Add[<?=$no42N1;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
								</td>
								<td class="text-left">
									<input type='text' class='form-control' name='DetailN1Add[<?=$no42N1;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
									<input type='hidden' name='DetailN1Add[<?=$no42N1;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
								</td>
								<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
								<td class="text-right">
									<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailN1Add[<?=$no42N1;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
								</td>
							</tr>
								<?php
							}
						}
						?> 
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
							<td class="text-center" width='12%'>Category Name</td>
							<td class="text-center">Material Name</td>
							<td class="text-center">Batch Number</td>
							<td class="text-center">Actual Type</td>
							<td class="text-center" width='8%'>Estimasi</td>
							<td class="text-center" width='8%'>Aktual</td>
						</tr>
						<?php
						//Liner Detail
						$no12N2=0;
						foreach($restDetail2N2 AS $val => $valx){ $no12N2++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left">
								<select name='DetailN2[<?=$no2;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='hidden' class='form-control' name='DetailN2[<?=$no12N2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<select name='DetailN2[<?=$no12N2;?>][actual_type]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailN2[<?=$no12N2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
								<input type='hidden' name='DetailN2[<?=$no12N2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailN2[<?=$no12N2;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
						//Liner Plus
						$no22N2=0;
						foreach($restDetail2N2Plus AS $val => $valx){ $no22N2++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<?php
							if($valx['type_category'] == 'TYP-0002'){
								?>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailN2Plus[<?=$no22N2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
							</td>
							<td class="text-left">
								<select name='DetailN2Plus[<?=$no22N2;?>][actual_type]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' class='form-control' name='DetailN2Plus[<?=$no22N2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
								<input type='hidden' name='DetailN2Plus[<?=$no22N2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<?php 
							}
							else{
								?>
								<td class="text-left">
									<select name='DetailN2Plus[<?=$no22N2;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' class='form-control' name='DetailN2Plus[<?=$no22N2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
								</td>
								<td class="text-left">
									<input type='text' class='form-control' name='DetailN2Plus[<?=$no22N2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
									<input type='hidden' name='DetailN2Plus[<?=$no22N2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
								</td>
							<?php
							} 
							?>
							<!-- <td class="text-left">
								<input type='text' name='DetailN2Plus[<?=$no22N2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
							</td>
							<td class="text-left">
								<input type='text' name='DetailN2Plus[<?=$no22N2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailN2Plus[<?=$no22N2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td> -->
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailN2Plus[<?=$no22N2;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
						//Liner Add
						$no42N2=0;
						if(!empty($restDetail2N2Add)){
							foreach($restDetail2N2Add AS $val => $valx){ $no42N2++;
								$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
								?>
							<tr>
								<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
								<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
								<td class="text-left">
									<select name='DetailN2Add[<?=$no42N2;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailN2Add[<?=$no42N2;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
								</td>
								<td class="text-left">
									<input type='text' name='DetailN2Add[<?=$no42N2;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
									<input type='hidden' name='DetailN2Add[<?=$no42N2;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
								</td>
								<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
								<td class="text-right">
									<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailN2Add[<?=$no42N2;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
								</td>
							</tr>
								<?php
							}
						}
						?> 
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
						<td class="text-center" width='12%'>Category Name</td>
						<td class="text-center">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-center" width='8%'>Estimasi</td>
						<td class="text-center" width='8%'>Aktual</td>
					</tr>
					<?php
					//Liner Detail
					$no12=0;
					foreach($restDetail2 AS $val => $valx){ $no12++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailStructure[<?=$no12;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailStructure[<?=$no12;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailStructure[<?=$no12;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailStructure[<?=$no12;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailStructure[<?=$no12;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Plus
					$no22=0;
					foreach($restDetailPlus2 AS $val => $valx){ $no22++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<?php
						if($valx['type_category'] == 'TYP-0002'){
							?>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailSturcturePlus[<?=$no22;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailSturcturePlus[<?=$no22;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' class='form-control' name='DetailSturcturePlus[<?=$no22;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailSturcturePlus[<?=$no22;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<?php 
						}
						else{
							?>
							<td class="text-left">
								<select name='DetailSturcturePlus[<?=$no22;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' class='form-control' name='DetailSturcturePlus[<?=$no22;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailSturcturePlus[<?=$no22;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailSturcturePlus[<?=$no22;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
						<?php
						} 
						?>
						<!-- <td class="text-left">
							<input type='text' name='DetailSturcturePlus[<?=$no22;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<input type='text' name='DetailSturcturePlus[<?=$no22;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
							<input type='hidden' name='DetailSturcturePlus[<?=$no22;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td> -->
						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailSturcturePlus[<?=$no22;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Add
					$no42=0;
					if(!empty($restDetailAdd2)){
						foreach($restDetailAdd2 AS $val => $valx){ $no42++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left">
								<select name='DetailStructureAdd[<?=$no42;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailStructureAdd[<?=$no42;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' name='DetailStructureAdd[<?=$no42;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailStructureAdd[<?=$no42;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailStructureAdd[<?=$no42;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
					}
					?> 
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
						<td class="text-center" width='12%'>Category Name</td>
						<td class="text-center">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-center" width='8%'>Estimasi</td>
						<td class="text-center" width='8%'>Aktual</td>
					</tr>
					<?php
					//Liner Detail
					$no13=0;
					foreach($restDetail3 AS $val => $valx){ $no13++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailExternal[<?=$no13;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailExternal[<?=$no13;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailExternal[<?=$no13;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailExternal[<?=$no13;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailExternal[<?=$no13;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Plus
					$no23=0;
					foreach($restDetailPlus3 AS $val => $valx){ $no23++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<?php
						if($valx['type_category'] == 'TYP-0002'){
							?>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailExternalPlus[<?=$no23;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailExternalPlus[<?=$no23;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' class='form-control' name='DetailExternalPlus[<?=$no23;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailExternalPlus[<?=$no23;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<?php 
						}
						else{
							?>
							<td class="text-left">
								<select name='DetailExternalPlus[<?=$no23;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' class='form-control' name='DetailExternalPlus[<?=$no23;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailExternalPlus[<?=$no23;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailExternalPlus[<?=$no23;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
						<?php
						} 
						?>
						<!-- <td class="text-left">
							<input type='text' name='DetailExternalPlus[<?=$no23;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<input type='text' name='DetailExternalPlus[<?=$no23;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
							<input type='hidden' name='DetailExternalPlus[<?=$no23;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td> -->
						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailExternalPlus[<?=$no23;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Add
					$no43=0;
					if(!empty($restDetailAdd3)){
						foreach($restDetailAdd3 AS $val => $valx){ $no43++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left">
								<select name='DetailExternalAdd[<?=$no43;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailExternalAdd[<?=$no43;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' name='DetailExternalAdd[<?=$no43;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailExternalAdd[<?=$no43;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailExternalAdd[<?=$no43;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
					}
					?> 
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
						<td class="text-center" width='12%'>Category Name</td>
						<td class="text-center">Material Name</td>
						<td class="text-center">Batch Number</td>
						<td class="text-center">Actual Type</td>
						<td class="text-center" width='8%'>Estimasi</td>
						<td class="text-center" width='8%'>Aktual</td>
					</tr>
					<?php
					//Liner Plus
					$no24=0;
					foreach($restDetailPlus4 AS $val => $valx){ $no24++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<?php
						if($valx['type_category'] == 'TYP-0002' OR $valx['type_category'] == 'TYP-0001'){
						?>
						<td class="text-left">
							<input type='text' class='form-control' name='DetailTCPlus[<?=$no24;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<select name='DetailTCPlus[<?=$no24;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['actual_type'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' class='form-control' name='DetailTCPlus[<?=$no24;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'> -->
							<input type='hidden' name='DetailTCPlus[<?=$no24;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td>
						<?php 
						}
						else{
							?>
							<td class="text-left">
								<select name='DetailTCPlus[<?=$no24;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' class='form-control' name='DetailTCPlus[<?=$no24;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' class='form-control' name='DetailTCPlus[<?=$no24;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailTCPlus[<?=$no24;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
						<?php
						} 
						?>
						<!-- <td class="text-left">
							<input type='text' name='DetailTCPlus[<?=$no24;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'>
						</td>
						<td class="text-left">
							<input type='text' name='DetailTCPlus[<?=$no24;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
							<input type='hidden' name='DetailTCPlus[<?=$no24;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
						</td> -->
						<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
						<td class="text-right">
							<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailTCPlus[<?=$no24;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
						</td>
					</tr>
						<?php
					}
					//Liner Add
					$no44=0;
					if(!empty($restDetailAdd4)){
						foreach($restDetailAdd4 AS $val => $valx){ $no44++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['type_category'],'delete'=>'N'))->result_array();
							?>
						<tr>
							<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
							<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
							<td class="text-left">
								<select name='DetailTCAdd[<?=$no44;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['batch_number'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailTCAdd[<?=$no44;?>][batch_number]' value='<?= strtoupper($valx['batch_number']);?>'> -->
							</td>
							<td class="text-left">
								<input type='text' name='DetailTCAdd[<?=$no44;?>][actual_type]' value='<?= strtoupper($valx['actual_type']);?>'>
								<input type='hidden' name='DetailTCAdd[<?=$no44;?>][id_real]' value='<?= strtoupper($valx['id_real']);?>'>
							</td>
							<td class="text-right text-bold"><?= number_format($valx['est_material'] * $qty_total, 3);?> Kg</td>
							<td class="text-right">
								<input type='text' style='text-align:right;' class='form-control numberOnly' name='DetailTCAdd[<?=$no4;?>][material_terpakai]' value='<?= number_format((!empty($valx['real_material'])?str_replace(',','.',$valx['real_material']):0), 3);?>'>
							</td>
						</tr>
							<?php
						}
					}
					?> 
				</tbody>
			</table>
		</div>
		<div class='box-footer' style='float:right;'>
			<button type='button' id='updateCheck' class='btn btn-md btn-primary'>Update</button>
			<button type='button' id='sendCheck' class='btn btn-md btn-success'><b>Upload<b></button>
		</div>
	</div>
	<br>
	<?php } ?>
	<style>
		.numberOnly{
			width: 100px;
		}
	</style>
	<script>
		$(document).ready(function(){
			swal.close();
			$('.chosen_select').chosen({
				width : '100%'
			});
		});

		$(".numberOnly").on("keypress keyup blur",function (event) {  
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
	</script>