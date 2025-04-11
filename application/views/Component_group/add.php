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
				<label class='label-control col-sm-2'><b>Resin System <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='resin_sistem' id='resin_sistem' class='form-control input-sm'>
						<option value='0'>Select Resin System</option>
					<?php
						foreach($resin_system AS $val => $valx){
							echo "<option value='".$valx['name']."'>".strtoupper($valx['name'])."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Pressure <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='pressure' id='pressure' class='form-control input-sm'>
						<option value='0'>Select Pressure</option>
					<?php
						foreach($pressure AS $val => $valx){
							$KdPressure		= sprintf('%02s',$valx['name']);
							echo "<option value='".$valx['name']."'>PN ".ucfirst(strtolower($KdPressure))."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>	
				<label class='label-control col-sm-2'><b>Liner Thickness <span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='liner' id='liner' class='form-control input-sm'>
							<option value='0'>Select Liner</option>
						<?php
							foreach($liner AS $val => $valx){
								echo "<option value='".$valx['name']."'>".ucfirst(strtolower($valx['name']))." mm</option>";
							}
						 ?>
						</select>
					</div>	
			</div>	
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<style>
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
</style>
<script>
	$(document).ready(function(){
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var resin_sistem	= $('#resin_sistem').val();
			var pressure		= $('#pressure').val();
			var liner			= $('#liner').val();
			
			if(resin_sistem=='' || resin_sistem==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin system is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(pressure=='' || pressure==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Pressure is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(liner == '' || liner == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Liner Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
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
						var baseurl		= base_url + active_controller +'/add';
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
										  timer	: 7000
										});
									window.location.href = base_url + active_controller;
								}
								else{ 
									if(data.status == 2){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 7000
										});
									}
									else if(data.status == 3){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 7000
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
								}
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
