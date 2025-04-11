<form action="#" method="POST" id="form_start_pro" enctype="multipart/form-data"> 
	<div class="box-body">
		<div class="alert alert-warning alert-dismissible">
			<h4><i class="icon fa fa-info"></i> Info!</h4>
			Dengan close produksi, semua produksi atas sales order tersebut sudah selesai.
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>IPP Number</b></label> 
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'so_number','name'=>'so_number','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'disabled'=>'disabled'), $header[0]['so_number']);
					echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $header[0]['id_produksi']);
				?>				
			</div>
			<label class='label-control col-sm-2'><b>Machine</b></label>
			<div class='col-sm-4'>
				<select name='id_mesin' id='id_mesin' class='form-control input-md chosen_select' disabled>
					<option value=''>Select An Machine</option>
				<?php
					foreach($list_mesin AS $val => $valx){
						$selected = ($valx['id_mesin'] == $header[0]['id_mesin'])?'selected':'';
						echo "<option value='".$valx['id_mesin']."' $selected>".strtoupper($valx['nm_mesin'])."</option>";
					}
				?>
				</select>	
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Rencana Mulai Production</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'plan_start_produksi','name'=>'plan_start_produksi','class'=>'form-control input-md datepicker','autocomplete'=>'off','placeholder'=>'Rencana Start Production','disabled'=>'disabled'),date('d-M-Y',strtotime($header[0]['plan_start_produksi'])));
			?>
			</div>
			<label class='label-control col-sm-2'><b>Rencana Selesai Production</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'plan_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md datepicker','autocomplete'=>'off','placeholder'=>'Rencana Selesai Production','disabled'=>'disabled'),date('d-M-Y',strtotime($header[0]['plan_end_produksi'])));
			?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Aktual Mulai Production *</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'real_start_produksi','name'=>'real_start_produksi','class'=>'form-control input-md datepicker','autocomplete'=>'off','placeholder'=>'Aktual Mulai Production','readonly'=>'readonly'));
			?>
			</div>
			<label class='label-control col-sm-2'><b>Aktual Selesai Production *</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'real_end_produksi','name'=>'real_end_produksi','class'=>'form-control input-md datepicker','autocomplete'=>'off','placeholder'=>'Aktual Selesai Production','readonly'=>'readonly'));
			?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='col-sm-2'></label>
			<div class='col-sm-4'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Close Produksi','id'=>'save_close_produksi'));
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
			dateFormat : 'yy-mm-dd'
		});
	});
</script>	