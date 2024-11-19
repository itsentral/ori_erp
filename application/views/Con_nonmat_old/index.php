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
		  <a href="<?php echo site_url('con_nonmat/add_new') ?>" class="btn btn-md btn-success" id='btn-add'>
			<i class="fa fa-plus"></i> Add
		  </a>
		  <!-- <a href="<?php echo site_url('con_nonmat/ExcelMasterDownload') ?>" target='_blank' class="btn btn-md btn-info">
			<i class="fa fa-file-excel-o"></i> Download
		  </a> -->
		  <?php
			}
		  ?>
		</div><br><br>
		<div class="box-tool pull-left">
			<select id='inventory' name='inventory' class='form-control input-sm chosen-select' style='min-width:150px; float:left; margin-bottom: 5px;'>
				<option value='0'>All Inventory Type</option>
				<?php
					foreach($inventory AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['category'])."</option>";
					}
				?>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center">Inventory Type</th> 
					<th class="text-center">Spesification</th>
					<th class="text-center">Brand</th>
					<th class="text-center">Order Point</th>
					<th class="text-center">Lead Time</th>
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
		DataTables(inventory);
		
		$(document).on('change','#inventory', function(e){
			e.preventDefault();
			var inventory 	= $('#inventory').val();
			DataTables(inventory);
		});
	});

	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL CONSUMABLE ["+$(this).data('code_group')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalDetail/'+$(this).data('code_group'));
		$("#ModalView").modal();
	});

	function DataTables(inventory = null){
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
					d.inventory = inventory
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
		// alert(bF);
		// return false;
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
					url			: base_url+'index.php/'+active_controller+'/hapus/'+code_group,
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
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});


</script>
