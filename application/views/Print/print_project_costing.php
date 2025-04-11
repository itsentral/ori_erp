<?php

$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
$mpdf		= new mPDF('utf-8','A4');

set_time_limit(0);
ini_set('memory_limit','1024M');

ob_start();
date_default_timezone_set('Asia/Jakarta');
$today 		= date('l, d F Y [H:i:s]');

$qBQ 		= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' ";
$dHeaderBQ	= $this->db->query($qBQ)->result_array();

$sql 		= "	SELECT
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
					(a.direct_labour * a.qty) AS direct_labour,
					(a.indirect_labour * a.qty) AS indirect_labour,
					(a.machine * a.qty) AS machine,
					(a.mould_mandrill * a.qty) AS mould_mandrill,
					(a.consumable * a.qty) AS consumable,
					(
						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) * `a`.`qty` 
					) AS `cost_process`,
					(
						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
					) * ( (b.pe_foh_consumable) / 100 ) * a.qty AS foh_consumable,
					(
						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
					) * ( (b.pe_foh_depresiasi) / 100 ) * a.qty AS foh_depresiasi,
					(
						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
					) * ( (b.pe_biaya_gaji_non_produksi) / 100 ) * a.qty AS biaya_gaji_non_produksi,
					(
						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
					) * ( (b.pe_biaya_non_produksi) / 100 ) * a.qty AS biaya_non_produksi,
					(
						((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
					) * ( (b.pe_biaya_rutin_bulanan) / 100 ) * a.qty AS biaya_rutin_bulanan 
				FROM
					estimasi_cost_and_mat a
					INNER JOIN bq_product b ON a.id_milik = b.id
				WHERE
					b.parent_product <> 'pipe slongsong' AND b.parent_product <> 'product kosong' AND
					a.id_bq = '".$id_bq."' ORDER BY a.id_milik ASC";
$result		= $this->db->query($sql)->result_array();

$detail 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'acc'))->result_array();
$detail2 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'mat'))->result_array();
$detail3 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'baut'))->result_array();
$detail4 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'plate'))->result_array();
$detail4g 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'gasket'))->result_array();
$detail5 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'lainnya'))->result_array();

?>

<table class="gridtable" border='1' width='100%' cellpadding='2'>
	<tr>
		<td width='80px' align='center' rowspan='2'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='90' width='80' ></td>
		<td align='center'><b><h2>PT  ORI POLYTEC COMPOSITE</h2></b></td>
	</tr>
	<tr>
		<td align='center'><b><h3>PROJECT RESULTS <?= str_replace('BQ-','',$id_bq);?></h3><?= strtoupper($dHeaderBQ[0]['project']); ?><br><?= strtoupper($dHeaderBQ[0]['nm_customer']); ?></b></td>
	</tr>
</table>
<?php if(!empty($result)){ ?>
<br>
<div class="box box-primary">
	<div class="box-header">
		<label>PIPA FITTING</label>
	</div>
	<div class="box-body">
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='12%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Material Est (Kg)</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Material Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='10%'>Process Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='10%'>COGS</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$Sum = 0;
					$SumX = 0;
					$Sum2 = 0;
					$SumX2 = 0;
					$Cost = 0;
					$No=0;
					$COGS = 0;
					if(!empty($result)){
						foreach($result AS $val => $valx){
							$No++;
							$spaces = "";
							$bgwarna = 'bg-blue';
							
							$SumQty	= $valx['sum_mat2'];
							$Sum += $SumQty;
							
							$SumQtyX	= $valx['est_harga2'];
							$SumX += $SumQtyX;
							
							$Costx2	= $valx['direct_labour'] + $valx['indirect_labour'] + $valx['machine'] + $valx['mould_mandrill'] + $valx['consumable'] + $valx['foh_consumable'] + $valx['foh_depresiasi'] + $valx['biaya_gaji_non_produksi'] + $valx['biaya_non_produksi'] + $valx['biaya_rutin_bulanan'];
							$Cost += $Costx2;
							
							$TotalCost = $Costx2;
							
							$cogsx = $Costx2 + $SumQtyX;
							$COGS += $cogsx;
							
							if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
								$lengthX = (floatval($valx['length']));
							}
							else{
								$lengthX = (floatval($valx['length']));
							}
							
							echo "<tr>";
								echo "<td align='center'>".$No."</td>";
								echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
								echo "<td align='left'>".$spaces."".spec_bq($valx['id_milik'])."</td>";
								echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
								echo "<td align='left'>".$valx['id_product']."</span></td>";
								echo "<td align='right'>".number_format($SumQty, 3)."</span></td>";
								echo "<td align='right'>".number_format($SumQtyX, 2)."</span></td>";
								echo "<td align='right'><a id='detail_process_cost2' style='cursor:pointer;' data-id='".$valx['id_milik']."' data-id_bq='".$valx['id_bq']."' data-id_product='".$valx['id_product']."'>".number_format($TotalCost, 2)."</a></td>";
								echo "<td align='right'>".number_format($cogsx, 2)."</span></td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='9'>Tidak ada product yang ditampilkan</td>";
						echo "</tr>";
					}
				?>
				<tr>
					<td class="text-center" colspan='5' style='vertical-align:middle;'></td>
					<td align='right'><b><?= number_format($Sum, 3);?></b></td>
					<td align='right'><b><?= number_format($SumX, 2);?></b></td>
					<?php
					echo "<td align='right'><b>".number_format($Cost, 2)."</b></td>";
					echo "<td align='right'><b>".number_format($COGS, 2)."</b></td>";
					?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php } ?>
<?php if(!empty($detail3)){ ?>
<br>
<div class="box box-success">
	<div class="box-header">
		<label>MUR BAUT</label>
	</div>
	<div class="box-body">
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='34%'>Material</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center" width='7%'>Unit</th>
					<th class="text-center" width='17%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail3)){
						foreach($detail3 AS $val => $valx){ $id++;
							$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
							$SUM += $valx['total_price'];
							echo "<tr class='header3_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->material)."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='7'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='8'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
		</table>
	</div>
</div>
<?php } ?>
<?php if(!empty($detail4)){ ?>
<br>
<div class="box box-success">
	<div class="box-header">
		<label>PLATE</label>
	</div>
	<div class="box-body">
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Ukuran Standart</th>
					<th class="text-center" width='10%'>Standart</th>
					<th class="text-center" width='7%'>Lebar (mm)</th>
					<th class="text-center" width='7%'>Panjang (mm)</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center" width='7%'>Berat (kg)</th>
					<th class="text-center" width='7%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail4)){
						foreach($detail4 AS $val => $valx){ $id++;
							$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$SUM += $valx['total_price'];
							echo "<tr class='header4_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->ukuran_standart)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='right'>".number_format($valx['berat'],3)."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='11'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='12'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>
<?php } ?>
<?php if(!empty($detail4g)){ ?>
<br>
<div class="box box-success">
	<div class="box-header">
		<label>GASKET</label>
	</div>
	<div class="box-body">
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Standart</th>
					<th class="text-center" width='10%'>Dimensi</th>
					<th class="text-center" width='7%'>Lebar (mm)</th>
					<th class="text-center" width='7%'>Panjang (mm)</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center" width='7%'>Unit</th>
					<th class="text-center" width='7%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail4g)){
						foreach($detail4g AS $val => $valx){ $id++;
							$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$SUM += $valx['total_price'];
							echo "<tr class='header4g_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->dimensi)."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='11'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='10'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>
<?php } ?>
<?php if(!empty($detail5)){ ?>
<br>
<div class="box box-success">
	<div class="box-header">
		<label>LAINNYA</label>
	</div>
	<div class="box-body">
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Ukuran Standart</th>
					<th class="text-center" width='14%'>Standart</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center" width='7%'>Unit</th>
					<th class="text-center" width='17%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail5)){
						foreach($detail5 AS $val => $valx){ $id++;
							$get_detail = $this->db->select('nama, material, spesifikasi, standart, ukuran_standart, dimensi')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$SUM += $valx['total_price'];
							echo "<tr class='header5_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->ukuran_standart)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='8'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>
<?php } ?>
<?php if(!empty($detail2)){ ?>
<br>
<div class="box box-info">
	<div class="box-header">
		<label>MATERIAL</label>
	</div>
	<div class="box-body">
		<table class="gridtable" width='100%' border='1' cellpadding='2'>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center" width='7%'>Unit</th>
					<th class="text-center" width='17%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail2)){
						foreach($detail2 AS $val => $valx){ $id++;
							$SUM += $valx['total_price'];
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>"; 
								echo "<td align='left'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='6'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='7'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>
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
$mpdf->SetTitle(str_replace('BQ-','',$id_bq));
$mpdf->AddPage('L');
$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output("RESULT PROJECT ".str_replace('BQ-','',$id_bq)." ".date('dmYHis').".pdf" ,'I');

