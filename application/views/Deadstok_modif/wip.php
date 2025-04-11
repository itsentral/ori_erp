<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title; ?></h3>
			<div class="box-tool pull-right"></div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">#</th>
						<th class="text-center">No SPK</th>
						<th class="text-center">Product</th>
						<th class="text-center">No SO</th>
						<th class="text-center no-sort">Customer</th>
						<th class="text-center no-sort">Project</th>
						<th class="text-center no-sort">Spec</th>
						<th class="text-center no-sort">Qty</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
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
		DataTables2();

		$(document).on('click', '#chk_all', function() {
			if ($(this).is(':checked') == true) {
				$('input:checkbox.chk_item').prop('checked', true);
			} else {
				$('input:checkbox.chk_item').prop('checked', false);

			}
		});

		$(document).on('click', '.qr', function(e) {
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>QR Code Product</b>");
			$("#view").load(base_url + active_controller + '/modalCreateQR/' + $(this).data('kode_spk') + '/' + $(this).data('id_produksi') + '/' + $(this).data('id_milik') + '/' + $(this).data('id_pro_detail'));
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
				var Links = base_url + active_controller + '/print_qrcode/' + idmilik + "/" + logo + "/" + size;
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


	function DataTables2() {
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
				url: base_url + active_controller + '/server_side_gudang_tanki',
				type: "post",
				// data: function(d) {
				// 	d.status = status
				// },
				cache: false,
				error: function() {
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display", "none");
				}
			}
		});
	}
</script>