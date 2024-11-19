<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
	</div>
	<!-- /.box-header -->
	<div class="box-body"><br>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#rutin" class='rutin' aria-controls="rutin" role="tab" data-toggle="tab">Price Reference</a></li>
			<li role="presentation"><a href="#pricebook" class='pricebook' aria-controls="pricebook" role="tab" data-toggle="tab">Price Book</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="rutin">
				<br>
				<?php
				if($akses_menu['create']=='1'){
				?>
				&nbsp;&nbsp;<a href="<?php echo site_url('cost/excel_price_ref_stok') ?>" target='_blank' class="btn btn-sm btn-success" style='float:right;'>Download</a>
				<?php
					}
				?><br><br>
				<table class="table table-bordered table-striped" id="my-grid" width='100%'>
					<thead>
						<tr class='bg-blue'>
							<th class="text-center">#</th>
							<th class="text-center">Code Program</th>
							<th class="text-center">Material Name</th>
							<th class="text-center">Spesification</th>
							<th class="text-center">Brand</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Kurs</th>
							<th class="text-center">Price Ref</th>
							<th class="text-center">CostBook</th>
							<th class="text-center">Option</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="pricebook">
				<br>
				<div class='form-group row'>
					<div class='col-sm-2'>
						<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Select Date'>
					</div>
					<div class='col-sm-10'>
						<button type='button' class='btn btn-md btn-primary' id='show_date'>Show</button>
						<button type='button' class='btn btn-md btn-success' id='download_excel'>Download</button>
					</div>
				</div>
				<div class='form-group row'>
					<div class='col-sm-12' id='htmlPriceBook'>
					</div>
				</div>
			</div>
		</div>

		
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

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('input[type="text"][data-role="datepicker2"]').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth:true,
			changeYear:true,
			maxDate:'-1d',
			showButtonPanel: true,
			// closeText: 'Clear',
			// 	onClose: function (dateText, inst) {
			// 	if ($(window.event.srcElement).hasClass('ui-datepicker-close'))
			// 	{
			// 		document.getElementById(this.id).value = '';
			// 		var status 			= $('#status').val();
			// 		var date_filter 	= $('#date_filter').val();
			// 		DataTables2(status, date_filter);
			// 	}
			// }
		});

		DataTables();
	});

	$(document).on('click', '.history', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>HISTORY COSTBOOK</b>");
		$("#view").load(base_url + active_controller+'/modalHistoryCostBook/'+$(this).data('id'));
		$("#ModalView").modal();
	});

	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			// "scrollX": true,
			"scrollY": "500",
			"scrollCollapse" : true,
			"serverSide": true,
			"processing" : true,
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
				url : base_url +active_controller+'/getDataJSONConsumable',
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

	$(document).on('click', '.deleted', function(){
		var id	= $(this).data('id');
		// alert(bF);
		// return false;
		swal({
			title: "Are you sure?",
			text: "Delete this data ?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: true,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url+active_controller+'/hapus_consumable/'+id,
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
							window.location.href = base_url + active_controller+'/consumable';
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

	$(document).on('click', '#show_date', function(e){
		let date_filter   = $('#date_filter').val();
		var baseurl=base_url + active_controller +'/get_history_costbook_rutin/'+date_filter;
		$.ajax({
			url			: baseurl,
			type		: "POST",
			cache		: false,
			dataType	: 'json',
			beforeSend	: function(){
				loading_spinner()
			},
			success		: function(data){
				$('#htmlPriceBook').html(data.option);
				swal.close()
			},
			error: function() {

				swal({
					title		: "Error Message !",
					text		: 'An Error Occured During Process. Please try again..',
					type		: "warning",
					timer		: 3000
				});
			}
		});
	});


</script>
