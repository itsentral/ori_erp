<?php
$this->load->view('include/side_menu');
$status = $tanda;
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
	<input type="hidden" id='status' name='status' value='<?= $status; ?>'>
	<input type="hidden" id='tanda' name='tanda' value='<?= $tanda; ?>'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title; ?></h3>
			<div class="box-tool pull-right">
				<?php
				if ($tanda == 'pipe') {
				?>
					<button type='button' class='btn btn-sm btn-success' id='make_cutting'><i class='fa fa-scissors'></i>&nbsp;Buat Cutting</button>
				<?php } ?>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">#</th>
						<th class="text-center">No SPK</th>
						<th class="text-center">Product</th>
						<th class="text-center no-sort">No SO</th>
						<th class="text-center no-sort">Customer</th>
						<th class="text-center no-sort">Project</th>
						<th class="text-center no-sort">Spec</th>
						<th class="text-center no-sort">Product Code</th>
						<th class="text-center no-sort">Berat (kg)</th>
						<th class="text-center no-sort" width="50">#</th>
						<?php if ($tanda == 'cutting') { ?>
							<th class="text-center no-sort" width="30">QR</th>
						<?php }
						if ($tanda == 'pipe') { ?>
							<th class="text-center no-sort">Cut</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<?php
			if ($tanda == 'cutting') {
			?>
			<br>
			<h5>Product Deadstock Cutting</h5>
			<table class="table table-sm table-bordered table-striped" id="my-grid3" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">#</th>
						<th class="text-center">No SPK</th>
						<th class="text-center">Product</th>
						<th class="text-center no-sort">No SO</th>
						<th class="text-center no-sort">Customer</th>
						<th class="text-center no-sort">Project</th>
						<th class="text-center no-sort" width="50">#</th>
						<th class="text-center no-sort" width="30">QR</th>

					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<?php
			}
			?>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->
</form>
<!-- modal -->
<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
	<div class="modal-dialog" style='width:90%; '>
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
	$(document).ready(function() {
		$('.chosen-select').chosen({
			width: '150px'
		})

		let status = $('#status').val();
		let tanda = $('#tanda').val();
		DataTables2(status);
		if(tanda='cutting'){
			DataTables3();
		}

		$(document).on('click', '.look_history', function(e) {
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL</b>");
			$("#view").load(base_url + 'gudang_wg/detail_berat/' + $(this).data('kode_spk') + '/' + $(this)
				.data('id_production_detail') + '/' + $(this).data('category') + '/' + $(this).data(
					'qty') + '/' + status + '/' + $(this).data('length') + '/' + $(this).data(
					'length_awal'));
			$("#ModalView").modal();
		});

		$(document).on('click', '#chk_all', function() {
			if ($(this).is(':checked') == true) {
				$('input:checkbox.chk_item').prop('checked', true);
			} else {
				$('input:checkbox.chk_item').prop('checked', false);

			}
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
						var formData = new FormData($('#form_proses_bro')[0]);
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

		$(document).on('click', '.qr', function(e) {
			e.preventDefault();
			// loading_spinner();
			$("#head_title").html("<b>QR Code Product</b>");
			$("#view").load(base_url + active_controller + '/modalCreateQR/' + $(this).data('kode_spk') +
				'/' + $(this).data('id_produksi') + '/' + $(this).data('id_milik') + '/' + $(this).data(
					'id_pro_detail'));
			$("#ModalView").modal();
		});

		$(document).on('click', '#print_qrcode', function(e) {
			e.preventDefault();
			var idmilik = [];
			let logo = $('input[name="logo"]:checked').val()
			let size = $('input[name="size"]:checked').val()
			console.log("ID atas : " + idmilik);
			$('.chk_item').each(function(i, obj) {
				if (this.checked) {
					idmilik.push($(this).val());
				}
			});
			console.log(idmilik);
			// '/print_qrcode/' + idmilik + "/" + logo + "/" + size;
			// console.log(idmilik.length);
			if (idmilik.length > 0) {
				idmilik = idmilik.join("-")
				var Links = base_url + active_controller + '/print_qrcode/' + idmilik + "/" + logo + "/" +
					size;
				window.open(Links, '_blank');
			} else {
				swal({
					title: "Warning!",
					text: "Mohon pilih produk terlebih dahulu!",
					type: "warning",
					timer: 5000
				});
			}
		});

		$(document).on('click', '.qr-cutting', function(e) {
			e.preventDefault();
			// loading_spinner();
			$("#head_title").html("<b>QR Code Product</b>");
			$("#view").load(base_url + active_controller + '/modalCreateQRCutting/' + $(this).data(
				'id_cutting'))
			$("#ModalView").modal();
		});

		$(document).on('click', '#print_qrcode_cutting', function(e) {
			e.preventDefault();
			var idCutting = $('#idCutting').val();
			let logo = $('input[name="logo"]:checked').val()
			let size = $('input[name="size"]:checked').val()

			if (idCutting.length > 0) {
				var Links = base_url + active_controller + '/print_qrcode_cutting/' + idCutting + "/" +
					logo + "/" +
					size;
				window.open(Links, '_blank');
			} else {
				swal({
					title: "Warning!",
					text: "Mohon pilih produk terlebih dahulu!",
					type: "warning",
					timer: 5000
				});
			}
		});

	});


	function DataTables2(status = null) {
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave": true,
			"bAutoWidth": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [
				[1, "asc"]
			],
			"columnDefs": [{
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [
				[10, 20, 50, 100, 150],
				[10, 20, 50, 100, 150]
			],
			"ajax": {
				url: base_url + active_controller + '/server_side_qc',
				type: "post",
				data: function(d) {
					d.status = status
				},
				cache: false,
				error: function() {
					$(".my-grid2-error").html("");
					$("#my-grid2").append(
						'<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#my-grid2_processing").css("display", "none");
				}
			}
		});
	}

	function DataTables3() {
		var dataTable = $('#my-grid3').DataTable({
			"serverSide": true,
			"stateSave": true,
			"bAutoWidth": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [
				[1, "asc"]
			],
			"columnDefs": [{
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [
				[10, 20, 50, 100, 150],
				[10, 20, 50, 100, 150]
			],
			"ajax": {
				url: base_url + active_controller + '/server_side_cutting_deadstock',
				type: "post",
				// data: function(d) {
				// 	d.status = status
				// },
				cache: false,
				error: function() {
					$(".my-grid2-error").html("");
					$("#my-grid2").append(
						'<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#my-grid2_processing").css("display", "none");
				}
			}
		});
	}
</script>