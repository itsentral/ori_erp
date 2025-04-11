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

$rest_data 	= $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result_array();
$rest_d		= $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();


$ADJTYPE = ($rest_data[0]['adjustment_type'] == 'mutasi')?'retur':$rest_data[0]['adjustment_type'];
?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>PERMINTAAN RETUR MATERIAL</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">No Transaksi</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= $kode_trans;?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'>Dari Gudang</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?= strtoupper(get_name('warehouse','nm_gudang','id',$rest_data[0]['id_gudang_dari']));?></td>
			<td class="mid" width='15%'>Ke Gudang</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?= strtoupper(get_name('warehouse','nm_gudang','id',$rest_data[0]['id_gudang_ke']));?></td>
		</tr>
		<tr>
			<td class="mid">No. Berita Acara</td>
			<td class="mid">:</td>
			<td class="mid"><?= strtoupper($rest_data[0]['no_ba']);?></td>
			<td class="mid"></td>
			<td class="mid"></td>
			<td class="mid"></td>
		</tr>
        <tr>
			<td class="mid">Type Adjustment</td>
			<td class="mid">:</td>
			<td class="mid"><?= strtoupper($ADJTYPE);?></td>
			<td class="mid"></td>
			<td class="mid"></td>
			<td class="mid"></td>
		</tr>
        <tr>
			<td class="mid">Tgl Adjustment</td>
			<td class="mid">:</td>
			<td class="mid"><?= date('d F Y', strtotime($rest_data[0]['created_date']));?></td>
			<td class="mid"></td>
			<td class="mid"></td>
			<td class="mid"></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" width='4%'>No</th>
			<th class="mid" style='vertical-align:middle;'>Material Name</th>
			<th class="mid" width='15%'>Category</th>
            <th class='mid' width='12%'>Lot Number</th>
            <th class='mid' width='12%'>Qty (Kg)</th>
            <th class='mid' width='15%'>Expired Date</th>
            <th class='mid' width='20%'>Reason</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		foreach($rest_d AS $val => $valx){$No++;

            $expired_date 		= (!empty($valx2['expired_date']) AND $valx2['expired_date'] != '0000-00-00')?$valx2['expired_date']:'';

			echo "<tr>";
				echo "<td align='center'>".$No."</td>";
				echo "<td>".strtoupper($valx['nm_material'])."</td>";
				echo "<td>".strtoupper($valx['nm_category'])."</td>";
				echo "<td>".strtoupper($valx['lot_number'])."</td>";
				echo "<td align='right'>".number_format($valx['qty_oke'],4)."</td>";
				echo "<td align='center'>".$expired_date."</td>";
				echo "<td>".strtoupper($valx['keterangan'])."</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='5%'></td>
		<td align='center'>Ttd, Mengetahui</td>
		<td width='5%'></td>
		<td align='center'>Ttd,</td>
		<td width='5%'></td>
		<td align='center'>Ttd,</td>
		<td></td>
	</tr>
	<tr>
		<td height='45px'></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'>(ATASAN GUDANG)</td>
		<td></td>
		<td align='center'>(PIC yang bertanggung jawab)</td>
		<td></td>
		<td align='center'>(PENERIMA)</td>
		<td></td>
	</tr>
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
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 10px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 10px;
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
</style>


<?php
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($kode_trans."/".date('ymdhis', strtotime($dated))); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('tanda terima material '.$kode_trans.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');