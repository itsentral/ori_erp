<?php
$this->load->view('include/side_menu'); 
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
	
		<div class="box-body">
			<!-- NEW -->
			<div class='headerTitleGroup'>GROUP COMPONENT</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm HideCost','readonly'=>'readonly'));
						echo form_input(array('id'=>'parent_product','name'=>'parent_product','class'=>'form-control input-sm HideCost','readonly'=>'readonly'));
						echo form_input(array('id'=>'top_diameter','name'=>'diameter','class'=>'form-control input-sm HideCost','readonly'=>'readonly'));
					?>	
					<select name='top_typeList' id='top_typeList' class='form-control input-sm'>
						<option value='0'>Select Diameter</option>
					<?php
						foreach($product AS $val => $valx){
							echo "<option value='".$valx['id']."'>".ucfirst(strtolower($valx['nm_product']))."</option>";
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
			<div class='headerTitleGroup'>SPESIFIKASI COMPONENT</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Fluida <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='criminal_barier' id='criminal_barier' class='form-control input-sm'>
						<option value='0'>Select Fluida</option>
					<?php
						foreach($criminal_barier AS $val => $valx){
							echo "<option value='".$valx['name']."'>".strtoupper(strtolower($valx['name']))."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Stiffness <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='aplikasi_product' id='aplikasi_product' class='form-control input-sm'>
						<option value='0'>Select Stiffness</option>
					<?php
						foreach($aplikasi_product AS $val => $valx){
							echo "<option value='".$valx['name']."'>".strtoupper($valx['data2'])."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Vacuum Rate <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='vacum_rate' id='vacum_rate' class='form-control input-sm'>
						<option value='0'>Select Vacuum Rate</option>
					<?php
						foreach($vacum_rate AS $val => $valx){
							echo "<option value='".$valx['data1']."'>".$valx['name']."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Design Life <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='design_life' id='design_life' class='form-control input-sm'>
						<option value='0'>Select Design Life</option>
					<?php
						foreach($design_life AS $val => $valx){
							echo "<option value='".$valx['name']."'>".strtoupper(strtolower($valx['name']))."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Application<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select id='top_app' name='top_app' class='form-control input-sm'>
						<option value='ABOVE GROUND' selected>Above Ground</option>
						<option value='UNDER GROUND'>Under Ground</option>
					</select>
				</div> 
			</div>
			<!-- //// -->
			<div class='headerTitle'>DETAILED ESTIMATION</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Thickness Design | Est<span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'top_tebal_design','name'=>'top_tebal_design','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Pipe Thickness (Design)', 'value'=>''));											
					?>	
				</div>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'top_tebal_est','name'=>'top_tebal_est','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Waste | Max Min</b></label>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm numberOnly','placeholder'=>'Waste','readonly'=>'readonly'));	
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'top_min_toleran','name'=>'top_min_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Min','readonly'=>'readonly'));											
						
						echo form_input(array('type'=>'text','id'=>'area','name'=>'area','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'top_toleran','name'=>'top_toleran','class'=>'HideCost'),'C100-1903000');
						echo form_input(array('type'=>'text','id'=>'acuhan_1','name'=>'acuhan_1','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'radius','name'=>'radius','class'=>'form-control input-sm HideCost','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
						
						//====================================TAMBAHAN=====================================
						echo form_input(array('type'=>'text','id'=>'standart_code','name'=>'standart_code','class'=>'HideCost'), 'PRODUCT-ORI');
						
						echo form_input(array('type'=>'text','id'=>'lin_faktor_veil','lin_faktor_veil'=>'area','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'lin_faktor_veil_add','name'=>'lin_faktor_veil_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'lin_faktor_csm','name'=>'lin_faktor_csm','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'lin_faktor_csm_add','lin_faktor_csm_add'=>'area','class'=>'HideCost'));
						
						echo form_input(array('type'=>'text','id'=>'str_faktor_csm','name'=>'str_faktor_csm','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_csm_add','name'=>'str_faktor_csm_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_wr','name'=>'str_faktor_wr','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_wr_add','name'=>'str_faktor_wr_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_rv','name'=>'str_faktor_rv','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add','name'=>'str_faktor_rv_add','class'=>'HideCost'));
						
						echo form_input(array('type'=>'text','id'=>'eks_faktor_veil','name'=>'eks_faktor_veil','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'eks_faktor_veil_add','name'=>'eks_faktor_veil_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'eks_faktor_csm','name'=>'eks_faktor_csm','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'eks_faktor_csm_add','name'=>'eks_faktor_csm_add','class'=>'HideCost'));
					?>
				</div>
				<div class='col-sm-1'>              
						<?php
							echo form_input(array('id'=>'top_max_toleran','name'=>'top_max_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Max','readonly'=>'readonly'));											
						?>	
					</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Type Radius | Angle <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>              
					<select name='type_elbow' id='type_elbow' class='form-control input-sm'>
						<option value='0'>Type Radius</option>
						<option value='SR'>Short Radius</option>
						<option value='LR'>Long Radius</option>
					</select>
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'nil_type','name'=>'nil_type','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Val LR/SR', 'maxlength'=>'5'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'angle','name'=>'angle','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Angle', 'maxlength'=>'3'));											
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
				<label class='label-control col-sm-2'><b><div id='mirror'>MIRROR GLASS<span class='text-red'>*</span></div></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetail[1][containing]' class='HideCost' id='layer_plastic' value='1'>
				</div>
				<label class='col-sm-1' style='text-align:left;'>Micron</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'micron_plastic','name'=>'ListDetail[1][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-4'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_plastic','name'=>'ListDetail[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>VEIL<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' style='text-align:left;'>Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_veil','name'=>'ListDetail[2][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' style='text-align:left;'>Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_veil','name'=>'ListDetail[2][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1' style='text-align:left;'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_veil','name'=>'ListDetail[2][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_veil','name'=>'ListDetail[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin1' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>	
					<input type='text' name='ListDetail[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail[3][last_full]' class='HideCost' id='hasil_resin1' value='0'>
					<input type='text' name='ListDetail[3][id_material]' class='HideCost' id='layer_resin1hide'>
				</div>
				<label class='col-sm-3'><u>Resin Containing</u></label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin1','name'=>'ListDetail[3][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));	//9										
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin1','name'=>'ListDetail[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL VEIL<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1'>Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_veil_add','name'=>'ListDetail[4][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1'>Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_veil_add','name'=>'ListDetail[4][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_veil_add','name'=>'ListDetail[4][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_veil_add','name'=>'ListDetail[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin2' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail[5][last_full]' class='HideCost' id='hasil_resin2' value='0'>
					<input type='text' name='ListDetail[5][id_material]' class='HideCost' id='layer_resin2hide'>
				</div>
				<label class='col-sm-3'><u>Resin Containing</u></label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin2','name'=>'ListDetail[5][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));									
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin2','name'=>'ListDetail[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1'>Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_matcsm','name'=>'ListDetail[6][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1'>Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_matcsm','name'=>'ListDetail[6][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_matcsm','name'=>'ListDetail[6][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_matcsm','name'=>'ListDetail[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin3' class='form-control input-sm' disabled>
					<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail[7][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail[7][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail[7][last_full]' class='HideCost' id='hasil_resin3' value='0'>
					<input type='text' name='ListDetail[7][id_material]' class='HideCost' id='layer_resin3hide'>
				</div>
				<label class='col-sm-3'><u>Resin Containing</u></label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin3','name'=>'ListDetail[7][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly','value'=>'0'));	//2.333									
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin3','name'=>'ListDetail[7][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1'>Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_csm_add','name'=>'ListDetail[8][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1'>Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_csm_add','name'=>'ListDetail[8][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_csm_add','name'=>'ListDetail[8][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_csm_add','name'=>'ListDetail[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin4' class='form-control input-sm' disabled>
					<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail[9][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail[9][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail[9][last_full]' class='HideCost' id='hasil_resin4' value='0'>
					<input type='text' name='ListDetail[9][id_material]' class='HideCost' id='layer_resin4hide'>					
				</div>
				<label class='col-sm-3'><u>Resin Containing</u></label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin4','name'=>'ListDetail[9][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly','value'=>'0'));	//2.333										
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin4','name'=>'ListDetail[9][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-3'></div>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetail[10][containing]' class='form-control input-sm numberOnly' id='layer_resin_tot' readonly='readonly'>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin_tot','name'=>'ListDetail[10][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='col-sm-2'></label>
				
				<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[0][containing]'class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis' value='0'>	
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
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[1][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_sm' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[1][perse]' class='form-control input-sm numberOnly' id='persen_sm' value='0'>	
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
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[2][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[2][perse]'  class='form-control input-sm numberOnly' id='persen_coblat' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_cobalt','name'=>'ListDetailPlus[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1' >Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[3][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[3][perse]'  class='form-control input-sm numberOnly' id='persen_dma' value='0.2'>	
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
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1' >Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[4][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_hydroquinone' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[4][perse]'  class='form-control input-sm numberOnly' id='persen_hydroquinone' value='0.05'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_hidro','name'=>'ListDetailPlus[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1' >Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[5][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_methanol' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus[5][perse]'  class='form-control input-sm numberOnly' id='persen_methanol' value='0.05'>	
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
			<!-- END -->
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Total Liner Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'tot_lin_thickness','name'=>'tot_lin_thickness','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Min Liner Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'mix_lin_thickness','name'=>'mix_lin_thickness','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Max Liner Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'max_lin_thickness','name'=>'max_lin_thickness','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-10'></div>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'hasil_linier_thickness','name'=>'hasil_linier_thickness','class'=>'form-control input-sm HasilKet','autocomplete'=>'off','readonly'=>'readonly'));											
					?>
				</div>
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
					<input type='text' name='acuhan_2' class='form-control input-md numberOnly Acuhan' id='acuhan_2' readonly='readonly' value='0'>
					<input type='text' name='detail_name2' id='detail_name2' class='HideCost' value='STRUKTUR THICKNESS'>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_matcsm2','name'=>'ListDetail2[0][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_matcsm2','name'=>'ListDetail2[0][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_matcsm2','name'=>'ListDetail2[0][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_matcsm2','name'=>'ListDetail2[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin21' class='form-control input-sm' disabled>
					<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail2[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail2[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail2[1][last_full]' class='HideCost' id='hasil_resin21' value='0'>
					<input type='text' name='ListDetail2[1][id_material]' class='HideCost' id='layer_resin21hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin21','name'=>'ListDetail2[1][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));	//2.333										
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin21','name'=>'ListDetail2[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_csm_add2','name'=>'ListDetail2[2][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_csm_add2','name'=>'ListDetail2[2][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_csm_add2','name'=>'ListDetail2[2][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_csm_add2','name'=>'ListDetail2[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin22' class='form-control input-sm' style="display:none;" disabled>
					<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail2[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail2[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail2[3][last_full]' class='HideCost' id='hasil_resin22' value='0'>
					<input type='text' name='ListDetail2[3][id_material]' class='HideCost' id='layer_resin22hide'>					
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin22','name'=>'ListDetail2[3][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));	//2,333										
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin22','name'=>'ListDetail2[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>WR<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_wr2','name'=>'ListDetail2[4][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_wr2','name'=>'ListDetail2[4][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_wr2','name'=>'ListDetail2[4][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_wr2','name'=>'ListDetail2[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin23' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>	
					<input type='text' name='ListDetail2[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail2[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail2[5][last_full]' class='HideCost' id='hasil_resin23' value='0'>
					<input type='text' name='ListDetail2[5][id_material]' class='HideCost' id='layer_resin23hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin23','name'=>'ListDetail2[5][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly','value'=>'0'));		//1									
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin23','name'=>'ListDetail2[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL WR<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_wr_add2','name'=>'ListDetail2[6][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_wr_add2','name'=>'ListDetail2[6][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_wr_add2','name'=>'ListDetail2[6][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_wr_add2','name'=>'ListDetail2[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin24' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>	
					<input type='text' name='ListDetail2[7][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail2[7][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>	
					<input type='text' name='ListDetail2[7][last_full]' class='HideCost' id='hasil_resin24' value='0'>
					<input type='text' name='ListDetail2[7][id_material]' class='HideCost' id='layer_resin24hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin24','name'=>'ListDetail2[7][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));		//1									
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin24','name'=>'ListDetail2[7][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ROOVING<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
					<select name='ListDetail2[8][id_material]' id='mid_mtl_rooving21' class='form-control input-sm'>
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
					<input type='text' name='ListDetail2[8][thickness]' class='HideCost' id='thickness_rooving21' value='0'>
					<input type='text' name='ListDetail2[8][last_full]' class='HideCost' id='hasil_rooving21' value='0'>
					<input type='text' name='ListDetail2[8][fak_pengali]' class='HideCost' id='penggali_rooving21' value='100'>
					<input type='text' name='ListDetail2[8][bw]' class='HideCost' id='bw_rooving21' value='0'>	
					<input type='text' name='ListDetail2[8][jumlah]' class='HideCost' id='jumlah_rooving21' value='0'>	
				</div>
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_rooving21','name'=>'ListDetail2[8][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_rooving21','name'=>'ListDetail2[8][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_rooving21','name'=>'ListDetail2[8][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_rooving21','name'=>'ListDetail2[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin25' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>	
					<input type='text' name='ListDetail2[9][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail2[9][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail2[9][last_full]' class='HideCost' id='hasil_resin25' value='0'>
					<input type='text' name='ListDetail2[9][id_material]' class='HideCost' id='layer_resin25hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin25','name'=>'ListDetail2[9][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin25','name'=>'ListDetail2[9][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL ROOVING<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
					<select name='ListDetail2[10][id_material]' id='mid_mtl_rooving22' class='form-control input-sm'>
						<option value=''>Select An Rooving</option>
					<?php
						foreach($ListMatRooving AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail2[10][id_ori]' class='HideCost' id='id_ori' value='TYP-0005'>
					<input type='text' name='ListDetail2[10][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0005'>
					<input type='text' name='ListDetail2[10][thickness]' class='HideCost' id='thickness_rooving22' value='0'>
					<input type='text' name='ListDetail2[10][last_full]' class='HideCost' id='hasil_rooving22' value='0'>
					<input type='text' name='ListDetail2[10][fak_pengali]' class='HideCost' id='penggali_rooving22' value='100'>
					<input type='text' name='ListDetail2[10][bw]' class='HideCost' id='bw_rooving22' value='0'>	
					<input type='text' name='ListDetail2[10][jumlah]' class='HideCost' id='jumlah_rooving22' value='0'>	
				</div>
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_rooving22','name'=>'ListDetail2[10][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_rooving22','name'=>'ListDetail2[10][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_rooving22','name'=>'ListDetail2[10][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_rooving22','name'=>'ListDetail2[10][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin26' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>	
					<input type='text' name='ListDetail2[11][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail2[11][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail2[11][last_full]' class='HideCost' id='hasil_resin26' value='0'>
					<input type='text' name='ListDetail2[11][id_material]' class='HideCost' id='layer_resin26hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin26','name'=>'ListDetail2[11][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>               
					<?php
						echo form_input(array('id'=>'last_resin26','name'=>'ListDetail2[11][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-3' ></label>
				
				<div class='col-sm-1'>              
					<input type='text' name='ListDetail2[12][containing]' class='form-control input-sm numberOnly' id='layer_resin_tot2' readonly='readonly'>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin_tot2','name'=>'ListDetail2[12][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2'></label>
				
				<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[0][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis2' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[0][perse]'  class='form-control input-sm numberOnly' id='persen_katalis2' value='2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_katalis2','name'=>'ListDetailPlus2[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[1][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_sm2' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[1][perse]'  class='form-control input-sm numberOnly' id='persen_sm2' value='2.5'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_sm2','name'=>'ListDetailPlus2[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[2][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat2' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[2][perse]'  class='form-control input-sm numberOnly' id='persen_coblat2' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_cobalt2','name'=>'ListDetailPlus2[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[3][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma2' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[3][perse]'  class='form-control input-sm numberOnly' id='persen_dma2' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_dma2','name'=>'ListDetailPlus2[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[4][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_hydroquinone2' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[4][perse]'  class='form-control input-sm numberOnly' id='persen_hydroquinone2' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_hidro2','name'=>'ListDetailPlus2[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[5][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_methanol2' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus2[5][perse]'  class='form-control input-sm numberOnly' id='persen_methanol2' value='0.2'>	
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
			<!-- END -->
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Total Structure Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'tot_lin_thickness2','name'=>'tot_lin_thickness2','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>		
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Min Structure Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'mix_lin_thickness2','name'=>'mix_lin_thickness2','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Max Structure Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'max_lin_thickness2','name'=>'max_lin_thickness2','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-10'></div>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'hasil_linier_thickness2','name'=>'hasil_linier_thickness2','class'=>'form-control input-sm HasilKet','autocomplete'=>'off','readonly'=>'readonly'));											
					?>
				</div>
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
				<div class='col-sm-6'></div>
				<div class='col-sm-2'>              
					<input type='text' name='acuhan_3' class='Acuhan numberOnly' id='acuhan_3' value='0'>
					<input type='text' name='detail_name3' id='detail_name3' class='HideCost' value='EXTERNAL LAYER THICKNESS'>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>VEIL<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>  						
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_veil3','name'=>'ListDetail3[0][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_veil3','name'=>'ListDetail3[0][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_veil3','name'=>'ListDetail3[0][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_veil3','name'=>'ListDetail3[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin31' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>	
					<input type='text' name='ListDetail3[1][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail3[1][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail3[1][last_full]' class='HideCost' id='hasil_resin31' value='0'>
					<input type='text' name='ListDetail3[1][id_material]' class='HideCost' id='layer_resin31hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin31','name'=>'ListDetail3[1][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin31','name'=>'ListDetail3[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL VEIL<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_veil_add3','name'=>'ListDetail3[2][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_veil_add3','name'=>'ListDetail3[2][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_veil_add3','name'=>'ListDetail3[2][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_veil_add3','name'=>'ListDetail3[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin32' class='form-control input-sm' disabled>
						<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail3[3][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail3[3][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>					
					<input type='text' name='ListDetail3[3][last_full]' class='HideCost' id='hasil_resin32' value='0'>
					<input type='text' name='ListDetail3[3][id_material]' class='HideCost' id='layer_resin32hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin32','name'=>'ListDetail3[3][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin32','name'=>'ListDetail3[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>MAT / CSM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_matcsm3','name'=>'ListDetail3[4][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_matcsm3','name'=>'ListDetail3[4][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_matcsm3','name'=>'ListDetail3[4][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_matcsm3','name'=>'ListDetail3[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin33' class='form-control input-sm' disabled>
					<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail3[5][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail3[5][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>
					<input type='text' name='ListDetail3[5][last_full]' class='HideCost' id='hasil_resin33' value='0'>
					<input type='text' name='ListDetail3[5][id_material]' class='HideCost' id='layer_resin33hide'>
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin33','name'=>'ListDetail3[5][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin33','name'=>'ListDetail3[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2' style='text-align:left;'><b>ADDITIONAL CSM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-1' >Weight</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'weight_csm_add3','name'=>'ListDetail3[6][value]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				<label class='col-sm-1' >Layer</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_csm_add3','name'=>'ListDetail3[6][layer]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'value'=>''));											
					?>	
				</div>
				<label class='col-sm-1'>Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'totthick_csm_add3','name'=>'ListDetail3[6][total_thickness]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_csm_add3','name'=>'ListDetail3[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b></b></label>
				<div class='col-sm-3'>              
					<select name='id_material' id='mid_mtl_resin34' class='form-control input-sm' disabled>
					<option value=''>Select An Resin</option>
					<?php
						foreach($ListResin AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetail3[7][id_ori]' class='HideCost' id='id_ori' value='TYP-0001'>
					<input type='text' name='ListDetail3[7][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0001'>		
					<input type='text' name='ListDetail3[7][last_full]' class='HideCost' id='hasil_resin34' value='0'>
					<input type='text' name='ListDetail3[7][id_material]' class='HideCost' id='layer_resin34hide'>					
				</div>
				<label class='col-sm-3' ><u>Resin Containing</u></label>
				
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'layer_resin34','name'=>'ListDetail3[7][containing]','class'=>'form-control input-sm numberOnly','autocomplete'=>'off', 'readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin34','name'=>'ListDetail3[7][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>TOTAL RESIN<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<label class='col-sm-3' ></label>
				
				<div class='col-sm-1'>              
					<input type='text' name='ListDetail3[8][containing]' class='form-control input-sm numberOnly' id='layer_resin_tot3' readonly='readonly'>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin_tot3','name'=>'ListDetail3[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Katalis<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[0][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis3' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[0][perse]'  class='form-control input-sm numberOnly' id='persen_katalis3' value='2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_katalis3','name'=>'ListDetailPlus3[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>SM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[1][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_sm3' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[1][perse]'  class='form-control input-sm numberOnly' id='persen_sm3' value='2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_sm3','name'=>'ListDetailPlus3[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Cobalt<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[2][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat3' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[2][perse]'  class='form-control input-sm numberOnly' id='persen_coblat3' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_cobalt3','name'=>'ListDetailPlus3[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>DMA<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[3][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma3' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[3][perse]'  class='form-control input-sm numberOnly' id='persen_dma3' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_dma3','name'=>'ListDetailPlus3[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Hydroquinone<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[4][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_hydroquinone3' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[4][perse]'  class='form-control input-sm numberOnly' id='persen_hydroquinone3' value='0.05'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_hidro3','name'=>'ListDetailPlus3[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-1'><b>Methanol<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
				<div class='col-sm-1'></div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[5][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_methanol3' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus3[5][perse]'  class='form-control input-sm numberOnly' id='persen_methanol3' value='0.05'>	
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
			<!-- END -->
			
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Total Eksternal Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'tot_lin_thickness3','name'=>'tot_lin_thickness3','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Min Eksternal Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'mix_lin_thickness3','name'=>'mix_lin_thickness3','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-8'></div>
				<label class='col-sm-2'>Max Eksternal Thickness</label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'max_lin_thickness3','name'=>'max_lin_thickness3','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-10'></div>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'hasil_linier_thickness3','name'=>'hasil_linier_thickness3','class'=>'form-control input-sm HasilKet','autocomplete'=>'off','readonly'=>'readonly','value'=>'OK'));											
					?>	
				</div>
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
				<div class='col-sm-3'>              
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
					<!-- <input type='text' name='ListDetailPlus4[0][perse]' class='HideCost' id='resin41'> -->
				</div>
				<div class='col-sm-3'></div>
				<div class='col-sm-1'>
					<input type='text' name='ListDetailPlus4[0][perse]' class='form-control input-sm' id='resin41' readonly>
				</div>
				<div class='col-sm-2'></div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_resin41','name'=>'ListDetailPlus4[0][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>Katalis<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[1][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_katalis4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[1][perse]'  class='form-control input-sm numberOnly' id='persen_katalis4' value='2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_katalis4','name'=>'ListDetailPlus4[1][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>Color<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[2][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_color4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[2][perse]'  class='form-control input-sm numberOnly' id='persen_color4' value='5'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_color4','name'=>'ListDetailPlus4[2][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>Tinuvin<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[3][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_tin4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[3][perse]'  class='form-control input-sm numberOnly' id='persen_tin4' value='2.6'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_tin4','name'=>'ListDetailPlus4[3][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>Chlroroform<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[4][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_chl4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[4][perse]'  class='form-control input-sm numberOnly' id='persen_chl4' value='2.6'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_chl4','name'=>'ListDetailPlus4[4][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>SM<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[5][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_stery4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[5][perse]'  class='form-control input-sm numberOnly' id='persen_stery4' value='3'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_stery4','name'=>'ListDetailPlus4[5][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>Solution Wax<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[6][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_wax4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[6][perse]'  class='form-control input-sm numberOnly' id='persen_wax4' value='3'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_wax4','name'=>'ListDetailPlus4[6][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2' ></label>
				
				<label class='label-control col-sm-2'><b>Methelene Chlorida<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
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
					<input type='text' name='ListDetailPlus4[7][containing]'  class='form-control input-sm numberOnly' readonly='readonly' id='layer_mch4' value='0'>	
				</div>
				<label class='col-sm-1' >Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[7][perse]'  class='form-control input-sm numberOnly' id='persen_mch4' value='0'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_mch4','name'=>'ListDetailPlus4[7][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2'></label>
				
				<label class='label-control col-sm-2'><b>Cobalt<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
					<select name='ListDetailPlus4[8][id_material]' id='mid_mtl_cobalt4' class='form-control input-sm'>
						<option value=''>Select An Cobalt</option>
					<?php
						foreach($ListMatCobalt AS $val => $valx){
							$selx = ($valx['id_material'] == 'MTL-1903220')?'selected':'';
							echo "<option value='".$valx['id_material']."' ".$selx.">".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetailPlus4[8][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
					<input type='text' name='ListDetailPlus4[8][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
					<input type='text' name='ListDetailPlus4[8][last_full]' class='HideCost' id='hasil_coblat4' value='0'>
				</div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[8][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_coblat4' value='0'>	
				</div>
				<label class='col-sm-1'>Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[8][perse]' class='form-control input-sm numberOnly' id='persen_coblat4' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_cobalt4','name'=>'ListDetailPlus4[8][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
					?>	
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='col-sm-2'></label>
				
				<label class='label-control col-sm-2'><b>DMA<span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>              
					<select name='ListDetailPlus4[9][id_material]' id='mid_mtl_dma4' class='form-control input-sm'>
						<option value=''>Select An DMA</option>
					<?php
						foreach($ListMatDma AS $val => $valx){
							$selx = ($valx['id_material'] == 'MTL-1903219')?'selected':'';
							echo "<option value='".$valx['id_material']."' ".$selx.">".strtoupper($valx['nm_material'])."</option>";
						}
						echo "<option value='MTL-1903000'>NONE MATERIAL</option>";
					 ?>
					</select>
					<input type='text' name='ListDetailPlus4[9][id_ori]' class='HideCost' id='id_ori' value='TYP-0021'>
					<input type='text' name='ListDetailPlus4[9][id_ori2]' class='HideCost' id='id_ori2' value='TYP-0021'>
					<input type='text' name='ListDetailPlus4[9][last_full]' class='HideCost' id='hasil_dma4' value='0'>				
				</div>
				<label class='col-sm-1'>Comparison</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[9][containing]' class='form-control input-sm numberOnly' readonly='readonly' id='layer_dma4' value='0'>	
				</div>
				<label class='col-sm-1'>Percent</label>
				<div class='col-sm-1'>              
					<input type='text' name='ListDetailPlus4[9][perse]' class='form-control input-sm numberOnly' id='persen_dma4' value='0.2'>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'last_dma4','name'=>'ListDetailPlus4[9][last_cost]','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly', 'value'=>'0'));											
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
			<!-- END --> 
			<div class='form-group row'>		 	 
				<div class='col-sm-12'>              
					<button type='button' name='simpan-bro' id='simpan-bro' style='float:right; width:100px;' class='btn btn-primary'>Save</button>	
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<!-- ===============================================END TOPCOAT============================================ -->
			<!-- ====================================================================================================== -->
		</div>	
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Est_js/elbow_mould.js'); ?>"></script>
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
		$('#plactic').hide();
		
		$(document).on('change', '#type_elbow', function(){
			if($('#top_diameter').val() == '' || $('#top_diameter').val() == null || $('#top_diameter').val() == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Type Elbow belum dipilih ...',
				  type	: "warning"
				});
				return false;
			}
			else{
				if($(this).val() == 'SR'){
					var Hsr	= parseFloat($('#top_diameter').val()) * 1;
					$('#radius').val(Hsr);
					$('#nil_type').val('1');
				}
				if($(this).val() == 'LR'){
					var Hlr	= parseFloat($('#top_diameter').val()) * 1.5;
					$('#radius').val(Hlr);
					$('#nil_type').val('1.5');
				}
				if($(this).val() == 0){
					$('#radius').val('0');
					$('#nil_type').val('0');
				}
				changeTop();
			}
		});
		
		$(document).on('keyup', '#angle', function(){
			changeTop();
		});
		
		$(document).on('keyup', '#top_tebal_design', function(){
			changeTop();
		}); 
		
		$(document).on('keyup', '#top_min_toleran', function(){
			AcuhanMaxMin();
		});
		
		$(document).on('keyup', '#top_max_toleran', function(){
			AcuhanMaxMin();
		});
	
		
		$(document).on('change', '#series', function(){
			var series 		= $('#series').val();
			var linerEx		= series.split('-');
			var liner		= (linerEx[1] == '2.5')?'2.45':linerEx[1];

			var external			= parseFloat($('#acuhan_3').val());
			
			//pengurangan structure thickness
			var top_thickness	= parseFloat($('#top_tebal_design').val());
			var acuhan_1		= parseFloat(liner);
			var struktur		= top_thickness - (acuhan_1 + external);
			
			$('#acuhan_2').val(struktur.toFixed(2));
			$('#acuhan_1').val(liner);
			
			AcuhanMaxMin();
		});
		
		//==========================================SAVED=================================================
		$(document).on('click', '#simpan-bro', function(e){
			e.preventDefault(); 
			var top_type			= $('#top_typeList').val();
			var top_diameter		= $('#top_diameter').val();
			var top_tebal_design	= $('#top_tebal_design').val();
			var top_max_toleran		= $('#top_max_toleran').val();
			var top_min_toleran		= $('#top_min_toleran').val();
			
			var series				= $('#series').val();
			
			var criminal_barier		= $('#criminal_barier').val();
			var aplikasi_product	= $('#aplikasi_product').val();
			var vacum_rate			= $('#vacum_rate').val();
			var design_life			= $('#design_life').val();
			
			var top_app				= $('#top_app').val();
			var top_toleran			= $('#top_toleran').val();
			var type_elbow			= $('#type_elbow').val();
			var angle				= $('#angle').val();
			
			//LINER THICKNESS
			var mid_mtl_realese		= $('#mid_mtl_realese').val();
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
			// var mid_mtl_additive	= $('#mid_mtl_additive').val();
			
			var layer_veil 			= $('#layer_veil').val();
			var layer_veil_add 		= $('#layer_veil_add').val();
			var layer_matcsm 		= $('#layer_matcsm').val();
			var layer_csm_add 		= $('#layer_csm_add').val();
			
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
			var mid_mtl_rooving21	= $('#mid_mtl_rooving21').val();
			var mid_mtl_rooving22	= $('#mid_mtl_rooving22').val();
			var mid_mtl_resin_tot2	= $('#mid_mtl_resin_tot2').val();
			
			var mid_mtl_katalis2	= $('#mid_mtl_katalis2').val();
			var mid_mtl_sm2			= $('#mid_mtl_sm2').val();
			var mid_mtl_cobalt2		= $('#mid_mtl_cobalt2').val();
			var mid_mtl_dma2		= $('#mid_mtl_dma2').val();
			var mid_mtl_hydro2		= $('#mid_mtl_hydro2').val();
			var mid_mtl_methanol2	= $('#mid_mtl_methanol2').val();
			// var mid_mtl_additive2	= $('#mid_mtl_additive2').val();
			
			var layer_matcsm2		= $('#layer_matcsm2').val();
			var layer_csm_add2		= $('#layer_csm_add2').val();
			var layer_wr2			= $('#layer_wr2').val();
			var layer_wr_add2		= $('#layer_wr_add2').val();
			var layer_rooving21		= $('#layer_rooving21').val();
			var layer_rooving22		= $('#layer_rooving22').val();
			
			var persen_katalis2		= $('#persen_katalis2').val();
			var persen_sm2			= $('#persen_sm2').val();
			var persen_coblat2		= $('#persen_coblat2').val();
			var persen_dma2			= $('#persen_dma2').val();
			var persen_hydroquinone2	= $('#persen_hydroquinone2').val();
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
			// var mid_mtl_additive3	= $('#mid_mtl_additive3').val();
			
			var layer_veil3 		= $('#layer_veil3').val();
			var layer_veil_add3 	= $('#layer_veil_add3').val();
			var layer_matcsm3 		= $('#layer_matcsm3').val();
			var layer_csm_add3 		= $('#layer_csm_add3').val();
			
			var persen_katalis3 	= $('#persen_katalis3').val();
			var persen_sm3 			= $('#persen_sm3').val();
			var persen_coblat3 		= $('#persen_coblat3').val();
			var persen_dma3 		= $('#persen_dma3').val();
			var persen_hydroquinone3 = $('#persen_hydroquinone3').val();
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
			// var mid_mtl_additive4	= $('#mid_mtl_methanol3').val();
			
			var persen_katalis4 	= $('#persen_katalis4').val();
			var persen_color4 		= $('#persen_color4').val();
			var layer_matcsm3 		= $('#layer_matcsm3').val();
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
			
			
			if(top_type == '' || top_type == null || top_type == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Pipe type is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(top_diameter == '' || top_diameter == null || top_diameter == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'ID Pipe is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			
			if(series == '' || series == null || series == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Series is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(criminal_barier == '' || criminal_barier == null || criminal_barier == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Fluida is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(aplikasi_product == '' || aplikasi_product == null || aplikasi_product == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Stiffness is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(vacum_rate == '' || vacum_rate == null || vacum_rate == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Vacuum Rate is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(design_life == '' || design_life == null || design_life == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Design Life is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(top_app == '' || top_app == null || top_app == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Application is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(top_toleran == '' || top_toleran == null || top_toleran == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Tolerance by is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(type_elbow == '' || type_elbow == null || type_elbow == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Type Radius is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(angle == '' || angle == null || angle == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Angle is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			
			if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Thickness Pipe Design is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(top_max_toleran == '' || top_max_toleran == null || top_max_toleran == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Min Tolerance is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(top_min_toleran == '' || top_min_toleran == null || top_min_toleran == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Max Tolerance is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			
			//LINER THICKNESS
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
			// if(mid_mtl_additive == '' || mid_mtl_additive == null || mid_mtl_additive == 0){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Additive Material [LINER THICKNESS] is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;	
			// }
			
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
			if(mid_mtl_rooving21 == '' || mid_mtl_rooving21 == null || mid_mtl_rooving21 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Rooving 1 Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(mid_mtl_rooving22 == '' || mid_mtl_rooving22 == null || mid_mtl_rooving22 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Rooving 2 Material [STRUKTURE THICKNESS] is Empty, please input first ...',
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
			// if(mid_mtl_additive2 == '' || mid_mtl_additive2 == null || mid_mtl_additive2 == 0){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Additive Material [STRUKTURE THICKNESS] is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;	
			// }
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
			if(layer_rooving21 == '' || layer_rooving21 == null || layer_rooving21 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer Rooving [STRUKTURE THICKNESS] is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(layer_rooving22 == '' || layer_rooving22 == null || layer_rooving22 == '.'){
				swal({
				  title	: "Error Message!",
				  text	: 'Layer Rooving Add [STRUKTURE THICKNESS] is Empty, please input first ...',
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
			if(persen_hydroquinone2 == '' || persen_hydroquinone2 == null || persen_hydroquinone2 == '.'){
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
			// if(mid_mtl_additive3 == '' || mid_mtl_additive3 == null || mid_mtl_additive3 == 0){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Additive Material [EXTERNAL THICKNESS] is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;	
			// }
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
			if(persen_hydroquinone3 == '' || persen_hydroquinone3 == null || persen_hydroquinone3 == '.'){
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
			// if(mid_mtl_additive4 == '' || mid_mtl_additive4 == null || mid_mtl_additive4 == 0){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Additive Material [TOPCOAT] is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;	
			// }
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
			
			var hasil_linier_thickness 	= $('#hasil_linier_thickness').val();
			var hasil_linier_thickness2 = $('#hasil_linier_thickness2').val();
			var hasil_linier_thickness3 = $('#hasil_linier_thickness3').val();
			
			if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Thickness is To High or To Low, please check back ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
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
						var baseurl		= base_url + active_controller +'/elbowmould';
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
		
		
	});

</script>
