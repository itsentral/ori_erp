<div class="box-body">
	<br>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Incoterms</b></label>
		<div class='col-sm-2'>
			<?php echo strtoupper($data[0]->incoterms); ?>
		</div>
		<label class='label-control col-sm-2'><b>Request Date</b></label>
		<div class='col-sm-2'>
			<?php echo strtoupper($data[0]->request_date); ?>
		</div>
		<label class='label-control col-sm-2'><b>Harga Pembelian</b></label>
		<div class='col-sm-2'>
			<select id='current' name='current' class='form-control input-sm chosen_select' disabled>
				<?php
				$kurs_mata_uang = (!empty($data[0]->mata_uang))?$data[0]->mata_uang:$data_rfq[0]->currency;
				foreach(get_list_kurs() AS $val => $valx){
					$sel = ($valx['kode_dari'] == $kurs_mata_uang)?'selected':'';
					echo "<option value='".$valx['kode_dari']."' ".$sel.">".$valx['kode_dari']." - ".strtoupper($valx['negara'])."</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Tax (%)</b></label>
		<div class='col-sm-2'>
			<?php echo number_format($data[0]->tax); ?>
		</div>
		<label class='label-control col-sm-2'><b>Term Of Payment</b></label>
		<div class='col-sm-2'>
		<?php echo strtoupper($data[0]->top); ?>
		</div>
		<label class='label-control col-sm-2'><b>Remarks</b></label>
		<div class='col-sm-2'>
			<?php echo strtoupper($data[0]->remarks); ?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Buyer</b></label>
		<div class='col-sm-8'>
			<?php
			$buyer = (!empty($data[0]->buyer))?strtoupper($data[0]->buyer):strtoupper(get_name('users','nm_lengkap','username',$data[0]->updated_by));
			 echo $buyer; ?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'>Detail Barang</label>
		<div class='col-sm-11'>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center">Nama Barang</th>
						<th class="text-center" width='10%'>Qty</th>
						<th class="text-center" width='20%'>Price/Unit</th>
						<th class="text-center" width='20%'>Total Price</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$jumlah = count($result);
					$no  = 0;
					$SUM = 0;
					foreach($result AS $val => $valx){ $no++;
						$qty_p = (!empty($valx['qty_po']))?$valx['qty_po']:$valx['qty_purchase'];
						$SUM += $qty_p * $valx['price_ref_sup'];
						echo "<tr>";
							echo "<td align='left'>".strtoupper($valx['nm_material'])."</td>";
							echo "<td align='right'>".number_format($qty_p,2)."</td>";
							echo "<td align='right'>".number_format($valx['price_ref_sup'],2)." <b class='text-primary'>".strtoupper($valx['currency'])."</b></td>";
							echo "<td align='right'><div id='qtytot_".$no."' class='sum_tot'>".number_format($qty_p * $valx['price_ref_sup'],2)."</div></td>";
						echo "</tr>";
					}
					?>
					<tr>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><?=number_format($data[0]->total_po,2);?></td>
					</tr>
					<tr>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>DISCOUNT (%)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><?=number_format($data[0]->discount,2);?></td>
					</tr>
					<tr>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>NET PRICE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><div id='total'><?=number_format($data[0]->net_price,2);?></div></td>
					</tr>
					<tr>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>TAX (%)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><?=number_format($data[0]->tax,2);?></td>
					</tr>
					<tr>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>NET PRICE + TAX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><?=number_format($data[0]->net_plus_tax,2);?></td>
					</tr>
					<tr hidden>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>DELIVERY COST&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><?=number_format($data[0]->delivery_cost,2);?></td>
					</tr>
					<tr>
						<td align='left' colspan='2'></td>					
						<td class='text-right'><b>GRAND TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
						<td class='text-right mid text-bold'><?=number_format($data[0]->total_price,2);?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'>TOP</label>
		<div class='col-sm-11'>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='15%'>Group TOP</th>
						<th class="text-center" width='10%'>Progress (%)</th>						
						<th class="text-center" width='15%'>Value</th>
						<th class="text-center" width='25%'>Keterangan</th>
						<th class="text-center" width='12%'>Est Jatuh Tempo</th>
						<th class="text-center" width='23%'>Persyaratan</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$id = 0;
					if(!empty($data_top)){
						foreach($data_top AS $val => $valx){ $id++;
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_po[".$id."][group_top]' class='form-control text-left chosen_select' disabled>";
										echo "<option value='0'>Select Group TOP</option>";
										foreach($payment AS $val2 => $valx2){
											$sel = ($valx2['name'] == $valx['group_top'])?'selected':'';
											echo "<option value='".$valx2['name']."' ".$sel.">".strtoupper($valx2['name'])."</option>";
										}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>".$valx['progress']."</td>";
								echo "<td align='left'>".number_format($valx['value_idr'])."</td>";
								echo "<td align='left'>".strtoupper($valx['keterangan'])."</td>";
								echo "<td align='left'>".$valx['jatuh_tempo']."</td>";
								echo "<td align='left'>".strtoupper($valx['syarat'])."</td>";
							echo "</tr>";
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>