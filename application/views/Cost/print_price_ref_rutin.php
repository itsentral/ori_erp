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
		<td align='center'><b><h2>LIST PRICE RUTIN <?=$sts_val;?></h2></b></td>
	</tr>
</table>
<br>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='5%'>No</th>
			<th class="mid" width='10%'>Tgl</th>
			<th class="mid" style='vertical-align:middle;'>Material Name</th>
			<th class="mid" width='12%' style='vertical-align:middle;'>Category</th>
			<th class="mid" width='8%'>Price (USD)</th>
			<th class="mid" width='10%'>Expired</th>
			<th class="mid" width='13%'>Keterangan</th> 
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		if(!empty($rest_d)){
			foreach($rest_d AS $val => $valx){
				$date_now 	= date('Y-m-d');
				$date_exp 	= $valx['exp_price_ref_est'];

				$tgl1x = new DateTime($date_now);
				$tgl2x = new DateTime($date_exp);
				$selisihx = $tgl2x->diff($tgl1x)->days + 1;

				$date_expv 	= date('d M Y', strtotime($date_exp));
				$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
				
				if($status == 'all'){
					$No++;
					$date_exp = "-";
					if(!empty($valx['exp_price_ref_est']) AND $valx['exp_price_ref_est'] <> '0000-00-00'){
						$date_exp = date('d M Y', strtotime($valx['exp_price_ref_est']));
					}
					echo "<tr>";
						echo "<td align='center'>".$No."</td>";
						echo "<td align='right'>".date('d M Y', strtotime($valx['created_date']))."</td>";
						echo "<td>".get_name_acc($valx['id'])."</td>";
						echo "<td>".$valx['nm_category']."</td>";
						echo "<td align='right'>".number_format($valx['harga'],2)."</td>";
						echo "<td align='right'>".$date_exp."</td>";
						echo "<td align='left'>".strtolower($valx['ket_price'])."</td>";
					echo "</tr>";
				}
				if($status == 'expired'){
					if($tgl2x < $tgl1x){
						$No++;
						$date_exp = "-";
						if(!empty($valx['exp_price_ref_est']) AND $valx['exp_price_ref_est'] <> '0000-00-00'){
							$date_exp = date('d M Y', strtotime($valx['exp_price_ref_est']));
						}
						echo "<tr>";
							echo "<td align='center'>".$No."</td>";
							echo "<td>".get_name_acc($valx['id'])."</td>";
							echo "<td>".$valx['nm_category']."</td>";
							echo "<td align='right'>".number_format($valx['harga'],2)."</td>";
							echo "<td align='right'>".$date_exp."</td>";
							echo "<td align='left'>".strtolower($valx['ket_price'])."</td>";
						echo "</tr>";
					}
				}
				if($status == 'less'){
					if($tgl2x >= $tgl1x AND $selisihx <= 7){
						$No++;
						$date_exp = "-";
						if(!empty($valx['exp_price_ref_est']) AND $valx['exp_price_ref_est'] <> '0000-00-00'){
							$date_exp = date('d M Y', strtotime($valx['exp_price_ref_est']));
						}
						echo "<tr>";
							echo "<td align='center'>".$No."</td>";
							echo "<td>".get_name_acc($valx['id'])."</td>";
							echo "<td>".$valx['nm_category']."</td>";
							echo "<td align='right'>".number_format($valx['harga'],2)."</td>";
							echo "<td align='right'>".$date_exp."</td>";
							echo "<td align='left'>".strtolower($valx['ket_price'])."</td>";
						echo "</tr>";
					}
				}
				if($status == 'oke'){
					if($tgl2x >= $tgl1x AND $selisihx > 7){
						$No++;
						$date_exp = "-";
						if(!empty($valx['exp_price_ref_est']) AND $valx['exp_price_ref_est'] <> '0000-00-00'){
							$date_exp = date('d M Y', strtotime($valx['exp_price_ref_est']));
						}
						echo "<tr>";
							echo "<td align='center'>".$No."</td>";
							echo "<td>".get_name_acc($valx['id'])."</td>";
							echo "<td>".$valx['nm_category']."</td>";
							echo "<td align='right'>".number_format($valx['harga'],2)."</td>";
							echo "<td align='right'>".$date_exp."</td>";
							echo "<td align='left'>".strtolower($valx['ket_price'])."</td>";
						echo "</tr>";
					}
				}
				
			}
		}
		?>
	</tbody>
</table><br><br><br>
<!--
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='65%'></td>
		<td align='center'>Dikeluarkan Oleh,</td>
		<td></td>
		<td width='5%'></td>
		<td align='center'>Diterima,</td>
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
-->
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($sts_val); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('LIST PRICE RUTIN '.$sts_val.' '.date('ymdhis', strtotime(date('Y-m-d H:i:s'))).'.pdf' ,'I');