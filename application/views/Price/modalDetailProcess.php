<?php
$id_bq = $this->uri->segment(3);
$id_product = $this->uri->segment(4);
$id = $this->uri->segment(5);


if(empty($id_product)){
	$getDetail = $this->db->query("SELECT * FROM cost_lain_sum WHERE id_bq = '".$id_bq."'")->row();
}

if(!empty($id_product)){
	$sqlH = "SELECT
				a.id_milik,
				a.id_bq,
				b.parent_product AS id_category,
				a.qty,
				b.diameter AS diameter_1,
				b.diameter2 AS diameter_2,
				b.panjang AS length,
				b.thickness,
				b.angle AS sudut,
				b.type,
				a.id_product,
				a.man_power,
				a.id_mesin,
				a.total_time,
				a.man_hours,
				b.standart_code,
				( a.est_harga * a.qty ) AS est_harga2,
				( a.sum_mat * a.qty ) AS sum_mat2,
				b.pressure,
				b.liner,
				(a.direct_labour * a.qty) AS direct_labour,
				(a.indirect_labour * a.qty) AS indirect_labour,
				(a.machine * a.qty) AS machine,
				(a.mould_mandrill * a.qty) AS mould_mandrill, 
				(a.consumable * a.qty) AS consumable,
				(
					((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
				) * ( (b.pe_foh_consumable) / 100 ) * a.qty AS foh_consumable,
				(
					((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
				) * ( (b.pe_foh_depresiasi) / 100 ) * a.qty AS foh_depresiasi,
				(
					((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
				) * ( (b.pe_biaya_gaji_non_produksi) / 100 ) * a.qty AS biaya_gaji_non_produksi,
				(
					((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
				) * ( (b.pe_biaya_non_produksi) / 100 ) * a.qty AS biaya_non_produksi,
				(
					((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable)) + `a`.`est_harga` 
				) * ( (b.pe_biaya_rutin_bulanan) / 100 ) * a.qty AS biaya_rutin_bulanan  
			FROM
				estimasi_cost_and_mat a
				INNER JOIN bq_product b ON a.id_milik = b.id
			WHERE
				b.parent_product <> 'pipe slongsong' 
				AND a.id_bq = '".$id_bq."'
				AND a.id_milik = '".$id."'
			ORDER BY
				a.id_milik ASC";
	// echo $sqlH;
	$getDetail = $this->db->query($sqlH)->row();
	// echo "<pre>";
	// print_r($getDetail);
}
// echo "<pre>";
	// print_r($getDetail);

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
					<td align='right'><?= number_format($getDetail->machine, 2);?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mould & Mandrill Cost</td>
					<td align='right'><?= number_format($getDetail->mould_mandrill, 2);?></td>
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
																		$getDetail->machine + 
																		$getDetail->mould_mandrill +
																		$getDetail->foh_consumable + 
																		$getDetail->foh_depresiasi + 
																		$getDetail->biaya_gaji_non_produksi + 
																		$getDetail->biaya_non_produksi + 
																		$getDetail->biaya_rutin_bulanan, 2);?></b></td>
				</tr>
			</tfoot>
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr>
					<td colspan='2'><b>DETAIL CYCLETIME</b></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Man Power</td>
					<td align='right'><?= $getDetail->man_power;?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Time</td>
					<td align='right'><?= $getDetail->total_time;?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Man Hours</td>
					<td align='right'><?= $getDetail->man_hours;?></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Machine Code</td>
					<td align='right'><?= (!empty($getDetail->id_mesin))?$getDetail->id_mesin:'FW00';?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>


<script>
	$(document).ready(function(){
		swal.close();
	});
</script>