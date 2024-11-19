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
<table cellpadding=2 cellspacing=0 border=0 width=650>
<tr>
	<th colspan=11>EXPENSES REPORT BENSIN & TOL</th>
</tr>
<tr>
	<td colspan=5>No : <?=$data->no_doc?></td>
	<td colspan=6>Tanggal : <?=$data->tgl_doc?></td>
</tr>
<tr>
	<td colspan=11>COA :
	<?php
		echo $data->coa;
		$dtcoa=$this->db->query("SELECT nama FROM ".DBACC.".coa_master where no_perkiraan='".$data->coa."'")->row();
		if(!empty($dtcoa)) echo " ".$dtcoa->nama;	
	?></td>
</tr>
<tr>
	<td colspan=11>Transfer ke Bank <?=$data->bank_id?>. Nomor Rekening <?php echo ($data->accnumber);?> - <?=$data->accname?></td>
</tr>
<tr>
	<td colspan=11>
	<table cellpadding=2 cellspacing=0 border=1 width=650 class="garis">
	<tr>
		<th rowspan=3>TGL</th>
		<th rowspan=3>Mobil</th>
		<th rowspan=3>AKTIVITAS<br />(PT.)</th>
		<th rowspan=3>RUTE</th>
		<th colspan=5>Jenis Jenis Bukti</th>
		<th rowspan=3>Total</th>
		<th rowspan=3>Ket</th>
	</tr>
	<tr>
		<th colspan=3>Bensin</th>
		<th rowspan=2 nowrap>Tol &<br />Parkir</th>
		<th rowspan=2 nowrap>Transport /<br />Lain Lain</th>
	</tr>
	<tr>
		<th>Mobil</th>
		<th>KM</th>
		<th>Jumlah</th>
	</tr>
	<?php $total_bensin=0; $total_tol=0;$total_parkir=0;$total_kasbon=0; $idd=1; $total_km=0; $grand_total=0;$i=0;$lainnya=0;
	if(!empty($data_detail)){
		foreach($data_detail AS $record){ $i++;?>
		<tr>
			<td><?=tgl_indo($record->tgl_doc);?></td>
			<td><?=$record->nopol;?></td>
			<td><?=$record->keperluan;?></td>
			<td><?=$record->rute;?></td>
			<td align="right"><?=number_format($record->bensin);?></td>
			<td align="right"><?=number_format($record->km_akhir-$record->km_awal);?></td>
			<td align="right"> </td>
			<td align="right"><?=number_format($record->tol+$record->parkir);?></td>
			<td align="right"><?=number_format($record->lainnya);?></td>
			<td align="right"><?=number_format($record->bensin+$record->tol+$record->parkir+$record->lainnya);?></td>		
			<td align="right"><?=($record->keterangan);?></td>
		</tr>
		<?php
			$total_bensin=($total_bensin+($record->bensin));
			$total_tol=($total_tol+($record->tol));
			$total_parkir=($total_parkir+($record->parkir));
			$total_km=($total_km+($record->km_akhir-$record->km_awal));
			$lainnya=($lainnya+$record->lainnya);
			$idd++;
		}
	}
	$grand_total=($total_bensin+$total_tol+$total_parkir+$lainnya);
	for($x=0;$x<(9-$i);$x++){
	echo '
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	';
	}
	?>
	<tr>
		<td colspan="9" align=center >TOTAL</td>
		<td align="right"><?=number_format($grand_total);?></td>
		<td></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td align=center>Mengajukan</td>
	<td></td>
	<td align=center>Mengetahui</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td align="right"></td>
	<td></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<?php
$mengajukan= $this->db->query("SELECT nm_lengkap FROM users a WHERE a.id_user='".$data->created_by."'")->row();
$mengetahui=$this->db->query("SELECT nm_lengkap FROM users a WHERE a.id_user='".$data->fin_check_by."'")->row();
?>
<tr height=120>
	<td colspan=2 nowrap valign="bottom"><!--<em>SSPM/ADM/17/Rev. 01</em>--></td>
	<td align=center nowrap valign="bottom">
	<u>&nbsp; &nbsp; <?=(($mengajukan)?$mengajukan->nm_lengkap:' &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; ')?> &nbsp; &nbsp; </u></td>
	<td></td>
	<td align=center nowrap valign="bottom">
	<u>&nbsp; &nbsp; <?=(($mengetahui)?$mengetahui->nm_lengkap:' &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; ')?> &nbsp; &nbsp; </u></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>