<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
//include $sroot."/application/libraries/MPDF57/mpdf.php";
require_once(APPPATH.'libraries/MPDF57/mpdf.php');
// $mpdf=new mPDF('utf-8','A4');
$mpdf=new mPDF('utf-8','A4-L');

set_time_limit(0);
ini_set('memory_limit','1024M');

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$sql_d 		= "SELECT * FROM warehouse_adjustment_detail WHERE kode_trans='".$kode_trans."' ";
$rest_d		= $this->db->query($sql_d)->result_array();

$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
$rest_data 			= $this->db->query($sql_header)->result_array();
?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>TANDA TERIMA BARANG</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">No Transaksi</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= $kode_trans;?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'>No PO</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?= $rest_data[0]['no_ipp'];?></td>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
		</tr>
		<tr>
			<td class="mid">Tanggal Terima</td>
			<td class="mid">:</td>
			<td class="mid"><?= date('d F Y', strtotime($rest_data[0]['checked_date']));?></td>
			<td class="mid"></td>
			<td class="mid"></td>
			<td class="mid"></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='4%'>No</th>
			<th class="mid" style='vertical-align:middle;'>Material Name</th>
			<th class="mid" width='10%'>Qty Order (Kg)</th>
			<th class="mid" width='10%'>Qty Diterima (Kg)</th>
			<th class="mid" width='10%'>Qty Rusak (Kg)</th>
			<th class="mid" width='10%'>Qty Oke (Kg)</th>
			<th class="mid" width='10%'>Qty Ng (Kg)</th>
			<th class="mid" width='8%'>Expired</th>
			<th class="mid" width='17%'>Keterangan</th> 
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		foreach($rest_d AS $val => $valx){$No++;
			$sql_check 		= "SELECT * FROM warehouse_adjustment_check WHERE kode_trans='".$kode_trans."' AND id_material='".$valx['id_material']."' ";
			$rest_dt_check	= $this->db->query($sql_check)->result_array();
			
			echo "<tr>";
				echo "<td align='center'>".$No."</td>";
				echo "<td>".strtoupper($valx['nm_material'])."</td>";
				echo "<td align='right'>".number_format($valx['qty_order'],2)."</td>";
				echo "<td align='right'>".number_format($valx['qty_oke'],2)."</td>";
				echo "<td align='right'>".number_format($valx['qty_rusak'],2)."</td>";
				echo "<td align='right'>";
					foreach($rest_dt_check AS $val2 => $valx2){echo number_format($valx2['qty_oke'],2)."<br>";}
				echo "</td>";
				echo "<td align='right'>";
					foreach($rest_dt_check AS $val2 => $valx2){echo number_format($valx2['qty_rusak'],2)."<br>";}
				echo "</td>";
				echo "<td align='right'>";
					foreach($rest_dt_check AS $val2 => $valx2){echo date('d M Y', strtotime($valx2['expired_date']))."<br>";}
				echo "</td>";
				echo "<td align='left'>";
					foreach($rest_dt_check AS $val2 => $valx2){echo ucfirst($valx2['keterangan'])."<br>";}
				echo "</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='65%'></td>
		<td align='center'></td>
		<td></td>
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
	</tr>
	<tr>
		<td></td>
		<td align='center'></td>
		<td></td>
		<td></td>
		<td align='center'>QC Inspector</td>
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($kode_trans."/".date('ymdhis', strtotime($dated))); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('tanda terima material '.$kode_trans.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');