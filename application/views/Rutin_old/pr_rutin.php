<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<label>Search : &nbsp;&nbsp;&nbsp;</label>
			<input type="text" name="date_range" id="date_range" class="form-control input-md datepicker" style='margin-bottom:5px;' readonly="readonly" placeholder="Select Date">
			<button type='button'class="btn btn-sm btn-success" id='pdf_report' style='float:right;'>
				<i class="fa fa-pdf"></i> Print PDF
			</button>
		</div>
		<br><br>
		<div class="box-tool pull-left">
		<?php
			if($akses_menu['create']=='1'){ 
			?>
				<a href="<?php echo site_url('con_nonmat/warehouse_rutin') ?>" class="btn btn-sm btn-success" id='btn-add'>
					<i class="fa fa-plus"></i> &nbsp;&nbsp;Add Pengajuan
				</a>
			<?php
			}
		?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-center">No Pengajuan</th>
					<th class="text-center">No PR</th>
					<th class="text-center">Tgl Request</th>
					<th class="text-center">Nama Barang</th>
					<th class="text-center">Spec</th>
					<th class="text-center">Qty PR</th>
					<th class="text-center">Qty PO</th>
					<th class="text-center">Qty Incoming</th>
					<th class="text-center">Last Requestor</th> 
					<th class="text-center">Last Request Date</th>
					<th class="text-center no-sort">Status</th>
					<th class="text-center no-sort">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
 
 <div class="modal fade" id="ModalView" style='overflow-y: auto;'>
	<div class="modal-dialog"  style='width:50%; '>
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

</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.datepicker{
		cursor: pointer;
	}
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
		    DataTables(range);
		});

		$('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			var range = $(this).val();
		    DataTables(range);
		});
  
		var range 	= $('#date_range').val();
		DataTables(range);
		
		
	});
	
	$(document).on('click', '.view_pr', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL PR ["+$(this).data('kode')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_edit_pr/'+$(this).data('kode')+'/view',
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
	
	$(document).on('click', '.edit_pr', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>EDIT PENGAJUAN ["+$(this).data('kode')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_edit_pr/'+$(this).data('kode'),
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
	
	$(document).on('click', '#pdf_report', function(e){
		var range 	= $('#date_range').val();
		
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		
		var Link	= base_url + active_controller +'/pdf_report/'+tgl_awal+'/'+tgl_akhir;
			window.open(Link);
	});
	
	$(document).on('click', '#save_edit', function(e){
		e.preventDefault();
		$('#save_edit').prop('disabled',true);
		
		var purchase		= $('#purchase').val();
		
		if(purchase == '0' || purchase == ''){
			swal({
				title	: "Error Message!",
				text	: 'Qty empty, select first ...',
				type	: "warning"
			});

			$('#save_edit').prop('disabled',false);
			return false;
		}
		
		swal({ 
			title: "Are you sure?",
			text: "You will save be able to process again this data!",
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
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/modal_edit_pr',
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false, 
					contentType	: false,				
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
							window.location.href = base_url + active_controller+'/pr_rutin';
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
						$('#save_edit').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save_edit').prop('disabled',false);
			return false;
			}
		});
	});

	function DataTables(range = null){
		var dataTable = $('#my-grid').DataTable({
			"scrollCollapse" : true,
			"serverSide": true,
			"processing" : true,
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
				url : base_url + active_controller+'/server_side_app_rutin',
				type: "post",
				data: function(d){
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
