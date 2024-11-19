<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Supplier Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_supplier','name'=>'nm_supplier','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Supplier Name'), $row[0]['nm_supplier']);
						echo form_input(array('type'=>'hidden','id'=>'id_supplier','name'=>'id_supplier','class'=>'form-control input-md','autocomplete'=>'off'), $row[0]['id_supplier']);

					?>
				</div>
				<label class='label-control col-sm-2'><b>Country<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('type'=>'hidden','id'=>'id_negara2','name'=>'id_negara2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Supplier Name'), $row[0]['id_negara']);
					?>
					<select name='id_negara' id='id_negara' class='form-control input-md' disabled>
						<option value=''>Select An Country</option>
					<?php
						foreach($id_negara AS $val => $valx){
							$sel = ($row[0]['id_negara'] == $valx['country_code'])?'selected':'';
							echo "<option value='".$valx['country_code']."' ".$sel.">".strtoupper(strtolower($valx['country_name']))." (".$valx['country_code'].")</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Province <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_prov' id='id_prov' class='form-control input-md'>
						<?php
						foreach($id_prov AS $val => $valx){
							$sel = ($row[0]['id_prov'] == $valx['id_prov'])?'selected':'';
							echo "<option value='".$valx['id_prov']."' ".$sel.">".strtoupper(strtolower($valx['nama']))."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>District <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_kab' id='id_kab' class='form-control input-md'>
						<?php
						foreach($id_kab AS $val => $valx){
							$sel = ($row[0]['id_kab'] == $valx['id_kab'])?'selected':'';
							echo "<option value='".$valx['id_kab']."' ".$sel.">".strtoupper(strtolower($valx['nama']))."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Currency <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='mata_uang' id='mata_uang' class='form-control input-md'>
						<option value=''>Select An Currency</option>
					<?php
						foreach($mata_uang AS $val => $valx){
							$sel = ($row[0]['mata_uang'] == $valx['kode'])?'selected':'';
							echo "<option value='".$valx['kode']."' ".$sel.">".strtoupper(strtolower($valx['kode']))." (".strtoupper(strtolower($valx['negara'])).")</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Telephone <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'telpon','name'=>'telpon','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Telephone'), $row[0]['telpon']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Telephone 2</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'telpon2','name'=>'telpon2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Telephone 2'), $row[0]['telpon2']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Telephone 3</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'telpon3','name'=>'telpon3','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Telephone 3'), $row[0]['telpon3']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Fax</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'fax','name'=>'fax','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Fax'), $row[0]['fax']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Email <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Email'), $row[0]['email']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Email 2</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'email2','name'=>'email2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Email 2'), $row[0]['email2']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Email 3</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'email3','name'=>'email3','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Email 3'), $row[0]['email3']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Contact Person <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'cp','name'=>'cp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Contact Person'), $row[0]['cp']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Contact <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'hp_cp','name'=>'hp_cp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Contact'), $row[0]['hp_cp']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Webchat ID</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'id_webchat','name'=>'id_webchat','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Webchat ID'), $row[0]['id_webchat']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Tax ID Number <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'npwp','name'=>'npwp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Tax ID Number'),$row[0]['npwp']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Address</b></label>
				<div class='col-sm-4'>

					<?php
						echo form_textarea(array('id'=>'alamat','name'=>'alamat','class'=>'form-control input-md','rows'=>'2','cols'=>'75','autocomplete'=>'off','placeholder'=>'Address'), $row[0]['alamat']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Tax ID Address</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_textarea(array('id'=>'alamat_npwp','name'=>'alamat_npwp','class'=>'form-control input-md','rows'=>'2','cols'=>'75','autocomplete'=>'off','placeholder'=>'Tax ID Address'), $row[0]['alamat_npwp']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Information</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_textarea(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','rows'=>'3','cols'=>'75','autocomplete'=>'off','placeholder'=>'Information'), $row[0]['keterangan']);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Bank Account</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'data_bank','name'=>'data_bank','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Bank Account'),$row[0]['data_bank']);
					?>
				</div>
				<label class='label-control col-sm-2'><br><b>Flag Active <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'><br>
					<select name='sts_aktif' id='sts_aktif' class='form-control input-md'>
						<?php
							$selAct	= ($row[0]['sts_aktif'] == 'aktif')?'selected':'';
							$selNon	= ($row[0]['sts_aktif'] == 'nonaktif')?'selected':'';
						?>
						<option value='aktif' <?= $selAct;?>>Active</option>;
						<option value='nonaktif' <?= $selNon;?>>Non-Active</option>;
					</select>
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
	#id_negara_chosen{
		width: 100% !important;
	}
	#id_prov_chosen{
		width: 100% !important;
	}
	#id_kab_chosen{
		width: 100% !important;
	}
	#mata_uang_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#telpon').mask('?9999-9999-99999');
		$('#telpon2').mask('?9999-9999-99999');
		$('#telpon3').mask('?9999-9999-99999');
		$('#fax').mask('?99-999-9999999');
		$('#hp_cp').mask('?9999-9999-99999');
		$('#npwp').mask('?99.999.999.9-999.99');
		$(document).on('change', '#id_negara', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+ active_controller+'/getProvince',
				cache: false,
				type: "POST",
				data: "id_negara="+this.value,
				dataType: "json",
				success: function(data){
					$("#id_prov").html(data.option).trigger("chosen:updated");
				}
			});
		});

		$(document).on('change', '#id_prov', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+ active_controller+'/getDistrict',
				cache: false,
				type: "POST",
				data: "id_prov="+this.value,
				dataType: "json",
				success: function(data){
					$("#id_kab").html(data.option).trigger("chosen:updated");
				}
			});
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			var nm_supplier	= $('#nm_supplier').val();
			var id_negara	= $('#id_negara').val();
			var id_prov		= $('#id_prov').val();
			var id_kab		= $('#id_kab').val();
			var mata_uang	= $('#mata_uang').val();
			var telpon		= $('#telpon').val();
			var fax			= $('#fax').val();
			var email		= $('#email').val();
			// var email2		= $('#email2').val();
			// var email3		= $('#email3').val();
			var cp			= $('#cp').val();
			var hp_cp		= $('#hp_cp').val();
			var id_webchat	= $('#id_webchat').val();
			var npwp		= $('#npwp').val();
			var alamat		= $('#alamat').val();
			var alamat_npwp	= $('#alamat_npwp').val();
			var keterangan	= $('#keterangan').val();

			$(this).prop('disabled',true);

			if(nm_supplier	=='' || nm_supplier	==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Supplier Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;

			}
			if(id_negara	 == '' || id_negara	 == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Country Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(id_prov		 == '' || id_prov		 == null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Province Name is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			// if(id_kab		=='' || id_kab		==null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'District Name is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;

			// }
			if(mata_uang == '' || mata_uang == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Currency is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(telpon == '' || telpon == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Telephone is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(fax=='' || fax==null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Fax is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(email == '' || email == null || email == '-' || validateEmail(email) == false){
				swal({
				  title	: "Error Message!",
				  text	: 'Email Empty / Incorrect is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(validateEmail(email2) == false){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Empty / Incorrect is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			// if(validateEmail(email3) == false){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Empty / Incorrect is Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(cp == '' || cp == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Contact Name Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(hp_cp == '' || hp_cp == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Contact Person Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(id_webchat == '' || id_webchat == null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Webchat IDe Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(npwp == '' || npwp == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Tax ID Number Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(alamat == '' || alamat == null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Address Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			// if(alamat_npwp == '' || alamat_npwp == null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Tax Id Address Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			// if(keterangan == '' || keterangan == null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Information Empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }

			// alert('Success Validate');
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
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									window.location.href = base_url + active_controller;
								}
								if(data.status == 2 || data.status == 3){
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
