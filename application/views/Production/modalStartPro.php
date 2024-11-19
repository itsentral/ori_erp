<?php
$id_produksi 	= $this->uri->segment(3);
$ListMachine	= $this->db->query("SELECT id_mesin, nm_mesin FROM machine WHERE sts_mesin='Y' ORDER BY nm_mesin ASC")->result_array();
			
$qSupplier 		= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
$row			= $this->db->query($qSupplier)->result_array();

?>
<form action="#" method="POST" id="form_start_pro" enctype="multipart/form-data"> 
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>IPP Number</b></label> 
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'so_number','name'=>'so_number','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['so_number']);
					echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_produksi']);
				?>				
			</div>
			<label class='label-control col-sm-2'><b>Machine</b></label>
			<div class='col-sm-4'>
				<select name='id_mesin' id='id_mesin' class='form-control input-md chosen_select'>
					<option value=''>Select An Machine</option>
				<?php
					foreach($ListMachine AS $val => $valx){
						echo "<option value='".$valx['id_mesin']."'>".strtoupper($valx['nm_mesin'])."</option>";
					}
				?>
				</select>	
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Plan Start Production</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'plan_start_produksi','name'=>'plan_start_produksi','class'=>'form-control input-md datepicker','autocomplete'=>'off','placeholder'=>'Plant Start Production','readonly'=>'readonly'));
			?>
			</div>
			<label class='label-control col-sm-2'><b>Plan End Production</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'plan_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md datepicker','autocomplete'=>'off','placeholder'=>'Plant End Production','readonly'=>'readonly'));
			?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='col-sm-2'></label>
			<div class='col-sm-4'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'save_start_produksi')).' ';
			?>
			</div>
			<label class='col-sm-2'></label>
			<div class='col-sm-4'></div>
		</div>
	</div>
</form>
<style>
	.datepicker{
		cursor: pointer;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}

</style>
<script>
	swal.close();
	$(document).ready(function(){
		$('.chosen_select').chosen();
		$('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd',
			minDate: 0
		});
	});
</script>	