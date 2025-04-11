<?php
$this->load->view('include/side_menu'); 

$id				= (!empty($data))?$data[0]->id:'';
$kode			= (!empty($data))?$data[0]->kode:'';
$product_parent	= (!empty($data))?strtolower($data[0]->product_parent):'0';
$diameter		= (!empty($data))?number_format($data[0]->diameter):'';
$diameter2		= (!empty($data))?number_format($data[0]->diameter2):'';
$pn				= (!empty($data))?$data[0]->pn:'0';
$linerx			= (!empty($data))?$data[0]->liner:'0';
$id_mesin		= (!empty($data))?$data[0]->id_mesin:'0';
$standard_length= (!empty($data))?number_format($data[0]->standard_length):'';
$man_power		= (!empty($data))?number_format($data[0]->man_power):'';
$total_time		= (!empty($data))?number_format($data[0]->total_time,2):'';

$disabled		= (!empty($data))?'disabled':'';
?> 
<form action="#" method="POST" id="form_proses_bro" autocomplete='off'>   
	<input type='hidden' name='id' value='<?=$id;?>'>
	<input type='hidden' name='kode' value='<?=$kode;?>'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Product <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='product_parent' id='product_parent' class='form-control input-sm' <?=$disabled;?>>
						<option value='0'>Select Product</option>
					<?php
						foreach($resin_system AS $val => $valx){
							$sel = ($valx['product_parent'] == $product_parent)?'selected':'';
							echo "<option value='".$valx['product_parent']."' ".$sel.">".strtoupper($valx['product_parent'])."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Diameter 1 /mm <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-md maskM','placeholder'=>'Diameter 1','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'',$disabled=>$disabled),$diameter);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Diameter 2 /mm</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-md maskM','placeholder'=>'Diameter 2','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'',$disabled=>$disabled),$diameter2);
					?>
				</div>
			</div>
			<div class='form-group row'>	
				<label class='label-control col-sm-2'><b>Pressure <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='pn' id='pn' class='form-control input-sm' <?=$disabled;?>>
						<option value='0'>Select Pressure</option>
					<?php
						foreach($pressure AS $val => $valx){
							$KdPressure		= sprintf('%02s',$valx['name']);
							$sel = ($valx['name'] == $pn)?'selected':'';
							echo "<option value='".$valx['name']."' ".$sel.">PN ".ucfirst(strtolower($KdPressure))."</option>";
						}
					 ?>
					</select>
				</div>
				
				<label class='label-control col-sm-2'><b>Liner <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='liner' id='liner' class='form-control input-sm' <?=$disabled;?>>
						<option value='0'>Select Liner</option>
					<?php
						foreach($liner AS $val => $valx){
							$sel = ($valx['name'] == $linerx)?'selected':'';
							echo "<option value='".$valx['name']."' ".$sel.">".ucfirst(strtolower($valx['name']))." mm</option>";
						}
					 ?>
					</select>
				</div>	
			</div>
			<div class='form-group row'>	
				<label class='label-control col-sm-2'><b>Machine</b></label>
				<div class='col-sm-4'>              
					<select name='id_mesin' id='id_mesin' class='form-control input-sm'>
						<option value='0'>Select Machine</option>
					<?php
						foreach($machine AS $val => $valx){
							$sel = ($valx['no_mesin'] == $id_mesin)?'selected':'';
							echo "<option value='".$valx['no_mesin']."' ".$sel."> ".strtoupper(strtolower($valx['nm_mesin']))."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Standard length /mm</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'standard_length','name'=>'standard_length','class'=>'form-control input-md maskM','placeholder'=>'Standard length','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>''),$standard_length);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Man Power <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'man_power','name'=>'man_power','class'=>'form-control input-md maskM','placeholder'=>'Man Power','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>''),$man_power);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Total Time <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'total_time','name'=>'total_time','class'=>'form-control input-md autoNumeric','placeholder'=>'Total Time'),$total_time);
					?>
				</div>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','id'=>'back','content'=>'Back'));
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
		$('.maskM').maskMoney();
		
		$(document).on('click', '#back', function(e){
			window.location.href = base_url + active_controller +'/cycletime';
		});
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var product_parent	= $('#product_parent').val();
			var diameter		= $('#diameter').val();
			var pn				= $('#pn').val();
			var liner			= $('#liner').val();
			var man_power		= $('#man_power').val();
			var total_time		= $('#total_time').val();
			
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
				  text	: 'Pressure is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(pn == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Pressure is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(liner=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Liner is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(man_power=='' || man_power==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Man Power is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(total_time == '' || total_time == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Total Time is Empty, please input first ...',
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
						var baseurl		= base_url + active_controller +'/add_cycletime';
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
									window.location.href = base_url + active_controller +'/cycletime';
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
