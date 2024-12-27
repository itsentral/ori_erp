<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
// $mpdf=new mPDF('utf-8','A4');
$mpdf=new mPDF('utf-8','A4');

set_time_limit(0);
ini_set('memory_limit','1024M');

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$NO_IPP = $result_aksesoris[0]['no_ipp'];
$GET_SO = get_detail_ipp();
$NO_SO  = (!empty($GET_SO[$NO_IPP]['so_number']))?$GET_SO[$NO_IPP]['so_number']:'';
?>

<table class="gridtable2" border='1' width='100%' cellpadding='2'>
	<tr>
		<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
	</tr>
	<tr>
		<td align='center'><b><h2>TANDA TERIMA BARANG</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">No Transaksi</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= $kode;?></td>
		</tr>
        <tr>
			<td class="mid">No IPP/SO</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?= $NO_IPP.' / '.$NO_SO;?></td>
		</tr>
		<tr>
			<td class="mid">Tanggal Terima</td>
			<td class="mid">:</td>
			<td class="mid"><?= date('d F Y, H:i:s', strtotime($result_aksesoris[0]['created_date']));?></td>
			<td class="mid"></td>
			<td class="mid"></td>
			<td class="mid"></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr>
			<th class="mid" style='vertical-align:middle;' width='5%'>No</th>
			<th class="mid" style='vertical-align:middle;'>Name Barang</th>
			<th class="mid" style='vertical-align:middle;' width='25%'>Material</th>
			<th class="mid" style='vertical-align:middle;' width='10%'>Qty</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$No=0;
		foreach($result_aksesoris AS $val => $valx){
			$No++;
			
			$qty    = $valx['qty'];
            $satuan = $valx['satuan'];
            if($valx['category'] == 'plate'){
                $qty    = $valx['berat'];
                $satuan = '1';
            }

            $qty_req = $valx['qty_request'];
            $qty_out = $valx['qty_out'];

			$id_material = (!empty($valx['id_material2']))?$valx['id_material2']:$valx['id_material'];
			$code_group = (!empty($GET_ACCESSORIES[$id_material]['code_group']))?$GET_ACCESSORIES[$id_material]['code_group']:0;
			$nm_material = get_name_acc($id_material);
			$material = get_name('accessories','material','id',$id_material);
			if($tanda == 'X'){
				$code_group = $valx['code_group'];
				$nm_material = get_name_by_code_group($valx['code_group']);
				$material = get_name('con_nonmat_new','material_name','code_group',$code_group);
			}
			
			echo "<tr>";
				echo "<td align='center'>".$No."</td>";
				echo "<td>".$nm_material."</td>";
				echo "<td>".$material."</td>";
				echo "<td align='center'>".number_format($qty,2)."</td>";
		}
		?>
	</tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='65%'></td>
		<td align='center'></td>
		<td></td>
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
	</tr>
	<tr>
		<td></td>
		<td align='center'></td>
		<td></td>
		<td></td>
		<td align='center'>_______________________</td>
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($kode); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('outgoing-aksesoris-'.$kode.'.pdf' ,'I');