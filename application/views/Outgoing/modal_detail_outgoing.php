
<div class="box-body">
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No SO/Type</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($no_po);?></td>
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
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Outgoing</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Dari Gudang</th>
                <th class="text-center" style='vertical-align:middle;' width='20%'>Tujuan Outgoing</th> 
				<th class="text-center" style='vertical-align:middle;' width='20%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
				if($valx['qty_oke'] > 0){
					$No++;
					$keterangan 	= ucfirst($valx['keterangan']);
					
					echo "<tr>";
						echo "<td align='center'>".$No."</td>";
						echo "<td>".$valx['nm_material']."</td>";
						echo "<td align='right'>".number_format($valx['qty_order'],4)."</td>";
						echo "<td align='right'>".number_format($valx['qty_oke'],4)."</td>";
						echo "<td align='left'>".strtoupper($outgoing_dari)."</td>";
						echo "<td align='left'>".strtoupper($outgoing_ke)."</td>";
						echo "<td>".$keterangan."</td>";
					echo "</tr>";
				}
			}
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close();
</script>