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
<?php
	$i=0;
	if(!empty($data_request)){
		foreach($data_request AS $bank){?>
<tr>
	<td colspan=9 align=center>Bank : <?=$bank->bank_id?><br />
	No rek : <?=$bank->accnumber?><br /> A.n : <?=$bank->accname?><br /><br /></td>
</tr>

<?php }

}?>

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
			
		</tr>
		<?php
		}
	}
	?>
	</table>

	<br>
	<br>

	<?php if($data_request[0]->tipe=='expense') { ?>

	<table valign="top" width="800" border=1 cellpadding=1 cellspacing=0>
		<tr>
		<th nowrap>No</th>
		<th nowrap>Tgl Pengajuan</th>
		<th nowrap>COA</th>
		<th nowrap>Nama Barang</th>
		<th nowrap>Spesifikasi</th>
		<th nowrap>Jml</th>
		<th nowrap>Tgl Dibutuhkan</th>
		<th nowrap>Biaya Satuan</th>
		<th nowrap>Total Biaya</th>
		<th nowrap>Kasbon</th>
		</tr>
		<?php $total_expense=0; $total_tol=0;$total_parkir=0;$total_kasbon=0; $idd=1; $total_km=0; $grand_total=0;$i=0;$gambar="";
		if(!empty($data_detail)){
			foreach($data_detail AS $record){ $i++;?>
			<tr>
				<td valign="top"><?=$i;?></td>
				<td valign="top"><?=tgl_indo($data->tgl_doc);?></td>
				<td valign="top"><?php
				echo $record->coa;
				$dtcoa=$this->db->query("SELECT * FROM ".DBACC.".coa_master where no_perkiraan='".$record->coa."'")->row();
				if(!empty($dtcoa)) echo " ".$dtcoa->nama;
				?></td>
				<td valign="top"><?=$record->deskripsi;?></td>
				<td valign="top"><?=$record->keterangan;?></td>
				<td valign="top" align="right"><?=number_format($record->qty);?></td>
				<td valign="top"><?=tgl_indo($record->tanggal);?></td>
				<td valign="top" align="right"><?=number_format($record->harga);?></td>
				<td valign="top" align="right"><?=number_format($record->expense);?></td>
				<td valign="top" align="right"><?=number_format($record->kasbon);?></td>
			</tr>
			<?php
				if($record->doc_file!=''){
					if(strpos($record->doc_file,'pdf',0)>1){
					}else{
						$gambar.='<img src="'.base_url("assets/expense/".$record->doc_file).'" width="500"><br />';
					}
				}
				$total_expense=($total_expense+($record->expense));
				$total_kasbon=($total_kasbon+($record->kasbon));
				$idd++;
			}
		}
		$grand_total=($total_expense);
		for($x=0;$x<(5-$i);$x++){
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
			</tr>
		';
		}
		$text="Saldo";$sisakurang="";
		$all_total=(($total_expense+$data->add_ppn_nilai-$data->add_pph_nilai-$total_kasbon));
		if ($total_expense>$total_kasbon){
	//		$text="(Kurang)";
			$sisakurang=number_format(($total_expense+$data->add_ppn_nilai)-$total_kasbon);
		}else{
	//		$text="Sisa Lebih";
			$sisakurang="( ".number_format(($total_expense+$data->add_ppn_nilai)-$total_kasbon)." )";
		}
	?>
		<tr>
			<td colspan=3></td>
			<td colspan=5><strong>Sub Total</strong></td>
			<td align="right"><?=number_format($total_expense);?></td>
			<td align="right"><?=number_format($total_kasbon);?></td>
		</tr>
	<?php
	if ($data->add_ppn_nilai>0){
		?>
		<tr>
			<td colspan=3></td>
			<td><strong>PPN</strong></td>
			<td colspan=4><?php
				echo $data->add_ppn_coa;
				$dtcoa=$this->db->query("SELECT * FROM ".DBACC.".coa_master where no_perkiraan='".$data->add_ppn_coa."'")->row();
				if(!empty($dtcoa)) echo " ".$dtcoa->nama;
				?></td>
			<td colspan=2 align=right><?=number_format($data->add_ppn_nilai);?></td>
		</tr>
	<?php }
	if ($data->add_pph_nilai>0){ ?>
		<tr>
			<td colspan=3></td>
			<td><strong>PPH</strong></td>
			<td colspan=4><?php
				echo $data->add_pph_coa;
				$dtcoa=$this->db->query("SELECT * FROM ".DBACC.".coa_master where no_perkiraan='".$data->add_pph_coa."'")->row();
				if(!empty($dtcoa)) echo " ".$dtcoa->nama;
				?></td>
			<td colspan=2 align=right><?=number_format($data->add_pph_nilai);?></td>
		</tr>
	<?php }
	if($all_total>0) { ?>
		<tr>
			<td colspan=3></td>
			<td colspan=5><strong>Total</strong></td>
			<td colspan=2 align=right><?=number_format($all_total);?></td>
		</tr>
		<?php 
	}
	if($total_kasbon>0) {?>
		<tr>
			<td colspan=8 align=center><strong><?=$text?></strong></td>
			<td colspan=2 align="right"><?=$sisakurang;?></td>
		</tr>
		<?php } ?>
		</table><br />
		</td>
	</tr>
	</table>

	<?php } else {?>

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


	<?php } ?>

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
    </td>
	</tr>
</table>
 <script type="text/javascript">
  <!--
  window.print();
  //-->
  </script>