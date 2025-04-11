

<div class="box box-primary">
    <div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-left">Nama Barang</th>
					<th class="text-left" width='15%'>Category</th>
					<th class="text-left" width='15%'>Spesifikasi</th>
					<th class="text-center" width='8%'>Qty</th>
					<th class="text-center" width='9%'>Tanggal Dibutuhkan</th>
					<th class="text-center" width='10%'>Spec PR</th>
					<th class="text-center" width='10%'>Info PR</th>
					<th class="text-center" width='10%'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				foreach($result AS $val => $valx){ $no++;
					$SPEC 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['spec']))?$GET_COMSUMABLE[$valx['id_material']]['spec']:'';
					$CATEGORY 	= get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal']);
					$SATUAN 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".strtoupper($valx['nm_material'])."</td>";
						echo "<td align='left'>".strtoupper($CATEGORY)."</td>";
						echo "<td align='left'>".strtoupper($SPEC)."</td>";
						echo "<td align='center'>".number_format($valx['purchase'],2)." ".strtolower($SATUAN)."</td>";
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