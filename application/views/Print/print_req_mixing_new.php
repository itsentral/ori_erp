<?php
	
$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php"; 
$mpdf=new mPDF('utf-8','A4');
// $mpdf=new mPDF('utf-8','A4-L');
set_time_limit(0);
ini_set('memory_limit','2048M');

ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('D, d-M-Y H:i:s');
$NO_IPP = $get_detail_spk2[0]['no_ipp'];

$CUSTOMER = strtoupper(get_name('production','nm_customer','no_ipp',$NO_IPP));
$PROJECT = strtoupper(get_name('production','project','no_ipp',$NO_IPP));
?>
<!-- ========================================================================================================= -->
<!-- =============================================MIXING====================================================== -->
<!-- ========================================================================================================= -->

<table class="gridtable" border='1' width='100%' cellpadding='2'>
    <tr>
        <td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
        <td align='center' height='50%'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
    </tr>
    <tr>
        <td align='center' height='50%'><b><h2>Request Material (Print Ke-<?=$print_ke;?>)</h2></b></td>
    </tr>
</table>
<table class="gridtable2" border='0' width='100%' >
    <tr>
        <td></td>
        <td></td>
        <td colspan='4'></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan='4'></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan='4'></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan='4'></td>
    </tr>
    <tr>
        <td width='20%'><b>No Transaksi</b></td>
        <td width='1%'><b>:</b></td>
        <td width='38%'><b><?=$kode_trans;?></b></td>
        <td width='22%'><b>Tgl Request Berikutnya</b></td>
        <td width='1%'><b>:</b></td>
        <td width='18%'><b><?=date('d F Y', strtotime($tgl_planning));?></b></td>
    </tr>
    <tr>
        <td>Tgl Planning</td>
        <td>:</td>
        <td><?=date('d F Y', strtotime($get_detail_spk2[0]['tanggal_produksi']));?></td>
        <td>Costcenter</td>
        <td>:</td>
        <td><?=strtoupper(get_name('warehouse','nm_gudang','id',$gudang_to));?></td>
    </tr>
    <tr>
        <td>Customer</td>
        <td>:</td>
        <td colspan='4'><?=$CUSTOMER;?></td>
    </tr>
    <tr>
        <td>Project</td>
        <td>:</td>
        <td colspan='4'><?=$PROJECT;?></td>
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
<p>Produk yang akan diproduksi:</p>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
<thead>
    <tr class='bg-blue'>
        <th class="text-center" width='5%'>#</th>
        <th class="text-center">No SO</th>
        <th class="text-center">Product</th>
        <th class="text-center">No SPK</th>
        <th class="text-center" width='20%'>Spec</th>
        <th class="text-center" width='10%'>Qty</th>
        <th class="text-center" width='10%'>Qty Produksi</th>
    </tr>
</thead>
<tbody>
    <?php
        if(!empty($get_detail_spk2)){
            foreach($get_detail_spk2 AS $key => $value){
                $key++;
                $EXPLODE = explode('-',$value['product_code']);

                $NO_SPK = $value['no_spk'];;
                $SPEC = spec_bq2($value['id_milik']);
                $KET = '';

                $readonly = '';
                $qty_auto = '';
                if($value['id_product'] == 'tanki'){
                    $NO_SPK = $value['no_spk'];
                    $SPEC = (!empty($GET_SPEC_TANK[$value['id_milik']]))?$GET_SPEC_TANK[$value['id_milik']]:'';
                    $KET = 'TANKI - ';
                }
                if($value['id_product'] == 'deadstok'){
                    $tanda_deadstok = $value['product_code_cut'];
                    $HeaderDeadstok = $this->db
                                        ->select('a.id, b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty, b.id_milik')
                                        ->group_by('a.kode')
                                        ->join('deadstok b','a.id_deadstok=b.id','left')
                                        ->get_where('deadstok_modif a',array('kode'=>$tanda_deadstok))
                                        ->result_array();
                    $SPEC = (!empty($HeaderDeadstok[0]['product_spec']))?$HeaderDeadstok[0]['product_spec']:'';
                    $readonly = 'readonly';
                    $qty_auto = $value['qty'] - $value['qty_input'];
                }
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".strtoupper($EXPLODE[0])."</td>";
                    echo "<td>".strtoupper($KET.$value['product'])."</td>";
                    echo "<td align='center'>".$NO_SPK."</td>";
                    echo "<td>".$SPEC."</td>";
                    echo "<td align='center'>".number_format($value['qty'])."</td>";
                    echo "<td align='center'>".number_format($value['qty_parsial'])."</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
            echo "</tr>";
        }
    ?>
</tbody>
</table>

<br>
<p>Kebutuhan material:</p>
<?php if(!empty($get_liner_utama)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='8' align='left'>LINER</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='15%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='8%'>Request (kg)</th>
                <th width='10%'>Aktual Material</th>
                <th width='10%'>Aktual Qty (kg)</th>
                <th width='14%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_liner_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".floatval($value['total_req'])." / ".floatval($value['estimasi'])."</td>";
                        echo "<td align='right'>".floatval($value['request'])."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td>".$value['keterangan']."</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_str_n1_utama)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='8' align='left'>STRUCTURE NECK 1</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='15%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='8%'>Request (kg)</th>
                <th width='10%'>Aktual Material</th>
                <th width='10%'>Aktual Qty (kg)</th>
                <th width='14%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n1_utama as $key => $value) { $nomor++;
					echo "<tr>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td>".$value['category']."</td>";
						echo "<td>".$value['nm_material']."</td>";
						echo "<td align='right'>".number_format($value['total_req'],4)." / ".number_format($value['estimasi'],4)."</td>";
						echo "<td align='right'>".number_format($value['request'],4)."</td>";
						echo "<td></td>";
						echo "<td></td>";
                        echo "<td>".$value['keterangan']."</td>";
					echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_str_n2_utama)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='8' align='left'>STRUCTURE NECK 2</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='15%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='8%'>Request (kg)</th>
                <th width='10%'>Aktual Material</th>
                <th width='10%'>Aktual Qty (kg)</th>
                <th width='14%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n2_utama as $key => $value) { $nomor++;
					echo "<tr>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td>".$value['category']."</td>";
						echo "<td>".$value['nm_material']."</td>";
						echo "<td align='right'>".number_format($value['total_req'],4)." / ".number_format($value['estimasi'],4)."</td>";
						echo "<td align='right'>".number_format($value['request'],4)."</td>";
						echo "<td></td>";
						echo "<td></td>";
                        echo "<td>".$value['keterangan']."</td>";
					echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_structure_utama)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='8' align='left'>STRUCTURE</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='15%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='8%'>Request (kg)</th>
                <th width='10%'>Aktual Material</th>
                <th width='10%'>Aktual Qty (kg)</th>
                <th width='14%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_structure_utama as $key => $value) { $nomor++;
					echo "<tr>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td>".$value['category']."</td>";
						echo "<td>".$value['nm_material']."</td>";
						echo "<td align='right'>".number_format($value['total_req'],4)." / ".number_format($value['estimasi'],4)."</td>";
						echo "<td align='right'>".number_format($value['request'],4)."</td>";
						echo "<td></td>";
						echo "<td></td>";
                        echo "<td>".$value['keterangan']."</td>";
					echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_external_utama)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='8' align='left'>EXTERNAL</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='15%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='8%'>Request (kg)</th>
                <th width='10%'>Aktual Material</th>
                <th width='10%'>Aktual Qty (kg)</th>
                <th width='14%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_external_utama as $key => $value) { $nomor++;
					echo "<tr>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td>".$value['category']."</td>";
						echo "<td>".$value['nm_material']."</td>";
						echo "<td align='right'>".number_format($value['total_req'],4)." / ".number_format($value['estimasi'],4)."</td>";
						echo "<td align='right'>".number_format($value['request'],4)."</td>";
						echo "<td></td>";
						echo "<td></td>";
                        echo "<td>".$value['keterangan']."</td>";
					echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_topcoat_utama)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='8' align='left'>TOP COAT</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='15%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='8%'>Request (kg)</th>
                <th width='10%'>Aktual Material</th>
                <th width='10%'>Aktual Qty (kg)</th>
                <th width='14%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_topcoat_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['total_req'],4)." / ".number_format($value['estimasi'],4)."</td>";
                        echo "<td align='right'>".number_format($value['request'],4)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td>".$value['keterangan']."</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
<?php } ?>
<br>
<table class="gridtable4" width='100%' border='0' cellpadding='2'>
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
		margin-top: 1 cm;
		margin-left: 1 cm;
		margin-right: 1 cm;
		margin-bottom: 1 cm;
		margin-footer: 0 cm
	}

    p{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
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
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 2px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}

    table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
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
		font-size:10px;
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

	p {
		margin: 0 0 0 0;
	}

    table.gridtable4 {
        font-family: verdana,arial,sans-serif;
        font-size:12;
        color:#333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
    }
    table.gridtable4 th {
        border-width: 1px;
        padding: 3px;
        border-style: none;
        border-color: #666666;
        background-color: #f2f2f2;
    }
    table.gridtable4 th.head {
        border-width: 1px;
        padding: 3px;
        border-style: none;
        border-color: #666666;
        background-color: #7f7f7f;
        color: #ffffff;
    }
    table.gridtable4 td {
        border-width: 1px;
        padding: 3px;
        border-style: none;
        border-color: #666666;
        background-color: #ffffff;
    }
    table.gridtable4 td.cols {
        border-width: 1px;
        padding: 3px;
        border-style: none;
        border-color: #666666;
        background-color: #ffffff;
    }
</style>

<?php
$html = ob_get_contents();
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_spk."</i></p>";
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle('Request Material');
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output("request-material-print-ke-$print_ke-$kode_trans.pdf" ,'I');
?>