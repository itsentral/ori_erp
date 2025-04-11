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

	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Iso Matric</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>No Unit Delivery</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>No Component</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Dimensi</th>
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
						echo "<td align='left' style='padding-left:20px;'>".$dim."</td>";
							
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>