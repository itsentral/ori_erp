<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='10%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Est Material</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($detail)){
            $Total1 = 0;
            $Total2 = 0;
            $No=0;
			foreach($detail AS $val => $valx){
                $No++;
                $Total1 += $valx['last_cost_qty'];
                $Total2 += $valx['last_cost_qty'] * $valx['cost_est'];
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
                    echo "<td align='right'>".number_format($valx['last_cost_qty'],3)." Kg</td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 3);?> Kg</b></td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='3'>Data tidak ada</td>";
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