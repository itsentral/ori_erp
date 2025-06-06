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
<?php
$header	= $this->db->query("SELECT * FROM tr_request_mutasi_aktual WHERE kd_mutasi ='$kodebayar'")->row();
$matauang	= $this->db->query("SELECT * FROM currency WHERE kode ='$header->mata_uang'")->row();

$jurnal1 = $header->jurnal1;

	    $dari = $header->dari;
		if($dari ='IDR') {
		$total          = $header->nilai_aktual;
		$dolar          = $header->nilai_request;	
		}else{				
		$total          = $header->nilai_request;
		$dolar          = $header->nilai_aktual;
		}
?>
<table width=800>
<tr><td rowspan=2 width="100"><img src="<?=base_url("assets/images/ori_logo.jpg")?>" width="80"></td><td colspan=2><h1>PT. ORI POLYTEC COMPOSITES</h1></td></tr>
<tr><td><center><div style="font-size:18px;"><u>VOUCHER BANK  <br> <?= $jurnal1 ?></u></div></center></td></tr>
<tr><td rowspan=2 valign=top></td><td rowspan=2 valign=top></td><td valign=top width="100">Nomor</td><td valign=top nowrap>: <?=$kodebayar?></td></tr>
<tr><td valign=top>Tanggal</td><td valign=top nowrap>: <?=date("d M Y",strtotime($header->tgl_request))?></td></tr>
</table>
    <br>
	    <table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>

		<?php
			echo "<tr>";
			echo "<th align='center' valign='top'>Account No</th>";
			echo "<th align='center' valign='top'>Account Name</th>";
			echo "<th align='center' valign='top'>Debet</th>";
			echo "<th align='center' valign='top'>Kredit</th>";
			echo "<th align='center' valign='top'>Keterangan</th>";
			echo "</tr>";
			
		?>
		<tbody>
			<tr>
				<td class="text-center"><?=$header->bank_tujuan?></td>
				<td class="text-center"><?=$header->nama_bank_tujuan?></td>
				<td class="text-center"><?=number_format($total,2)?></td>
				<td class="text-center">0.00</td>
				<td class="text-center"><?=$header->keterangan?></td>
				</td>
			</tr>
			<tr>
				<td class="text-center"><?=$header->bank_asal?></td>
				<td class="text-center"><?=$header->nama_bank_asal?></td>
				<td class="text-center">0.00</td>
				<td class="text-center"><?=number_format($total,2)?></td>
				<td class="text-center"><?=number_format($header->kurs,2)?></td>
				</td>
			</tr>
		</tbody>
		</table>
		<br>
		<br>
		<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0> 
		<tr>
	    <td class="text-center">Terbilang :<?= ucfirst(strtolower($header->terbilang))?></td>
		</tr>
		</table>
		<br>
		<br>
		 <table valign="top" width="300" border=1 cellpadding=1 cellspacing=0>

		<?php
			echo "<tr>";
			echo "<th align='center' valign='top'>Dari</th>";
			echo "<th align='center' valign='top'>Ke</th>";
			echo "<th align='center' valign='top'>Kurs</th>";
			echo "<th align='center' valign='top'>Transaksi</th>";
			echo "</tr>";			
		?>
		<tbody>
			<tr>
				<td class="text-center"><?=$header->dari?></td>
				<td class="text-center"><?=$header->ke?></td>
				<td class="text-center"><?=number_format($header->kurs,2)?></td>
				<td class="text-center"><?=number_format($dolar,2)?></td>
				
				
			</tr>
		</tbody>
		</table>
		<br>
		<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>
		<tr><th>DIBUAT</th><th>DIPERIKSA</th><th>DISETUJUI OLEH</th></tr>
		<tr><th width=100><br><br><br><br><br></th>
		<th width=100><br><br><br><br><br></th>
		<th width=100><br><br><br><br><br></th>
	
		</table>
</body>
</html>
