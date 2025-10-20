

<div class="box box-primary">
    <div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='4%'>#</th>
					<th class="text-left">Nama Barang</th>
					<th class="text-left">Category</th>
					<th class="text-left">Brand</th>
					<th class="text-center">Keb.1 Bln</th>
					<th class="text-center">Max Stock</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center">Unit</th>
					<th class="text-center">Dibutuhkan</th>
					<th class="text-center">Spec PR</th>
					<th class="text-center">Info PR</th>
					<th class="text-center">Status</th>
					<th class="text-right" width='7%'>Price From Supplier</th>
					<th class="text-right" width='7%'>Total Budget</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				$SUM_BUDGET = 0;
				foreach($result AS $val => $valx){ $no++;
					$SPEC 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['spec']))?$GET_COMSUMABLE[$valx['id_material']]['spec']:'';
					$BRAND 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['brand']))?$GET_COMSUMABLE[$valx['id_material']]['brand']:'';
					$CATEGORY 	= get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal']);
					$SATUAN 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);

					$kebutuhnMonth 	= (!empty($GET_KEBUTUHAN_PER_MONTH[$valx['id_material']]['kebutuhan']))?$GET_KEBUTUHAN_PER_MONTH[$valx['id_material']]['kebutuhan']:0;
					$maxStock 		= $kebutuhnMonth * 1.5;
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material'].$SPEC."</td>";
						echo "<td align='left'>".strtoupper($CATEGORY)."</td>";
						echo "<td align='left'>".strtoupper($BRAND)."</td>";
						echo "<td align='center'>".number_format($kebutuhnMonth)."</td>";
						echo "<td align='center'>".number_format($maxStock)."</td>";
						echo "<td align='center'>".number_format($valx['purchase'],2)."</td>";
						echo "<td align='center'>".strtoupper($SATUAN)."</td>";
						echo "<td align='center'>".date('d-M-Y', strtotime($valx['tanggal']))."</td>";
						echo "<td align='left'>".strtoupper($valx['spec_pr'])."</td>";
						echo "<td align='left'>".strtoupper($valx['info_pr'])."</td>";
						
						if($valx['sts_app'] == 'N'){
							$sts_name = 'Waiting Approval';
							$warna	= 'blue';
						}
						elseif($valx['sts_app'] == 'Y'){
							$sts_name = 'Approved';
							$warna	= 'green';
						}
						elseif($valx['sts_app'] == 'D'){
							$sts_name = 'Rejected';
							$warna	= 'red';
						}
						
						echo "<td align='center'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
						$price_from_supplier = (!empty($valx['price_from_supplier']))?$valx['price_from_supplier']:0;
						$total_budget = $price_from_supplier * $valx['purchase'];
						echo "<td align='right'>".number_format($price_from_supplier)."</td>";
						echo "<td align='right' class='cal_tot_budget'>".number_format($total_budget,2)."</td>";
					echo "</tr>";

					$SUM_BUDGET += $total_budget;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th align='center'></th>
					<th align='center' colspan='11'>TOTAL BUDGET</th>
					<th class='text-right'></th>
					<th class='text-right' id='cal_tot_budget'><?=number_format($SUM_BUDGET,2);?></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script>
    swal.close();
</script>