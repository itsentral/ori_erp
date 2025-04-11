<?php
$id = $this->uri->segment(3);
// echo $id;
$product	= $this->db->query("SELECT * FROM product_parent WHERE estimasi='Y' ORDER BY product_parent ASC")->result_array();

$getData	= $this->db->query("SELECT * FROM mould_mandrill WHERE id='".$id."'")->row();
?>

<div class="box box-success">
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Komponen <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='product_parentx' id='product_parentx' class='form-control input-md' disabled>
						<option value='0'>Pilih Komponent</option>
						<?php
							foreach($product AS $val => $valx){
								$selx = ($getData->product_parent == $valx['product_parent'])?'selected':'';
								echo "<option value='".strtolower($valx['product_parent'])."' ".$selx.">".strtoupper($valx['product_parent'])."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Dimensi <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('type'=>'hidden','id'=>'product_parent','name'=>'product_parent'),$getData->product_parent);
							echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id'),$getData->id);
							echo form_input(array('id'=>'dimensi','name'=>'dimensi','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Dimensi'),floatval($getData->dimensi));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Diameter 1 <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Diameter 1'),floatval($getData->diameter));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Diameter 2 <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Diameter 2'),floatval($getData->diameter2));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Harga (USD) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'harga','name'=>'harga','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Harga (USD)'),floatval($getData->harga));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Est Pemakaian (Pcs)</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'est_pakai','name'=>'est_pakai','class'=>'form-control input-md numberOnly','placeholder'=>'Est Pemakaian (Pcs)'),floatval($getData->est_pakai));											
						?>		
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Biaya Per Pcs (USD)</b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'biaya_per_pcs','name'=>'biaya_per_pcs','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Biaya Per Pcs (IDR)'),$getData->biaya_per_pcs);											
						?>		
				</div>
			</div>	
			<div class='box-footer'>
				<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
				?>
			</div>			
		</div>
	 </div>
</div>
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

<script>
	$(document).ready(function(){
		swal.close();

		$(".numberOnly").on("keypress keyup blur",function (event) {
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		
		$("#harga, #est_pakai").keyup(function(){
			var harga 		= $("#harga").val();
			var est_pakai	= $("#est_pakai").val();
			
			var dep_mo		= parseFloat(harga) / parseFloat(est_pakai);
			
			$("#biaya_per_pcs").val(dep_mo.toFixed(2));
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var dimensi			= $('#dimensi').val();
			var product_parent	= $('#product_parent').val();
			var diameter		= $('#diameter').val();
			var diameter2		= $('#diameter2').val();
			var harga			= $('#harga').val();
			var est_pakai		= $('#est_pakai').val();
			var biaya_per_pcs	= $('#biaya_per_pcs').val();
			
			if(harga=='' || harga==null || harga=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Price is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(biaya_per_pcs == '' || biaya_per_pcs == null || biaya_per_pcs=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Preice per Pcs is empty, please input first ...',
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