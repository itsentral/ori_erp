<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?=$title;?></h3>
			<div class="box-tool pull-right">
				
			</div>
		</div>
		<br>
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>IPP Number</b></label>
				<div class='col-sm-4'>
					<input type='text' name='no_ipp' id='no_ipp' class='form-control input-md' readonly value='<?=$in_ipp;?>'>
					<input type='hidden' name='id' id='id' class='form-control input-md' readonly value='<?=$id;?>'>
				</div>
				<label class='label-control col-sm-2'><b>SO Number</b></label>
				<div class='col-sm-4'>
					<input type='text' name='no_so' id='no_so' class='form-control input-md' readonly value='<?=$in_so;?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Invoice Type</b></label>
				<div class='col-sm-4'>
					<input type='text' name='type' id='type' class='form-control input-md' readonly value='PROGRESS'>
				</div>
				<label class='label-control col-sm-2'><b>Invoice Date</b></label>
				<div class='col-sm-4'>
					<input type='text' name='tgl_inv' id='tgl_inv' class='form-control input-md datepicker' readonly value='<?=date('Y-m-d')?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Customer Name</b></label>
				<div class='col-sm-4'>
					<input type='text' name='nm_customer' id='nm_customer' class='form-control input-md' readonly value='<?=$penagihan[0]->customer;?>'>
					<input type='hidden' name='id_customer' id='id_customer' class='form-control input-md' readonly value='<?=$penagihan[0]->kode_customer;?>'>
				</div>
				<label class='label-control col-sm-2'><b>Customer Address</b></label>
				<div class='col-sm-4'>
					<textarea name='cust_address' id='cust_address' class='form-control input-md' rows='3' readonly><?= get_name('customer','alamat','id_customer',$penagihan[0]->kode_customer);?></textarea>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>PO Number <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_po' id='nomor_po' class='form-control input-md' value='<?=$penagihan[0]->no_po;?>'>
				</div>
				<label class='label-control col-sm-2'><b>F. No Faktur <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_faktur' id='nomor_faktur' class='form-control input-md'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No Pajak <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_pajak' id='nomor_pajak' class='form-control input-md'>
				</div>
				<label class='label-control col-sm-2'><b>Kurs <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='kurs' id='kurs' class='form-control input-md' value='<?=number_format($kurs,2);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
					<input type="hidden" id='wilayah' name="wilayah" class="form-control input-sm" value="<?= get_name('so_number','wilayah','id_bq', "BQ-".$getHeader[0]->no_ipp);?>">
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>PPN | TOP <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<select id="ppnselect" name="ppnselect" class="form-control input-sm chosen_select" required>
						<option value="0">SELECT AN PPN</option>
						<option value="1">PPN</option>
						<option value="0">NON PPN</option>
					</select>
				</div>
				<div class='col-sm-2'>
					<select id="top" name="top" class="form-control input-sm chosen_select" required>
						<option value="0">SELECT AN TOP</option>
						<?php
						foreach($list_top AS $val => $valx){
							echo "<option value='".$valx['data1']."'>".strtoupper($valx['name'])."</option>";
						}
						?>
					</select>
				</div>
				
				<label class='label-control col-sm-2'><b>Persentase Progress (%) <span class='text-red'>*</span></b></label>
				<div class='col-sm-3'>
					<?php
					$sisa_progress = 100 - $penagihan[0]->progress_persen;
					?>
					<input type='text' name='umpersen' id='umpersen' class='form-control input-md maskMoney' maxlength='3' value='' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
				</div>
				<div class='col-sm-1'>
					<input type='hidden' name='sudah_progress' id='sudah_progress' class='form-control text-center input-md' value='<?=$sisa_progress;?>' readonly>
					<input type='text' name='progressx' id='progressx' class='form-control text-center input-md' value='<?=$penagihan[0]->progress_persen;?>' readonly>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Uang Muka I (%)</b></label>
				<div class='col-sm-4'>
					<input type="text" name="persen" id="persen" class="form-control input-md persen" value="<?=$uang_muka_persen;?>" readonly> 
				</div>
				<label class='label-control col-sm-2'><b>Uang Muka II (%)</b></label>
				<div class='col-sm-4'>
					<input type="text" name="persen2" id="persen2" class="form-control input-md persen2" value="<?=$uang_muka_persen2;?>" readonly> 
					<input type="hidden" name="um_persen2" id="um_persen2" class="form-control input-md" value="<?=$uang_muka_persen2;?>">
				</div>
			</div>
			
			
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead>
					<tr class='bg-blue'>
						<td class="text-left" colspan='15'><b>PRODUCT</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" width='2%'>#</th>
						<th class="text-center">Product</th>
						<th class="text-center">Item Cust</th>
						<th class="text-center">Desc</th>
						<th class="text-center" width='6%'>Dim 1</th>
						<th class="text-center" width='6%'>Dim 2</th>
						<th class="text-center" width='5%'>Lin</th>
						<th class="text-center" width='5%'>Pre</th>
						<th class="text-center" width='10%'>Specification</th>
						<th class="text-center" width='7%'>Unit Price</th>
						<th class="text-center" width='5%'>Qty Total</th>
						<th class="text-center" width='5%'>Qty Sisa</th>
						<th class="text-center" width='5%'>Qty Inv</th>
						<th class="text-center" width='6%'>Unit</th>
						<th class="text-center" width='8%'>Total Price</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$numb=0;
					$SUM = 0;
					foreach($getDetail AS $val => $valx){ $numb++;
						$pr		= 'pr';
						$numb1	= $pr.$numb;
						
						$NegoPersen = (!empty($valx['nego']))?'0':'0';
						$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
						$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
						$est_harga 	= 0;
						$dataSum 	= 0;
						
						if($valx['qty'] <> 0){
							$HrgTot  	= $valx['total_deal_usd'];
							$dataSum	= $HrgTot;
						}

						$SUM += $dataSum;
						
						if($valx['product'] == 'pipe' OR $valx['product'] == 'pipe slongsong'){
							$unitT = "Btg";
						}
						else{
							$unitT = "Pcs";
						}
						$sisa_inv = $valx['qty'] - $valx['qty_inv'];
						$harga_sat	= round($dataSum / $valx['qty'],2);
						$harga_tot	= number_format($harga_sat * $sisa_inv,2);
						$harga_tot2	= round($harga_sat * $sisa_inv,2);
						?>			
						<tr id='tr_<?= $numb;?>' >
							<td align='center'>
								<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow(<?= $numb;?>)'><i class="fa fa-times"></i></a></span>
							</td>
							<td>
								<input type="text" class="form-control input-md" id="material_name1_<?= $numb;?>" name="data1[<?=$numb ?>][material_name1]" value="<?=strtoupper($valx['product']); ?>" readonly title='<?=get_nomor_so($valx['no_ipp']);?>'>
							</td>
							<td>
								<input type="text" class="form-control input-md" id="product_cust<?= $numb;?>" name="data1[<?=$numb ?>][product_cust]" value="<?=strtoupper($valx['customer_item']); ?>" readonly title='<?=get_nomor_so($valx['no_ipp']);?>'>
							</td>
							<td>
								<input type="text" class="form-control input-md" id="product_desc<?= $numb;?>" name="data1[<?=$numb ?>][product_desc]" value="<?=strtoupper($valx['desc']); ?>" readonly title='<?=get_nomor_so($valx['no_ipp']);?>'>
							</td>
							<td><input type="text" class="form-control input-md text-right" id="diameter_1_<?= $numb;?>" name="data1[<?=$numb ?>][diameter_1]" value="<?=$valx['dim1']; ?>" readonly ></td>
							<td><input type="text" class="form-control input-md text-right" id="diameter_2_<?= $numb;?>" name="data1[<?=$numb ?>][diameter_2]" value="<?=$valx['dim2']; ?>" readonly ></td>
							<td><input type="text" class="form-control input-md text-right" id="liner_<?= $numb;?>" name="data1[<?=$numb ?>][liner]" value="<?=$valx['liner']; ?>" readonly ></td>
							<td><input type="text" class="form-control input-md text-center" id="pressure_<?= $numb;?>" name="data1[<?=$numb ?>][pressure]" value="<?=$valx['pressure']; ?>" readonly ></td>
							<td><input type="text" class="form-control input-md" id="id_milik_<?= $numb;?>" name="data1[<?=$numb ?>][id_milik]" value="<?=spec_bq($valx['id_milik']); ?>" readonly ></td>
							<td>
								<input type='hidden' name="data1[<?=$numb ?>][id]" value='<?=$valx['id'];?>'>
								<input type='hidden' name="data1[<?=$numb ?>][qty_sudah]" value='<?=$valx['qty_inv'];?>'>
								<input type="text" class="form-control input-md text-right" id="harga_sat_<?= $numb;?>" name="data1[<?=$numb ?>][harga_sat]" value="<?=set_value('harga_sat', isset($harga_sat) ? number_format($harga_sat,2) : ''); ?>" readonly >
								<input type="hidden" class="form-control input-md" id="harga_sat_hidden_<?=$numb ?>" name="data1[<?=$numb ?>][harga_sat_hidden]" value="<?=set_value('harga_sat_hidden', isset($harga_sat) ? $harga_sat : ''); ?>" readonly >
							</td>
							<td><input type="text" class="form-control input-md text-center" id="qty_ori_<?= $numb;?>" data-nomor='<?=$numb ?>' name="data1[<?=$numb ?>][qty_ori]" value="<?=$valx['qty']; ?>" readonly></td>
							<td><input type="text" class="form-control input-md text-center" id="qty_belum_<?= $numb;?>" data-nomor='<?=$numb ?>' name="data1[<?=$numb ?>][qty_belum]" value="<?=$sisa_inv; ?>" readonly></td>
							<td><input type="text" class="form-control input-md qty_product text-center maskMoney" id="qty_<?= $numb;?>" data-nomor='<?=$numb ?>' name="data1[<?=$numb ?>][qty]" data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''  value="<?=$sisa_inv; ?>" ></td>
							<td><input type="text" class="form-control input-md text-center" id="unit1_<?= $numb;?>" name="data1[<?=$numb ?>][unit1]" value="<?=$unitT; ?>" readonly ></td>
							<td>
								<input type="text" class="form-control text-right" id="harga_tot_<?=$numb ?>" name="data1[<?=$numb ?>][harga_tot]" value="<?=$harga_tot; ?>" readonly >
								<input type="hidden" class="form-control amount1" id="harga_tot_hidden<?=$numb ?>" name="data1[<?=$numb ?>][harga_tot_hidden]" value="<?=$harga_tot2; ?>" readonly >
								<input type="hidden" class="form-control changeProduct" data-id='<?=$numb ?>' value="<?=$harga_tot; ?>" readonly >
							</td>
						</tr>
					<?php			
					}
					?>
					<tr class='FootColor'>
						<td colspan='14'><b>TOTAL COST  OF PRODUCT</b></td>
						<td align='center'>
							<?php 
							$tot_product=number_format($SUM,2);
							$tot_product2=round($SUM,2);
							?>
							<input type="text" class="form-control input-md result1 text-right" id="tot_product" name="tot_product" value="<?=set_value('tot_product', isset($tot_product) ? $tot_product : ''); ?>" readonly >
							<input type="hidden" class="form-control result1_hidden" id="tot_product_hidden" name="tot_product_hidden" value="<?=set_value('tot_product_hidden', isset($tot_product2) ? $tot_product2 : ''); ?>" readonly >
							<input type="hidden" class="form-control result1  changeProductTot" value="<?=set_value('tot_product', isset($tot_product) ? $tot_product : ''); ?>" readonly >
							
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
							echo "<th class='text-center' colspan='10'>Material Name</th>";
							echo "<th class='text-center'>Qty</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
						$numb2 =0;
						foreach($non_frp AS $val => $valx){
							$numb2++;
							$SUM_NONFRP += $valx['total_deal_usd'];
							?>
							<tr id='tr1_<?= $numb2;?>' >
								<td align='center'>
									<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow2(<?= $numb2;?>)'><i class="fa fa-times"></i>
									</a></span>
								</td>
								<td colspan='10'>
									<?php
									$material_name2= get_nomor_so($valx['no_ipp']).' / '.strtoupper(get_name_acc($valx['id_material']));
									?>
									<input type="text" class="form-control" id="material_name2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][material_name2]" value="<?=set_value('material_name2', isset($material_name2) ? $material_name2 : ''); ?>" readonly >
								</td>
								<td>
								   <input type="text" class="form-control qty_bq text-right maskMoney" data-nomor='<?=$numb2 ?>' id="qty2" name="data2[<?=$numb2 ?>][qty2]" value="<?=set_value('qty2', isset($valx['qty']) ? $valx['qty'] : ''); ?>" >
								</td>
								<td>
									<?php
									$unit2= strtoupper($valx['satuan']);
									?>
									<input type="text" class="form-control text-center" id="unit2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][unit2]" value="<?=set_value('unit2', isset($unit2) ? $unit2 : ''); ?>" readonly >
								</td>
								<td>
									<?php
									$harga_sat2= number_format($valx['total_deal_usd']/$valx['qty'],2);
									$harga_sat2_hidden= round($valx['total_deal_usd']/$valx['qty'],2);
									?>
									<input type="text" class="form-control text-right" id="harga_sat2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][harga_sat2]" value="<?=set_value('harga_sat2', isset($harga_sat2) ? $harga_sat2 : ''); ?>" readonly >
									<input type="hidden" class="form-control" id="harga_sat2_hidden<?=$numb2 ?>" name="data2[<?=$numb2 ?>][harga_sat2_hidden]" value="<?=set_value('harga_sat2_hidden', isset($harga_sat2_hidden) ? $harga_sat2_hidden : ''); ?>" readonly >
						  
								</td>
								<td>
									<?php
									$harga_tot2= number_format($valx['total_deal_usd'],2);
									$harga_tot2_hidden= round($valx['total_deal_usd'],2);
									?>
									<input type="text" class="form-control text-right" id="harga_tot2<?=$numb2 ?>" name="data2[<?=$numb2 ?>][harga_tot2]" value="<?=set_value('harga_tot2', isset($harga_tot2) ? $harga_tot2 : ''); ?>" readonly >
									<input type="hidden" class="form-control amount2" id="harga_tot2_hidden<?=$numb2 ?>" name="data2[<?=$numb2 ?>][harga_tot2_hidden]" value="<?=set_value('harga_tot2_hidden', isset($harga_tot2_hidden) ? $harga_tot2_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changeAcc" data-id='<?=$numb2 ?>' value="<?=set_value('harga_tot2', isset($harga_tot2) ? $harga_tot2 : ''); ?>" readonly >
									
								</td>
							</tr>
							<?php
						}
						?>
						<tr class='FootColor'>
							<td colspan='13'><b>TOTAL BILL OF QUANTITY NON FRP</b></td>
							<td align='center'></td> 
							<td align="right">
								<?php
								$total_bq_nf= number_format($SUM_NONFRP,2);
								$total_bq_nf_hidden= round($SUM_NONFRP,2);
								?>
								<input type="text" class="form-control result2 text-right" id="total_bq_nf" name="total_bq_nf" value="<?=set_value('total_bq_nf', isset($total_bq_nf) ? $total_bq_nf : ''); ?>" readonly >
								<input type="hidden" class="form-control result2_hidden" id="total_bq_nf_hidden" name="total_bq_nf_hidden" value="<?=set_value('total_bq_nf_hidden', isset($total_bq_nf_hidden) ? $total_bq_nf_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control result2 changeAccTot" value="<?=set_value('total_bq_nf', isset($total_bq_nf) ? $total_bq_nf : ''); ?>" readonly >
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
							echo "<th class='text-center' colspan='10'>Material Name</th>";
							echo "<th class='text-center'>Weight</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</thead>";
					echo "<tbody class='body_x'>";
						$numb3 =0;
						foreach($material AS $val => $valx){
							$numb3++;
							$SUM_MAT += $valx['total_deal_usd'];
							?>
							<tr id='tr2_<?= $numb3;?>' >
								<td align='center'>
									<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow3(<?= $numb3;?>)'><i class="fa fa-times"></i>
									</a></span>
								</td>
								<td colspan='10'>
									<?php
									$material_name3= get_nomor_so($valx['no_ipp']).' / '.strtoupper($valx['nm_material']);
									?>
									<input type="text" class="form-control" id="material_name3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][material_name3]" value="<?=set_value('material_name3', isset($material_name3) ? $material_name3 : ''); ?>" readonly >
								</td>
								<td>
								   <input type="text" class="form-control qty_material text-right maskMoney" id="qty3_<?= $numb3;?>" data-nomor='<?=$numb3 ?>'  name="data3[<?=$numb3 ?>][qty3]" value="<?=set_value('qty3', isset($valx['qty']) ? $valx['qty'] : ''); ?>" >
								</td>
								<td>
									<?php
									$unit3= strtoupper($valx['satuan']);
									?>
									<input type="text" class="form-control text-center" id="unit3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][unit3]" value="<?=set_value('unit3', isset($unit3) ? $unit3 : ''); ?>" readonly >
								</td>
								<td>
									<?php
									$harga_sat3= number_format($valx['total_deal_usd']/$valx['qty'],2);
									$harga_sat3_hidden= round($valx['total_deal_usd']/$valx['qty'],2);
									?>
									<input type="text" class="form-control text-right text-right" id="harga_sat3_<?= $numb3;?>" name="data3[<?=$numb3 ?>][harga_sat3]" value="<?=set_value('harga_sat3', isset($harga_sat3) ? $harga_sat3 : ''); ?>" readonly >
									<input type="hidden" class="form-control" id="harga_sat3_hidden<?=$numb3 ?>" name="data3[<?=$numb3 ?>][harga_sat3_hidden]" value="<?=set_value('harga_sat2_hidden', isset($harga_sat3_hidden) ? $harga_sat3_hidden : ''); ?>" readonly >
								</td>
								<td>
									<?php
									$harga_tot3= number_format($valx['total_deal_usd'],2);
									$harga_tot3_hidden= round($valx['total_deal_usd'],2);
									?>
									<input type="text" class="form-control text-right" id="harga_tot3<?=$numb3 ?>" name="data3[<?=$numb3 ?>][harga_tot3]" value="<?=set_value('harga_tot3', isset($harga_tot3) ? $harga_tot3 : ''); ?>" readonly >
									<input type="hidden" class="form-control amount3" id="harga_tot3_hidden<?=$numb3 ?>" name="data3[<?=$numb3 ?>][harga_tot3_hidden]" value="<?=set_value('harga_tot3_hidden', isset($harga_tot3_hidden) ? $harga_tot3_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changeMat" data-id='<?=$numb3 ?>' value="<?=set_value('harga_tot3', isset($harga_tot3) ? $harga_tot3 : ''); ?>" readonly >
									
								</td>
							</tr>
						<?php
						}
						?>
						<tr class='FootColor'>
							<td colspan='14'><b>TOTAL MATERIAL</b></td>
							<td align="right">
								<?php
								$total_material= number_format($SUM_MAT,2);
								$total_material_hidden= round($SUM_MAT,2);
								?>
								<input type="text" class="form-control result3 text-right" id="total_material<?=$numb3 ?>" name="total_material" value="<?=set_value('total_material', isset($total_material) ? $total_material : ''); ?>" readonly >
								<input type="hidden" class="form-control result3_hidden" id="total_material_hidden<?=$numb3 ?>" name="total_material_hidden" value="<?=set_value('total_material_hidden', isset($total_material_hidden) ? $total_material_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control result3 changeMatTot" value="<?=set_value('total_material', isset($total_material) ? $total_material : ''); ?>" readonly >
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
							<th class="text-center" colspan='13'>Item Product</th>
							<th class="text-center">Total Price</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no1=0;
						$SUM1=0;
						$numb4=0;
						foreach($getEngCost AS $val => $valx){
							$SUM1 += $valx['total_deal_usd'];
							$no1++;
							$numb4++;
							
							$material_name4= get_nomor_so($valx['no_ipp']).' / '.strtoupper('ENGINERING COST').' - '.get_name('cost_project_detail','caregory_sub','id',$valx['id_milik']);
							$unit4= strtoupper('-');
							$harga_tot4= (!empty($valx['total_deal_usd']))?number_format($valx['total_deal_usd'],2):'-';
							$harga_tot4_hidden= (!empty($valx['total_deal_usd']))?round($valx['total_deal_usd'],2):'-';
							?>
							<tr id='tr3_<?= $numb4;?>' >
								<td align='center'>
									<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow4(<?= $numb4;?>)'><i class="fa fa-times"></i>
									</a></span>
								</td>
								<td colspan='13'>
									<input type="text" class="form-control" id="material_name4" name="data4[<?=$numb4 ?>][material_name4]" value="<?=set_value('material_name4', isset($material_name4) ? $material_name4 : ''); ?>" readonly >
								</td>
								<td>
									<input type="hidden" class="form-control" id="unit4" name="data4[<?=$numb4 ?>][unit4]" value="<?=set_value('unit4', isset($unit4) ? $unit4 : ''); ?>" readonly >
									<input type="text" class="form-control text-right harga_tot4 changeAll maskMoney" id="harga_tot4<?=$numb4 ?>" data-nomor='<?=$numb4 ?>' name="data4[<?=$numb4 ?>][harga_tot4]" value="<?=set_value('harga_tot4', isset($harga_tot4) ? $harga_tot4 : ''); ?>" >
									<input type="hidden" class="form-control amount4 changeAll" id="harga_tot4_hidden<?=$numb4 ?>" name="data4[<?=$numb4 ?>][harga_tot4_hidden]" value="<?=set_value('harga_tot4_hidden', isset($harga_tot4_hidden) ? $harga_tot4_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changeEng" data-id='<?=$numb4 ?>' value="<?=set_value('harga_tot4', isset($harga_tot4) ? $harga_tot4 : ''); ?>" >
								</td>
							</tr>
						<?php
						}
						?>
						<tr id='tr3X' class='FootColor'>
							<td colspan='14'><b>TOTAL ENGINEERING COST</b></td>
							<td align="right">
								<?php
								$total_enginering= number_format($SUM1,2);
								$total_enginering_hidden= round($SUM1,2);
									?>
								<input type="text" class="form-control text-right result4 changeAll" id="total_enginering<?=$numb4 ?>" name="total_enginering" value="<?=set_value('total_enginering', isset($total_enginering) ? $total_enginering : ''); ?>" readonly >
								<input type="hidden" class="form-control result4_hidden changeAll" id="total_enginering_hidden<?=$numb4 ?>" name="total_enginering_hidden" value="<?=set_value('total_enginering_hidden', isset($total_enginering_hidden) ? $total_enginering_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control changeEngTot" value="<?=set_value('total_enginering', isset($total_enginering) ? $total_enginering : ''); ?>" readonly >
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
							<th class="text-center" colspan='13'>Category</th>
							<th class="text-center">Total Price</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$SUM2=0;
						$numb5=0;
						foreach($getPackCost AS $val => $valx){
							$numb5++;
							
							$SUM2 += $valx['total_deal_usd'];
							$material_name5= get_nomor_so($valx['no_ipp']).' / '.strtoupper('PACKING COST').' - '.get_name('cost_project_detail','caregory_sub','id',$valx['id_milik']);
							$unit5= strtoupper('-');
							$harga_tot5= number_format($valx['total_deal_usd'],2);
							$harga_tot5_hidden= round($valx['total_deal_usd'],2);
							?>
							<tr id='tr4_<?= $numb5;?>' >
								<td align='center'>
									<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow5(<?= $numb5;?>)'><i class="fa fa-times"></i>
									</a></span>
								</td>
								<td colspan='13'>
									<input type="text" class="form-control" id="material_name5" name="data5[<?=$numb5 ?>][material_name5]" value="<?=set_value('material_name5', isset($material_name5) ? $material_name5 : ''); ?>" readonly >
								</td>
								<td>
									<input type="hidden" class="form-control" id="unit5" name="data5[<?=$numb5 ?>][unit5]" value="<?=set_value('unit5', isset($unit5) ? $unit5 : ''); ?>" readonly >
									<input type="text" class="form-control text-right  harga_tot5 maskMoney" id="harga_tot5<?=$numb5 ?>" data-nomor='<?=$numb5 ?>' name="data5[<?=$numb5 ?>][harga_tot5]" value="<?=set_value('harga_tot5', isset($harga_tot5) ? $harga_tot5 : ''); ?>" >
									<input type="hidden" class="form-control amount5" id="harga_tot5_hidden<?=$numb5 ?>" name="data5[<?=$numb5 ?>][harga_tot5_hidden]" value="<?=set_value('harga_tot5_hidden', isset($harga_tot5_hidden) ? $harga_tot5_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changePack" data-id='<?=$numb5 ?>' value="<?=set_value('harga_tot5', isset($harga_tot5) ? $harga_tot5 : ''); ?>" >
								</td>
							</tr>
							<?php
						}
						?>
						<tr id='tr4X' class='FootColor'>
							<td colspan='14'><b>TOTAL PACKING COST</b></td>
							<td align="right">
								<?php
								$total_packing= number_format($SUM2,2);
								$total_packing_hidden= round($SUM2,2);
								?>
								<input type="text" class="form-control text-right result5 changeAll" id="total_packing<?=$numb5 ?>" name="total_packing" value="<?=set_value('total_packing', isset($total_packing) ? $total_packing : ''); ?>" readonly >
								<input type="hidden" class="form-control result5_hidden changeAll" id="total_packing_hidden<?=$numb5 ?>" name="total_packing_hidden" value="<?=set_value('total_packing_hidden', isset($total_packing_hidden) ? $total_packing_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control result5 changePackTot"value="<?=set_value('total_packing', isset($total_packing) ? $total_packing : ''); ?>" readonly >
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
							<th class="text-center" colspan='13'>Category</th>
							<th class="text-center">Total Price</th>
						</tr>
					</tbody>
					<tbody>
						<?php
						$SUM3=0;
						$nomor=0;
						foreach($getTruck AS $val => $valx){
							$Qty3 = (!empty($valx['qty']))?$valx['qty']:'-';
							$SUM3 += $valx['total_deal_usd'];
							$nomor++;
							
							$category = get_name('cost_project_detail','category','id',$valx['id_milik']);
							$name_add = "";
							if($category == 'lokal'){
								$name_add = " - ".get_name('cost_project_detail','tujuan','id',$valx['id_milik']);;
							}
							
							$material_name6= get_nomor_so($valx['no_ipp']).' / '.strtoupper('SHIPPING COST').' - '.get_name('cost_project_detail','caregory_sub','id',$valx['id_milik']).strtoupper($name_add);
							
							$unit6				= strtoupper('-');
							$harga_tot6			= number_format($valx['total_deal_usd'],2);
							$harga_tot6_hidden	= round($valx['total_deal_usd'],2);
							?>
							<tr id='tr5_<?= $nomor;?>' >
								<td align='center'><span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow6(<?= $nomor;?>)'><i class="fa fa-times"></i></a></span></td>
								<td colspan='13'>
									<input type="text" class="form-control" id="material_name6" name="data6[<?=$nomor ?>][material_name6]" value="<?=set_value('material_name6', isset($material_name6) ? $material_name6 : ''); ?>" readonly >
								</td>
								<td>
									<input type="hidden" class="form-control" id="unit6" name="data6[<?=$nomor ?>][unit6]" value="<?=set_value('unit6', isset($unit6) ? $unit6 : ''); ?>" readonly >
									<input type="text" class="form-control text-right harga_tot6 changeAll maskMoney" id="harga_tot6<?=$nomor ?>" data-nomor='<?=$nomor ?>' name="data6[<?=$nomor ?>][harga_tot6]" value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" >
									<input type="hidden" class="form-control amount6 changeAll" id="harga_tot6_hidden<?=$nomor ?>" name="data6[<?=$nomor ?>][harga_tot6_hidden]" value="<?=set_value('harga_tot6_hidden', isset($harga_tot6_hidden) ? $harga_tot6_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changeShip" data-id='<?=$nomor ?>' value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>">
								</td>
							</tr>
							<?php
						}
						?>
						<tr id='tr5X' class='FootColor'>
							<td colspan='14'><b>TOTAL TRUCKING</b></td>
							<td align="right">
								<?php
								$total_trucking= number_format($SUM3,2);
								$total_trucking_hidden= round($SUM3,2);
								?>
								<input type="text" class="form-control text-right result6 changeAll" id="total_trucking<?=$nomor ?>" name="total_trucking" value="<?=set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" readonly >
								<input type="hidden" class="form-control result6_hidden changeAll" id="total_trucking_hidden<?=$nomor ?>" name="total_trucking_hidden" value="<?=set_value('total_trucking_hidden', isset($total_trucking_hidden) ? $total_trucking_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control changeShipTot" value="<?=set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" readonly >
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
						<td align='right' colspan='13'><b>TOTAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center' style='text-align:center;'></td>
						<td align='right' style='text-align:center;'>
							<?php 
								$grand_total 		= number_format($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);
								$grand_total_hidden = round($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);
							?>
							<input type="text" class="form-control grand_total text-right" id="grand_total" name="grand_total" value="<?php echo set_value('grand_total', isset($grand_total) ? $grand_total : ''); ?>" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control grand_total_hidden" id="grand_total_hidden" name="grand_total_hidden" value="<?php echo set_value('grand_total_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>DOWN PAYMENT I</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center'></td>
						<td align='right'>
							<input type="text" class="form-control down_payment text-right" id="down_payment" name="down_payment" value="<?= number_format($down_payment,2); ?>" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control down_payment_hidden" id="down_payment_hidden" name="down_payment_hidden" value="<?=$down_payment; ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>DOWN PAYMENT II</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center'></td>
						<td align='right'>
							<input type="text" class="form-control down_payment2 text-right" id="down_payment2" name="down_payment2" value="<?=number_format($down_payment2,2); ?>" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control down_payment_hidden2" id="down_payment_hidden2" name="down_payment_hidden2" value="<?=$down_payment2; ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>DISKON</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center'></td>
						<td align='right'>
							<input type="text" class="form-control diskon text-right autoNumeric" id="diskon" name="diskon" value="0" placeholder="Diskon"  >
							<input type="hidden" class="form-control diskon_hidden" id="diskon_hidden" name="diskon_hidden" value="0" placeholder="Automatic" readonly >
						</td>
					</tr>
					<?php 
					$ret       =  $this->db->select('SUM(retensi_um) AS retensi_um')->where_in('no_ipp',$arr_in_ipp)->get('billing_so')->row(); 
					$retum 		= $ret->retensi_um;
					?>
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>POTONGAN RETENSI</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center' style='text-align:center;'>
							<select id="persen_retensi" name="persen_retensi" class="form-control input-sm text-right" style="width: 100%;" tabindex="-1" required readonly>
								<option value="0">0 %</option>			
								<option value="5">5 %</option>
								<option value="10">10 %</option>
								<option value="15">15 %</option>
								<option value="20">20 %</option>
							</select>
						</td>
						<td align='right'>
							<input type="text" class="form-control potongan_retensi text-right" id="potongan_retensi" name="potongan_retensi" value="0" placeholder="Automatic"  readonly>
							<input type="hidden" class="form-control potongan_retensi_hidden" id="potongan_retensi_hidden" name="potongan_retensi_hidden" value="0" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control retensi_um" id="retensi_um" name="retensi_um" value="<?php echo set_value('retensi_um', isset($retum) ? $retum : '0'); ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
					
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>PPN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center'></td>
						<td align='right'>
							<input type="text" class="form-control ppn text-right" id="ppn" name="ppn" value="0" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control ppn_hidden" id="ppn_hidden" name="ppn_hidden" value="0" placeholder="Automatic" readonly >
						</td>
					</tr>
					
					<?php 
					$ret       =  $this->db->select('SUM(retensi_um) AS retensi_um')->where_in('no_ipp',$arr_in_ipp)->get('billing_so')->row(); 
					$retum 		= $ret->retensi_um;
					?>
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>POTONGAN RETENSI PPN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center' style='text-align:center;'>
							<select id="persen_retensi2" name="persen_retensi2" class="form-control input-sm text-right" style="width: 100%;" tabindex="-1" required readonly>
								<option value="0">0 %</option>			
								<option value="5">5 %</option>
								<option value="10">10 %</option>
								<option value="15">15 %</option>
								<option value="20">20 %</option>
							</select>
						</td>
						<td align='right'>
							<input type="text" class="form-control potongan_retensi2 text-right" id="potongan_retensi2" name="potongan_retensi2" value="0" readonly>
							<input type="hidden" class="form-control potongan_retensi_hidden2" id="potongan_retensi_hidden2" name="potongan_retensi_hidden2" value="0" readonly >
							<input type="hidden" class="form-control retensi_um2" id="retensi_um2" name="retensi_um2" value="<?php echo set_value('retensi_um', isset($retum) ? $retum : '0'); ?>" readonly >
						</td>
					</tr>
					
					<tr class='HeaderHr'>
						<td align='right' colspan='13'><b>TOTAL INVOICE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='center'>
						<td align='right'>
							<?php 
							$grand_total = number_format($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT - ($down_payment + $down_payment2), 2);
							$grand_total_hidden = round($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT - ($down_payment + $down_payment2), 2);
							?>
							<input type="text" class="form-control total_invoice text-right" id="total_invoice" name="total_invoice" value="<?php echo set_value('total_invoice', isset($grand_total) ? $grand_total : ''); ?>" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control total_invoice_hidden" id="total_invoice_hidden" name="total_invoice_hidden" value="<?php echo set_value('total_invoice_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
				</tfoot>
			</table>
			
			<br>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin: 0px 0px 5px 5px;','value'=>'Back','content'=>'Back','id'=>'back')).' ';
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin: 0px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'proses_inv')).' ';
			?>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<script src="<?=base_url('application/views/Component/general.js'); ?>"></script>
<style>
	.datepicker{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$('.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth:true,
				changeYear:true
			});
		// umLoad();
		
		$(document).on('keyup','#kurs', function(){
			let kurs = $(this).val();
			
			if(kurs == '0' || kurs == ''){
				$(this).val('1');
			}
		});
		
		$(document).on('keyup','#umpersen', function(){
			let umpersen = getNum($(this).val());
			let umpersen_sisa = getNum($('#sudah_progress').val());

			if(umpersen > umpersen_sisa){
				$(this).val(umpersen_sisa);
			}
		});
		
		$(document).on('click','#proses_inv', function(e){
			 e.preventDefault();
			if ($('#tgl_inv').val() == "") {
				swal({
					title	: "TANGGAL INVOICE TIDAK BOLEH KOSONG!",
					text	: "ISI TANGGAL INVOICE!",
					type	: "warning",
					timer	: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
			else if ($('#kurs').val()=="" && $('#wilayah').val() == 'L') {
				swal({
					title	: "KURS HARUS DI UPDATE!",
					text	: "SILAHKAN UPDATE KURS TERLEBIH DAHULU!",
					type	: "warning",
					timer	: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
				$('#kurs').focus();
			}
			else if ($('#jenis_invoice').val()=="uang muka" && $('#um_persen').val()=="") {
				swal({
					title	: "PERSENTASE UM HARUS DIISI!",
					text	: "SILAHKAN ISI PERSENTASE UM TERLEBIH DAHULU!",
					type	: "warning",
					timer	: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
				$('#um_persen').focus();
			}
			else{
				// alert('Development');
				// return false;
				
				swal({
					title: "Anda Yakin?",
					text: "You will not be able to process again this data!",
					type: "warning",
					showCancelButton: true,
					confirmButtonClass: "btn-danger",
					confirmButtonText: "Ya Lanjutkan",
					cancelButtonText: "Batal",
					closeOnConfirm: false,
					closeOnCancel: false,
					showLoaderOnConfirm: true
				},
				function(isConfirm) {
					if (isConfirm) {
					  var formData 	=new FormData($('#form_proses')[0]);
					  var baseurl=base_url + active_controller +'/create_progress';
					  $.ajax({
						url			: baseurl,
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false,
						contentType	: false,
						success		: function(data){

						  if(data.status == 1){
							swal({
							  title	: "Save Success!",
							  text	: data.pesan,
							  type	: "success",
							  timer	: 15000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							window.location.href = base_url + active_controller;
						  }else{

							if(data.status == 2){
							  swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 10000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							  });
							}else{
							  swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 10000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							  });
							}

						  }
						},
						error: function() {
						  swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',
							type				: "error",
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}
					  });
					}else {
					  swal("Batal Proses", "Data bisa diproses nanti", "error");
					  return false;
					}
				});
			}
		});
		
		$(document).on('keyup', '#um_persen', function(){
			umLoad()
		});
		
		$(document).on('keyup', '#um_persen2', function(){
			umLoad2()
		});
		
		$(document).on('keyup', '.qty_product', function(){
			let dataNomor 	= $(this).data('nomor');
			let sisa 		= getNum($('#qty_belum_'+dataNomor).val().split(",").join(""));
			let dataIni	  	= getNum($(this).val().split(",").join(""));
			if(dataIni > sisa){
				$(this).val(number_format(sisa));
				dataIni	 = sisa;
			}
			let hargaSat  	= $('#harga_sat_hidden_'+dataNomor).val();
			let total     	= getNum(hargaSat*dataIni).toFixed(2);

			$('#harga_tot_'+dataNomor).val(num2(total));
			$('#harga_tot_hidden'+dataNomor).val(total);
			
			fnAlltotal()
		});
		
		$(document).on('blur', '.harga_tot8', function(){
			let dataNomor8 = $(this).data('nomor');
			let hargaSat8  = 1;
			let dataIni8	 = $(this).val();
			let total8     = getNum(hargaSat8*dataIni8).toFixed(2);
			
			$('#harga_tot8'+dataNomor8).val(num2(total8));
			$('#harga_tot8_hidden'+dataNomor8).val(total8);
			 
			fnAlltotal8()
		});
		
		$(document).on('keyup', '.qty_bq', function(){
			let dataNomor = $(this).data('nomor');
			let hargaSat  = $('#harga_sat2_hidden'+dataNomor).val();
			let dataIni	  = $(this).val();
			let total     = getNum(hargaSat*dataIni).toFixed(2);

			$('#harga_tot2'+dataNomor).val(num2(total));
			$('#harga_tot2_hidden'+dataNomor).val(total);
			
			fnAlltotal2()
		});
		
		$(document).on('keyup', '.qty_material', function(){
			let dataNomor3 = $(this).data('nomor');
			let hargaSat3  = $('#harga_sat3_hidden'+dataNomor3).val();
			let dataIni3	  = $(this).val();
			let total3     = getNum(hargaSat3*dataIni3).toFixed(2);

			$('#harga_tot3'+dataNomor3).val(num2(total3));
			$('#harga_tot3_hidden'+dataNomor3).val(total3);
			
			fnAlltotal3()
		});
		
		$(document).on('blur', '.harga_tot4', function(){
			let dataNomor4 = $(this).data('nomor');
			let hargaSat4  = 1;
			let dataIni4	  = $(this).val();
			let total4     = getNum(hargaSat4*dataIni4).toFixed(2);
			console.log(total4);
			console.log(dataIni4);
			$('#harga_tot4'+dataNomor4).val(num2(total4));
			$('#harga_tot4_hidden'+dataNomor4).val(total4);
			 
			fnAlltotal4()
		});
		
		$(document).on('blur', '.harga_tot5', function(){
			let dataNomor4 = $(this).data('nomor');
			let hargaSat4  = 1;
			let dataIni4	  = $(this).val();
			let total4     = getNum(hargaSat4*dataIni4).toFixed(2);
			
			$('#harga_tot5'+dataNomor4).val(num2(total4));
			$('#harga_tot5_hidden'+dataNomor4).val(total4);
			 
			fnAlltotal5()
		});
		
		$(document).on('blur', '.harga_tot6', function(){
			let dataNomor6 = $(this).data('nomor');
			let hargaSat6  = 1;//$('#harga_sat6_hidden'+dataNomor6).val();
			let dataIni6	  = $(this).val();
			let total6     = getNum(hargaSat6*dataIni6).toFixed(2);
			
			$('#harga_tot6'+dataNomor6).val(num2(total6));
			$('#harga_tot6_hidden'+dataNomor6).val(total6);
			 
			fnAlltotal6()
		});
		
		$(document).on('keyup', '.qty_lokal', function(){
			let dataNomor7 = $(this).data('nomor');
			let hargaSat7  = $('#harga_sat7_hidden'+dataNomor7).val();
			let dataIni7	  = $(this).val();
			let total7     = getNum(hargaSat7*dataIni7).toFixed(2);
			
			$('#harga_tot7'+dataNomor7).val(num2(total7));
			$('#harga_tot7_hidden'+dataNomor7).val(total7);
			 
			fnAlltotal7()
		});
		
		$(document).on('blur', '.diskon', function(){
			let dataPpn	  = $('#ppnselect').val();
			let dataDiskon	  = $(this).val();
			let totalDiskon     = getNum(dataDiskon).toFixed(2);
			let grandtotal   = $(".grand_total_hidden").val();
			let uangmuka     = $(".down_payment_hidden").val();
			
			$('.diskon').val(num2(totalDiskon));
			$('.diskon_hidden').val(totalDiskon);
			
			if(dataPpn==1){
			let totalPpn     = getNum((grandtotal-totalDiskon-uangmuka)*0.1).toFixed(2);			
			}
			else{
			let totalPpn     = getNum(0).toFixed(2);	
			}
			
			$('.ppn').val(num2(totalPpn));
			$('.ppn_hidden').val(totalPpn);
			 
			totalInvoice()
		});
		
		$(document).on('change', '#persen_retensi', function(){  
			let um1                = $('#persen').val();
			let um2                = $('#persen2').val();		  			
			let retensi_um         = $('#retensi_um').val();		   
			let datRetensi         = $('#persen_retensi').val();
			let totalRetensi       = 0
			let result1_hidden1 = 0
			let result2_hidden1 = 0
			let result3_hidden1 = 0
			let result4_hidden1 = 0
			let result5_hidden1 = 0
			let result6_hidden1 = 0
			let result7_hidden1 = 0
			let result8_hidden1 = 0
			
			let result1_hidden  = $('.result1_hidden').val();
			let result2_hidden  = $('.result2_hidden').val();
			let result3_hidden  = $('.result3_hidden').val();
			let result4_hidden  = $('.result4_hidden').val();
			let result5_hidden  = $('.result5_hidden').val();
			let result6_hidden  = $('.result6_hidden').val();
			let result7_hidden  = $('.result7_hidden').val();
			let result8_hidden  = $('.result8_hidden').val();
			
			$('#potongan_retensi2').val(0);
			$('#potongan_retensi_hidden2').val(0);
			
			if($('#potongan_retensi2').val == 0){
				$("#persen_retensi2 option[value='0']").attr('selected',true);
			}
			
			if(result1_hidden==null){
				result1_hidden1 = 0;
			}
			else{
				result1_hidden1 = result1_hidden;
			}
			
			if(result2_hidden==null){
				result2_hidden1 = 0;
			}
			else{
				result2_hidden1 = result2_hidden;
			}
			if(result3_hidden==null){
				result3_hidden1 = 0;
			}
			else{
				result3_hidden1 = result3_hidden;
			}
			
			if(retensi_um > 0){
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100)*(100/100 - getNum(um2/100)).toFixed(2);
			}
			else{
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100).toFixed(2);
			}
			
			var totalret   = totalRetensi.toFixed(2);
			 
			$('.potongan_retensi').val(num2(totalret));
			$('.potongan_retensi_hidden').val(totalret);
			
			totalInvoice();
		});
		
		$(document).on('change', '#persen_retensi2', function(){
			   
			let um1                = $('#persen').val();
			let um2                = $('#persen2').val();		  			
			let retensi_um         = $('#retensi_um').val();		   
			let datRetensi         = $('#persen_retensi2').val();
			let totalRetensi       = 0
			let result1_hidden1 = 0
			let result2_hidden1 = 0
			let result3_hidden1 = 0
			let result4_hidden1 = 0
			let result5_hidden1 = 0
			let result6_hidden1 = 0
			let result7_hidden1 = 0
			let result8_hidden1 = 0
			
			let result1_hidden  = $('.result1_hidden').val();
			let result2_hidden  = $('.result2_hidden').val();
			let result3_hidden  = $('.result3_hidden').val();
			let result4_hidden  = $('.result4_hidden').val();
			let result5_hidden  = $('.result5_hidden').val();
			let result6_hidden  = $('.result6_hidden').val();
			let result7_hidden  = $('.result7_hidden').val();
			let result8_hidden  = $('.result8_hidden').val();
			
			$('#potongan_retensi').val(0);
			$('#potongan_retensi_hidden').val(0);
			
			if($('#potongan_retensi').val == 0){
				$("#persen_retensi option[value='0']").attr('selected',true);
			}
			
		
			if(result1_hidden==null){
				result1_hidden1 = 0;
			}
			else{
				result1_hidden1 = result1_hidden;
			}
			
			if(result2_hidden==null){
				result2_hidden1 = 0;
			}
			else{
				result2_hidden1 = result2_hidden;
			}
			
			if(result3_hidden==null){
				result3_hidden1 = 0;
			}
			else{
				result3_hidden1 = result3_hidden;
			}
			
			if(retensi_um > 0){
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100)*(100/100 - getNum(um2/100)).toFixed(2);
			}
			else{
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100).toFixed(2);
			}
			
			var totalret   	= totalRetensi;
			var retensiPPN  = totalRetensi * 10/100;
			
			var Retensi2	= totalret - retensiPPN;

			$('.potongan_retensi2').val(number_format(Retensi2,2));
			$('.potongan_retensi_hidden2').val(Retensi2);
			
			totalInvoice();
		});
		
		$(document).on('blur', '.potongan_retensix', function(){
			
			let dataPpn	  = $('#ppnselect').val();
			let dataRetensi	  = $(this).val();
			let totalRetensi     = getNum(dataRetensi).toFixed(2);
			let grandtotal   = $(".grand_total_hidden").val();
			let totalDiskon     = $(".diskon_hidden").val();
			let uangmuka     = $(".down_payment_hidden").val();
			
			$('.potongan_retensi').val(num2(totalRetensi));
			$('.potongan_retensi_hidden').val(totalRetensi);
			
			if(dataPpn==1){
			var totalPpn     = getNum((grandtotal-totalDiskon-totalRetensi-uangmuka)*0.1).toFixed(2);			
			}
			else{
			var totalPpn     = getNum(0).toFixed(2);	
			}
			
			$('.ppn').val(num2(totalPpn));
			$('.ppn_hidden').val(totalPpn);
			 
			totalInvoice();
		});
		
		$(document).on('blur', '.ppn', function(){
			
			let dataPpn	  = $(this).val();
			let totalPpn     = getNum(dataPpn).toFixed(2);
			
			$('.ppn').val(num2(totalPpn));
			$('.ppn_hidden').val(totalPpn);
			 
			totalInvoice();
		});
		
		$(document).on('change', '#ppnselect', function(){
			ppn();
		});
		
		
		
		
		
		
		
		
		
		
		
		
		$(document).on('click','#back', function(){
			window.location.href = base_url + active_controller;
		});
	});
	
	
	//FUNCTION
	let delRow = (row) => {
		$('#tr_'+row).remove();
		fnAlltotal();
	}
	
	let delRow2 = (row) => {
		$('#tr1_'+row).remove();
		fnAlltotal2();
	}
	
	let delRow3 = (row) => {
		$('#tr2_'+row).remove();
		fnAlltotal3();
	}
	
	let delRow4 = (row) => {
		$('#tr3_'+row).remove();
		fnAlltotal4();
	}
	
	let delRow5 = (row) => {
		$('#tr4_'+row).remove();
		fnAlltotal5();
	}
	
	let delRow6 = (row) => {
		$('#tr5_'+row).remove();
		fnAlltotal6();
	}
	
	let delRow7 = (row) => {
		$('#tr6_'+row).remove();
		fnAlltotal7();
	}
	
	let delRow8 = (row) => {
		$('#tr7_'+row).remove();
		fnAlltotal8();
	}
	
	let umLoad = () => {	
		let hargaSat  = getNum($('#harga_tot_hidden').val());
		let dataIni	  = getNum($('#um_persen').val());
		let nilai;
		let total;
		let id;
		//PRODUCT
		let SUM = 0;
		$(".changeProduct" ).each(function() {
			id = $(this).data('id');
			let harga_satuan = getNum($('#harga_sat_'+id).val().split(",").join(""));
			let qty_sisa = getNum($('#qty_'+id).val().split(",").join(""));
			nilai 	= Number($(this).val().split(",").join(""));
			// nilai 	= harga_satuan * qty_sisa;
			total   = nilai * (dataIni/100);
			SUM += total;
			$('#harga_tot_'+id).val(num(total));
			$('#harga_tot_hidden'+id).val(total.toFixed(2));
		});
		$('#tot_product').val(num(SUM));
		$('#tot_product_hidden').val(SUM.toFixed(2));

		//AKSESORIS
		let SUM2 = 0;
		$(".changeAcc").each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM2 += total;
			$('#harga_tot2'+id).val(num(total));
			$('#harga_tot2_hidden'+id).val(total.toFixed(2));
		});
		$('#total_bq_nf').val(num(SUM2));
		$('#total_bq_nf_hidden').val(SUM2.toFixed(2));

		//MATERIAL
		let SUM3 = 0;
		$(".changeMat" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM3 += total;
			$('#harga_tot3'+id).val(num(total));
			$('#harga_tot3_hidden'+id).val(total.toFixed(2));
		});
		$('#total_material3').val(num(SUM3));
		$('#total_material_hidden3').val(SUM3.toFixed(2));

		//ENGINE
		let SUM4 = 0;
		$(".changeEng" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM4 += total;
			console.log(total);
			console.log(nilai);
			$('#harga_tot4'+id).val(num(total));
			$('#harga_tot4_hidden'+id).val(total.toFixed(2));
		});
		$('#total_enginering1').val(num(SUM4));
		$('#total_enginering_hidden1').val(SUM4.toFixed(2));

		//PACKING
		let SUM5 = 0;
		$(".changePack" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM5 += total;
			$('#harga_tot5'+id).val(num(total));
			$('#harga_tot5_hidden'+id).val(total.toFixed(2));
		});
		$('#total_packing1').val(num(SUM5));
		$('#total_packing_hidden1').val(SUM5.toFixed(2));

		//SHIPPING E
		let SUM6 = 0;
		$(".changeShip" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM6 += total;
			$('#harga_tot6'+id).val(num(total));
			$('#harga_tot6_hidden'+id).val(total.toFixed(2));
		});
		$('#total_trucking1').val(num(SUM6));
		$('#total_trucking_hidden1').val(SUM6.toFixed(2));
		
		// var total_grand = SUM + SUM2+ SUM3+ SUM4+ SUM5+ SUM6+ SUM7;
		
		// $('#grand_total').val(num(total_grand));
		// $('#grand_total_hidden').val(total_grand.toFixed(2));

        grandtotal();	
		totalInvoice();
	}

	let umLoad2 = () => {	
		let hargaSat  = $('#harga_tot_hidden').val();
		let dataIni	  = Number($('#um_persen2').val());
		let nilai;
		let total;
		let id;
		//PRODUCT
		let SUM = 0;
		$(".changeProduct" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM += total;
			$('#harga_tot_'+id).val(num(total));
			$('#harga_tot_hidden'+id).val(total.toFixed(2));
		});
		$('#tot_product').val(num(SUM));
		$('#tot_product_hidden').val(SUM.toFixed(2));

		//AKSESORIS
		let SUM2 = 0;
		$(".changeAcc").each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM2 += total;
			$('#harga_tot2'+id).val(num(total));
			$('#harga_tot2_hidden'+id).val(total.toFixed(2));
		});
		$('#total_bq_nf').val(num(SUM2));
		$('#total_bq_nf_hidden').val(SUM2.toFixed(2));

		//MATERIAL
		let SUM3 = 0;
		$(".changeMat" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM3 += total;
			$('#harga_tot3'+id).val(num(total));
			$('#harga_tot3_hidden'+id).val(total.toFixed(2));
		});
		$('#total_material3').val(num(SUM3));
		$('#total_material_hidden3').val(SUM3.toFixed(2));

		//ENGINE
		let SUM4 = 0;
		$(".changeEng" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM4 += total;
			$('#harga_tot4'+id).val(num(total));
			$('#harga_tot4_hidden'+id).val(total.toFixed(2));
		});
		$('#total_enginering1').val(num(SUM4));
		$('#total_enginering_hidden1').val(SUM4.toFixed(2));

		//PACKING
		let SUM5 = 0;
		$(".changePack" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM5 += total;
			$('#harga_tot5'+id).val(num(total));
			$('#harga_tot5_hidden'+id).val(total.toFixed(2));
		});
		$('#total_packing1').val(num(SUM5));
		$('#total_packing_hidden1').val(SUM5.toFixed(2));

		//SHIPPING E
		let SUM6 = 0;
		$(".changeShip" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM6 += total;
			$('#harga_tot6'+id).val(num(total));
			$('#harga_tot6_hidden'+id).val(total.toFixed(2));
		});
		$('#total_trucking1').val(num(SUM6));
		$('#total_trucking_hidden1').val(SUM6.toFixed(2));
		
		// var total_grand = SUM + SUM2+ SUM3+ SUM4+ SUM5+ SUM6+ SUM7;
		
		// $('#grand_total').val(num(total_grand));
		// $('#grand_total_hidden').val(total_grand.toFixed(2));

        grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal = () => {
	  let total=0
	  let total2=0
		$(".amount1").each(function(){
			 total += getNum($(this).val()||0);
			 total2 += getNum($(this).val()||0);
		});
		
		$(".result1").val(num(total));
		$(".result1_hidden").val(num3(total2));
		
		grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal2 = () => {
	  let total=0
	  let total2=0
		$(".amount2").each(function(){
			 total += getNum($(this).val()||0);
			 total2 += getNum($(this).val()||0);
		});
		
		$(".result2").val(num(total));
		$(".result2_hidden").val(num3(total2));
		
		grandtotal();		
		totalInvoice();
	}

	let fnAlltotal3 = () => {
		let total31=0
		let total32=0
		$(".amount3").each(function(){
			 total31 += getNum($(this).val()||0);
			 total32 += getNum($(this).val()||0);
		});
		
		$(".result3").val(num(total31));
		$(".result3_hidden").val(num3(total32));
		
		grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal4 = () => {
	  let total41=0
	  let total42=0
		$(".amount4").each(function(){
			 total41 += getNum($(this).val()||0);
			 total42 += getNum($(this).val()||0);
		});
		
		$(".result4").val(num(total41));
		$(".result4_hidden").val(num3(total42));
		
		grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal5 = () => {
		let total51=0
		let total52=0
		$(".amount5").each(function(){
			 total51 += getNum($(this).val()||0);
			 total52 += getNum($(this).val()||0);
		});
		
		$(".result5").val(num(total51));
		$(".result5_hidden").val(num3(total52));
		
		grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal6 = () => {
		let total61=0
		let total62=0
		$(".amount6").each(function(){
			 total61 += getNum($(this).val()||0);
			 total62 += getNum($(this).val()||0);
		});
		
		$(".result6").val(num(total61));
		$(".result6_hidden").val(num3(total62));
		
		grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal7 = () => {
		let total71=0
		let total72=0

		$(".amount7").each(function(){
			 total71 += getNum($(this).val()||0);
			 total72 += getNum($(this).val()||0); 
		});
		
		$(".result7").val(num(total71));
		$(".result7_hidden").val(num3(total72));
		
		grandtotal();		
		totalInvoice();
	}
	
	let fnAlltotal8 = () => {
		let total81=0
		let total82=0
		$(".amount8").each(function(){
			 total81 += getNum($(this).val()||0);
			 total82 += getNum($(this).val()||0);
		});
		
		$(".result8").val(num(total81));
		$(".result8_hidden").val(num3(total82));
		
		grandtotal();		
		totalInvoice();
	}
		
	let grandtotal = () => {
		let dataPpn    = $('#ppnselect').val();
		let result1_hidden1 = 0;
		let result2_hidden1 = 0;
		let result3_hidden1 = 0;
		let result4_hidden1 = 0;
		let result5_hidden1 = 0;
		let result6_hidden1 = 0;
		let result7_hidden1 = 0;
		let result8_hidden1 = 0;
		
		let result1_hidden  = getNum($('.result1_hidden').val());
		let result2_hidden  = getNum($('.result2_hidden').val());
		let result3_hidden  = getNum($('.result3_hidden').val());
		let result4_hidden  = getNum($('.result4_hidden').val());
		let result5_hidden  = getNum($('.result5_hidden').val());
		let result6_hidden  = getNum($('.result6_hidden').val());
		let result7_hidden  = getNum($('.result7_hidden').val());
		let result8_hidden  = getNum($('.result8_hidden').val());
		let diskon_hidden  			= getNum($('.diskon_hidden').val());
		let potongan_retensi_hidden = getNum($('.potongan_retensi_hidden').val());
		let down_payment_hidden     = getNum($('.down_payment_hidden').val());
		let uang_muka  				= getNum($('.persen').val());
		let uang_muka2 				= getNum($('.persen2').val());
		
		result1_hidden1 = result1_hidden==null ? 0 : result1_hidden;
		result2_hidden1 = result2_hidden==null ? 0 : result2_hidden;
		result3_hidden1 = result3_hidden==null ? 0 : result3_hidden;
		result4_hidden1 = result4_hidden==null ? 0 : result4_hidden;
		result5_hidden1 = result5_hidden==null ? 0 : result5_hidden;
		result6_hidden1 = result6_hidden==null ? 0 : result6_hidden;
		result7_hidden1 = result7_hidden==null ? 0 : result7_hidden;
		result8_hidden1 = result8_hidden==null ? 0 : result8_hidden;		
		
		let grandtotal 	= 	getNum(result1_hidden1)
							+ getNum(result2_hidden1)
							+ getNum(result3_hidden1)
							+ getNum(result4_hidden1)
							+ getNum(result5_hidden1)
							+ getNum(result6_hidden1)
							+ getNum(result7_hidden1)
							+ getNum(result8_hidden1);
		
		let uangmuka   	= 	(getNum(result1_hidden1)+ getNum(result2_hidden1)+ getNum(result3_hidden1)) * getNum(uang_muka/100);
		   
	    let uangmuka2  	= 	(getNum(result1_hidden1)+ getNum(result2_hidden1)+ getNum(result3_hidden1))
							*
							( getNum(uang_muka2/100) - (getNum(uang_muka/100) * getNum(uang_muka2/100)) );

		let totalPpn    = dataPpn==1 ? getNum((grandtotal - diskon_hidden - potongan_retensi_hidden - down_payment_hidden - uangmuka)*0.1) : 0;			

		$(".down_payment").val(number_format(uangmuka,2));
		$(".down_payment_hidden").val(num3(uangmuka));
		$(".down_payment2").val(number_format(uangmuka2,2));
		$(".down_payment_hidden2").val(num3(uangmuka2));
		$(".grand_total").val(number_format(grandtotal,2));
		$(".grand_total_hidden").val(num3(grandtotal));
		ppn();
	}
		
	let ppn = () => {  
		let dataPpn                 = $('#ppnselect').val();
		let ppntotal                = 0
		let grandtotal              = $(".grand_total_hidden").val();
		let diskon_hidden           = $('.diskon_hidden').val();
		let potongan_retensi_hidden = $('.potongan_retensi_hidden').val();
		let down_payment_hidden     = $('.down_payment_hidden').val()
		let down_payment_hidden2    = $('.down_payment_hidden2').val()

		let	diskon_hidden1 				= diskon_hidden==null ? 0 : diskon_hidden;
		let	potongan_retensi_hidden1 	= potongan_retensi_hidden==null ? 0 : potongan_retensi_hidden;
		let	down_payment_hidden1 		= down_payment_hidden==null ? 0 : down_payment_hidden;
		let	down_payment_hidden12 		= down_payment_hidden2==null ? 0 : down_payment_hidden2;
		let totalPpn     = dataPpn==1 ? getNum((grandtotal - diskon_hidden1 - potongan_retensi_hidden1 - down_payment_hidden1- down_payment_hidden12)*0.1) : 0;			
		
		$('.ppn').val(num(totalPpn));
		$('.ppn_hidden').val(num3(totalPpn));
		totalInvoice();
	}
	
	let totalInvoice = () => {
		let grandtotal 		= 0;
		let result1_hidden1 = 0;
		let result2_hidden1 = 0;
		let result3_hidden1 = 0;
		let result4_hidden1 = 0;
		let result5_hidden1 = 0;
		let result6_hidden1 = 0;
		let result7_hidden1 = 0;
		let result8_hidden1 = 0;
		let potongan_retensi_hidden1 	= 0;
		let down_payment_hidden1 		= 0;
		let down_payment_hidden12 		= 0;
		let result1_hidden  = $('.result1_hidden').val();
		let result2_hidden  = $('.result2_hidden').val();
		let result3_hidden  = $('.result3_hidden').val();
		let result4_hidden  = $('.result4_hidden').val();
		let result5_hidden  = $('.result5_hidden').val();
		let result6_hidden  = $('.result6_hidden').val();
		let result7_hidden  = $('.result7_hidden').val();
		let result8_hidden  = $('.result8_hidden').val();
		let diskon_hidden  				= $('.diskon_hidden').val();
		let potongan_retensi_hidden  	= $('.potongan_retensi_hidden').val();
		let potongan_retensi_hidden2  	= $('.potongan_retensi_hidden2').val();
		let ppn_hidden  				= $('.ppn_hidden').val();
		let down_payment_hidden  		= $('.down_payment_hidden').val()
		let down_payment_hidden2  		= $('.down_payment_hidden2').val()   
	
		result1_hidden1 = result1_hidden==null ? 0 : result1_hidden;
		result2_hidden1 = result2_hidden==null ? 0 : result2_hidden;
		result3_hidden1 = result3_hidden==null ? 0 : result3_hidden;
		result4_hidden1 = result4_hidden==null ? 0 : result4_hidden;
		result5_hidden1 = result5_hidden==null ? 0 : result5_hidden;
		result6_hidden1 = result6_hidden==null ? 0 : result6_hidden;
		result7_hidden1 = result7_hidden==null ? 0 : result7_hidden;
		result8_hidden1 = result8_hidden==null ? 0 : result8_hidden;
		
		potongan_retensi_hidden1 	= potongan_retensi_hidden==null ? 0 : potongan_retensi_hidden;
		down_payment_hidden1 		= down_payment_hidden==null ? 0 : down_payment_hidden;
		down_payment_hidden12 		= down_payment_hidden2==null ? 0 : down_payment_hidden2;
		
		grandtotal 	= 	(getNum(result1_hidden1)
						+ getNum(result2_hidden1)
						+ getNum(result3_hidden1)
						+ getNum(result4_hidden1)
						+ getNum(result5_hidden1)
						+ getNum(result6_hidden1)
						+ getNum(ppn_hidden)
						+ getNum(result7_hidden1)
						+ getNum(result8_hidden1)
						- getNum(diskon_hidden)
						- getNum(potongan_retensi_hidden1)
						- getNum(potongan_retensi_hidden2)
						- getNum(down_payment_hidden1)
						- getNum(down_payment_hidden12));
		
		$(".total_invoice").val(num(grandtotal));
		$(".total_invoice_hidden").val(num3(grandtotal));
	}
	
	function num(n) {
      return (n).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
	
	function num2(n) {
      return (n).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
	
	function num3(n) {
      return (n).toFixed(2);
    }
</script>
