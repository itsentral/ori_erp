<?php
	$id_product = $this->uri->segment(3);
	$id_produksi = $this->uri->segment(4);
	$idProducktion = $this->uri->segment(5);
	$id_milik = $this->uri->segment(6);
	// echo "Milik=>".$id_milik;

	$qProduksi		= "SELECT * FROM production_header WHERE id_produksi='".$id_produksi."' ";
	$restProduksi	= $this->db->query($qProduksi)->result_array();

	$qHeader			= "SELECT * FROM bq_component_header WHERE id_product='".$id_product."' AND id_milik ='".$id_milik."'";
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
		$qDetail2			= "SELECT * FROM bq_component_detail WHERE id_product='".$id_product."' AND id_milik='".$id_milik."' AND detail_name='RESIN AND ADD' AND id_material <> 'MTL-1903000' LIMIT 6";
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
						<td width='9%'>Qty</td>
						<td width='16%'>Lot/Batch Num</td>
						<td width='12%'>Actual Type</td>
						<td width='8%'>Terpakai</td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'><b><?= $restDetail1[0]['detail_name']; ?></b></td>
					</tr>
				<thead>
				<tbody id='restDetail1'>
					<?php
					$no1 = 0;
					if ($restDetail1) {
						foreach($restDetail1 AS $val => $valx){
						$no1++;
						?>
						<tr id='tr_<?= $no1;?>'>
							<td class="text-left">
								<?= $valx['nm_category'];?>
								<input type='text' name='DetailUtama[<?=$no1;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailUtama[<?=$no1;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['material_weight'], 3);?> Kg</td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][batch_number]' id='batch_number_<?= $no1;?>' class='form-control input-sm' autocomplete='off' value="0"></td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][actual_type]' id='actual_type_<?= $no1;?>' class='form-control input-sm' autocomplete='off' value="0"></td>
							<td class="text-right"><input type='text' name='DetailUtama[<?=$no1;?>][material_terpakai]' id='material_terpakai_<?= $no1;?>' class='form-control input-sm numberOnly' autocomplete='off' value="0"></td>
						</tr>
						<?php
					}
					}
				echo "</tbody>";
				?>
				<thead>
					<tr class='bg-blue' align='center'>
						<td width='13%'>Jenis Material</td>
						<td width='27%'>Tipe Material</td>
						<td width='9%'>Persentase</td>
						<td width='16%'>Berat Material</td>
						<td width='12%'>Persentase</td>
						<td width='8%'>Berat Material</td>
					</tr>
					<tr>
						<td class="text-left" colspan='6'><b><?= $restDetail2[0]['detail_name']; ?></b></td>
					</tr>
				<thead>
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
						?>
						<tr id='tr_<?= $no5;?>'>
							<td class="text-left">
								<?= $valx['nm_category'];?>
								<input type='text' name='DetailUtama2[<?=$no5;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
								<input type='text' name='DetailUtama2[<?=$no5;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								<input type='text' name='DetailUtama2[<?=$no5;?>][jenis_spk]' class='THide' value='1'>
							</td>
							<td class="text-left"><?= $valx['nm_material'];?></td>
							<td class="text-right"><?= number_format($valx['percentage'], 1);?> %</td>
							<td class="text-right"><?= number_format($valx['material_weight'], 1);?></td>
							<?php if ($no5 != 1 && $no5 != 6): ?>
								<td class="text-right"><input type='text' name='DetailUtama2[<?=$no5;?>][percentage]' id='percentage_<?= $no5;?>' class='form-control input-sm no_2' autocomplete='off' value="0" ></td>
							<?php else:
								$col = 2; ?>
							<?php endif; ?>

							<td class="text-right" <?=isset($col)?'colspan="'.$col.'"':''?>><input type='text' name='DetailUtama2[<?=$no5;?>][material_weight]' id='material_weight_<?= $no5;?>' class='form-control input-sm numberOnly no_2' autocomplete='off' value="0" <?=($no5 != 1 && $no5 !=6)?'readonly':''?>></td>
						</tr>
						<?php
					}
					}
				echo "</tbody>";


				?>
				</table>
				<table id="my-grid2" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead>
						<tr class='bg-blue' align='center'>
							<td >Lapisan ke-</td>
							<td >Glass Configuration</td>
							<td >Width</td>
							<td >Stage</td>
							<td >Actual Glass Configuration</td>
							<td >Actual Widyth</td>
						</tr>
						<tr>
							<td class="text-left" colspan='8'><b><?= $restDetail3[0]['detail_name']; ?></b></td>
						</tr>
					<thead>
					<tbody id='restDetail3'>
						<?php
				if($NumDetail3 > 0){

					//echo "<tbody id='restDetail3'>";
						$no9 = 0;
						foreach($restDetail3 AS $val => $valx){
							$no9++;
							if (!isset($stage)) {
								$stage = '';
							}
							?>
							<tr id='tr_<?= $no9;?>'>
								<td class="text-left">
									<?= $valx['lapisan'];?>
									<input type='text' name='DetailUtama3[<?=$no9;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailUtama3[<?=$no9;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								</td>
								<td class="text-left"><?= $valx['glass'];?></td>
								<td class="text-right"><?= $valx['width'];?></td>
								<?php if ($stage != $valx['stage']):
									$stage = $valx['stage'];
									$n_stage = $this->db->get_where('bq_component_lamination',array('detail_name'=>'Inside Lamination','stage'=>$stage,'id_product'=>$id_product,'id_milik'=>$id_milik))->num_rows();
								?>
									<td style="text-align:center;vertical-align:middle" rowspan="<?=$n_stage?>"><?= $stage;?></td>
								<?php endif; ?>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][actual_glass]' id='actual_glass_<?= $no9;?>' class='form-control input-sm' autocomplete='off' value="0"></td>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no9;?>][actual_width]' id='actual_width_<?= $no9;?>' class='form-control input-sm numberOnly' autocomplete='off' value="0"></td>


							</tr>
							<?php
						}
					echo "</tbody>";

				}
				if($restDetail4){
						?>
						<tr>
							<td class="text-left" colspan='8'><b><?= $restDetail4[0]['detail_name']; ?></b></td>
						</tr>
						<?php
					echo "<tbody id='restDetail4'>";
						$no10 = 0;
						$stage = '';
						foreach($restDetail4 AS $val => $valx){
							$no10++;

							?>
							<tr id='tr_<?= $no10;?>'>
								<td class="text-left">
									<?= $valx['lapisan'];?>
									<input type='text' name='DetailUtama3[<?=$no10;?>][id_detail]' class='THide' value='<?= $valx['id_detail'];?>'>
									<input type='text' name='DetailUtama3[<?=$no10;?>][id_product]' class='THide' value='<?= $valx['id_product'];?>'>
								</td>
								<td class="text-left"><?= $valx['glass'];?></td>
								<td class="text-right"><?= $valx['width'];?></td>
								<?php if ($stage != $valx['stage']):
									$stage = $valx['stage'];
									$n_stage = $this->db->get_where('bq_component_lamination',array('detail_name'=>'Outside Lamination','stage'=>$stage,'id_product'=>$id_product,'id_milik'=>$id_milik))->num_rows();
								?>
									<td style="text-align:center;vertical-align:middle" rowspan="<?=$n_stage?>"><?= $stage;?></td>
								<?php endif; ?>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no10;?>][actual_glass]' id='actual_glass_<?= $no10;?>' class='form-control input-sm' autocomplete='off' value="0"></td>
								<td class="text-right"><input type='text' name='DetailUtama3[<?=$no10;?>][actual_width]' id='actual_width_<?= $no10;?>' class='form-control input-sm numberOnly' autocomplete='off' value="0"></td>


							</tr>
							<?php
						}
					echo "</tbody>";

				}
					?>
			</table>
		</div>
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>

					<tr>
						<td width='25%'><b>Thickness Est</b></td>
						<td width='25%'><b><?= $restHeader[0]['joint_thickness'];?></b></td>
						<td width='25%'><b>Thickness Real</b></td>
						<td width='25%'><input type='text' name='est_real' id='est_real' class='form-control input-sm numberOnly' autocomplete='off' value="0"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='box-footer' style="">
			<button type='button' id='updateRealMat1_Joint' class='btn btn-md btn-primary'>Update</button>
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
					if (i == 2) {
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
