<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
        
        </div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">IPP</th>
						<th class="text-center th">SO</th>
						<th class="text-center th">Customer</th>
						<th class="text-center th">Project</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">Spec</th>
						<th class="text-center th">Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		<!-- </div> -->
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:60%; '>
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
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		DataTables();
	});

	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ESTIMATION</b>");
		$("#view").load(base_url + active_controller+'/estimasi_detail/'+$(this).data('id'));
		$("#ModalView").modal();
	});

	$(document).on('click', '.approve', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>APPROVE ESTIMATION</b>");
		$("#view").load(base_url + active_controller+'/estimasi_approve_action/'+$(this).data('id'));
		$("#ModalView").modal();
	});

	$(document).on('click', '#approve', function(){
		var kode	= $('#kode').val();
		var action	= $('#action').val();
		var reason	= $('#reason').val();
		
		swal({
		  title: "Are you sure?",
		  text: "Proses approval ?",
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
					url			: base_url+active_controller+'/process_approval',
					type		: "POST",
					data		: {
						'kode' : kode,
						'action' : action,
						'reason' : reason
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
								window.location.href = base_url + active_controller;
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
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 7000
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
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"lengthChange": true,
			"destroy": true,
			"processing": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON',
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
