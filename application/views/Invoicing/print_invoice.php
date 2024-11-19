<html>
<head>
  <title>Cetak PDF</title>
<style>
    #tables td, th {
		border: 1px solid #808080;
        padding: 2 px;
		border-collapse: collapse;
    }
	.clearth{
		border: 0px;
		border-collapse: collapse;
	}
	.page {
		width:90% ;
		font-size:12px;
	}
	.pagebreak {
		width:90% ;
		page-break-after: always;
		margin-bottom:10px;
	}
</style>
</head>
<body>
<?php
	foreach($total as $val){
		$date = tgl_indo($val->tgl_invoice);//date('d-m-Y');
		$invoice  = $val->no_invoice;
		$so  = $val->so_number;
		$total2  = $val->total_invoice;
		
	}
	?>
			
	<table border="0">
			<tr>
			<td width="30" align="left"></td>
			<td width="200" align="center" valign="top"><img src="assets/images/ori_logo.jpg"/></td>
			<!--<td width="250" align="center" valign="top">
			Jakarta, <?php// echo $date ?> <br><br>
			Kepada Yth.<br>
			<?php// echo $klien ?><br>
			<?php// echo nl2br($alamat) ?>

			</td>-->
			</tr>
		</table>
		<table border="0" align="left">
			<tr>
			<td width="30" align="left"></td>
			<td width="300" align="left" valign="top">	INVOICE No.<?php echo $invoice ?></td>
			<td width="300" align="center" valign="top">SESUAI PO No.<?php echo $so ?></td>
			<td width="300" align="center" valign="top"></td>
			</tr>
		</table>
		<table id="tables" border="0" width="100%" align="left" cellpadding="2" cellspacing="0">
			<tr>
			<td width="20" class="clearth"></td>
			<th width="40" align="center">NO</th>
			<th width="345" align="center">KETERANGAN</th>
			<th width="110" align="center">JUMLAH</th>
			<th width="30"></th>
			<th width="110" align="center">TOTAL</th>
	       </tr>		
   
	
	<?php
	function myfooter(){	
	echo "</table>";
    }

    if( ! empty($results)){
	$no = 1;
	$page=1;
	$rp ="IDR";
	foreach($results as $data){
				
	
			if(($no%40) == 1){
		
			if($no > 1){ 
			 
				echo "
				$page
				";
				$page++;
		  	}
			
			echo"test2";
			
			}

    
				echo "<tr>";
				echo "<th class='clearth'></th>";
				echo "<th cellpadding='8' align='center' valign='top'>".$no."</th>";
				echo "<td valign='top' width='345'>".nl2br($data->nm_material)."</td>";
				echo "<th cellpadding='8' valign='top' align='center'></th>";
				echo "<th valign='top' align='center'></th>";
				echo "<th valign='top' align='right'></th>";
				echo "</tr>";
				$no++;
				
		    }
			
			
			
		}
		

        myfooter()   
	?>
	
	<div class='page' align='center'>Hal - <?php echo"$page" ?></div>
	
    <table border="0" >
	 <tr>
	       <td  width="30" align="left"></td>
		   <td  width="350" valign="top" align="left">
			<b><i>TERBILANG : <?php echo ucwords(ynz_terbilang_format($total2)); ?> Rupiah</i></b><br><br>
			</td>
     </tr>
	 </table>
	<table width="100%" border="0" >
	 <tr>
	        <td  width="300" valign="top" align="center">Hormat Kami <br><br><br><br><br><br><br><br><br>
			(.............................)<br>
			</td>
            <td  width="700" valign="top" align="left"><br><br><br>No. Faktur pajak : <?//=$no_faktur?><br><br>Permohonan Pembayaran : <?//=$bank_nama?>,
			<br>Cabang : <?//=$bank_cabang?> <br>
			a/c <?//=$bank_ac?>,
            a/n <?//=$bank_an?><br><br>
			Ketentuan : Pembayaran dengan cek/bilyet giro dianggap sah,<br>
			setelah cek/bilyet giro tersebut diuangkan(clearing).<br>
          </td>

	</tr>
    </table>
    <h6 style="text-align: left;">Printed</h6>

</body>
</html>
