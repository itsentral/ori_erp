<?php
$this->load->view('include/side_menu'); 
?>
<form action="#" method="POST" id="form_proses_bro">
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
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'> 
				<thead>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>#</th>
						<th class="text-center" width='10%'>IPP</th>
						<th class="text-center" width='15%'>Product</th>
						<th class="text-center" width='26%'>Product ID</th>
						<th class="text-center" width='14%'>Product To</th>
						<th class="text-center" width='10%'>By</th>
						<th class="text-center" width='10%'>Date</th>
						<th class="text-center" width='10%'>Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
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
		<!-- modal -->
		<div class="modal fade" id="ModalView2">
			<div class="modal-dialog"  style='width:90%; '>
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
		<!-- modal -->	
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
        $('#spinnerx').hide();
		DataTables();
	});

	$(document).on('click', '#detailPlant', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL PRODUCTION ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#printSPK', function(e){
		e.preventDefault();
		$("#head_title").html("<b>PRINT SPK ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalPrint/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#Perbandingan', function(e){ 
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>HISTORY INPUT ["+$(this).data('id_produksi')+" / "+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalPerbandingan_tmp/'+$(this).data('id_product')+'/'+$(this).data('id_pro_detail')+'/'+$(this).data('id_produksi')+'/'+$(this).data('qty_awal')+'/'+$(this).data('qty_akhir')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});

	$(document).on('click', '.check_real', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>EDIT REAL INPUT PRODUKSI ["+$(this).data('id_produksi')+" / "+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalEditReal/'+$(this).data('id_product')+'/'+$(this).data('id_pro_detail')+'/'+$(this).data('id_produksi')+'/'+$(this).data('qty_awal')+'/'+$(this).data('qty_akhir')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});

    $(document).on('click', '#update_cost', function(){
		swal({
		  title: "Update Check History Production ?",
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
					url			: base_url+'index.php/insert_select/insert_select_history_production_check',
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
							window.location.href = base_url + active_controller + '/check_real';
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

	$(document).on('click', '#updateCheck', function(e){
		e.preventDefault();

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
					var baseurl=base_url + active_controller +'/update_real_edit';
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
								window.location.href = base_url + active_controller+"/check_real/";
							}
							else if(data.status == 2){
								swal({
									title	: "Save Failed!",
									text	: data.pesan,
									type	: "warning",
									timer	: 7000
								});
							}
							$('#updateCheck').prop('disabled',false);
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
							$('#updateCheck').prop('disabled',false);
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#updateCheck').prop('disabled',false);
				return false;
				}
		});
	});

	$(document).on('click', '#sendCheck', function(e){
		e.preventDefault();

		swal({
				title: "Are you sure?",
				text: "Pastikan semua data sudah benar!",
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
					var baseurl=base_url + active_controller +'/real_send';
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
								window.location.href = base_url + active_controller+"/check_real/";
							}
							else if(data.status == 2){
								swal({
									title	: "Save Failed!",
									text	: data.pesan,
									type	: "warning",
									timer	: 7000
								});
							}
							$('#updateCheck').prop('disabled',false);
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
							$('#updateCheck').prop('disabled',false);
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#updateCheck').prop('disabled',false);
				return false;
				}
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
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url +'index.php/'+active_controller+'/getDataJSON2_check',
				type: "post",
				data: function(d){
					// d.kode_partner = $('#kode_partner').val()
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
