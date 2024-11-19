
	<input type='text' name='id_produksi' class='THide' value='<?= $id_produksi;?>'>
	<input type='text' name='product' class='THide' value='<?= $id_product;?>'>
	<input type='text' name='id_production_detail' class='THide' value='<?= $idProducktion;?>'>
	<input type='text' name='qty_awal' class='THide' value='<?= $qty_awal;?>'>
	<input type='text' name='qty_akhir' class='THide' value='<?= $qty_akhir;?>'>
	<input type='text' name='id_milik' class='THide' value='<?= $id_milik;?>'>
	<div class="box box-primary">
		<br>
		<div class="callout callout-success">
			<h4>Reminder!</h4>
			KOMA  '   ,   ' Sparator ribuan<br>
			TITIK '   .   ' Decimal
		</div>
		<div class="box-body" style=""><br>
			<div class="form-group row">
				<div class='col-sm-2 '>
				   <label class='label-control'>IPP Number</label>
				</div>
				<div class='col-sm-10 '>
				   <?= $restProduksi[0]['no_ipp']; ?>
				</div>
			</div>
			<div class="form-group row">
				<div class='col-sm-2 '>
				   <label class='label-control'>Warehouse Produksi</label>
				</div>
				<div class='col-sm-3 '>
					<select name="id_gudang" id="id_gudang" class='form-control chosen_select'>
						<option value="0">Pilih Warehouse Produksi</option>
					<?php
					foreach ($warehouse as $key => $value) {
						echo "<option value='".$value['id']."'>".$value['nm_gudang']."</option>";
					}
					?>
					</select>
				</div>
			</div>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead>
					<tr class='bg-blue' align='center'>
						<td width='18%' colspan='2'>Material</td>
						<td width='27%'>Tipe Material</td>
						<td width='9%'>Qty</td>
						<td width='16%'>Actual Type</td>
						<td width='20%' colspan='2'>Persentase</td>
						<td width='8%'>Terpakai</td>
					</tr>
					<?php
					if(!empty($restDetail1[0]['detail_name'])){
					?>
					<tr class='title'>
						<td class="text-left" colspan='8'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
					</tr>
					<?php }?>
				<thead>
				<?php
					$no2 = 0;
					foreach($restDetailResin1 AS $val => $valx){
						$no2++;
						$get_resin = $this->db->query("SELECT material_terpakai FROM tmp_production_real_detail WHERE id_detail='".$valx['id_detail']."' AND id_production_detail='".$idProducktion."' LIMIT 1")->result();
						$real_resin = (!empty($get_resin))?$get_resin[0]->material_terpakai:'';
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
						?>
						<tr id='tr_<?= $no2;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailResin[<?=$no2;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailResin[<?=$no2;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='hidden' name='DetailResin[<?=$no2;?>][actual_type]' id='actual_type_<?= $no2;?>' class='form-control input-sm' autocomplete='off' value='-'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
							<td class="text-left" colspan='3'>
								<select name='DetailResin[<?=$no2;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailResin[<?=$no2;?>][batch_number]' id='batch_number_<?= $no2;?>' class='form-control input-sm' autocomplete='off' value='-'> -->
							</td>
							<td class="text-right"><input type='text' name='DetailResin[<?=$no2;?>][material_terpakai]' id='material_terpakai_<?= $no2;?>' class='form-control input-sm numberOnly3 resin1' autocomplete='off' value='<?=$real_resin;?>'></td>
						</tr>
						<?php
					}
					$no3 = 0;
					foreach($restDetailPlus1 AS $val => $valx){
						$no3++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
						?>
						<tr id='tr_<?= $no3;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailPlus[<?=$no3;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailPlus[<?=$no3;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='hidden' name='DetailPlus[<?=$no3;?>][containing]' id='batch_number_<?= $no3;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
							<td class="text-left">
								<select name='DetailPlus[<?=$no3;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailPlus[<?=$no3;?>][batch_number]' id='batch_number_<?= $no3;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
							</td>
							<td class="text-right" colspan='2'><input type='text' name='DetailPlus[<?=$no3;?>][actual_type]' id='actual_type_<?= $no3;?>' class='form-control input-sm numberOnly getResin1 aCenter' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailPlus[<?=$no3;?>][material_terpakai]' id='material_terpakai_<?= $no3;?>' class='form-control input-sm numberOnly aRight terpakai1' autocomplete='off' readonly value='0'></td>
						</tr>
						<?php
					}
					$no4 = 0;
					foreach($restDetailAdd1 AS $val => $valx){
						$no4++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
						?>
						<tr id='tr_<?= $no4;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailAdd[<?=$no4;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailAdd[<?=$no4;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='hidden' name='DetailAdd[<?=$no4;?>][containing]' id='batch_number_<?= $no4;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
							<td class="text-left">
								<select name='DetailAdd[<?=$no4;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailAdd[<?=$no4;?>][batch_number]'id='batch_number_<?= $no4;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
							</td>
							<td class="text-right" colspan='2'><input type='text' name='DetailAdd[<?=$no4;?>][actual_type]' id='actual_type_<?= $no4;?>' class='form-control input-sm numberOnly getResin1 aCenter'autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailAdd[<?=$no4;?>][material_terpakai]' id='material_terpakai_<?= $no4;?>' class='form-control input-sm numberOnly aRight terpakai1' autocomplete='off' readonly value='0'></td>
						</tr>
						<?php
					}
					
					
					//NECK 1
					if(!empty($restDetail2N1)){
						?>
						<tr class='title'>
							<td class="text-left" colspan='8'><b><?= $restDetail2N1[0]['detail_name']; ?></b></td>
						</tr>
						<?php
						
						$no6 = 0;
						foreach($restDetailResin2N1 AS $val => $valx){
							$no6++;
							$get_resin = $this->db->query("SELECT material_terpakai FROM tmp_production_real_detail WHERE id_detail='".$valx['id_detail']."' AND id_production_detail='".$idProducktion."' LIMIT 1")->result();
							$real_resin = (!empty($get_resin))?$get_resin[0]->material_terpakai:'';
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no6;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailResin2N1[<?=$no6;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailResin2N1[<?=$no6;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailResin2N1[<?=$no6;?>][actual_type]' id='actual_type_<?= $no6;?>' class='form-control input-sm' autocomplete='off' value='-'>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left" colspan='3'>
									<select name='DetailResin2N1[<?=$no6;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailResin2N1[<?=$no6;?>][batch_number]' id='batch_number_<?= $no6;?>' class='form-control input-sm' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right"><input type='text' name='DetailResin2N1[<?=$no6;?>][material_terpakai]' id='material_terpakai_<?= $no6;?>' class='form-control input-sm numberOnly3 resin2N1' autocomplete='off' value='<?=$real_resin;?>'></td>
							</tr>
							<?php
						}
						$no7 = 0;
						foreach($restDetailPlus2N1 AS $val => $valx){
							$no7++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no7;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailPlus2N1[<?=$no7;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailPlus2N1[<?=$no7;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailPlus2N1[<?=$no7;?>][containing]' id='batch_number_<?= $no7;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailPlus2N1[<?=$no7;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>	
									<!-- <input type='text' name='DetailPlus2N1[<?=$no7;?>][batch_number]' id='batch_number_<?= $no7;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailPlus2N1[<?=$no7;?>][actual_type]' id='actual_type_<?= $no7;?>' class='form-control input-sm numberOnly getResin2N1 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailPlus2N1[<?=$no7;?>][material_terpakai]' id='material_terpakai_<?= $no7;?>' class='form-control input-sm numberOnly aRight terpakai2N1' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
						$no8 = 0;
						foreach($restDetailAdd2N1 AS $val => $valx){
							$no8++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no8;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailAdd2N1[<?=$no8;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailAdd2N1[<?=$no8;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailAdd2N1[<?=$no8;?>][containing]' id='batch_number_<?= $no8;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailAdd2N1[<?=$no8;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailAdd2N1[<?=$no8;?>][batch_number]' id='batch_number_<?= $no8;?>'  class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailAdd2N1[<?=$no8;?>][actual_type]' id='actual_type_<?= $no8;?>' class='form-control input-sm numberOnly getResin2N1 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailAdd2N1[<?=$no8;?>][material_terpakai]' id='material_terpakai_<?= $no8;?>' class='form-control input-sm numberOnly aRight terpakai2N1' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
					}
					
					//NECK 2
					if(!empty($restDetail2N2)){
						?>
						<tr class='title'>
							<td class="text-left" colspan='8'><b><?= $restDetail2N2[0]['detail_name']; ?></b></td>
						</tr>
						<?php
						
						$no6 = 0;
						foreach($restDetailResin2N2 AS $val => $valx){
							$no6++;
							$get_resin = $this->db->query("SELECT material_terpakai FROM tmp_production_real_detail WHERE id_detail='".$valx['id_detail']."' AND id_production_detail='".$idProducktion."' LIMIT 1")->result();
							$real_resin = (!empty($get_resin))?$get_resin[0]->material_terpakai:'';
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no6;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailResin2N2[<?=$no6;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailResin2N2[<?=$no6;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailResin2N2[<?=$no6;?>][actual_type]' id='actual_type_<?= $no6;?>' class='form-control input-sm' autocomplete='off' value='-'>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left" colspan='3'>
									<select name='DetailResin2N2[<?=$no6;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailResin2N2[<?=$no6;?>][batch_number]' id='batch_number_<?= $no6;?>' class='form-control input-sm' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right"><input type='text' name='DetailResin2N2[<?=$no6;?>][material_terpakai]' id='material_terpakai_<?= $no6;?>' class='form-control input-sm numberOnly3 resin2N2' autocomplete='off' value='<?=$real_resin;?>'></td>
							</tr>
							<?php
						}
						$no7 = 0;
						foreach($restDetailPlus2N2 AS $val => $valx){
							$no7++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no7;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailPlus2N2[<?=$no7;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailPlus2N2[<?=$no7;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailPlus2N2[<?=$no7;?>][containing]' id='batch_number_<?= $no7;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailPlus2N2[<?=$no7;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>	
									<!-- <input type='text' name='DetailPlus2N2[<?=$no7;?>][batch_number]' id='batch_number_<?= $no7;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailPlus2N2[<?=$no7;?>][actual_type]' id='actual_type_<?= $no7;?>' class='form-control input-sm numberOnly getResin2N2 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailPlus2N2[<?=$no7;?>][material_terpakai]' id='material_terpakai_<?= $no7;?>' class='form-control input-sm numberOnly aRight terpakai2N2' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
						$no8 = 0;
						foreach($restDetailAdd2N2 AS $val => $valx){
							$no8++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no8;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailAdd2N2[<?=$no8;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailAdd2N2[<?=$no8;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailAdd2N2[<?=$no8;?>][containing]' id='batch_number_<?= $no8;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailAdd2N2[<?=$no8;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailAdd2N2[<?=$no8;?>][batch_number]' id='batch_number_<?= $no8;?>'  class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailAdd2N2[<?=$no8;?>][actual_type]' id='actual_type_<?= $no8;?>' class='form-control input-sm numberOnly getResin2N2 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailAdd2N2[<?=$no8;?>][material_terpakai]' id='material_terpakai_<?= $no8;?>' class='form-control input-sm numberOnly aRight terpakai2N2' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
					}
					if(!empty($restDetail2)){
					?>
					<tr class='title'>
						<td class="text-left" colspan='8'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
					</tr>
					<?php
					}
					$no6 = 0;
					foreach($restDetailResin2 AS $val => $valx){
						$no6++;
						$get_resin = $this->db->query("SELECT material_terpakai FROM tmp_production_real_detail WHERE id_detail='".$valx['id_detail']."' AND id_production_detail='".$idProducktion."' LIMIT 1")->result();
						$real_resin = (!empty($get_resin))?$get_resin[0]->material_terpakai:'';
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
						?>
						<tr id='tr_<?= $no6;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailResin2[<?=$no6;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailResin2[<?=$no6;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='hidden' name='DetailResin2[<?=$no6;?>][actual_type]' id='actual_type_<?= $no6;?>' class='form-control input-sm' autocomplete='off' value='-'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
							<td class="text-left" colspan='3'>
								<select name='DetailResin2[<?=$no6;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailResin2[<?=$no6;?>][batch_number]' id='batch_number_<?= $no6;?>' class='form-control input-sm' autocomplete='off' value='-'> -->
							</td>
							<td class="text-right"><input type='text' name='DetailResin2[<?=$no6;?>][material_terpakai]' id='material_terpakai_<?= $no6;?>' class='form-control input-sm numberOnly3 resin2' autocomplete='off' value='<?=$real_resin;?>' value='0'></td>
						</tr>
						<?php
					}
					$no7 = 0;
					foreach($restDetailPlus2 AS $val => $valx){
						$no7++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
						?>
						<tr id='tr_<?= $no7;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailPlus2[<?=$no7;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailPlus2[<?=$no7;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='hidden' name='DetailPlus2[<?=$no7;?>][containing]' id='batch_number_<?= $no7;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
							<td class="text-left">
								<select name='DetailPlus2[<?=$no7;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailPlus2[<?=$no7;?>][batch_number]' id='batch_number_<?= $no7;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
							</td>
							<td class="text-right" colspan='2'><input type='text' name='DetailPlus2[<?=$no7;?>][actual_type]' id='actual_type_<?= $no7;?>' class='form-control input-sm numberOnly getResin2 aCenter' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailPlus2[<?=$no7;?>][material_terpakai]' id='material_terpakai_<?= $no7;?>' class='form-control input-sm numberOnly aRight terpakai2' autocomplete='off' readonly value='0'></td>
						</tr>
						<?php
					}
					$no8 = 0;
					foreach($restDetailAdd2 AS $val => $valx){
						$no8++;
						$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
						?>
						<tr id='tr_<?= $no8;?>'>
							<td colspan='2' class="text-left">
								<?= $valx['nm_category'];?><br>
								<input type='text' name='DetailAdd2[<?=$no8;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailAdd2[<?=$no8;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='hidden' name='DetailAdd2[<?=$no8;?>][containing]' id='batch_number_<?= $no8;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
							<td class="text-left">
								<select name='DetailAdd2[<?=$no8;?>][batch_number]' class='form-control chosen_select'>
									<option value="MTL-1903000">NONE MATERIAL</option>
									<?php
									foreach($list_material AS $valMat => $valxMat){
										$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
										echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
									}
									?>
								</select>
								<!-- <input type='text' name='DetailAdd2[<?=$no8;?>][batch_number]' id='batch_number_<?= $no8;?>'  class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
							</td>
							<td class="text-right" colspan='2'><input type='text' name='DetailAdd2[<?=$no8;?>][actual_type]' id='actual_type_<?= $no8;?>' class='form-control input-sm numberOnly getResin2 aCenter' autocomplete='off'></td>
							<td class="text-right"><input type='text' name='DetailAdd2[<?=$no8;?>][material_terpakai]' id='material_terpakai_<?= $no8;?>' class='form-control input-sm numberOnly aRight terpakai2' autocomplete='off' readonly value='0'></td>
						</tr>
						<?php
					}
					
					if(!empty($restDetailResin3)){
						?>
						<tr class='title'>
							<td class="text-left" colspan='8'><b><?= $restDetail3[0]['detail_name']; ?></b></td>
						</tr>
						<?php
						$no10 = 0;
						foreach($restDetailResin3 AS $val => $valx){
							$no10++;
							$get_resin = $this->db->query("SELECT material_terpakai FROM tmp_production_real_detail WHERE id_detail='".$valx['id_detail']."' AND id_production_detail='".$idProducktion."' LIMIT 1")->result();
							$real_resin = (!empty($get_resin))?$get_resin[0]->material_terpakai:'';
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no10;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailResin3[<?=$no10;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailResin3[<?=$no10;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailResin3[<?=$no10;?>][actual_type]' id='actual_type_<?= $no10;?>' class='form-control input-sm' autocomplete='off' value='-'>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left" colspan='2'>
									<select name='DetailResin3[<?=$no10;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailResin3[<?=$no10;?>][batch_number]' id='batch_number_<?= $no10;?>' class='form-control input-sm' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right"><input type='text' name='DetailResin3[<?=$no10;?>][material_terpakai]' id='material_terpakai_<?= $no10;?>' class='form-control input-sm numberOnly3 resin3' autocomplete='off' value='<?=$real_resin;?>'></td>
							</tr>
							<?php
						}
						$no11 = 0;
						foreach($restDetailPlus3 AS $val => $valx){
							$no11++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no11;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailPlus3[<?=$no11;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailPlus3[<?=$no11;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailPlus3[<?=$no11;?>][containing]' id='batch_number_<?= $no11;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailPlus3[<?=$no11;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailPlus3[<?=$no11;?>][batch_number]' id='batch_number_<?= $no11;?>'  class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailPlus3[<?=$no11;?>][actual_type]' id='actual_type_<?= $no11;?>' class='form-control input-sm numberOnly getResin3 aCenter' autocomplete='off value='0''></td>
								<td class="text-right"><input type='text' name='DetailPlus3[<?=$no11;?>][material_terpakai]' id='material_terpakai_<?= $no11;?>' class='form-control input-sm numberOnly aRight terpakai3' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
						$no12 = 0;
						foreach($restDetailAdd3 AS $val => $valx){
							$no12++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no12;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailAdd3[<?=$no12;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailAdd3[<?=$no12;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailAdd3[<?=$no12;?>][containing]' id='batch_number_<?= $no12;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailAdd3[<?=$no12;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailAdd3[<?=$no12;?>][batch_number]' id='batch_number_<?= $no12;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailAdd3[<?=$no12;?>][actual_type]' id='actual_type_<?= $no12;?>' class='form-control input-sm numberOnly getResin3 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailAdd3[<?=$no12;?>][material_terpakai]' id='material_terpakai_<?= $no12;?>' class='form-control input-sm numberOnly aRight terpakai3' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
					}

					if(!empty($restDetailResin4)){
						?>
						<tr class='title'>
							<td class="text-left" colspan='12'><b><?= $restDetailPlus4[0]['detail_name']; ?></b></td>
						</tr>
						<?php
						$no15 = 0;
						foreach($restDetailResin4 AS $val => $valx){
							$no15++;
							$get_resin = $this->db->query("SELECT material_terpakai FROM tmp_production_real_detail_plus WHERE id_detail='".$valx['id_detail']."' AND id_production_detail='".$idProducktion."' LIMIT 1")->result();
							$real_resin = (!empty($get_resin))?$get_resin[0]->material_terpakai:'';
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no15;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailPlus4[<?=$no15;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailPlus4[<?=$no15;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailPlus4[<?=$no15;?>][actual_type]' id='actual_type_<?= $no15;?>' class='form-control input-sm' autocomplete='off' value='-'>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left" colspan='3'>
									<select name='DetailPlus4[<?=$no15;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailPlus4[<?=$no15;?>][batch_number]' id='batch_number_<?= $no15;?>' class='form-control input-sm' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right"><input type='text' name='DetailPlus4[<?=$no15;?>][material_terpakai]' id='material_terpakai_<?= $no15;?>' class='form-control input-sm numberOnly3 resin4' autocomplete='off' value='<?=$real_resin;?>'></td>
							</tr>
							<?php
						}
						$no13 = 0;
						foreach($restDetailPlus4 AS $val => $valx){
							$no13++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no13;?>'>
								<td colspan='2'class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailPlus4[<?=$no13;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailPlus4[<?=$no13;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailPlus4[<?=$no13;?>][containing]' id='batch_number_<?= $no13;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailPlus4[<?=$no13;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailPlus4[<?=$no13;?>][batch_number]' id='batch_number_<?= $no13;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailPlus4[<?=$no13;?>][actual_type]' id='actual_type_<?= $no13;?>' class='form-control input-sm numberOnly getResin4 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailPlus4[<?=$no13;?>][material_terpakai]' id='material_terpakai_<?= $no13;?>' class='form-control input-sm numberOnly aRight terpakai4' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
						$no14 = 0;
						foreach($restDetailAdd4 AS $val => $valx){
							$no14++;
							$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
							?>
							<tr id='tr_<?= $no14;?>'>
								<td colspan='2' class="text-left">
									<?= $valx['nm_category'];?><br>
									<input type='text' name='DetailAdd4[<?=$no14;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailAdd4[<?=$no14;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
									<input type='hidden' name='DetailAdd4[<?=$no14;?>][containing]' id='batch_number_<?= $no14;?>' value='<?= floatval($valx['containing']);?>' class='form-control input-sm aCenter dataCount' autocomplete='off' readonly>
								</td>
								<td class="text-left"><?= $valx['nm_material'];?></td>
								<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
								<td class="text-left">
									<select name='DetailAdd4[<?=$no14;?>][batch_number]' class='form-control chosen_select'>
										<option value="MTL-1903000">NONE MATERIAL</option>
										<?php
										foreach($list_material AS $valMat => $valxMat){
											$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
											echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
										}
										?>
									</select>
									<!-- <input type='text' name='DetailAdd4[<?=$no14;?>][batch_number]' id='batch_number_<?= $no14;?>' class='form-control input-sm aCenter' autocomplete='off' value='-'> -->
								</td>
								<td class="text-right" colspan='2'><input type='text' name='DetailAdd4[<?=$no14;?>][actual_type]' id='actual_type_<?= $no14;?>' class='form-control input-sm numberOnly getResin4 aCenter' autocomplete='off'></td>
								<td class="text-right"><input type='text' name='DetailAdd4[<?=$no14;?>][material_terpakai]' id='material_terpakai_<?= $no14;?>' class='form-control input-sm numberOnly aRight terpakai4' autocomplete='off' readonly value='0'></td>
							</tr>
							<?php
						}
					}
					?>
					<tr>
						<td colspan='7'></td>
						<td><button type='button' style='width:100%' id='updateRealMat3New' class='btn btn-md btn-success'>Update</button></td>
					</tr>	
				</table>
			</div>
	</div>
	<style>
		.aRight, .numberOnly3{
			text-align: right !important;
		}		
		.aCenter{
			text-align: center !important;
		}
		.mid{
			vertical-align: middle !important;
		}
		.title{
			background-color: #d3d3d3 !important;
		}
	</style>
	<script>
		swal.close();
		$(".THide").hide();
		$(".numberOnly").autoNumeric('init', {mDec: '2', aPad: false});
		$(".numberOnly3").autoNumeric('init', {mDec: '3', aPad: false});
		$('.chosen_select').chosen({
			width : '100%'
		});
		
		$(document).on('keyup', '.getResin1', function(){
			var resin1 	= parseFloat($('.resin1').val().split(",").join(""));
							if(isNaN(resin1)){ var resin1 = 0;}
			if(resin1 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin terpakai masih kosong ...',
				  type	: "warning"
				});
				$(this).parent().parent().find("td:nth-child(6) input").val("");
				$(this).val("");
				return false;
			}
			var perse	= parseFloat($(this).val().split(",").join("")) / 100;
							if(isNaN(perse)){ var perse = 0;}
			var containing = $(this).parent().parent().find("td:nth-child(1) .dataCount").val();
			if(containing == 0){
				var containing = 1;
			}
			// console.log(containing);
			var terpakai = perse * resin1 * containing;
			$(this).parent().parent().find("td:nth-child(6) input").val(terpakai.toFixed(3));
		});
		
		$(document).on('keyup', '.resin1', function(){
			var resin1 	= parseFloat($('.resin1').val().split(",").join(""));
							if(isNaN(resin1)){ var resin1 = 0;}
			if(resin1 == 0){
				$('.getResin1').val("");
				$('.terpakai1').val("");
			}
		});
		
		$(document).on('keyup', '.getResin2', function(){
			var resin2 	= parseFloat($('.resin2').val().split(",").join(""));
						  if(isNaN(resin2)){ var resin2 = 0;}
			if(resin2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin terpakai masih kosong ...',
				  type	: "warning"
				});
				$(this).parent().parent().find("td:nth-child(6) input").val("");
				$(this).val("");
				return false;
			}
			var perse	= parseFloat($(this).val().split(",").join("")) / 100;
							if(isNaN(perse)){ var perse = 0;}
			var containing = $(this).parent().parent().find("td:nth-child(1) .dataCount").val();
			var terpakai = perse * resin2 * containing;
			$(this).parent().parent().find("td:nth-child(6) input").val(terpakai.toFixed(3));
		});
		
		$(document).on('keyup', '.resin2', function(){
			var resin2 	= parseFloat($('.resin2').val().split(",").join(""));
							if(isNaN(resin2)){ var resin2 = 0;}
			if(resin2 == 0){
				$('.getResin2').val("");
				$('.terpakai2').val("");
			}
		});
		
		$(document).on('keyup', '.getResin2N1', function(){
			var resin2 	= parseFloat($('.resin2N1').val().split(",").join(""));
						  if(isNaN(resin2)){ var resin2 = 0;}
			if(resin2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin terpakai masih kosong ...',
				  type	: "warning"
				});
				$(this).parent().parent().find("td:nth-child(6) input").val("");
				$(this).val("");
				return false;
			}
			var perse	= parseFloat($(this).val().split(",").join("")) / 100;
							if(isNaN(perse)){ var perse = 0;}
			var containing = $(this).parent().parent().find("td:nth-child(1) .dataCount").val();
			var terpakai = perse * resin2 * containing;
			$(this).parent().parent().find("td:nth-child(6) input").val(terpakai.toFixed(3));
		});
		
		$(document).on('keyup', '.resin2N1', function(){
			var resin2 	= parseFloat($('.resin2N1').val().split(",").join(""));
							if(isNaN(resin2)){ var resin2 = 0;}
			if(resin2 == 0){
				$('.getResin2N1').val("");
				$('.terpakai2N1').val("");
			}
		});
		
		$(document).on('keyup', '.getResin2N2', function(){
			var resin2 	= parseFloat($('.resin2N2').val().split(",").join(""));
						  if(isNaN(resin2)){ var resin2 = 0;}
			if(resin2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin terpakai masih kosong ...',
				  type	: "warning"
				});
				$(this).parent().parent().find("td:nth-child(6) input").val("");
				$(this).val("");
				return false;
			}
			var perse	= parseFloat($(this).val().split(",").join("")) / 100;
							if(isNaN(perse)){ var perse = 0;}
			var containing = $(this).parent().parent().find("td:nth-child(1) .dataCount").val();
			var terpakai = perse * resin2 * containing;
			$(this).parent().parent().find("td:nth-child(6) input").val(terpakai.toFixed(3));
		});
		
		$(document).on('keyup', '.resin2N2', function(){
			var resin2 	= parseFloat($('.resin2N2').val().split(",").join(""));
							if(isNaN(resin2)){ var resin2 = 0;}
			if(resin2 == 0){
				$('.getResin2N2').val("");
				$('.terpakai2N2').val("");
			}
		});
		
		$(document).on('keyup', '.getResin3', function(){
			var resin3 	= parseFloat($('.resin3').val().split(",").join(""));
						  if(isNaN(resin3)){ var resin3 = 0;}
			if(resin3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin terpakai masih kosong ...',
				  type	: "warning"
				});
				$(this).parent().parent().find("td:nth-child(6) input").val("");
				$(this).val("");
				return false;
			}
			var perse	= parseFloat($(this).val().split(",").join("")) / 100;
							if(isNaN(perse)){ var perse = 0;}
			var containing = $(this).parent().parent().find("td:nth-child(1) .dataCount").val();
			var terpakai = perse * resin3 * containing;
			$(this).parent().parent().find("td:nth-child(6) input").val(terpakai.toFixed(3));
		});
		
		$(document).on('keyup', '.resin3', function(){
			var resin3 	= parseFloat($('.resin3').val().split(",").join(""));
							if(isNaN(resin3)){ var resin3 = 0;}
			if(resin3 == 0){
				$('.getResin3').val("");
				$('.terpakai3').val("");
			}
		});
		
		$(document).on('keyup', '.getResin4', function(){
			var resin4 	= parseFloat($('.resin4').val().split(",").join(""));
			if(isNaN(resin4)){ var resin4 = 0;}
			if(resin4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin terpakai masih kosong ...',
				  type	: "warning"
				});
				$(this).parent().parent().find("td:nth-child(6) input").val("");
				$(this).val("");
				return false;
			}
			var perse	= parseFloat($(this).val().split(",").join("")) / 100;
							if(isNaN(perse)){ var perse = 0;}
			var containing = $(this).parent().parent().find("td:nth-child(1) .dataCount").val();
			var terpakai = perse * resin4 * containing;
			$(this).parent().parent().find("td:nth-child(6) input").val(terpakai.toFixed(3));
		});
		
		$(document).on('keyup', '.resin4', function(){
			var resin4 	= parseFloat($('.resin4').val().split(",").join(""));
			if(isNaN(resin4)){ var resin4 = 0;}
			if(resin4 == 0){
				$('.getResin4').val("");
				$('.terpakai4').val("");
			}
		});
		
		
	</script>