<?php
$id_bq = $this->uri->segment(3);
$id_product = $this->uri->segment(4);
$id = $this->uri->segment(5);


if(empty($id_product)){
	$getDetail = $this->db->query("SELECT * FROM cost_lain_sum WHERE id_bq = '".$id_bq."'")->row();
}

if(!empty($id_product)){
	// $getDetail = $this->db->query("SELECT * FROM cost_lain_sum_detail WHERE id_bq = '".$id_bq."' AND id_product = '".$id_product."'")->row();
	$getDetail = $this->db->query("SELECT * FROM cost_lain_sum_detail WHERE id = '".$id."' ")->row();
}


?>
<div class="box">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			
			<tbody>
				<tr>
					<td colspan='2'><b>COST PROCESS</b></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Direct Labour</td>
					<td align='right'><?= number_format($getDetail->direct_labour, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Indirect Labour</td>
					<td align='right'><?= number_format($getDetail->indirect_labour, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consumable</td>
					<td align='right'><?= number_format($getDetail->consumable, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Machine Cost</td>
					<td align='right'><?= number_format($getDetail->biaya_mesin, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mould & Mandrill Cost</td>
					<td align='right'><?= number_format($getDetail->biaya_mould, 2);?></td>
				</tr>
				<tr>
					<td colspan='2'><b>COST FOH</b></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consumable FOH</td>
					<td align='right'><?= number_format($getDetail->foh_consumable, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Depresiasi FOH</td>
					<td align='right'><?= number_format($getDetail->foh_depresiasi, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Biaya Gaji Non Produksi</td>
					<td align='right'><?= number_format($getDetail->biaya_gaji_non_produksi, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Biaya Non Produksi</td>
					<td align='right'><?= number_format($getDetail->biaya_non_produksi, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Biaya Rutin Bulanan</td>
					<td align='right'><?= number_format($getDetail->biaya_rutin_bulanan, 2);?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th align='center' width='60%'>TOTAL COST PRODUCT</th>
					<td align='right' width='40%' ><b><?= number_format($getDetail->direct_labour + 
																		$getDetail->indirect_labour + 
																		$getDetail->consumable + 
																		$getDetail->biaya_mesin + 
																		$getDetail->biaya_mould +
																		$getDetail->foh_consumable + 
																		$getDetail->foh_depresiasi + 
																		$getDetail->biaya_gaji_non_produksi + 
																		$getDetail->biaya_non_produksi + 
																		$getDetail->biaya_rutin_bulanan, 2);?></b></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>


<script>
	$(document).ready(function(){
		swal.close();
	});
</script>