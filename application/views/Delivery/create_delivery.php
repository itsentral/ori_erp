<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title; ?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No. Surat Jalan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id' => 'nomor_sj', 'name' => 'nomor_sj', 'class' => 'form-control input-md', 'placeholder' => 'No Surat Jalan'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Alamat <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_textarea(array('id' => 'alamat', 'name' => 'alamat', 'class' => 'form-control input-md', 'rows' => 3, 'placeholder' => 'Alamat Delivery'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Delivery Date <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id' => 'delivery_date', 'name' => 'delivery_date', 'class' => 'form-control input-md', 'readonly' => true));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_textarea(array('id' => 'project', 'name' => 'project', 'class' => 'form-control input-md', 'rows' => 3, 'placeholder' => 'Project'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Nomor SO <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<div class="form-group">
						<select name="list_so[]" id="list_so" multiple class="form-control chosen-select">
							<option value='0'>PILIH SO</option>
							<?php

							foreach ($dataSO as $val => $valx) {
								echo "<option value='" . $valx->id_produksi . "'>" . $valx->so_number . "</option>";
							}
							?>
						</select>
						<!-- <textarea name="no_so" id="no_so" class="form-control" readonly placeholder="Nomor SO"></textarea> -->
					</div>
					<!-- <button type="button" id="add-so" class="btn btn-warning" title="Add SO"><i class="fa fa-plus"></i> Add SO</button> -->
				</div>
			</div>
			<div class="box-footer">
				<button type="button" class="btn btn-success" id="saveDelivery"><i class="fa fa-save"></i> Save</button>
				<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-default"><i class="fa fa-reply"></i> Kembali</a>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal-id" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary"><i class="fa fa-save"></i> Simpan SO</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function() {
		$('#delivery_date').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});

		$('.chosen-select').chosen({
			width: '150px'
		})

		$(document).on('click', '#delete_spool', function() {

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
							url: base_url + active_controller + '/delete_delivery',
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
									window.location.href = base_url + active_controller;
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

		$(document).on('click', '#saveDelivery', function() {
			var nomor_sj = $('#nomor_sj').val()
			var delivery_date = $('#delivery_date').val()
			var project = $('#project').val()
			var alamat = $('#alamat').val()

			if (nomor_sj == '') {
				swal({
					title: "Error Message!",
					text: 'Nomor surat jalan empty, select first ...',
					type: "warning"
				});
				return false;
			}

			if (delivery_date == '') {
				swal({
					title: "Error Message!",
					text: 'Delivery Date empty, select first ...',
					type: "warning"
				});
				return false;
			}

			if (project == '') {
				swal({
					title: "Error Message!",
					text: 'Project Name empty, select first ...',
					type: "warning"
				});
				return false;
			}

			if (alamat == '') {
				swal({
					title: "Error Message!",
					text: 'Address delivery empty, select first ...',
					type: "warning"
				});
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
						// loading_spinner();
						var formData = new FormData($('#form_proses_bro')[0]);
						$.ajax({
							url: base_url + active_controller + '/saveDelivery',
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
									window.location.href = base_url + active_controller + '/edit_delivery/' + data.kode_delivery;
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

		/* ADD SO */

		$(document).on('click', '#add-so', function() {
			$('#modal-id').modal('show')
			$('#modal-id .modal-title').text('List SO')
			$('#modal-id .modal-body').load(base_url + active_controller + '/load_so')
		})
	});
</script>