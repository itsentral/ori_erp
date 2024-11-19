
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
	<br>
    <input type="hidden" name='no_po' id='no_po' value='<?= $no_po;?>'>
    <input type="hidden" name='gudang' id='gudang' value='<?= $gudang;?>'>
	<input type="hidden" name='pic' id='pic' value='<?= $pic;?>'>
	<input type="hidden" name='note' id='note' value='<?= $note;?>'>
	<input type="hidden" name='no_ros' id='no_ros' value='<?= $no_ros;?>'>
	<input type="hidden" name='tanggal_trans' id='no_ros' value='<?= $tanggal_trans;?>'>
    <input type="hidden" name='adjustment' id='adjustment' value='IN'>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
                <th class="text-center" style='vertical-align:middle;'>Material ID</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty PO</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Qty belum dikirim</th>
                <th class="text-center" style='vertical-align:middle;' width='7%'>Qty Diterima</th> 
				<th class="text-center" style='vertical-align:middle;' width='17%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
            $Total1 = 0;
			$Total2 = 0;
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
                $Total1 += $valx['qty_po'] - $valx['qty_in'];
				$Total2 += $valx['qty_po'];
                
                $totIn = $valx['qty_po'] - $valx['qty_in'];
				
				echo "<tr>";
                    echo "<td align='center'>".$No."
                        <input type='hidden' name='addInMat[$No][no_po]' value='".$valx['no_po']."'>
                        <input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
                        <input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty_po']."'>
						<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
						<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
                    </td>";
                    echo "<td>".$valx['id_barang']."</td>";
                    echo "<td>".strtoupper($valx['nm_barang'])."</td>";
					echo "<td align='right'>".number_format($valx['qty_po'],2)."</td>";
                    echo "<td align='right' class='belumDiterima'>".number_format($totIn,2)."</td>";
                    echo "<td align='center'><input type='text' name='addInMat[$No][qty_in]' data-no='$No' class='form-control input-sm text-right maskM qtyDiterima' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
                    echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
                echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td colspan='2'><b>SUM TOTAL</b></td> 
				<td align='right'><b><?= number_format($Total2,2);?></b></td> 
				<td align='right'><b><?= number_format($Total1,2);?></b></td> 
                <td colspan='2'><b></b></td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='3'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'saveINMaterial'));
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
		$('.maskM').maskMoney();
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
    });

	$(document).on('keyup','.qtyDiterima',function(){
		let belumDiterima 	= getNum($(this).parent().parent().find('.belumDiterima').text().split(',').join(''))
		let qtyDiterima 	= getNum($(this).val().split(',').join(''))

		if(qtyDiterima > belumDiterima){
			$(this).val(belumDiterima)
		}
	})
</script>