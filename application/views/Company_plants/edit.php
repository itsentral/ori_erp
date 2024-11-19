<?php
$this->load->view('include/side_menu');
// echo"<pre>";print_r($branch);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Plant Name<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_plant','name'=>'nm_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['nm_plant']);
						echo form_input(array('type'=>'hidden','id'=>'id_plant','name'=>'id_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_plant']);
					?>				
				</div>
				<label class='label-control col-sm-2'><b>Initial Plants</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'inisial_plant','name'=>'inisial_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','maxlength'=>'10', 'style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['inisial_plant']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Branch<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='kdcab' id='kdcab' class='form-control'>
						<?php
							foreach($branch AS $val => $valx){
								$sel = ($row[0]['kdcab'] == $val)?'selected':'';
								echo "<option value='".$val."' ".$sel.">".$valx."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Province <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					 <select name='province' id='province' class='form-control'>
						<?php
							foreach($rows_province AS $val => $valx){
								$sel = ($row[0]['province'] == $val)?'selected':'';
								echo "<option value='".$val."' ".$sel.">".$valx."</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Phone <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'phone','name'=>'phone','class'=>'form-control input-md','placeholder'=>'User Phone'), $row[0]['phone']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Fax <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'fax','name'=>'fax','class'=>'form-control input-md','placeholder'=>'Fax Number'), $row[0]['fax']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Email <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','placeholder'=>'Email company plant'), $row[0]['email']);
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Address</b></label>
				<div class='col-sm-4'>
					 <?php
						// echo form_hidden('id',$row[0]->kode_divisi);
						echo form_textarea(array('id'=>'address','name'=>'address','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Address company plants'), $row[0]['address']);
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
		$(document).on('change', '#branch', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+ active_controller+'/getWorkLocation',
				cache: false,
				type: "POST",
				data: "branch="+this.value,
				dataType: "json",
				success: function(data){
					$("#kdcab").html(data.option).trigger("chosen:updated");
				}
			});
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var nm_plant			= $('#nm_plant').val();
			var inisial_plant	= $('#inisial_plant').val();
			var kdcab					= $('#kdcab').val();
			var address				= $('#address').val();
			var province			= $('#province').val();
			var phone					= $('#phone').val();
			var email					= $('#email').val();
			var fax						= $('#fax').val();

			if(nm_plant=='' || nm_plant==null || nm_plant=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Plant Name, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(inisial_plant=='' || inisial_plant==null || inisial_plant=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Inisial Plant, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(kdcab == '' || kdcab == null || kdcab == '-' || kdcab == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Branch, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(address=='' || address==null || address=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Address, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(province == '' || province == null || province == '-' || province == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Province, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(phone=='' || phone==null || phone=='-' || phone.length < 10){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty User Phone, please input User Phone first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;

			}
			if(email=='' || email==null || email=='-' || validateEmail(email) == false){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty / Incorrect User Email, please input User Email first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(fax=='' || fax==null || fax=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Fax, please input first ...',
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
						// loading_spinner();
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/edit/'+$('#id_plant').val();
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
								}
								else if(data.status == 2){
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
	function validateEmail(sEmail) {
		var numericExpression = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if (sEmail.match(numericExpression)) {
			return true;
		}
		else {
			return false;
		}
	}
</script>
