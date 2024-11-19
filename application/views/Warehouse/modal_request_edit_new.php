
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
    <input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td> 
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Request</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Request (Pack)</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Unit Pack</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
            $Total2 = 0;
            $No=0;
			foreach($result AS $val => $valx){
                $No++;

                $request = 0;
                if($valx['qty_oke'] > 0 AND $valx['konversi'] > 0){
                    $request = $valx['qty_oke'] / $valx['konversi'];
                }
                
				echo "<tr>";
                    echo "<td align='center'>".$No."</td>";
                    echo "<td>".$valx['nm_material']."</td>";
                    echo "<td align='center'>
							<input type='hidden' name='detail[".$No."][id]' value='".$valx['id']."'>
							<input type='hidden' name='detail[".$No."][konversi]' value='".$valx['konversi']."'>
							<input type='hidden' name='detail[".$No."][edit_qty_before]' class='form-control input-md text-right' value='".$request."'>
							<input type='text' name='detail[".$No."][edit_qty]' class='form-control input-md text-center maskM' value='".$request."'>
						</td>";
                    echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing']))."</td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][keterangan]' data-no='$No' class='form-control input-md text-left' value='".$valx['keterangan']."'></td>";
				echo "</tr>";
			}
			?>
			<?php 
			}
            ?>
		</tbody>
	</table>
    <?php
		if(!empty($result)){
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Update','id'=>'edit_material_new'));
		}
	?>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
</style> 
<script>
	$(document).ready(function(){
        swal.close();
		$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			minDate: 0
		});
    });

</script>