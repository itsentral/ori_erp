			
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<?php
				$SUM=0;
				if(!empty($get_retensi)){
					?>
					<thead>
						<tr class='bg-blue'>
							<td class="text-left headX HeaderHr" colspan='3'><b>RETENSI</b></td>
						</tr>
						<tr class='bg-blue'>
							<th class="text-center" width = '2%'>#</th>
							<th class="text-center">Category</th>
							<th class="text-center" width = '15%'>Total Price</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$SUM=0;
						$nomor=0;
						foreach($get_retensi AS $val => $valx){
							$nomor++;
							$harga_tot6			= number_format($valx['total_retensi2'],2);
							$harga_tot6_hidden	= round($valx['total_retensi2'],2);
							$SUM += $harga_tot6_hidden;
							$material_name = ($valx['nomor_po']).' / RETENSI';
							?>
							<tr id='tr5_<?= $nomor;?>' >
								<td align='center'><span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow6(<?= $nomor;?>)'><i class="fa fa-times"></i></a></span></td>
								<td>
									<input type="text" class="form-control" id="material_name6" name="data6[<?=$nomor ?>][material_name6]" value="<?=set_value('material_name6', isset($material_name) ? $material_name : ''); ?>" readonly tabindex='-1' >
									<input type="hidden" id="unit6_ipp_<?=$nomor ?>" name="data6[<?=$nomor ?>][no_ipp]" value="<?=$valx['nomor_po']?>" >
									<input type="hidden" id="unit6_so_<?=$nomor ?>" name="data6[<?=$nomor ?>][no_so]" value="<?=($valx['nomor_po'])?>" >
								</td>
								<td>
									<input type="hidden" class="form-control" id="unit6" name="data6[<?=$nomor ?>][unit6]" value="" readonly >
									<input type="text" class="form-control text-right harga_tot6 changeAll divide" id="harga_tot6<?=$nomor ?>" data-nomor='<?=$nomor ?>' readonly name="data6[<?=$nomor ?>][harga_tot6]" value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" >
									<input type="hidden" class="form-control amount6 " id="harga_tot6_hidden<?=$nomor ?>" name="data6[<?=$nomor ?>][harga_tot6_hidden]" value="<?=set_value('harga_tot6_hidden', isset($harga_tot6_hidden) ? $harga_tot6_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changeShip" data-id='<?=$nomor ?>' value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>">
								</td>
							</tr>
							<?php
						}
						?>
						<tr id='tr5X' class='FootColor'>
							<td></td>
							<td><b>TOTAL RETENSI</b></td>
							<td align="right">
								<?php
								$total_trucking= number_format($SUM,2);
								$total_trucking_hidden= round($SUM,2);
								?>
								<input type="text" class="form-control text-right result6 divide" id="total_trucking" name="total_trucking" value="<?=set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" readonly >
								<input type="hidden" class="form-control result6_hidden" id="total_trucking_hidden" name="total_trucking_hidden" value="<?=set_value('total_trucking_hidden', isset($total_trucking_hidden) ? $total_trucking_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control changeShipTot" value="<?=set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" readonly >
							</td>
						</tr>
					</tbody>
				<?php } ?>	
				<tfoot>
					<tr class='HeaderHr'>
						<td align='right' colspan='3' height='20px;'></td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>TOTAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right' style='text-align:center;'>
							<?php 
								$grand_total 		= number_format($SUM, 2);
								$grand_total_hidden = round($SUM, 2);
							?>
							<input type="text" class="form-control grand_total text-right input-sm divide" id="grand_total" name="grand_total" value="<?php echo set_value('grand_total_hidden', isset($penagihan->total_dpp_usd) ? $penagihan->total_dpp_usd : '0'); ?>" tabindex="-1">
							<input type="hidden" class="grand_total_hidden" id="grand_total_hidden" name="grand_total_hidden" value="<?php echo set_value('grand_total_hidden', isset($penagihan->total_dpp_usd) ? $penagihan->total_dpp_usd : '0'); ?>">
						</td>
					</tr>
					<tr class='HeaderHr hidden'>
						<td align='right' colspan='2'><b>DISKON</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<input type="text" class="form-control diskon text-right  input-sm divide" id="diskon" name="diskon" value="<?=(isset($penagihan[0]->total_diskon)?$penagihan[0]->total_diskon:0)?>" placeholder="Diskon">
							<input type="hidden" class="diskon_hidden" id="diskon_hidden" name="diskon_hidden" value="<?=(isset($penagihan[0]->total_diskon)?$penagihan[0]->total_diskon:0)?>">
						</td>
					</tr>
					
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>PPN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<input type="text" class="form-control ppn text-right input-sm divide" id="ppn" name="ppn" value="<?=(isset($penagihan[0]->total_ppn)?$penagihan[0]->total_ppn:0)?>" tabindex="-1">
							<input type="hidden" class="ppn_hidden" id="ppn_hidden" name="ppn_hidden" value="<?=(isset($penagihan[0]->total_ppn)?$penagihan[0]->total_ppn:0)?>">
						</td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>TOTAL INVOICE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<?php 
							$grand_total = number_format($SUM, 2);
							$grand_total_hidden = round($SUM, 2);
							?>
							<input type="text" class="form-control total_invoice text-right input-sm divide" id="total_invoice" name="total_invoice" value="<?=(isset($penagihan[0]->total_ppn)?$penagihan[0]->total_invoice:0)?>" tabindex="-1">
							<input type="hidden" class="total_invoice_hidden" id="total_invoice_hidden" name="total_invoice_hidden" value="<?=(isset($penagihan[0]->total_ppn)?$penagihan[0]->total_invoice:0)?>">
						</td>
					</tr>
				</tfoot>
			</table>
