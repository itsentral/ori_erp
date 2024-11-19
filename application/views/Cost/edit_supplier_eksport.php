<?php
$this->load->view('include/side_menu'); 
// echo"<pre>";print_r($row);
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header --> 
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Country</b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'nama','name'=>'nama','class'=>'form-control input-md','readonly'=>'readonly'), strtoupper(get_name('country','country_name','country_code',$row[0]['country_code'])));	
						
					?>	
				</div>
				<label class='label-control col-sm-2'><b>Shipping Name</b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'shipping','name'=>'shipping','class'=>'form-control input-md','readonly'=>'readonly'), strtoupper($row[0]['shipping_name']));	
						echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id','class'=>'form-control input-md'), $row[0]['id']);
					?>	
				</div>
			</div>
			
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Price From Supplier ($) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'price_supplier','name'=>'price_supplier','class'=>'form-control input-md autoNumeric3','autocomplete'=>'off','placeholder'=>'Price From Supplier'), $row[0]['price_supplier']);											
					?>
				</div>
				<label class='label-control col-sm-2'><b>Expired Price <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'expired_supplier','name'=>'expired_supplier','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'Expired Price'), $row[0]['expired_supplier']);											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Note</b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_textarea(array('id'=>'note_sup','name'=>'note_sup','class'=>'form-control input-md','rows'=>'3'), $row[0]['note_sup']);											
					?>
				</div>
			</div>
		</div>
		
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','style'=>'width:80px; margin-left:10px;','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','id'=>'back','style'=>'width:80px; margin-left:5px;','content'=>'Back'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<style type="text/css">
	.tgl{
		cursor:pointer;
	}
</style>
<script>
	
	$(document).ready(function(){
		$('.autoNumeric3').autoNumeric({
			mDec: '2'
		});
		$('.tgl').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
		
		$(document).on('click', '#back', function(){
			window.location.href = base_url + active_controller+'/supplier';
		});
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			
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
						var baseurl		= base_url + active_controller +'/edit_supplier_eksport';
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
									window.location.href = base_url + active_controller+'/supplier';
								}
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
								}
								$('#simpan-bro').prop('disabled',false);
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
