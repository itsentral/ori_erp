<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-right">
			<select name='costcenter' id='costcenter' class='form-control input-sm'>
				<option value='0'>ALL COSTCENTER</option>
				<?php
				foreach($coctcenter AS $val => $valx){
					echo "<option value='".$valx['id']."'>".strtoupper($valx['name'])."</option>";
				}
				?>
			</select>
			<input type="text" name="date_range" id="date_range" class="form-control input-md datepicker" style='margin-top:5px; width:500px;' readonly="readonly" placeholder="Select Date">
			
		</div>
		<div class="box-tool pull-left">
			<select name='no_ipp' id='no_ipp' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>ALL IPP</option>
				<?php
				foreach($list_ipp AS $val => $valx){
					echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp']." / ".$valx['project'])."</option>";
				}
				?>
			</select><br>
			<button type='button' id='print' class='btn btn-sm btn-primary'  style='margin-top:5px;'>Print Order Produksi</button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-center">Must Finish</th>
					<th class="text-center">IPP</th>
					<th class="text-center">Product</th>
					<th class="text-center">Dimensi</th>
					<th class="text-center">Qty</th>
					<th class="text-center">Id Spool</th>
					<th class="text-center">Costcenter</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.datepicker{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
		var costcenter 	= $('#costcenter').val();
		var range 	= $('#date_range').val();
		var no_ipp 	= $('#no_ipp').val();
		DataTables(costcenter, range, no_ipp);
		
		$('.datepicker').daterangepicker({
			showDropdowns: true,
			autoUpdateInput: false,
			locale: {
				cancelLabel: 'Clear'
			}
		});
		
		$('.datepicker').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
			var costcenter 	= $('#costcenter').val();
			var range 	= $('#date_range').val();
			var no_ipp 	= $('#no_ipp').val();
			DataTables(costcenter, range, no_ipp);
		});

		$('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			var costcenter 	= $('#costcenter').val();
			var range 	= $('#date_range').val();
			var no_ipp 	= $('#no_ipp').val();
			DataTables(costcenter, range, no_ipp);
		});
		
		$(document).on('change', '#costcenter, #no_ipp', function(e){
			var costcenter 	= $('#costcenter').val();
			var range 	= $('#date_range').val();
			var no_ipp 	= $('#no_ipp').val();
			DataTables(costcenter, range, no_ipp);
        });
		
		$(document).on('click', '#print', function(e){
			  e.preventDefault();
			  var costcenter	= $('#costcenter').val();
			  var no_ipp	= $('#no_ipp').val();
			  // console.log(proX);
			  // return false;

			  var range		    = $('#date_range').val();
			  if(costcenter == '0' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Costcenter is empty, select first ...',
				  type	: "warning"
				});
				return false;
			  }
			  if(no_ipp == '0' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Project is empty, select first ...',
				  type	: "warning"
				});
				return false;
			  }

			  if(range == '' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Date is empty, select first ...',
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

			  var Link	= base_url + active_controller +'/print_order_produksi/'+costcenter+'/'+tgl_awal+'/'+tgl_akhir+'/'+no_ipp;
				  window.open(Link);

		});
	});
		
	function DataTables(costcenter=null, range=null, no_ipp=null){
		var dataTable = $('#my-grid').DataTable({
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
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
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
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_order_produksi',
				type: "post",
				data: function(d){
					d.costcenter = costcenter,
					d.range = range,
					d.no_ipp = no_ipp
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
