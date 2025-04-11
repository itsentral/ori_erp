<?php
	
	// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
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
	
	echo "<htmlpageheader>";
	// exit;
	?>
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $rest_ipp[0]['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>SPK BOOKING DEADSTOK</h2></b></td>
			<td>Rev</td>
			<td></td>
		</tr>
		<tr>
			<td>Due Date</td>
			<td></td>
		</tr>
		<thead>
	</table>
	<table class="gridtable2" border='0' width='100%' >
		<tr>
			<td></td>
			<td></td>
			<td colspan='3'></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td colspan='3'></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td colspan='3'></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td colspan='3'></td>
		</tr>
		<tr>
			<td width='20%'>IPP Number / SO</td>
			<td width='1%'>:</td>
			<td colspan='3'><?= $no_ipp.' / '.$no_so; ?></td>
		</tr>
		<tr>
			<td>No SPK</td>
			<td>:</td>
			<td colspan='3'><?= $no_spk; ?></td>
		</tr>
		
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table><br>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th>#</th>
				<th>Product</th>
				<th>Type</th>
				<th>Spec</th>
				<th>Resin</th>
				<th>Length</th>
				<th>Qty</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($detail AS $val => $valx){$val++;
                $id_deadstok = $valx['id_product_deadstok'];

                $getDeadstok = $this->db->get_where('deadstok',array('id_product'=>$id_deadstok))->result_array();

				echo "<tr>";
					echo "<td align='center'>".$val."</td>";
					echo "<td>".$getDeadstok[0]['product_name']."</td>";
					echo "<td align='center'>".$getDeadstok[0]['type_std']."</td>";
					echo "<td align='center'>".$getDeadstok[0]['product_spec']."</td>";
					echo "<td align='center'>".$getDeadstok[0]['resin']."</td>";
					echo "<td align='center'>".$getDeadstok[0]['length']."</td>";
					echo "<td align='center'>".$valx['qty_booking']."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<div id='space'></div>
	<div id='space'></div>
	<div id='space'></div>
	<div id='space'></div>
	<table class="gridtable3" width='100%' border='0' cellpadding='2'>
		<tr>
			<td>Dibuat,</td>
			<td></td>
			<td>Diperiksa,</td>
			<td></td>
			<td>Diketahui,</td>
			<td></td>
		</tr>
		<tr>
			<td height='35px'></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Ka. Regu</td>
			<td></td>
			<td>SPV Produksi</td>
			<td></td>
			<td>Dept Head</td>
			<td></td>
		</tr>
	</table>
	<style type="text/css">
	@page {
		margin-top: 1 cm;
		margin-left: 1 cm;
		margin-right: 1 cm;
		margin-bottom: 1 cm;
		margin-footer: 0 cm
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
		font-size:11px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 6px;
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

	table.gridtable3 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
		color:#333333;
	}
	table.gridtable3 th {
		border-width: 1px;
		padding: 8px;
	}
	table.gridtable3 th.head {
		border-width: 1px;
		padding: 8px;
		color: #ffffff;
	}
	table.gridtable3 td {
		border-width: 1px;
		padding: 8px;
		background-color: #ffffff;
	}
	table.gridtable3 td.cols {
		border-width: 1px;
		padding: 8px;
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
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby))."/".$id_booking.", ".$today."</i></p>";
	
	$html = ob_get_contents(); 
	ob_end_clean(); 
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Booking Deadstok');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("spk-booking-deadstok-".$id_booking.".pdf" ,'I');