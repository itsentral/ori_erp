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
    </style>
</head>
<body>
	<table valign="top" width="100%" id="tabel-laporan" style="!important; padding: 0 !important;">
     	
        
		<?php
		$tglprint2 = date("d-m-Y");
		
		foreach($total as $val){
		$date = tgl_indo($val->tgl_invoice);//date('d-m-Y');
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
				
	
			 
				echo "<tr>";
				echo "<th width=\"39\"  align='center' valign='top'>".$no."</th>";
				echo "<td width=\"345\" valign='top' >".$data->nm_material."</td>";
				echo "<th width=\"110\" valign='top' align='center'>".$data->qty."</th>";
				echo "<th width=\"110\" valign='top' align='right'>".number_format($data->harga_satuan,2)."</th>";
				echo "<th width=\"110\" valign='top' align='right'>".number_format($data->harga_total,2)."</th>";
				echo "</tr>";
				$no++;
				
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
              <i>Terbilang : <strong>'.ucwords(ynz_terbilang_format(@$val->total_invoice)).'</strong></i>
            </td>
            <th rowspan="3" style="text-align:left;vertical-align:top;padding:0 0 0 1%">
              TOTAL     <br>';
			  
			  if($val->total_um > 0){
			  echo '
              DOWN PAYMENT        <br> '; 
			  }
			  echo '
              DISKON        <br>
              POTONGAN RETENSI <br>
              PPN        <br>
              TOTAL INVOICE<br>


            </th>
            <th rowspan="3" style="text-align:left;vertical-align:top;padding:0 0 0 1%">
              :' .number_format(@$val->total_dpp_usd,2).'<br>';
			  if($val->total_um > 0){
			  echo '
              :' .number_format(@$val->total_um,2).'<br>';
			  }
			  echo '
              :' .number_format(@$val->total_diskon,2).'<br>
              :' .number_format(@$val->total_retensi,2).'<br>
              :' .number_format(@$val->total_ppn,2).'<br>
			  :' .number_format(@$val->total_invoice,2).'<br>


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

              <!----><img src="assets/img/logo.JPG" style="height: 50px;width: auto;display:none">
            </td>
          </tr>
        </table>

       <br>
	   <br>

        <table>
            <tr>
              <td width="70%" style="font-size:8pt; font-weight:bold;">
              CATATAN :<br>
                 Pembayaran dengan Cheque/Giro dianggap sah,<br>
                 setelah Cheque/Giro dapat diuangkan(Clearing)<br>
                 Pembayaran harap di transfer full amount ke:<br>
                 PT ORI POLYTEC COMPOSITES<br>
				 BCA WISMA ASIA USD 084.056.0333 IDR 084.056.1313<br>
				 * Denda 0,1%/hari, max 5% dihitung sejak tanggal<br>
				   &nbsp;&nbsp;&nbsp;jatuh tempo pembayaran<br>
				 * Untuk tagihan USD yang akan dibayarkan dalam rupiah,<br>
				   &nbsp;&nbsp;&nbsp;harap konfirmasi kurs dengan finance kami<br>
              
              </td>

			  <td align="center" style="font-size:8pt; font-weight:bold;">
			  Jakarta,'.$tglprint2.'<br>
			  <br>
			  <br>
			  <br>
			  <br>
			  <br>
			  <br>
			  <br>
			  <br>
			  WENDA DERIYANTI KURNIAWAN<br>
			  </td>
            </tr>
        </table>';
	?>
    
   
</body>
</html>
