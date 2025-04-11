
<div class="box-body">
	<?php 
	$GET_NM_BARANG = get_detail_consumable();
	if($tanda != 'request' AND $tanda != 'outgoing_rutin'){?>
		<table id="my-grid" class="table" width="100%">
			<thead>
				<tr>
					<td class="text-left" style='vertical-align:middle;' width='15%'>No PO</td>
					<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?=$no_po;?></td>
				</tr>
				<tr>
					<td class="text-left" style='vertical-align:middle;' width='15%'>No ROS</td>
					<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?=$no_ros;?></td>
				</tr>
				<tr>
					<td class="text-left" style='vertical-align:middle;'>No Transaksi</td>
					<td class="text-left" style='vertical-align:middle;'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?=$dated;?></td>
				</tr>
				<tr>
					<td class="text-left" style='vertical-align:middle;'>Tanggal Penerimaan</td>
					<td class="text-left" style='vertical-align:middle;'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
				</tr>
			</thead>
		</table><br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Code</th>
					<th class="text-center" style='vertical-align:middle;'>Name Barang</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Qty PO</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Diterima</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Kurang</th> 
					<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
				</tr>
			</thead>
			<tbody>
				<?php
				$No=0;
				foreach($result AS $val => $valx){
					if($valx['check_qty_oke'] > 0){
						$No++;
						
						$qty_oke 		= number_format($valx['qty_oke'],2);
						$qty_rusak 		= number_format($valx['qty_rusak'],2);
						$keterangan 	= (!empty($valx['keterangan']))?ucfirst($valx['keterangan']):'-';
						$qty_kurang 	= number_format($valx['qty_order'] - $valx['qty_oke'],2);
						if($tanda == 'check' AND $checked == 'Y'){
							$qty_oke 		= number_format($valx['check_qty_oke'],2);
							$qty_rusak 		= number_format($valx['check_qty_rusak'],2);
							$keterangan 	= (!empty($valx['check_keterangan']))?ucfirst($valx['check_keterangan']):'-';
							$qty_kurang 	= number_format($valx['qty_order'] - $valx['check_qty_oke'],2);
						}

						$nama_barang = (!empty($GET_NM_BARANG[$valx['id_material']]['nm_barang']))?$GET_NM_BARANG[$valx['id_material']]['nm_barang']:'-';
						
						echo "<tr>";
							echo "<td align='center'>".$No."</td>";
							echo "<td align='center'>".$valx['id_material']."</td>";
							echo "<td>".strtoupper($nama_barang)."</td>";
							echo "<td align='right'>".number_format($valx['qty_order'],2)."</td>";
							echo "<td align='right'>".$qty_oke."</td>";
							echo "<td align='right'>".$qty_kurang."</td>";
							echo "<td>".$keterangan."</td>";
						echo "</tr>";
					}
				}
				?>
			</tbody>
		</table>
	<?php } ?>
	
	<?php if($tanda == 'outgoing_rutin'){?>
		<table id="my-grid" class="table" width="100%">
			<thead>
				<tr>
					<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
					<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?= $result_header[0]->kode_trans;?></td>
				</tr>
				<tr>
					<td class="text-left" style='vertical-align:middle;'>Costcenter</td>
					<td class="text-left" style='vertical-align:middle;'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?= strtoupper($result_header[0]->kd_gudang_ke);?></td>
				</tr>
				<tr>
					<td class="text-left" style='vertical-align:middle;'>Date Outgoing</td>
					<td class="text-left" style='vertical-align:middle;'>:</td>
					<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
				</tr>
				<?php
				if($result_header[0]->id_gudang_ke == '17'){
					$PROJECT = (!empty($result_header[0]->no_so) AND !empty($GET_SO[$result_header[0]->no_so]['nm_project']))?' - '.strtoupper($GET_SO[$result_header[0]->no_so]['nm_project']):'';
					?>
					<tr>
						<td class="text-left" style='vertical-align:middle;'>Project</td>
						<td class="text-left" style='vertical-align:middle;'>:</td>
						<td class="text-left" style='vertical-align:middle;'><?= strtoupper($result_header[0]->no_so).$PROJECT;?></td>
					</tr>
					<?php
				}
				?>
			</thead>
		</table><br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
					<th class="text-center" style='vertical-align:middle;' width='20%'>Category</th>
					<th class="text-center" style='vertical-align:middle;'>Name Barang</th>
					<th class="text-center" style='vertical-align:middle;' width='25%'>Spesifikasi</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
				</tr>
			</thead>
			<tbody>
				<?php
				$No=0;
				foreach($result AS $val => $valx){
					$No++;
					
					$qty_oke 		= number_format($valx['qty_oke'],2);
					$keterangan 	= (!empty($valx['keterangan']))?ucfirst($valx['keterangan']):'-';
					$nama_barang = (!empty($GET_NM_BARANG[$valx['id_material']]['nm_barang']))?$GET_NM_BARANG[$valx['id_material']]['nm_barang']:'-';
					echo "<tr>";
						echo "<td align='center'>".$No."</td>";
						echo "<td>".strtoupper(get_name('con_nonmat_category_awal','category','id',$valx['id_category']))."</td>";
						echo "<td>".strtoupper($nama_barang)."</td>";
						echo "<td>".strtoupper(get_name('con_nonmat_new','spec','code_group',$valx['id_material']))."</td>";
						echo "<td align='center'>".$qty_oke."</td>";
						echo "<td>".$keterangan."</td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
	<?php } ?>
</div>
<script>
	swal.close();
</script>