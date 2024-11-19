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
<?= $time_uniq; ?> / <?= $id_milik2; ?>
<div class="box box-primary">
	<div class="box-body">
		<?php if ($get_split_code[0]['lock_qc'] == 'N') { ?>
			<div class='note'>
				<p>
					<strong>Info!</strong><br>
					Klik Start QC Terlebih dahulu untuk memulai Quality Control yang baru saja diinput produksi !!!
				</p>
			</div>
		<?php } ?>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Product</b></label>
			<div class='col-sm-10'>
				<?php
				echo form_textarea(array('id' => 'product_code', 'name' => 'product_code', 'rows' => 3, 'class' => 'form-control input-md', 'readonly' => 'true'), $kode_product);
				?>
			</div>
		</div>
		<div class='form-group row' hidden>
			<label class='label-control col-sm-2'><b>Start</b></label>
			<div class='col-sm-4'>
				<input type='text' id='start_time' name='start_time' class='form-control input-md datetimepicker' placeholder='Start Produksi' value='<?= $start_time; ?>'>
			</div>
			<label class='label-control col-sm-2'><b>Cycletime | Total Time</b></label>
			<div class='col-sm-2'>
				<input type='text' id='cycletime' name='cycletime' class='form-control input-md autoNumeric change_product' placeholder='Cycletime' value='<?= $cycletime; ?>'>
			</div>
			<div class='col-sm-2'>
				<input type='text' id='total_time' name='total_time' class='form-control input-md autoNumeric change_product' placeholder='Total Time' value='<?= $total_time; ?>'>
			</div>
		</div>
		<div class='form-group row' hidden>
			<label class='label-control col-sm-2'><b>Finish</b></label>
			<div class='col-sm-4'>
				<input type='text' id='finish_time' name='finish_time' class='form-control input-md datetimepicker' placeholder='Finish Produksi' value='<?= $finish_time; ?>'>
			</div>
			<label class='label-control col-sm-2'><b>Productivity</b></label>
			<div class='col-sm-4'>
				<input type='text' id='productivity' name='productivity' class='form-control input-md' placeholder='Productivity' readonly value='<?= $productivity; ?>'>
			</div>
		</div>
		<div class='form-group row' hidden>
			<label class='label-control col-sm-2'><b>Upload SPK</b></label>
			<div class='col-sm-4 text-right'>
				<input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload SPK'>
				<?php if (!empty($upload_spk)) { ?>
					<a href='#' target='_blank' title='Download' data-role='qtip'>Download</a>
				<?php } ?>
			</div>
			<label class='label-control col-sm-2'><b>Next Process</b></label>
			<div class='col-sm-4'>
				<select name='next_process' class='form-control input-md chosen_select'>
					<option value='0'>Select Next Process</option>
					<?php
					foreach ($costcenter as $key => $value) {
						$selc = ($next_process == $value['id_costcenter']) ? 'selected' : '';
						echo "<option value='" . $value['id_costcenter'] . "' " . $selc . ">" . strtoupper($value['nm_costcenter']) . "</option>";
					}
					?>
				</select>
			</div>
		</div>
		<div class='form-group row'>
			<div class='col-sm-6 text-left'>
				<?php if ($get_split_code[0]['lock_qc'] == 'Y') { ?>
					<button type='button' id='sendCheckRelease' class='btn btn-md btn-success'><b>Release To Finish Good</b></button>
				<?php } ?>
			</div>
			<div class='col-sm-6 text-right' style='float:right;'>
				<?php if ($get_split_code[0]['lock_qc'] == 'N') { ?>
					<button type='button' id='sendCheck' class='btn btn-md btn-success'><b>Start QC</b></button>
				<?php } ?>
			</div>
		</div>
		<div class='form-group row'>
			<div class='col-sm-12'>
				<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
					<thead>
						<tr class='bg-blue'>
							<th class="text-center" width='3%'><?php if ($get_split_code[0]['lock_qc'] == 'Y') { ?><input type='checkbox' name='chk_all' id='chk_all'><?php } ?></th>
							<th class="text-center" width='15%'>Product</th>
							<th class="text-center" width='10%'>Spec</th>
							<th class="text-center" width='12%'>Product Code</th>
							<th class="text-center" width='10%' hidden>Cutting</th>
							<th class="text-center" width='6%'>Status</th>
							<th class="text-center" width='10%'>Daycode</th>
							<td class="text-center" width='10%'><input type='text' name='datepicker_qc_all' id='datepicker_qc_all' class='form-control input-md text-center' placeholder='QC Pass Date' readonly></td>
							<th class="text-center">Keterangan</th>
							<th class="text-center" width='8%'>Resin</th>
							<th class="text-center" width='15%'>Upload Checksheet Inspeksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$spec = spec_bq2($id_milik);
						foreach ($get_split_code as $key => $value) {
							$key++;
							$IMPLODE = explode('.', $value['product_code']);
							$product_code = $IMPLODE[0] . '.' . $value['product_ke'];
							$download = '';
							if (!empty($value['inspeksi'])) {
								$download = "<a href='#' target='_blank' title='Download' data-role='qtip'>Download</a>";
							}

							$product_name = $value['id_category'];

							$sum_split = 0;
							$CUTTING = "<span class='text-red'><i>Tidak di cutting</i></span>";
							$get_id_header = $this->db->get_where('so_cutting_header', array('id_milik' => $value['id_milik'], 'id_bq' => str_replace('PRO-', 'BQ-', $value['id_produksi']), 'qty_ke' => $value['product_ke']))->result();
							if($kode_spk == 'deadstok'){
								$get_id_header = $this->db->get_where('so_cutting_header', array('id_deadstok' => $value['id_deadstok_dipakai'], 'id_bq' => str_replace('PRO-', 'BQ-', $value['id_produksi']), 'qty_ke' => $value['product_ke']))->result();
							}
							if ($value['sts_cutting'] == 'Y') {
								$CUTTING = "<span class='text-red'><b>Belum Dicutting</b></span>";
								if (!empty($get_id_header) and $get_id_header[0]->app == 'Y') {
									$ID_CUTTING = $get_id_header[0]->id;
									$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header' => $ID_CUTTING))->result_array();
									if (!empty($result_cutting)) {

										$cuttingx = [];
										foreach ($result_cutting as $key2 => $value2) {
											$cuttingx[] = number_format($value2['length_split']);
											$sum_split += $value2['length_split'];
										}

										$CUTTING = "<span class='text-blue'><b>" . implode(" / ", $cuttingx) . "</b></span>";
										$CUTTING .= "<input type='hidden' name='cut_header_".$key."' value='".$ID_CUTTING."'> ";
									}

									
                                    $product_name = $get_id_header[0]->id_category;
                                    $spec = (!empty($get_id_header[0]->id_deadstok))?'':$spec;

								}else{
//edit agus
									$CUTTING .= "<input type='hidden' name='cut_header_".$key."' value=''> ";
								}
							}else{
//edit agus
								$CUTTING .= "<input type='hidden' name='cut_header_".$key."' value=''> ";
							}

							$check = "";
							$title = '';
							if ($value['fg_date'] == NULL and ($value['lock_qc'] == 'Y' OR $value['kode_spk'] == 'deadstok')) {
								$check = "<input type='checkbox' name='check[$key]' data-nomor='$key' class='chk_personal' value='" . $value['id'] . "' >";
								if ($value['sts_cutting'] == 'Y') {
								$title = 'text-green';
								// 	$check = "<span class='text-red' title='Cutting terlebih dahulu !!!'><i class='fa fa-times'></i></span>";
								// 	if (!empty($get_id_header) and $get_id_header[0]->app == 'Y') {
								// 		$result_cutting	= $this->db->get_where('so_cutting_detail', array('id_header' => $get_id_header[0]->id))->result_array();
								// 		if (!empty($result_cutting)) {
								// 			$check = "<input type='checkbox' name='check[$key]' data-nomor='$key' class='chk_personal' value='" . $value['id'] . "' >";
								// 		}
								// 	}
								}
							}
							echo "<tr>";
							echo "<td class='text-center'>" . $check . "</td>";
							echo "<td class='".$title."'>" . strtoupper($product_name) . "</td>";
							echo "<td>" . $spec . "</td>";
							echo "<td class='text-left'>" . $product_code . "</td>";
							echo "<td class='text-left' hidden>" . $CUTTING . "</td>";
							echo "	<td>
												<select name='detail[$key][status]' class='form-control input-md chosen_select'>
													<option value='1'>OKE</option>
												</select>
											</td>";
							echo "<td><input type='text' name='detail[$key][daycode]' id='daycode_$key' class='form-control input-md' placeholder='Daycode' value='" . $value['daycode'] . "'></td>";
							echo "<td><input type='text' name='detail[$key][qc_pass_date]' id='qc_pass_date_$key' class='form-control input-md datepicker text-center' placeholder='QC Pass Date' readonly value='" . $value['qc_pass_date'] . "'></td>";
							echo "<td><input type='text' name='detail[$key][ket]' class='form-control input-md' placeholder='Keterangan' value='" . $value['keterangan'] . "'></td>";
							echo "<td><select name='detail[$key][resin]' class='form-control input-md chosen_select'>
											<option value=''>~ Select ~</option>
											<option value='1' " . (($value['resin'] == '1') ? 'selected' : '') . ">Ortho</option>
											<option value='2' " . (($value['resin'] == '2') ? 'selected' : '') . ">ISO</option>
											<option value='3' " . (($value['resin'] == '3') ? 'selected' : '') . ">Vinylester</option>
											<option value='4' " . (($value['resin'] == '4') ? 'selected' : '') . ">Novolac</option>
										</select>
									</td>";
							echo "<td class='text-right'>
											<input type='hidden' name='detail[$key][id]' class='form-control input-md' value='" . $value['id'] . "'>
											<input type='hidden' name='total_cutting_$key' class='form-control input-md' value='" . $sum_split . "'>
											<input type='file' name='inspeksi_" . $value['id'] . "' class='form-control input-md'>
											" . $download . "
											</td>";
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

				$("#chk_all").click(function() {
					$('.chk_personal').not(this).prop('checked', this.checked);
				});

				$("#datepicker_qc_all").change(function() {
					let date_qc_all = $(this).val()

					$(".datepicker").each(function() {
						if ($(this).val() == '') {
							$(this).val(date_qc_all)
						}
					});

				});
			});
		</script>