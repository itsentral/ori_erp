
<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr>
					<td class="text-left">Input Step</td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'step_name','name'=>'step_name','class'=>'form-control input-sm'));
						?>
					</td>
				</tr>
			</tbody>
		</table><br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'addStepSave')).' ';
		?>
	</div>
</div>

<style>
	.inSp{
		text-align: center;
	}
	.inSpL{
		text-align: left;
	}

</style>

<script>
	$(document).on('click', '#addStepSave', function(){
		var step_name			= $('#step_name').val();

		if(step_name == '' || step_name == null || step_name == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Step Input is Empty, please input first ...',
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
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/addStepSave_Master',
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
							// window.location.href = base_url + active_controller+'/'+data_url;
							$("#ModalView2").modal('hide');
							$("#head_title").html("<b>ADD DEFAULT</b>");
							/*$("#select_step").each(function() {

							});*/
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalAdd_Step/');
							$("#ModalView").modal();


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
</script>
