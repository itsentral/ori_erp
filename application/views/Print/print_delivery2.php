<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
// $sroot 		= $_SERVER['DOCUMENT_ROOT'].'/ori_dev_arwant';
include $sroot."/application/libraries/MPDF57/mpdf.php";
// include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf=new mPDF('utf-8','A4');
$mpdf->defaultheaderline=0;

set_time_limit(0);
ini_set('memory_limit','1024M');

//Beginning Buffer to save PHP variables and HTML tags
ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('l, d F Y [H:i:s]');

$header 		= $this->db->get_where('production', array('no_ipp'=>str_replace('PRO-','',$result[0]['id_produksi'])))->result();
$header_del 	= $this->db->get_where('delivery_product', array('kode_delivery'=>$kode_delivery))->result();
$alamat 		= $header_del[0]->alamat;
$project 		= $header_del[0]->project;

$nomorSO = array();
$nomorPO = array();
foreach($result	 AS $val => $valx){ $val++;
	$EXPLODE 	= explode('-',$valx['product_code']);
	$no_so 		= $EXPLODE[0];
	if(empty($valx['product_code'])){
		$no_so 	= $valx['no_drawing'];
	}
	$nomorSO[] 	= $no_so;

	$no_ipp = str_replace('PRO-','',$valx['id_produksi']);
	$tandaTanki = substr($no_ipp,0,4);
	if($tandaTanki != 'IPPT'){
		$no_po = get_name('billing_so','no_po','no_ipp',$no_ipp);
	}else{
		$no_po = $tanki_model->get_ipp_detail($no_ipp)['no_po'];
	}
	$nomorPO[] = $no_po;
}

$NOS_OS = implode('/',array_unique($nomorSO));
$NOS_PO = implode('/',array_unique($nomorPO));

$linkImage = $sroot."/assets/images/ori_logo.jpg";


$HTML_HEADER = '<table class="gridtable2" width="100%" border="0" style="border-bottom:none; margin-top:0px;">';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td rowspan="7" style="border-right:none; border-bottom:none; ">';
$HTML_HEADER .= '<img src="'.$linkImage.'" alt="" height="90" width="80" >';
$HTML_HEADER .= '</td>';
$HTML_HEADER .= '<td rowspan="7" align="center"  style="border-bottom:none; border-left:none;">';
$HTML_HEADER .= '<b>PT. ORI POLYTEC COMPOSITES</b><br>';
$HTML_HEADER .= 'Jl. Akasia II Block A9/3<br>';
$HTML_HEADER .= 'Cikarang - Bekasi - Indonesia<br>';
$HTML_HEADER .= 'Telp : (021) 8972193<br>';
$HTML_HEADER .= 'Fax  : 8972192';
$HTML_HEADER .= '</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-right:none; border-bottom: none;" width="8%">Form No</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-left:none; border-right:none; border-bottom: none;" width="1%">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-left:none; border-bottom: none;" width="30%">'.strtoupper($header_del[0]->fm_no).'</td>';
$HTML_HEADER .= '<td style="border-bottom:none;" colspan="3" rowspan="2" align="center"><u><b>DISTRIBUSI SURAT JALAN</b></u></td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="font-size: 9px; border-right:none; border-top: none; border-bottom: none;">Rev. No</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-left:none; border-top: none; border-bottom: none;">'.$header_del[0]->rev.'</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="font-size: 9px; border-right:none; border-top: none;">Issue Date</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-right:none; border-top: none; border-left:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-left:none; border-top: none;">'.$header_del[0]->issue_date.'</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-right: none; border-top: none;" width="8%">Putih/Asli</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border:none;" width="1%">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-left: none; border-top: none;" width="18%">Penagihan / Finance</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td colspan="3" style="border-bottom:none;" align="center"><u><b>SURAT JALAN</b></u></td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Merah</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">PPIC/Logistik</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO</td>';
$HTML_HEADER .= '<td style="font-size: 12px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;">'.strtoupper($header_del[0]->nomor_sj).'</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Kuning</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Pembeli / Penerima</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO. PO</td>';
$HTML_HEADER .= '<td style="font-size: 12px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;">'.$NOS_PO.'</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Hijau</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Cost Control</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="font-size: 12px; border-bottom:none; border-top: none; border-right: none;">NO. SO</td>';
$HTML_HEADER .= '<td style="font-size: 12px; border:none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 12px; border-bottom:none; border-top: none; border-left:none;">'.$NOS_OS.'</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-right: none;">Biru</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border: none;">:</td>';
$HTML_HEADER .= '<td style="font-size: 9px; border-bottom:none; border-top: none; border-left:none;">Security</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '</table>';

$HTML_HEADER .= '<table class="gridtable2" width="100%" border="0">';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td width="15%" style="border-bottom:none; border-right: none; vertical-align: bottom;" height="30px">Pengemudi</td>';
$HTML_HEADER .= '<td width="2%" style="border-bottom:none; border-left: none;  border-right: none; vertical-align: bottom;">:</td>';
$HTML_HEADER .= '<td width="20%" style="border-bottom:none; border-left: none; border-right: none; vertical-align: bottom;">................................</td>';
$HTML_HEADER .= '<td rowspan="3" style="border-bottom:none; border-right: none; border-left: none;"></td>';
$HTML_HEADER .= '<td width="50%" rowspan="3" style="border-bottom:none; border-left: none; vertical-align: top; padding-top:10px;">';
$HTML_HEADER .= 'Place & Date: Cikarang, '.date('d F Y',strtotime($header_del[0]->delivery_date)).'<br>';
$HTML_HEADER .= 'To, <br>';
$HTML_HEADER .= '<b>'.$alamat.'</b>';
$HTML_HEADER .= '</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="border-bottom:none; border-right: none;  border-top: none;">No. Container</td>';
$HTML_HEADER .= '<td style="border: none;">:</td>';
$HTML_HEADER .= '<td style="border: none;">................................</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="border-bottom:none; border-right: none;  border-top: none;">No. Seal</td>';
$HTML_HEADER .= '<td style="border: none;">:</td>';
$HTML_HEADER .= '<td style="border: none;">................................</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="border-bottom:none; border-right: none;  border-top: none;">Jenis Kendaraan</td>';
$HTML_HEADER .= '<td style="border: none;">:</td>';
$HTML_HEADER .= '<td style="border: none;">................................</td>';
$HTML_HEADER .= '<td rowspan="2" style="border-right: none; border-left: none; border-top: none;"></td>';
$HTML_HEADER .= '<td rowspan="2" style="border-left: none; vertical-align: top; border-top: none;">';
$HTML_HEADER .= 'Project, <br>';
$HTML_HEADER .= '<b>'.$project.'</b>';
$HTML_HEADER .= '</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '<tr>';
$HTML_HEADER .= '<td style="border-right: none;  border-top: none;">No. Polisi</td>';
$HTML_HEADER .= '<td style="border-right: none;  border-top: none; border-left: none;">:</td>';
$HTML_HEADER .= '<td style="border-right: none;  border-top: none; border-left: none;">................................</td>';
$HTML_HEADER .= '</tr>';
$HTML_HEADER .= '</table>';
?>
<table class="gridtable2" width='100%' border='1' cellpadding='2'>
	<thead>
		<tr>
			<td align='center' width='10%'>QTY</td>
			<td align='center' width='5%'>UNIT</td>
			<!-- <td align='center' style='vertical-align:middle;'>NAMA DAN UKURAN BARANG</td> -->
			<td align='center' style='vertical-align:middle;'>ITEM CUST/DESC CUST</td>
		</tr>
	</thead>
	<?php
	foreach($result	 AS $val => $valx){ $val++;
		$series 	= get_name('so_detail_header','series','id',$valx['id_milik']);
		$product 	= strtoupper($valx['product']).", ".$series.", DIA ".spec_bq2($valx['id_milik']);
		$SATUAN 	= 'PCS';
		$QTY 		= $valx['qty_product'];

		$ID_MILIK 	= (!empty($GET_ID_MILIK[$valx['id_milik']]))?$GET_ID_MILIK[$valx['id_milik']]:'';
		$GET_DESC 	= (!empty($GET_DESC_DEAL[$ID_MILIK]))?$GET_DESC_DEAL[$ID_MILIK]:'';
		$DESC 		= (!empty($GET_DESC))?$GET_DESC:$valx['desc'];
		$ALIGN 		= "center";
		if($valx['sts_product'] == 'so material'){
			$product 	= strtoupper(get_name('raw_materials','nm_material','id_material',$valx['product']));
			$SATUAN 	= 'KG';
			$QTY 		= number_format($valx['berat'],4);
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "right";
			$DESC 		= (!empty($valx['desc']))?$valx['desc']:$product;
		}

		if($valx['sts_product'] == 'field joint'){
			$SATUAN 	= 'KIT';
			$QTY 		= number_format($valx['berat']);
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "right";
			$DESC 		= (!empty($valx['desc']))?$valx['desc']:$product;
		}

		if($valx['sts_product'] == 'deadstok'){
			$SATUAN 	= 'PCS';
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "center";
			$DESC 		= $valx['product'].', '.$valx['kode_spk'].'x'.$valx['length'];
		}

		if($valx['sts'] == 'aksesoris'){
			$SATUAN 	= 'PCS';
			$QTY 		= number_format($valx['qty_product'],2);
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "center";
			$DESC 		= $valx['no_drawing'];
		}

		if($valx['sts'] == 'cut'){
			$getDetailCut = $this->db
							->get_where('delivery_product_detail a', array(
								'a.kode_delivery' => $kode_delivery, 
								'sts' => 'cut',
								'id_milik'=>$valx['id_milik']
							))->result_array();
			$LENGTH_CUT = "";
			foreach ($getDetailCut as $key => $value) {
				$LENGTH_CUT .= "/ ".number_format($value['length']);
			}

			$SATUAN 	= 'PCS';
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "center";
			$DESC 		= strtoupper($valx['product']).', <i>Cut '.$LENGTH_CUT.'</i>';
		}
		if($valx['type_product'] == 'deadstok'){
			$SATUAN 	= 'PCS';
			$ID_MILIK 	= '';
			$GET_DESC 	= '';
			$ALIGN 		= "center";
			$DESC 		= (!empty($valx['desc']))?$valx['desc']:strtoupper($valx['product']);
		}
		
        if($valx['type_product'] != 'spool' AND $valx['type_product'] != 'spool_tanki'){
            echo "<tr>";
                echo "<td align='".$ALIGN."'>".$QTY."</td>";
                echo "<td align='center'>".strtolower($SATUAN)."</td>";
                // echo "<td align='left'>".$GET_DESC."</td>";
                echo "<td align='left'>".$DESC."</td>";
            echo "</tr>";
        }

        if($valx['type_product'] == 'spool'){
            $getSpoolDetail = $this->db
                                    ->order_by('a.id', 'asc')
                                    ->group_by('a.id_milik, a.sts, a.cutting_ke')
                                    ->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki, b.nm_tanki')
                                    ->where('(a.berat > 0 OR a.berat IS NULL)')
									->join('production_detail b','a.id_pro=b.id','left')
                                    ->get_where('delivery_product_detail a', array(
                                        'a.kode_delivery' => $kode_delivery, 
                                        'a.sts_product' => NULL,
                                        'a.spool_induk'=>$valx['spool_induk'],
                                        'a.kode_spool'=>$valx['kode_spool'],
                                    ))->result_array();
            $DEAT = "";
            $NO_DRAWING = [];
            foreach ($getSpoolDetail as $key => $value) {
                $series 	= get_name('so_detail_header','series','id',$value['id_milik']);
		        $product 	= "<b>".strtoupper($value['product'])."</b>, ".$series.", DIA ".spec_bq2($value['id_milik']);
				if($value['sts'] == 'loose_dead'){
					$product 	= "<b>".strtoupper($value['product'])."</b>, DIA ".$value['kode_spk']." x ".number_format($value['length']);
				}
				if($value['sts'] == 'cut'){
					$product 	= "<b>".strtoupper($value['product'])."</b> ".$series.", DIA ".spec_bq2($value['id_milik']).", <i>Cut ".number_format($value['length'])."</i>";
				}
				if($value['type_product'] == 'tanki'){
					$product 	= "<b>".strtoupper($value['product_tanki'])."</b>, ".$tanki_model->get_spec($value['id_milik'])."</i>";
				}
				$DEAT .= ", (<b>".$value['qty_product']."</b> ". $product.")";
				$NO_DRAWING[] = $value['no_drawing'];
            }

			$NODraw = '';
			if(!empty($NO_DRAWING)){
				$ARrUniq = array_unique($NO_DRAWING);
				$NODraw = implode(',',$ARrUniq);
			}


            $DESC 	= $valx['spool_induk'].'-'.$valx['kode_spool'].$DEAT.' - '.$NODraw;
            echo "<tr>";
                echo "<td align='".$ALIGN."'>".$QTY."</td>";
                echo "<td align='center'>".strtolower($SATUAN)."</td>";
                // echo "<td align='left'>".$GET_DESC."</td>";
                echo "<td align='left'>".$DESC."</td>";
            echo "</tr>";
        }

		if($valx['type_product'] == 'spool_tanki'){
            $getSpoolDetail = $this->db
                                    ->order_by('a.id', 'asc')
                                    ->group_by('a.spool_induk, a.kode_spool')
                                    ->select('COUNT(a.id_milik) AS qty_product, a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki, b.nm_tanki')
                                    ->where('(a.berat > 0 OR a.berat IS NULL)')
									->join('production_detail b','a.id_pro=b.id','left')
                                    ->get_where('delivery_product_detail a', array(
                                        'a.kode_delivery' => $kode_delivery, 
                                        'a.sts_product' => NULL,
                                        'a.spool_induk'=>$valx['spool_induk'],
                                        'a.kode_spool'=>$valx['kode_spool'],
                                    ))->result_array();
            $DEAT = "";
            foreach ($getSpoolDetail as $key => $value) {
				$DEAT .= ", ".$value['nm_tanki'];
            }


            $DESC 	= $valx['spool_induk'].'-'.$valx['kode_spool'].$DEAT;
            echo "<tr>";
                echo "<td align='".$ALIGN."'>".$QTY."</td>";
                echo "<td align='center'>".strtolower($SATUAN)."</td>";
                // echo "<td align='left'>".$GET_DESC."</td>";
                echo "<td align='left'>".$DESC."</td>";
            echo "</tr>";
        }
	}
	echo "<tr>";
		echo "<td colspan='2'></td>";
		echo "<td><b>Note: Barang dikirim dalam keadaan baik.</b></td>";
	echo "<tr>";
		echo "<td colspan='2' style='border-bottom:none;'></td>";
		echo "<td style='border-bottom:none;'></td>";
	echo "</tr>";
	?>
</table>
<table class="gridtable2" width='100%' border='0' cellpadding='2'>
	<tr>
		<td width='15%' align='center'>Dibuat,</td>
		<td width='15%' align='center'>Diperiksa,</td>
		<td align='center'>Mengetahui,</td>
		<td width='15%' align='center'>Dicek oleh,</td>
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
		<td align='center'>Cost Control &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Factory Manager</td>
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
$html = ob_get_contents();
// echo $HTML_HEADER;
// exit;
ob_end_clean();
$mpdf->SetHeader($HTML_HEADER);
$mpdf->SetTitle($kode_delivery); 
$mpdf->AddPageByArray([
	'margin-left' => 2,
	'margin-right' => 2,
	'margin-top' => 80,
	'margin-bottom' => 2
]);
$mpdf->WriteHTML($html);
$mpdf->Output('SJ delivery '.$kode_delivery.'.pdf' ,'I');