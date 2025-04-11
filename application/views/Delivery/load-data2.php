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

					$DESC = (!empty($valx['desc'])) ? $valx['desc'] : $GET_DESC;
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
		<h3 class="box-title text-bold">List Item</h3>
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
						<th class="text-center no-sort">Status</th>
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
	</div>
	<!-- /.box-body -->
</div>