
<div class="box-body">
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Est Material</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Unit</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($data_result)){
        $Total1 = 0;
        $No=0;
				foreach($data_result AS $val => $valx){
          $No++;
					$Total1 += $valx['last_cost'];

					echo "<tr>";
						echo "<td align='center'>".$No."</td>";
						echo "<td>".strtoupper($valx['nm_material'])."</td>";
						echo "<td align='right'>".number_format($valx['last_cost'],3)."</td>";
						echo "<td align='left'>KG</td>";
					echo "</tr>";
				}
				?>
				<tr>
					<td><b></b></td>
					<td><b>SUM TOTAL</b></td>
					<td align='right'><b><?= number_format($Total1, 3);?></b></td>
					<td align='left'>KG</td>
				</tr>
				<?php
			}
			else{
				echo "<tr>";
					echo "<td colspan='4'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
		<thead id='head_table'>
			<tr>
				<td class='bg-blue' colspan='4'><b>BQ NON FRP</b></td>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($non_frp)){
            $Total1 = 0;
            $No=0;
			foreach($non_frp AS $val => $valx){
                $No++;
					
				$qty = $valx['qty'];
				if($valx['type'] == 'tanki'){
					$SPEC = get_name_acc_tanki($valx['id_material']);
				}
				if($valx['type'] == 'pipe'){
					$SPEC = get_name_acc($valx['id_material']);
				}
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$SPEC."</td>";
                    echo "<td align='right'>".number_format($qty,2)."</td>";
					// echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
					echo "<td align='left'></td>";
				echo "</tr>";
			}
			?>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='4'>Data tidak ada</td>";
				echo "</tr>";
			}
			?>
		</tbody>
		<thead id='head_table'>
			<tr>
				<td class='bg-blue' colspan='4'><b>MATERIAL</b></td>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($material)){
            $Total1 = 0;
            $No=0;
			foreach($material AS $val => $valx){
                $No++;
                $Total1 += $valx['qty'];
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
                    echo "<td align='right'>".number_format($valx['qty'],3)."</td>";
					echo "<td align='left'>KG</td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 3);?></b></td>
				<td align='left'>KG</td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='4'>Data tidak ada</td>";
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
