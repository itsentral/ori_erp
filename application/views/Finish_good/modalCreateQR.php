<<<<<<< HEAD
<?php
$start_time				= (!empty($get_spk)) ? $get_spk[0]->start_time : '';
$finish_time			= (!empty($get_spk)) ? $get_spk[0]->finish_time : '';
$cycletime				= (!empty($get_spk)) ? $get_spk[0]->cycletime : '';
$total_time				= (!empty($get_spk)) ? $get_spk[0]->total_time : '';
$productivity			= (!empty($get_spk)) ? $get_spk[0]->productivity : '';
$upload_spk				= (!empty($get_spk)) ? $get_spk[0]->upload_spk : '';
$next_process			= (!empty($get_spk)) ? $get_spk[0]->next_process : '';

// echo "<pre>";
// print_r($get_split_code);
// echo "</pre>";
?>
<input type='hidden' name='kode_spk' value='<?= $kode_spk; ?>'>
<input type='hidden' name='id_produksi' value='<?= $id_produksi; ?>'>
<input type='hidden' name='id_milik' value='<?= $id_milik; ?>'>
<input type='hidden' name='id_milik2' value='<?= $id_milik2; ?>'>
<input type='hidden' name='id_product' value='<?= $id_product; ?>'>
<input type='hidden' name='total_qty' value='<?= COUNT($get_split_code); ?>'>
<input type='hidden' name='first_id' value='<?= $first_id; ?>'>
<input type='hidden' name='time_uniq' value='<?= $time_uniq; ?>'>
<input type='hidden' name='id_spk' value='<?= $get_spk[0]->id; ?>'>
<!-- <?= $time_uniq; ?> / <?= $id_milik2; ?> -->

<div class="box box-info">
	<div class="box-body bg-info">
		<div class="row">
			<div class="col-md-3">
				<div class="form-horizontal">
					<label class="">Logo :</label>
					<div class="form-group row">
						<div class="col-sm-2">
							<div class="radio">
								<label>
									<input type="radio" name="logo" id="logo_ORI" value="ORI" checked="checked">
									ORI
								</label>
							</div>

							<div class="radio">
								<label>
									<input type="radio" name="logo" id="logo_NOV" value="NOV">
									NOV
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-horizontal">
					<label class="">Size :</label>
					<div class="form-group row">
						<div class="col-sm-2">
							<div class="radio">
								<label>
									<input type="radio" name="size" id="size_lg" value="lg" checked="checked">
									Large
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="size" id="size_md" value="md">
									Medium
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="size" id="size_sm" value="sm">
									Small
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box-footer bg-info">
		<button type="button" style="margin-bottom:10px" id="print_qrcode" class="btn btn-success"><i class="fa fa-print"></i> Print QR Code</button>
	</div>
</div>

<div class="box box-primary">
	<div class="box-body">
		<div class='form-group row'>
			<div class='col-sm-12'>
				<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
					<thead>
						<tr class='bg-blue'>
							<th class="text-center" width='3%'>
								<input type='checkbox' name='chk_all' id='chk_all'>
							</th>
							<th class="text-center" width='15%'>Product</th>
							<th class="text-center" width='10%'>Spec</th>
							<th class="text-center" width='12%'>Product Code</th>
							<th class="text-center" width='10%'>Cutting</th>
							<th class="text-center" width='6%'>Status</th>
							<th class="text-center" width='10%'>Daycode</th>
							<td class="text-center" width='10%'>QC Pass Date</td>
							<th class="text-center">Keterangan</th>
							<th class="text-center">Resin</th>
							<th class="text-center" width='50'>Flag QR</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$spec = spec_bq2($id_milik);
						foreach ($get_split_code as $key => $value) {
							$key++;
							$IMPLODE = explode('.', $value['product_code']);
							$product_code = $IMPLODE[0] . '.' . $value['product_ke'];

							$sum_split = 0;
							$CUTTING = "<span class='text-red'><i>Tidak di cutting</i></span>";
							$get_id_header = $this->db->get_where('so_cutting_header', array('id_milik' => $value['id_milik'], 'id_bq' => str_replace('PRO-', 'BQ-', $value['id_produksi']), 'qty_ke' => $value['product_ke']))->result();
							if ($value['sts_cutting'] == 'Y') {
								$CUTTING = "<span class='text-red'><b>Belum Dicutting</b></span>";
								if (!empty($get_id_header) and $get_id_header[0]->app == 'Y') {
									$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header' => $get_id_header[0]->id))->result_array();
									if (!empty($result_cutting)) {

										$cuttingx = [];
										foreach ($result_cutting as $key2 => $value2) {
											$cuttingx[] = number_format($value2['length_split']);
											$sum_split += $value2['length_split'];
										}

										$CUTTING = "<span class='text-blue'><b>" . implode(" / ", $cuttingx) . "</b></span>";
									}
								}
							}

							$check = "";
							if ($value['fg_date'] != NULL && $value['lock_qc'] = 'Y' && $value['sts_cutting'] == 'Y') {
								$check = "<input type='checkbox' name='check[$key]' data-nomor='$key' class='chk_item' value='" . $value['id'] . "' >";
							}
							$flagQR = ($value['flag_qr'] != null && $value['flag_qr'] == 'Y') ? '<i class="text-success fa fa-check"></i>' : '-';
							echo "<tr>";
							echo "<td class='text-center'>" . $check . "</td>";
							echo "<td>" . strtoupper($value['id_category']) . "</td>";
							echo "<td>" . $spec . "</td>";
							echo "<td class='text-left'>" . $product_code . "</td>";
							echo "<td class='text-left'>" . $CUTTING . "</td>";
							echo "	<td>OKE</td>";
							echo "<td>" . $value['daycode'] . "</td>";
							echo "<td>" . $value['qc_pass_date'] . "</td>";
							echo "<td>" . $value['keterangan'] . "</td>";
							echo "<td>" . $value['resin'] . "</td>";
							echo "<td class='text-center'>" . $flagQR . "</td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div>
		</div>
		<style>
			.datepicker,
			#datepicker_qc_all {
				cursor: pointer;
			}
		</style>
		<script>
			$(document).ready(function() {
				swal.close();
				$('.chosen_select').chosen({
					width: '100%'
				});
				$('.autoNumeric').autoNumeric();
				$('.datetimepicker').datetimepicker();
				$('.datepicker, #datepicker_qc_all').datepicker({
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true
				});

				// $("#chk_all").click(function() {
				// 	$('.chk_item').not(this).prop('checked', this.checked);
				// });

				$("#datepicker_qc_all").change(function() {
					let date_qc_all = $(this).val()

					$(".datepicker").each(function() {
						if ($(this).val() == '') {
							$(this).val(date_qc_all)
						}
					});

				});
			});
=======
<?php
$start_time				= (!empty($get_spk)) ? $get_spk[0]->start_time : '';
$finish_time			= (!empty($get_spk)) ? $get_spk[0]->finish_time : '';
$cycletime				= (!empty($get_spk)) ? $get_spk[0]->cycletime : '';
$total_time				= (!empty($get_spk)) ? $get_spk[0]->total_time : '';
$productivity			= (!empty($get_spk)) ? $get_spk[0]->productivity : '';
$upload_spk				= (!empty($get_spk)) ? $get_spk[0]->upload_spk : '';
$next_process			= (!empty($get_spk)) ? $get_spk[0]->next_process : '';

// echo "<pre>";
// print_r($get_split_code);
// echo "</pre>";
?>
<input type='hidden' name='kode_spk' value='<?= $kode_spk; ?>'>
<input type='hidden' name='id_produksi' value='<?= $id_produksi; ?>'>
<input type='hidden' name='id_milik' value='<?= $id_milik; ?>'>
<input type='hidden' name='id_milik2' value='<?= $id_milik2; ?>'>
<input type='hidden' name='id_product' value='<?= $id_product; ?>'>
<input type='hidden' name='total_qty' value='<?= COUNT($get_split_code); ?>'>
<input type='hidden' name='first_id' value='<?= $first_id; ?>'>
<input type='hidden' name='time_uniq' value='<?= $time_uniq; ?>'>
<input type='hidden' name='id_spk' value='<?= $get_spk[0]->id; ?>'>
<!-- <?= $time_uniq; ?> / <?= $id_milik2; ?> -->

<div class="box box-info">
	<div class="box-body bg-info">
		<div class="row">
			<div class="col-md-3">
				<div class="form-horizontal">
					<label class="">Logo :</label>
					<div class="form-group row">
						<div class="col-sm-2">
							<div class="radio">
								<label>
									<input type="radio" name="logo" id="logo_ORI" value="ORI" checked="checked">
									ORI
								</label>
							</div>

							<div class="radio">
								<label>
									<input type="radio" name="logo" id="logo_NOV" value="NOV">
									NOV
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-horizontal">
					<label class="">Size :</label>
					<div class="form-group row">
						<div class="col-sm-2">
							<div class="radio">
								<label>
									<input type="radio" name="size" id="size_lg" value="lg" checked="checked">
									Large
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="size" id="size_md" value="md">
									Medium
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="size" id="size_sm" value="sm">
									Small
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box-footer bg-info">
		<button type="button" style="margin-bottom:10px" id="print_qrcode" class="btn btn-success"><i class="fa fa-print"></i> Print QR Code</button>
	</div>
</div>

<div class="box box-primary">
	<div class="box-body">
		<div class='form-group row'>
			<div class='col-sm-12'>
				<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
					<thead>
						<tr class='bg-blue'>
							<th class="text-center" width='3%'>
								<input type='checkbox' name='chk_all' id='chk_all'>
							</th>
							<th class="text-center" width='15%'>Product</th>
							<th class="text-center" width='10%'>Spec</th>
							<th class="text-center" width='12%'>Product Code</th>
							<th class="text-center" width='10%'>Cutting</th>
							<th class="text-center" width='6%'>Status</th>
							<th class="text-center" width='10%'>Daycode</th>
							<td class="text-center" width='10%'>QC Pass Date</td>
							<th class="text-center">Keterangan</th>
							<th class="text-center">Resin</th>
							<th class="text-center" width='50'>Flag QR</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$spec = spec_bq2($id_milik);
						foreach ($get_split_code as $key => $value) {
							$key++;
							$IMPLODE = explode('.', $value['product_code']);
							$product_code = $IMPLODE[0] . '.' . $value['product_ke'];

							$sum_split = 0;
							$CUTTING = "<span class='text-red'><i>Tidak di cutting</i></span>";
							$get_id_header = $this->db->get_where('so_cutting_header', array('id_milik' => $value['id_milik'], 'id_bq' => str_replace('PRO-', 'BQ-', $value['id_produksi']), 'qty_ke' => $value['product_ke']))->result();
							if ($value['sts_cutting'] == 'Y') {
								$CUTTING = "<span class='text-red'><b>Belum Dicutting</b></span>";
								if (!empty($get_id_header) and $get_id_header[0]->app == 'Y') {
									$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header' => $get_id_header[0]->id))->result_array();
									if (!empty($result_cutting)) {

										$cuttingx = [];
										foreach ($result_cutting as $key2 => $value2) {
											$cuttingx[] = number_format($value2['length_split']);
											$sum_split += $value2['length_split'];
										}

										$CUTTING = "<span class='text-blue'><b>" . implode(" / ", $cuttingx) . "</b></span>";
									}
								}
							}

							$check = "";
							if ($value['fg_date'] != NULL && $value['lock_qc'] == 'Y' && $value['sts_cutting'] == 'N') {
								$check = "<input type='checkbox' name='check[$key]' data-nomor='$key' class='chk_item' value='" . $value['id'] . "' >";
							}
							$flagQR = ($value['flag_qr'] != null && $value['flag_qr'] == 'Y') ? '<i class="text-success fa fa-check"></i>' : '-';
							echo "<tr>";
							echo "<td class='text-center'>" . $check . "</td>";
							echo "<td>" . strtoupper($value['id_category']) . "</td>";
							echo "<td>" . $spec . "</td>";
							echo "<td class='text-left'>" . $product_code . "</td>";
							echo "<td class='text-left'>" . $CUTTING . "</td>";
							echo "	<td>OKE</td>";
							echo "<td>" . $value['daycode'] . "</td>";
							echo "<td>" . $value['qc_pass_date'] . "</td>";
							echo "<td>" . $value['keterangan'] . "</td>";
							echo "<td>" . $value['resin'] . "</td>";
							echo "<td class='text-center'>" . $flagQR . "</td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div>
		</div>
		<style>
			.datepicker,
			#datepicker_qc_all {
				cursor: pointer;
			}
		</style>
		<script>
			$(document).ready(function() {
				swal.close();
				$('.chosen_select').chosen({
					width: '100%'
				});
				$('.autoNumeric').autoNumeric();
				$('.datetimepicker').datetimepicker();
				$('.datepicker, #datepicker_qc_all').datepicker({
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true
				});

				// $("#chk_all").click(function() {
				// 	$('.chk_item').not(this).prop('checked', this.checked);
				// });

				$("#datepicker_qc_all").change(function() {
					let date_qc_all = $(this).val()

					$(".datepicker").each(function() {
						if ($(this).val() == '') {
							$(this).val(date_qc_all)
						}
					});

				});
			});
>>>>>>> refs/remotes/origin/main
		</script>