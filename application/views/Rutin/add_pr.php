<?php
$this->load->view('include/side_menu'); 
//echo"<pre>";print_r($data_menu);
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Nama Barang</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'nm_barang','name'=>'nm_barang','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Nama Barang','readonly'=>'readonly'), strtoupper($data[0]->material_name));
							echo form_input(array('type'=>'hidden','id'=>'id_barang','name'=>'id_barang','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Item Cost','readonly'=>'readonly'),$data[0]->code_group);							
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Spesifikasi</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'spec','name'=>'spec','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Spesifikasi','readonly'=>'readonly'), strtoupper($data[0]->spec));											
						?>		
				</div>
			</div>	
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Inventory Type</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'category_awal','name'=>'category_awal','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Inventory Type','readonly'=>'readonly'), strtoupper($data[0]->categoryb));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Gudang</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'gudang','name'=>'gudang','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Gudang','readonly'=>'readonly'), strtoupper($data[0]->nm_gudang));											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Stock Oke</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'stock','name'=>'stock','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Stock Oke','readonly'=>'readonly'), number_format($data[0]->stock));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Kebutuhan (Bulan)</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'kebutuhan_month','name'=>'kebutuhan_month','class'=>'form-control input-md numberOnly','autocomplete'=>'off','placeholder'=>'Kebutuhan (Month)','readonly'=>'readonly'), number_format($sum_kebutuhan[0]->sum_keb));											
						?>		
				</div>
			</div>
			
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Permintaan Pembelian <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>             
						<?php
							echo form_input(array('id'=>'qty_purchase','name'=>'qty_purchase','class'=>'form-control input-md maskM','autocomplete'=>'off','placeholder'=>'Permintaan Pembelian','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>''));											
						?>		
				</div>
				<div class='col-sm-2'>             
						<?php
							echo "<select name='satuan' class='chosen_select form-control input-sm'>";
								echo "<option value='0'>Select Satuan</option>";
								foreach($satuan AS $val2 => $valx2){
									echo "<option value='".$valx2['id']."'>".strtoupper($valx2['unit'])."</option>";
								}
							echo "</select>";											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Tgl Dibutuhkan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'tgl_dibutuhkan','name'=>'tgl_dibutuhkan','class'=>'form-control input-md tgl','placeholder'=>'Tanggal Dibutuhkan','readonly'=>'readonly'));											
						?>		
				</div>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro'));
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'back'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<style type="text/css">
	.tgl{
		cursor: pointer;
	}
</style>

<?php $this->load->view('include/footer'); ?> 
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.tgl').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			minDate : 0
		});
		
		$(document).on('click','#back', function(){
			window.location.href = base_url + active_controller + "/warehouse_rutin";
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			return false();
			$(this).prop('disabled',true);
			var item_cost	= $('#item_cost').val();
			var std_rate	= $('#std_rate').val();
			var std_hitung	= $('#std_hitung').val();
			
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
						var baseurl		= base_url + active_controller +'/add_foh';
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
									window.location.href = base_url + active_controller +'/foh';
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
	});
</script>
