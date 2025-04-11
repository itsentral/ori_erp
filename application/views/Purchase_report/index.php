<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		    <select name='status' id='status' class='form-control input-sm' style='min-width:250px;'>
			<option value='0'>ALL CATEGORY</option>
			<?php
			foreach($status as $row)
			{
				$NAMA = $row->category;
				if($row->category == 'non rutin'){
					$NAMA = 'department';
				}
				if($row->category == 'rutin'){
					$NAMA = 'stok';
				}
				echo "<option value='".$row->category."'>".strtoupper($NAMA)."</option>";
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
					<th class="text-center">No PR</th>
					<th class="text-center">Tanggal</th>
					<th class="text-center">Category</th>
					<th class="text-center">AppBy</th>
					<th class="text-center">AppDate</th>
					<th class="text-center">Detail</th>
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
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->	
</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.detail_pr{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
		var status = $('#status').val();
		DataTables(status);
		
		$(document).on('change','#status', function(e){
			e.preventDefault();
			var status = $('#status').val();
			DataTables(status);
		});
	});
	
	$(document).on('click', '.detail_pr', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL "+$(this).data('no_pr')+"</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modal_detail_pr/'+$(this).data('no_pr')+'/'+$(this).data('category'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 3000
				});
			}
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
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_progress_pr', 
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
