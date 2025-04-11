
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
    <input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<table id="my-grid" class="table" width="100%">
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
				<th class="text-center" style='vertical-align:middle;' width='15%'>Stock (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Request (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Edit Req (Kg)</th>
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
                $request = $valx['qty_oke'];
				$Total2 += $request;
                
				echo "<tr class='baris_".$No."'>";
                    echo "<td rowspan='1' class='id_".$No."' align='center'>".$No."</td>";
                    echo "<td rowspan='1' class='id_".$No."' >".$valx['nm_material']."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($valx['stock'],4)."</td>";
                    echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($request,4)."</td>";
                    echo "<td align='center'>
							<input type='hidden' name='detail[".$No."][id]' value='".$valx['id']."'>
							<input type='hidden' name='detail[".$No."][edit_qty_before]' id='cstkbef_".$No."_1' data-no='$No' class='form-control input-md text-right' value='".$request."'>
							<input type='text' name='detail[".$No."][edit_qty]' id='cstk_".$No."_1' data-no='$No' class='form-control input-md text-right maskM' value='".$request."'>
						</td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][keterangan]' data-no='$No' class='form-control input-md text-left' value='".$valx['keterangan']."'></td>";
				echo "</tr>";
			}
			?>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='6'><b>Material tidak berada di gudang yang dipilih, silahkan coba gudang yang lain.</b></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
		if(!empty($result)){
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Update Request','id'=>'edit_material'));
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