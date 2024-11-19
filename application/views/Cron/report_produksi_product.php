<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right"><br><br>
            <label>Search : &nbsp;&nbsp;&nbsp;</label>
			<input type="text" name="date_range" id="date_range" class="form-control input-md datepicker" style='margin-bottom:5px;' readonly="readonly" placeholder="Select Date">
			<select id='tanggal' name='tanggal' class='form-control input-sm' style='width:100px;'>
				<option value='0'>All Date</option>
				<?php
				for($a=1; $a <= 31; $a++){
					echo "<option value='".$a."'>".$a."</option>";
				}
				?>
			</select>
			<select id='bulan' name='bulan' class='form-control input-sm' style='width:120px;'>
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
				<?php
				$date = date('Y') + 5;
				for($a=2019; $a < $date; $a++){
					echo "<option value='".$a."'>".$a."</option>";
				}
				?>
			</select>
		</div><br><br>
		<div class="box-tool pull-left">
			<button type='button'class="btn btn-sm btn-success" id='excel_report' style='float:right;'>
				<i class="fa fa-print"></i> Print Excel
			</button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body" style="width:100%;">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th width='30px' rowspan='2' class="text-center">#</th>
					<th width='150px' rowspan='2' class="text-center">Warehouse Produksi</th>
					<th width='300px' rowspan='2' class="text-center">Customer</th>
					<th width='300px' rowspan='2' class="text-center">Project</th>
					<th width='70px' rowspan='2' class="text-center">SO Number</th>
					<th width='70px' rowspan='2' class="text-center">SPK Number</th>
					<th width='70px' rowspan='2' class="text-center">Start QC</th>
					<th width='70px' rowspan='2' class="text-center">Start Date</th>
					<th width='70px' rowspan='2' class="text-center">Finish Date</th>
					<th width='200px' rowspan='2' class="text-center">Product</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Dim 1</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Dim 2</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Length</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Thickness</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Liner</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Qty Order</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Qty Produksi</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Urutan</th>
					<th colspan='2' class="text-center no-sort">VEILS</th>
					<th colspan='2' class="text-center no-sort">CSM</th>
					<th colspan='2' class="text-center no-sort">ROOVING</th>
					<th colspan='2' class="text-center no-sort">WR</th>
					<th colspan='2' class="text-center no-sort">RESIN</th>
					<th colspan='2' class="text-center no-sort">CATALYS</th>
					<th colspan='2' class="text-center no-sort">LAINNYA</th>
					<th colspan='2' class="text-center no-sort">ADD</th>
					<th width='120px' rowspan='2' class="text-center no-sort">Total Material</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Work Hour</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Man Power</th>
					<th width='60px' rowspan='2' class="text-center no-sort">Man Hour</th>
				</tr>
				<tr class='bg-blue'>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
					<th width='300px' class="text-center no-sort">Material</th>
					<th width='60px' class="text-center no-sort">Berat</th>
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
	.datepicker{
		cursor: pointer;
	}
	
	/* th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 800px;
        margin: 0 auto;
    } */
</style>
<script>
	$(document).ready(function(){
		$('.datepicker').daterangepicker({
			showDropdowns: true,
			autoUpdateInput: false,
			locale: {
				cancelLabel: 'Clear'
			}
		});
		
		$('.datepicker').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
			var range = $(this).val();
			var tanggal = $('#tanggal').val();
            var bulan 	= $('#bulan').val();
            var tahun 	= $('#tahun').val();
		    DataTables(tanggal, bulan, tahun, range);
		});

		$('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			var range = $(this).val();
			var tanggal = $('#tanggal').val();
            var bulan 	= $('#bulan').val();
            var tahun 	= $('#tahun').val();
		    DataTables(tanggal, bulan, tahun, range);
		});
  
		var range 	= $('#date_range').val();
		var tanggal = $('#tanggal').val();
        var bulan 	= $('#bulan').val();
        var tahun 	= $('#tahun').val();
		DataTables(tanggal, bulan, tahun, range);
		
		$(document).on('change', '#tanggal', function(e){
			var range 	= $('#date_range').val();
			var tanggal = $('#tanggal').val();
			var bulan 	= $('#bulan').val();
			var tahun 	= $('#tahun').val();
			DataTables(tanggal, bulan, tahun, range);
        });
		
        $(document).on('change', '#bulan', function(e){
			var range 	= $('#date_range').val();
			var tanggal = $('#tanggal').val();
			var bulan 	= $('#bulan').val();
			var tahun 	= $('#tahun').val();
			DataTables(tanggal, bulan, tahun, range);
        });

        $(document).on('change', '#tahun', function(e){
			var range 	= $('#date_range').val();
			var tanggal = $('#tanggal').val();
			var bulan 	= $('#bulan').val();
			var tahun 	= $('#tahun').val();
			DataTables(tanggal, bulan, tahun, range);
        });

	});
	
	$(document).on('click', '#detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('tanggal')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('tanggal'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#excel_report', function(e){
			// loading_spinner();
    var range 	= $('#date_range').val();
	var tanggal = $('#tanggal').val();
	var bulan 	= $('#bulan').val();
	var tahun 	= $('#tahun').val();

	if(range == ''){
		swal({
			title	: "Error Message!",
			text	: 'Filter range wajib diisi ...',
			type	: "warning"
		});
		return false;
	}
	
	var tgl_awal 	= '0';
	var tgl_akhir 	= '0';
	if(range != ''){
		var sPLT 		= range.split(' - ');
		var tgl_awal 	= sPLT[0];
		var tgl_akhir 	= sPLT[1];
	}
	

	var Link	= base_url + active_controller +'/excel_report/'+tanggal+'/'+bulan+'/'+tahun+'/'+tgl_awal+'/'+tgl_akhir;
		window.open(Link);
	});
		
	function DataTables(tanggal = null, bulan = null, tahun = null, range = null){
		var dataTable = $('#my-grid').DataTable({ 
            "scrollX": true,
			"scrollY": "700",
			"scrollCollapse" : true,
			"serverSide": true,
			"processing": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 2, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/get_data_side_report_product', 
				type: "post",
				data: function(d){
					d.tanggal = $('#tanggal').val(),
					d.bulan = $('#bulan').val(),
                    d.tahun = $('#tahun').val(),
					d.range = range
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
