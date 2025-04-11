<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
        <div class='form-group row'>
			<div class='col-sm-2'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
			<div class='col-sm-8'>
				<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
			</div>
			<div class='col-sm-2 text-right'></div>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">ID</th>
						<th class="text-center th">Category</th>
						<th class="text-center th">No Barang</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">Type</th>
						<th class="text-center th">Spec</th>
						<th class="text-center th">Resin</th>
						<th class="text-center th">Length</th>
						<th class="text-center th">Qty</th>
						<th class="text-center th">Price Book</th>
						<th class="text-center th">Total Price</th>
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
		<div class="modal-dialog"  style='width:80%; '>
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
<style>
	#date_filter{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
		var date_filter = $('#date_filter').val();
		DataTables(date_filter);
	});

	$(document).on('change','#date_filter', function(e){
		e.preventDefault();
		var date_filter = $('#date_filter').val();
		DataTables(date_filter);
	});

	$('input[type="text"][data-role="datepicker2"]').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth:true,
		changeYear:true,
		maxDate:'-1d',
		showButtonPanel: true,
		closeText: 'Clear',
			onClose: function (dateText, inst) {
			if ($(window.event.srcElement).hasClass('ui-datepicker-close'))
			{
				document.getElementById(this.id).value = '';
				var date_filter 	= $('#date_filter').val();
				DataTables(date_filter);
			}
		}
	});

	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		var date_filter 	= $('#date_filter').val();
		var date_filter_ 	= 0;
		if(date_filter != ''){
			var date_filter_ 	= $('#date_filter').val();
		}
		var Links		= base_url + active_controller+'/download_excel/'+date_filter;
		window.open(Links,'_blank');
	});
		
	function DataTables(date_filter=null){
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
					d.date_filter = date_filter
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
