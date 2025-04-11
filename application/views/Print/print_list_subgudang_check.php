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


$rest_d		= $this->db->get_where('warehouse_adjustment_check',array('kode_trans'=>$kode_trans,'update_by'=>$update_by,'update_date'=>$update_date,'qty_oke >'=>0))->result_array();
$rest_d2	= $this->db->get_where('production_spk_add_hist',array('kode_trans'=>$kode_trans,'created_by'=>$update_by,'created_date'=>$update_date,'terpakai >'=>0))->result_array();

$NO_SPK 			= $rest_data[0]['no_spk'];
$TGL_PLANNING_ 		= $rest_data[0]['tanggal'];

$NO_SO = "";
if($rest_data[0]['no_ipp'] != 'resin mixing'){
$NO_SO = $ArrGetSO['BQ-'.$rest_data[0]['no_ipp']];
}

if($rest_data[0]['no_ipp'] == 'resin mixing'){
    $REST_SQL		= $this->db->get_where('production_spk_parsial',array('kode_spk'=>$rest_data[0]['kode_spk'],'created_date'=>$rest_data[0]['created_date'],'spk'=>'1'))->result_array();
    $ArrNo_SPK = [];
    $ArrNo_SO = [];
    foreach($REST_SQL AS $valx => $value){
        $ArrNo_SPK[] 	= $ArrGetSPK[$value['id_milik']];
        $ArrNo_SO[] 	= $ArrGetSO[$ArrGetIPP[$value['id_milik']]];
    }

    $NO_SO = implode(', ',array_unique($ArrNo_SO));
    $NO_SPK = implode(', ',array_unique($ArrNo_SPK));
}
$id_milik 			= get_name('production_detail','id_milik','no_spk',$NO_SPK);
$TGL_PLANNING = (!empty($TGL_PLANNING_))?date('d-M-Y',strtotime($TGL_PLANNING_)):'';
$QTY_SPK = (!empty($rest_data[0]['qty_spk']))?'( Qty: '.number_format($rest_data[0]['qty_spk']).')':'';
$ID_BQ 			= get_name('so_number','id_bq','so_number',$NO_SO);
$NO_IPP 		= str_replace('BQ-','',$ID_BQ);
$QTY = (!empty($REST_SQL))?$REST_SQL[0]['qty']:'-';
?>

<table class="gridtable" border='1' width='100%' cellpadding='2'>
    <tr>
        <td width='70px' style='padding:0px;' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
        <td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
    </tr>
    <tr>
        <td align='center' ><b><h2>LEMBAR AKTUAL KELUAR</h2></b></td>
    </tr>
</table>
<br>
<table class="gridtable2" width="100%" border='0'>
    <thead>
        <tr>
            <td class="mid" width='15%'>Dari Gudang</td>
            <td class="mid" width='2%'>:</td>
            <td class="mid" width='25%'><?= get_name('warehouse', 'nm_gudang', 'id', $rest_data[0]['id_gudang_dari']);?></td>
            <td class="mid" width='15%'>Ke Gudang</td>
            <td class="mid" width='2%'>:</td>
            <td class="mid" width='41%'><?= $KeGudang; ?></td>
        </tr>
        <tr>
            <td class="mid">No Transaksi</td>
            <td class="mid">:</td>
            <td class="mid"><?= $kode_trans;?></td>
            <td class="mid">No SO / Project</td>
            <td class="mid">:</td>
            <td class="mid"><?= $NO_SO;?> / <?=strtoupper(get_name('production','project','no_ipp',$NO_IPP));?></td>
        </tr>
        <tr>
            <td class="mid">Tanggal Request</td>
            <td class="mid">:</td>
            <td class="mid"><?= date('d F Y', strtotime($rest_data[0]['created_date']));?></td>
            <td class="mid">No SPK</td>
            <td class="mid">:</td>
            <td class="mid"><?= $NO_SPK.$QTY_SPK;?></td>
        </tr>
        <tr>
            <td class="mid">Product / Spec</td>
            <td class="mid">:</td>
            <td class="mid"><?=strtoupper(get_name('so_detail_header','id_category','id',$id_milik)).' / '.strtoupper(spec_bq2($id_milik));?></td>
            <td class="mid">Tgl Planning</td>
            <td class="mid">:</td>
            <td class="mid"><?= $tgl_planning;?></td>
        </tr>
        <tr>
            <td class="mid">Qty Product</td>
            <td class="mid">:</td>
            <td class="mid"><?=$QTY;?></td>
            <td class="mid">Customer</td>
            <td class="mid">:</td>
            <td class="mid"><?=strtoupper(get_name('production','nm_customer','no_ipp',$NO_IPP));?></td>
        </tr>
        <tr>
            <td class="mid">History By</td>
            <td class="mid">:</td>
            <td class="mid"><?=strtoupper(get_name('users','nm_lengkap','username',$update_by)).' / '.date('d-M-Y H:i:s',strtotime($update_date));?></td>
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
            <th class="mid" style='vertical-align:middle;'>Category</th>
            <th class="mid" width='10%'>Est (Kg)</th>
            <th class="mid" width='15%'>Aktual Keluar (Kg)</th>
            <th class="mid" width='18%'>Keterangan</th> 
        </tr>
    </thead>
    <tbody>
        <?php
        $No=0;
        foreach ($rest_d as $key => $valx) {
            $No++;
            echo "<tr>";
                echo "<td align='center'>".$No."</td>";
                echo "<td>".$valx['nm_material']."</td>";
                echo "<td>".$valx['nm_category']."</td>";
                echo "<td align='right'>".number_format($valx['qty_order'],4)."</td>";
                echo "<td align='right'>".number_format($valx['qty_oke'],4)."</td>";
                echo "<td>".strtoupper($valx['keterangan'])."</td>";
            echo "</tr>";
        }
        foreach ($rest_d2 as $key => $valx) {
            $No++;
            echo "<tr>";
                echo "<td align='center'>".$No."</td>";
                echo "<td>".get_name('raw_materials','nm_material','id_material',$valx['actual_type'])."</td>";
                echo "<td>".get_name('raw_materials','nm_category','id_material',$valx['actual_type'])."</td>";
                echo "<td align='right'></td>";
                echo "<td align='right'>".number_format($valx['terpakai'],4)."</td>";
                echo "<td>ADD, ".strtoupper($valx['layer'])."</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table><br><br><br>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
    <tr>
        <td width='65%'></td>
        <td>Disiapkan,</td>
        <td></td>
        <td>Penerima,</td>
        <td></td>
    </tr>
    <tr>
        <td height='45px'></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>_________________</td>
        <td></td>
        <td>_________________</td>
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
        font-size:13;
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
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans.$no_request."</i></p>";
$html = ob_get_contents();
// exit;
ob_end_clean();
$mpdf->SetTitle($kode_trans); 
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output('pengeluaran aktual subgudang '.$kode_trans.'.pdf' ,'I');