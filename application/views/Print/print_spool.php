<?php
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	?>
<table class="gridtable" border='1' width='100%' cellpadding='2'>
    <tr>
        <td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
        <td align='center' height='50%'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
    </tr>
    <tr>
        <td align='center' height='50%'><b><h2>SURAT PERINTAH KERJA SPOOL</h2></b></td>
    </tr>
</table>
<br>
<table class="gridtable2" border='0' width='100%' >
	<tr>
		<td width='20%'>No SPK</td>
		<td width='1%'>:</td>
		<td width='29%'><?= $spool_induk; ?></td>
		<td width='20%'>Qty</td>
		<td width='1%'>:</td>
		<td width='29%'><?= count($result);?></td>
	</tr>
	<tr>
		<td>Cycle Time (menit)</td>
		<td>:</td>
		<td></td>
		<td>Start Produksi</td>
		<td>:</td>
		<td></td>
	</tr>
	<tr>
		<td>Man Power</td>
		<td>:</td>
		<td></td>
		<td>Finish Produksi</td>
		<td>:</td>
		<td></td>
	</tr>
	<tr>
		<td>No Drawing</td>
		<td>:</td>
		<td><?=$result[0]['no_drawing'];?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table><br>
<?php
foreach ($result as $key2 => $value2) { $key2++;
    $result2 = $this->db->get_where('spool_group', array('spool_induk'=>$spool_induk,'kode_spool'=>$value2['kode_spool']))->result_array();
    ?>  
        <table class="gridtable" width='100%' border='1' cellpadding='2'>
            <thead>
                <tr>
                    <th colspan='7' align='left'><?=$key2?>. <?=$value2['kode_spool'];?></th>
                </tr>
                <tr>
                    <th width='15%'>Spool</th>
					<th align="center">Product</th>
					<th align="center">Spec</th>
					<th align="center">Length</th>
					<th align="center">Code</th>
					<th align="center">No SPK</th>
					<th align="center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result2 as $key => $value) { $key++;
					$SPEC = $value['kode_spk'];
					$product_code = $value['product_code'];
					if($value['sts'] == 'loose' OR $value['sts'] == 'cut'){
						$SPEC = spec_bq2($value['id_milik']);
						$IMPLODE = explode('.', $value['product_code']);
						$product_code = $IMPLODE[0].'.'.$value['product_ke'].$CUTTING_KE;
					}

					$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
					$LENGTH = (!empty($value['length']))?number_format($value['length']):'';

                    echo "<tr>";
                        if($key == 1){
                        echo "<td align='center' rowspan='".COUNT($result2)."' style='vertical-align:top;'>".$value2['kode_spool']."</td>";
                        }
						echo "<td align='left'>".strtoupper($value['id_category'])."</td>";
						echo "<td align='left'>".$SPEC."</td>";
						echo "<td align='right'>".$LENGTH."</td>";
						echo "<td align='left'>".$product_code."</td>";
						echo "<td align='center'>".$value['no_spk']."</td>";
                        echo "<td align='left'></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table><br>
    <?php
}
if(!empty($result_material)){
?>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead>
		<tr>
			<th colspan='3' align='left'>MATERIAL SHOP JOINT</th>
		</tr>
		<tr>
			<th width='5%'>#</th>
			<th align="center">Material</th>
			<th width='35%' align="center">Berat</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($result_material as $key => $value) { $key++;
			echo "<tr>";
				echo "<td align='center'>".$key."</td>";
				echo "<td align='left'>".strtoupper($value['nm_material'])."</td>";
				echo "<td align='right'>".number_format($value['berat'],3)." kg</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table>
<?php } ?>


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
			font-size:11px;
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
	$mpdf->SetTitle('SPK Spool');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output('SPK Spool '.$spool_induk.' '.date('YmdHis').'.pdf' ,'I');