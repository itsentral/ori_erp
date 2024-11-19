<?php
$id_bq = $this->uri->segment(3);

$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
$getHeader	= $this->db->query($qSupplier)->result();

$qMatr 		= SQL_FD($id_bq);					
$getDetail	= $this->db->query($qMatr)->result_array();

$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
$getEngCost	= $this->db->query($engC)->result_array();

$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
$getPackCost	= $this->db->query($engCPC)->result_array();
// echo $engCPC;
$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
$getTruck	= $this->db->query($gTruck)->result_array();

$engCPCV 	= "SELECT
					b.*,
					c.* 
				FROM
					cost_project_detail b
					LEFT JOIN truck c ON b.kendaraan = c.id 
				WHERE
					 b.category = 'lokal' 
					AND b.id_bq = '".$id_bq."' 
					AND b.price_total <> 0
				ORDER BY
					b.id ASC ";
$getVia	= $this->db->query($engCPCV)->result_array();


?>
<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>PRODUCT</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='2' width='22%'>Item Product</th>
			<th class="text-center" width='6%'>Dim 1</th>
			<th class="text-center" width='6%'>Dim 2</th>
			<th class="text-center" width='6%'>Liner</th>
			<th class="text-center" width='8%'>Pressure</th>
			<th class="text-center" width='17%'>Specification</th>
			<th class="text-center" width='10%'>Unit Price</th>
			<th class="text-center" width='7%'>Qty</th>
			<th class="text-center" width='8%'>Unit</th>
			<th class="text-center" width='11%'>Total Price (USD)</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$SUM = 0;
		$no = 0;
		foreach($getDetail AS $val => $valx){
			$no++;
			// $NegoPersen 	= (!empty($valx['nego']))?$valx['nego']:'10';
			$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
				
			$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
			$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
			
			$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
			$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
			$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
			
			$nego		= $HrgTot * ($NegoPersen/100);
			$dataSum	= $HrgTot + $nego;
			
			$SUM += $dataSum;
			
			if($valx['parent_product'] == 'pipe' OR $valx['parent_product'] == 'pipe slongsong'){
				$unitT = "Btg";
			}
			else{
				$unitT = "Pcs";
			}
			echo "<tr>";
				echo "<td colspan='2'>".strtoupper($valx['parent_product'])."</td>";
				echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
				echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
				echo "<td align='center'>".$valx['liner']."</td>";
				echo "<td align='center'>".$valx['pressure']."</td>";
				echo "<td align='left'>".spec_fd($valx['id'], 'so_detail_header')."</td>";
				echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
				echo "<td align='center'>".$valx['qty']."</td>";
				echo "<td align='center'>".$unitT."</td>";
				echo "<td align='right'>".number_format($dataSum,2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
			<td align='right'><b><?= number_format($SUM,2);?></b></td>
		</tr>
	</tbody>
	<?php
	$SUM1=0;
	if(!empty($getEngCost)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>ENGINEERING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='7'>Item Product</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Unit</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no1=0;
		$SUM1=0;
		foreach($getEngCost AS $val => $valx){
			$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
			$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
			$SUM1 += $valx['price_total'];
			$no1++;
			echo "<tr>";
				echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
				echo "<td align='center'>".$Qty1."</td>";
				echo "<td align='center'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
			<td align='right'><b><?= number_format($SUM1,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	$SUM2=0;
	if(!empty($getPackCost)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>PACKING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='9'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no2=0;
		$SUM2=0;
		foreach($getPackCost AS $val => $valx){
			$no2++;
			$SUM2 += $valx['price_total'];
			echo "<tr>";
				echo "<td colspan='9'>".strtoupper($valx['name']);
				echo "</td>";
				echo "<td align='center'>".strtoupper($valx['option_type']);
				echo "</td>";
				echo "<td align='right'>".number_format($valx['price_total'],2);
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL PACKING COST</b></td>
			<td align='right'><b><?= number_format($SUM2,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	$SUM3=0;
	if(!empty($getTruck)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>TRUCKING EXPORT</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='6'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Fumigation</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no3=0;
		$SUM3=0;
		foreach($getTruck AS $val => $valx){
			$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$SUM3 += $valx['price_total'];
			$no3++;
			echo "<tr>";
				echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
				echo "</td>";
				echo "<td align='center'>".strtoupper($valx['type'])."</td>";
				echo "<td align='center'>".$Qty3."</td>";
				echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
				echo "<td align='right'>".number_format($valx['price'],2)."</td>";
				echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
			<td align='right'><b><?= number_format($SUM3,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	$SUM4=0;
	if(!empty($getVia)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='11'><b>TRUCKING LOKAL</b></td>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center">Item Product</th>
			<th class="text-center" colspan='3'>Area</th>
			<th class="text-center" colspan='2'>Tujuan</th>
			<th class="text-center" colspan='2'>Kendaraan</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no4=0;
		$SUM4=0;
		foreach($getVia AS $val => $valx){
			$SUM4 += $valx['price_total'];
			$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
			$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
			if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
				$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
			}
			else{
				$Kendaraanx = strtoupper($valx['kendaraan']);
			}
			
			$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
			
			$no4++;
			echo "<tr>";
				echo "<td>".strtoupper($valx['caregory_sub'])."</td>";
				echo "<td align='left' colspan='3'>".$Areax."</td>";
				echo "<td align='left' colspan='2'>".$Tujuanx."</td>";
				echo "<td align='left' colspan='2'>".$Kendaraanx."</td>";
				echo "<td align='center'>".$Qty4."</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
			<td align='right'><b><?= number_format($SUM4,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	?>
	<tfoot>
		<tr class='HeaderHr'>
			<th align='left' colspan='10'>TOTAL</th>
			<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1, 2);?></th>
		</tr>
	</tfoot>
</table>

<style>
.HeaderHr{
	background-color: #ce4c00;
    color: white;
}

.bg-bluexyz{
	background-color: #05b3a3 !important;
	color : white;
}

</style>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>