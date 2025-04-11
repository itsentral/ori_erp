<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
	
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-center">BQ</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Order Type</th>
					<th class="text-center">Series Product</th>
					<th class="text-center">Rev</th>
					<th class="text-center">Status</th>
					<th class="text-center" width='150px'>Option</th>
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
		DataTables();
	});
	
	$(document).on('click', '#detailBQ', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ IN FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailBQ/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#editBQ', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>EDIT STRUCTURE BQ IN FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalEditBQ/'+$(this).data('id_bq')+'/'+$(this).data('ciri'));
		$("#ModalView2").modal();
	});
	
	$(".numberOnly").on("keypress keyup blur",function (event) {    
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
	
	//DELETE SEBAGIAN BQ
	$(document).on('click', '#del', function(){
		var bF	= $(this).data('id');
		var bF2	= $(this).data('id_bq_header');
		// alert(bF);
		// return false;
		swal({
		  title: "Are you sure?",
		  text: "Data akan terhapus secara permanen ?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Yes, Process it!",
		  cancelButtonText: "No, cancel process!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/DeleteEdit/'+bF+'/'+bF2,
					type		: "POST",
					data		: "id="+bF,
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
							// window.location.href = base_url + active_controller+'/modalEditBQ/'+$(this).data('id_bq');
							
							$("#head_title2").html("<b>EDIT STRUCTURE BQ ["+data.id_bq+"]</b>");
							$("#view2").load(base_url +'index.php/'+ active_controller+'/modalEditBQ/'+data.id_bq);
							$("#ModalView2").modal();
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
	
	$(document).on('click', '#ajuAppBQ', function(){
		var bq		= $(this).data('id_bq');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Menuju ke proses estimasi ...",
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
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/ajukanAppBQ/'+bq,
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
							window.location.href = base_url + active_controller;
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
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
			"deferRender": true,
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
				url : base_url +'index.php/'+active_controller+'/getDataJSONEst',
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
