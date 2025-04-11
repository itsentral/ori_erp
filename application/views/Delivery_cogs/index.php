<?php
$this->load->view('include/side_menu');//print_r($akses_menu);
?>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<div class="box-tool pull-right">
			<?php if($akses_menu['create']=='1'){ ?>
			  <button type='button' class="btn btn-md btn-info" id='add'><i class="fa fa-plus"></i> Add</button>
			  <?php } ?>
			</div>
		</div>
		<div class="box-body">
			<div class="table-responsive col-lg-12">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">#</th>
						<th class="text-center">No Delivery</th>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Keterangan</th>
						<th class="text-center">Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			</div>
		</div>
	</div>
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:60%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
				</div>
				<div class="modal-body" id="view"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		DataTables();
		$('.chosen-select').chosen();
        $('.datepicker').datepicker({
            dateFormat: 'dd-MM-yy',
            changeMonth:true,
            changeYear:true
        });
	});
	$(document).on('click', '#add', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>ADD</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/add_data');
		$("#ModalView").modal();
	});
	$(document).on('click', '.edit', function(e){
		e.preventDefault();
		loading_spinner();
		var id = $(this).data('code');
		$("#head_title").html("<b>EDIT</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/add_data/'+id+'/edit');
		$("#ModalView").modal();
	});
	$(document).on('click', '.approve', function(e){
		e.preventDefault();
		loading_spinner();
		var id = $(this).data('code');
		$("#head_title").html("<b>UPDATE</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/add_data/'+id+'/update');
		$("#ModalView").modal();
	});
	$(document).on('click', '.view', function(e){
		e.preventDefault();
		loading_spinner();
		var id = $(this).data('code');
		$("#head_title").html("<b>VIEW</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/add_data/'+id+'/view');
		$("#ModalView").modal();
	});
    $(document).on('click', '.delete', function(){
		var code	= $(this).data('code');
		swal({
		  title: "Are you sure?",
		  text: "Delete this data ?",
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
					url			: base_url+'index.php/'+active_controller+'/hapus_data/'+code,
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
							window.location.href = base_url + active_controller +'/index';
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
			"processing" : true, "serverSide": true, "stateSave" : true, "bAutoWidth": true, "destroy": true, "responsive": true,
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev", "sNext": "Next"
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
				url : base_url +'index.php/'+active_controller+'/data_side',
				type: "post",
				data: function(d){
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
