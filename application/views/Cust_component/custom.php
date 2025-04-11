<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">

		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>

		<div class="box-body">
			<section id="header">
				<div class='headerTitleGroup'>GROUP COMPONENT</div>

				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>Customer</b></label>
					<div class='col-sm-4'>
						<select name='cust' id='cust' class='form-control input-sm'>
							<option value='0'>Select Customer</option>
						<?php
							foreach($standard AS $val => $valx){
								echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
							}
						 ?>
						</select>
					</div>
					<label class='label-control col-sm-2'><b>Series <span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='series' id='series' class='form-control input-sm'>
							<option value='0'>Select Series</option>
						<?php
							foreach($series AS $val => $valx){
								echo "<option value='".$valx['seriesx']."'>".strtoupper($valx['seriesx'])."</option>";
							}
						 ?>
						</select>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>Component Name <span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'parent_product','name'=>'parent_product','class'=>'form-control input-sm Hide','readonly'=>'readonly'));
						?>
						<select name='component_list' id='component_list' class='form-control input-sm'>
							<option value='0'>Select Component</option>
						<?php
							foreach($product AS $val => $valx){
								echo "<option value='".$valx['product_parent']."'>".strtoupper(strtolower($valx['product_parent']))."</option>";
							}
						 ?>
						</select>
					</div>
					<!--

					-->
					<label class='label-control col-sm-2'><b>Keterangan</b></label>
					<div class='col-sm-4'>
						<?php
							echo form_input(array('type'=>'text','id'=>'ket_plus','name'=>'ket_plus','class'=>'form-control input-sm','placeholder'=>'Isi dengan singkat / kode', 'maxlength'=>'10'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'></label>
					<div class='col-sm-4'>
						<div id='tamp' style='font-weight: bold; background-color: #f1f1f1; padding: 1px 0px 0px 8px;border-radius: 0px 10px 10px 0px;'></div>
					</div>

				</div>
			</section>
			<!-- //// -->
			<section id="detail">
				<div class='headerTitle'>DETAILED ESTIMATION</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b><span id='id_1'>Inner Dim  </span><span class='text-red'>*</span> <span id='id_2'>| Outter Dim </span><span class='text-red'>*</span></b></label>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Inner Diameter'));
						?>
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Outer Diameter'));
						?>
					</div>

					<label class='label-control col-sm-2'><b><span id='id_3'>Thickness  </span><span class='text-red'>*</span></b></label>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('type'=>'text','id'=>'design','name'=>'design','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness'));
						?>
					</div>
				</div>

				<!-- ====================================================================================================== -->
				<!-- ============================================LINER THICKNESS=========================================== -->
				<!-- ====================================================================================================== -->
				<div class='headerTitle'>LINER THIKNESS / CB</div>
				<div class='form-group row'>
					<input type='text' name='detail_name' id='detail_name' class='HideCost' value='LINER THIKNESS / CB'>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b><div id='mirror'>MIRROR GLASS<span class='text-red'>*</span></div><div id='plactic'>PLASTIC FILM<span class='text-red'>*</span></div></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail[1][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
							<option value=''>Select An Mirror Glass</option>
						<?php
							foreach($ListPlastic AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0008'>
						<input type='text' name='ListDetail[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0008'>
						<input type='text' name='ListDetail[1][last_full]' class='HideCost' id='hasil_plastic' value='0'>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-2'></div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_plastic','name'=>'ListDetail[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail[2][id_material]' id='mid_mtl_veil' class='form-control input-sm'>
							<option value=''>Select An Veil</option>
						<?php
							foreach($ListVeil AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0003'>
						<input type='text' name='ListDetail[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0003'>
						<input type='text' name='ListDetail[2][thickness]' class='HideCost' id='thickness_veil' value='0'>
						<input type='text' name='ListDetail[2][last_full]' class='HideCost' id='hasil_veil' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_veil','name'=>'ListDetail[2][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_veil','name'=>'ListDetail[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail[4][id_material]' id='mid_mtl_veil_add' class='form-control input-sm'>
							<option value=''>Select An Veil Add</option>
						<?php
							foreach($ListVeil AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0003'>
						<input type='text' name='ListDetail[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0003'>
						<input type='text' name='ListDetail[4][thickness]' class='HideCost' id='thickness_veil_add' value='0'>
						<input type='text' name='ListDetail[4][last_full]' class='HideCost' id='hasil_veil_add' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_veil_add','name'=>'ListDetail[4][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_veil_add','name'=>'ListDetail[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail[6][id_material]' id='mid_mtl_matcsm' class='form-control input-sm'>
							<option value=''>Select An MAT/CSM</option>
						<?php
							foreach($ListMatCsm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail[6][id_ori]' class='HideCost' id='id_ori' value='TYP-0004'>
						<input type='text' name='ListDetail[6][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0004'>
						<input type='text' name='ListDetail[6][thickness]' class='HideCost' id='thickness_matcsm' value='0'>
						<input type='text' name='ListDetail[6][last_full]' class='HideCost' id='hasil_matcsm' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_matcsm','name'=>'ListDetail[6][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_matcsm','name'=>'ListDetail[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail[8][id_material]' id='mid_mtl_csm_add' class='form-control input-sm'>
						<option value=''>Select An MAT/CSM</option>
						<?php
							foreach($ListMatCsm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail[8][id_ori]' class='HideCost' id='id_ori' value='TYP-0004'>
						<input type='text' name='ListDetail[8][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0004'>
						<input type='text' name='ListDetail[8][thickness]' class='HideCost' id='thickness_csm_add' value='0'>
						<input type='text' name='ListDetail[8][last_full]' class='HideCost' id='hasil_csm_add' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_csm_add','name'=>'ListDetail[8][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_csm_add','name'=>'ListDetail[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail[10][id_material]' id='mid_mtl_resin_tot' class='form-control input-sm'>
						<option value=''>Select An Resin</option>
						<?php
							foreach($ListResin AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail[10][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
						<input type='text' name='ListDetail[10][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
						<input type='text' name='ListDetail[10][last_full]' class='HideCost' id='hasil_resin_tot' value='0'>
					</div>
					<div class='col-sm-4'></div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_resin_tot','name'=>'ListDetail[10][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus[0][id_material]' id='mid_mtl_katalis' class='form-control input-sm'>
							<option value=''>Select An Katalis</option>
						<?php
							foreach($ListMatKatalis AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus[0][id_ori]' class='HideCost' id='id_ori' value='TYP-0002'>
						<input type='text' name='ListDetailPlus[0][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0002'>
						<input type='text' name='ListDetailPlus[0][last_full]' class='HideCost' id='hasil_katalis' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus[0][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus[0][perse]' class='form-control input-sm numberOnly' id='persen_katalis' value='2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_katalis','name'=>'ListDetailPlus[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus[1][id_material]' id='mid_mtl_sm' class='form-control input-sm'>
							<option value=''>Select An SM</option>
						<?php
							foreach($ListMatSm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0024'>
						<input type='text' name='ListDetailPlus[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0024'>
						<input type='text' name='ListDetailPlus[1][last_full]' class='HideCost' id='hasil_sm' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus[1][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_sm' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus[1][perse]' class='form-control input-sm numberOnly' id='persen_sm' value='2.5'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_sm','name'=>'ListDetailPlus[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus[2][id_material]' id='mid_mtl_cobalt' class='form-control input-sm'>
							<option value=''>Select An Cobalt</option>
						<?php
							foreach($ListMatCobalt AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
						<input type='text' name='ListDetailPlus[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
						<input type='text' name='ListDetailPlus[2][last_full]' class='HideCost' id='hasil_coblat' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus[2][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat' value='0.6'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus[2][perse]' class='form-control input-sm numberOnly' id='persen_coblat' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_cobalt','name'=>'ListDetailPlus[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus[3][id_material]' id='mid_mtl_dma' class='form-control input-sm'>
							<option value=''>Select An DMA</option>
						<?php
							foreach($ListMatDma AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
						<input type='text' name='ListDetailPlus[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
						<input type='text' name='ListDetailPlus[3][last_full]' class='HideCost' id='hasil_dma' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus[3][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma' value='0.4'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus[3][perse]' class='form-control input-sm numberOnly' id='persen_dma' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_dma','name'=>'ListDetailPlus[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus[4][id_material]' id='mid_mtl_hydro' class='form-control input-sm'>
							<option value=''>Select An Hydroquinone</option>
						<?php
							foreach($ListMatHydo AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0026'>
						<input type='text' name='ListDetailPlus[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0026'>
						<input type='text' name='ListDetailPlus[4][last_full]' class='HideCost' id='hasil_hydroquinone' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus[4][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_hydroquinone' value='0.1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus[4][perse]' class='form-control input-sm numberOnly' id='persen_hydroquinone' value='0.05'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_hidro','name'=>'ListDetailPlus[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus[5][id_material]' id='mid_mtl_methanol' class='form-control input-sm'>
							<option value=''>Select An Methanol</option>
						<?php
							foreach($ListMatMethanol AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0026'>
						<input type='text' name='ListDetailPlus[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0026'>
						<input type='text' name='ListDetailPlus[5][last_full]' class='HideCost' id='hasil_methanol' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus[5][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_methanol' value='0.9'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus[5][perse]' class='form-control input-sm numberOnly' id='persen_methanol' value='0.05'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_methanol','name'=>'ListDetailPlus[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>
				</div>
				<!-- Add Material-->
				<button type='button' name='add_liner' id='add_liner' class='btn btn-success btn-sm' style='width:150px; margin-left: 10px;'>Add Material</button>
				<input type='hidden' name='numberMax_liner' id='numberMax_liner' value='0'>

				<div class="box-body" style="">
					<table id="my-grid_liner" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody id='detail_body_liner'>
						</tbody>
					</table>
				</div>
				
				<!-- ====================================================================================================== -->
				<!-- ============================================END LINER THICKNESS======================================= -->
				<!-- ====================================================================================================== -->

				<!-- ====================================================================================================== -->
				<!-- ============================================STRUKTUR THICKNESS======================================== -->
				<!-- ====================================================================================================== -->
				<div class='headerTitle'>STRUKTUR THIKNESS</div>
				<div class='form-group row'>
					<div class='col-sm-10'></div>
					<div class='col-sm-2'>
						<input type='text' name='detail_name2' id='detail_name2' class='HideCost' value='STRUKTUR THICKNESS'>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>

					<div class='col-sm-5'>
						<select name='ListDetail2[0][id_material]' id='mid_mtl_matcsm2' class='form-control input-sm'>
							<option value=''>Select An MAT/CSM</option>
						<?php
							foreach($ListMatCsm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[0][id_ori]' class='HideCost' id='id_ori' value='TYP-0004'>
						<input type='text' name='ListDetail2[0][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0004'>
						<input type='text' name='ListDetail2[0][thickness]' class='HideCost' id='thickness_matcsm2' value='0'>
						<input type='text' name='ListDetail2[0][last_full]' class='HideCost' id='hasil_matcsm2' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_matcsm2','name'=>'ListDetail2[0][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_matcsm2','name'=>'ListDetail2[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail2[2][id_material]' id='mid_mtl_csm_add2' class='form-control input-sm'>
						<option value=''>Select An MAT/CSM</option>
						<?php
							foreach($ListMatCsm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0004'>
						<input type='text' name='ListDetail2[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0004'>
						<input type='text' name='ListDetail2[2][thickness]' class='HideCost' id='thickness_csm_add2' value='0'>
						<input type='text' name='ListDetail2[2][last_full]' class='HideCost' id='hasil_csm_add2' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_csm_add2','name'=>'ListDetail2[2][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_csm_add2','name'=>'ListDetail2[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>WR<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail2[4][id_material]' id='mid_mtl_wr2' class='form-control input-sm'>
							<option value=''>Select An WR</option>
						<?php
							foreach($ListMatWR AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0006'>
						<input type='text' name='ListDetail2[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0006'>
						<input type='text' name='ListDetail2[4][thickness]' class='HideCost' id='thickness_wr2' value='0'>
						<input type='text' name='ListDetail2[4][last_full]' class='HideCost' id='hasil_wr2' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_wr2','name'=>'ListDetail2[4][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_wr2','name'=>'ListDetail2[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL WR<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail2[6][id_material]' id='mid_mtl_wr_add2' class='form-control input-sm'>
							<option value=''>Select An WR Add</option>
						<?php
							foreach($ListMatWR AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[6][id_ori]' class='HideCost' id='id_ori' value='TYP-0006'>
						<input type='text' name='ListDetail2[6][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0006'>
						<input type='text' name='ListDetail2[6][thickness]' class='HideCost' id='thickness_wr_add2' value='0'>
						<input type='text' name='ListDetail2[6][last_full]' class='HideCost' id='hasil_wr_add2' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_wr_add2','name'=>'ListDetail2[6][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_wr_add2','name'=>'ListDetail2[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ROOVING<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail2[8][id_material]' id='mid_mtl_rv2' class='form-control input-sm'>
							<option value=''>Select An Rooving</option>
						<?php
							foreach($ListMatRooving AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[8][id_ori]' class='HideCost' id='id_ori' value='TYP-0005'>
						<input type='text' name='ListDetail2[8][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0005'>
						<input type='text' name='ListDetail2[8][thickness]' class='HideCost' id='thickness_rv2' value='0'>
						<input type='text' name='ListDetail2[8][last_full]' class='HideCost' id='hasil_rv2' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_rv2','name'=>'ListDetail2[8][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_rv2','name'=>'ListDetail2[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL ROOVING<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail2[10][id_material]' id='mid_mtl_rv_add2' class='form-control input-sm'>
							<option value=''>Select An Rooving Add</option>
						<?php
							foreach($ListMatRooving AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[10][id_ori]' class='HideCost' id='id_ori' value='TYP-0005'>
						<input type='text' name='ListDetail2[10][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0005'>
						<input type='text' name='ListDetail2[10][thickness]' class='HideCost' id='thickness_rv_add2' value='0'>
						<input type='text' name='ListDetail2[10][last_full]' class='HideCost' id='hasil_rv_add2' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_rv_add2','name'=>'ListDetail2[10][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_rv_add2','name'=>'ListDetail2[10][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail2[12][id_material]' id='mid_mtl_resin_tot2' class='form-control input-sm'>
						<option value=''>Select An Resin</option>
						<?php
							foreach($ListResin AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail2[12][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
						<input type='text' name='ListDetail2[12][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
						<input type='text' name='ListDetail2[12][last_full]' class='HideCost' id='hasil_resin_tot2' value='0'>
					</div>
					<div class='col-sm-4'></div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_resin_tot2','name'=>'ListDetail2[12][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus2[0][id_material]' id='mid_mtl_katalis2' class='form-control input-sm'>
							<option value=''>Select An Katalis</option>
						<?php
							foreach($ListMatKatalis AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus2[0][id_ori]' class='HideCost' id='id_ori' value='TYP-0002'>
						<input type='text' name='ListDetailPlus2[0][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0002'>
						<input type='text' name='ListDetailPlus2[0][last_full]' class='HideCost' id='hasil_katalis2' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus2[0][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis2' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus2[0][perse]' class='form-control input-sm numberOnly' id='persen_katalis2' value='2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_katalis2','name'=>'ListDetailPlus2[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus2[1][id_material]' id='mid_mtl_sm2' class='form-control input-sm'>
							<option value=''>Select An SM</option>
						<?php
							foreach($ListMatSm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus2[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0024'>
						<input type='text' name='ListDetailPlus2[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0024'>
						<input type='text' name='ListDetailPlus2[1][last_full]' class='HideCost' id='hasil_sm2' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus2[1][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_sm2' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus2[1][perse]' class='form-control input-sm numberOnly' id='persen_sm2' value='2.5'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_sm2','name'=>'ListDetailPlus2[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus2[2][id_material]' id='mid_mtl_cobalt2' class='form-control input-sm'>
							<option value=''>Select An Cobalt</option>
						<?php
							foreach($ListMatCobalt AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus2[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
						<input type='text' name='ListDetailPlus2[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
						<input type='text' name='ListDetailPlus2[2][last_full]' class='HideCost' id='hasil_coblat2' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus2[2][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat2' value='0.6'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus2[2][perse]' class='form-control input-sm numberOnly' id='persen_coblat2' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_cobalt2','name'=>'ListDetailPlus2[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus2[3][id_material]' id='mid_mtl_dma2' class='form-control input-sm'>
							<option value=''>Select An DMA</option>
						<?php
							foreach($ListMatDma AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus2[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
						<input type='text' name='ListDetailPlus2[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
						<input type='text' name='ListDetailPlus2[3][last_full]' class='HideCost' id='hasil_dma2' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus2[3][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma2' value='0.4'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus2[3][perse]' class='form-control input-sm numberOnly' id='persen_dma2' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_dma2','name'=>'ListDetailPlus2[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus2[4][id_material]' id='mid_mtl_hydro2' class='form-control input-sm'>
							<option value=''>Select An Hydroquinone</option>
						<?php
							foreach($ListMatHydo AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus2[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0026'>
						<input type='text' name='ListDetailPlus2[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0026'>
						<input type='text' name='ListDetailPlus2[4][last_full]' class='HideCost' id='hasil_hydroquinone2' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus2[4][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_hydroquinone2' value='0.1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus2[4][perse]' class='form-control input-sm numberOnly' id='persen_hydroquinone2' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_hidro2','name'=>'ListDetailPlus2[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus2[5][id_material]' id='mid_mtl_methanol2' class='form-control input-sm'>
							<option value=''>Select An Methanol</option>
						<?php
							foreach($ListMatMethanol AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus2[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0026'>
						<input type='text' name='ListDetailPlus2[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0026'>
						<input type='text' name='ListDetailPlus2[5][last_full]' class='HideCost' id='hasil_methanol2' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus2[5][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_methanol2' value='0.9'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus2[5][perse]' class='form-control input-sm numberOnly' id='persen_methanol2' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_methanol2','name'=>'ListDetailPlus2[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<!-- Add Material-->
				<button type='button' name='add_strukture' id='add_strukture' class='btn btn-success btn-sm' style='width:150px; margin-left: 10px;'>Add Material</button>
				<input type='hidden' name='numberMax_strukture' id='numberMax_strukture' value='0'>

				<div class="box-body" style="">
					<table id="my-grid_strukture" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody id='detail_body_strukture'>
						</tbody>
					</table>
				</div>
				<!-- ====================================================================================================== -->
				<!-- ============================================END STRUKTUR THICKNESS==================================== -->
				<!-- ====================================================================================================== -->

				<!-- ====================================================================================================== -->
				<!-- ==========================================EXTERNAL LAYER THICKNESS==================================== -->
				<!-- ====================================================================================================== -->
				<div class='headerTitle'>EXTERNAL LAYER THICKNESS</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>EXTERNAL LAYER ? <span class='text-red'>*</span></b></label>
					<div class='col-sm-2'>
						<select name='external_layer' id='external_layer' class='form-control input-sm'>
							<option value='Y'>Yes</option>
							<option value='N'>No</option>
						</select>
					</div>
					<div class='col-sm-6'> </div>
					<div class='col-sm-2'>
						<input type='text' name='detail_name3' id='detail_name3' class='HideCost' value='EXTERNAL LAYER THICKNESS'>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail3[0][id_material]' id='mid_mtl_veil3' class='form-control input-sm'>
							<option value=''>Select An Veil</option>
						<?php
							foreach($ListVeil AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail3[0][id_ori]' class='HideCost' id='id_ori' value='TYP-0003'>
						<input type='text' name='ListDetail3[0][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0003'>
						<input type='text' name='ListDetail3[0][thickness]' class='HideCost' id='thickness_veil3' value='0'>
						<input type='text' name='ListDetail3[0][last_full]' class='HideCost' id='hasil_veil3' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_veil3','name'=>'ListDetail3[0][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_veil3','name'=>'ListDetail3[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail3[2][id_material]' id='mid_mtl_veil_add3' class='form-control input-sm'>
							<option value=''>Select An Veil Add</option>
						<?php
							foreach($ListVeil AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail3[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0003'>
						<input type='text' name='ListDetail3[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0003'>
						<input type='text' name='ListDetail3[2][thickness]' class='HideCost' id='thickness_veil_add3' value='0'>
						<input type='text' name='ListDetail3[2][last_full]' class='HideCost' id='hasil_veil_add3' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_veil_add3','name'=>'ListDetail3[2][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_veil_add3','name'=>'ListDetail3[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail3[4][id_material]' id='mid_mtl_matcsm3' class='form-control input-sm'>
							<option value=''>Select An MAT/CSM</option>
						<?php
							foreach($ListMatCsm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail3[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0004'>
						<input type='text' name='ListDetail3[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0004'>
						<input type='text' name='ListDetail3[4][thickness]' class='HideCost' id='thickness_matcsm3' value='0'>
						<input type='text' name='ListDetail3[4][last_full]' class='HideCost' id='hasil_matcsm3' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_matcsm3','name'=>'ListDetail3[4][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_matcsm3','name'=>'ListDetail3[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail3[6][id_material]' id='mid_mtl_csm_add3' class='form-control input-sm'>
						<option value=''>Select An MAT/CSM</option>
						<?php
							foreach($ListMatCsm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail3[6][id_ori]' class='HideCost' id='id_ori' value='TYP-0004'>
						<input type='text' name='ListDetail3[6][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0004'>
						<input type='text' name='ListDetail3[6][thickness]' class='HideCost' id='thickness_csm_add3' value='0'>
						<input type='text' name='ListDetail3[6][last_full]' class='HideCost' id='hasil_csm_add3' value='0'>
					</div>
					<label class='col-sm-1'>Layer</label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'layer_csm_add3','name'=>'ListDetail3[6][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off'));
						?>
					</div>
					<label class='col-sm-2'></label>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_csm_add3','name'=>'ListDetail3[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetail3[8][id_material]' id='mid_mtl_resin_tot3' class='form-control input-sm'>
						<option value=''>Select An Resin</option>
						<?php
							foreach($ListResin AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetail3[8][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
						<input type='text' name='ListDetail3[8][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
						<input type='text' name='ListDetail3[8][last_full]' class='HideCost' id='hasil_resin_tot3' value='0'>
					</div>
					<div class='col-sm-4'></div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_resin_tot3','name'=>'ListDetail3[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus3[0][id_material]' id='mid_mtl_katalis3' class='form-control input-sm'>
							<option value=''>Select An Katalis</option>
						<?php
							foreach($ListMatKatalis AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus3[0][id_ori]' class='HideCost' id='id_ori' value='TYP-0002'>
						<input type='text' name='ListDetailPlus3[0][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0002'>
						<input type='text' name='ListDetailPlus3[0][last_full]' class='HideCost' id='hasil_katalis3' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus3[0][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis3' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus3[0][perse]' class='form-control input-sm numberOnly' id='persen_katalis3' value='2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_katalis3','name'=>'ListDetailPlus3[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus3[1][id_material]' id='mid_mtl_sm3' class='form-control input-sm'>
							<option value=''>Select An SM</option>
						<?php
							foreach($ListMatSm AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus3[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0024'>
						<input type='text' name='ListDetailPlus3[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0024'>
						<input type='text' name='ListDetailPlus3[1][last_full]' class='HideCost' id='hasil_sm3' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus3[1][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_sm3' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus3[1][perse]' class='form-control input-sm numberOnly' id='persen_sm3' value='2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_sm3','name'=>'ListDetailPlus3[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus3[2][id_material]' id='mid_mtl_cobalt3' class='form-control input-sm'>
							<option value=''>Select An Cobalt</option>
						<?php
							foreach($ListMatCobalt AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus3[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
						<input type='text' name='ListDetailPlus3[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
						<input type='text' name='ListDetailPlus3[2][last_full]' class='HideCost' id='hasil_coblat3' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus3[2][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat3' value='0.6'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus3[2][perse]' class='form-control input-sm numberOnly' id='persen_coblat3' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_cobalt3','name'=>'ListDetailPlus3[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus3[3][id_material]' id='mid_mtl_dma3' class='form-control input-sm'>
							<option value=''>Select An DMA</option>
						<?php
							foreach($ListMatDma AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus3[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
						<input type='text' name='ListDetailPlus3[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
						<input type='text' name='ListDetailPlus3[3][last_full]' class='HideCost' id='hasil_dma3' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus3[3][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma3' value='0.4'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus3[3][perse]' class='form-control input-sm numberOnly' id='persen_dma3' value='0.2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_dma3','name'=>'ListDetailPlus3[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus3[4][id_material]' id='mid_mtl_hydro3' class='form-control input-sm'>
							<option value=''>Select An Hydroquinone</option>
						<?php
							foreach($ListMatHydo AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus3[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0026'>
						<input type='text' name='ListDetailPlus3[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0026'>
						<input type='text' name='ListDetailPlus3[4][last_full]' class='HideCost' id='hasil_hydroquinone3' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus3[4][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_hydroquinone3' value='0.1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus3[4][perse]' class='form-control input-sm numberOnly' id='persen_hydroquinone3' value='0.05'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_hidro3','name'=>'ListDetailPlus3[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus3[5][id_material]' id='mid_mtl_methanol3' class='form-control input-sm'>
							<option value=''>Select An Methanol</option>
						<?php
							foreach($ListMatMethanol AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus3[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0026'>
						<input type='text' name='ListDetailPlus3[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0026'>
						<input type='text' name='ListDetailPlus3[5][last_full]' class='HideCost' id='hasil_methanol3' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus3[5][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_methanol3' value='0.9'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus3[5][perse]' class='form-control input-sm numberOnly' id='persen_methanol3' value='0.05'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_methanol3','name'=>'ListDetailPlus3[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<!-- Add Material-->
				<button type='button' name='add_external' id='add_external' class='btn btn-success btn-sm' style='width:150px; margin-left: 10px;'>Add Material</button>
				<input type='hidden' name='numberMax_external' id='numberMax_external' value='0'>

				<div class="box-body" style="">
					<table id="my-grid_external" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody id='detail_body_external'>
						</tbody>
					</table>
				</div>
				
				<!-- ====================================================================================================== -->
				<!-- ========================================END EXTERNAL LAYER THICKNESS================================== -->
				<!-- ====================================================================================================== -->

				<!-- ====================================================================================================== -->
				<!-- ==========================================TOPCOAT==================================== -->
				<!-- ====================================================================================================== -->
				<div class='headerTitle'>TOPCOAT</div>
					<input type='text' name='detail_name4' id='detail_name4'  class='HideCost' value='TOPCOAT'>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-5'>
						<select name='ListDetailPlus4[0][id_material]' id='mid_mtl_resin41' class='form-control input-sm'>
						<option value=''>Select An Resin</option>
						<?php
							foreach($ListResin AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[0][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
						<input type='text' name='ListDetailPlus4[0][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
						<input type='text' name='ListDetailPlus4[0][last_full]' class='HideCost' id='hasil_resin41' value='0'>
						<input type='text' name='ListDetailPlus4[0][perse]' class='HideCost' id='resin41'>
					</div>
					<div class='col-sm-4'></div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_resin41','name'=>'ListDetailPlus4[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[1][id_material]' id='mid_mtl_katalis4' class='form-control input-sm'>
							<option value=''>Select An Katalis</option>
						<?php
							foreach($ListMatKatalis AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0002'>
						<input type='text' name='ListDetailPlus4[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0002'>
						<input type='text' name='ListDetailPlus4[1][last_full]' class='HideCost' id='hasil_katalis4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[1][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis4' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[1][perse]' class='form-control input-sm numberOnly perseTC' id='persen_katalis4' value='2'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_katalis4','name'=>'ListDetailPlus4[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Color<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[2][id_material]' id='mid_mtl_color4' class='form-control input-sm'>
							<option value=''>Select An Color</option>
						<?php
							foreach($ListMatColor AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[2][id_ori]' class='HideCost' id='id_ori' value='TYP-0007'>
						<input type='text' name='ListDetailPlus4[2][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0007'>
						<input type='text' name='ListDetailPlus4[2][last_full]' class='HideCost' id='hasil_color4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[2][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_color4' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[2][perse]' class='form-control input-sm numberOnly perseTC' id='persen_color4' value='5'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_color4','name'=>'ListDetailPlus4[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Tinuvin<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[3][id_material]' id='mid_mtl_tin4' class='form-control input-sm'>
							<option value=''>Select An Tinuvin</option>
						<?php
							foreach($ListMatTinuvin AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0019'>
						<input type='text' name='ListDetailPlus4[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0019'>
						<input type='text' name='ListDetailPlus4[3][last_full]' class='HideCost' id='hasil_tin4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[3][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_tin4' value='0.1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[3][perse]' class='form-control input-sm numberOnly perseTC' id='persen_tin4' value='2.6'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_tin4','name'=>'ListDetailPlus4[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Chlroroform<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[4][id_material]' id='mid_mtl_chl4' class='form-control input-sm'>
							<option value=''>Select An Chlroroform</option>
						<?php
							foreach($ListMatChl AS $val => $valx){
								echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[4][id_ori]' class='HideCost' id='id_ori' value='TYP-0019'>
						<input type='text' name='ListDetailPlus4[4][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0019'>
						<input type='text' name='ListDetailPlus4[4][last_full]' class='HideCost' id='hasil_chl4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[4][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_chl4' value='0.9'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[4][perse]' class='form-control input-sm numberOnly perseTC' id='persen_chl4' value='2.6'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_chl4','name'=>'ListDetailPlus4[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[5][id_material]' id='mid_mtl_stery4' class='form-control input-sm'>
							<option value=''>Select An SM</option>
						<?php
							foreach($ListMatStery AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0024'>
						<input type='text' name='ListDetailPlus4[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0024'>
						<input type='text' name='ListDetailPlus4[5][last_full]' class='HideCost' id='hasil_stery4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[5][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_stery4' value='0.9'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[5][perse]' class='form-control input-sm numberOnly perseTC' id='persen_stery4' value='3'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_stery4','name'=>'ListDetailPlus4[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Solution Wax<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[6][id_material]' id='mid_mtl_wax4' class='form-control input-sm'>
							<option value=''>Select An Solution Wax</option>
						<?php
							foreach($ListMatWax AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[6][id_ori]' class='HideCost' id='id_ori' value='TYP-0008'>
						<input type='text' name='ListDetailPlus4[6][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0019'>
						<input type='text' name='ListDetailPlus4[6][last_full]' class='HideCost' id='hasil_wax4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[6][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_wax4' value='0.1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[6][perse]' class='form-control input-sm numberOnly perseTC' id='persen_wax4' value='3'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_wax4','name'=>'ListDetailPlus4[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Met. Chlorida<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='ListDetailPlus4[7][id_material]' id='mid_mtl_mch4' class='form-control input-sm'>
							<option value=''>Select An Methelene Chlorida</option>
						<?php
							foreach($ListMatMchl AS $val => $valx){
								echo "<option value='".$valx['id_material']."' >".strtoupper($valx['nm_material'])."</option>";
							}
							echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
						 ?>
						</select>
						<input type='text' name='ListDetailPlus4[7][id_ori]' class='HideCost' id='id_ori' value='TYP-0024'>
						<input type='text' name='ListDetailPlus4[7][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0024'>
						<input type='text' name='ListDetailPlus4[7][last_full]' class='HideCost' id='hasil_mch4' value='0'>
					</div>
					<label class='col-sm-1'>Comparison</label>
					<div class='col-sm-1'>              
						<input type='text' name='ListDetailPlus4[7][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_mch4' value='1'>	
					</div>
					<label class='col-sm-1'>Percent</label>
					<div class='col-sm-1'>
						<input type='text' name='ListDetailPlus4[7][perse]' class='form-control input-sm numberOnly perseTC' id='persen_mch4' value='1'>
					</div>
					<div class='col-sm-1'>
						<?php
							echo form_input(array('id'=>'last_mch4','name'=>'ListDetailPlus4[7][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly'),0);
						?>
					</div>

				</div>
				<!-- Add Material-->
				<button type='button' name='add_topcoat' id='add_topcoat' class='btn btn-success btn-sm' style='width:150px; margin-left: 10px;'>Add Material</button>
				<input type='hidden' name='numberMax_topcoat' id='numberMax_topcoat' value='0'>

				<div class="box-body" style="">
					<table id="my-grid_topcoat" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody id='detail_body_topcoat'>
						</tbody>
					</table>
				</div>
				<!-- ====================================================================================================== -->
				<!-- ===============================================END TOPCOAT============================================ -->
				<!-- ====================================================================================================== -->
				<br>
				<!-- END -->
				
				<div class='form-group row'>
					<div class='col-sm-12'>
						<button type='button' name='simpan-bro' id='simpan-bro' style='float:right; width:100px;' class='btn btn-primary'>Save</button>
					</div>
				</div>
				
			</section>
		</div>

		 <!-- modal -->
		<div class="modal fade" id="ModalView">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="head_title"></h4>
						</div>
						<div class="modal-body" id="view">
						</div>
						<div class="modal-footer">
						<!--<button type="button" class="btn btn-primary">Save</button>-->
						<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="ModalView2">
			<div class="modal-dialog"  style='width:30%; '>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="head_title2"></h4>
						</div>
						<div class="modal-body" id="view2">
						</div>
						<div class="modal-footer">
						<!--<button type="button" class="btn btn-primary">Save</button>-->
						<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
					</div> 
				</div>
			</div>
		</div>
		<!-- modal -->

</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Est_js/custom_est_manual.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Component/est/javascript/help_general.js'); ?>"></script>
<style type="text/css">
	.kghide{
		display:none !important;
	}
	label{
		    font-size: small !important;
	}
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
	#app_pipe_chosen{
		width: 100% !important;
	}
	#standard_pipe_chosen{
		width: 100% !important;
	}
	.headerTitle{
		text-align: center;
		background-color: #294267;
		height: 30px;
		font-weight: bold;
		padding-top: 5px;
		margin-bottom: 15px;
		margin-top: 30px;
		color: white;
	}

	.headerTitleGroup{
		text-align: center;
		background-color: #b97f29;
		height: 30px;
		font-weight: bold;
		padding-top: 5px;
		margin-bottom: 15px;
		margin-top: 30px;
		color: white;
	}

	.HasilKet{
		font-size: 20px;
		text-align: center;
		font-weight: bold;
	}
	.Acuhan{
		float: right;
		width: 180px;
		font-size: 20px;
		text-align: center;
		font-weight: bold;
		background-color: #ffffff;
	}
	#customer_chosen,
	#top_type_chosen,
	#top_typeList_chosen,
	#series_chosen,
	#criminal_barier_chosen,
	#vacum_rate_chosen,
	#aplikasi_product_chosen,
	#design_life_chosen,
	#top_app_chosen,
	#top_toleran_chosen,
	#mid_mtl_realese_chosen,
	#mid_mtl_plastic_chosen,
	#mid_mtl_veil_chosen,
	#mid_mtl_resin1_chosen,
	#mid_mtl_veil_add_chosen,
	#mid_mtl_resin2_chosen,
	#mid_mtl_matcsm_chosen,
	#mid_mtl_resin3_chosen,
	#mid_mtl_csm_add_chosen,
	#mid_mtl_resin4_chosen,
	#mid_mtl_resin_tot_chosen,
	#mid_mtl_katalis_chosen,
	#mid_mtl_sm_chosen,
	#mid_mtl_cobalt_chosen,
	#mid_mtl_dma_chosen,
	#mid_mtl_hydro_chosen,
	#mid_mtl_methanol_chosen,
	#mid_mtl_additive_chosen,
	#mid_mtl_matcsm2_chosen,
	#mid_mtl_resin21_chosen,
	#mid_mtl_csm_add2_chosen,
	#mid_mtl_resin22_chosen,
	#mid_mtl_wr2_chosen,
	#mid_mtl_resin23_chosen,
	#mid_mtl_wr_add2_chosen,
	#mid_mtl_resin24_chosen,
	#mid_mtl_rooving21_chosen,
	#mid_mtl_resin25_chosen,
	#mid_mtl_rooving22_chosen,
	#mid_mtl_resin26_chosen,
	#mid_mtl_resin_tot2_chosen,
	#mid_mtl_katalis2_chosen,
	#mid_mtl_sm2_chosen,
	#mid_mtl_cobalt2_chosen,
	#mid_mtl_dma2_chosen,
	#mid_mtl_hydro2_chosen,
	#mid_mtl_methanol2_chosen,
	#mid_mtl_additive2_chosen,
	#mid_mtl_veil3_chosen,
	#mid_mtl_resin31_chosen,
	#mid_mtl_veil_add3_chosen,
	#mid_mtl_resin32_chosen,
	#mid_mtl_matcsm3_chosen,
	#mid_mtl_resin33_chosen,
	#mid_mtl_csm_add3_chosen,
	#mid_mtl_resin34_chosen,
	#mid_mtl_resin_tot3_chosen,
	#mid_mtl_katalis3_chosen,
	#mid_mtl_sm3_chosen,
	#mid_mtl_cobalt3_chosen,
	#mid_mtl_dma3_chosen,
	#mid_mtl_hydro3_chosen,
	#mid_mtl_methanol3_chosen,
	#mid_mtl_additive3_chosen,
	#mid_mtl_resin41_chosen,
	#mid_mtl_katalis4_chosen,
	#mid_mtl_color4_chosen,
	#mid_mtl_tin4_chosen,
	#mid_mtl_chl4_chosen,
	#mid_mtl_stery4_chosen,
	#mid_mtl_wax4_chosen,
	#mid_mtl_mch4_chosen,
	#mid_mtl_additive4_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.HideCost').hide();
		$('.Hide').hide();
		$('#plactic').hide();
		
		$(document).on('change', '#component_list', function(e){
			var product = $(this).val();
			var product_parent = product.replaceAll(' ', '0_0');
			console.log(product_parent);
			if(product_parent == 0){
				return false;
			}
			loading_spinner();
			
			$.ajax({
				url: base_url +'product/get_custom_product/'+product_parent,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					if(data.nomor == 1){
						$('#id_1').html(data.spec1);
						$('#id_2').html(' | '+data.spec2);
						$('#diameter').attr('placeholder',data.spec1);
						$('#diameter2').attr('placeholder',data.spec2);
						$('#diameter2').show();
						$('#diameter2').val('');
						swal.close();
					}
					
					if(data.nomor == 0){
						$('#id_1').html(data.spec1);
						$('#id_2').html('');
						$('#diameter').attr('placeholder',data.spec1);
						$('#diameter2').hide();
						$('#diameter2').val(0);
						swal.close();
					}
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
			
		});
		
		// $(document).on('change', '#component_list', function(e){
			// var component_list = $(this).val();
			// if(component_list == 'plate' || component_list == 'joint plate' || component_list == 'taper plate' || component_list == 'square flange' || component_list == 'shim plate' || component_list == 'joint saddle'){
				// $('#id_1').html('Panjang');
				// $('#id_2').html(' | Lebar');
				// $('#diameter').attr('placeholder','Panjang');
				// $('#diameter2').attr('placeholder','Lebar');
				// var dimx1 = 'Panjang';
				// var dimx2 = 'Lebar';
			// }
			// if(component_list == 'rib taper plate' || component_list == 'rib end plate'){
				// $('#id_1').html('Panjang');
				// $('#id_2').html(' | Tinggi');
				// $('#diameter').attr('placeholder','Panjang');
				// $('#diameter2').attr('placeholder','Tinggi');
				// var dimx1 = 'Panjang';
				// var dimx2 = 'Tinggi';
			// }
			// if(component_list == 'rib' || component_list == 'joint rib'){
				// $('#id_1').html('Alas');
				// $('#id_2').html(' | Tinggi');
				// $('#diameter').attr('placeholder','Alas');
				// $('#diameter2').attr('placeholder','Tinggi');
				// var dimx1 = 'Alas';
				// var dimx2 = 'Tinggi';
			// }
			// if(component_list == 'figure 8'){
				// $('#id_1').html('Alas');
				// $('#id_2').html(' | Outer Diameter');
				// $('#diameter').attr('placeholder','Alas');
				// $('#diameter2').attr('placeholder','Outer Diameter');
				// var dimx1 = 'Alas';
				// var dimx2 = 'Outer Diameter';
			// }
			// if(component_list == 'loose flange' || component_list == 'spacer' || component_list == 'puddle flange' || component_list == 'joint puddle flange' || component_list == 'spacer ring' || component_list == 'blind spacer' || component_list == 'blind flange with hole'){ //puddle flange
				// $('#id_1').html('Inner Diameter');
				// $('#id_2').html(' | Outer Diameter');
				// $('#diameter').attr('placeholder','Inner Diameter');
				// $('#diameter2').attr('placeholder','Outer Diameter');
				// var dimx1 = 'Inner Diameter';
				// var dimx2 = 'Outer Diameter';
			// }
			// if(component_list == 'saddle'){
				// $('#id_1').html('Diameter');
				// $('#id_2').html(' | Panjang');
				// $('#diameter').attr('placeholder','Diameter');
				// $('#diameter2').attr('placeholder','Panjang');
				// var dimx1 = 'Diameter';
				// var dimx2 = 'Panjang';
			// }
			// if(component_list == 'inlet cone'){
				// $('#id_1').html('Inner Diameter');
				// $('#id_2').html(' | Tinggi');
				// $('#diameter').attr('placeholder','Inner Diameter');
				// $('#diameter2').attr('placeholder','Tinggi');
				// var dimx1 = 'Inner Diameter';
				// var dimx2 = 'Tinggi';
			// }
			// if(component_list == 'bellmouth'){
				// $('#id_1').html('Diameter 1');
				// $('#id_2').html(' | Diameter 2');
				// $('#diameter').attr('placeholder','Diameter 1');
				// $('#diameter2').attr('placeholder','Diameter 2');
				// var dimx1 = 'Diameter 1';
				// var dimx2 = 'Diameter 2';
			// }
			// if(component_list == 'spectacle blind' || component_list == 'blank and spacer' || component_list == 'end plate'){
				// $('#id_1').html('Diameter');
				// $('#id_2').html('');
				// $('#diameter').attr('placeholder','Diameter');
				// $('#diameter2').hide();
				// $('#diameter2').val(0);
				// var dimx1 = 'Diameter';
				// var dimx2 = '';
			// }
		// });

		$(document).on('click', '#simpan-bro', function(e){
			e.preventDefault();
			var cust				= $('#cust').val();
			var series				= $('#series').val();
			var component_list		= $('#component_list').val();
			
			var diameter		= $('#diameter').val();
			var diameter2		= $('#diameter2').val();
			var design			= $('#design').val();
			
			//LINER THICKNESS
			var mid_mtl_plastic		= $('#mid_mtl_plastic').val();
			var mid_mtl_veil		= $('#mid_mtl_veil').val();
			var mid_mtl_veil_add	= $('#mid_mtl_veil_add').val();
			var mid_mtl_matcsm		= $('#mid_mtl_matcsm').val();
			var mid_mtl_csm_add		= $('#mid_mtl_csm_add').val();
			var mid_mtl_resin_tot	= $('#mid_mtl_resin_tot').val();

			var mid_mtl_katalis		= $('#mid_mtl_katalis').val();
			var mid_mtl_sm			= $('#mid_mtl_sm').val();
			var mid_mtl_cobalt		= $('#mid_mtl_cobalt').val();
			var mid_mtl_dma			= $('#mid_mtl_dma').val();
			var mid_mtl_hydro		= $('#mid_mtl_hydro').val();
			var mid_mtl_methanol	= $('#mid_mtl_methanol').val();

			var layer_veil 			= $('#layer_veil').val();
			var layer_veil_add 		= $('#layer_veil_add').val();
			var layer_matcsm 		= $('#layer_matcsm').val();
			var layer_csm_add 		= $('#layer_csm_add').val();
			
			var last_plastic 		= $('#last_plastic').val();
			var last_veil 			= $('#last_veil').val();
			var last_veil_add 		= $('#last_veil_add').val();
			var last_matcsm 		= $('#last_matcsm').val();
			var last_csm_add 		= $('#last_csm_add').val();
			var last_resin_tot 		= $('#last_resin_tot').val();

			var persen_katalis 		= $('#persen_katalis').val();
			var persen_sm 			= $('#persen_sm').val();
			var persen_coblat 		= $('#persen_coblat').val();
			var persen_dma 			= $('#persen_dma').val();
			var persen_hydroquinone = $('#persen_hydroquinone').val();
			var persen_methanol 	= $('#persen_methanol').val();

			//STRYKTURE THICKNESS
			var mid_mtl_matcsm2		= $('#mid_mtl_matcsm2').val();
			var mid_mtl_csm_add2	= $('#mid_mtl_csm_add2').val();
			var mid_mtl_wr2			= $('#mid_mtl_wr2').val();
			var mid_mtl_wr_add2		= $('#mid_mtl_wr_add2').val();
			var mid_mtl_resin_tot2	= $('#mid_mtl_resin_tot2').val();

			var mid_mtl_katalis2	= $('#mid_mtl_katalis2').val();
			var mid_mtl_sm2			= $('#mid_mtl_sm2').val();
			var mid_mtl_cobalt2		= $('#mid_mtl_cobalt2').val();
			var mid_mtl_dma2		= $('#mid_mtl_dma2').val();
			var mid_mtl_hydro2		= $('#mid_mtl_hydro2').val();
			var mid_mtl_methanol2	= $('#mid_mtl_methanol2').val();

			var layer_matcsm2		= $('#layer_matcsm2').val();
			var layer_csm_add2		= $('#layer_csm_add2').val();
			var layer_wr2			= $('#layer_wr2').val();
			var layer_wr_add2		= $('#layer_wr_add2').val();
			
			var last_matcsm2 		= $('#last_matcsm2').val();
			var last_csm_add2 		= $('#last_csm_add2').val();
			var last_wr2 			= $('#last_wr2').val();
			var last_wr_add2 		= $('#last_wr_add2').val();
			var last_resin_tot2 	= $('#last_resin_tot2').val();

			var persen_katalis2		= $('#persen_katalis2').val();
			var persen_sm2			= $('#persen_sm2').val();
			var persen_coblat2		= $('#persen_coblat2').val();
			var persen_dma2			= $('#persen_dma2').val();
			var persen_hydro2		= $('#persen_hydroquinone2').val();
			var persen_methanol2	= $('#persen_methanol2').val();

			//EXTERNAL THICKNESS
			var mid_mtl_veil3		= $('#mid_mtl_veil3').val();
			var mid_mtl_veil_add3	= $('#mid_mtl_veil_add3').val();
			var mid_mtl_matcsm3		= $('#mid_mtl_matcsm3').val();
			var mid_mtl_csm_add3	= $('#mid_mtl_csm_add3').val();
			var mid_mtl_resin_tot3	= $('#mid_mtl_resin_tot3').val();

			var mid_mtl_katalis3	= $('#mid_mtl_katalis3').val();
			var mid_mtl_sm3			= $('#mid_mtl_sm3').val();
			var mid_mtl_cobalt3		= $('#mid_mtl_cobalt3').val();
			var mid_mtl_dma3		= $('#mid_mtl_dma3').val();
			var mid_mtl_hydro3		= $('#mid_mtl_hydro3').val();
			var mid_mtl_methanol3	= $('#mid_mtl_methanol3').val();

			var layer_veil3 		= $('#layer_veil3').val();
			var layer_veil_add3 	= $('#layer_veil_add3').val();
			var layer_matcsm3 		= $('#layer_matcsm3').val();
			var layer_csm_add3 		= $('#layer_csm_add3').val();
			
			var last_veil3 			= $('#last_veil3').val();
			var last_veil_add3 		= $('#last_veil_add3').val();
			var last_matcsm3 		= $('#last_matcsm3').val();
			var last_csm_add3 		= $('#last_csm_add3').val();
			var last_resin_tot3 	= $('#last_resin_tot3').val();

			var persen_katalis3 	= $('#persen_katalis3').val();
			var persen_sm3 			= $('#persen_sm3').val();
			var persen_coblat3 		= $('#persen_coblat3').val();
			var persen_dma3 		= $('#persen_dma3').val();
			var persen_hydroq3 		= $('#persen_hydroquinone3').val();
			var persen_methanol3 	= $('#persen_methanol3').val();

			//TOPCOAT
			var mid_mtl_resin41		= $('#mid_mtl_resin41').val();
			var mid_mtl_katalis4	= $('#mid_mtl_katalis4').val();
			var mid_mtl_color4		= $('#mid_mtl_color4').val();
			var mid_mtl_tin4		= $('#mid_mtl_tin4').val();
			var mid_mtl_chl4		= $('#mid_mtl_chl4').val();
			var mid_mtl_stery4		= $('#mid_mtl_stery4').val();
			var mid_mtl_wax4		= $('#mid_mtl_dma3').val();
			var mid_mtl_mch4		= $('#mid_mtl_hydro3').val();
			
			var last_resin41 		= $('#last_resin41').val();

			var persen_katalis4 	= $('#persen_katalis4').val();
			var persen_color4 		= $('#persen_color4').val();
			var persen_tin4 		= $('#persen_tin4').val();
			var persen_chl4 		= $('#persen_chl4').val();
			var persen_stery4 		= $('#persen_stery4').val();
			var persen_wax4 		= $('#persen_wax4').val();
			var persen_mch4 		= $('#persen_mch4').val();

			//Maxsimal Number
			var numberMax_liner		= $('#numberMax_liner').val();
			var numberMax_strukture	= $('#numberMax_strukture').val();
			var numberMax_external	= $('#numberMax_external').val();
			var numberMax_topcoat	= $('#numberMax_topcoat').val();

			$(this).prop('disabled',true);
 
			// if(cust == '' || cust == null || cust == 0){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Customer is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(series == '' || series == null || series == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Series is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(component_list == '' || component_list == null || component_list == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Component is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(diameter == '' || diameter == null){
				swal({
				  title	: "Error Message!",
				  text	: '... is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(diameter2 == '' || diameter2 == null){
				swal({
				  title	: "Error Message!",
				  text	: '... is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(design == '' || design == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Thickness is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			
			if(mid_mtl_plastic == '' || mid_mtl_plastic == null || mid_mtl_plastic == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Plastic Film/Mirror Glass Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_veil == '' || mid_mtl_veil == null || mid_mtl_veil == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Veil Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_veil_add == '' || mid_mtl_veil_add == null || mid_mtl_veil_add == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Veil Add Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_matcsm == '' || mid_mtl_matcsm == null || mid_mtl_matcsm == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'MAT/CSM Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_csm_add == '' || mid_mtl_csm_add == null || mid_mtl_csm_add == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'MAT/CSM Add Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_resin_tot == '' || mid_mtl_resin_tot == null || mid_mtl_resin_tot == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			if(mid_mtl_katalis == '' || mid_mtl_katalis == null || mid_mtl_katalis == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Katalis Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_sm == '' || mid_mtl_sm == null || mid_mtl_sm == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'SM Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_cobalt == '' || mid_mtl_cobalt == null || mid_mtl_cobalt == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Cobalt Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_dma == '' || mid_mtl_dma == null || mid_mtl_dma == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'DMA Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_hydro == '' || mid_mtl_hydro == null || mid_mtl_hydro == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Hydroquinone Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_methanol == '' || mid_mtl_methanol == null || mid_mtl_methanol == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Methanol Material [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			//Tambahan
			if(layer_veil == '' || layer_veil == null || layer_veil == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer Veil [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_veil_add == '' || layer_veil_add == null || layer_veil_add == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer Veil Add [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_matcsm == '' || layer_matcsm == null || layer_matcsm == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer MAT/CSM [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_csm_add == '' || layer_csm_add == null || layer_csm_add == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer CSM Add [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(last_plastic == '' || last_plastic == null || last_plastic == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Plastic [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_veil == '' || last_veil == null || last_veil == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Veil [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_veil_add == '' || last_veil_add == null || last_veil_add == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Veil Add [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_matcsm == '' || last_matcsm == null || last_matcsm == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Mat Csm [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_csm_add == '' || last_csm_add == null || last_csm_add == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Mat Csm Veil [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_resin_tot == '' || last_resin_tot == null || last_resin_tot == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Resin [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(persen_katalis == '' || persen_katalis == null || persen_katalis == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Katalis [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_sm == '' || persen_sm == null || persen_sm == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent SM [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_coblat == '' || persen_coblat == null || persen_coblat == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Coblat [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_dma == '' || persen_dma == null || persen_dma == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent DMA [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_hydroquinone == '' || persen_hydroquinone == null || persen_hydroquinone == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Hydroquinone [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_methanol == '' || persen_methanol == null || persen_methanol == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Methanol [LINER THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			//STRUKTUR THICKNESS
			if(mid_mtl_matcsm2 == '' || mid_mtl_matcsm2 == null || mid_mtl_matcsm2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'MAT/CSM Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_csm_add2 == '' || mid_mtl_csm_add2 == null || mid_mtl_csm_add2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'MAT/CSM Add Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_wr2 == '' || mid_mtl_wr2 == null || mid_mtl_wr2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'WR Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_wr_add2 == '' || mid_mtl_wr_add2 == null || mid_mtl_wr_add2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'WR Add Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_resin_tot2 == '' || mid_mtl_resin_tot2 == null || mid_mtl_resin_tot2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			//==
			if(mid_mtl_katalis2 == '' || mid_mtl_katalis2 == null || mid_mtl_katalis2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Katalis Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_sm2 == '' || mid_mtl_sm2 == null || mid_mtl_sm2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'SM Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_cobalt2 == '' || mid_mtl_cobalt2 == null || mid_mtl_cobalt2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Cobalt Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_dma2 == '' || mid_mtl_dma2 == null || mid_mtl_dma2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'DMA Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_hydro2 == '' || mid_mtl_hydro2 == null || mid_mtl_hydro2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Hydroquinone Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_methanol2 == '' || mid_mtl_methanol2 == null || mid_mtl_methanol2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Methanol Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			//Tambahan
			if(layer_matcsm2 == '' || layer_matcsm2 == null || layer_matcsm2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer MAT/CSM [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_csm_add2 == '' || layer_csm_add2 == null || layer_csm_add2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer CSM Add [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_wr2 == '' || layer_wr2 == null || layer_wr2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer WR [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_wr_add2 == '' || layer_wr_add2 == null || layer_wr_add2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer WR Add [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(last_matcsm2 == '' || last_matcsm2 == null || last_matcsm2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Mat Csm [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_csm_add2 == '' || last_csm_add2 == null || last_csm_add2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Mat Csm Add [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_wr2 == '' || last_wr2 == null || last_wr2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material WR [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_wr_add2 == '' || last_wr_add2 == null || last_wr_add2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material WR Add [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_resin_tot2 == '' || last_resin_tot2 == null || last_resin_tot2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Resin [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(persen_katalis2 == '' || persen_katalis2 == null || persen_katalis2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Katalis [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_sm2 == '' || persen_sm2 == null || persen_sm2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent SM [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_coblat2 == '' || persen_coblat2 == null || persen_coblat2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Coblat [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_dma2 == '' || persen_dma2 == null || persen_dma2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent DMA [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_hydro2 == '' || persen_hydro2 == null || persen_hydro2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Hydroquinone [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_methanol2 == '' || persen_methanol2 == null || persen_methanol2 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Methanol [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}


			//EXTERNAL LAYER
			if(mid_mtl_veil3 == '' || mid_mtl_veil3 == null || mid_mtl_veil3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Veil Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			if(mid_mtl_veil_add3 == '' || mid_mtl_veil_add3 == null || mid_mtl_veil_add3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Veil Add Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_matcsm3 == '' || mid_mtl_matcsm3 == null || mid_mtl_matcsm3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'MAT/CSM Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_csm_add3 == '' || mid_mtl_csm_add3 == null || mid_mtl_csm_add3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'MAT/CSM Add Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_resin_tot3 == '' || mid_mtl_resin_tot3 == null || mid_mtl_resin_tot3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			//==
			if(mid_mtl_katalis3 == '' || mid_mtl_katalis3 == null || mid_mtl_katalis3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Katalis Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_sm3 == '' || mid_mtl_sm3 == null || mid_mtl_sm3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'SM Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_cobalt3 == '' || mid_mtl_cobalt3 == null || mid_mtl_cobalt3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Cobalt Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_dma3 == '' || mid_mtl_dma3 == null || mid_mtl_dma3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'DMA Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_hydro3 == '' || mid_mtl_hydro3 == null || mid_mtl_hydro3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Hydroquinone Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_methanol3 == '' || mid_mtl_methanol3 == null || mid_mtl_methanol3 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Methanol Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			//Tambahan
			if(layer_veil3 == '' || layer_veil3 == null || layer_veil3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer Veil [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_veil_add3 == '' || layer_veil_add3 == null || layer_veil_add3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer Veil Add [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_matcsm3 == '' || layer_matcsm3 == null || layer_matcsm3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer MAT/CSM [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(layer_csm_add3 == '' || layer_csm_add3 == null || layer_csm_add3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer MAT/CSM Add [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(last_veil3 == '' || last_veil3 == null || last_veil3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Veil [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_veil_add3 == '' || last_veil_add3 == null || last_veil_add3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Veil Add [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_matcsm3 == '' || last_matcsm3 == null || last_matcsm3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Mat Csm [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_csm_add3 == '' || last_csm_add3 == null || last_csm_add3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Mat Csm Add [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(last_resin_tot3 == '' || last_resin_tot3 == null || last_resin_tot3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Resin [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			
			if(persen_katalis3 == '' || persen_katalis3 == null || persen_katalis3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Katalis [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_sm3 == '' || persen_sm3 == null || persen_sm3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent SM [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_coblat3 == '' || persen_coblat3 == null || persen_coblat3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Coblat [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_dma3 == '' || persen_dma3 == null || persen_dma3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent DMA [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_hydroq3 == '' || persen_hydroq3 == null || persen_hydroq3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Hydroquinone [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_methanol3 == '' || persen_methanol3 == null || persen_methanol3 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Methanol [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			//TOPCOAT
			if(mid_mtl_resin41 == '' || mid_mtl_resin41 == null || mid_mtl_resin41 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_katalis4 == '' || mid_mtl_katalis4 == null || mid_mtl_katalis4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Katalis Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_color4 == '' || mid_mtl_color4 == null || mid_mtl_color4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Color Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_tin4 == '' || mid_mtl_tin4 == null || mid_mtl_tin4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Tinuvin Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_chl4 == '' || mid_mtl_chl4 == null || mid_mtl_chl4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Chlroroform Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_stery4 == '' || mid_mtl_stery4 == null || mid_mtl_stery4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Steryne Monomer Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_wax4 == '' || mid_mtl_wax4 == null || mid_mtl_wax4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Solution Wax Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(mid_mtl_mch4 == '' || mid_mtl_mch4 == null || mid_mtl_mch4 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Methelene Chlorida Material [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(last_resin41 == '' || last_resin41 == null || last_resin41 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Last Material Resin [EXTERNAL THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			//tambahan
			if(persen_katalis4 == '' || persen_katalis4 == null || persen_katalis4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Katalis [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_color4 == '' || persen_color4 == null || persen_color4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Color [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_tin4 == '' || persen_tin4 == null || persen_tin4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Tinuvin [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_chl4 == '' || persen_chl4 == null || persen_chl4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Chlroroform [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_stery4 == '' || persen_stery4 == null || persen_stery4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent SM [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_wax4 == '' || persen_wax4 == null || persen_wax4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Wax [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(persen_mch4 == '' || persen_mch4 == null || persen_mch4 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Percent Methanol [TOPCOAT] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			//MAX Number
			if(numberMax_liner != 0 || numberMax_strukture != 0 || numberMax_external != 0 || numberMax_topcoat != 0){
				var intL = 0;
				var intError = 0;
				var pesan = '';

				if(numberMax_topcoat != 0){
					$('#detail_body_topcoat').find('tr').each(function(){
						intL++;
						var findId	= $(this).attr('id');
						// console.log(findId);
						// return false;
						var nomor	= findId.split('_');
						var perse_topcoat		= $('#perse_topcoat_'+nomor[1]).val();
						var containing_topcoat	= $('#containing_topcoat_'+nomor[1]).val();
						var id_material_topcoat	= $('#id_material_topcoat_'+nomor[1]).val();
						var id_category_topcoat	= $('#id_category_topcoat_'+nomor[1]).val();

						if(perse_topcoat == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Percent [TOPCOAT] has not empty ...";
						}
						if(containing_topcoat == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Comparison [TOPCOAT] has not empty ...";
						}
						if(id_material_topcoat == '' || id_material_topcoat == null || id_material_topcoat == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Material [TOPCOAT] has not selected ...";
						}
						if(id_category_topcoat == '' || id_category_topcoat == null || id_category_topcoat == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Category Material [TOPCOAT] has not selected ...";
						}
					});
				}

				if(numberMax_external != 0){
					$('#detail_body_external').find('tr').each(function(){
						intL++;
						var findId	= $(this).attr('id');
						// console.log(findId);
						// return false;
						var nomor	= findId.split('_');
						var perse_external			= $('#perse_external_'+nomor[1]).val();
						var containing_external	= $('#containing_external_'+nomor[1]).val();
						var id_material_external	= $('#id_material_external_'+nomor[1]).val();
						var id_category_external	= $('#id_category_external_'+nomor[1]).val();

						if(perse_external == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Percent [EXTERNAL THICKNESS] has not empty ...";
						}
						if(containing_external == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Comparison [EXTERNAL THICKNESS] has not empty ...";
						}
						if(id_material_external == '' || id_material_external == null || id_material_external == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Material [EXTERNAL THICKNESS] has not selected ...";
						}
						if(id_category_external == '' || id_category_external == null || id_category_external == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Category Material [EXTERNAL THICKNESS] has not selected ...";
						}
					});
				}

				if(numberMax_strukture != 0){
					$('#detail_body_strukture').find('tr').each(function(){
						intL++;
						var findId	= $(this).attr('id');
						// console.log(findId);
						// return false;
						var nomor	= findId.split('_');
						var perse_strukture			= $('#perse_strukture_'+nomor[1]).val();
						var containing_strukture	= $('#containing_strukture_'+nomor[1]).val();
						var id_material_strukture	= $('#id_material_strukture_'+nomor[1]).val();
						var id_category_strukture	= $('#id_category_strukture_'+nomor[1]).val();

						if(perse_strukture == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" :Percent [STRUKTURE THICKNESS] has not empty ...";
						}
						if(containing_strukture == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Comparison [STRUKTURE THICKNESS] has not empty ...";
						}
						if(id_material_strukture == '' || id_material_strukture == null || id_material_strukture == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Material [STRUKTURE THICKNESS] has not selected ...";
						}
						if(id_category_strukture == '' || id_category_strukture == null || id_category_strukture == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Category Material [STRUKTURE THICKNESS] has not selected ...";
						}
					});
				}

				if(numberMax_liner != 0){
					$('#detail_body_liner').find('tr').each(function(){
						intL++;
						var findId	= $(this).attr('id');
						// console.log(findId);
						// return false;
						var nomor	= findId.split('_');
						var perse_liner			= $('#perse_liner_'+nomor[1]).val();
						var containing_liner	= $('#containing_liner_'+nomor[1]).val();
						var id_material_liner	= $('#id_material_liner_'+nomor[1]).val();
						var id_category_liner	= $('#id_category_liner_'+nomor[1]).val();

						if(perse_liner == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Percent [LINER THICKNESS] has not empty ...";
						}
						if(containing_liner == '' ){
							intError++;
							pesan = "Number "+nomor[1]+" : Comparison [LINER THICKNESS] has not empty ...";
						}
						if(id_material_liner == '' || id_material_liner == null || id_material_liner == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Material [LINER THICKNESS] has not selected ...";
						}
						if(id_category_liner == '' || id_category_liner == null || id_category_liner == 0 ){
							intError++;
							pesan = "Number "+nomor[1]+" : Category Material [LINER THICKNESS] has not selected ...";
						}
					});
				}

				if(intError > 0){
					// alert(pesan);
					swal({
						title				: "Notification Message !",
						text				: pesan,
						type				: "warning"
					});
					$('#simpan-bro').prop('disabled',false);
					return false;
				}
			}

			$('#simpan-bro').prop('disabled',false);

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
						var baseurl		= base_url + active_controller +'/index';
						$.ajax({
							url			: baseurl,
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
									window.location.href = base_url + 'component_custom';
								}
								else if(data.status == 2){
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
								else if(data.status == 3){
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
								else{
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
								$('#simpan-bro').prop('disabled',false);
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
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});
	});

	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}

</script>
