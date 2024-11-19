<?php
$this->load->view('include/side_menu'); 
$tanda = $this->uri->segment(3);

// echo $tanda;
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Product <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>  
						<select id='product_parent' name='product_parent' class='form-control input-sm'>
							<option value='0'>All Component</option>
							<?php
								foreach($listkomponen AS $val => $valx){
									$selx = ($data[0]['product_parent'] == $valx['product_parent'])?'selected':'';
									echo "<option value='".$valx['product_parent']."' ".$selx.">".strtoupper($valx['product_parent'])."</option>"; 
								}
							?>
						</select> 
						<?php
							echo form_input(array('type'=>'hidden','id'=>'standard_code','name'=>'standard_code','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Standard Code'), "PRODUCT-ORI");											
							echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id'), $tanda);											
						
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>             
						<?php
							echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Diameter 1'),(!empty($tanda))?$data[0]['diameter']:'');											
						?>		
				</div>
				<div class='col-sm-2'>             
						<?php
							echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Diameter 2'),(!empty($tanda))?$data[0]['diameter2']:'');											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Price <span class='text-red'>*</span></b></label> 
				<div class='col-sm-2'>             
						<?php
							echo form_input(array('id'=>'profit','name'=>'profit','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Price USD','maxlength'=>'10'),(!empty($tanda))?floatval($data[0]['profit']):'');											
						?>		
				</div>
			</div>		
		</div>
		<div class='box-footer'>
			<?php
			if(empty($tanda)){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			}
			if(!empty($tanda)){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'update','content'=>'Update','id'=>'update')).' ';
			}
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'kembali')).' ';
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<style type="text/css">
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
	#unit_chosen{
		width: 100% !important;
	}
</style>

<?php $this->load->view('include/footer'); ?> 
<script>
	$(document).ready(function(){
		$(".numberOnly").on("keypress keyup blur",function (event) {
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		
		$(document).on('click','#kembali', function(){
			window.location.href = base_url + active_controller + "/index";
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var product_parent	= $('#product_parent').val();
			var diameter		= $('#diameter').val();
			// var diameter2		= $('#diameter2').val();
			var profit			= $('#profit').val();
			
			if(product_parent=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Product is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(diameter=='' || diameter==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(diameter2=='' || diameter2==null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Diameter 2 is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(profit=='' || profit==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Profit is Empty, please input first ...',
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
						var baseurl		= base_url + active_controller +'/add_profit';
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
										  timer	: 5000
										});
									window.location.href = base_url + active_controller +'/index';
								}
								else{ 
									if(data.status == 2){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 3000
										});
									}
									else{
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 3000,
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
								  timer				: 3000,
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
		
		$('#update').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var product_parent	= $('#product_parent').val();
			var diameter		= $('#diameter').val();
			// var diameter2		= $('#diameter2').val();
			var profit			= $('#profit').val();
			
			if(product_parent=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Product is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(diameter=='' || diameter==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(diameter2=='' || diameter2==null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Diameter 2 is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(profit=='' || profit==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Profit is Empty, please input first ...',
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
						var baseurl		= base_url + active_controller +'/edit_profit';
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
										  timer	: 5000
										});
									window.location.href = base_url + active_controller +'/index';
								}
								else{ 
									if(data.status == 2){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 3000
										});
									}
									else{
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 3000,
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
								  timer				: 3000,
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
