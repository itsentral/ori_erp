<?php
date_default_timezone_set("Asia/Bangkok");
$tglprint2 = date("d-m-Y");
$date = tgl_indo($total->tgl_invoice);//date('d-m-Y');
$invoice  = $total->no_invoice;
$so  = $total->so_number;
$total2  = $total->total_invoice;
$nm_customer  = $total->nm_customer;
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
    @font-face { font-family: kitfont; src: url('1979 Dot Matrix Regular.TTF'); }
      html
        {
            margin:0;
            padding:0;
            font-style: kitfont;
            font-family:Arial;
            font-size:9pt;
			font-weignt:bold;
            color:#000;
        }
        body
        {
            width:100%;
            font-family:Arial;
            font-style: kitfont;
            font-size:9pt;
			font-weight:bold;
            margin:0;
            padding:0;
        }

        p
        {
            margin:0;
            padding:0;
        }

        .page
        {
            width: 210mm;
            height: 145mm;
            page-break-after:always;
        }

        #header-tabel tr {
            padding: 0px;
        }
        #tabel-laporan {
            border-spacing: -1px;
            padding: 0px !important;
        }

        #tabel-laporan th{
            /*
            border-top: solid 1px #000;
            border-bottom: solid 1px #000;
            */
           border : solid 1px #000;
            margin: 0px;
            height: auto;
        }

        #tabel-laporan td{
            border : solid 1px #000;
            margin: 0px;
            height: auto;
        }
        #tabel-laporan {
          border-bottom:1px solid #000 !important;
        }

        .isi td{
          border-top:0px !important;
          border-bottom:0px !important;
        }
		
		 #grey
        {
             background:#eee;
        }

        #footer
        {
            /*width:180mm;*/
            margin:0 15mm;
            padding-bottom:3mm;
        }
        #footer table
        {
            width:100%;
            border-left: 1px solid #ccc;
            border-top: 1px solid #ccc;

            background:#eee;

            border-spacing:0;
            border-collapse: collapse;
        }
        #footer table td
        {
            width:25%;
            text-align:center;
            font-size:9pt;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }

        img.resize {
          max-width:12%;
          max-height:12%;
        }
		.pagebreak 
		{
		width:100% ;
		page-break-after: always;
		margin-bottom:10px;
		}
    </style>
</head>
<body>
	<table width="100%">
	<tr><td>Kepada Yth</td><td>:</td><td><?=$nm_customer?></td><td>Faktur No.</td><td>:</td><td><?=$total->no_faktur?></td></tr>
	<tr><td>Alamat</td><td>:</td><td><?=$customer->alamat?></td><td>F. Pajak No.</td><td>:</td><td><?=$total->no_pajak?></td></tr>
	</table>
	<table width="100%" id="tabel-laporan" >
		<thead>
     	<tr>
			<td style='height:50px; width:5% !important;' align="center">No</td>
			<td style='height:50px; width:30% !important;' align="center">NAMA BARANG</td>
			<td style='height:50px; width:10% !important;' align="center">QUANTITY</td>
			<td style='height:50px; width:10% !important;' align="center" colspan=2>HARGA SATUAN</td>
			<td style='height:50px; width:14% !important;' align="center" colspan=2>JUMLAH</td>
		</tr>
        </thead>
        <tbody>
		<?php
		if( ! empty($results)){
			$no = 1;
			$page=1;
			foreach($results as $data){
				echo "<tr>";
					echo "<td align='center' valign='top'>".$no."</td>";
					echo "<td valign='top'>".$data->desc."</td>";
					echo "<td valign='top' align='center'>".($data->qty)." ".($data->unit)."</td>";
					echo "<td valign='top' align='center' style='border-color: black white black black;'>IDR</td>";
					echo "<td valign='top' align='right' style='border-color: black black black white;'>".number_format($data->harga_satuan_usd,0)."</td>";
					echo "<td valign='top' align='center' style='border-color: black white black black;'>IDR</td>";
					echo "<td valign='top' align='right' style='border-color: black black black white;'>".number_format($data->harga_total_usd,0)."</td>";
				echo "</tr>";
				$no++;
		    }	
		}
		
		echo "<tr>";
			echo "<td colspan='2'></td>";
            echo "<td colspan='3' align=center>";
				if($total->ppn_persen > 0){echo 'VAT<br>';}
				if($total->total_invoice_usd > 0){echo 'TOTAL<br>';}
            echo "</td>";
			echo "<td align='center' style='border-color: black white black black;'>";
				if($total->ppn_persen > 0){echo 'IDR<br>';}
				if($total->total_invoice_usd > 0){echo 'IDR<br>';}
            echo "</td>";
            echo "<td align='right' style='border-color: black black black white;'>";
				if($total->ppn_persen > 0){echo number_format($total->total_ppn_idr).'<br>';}
				if($total->total_invoice_usd > 0){echo number_format($total->total_invoice_usd).'<br>';}
			echo "</td>";
        echo "</tr>";
		?>
    </table>
     <hr>
        <?php
		echo '
        <table border="0" style="border-spacing:-1px;width:100%">
          <tr>
            <td id="grey" style="text-align:left;border:1px solid #000;border-spacing:1px !important">
              <i>Terbilang : <strong>#'.ucwords(ynz_terbilang_format(@$total->total_invoice_usd)).'&nbsp;Rupiah#</strong></i>
            </td>
          </tr>
        </table>

       <br>
	   <br>

        ';
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
				echo "<td width='12%'></td>";
				echo "<td width='13%' style='vertical-align:top;'>";
					echo "<table class='sign' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='40%'>Bekasi, ".date("d-M-Y",strtotime($total->tgl_invoice))."</td>";
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
    
   
</body>
</html>
