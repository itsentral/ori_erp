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
				<div class='headerTitle'>DETAIL PRODUCT</div>
                <input type="hidden" name='id_deadstok' id='id_deadstok' value='<?=$kode;?>'>
				<div class='form-group row'>
					<div class="col-md-12">
						<table width='80%'>
							<tr>
								<td width='20%'>Sales Order</td>
								<td width='1%'>:</td>
								<td><?=$HeaderDeadstok[0]['no_so'];?></td>
							</tr>
							<tr>
								<td>IPP</td>
								<td>:</td>
								<td><?=$HeaderDeadstok[0]['no_ipp'];?></td>
							</tr>
							<tr>
								<td>No SPK</td>
								<td>:</td>
								<td><?=$HeaderDeadstok[0]['no_spk'];?></td>
							</tr>
							<tr>
								<td>Product Deadstok</td>
								<td>:</td>
								<td><?=$HeaderDeadstok[0]['product_name'];?></td>
							</tr>
							<tr>
								<td>Spec Deadstok</td>
								<td>:</td>
								<td><?=$HeaderDeadstok[0]['product_spec'];?></td>
							</tr>
							<tr>
								<td>Qty</td>
								<td>:</td>
								<td><?=$HeaderDeadstok[0]['qty'];?></td>
							</tr>
							<tr>
								<td>Proses</td>
								<td>:</td>
								<td><?=$HeaderDeadstok[0]['proses'];?></td>
							</tr>
						</table>
					</div>
				</div>
			</section>
			<!-- //// -->
			<section id="detail">
				<!-- ====================================================================================================== -->
				<!-- ============================================LINER THICKNESS=========================================== -->
				<!-- ====================================================================================================== -->
				<div class='headerTitle'>LINER THIKNESS / CB</div>
				<div class='form-group row'>
					<input type='text' name='detail_name' id='detail_name' class='HideCost' value='LINER THIKNESS / CB'>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b><div id='mirror'>MIRROR GLASS<span class='text-red'>*</span></div><div id='plactic'>PLASTIC FILM<span class='text-red'>*</span></div></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_plastic','name'=>'ListDetail[1][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_veil','name'=>'ListDetail[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_veil_add','name'=>'ListDetail[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_matcsm','name'=>'ListDetail[6][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_csm_add','name'=>'ListDetail[8][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_resin_tot','name'=>'ListDetail[10][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_katalis','name'=>'ListDetailPlus[0][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_sm','name'=>'ListDetailPlus[1][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_cobalt','name'=>'ListDetailPlus[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_dma','name'=>'ListDetailPlus[3][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_hidro','name'=>'ListDetailPlus[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_methanol','name'=>'ListDetailPlus[5][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
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

					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_matcsm2','name'=>'ListDetail2[0][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_csm_add2','name'=>'ListDetail2[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>WR<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_wr2','name'=>'ListDetail2[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL WR<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_wr_add2','name'=>'ListDetail2[6][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ROOVING<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_rv2','name'=>'ListDetail2[8][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL ROOVING<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_rv_add2','name'=>'ListDetail2[10][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_resin_tot2','name'=>'ListDetail2[12][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_katalis2','name'=>'ListDetailPlus2[0][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_sm2','name'=>'ListDetailPlus2[1][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_cobalt2','name'=>'ListDetailPlus2[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_dma2','name'=>'ListDetailPlus2[3][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_hidro2','name'=>'ListDetailPlus2[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_methanol2','name'=>'ListDetailPlus2[5][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
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
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_veil3','name'=>'ListDetail3[0][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL VEIL<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_veil_add3','name'=>'ListDetail3[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_matcsm3','name'=>'ListDetail3[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_csm_add3','name'=>'ListDetail3[6][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_resin_tot3','name'=>'ListDetail3[8][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_katalis3','name'=>'ListDetailPlus3[0][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_sm3','name'=>'ListDetailPlus3[1][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_cobalt3','name'=>'ListDetailPlus3[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_dma3','name'=>'ListDetailPlus3[3][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_hidro3','name'=>'ListDetailPlus3[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_methanol3','name'=>'ListDetailPlus3[5][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
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
					<div class='col-sm-8'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_resin41','name'=>'ListDetailPlus4[0][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_katalis4','name'=>'ListDetailPlus4[1][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Color<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_color4','name'=>'ListDetailPlus4[2][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Tinuvin<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_tin4','name'=>'ListDetailPlus4[3][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Chlroroform<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_chl4','name'=>'ListDetailPlus4[4][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_stery4','name'=>'ListDetailPlus4[5][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Solution Wax<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_wax4','name'=>'ListDetailPlus4[6][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
						?>
					</div>

				</div>
				<div class='form-group row'>
					<label class='col-sm-2'></label>

					<label class='label-control col-sm-1'><b>Met. Chlorida<span class='text-red'>*</span></b></label>
					<div class='col-sm-7'>
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
					</div>
					<div class='col-sm-2'>
						<?php
							echo form_input(array('id'=>'last_mch4','name'=>'ListDetailPlus4[7][last_cost]','class'=>'form-control  numberOnly5 input-sm ','autocomplete'=>'off'));
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
</form>

<?php $this->load->view('include/footer'); ?>
<script src="<?php echo base_url('application/views/Est_js/custom_est_deadstok.js'); ?>"></script>
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
		$(".numberOnly5").autoNumeric('init', {mDec: '5', aPad: false});
		
		$('.HideCost').hide();
		$('.Hide').hide();
		$('#plactic').hide();

		$(document).on('click', '#simpan-bro', function(e){
			e.preventDefault();
			
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
						var baseurl		= base_url + active_controller +'/estimasi';
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
										  timer	: 7000
										});
									window.location.href = base_url + 'est_modifikasi_deadstok';
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								else if(data.status == 3){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#simpan-bro').prop('disabled',false);
							},
							error: function() {
								swal({
								  title				: "Error Message !",
								  text				: 'An Error Occured During Process. Please try again..',
								  type				: "warning",
								  timer				: 7000
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
