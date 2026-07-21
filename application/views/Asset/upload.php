<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_upload" enctype="multipart/form-data">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					<div class="callout callout-info">
						<h4>Petunjuk Upload Asset</h4>
						<ol>
							<li>Download template Excel terlebih dahulu.</li>
							<li>Isi data asset sesuai format template <b>(JANGAN MERUBAH STRUKTUR KOLOM)</b>.</li>
							<li>Kolom <b>category</b> diisi dengan ID category (lihat sheet 'Reference' di template).</li>
							<li>Kolom <b>category_pajak</b> diisi dengan ID category pajak (lihat sheet 'Reference' di template).</li>
							<li>Kolom <b>id_coa</b> diisi dengan ID COA (lihat sheet 'Reference' di template).</li>
							<li>Kolom <b>branch</b> diisi kode cabang (lihat sheet 'Reference' di template).</li>
							<li>Upload file Excel tersebut.</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Download Template</label><br>
						<a href="<?php echo site_url('asset/download_template_upload'); ?>" class="btn btn-info" target="_blank">
							<i class="fa fa-download"></i> Download Template
						</a>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Upload File Excel</label>
						<input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xls,.xlsx" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<button type="button" class="btn btn-success" id="btn_upload">
						<i class="fa fa-upload"></i> Upload & Import
					</button>
					<a href="<?php echo site_url('asset'); ?>" class="btn btn-default">
						<i class="fa fa-arrow-left"></i> Kembali
					</a>
				</div>
			</div>

			<!-- Result Area -->
			<div class="row" style="margin-top:20px;">
				<div class="col-md-12">
					<div id="result_area"></div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php $this->load->view('include/footer'); ?>
<script type="text/javascript">
$(document).ready(function(){
	$(document).on('click', '#btn_upload', function(e){
		e.preventDefault();
		var fileInput = $('#excel_file')[0].files[0];
		if(!fileInput){
			swal("Warning", "Pilih file Excel terlebih dahulu!", "warning");
			return false;
		}

		var ext = fileInput.name.split('.').pop().toLowerCase();
		if($.inArray(ext, ['xls','xlsx']) == -1){
			swal("Warning", "Format file harus .xls atau .xlsx!", "warning");
			return false;
		}

		swal({
			title: "Konfirmasi",
			text: "Apakah Anda yakin ingin upload data asset ini?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-success",
			confirmButtonText: "Ya, Upload!",
			cancelButtonText: "Batal",
			closeOnConfirm: false
		},
		function(isConfirm){
			if(isConfirm){
				loading_spinner();
				var formData = new FormData($('#form_upload')[0]);
				$.ajax({
					url: base_url + active_controller + '/proses_upload',
					type: 'POST',
					data: formData,
					cache: false,
					dataType: 'json',
					processData: false,
					contentType: false,
					success: function(data){
						if(data.status == 1){
							swal({
								title: "Success!",
								text: data.pesan,
								type: "success",
								timer: 5000
							});
							$('#result_area').html('<div class="alert alert-success">'+data.pesan+'</div>');
						} else {
							swal({
								title: "Failed!",
								text: data.pesan,
								type: "warning"
							});
							if(data.errors){
								var errHtml = '<div class="alert alert-danger"><b>Error Detail:</b><ul>';
								$.each(data.errors, function(i, val){
									errHtml += '<li>'+val+'</li>';
								});
								errHtml += '</ul></div>';
								$('#result_area').html(errHtml);
							}
						}
					},
					error: function(){
						swal("Error", "Terjadi kesalahan. Silakan coba lagi.", "error");
					}
				});
			}
		});
	});
});
</script>
