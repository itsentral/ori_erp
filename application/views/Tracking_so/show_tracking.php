<style>
	.tracking-table th {
		text-align: center;
		vertical-align: middle !important;
		font-size: 11px;
	}
	.tracking-table td {
		text-align: center;
		vertical-align: middle !important;
		font-size: 11px;
	}
	.tracking-table .bg-header-main {
		background-color: #3c8dbc;
		color: #fff;
	}
	.tracking-table .bg-header-sub {
		background-color: #4a9fd5;
		color: #fff;
	}
	.val-zero {
		color: #999;
	}
</style>

<table class="table table-bordered table-condensed tracking-table" style="margin-top:15px;">
	<thead>
		<tr class='bg-header-main'>
			<th rowspan="2" style="min-width:100px;">No. PO</th>
			<th rowspan="2" style="min-width:100px;">SO No</th>
			<th rowspan="2" style="min-width:110px;">No SPK</th>
			<th rowspan="2" style="min-width:120px;">Produk</th>
			<th rowspan="2" style="min-width:120px;">Spesifikasi</th>
			<th rowspan="2" style="min-width:50px;">Qty SO</th>
			<th colspan="2">SPK</th>
			<th colspan="3">PRODUKSI</th>
			<th colspan="2">FG</th>
			<th colspan="3">IN TRANSIT</th>
			<th colspan="2">CUSTOMER</th>
			<th colspan="4">Invoice</th>
		</tr>
		<tr class='bg-header-sub'>
			<th>R</th>
			<th>O</th>
			<th>R</th>
			<th>O</th>
			<th>D</th>
			<th>R</th>
			<th>O</th>
			<th>R</th>
			<th>O</th>
			<th style="min-width:130px;">No. Delivery</th>
			<th>R</th>
			<th>O</th>
			<th>R</th>
			<th>O</th>
			<th style="min-width:130px;">No. Invoice</th>
			<th style="min-width:120px;">Nilai</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(!empty($result)){
			foreach($result as $key => $row){
				echo "<tr>";
					// No. PO
					echo "<td align='left'>".strtoupper($row['no_po'])."</td>";
					// SO No
					echo "<td>".$row['so_number']."</td>";
					// No SPK
					echo "<td>".$row['no_spk']."</td>";
					// Produk
					echo "<td align='left'>".$row['product']."</td>";
					// Spesifikasi
					echo "<td align='left'>".$row['spesifikasi']."</td>";
					// Qty SO
					echo "<td><b>".number_format($row['qty_so'])."</b></td>";

					// SPK - R
					$cls = ($row['spk_r'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['spk_r']."</td>";
					// SPK - O
					$cls = ($row['spk_o'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['spk_o']."</td>";

					// PRODUKSI - R
					$cls = ($row['prod_r'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['prod_r']."</td>";
					// PRODUKSI - O
					$cls = ($row['prod_o'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['prod_o']."</td>";
					// PRODUKSI - D (Defect)
					$cls = ($row['prod_d'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['prod_d']."</td>";

					// FG - R
					$cls = ($row['fg_r'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['fg_r']."</td>";
					// FG - O
					$cls = ($row['fg_o'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['fg_o']."</td>";

					// IN TRANSIT - R
					$cls = ($row['transit_r'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['transit_r']."</td>";
					// IN TRANSIT - O
					$cls = ($row['transit_o'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['transit_o']."</td>";
					// IN TRANSIT - No. Delivery
					echo "<td align='left' style='font-size:10px;'>".$row['no_delivery']."</td>";

					// CUSTOMER - R
					$cls = ($row['cust_r'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['cust_r']."</td>";
					// CUSTOMER - O
					$cls = ($row['cust_o'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['cust_o']."</td>";

					// Invoice - R
					$cls = ($row['inv_r'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['inv_r']."</td>";
					// Invoice - O
					$cls = ($row['inv_o'] == 0) ? "val-zero" : "";
					echo "<td class='".$cls."'>".$row['inv_o']."</td>";
					// Invoice - No. Invoice
					echo "<td align='left' style='font-size:10px;'>".$row['no_invoice']."</td>";
					// Invoice - Nilai
					echo "<td align='right'>".$row['inv_nilai']."</td>";
				echo "</tr>";
			}
		} else {
			echo "<tr><td colspan='22' align='center'>Tidak ada data yang ditampilkan.</td></tr>";
		}
		?>
	</tbody>
</table>
