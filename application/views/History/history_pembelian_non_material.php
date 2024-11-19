<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['download']=='1'){
		?>
		  <a href="<?php echo site_url('history_pembelian/excel_non_material') ?>" class="btn btn-sm btn-success" style='float:right;' target='_blank'>
			<i class="fa fa-file-excel-o"></i> &nbsp;&nbsp;Download
		  </a>
		  <?php
			}
		  ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">Tipe Pembelian</th>
						<th class="text-center th">No PR</th>
						<th class="text-center th">No PO</th>
						<th class="text-center th">Tgl PO</th>
						<th class="text-center th">Supplier</th>
						<th class="text-center th">Detail</th>
						<th class="text-center th">Total PO</th>
						<th class="text-center th">Tgl Permintaan</th>
						<th class="text-center th">Aktual Kedatangan</th>    
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
<?php $this->load->view('include/footer'); ?>
<!-- <style>
.tableFixHead {
  overflow: auto;
  height: 100px;
}

.tableFixHead .thead .th {
  	position: sticky;
  	top: 0;
	background: #3c8dbc;
}

</style> -->
<script>
	$(document).ready(function(){
		DataTables();

		$(document).on('click', '.detail_material', function(){
			var no_po 	= $(this).data('no_po');
			$("#head_title2").html("<b>DETAIL BARANG</b>");
			loading_spinner();
			$.ajax({
				type:'POST',
				url:base_url+active_controller+'/modal_detail/'+no_po,
				success:function(data){
					$("#ModalView2").modal();
					$("#view2").html(data);
				},
				error: function() {
					swal({
					title	: "Error Message !",
					text	: 'Connection Timed Out ...',
					type	: "warning",
					timer	: 5000,
					});
				}
			})
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
