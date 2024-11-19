<?php
$this->load->view('include/side_menu');
// echo"<pre>";print_r($CustList);
// echo "</pre>";
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>IPP Number<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'IPP Number'));
					?>				</div>
				<label class='label-control col-sm-2'><b>PO Number</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'no_po','name'=>'no_po','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'PO Number'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>SO Number<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'no_so','name'=>'no_so','class'=>'form-control input-md','placeholder'=>'SO Number'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Customer Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_customer' id='id_customer' class='form-control input-md'>
						<option>Select An Customer</option>
					 <?php
						foreach($CustList AS $val => $valx){
							echo "<option value='".$valx['id_customer']."'>".$valx['nm_customer']."</option>";
						}
					 ?>
					 </select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Issue Date <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'tgl_rilis','name'=>'tgl_rilis','class'=>'form-control input-md','placeholder'=>'Issue date','readonly'=>'readonly'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Due Date <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'tgl_akhir','name'=>'tgl_akhir','class'=>'form-control input-md','placeholder'=>'Due Date','readonly'=>'readonly'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Project'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Information</b></label>
				<div class='col-sm-4'>
					 <?php
						echo form_textarea(array('id'=>'ket','name'=>'ket','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Information'));
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
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
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
		$('#tgl_rilis').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$('#tgl_akhir').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var no_ipp		= $('#no_ipp').val();
			var no_po		= $('#no_po').val();
			var no_so		= $('#no_so').val();
			var id_customer	= $('#id_customer').val();
			var tgl_rilis	= $('#tgl_rilis').val();
			var tgl_akhir	= $('#tgl_akhir').val();
			var project		= $('#project').val();

			if(no_ipp=='' || no_ipp==null || no_ipp=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'IPP Number, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(no_po=='' || no_po==null || no_po=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'PO Number, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(no_so=='' || no_so==null || no_so=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'SO Number, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_customer=='' || id_customer==null || id_customer=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Customer Name, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(tgl_rilis=='' || tgl_rilis==null || tgl_rilis=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Issue Date, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(tgl_akhir=='' || tgl_akhir==null || tgl_akhir=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Due Date, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(project=='' || project==null || project=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Project Name, please input first ...',
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
						var baseurl=base_url + active_controller +'/add';
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
								else if(data.status == 4){
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
