<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Delivery</th>
                    <th class="text-center no-sort">No SPK</th>
                    <th class="text-center no-sort">Product</th>
                    <th class="text-center no-sort">No SO</th>
                    <th class="text-center no-sort">Customer</th>
                    <th class="text-center no-sort">Project</th>
                    <th class="text-center no-sort">Spec</th>
                    <th class="text-center no-sort">Length Cut</th>
                    <th class="text-center no-sort">Qty</th>
					<th class="text-center no-sort">Berat (Kg)</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<!-- modal -->
<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
	<div class="modal-dialog"  style='width:70%; '>
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
		$('.chosen-select').chosen({
			width: '150px'
		})
		DataTables2();

		$(document).on('click', '.look_history', function(e){
            e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL</b>");
            $("#view").load(base_url + 'gudang_wg/detail_berat/'+$(this).data('kode_spk')+'/'+$(this).data('id_production_detail')+'/'+$(this).data('category')+'/'+$(this).data('qty')+'/'+$(this).data('statusx')+'/'+$(this).data('length')+'/'+$(this).data('length_awal'));
            $("#ModalView").modal();
        });

	});

	function DataTables2(status=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_transit',
				type: "post",
				data: function(d){
					d.status = status
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}
</script>
