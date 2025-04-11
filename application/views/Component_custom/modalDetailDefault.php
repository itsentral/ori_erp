<?php

$id = $this->uri->segment(3);

$sqlDefault	= "SELECT * FROM help_default WHERE id='".$id."' ";
$getDefault	= $this->db->query($sqlDefault)->result_array();


?>
<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><?= $getDefault[0]['standart_code']; ?> (DN<?= $getDefault[0]['diameter']; ?>)</th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Item</th>
					<th class="text-left" width='66%'>Standar Value</th>
				<tr>
					<td class="text-left">Standart</td>
					<td class="text-left"><?= $getDefault[0]['standart_code']; ?></td>
				</tr>
				<tr>
					<td class="text-left">Product</td>
					<td class="text-left"><?= strtoupper($getDefault[0]['product_parent']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Diameter</td>
					<td class="text-left"><?= floatval($getDefault[0]['diameter']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Diameter 2</td>
					<td class="text-left"><?= floatval($getDefault[0]['diameter2']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Toleransi Max</td>
					<td class="text-left"><?= floatval($getDefault[0]['max']) * 100; ?> %</td>
				</tr>
				<tr>
					<td class="text-left">Toleransi Min</td>
					<td class="text-left"><?= floatval($getDefault[0]['min']) * 100; ?> %</td>
				</tr>
				<tr>
					<td class="text-left">Layer Plastic Film</td>
					<td class="text-left"><?= floatval($getDefault[0]['plastic_film']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Waste</td>
					<td class="text-left"><?= floatval($getDefault[0]['waste']); ?> %</td>
				</tr>
				<tr>
					<td class="text-left">Overlap</td>
					<td class="text-left"><?= floatval($getDefault[0]['overlap']); ?></td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>LINER THICKNESS</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left">Veil</td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_resin_veil_a'])."/".floatval($getDefault[0]['lin_resin_veil_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_faktor_veil']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add Veil</td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_resin_veil_add_a'])."/".floatval($getDefault[0]['lin_resin_veil_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_faktor_veil_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_resin_csm_a'])."/".floatval($getDefault[0]['lin_resin_csm_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_faktor_csm']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_resin_csm_add_a'])."/".floatval($getDefault[0]['lin_resin_csm_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_faktor_csm_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['lin_resin']); ?></td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>STRUCTURE THICKNESS</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left">CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_csm_a'])."/".floatval($getDefault[0]['str_resin_csm_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_csm']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_csm_add_a'])."/".floatval($getDefault[0]['str_resin_csm_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_csm_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">WR</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_wr_a'])."/".floatval($getDefault[0]['str_resin_wr_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_wr']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add WR</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_wr_add_a'])."/".floatval($getDefault[0]['str_resin_wr_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_wr_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left" rowspan='3'>Rooving</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_rv_a'])."/".floatval($getDefault[0]['str_resin_rv_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_rv']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Benang</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_rv_jb']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Bandwitch</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_rv_bw']); ?></td>
				</tr>
				<tr>
					<td class="text-left" rowspan='3'> Add Rooving</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_rv_add_a'])."/".floatval($getDefault[0]['str_resin_rv_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_rv_add']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Benang</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_rv_add_jb']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Bandwitch</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_faktor_rv_add_bw']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_resin_thickness']); ?></td>
				</tr>
			</tbody> 
		</table>
		<br>
		<?php if($getDefault[0]['product_parent'] == 'flange mould' OR $getDefault[0]['product_parent'] == 'flange slongsong'){ ?>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>STRUCTURE NECK 1</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left">CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_csm_a'])."/".floatval($getDefault[0]['str_n1_resin_csm_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_csm']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_csm_add_a'])."/".floatval($getDefault[0]['str_n1_resin_csm_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_csm_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">WR</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_wr_a'])."/".floatval($getDefault[0]['str_n1_resin_wr_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_wr']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add WR</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_wr_add_a'])."/".floatval($getDefault[0]['str_n1_resin_wr_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_wr_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left" rowspan='3'>Rooving</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_rv_a'])."/".floatval($getDefault[0]['str_n1_resin_rv_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_rv']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Benang</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_rv_jb']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Bandwitch</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_rv_bw']); ?></td>
				</tr>
				<tr>
					<td class="text-left" rowspan='3'> Add Rooving</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_rv_add_a'])."/".floatval($getDefault[0]['str_n1_resin_rv_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_rv_add']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Benang</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_rv_add_jb']); ?></td>
				</tr>
				<tr>
					<td class="text-center">Bandwitch</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_faktor_rv_add_bw']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n1_resin_thickness']); ?></td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>STRUCTURE NECK 2</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left">CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_resin_csm_a'])."/".floatval($getDefault[0]['str_n2_resin_csm_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_faktor_csm']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_resin_csm_add_a'])."/".floatval($getDefault[0]['str_n2_resin_csm_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_faktor_csm_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">WR</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_resin_wr_a'])."/".floatval($getDefault[0]['str_n2_resin_wr_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_faktor_wr']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add WR</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_resin_wr_add_a'])."/".floatval($getDefault[0]['str_n2_resin_wr_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_faktor_wr_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_resin']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin Thickness</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['str_n2_resin_thickness']); ?></td>
				</tr>
			</tbody> 
		</table>
		<br>
		<?php } ?>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>EKSTERNAL THICKNESS</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left">Veil</td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_resin_veil_a'])."/".floatval($getDefault[0]['eks_resin_veil_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_faktor_veil']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add Veil</td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_resin_veil_add_a'])."/".floatval($getDefault[0]['eks_resin_veil_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_faktor_veil_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_resin_csm_a'])."/".floatval($getDefault[0]['eks_resin_csm_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_faktor_csm']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Add CSM</td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_resin_csm_add_a'])."/".floatval($getDefault[0]['eks_resin_csm_add_b']); ?></td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_faktor_csm_add']); ?></td>
				</tr>
				<tr>
					<td class="text-left">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['eks_resin']); ?></td>
				</tr>
			</tbody> 
		</table>
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'><b>TOPCOAT</b></th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Material</th>
					<th class="text-center" width='33%'>Resin Content</th>
					<th class="text-center" width='33%'>Faktor</th>
				</tr>
				<tr>
					<td class="text-left">Resin</td>
					<td class="text-center">-</td>
					<td class="text-center"><?= floatval($getDefault[0]['topcoat_resin']); ?></td>
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