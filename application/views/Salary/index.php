<?php $this->load->view('include/side_menu'); ?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
			<div class="box-tool pull-right">
			<?php if($akses_menu['create']=='1'){ ?>
				<a href="<?php echo base_url('salary/add') ?>" class="btn btn-md btn-success" id='btn-add'>
					<i class="fa fa-plus"></i> Add Salary
				</a>
				<a href="<?php echo base_url('salary/jurnal') ?>" class="btn btn-md btn-primary hidden" id='btn-jurnal'>
					<i class="fa fa-plus"></i> Jurnal
				</a>				
			<?php } ?>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive col-md-12">
				<table id="example1" class="table table-bordered table-striped" width='100%'>
					<thead>
						<tr class='bg-blue' >
							<th class="text-center">No</th>
							<th class="text-center">Kode</th>
							<th class="text-center">Periode</th>
							<th class="text-center">Keterangan</th>
							<th class="text-center">Periode</th>
							<th class="text-center">Status</th>
							<th class="text-center" width="100">Option</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<!-- /.box-body -->
	</div>

 <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:100%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
				</div>
				<div class="modal-body" id="view"></div>
				<div class="modal-footer"><button type="button" class="btn btn-default " data-dismiss="modal">Close</button></div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>
<!-- DataTables -->
<?php $this->load->view('include/footer'); ?>
<!-- page script -->
<script type="text/javascript">
	$(document).ready(function(){
		DataTables();
	});
	function DataTables(kategori=null){
		let total_aset	= 0;
		let total_susut	= 0;
		let total_sisa	= 0;
		var dataTable = $('#example1').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy"	: true,
			"responsive": true,
			"processing": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting"		: [[ 1, "asc" ]],
			"columnDefs"	: [ {
				"targets"	: 'no-sort',
				"orderable"	: false,
				},
				{ className: 'text-right', targets: [7] }
			],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side',
				type: "post",
				data: function(d){
					d.kategori = kategori
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				},
				 dataSrc: function ( data ) {
				   total_aset = data.recordsAset;
				   return data.data;
				 }
			},
		});
	}

	$(document).on('click', '.delete', function(e){
		e.preventDefault();
		var id			= $(this).data('id');
		swal({
			  title: "Are you sure?",
			  text: "Delete this data!",
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
					var baseurl		= base_url + active_controller +'/hapus/'+id;
					$.ajax({
						url			: baseurl,
						type		: "POST",
						cache		: false,
						dataType	: 'json',
						processData	: false,
						contentType	: false,
						success		: function(data){
							if(data.status == 1){
								swal({
									  title	: "Delete Success!",
									  text	: data.pesan,
									  type	: "success",
									  timer	: 7000
									});
								window.location.href = base_url + active_controller;
							}
							else{
								swal({
								  title	: "Delete Failed!",
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
							  timer				: 3000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again", "error");
				return false;
			  }
		});
	});
	$(document).on('click', '.approve', function(e){
		e.preventDefault();
		var id			= $(this).data('id');

		swal({
			  title: "Are you sure?",
			  text: "Update this data!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-info",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					var baseurl		= base_url + active_controller +'/approve/'+id;
					$.ajax({
						url			: baseurl,
						type		: "POST",
						cache		: false,
						dataType	: 'json',
						processData	: false,
						contentType	: false,
						success		: function(data){
							if(data.status == 1){
								swal({
									  title	: "Approve Success!",
									  text	: data.pesan,
									  type	: "success",
									  timer	: 7000
									});
								window.location.href = base_url + active_controller;
							}
							else{
								swal({
								  title	: "Approve Failed!",
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
							  timer				: 3000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again", "error");
				return false;
			  }
		});
	});
</script>
