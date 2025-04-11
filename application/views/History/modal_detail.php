
<div class="box-body">
	<!-- <table id="my-grid" class="table" width="100%">
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
		</thead>
	</table><br> -->
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>NO PR</th>
				<th class="text-center" style='vertical-align:middle;'>Nama Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Unit Price</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Total Price</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $SUM_QTY = 0;
            $SUM_NILAI = 0;
			$nomor = 0;
			foreach($result AS $val => $valx){ 
				if($valx['qty_po'] > 0){
					$nomor++;
					$SUM_QTY += $valx['qty_po'];
					$SUM_NILAI += $valx['total_price'];
					echo "<tr>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td align='center'>".$valx['no_pr']."</td>";
						echo "<td>".strtoupper($valx['nm_barang'])."</td>";
						echo "<td align='right'>".number_format($valx['qty_po'])."</td>";
						echo "<td align='right'>".number_format($valx['unit_price'])."</td>";
						echo "<td align='right'>".number_format($valx['total_price'])."</td>";
					echo "</tr>";
				}
			}
            echo "<tr>";
                echo "<th align='center' colspan='3'>TOTAL</th>";
                echo "<td align='right'>".number_format($SUM_QTY)."</td>";
                echo "<td align='right'></td>";
                echo "<td align='right'>".number_format($SUM_NILAI)."</td>";
            echo "</tr>";
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close();
</script>