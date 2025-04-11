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
            font-size:11px;
			<!--font-weignt:bold;-->
            color:#000;
        }
        body
        {
            width:100%;
            font-family: "Times New Roman", Times, serif;
            font-size:11px;
			<!--font-weignt:bold;-->
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
           border : solid 1px #000;
            margin: 0px;
        }
        #tabel-laporan td{
            border : solid 1px #000;
            margin: 0px;
        }
        #tabel-laporan {
          border-bottom:1px solid #000 !important;
        }
		.pagebreak {
			width:100% ;
			page-break-after: always;
			margin-bottom:10px;
		}
    </style>
</head>
<body>
	<table width="100%" id="tabel-laporan" >
		<tr>
			<td style='height:50px; width:18% !important;' align="center" colspan='2' valign=top>Cust.Ref./P.O<br><br><strong><?=$data_header->no_po;?></strong></td>
			<td style='height:50px; width:17% !important;' align="center" valign=top>Term of Payment<br><br><strong><?=(($data_header->payment_term==0)?"CASH BEFORE DELIVERY":$data_header->payment_term ." Days");?></strong></td>
			<td style='height:50px; width:20% !important;' align="center" colspan='2' valign=top>Port of Loading<br><br><strong><?=$data_header->port_of_loading;?></strong></td>
			<td style='height:50px; width:24% !important;' align="center" colspan='3' valign=top>Port of Discharges<br><br><strong><?=$data_header->port_of_discharges;?></strong></td>
			<td style='height:50px; width:45% !important;' align="center" colspan='2' valign=top>Flight/Airway-bill No.<br><br><strong><?=$data_header->flight_airway_no;?></strong></td>
		</tr>
		<tr>
			<td style='height:50px;' align="center" colspan='2' valign=top>Term of Delivery<br><br><?=$data_header->term_delivery;?></td>
			<td align="center" valign=top>Ship Via<br><br><strong><?=$data_header->ship_via;?></strong></td>
			<td align="center" colspan='2' valign=top>Sailing on/about<br><br><strong>
			<?php if($data_header->saliling=="0000-00-00" || $data_header->saliling==""){
				echo "";
			}else{
				echo date('M d, Y', strtotime($data_header->saliling));
			}?></strong></td>
			<td align="center" colspan='3' valign=top>Vessel / Flight<br><br><strong><?=$data_header->vessel_flight;?></strong></td>
			<td align="center" colspan='2' valign=top>Currency<br><br><strong>USD</strong></td>
		</tr>
	</table>
	<table width="100%" id="tabel-laporan" >
		<thead>
     	<tr>
			<td style='height:50px; width:5% !important;' align="center">No</td>
			<td style='height:50px; width:30% !important;' align="center">Description of Goods</td>
			<td style='height:50px; width:10% !important;' align="center">Qty<br>Delivered</td>
			<td style='height:50px; width:10% !important;' align="center">Unit</td>
			<td style='height:50px; width:14% !important;' align="center" colspan='2'>Price<br>List</td>
			<td style='height:50px; width:10% !important;' align="center">Disc<br>%</td>
			<td style='height:50px; width:14% !important;' align="center" colspan='2'>Net Value</td>
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

		if( ! empty($results)){
			$no = 1;
			$page=1;
			$rp ="IDR";
			foreach($results as $data){
				if($data->harga_total > 0){
					$qty = ($data->qty > 0)?$data->qty:1;
					$unit = ($data->harga_satuan > 0)?$data->harga_satuan:$data->harga_total;
					if($jenis_invoice!='uang muka'){
					echo "<tr>";
						echo "<td align='center' valign='top'>".$no."</td>";
//						echo "<td valign='top'>".$data->nm_material."</td>";
						echo "<td valign='top'>".$data->desc."</td>";
						echo "<td valign='top' align='center'>".number_format($qty,2)."</td>";
						echo "<td valign='top' align='center'>".$data->unit."</td>";
						echo "<td valign='top' align='center' style='border-color: black white black black;'>USD</td>";
						echo "<td valign='top' align='right' style='border-color: black black black white;'>".number_format($unit,2)."</td>";
						echo "<td valign='top' align='right'></td>";
						echo "<td valign='top' align='center' style='border-color: black white black black;'>USD</td>";
						echo "<td valign='top' align='right' style='border-color: black black black white;'>".number_format($data->harga_total,2)."</td>";
					echo "</tr>";
					$no++;
					}
				}
		    }	
		}
		
		echo "<tr>";
			echo "<td colspan='4' valign=top>".$data_header->ket."</td>";
            echo "<td colspan='3'>";
				echo "TOTAL<br>";
				if($val->total_um > 0){
					$persenum=($val->total_um*100/$val->total_dpp_usd);
					echo "DOWN PAYMENT ".number_format($persenum)."%<br>"; 
				}
				if($val->total_diskon > 0){echo 'DISCOUNT<br>';}
				if($val->total_retensi > 0){
					$persenretensi=($val->total_retensi*100/($val->total_dpp_usd-$val->total_um));
					echo 'RETENTION PIECES '.number_format($persenretensi).'%<br>';
				}
				if($val->total_ppn > 0){
					$persenppn=($val->total_ppn*100/($val->total_dpp_usd-$val->total_um-$val->total_retensi));
					echo 'TAX '.number_format($persenppn).'%<br>';
				}
				if($val->total_retensi2 > 0){
					$persenretensi2=($val->total_retensi2*100/($val->total_dpp_usd));
					echo 'RETENTION PIECES '.number_format($persenretensi2).'%<br>';
				}
				if($val->total_invoice > 0){echo 'TOTAL INVOICE<br>';}
            echo "</td>";
			echo "<td align='center' style='border-color: black white black black;'>";
				echo "USD<br>";
				if($val->total_um > 0){
				echo "USD<br>"; 
				}
				if($val->total_diskon > 0){echo 'USD<br>';}
				if($val->total_retensi > 0){echo 'USD<br>';}
				if($val->total_ppn > 0){echo 'USD<br>';}
				if($val->total_retensi2 > 0){echo 'USD<br>';}
				if($val->total_invoice > 0){echo 'USD<br>';}
            echo "</td>";
            echo "<td align='right' style='border-color: black black black white;'>";
				echo number_format(@$val->total_dpp_usd,2)."<br>";
				if($val->total_um > 0){
					echo number_format(@$val->total_um,2).'<br>';
				}
				if($val->total_diskon > 0){echo number_format(@$val->total_diskon,2).'<br>';}
				if($val->total_retensi > 0){echo number_format(@$val->total_retensi,2).'<br>';}
				if($val->total_ppn > 0){echo number_format(@$val->total_ppn,2).'<br>';}
				if($val->total_retensi2 > 0){echo number_format(@$val->total_retensi2,2).'<br>';}
				if($val->total_invoice > 0){echo number_format(@$val->total_invoice,2).'<br>';}
			echo "</td>";
        echo "</tr>";
		?>
    </table>
	<br><br>
    <?php
		
	echo "<table border='0' width='100%' cellpadding='0'>";
			echo "<tr>";
				echo "<td width='12%'></td>";
				echo "<td width='18%' style='vertical-align:top;'>";
					echo "<table class='sign' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td width='40%'></td>";
//							echo "<td width='40%'>Bekasi, ".date('F d, Y', strtotime($tglprint2))."</td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td style='height:70px;'></td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td><b>.....................................</b><br />&nbsp; &nbsp; &nbsp;<b>".get_finance_manager()."</b></td>";
//							echo "<td><b>__________________________</b></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				echo "<td width='12%'></td>";
				echo "<td width='58%' style='vertical-align:top;'>";
					echo "<table class='catatan' border='0' width='100%' cellpadding='2'>";
						echo "<tr>";
							echo "<td>
										 Please remittance the payment in FULL AMOUNT to:<br>
										 <b>PT ORI POLYTEC COMPOSITES</b><br>
										 <!--
										 JAKARTA ROXI<br> 
										 BANK RAKYAT INDONESIA<br>
										 Pusat Niaga Roxi Mas Blok B.1 No. 1-2<br>
										 Jl. KH. Hasyim Ashari Jakarta 10150<br>
										 <b>Swift Code : BRINIDJA </b><br>
										 <b>AC : (USD) 0338.02.000079.307</b>-->

										Mangga Dua Le Grandeur<br />
										OCBC NISP<br />
										Komplek Dusit Mangga Dua Ruko No 1.<br />
										Jl. Mangga Dua Raya, Jakarta Pusat<br />
										<strong>Swift Code : NISPIDJA<br />
										A/C : (USD)  0278.0001.6993</strong>

									</td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				
			echo "</tr>";
		echo "</table>";
	?>
    
   
</body>
</html>
