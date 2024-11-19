<?php
set_time_limit(0);
ob_start();

$Successno			=0;
$ErrorInfo			=0;
$sroot 				= $_SERVER['DOCUMENT_ROOT'];

//NEW SPK 
function PrintSPKPlanning($Nama_APP, $id_bq, $koneksi, $printby){ 
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	// print_r($KONN); exit;
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot. "/application/libraries/PHPMailer/PHPMailerAutoload.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('D, d-M-Y H:i:s');
    
    $no_ipp = str_replace('BQ-','',$id_bq);
    
	$qHeader	= "	SELECT 
						a.*
					FROM 
						warehouse_planning_header a 					
					WHERE 
						a.no_ipp='".$no_ipp."'
						LIMIT 1"; 
	// echo $qHeader2; 
	$dResult	= mysqli_query($conn, $qHeader);
	$dHeader	= mysqli_fetch_array($dResult);
	
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>SPK MATERIAL PLANNING</h2></b></td>
			<td>Rev</td>
			<td></td>
		</tr>
		<tr>
			<td>Due Date</td>
			<td></td>
		</tr>
	</table>
	<table class="gridtable2" border='0' width='100%' >
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td width='20%'>Production Date</td>
			<td width='1%'>:</td>
			<td width='29%'></td>
			<td width='20%'>SO Number</td>
			<td width='1%'>:</td>
			<td width='29%'></td>
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
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='5%'>No</th>
				<th width='20%'>Material Id</th>
				<th>Material Name</th>
				<th width='12%'>Qty</th>
				<th width='12%'>Lot/Batch Num</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tDetailLiner	= "SELECT *  FROM warehouse_planning_detail WHERE no_ipp='".$no_ipp."' ORDER BY id ASC ";
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
        // echo $tDetailLiner; exit;
        $no=0;
		while($valx = mysqli_fetch_array($dDetailLiner)){
            $no++;
			?>
			<tr>
				<td><?= $no;?></td>
				<td><?= $valx['idmaterial'];?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= number_format($valx['use_stock'], 3);?> Kg</td>
				<td></td>
			</tr>
			<?php
		}
		?>
		</tbody>
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
	
	table.cooltabs {
		font-size:12px;
		font-family: verdana,arial,sans-serif;
	}
	
	table.cooltabs th.reg {
		font-family: verdana,arial,sans-serif;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
	}
	table.cooltabs td.reg {
		font-family: verdana,arial,sans-serif;
		border-radius: 5px 5px 5px 5px;
		padding: 5px;
	}
	#cooltabs {
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
		width: 800px;
		height: 20px;
	}
	#cooltabs2{
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
		width: 180px;
		height: 10px;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	#cooltabshead{
		font-size:12px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 0 0;
		background: #dfdfdf;
		padding: 5px;
		width: 162px;
		height: 10px;
		float:left;
	}
	#cooltabschild{
		font-size:10px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 0 0 5px 5px;
		padding: 5px;
		width: 162px;
		height: 10px;
		float:left;
	}
	p {
		margin: 0 0 0 0;
	}
	p.pos_fixed {
		font-family: verdana,arial,sans-serif;
		position: fixed;
		top: 30px;
		left: 230px;
	}
	p.pos_fixed2 {
		font-family: verdana,arial,sans-serif;
		position: fixed;
		top: 589px;
		left: 230px;
	}
	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000044;
	}
	.barcodecell {
		text-align: center;
		vertical-align: middle;
		position: fixed;
		top: -10px;
		right: 10px;
	}
	.barcodecell2 {
		text-align: center;
		vertical-align: middle;
		position: fixed;
		top: 548px;
		right: 10px;
	}
	p.barcs {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		position: fixed;
		top: 38px;
		right: 115px;
	}
	p.barcs2 {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		position: fixed;
		top: 591px;
		right: 115px;
	}
	p.pt {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 62px;
		left: 5px;
	}
	p.alamat {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 71px;
		left: 5px;
	}
	p.tlp {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 80px;
		left: 5px;
	}
	p.pt2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 596px;
		left: 5px;
	}
	p.alamat2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 605px;
		left: 5px;
	}
	p.tlp2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 614px;
		left: 5px;
	}
	#hrnew {
		border: 0;
		border-bottom: 1px dashed #ccc;
		background: #999;
	}
</style>

	
	<?php
	
	$html = ob_get_contents(); 
	// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$no_ipp."</i></p>";
	// exit;
	ob_end_clean(); 
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('SPK Of Material Planning');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);		
	$mpdf->Output("SPK Material Planning ".$no_ipp.' '.date('YmdHis').'.pdf' ,'I');

	//exit;
	//return $attachment;
}

function PrintSPKPurchase($Nama_APP, $id_bq, $koneksi, $printby){ 
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	// print_r($KONN); exit;
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot. "/application/libraries/PHPMailer/PHPMailerAutoload.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('D, d-M-Y H:i:s');
    
    $no_ipp = str_replace('BQ-','',$id_bq);
    
	$qHeader	= "	SELECT 
						a.*
					FROM 
						tran_material_purchase_header a 					
					WHERE 
						a.no_po='".$no_ipp."'
						LIMIT 1"; 
	// echo $qHeader2; 
	$dResult	= mysqli_query($conn, $qHeader);
	$dHeader	= mysqli_fetch_array($dResult);
	
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='2' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>PURCHASE ORDER</h2></b></td>
		</tr>
	</table>
	<table class="gridtable2" border='0' width='100%' >
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td width='20%'>PO Date</td>
			<td width='1%'>:</td>
			<td width='29%'><?= date('d M Y', strtotime($dHeader['created_date']));?></td>
			<td width='20%'></td>
			<td width='1%'></td>
			<td width='29%'></td>
		</tr>
		<tr>
			<td width='20%'>PO Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?=$dHeader['no_po'];?></td>
			<td width='20%'></td>
			<td width='1%'></td>
			<td width='29%'></td>
		</tr>
		<tr>
			<td width='20%'>Supplier Name</td>
			<td width='1%'>:</td>
			<td width='29%'><?=$dHeader['nm_supplier'];?></td>
			<td width='20%'></td>
			<td width='1%'></td>
			<td width='29%'></td>
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
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='5%'>No</th>
				<th width='20%'>Material Id</th>
				<th>Material Name</th>
				<th width='12%'>Wight</th>
				<th width='12%'>Real Purchase</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tDetailLiner	= "SELECT *  FROM tran_material_purchase_detail WHERE no_po='".$no_ipp."' AND deleted='N' ";
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
        // echo $tDetailLiner; exit;
		$no=0;
		$SumM = 0;
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$no++;
			$SumM += $valx['qty'];
			?>
			<tr>
				<td><?= $no;?></td>
				<td><?= $valx['idmaterial'];?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= number_format($valx['qty'], 2);?> Kg</td>
				<td></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td colspan='3'><b>SUM MATERIAL PURCHASE</b></td>
			<td align='right'><b><?= number_format($SumM, 2);?> Kg</b></td>
			<td></td>
		</tr>
		</tbody>
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
	
	table.cooltabs {
		font-size:12px;
		font-family: verdana,arial,sans-serif;
	}
	
	table.cooltabs th.reg {
		font-family: verdana,arial,sans-serif;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
	}
	table.cooltabs td.reg {
		font-family: verdana,arial,sans-serif;
		border-radius: 5px 5px 5px 5px;
		padding: 5px;
	}
	#cooltabs {
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
		width: 800px;
		height: 20px;
	}
	#cooltabs2{
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
		width: 180px;
		height: 10px;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	#cooltabshead{
		font-size:12px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 0 0;
		background: #dfdfdf;
		padding: 5px;
		width: 162px;
		height: 10px;
		float:left;
	}
	#cooltabschild{
		font-size:10px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 0 0 5px 5px;
		padding: 5px;
		width: 162px;
		height: 10px;
		float:left;
	}
	p {
		margin: 0 0 0 0;
	}
	p.pos_fixed {
		font-family: verdana,arial,sans-serif;
		position: fixed;
		top: 30px;
		left: 230px;
	}
	p.pos_fixed2 {
		font-family: verdana,arial,sans-serif;
		position: fixed;
		top: 589px;
		left: 230px;
	}
	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000044;
	}
	.barcodecell {
		text-align: center;
		vertical-align: middle;
		position: fixed;
		top: -10px;
		right: 10px;
	}
	.barcodecell2 {
		text-align: center;
		vertical-align: middle;
		position: fixed;
		top: 548px;
		right: 10px;
	}
	p.barcs {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		position: fixed;
		top: 38px;
		right: 115px;
	}
	p.barcs2 {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		position: fixed;
		top: 591px;
		right: 115px;
	}
	p.pt {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 62px;
		left: 5px;
	}
	p.alamat {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 71px;
		left: 5px;
	}
	p.tlp {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 80px;
		left: 5px;
	}
	p.pt2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 596px;
		left: 5px;
	}
	p.alamat2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 605px;
		left: 5px;
	}
	p.tlp2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 614px;
		left: 5px;
	}
	#hrnew {
		border: 0;
		border-bottom: 1px dashed #ccc;
		background: #999;
	}
</style>

	
	<?php
	
	$html = ob_get_contents(); 
	// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$no_ipp."</i></p>";
	// exit;
	ob_end_clean(); 
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('SPK Of Material Purchase');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);		
	$mpdf->Output("SPK Material Purchase ".$no_ipp.' '.date('YmdHis').'.pdf' ,'I');
	//exit;
	//return $attachment;
}

?>