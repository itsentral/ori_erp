
<div class="box-body"> 
	<br>
	<input type='hidden' name='id' value='<?=$id;?>'>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>Form No</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'form_no','name'=>'form_no','class'=>'form-control input-md','placeholder'=>'Form No'), $get_detail[0]->form_no);
			?>
		</div>
	</div>
    <div class='form-group row'>
		<label class='label-control col-sm-3'><b>Rev No</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'rev_no','name'=>'rev_no','class'=>'form-control input-md','placeholder'=>'Rev No'), $get_detail[0]->rev_no);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>Issue Date</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'issue_date','name'=>'issue_date','class'=>'form-control input-md datepicker','placeholder'=>'Issue Date','readonly'=>true), $get_detail[0]->issue_date);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-3'><b>No Surat Jalan</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'no_surat_jalan','name'=>'no_surat_jalan','class'=>'form-control input-md','placeholder'=>'No Surat Jalan'), $get_detail[0]->no_surat_jalan);
			?>
		</div>
	</div>
    <div class='form-group row'>
		<label class='label-control col-sm-3'><b>No Memo</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'no_memo','name'=>'no_memo','class'=>'form-control input-md','placeholder'=>'No Memo'), $get_detail[0]->no_memo);
			?>
		</div>
	</div>
    <div class='form-group row'>
		<label class='label-control col-sm-3'><b>No SO</b></label>
		<div class='col-sm-9'>
			<?php
			 echo form_input(array('id'=>'no_so','name'=>'no_so','class'=>'form-control input-md','placeholder'=>'No SO'), $get_detail[0]->no_so);
			?>
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