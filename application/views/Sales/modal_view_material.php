
<div class="box-body"> 
	<div class="table-responsive">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		
		<thead id='head_table'>
			<tr>
				<td class='bg-blue' colspan='4'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Est Material</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Unit</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($detail)){
            $Total1 = 0;
            $No=0;
			foreach($detail AS $val => $valx){
                $No++;
                $Total1 += $valx['last_cost_qty'];
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
                    echo "<td align='right'>".number_format($valx['last_cost_qty'],3)."</td>";
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
				
				$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
				$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
				$nama_acc = "";
				if($valx['category'] == 'baut'){
					$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
				}
				if($valx['category'] == 'plate'){
					$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
				}
				if($valx['category'] == 'gasket'){
					$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
				}
				if($valx['category'] == 'lainnya'){
					$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
				}
					
				$qty = $valx['qty'];
				$satuan = $valx['option_type'];
				if($valx['category'] == 'plate'){
					$qty = $valx['weight'];
					$satuan = '1';
				}
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$nama_acc."</td>";
                    echo "<td align='right'>".number_format($qty,2)."</td>";
					echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
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
					echo "<td>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
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
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});

</script>