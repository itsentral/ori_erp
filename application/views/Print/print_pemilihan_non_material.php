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

$sql_d		= "SELECT a.* FROM tran_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' GROUP BY a.id_barang ORDER BY a.id ASC";
$rest_d		= $this->db->query($sql_d)->result_array();

$sql_sup	= "SELECT a.* FROM tran_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' GROUP BY a.id_supplier ORDER BY a.id_supplier ASC";
$rest_sup	= $this->db->query($sql_sup)->result_array();
$num_sup 	= $this->db->query($sql_sup)->num_rows();
?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>HASIL PEMILIHAN SUPPLIER <?= $no_rfq; ?></h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class='mid' width='18%' rowspan='2'>MATERIAL NAME</th>
			<th class='mid' width='5%' rowspan='2'>PRICE REF</th>
			<?php
			$wid = 70 / $num_sup;
			$wind2 = $wid/4;
			foreach($rest_sup AS $val => $supplier){
				echo "<th class='mid' width='".$wid."%' colspan='4'>".$supplier['nm_supplier']."</th>";
			}
			?>
			<th class='mid' width='7%' rowspan='2'>HASIL PILIH</th>
		</tr>
		<tr>
			<?php
			foreach($rest_sup AS $val => $supplier2){
				echo "<th class='mid' width='".$wind2."%'>Price</th>";
				echo "<th class='mid' width='".$wind2."%'>MOQ</th>";
				echo "<th class='mid' width='".$wind2."%'>Lead Time</th>";
				echo "<th class='mid' width='".$wind2."%'>TOP</th>";
			} 
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($rest_d AS $val => $result){
			$sql2 		= "SELECT id_supplier, nm_supplier FROM tran_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' AND a.id_barang='".$result['id_barang']."' AND a.status='SETUJU' LIMIT 1";
			$data_sup	= $this->db->query($sql2)->result_array();
			echo "<tr>";
				echo "<td class='mid' >".strtoupper($result['nm_barang'])."</td>";
				echo "<td align='right' class='mid'>".number_format($result['price_ref'])."</td>";
				foreach($rest_sup AS $val => $supplier3){
					$sql_d 		= "SELECT a.* FROM tran_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' AND a.id_barang='".$result['id_barang']."' AND a.id_supplier='".$supplier3['id_supplier']."'";
					$data_supT	= $this->db->query($sql_d)->result_array();
					
					echo "<td align='right' class='mid' width='".$wind2."%'>".number_format($data_supT[0]['price_ref_sup'])."</td>";
					echo "<td align='right' class='mid' width='".$wind2."%'>".number_format($data_supT[0]['moq'],2)."</td>";
					echo "<td align='center' class='mid' width='".$wind2."%'>".number_format($data_supT[0]['lead_time'])."</td>";
					echo "<td align='left' class='mid' width='".$wind2."%'>".$data_supT[0]['top']."</td>";
				}
				echo "<td align='left' >".$data_sup[0]['nm_supplier']."</td>";
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
$mpdf->SetTitle($no_rfq);
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('hasil_pemilihan_supplier_'.$no_rfq.'.pdf' ,'I');