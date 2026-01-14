<?php
$this->load->view('include/side_menu');
$gudang = $this->uri->segment(3);
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br>
		<!--<input type='hidden' id='gudang' value='<?=$gudang;?>'>-->
		<div class='form-group row'>
			<div class='col-sm-7 text-right'><b>Search:</b></div>
			<div class='col-sm-3'>
				<!-- <label>Warehouse : </label> -->
				<select id='gudang' name='gudang' class='form-control input-sm'>
					<!--<option value='0'>All Warehouse</option>-->
					<?php
						if($category == 'produksi'){
							echo "<option value='0'>All Gudang Produksi</option>";
						}
						foreach($data_gudang AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
					?>
				</select>
			</div>
			<div class='col-sm-2'>
					<input type="hidden" id='category' value='<?=$category;?>'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
			<!-- <div class='col-sm-1'>
				<button type='button' class='btn btn-md btn-success' id='search'>Search</button>
			</div> -->
		</div>
		<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
		
	</div>
	<!-- /.box-header -->
	<div class="box-body table-responsive">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Kode</th>
					<th class="text-center">Id Material</th>
					<th class="text-center">Material</th>
					<th class="text-center">Category</th>
					<th class="text-center">Warehouse</th>
					<th class="text-center">Stock</th>
					<?php if($gudang != 'produksi'){?>
					<th class="text-center no-sort">Booking</th>
					<th class="text-center no-sort">Available</th>
					<?php if($gudang == 'pusat'){?>
					<th class="text-center no-sort">Damaged</th>
					<!-- <th class="text-center no-sort">Cost Book</th>
					<th class="text-center no-sort">Total</th> -->
					<?php }} ?>
					<th class="text-center">#</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="6" style="text-align:center">SUM</th>
					<th></th>
					<?php if($gudang != 'produksi'){?>
					<th></th>
					<th></th>
					<?php if($gudang == 'pusat'){?>
					<th></th>
					<?php }} ?>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  	
	<!-- modal -->
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
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
	.datepicker{
		cursor:pointer;
	}
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
				var category = $('#category').val();
				DataTables(gudang, date_filter, category);
			}
		}
	});

	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		var gudang = $('#gudang').val();
		var date_filter = $('#date_filter').val();
		var category = $('#category').val();

		var Links		= base_url + active_controller+'/ExcelGudang/'+gudang+'/'+category+'/'+date_filter;
		window.open(Links,'_blank');
	});

	$(document).ready(function(){
        var gudang = $('#gudang').val();
        var date_filter = $('#date_filter').val();
		var category = $('#category').val();

        DataTables(gudang, date_filter, category);
        
        $(document).on('change','#gudang, #date_filter', function(e){
			e.preventDefault();
			var gudang = $('#gudang').val();
			var date_filter = $('#date_filter').val();
			var category = $('#category').val();

        	DataTables(gudang, date_filter, category);
		});

		$(document).on('click','#search', function(e){
			e.preventDefault();
			var gudang = $('#gudang').val();
			var date_filter = $('#date_filter').val();
			var category = $('#category').val();

        	DataTables(gudang, date_filter, category);
		});

		$(document).on('click', '.look_history', function(e){
            e.preventDefault();
            loading_spinner();
			$("#head_title2").html("<b>History "+$(this).data('nm_material')+"</b>");
            $("#view2").load(base_url + active_controller + '/modal_history/'+$(this).data('id_material')+'/'+$(this).data('id_gudang')+'/<?=(isset($akses_menu)?$akses_menu['approve']:'0')?>');
            $("#ModalView2").modal();
        });

		$(document).on('click', '.look_history_tras', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>History Tras "+$(this).data('nm_material')+"</b>");
            $("#view2").load(base_url + active_controller + '/modal_history_tras/'+$(this).data('id_material')+'/'+$(this).data('id_gudang')+'/<?=(isset($akses_menu)?$akses_menu['approve']:'0')?>');
            $("#ModalView2").modal();
        });

		$(document).on('click', '.lot_history', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>LOT "+$(this).data('nm_material')+"</b>");
            $("#view2").load(base_url + active_controller + '/modal_history_lot/'+$(this).data('id_material')+'/'+$(this).data('id_gudang')+'/<?=(isset($akses_menu)?$akses_menu['approve']:'0')?>');
            $("#ModalView2").modal();
        });

		$(document).on('click', '.detailBooking', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>Detail Booking "+$(this).data('nm_material')+"</b>");
            $("#view2").load(base_url + active_controller + '/modal_history_booking/'+$(this).data('id_material')+'/'+$(this).data('id_gudang'));
            $("#ModalView2").modal();
        });
    });
    

		
	function DataTables(gudang=null, date_filter=null, category=null){
		let qty_stock	= 0;
		let qty_booking	= 0;
		let qty_available	= 0;
		let qty_rusak	= 0;
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 2, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_product_stock',
				type: "post",
				data: function(d){
					d.gudang = gudang,
					d.date_filter = date_filter,
					d.category = category
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				},
				 dataSrc: function ( data ) {
				   qty_stock = data.recordsStock;
				   qty_booking = data.recordsBooking;
				   qty_rusak = data.recordsRusak;
				   qty_available = data.recordsStock - data.recordsBooking;
				   category = data.category;
				   return data.data;
				 }
			},
			drawCallback: function( settings ) {
				var api = this.api();
				$( api.column( 6 ).footer() ).html("<div align='right'>"+ number_format(qty_stock,4) +"</div>");
				if(category != 'produksi'){
				$( api.column( 7 ).footer() ).html("<div align='right'>"+ number_format(qty_booking,4) +"</div>");
				$( api.column( 8 ).footer() ).html("<div align='right'>"+ number_format(qty_available,4) +"</div>");
				if(category == 'pusat'){
				$( api.column( 9 ).footer() ).html("<div align='right'>"+ number_format(qty_rusak,4) +"</div>");
				}
				}
			}
		});
	}

	
</script>
