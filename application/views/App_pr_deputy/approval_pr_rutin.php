<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
            
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<input type='hidden' id='tanda' value='<?=$tanda;?>'>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>No</th>
					<th class="text-center">Kode Pengajuan</th>
					<th class="text-center">Asal Permintaan</th>
					<th class="text-center">Untuk Kebutuhan</th>
					<th class="text-center">Request By</th>
					<th class="text-center">Request Date</th>
					<!-- <th class="text-center">Status</th> -->
					<th class="text-center no-sort" width='10%'>#</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  <div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:95%; '>
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
	
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var tanda = $('#tanda').val();
        DataTables(tanda);
        $('.maskM').maskMoney();
		$('.tgl').datepicker();
    });
	
	$(document).on('click', '.view_pr', function(e){ 
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL PR ["+$(this).data('pengajuangroup')+"]</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_detail_pr/'+$(this).data('no_ipp')+'/'+$(this).data('sts_app')+'/'+$(this).data('tanda'),
			type: "POST",
			data: {
				"id_user" 	: $(this).data('user'),
				"pengajuangroup" 	: $(this).data('pengajuangroup'),
			},
			cache		: false,
			// dataType	: 'json',
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

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
	
	$(document).on('click', '.app_pr', function(e){
		e.preventDefault();
		// alert($(this).data('no_ipp'));
		loading_spinner();
		$("#head_title2").html("<b>APPROVE PR ["+$(this).data('pengajuangroup')+"]</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_approve_pr/'+$(this).data('no_ipp')+'/'+$(this).data('tanda'),
			type: "POST",
			data: {
				"id_user" 				: $(this).data('user'),
				"pengajuangroup" 	: $(this).data('pengajuangroup'),
				"category_awal" 		: $(this).data('category_awal'),
			},
			cache		: false,
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 5000
				});
			}
		});
	});
	
	$(document).on('click','#app_pr', function(e){
		e.preventDefault();
		
		var tanda = $('#tanda').val();
		
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
					url			: base_url + active_controller+'/approve_pr_rutin/'+tanda,
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
							window.location.href = base_url + active_controller+'/approval_pr_rutin/'+data.tanda;
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',
							type	: "warning",
							timer	: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
	$(document).on('click','.rejectPR', function(){
		// alert("Tahan");
		// return false;
		var no_pengajuan 	= $(this).data('no_pengajuan');
		var no_ipp		 	= $('#no_ipp').val();
		var tanda 			= $('#tanda').val();
		var id_user 		= $('#id_user').val();
		
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
					url			: base_url + active_controller+'/reject_sebagian_pr_rutin',
					type		: "POST",
					data: {
						"no_pengajuan" 		: no_pengajuan,
						"no_ipp" 			: no_ipp,
						"tanda" 			: tanda,
						"id_user" 			: id_user
					},
					cache		: false,
					dataType	: 'json',
					success		: function(data){
						if(data.status == 1){
							swal({
									title	: "Save Success!",
									text	: data.pesan,
									type	: "success",
									timer	: 7000
								});
							// window.location.href = base_url + active_controller+'/approval_pr_rutin';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	:data.id_user
								},
								cache		: false,
								success:function(data){
									$("#ModalView2").modal();
									$("#view2").html(data);

								},
								error: function() {
									swal({
									title				: "Error Message !",
									text				: 'Connection Timed Out ...',
									type				: "warning",
									timer				: 5000
									});
								}
							});
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',
							type	: "warning",
							timer	: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '#rejectPR_check', function(){
		
		if($('.chk_personal:checked').length == 0){
			swal({
				title	: "Error Message!",
				text	: 'Checklist Item Reject',
				type	: "warning"
			});
			$('#rejectPR_check').prop('disabled',false);
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
					url			: base_url+active_controller+'/reject_all_pr_rutin',
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
							
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	:data.id_user
								},
								cache		: false,
								success:function(data){
									$("#ModalView2").modal();
									$("#view2").html(data);

								},
								error: function() {
									swal({
									title				: "Error Message !",
									text				: 'Connection Timed Out ...',
									type				: "warning",
									timer				: 5000
									});
								}
							});
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

		
	function DataTables(tanda = null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
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
				url : base_url + active_controller+'/server_side_app_pr_rutin',
				type: "post",
				data: function(d){
					d.tanda = tanda
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
