<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th no-sort">Category</th>
						<th class="text-center th no-sort">Keterangan</th>
						<th class="text-center th no-sort">DEBIT</th>
						<th class="text-center th no-sort">KREDIT</th>
						<th class="text-center th no-sort">Created By</th>
						<th class="text-center th no-sort">Created Date</th>
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
		<div class="modal-dialog"  style='width:100%; '>
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
	
	
	<div class="modal modal-default fade" id="dialog-popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:hidden;">
	  <div class="modal-dialog modal-lg" style='width:80%;'>
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-users"></span>&nbsp;Detail Jurnal</h4>
		  </div>
		  <div class="modal-body" id="ModalView">
			...
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">
			<span class="glyphicon glyphicon-remove"></span>  Close</button>
			 
		 </div>
	    </div>
	  </div>
	
	</div>

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
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url +'index.php/'+active_controller+'/getDataJSON',
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
	
	
	$(document).on('click', '.view2', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'pononstok';
		var akses = 'approval_jurnal_po_nonstok';
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_jv/'+id+'/'+pp+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
				
			}
		})
	});

	
</script>
