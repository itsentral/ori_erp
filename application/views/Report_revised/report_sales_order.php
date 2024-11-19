<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right"><br><br>
            <!-- <label>Search : </label>
			<select id='bulan' name='bulan' class='form-control input-sm' style=width:120px;'>
				<option value='0'>All Month</option>
                <option value='1'>January</option>
                <option value='2'>February</option>
                <option value='3'>March</option>
                <option value='4'>April</option>
                <option value='5'>May</option>
                <option value='6'>June</option>
                <option value='7'>July</option>
                <option value='8'>August</option>
                <option value='9'>September</option>
                <option value='10'>October</option>
                <option value='11'>November</option>
                <option value='12'>December</option>
			</select>
			<select id='tahun' name='tahun' class='form-control input-sm' style='width:100px;'>
				<option value='0'>All Year</option>
                <option value='2019'>2019</option>
                <option value='2020'>2020</option>
			</select> -->
		</div><br><br>
		<div class="box-tool pull-left">
			<!-- <a href="<?php echo site_url('cron/excel_project') ?>" target='_blank' class="btn btn-sm btn-success" id='btn-add' style='float:right;'>
				<i class="fa fa-print"></i> Print Excel
			</a> -->
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body" style="width:100%;">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>No</th>
					<th class="text-center" width='10%'>IPP</th>
					<th class="text-center" width='30%'>Customer Name</th>
					<th class="text-center">Project Name</th>
					<th class="text-center no-sort" width='10%'>Option</th>
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
<script>
	$(document).ready(function(){
		DataTables();
	});
	
	$(document).on('click', '.detail_so', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('id_bq')+"]</b>"); 
		$("#view").load(base_url + active_controller+'/modalDetailSO/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
            // "scrollX": true,
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
				url : base_url +active_controller+'/getDataJSONSO', 
				type: "post",
				// data: function(d){
				// 	d.bulan = $('#bulan').val(),
                //     d.tahun = $('#tahun').val()
				// },
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}

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
				url : base_url +active_controller+'/getDataJSONDetailSO', 
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
