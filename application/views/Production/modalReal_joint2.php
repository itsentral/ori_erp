<?php
	$id_product = $this->uri->segment(3);
	$id_produksi = $this->uri->segment(4);
	$idProducktion = $this->uri->segment(5);
	$id_milik = $this->uri->segment(6);
	// echo "Milik=>".$id_milik;

	$qProduksi		= "SELECT * FROM production_header WHERE id_produksi='".$id_produksi."' ";
	$restProduksi	= $this->db->query($qProduksi)->result_array();

	$qHeader			= "SELECT * FROM bq_component_header WHERE id_product='".$id_product."'";
	$restHeader			= $this->db->query($qHeader)->result_array();

	//LINER
	$qDetail1			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	if ($restHeader[0]['parent_product'] == 'branch joint' || $restHeader[0]['parent_product'] == 'shop joint' || $restHeader[0]['parent_product'] == 'field joint') {
		$qDetail1			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='GLASS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	}
	$restDetail1		= $this->db->query($qDetail1)->result_array();

	//STRUKTURE
	$qDetail2			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='STRUKTUR THICKNESS' AND id_material <> 'MTL-1903000' AND id_category <> 'TYP-0001'";
	if ($restHeader[0]['parent_product'] == 'branch joint' || $restHeader[0]['parent_product'] == 'shop joint' || $restHeader[0]['parent_product'] == 'field joint') {
		$qDetail2			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000'";
	}
	$restDetail2		= $this->db->query($qDetail2)->result_array();


	//EXTERNAL
	$qDetail3			= "SELECT * FROM bq_component_lamination WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='Inside Lamination' AND width >= 0";
	$restDetail3		= $this->db->query($qDetail3)->result_array();
	$NumDetail3			= $this->db->query($qDetail3)->num_rows();

	//TOPCOAT
	$qDetail4		= "SELECT * FROM bq_component_lamination WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='Outside Lamination' AND width > 0";
	$restDetail4	= $this->db->query($qDetail4)->result_array();
?>
	<input type='text' name='id_produksi' class='THide' value='<?= $id_produksi;?>'>
	<input type='text' name='product' class='THide' value='<?= $id_product;?>'>
	<input type='text' name='id_production_detail' class='THide' value='<?= $idProducktion;?>'>
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left"width='13%'><u>IPP Number</u></td>
						<td class="text-left" colspan='5'><b><?= $restProduksi[0]['no_ipp']; ?></b></td>
					</tr>
					<!--
					<tr>
						<td class="text-left" width='20%'><u>Product Name</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['nm_product']); ?></td>
						<td class="text-left" width='15%'><u>Diameter</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['diameter']; ?></td>
						<td class="text-left" width='15%'><u>Width</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['panjang']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Standard Tolerance By</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['standart_toleransi']); ?></td>
						<td class="text-left" width='15%'><u>Max</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['max_toleransi']; ?></td>
						<td class="text-left" width='15%'><u>Min</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['min_toleransi']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Product Application</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['aplikasi_product']); ?></td>
						<td class="text-left" width='15%'><u>Thickness Pipe (Design)</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['design']; ?></td>
						<td class="text-left" width='15%'><u>Thickness Pipe (EST)</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['est']; ?></td>
					</tr>
					-->
				</tbody>
			</table>
		</div>
	</div>
	<div class="box box-primary">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead>
					<tr class='bg-blue' align='center'>
						<td width='13%'>Jenis Material</td>
						<td width='27%'>Tipe Material</td>
						<td width='9%'>Persentase</td>
						<td width='16%'>Berat Material</td>
						<td width='9%'>Persentase</td>
						<td width='16%'>Berat Material</td>
					</tr>
				<thead>

					<tr>
						<td class="text-left" colspan='6'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
					</tr>
					<?php
				echo "<tbody id='restDetail2'>";
					$no5 = 0;
					if ($restDetail2) {
						foreach($restDetail2 AS $val => $valx){
						$no5++;
						$benang = "";
						if($valx['id_category'] == 'TYP-0005' OR $valx['id_category'] == 'TYP-0006'){
							$benang = "<br><br> Bn: ".floatval($valx['jumlah'])."<br><br> Bw: ".floatval($valx['bw']);
						}
						$get_spk = $this->db->get_where('production_real_detail', array('id_produksi'=>$id_produksi,'id_detail'=>$valx['id_detail'],'jenis_spk'=>1,'id_production_detail'=>$idProducktion))->row();
						if ($get_spk) {
							$percentage = $get_spk->percentage;
							$material_weight = $get_spk->material_weight;
						}else {
							$percentage = '';
							$material_weight = '';
						}
						if ($no5 < 7) {
							$read = 'readonly';
						}else {
							$read = '';
						}
						?>
						<tr id='tr_<?= $no5;?>'>
							<td class="text-left">
								<?= $valx['nm_category'];?>
								<input type='text' name='DetailUtama2[<?=$no5;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailUtama2[<?=$no5;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
                <input type='text' name='DetailUtama2[<?=$no5;?>][jenis_spk]' class='THide' value='2'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['percentage'], 0);?> %</td>
							<td class="text-right"><?= number_format($valx['material_weight'], 3);?> Kg</td>
							<?php if ($no5 != 1 && $no5 != 6): ?>
								<td class="text-right"><input type='text' name='DetailUtama2[<?=$no5;?>][percentage]' id='percentage_<?= $no5;?>' class='form-control input-sm no_2' autocomplete='off' value="<?=$percentage?>" <?=$read?>></td>
							<?php else:
								$col = 2; ?>
							<?php endif; ?>
							<?php if ($no5>6):
								$read = 'readonly';
							endif; ?>
							<td class="text-right" <?=isset($col)?'colspan="'.$col.'"':''?>><input type='text' name='DetailUtama2[<?=$no5;?>][material_weight]' id='material_weight_<?= $no5;?>' class='form-control input-sm numberOnly no_2' autocomplete='off' value="<?=$material_weight?>" <?=$read?>></td>
						</tr>
						<?php
					}
					}
				echo "</tbody>";


				?>
				</table>

		</div>
    <div class='box-footer'>
			<button type='button' id='UpdateRealMat2_Joint' class='btn btn-md btn-primary'>Update</button>
		</div>
	</div>




	<script>
		$(document).ready(function(){
			$(".no_2").on("keypress keyup blur",function () {
				var no_2 = parseInt('<?= $no5?>');
				var d1 = parseFloat('<?=$restHeader[0]['diameter']?>');
				var d2 = parseFloat('<?=$restHeader[0]['diameter2']?>');
				var pipe_thickness = parseFloat('<?=$restHeader[0]['pipe_thickness']?>');
				if (d2 == '') {
					var d = d1;
				}else if (d2>d1) {
					var d = d2;
				}else {
					var d = d1;
				}
				console.log($(this).val());
				for (var i = 1; i <= no_2; i++) {
					/*if (i == 2) {
						var matwe = ($('#percentage_'+i).val()/100)*$('#material_weight_'+(i-1)).val();
						$('#material_weight_'+i).val(matwe);
					}
					if (i == 3) {
						var matwe = d*3.14*(8/9)*Math.pow(pipe_thickness,2)/Math.pow(10,6)*($('#percentage_'+i).val()/100);
						$('#material_weight_'+i).val((matwe).toFixed(4));
					}
					if (i == 4) {
						var matwe = ($('#percentage_'+i).val()/100)*$('#material_weight_'+(i-1)).val();
						$('#material_weight_'+i).val((matwe).toFixed(4));
					}
					if (i == 5) {
						var matwe = ($('#percentage_'+i).val()/100)*$('#material_weight_'+(i-2)).val();
						$('#material_weight_'+i).val((matwe).toFixed(4));
					}*/
					if (i == 7) {
						var matwe = (parseFloat($('#material_weight_'+(i-1)).val())+parseFloat($('#material_weight_'+(i-3)).val())+parseFloat($('#material_weight_1').val()))*parseFloat($('#percentage_'+(i)).val())/100;
						$('#material_weight_'+i).val((matwe).toFixed(4));
					}
					if (i > 7) {
						var matwe = parseFloat($('#material_weight_6').val())*parseFloat($('#percentage_'+(i)).val())/100;
						$('#material_weight_'+i).val((matwe).toFixed(4));
					}
				}
			});
		});
		$(".THide").hide();

		$(".numberOnly").on("keypress keyup blur",function (event) {
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});


	</script>
