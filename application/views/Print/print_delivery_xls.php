<?php

date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$header 		= $this->db->get_where('production', array('no_ipp'=>str_replace('PRO-','',$result[0]['id_produksi'])))->result();
$header_del 	= $this->db->get_where('delivery_product', array('kode_delivery'=>$kode_delivery))->result();
$alamat 		= $header_del[0]->alamat;
$project 		= $header_del[0]->project;

$nomorSO = array();
$nomorPO = array();
foreach($result	 AS $val => $valx){ $val++;
	$EXPLODE = explode('-',$valx['product_code']);
	$nomorSO[] = $EXPLODE[0];

	$no_ipp = str_replace('PRO-','',$valx['id_produksi']);
	$no_po = get_name('billing_so','no_po','no_ipp',$no_ipp);

	$nomorPO[] = $no_po;
}

$NOS_OS = implode('/',array_unique($nomorSO));
$NOS_PO = implode('/',array_unique($nomorPO));

?>
<html>
 <head>
  <title> Surat Jalan </title>
	<style type="text/css">
		@page {
			margin-top: 0cm;
			margin-left: 0cm;
			margin-right: 0cm;
			margin-bottom: 0cm;
		}
		.mid{
			vertical-align: middle !important;
		}
		p{
			font-family: verdana,arial,sans-serif;
			font-size:12px;
		}
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:9px;
			color:#333333;
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
			font-family: verdana,arial,sans-serif;
			font-size:12px;
			color:#333333;
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
 </head>
 <body>
<table class="gridtable2" width="700" border="0" style="border-bottom:none;">
	<tr>
		<td rowspan="7" align="center"  style="border-bottom:none;" colspan="4" width="220">
			<b>PT. ORI POLYTEC COMPOSITES</b><br>
			Jl. Akasia II Block A9/3<br>
			Cikarang - Bekasi - Indonesia<br>
			Telp : (021) 8972193<br>
			Fax  : 8972192
		</td>
		<td style="font-size: 9px; border-right:none; border-bottom: none;">Form No</td>
		<td style="font-size: 9px; border-left:none; border-right:none; border-bottom: none;" width="2" >:</td>
		<td style="font-size: 9px; border-left:none; border-bottom: none;" colspan=2 align=left><?=strtoupper($header_del[0]->fm_no);?></td>
		<td style="border-bottom:none;" colspan="3" rowspan="2" align="center" width="220"><u><b>DISTRIBUSI SURAT JALAN</b></u></td>
	</tr>
	<tr>
		<td style="font-size: 9px; border-right:none; border-top: none; border-bottom: none;">Rev. No</td>
		<td style="font-size: 9px; border:none;">:</td>
		<td style="font-size: 9px; border-left:none; border-top: none; border-bottom: none;" align=left colspan=2><?=$header_del[0]->rev;?></td>
	</tr>
	<tr>
		<td style="font-size: 9px; border-right:none; border-top: none;">Issue Date</td>
		<td style="font-size: 9px; border-right:none; border-top: none; border-left:none;">:</td>
		<td style="font-size: 9px; border-left:none; border-top: none;" align=left colspan=2><?=$header_del[0]->issue_date;?></td>
		<td style="font-size: 9px; border-bottom:none; border-right: none; border-top: none;" nowrap>Putih/Asli</td>
		<td style="font-size: 9px; border:none;" width="2">:</td>
		<td style="font-size: 9px; border-bottom:none; border-left: none; border-top: none;" nowrap>Penagihan / Finance</td>
	</tr>
	<tr>
		<td colspan="4" style="border-bottom:none;" align="center" width="260"><u><b>SURAT JALAN</b></u></td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Merah</td>
		<td style="font-size: 9px; border:none;">:</td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">PPIC/Logistik</td>
	</tr>
	<tr>
		<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO</td>
		<td style="font-size: 12px; border:none;">:</td>
		<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;" align=left colspan=2><?=strtoupper($header_del[0]->nomor_sj);?></td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Kuning</td>
		<td style="font-size: 9px; border:none;">:</td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Pembeli / Penerima</td>
	</tr>
	<tr>
		<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;" valign=top>NO. PO</td>
		<td style="font-size: 12px; border:none;" valign=top width="2">:</td>
		<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none; word-wrap:break-word;width:120pt;" align=left colspan=2><?php
			$newNOS_PO=$NOS_PO;
			$panjang=strlen($newNOS_PO);
			if($panjang>30){
				$limit1=floor($panjang/2);
				echo substr($newNOS_PO, 0, $limit1).'<br/>';
				echo substr($newNOS_PO, $limit1);
			}else{
				echo $newNOS_PO;
			}?></td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Hijau</td>
		<td style="font-size: 9px; border:none;">:</td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Cost Control</td>
	</tr>
	<tr>
		<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO. SO</td>
		<td style="font-size: 12px; border:none;">:</td>
		<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;" align=left colspan=2><?=$NOS_OS;?></td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Biru</td>
		<td style="font-size: 9px; border: none;">:</td>
		<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Security</td>
	</tr>
</table>
<table class="gridtable2" width="700" border='0'>
	<tr>
		<td style='border-bottom:none; border-right: none; vertical-align: bottom;' height='30px' colspan=2>Pengemudi</td>
		<td style='border-bottom:none; border-left: none;  border-right: none; vertical-align: bottom;'>:</td>
		<td style='border-bottom:none; border-left: none; border-right: none; vertical-align: bottom;'>................................</td>
		<td rowspan='2' colspan=3 style='border-bottom:none; border-right: none; border-left: none;'>&nbsp;</td>
		<td rowspan='3' colspan="4" style='border-bottom:none; border-left: none; vertical-align: top; padding-top:10px; word-wrap:break-word'>
			Place &amp; Date: Cikarang, <?=date('d F Y',strtotime($header_del[0]->delivery_date));?><br>
			To,<br />
			<b><?php
			$newalamat=str_replace(' & ', '&amp;', $alamat);
			$panjang=strlen($newalamat);
			if($panjang>50){
				$limit1=floor($panjang/2);
				echo substr($newalamat, 0, $limit1).'<br/>';
				echo substr($newalamat, $limit1);
			}else{
				echo $newalamat;
			}
			?></b>
		</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;' colspan=2>No. Container</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;' colspan=2>No. Seal</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;' colspan=2>Jenis Kendaraan</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
		<td rowspan='2' colspan='3' style='border-bottom:none; border-right: none; border-left: none; border-top: none;'></td>
		<td rowspan='2' colspan="4" style='border-bottom:none; border-left: none; vertical-align: top; border-top: none;'>
			Project, <br>
			<b><?=$project;?></b>
		</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;' colspan=2>No. Polisi</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
</table>
<table class="gridtable2" width='700' border='1' cellpadding='2'>
	<thead>
		<tr>
			<td align='center'>QTY</td>
			<td align='center'>UNIT</td>
			<!-- <td align='center' style='vertical-align:middle;'>NAMA DAN UKURAN BARANG</td> -->
			<td align='center' style='vertical-align:middle;' colspan=9>ITEM CUST/DESC CUST</td>
		</tr>
	</thead>
	<?php
	foreach($result	 AS $val => $valx){ $val++;
		$series 	= get_name('so_detail_header','series','id',$valx['id_milik']);
		$product 	= strtoupper($valx['product']).", ".$series.", DIA ".spec_bq2($valx['id_milik']);
		$SATUAN 	= 'PCS';
		$QTY 		= $valx['qty_product'];

		$ID_MILIK 	= (!empty($GET_ID_MILIK[$valx['id_milik']]))?$GET_ID_MILIK[$valx['id_milik']]:'';
		$GET_DESC 	= (!empty($GET_DESC_DEAL[$ID_MILIK]))?$GET_DESC_DEAL[$ID_MILIK]:'';
		$DESC 		= (!empty($valx['desc']))?$valx['desc']:'';
		$ALIGN 		= "center";
		if($valx['sts_product'] == 'so material'){
			$product 	= strtoupper(get_name('raw_materials','nm_material','id_material',$valx['product']));
			$SATUAN 	= 'KG';
			$QTY 		= number_format($valx['berat'],4);
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "right";
			$DESC 		= (!empty($valx['desc']))?$valx['desc']:$product;
		}
		
		echo "<tr>";
			echo "<td align='".$ALIGN."'>".$QTY."</td>";
			echo "<td align='center'>".strtolower($SATUAN)."</td>";
			// echo "<td align='left'>".$GET_DESC."</td>";
			echo "<td align='left' colspan=9>".$DESC."</td>";
		echo "</tr>";
	}
	echo "<tr>";
		echo "<td colspan='2'></td>";
		echo "<td colspan=9><b>Note: Barang dikirim dalam keadaan baik.</b></td>";
	echo "<tr>";
		echo "<td colspan='2' style='border-bottom:none;'></td>";
		echo "<td style='border-bottom:none;' colspan=9></td>";
	echo "</tr>";
	?>
</table>
<table class="gridtable2" width='700' border='0' cellpadding='2'>
	<tr>
		<td align='center' colspan=2>Dibuat,</td>
		<td align='center' colspan=2>Diperiksa,</td>
		<td align='center' colspan=3>Mengetahui,</td>
		<td align='center' colspan=2>Dicek oleh,</td>
		<td align='center' colspan=2>Diterima</td>
	</tr>
	<tr>
		<td height='100' colspan=11></td>
	</tr>
	<tr>
		<td align='center' colspan=2 width=100>Staff Gudang</td>
		<td align='center' colspan=2 width=100>Head PPIC</td>
		<td align='center' colspan=3></td>
		<td align='center' colspan=2 width=100>Pembawa</td>
		<td align='center' colspan=2 width=100></td>
	</tr>
	<tr>
		<td align='center' colspan='4'>Log Dept</td>
		<td align='center' colspan=3>Cost Control &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Factory Manager</td>
		<td align='center' colspan=2>(Supir/Ekspedisi)</td>
		<td align='center' colspan=2>Penerima</td>
	</tr>
</table>
<p>NB : Pembawa bertanggungjawab atas barang yang dikirim.</p>
 </body>
</html>
