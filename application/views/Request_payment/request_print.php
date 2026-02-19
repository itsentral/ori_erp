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
<?php
$judul='Form Request Payment';
$nomorreq="";$tglreq="";
if(!empty($data_request)){
	$nomorreq=$data_request[0]->no_request;
	$tglreq=tgl_indo($data_request[0]->created_on);
}
?>
<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>
<tr>
	<th colspan=9 height=50><?=$judul?></th>
</tr>
<tr>
	<td colspan=9 align=center>Nomor Request : <?=$nomorreq?><br />
	Tanggal Request : <?=$tglreq?><br /><br /></td>
</tr>
<tr>
	<td colspan=9>
	<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>
		<tr>
			<th width="5">#</th>
			<th>No Dokumen</th>
			<th>Request By</th>
			<th>Tanggal</th>
			<th>Keperluan</th>
			<th>Tipe</th>
			<th>Nilai Pengajuan</th>
			<th>Tanggal Pembayaran</th>
			<th>Bank</th>
			<th>No Rek</th>
			<th>Nama</th>
		</tr>
	<?php
	$i=0;
	if(!empty($data_request)){
		foreach($data_request AS $record){ $i++;?>
		<tr>
			<td><?=$i;?></td>
			<td><?= $record->no_doc ?></td>
			<td><?= $record->nama ?></td>
			<td><?= tgl_indo($record->tgl_doc) ?></td>
			<td><?= $record->keperluan ?></td>
			<td><?= strtoupper($record->tipe) ?></td>
			<td align=right><?= number_format($record->jumlah) ?></td>
			<td><?= tgl_indo($record->tanggal) ?></td>
			<td><?= strtoupper($record->bank_id) ?></td>
			<td><?= $record->accnumber ?></td>
			<td><?= strtoupper($record->accname) ?></td>
		</tr>
		<?php
		}
	}
	?>
	</table>

	<br>
	<br>

	<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>
		<tr>
			<th width="5">#</th>
			<th>Tanggal</th>
			<th>Deskripsi</th>
			<th>Qty</th>
			<th>Harga</th>
			<th>Total Harga</th>
			<th>Keterangan</th>
		</tr>
	<?php
	$i=0;
	if(!empty($data_detail)){
		foreach($data_detail AS $rec){ $i++;?>
		<tr>
			<td><?=$i;?></td>
			<td><?= $rec->tanggal ?></td>
			<td><?= $rec->deskripsi ?></td>
			<td align=right><?= number_format($rec->qty) ?></td>
			<td align=right><?= number_format($rec->harga) ?></td>
			<td align=right><?= number_format($rec->total_harga) ?></td>
			<td><?= $rec->keterangan ?></td>
		</tr>
		<?php
		}
	}
	?>
	</table>

	</td>

	<br>
	<br>
	<br>

	<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>
	<tr><th>DIBUAT</th><th>DIPERIKSA</th><th>DIBUKUKAN ACCT</th><th colspan=4>DISETUJUI OLEH</th><th>PENERIMA</th></tr>
		<tr><th width=100><br><br><br><br><br></th>
		<th width=100><br><br><br><br><br></th>
		<th width=120><br><br><br><br><br></th>
		<th width=90><br><br><br><br><br></th>
		<th width=90><br><br><br><br><br></th>
		<th width=90><br><br><br><br><br></th>
		<th width=90><br><br><br><br><br></th>
		<th width=100><br><br><br><br><br></th></tr>
	</table>

	</tr>
</table>
 <script type="text/javascript">
  <!--
  window.print();
  //-->
  </script>