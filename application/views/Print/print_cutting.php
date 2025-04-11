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
			<td align='center'><b><h2>SPK CUTTING</h2></b></td>
		</tr>
	</table>
	<br><br>
	<table class="gridtable2" border='0' width='100%' >
		<tr>
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= str_replace('BQ-','',$result[0]->id_bq); ?></td>
			<td width='20%'>Print Date</td>
			<td width='1%'>:</td>
			<td width='29%'><?= date('d F Y', strtotime(date('Y-m-d'))); ?></td>
		</tr>
		<tr>
			<td>Qty SO</td>
			<td>:</td>
			<td><?= $qty_order; ?></td>
			<td>Qty SPK</td>
			<td>:</td>
			<td>1</td>
		</tr>
        <tr>
			<td>Cutting Plan (mm)</td>
			<td>:</td>
			<td><?= strtoupper($cutting); ?></td>
			<td>Panjang (mm)</td>
			<td>:</td>
			<td><?= $sum_split;?></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='5%'>No</th>
				<th width='15%'>Cycletime</th>
				<th width='10%'>MP</th>
				<th>Tahapan Process</th>
				<th width='10%'>Tanggal</th>
				<th width='10%'>Start</th>
				<th width='10%'>Finish</th>
				<th width='10%'>MP</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$COUNT = COUNT(json_decode($result[0]->tahapan));
				if(!empty($result[0]->tahapan)){
					$nomor = 0;
					foreach (json_decode($result[0]->tahapan) as $value) {
						if(!empty($value)){
							$nomor++;
							echo "<tr>";
								if($nomor == 1){
								echo "<td align='center' rowspan='".$COUNT."'>".$nomor."</td>";
								echo "<td align='center' rowspan='".$COUNT."'>".$result[0]->tt_ct."</td>";
								echo "<td align='center' rowspan='".$COUNT."'>".$result[0]->tt_mp."</td>";
								}
								echo "<td>".strtoupper($value)."</td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td></td>";
								echo "<td></td>";
							echo "</tr>";
						}
					}
				}
			?>
		</tbody>
	</table>
	<!-- ========================================================================================================= -->
<!-- ==========================================SPK LOOSE====================================================== -->
<!-- ========================================================================================================= -->
<?php
echo "<pagebreak />";
?>
<table class="gridtable" border='1' width='100%' cellpadding='2'>
    <tr>
        <td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
        <td align='center' height='50%'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
    </tr>
    <tr>
        <td align='center' height='50%'><b><h2>SURAT PERINTAH KERJA CUTTING</h2></b></td>
    </tr>
</table><br>
<br><br>
<table class="gridtable2" border='0' width='100%' >
	<tr>
		<td width='20%'>No SPK</td>
		<td width='1%'>:</td>
		<td width='29%'><?= $result_pro[0]->no_spk; ?></td>
		<td width='20%'>Qty</td>
		<td width='1%'>:</td>
		<td width='29%'>1</td>
	</tr>
	<tr>
		<td>Start Produksi</td>
		<td>:</td>
		<td></td>
		<td>Man Power</td>
		<td>:</td>
		<td><?=$result[0]->tt_mp;?></td>
	</tr>
	<tr>
		<td>Finish Produksi</td>
		<td>:</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>
<br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
    <thead align='center'>
        <tr>
            <th width='5%'>#</th>
            <th width='20%'>ID Product</th>
            <th>Spec Product</th>
            <th width='12%'>SO</th>
            <th width='8%'>Mesin</th>
            <th width='22%'>Catatan Masalah Product/Process</th>
        </tr>
    </thead>
    <tbody>
        <?php
			$nomor_so = get_nomor_so(str_replace('BQ-','',$result[0]->id_bq));
            foreach ($result_cutting as $key => $value) { $key++;
               
                echo "<tr>";
					if($key == 1){
                    echo "<td align='center' rowspan='".COUNT($result_cutting)."'>".$key."</td>";
					}
                    echo "<td align='center' style='height:100px;'>".$result_pro[0]->product_code_cut.".".$key."</td>";
                    echo "<td>".strtoupper($value['id_category'])." ".number_format($value['diameter_1'])." x ".number_format($value['length_split'])." x ".number_format($result[0]->thickness,2)."</td>";
                    echo "<td align='center'>".$nomor_so."</td>";
                    echo "<td align='center'>".get_name('so_detail_header','id_mesin','id',$value['id_milik'])."</td>";
                    echo "<td></td>";
                echo "</tr>";
            }
        ?>
    </tbody>
</table>


	<style type="text/css">
		@page {
			margin-top: 1cm;
			margin-left: 1.5cm;
			margin-right: 1cm;
			margin-bottom: 1cm;
		}
		.mid{
			vertical-align: middle;
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
			font-size:12px;
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
			font-size:12px;
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
		p {
			margin: 0 0 0 0;
		}
	</style>


	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$refX	=  $dHeader[0]['ref_ke'];
	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Cutting Not Set');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output('Cutting Not Set.pdf' ,'I');