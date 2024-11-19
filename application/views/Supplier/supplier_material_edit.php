<?php
$this->load->view('include/side_menu');
// echo"<pre>";print_r($row);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Material ID <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'idmaterial','name'=>'idmaterial','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material ID','readonly'=>'readonly'), $row[0]['idmaterial']);

					?>
				</div>
				<label class='label-control col-sm-2'><b>Material Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_material','name'=>'nm_material','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material Name','readonly'=>'readonly'), $row[0]['nm_material']);
						echo form_input(array('type'=>'hidden','id'=>'id_material','name'=>'id_material','class'=>'form-control input-md'), $row[0]['id_material']);
					?>
				</div>
			</div>
			<div class="box box-primary">
				<div class="box-header">
					<h4 class="box-title"><b>SUPPLIER LIST</b></h4>
				</div>
				<div class="box-body" style="">
					<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead>
							<tr class='bg-blue'>
								<th class="text-center" width="4%">No</th>
								<th class="text-center" width="25%">Supplier</th>
								<th class="text-center" width="10%">Price</th>
								<th class="text-center" width="10%">Valid Until</th>
								<th class="text-center" width="10%">MOQ</th>
								<th class="text-center" width="10%">Unit</th>
								<th class="text-center" width="10%">Lead Time</th>
								<th class="text-center" width="10%">Descr</th>
								<th class="text-center" width="7%">Flag</th>
								<th class="text-center" width="4%">Del</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$number = 0;
								foreach($Supply AS $val =>$valx){
									$number++;
									$status	= 'Active';
									$class	= 'bg-green';
									if($valx['flag_active'] == 'N'){
										$class	= 'bg-red';
										$status	= 'Not Active';
									}
									?>
									<tr id="add_<?= $number;?>">
										<td align='center'><?= $number;?></td>
										<td>
											<select name='Detail[<?=$number;?>][id_supplier]' class='chosen_select form-control inline-block'>
											<?php
												foreach($ListSup AS $valL => $valLx){
													$sel = ($valx['id_supplier'] == $valLx['id_supplier'])?'selected':'';
													echo "<option value='".$valLx['id_supplier']."' ".$sel.">".$valLx['nm_supplier']."</option>";
												}
											?>
											</select>
										</td>
										<td>
											<input type='text' style="text-align: right;" class='form-control maskM' name='Detail[<?=$number;?>][price]' id='Edprice_sp_<?= $number;?>' value='<?= ucfirst($valx['price']);?>'>
										</td>
										<td>
											<input type='text' style="text-align: center;" style='cursor: pointer;' class='form-control valid_until' name='Detail[<?=$number;?>][valid_until]' id='Edvalid_until_sp_<?= $number;?>' value='<?= ucfirst($valx['valid_until']);?>' readonly>
										</td>
										<td>
											<input type='text' class='form-control text-center maskM' name='Detail[<?=$number;?>][moq]' value='<?= number_format($valx['moq']);?>' data-decimal="." data-thousand="" data-precision="0" data-allow-zero="">
										</td>
										<td>
											<select name='Detail[<?=$number;?>][unit]' class='chosen_select form-control inline-block'>
											<?php
												foreach($getPiece AS $valL => $valLx){
													$sel = ($valx['kode_satuan'] == $valLx['kode_satuan'])?'selected':'';
													echo "<option value='".$valLx['kode_satuan']."' ".$sel.">".$valLx['nama_satuan']."</option>";
												}
											?>
											</select>
										</td>
										<td>
											<input type='text' class='form-control text-center maskM' name='Detail[<?=$number;?>][lead_time_order]' value='<?= number_format($valx['lead_time_order']);?>' data-decimal="." data-thousand="" data-precision="0" data-allow-zero="">
										</td>
										<td>
											<input type='text' class='form-control' name='Detail[<?=$number;?>][descr]' value='<?= ucfirst($valx['descr']);?>'>
										</td>
										<td>
											<select name='Detail[<?=$number;?>][flag_active]' class='chosen_select form-control inline-block'>
												<option value='Y'>Active</option>
												<option value='N'>Non-Active</option>
											</select>
										</td>
										<td align='center'>
											<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>
										</td>
									</tr>
									<?php

								}
								?>
								<tr class='add_<?= $number;?>'>
									<td align='center'></td>
									<td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Supplier</button></td>
									<td align='center'></td>
									<td align='center'></td>
									<td align='center'></td>
									<td align='center'></td>
									<td align='center'></td>
									<td align='center'></td>
									<td align='center'></td>
									<td align='center'></td>
								</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','style'=>'width:80px; margin-left:10px;','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','id'=>'back','style'=>'width:80px; margin-left:5px;','content'=>'Back'));
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
	.tgl{
		cursor:pointer;
	}
</style>
<script>

	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.chosen_select').chosen({width: '100%'});

		$(document).on('click', '#back', function(){
			window.location.href = base_url + active_controller+'/supplier_material';
		});

		$(document).on('click', '#simpan-bro', function(){

			$('#simpan-bro').prop('disabled',false);

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
						var baseurl		= base_url + active_controller +'/supplier_material_edit';
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
									window.location.href = base_url + active_controller+'/supplier_material';
								}
								if(data.status == 2){
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


		//add substr
		$(document).on('click', '.addPart', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			var split_id	= get_id.split('_');
			var id 			= parseInt(split_id[1])+1;
			var id_bef 		= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(".add_"+id_bef).before(data.header);
					$(".add_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					$('.tgl').datepicker({
						dateFormat : 'yy-mm-dd',
						minDate: 0,
						changeMonth: true,
						changeYear: true
					});
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						allowOutsideClick	: false,
						showConfirmButton	: false
					});
				}
			});
		});
	});

	$(document).on('click', '.delPart', function(){
		var get_id 		= $(this).parent().parent('tr').html();
		$(this).parent().parent('tr').remove();
	});
</script>
