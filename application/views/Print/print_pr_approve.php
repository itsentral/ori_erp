<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
// $mpdf=new mPDF('utf-8','A4');
$mpdf=new mPDF('utf-8','A4-L');

set_time_limit(0);
ini_set('memory_limit','1024M');

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$TGL_DIBUTUHKAN = '';
if(!empty($non_frp)){
	if($non_frp[0]['tanggal'] != null AND $non_frp[0]['tanggal'] != '0000-00-00'){
	$TGL_DIBUTUHKAN = date('d F Y',strtotime($non_frp[0]['tanggal']));
	}
}
if(!empty($result)){
	if($result[0]['tanggal'] != null AND $result[0]['tanggal'] != '0000-00-00'){
	$TGL_DIBUTUHKAN = date('d F Y',strtotime($result[0]['tanggal']));
	}
}

?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>PR MATERIAL</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">Asal Permintaan</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?=$no_ipp;?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'>Kebutuhan</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?=$kebutuhan;?></td>
			<td class="mid" width='15%'>Tgl Dibutuhkan</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?=$TGL_DIBUTUHKAN;?></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr class='bg-blue'>
			<th colspan='10' style='text-align:left;'><b>MATERIAL</b></th>
		</tr>
		<tr>
			<th class="mid" width='5%'>#</th>
			<th class="mid" style='vertical-align:middle;'>Material Name</th>
			<th class="mid" width='13%'>Category</th>
			<th class="mid" width='7%'>Stock<br>Actual (Kg)</th>
			<th class="mid" width='7%'>Stock<br>Free (Kg)</th>
			<th class="mid" width='7%'>Safety<br>Stock (Kg)</th>
			<th class="mid" width='7%'>Max<br>Stock (Kg)</th>
			<th class="mid" width='7%'>Kg/bulan</th>
			<th class="mid" width='7%'>MOQ</th>
			<th class="mid" width='7%'>Qty (Kg)</th>
			<!-- <th class="mid" width='7%'>Tanggal Dibutuhkan</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
			$no  = 0;
			foreach($result AS $val => $valx){ $no++;
				$safetystock 	= get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']);
				$kg_per_bulan 	= get_max_field('raw_materials', 'kg_per_bulan', 'id_material', $valx['id_material']);
				$max_stock 		= get_max_field('raw_materials', 'max_stock', 'id_material', $valx['id_material']);
				
				$reorder 		= ($safetystock/30) * $kg_per_bulan;
				$max_stock2 	= ($max_stock/30) * $kg_per_bulan;
				echo "<tr>";
					echo "<td align='center'>".$no."</td>";
					echo "<td align='left'>".$valx['nm_material']."</td>";
					echo "<td align='left'>".get_name('raw_materials', 'nm_category', 'id_material', $valx['id_material'])."</td>"; 
					echo "<td align='right'>".number_format($valx['qty_stock'],2)."</td>";
					echo "<td align='right'>".number_format($valx['qty_stock'] - $valx['qty_booking'],2)."</td>";
					echo "<td align='right'>".number_format($reorder,2)."</td>";
					echo "<td align='right'>".number_format($max_stock2,2)."</td>";
					echo "<td align='right'>".number_format($kg_per_bulan,2)."</td>";
					echo "<td align='right'>".number_format($valx['moq_m'])."</td>";
					echo "<td align='right'>".number_format($valx['qty_request'])."</td>";
					// echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
				echo "</tr>";
			}
		?>
	</tbody>
	<?php if(!empty($non_frp)){ ?>
	<thead>
		<tr class='bg-blue'>
			<th colspan='10' style='text-align:left;'><b>NON FRP</b></th>
		</tr>
		<tr class='bg-blue'>
			<th class="text-center no-sort">#</th>
			<th class="text-center" colspan='3'>Material Name</th>
			<th class="text-center" colspan='3'>Material</th>
			<th class="text-center">Category</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Unit</th>
			<!-- <th class="text-center">Tanggal Dibutuhkan</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
		$no  = 0;
		foreach($non_frp AS $val => $valx){ $no++;
			
			$satuan = $valx['satuan'];
			if($valx['idmaterial'] == '2'){
				$satuan = '1';
			}
			$satx = get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan);
			
			// $nm_acc = get_name_acc($valx['id_material']);
			// $nm_mat = strtoupper(get_name('accessories','material','id',$valx['id_material']));
			// if(empty($valx['idmaterial'])){
			// 	$satx = '-';
			// 	$nm_acc = strtoupper($valx['nm_material']);
			// 	$nm_mat = "";
			// }
			

			$nm_acc = get_name_acc($valx['id_material']);
			$nm_mat = strtoupper(get_name('accessories','material','id',$valx['id_material']));

			if($nm_acc == 'Not found'){
			  $nm_acc = strtoupper($valx['nm_material']);
			  $nm_mat = "";
			}

			echo "<tr>";
				echo "<td align='center'>".$no."</td>";
				echo "<td align='left' colspan='3'>".$nm_acc."</td>";
				echo "<td align='left' colspan='3'>".$nm_mat."</td>";
				echo "<td align='center'>".strtoupper(get_name('accessories_category', 'category', 'id', $valx['idmaterial']))."</td>";
				echo "<td align='center'>".number_format($valx['purchase'])."</td>";
				echo "<td align='center'>".strtoupper($satx)."</td>";
				// echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
			echo "</tr>";
		}
		?>
	</tbody>
	<?php } ?>
</table><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='50%'></td>
		<td align='center'></td>
		<td align='center'>Diketahui,</td>
		<td width='5%'></td>
		<td align='center'>Disetujui,</td>
		<td></td>
	</tr>
	<tr>
		<td height='45px'></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'></td>
		<td align='center'>(________________)</td>
		<td></td>
		<td align='center'>(________________)</td>
		<td></td>
	</tr>
</table>

<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 1cm;
	}
	.mid{
		vertical-align: middle !important;
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
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable2 th {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
</style>


<?php
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$no_ipp."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($no_ipp."/".date('ymdhis', strtotime($dated))); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('print pr approval '.$no_ipp.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');