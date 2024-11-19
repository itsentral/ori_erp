<style>
	* {
		font-family: monospace;
		margin: 0;
		padding: 0;
	}

	.wrapper {
		position: relative;
		width: 365px;
		border: 3px solid;
		height: 120px;
		padding: 2px;
		margin: 5px;
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

	img {
		height: 100%;
	}

	.title-name {
		margin: 0px;
		padding-top: 5px;
		font-weight: bolder;
		font-size: 18px;
	}
</style>

<body>
	<?php
	foreach ($explode as $keys => $code) :
		$NOMOR_SO 	= explode('-', $products[$code]->product_code);
		$spec 		= spec_bq2($products[$code]->id_milik);
	?>
		<div class="wrapper">
			<div class="content-wrapper">
				<img src="<?= base_url('assets/qrcode/images/' . $code . '.png'); ?>">
				<div style="position: relative;width: 240px;">
					<h3 class="title-name mb-0"><?= $NOMOR_SO[0]; ?></h3>
					<div style="font-weight: bolder;font-size: 14px;padding-bottom:7px"><?= $products[$code]->nm_customer; ?></div>
					<h2 class="fw-bold"><?= strtoupper($products[$code]->id_category); ?></h2>
					<p style="word-wrap: break-word;">Spec : <?= $spec; ?></p>
					<div style="font-weight: bolder;font-size: 14px;padding-bottom:7px"><?= $products[$code]->id; ?></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

</body>
<script>
	window.print();
	window.onmousemove = function() {
		setTimeout(function() {
			window.close();
		}, 300)
	}
</script>