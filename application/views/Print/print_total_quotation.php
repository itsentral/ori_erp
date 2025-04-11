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
		<td align='center'><b><h2>LIST QUOTATION <?=date('Y');?></h2></b></td>
	</tr>
</table>
<br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='5%'>#</th>
			<th class="mid" width='9%' style='vertical-align:middle;'>IPP</th>
			<th class="mid" width='30%' style='vertical-align:middle;'>CUSTOMER</th>
			<th class="mid" >PROJECT</th>
			<th class="mid" width='10%' >App Quo.</th>
			<th class="mid" width='10%'>Price</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		if(!empty($result)){
            $SUM = 0;
			foreach($result AS $val => $valx){ 
                $get_revisi_max     = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>'BQ-'.$valx['no_ipp']))->result();
                $revised_no         = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

                $get_nilai          = $this->db->select('price_project AS total_quo')->get_where('laporan_revised_header',array('id_bq'=>'BQ-'.$valx['no_ipp'], 'revised_no'=>$revised_no))->result();
                $nilai_quotation    = (!empty($get_nilai))?$get_nilai[0]->total_quo:0;

                $SUM += $nilai_quotation;

                $No++;
                echo "<tr>";
                    echo "<td style='vertical-align:top;' align='center'>".$No."</td>";
                    echo "<td style='vertical-align:top;' align='center'>".strtoupper($valx['no_ipp'])."</td>";
                    echo "<td style='vertical-align:top;'>".strtoupper($valx['nm_customer'])."</td>";
                    echo "<td style='vertical-align:top;'>".strtoupper($valx['project'])."</td>";
                    echo "<td style='vertical-align:top;' align='center'>".date('d-M-Y', strtotime($valx['app_quo_date']))."</td>";
                    echo "<td style='vertical-align:top;' align='right'>".number_format($nilai_quotation,2)."</td>";
                echo "</tr>";
			}
            echo "<tr>";
                echo "<td align='center'></td>";
                echo "<td colspan='4'>TOTAL QUOTATION</td>";
                echo "<td align='right'>".number_format($SUM,2)."</td>";
            echo "</tr>";
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
$mpdf->SetTitle('Quotation'); 
$mpdf->AddPageByArray([
	'margin-left' => 5,
	'margin-right' => 5,
	'margin-top' => 5,
	'margin-bottom' => 5
]);
// $mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('quotation.pdf' ,'I');