<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<a href="<?php echo site_url('price/PrintHasilProject') ?>" target='_blank' class="btn btn-sm btn-success" id='btn-add' style='float:right;'>
				<i class="fa fa-print"></i> Print Summary Project
			</a>
		</div><br><br>
		<div class="box-tool pull-right">
			<label>Search : </label>
			<select id='status' name='status' class='form-control input-sm' style='min-width:200px;'>
				<option value=''>All Status</option>
				<option value='FINISH'>FINISH</option>
				<option value='OVER BUDGET'>OVER BUDGET</option>
			</select>
		</div>
		
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead> 
				<tr class='bg-blue'>  
					<th class="text-center">No</th>
					<th class="text-center">IPP</th>  
					<th class="text-center">Customer</th>
					<th class="text-center">Project</th>
					<th class="text-center">Type</th>
					<th class="text-center no-sort">Series</th>
					<th class="text-right no-sort">Material<br>(Est)</th>
					<th class="text-right no-sort">Cost<br>(Est)</th>
					<!-- <th class="text-center no-sort">Process Cost</th> -->
					<th class="text-right no-sort">Material<br>(Real)</th>
					<th class="text-right no-sort">Cost<br>(Real)</th>
					<th class="text-center no-sort">Rev</th>
					<!-- <th width='10%' class="text-center no-sort">Status</th> -->
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
		<div class="modal-dialog"  style='width:90%;'  style='overflow-y: auto;'>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:80%;'  style='overflow-y: auto;'>
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
	
	<div class="modal fade" id="ModalView3">
		<div class="modal-dialog"  style='width:30%;'  style='overflow-y: auto;'> 
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title3"></h4>
					</div>
					<div class="modal-body" id="view3">
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
<style>
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
</style>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script>
	$(document).ready(function(){
		var status = $('#status').val();
		DataTables(status);
	});
	
	$(document).on('change','#status', function(e){
		e.preventDefault();
		var status = $('#status').val();
		DataTables(status);
	});
	
	$(document).on('click', '#detail_process_cost', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>DETAIL COST PROCESS BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view3").load(base_url + active_controller+'/modalDetailProcess/'+$(this).data('id_bq'));
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#detailBQ', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalDetailBQ/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#viewDT', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL DATA BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalviewDT/'+$(this).data('id_bq')+'/'+$(this).data('cost_control'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#TotalCost', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>TOTAL METRIAL PROJECT ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalTotalCost/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#ApproveDT', function(e){
		e.preventDefault();
		$("#head_title").html("<b>APPROVE PROJECT PRICE ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalAppCost/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#detailPlant', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL PRODUCTION ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalDetailPrice/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
		
	function DataTables(status = null){
		// loading_spinner();
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
				url : base_url + active_controller+'/getDataJSON/cost_control',
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
