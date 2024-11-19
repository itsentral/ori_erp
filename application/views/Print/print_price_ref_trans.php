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
		<td align='center'><b><h2>LIST PRICE TRANSPORT <?=$sts_val;?></h2></b></td>
	</tr>
</table>
<br>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='5%'>#</th>
			<th class="mid" width='9%'>Tgl</th>
			<th class="mid" width='14%' style='vertical-align:middle;'>Area</th>
			<th class="mid" width='16%' style='vertical-align:middle;'>Tujuan</th>
			<th class="mid" style='vertical-align:middle;'>Truck</th>
			<th class="mid" width='9%'>Price (IDR)</th>
			<th class="mid" width='9%'>Expired</th>
			<th class="mid" width='8%'>Ket</th> 
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		if(!empty($rest_d)){
			foreach($rest_d AS $val => $valx){
				$date_now 	= date('Y-m-d');
				$date_exp 	= $valx['expired'];

				$tgl1x = new DateTime($date_now);
				$tgl2x = new DateTime($date_exp);
				$selisihx = $tgl2x->diff($tgl1x)->days + 1;

				$date_expv 	= date('d-M-Y', strtotime($date_exp));
				$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
				
				if($status == 'all'){
					$No++;
					$date_exp = "-";
					if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
						$date_exp = date('d-M-Y', strtotime($valx['expired']));
					}
					echo "<tr>";
						echo "<td align='center'>".$No."</td>";
						echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_date']))."</td>";
						echo "<td>".strtoupper($valx['area'])."</td>";
						echo "<td>".strtoupper($valx['tujuan'])."</td>";
						echo "<td>".strtoupper($valx['nama_truck'])."</td>";
						echo "<td align='right'>".number_format($valx['price'])."</td>";
						echo "<td align='right'>".$date_exp."</td>";
						echo "<td align='left'>".strtolower($valx['note'])."</td>";
					echo "</tr>";
				}
				if($status == 'expired'){
					if($tgl2x < $tgl1x){
						$No++;
						$date_exp = "-";
						if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
							$date_exp = date('d-M-Y', strtotime($valx['expired']));
						}
						echo "<tr>";
                            echo "<td align='center'>".$No."</td>";
                            echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_date']))."</td>";
                            echo "<td>".strtoupper($valx['area'])."</td>";
                            echo "<td>".strtoupper($valx['tujuan'])."</td>";
                            echo "<td>".strtoupper($valx['nama_truck'])."</td>";
                            echo "<td align='right'>".number_format($valx['price'])."</td>";
                            echo "<td align='right'>".$date_exp."</td>";
                            echo "<td align='left'>".strtolower($valx['note'])."</td>";
                        echo "</tr>";   
					}
				}
				if($status == 'less'){
					if($tgl2x >= $tgl1x AND $selisihx <= 7){
						$No++;
						$date_exp = "-";
						if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
							$date_exp = date('d-M-Y', strtotime($valx['expired']));
						}
						echo "<tr>";
                            echo "<td align='center'>".$No."</td>";
                            echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_date']))."</td>";
                            echo "<td>".strtoupper($valx['area'])."</td>";
                            echo "<td>".strtoupper($valx['tujuan'])."</td>";
                            echo "<td>".strtoupper($valx['nama_truck'])."</td>";
                            echo "<td align='right'>".number_format($valx['price'])."</td>";
                            echo "<td align='right'>".$date_exp."</td>";
                            echo "<td align='left'>".strtolower($valx['note'])."</td>";
                        echo "</tr>";   
					}
				}
				if($status == 'oke'){
					if($tgl2x >= $tgl1x AND $selisihx > 7){
						$No++;
						$date_exp = "-";
						if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
							$date_exp = date('d-M-Y', strtotime($valx['expired']));
						}
						echo "<tr>";
                            echo "<td align='center'>".$No."</td>";
                            echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_date']))."</td>";
                            echo "<td>".strtoupper($valx['area'])."</td>";
                            echo "<td>".strtoupper($valx['tujuan'])."</td>";
                            echo "<td>".strtoupper($valx['nama_truck'])."</td>";
                            echo "<td align='right'>".number_format($valx['price'])."</td>";
                            echo "<td align='right'>".$date_exp."</td>";
                            echo "<td align='left'>".strtolower($valx['note'])."</td>";
                        echo "</tr>";   
					}
				}
				
			}
		}
		?>
	</tbody>
</table>
<br><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='5%'>#</th>
			<th class="mid" width='9%'>Tgl</th>
			<th class="mid" style='vertical-align:middle;'>Country Destination</th>
			<th class="mid" style='vertical-align:middle;'>Shipping</th>
			<th class="mid">Price (USD)</th>
			<th class="mid" width='9%'>Expired</th>
			<th class="mid" width='8%'>Ket</th> 
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		if(!empty($transport_export)){
			foreach($transport_export AS $val => $valx){
				$date_now 	= date('Y-m-d');
				$date_exp 	= $valx['expired'];

				$tgl1x = new DateTime($date_now);
				$tgl2x = new DateTime($date_exp);
				$selisihx = $tgl2x->diff($tgl1x)->days + 1;

				$date_expv 	= date('d-M-Y', strtotime($date_exp));
				$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
				
				if($status == 'all'){
					$No++;
					$date_exp = "-";
					if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
						$date_exp = date('d-M-Y', strtotime($valx['expired']));
					}
					echo "<tr>";
						echo "<td align='center'>".$No."</td>";
						echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_on']))."</td>";
						echo "<td>".strtoupper($valx['country_name'])."</td>";
						echo "<td>".strtoupper($valx['shipping_name'])."</td>";
						echo "<td align='right'>".number_format($valx['price'],2)."</td>";
						echo "<td align='right'>".$date_exp."</td>";
						echo "<td align='left'>".strtolower($valx['note'])."</td>";
					echo "</tr>";
				}
				if($status == 'expired'){
					if($tgl2x < $tgl1x){
						$No++;
						$date_exp = "-";
						if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
							$date_exp = date('d-M-Y', strtotime($valx['expired']));
						}
						echo "<tr>";
                            echo "<td align='center'>".$No."</td>";
                            echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_on']))."</td>";
                            echo "<td>".strtoupper($valx['country_name'])."</td>";
                            echo "<td>".strtoupper($valx['shipping_name'])."</td>";
                            echo "<td align='right'>".number_format($valx['price'],2)."</td>";
                            echo "<td align='right'>".$date_exp."</td>";
                            echo "<td align='left'>".strtolower($valx['note'])."</td>";
                        echo "</tr>";
					}
				}
				if($status == 'less'){
					if($tgl2x >= $tgl1x AND $selisihx <= 7){
						$No++;
						$date_exp = "-";
						if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
							$date_exp = date('d-M-Y', strtotime($valx['expired']));
						}
						echo "<tr>";
                            echo "<td align='center'>".$No."</td>";
                            echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_on']))."</td>";
                            echo "<td>".strtoupper($valx['country_name'])."</td>";
                            echo "<td>".strtoupper($valx['shipping_name'])."</td>";
                            echo "<td align='right'>".number_format($valx['price'],2)."</td>";
                            echo "<td align='right'>".$date_exp."</td>";
                            echo "<td align='left'>".strtolower($valx['note'])."</td>";
                        echo "</tr>";  
					}
				}
				if($status == 'oke'){
					if($tgl2x >= $tgl1x AND $selisihx > 7){
						$No++;
						$date_exp = "-";
						if(!empty($valx['expired']) AND $valx['expired'] <> '0000-00-00'){
							$date_exp = date('d-M-Y', strtotime($valx['expired']));
						}
						echo "<tr>";
                            echo "<td align='center'>".$No."</td>";
                            echo "<td align='right'>".date('d-M-Y', strtotime($valx['created_on']))."</td>";
                            echo "<td>".strtoupper($valx['country_name'])."</td>";
                            echo "<td>".strtoupper($valx['shipping_name'])."</td>";
                            echo "<td align='right'>".number_format($valx['price'],2)."</td>";
                            echo "<td align='right'>".$date_exp."</td>";
                            echo "<td align='left'>".strtolower($valx['note'])."</td>";
                        echo "</tr>"; 
					}
				}
				
			}
		}
		?>
	</tbody>
</table>
<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 1cm;
	}
	td{
		vertical-align: top !important;
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
$mpdf->Output('LIST PRICE TRANSPORT '.$sts_val.' '.date('ymdhis', strtotime(date('Y-m-d H:i:s'))).'.pdf' ,'I');