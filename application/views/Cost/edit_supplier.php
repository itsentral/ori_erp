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
				<label class='label-control col-sm-2'><b>Material ID</b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'idmaterial','name'=>'idmaterial','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material ID','readonly'=>'readonly'), $row[0]['idmaterial']);	
						
					?>	
				</div>
				<label class='label-control col-sm-2'><b>Material Name</b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'nm_material','name'=>'nm_material','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material Name','readonly'=>'readonly'), $row[0]['nm_material']); 
						echo form_input(array('type'=>'hidden','id'=>'id_material','name'=>'id_material','class'=>'form-control input-md'), $row[0]['id_material']);
					?>	
				</div>
			</div>
			
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Price From Supplier ($) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'price_from_supplier','name'=>'price_from_supplier','class'=>'form-control input-md autoNumeric3','autocomplete'=>'off','placeholder'=>'Price From Supplier'), $row[0]['price_from_supplier']);											
					?>
				</div>
				<label class='label-control col-sm-2'><b>Expired Price <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'exp_price_ref_sup','name'=>'exp_price_ref_sup','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'Expired Price'), $row[0]['exp_price_ref_sup']);											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Note</b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_textarea(array('id'=>'ket_price_sup','name'=>'ket_price_sup','class'=>'form-control input-md','rows'=>'3'), $row[0]['ket_price_sup']);											
					?>
				</div>
				<label class='label-control col-sm-2'><b>Price Last History</b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'bf_price_from_supplier','name'=>'bf_price_from_supplier','class'=>'form-control input-md autoNumeric3','readonly'=>'readonly','placeholder'=>'Price From Supplier Before'), number_format($last_price,3));											
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
			mDec: '3'
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
			var price_from_supplier	= $('#price_from_supplier').val();
			
			$(this).prop('disabled',true);
			
			
			// if(price_from_supplier=='' || price_from_supplier==null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Price From Supplier is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			
			$('#simpan-bro').prop('disabled',false);
			// return false;
			
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
						var baseurl		= base_url + active_controller +'/edit_supplier';
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
