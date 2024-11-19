<?php
$this->load->view('include/side_menu'); 
?>

<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-left">
			<button type='button' id='update_cost' style='min-width:150px;' class="btn btn-sm btn-primary">
				Update
			</button>
			<br>
			<?php 
			if(!empty($get_by[0]['create_by'])){
			?>
				<div style='color:red;'><b>Last Update by <span style='color:green;'><?= strtoupper(strtolower($get_by[0]['create_by']))."</span> On <u>".date('d-m-Y H:i:s', strtotime($get_by[0]['create_date']));?></u></b></div>
			<?php 
			}
			else{
			?>
				<div style='color:red;'><b>Please update again ...</b></div>
			<?php } ?>
			<div id="spinnerx">
				<img src="<?php echo base_url('assets/img/tres_load.gif') ?>" > <span style='color:green; font-size:16px;'><b>Please Wait ...</b></span>
			</div>
		</div>
		<!-- <div class="box-tool pull-right">
			<label>Search : </label>
			<select id='status' name='status' class='form-control input-sm' style='min-width:200px;'>
				<option value=''>All Status</option>
				<option value='ALREADY ESTIMATED PRICE'>ALREADY ESTIMATED PRICE</option>
				<option value='ALREADY ESTIMATED'>ALREADY & SOME ESTIMATED</option>
				<option value='WAITING ESTIMATION'>WAITING ESTIMATION</option>
				<option value='WAITING PRODUCTION'>WAITING PRODUCTION</option>
			</select>
		</div> -->
	</div>
	<?php
		
		// $this->db->truncate('group_cost_project_table');
			
		// $sqlUpdate = "
			// INSERT INTO group_cost_project_table ( id_bq, no_ipp, estimasi, rev, order_type, nm_customer, sts_ipp, qty, est_harga, est_mat, process_cost, foh_consumable, foh_depresiasi, biaya_gaji_non_produksi, biaya_non_produksi, biaya_rutin_bulanan ) SELECT
				// a.id_bq,
				// a.no_ipp,
				// a.estimasi,
				// a.rev,
				// a.order_type,
				// a.nm_customer,
				// a.sts_ipp,
				// a.qty,
				// a.est_harga,
				// a.est_mat,
				// a.process_cost,
				// a.foh_consumable,
				// a.foh_depresiasi,
				// a.biaya_gaji_non_produksi,
				// a.biaya_non_produksi,
				// a.biaya_rutin_bulanan 
				// FROM
					// group_cost_project a";
		
		// $this->db->query($sqlUpdate);
	?>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th width='4%' class="text-center valign-middle">#</th>
					<th width='7%' class="text-center valign-middle">IPP</th>
					<th width='14%' class="text-center valign-middle">Customer</th>
					<th class="text-center valign-middle">Project</th>
					<th width='6%' class="text-center valign-middle">Type</th>
					<th width='6%' class="text-center no-sort valign-middle">Series</th>
					<th width='8%' class="text-center no-sort valign-middle">Total&nbsp;Material<br>(Est) Kg</th>
					<th width='8%' class="text-center no-sort valign-middle">Material&nbsp;Cost<br>(Est)</th>
					<th width='8%' class="text-center no-sort valign-middle">Process Cost</th>
					<th width='8%' class="text-center no-sort valign-middle">COGS<br>(USD)</th>
					<th width='3%' class="text-center no-sort valign-middle">Rev</th>
					<!--<th class="text-center no-sort">Status</th>-->
					<th width='6%' class="text-center no-sort valign-middle">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:99%;'>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:80%;'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title2"></h4>
					</div>
					<div class="modal-body" id="view2">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="ModalView3" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:30%;'> 
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title3"></h4>
					</div>
					<div class="modal-body" id="view3">
					<div class="box">
						<div class="box-body">
							<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
								<tbody>
									<tr>
										<td colspan='2'><b>COST PROCESS</b></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Direct Labour</td>
										<td align='right' id='mdl_direct_labour'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Indirect Labour</td>
										<td align='right' id='mdl_indirect_labour'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consumable</td>
										<td align='right' id='mdl_consumable'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Machine Cost</td>
										<td align='right' id='mdl_machine'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mould & Mandrill Cost</td>
										<td align='right' id='mdl_mould_mandrill'></td>
									</tr>
									<tr>
										<td colspan='2'><b>COST FOH</b></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consumable FOH</td>
										<td align='right' id='mdl_foh_consumable'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Depresiasi FOH</td>
										<td align='right' id='mdl_foh_depresiasi'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Biaya Gaji Non Produksi</td>
										<td align='right' id='mdl_biaya_gaji_non_produksi'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Biaya Non Produksi</td>
										<td align='right' id='mdl_biaya_non_produksi'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Biaya Rutin Bulanan</td>
										<td align='right' id='mdl_biaya_rutin_bulanan'></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th align='center' width='60%'>TOTAL COST PRODUCT</th>
										<td align='right' width='40%'><b id='mdl_total_process'></b></td>
									</tr>
								</tfoot>
							</table>
							<br>
							<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
								<tbody>
									<tr>
										<td colspan='2'><b>DETAIL CYCLETIME</b></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Man Power</td>
										<td align='right' id='mdl_man_power'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Time</td>
										<td align='right' id='mdl_total_time'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Man Hours</td>
										<td align='right' id='mdl_man_hours'></td>
									</tr>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Machine Code</td>
										<td align='right' id='mdl_id_mesin'></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					</div>
					<div class="modal-footer">
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>
<?php $this->load->view('include/footer'); ?>
<style>
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
	.valign-middle{
		vertical-align: middle!important;
	}
</style>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		var status = $('#status').val();
		DataTables(status);
	});
	
	// loading_spinner();
	// $.ajax({
		// url : base_url +'index.php/'+active_controller+'/insert_select',
		// cache: false,
		// type: "POST",
		// dataType: "json",
		// success: function(response){
			 // swal.close()
		// }
	// });
	
	$(document).on('change','#status', function(e){
		e.preventDefault();
		var status = $('#status').val();
		DataTables(status);
	});
	
	$(document).on('click', '#detailBQ', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailBQ/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.detail_data', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL DATA BQ ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalviewDT/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '.data_approve', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>APPROVE PROJECT PRICE ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalAppCost/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '#detailPlant', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL PRODUCTION ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailPrice/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#update_cost', function(){
		swal({
		  title: "Update Estimate Price Project ?",
		  text: "Tunggu sampai 'Last Update by ' menunjukan nama user dan update jam sekarang. ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Update!",
		  cancelButtonText: "Tidak, Batalkan!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				// loading_spinner();
				$('#spinnerx').show();
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/insert_select',
					type		: "POST",
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
							$('#spinnerx').hide();
							window.location.href = base_url + active_controller + '/project';
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 7000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#spinnerx').hide();
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
						$('#spinnerx').hide();
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '.update_cycle', function(){
		var id_bq = $(this).data('id_bq');
		var id_milik = $(this).data('id_milik');
		
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
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/update_cycle_time/'+id_bq+'/'+id_milik,
					type		: "POST",
					// data		: formData,
					// data:{
							// 'id_bq' : id_bq
						// },
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
							
							$("#head_title").html("<b>DETAIL DATA BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalviewDT/'+data.id_bqx);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
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
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	//update price reference
	$(document).on('click', '#update_price', function(){
		var id_bq = $(this).data('id_bq');
		// alert(id_bq); return false;
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
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/update_all_price/'+id_bq,
					type		: "POST",
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
							
							$("#head_title").html("<b>DETAIL DATA BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalviewDT/'+data.id_bqx);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
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
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

    $(document).on('click', '#update_price_bq', function(){
		var id_bq = $(this).data('id_bq');
		// alert(id_bq); return false;
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
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/update_all_price_non_frp/'+id_bq,
					type		: "POST",
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
							
							$("#head_title").html("<b>DETAIL DATA BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalviewDT/'+data.id_bqx);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
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
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

    $(document).on('click', '#update_price_mat', function(){
		var id_bq = $(this).data('id_bq');
		// alert(id_bq); return false;
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
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/update_all_price_material/'+id_bq,
					type		: "POST",
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
							
							$("#head_title").html("<b>DETAIL DATA BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalviewDT/'+data.id_bqx);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
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
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
	//update price manual
	$(document).on('click', '#update_this_ipp', function(){
		// var id_bq = $(this).data('id_bq');
		// alert(id_bq); return false;
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
				var formData  	= new FormData($('#form_update_this_ipp')[0]);
				$.ajax({
					url			: base_url + active_controller+'/update_harga_this_ipp',
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
							
							$("#head_title").html("<b>DETAIL DATA BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url + active_controller+'/modalviewDT/'+data.id_bqx);
							$("#ModalView").modal();

							$('#ModalView2').modal('toggle');
						}
						else if(data.status == 0){
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
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('change', '.update_mh', function(e){
		e.preventDefault();
		var id_bq 	= $(this).data('id_bq');
		var id_milik = $(this).data('id_milik');
		var manpower = getNum($(this).data('manpower'));
		var manhours = getNum($(this).val().split(",").join(""));
		var thstatus = $(this).parent().find('.status_mh')
		var thsts = $(this).parent().find('.sts_mh')
		
		$.ajax({
			url			: base_url+active_controller+'/update_man_hours',
			type		: "POST",
			data		: {
				'id_bq' 	: id_bq,
				'id_milik' : id_milik,
				'manpower' : manpower,
				'manhours' : manhours
			},
			cache		: false,
			dataType	: 'json',				
			success		: function(data){								
				if(data.status == 1){							
					thstatus.html(data.pesan)
					thstatus.addClass(data.warna)
					thsts.show()
					console.log('berhasil')
				}
				else{
					thstatus.html(data.pesan)
					thstatus.addClass(data.warna)
					thsts.show()
					console.log('failed')
				}
			},
			error: function() {
				thstatus.html('Error!!!')
				thstatus.addClass("text-red")
				thsts.show()
				console.log('error')
			}
		});
	});

	$(document).on('keyup', '.update_mh', function(e){
		e.preventDefault();
		$(this).parent().find('.sts_mh').hide()
	});

	$(document).on('click', '.detail_process_cost2', function(e){
		e.preventDefault();
		var id_milik 				= $(this).data('id_milik');
		var id_bq 					= $(this).data('id_bq');
		var id_product 				= $(this).data('id_product');
		var direct_labour 			= number_format($(this).data('direct_labour'),2)
		var indirect_labour 		= number_format($(this).data('indirect_labour'),2)
		var consumable 				= number_format($(this).data('consumable'),2)
		var machine 				= number_format($(this).data('machine'),2)
		var mould_mandrill 			= number_format($(this).data('mould_mandrill'),2)
		var foh_consumable 			= number_format($(this).data('foh_consumable'),2)
		var foh_depresiasi 			= number_format($(this).data('foh_depresiasi'),2)
		var biaya_gaji_non_produksi = number_format($(this).data('biaya_gaji_non_produksi'),2)
		var biaya_non_produksi 		= number_format($(this).data('biaya_non_produksi'),2)
		var biaya_rutin_bulanan 	= number_format($(this).data('biaya_rutin_bulanan'),2)
		var total_process 			= number_format($(this).html(),2)
		var man_power 	= $(this).data('man_power');
		var man_hours 	= $(this).data('man_hours');
		var id_mesin 	= $(this).data('id_mesin');
		var total_time 	= $(this).data('total_time');

		$('#mdl_direct_labour').html(direct_labour)
		$('#mdl_indirect_labour').html(indirect_labour)
		$('#mdl_consumable').html(consumable)
		$('#mdl_machine').html(machine)
		$('#mdl_mould_mandrill').html(mould_mandrill)
		$('#mdl_foh_consumable').html(foh_consumable)
		$('#mdl_foh_depresiasi').html(foh_depresiasi)
		$('#mdl_biaya_gaji_non_produksi').html(biaya_gaji_non_produksi)
		$('#mdl_biaya_non_produksi').html(biaya_non_produksi)
		$('#mdl_biaya_rutin_bulanan').html(biaya_rutin_bulanan)
		$('#mdl_total_process').html(total_process)
		$('#mdl_man_power').html(man_power)
		$('#mdl_man_hours').html(man_hours)
		$('#mdl_id_mesin').html(id_mesin)
		$('#mdl_total_time').html(total_time)

		$("#head_title3").html("<b>DETAIL COST PROCESS ["+$(this).data('id_bq')+"]</b>");
		$("#ModalView3").modal();
	});

	$(document).on('click', '#MatDetail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url + active_controller+'/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('length')+'/'+$(this).data('id_bq'));
		$("#ModalView2").modal();
	});
		
	function DataTables(status = null){
		var dataTable = $('#my-grid').DataTable({
			"scrollY": "1000",
			"scrollCollapse" : true,
			"serverSide": true,
			"processing": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON/project',
				type: "post",
				data: function(d){
					d.status = status
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

	$(document).on('click', '#agusDetail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>FULL  ESTIMATION</b>");
		$("#view2").html("");
		$("#view2").load(base_url + active_controller+'/agus_modalviewDT/'+$(this).data('id_bq'));
		$("#ModalView2").modal();
	});	
</script>
