<?php
$id_bq = $this->uri->segment(3);
$tanda = $this->uri->segment(4);

$qSupplier 	= "	SELECT a.* FROM cost_engine a WHERE a.id = '".$tanda."'";
$row	= $this->db->query($qSupplier)->result_array();

$qMatr 		= "	SELECT
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
					b.standart_code,
					( a.est_harga * a.qty ) AS est_harga2,
					( a.sum_mat * a.qty ) AS sum_mat2,
					b.pressure,
					b.liner,
					(
						(
						SELECT
							d.total 
						FROM
							cost_process_auto d 
						WHERE
							d.product_parent = b.parent_product 
							AND d.diameter = b.diameter 
							AND ( d.diameter2 = b.diameter2 ) 
							AND d.pn = b.pressure 
							AND d.liner = b.liner 
							) + (
						IF
								(
									( h.type = 'pipe' ),
									( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
								IF
									(
										( h.type = 'fitting' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												0 
											) 
									) 
								) * a.sum_mat 
						) 
					) * a.qty AS cost_process,
					(
						(
							(
							SELECT
								d.total 
							FROM
								cost_process_auto d 
							WHERE
								d.product_parent = b.parent_product 
								AND d.diameter = b.diameter 
								AND d.diameter2 = b.diameter2 
								AND d.pn = b.pressure 
								AND d.liner = b.liner 
								) + (
							IF
								(
									( h.type = 'pipe' ),
									( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
								IF
									(
										( h.type = 'fitting' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												0 
											) 
									) 
								) * a.sum_mat 
							) 
						) + a.est_harga 
					) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) * a.qty AS foh_consumable,
					(
						(
							(
							SELECT
								d.total 
							FROM
								cost_process_auto d 
							WHERE
								d.product_parent = b.parent_product 
								AND d.diameter = b.diameter 
								AND d.diameter2 = b.diameter2 
								AND d.pn = b.pressure 
								AND d.liner = b.liner 
								) + (
							IF
								(
									( h.type = 'pipe' ),
									( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
								IF
									(
										( h.type = 'fitting' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												0 
											) 
									) 
								) * a.sum_mat 
							) 
						) + a.est_harga 
					) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) * a.qty AS foh_depresiasi,
					(
						(
							(
							SELECT
								d.total 
							FROM
								cost_process_auto d 
							WHERE
								d.product_parent = b.parent_product 
								AND d.diameter = b.diameter 
								AND d.diameter2 = b.diameter2 
								AND d.pn = b.pressure 
								AND d.liner = b.liner 
								) + (
							IF
								(
									( h.type = 'pipe' ),
									( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
								IF
									(
										( h.type = 'fitting' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												0 
											) 
									) 
								) * a.sum_mat 
							) 
						) + a.est_harga 
					) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
					(
						(
							(
							SELECT
								d.total 
							FROM
								cost_process_auto d 
							WHERE
								d.product_parent = b.parent_product 
								AND d.diameter = b.diameter 
								AND d.diameter2 = b.diameter2 
								AND d.pn = b.pressure 
								AND d.liner = b.liner 
								) + (
							IF
								(
									( h.type = 'pipe' ),
									( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
								IF
									(
										( h.type = 'fitting' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												0 
											) 
									) 
								) * a.sum_mat 
							) 
						) + a.est_harga 
					) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) * a.qty AS biaya_non_produksi,
					(
						(
							(
							SELECT
								d.total 
							FROM
								cost_process_auto d 
							WHERE
								d.product_parent = b.parent_product 
								AND d.diameter = b.diameter 
								AND d.diameter2 = b.diameter2 
								AND d.pn = b.pressure 
								AND d.liner = b.liner 
								) + (
							IF
								(
									( h.type = 'pipe' ),
									( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
								IF
									(
										( h.type = 'fitting' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												0 
											) 
									) 
								) * a.sum_mat 
							) 
						) + a.est_harga 
					) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) * a.qty AS biaya_rutin_bulanan 
				FROM
					estimasi_cost_and_mat a
					INNER JOIN bq_product b ON a.id_milik = b.id 
					LEFT JOIN product_parent h ON h.product_parent = b.parent_product
				WHERE
					b.parent_product <> 'pipe slongsong' AND
					a.id_bq = '".$id_bq."' ORDER BY a.id_milik ASC";
$getDetail		= $this->db->query($qMatr)->result_array();
?>

<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<tbody>
			<tr class='bg-bluexyz'>
				<th style='background:none;' width='6%' class='no-sort'><font size='2'><B><center><input type='checkbox' name='chk_all' id='chk_all'></center></B></font></th>
				<th class="text-center" width='14%'>Item Product</th>
				<th class="text-center" width='8%'>Dim 1</th>
				<th class="text-center" width='8%'>Dim 2</th>
				<th class="text-center" width='8%'>Liner</th>
				<th class="text-center" width='8%'>Pressure</th>
				<th class="text-center">Length /Unit</th>
				<th class="text-center" width='8%'>Unit Price</th>
				<th class="text-center" width='8%'>Qty Test</th>
				<th class="text-center" width='10%'>Total Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$SumTot2x = 0;
			foreach($getDetail AS $val => $valx){
				$getProfit = $this->db->query("SELECT profit FROM cost_profit WHERE diameter='".str_replace('.','',$valx['diameter_1'])."' AND diameter2='".str_replace('.','',$valx['diameter_2'])."' AND product_parent='".$valx['id_category']."' ")->result_array();
				$est_harga = (($valx['est_harga2']+$valx['cost_process']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$profit = (!empty($getProfit[0]['profit']))?floatval($getProfit[0]['profit']):0;
				$helpProfit = 0;
				if($profit <> 0){
					$helpProfit = $est_harga *($profit/100);
				}
				$HrgTot   = (($est_harga) + ($helpProfit)) * $valx['qty'];
				$SumTot2x += $HrgTot;
			}
			$SUM = 0;
			$no = 0;
			$SumEstHarga = 0;
			$SumTot2 = 0;
			foreach($getDetail AS $val => $valx){
				$no++;
				$getProfit = $this->db->query("SELECT profit FROM cost_profit WHERE diameter='".str_replace('.','',$valx['diameter_1'])."' AND diameter2='".str_replace('.','',$valx['diameter_2'])."' AND product_parent='".$valx['id_category']."' ")->result_array();
				// echo "SELECT profit FROM cost_profit WHERE diameter='".str_replace('.','',$valx['diameter_1'])."' AND diameter2='".str_replace('.','',$valx['diameter_2'])."' AND product_parent='".$valx['id_category']."' <br>";
				$est_harga = (($valx['est_harga2']+$valx['cost_process']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				
				$profit = (!empty($getProfit[0]['profit']))?floatval($getProfit[0]['profit']):0;
				
				$helpProfit = 0;
				if($profit <> 0){
					$helpProfit = $est_harga *($profit/100);
				}
				
				$HrgTot   = (($est_harga) + ($helpProfit));
				$SumTot2 += $HrgTot;
				
				if($SumTot2x > 1000000){
					$allow = 11.5;
				}
				if($SumTot2x >= 500000 AND $SumTot2x <= 1000000){
					$allow = 12.5;
				}
				if($SumTot2x < 500000){
					$allow = 15;
				}
				
				$HrgTot2  = (($HrgTot) + ($HrgTot * ($allow/100)));
				
				if($valx['id_category'] == 'pipe'){
					$estharga = ($HrgTot2 / floatval($valx['length'])) * 1000;
				}
				else{
					$estharga = $HrgTot2;
				}
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".floatval($valx['sudut']);
				}
				elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
				}
				elseif($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' ){
					$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang']);
				}
				elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
					$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
				}
				else{$dim = "belum di set";} 
				echo "<tr>";
					echo "<td align='right' style='vertical-align:middle;'><center><input type='checkbox' name='check[$no]' class='chk_personal' data-nomor='".$no."' value='".$valx['id_milik']."'></center></td>";
					echo "<td style='vertical-align:middle;'>".strtoupper($valx['id_category'])."</td>";
					echo "<td align='right' style='vertical-align:middle;'>".$valx['diameter_1']."</td>";
					echo "<td align='right' style='vertical-align:middle;'>".$valx['diameter_2']."</td>";
					echo "<td align='center' style='vertical-align:middle;'>".$valx['liner']."</td>";
					echo "<td align='center' style='vertical-align:middle;'>".$valx['pressure']."</td>";
					echo "<td align='left' style='vertical-align:middle;'>".$dim."</td>";
					echo "<td align='right' style='vertical-align:middle;'>".number_format($estharga,2);
						echo "<input type='text' id='hargamodalest_$no' style='text-align: right;' class='form-control input-sm THide' value='".$estharga."'>";
					echo "</td>";
					echo "<td align='center'>";
						echo "<input type='text' id='qtymodal_$no' style='text-align: center;' class='form-control input-sm numberOnly qty_number' data-nomor='".$no."' placeholder='Qty'>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<input type='text' id='hargamodal_$no' style='text-align: right;' class='form-control input-sm harga_number' readonly value='0'>";
					echo "</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table><br><br>
	<?php
		echo form_input(array('type'=>'hidden','id'=>'maxModal','name'=>'maxModal','readonly'=>'readonly'),$no);
	?>	
	<div class='form-group row'>
		<label class='label-control col-sm-5'><b>SUM TEST<span class='text-red'>*</span></b></label>
		<div class='col-sm-7'>
			<?php
				echo form_input(array('id'=>'value1x','name'=>'value1x','class'=>'form-control input-md','readonly'=>'readonly'));
			?>	 			
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-5'><b>ACCESSORIS<span class='text-red'>*</span></b></label>
		<div class='col-sm-7'>
			<?php
				echo form_input(array('id'=>'value1','name'=>'value1','class'=>'form-control input-md numberOnlyT','autocomplete'=>'off'), floatval($row[0]['value1']));
				echo form_input(array('type'=>'hidden','id'=>'idbq','name'=>'idbq','class'=>'form-control input-md numberOnlyT','autocomplete'=>'off'), $this->uri->segment(3));
			?>	 			
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-5'><b>MAN POWER<span class='text-red'>*</span></b></label>
		<div class='col-sm-7'>
			<?php
				echo form_input(array('id'=>'value2','name'=>'value2','class'=>'form-control input-md numberOnlyT','autocomplete'=>'off'), floatval($row[0]['value2']));
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-5'><b>TOTAL BIAYA</b></label>
		<div class='col-sm-7'>
		<?php
			echo form_input(array('id'=>'sum1','name'=>'sum1','class'=>'form-control input-md','autocomplete'=>'off','maxlength'=>'10', 'readonly'=>'readonly'));
		?>
		</div>
	</div>
	<div class='form-group row'>
		<div class='col-sm-12'>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','id'=>'btn-set1','value'=>'Update','content'=>'Update'));
			?>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		swal.close(); 
		$(".THide").hide();
		$(".qty_number").hide();
		$(".harga_number").hide();
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var sum	= value1 + value2;
		$('#sum1').val(sum.toFixed(2));
	});
	$("#chk_all").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
		if ($(this).prop('checked')) {
			$(".qty_number").show();
			$(".harga_number").show();
		}
		else{
			$(".qty_number").hide();
			$(".harga_number").hide();
		}
	});
	
	$(".chk_personal").click(function(){
		var nomor = $(this).data('nomor');
		if ($(this).prop('checked')) {
			$("#qtymodal_"+nomor).show();
			$("#hargamodal_"+nomor).show();
		}
		else{
			$("#qtymodal_"+nomor).hide();
			$("#hargamodal_"+nomor).hide();
		}
	});
	
	$(".qty_number").on("keypress keyup blur",function (event) {    
		var nomor = $(this).data('nomor');
		var hargaest = parseFloat($("#hargamodalest_"+nomor).val());
		var qty = parseFloat($(this).val());
		var total = hargaest * qty;
		$("#hargamodal_"+nomor).val(total.toFixed(2));
		
		var nox2	= $('#maxModal').val();
		var Totalx2	= 0;
		var a2;
		for(a2=1; a2 <= nox2; a2++){
			Totalx2 += getNum($('#hargamodal_'+a2).val());
		}
		$('#value1x').val(Totalx2.toFixed(2));
		
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var sum	= value1 + value2 + Totalx2;
		$('#sum1').val(sum.toFixed(2));
	});
	
	$(".numberOnlyT").on("keypress keyup blur",function (event) {    
		// $(this).val($(this).val().replace(/[^\d].+/, "")); // 
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 ))  {
			event.preventDefault();
		}
	});
	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 )) {
			event.preventDefault();
		}
	});
	$(document).on('keyup','#value1', function(){
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var value3 = parseFloat($("#value1x").val());
		var sum	= value1 + value2 + value3;
		$('#sum1').val(sum.toFixed(2));
	});
	$(document).on('keyup','#value2', function(){
		var value1 = parseFloat($("#value1").val());
		var value2 = parseFloat($("#value2").val());
		var value3 = parseFloat($("#value1x").val());
		var sum	= value1 + value2 + value3;
		$('#sum1').val(sum.toFixed(2));
	});
	
	$(document).on('click', '#btn-set1', function(e){
		e.preventDefault();
		$(this).prop('disabled',true);

		swal({
			  title: "Are you sure?",
			  text: "You will not be able to process again this data!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData 	=new FormData($('#form_proses_bro')[0]);
					var baseurl=base_url + active_controller +'/update_set_b';
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
									  timer	: 3000
									});
								window.location.href = base_url + active_controller +'/priceProcessCost/'+data.id_bq;
							}
							else if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#btn-set1').prop('disabled',false);
						},
						error: function() {

							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',
							  type				: "warning",
							  timer				: 7000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#btn-set1').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#saved_data').prop('disabled',false);
				return false;
			  }
		});
	});
</script>