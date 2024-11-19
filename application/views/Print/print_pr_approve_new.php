<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf=new mPDF('utf-8','A4');

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
		<td align='center'><b><h2>PR MATERIAL</h2></b></td>
	</tr>
</table>
<br>
<br>
<table class="gridtable2" width="100%" border='0'>
	<thead>
		<tr>
			<td class="mid">No PR</td>
			<td class="mid">:</td>
			<td class="mid" colspan='4'><?=$no_pr;?></td>
		</tr>
		<tr>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
			<td class="mid" width='15%'></td>
			<td class="mid" width='2%'></td>
			<td class="mid" width='33%'></td>
		</tr>
	</thead>
</table><br>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
	<thead align='center'>
		<tr class='bg-blue'>
			<th colspan='8' style='text-align:left;'><b>MATERIAL</b></th>
		</tr>
		<tr>
            <th class="text-center no-sort" width='5%'>#</th>
            <th class="text-center">Material Name</th>
            <th class="text-center" width='10%'>Re-Order Point</th>
            <th class="text-center" width='10%'>MOQ</th>
            <th class="text-center" width='10%'>Qty Request</th>
            <th class="text-center" width='10%'>Qty Revisi</th>
            <th class="text-center" width='13%'>Dibutuhkan</th>
            <th class="text-center" width='15%'>Ket</th>
            <!-- <th class="text-center" width='10%'>Status</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
			if(!empty($result)){
                $no  = 0;
                $SUM_REQ = 0;
                $SUM_REV = 0;
                foreach($result AS $val => $valx){ $no++;
                    $bookpermonth 	= number_format($valx['book_per_month']);
                    $leadtime 		= number_format(get_max_field('raw_material_supplier', 'lead_time_order', 'id_material', $valx['id_material']));
                    $safetystock 	= number_format(get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']));
                    $reorder 		= ($bookpermonth*($safetystock/30))+($leadtime*($bookpermonth/30));
                    $sisa_avl 		= $valx['qty_stock'] - $valx['qty_booking'];
                    
                    $SUM_REQ += $valx['qty_request'];
                    $SUM_REV += $valx['qty_revisi'];
                    echo "<tr>";
                        echo "<td align='center'>".$no."</td>";
                        echo "<td align='left'>".$valx['nm_material']."</td>";
                        echo "<td align='right'>".number_format($reorder,2)."</td>";
                        echo "<td align='right'>".number_format($valx['moq'],2)."</td>";
                        echo "<td align='right'>".number_format($valx['qty_request'],2)."</td>";
                        echo "<td align='right'>".number_format($valx['qty_revisi'],2)."</td>";
                        $TANGGAL_DIBUTUHKAN = (!empty($valx['tanggal'])AND $valx['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($valx['tanggal'])):'';
                        echo "<td align='center'>".$TANGGAL_DIBUTUHKAN."</td>";
                        echo "<td align='left'>".ucfirst(strtolower($valx['keterangan']))."</td>";
                        if($valx['sts_ajuan'] == 'REJ'){
                            $sts_name = 'PR Rejected';
                            $warna	= 'red';
                        }
                        else{
                            if($valx['qty_request'] == $valx['qty_revisi']){
                                $sts_name = 'PR Approved';
                                $warna	= 'green';
                                if(!empty($valx['no_po'])){
                                    $sts_name = 'PR Approved, by '.$valx['no_po'];
                                    $warna	= 'green';
                                }
                                
                            }
                            elseif($valx['qty_request'] <> $valx['qty_revisi']){
                                $sts_name = 'PR Approved Rev Qty';
                                $warna	= 'blue';
                                if(!empty($valx['no_po'])){
                                    $sts_name = 'PR Approved Rev Qty, by '.$valx['no_po'];
                                    $warna	= 'blue';
                                }
                            }
                        }
                        // echo "<td align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
                        
                    echo "</tr>";
                }
                ?>
                <!-- <tr>
                    <td></td>
                    <td colspan='3'><b>TOTAL MATERIAL</b></td>
                    <td align='right'><b><?=number_format($SUM_REQ,2);?></b></td>
                    <td align='right'><b><?=number_format($SUM_REV,2);?></b></td>
                </tr> -->
                <?php
            }
            else{
                echo "<tr><td colspan='9'>Data not found</td></tr>";
            }
		?>
	</tbody>
	<?php if(!empty($non_frp)){ ?>
	<thead>
		<tr class='bg-blue'>
			<th colspan='8' style='text-align:left;'><b>NON FRP</b></th>
		</tr>
		<tr class='bg-blue'>
            <th class="text-center no-sort" width='5%'>#</th>
            <th class="text-center" colspan='2'>Material Name</th>
            <th class="text-center">Qty Request</th>
            <th class="text-center">Qty Revisi</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Dibutuhkan</th>
            <th class="text-center">Ket</th>
            <!-- <th class="text-center">Status</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
		$no  = 0;
        foreach($non_frp AS $val => $valx){ $no++;
        
            $satuan = $valx['satuan'];
            if($valx['idmaterial'] == '2'){
                $satuan = '1';
            }
            $satx = get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan);
            
            $nm_acc = get_name_acc($valx['id_material']);
            if(empty($valx['idmaterial'])){
                $satx = '-';
                $nm_acc = strtoupper($valx['nm_material']);
            }
            
            echo "<tr>";
                echo "<td align='center'>".$no."</td>";
                echo "<td align='left'>".$nm_acc."</td>";
                echo "<td align='right'>".number_format($valx['qty_request'],2)."</td>";
                echo "<td align='right'>".number_format($valx['qty_revisi'],2)."</td>";
                echo "<td align='left'>".strtoupper($satx)."</td>";
                $TANGGAL_DIBUTUHKAN = (!empty($valx['tanggal'])AND $valx['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($valx['tanggal'])):'';
                echo "<td align='center'>".$TANGGAL_DIBUTUHKAN."</td>";
                echo "<td align='left'>".ucfirst(strtolower($valx['keterangan']))."</td>";
                if($valx['sts_ajuan'] == 'REJ'){
                    $sts_name = 'PR Rejected';
                    $warna	= 'red';
                }
                else{
                    if($valx['qty_request'] == $valx['qty_revisi']){
                        $sts_name = 'PR Approved';
                        $warna	= 'green';
                        if(!empty($valx['no_po'])){
                            $sts_name = 'PR Approved, by '.$valx['no_po'];
                            $warna	= 'green';
                        }
                        
                    }
                    elseif($valx['qty_request'] <> $valx['qty_revisi']){
                        $sts_name = 'PR Approved Rev Qty';
                        $warna	= 'blue';
                        if(!empty($valx['no_po'])){
                            $sts_name = 'PR Approved Rev Qty, by '.$valx['no_po'];
                            $warna	= 'blue';
                        }
                    }
                }
                // echo "<td align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
                
            echo "</tr>";
        }
		?>
	</tbody>
	<?php } ?>
</table><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='50%'></td>
		<td align='center'></td>
		<td align='center'>Diketahui,</td>
		<td width='5%'></td>
		<td align='center'>Disetujui,</td>
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
		<td align='center'>(________________)</td>
		<td></td>
		<td align='center'>(________________)</td>
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$no_ipp."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle($no_pr); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('print-pr-approval-'.$no_pr.'.pdf' ,'I');