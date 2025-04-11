<?php
set_time_limit(0);
ob_start();

$Successno			=0;
$ErrorInfo			=0;
$sroot 				= $_SERVER['DOCUMENT_ROOT']; 

function PrintPricePerComp($Nama_APP, $kode_produksi, $koneksi, $printby, $id_product, $product_to, $id_delivery, $id_production, $id_milik, $qty_total, $qty_awal, $qty_akhir){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	// echo $kode_produksi;
	$KpT	= explode('-', $kode_produksi);

	$qChMix	= "	SELECT a.* FROM update_real_list_mixing a
					WHERE a.id_milik = '".$id_milik."'
					AND (('".$qty_awal."' BETWEEN a.qty_awal AND a.qty_akhir )
					OR ('".$qty_akhir."' BETWEEN a.qty_awal AND a.qty_akhir ))
					"; 
	// echo $qChMix;
	$dResultMix	= mysqli_query($conn, $qChMix);
	$rowMix		= mysqli_fetch_array($dResultMix);
	$qty_awal2 	= floatval($rowMix['qty_awal']);
	$qty_akhir2 = floatval($rowMix['qty_akhir']);
	$qty_total2 = ($qty_akhir2 - $qty_awal2) + 1;
	$id_mixing 	= $rowMix['id'];

	$qSupplier	= "	SELECT * FROM production_header WHERE id_produksi = '".$kode_produksi."' ";
	$dResultSp	= mysqli_query($conn, $qSupplier);
	$dHeaderSP	= mysqli_fetch_array($dResultSp);

	$HelpDet 	= "bq_component_header";
	$HelpDet2 	= "bq_component_detail";
	$HelpDet3 	= "bq_component_detail_plus";
	$HelpDet4 	= "bq_component_detail_add";
	$HelpDet5 	= "bq_detail_header";
	if($dHeaderSP['jalur'] == 'FD'){
		$HelpDet 	= "so_component_header";
		$HelpDet2 	= "so_component_detail";
		$HelpDet3 	= "so_component_detail_plus";
		$HelpDet4 	= "so_component_detail_add";
		$HelpDet5 	= "so_detail_header";
	}
	
	$qHeader		= "SELECT * FROM production WHERE no_ipp='".$KpT[1]."' "; 
	$dResult			= mysqli_query($conn, $qHeader);
	$dHeader			= mysqli_fetch_array($dResult);
	
	$qHeader2		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
	$dResult2			= mysqli_query($conn, $qHeader2);
	$dHeader2			= mysqli_fetch_array($dResult2);
	
	
	$qHeaderP		= "SELECT * FROM production_header WHERE id_produksi='".$kode_produksi."' "; 
	$qHeaderD		= "SELECT * FROM production_detail WHERE id_produksi='".$kode_produksi."' AND id_milik='".$id_milik."' AND product_ke='".$product_to."' "; 
	$qDetail1		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	if($dHeader2['parent_product'] == 'shop joint' OR $dHeader2['parent_product'] == 'field joint' OR $dHeader2['parent_product'] == 'branch joint'){
		$qDetail1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='GLASS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  GROUP BY a.id_detail";
	}
	$qDetail2		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	if($dHeader2['parent_product'] == 'shop joint' OR $dHeader2['parent_product'] == 'field joint' OR $dHeader2['parent_product'] == 'branch joint'){
		$qDetail2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='RESIN AND ADD' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  GROUP BY a.id_detail";
	}
	$qDetail2N1		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$qDetail2N2		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$qDetail3		= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND c.id_production_detail = '".$id_production."'  AND a.id_category <> 'TYP-0001' GROUP BY a.id_detail";
	$detailResin1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
	$detailResin2	= "SELECT a.nm_category, a.nm_material,	(a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
	$detailResin2N1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
	$detailResin2N2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
	$detailResin3	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, c.material_terpakai FROM ".$HelpDet2." a INNER JOIN production_real_detail c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND c.id_production_detail = '".$id_production."'  AND a.id_category ='TYP-0001' ORDER BY c.id ASC LIMIT 1 ";
	$qDetailPlus1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail";
	$qDetailPlus2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus2N1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus2N2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus3	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailPlus4	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet3." a LEFT JOIN production_real_detail_plus c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."')  AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail ";
	$qDetailAdd1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
	$qDetailAdd2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
	$qDetailAdd2N1	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 1' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
	$qDetailAdd2N2	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='STRUKTUR NECK 2' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
	$qDetailAdd3	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
	$qDetailAdd4	= "SELECT a.nm_category, a.nm_material, (a.last_cost * $qty_total) AS last_cost,  a.detail_name, a.price_mat, (c.material_terpakai) AS material_terpakai FROM ".$HelpDet4." a LEFT JOIN production_real_detail_add c ON a.id_detail = c.id_detail WHERE a.id_milik = '".$id_milik."' AND a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND (c.id_production_detail = '".$id_mixing."' OR c.id_production_detail = '".$id_production."') GROUP BY a.id_detail ";
	// echo $qDetail2; exit;  
	
	
	$dResultP			= mysqli_query($conn, $qHeaderP);
	$dHeaderP			= mysqli_fetch_array($dResultP);
	
	$dResultD			= mysqli_query($conn, $qHeaderD);
	$dHeaderD			= mysqli_fetch_array($dResultD);
	
	$drestDetail1		= mysqli_query($conn, $qDetail1);
	$drestDetail2		= mysqli_query($conn, $qDetail2);
	$drestDetail2N1		= mysqli_query($conn, $qDetail2N1);
	$drestDetail2N2		= mysqli_query($conn, $qDetail2N2);
	$drestDetail3		= mysqli_query($conn, $qDetail3);

	$numRows3			= mysqli_num_rows($drestDetail3);
	$numRows2N1			= mysqli_num_rows($drestDetail2N1);
	$numRows2N2			= mysqli_num_rows($drestDetail2N2);
	
	$drestResin1		= mysqli_query($conn, $detailResin1);
	$drestResin2		= mysqli_query($conn, $detailResin2);
	$drestResin2N1		= mysqli_query($conn, $detailResin2N1);
	$drestResin2N2		= mysqli_query($conn, $detailResin2N2);
	$drestResin3		= mysqli_query($conn, $detailResin3);
	
	$drestDetailPlus1	= mysqli_query($conn, $qDetailPlus1);
	$drestDetailPlus2	= mysqli_query($conn, $qDetailPlus2);
	$drestDetailPlus2N1	= mysqli_query($conn, $qDetailPlus2N1);
	$drestDetailPlus2N2	= mysqli_query($conn, $qDetailPlus2N2);
	$drestDetailPlus3	= mysqli_query($conn, $qDetailPlus3);
	
	$drestDetailPlus4	= mysqli_query($conn, $qDetailPlus4);
	$NumDetailPlus4		= mysqli_num_rows($drestDetailPlus4);
	
	$drestDetailAdd1	= mysqli_query($conn, $qDetailAdd1);
	$drestDetailAdd2	= mysqli_query($conn, $qDetailAdd2);
	$drestDetailAdd2N1	= mysqli_query($conn, $qDetailAdd2N1);
	$drestDetailAdd2N2	= mysqli_query($conn, $qDetailAdd2N2);
	$drestDetailAdd3	= mysqli_query($conn, $qDetailAdd3);
	$drestDetailAdd4	= mysqli_query($conn, $qDetailAdd4);
	
	$NumDetailAdd1		= mysqli_num_rows($drestDetailAdd1);
	$NumDetailAdd2		= mysqli_num_rows($drestDetailAdd2);
	$NumDetailAdd2N1		= mysqli_num_rows($drestDetailAdd2N1);
	$NumDetailAdd2N2		= mysqli_num_rows($drestDetailAdd2N2);
	$NumDetailAdd3		= mysqli_num_rows($drestDetailAdd3);
	$NumDetailAdd4		= mysqli_num_rows($drestDetailAdd4);
	
	$qHeaderX	= "SELECT a.*, b.* FROM ".$HelpDet." a LEFT JOIN ".$HelpDet5." b ON a.id_milik=b.id
						WHERE a.id_product='".$id_product."' AND a.id_milik ='".$id_milik."' ";
	// echo $qHeader;
	$dResult	= mysqli_query($conn, $qHeaderX);
	$dHeaderX	= mysqli_fetch_array($dResult);
	
	$qIPP	= "SELECT a.* FROM production a WHERE a.no_ipp='".$dHeader2['no_ipp']."' ";
	// echo $qIPP;
	$dIPP	= mysqli_query($conn, $qIPP);
	$dRIPP	= mysqli_fetch_array($dIPP);
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITE</h2><br><h3>PRICE ESTIMATION REAL</h3></b></td>
		</tr>
	</table>
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
			<td width='17%'>Tgl. Produksi</td>
			<td width='1%'>:</td>
			<td width='32%'></td>
			<td width='17%'>SO</td>
			<td width='1%'>:</td>
			<td width='32%'><?= $dHeaderP['so_number'];?></td>
		</tr>
		<tr>
			<td>No. SPK</td>
			<td>:</td>
			<td><?= $dHeaderX['no_spk'];?></td> 
			<td>Customer</td>
			<td>:</td>
			<td><?= $dHeader['nm_customer'];?></td>
		</tr>
		<tr>
			<td width='17%'>No. Mesin</td>
			<td width='1%'>:</td>
			<td width='32%'><?= strtoupper($dHeaderP['nm_mesin']);?></td>
			<td width='17%'>Spec Product</td>
			<td width='1%'>:</td>
			<td width='32%'><?= spec_hasil($id_milik, $HelpDet);?></td>
		</tr>
		<tr>
			<td>Project</td>
			<td>:</td>
			<td><?= strtoupper($dHeader['project']);?></td>
			<td><?= ucwords($dHeaderD['id_category'])." Ke";?></td>
			<td>:</td>
			<td><?= $qty_awal."-".$qty_akhir." (".$dHeaderX['no_komponen'].")";?></td>
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
				<th width='13%'>Category Name</th>
				<th>Material Name</th>
				<th width='11%'>Price (USD)</th>
				<th width='12%'>Total/Kg (Est)</th>
				<th width='11%'>Sub (Est)</th>
				<th width='12%'>Total/Kg (Real)</th>
				<th width='11%'>Sub (Real)</th>
			</tr>
			<tr>
				<th align='left' colspan='7'>LINER THIKNESS / CB</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sumTotDet1		= 0;
			$sumTotDet12	= 0;
			$sumTotDet1Kg	= 0;
			$sumTotDet1Pr	= 0;
			$sumTotDet1Kg2	= 0;
			while($valx = mysqli_fetch_array($drestDetail1)){
				$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
				$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $valx['price_mat']; 
				$warna 	= "";
				$backg	= "#2bff9d";
				if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
					$warna 	= "white";
					$backg	= "black";
				}
				$sumTotDet1 += $TotPrice;
				$sumTotDet12 += $TotPrice2;
				$sumTotDet1Kg += $valx['last_cost'];
				$sumTotDet1Pr += $valx['price_mat'];
				$sumTotDet1Kg2 += $mat_terpakai1;
				?>
			<tr>
				<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
				<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
				<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
			</tr>
				<?php
			}
			$sumTotRes1	= 0;
			$sumTotRes12	= 0;
			$sumTotRes1Kg	= 0;
			$sumTotRes1Pr	= 0;
			$sumTotRes1Kg2	= 0;
			$sumTotRes1Pr2	= 0;
			while($valx = mysqli_fetch_array($drestResin1)){
				$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
				$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
				$warna 	= "";
				$backg	= "#2bff9d";
				if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
					$warna 	= "white";
					$backg	= "black";
				}
				$sumTotRes1 += $TotPrice;
				$sumTotRes12 += $TotPrice2;
				$sumTotRes1Kg += $valx['last_cost'];
				$sumTotRes1Pr += $valx['price_mat'];
				$sumTotRes1Kg2 += $mat_terpakai1;
			?>
			<tr>
				<td class="text-left"><?= strtoupper($valx['nm_category']);?> TOTAL</td>
				<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
				<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
			</tr>
			<?php
			}
			$sumTotPlus1 = 0;
			$sumTotPlus12 = 0;
			$sumTotPlus1Kg = 0;
			$sumTotPlus1Pr = 0;
			$sumTotPlus1Kg2 = 0;
			while($valx = mysqli_fetch_array($drestDetailPlus1)){
				$mat_terpakai1Bf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
				$mat_terpakai1	= $mat_terpakai1Bf;
				$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
				$warna 	= "";
				$backg	= "#2bff9d";
				if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
					$warna 	= "white";
					$backg	= "black";
				}
				$sumTotPlus1 += $TotPrice;
				$sumTotPlus12 += $TotPrice2;
				$sumTotPlus1Kg += $valx['last_cost'];
				$sumTotPlus1Pr += $valx['price_mat'];
				$sumTotPlus1Kg2 += $mat_terpakai1;
				?>
			<tr>
				<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
				<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
				<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
			</tr>
				<?php
			}
			
			$sumTotAdd1 = 0;
			$sumTotAdd12 = 0;
			$sumTotAdd1Kg = 0;
			$sumTotAdd1Pr = 0;
			$sumTotAdd1Kg2 = 0;
			if($NumDetailAdd1 > 0){
				$sumTotAdd1 = 0;
				$sumTotAdd1Kg = 0;
				$sumTotAdd1Kg2 = 0;
				$sumTotAdd1Pr = 0;
				while($valx = mysqli_fetch_array($drestDetailAdd1)){
					$mat_terpakai1Bf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$mat_terpakai1	= $mat_terpakai1Bf;
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
					$warna 	= "";
					$backg	= "";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotAdd1 += $TotPrice;
					$sumTotAdd12 += $TotPrice2;
					$sumTotAdd1Kg += $valx['last_cost'];
					$sumTotAdd1Pr += $valx['price_mat'];
					$sumTotAdd1Kg2 += $mat_terpakai1;
					?> 
				<tr>
					<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
					<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
					<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
					<?php
				}
			}
			$TotLiner	= $sumTotDet1 + $sumTotRes1 + $sumTotAdd1 + $sumTotPlus1;
			$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg + $sumTotAdd1Kg + $sumTotPlus1Kg;
			$TotLinerPr	= $sumTotDet1Pr + $sumTotRes1Pr + $sumTotAdd1Pr + $sumTotPlus1Pr;
			$TotLiner2	= $sumTotDet12 + $sumTotRes12 + $sumTotAdd12 + $sumTotPlus12;
			$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotRes1Kg2 + $sumTotAdd1Kg2 + $sumTotPlus1Kg2;
			?> 
			
			<tr>
				<td class="text-left" colspan='2'><b></b></td>
				<td class="text-right" style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotLinerPr, 2);?></b></td>
				<td class="text-right" style='background-color: bisque;text-align:right;'><b><?= number_format($TotLinerKg,3);?> Kg</b></td>
				<td class="text-right" style='background-color: bisque;text-align:right;'><b><?= number_format($TotLiner, 2);?></b></td>
				<td class="text-right" style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotLinerKg2,3);?> Kg</b></td>
				<td class="text-right" style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotLiner2, 2);?></b></td>
			</tr>
		</tbody>
	</table>
	
	<?php
	if($numRows2N1 > 0){
	?>	
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='7'>STRUKTUR NECK 1</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sumTotDet2N1 = 0;
				$sumTotDet22N1 = 0;
				$sumTotDet2KgN1 = 0;
				$sumTotDet2PrN1 = 0;
				$sumTotDet2Kg2N1 = 0;
				$sumTotDet2Pr2N1 = 0;
				while($valx = mysqli_fetch_array($drestDetail2N1)){ 
					$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotDet2N1 += $TotPrice;
					$sumTotDet22N1 += $TotPrice2;
					$sumTotDet2KgN1 += $valx['last_cost'];
					$sumTotDet2PrN1 += $valx['price_mat'];
					$sumTotDet2Kg2N1 += $mat_terpakai1;
					?>
				<tr>
					<td width='13%' class="text-left"><?= $valx['nm_category'];?></td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td width='11%' style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td width='12%' style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td width='11%' style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td width='12%' style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
					<td width='11%' style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
					<?php
				}
				
				$sumTotRes2N1 = 0;
				$sumTotRes22N1 = 0;
				$sumTotRes2KgN1 = 0;
				$sumTotRes2PrN1 = 0;
				$sumTotRes2Kg2N1 = 0;
				while($valx = mysqli_fetch_array($drestResin2N1)){
					$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotRes2N1 += $TotPrice;
					$sumTotRes22N1 += $TotPrice2;
					$sumTotRes2KgN1 += $valx['last_cost'];
					$sumTotRes2PrN1 += $valx['price_mat'];
					$sumTotRes2Kg2N1 += $mat_terpakai1;
					?>
				<tr>
					<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1, 3);?> Kg</td>
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
				<?php
				}
				$sumTotPlus2N1 = 0;
				$sumTotPlus22N1 = 0;
				$sumTotPlus2KgN1 = 0;
				$sumTotPlus2Kg2N1 = 0;
				$sumTotPlus2PrN1 = 0;
				while($valx = mysqli_fetch_array($drestDetailPlus2N1)){
					$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$detMat	= $detMatBf;
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $detMat * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotPlus2N1 += $TotPrice;
					$sumTotPlus22N1 += $TotPrice2;
					$sumTotPlus2KgN1 += $valx['last_cost'];
					$sumTotPlus2PrN1 += $valx['price_mat'];
					$sumTotPlus2Kg2N1 += $detMat;
					?>
				<tr>
					<td class="text-left"><?= $valx['nm_category'];?></td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td> 
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
					<?php
				}
				$sumTotAdd2N1 = 0;
				$sumTotAdd22N1 = 0;
				$sumTotAdd2KgN1 = 0;
				$sumTotAdd2PrN1 = 0;
				$sumTotAdd2Kg2N1 = 0;
				if($NumDetailAdd2N1 > 0){
					$sumTotAdd2N1 = 0;
					while($valx = mysqli_fetch_array($drestDetailAdd2N1)){
						$mat_terpakai1Bf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
						$mat_terpakai1	= $mat_terpakai1Bf;
						$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotAdd2N1 += $TotPrice;
						$sumTotAdd22N1 += $TotPrice2;
						$sumTotAdd2KgN1 += $valx['last_cost'];
						$sumTotAdd2PrN1 += $valx['price_mat'];
						$sumTotAdd2Kg2N1 += $mat_terpakai1;
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
				}
				$TotStructureN1	= $sumTotDet2N1 + $sumTotRes2N1 + $sumTotAdd2N1 + $sumTotPlus2N1;
				$TotStructureKgN1	= $sumTotDet2KgN1 + $sumTotRes2KgN1 + $sumTotAdd2KgN1 + $sumTotPlus2KgN1;
				$TotStructurePrN1	= $sumTotDet2PrN1 + $sumTotRes2PrN1 + $sumTotAdd2PrN1 + $sumTotPlus2PrN1;
				
				$TotStructure2N1	= $sumTotDet22N1 + $sumTotRes22N1 + $sumTotAdd22N1 + $sumTotPlus22N1;
				$TotStructureKg2N1	= $sumTotDet2Kg2N1 + $sumTotRes2Kg2N1 + $sumTotAdd2Kg2N1 + $sumTotPlus2Kg2N1;
				?> 
				
				<tr style='background-color: #4edcc1;'>
					<td class="text-left" colspan='2'><b></b></td>
					<td style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotStructurePrN1, 2);?></b></td>
					<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotStructureKgN1, 3);?> Kg</b></td>
					<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotStructureN1, 2);?></b></td>
					<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotStructureKg2N1,3);?> Kg</b></td>
					<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotStructure2N1, 2);?></b></td>
				</tr>
			</tbody>
		</table>
		
		
		
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='7'>STRUKTUR NECK 2</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sumTotDet2N2 = 0;
				$sumTotDet22N2 = 0;
				$sumTotDet2KgN2 = 0;
				$sumTotDet2PrN2 = 0;
				$sumTotDet2Kg2N2 = 0;
				$sumTotDet2Pr2N2 = 0;
				while($valx = mysqli_fetch_array($drestDetail2N2)){ 
					$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotDet2N2 += $TotPrice;
					$sumTotDet22N2 += $TotPrice2;
					$sumTotDet2KgN2 += $valx['last_cost'];
					$sumTotDet2PrN2 += $valx['price_mat'];
					$sumTotDet2Kg2N2 += $mat_terpakai1;
					?>
				<tr>
					<td width='13%' class="text-left"><?= $valx['nm_category'];?></td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td width='11%' style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td width='12%' style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td width='11%' style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td width='12%' style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
					<td width='11%' style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
					<?php
				}
				
				$sumTotRes2N2 = 0;
				$sumTotRes22N2 = 0;
				$sumTotRes2KgN2 = 0;
				$sumTotRes2PrN2 = 0;
				$sumTotRes2Kg2N2 = 0;
				while($valx = mysqli_fetch_array($drestResin2N2)){
					$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotRes2N2 += $TotPrice;
					$sumTotRes22N2 += $TotPrice2;
					$sumTotRes2KgN2 += $valx['last_cost'];
					$sumTotRes2PrN2 += $valx['price_mat'];
					$sumTotRes2Kg2N2 += $mat_terpakai1;
					?>
				<tr>
					<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1, 3);?> Kg</td>
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
				<?php
				}
				$sumTotPlus2N2 = 0;
				$sumTotPlus22N2 = 0;
				$sumTotPlus2KgN2 = 0;
				$sumTotPlus2Kg2N2 = 0;
				$sumTotPlus2PrN2 = 0;
				while($valx = mysqli_fetch_array($drestDetailPlus2N2)){
					$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$detMat	= $detMatBf;
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice2	= $detMat * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotPlus2N2 += $TotPrice;
					$sumTotPlus22N2 += $TotPrice2;
					$sumTotPlus2KgN2 += $valx['last_cost'];
					$sumTotPlus2PrN2 += $valx['price_mat'];
					$sumTotPlus2Kg2N2 += $detMat;
					?>
				<tr>
					<td class="text-left"><?= $valx['nm_category'];?></td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td> 
					<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
				</tr>
					<?php
				}
				$sumTotAdd2N2 = 0;
				$sumTotAdd22N2 = 0;
				$sumTotAdd2KgN2 = 0;
				$sumTotAdd2PrN2 = 0;
				$sumTotAdd2Kg2N2 = 0;
				if($NumDetailAdd2N2 > 0){
					$sumTotAdd2N1 = 0;
					while($valx = mysqli_fetch_array($drestDetailAdd2N2)){
						$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
						$detMat	= $detMatBf;
						$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotAdd2N2 += $TotPrice;
						$sumTotAdd22N2 += $TotPrice2;
						$sumTotAdd2KgN2 += $valx['last_cost'];
						$sumTotAdd2PrN2 += $valx['price_mat'];
						$sumTotAdd2Kg2N2 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					</tr>
						<?php
					}
				}
				$TotStructureN2		= $sumTotDet2N2 + $sumTotRes2N2 + $sumTotAdd2N2 + $sumTotPlus2N2;
				$TotStructureKgN2	= $sumTotDet2KgN2 + $sumTotRes2KgN2 + $sumTotAdd2KgN2 + $sumTotPlus2KgN2;
				$TotStructurePrN2	= $sumTotDet2PrN2 + $sumTotRes2PrN2 + $sumTotAdd2PrN2 + $sumTotPlus2PrN2;
				
				$TotStructure2N2	= $sumTotDet22N2 + $sumTotRes22N2 + $sumTotAdd22N2 + $sumTotPlus22N2;
				$TotStructureKg2N2	= $sumTotDet2Kg2N2 + $sumTotRes2Kg2N2 + $sumTotAdd2Kg2N2 + $sumTotPlus2Kg2N2;
				?> 
				
				<tr style='background-color: #4edcc1;'>
					<td class="text-left" colspan='2'><b></b></td>
					<td style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotStructurePrN2, 2);?></b></td>
					<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotStructureKgN2, 3);?> Kg</b></td>
					<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotStructureN2, 2);?></b></td>
					<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotStructureKg2N2,3);?> Kg</b></td>
					<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotStructure2N2, 2);?></b></td>
				</tr>
			</tbody>
		</table>
	<?php } ?>			
		
		
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='7'>STRUKTUR THICKNESS</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sumTotDet2 = 0;
			$sumTotDet22 = 0;
			$sumTotDet2Kg = 0;
			$sumTotDet2Pr = 0;
			$sumTotDet2Kg2 = 0;
			$sumTotDet2Pr2 = 0;
			while($valx = mysqli_fetch_array($drestDetail2)){ 
				$detMat2	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0; 
				$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
				$TotPrice2	= $detMat2 * $valx['price_mat'];
				$warna 	= "";
				$backg	= "#2bff9d";
				if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
					$warna 	= "white";
					$backg	= "black";
				}
				$sumTotDet2 += $TotPrice;
				$sumTotDet22 += $TotPrice2;
				$sumTotDet2Kg += $valx['last_cost'];
				$sumTotDet2Pr += $valx['price_mat'];
				$sumTotDet2Kg2 += $detMat2;
				?>
			<tr>
				<td width='13%' class="text-left"><?= $valx['nm_category'];?></td>
				<td class="text-left"><?= $valx['nm_material'];?></td>
				<td width='11%' style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
				<td width='12%' style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td width='11%' style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				<td width='12%' style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat2,3);?> Kg</td>
				<td width='11%' style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
			</tr>
				<?php
			}
			
			$sumTotRes2 = 0;
			$sumTotRes22 = 0;
			$sumTotRes2Kg = 0;
			$sumTotRes2Pr = 0;
			$sumTotRes2Kg2 = 0;
			while($valx = mysqli_fetch_array($drestResin2)){
				$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
					
				$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
				$TotPrice2	= $mat_terpakai1 * $valx['price_mat'];
				$warna 	= "";
				$backg	= "#2bff9d";
				if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
					$warna 	= "white";
					$backg	= "black";
				}
				$sumTotRes2 += $TotPrice;
				$sumTotRes22 += $TotPrice2;
				$sumTotRes2Kg += $valx['last_cost'];
				$sumTotRes2Pr += $valx['price_mat'];
				$sumTotRes2Kg2 += $mat_terpakai1;
				?>
			<tr>
				<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
				<td class="text-left"><?= $valx['nm_material'];?></td>
				<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1, 3);?> Kg</td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
			</tr>
			<?php
			}
			$sumTotPlus2 = 0;
			$sumTotPlus22 = 0;
			$sumTotPlus2Kg = 0;
			$sumTotPlus2Kg2 = 0;
			$sumTotPlus2Pr = 0;
			while($valx = mysqli_fetch_array($drestDetailPlus2)){
				$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
				$detMat	= $detMatBf;
				$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
				$TotPrice2	= $detMat * $valx['price_mat'];
				$warna 	= "";
				$backg	= "#2bff9d";
				if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
					$warna 	= "white";
					$backg	= "black";
				}
				$sumTotPlus2 += $TotPrice;
				$sumTotPlus22 += $TotPrice2;
				$sumTotPlus2Kg += $valx['last_cost'];
				$sumTotPlus2Pr += $valx['price_mat'];
				$sumTotPlus2Kg2 += $detMat;
				?>
			<tr>
				<td class="text-left"><?= $valx['nm_category'];?></td>
				<td class="text-left"><?= $valx['nm_material'];?></td>
				<td style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td> 
				<td style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice2, 2);?></td>
			</tr>
				<?php
			}
			$sumTotAdd2 = 0;
			$sumTotAdd22 = 0;
			$sumTotAdd2Kg = 0;
			$sumTotAdd2Pr = 0;
			$sumTotAdd2Kg2 = 0;
			if($NumDetailAdd2 > 0){
				$sumTotAdd2 = 0;
				while($valx = mysqli_fetch_array($drestDetailAdd2)){
					$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$detMat	= $detMatBf;
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotAdd2 += $TotPrice;
					$sumTotAdd22 += $TotPrice2;
					$sumTotAdd2Kg += $valx['last_cost'];
					$sumTotAdd2Pr += $valx['price_mat'];
					$sumTotAdd2Kg2 += $detMat;
					?>
				<tr>
					<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
					<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
					<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
					
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice, 2);?></td>
				</tr>
					<?php
				}
			}
			$TotStructure	= $sumTotDet2 + $sumTotRes2 + $sumTotAdd2 + $sumTotPlus2;
			$TotStructureKg	= $sumTotDet2Kg + $sumTotRes2Kg + $sumTotAdd2Kg + $sumTotPlus2Kg;
			$TotStructurePr	= $sumTotDet2Pr + $sumTotRes2Pr + $sumTotAdd2Pr + $sumTotPlus2Pr;
			
			$TotStructure2	= $sumTotDet22 + $sumTotRes22 + $sumTotAdd22 + $sumTotPlus22;
			$TotStructureKg2	= $sumTotDet2Kg2 + $sumTotRes2Kg2 + $sumTotAdd2Kg2 + $sumTotPlus2Kg2;
			?> 
			
			<tr style='background-color: #4edcc1;'>
				<td class="text-left" colspan='2'><b></b></td>
				<td style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotStructurePr, 2);?></b></td>
				<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotStructureKg, 3);?> Kg</b></td>
				<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotStructure, 2);?></b></td>
				<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotStructureKg2,3);?> Kg</b></td>
				<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotStructure2, 2);?></b></td>
			</tr>
		</tbody>
	</table>	


	<?php
	if($numRows3 > 0){
	?>
		<table class='gridtable' width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='7'>EXTERNAL THICKNESS</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sumTotDet3 =0;
				$sumTotDet3Kg =0;
				$sumTotDet3Kg3 =0;
				$sumTotDet3Pr =0;
				while($valx = mysqli_fetch_array($drestDetail3)){
					$detMat	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice3	= $detMat * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotDet3 += $TotPrice;
					$sumTotDet33 += $TotPrice3;
					$sumTotDet3Kg += $valx['last_cost'];
					$sumTotDet3Pr += $valx['price_mat'];
					$sumTotDet3Kg3 += $detMat;
					?>
					<tr>
						<td width='13%' class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td width='11%' class="text-right" style='background-color: <?=$backg;?>; text-align:right; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td width='12%' class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td width='11%' class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
						<td width='12%' class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td>
						<td width='11%' class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice3, 2);?></td>
					</tr>
					<?php
				}
				$sumTotRes3 =0;
				$sumTotRes33 =0;
				$sumTotRes3Kg =0;
				$sumTotRes3Kg3 =0;
				$sumTotRes3Pr =0;
				while($valx = mysqli_fetch_array($drestResin3)){
					$mat_terpakai1 = (!empty($valx['material_terpakai'])?str_replace(',','.',$valx['material_terpakai']):0);
				
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice3	= $mat_terpakai1 * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotRes3 += $TotPrice;
					$sumTotRes33 += $TotPrice3;
					$sumTotRes3Kg += $valx['last_cost'];
					$sumTotRes3Pr += $valx['price_mat'];
					$sumTotRes3Kg3 += $mat_terpakai1;
					?>
				<tr>
					<td class="text-left"><?= $valx['nm_category'];?> TOTAL</td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td class="text-right" style='background-color: <?=$backg;?>; text-align:right; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
					
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'], 3);?> Kg</td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($mat_terpakai1,3);?> Kg</td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice3, 2);?></td>
				</tr>
					<?php
				}
				$sumTotPlus3 =0;
				$sumTotPlus3Kg =0;
				$sumTotPlus3Kg3 =0;
				$sumTotPlus3Pr =0;
				while($valx = mysqli_fetch_array($drestDetailPlus3)){
					$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$detMat	= $detMatBf;$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice3	= $detMat * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotPlus3 += $TotPrice;
					$sumTotPlus33 += $TotPrice3;
					$sumTotPlus3Kg += $valx['last_cost'];
					$sumTotPlus3Pr += $valx['price_mat'];
					$sumTotPlus3Kg3 += $detMat;
					?>
				<tr>
					<td class="text-left"><?= $valx['nm_category'];?></td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td class="text-right" style='background-color: <?=$backg;?>; text-align:right; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
					
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice3, 2);?></td>
				</tr>
					<?php
				}
				$sumTotAdd3 =0;
				$sumTotAdd3Kg =0;
				$sumTotAdd3Kg3 =0;
				$sumTotAdd3Pr =0;
				if($NumDetailAdd3 > 0){
					$sumTotDet3 =0;
					$sumTotDet3Kg =0;
					$sumTotDet3Kg3 =0;
					$sumTotDet3Pr =0;
					while($valx = mysqli_fetch_array($drestDetailAdd3)){
						$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
						$detMat	= $detMatBf;
						$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
						$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
						$TotPrice3	= $detMat * $valx['price_mat'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotDet3 += $TotPrice;
						$sumTotDet33 += $TotPrice3;
						$sumTotDet3Kg += $valx['last_cost'];
						$sumTotDet3Pr += $valx['price_mat'];
						$sumTotDet3Kg3 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= $valx['nm_category'];?></td>
						<td class="text-left"><?= $valx['nm_material'];?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'],3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat,3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice3, 2);?></td>
					</tr>
						<?php
					}
				}
				$TotExternal	= $sumTotDet3 + $sumTotRes3 + $sumTotAdd3 + $sumTotPlus3;
				$TotExternalKg	= $sumTotDet3Kg + $sumTotRes3Kg + $sumTotAdd3Kg + $sumTotPlus3Kg;
				$TotExternalPr	= $sumTotDet3Pr + $sumTotRes3Pr + $sumTotAdd3Pr + $sumTotPlus3Pr;
				
				$TotExternal3		= $sumTotDet33 + $sumTotRes33 + $sumTotAdd33 + $sumTotPlus33;
				$TotExternalKg3	= $sumTotDet3Kg3 + $sumTotRes3Kg3 + $sumTotAdd3Kg3 + $sumTotPlus3Kg3;
				?> 
				
				<tr style='background-color: #4edcc1;'> 
					<td class="text-left" colspan='2'><b></b></td>
					<td style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotExternalPr, 2);?></b></td>
					<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotExternalKg, 3);?> Kg</b></td>
					<td style='background-color: bisque;text-align:right;'><b><?= number_format($TotExternal, 2);?></b></td>
					<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotExternalKg3,3);?> Kg</b></td>
					<td style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotExternal3, 2);?></b></td>
				</tr>
				
				
				
			
			</tbody>
		</table>
		<?php
		}
		?>
		
	<?php
	$TotCoat = 0;
	$TotCoatKg = 0;
	$TotCoatPr = 0;
	if($NumDetailPlus4 > 0){
	?>
		
		<table class='gridtable' width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='7'>TOPCOAT</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$sumTotPlus4 = 0;
				$sumTotPlus44 = 0;
				$sumTotPlus4Kg = 0;
				$sumTotPlus4Kg4 = 0;
				$sumTotPlus4Pr = 0;
				while($valx = mysqli_fetch_array($drestDetailPlus4)){
					$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
					$detMat	= $detMatBf;
					$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
					$TotPrice4	= $detMat * $valx['price_mat'];
					$warna 	= "";
					$backg	= "#2bff9d";
					if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
						$warna 	= "white";
						$backg	= "black";
					}
					$sumTotPlus4 += $TotPrice;
					$sumTotPlus44 += $TotPrice4;
					$sumTotPlus4Kg += $valx['last_cost'];
					$sumTotPlus4Pr += $valx['price_mat'];
					$sumTotPlus4Kg4 += $detMat;
					?>
				<tr>
					<td width='13%' class="text-left"><?= $valx['nm_category'];?></td>
					<td class="text-left"><?= $valx['nm_material'];?></td>
					<td width='11%' class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
					
					<td width='12%' class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'], 3);?> Kg</td>
					<td width='11%' class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
					<td width='12%' class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat, 3);?> Kg</td>
					<td width='11%' class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice4, 2);?></td>
				</tr>
					<?php
				}
				$sumTotAdd4 = 0;
				$sumTotAdd4Kg = 0;
				$sumTotAdd44 = 0;
				$sumTotAdd4Kg4 = 0;
				$sumTotAdd4Pr = 0;
				if($NumDetailAdd4 > 0){
					$sumTotAdd4 = 0;
					$sumTotAdd4Kg = 0;
					$sumTotAdd44 = 0;
					$sumTotAdd4Kg4 = 0;
					$sumTotAdd4Pr = 0;
					while($valx = mysqli_fetch_array($drestDetailAdd4)){
						$detMatBf	= (!empty($valx['material_terpakai']))? str_replace(',','.',$valx['material_terpakai']): 0;
						$detMat	= $detMatBf;
						$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
						$TotPrice	= $valx['last_cost'] * $valx['price_mat'];
						$TotPrice4	= $detMat * $valx['price_mat'];
						$warna 	= "";
						$backg	= "#2bff9d";
						if($valx['price_mat'] == 0 || $valx['price_mat'] == null || $valx['price_mat'] == ''){
							$warna 	= "white";
							$backg	= "black";
						}
						$sumTotAdd4 += $TotPrice;
						$sumTotAdd44 += $TotPrice4;
						$sumTotAdd4Kg += $valx['last_cost'];
						$sumTotAdd4Pr += $valx['price_mat'];
						$sumTotAdd4Kg4 += $detMat;
						?>
					<tr>
						<td class="text-left"><?= strtoupper($valx['nm_category']);?></td>
						<td class="text-left"><?= strtoupper($valx['nm_material']);?></td>
						<td class="text-right" style='background-color: <?=$backg;?>; color: <?=$warna;?>;text-align:right;'><?= number_format($valx['price_mat'], 2);?></td>
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($valx['last_cost'], 3);?> Kg</td>
						<td class="text-right" style='background-color: bisque;text-align:right;'><?= number_format($TotPrice, 2);?></td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($detMat, 3);?> Kg</td>
						<td class="text-right" style='background-color: #ffc4da;text-align:right;'><?= number_format($TotPrice4, 2);?></td>
					</tr>
						<?php
					}
				}
				$TotCoat	= $sumTotAdd4 + $sumTotPlus4;
				$TotCoatKg	= $sumTotAdd4Kg + $sumTotPlus4Kg;
				$TotCoatPr	= $sumTotAdd4Pr + $sumTotPlus4Pr;
				$TotCoat4	= $sumTotAdd44 + $sumTotPlus44;
				$TotCoatKg4	= $sumTotAdd4Kg4 + $sumTotPlus4Kg4;
				?> 
				
				<tr style='background-color: #4edcc1;'>
					<td class="text-left" colspan='2'></td>
					<td class="text-right" style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotCoatPr, 2);?></b></td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><b><?= number_format($TotCoatKg, 3);?> Kg</b></td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><b><?= number_format($TotCoat, 2);?></b></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotCoatKg4, 3);?> Kg</b></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotCoat4, 2);?></b></td>
				</tr>
				<tr style='background-color: #4edcc1; font-size: 18px; color: black;'>
					<td class="text-left" colspan='2'><b>TOTAL ALL</b></td>
					<td class="text-right" style='background-color: #2bff9d;text-align:right;'><b><?= number_format($TotLinerPr + $TotStructurePr + $TotStructurePrN1 + $TotStructurePrN2 + $TotExternalPr + $TotCoatPr, 2);?></b></td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><b><?= number_format($TotLinerKg + $TotStructureKg + $TotStructureKgN1 + $TotStructureKgN2 + $TotExternalKg + $TotCoatKg, 3);?> Kg</b></td>
					<td class="text-right" style='background-color: bisque;text-align:right;'><b><?= number_format($TotLiner + $TotStructure + $TotStructureN1 + $TotStructureN2 + $TotExternal + $TotCoat, 2);?></b></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotLinerKg2 + $TotStructureKg2 + $TotStructureKg2N1 + $TotStructureKg2N2 + $TotExternalKg3 + $TotCoatKg4, 3);?> Kg</b></td>
					<td class="text-right" style='background-color: #ffc4da;text-align:right;'><b><?= number_format($TotLiner2 + $TotStructure2 + $TotStructure2N1 + $TotStructure2N2  + $TotExternal3 + $TotCoat4, 2);?></b></td>
				</tr>
			</tbody>
		</table>
	<?php } ?>
	
	<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 1cm;
		margin-right: 1cm;
		margin-bottom: 0.5cm;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
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
		font-size:9px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
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
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group');
	
	$mpdf->showWatermarkText = true;
	$mpdf->AddPage();  
	$mpdf->SetTitle('Perbandingan Produksi');
	$mpdf->setHTMLFooter($footer);
	$mpdf->WriteHTML($html);
	// $mpdf->Output($kode_produksi.'_'.strtolower($dHeader['nm_product']).'_product_ke_'.$product_to.'.pdf' ,'I');
	$mpdf->Output("PERBANDINGAN_".str_replace('PRO-','',$kode_produksi).'_'.strtoupper($dHeaderX['id_category']).'_'.$id_product.'_('.$qty_awal.'-'.$qty_akhir.')_'.date('YmdHis').'.pdf' ,'I');
	// header('Content-Disposition: attachment;filename="perbandingan_'.str_replace('PRO-','',$id_produksi).'_'.$rowMix[0]['id_category'].'_'.$nama_produk.'_('.$qty_awal.'-'.$qty_akhir.')_'.date('YmdHis').'.xls"');
		
	//exit;
	//return $attachment;
}











function PrintHasilProject($Nama_APP, $koneksi, $printby){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qHeader	= "	(SELECT
						a.*
					FROM
						group_cost_project_finish_fast_table a) UNION (SELECT
						a.*
					FROM
						group_so_cost_project_finish_fast_table a)";
	
	$dResult	= mysqli_query($conn, $qHeader);
	
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='80px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITES</h2></b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>PROJECT FINISH</h2></b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='3%'>No</th>
				<th width='6%'>IPP</th>
				<th>Customer</th>
				<th width='7%'>Series</th>
				<th width='9%'>Tot Mat<br>(Est)</th>
				<th width='8%'>Tot Cost<br>(Est)</th>
				<th width='9%'>Tot Mat<br>(Real)</th>
				<th width='8%'>Tot Cost<br>(Real)</th>
				<th width='5%'>Selisih</th>
				<th width='3%'>Rev</th>
				<th width='12%'>Status</th>
			</tr>
		</thead>
		<tbody>
		
			<?php
			$no = 0;
			$Sum = 0;
			$SumX = 0;
			$Sum2 = 0;
			$SumX2 = 0;
			$Cost = 0;
			while($valx = mysqli_fetch_array($dResult)){
				if($valx['est_mat'] > 0){
					$no++;
					$warna = 'white';
					// if($valx['sts_ipp'] == 'FINISH'){
						// $warna = 'bisque';
					// }
					$ListBQipp		= "SELECT series  FROM bq_detail_header WHERE id_bq = 'BQ-".$valx['no_ipp']."' GROUP BY series";
					$dResult2	= mysqli_query($conn, $ListBQipp);
					$dtListArray = array();
					$h=0;
					while($valx2 = mysqli_fetch_array($dResult2)){
						$dtListArray[$h] = $valx2['series'];
						$h++;
					}
					$dtImplode	= "".implode(", ", $dtListArray)."";
					
					if($valx['estimasi'] == 'Y'){
						if($valx['sts_ipp'] == 'PROCESS PRODUCTION'){
							$status	= "PRODUCTION";
						}
						else if($valx['sts_ipp'] == 'FINISH'){
							if($valx['persenx'] > 100){
								$status	= "OVER BUDGET ".number_format($valx['persenx'])." %";
								$warna = 'bisque';
							}
							if($valx['persenx'] <= 100){
								$status	= "FINISH ".number_format($valx['persenx'])." %";
							}
						}
					}
					
					$SumQty	= $valx['est_mat'];
					$Sum += $SumQty;
					
					$SumQtyX	= $valx['est_harga'];
					$SumX += $SumQtyX;
					
					$SumQty2	= $valx['real_material'];
					$Sum2 += $SumQty2;
					
					$SumQtyX2	= $valx['real_harga'];
					$SumX2 += $SumQtyX2;
					
					
					$HasilAKhir = number_format(($SumX2 / $SumX) * 100);
					?>
					<tr>
						<td style='background-color:<?=$warna;?>' align='center'><?= $no;?></td>
						<td style='background-color:<?=$warna;?>' align='center'><?= $valx['no_ipp'];?></td>
						<td style='background-color:<?=$warna;?>'><?= $valx['nm_customer'];?></td>
						<td style='background-color:<?=$warna;?>'><?= $dtImplode;?></td>
						<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['est_mat'], 3);?> Kg</td>
						<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['est_harga'], 2);?></td>
						<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['real_material'], 3);?> Kg</td>
						<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['real_harga'], 2);?></td>
						<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['persenx']);?> %</td>
						<td style='background-color:<?=$warna;?>' align='center'><?= $valx['rev'];?></td>
						<td style='background-color:<?=$warna;?>' align='left'><?= $status;?></td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td align="center" colspan='4' style='vertical-align:middle;'><b>TOTAL</b></td>
				<td align="right"><b><?= number_format($Sum, 3);?> Kg</b></td>
				<td align="right"><b><?= number_format($SumX, 2);?></b></td>
				<td align="right"><b><?= number_format($Sum2, 3);?> Kg</b></td>
				<td align="right"><b><?= number_format($SumX2, 2);?></b></td>
				<td align="right" style='vertical-align:middle;'><b><?= $HasilAKhir;?> %</b></td> 
				<td align="center" colspan='2'></td>
			</tr>
		</tbody>
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0.2cm;
		margin-left: 0.2cm;
		margin-right: 0.2cm;
		margin-bottom: 0.2cm;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
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
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
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
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group');
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Project Finish');
	$mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Project_Finish_".date('d-m-Y').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintHasilProjectPerBQ($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	// echo $qBQ; exit;
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$qHeader 	= "	SELECT
						a.id_milik,
						a.id_bq,
						b.parent_product AS id_category,
						a.qty,
						b.diameter AS diameter_1,
						b.diameter2 AS diameter_2,
						b.panjang AS length,
						b.thickness,
						b.angle AS sudut,
						b.type,
						a.id_product,
						b.standart_code,
						( a.est_harga * a.qty ) AS est_harga2,
						( a.sum_mat * a.qty ) AS sum_mat2,
						b.pressure,
						b.liner,
						(
							((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) * `a`.`qty` 
						) AS `cost_process`,
						(
							((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
						) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) * a.qty AS foh_consumable,
						(
							((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
						) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) * a.qty AS foh_depresiasi,
						(
							((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
						) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
						(
							((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
						) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) * a.qty AS biaya_non_produksi,
						(
							((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
						) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) * a.qty AS biaya_rutin_bulanan 
					FROM
						estimasi_cost_and_mat a
						INNER JOIN bq_product b ON a.id_milik = b.id
					WHERE
							b.parent_product <> 'pipe slongsong' AND
							a.id_bq = '".$id_bq."' ORDER BY a.id_milik ASC";
	
	$dResult	= mysqli_query($conn, $qHeader);
	
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='80px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
		</tr>
		<tr>
			<td align='center'><b><h3>PROJECT RESULTS <?= $id_bq;?></h3><?= strtoupper($dHeaderBQ['project']); ?><br><?= strtoupper($dHeaderBQ['nm_customer']); ?></b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='4%'>No</th>
				<th width='12%'>Component</th>
				<th width='11%'>Dimention</th>
				<th width='19%'>Product</th>
				<th width='4%'>Qty</th>
				<th width='10%'>Total Material (Est)</th>
				<th width='8%'>Total Cost (Est)</th>
				<th width='8%'>Cost Process</th>
				<th width='10%'>Total Material (Real)</th>
				<th width='8%'>Total Cost (Real)</th>
				<th width='6%'>Selisih</th>
			</tr>
		</thead>
		<tbody>
		
			<?php
			$no = 0;
			$Sum = 0;
			$SumX = 0;
			$Sum2 = 0;
			$SumX2 = 0;
			$Cost = 0;
			while($valx = mysqli_fetch_array($dResult)){
				$no++;
				
				$SumQty	= $valx['sum_mat2'];
				$Sum += $SumQty;
				
				$SumQtyX	= $valx['est_harga2'];
				$SumX += $SumQtyX;
				
				$SumQty2	= $valx['real_material'];
				$Sum2 += $SumQty2;
				
				$SumQtyX2	= $valx['real_harga'];
				$SumX2 += $SumQtyX2;
				
				$Costx2	= $valx['cost_process'] + $valx['foh_consumable'] + $valx['foh_depresiasi'] + $valx['biaya_gaji_non_produksi'] + $valx['biaya_non_produksi'] + $valx['biaya_rutin_bulanan'];
				$Cost += $Costx2;
				
				$TotalCost = $valx['cost_process'] + $valx['foh_consumable'] + $valx['foh_depresiasi'] + $valx['biaya_gaji_non_produksi'] + $valx['biaya_non_produksi'] + $valx['biaya_rutin_bulanan'];
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".$valx['sudut'];
				}
				elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong' OR $valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
				}
				else{$dim = "belum di set";}
				
			?>
			<tr>
				<td style='background-color:<?=$warna;?>' align='center'><?= $no;?></td>
				<td style='background-color:<?=$warna;?>'><?= strtoupper($valx['id_category']);?></td>
				<td style='background-color:<?=$warna;?>'><?= $dim;?></td>
				<td style='background-color:<?=$warna;?>'><?= strtoupper($valx['id_product']);?></td>
				<td style='background-color:<?=$warna;?>' align='center'><?= strtoupper($valx['qty']);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['sum_mat2'], 3);?> Kg</td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['est_harga2'], 2);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($TotalCost,2);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['real_material'], 3);?> Kg</td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['real_harga'], 2);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= back_number(number_format(abs($valx['persenx']),2));?> %</td>
			</tr>
				<?php
			}
			?>
			<tr>
				<td align="center" colspan='5' style='vertical-align:middle;'><b>TOTAL</b></td>
				<td align="right"><b><?= number_format($Sum, 3);?> Kg</b></td>
				<td align="right"><b><?= number_format($SumX, 2);?></b></td>
				<td align="right"><b><?= number_format($Cost, 2);?></b></td>
				<td align="right"><b><?= number_format($Sum2, 3);?> Kg</b></td>
				<td align="right"><b><?= number_format($SumX2, 2);?></b></td>
				<td align="right" style='vertical-align:middle;'><b><?= back_number(number_format(((($Sum2 / $Sum) * 100) - 100), 2));?> %</b></td>
			</tr>
		</tbody>
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 1cm;
		margin-right: 1cm;
		margin-bottom: 0.5cm;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
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
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
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
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group');
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Perbandingan Produksi');
	$mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Hasil_Perbandingan_Project_".$id_bqdate."_".('dmYHis').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function printCostControl($Nama_Beda, $id_product, $koneksi, $printby, $id_milik, $id_bq, $qty){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$KpT	= explode('-', $id_bq);
	
	$qHeaderXX		= "SELECT * FROM production WHERE no_ipp='".$KpT[1]."' ";
	// echo $qHeaderX."<br>";
	$dResultXX			= mysqli_query($conn, $qHeaderXX);
	$dHeaderXX			= mysqli_fetch_array($dResultXX);
	
	$qHeaderP		= "SELECT * FROM production_header WHERE no_ipp='".$KpT[1]."' "; 
	$dResultP			= mysqli_query($conn, $qHeaderP);
	$dHeaderP			= mysqli_fetch_array($dResultP);

	$HelpDet 	= "bq_component_header";
	$HelpDet1 	= "bq_detail_header";
	$HelpDet2 	= "banding_mat";
	if($dHeaderP['jalur'] == 'FD'){
		$HelpDet = "so_component_header";
		$HelpDet1 	= "so_detail_header";
		$HelpDet2 	= "banding_so_mat";
	}
	
	$qHeader		= "SELECT * FROM ".$HelpDet." WHERE id_product='".$id_product."'";
	$qDetail1		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
	$qDetail2		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
	$qDetail3		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
	$qDetail4		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='TOPCOAT' GROUP BY a.id_material";
	
	$detailResin1	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='LINER THIKNESS / CB' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR THICKNESS' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
		
	// echo $qDetail1."<br>";
	// echo $detailResin1."<br>";
	// echo $qDetailPlus1."<br>";
	// echo $qDetailAdd1."<br>";
	$restHeader		= mysqli_query($conn, $qHeader);
	$restDetail1	= mysqli_query($conn, $qDetail1);
	$restDetail2	= mysqli_query($conn, $qDetail2);
	$restDetail3	= mysqli_query($conn, $qDetail3);
	$restDetail4	= mysqli_query($conn, $qDetail4);
	
	$restDetail3Num	= mysqli_num_rows($restDetail3);
	$restDetail4Num	= mysqli_num_rows($restDetail4);
	// $numRows3		= mysqli_query($conn, $qHeader);
	$restResin1			= mysqli_query($conn, $detailResin1);
	$restResin2			= mysqli_query($conn, $detailResin2);
	$restResin3			= mysqli_query($conn, $detailResin3);
	
	// $qCustomer			= "SELECT nm_customer, produk_jual FROM customer WHERE id_customer='".$restHeader[0]['standart_by']."' ";   
	// $restCustomer		= mysqli_query($conn, $qHeader);
	
	//tambahan flange mould /slongsong
	$qDetail2N1		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
	$qDetail2N2		= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.id_material <> 'MTL-1903000' AND a.type_category <> 'TYP-0001' AND a.type_category <> 'TYP-0030' GROUP BY a.id_material";
	
	$detailResin2N1	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 1' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2N2	= "SELECT a.id_bq, nm_category, nm_material, cost, sum(est_material * ".$qty.") as est_material, sum(est_harga * ".$qty.") as est_harga, sum(real_material) as real_material, sum(real_harga) as real_harga, round( ( ( sum(real_harga) / sum(est_harga) ) * 100 )) AS selisih FROM ".$HelpDet2." a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$id_product."' AND a.id_milik='".$id_milik."' AND a.detail_name='STRUKTUR NECK 2' AND a.type_category ='TYP-0001' AND a.type_category <> 'TYP-0030' ORDER BY a.id_detail DESC LIMIT 1 ";
	
	// echo $qDetail2N1;
	
	$restDetail2N1	= mysqli_query($conn, $qDetail2N1);
	$restDetail2N2	= mysqli_query($conn, $qDetail2N2);
	
	$restDetail2N1Num	= mysqli_num_rows($restDetail2N1);
	
	$restResin2N1	= mysqli_query($conn, $detailResin2N1);
	$restResin2N2	= mysqli_query($conn, $detailResin2N2);
	
	
	$qHeaderX	= "SELECT a.*, b.* FROM ".$HelpDet." a INNER JOIN ".$HelpDet1." b ON a.id_milik = b.id 
						WHERE a.id_product='".$id_product."' AND a.id_milik ='".$id_milik."' ";
	// echo $qHeader;
	$dResult	= mysqli_query($conn, $qHeaderX);
	$dHeaderX	= mysqli_fetch_array($dResult);
	
	$qIPP	= "SELECT a.* FROM production a WHERE a.no_ipp='".$dHeader2['no_ipp']."' ";
	// echo $qIPP;
	$dIPP	= mysqli_query($conn, $qIPP);
	$dRIPP	= mysqli_fetch_array($dIPP);

	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
		</tr>
		<tr>
			<td align='center'><b><h3>PROJECT RESULTS</h3></b></td>
		</tr>
	</table>
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
			<td width='15%'>Tgl. Produksi</td>
			<td width='1%'>:</td>
			<td width='34%'></td>
			<td width='15%'>SO</td>
			<td width='1%'>:</td>
			<td width='34%'><?= $dHeaderP['so_number'];?></td>
		</tr>
		<tr>
			<td>No. SPK</td>
			<td>:</td>
			<td></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dHeaderXX['nm_customer'];?></td>
		</tr>
		<tr>
			<td>No. Mesin</td>
			<td>:</td>
			<td><?= $dHeaderP['nm_mesin'];?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= spec_hasil($dHeaderX['id_milik'], $HelpDet);?></td>
		</tr>
		<tr>
			<td>Project</td>
			<td>:</td>
			<td><?= strtoupper($dHeaderXX['project']);?></td>
			<td>Product <?= ucfirst(strtolower($dHeaderX['id_category']));?> </td>
			<td>:</td>
			<td><?= $id_product;?></td>
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
				<th align='center' width='15%'>Category Name</th>
				<th align='center'>Material Name</th>
				<th align='center' width='10%'>Mat Est</th>
				<th align='center' width='10%'>Cost Est</th>
				<th align='center' width='10%'>Mat Real</th>
				<th align='center' width='10%'>Cost Real</th>
				<th align='center' width='10%'>Selisih</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th align='left' colspan='7'>LINER THICKNESS / CB</th>
			</tr>
			<?php
			$sumTotDet1Kg1	= 0;
			$sumTotDet1Pr1	= 0;
			$sumTotDet1Kg	= 0;
			$sumTotDet1Pr	= 0;
			while($valx = mysqli_fetch_array($restDetail1)){
				$sumTotDet1Kg1 += $valx['est_material'];
				$sumTotDet1Pr1 += $valx['est_harga'];
				$sumTotDet1Kg += $valx['real_material'];
				$sumTotDet1Pr += $valx['real_harga'];
			?>
			<tr>
				<td><?= strtoupper($valx['nm_category']);?></td>
				<td><?= strtoupper($valx['nm_material']);?></td>
				<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
				<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
				<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
				<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
				<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
			</tr>
				<?php
			}
			$sumTotRes1Kg1	= 0;
			$sumTotRes1Pr1	= 0;
			$sumTotRes1Kg	= 0;
			$sumTotRes1Pr	= 0; 
			foreach($restResin1 AS $val => $valx){
				$sumTotRes1Kg1 += $valx['est_material'];
				$sumTotRes1Pr1 += $valx['est_harga'];
				$sumTotRes1Kg += $valx['real_material'];
				$sumTotRes1Pr += $valx['real_harga'];
			?>
			<tr>
				<td><?= strtoupper($valx['nm_category']);?></td>
				<td><?= strtoupper($valx['nm_material']);?></td>
				<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
				<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
				<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
				<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
				<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
			</tr>
			<?php
			}
			$TotLinerKg1	= $sumTotDet1Kg1 + $sumTotRes1Kg1;
			$TotLinerPr1	= $sumTotDet1Pr1 + $sumTotRes1Pr1;
			$TotLinerKg	= $sumTotDet1Kg + $sumTotRes1Kg;
			$TotLinerPr	= $sumTotDet1Pr + $sumTotRes1Pr;
			?> 
			
			<tr>
				<td colspan='2'></td>
				<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerKg1, 3);?> Kg</b></td>
				<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerPr1, 2);?></b></td>
				<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerKg, 3);?> Kg</b></td>
				<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerPr, 2);?></b></td>
				<td align='right' style='background-color: #ffc4e7;'><b><?= number_format(($TotLinerPr/$TotLinerPr1)*100);?> %</b></td>
			</tr>
			<?php
			$TotLinerKg2N11	= 0;
			$TotLinerPr2N11	= 0;
			$TotLinerKg2N1	= 0;
			$TotLinerPr2N1	= 0;
			
			$TotLinerKg2N21	= 0;
			$TotLinerPr2N21	= 0;
			$TotLinerKg2N2	= 0;
			$TotLinerPr2N2	= 0;
			if($restDetail2N1Num > 0){
				?>
				<tr>
					<th align='left' colspan='7'>NECK 1 THICKNESS</th>
				</tr>	
				<?php
				$sumTotDet1Kg2N11	= 0;
				$sumTotDet1Pr2N11	= 0;
				$sumTotDet1Kg2N1	= 0;
				$sumTotDet1Pr2N1	= 0;
				foreach($restDetail2N1 AS $val => $valx){
					$sumTotDet1Kg2N11 += $valx['est_material'];
					$sumTotDet1Pr2N11 += $valx['est_harga'];
					$sumTotDet1Kg2N1 += $valx['real_material'];
					$sumTotDet1Pr2N1 += $valx['real_harga'];
					?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
					<?php
				}
				$sumTotRes1Kg2N11	= 0;
				$sumTotRes1Pr2N11	= 0;
				$sumTotRes1Kg2N1	= 0;
				$sumTotRes1Pr2N1	= 0;
				foreach($restResin2N1 AS $val => $valx){
					$sumTotRes1Kg2N11 += $valx['est_material'];
					$sumTotRes1Pr2N11 += $valx['est_harga'];
					$sumTotRes1Kg2N1 += $valx['real_material'];
					$sumTotRes1Pr2N1 += $valx['real_harga'];
				?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
				<?php
				}
				$TotLinerKg2N11	= $sumTotDet1Kg2N11 + $sumTotRes1Kg2N11;
				$TotLinerPr2N11	= $sumTotDet1Pr2N11 + $sumTotRes1Pr2N11;
				$TotLinerKg2N1	= $sumTotDet1Kg2N1 + $sumTotRes1Kg2N1;
				$TotLinerPr2N1	= $sumTotDet1Pr2N1 + $sumTotRes1Pr2N1;
				?> 
				
				<tr style='background-color: #4edcc1;'>
					<td colspan='2'></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerKg2N11, 3);?> Kg</b></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerPr2N11, 2);?></b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerKg2N1, 3);?> Kg</b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerPr2N1, 2);?></b></td>
					<td align='right' style='background-color: #ffc4e7;'><b><?= number_format(($TotLinerPr2N1/$TotLinerPr2N11)*100);?> %</b></td>
				</tr>
				<tr>
					<th align='left' colspan='7'>NECK 2 THICKNESS</th>
				</tr>
				<?php
				$sumTotDet1Kg2N21	= 0;
				$sumTotDet1Pr2N21	= 0;
				$sumTotDet1Kg2N2	= 0;
				$sumTotDet1Pr2N2	= 0;
				foreach($restDetail2N2 AS $val => $valx){
					$sumTotDet1Kg2N21 += $valx['est_material'];
					$sumTotDet1Pr2N21 += $valx['est_harga'];
					$sumTotDet1Kg2N2 += $valx['real_material'];
					$sumTotDet1Pr2N2 += $valx['real_harga'];
					?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
					<?php
				}
				$sumTotRes1Kg2N21	= 0;
				$sumTotRes1Pr2N21	= 0;
				$sumTotRes1Kg2N2	= 0;
				$sumTotRes1Pr2N2	= 0;
				foreach($restResin2N2 AS $val => $valx){
					$sumTotRes1Kg2N21 += $valx['est_material'];
					$sumTotRes1Pr2N21 += $valx['est_harga'];
					$sumTotRes1Kg2N2 += $valx['real_material'];
					$sumTotRes1Pr2N2 += $valx['real_harga'];
				?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
				<?php
				}
				$TotLinerKg2N21	= $sumTotDet1Kg2N21 + $sumTotRes1Kg2N21;
				$TotLinerPr2N21	= $sumTotDet1Pr2N21 + $sumTotRes1Pr2N21;
				$TotLinerKg2N2	= $sumTotDet1Kg2N2 + $sumTotRes1Kg2N2;
				$TotLinerPr2N2	= $sumTotDet1Pr2N2 + $sumTotRes1Pr2N2;
				?> 
				
				<tr style='background-color: #4edcc1;'>
					<td colspan='2'></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerKg2N21, 3);?> Kg</b></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerPr2N21, 2);?></b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerKg2N2, 3);?> Kg</b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerPr2N2, 2);?></b></td>
					<td align='right' style='background-color: #ffc4e7;'><b><?= number_format(($TotLinerPr2N2/$TotLinerPr2N21)*100);?> %</b></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<th align='left' colspan='7'>STRUCTURE THICKNESS</th>
			</tr>
			<?php
			$sumTotDet1Kg21	= 0;
			$sumTotDet1Pr21	= 0;
			$sumTotDet1Kg2	= 0;
			$sumTotDet1Pr2	= 0;
			foreach($restDetail2 AS $val => $valx){
				$sumTotDet1Kg21 += $valx['est_material'];
				$sumTotDet1Pr21 += $valx['est_harga'];
				$sumTotDet1Kg2 += $valx['real_material'];
				$sumTotDet1Pr2 += $valx['real_harga'];
				?>
			<tr>
				<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
			</tr>
				<?php
			}
			$sumTotRes1Kg21	= 0;
			$sumTotRes1Pr21	= 0;
			$sumTotRes1Kg2	= 0;
			$sumTotRes1Pr2	= 0;
			foreach($restResin2 AS $val => $valx){
				$sumTotRes1Kg21 += $valx['est_material'];
				$sumTotRes1Pr21 += $valx['est_harga'];
				$sumTotRes1Kg2 += $valx['real_material'];
				$sumTotRes1Pr2 += $valx['real_harga'];
			?>
			<tr>
				<td><?= strtoupper($valx['nm_category']);?></td>
				<td><?= strtoupper($valx['nm_material']);?></td>
				<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
				<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
				<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
				<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
				<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
			</tr>
			<?php
			}
			$TotLinerKg21	= $sumTotDet1Kg21 + $sumTotRes1Kg21;
			$TotLinerPr21	= $sumTotDet1Pr21 + $sumTotRes1Pr21;
			$TotLinerKg2	= $sumTotDet1Kg2 + $sumTotRes1Kg2;
			$TotLinerPr2	= $sumTotDet1Pr2 + $sumTotRes1Pr2;
			?> 
			
			<tr style='background-color: #4edcc1;'>
				<td colspan='2'></td>
				<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerKg21, 3);?> Kg</b></td>
				<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerPr21, 2);?></b></td>
				<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerKg2, 3);?> Kg</b></td>
				<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerPr2, 2);?></b></td>
				<td align='right' style='background-color: #ffc4e7;'><b><?= number_format(($TotLinerPr2/$TotLinerPr21)*100);?> %</b></td>
			</tr>
			<?php
			$TotLinerKg31	= 0;
			$TotLinerPr31	= 0;
			$TotLinerKg3	= 0;
			$TotLinerPr3	= 0;
			if($restDetail3Num > 0){
			?>
				<tr>
					<th align='left' colspan='7'>EXTERNAL THICKNESS</th>
				</tr>
				<?php
				$sumTotDet1Kg31	= 0;
				$sumTotDet1Pr31	= 0;
				$sumTotDet1Kg3	= 0;
				$sumTotDet1Pr3	= 0;
				foreach($restDetail3 AS $val => $valx){
					$sumTotDet1Kg31 += $valx['est_material'];
					$sumTotDet1Pr31 += $valx['est_harga'];
					$sumTotDet1Kg3 += $valx['real_material'];
					$sumTotDet1Pr3 += $valx['real_harga'];
					?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
					<?php
				}
				$sumTotRes1Kg31	= 0;
				$sumTotRes1Pr31	= 0;
				$sumTotRes1Kg3	= 0;
				$sumTotRes1Pr3	= 0;
				foreach($restResin3 AS $val => $valx){
					$sumTotRes1Kg31 += $valx['est_material'];
					$sumTotRes1Pr31 += $valx['est_harga'];
					$sumTotRes1Kg3 += $valx['real_material'];
					$sumTotRes1Pr3 += $valx['real_harga'];
				?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
				<?php
				}
				$TotLinerKg31	= $sumTotDet1Kg31 + $sumTotRes1Kg31;
				$TotLinerPr31	= $sumTotDet1Pr31 + $sumTotRes1Pr31;
				$TotLinerKg3	= $sumTotDet1Kg3 + $sumTotRes1Kg3;
				$TotLinerPr3	= $sumTotDet1Pr3 + $sumTotRes1Pr3;
				?> 
				
				<tr style='background-color: #4edcc1;'>
					<td colspan='2'></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerKg31, 3);?> Kg</b></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerPr31, 2);?></b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerKg3, 3);?> Kg</b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerPr3, 2);?></b></td>
					<td align='right' style='background-color: #ffc4e7;'><b><?= number_format(($TotLinerPr3/$TotLinerPr31)*100);?> %</b></td>
				</tr>
			<?php
			}
			$TotLinerKg41	= 0;
			$TotLinerPr41	= 0;
			$TotLinerKg4	= 0;
			$TotLinerPr4	= 0;
			if($restDetail4Num > 0){
			?>
				<tr>
					<th align='left' colspan='7'>TOPCOAT</th>
				</tr>
				<?php
				$sumTotDet1Kg41	= 0;
				$sumTotDet1Pr41	= 0;
				$sumTotDet1Kg4	= 0;
				$sumTotDet1Pr4	= 0;
				foreach($restDetail4 AS $val => $valx){
					$sumTotDet1Kg41 += $valx['est_material'];
					$sumTotDet1Pr41 += $valx['est_harga'];
					$sumTotDet1Kg4 += $valx['real_material'];
					$sumTotDet1Pr4 += $valx['real_harga'];
					?>
				<tr>
					<td><?= strtoupper($valx['nm_category']);?></td>
					<td><?= strtoupper($valx['nm_material']);?></td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_material'])?$valx['est_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #c4ffec;'><?= number_format((!empty($valx['est_harga'])?$valx['est_harga']:0), 2);?></td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_material'])?$valx['real_material']:0), 3);?> Kg</td>
					<td align='right' style='background-color: #d5ffc4;'><?= number_format((!empty($valx['real_harga'])?$valx['real_harga']:0), 2);?></td>
					<td align='right' style='background-color: #ffc4e7;'><?= number_format(((!empty($valx['selisih']))?$valx['selisih']:'0'));?> %</td>
				</tr>
					<?php
				}
				$TotLinerKg41	= $sumTotDet1Kg41;
				$TotLinerPr41	= $sumTotDet1Pr41;
				$TotLinerKg4	= $sumTotDet1Kg4;
				$TotLinerPr4	= $sumTotDet1Pr4;
				if($TotLinerPr4 == 0 AND $TotLinerPr41 == 0){
					$hasilx = 0;
				}
				else{
					$hasilx = ($TotLinerPr4/$TotLinerPr41)*100;
				}
				?> 
				
				<tr>
					<td colspan='2'></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerKg41, 3);?> Kg</b></td>
					<td align='right' style='background-color: #c4ffec;'><b><?= number_format($TotLinerPr41, 2);?></b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerKg4, 3);?> Kg</b></td>
					<td align='right' style='background-color: #d5ffc4;'><b><?= number_format($TotLinerPr4, 2);?></b></td>
					<td align='right' style='background-color: #ffc4e7;'><b><?= number_format($hasilx);?> %</b></td>
				</tr>
			<?php
			}
			$TotFinalEstMat		= $TotLinerKg1 + $TotLinerKg2N11 + $TotLinerKg2N21 + $TotLinerKg21 + $TotLinerKg31 + $TotLinerKg41;
			$TotFinalEstCost	= $TotLinerPr1 + $TotLinerPr2N11 + $TotLinerPr2N21 + $TotLinerPr21 + $TotLinerPr31 + $TotLinerPr41;
			$TotFinalRealMat	= $TotLinerKg + $TotLinerKg2N1 + $TotLinerKg2N2 + $TotLinerKg2 + $TotLinerKg3 + $TotLinerKg4;
			$TotFinalRealCost	= $TotLinerPr + $TotLinerPr2N1 + $TotLinerPr2N2 + $TotLinerPr2 + $TotLinerPr3 + $TotLinerPr4;
			
			if($TotFinalRealCost == 0 AND $TotFinalEstCost == 0){
				$hasilxFinal = 0;
			}
			else{
				$hasilxFinal = (($TotFinalRealCost/$TotFinalEstCost)*100);
			}
			?>
			<tr>
				<td colspan='2'><b>SUM TOTAL</b></td>
				<td align='right' width='10%' style='background-color: #c4ffec;'><b><?= number_format((!empty($TotFinalEstMat)?$TotFinalEstMat:0), 3);?> Kg</b></td>
				<td align='right' width='10%' style='background-color: #c4ffec;'><b><?= number_format((!empty($TotFinalEstCost)?$TotFinalEstCost:0), 2);?></b></td>
				<td align='right' width='10%' style='background-color: #d5ffc4;'><b><?= number_format((!empty($TotFinalRealMat)?$TotFinalRealMat:0), 3);?> Kg</b></td>
				<td align='right' width='10%' style='background-color: #d5ffc4;'><b><?= number_format((!empty($TotFinalRealCost)?$TotFinalRealCost:0), 2);?></b></td>
				<td align='right' width='10%' style='background-color: #ffc4e7;'><b><?= number_format($hasilxFinal);?> %</td>
			</tr>
		</tbody>
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0.5cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0.5cm;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
	}
	.font{
		font-family: verdana,arial,sans-serif;
		font-size:14px;
	}
	.fontheader{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
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
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
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
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group');
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Project Finish Per Product');
	// $mpdf->AddPage('L');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Project_Finish_Per_Product_".$id_bq."_".$id_product."_".date('d-m-Y').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintTotalMaterial($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	// echo $qBQ; exit;
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$qHeader	= "SELECT * FROM hasil_material_project_table WHERE id_bq='".$id_bq."' AND created='".$printby."' ORDER BY nm_category ASC ";
	
	$dResult	= mysqli_query($conn, $qHeader);
	
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='80px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITES</h2></b></td>
		</tr>
		<tr>
			<td align='center'><b><h3>PROJECT RESULTS <?= $id_bq;?></h3><?= strtoupper($dHeaderBQ['project']); ?><br><?= strtoupper($dHeaderBQ['nm_customer']); ?></b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th style='vertical-align:middle;' width='15%'>Category</th>
				<th style='vertical-align:middle;' width='30%'>Material Name</th>
				<th style='vertical-align:middle;' width='11%'>Est Material</th>
				<th style='vertical-align:middle;' width='11%'>Est Cost</th>
				<th style='vertical-align:middle;' width='11%'>Real Material</th>
				<th style='vertical-align:middle;' width='11%'>Real Harga</th>
				<th style='vertical-align:middle;' width='11%'>Selisih</th>   
			</tr>
		</thead>
		<tbody>
			<?php
			$Total1 = 0;
			$Total2 = 0;
			$Total3 = 0;
			$Total4 = 0;
			foreach($dResult AS $val => $valx){
				$Total1 += $valx['est_material'];
				$Total2 += $valx['est_harga'];
				$Total3 += $valx['real_material'];
				$Total4 += $valx['real_harga'];
				
				if($Total4 <= 0 OR $Total2 <= 0){
					$hasilx = 0;
				}
				else{
					$hasilx = (($Total4/$Total2)*100);
				}
				
				if($valx['real_harga'] <= 0 OR $valx['est_harga'] <= 0){
					$hasilxx = 0;
				}
				else{
					$hasilxx = (($valx['real_harga']/$valx['est_harga'])*100);
				}
				
				echo "<tr>";
					echo "<td>".$valx['nm_category']."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['est_material'],3)." Kg</td>";
					echo "<td align='right'>".number_format($valx['est_harga'],2)."</td>";
					echo "<td align='right'>".number_format($valx['real_material'],3)." Kg</td>";
					echo "<td align='right'>".number_format($valx['real_harga'],2)."</td>";
					echo "<td align='right'>".number_format($hasilxx)." %</td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td colspan='2'><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 3);?> Kg</b></td>
				<td align='right'><b><?= number_format($Total2, 2);?></b></td>
				<td align='right'><b><?= number_format($Total3, 3);?> Kg</b></td>
				<td align='right'><b><?= number_format($Total4, 2);?></b></td>
				<td align='right'><b><?= number_format($hasilx);?> %</b></td>  
			</tr>
		</tbody>
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 1cm;
		margin-right: 1cm;
		margin-bottom: 0.5cm;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
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
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
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
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group');
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Total Material Project');
	$mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Total_Material_Project_".$id_bq."_".date('d-m-Y').".pdf" ,'I');

	//exit;
	//return $attachment;
}

function PrintHasilProjectPerBQFinish($Nama_APP, $koneksi, $printby, $id_bq){
	
	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);
	
	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".substr($id_bq, 3,9)."' ";
	// echo $qBQ; exit;
	$dResulBQ	= mysqli_query($conn, $qBQ);
	$dHeaderBQ	= mysqli_fetch_array($dResulBQ);
	
	$qHeader	= "SELECT 
							a.*,
							b.foh_consumable,
							b.foh_depresiasi,
							b.biaya_gaji_non_produksi,
							b.biaya_non_produksi,
							b.biaya_rutin_bulanan
						FROM 
							cost_lain_banding a INNER JOIN cost_lain_sum_detail b ON a.id=b.id
						WHERE 
							a.id_bq = '".$id_bq."'";
	
	$dResult	= mysqli_query($conn, $qHeader);
	
	?>
	
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='80px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
			<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
		</tr>
		<tr>
			<td align='center'><b><h3>PROJECT RESULTS <?= $id_bq;?></h3><?= strtoupper($dHeaderBQ['project']); ?><br><?= strtoupper($dHeaderBQ['nm_customer']); ?></b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th width='4%'>No</th>
				<th width='12%'>Component</th>
				<th width='11%'>Dimention</th>
				<th width='23%'>Product</th>
				<th width='4%'>Qty</th>
				<th width='10%'>Total Material (Est)</th>
				<th width='9%'>Total Cost (Est)</th>
				<th width='10%'>Total Material (Real)</th>
				<th width='9%'>Total Cost (Real)</th>
				<th width='8%'>Selisih</th>
			</tr>
		</thead>
		<tbody>
		
			<?php
			$no = 0;
			$Sum = 0;
			$SumX = 0;
			$Sum2 = 0;
			$SumX2 = 0;
			$Cost = 0;
			while($valx = mysqli_fetch_array($dResult)){
				$no++;
				
				$SumQty	= $valx['sum_mat2'];
				$Sum += $SumQty;
				
				$SumQtyX	= $valx['est_harga2'];
				$SumX += $SumQtyX;
				
				$SumQty2	= $valx['real_material'];
				$Sum2 += $SumQty2;
				
				$SumQtyX2	= $valx['real_harga'];
				$SumX2 += $SumQtyX2;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".$valx['sudut'];
				}
				elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
				}
				else{$dim = "belum di set";}
				
			?>
			<tr>
				<td style='background-color:<?=$warna;?>' align='center'><?= $no;?></td>
				<td style='background-color:<?=$warna;?>'><?= strtoupper($valx['id_category']);?></td>
				<td style='background-color:<?=$warna;?>'><?= $dim;?></td>
				<td style='background-color:<?=$warna;?>'><?= strtoupper($valx['id_product']);?></td>
				<td style='background-color:<?=$warna;?>' align='center'><?= strtoupper($valx['qty']);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['sum_mat2'], 3);?> Kg</td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['est_harga2'], 2);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['real_material'], 3);?> Kg</td>
				<td style='background-color:<?=$warna;?>' align='right'><?= number_format($valx['real_harga'], 2);?></td>
				<td style='background-color:<?=$warna;?>' align='right'><?= back_number(number_format($valx['persenx'],2));?> %</td>
			</tr>
				<?php
			}
			?>
			<tr>
				<td align="center" colspan='5' style='vertical-align:middle;'><b>TOTAL</b></td>
				<td align="right"><b><?= number_format($Sum, 3);?> Kg</b></td>
				<td align="right"><b><?= number_format($SumX, 2);?></b></td>
				<td align="right"><b><?= number_format($Sum2, 3);?> Kg</b></td>
				<td align="right"><b><?= number_format($SumX2, 2);?></b></td>
				<td align="right" style='vertical-align:middle;'><b><?= back_number(number_format(((($Sum2 / $Sum) * 100) - 100), 2));?> %</b></td>
			</tr>
		</tbody>
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 1cm;
		margin-right: 1cm;
		margin-bottom: 0.5cm;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
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
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
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
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	// exit;
	$html = ob_get_contents(); 
	ob_end_clean(); 
	// flush();
	// $mpdf->SetWatermarkText('ORI Group');
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Perbandingan Produksi');
	$mpdf->AddPage('L');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("Hasil_Perbandingan_Project_".$id_bq."_".date('dmYHis').".pdf" ,'I');

	//exit;
	//return $attachment;
}

?>