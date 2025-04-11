<?php
$id_delivery	= $this->uri->segment(3);
$sub_delivery	= $this->uri->segment(4);
$id_bq			= $this->uri->segment(5);
$sts_delivery	= $this->uri->segment(6);

$sqlDetEst	= "SELECT * FROM bq_detail_detail WHERE id_bq='".$id_bq."' AND id_delivery='".$id_delivery."' AND sub_delivery='".$sub_delivery."' AND sts_delivery='".$sts_delivery."' ";
$restDetEst	= $this->db->query($sqlDetEst)->result_array();
// echo $sqlDetEst; 



?>

<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
	<thead id='head_table'>
		<tr class='bg-green'>
			<th class="text-center" width='15%'>Product</th>
			<th class="text-center">Spesification</th>
			<th class="text-center" width='10%'>Product To</th>
			<th class="text-center" width='25%'>Product Est</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($restDetEst AS $val => $valx){
				$detailSp = "";
				if($valx['id_category'] == 'pipe'){
					$detailSp = "AAS";
				}
				echo "<tr>";
					echo "<td align='center'>".strtoupper($valx['id_category'])."</td>";
					echo "<td>".$detailSp."</td>";
					echo "<td align='center'><span class='badge bg-blue'>".$valx['product_ke']."</span></td>";
					echo "<td></td>";
				echo "</tr>";
			}
		?>
	</tbody>
</table>