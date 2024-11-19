<?php
	
$sroot 		= $_SERVER['DOCUMENT_ROOT'];
// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
include $sroot."/application/libraries/MPDF57/mpdf.php"; 
$mpdf=new mPDF('utf-8','A4');
// $mpdf=new mPDF('utf-8','A4-L');
set_time_limit(0);
ini_set('memory_limit','1024M');

ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('D, d-M-Y H:i:s');
?>
<!-- ========================================================================================================= -->
<!-- ==========================================SPK LOOSE====================================================== -->
<!-- ========================================================================================================= -->
<table class="gridtable" border='1' width='100%' cellpadding='2'>
    <tr>
        <td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
        <td align='center' height='50%'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
    </tr>
    <tr>
        <td align='center' height='50%'><b><h2>SURAT PERINTAH KERJA</h2></b></td>
    </tr>
</table><br>
<table class="gridtable2" border='0' width='100%' >
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td width='20%'>No Kode Program</td>
        <td width='1%'>:</td>
        <td width='29%'><?=$kode_spk;?></td>
        <td width='20%'>Qty Jenis Product</td>
        <td width='1%'>:</td>
        <td width='29%'><?= COUNT($get_detail_spk);?></td>
    </tr>
    <tr>
        <td>Cycle time (Menit)</td>
        <td>:</td>
        <td></td>
        <td>Operator</td>
        <td>:</td>
        <td></td>
    </tr>
    <tr>
        <td>Manhours</td>
        <td>:</td>
        <td></td>
        <td>Start Produksi</td>
        <td>:</td>
        <td></td>
    </tr>
    <tr>
        <td>Aktual Manhours</td>
        <td>:</td>
        <td></td>
        <td>Finish Produksi</td>
        <td>:</td>
        <td></td>
    </tr>
    <tr>
        <td>Produktivity</td>
        <td>:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
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
<table class="gridtable" width='100%' border='1' cellpadding='2'>
    <thead align='center'>
        <tr>
            <th width='5%'>#</th>
            <th width='15%'>ID Product</th>
            <th width='12%'>No SPK</th>
            <th>Spec Product</th>
            <th width='10%'>SO</th>
            <th width='7%'>Qty</th>
            <th width='7%'>Mesin</th>
            <th width='20%'>Catatan Masalah Product/Process</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($get_detail_spk as $key => $value) { $key++;
                $IMPLODE = explode('-',$value['product_code']);
                $IMPLODE2 = explode('.',$value['product_code']);

                $spec = spec_bq2($value['id_milik']);
                $catatan = get_name('so_detail_header','id_mesin','id',$value['id_milik']);
                if($value['id_product'] == 'deadstok'){
                    $spec = "";
                    $catatan = "";
                }

                echo "<tr>";
                    echo "<td align='center' style='height:100px;'>".$key."</td>";
                    echo "<td align='center'>".$IMPLODE2[0]."</td>";
                    echo "<td align='center'>".$value['no_spk']."</td>";
                    echo "<td>".strtoupper($value['product'])."<br>".$spec."</td>";
                    echo "<td align='center'>".$IMPLODE[0]."</td>";
                    echo "<td align='center'>".$value['qty']."</td>";
                    echo "<td align='center'>".$catatan."</td>";
                    echo "<td></td>";
                echo "</tr>";
            }
        ?>
    </tbody>
</table>
<!-- ========================================================================================================= -->
<!-- ==============================================UTAMA====================================================== -->
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
        <td align='center' height='50%'><b><h2>SPK UTAMA</h2></b></td>
    </tr>
</table>

<br>
<p>Produk yang akan diproduksi:</p>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
    <thead align='center'>
        <tr>
            <th width='3%'>#</th>
            <th width='10%'>No IPP</th>
            <th width='10%'>No SPK</th>
            <th width='15%'>Customer</th>
            <th>Project</th>
            <th width='18%'>Produk</th>
            <th width='12%'>Spesifikasi</th>
            <th width='5%'>Qty</th>
            <th width='8%'>Product Ke</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($get_detail_spk as $key => $value) { $key++;
                $product_ke = $this->db->select('qty, MAX(product_ke) AS max_q, MIN(product_ke) AS min_q, no_spk')->get_where('production_detail', array('kode_spk'=>$kode_spk,'id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp']))->result();
                $spec = spec_bq2($value['id_milik']);
                $product_kex = $product_ke[0]->min_q."-".$product_ke[0]->max_q." of ".$product_ke[0]->qty;
                if($value['id_product'] == 'deadstok'){
                    $spec = "";
                    $product_kex = "";
                }
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$value['no_ipp']."</td>";
                    echo "<td align='center'>".$value['no_spk']."</td>";
                    echo "<td align='left'>".strtoupper(get_name('production','nm_customer','no_ipp',$value['no_ipp']))."</td>";
                    echo "<td align='left'>".strtoupper(get_name('production','project','no_ipp',$value['no_ipp']))."</td>";
                    echo "<td>".strtoupper($value['product'])."</td>";
                    echo "<td>".$spec."</td>";
                    echo "<td align='center'>".$value['qty']."</td>";
                    echo "<td align='left'>".$product_kex."</td>";
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
            <tr><th colspan='6' align='left'>LINER</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_liner_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
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
            <tr><th colspan='6' align='left'>STRUCTURE NECK 1</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n1_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
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
            <tr><th colspan='6' align='left'>STRUCTURE NECK 2</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n2_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
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
            <tr><th colspan='6' align='left'>STRUCTURE</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_structure_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
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
            <tr><th colspan='6' align='left'>EXTERNAL</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_external_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
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
            <tr><th colspan='6' align='left'>TOP COAT</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_topcoat_utama as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>

<table class="gridtable" width='100%' border='1' cellpadding='2'>
    <tr>
        <th align='left' colspan='6'>NOTE</th>
    </tr>
    <tr>
        <td height='50px' colspan='6'></td>
    </tr>
</table>
<br>
<table class="gridtable3" width='100%' border='0' cellpadding='2'>
    <tr>
        <td>Dibuat,</td>
        <td></td>
        <td>Diperiksa,</td>
        <td></td>
        <td>Diketahui,</td>
        <td></td>
    </tr>
    <tr>
        <td height='25px'></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>Ka. Regu</td>
        <td></td>
        <td>SPV Produksi</td>
        <td></td>
        <td>Dept Head</td>
        <td></td>
    </tr>
</table>

<!-- ========================================================================================================= -->
<!-- =============================================MIXING====================================================== -->
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
        <td align='center' height='50%'><b><h2>SPK MIXING (Permintaan Resin Produksi)</h2></b></td>
    </tr>
</table>

<br>
<p>Produk yang akan diproduksi:</p>
<table class="gridtable" width='100%' border='1' cellpadding='2'>
    <thead align='center'>
        <tr>
            <th width='5%'>#</th>
            <th width='17%'>No IPP</th>
            <th width='14%'>No SPK</th>
            <th>Produk</th>
            <th width='20%'>Spesifikasi</th>
            <th width='10%'>Qty</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($get_detail_spk as $key => $value) { $key++;
                $spec = spec_bq2($value['id_milik']);
                if($value['id_product'] == 'deadstok'){
                    $spec = "";
                }
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$value['no_ipp']."</td>";
                    echo "<td align='center'>".$value['no_spk']."</td>";
                    echo "<td>".strtoupper($value['product'])."</td>";
                    echo "<td>".$spec."</td>";
                    echo "<td align='center'>".$value['qty']."</td>";
                echo "</tr>";
            }
        ?>
    </tbody>
</table>

<br>
<p>Kebutuhan material:</p>
<?php if(!empty($get_liner_mix)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='6' align='left'>LINER</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_liner_mix as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_str_n1_mix)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='6' align='left'>STRUCTURE NECK 1</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n1_mix as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_str_n2_mix)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='6' align='left'>STRUCTURE NECK 2</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n2_mix as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_structure_mix)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='6' align='left'>STRUCTURE</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_structure_mix as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_external_mix)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='6' align='left'>EXTERNAL</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_external_mix as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>
<?php if(!empty($get_topcoat_mix)){ ?>
    <table class="gridtable" width='100%' border='1' cellpadding='2'>
        <thead align='center'>
            <tr><th colspan='6' align='left'>TOP COAT</th></tr>
            <tr>
                <th width='5%'>#</th>
                <th width='17%'>Kategori</th>
                <th>Material</th>
                <th width='12%'>Kebutuhan (kg)</th>
                <th width='12%'>Aktual Material</th>
                <th width='12%'>Aktual Qty (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_topcoat_mix as $key => $value) { $nomor++;
                    echo "<tr>";
                        echo "<td align='center'>".$nomor."</td>";
                        echo "<td>".$value['nm_category']."</td>";
                        echo "<td>".$value['nm_material']."</td>";
                        echo "<td align='right'>".number_format($value['berat'],3)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <br>
<?php } ?>

<table class="gridtable" width='100%' border='1' cellpadding='2'>
    <tr>
        <th align='left' colspan='6'>NOTE</th>
    </tr>
    <tr>
        <td height='50px' colspan='6'></td>
    </tr>
</table>
<br>
<table class="gridtable3" width='100%' border='0' cellpadding='2'>
    <tr>
        <td>Dibuat,</td>
        <td></td>
        <td>Diperiksa,</td>
        <td></td>
        <td>Diketahui,</td>
        <td></td>
    </tr>
    <tr>
        <td height='25px'></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>Ka. Regu</td>
        <td></td>
        <td>SPV Produksi</td>
        <td></td>
        <td>Dept Head</td>
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
</style>

<?php
$html = ob_get_contents();
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_spk."</i></p>";
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle('SPK Of Production');
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output($kode_spk.' '.date('YmdHis').'.pdf' ,'I');
?>