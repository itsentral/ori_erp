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
	$spec 		= spec_bq2($spools->id_milik);
	$NOMOR_SO 	= explode('-', $spools->product_code);
	$Ipp 		= explode("-", $spools->id_produksi)[1];

	$std_asme = (!empty($ArrProd[$Ipp]->std_asme))?$ArrProd[$Ipp]->std_asme:'x';
	$std_ansi = (!empty($ArrProd[$Ipp]->std_ansi))?$ArrProd[$Ipp]->std_ansi:'x';
	$std_astm = (!empty($ArrProd[$Ipp]->std_astm))?$ArrProd[$Ipp]->std_astm:'x';
	$std_awwa = (!empty($ArrProd[$Ipp]->std_awwa))?$ArrProd[$Ipp]->std_awwa:'x';
	$std_bsi = (!empty($ArrProd[$Ipp]->std_bsi))?$ArrProd[$Ipp]->std_bsi:'x';
	$std_jis = (!empty($ArrProd[$Ipp]->std_jis))?$ArrProd[$Ipp]->std_jis:'x';
	$std_sni = (!empty($ArrProd[$Ipp]->std_sni))?$ArrProd[$Ipp]->std_sni:'x';
	$std_etc = (!empty($ArrProd[$Ipp]->std_etc))?$ArrProd[$Ipp]->std_etc:'x';

	$project = (!empty($proj->project))?$proj->project:'';
	$nm_customer = (!empty($spools->nm_customer))?$spools->nm_customer:'';

	$tandaTanki = substr($Ipp,0,4);
	// echo $tandaTanki; exit;
	if($tandaTanki == 'IPPT'){
		$GETTANKI 		= $tanki_model->get_ipp_detail($Ipp);
		$project 		= (!empty($GETTANKI['nm_project']))?$GETTANKI['nm_project']:'';
		$nm_customer 	= (!empty($GETTANKI['customer']))?$GETTANKI['customer']:'';
	}

	$STD = [
		($std_asme == 'N') ? '' : 'ASME',
		($std_ansi == 'N') ? '' : 'ANSI',
		($std_astm == 'N') ? '' : 'ASTM',
		($std_awwa == 'N') ? '' : 'AWWA',
		($std_bsi == 'N') ? '' : 'BSI',
		($std_jis == 'N') ? '' : 'JIS',
		($std_sni == 'N') ? '' : 'SNI',
		($std_etc == 'N') ? '' : 'ETC',
	]; ?>

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
								<p><?= "CUST : " . $nm_customer; ?></p>
								<?php $Ipp = explode("-", $spools->id_produksi)[1]; ?>
								<p><?= "PROJ : " . $project; ?></p>
							</div>
						</td>
					</tr>

					<!-- Middle -->
					<tr>
						<td rowspan="2" style="text-align: center;overflow:hidden">
							<img src="<?= base_url('assets/qrcode/images/' . $spools->spool_induk . '.png'); ?>" width="60%;display:inline">
						</td>
						<td style="padding:3px;height:16mm;font-size: 11px;">
							<p>DRAWING NO : <?= $spools->no_drawing; ?></p>
							<!-- <input placeholder="-" style="dislpay:inline-block;font-size: 12px;border:none;width:70%" rows="1" maxlength="26"><br> -->
							<p>SPOOL NO : <?= isset($dycode->sp_group_daycode)?strtoupper($dycode->sp_group_daycode):'-'; ?></p>
							<!-- <input placeholder="-" style="dislpay:inline-block;font-size: 12px;border:none;width:75%" maxlength="26"> -->
							<!-- <input placeholder="-" style="font-size: 12px;width: 100%;border:none;" maxlength="26"></input> -->
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
								<p><?= "CUST : " . $nm_customer; ?></p>
								<?php $Ipp = explode("-", $spools->id_produksi)[1]; ?>
								<p><?= "PROJ : " . $project; ?></p>
							</div>
						</td>
					</tr>

					<!-- Middle -->
					<tr>
						<td rowspan="2" style="text-align: center;overflow:hidden;">
							<img src="<?= base_url('assets/qrcode/images/' . $spools->spool_induk . '.png'); ?>" width="60%;display:inline">
						</td>
						<td style="padding-left:2px;height:12mm;font-size: 8px;">
							<p>DRAWING NO : <?= $spools->no_drawing; ?></p>
							<!-- <input placeholder="-" style="dislpay:inline-block;font-size: 8px;border:none;width:70%" rows="1" maxlength="26"><br> -->
							<p>SPOOL NO : <?= isset($dycode->sp_group_daycode)?strtoupper($dycode->sp_group_daycode):'-'; ?></p>
							<!-- <input placeholder="-" style="dislpay:inline-block;font-size: 8px;border:none;width:75%" maxlength="26"> -->
							<!-- <input placeholder="-" style="font-size: 12px;width: 100%;border:none;" maxlength="26"></input> -->
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
								<p><?= "CUST : " . $nm_customer; ?></p>
								<?php $Ipp = explode("-", $spools->id_produksi)[1]; ?>
								<p><?= "PROJ : " . $project; ?></p>
							</div>
						</td>
					</tr>

					<!-- Middle -->
					<tr>
						<td rowspan="2" style="text-align: center;overflow:hidden;">
							<img src="<?= base_url('assets/qrcode/images/' . $spools->spool_induk . '.png'); ?>" width="60%;display:inline">
						</td>
						<td style="padding-left:2px;height:6mm;font-size: 4px;">
							<p>DRAWING NO : <?= $spools->no_drawing; ?></p>
							<!-- <input placeholder="-" style="dislpay:inline-block;font-size: 5px;border:none;width:70%" rows="1" maxlength="26"><br> -->
							<p>SPOOL NO : <?= isset($dycode->sp_group_daycode)?strtoupper($dycode->sp_group_daycode):'-'; ?></p>
							<!-- <input placeholder="-" style="dislpay:inline-block;font-size: 5px;border:none;width:75%" maxlength="26"> -->
							<!-- <input placeholder="-" style="font-size: 12px;width: 100%;border:none;" maxlength="26"></input> -->
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

</body>
<script>
	// window.print();
	// window.onmousemove = function() {
	//     setTimeout(function() {
	//         window.close();
	//     }, 300)
	// }
</script>