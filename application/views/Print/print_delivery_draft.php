<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Print Delivery</title>
</head>

<body>

	<?php
	set_time_limit(0);
	ini_set('memory_limit', '1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	// ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');

	$header 		= $this->db->get_where('production', array('no_ipp' => str_replace('PRO-', '', $result[0]['id_produksi'])))->result();
	$header_del 	= $this->db->get_where('delivery_product', array('kode_delivery' => $kode_delivery))->result();
	$alamat 		= $header_del[0]->alamat;
	$project 		= $header_del[0]->project;

	$nomorSO = array();
	$nomorPO = array();
	foreach ($result	 as $val => $valx) {
		$val++;
		$EXPLODE = explode('-', $valx['product_code']);
		if ($valx['sts_product'] == 'so material') {
			$idPro = str_replace("PRO", "BQ", $valx['id_produksi']);
			$lis_so = $this->db->get_where('so_number', ['id_bq' => $idPro])->row();
			$nomorSO[] = $lis_so->so_number;
		} else {
			$nomorSO[] = $EXPLODE[0];
		}

		$no_ipp = str_replace('PRO-', '', $valx['id_produksi']);
		$no_po = get_name('billing_so', 'no_po', 'no_ipp', $no_ipp);

		$nomorPO[] = $no_po;
	}

	$NOS_OS = implode('/', array_unique($nomorSO));
	$NOS_PO = implode('/', array_unique($nomorPO));

	?>
	<table class="gridtable2" width="100%" border="0" style="border-bottom:none;">
		<tr>
			<td rowspan="7" align="center" style="border-bottom:none;">
				<b>PT. ORI POLYTEC COMPOSITES</b><br>
				Jl. Akasia II Block A9/3<br>
				Cikarang - Bekasi - Indonesia<br>
				Telp : (021) 8972193<br>
				Fax : 8972192
			</td>
			<td style="font-size: 9px; border-right:none; border-bottom: none;" width="8%">Form No</td>
			<td style="font-size: 9px; border-left:none; border-right:none; border-bottom: none;" width="1%">:</td>
			<td style="font-size: 9px; border-left:none; border-bottom: none;" width="30%"><?= strtoupper($header_del[0]->fm_no); ?></td>
			<td style="border-bottom:none;" colspan="3" rowspan="2" align="center"><u><b>DISTRIBUSI SURAT JALAN</b></u></td>
		</tr>
		<tr>
			<td style="font-size: 9px; border-right:none; border-top: none; border-bottom: none;">Rev. No</td>
			<td style="font-size: 9px; border:none;">:</td>
			<td style="font-size: 9px; border-left:none; border-top: none; border-bottom: none;"><?= $header_del[0]->rev; ?></td>
		</tr>
		<tr>
			<td style="font-size: 9px; border-right:none; border-top: none;">Issue Date</td>
			<td style="font-size: 9px; border-right:none; border-top: none; border-left:none;">:</td>
			<td style="font-size: 9px; border-left:none; border-top: none;"><?= $header_del[0]->issue_date; ?></td>
			<td style="font-size: 9px; border-bottom:none; border-right: none; border-top: none;" width="8%">Putih/Asli</td>
			<td style="font-size: 9px; border:none;" width="1%">:</td>
			<td style="font-size: 9px; border-bottom:none; border-left: none; border-top: none;" width="18%">Penagihan / Finance</td>
		</tr>
		<tr>
			<td colspan="3" style="border-bottom:none;" align="center"><u><b>SURAT JALAN</b></u></td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Merah</td>
			<td style="font-size: 9px; border:none;">:</td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">PPIC/Logistik</td>
		</tr>
		<tr>
			<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO</td>
			<td style="font-size: 12px; border:none;">:</td>
			<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;"><?= strtoupper($header_del[0]->nomor_sj); ?></td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Kuning</td>
			<td style="font-size: 9px; border:none;">:</td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Pembeli / Penerima</td>
		</tr>
		<tr>
			<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO. PO</td>
			<td style="font-size: 12px; border:none;">:</td>
			<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;"><?= $NOS_PO; ?></td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Hijau</td>
			<td style="font-size: 9px; border:none;">:</td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Cost Control</td>
		</tr>
		<tr>
			<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO. SO</td>
			<td style="font-size: 12px; border:none;">:</td>
			<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;"><?= $NOS_OS; ?></td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Biru</td>
			<td style="font-size: 9px; border: none;">:</td>
			<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Security</td>
		</tr>
	</table>

	<table class="gridtable2" width="100%" border='0'>
		<tr>
			<td width='15%' style='border-bottom:none; border-right: none; vertical-align: bottom;' height='30px'>Pengemudi</td>
			<td width='2%' style='border-bottom:none; border-left: none;  border-right: none; vertical-align: bottom;'>:</td>
			<td width='20%' style='border-bottom:none; border-left: none; border-right: none; vertical-align: bottom;'>................................</td>
			<td rowspan='3' style='border-bottom:none; border-right: none; border-left: none;'></td>
			<td width='50%' rowspan='3' style='border-bottom:none; border-left: none; vertical-align: top; padding-top:10px;'>
				Place & Date: Cikarang, <?= date('d F Y', strtotime($header_del[0]->delivery_date)); ?><br>
				To, <br>
				<b><?= $alamat; ?></b>
			</td>
		</tr>
		<tr>
			<td style='border-bottom:none; border-right: none;  border-top: none;'>No. Container</td>
			<td style='border: none;'>:</td>
			<td style='border: none;'>................................</td>
		</tr>
		<tr>
			<td style='border-bottom:none; border-right: none;  border-top: none;'>No. Seal</td>
			<td style='border: none;'>:</td>
			<td style='border: none;'>................................</td>
		</tr>
		<tr>
			<td style='border-bottom:none; border-right: none;  border-top: none;'>Jenis Kendaraan</td>
			<td style='border: none;'>:</td>
			<td style='border: none;'>................................</td>
			<td rowspan='2' style='border-bottom:none; border-right: none; border-left: none; border-top: none;'></td>
			<td rowspan='2' style='border-bottom:none; border-left: none; vertical-align: top; border-top: none;'>
				Project, <br>
				<b><?= $project; ?></b>
			</td>
		</tr>
		<tr>
			<td style='border-bottom:none; border-right: none;  border-top: none;'>No. Polisi</td>
			<td style='border: none;'>:</td>
			<td style='border: none;'>................................</td>
		</tr>
	</table>
	<table class="gridtable2" width='100%' border='1' cellpadding='2'>
		<thead>
			<tr>
				<td align='center' width='10%'>QTY</td>
				<td align='center' width='5%'>UNIT</td>
				<!-- <td align='center' style='vertical-align:middle;'>NAMA DAN UKURAN BARANG</td> -->
				<td align='center' style='vertical-align:middle;'>ITEM CUST/DESC CUST</td>
			</tr>
		</thead>
		<?php
		foreach ($result	 as $val => $valx) {
			$val++;
			$series 	= get_name('so_detail_header', 'series', 'id', $valx['id_milik']);
			$product 	= strtoupper($valx['product']) . ", " . $series . ", DIA " . spec_bq2($valx['id_milik']);
			$SATUAN 	= 'PCS';
			$QTY 		= $valx['qty_product'];

			$ID_MILIK 	= (!empty($GET_ID_MILIK[$valx['id_milik']])) ? $GET_ID_MILIK[$valx['id_milik']] : '';
			$GET_DESC 	= (!empty($GET_DESC_DEAL[$ID_MILIK])) ? $GET_DESC_DEAL[$ID_MILIK] : '';
			$DESC 		= (!empty($valx['desc'])) ? $valx['desc'] : '';
			$ALIGN 		= "center";
			if ($valx['sts_product'] == 'so material') {
				$product 	= strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['product']));
				$SATUAN 	= 'KG';
				$QTY 		= number_format($valx['berat'], 4);
				$ID_MILIK 	= '';
				$GET_DESC 	= '';
				$ALIGN 		= "right";
				$DESC 		= (!empty($valx['desc'])) ? $valx['desc'] : $product;
			}

			echo "<tr>";
			echo "<td align='" . $ALIGN . "'>" . $QTY . "</td>";
			echo "<td align='center'>" . strtolower($SATUAN) . "</td>";
			// echo "<td align='left'>".$GET_DESC."</td>";
			echo "<td align='left'>" . $DESC . "</td>";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td colspan='2'></td>";
		echo "<td><b>Note: Barang dikirim dalam keadaan baik.</b></td>";
		echo "<tr>";
		echo "<td colspan='2' style='border-bottom:none;'></td>";
		echo "<td style='border-bottom:none;'></td>";
		echo "</tr>";
		?>
	</table>
	<table class="gridtable2" width='100%' border='0' cellpadding='2'>
		<tr>
			<td width='15%' align='center'>Dibuat,</td>
			<td width='15%' align='center'>Diperiksa,</td>
			<td align='center'>Mengetahui,</td>
			<td width='15%' align='center'>Dicek oleh,</td>
			<td width='15%' align='center'>Diterima</td>
		</tr>
		<tr>
			<td height='65px'></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td align='center'>Staff Gudang</td>
			<td align='center'>Head PPIC</td>
			<td align='center'></td>
			<td align='center'>Pembawa</td>
			<td align='center'></td>
		</tr>
		<tr>
			<td align='center' colspan='2'>Log Dept</td>
			<td align='center'>Cost Control &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Factory Manager</td>
			<td align='center'>(Supir/Ekspedisi)</td>
			<td align='center'>Penerima</td>
		</tr>
	</table>
	<p>NB : Pembawa bertanggungjawab atas barang yang dikirim.</p>

</body>
<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}

	.mid {
		vertical-align: middle !important;
	}

	p {
		font-family: verdana, arial, sans-serif;
		font-size: 12px;
	}

	table.gridtable {
		font-family: verdana, arial, sans-serif;
		font-size: 9px;
		color: #333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}

	table.gridtable th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}

	table.gridtable th.head {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}

	table.gridtable td {
		border-width: 1px;
		padding: 3px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}

	table.gridtable td.cols {
		border-width: 1px;
		padding: 3px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}

	table.gridtable2 {
		font-family: verdana, arial, sans-serif;
		font-size: 12px;
		color: #333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0cm;
	}

	table.gridtable2 th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
		margin-top: 0cm;
		margin-left: 0cm;
	}

	table.gridtable2 th.head {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
		margin-top: 0cm;
		margin-left: 0cm;
	}

	table.gridtable2 td {
		border-width: 1px;
		padding: 3px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
		margin-top: 0cm;
		margin-left: 0cm;
	}

	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 3px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
		margin-top: 0cm;
		margin-left: 0cm;
	}
</style>

</html>
<script>
	window.print();
	window.onmousemove = function() {
		setTimeout(function() {
			window.close();
		}, 300)
	}
</script>