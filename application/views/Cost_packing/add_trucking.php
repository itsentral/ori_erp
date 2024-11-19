<?php
$this->load->view('include/side_menu');

$id			= (!empty($data))?$data[0]['id']:'';
$category	= (!empty($data))?$data[0]['category']:'';
$id_truck	= (!empty($data))?$data[0]['id_truck']:'';
$area		= (!empty($data))?$data[0]['area']:'';
$tujuan		= (!empty($data))?$data[0]['tujuan']:'';
$price		= (!empty($data))?number_format($data[0]['price']):'';

$category_darat = '';
$category_laut = '';
if(!empty($data)){
$category_darat = ($data[0]['category'] == 'darat')?'selected':'';
$category_laut = ($data[0]['category'] == 'laut')?'selected':'';
}
// echo $tanda;
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Category <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='category' id='category' class='form-control input-md'>
						<option value='0'>Select Category</option>
						<option value='darat' <?=$category_darat?>>DARAT</option>
						<option value='laut' <?=$category_laut?>>LAUT</option>
					 </select>
					 <?php
						echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id'), $id);
						echo form_input(array('type'=>'hidden','id'=>'tanda','name'=>'tanda'), $tanda);
					?>	
				</div>
				<label class='label-control col-sm-2'><b>Truck Type <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_truck' id='id_truck' class='form-control input-md'>
						<option value='0'>Select Truck</option>
						<?php
						foreach($list_truck AS $val => $valx){
							$selxc = ($id_truck == $valx['id'])?'selected':'';
							echo "<option value='".$valx['id']."' ".$selxc.">".strtoupper($valx['nama_truck'])."</option>";
						}
					 ?>
					 </select>
					<button type='button' id='addCountry' style='font-weight: bold; font-size: 12px; margin-top: 5px; color: #175477;'>Add Truck</button>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Shipping Method <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='area' id='area' class='form-control input-md'>
						<option value='0'>Select Area</option>
						<?php
						foreach($list_area AS $val => $valx){
							$selxc = ($area == $valx['area'])?'selected':'';
							echo "<option value='".$valx['area']."' ".$selxc.">".strtoupper($valx['area'])."</option>";
						}
						?>
					 </select>
				</div>	 	 
				<label class='label-control col-sm-2'><b>Destination <span class='text-red'>*</span></b></label> 
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'tujuan','name'=>'tujuan','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Destination'),$tujuan);											
						?>		
				</div>
			</div>		
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Price IDR <span class='text-red'>*</span></b></label> 
				<div class='col-sm-2'>             
						<?php
							echo form_input(array('id'=>'price','name'=>'price','class'=>'form-control input-md maskMoney','autocomplete'=>'off','placeholder'=>'Price IDR','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>''),$price);											
						?>		
				</div>
			</div>		
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'kembali')).' ';
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
  <!-- modal -->
	<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:40%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</form>

<?php $this->load->view('include/footer'); ?> 
<script>
	$(document).ready(function(){
		$('.maskMoney').maskMoney();
		
		$(document).on('click', '#addCountry', function(e){
			e.preventDefault();
			$("#head_title").html("<b>ADD TRUCK</b>");
			$("#view").load(base_url + active_controller+'/modalAddTruck');
			$("#ModalView").modal();
		});
		
		$(document).on('click','#kembali', function(){
			window.location.href = base_url + active_controller + "/trucking";
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var category	= $('#category').val();
			var id_truck	= $('#id_truck').val();
			var area		= $('#area').val();
			var tujuan		= $('#tujuan').val();
			var price		= $('#price').val();
			
			if(category=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Category is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_truck=='0' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Truck is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(area=='0' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Area is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(tujuan==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Destination is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(price==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Price is Empty, please input first ...',
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
						var baseurl		= base_url + active_controller +'/add_trucking';
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
										  timer	: 5000
										});
									window.location.href = base_url + active_controller +'/trucking';
								}
								else{
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 3000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									$('#simpan-bro').prop('disabled',false);
								}
							},
							error: function() {
								swal({
								  title				: "Error Message !",
								  text				: 'An Error Occured During Process. Please try again..',						
								  type				: "warning",								  
								  timer				: 3000,
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
		
		$(document).on('click', '#addPSave', function(){
			var nama_truck			= $('#nama_truck').val();
			
			if(nama_truck == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Truck Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addPSave').prop('disabled',false);
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
						url			: base_url+active_controller+'/modalAddTruck',
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
								window.location.href = base_url + active_controller +'/add_trucking';
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
	});
</script>
