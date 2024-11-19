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
            color:#000;
        }
        body
        {
            width:100%;
            font-family:Arial;
            font-style: kitfont;
            font-size:9pt;
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

		  <table valign="top" width="100%" id="tabel-laporan" style="!important; padding: 0 !important;">
			<tr id="grey" height="100px">
			<th width="5%"  align="center" valign="top">NO</th>
			<th width="42%" align="center">NAMA BARANG</th>
			<th width="10%" align="center">QUANTITY</th>
			<th width="8%" align="center">SATUAN</th>
			<th width="18%" align="center" colspan='2'>HARGA SATUAN</th>
			<th width="18%" align="center" colspan='2'>JUMLAH</th>
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
			$ket  = $val->ket;
			$jenis_invoice=$val->jenis_invoice;
	    }
		if(!empty($results)){
		$no = 1;
		$page=1;
		$rp ="IDR";
		foreach($results as $data){
			if($data->harga_total_idr > 0){
				$QTY = ($data->kategori_detail <> 'PACKING' AND $data->kategori_detail <> 'ENGINERING' AND $data->kategori_detail <> 'TRUCKING')?number_format($data->qty,2):'';
				$HARGA_SAT = ($data->kategori_detail <> 'PACKING' AND $data->kategori_detail <> 'ENGINERING' AND $data->kategori_detail <> 'TRUCKING')?number_format($data->harga_satuan_idr):'';
				$IDR = ($data->kategori_detail <> 'PACKING' AND $data->kategori_detail <> 'ENGINERING' AND $data->kategori_detail <> 'TRUCKING')?'IDR':'';
				if($jenis_invoice!='uang muka'){
					echo "<tr>";
					echo "<td align='center' valign='top'>".$no."</td>";
//					echo "<td valign='top' >".str_replace($data->so_number.' / ','',$data->nm_material)."</td>";
					echo "<td valign='top' >".$data->desc."</td>";
					echo "<td valign='top' align='center'>".$QTY."</td>";
					echo "<td valign='top' align='center'>".$data->unit."</td>";
					echo "<td valign='top' align='left' style='border-color: black white black black;'>&nbsp;&nbsp;".$IDR."</td>";
					echo "<td valign='top' align='right' style='border-color: black black black white;'>".$HARGA_SAT."</td>";
					echo "<td valign='top' align='left' style='border-color: black white black black;'>&nbsp;&nbsp;IDR</td>";
					echo "<td valign='top' align='right' style='border-color: black black black white;'>".number_format($data->harga_total_idr,2)."</td>";
					echo "</tr>";
					$no++;
				}
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
            <td colspan=2 valign=top>'.$ket.'</td>
            <th rowspan="3" style="text-align:left;vertical-align:top;padding:0 0 0 1%">
              SUBTOTAL     <br>';
			  
			  if($val->total_um_idr > 0){
				  $persenum=($val->total_um_idr*100/$val->total_dpp_rp);
				  echo '
				  DP I      '.number_format($persenum).'%<br> '; 
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
				$persenretensi=($val->total_retensi_idr*100/($val->total_dpp_rp));
				echo 'RETENSI '.number_format($persenretensi).'%<br>';
			  }
			  if($val->total_ppn_idr > 0){
				  $persenppn=($val->total_ppn_idr*100/($val->total_dpp_rp-$val->total_um_idr-$val->total_retensi_idr));
				echo 'PPN '.number_format($persenppn).'%<br>';
			  }
			  if($val->total_retensi2_idr > 0){
				  $persenretensi2=($val->total_retensi2_idr*100/($val->total_dpp_rp));
				  echo 'RETENSI '.number_format($persenretensi2).'%<br>';
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
				echo ': IDR <br>';
			  }
			  echo ': IDR <br>


            </th>
            <th rowspan="3" style="text-align:right;vertical-align:top;padding:0 0 0 1%">
              ' .number_format(@$val->total_dpp_rp,2).'<br>';
			  if($val->total_um_idr > 0){
			  echo '
              ' .number_format(@$val->total_um_idr,2).'<br>';
			  }
			    if($val->total_um_idr2 > 0){
			  echo '
              ' .number_format(@$val->total_um_idr2,2).'<br>';
			  }
			  if($val->total_diskon_idr > 0){
				echo number_format(@$val->total_diskon_idr,2).'<br>';
			  }
			  if($val->total_retensi_idr > 0){
				echo number_format(@$val->total_retensi_idr,2).'<br>';
			  }
			  if($val->total_ppn_idr > 0){
				echo number_format(@$val->total_ppn_idr,2).'<br>';
			  }
			  if($val->total_retensi2_idr > 0){
				echo number_format(@$val->total_retensi2_idr,2).'<br>';
			  }
			  echo '
			  ' .number_format(@$val->total_invoice_idr,2).'<br>


            </th>
          </tr>
          <tr>
            <th width="35%">
             
            </th>
            <th width="35%">            
            </th>

          </tr>
          <tr>
            <th></th>
            <td width="30%" style="text-align: center;color:#fff">
				i<br>
				i<br>
				i<br>
              <!----><img src="assets/img/logo.JPG" style="height: 50px;width: auto;display:none">
            </td>
          </tr>
		  <tr>
			<td colspan=4>
			<table width="100%">
			  <tr>
				<td valign=top><strong>Terbilang :</strong></td>
				<td id="grey" style="text-align:left;border:1px solid #000;border-spacing:1px !important">
				  <i> #'.strtoupper(ynz_terbilang_format(@$val->total_invoice_idr)).'&nbsp;RUPIAH#</i>
				</td>		  
			  </tr>			
			</table>
			</td>
			<td></td>
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
							echo "<td><strong><u>CATATAN :</u></strong><br>
										 Pembayaran dengan Cheque/Giro dianggap sah,<br>
										 setelah Cheque/Giro dapat diuangkan(clearing).<br>
										 Pembayaran harap di transfer full amount ke:<br>
										 <b>PT ORI POLYTEC COMPOSITES<br>
OCBC Mangga Dua Le Grandeur<br>
IDR : 0278.0001.6993<br>
USD : 0278.0001.6993<br>
* Denda 0,1% / hari, max 5% dihitung sejak tanggal jatuh tempo pembayaran<br>
* Untuk tagihan USD yang akan dibayarkan dalam rupiah, <br>
&nbsp; &nbsp;harap konfirmasi kurs dengan finance kami<br>
										 
										 <!--BCA WISMA ASIA USD 084.056.0333 IDR 084.056.1313<br><br>
										 * Denda 0,1%/hari, max 5% dihitung sejak tanggal jatuh tempo pembayaran<br>
										 * Untuk tagihan USD yang akan dibayarkan dalam rupiah, harap konfirmasi kurs dengan finance kami-->
										</b>
									</td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "<td width='10%'></td>";
				echo "<td width='15%' style='vertical-align:top;'>";
					echo "<table class='sign' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='40%' align=center>Bekasi, ".date('d F Y', strtotime($date))."</td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td style='height:90px;'></td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td align=center><b><u>".get_finance_manager()."</u></b></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
	?>
    
   
</body>
</html>
