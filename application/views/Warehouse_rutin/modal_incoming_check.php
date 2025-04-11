
<div class="box-body">
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No PO</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po."/".$dated;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Penerimaan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
		</thead>
	</table><br>
	<input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
	<input type="hidden" name='id_header' id='id_header' value='<?= $id_header;?>'>
	<input type="hidden" name='gudang_tujuan' id='gudang_tujuan' value='<?= $gudang_tujuan;?>'>
	<input type="hidden" name='id_tujuan' id='id_tujuan' value='<?= $id_tujuan;?>'>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='4%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Nama Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Qty Order</th>
                <th class="text-center" style='vertical-align:middle;' width='9%'>Qty Diterima</th>
                <th class="text-center" style='vertical-align:middle;' width='9%'>Qty Kurang</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Qty Oke</th>
                <th class="text-center" style='vertical-align:middle;' width='9%'>Qty NG</th> 
				<th class="text-center" style='vertical-align:middle;' width='12%'>Keterangan</th>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				$totIn = $valx['qty_po'] - $valx['qty_in'];
				echo "<tr class='baris_".$No."'>";
					echo "<td rowspan='1' class='id_".$No."' align='center'>".$No."<input type='hidden' name='detail[$No][id]' value='".$valx['id']."'><input type='hidden' name='detail[$No][id2]' value='".$valx['id2']."'></td>";
					echo "<td rowspan='1' class='id_".$No."'>".strtoupper($valx['nm_material'])."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($valx['qty_order'])."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($valx['qty_oke'])."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($totIn)."</td>";
					echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_oke]' data-no='$No' class='form-control input-sm text-right maskM' value='".number_format($valx['qty_oke'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
					// echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][expired]' data-no='$No' class='form-control text-center input-sm text-left tanggal' readonly placeholder='Expired Date'></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
					// echo "<td align='center'>
							// <button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$No."'><i class='fa fa-plus'></i></button>
						// </td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'checkMaterial')).' ';
	?>
</div>
<style>
	.tanggal{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
        swal.close();
		$('.maskM').maskMoney();
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
    });
</script>