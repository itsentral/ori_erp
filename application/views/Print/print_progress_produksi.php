<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf		= new mPDF('utf-8','A4');

set_time_limit(0);
ini_set('memory_limit','1024M');

ob_start();
date_default_timezone_set('Asia/Jakarta');
$today 		= date('l, d F Y [H:i:s]');
?>

<table class="gridtable" border='1' width='100%' cellpadding='2'>
	<tr>
		<td width='80px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
		<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
	</tr>
	<tr>
		<td align='center'><b><h3>PROGRESS PRODUCTION <?= str_replace('BQ-','',$id_bq);?></h3></b></td>
	</tr>
</table>
<br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead id='head_table'>
		<tr class='bg-blue'>
			<th class="text-center" style='width: 3%;'class="no-sort">#</th>
			<th class="text-left" style='width: 15%;'>PRODUCT TYPE</th>
			<th class="text-center" style='width: 10%;'>NO SPK</th>
			<th class="text-left" style='width: 12%;'>SPEC</th>
			<th class="text-left">PRODUCT NAME</th>
			<th class="text-center" style='width: 8%;'>QTY ORDER</th>
			<th class="text-center" style='width: 8%;'>QTY ACTUAL</th>
			<th class="text-center" style='width: 8%;'>QTY BALANCE</th>
			<th class="text-center" style='width: 8%;'>QTY DELIVERY</th>
			<th class="text-center" style='width: 8%;'>QTY FG</th>
			<th class="text-center" style='width: 8%;'>PROGRESS</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$a=0;
			if(!empty($rowD)){
				foreach($rowD AS $val => $valx){
					$a++;
					//check selain field joint
					if($valx['typeProduct'] != 'field'){
						$sqlCheck2 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'daycode !='=>NULL))->result();
						
						//check delivery
						$sqlCheck3 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'kode_delivery !='=>NULL))->result();
						$QTY_DELIVERY	=$sqlCheck3[0]->Numc;

						$QTY_PRODUCT 		= $valx['qty'];
						$QTY 		= $valx['qty'];
						$ACT 		= $sqlCheck2[0]->Numc;
						$ACT_OUT 	= $sqlCheck2[0]->Numc;
						$balance 	= $QTY - $ACT;
						$progress = 0;
						if($ACT != 0 AND $QTY != 0){
						$progress 	= ($ACT/$QTY) *(100);
						}
						if($progress == 100){
							$bgc = '#75e975';
						}
						else if($progress == 0){
							$bgc = '#f65b5b';
						}
						else{
							$bgc = '#67a4ff';
						}

						$bal_dev	=$ACT_OUT-$QTY_DELIVERY;
					}

					if($valx['typeProduct'] == 'field'){
						//check selain shop joint
						$sqlCheck2 	= $this->db->select('SUM(qty) as Numc')->get_where('outgoing_field_joint', array('id_milik'=>$valx['id_milik'],'no_ipp'=>str_replace('PRO-','',$valx['id_produksi'])))->result();
						$QTY_PRODUCT 		= $valx['qty'];
						$QTY 		= $valx['qty'];
						$ACT 		= $sqlCheck2[0]->Numc;
						$ACT_OUT 	= number_format($sqlCheck2[0]->Numc);
						$balance 	= $QTY - $ACT;
						$progress = 0;
						if($ACT != 0 AND $QTY != 0){
						$progress 	= ($ACT/$QTY) *(100);
						}
						if($progress == 100){
							$bgc = '#75e975';
						}
						else if($progress == 0){
							$bgc = '#f65b5b';
						}
						else{
							$bgc = '#67a4ff';
						}

						$bal_dev	=$ACT_OUT-$QTY_DELIVERY;
					}

					//check field joint
					if (in_array($valx['comp'], NotInProductArray())) {
						$QTY 		= (!empty($GET_MATERIAL_FIELD_EST[$valx['id_uniq']]['est']))?number_format($GET_MATERIAL_FIELD_EST[$valx['id_uniq']]['est'] * $QTY_PRODUCT,4):0;
						$ACT 		= (!empty($GET_MATERIAL_FIELD[$valx['id_uniq']]['est']))?number_format($GET_MATERIAL_FIELD[$valx['id_uniq']]['est'],4):0;
						$QTY_ 		= (!empty($GET_MATERIAL_FIELD_EST[$valx['id_uniq']]['est']))?$GET_MATERIAL_FIELD_EST[$valx['id_uniq']]['est'] * $QTY_PRODUCT:0;
						$ACT_ 		= (!empty($GET_MATERIAL_FIELD[$valx['id_uniq']]['est']))?$GET_MATERIAL_FIELD[$valx['id_uniq']]['est']:0;
						$ACT_OUT 	= (!empty($GET_MATERIAL_FIELD[$valx['id_uniq']]['out']))?number_format($GET_MATERIAL_FIELD[$valx['id_uniq']]['out'],4):0;
						$ACT_OUT_ 	= (!empty($GET_MATERIAL_FIELD[$valx['id_uniq']]['out']))?$GET_MATERIAL_FIELD[$valx['id_uniq']]['out']:0;
						$balance 	= number_format($QTY_ - $ACT_OUT_,4);
						$progress = 0;
						if($ACT_OUT_ != 0 AND $QTY_ != 0){
						$progress 	= ($ACT_OUT_/$QTY_) *(100);
						}
						// if($progress == 100){
						// 	$bgc = '#75e975';
						// }
						// else if($progress == 0){
						// 	$bgc = '#f65b5b';
						// }
						// else{
						// 	$bgc = '#67a4ff';
						// }

						$bgc = 'transparant';
						$progress = '';

						$bal_dev	=$ACT_OUT-$QTY_DELIVERY;
					}
					echo "<tr>";
						echo "<td align='center'>".$a."</td>";
						echo "<td>".strtoupper($valx['comp'])."</td>";
						echo "<td align='center'>".strtoupper($valx['no_spk'])."</td>";
						echo "<td>".spec_fd($valx['id_uniq'], $HelpDet)."</td>";
						echo "<td>".$valx['id_product']."</td>";
						echo "<td align='center'>".$QTY."</td>";
						echo "<td align='center'>".$ACT_OUT."</td>";
						echo "<td align='center'>".$balance."</td>";
						echo "<td align='center'>".$QTY_DELIVERY."</td>";
						echo "<td align='center'>".$balance."</td>";
						echo "<td align='center' style='background-color: ".$bgc.";'><b>".$progress." %</b></td>";
					echo "</tr>";
				}
			}
			else{
				echo "<tr>";
					echo "<td colspan='9'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
				echo "</tr>";
			}
		?>
	</tbody>
</table>
<style type="text/css">
@page {
	margin-top: 1cm;
	margin-left: 1cm;
	margin-right: 1cm;
	margin-bottom: 0.5cm;
}
p.foot1 {
	font-family: verdana,arial,sans-serif;
	font-size:10px;
}
.font{
	font-family: verdana,arial,sans-serif;
	font-size:14px;
}
.fontheader{
	font-family: verdana,arial,sans-serif;
	font-size:13px;
	color:#333333;
	border-width: 1px;
	border-color: #666666;
	border-collapse: collapse;
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
	padding: 5px;
	border-style: solid;
	border-color: #666666;
	background-color: #f2f2f2;
}
table.gridtable th.head {
	border-width: 1px;
	padding: 5px;
	border-style: solid;
	border-color: #666666;
	background-color: #7f7f7f;
	color: #ffffff;
}
table.gridtable td {
	border-width: 1px;
	padding: 5px;
	border-style: solid;
	border-color: #666666;
	background-color: #ffffff;
}
table.gridtable td.cols {
	border-width: 1px;
	padding: 5px;
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
	padding: 5px;
	border-style: none;
	border-color: #666666;
	background-color: #f2f2f2;
}
table.gridtable2 th.head {
	border-width: 1px;
	padding: 5px;
	border-style: none;
	border-color: #666666;
	background-color: #7f7f7f;
	color: #ffffff;
}
table.gridtable2 td {
	border-width: 1px;
	padding: 5px;
	border-style: none;
	border-color: #666666;
	background-color: #ffffff;
}
table.gridtable2 td.cols {
	border-width: 1px;
	padding: 5px;
	border-style: none;
	border-color: #666666;
	background-color: #ffffff;
}
#space{
	padding: 3px;
	width: 180px;
	height: 1px;
}
p {
	margin: 0 0 0 0;
}

</style>

<?php
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";

// exit;
$html = ob_get_contents(); 
ob_end_clean(); 
// flush();
// $mpdf->SetWatermarkText('ORI Group');

$mpdf->showWatermarkText = true;
$mpdf->SetTitle(str_replace('BQ-','',$id_bq));
$mpdf->AddPage('L');
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output("RESULT PROGRESS PRODUKSI ".str_replace('BQ-','',$id_bq)." ".date('dmYHis').".pdf" ,'I');

