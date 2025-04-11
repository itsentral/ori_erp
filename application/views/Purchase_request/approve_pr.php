<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
            
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>No</th>
					<th class="text-center">Material</th>
					<th class="text-center" width='6%'>Category</th>
					<th class="text-center" width='8%'>Stock Actual</th>
					<th class="text-center" width='8%'>Dipesan Project</th>
					<th class="text-center" width='8%'>Stock Free</th>
					<th class="text-center" width='8%'>Re-Order Point</th>
					<th class="text-center" width='8%'>MOQ</th>
					<th class="text-center" width='8%'>Qty PR</th>
					<th class="text-center" width='8%'>Tgl Dibutuhkan</th>
					<th class="text-center" width='8%'>Rev. Qty PR</th>
					<th class="text-center no-sort" width='6%'>#</th>
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
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
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
        $('.maskM').maskMoney();
		$('.tgl').datepicker();
    });
	
	$(document).on('click','.app_pr', function(){
		var nomor 		= $(this).data('no');
		var id_material = $(this).data('id_material');
		var status = $(this).data('status');
		var qty_request = $('#tot_pur_'+nomor).val().split(",").join("");
		var qty_revisi 	= $('#tot_rev_'+nomor).val().split(",").join("");
		var tanggal 	= $('#tgl_butuh_'+nomor).val();
		
		var moq 			= $('#moq_'+nomor).val();
		var reorder_point 	= $('#reorder_point_'+nomor).val();
		var sisa_avl 		= $('#sisa_avl_'+nomor).val();
		var book_per_month 	= $('#book_per_month_'+nomor).val();
		
		swal({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/save_approve_pr',
					type		: "POST",
					data: {
						"id_material" 	: id_material,
						"qty_request" 	: qty_request,
						"qty_revisi" 	: qty_revisi,
						"tanggal" 		: tanggal,
						"moq" 			: moq,
						"reorder_point"	: reorder_point,
						"sisa_avl" 		: sisa_avl,
						"book_per_month": book_per_month,
						"status"		: status
					},
					cache		: false,
					dataType	: 'json',
					success		: function(data){
						if(data.status == 1){
							swal({
									title	: "Save Success!",
									text	: data.pesan,
									type	: "success",
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller+'/approval_pr';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',
							type				: "warning",
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
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
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_app_pr',
				type: "post",
				// data: function(d){
					// d.gudang = $('#gudang').val()
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

	
</script>
