<?php
$this->load->view('include/side_menu');
$gudang = $this->uri->segment(3);
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class='form-group row'>
			<div class='col-sm-7 text-right'><b>Search:</b></div>
			<div class='col-sm-3'>
				<select id='gudang' name='gudang' class='form-control input-sm' style='min-width:200px;'>
					<option value='0'>All Category</option>
					<?php
						foreach($data_gudang AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['category'])."</option>";
						}
					?>
				</select>
			</div>
			<div class='col-sm-2'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
		</div>
		<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Code Program</th>
					<th class="text-center">Code Item</th>
					<th class="text-center">Code Excel</th>
					<th class="text-center">Nama Barang</th>
					<th class="text-center">Spesifikasi</th>
					<th class="text-center">Category</th>
					<th class="text-center">Gudang</th>
					<th class="text-center">Stock</th>
					<th class="text-center">Stock NG</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="8" style="text-align:center">SUM</th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
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
<style>
	<style>
	.datepicker{
		cursor:pointer;
	}
</style>
</style>
<script>
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
				var gudang = $('#gudang').val();
				var date_filter = $('#date_filter').val();
				DataTables(gudang, date_filter);
			}
		}
	});

	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		var gudang = $('#gudang').val();
		var date_filter = $('#date_filter').val();
		var Links		= base_url + active_controller+'/ExcelGudangStok/'+gudang+'/'+date_filter;
		window.open(Links,'_blank');
	});

	$(document).ready(function(){
        var gudang = $('#gudang').val();
        var date_filter = $('#date_filter').val();
        DataTables(gudang, date_filter);
        
        $(document).on('change','#gudang, #date_filter', function(e){
			e.preventDefault();
			var gudang = $('#gudang').val();
			var date_filter = $('#date_filter').val();
        	DataTables(gudang, date_filter);
		});

		$(document).on('click','#search', function(e){
			e.preventDefault();
			var gudang = $('#gudang').val();
			var date_filter = $('#date_filter').val();
        	DataTables(gudang, date_filter);
		});
    });
    
	function DataTables(gudang=null, date_filter=null){
		let stock	= 0;
		let rusak	= 0;
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_stock',
				type: "post",
				data: function(d){
					d.gudang = gudang,
					d.date_filter = date_filter
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				},
				 dataSrc: function ( data ) {
				   stock = data.recordsStock;
				   rusak = data.recordsRusak;
				   return data.data;
				 }
			},
			drawCallback: function( settings ) {
				var api = this.api();
				$( api.column( 8 ).footer() ).html("<div align='right'>"+ number_format(stock) +"</div>");
				$( api.column( 9 ).footer() ).html("<div align='right'>"+ number_format(rusak) +"</div>");
			}
		});
	}

	
</script>
