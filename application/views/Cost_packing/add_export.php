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
				<label class='label-control col-sm-2'><b>Country Destination<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='country_code' id='country_code' class='form-control input-md'>
						<option value='0'>Select Country Destination</option>
					 <?php
						foreach($CountryName AS $val => $valx){
							$selx = ($data[0]['country_code'] == $valx['country_code'])?'selected':'';
							echo "<option value='".$valx['country_code']."' ".$selx.">".$valx['country_name']."</option>";
						}
					 ?>
					 </select>
					 <?php
						echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id'), $tanda);
					?>	
					<br>
					<button type='button' id='addCountry' style='font-weight: bold; font-size: 12px; margin-top: 5px; color: #175477;'>Add Country</button>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Shipping Method<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='shipping_name' id='shipping_name' class='form-control input-md'>
						<option value='0'>Select Shipping Method</option>
					 <?php
						foreach($ShippingName AS $val => $valx){
							$selxc = ($data[0]['shipping_name'] == $valx['shipping_name']." ".$valx['type'])?'selected':'';
							echo "<option value='".$valx['shipping_name']." ".$valx['type']."' ".$selxc.">".$valx['shipping_name']." ".$valx['type']."</option>";
						}
					 ?>
					 </select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Price USD<span class='text-red'>*</span></b></label> 
				<div class='col-sm-2'>             
						<?php
							echo form_input(array('id'=>'price','name'=>'price','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Price USD','maxlength'=>'10'),(!empty($tanda))?floatval($data[0]['price']):'');											
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
  <!-- modal -->
	<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:40%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
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
		
		$(document).on('click', '#addCountry', function(e){
			e.preventDefault();
			$("#head_title").html("<b>ADD COUNTRY</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalAddCountry/');
			$("#ModalView").modal();
		});
		
		$(document).on('click','#kembali', function(){
			window.location.href = base_url + active_controller + "/export";
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var country_code	= $('#country_code').val();
			var shipping_name		= $(shipping_name).val();
			var price			= $('#price').val();
			
			if(country_code=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Product is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(shipping_name=='0' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(price=='' || price==null){
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
						var baseurl		= base_url + active_controller +'/add_export';
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
									window.location.href = base_url + active_controller +'/export';
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
			
			var country_code	= $('#country_code').val();
			var shipping_name		= $(shipping_name).val();
			var price			= $('#price').val();
			
			if(country_code=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Product is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(shipping_name=='0' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(price=='' || price==null){
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
						var baseurl		= base_url + active_controller +'/edit_export';
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
									window.location.href = base_url + active_controller +'/export';
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
		
		$(document).on('click', '#addPSave', function(){
			var standart_code			= $('#country').val();
			
			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Country Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addPSave').prop('disabled',false);
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
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/addCountry',
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
									  timer	: 5000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								window.location.href = base_url + active_controller +'/add_export';
								
								
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							}
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
	});
</script>
