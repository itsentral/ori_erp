<?php
$this->load->view('include/side_menu');

$sel1 = ($data_uri == '1')?'selected':'';
$sel2 = ($data_uri == '2')?'selected':'';
$sel3 = ($data_uri == '3')?'selected':'';
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-left">
		<!--
			<button type='button' id='update_cost' style='min-width:150px;' class="btn btn-sm btn-primary">
				Update
			  </button>
			<br>
			-->
			<?php
			if(!empty($get_by)){?>
			<div style='color:red;'><b>Last Update by <span style='color:green;'><?= strtoupper(strtolower($get_by[0]['create_by']))."</span> On <u>".date('d-m-Y H:i:s', strtotime($get_by[0]['create_date']));?></u></b></div>
			<?php }else{ ?>
			<div style='color:red;'><b>Update System Failed</b></div>
			<?php } ?>
			
			<div id="spinnerx">
				<img src="<?php echo base_url('assets/img/tres_load.gif') ?>" > <span style='color:green; font-size:16px;'><b>Please Wait ...</b></span>
			</div>
		</div><br><br>
		<div class="box-tool pull-right">
			<!-- <label>Search : </label> -->
			<select id='status' name='status' class='form-control input-sm' style='min-width:200px;'>
				<option value=''>All Status</option>
				<option value='OVER BUDGET' <?=$sel1;?>>OVER BUDGET</option>
				<option value='FINISH' <?=$sel2;?>>SESUAI STANDARD</option>
				<option value='FINISH 2' <?=$sel3;?>>DIBAWAH STANDARD</option>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead> 
				<tr class='bg-blue'>  
					<th width='1%' class="text-center no-sort">No</th>
					<th width='5%' class="text-center">IPP</th>  
					<th class="text-center">Customer</th>
					<th width='5%' class="text-center no-sort">Type</th>
					<th width='6%' class="text-center no-sort">Series</th>
					<th width='9%' class="text-center no-sort">Material (Est)</th>
					<th width='7%' class="text-center no-sort">Cost (Est)</th>
					<th width='9%' class="text-center no-sort">Material (Real)</th>
					<th width='7%' class="text-center no-sort">Cost (Real)</th>
					<th width='1%' class="text-center no-sort">Rev</th>
					<th width='10%' class="text-center no-sort">Status</th>
					<th width='9%' class="text-center no-sort">Option</th>
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
		<div class="modal-dialog"  style='width:90%;'  style='overflow-y: auto;'>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:80%;'  style='overflow-y: auto;'>
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
		<div class="modal-dialog"  style='width:30%;'  style='overflow-y: auto;'> 
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
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		var status = $('#status').val();
		DataTables(status);
		
		// $.ajax({
			// url : base_url +'index.php/'+active_controller+'/insert_select_process',
			// cache: false,
			// type: "POST",
			// dataType: "json",
			// success: function(response){
				 // swal.close()
			// }
		// });
	});
	
	$(document).on('change','#status', function(e){
		e.preventDefault();
		var status = $('#status').val();
		DataTables(status);
	});
	
	$(document).on('click', '#detail_process_cost', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>DETAIL COST PROCESS BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalDetailProcess/'+$(this).data('id_bq'));
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#detailBQ', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailBQ/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.view_data', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL DATA ["+$(this).data('id_bq')+"]</b>"); 
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/view_modal_view_dt/'+$(this).data('id_bq')+'/'+$(this).data('cost_control'),
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
	
	$(document).on('click', '.total_material', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>TOTAL METRIAL PROJECT ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/view_modal_total_material/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '#ApproveDT', function(e){
		e.preventDefault();
		$("#head_title").html("<b>APPROVE PROJECT PRICE ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppCost/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#detailPlant', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL PRODUCTION ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailPrice/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#update_cost', function(){
		swal({
		  title: "Update Cost Control On Progress ?",
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
				$.ajax({
					url			: base_url + active_controller+'/insert_select_process',
					type		: "POST",
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
							window.location.href = base_url + active_controller + '/on_progress';
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
		
	function DataTables(status = null){
		// loading_spinner();
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_on_progress/cost_control',
				type: "post",
				data: function(d){
					d.status = status
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
