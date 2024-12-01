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
					echo form_input(array('type' => 'hidden', 'id' => 'kode_delivery', 'name' => 'kode_delivery', 'class' => 'form-control input-md'), $kode_delivery);
					echo form_input(array('id' => 'nomor_sj', 'name' => 'nomor_sj', 'class' => 'form-control input-md'), $header[0]->nomor_sj);

					?>
				</div>
				<label class='label-control col-sm-2'><b>Alamat <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_textarea(array('id' => 'alamat', 'name' => 'alamat', 'class' => 'form-control input-md', 'rows' => 3, 'placeholder' => 'Alamat Delivery'), $header[0]->alamat);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Delivery Date <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_input(array('id' => 'delivery_date', 'name' => 'delivery_date', 'class' => 'form-control input-md', 'readonly' => true), $header[0]->delivery_date);
					?>
				</div>
				<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					echo form_textarea(array('id' => 'project', 'name' => 'project', 'class' => 'form-control input-md', 'rows' => 3, 'placeholder' => 'Project'), $header[0]->project);
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Nomor SO </b></label>
				<div class='col-sm-4'>
					<select name="list_so[]" id="list_so" multiple class="form-control chosen-select">
						<option value='0'>PILIH SO</option>
						<?php
						foreach ($dataSO as $val => $valx) {
							$selected  = (in_array($valx->id_produksi, json_decode($header[0]->list_ipp))) ? 'selected' : '';
							echo "<option value='" . $valx->id_produksi . "' $selected>" . $valx->so_number . "</option>";
						}
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="box-footer">
			<button type="button" class="btn btn-success" id="updateDelivery"><i class="fa fa-save"></i> Save</button>
			<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-default"><i class="fa fa-reply"></i> Kembali</a>
		</div>
	</div>

	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">SCAN QRCODE</h3>
		</div>
		<div class="box-body">
			<div class="form-group row">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon" style="padding: 4px 10px 0px 10px;">
							<i class="fa fa-qrcode fa-3x"></i>
						</span>
						<input type="text" name="qr_code" id="qr_code" class="form-control input-lg" placeholder="QR Code">
					</div>
				</div>
				<div class="col-md-8">
					<span id="help-text" class="text-success text-bold text-lg"></span>
					<div class="notif">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="load-data">
		<?php if (!empty($result_print)) : ?>
			<div class="box box-primary">
				<div class="box-body">
					<h4>EDIT DESCRIPTION</h4>
					<table class="table table-sm table-bordered table-striped" width='100%' border='1' cellpadding='2'>
						<tr>
							<th class='text-center' width='3%'>#</th>
							<th class='text-center' width='8%'>QTY</th>
							<th class='text-center' width='5%'>UNIT</th>
							<th class='text-center' width='35%' style='vertical-align:middle;'>NAMA DAN UKURAN BARANG</th>
							<th class='text-center' style='vertical-align:middle;'>DESC</th>
						</tr>
						<?php
						$NOMOR = 0;
						foreach ($result_print as $val => $valx) {
							$val++;
							$NOMOR++;
							$series 	= get_name('so_detail_header', 'series', 'id', $valx['id_milik']);

							$ket_cut = '';
							if ($valx['sts'] == 'cut') {
								$getDetailCut = $this->db
												->get_where('delivery_product_detail a', array(
													'a.kode_delivery' => $kode_delivery, 
													'sts' => 'cut',
													'id_milik'=>$valx['id_milik']
												))->result_array();
								$LENGTH_CUT = "";
								foreach ($getDetailCut as $key => $value) {
									$LENGTH_CUT .= "/ ".number_format($value['length']);
								}

								$ket_cut = ' Cut '.$LENGTH_CUT;
							}

							$product 	= strtoupper($valx['product']) . ", " . $series . ", DIA " . spec_bq2($valx['id_milik']).$ket_cut;
							$SATUAN 	= 'PCS';
							$QTY 		= $valx['qty_product'];

							$ID_MILIK 	= (!empty($GET_ID_MILIK[$valx['id_milik']])) ? $GET_ID_MILIK[$valx['id_milik']] : '';
							$GET_DESC 	= (!empty($GET_DESC_DEAL[$ID_MILIK])) ? $GET_DESC_DEAL[$ID_MILIK] : '';
							$ID_UNIQ 	= $valx['id_milik'];
							if ($valx['sts_product'] == 'so material') {
								$product 	= strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['product']));
								$SATUAN 	= 'KG';
								$QTY 		= number_format($valx['berat'], 4);
								$ID_MILIK 	= '';
								$GET_DESC 	= '';
								$ID_UNIQ 	= $valx['id_uniq'];
							}

							if ($valx['sts'] == 'loose_dead') {
								$product 	= $valx['product'];
								$SATUAN 	= 'PCS';
								$QTY 		= $valx['qty_product'];
								$ID_MILIK 	= '';
								$GET_DESC 	= '';
								$ID_UNIQ 	= $valx['id_milik'];
							}

							if ($valx['type_product'] == 'tanki') {
								$spec = $tanki_model->get_spec($valx['id_milik']);

								$product 	= $valx['product_tanki'].', '.$spec;
								$SATUAN 	= 'PCS';
								$QTY 		= $valx['qty_product'];
								$ID_MILIK 	= '';
								$GET_DESC 	= '';
								$ID_UNIQ 	= $valx['id_milik'];
							}

							$DESC = (!empty($GET_DESC)) ? $GET_DESC : $valx['desc'];
							echo "<tr>";
							echo "<td align='center'>" . $NOMOR . "</td>";
							echo "<td align='center'>" . $QTY . "</td>";
							echo "<td align='center'>" . strtolower($SATUAN) . "</td>";
							echo "<td align='left'>" . $product . "</td>";
							echo "<td align='left'>";
							if ($valx['sts_product'] == 'so material') {
								echo "<input type='hidden' name='edit_desc_mat[$val][id_milik]' class='form-control' value='" . $ID_UNIQ . "'>";
								echo "<input type='text' name='edit_desc_mat[$val][desc]' class='form-control' value='" . $DESC . "'>";
							} else {
								echo "<input type='hidden' name='edit_desc[$val][id_milik]' class='form-control' value='" . $ID_UNIQ . "'>";
								echo "<input type='text' name='edit_desc[$val][desc]' class='form-control' value='" . $DESC . "'>";
							}
							echo "</td>";
							echo "</tr>";
						}
						?>
					</table>
					<div class="box-footer">
						<button type='button' class='btn btn-sm btn-primary' id='update_print'>Update Setting Print</button>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="box box-primary">
			<div class="box-header with-border ">
				<h3 class="box-title text-bold">List Produk</h3>
				<div class="box-tools text-right">
					<button type='button' class='btn btn-sm btn-danger' style='float:right; margin-bottom:10px;' id='delete_spool'><i class='fa fa-times'></i>&nbsp;Delete Delivery</button>
				</div>
			</div>
			<div class="box-body">
				<?php if (!empty($result)) { ?>
					<h4>LOOSE</h4>
					<table id="loose" class="table table-sm table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">IPP</th>
								<th class="text-center">Product</th>
								<th class="text-center">Spec</th>
								<th class="text-center">ID Product</th>
								<th class="text-center">No SPK</th>
								<th class="text-center">Status</th>
								<th class="text-center no-sort">#</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $key => $value) {
								$key++;
								$CUTTING_KE = (!empty($value['cutting_ke'])) ? '.' . $value['cutting_ke'] : '';

								$IMPLODE = explode('.', $value['product_code']);
								$product_code = $IMPLODE[0] . '.' . $value['product_ke'] . $CUTTING_KE;
								if ($value['sts_product'] == 'so material') {
									$product_code = '';
								}

								$PRODUCT = strtoupper($value['product']);
								if ($value['sts_product'] == 'so material') {
									$PRODUCT = strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
								}

								$SPEC = spec_bq3($value['id_milik']);
								if ($value['sts_product'] == 'so material') {
									$SPEC = number_format($value['berat'], 2) . ' kg';
								}

								if ($value['sts'] == 'cut') {
									$SPEC .= " x " . number_format($value['length']);
								}
								if ($value['sts_product'] == 'cut deadstock') {
									$SPEC = number_format($value['length']);
									$product_code = $value['product_code'];
								}
								echo "<tr>";
								echo "<td align='center'>" . $key . "</td>";
								echo "<td align='center'>" . str_replace('PRO-', '', $value['id_produksi']) . "</td>";
								echo "<td align='left'>" . $PRODUCT . "</td>";
								echo "<td align='left'>" . $SPEC . "</td>";
								echo "<td align='center'>" . $product_code . "</td>";
								echo "<td align='center'>" . $value['no_spk'] . "</td>";
								echo "<td align='center'>" . $value['sts'] . "</td>";
								if ($value['sts'] == 'loose') {
									echo "<td align='center'><input type='checkbox' name='check[" . $value['id_uniq'] . "]' class='chk_personal' value='" . $value['id_uniq'] . "&" . $value['id'] . "' ></td>";
								}
								if ($value['sts'] == 'cut') {
									echo "<td align='center'><input type='checkbox' name='check_cut[" . $value['id_uniq'] . "]' class='chk_personal' value='" . $value['id_uniq'] . "&" . $value['id'] . "' ></td>";
								}
								if ($value['sts_product'] == 'so material') {
									echo "<td align='center'></td>";
								}
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				<?php } ?>

				<?php if (!empty($result2)) { ?>
					<h4>SPOOL</h4>
					<table class="table table-sm table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">Kode</th>
								<th class="text-center">Product</th>
								<th class="text-center">Spec</th>
								<th class="text-center">ID Product</th>
								<th class="text-center">No SPK</th>
								<th class="text-center no-sort">#</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result2 as $key => $value) {
								$key++;

								$IMPLODE = explode('.', $value['product_code']);
								$product_code = $IMPLODE[0] . '.' . $value['product_ke'];

								$get_split_ipp = $this->db
													->select('a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
													->join('production_detail b','a.id_pro=b.id','left')
													->get_where('delivery_product_detail a',
														array(
															'a.kode_delivery' => $value['kode_delivery'], 
															'a.kode_spool' => $value['kode_spool'], 
															'a.spool_induk' => $value['spool_induk']
															)
														)->result_array();
								$ArrNo_Spool = [];
								$ArrNo_IPP = [];
								$ArrNo_SPK = [];
								$ArrNo_ID = [];
								foreach ($get_split_ipp as $key2 => $value2) {
									$key2++;
									$LENGTH = '';
									if ($value2['product'] == 'pipe') {
										$no_spk_list = $this->db->select('length')->get_where('so_detail_header', array('id' => $value2['id_milik']))->result();
										$LENGTH = ($value2['sts'] == 'cut') ? number_format($value2['length']) : number_format($no_spk_list[0]->length);
									}

									$nm_product = ($value2['type_product'] == 'tanki')?$value2['product_tanki']:$value2['product'];
									$spec = ($value2['type_product'] == 'tanki')?$tanki_model->get_spec($value2['id_milik']):spec_bq2($value2['id_milik']);

									$ArrNo_IPP[] = $key2 . '. ' . strtoupper($nm_product . ' ' . $LENGTH);

									if($value2['sts'] == 'loose_dead'){
										$ArrNo_Spool[] = $key2 . '. ' . strtoupper($value2['kode_spk']);
									}
									else{
										$ArrNo_Spool[] = $key2 . '. ' . strtoupper($spec);
									}

									$CUTTING_KE = (!empty($value2['cutting_ke'])) ? '.' . $value2['cutting_ke'] : '';

									$IMPLODE = explode('.', $value2['product_code']);
									$ArrNo_SPK[] = $key2 . '. ' . $value2['no_spk'];
									$ArrNo_ID[] = $key2 . '. ' . $IMPLODE[0] . '.' . $value2['product_ke'] . $CUTTING_KE;
								}
								// print_r($ArrGroup); exit;
								$explode_spo = implode('<br>', $ArrNo_Spool);
								$explode_ipp = implode('<br>', $ArrNo_IPP);
								$explode_spk = implode('<br>', $ArrNo_SPK);
								$explode_id = implode('<br>', $ArrNo_ID);

								echo "<tr>";
								echo "<td align='center'>" . $key . "</td>";
								echo "<td align='left'>" . $value['spool_induk'] . "-" . $value['kode_spool'] . "<br>" . $value['no_drawing'] . "</td>";
								echo "<td align='left'>" . $explode_ipp . "</td>";
								echo "<td align='left'>" . $explode_spo . "</td>";
								echo "<td align='left'>" . $explode_id . "</td>";
								echo "<td align='left'>" . $explode_spk . "</td>";
								echo "<td align='center'><input type='checkbox' name='check2[" . $value['id_uniq'] . "]' class='chk_personal' value='" . $value['spool_induk'] . "&" . $value['kode_spool'] . "' ></td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				<?php } ?>

				<?php if (!empty($result3)) { ?>
					<h4>SO MATERIAL</h4>
					<table class="table table-sm table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">IPP</th>
								<th class="text-center">Material Name</th>
								<th class="text-center">Berat (kg)</th>
								<th class="text-center no-sort">#</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result3 as $key => $value) {
								$key++;
								$PRODUCT = strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
								$SPEC = number_format($value['berat'], 2);
								echo "<tr>";
								echo "<td align='center'>" . $key . "</td>";
								echo "<td align='center'>" . str_replace('PRO-', '', $value['id_produksi']) . "</td>";
								echo "<td align='left'>" . $PRODUCT . "</td>";
								echo "<td align='center'>" . $SPEC . "</td>";
								echo "<td align='center'><input type='checkbox' name='check3[" . $value['id_uniq'] . "]' class='chk_personal' value='" . $value['id_uniq'] . "&" . $value['id'] . "'></td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				<?php } ?>

				<?php if (!empty($result4)) { ?>
					<h4>MATERIAL FIELD JOINT</h4>
					<table class="table table-sm table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">IPP</th>
								<th class="text-center">Material Name</th>
								<th class="text-center">Berat (kg)</th>
								<th class="text-center no-sort">#</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result4 as $key => $value) {
								$key++;
								$PRODUCT = strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $value['product']));
								$SPEC = number_format($value['berat'], 2);
								echo "<tr>";
								echo "<td align='center'>" . $key . "</td>";
								echo "<td align='center'>" . str_replace('PRO-', '', $value['id_produksi']) . "</td>";
								echo "<td align='left'>" . $PRODUCT . "</td>";
								echo "<td align='center'>" . $SPEC . "</td>";
								echo "<td align='center'><input type='checkbox' name='check4[" . $value['id_uniq'] . "]' class='chk_personal' value='" . $value['id_uniq'] . "&" . $value['id'] . "'></td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				<?php } ?>

				<?php if (!empty($result5)) { ?>
					<h4>DEADSTOK</h4>
					<table class="table table-sm table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">Product</th>
								<th class="text-center">Spec</th>
								<th class="text-center">From</th>
								<th class="text-center no-sort">#</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result5 as $key => $value) {
								$fromDeadstok = ($value['sts'] == 'loose_dead')?'Deadstok':'Deadstok Modifikasi';
								$key++;
								echo "<tr>";
								echo "<td align='center'>" . $key . "</td>";
								echo "<td align='center'>" . $value['product'] . "</td>";
								echo "<td align='center'>" . $value['kode_spk'].'x'.$value['length'] . "</td>";
								echo "<td align='center'>" . $fromDeadstok . "</td>";
								echo "<td align='center'><input type='checkbox' name='check5[" . $value['id_uniq'] . "]' class='chk_personal' value='" . $value['id_uniq'] . "&" . $value['id'] . "&" . $value['sts'] . "'></td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				<?php } ?>

				<?php if (!empty($result6)) { ?>
					<h4>AKSESORIS</h4>
					<table class="table table-sm table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">Aksesoris Name</th>
								<th class="text-center">Qty</th>
								<th class="text-center no-sort">#</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result6 as $key => $value) {
								$key++;
								echo "<tr>";
								echo "<td align='center'>".$key."</td>";
								echo "<td align='left'>".$value['no_drawing']."</td>";
								echo "<td align='center'>".number_format($value['berat'],2)."</td>";
								echo "<td align='center'><input type='checkbox' name='check6[".$value['id']."]' class='chk_personal' value='".$value['id_uniq']."&".$value['id']."'></td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				<?php } ?>
			</div>
			<!-- /.box-body -->
		</div>

	</div>
	<!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function() {
		setTimeout(() => {
			$("#qr_code").focus();
			$('#help-text').html('<i>Ready to Scan QR...!!</i>')
		}, 500)

		$(document).on('focus', '#qr_code', function() {
			$('#help-text').html('<i>Ready to Scan QR...!!</i>')
		})
		$(document).on('blur', '#qr_code', function() {
			$('#help-text').html('')
		})

		$('#delivery_date').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});

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

		$(document).on('click', '#update_print', function() {
			var nomor_sj = $('#nomor_sj').val()
			var delivery_date = $('#delivery_date').val()
			var project = $('#project').val()
			var alamat = $('#alamat').val()
			var list_so = $('#list_so').val()

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
			// if (list_so == '') {
			// 	swal({
			// 		title: "Error Message!",
			// 		text: 'SO is empty, select first ...',
			// 		type: "warning"
			// 	});
			// 	return false;
			// }

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
							url: base_url + active_controller + '/update_print',
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
									window.reload();
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
				}
			);
		});

		$(document).on('keypress', '#qr_code', function(e) {
			const input = $(this)
			if (e.keyCode == '13') {
				var formData = new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url: base_url + active_controller + '/save_detail_delivery',
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
								text: data.pesan,
								type: "success",
								timer: 3000
							});

							console.log(data);
							$('#load-data').load(base_url + active_controller + '/loadDataSS/' + data.kode_delivery)
							// loadData(type = data.data[0].sts, kode_delivery = data.data[0].kode_delivery)
							$('.notif').fadeIn('slow').html(`
								<div class="alert alert-info" role="alert">
								<h4 class="alert-heading">Scan Berhasil!</h4>
								<p>` + input.val() + `</p>
								</div>
								`)

							input.val('').focus();
							setTimeout(function() {
								$('.notif').fadeToggle('slow')
							}, 7000)
							// window.location.href = base_url + active_controller;
						} else {
							swal({
								title: "Failed!",
								text: data.pesan,
								type: "warning",
								timer: 3000
							});

							$('.notif').fadeIn('slow').html(`
								<div class="alert alert-warning" role="alert">
								<h4 class="alert-heading">Scan Gagal!</h4>
								<p>` + data.pesan + `</p>
								</div>
								`)

							input.val('').focus();
							setTimeout(function() {
								$('.notif').fadeToggle('slow')
							}, 7000)
						}
					},
					error: function() {
						swal({
							title: "Error Message !",
							text: 'An Error Occured During Process. Please try again..',
							type: "error",
							timer: 3000
						});
					}
				});
			}
		})

		$(document).on('click', '#updateDelivery', function() {
			var nomor_sj = $('#nomor_sj').val()
			var delivery_date = $('#delivery_date').val()
			var project = $('#project').val()
			var alamat = $('#alamat').val()
			var list_so = $('#list_so').val()
			// alert(list_so)
			console.log();
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

			if (list_so == '') {
				swal({
					title: "Error Message!",
					text: 'SO is empty, select first ...',
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
							url: base_url + active_controller + '/updateDelivery',
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
									location.reload();
									// $('#load-data').load(base_url + active_controller + '/loadDataSS/' + data.kode_delivery)
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
</script>