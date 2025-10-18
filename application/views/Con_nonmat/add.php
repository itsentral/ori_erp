<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);

// $ArrSatuan = array();
// foreach($satuan AS $val => $valx){
	// $ArrSatuan[$valx['id_satuan']] = strtoupper($valx['kode_satuan']);
// }
?>
<form action="#" method="POST" id="form_man_power" autocomplete="off">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<?php
			$tanda 			= (!empty($header[0]->category))?'edit':'';
			$category_awal 	= (!empty($header[0]->category_awal))?$header[0]->category_awal:'';
			$id_category_acc 	= (!empty($header[0]->id_category_acc))?$header[0]->id_category_acc:'';
			$id_acc 	= (!empty($header[0]->id_acc))?$header[0]->id_acc:'';
			$kode_excel 	= (!empty($header[0]->kode_excel))?strtoupper($header[0]->kode_excel):'';
			$kode_item 		= (!empty($header[0]->kode_item))?strtoupper($header[0]->kode_item):'';
			$id_accurate 		= (!empty($header[0]->id_accurate))?strtoupper($header[0]->id_accurate):'';
			$material_name 	= (!empty($header[0]->material_name))?strtoupper($header[0]->material_name):'';
			$trade_name 	= (!empty($header[0]->trade_name))?strtoupper($header[0]->trade_name):'';
			$spec 			= (!empty($header[0]->spec))?strtoupper($header[0]->spec):'';
			$brand 			= (!empty($header[0]->brand))?strtoupper($header[0]->brand):'';
			$no_rak 		= (!empty($header[0]->no_rak))?strtoupper($header[0]->no_rak):'';
			$satuan_val 	= (!empty($header[0]->satuan))?$header[0]->satuan:'';
			$konversi 		= (!empty($header[0]->konversi))?$header[0]->konversi:'';
			$satuan_konversi= (!empty($header[0]->satuan_konversi))?$header[0]->satuan_konversi:'';
			$min_order 		= (!empty($header[0]->min_order))?$header[0]->min_order:'';
			$lead_time 		= (!empty($header[0]->lead_time))?$header[0]->lead_time:'';
			$note 			= (!empty($header[0]->note))?strtoupper($header[0]->note):'';
			$status 		= (!empty($header[0]->status))?$header[0]->status:'';
	
			echo form_input(array('type'=>'hidden','name'=>'tanda_edit','id'=>'tanda_edit','class'=>'form-control input-md'),$tanda);
			echo form_input(array('type'=>'hidden','name'=>'id_acc','id'=>'id_acc','class'=>'form-control input-md'),$id_acc);
			echo form_input(array('type'=>'hidden','name'=>'code_group','id'=>'code_group','class'=>'form-control input-md'),$this->uri->segment(3));
		?>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Kategori Stok <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<select name='category_awal' id='category_awal' class='form-control input-md'>
						<option value='0'>Select Kategori Stok </option>
						<?php
							foreach($cateMPUtama AS $val => $valx){
								$selected = ($category_awal == $valx['id'])?'selected':'';
								echo "<option value='".$valx['id']."' ".$selected.">".strtoupper(strtolower($valx['category']))."</option>";
							}
						?>
					</select>
				</div>
				<div class='col-sm-2'>
				<div id='catAcc'>
					<select name='id_category_acc' id='id_category_acc' class='form-control input-md'>
						<option value='0'>Bukan Tipe Aksesoris </option>
						<?php
							foreach($categoryAcc AS $val => $valx){
								$selected = ($id_category_acc == $valx['id'])?'selected':'';
								echo "<option value='".$valx['id']."' ".$selected.">".strtoupper(strtolower($valx['category']))."</option>";
							}
						?>
					</select>
				</div>
				</div>
				<label class='label-control col-sm-2'><b>Status </b></label>
				<div class='col-sm-2' style='vertical-align:middle;'>
					<?php
					$active		= ($status =='1')?TRUE:FALSE;
					$color		= ($status =='1')?'green':'red';
					$label		= ($status =='1')?'ACTIVE':'NOT ACTIVE';
					$data = array(
							'name'          => 'status',
							'id'            => 'status',
							'value'         => '1',
							'checked'       => $active,
							'class'         => 'input-sm'
					);
	
					echo form_checkbox($data).'&nbsp;&nbsp;Yes';
					
				?>				
				</div>
				<div class='col-sm-2'>
					<span class='badge bg-<?=$color;?>'><?=$label?></span>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Excel Code / ID Accurate</b></label>
				<div class='col-sm-2'>
					<?php
					 echo form_input(array('id'=>'kode_excel','name'=>'kode_excel','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Excel Code'),$kode_excel);
					?>
				</div>
				<div class='col-sm-2'>
					<?php
					 echo form_input(array('id'=>'id_accurate','name'=>'id_accurate','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'ID Accurate'),$id_accurate);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Item Code</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'kode_item','name'=>'kode_item','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Item Code'),$kode_item);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Material Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'material_name','name'=>'material_name','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material Name'),$material_name);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Trade Name</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'trade_name','name'=>'trade_name','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Trade Name'),$trade_name);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Spesification <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'spec','name'=>'spec','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Spesification'),$spec);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Brand <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'brand','name'=>'brand','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Brand'),$brand);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Minimal Order Stock</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'min_order','name'=>'min_order','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Minimal Order Stock','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>''),$min_order);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Lead Time (Day)</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'lead_time','name'=>'lead_time','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Lead Time','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>''),$lead_time);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Satuan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='satuan' id='satuan' class='form-control input-md'>
						<option value='0'>Select Satuan </option>
						<?php
							foreach($satuan AS $val => $valx){
								$selected = ($satuan_val == $valx['id_satuan'])?'selected':'';
								echo "<option value='".$valx['id_satuan']."' ".$selected.">".strtoupper(strtolower($valx['kode_satuan']))."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>No Rak</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'no_rak','name'=>'no_rak','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'No Rak'),$no_rak);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Konversi</b></label>
				<div class='col-sm-2'>
					<?php
					 echo form_input(array('id'=>'konversi','name'=>'konversi','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Konversi'),$konversi);
					?>
				</div>
				<div class='col-sm-2'>
					<select name='satuan_konversi' id='satuan_konversi' class='form-control input-md'>
						<option value='0'>Select Satuan </option>
						<?php
							foreach($satuan AS $val => $valx){
								$selected = ($satuan_konversi == $valx['id_satuan'])?'selected':'';
								echo "<option value='".$valx['id_satuan']."' ".$selected.">".strtoupper(strtolower($valx['kode_satuan']))."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Note</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'note','name'=>'note','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Note'),$note);
					?>
				</div>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'save_rutin')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'back_man_power'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>

<script>
	$(document).ready(function(){
		let id_category_acc = '<?=$id_category_acc;?>'

		$('#catAcc').show()

		$(document).on('change','#category_awal',function(){
			var idcategost = $(this).val()
			if(idcategost == '7'){
				$('#catAcc').show()
			}
			else{
				$('#catAcc').hide()
			}
		})


		$(".numberOnly").autoNumeric('init', {mDec: '2', aPad: false});

		$('#save_rutin').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var category_awal	= $('#category_awal').val();
			var id_category_acc	= $('#id_category_acc').val();

			var material_name	= $('#material_name').val();
			var trade_name	= $('#trade_name').val();
			var spec	= $('#spec').val();
			var brand	= $('#brand').val();

			if(category_awal=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Category, please input first ...',
				  type	: "warning"
				});
				$('#save_rutin').prop('disabled',false);
				return false;
			}

			if(category_awal == '7'){
				if(id_category_acc=='0'){
					swal({
					title	: "Error Message!",
					text	: 'Empty Type Accessories, please input first ...',
					type	: "warning"
					});
					$('#save_rutin').prop('disabled',false);
					return false;
				}
			}

			if(material_name=='' || material_name==null || material_name=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Material Name, please input first ...',
				  type	: "warning"
				});
				$('#save_rutin').prop('disabled',false);
				return false;
			}

			if(spec==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Spesifikasi, please input first ...',
				  type	: "warning"
				});
				$('#save_rutin').prop('disabled',false);
				return false;
			}

			if(brand==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Empty Brand, please input first ...',
				  type	: "warning"
				});
				$('#save_rutin').prop('disabled',false);
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
						var baseurl		= base_url + active_controller +'/add_new';
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
								else if(data.status == 2 || data.status == 3){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}

								$('#save_rutin').prop('disabled',false);
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
								$('#save_rutin').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#save_rutin').prop('disabled',false);
					return false;
				  }
			});
		});


		$('#back_man_power').click(function(e){
			window.location.href = base_url + active_controller;
		});

	});
</script>
