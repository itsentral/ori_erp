<?php
$dtdepartment= $this->db->query("SELECT * FROM department a WHERE a.id='".$data->departement."'")->row();
$nm_dept='';
if(!empty($dtdepartment)) $nm_dept=$dtdepartment->nm_dept;
?>
<link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/select2.css">
<script src="<?=base_url()?>assets/plugins/select2/select2.full.min.js"></script>

<table cellpadding=2 cellspacing=0 border=0>
<tr>
	<th colspan=9>
	<table>
		<tr>
			<td width="50%">No Dokumen : <?=$data->no_doc?></td>
			<td width="50%" align=right>Departmen : <?=$nm_dept?></td>
		</tr>
		<tr>
			<td colspan=2>Transfer ke Bank <?=$data->bank_id?>. Nomor Rekening <?php echo ($data->accnumber);?> - <?=$data->accname?></td>
		</tr>
	</table>
	</th>
</tr>
<tr>
	<td colspan=9>
	<table cellpadding=2 cellspacing=0 border=1 class="table table-bordered">
	<tr>
		<th nowrap>No</th>
		<th nowrap>Tgl Pengajuan</th>
		<th nowrap width="350">COA</th>
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
			<td valign="top">
			<?php
			$coa='';
			echo form_dropdown('coa[]',$combo_coa,(isset($record->coa) ? $record->coa: ''),array('id'=>'coa_'.$i,'class'=>'form-control select2', 'style'=>"width : '200px'"));
			?><input type="hidden" name="detail_id[]" id="detail_id_<?=$i?>" value="<?=$record->id;?>">
			<button class="btn btn-info btn-xs" type="button" onclick="simpan(<?=$i?>)">Save</button>
			</td>
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
	$text="Saldo";$sisakurang="";
	$all_total=(($total_expense+$data->add_ppn_nilai-$data->add_pph_nilai-$total_kasbon));
	if ($total_expense>$total_kasbon){
//		$text="(Kurang)";
		$sisakurang=number_format($total_expense-$total_kasbon);
	}else{
//		$text="Sisa Lebih";
		$sisakurang="( ".number_format($total_expense-$total_kasbon)." )";
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
<?php } ?>
	<tr>
		<td colspan=3></td>
		<td colspan=5><strong>Total</strong></td>
		<td colspan=2 align=right><?=number_format($all_total);?></td>
	</tr>
	<?php if($total_kasbon>0) {?>
	<tr>
		<td colspan=8 align=center><strong><?=$text?></strong></td>
		<td colspan=2 align="right"><?=$sisakurang;?></td>
	</tr>
	<?php } ?>
	</table><br />
	</td>
</tr>

<?php
$mengajukan= $this->db->query("SELECT nm_lengkap FROM users a WHERE a.username='".$data->created_by."'")->row();
$mengetahui=$this->db->query("SELECT nm_lengkap FROM users a WHERE a.username='".$data->approved_by."'")->row();
?>

<tr>
	<td colspan=2 align=center>Mengajukan</td>
	<td></td>
	<td align=center colspan=2>Mengetahui</td>
	<td></td>
	<td></td>
	<td align=center colspan=3>Menyetujui</td>
</tr>
<tr>
	<td colspan=9>&nbsp;</td>
</tr>
<tr height=120>
	<td colspan=2 align=center nowrap valign="bottom"><u>&nbsp; &nbsp; <?=(($mengajukan)?$mengajukan->nm_lengkap:' &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; ')?> &nbsp; &nbsp; </u><br /> &nbsp;</td>
	<td width=20>&nbsp;</td>
	<td colspan=2 align=center nowrap valign="bottom"><u>&nbsp; &nbsp; <?=(($mengetahui)?$mengetahui->nm_lengkap:' &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; ')?> &nbsp; &nbsp; </u><br /> &nbsp;</td>
	<td width=20>&nbsp;</td>
	<td width=20>&nbsp;</td>
	<td colspan=3 align=center nowrap valign="bottom"><u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u><br /><br /></td>
</tr>
</table>
<script type="text/javascript">
	$('.select2').select2({dropdownAutoWidth : true, width : '100%'});
	function simpan(id){
		var coa=$("#coa_"+id).val();
		var detail_id=$("#detail_id_"+id).val();
		swal({
		  title: "Anda Yakin?",
		  text: "Data Akan Disimpan!",
		  type: "info",
		  showCancelButton: true,
		  confirmButtonText: "Ya, simpan!",
		  cancelButtonText: "Tidak!",
		  closeOnConfirm: false,
		  closeOnCancel: true
		},
		function(isConfirm){
		  if (isConfirm) {			
			$.ajax({
				url: base_url+'expense/save_coa/'+detail_id+'/'+coa,
				dataType : "json",
				type: 'POST',
				processData	: false,
				contentType	: false,
				success: function(msg){
					if(msg['save']=='1'){
						swal({
							title: "Sukses!",
							text: "Data Berhasil Di Simpan",
							type: "success",
							timer: 1500,
							showConfirmButton: false
						});						
					} else {
						swal({
							title: "Gagal!",
							text: "Data Gagal Di Simpan",
							type: "error",
							timer: 1500,
							showConfirmButton: false
						});
					};
					console.log(msg);
				},
				error: function(msg){
					swal({
						title: "Gagal!",
						text: "Ajax Data Gagal Di Proses",
						type: "error",
						timer: 1500,
						showConfirmButton: false
					});
					console.log(msg);
				}
			});
		  }
		});
	}
</script>
<br />
<?=$gambar?>