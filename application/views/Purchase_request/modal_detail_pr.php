

<div class="box box-primary">
    <div class="box-body">
        <br>
		<div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Asal Permintaan</b></label>
            <div class='col-sm-4'>              
                <?=$no_ipp;?>
            </div>
        </div>
		<div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Kebutuhan</b></label>
            <div class='col-sm-4'>              
                <?=$kebutuhan;?>
            </div>
        </div>
		<br>
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th colspan='7'>MATERIAL</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Category</th>
					<th class="text-center" width='10%'>MOQ</th>
					<th class="text-center" width='10%'>Qty (Kg)</th>
					<th class="text-center" width='10%'>Tanggal Dibutuhkan</th>
					<th class="text-center" width='10%'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				foreach($result AS $val => $valx){ $no++;
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material']."</td>";
						echo "<td align='left'>".get_name('raw_materials', 'nm_category', 'id_material', $valx['id_material'])."</td>";
						echo "<td align='right'>".number_format($valx['moq_m'])."</td>";
						echo "<td align='right'>".number_format($valx['qty_request'],2)."</td>";
						$TANGGAL_DIBUTUHKAN = (!empty($valx['tanggal'])AND $valx['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($valx['tanggal'])):'';
						echo "<td align='center'>".$TANGGAL_DIBUTUHKAN."</td>";
						
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
						
						echo "<td align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
					echo "</tr>";
				}
				if(empty($result)){
					echo "<tr>";
						echo "<td colspan='7'>Tidak ada data.</td>";
					echo "</tr>";
				}
				?>
			</tbody>
			<thead>
				<tr class='bg-blue'>
					<th colspan='7'>NON FRP</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Category</th>
					<th class="text-center" width='10%'>Qty</th>
					<th class="text-center" width='10%'>Unit</th>
					<th class="text-center" width='10%'>Tanggal Dibutuhkan</th>
					<th class="text-center" width='10%'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				foreach($non_frp AS $val => $valx){ $no++;

					$satuan = $valx['satuan'];
					if($valx['idmaterial'] == '2'){
						$satuan = '1';
					}

					$nm_acc = get_name_acc($valx['id_material']);
					if($nm_acc == 'Not found'){
						$nm_acc = strtoupper($valx['nm_material']);
					}
					
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$nm_acc."</td>";
						echo "<td align='left'>".strtoupper(get_name('accessories_category', 'category', 'id', $valx['idmaterial']))."</td>";
						echo "<td align='right'>".number_format($valx['purchase'])."</td>";
						echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
						$TANGGAL_DIBUTUHKAN = (!empty($valx['tanggal'])AND $valx['tanggal'] != '0000-00-00')?date('d-m-Y', strtotime($valx['tanggal'])):'';
						echo "<td align='center'>".$TANGGAL_DIBUTUHKAN."</td>";
						
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
						
						echo "<td align='left'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
					echo "</tr>";
				}
				if(empty($non_frp)){
					echo "<tr>";
						echo "<td colspan='7'>Tidak ada data.</td>";
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