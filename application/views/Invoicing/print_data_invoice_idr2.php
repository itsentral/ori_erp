<?php
date_default_timezone_set("Asia/Bangkok");
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
		
		.rupiah_kanan{
			text-align: right;
		}
    </style>
</head>
<body>
	<table valign="top" width="100%" id="tabel-laporan" style="!important; padding: 0 !important;">
		<thead>
			<tr id="grey" height="100px">
				<th width="5%" align="center">NO</th>
				<th width="36%" align="center">NAMA BARANG</th>
				<th width="10%" align="right">QTY</th>
				<th width="22%" align="right">HARGA SATUAN(IDR)</th>
				<th width="22%" align="right">JUMLAH(IDR)</th>
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
				$jenis_invoice=$val->jenis_invoice;
			}

			if(!empty($results)){
				$no = 1;
				$page=1;
				$rp ="IDR";
				foreach($results as $data){
					if($data->harga_total_idr > 0){
						$QTY = ($data->qty > 0)?number_format($data->qty):'';
						$SAT_IDR = ($data->harga_satuan_idr > 0)?number_format($data->harga_satuan_idr):'';
						if($jenis_invoice!='uang muka'){
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
			}
			else{
				echo "<tr><td colspan='5'>Data tidak ada .</td></tr>";
			}
		?>
    </table>
	
     <hr>
        <?php
		echo "<table border='0' style='border-spacing:-1px;width:100%'>";
			echo "<tr>";
				echo "<td id='grey' colspan='2' style='text-align:left;border:1px solid #000;border-spacing:1px !important'>";
					echo "<table class='terbilang' border='0' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td ><i>Terbilang : <strong>#".ucwords(ynz_terbilang_format(@$val->total_invoice_idr))."&nbsp;Rupiah#</strong></i></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "<td width='2%'></td>";
				echo "<td width='35%' style='vertical-align:top;'>";
					echo "<table class='total' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='41%'>TOTAL</td>";
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
				echo "<td width='70%' style='vertical-align:top;'>";
					echo "<table class='catatan' border='0' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td><b>CATATAN :</b><br>
										 Pembayaran dengan Cheque/Giro dianggap sah, setelah Cheque/Giro dapat diuangkan(Clearing).<br>
										 Pembayaran harap di transfer full amount ke:<br>
										 <b>PT ORI POLYTEC COMPOSITES<br>
										 BCA WISMA ASIA USD 084.056.0333 IDR 084.056.1313</b><br>
										 * Denda 0,1%/hari, max 5% dihitung sejak tanggal jatuh tempo pembayaran<br>
										 * Untuk tagihan USD yang akan dibayarkan dalam rupiah, harap konfirmasi kurs dengan finance kami</td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "<td width='6%'></td>";
				echo "<td style='vertical-align:top;'>";
					echo "<table class='sign' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='40%'>Jakarta, ".date('d F Y', strtotime($tglprint2))."</td>";
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
		
		
		
		
		
		
		// echo '
        // <table border="1" style="border-spacing:-1px;width:100%">
          // <tr>
            // <td id="grey" colspan="2" style="text-align:left;border:1px solid #000;border-spacing:1px !important; font-size 12px;">
              // <i>Terbilang : <strong>#'.ucwords(ynz_terbilang_format(@$val->total_invoice_idr)).'&nbsp;Rupiah#</strong></i>
            // </td>
			
            // <td rowspan="3" style="text-align:left;vertical-align:top;padding:0 0 0 1%">
              // TOTAL     <br>';
			  
			  // if($val->total_um > 0){
			  // echo '
              // DP I      <br> '; 
			  // }
			  // if($val->total_um2 > 0){
			  // echo '
              // DP II      <br> '; 
			  // }
			  // echo "
              // DISKON        <br>";
			  // if($val->total_retensi_idr > 0){
              // echo "RETENSI <br> ";
			  // }
              // echo "PPN        <br>
              // TOTAL INVOICE<br>


            // </td>
			 // <td rowspan='3' style='text-align:right;vertical-align:top;padding:0 0 0 1%'>
              // :<br>";
			  // if($val->total_um_idr > 0){
			  // echo '
              // :<br>';
			  // }
			  // if($val->total_um_idr2 > 0){
			  // echo '
              // :<br>';
			  // }
			  // echo '
              // :<br>';
			  // if($val->total_retensi_idr > 0){
              // echo ":<br>";
			  // }
              // echo ':<br>
			  // :<br>


            // </td>
            // <td rowspan="3" style="text-align:right;vertical-align:top;padding:0 0 0 1%">
              // ' .number_format(@$val->total_dpp_rp).'<br>';
			  // if($val->total_um_idr > 0){
			  // echo '
              // ' .number_format(@$val->total_um_idr).'<br>';
			  // }
			    // if($val->total_um_idr2 > 0){
			  // echo '
              // ' .number_format(@$val->total_um_idr2).'<br>';
			  // }
			  // echo '
              // ' .number_format(@$val->total_diskon_idr).'<br>
			  
              // ';
			  // if($val->total_retensi_idr > 0){
				  // echo number_format(@$val->total_retensi_idr)."<br>";
              // }
			  
			  // echo number_format(@$val->total_ppn_idr).'<br>
			  // ' .number_format(@$val->total_invoice_idr).'<br>


            // </td>
          // </tr>
          // <tr>
            // <td width="35%">
             
            // </td>
            // <td width="35%">
            
            // </td>

          // </tr>
          // <tr>
            // <td>
            // </td>
            // <td width="30%" style="text-align: center;color:#fff">
            // i<br>
            // i<br>
            // i<br>

              // <!----><img src="assets/img/logo.JPG" style="height: 50px;width: auto;display:none">
            // </td>
          // </tr>
        // </table>

       // <br>
	   // <br>

        // <table>
            // <tr>
              // <td width="70%" style="font-size:8pt;">
              // CATATAN :<br>
                 // Pembayaran dengan Cheque/Giro dianggap sah,<br>
                 // setelah Cheque/Giro dapat diuangkan(Clearing)<br>
                 // Pembayaran harap di transfer full amount ke:<br>
                 // PT ORI POLYTEC COMPOSITES<br>
				 // BCA WISMA ASIA USD 084.056.0333 IDR 084.056.1313<br>
				 // * Denda 0,1%/hari, max 5% dihitung sejak tanggal<br>
				   // &nbsp;&nbsp;&nbsp;jatuh tempo pembayaran<br>
				 // * Untuk tagihan USD yang akan dibayarkan dalam rupiah,<br>
				   // &nbsp;&nbsp;&nbsp;harap konfirmasi kurs dengan finance kami<br>
              
              // </td>
			  // <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  // &nbsp;&nbsp;&nbsp;</td>
			  // <td align="center" style="font-size:8pt; font-weight:bold;">
			  // Jakarta,'.$tglprint2.'<br>
			  // <br> 
			  // <br>
			  // <br>
			  // <br>
			  // <br> 
			  // <br>
			  // <br>
			  // <br>
			  // '.ucwords($user).'<br>
			  // </td>
            // </tr>
        // </table>';
	?>
    
   
</body>
</html>
