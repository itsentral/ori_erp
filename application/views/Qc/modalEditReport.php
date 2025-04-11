
<div class="box-body"> 
	<br>
	<input type='hidden' name='id' value='<?=$id;?>'>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>Daycode</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'daycode','name'=>'daycode','class'=>'form-control input-md','placeholder'=>'Daycode'), strtoupper($get_detail[0]->daycode));
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>QC Pass Date</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'qc_pass','name'=>'qc_pass','class'=>'form-control input-md datepicker','placeholder'=>'QC Pass Date','readonly'=>true), $get_detail[0]->qc_pass_date);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>Keterangan</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','placeholder'=>'Keterangan'), $get_detail[0]->keterangan);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>Replace Document</b></label>
		<div class='col-sm-9'>
			<input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Replace Document'>
		</div>
	</div>
    <div class='form-group row'>
		<label class='label-control col-sm-3'></label>
		<div class='col-sm-9'>
            <?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'edit_report'));
			?>
		</div>
	</div>
</div>
<style>
	.datepicker{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth:true,
            changeYear:true
        });
	});
</script>