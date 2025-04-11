

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
						echo "<td align='right'>".number_format($valx['qty_request'])."</td>";
						echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
						
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
				?>
			</tbody>
		</table>
	</div>
</div>

<script>
    swal.close();
</script>