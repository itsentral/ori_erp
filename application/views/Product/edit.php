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
				<label class='label-control col-sm-2'><b>Product Name<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_product','name'=>'nm_product','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Product Name'),$data[0]['nm_product']);
						echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id','class'=>'form-control input-md'),$data[0]['id']);
					?>				
				</div>
				<label class='label-control col-sm-2'><b>Type Product<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='parent_product' id='parent_product' class='form-control input-md'>
						<?php
							foreach($type AS $val => $valx){
								$Sel	= ($data[0]['parent_product'] == $valx['product_parent'])?'selected':'';
								echo "<option value='".$valx['product_parent']."' ".$Sel.">".ucwords(strtolower($valx['product_parent']))."</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
					 echo form_input(array('id'=>'value_d','name'=>'value_d','class'=>'form-control input-md numberOnly','placeholder'=>'Diameter'),$data[0]['value_d']);
					?>
				</div>
				<div class='col-sm-2'>
					<?php
					 echo form_input(array('id'=>'value_d2','name'=>'value_d2','class'=>'form-control input-md numberOnly','placeholder'=>'Diameter 2'),$data[0]['value_d2']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Information</b></label>
				<div class='col-sm-4'>
					 <?php
						// echo form_hidden('id',$row[0]->kode_divisi);
						echo form_textarea(array('id'=>'ket','name'=>'ket','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Product Information'),$data[0]['ket']);
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
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#phone').mask('?999-9999999999');
		$('#fax').mask('?999-9999999999');
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
			// if($(this).val() == ''){
				// $(this).val(0);
			// }
		});
		

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var nm_product		= $('#nm_product').val();
			var parent_product	= $('#parent_product').val();
			var value_d			= $('#value_d').val();
			var ket				= $('#ket').val();

			if(nm_product=='' || nm_product==null || nm_product=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Product Name, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(parent_product=='' || parent_product==null || parent_product=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Type Product, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(value_d == '' || value_d == null || value_d == '-' || value_d == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Diameter, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(ket=='' || ket==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Informaton, please input first ...',
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
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/edit';
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
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								else if(data.status == 3){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
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
