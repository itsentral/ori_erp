<?php
date_default_timezone_set("Asia/Bangkok");
?>

	<table width="100%" class='gridtable'>
		<thead>
			<tr>
				<th width="5%" align="center">NO</th>
				<th width="36%" align="center">NAMA BARANG</th>
				<th width="10%" align="right">QTY</th>
				<th width="22%" align="right">HARGA SATUAN</th>
				<th width="22%" align="right">JUMLAH</th>
			</tr>
		</thead>
		<?php
			$tglprint2 = date("d-m-Y");
			
			foreach($total as $val){
				$date = tgl_indo($val->tgl_invoice);//date('d-m-Y');
				$invoice  = $val->no_invoice;
				$so  = $val->so_number;
				$total2  = $val->total_invoice;
				$customer  = $val->nm_customer;
			}

			if(!empty($results)){
				$no = 1;
				$page=1;
				$rp ="IDR";
				foreach($results as $data){
					if($data->harga_total_idr > 0){
						$QTY = ($data->qty > 0)?number_format($data->qty,2):'';
						$SAT_IDR = ($data->harga_satuan_idr > 0)?number_format($data->harga_satuan_idr):'';
						echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td >".$data->nm_material."</td>";
						echo "<td align='right'>".$QTY."</td>";
						echo "<td align='right'>".$SAT_IDR."</td>";
						echo "<td align='right'>".number_format($data->harga_total_idr)."</td>";
						echo "</tr>";
						$no++;
					}
				}
			}
			else{
				echo "<tr><td colspan='5'>Data tidak ada .</td></tr>";
			}
		?>
    </table>
	
     <br>
        <?php
		echo "<table border='0' width='100%' cellpadding='0'>";
			echo "<tr>";
				echo "<td width='65%' style='vertical-align:top;'>";
					echo "<table class='terbilang' border='0' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td><i>Terbilang : <strong>#".ucwords(ynz_terbilang_format(@$val->total_invoice_idr))."&nbsp;Rupiah#</strong></i></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "<td width='2%'></td>";
				echo "<td width='33%' style='vertical-align:top;'>";
					echo "<table class='total' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='40%'>TOTAL</td>";
							echo "<td width='5%'>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_dpp_rp)."</td>";
						echo "</tr>";
						if($val->total_um_idr > 0){
						echo "<tr>";
							echo "<td>DP I</td>";
							echo "<td>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_um_idr)."</td>";
						echo "</tr>";
						}
						if($val->total_um_idr2 > 0){
						echo "<tr>";
							echo "<td>DP II</td>";
							echo "<td>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_um_idr2)."</td>";
						echo "</tr>";
						}
						if($val->total_diskon_idr > 0){
						echo "<tr>";
							echo "<td>DISKON</td>";
							echo "<td>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_diskon_idr)."</td>";
						echo "</tr>";
						}
						if($val->total_retensi_idr > 0){
						echo "<tr>";
							echo "<td>RETENSI</td>";
							echo "<td>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_retensi_idr)."</td>";
						echo "</tr>";
						}
						echo "<tr>";
							echo "<td>PPN</td>";
							echo "<td>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_ppn_idr)."</td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td>TOTAL INVOICE</td>";
							echo "<td>:</td>";
							echo "<td class='rupiah_kanan'>".number_format(@$val->total_invoice_idr)."</td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
		echo "<br>";
		echo "<table border='0' width='100%' cellpadding='0'>";
			echo "<tr>";
				echo "<td width='75%' style='vertical-align:top;'>";
					echo "<table class='catatan' border='0' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td><b>CATATAN :</b><br>
										 Pembayaran dengan Cheque/Giro dianggap sah, setelah Cheque/Giro dapat diuangkan(Clearing).<br>
										 Pembayaran harap di transfer full amount ke:<br>
										 <b>PT ORI POLYTEC COMPOSITES<br>
										 BCA WISMA ASIA USD 084.056.0333 IDR 084.056.1313<br><br>
										 * Denda 0,1%/hari, max 5% dihitung sejak tanggal jatuh tempo pembayaran<br>
										 * Untuk tagihan USD yang akan dibayarkan dalam rupiah, harap konfirmasi kurs dengan finance kami
										</b>
									</td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "<td width='2%'></td>";
				echo "<td width='23%' style='vertical-align:top;'>";
					echo "<table class='sign' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='40%'>Bekasi, ".date('d F Y', strtotime($tglprint2))."</td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td style='height:70px;'></td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td><b>__________________________</b></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
		
	?>
<style>

	p{
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
	}
	
	.bold{
		font-weight: bold;
	}
	
	.color_req{
		color: #0049a8;
	}
	
	.header_style_company{
		padding: 15px;
		color: black;
		font-size: 20px;
	}
	
	.header_style_alamat{
		padding: 10px;
		color: black;
		font-size: 10px;
	}
	
	.header_style2{
		background-color: #0049a8;
		color: white;
		font-size: 10px;
		padding: 8px;
	}
	
	table.default {
		font-family: Arial, Helvetica, sans-serif;
		font-size:9px;
		padding: 0px;
	}
	
	table.total {
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
		padding: 0px;
		font-weight: bold;
	}
	
	table.terbilang {
		font-family: Arial, Helvetica, sans-serif;
		font-size:12px;
		padding: 0px;
		background:#eee;
		text-align: justify;
	}
	
	table.catatan {
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
		padding: 0px;
		text-align: justify;
	}
	
	table.sign {
		font-family: Arial, Helvetica, sans-serif;
		font-size:12px;
		padding: 0px;
		text-align: center;
	}
	
	#grey
	{
		 background:#eee;
	}
	
	.rupiah_kanan{
		text-align: right;
	}
	
	
	
	
	table.gridtable2 {
		font-family: Arial, Helvetica, sans-serif;
		font-size:9px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable2 th {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #f2f2f2;
	}
	table.gridtable2 th.head {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable2 td {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 3px;
		border-style: none;
		border-color: #666666;
		background-color: #ffffff;
	}
	
	
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border: 1px solid #dddddd;
		border-collapse: collapse;
	}
	table.gridtable th {
		padding: 6px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable th.head {
		padding: 6px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable tr:nth-child(even) {
		background-color: #f2f2f2;
	}
	table.gridtable td {
		padding: 6px;
	}
	table.gridtable td.cols {
		padding: 6px;
	}

</style>