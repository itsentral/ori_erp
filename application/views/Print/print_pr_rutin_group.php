<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
// $mpdf=new mPDF('utf-8','A4');
$mpdf=new mPDF('utf-8','A4');

set_time_limit(0);
ini_set('memory_limit','1024M');

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>PURCHASE REQUEST RUTIN</h2></b></td>
	</tr>
</table>
<br>
<br>
<?php
if($tgl_awal <> '0'){
?>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">Tanggal Awal</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= date('d F Y', strtotime($tgl_awal));?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'>Tanggal Akhir</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?= date('d F Y', strtotime($tgl_akhir));?></td>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
		</tr>
	</thead>
</table><br>
<?php } ?>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='4%'>#</th>
			<th class="mid" style='vertical-align:middle;'>Material Name</th>
			<th class="mid" width='10%'>Kebutuhan/bulan</th>
			<th class="mid" width='10%'>Stock</th>
			<th class="mid" width='10%'>Purchase</th>
			<th class="mid" width='9%'>Unit</th>
			<th class="mid" width='11%'>Tanggal Dibutuhkan</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no  = 0;
			foreach($result AS $val => $valx){ $no++;
				$sum_kebutuhan = $this->db->query("SELECT SUM(kebutuhan_month) AS sum_keb FROM budget_rutin_detail WHERE id_barang='".$valx['code_group']."' ")->result();
			
				echo "<tr>";
					echo "<td align='center'>".$no."</td>";
					echo "<td align='left'>".strtoupper($valx['nm_material']." - ".$valx['spec'])."</td>";
					echo "<td align='right'>".number_format($sum_kebutuhan[0]->sum_keb)."</td>";
					echo "<td align='right'>".number_format($valx['stock'])."</td>";
					echo "<td align='right'>".number_format($valx['purchase'])."</td>";
					echo "<td align='left'>".get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan'])."</td>";
					echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";				
				echo "</tr>";
			}
		?>
	</tbody>
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$tgl_awal." - ".$tgl_awal."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle("PR Rutin"); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('print pr '.$tgl_awal.' '.$tgl_awal.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');