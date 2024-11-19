<style>
	* {
		font-family: 'Century Gothic';
		margin: 0;
		padding: 0;
	}

	.wrapper {
		position: relative;
		/* width: 365px; */
		/* border: 1px solid; */
		/* height: 120px; */
		/* padding: 2px; */
		/* margin: 5px; */
		display: inline-block;
	}

	.content-wrapper {
		display: flex;
		position: relative;
		flex-direction: row;
		flex-wrap: wrap;
		justify-content: flex-start;
		height: inherit;
	}

	/* 
	img {
		height: 100%;
	} */

	.title-name {
		margin: 0px;
		padding-top: 5px;
		font-size: 18px;
	}

	.d-flex {
		display: flex;
	}

	.d-flex.justify-content-start {
		flex: content;
		flex-direction: row;
		/* flex: column; */
	}

	table {
		border-collapse: collapse;
		border: 2px solid
	}

	table td {
		border: 2px solid
	}

	table.table-sm {
		border-collapse: collapse;
		border: 1px solid
	}

	table.table-sm td {
		border: 1px solid
	}
</style>

<body>
	<?php
	foreach ($explode as $keys => $code) :
		$NOMOR_SO 	= explode('-', $products[$code]->product_code);
		$spec 		= spec_bq2($products[$code]->id_milik);
	?>
		<?php if ($size == 'lg') : ?>
			<div class="wrapper" style="width:120mm;height:49mm;margin-bottom:10px;position:relative;">
				<table width="100%">
					<thead>
						<!-- Top -->
						<tr>
							<td style="width: 40mm;overflow:hidden;text-align: center;padding:2px;height:22mm;">
								<?php if ($this->uri->segment(4) == 'ORI') : ?>
									<img src="<?= $logo; ?>" width="95%">
								<?php else : ?>
									<img src="<?= $logo; ?>" width="90%">
								<?php endif; ?>
							</td>
							<td>
								<div style="font-size: 12px;padding:3px;">
									<p><?= "CUST : " . $products[$code]->nm_customer; ?></p>
									<?php $Ipp = explode("-", $products[$code]->id_produksi)[1]; ?>
									<p><?= "PROJ : " . $ITEMS[$Ipp]->project; ?></p>
									<!-- <h5 class="fw-bold" style="font-size: 9px;"><?= strtoupper($products[$code]->id_category); ?></h5> -->
									<div>
										<?php $STD = [
											($ArrProd[$Ipp]->std_asme == 'N') ? '' : 'ASME',
											($ArrProd[$Ipp]->std_ansi == 'N') ? '' : 'ANSI',
											($ArrProd[$Ipp]->std_astm == 'N') ? '' : 'ASTM',
											($ArrProd[$Ipp]->std_awwa == 'N') ? '' : 'AWWA',
											($ArrProd[$Ipp]->std_bsi == 'N') ? '' : 'BSI',
											($ArrProd[$Ipp]->std_jis == 'N') ? '' : 'JIS',
											($ArrProd[$Ipp]->std_sni == 'N') ? '' : 'SNI',
											($ArrProd[$Ipp]->std_etc == 'N') ? '' : 'ETC',
										]; ?> DN <?= $DN[$products[$code]->id_milik]->diameter_1 . " " . strtoupper($products[$code]->id_category) . " " . $products[$code]->resin . " " . ((implode($STD)) ? implode(", ", $STD) : '') . " " . substr($DN[$products[$code]->id_milik]->series, 1, 4); ?>
									</div>
									<span><?= strtoupper($products[$code]->daycode); ?></span>
								</div>
							</td>
						</tr>

						<!-- Middle -->
						<tr>
							<td rowspan="2" style="text-align: center;overflow:hidden">
								<img src="<?= base_url('assets/qrcode/images/' . $code . '.png'); ?>" width="60%;display:inline">
							</td>
							<td style="padding:3px;height:16mm;">
								<input placeholder="-" style="font-size: 12px;width: 100%;border:none;" maxlength="26"></input>
								<input placeholder="-" style="font-size: 12px;width: 100%;border:none;" maxlength="26"></input>
								<input placeholder="-" style="font-size: 12px;width: 100%;border:none;" maxlength="26"></input>
							</td>
						</tr>
						<tr>
							<td style="font-size: 12px;padding:3px;height:11mm;">
								<p style=""><?= $this->session->ORI_User['nm_lengkap']; ?></p>
								HANDLE WITH CARE
							</td>
						</tr>
					</thead>
				</table>
			</div>
		<?php elseif ($size == 'md') : ?>
			<div class="wrapper" style="width:88mm;height:36mm;margin-bottom:10px;position:relative;">
				<table width="100%">
					<thead>
						<!-- Top -->
						<tr>
							<td style="width: 30mm;overflow:hidden;text-align: center;padding:2px;height:16mm">
								<?php if ($this->uri->segment(4) == 'ORI') : ?>
									<img src="<?= $logo; ?>" width="95%">
								<?php else : ?>
									<img src="<?= $logo; ?>" width="90%">
								<?php endif; ?>
							</td>
							<td>
								<div style="font-size: 8px;padding-left:2px;">
									<p><?= "CUST : " . $products[$code]->nm_customer; ?></p>
									<?php $Ipp = explode("-", $products[$code]->id_produksi)[1]; ?>
									<p><?= "PROJ : " . $ITEMS[$Ipp]->project; ?></p>
									<!-- <h5 class="fw-bold" style="font-size: 9px;"><?= strtoupper($products[$code]->id_category); ?></h5> -->
									<div>
										<?php $STD = [
											($ArrProd[$Ipp]->std_asme == 'N') ? '' : 'ASME',
											($ArrProd[$Ipp]->std_ansi == 'N') ? '' : 'ANSI',
											($ArrProd[$Ipp]->std_astm == 'N') ? '' : 'ASTM',
											($ArrProd[$Ipp]->std_awwa == 'N') ? '' : 'AWWA',
											($ArrProd[$Ipp]->std_bsi == 'N') ? '' : 'BSI',
											($ArrProd[$Ipp]->std_jis == 'N') ? '' : 'JIS',
											($ArrProd[$Ipp]->std_sni == 'N') ? '' : 'SNI',
											($ArrProd[$Ipp]->std_etc == 'N') ? '' : 'ETC',
										]; ?> DN <?= $DN[$products[$code]->id_milik]->diameter_1 . " " . strtoupper($products[$code]->id_category) . " " . $products[$code]->resin . " " . ((implode($STD)) ? implode(", ", $STD) : '') . " " . substr($DN[$products[$code]->id_milik]->series, 1, 4); ?>
									</div>
									<span><?= strtoupper($products[$code]->daycode); ?></span>
								</div>
							</td>
						</tr>

						<!-- Middle -->
						<tr>
							<td rowspan="2" style="text-align: center;overflow:hidden;">
								<img src="<?= base_url('assets/qrcode/images/' . $code . '.png'); ?>" width="60%;display:inline">
							</td>
							<td style="padding-left:2px;height:12mm">
								<input placeholder="-" style="font-size: 8px;width: 100%;border:none" maxlength="26"></input>
								<input placeholder="-" style="font-size: 8px;width: 100%;border:none" maxlength="26"></input>
								<input placeholder="-" style="font-size: 8px;width: 100%;border:none" maxlength="26"></input>
							</td>
						</tr>
						<tr>
							<td style="font-size: 8px;padding-left:2px;height:8mm">
								<p style=""><?= $this->session->ORI_User['nm_lengkap']; ?></p>
								HANDLE WITH CARE
							</td>
						</tr>
					</thead>
				</table>
			</div>
		<?php elseif ($size == 'sm') : ?>
			<div class="wrapper" style="width:44mm;height:18mm;margin-bottom:8px;position:relative;">
				<table width="100%" class="table-sm">
					<thead>
						<!-- Top -->
						<tr>
							<td style="width: 15mm;overflow:hidden;text-align: center;padding:1px;height:8mm">
								<?php if ($this->uri->segment(4) == 'ORI') : ?>
									<img src="<?= $logo; ?>" width="90%">
								<?php else : ?>
									<img src="<?= $logo; ?>" width="75%">
								<?php endif; ?>
							</td>
							<td>
								<div style="font-size: 4px;padding-left:2px;">
									<p><?= "CUST : " . $products[$code]->nm_customer; ?></p>
									<?php $Ipp = explode("-", $products[$code]->id_produksi)[1]; ?>
									<p><?= "PROJ : " . $ITEMS[$Ipp]->project; ?></p>
									<!-- <h5 class="fw-bold" style="font-size: 5px;"><?= strtoupper($products[$code]->id_category); ?></h5> -->
									<div>
										<?php $STD = [
											($ArrProd[$Ipp]->std_asme == 'N') ? '' : 'ASME',
											($ArrProd[$Ipp]->std_ansi == 'N') ? '' : 'ANSI',
											($ArrProd[$Ipp]->std_astm == 'N') ? '' : 'ASTM',
											($ArrProd[$Ipp]->std_awwa == 'N') ? '' : 'AWWA',
											($ArrProd[$Ipp]->std_bsi == 'N') ? '' : 'BSI',
											($ArrProd[$Ipp]->std_jis == 'N') ? '' : 'JIS',
											($ArrProd[$Ipp]->std_sni == 'N') ? '' : 'SNI',
											($ArrProd[$Ipp]->std_etc == 'N') ? '' : 'ETC',
										]; ?> DN <?= $DN[$products[$code]->id_milik]->diameter_1 . " " . strtoupper($products[$code]->id_category) . " " . $products[$code]->resin . " " . ((implode($STD)) ? implode(", ", $STD) : '') . " " . substr($DN[$products[$code]->id_milik]->series, 1, 4); ?>
									</div>
									<span><?= strtoupper($products[$code]->daycode); ?></span>
								</div>
							</td>
						</tr>

						<!-- Middle -->
						<tr>
							<td rowspan="2" style="text-align: center;overflow:hidden;">
								<img src="<?= base_url('assets/qrcode/images/' . $code . '.png'); ?>" width="60%;display:inline">
							</td>
							<td style="padding-left:2px;height:6mm">
								<input placeholder="-" style="font-size: 5px;width: 100%;border:none" maxlength="26"></input>
								<input placeholder="-" style="font-size: 5px;width: 100%;border:none" maxlength="26"></input>
								<input placeholder="-" style="font-size: 5px;width: 100%;border:none" maxlength="26"></input>
							</td>
						</tr>

						<tr>
							<td style="font-size: 4px;padding-left:2px;height:4mm">
								<p style=""><?= $this->session->ORI_User['nm_lengkap']; ?></p>
								HANDLE WITH CARE
							</td>
						</tr>
					</thead>
				</table>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</body>
<script>
	// window.print();
	// window.onmousemove = function() {
	// 	setTimeout(function() {
	// 		window.close();
	// 	}, 300)
	// }
</script>