<?php
$this->load->view('include/side_menu');
$ArrList = array();
foreach($ListIPP AS $val => $valx){
	$ArrList[$valx['no_ipp']] = $valx['no_ipp'];
}
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3><br><br>
			<div class="box-tool pull-right">
			<select name='no_sox' id='no_sox' class='form-control input-sm' style='width:150px; float:right;'>
				<option value='0'>ALL NO SO</option>
				<?php
				foreach($list_so AS $val => $valx){
					echo "<option value='".$valx['so_number']."'>".strtoupper($valx['so_number'])."</option>";
				}
				?>
			</select>
				
			<select name='customer' id='customer' class='form-control input-sm' style='width:300px; float:right;'>
				<option value='0'>ALL CUSTOMER</option>
				<?php
				foreach($list_cust AS $val => $valx){
					echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
				}
				?>
			</select>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center" width='3%'>No</th>
						<th class="text-center" width='6%'>No SO</th>
						<th class="text-center" width='6%'>No PO</th>
						<th class="text-center" width='18%'>Project</th>
						<th class="text-center" width='18%'>Customer</th>
						<th class="text-center" width='15%'>Keterangan</th>
						<th class="text-center" width='9%'>Plan Tagih</th>
						<th class="text-center" width='9%'>Plan Tagih USD</th>
						<th class="text-center" width='9%'>Plan Tagih IDR</th>
						<!--<th class="text-center" width='9%'>Weight</th>
						<th class="text-center" width='9%'>Nilai SO</th>-->
						<th class="text-center no-sort" width='8%'>Status</th>
						<th class="text-center no-sort" width='11%'>Option</th>
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
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		
		var no_so = $('#no_sox').val();
		var customer = $('#customer').val();
		DataTables(no_so, customer);
		
		$(document).on('change','#no_sox, #customer', function(e){
			e.preventDefault();
			var no_so = $('#no_sox').val();
			var customer = $('#customer').val();
			DataTables(no_so, customer);
		});
	});
	
	$(document).on('click', '.create_invoice', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>CREATE INVOICE ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/create_invoice/'+$(this).data('id_bq'),
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
	
		
	function DataTables(no_so=null, customer=null){
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
			"aaSorting": [[ 6, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_create_new', 
				type: "post",
				data: function(d){
					d.no_so = no_so,
					d.customer = customer
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
