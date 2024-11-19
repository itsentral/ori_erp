<?php
$this->load->view('include/side_menu');
$active = (empty($tanda))?'active':'';
$active2 = (!empty($tanda))?'active':'';
$status = (!empty($tanda))?1:0;
if($tanda == 'request'){
	$status = 2;
}
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<input type="hidden" id='status' name='status' value='<?=$status;?>'>
<!-- <div class="alert alert-danger alert-dismissible">
	Proses FG/SPK Deadstok belum bisa digunakan, mohon tidak dicoba !!!
</div> -->
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<a href="<?php echo site_url('produksi/spk_print_outgoing') ?>" class="btn btn-md btn-info" style='float:right; margin-left:5px;'>Re-Print SPK</a>
			<?php
			if(empty($tanda)){
			?>
			<button type='button' class='btn btn-md btn-success' id='print'>Print</button>
			<?php } ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<?php
				if(empty($tanda)){
				?>
				<li role="presentation" class="<?=$active;?>"><a href="#request" class='request' aria-controls="request" role="tab" data-toggle="tab">Request</a></li>
				<?php } ?>
				<li role="presentation" class="<?=$active2?>"><a href="#aktual" class='aktual' aria-controls="aktual" role="tab" data-toggle="tab">Aktual</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<?php
				if(empty($tanda)){
				?>
				<div role="tabpanel" class="tab-pane <?=$active;?>" id="request"><br>
					<div class='pull-right'>
						<select name='no_ipp' id='no_ipp' class='form-control input-sm chosen-select' style='width:150px; float:right;'>
							<option value='0'>ALL IPP</option>
							<?php
							foreach($list_ipp as $val => $valx)
							{
								echo "<option value='".$valx['id_produksi']."'>".str_replace('PRO-','',$valx['id_produksi'])."</option>";
							}
							?>
						</select>
						</div>
					<br><br>
					<table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center" width='4%'>#</th>
								<th class="text-center" width='7%'>IPP</th>
								<th class="text-center">Customer</th>
								<th class="text-center" width='15%'>Product</th>
								<th class="text-center" width='8%'>No SPK</th>
								<th class="text-center" width='10%'>Spec</th>
								<th class="text-center" width='15%'>Id Product</th>
								<th class="text-center no-sort" width='7%'>Qty SO</th>
								<th class="text-center no-sort" width='7%'>Qty Sisa</th>
								<th class="text-center no-sort" width='7%'>Qty SPK</th>
								<th class="text-center no-sort" width='5%'>SPK Material</th>
								<th class="text-center no-sort" width='5%'>SPK FG</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<?php } ?>
				<div role="tabpanel" class="tab-pane <?=$active2?>" id="aktual"><br>
					<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center" width='3%'>#</th>
								<th class="text-center" width='10%'>Product</th>
								<th class="text-center" width='7%'>IPP</th>
								<th class="text-center">No SPK</th>
								<th class="text-center no-sort" width='15%'>SPK 1</th>
								<th class="text-center no-sort" width='12%'>SPK Mix</th>
								<th class="text-center no-sort" width='7%'>By</th>
								<th class="text-center no-sort" width='9%'>Date</th>
								<th class="text-center no-sort" width='5%'>Qty SPK</th>
								<th class="text-center no-sort" width='5%'>Qty Produksi</th>
								<th class="text-center no-sort" width='5%'>Qty Balance</th>
								<th class="text-center no-sort" width='5%'>Qty CLose</th>
								<th class="text-center no-sort">Option</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
 <!-- modal -->
 <div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
	<div class="modal-dialog"  style='width:70%; '>
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
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})
		// $("#example2").DataTable({
		// 	"stateSave" : true,
		// 	"bAutoWidth": true,
		// 	"destroy": true,
		// 	"processing": true,
		// 	"responsive": true,
		// 	"fixedHeader": {
		// 		"header": true,
		// 		"footer": true
		// 	}
		// });
		let status = $('#status').val();
		DataTables2(status);

		let no_ipp = $('#no_ipp').val();
		DataTables(no_ipp);

		$(document).on('change','#no_ipp', function(){
			let no_ipp = $('#no_ipp').val();
			DataTables(no_ipp);
		});

		$(document).on('click', '.detail_spk', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL SPK ["+$(this).data('kode_spk')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modalDetail/'+$(this).data('kode_spk'),
				success:function(data){
					$("#ModalView").modal();
					$("#view").html(data);

				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Timed Out ...',
					type				: "warning",
					timer				: 3000
					});
				}
			});
		});

		$(document).on('click', '.history_print', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>HISTORY PRINT OUTGOING</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/history_print_outgoing/'+$(this).data('no_ipp')+'/'+$(this).data('id_milik'),
				success:function(data){
					$("#ModalView").modal();
					$("#view").html(data);

				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Timed Out ...',
					type				: "warning",
					timer				: 3000
					});
				}
			});
		});

		$(document).on('keyup','.qty_spk', function(){
			var qty 	= getNum($(this).val().split(",").join(""));
			var qty_max = getNum($(this).parent().parent().parent().find('.sisa_spk').html().split(",").join(""));
			if(qty > qty_max){
				$(this).val(qty_max);
			}
		});

		$(document).on('click', '#print', function(){
			
			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist product minimal 1',
					type	: "warning"
				});
				return false;
			}
			// return false;
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
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/create_spk',  
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){
								window.open(base_url + active_controller+'/spk_baru/'+data.kode_spk,'_blank');
								window.location.href = base_url + active_controller + '/index_loose';
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
									type	: "warning",
									timer	: 3000
								});
							}
						},
						error: function() {
							swal({
							title		: "Error Message !",
							text		: 'An Error Occured During Process. Please try again..',						
							type		: "warning",								  
							timer		: 3000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		$(document).on('click', '.release_spk', function(e){
			e.preventDefault();

			let kode_spk = $(this).data('kode_spk');
			

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
						var baseurl=base_url + active_controller +'/release_spk';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: {
								kode_spk : kode_spk
							},
							cache		: false,
							dataType	: 'json',
							success		: function(data){
								if(data.status == 1){
									swal({
											title	: "Save Success!",
											text	: data.pesan,
											type	: "success",
											timer	: 3000
										});
									window.location.href = base_url + active_controller+"/index_loose/aktual";
								}
								else{
									swal({
										title	: "Save Failed!",
										text	: data.pesan,
										type	: "warning",
										timer	: 3000
									});
								}
							},
							error: function() {

								swal({
									title				: "Error Message !",
									text				: 'An Error Occured During Process. Please try again..',
									type				: "warning",
									timer				: 3000
								});
							}
						});
					} else {
					swal("Cancelled", "Data can be process again :)", "error");
					return false;
					}
			});
		});

		$(document).on('click', '.printSPKNew', function(){
			var ID 			= $(this).data('id');
			var print_ke 	= getNum($(this).data('print_ke')) + 1;
			
			swal({
			title: "Are you sure?",
			text: "Print SPK Mixing Urutan "+print_ke,
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
					window.open(base_url + active_controller+'/print_req_mixing_edit/'+ID,'_blank');
					swal.close()
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		$(document).on('click', '.go_to_deadstok', function(){
			var no_ipp 		= $(this).data('no_ipp');
			var id_milik 	= $(this).data('id_milik');
			var sisa_spk 	= $(this).data('sisa_spk');
			
			swal({
			title: "Are you sure?",
			text: "Booking dari deadstok ?",
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
					window.open(base_url + active_controller+'/booking_deadstok/'+no_ipp+'/'+id_milik+'/'+sisa_spk,'_self');
					swal.close()
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		//FILED JOIN TO OUTGOING
		$(document).on('click', '.go_to_outgoing', function(e){
			e.preventDefault();

			let no_ipp 			= $(this).data('no_ipp')
			let no_ipp_filter 	= $('#no_ipp').val()
			let id_milik 		= $(this).data('id_milik')
			let qty 			= getNum($('#spk_'+id_milik).val().split(",").join(""))

			if(qty < 1){
				swal({
					title	: "Error Message!",
					text	: 'Qty wajib diisi!',
					type	: "warning"
				});
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
						var baseurl = base_url + active_controller +'/field_joint_to_outgoing';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: {
								no_ipp_filter : no_ipp_filter,
								no_ipp : no_ipp,
								id_milik : id_milik,
								qty : qty
							},
							cache		: false,
							dataType	: 'json',
							success		: function(data){
								if(data.status == 1){
									swal({
											title	: "Save Success!",
											text	: data.pesan,
											type	: "success",
											timer	: 3000
										});
										DataTables(data.no_ipp_filter);
										window.open(base_url + active_controller+'/spk_baru/'+data.kode_spk,'_blank');
								}
								else{
									swal({
										title	: "Save Failed!",
										text	: data.pesan,
										type	: "warning",
										timer	: 3000
									});
								}
							},
							error: function() {
								swal({
									title				: "Error Message !",
									text				: 'An Error Occured During Process. Please try again..',
									type				: "warning",
									timer				: 3000
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

	function DataTables(no_ipp=null){
		var dataTable = $('#my-grid').DataTable({
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
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
				url : base_url + active_controller+'/server_side_request',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			},
			"processing": true,
			"search": {
				return: true
			},
			"serverSide": true,
		});
	}

	function DataTables2(status=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"search": {
				return: true
			},
			"destroy": true,
			"autoWidth": false,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 1, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_aktual',
				type: "post",
				data: function(d){
					d.status = status
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}
</script>
