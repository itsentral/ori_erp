<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<select id='cust' name='cust' class='form-control input-sm' style='max-width:400px;'>
				<option value='0'>All Customer</option>
				<?php
					foreach($cust AS $val => $valx){
						echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
					}
				?>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class="table-responsive"> -->
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='4%'>#</th>
					<th class="text-center" width='9%'>IPP</th>
					<th class="text-center" width='22%'>Customer</th>
					<th class="text-center" >Project</th>
                    <th class="text-center no-sort" width='8%'>Estimation</th>
                    <th class="text-center no-sort" width='8%'>Costing</th>
                    <th class="text-center no-sort" width='8%'>Quotation</th>
                    <th class="text-center no-sort" width='8%'>Sales Order</th>
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

	<!-- modal -->
	<div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:95%; '>
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
<style>
.font-cs{
    font-size : 18px;
    cursor : pointer;
}
</style>
<script>
	$(document).ready(function(){
		var cust = $('#cust').val();
		DataTables(cust);
	});

	$(document).on('change','#cust', function(){
		var cust = $('#cust').val();
		DataTables(cust);
	});

	//ENGINNERING
    $(document).on('click', '.detail_est', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('id_bq')+"]</b>"); 
		$("#view").load(base_url +'report_revised/modalDetail_costing/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});

	$(document).on('click', '.detail_eng', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ["+$(this).data('id_bq')+" Rev: "+$(this).data('rev')+"]</b>"); 
		$("#view2").load(base_url +'report_revised/modalDetail2_costing/'+$(this).data('id_bq')+'/'+$(this).data('rev'));
		$("#ModalView2").modal();
	});

	//COSTING
	$(document).on('click', '.detail_cos', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('id_bq')+"]</b>"); 
		$("#view").load(base_url +'report_revised/modalDetail/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});

	$(document).on('click', '.detail_costing', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ["+$(this).data('id_bq')+" Rev: "+$(this).data('rev')+"]</b>"); 
		$("#view2").load(base_url +'report_revised/modalDetail2/'+$(this).data('id_bq')+'/'+$(this).data('rev'));
		$("#ModalView2").modal();
	});

	//QUOTATION
	$(document).on('click', '.detail_quo', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('id_bq')+"]</b>"); 
		$("#view").load(base_url +'report_revised/modal_detail_quotation/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});

	$(document).on('click', '.detail_quotation', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ["+$(this).data('id_bq')+" Rev: "+$(this).data('rev')+"]</b>"); 
		$("#view2").load(base_url +'report_revised/modal_detail_quotation_detail/'+$(this).data('id_bq')+'/'+$(this).data('rev'));
		$("#ModalView2").modal();
	});

	//SALES ORDER
	$(document).on('click', '.detail_so', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('id_bq')+"]</b>"); 
		$("#view").load(base_url +'report_revised/modalDetailSO/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});


	//datatable utama	
	function DataTables(cust=null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": false,
			"processing": true,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 1, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"ajax":{
				url : base_url + active_controller+'/server_side_history_revisi',
				type: "post",
				data: function(d){
					d.cust = cust
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

	//enginnering
    function DataTables_engine(id_bq=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"processing": true,
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
				url : base_url +'report_revised/getDataJSONDetail_costing', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val()
                    // d.tahun = $('#tahun').val()
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
	//engginering detail
	function DataTables_engine_detail(id_bq=null, rev=null){
		var dataTable = $('#my-grid3').DataTable({
            "serverSide": true,
			"processing": true,
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
				url : base_url +'report_revised/getDataJSONDetail2_costing', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val(),
					d.rev = $('#rev').val()
                    // d.tahun = $('#tahun').val()
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

	//costing
	function DataTables_costing(id_bq=null){
		var dataTable = $('#my-grid2').DataTable({
            "scrollX": true,
			"serverSide": true,
			"processing": true,
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
				url : base_url + 'report_revised/getDataJSONDetail', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val()
                    // d.tahun = $('#tahun').val()
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
	//costing detail
	function DataTables_costing_detail(id_bq=null, rev=null){
		var dataTable = $('#my-grid3').DataTable({
            "scrollX": true,
			"serverSide": true,
			"processing": true,
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
				url : base_url +'report_revised/getDataJSONDetail2', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val(),
					d.rev = $('#rev').val()
                    // d.tahun = $('#tahun').val()
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

	//quotation
	function DataTables_quotation(id_bq=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"processing": true,
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
				url : base_url +'report_revised/getDataJSONDetail_quo', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val()
                    // d.tahun = $('#tahun').val()
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
	//quotation_detail
	function DataTables_quotation_detail(id_bq=null, rev=null){
		var dataTable = $('#my-grid3').DataTable({
            "serverSide": true,
			"processing": true,
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
				url : base_url +'report_revised/getDataJSONDetail2_quo', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val(),
					d.rev = $('#rev').val()
                    // d.tahun = $('#tahun').val()
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
	
	//sales order
	function DataTables_so(id_bq=null){
		var dataTable = $('#my-grid3').DataTable({
            "scrollX": true,
			"serverSide": true,
			"processing": true,
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
				url : base_url +'report_revised/getDataJSONDetailSO', 
				type: "post",
				data: function(d){
					d.id_bq = $('#id_bq').val()
                    // d.tahun = $('#tahun').val()
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
