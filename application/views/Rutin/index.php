<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['create']=='1'){
				?>
		  		<a href="<?php echo site_url('con_nonmat/add_new') ?>" class="btn btn-sm btn-success" id='btn-add'><i class="fa fa-plus"></i> Add</a>
		  	<?php
			}
			if($akses_menu['download']=='1'){
				?>
		  		<button type='button' class='btn btn-sm btn-primary' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
		  		<?php
			}
		  	?>
		</div><br><br>
		<div class="box-tool pull-left">
			<select id='inventory' name='inventory' class='form-control input-sm chosen-select' style='min-width:150px; float:left; margin-bottom: 5px;'>
				<option value='0'>All Kategori Stok</option>
				<?php
					foreach($inventory AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['category'])."</option>";
					}
				?>
			</select>

			<select name="status" id="status" class='form-control input-sm chosen-select'>
				<option value="X">All Status</option>
				<option value="1">Active</option>
				<option value="0">Non-Active</option>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Kode</th> 
					<th class="text-center">Kategori Stok</th> 
					<th class="text-center">Excel Code</th>
					<th class="text-center">Item Code</th>
					<th class="text-center">Accurate Code</th>
					<th class="text-center">Material Name</th>
					<th class="text-center">Trade Name</th>
					<th class="text-center">Spec</th>
					<th class="text-center">Brand</th>
					<th class="text-center">Status</th>
					<th class="text-center">Option</th>
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
		<div class="modal-dialog"  style='width:80%; '>
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
		var inventory 		= $('#inventory').val();
		var status 		= $('#status').val();
		DataTables(inventory,status);
		
		$(document).on('change','#inventory, #status', function(e){
			e.preventDefault();
			var inventory 	= $('#inventory').val();
			var status 	= $('#status').val();
			DataTables(inventory, status);
		});
	});

	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		var inventory = $('#inventory').val();
		var status = $('#status').val();
		var Links		= base_url + active_controller+'/ExcelMasterDownload/'+inventory+'/'+status;
		window.open(Links,'_blank');
	});

	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL CONSUMABLE ["+$(this).data('code_group')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalDetail/'+$(this).data('code_group'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				title	: "Error Message !",
				text	: 'Connection Timed Out ...',
				type	: "warning",
				timer	: 3000
				});
			}
		});
	});

	function DataTables(inventory = null, status = null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"processing": true,
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
				url : base_url + active_controller+'/data_side_rutin',
				type: "post",
				data: function(d){
					d.inventory = inventory,
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

	$(document).on('click', '.deleted', function(){
		var code_group	= $(this).data('code_group');

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
					url			: base_url + active_controller+'/hapus/'+code_group,
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
								  timer	: 5000
								});
							window.location.href = base_url + active_controller;
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000
							});
						}
					},
					error: function() {
						swal({
						  title	: "Error Message !",
						  text	: 'An Error Occured During Process. Please try again..',
						  type	: "warning",
						  timer	: 5000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});


</script>
