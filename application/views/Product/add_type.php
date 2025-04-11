<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
?>
<form action="#" method="POST" id="form_proses_bro" autocomplete='off'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Product Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'product_parent','name'=>'product_parent','class'=>'form-control input-md number_alfabet','autocomplete'=>'off','placeholder'=>'Product Name'));
					?>				
				</div>
				<label class='label-control col-sm-2'><b>Type Product</b></label>
				<div class='col-sm-2'>
					<select name='type' id='type' class='form-control input-md'>
						<option value='fitting'>Fitting</option>
						<?php
							foreach($type AS $val => $valx){
								$name_only = ($valx['type'] == 'field')?'Material Only':$valx['type'];
								echo "<option value='".$valx['type']."'>".ucwords(strtolower($name_only))."</option>";
							}
						?>
					</select>
				</div>
				<div class='col-sm-2'>
					<select name='type2' id='type2' class='form-control input-md'>
						<option value='custom'>Custom</option>
					</select>
				</div>
			</div>
			<div class='form-group row'>
                <label class='label-control col-sm-2'><b>Code Uniq <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					 <?php
						echo form_input(array('id'=>'code','name'=>'code','class'=>'form-control input-md alfabet','maxlength'=>'2','placeholder'=>'Digunakan untuk kode product, hanya 2 karakter'));
					?>
				</div>

				<label class='label-control col-sm-2'><b>Info/Est</b></label>
				<div class='col-sm-2'>
                    <select name='ket' id='ket' class='form-control input-md'>
						<option value='Y'>Product</option>
                        <!--<option value='Slong'>Product Slongsong</option>-->
					</select>
				</div>
				<div class='col-sm-2'>
                    <select name='estimasi' id='estimasi' class='form-control input-md'>
						<option value='Y'>Yes</option>
                        <!--<option value='N'>No</option>-->
					</select>
				</div>	
			</div>
			<div class='form-group row'>
                <label class='label-control col-sm-2'><b>Spec 1 | 2 <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					 <?php
						echo form_input(array('id'=>'spec1','name'=>'spec1','class'=>'form-control input-md','placeholder'=>'Ex. Diameter1, Alas, dll'));
					?>
				</div>
				<div class='col-sm-2'>
					 <?php
						echo form_input(array('id'=>'spec2','name'=>'spec2','class'=>'form-control input-md','placeholder'=>'Ex. Diameter2, Tinggi, dll'));
					?>
				</div>
				
			</div>
		</div>
		<div class='box-footer'>
			<?php
            echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:10px;','id'=>'back','content'=>'Back'));
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
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
	#type_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#phone').mask('?999-9999999999');
		$('#fax').mask('?999-9999999999');
		
		$(".number_alfabet").on("keypress keyup blur",function (event) {
			if ((event.which < 48 || event.which > 57 ) && (event.which < 32 || event.which > 32) && (event.which < 65 || event.which > 90) && (event.which < 97 || event.which > 122)) {
				event.preventDefault();
			}
		});
		
		$(".alfabet").on("keypress keyup blur", function (event) {
			if ((event.which < 65 || event.which > 90) && (event.which < 97 || event.which > 122)) {
				event.preventDefault();
			}
		});

        $(document).on('click','#back', function(){
            window.location.href = base_url + active_controller+'/type';
        });
		

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var parent_product	= $('#product_parent').val();
			var code			= $('#code').val();
			var spec1			= $('#spec1').val();
			var spec2			= $('#spec2').val();

			if(parent_product=='' || parent_product==null || parent_product=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Type Product, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(code=='' || code==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Code Uniq, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(spec1=='' || spec1==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Spec1, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			// if(spec2=='' || spec2==null){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Empty Spec2, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }

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
						var baseurl=base_url + active_controller +'/add_type';
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
									window.location.href = base_url + active_controller+'/type';
								}
								else if(data.status == 2){
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
