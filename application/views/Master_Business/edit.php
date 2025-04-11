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
						echo form_hidden('id_bidang_usaha',$rows[0]->id_bidang_usaha);
						echo form_input(array('id'=>'bidang_usaha','name'=>'bidang_usaha','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Business Fields','style'=>'text-transform:uppercase'),$rows[0]->bidang_usaha);										
					?>		
				</div>
				<label class='label-control col-sm-2'><b>Description <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_textarea(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','cols'=>'75','rows'=>'1','autocomplete'=>'off','placeholder'=>'Description','style'=>'text-transform:uppercase'),strtoupper($rows[0]->keterangan));										
					?>		
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
<script>
	$(document).ready(function(){		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var nama	= $('#bidang_usaha').val();
			var lokasi	= $('#keterangan').val();
			if(nama=='' || nama==null || nama=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Business Fields, please input business fields first.....',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			
			if(lokasi=='' || lokasi==null || lokasi=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Business Fields Description, please input business  fields description first.....',
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
				  closeOnConfirm: true,
				  closeOnCancel: false
				},
				function(isConfirm) {
				  if (isConfirm) {
						loading_spinner();
						var formData 	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/edit';
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
								}else{
									
									if(data.status == 2){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									}else{
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
