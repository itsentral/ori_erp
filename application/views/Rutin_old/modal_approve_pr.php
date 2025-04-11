

<div class="box box-primary">
    <div class="box-body">
		<br>
		<input type='hidden' id='no_ipp' name='no_ipp' value='<?=$no_ipp;?>'>
		<input type='hidden' id='tanda' name='tanda'value='<?=$tanda;?>'>
		<input type='hidden' id='id_user' name='id_user' value='<?=$id_user;?>'>
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='3%'>#</th>
					<th class="text-center">Nama Barang</th>
					<th class="text-center" width='10%'>Category</th>
					<th class="text-center" width='9%'>Spec</th>
					<th class="text-center" width='5%'>Stock</th>
					<th class="text-center" width='5%'>Kebutuhan</th>
					<th class="text-center" width='5%'>Max Stock</th>
					<th class="text-center" width='5%'>Qty</th>
					<th class="text-center" width='7%'>Dibutuhkan</th>
					<th class="text-center" width='8%'>Spec PR</th>
					<!-- <th class="text-center" width='8%'>Info PR</th> -->
					<th class="text-center" width='6%'>Rev Qty</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='3%'>Reject</th>
					<th class="text-center" width='3%'><input type="checkbox" name="chk_all" id="chk_all"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				$GET_KEBUTUHAN = get_kebutuhanPerMonth();
				foreach($result AS $val => $valx){ $no++;
					$KEBUTUHAN = (!empty($GET_KEBUTUHAN[$valx['id_material']]['kebutuhan']))?$GET_KEBUTUHAN[$valx['id_material']]['kebutuhan']:0;
					$SATUAN 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".strtoupper($valx['nm_material'])."</td>";
						echo "<td align='left'>".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal']))."</td>";
						echo "<td align='left'>".strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $valx['id_material']))."</td>";
						echo "<td align='center'>".number_format($valx['stock'],2)."</td>";
						echo "<td align='center'>".number_format($KEBUTUHAN,2)."</td>";
						echo "<td align='center'>".number_format(($KEBUTUHAN * 1.5), 2)."</td>";
						echo "<td align='center'>".number_format($valx['purchase'])." ".strtolower($SATUAN)."</td>";
						echo "<td align='center'>".date('d-M-Y', strtotime($valx['tanggal']))."</td>";
						echo "<td align='left'>".strtoupper($valx['spec_pr'])."</td>";
						// echo "<td align='left'>".strtoupper($valx['info_pr'])."</td>";
						echo "<td align='center'><input type='text' name='detail[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-center maskM rev_qty' value='".number_format($valx['purchase'])."' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
						echo "<td align='center'>
								<input type='text' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;' value='".$valx['info_pr']."'>
								<input type='hidden' name='detail[".$no."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='detail[".$no."][id]' value='".$valx['id']."'>
								<input type='hidden' name='detail[".$no."][no_pengajuan]' value='".$valx['no_pengajuan']."'>
								<input type='hidden' name='detail[".$no."][satuan]' value='".$valx['satuan']."'>
								<input type='hidden' name='detail[".$no."][purchase]' value='".number_format($valx['purchase'])."'>
								<input type='hidden' name='detail[".$no."][tanggal]' value='".$valx['tanggal']."'>
								</td>";
						echo "<td align='center'><button type='button'class='btn btn-sm btn-danger rejectPR' title='Reject PR' data-no_pengajuan='".$valx['no_pengajuan']."'><i class='fa fa-close'></i></button></td>";
						echo "<td align='center'><input type='checkbox' name='check[".$no."]' class='chk_personal' data-nomor='".$no."' value='".$valx['no_pengajuan']."'></td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 15px 0px 5px 0px;','content'=>'Approve','id'=>'app_pr'));
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-danger','style'=>'min-width:100px; float:right; margin: 15px 5px 5px 0px;','content'=>'Reject','id'=>'rejectPR_check'));
		?>
	</div>
</div>

<script>
    swal.close();
	$(document).ready(function(){
		$('.maskM').maskMoney();

		$("#chk_all").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
	});
</script>