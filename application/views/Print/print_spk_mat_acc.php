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
	
	$sql_ipp 	= "SELECT * FROM production WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
	$rest_ipp	= $this->db->query($sql_ipp)->result_array();
	
	$whereFilter = "AND category='".$tanda."'";
	if($tanda == 'acc'){
		$whereFilter = "AND category <> 'mat'";
	}
	
	$sql_detail 	= "SELECT * FROM production_acc_and_mat WHERE id_bq = '".$id_bq."' ".$whereFilter." ";
	$rest_detail	= $this->db->query($sql_detail)->result_array();
	
	$judul = "SPK BQ NON FRP";
	$td2 = "";
	if($tanda == 'mat'){
		$judul = "SPK MATERIAL";
		$td2 = "(Kg)";
	}
	
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
			<td align='center' rowspan='2'><b><h2><?=$judul;?></h2></b></td>
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
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td colspan='3'><?= $rest_ipp[0]['no_ipp'].' / '.get_nomor_so($rest_ipp[0]['no_ipp']); ?></td>
		</tr>
		<tr>
			<td>Customer Name</td>
			<td>:</td>
			<td colspan='3'><?= $rest_ipp[0]['nm_customer']; ?></td>
		</tr>
		
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='5%'>No</th>
				<th align='left' width='29%'>Material Name</th>
				<th width='15%'>Qty <?=$td2;?></th>
				<th width='15%'>Actual Qty</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($rest_detail AS $val => $valx){$val++;
				$nama = strtoupper(get_name_acc($valx['id_material']));
				$qty = number_format($valx['qty']);
				if($tanda == 'mat'){
					$nama = strtoupper(get_name('raw_materials','nm_material','id_material',$valx['id_material']));
					$qty = number_format($valx['qty'],2);
				}
				echo "<tr>";
					echo "<td align='center'>".$val."</td>";
					echo "<td>".$nama."</td>";
					echo "<td align='center'>".$qty."</td>";
					echo "<td align='center'></td>";
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
		font-size:9px;
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
		font-size:9px;
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
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	$html = ob_get_contents(); 
	ob_end_clean(); 
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('SPK Of Production');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("SPK_".strtoupper($tanda)."_".str_replace('BQ-', '', $id_bq)."_".date('dmYHis').".pdf" ,'I');