<?php
$this->load->view('include/side_menu');
// echo get_status_approve_pr('94');
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
		<table class="table table-bordered table-striped" id="example1" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Asal Permintaan</th>
					<th class="text-center">No Pengajuan</th>
					<th class="text-center">Nomor SO</th>
					<th class="text-center">Untuk Kebutuhan</th>
					<th class="text-center">Request By</th>
					<th class="text-center">Request Date</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$nomor = 0;
				foreach($data_pr as $val => $row){ 
					$tanda = substr($row['no_ipp'],0,1);
					$tanda2 = substr($row['no_ipp'],0,4);
					$no_ipp = $row['no_ipp'];
					if($tanda != 'P'){
						if($tanda2 != 'IPPT'){
							$no_so = $ArrGetSO["BQ-".$row['no_ipp']];
						}
						else{
							$no_so = 'TANKI';
						}
					}
					$no_ipp2 = $row['no_ipp'];
					$no_ipp3 = $row['no_ipp'];
					$kebutuhan = "Project ".strtoupper(get_name('production', 'project', 'no_ipp', $row['no_ipp']));
					if($tanda == 'P'){
						$no_ipp = "Re-Order Point ".date('d-m-Y', strtotime($row['created_date']));
						$no_ipp2 = date('d-m-Y', strtotime($row['created_date']));
						$no_ipp3 = date('Y-m-d', strtotime($row['created_date']));
						$kebutuhan = "Pemenuhan Stock Material";
						$no_so = '';
					}
		
					if(check_atatus_pr($tanda, $no_ipp3, $row['created_by']) > 0){
						$nomor++;

						$id_user = get_name('users','id_user','username',$row['created_by']);
						$save			= "";
						$view			= "<button type='button'class='btn btn-sm btn-warning view_pr' title='View PR' data-tanda='".$tanda."' data-no_ipp='".$no_ipp2."' data-no='".$nomor."' data-user='".$id_user."'><i class='fa fa-eye'></i></button>";
						$print			= "&nbsp;<a href='".site_url($this->uri->segment(1).'/print_detail_pr/'.$no_ipp2.'/'.$tanda.'/'.$id_user)."' class='btn btn-sm btn-success' target='_blank' title='Print PR' data-role='qtip'><i class='fa fa-print'></i></a>";
						if($row['sts_app'] == 'N'){
							$save		= "&nbsp;<button type='button'class='btn btn-sm btn-info app_pr' title='Approve PR' data-tanda='".$tanda."' data-no_ipp='".$no_ipp2."' data-no='".$nomor."' data-user='".$id_user."'><i class='fa fa-check'></i></button>";
						}
								
						echo "<tr>";
							echo "<td align='center'>".$nomor."</td>";
							echo "<td align='left'>".$no_ipp."</td>";
							echo "<td align='center'>".$row['no_ipp']."</td>";
							echo "<td align='center'>".$no_so."</td>";
							echo "<td align='left'>".$kebutuhan."</td>";
							echo "<td align='center'>".$row['created_by']."</td>";
							echo "<td align='center'>".date('d-M-Y', strtotime($row['created_date']))."</td>";
							echo "<td align='center'>".$view.$save.$print."</td>";
						echo "</tr>";
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  <div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
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
	
</form>
<?php $this->load->view('include/footer'); ?>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<script>
	$(document).ready(function(){
        // DataTables();
        $('.maskM').maskMoney();
		$('.tgl').datepicker();
    });
	
	$(document).on('click', '.view_pr', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL PR ["+$(this).data('no_ipp')+"]</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_detail_pr/'+$(this).data('no_ipp')+'/'+$(this).data('tanda'),
			type: "POST",
			data: {
				"id_user" 	: $(this).data('user')
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
		loading_spinner();
		$("#head_title2").html("<b>APPROVE PR ["+$(this).data('no_ipp')+"]</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_approve_pr/'+$(this).data('no_ipp')+'/'+$(this).data('tanda'),
			type: "POST",
			data: {
				"id_user" 	: $(this).data('user')
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
				  timer				: 5000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click','#app_pr', function(){
		
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
					url			: base_url + active_controller+'/save_approve_pr_new',
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
							// window.location.href = base_url + active_controller+'/approval_pr_new';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	: data.id_user
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
									timer				: 5000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								}
							});
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

	$(document).on('click','#app_pr_acc', function(){
		
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
					url			: base_url + active_controller+'/save_approve_pr_new_aksesoris/acc',
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
							// window.location.href = base_url + active_controller+'/approval_pr_new';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	: data.id_user
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
									timer				: 5000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								}
							});
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
	
	$(document).on('click','.rejectPR', function(){
		var id 			= $(this).data('id');
		var id_material = $(this).data('id_material');
		
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
					url			: base_url + active_controller+'/reject_sebagian_pr_new',
					type		: "POST",
					data: {
						"id" 			: id,
						"id_material" 	: id_material,
						"no_ipp"		: $('#no_ipp').val(),
						"tanda"			: $('#tanda').val(),
						"id_user"		: $('#id_user').val()
					},
					cache		: false,
					dataType	: 'json',
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
							// window.location.href = base_url + active_controller+'/approval_pr_new';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	: data.id_user
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
									timer				: 5000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								}
							});
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

	$(document).on('click','.appPR', function(){
		var id 			= $(this).data('id');
		var id_material = $(this).data('id_material');
		var nomor 		= $(this).data('nomor');
		var qty_revisi 	= $('#tot_rev_'+nomor).val();
		
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
					url			: base_url + active_controller+'/approve_sebagian_pr_new',
					type		: "POST",
					data: {
						"id" 			: id,
						"id_material" 	: id_material,
						"qty_revisi" 	: qty_revisi,
						"no_ipp"		: $('#no_ipp').val(),
						"tanda"			: $('#tanda').val(),
						"id_user"		: $('#id_user').val()
					},
					cache		: false,
					dataType	: 'json',
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
							// window.location.href = base_url + active_controller+'/approval_pr_new';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	: data.id_user
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
									timer				: 5000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								}
							});
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
	
	$(document).on('click','.rejectPR_acc', function(){
		var id 			= $(this).data('id');
		var id_material = $(this).data('id_material');
		
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
					url			: base_url + active_controller+'/reject_sebagian_pr_new_acc',
					type		: "POST",
					data: {
						"id" 			: id,
						"id_material" 	: id_material,
						"no_ipp"		: $('#no_ipp').val(),
						"tanda"			: $('#tanda').val(),
						"id_user"		: $('#id_user').val()
					},
					cache		: false,
					dataType	: 'json',
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
							// window.location.href = base_url + active_controller+'/approval_pr_new';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	: data.id_user
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
									timer				: 5000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								}
							});
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

	$(document).on('click','.appPR_acc', function(){
		var id 			= $(this).data('id');
		var id_material = $(this).data('id_material');
		var nomor 		= $(this).data('nomor');
		var qty_revisi 	= $('#tot_rev_acc_'+nomor).val();
		
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
					url			: base_url + active_controller+'/approve_sebagian_pr_new_acc',
					type		: "POST",
					data: {
						"id" 			: id,
						"id_material" 	: id_material,
						"qty_revisi" 	: qty_revisi,
						"no_ipp"		: $('#no_ipp').val(),
						"tanda"			: $('#tanda').val(),
						"id_user"		: $('#id_user').val()
					},
					cache		: false,
					dataType	: 'json',
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
							// window.location.href = base_url + active_controller+'/approval_pr_new';
							loading_spinner();
							$("#head_title2").html("<b>APPROVE PR ["+data.no_ipp+"]</b>");
							$.ajax({
								url: base_url + active_controller+'/modal_approve_pr/'+data.no_ipp+'/'+data.tanda,
								type: "POST",
								data: {
									"id_user" 	: data.id_user
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
									timer				: 5000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								}
							});
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
    

		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
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
				url : base_url + active_controller+'/server_side_app_pr_new',
				type: "post",
				// data: function(d){
					// d.gudang = $('#gudang').val()
				// },
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
script>
