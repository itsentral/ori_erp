<?php
set_time_limit(0);
ob_start();

$Successno			=0;
$ErrorInfo			=0;
$sroot 				= $_SERVER['DOCUMENT_ROOT'];

function PrintHasilPenawaran($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
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
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$qBQ2 		= "	SELECT * FROM cost_project_header_sales WHERE id_bq = '".$id_bq."' ";
	$dResulBQ2	= mysqli_query($conn, $qBQ2);
	$dHeaderBQ2	= mysqli_fetch_array($dResulBQ2);
	
	$data1BTS 	= SQL_Quo_Edit($id_bq);
	$result1BTS	= mysqli_query($conn, $data1BTS);
	
	$data2BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
	$result2BTS	= mysqli_query($conn, $data2BTS);
	$result2BTSNum	= mysqli_num_rows($result2BTS);
	
	$data3BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
	$result3BTS	= mysqli_query($conn, $data3BTS);
	$result3BTSNum	= mysqli_num_rows($result3BTS);
	
	$data4BTS 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
	$result4BTS	= mysqli_query($conn, $data4BTS);
	$result4BTSNum	= mysqli_num_rows($result4BTS);
	
	$data5BTS 	= "SELECT a.*, b.*, c.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub LEFT JOIN truck c ON b.kendaraan=c.id WHERE a.group_by = 'via' AND b.category = 'lokal' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
	$result5BTS	= mysqli_query($conn, $data5BTS);
	$result5BTSNum	= mysqli_num_rows($result5BTS);
	
	$data6BTS 	= "SELECT series  FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series "; 
	$result6BTS	= mysqli_query($conn, $data6BTS);
	
	while($valx = mysqli_fetch_array($result6BTS)){
		$dtListArray = $valx['series'].", ";
	}
	?>
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='100px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='110' width='100' ></td> 
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITES</h2></b></td>
		</tr>
		<tr>
			<td align='left'><b>
				Quotation No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <br>
				Project &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= strtoupper(strtolower($dHeaderBQ['project'])); ?><br>
				Customer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= strtoupper(strtolower($dHeaderBQ['nm_customer'])); ?><br>
				Job No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= strtoupper($dHeaderBQ2['job_number']); ?><br>
				Product Series &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= strtoupper($dtListArray); ?>
				
			</b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<tbody>
			<tr>
				<td class="text-left headX" colspan='11'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='2' width='22%'>Item Product</th>
				<th class="text-center" width='6%'>Dim 1</th>
				<th class="text-center" width='6%'>Dim 2</th>
				<th class="text-center" width='6%'>Liner</th>
				<th class="text-center" width='8%'>Pressure</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='10%'>Unit Price</th>
				<th class="text-center" width='7%'>Qty</th>
				<th class="text-center" width='8%'>Unit</th>
				<th class="text-center" width='11%'>Total Price (USD)</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$SUM = 0;
			$no = 0;
			while($valx = mysqli_fetch_array($result1BTS)){
				$no++;
				
				// $NegoPersen 	= (!empty($valx['nego']))?$valx['nego']:'10';
				$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
				
				$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
				$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
				
				$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
				
				$nego		= $HrgTot * ($NegoPersen/100);
				$dataSum	= $HrgTot + $nego;
				
				$SUM += $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$dim = number_format($valx['diameter_1'])." x ".number_format($valx['length'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
					$dim = number_format($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".floatval($valx['sudut']);
				}
				elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
					$dim = number_format($valx['diameter_1'])." x ".number_format($valx['diameter_2'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong' OR $valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
					$dim = number_format($valx['diameter_1'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' ){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length']);
				}
				else{$dim = "belum di set";} 
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='2'>".strtoupper($valx['parent_product'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".$valx['liner']."</td>";
					echo "<td align='center'>".$valx['pressure']."</td>";
					echo "<td align='left'>".$dim."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		<?php
		if($result2BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td class="text-left headX" colspan='11'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='7'>Item Product</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price (USD)</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no1=0;
			$SUM1=0;
			while($valx = mysqli_fetch_array($result2BTS)){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php } ?>
		<?php
		if($result3BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td class="text-left headX" colspan='11'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='9'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price (USD)</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no2=0;
			$SUM2=0;
			while($valx = mysqli_fetch_array($result3BTS)){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL PACKING COST</b></td>
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php } ?>
		<?php
		if($result4BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td class="text-left headX" colspan='11'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='6'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Fumigation</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price (USD)</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no3=0;
			$SUM3=0;
			while($valx = mysqli_fetch_array($result4BTS)){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['type'])."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php } ?>
		<?php
		if($result5BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td class="text-left headX" colspan='11'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Item Product</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='2'>Tujuan</th>
				<th class="text-center" colspan='2'>Kendaraan</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price (USD)</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$no4=0;
			$SUM4=0;
			while($valx = mysqli_fetch_array($result5BTS)){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td>".strtoupper($valx['name'])."</td>";
					echo "<td align='center' colspan='3'>".$Areax."</td>";
					echo "<td align='center' colspan='2'>".$Tujuanx."</td>";
					echo "<td align='center' colspan='2'>".$Kendaraanx."</td>";
					echo "<td align='center'>".$Qty4."</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php } ?>
		<tfoot>
			<tr style='background-color: #05b3a3;'>
				<th align='left' colspan='10'>TOTAL (USD)</th>
				<th align='right'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1, 2);?></th>
			</tr>
		</tfoot>
		
		
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
	$mpdf->SetTitle('Penawaran');
	// $mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Hasil_Penawaran_".date('d-m-Y').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintHasilPenawaran2($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$qBQ2 		= "	SELECT * FROM cost_project_header_sales WHERE id_bq = '".$id_bq."' ";
	$dResulBQ2	= mysqli_query($conn, $qBQ2);
	$dHeaderBQ2	= mysqli_fetch_array($dResulBQ2);
	
	$data1BTS 	= SQL_Quo_Edit($id_bq);
	// echo $data1BTS; exit;
	$result1BTS	= mysqli_query($conn, $data1BTS);
	
	$data2BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
	$result2BTS	= mysqli_query($conn, $data2BTS);
	$result2BTSNum	= mysqli_num_rows($result2BTS);
	
	$data3BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
	$result3BTS	= mysqli_query($conn, $data3BTS);
	$result3BTSNum	= mysqli_num_rows($result3BTS);
	
	$data4BTS 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
	$result4BTS	= mysqli_query($conn, $data4BTS);
	$result4BTSNum	= mysqli_num_rows($result4BTS);
	
	$data5BTS 	= "SELECT
						b.*,
						c.* 
					FROM
						cost_project_detail b
						LEFT JOIN truck c ON b.kendaraan = c.id 
					WHERE
						 b.category = 'lokal' 
						AND b.id_bq = '".$id_bq."' 
						AND b.price_total <> 0
					ORDER BY
						b.id ASC";
	$result5BTS	= mysqli_query($conn, $data5BTS);
	$result5BTSNum	= mysqli_num_rows($result5BTS);
	
	$data6BTS 	= "SELECT series  FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series ";
	$result6BTS	= mysqli_query($conn, $data6BTS);
	
	$dtListArray = array();
	foreach($result6BTS AS $val => $valx){
		$dtListArray[$val] = $valx['series'];
	}
	$dtImplode	= "".implode(", ", $dtListArray)."";

	
	$data7BTS 	= "select nm_material from estimasi_total_down WHERE id_bq='".$id_bq."' AND id_category='TYP-0001' GROUP BY id_material ";
	$result7BTS	= mysqli_query($conn, $data7BTS);
	
	$dtListArray2 = array();
	foreach($result7BTS AS $val => $valx){
		$dtListArray2[$val] = $valx['nm_material'];
	}
	$dtImplode2	= "".implode(", ", $dtListArray2)."";
	
	$sql_non_frp 	= "	SELECT 
							a.*,
							b.unit_price
						FROM 
							cost_project_detail a
							LEFT JOIN bq_acc_and_mat b ON a.id_milik = b.id 
						WHERE 
							(b.category='acc' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')
							AND b.id_bq='".$id_bq."' ";
	$rest_non_frp	= mysqli_query($conn, $sql_non_frp);
	$resultNONFRP_Num	= mysqli_num_rows($rest_non_frp);
	
	$sql_material 	= "	SELECT 
							a.*,
							b.* 
						FROM 
							cost_project_detail a
							LEFT JOIN bq_acc_and_mat b ON a.id_milik = b.id 
						WHERE 
							b.category='mat'
							AND b.id_bq='".$id_bq."' ";
	$rest_mat	= mysqli_query($conn, $sql_material);
	$resultMAT_Num	= mysqli_num_rows($rest_mat);

	//OTHER
	$dataOther 	= "SELECT * FROM cost_project_detail WHERE category = 'other' AND id_bq = '".$id_bq."' AND price_total <> 0 ORDER BY id ASC";
	$resultOther	= mysqli_query($conn, $dataOther);
	$resultOtherSum	= mysqli_num_rows($resultOther);
	
	echo "<htmlpageheader>";
	?>

	<table class='header_style' border='0' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td><b><h2>Quotation</h2></b></td>
		</tr>
		</thead>
	</table>
	
	<table class='header_style2' border='0' width='100%' cellpadding='2' style='margin-top:10px; margin-bottom:10px;'>
		<tr>
			<td width='100px' align='center' style='vertical-align:top;' rowspan='7'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='120' width='110' ></td>
			<td rowspan='7' width='20px'></td>
			<td colspan='3' style='background-color: #ffffff !important;color: #0e5ca9;'><b><h3>PT  ORI POLYTEC COMPOSITES</h3></b></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td width='100px'>Quotation No</td>
			<td width='15px'>:</td>
			<td><?= strtoupper($dHeaderBQ2['quo_number']); ?></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['project'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Customer</td>
			<td>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['nm_customer'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Job No.</td>
			<td>:</td>
			<td><?= strtoupper($dHeaderBQ2['job_number']); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Product Series</td>
			<td>:</td>
			<td><?= strtoupper($dtImplode); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Resin Type</td>
			<td style='vertical-align:top;'>:</td> 
			<td style='vertical-align:top;'><?= strtoupper($dtImplode2); ?></td>
		
		</tr>
	</table>
	<?php echo "<htmlpageheader>";?>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3' width='22%'>Item Product</th>
				<th class="text-center" width='7%'>Dim 1</th>
				<th class="text-center" width='7%'>Dim 2</th>
				<th class="text-center" width='10%'>Series</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='9%'>Qty</th>
				<th class="text-center" width='6%'>Unit</th>
				<th class="text-center" width='11%'>Unit Price</th>
				<th class="text-center" width='11%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM = 0;
			$no = 0;
			while($valx = mysqli_fetch_array($result1BTS)){
				$no++;
				
				// $NegoPersen 	= (!empty($valx['nego']))?$valx['nego']:'10';
				$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
				
				$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
				$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
				
				$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
				
				$nego		= $HrgTot * ($NegoPersen/100);
				$dataSum	= $HrgTot + $nego;
				
				$SUM += $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['parent_product'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		<?php
		$SUM_NONFRP = 0;
		if($resultNONFRP_Num > 0){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>BQ NON FRP</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='7'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			while($valx = mysqli_fetch_array($rest_non_frp)){
				$SUM_NONFRP += $valx['price_total'];
				
				// $get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
				$sql_det 	= "	SELECT * FROM accessories WHERE id = '".$valx['caregory_sub']."' ";
				$rest_det	= mysqli_query($conn, $sql_det);
				$get_detail	= mysqli_fetch_array($rest_det);
				
				// $radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
				$nama_acc = "";
				if($valx['category'] == 'baut'){
					$nama_acc = strtoupper($get_detail['nama']).' M '.floatval($get_detail['diameter']).' x '.floatval($get_detail['panjang']).' L '.$radx;
				}
				if($valx['category'] == 'plate'){
					$nama_acc = strtoupper($get_detail['nama'].', '.$get_detail['material']).' x '.floatval($get_detail['thickness'])." T";
				}
				if($valx['category'] == 'gasket'){
					$nama_acc = strtoupper($get_detail['nama'].', '.$get_detail['material']).' x '.floatval($get_detail['thickness'])." T";
				}
				if($valx['category'] == 'lainnya'){
					$nama_acc = strtoupper($get_detail['nama'].', '.$get_detail['material'].' - '.$get_detail['dimensi'].' - '.$get_detail['spesifikasi']);
				}
					
				$qty = $valx['qty'];
				$satuan = $valx['option_type'];
				if($valx['category'] == 'plate'){
					$qty = $valx['weight'];
					$satuan = '1';
				}
				
				echo "<tr>";
					echo "<td colspan='7'>".$nama_acc."</td>";
					echo "<td align='right'>".number_format($qty,2)."</td>";
					echo "<td align='center'>".ucfirst(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan)))."</td>";
					echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL BQ NON FRP</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_MAT = 0;
		if($resultMAT_Num > 0){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>MATERIAL</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='8'>Material Name</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center' colspan='2'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			while($valx = mysqli_fetch_array($rest_mat)){
				if($valx['price_total'] > 0){
					$SUM_MAT += $valx['price_total'];
					echo "<tr>";
						echo "<td colspan='8'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
						echo "<td align='center'>".ucfirst(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type'])))."</td>";
						echo "<td align='right' colspan='2'>".number_format($valx['price_total'],2)."</td>";
					echo "</tr>";
				}
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL MATERIAL</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		?>
		<?php
		if($result2BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='7'>Test Name</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no1=0;
			$SUM1=0;
			while($valx = mysqli_fetch_array($result2BTS)){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result3BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='9'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no2=0;
			$SUM2=0;
			while($valx = mysqli_fetch_array($result3BTS)){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL PACKING COST</b></td>
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result4BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='6'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Fumigation</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no3=0;
			$SUM3=0;
			while($valx = mysqli_fetch_array($result4BTS)){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['type'])."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result5BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Via</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='2'>Destination</th>
				<th class="text-center" colspan='2'>Vehicle</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no4=0;
			$SUM4=0;
			while($valx = mysqli_fetch_array($result5BTS)){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
					$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				}
				else{
					$Kendaraanx = strtoupper($valx['kendaraan']);
				}
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Areax."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Tujuanx."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Kendaraanx."</td>";
					echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
		}
			$SUM_OTHER=0;
			if($resultOtherSum > 0){
				?>
				<tbody>
					<tr>
						<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>OTHER</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='8'>Description</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Price</th>
						<th class="text-center">Total Price</th>
					</tr>
				</tbody>
				<tbody class='body_x'>
					<?php
					while($valx = mysqli_fetch_array($resultOther)){
						$SUM_OTHER += $valx['price_total'];
						
						echo "<tr>";
							echo "<td style='vertical-align:top' colspan='8' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
							echo "<td style='vertical-align:top' align='center'>".number_format($valx['qty'],2)."</td>";
							echo "<td style='vertical-align:top' align='right'>".number_format($valx['price'],2)."</td>";
							echo "<td style='vertical-align:top' align='right'>".number_format($valx['price_total'],2)."</td>";
						echo "</tr>";
					}
					?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL OTHER</b></td>
				<td align='right'><b><?= number_format($SUM_OTHER,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<tfoot>
			<tr>
				<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='10'>TOTAL QUOTATION</th>
				<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP + $SUM_OTHER, 2);?></th>
			</tr>
			<?php
			// if($resultNONFRP_Num > 0){
				// echo "<tr>";
					// echo "<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='9'></th>";
					// echo "<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>IDR</th>";
					// echo "<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'>".number_format($SUM_NONFRP, 2)."</th>";
				// echo "</tr>";
			// }
			?>
		</tfoot>
		
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}
	
	#header{
		position:fixed;
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
		border-color: #ffffff;
		border-collapse: collapse;
	}
	
	.headX{
		background-color: #0e5ca9 !important;
		color: white;
	}
	
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #ea572b;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
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
	$mpdf->SetTitle('Quotation');
	// $mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Quotation ".str_replace('BQ-', '', $id_bq)." ".date('d/m/Y/H/i/s').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintHasilPenawaran3($Nama_APP, $koneksi, $printby, $id_bq, $rev){ 
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
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
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$qBQ2 		= "SELECT * FROM cost_project_header_sales WHERE id_bq = '".$id_bq."' ";
	$dResulBQ2	= mysqli_query($conn, $qBQ2);
	$dHeaderBQ2	= mysqli_fetch_array($dResulBQ2);
	
	$data1BTS 	= "SELECT * FROM laporan_revised_detail WHERE id_bq = '".$id_bq."' AND revised_no = '".$rev."' ";;
	// echo $data1BTS; exit;
	$result1BTS	= mysqli_query($conn, $data1BTS);
	
	$data2BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN laporan_revised_etc b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.revised_no = '".$rev."' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
	$result2BTS	= mysqli_query($conn, $data2BTS);
	$result2BTSNum	= mysqli_num_rows($result2BTS);
	
	$data3BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN laporan_revised_etc b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.revised_no = '".$rev."' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
	$result3BTS	= mysqli_query($conn, $data3BTS);
	$result3BTSNum	= mysqli_num_rows($result3BTS);
	
	$data4BTS 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN laporan_revised_etc b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.revised_no = '".$rev."' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
	$result4BTS	= mysqli_query($conn, $data4BTS);
	$result4BTSNum	= mysqli_num_rows($result4BTS);
	
	$data5BTS 	= "SELECT
						b.*,
						c.* 
					FROM
						laporan_revised_etc b
						LEFT JOIN truck c ON b.kendaraan = c.id 
					WHERE
						 b.category = 'lokal' 
						AND b.revised_no = '".$rev."'
						AND b.id_bq = '".$id_bq."' 
						AND b.price_total <> 0
					";
						// echo $data5BTS;
	$result5BTS	= mysqli_query($conn, $data5BTS);
	$result5BTSNum	= mysqli_num_rows($result5BTS);
	
	$data6BTS 	= "SELECT series  FROM laporan_revised_detail WHERE id_bq = '".$id_bq."' AND revised_no = '".$rev."' GROUP BY series ";
	$result6BTS	= mysqli_query($conn, $data6BTS);
	
	$dtListArray = array();
	foreach($result6BTS AS $val => $valx){
		$dtListArray[$val] = $valx['series'];
	}
	$dtImplode	= "".implode(", ", $dtListArray)."";

	
	$data7BTS 	= "select nm_material from estimasi_total_down WHERE id_bq='".$id_bq."' AND id_category='TYP-0001' GROUP BY id_material ";
	$result7BTS	= mysqli_query($conn, $data7BTS);
	
	$dtListArray2 = array();
	foreach($result7BTS AS $val => $valx){
		$dtListArray2[$val] = $valx['nm_material'];
	}
	$dtImplode2	= "".implode(", ", $dtListArray2)."";
	
	echo "<htmlpageheader>";
	?>

	<table class='header_style' border='0' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td><b><h2>Quotation <span style='color:#0e5ca9;'><?=str_replace('BQ-', '', $id_bq);?></span> for Internal (Revision <?=$rev;?>)</h2></b></td>
		</tr>
		</thead> 
	</table>
	<br>
	
	<table class='header_style2' border='0' width='100%' cellpadding='2'>
		<tr>
			<td width='100px' align='center' style='vertical-align:top;' rowspan='7'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='120' width='110' ></td>
			<td rowspan='7' width='20px'></td>
			<td colspan='3' style='background-color: #ffffff !important;color: #0e5ca9;'><b><h3>PT  ORI POLYTEC COMPOSITES</h3></b></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td width='100px'>Quotation No</td>
			<td width='15px'>:</td>
			<td><?= strtoupper($dHeaderBQ2['quo_number']); ?></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['project'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Customer</td>
			<td>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['nm_customer'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Job No.</td>
			<td>:</td>
			<td><?= strtoupper($dHeaderBQ2['job_number']); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Product Series</td>
			<td>:</td>
			<td><?= strtoupper($dtImplode); ?></td>
		
		</tr>
		<!-- <tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Resin Type</td>
			<td style='vertical-align:top;'>:</td> 
			<td style='vertical-align:top;'><?= strtoupper($dtImplode2); ?></td>
		
		</tr> -->
	</table>
	<br>
	<?php echo "<htmlpageheader>";?>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3' width='22%'>Item Product</th>
				<th class="text-center" width='7%'>Dim 1</th>
				<th class="text-center" width='7%'>Dim 2</th>
				<th class="text-center" width='10%'>Series</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='6%'>Qty</th>
				<th class="text-center" width='9%'>Unit</th>
				<th class="text-center" width='11%'>Unit Price</th>
				<th class="text-center" width='11%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM = 0;
			$no = 0;
			while($valx = mysqli_fetch_array($result1BTS)){
				$no++;
				$SUM += $valx['total_price_last'];
				
				if($valx['product_parent'] == 'pipe' OR $valx['product_parent'] == 'pipe slongsong'){
					$dim = number_format($valx['diameter'])." x ".number_format($valx['length'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['product_parent'] == 'elbow mitter' OR $valx['product_parent'] == 'elbow mould'){
					$dim = number_format($valx['diameter'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".floatval($valx['sudut']);
				}
				elseif($valx['product_parent'] == 'concentric reducer' OR $valx['product_parent'] == 'reducer tee mould' OR $valx['product_parent'] == 'eccentric reducer' OR $valx['product_parent'] == 'reducer tee slongsong' OR $valx['product_parent'] == 'branch joint'){
					$dim = number_format($valx['diameter'])." x ".number_format($valx['diameter2'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['product_parent'] == 'colar' OR $valx['product_parent'] == 'colar slongsong' OR $valx['product_parent'] == 'end cap' OR $valx['product_parent'] == 'flange slongsong' OR $valx['product_parent'] == 'flange mould' OR $valx['product_parent'] == 'equal tee mould' OR $valx['product_parent'] == 'blind flange' OR $valx['product_parent'] == 'equal tee slongsong'){
					$dim = number_format($valx['diameter'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['product_parent'] == 'field joint' OR $valx['product_parent'] == 'shop joint' ){
					$dim = number_format($valx['diameter'])." x ".floatval($valx['length']);
				}
				else{$dim = "belum di set";} 
				
				if($valx['product_parent'] == 'pipe' OR $valx['product_parent'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['product_parent'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".$dim."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($valx['total_price_last'] / $valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($valx['total_price_last'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		<?php
		if($result2BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='7'>Test Name</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no1=0;
			$SUM1=0;
			while($valx = mysqli_fetch_array($result2BTS)){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result3BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='9'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no2=0;
			$SUM2=0;
			while($valx = mysqli_fetch_array($result3BTS)){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL PACKING COST</b></td>
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result4BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='6'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Fumigation</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no3=0;
			$SUM3=0;
			while($valx = mysqli_fetch_array($result4BTS)){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['type'])."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result5BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Via</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='2'>Destination</th>
				<th class="text-center" colspan='2'>Vehicle</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no4=0;
			$SUM4=0;
			while($valx = mysqli_fetch_array($result5BTS)){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
					$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				}
				else{
					$Kendaraanx = strtoupper($valx['kendaraan']);
				}
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Areax."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Tujuanx."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Kendaraanx."</td>";
					echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby))." (".str_replace('BQ-', '', $id_bq)."), ".$today."</i></p>";
	
		?>
		<tfoot>
			<tr>
				<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='10'>TOTAL QUOTATION</th>
				<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1, 2);?></th>
			</tr>
			<tr>
				<th align='left' style='font-size:8px' colspan='11'><i>Printed by : <?=ucfirst(strtolower($printby));?> (<?=str_replace('BQ-', '', $id_bq);?>), <?=$today;?></i></th>
			</tr>
		</tfoot>
		
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}
	
	#header{
		position:fixed;
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
		border-color: #ffffff;
		border-collapse: collapse;
	}
	
	.headX{
		background-color: #0e5ca9 !important;
		color: white;
	}
	
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #ea572b;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
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
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby))." (".str_replace('BQ-', '', $id_bq)."), ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group'); 
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Quotation Rev '.$rev);
	// $mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Quotation for internal revision (".$rev.") ".str_replace('BQ-', '', $id_bq)." ".date('d/m/Y/H/i/s').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintSalesOrder($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
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
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);

	$qBQSO 		= "	SELECT * FROM so_bf_header WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	$dResulBQSO	= mysqli_query($conn, $qBQSO);
	$dHeaderBQSO	= mysqli_fetch_array($dResulBQSO);
	
	$qBQ2 		= "	SELECT * FROM cost_project_header_sales WHERE id_bq = '".$id_bq."' ";
	$dResulBQ2	= mysqli_query($conn, $qBQ2);
	$dHeaderBQ2	= mysqli_fetch_array($dResulBQ2);
	
	$data1BTS 	= SQL_SO($id_bq);
	// echo $data1BTS; exit;
	$result1BTS	= mysqli_query($conn, $data1BTS);
	
	$data2BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
	$result2BTS	= mysqli_query($conn, $data2BTS);
	$result2BTSNum	= mysqli_num_rows($result2BTS);
	
	$data3BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
	$result3BTS	= mysqli_query($conn, $data3BTS);
	$result3BTSNum	= mysqli_num_rows($result3BTS);
	
	$data4BTS 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
	$result4BTS	= mysqli_query($conn, $data4BTS);
	$result4BTSNum	= mysqli_num_rows($result4BTS);
	
	$data5BTS 	= "SELECT
						b.*,
						c.* 
					FROM
						cost_project_detail b
						LEFT JOIN truck c ON b.kendaraan = c.id 
					WHERE
						 b.category = 'lokal' 
						AND b.id_bq = '".$id_bq."' 
						AND b.price_total <> 0
					ORDER BY
						b.id ASC";
	$result5BTS	= mysqli_query($conn, $data5BTS);
	$result5BTSNum	= mysqli_num_rows($result5BTS);
	
	$data6BTS 	= "SELECT series  FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series ";
	$result6BTS	= mysqli_query($conn, $data6BTS);
	
	$dtListArray = array();
	foreach($result6BTS AS $val => $valx){
		$dtListArray[$val] = $valx['series'];
	}
	$dtImplode	= "".implode(", ", $dtListArray)."";

	
	$data7BTS 	= "select nm_material from estimasi_total_down WHERE id_bq='".$id_bq."' AND id_category='TYP-0001' GROUP BY id_material ";
	$result7BTS	= mysqli_query($conn, $data7BTS);
	
	$dtListArray2 = array();
	foreach($result7BTS AS $val => $valx){
		$dtListArray2[$val] = $valx['nm_material'];
	}
	$dtImplode2	= "".implode(", ", $dtListArray2)."";
	
	echo "<htmlpageheader>";
	?>

	<table class='header_style' border='0' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td><b><h2>Sales Order</h2></b></td>
		</tr>
		</thead>
	</table>
	<br>
	
	<table class='header_style2' border='0' width='100%' cellpadding='2'>
		<tr>
			<td width='100px' align='center' style='vertical-align:top;' rowspan='7'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='120' width='110' ></td>
			<td rowspan='7' width='20px'></td>
			<td colspan='3' style='background-color: #ffffff !important;color: #0e5ca9;'><b><h3>PT  ORI POLYTEC COMPOSITES</h3></b></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td width='100px'>SO Number</td>
			<td width='15px'>:</td>
			<td><?= (!empty($dHeaderBQSO['so_number']))?$dHeaderBQSO['so_number'].' / '.$dHeaderBQSO['no_ipp']:$dHeaderBQSO['no_ipp'];?></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['project'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Customer</td>
			<td>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['nm_customer'])); ?></td>
		
		</tr>
		<!-- <tr style='background-color: #ffffff;'>
			<td>Job No.</td>
			<td>:</td>
			<td><?= strtoupper($dHeaderBQ2['job_number']); ?></td>
		</tr> -->
		<tr style='background-color: #ffffff;'>
			<td>Product Series</td>
			<td>:</td>
			<td><?= strtoupper($dtImplode); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Resin Type</td>
			<td style='vertical-align:top;'>:</td> 
			<td style='vertical-align:top;'><?= strtoupper($dtImplode2); ?></td>
		
		</tr>
	</table>
	<br>
	<?php echo "<htmlpageheader>";?>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3' width='22%'>Item Product</th>
				<th class="text-center" width='7%'>Dim 1</th>
				<th class="text-center" width='7%'>Dim 2</th>
				<th class="text-center" width='10%'>Series</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='6%'>Qty</th>
				<th class="text-center" width='9%'>Unit</th>
				<th class="text-center" width='11%'>Unit Price</th>
				<th class="text-center" width='11%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM = 0;
			$no = 0;
			while($valx = mysqli_fetch_array($result1BTS)){
				$no++;
				
				// $NegoPersen 	= (!empty($valx['nego']))?$valx['nego']:'10';
				$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
				
				$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
				$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
				
				$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
				
				$nego		= $HrgTot * ($NegoPersen/100);
				$dataSum	= $HrgTot + $nego;
				
				$SUM += $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$dim = number_format($valx['diameter_1'])." x ".number_format($valx['length'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
					$dim = number_format($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".floatval($valx['sudut']);
				}
				elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
					$dim = number_format($valx['diameter_1'])." x ".number_format($valx['diameter_2'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong' OR $valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
					$dim = number_format($valx['diameter_1'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' ){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length']);
				}
				else{$dim = "belum di set";} 
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['parent_product'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".$dim."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		<?php
		if($result2BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='7'>Test Name</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no1=0;
			$SUM1=0;
			while($valx = mysqli_fetch_array($result2BTS)){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result3BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='9'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no2=0;
			$SUM2=0;
			while($valx = mysqli_fetch_array($result3BTS)){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL PACKING COST</b></td>
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result4BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='6'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Fumigation</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no3=0;
			$SUM3=0;
			while($valx = mysqli_fetch_array($result4BTS)){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['type'])."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result5BTSNum > 0){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Via</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='2'>Destination</th>
				<th class="text-center" colspan='2'>Vehicle</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no4=0;
			$SUM4=0;
			while($valx = mysqli_fetch_array($result5BTS)){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
					$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				}
				else{
					$Kendaraanx = strtoupper($valx['kendaraan']);
				}
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Areax."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Tujuanx."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Kendaraanx."</td>";
					echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<tfoot>
			<tr>
				<th align='left' style='background-color: #0e5ca9; color:white; font-size:12px' colspan='10'>TOTAL QUOTATION</th>
				<th align='right' style='background-color: #0e5ca9; color:white; font-size:12px'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1, 2);?></th>
			</tr>
		</tfoot>
		
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}
	
	#header{
		position:fixed;
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
		border-color: #ffffff;
		border-collapse: collapse;
	}
	
	.headX{
		background-color: #0e5ca9 !important;
		color: white;
	}
	
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #0e5ca9;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
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
	$mpdf->SetTitle('Sales Order');
	// $mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Sales Order ".str_replace('BQ-', '', $id_bq)." ".date('d/m/Y').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintSetButtomPrice($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
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
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$data1BTS 	= SQL_Quo_Edit($id_bq);
	$result1BTS	= mysqli_query($conn, $data1BTS);
	
	$data2BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
	$result2BTS	= mysqli_query($conn, $data2BTS);
	$result2BTSNum	= mysqli_num_rows($result2BTS);
	
	$data3BTS 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
	$result3BTS	= mysqli_query($conn, $data3BTS);
	$result3BTSNum	= mysqli_num_rows($result3BTS);
	
	$data4BTS 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' ORDER BY a.urut ASC ";
	$result4BTS	= mysqli_query($conn, $data4BTS);
	$result4BTSNum	= mysqli_num_rows($result4BTS);
	
	$data5BTS 	= "SELECT
						b.*,
						c.* 
					FROM
						cost_project_detail b
						LEFT JOIN truck c ON b.kendaraan = c.id 
					WHERE
						 b.category = 'lokal' 
						AND b.id_bq = '".$id_bq."'
					ORDER BY
						b.id ASC";
	$result5BTS	= mysqli_query($conn, $data5BTS);
	$result5BTSNum	= mysqli_num_rows($result5BTS);
	
	$data6BTS 	= "SELECT series  FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series ";
	$result6BTS	= mysqli_query($conn, $data6BTS);
	
	$dtListArray = array();
	foreach($result6BTS AS $val => $valx){
		$dtListArray[$val] = $valx['series'];
	}
	$dtImplode	= "".implode(", ", $dtListArray)."";

	
	$data7BTS 	= "select nm_material from estimasi_total_down WHERE id_bq='".$id_bq."' AND id_category='TYP-0001' GROUP BY id_material ";
	$result7BTS	= mysqli_query($conn, $data7BTS);
	
	$dtListArray2 = array();
	foreach($result7BTS AS $val => $valx){
		$dtListArray2[$val] = $valx['nm_material'];
	}
	$dtImplode2	= "".implode(", ", $dtListArray2)."";
	
	
	$sql_non_frp 	= "	SELECT 
							a.*,
							b.unit_price
						FROM 
							cost_project_detail a
							LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material 
						WHERE 
							b.category='acc'
							AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
	$rest_non_frp	= mysqli_query($conn, $sql_non_frp);
	
	$sql_material 	= "	SELECT 
							a.*,
							b.* 
						FROM 
							cost_project_detail a
							LEFT JOIN bq_acc_and_mat b ON a.caregory_sub = b.id_material 
						WHERE 
							b.category='mat'
							AND b.id_bq='".$id_bq."' AND a.id_bq='".$id_bq."' ";
	$rest_mat	= mysqli_query($conn, $sql_material);
	
	echo "<htmlpageheader>";
	?>
	<table class='header_style' border='0' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td><b><h2>Set Buttom Price</h2></b></td>
		</tr>
		</thead>
	</table>
	<br>
	
	<table class='header_style2' border='0' width='100%' cellpadding='2'>
		<tr>
			<td width='100px' align='center' style='vertical-align:top;' rowspan='7'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='120' width='110' ></td>
			<td rowspan='7' width='20px'></td>
			<td colspan='3' style='background-color: #ffffff !important;color: #0e5ca9;'><b><h3>PT  ORI POLYTEC COMPOSITES</h3></b></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td width='100px'>IPP No</td>
			<td width='15px'>:</td>
			<td><?= str_replace('BQ-','',$id_bq); ?></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['project'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Customer</td>
			<td>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ['nm_customer'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>&nbsp;</td>
			<td style='vertical-align:top;'></td> 
			<td style='vertical-align:top;'></td>
		
		</tr>
	</table>
	<br>
	<?php echo "<htmlpageheader>";?>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3' width='16%'>Item Product</th>
				<th class="text-center" width='6%'>Dim 1</th>
				<th class="text-center" width='6%'>Dim 2</th>
				<th class="text-center" width='10%'>Series</th>
				<th class="text-center" width='14%'>Specification</th>
				<th class="text-center" width='6%'>Qty</th>
				<th class="text-center" width='9%'>Unit</th>
				<th class="text-center" width='6%'>Profit</th>
				<th class="text-center" width='10%'>Unit Price</th>
				<th class="text-center" width='6%'>Allow</th>
				<th class="text-center" width='10%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM = 0;
			$SUM0 = 0;
			$no = 0;
			$HPP_Tot = 0;
			while($valx = mysqli_fetch_array($result1BTS)){
				$no++;
				$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
				$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
				
				$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));

				$HPP_Tot += $est_harga * $valx['qty'];
				
				$dataSum	= $HrgTot;
				
				$SUM0 += $est_harga;
				$SUM1 += $HrgTot2;
				$SUM += $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['parent_product'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='right'>".number_format($est_harga,2)."</td>";
					echo "<td align='right'>".number_format($persen,2)."</td>";
					echo "<td align='right'>".number_format($HrgTot2,2)."</td>";
					echo "<td align='right'>".number_format($extra,2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='8'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM0,2);?></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
			<tr class='FootColor'>
				<td colspan='8'><b></b></td>
				<td align='right'><b>Net Profit</b></td>
				<td align='right'><b><?= number_format(($SUM1 - $HPP_Tot)/$SUM1,2) * 100;?> %</b></td>
				<td align='right'><b><?= number_format($SUM1 - $HPP_Tot,2);?></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
			</tr>
		</tbody>
		<?php
		$SUM_NONFRP = 0;
		if(!empty($rest_non_frp)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>BILL OF QUANTITY NON FRP</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='5'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center' colspan='2'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			while($valx = mysqli_fetch_array($rest_non_frp)){
				$SUM_NONFRP += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='5'>".strtoupper(get_name('con_nonmat_new', 'material_name', 'code_group', $valx['caregory_sub']))."</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center' colspan='2'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'])."</td>";
					echo "<td align='right'>".number_format($valx['persen'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'])."</td>";
					echo "<td align='right'>".number_format($valx['extra'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'])."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='11'><b>TOTAL BILL OF QUANTITY NON FRP</b></td> ";
				echo "<td align='center'><b>IDR</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_NONFRP)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_MAT = 0;
		if(!empty($rest_mat)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>MATERIAL</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='5'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
					echo "<th class='text-center' colspan='2'>Satuan</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Profit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Allow</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			while($valx = mysqli_fetch_array($rest_mat)){
				$SUM_MAT += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='5'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
					echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
					echo "<td align='center' colspan='2'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['persen'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['extra'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='12'><b>TOTAL MATERIAL</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		?>
		<?php
		if($result2BTSNum > -1){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='8'>Test Name</th>
				<th class="text-center">Opt</th>
				<th class="text-center">Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no1=0;
			$SUM1=0;
			while($valx = mysqli_fetch_array($result2BTS)){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';

				$Opt 	= ($valx['option_type'] == 'Y')?'YES':'NO';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='8'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Opt."</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='12'><b>TOTAL ENGINEERING COST</b></td> 
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result3BTSNum > -1){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='11'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no2=0;
			$SUM2=0;
			while($valx = mysqli_fetch_array($result3BTS)){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='11'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='12'><b>TOTAL PACKING COST</b></td> 
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result4BTSNum > -1){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='6'>Country</th>
				<th class="text-center" colspan='3'>Shipping</th>
				<th class="text-center" colspan='2'>Unit Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no3=0;
			$SUM3=0;
			while($valx = mysqli_fetch_array($result4BTS)){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center' colspan='3'>".strtoupper($valx['category_sub'])."</td>";
					
					echo "<td align='center'>".$valx['option_type']."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='12'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if($result5BTSNum > -1){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='13'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Via</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='3'>Destination</th>
				<th class="text-center" colspan='3'>Vehicle</th>
				
				<th class="text-center">Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no4=0;
			$SUM4=0;
			while($valx = mysqli_fetch_array($result5BTS)){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
					$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				}
				else{
					$Kendaraanx = strtoupper($valx['kendaraan']);
				}
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Areax."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Tujuanx."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Kendaraanx."</td>";
					
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='12'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<tfoot>
			<tr>
				<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='11'>TOTAL QUOTATION</th>
				<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>USD</th>
				<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT, 2);?></th>
			</tr>
			<?php
			if(!empty($rest_non_frp)){
				echo "<tr>";
					echo "<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='11'></th>";
					echo "<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>IDR</th>";
					echo "<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'>".number_format($SUM_NONFRP, 2)."</th>";
				echo "</tr>";
			}
			?>
		</tfoot>
		
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}
	
	#header{
		position:fixed;
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
		border-color: #ffffff;
		border-collapse: collapse;
	}
	
	.headX{
		background-color: #0e5ca9 !important;
		color: white;
	}
	
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #ea572b;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
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
	$mpdf->SetTitle('Set Buttom Price');
	// $mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Set_buttom_price_".str_replace('BQ-', '', $id_bq)."_".date('d/m/Y/H/i/s').".pdf" ,'I');

	//exit;
	//return $attachment;
}

?>