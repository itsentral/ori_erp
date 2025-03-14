<?php

$listCode	= "SELECT * FROM help_default_name ORDER BY nm_default";
$getDef		= $this->db->query($listCode)->result_array();

$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
		

?>
<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'>HEADER</th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Item</th>
					<th class="text-left" width='66%'>Standar Value</th>
				</tr>
				<tr>
					<td class="text-left vMid">Product <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<select id='komponen' name='komponen' class='chosen_select form-control input-sm inline-block' style='min-width:200px;'>
							<option value='0'>Select Component</option>
							<?php
								foreach($getKomp AS $val => $valx){
									echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>"; 
								}
							?>
						</select> 
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Standart <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<select name='standart_code' id='standart_code' class='chosen_select form-control input-sm inline-block'>
							<option value='0'>Select Default</option>
						<?php
							foreach($getDef AS $val => $valx){
								echo "<option value='".$valx['nm_default']."'>".strtoupper($valx['nm_default'])."</option>";
							}
						 ?>
						</select>
						<button type='button' id='addP' style='font-weight: bold; font-size: 12px; margin-top: 5px; color: #175477;'>Add Standart Default</button>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Diameter <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'diameter','name'=>'diameter','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Diameter /mm','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Diameter 2 <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Diameter /mm','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Toleransi Max (%)</td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'max','name'=>'max','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Maximal Tolerance','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Toleransi Min (%)</td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'min','name'=>'min','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Minimal Tolerance','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Layer Plastic Film <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'plastic_film','name'=>'plastic_film','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Faktor Plastic Film','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Waste (%) <span class='text-red'>*</span></b></td>  
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Waste','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Overlap <span class='text-red'>*</span></b></td>  
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'overlap','name'=>'overlap','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Overlap','autocomplete'=>'off'));	
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
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_a','name'=>'lin_resin_veil_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_b','name'=>'lin_resin_veil_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_veil','name'=>'lin_resin_veil','class'=>'form-control input-sm inSp2 numberOnly'));	
						?>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_veil','name'=>'lin_faktor_veil','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add Veil</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_add_a','name'=>'lin_resin_veil_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_veil_add_b','name'=>'lin_resin_veil_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_veil_add','name'=>'lin_resin_veil_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_veil_add','name'=>'lin_faktor_veil_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_a','name'=>'lin_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_b','name'=>'lin_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_csm','name'=>'lin_resin_csm','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_csm','name'=>'lin_faktor_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_add_a','name'=>'lin_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'lin_resin_csm_add_b','name'=>'lin_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'lin_resin_csm_add','name'=>'lin_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_faktor_csm_add','name'=>'lin_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'lin_resin','name'=>'lin_resin','class'=>'form-control input-sm inSp numberOnly'));	
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
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_a','name'=>'str_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_b','name'=>'str_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_csm','name'=>'str_resin_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_csm','name'=>'str_faktor_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_add_a','name'=>'str_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_resin_csm_add_b','name'=>'str_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_csm_add','name'=>'str_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_csm_add','name'=>'str_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_a','name'=>'str_resin_wr_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_b','name'=>'str_resin_wr_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_wr','name'=>'str_resin_wr','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_wr','name'=>'str_faktor_wr','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_add_a','name'=>'str_resin_wr_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_resin_wr_add_b','name'=>'str_resin_wr_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_wr_add','name'=>'str_resin_wr_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_wr_add','name'=>'str_faktor_wr_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'>Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_a','name'=>'str_resin_rv_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_b','name'=>'str_resin_rv_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_rv','name'=>'str_resin_rv','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv','name'=>'str_faktor_rv','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_jb','name'=>'str_faktor_rv_jb','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_bw','name'=>'str_faktor_rv_bw','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'> Add Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_add_a','name'=>'str_resin_rv_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_resin_rv_add_b','name'=>'str_resin_rv_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_resin_rv_add','name'=>'str_resin_rv_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add','name'=>'str_faktor_rv_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add_jb','name'=>'str_faktor_rv_add_jb','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add_bw','name'=>'str_faktor_rv_add_bw','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin','name'=>'str_resin','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_resin_thickness','name'=>'str_resin_thickness','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>NECK 1 <span style='font-size: 11px;'>(Boleh Kosong)</span></b></th>
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
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_a','name'=>'str_n1_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_b','name'=>'str_n1_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_csm','name'=>'str_n1_resin_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_csm','name'=>'str_n1_faktor_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_add_a','name'=>'str_n1_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_csm_add_b','name'=>'str_n1_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_csm_add','name'=>'str_n1_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_csm_add','name'=>'str_n1_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_a','name'=>'str_n1_resin_wr_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_b','name'=>'str_n1_resin_wr_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_wr','name'=>'str_n1_resin_wr','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_wr','name'=>'str_n1_faktor_wr','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_add_a','name'=>'str_n1_resin_wr_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_wr_add_b','name'=>'str_n1_resin_wr_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_wr_add','name'=>'str_n1_resin_wr_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_wr_add','name'=>'str_n1_faktor_wr_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'>Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_a','name'=>'str_n1_resin_rv_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_b','name'=>'str_n1_resin_rv_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_rv','name'=>'str_n1_resin_rv','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv','name'=>'str_n1_faktor_rv','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_jb','name'=>'str_n1_faktor_rv_jb','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_bw','name'=>'str_n1_faktor_rv_bw','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid" rowspan='3'> Add Rooving</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_add_a','name'=>'str_n1_resin_rv_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_rv_add_b','name'=>'str_n1_resin_rv_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n1_resin_rv_add','name'=>'str_n1_resin_rv_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_add','name'=>'str_n1_faktor_rv_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Benang</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_add_jb','name'=>'str_n1_faktor_rv_add_jb','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-center vMid">Bandwitch</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_faktor_rv_add_bw','name'=>'str_n1_faktor_rv_add_bw','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin','name'=>'str_n1_resin','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n1_resin_thickness','name'=>'str_n1_resin_thickness','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>NECK 2 <span style='font-size: 11px;'>(Boleh Kosong)</span></b></th>
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
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_a','name'=>'str_n2_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_b','name'=>'str_n2_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_csm','name'=>'str_n2_resin_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_csm','name'=>'str_n2_faktor_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_add_a','name'=>'str_n2_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_csm_add_b','name'=>'str_n2_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_csm_add','name'=>'str_n2_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_csm_add','name'=>'str_n2_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_a','name'=>'str_n2_resin_wr_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_b','name'=>'str_n2_resin_wr_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_wr','name'=>'str_n2_resin_wr','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_wr','name'=>'str_n2_faktor_wr','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add WR</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_add_a','name'=>'str_n2_resin_wr_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_wr_add_b','name'=>'str_n2_resin_wr_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'str_n2_resin_wr_add','name'=>'str_n2_resin_wr_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_faktor_wr_add','name'=>'str_n2_faktor_wr_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin','name'=>'str_n2_resin','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'str_n2_resin_thickness','name'=>'str_n2_resin_thickness','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
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
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_a','name'=>'eks_resin_veil_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_b','name'=>'eks_resin_veil_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_veil','name'=>'eks_resin_veil','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_veil','name'=>'eks_faktor_veil','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add Veil</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_add_a','name'=>'eks_resin_veil_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_veil_add_b','name'=>'eks_resin_veil_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_veil_add','name'=>'eks_resin_veil_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_veil_add','name'=>'eks_faktor_veil_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_a','name'=>'eks_resin_csm_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_b','name'=>'eks_resin_csm_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_csm','name'=>'eks_resin_csm','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_csm','name'=>'eks_faktor_csm','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Add CSM</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_add_a','name'=>'eks_resin_csm_add_a','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Resin', 'maxlength'=>'3','autocomplete'=>'off'));	
							echo form_input(array('type'=>'text','id'=>'eks_resin_csm_add_b','name'=>'eks_resin_csm_add_b','class'=>'form-control input-sm inSp2 numberOnly', 'readonly'=>'readonly', 'placeholder'=>'Glass'));	
							echo form_input(array('type'=>'hidden','id'=>'eks_resin_csm_add','name'=>'eks_resin_csm_add','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_faktor_csm_add','name'=>'eks_faktor_csm_add','class'=>'form-control input-sm inSp numberOnly','autocomplete'=>'off'));	
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center">
						<?php
							echo form_input(array('type'=>'text','id'=>'eks_resin','name'=>'eks_resin','class'=>'form-control input-sm inSp numberOnly'));	
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
							echo form_input(array('type'=>'text','id'=>'topcoat_resin','name'=>'topcoat_resin','class'=>'form-control input-sm inSp numberOnly'));	
						?>
					</td>
				</tr>
			</tbody> 
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'addDefaultSave')).' ';
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
	
</script>