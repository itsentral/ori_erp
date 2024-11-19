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

$get_namacc = $this->db->select('name')->get_where('hris.departments', array('id'=>$dept))->result();

?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h1>ORDER PRODUKSI</h1></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">Cost Center</td>
			<td class="mid">:</td>
			<td class="mid"><?= strtoupper($get_namacc[0]->name);?></td>
		</tr>
		<tr>
			<td class="mid" width='10%'>Periode</td>
			<td class="mid" width='3%'>:</td>
			<td class="mid"><?= date('d F Y', strtotime($tgl_awal));?> - <?= date('d F Y', strtotime($tgl_akhir));?></td>
		</tr>
		<tr>
			<td class="mid">Project SO</td>
			<td class="mid">:</td>
			<td class="mid"><?= get_nomor_so($no_ipp);?> / <?= strtoupper(get_name('production','project', 'no_ipp',$no_ipp));?></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='4%'>No</th>
			<th class="mid" width='10%' style='vertical-align:middle;'>IPP</th>
			<th class="mid" style='vertical-align:middle;'>Product</th>
			<th class="mid" width='15%'>Dimensi</th>
			<th class="mid" width='7%'>Qty</th>
			<th class="mid" width='33%'>ID Spool</th>
			<th class="mid" width='12%'>Due Date</th> 
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		foreach($detail AS $val => $valx){$No++;
			echo "<tr>";
				echo "<td align='center'>".$No."</td>";
				echo "<td align='center'>".strtoupper($no_ipp)."</td>";
				echo "<td>".strtoupper($valx['product'])."</td>";
				echo "<td>".strtoupper($valx['dimensi'])."</td>";
				echo "<td align='center'>".number_format($valx['qty'])."</td>";
				echo "<td>".strtoupper($valx['id_spool'])."</td>";
				echo "<td align='right'>".date('d-F-Y', strtotime($valx['must_finish']))."</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='65%'><b>Catatan :</b></td>
		<td align='center'>Disiapkan,</td>
		<td></td>
		<td width='5%'></td>
		<td align='center'>Diperiksa Oleh,</td>
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
		<td align='center'>(_________________)</td>
		<td></td>
		<td></td>
		<td align='center'>(_________________)</td>
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
$mpdf->Output('request produksi order '.$no_ipp.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');