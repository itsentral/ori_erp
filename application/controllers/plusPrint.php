<?php
set_time_limit(0);
ob_start();

$Successno			=0;
$ErrorInfo			=0;
$sroot 				= $_SERVER['DOCUMENT_ROOT'];

function PrintSPKRealOri($Nama_APP, $kode_produksi, $koneksi, $printby, $kode_product, $product_to, $id_production_detail, $id_delivery, $id_milik){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot. "/application/libraries/PHPMailer/PHPMailerAutoload.php";
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
	$qHeader2	= "SELECT
						a.*,
						b.id_category
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

	$qHeader	= "SELECT a.*, b.* FROM bq_component_header a INNER JOIN bq_detail_header b ON a.id_milik=b.id
					WHERE a.id_product='".$kode_product."' AND a.id_milik='".$id_milik."' ";
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
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader2['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>PRODUCTION REAL REPORT</h2></b></td>
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
			<td><?= $dim;?></td>
		</tr>
		<tr>
			<td>Project</td>
			<td>:</td>
			<td><?= strtoupper($dRIPP['project']); ?></td>
			<td><?= ucwords($dHeader['parent_product']);?> To</td>
			<td>:</td>
			<td><?= $product_to." (".strtoupper(strtolower($dHeader['no_komponen'])).") of ".$dHeader['qty']." Component";?></td>
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
				<th width='7%'>Layer</th>
				<th width='27%'>Tipe Material</th>
				<th width='10%'>Qty</th>
				<th width='15%'>Lot/Batch Num</th>
				<th width='10%'>Actual</th>
				<th width='8%'>Layer</th>
				<th width='8%'>Terpakai</th>
			</tr>
			<tr>
				<th align='left' colspan='8'>LINER THIKNESS / CB</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tDetailLiner	= "	SELECT
								a.nm_category,
								a.layer,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.layer as layer_real,
								b.material_terpakai
							FROM
								bq_component_detail a
								INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='LINER THIKNESS / CB'
								AND a.id_material <> 'MTL-1903000'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_category <> 'TYP-0001' ";
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		// echo $tDetailLiner; exit;
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$dataL	= ($valx['layer'] == 0.00)?'-':($valx['layer'] == 0)?'-':floatval($valx['layer']);
			?>
			<tr>
				<td><?= $valx['nm_category'];?></td>
				<td align='center'><?= $dataL;?></td>
				<td><?= $valx['nm_material'];?></td>
				<td align='right'><?= number_format($valx['last_cost'], 3);?> Kg</td>
				<td><?= $valx['batch_number'];?></td>
				<td><?= $valx['actual_type'];?></td>
				<td align='center'><?= $valx['layer_real'];?></td>
				<td align='right'><?= number_format($valx['material_terpakai'], 3);?> Kg</td>
			</tr>
			<?php
		}

		$detailResin	= "
							SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.layer as layer_real,
								b.material_terpakai
							FROM
								bq_component_detail a
								INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.detail_name='LINER THIKNESS / CB'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_category ='TYP-0001'
							ORDER BY
								a.id_detail DESC LIMIT 1 ";
		$dDetailResin	= mysqli_query($conn, $detailResin);
		// echo $detailResin;
		while($valH = mysqli_fetch_array($dDetailResin)){
			?>
			<tr>
				<td colspan='2'><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'], 3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td colspan='2'><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'], 3);?> Kg</td>
			</tr>
			<?php
		}

		$detailPlus	= "
						SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_plus a
							INNER JOIN production_real_detail_plus b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND a.detail_name='LINER THIKNESS / CB'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.id_material <> 'MTL-1903000' ";
		$dDetailPlus	= mysqli_query($conn, $detailPlus);
		// echo $detailPlus;
		while($valH = mysqli_fetch_array($dDetailPlus)){
			?>
			<tr>
				<td colspan='2'><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'], 3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td colspan='2'><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'], 3);?> Kg</td>
			</tr>
			<?php
		}

		$detailAdd	= "	SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_add a
							INNER JOIN production_real_detail_add b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.detail_name='LINER THIKNESS / CB' ";
		$dDetailAdd	= mysqli_query($conn, $detailAdd);
		$NUmRow		= mysqli_num_rows($dDetailAdd);
		// echo $NUmRow;
		if($NUmRow > 0){
			echo "<tr>";
				echo "<th align='left' colspan='8'>Add Materials</th>";
			echo "</tr>";
			while($valD = mysqli_fetch_array($dDetailAdd)){
			?>
			<tr>
				<td colspan='2'><?= $valD['nm_category'];?></td>
				<td><?= $valD['nm_material'];?></td>
				<td align='right'><?= number_format($valD['last_cost'], 3);?> Kg</td>
				<td><?= $valD['batch_number'];?></td>
				<td colspan='2'><?= $valD['actual_type'];?></td>
				<td align='right'><?= number_format($valD['material_terpakai'], 3);?> Kg</td>
			</tr>
			<?php
			}
		}
		?>

		</tbody>
		</table>
		<!-- STRUCTURE NECK ================================================================================ -->

		<?php
		$tDetailLinerN1	= "
								SELECT
									a.id_category,
									a.nm_category,
									a.layer,
									a.nm_material,
									a.last_cost,
									a.jumlah,
									b.batch_number,
									b.actual_type,
									b.layer as layer_real,
									b.material_terpakai,
									b.benang
								FROM
									bq_component_detail a
									INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
								WHERE
									a.id_product='".$kode_product."'
									AND a.id_milik = '".$id_milik."'
									AND a.detail_name='STRUKTUR NECK 1'
									AND b.id_production_detail = '".$id_production_detail."'
									AND a.id_material <> 'MTL-1903000'
									AND a.id_category <>'TYP-0001' ";
		$dDetailLinerN1	= mysqli_query($conn, $tDetailLinerN1);
		$dDetailLinerN1Num	= mysqli_num_rows($dDetailLinerN1);
		if($dDetailLinerN1Num > 0){
		?>
			<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='8'>STRUKTUR NECK 1</th>
				</tr>
			</thead>
			<tbody>
			<?php
			// echo $tDetailLiner; exit;

			while($valx = mysqli_fetch_array($dDetailLinerN1)){
				$dataL	= ($valx['layer'] == 0.00)?'-':floatval($valx['layer']);
				$benang = "";
				$benangR = "";
					if($valx['id_category'] == 'TYP-0005'){
						$benang = " | ".floatval($valx['jumlah']);
						$benangR = " | ".floatval($valx['benang']);
					}
				?>
				<tr>
					<td width='13%'><?= $valx['nm_category'];?></td>
					<td width='7%' align='center'><?= $dataL.$benang;?></td>
					<td width='27%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'], 3);?> Kg</td>
					<td width='15%'><?= $valx['batch_number'];?></td>
					<td width='10%'><?= $valx['actual_type'];?></td>
					<td width='8%' align='center'><?= $valx['layer_real'].$benang;?></td>
					<td width='8%' align='right'><?= number_format($valx['material_terpakai'], 3);?> Kg</td>
				</tr>
				<?php
			}

			$detailResinN1	= "	SELECT
									a.nm_category,
									a.nm_material,
									a.last_cost,
									b.batch_number,
									b.actual_type,
									b.layer as layer_real,
									b.material_terpakai
								FROM
									bq_component_detail a
									INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
								WHERE
									a.id_product='".$kode_product."'
									AND a.id_milik = '".$id_milik."'
									AND a.detail_name='STRUKTUR NECK 1'
									AND b.id_production_detail = '".$id_production_detail."'
									AND a.id_category ='TYP-0001'
								ORDER BY
									a.id_detail DESC LIMIT 1 ";
			$dDetailResinN1	= mysqli_query($conn, $detailResinN1);
			// echo $detailResin;
			while($valH = mysqli_fetch_array($dDetailResinN1)){
				?>
				<tr>
					<td colspan='2'><?= $valH['nm_category'];?></td>
					<td><?= $valH['nm_material'];?></td>
					<td align='right'><?= number_format($valH['last_cost'], 3);?> Kg</td>
					<td><?= $valH['batch_number'];?></td>
					<td colspan='2'><?= $valH['actual_type'];?></td>
					<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
				</tr>
				<?php
			}

			$detailPlusN1	= "SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.material_terpakai
							FROM
								bq_component_detail_plus a
								INNER JOIN production_real_detail_plus b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='STRUKTUR NECK 1'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_material <> 'MTL-1903000' ";
			$dDetailPlusN1	= mysqli_query($conn, $detailPlusN1);
			// echo $detailPlus;
			while($valH = mysqli_fetch_array($dDetailPlusN1)){
				?>
				<tr>
					<td colspan='2'><?= $valH['nm_category'];?></td>
					<td><?= $valH['nm_material'];?></td>
					<td align='right'><?= number_format($valH['last_cost'],3);?> Kg</td>
					<td><?= $valH['batch_number'];?></td>
					<td colspan='2'><?= $valH['actual_type'];?></td>
					<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
				</tr>
				<?php
			}

			$detailAddN1	= "SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.material_terpakai
							FROM
								bq_component_detail_add a
								INNER JOIN production_real_detail_add b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.detail_name='STRUKTUR NECK 1' ";
			$dDetailAddN1	= mysqli_query($conn, $detailAddN1);
			$NUmRowN1		= mysqli_num_rows($dDetailAddN1);
			// echo $NUmRow;
			if($NUmRowN1 > 0){
				echo "<tr>";
					echo "<th align='left' colspan='8'>Add Materials</th>";
				echo "</tr>";

				while($valD = mysqli_fetch_array($dDetailAddN1)){
				?>
				<tr>
					<td colspan='2'><?= $valD['nm_category'];?></td>
					<td><?= $valD['nm_material'];?></td>
					<td align='right'><?= number_format($valD['last_cost'],3);?> Kg</td>
					<td><?= $valD['batch_number'];?></td>
					<td colspan='2'><?= $valD['actual_type'];?></td>
					<td align='right'><?= number_format($valD['material_terpakai'],3);?> Kg</td>
				</tr>
				<?php
				}
			}
			?>
			</tbody>
			</table>



			<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='8'>STRUKTUR NECK 2</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$tDetailLinerN2	= "
								SELECT
									a.id_category,
									a.nm_category,
									a.layer,
									a.nm_material,
									a.last_cost,
									a.jumlah,
									b.batch_number,
									b.actual_type,
									b.layer as layer_real,
									b.material_terpakai,
									b.benang
								FROM
									bq_component_detail a
									INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
								WHERE
									a.id_product='".$kode_product."'
									AND a.id_milik = '".$id_milik."'
									AND a.detail_name='STRUKTUR NECK 2'
									AND b.id_production_detail = '".$id_production_detail."'
									AND a.id_material <> 'MTL-1903000'
									AND a.id_category <>'TYP-0001' ";
			$dDetailLinerN2	= mysqli_query($conn, $tDetailLinerN2);

			while($valx = mysqli_fetch_array($dDetailLinerN2)){
				$dataL	= ($valx['layer'] == 0.00)?'-':floatval($valx['layer']);
				$benang = "";
				$benangR = "";
					if($valx['id_category'] == 'TYP-0005'){
						$benang = " | ".floatval($valx['jumlah']);
						$benangR = " | ".floatval($valx['benang']);
					}
				?>
				<tr>
					<td width='13%'><?= $valx['nm_category'];?></td>
					<td width='7%' align='center'><?= $dataL.$benang;?></td>
					<td width='27%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'], 3);?> Kg</td>
					<td width='15%'><?= $valx['batch_number'];?></td>
					<td width='10%'><?= $valx['actual_type'];?></td>
					<td width='8%' align='center'><?= $valx['layer_real'].$benang;?></td>
					<td width='8%' align='right'><?= number_format($valx['material_terpakai'], 3);?> Kg</td>
				</tr>
				<?php
			}

			$detailResinN2	= "	SELECT
									a.nm_category,
									a.nm_material,
									a.last_cost,
									b.batch_number,
									b.actual_type,
									b.layer as layer_real,
									b.material_terpakai
								FROM
									bq_component_detail a
									INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
								WHERE
									a.id_product='".$kode_product."'
									AND a.id_milik = '".$id_milik."'
									AND a.detail_name='STRUKTUR NECK 2'
									AND b.id_production_detail = '".$id_production_detail."'
									AND a.id_category ='TYP-0001'
								ORDER BY
									a.id_detail DESC LIMIT 1 ";
			$dDetailResinN2	= mysqli_query($conn, $detailResinN2);
			// echo $detailResin;
			while($valH = mysqli_fetch_array($dDetailResinN2)){
				?>
				<tr>
					<td colspan='2'><?= $valH['nm_category'];?></td>
					<td><?= $valH['nm_material'];?></td>
					<td align='right'><?= number_format($valH['last_cost'], 3);?> Kg</td>
					<td><?= $valH['batch_number'];?></td>
					<td colspan='2'><?= $valH['actual_type'];?></td>
					<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
				</tr>
				<?php
			}

			$detailPlusN2	= "SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.material_terpakai
							FROM
								bq_component_detail_plus a
								INNER JOIN production_real_detail_plus b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='STRUKTUR NECK 2'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_material <> 'MTL-1903000' ";
			$dDetailPlusN2	= mysqli_query($conn, $detailPlusN2);
			// echo $detailPlus;
			while($valH = mysqli_fetch_array($dDetailPlusN2)){
				?>
				<tr>
					<td colspan='2'><?= $valH['nm_category'];?></td>
					<td><?= $valH['nm_material'];?></td>
					<td align='right'><?= number_format($valH['last_cost'],3);?> Kg</td>
					<td><?= $valH['batch_number'];?></td>
					<td colspan='2'><?= $valH['actual_type'];?></td>
					<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
				</tr>
				<?php
			}

			$detailAddN2	= "SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.material_terpakai
							FROM
								bq_component_detail_add a
								INNER JOIN production_real_detail_add b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.detail_name='STRUKTUR NECK 2' ";
			$dDetailAddN2	= mysqli_query($conn, $detailAddN2);
			$NUmRowN2		= mysqli_num_rows($dDetailAddN2);
			// echo $NUmRow;
			if($NUmRowN2 > 0){
				echo "<tr>";
					echo "<th align='left' colspan='8'>Add Materials</th>";
				echo "</tr>";

				while($valD = mysqli_fetch_array($dDetailAddN2)){
				?>
				<tr>
					<td colspan='2'><?= $valD['nm_category'];?></td>
					<td><?= $valD['nm_material'];?></td>
					<td align='right'><?= number_format($valD['last_cost'],3);?> Kg</td>
					<td><?= $valD['batch_number'];?></td>
					<td colspan='2'><?= $valD['actual_type'];?></td>
					<td align='right'><?= number_format($valD['material_terpakai'],3);?> Kg</td>
				</tr>
				<?php
				}
			}
			?>
			</tbody>
			</table>
		<?php
		}
		?>

		<!-- ===============================================================================================-->
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='8'>STRUKTUR THICKNESS</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$tDetailLiner	= "
							SELECT
								a.id_category,
								a.nm_category,
								a.layer,
								a.nm_material,
								a.last_cost,
								a.jumlah,
								b.batch_number,
								b.actual_type,
								b.layer as layer_real,
								b.material_terpakai,
								b.benang
							FROM
								bq_component_detail a
								INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='STRUKTUR THICKNESS'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_material <> 'MTL-1903000'
								AND a.id_category <>'TYP-0001' ";
		// echo $tDetailLiner; exit;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		while($valx = mysqli_fetch_array($dDetailLiner)){
			$dataL	= ($valx['layer'] == 0.00)?'-':floatval($valx['layer']);
			$benang = "";
			$benangR = "";
				if($valx['id_category'] == 'TYP-0005'){
					$benang = " | ".floatval($valx['jumlah']);
					$benangR = " | ".floatval($valx['benang']);
				}
			?>
			<tr>
				<td width='13%'><?= $valx['nm_category'];?></td>
				<td width='7%' align='center'><?= $dataL.$benang;?></td>
				<td width='27%'><?= $valx['nm_material'];?></td>
				<td width='10%' align='right'><?= number_format($valx['last_cost'], 3);?> Kg</td>
				<td width='15%'><?= $valx['batch_number'];?></td>
				<td width='10%'><?= $valx['actual_type'];?></td>
				<td width='8%' align='center'><?= $valx['layer_real'].$benang;?></td>
				<td width='8%' align='right'><?= number_format($valx['material_terpakai'], 3);?> Kg</td>
			</tr>
			<?php
		}

		$detailResin	= "	SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.layer as layer_real,
								b.material_terpakai
							FROM
								bq_component_detail a
								INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='STRUKTUR THICKNESS'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_category ='TYP-0001'
							ORDER BY
								a.id_detail DESC LIMIT 1 ";
		$dDetailResin	= mysqli_query($conn, $detailResin);
		// echo $detailResin;
		while($valH = mysqli_fetch_array($dDetailResin)){
			?>
			<tr>
				<td colspan='2'><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'], 3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td colspan='2'><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
		}

		$detailPlus	= "SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_plus a
							INNER JOIN production_real_detail_plus b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND a.detail_name='STRUKTUR THICKNESS'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.id_material <> 'MTL-1903000' ";
		$dDetailPlus	= mysqli_query($conn, $detailPlus);
		// echo $detailPlus;
		while($valH = mysqli_fetch_array($dDetailPlus)){
			?>
			<tr>
				<td colspan='2'><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'],3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td colspan='2'><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
		}

		$detailAdd	= "SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_add a
							INNER JOIN production_real_detail_add b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.detail_name='STRUKTUR THICKNESS' ";
		$dDetailAdd	= mysqli_query($conn, $detailAdd);
		$NUmRow		= mysqli_num_rows($dDetailAdd);
		// echo $NUmRow;
		if($NUmRow > 0){
			echo "<tr>";
				echo "<th align='left' colspan='8'>Add Materials</th>";
			echo "</tr>";

			while($valD = mysqli_fetch_array($dDetailAdd)){
			?>
			<tr>
				<td colspan='2'><?= $valD['nm_category'];?></td>
				<td><?= $valD['nm_material'];?></td>
				<td align='right'><?= number_format($valD['last_cost'],3);?> Kg</td>
				<td><?= $valD['batch_number'];?></td>
				<td colspan='2'><?= $valD['actual_type'];?></td>
				<td align='right'><?= number_format($valD['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
			}
		}
		?>
		</tbody>
		</table>
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<?php
		$tDetailLiner	= "SELECT
								a.nm_category,
								a.layer,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.layer as layer_real,
								b.material_terpakai
							FROM
								bq_component_detail a
								INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='EXTERNAL LAYER THICKNESS'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_material <> 'MTL-1903000'
								AND a.id_category <>'TYP-0001' ";
		// echo $tDetailLiner;
		$dDetailLiner	= mysqli_query($conn, $tDetailLiner);
		$numRows		= mysqli_num_rows($dDetailLiner);

		if($numRows > 0){
			?>

				<thead align='center'>
					<tr>
						<th align='left' colspan='8'>EXTERNAL LAYER THICKNESS</th>
					</tr>
				</thead>
				<tbody>
			<?php
		}

		while($valx = mysqli_fetch_array($dDetailLiner)){
			$dataL	= ($valx['layer'] == 0)?'-':floatval($valx['layer']);
			?>
			<tr>
				<td width='13%'><?= $valx['nm_category'];?></td>
				<td width='7%' align='center'><?= $dataL;?></td>
				<td width='27%'><?= $valx['nm_material'];?></td>
				<td width='10%' align='right'><?= number_format($valx['last_cost'],3);?> Kg</td>
				<td width='15%'><?= $valx['batch_number'];?></td>
				<td width='10%'><?= $valx['actual_type'];?></td>
				<td width='8%' align='center'><?= $valx['layer_real'];?></td>
				<td width='8%' align='right'><?= number_format($valx['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
		}

		$detailResin	= "SELECT
								a.nm_category,
								a.nm_material,
								a.last_cost,
								b.batch_number,
								b.actual_type,
								b.layer as layer_real,
								b.material_terpakai
							FROM
								bq_component_detail a
								INNER JOIN production_real_detail b ON a.id_detail = b.id_detail
							WHERE
								a.id_product='".$kode_product."'
								AND a.id_milik = '".$id_milik."'
								AND a.detail_name='EXTERNAL LAYER THICKNESS'
								AND b.id_production_detail = '".$id_production_detail."'
								AND a.id_category ='TYP-0001'
							ORDER BY a.id_detail DESC LIMIT 1 ";
		$dDetailResin	= mysqli_query($conn, $detailResin);
		// echo $detailResin;
		while($valH = mysqli_fetch_array($dDetailResin)){
			?>
			<tr>
				<td colspan='2'><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'],3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td colspan='2'><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
		}

		$detailPlus	= "SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_plus a
							INNER JOIN production_real_detail_plus b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND a.detail_name='EXTERNAL LAYER THICKNESS'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.id_material <> 'MTL-1903000' ";
		$dDetailPlus	= mysqli_query($conn, $detailPlus);
		// echo $detailPlus;
		while($valH = mysqli_fetch_array($dDetailPlus)){
			?>
			<tr>
				<td colspan='2'><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'],3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td colspan='2'><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
		}

		$detailAdd	= "SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_add a
							INNER JOIN production_real_detail_add b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.detail_name='EXTERNAL LAYER THICKNESS' ";
		$dDetailAdd	= mysqli_query($conn, $detailAdd);
		$NUmRow		= mysqli_num_rows($dDetailAdd);
		// echo $NUmRow;
		if($NUmRow > 0){
			echo "<tr>";
				echo "<th align='left' colspan='8'>Add Materials</th>";
			echo "</tr>";

			while($valD = mysqli_fetch_array($dDetailAdd)){
			?>
			<tr>
				<td colspan='2'><?= $valD['nm_category'];?></td>
				<td><?= $valD['nm_material'];?></td>
				<td align='right'><?= number_format($valD['last_cost'],3);?> Kg</td>
				<td><?= $valD['batch_number'];?></td>
				<td colspan='2'><?= $valD['actual_type'];?></td>
				<td align='right'><?= number_format($valD['material_terpakai'],3);?> Kg</td>
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
				<th align='left' colspan='6'>TOPCOAT</th>
			</tr>
			<tr>
				<th width='20%'>Material</th>
				<th width='27%'>Tipe Material</th>
				<th width='10%'>Qty</th>
				<th width='15%'>Lot/Batch Num</th>
				<th width='18%'>Actual Type</th>
				<th width='8%'>Terpakai</th>
			</tr>
		</thead>
		<tbody>
		<?php

		$detailPlus	= "SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_plus a
							INNER JOIN production_real_detail_plus b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.detail_name='TOPCOAT'
							AND a.id_material <> 'MTL-1903000' GROUP BY a.id_detail";
		$dDetailPlus	= mysqli_query($conn, $detailPlus);
		// echo $detailPlus;
		while($valH = mysqli_fetch_array($dDetailPlus)){
			?>
			<tr>
				<td><?= $valH['nm_category'];?></td>
				<td><?= $valH['nm_material'];?></td>
				<td align='right'><?= number_format($valH['last_cost'],3);?> Kg</td>
				<td><?= $valH['batch_number'];?></td>
				<td><?= $valH['actual_type'];?></td>
				<td align='right'><?= number_format($valH['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
		}

		$detailAdd	= "SELECT
							a.nm_category,
							a.nm_material,
							a.last_cost,
							b.batch_number,
							b.actual_type,
							b.material_terpakai
						FROM
							bq_component_detail_add a
							INNER JOIN production_real_detail_add b ON a.id_detail = b.id_detail
						WHERE
							a.id_product='".$kode_product."'
							AND a.id_milik = '".$id_milik."'
							AND b.id_production_detail = '".$id_production_detail."'
							AND a.detail_name='TOPCOAT' ";
		$dDetailAdd	= mysqli_query($conn, $detailAdd);
		$NUmRow		= mysqli_num_rows($dDetailAdd);
		// echo $NUmRow;
		if($NUmRow > 0){
			echo "<tr>";
				echo "<th align='left' colspan='6'>Add Materials</th>";
			echo "</tr>";

			while($valD = mysqli_fetch_array($dDetailAdd)){
			?>
			<tr>
				<td><?= $valD['nm_category'];?></td>
				<td><?= $valD['nm_material'];?></td>
				<td align='right'><?= number_format($valD['last_cost'],3);?> Kg</td>
				<td><?= $valD['batch_number'];?></td>
				<td><?= $valD['actual_type'];?></td>
				<td align='right'><?= number_format($valD['material_terpakai'],3);?> Kg</td>
			</tr>
			<?php
			}
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
				<td height='75px' colspan='7'></td>
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
	// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	// $footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_produksi." / ".$kode_product." / ".$dRIPP['no_ipp']." / <b>First</b></i></p>";
	$footer = "<p class='foot1'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_produksi." / ".$kode_product." / ".$id_production_detail."</i></p>";

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

function PrintIPP($Nama_APP, $no_ipp, $koneksi, $printby){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot."/application/libraries/MPDF57/mpdf.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');

	$qHeader	= "SELECT a.* FROM production a WHERE a.no_ipp='".$no_ipp."' ";
	$dResult	= mysqli_query($conn, $qHeader);
	$dHeader	= mysqli_fetch_array($dResult);

	// $qHeaderD	= "SELECT a.* FROM production_req_customer a WHERE a.no_ipp='".$no_ipp."' ";
	// $dResultD	= mysqli_query($conn, $qHeaderD);
	// $dHeaderD	= mysqli_fetch_array($dResultD);

	$qFluida	= "SELECT a.* FROM list_fluida a WHERE a.id_fluida='".$dHeaderD['id_fluida']."' ";
	$dRFluida	= mysqli_query($conn, $qFluida);
	$dFluidaD	= mysqli_fetch_array($dRFluida);

	$qStand		= "SELECT a.* FROM list_standard a WHERE a.id_standard='".$dHeaderD['standard_spec']."' ";
	$dRStand	= mysqli_query($conn, $qStand);
	$dStand		= mysqli_fetch_array($dRStand);

	$qHeaderShi	= "SELECT a.*, b.country_name FROM production_delivery a INNER JOIN country b ON a.country_code=b.country_code WHERE a.no_ipp='".$no_ipp."' ";
	$dResultShip	= mysqli_query($conn, $qHeaderShi);
	$dHeaderShip	= mysqli_fetch_array($dResultShip);

	$qHeaderDet	= "SELECT a.* FROM production_req_sp a WHERE a.no_ipp='".$no_ipp."' ";
	$dResultDet	= mysqli_query($conn, $qHeaderDet);
	$dResultDet2	= mysqli_query($conn, $qHeaderDet);
	?>

	<table class="gridtable2" border='1' width='100%' cellpadding='2'>
		<tr>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>IDENTIFIKASI PERMINTAAN PELANGGAN</h2></b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable2" border='0' width='100%' >
		<tr>
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $no_ipp; ?></td>
			<td width='20%'>IPP Date</td>
			<td width='1%'>:</td>
			<td width='29%'><?= date('d F Y', strtotime($dHeader['created_date'])); ?></td>
		</tr>
		<tr>
			<td>Customer Name</td>
			<td>:</td>
			<td><?= $dHeader['nm_customer']; ?></td>
			<td>Revision To</td>
			<td>:</td>
			<td><?= $dHeader['ref_ke'];?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= $dHeader['project']; ?></td>
			<td style='vertical-align:top;'>Revision By</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= ($dHeader['ref_ke'] == '0')?ucfirst(strtolower($dHeader['created_by'])):ucfirst(strtolower($dHeader['modified_by']));?></td>
		</tr>
		<tr>
			<td>Max Tolerance</td>
			<td>:</td>
			<td><?= floatval($dHeader['max_tol']); ?></td>
			<td>Min Tolerance</td>
			<td>:</td>
			<td><?= floatval($dHeader['min_tol']); ?></td>
		</tr>
		<tr>
			<td>Validity & Guarantee</td>
			<td>:</td>
			<td><?= (!empty($dHeader['validity']))?strtoupper($dHeader['validity']):'-';?></td>
			<td>Payment Term</td>
			<td>:</td>
			<td><?= (!empty($dHeader['payment']))?ucwords(strtolower($dHeader['payment'])):'-';?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Referensi Customer/Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= (!empty($dHeader['ref_cust']))?strtoupper($dHeader['ref_cust']):'-';?></td>
			<td style='vertical-align:top;'>Special Requirements From Customer</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= (!empty($dHeader['syarat_cust']))?strtoupper($dHeader['syarat_cust']):'-';?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Note</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= (!empty($dHeader['note']))?strtoupper($dHeader['note']):'-';?></td>
			<td style='vertical-align:top;'></td>
			<td style='vertical-align:top;'></td>
			<td style='vertical-align:top;'></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='14'>SPECIFICATION LIST</th>
			</tr>
			<tr>
				<th width='5%'>No</th>
				<th width='7%'>Product</th>
				<th width='7%'>Resin Type</th>
				<th width='7%'>Liner</th>
				<th width='7%'>Preaseure</th>
				<th width='9%'>Stifness</th>
				<th width='10%'>Aplication</th>
				<th width='8%'>Vacum_Rate</th>
				<th width='8%'>Life Time</th>
				<th width='14%'>Reference Standard</th>
				<th width='7%'>Conductive</th>
				<th width='7%'>Fire Retardant</th>
				<th width='7%'>Color</th>
				<th width='7%'>Abrasive</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no=0;
				while($data = mysqli_fetch_array($dResultDet)){
					$no++;
					$std_asme	= ($data['std_asme']=='Y')?'ASME , ':'';
					$std_ansi	= ($data['std_ansi']=='Y')?'ANSI , ':'';
					$std_astm	= ($data['std_astm']=='Y')?'ASTM , ':'';
					$std_awwa	= ($data['std_awwa']=='Y')?'AWWA , ':'';
					$std_bsi	= ($data['std_bsi']=='Y')?'BSI , ':'';
					$std_jis	= ($data['std_jis']=='Y')?'JIS , ':'';
					$std_sni	= ($data['std_sni']=='Y')?'SNI , ':'';
					$etc_1		= ($data['std_etc']=='Y' AND $data['etc_1'] != '')?$data['etc_1']."/":'';
					$etc_2		= ($data['std_etc']=='Y' AND $data['etc_2'] != '')?$data['etc_2']."/":'';
					$etc_3		= ($data['std_etc']=='Y' AND $data['etc_3'] != '')?$data['etc_3']."/":'';
					$etc_4		= ($data['std_etc']=='Y' AND $data['etc_4'] != '')?$data['etc_4']."/":'';

					?>
					<tr>
						<td align='center'><?= $no;?></td>
						<td align='center'><?= $data['product'];?></td>
						<td align='center'><?= $data['type_resin'];?></td>
						<td align='center'><?= $data['liner_thick'];?></td>
						<td align='center'><?= $data['pressure'];?> Bar</td>
						<td align='center'><?= $data['stifness'];?> Pa</td>
						<td align='center'><?= $data['aplikasi'];?></td>
						<td align='center'><?= $data['vacum_rate'];?></td>
						<td align='center'><?= $data['time_life'];?> Year</td>
						<td align='center'><?= $std_asme.$std_ansi.$std_astm.$std_awwa.$std_bsi.$std_jis.$std_sni.$etc_1.$etc_2.$etc_3.$etc_4; ?></td>

						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Liner</td>
									<td align='left' width='10%'>:</td>
									<td align='left' width='60%'><?= ($data['konduksi_liner'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Str</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['konduksi_structure'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Eks</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['konduksi_eksternal'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Tc</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['konduksi_topcoat'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Liner</td>
									<td align='left' width='10%'>:</td>
									<td align='left' width='60%'><?= ($data['tahan_api_liner'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Str</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['tahan_api_structure'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Eks</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['tahan_api_eksternal'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Tc</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['tahan_api_topcoat'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Liner</td>
									<td align='left' width='10%'>:</td>
									<td align='left' width='60%'><?= ($data['color'] == 'N')?'-':($data['color_liner'] == '')?'-':strtoupper($data['color_liner']);?></td>
								</tr>
								<tr>
									<td align='left'>Str</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['color'] == 'N')?'-':($data['color_structure'] == '')?'-':strtoupper($data['color_structure']);?></td>
								</tr>
								<tr>
									<td align='left'>Eks</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['color'] == 'N')?'-':($data['color_external'] == '')?'-':strtoupper($data['color_external']);?></td>
								</tr>
								<tr>
									<td align='left'>Tc</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['color'] == 'N')?'-':($data['color_topcoat'] == '')?'-':strtoupper($data['color_topcoat']);?></td>
								</tr>
							</table>
						</td>
						<td align='center'><?= ($data['abrasi'] == 'Y')?'<b>YES</b>':'NO';?></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='7'>SPECIFICATION LIST</th>
			</tr>

			<tr>
				<th width='5%'>No</th>
				<th width='7%'>Product</th>
				<th width='20%'>Document</th>
				<th width='20%'>Certificate</th>
				<th width='20%'>Testing</th>
				<th width='18%'>Add Request</th>
				<th width='10%'>Note</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no2=0;
				while($data2 = mysqli_fetch_array($dResultDet2)){
					$no2++;
				?>
					<tr>
						<td align='center'><?= $no2;?></td>
						<td align='center'><?= $data2['product'];?></td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Document 1</td>
									<td align='left' width='5%'>:</td>
									<td align='left' width='65%'><?= ($data2['document'] == 'N')?'-':($data2['document_1'] == '')?'-':strtoupper($data2['document_1']);?></td>
								</tr>
								<tr>
									<td align='left'>Document 2</td>
									<td align='left'>:</td>
									<td align='left' width='65%'><?= ($data2['document'] == 'N')?'-':($data2['document_2'] == '')?'-':strtoupper($data2['document_2']);?></td>
								</tr>
								<tr>
									<td align='left'>Document 3</td>
									<td align='left'>:</td>
									<td align='left' width='65%'><?= ($data2['document'] == 'N')?'-':($data2['document_3'] == '')?'-':strtoupper($data2['document_3']);?></td>
								</tr>
								<tr>
									<td align='left'>Document 4</td>
									<td align='left'>:</td>
									<td align='left' width='65%'><?= ($data2['document'] == 'N')?'-':($data2['document_4'] == '')?'-':strtoupper($data2['document_4']);?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Certificate 1</td>
									<td align='left' width='5%'>:</td>
									<td align='left' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_1'] == '')?'-':strtoupper($data2['sertifikat_1']);?></td>
								</tr>
								<tr>
									<td align='left'>Certificate 2</td>
									<td align='left'>:</td>
									<td align='left' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_2'] == '')?'-':strtoupper($data2['sertifikat_2']);?></td>
								</tr>
								<tr>
									<td align='left'>Certificate 3</td>
									<td align='left'>:</td>
									<td align='left' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_3'] == '')?'-':strtoupper($data2['sertifikat_3']);?></td>
								</tr>
								<tr>
									<td align='left'>Certificate 4</td>
									<td align='left'>:</td>
									<td align='left' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_4'] == '')?'-':strtoupper($data2['sertifikat_4']);?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='21%'>Testing 1</td>
									<td align='left' width='5%'>:</td>
									<td align='left' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_1'] == '')?'-':strtoupper($data2['test_1']);?></td>
								</tr>
								<tr>
									<td align='left'>Testing 2</td>
									<td align='left'>:</td>
									<td align='left' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_2'] == '')?'-':strtoupper($data2['test_2']);?></td>
								</tr>
								<tr>
									<td align='left'>Testing 3</td>
									<td align='left'>:</td>
									<td align='left' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_3'] == '')?'-':strtoupper($data2['test_3']);?></td>
								</tr>
								<tr>
									<td align='left'>Testing 4</td>
									<td align='left'>:</td>
									<td align='left' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_4'] == '')?'-':strtoupper($data2['test_4']);?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='45%'>Top Coated Color</td>
									<td align='left' width='5%'>:</td>
									<td align='left' width='50%'><?= ($data2['ck_minat_warna_tc'] == 'N')?'-':($data2['ck_minat_warna_tc'] == '')?'-':strtoupper($data2['minat_warna_tc']);?></td>
								</tr>
								<tr>
									<td align='left'>Pigmented Color</td>
									<td align='left'>:</td>
									<td align='left' width='50%'><?= ($data2['ck_minat_warna_pigment'] == 'N')?'-':($data2['ck_minat_warna_pigment'] == '')?'-':strtoupper($data2['minat_warna_pigment']);?></td>
								</tr>
								<tr>
									<td align='left'>Resin Request</td>
									<td align='left'>:</td>
									<td align='left' width='50%'><?= (empty($data2['resin_req_cust']))?'-':$data2['resin_req_cust'];?></td>
								</tr>
								<tr>
									<td align='left'>&nbsp;</td>
									<td align='left'></td>
									<td align='left' width='50%'></td>
								</tr>
							</table>
						</td>
						<td><?= strtoupper($data2['note']);?></td>
					</tr>
				<?php
				}
			?>
		</tbody>
	</table>
	<br>
	<table class="gridtable2" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<td align='left' colspan='6'><b>SHIPPING DETAIL</b></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td  width='20%'>Country</td>
				<td  width='1%'>:</td>
				<td  width='29%'><?= strtoupper($dHeaderShip['country_name']); ?></td>
				<td  width='20%'>Delivery Date</td>
				<td  width='1%'>:</td>
				<td  width='29%'><?= date('d F Y', strtotime($dHeaderShip['date_delivery'])); ?></td>
			</tr>
			<tr>
				<td>Shipping Method</td>
				<td>:</td>
				<td><?= strtoupper($dHeaderShip['metode_delivery']); ?></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td  width='24%'>Address</td>
				<td  width='1%'>:</td>
				<td  width='25%' colspan='4'><?= strtoupper($dHeaderShip['address_delivery']); ?></td>
			</tr>
			<tr>
				<td>Instalation</td>
				<td>:</td>
				<td><?= strtoupper($dHeaderShip['isntalasi_by']); ?></td>
				<td>Handling Equipment</td>
				<td>:</td>
				<td><?= strtoupper($dHeaderShip['alat_berat']); ?></td>
			</tr>

			<tr>
				<td>Truck</td>
				<td>:</td>
				<td><?= ($dHeaderShip['truck'] == null || $dHeaderShip['truck'] == '')?'-':$dHeaderShip['truck']; ?></td>
				<td>Vendor</td>
				<td>:</td>
				<td><?= ($dHeaderShip['vendor'] == null || $dHeaderShip['vendor'] == '')?'-':$dHeaderShip['vendor']; ?></td>
			</tr>

			<tr>
				<td>Qty</td>
				<td>:</td>
				<td><?= ($dHeaderShip['qty'] == null || $dHeaderShip['qty'] == '')?'-':$dHeaderShip['qty']; ?></td>
				<td>Packing</td>
				<td>:</td>
				<td><?= $dHeaderShip['packing']; ?></td>
			</tr>
			<tr>
				<td>Pipe Packing</td>
				<td>:</td>
				<td><?= $dHeaderShip['packing_pipa_qty']; ?></td>
				<td>Fitting Packing</td>
				<td>:</td>
				<td><?= $dHeaderShip['packing_fitting_qty']; ?></td>
			</tr>
			<tr>
				<td>DG Packing</td>
				<td>:</td>
				<td><?= $dHeaderShip['packing_dg_qty']; ?></td>
				<td>Validity & Guarantee</td>
				<td>:</td>
				<td><?= $dHeaderShip['garansi']; ?> Year</td>
			</tr>
		</tbody>
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

		table.gridtable3 {
			font-family: verdana,arial,sans-serif;
			font-size:9px;
			color:#333333;
			border-width: 0px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable3 th {
			border-width: 1px;
			padding: 8px;
			border-style: none;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable3 th.head {
			border-width: 1px;
			padding: 8px;
			border-style: none;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable3 td {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable3 td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: none;
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
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$refX	=  $dHeader['ref_ke'];
	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($no_ipp);
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output($no_ipp.'_revisi_ke_'.$refX.'.pdf' ,'I');
}
?>
