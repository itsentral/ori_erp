<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		  <select name='status' id='status' class='form-control input-sm' style='min-width:250px;'>
			<option value='0'>ALL STATUS</option>
			<?php
			foreach($status as $row)
			{
				echo "<option value='".$row->status."'>".strtoupper($row->status)."</option>";
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
					<th class="text-center">IPP</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Project</th>
					<!-- <th class="text-center no-sort" width='15%'>Status</th> -->
					<th class="text-center no-sort" style='width:100px !important'>IPP Release</th>
					<th class="text-center no-sort">BQ Release</th>
					<th class="text-center no-sort">App. BQ</th>
					<th class="text-center no-sort">Est. Release</th>
					<th class="text-center no-sort">App. Est</th>
					<th class="text-center no-sort">Est. Price Release</th>
					<th class="text-center no-sort">App Quo</th>
					<th class="text-center no-sort">Quo Release</th>
					<th class="text-center no-sort">SO Number</th>
					<th class="text-center no-sort">SO Release</th>
					<th class="text-center no-sort">App. SO</th>
					<th class="text-center no-sort">FD Release</th>
					<th class="text-center no-sort">App. FD</th>
					<th class="text-center no-sort">SPK Release</th>
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
		<div class="modal-dialog"  style='width:95%; '>
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
		var status = $('#status').val();
		DataTables(status);
		
		$(document).on('change','#status', function(e){
			e.preventDefault();
			var status = $('#status').val();
			DataTables(status);
		});

		$(document).on('click', '.detail', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL ["+$(this).data('no_ipp')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url+active_controller+'/detail/'+$(this).data('no_ipp'),
				success:function(data){
					$("#ModalView").modal();
					$("#view").html(data);
				},
				error: function() {
					swal({
					title	: "Error Message !",
					text	: 'Connection Timed Out ...',
					type	: "warning",
					timer	: 5000,
					});
				}
			});
		});
	});
		
	function DataTables(status=null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"processing": true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
            "scrollX": true,
			// "fixedHeader": {
			// 	"header": true,
			// 	"footer": true
			// },
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_sales_ipp', 
				type: "post",
				data: function(d){
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
	
</script>
