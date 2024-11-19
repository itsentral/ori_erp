<?php
set_time_limit(0);
ob_start();

$Successno			=0;
$ErrorInfo			=0;
$sroot 				= $_SERVER['DOCUMENT_ROOT'];

function PrintSPK1($Nama_APP, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery, $id_milik, $qty){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	// print_r($KONN); exit;

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot. "/".$Nama_APP."/application/libraries/PHPMailer/PHPMailerAutoload.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y H:i:s');


	$qHeader2	= "	SELECT
						a.*
					FROM
						production_header a
						LEFT JOIN production_detail b ON a.id_produksi=b.id_produksi
					WHERE
						a.id_produksi='".$kode_produksi."'
						AND b.id_delivery = '".$id_delivery."'
						LIMIT 1";

	$dResult2	= mysqli_query($conn, $qHeader2);
	$dHeader2	= mysqli_fetch_array($dResult2);
	
	$HelpDet_BDH 	= "bq_detail_header";
	$HelpDet_BCH 	= "bq_component_header";
	$HelpDet_BCD 	= "bq_component_detail";
	$HelpDet_BCL 	= "bq_component_lamination";
	if($dHeader2['jalur'] == 'FD'){
		$HelpDet_BDH 	= "so_detail_header";
		$HelpDet_BCH 	= "so_component_header";
		$HelpDet_BCD 	= "so_component_detail";
		$HelpDet_BCL 	= "so_component_lamination";
	}

	$qHeader	= "SELECT a.*, b.* FROM ".$HelpDet_BCH." a INNER JOIN ".$HelpDet_BDH." b ON a.id_milik = b.id
						WHERE a.id_product='".$kode_product."' AND a.id_milik ='".$id_milik."' ";
	// echo $qHeader;
	$dResult	= mysqli_query($conn, $qHeader);
	$dHeader	= mysqli_fetch_array($dResult);

	$qIPP	= "SELECT a.* FROM production a WHERE a.no_ipp='".$dHeader2['no_ipp']."' ";
	// echo $qIPP;
	$dIPP	= mysqli_query($conn, $qIPP);
	$dRIPP	= mysqli_fetch_array($dIPP);

	if($dHeader['id_category'] == 'pipe'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['length'])." x ".floatval($dHeader['thickness']);
	}
	elseif($dHeader['id_category'] == 'elbow mitter' OR $dHeader['id_category'] == 'elbow mould'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['thickness']).", ".$dHeader['type']." ".$dHeader['sudut'];
	}
	elseif($dHeader['id_category'] == 'concentric reducer' OR $dHeader['id_category'] == 'reducer tee mould' OR $dHeader['id_category'] == 'eccentric reducer' OR $dHeader['id_category'] == 'reducer tee slongsong'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['diameter_2'])." x ".floatval($dHeader['thickness']);
	}
	elseif($dHeader['id_category'] == 'end cap' OR $dHeader['id_category'] == 'flange slongsong' OR $dHeader['id_category'] == 'flange mould' OR $dHeader['id_category'] == 'equal tee mould' OR $dHeader['id_category'] == 'blind flange' OR $dHeader['id_category'] == 'equal tee slongsong'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['thickness']);
	}
  elseif($dHeader['id_category'] == 'branch joint'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['diameter_2'])." x ".floatval($dHeader['joint_thickness']);
	}
  elseif($dHeader['id_category'] == 'shop joint' OR $dHeader['id_category'] == 'field joint'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['joint_thickness']);
	}
	else{$dim = "belum di set";}

	?>

	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader2['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>DAILY PRODUCTION REPORT</h2></b></td>
			<td>Rev</td>
			<td></td>
		</tr>
		<tr>
			<td>Due Date</td>
			<td></td>
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
			<td width='20%'>Tgl. Produksi</td>
			<td width='1%'>:</td>
			<td width='29%'></td>
			<td width='20%'>No. SO</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $dHeader2['so_number']; ?></td>
		</tr>
		<tr>
			<td>No. SPK</td>
			<td>:</td>
			<td><?= $dHeader['no_spk'];?></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dRIPP['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>No. Mesin</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= $dim;?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dRIPP['project']); ?></td>
			<td style='vertical-align:top;'><?= ucwords($dHeader['parent_product']);?> To</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= str_replace('-','-',$product_to)." (".strtoupper(strtolower($dHeader['no_komponen'])).") of ".$dHeader['qty']." Component";?></td>
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
				<th width='13%'>Material</th>
				<th width='27%'>Tipe Material</th>
				<th width='10%'>Berat Material</th>
				<th width='15%'>Lot/Batch Num</th>
				<th width='10%'>Actual Type</th>
				<th width='8%'>Terpakai</th>
			</tr>
			<tr>
				<th align='left' colspan='6'>GLASS</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, id_category  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='GLASS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001' ";
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner); 
		// echo $tDetailLiner; exit;
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$dataL	= ($valx['layer'] == 0.00)?'-':(floatval($valx['layer']) == 0)?'-':floatval($valx['layer']);
			
			
			$tDetailLinerx	= "SELECT category FROM raw_categories WHERE id_category='".$valx['id_category']."' ";
			$dDetailLinerx	= mysqli_query($conn, $tDetailLinerx);
			$valxy = mysqli_fetch_array($dDetailLinerx);
			?>
			<tr>
				<td><?= $valxy['category'];?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?php
		}



		?>

		</tbody>

		</table>
		<table class="gridtable" width='100%' border='1' cellpadding='2'>

			<tr>
				<th align='left' colspan='6'>RESIN & ADD</th>
			</tr>
      <tr>
				<th width='13%'>Jenis Material</th>
				<th width='27%'>Material</th>
				<th width='10%'>Persentase</th>
				<th width='15%'>Berat Material</th>
				<th width='10%'>Persentase</th>
				<th width='8%'>Berat Material</th>
			</tr>


		<?php
		$tDetailLiner	= "SELECT *  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' LIMIT 6 ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$SUn	= "";
			if($valx['id_category'] == 'TYP-0005'){
				$SUn	= " | ".floatval($valx['jumlah']);
			}
			?>
			<tr>
				<td width='13%'><?= $valx['nm_category'];?></td>
				<td width='27%'><?= $valx['nm_material'];?></td>
				<td width='10%' align='right'><?= floatval($valx['percentage']);?> %</td>
				<td align='right' width='15%'><?= $valx['material_weight'] * $qty;?> Kg</td>
				<td width='10%'></td>
				<td width='8%'></td>
			</tr>
			<?php
			if($valx['id_category'] == 'TYP-0005'){
			?>
			<tr>
				<td colspan='2'></td>
				<td><b>Jumlah Benang</b></td>
				<td align='right'><?= floatval($valx['jumlah'])?></td>
				<td colspan='3'><b>Actual Jumlah Benang</b></td>
				<td></td>
			</tr>
			<tr>
				<td colspan='2'></td>
				<td><b>Bandwidch</b></td>
				<td align='right'><?= floatval($valx['bw'])?></td>
				<td colspan='3'><b>Actual Bandwidch</b></td>
				<td></td>
			</tr>
			<?php
			}
		}


		?>

		<?php
		$tDetailLiner	= "SELECT * FROM ".$HelpDet_BCL." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='Inside Lamination' ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		$numRows		= mysqli_num_rows($dDetailLiner);
    //echo $tDetailLiner;
    //exit;
		if($numRows > 0){
				?>

						<tr>
							<th align='left' colspan='6'>INSIDE LAMINATION</th>
						</tr>
            <tr>
              <th >Lapisan ke-</th>
							<th >Glass Configuration</th>
							<th >Width</th>
							<th >Stage</th>
							<th >Actual Glass Configuration</th>
							<th >Actual Widyth</th>
    				</tr>


				<?php


			while($valx = mysqli_fetch_array($dDetailLiner)){
				$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
				?>
				<tr>
          <td class="text-left">
            <?= $valx['lapisan'];?>
          </td>
          <td class="text-left"><?= $valx['glass'];?></td>
          <td align='right'><?= floatval($valx['width']);?></td>
          <?php if ($stage != $valx['stage']):
            $stage = $valx['stage'];
          ?>
            <td style="text-align:center;vertical-align:middle" rowspan="10"><?= $stage;?></td>
          <?php endif; ?>
          <td class="text-right"></td>
          <td class="text-right"></td>


				</tr>
				<?php
			}

		}
    $tDetailoutside	= "SELECT * FROM ".$HelpDet_BCL." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='Outside Lamination' AND width > 0";
		// echo $tDetailoutside;
		$dDetailoutside	= mysqli_query($conn, $tDetailoutside);
		$numRows		= mysqli_num_rows($dDetailoutside);
    //echo $tDetailoutside;
    //exit;
		if($numRows > 0){
				?>
					<thead align='center'>
						<tr>
							<th align='left' colspan='6'>OUTSIDE LAMINATION</th>
						</tr>
            <tr>
              <th >Lapisan ke-</th>
							<th >Glass Configuration</th>
							<th >Width</th>
							<th >Stage</th>
							<th >Actual Glass Configuration</th>
							<th >Actual Widyth</th>
    				</tr>
					</thead>
					<tbody>
				<?php
        $stage = '';

			while($valx = mysqli_fetch_array($dDetailoutside)){
				?>
				<tr>
          <td class="text-left">
            <?= $valx['lapisan'];?>
          </td>
          <td class="text-left"><?= $valx['glass'];?></td>
          <td align='right'><?= floatval($valx['width']);?></td>
          <?php if ($stage != $valx['stage']):
            $stage = $valx['stage'];
            $n_s	= "SELECT * FROM ".$HelpDet_BCL." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='Outside Lamination' AND stage = '$stage'";
        		// echo $tDetailLiner;
        		$n_s_	= mysqli_query($conn, $n_s);
        		$numRowss		= mysqli_num_rows($n_s_);
          ?>
            <td style="text-align:center;vertical-align:middle" rowspan="<?=$numRowss?>"><?= $stage;?></td>
          <?php endif; ?>
          <td class="text-right"></td>
          <td class="text-right"></td>


				</tr>
				<?php
			}

		}
		?>
		</tbody>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='6'>THICKNESS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Thickness Est</b></td>
				<td align='center'><b><?= floatval($dHeader['est']);?></b></td>
				<td><b>Thickness Act (Web)</b></td>
				<td></td>
				<td><b>Thickness Act (Dry)</b></td>
				<td width='80px'></td>
			</tr>
			<tr>
				<td><b>Status : Reject / Pass</b></td>
				<td colspan='2'><b>Inspector :</b></td>
				<td width='100px'><b>Ttd : </b></td>
				<td colspan='2'><b>Tgl Isnpeksi : </b></td>
			</tr>
			<tr>
				<td height='60px' colspan='6' style='vertical-align: top;'><b>Note :</b></td>
			</tr>
		</tbody>
	</table>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='9'>MACHINE SETUP</th>
			</tr>
			<tr>
				<th><b>#</b></th>
				<th><b>Standard</b></th>
				<th><b>Actual</b></th>
				<th><b>#</b></th>
				<th><b>Standard</b></th>
				<th><b>Actual</b></th>
				<th><b>#</b></th>
				<th><b>Standard</b></th>
				<th><b>Actual</b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align='center'>RPM</td>
				<td></td>
				<td></td>
				<td align='center'>TENTION</td>
				<td></td>
				<td></td>
				<td align='center'>SUDUT ROOVING</td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<div id='space'></div>
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
	<div id='space'></div>
  <style type="text/css">
  	@page {
  		margin-top: 1cm;
  		margin-left: 1.5cm;
  		margin-right: 1cm;
  		margin-bottom: 1cm;
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
  		padding: 6px;
  		border-style: solid;
  		border-color: #666666;
  		background-color: #f2f2f2;
  	}
  	table.gridtable th.head {
  		border-width: 1px;
  		padding: 6px;
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

  	table.gridtable3 {
  		font-family: verdana,arial,sans-serif;
  		font-size:9px;
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

  	table.cooltabs {
  		font-size:12px;
  		font-family: verdana,arial,sans-serif;
  	}

  	table.cooltabs th.reg {
  		font-family: verdana,arial,sans-serif;
  		border-radius: 5px 5px 5px 5px;
  		background: #e3e0e4;
  		padding: 5px;
  	}
  	table.cooltabs td.reg {
  		font-family: verdana,arial,sans-serif;
  		border-radius: 5px 5px 5px 5px;
  		padding: 5px;
  	}
  	#cooltabs {
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 5px 5px 5px 5px;
  		background: #e3e0e4;
  		padding: 5px;
  		width: 800px;
  		height: 20px;
  	}
  	#cooltabs2{
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 5px 5px 5px 5px;
  		background: #e3e0e4;
  		padding: 5px;
  		width: 180px;
  		height: 10px;
  	}
  	#space{
  		padding: 3px;
  		width: 180px;
  		height: 1px;
  	}
  	#cooltabshead{
  		font-size:12px;
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 5px 5px 0 0;
  		background: #dfdfdf;
  		padding: 5px;
  		width: 162px;
  		height: 10px;
  		float:left;
  	}
  	#cooltabschild{
  		font-size:10px;
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 0 0 5px 5px;
  		padding: 5px;
  		width: 162px;
  		height: 10px;
  		float:left;
  	}
  	p {
  		margin: 0 0 0 0;
  	}
  	p.pos_fixed {
  		font-family: verdana,arial,sans-serif;
  		position: fixed;
  		top: 30px;
  		left: 230px;
  	}
  	p.pos_fixed2 {
  		font-family: verdana,arial,sans-serif;
  		position: fixed;
  		top: 589px;
  		left: 230px;
  	}
  	.barcode {
  		padding: 1.5mm;
  		margin: 0;
  		vertical-align: top;
  		color: #000044;
  	}
  	.barcodecell {
  		text-align: center;
  		vertical-align: middle;
  		position: fixed;
  		top: -10px;
  		right: 10px;
  	}
  	.barcodecell2 {
  		text-align: center;
  		vertical-align: middle;
  		position: fixed;
  		top: 548px;
  		right: 10px;
  	}
  	p.barcs {
  		font-family: verdana,arial,sans-serif;
  		font-size:11px;
  		position: fixed;
  		top: 38px;
  		right: 115px;
  	}
  	p.barcs2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:11px;
  		position: fixed;
  		top: 591px;
  		right: 115px;
  	}
  	p.pt {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 62px;
  		left: 5px;
  	}
  	p.alamat {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 71px;
  		left: 5px;
  	}
  	p.tlp {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 80px;
  		left: 5px;
  	}
  	p.pt2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 596px;
  		left: 5px;
  	}
  	p.alamat2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 605px;
  		left: 5px;
  	}
  	p.tlp2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 614px;
  		left: 5px;
  	}
  	#hrnew {
  		border: 0;
  		border-bottom: 1px dashed #ccc;
  		background: #999;
  	}
  </style>


	<?php

	$html = ob_get_contents();
	// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_produksi." / ".$kode_product." / ".$dRIPP['no_ipp']." / <b>First</b></i></p>";
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('SPK Of Production');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output($kode_produksi.'_'.strtolower($dHeader['nm_product']).'_product_ke_'.$product_to.'.pdf' ,'I');

	//exit;
	//return $attachment;
}

function PrintSPK2($Nama_APP, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery, $id_milik, $qty){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot. "/".$Nama_APP."/application/libraries/PHPMailer/PHPMailerAutoload.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');


	// $qHeader2	= "SELECT * FROM production_header WHERE id_produksi='".$kode_produksi."' ";
	$qHeader2	= "	SELECT
						a.*
					FROM
						production_header a
						LEFT JOIN production_detail b ON a.id_produksi=b.id_produksi
					WHERE
						a.id_produksi='".$kode_produksi."'
						AND b.id_delivery = '".$id_delivery."'
						LIMIT 1";
	// echo $qHeader2;
	$dResult2	= mysqli_query($conn, $qHeader2);
	$dHeader2	= mysqli_fetch_array($dResult2);
	
	$HelpDet_BDH 	= "bq_detail_header";
	$HelpDet_BCH 	= "bq_component_header";
	$HelpDet_BCD 	= "bq_component_detail";
	$HelpDet_BCDP 	= "bq_component_detail_plus";
	$HelpDet_BCDA 	= "bq_component_detail_add";
	if($dHeader2['jalur'] == 'FD'){
		$HelpDet_BDH 	= "so_detail_header";
		$HelpDet_BCH 	= "so_component_header";
		$HelpDet_BCD 	= "so_component_detail";
		$HelpDet_BCDP 	= "so_component_detail_plus";
		$HelpDet_BCDA 	= "so_component_detail_add";
	}

	$qHeader	= "SELECT a.*, b.* FROM ".$HelpDet_BCH." a INNER JOIN ".$HelpDet_BDH." b ON a.id_milik = b.id
						WHERE a.id_product='".$kode_product."' AND a.id_milik ='".$id_milik."' ";
	// echo $qHeader;
	$dResult	= mysqli_query($conn, $qHeader);
	$dHeader	= mysqli_fetch_array($dResult);

	$qIPP	= "SELECT a.* FROM production a WHERE a.no_ipp='".$dHeader2['no_ipp']."' ";
	// echo $qIPP;
	$dIPP	= mysqli_query($conn, $qIPP);
	$dRIPP	= mysqli_fetch_array($dIPP);

	if($dHeader['id_category'] == 'pipe'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['length'])." x ".floatval($dHeader['thickness']);
	}
	elseif($dHeader['id_category'] == 'elbow mitter' OR $dHeader['id_category'] == 'elbow mould'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['thickness']).", ".$dHeader['type']." ".$dHeader['sudut'];
	}
	elseif($dHeader['id_category'] == 'concentric reducer' OR $dHeader['id_category'] == 'reducer tee mould' OR $dHeader['id_category'] == 'eccentric reducer' OR $dHeader['id_category'] == 'reducer tee slongsong'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['diameter_2'])." x ".floatval($dHeader['thickness']);
	}
	elseif($dHeader['id_category'] == 'end cap' OR $dHeader['id_category'] == 'flange slongsong' OR $dHeader['id_category'] == 'flange mould' OR $dHeader['id_category'] == 'equal tee mould' OR $dHeader['id_category'] == 'blind flange' OR $dHeader['id_category'] == 'equal tee slongsong'){
		$dim = floatval($dHeader['diameter_1'])." x ".floatval($dHeader['thickness']);
	}
	else{$dim = "belum di set";}

	?>

	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='15%' rowspan='3'></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Nomor Dok.</td>
			<td width='15%'><?= $dRIPP['no_ipp'];?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>DAILY PRODUCTION REPORT</h2></b></td>
			<td>Rev.</td>
			<td></td>
		</tr>
		<tr>
			<td>Tgl Berlaku</td>
			<td></td>
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
			<td width='24%'>Tgl. Produksi</td>
			<td width='1%'>:</td>
			<td width='25%'></td>
			<td width='24%'>SO</td>
			<td width='1%'>:</td>
			<td width='25%'><?= $dHeader2['so_number']; ?></td>
		</tr>
		<tr>
			<td>No. SPK</td>
			<td>:</td>
			<td></td>
			<td width='24%'>Customer</td>
			<td width='1%'>:</td>
			<td width='25%'><?= $dRIPP['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>No. Mesin</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= $dim;?></td>
		</tr>
		<tr>
			<td>Project</td>
			<td>:</td>
			<td><?= strtoupper($dRIPP['project']); ?></td>
			<td><?= ucwords($dHeader['parent_product']);?> Ke</td>
			<td>:</td>
			<td><?= $product_to." (".strtoupper(strtolower($dHeader['no_komponen'])).")";?></td>
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

			<tr>
				<th align='left' colspan='6'>RESIN & ADD</th>
			</tr>
      <tr>
				<th width='13%'>Jenis Material</th>
				<th width='27%'>Material</th>
				<th width='10%'>Persentase</th>
				<th width='15%'>Berat Material</th>
				<th width='10%'>Persentase</th>
				<th width='8%'>Berat Material</th>
			</tr>


		<?php
		$tDetailLiner	= "SELECT *  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$SUn	= "";
			if($valx['id_category'] == 'TYP-0005'){
				$SUn	= " | ".floatval($valx['jumlah']);
			}
			?>
			<tr>
				<td width='13%'><?= $valx['nm_category'];?></td>
				<td width='27%'><?= $valx['nm_material'];?></td>
				<td width='10%' align='right'><?= $valx['percentage'];?> %</td>
				<td width='15%'><?= $valx['material_weight'] * $qty;?></td>
				<td width='10%'></td>
				<td width='8%'></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<tr>
				<th align='left' colspan='7'>NOTE</th>
			</tr>
			<tr>
				<td height='50px' colspan='7'></td>
			</tr>
	</table>


	<style type="text/css">
	@page {
		margin-top: 1cm;
		margin-left: 1.5cm;
		margin-right: 1cm;
		margin-bottom: 1cm;
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
		padding: 6px;
		border-style: solid;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 6px;
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

	table.cooltabs {
		font-size:12px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
	}
	table.cooltabs th.reg {
		font-family: verdana,arial,sans-serif;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
	}
	table.cooltabs td.reg {
		font-family: verdana,arial,sans-serif;
		border-radius: 5px 5px 5px 5px;
		padding: 5px;
	}
	#cooltabs {
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
		width: 800px;
		height: 20px;
	}
	#cooltabs2{
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 5px 5px;
		background: #e3e0e4;
		padding: 5px;
		width: 180px;
		height: 10px;
	}
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	#cooltabshead{
		font-size:12px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 5px 5px 0 0;
		background: #dfdfdf;
		padding: 5px;
		width: 162px;
		height: 10px;
		float:left;
	}
	#cooltabschild{
		font-size:10px;
		font-family: verdana,arial,sans-serif;
		border-width: 1px;
		border-style: solid;
		border-radius: 0 0 5px 5px;
		padding: 5px;
		width: 162px;
		height: 10px;
		float:left;
	}
	p {
		margin: 0 0 0 0;
	}
	p.pos_fixed {
		font-family: verdana,arial,sans-serif;
		position: fixed;
		top: 30px;
		left: 230px;
	}
	p.pos_fixed2 {
		font-family: verdana,arial,sans-serif;
		position: fixed;
		top: 589px;
		left: 230px;
	}
	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000044;
	}
	.barcodecell {
		text-align: center;
		vertical-align: middle;
		position: fixed;
		top: -10px;
		right: 10px;
	}
	.barcodecell2 {
		text-align: center;
		vertical-align: middle;
		position: fixed;
		top: 548px;
		right: 10px;
	}
	p.barcs {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		position: fixed;
		top: 38px;
		right: 115px;
	}
	p.barcs2 {
		font-family: verdana,arial,sans-serif;
		font-size:11px;
		position: fixed;
		top: 591px;
		right: 115px;
	}
	p.pt {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 62px;
		left: 5px;
	}
	p.alamat {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 71px;
		left: 5px;
	}
	p.tlp {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 80px;
		left: 5px;
	}
	p.pt2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 596px;
		left: 5px;
	}
	p.alamat2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 605px;
		left: 5px;
	}
	p.tlp2 {
		font-family: verdana,arial,sans-serif;
		font-size:7px;
		position: fixed;
		top: 614px;
		left: 5px;
	}
	#hrnew {
		border: 0;
		border-bottom: 1px dashed #ccc;
		background: #999;
	}
	</style>


	<?php

	$html = ob_get_contents();
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_produksi." / ".$kode_product." / ".$dRIPP['no_ipp']." / <b>Second</b></i></p>";
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('SPK Of Production');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output($kode_produksi.'_'.strtolower($dHeader['nm_product']).'_product_ke_'.$product_to.'.pdf' ,'I');
}

function PrintSPK12($Nama_APP, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_delivery, $id_milik, $qty){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	// print_r($KONN); exit;

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot. "/".$Nama_APP."/application/libraries/PHPMailer/PHPMailerAutoload.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('D, d-M-Y H:i:s');


	$qHeader2	= "	SELECT
						a.*
					FROM
						production_header a
						LEFT JOIN production_detail b ON a.id_produksi=b.id_produksi
					WHERE
						a.id_produksi='".$kode_produksi."'
						AND b.id_delivery = '".$id_delivery."'
						LIMIT 1";

	$dResult2	= mysqli_query($conn, $qHeader2);
	$dHeader2	= mysqli_fetch_array($dResult2);
	
	$HelpDet_BDH 	= "bq_detail_header";
	$HelpDet_BCH 	= "bq_component_header";
	$HelpDet_BCD 	= "bq_component_detail";
	$HelpDet_BCL 	= "bq_component_lamination";
	if($dHeader2['jalur'] == 'FD'){
		$HelpDet_BDH 	= "so_detail_header";
		$HelpDet_BCH 	= "so_component_header";
		$HelpDet_BCD 	= "so_component_detail";
		$HelpDet_BCL 	= "so_component_lamination";
	}

	$qHeader	= "SELECT a.*, b.*, b.id AS id_unik FROM ".$HelpDet_BCH." a INNER JOIN ".$HelpDet_BDH." b ON a.id_milik = b.id
						WHERE a.id_product='".$kode_product."' AND a.id_milik ='".$id_milik."' ";
	// echo $qHeader;
	$dResult	= mysqli_query($conn, $qHeader);
	$dHeader	= mysqli_fetch_array($dResult);

	$qIPP	= "SELECT a.* FROM production a WHERE a.no_ipp='".$dHeader2['no_ipp']."' ";
	// echo $qIPP;
	$dIPP	= mysqli_query($conn, $qIPP);
	$dRIPP	= mysqli_fetch_array($dIPP);

	?>

	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader2['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>DAILY PRODUCTION REPORT</h2></b></td>
			<td>Rev</td>
			<td></td>
		</tr>
		<tr>
			<td>Due Date</td>
			<td></td>
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
			<td width='20%'>Production Date</td>
			<td width='1%'>:</td>
			<td width='29%'></td>
			<td width='20%'>SO Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $dHeader2['so_number']; ?></td>
		</tr>
		<tr>
			<td>SPK Number</td>
			<td>:</td>
			<td><?= $dHeader['no_spk'];?></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dRIPP['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>Machine Number</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= spec_fd($dHeader['id_unik'], $HelpDet_BDH);?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dRIPP['project']); ?></td>
			<td style='vertical-align:top;'><?= ucwords($dHeader['parent_product']);?> To</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= str_replace('-','-',$product_to)." (".strtoupper(strtolower($dHeader['no_komponen'])).") of ".$dHeader['qty']." Component";?></td>
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
				<th width='13%'>Material</th>
				<th>Material Type</th>
				<th width='12%'>Weight</th>
				<th width='12%'>Lot/Batch Num</th>
				<th width='12%'>Actual Type</th>
				<th width='12%'>Used</th>
			</tr>
			<tr>
				<th align='left' colspan='6'>GLASS</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, id_category  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='GLASS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001' ";
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner); 
		// echo $tDetailLiner; exit;
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$dataL	= ($valx['layer'] == 0.00)?'-':(floatval($valx['layer']) == 0)?'-':floatval($valx['layer']);
			
			
			$tDetailLinerx	= "SELECT category FROM raw_categories WHERE id_category='".$valx['id_category']."' ";
			$dDetailLinerx	= mysqli_query($conn, $tDetailLinerx);
			$valxy = mysqli_fetch_array($dDetailLinerx);
			?>
			<tr>
				<td><?= $valxy['category'];?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?php
		}
		?>
		</tbody>
		</table>
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<tr>
				<th align='left' colspan='6'>RESIN & ADD</th>
			</tr>
			<tr>
				<th width='13%'>Material</th>
				<th>Material Type</th>
				<th width='12%'>Persentase</th>
				<th width='12%'>Weight</th>
				<th width='12%'>Real Persentase</th>
				<th width='12%'>Used</th>
			</tr>


		<?php
		$tDetailLiner	= "SELECT *  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' LIMIT 6 ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$SUn	= "";
			if($valx['id_category'] == 'TYP-0005'){
				$SUn	= " | ".floatval($valx['jumlah']);
			}
			?>
			<tr>
				<td><?= $valx['nm_category'];?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= floatval($valx['percentage']);?> %</td>
				<td align='right'><?= $valx['material_weight'] * $qty;?> Kg</td>
				<td></td>
				<td></td>
			</tr>
			<?php
			if($valx['id_category'] == 'TYP-0005'){
			?>
			<tr>
				<td colspan='2'></td>
				<td><b>Jumlah Benang</b></td>
				<td align='right'><?= floatval($valx['jumlah'])?></td>
				<td colspan='3'><b>Actual Jumlah Benang</b></td>
				<td></td>
			</tr>
			<tr>
				<td colspan='2'></td>
				<td><b>Bandwidch</b></td>
				<td align='right'><?= floatval($valx['bw'])?></td>
				<td colspan='3'><b>Actual Bandwidch</b></td>
				<td></td>
			</tr>
			<?php
			}
		}


		?>

		<?php
		$tDetailLiner	= "SELECT * FROM ".$HelpDet_BCL." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='Inside Lamination' ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		$numRows		= mysqli_num_rows($dDetailLiner);
    //echo $tDetailLiner;
    //exit;
		if($numRows > 0){
				?>

						<tr>
							<th align='left' colspan='6'>INSIDE LAMINATION</th>
						</tr>
            <tr>
              <th >Lapisan ke-</th>
							<th >Glass Configuration</th>
							<th >Width</th>
							<th >Stage</th>
							<th >Actual Glass Configuration</th>
							<th >Actual Width</th>
    				</tr>


				<?php


			while($valx = mysqli_fetch_array($dDetailLiner)){
				$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
				?>
				<tr>
          <td class="text-left">
            <?= $valx['lapisan'];?>
          </td>
          <td class="text-left"><?= $valx['glass'];?></td>
          <td align='right'><?= floatval($valx['width']);?></td>
          <?php if ($stage != $valx['stage']):
            $stage = $valx['stage'];
          ?>
            <td style="text-align:center;vertical-align:middle" rowspan="10"><?= $stage;?></td>
          <?php endif; ?>
          <td class="text-right"></td>
          <td class="text-right"></td>


				</tr>
				<?php
			}

		}
    $tDetailoutside	= "SELECT * FROM ".$HelpDet_BCL." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='Outside Lamination' AND width > 0";
		// echo $tDetailoutside;
		$dDetailoutside	= mysqli_query($conn, $tDetailoutside);
		$numRows		= mysqli_num_rows($dDetailoutside);
    //echo $tDetailoutside;
    //exit;
		if($numRows > 0){
				?>
					<thead align='center'>
						<tr>
							<th align='left' colspan='6'>OUTSIDE LAMINATION</th>
						</tr>
            <tr>
              <th >Lapisan ke-</th>
							<th >Glass Configuration</th>
							<th >Width</th>
							<th >Stage</th>
							<th >Actual Glass Configuration</th>
							<th >Actual Width</th>
    				</tr>
					</thead>
					<tbody>
				<?php
        $stage = '';

			while($valx = mysqli_fetch_array($dDetailoutside)){
				?>
				<tr>
          <td class="text-left">
            <?= $valx['lapisan'];?>
          </td>
          <td class="text-left"><?= $valx['glass'];?></td>
          <td align='right'><?= floatval($valx['width']);?></td>
          <?php if ($stage != $valx['stage']):
            $stage = $valx['stage'];
            $n_s	= "SELECT * FROM ".$HelpDet_BCL." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='Outside Lamination' AND stage = '$stage'";
        		// echo $tDetailLiner;
        		$n_s_	= mysqli_query($conn, $n_s);
        		$numRowss		= mysqli_num_rows($n_s_);
          ?>
            <td style="text-align:center;vertical-align:middle" rowspan="<?=$numRowss?>"><?= $stage;?></td>
          <?php endif; ?>
          <td class="text-right"></td>
          <td class="text-right"></td>


				</tr>
				<?php
			}

		}
		?>
		</tbody>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='6'>THICKNESS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Thickness Est</b></td>
				<td align='center'><b><?= floatval($dHeader['est']);?></b></td>
				<td><b>Thickness Act (Web)</b></td>
				<td></td>
				<td><b>Thickness Act (Dry)</b></td>
				<td width='80px'></td>
			</tr>
			<tr>
				<td><b>Status : Reject / Pass</b></td>
				<td colspan='2'><b>Inspector :</b></td>
				<td width='100px'><b>Ttd : </b></td>
				<td colspan='2'><b>Tgl Isnpeksi : </b></td>
			</tr>
			<tr>
				<td height='60px' colspan='6' style='vertical-align: top;'><b>Note :</b></td>
			</tr>
		</tbody>
	</table>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='9'>MACHINE SETUP</th>
			</tr>
			<tr>
				<th><b>#</b></th>
				<th><b>Standard</b></th>
				<th><b>Actual</b></th>
				<th><b>#</b></th>
				<th><b>Standard</b></th>
				<th><b>Actual</b></th>
				<th><b>#</b></th>
				<th><b>Standard</b></th>
				<th><b>Actual</b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align='center'>RPM</td>
				<td></td>
				<td></td>
				<td align='center'>TENTION</td>
				<td></td>
				<td></td>
				<td align='center'>SUDUT ROOVING</td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<div id='space'></div>
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
	<div id='space'></div>
	<pagebreak />
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader2['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>DAILY PRODUCTION REPORT</h2></b></td>
			<td>Rev</td>
			<td></td>
		</tr>
		<tr>
			<td>Due Date</td>
			<td></td>
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
			<td width='20%'>Tgl. Produksi</td>
			<td width='1%'>:</td>
			<td width='29%'></td>
			<td width='20%'>No. SO</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $dHeader2['so_number']; ?></td>
		</tr>
		<tr>
			<td>No. SPK</td>
			<td>:</td>
			<td><?= $dHeader['no_spk'];?></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dRIPP['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>No. Mesin</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= $dim;?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dRIPP['project']); ?></td>
			<td style='vertical-align:top;'><?= ucwords($dHeader['parent_product']);?> To</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= str_replace('-','-',$product_to)." (".strtoupper(strtolower($dHeader['no_komponen'])).") of ".$dHeader['qty']." Component";?></td>
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
		<tr>
			<th align='left' colspan='6'>RESIN & ADD</th>
		</tr>
     	<tr>
			<th width='15%'>Material</th>
			<th>Material Type</th>
			<th width='10%'>Persentase</th>
			<th width='10%'>Weight</th>
			<th width='10%'>Real Persentase</th>
			<th width='10%'>Used</th>
		</tr>

		<?php
		$tDetailLiner	= "SELECT *  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$SUn	= "";
			if($valx['id_category'] == 'TYP-0005'){
				$SUn	= " | ".floatval($valx['jumlah']);
			}
			?>
			<tr>
				<td><?= $valx['nm_category'];?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= number_format($valx['percentage']);?> %</td>
				<td align='right'><?= number_format($valx['material_weight'] * $qty,3);?> Kg</td>
				<td></td>
				<td></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<tr>
			<th align='left' colspan='7'>NOTE</th>
		</tr>
		<tr>
			<td height='50px' colspan='7'></td>
		</tr>
	</table>
  	<style type="text/css">
  	@page {
  		margin-top: 1cm;
  		margin-left: 1.5cm;
  		margin-right: 1cm;
  		margin-bottom: 1cm;
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
  		padding: 6px;
  		border-style: solid;
  		border-color: #666666;
  		background-color: #f2f2f2;
  	}
  	table.gridtable th.head {
  		border-width: 1px;
  		padding: 6px;
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

  	table.gridtable3 {
  		font-family: verdana,arial,sans-serif;
  		font-size:9px;
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

  	table.cooltabs {
  		font-size:12px;
  		font-family: verdana,arial,sans-serif;
  	}

  	table.cooltabs th.reg {
  		font-family: verdana,arial,sans-serif;
  		border-radius: 5px 5px 5px 5px;
  		background: #e3e0e4;
  		padding: 5px;
  	}
  	table.cooltabs td.reg {
  		font-family: verdana,arial,sans-serif;
  		border-radius: 5px 5px 5px 5px;
  		padding: 5px;
  	}
  	#cooltabs {
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 5px 5px 5px 5px;
  		background: #e3e0e4;
  		padding: 5px;
  		width: 800px;
  		height: 20px;
  	}
  	#cooltabs2{
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 5px 5px 5px 5px;
  		background: #e3e0e4;
  		padding: 5px;
  		width: 180px;
  		height: 10px;
  	}
  	#space{
  		padding: 3px;
  		width: 180px;
  		height: 1px;
  	}
  	#cooltabshead{
  		font-size:12px;
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 5px 5px 0 0;
  		background: #dfdfdf;
  		padding: 5px;
  		width: 162px;
  		height: 10px;
  		float:left;
  	}
  	#cooltabschild{
  		font-size:10px;
  		font-family: verdana,arial,sans-serif;
  		border-width: 1px;
  		border-style: solid;
  		border-radius: 0 0 5px 5px;
  		padding: 5px;
  		width: 162px;
  		height: 10px;
  		float:left;
  	}
  	p {
  		margin: 0 0 0 0;
  	}
  	p.pos_fixed {
  		font-family: verdana,arial,sans-serif;
  		position: fixed;
  		top: 30px;
  		left: 230px;
  	}
  	p.pos_fixed2 {
  		font-family: verdana,arial,sans-serif;
  		position: fixed;
  		top: 589px;
  		left: 230px;
  	}
  	.barcode {
  		padding: 1.5mm;
  		margin: 0;
  		vertical-align: top;
  		color: #000044;
  	}
  	.barcodecell {
  		text-align: center;
  		vertical-align: middle;
  		position: fixed;
  		top: -10px;
  		right: 10px;
  	}
  	.barcodecell2 {
  		text-align: center;
  		vertical-align: middle;
  		position: fixed;
  		top: 548px;
  		right: 10px;
  	}
  	p.barcs {
  		font-family: verdana,arial,sans-serif;
  		font-size:11px;
  		position: fixed;
  		top: 38px;
  		right: 115px;
  	}
  	p.barcs2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:11px;
  		position: fixed;
  		top: 591px;
  		right: 115px;
  	}
  	p.pt {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 62px;
  		left: 5px;
  	}
  	p.alamat {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 71px;
  		left: 5px;
  	}
  	p.tlp {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 80px;
  		left: 5px;
  	}
  	p.pt2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 596px;
  		left: 5px;
  	}
  	p.alamat2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 605px;
  		left: 5px;
  	}
  	p.tlp2 {
  		font-family: verdana,arial,sans-serif;
  		font-size:7px;
  		position: fixed;
  		top: 614px;
  		left: 5px;
  	}
  	#hrnew {
  		border: 0;
  		border-bottom: 1px dashed #ccc;
  		background: #999;
  	}
  </style>


	<?php

	$html = ob_get_contents();
	// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_product." / ".$dRIPP['no_ipp']."</i></p>";
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('SPK Of Production');
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output($kode_produksi.'_'.strtolower($dHeader['nm_product']).'_product_ke_'.$product_to.'.pdf' ,'I');

	//exit;
	//return $attachment;
}

?>
