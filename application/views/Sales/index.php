<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['create']=='1'){
		?>
		  <a href="<?php echo site_url('sales/request') ?>" class="btn btn-sm btn-success" style='float:right;' id='btn-add'>
			<i class="fa fa-plus"></i> &nbsp;&nbsp;Add Request
		  </a>
		  <?php
			}
		  ?><br><br>
		  <select name='status' id='status' class='form-control input-sm' style='min-width:250px;'>
			<option value='0'>ALL STATUS</option>
			<?php
			foreach($status as $row)
			{
				echo "<option value='".$row->status."'>".strtoupper($row->status)."</option>";
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
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='6%'>IPP</th>
					<th class="text-center" width='17%'>Customer</th>
					<th class="text-center">Project</th>
					<th class="text-center no-sort" width='5%'>Rev</th>
					<th class="text-center no-sort" width='6%'>Last</th>
					<th class="text-center no-sort" width='7%'>Date</th>
					<th class="text-center no-sort" width='15%'>Status</th>
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
.scrollbar
{
	margin-left: 30px;
	float: left;
	height: 300px;
	width: 65px;
	background: #F5F5F5;
	overflow-y: scroll;
	margin-bottom: 25px;
}
</style>
<script>
	$(document).ready(function(){
		var status = $('#status').val();
		DataTables(status);
		
		$(document).on('change','#status', function(e){
			e.preventDefault();
			var status = $('#status').val();
			DataTables(status);
		});
	});
	
	$(document).on('click', '#detailSO', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL IDENTIFICATION OF CUSTOMER ["+$(this).data('no_ipp')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/detail_ipp/'+$(this).data('no_ipp'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 3000
				});
			}
		});
	});
	
	$(document).on('click', '#CancelIPP', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>CANCEL IDENTIFICATION OF CUSTOMER ["+$(this).data('no_ipp')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/cancel_ipp/'+$(this).data('no_ipp'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 3000
				});
			}
		});
	});
	
	$(document).on('click', '#EditIPP', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>EDIT IDENTIFICATION OF CUSTOMER ["+$(this).data('no_ipp')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/edit_ipp/'+$(this).data('no_ipp'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 3000
				});
			}
		});
	});
	
	//Cancel
	$(document).on('click', '#cancel_ipp', function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var status_reason			= $('#status_reason').val();

		if(status_reason=='' || status_reason==null || status_reason=='-'){
			swal({
			  title	: "Error Message!",
			  text	: 'Reason cancel is empty, please input first ...',
			  type	: "warning"
			});
			$('#cancel_ipp').prop('disabled',false);
			return false;
		}

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
					var formData 	=new FormData($('#form_proses_bro')[0]);
					var baseurl=base_url + active_controller +'/cancel_ipp';
					$.ajax({
						url			: baseurl,
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
									  timer	: 3000
									});
								window.location.href = base_url + active_controller;
							}
							else if(data.status == 0){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 3000
								});
							}
							$('#cancel_ipp').prop('disabled',false);
						},
						error: function() {

							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',
							  type				: "warning",
							  timer				: 3000
							});
							$('#cancel_ipp').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#cancel_ipp').prop('disabled',false);
				return false;
			  }
		});
	});
	
	//Ajukan IPP
	$(document).on('click', '.ajukan_ipp', function(e){
		e.preventDefault();
		var no_ipp			= $(this).data('ipp');

		swal({
			  title: "Are you sure?",
			  text: "Release IPP ke Enggnering !",
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
					var formData 	=new FormData($('#form_proses_bro')[0]);
					var baseurl=base_url + active_controller +'/ajukan_ipp/'+no_ipp;
					$.ajax({
						url			: baseurl,
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
									  timer	: 3000
									});
								window.location.href = base_url + active_controller;
							}
							else if(data.status == 0){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 3000
								});
							}
						},
						error: function() {

							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',
							  type				: "warning",
							  timer				: 3000
							});
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
			  }
		});
	});
		
	function DataTables(status=null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"processing": true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
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
				url : base_url + active_controller+'/server_side_sales_ipp', 
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
