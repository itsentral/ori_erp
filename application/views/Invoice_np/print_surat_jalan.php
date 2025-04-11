<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
// include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf=new mPDF('utf-8','A4');
$mpdf->defaultheaderline=0;

set_time_limit(0);
ini_set('memory_limit','1024M');
$tglprint2 = date("d-m-Y");
$date = tgl_indo($total->tgl_invoice);//date('d-m-Y');
$invoice  = $total->no_invoice;
$so  = $total->so_number;
$total2  = $total->total_invoice;
$nm_customer  = $total->nm_customer;

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$HTML_HEADER = '<table class="gridtable2" width="100%" border="0">';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td align="center" width="30%">';
$HTML_HEADER .= '<b>PT. ORI POLYTEC COMPOSITES</b><br>';
$HTML_HEADER .= 'Jl. Akasia II Block A9/3<br>';
$HTML_HEADER .= 'Cikarang - Bekasi - Indonesia<br>';
$HTML_HEADER .= 'Telp : (021) 8972193<br>';
$HTML_HEADER .= 'Fax  : 8972192';
$HTML_HEADER .= '</td>';
$HTML_HEADER .= '<td width="30%" align=center valign=center><b>SURAT JALAN</b>
<br ><br><b>'.$invoice.'</b><br >
'.date("F d, Y",strtotime($total->tgl_invoice)).'
</td>';
$HTML_HEADER .= '<td width="30%"></td>';
$HTML_HEADER .= '</tr></table>';

$HTML_HEADER .= '<table class="gridtable2" width="100%" border="0">';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td><b>';
$HTML_HEADER .= $nm_customer;
$HTML_HEADER .= '<br>'.$customer->alamat.'</b>';
$HTML_HEADER .= '<br>Phone : '.$customer->telpon;
$HTML_HEADER .= '<br>Fax : '.$customer->fax;
$HTML_HEADER .= '<br>Attn. : '.$pic_customer->nm_pic;
$HTML_HEADER .= '</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '</table>';
?>
<table class="gridtable2" width='100%' border='1' cellpadding='2'>
	<thead>
		<tr>
			<td align='center' width='10%'>QTY</td>
			<td align='center' width='5%'>UNIT</td>
			<td align='center' style='vertical-align:middle;'>ITEM CUST/DESC CUST</td>
		</tr>
	</thead>
	<?php
	foreach($results as $data){ $val++;
		echo "<tr>";
			echo "<td align='right'>".$data->qty."</td>";
			echo "<td align='center'>".$data->unit."</td>";
			echo "<td align='left'>".$data->desc."</td>";
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


<?php
$html = ob_get_contents();
// exit;
ob_end_clean();
$mpdf->SetHeader($HTML_HEADER);
$mpdf->SetTitle($kode_delivery); 
$mpdf->AddPageByArray([
	'margin-left' => 2,
	'margin-right' => 2,
	'margin-top' => 55,
	'margin-bottom' => 2
]);
$mpdf->WriteHTML($html);
$mpdf->Output('Surat Jalan '.date('dmYHis').'.pdf' ,'I');