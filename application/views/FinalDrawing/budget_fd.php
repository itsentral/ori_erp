<?php
$this->load->view('include/side_menu');
$ArrList = array();
foreach($ListIPP AS $val => $valx){
	$ArrList[$valx['id_bq']] = $valx['id_bq'];
}
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-left">
			<button type='button' id='update_cost' style='min-width:150px;' class="btn btn-sm btn-primary">
			Update
			</button>
			<?php
				echo form_dropdown('no_ipp_filter[]', $ArrList, '0', array('id'=>'no_ipp_filter','multiple'=>'multiple','class'=>'form-control input-md'));
			?>
			<br><div style='color:red;'><b><span style='color:#892b07;'>SELECT MULTIPLE IPP TO SPEED UP THE LOADING PROCESS.</span></b></div>
			<?php if(!empty($get_by[0]['create_by'])){ 
				?>
				<div style='color:red;'><b>Last Update by <span style='color:green;'><?= strtoupper(strtolower($get_by[0]['create_by']))."</span> On <u>".date('d-m-Y H:i:s', strtotime($get_by[0]['create_date']));?></u></b></div>
			<?php 
			}
			else{
				?>
				<div style='color:red;'><b>Update failed (Connection Timeout Server), Please Try Again ...</b></div>
			<?php 
			}
			?>
			<div id="spinnerx">
				<img src="<?php echo base_url('assets/img/tres_load.gif') ?>" > <span style='color:green; font-size:16px;'><b>Please Wait ...</b></span>
			</div>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>No</th>
					<th class="text-center" width='6%'>IPP</th>
					<th class="text-center" width='15%'>Project</th>
					<th class="text-center" width='9%'>Est Mat (Kg)</th>
					<th class="text-center" width='7%'>Est Cost</th>
					<th class="text-center" width='7%'>Process</th>
					<th class="text-center" width='7%'>FOH</th>
					<th class="text-center" width='7%'>Profit</th>
					<th class="text-center" width='7%'>Allow</th>
					<th class="text-center" width='7%'>Packing</th>
					<th class="text-center" width='7%'>Eng</th>
					<th class="text-center" width='7%'>Truck</th>
					<th class="text-center" width='11%'>Option</th>
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
	
	<div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:50%; '>
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
	<div class="modal fade" id="ModalView3">
		<div class="modal-dialog"  style='width:70%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title3"></h4>
					</div>
					<div class="modal-body" id="view3">
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
		DataTables(); 
	});

	$(document).on('click', '.MatDetailCost', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>DETAIL ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalDetailBudgetCost/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('id_bq'));
		$("#ModalView3").modal();
	});

	$(document).on('click', '.detail_group', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>DETAIL GROUP COMPONENT</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalDetailBudgetGroup/'+$(this).data('id_milik')+'/'+$(this).data('id_bq')+'/'+$(this).data('qty'));
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '.ViewDT', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>VIEW FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalViewBudgetFd/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.ViewSO', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>VIEW DETAIL FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalViewBudgetDetail/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#update_cost', function(){
		swal({
		  title: "Update Budget Final Drawing ?",
		  text: "Tunggu sampai 'Last Update by ' menunjukan nama user dan update jam sekarang. ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Update!",
		  cancelButtonText: "Tidak, Batalkan!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				// loading_spinner();
				$('#spinnerx').show();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/insert_select_budget_fd2',
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
							$('#spinnerx').hide();
							window.location.href = base_url + active_controller +'/budget_fd';
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
							$('#spinnerx').hide();
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
						$('#spinnerx').hide();
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '.download_excel', function(){
		var id_bq		= $(this).data('id_bq');
		
		var Links		= base_url +'index.php/'+ active_controller+'/ExcelBudgetFd/'+id_bq;
		window.open(Links,'_blank');
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
				url : base_url +'index.php/'+active_controller+'/getDataJSONQuo',
				type: "post",
				data: function(d){
					// d.kode_partner = $('#kode_partner').val()
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
