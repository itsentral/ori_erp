<?php

$id 	= $this->uri->segment(3);
$tanda 	= $this->uri->segment(4); 
// echo $id;
if($tanda == 'foh'){
	$get_Data	= $this->db->query("SELECT * FROM cost_foh WHERE id='".$id."'")->row();
}
elseif($tanda == 'process'){
	$get_Data	= $this->db->query("SELECT * FROM cost_process WHERE id='".$id."'")->row();
}
?>

<div class="box box-success">
	<div class="box box-primary">
		<br>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Item Cost <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'item_cost','name'=>'item_cost','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Item Cost','readonly'=>'readonly'), $get_Data->item_cost);											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Standart Rate (<?=$get_Data->satuan;?>) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'std_rate','name'=>'std_rate','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Standart Rate (%)','readonly'=>'readonly'), floatval($get_Data->std_rate));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Standart Perhitungan <span class='text-red'>*</span></b></label>
				<div class='col-sm-10'>             
						<?php
							echo form_input(array('id'=>'std_hitung','name'=>'std_hitung','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Standart Perhitungan','readonly'=>'readonly'), $get_Data->std_hitung);											
						?>		
				</div>
			</div>		
		</div>
	 </div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>