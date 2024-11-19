
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
	<br>
    <input type="hidden" name='tipe_out' id='no_po' value='<?= $tipe_out;?>'>
    <input type="hidden" name='tanda' id='tanda' value='<?= $tanda;?>'>
    <input type="hidden" name='gudang' id='gudang' value='<?= $gudang;?>'>
    <input type="hidden" name='tujuan_out' id='pembeda' value='<?= $tujuan_out;?>'>
	<input type="hidden" name='field_joint' id='field_joint' value='<?= $field_joint;?>'>
	<input type="hidden" name='no_spk_field' id='no_spk_field' value='<?= $no_spk_field;?>'>
    <input type="hidden" name='adjustment' id='adjustment' value='OUT'>

	<?php
	// echo "in: ".$QTY_SPK."<br>";
	// echo "out: ".$QTY_DONE."<br>";
	// print_r($result);
	?>
	<?php if($tipe_out != 'non-so'){ ?>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>No</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Material ID</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Category</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<?php
				if($field_joint == 'yes'){
				?>
				<th class="text-center" style='vertical-align:middle;'>No SPK / Spec</th>
				<?php
				}
				?>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Stock (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty Kirim (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Sub Gudang</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
				$Total1 = 0;
				$Total2 = 0;
				$No=0;
				if($tanda == 'TRS'){
					foreach($result AS $val => $valx){	
						$No++;
						$Total2 += $valx['qty'];
						
						echo "<tr>";
							echo "<td align='center'>".$No."
								<input type='hidden' name='addInMat[$No][no_po]' value='".$valx['kode_trans']."'>
								<input type='hidden' class='material' name='addInMat[$No][id_material_req]' value='".$valx['id_material']."'>
								<input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
								<input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty']."'>
								<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
								<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
								
							</td>";
							echo "<td>".get_name('raw_materials','idmaterial','id_material',$valx['id_material'])."</td>";
							echo "<td>".get_name('raw_materials','nm_category','id_material',$valx['id_material'])."</td>";
							echo "<td>".get_name('raw_materials','nm_material','id_material',$valx['id_material'])."</td>";

							$UNIQ_STOCK = $valx['id_material'].'-'.$valx['key_gudang'];
							$STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;

							$QTY_IN = $valx['qty'];
							if($STOCK < $QTY_IN AND $STOCK > 0){
								$QTY_IN = $STOCK;
							}

							if($STOCK < 0){
								$QTY_IN = 0;
							}

							// echo "<td align='right'>".number_format($STOCK,4)."</td>";
							echo "<td align='right'><input type='text' name='addInMat[$No][qty_stock]' value='".$STOCK."' readonly data-no='$No' class='form-control input-md text-right autoNumeric2 stockval'></td>";
							echo "<td align='right'>".number_format($valx['qty'],4)."</td>";
							echo "<td align='right'><input type='text' name='addInMat[$No][qty_in]' value='".$QTY_IN."' data-no='$No' class='form-control input-md text-right autoNumeric2 qtyval'></td>";
							echo "<td><select name='addInMat[$No][sub_gudang]' class='form-control chosen_select sub_gudang'>";
								foreach($subgudang AS $val2 => $valx2){
									$selected = ($valx2['id'] == $valx['key_gudang'])?'selected':'';
									echo "<option value='".$valx2['id']."' ".$selected.">".strtoupper($valx2['nm_gudang'])."</option>";
								}
							echo "</td>";
							echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
						echo "</tr>";
					}
				}
				else{
					if($field_joint == 'yes'){
						if($QTY_DONE < $QTY_SPK){
							foreach($result AS $val => $valx){
								$No++;
								$SISA = $valx['qty'] - $valx['qty_out'];
								$Total2 += $SISA;
								$NO_SPK = '';
								$PRODUCT = '';
								$SPEC = '';
								if(!empty($valx['id_milik'])){
									$NO_SPK = get_name('so_detail_header','no_spk','id',$valx['id_milik']);
									$PRODUCT = strtoupper(get_name('so_detail_header','id_category','id',$valx['id_milik']));
									$SPEC = spec_bq2($valx['id_milik']);
								}
								echo "<tr>";
									echo "<td align='center'>".$No."
										<input type='hidden' name='addInMat[$No][no_po]' value='".$valx['id_bq']."'>
										<input type='hidden' class='material' name='addInMat[$No][id_material_req]' value='".$valx['id_material']."'>
										<input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
										<input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty']."'>
										<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
										<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
										
									</td>";
									echo "<td>".get_name('raw_materials','idmaterial','id_material',$valx['id_material'])."</td>";
									echo "<td>".get_name('raw_materials','nm_category','id_material',$valx['id_material'])."</td>";
									echo "<td>".get_name('raw_materials','nm_material','id_material',$valx['id_material'])."</td>";
									if($field_joint == 'yes'){
										echo "<td align='left'><b>".$NO_SPK."</b>, ".$PRODUCT." [".$SPEC."]</td>";
									}

									$UNIQ_STOCK = $valx['id_material'].'-'.$gudang;
									$STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;

									$QTY_IN = $SISA;
									if($STOCK < $QTY_IN AND $STOCK > 0){
										$QTY_IN = $STOCK;
									}

									if($STOCK <= 0){
										$QTY_IN = 0;
									}

									// echo "<td align='right'>".number_format($STOCK,4)."</td>";
									echo "<td align='right'><input type='text' name='addInMat[$No][qty_stock]' value='".$STOCK."' readonly data-no='$No' class='form-control input-md text-right autoNumeric2 stockval'></td>";
									echo "<td align='right'><b><span class='text-green'>".number_format($SISA,4)."</span> / <span class='text-blue'>".number_format($valx['qty'],4)."</span></b></td>";
									echo "<td align='right'><input type='text' name='addInMat[$No][qty_in]' value='".$QTY_IN."' data-no='$No' class='form-control input-md text-right autoNumeric2 qtyval'></td>";
									echo "<td><select name='addInMat[$No][sub_gudang]' class='form-control chosen_select sub_gudang'>";
										foreach($subgudang AS $val2 => $valx2){
											$selected = (!empty($gudang) AND $gudang == $valx2['id'])?'selected':'';
											echo "<option value='".$valx2['id']."' ".$selected.">".strtoupper($valx2['nm_gudang'])."</option>";
										}
									echo "</td>";
									echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left' value='".$valx['note']."'></td>";
								echo "</tr>";
							}
						}
					}
					else{
						foreach($result AS $val => $valx){
							$No++;
							$SISA = $valx['qty'] - $valx['qty_out'];
							$Total2 += $SISA;
							$NO_SPK = '';
							$PRODUCT = '';
							$SPEC = '';
							if(!empty($valx['id_milik'])){
								$NO_SPK = get_name('so_detail_header','no_spk','id',$valx['id_milik']);
								$PRODUCT = strtoupper(get_name('so_detail_header','id_category','id',$valx['id_milik']));
								$SPEC = spec_bq2($valx['id_milik']);
							}
							echo "<tr>";
								echo "<td align='center'>".$No."
									<input type='hidden' name='addInMat[$No][no_po]' value='".$valx['id_bq']."'>
									<input type='hidden' class='material' name='addInMat[$No][id_material_req]' value='".$valx['id_material']."'>
									<input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
									<input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty']."'>
									<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
									<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
									
								</td>";
								echo "<td>".get_name('raw_materials','idmaterial','id_material',$valx['id_material'])."</td>";
								echo "<td>".get_name('raw_materials','nm_category','id_material',$valx['id_material'])."</td>";
								echo "<td>".get_name('raw_materials','nm_material','id_material',$valx['id_material'])."</td>";
								if($field_joint == 'yes'){
									echo "<td align='left'><b>".$NO_SPK."</b>, ".$PRODUCT." [".$SPEC."]</td>";
								}

								$UNIQ_STOCK = $valx['id_material'].'-'.$gudang;
								$STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;

								$QTY_IN = $SISA;
								if($STOCK < $QTY_IN AND $STOCK > 0){
									$QTY_IN = $STOCK;
								}

								if($STOCK <= 0){
									$QTY_IN = 0;
								}

								// echo "<td align='right'>".number_format($STOCK,4)."</td>";
								echo "<td align='right'><input type='text' name='addInMat[$No][qty_stock]' value='".$STOCK."' readonly data-no='$No' class='form-control input-md text-right autoNumeric2 stockval'></td>";
								echo "<td align='right'><b><span class='text-green'>".number_format($SISA,4)."</span> / <span class='text-blue'>".number_format($valx['qty'],4)."</span></b></td>";
								echo "<td align='right'><input type='text' name='addInMat[$No][qty_in]' value='".$QTY_IN."' data-no='$No' class='form-control input-md text-right autoNumeric2 qtyval'></td>";
								echo "<td><select name='addInMat[$No][sub_gudang]' class='form-control chosen_select sub_gudang'>";
									foreach($subgudang AS $val2 => $valx2){
										$selected = (!empty($gudang) AND $gudang == $valx2['id'])?'selected':'';
										echo "<option value='".$valx2['id']."' ".$selected.">".strtoupper($valx2['nm_gudang'])."</option>";
									}
								echo "</td>";
								echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left' value='".$valx['note']."'></td>";
							echo "</tr>";
						}
					}
				}
				if($field_joint == 'yes'){
					?>
					<tr id='add_<?=$No;?>'>
						<td align='center'></td>
						<td align='center'></td>
						<td align='center'></td>
						<td align='center'></td>
						<td align='left'><button type='button' class='btn btn-sm btn-success addPartCustom' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
						<td align='center' colspan='5'></td>
					</tr>
					<tr>
						<td colspan='3'></td>
						<td align='right' colspan='4'><b>Input material tersebut untuk berapa kit ?</b></td> 
						<td align='center'><input type="text" id='qty_kit' name='qty_kit' class='form-control input-sm autoNumeric2 text-center' placeholder='Qty Kit'></td> 
						<td colspan='2' align='right'><b>Qty SPK Field Joint: <span class='text-red'>(<?=$QTY_DONE;?>/<?=$QTY_SPK;?>)</span></b></td>
					</tr>
					<?php 
				}
			}
			else{
				echo "<tr>";
					echo "<td colspan='8'>Data sudah di proccess !!!</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php } ?>
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
    <?php
	echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Process To FinishGood','id'=>'saveFGMaterial'));
	if(!empty($result) AND $QTY_DONE != $QTY_SPK){
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 10px 5px 0px;','value'=>'Save','content'=>'Confirm','id'=>'saveINMaterial'));
		if($field_joint == 'yes'){
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-warning','style'=>'min-width:100px; float:right; margin: 5px 10px 5px 0px;','value'=>'Save','content'=>'Save Draf','id'=>'saveSementara'));
		}
	}
	?>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
    /* .chosen-container{
		width: 100% !important;
		text-align : left !important;
	} */
</style> 
<script>
	$(document).ready(function(){
        swal.close();
    });
</script>