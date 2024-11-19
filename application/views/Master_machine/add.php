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
				<label class='label-control col-sm-2'><b>Nomor Mesin <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'no_mesin','name'=>'no_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Nomor Mesin'));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Nama Mesin <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Nama Mesin'));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Kapasitas <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'capacity','name'=>'capacity','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Kapasitas'));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Satuan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='unit' id='unit' class='form-control input-md'>
						<option value='0'>Pilih Satuan</option>
						<?php
							foreach($satuan AS $val => $valx){
								echo "<option value='".strtolower($valx['kode_satuan'])."'>".strtolower($valx['kode_satuan'])."</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Harga Mesin (USD) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'machine_price','name'=>'machine_price','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Harga Mesin (USD)'));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Est. Pemanfaatan (Tahun) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'utilization_estimate','name'=>'utilization_estimate','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Est. Pemanfaatan (Tahun)'));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Depresiasi /Bulan (USD)</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'depresiation_per_month','name'=>'depresiation_per_month','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Depresiasi /Bulan (USD)'));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Biaya Mesin /Jam (USD)</b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'machine_cost_per_hour','name'=>'machine_cost_per_hour','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Biaya Mesin /Jam (USD)'));											
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
		
		$("#machine_price, #utilization_estimate").keyup(function(){
			var harga 		= $("#machine_price").val();
			var dep			= $("#utilization_estimate").val() * 12;
			
			var dep_mo		= parseFloat(harga) / parseFloat(dep);
			var by_mesin	= dep_mo / 173;
			
			$("#depresiation_per_month").val(dep_mo.toFixed(2));
			$("#machine_cost_per_hour").val(by_mesin.toFixed(2));
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var no_mesin			= $('#no_mesin').val();
			var nm_mesin			= $('#nm_mesin').val();
			var capacity			= $('#capacity').val();
			var unit				= $('#unit').val();
			var machine_price		= $('#machine_price').val();
			var utilization_est		= $('#utilization_estimate').val();
			
			if(no_mesin=='' || no_mesin==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Machine Number is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(nm_mesin=='' || nm_mesin==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Machine Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(capacity == '' || capacity == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Capacity, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(unit == '' || unit == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Unit/ Satuan Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(machine_price == '' || machine_price == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Machine Price Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(utilization_est == '' || utilization_est == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Utilization Est Empty, please input first ...',
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
