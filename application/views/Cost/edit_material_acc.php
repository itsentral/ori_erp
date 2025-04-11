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
				<label class='label-control col-sm-2'><b>Material Name</b></label>
				<div class='col-sm-5'>              
					<?php
						echo form_input(array('id'=>'nama','name'=>'nama','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material ID','readonly'=>'readonly'), strtoupper($row[0]['nama']));	
						
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Spesifikasi</b></label>
				<div class='col-sm-5'>              
					<?php
						echo form_input(array('id'=>'nm_material','name'=>'nm_material','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material Name','readonly'=>'readonly'), get_name_acc($row[0]['id'])); 
						echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id','class'=>'form-control input-md'), $row[0]['id']);
						echo form_input(array('type'=>'hidden','id'=>'profit','name'=>'profit','class'=>'form-control input-md'), $row[0]['profit']);
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'><span class='text-red font-weight-bold'><b>BEFORE</b></span></div>
				<div class='col-sm-5'><span class='text-green font-weight-bold'><b>AFTER</b></span></div>
			</div>
			
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Price From Purchase ($)</b></label>
				<div class='col-sm-5'>          
					<?php
						echo form_input(array('id'=>'price_ref_purchase','name'=>'price_ref_purchase','class'=>'form-control input-md autoNumeric3','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Price Ref Purchase'), number_format($row[0]['price_ref_purchase'],3));											
					?>	
				</div>
				<div class='col-sm-5'>          
					<?php
						echo form_input(array('id'=>'price_from_supplier','name'=>'price_from_supplier','class'=>'form-control input-md autoNumeric3','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Price From Supplier'), number_format($row[0]['price_from_supplier'],3));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Expired</b></label>
				<div class='col-sm-5'>          
					<?php
						echo form_input(array('id'=>'exp_price_ref_pur','name'=>'exp_price_ref_pur','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Exp Price Ref Purchase'), $row[0]['exp_price_ref_pur']);											
					?>	
				</div>
				<div class='col-sm-5'>          
					<?php
						echo form_input(array('id'=>'exp_price_ref_sup','name'=>'exp_price_ref_sup','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Exp Price Ref Purchase'), $row[0]['exp_price_ref_sup']);											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Price Ref Estimation ($) <span class='text-red'>*</span></b></label>
				<div class='col-sm-5'>             
					<?php
						echo form_input(array('id'=>'price_ref_estimationx','name'=>'price_ref_estimationx','class'=>'form-control input-md autoNumeric3','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Price Ref Estimation'), number_format($row[0]['harga'],3));											
					?>
				</div>
				<div class='col-sm-5'>             
					<?php
						echo form_input(array('id'=>'price_ref_estimation','name'=>'price_ref_estimation','class'=>'form-control input-md autoNumeric3','autocomplete'=>'off','placeholder'=>'Price Ref Estimation'));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Expired</b></label>
				<div class='col-sm-5'>             
					<?php
						echo form_input(array('id'=>'exp_price_ref_estx','name'=>'exp_price_ref_estx','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Exp Price Ref Estimation'), $row[0]['exp_price_ref_est']);											
					?>
				</div>
				<div class='col-sm-5'>             
					<?php
						echo form_input(array('id'=>'exp_price_ref_est','name'=>'exp_price_ref_est','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'Exp Price Ref Estimation'), $row[0]['exp_price_ref_sup']);											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Note</b></label>
				<div class='col-sm-5'>             
					<?php
						echo form_textarea(array('id'=>'ket_price','name'=>'ket_price','class'=>'form-control input-md','rows'=>'3'), $row[0]['note']);											
					?>
				</div>
				<div class='col-sm-5'>             
					<?php
						echo form_textarea(array('id'=>'ket_price_sup','name'=>'ket_price_sup','class'=>'form-control input-md','readonly'=>'readonly','rows'=>'3'), strtoupper($row[0]['note_sup']));											
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Alasan Reject <span class='text-red'>*</span></b></label>
				<div class='col-sm-5'>             
					<?php
						echo form_textarea(array('id'=>'reject_reason','name'=>'reject_reason','class'=>'form-control input-md','rows'=>'3'));											
					?>
				</div>
			</div>
		</div>
		
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','value'=>'save','style'=>'width:80px; margin-left:10px;','content'=>'Approve','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'save','style'=>'width:80px; margin-left:10px;','content'=>'Reject','id'=>'reject')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','onclick'=>'goBack()','style'=>'width:80px; margin-left:5px; float:right;','content'=>'Back'));
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
		
		// changePrice();
		
		$('.maskM').maskMoney();
		$('.tgl').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
		
		// $(document).on('click', '#back', function(){
			// window.location.href = base_url + active_controller+'/material';
		// });
		
		$(document).on('keyup', '#price_from_supplier', function(){
			changePrice();
		});
		
		$(document).on('change', '#exp_price_ref_sup', function(){
			changePrice();
		});
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			var price_ref_estimation	= $('#price_ref_estimation').val();
			
			$(this).prop('disabled',true);
			
			
			if(price_ref_estimation=='' || price_ref_estimation==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Price Ref Estimasi is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			$('#simpan-bro').prop('disabled',false);
			// return false;
			
			swal({
				  title: "Are you sure?",
				  text: "APPROVE price reference ?",
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
						var baseurl		= base_url + active_controller +'/edit_accessories';
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
									window.location.href = base_url + active_controller+'/material';
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

		$('#reject').click(function(e){
			e.preventDefault();
			var reject_reason	= $('#reject_reason').val();
			
			$(this).prop('disabled',true);
			
			
			if(reject_reason=='' || reject_reason==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Alasan reject is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			$('#simpan-bro').prop('disabled',false);
			// return false;
			
			swal({
				  title: "Are you sure?",
				  text: "REJECT price reference ?",
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
						var baseurl		= base_url + active_controller +'/reject_accessories';
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
									window.location.href = base_url + active_controller+'/material';
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
	
	const changePrice = () => {
		let price_from_supplier = getNum($('#price_from_supplier').val().split(",").join(""));
		let exp_price_ref_sup 	= $('#exp_price_ref_sup').val();
		let profit 				= getNum($('#profit').val()) / 100;
		
		let new_price = price_from_supplier * profit;
		
		$('#price_ref_estimation').val(number_format(new_price,3));
		$('#exp_price_ref_est').val(exp_price_ref_sup);
		
	}
	
	function goBack() {
	  window.history.back();
	}
	
</script>
