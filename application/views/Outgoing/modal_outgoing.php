
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
	<br>
    <input type="hidden" name='tipe_out' id='no_po' value='<?= $tipe_out;?>'>
    <input type="hidden" name='id_customer' id='id_customer' value='<?= $id_customer;?>'>
    <input type="hidden" name='gudang' id='gudang' value='<?= $gudang;?>'>
    <input type="hidden" name='tujuan_out' id='pembeda' value='<?= $tujuan_out;?>'>
    <input type="hidden" name='id_gudang_origa' id='id_gudang_origa' value='<?= $gudang_origa;?>'>
    <input type="hidden" name='field_joint' id='field_joint' value='<?= $field_joint;?>'>
    <input type="hidden" name='adjustment' id='adjustment' value='OUT'>
	<?php if($tipe_out == 'non-so'){?>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Stock</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Permintaan (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Sub Gudang</th> 
				<th class="text-center" style='vertical-align:middle;' width='17%'>Keterangan</th> 
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th> 
			</tr>
		</thead>
		<tbody>
			<?php
            $id = 0;
            ?>
			<tr id='add_<?=$id;?>'>
                <td align='left'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                <td align='center' colspan='5'></td>
            </tr>
		</tbody>
	</table>
	<?php } ?>
    <?php if($tipe_out != 'non-so'){ ?>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
                <th class="text-center" style='vertical-align:middle;'>Material ID</th>
                <th class="text-center" style='vertical-align:middle;'>Category</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Stock (kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Qty Kirim (kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Sub Gudang</th>
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
				$UNIQ 	= $valx['id_material'].'-'.$gudang;
				$SISA 	= $valx['qty'] - $valx['qty_out'];
				$STOCK 	= (!empty($GET_STOCK[$UNIQ]))?$GET_STOCK[$UNIQ]:0;
				if($SISA > $STOCK){
					$SISA = $STOCK;
				}
				if($SISA < 0){
					$SISA = 0;
				}
				
				$Total2 += $SISA;
				echo "<tr>";
                    echo "<td align='center'>".$No."
                        <input type='hidden' name='addInMat[$No][no_po]' value='".$valx['id_bq']."'>
                        <input type='hidden' name='addInMat[$No][id_material_req]' value='".$valx['id_material']."'>
                        <input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
                        <input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty']."'>
						<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
						<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
						
                    </td>";
                    echo "<td>".get_name('raw_materials','idmaterial','id_material',$valx['id_material'])."</td>";
                    echo "<td>".get_name('raw_materials','nm_category','id_material',$valx['id_material'])."</td>";
                    echo "<td>".get_name('raw_materials','nm_material','id_material',$valx['id_material'])."</td>";
					echo "<td align='right'><input type='text' name='addInMat[$No][qty_stock]' value='".$STOCK."' data-no='$No' class='form-control input-md text-right autoNumeric2 stockval' readonly></td>";
					echo "<td align='right'><b><span class='text-green'>".number_format($SISA,4)."</span> / <span class='text-blue'>".number_format($valx['qty'],4)."</span></b></td>";
					echo "<td align='right'><input type='text' name='addInMat[$No][qty_in]' value='".$SISA."' data-no='$No' class='form-control input-md text-right autoNumeric2 qtyval'></td>";
                    echo "<td align='center'><select name='addInMat[$No][sub_gudang]' class='form-control'>";
                        foreach($subgudang AS $val2 => $valx2){
                            echo "<option value='".$valx2['id']."'>".strtoupper($valx2['nm_gudang'])."</option>";
                        }
                    echo "</td>";
                    echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left' value='".$valx['note']."'></td>";
                echo "</tr>";
			}
			if($field_joint == 'yes'){
				?>
				<tr id='add_<?=$No;?>'>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='left'><button type='button' class='btn btn-sm btn-success addPartCustom' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
					<td align='center' colspan='4'></td>
				</tr>
				<!-- <tr>
					<td><b></b></td>
					<td colspan='2'><b>SUM TOTAL</b></td> 
					<td align='right'><b><?= number_format($Total2, 4);?></b></td> 
					<td><b></b></td>
				</tr> -->
				<?php 
				}
			}
			else{
				echo "<tr>";
					echo "<td colspan='3'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php } ?>
    <?php
		// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Process To FinishGood','id'=>'saveFGMaterial')).' ';
		// if($id_customer != 'C100-2104003'){ //kode paten origa
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 10px 5px 0px;','value'=>'Save','content'=>'Process To SubGudang','id'=>'saveINMaterial')).' ';
		// }
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
		$('.autoNumeric2').autoNumeric('init', {mDec: '4', aPad: false});
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
    });
</script>