<?php
$id_bq				= $this->uri->segment(3); 
$id_milik			= $this->uri->segment(4);
$id_url				= $this->uri->segment(2);

$header				= $this->db->query("SELECT a.*, b.id FROM bq_component_header a LEFT JOIN product b ON a.nm_product = b.nm_product WHERE a.id_bq='".$id_bq."' AND a.id_milik='".$id_milik."' LIMIT 1 ")->result();
$series				= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
$product			= $this->db->query("SELECT * FROM product WHERE parent_product='shop joint' AND deleted='N'")->result_array();
$component_header	= $this->db->query("SELECT * FROM bq_component_header WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' ")->row();
$component_add		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'")->result_array();

$detGlass			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='GLASS' ")->result_array();
$detResinAdd		= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' ")->result_array();
$detInside			= $this->db->query("SELECT * FROM bq_component_lamination WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='Inside Lamination' ")->result_array();
$detOutside			= $this->db->query("SELECT * FROM bq_component_lamination WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='Outside Lamination' ")->result_array();
$ILamination	= $this->db->query("SELECT * FROM raw_material_lamination WHERE kategori='INSIDE LAMINATION'")->result_array();
			
history('View edit product estimasi shop joint : '.$id_bq.' / '.$id_milik.' / '.$header[0]->id_product); 

?>
<input type='hidden' id='NumdetInside' value='<?= count($detInside);?>' >
<input type='hidden' id='NumdetOutside' value='<?= count($detOutside);?>' >
<div class="box box-primary">
	<div class="box-body">
		<?php
			echo "&nbsp;<button type='button' name='simpan-bro-joint' style='min-width:100px; float:right; margin-right:10px;' id='simpan-bro-joint' class='btn btn-sm btn-primary'>Save</button>";
		?>
		&nbsp;<a class="btn btn-sm btn-success" id="calc" style="margin-right:10px; float:right; min-width:100px;">Calculation</a>
		<div class='headerTitleGroup'>GROUP COMPONENT</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm Hide','readonly'=>'readonly'),$header[0]->nm_product);
					echo form_input(array('id'=>'diameter_1','name'=>'diameter_1','class'=>'Hide'),$header[0]->diameter);
					echo form_input(array('id'=>'diameter_2','name'=>'diameter_2','class'=>'Hide'),$header[0]->diameter2);
					echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'Hide'),$header[0]->id_product);
					echo form_input(array('id'=>'id_bq','name'=>'id_bq','class'=>'Hide'),$id_bq);
					echo form_input(array('id'=>'id_milik','name'=>'id_milik','class'=>'Hide'),$id_milik);
					echo form_input(array('id'=>'series','name'=>'series','class'=>'Hide'),$header[0]->series);
					echo form_input(array('id'=>'rev','name'=>'rev','class'=>'Hide'),$header[0]->rev);
					echo form_input(array('id'=>'status','name'=>'status','class'=>'Hide'),$header[0]->status);
					echo form_input(array('id'=>'sts_price','name'=>'sts_price','class'=>'Hide'),$header[0]->sts_price);
					echo form_input(array('id'=>'toleransi','name'=>'toleransi','class'=>'Hide'),$header[0]->standart_by);
					echo form_input(array('id'=>'url_help','name'=>'url_help','class'=>'Hide'),$this->uri->segment(5)); 
					echo form_input(array('id'=>'penanda','name'=>'penanda','class'=>'Hide')); 
					echo form_input(array('id'=>'help_url','name'=>'help_url','class'=>'Hide'), $id_url); 
					echo form_input(array('id'=>'top_toleran','name'=>'top_toleran','class'=>'form-control input-sm Hide','readonly'=>'readonly'),$header[0]->standart_by);
					echo form_input(array('id'=>'standart_code','name'=>'standart_code','class'=>'form-control input-sm Hide','autocomplete'=>'off','readonly'=>'readonly'), $header[0]->standart_code);		
					echo form_input(array('id'=>'standart_code2','name'=>'standart_code2','class'=>'form-control input-sm Hide','autocomplete'=>'off'));						
					echo form_input(array('id'=>'top_type_1','name'=>'top_type_1','class'=>'form-control input-sm Hide','value'=>$component_header->nm_product));		
				?>	
				<select name='top_typeList' id='top_typeList' class='form-control input-sm chosen-select' disabled>
					<option value='0'>Select Diameter</option>
					<?php
						foreach($product AS $val => $valx){
							$selx	= ($header[0]->id == $valx['id'])?'selected':'';
							echo "<option value='".$valx['id']."' ".$selx.">".ucfirst(strtolower($valx['nm_product']))."</option>";
						}
					 ?>
				</select>
			</div>
			<label class='label-control col-sm-2'><b>Series <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<select name='seriesX' id='seriesX' class='form-control input-sm chosen-select' disabled>
				<?php
					foreach($series AS $val => $valx){
						$selx	= ($header[0]->series == $valx['kode_group'])?'selected':'';
						echo "<option value='".$valx['kode_group']."' ".$selx.">".strtoupper($valx['kode_group'])."</option>";
					}
				 ?>
				</select>
			</div>
		</div>
		<div class='headerTitleGroup'>SPESIFIKASI COMPONENT</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Minimum Width <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<?php
					echo form_input(array('id'=>'minimum_width','name'=>'minimum_width','class'=>'form-control input-sm','autocomplete'=>'off'), $header[0]->panjang);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Faktor</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'factor','name'=>'factor','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly'), 1);
				?>
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Pipe Thickness <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<?php
					echo form_input(array('id'=>'pipe_thickness','name'=>'pipe_thickness','class'=>'form-control input-sm','autocomplete'=>'off'), floatval($header[0]->pipe_thickness));
				?>
			</div>
			<label class='label-control col-sm-2'><b>Faktor Thickness <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'factor_thickness','name'=>'factor_thickness','class'=>'form-control input-sm','autocomplete'=>'off'), floatval($header[0]->factor_thickness));
				?>
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Joint Thickness<span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>           
				<?php
					echo form_input(array('id'=>'joint_thickness','name'=>'joint_thickness','class'=>'form-control input-sm','autocomplete'=>'off'), floatval($header[0]->joint_thickness));
					echo form_input(array('type'=>'text','id'=>'area','name'=>'area','class'=>'HideCost'), $header[0]->area);
					echo form_input(array('type'=>'text','id'=>'parent_product','name'=>'parent_product','class'=>'HideCost'), $header[0]->parent_product);
					echo form_input(array('type'=>'text','id'=>'standart_code','name'=>'standart_code','class'=>'HideCost'), $header[0]->standart_code);
				?>	
			</div>
			<label class='label-control col-sm-2'><b>Overlap<span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>           
				<?php
					echo form_input(array('id'=>'waste','name'=>'waste','class'=>'form-control input-sm','autocomplete'=>'off'), floatval($header[0]->waste));
				?>	
			</div>
		</div>
		<!-- ====================================================================================================== -->
		<!-- ============================================GLASS THICKNESS=========================================== -->
		<!-- ====================================================================================================== -->
		<div class='headerTitle'>GLASS</div>
		<input type='text' name='detail_name' id='detail_name' class='HideCost' value='GLASS'>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<head>
						<tr class='bg-blue'>
							<th class="text-center" width='15%'>Type</th>
							<th class="text-center">Material</th>
							<th class="text-center" width='10%'>Weight</th>
							<th class="text-center" width='10%'>Resin Content</th>
							<th class="text-center" width='10%'>Thickness</th>
							<th class="text-center" width='10%'>Last Weight</th>
						</tr>
					</head>
					<tbody>
					<tr>
						<td>VEIL</td>
						<td>
							<?php
								$getV = $this->db->get_where('bq_component_detail',array('detail_name'=>'GLASS','id_category'=>'TYP-0003','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								echo $this->master_model->get_select('veil',$getV->id_material);
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'veil_area_weight','name'=>'glass[area_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Area Weight','readonly'=>'readonly'),floatval($getV->area_weight));
							?>
						</td>
						<td>
							<?php
								$veil_res = 9/1;
								echo form_input(array('id'=>'veil_resin_content','name'=>'glass[resin_content][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Content','readonly'=>'readonly','value'=>$getV->resin_content));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'veil_thickness','name'=>'glass[thickness][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness','readonly'=>'readonly','value'=>$getV->thickness));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'veil_material_weight','name'=>'glass[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Material Weight','readonly'=>'readonly','value'=>$getV->material_weight));
							?>
						</td>
					</tr>
					<tr>
						<td>WR</td>
						<td>
							<?php
								$getW = $this->db->get_where('bq_component_detail',array('detail_name'=>'GLASS','id_category'=>'TYP-0006','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								echo $this->master_model->get_select('wr',$getW->id_material);
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'wr_area_weight','name'=>'glass[area_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Area Weight','readonly'=>'readonly','value'=>floatval($getW->area_weight)));
							?>
						</td>
						<td>
							<?php
								$wr_res = 45/55;
								echo form_input(array('id'=>'wr_resin_content','name'=>'glass[resin_content][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Content','readonly'=>'readonly','value'=>$getW->resin_content));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'wr_thickness','name'=>'glass[thickness][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness','readonly'=>'readonly','value'=>$getW->thickness));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'wr_material_weight','name'=>'glass[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Material Weight','readonly'=>'readonly','value'=>$getW->material_weight));
							?>
						</td>
					</tr>
					<tr>
						<td>CSM</td>
						<td>
							<?php
								$getC = $this->db->get_where('bq_component_detail',array('detail_name'=>'GLASS','id_category'=>'TYP-0004','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								echo $this->master_model->get_select('csm',$getC->id_material);
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'csm_area_weight','name'=>'glass[area_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Area Weight','readonly'=>'readonly','value'=>floatval($getC->area_weight)));
							?>
						</td>
						<td>
							<?php
								$csm_res = 70/30;
								echo form_input(array('id'=>'csm_resin_content','name'=>'glass[resin_content][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Content','readonly'=>'readonly','value'=>$getC->resin_content));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'csm_thickness','name'=>'glass[thickness][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness','readonly'=>'readonly','value'=>$getC->thickness));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'csm_material_weight','name'=>'glass[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Material Weight','readonly'=>'readonly','value'=>$getC->material_weight));
							?>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
		
		<div class='headerTitle'>RESIN AND ADD</div>
		<input type='text' name='detail_name' id='detail_name' class='HideCost' value='RESIN AND ADD'>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<head>
						<tr class='bg-blue'>
							<th class="text-center" width='15%'>Type</th>
							<th class="text-center">Material</th>
							<th class="text-center" width='10%'>Percent</th>
							<th class="text-center" width='10%'>Last Weight</th>
						</tr>
					</head>
					<tbody>
					<tr>
						<td>RESIN</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'RESIN','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'resin_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'resin_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>CATALYS</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'CATALYS','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0002','resinnadd[id_material][]','CATALYS',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0002','resinnadd[id_material][]','CATALYS');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'catalys_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'catalys_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>DEMPUL</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'DEMPUL','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','DEMPUL',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','DEMPUL');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'dempul_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'dempul_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>CARBOSIL BUBUK</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'CARBOSIL BUBUK','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-001519','resinnadd[id_material][]','CARBOSIL BUBUK',$getM->id_material);
								}
								else {
									echo $this->master_model->get_select_detail('TYP-001519','resinnadd[id_material][]','CARBOSIL BUBUK');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'carbosilbubuk_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'carbosilbubuk_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>RESIN CARBOSIL</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'RESIN CARBOSIL','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN CARBOSIL',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN CARBOSIL');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'resincarbosil_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'resincarbosil_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>RESIN TOPCOAT</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'RESIN TOPCOAT','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN TOPCOAT',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0001','resinnadd[id_material][]','RESIN TOPCOAT');
								}
								?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'resintopcoat_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'resintopcoat_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>COBALT</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'COBALT','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0021','resinnadd[id_material][]','COBALT',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0021','resinnadd[id_material][]','COBALT');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'cobalt_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'cobalt_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>PIGMENT</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'PIGMENT','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0007','resinnadd[id_material][]','PIGMENT',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0007','resinnadd[id_material][]','PIGMENT');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'pigment_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'pigment_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>TINUVIN</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'TINUVIN','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','TINUVIN',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','TINUVIN');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'tinuvin_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'tinuvin_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','value'=>'0','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>CHLOROFORM</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'CHLOROFORM','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','CHLOROFORM',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0019','resinnadd[id_material][]','CHLOROFORM');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'chloroform_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'chloroform_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<tr>
						<td>SOLUTION WAX</td>
						<td>
							<?php
								$getM = $this->db->get_where('bq_component_detail',array('detail_name'=>'RESIN AND ADD','nm_category'=>'SOLUTION WAX','id_milik'=>$id_milik,'id_bq'=>$id_bq))->row();
								if (isset($getM->id_material) || !empty($getM->id_material)) {
									echo $this->master_model->get_select_detail('TYP-0008','resinnadd[id_material][]','SOLUTION WAX',$getM->id_material);
								}else {
									echo $this->master_model->get_select_detail('TYP-0008','resinnadd[id_material][]','SOLUTION WAX');
								}
							 ?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'solutionwax_percentage','name'=>'resinnadd[percentage][]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight'),floatval($getM->percentage));
							?>
						</td>
						<td>
							<?php
								echo form_input(array('id'=>'solutionwax_material_weight','name'=>'resinnadd[material_weight][]','class'=>'form-control input-sm numberOnly','placeholder'=>'Resin Material Weight','readonly'=>'readonly'));
							?>
						</td>
					</tr>
					<?php
						$a=0;
						foreach($component_add AS $val => $valx){
							$a++;
							echo "<tr>";
								echo "<td>".$valx['nm_category']."</td>";
								echo "<td>";
									echo $this->master_model->get_select_detail($valx['id_category'],'ResinAdd['.$a.'][id_material]',$valx['nm_category'],$valx['id_material']);
									echo form_input(array('type'=>'hidden','id'=>'id_category_'.$a.'','name'=>'ResinAdd['.$a.'][id_category]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','readonly'=>'readonly'), $valx['id_category']);
									echo form_input(array('type'=>'hidden','id'=>'detail_name_'.$a.'','name'=>'ResinAdd['.$a.'][detail_name]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','readonly'=>'readonly'), $valx['detail_name']);
								echo "</td>";
								echo "<td>".form_input(array('id'=>'perse','name'=>'ResinAdd['.$a.'][perse]','style'=>'text-align:center','class'=>'form-control input-sm numberOnly persen_resinadd', 'data-nomor'=>''.$a.'' ), floatval($valx['perse']))."</td>";
								echo "<td>".form_input(array('id'=>'last_cost_'.$a.'','name'=>'ResinAdd['.$a.'][last_cost]','style'=>'text-align:right','class'=>'form-control input-sm numberOnly','readonly'=>'readonly'), $valx['last_cost'])."</td>";
							echo "</tr>";
						}
					?>
				</tbody>
				</table>
			</div>
		</div>
		<div class='headerTitle'>INSIDE LAMINATION</div>
		<input type='text' name='detail_name' id='detail_name' class='HideCost' value='Inside Lamination'>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<head>
						<tr class='bg-blue'>
							<th class="text-center" width='5%'>Lapisan</th>
							<th class="text-center" width='10%'>Std Glass Conf</th>
							<th class="text-center" width='10%'>Width</th>
							<th class="text-center" width='5%'>Stage</th>
							<th class="text-center" width='10%'>Glass Conf</th>
							<th class="text-center" width='10%'>Thickness</th>
							<th class="text-center" width='10%'></th>
							<th class="text-center" width='10%'>Glass Length</th>
							<th class="text-center" width='10%'>Weight VEIL</th>
							<th class="text-center" width='10%'>Weight CSM</th>
							<th class="text-center" width='10%'>Weight WR</th>
						</tr>
					</head>
					
					<tbody>
					<?php $no = 0; ?>
					<?php foreach ($ILamination as $key => $v) { $no++?>
									<tr id="IL_<?=$no;?>">
										<input type="hidden" id="diameter_penentuan_<?=$no?>" name="diameter_penentuan_<?=$no?>" value="<?=$v['diameter_penentuan']?>">
										<input type="hidden" id="std_gc_<?=$no?>" name="std_gc_<?=$no?>" value="<?=$v['type']?>">

										<td >
											<input class="form-control input-sm  numberOnlyCent" type="text" id="lapisan_<?=$no?>" name="lapisan_<?=$no?>" value="<?=$v['lapisan']?>" readonly>
										</td>

										<td >
											<?php	if ($v['type'] == "RUMUS") {?>
												<input class="form-control input-sm numberOnlyCent" type="text" id="std_<?=$no?>" name="std_<?=$no?>" value="" readonly>
											<?php	}else {	?>
												<input class="form-control input-sm numberOnlyCent" type="text" id="std_<?=$no?>" name="std_<?=$no?>" value="<?=$v['type']?>" readonly>
											<?php	}	?>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="width_<?=$no?>" name="width_<?=$no?>" value="" readonly>
										</td>

										<?php	if ($no == 1) {?>
											<td  style="text-align:center;vertical-align:middle" rowspan="<?=count($ILamination);?>">
												<input class="form-control input-sm numberOnlyCent" type="text" id="stage_<?=$no?>" name="stage_<?=$no?>" value="<?=$v['stage_ke']?>" readonly>
											</td>
										<?php	}	?>

										<td >
											<input class="form-control input-sm numberOnlyCent" type="text" id="glassconfiguration_<?=$no?>" name="glassconfiguration_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="thickness1_<?=$no?>" name="thickness1_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="thickness2_<?=$no?>" name="thickness2_<?=$no?>" value="" readonly>
										</td>

										<?php	if ($no == 1) {?>
											<td rowspan="<?=count($ILamination);?>" >
												<input class="form-control input-sm numberOnly" type="text" id="glasslength_<?=$no?>" name="glasslength_<?=$no?>" value="" readonly>
											</td>
										<?php	}	?>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="veil_weight_<?=$no?>" name="veil_weight_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="csm_weight_<?=$no?>" name="csm_weight_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="wr_weight_<?=$no?>" name="wr_weight_<?=$no?>" value="" readonly>
										</td>

									</tr>
					<?php } ?>
					<input type="hidden" name="no_il" value="<?=$no?>">
					</tbody>
				</table>
			</div>
		</div>
		<div class='headerTitle'>OUTSIDE LAMINATION</div>
		<input type='text' name='detail_name' id='detail_name' class='HideCost' value='Outside Lamination'>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<head>
						<tr class='bg-blue'>
							<th class="text-center" width='5%'>Lapisan</th>
							<th class="text-center" width='10%'>Std Glass Conf</th>
							<th class="text-center" width='10%'>Width</th>
							<th class="text-center" width='5%'>Stage</th>
							<th class="text-center" width='10%'>Glass Conf</th>
							<th class="text-center" width='10%'>Thickness</th>
							<th class="text-center" width='10%'></th>
							<th class="text-center" width='10%'>Glass Length</th>
							<th class="text-center" width='10%'>Weight VEIL</th>
							<th class="text-center" width='10%'>Weight CSM</th>
							<th class="text-center" width='10%'>Weight WR</th>
						</tr>
					</head>
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
										<input class="form-control input-sm numberOnlyCent" type="hidden" id="o_stage_ke_<?=$no?>" name="o_stage_ke_<?=$no?>" value="<?=$i?>">

										<td >
											<input class="form-control input-sm numberOnlyCent" type="text" id="o_lapisan_<?=$no?>" name="o_lapisan_<?=$no?>" value="<?=$no?>" readonly>
										</td>

										<td >
											<?php	if ($v['type'] == "RUMUS") {?>
												<input class="form-control input-sm numberOnlyCent" type="text" id="o_std_<?=$no?>" name="o_std_<?=$no?>" value="" readonly>
											<?php	}else {	?>
												<input class="form-control input-sm numberOnlyCent" type="text" id="o_std_<?=$no?>" name="o_std_<?=$no?>" value="<?=$v['type']?>" readonly>
											<?php	}	?>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="o_width_<?=$no?>" name="o_width_<?=$no?>" value="" readonly>
										</td>

										<?php	if ($no == 1) {?>
											<td  style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" rowspan="<?=count($new);?>">
												<input class="form-control input-sm numberOnlyCent" type="text" id="o_stage_<?=$i?>" name="o_stage_<?=$i?>" value="<?=$i?>" readonly>
											</td>
										<?php	}elseif (($no % 7) == 0) {?>
											<td  style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" rowspan="<?=count($new);?>">
												<input class="form-control input-sm numberOnlyCent" type="text" id="0_stage_<?=$i?>" name="0_stage_<?=$i?>" value="<?=$i?>" readonly>
											</td>
										<?php	}	?>

										<td >
											<input class="form-control input-sm numberOnlyCent" type="text" id="o_glassconfiguration_<?=$no?>" name="o_glassconfiguration_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="o_thickness1_<?=$no?>" name="o_thickness1_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="o_thickness2_<?=$no?>" name="o_thickness2_<?=$no?>" value="" readonly>
										</td>

										<?php	if ($no == 1) {?>
											<td rowspan="<?=count($new);?>" style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" >
												<input class="form-control input-sm numberOnly" type="text" id="o_glasslength_<?=$i?>" name="o_glasslength_<?=$i?>" value="" readonly>
											</td>
										<?php	}elseif (($no % 7) == 0) {?>
											<td rowspan="<?=count($new);?>" style="text-align:center;vertical-align:middle;border-bottom:2px solid #000" >
												<input class="form-control input-sm numberOnly" type="text" id="o_glasslength_<?=$i?>" name="o_glasslength_<?=$i?>" value="" readonly>
											</td>
										<?php	}	?>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="o_veil_weight_<?=$no?>" name="o_veil_weight_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="o_csm_weight_<?=$no?>" name="o_csm_weight_<?=$no?>" value="" readonly>
										</td>

										<td >
											<input class="form-control input-sm numberOnly" type="text" id="o_wr_weight_<?=$no?>" name="o_wr_weight_<?=$no?>" value="" readonly>
										</td>

									</tr>
					<?php
								}
								}	?>
								<input type="hidden" name="no_ol" value="<?=$no?>">
				</tbody>
				</table>
			</div>
		</div>
		<br>			
	</div>	
</div>

<style type="text/css">
	
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
		background-color: #296753;
		height: 30px;
		font-weight: bold;
		padding-top: 5px;
		margin-bottom: 15px;
		margin-top: 30px;
		color: white;
	}
	
	.headerTitleGroup{
		text-align: center;
		background-color: #47a997;
		height: 30px;
		font-weight: bold;
		padding-top: 5px;
		margin-bottom: 15px;
		margin-top: 30px;
		color: white;
	}

	.numberOnly{
		text-align: right;
	}

	.numberOnlyCent{
		text-align: center;
	}
	
	
</style>
<script>	
	$(document).ready(function(){
		swal.close();
		$(".HideCost").hide();
		$(".Hide").hide();
		$(".chosen-select").chosen();
		
		std();
		OL_Function();
		hitung_all();
	});

	$(document).on('click', '#calc', function(e){
		// console.log("Kena");
		std();
		OL_Function();
		hitung_all();
	});

	$(document).on('keyup', '#pipe_thickness', function(e){
		var x = parseFloat($("#factor_thickness").val())*parseFloat($("#pipe_thickness").val());
		if (isNaN(x)) {
			x = 0;
		}
		$("#joint_thickness").val(x.toFixed(4)); 
	});
	
	$(document).on('keyup', '#factor_thickness', function(e){
		var x = parseFloat($("#factor_thickness").val())*parseFloat($("#pipe_thickness").val());
		if (isNaN(x)) {
			x = 0;
		}
		$("#joint_thickness").val(x.toFixed(4));
	});
	
	function joint_thickness_calc(){
		var x = parseFloat($("#factor_thickness").val())*parseFloat($("#pipe_thickness").val());
		if (isNaN(x)) {
			x = 0;
		}
		$("#joint_thickness").val(x);
	}

	//GET VEIL MATERIAL
	$(document).on('change', '#veil', function(e){
		var x = $("#veil").val();
		get_mat(x,'area weight','veil')
	});
	//GET WR MATERIAL
	$(document).on('change', '#wr', function(e){
		var x = $("#wr").val();
		get_mat(x,'area weight','wr')
	});
	//GET CSM MATERIAL
	$(document).on('change', '#csm', function(e){
		var x = $("#csm").val();
		get_mat(x,'area weight','csm')
	});
	//GET MATERIAL
	function get_mat(id, table, nama){
		if(id != ''){
			$.ajax({
			  type:"GET",
			  url:base_url+"/edit_joint/get_detail_mat",
						data: {
						id_material: id,
							nm_standard: table
					  },
			  success:function(result){
				var data = JSON.parse(result);
				$('#'+nama+'_area_weight').val(data.nilai_standard);
							//$('#'+nama+'_thickness').val((parseFloat(data.nilai_standard)/1000/2.56)+(parseFloat(data.nilai_standard)/1000/1.2*parseFloat($('#veil_resin_content').val())));
							hitung_all();
			  },error:function(){
							console.log(base_url+"/edit_joint/"+"get_detail_mat");
						}
			});
		}
	}

	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});

	//=====================================GLASS HEAD=============================================//
		//-----------KONSTANTA------------//
		function kg(){ // KONSTANTA GLASS
				var dia = $("#diameter_1").val();;
				
				var D3 = parseFloat(dia);
				var D4 = parseFloat($("#minimum_width").val());
				var G3 = parseFloat($("#pipe_thickness").val());
				var G4 = parseFloat($("#joint_thickness").val());
				var G5 = parseFloat($("#factor_thickness").val());
				var J3 = parseFloat($("#factor").val());
				var F9 = parseFloat($("#veil_area_weight").val());
				var F10 = parseFloat($("#wr_area_weight").val());
				var F11 = parseFloat($("#csm_area_weight").val());
				var G9 = parseFloat($("#veil_resin_content").val());
				var G10 = parseFloat($("#wr_resin_content").val());
				var G11 = parseFloat($("#csm_resin_content").val());
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
				
				var D3 = parseFloat(dia);
				var D4 = parseFloat($("#minimum_width").val());
				var G3 = parseFloat($("#pipe_thickness").val());
				var G4 = parseFloat($("#joint_thickness").val());
				var G5 = parseFloat($("#factor_thickness").val());
				var J3 = parseFloat($("#factor").val());
				var J9 = parseFloat($("#veil_material_weight").val());
				var G9 = parseFloat($("#veil_resin_content").val());
				var J10 = parseFloat($("#wr_material_weight").val());
				var G10 = parseFloat($("#wr_resin_content").val());
				var J11 = parseFloat($("#csm_material_weight").val());
				var G11 = parseFloat($("#csm_resin_content").val());
				var G14 = parseFloat($("#resin_percentage").val()/100);
				var G15 = parseFloat($("#catalys_percentage").val()/100);
				var G16 = parseFloat($("#dempul_percentage").val()/100);
				var G17 = parseFloat($("#carbosilbubuk_percentage").val()/100);
				var G18 = parseFloat($("#resincarbosil_percentage").val()/100);
				var G19 = parseFloat($("#resintopcoat_percentage").val()/100);
				var G20 = parseFloat($("#cobalt_percentage").val()/100);
				var G21 = parseFloat($("#pigment_percentage").val()/100);
				var G22 = parseFloat($("#tinuvin_percentage").val()/100);
				var G23 = parseFloat($("#chloroform_percentage").val()/100);
				var G24 = parseFloat($("#solutionwax_percentage").val()/100);
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
				G24 : G24}
			}
			function PI(){
				return 3.141593;
			}
			//---------END KONSTANTA----------//

			function resin_material_weight(){
				kons();
				return (kons().J9*kons().G9)+(kons().J10*kons().G10)+(kons().J11*kons().G11);
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

		//==================================END RESIN & ADD=============================================//

//--------------------------------------------------------------------------------------------------------------------//

		//=====================================GET ALL COUNT============================================//
			function hitung_all(){
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
			}
	//==================================END GET ALL COUNT===========================================//
	//=====================================INSIDE LAMINATION========================================//
		//-----------STD------------//
		function std(){ // STD
			var jum = '<?= count($ILamination)?>';
			var w_il = 2;
			var dia = $("#diameter_1").val();;
			
			for (var i = parseInt(jum); i >= 1; i--){
				//STD
				if ($('#std_gc_'+i).val() == 'RUMUS') {
					if (parseFloat(dia) < parseFloat($('#diameter_penentuan_'+i).val())) {
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
					if (parseFloat(dia) > 2150) {
						$('#width_'+i).val(c1-50);
					}
				}

				if (i <= (jum-(2*2))) {
					if (parseFloat(dia) > 1500) {
						if (parseFloat(dia) > 2150) {
							$('#width_'+i).val(c1-100);
						}else {
							$('#width_'+i).val(c1-50);
						}
					}
				}

				if (i <= (jum-(2*3))) {
					if (parseFloat(dia) > 1050) {
						if (parseFloat(dia) > 1500) {
							if (parseFloat(dia) > 2150) {
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
					if (parseFloat(dia) > 600) {
						if (parseFloat(dia) > 1050) {
							if (parseFloat(dia) > 1500) {
								if (parseFloat(dia) > 2150) {
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
				if (parseFloat(dia) > 600) {
					var gl = PI()*parseFloat(dia)+100;
					$('#glasslength_'+i).val(gl);
				}
			}
		}
		//---------END KONSTANTA----------//
	//=================================END INSIDE LAMINATION========================================//

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
					sum_thickness1 = sum_thickness1 + parseFloat($('#o_thickness1_'+j).val());
				}

				//GLASS CONFIGURATION
				if (sum_thickness1 < parseFloat($('#joint_thickness').val())) {
					if (i == 2) {
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

				}else if (sum_thickness1 >	parseFloat($('#joint_thickness').val())) {
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
						$('#o_width_'+n).val(width.toFixed(4));
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
						$('#o_width_'+n).val(width.toFixed(4));
					}else {
						if (n != 1 && s != 1) {
								if (i%2 == 0) {
									if 	(	(($('#o_thickness1_'+(parseInt(n)+1)).val() == null || $('#o_thickness1_'+(parseInt(n)+1)).val() == 0) && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
												(($('#o_thickness1_'+(parseInt(n)+2)).val() == null	|| $('#o_thickness1_'+(parseInt(n)+2)).val() == 0) && $('#o_glassconfiguration_'+(parseInt(n+1))).val() == 'CSM')
											) {
													var	width	=	ceilingxcl(kons().D4,50);
									}else if (($('#o_width_'+(parseInt(n)+1)).val() == null || $('#o_width_'+(parseInt(n)+1)).val() == 0) || parseFloat($('#o_width_'+(parseInt(n)+1)).val()) < 1) {
										var width = 0;
									}else {
										var width = getNum($('#o_width_'+(parseInt(n)+1)).val()) - 50;
									}
									$('#o_width_'+n).val(width.toFixed(4));
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
									$('#o_width_'+n).val(width.toFixed(4));
								}
						}else if (n != 1 && s == 1) {
								if (i%2 != 0) {
									if 	(	(($('#o_thickness1_'+(parseInt(n)+1)).val() == null || $('#o_thickness1_'+(parseInt(n)+1)).val() == 0) && $('#o_glassconfiguration_'+n).val() == 'CSM')	||
												(($('#o_thickness1_'+(parseInt(n)+2)).val() == null	|| $('#o_thickness1_'+(parseInt(n)+2)).val() == 0) && $('#o_glassconfiguration_'+(parseInt(n+1))).val() == 'CSM')
											) {
													var	width	=	ceilingxcl(kons().D4,50);
									}else if (($('#o_width_'+(parseInt(n)+1)).val() == null || $('#o_width_'+(parseInt(n)+1)).val() == 0) || parseFloat($('#o_width_'+(parseInt(n)+1)).val()) < 1) {
										var width = 0;
									}else {
										var width = getNum($('#o_width_'+(parseInt(n)+1)).val()) - 50;
									}
									$('#o_width_'+n).val(width.toFixed(4));
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
									$('#o_width_'+n).val(width.toFixed(4));
								}
						}
					}

					//GL
					var waste = getNum($('#waste').val());
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
							var nilxy = nil + waste;
							$('#o_glasslength_'+s).val(nilxy.toFixed(4));
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
						if (gl	<	getNum($('#o_glasslength_'+k).val())) {
							gl = getNum($('#o_glasslength_'+k).val());
						}
					}
					//q =
					if ($('#o_glassconfiguration_'+q).val()	==	"VEIL") {
						var vw = getNum($('#o_width_'+q).val())*gl*(getNum($('#veil_area_weight').val())/Math.pow(10,9)*2)

						$('#o_veil_weight_'+q).val(vw.toFixed(4));
					}else {
						$('#o_veil_weight_'+q).val(0);
					}

					//CSM

					if ($('#o_glassconfiguration_'+q).val()	==	"CSM") {
						var cw = getNum($('#o_width_'+q).val())*gl*(getNum($('#csm_area_weight').val())/Math.pow(10,9))
						$('#o_csm_weight_'+q).val(cw.toFixed(4));
					}else {
						$('#o_csm_weight_'+q).val(0);
					}

					//WR

					if ($('#o_glassconfiguration_'+q).val()	==	"WR") {
						var ww = getNum($('#o_width_'+q).val())*gl*(getNum($('#wr_area_weight').val())/Math.pow(10,9))
						$('#o_wr_weight_'+q).val(ww.toFixed(4));
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

					$('#veil_weight_'+q).val(vw.toFixed(4));
				}else {
					$('#veil_weight_'+q).val(0);
				}

				//CSM

				if ($('#glassconfiguration_'+q).val()	==	"CSM") {
					var cw = getNum($('#width_'+q).val())*gl*(getNum($('#csm_area_weight').val())/Math.pow(10,9))
					$('#csm_weight_'+q).val(cw.toFixed(4));
				}else {
					$('#csm_weight_'+q).val(0);
				}

				//WR

				if ($('#glassconfiguration_'+q).val()	==	"WR") {
					var ww = getNum($('#width_'+q).val())*gl*(getNum($('#wr_area_weight').val())/Math.pow(10,9))
					$('#wr_weight_'+q).val(ww.toFixed(4));
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
			// console.log(il);
			for (var i = 1; i <= il; i++) {
				juml_veil = juml_veil+getNum($('#veil_weight_'+i).val());
				juml_csm = juml_csm+getNum($('#csm_weight_'+i).val());
				juml_wr = juml_wr+getNum($('#wr_weight_'+i).val());
			}
			for (var j = 1; j <= ol; j++) {
				juml_veil = juml_veil+getNum($('#o_veil_weight_'+j).val());
				juml_csm = juml_csm+getNum($('#o_csm_weight_'+j).val());
				juml_wr = juml_wr+getNum($('#o_wr_weight_'+j).val());
				// console.log(ol);
			}
			var weightVeil = juml_veil*parseFloat($('#factor').val());
			var weightCsm = juml_csm*parseFloat($('#factor').val());
			var weightWr = juml_wr*parseFloat($('#factor').val());
			
			$('#veil_material_weight').val(weightVeil.toFixed(4));
			$('#csm_material_weight').val(weightCsm.toFixed(4));
			$('#wr_material_weight').val(weightWr.toFixed(4));
		}
		
		function getNum(val) {
		   if (isNaN(val) || val == '') {
			 return 0;
		   }
		   return parseFloat(val);
		}
</script>