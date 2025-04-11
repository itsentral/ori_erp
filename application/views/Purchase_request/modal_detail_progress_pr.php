

<div class="box box-primary">
    <div class="box-body">
        <br>
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-left" colspan='9'>MATERIAL</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Re-Order Point</th>
					<th class="text-center" width='10%'>MOQ</th>
					<th class="text-center" width='10%'>Qty Request</th>
					<th class="text-center" width='10%'>Qty Revisi</th>
					<th class="text-center" width='10%'>Tanggal Dibutuhkan</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='10%'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($result)){
					$no  = 0;
					$SUM_REQ = 0;
					$SUM_REV = 0;
					foreach($result AS $val => $valx){ $no++;
						$bookpermonth 	= number_format($valx['book_per_month']);
						$leadtime 		= number_format(get_max_field('raw_material_supplier', 'lead_time_order', 'id_material', $valx['id_material']));
						$safetystock 	= number_format(get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']));
						$reorder 		= ($bookpermonth*($safetystock/30))+($leadtime*($bookpermonth/30));
						$sisa_avl 		= $valx['qty_stock'] - $valx['qty_booking'];
						
						$SUM_REQ += $valx['qty_request'];
						$SUM_REV += $valx['qty_revisi'];
						echo "<tr>";
							echo "<td align='center'>".$no."</td>";
							echo "<td align='left'>".$valx['nm_material']."</td>";
							echo "<td align='right'>".number_format($reorder,2)."</td>";
							echo "<td align='right'>".number_format($valx['moq'],2)."</td>";
							echo "<td align='right'>".number_format($valx['qty_request'],2)."</td>";
							echo "<td align='right'>".number_format($valx['qty_revisi'],2)."</td>";
							$TANGGAL_DIBUTUHKAN = (!empty($valx['tanggal'])AND $valx['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($valx['tanggal'])):'';
							echo "<td align='center'>".$TANGGAL_DIBUTUHKAN."</td>";
							echo "<td align='left'>".ucfirst(strtolower($valx['keterangan']))."</td>";
							if($valx['sts_ajuan'] == 'REJ'){
								$sts_name = 'PR Rejected';
								$warna	= 'red';
							}
							else{
								if($valx['qty_request'] == $valx['qty_revisi']){
									$sts_name = 'PR Approved';
									$warna	= 'green';
									if(!empty($valx['no_po'])){
										$sts_name = 'PR Approved, by '.$valx['no_po'];
										$warna	= 'green';
									}
									
								}
								elseif($valx['qty_request'] <> $valx['qty_revisi']){
									$sts_name = 'PR Approved Rev Qty';
									$warna	= 'blue';
									if(!empty($valx['no_po'])){
										$sts_name = 'PR Approved Rev Qty, by '.$valx['no_po'];
										$warna	= 'blue';
									}
								}
							}
							echo "<td align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
							
						echo "</tr>";
					}
					?>
					<tr>
						<td></td>
						<td colspan='3'><b>TOTAL MATERIAL</b></td>
						<td align='right'><b><?=number_format($SUM_REQ,2);?></b></td>
						<td align='right'><b><?=number_format($SUM_REV,2);?></b></td>
						<td colspan='3'></td>
					</tr>
					<?php
				}
				else{
					echo "<tr><td colspan='9'>Data not found</td></tr>";
				}
				?>
			</tbody>
		</table>
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-left" colspan='8'>NON FRP</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Qty Request</th>
					<th class="text-center" width='10%'>Qty Revisi</th>
					<th class="text-center" width='10%'>Unit</th>
					<th class="text-center" width='10%'>Tanggal Dibutuhkan</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='10%'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($non_frp)){
					$no  = 0;
					foreach($non_frp AS $val => $valx){ $no++;
					
						$satuan = $valx['satuan'];
						if($valx['idmaterial'] == '2'){
							$satuan = '1';
						}
						$satx = get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan);
						
						$nm_acc = get_name_acc($valx['id_material']);
						if(empty($valx['idmaterial'])){
							$satx = '-';
							$nm_acc = strtoupper($valx['nm_material']);
						}
						
						echo "<tr>";
							echo "<td align='center'>".$no."</td>";
							echo "<td align='left'>".$nm_acc."</td>";
							echo "<td align='right'>".number_format($valx['qty_request'],2)."</td>";
							echo "<td align='right'>".number_format($valx['qty_revisi'],2)."</td>";
							echo "<td align='left'>".strtoupper($satx)."</td>";
							$TANGGAL_DIBUTUHKAN = (!empty($valx['tanggal'])AND $valx['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($valx['tanggal'])):'';
							echo "<td align='center'>".$TANGGAL_DIBUTUHKAN."</td>";
							echo "<td align='left'>".ucfirst(strtolower($valx['keterangan']))."</td>";
							if($valx['sts_ajuan'] == 'REJ'){
								$sts_name = 'PR Rejected';
								$warna	= 'red';
							}
							else{
								if($valx['qty_request'] == $valx['qty_revisi']){
									$sts_name = 'PR Approved';
									$warna	= 'green';
									if(!empty($valx['no_po'])){
										$sts_name = 'PR Approved, by '.$valx['no_po'];
										$warna	= 'green';
									}
									
								}
								elseif($valx['qty_request'] <> $valx['qty_revisi']){
									$sts_name = 'PR Approved Rev Qty';
									$warna	= 'blue';
									if(!empty($valx['no_po'])){
										$sts_name = 'PR Approved Rev Qty, by '.$valx['no_po'];
										$warna	= 'blue';
									}
								}
							}
							echo "<td align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
							
						echo "</tr>";
					}
				}
				else{
					echo "<tr><td colspan='8'>Data not found</td></tr>";
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<script>
    swal.close();
</script>