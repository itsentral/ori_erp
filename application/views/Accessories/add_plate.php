<?php
$this->load->view('include/side_menu');
$tanda 			= (!empty($header[0]->id))?'edit':'';

$category 		= (!empty($header[0]->category))?$header[0]->category:'';
$nama 			= (!empty($header[0]->nama))?$header[0]->nama:'';
$id_material 	= (!empty($header[0]->id_material))?$header[0]->id_material:'';
$diameter 		= (!empty($header[0]->diameter))?$header[0]->diameter:'';
$panjang 		= (!empty($header[0]->panjang))?number_format($header[0]->panjang,2):'';
$thickness 		= (!empty($header[0]->thickness))?number_format($header[0]->thickness,2):'';
$radius 		= (!empty($header[0]->radius))?number_format($header[0]->radius,2):'';
$density 		= (!empty($header[0]->density))?number_format($header[0]->density,2):'';
$spesifikasi 	= (!empty($header[0]->spesifikasi))?$header[0]->spesifikasi:'';
$material 		= (!empty($header[0]->material))?$header[0]->material:'';
$satuan 		= (!empty($header[0]->satuan))?$header[0]->satuan:'';
$standart 		= (!empty($header[0]->standart))?$header[0]->standart:'';
$ukuran_standart= (!empty($header[0]->ukuran_standart))?$header[0]->ukuran_standart:'';
$keterangan 	= (!empty($header[0]->keterangan))?$header[0]->keterangan:'';
$harga 			= (!empty($header[0]->harga))?number_format($header[0]->harga,2):'';

?>
<form action="#" method="POST" id="form_man_power" autocomplete="off">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<?php
			echo form_input(array('type'=>'hidden','name'=>'tanda_edit','id'=>'tanda_edit','class'=>'form-control input-md'),$tanda);
			echo form_input(array('type'=>'hidden','name'=>'id','id'=>'id','class'=>'form-control input-md'),$this->uri->segment(3));
		?>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Category <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='category' id='category' class='form-control input-md'>
						<?php
							foreach($category_l AS $val => $valx){
								$selected = ($category == $valx['id'])?'selected':'';
								echo "<option value='".$valx['id']."' ".$selected.">".strtoupper(strtolower($valx['category']))."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>ID Material</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'id_material','name'=>'id_material','class'=>'form-control input-md','placeholder'=>'ID Material'),$id_material);
					?>
				</div>
			</div>
			
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Nama Spesifik <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'nama','name'=>'nama','class'=>'form-control input-md','placeholder'=>'Nama Spesifik'),$nama);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Thickness (mm) </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'thickness','name'=>'thickness','class'=>'form-control input-md autoNumeric','placeholder'=>'Thickness'),$thickness);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Material </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'material','name'=>'material','class'=>'form-control input-md','placeholder'=>'Material'),$material);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Density (Kg/cm3) </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'density','name'=>'density','class'=>'form-control input-md autoNumeric','placeholder'=>'Density'),$density);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Standart </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'standart','name'=>'standart','class'=>'form-control input-md','placeholder'=>'Standart'),$standart);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Ukuran Standart </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'ukuran_standart','name'=>'ukuran_standart','class'=>'form-control input-md','placeholder'=>'Ukuran Standart'),$ukuran_standart);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Satuan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='satuan' id='satuan' class='form-control input-md'>
						<option value='0'>Select Satuan </option>
						<?php
							foreach($satuan_l AS $val => $valx){
								$selected = ($satuan == $valx['id_satuan'])?'selected':'';
								echo "<option value='".$valx['id_satuan']."' ".$selected.">".strtoupper(strtolower($valx['kode_satuan']))."</option>";
							}
						?>
					</select>
				</div>
				<!-- <label class='label-control col-sm-2'><b>Harga (USD) </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'harga','name'=>'harga','class'=>'form-control input-md autoNumeric','placeholder'=>'Harga'),$harga);
					?>
				</div> -->
			</div>
			
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Keterangan</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_textarea(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','rows'=>'3','placeholder'=>'Keterangan'),$keterangan);
					?>
				</div>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'save'));
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'back'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>

<script>
	$(document).ready(function(){
		var satuan = $('#satuan').val();
			
		if(satuan == '3'){
			$('#standart').attr('readonly', false);
			$('#ukuran_standart').attr('readonly', true);
		}
		else if(satuan == '20'){
			$('#standart').attr('readonly', true);
			$('#ukuran_standart').attr('readonly', false);
		}
		else{
			$('#standart').attr('readonly', false);
			$('#ukuran_standart').attr('readonly', false);
		}
		
		$(document).on('change','#satuan', function(e){
			var satuan = $('#satuan').val();
			
			if(satuan == '3'){
				$('#standart').attr('readonly', false);
				$('#ukuran_standart').attr('readonly', true);
				$('#ukuran_standart').val('');
			}
			else if(satuan == '20'){
				$('#standart').attr('readonly', true);
				$('#ukuran_standart').attr('readonly', false);
				$('#standart').val('');
			}
			else{
				$('#standart').attr('readonly', false);
				$('#ukuran_standart').attr('readonly', false);
				
				$('#standart').val('');
				$('#ukuran_standart').val('');
			}
		});
		
		$(document).on('click','#save', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			
			var category	= $('#category').val();
			var nama		= $('#nama').val();
			var satuan		= $('#satuan').val();

			if(category=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Category, please input first ...',
				  type	: "warning"
				});
				$('#save').prop('disabled',false);
				return false;
			}

			if(nama=='' || nama==null || nama=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Material Name, please input first ...',
				  type	: "warning"
				});
				$('#save').prop('disabled',false);
				return false;
			}
			
			if(satuan=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Satuan, please input first ...',
				  type	: "warning"
				});
				$('#save').prop('disabled',false);
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
						var formData 	= new FormData($('#form_man_power')[0]);
						var baseurl		= base_url + active_controller +'/add_plate';
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

								$('#save').prop('disabled',false);
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
								$('#save').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#save').prop('disabled',false);
					return false;
				  }
			});
		});

		$('#back').click(function(e){
			window.location.href = base_url + active_controller;
		});
		
	});


</script>
