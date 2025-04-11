<?php
$this->load->view('include/side_menu');
// echo"<pre>";print_r($CustList);
// echo "</pre>";
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Request Customer</h3>
				</div>
				<div class="box-body">
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Customer Name <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='id_customer' id='id_customer' class='form-control input-md'>
								<option value='0'>Select An Customer</option>
							 <?php
								foreach($CustList AS $val => $valx){
									echo "<option value='".$valx['id_customer']."'>".$valx['nm_customer']."</option>";
								}
							 ?>
							 </select>
						</div>

						<label class='label-control col-sm-2'><b>Max Min Tolerance <span class='text-red'>*</span></b></label>
						<div class='col-sm-2'>
							<?php
							 echo form_input(array('id'=>'max_tol','name'=>'max_tol','class'=>'form-control input-md numberOnly','placeholder'=>'Max Tolerance'));
							?>
						</div>
						<div class='col-sm-2'>
							<?php
							 echo form_input(array('id'=>'min_tol','name'=>'min_tol','class'=>'form-control input-md numberOnly','placeholder'=>'Min Tolerance'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Project'));
							?>
						</div>
						<label class='label-control col-sm-2'><b>Note</b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'note','name'=>'note','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Note Etc'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Validity & Guarantee <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_input(array('id'=>'validity','name'=>'validity','class'=>'form-control input-md','placeholder'=>'Validity & Guarantee'));
							?>
						</div>
						<label class='label-control col-sm-2'><b>Payment Term <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_input(array('id'=>'payment','name'=>'payment','class'=>'form-control input-md','placeholder'=>'Payment Term'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Referensi Customer/Project <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'ref_cust','name'=>'ref_cust','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Ref Customer/Project'));
							?>
						</div>
						<label class='label-control col-sm-2'><b>Special Requirements From Customer</b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'syarat_cust','name'=>'syarat_cust','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Special Requirements From Customer'));
							?>
						</div>
					</div>
					<div class="box box-danger">
						<div class="box-header">
							<h3 class="box-title">Specification List</h3>
							<input type='hidden' name='numberMax' id='numberMax' value='0'>
							<input type='hidden' name='numberMaxSpool' id='numberMaxSpool' value='0'>
							<input type='hidden' name='numberMaxComp' id='numberMaxComp' value='0'>
							<input type='hidden' name='numberHelp1' id='numberHelp1' value='0'>
							<input type='hidden' name='numberHelp2' id='numberHelp2' value='0'>
						</div>
						<!-- style="overflow-x:auto;" -->
						<div class="box-body" >
							<button type="button" id='add_sp' style='width:130px; margin-right:0px; margin-bottom:3px; margin-left:5px; float:right;' class="btn btn-success btn-sm">Add Specification</button>
							<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
								<thead id='head_table'>
									<tr class='bg-blue'>
										<th class="text-center" style='width: 10%;'>#</th>
										<th class="text-center" style='width: 83%;' colspan='6'>Specification</th>
										<th class="text-center" style='width: 7%;'>Option</th>
									</tr>
								</thead>
								<tbody id='detail_body'></tbody>
								<tbody id='detail_body_Kosong'>
									<tr>
										<td colspan='9'>Specification list empty ...</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box box-warning">
				<div class="box-header">
					<h3 class="box-title">Delivery</h3>
				</div>
				<div class="box-body">
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Country of Destination<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='country_code' id='country_code' class='form-control input-md'>
								<option value='0'>Select An Country Destination</option>
							 <?php
								foreach($CountryName AS $val => $valx){
									echo "<option value='".$valx['country_code']."'>".$valx['country_name']."</option>";
								}
							 ?>
							 </select><br>
							 <button type='button' id='addCountry' style='font-weight: bold; font-size: 12px; margin-top: 5px; color: #175477;'>Add Country</button>
						</div>
						<label class='label-control col-sm-2'><b>Delivery Date<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							echo form_input(array('id'=>'date_delivery','name'=>'date_delivery','class'=>'form-control input-md','style'=>'cursor:pointer','placeholder'=>'Delivery Date', 'readonly'=>'readonly'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Delivery Address<span class='text-red'>*</span></b></label>
						<div class='col-sm-10'>
							<?php
							 echo form_textarea(array('id'=>'address_delivery','name'=>'address_delivery','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Delivery Address'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Shipping Method<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='metode_delivery' id='metode_delivery' class='form-control input-md'>
								<option>Select An Shipping</option>
							 <?php
								foreach($ShippingName AS $val => $valx){
									echo "<option value='".$valx['shipping_name']."'>".$valx['shipping_name']."</option>";
								}
							 ?>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Packing<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='packing' id='packing' class='form-control input-md'>
								<option>Select An Packing</option>
							 <?php
								foreach($PackningName AS $val => $valx){
									echo "<option value='".$valx['packing_name']."'>".$valx['packing_name']."</option>";
								}
							 ?>
							 </select>
						</div>
					</div>
					<div id='HideShipping'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'><b>Trucking<span class='text-red'>*</span></b></label>
							<div class='col-sm-3'>
								<select name='truck' id='truck' class='form-control input-md'>
									<option>Select An Trucking</option>
								 <?php
									foreach($ShippingName AS $val => $valx){
										echo "<option value='".$valx['shipping_name']."'>".$valx['shipping_name']."</option>";
									}
								 ?>
								 </select>
							</div>
							<div class='col-sm-1'>
								 <?php
									echo form_input(array('id'=>'qty_truck','name'=>'qty_truck','maxlength'=>'3','style'=>'text-align:center;','class'=>'form-control input-md numberOnly','placeholder'=>'Qty','autocomplete'=>'off'));
								?>
							</div>
							<label class='label-control col-sm-2'><b>Vendor<span class='text-red'>*</span></b></label>
							<div class='col-sm-4'>
								<select name='vendor' id='vendor' class='form-control input-md'>
									<option>Select An Vendor</option>
								 <?php
									foreach($PackningName AS $val => $valx){
										echo "<option value='".$valx['packing_name']."'>".$valx['packing_name']."</option>";
									}
								 ?>
								 </select>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Handling Equipment<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='alat_berat' id='alat_berat' class='form-control input-md'>
								<option value='' selected>Select An Handling</option>
								<option value='by ori'>BY ORI</option>
								<option value='by customer'>BY CUSTOMER</option>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Instalation<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='isntalasi_by' id='isntalasi_by' class='form-control input-md'>
								<option value='' selected>Select An Instalation</option>
								<option value='by ori'>BY ORI</option>
								<option value='by customer'>BY CUSTOMER</option>
							 </select>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Validity & Guarantee<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							echo form_input(array('id'=>'garansi','name'=>'garansi','maxlength'=>'3','class'=>'form-control input-md numberOnly','placeholder'=>'Validity & Guarantee / Year','autocomplete'=>'off'));
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='box-footer'> 
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','id'=>'back','content'=>'Back'));
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
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
	.labDet{
		font-weight: bold;
		margin: 10px 0px 3px 0px;
		text-align: end;
		color: #0376c7;
	}
</style>
<script>
	$(document).ready(function(){
		
		$('#HideShipping').hide();
		
		$(document).on('click', '#addCountry', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>ADD COUNTRY</b>");
			$.ajax({
				type:'POST',
				url: base_url+active_controller+'/add_country',
				success:function(data){
					$("#ModalView").modal();
					$("#view").html(data);

				},
				error: function() {
					swal({
					  title				: "Error Message !",
					  text				: 'Connection Timed Out ...',
					  type				: "warning",
					  timer				: 5000,
					  showCancelButton	: false,
					  showConfirmButton	: false,
					  allowOutsideClick	: false
					});
				}
			});
		});
		
		$(document).on('click', '#back', function(e){
			window.location.href = base_url + active_controller;
		});
		
		var nomor	= 1;
		$('#add_sp').click(function(e){
			e.preventDefault();
			var nilaiAwalSpool	= parseInt($("#numberMaxSpool").val());
			var nilaiAkhirSpool	= nilaiAwalSpool + 1;
			$("#numberMaxSpool").val(nilaiAkhirSpool);
			
			AppendBaris(nomor, nilaiAkhirSpool);
			$('#head_table').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax").val(nilaiAkhir);
			$("#detail_body_Kosong").hide();
			$('#simpan-bro').show();
		});
		
		$(document).on('change', '#stifness', function(){
			if($(this).val() == '1250'){
				$('#aplikasi').val('ABOVE GROUND');
			}
			else if($(this).val() == '2500' || $(this).val() == '5000' || $(this).val() == '10000'){
				$('#aplikasi').val('UNDER GROUND');
			}
			else{
				$('#aplikasi').val('');
			}
		});
		
		$(document).on('change', '#std_etc', function(){
			if(this.checked){
				$('#StandardHide').show();
			}
			else{
				$('#StandardHide').hide();
			}
		});
		
		$(document).on('change', '#document', function(){
			if($(this).val() == 'Y'){
				$('#DocumentHide').show();
			}
			else{
				$('#DocumentHide').hide();
			}
		});
		
		$(document).on('change', '#sertifikat', function(){
			if($(this).val() == 'Y'){
				$('#SertifikatHide').show();
			}
			else{
				$('#SertifikatHide').hide();
			}
		});
		
		$(document).on('change', '#color', function(){
			if($(this).val() == 'Y'){
				$('#ColorHide').show();
			}
			else{
				$('#ColorHide').hide();
			}
		});
		
		$(document).on('change', '#test', function(){
			if($(this).val() == 'Y'){
				$('#TestingHide').show();
			}
			else{
				$('#TestingHide').hide();
			}
		});
		
		$(document).on('change', '#abrasi', function(){
			if($(this).val() == 'Y'){
				$('#AbrasiHide').show();
			}
			else{
				$('#AbrasiHide').hide();
			}
		});
		
		$(document).on('change', '#konduksi', function(){
			if($(this).val() == 'Y'){
				$('#KonduksiHide').show();
			}
			else{
				$('#KonduksiHide').hide();
			}
		});
		
		$(document).on('change', '#tahan_api', function(){
			if($(this).val() == 'Y'){
				$('#FireHide').show();
			}
			else{
				$('#FireHide').hide();
			}
		});
		
		
		$('#date_delivery').datepicker({
			dateFormat : 'yy-mm-dd',
			minDate: 0
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		
		$(document).on('click', '#addPSave', function(){
			var standart_code			= $('#country').val();
			
			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Country Name is Empty, please input first ...',
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
						url			: base_url+'index.php/'+active_controller+'/add_country',
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
								window.location.href = base_url + active_controller +'/request';
								
								
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

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var id_customer			= $('#id_customer').val();
			var project				= $('#project').val();
			var country_code		= $('#country_code').val();
			var date_delivery		= $('#date_delivery').val();
			var address_delivery	= $('#address_delivery').val();
			var metode_delivery		= $('#metode_delivery').val();
			var packing				= $('#packing').val();
			var alat_berat			= $('#alat_berat').val();
			var isntalasi_by		= $('#isntalasi_by').val();
			var garansi				= $('#garansi').val();
			
			var max_tol				= $('#max_tol').val();
			var min_tol				= $('#min_tol').val();
			
			var numberMax			= $('numberMax').val();

			if(id_customer=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Customer name is empty, please chose first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(project=='' || project==null || project=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Project name is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(max_tol=='' || max_tol==null || max_tol=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Maximal Tolerance is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(min_tol=='' || min_tol==null || min_tol=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Minimal Tolerance is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			//NEW
			if(validity=='' || validity==null ){
				swal({
				  title	: "Error Message!",
				  text	: 'Validity & Guarantee is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(payment=='' || payment==null ){
				swal({
				  title	: "Error Message!",
				  text	: 'Payment Term is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(ref_cust=='' || ref_cust==null ){
				swal({
				  title	: "Error Message!",
				  text	: 'Referensi Customer/Project is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(numberMax=='' || numberMax==null || numberMax=='0'){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Add Specifications Not been added. Please add first',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(country_code=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Country delivery is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(date_delivery=='' || date_delivery==null || date_delivery=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery date is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(address_delivery=='' || address_delivery==null || address_delivery=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery address is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(metode_delivery=='' || metode_delivery==null || metode_delivery=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery methode is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(packing=='' || packing==null || packing=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery packing is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(alat_berat=='' || alat_berat==null || alat_berat=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Handling Equipment is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(isntalasi_by=='' || isntalasi_by==null || isntalasi_by=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Instalation by is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(garansi=='' || garansi==null || garansi=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Validity & Guarantee is empty, please input first ...',
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
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/request';
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
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
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
	});
	
	function validateEmail(sEmail) {
		var numericExpression = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if (sEmail.match(numericExpression)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function AppendBaris(intd, NilSpool)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = 	"<tr id='tr_"+nomor+"'>"; 
			Rows	+= 		"<td align='center' style='padding: 0px !important; vertical-align: middle !important;'>";
			Rows	+=				"<button type='button' style='min-width:100px; margin-left: 5px;' id='add_component_"+nomor+"' data-pluskom='"+nomor+"' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Product'>Add Product</button>&nbsp;";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' colspan='6' style='text-align: center;'>";
			// Rows	+=			"<select name='ListDetail["+nomor+"][type_resin]' id='type_resin_"+nomor+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Resin Type</option><option value='GRP (FRP)'>GRP (RFP)</option><option value='GRV'>GRV</option></select>";
			Rows	+=			"<select name='ListDetail["+nomor+"][product]' id='product_"+nomor+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Product</option><option value='FRP'>FRP</option><option value='RPM'>RPM</option></select>";
			
			Rows	+=			"<input type='hidden' style='color:black !important;' id='numberMax_"+nomor+"' value='0'>";  
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRow("+nomor+")' title='Delete Record'>Del Row</button>";
			Rows	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBody_"+nomor+"'>";
			Rows 	+= 		"<td colspan='8' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_body_"+nomor+"' class='det_detail'></tbody>"; 
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		$('#detail_body').append(Rows);
		
		var add_component_ 	= "#add_component_"+nomor;
		var numberMax_ 	= "#numberMax_"+nomor;
		var product_	= "#product_"+nomor;
		var product_C	= "#product_"+nomor+"_chosen";
		
		$(document).on('click', add_component_, function(e){
			// e.preventDefault();
			if($(product_).val() == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Product type is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			else{
				// alert($(product_).val()); attr("checked", "checked")
				// return false;
				var ProductC	= $(product_).val();
				var dataKom		= $(this).data('pluskom');
				var nilaiAwal	= parseInt($(numberMax_).val());
				var nilaiAkhir	= nilaiAwal + 1;
				$(numberMax_).val(nilaiAkhir);
				
				var nilaiUrut1	= parseInt($('#numberHelp1').val());
				var hasilUrut1	= nilaiUrut1 + 1;
				$('#numberHelp1').val(hasilUrut1);
				
				$(product_C).addClass("chosen-disabled");
				AppendBarisKom(dataKom, nilaiAkhir, NilSpool, hasilUrut1, ProductC);
				$('.chosen_select').chosen({width: '100%'});
				
				nomor++;
			}
		});
		
		
	}
	
	function AppendBarisKom(intd, num, NilSpool, hasilUrut1, ProductC){
		var nomorK	= 1;
		var valuex	= $('#detail_body_'+nomorK).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_'+nomorK+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr_"+intd+"_"+num+"'>"; 
			Rows	+= 		"<td style='width: 10%;' align='center'>";
			Rows	+=			"<input type='hidden' name='ListDetailKomp["+hasilUrut1+"][product]' id='product_"+intd+"_"+num+"' class='form-control input-sm' value='"+ProductC+"'>";
			Rows	+= 			"<div style='text-align: center;'><input type='hidden' style='color:black !important;' id='numberMax_"+intd+"_"+num+"' value='0'></div>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 12%;' align='right'>";
			Rows	+=			"<div class='labDet'>RESIN</div><select name='ListDetailKomp["+hasilUrut1+"][type_resin]' id='type_resin_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Resin</option><option value='GRP(FRP)'>GRP (RFP)</option><option value='GRV'>GRV</option></select>";
			Rows	+=			"<div class='labDet'>VACUM RATE</div><select name='ListDetailKomp["+hasilUrut1+"][vacum_rate]' id='vacum_rate_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Vacum Rate</option><option value='NON VACUUM'>NON VACUUM</option><option value='HALF VACUUM'>HALF VACUUM</option><option value='FULL VACUUM'>FULL VACUUM</option></select>";
			Rows	+= 			"<div class='labDet'>DOCUMENT</div><input class='form-check-input' id='document_"+intd+"_"+num+"' name='ListDetailKomp["+hasilUrut1+"][document]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;YES</label>";
			Rows	+=			"<div id='documentCh_"+intd+"_"+num+"'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][document_1]' id='document_1_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Document 1' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][document_2]' id='document_2_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Document 2' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][document_3]' id='document_3_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Document 3' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][document_4]' id='document_4_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Document 4' style='margin-bottom: 3px;'>";
			Rows	+=			"</div>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 12%;' align='right'>";
			Rows	+=			"<div class='labDet'>FLUIDA</div><select name='ListDetailKomp["+hasilUrut1+"][id_fluida]' id='id_fluida_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Fluida</option></select>";
			Rows	+=			"<div class='labDet'>LIFE TIME</div><select name='ListDetailKomp["+hasilUrut1+"][time_life]' id='time_life_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Life Time</option><option value='20'>20 Year</option><option value='25'>25 Year</option><option value='30'>30 Year</option></select>";
			Rows	+= 			"<div class='labDet'>CERTIFICATE</div><input class='form-check-input' id='sertifikat_"+intd+"_"+num+"' name='ListDetailKomp["+hasilUrut1+"][sertifikat]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;YES</label>";
			
			Rows	+=			"<div id='sertifikatCh_"+intd+"_"+num+"'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][sertifikat_1]' id='sertifikat_1_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Certificate 1' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][sertifikat_2]' id='sertifikat_2_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Certificate 2' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][sertifikat_3]' id='sertifikat_3_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Certificate 3' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][sertifikat_4]' id='sertifikat_4_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Certificate 4' style='margin-bottom: 3px;'>";
			Rows	+=			"</div>";
			
			Rows	+= 		"</td>"; 
			Rows	+= 		"<td style='width: 12%;' align='right'>";  
			Rows	+=			"<div class='labDet'>PRESSURE</div><select name='ListDetailKomp["+hasilUrut1+"][pressure]' id='pressure_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Pressure</option><option value='6'>6 Bar</option><option value='8'>8 Bar</option><option value='10'>10 Bar</option><option value='12'>12 Bar</option><option value='16'>16 Bar</option><option value='18'>18 Bar</option><option value='20'>20 Bar</option></select>";
			Rows	+= 			"<div class='labDet'>CONDUCTIVE</div><input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][konduksi_liner]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;LINER</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' type='checkbox' name='ListDetailKomp["+hasilUrut1+"][konduksi_structure]' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;STR</label><br>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][konduksi_eksternal]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;EKS</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][konduksi_topcoat]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;TC</label>";
			
			
			Rows	+= 			"<div class='labDet'>TESTING</div><input class='form-check-input' id='test_"+intd+"_"+num+"' name='ListDetailKomp["+hasilUrut1+"][test]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;YES</label>";
			Rows	+=			"<div id='testCh_"+intd+"_"+num+"'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][test_1]' id='test_1_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Testing 1' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][test_2]' id='test_2_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Testing 2' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][test_3]' id='test_3_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Testing 3' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][test_4]' id='test_4_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Testing 4' style='margin-bottom: 3px;'>";
			Rows	+=			"</div>";
			
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 15%;' align='right'>";
			Rows	+=			"<div class='labDet'>STIFNESS</div><select name='ListDetailKomp["+hasilUrut1+"][stifness]' id='stifness_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Stifness</option><option value='1250'>1250 Pa</option><option value='2500'>2500 Pa</option><option value='5000'>5000 Pa</option><option value='10000'>10000 Pa</option></select>";
			
			Rows	+= 			"<div class='labDet'>FIRE RETARDANT</div><input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][tahan_api_liner]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;LINER</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' type='checkbox' name='ListDetailKomp["+hasilUrut1+"][tahan_api_structure]' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;STR</label><br>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][tahan_api_eksternal]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;EKS</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][tahan_api_topcoat]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;TC</label>";
			
			Rows	+= 			"<div class='labDet'>RESIN REQ CUSTOMER</div>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][resin_req_cust]' id='resin_req_cust_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Resin Request Customer' style='margin-bottom: 3px;'>";
			
			
			Rows	+= 		"</td>"; 
			Rows	+= 		"<td style='width: 12%;' align='right'>";  
			
			Rows	+=			"<div class='labDet'>APPLICATION</div><select name='ListDetailKomp["+hasilUrut1+"][aplikasi]' id='aplikasi_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Select An App</option><option value='ABOVE GROUND'>ABOVE GROUND</option><option value='UNDER GROUND'>UNDER GROUND</option></select>";
			// Rows	+=			"<div class='labDet'>APPLICATION</div><input type='text' class='form-control' style='text-align: center;' name='ListDetailKomp["+hasilUrut1+"][aplikasi]' id='aplikasi_"+intd+"_"+num+"' autocomplete='off' placeholder='Application' readonly='readonly'>";
			Rows	+= 			"<div class='labDet'>ABRASIVE</div><input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][abrasi]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;YES</label>";
			
			Rows	+= 			"<div class='labDet'>COLOR</div><input class='form-check-input' id='color_"+intd+"_"+num+"' name='ListDetailKomp["+hasilUrut1+"][color]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;YES</label>";
			
			Rows	+=			"<div id='colorCh_"+intd+"_"+num+"'>";
			Rows	+=				"<select name='ListDetailKomp["+hasilUrut1+"][color_liner]' id='color_liner_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Color Liner</option></select>";
			Rows	+=				"<select name='ListDetailKomp["+hasilUrut1+"][color_structure]' id='color_structure_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Color Structure</option></select>";
			Rows	+=				"<select name='ListDetailKomp["+hasilUrut1+"][color_external]' id='color_external_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Color Eksternal</option></select>";
			Rows	+=				"<select name='ListDetailKomp["+hasilUrut1+"][color_topcoat]' id='color_topcoat_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Color Top Coat</option></select>";
			Rows	+=			"</div>";
			
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 20%;' align='right'>";  
			Rows	+= 			"<div class='labDet'>SATNDARD SPEC</div><input class='form-check-input' type='checkbox' name='ListDetailKomp["+hasilUrut1+"][std_asme]' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;ASME</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][std_ansi]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;ANSI</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][std_astm]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;ASTM</label><br>";
			Rows	+= 			"<input class='form-check-input' type='checkbox' name='ListDetailKomp["+hasilUrut1+"][std_awwa]' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;AWWA</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][std_bsi]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;BSI</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][std_jis]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;JIS</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][std_sni]' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;SNI</label><br>";
			
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][std_etc]' id='std_etc_"+intd+"_"+num+"' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;ETC</label><br>";
			
			Rows	+=			"<div id='etcCh_"+intd+"_"+num+"'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][etc_1]' id='etc_1_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Standard Etc 1' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][etc_2]' id='etc_2_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Standard Etc 2' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][etc_3]' id='etc_3_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Standard Etc 3' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][etc_4]' id='etc_4_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Standard Etc 4' style='margin-bottom: 3px;'>";
			Rows	+=			"</div>";
			Rows	+=				"<textarea name='ListDetailKomp["+hasilUrut1+"][note]' id='note_"+intd+"_"+num+"' rows='2' class='form-control input-sm' placeholder='Note'></textarea>";
			
			Rows	+= 			"<div class='labDet'>COLOR REQUEST</div>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][ck_minat_warna_tc]' id='top_coat_"+intd+"_"+num+"' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;TOP COATED</label>";
			Rows	+= 			"&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailKomp["+hasilUrut1+"][ck_minat_warna_pigment]' id='pigment_"+intd+"_"+num+"' type='checkbox' value='Y'>";
			Rows	+= 				"<label class='form-check-label'>&nbsp;PIGMENTED</label><br>";

			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][minat_warna_tc]' id='minat_warna_tc_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Top Coated Color' style='margin-bottom: 3px;'>";
			Rows	+=				"<input type='text' name='ListDetailKomp["+hasilUrut1+"][minat_warna_pigment]' id='minat_warna_pigment_"+intd+"_"+num+"' class='form-control input-sm' placeholder='Pigmented Color' style='margin-bottom: 3px;'>";
			
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align=\"center\">";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRowKom("+intd+","+num+")' title='Delete Record'>Del Row</button>";  
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
		
		// alert('#detail_body_'+intd);
		// alert(Rows);
		$('#detail_body_'+intd).append(Rows);
		
		var stifness_ 		= "#stifness_"+intd+"_"+num;
		var aplikasi_ 		= "#aplikasi_"+intd+"_"+num;
		var Fluida 			= "#id_fluida_"+intd+"_"+num;
		
		var documentCk 		= "#document_"+intd+"_"+num;
		var testCk 			= "#test_"+intd+"_"+num;
		var colorCk 		= "#color_"+intd+"_"+num;
		var sertifikatCk 	= "#sertifikat_"+intd+"_"+num;  
		var etcCk			= "#std_etc_"+intd+"_"+num;
		
		var documentCh 		= "#documentCh_"+intd+"_"+num;
		var sertifikatCh 	= "#sertifikatCh_"+intd+"_"+num;
		var testCh 			= "#testCh_"+intd+"_"+num;
		var colorCh 		= "#colorCh_"+intd+"_"+num;
		var etcCh 			= "#etcCh_"+intd+"_"+num;

		var top_coat 		= "#top_coat_"+intd+"_"+num;
		var pigment 		= "#pigment_"+intd+"_"+num;
		var minat_warna_tc 		= "#minat_warna_tc_"+intd+"_"+num;
		var minat_warna_pigment = "#minat_warna_pigment_"+intd+"_"+num;
		
		$(documentCh).hide();
		$(testCh).hide();
		$(colorCh).hide();
		$(sertifikatCh).hide();
		$(etcCh).hide();  

		$(minat_warna_tc).hide();
		$(minat_warna_pigment).hide(); 
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getFluida',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(Fluida).html(data.option).trigger("chosen:updated");
			}
		});

		$(document).on('change', top_coat, function(e){
			if(this.checked) {
				$(minat_warna_tc).show();
			}
			else{
				$(minat_warna_tc).hide();
			}
		});

		$(document).on('change', pigment, function(e){
			if(this.checked) {
				$(minat_warna_pigment).show();
			}
			else{
				$(minat_warna_pigment).hide();
			}
		});
		
		$(document).on('change', documentCk, function(e){
			if(this.checked) {
				$(documentCh).show();
			}
			else{
				$(documentCh).hide();
			}
		});
		
		$(document).on('change', testCk, function(e){
			if(this.checked) {
				$(testCh).show();
			}
			else{
				$(testCh).hide();
			}
		});
		
		$(document).on('change', colorCk, function(e){
			if(this.checked) {
				$(colorCh).show();
			}
			else{
				$(colorCh).hide();
			}
		});
		
		$(document).on('change', sertifikatCk, function(e){
			if(this.checked) {
				$(sertifikatCh).show();
			}
			else{
				$(sertifikatCh).hide();
			}
		});
		
		$(document).on('change', etcCk, function(e){
			if(this.checked) {
				$(etcCh).show();
			}
			else{
				$(etcCh).hide();
			}
		});
		
		$(document).on('change', stifness_, function(e){
			if($(stifness_).val() == '1250'){
				$(aplikasi_).val('ABOVE GROUND');
			}
			else if($(stifness_).val() == '2500' || $(stifness_).val() == '5000' || $(stifness_).val() == '10000'){
				$(aplikasi_).val('UNDER GROUND');
			}
			else{
				$(aplikasi_).val('');
			}
		});
		
		var colorLiner = "#color_liner_"+intd+"_"+num;
		var colorStructure = "#color_structure_"+intd+"_"+num;
		var colorEks = "#color_external_"+intd+"_"+num;
		var colorTC = "#color_topcoat_"+intd+"_"+num;
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getColorLin',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(colorLiner).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getColorStr',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(colorStructure).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getColorEks',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(colorEks).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getColorTC',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(colorTC).html(data.option).trigger("chosen:updated");
			}
		});
		
		nomorK++;
		num++;
	}
	
	function delRow(row){
		$('#tr_'+row).remove();
		$('#trBody_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax").val() - 1;
		$("#numberMax").val(updatemax);
		
		var maxLine = $("#numberMax").val();
		if(maxLine == 0){
			// $('#head_table').hide();
			$("#detail_body_Kosong").show();
			// $('#simpan-bro').hide();
		}
	}
	
	function delRowKom(row, row2){
		$('#tr_'+row+'_'+row2).remove();
		$('#trBodySub_'+row+'_'+row2).remove();

		var updatemax	=	$('#numberMax_'+row+'_'+row2).val() - 1;
		$('#numberMax'+row+'_'+row2).val(updatemax);
	}
</script>
