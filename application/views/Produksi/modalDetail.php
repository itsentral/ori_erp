<div class="box box-primary">
	<div class="box-body">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Product</th>
					<th class="text-center" width='25%'>Spec</th>
					<th class="text-center" width='8%'>Qty</th>
					<th class="text-center" width='15%'>Status SPK</th>
					<th class="text-center" width='15%'>Status SPK Mixing</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if(!empty($result_data)){
						foreach($result_data AS $key => $value){
							$key++;

							echo "<tr>";
								echo "<td align='center'>".$key."</td>";
								echo "<td>".strtoupper($value['product'])."</td>";
								echo "<td>".strtoupper($value['spec'])."</td>";
                                echo "<td align='center'>".strtoupper($value['qty'])."</td>";
                                echo "<td align='center'>".strtoupper($value['spk1'])."</td>";
                                echo "<td align='center'>".strtoupper($value['spk2'])."</td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>

<script>
swal.close();
</script>
