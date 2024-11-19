<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM draf_bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT * FROM draf_bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
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
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Iso Matric</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>No Unit Delivery</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>No Component</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Dimensi</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$No = 0;
				foreach($qBQdetailRest AS $val => $valx){
					$No++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";	
					$plusStyle = "";
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
						$plusStyle	= "style='background-color: khaki;'";
					}
					echo "<tr ".$plusStyle.">";
						echo "<td align='center'>".$No."</td>";
						echo "<td align='center'>".$spaces."".$valx['id_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['sub_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
							if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".$valx['sudut'];
							}
							elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong' OR $valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
							}
							else{$dim = "belum di set";} 
						echo "<td align='left' style='padding-left:20px;'>".spec_draf($valx['id'])."</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>