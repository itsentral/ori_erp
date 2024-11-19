<?php
$this->load->view('include/side_menu');
$update = 'System';
$dated = date('Y-m-d H:i:s');
if(!empty($get_by[0]['create_by'])){
	$update = $get_by[0]['create_by'];
	$dated = $get_by[0]['create_date'];
}
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
			<br><div style='color:red;'><b>Last Update by <span style='color:green;'><?= strtoupper(strtolower($get_by[0]['user_id']))."</span> On <u>".date('d-m-Y H:i:s', strtotime($get_by[0]['created']));?></u></b></div>
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
					<th class="text-center">#</th>
					<th class="text-center">IPP</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Project</th>
					<th class="text-center no-sort">Series</th> 
					<th class="text-center no-sort">Rev</th>
					<th class="text-center no-sort">SO</th>
					<th class="text-center no-sort">FD</th>
					<th class="text-center no-sort">Status</th>
					<th class="text-center no-sort">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:99%; '>
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
		<div class="modal-dialog"  style='width:80%; '>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView3" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title3"></h4>
					</div>
					<div class="modal-body" id="view3">
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
	<div class="modal fade" id="ModalView4" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:40%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title4"></h4>
					</div>
					<div class="modal-body" id="view4">
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
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		DataTables();
	});
	
	$(document).on('click', '.detailSO', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL SALES ORDER ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalViewSO/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.detailFD', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalViewFD/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.AppFinalDrawing', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>APPROVE FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppFD/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.AppFinalDrawingNew', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>APPROVE FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modalAppFD_new/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',						
				  type				: "warning",								  
				  timer				: 7000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click', '.download_excel', function(){
		var id_bq		= $(this).data('id_bq');
		var Links		= base_url +'index.php/'+ active_controller+'/ExcelBudgetSo/'+id_bq;
		window.open(Links,'_blank');
	});

	$(document).on('click', '#approvedFD', function(){
		var bF				= $('#id_bq').val();
		var status 			= $('#status').val();
		var approve_reason 	= $('#approve_reason').val();
		
		if(status == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Action approve belum dipilih ...',
			  type	: "warning"
			});
			$('#approvedQ').prop('disabled',false);
			return false;
		}
		
		if(status == 'N' && approve_reason == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Alasan reject masih kosong ...',
			  type	: "warning"
			});
			$('#approvedQ').prop('disabled',false);
			return false;
		}
		
		if(status == 'M' && approve_reason == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Alasan reject masih kosong ...',
			  type	: "warning"
			});
			$('#approvedQ').prop('disabled',false);
			return false;
		}
		
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Approve Final Drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					// url			: base_url+'index.php/'+active_controller+'/AppBQFDEstNew/'+bF,
					url			: base_url+'index.php/'+active_controller+'/AppBQFDMatPlan/'+bF,
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
							window.location.href = base_url + active_controller+'/approve';
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
							window.location.href = base_url + active_controller+'/approve';
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

	//Lewat Material Planning
	$(document).on('click', '#approvedFDNew', function(){
		var bF				= $('#id_bq').val();
		var status 			= $('#status').val();
		var approve_reason 	= $('#approve_reason').val();
		
		if(status == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Action approve belum dipilih ...',
			  type	: "warning"
			});
			$('#approvedFDNew').prop('disabled',false);
			return false;
		}
		
		if(status == 'N' && approve_reason == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Alasan reject masih kosong ...',
			  type	: "warning"
			});
			$('#approvedFDNew').prop('disabled',false);
			return false;
		}
		
		if(status == 'M' && approve_reason == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Alasan reject masih kosong ...',
			  type	: "warning"
			});
			$('#approvedFDNew').prop('disabled',false);
			return false;
		}
		
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Approve Final Drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/AppBQFDMatPlan/'+bF,
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
							window.location.href = base_url + active_controller+'/approve';
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
							window.location.href = base_url + active_controller+'/approve';
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
	
	
	
	$(document).on('click', '#update_cost', function(){
		swal({
		  title: "Update Approve Final Drawing ?",
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
					url			: base_url+'index.php/'+active_controller+'/insert_final_drawing',
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
							window.location.href = base_url + active_controller+'/approve';
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
	
	//NEW
	//Lewat Material Planning
	$(document).on('click', '#approvedFD_All', function(){
		var bF				= $('#id_bq').val();
		var status 			= $('#status').val();
		var approve_reason 	= $('#approve_reason').val();
		
		if($('input[type=checkbox]:checked').length == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Checklist milimal satu terlebih dahulu',
			  type	: "warning"
			});
			$('#approvedFD_All').prop('disabled',false);
			return false;
		}
		
		if(status == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Action approve belum dipilih ...',
			  type	: "warning"
			});
			$('#approvedFD_All').prop('disabled',false);
			return false;
		}
		
		if(status == 'N' && approve_reason == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Alasan reject masih kosong ...',
			  type	: "warning"
			});
			$('#approvedFD_All').prop('disabled',false);
			return false;
		}
		
		if(status == 'M' && approve_reason == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Alasan reject masih kosong ...',
			  type	: "warning"
			});
			$('#approvedFD_All').prop('disabled',false);
			return false;
		}
		
		// alert('Sampai Sini');
		// return false;
		
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Approve Final Drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url+active_controller+'/AppBQFD_All/'+bF, 
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
							window.location.href = base_url + active_controller+'/approve';
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
							window.location.href = base_url + active_controller+'/approve';
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
				url : base_url + active_controller+'/getDataJSONAppFD',
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
