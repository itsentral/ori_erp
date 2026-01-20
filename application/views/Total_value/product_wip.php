<?php
$this->load->view('include/side_menu');
$gudang = $this->uri->segment(3);
// print_r($gudang);
// exit;
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br>
		<input type='hidden' id='gudang1' value='<?='wip';?>'>
		<div class='form-group row'>
		<div class='col-sm-2'>
			<input type="hidden" id='category' value='<?=$category;?>'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
			<!-- <div class='col-sm-1'>
				<button type='button' class='btn btn-md btn-success' id='search'>Search</button>
			</div> -->
		</div>
		
		<?php if($gudang=='wip') {?>
		<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download WIP</button>
		<?php } elseif($gudang=='fg') {?>		
		<button type='button' class='btn btn-sm btn-success' id='download_excel2'><i class='fa fa-file-excel-o'></i> Download FG</button>
		<?php } elseif($gudang=='intransit') {?>
			<button type='button' class='btn btn-sm btn-success' id='download_excel3'><i class='fa fa-file-excel-o'></i> Download Intransit</button>
		<?php } elseif($gudang=='incustomer') {?>
			<button type='button' class='btn btn-sm btn-success' id='download_excel4'><i class='fa fa-file-excel-o'></i> Download Incustomer</button>
		<?php } ?>
	</div>
	<!-- /.box-header -->
	<div class="box-body table-responsive">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Nomor SO</th>
					<th class="text-center">Nomor SPK</th>
					<th class="text-center">Produk</th> 
					<th class="text-center">Keterangan</th> 
					<th class="text-center">Stock</th>
                    <th class="text-center">Nilai per unit</th>
					<th class="text-center">Total Value</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
			<!-- <tfoot>
				<tr>
					<th colspan="5" style="text-align:center">SUM</th>
					<th></th>
					<?php if($gudang != 'produksi'){?>
					<th></th>
					<th></th>
					<?php if($gudang == 'pusat'){?>
					<th></th>
					<?php }} ?>
					<th></th>
				</tr>
			</tfoot> -->
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
				DataTables(gudang, date_filter,category);
			}
		}
	});

	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		var gudang = $('#gudang').val();
		var date_filter = $('#date_filter').val();
		var Links		= base_url + active_controller+'/ExcelGudang/'+gudang+'/'+date_filter;
		window.open(Links,'_blank');
	});
	
	$(document).on('click', '#download_excel2', function(e){
		e.preventDefault();
		var gudang = $('#gudang').val();
		var date_filter = $('#date_filter').val();
		var Links		= base_url + active_controller+'/ExcelGudangSubgudang/'+gudang+'/'+date_filter;
		window.open(Links,'_blank');
	});
	
	$(document).on('click', '#download_excel3', function(e){
		e.preventDefault();
		var gudang = $('#gudang').val();
		var date_filter = $('#date_filter').val();
		var category = $('#category').val();
		var Links		= base_url + active_controller+'/ExcelGudangProduksi/'+gudang+'/'+category+'/'+date_filter;
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
			$("#head_title2").html("<b>History "+$(this).data('no_so')+'/'+$(this).data('no_spk')+'/'+$(this).data('kode_trans')+'/'+$(this).data('product')+"</b>");
            $("#view2").load(base_url + active_controller + '/modal_history/'+$(this).data('no_so')+'/'+$(this).data('no_spk')+'/'+$(this).data('product')+'/'+$(this).data('kode_trans')+'/'+<?=(isset($akses_menu)?$akses_menu['approve']:'0')?>');
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
		let gudang1 = $('#gudang1').val();
		if(gudang1 =='wip'){
			var link =  base_url + active_controller+'/server_side_product_stock_wip';
		}else if(gudang1 =='fg'){
			var link =  base_url + active_controller+'/server_side_product_stock_fg';
		}else if(gudang1 =='intransit'){
			var link =  base_url + active_controller+'/server_side_product_stock_intransit';
		}else{
			var link =  base_url + active_controller+'/server_side_product_stock_incustomer';
		}
		
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
				url :link,
				type: "post",
				data: function(d){
					d.gudang = gudang1,
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
				    return data.data;
				 }
			},
			// drawCallback: function( settings ) {
			// 	var api = this.api();
			// 	$( api.column( 5 ).footer() ).html("<div align='right'>"+ number_format(qty_stock,4) +"</div>");
			// 	if(category != 'produksi'){
			// 	$( api.column( 6 ).footer() ).html("<div align='right'>"+ number_format(qty_booking,4) +"</div>");
			// 	$( api.column( 7 ).footer() ).html("<div align='right'>"+ number_format(qty_available,4) +"</div>");
			// 	if(category == 'pusat'){
			// 	$( api.column( 8 ).footer() ).html("<div align='right'>"+ number_format(qty_rusak,4) +"</div>");
			// 	}
			// 	}
			// }
		});
	}

	
	
</script>
