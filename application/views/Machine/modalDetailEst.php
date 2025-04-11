<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT * FROM bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id_delivery ASC, sub_delivery ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader;
// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box-body">
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left" width='20%'><u>ID BQ</u></td>
						<td class="text-left" width='30%'><?= $row[0]['id_bq']; ?></td>
						<td class="text-left" width='20%'><u>IPP Number</u></td>
						<td class="text-left" width='30%'><?= $row[0]['no_ipp']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Information</u></td>
						<td class="text-left" width='30%'><?= ucfirst(strtolower($row[0]['ket'])); ?></td>
						<td class="text-left" width='20%'><u>Created By</u></td>
						<td class="text-left" width='30%'><?= ucwords(strtolower($row[0]['created_by']))." [".date('d F Y', strtotime($row[0]['created_date']))."]"; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" width='13%'>Product Delivery</th>
				<th class="text-center" width='17%'>Product Category</th>
				<th class="text-center" width='42%'>Spesification</th>
				<th class="text-center" width='5%'>Qty</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($qBQdetailRest AS $val => $valx){
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";	
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					$detailSp = "";
					if($valx['id_category'] == 'pipe'){
						$detailSp = "<u>Diameter:</u> ".$valx['diameter_1']." mm, <u>Length:</u> ".$valx['length']." mm, <u>Thickness:</u> ".$valx['thickness']." mm";
					}
					if($valx['id_category'] == 'pipe slongsong'){
						$detailSp = "<u>Diameter:</u> ".$valx['diameter_1']." mm, <u>Length:</u> ".$valx['length']." mm, <u>Thickness:</u> ".$valx['thickness']." mm";
					}
					$nm_cty	= ucwords(strtolower($valx['id_category']));
					
					$sqlProduct	= "SELECT id_product FROM component_header WHERE parent_product='".$valx['id_category']."' ";
					$restProduct = $this->db->query($sqlProduct)->result_array();
					
					echo "<tr>";
						echo "<td style='vertical-align:middle;'>".$spaces."".$id_delivery."</td>";
						echo "<td style='vertical-align:middle;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td style='vertical-align:middle;' align='left'>".$detailSp."</td>";
						echo "<td style='vertical-align:middle;' align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>