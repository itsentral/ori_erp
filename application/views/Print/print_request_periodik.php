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


?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>PENGAJUAN PEMBAYARAN PERIODIK</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">No Dokumen</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= $rest_d[0]->no_doc;?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'>Department</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?= strtoupper(get_name('department','nm_dept','id',$rest_d[0]->departement));?></td>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
		</tr>
		<tr>
			<td class="mid">Tgl. Pengajuan</td>
			<td class="mid">:</td>
			<td class="mid"><?= date('d F Y', strtotime($rest_d[0]->tanggal_doc));?></td>
			<td class="mid"></td>
			<td class="mid"></td>
			<td class="mid"></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='5%'>No</th>
			<th class="mid" style='vertical-align:middle;'>Pengajuan</th>
			<th class="mid" width='10%'>Jatuh Tempo</th>
			<th class="mid" width='10%'>Budget</th>
			<th class="mid" width='10%'>Perkiraan Biaya</th>
			<th class="mid" width='10%'>Tanggal Bayar</th>
			<th class="mid" width='20%'>Keterangan</th> 
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		foreach($rest_data AS $val => $valx){$No++;
			$TGL_BAYAR = (!empty($valx['tgl_bayar']))?date('d-M-Y',strtotime($valx['tgl_bayar'])):'';
			echo "<tr>";
				echo "<td align='center'>".$No."</td>";
				echo "<td>".strtoupper($valx['nama'])."</td>";
				echo "<td align='center'>".date('d-M-Y',strtotime($valx['tanggal']))."</td>";
				echo "<td align='right'>".number_format($valx['budget'])."</td>";
				echo "<td align='right'>".number_format($valx['nilai'])."</td>";
				echo "<td align='center'>".$TGL_BAYAR."</td>";
				echo "<td>".strtoupper($valx['keterangan'])."</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td align='center' width='35%'>Mengetahui</td>
		<td align='center' width='5%'></td>
		<td align='center' width='25%'>Head Department</td>
		<td width='10%'></td>
		<td align='center'>Menyetujui</td>
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
		<td align='center'>________________</td>
		<td align='center'></td>
		<td align='center'>________________</td>
		<td align='center'></td>
		<td align='center'>________________</td>
		<td align='center'></td>
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
		font-size:10px;
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
		font-size:11px;
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($rest_d[0]->no_doc);
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('pengajuan-pembayaran-periodik.pdf' ,'I');