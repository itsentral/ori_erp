<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf=new mPDF('utf-8','A4');
// $mpdf=new mPDF([
// 'mode' => 'utf-8',
// 'format' => 'A4',
// 'orientation' => 'P',
// 'margin_left' => 0,
// 'margin_right' => 0,
// 'margin_top' => 0,
// 'margin_bottom' => 0,
// 'margin_header' => 0,
// 'margin_footer' => 0,
// ]);

// $mpdf=new mPDF('utf-8','A4-L');

set_time_limit(0);
ini_set('memory_limit','1024M');

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$rest_d		= $this->db
                    ->select('a.*, b.nm_material, c.keterangan')
                    ->join('raw_materials b','a.id_material=b.id_material','left')
                    ->join('warehouse_adjustment_check c','a.id_lot=c.id','left')
                    ->get_where('warehouse_adjustment_spk a', array('a.kode_trans'=>$kode_trans,'deleted_date'=>NULL))->result_array();
$rest_data 	= $this->db->get_where('warehouse_adjustment', array('kode_trans'=>$kode_trans))->result();

$nm_ke 		= get_name('warehouse', 'nm_kode', 'id', $rest_data[0]->id_gudang_ke);
$nm_dari 	= get_name('warehouse', 'nm_kode', 'id', $rest_data[0]->id_gudang_dari);

$nm_gd_ke 	= get_name('warehouse', 'nm_gudang', 'id', $rest_data[0]->id_gudang_ke);
$nm_gd_dari = get_name('warehouse', 'nm_gudang', 'id', $rest_data[0]->id_gudang_dari);
?>

<table class="gridtable2" width="100%" border='0' style='border-bottom:none;'>
	<tr>
		<td rowspan='7' align='center'  style='border-bottom:none;'>
			<b>PT. ORI POLYTEC COMPOSITES</b><br>
			Jl. Akasia II Block A9/3<br>
			Cikarang - Bekasi - Indonesia<br>
			Telp : (021) 8972193<br>
			Fax  : 8972192
		</td>
		<td style='border-right:none; border-bottom: none;' width='12%'>Form No</td>
		<td style='border-left:none; border-right:none; border-bottom: none;' width='1%'>:</td>
		<td style='border-left:none; border-bottom: none;' width='20%'></td>
		<td style='border-bottom:none;' colspan='3' rowspan='2' align='center'><u><b>DISTRIBUSI SURAT JALAN</b></u></td>
	</tr>
	<tr>
		<td style='border-right:none; border-top: none; border-bottom: none;'>Rev. No</td>
		<td style='border:none;'>:</td>
		<td style='border-left:none; border-top: none; border-bottom: none;'></td>
	</tr>
	<tr>
		<td style='border-right:none; border-top: none;'>Issue Date</td>
		<td style='border-right:none; border-top: none; border-left:none;'>:</td>
		<td style='border-left:none; border-top: none;'></td>
		<td style='border-bottom:none; border-right: none; border-top: none;' width='12%'>Putih/Asli</td>
		<td style='border:none;' width='1%'>:</td>
		<td style='border-bottom:none; border-left: none; border-top: none;' width='20%'>Penagihan / Finance</td>
	</tr>
	<tr>
		<td colspan='3' style='border-bottom:none;' align='center'><u><b>SPK PENGAMBILAN BARANG</b></u></td>
		<td style='border-bottom:none; border-top: none; border-right: none;'>Merah</td>
		<td style='border:none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'>PPIC/Logistik</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-top: none; border-right: none;'>NO</td>
		<td style='border:none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'><?=$rest_data[0]->no_surat_jalan;?></td>
		<td style='border-bottom:none; border-top: none; border-right: none;'>Kuning</td>
		<td style='border:none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'>Pembeli / Penerima</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-top: none; border-right: none;'>NO. Memo</td>
		<td style='border:none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'></td>
		<td style='border-bottom:none; border-top: none; border-right: none;'>Hijau</td>
		<td style='border:none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'>Cost Control</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-top: none; border-right: none;'>NO. SO</td>
		<td style='border:none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'></td>
		<td style='border-bottom:none; border-top: none; border-right: none;'>Biru</td>
		<td style='border: none;'>:</td>
		<td style='border-bottom:none; border-top: none; border-left:none;'>Security</td>
	</tr>
</table>
<table class="gridtable2" width="100%" border='0'>
	<tr>
		<td width='15%' style='border-bottom:none; border-right: none; vertical-align: bottom;' height='30px'>Supir</td>
		<td width='2%' style='border-bottom:none; border-left: none;  border-right: none; vertical-align: bottom;'>:</td>
		<td width='20%' style='border-bottom:none; border-left: none; border-right: none; vertical-align: bottom;'>................................</td>
		<td rowspan='5' style='border-bottom:none; border-right: none; border-left: none;'></td>
		<td width='33%' rowspan='5' style='border-bottom:none; border-left: none; vertical-align: top; padding-top:10px;'>
			Cikarang, <?=date('d F Y');?><br>
			Kepada Yth, <br>
			<?=$nm_ke;?> <br>
		</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;'>No. Container</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;'>No. Seal</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;'>Jenis Kendaraan</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
	<tr>
		<td style='border-bottom:none; border-right: none;  border-top: none;'>No. Polisi</td>
		<td style='border: none;'>:</td>
		<td style='border: none;'>................................</td>
	</tr>
</table>
<table class="gridtable2" width='100%' border='1' cellpadding='2'>
	<tr>
		<td align='center' width='15%'>BANYAKNYA</td>
		<td align='center' width='10%'>SATUAN</td>
		<td align='center' style='vertical-align:middle;'>NAMA DAN JENIS BARANG</td>
		<td align='center' width='20%'>Keterangan</td> 
	</tr>
	<?php
	foreach($rest_d AS $val => $valx){
		echo "<tr>";
			echo "<td align='right'>".number_format($valx['qty_confirm_pack'],2)."</td>";
			echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing']))."</td>";
			echo "<td align='left'>".$valx['nm_material']."</td>";
			echo "<td align='left'>".$valx['keterangan']."</td>";
		echo "</tr>";
	}
	echo "<tr>";
		echo "<td colspan='2'></td>";
		echo "<td colspan='2'><b>Note: Barang dikirim dari ".$nm_dari." ke ".$nm_ke."</b></td>";
	echo "<tr>";
		echo "<td colspan='2' style='border-bottom:none;'></td>";
		echo "<td colspan='2' style='border-bottom:none;'><b>".strtoupper($nm_gd_dari)." ke ".strtoupper($nm_gd_ke)."</b></td>";
	echo "</tr>";
	?>
</table>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='15%' align='center'>Dibuat,</td>
		<td width='15%' align='center'>Diperiksa,</td>
		<td align='center'>Diketahui,</td>
		<td width='15%' align='center'>Diketahui,</td>
		<td width='15%' align='center'>Diterima</td>
	</tr>
	<tr>
		<td height='65px'></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td align='center'>Staff Gudang</td>
		<td align='center'>Head PPIC</td>
		<td align='center'></td>
		<td align='center'>Pembawa</td>
		<td align='center'></td>
	</tr>
	<tr>
		<td align='center' colspan='2'>Log Dept</td>
		<td align='center'>Cost Control Factory Manager</td>
		<td align='center'>(Supir/Ekspedisi)</td>
		<td align='center'>Penerima</td>
	</tr>
</table>
<p>NB : Pembawa bertanggungjawab atas barang yang dikirim.</p>
<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}

	.mid{
		vertical-align: middle !important;
	}

	p{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
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
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0cm;
	}
	table.gridtable2 th {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
		margin-top: 0cm;
		margin-left: 0cm;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
		margin-top: 0cm;
		margin-left: 0cm;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 3px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
		margin-top: 0cm;
		margin-left: 0cm;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 3px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
		margin-top: 0cm;
		margin-left: 0cm;
	}
</style>


<?php
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans."</i></p>";
$html = ob_get_contents();
// exit;
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($kode_trans); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
ob_clean();
$mpdf->Output('spk-pegambilan-barang-'.$kode_trans.'.pdf' ,'I');