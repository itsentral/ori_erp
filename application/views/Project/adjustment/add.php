<?php
$this->load->view('include/side_menu'); 

?> 
<form action="#" method="POST" id="form_proses" autocomplete='off'>   
	<input type='hidden' name='id' value='<?=$id;?>'>
	<input type='hidden' name='kode' value='<?=$kode;?>'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Adjustment Type <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='adjustment_type' id='adjustment_type' class='form-control input-sm'>
						<option value='0'>Select Type</option>
						<option value='plus'>PLUS</option>
						<option value='minus'>MINUS</option>
						<option value='mutasi'>MUTASI</option>
					</select>
				</div>
				<label class='label-control col-sm-2 gudang_mutasi'><b>PIC <span class='text-red'>*</span></b></label>
				<div class='col-sm-4 gudang_mutasi'>
					<?php
					 echo form_input(array('id'=>'pic_m','name'=>'pic_m','class'=>'form-control input-md','placeholder'=>'PIC',));
					?>
				</div>
			</div>
			<div class='form-group row gudang_mutasi'>
				<label class='label-control col-sm-2'><b>Gudang <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_gudang_dari_m' id='id_gudang_dari_m' class='form-control input-sm'>
						<?php
						foreach($gudang_to AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Ke Gudang</b></label>
				<div class='col-sm-4'>
					<select name='id_gudang_ke_m' id='id_gudang_ke_m' class='form-control input-sm'>
						<!-- <option value='0'>Select Tujuan Gudang</option> -->
						<?php
						foreach($gudang_to AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row gudang_plus_min'>
				<label class='label-control col-sm-2'><b>Gudang <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_gudang_ke' id='id_gudang_ke' class='form-control input-sm'>
						<?php
						foreach($gudang_to AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>PIC <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'pic','name'=>'pic','class'=>'form-control input-md','placeholder'=>'PIC',));
					?>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Nama Barang <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='id_material' id='id_material' class='form-control input-sm'>
						<option value='0'>Select Nama Barang</option>
						<?php
						foreach($barang AS $val => $valx){
							echo "<option value='".$valx['code_group']."'>".$valx['code_group']." - ".strtoupper($valx['material_name']." - ".$valx['spec'])."</option>";
						}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Qty <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'qty_oke','name'=>'qty_oke','class'=>'form-control input-md autoNumeric','placeholder'=>'Qty'));
					?>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>No. Request/BA <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'no_ba','name'=>'no_ba','class'=>'form-control input-md','placeholder'=>'No BA',));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Keterangan</b></label>
				<div class='col-sm-4'>              
					<?php
					 echo form_textarea(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','placeholder'=>'Keterangan','rows'=>'3'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-10'>              
					<?php
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan')).' ';
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','id'=>'back','content'=>'Back'));
					?>
				</div>
			</div>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.gudang_plus_min').hide();
		
		$(document).on('click', '#back', function(e){
			window.location.href = base_url + active_controller;
		});
		
		$(document).on('change', '#adjustment_type', function(e){ 
			var type = $(this).val();
			if(type != 'mutasi'){
				$('.gudang_mutasi').hide();
				$('.gudang_plus_min').show();
			}
			if(type == 'mutasi'){
				$('.gudang_mutasi').show();
				$('.gudang_plus_min').hide();
			}
		});
		
		$(document).on('click','#simpan', function(e){
			e.preventDefault();
			
			$(this).prop('disabled',true);
			//umum
			var adjustment_type		= $('#adjustment_type').val();
			var id_material			= $('#id_material').val();
			var no_ba				= $('#no_ba').val();
			var qty_oke				= $('#qty_oke').val();
			//mutasi
			var id_gudang_dari_m	= $('#id_gudang_dari_m').val();
			var id_gudang_ke_m		= $('#id_gudang_ke_m').val();
			var pic_m				= $('#pic_m').val();
			//plus min
			var id_gudang_ke		= $('#id_gudang_ke').val();
			var pic					= $('#pic').val();
			
			if(adjustment_type=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Adjustment type is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan').prop('disabled',false);
				return false;
				
			}
			
			if(adjustment_type == 'mutasi'){
				if(id_gudang_dari_m == '0'){
					swal({
					  title	: "Error Message!",
					  text	: 'Gudang dari is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan').prop('disabled',false);
					return false;
				}
				if(id_gudang_ke_m=='0'){
					swal({
					  title	: "Error Message!",
					  text	: 'Gudang ke is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan').prop('disabled',false);
					return false;
				}
				if(pic_m == ''){
					swal({
					  title	: "Error Message!",
					  text	: 'PIC is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan').prop('disabled',false);
					return false;
				}
			}
			
			if(adjustment_type != 'mutasi'){
				if(id_gudang_ke == '0'){
					swal({
					  title	: "Error Message!",
					  text	: 'Gudang is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan').prop('disabled',false);
					return false;
				}
				if(pic == ''){
					swal({
					  title	: "Error Message!",
					  text	: 'PIC is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan').prop('disabled',false);
					return false;
				}
			}
			
			if(id_material == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Material is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan').prop('disabled',false);
				return false;
			}
			if(qty_oke == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Qty is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan').prop('disabled',false);
				return false;
			}
			if(no_ba == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'No BA is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan').prop('disabled',false);
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
						var formData  	= new FormData($('#form_proses')[0]);
						var baseurl		= base_url + active_controller +'/add';
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
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#simpan').prop('disabled',false);
							
							},
							error: function() {
								swal({
								  title		: "Error Message !",
								  text		: 'An Error Occured During Process. Please try again..',						
								  type		: "warning",								  
								  timer		: 7000
								});
								$('#simpan').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan').prop('disabled',false);
					return false;
				  }
			});
		});
	});
</script>
