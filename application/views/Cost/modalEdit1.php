<?php

$id 	= $this->uri->segment(3); 
$tanda 	= $this->uri->segment(4); 
// echo $id;
if($tanda == 'foh'){
	$get_Data	= $this->db->query("SELECT * FROM cost_foh WHERE id='".$id."'")->row();
}
if($tanda == 'foh_tanki'){
	$get_Data	= $this->db->query("SELECT * FROM cost_foh WHERE id='".$id."'")->row();
}
if($tanda == 'process'){
	$get_Data	= $this->db->query("SELECT * FROM cost_process WHERE id='".$id."'")->row();
}
if($tanda == 'process_tanki'){
	$get_Data	= $this->db->query("SELECT * FROM cost_process WHERE id='".$id."'")->row();
}
?>

<div class="box box-success">
	<div class="box box-primary">
		<br>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Item Cost <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'item_cost','name'=>'item_cost','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Item Cost','readonly'=>'readonly'), $get_Data->item_cost);	
							echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id'), $get_Data->id);	
							echo form_input(array('type'=>'hidden','id'=>'tanda','name'=>'tanda'), $tanda);								
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Standart Rate (<?=$get_Data->satuan;?>) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'std_rate','name'=>'std_rate','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Standart Rate'), floatval($get_Data->std_rate));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Standart Perhitungan <span class='text-red'>*</span></b></label>
				<div class='col-sm-10'>             
						<?php
							echo form_input(array('id'=>'std_hitung','name'=>'std_hitung','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Standart Perhitungan'), $get_Data->std_hitung);											
						?>		
				</div>
			</div>		
		</div>
	 </div>
	 <div class='box-footer'>
		<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'width:100px;','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
		?>
	</div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
	
	$('#simpan-bro').click(function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		
		var item_cost	= $('#item_cost').val();
		var std_rate	= $('#std_rate').val();
		var std_hitung	= $('#std_hitung').val();
		var tanda		= $('#tanda').val();
		
		if(item_cost=='0' || item_cost==null){
			swal({
			  title	: "Error Message!",
			  text	: 'Item Cost is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;
		}
		if(std_rate=='' || std_rate==null || std_rate=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Standart Rate is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;
			
		}
		if(std_hitung == '' || std_hitung == null || std_hitung=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Standart Perhitungan is empty, please input first ...',
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
					var baseurl		= base_url + active_controller +'/edit/'+tanda;
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
								window.location.href = base_url + active_controller +'/'+ data.tanda;
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
</script>