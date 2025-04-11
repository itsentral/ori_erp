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
	$Ipp = explode("-", $products->id_bq)[1];
	$STD = [
		($ArrProd[$Ipp]->std_asme == 'N') ? '' : 'ASME',
		($ArrProd[$Ipp]->std_ansi == 'N') ? '' : 'ANSI',
		($ArrProd[$Ipp]->std_astm == 'N') ? '' : 'ASTM',
		($ArrProd[$Ipp]->std_awwa == 'N') ? '' : 'AWWA',
		($ArrProd[$Ipp]->std_bsi == 'N') ? '' : 'BSI',
		($ArrProd[$Ipp]->std_jis == 'N') ? '' : 'JIS',
		($ArrProd[$Ipp]->std_sni == 'N') ? '' : 'SNI',
		($ArrProd[$Ipp]->std_etc == 'N') ? '' : 'ETC',
	];; ?>

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
							<div style="font-size: 12px;padding:3px;color:blue;">
								<p><?= "CUST : " . $products->nm_customer; ?></p>
								<?php $Ipp = explode("-", $products->id_bq)[1]; ?>
								<p><?= "PROJ : " . $proj->project; ?></p>
								<div>
									DN
									<?= $DN[$products->id_milik]->diameter_1 . " " . strtoupper($products->id_category) . " " . ((implode($STD)) ? implode(", ", $STD) : '') . " " . substr($DN[$products->id_milik]->series, 1, 4); ?>
								</div>
								<span><?= strtoupper($products->sp_daycode); ?></span>
							</div>
						</td>
					</tr>

					<!-- Middle -->
					<tr>
						<td rowspan="2" style="text-align: center;overflow:hidden">
							<img src="<?= base_url('assets/qrcode/images/' . "cut-" . $products->id . '.png'); ?>" width="60%;display:inline">
						</td>
						<td style="padding:3px;height:16mm;">
							<input placeholder="-" style="font-size: 12px;width: 100%;border:none;color:red;" maxlength="26"></input>
							<input placeholder="-" style="font-size: 12px;width: 100%;border:none;color:red;" maxlength="26"></input>
							<input placeholder="-" style="font-size: 12px;width: 100%;border:none;color:red;" maxlength="26"></input>
						</td>
					</tr>
					<tr>
						<td style="font-size: 12px;padding:3px;height:11mm;">
							<p style="color:blue;"><?= $this->session->ORI_User['nm_lengkap']; ?></p>
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
							<div style="font-size: 8px;padding-left:2px;color:blue;">
								<p><?= "CUST : " . $products->nm_customer; ?></p>
								<p><?= "PROJ : " . $proj->project; ?></p>
								<div>
									DN
									<?= $DN[$products->id_milik]->diameter_1 . " " . strtoupper($products->id_category) . " " . ((implode($STD)) ? implode(", ", $STD) : '') . " " . substr($DN[$products->id_milik]->series, 1, 4); ?>
								</div>
								<span><?= strtoupper($products->sp_daycode); ?></span>
							</div>
						</td>
					</tr>

					<!-- Middle -->
					<tr>
						<td rowspan="2" style="text-align: center;overflow:hidden;">
							<img src="<?= base_url('assets/qrcode/images/' . $products->spool_induk . '.png'); ?>" width="60%;display:inline">
						</td>
						<td style="padding-left:2px;height:12mm">
							<input placeholder="-" style="color:red;font-size: 8px;width: 100%;border:none" maxlength="26"></input>
							<input placeholder="-" style="color:red;font-size: 8px;width: 100%;border:none" maxlength="26"></input>
							<input placeholder="-" style="color:red;font-size: 8px;width: 100%;border:none" maxlength="26"></input>
						</td>
					</tr>
					<tr>
						<td style="font-size: 8px;padding-left:2px;height:8mm">
							<p style="color:blue;"><?= $this->session->ORI_User['nm_lengkap']; ?></p>
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
							<div style="font-size: 4px;padding-left:2px;color:blue;">
								<p><?= "CUST : " . $products->nm_customer; ?></p>
								<?php $Ipp = explode("-", $products->id_bq)[1]; ?>
								<p><?= "PROJ : " . $proj->project; ?></p>
								<div>
									DN
									<?= $DN[$products->id_milik]->diameter_1 . " " . strtoupper($products->id_category) . " " . ((implode($STD)) ? implode(", ", $STD) : '') . " " . substr($DN[$products->id_milik]->series, 1, 4); ?>
								</div>
								<span><?= strtoupper($products->sp_daycode); ?></span>
							</div>
						</td>
					</tr>

					<!-- Middle -->
					<tr>
						<td rowspan="2" style="text-align: center;overflow:hidden;">
							<img src="<?= base_url('assets/qrcode/images/' . "cut-" . $products->id . '.png'); ?>" width="60%;display:inline">
						</td>
						<td style="padding-left:2px;height:6mm">
							<input placeholder="-" style="color:red;font-size: 5px;width: 100%;border:none" maxlength="26"></input>
							<input placeholder="-" style="color:red;font-size: 5px;width: 100%;border:none" maxlength="26"></input>
							<input placeholder="-" style="color:red;font-size: 5px;width: 100%;border:none" maxlength="26"></input>
						</td>
					</tr>

					<tr>
						<td style="font-size: 4px;padding-left:2px;height:4mm">
							<p style="color:blue;"><?= $this->session->ORI_User['nm_lengkap']; ?></p>
							HANDLE WITH CARE
						</td>
					</tr>
				</thead>
			</table>
		</div>
	<?php endif; ?>

</body>
<script>
	// window.print();
	// window.onmousemove = function() {
	//     setTimeout(function() {
	//         window.close();
	//     }, 300)
	// }
</script>