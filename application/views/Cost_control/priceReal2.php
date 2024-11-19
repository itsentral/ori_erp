<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>IPP Number</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['no_ipp']);
						echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_produksi']);
					?>				
				</div>
				<label class='label-control col-sm-2'><b>Machine</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Machine Name', 'readonly'=>'readonly'), $row[0]['nm_mesin']);
					?>
				</div>
			</div>
			<br><br>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width="5%">#</th>
						<th class="text-center" width="15%">Product Delivery</th>
						<th class="text-center" width="20%">Product Type</th>
						<th class="text-center" width="35%">Product Name</th>
						<th class="text-center no-sort" width="10%">Product To</th>
						<th class="text-center no-sort" width="15%">Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class='box-footer'>
			<a href="<?php echo site_url('cost_control/on_progress') ?>" class="btn btn-md btn-danger" style='float:right; width: 100px; margin-bottom: 5px;'>Back</a>
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
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<style type="text/css">
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
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		DataTables();

		$(document).on('click', '.btn_download', function(e){
			e.preventDefault();
			var id_produksi		= $(this).data('id_produksi');
			var id_milik		= $(this).data('id_milik');
			var qty_awal		= $(this).data('qty_awal');
			var qty_akhir		= $(this).data('qty_akhir');
			var qty				= $(this).data('qty');
			var nm_product		= $(this).data('nm_product');
			
			var Links		= base_url + active_controller+'/ExcelPerbandingan/'+id_produksi+'/'+id_milik+'/'+qty_awal+'/'+qty_akhir+'/'+qty+'/'+nm_product;
			window.open(Links,'_blank');
		});
		
		$('#real_start_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$('#real_end_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$(document).on('click', '#MatDetail', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>HASIL PRODUKSI ["+$(this).data('id_product')+"]</b>");
			
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modalDetailPriceDetail/'+$(this).data('id_milik')+'/'+$(this).data('id_produksi')+'/'+$(this).data('qty_awal')+'/'+$(this).data('qty_akhir')+'/'+$(this).data('qty')+'/'+$(this).data('id_product'),
				success:function(data){
					$("#ModalView").modal();
					$("#view").html(data);

				},
				error: function() {
					swal({
					  title				: "Error Message !",
					  text				: 'Connection Timed Out ...',
					  type				: "warning",
					  timer				: 5000,
					  showCancelButton	: false,
					  showConfirmButton	: false,
					  allowOutsideClick	: false
					});
				}
			});
			
		});
	});

	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150, 250, 500, 1000], [10, 20, 50, 100, 150, 250, 500, 1000]],
			"ajax":{
				url : base_url +'index.php/'+active_controller+'/getDataJSONUP2',
				type: "post",
				data: function(d){
				d.id_produksi = $('#id_produksi').val()
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
