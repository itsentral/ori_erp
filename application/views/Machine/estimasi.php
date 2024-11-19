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
				<label class='label-control col-sm-2'><b>Customer Name</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_customer','name'=>'nm_customer','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['nm_customer']);
						echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_produksi']);
					?>				
				</div>
				<label class='label-control col-sm-2'><b>Machine</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Machine Name', 'readonly'=>'readonly'), $row[0]['nm_mesin']);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Plant Start Production</b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'plan_start_produksi','name'=>'plan_start_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly'), date('d F Y', strtotime($row[0]['plan_start_produksi'])));
				?>
				</div>
				<label class='label-control col-sm-2'><b>Plant End Production</b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'plan_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly'), date('d F Y', strtotime($row[0]['plan_end_produksi'])));
				?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Real Start Production<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'real_start_produksi','name'=>'real_start_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly','placeholder'=>'Real Start Production', 'style'=>'cursor:pointer;'));
				?>
				</div>
				<label class='label-control col-sm-2'><b>Real End Production<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'real_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly','placeholder'=>'Real End Production', 'style'=>'cursor:pointer;'));
				?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Information</b></label>
				<div class='col-sm-4'>
					 <?php
						// echo form_hidden('id',$row[0]->kode_divisi);
						echo form_textarea(array('id'=>'ket','name'=>'ket','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Address company plants', 'readonly'=>'readonly'), ucfirst(strtolower($row[0]['ket'])));
					?>
				</div>
				
			</div>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" class="no-sort" width="75px">No</th>
						<th class="text-center" style='width: 250px;'>Product Delivery</th>
						<th class="text-center" style='width: 250px;'>Product Type</th>
						<th class="text-center">Product Name</th>
						<th class="text-center">Product To</th>
						<th class="text-center" style='width: 150px;'>Edit Real</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$a=0;
						foreach($rowD AS $val => $valx){
							$a++;
							?>
							<tr>
								<td align='center'><?= $a; ?></td>
								<td><?= strtoupper($valx['delivery_name']);?></td>
								<td><?= strtoupper($valx['id_category']);?></td>
								<td><?= $valx['nm_product'];?></td>
								<td align='center'><span class='badge bg-red'><?= $valx['product_ke'];?></span></td>
								<td align='center'>
								<?php
									// $jumlah = $valx['upload_real'];
									// if($jumlah == 'N'){
										// echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal' title='".$valx['nm_product']." ke (".$valx['product_ke'].")' data-id_product='".$valx['id_product']."' data-id_produksi='".$valx['id_produksi']."' data-id_producktion='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									// }
									// else{
										// echo "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
									// }
									
									$jumlah = $valx['upload_real'];
									if($jumlah == 'N'){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='inputReal1' title='".$valx['nm_product']." ke (".$valx['product_ke'].")' data-id_product='".$valx['id_product']."' data-id_produksi='".$valx['id_produksi']."' data-id_producktion='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									else{
										echo "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
									}
									
									$jumlah2 = $valx['upload_real2'];
									if($jumlah2 == 'N'){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-warning' id='inputReal2' title='".$valx['nm_product']." ke (".$valx['product_ke'].")' data-id_product='".$valx['id_product']."' data-id_produksi='".$valx['id_produksi']."' data-id_producktion='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									else{
										echo "&nbsp;<button type='button' class='btn btn-sm btn-primary' title='Success Upload'><i class='fa fa-check'></i></button>";
									}
								?>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
			
		</div>

		<div class='box-footer'>
			<?php
			// echo $numB;
			if($numB == 0 AND $numB2 == 0){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			}
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
   <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:80%; '>
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
	<!-- modal -->	
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
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
</style>
<script>
	$(document).ready(function(){
		$('#real_start_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$('#real_end_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$(document).on('click', '#inputReal', function(e){
			e.preventDefault();
			$("#head_title").html("<b>UPDATE REAL PRODUCTION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalReal/'+$(this).data('id_product')+'/'+$(this).data('id_produksi')+'/'+$(this).data('id_producktion'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#inputReal1', function(e){
			e.preventDefault();
			$("#head_title").html("<b>UPDATE REAL PRODUCTION FIRST ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalReal1/'+$(this).data('id_product')+'/'+$(this).data('id_produksi')+'/'+$(this).data('id_producktion'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#inputReal2', function(e){
			e.preventDefault();
			$("#head_title").html("<b>UPDATE REAL PRODUCTION SECOND ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalReal2/'+$(this).data('id_product')+'/'+$(this).data('id_produksi')+'/'+$(this).data('id_producktion'));
			$("#ModalView").modal();
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var real_start_produksi	= $('#real_start_produksi').val();
			var real_end_produksi	= $('#real_end_produksi').val();

			
			if(real_start_produksi=='' || real_start_produksi==null || real_start_produksi=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Start Real Production, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(real_end_produksi=='' || real_end_produksi==null || real_end_produksi=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'End Real Production, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
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
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/updateReal';
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
		
		$(document).on('click', '#updateRealMat', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			// var URL = base_url + active_controller+"/updateReal/";
			// alert(URL);
			// return false;
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			$('#restDetailAdd4').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus4').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailAdd3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetail3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var layer				= $('#layer_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailAdd2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				// console.log(findId);
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetail2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var layer				= $('#layer_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailAdd1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				// console.log(findId);
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetail1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var layer				= $('#layer_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$(this).prop('disabled',false);
				return false;
			}
			
			// $('#simpan-bro').prop('disabled',false);

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
						var baseurl=base_url + active_controller +'/UpdateRealMat';
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
									window.location.href = base_url + active_controller+"/updateReal/"+data.produksi;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#updateRealMat').prop('disabled',false);
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
								$('#updateRealMat').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#updateRealMat').prop('disabled',false);
					return false;
				  }
			});
		});
		
		//Modal1
		$(document).on('click', '#updateRealMat1', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			// var URL = base_url + active_controller+"/updateReal/";
			// alert(URL);
			// return false;
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			$('#restDetailPlus4').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetail3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var layer				= $('#layer_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetail2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var layer				= $('#layer_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetail1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var layer				= $('#layer_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$(this).prop('disabled',false);
				return false;
			}
			
			// $('#simpan-bro').prop('disabled',false);

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
						var baseurl=base_url + active_controller +'/UpdateRealMat1';
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
									window.location.href = base_url + active_controller+"/updateReal/"+data.produksi;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#updateRealMat').prop('disabled',false);
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
								$('#updateRealMat').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#updateRealMat').prop('disabled',false);
					return false;
				  }
			});
		});
		
		//Modal 2
		$(document).on('click', '#updateRealMat2', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			// var URL = base_url + active_controller+"/updateReal/";
			// alert(URL);
			// return false;
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			$('#restDetailAdd4').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus4').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailAdd3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin3').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailAdd2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				// console.log(findId);
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailAdd1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				// console.log(findId);
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailPlus1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			$('#restDetailResin1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_number_'+nomor[1]).val();
				var actual_type			= $('#actual_type_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(actual_type == ''){
					intError++;
					pesan = "Actual type has not empty ...";
				}
				if(batch_number == '' ){
					intError++;
					pesan = "Batch number has not empty ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$(this).prop('disabled',false);
				return false;
			}
			
			// $('#simpan-bro').prop('disabled',false);

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
						var baseurl=base_url + active_controller +'/estimasi';
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
									window.location.href = base_url + active_controller+"/index_estimasi/"+data.produksi;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#updateRealMat').prop('disabled',false);
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
								$('#updateRealMat').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#updateRealMat').prop('disabled',false);
					return false;
				  }
			});
		});
	}); 
</script>
