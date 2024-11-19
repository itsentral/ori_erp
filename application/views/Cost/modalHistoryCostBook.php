
<div class="box-body"> 
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='10%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Nama Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Price Book</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Updated Date</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($detail)){
                $No=0;
			foreach($detail AS $val => $valx){
                $No++;
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['material_name']."</td>";
                    echo "<td align='right'>".number_format($valx['price_book'],2)."</td>";
                    echo "<td align='center'>".date('d-M-Y H:i:s',strtotime($valx['updated_date']))."</td>";
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
	</table>
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});

</script>