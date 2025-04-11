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
$sts_app='';
if($header[0]->sts_app=='Y') $sts_app='Y';
?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2><?=($sts_app==''?'PENGAJUAN DEPARTEMENT':'PR DEPARTEMEN')?></h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">No Transaksi</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?=($sts_app==''?$header[0]->no_pengajuan:$header[0]->no_pr)?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'>Department</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?= strtoupper(get_name('department','nm_dept','id',$header[0]->id_dept)." / ".get_name('costcenter','nm_costcenter','id_costcenter',$header[0]->id_costcenter));?></td>
			<td class="mid" width='15%'>COA Anggaran</td>
			<td class="mid" width='2%'>:</td>
			<td class="mid" width='33%'><?=$header[0]->coa;?></td>
		</tr>
		<tr>
			<td class="mid">Nilai Budget</td>
			<td class="mid">:</td>
			<td class="mid"><?=number_format($header[0]->budget);?></td>
			<td class="mid">Sisa Budget</td>
			<td class="mid">:</td>
			<td class="mid"><?=number_format($header[0]->sisa_budget);?></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class='text-center' style='width: 3%;'>#</th>
			<th class='text-center' >Nama Barang/Jasa</th>
			<th class='text-center' style='width: 15%;'>Spesifikasi</th>
			<th class='text-center' style='width: 7%;'>Qty</th>
			<th class='text-center' style='width: 7%;'>Satuan</th>
			<th class='text-center' style='width: 8%;'>Est Harga</th>
			<th class='text-center' style='width: 8%;'>Est Total Harga</th>
			<th class='text-center' style='width: 10%;'>Tgl Dibutuhkan</th>
			<th class='text-center' style='width: 15%;'>Keterangan</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$nomor = 0;
			if(!empty($detail)){
				foreach($detail AS $val => $valx){ $nomor++;

					$get_satuan		= $this->db->get_where('raw_pieces',array('id_satuan'=>$valx['satuan']))->result();
					$nm_satuan = (!empty($get_satuan))?strtoupper($get_satuan[0]->kode_satuan):'';
					$tgl_dibutuhkan = ($valx['tanggal'] <> '0000-00-00' AND $valx['tanggal'] != NULL)?date('d-M-Y', strtotime($valx['tanggal'])):'not set';
					echo "<tr class='header_".$nomor."'>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td align='left'>".strtoupper($valx['nm_barang'])."</td>";
						echo "<td align='left'>".strtoupper($valx['spec'])."</td>";
						echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
						echo "<td align='center'>".$nm_satuan."</td>";
						echo "<td align='right'>".number_format($valx['harga'])."</td>";
						echo "<td align='right'>".number_format($valx['qty'] * $valx['harga'])."</td>";
						echo "<td align='center'>".$tgl_dibutuhkan."</td>";
						echo "<td align='left'>".strtoupper($valx['keterangan'])."</td>";
					echo "</tr>";
				}
			}
		?>
	</tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='65%'></td>
		<td align='center'></td>
		<td align='center'>Dibuat,</td>
		<td width='5%'></td>
		<td align='center'>Disetujui,</td>
		<td></td>
	</tr>
	<tr>
		<td height='55px'></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'></td>
		<td align='center'>(______________________)</td>
		<td></td>
		<td align='center'>(______________________)</td>
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
		font-size:10px;
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
$mpdf->Output('tanda terima rutin '.$kode_trans.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');