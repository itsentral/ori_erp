
<div class="box-body"> 
	<!-- <?php if($qBQdetailNum != 0){ ?>
	<a href="<?php echo site_url('cron/excel_material/'.$id_bq) ?>" target='_blank' class="btn btn-sm btn-success" id='btn-add' style='float:right;'>
	<i class="fa fa-file-excel-o">&nbsp;Excel Material Planning</i>
	</a>
	<?php } ?>  -->
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='10%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Est Material</th>
                <th class="text-center" style='vertical-align:middle;' width='11%'>Price /kg</th> 
                <th class="text-center" style='vertical-align:middle;' width='11%'>Total Price</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if($detail_num != 0){
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
                    echo "<td align='right'>".number_format($valx['cost_est'],2)."</td>";
                    echo "<td align='right'>".number_format($valx['cost_est'] * $valx['last_cost_qty'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 3);?> Kg</b></td>
                <td align='right'></td>
                <td align='right'><b><?= number_format($Total2, 2);?></b></td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='5'>Data tidak ada</td>";
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