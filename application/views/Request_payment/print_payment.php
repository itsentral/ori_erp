<?php
$judul='Payment';
$no_doc="";$tgl_bayar="";$jml_bayar="";$ket_bayar="";$bank_bayar="";$bank_admin="";
if(!empty($results)){
	$no_doc=$results->no_doc;
	$tgl_bayar=tgl_indo($results->pay_on);
	$bank_bayar=$results->bank_coa." / ".$results->namabank;
	$ket_bayar=$results->keterangan;
	$jml_bayar=$results->bank_nilai;
	$bank_admin=$results->bank_admin;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> <?=$judul.' '.$no_doc?> </title>
<style>
body{
	font-family: sans-serif;
}
table.garis {
	border-collapse: collapse;
	font-size: 0.9em;
	font-family: sans-serif;
}
</style>
 </head>

 <body>
<table cellpadding=2 cellspacing=0 border=0 width=700>
<tr>
	<th colspan=5 height=50><?=$judul?></th>
</tr>
<tr>
	<td colspan=5>
	Nomor Dokumen : <?=$no_doc?><br />
	Tanggal Pembayaran : <?=$tgl_bayar?><br />
	Bank : <?=$bank_bayar?><br />
	Keterangan : <?=$ket_bayar?><br />
	Jumlah Bayar : Rp. <?=number_format($jml_bayar)?><br />
	<?php if($bank_admin!="" and $bank_admin!="0"){
		echo "Bank Admin : Rp. ".number_format($bank_admin)."<br>";
	}
	?>
	<br /></td>
</tr>
<tr>
	<td colspan=5>
	<table cellpadding=2 cellspacing=0 border=1 width=700 class="garis">
		<tr>
			<th>Request By</th>
			<th>Tanggal</th>
			<th>Keperluan</th>
			<th>Nilai Pengajuan</th>
			<th>Tipe</th>
		</tr>
	<?php
	$i=0;
	if(!empty($results)){ ?>
		<tr>
			<td><?= $results->nama ?></td>
			<td><?= tgl_indo($results->tgl_doc) ?></td>
			<td><?= $results->keperluan ?></td>
			<td align=right><?= number_format($results->jumlah) ?></td>
			<td><?= strtoupper($results->tipe) ?></td>
		</tr>
		<?php
	}
?>
	</table>
	</td>
</tr>
</table>
  <script type="text/javascript">
  <!--
  window.print();
  //-->
  </script>
   </body>
</html>
