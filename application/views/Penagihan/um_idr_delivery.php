<?php
$SUM=0;
foreach($getDetail AS $val => $valx){
	$SUM+=$valx['total_deal_idr'];
}
$SUM2=0;
foreach($getPackCost AS $val => $valx){
	$SUM2+=$valx['total_deal_idr'];
}
$SUM3=0;
foreach($getTruck AS $val => $valx){
	$SUM3+=$valx['total_deal_idr'];
}
$SUM1=0;
foreach($getEngCost AS $val => $valx){
	$SUM1+=$valx['total_deal_idr'];
}
$SUM_MAT=0;
foreach($material AS $val => $valx){
	$SUM_MAT+=$valx['total_deal_idr'];
}
$SUM_NONFRP=0;
foreach($non_frp AS $val => $valx){
	$SUM_NONFRP+=$valx['total_deal_idr'];
}
$grand_total = round($SUM + $SUM2 + $SUM3 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);
$total_so=$grand_total;
if(isset($penagihan[0]->total_invoice)){
	$grand_total = round($penagihan[0]->total_invoice, 2);
}
?>
<input type="text" id="total_so" name="total_so" value="<?=$total_so;?>" readonly>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
	<tfoot>
		<tr class='HeaderHr'>
			<td align='right' colspan='15' height='20px;'></td>
		</tr>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>TOTAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' style='text-align:center;' colspan='2'></td>
			<td align='right' style='text-align:center;' colspan='2'>
				<input type="text" class="form-control grand_total text-right input-sm divide" id="grand_total" name="grand_total" value="<?=(isset($penagihan[0]->total_diskon)?$penagihan[0]->total_dpp_rp:0)?>" tabindex="-1">
			</td>
		</tr>
		<tr class='HeaderHr hidden'>
			<td align='right' colspan='11'><b>DISKON</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control diskon text-right  input-sm divide" id="diskon" name="diskon" value="<?=(isset($penagihan[0]->total_diskon)?$penagihan[0]->total_diskon_idr:0)?>" placeholder="Diskon"  >
			</td>
		</tr>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>PPN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control ppn text-right input-sm divide" id="ppn" name="ppn" value="<?=(isset($penagihan[0]->total_ppn)?$penagihan[0]->total_ppn_idr:0)?>" tabindex="-1">
			</td>
		</tr>
		<tr class='HeaderHr'>
			<td align='right' colspan='11'><b>TOTAL INVOICE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td align='center' colspan='2'></td>
			<td align='right' colspan='2'>
				<input type="text" class="form-control total_invoice text-right input-sm divide" id="total_invoice" name="total_invoice" value="<?=(isset($penagihan[0]->total_invoice_idr)?$penagihan[0]->total_invoice_idr:0)?>" tabindex="-1">
			</td>
		</tr>
	</tfoot>
			</table>
			<br>
