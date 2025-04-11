
<div class="box-body"> 
	<?php if(!empty($result)){ ?>
	<a href="<?php echo site_url('cost_control/print_total_material/'.$id_bq) ?>" target='_blank' class="btn btn-sm btn-primary" id='btn-add' style='float:right;'>
	<i class="fa fa-print"></i> Print Total Material
	</a>
	<?php } ?> 
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Category</th>
				<th class="text-center" style='vertical-align:middle;' width='30%'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Est Material</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Est Cost</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Real Material</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Real Harga</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Selisih</th>   
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
			$Total1 = 0;
			$Total2 = 0;
			$Total3 = 0;
			$Total4 = 0;
			foreach($result AS $val => $valx){
				$Total1 += $valx['est_material'];
				$Total2 += $valx['est_harga'];
				$Total3 += $valx['real_material'];
				$Total4 += $valx['real_harga'];
				
				if($Total4 == 0 OR $Total2 == 0){
					$hasilx = 0;
				}
				else{
					$hasilx = (($Total4/$Total2)*100);
				}
				
				if($valx['real_harga'] == 0 OR $valx['est_harga'] == 0){
					$hasilxx = 0;
				}
				else{
					$hasilxx = (($valx['real_harga']/$valx['est_harga'])*100);
				}
				
				echo "<tr>";
					echo "<td>".$valx['nm_category']."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['est_material'],5)." Kg</td>";
					echo "<td align='right'>".number_format($valx['est_harga'],2)."</td>";
					echo "<td align='right'>".number_format($valx['real_material'],5)." Kg</td>";
					echo "<td align='right'>".number_format($valx['real_harga'],2)."</td>";
					echo "<td align='right'>".number_format($hasilxx)." %</td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td colspan='2'><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 5);?> Kg</b></td> 
				<td align='right'><b><?= number_format($Total2, 2);?></b></td>
				<td align='right'><b><?= number_format($Total3, 5);?> Kg</b></td>
				<td align='right'><b><?= number_format($Total4, 2);?></b></td>
				<td align='right'><b><?= number_format($hasilx);?> %</b></td>  
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='7'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
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