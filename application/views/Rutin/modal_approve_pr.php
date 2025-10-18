

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
					<th class="text-center">Category</th>
					<th class="text-center">Brand</th>
					<th class="text-center">Keb.1 Bln</th>
					<th class="text-center">Max Stock</th>
					<th class="text-center">Stock</th>
					<th class="text-center">Qty</th>
					<th class="text-center">Unit</th>
					<th class="text-center">Dibutuhkan</th>
					<th class="text-center">Spec PR</th>
					<th class="text-right">Price From Supplier</th>
					<th class="text-right">Total Budget</th>
					<th class="text-center" width='6%'>Rev Qty</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center">Reject</th>
					<th class="text-center"><input type="checkbox" name="chk_all" id="chk_all"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				$SUM_BUDGET = 0;
				foreach($result AS $val => $valx){ $no++;
					$SPEC 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['spec']))?' - '.$GET_COMSUMABLE[$valx['id_material']]['spec']:'';
					$BRAND 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['brand']))?$GET_COMSUMABLE[$valx['id_material']]['brand']:'';
					$CATEGORY 	= get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal']);
					$SATUAN 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);

					$kebutuhnMonth 	= (!empty($GET_KEBUTUHAN_PER_MONTH[$valx['id_material']]['kebutuhan']))?$GET_KEBUTUHAN_PER_MONTH[$valx['id_material']]['kebutuhan']:0;
					$maxStock 		= $kebutuhnMonth * 1.5;
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material'].$SPEC."</td>";
						echo "<td align='left'>".strtoupper($CATEGORY)."</td>";
						echo "<td align='left'>".strtoupper($BRAND)."</td>";
						echo "<td align='center'>".number_format($kebutuhnMonth)."</td>";
						echo "<td align='center'>".number_format($maxStock)."</td>";
						echo "<td align='center'>".number_format($valx['stock'],2)."</td>";
						echo "<td align='center'>".number_format($valx['purchase'])."</td>";
						echo "<td align='center'>".$SATUAN."</td>";
						echo "<td align='center'>".date('d-M-Y', strtotime($valx['tanggal']))."</td>";
						echo "<td align='left'>".$valx['spec_pr']."</td>";
						$price_from_supplier = (!empty($valx['price_from_supplier']))?$valx['price_from_supplier']:0;
						$total_budget = $price_from_supplier * $valx['purchase'];
						echo "<td align='right'>".number_format($price_from_supplier)."</td>";
						echo "<td align='right' class='cal_tot_budget'>".number_format($total_budget,2)."</td>";
						// echo "<td align='left'>".strtoupper($valx['info_pr'])."</td>";
						echo "<td align='center'><input type='text' name='detail[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-center maskM rev_qty' value='".number_format($valx['purchase'])."' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
						echo "<td align='center'>
								<input type='text' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;' value='".$valx['info_pr']."'>
								<input type='hidden' name='detail[".$no."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='detail[".$no."][id]' value='".$valx['id']."'>
								<input type='hidden' name='detail[".$no."][no_pengajuan]' value='".$valx['no_pengajuan']."'>
								<input type='hidden' name='detail[".$no."][satuan]' value='".$valx['satuan']."'>
								<input type='hidden' name='detail[".$no."][in_gudang]' value='".$valx['in_gudang']."'>
								<input type='hidden' name='detail[".$no."][purchase]' value='".number_format($valx['purchase'])."'>
								<input type='hidden' name='detail[".$no."][tanggal]' value='".$valx['tanggal']."'>
								</td>";
						echo "<td align='center'><button type='button'class='btn btn-sm btn-danger rejectPR' title='Reject PR' data-no_pengajuan='".$valx['no_pengajuan']."'><i class='fa fa-close'></i></button></td>";
						echo "<td align='center'><input type='checkbox' name='check[".$no."]' class='chk_personal' data-nomor='".$no."' value='".$valx['no_pengajuan']."'></td>";
					echo "</tr>";

					$SUM_BUDGET += $total_budget;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th align='center'></th>
					<th align='center' colspan='11'>TOTAL BUDGET</th>
					<th class='text-right'><?=number_format($SUM_BUDGET,2);?></th>
					<th class='text-right' colspan='4'></th>
				</tr>
			</tfoot>
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