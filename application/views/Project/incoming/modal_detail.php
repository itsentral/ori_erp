
<div class="box-body">
	<table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No PO</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Penerimaan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No ROS</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_ros;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Surat Jalan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_surat_jalan;?></td>
			</tr>
		</thead>
	</table><br>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Name Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='25%'>Spec</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Brand</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Qty Diterima</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Keterangan</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				
				$qty_oke 		= number_format($valx['qty_oke'],2);
				$keterangan 	= (!empty($valx['keterangan']))?ucfirst($valx['keterangan']):'-';
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td>".$valx['id_category']."</td>";
					echo "<td>".$valx['nm_category']."</td>";
					echo "<td align='right'>".$qty_oke."</td>";
					echo "<td>".$keterangan."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close();
</script>