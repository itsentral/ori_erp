<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf=new mPDF('utf-8','A4');
// $mpdf=new mPDF('utf-8','A4-L');

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
		<td align='center'><b><h2>PR ASSET</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">Kode Pengajuan</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= $result[0]['no_pr'];?></td>
		</tr>
		<tr>
			<td class="mid">Tanggal</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= date('d-M-Y',strtotime($result[0]['tgl_pr']));?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
            <th class="mid" width='7%'>#</th>
            <th class="mid">Nama Asset</th>
            <th class="mid" width='10%'>Qty</th>
            <th class="mid" width='17%'>Tgl Dibutuhkan</th>
            <th class="mid" width='20%'>Nilai PR</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$no  = 0;
        foreach($result AS $val => $valx){ $no++;
            echo "<tr>";
                echo "<td align='center'>".$no."</td>";
                echo "<td align='left'>".strtoupper($valx['nm_barang'])."</td>";
                echo "<td align='center'>".number_format($valx['qty'],2)."</td>";
                echo "<td align='center'>".date('d-M-Y', strtotime($valx['tgl_dibutuhkan']))."</td>";
				echo "<td align='right'>".number_format($valx['nilai_pr'],2)."</td>";
            echo "</tr>";
        }
		?>
	</tbody>
</table><br><br><br>
<!-- <table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='5%'></td>
		<td align='center'>Ttd, Mengetahui</td>
		<td width='5%'></td>
		<td align='center'>Ttd,</td>
		<td width='5%'></td>
		<td align='center'>Ttd,</td>
		<td></td>
	</tr>
	<tr>
		<td height='45px'></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'>(ATASAN GUDANG)</td>
		<td></td>
		<td align='center'>(PIC yang bertanggung jawab)</td>
		<td></td>
		<td align='center'>(PENERIMA)</td>
		<td></td>
	</tr>
</table> -->

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
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 2px;
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
// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle('PR Asset'); 
$mpdf->AddPage();
// $mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('pr-asset-'.$no_pr.'.pdf' ,'I');