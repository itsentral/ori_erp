<?php
	$qResinContaining	= "SELECT * FROM help_resin_containing ORDER BY material ASC";
	$restResnCont		= $this->db->query($qResinContaining)->result_array();
	// print_r($restResnCont);
?>
<div class="box box-primary">
	<div class="box-body" style="">
		<button type="button" id='edit' style='width:100px; margin-right: 3px; margin-bottom: 5px; float:right;' class="btn btn-warning btn-sm">Edit</button>
		<button type="button" id='cancel_edit' style='width:100px; margin-right: 3px; margin-bottom: 5px; float:right;' class="btn btn-danger btn-sm">Cancel Edit</button>
		<button type="button" id='update_edit' style='width:100px; margin-right: 3px; margin-bottom: 5px; float:right;' class="btn btn-primary btn-sm">Update Edit</button>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th width='10%' class="text-center">No.</th>
					<th width='15%' class="text-center">Category</th>
					<th width='15%' class="text-center">Layer</th>
					<th width='15%' class="text-center">First Diameter</th>
					<th width='15%' class="text-center">End Diamater</th>
					<th width='15%' class="text-center">Numbers Divided</th>
					<th width='15%' class="text-center">Divider Number</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$no = 0;
					foreach($restResnCont AS $val => $valx){
						$no++;
						?>
							<tr>
								<td align='center'><?= $no;?></td>
								<td><?= strtoupper($valx['material']);?></td>
								<td>
									<?= strtoupper($valx['layer']);?>
									<input type='hidden' class='form-control input-sm' name='SetResinC[<?=$no;?>][id]' id='id_<?= $no;?>' value='<?= $valx['id'];?>'>
								</td>
								<td>
									<div class='dataR' align='right' style='padding-right: 60px;'><?= $valx['start'];?></div>
									<div class='dataE'><input type='text' class='form-control input-sm numberOnly' style='text-align:right; float:right;' name='SetResinC[<?=$no;?>][start]' id='start_<?= $no;?>' value='<?= $valx['start'];?>'></div>
								</td>
								<td>
									<div class='dataR' align='right' style='padding-right: 60px;'><?= $valx['end'];?></div>
									<div class='dataE'><input type='text' class='form-control input-sm numberOnly' style='text-align:right;' name='SetResinC[<?=$no;?>][end]' id='end_<?= $no;?>' value='<?= $valx['end'];?>'></div>
								</td>
								<td>
									<div class='dataR' align='right' style='padding-right: 60px;'><?= $valx['value1'];?></div>
									<div class='dataE'><input type='text' class='form-control input-sm numberOnly' style='text-align:right;' name='SetResinC[<?=$no;?>][value1]' id='value1_<?= $no;?>' value='<?= $valx['value1'];?>'></div>
								</td>
								<td>
									<div class='dataR' align='right' style='padding-right: 60px;'><?= $valx['value2'];?></div>
									<div class='dataE'><input type='text' class='form-control input-sm numberOnly' style='text-align:right;' name='SetResinC[<?=$no;?>][value2]' id='value2_<?= $no;?>' value='<?= $valx['value2'];?>'></div>
								</td>
							</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function(){
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
			if($(this).val() == ''){
				$(this).val(0);
			}
		});
		
		$('.dataE').hide();
		$('#update_edit').hide();
		$('#cancel_edit').hide();
		
		$(document).on('click', '#edit', function(){
			$(this).hide();
			$('#cancel_edit').show();
			$('#update_edit').show();
			$('.dataE').show();
			$('.dataR').hide();
		});
		$(document).on('click', '#cancel_edit', function(){
			$(this).hide();
			$('#update_edit').hide();
			$('#edit').show();
			$('.dataE').hide();
			$('.dataR').show();
		});
		
		$(document).on('click', '#update_edit', function(){
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
						var baseurl		= base_url + active_controller +'/setResin';
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
									window.location.href = base_url + active_controller+"/pipe";
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
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					return false;
				  }
			});
		});
		
	});
</script>