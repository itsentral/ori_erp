<?php
$id_bq = $this->uri->segment(3);
$tanda = $this->uri->segment(4);

$qSupplier 	= "	SELECT a.* FROM cost_engine a WHERE a.id = '".$tanda."'";
$row	= $this->db->query($qSupplier)->result_array();
?>

<div class="box-body">
	<div class='form-group row'>
		<label class='label-control col-sm-5'><b>PERCEN OF COST OF PRODUCT<span class='text-red'>*</span></b></label>
		<div class='col-sm-7'>
			<?php
				echo form_input(array('id'=>'value1','name'=>'value1','class'=>'form-control input-md numberOnlyT','autocomplete'=>'off'), floatval($row[0]['value1']));
				echo form_input(array('type'=>'hidden','id'=>'idbq','name'=>'idbq','class'=>'form-control input-md numberOnlyT','autocomplete'=>'off'), $this->uri->segment(3));
			
				echo form_input(array('type'=>'hidden','id'=>'value2','name'=>'value2','class'=>'form-control input-md numberOnlyT','autocomplete'=>'off'), floatval($row[0]['value2']));
				echo form_input(array('type'=>'hidden','id'=>'sum1','name'=>'sum1','class'=>'form-control input-md','autocomplete'=>'off','maxlength'=>'10', 'readonly'=>'readonly'));
			?>			
		</div>
	</div>
	<div class='form-group row'>
		<div class='col-sm-12'>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','id'=>'btn-set1','value'=>'Update','content'=>'Update'));
			?>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		swal.close();
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var sum	= value1 + value2;
		$('#sum1').val(sum.toFixed(2));
	});
	$(".numberOnlyT").on("keypress keyup blur",function (event) {    
		// $(this).val($(this).val().replace(/[^\d].+/, "")); // 
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 ))  {
			event.preventDefault();
		}
	}); 
	$(document).on('keyup','#value1', function(){
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var sum	= value1 + value2;
		$('#sum1').val(sum.toFixed(2));
	});
	$(document).on('keyup','#value2', function(){
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var sum	= value1 + value2;
		$('#sum1').val(sum.toFixed(2));
	});
	
	$(document).on('click', '#btn-set1', function(e){
		e.preventDefault();
		$(this).prop('disabled',true);

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
					var baseurl=base_url + active_controller +'/update_set_g';
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
									  timer	: 3000
									});
								window.location.href = base_url + active_controller +'/priceProcessCost/'+data.id_bq;
							}
							else if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#btn-set1').prop('disabled',false);
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
							$('#btn-set1').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#saved_data').prop('disabled',false);
				return false;
			  }
		});
	});
</script>