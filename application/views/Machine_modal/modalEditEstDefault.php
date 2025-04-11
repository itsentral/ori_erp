<?php

$id = $this->uri->segment(3);
$help_url = $this->uri->segment(4);

$sqlDefault	= "SELECT * FROM bq_component_default WHERE id_milik='".$id."' ";
$getDefault	= $this->db->query($sqlDefault)->result_array();
// echo $sqlDefault;
// exit;
?>
<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><?= $getDefault[0]['standart_code']; ?> (DN<?= $getDefault[0]['diameter']; ?>)</th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Item</th>
					<th class="text-left" width='66%'>Standar Value</th>
				<tr>
					<td class="text-left vMid">Standart</td>
					<td class="text-left"><?= $getDefault[0]['standart_code']; ?></td>
				</tr>
				<tr>
					<td class="text-left vMid">Product</td>
					<td class="text-left"><?= strtoupper($getDefault[0]['product_parent']); ?></td>
				</tr>
				<tr>
					<td class="text-left vMid">Diameter</td>
					<td class="text-left"><?= floatval($getDefault[0]['diameter']); ?> mm</td>
				</tr>
				<tr>
					<td class="text-left vMid">Diameter 2</td>
					<td class="text-left"><?= floatval($getDefault[0]['diameter2']); ?> mm</td>
				</tr>
				<tr>
					<td class="text-left vMid">Toleransi Max (%)</td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id','class'=>'form-control input-sm inSpL numberOnly'), $getDefault[0]['id']);
							echo form_input(array('type'=>'hidden','id'=>'id_milik','name'=>'id_milik','class'=>'form-control input-sm inSpL numberOnly'), $getDefault[0]['id_milik']);
							echo form_input(array('type'=>'hidden','id'=>'product_parent','name'=>'product_parent','class'=>'form-control input-sm inSpL numberOnly'), $getDefault[0]['product_parent']);
							echo form_input(array('type'=>'text','id'=>'max','name'=>'max','class'=>'form-control input-sm inSpL numberOnly'), floatval($getDefault[0]['max']) * 100);
							
							echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq','class'=>'form-control input-sm inSpL'), $getDefault[0]['id_bq']);
							echo form_input(array('type'=>'hidden','id'=>'help_url','name'=>'help_url','class'=>'form-control input-sm inSpL'), $help_url);
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Toleransi Min (%)</td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'min','name'=>'min','class'=>'form-control input-sm inSpL numberOnly'), floatval($getDefault[0]['min']) * 100);	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Layer Plastic Film <span class='text-red'>*</span></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'plastic_film','name'=>'plastic_film','class'=>'form-control input-sm inSpL numberOnly'), floatval($getDefault[0]['plastic_film']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Waste (%) <span class='text-red'>*</span></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm inSpL numberOnly'), floatval($getDefault[0]['waste']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Overlap <span class='text-red'>*</span></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'overlap','name'=>'overlap','class'=>'form-control input-sm inSpL numberOnly'), floatval($getDefault[0]['overlap']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>LINER THICKNESS</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left vMid">Veil</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_a','name'=>'lin_resin_veil_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['lin_resin_veil_a']));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_b','name'=>'lin_resin_veil_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['lin_resin_veil_b']));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_veil','name'=>'lin_resin_veil','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_resin_veil']));	
						?>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_veil','name'=>'lin_faktor_veil','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_faktor_veil']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add Veil</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_add_a','name'=>'lin_resin_veil_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['lin_resin_veil_add_a']));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_add_b','name'=>'lin_resin_veil_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['lin_resin_veil_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_veil_add','name'=>'lin_resin_veil_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_resin_veil_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_veil_add','name'=>'lin_faktor_veil_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_faktor_veil_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_a','name'=>'lin_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['lin_resin_csm_a']));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_b','name'=>'lin_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['lin_resin_csm_b']));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_csm','name'=>'lin_resin_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_resin_csm']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_csm','name'=>'lin_faktor_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_faktor_csm']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_add_a','name'=>'lin_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['lin_resin_csm_add_a']));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_add_b','name'=>'lin_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['lin_resin_csm_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_csm_add','name'=>'lin_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_resin_csm_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_csm_add','name'=>'lin_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_faktor_csm_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin','name'=>'lin_resin','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['lin_resin']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>STRUCTURE THICKNESS</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_a','name'=>'str_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_resin_csm_a']));	
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_b','name'=>'str_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_resin_csm_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_csm','name'=>'str_resin_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_csm']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_csm','name'=>'str_faktor_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_csm']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_add_a','name'=>'str_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_resin_csm_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_add_b','name'=>'str_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_resin_csm_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_csm_add','name'=>'str_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_csm_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_csm_add','name'=>'str_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_csm_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_a','name'=>'str_resin_wr_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_resin_wr_a']));	
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_b','name'=>'str_resin_wr_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_resin_wr_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_wr','name'=>'str_resin_wr','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_wr']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_wr','name'=>'str_faktor_wr','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_wr']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_add_a','name'=>'str_resin_wr_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_resin_wr_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_add_b','name'=>'str_resin_wr_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_resin_wr_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_wr_add','name'=>'str_resin_wr_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_wr_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_wr_add','name'=>'str_faktor_wr_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_wr_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'>Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_a','name'=>'str_resin_rv_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_resin_rv_a']));	
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_b','name'=>'str_resin_rv_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_resin_rv_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_rv','name'=>'str_resin_rv','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_rv']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv','name'=>'str_faktor_rv','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_rv']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_jb','name'=>'str_faktor_rv_jb','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_rv_jb']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_bw','name'=>'str_faktor_rv_bw','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_rv_bw']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'> Add Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_add_a','name'=>'str_resin_rv_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_resin_rv_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_add_b','name'=>'str_resin_rv_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_resin_rv_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_rv_add','name'=>'str_resin_rv_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_rv_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add','name'=>'str_faktor_rv_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_rv_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add_jb','name'=>'str_faktor_rv_add_jb','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_rv_add_jb']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add_bw','name'=>'str_faktor_rv_add_bw','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_faktor_rv_add_bw']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin','name'=>'str_resin','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_thickness','name'=>'str_resin_thickness','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_resin_thickness']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<?php if($getDefault[0]['product_parent'] == 'flange mould' OR $getDefault[0]['product_parent'] == 'flange slongsong'){ ?>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>STRUCTURE NECK 1</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_a','name'=>'str_n1_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n1_resin_csm_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_b','name'=>'str_n1_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n1_resin_csm_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_csm','name'=>'str_n1_resin_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_csm']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_csm','name'=>'str_n1_faktor_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_csm']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_add_a','name'=>'str_n1_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n1_resin_csm_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_add_b','name'=>'str_n1_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n1_resin_csm_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_csm_add','name'=>'str_n1_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_csm_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_csm_add','name'=>'str_n1_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_csm_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_a','name'=>'str_n1_resin_wr_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n1_resin_wr_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_b','name'=>'str_n1_resin_wr_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n1_resin_wr_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_wr','name'=>'str_n1_resin_wr','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_wr']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_wr','name'=>'str_n1_faktor_wr','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_wr']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_add_a','name'=>'str_n1_resin_wr_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n1_resin_wr_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_add_b','name'=>'str_n1_resin_wr_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n1_resin_wr_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_wr_add','name'=>'str_n1_resin_wr_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_wr_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_wr_add','name'=>'str_n1_faktor_wr_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_wr_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'>Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_a','name'=>'str_n1_resin_rv_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n1_resin_rv_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_b','name'=>'str_n1_resin_rv_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n1_resin_rv_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_rv','name'=>'str_n1_resin_rv','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_rv']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv','name'=>'str_n1_faktor_rv','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_rv']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_jb','name'=>'str_n1_faktor_rv_jb','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_rv_jb']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_bw','name'=>'str_n1_faktor_rv_bw','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_rv_bw']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'> Add Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_add_a','name'=>'str_n1_resin_rv_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n1_resin_rv_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_add_b','name'=>'str_n1_resin_rv_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n1_resin_rv_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_rv_add','name'=>'str_n1_resin_rv_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_rv_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_add','name'=>'str_n1_faktor_rv_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_rv_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_add_jb','name'=>'str_n1_faktor_rv_add_jb','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_rv_add_jb']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_add_bw','name'=>'str_n1_faktor_rv_add_bw','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_faktor_rv_add_bw']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin','name'=>'str_n1_resin','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_thickness','name'=>'str_n1_resin_thickness','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n1_resin_thickness']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>STRUCTURE NECK 2</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_a','name'=>'str_n2_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n2_resin_csm_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_b','name'=>'str_n2_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n2_resin_csm_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_csm','name'=>'str_n2_resin_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_resin_csm']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_csm','name'=>'str_n2_faktor_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_faktor_csm']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_add_a','name'=>'str_n2_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n2_resin_csm_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_add_b','name'=>'str_n2_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n2_resin_csm_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_csm_add','name'=>'str_n2_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_resin_csm_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_csm_add','name'=>'str_n2_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_faktor_csm_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_a','name'=>'str_n2_resin_wr_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n2_resin_wr_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_b','name'=>'str_n2_resin_wr_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n2_resin_wr_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_wr','name'=>'str_n2_resin_wr','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_resin_wr']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_wr','name'=>'str_n2_faktor_wr','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_faktor_wr']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_add_a','name'=>'str_n2_resin_wr_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['str_n2_resin_wr_add_a']));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_add_b','name'=>'str_n2_resin_wr_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['str_n2_resin_wr_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_wr_add','name'=>'str_n2_resin_wr_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_resin_wr_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_wr_add','name'=>'str_n2_faktor_wr_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_faktor_wr_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin','name'=>'str_n2_resin','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_resin']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_thickness','name'=>'str_n2_resin_thickness','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['str_n2_resin_thickness']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<?php } ?>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>EKSTERNAL THICKNESS</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left vMid">Veil</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_a','name'=>'eks_resin_veil_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['eks_resin_veil_a']));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_b','name'=>'eks_resin_veil_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['eks_resin_veil_b']));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_veil','name'=>'eks_resin_veil','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_resin_veil']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_veil','name'=>'eks_faktor_veil','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_faktor_veil']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add Veil</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_add_a','name'=>'eks_resin_veil_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['eks_resin_veil_add_a']));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_add_b','name'=>'eks_resin_veil_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['eks_resin_veil_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_veil_add','name'=>'eks_resin_veil_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_resin_veil_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_veil_add','name'=>'eks_faktor_veil_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_faktor_veil_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_a','name'=>'eks_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['eks_resin_csm_a']));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_b','name'=>'eks_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['eks_resin_csm_b']));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_csm','name'=>'eks_resin_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_resin_csm']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_csm','name'=>'eks_faktor_csm','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_faktor_csm']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_add_a','name'=>'eks_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3'), floatval($getDefault[0]['eks_resin_csm_add_a']));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_add_b','name'=>'eks_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'), floatval($getDefault[0]['eks_resin_csm_add_b']));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_csm_add','name'=>'eks_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_resin_csm_add']));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_csm_add','name'=>'eks_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_faktor_csm_add']));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin','name'=>'eks_resin','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['eks_resin']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>TOPCOAT</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'topcoat_resin','name'=>'topcoat_resin','class'=>'form-control input-sm inSp numberOnly'), floatval($getDefault[0]['topcoat_resin']));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'editDefaultSave')).' ';
		?>
	</div>
</div>

<style>
	.inSp{
		text-align: center;
		display: inline-block;
		width: 100px;
	}
	.inSp2{
		text-align: center;
		display: inline-block;
		width: 80px;
	}
	.inSpL{
		text-align: left;
	}
	.vMid{
		vertical-align: middle !important;
	}
</style>

<script>
	$(document).ready(function(){
		swal.close();
	});
	
	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
	
	
	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}
	//LINER
	$(document).on('keyup', '#lin_resin_veil_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#lin_resin_veil_b').val(b);
		$('#lin_resin_veil').val(c);
		
	});
	$(document).on('keyup', '#lin_resin_veil_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#lin_resin_veil_add_b').val(b);
		$('#lin_resin_veil_add').val(c);
		
	});
	$(document).on('keyup', '#lin_resin_csm_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#lin_resin_csm_b').val(b);
		$('#lin_resin_csm').val(c);
		
	});
	$(document).on('keyup', '#lin_resin_csm_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#lin_resin_csm_add_b').val(b);
		$('#lin_resin_csm_add').val(c);
		
	});
	
	//STRUCTURE
	$(document).on('keyup', '#str_resin_csm_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_resin_csm_b').val(b);
		$('#str_resin_csm').val(c);
		
	});
	$(document).on('keyup', '#str_resin_csm_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_resin_csm_add_b').val(b);
		$('#str_resin_csm_add').val(c);
		
	});

	$(document).on('keyup', '#str_resin_wr_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_resin_wr_b').val(b);
		$('#str_resin_wr').val(c);
		
	});
	$(document).on('keyup', '#str_resin_wr_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_resin_wr_add_b').val(b);
		$('#str_resin_wr_add').val(c);
		
	});
	
	$(document).on('keyup', '#str_resin_rv_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_resin_rv_b').val(b);
		$('#str_resin_rv').val(c);
		
	});
	$(document).on('keyup', '#str_resin_rv_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_resin_rv_add_b').val(b);
		$('#str_resin_rv_add').val(c);
		
	});
	
	//STRUCTURE
	$(document).on('keyup', '#str_n1_resin_csm_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n1_resin_csm_b').val(b);
		$('#str_n1_resin_csm').val(c);
		
	});
	$(document).on('keyup', '#str_n1_resin_csm_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n1_resin_csm_add_b').val(b);
		$('#str_n1_resin_csm_add').val(c);
		
	});

	$(document).on('keyup', '#str_n1_resin_wr_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n1_resin_wr_b').val(b);
		$('#str_n1_resin_wr').val(c);
		
	});
	$(document).on('keyup', '#str_n1_resin_wr_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n1_resin_wr_add_b').val(b);
		$('#str_n1_resin_wr_add').val(c);
		
	});
	
	$(document).on('keyup', '#str_n1_resin_rv_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n1_resin_rv_b').val(b);
		$('#str_n1_resin_rv').val(c);
		
	});
	$(document).on('keyup', '#str_n1_resin_rv_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n1_resin_rv_add_b').val(b);
		$('#str_n1_resin_rv_add').val(c);
		
	});
	
	//STRUCTURE
	$(document).on('keyup', '#str_n2_resin_csm_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n2_resin_csm_b').val(b);
		$('#str_n2_resin_csm').val(c);
		
	});
	$(document).on('keyup', '#str_n2_resin_csm_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n2_resin_csm_add_b').val(b);
		$('#str_n2_resin_csm_add').val(c);
		
	});

	$(document).on('keyup', '#str_n2_resin_wr_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n2_resin_wr_b').val(b);
		$('#str_n2_resin_wr').val(c);
		
	});
	$(document).on('keyup', '#str_n2_resin_wr_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n2_resin_wr_add_b').val(b);
		$('#str_n2_resin_wr_add').val(c);
		
	});
	
	$(document).on('keyup', '#str_n2_resin_rv_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n2_resin_rv_b').val(b);
		$('#str_n2_resin_rv').val(c);
		
	});
	$(document).on('keyup', '#str_n2_resin_rv_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#str_n2_resin_rv_add_b').val(b);
		$('#str_n2_resin_rv_add').val(c);
		
	});
	
	//EKTERNAL
	$(document).on('keyup', '#eks_resin_veil_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#eks_resin_veil_b').val(b);
		$('#eks_resin_veil').val(c);
		
	});
	$(document).on('keyup', '#eks_resin_veil_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#eks_resin_veil_add_b').val(b);
		$('#eks_resin_veil_add').val(c);
		
	});
	$(document).on('keyup', '#eks_resin_csm_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#eks_resin_csm_b').val(b);
		$('#eks_resin_csm').val(c);
		
	});
	$(document).on('keyup', '#eks_resin_csm_add_a', function(){
		var a = getNum($(this).val());
		var b = getNum(100 - a);
		var c = getNum(a/b);
		
		$('#eks_resin_csm_add_b').val(b);
		$('#eks_resin_csm_add').val(c);
		
	});
	
	$(document).on('click', '#editDefaultSave', function(){
		var max				= $('#max').val();
		var min				= $('#min').val();
		var plastic_film	= $('#plastic_film').val();
		var waste			= $('#waste').val();
		var overlap			= $('#overlap').val();
		
		if(plastic_film == '' || plastic_film == null || plastic_film == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Plastic Faktor is Empty, please input first ...',
			  type	: "warning"
			});
			$('#editDefaultSave').prop('disabled',false);
			return false;	
		}
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Waste is Empty, please input first ...',
			  type	: "warning"
			});
			$('#editDefaultSave').prop('disabled',false);
			return false;	
		}
		if(overlap == '' || overlap == null){
			swal({
			  title	: "Error Message!",
			  text	: 'Overlap is Empty, please input first ...',
			  type	: "warning"
			});
			$('#editDefaultSave').prop('disabled',false);
			return false;	
		}
		
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
					url			: base_url+'index.php/json_help/editDefaultEstProject',
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
								  timer	: 3000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
							$("#ModalView").modal();
							
							$("#head_title3").html("<b>DETAIL ESTIMATION</b>");
							$("#view3").load(base_url +'index.php/'+ active_controller+'/'+data.help_url+'/'+data.id_bq+'/'+data.id_milik);
							$("#ModalView3").modal();
							
							$("#ModalView4").modal('hide');
							
						}
						else{
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 3000,
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
						  timer				: 5000,
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