<?php
$this->load->view('include/side_menu');
// echo $tanda;
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($tanda <> 'approval'){
				if($akses_menu['create']=='1'){
					?>
					<a href="<?php echo site_url('non_rutin/add') ?>" class="btn btn-sm btn-success" style='float:right;' id='btn-add'>
						<i class="fa fa-plus"></i> &nbsp;&nbsp;Add
					</a>
					<?php
				}
			}
		?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body table-responsive">
		<input type='hidden' id='tanda' value='<?=$tanda;?>'>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">No PR</th> 
					<th class="text-center">Departemen</th> 
					<th class="text-center no-sort">Nama Barang/Jasa</th>
					<!-- <th class="text-center no-sort">Spec / Requirement</th> -->
					<!-- <th class="text-center no-sort" width='7%'>Qty</th> -->
					<!-- <th class="text-center no-sort">Dibutuhkan</th>
					<th class="text-center no-sort">Keterangan</th> -->
					<th class="text-center">Req. By</th>
					<th class="text-center no-sort">Req. Date</th> 
					<th class="text-center no-sort">Status</th> 
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
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
			
		var tanda = $('#tanda').val();
		DataTables(tanda);
	});

	function DataTables(tanda = null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
				url : base_url + active_controller+'/server_side_non_rutin',
				type: "post",
				data: function(d){
					d.tanda = tanda
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
