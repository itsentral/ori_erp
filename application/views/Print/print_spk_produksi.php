<?php
	
$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php"; 
$mpdf=new mPDF('utf-8','A4');
// $mpdf=new mPDF('utf-8','A4-L');
set_time_limit(0);
ini_set('memory_limit','1024M');

ob_start();
date_default_timezone_set('Asia/Jakarta');
$today = date('D, d-M-Y H:i:s');

$qHeader2		= "SELECT a.* FROM production_header a WHERE a.id_produksi='".$kode_produksi."' LIMIT 1";
$dHeader2		= $this->db->query($qHeader2)->result_array();

$HelpDet_BDH 	= "bq_detail_header";
$HelpDet_BCH 	= "bq_component_header";
$HelpDet_BCD 	= "bq_component_detail";
$HelpDet_BCDP 	= "bq_component_detail_plus";
$HelpDet_BCDA 	= "bq_component_detail_add";
if($dHeader2[0]['jalur'] == 'FD'){
	$HelpDet_BDH 	= "so_detail_header";
	$HelpDet_BCH 	= "so_component_header";
	$HelpDet_BCD 	= "so_component_detail";
	$HelpDet_BCDP 	= "so_component_detail_plus";
	$HelpDet_BCDA 	= "so_component_detail_add";
}

$qHeader		= "SELECT a.*, b.*, b.id AS id_unik FROM ".$HelpDet_BCH." a INNER JOIN ".$HelpDet_BDH." b ON a.id_milik = b.id WHERE a.id_product='".$kode_product."' AND a.id_milik ='".$id_milik."' ";
$dHeader		= $this->db->query($qHeader)->result_array();

$qIPP			= "SELECT a.* FROM production a WHERE a.no_ipp='".$dHeader2[0]['no_ipp']."' ";
$dRIPP			= $this->db->query($qIPP)->result_array();

$detailResinXX	= "SELECT nm_category, nm_material, MIN(last_cost) as last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 2 ";
$valHXX 		= $this->db->query($detailResinXX)->result_array();
$NilaiMinTeeSl 	= $valHXX[0]['last_cost'];

$qSO			= "SELECT a.so_number, a.no_ipp FROM so_bf_header a WHERE a.no_ipp='".$dHeader2[0]['no_ipp']."' ";
$dataSO			= $this->db->query($qSO)->result_array();
$no_so 			= (!empty($dataSO[0]['so_number']))?" / ".$dataSO[0]['so_number']:'';

if (substr($kode_product,0,2) != "BJ" AND substr($kode_product,0,2) != "SJ" AND substr($kode_product,0,2) != "FJ") {
?>
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader2[0]['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>LAPORAN PEMAKAIAN MATERIAL</h2></b></td>
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
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $dHeader2[0]['no_ipp'].' / '.get_nomor_so($dHeader2[0]['no_ipp']); ?></td>
		</tr>
		<tr>
			<td>SPK Number</td>
			<td>:</td>
			<td><?= $dHeader[0]['no_spk'];?></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dRIPP[0]['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>Machine Number</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2[0]['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= spec_fd($dHeader[0]['id_unik'], $HelpDet_BDH);?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dRIPP[0]['project']); ?></td>
			<td style='vertical-align:top;'><?= ucwords($dHeader[0]['parent_product']);?> To</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= $product_to." (".strtoupper(strtolower($dHeader[0]['no_komponen'])).") of ".$dHeader[0]['qty']." Component";?></td>
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
				<th width='27%'>Material Type</th>
				<th width='10%'>Qty</th>
				<th width='15%'>Lot/Batch Num</th>
				<th width='10%'>Actual Type</th>
				<th width='8%'>Layer</th>
				<th width='8%'>Used</th>
			</tr>
			<?php
			if($dHeader[0]['id_category'] != 'flange slongsong' AND $dHeader[0]['id_category'] != 'equal tee slongsong' AND $dHeader[0]['id_category'] != 'colar slongsong' AND $dHeader[0]['id_category'] != 'elbow mitter'){
			?>
				<tr>
					<th align='left' colspan='8'>LINER THIKNESS / CB</th>
				</tr>
			<?php 
			}
			?>
		</thead>
		
		<?php
		if($dHeader[0]['id_category'] != 'flange slongsong' AND $dHeader[0]['id_category'] != 'equal tee slongsong' AND $dHeader[0]['id_category'] != 'colar slongsong' AND $dHeader[0]['id_category'] != 'elbow mitter'){
			echo "<tbody>";
				$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001' ";
				$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
				
				foreach($dDetailLiner AS $val => $valx){
					$dataL	= ($valx['layer'] == 0.00)?'-':(floatval($valx['layer']) == 0)?'-':floatval($valx['layer']);
					?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td align='center'><?= $dataL;?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?php
				}

				$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
				$dDetailResin	= $this->db->query($detailResin)->result_array();
				// echo $detailResin;
				foreach($dDetailResin AS $val => $valx){
					?>
					<tr>
						<td colspan='2'><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td colspan='2'></td>
						<td></td>
					</tr>
					<?php
				}

				$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
				$dDetailPlus	= $this->db->query($detailPlus)->result_array();
				// echo $detailPlus;
				foreach($dDetailPlus AS $val => $valx){
					?>
					<tr>
						<td colspan='2'><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td colspan='2'></td>
						<td></td>
					</tr>
					<?php
				}
			echo "</tbody>";
		} 

		//JIKA ADA NECK 1 & 2
		if($dHeader[0]['parent_product'] == 'flange mould' OR $dHeader[0]['parent_product'] == 'flange slongsong' OR $dHeader[0]['parent_product'] == 'colar' OR $dHeader[0]['parent_product'] == 'colar slongsong'){
			if($dHeader[0]['id_category'] != 'flange slongsong' AND $dHeader[0]['id_category'] != 'colar slongsong'){
				?>
				<thead align='center'>
					<tr>
						<th align='left' colspan='8'>STRUKTUR NECK 1</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' ";
				$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
				foreach($dDetailLiner AS $val => $valx){
					$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
					$SUn	= "";
					if($valx['id_category'] == 'TYP-0005'){
						$SUn	= " | ".floatval($valx['jumlah']);
					}
					?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td align='center'><?= floatval($dataL);?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
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

				$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
				$dDetailResin	= $this->db->query($detailResin)->result_array();
				foreach($dDetailResin AS $val => $valx){
				?>
					<tr>
						<td colspan='2'><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td colspan='2'></td>
						<td></td>
					</tr>
				<?php
				}

				$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
				$dDetailPlus	= $this->db->query($detailPlus)->result_array();
				foreach($dDetailPlus AS $val => $valx){
				?>
					<tr>
						<td colspan='2'><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td colspan='2'></td>
						<td></td>
					</tr>
				<?php
				}
				echo "</tbody>";
			} 
			?>
			<!-- NECK 2-->
			<thead align='center'>
				<tr>
					<th align='left' colspan='8'>STRUKTUR NECK 2</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$tDetailLinerN2	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' ";
				$dDetailLinerN2	= $this->db->query($tDetailLinerN2)->result_array();
				foreach($dDetailLinerN2 AS $val => $valx){
					$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
					$SUn	= "";
					if($valx['id_category'] == 'TYP-0005'){
						$SUn	= " | ".floatval($valx['jumlah']);
					}
					?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td align='center'><?= floatval($dataL);?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
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
				$detailResinN2	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 2' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
				$dDetailResinN2	= $this->db->query($detailResinN2)->result_array();
				foreach($dDetailResinN2 AS $val => $valx){
					?>
					<tr>
						<td colspan='2'><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td colspan='2'></td>
						<td></td>
					</tr>
					<?php
				}
				$detailPlusN2	= "SELECT nm_category, nm_material, last_cost, last_full  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
				$dDetailPlusN2	= $this->db->query($detailPlusN2)->result_array();
				foreach($dDetailPlusN2 AS $val => $valx){
					?>
					<tr>
						<td colspan='2'><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td colspan='2'></td>
						<td></td>
					</tr>
					<?php
				}
			echo "</tbody>";
		}
		?>
	</table>


	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='8'>STRUKTUR THICKNESS</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' ";
			if($dHeader[0]['parent_product'] == 'equal tee slongsong' OR $dHeader[0]['parent_product'] == 'elbow mitter'){
				$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' AND id_category <>'TYP-0005' ";
			}
			$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
			foreach($dDetailLiner AS $val => $valx){
				$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
				$SUn	= "";
				if($valx['id_category'] == 'TYP-0005'){
					$SUn	= " | ".floatval($valx['jumlah']);
				}
				?>
				<tr>
					<td width='13%'><?= $valx['nm_category'];?></td>
					<td width='7%' align='center'><?= floatval($dataL);?></td>
					<td width='27%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td width='15%'></td>
					<td width='10%'></td>
					<td width='8%'></td>
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

			$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
			$dDetailResin	= $this->db->query($detailResin)->result_array();
			$nilbanTee = 0;
			if($dHeader[0]['id_category'] == 'equal tee slongsong' OR $dHeader[0]['id_category'] == 'elbow mitter'){
				$nilbanTee = $NilaiMinTeeSl;
			}
			foreach($dDetailResin AS $val => $valx){
				?>
				<tr>
					<td colspan='2'><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format(($valx['last_cost'] - $nilbanTee)  * $qty, 3);?> Kg</td>
					<td></td>
					<td colspan='2'></td>
					<td></td>
				</tr>
				<?php
			}

			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
				?>
				<tr>
					<td colspan='2'><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td colspan='2'></td>
					<td></td>
				</tr>
				<?php
			}
			?>
		</tbody>
		
		<?php
		$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' ";
		$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
		if(!empty($dDetailLiner)){
			?>
			<thead align='center'>
				<tr>
					<th align='left' colspan='8'>EXTERNAL LAYER THICKNESS</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach($dDetailLiner AS $val => $valx){
				$dataL	= ($valx['layer'] == 0.00)?'-':number_format($valx['layer']);
				?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td align='center'><?= $dataL;?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}

			$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
			$dDetailResin	= $this->db->query($detailResin)->result_array();
			foreach($dDetailResin AS $val => $valx){
				?>
				<tr>
					<td colspan='2'><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td colspan='2'></td>
					<td></td>
				</tr>
				<?php
			}

			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
				?>
				<tr>
					<td colspan='2'><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td colspan='2'></td>
					<td></td>
				</tr>
				<?php
			}
			echo "</tbody>";
		}
		?>
	</table>


	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='6'>TOPCOAT</th>
			</tr>
			<tr>
				<th width='20%'>Material</th>
				<th width='29%'>Material Type</th>
				<th width='10%'>Qty</th>
				<th width='15%'>Lot/Batch Num</th>
				<th width='18%'>Actual Type</th>
				<th width='8%'>Used</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000' AND (id_category = 'TYP-0002' OR id_category = 'TYP-0001') ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
			?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
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
				<td align='center'><b><?= floatval($dHeader[0]['est']);?></b></td>
				<td><b>Thickness Act (Web)</b></td>
				<td></td>
				<td><b>Thickness Act (Dry)</b></td>
				<td width='80px'></td>
			</tr>
			<tr>
				<td><b>Status : Reject / Pass</b></td>
				<td colspan='2'><b>Inspector :</b></td>
				<td width='100px'><b>Signed : </b></td>
				<td colspan='2'><b>Inspection Date : </b></td>
			</tr>
			<tr>
				<td height='60px' colspan='6' style='vertical-align: top;'><b>Note :</b></td>
			</tr>
		</tbody>
	</table>

	<?php
	if($dHeader[0]['id_category'] == 'pipe' OR $dHeader[0]['id_category'] == 'pipe slongsong'){
		$TamSudut = "53-57";
	}
	else{
		$TamSudut = "";
	}
	?>

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
				<td align='center'><?=$TamSudut;?></td>
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

	<?php
	if($dHeader[0]['id_category'] == 'flange slongsong' || $dHeader[0]['id_category'] == 'equal tee slongsong' || $dHeader[0]['id_category'] == 'colar slongsong' || $dHeader[0]['id_category'] == 'elbow mitter'){
		echo "<pagebreak />";
		?>

		<table class="gridtable" border='1' width='100%' cellpadding='2'>
			<thead>
			<tr>
				<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
				<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
				<td width='15%'>Doc Number</td>
				<td width='15%'><?= $dHeader2[0]['no_ipp']; ?></td>
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
			<thead>
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
				<td width='20%'>IPP Number</td>
				<td width='1%'>:</td>
				<td width='29%'><?= $dHeader2[0]['no_ipp'].' / '.get_nomor_so($dHeader2[0]['no_ipp']); ?></td>
			</tr>
			<tr>
				<td>SPK Number</td>
				<td>:</td>
				<td><?= $dHeader[0]['no_spk'];?></td>
				<td>Customer</td>
				<td>:</td>
				<td><?= $dRIPP[0]['nm_customer']; ?></td>
			</tr>
			<tr>
				<td>Machine Number</td>
				<td>:</td>
				<td><?= strtoupper($dHeader2[0]['nm_mesin']);?></td>
				<td>Spec Product</td>
				<td>:</td>
				<td><?= spec_fd($dHeader[0]['id_unik'], $HelpDet_BDH);?></td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>Project</td>
				<td style='vertical-align:top;'>:</td>
				<td style='vertical-align:top;'><?= strtoupper($dRIPP[0]['project']); ?></td>
				<td style='vertical-align:top;'><?= ucwords($dHeader[0]['parent_product']);?> To</td>
				<td style='vertical-align:top;'>:</td>
				<td style='vertical-align:top;'><?= str_replace('-',', ',$product_to)." (".strtoupper(strtolower($dHeader[0]['no_komponen'])).") of ".$dHeader[0]['qty']." Component";?></td>
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
					<th width='7%'>Number Layer</th>
					<th width='27%'>Material Type</th>
					<th width='10%'>Qty</th>
					<th width='15%'>Lot/Batch Num</th>
					<th width='10%'>Actual Type</th>
					<th width='8%'>Layer</th>
					<th width='8%'>Used</th>
				</tr>
				<tr>
					<th align='left' colspan='8'>LINER THIKNESS / CB</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001' ";
			$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
			foreach($dDetailLiner AS $val => $valx){
				$dataL	= ($valx['layer'] == 0.00)?'-':(floatval($valx['layer']) == 0)?'-':floatval($valx['layer']);
				?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td align='center'><?= $dataL;?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}

			$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
			$dDetailResin	= $this->db->query($detailResin)->result_array();
			foreach($dDetailResin AS $val => $valx){
				?>
				<tr>
					<td colspan='2'><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td colspan='2'></td>
					<td></td>
				</tr>
				<?php
			}

			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
				?>
				<tr>
					<td colspan='2'><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td colspan='2'></td>
					<td></td>
				</tr>
				<?php
			}
			?>

			</tbody>
			<?php
			if($dHeader[0]['parent_product'] == 'flange mould' OR $dHeader[0]['parent_product'] == 'flange slongsong' OR $dHeader[0]['parent_product'] == 'colar' OR $dHeader[0]['parent_product'] == 'colar slongsong'){
			?>
				<thead align='center'>
					<tr>
						<th align='left' colspan='8'>STRUKTUR NECK 1</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' ";
					$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
					foreach($dDetailLiner AS $val => $valx){
						$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
						$SUn	= "";
						if($valx['id_category'] == 'TYP-0005'){
							$SUn	= " | ".floatval($valx['jumlah']);
						}
						?>
						<tr>
							<td><?= $valx['nm_category'];?></td>
							<td align='center'><?= floatval($dataL);?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td></td>
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

					$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
					$dDetailResin	= $this->db->query($detailResin)->result_array();
					foreach($dDetailResin AS $val => $valx){
						?>
						<tr>
							<td colspan='2'><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td colspan='2'></td>
							<td></td>
						</tr>
						<?php
					}

					$detailPlus		= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
					$dDetailPlus	= $this->db->query($detailPlus)->result_array();
					foreach($dDetailPlus AS $val => $valx){
						?>
						<tr>
							<td colspan='2'><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td colspan='2'></td>
							<td></td>
						</tr>
						<?php
					}
				echo "</tbody>";
			}



			if($dHeader[0]['parent_product'] == 'equal tee slongsong' OR $dHeader[0]['parent_product'] == 'elbow mitter'){
				?>
				<thead align='center'>
					<tr>
						<th align='left' colspan='8'>STRUKTUR THICKNESS</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category ='TYP-0005' ";
					$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
					foreach($dDetailLiner AS $val => $valx){
						$dataL	= ($valx['layer'] == 0.00)?'-':$valx['layer'];
						$SUn	= "";
						if($valx['id_category'] == 'TYP-0005'){
							$SUn	= " | ".floatval($valx['jumlah']);
						}
						?>
						<tr>
							<td width='13%'><?= $valx['nm_category'];?></td>
							<td width='7%' align='center'><?= floatval($dataL);?></td>
							<td width='27%'><?= $valx['nm_material'];?></td>
							<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td width='15%'></td>
							<td width='10%'></td>
							<td width='8%'></td>
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

					$detailResin	= "SELECT nm_category, nm_material, MIN(last_cost) as last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 2 ";
					$dDetailResin	= $this->db->query($detailResin)->result_array();
					foreach($dDetailResin AS $val => $valx){
						?>
						<tr>
							<td colspan='2'><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td colspan='2'></td>
							<td></td>
						</tr>
						<?php
					}
					$detailPlus2	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category = 'TYP-0002' ";
					$dDetailPlus2	= $this->db->query($detailPlus2)->result_array();
					foreach($dDetailPlus2 AS $val => $valx){
						?>
						<tr>
							<td colspan='2'><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td colspan='2'></td>
							<td></td>
						</tr>
						<?php
					}
				echo "</tbody>";
			}
			?>
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
					<td align='center'><b><?= floatval($dHeader[0]['est_neck_1']);?></b></td>
					<td><b>Thickness Act (Web)</b></td>
					<td></td>
					<td><b>Thickness Act (Dry)</b></td>
					<td width='80px'></td>
				</tr>
				<tr>
					<td><b>Status : Reject / Pass</b></td>
					<td colspan='2'><b>Inspector :</b></td>
					<td width='100px'><b>Signed : </b></td>
					<td colspan='2'><b>Inspection Date : </b></td>
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
		<?php
	}
	echo "<pagebreak />";
	?>

	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dRIPP[0]['no_ipp'];?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>LAPORAN PEMAKAIAN MATERIAL</h2></b></td>
			<td>Rev.</td>
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
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $dRIPP[0]['no_ipp'].' / '.get_nomor_so($dRIPP[0]['no_ipp']); ?></td>
		</tr>
		<tr>
			<td>SPK Number</td>
			<td>:</td>
			<td><?= $dHeader[0]['no_spk'];?></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dRIPP[0]['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>Machine Number</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2[0]['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= spec_fd($dHeader[0]['id_unik'], $HelpDet_BDH);?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dRIPP[0]['project']); ?></td>
			<td style='vertical-align:top;'><?= ucwords($dHeader[0]['parent_product']);?> To</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= $product_to." (".strtoupper(strtolower($dHeader[0]['no_komponen'])).") of ".$dHeader[0]['qty']." Component";?></td>
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
				<th width='20%'>Material</th>
				<th width='29%'>Material Type</th>
				<th width='10%'>Qty</th>
				<th width='13%'>Lot/Batch Num</th>
				<th width='18%'>Actual Type</th>
				<th width='8%'>Used</th>
			</tr>
			<tr>
				<th align='left' colspan='6'>LINER THIKNESS / CB</th>
			</tr>

		</thead>
		<tbody>
			<?php
			$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
			$dDetailResin	= $this->db->query($detailResin)->result_array();
			foreach($dDetailResin AS $val => $valx){
				?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}

			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
				?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}

			$detailAdd	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000'";
			$dDetailAdd	= $this->db->query($detailAdd)->result_array();
			if(!empty($dDetailAdd)){
				echo "<tr>";
					echo "<th align='left' colspan='6'>Add Materials</th>";
				echo "</tr>";
				foreach($dDetailAdd AS $val => $valx){
				?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				<?php
				}
			}
			?>
		</tbody>
	</table>

	<?php
	if($dHeader[0]['parent_product'] == 'flange mould' OR $dHeader[0]['parent_product'] == 'flange slongsong'){
	?>
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='6'>STRUKTUR NECK 1</th>
				</tr>

			</thead>
			<tbody>
				<?php

				$detailResinN1	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
				$dDetailResinN1	= $this->db->query($detailResinN1)->result_array();
				foreach($dDetailResinN1 AS $val => $valx){
					?>
					<tr>
						<td width='20%'><?= $valx['nm_category'];?></td>
						<td width='29%'><?= $valx['nm_material'];?></td>
						<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td width='13%'></td>
						<td width='18%'></td>
						<td width='8%'></td>
					</tr>
					<?php
				}

				$detailPlusN1	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' ";
				$dDetailPlusN1	= $this->db->query($detailPlusN1)->result_array();
				foreach($dDetailPlusN1 AS $val => $valx){
					?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?php
				}

				$detailAddN1	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 1' AND id_material <> 'MTL-1903000'";
				$dDetailAddN1	= $this->db->query($detailAddN1)->result_array();
				if(!empty($dDetailAddN1)){
					echo "<tr>";
						echo "<th align='left' colspan='6'>Add Materials</th>";
					echo "</tr>";

					foreach($dDetailAddN1 AS $val => $valx){
					?>
						<tr>
							<td><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td></td>
							<td></td>
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
					<th align='left' colspan='6'>STRUKTUR NECK 2</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$detailResinN2	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
				$dDetailResinN2	= $this->db->query($detailResinN2)->result_array();
				foreach($dDetailResinN2 AS $val => $valx){
					?>
					<tr>
						<td width='20%'><?= $valx['nm_category'];?></td>
						<td width='29%'><?= $valx['nm_material'];?></td>
						<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td width='13%'></td>
						<td width='18%'></td>
						<td width='8%'></td>
					</tr>
					<?php
				}

				$detailPlusN2	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' ";
				$dDetailPlusN2	= $this->db->query($detailPlusN2)->result_array();
				foreach($dDetailPlusN2 AS $val => $valx){
					?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?php
				}

				$detailAddN2	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR NECK 2' AND id_material <> 'MTL-1903000'";
				$dDetailAddN2	= $this->db->query($detailAddN2)->result_array();
				if(!empty($dDetailAddN2)){
					echo "<tr>";
						echo "<th align='left' colspan='6'>Add Materials</th>";
					echo "</tr>";

					foreach($dDetailAddN2 AS $val => $valx){
					?>
						<tr>
							<td><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td></td>
							<td></td>
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

	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='6'>STRUKTUR THICKNESS</th>
			</tr>

		</thead>
		<tbody>
			<?php
			$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
			$dDetailResin	= $this->db->query($detailResin)->result_array();
			foreach($dDetailResin AS $val => $valx){
				?>
				<tr>
					<td width='20%'><?= $valx['nm_category'];?></td>
					<td width='29%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td width='13%'></td>
					<td width='18%'></td>
					<td width='8%'></td>
				</tr>
				<?php
			}

			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
				?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}

			$detailAdd	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000'";
			$dDetailAdd	= $this->db->query($detailAdd)->result_array();
			if(!empty($dDetailAdd)){
				echo "<tr>";
					echo "<th align='left' colspan='6'>Add Materials</th>";
				echo "</tr>";

				foreach($dDetailAdd AS $val => $valx){
				?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				}
			}
			?>
		</tbody>
	</table>

	<?php
	$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <>'TYP-0001' ";
	$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
	if(!empty($dDetailLiner)){
		?>
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead align='center'>
				<tr>
					<th align='left' colspan='6'>EXTERNAL LAYER THICKNESS</th>
				</tr>

			</thead>
			<tbody>
				<?php
				$detailResin	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_category ='TYP-0001' ORDER BY id_detail DESC LIMIT 1 ";
				$dDetailResin	= $this->db->query($detailResin)->result_array();
				foreach($dDetailResin AS $val => $valx){
					?>
					<tr>
						<td width='20%'><?= $valx['nm_category'];?></td>
						<td width='29%'><?= $valx['nm_material'];?></td>
						<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td width='13%'></td>
						<td width='18%'></td>
						<td width='8%'></td>
					</tr>
					<?php
				}

				$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' ";
				$dDetailPlus	= $this->db->query($detailPlus)->result_array();
				foreach($dDetailPlus AS $val => $valx){
					?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?php
				}

				$detailAdd	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='EXTERNAL LAYER THICKNESS' AND id_material <> 'MTL-1903000'";
				$dDetailAdd	= $this->db->query($detailAdd)->result_array();
				if(!empty($dDetailAdd)){
					echo "<tr>";
						echo "<th align='left' colspan='6'>Add Materials</th>";
					echo "</tr>";

					foreach($dDetailAdd AS $val => $valx){
					?>
						<tr>
							<td><?= $valx['nm_category'];?></td>
							<td><?= $valx['nm_material'];?></td>
							<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					<?php
					}
				}
			echo "</tbody>";
		echo "</table>";
	}
	?>

	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='6'>TOPCOAT</th>
			</tr>

		</thead>
		<tbody>
			<?php
			$detailPlus	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDP." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0002' ";
			$dDetailPlus	= $this->db->query($detailPlus)->result_array();
			foreach($dDetailPlus AS $val => $valx){
				?>
				<tr>
					<td width='20%'><?= $valx['nm_category'];?></td>
					<td width='29%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td width='13%'></td>
					<td width='18%'></td>
					<td width='8%'></td>
				</tr>
				<?php
			}

			$detailAdd	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."' AND detail_name='TOPCOAT' AND id_material <> 'MTL-1903000'";
			$dDetailAdd	= $this->db->query($detailAdd)->result_array();
			if(!empty($dDetailAdd)){
				echo "<tr>";
					echo "<th align='left' colspan='6'>Add Materials</th>";
				echo "</tr>";

				foreach($dDetailAdd AS $val => $valx){
				?>
					<tr>
						<td><?= $valx['nm_category'];?></td>
						<td><?= $valx['nm_material'];?></td>
						<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
						<td></td>
						<td></td>
						<td></td>
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
			<td height='50px' colspan='7'></td>
		</tr>
	</table>
	<div id='space'></div>
<?php 
}
else{
?>
	<table class="gridtable" border='1' width='100%' cellpadding='2'>
		<tr>
			<td width='70px' rowspan='3' style='padding:0px;'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='80' width='70' ></td>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
			<td width='15%'>Doc Number</td>
			<td width='15%'><?= $dHeader2[0]['no_ipp']; ?></td>
		</tr>
		<tr>
			<td align='center' rowspan='2'><b><h2>LAPORAN PEMAKAIAN MATERIAL</h2></b></td>
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
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $dHeader2[0]['no_ipp'].' / '.get_nomor_so($dHeader2[0]['no_ipp']); ?></td>
		</tr>
		<tr>
			<td>SPK Number</td>
			<td>:</td>
			<td><?= $dHeader[0]['no_spk'];?></td>
			<td>Customer</td>
			<td>:</td>
			<td><?= $dRIPP[0]['nm_customer']; ?></td>
		</tr>
		<tr>
			<td>Machine Number</td>
			<td>:</td>
			<td><?= strtoupper($dHeader2[0]['nm_mesin']);?></td>
			<td>Spec Product</td>
			<td>:</td>
			<td><?= spec_fd($dHeader[0]['id_unik'], $HelpDet_BDH);?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dRIPP[0]['project']); ?></td>
			<td style='vertical-align:top;'><?= ucwords($dHeader[0]['parent_product']);?> To</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= $product_to." (".strtoupper(strtolower($dHeader[0]['no_komponen'])).") of ".$dHeader[0]['qty']." Component";?></td>
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
				<th width='10%'>Qty</th>
				<th width='15%'>Lot/Batch Num</th>
				<th width='10%'>Actual Type</th>
				<th width='8%'>Used</th>
			</tr>
			<tr>
				<th align='left' colspan='6'>GLASS</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='GLASS' AND id_material <> 'MTL-1903000' ";
			$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
			foreach($dDetailLiner AS $val => $valx){
				?>
				<tr>
					<td width='13%'><?= $valx['nm_category'];?></td>
					<td width='27%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td width='15%'></td>
					<td width='10%'></td>
					<td width='8%'></td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<thead align='center'>
			<tr>
				<th align='left' colspan='6'>RESIN AND ADD</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$tDetailLiner	= "SELECT nm_category, layer, nm_material, last_cost, jumlah, id_category, bw  FROM ".$HelpDet_BCD." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'  AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' ";
			$dDetailLiner	= $this->db->query($tDetailLiner)->result_array();
			foreach($dDetailLiner AS $val => $valx){
				?>
				<tr>
					<td width='13%'><?= $valx['nm_category'];?></td>
					<td width='27%'><?= $valx['nm_material'];?></td>
					<td width='10%' align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td width='15%'></td>
					<td width='10%'></td>
					<td width='8%'></td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<?php
		$detailAdd	= "SELECT nm_category, nm_material, last_cost  FROM ".$HelpDet_BCDA." WHERE id_product='".$kode_product."' AND id_milik='".$id_milik."'";
		$dDetailAdd	= $this->db->query($detailAdd)->result_array();
		if(!empty($dDetailAdd)){
			echo "<tr>";
				echo "<th align='left' colspan='6'>Add Materials</th>";
			echo "</tr>";

			foreach($dDetailAdd AS $val => $valx){
			?>
				<tr>
					<td><?= $valx['nm_category'];?></td>
					<td><?= $valx['nm_material'];?></td>
					<td align='right'><?= number_format($valx['last_cost'] * $qty, 3);?> Kg</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			<?php
			}
		}
		?>
	</table>
	
	<div id='space'></div>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<tr>
			<th align='left' colspan='6'>NOTE</th>
		</tr>
		<tr>
			<td height='50px' colspan='6'></td>
		</tr>
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
<?php
}
?>

<style type="text/css">
	@page {
		margin-top: 1 cm;
		margin-left: 1 cm;
		margin-right: 1 cm;
		margin-bottom: 1 cm;
		margin-footer: 0 cm
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
	}
	.font{
		font-family: verdana,arial,sans-serif;
		font-size:14px;
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
$html = ob_get_contents();
$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px; color:black;'><i>Printed by : ".ucwords(strtolower($printby)).", ".$today." / ".$kode_product." / ".$dHeader2[0]['no_ipp']."</i></p>";
// exit;
ob_end_clean();
// $mpdf->SetWatermarkText('ORI Group');
$mpdf->showWatermarkText = true;
$mpdf->SetTitle('SPK Of Production');
$mpdf->AddPage();
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output($kode_produksi.'_'.strtolower($dHeader[0]['nm_product']).'_product_ke_'.$product_to.'.pdf' ,'I');
?>