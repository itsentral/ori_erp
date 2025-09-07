<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_plan_tagih" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<div class="box-tool pull-right">
				
			</div>
		</div>
		
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Customer <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='customer' id='customer' class='form-control input-md'>
						<option value='0'>Select An Customer</option>
					 <?php
						foreach($customer AS $valx){
							echo "<option value='".$valx->kode_customer."'>".strtoupper($valx->nm_customer)."</option>";
						}
					 ?>
					 </select>
				</div>
				<label class='label-control col-sm-2'><b>Type LC <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='type_lc' id='type_lc' class='form-control input-md'>
						<option value='0'>Select An Type LC</option>
						<option value='lc'>LC</option>
						<option value='non lc'>NON LC</option>
					 </select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No PO <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='no_po' id='no_po' class='form-control input-md'>
						<option value='0'>List Empty</option>
					</select>
				</div>
				
				<label class='label-control col-sm-2'><b>Type Inv <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='type' id='type' class='form-control input-md'>
						<option value='0'>Select An Type Inv</option>
						<option value='uang muka'>UANG MUKA</option>
						<option value='progress'>PROGRESS</option>
						<option value='retensi'>RETENSI</option>
					 </select>
				</div>
			</div>
			
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>ETD </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'etd','name'=>'etd','class'=>'form-control input-md datepicker','placeholder'=>'ETD','readonly'=>'readonly'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>ETA </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'eta','name'=>'eta','class'=>'form-control input-md datepicker','placeholder'=>'ETA','readonly'=>'readonly'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Consignee </b></label>
				<div class='col-sm-4'>
					<textarea id='consignee' name='consignee' class="textarea" placeholder="Place some text here" style="width: 100%; height: 100px; font-size: 12px; line-height: 18px; padding: 10px;"></textarea>
					<?php
					// echo form_input(array('id'=>'consignee','name'=>'consignee','class'=>'form-control input-md','placeholder'=>'Consignee'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Notify Party </b></label>
				<div class='col-sm-4'>
					<textarea id='notify_party' name='notify_party' class="textarea" placeholder="Place some text here" style="width: 100%; height: 100px; font-size: 12px; line-height: 18px; padding: 10px;"></textarea>
					
					<?php
					// echo form_input(array('id'=>'notify_party','name'=>'notify_party','class'=>'form-control input-md','placeholder'=>'Notify Party'));
					?>
				</div>
			</div><div class='form-group row'>
				<label class='label-control col-sm-2'><b>Port Of Loading </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'port_of_loading','name'=>'port_of_loading','class'=>'form-control input-md','placeholder'=>'Port Of Loading'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Port Of Discharges </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'port_of_discharges','name'=>'port_of_discharges','class'=>'form-control input-md','placeholder'=>'Port Of Discharges'));
					?>
				</div>
			</div><div class='form-group row'>
				<label class='label-control col-sm-2'><b>Flight/Airway-bill No </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'flight_airway_no','name'=>'flight_airway_no','class'=>'form-control input-md','placeholder'=>'Flight/Airway-bill No'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Ship Via </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'ship_via','name'=>'ship_via','class'=>'form-control input-md','placeholder'=>'Ship Via'));
					?>
				</div>	
			</div><div class='form-group row'>
				<label class='label-control col-sm-2'><b>Saliling on/About </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'saliling','name'=>'saliling','class'=>'form-control input-md datepicker','placeholder'=>'Saliling on/About','readonly'=>'readonly'));
					?>
				</div>
				
				<label class='label-control col-sm-2'><b>Vessel / Flight </b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'vessel_flight','name'=>'vessel_flight','class'=>'form-control input-md','placeholder'=>'Vessel / Flight'));
					?>
				</div>
			</div><div class='form-group row'>
				<label class='label-control col-sm-2'><b>Term of Delivery</b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id'=>'term_delivery','name'=>'term_delivery','class'=>'form-control input-md','placeholder'=>'Term of Delivery'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Currency <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='base_cur' id='base_cur' class='form-control input-md'>
						<option value='0'>Select An Currency</option>
						<option value='IDR'>IDR</option>
						<option value='USD'>USD</option>
					 </select>
				</div>				
			</div>
			
			<br>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin: 0px 0px 5px 5px;','value'=>'Back','content'=>'Back','id'=>'back')).' ';
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin: 0px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'create_plan')).' ';
			?>
			<br><br><br>
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">#</th>
						<th class="text-center">No SO</th> 
						<th class="text-center">No PO</th>
						<th class="text-center">Project</th>
						<th class="text-center">Customer</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.datepicker{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$(".textarea").wysihtml5();
		
		$('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth:true,
			changeYear:true
		});
		
		let filter = {
			'customer' : $('#customer').val(),
			'no_po' : $('#no_po').val(),
			'type' : $('#type').val()
		};
		// console.log(`${filter.no_po}`)
		DataTables(filter.customer, filter.type, filter.no_po);
		
		$(document).on('change','#customer, #type, #no_po', function(){
			let filter = {
				'customer' : $('#customer').val(),
				'no_po' : $('#no_po').val(),
				'type' : $('#type').val()
			};
			DataTables(filter.customer, filter.type, filter.no_po);
		});
		
		$(document).on('click','#back', function(){
			window.location.href = base_url + active_controller+"/instalasi";
		});
		
		$(document).on('change', '#customer', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url + active_controller +'/get_po',
				cache: false,
				type: "POST",
				data: "id="+this.value,
				dataType: "json",
				success: function(data){
					$("#no_po").html(data.option).trigger("chosen:updated");
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
		
		$(document).on('click','#create_plan', function(e){
			e.preventDefault();
			
			let validasi = {
				'customer' : $('#customer').val(),
				'no_po' : $('#no_po').val(),
				'type' : $('#type').val(),
				'type_lc' : $('#type_lc').val(),
				'etd' : $('#etd').val(),
				'eta' : $('#eta').val(),
				'consignee' : $('#consignee').val(),
				'notify_party' : $('#notify_party').val(),
				'port_of_loading' : $('#port_of_loading').val(),
				'port_of_discharges' : $('#port_of_discharges').val(),
				'flight_airway_no' : $('#flight_airway_no').val(),
				'ship_via' : $('#ship_via').val(),
				'base_cur' : $('#base_cur').val(),
				'saliling' : $('#saliling').val(),
				'vessel_flight' : $('#vessel_flight').val()
			};

			if(validasi.customer == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Customer is empty, please chose first ...',
				  type	: "warning"
				});
				return false;
			}
			if(validasi.no_po == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'PO Number is empty, please chose first ...',
				  type	: "warning"
				});
				return false;
			}
			if(validasi.type == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Type is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(validasi_cur.base_cur == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Currency is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			
			if(validasi.type_lc == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Type LC is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			
			if($('.chk_personal:checked').length == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Minimal checklist satu, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			
			$(this).prop('disabled',true);
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
						var formData 	=new FormData($('#form_plan_tagih')[0]);
						var baseurl=base_url + active_controller +'/add_new_instalasi';
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
									window.location.href = base_url + active_controller+"/instalasi";
								}
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('create_plan').prop('disabled',false);
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
								$('#create_plan').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#create_plan').prop('disabled',false);
					return false;
				  }
			});
		});
	});
	
	function DataTables(customer=null, type=null, no_po=null){
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
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_penagihan_add_new',
				type: "post",
				data: function(d){
					d.customer = customer,
					d.type = type,
					d.no_po = no_po
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
</script>
