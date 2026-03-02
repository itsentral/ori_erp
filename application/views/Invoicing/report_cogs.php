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
			<select name='no_so' id='no_so' class='form-control input-sm' style='width:150px; float:right;'>
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
		<div class="box-body table-responsive">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center" width='4%'>#</th>
						<th class="text-center" width='8%'>Tgl Invoice</th>
						<th class="text-center" width='8%'>No Invoice</th>
						<th class="text-center" width='8%'>No SO</th>
						<th class="text-center">Customer</th>
						<th class="text-center" width='12%'>Total Invoice</th>
						<th class="text-center" width='12%'>Total COGS</th>
						<th class="text-center" width='12%'>Gross Profit</th>
						<th class="text-center" width='12%'>Material</th>
						<th class="text-center" width='12%'>Direct</th>
						<th class="text-center" width='12%'>Indirect</th>
						<th class="text-center" width='12%'>Consumable</th>
						<th class="text-center" width='12%'>FOH</th>
						<th class="text-center" width='10%'>No Delivery</th>
                        <th class="text-center" width='10%'>Jenis Inv</th>
						<th class="text-center no-sort" width='16%'>Option</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
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
<!-- modal -->
<div class="modal fade" id="ModalUpload">
<form action="<?=base_url('invoicing/uploadfile')?>" method="POST" id="form_upload" enctype="multipart/form-data">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="head_title">Upload Invoice</h4>
			</div>
			<div class="modal-body" id="view">
			<input type="hidden" name="noinv" id="noinv">
                <div class="form-group">
                  <label for="fileinv">File Invoice</label>
                  <input type="file" id="doc_file" name="doc_file">
                </div>
			</div>
			<div class="modal-footer">
			<button type="submit" class="btn btn-primary">Upload</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</form>
</div>

<!-- modal -->
<?php $this->load->view('include/footer'); ?>

<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
	$(document).on('click', '.uploadfile', function(e){
		e.preventDefault();
		$("#noinv").val($(this).data('no_invoice'));
		$("#ModalUpload").modal();
	});
	$(document).ready(function(){
		$('#spinnerx').hide();

		var no_so = $('#no_so').val();
		var customer = $('#customer').val();
		DataTables(no_so, customer);

		$(document).on('change','#no_so, #customer', function(e){
			e.preventDefault();
			var no_so = $('#no_so').val();
			var customer = $('#customer').val();
			DataTables(no_so, customer);
		});
	});

	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		$("#head_title").html("<b>VIEW DETAIL PRODUCT ["+$(this).data('no_invoice')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_invoice/'+$(this).data('no_invoice'),
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

	$(document).on('click', '.print', function(e){
		e.preventDefault();
		var invoice = $(this).data('inv');

		loading_spinner();
		swal({
			title: "Jurnal akan diproses ?",
			text: "Data tidak bisa dirubah lagi !!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Ya, Proses!",
			cancelButtonText: "Tidak, Batalkan!",
			closeOnConfirm: true,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				$('#spinnerx').show();
				//window.open(base_url + active_controller+'/print_invoice/'+invoice);
				window.location.href = base_url + active_controller+'/print_invoice/'+invoice;
			}
			else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
			}
		});
	});

	$(document).on('click', '.terima', function(e){
		e.preventDefault();

		$("#head_title").html("<b>PENERIMAAN INVOICE ["+$(this).data('inv')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url +'penerimaan/modal_detail_invoice/'+$(this).data('inv'),
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
			"aaSorting": [[ 1, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"buttons": [
				{
                "extend": 'excel',
				}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_inv_cogs',
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

	function add_inv(){
        window.location.href = base_url + active_controller +'/create_new';
    }

</script>
