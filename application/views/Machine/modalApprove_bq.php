<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT * FROM bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader;
// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box-body">
<!--
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
	-->
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='10%' rowspan='2'>Iso Matric</th>
				<th class="text-center" style='vertical-align:middle;' width='15%' rowspan='2'>No Unit Delivery</th>
				<th class="text-center" style='vertical-align:middle;' width='20%' rowspan='2'>No Component</th>
				<th class="text-center" style='vertical-align:middle;' width='20%' rowspan='2'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='5%' rowspan='2'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='10%' colspan='3'>Dimensi (mm)</th>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center" width='10%'>Diameter</th>
				<th class="text-center" width='10%'>Length</th>
				<th class="text-center" width='10%'>Thickness</th>
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
					echo "<tr>";
						echo "<td align='center'>".$spaces."".$valx['id_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['sub_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
							if($valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong'){$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2']);}
							else{$dim = floatval($valx['diameter_1']);} 
						echo "<td align='center'>".$dim."</td>";
							if($valx['id_category'] == 'pipe'){$length = floatval($valx['length']);}
							elseif($valx['id_category'] == 'elbow mould'){$length = $valx['type']." ".$valx['sudut'];}
							elseif($valx['id_category'] == 'reducer tee mould'){$length = $valx['diameter_1']." x ".$valx['diameter_2'];}
							else{$length = '-';}
						echo "<td align='center'>".$length."</td>";
						echo "<td align='center'>".floatval($valx['thickness'])."</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>