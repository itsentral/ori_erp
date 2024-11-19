
<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Iso Matric</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>No Unit Delivery</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>No Component</th>
				<th class="text-left" style='vertical-align:middle;'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-left" style='vertical-align:middle;' width='15%'>Dimensi</th>
				<th class="text-right" style='vertical-align:middle;' width='7%'>Man Hours</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no=0;
				foreach($result AS $val => $valx){ $no++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";	
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					if($valx['man_hours'] <= 0){
						$bc = 'red';
					}
					if($valx['man_hours'] > 0){
						$bc = 'transparant';
					}
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='center'>".$spaces."".$valx['id_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['sub_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".spec_bq($valx['id'])."</td>";
						// echo "<td align='right' style='background-color:".$bc."'>".$valx['total_time']."</td>";
						echo "<td align='right' style='background-color:".$bc."'>".$valx['man_hours']."</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});
</script>