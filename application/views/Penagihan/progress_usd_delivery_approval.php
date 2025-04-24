<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
	<thead>
		<tr class='bg-blue'>
			<td class="text-left" colspan='15'><b>PRODUCT USD</b></td>
		</tr>
		<tr class='bg-blue'>
			<th class="text-center" width='2%'>#</th>
			<th class="text-center">Product</th>
			<th class="text-center">Item No</th>
			<th class="text-center">PO Description</th>
			<th class="text-center" width='6%'>Dim 1</th>
			<th class="text-center" width='6%'>Dim 2</th>
			<th class="text-center" width='5%'>Lin</th>
			<th class="text-center" width='5%'>Pre</th>
			<th class="text-center" width='10%'>Specification</th>
			<th class="text-center" width='7%'>Unit Price</th>
			<th class="text-center" width='5%'>Qty Total</th>
			<th class="text-center" width='5%'>Qty Sisa</th>
			<th class="text-center" width='5%' nowrap>Qty-Inv</th>
			<th class="text-center" width='6%'>Unit</th>
			<th class="text-center" width='10%'>Total Price</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$numb=0;
		$SUM = 0;$total_cogs=0;
		foreach($getDetail AS $val => $valx){ $numb++;
			$pr		= 'pr';
			$numb1	= $pr.$numb;
			if(isset($valx['harga_total'])){
				$HrgTot	= $valx['harga_total'];
				$dataSum = $HrgTot;
				$unitT = $valx['unit'];
				$sisa_inv = $valx['qty_delivery'];
				$harga_sat	= round($valx['harga_satuan'],2);
				$harga_tot	= round($valx['harga_total'],2);
				$SUM += ($harga_tot);

			}else{
				if($valx['qty'] <> 0){
					$HrgTot  	= $valx['total_deal_usd'];
					$dataSum	= $HrgTot;
				}
				if($valx['product'] == 'pipe' OR $valx['product'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				$sisa_inv = $valx['qty_delivery'];//$valx['qty'] - $valx['qty_inv'];
				$harga_sat	= round($dataSum / $valx['qty'],2);
				$harga_tot	= round($harga_sat * $sisa_inv,2);
				$SUM += ($harga_tot);
			}
			$total_cogs=($total_cogs+$valx['cogs']);
			?>
			<tr id='tr_<?= $numb;?>' >
				<td align='center'>
					<input type="hidden" id="ck_<?= $numb;?>" name="tr1_<?= $numb;?>" value="<?= $numb;?>">
					<input type="hidden" id="data_ipp_<?=$numb?>" name="data1[<?=$numb?>][no_ipp]" value="<?=$valx['no_ipp']?>">
					<input type="hidden" id="data_so_<?=$numb?>" name="data1[<?=$numb?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
					<input type="hidden" id="data_idmilik_<?=$numb?>" name="data1[<?=$numb?>][id_milik]" value="<?=$valx['id_milik']?>">
					<input type="hidden" id="data_cogs_<?=$numb?>" name="data1[<?=$numb?>][cogs]" value="<?=$valx['cogs']?>">
				</td>
				<td><input type="text" class="form-control input-sm" id="material_name1_<?= $numb;?>" name="data1[<?=$numb ?>][material_name1]" value="<?=strtoupper(str_replace('"','',$valx['product'])); ?>" readonly title='<?=get_nomor_so($valx['no_ipp']);?>' tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm" id="product_cust<?= $numb;?>" name="data1[<?=$numb ?>][product_cust]" value="<?=strtoupper(str_replace('"','',$valx['customer_item'])); ?>" readonly title='<?=get_nomor_so($valx['no_ipp']);?>' tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm" id="product_desc<?= $numb;?>" name="data1[<?=$numb ?>][product_desc]" value="<?=strtoupper(str_replace('"','',$valx['desc'])); ?>" readonly title='<?=get_nomor_so($valx['no_ipp']);?>' tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm text-right" id="diameter_1_<?= $numb;?>" name="data1[<?=$numb ?>][diameter_1]" value="<?=$valx['dim1']; ?>" readonly  tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm text-right" id="diameter_2_<?= $numb;?>" name="data1[<?=$numb ?>][diameter_2]" value="<?=$valx['dim2']; ?>" readonly  tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm text-right" id="liner_<?= $numb;?>" name="data1[<?=$numb ?>][liner]" value="<?=$valx['liner']; ?>" readonly  tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm text-center" id="pressure_<?= $numb;?>" name="data1[<?=$numb ?>][pressure]" value="<?=$valx['pressure']; ?>" readonly  tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm" id="id_milik_<?= $numb;?>" name="data1[<?=$numb ?>][spesifikasi]" value="<?=spec_bq($valx['id_milik']); ?>" readonly  tabindex="-1"></td>
				<td>
					<input type='hidden' name="data1[<?=$numb ?>][id]" value='<?=$valx['id'];?>'>
					<input type="text" class="form-control input-sm text-right divide" id="harga_sat_<?= $numb;?>" name="data1[<?=$numb ?>][harga_sat]" value="<?=set_value('harga_sat', isset($harga_sat) ? $harga_sat : '0'); ?>" readonly  tabindex="-1">
				</td>
				<td><input type="text" class="form-control input-sm text-center" id="qty_ori_<?= $numb;?>" data-nomor='<?=$numb ?>' name="data1[<?=$numb ?>][qty_ori]" value="<?=$valx['qty_total']; ?>" readonly tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm text-center" id="qty_belum_<?= $numb;?>" data-nomor='<?=$numb ?>' name="data1[<?=$numb ?>][qty_belum]" value="<?=$valx['qty_inv']; ?>" readonly tabindex="-1"></td>
				<td><input type="text" class="form-control input-sm qty_product text-center" id="qty_<?= $numb;?>" data-nomor='<?=$numb ?>' name="data1[<?=$numb ?>][qty]" value="<?=$valx['qty_delivery']; ?>"></td>
				<td><input type="text" class="form-control input-sm text-center" id="unit1_<?= $numb;?>" name="data1[<?=$numb ?>][unit1]" value="<?=$unitT; ?>" readonly tabindex="-1"></td>
				<td>
					<input type="text" class="form-control text-right input-sm divide amount1" id="harga_tot_<?=$numb ?>" name="data1[<?=$numb ?>][harga_tot]" value="<?=$harga_tot; ?>" readonly tabindex="-1">
				</td>
			</tr>
		<?php
		}
		?>
		<tr class='FootColor'>
			<td colspan='14'><b>TOTAL COST  OF PRODUCT</b></td>
			<td align='center'>
				<?php
				$tot_product2=round($SUM,2);
				?>
				<input type="text" class="form-control input-sm result1 text-right divide" id="tot_product" name="tot_product" value="<?=set_value('tot_product', isset($tot_product2) ? $tot_product2 : '0'); ?>" readonly tabindex="-1">
				<input type="hidden" value="<?=$total_cogs; ?>" name="total_cogs">
			</td>
		</tr>
	</tbody>
	<?php
	$SUM_NONFRP = 0;
	if(!empty($non_frp)){
		echo "<tbody>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='15'><b>BILL OF QUANTITY NON FRP</b></td>";
			echo "</tr>";
			echo "<tr class='bg-blue'>";
				echo "<th class='text-center'>#</th>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Description</th>";
				echo "<th class='text-center'>Qty Total</th>";
				echo "<th class='text-center'>Qty Sisa</th>";
				echo "<th class='text-center'>Qty-Inv</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
			$numb2 =0;
			foreach($non_frp AS $val => $valx){
				$numb2++;$harga_tot2=0;
				$checked="";$readonly=" disabled";
				if(isset($valx['total_deal_usd'])){
					$harga_sat2= round($valx['total_deal_usd']/$valx['qty_total'],2);
				}else{
					$harga_sat2= round($valx['harga_satuan'],2);
				}
				if(isset($valx['checked'])){
					if($valx['checked']!="") { 
						$checked="checked";$readonly="";
						$harga_tot2=round($harga_sat2*$valx['qty_delivery'],2);
						$SUM_NONFRP += ($harga_tot2);
					}
				}
				
				
				
				
				?>
				<tr id='tr1_<?= $numb2;?>' >
					<td align='center'>
						<input type="checkbox" checked id="ck1_<?= $numb2;?>" name="tr2_<?= $numb2;?>" value="<?=$numb2;?>" onclick="showdata('1',<?= $numb2;?>)">
						<input type="hidden" id="data1_ipp_<?=$numb2?>" name="data2[<?=$numb2?>][no_ipp]" value="<?=$valx['no_ipp']?>">
						<input type="hidden" id="data1_so_<?=$numb2?>" name="data2[<?=$numb2?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
						<input type="hidden" id="data1_idmilik_<?=$numb2?>" name="data2[<?=$numb2?>][id_milik]" value="<?=$valx['id_milik']?>">
					</td>
					<td colspan='7'>
						<?php
						if(isset($valx['id_material'])){
							$material_name2= get_nomor_so($valx['no_ipp']).' / '.strtoupper(get_name_acc($valx['id_material']));
						}else{
							$material_name2= $valx['nm_material'];
						}
						?>
						<input type="text" class="form-control input-sm" id="material_name2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][material_name2]" value="<?=set_value('material_name2', isset($material_name2) ? $material_name2 : ''); ?>" readonly tabindex="-1">
					</td>
					<td><input type="text" class="form-control" id="material_desc2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][material_desc2]" value="<?=set_value('material_desc2', isset($valx['desc']) ? $valx['desc'] : ''); ?>" readonly tabindex="-1" ></td>
					<td>
					   <input type="text" class="form-control text-right input-sm divide" data-nomor='<?=$numb2 ?>' id="qty2_ori_<?=$numb2 ?>" name="data2[<?=$numb2 ?>][qty2_ori]" value="<?=set_value('qty2_ori', isset($valx['qty_total']) ? $valx['qty_total'] : '0'); ?>" readonly tabindex="-1">
					</td>
					<td>
					   <input type="text" class="form-control text-right input-sm divide" data-nomor='<?=$numb2 ?>' id="qty2_belum_<?=$numb2 ?>" name="data2[<?=$numb2 ?>][qty2_belum]" value="<?=set_value('qty2_belum', isset($valx['qty_inv']) ? $valx['qty_inv'] : '0'); ?>" readonly tabindex="-1">
					</td>
					<td>
					   <input type="text" class="form-control qty_bq input-sm text-right divide" data-nomor='<?=$numb2 ?>' id="qty2_<?=$numb2 ?>" name="data2[<?=$numb2 ?>][qty2]" value="<?=set_value('qty2', isset($valx['qty_delivery']) ? $valx['qty_delivery'] : '0'); ?>" <?=$readonly?>>
					</td>
					<td>
						<?php $unit2= strtoupper($valx['satuan']); ?>
						<input type="text" class="form-control text-center input-sm" id="unit2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][unit2]" value="<?=set_value('unit2', isset($unit2) ? $unit2 : ''); ?>" readonly  tabindex="-1">
					</td>
					<td>
						<input type="text" class="form-control text-right divide input-sm" id="harga_sat2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][harga_sat2]" value="<?=set_value('harga_sat2', isset($harga_sat2) ? $harga_sat2 : '0'); ?>" readonly tabindex="-1">						
					</td>
					<td>
						<input type="text" class="form-control text-right divide input-sm amount2" id="harga_tot2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][harga_tot2]" value="<?=set_value('harga_tot2', isset($harga_tot2) ? $harga_tot2 : '0'); ?>" readonly tabindex="-1" >						
					</td>
				</tr>
				<?php
			}
			?>
			<tr class='FootColor'>
				<td colspan='14'><b>TOTAL BILL OF QUANTITY NON FRP</b></td>
				<td align="right">
					<?php $total_bq_nf= round($SUM_NONFRP,2); 
					
					
					?>
					<input type="text" class="form-control result2 text-right  divide input-sm" id="total_bq_nf" name="total_bq_nf" value="<?=set_value('total_bq_nf', isset($total_bq_nf) ? $total_bq_nf : '0'); ?>" readonly tabindex="-1" >
				</td>
			</tr>
		</tbody>
	<?php
	}

	$SUM_MAT = 0;
	if(!empty($material)){
		echo "<thead>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='15'><b>MATERIAL</b></td>";
			echo "</tr>";
			echo "<tr class='bg-blue'>";
				echo "<th class='text-center'>#</th>";
				echo "<th class='text-center' colspan='4'>Material Name</th>";
				echo "<th class='text-center' colspan='2'>Item No</th>";
				echo "<th class='text-center' colspan='2'>PO Description</th>";
				echo "<th class='text-center'>Weight Total</th>";
				echo "<th class='text-center'>Weight Sisa</th>";
				echo "<th class='text-center'>Weight-Inv</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody class='body_x'>";
			$numb3 =0;
			foreach($material AS $val => $valx){
				$numb3++;$harga_tot3=0;
				$checked="";$readonly=" disabled";
				if(isset($valx['total_deal_usd'])){
					$harga_sat3= round($valx['total_deal_usd']/$valx['qty_total'],2);
				}else{
					$harga_sat3= round($valx['harga_satuan'],2);
				}
				if(isset($valx['checked'])){
					if($valx['checked']!="") { 
						$checked="checked";$readonly="";
						$harga_tot3=round($harga_sat3*$valx['qty_delivery'],2);
						$SUM_MAT += ($harga_tot3);
					}
				}
				?>
				<tr id='tr2_<?= $numb3;?>' >
					<td align='center'>
						<input type="checkbox" <?=$checked?> id="ck2_<?= $numb3;?>" name="tr3_<?= $numb3;?>" value="<?= $numb3;?>" onclick="showdata('2',<?= $numb3;?>)">
						<input type="hidden" id="data2_ipp_<?=$numb3?>" name="data3[<?=$numb3?>][no_ipp]" value="<?=$valx['no_ipp']?>">
						<input type="hidden" id="data2_so_<?=$numb3?>" name="data3[<?=$numb3?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
						<input type="hidden" id="data2_idmilik_<?=$numb3?>" name="data3[<?=$numb3?>][id_milik]" value="<?=$valx['id_milik']?>">
					</td>
					<td colspan='4'>
						<?php
						$material_name3= get_nomor_so($valx['no_ipp']).' / '.strtoupper($valx['nm_material']);
						?>
						<input type="text" class="form-control input-sm" id="material_name3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][material_name3]" value="<?=set_value('material_name3', isset($material_name3) ? $material_name3 : ''); ?>" readonly tabindex="-1" >
					</td>
					<td colspan='2'>
						<input type="text" class="form-control input-sm" id="customer_item3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][product_cust]" value="<?=$valx['customer_item']?>" readonly tabindex="-1" >
					</td>
					<td colspan='2'>
						<input type="text" class="form-control input-sm" id="desc3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][product_desc]" value="<?=$valx['desc']?>" readonly tabindex="-1" >
					</td>
					<td>
					   <input type="text" class="form-control text-right input-sm divide" id="qty3_ori_<?= $numb3;?>" data-nomor='<?=$numb3 ?>'  name="data3[<?=$numb3 ?>][qty3_ori]" value="<?=set_value('qty3_ori', isset($valx['qty_total']) ? $valx['qty_total'] : '0'); ?>" readonly tabindex="-1">
					</td>
					<td>
					   <input type="text" class="form-control text-right input-sm divide" id="qty3_belum_<?= $numb3;?>" data-nomor='<?=$numb3 ?>'  name="data3[<?=$numb3 ?>][qty3_belum]" value="<?=set_value('qty3_belum', isset($valx['qty_inv']) ? $valx['qty_inv'] : '0'); ?>" readonly tabindex="-1">
					</td>
					<td>
					   <input type="text" class="form-control qty_material text-right divide input-sm" id="qty3_<?= $numb3;?>" data-nomor='<?=$numb3 ?>'  name="data3[<?=$numb3 ?>][qty3]" value="<?=set_value('qty3', isset($valx['qty_delivery']) ? $valx['qty_delivery'] : '0'); ?>" <?=$readonly ?>>
					</td>
					<td>
						<?php $unit3= strtoupper($valx['satuan']); ?>
						<input type="text" class="form-control text-center input-sm" id="unit3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][unit3]" value="<?=set_value('unit3', isset($unit3) ? $unit3 : ''); ?>" readonly tabindex="-1" >
					</td>
					<td>
						<input type="text" class="form-control text-right input-sm divide" id="harga_sat3<?= $numb3;?>" name="data3[<?=$numb3 ?>][harga_sat3]" value="<?=set_value('harga_sat3', isset($harga_sat3) ? $harga_sat3 : '0'); ?>" readonly tabindex="-1" >
					</td>
					<td>
						<input type="text" class="form-control text-right input-sm divide amount3" id="harga_tot3<?=$numb3 ?>" name="data3[<?=$numb3 ?>][harga_tot3]" value="<?=set_value('harga_tot3', isset($harga_tot3) ? $harga_tot3 : '0'); ?>" readonly tabindex="-1" >
					</td>
				</tr>
			<?php
			}
			?>
			<tr class='FootColor'>
				<td colspan='14'><b>TOTAL MATERIAL</b></td>
				<td align="right">
					<?php
					$total_material= round($SUM_MAT,2);
					?>
					<input type="text" class="form-control result3 text-right input-sm divide" id="total_material3" name="total_material" value="<?=set_value('total_material', isset($total_material) ? $total_material : '0'); ?>" readonly tabindex="-1" >
				</td>
			</tr>
		</tbody>
	<?php
	}
	$SUM1=0;
	$SUM2=0;
	$SUM3=0;
	$SUM4=0;

	//ENGENERING
	$SUM1=0;
	if(!empty($getEngCost)){
	?>
		<thead>
			<tr class='bg-blue'>
				<td class="text-left headX HeaderHr" colspan='15'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center">#</th>
				<th class="text-center" colspan='11'>Item Product</th>
				<th class="text-center">Total Price SO</th>
				<th class="text-center">Total Price Sisa</th>
				<th class="text-center">Total Price Inv</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$no1=0;
			$SUM1=0;
			$numb4=0;
			foreach($getEngCost AS $val => $valx){
				$no1++;
				$numb4++;
				$checked="";$readonly=" disabled";$harga_tot4=0;
				$harga_total_so=(isset($valx['total_deal_usd'])?$valx['total_deal_usd']:$valx['harga_total_so']);
				$harga_sisa_so=(isset($valx['total_deal_usd'])?($valx['total_deal_usd']-$valx['total_deal_inv']):$valx['harga_sisa_so']);
 				if(isset($valx['checked'])){
					if($valx['checked']!="") { 
						$checked="checked";$readonly="";
						$harga_tot4= round($valx['harga_total'],2);
						$SUM1 += ($harga_tot4);
					}
				}
				$material_name4= get_nomor_so($valx['no_ipp']).' / '.strtoupper('ENGINERING COST').' - '.get_name('cost_project_detail','caregory_sub','id',$valx['id_milik']);
				$unit4= strtoupper('-');
				?>
				<tr id='tr3_<?= $numb4;?>' >
					<td align='center'>
						<input type="checkbox" <?=$checked?> id="ck3_<?= $numb4;?>" name="tr4_<?= $numb4;?>" value="<?= $numb4;?>" onclick="showdata('3',<?= $numb4;?>)">
						<input type="hidden" id="data3_ipp_<?=$numb4?>" name="data4[<?=$numb4?>][no_ipp]" value="<?=$valx['no_ipp']?>">
						<input type="hidden" id="data3_so_<?=$numb4?>" name="data4[<?=$numb4?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
						<input type="hidden" id="data3_idmilik_<?=$numb4?>" name="data4[<?=$numb4?>][id_milik]" value="<?=$valx['id_milik']?>">
					</td>
					<td colspan='11'>
						<input type="text" class="form-control input-sm" id="material_name4" name="data4[<?=$numb4 ?>][material_name4]" value="<?=set_value('material_name4', isset($material_name4) ? $material_name4 : ''); ?>" readonly tabindex="-1" >
					</td>
					<td><input type="text" class="form-control text-right input-sm divide" id="harga_tot4_ori_<?=$numb4 ?>" data-nomor='<?=$numb4 ?>' name="data4[<?=$numb4 ?>][harga_tot4_ori]" value="<?=$harga_total_so; ?>" readonly tabindex="-1"></td>
					<td><input type="text" class="form-control text-right input-sm divide" id="harga_tot4_sisa_<?=$numb4 ?>" data-nomor='<?=$numb4 ?>' name="data4[<?=$numb4 ?>][harga_tot4_sisa]" value="<?=$harga_sisa_so; ?>" readonly tabindex="-1"></td>
					<td>
						<input type="hidden" id="unit4" name="data4[<?=$numb4 ?>][unit4]" value="<?=set_value('unit4', isset($unit4) ? $unit4 : '0'); ?>" >
						<input type="text" class="form-control text-right harga_tot4 changeAll input-sm divide" id="harga_tot4<?=$numb4 ?>" data-nomor='<?=$numb4 ?>' name="data4[<?=$numb4 ?>][harga_tot4]" <?=$readonly ?> value="<?=set_value('harga_tot4', isset($harga_tot4) ? $harga_tot4 : '0'); ?>" >
					</td>
				</tr>
			<?php
			}
			?>
			<tr id='tr3X' class='FootColor'>
				<td colspan='14'><b>TOTAL ENGINEERING COST</b></td>
				<td align="right">
					<?php
					$total_enginering= round($SUM1,2);
					?>
					<input type="text" class="form-control text-right result4 changeAll input-sm divide" id="total_enginering1" name="total_enginering" value="<?=set_value('total_enginering', isset($total_enginering) ? $total_enginering : ''); ?>" readonly tabindex="-1" >
				</td>
			</tr>
		</tbody>
	<?php
	}
	$SUM2=0;
	if(!empty($getPackCost)){
		?>
		<thead>
			<tr class='bg-blue'>
				<td class="text-left headX HeaderHr" colspan='15'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center">#</th>
				<th class="text-center" colspan='11'>Category</th>
				<th class="text-center">Total Price SO</th>
				<th class="text-center">Total Price Sisa</th>
				<th class="text-center">Total Price Inv</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$SUM2=0;
			$numb5=0;
			foreach($getPackCost AS $val => $valx){
				$numb5++;$checked='';$harga_tot5=0;
				$readonly=" disabled";
				$harga_total_so=(isset($valx['total_deal_usd'])?$valx['total_deal_usd']:$valx['harga_total_so']);
				$harga_sisa_so=(isset($valx['total_deal_usd'])?($valx['total_deal_usd']-$valx['total_deal_inv']):$valx['harga_sisa_so']);

 				if(isset($valx['checked'])){
					if($valx['checked']!="") { 
						$checked="checked";$readonly="";
						$harga_tot5= round($valx['harga_total'],2);
						$SUM2 += $harga_tot5;
					}
				}
				$material_name5= get_nomor_so($valx['no_ipp']).' / '.strtoupper('PACKING COST').' - '.get_name('cost_project_detail','caregory_sub','id',$valx['id_milik']);
				$unit5= strtoupper('-');
				?>
				<tr id='tr4_<?= $numb5;?>' >
					<td align='center'>
						<input type="checkbox" <?=$checked ?> id="ck4_<?= $numb5;?>" name="tr5_<?= $numb5;?>" value="<?= $numb5;?>" onclick="showdata('4',<?= $numb5;?>)">
						<input type="hidden" id="data4_ipp_<?=$numb5?>" name="data5[<?=$numb5?>][no_ipp]" value="<?=$valx['no_ipp']?>">
						<input type="hidden" id="data4_so_<?=$numb5?>" name="data5[<?=$numb5?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
						<input type="hidden" id="data4_idmilik_<?=$numb5?>" name="data5[<?=$numb5?>][id_milik]" value="<?=$valx['id_milik']?>">
					</td>
					<td colspan='11'>
						<input type="text" class="form-control input-sm" id="material_name5" name="data5[<?=$numb5 ?>][material_name5]" value="<?=set_value('material_name5', isset($material_name5) ? $material_name5 : ''); ?>" readonly tabindex="-1" >
					</td>
					<td><input type="text" class="form-control text-right input-sm divide" id="harga_tot5_ori_<?=$numb5 ?>" data-nomor='<?=$numb5 ?>' name="data5[<?=$numb5 ?>][harga_tot5_ori]" value="<?=$harga_total_so?>" readonly tabindex="-1" ></td>
					<td><input type="text" class="form-control text-right input-sm divide" id="harga_tot5_sisa_<?=$numb5 ?>" data-nomor='<?=$numb5 ?>' name="data5[<?=$numb5 ?>][harga_tot5_sisa]" value="<?=$harga_sisa_so ?>" readonly tabindex="-1" ></td>
					<td>
						<input type="hidden" class="form-control" id="unit5" name="data5[<?=$numb5 ?>][unit5]" value="<?=set_value('unit5', isset($unit5) ? $unit5 : ''); ?>" readonly >
						<input type="text" class="form-control text-right harga_tot5 input-sm divide" id="harga_tot5<?=$numb5 ?>" data-nomor='<?=$numb5 ?>' name="data5[<?=$numb5 ?>][harga_tot5]" value="<?=(isset($harga_tot5) ? $harga_tot5 : '0'); ?>" <?=$readonly ?>>
					</td>
				</tr>
				<?php
			}
			?>
			<tr id='tr4X' class='FootColor'>
				<td colspan='14'><b>TOTAL PACKING COST</b></td>
				<td align="right">
					<?php
					$total_packing= round($SUM2,2);
					?>
					<input type="text" class="form-control text-right result5 changeAll input-sm divide" id="total_packing1" name="total_packing" value="<?=(isset($total_packing) ? $total_packing : '0'); ?>" readonly tabindex="-1" >					
					</td>
			</tr>
		</tbody>
	<?php
	}
	//TRUCKING
	$SUM3=0;
	if(!empty($getTruck)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left headX HeaderHr" colspan='15'><b>TRUCKING</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center">#</th>
				<th class="text-center" colspan='11'>Category</th>
				<th class="text-center">Total Price SO</th>
				<th class="text-center">Total Price Sisa</th>
				<th class="text-center">Total Price Inv</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$SUM3=0;
			$nomor=0;
			foreach($getTruck AS $val => $valx){
				$nomor++;$harga_tot6=0; $checked=''; $readonly=" disabled";
				$category = get_name('cost_project_detail','category','id',$valx['id_milik']);
				$name_add = "";
				if($category == 'lokal'){
					$name_add = " - ".get_name('cost_project_detail','tujuan','id',$valx['id_milik']);;
				}
				$material_name6= get_nomor_so($valx['no_ipp']).' / '.strtoupper('SHIPPING COST').' - '.get_name('cost_project_detail','caregory_sub','id',$valx['id_milik']).strtoupper($name_add);
				$unit6				= strtoupper('-');
				$harga_total_so=(isset($valx['total_deal_usd'])?$valx['total_deal_usd']:$valx['harga_total_so']);
				$harga_sisa_so=(isset($valx['total_deal_usd'])?($valx['total_deal_usd']-$valx['total_deal_inv']):$valx['harga_sisa_so']);;

 				if(isset($valx['checked'])){
					if($valx['checked']!="") { 
						$checked="checked";$readonly="";
						$harga_tot6	= round($valx['harga_total'],2);
						$SUM3 += $harga_tot6;
					}
				}
				?>
				<tr id='tr5_<?= $nomor;?>' >
					<td align='center'>
						<input type="checkbox" <?=$checked ?> id="ck5_<?= $nomor;?>" name="tr6_<?= $nomor;?>" value="<?= $nomor;?>" onclick="showdata('5',<?= $nomor;?>)">
						<input type="hidden" id="data5_ipp_<?=$nomor?>" name="data6[<?=$nomor?>][no_ipp]" value="<?=$valx['no_ipp']?>">
						<input type="hidden" id="data5_so_<?=$nomor?>" name="data6[<?=$nomor?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
						<input type="hidden" id="data5_idmilik_<?=$nomor?>" name="data6[<?=$nomor?>][id_milik]" value="<?=$valx['id_milik']?>">
					</td>
					<td colspan='11'>
						<input type="text" class="form-control input-sm" id="material_name6" name="data6[<?=$nomor ?>][material_name6]" value="<?=set_value('material_name6', isset($material_name6) ? $material_name6 : ''); ?>" readonly tabindex="-1" >
					</td>
					<td><input type="text" class="form-control text-right input-sm divide" id="harga_tot6_ori_<?=$nomor ?>" data-nomor='<?=$nomor ?>' name="data6[<?=$nomor ?>][harga_tot6_ori]" value="<?=$harga_total_so; ?>" readonly tabindex="-1"></td>
					<td><input type="text" class="form-control text-right input-sm divide" id="harga_tot6_sisa_<?=$nomor ?>" data-nomor='<?=$nomor ?>' name="data6[<?=$nomor ?>][harga_tot6_sisa]" value="<?=$harga_sisa_so; ?>" readonly tabindex="-1"></td>
					<td>
						<input type="hidden" class="form-control" id="unit6" name="data6[<?=$nomor ?>][unit6]" value="<?=set_value('unit6', isset($unit6) ? $unit6 : ''); ?>" readonly >
						<input type="text" class="form-control text-right harga_tot6 changeAll input-sm divide" id="harga_tot6<?=$nomor ?>" data-nomor='<?=$nomor ?>' name="data6[<?=$nomor ?>][harga_tot6]" value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" <?=$readonly ?>>
					</td>
				</tr>
				<?php
			}
			?>
			<tr id='tr5X' class='FootColor'>
				<td colspan='14'><b>TOTAL TRUCKING</b></td>
				<td align="right">
					<?php
					$total_trucking= round($SUM3,2);
					?>
					<input type="text" class="form-control text-right result6 changeAll input-sm divide" id="total_trucking1" name="total_trucking" value="<?=(isset($total_trucking) ? $total_trucking : ''); ?>" readonly tabindex="-1" >
				</td>
			</tr>
		</tbody>
	<?php
	}




	
	$tagih = isset($penagihan[0]->total_ppn)?$penagihan[0]->total_ppn:0;
	
	
	?>


<?php
	$SUM_OTHER = 0;
	if(!empty($other)){
		echo "<tbody>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='15'><b>OTHER</b></td>";
			echo "</tr>";
			echo "<tr class='bg-blue'>";
				echo "<th class='text-center'>#</th>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Description</th>";
				echo "<th class='text-center'>Qty Total</th>";
				echo "<th class='text-center'>Qty Sisa</th>";
				echo "<th class='text-center'>Qty-Inv</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
			$numb9 =0;
			foreach($other AS $val => $valx){
				$numb9++;$harga_tot9=0;
				$checked="";$readonly=" disabled";
				if(isset($valx['total_deal_usd'])){
					$harga_sat9= round($valx['total_deal_usd']/$valx['qty'],2);
				}else{
					$harga_sat9= round($valx['harga_satuan'],2);
				}
				if(isset($valx['checked'])){
					if($valx['checked']!="") { 
						$checked="checked";$readonly="";
						$harga_tot9=round($harga_sat9*$valx['qty'],2);
						$SUM_OTHER += ($harga_tot9);
					}
				}
				?>
				<tr id='tr8_<?= $numb9;?>' >
					<td align='center'>
						<input type="checkbox" <?=$checked?> id="ck8_<?= $numb9;?>" name="tr9_<?= $numb9;?>" value="<?=$numb9;?>" onclick="showdata('8',<?= $numb9;?>)">
						<input type="hidden" id="data8_ipp_<?=$numb9?>" name="data9[<?=$numb9?>][no_ipp]" value="<?=$valx['no_ipp']?>">
						<input type="hidden" id="data8_so_<?=$numb9?>" name="data9[<?=$numb9?>][no_so]" value="<?=get_nomor_so($valx['no_ipp'])?>">
						<input type="hidden" id="data8_idmilik_<?=$numb9?>" name="data9[<?=$numb9?>][id_milik]" value="<?=$valx['id_milik']?>">
					</td>
					<td colspan='7'>
						<?php
						$material_name9= get_nomor_so($valx['no_ipp']).' / '.strtoupper('OTHER');
						?>
						<input type="text" class="form-control input-sm" id="material_name9<?=$numb9 ?>" name="data9[<?=$numb9 ?>][material_name9]" value="<?=set_value('material_name9', isset($material_name9) ? $material_name9 : ''); ?>" readonly tabindex="-1">
					</td>
					<td><input type="text" class="form-control" id="material_desc9<?=$numb9 ?>" name="data9[<?=$numb9 ?>][material_desc9]" value="<?=set_value('material_desc9', isset($valx['desc']) ? $valx['desc'] : ''); ?>" readonly tabindex="-1" ></td>
					<td>
					   <input type="text" class="form-control text-right input-sm divide" data-nomor='<?=$numb9 ?>' id="qty9_ori_<?=$numb9 ?>" name="data9[<?=$numb9 ?>][qty9_ori]" value="<?=set_value('qty9_ori', isset($valx['qty']) ? $valx['qty'] : '0'); ?>" readonly tabindex="-1">
					</td>
					<td>
					   <input type="text" class="form-control text-right input-sm divide" data-nomor='<?=$numb9 ?>' id="qty9_belum_<?=$numb9 ?>" name="data9[<?=$numb9 ?>][qty9_belum]" value="<?=set_value('qty9_belum', isset($valx['qty_sisa']) ? $valx['qty_sisa'] : '0'); ?>" readonly tabindex="-1">
					</td>
					<td>
					   <input type="text" class="form-control qty_oth input-sm text-right divide" data-nomor='<?=$numb9 ?>' id="qty9_<?=$numb9 ?>" name="data9[<?=$numb9 ?>][qty9]" value="<?=set_value('qty9', isset($valx['qty']) ? $valx['qty'] : '0'); ?>" <?=$readonly?>>
					</td>
					<td>
						<?php $unit9= strtoupper($valx['unit']); ?>
						<input type="text" class="form-control text-center input-sm" id="unit9<?=$numb9 ?>" name="data9[<?=$numb9 ?>][unit9]" value="<?=set_value('unit9', isset($unit9) ? $unit9 : ''); ?>" readonly  tabindex="-1">
					</td>
					<td>
						<input type="text" class="form-control text-right divide input-sm" id="harga_sat9<?=$numb9 ?>" name="data9[<?=$numb9 ?>][harga_sat9]" value="<?=set_value('harga_sat9', isset($harga_sat9) ? $harga_sat9 : '0'); ?>" readonly tabindex="-1">						
					</td>
					<td>
						<input type="text" class="form-control text-right divide input-sm amount9" id="harga_tot9<?=$numb9 ?>" name="data9[<?=$numb9 ?>][harga_tot9]" value="<?=set_value('harga_tot9', isset($harga_tot9) ? $harga_tot9 : '0'); ?>" readonly tabindex="-1" >						
					</td>
				</tr>
				<?php
			}
			?>
			<tr class='FootColor'>
				<td colspan='14'><b>TOTAL OTHER</b></td>
				<td align="right">
					<?php $total_other= round($SUM_OTHER,2); ?>
					<input type="text" class="form-control result9 text-right  divide input-sm" id="total_other" name="total_other" value="<?=set_value('total_other', isset($total_other) ? $total_other : '0'); ?>" readonly tabindex="-1" >
				</td>
			</tr>
		</tbody>
	<?php
	}
	
	?>

	<tfoot>
		<tr class='HeaderHr'>
			<td align='right' colspan='15' height='20px;'></td>
		</tr>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>TOTAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' style='text-align:center;' colspan='2'></td>
			<td align='right' style='text-align:center;' colspan='2'>
				<?php
					$grand_total = round($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);
					$down_payment=($uang_muka_persen*$grand_total/100);
					$down_payment2=0;
				?>
				<input type="text" class="form-control grand_total text-right input-sm divide" id="grand_total" name="grand_total" value="<?php echo set_value('grand_total', isset($grand_total) ? $grand_total : '0'); ?>" tabindex="-1">				
			</td>
		</tr>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>DOWN PAYMENT</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control down_payment text-right input-sm divide" id="down_payment" name="down_payment" value="<?= $down_payment; ?>" tabindex="-1">
			</td>
		</tr>
		<tr class='HeaderHr hidden'>
			<td align='right' colspan='11'><b>DOWN PAYMENT II</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control down_payment2 text-right input-sm divide" id="down_payment2" name="down_payment2" value="<?=$down_payment2; ?>" tabindex="-1">
			</td>
		</tr>
		<tr class='HeaderHr hidden'>
			<td align='right' colspan='11'><b>DISKON</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control diskon text-right  input-sm divide" id="diskon" name="diskon" value="<?=(isset($penagihan[0]->total_diskon)?$penagihan[0]->total_diskon:0)?>" placeholder="Diskon"  >	
			</td>
		</tr>
		<?php
		if(isset($penagihan[0]->total_retensi)){
			$total_retensi=$penagihan[0]->total_retensi;
			$persen_retensi=$penagihan[0]->persen_retensi;
		}else{
			$total_retensi=0;
			$persen_retensi=0;
		}
		$ret       =  $this->db->select('SUM(retensi_um) AS retensi_um')->where_in('no_ipp',$arr_in_ipp)->get('billing_so')->row();
		$retum 		= $ret->retensi_um;
		?>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>POTONGAN RETENSI %</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' style='text-align:center;' colspan='2'>
				<input type="text" id="persen_retensi" name="persen_retensi" class="form-control input-sm text-right" value="<?=$persen_retensi?>" >				
			</td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control potongan_retensi text-right input-sm divide" id="potongan_retensi" name="potongan_retensi" value="<?=$total_retensi?>" tabindex="-1">
			</td>
		</tr>

		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>PPN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control ppn text-right input-sm divide" id="ppn" name="ppn" value="<?=$tagih?>" tabindex="-1">
			</td>
		</tr>

		<?php
		if(isset($penagihan[0]->total_retensi2)){
			$retum=$penagihan[0]->total_retensi2;
			$persen_retensi2=$penagihan[0]->persen_retensi2;
		}else{
			$ret       =  $this->db->select('SUM(retensi_um) AS retensi_um')->where_in('no_ipp',$arr_in_ipp)->get('billing_so')->row();
			$retum 		= $ret->retensi_um;
			$persen_retensi2=0;
		}
		
		?>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>POTONGAN RETENSI PPN %</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' style='text-align:center;' colspan='2'>
				<input type="text" id="persen_retensi2" name="persen_retensi2" class="form-control input-sm text-right" value="<?=$persen_retensi2?>" >	
			</td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control potongan_retensi2 text-right input-sm divide" id="potongan_retensi2" name="potongan_retensi2" value="<?=$retum?>" tabindex="-1">
			</td>
		</tr>

		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>TOTAL INVOICE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<?php
				if(isset($penagihan[0]->total_invoice)){
					$grand_total = round($penagihan[0]->total_invoice, 2);
				}else{
					$grand_total = round($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT + $SUM_NONFRP - ($down_payment + $down_payment2), 2);
				}
				?>
				<input type="text" class="form-control total_invoice text-right input-sm divide" id="total_invoice" name="total_invoice" value="<?php echo set_value('total_invoice', isset($grand_total) ? $grand_total : '0'); ?>" tabindex="-1">
			</td>
		</tr>
	</tfoot>
</table>
<br>