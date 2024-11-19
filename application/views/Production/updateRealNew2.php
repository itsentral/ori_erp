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
				<label class='label-control col-sm-2'><b>SO Number</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'so_number','name'=>'so_number','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['so_number']);
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
					echo form_input(array('id'=>'real_start_produksi','name'=>'real_start_produksi','class'=>'form-control input-md tgl','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly','placeholder'=>'Real Start Production', 'style'=>'cursor:pointer;'));
				?>
				</div>
				<label class='label-control col-sm-2'><b>Real End Production<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'real_end_produksi','name'=>'real_end_produksi','class'=>'form-control input-md tgl','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly','placeholder'=>'Real End Production', 'style'=>'cursor:pointer;'));
				?>
				</div>
			</div>
			<div class="box box-success">
				<!-- /.box-header -->
				<div class="box-body">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead id='head_table'>
							<tr class='bg-blue'>
								<th class="text-center" class="no-sort" width="5%">No</th>
								<th class="text-center" width="15%">Product Delivery</th>
								<th class="text-center" width="20%">Product Type</th>
								<th class="text-center" width="35%">Product Name</th>
								<th class="text-center" width="10%">Product To</th>
								<th class="text-center" width="15%">Edit Real</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
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
		<div class="modal-dialog"  style='width:90%; '>
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
<style type="text/css">
	.tgl{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$(document).ready(function(){
			DataTables();
		});
	
		$('.tgl').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
		
		$(document).on('click', '#inputReal1New', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>UPDATE REAL PRODUCTION FIRST ["+$(this).data('id_product')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modalReal1New/'+$(this).data('id_product')+'/'+$(this).data('id_produksi')+'/'+$(this).data('id_producktion')+'/'+$(this).data('id_milik')+'/'+$(this).data('awal')+'/'+$(this).data('akhir'),
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
		
		$(document).on('click', '.Perbandingan', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>COMPARISON PRODUCTION ["+$(this).data('id_product')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modalPerbandingan/'+$(this).data('id_product')+'/'+$(this).data('id_pro_detail')+'/'+$(this).data('id_produksi')+'/'+$(this).data('awal')+'/'+$(this).data('akhir'),
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
						var baseurl=base_url + active_controller +'/updateRealNew';
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
		
		//Modal 1 New
		$(document).on('click', '#updateRealMat1New', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			// var URL = base_url + active_controller+"/updateReal/";
			// alert(URL);
			// return false;
			let id_gudang = $('#id_gudang').val();
			if(id_gudang == '0'){
				swal({
					title				: "Notification Message !",
					text				: 'Gudang Produksi belum dipilih',						
					type				: "warning"
				});
				$(this).prop('disabled',false);
				return false;
			}
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			var production_date = $('#production_date').val();
			if(production_date == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Production date is Empty, please check back ...',
				  type	: "warning"
				});
				$(this).prop('disabled',false);
				return false;	
			}
			
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
				var benang				= $('#benang_'+nomor[1]).val();
				var bw					= $('#bw_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakai_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(benang == ''){
					intError++;
					pesan = "Benang has not empty ...";
				}
				if(bw == ''){
					intError++;
					pesan = "Bandwidch has not empty ...";
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
			
			$('#restDetailPlus2N2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_numberN2_'+nomor[1]).val();
				var actual_type			= $('#actual_typeN2_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakaiN2_'+nomor[1]).val();
				
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
			
			$('#restDetailResin2N2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_numberN2_'+nomor[1]).val();
				var actual_type			= $('#actual_typeN2_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakaiN2_'+nomor[1]).val();
				
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
			
			$('#restDetail2N2').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_numberN2_'+nomor[1]).val();
				var actual_type			= $('#actual_typeN2_'+nomor[1]).val();
				var layer				= $('#layerN2_'+nomor[1]).val();
				var benang				= $('#benangN2_'+nomor[1]).val();
				var bw					= $('#bwN2_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakaiN2_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(benang == ''){
					intError++;
					pesan = "Benang has not empty ...";
				}
				if(bw == ''){
					intError++;
					pesan = "Bandwidch has not empty ...";
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
			
			$('#restDetailPlus2N1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_numberN1_'+nomor[1]).val();
				var actual_type			= $('#actual_typeN1_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakaiN1_'+nomor[1]).val();
				
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
			
			$('#restDetailResin2N1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_numberN1_'+nomor[1]).val();
				var actual_type			= $('#actual_typeN1_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakaiN1_'+nomor[1]).val();
				
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
			
			$('#restDetail2N1').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var batch_number		= $('#batch_numberN1_'+nomor[1]).val();
				var actual_type			= $('#actual_typeN1_'+nomor[1]).val();
				var layer				= $('#layerN1_'+nomor[1]).val();
				var benang				= $('#benangN1_'+nomor[1]).val();
				var bw					= $('#bwN1_'+nomor[1]).val();
				var material_terpakai	= $('#material_terpakaiN1_'+nomor[1]).val();
				
				if(material_terpakai == '' ){
					intError++;
					pesan = "Material terpakai has not empty ...";
				}
				if(layer == ''){
					intError++;
					pesan = "Layer has not empty ...";
				}
				if(benang == ''){
					intError++;
					pesan = "Benang has not empty ...";
				}
				if(bw == ''){
					intError++;
					pesan = "Bandwidch has not empty ...";
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
			
			var est_real = $('#est_real').val();
			if(est_real == '0' || est_real == null || est_real == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Real Estimation is Empty, please check back ...',
				  type	: "warning"
				});
				$(this).prop('disabled',false);
				return false;	
			}
			
			$('#updateRealMat1New').prop('disabled',false);

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
						var baseurl=base_url + active_controller +'/save_update_produksi_1';
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
									window.location.href = base_url + active_controller+"/updateRealNew2/"+data.produksi;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#updateRealMat1New').prop('disabled',false);
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
								$('#updateRealMat1New').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#updateRealMat1New').prop('disabled',false);
					return false;
				  }
			});
		});
	}); 
	
	
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150, 250, 500, 1000], [10, 20, 50, 100, 150, 250, 500, 1000]],
			"ajax":{
				url : base_url + active_controller+'/server_side_update_produksi_1',
				type: "post",
				data: function(d){
				d.id_produksi = $('#id_produksi').val()
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
</script>
