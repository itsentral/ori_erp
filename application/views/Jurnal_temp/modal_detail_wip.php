
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
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Costbook</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Total Nilai</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $SUM_QTY = 0;
            $SUM_NILAI = 0;
			$nomor = 0;
			foreach($result AS $val => $valx){ 
				if($valx['qty'] > 0){
					$nomor++;
					$SUM_QTY += $valx['qty'];
					$SUM_NILAI += $valx['total_nilai'];
					echo "<tr>";
						echo "<td align='center'>".$nomor."</td>";
						echo "<td>".$valx['nm_material']."</td>";
						echo "<td align='right'>".number_format($valx['qty'],4)."</td>";
						echo "<td align='right'>".number_format($valx['cost_book'],2)."</td>";
						echo "<td align='right'>".number_format($valx['total_nilai'],2)."</td>";
					echo "</tr>";
				}
			}
            echo "<tr>";
                echo "<th align='center' colspan='2'>TOTAL</th>";
                echo "<td align='right'>".number_format($SUM_QTY,4)."</td>";
                echo "<td align='right'></td>";
                echo "<td align='right'>".number_format($SUM_NILAI,2)."</td>";
            echo "</tr>";
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close();
</script>