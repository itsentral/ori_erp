<div class="box box-primary">
	<div class="box-body">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Product</th>
					<th class="text-center" width='20%'>Spec</th>
					<th class="text-center" width='10%'>Qty</th>
					<th class="text-center" width='10%'>Print By</th>
					<th class="text-center" width='15%'>Print Date</th>
                    <th class="text-center" width='10%'>Print</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if(!empty($result_data)){
						foreach($result_data AS $key => $value){
							$key++;

							$SPEC = (!empty($tanki_model->get_spec($value['id_milik'])))?$tanki_model->get_spec($value['id_milik']):'';

							echo "<tr>";
								echo "<td align='center'>".$key."</td>";
								echo "<td>".strtoupper($value['product'])."</td>";
								if($value['id_product'] != 'tanki'){
									echo "<td>".spec_bq2($value['id_milik'])."</td>";
								}
								else{
									echo "<td>".$SPEC."</td>";
								}
                                echo "<td align='center'>".$value['qty']."</td>";
                                echo "<td align='center'>".strtolower($value['created_by'])."</td>";
                                echo "<td align='center'>".date('d-M-Y H:i:s',strtotime($value['created_date']))."</td>";
                                echo "<td align='center'><a href='".base_url('produksi/spk_baru/').$value['kode_spk']."' target='_blank'>Print</a></td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='7'>Tidak ada data yang ditampilkan</td>";
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
