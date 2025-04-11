<?php
$this->load->view('include/side_menu'); 
//echo"<pre>";print_r($data_menu);
?> 
<form action="#" method="POST" id="form_proses_bro"> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>			
				<label class='label-control col-sm-2'><b>Business Fields <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'bidang_usaha','name'=>'bidang_usaha','class'=>'form-control input-sm','autocomplete'=>'off','readOnly'=>true,'style'=>'text-transform:uppercase'),$rows[0]->bidang_usaha);										
					?>		
				</div>
				<label class='label-control col-sm-2'><b>Description <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_textarea(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-sm','cols'=>'75','rows'=>'1','autocomplete'=>'off','readOnly'=>true,'style'=>'text-transform:uppercase'),strtoupper($rows[0]->keterangan));										
					?>	
				</div>
			</div>			 
						
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){		
		
	});
</script>
