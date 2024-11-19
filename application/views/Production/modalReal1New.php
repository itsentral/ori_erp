
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
	<div class="box-body"><br>
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
			   <label class='label-control'>Production Real Date</label>
			</div>
			<div class='col-sm-3'>
			   <input type="text" name="production_date" id="production_date" class="form-control input-sm datepicker" placeholder="Production Real Date" readonly>
			</div>
			<div class='col-sm-2'>
				<label class='label-control'>Warehouse Produksi</label>
			</div>
			<div class='col-sm-3'>
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
		<div class="form-group row">
			<div class='col-sm-2 '>
			   <label class='label-control'>Finish Production Date</label>
			</div>
			<div class='col-sm-3'>
			   <input type="text" name="finish_production_date" id="finish_production_date" class="form-control input-sm datepicker" placeholder="Finish Production Date" readonly>
			</div>
			<div class='col-sm-7'></div>
		</div>
		<div class="form-group row">
			<div class='col-sm-2 '>
			   <label class='label-control'>Tanggal Terima SPK</label>
			</div>
			<div class='col-sm-3'>
			   <input type="text" name="terima_spk_date" id="terima_spk_date" class="form-control input-sm datepicker" placeholder="Tanggal Terima SPK" readonly>
			</div>
			<div class='col-sm-7'></div>
		</div>
	
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
			</thead>
			
			<?php
			if(!empty($restDetail1) OR !empty($restDetailResin1) OR !empty($restDetailPlus1)){
				echo "<tr class='title'>";
					echo "<td class='text-left' colspan='8'><b>LINER THICKNESS</b></td>";
				echo "</tr>";
				$no1 = 0;
				foreach($restDetail1 AS $val => $valx){
					$no1++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
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
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][batch_number]' id='batch_number_<?= $no1;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left">
							<select name='DetailUtama[<?=$no1;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailUtama[<?=$no1;?>][actual_type]' id='actual_type_<?= $no1;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][layer]' id='layer_<?= $no1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][material_terpakai]' id='material_terpakai_<?= $no1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			
				$no2 = 0;
				foreach($restDetailResin1 AS $val => $valx){
					$no2++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no2;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailResin[<?=$no2;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailResin[<?=$no2;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailResin[<?=$no2;?>][batch_number]' id='batch_number_<?= $no2;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailResin[<?=$no2;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailResin[<?=$no2;?>][actual_type]' id='actual_type_<?= $no2;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailResin[<?=$no2;?>][material_terpakai]' id='material_terpakai_<?= $no2;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
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
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailPlus[<?=$no3;?>][batch_number]' id='batch_number_<?= $no3;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailPlus[<?=$no3;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailPlus[<?=$no3;?>][actual_type]' id='actual_type_<?= $no3;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailPlus[<?=$no3;?>][material_terpakai]' id='material_terpakai_<?= $no3;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			}
			//NECK 1
			if(!empty($restDetail2N1)){
				echo "<tr class='title'>";
					echo "<td class='text-left' colspan='8'><b>".$restDetail2N1[0]['detail_name']."</b></td>";
				echo "</tr>";
				$no5N1 = 0;
				foreach($restDetail2N1 AS $val => $valx){
					$no5N1++;
					$benang = "";
					if($valx['id_category'] == 'TYP-0005'){
						$benang = "<br><br> Bn: ".floatval($valx['jumlah'])."<br><br> Bw: ".floatval($valx['bw']);
					}
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no5N1;?>'>
						<td class="text-left">
							<?= $valx['nm_category'];?>
							<input type='text' name='DetailUtama2N1[<?=$no5N1;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailUtama2N1[<?=$no5N1;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-center"><?= floatval($valx['layer']);?></td>
						<td class="text-left"><?= $valx['nm_material'].$benang;;?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailUtama2N1[<?=$no5N1;?>][batch_number]' id='batch_numberN1_<?= $no5N1;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left">
							<select name='DetailUtama2N1[<?=$no5N1;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>

							<!-- <input type='text' name='DetailUtama2N1[<?=$no5N1;?>][actual_type]' id='actual_typeN1_<?= $no5N1;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right">
							<input type='text' name='DetailUtama2N1[<?=$no5N1;?>][layer]' id='layerN1_<?= $no5N1;?>' class='form-control input-sm numberOnly' autocomplete='off'>
							<?php
								if($valx['id_category'] == 'TYP-0005'){
									?>
										<input type='text' style='margin-top: 5px;' placeholder='Benang' name='DetailUtama2N1[<?=$no5N1;?>][benang]' id='benangN1_<?= $no5N1;?>' class='form-control input-sm numberOnly' autocomplete='off'>
										<input type='text' style='margin-top: 5px;' placeholder='Bandwidch' name='DetailUtama2N1[<?=$no5N1;?>][bw]' id='bwN1_<?= $no5N1;?>' class='form-control input-sm numberOnly' autocomplete='off'>
									<?php
								}
							?>
						</td>
						<td class="text-right"><input type='text' name='DetailUtama2N1[<?=$no5N1;?>][material_terpakai]' id='material_terpakaiN1_<?= $no5N1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
				$no6N1 = 0;
				foreach($restDetailResin2N1 AS $val => $valx){
					$no6N1++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no6N1;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailResin2N1[<?=$no6N1;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailResin2N1[<?=$no6N1;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailResin2N1[<?=$no6N1;?>][batch_number]' id='batch_numberN1_<?= $no6N1;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailResin2N1[<?=$no6N1;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailResin2N1[<?=$no6N1;?>][actual_type]' id='actual_typeN1_<?= $no6N1;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailResin2N1[<?=$no6N1;?>][material_terpakai]' id='material_terpakaiN1_<?= $no6N1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
				$no7N1 = 0;
				foreach($restDetailPlus2N1 AS $val => $valx){
					$no7N1++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no7N1;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailPlus2N1[<?=$no7N1;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailPlus2N1[<?=$no7N1;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailPlus2N1[<?=$no7N1;?>][batch_number]' id='batch_numberN1_<?= $no7N1;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailPlus2N1[<?=$no7N1;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailPlus2N1[<?=$no7N1;?>][actual_type]' id='actual_typeN1_<?= $no7N1;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailPlus2N1[<?=$no7N1;?>][material_terpakai]' id='material_terpakaiN1_<?= $no7N1;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			}
			//END NECK 1
			
			//NECK 2
			if(!empty($restDetail2N2)){
				echo "<tr class='title'>";
					echo "<td colspan='8'><b>".$restDetail2N2[0]['detail_name']."</b></td>";
				echo "</tr>";
				$no5N2 = 0;
				foreach($restDetail2N2 AS $val => $valx){
					$no5N2++;
					$benang = "";
					if($valx['id_category'] == 'TYP-0005'){
						$benang = "<br><br> Bn: ".floatval($valx['jumlah'])."<br><br> Bw: ".floatval($valx['bw']);
					}
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no5N2;?>'>
						<td class="text-left">
							<?= $valx['nm_category'];?>
							<input type='text' name='DetailUtama2N2[<?=$no5N2;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailUtama2N2[<?=$no5N2;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-center"><?= floatval($valx['layer']).$benang;?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailUtama2N2[<?=$no5N2;?>][batch_number]' id='batch_numberN2_<?= $no5N2;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left">
							<select name='DetailUtama2N2[<?=$no5N2;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailUtama2N2[<?=$no5N2;?>][actual_type]' id='actual_typeN2_<?= $no5N2;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right">
							<input type='text' name='DetailUtama2N2[<?=$no5N2;?>][layer]' id='layerN2_<?= $no5N2;?>' class='form-control input-sm numberOnly' autocomplete='off'>
							<?php
								if($valx['id_category'] == 'TYP-0005'){
									?>
										<input type='text' style='margin-top: 5px;' placeholder='Benang' name='DetailUtama2N2[<?=$no5N2;?>][benang]' id='benangN2_<?= $no5N2;?>' class='form-control input-sm numberOnly' autocomplete='off'>
										<input type='text' style='margin-top: 5px;' placeholder='Bandwidch' name='DetailUtama2N2[<?=$no5N2;?>][bw]' id='bwN2_<?= $no5N2;?>' class='form-control input-sm numberOnly' autocomplete='off'>
									<?php
								}
							?>
						</td>
						<td class="text-right"><input type='text' name='DetailUtama2N2[<?=$no5N2;?>][material_terpakai]' id='material_terpakaiN2_<?= $no5N2;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
				$no6N2 = 0;
				foreach($restDetailResin2N2 AS $val => $valx){
					$no6N2++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr<?= $no6N2;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailResin2N2[<?=$no6N2;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailResin2N2[<?=$no6N2;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailResin2N2[<?=$no6N2;?>][batch_number]' id='batch_numberN2_<?= $no6N2;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailResin2N2[<?=$no6N2;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailResin2N2[<?=$no6N2;?>][actual_type]' id='actual_typeN2_<?= $no6N2;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailResin2N2[<?=$no6N2;?>][material_terpakai]' id='material_terpakaiN2_<?= $no6N2;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
				$no7N2 = 0;
				foreach($restDetailPlus2N2 AS $val => $valx){
					$no7N2++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no7N2;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailPlus2N2[<?=$no7N2;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailPlus2N2[<?=$no7N2;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailPlus2N2[<?=$no7N2;?>][batch_number]' id='batch_numberN2_<?= $no7N2;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailPlus2N2[<?=$no7N2;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailPlus2N2[<?=$no7N2;?>][actual_type]' id='actual_typeN2_<?= $no7N2;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailPlus2N2[<?=$no7N2;?>][material_terpakai]' id='material_terpakaiN2_<?= $no7N2;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			}
			//END NECK 2
			if(!empty($restDetail2)){
				?>
				<tr class='title'>
					<td colspan='8'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
				</tr>
				<?php
				$no5 = 0;
				foreach($restDetail2 AS $val => $valx){
					$no5++;
					$benang = "";
					if($valx['id_category'] == 'TYP-0005'){
						$benang = "<br><br> Bn: ".floatval($valx['jumlah'])."<br><br> Bw: ".floatval($valx['bw']);
					}
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no5;?>'>
						<td class="text-left">
							<?= $valx['nm_category'];?>
							<input type='text' name='DetailUtama2[<?=$no5;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailUtama2[<?=$no5;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-center"><?= floatval($valx['layer'])?></td>
						<td class="text-left"><?= $valx['nm_material'].$benang;;?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailUtama2[<?=$no5;?>][batch_number]' id='batch_number_<?= $no5;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left">
							<select name='DetailUtama2[<?=$no5;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailUtama2[<?=$no5;?>][actual_type]' id='actual_type_<?= $no5;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
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
			
				$no6 = 0;
				foreach($restDetailResin2 AS $val => $valx){
					$no6++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no6;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailResin2[<?=$no6;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailResin2[<?=$no6;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailResin2[<?=$no6;?>][batch_number]' id='batch_number_<?= $no6;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailResin2[<?=$no6;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailResin2[<?=$no6;?>][actual_type]' id='actual_type_<?= $no6;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailResin2[<?=$no6;?>][material_terpakai]' id='material_terpakai_<?= $no6;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
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
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailPlus2[<?=$no7;?>][batch_number]' id='batch_number_<?= $no7;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailPlus2[<?=$no7;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailPlus2[<?=$no7;?>][actual_type]' id='actual_type_<?= $no7;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailPlus2[<?=$no7;?>][material_terpakai]' id='material_terpakai_<?= $no7;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			}
			if(!empty($restDetail3)){
				echo "<tr class='title'>";
					echo "<td class='text-left' colspan='8'><b>".$restDetail3[0]['detail_name']."</b></td>";
				echo "</tr>";
				$no9 = 0;
				foreach($restDetail3 AS $val => $valx){
					$no9++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no9;?>'>
						<td class="text-left">
							<?= $valx['nm_category'];?>
							<input type='text' name='DetailUtama3[<?=$no9;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailUtama3[<?=$no9;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-right"><?= $valx['layer'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][batch_number]' id='batch_number_<?= $no9;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left">
							<select name='DetailUtama3[<?=$no9;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailUtama3[<?=$no9;?>][actual_type]' id='actual_type_<?= $no9;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][layer]' id='layer_<?= $no9;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
						<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][material_terpakai]' id='material_terpakai_<?= $no9;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
				$no10 = 0;
				foreach($restDetailResin3 AS $val => $valx){
					$no10++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no10;?>'>
						<td colspan='2' class="text-left">
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailResin3[<?=$no10;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailResin3[<?=$no10;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailResin3[<?=$no10;?>][batch_number]' id='batch_number_<?= $no10;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailResin3[<?=$no10;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailResin3[<?=$no10;?>][actual_type]' id='actual_type_<?= $no10;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailResin3[<?=$no10;?>][material_terpakai]' id='material_terpakai_<?= $no10;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
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
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailPlus3[<?=$no11;?>][batch_number]' id='batch_number_<?= $no11;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailPlus3[<?=$no11;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailPlus3[<?=$no11;?>][actual_type]' id='actual_type_<?= $no11;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailPlus3[<?=$no11;?>][material_terpakai]' id='material_terpakai_<?= $no11;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			}

			if(!empty($restDetailPlus4)){
			?>
				<tr class='title'>
					<td colspan='12'><b><?= $restDetailPlus4[0]['detail_name']; ?></b></td>
				</tr>
				<?php
				$no13 = 0;
				foreach($restDetailPlus4 AS $val => $valx){
					$no13++;
					$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category'],'delete'=>'N'))->result_array();
					?>
					<tr id='tr_<?= $no13;?>'>
						<td class="text-left" colspan='2'>
							<?= $valx['nm_category'];?><br>
							<input type='text' name='DetailPlus4[<?=$no13;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
							<input type='text' name='DetailPlus4[<?=$no13;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
						</td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right"><?= number_format($valx['last_cost'] * $qty_total, 3);?> Kg</td>
						<td class="text-right"><input type='text' name='DetailPlus4[<?=$no13;?>][batch_number]' id='batch_number_<?= $no13;?>' class='form-control input-sm' autocomplete='off'></td>
						<td class="text-left" colspan='2'>
							<select name='DetailPlus4[<?=$no13;?>][actual_type]' class='form-control chosen_select'>
								<option value="MTL-1903000">NONE MATERIAL</option>
								<?php
								foreach($list_material AS $valMat => $valxMat){
									$sel = ($valxMat['id_material'] == $valx['id_material'])?'selected':'';
									echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
								}
								?>
							</select>
							<!-- <input type='text' name='DetailPlus4[<?=$no13;?>][actual_type]' id='actual_type_<?= $no13;?>' class='form-control input-sm' autocomplete='off'> -->
						</td>
						<td class="text-right"><input type='text' name='DetailPlus4[<?=$no13;?>][material_terpakai]' id='material_terpakai_<?= $no13;?>' class='form-control input-sm numberOnly' autocomplete='off'></td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td><b>Thickness Est</b></td>
				<td colspan='3'><b><?= $restHeader[0]['est'];?></b></td>
				<td><b>Thickness Real</b></td>
				<td colspan='2'><input type='text' name='est_real' id='est_real' class='form-control input-sm numberOnly' autocomplete='off'></td>
				<td><button type='button' id='updateRealMat1New' style='width:100%' class='btn btn-md btn-success'>Update</button></td>
			</tr>	
		</table>
	</div>
</div>
<style type="text/css">
	.datepicker {
		cursor:pointer;
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
	$(".numberOnly").autoNumeric('init', {mDec: '3', aPad: false});
	$('.chosen_select').chosen({
		width : '100%'
	});
	$('.datepicker').datepicker({
		dateFormat : 'yy-mm-dd',
		maxDate: 0,
		changeMonth: true,
		changeYear: true,
	});
</script>