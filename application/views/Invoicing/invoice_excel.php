<?php
date_default_timezone_set("Asia/Bangkok");

foreach($total as $val){
	$date 		= tgl_indo($val->tgl_invoice);//date('d-m-Y');
	$invoice  	= $val->no_invoice;
	$so  		= $val->so_number;
	$total2  	= $val->total_invoice;
	$customer  	= $val->nm_customer;
	$tagih  	= $val->jenis_invoice;
	$persentase = number_format($val->persentase);
	$persen     = '%';

	if($tagih=='uang muka'){
		$jenis_invoice1='DOWN PAYMENT OF ';
		$jenis_invoice=$jenis_invoice1.$persentase.$persen;
	}
	elseif($tagih=='progress'){
		$jenis_invoice1='PROGRESS';
		$jenis_invoice=$jenis_invoice1;
	}
	else{
		$jenis_invoice='RETENSI';
	}

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invoice <?=@$val->no_invoice?></title>
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
<?php
echo '
		<table border="0" width="100%">
		<tr>
			 <td style="width: 18%; font-size:8pt !important;vertical-align:top"><b>Kepada Yth</b></td>
			 <td style="width: 1%; font-size:8pt !important;vertical-align:top"><b>:</b></td>
			 <td style="width: 31%; font-size:8pt !important;vertical-align:top"><b>' .@$val->nm_customer.'</b></td>
			 <td style="width: 18%; font-size:8pt !important;vertical-align:top"><b>Faktur No.</b></td>
			 <td style="width: 1%; font-size:8pt !important;vertical-align:top"><b>:</b></td>
			 <td style="width: 31%; font-size:8pt !important;vertical-align:top"><b>' .@$val->no_faktur.'</b></td>
		</tr>
		<tr>
			 <td style="font-size:8pt !important;vertical-align:top" rowspan="3"><b>Alamat</b></td>
			 <td style="font-size:8pt !important;vertical-align:top" rowspan="3"><b>:</b></td>
			 <td style="font-size:8pt !important;vertical-align:top" rowspan="3"><b>' .@$alamat_cust->alamat.'</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>F. Pajak No.</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>' .@$val->no_pajak.'</b></td>

		</tr>
		<tr>
			 <td style="font-size:8pt !important;vertical-align:top"><b>No PO.</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>' .@$val->no_po.'</b></td>

		</tr>
		<tr>
			 <td style="font-size:8pt !important;vertical-align:top"><b>Payment Term</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>:</b></td>
			 <td style="font-size:8pt !important;vertical-align:top"><b>' .@$val->payment_term.'</b></td>
		</tr>
		</table>
';
?>
		  <table valign="top" width="100%" id="tabel-laporan" style="!important; padding: 0 !important;">
			<tr id="grey" height="100px">
			<b>
			<th width="7%"  align="center" valign="top">NO</th>
			<th width="47%" align="center">NAMA BARANG</th>
			<th width="10%" align="center">QUANTITY</th>
			<th width="18%" align="center" colspan='2'>HARGA SATUAN</th>
			<th width="18%" align="center" colspan='2'>JUMLAH</th>
			</b>
			</tr>
		  <!--</table>
	<table valign="top" width="100%" id="tabel-laporan" style="!important; padding: 0 !important;">-->
		<?php
		$tglprint2 = date("d-m-Y");
		foreach($total as $val){
			$date = $val->tgl_invoice;//date('d-m-Y');
			$invoice  = $val->no_invoice;
			$so  = $val->so_number;
			$total2  = $val->total_invoice;
			$customer  = $val->nm_customer;
	    }
		if( ! empty($results)){
		$no = 1;
		$page=1;
		$rp ="IDR";
		foreach($results as $data){
				if($data->harga_total_idr > 0){
				$QTY = ($data->kategori_detail <> 'PACKING' AND $data->kategori_detail <> 'ENGINERING' AND $data->kategori_detail <> 'TRUCKING')?number_format($data->qty,2):'';
				$HARGA_SAT = ($data->kategori_detail <> 'PACKING' AND $data->kategori_detail <> 'ENGINERING' AND $data->kategori_detail <> 'TRUCKING')?number_format($data->harga_satuan_idr):'';
				$IDR = ($data->kategori_detail <> 'PACKING' AND $data->kategori_detail <> 'ENGINERING' AND $data->kategori_detail <> 'TRUCKING')?'IDR':'';
				echo "<tr>";
				echo "<th align='center' valign='top'>".$no."</th>";
//				echo "<td valign='top' >".str_replace($data->so_number.' / ','',$data->nm_material)."</td>";
				echo "<td valign='top' >".$data->desc."</td>";
				echo "<th valign='top' align='center'>".$QTY."</th>";
				echo "<th valign='top' align='left' style='border-color: black white black black;'>&nbsp;&nbsp;".$IDR."</th>";
				echo "<th valign='top' align='right' style='border-color: black black black white;'>".$HARGA_SAT."</th>";
				echo "<th valign='top' align='left' style='border-color: black white black black;'>&nbsp;&nbsp;IDR</th>";
				echo "<th valign='top' align='right' style='border-color: black black black white;'>".number_format($data->harga_total_idr)."</th>";
				echo "</tr>";
				$no++;
				}
		    }
		}
		?>
    </table>
     <hr>
        <?php
		echo '
        <table border="0" style="border-spacing:-1px;width:100%">
          <tr>
            <td id="grey" colspan="2" style="text-align:left;border:1px solid #000;border-spacing:1px !important">
              <i>Terbilang : <strong>#'.ucwords(ynz_terbilang_format(@$val->total_invoice_idr)).'&nbsp;Rupiah#</strong></i>
            </td>
            <th rowspan="3" style="text-align:left;vertical-align:top;padding:0 0 0 1%">
              SUBTOTAL <br>';
			  if($val->total_um > 0){
			  echo '
              DP I      <br> ';
			  }
			  if($val->total_um2 > 0){
			  echo '
              DP II      <br> ';
			  }
			  if($val->total_diskon_idr > 0){
			  echo '
              DISKON        <br>';
			  }
			  if($val->total_retensi_idr > 0){
			  echo 'RETENSI <br>';
			  }
			  if($val->total_ppn_idr > 0){
			  echo 'PPN 10% <br>';
			  }
			  if($val->total_retensi2_idr > 0){
			  echo 'RETENSI <br>';
			  }
              echo 'TOTAL<br>
            </th>
			 <th rowspan="3" style="text-align:right;vertical-align:top;padding:0 0 0 1%">
              : IDR <br>';
			  if($val->total_um_idr > 0){
				echo ': IDR <br>';
			  }
			  if($val->total_um_idr2 > 0){
				echo ': IDR <br>';
			  }
			  if($val->total_diskon_idr > 0){
				echo ': IDR <br>';
			  }
			  if($val->total_retensi_idr > 0){
				echo ': IDR <br>';
			  }
			  if($val->total_ppn_idr > 0){
				echo ': IDR <br>';
			  }
			  if($val->total_retensi2_idr > 0){
				echo 'IDR <br>';
			  }
			  echo ': IDR <br>
            </th>
            <th rowspan="3" style="text-align:right;vertical-align:top;padding:0 0 0 1%">
              ' .number_format(@$val->total_dpp_rp).'<br>';
			  if($val->total_um_idr > 0){
			  echo '
              ' .number_format(@$val->total_um_idr).'<br>';
			  }
			    if($val->total_um_idr2 > 0){
			  echo '
              ' .number_format(@$val->total_um_idr2).'<br>';
			  }
			  if($val->total_diskon_idr > 0){
				echo number_format(@$val->total_diskon_idr).'<br>';
			  }
			  if($val->total_retensi_idr > 0){
				echo number_format(@$val->total_retensi_idr).'<br>';
			  }
			  if($val->total_ppn_idr > 0){
				echo number_format(@$val->total_ppn_idr).'<br>';
			  }
			  if($val->total_retensi2_idr > 0){
				echo number_format(@$val->total_retensi2_idr).'<br>';
			  }
			  echo '
			  ' .number_format(@$val->total_invoice_idr).'<br>
            </th>
          </tr>
          <tr>
            <th width="35%">

            </th>
            <th width="35%">

            </th>

          </tr>
          <tr>
            <th>
            </th>
            <td width="30%" style="text-align: center;color:#fff">
            i<br>
            i<br>
            i<br>
              <!--<img src="'.base_url('assets/images/ori_logo.jpg').'" style="height: 50px;width: auto;display:none">-->
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
							echo "<td width='40%'>Bekasi, ".date('d F Y', strtotime($date))."</td>";
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
