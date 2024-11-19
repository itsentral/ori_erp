<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class='form-group row'>
			<div class='col-sm-2'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
			<div class='col-sm-8'>
				<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
				<!-- <a href="<?php echo site_url('deadstok_fg/download_excel') ?>" class="btn btn-sm btn-success" style='float:right; margin-right:5px;'>Download Data</a> -->
			</div>
			<div class='col-sm-2 text-right'>
				<button type='button' class='btn btn-sm btn-success' id='make_cutting'><i class='fa fa-scissors'></i>&nbsp;Buat Cutting</button>
			</div>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
	<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">FG Deadstok</a></li>
            <li><a data-toggle="tab" href="#menu1">FG Deadstok Modifikasi</a></li>
        </ul>
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br>
				<table class="table table-bordered table-striped" id="my-grid" width='100%'>
					<thead class='thead'>
						<tr class='bg-blue'>
							<th class="text-center th">#</th>
							<th class="text-center th">NO SO</th>
							<th class="text-center th">NO SPK</th>
							<th class="text-center th">Customer</th>
							<th class="text-center th">Project</th>
							<th class="text-center th">Product</th>
							<th class="text-center th">Spec</th>
							<th class="text-center th no-sort">Qty</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
            </div>
            <div id="menu1" class="tab-pane fade"><br>
                <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
							<th class="text-center th">#</th>
							<th class="text-center th">NO SO</th>
							<th class="text-center th">NO SPK</th>
							<th class="text-center th">Customer</th>
							<th class="text-center th">Project</th>
							<th class="text-center th">Product</th>
							<th class="text-center th">Spec</th>
							<th class="text-center th no-sort">Qty</th>
							<th class="text-center th no-sort">Cut</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var date_filter = $('#date_filter').val();
		DataTables(date_filter);
		DataTables2(date_filter);

		$(document).on('change','#date_filter', function(e){
			e.preventDefault();
			var date_filter = $('#date_filter').val();
			DataTables(date_filter);
			DataTables2(date_filter);
		});

		$('input[type="text"][data-role="datepicker2"]').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth:true,
			changeYear:true,
			maxDate:'-1d',
			showButtonPanel: true,
			closeText: 'Clear',
				onClose: function (dateText, inst) {
				if ($(window.event.srcElement).hasClass('ui-datepicker-close'))
				{
					document.getElementById(this.id).value = '';
					var date_filter 	= $('#date_filter').val();
					DataTables(date_filter);
					DataTables2(date_filter);
				}
			}
		});

		$(document).on('click', '#download_excel', function(e){
			e.preventDefault();
			var date_filter 	= $('#date_filter').val();
			var date_filter_ 	= 0;
			if(date_filter != ''){
				var date_filter_ 	= $('#date_filter').val();
			}
			var Links		= base_url + active_controller+'/download_excel/'+date_filter;
			window.open(Links,'_blank');
		});

		$(document).on('click', '#make_cutting', function() {

			if ($('.chk_personal:checked').length == 0) {
				swal({
					title: "Error Message!",
					text: 'Checklist product minimal 1',
					type: "warning"
				});
				return false;
			}
			// return false;
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
						// loading_spinner();
						var formData = new FormData($('#form_proses')[0]);
						$.ajax({
							url: base_url + active_controller + '/create_cutting',
							type: "POST",
							data: formData,
							cache: false,
							dataType: 'json',
							processData: false,
							contentType: false,
							success: function(data) {
								if (data.status == 1) {
									swal({
										title: "Success!",
										text: 'Succcess Process!',
										type: "success",
										timer: 3000
									});
									window.location.href = base_url + active_controller +
										'/index/' + status;
								} else {
									swal({
										title: "Failed!",
										text: 'Failed Process!',
										type: "warning",
										timer: 3000
									});
								}
							},
							error: function() {
								swal({
									title: "Error Message !",
									text: 'An Error Occured During Process. Please try again..',
									type: "warning",
									timer: 3000
								});
							}
						});
					} else {
						swal("Cancelled", "Data can be process again :)", "error");
						return false;
					}
				});
			});
	});
		
	function DataTables(date_filter=null){
		var dataTable = $('#my-grid').DataTable({
			
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": true,
			"lengthChange": true,
			"destroy": true,
			"processing": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON',
				type: "post",
				data: function(d){
					d.date_filter = date_filter
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

	function DataTables2(date_filter=null){
		var dataTable = $('#my-grid2').DataTable({
			
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": true,
			"lengthChange": true,
			"destroy": true,
			"processing": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON_modif',
				type: "post",
				data: function(d){
					d.date_filter = date_filter
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
