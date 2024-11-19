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
				<label class='label-control col-sm-2'><b>File Header <span class='text-red'>*</span></b></label>
				<div class='col-sm-6'>
					<input type="file" class="custom-file-input form-control" id="upload_header" name='upload_header' accept=".xls, .xlsx">
				</div>
			</div>
            <div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>File Detail <span class='text-red'>*</span></b></label>
				<div class='col-sm-6'>
					<input type="file" class="custom-file-input form-control" id="upload_detail" name='upload_detail' accept=".xls, .xlsx">
				</div>
			</div>
            <div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>File Detail Plus <span class='text-red'>*</span></b></label>
				<div class='col-sm-6'>
					<input type="file" class="custom-file-input form-control" id="upload_plus" name='upload_plus' accept=".xls, .xlsx">
				</div>
			</div>
            <div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>File Detail Add</b></label>
				<div class='col-sm-6'>
					<input type="file" class="custom-file-input form-control" id="upload_add" name='upload_add' accept=".xls, .xlsx">
				</div>
			</div>		
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Upload','id'=>'upload_process')).' ';
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
		$('#upload_process').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var upload_header		= $('#upload_header').val();
			var upload_detail		= $('#upload_detail').val();
			var upload_plus			= $('#upload_plus').val();

			if(upload_header=='' || upload_header==null){
				swal({
				  title	: "Error Message!",
				  text	: 'File HEADER kosong ...',
				  type	: "warning"
				});
				$('#upload_process').prop('disabled',false);
				return false;
				
			}
			if(upload_detail=='' || upload_detail==null){
				swal({
				  title	: "Error Message!",
				  text	: 'File DETAIL kosong ...',
				  type	: "warning"
				});
				$('#upload_process').prop('disabled',false);
				return false;
				
			}
			if(upload_plus == '' || upload_plus == null){
				swal({
				  title	: "Error Message!",
				  text	: 'File DETAIL PLUS kosong ...',
				  type	: "warning"
				});
				$('#upload_process').prop('disabled',false);
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
						var baseurl		= base_url + active_controller +'/process_upload';
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
									$('#upload_process').prop('disabled',false);
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
								$('#upload_process').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#upload_process').prop('disabled',false);
					return false;
				  }
			});
		});
	});
</script>
