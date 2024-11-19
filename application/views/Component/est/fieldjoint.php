<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<button type='button' name='simpan-bro' id='simpan-bro' class='btn btn-primary btn-sm' style='width:100px;right:0;float:right;margin:5px'>Save</button>
			<a class="btn btn-sm btn-success" id="calc" style="right:0;float:right;margin:5px">Calculation</a>
		</div>

		<div class="box-body">
			<!-- NEW-->

			<div class='headerTitleGroup'>GROUP COMPONENT</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Choose Customer if Custom </b></label>
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
							echo "<option value='".$valx['kode_group']."'>".strtoupper($valx['kode_group'])."</option>";
						}
					 ?>
					</select>
				</div>

			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Diameter 1 <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'top_type_1','name'=>'top_type_1','class'=>'form-control input-sm Hide'));
						echo form_input(array('id'=>'diameter_1','name'=>'diameter_1','class'=>'form-control input-sm Hide'));
					?>
					<select name='d1' id='d1' class='form-control input-sm'>
						<option value='0'>Select Diameter</option>
					<?php
						foreach($product AS $val => $valx){
							echo "<option value='".$valx['id']."'>".ucfirst(strtolower($valx['nm_product']))."</option>";
						}
					 ?>
					</select>
				</div>

				<label class='label-control col-sm-2'><b>Standard Tolerance By <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='top_toleran' id='top_toleran' class='form-control input-sm'>
						<option value='0'>Select Tolerance By</option>
					<?php
						foreach($customer AS $val => $valx){
							$seL = ($valx['id_customer'] == 'C100-1903000')?'selected':'';
							// $seL = "";
							echo "<option value='".$valx['id_customer']."' ".$seL.">".strtoupper(strtolower($valx['nm_customer']))."</option>";
						}
					 ?>
					</select>
				</div>

			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Keterangan</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'ket_plus','name'=>'ket_plus','class'=>'form-control input-sm','placeholder'=>'Isi dengan singkat / kode','maxlength'=>10));
					?>
				</div>
			</div>

			<div class='headerTitleGroup'>SPESIFICATION COMPONENT</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Minimum Width <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('id'=>'minimum_width','name'=>'minimum_width','class'=>'form-control input-sm numberOnly','placeholder'=>'Width Minimum'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Pipe Thickness <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('type'=>'text','id'=>'pipe_thickness','name'=>'pipe_thickness','class'=>'form-control input-sm numberOnly','placeholder'=>'Pipe Thickness'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Faktor <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('type'=>'text','id'=>'factor','name'=>'factor','class'=>'form-control input-sm','readonly'=>'readonly','value'=>'1'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Overlap <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm numberOnly'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Joint Thickness <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('type'=>'text','id'=>'joint_thickness','name'=>'joint_thickness','class'=>'form-control input-sm','readonly'=>'readonly','value'=>'0'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Factor Thickness <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('type'=>'text','id'=>'factor_thickness','name'=>'factor_thickness','class'=>'form-control input-sm numberOnly','value'=>'1.5'));
					?>
				</div>
			</div>
			
			<div class='headerTitle'>GLASS</div> 
			<table class="table" border="0">
				<thead>
					<th width="10%">MATERIAL TYPE</th>
					<th>MATERIAL</th>
					<th width="16%">AREA WEIHT</th>
					<th width="16%">RESIN CONTENT</th>
					<th width="16%">THICKNESS</th>
					<th width="16%">MATERIAL WEIGHT</th>
				</thead>
				<?php
					$veil_res 	= 9/1;
					$wr_res 	= 45/55;
					$csm_res 	= 70/30;
				?>
				<tbody>
					<tr>
						<td>VEIL</td>
						<td><?= $this->master_model->get_select('veil');?></td>
						<td><?= form_input(array('id'=>'veil_area_weight','name'=>'glass[area_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Area Weight','readonly'=>'readonly'));?></td>
						<td><?= form_input(array('id'=>'veil_resin_content','name'=>'glass[resin_content][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Content','value'=>$veil_res));?></td>
						<td><?= form_input(array('id'=>'veil_thickness','name'=>'glass[thickness][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness','readonly'=>'readonly'));?></td>
						<td><?= form_input(array('id'=>'veil_material_weight','name'=>'glass[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Material Weight','readonly'=>'readonly'));?>
							<input type='hidden' id='veil_material_weight_i' >
                            <input type='hidden' id='veil_material_weight_o' >
						</td>
					</tr>
					<tr>
						<td>WR</td>
						<td><?= $this->master_model->get_select('wr');?></td>
						<td><?= form_input(array('id'=>'wr_area_weight','name'=>'glass[area_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Area Weight','readonly'=>'readonly'));?></td>
						<td><?= form_input(array('id'=>'wr_resin_content','name'=>'glass[resin_content][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Content','value'=>round($wr_res,3)));?></td>
						<td><?= form_input(array('id'=>'wr_thickness','name'=>'glass[thickness][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness','readonly'=>'readonly'));?></td>
						<td><?= form_input(array('id'=>'wr_material_weight','name'=>'glass[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Material Weight','readonly'=>'readonly'));?>
							<input type='hidden' id='wr_material_weight_i' >
                            <input type='hidden' id='wr_material_weight_o' >
						</td>
					</tr>
					<tr>
						<td>CSM</td>
						<td><?= $this->master_model->get_select('csm');?></td>
						<td><?= form_input(array('id'=>'csm_area_weight','name'=>'glass[area_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Area Weight','readonly'=>'readonly'));?></td>
						<td><?= form_input(array('id'=>'csm_resin_content','name'=>'glass[resin_content][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Content','value'=>round($csm_res,3)));?></td>
						<td><?= form_input(array('id'=>'csm_thickness','name'=>'glass[thickness][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness','readonly'=>'readonly'));?></td>
						<td><?= form_input(array('id'=>'csm_material_weight','name'=>'glass[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Material Weight','readonly'=>'readonly'));?>
							<input type='hidden' id='csm_material_weight_i' >
                            <input type='hidden' id='csm_material_weight_o' >
						</td>
					</tr>
				</tbody>
			</table>

			<div class='headerTitle'>RESIN & ADD</div>
			<table class="table" border="0">
				<thead>
					<th width="25%">MATERIAL TYPE</th>
					<th width="25%">MATERIAL</th>
					<th width="25%">PERCENTAGE(%)</th>
					<th width="25%">MATERIAL WEIGHT</th>
				</thead>
				<tbody>
					<!--
					<tr>
						<td>RESIN</td>
						<td><?= $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN');?></td>
						<td><?= form_input(array('id'=>'resin_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'NaN'));?></td>
						<td><?= form_input(array('id'=>'resin_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					-->
					<tr>
						<td>RESIN INSIDE</td>
						<td><?= $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN INSIDE');?></td>
						<td><?= form_input(array('id'=>'resin_percentage_in','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Inside','value'=>'NaN'));?></td>
						<td><?= form_input(array('id'=>'resin_material_weight_in','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Inside','readonly'=>'readonly'));?></td>
					</tr>
                    <tr>
						<td>RESIN OUTSIDE</td>
						<td><?= $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN OUTSIDE');?></td>
						<td><?= form_input(array('id'=>'resin_percentage_out','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Outside','value'=>'NaN'));?></td>
						<td><?= form_input(array('id'=>'resin_material_weight_out','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Outside','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>CATALYS</td>
						<td><?= $this->master_model->get_select_detail('TYP-0002','resinnadd[id_material][]','CATALYS');?></td>
						<td><?= form_input(array('id'=>'catalys_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'2'));?></td>
						<td><?= form_input(array('id'=>'catalys_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>DEMPUL</td>
						<td><?= $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','DEMPUL');?></td>
						<td><?= form_input(array('id'=>'dempul_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'140'));?></td>
						<td><?= form_input(array('id'=>'dempul_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>CARBOSIL BUBUK</td>
						<td><?= $this->master_model->get_select_detail('TYP-001519','resinnadd[id_material][]','CARBOSIL BUBUK');?></td>
						<td><?= form_input(array('id'=>'carbosilbubuk_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'15'));?></td>
						<td><?= form_input(array('id'=>'carbosilbubuk_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>RESIN CARBOSIL</td>
						<td><?= $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN CARBOSIL');?></td>
						<td><?= form_input(array('id'=>'resincarbosil_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'85'));?></td>
						<td><?= form_input(array('id'=>'resincarbosil_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>RESIN TOPCOAT</td>
						<td><?= $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN TOPCOAT');?></td>
						<td><?= form_input(array('id'=>'resintopcoat_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'NaN'));?></td>
						<td><?= form_input(array('id'=>'resintopcoat_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>COBALT</td>
						<td><?= $this->master_model->get_select_detail('TYP-0021','resinnadd[id_material][]','COBALT');?></td>
						<td><?= form_input(array('id'=>'cobalt_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'2'));?></td>
						<td><?= form_input(array('id'=>'cobalt_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>PIGMENT</td>
						<td><?= $this->master_model->get_select_detail('TYP-0007','resinnadd[id_material][]','PIGMENT');?></td>
						<td><?= form_input(array('id'=>'pigment_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'5'));?></td>
						<td><?= form_input(array('id'=>'pigment_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>TINUVIN</td>
						<td><?= $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','TINUVIN');?></td>
						<td><?= form_input(array('id'=>'tinuvin_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0.3'));?></td>
						<td><?= form_input(array('id'=>'tinuvin_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>CHLOROFORM</td>
						<td><?= $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','CHLOROFORM');?></td>
						<td><?= form_input(array('id'=>'chloroform_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'2'));?></td>
						<td><?= form_input(array('id'=>'chloroform_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>SOLUTION WAX</td>
						<td><?= $this->master_model->get_select_detail('TYP-0008','resinnadd[id_material][]','SOLUTION WAX');?></td>
						<td><?= form_input(array('id'=>'solutionwax_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'3'));?></td>
						<td><?= form_input(array('id'=>'solutionwax_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
					<tr>
						<td>ACCELERATOR</td>
						<td><?= $this->master_model->get_select_detail('TYP-0021','resinnadd[id_material][]','ACCELERATOR');?></td>
						<td><?= form_input(array('id'=>'accelerator_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0.2'));?></td>
						<td><?= form_input(array('id'=>'accelerator_material_weight','name'=>'resinnadd[material_weight][]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));?></td>
					</tr>
				</tbody>
			</table>
			<!-- ================== ADD =============== -->
			<table id="my-grid_resin" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody id='detail_body_resin'>
				</tbody>
			</table>
			<br>
			<button type='button' name='add_resin' id='add_resin' class='btn btn-success btn-sm' style='width:100px; margin-left: 10px;'>Add Material</button>
			<input type='hidden' name='numberMax_resin' id='numberMax_resin' value='0'>
			<!-- ================== END =============== -->
			<!-- ====================================================================================================== -->
			<!-- ============================================INSIDE LAMINATION========================================= -->
			<!-- ====================================================================================================== -->

			<div class='headerTitle'>INSIDE LAMINATION</div>
			<table class="table" border="0">
				<thead>
					<th width="5%">Lapisan</th>
					<th width="10%">STD GLASS CONFIGURATION</th>
					<th width="5%">WIDTH</th>
					<th width="5%">STAGE</th>
					<th width="10%">GLASS CONFIGURATION</th>
					<th width="15%" colspan="2">THICKNESS</th>
					<th width="5%">GLASS LENGTH</th>
					<th width="5%">WEIGHT VEIL</th>
					<th width="5%">WEIGTH CSM</th>
					<th width="5%">WEIGTH WR</th>
				</thead>
				<tbody>
					<?php 
						$no = 0;
						foreach ($ILamination as $key => $v) { $no++?>
							<tr id="IL_<?=$no;?>">
								<input type="hidden" id="diameter_penentuan_<?=$no?>" name="diameter_penentuan_<?=$no?>" value="<?=$v['diameter_penentuan']?>">
								<input type="hidden" id="std_gc_<?=$no?>" name="std_gc_<?=$no?>" value="<?=$v['type']?>">
								<td >
									<input class="form-control input-sm" type="text" id="lapisan_<?=$no?>" name="lapisan_<?=$no?>" value="<?=$v['lapisan']?>" readonly>
								</td>
								<td >
									<?php	if ($v['type'] == "RUMUS") {?>
										<input class="form-control input-sm" type="text" id="std_<?=$no?>" name="std_<?=$no?>" value="" readonly>
									<?php	}else {	?>
										<input class="form-control input-sm" type="text" id="std_<?=$no?>" name="std_<?=$no?>" value="<?=$v['type']?>" readonly>
									<?php	}	?>
								</td>
								<td >
									<input class="form-control input-sm" type="text" id="width_<?=$no?>" name="width_<?=$no?>" value="" readonly>
								</td>
								<?php	if ($no == 1) {?>
									<td  style="text-align:center;vertical-align:middle" rowspan="<?=count($ILamination);?>">
										<input class="form-control input-sm" type="text" id="stage_<?=$no?>" name="stage_<?=$no?>" value="<?=$v['stage_ke']?>" readonly>
									</td>
								<?php	}	?>
								<td >
									<input class="form-control input-sm" type="text" id="glassconfiguration_<?=$no?>" name="glassconfiguration_<?=$no?>" value="" readonly>
								</td>
								<td >
									<input class="form-control input-sm" type="text" id="thickness1_<?=$no?>" name="thickness1_<?=$no?>" value="" readonly>
								</td>
								<td >
									<input class="form-control input-sm" type="text" id="thickness2_<?=$no?>" name="thickness2_<?=$no?>" value="" readonly>
								</td>
								<?php	if ($no == 1) {?>
									<td rowspan="<?=count($ILamination);?>" >
										<input class="form-control input-sm" type="text" id="glasslength_<?=$no?>" name="glasslength_<?=$no?>" value="" readonly>
									</td>
								<?php	}	?>
								<td >
									<input class="form-control input-sm" type="text" id="veil_weight_<?=$no?>" name="veil_weight_<?=$no?>" value="" readonly>
								</td>
								<td >
									<input class="form-control input-sm" type="text" id="csm_weight_<?=$no?>" name="csm_weight_<?=$no?>" value="" readonly>
								</td>
								<td >
									<input class="form-control input-sm" type="text" id="wr_weight_<?=$no?>" name="wr_weight_<?=$no?>" value="" readonly>
								</td>
							</tr>
					<?php } ?>
					<input type="hidden" name="no_il" value="<?=$no?>">
				</tbody>
			</table>


			<!-- ====================================================================================================== -->
			<!-- ============================================OUTSIDE LAMINATION======================================== -->
			<!-- ====================================================================================================== -->

			<div class='headerTitle'>OUTSIDE LAMINATION</div>
			<table class="table" border="0">
				<thead>
					<th width="5%">Lapisan</th>
					<th width="10%">STD GLASS CONFIGURATION</th>
					<th width="5%">WIDTH</th>
					<th width="5%">STAGE</th>
					<th width="10%">GLASS CONFIGURATION</th>
					<th width="15%" colspan="2">THICKNESS</th>
					<th width="5%">GLASS LENGTH</th>
					<th width="5%">WEIGHT VEIL</th>
					<th width="5%">WEIGTH CSM</th>
					<th width="5%">WEIGTH WR</th>
				</thead>
				<tbody>
					<?php $no = 0; ?>
					<?php for ($i=1; $i <= 20; $i++) {
						if ($i == 1) {
							$new = $OLamination_1 = $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION' AND stage_ke = 1")->result_array();
						}else {
							$new = $OLamination_2 = $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='OUTSIDE LAMINATION' AND stage_ke = 2")->result_array();
						}
						$b = 0;
						foreach ($new as $key => $v) { $no++;$b++?>
							<?php if (($no+1) % 7 == 0): ?>
								<tr id="OL_<?=$no;?>" style="border-bottom:2px solid #000">
							<?php else: ?>
								<tr id="OL_<?=$no;?>">
							<?php endif; ?>
								<input type="hidden" id="o_diameter_penentuan_<?=$no?>" name="o_diameter_penentuan_<?=$no?>" value="<?=$v['diameter_penentuan']?>">
								<input type="hidden" id="o_std_gc_<?=$no?>" name="o_std_gc_<?=$no?>" value="<?=$v['type']?>">
								<input class="form-control input-sm" type="hidden" id="o_stage_ke_<?=$no?>" name="o_stage_ke_<?=$no?>" value="<?=$i?>">
								<td>
									<input class="form-control input-sm" type="text" id="o_lapisan_<?=$no?>" name="o_lapisan_<?=$no?>" value="<?=$no?>" readonly>
								</td>
								<td>
									<?php	if ($v['type'] == "RUMUS") {?>
										<input class="form-control input-sm" type="text" id="o_std_<?=$no?>" name="o_std_<?=$no?>" value="" readonly>
									<?php	}else {	?>
										<input class="form-control input-sm" type="text" id="o_std_<?=$no?>" name="o_std_<?=$no?>" value="<?=$v['type']?>" readonly>
									<?php	}	?>
								</td>
								<td>
									<input class="form-control input-sm" type="text" id="o_width_<?=$no?>" name="o_width_<?=$no?>" value="" readonly>
								</td>
								<?php	if ($no == 1) {?>
									<td  style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" rowspan="<?=count($new);?>">
										<input class="form-control input-sm" type="text" id="o_stage_<?=$i?>" name="o_stage_<?=$i?>" value="<?=$i?>" readonly>
									</td>
								<?php	}elseif (($no % 7) == 0) {?>
									<td style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" rowspan="<?=count($new);?>">
										<input class="form-control input-sm" type="text" id="0_stage_<?=$i?>" name="0_stage_<?=$i?>" value="<?=$i?>" readonly>
									</td>
								<?php	}	?>
								<td>
									<input class="form-control input-sm" type="text" id="o_glassconfiguration_<?=$no?>" name="o_glassconfiguration_<?=$no?>" value="" readonly>
								</td>
								<td>
									<input class="form-control input-sm" type="text" id="o_thickness1_<?=$no?>" name="o_thickness1_<?=$no?>" value="" readonly>
								</td>
								<td>
									<input class="form-control input-sm" type="text" id="o_thickness2_<?=$no?>" name="o_thickness2_<?=$no?>" value="" readonly>
								</td>
								<?php	if ($no == 1) {?>
									<td rowspan="<?=count($new);?>" style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" >
										<input class="form-control input-sm" type="text" id="o_glasslength_<?=$i?>" name="o_glasslength_<?=$i?>" value="" readonly>
									</td>
								<?php	}elseif (($no % 7) == 0) {?>
									<td rowspan="<?=count($new);?>" style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" >
										<input class="form-control input-sm" type="text" id="o_glasslength_<?=$i?>" name="o_glasslength_<?=$i?>" value="" readonly>
									</td>
								<?php	}	?>
								<td>
									<input class="form-control input-sm" type="text" id="o_veil_weight_<?=$no?>" name="o_veil_weight_<?=$no?>" value="" readonly>
								</td>
								<td>
									<input class="form-control input-sm" type="text" id="o_csm_weight_<?=$no?>" name="o_csm_weight_<?=$no?>" value="" readonly>
								</td>
								<td>
									<input class="form-control input-sm" type="text" id="o_wr_weight_<?=$no?>" name="o_wr_weight_<?=$no?>" value="" readonly>
								</td>
							</tr>
								<?php
							}
						}	?>
						<input type="hidden" name="no_ol" value="<?=$no?>">
				</tbody>
			</table>

			<br><br><br>
			<!-- END -->


		</div>

		<!-- modal -->
		<div class="modal fade" id="ModalView">
			<div class="modal-dialog"  style='width:75%; '>
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
		<!-- modal -->

</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
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
		margin-bottom: 5px;
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


	#mid_mtl_resin1_chosen,
	#mid_mtl_resin2_chosen,
	#mid_mtl_resin3_chosen,
	#mid_mtl_resin4_chosen,
	#mid_mtl_resin21_chosen,
	#mid_mtl_resin22_chosen,
	#mid_mtl_resin23_chosen,
	#mid_mtl_resin24_chosen,
	#mid_mtl_resin25_chosen,
	#mid_mtl_resin26_chosen,
	#mid_mtl_resin31_chosen,
	#mid_mtl_resin32_chosen,
	#mid_mtl_resin33_chosen,
	#mid_mtl_resin34_chosen{
		display: none;
	}

</style>
<script>


	$(document).ready(function(){
		$('.HideCost').hide();
		$('.Hide').hide();
		std();
		OL_Function();
		hitung_all();

		$(document).on('change', '#d1', function(e){
			// e.preventDefault();
			var id	= $(this).val();
			//console.log(id+"XXX");
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getDiameterNoWaste',
				cache: false,
				type: "POST",
				data: "id="+$(this).val(),
				dataType: "json",
				success: function(data){
					//console.log(data.pipeN);
					$('#top_type_1').val(data.pipeN);
					$('#diameter_1').val(data.pipeD);
					//console.log($('#top_type_1').val());
					// $('#waste').val(data.wasted);

				}
			});
		});
		$(document).on('change', '#d2', function(e){
			// e.preventDefault();
			var id	= $(this).val();
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getDiameterNoWaste',
				cache: false,
				type: "POST",
				data: "id="+$(this).val(),
				dataType: "json",
				success: function(data){
					$('#top_type_2').val(data.pipeN);
					$('#diameter_2').val(data.pipeD);
					// $('#waste').val(data.wasted);

				}
			});
		});

		$(document).on('click', '#calc', function(e){
			std();
			OL_Function();
			hitung_all();
		});

		$(document).on('click paste keyup change', '#pipe_thickness', function(e){
			var x = getNum($("#factor_thickness").val())*getNum($("#pipe_thickness").val());
			if (isNaN(x)) {
				x = 0;
			}
			$("#joint_thickness").val(x.toFixed(4));
		});
		
		$(document).on('click paste keyup change', '#factor_thickness', function(e){
			var x = getNum($("#factor_thickness").val())*getNum($("#pipe_thickness").val());
			if (isNaN(x)) {
				x = 0;
			}
			$("#joint_thickness").val(x.toFixed(4));
		});
		
		function joint_thickness_calc(){
			var x = getNum($("#factor_thickness").val())*getNum($("#pipe_thickness").val());
			if (isNaN(x)) {
				x = 0;
			}
			$("#joint_thickness").val(x.toFixed(4));
		}

		$(document).on('click paste keyup change', '#diameter', function(e){
			std();
			OL_Function();
			hitung_all();
		});

		//GET VEIL MATERIAL
		$(document).on('change', '#veil', function(e){

			var x = $("#veil").val();
			get_mat(x,'area weight','veil')
			//console.log(x);
			//$("#joint_thickness").val(x);
		});
		//GET WR MATERIAL
		$(document).on('change', '#wr', function(e){

			var x = $("#wr").val();
			get_mat(x,'area weight','wr')
			//console.log(x);
			//$("#joint_thickness").val(x);
		});
		//GET CSM MATERIAL
		$(document).on('change', '#csm', function(e){

			var x = $("#csm").val();
			get_mat(x,'area weight','csm')
			//console.log(x);
			//$("#joint_thickness").val(x);
		});
		//GET MATERIAL
		function get_mat(id, table, nama){
			if(id != ''){
        $.ajax({
          type:"GET",
          url:base_url+active_controller+"/get_detail_mat",
					data: {
				    id_material: id,
						nm_standard: table
				  },
          success:function(result){
            var data = JSON.parse(result);
            $('#'+nama+'_area_weight').val(data.nilai_standard);
						//$('#'+nama+'_thickness').val((getNum(data.nilai_standard)/1000/2.56)+(getNum(data.nilai_standard)/1000/1.2*getNum($('#veil_resin_content').val())));
						hitung_all();
          },error:function(){
						console.log(base_url+active_controller+"get_detail_mat");
					}
        });
      }
		}

		$(".numberOnly").on("keypress keyup blur",function (event) {
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
			// if($(this).val() == ''){
			// $(this).val(0);
			// }
		});

		//=====================================GLASS HEAD=============================================//
			//-----------KONSTANTA------------//
			function kg(){ // KONSTANTA GLASS
				var dia = $("#diameter_1").val();;
				/*if ($("#diameter_1").val() > $("#diameter_2").val()) {
					var dia = $("#diameter_1").val();
				}
				else {
					var dia = $("#diameter_2").val();
				}*/
				var D3 = getNum(dia);
				var D4 = getNum($("#minimum_width").val());
				var G3 = getNum($("#pipe_thickness").val());
				var G4 = getNum($("#joint_thickness").val());
				var G5 = getNum($("#factor_thickness").val());
				var J3 = getNum($("#factor").val());
				var F9 = getNum($("#veil_area_weight").val());
				var F10 = getNum($("#wr_area_weight").val());
				var F11 = getNum($("#csm_area_weight").val());
				var G9 = getNum($("#veil_resin_content").val());
				var G10 = getNum($("#wr_resin_content").val());
				var G11 = getNum($("#csm_resin_content").val());
				return {
				D3 : D3,
				D4 : D4,
				G3 : G3,
				G4 : G4,
				G5 : G5,
				J3 : J3,
				F9 : F9,
				F10 : F10,
				F11 : F11,
				G9 : G9,
				G10 : G10,
				G11 : G11}
			}
			//---------END KONSTANTA----------//

			function veil_thickness(){
				kg();
				var H9 = ((kg().F9/1000)/2.56)+(((kg().F9/1000)/1.2)*kg().G9);
				return H9.toFixed(4);
			}
			function wr_thickness(){
				kg();
				var H10 = (kg().F10/1000/2.56)+(kg().F10/1000/1.2*kg().G10);
				return H10.toFixed(4);
			}
			function csm_thickness(){
				kg();
				var H11 = (kg().F11/1000/2.56)+(kg().F11/1000/1.2*kg().G11);
				return H11.toFixed(4);
			}
		//=================================END GLASS HEAD=============================================//




		//=====================================RESIN & ADD=============================================//
			//-----------KONSTANTA------------//
			function ceilingxcl(a,b){
				return Math.ceil(a / b) * b;

			}
			function kons(){ //KONSTANTA RESIN & ADD
				var dia = $("#diameter_1").val();;
				
				var D3 = getNum(dia);
				var D4 = getNum($("#minimum_width").val());
				var G3 = getNum($("#pipe_thickness").val());
				var G4 = getNum($("#joint_thickness").val());
				var G5 = getNum($("#factor_thickness").val());
				var J3 = getNum($("#factor").val());
				var J9 = getNum($("#veil_material_weight").val());
				var G9 = getNum($("#veil_resin_content").val());
				var J10 = getNum($("#wr_material_weight").val());
				var G10 = getNum($("#wr_resin_content").val());
				var J11 = getNum($("#csm_material_weight").val());
				var G11 = getNum($("#csm_resin_content").val());
				var G14 = getNum($("#resin_percentage").val()/100);
				var G15 = getNum($("#catalys_percentage").val()/100);
				var G16 = getNum($("#dempul_percentage").val()/100);
				var G17 = getNum($("#carbosilbubuk_percentage").val()/100);
				var G18 = getNum($("#resincarbosil_percentage").val()/100);
				var G19 = getNum($("#resintopcoat_percentage").val()/100);
				var G20 = getNum($("#cobalt_percentage").val()/100);
				var G21 = getNum($("#pigment_percentage").val()/100);
				var G22 = getNum($("#tinuvin_percentage").val()/100);
				var G23 = getNum($("#chloroform_percentage").val()/100);
				var G24 = getNum($("#solutionwax_percentage").val()/100);
				var G25 = getNum($("#accelerator_percentage").val()/100);
				
				var JV1 = getNum($("#veil_material_weight_i").val());
                var JV2 = getNum($("#veil_material_weight_o").val());
                var JC1 = getNum($("#csm_material_weight_i").val());
                var JC2 = getNum($("#csm_material_weight_o").val());
                var JW1 = getNum($("#wr_material_weight_i").val());
                var JW2 = getNum($("#wr_material_weight_o").val());
				return {D3 : D3,
				D4 : D4,
				G3 : G3,
				G4 : G4,
				G5 : G5,
				J3 : J3,
				J9 : J9,
				G9 : G9,
				J10 : J10,
				G10 : G10,
				J11 : J11,
				G11 : G11,
				G14 : G14,
				G15 : G15,
				G16 : G16,
				G17 : G17,
				G18 : G18,
				G19 : G19,
				G20 : G20,
				G21 : G21,
				G22 : G22,
				G23 : G23,
				G24 : G24,
				G25 : G25,
				
				JV1 : JV1,
				JV2 : JV2,
                JC1 : JC1,
				JC2 : JC2,
                JW1 : JW1,
				JW2 : JW2}
			}
			function PI(){
				return 3.141593;
			}
			//---------END KONSTANTA----------//

			function resin_material_weight(){
				kons();
				return (kons().J9*kons().G9)+(kons().J10*kons().G10)+(kons().J11*kons().G11);
			}
			function resin_material_weight_i(){
				kons();
				return (kons().JV1*kons().G9)+(kons().JC1*kons().G10)+(kons().JW1*kons().G11);
			}
            function resin_material_weight_o(){
				kons();
				return (kons().JV2*kons().G9)+(kons().JC2*kons().G10)+(kons().JW2*kons().G11);
			}
			function catalys_material_weight(){
				kons();
				var J14 = resin_material_weight();
				return (kons().G15)*J14;
			}
			function dempul_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				return ( (kons().D3)*PI() * (8/9) ) * ( (Math.pow(kons().G3,2)) / Math.pow(10,6) * ( kons().G16 ) );
			}
			function carbosilbubuk_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				return (kons().G17)*J16;
			}
			function resincarbosil_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				return kons().G18*J16;
			}
			function resintopcoat_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				//console.log(ceilingxcl(kons().D4,50));
				return ((PI()*(kons().D3+2*kons().G3+2*kons().G4)*ceilingxcl(kons().D4,50)*1.2)/1000000)*0.5;
			}
			function cobalt_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				var J19 = resintopcoat_material_weight();
				return (J19+J17+J14)*kons().G20;
			}
			function pigment_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				var J19 = resintopcoat_material_weight();
				var J20 = cobalt_material_weight();
				return kons().G21*J19;
			}
			function tinuvin_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				var J19 = resintopcoat_material_weight();
				var J20 = cobalt_material_weight();
				var J21 = pigment_material_weight();
				return kons().G22*J19;
			}
			function chloroform_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				var J19 = resintopcoat_material_weight();
				var J20 = cobalt_material_weight();
				var J21 = pigment_material_weight();
				return kons().G23*J19;
			}
			function solutionwax_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				var J19 = resintopcoat_material_weight();
				var J20 = cobalt_material_weight();
				var J21 = pigment_material_weight();
				return kons().G24*J19;
			}

			function accelerator_material_weight(){
				kons();
				var J14 = resin_material_weight();
				var J15 = catalys_material_weight();
				var J16 = dempul_material_weight();
				var J17 = carbosilbubuk_material_weight();
				var J18 = resincarbosil_material_weight();
				var J19 = resintopcoat_material_weight();
				var J20 = cobalt_material_weight();
				var J21 = pigment_material_weight();
				return kons().G25*J19;
			}

		//==================================END RESIN & ADD=============================================//

//--------------------------------------------------------------------------------------------------------------------//

		//=====================================GET ALL COUNT============================================//
			function hitung_all(){
				//kg();
				//veil_thickness();
				//wr_thickness();
				//csm_thickness();
				$('#veil_thickness').val(veil_thickness());
				$('#wr_thickness').val(wr_thickness());
				$('#csm_thickness').val(csm_thickness());

				$('#resin_material_weight').val(resin_material_weight().toFixed(4));
				$('#catalys_material_weight').val(catalys_material_weight().toFixed(4));
				$('#dempul_material_weight').val(dempul_material_weight().toFixed(4));
				$('#carbosilbubuk_material_weight').val(carbosilbubuk_material_weight().toFixed(4));
				$('#resincarbosil_material_weight').val(resincarbosil_material_weight().toFixed(4));
				$('#resintopcoat_material_weight').val(resintopcoat_material_weight().toFixed(4));
				$('#cobalt_material_weight').val(cobalt_material_weight().toFixed(4));
				$('#pigment_material_weight').val(pigment_material_weight().toFixed(4));
				$('#tinuvin_material_weight').val(tinuvin_material_weight().toFixed(4));
				$('#chloroform_material_weight').val(chloroform_material_weight().toFixed(4));
				$('#solutionwax_material_weight').val(solutionwax_material_weight().toFixed(4));
				$('#accelerator_material_weight').val(accelerator_material_weight().toFixed(4));
				
				$('#resin_material_weight_in').val(resin_material_weight_i().toFixed(4));
                $('#resin_material_weight_out').val(resin_material_weight_o().toFixed(4));

			}
		//==================================END GET ALL COUNT===========================================//

//--------------------------------------------------------------------------------------------------------------------//

		//=====================================INSIDE LAMINATION========================================//
			//-----------STD------------//
			function std(){ // STD

				var jum = '<?= count($ILamination)?>';
				var w_il = 2;
				var dia = $("#diameter_1").val();;
				
				for (var i = parseInt(jum); i >= 1; i--) {
					//STD
					if ($('#std_gc_'+i).val() == 'RUMUS') {
						if (getNum(dia) < getNum($('#diameter_penentuan_'+i).val())) {
							$('#std_'+i).val('');
						}else {
							$('#std_'+i).val('VEIL');
						}
					}

					//WIDTH
					if ($('#std_'+jum).val() == '') {
						$('#width_'+jum).val('');
					}else {
						var c1 = ceilingxcl((0.5*ceilingxcl( $('#minimum_width').val(),50) ),50);
						$('#width_'+jum).val(c1);
					}
					$('#width_'+(jum-1)).val(c1);
					//PERHITUNGAN SELANJUTNYA KEATAS
					if (i <= (jum-2) && i > (jum-(2*2))) {
						if (getNum(dia) > 2150) {
							$('#width_'+i).val(c1-50);
						}
					}

					if (i <= (jum-(2*2))) {
						if (getNum(dia) > 1500) {
							if (getNum(dia) > 2150) {
								$('#width_'+i).val(c1-100);
							}else {
								$('#width_'+i).val(c1-50);
							}
						}
					}

					if (i <= (jum-(2*3))) {
						if (getNum(dia) > 1050) {
							if (getNum(dia) > 1500) {
								if (getNum(dia) > 2150) {
									$('#width_'+i).val(c1-150);
								}else {
									$('#width_'+i).val(c1-100);
								}
							}else {
								$('#width_'+i).val(c1-50);
							}
						}
					}

					if (i <= (jum-(2*4))) {
						if (getNum(dia) > 600) {
							if (getNum(dia) > 1050) {
								if (getNum(dia) > 1500) {
									if (getNum(dia) > 2150) {
										$('#width_'+i).val(c1-200);
									}else {
										$('#width_'+i).val(c1-150);
									}
								}else {
									$('#width_'+i).val(c1-100);
								}
							}else {
								$('#width_'+i).val(c1-50);
							}
						}
					}

					//GLASS CONFIGURATION
					if ($('#width_'+i).val() == '')  {
						$('#glassconfiguration_'+i).val('');
					}else {
						$('#glassconfiguration_'+i).val($('#std_'+i).val());
					}


					//THICKNESS 1
					if ($('#glassconfiguration_'+i).val() == "CSM") {
						$('#thickness1_'+i).val($('#csm_thickness').val());
					}else if ($('#glassconfiguration_'+i).val() == "VEIL") {
						$('#thickness1_'+i).val($('#veil_thickness').val());
					}else {
						$('#thickness1_'+i).val('');
					}

					//THICKNESS 2
					$('#thickness2_'+i).val($('#thickness1_'+i).val());

					//GLASS LENGTH
					$overlap = parseFloat($('#waste').val());
					if (getNum(dia) > 600) {
						var gl = parseFloat(PI()*getNum(dia)+100);
						$('#glasslength_'+i).val(gl + $overlap);
					}

				}
				//console.log(jum);
				//console.log(getNum(dia));
				//console.log(jum);
			}
			//---------END KONSTANTA----------//
		//=================================END INSIDE LAMINATION========================================//

//--------------------------------------------------------------------------------------------------------------------//

		//=====================================OUTSIDE LAMINATION=======================================//
			//-----------STD------------//
			
			function OL_Function(){ // STD
				kons();
				//KONSTANTA
				if (	kons().D3	<	600) {
					$('#o_std_1').val('VEIL');
					$('#o_glassconfiguration_1').val('VEIL');
					$('#o_thickness1_1').val(0);
				}else {
					$('#o_std_1').val('CSM');
					$('#o_glassconfiguration_1').val('CSM');
					$('#o_thickness1_1').val($('#csm_thickness').val());
				}
				//END KONSTANTA

				for (var i = 2; i <= <?=$no?>; i++) {
					var sum_thickness1 = 0;
					for (var j = 1; j < i; j++) {
						sum_thickness1 = sum_thickness1 + getNum($('#o_thickness1_'+j).val());
					}

					//GLASS CONFIGURATION
					if (sum_thickness1 < getNum($('#joint_thickness').val())) {
						if (i == 2) {
							//PERBAIKAN 2019-11-20
							var	o_glassconfiguration_ = $('#o_std_'+i).val();
							$('#o_glassconfiguration_'+i).val($('#o_std_'+i).val());
						}
						if (i != 1 && i >= 3) {
							//PERBAIKAN 2019-11-20
							var	o_glassconfiguration_ = $('#o_std_'+i).val();
							$('#o_glassconfiguration_'+i).val($('#o_std_'+i).val());
						}

						//THICKNESS 1
						if (o_glassconfiguration_ == 'CSM') {
							var o_thickness1_ = $('#csm_thickness').val()
							$('#o_thickness1_'+i).val(	$('#csm_thickness').val()	);
						}else if (o_glassconfiguration_ == 'WR') {
							var o_thickness1_ = $('#wr_thickness').val()
							$('#o_thickness1_'+i).val(	$('#wr_thickness').val()	);
						}

						//THICKNESS 2
						if (o_glassconfiguration_ == 'CSM') {
							var o_thickness2_ = $('#csm_thickness').val()
							$('#o_thickness2_'+i).val($('#csm_thickness').val());
						}else if (o_glassconfiguration_	==	'WR') {
							$('#o_thickness2_'+i).val($('#wr_thickness').val());
						}else if (o_glassconfiguration_ ==	'VEIL'	||	o_glassconfiguration_ == '') {
							$('#o_thickness2_'+i).val(0);
						}

					}else if (sum_thickness1 >	getNum($('#joint_thickness').val())) {
						//GLASS
						if (i != 1 && i >= 3) {
							if (	($('#o_glassconfiguration_'+(parseInt(i)-1)).val()	==	"CSM"	&&	$('#o_std_'+(parseInt(i)-2)).val()	==	"WR")	||
							($('#o_glassconfiguration_'+(parseInt(i)-1)).val()	==	"WR"	&&	$('#o_std_'+(parseInt(i)-2)).val()	==	"CSM")	) {
								var	o_glassconfiguration_ = 'CSM';
								$('#o_glassconfiguration_'+i).val('CSM');
							}else {
								var	o_glassconfiguration_ = '';
								$('#o_glassconfiguration_'+i).val('');
							}
						}

						//THICKNESS 2
						if (o_glassconfiguration_ == 'CSM') {
							var o_thickness2_ = $('#csm_thickness').val()
							$('#o_thickness2_'+i).val($('#csm_thickness').val());
						}else if (o_glassconfiguration_	==	'WR') {
							$('#o_thickness2_'+i).val($('#wr_thickness').val());
						}else if (o_glassconfiguration_ ==	'VEIL'	||	o_glassconfiguration_ == '') {
							$('#o_thickness2_'+i).val(0);
						}


					}else {
						$('#o_thickness1_'+i).val(0);
						$('#o_thickness2_'+i).val(0);
					}
				}

				$('#o_thickness2_1').val($('#o_thickness1_1').val());

				if (n != 1) {
					//$('#o_thickness2_'+n).val($('#o_thickness1_'+n).val());

				}

				var jum = 0;
				var max_width = 0;
				var D178 = 0;
				var max_glassconfiguration = 0;
				var F178 = 0;
				var max_thickness_1 = 0;
				var G178 = '';
				var max_thickness_2 = 0;
				var G179 = '';
				var no = 0;
				for (var s = 20; s >= 1; s--) {
					if (s == 1) {
						var x = parseInt('<?= count($OLamination_1)?>');
						var max_num = s*x;
					}else {
						var x = parseInt('<?= count($OLamination_2)?>');
						var max_num = (s*x)-1;
					}

					var n = max_num;
					for (var i = x; i >= 1; i--) {

						no++;

						//WIDTH

						if (i == x && s == 20) {
							if ( $('#o_glassconfiguration_'+n).val() == 'CSM') {
								var	width	=	ceilingxcl(kons().D4,50);
							}else {
								var width = 0;
							}
							$('#o_width_'+n).val(width);
						}else if (i == x && s != 20) {
							if 	(	($('#o_thickness1_'+parseInt(n+1)).val() == 0 && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
										($('#o_thickness1_'+parseInt(n+2)).val() == 0 && $('#o_glassconfiguration_'+parseInt(n+1)).val() == 'CSM')	||
										($('#o_thickness1_'+parseInt(n+1)).val() == null && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
										($('#o_thickness1_'+parseInt(n+2)).val() == null && $('#o_glassconfiguration_'+parseInt(n+1)).val() == 'CSM')
									) {
											var	width	=	ceilingxcl(kons().D4,50);
							}else if ( $('#o_width_'+parseInt(n+1)).val() == null ) {
								var width = 0;
							}else if (kons().D4 < 601 || (kons().D4 - (Math.floor(kons().G4/5.5)*100))<550) {
								var width = $('#o_width_'+parseInt(n+1)).val() - 50;
							}else {
								var width = $('#o_width_'+parseInt(n+1)).val() - 100;
							}
							$('#o_width_'+n).val(width);
						}else {
							if (n != 1 && s != 1) {
									if (i%2 == 0) {
										if 	(	(($('#o_thickness1_'+(parseInt(n)+1)).val() == null || $('#o_thickness1_'+(parseInt(n)+1)).val() == 0) && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
													(($('#o_thickness1_'+(parseInt(n)+2)).val() == null	|| $('#o_thickness1_'+(parseInt(n)+2)).val() == 0) && $('#o_glassconfiguration_'+(parseInt(n+1))).val() == 'CSM')
												) {
														var	width	=	ceilingxcl(kons().D4,50);
										}else if (($('#o_width_'+(parseInt(n)+1)).val() == null || $('#o_width_'+(parseInt(n)+1)).val() == 0) || getNum($('#o_width_'+(parseInt(n)+1)).val()) < 1) {
											var width = 0;
										}else {
											var width = getNum($('#o_width_'+(parseInt(n)+1)).val()) - 50;
										}
										$('#o_width_'+n).val(width);
									}else {
										if 	(	(($('#o_thickness1_'+(parseInt(n)+1)).val() == null || $('#o_thickness1_'+(parseInt(n)+1)).val() == 0) && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
													(($('#o_thickness1_'+(parseInt(n)+2)).val() == null	|| $('#o_thickness1_'+(parseInt(n)+2)).val() == 0) && $('#o_glassconfiguration_'+(parseInt(n+1))).val() == 'CSM')
												) {
														var	width	=	ceilingxcl(kons().D4,50);
										}else if (
															($('#o_width_'+(parseInt(n)+1)).val() == null || $('#o_width_'+(parseInt(n)+1)).val() == 0) ||
															getNum($('#o_width_'+(parseInt(n)+1)).val()) < 1) {
											var width = 0;
										}else {
											var width = getNum($('#o_width_'+(parseInt(n)+2)).val());
										}
										$('#o_width_'+n).val(width);
									}
							}else if (n != 1 && s == 1) {
									if (i%2 != 0) {
										if 	(	(($('#o_thickness1_'+(parseInt(n)+1)).val() == null || $('#o_thickness1_'+(parseInt(n)+1)).val() == 0) && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
													(($('#o_thickness1_'+(parseInt(n)+2)).val() == null	|| $('#o_thickness1_'+(parseInt(n)+2)).val() == 0) && $('#o_glassconfiguration_'+(parseInt(n+1))).val() == 'CSM')
												) {
														var	width	=	ceilingxcl(kons().D4,50);
										}else if (($('#o_width_'+(parseInt(n)+1)).val() == null || $('#o_width_'+(parseInt(n)+1)).val() == 0) || getNum($('#o_width_'+(parseInt(n)+1)).val()) < 1) {
											var width = 0;
										}else {
											var width = getNum($('#o_width_'+(parseInt(n)+1)).val()) - 50;
										}
										$('#o_width_'+n).val(width);
									}else {
										if 	(	(($('#o_thickness1_'+(parseInt(n)+1)).val() == null || $('#o_thickness1_'+(parseInt(n)+1)).val() == 0) && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
													(($('#o_thickness1_'+(parseInt(n)+2)).val() == null	|| $('#o_thickness1_'+(parseInt(n)+2)).val() == 0) && $('#o_glassconfiguration_'+(parseInt(n+1))).val() == 'CSM')
												) {
														var	width	=	ceilingxcl(kons().D4,50);
										}else if (
															($('#o_width_'+(parseInt(n)+1)).val() == null || $('#o_width_'+(parseInt(n)+1)).val() == 0) ||
															getNum($('#o_width_'+(parseInt(n)+1)).val()) < 1) {
											var width = 0;
										}else {
											var width = getNum($('#o_width_'+(parseInt(n)+2)).val());
										}
										$('#o_width_'+n).val(width);
									}
							}
						}

						//GL
						var waste = parseFloat($('#waste').val());
						if (i == 1) {
							if (($('#o_glassconfiguration_'+n).val() == '' || $('#o_glassconfiguration_'+n).val() == null)) {
								$('#o_glasslength_'+s).val('');
							}else {
								var sum_ = 0;
								for (var th = 1; th <= n; th++) {
									var sum_ = sum_ + getNum($('#o_thickness2_'+th).val());
								}
								if (getNum(kons().D3) < 300) {
									var nil = (getNum(kons().D3)	+	2*(getNum($('#pipe_thickness').val())	+	sum_	))*PI()+25;
									//$('#o_glasslength_'+s).val(nil);
								}else if (getNum(kons().D3) < 550) {
									var nil = (getNum(kons().D3)	+	2*(getNum($('#pipe_thickness').val())	+	sum_	))*PI()+50;
								}else if (getNum(kons().D3) < 800) {
									var nil = (getNum(kons().D3)	+	2*(getNum($('#pipe_thickness').val())	+	sum_	))*PI()+75;
								}else {
									var nil = (getNum(kons().D3)	+	2*(getNum($('#pipe_thickness').val())	+	sum_	))*PI()+100;
								}
								$('#o_glasslength_'+s).val(nil + waste);
							}
						}


						n--;

					}
				}
				$('#o_width_1').val($('#o_width_2').val());

				for (var p = 20; p >= 1; p--) {
					if (p == 1) {
						var xx = parseInt('<?= count($OLamination_1)?>');
						var max_num_2 = p*xx;
					}else {
						var xx = parseInt('<?= count($OLamination_2)?>');
						var max_num_2 = (p*xx)-1;
					}
					var q = max_num_2;

					for (var i = xx; i >= 1; i--) {
						//VEIL
						var gl = 0;
						for (var k = 1; k <= p; k++) {
							//gl.push(getNum($('#o_glasslength_'+k).val()));
							//gl[k-1] = getNum($('#o_glasslength_'+k).val());
							if (gl	<	getNum($('#o_glasslength_'+k).val())) {
								gl = getNum($('#o_glasslength_'+k).val());
							}
						}
						//q =
						if ($('#o_glassconfiguration_'+q).val()	==	"VEIL") {
							var vw = getNum($('#o_width_'+q).val())*gl*(getNum($('#veil_area_weight').val())/Math.pow(10,9)*2)

							$('#o_veil_weight_'+q).val(vw);
						}else {
							$('#o_veil_weight_'+q).val(0);
						}

						//CSM

						if ($('#o_glassconfiguration_'+q).val()	==	"CSM") {
							var cw = getNum($('#o_width_'+q).val())*gl*(getNum($('#csm_area_weight').val())/Math.pow(10,9))
							$('#o_csm_weight_'+q).val(cw);
						}else {
							$('#o_csm_weight_'+q).val(0);
						}

						//WR

						if ($('#o_glassconfiguration_'+q).val()	==	"WR") {
							var ww = getNum($('#o_width_'+q).val())*gl*(getNum($('#wr_area_weight').val())/Math.pow(10,9))
							$('#o_wr_weight_'+q).val(ww);
						}else {
							$('#o_wr_weight_'+q).val(0);
						}
						//console.log(gl);
						q--;
					}
				}

				//VEIL
				var gl = 0;
				if (getNum($('#glasslength_1').val())	<	getNum($('#o_glasslength_1').val())) {
					gl = getNum($('#o_glasslength_1').val());
				}else {
					gl = getNum($('#glasslength_1').val())
				}
				for (var q = 10; q >= 1; q--) {
					if ($('#glassconfiguration_'+q).val()	==	"VEIL") {
						var vw = getNum($('#width_'+q).val())*gl*(getNum($('#veil_area_weight').val())/Math.pow(10,9)*2)
						// console.log(getNum($('#width_'+q)));
						// console.log(gl);
						// console.log(getNum($('#veil_area_weight').val()));
						$('#veil_weight_'+q).val(vw);
					}else {
						$('#veil_weight_'+q).val(0);
					}

					//CSM

					if ($('#glassconfiguration_'+q).val()	==	"CSM") {
						var cw = getNum($('#width_'+q).val())*gl*(getNum($('#csm_area_weight').val())/Math.pow(10,9))
						$('#csm_weight_'+q).val(cw);
					}else {
						$('#csm_weight_'+q).val(0);
					}

					//WR

					if ($('#glassconfiguration_'+q).val()	==	"WR") {
						var ww = getNum($('#width_'+q).val())*gl*(getNum($('#wr_area_weight').val())/Math.pow(10,9))
						$('#wr_weight_'+q).val(ww);
					}else {
						$('#wr_weight_'+q).val(0);
					}
				}

				//VEIL MATERIAL WEIGHT
				var juml_veil = 0;
				var juml_csm = 0;
				var juml_wr = 0;
				var il = '<?=count($ILamination)?>';
				var ol = '<?=$no?>';
				
				var juml_veil_i = 0;
				var juml_csm_i = 0;
				var juml_wr_i = 0;

                var juml_veil_o = 0;
				var juml_csm_o = 0;
				var juml_wr_o = 0;
				// console.log(il);
				for (var i = 1; i <= il; i++) {
					juml_veil = juml_veil+getNum($('#veil_weight_'+i).val());
					juml_csm = juml_csm+getNum($('#csm_weight_'+i).val());
					juml_wr = juml_wr+getNum($('#wr_weight_'+i).val());
					
					juml_veil_i = juml_veil+getNum($('#veil_weight_'+i).val());
					juml_csm_i = juml_csm+getNum($('#csm_weight_'+i).val());
					juml_wr_i = juml_wr+getNum($('#wr_weight_'+i).val());
				}
				for (var j = 1; j <= ol; j++) {
					juml_veil = juml_veil+getNum($('#o_veil_weight_'+j).val());
					juml_csm = juml_csm+getNum($('#o_csm_weight_'+j).val());
					juml_wr = juml_wr+getNum($('#o_wr_weight_'+j).val());
					
					juml_veil_o = juml_veil+getNum($('#o_veil_weight_'+j).val());
					juml_csm_o = juml_csm+getNum($('#o_csm_weight_'+j).val());
					juml_wr_o = juml_wr+getNum($('#o_wr_weight_'+j).val());
					// console.log(juml_veil);
				}
				// console.log(juml_veil);
				var veil_wg = juml_veil*parseFloat($('#factor').val());
				var csm_wg 	= juml_csm*parseFloat($('#factor').val());
				var wr_wg 	= juml_wr*parseFloat($('#factor').val());
				
				var veil_wg_i   = juml_veil_i * parseFloat($('#factor').val());
				var csm_wg_i 	= juml_csm_i * parseFloat($('#factor').val());
				var wr_wg_i 	= juml_wr_i * parseFloat($('#factor').val());

                var veil_wg_o   = juml_veil_o * parseFloat($('#factor').val());
				var csm_wg_o 	= juml_csm_o * parseFloat($('#factor').val());
				var wr_wg_o 	= juml_wr_o * parseFloat($('#factor').val());
				
				$('#veil_material_weight').val(veil_wg.toFixed(4));
				$('#csm_material_weight').val(csm_wg.toFixed(4));
				$('#wr_material_weight').val(wr_wg.toFixed(4));
				
				$('#veil_material_weight_i').val(veil_wg_i.toFixed(4));
				$('#csm_material_weight_i').val(csm_wg_i.toFixed(4));
				$('#wr_material_weight_i').val(wr_wg_i.toFixed(4));

                $('#veil_material_weight_o').val(veil_wg_o.toFixed(4));
				$('#csm_material_weight_o').val(csm_wg_o.toFixed(4));
				$('#wr_material_weight_o').val(wr_wg_o.toFixed(4));
			}


			//---------END KONSTANTA----------//
		//=================================END OUTSIDE LAMINATION=======================================//
	});
$(document).on('click', '#simpan-bro', function(e){
	if($('#series').val() == null || $('#series').val() == '' || $('#series').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Series is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#d1').val() == null || $('#d1').val() == '' || $('#d1').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Diameter is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#top_toleran').val() == null || $('#top_toleran').val() == '' || $('#top_toleran').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Tolerance is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#minimum_width').val() == null || $('#minimum_width').val() == '' || $('#minimum_width').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Minimum Width is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#pipe_thickness').val() == null || $('#pipe_thickness').val() == '' || $('#pipe_thickness').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Pipe Thickness is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#waste').val() == null || $('#waste').val() == '' || $('#waste').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Overlap is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#veil').val() == null || $('#veil').val() == '' || $('#veil').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'Veil Value is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#wr').val() == null || $('#wr').val() == '' || $('#wr').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'WR is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#csm').val() == null || $('#csm').val() == '' || $('#csm').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'CSM is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0001').val() == null || $('#TYP-0001').val() == '' || $('#TYP-0001').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'RESIN is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0002').val() == null || $('#TYP-0002').val() == '' || $('#TYP-0002').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'CATALYS is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0019').val() == null || $('#TYP-0019').val() == '' || $('#TYP-0019').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'DEMPUL is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-001519').val() == null || $('#TYP-001519').val() == '' || $('#TYP-001519').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'CARBOSIL BUBUK is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0001').val() == null || $('#TYP-0001').val() == '' || $('#TYP-0001').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'RESIN CARBOSIL is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0001').val() == null || $('#TYP-0001').val() == '' || $('#TYP-0001').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'RESIN TOPCOAT is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0021').val() == null || $('#TYP-0021').val() == '' || $('#TYP-0021').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'COBALT is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0007').val() == null || $('#TYP-0007').val() == '' || $('#TYP-0007').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'PIGMENT is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0019').val() == null || $('#TYP-0019').val() == '' || $('#TYP-0019').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'TINUVIN is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0019').val() == null || $('#TYP-0019').val() == '' || $('#TYP-0019').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'CHLOROFORM is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
		return false;
	}
	if($('#TYP-0008').val() == null || $('#TYP-0008').val() == '' || $('#TYP-0008').val() == 0){
		swal({
			title	: "Error Message!",
			text	: 'SOLUTION WAX is Empty, please input first ...',
			type	: "warning"
		});
		//$('#simpan-bro').prop('disabled',false);
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
				var baseurl		= base_url + active_controller +'/fieldjoint';
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
							window.location.href = base_url + active_controller;
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

var nomor	= 1;
// $('#add_glass').click(function(e){
	// e.preventDefault();
	// AppendBaris_Glass(nomor);
	
	// var nilaiAwal	= parseInt($("#numberMax_glass").val());
	// var nilaiAkhir	= nilaiAwal + 1;
	// $("#numberMax_glass").val(nilaiAkhir);
// });

$('#add_resin').click(function(e){
	e.preventDefault();
	AppendBaris_Resin(nomor);
	
	var nilaiAwal	= parseInt($("#numberMax_resin").val());
	var nilaiAkhir	= nilaiAwal + 1;
	$("#numberMax_resin").val(nilaiAkhir);
});

// function AppendBaris_Glass(intd){
	// var nomor	= 1;
	// var valuex	= $('#detail_body_glass').find('tr').length;
	// if(valuex > 0){
		// var akhir	= $('#detail_body_glass tr:last').attr('id');
		// var det_id	= akhir.split('_');
		// var nomor	= parseInt(det_id[1])+1;
	// }

	// var Rows	 = "<tr id='trtopcoat_"+nomor+"'>";
		// Rows	+= 	"<td align='left'  width='10%'>";
		// Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn btn-sm btn-danger' style='width:100%; margin-bottom: 5px;' data-toggle='tooltip' data-placement='bottom' onClick='delRow_TopCoat("+nomor+")' title='Delete Record'>Delete</button></div>";
		// Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_TopCoat["+nomor+"][last_full]' id='last_full_topcoat_"+nomor+"' value='0' autocomplete='off'>";
		// Rows	+=		"<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		// Rows	+= 	"</td>";
		// Rows	+= 	"<td align='left' style='vertical-align:bottom;'>";
		// Rows	+=		"<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		// Rows	+= 	"</td>";
		// Rows	+= 	"<td width='16%' style='vertical-align:bottom;'>";
		// Rows	+=		"<input type='text' class='form-control input-sm numberOnly' id='weight_glass_"+nomor+"' name='ListDetailAdd_TopCoat["+nomor+"][containing]' id='containing_topcoat_"+nomor+"' placeholder='Area Weight' readonly>";
		// Rows	+= 	"</td>";
		// Rows	+= 	"<td width='16%' style='vertical-align:bottom;'>";
		// Rows	+=		"<input type='text' class='form-control input-sm numberOnly resin_change' data-nomor='"+nomor+"' name='ListDetailAdd_TopCoat["+nomor+"][perse]' id='resin_glass_"+nomor+"'>";
		// Rows	+= 	"</td>";
		// Rows	+= 	"<td width='16%' style='vertical-align:bottom;'>";
		// Rows	+=		"<input type='text' class='form-control input-sm Cost' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' id='thickness_glass_"+nomor+"' placeholder='Thickness' readonly>";
		// Rows	+= 	"</td>";
		// Rows	+= 	"<td width='16%' style='vertical-align:bottom;'>";
		// Rows	+=		"<input type='text' class='form-control input-sm Cost' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' id='last_glass_"+nomor+"' placeholder='Material Weight' readonly>";
		// Rows	+= 	"</td>";
		// Rows	+= "</tr>";

	// $('#detail_body_glass').append(Rows);
	// var id_category_topcoat_ 	= "#id_category_topcoat_"+nomor;
	// var id_material_topcoat_ 	= "#id_material_topcoat_"+nomor;
	// var weight_glass 			= "#weight_glass_"+nomor;
	
	
	
	// $.ajax({
		// url: base_url +'index.php/'+active_controller+'/getCategoryJoint',
		// cache: false,
		// type: "POST",
		// dataType: "json",
		// success: function(data){
			// $(id_category_topcoat_).html(data.option).trigger("chosen:updated");
		// }
	// });
	
	// $(".numberOnly").on("keypress keyup blur",function (event) {
		// if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			// event.preventDefault();
		// }
	// });
	
	// $("#id_category_topcoat_"+nomor+"").on('change', function(e){
		// e.preventDefault();
		// $.ajax({
			// url: base_url +'index.php/'+active_controller+'/getMaterial',
			// cache: false,
			// type: "POST",
			// data: "id_category="+$(this).val(),
			// dataType: "json",
			// success: function(data){
				// $(id_material_topcoat_).html(data.option).trigger("chosen:updated");
			// }
		// });
	// });
	
	// $(id_material_topcoat_).on('change', function(e){
		// e.preventDefault();
		// var table = "area weight";
		// $.ajax({
			// url		: base_url+active_controller+"/get_detail_mat",
			// cache	: false,
			// type	: "GET",
			// data	: {
						// id_material: $(this).val(),
						// nm_standard: table
					  // },
			// dataType: "json",
			// success	:function(data){
				// $(weight_glass).val(data.nilai_standard);
			 // }
		// });
	// });
	
	// nomor++;
// }

function AppendBaris_Resin(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_resin').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_resin tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trresin_"+nomor+"'>";
		Rows	+= 	"<td align='left'  width='20%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' style='min-width:100px; margin-bottom: 5px;' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Resin("+nomor+")' title='Delete Record'>Delete</button></div>";
		Rows	+=		"<select name='ListAdd_Resin["+nomor+"][id_category]' id='id_category_resin_"+nomor+"' class='form-control input-md chosen-select' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left' style='vertical-align:bottom;'>";
		Rows	+=		"<select name='ListAdd_Resin["+nomor+"][id_material]' id='id_material_resin_"+nomor+"' class='form-control input-md chosen-select' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='25%' style='vertical-align:bottom;'>";
		Rows	+=		"<input type='text' style='text-align:center;' class='form-control input-sm numberOnly persen_resin' data-nomor='"+nomor+"' name='ListAdd_Resin["+nomor+"][perse]' id='resin_resin_"+nomor+"'>";
		Rows	+=		"<input type='hidden' name='ListAdd_Resin["+nomor+"][detail_name]' value='RESIN AND ADD'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='25%' style='vertical-align:bottom;'>";
		Rows	+=		"<input type='text' style='text-align:right;' class='form-control input-sm' name='ListAdd_Resin["+nomor+"][last_cost]' id='last_resin_"+nomor+"' readonly>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_resin').append(Rows);
	var id_category_resin 	= "#id_category_resin_"+nomor;
	var id_material_resin 	= "#id_material_resin_"+nomor;
	
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_resin).html(data.option).trigger("chosen:updated");
		}
	});
	
	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
	
	$("#id_category_resin_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterial',
			cache: false,
			type: "POST",
			data: "id_category="+$(this).val(),
			dataType: "json",
			success: function(data){
				$(id_material_resin).html(data.option).trigger("chosen:updated");
			}
		});
	});
	
	nomor++;
}

// function delRow_TopCoat(row){
	// $('#trtopcoat_'+row).remove();
	// // row = 0;
	// var updatemax	=	$("#numberMax_glass").val() - 1;
	// $("#numberMax_glass").val(updatemax);
	
	// var maxLine = $("#numberMax_glass").val();
// }

function delRow_Resin(row){
	$('#trresin_'+row).remove();
	// row = 0;
	var updatemax	=	$("#numberMax_resin").val() - 1;
	$("#numberMax_resin").val(updatemax);
	
	var maxLine = $("#numberMax_resin").val();
}

$(document).on('keyup','.resin_change', function(){
	var nomor 	= $(this).data('nomor');
	var weight = veil_thickness(nomor);
	$('#thickness_glass_'+nomor).val(weight);
});

$(document).on('keyup','.persen_resin', function(){
	var nomor 	= $(this).data('nomor');
	var persen 	= getNum($(this).val()) / 100;
	// var resin	= getNum($("#resin_material_weight").val());
	var resin1	= getNum($("#resin_material_weight_in").val());
	var resin2	= getNum($("#resin_material_weight_out").val());
	var weight 	= persen * (resin1 + resin2);
	$('#last_resin_'+nomor).val(getF4(weight));
});

function veil_thickness(nomor){
	var weight 		= $("#weight_glass_"+nomor).val();
	var resin 		= $("#resin_glass_"+nomor).val();
	var thickness 	= ((weight/1000)/2.56)+(((weight/1000)/1.2)*resin);
	return thickness.toFixed(4);
}

function getNum(val) {
   if (isNaN(val) || val == '') {
     return 0;
   }
   return parseFloat(val);
}
function getF4(val) {
   return val.toFixed(4);
}



</script>
