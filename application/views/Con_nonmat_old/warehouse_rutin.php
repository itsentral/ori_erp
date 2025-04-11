<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		</div><br><br>
		<div class="box-tool pull-left">
			<select id='inventory' name='inventory' class='form-control input-sm chosen-select' style='min-width:150px; float:left; margin-bottom: 5px;'>
				<option value='0'>ALL INVENTORY TYPE</option>
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
					<th class="text-center" width='4%'>No</th>
					<th class="text-center">Nama Barang</th>
					<th class="text-center">Spesifikasi</th>
					<th class="text-center" width='15%'>Inventory Type</th> 
					<th class="text-center" width='12%'>Gudang</th>
					<th class="text-center" width='9%'>Stock Ok</th>
					<th class="text-center" width='9%'>Stock Rusak</th>
					<th class="text-center" width='9%'>Kebutuhan</th>
					<th class="text-center" width='8%'>Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<?php $this->load->view('include/footer'); ?>
<style>
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}

</style>
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

	function DataTables(inventory = null){
		var dataTable = $('#my-grid').DataTable({
			// "scrollX": true,
			"scrollY": "500",
			"scrollCollapse" : true,
			"serverSide": true,
			"processing" : true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"oLanguage": {
				"sSearch": "<b>Search : </b>",
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
				url : base_url + active_controller+'/data_side_warehouse_rutin',
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


</script>
