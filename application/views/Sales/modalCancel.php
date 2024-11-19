
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Request Customer</h3>
	</div>
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Customer Name</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_customer','name'=>'nm_customer','class'=>'form-control input-sm','placeholder'=>'Customer Name','readonly'=>'readonly'),strtoupper(strtolower($RestRequest[0]->nm_customer)));
					echo form_input(array('type'=>'hidden','id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-sm','placeholder'=>'Customer Name','readonly'=>'readonly'),$RestRequest[0]->no_ipp);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Product</b></label>
			<div class='col-sm-4'>
				 <?php
					echo form_input(array('id'=>'product','name'=>'product','class'=>'form-control input-sm','rows'=>'2','cols'=>'75','placeholder'=>'Product','readonly'=>'readonly'),strtoupper(strtolower($RestRequest[0]->product)));
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Project</b></label>
			<div class='col-sm-4'>
				<?php
				 echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-sm','rows'=>'2','cols'=>'75','placeholder'=>'Project','readonly'=>'readonly'),strtoupper(strtolower($RestRequest[0]->project)));
				?>
			</div>
			
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Cancel Reason <span class='text-red'>*</span></b></label>
			<div class='col-sm-10'>
				<?php
				 echo form_textarea(array('id'=>'status_reason','name'=>'status_reason','class'=>'form-control input-sm','rows'=>'2','cols'=>'75','placeholder'=>'Cancel Reason'));
				?>
			</div>
		</div>
	</div>
	<div class='box-footer'>
		<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'cancel_ipp')).' ';
		// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
		?>
	</div>
</div>
<script>
	swal.close();
	
</script>