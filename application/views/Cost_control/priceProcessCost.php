<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'>IPP Number</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= $getHeader[0]->no_ipp;?></div>
				<!--
				<label class='label-control col-sm-2'>JOB Number</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; </div>
				-->
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Project Name</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= strtoupper($getHeader[0]->project);?></div>
				<!--
				<label class='label-control col-sm-2'>Delivery</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; </div>
				-->
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Customer Name</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= strtoupper($getHeader[0]->nm_customer);?></div>
				<!--
				<label class='label-control col-sm-2'>Delivery Point</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; </div>
				-->
			</div>
		</div>
		<!-- INPUTAN -->
		<input type='text' class='THide' name='id_bq' id='id_bq' value='BQ-<?= $getHeader[0]->no_ipp;?>'>
		<input type='text' class='THide' name='project' id='project' value='<?= $getHeader[0]->project;?>'>
		<input type='text' class='THide' name='customer' id='customer' value='<?= $getHeader[0]->nm_customer;?>'>
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left headX" colspan='10'><b>A. PRODUCT</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" width='10%'>Item Product</th>
						<th class="text-center" width='10%'>Diameter 1</th>
						<th class="text-center" width='10%'>Diamater 2</th>
						<th class="text-center" width='10%'>Liner</th>
						<th class="text-center" width='10%'>Pressure</th>
						<th class="text-center" width='13%'>Length /Unit</th>
						<th class="text-center" width='7%'>Qty</th>
						<th class="text-center" width='10%'>Unit Price</th>
						<th class="text-center" width='10%'>Profit (%)</th>
						<th class="text-center" width='10%'>Total Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$SUM = 0;
					$no = 0;
					foreach($getDetail AS $val => $valx){
						$no++;
						$SUM += (($valx['est_harga']) + ($valx['est_harga'] * (30/100))) * $valx['qty'];
						
						if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
							$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
						}
						elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
							$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".$valx['sudut'];
						}
						elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong'){
							$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
						}
						elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
							$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
						}
						else{$dim = "belum di set";} 
						echo "<tr>";
							echo "<td>".strtoupper($valx['parent_product'])."</td>";
							echo "<td align='right'>".$valx['diameter_1']."</td>";
							echo "<td align='right'>".$valx['diameter_2']."</td>";
							echo "<td align='center'>".substr($valx['series'], 6, 4)."</td>";
							echo "<td align='center'>".substr($valx['series'], 1, 4)."</td>";
							echo "<td align='left'>".$dim."</td>";
							echo "<td align='center'>".$valx['qty'];
								echo "<input type='text' id='qty_$no' style='text-align: right;' name='MatCost[$no][qty]' class='form-control input-sm THide' value='".$valx['qty']."'>";
								echo "<input type='text' id='id_milik_$no' style='text-align: right;' name='MatCost[$no][id_milik]' class='form-control input-sm THide' value='".$valx['id_milik']."'>";
								echo "<input type='text' id='id_category_$no' style='text-align: right;' name='MatCost[$no][category]' class='form-control input-sm THide' value='material'>";
							echo "</td>";
							echo "<td align='right'>".number_format($valx['est_harga'],2);
								echo "<input type='text' id='harga_$no' style='text-align: right;' name='MatCost[$no][harga]' class='form-control input-sm THide' value='".$valx['est_harga']."'>";
							echo "</td>";
							echo "<td align='center'>";
								echo "<input type='text' id='persen_$no' style='text-align: center;' name='MatCost[$no][persen]' maxlength='2' class='form-control input-sm numberOnly persenMat' value='30' data-nomor='$no'>";
							echo "</td>";
							echo "<td align='right'><div id='hargaTotL_$no'>".number_format((($valx['est_harga'] + ($valx['est_harga'] * (30/100)))) * $valx['qty'],2)."</div>";
								echo "<input type='text' id='hargaTot_$no' style='text-align: right;' name='MatCost[$no][harga_total]' class='form-control input-sm THide' value='".(($valx['est_harga'] + ($valx['est_harga'] * (30/100)))) * $valx['qty']."'>";
							
							echo "</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<th colspan='9'>TOTAL COST  OF PRODUCT</th>
						<th class="text-right"><div id='total_materialx'><?= number_format($SUM,2);?></div><input type='text' name='nox' id='nox' class='THide' value='<?= $no;?>'><input type='text' class='form-control input-sm THide' name='total_material' id='total_material' readonly value='<?= $SUM;?>'></th>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="text-left headX" colspan='10'><b>B. ENGINEERING COST</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center">No</th>
						<th class="text-center" colspan='5'>Item Product</th>
						<th class="text-center">Option</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Unit</th>
						<th class="text-center">Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$no1=0;
					foreach($getEngCost AS $val => $valx){
						$no1++;
						echo "<tr>";
							echo "<td align='center'>".$no1."</td>";
							echo "<td colspan='5'>".strtoupper($valx['name'])."";
								echo "<input type='text' name='EngCost[$no1][category]' class='form-control input-sm THide' value='engine'>";
								echo "<input type='text' name='EngCost[$no1][caregory_sub]' class='form-control input-sm THide' value='".strtoupper($valx['name'])."'>";
							echo "</td>";
							echo "<td align='left'>";
								if($no1 != '5'){
									echo "<select name='EngCost[$no1][option_type]' class='form-control input-sm optcl' data-nomor='$no1'>";
										foreach($getOpt AS $val2 => $valx2){
											echo "<option value='".$valx2['data1']."'>".$valx2['name']."</option>";
										}
									echo "</select>";
								}
								if($no1 == '5'){
									echo "<select name='EngCost[$no1][option_type]' class='form-control input-sm optcl' data-nomor='$no1'>";
										foreach($getOptP AS $val2 => $valx2){
											$Selx = ($valx2['data1'] == 'N')?'selected':'';
											echo "<option value='".$valx2['data1']."' ".$Selx.">".$valx2['name']."</option>";
										}
									echo "</select>";
								}
							echo "</td>";
							echo "<td align='right'>";
								echo "<input type='text' style='text-align:center;' name='EngCost[$no1][qty]' id='qtyX_$no1' data-nomor='$no1' class='form-control input-sm numberOnly engCostCls'>";
							echo "</td>";
							echo "<td align='center'>";
								echo "<div id='unit_".$no1."' class='unitEngCost'></div>";
								echo "<input type='text' id='unitV_".$no1."' style='text-align:center;' name='EngCost[$no1][unit]' class='form-control input-sm THide'>";
							echo "</td>";
							echo "<td align='right'>";
								echo "<input type='text' style='text-align:right;' id='EngCostPrice_$no1' data-nomor='$no1' name='EngCost[$no1][price]' class='form-control input-sm numberOnlyT EngCostPrice' value='0'>";
								echo "<input type='text' style='text-align:right; margin-top:5px;' id='EngCostPriceTot_$no1' name='EngCost[$no1][price_total]' class='form-control input-sm' readonly  value='0'>";
							echo "</td>";
						echo "</tr>";
					}
					
					?>
					<tr class='FootColor'>
						<th colspan='9'>TOTAL ENGINEERING COST</th>
						<th class="text-right"><input type='hidden' name='no1x' id='no1x' value='<?= $no1;?>'><input type='input' style='text-align:right;' class='form-control input-sm' name='total_eng' id='total_eng' readonly></th>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="text-left headX" colspan='10'><b>C. PACKING COST</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center">No</th>
						<th class="text-center" colspan='5'>Category</th>
						<th class="text-center">Type</th>
						<th class="text-center" colspan='2' width='20%'></th>
						<th class="text-center">Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$no2=0;
					foreach($getPackCost AS $val => $valx){
						$no2++;
						echo "<tr>";
							echo "<td align='center'>".$no2."</td>";
							echo "<td colspan='5'>".strtoupper($valx['name']);
								echo "<input type='text' name='PackCost[$no2][category]' class='form-control input-sm THide' value='packing'>";
								echo "<input type='text' name='PackCost[$no2][caregory_sub]' class='form-control input-sm THide' value='".strtoupper($valx['name'])."'>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<select name='PackCost[$no2][option_type]' class='form-control input-sm' data-nomor='$no2'>";
									foreach($getPackP AS $val2 => $valx2){
										echo "<option value='".$valx2['packing_name']."'>".$valx2['packing_name']."</option>";
									}
								echo "</select>";
							echo "</td>";
							echo "<td colspan='2' align='center'></td>";
							echo "<td align='center'>";
								echo "<input type='text' style='text-align:right;' id='PackingCost_$no2' data-nomor='$no2' name='PackCost[$no2][price_total]' class='form-control input-sm numberOnlyT PackingCost'>";
							echo "</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<th colspan='9'>TOTAL PACKING COST</th>
						<th class="text-right"><input type='hidden' name='no2x' id='no2x' value='<?= $no2;?>'><input type='input' style='text-align:right;' class='form-control input-sm' name='total_packing' id='total_packing' readonly></th>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="text-left headX" colspan='10'><b>D. TRUCKING EXPORT</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center">No</th>
						<th class="text-center"colspan='4'>Category</th>
						<th class="text-center">Type</th>
						<th class="text-center" colspan='2'>Fumigasi</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$no3=0;
					foreach($getTruck AS $val => $valx){
						$no3++;
						echo "<tr>";
							echo "<td align='center'>".$no3."</td>";
							echo "<td colspan='4'>".strtoupper($valx['shipping_name']);
								echo "<input type='text' name='ExportCost[$no3][category]' class='form-control input-sm THide' value='export'>";
								echo "<input type='text' name='ExportCost[$no3][caregory_sub]' class='form-control input-sm THide' value='".strtoupper($valx['shipping_name'])." ".strtoupper($valx['type'])."'>";
							echo "</td>";
							echo "<td align='center'>".strtoupper($valx['type'])."</td>";
							echo "<td align='left'>";
								echo "<select name='ExportCost[$no3][option_type]' class='form-control input-sm optc2' data-nomor='$no3' data-category='".$valx['shipping_name']."' data-type='".$valx['type']."'>";
									foreach($getOpt AS $val2 => $valx2){
										echo "<option value='".$valx2['data1']."'>".$valx2['name']."</option>";
									}
								echo "</select>";
							echo "</td>";
							echo "<td align='right'>";
								echo "<div id='unit3_".$no3."' class='unitExport'></div>";
								echo "<input type='text' id='unit3V_".$no3."' style='text-align:center;' name='ExportCost[$no3][price]' data-nomor='$no3' class='form-control numberOnlyT input-sm ExQtyUnit' value='0'>";
							echo "</td>";
							echo "<td align='right'><input type='text' style='text-align:center;' id='qtyShip_$no3' data-nomor='$no3' name='ExportCost[$no3][qty]' class='form-control input-sm numberOnly ExQty'></td>";
							echo "<td align='right'>";
								echo "<input type='text' style='text-align:right;' id='ExportPrice_$no3' name='ExportCost[$no3][price_total]' class='form-control input-sm' readonly  value='0'>";
							echo "</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<th colspan='9'>TOTAL TRUCKING EXPORT</th>
						<th class="text-right"><input type='hidden' name='no3x' id='no3x' value='<?= $no3;?>'><input type='input' style='text-align:right;' class='form-control input-sm' name='total_export' id='total_export' readonly></th>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="text-left headX" colspan='10'><b>E. TRUCKING LOKAL</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center">Item Product</th>
						<th class="text-center" colspan='2'>Area</th>
						<th class="text-center" colspan='2'>Tujuan</th>
						<th class="text-center" colspan='3'>Kendaraan</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$no4=0;
					foreach($getVia AS $val => $valx){ 
						$no4++;
						echo "<tr>";
							echo "<td>".strtoupper($valx['name']);
								echo "<input type='text' name='LokalCost[$no4][category]' class='form-control input-sm THide' value='lokal'>";
								echo "<input type='text' name='LokalCost[$no4][caregory_sub]' class='form-control input-sm THide' value='".strtoupper($valx['name'])."'>";
							echo "</td>";
							echo "<td align='left' colspan='2'>";
								echo "<select name='LokalCost[$no4][area]' id='AreaTruck_$no4' class='form-control input-sm AreaTruck' data-nomor='$no4'>";
									echo "<option value='0'>Select An Area</option>";
									if(strtolower($valx['name']) == 'via darat'){
										foreach($getArea AS $val2 => $valx2){
											echo "<option value='".$valx2['area']."'>".strtoupper($valx2['area'])."</option>";
										}
									}
									else{
										foreach($getAreaL AS $val2 => $valx2){
											echo "<option value='".$valx2['area']."'>".strtoupper($valx2['area'])."</option>";
										}
									}
								echo "</select>";
							echo "</td>";
							echo "<td align='left' colspan='2'>";
								echo "<select name='LokalCost[$no4][tujuan]' id='TujuanTruck_$no4' class='form-control input-sm TujuanTruck' data-nomor='$no4'>";
									echo "<option value='0'>List Empty</option>";
								echo "</select>";
							echo "</td>";
							echo "<td align='left' colspan='3'>";
								echo "<select name='LokalCost[$no4][kendaraan]' id='TruckTruck_$no4' class='form-control input-sm TruckTruck' data-nomor='$no4'>";
									echo "<option value='0'>List Empty</option>";
								echo "</select>";
							echo "</td>";
							echo "<td align='right'><input type='text' id='TrQty_$no4' style='text-align:center;' name='LokalCost[$no4][qty]' class='form-control input-sm numberOnly QtyTruck' data-nomor='$no4'></td>";
							echo "<td align='right'>";
								echo "<input type='text' style='text-align:right;' id='TrPrice_$no4' name='LokalCost[$no4][price]' class='form-control input-sm PriceTruck'  value='0' data-nomor='$no4'>";
								echo "<input type='text' style='text-align:right; margin-top:5px;' id='TrPriceSum_$no4' name='LokalCost[$no4][price_total]' class='form-control input-sm QtyTruck' readonly  value='0'>";
							echo "</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<th colspan='9'>TOTAL TRUCKING LOKAL</th>
						<th class="text-right"><input type='hidden' name='no4x' id='no4x' value='<?= $no4;?>'><input type='input' style='text-align:right;' class='form-control input-sm' name='total_lokal' id='total_lokal' readonly></th>
					</tr>
				</tbody>
				<tfoot>
					<tr style='background-color: #05b3a3;'>
						<th colspan='9'>TOTAL</th>
						<th class="text-right"><input type='input' style='text-align:right;' class='form-control input-sm' name='total_all' id='total_all' readonly></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class='box-footer' style='float:right;'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','id'=>'saved_data','value'=>'simpan','content'=>'Simpan'));
			?>
			<a href="<?php echo site_url('price/project') ?>" class="btn btn-md btn-danger">Back</a>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
   <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->	
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<style type="text/css">
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
	.headX{
		background-color: #05b3a3 !important;
		color : white;
	}
	.bg-bluexyz{
		background-color: #05b3a3 !important;
		color : white;
	}
	.FootColor{
		background-color: #91cbd4 !important;
	}
</style>

<script>
	$(document).ready(function(){
		$(".THide").hide();
		$(".unitEngCost").html('-');
		$(".unitExport").html();
		$(".engCostCls").attr('readonly', true);
		$(".ExQty").attr('readonly', true);
		$(".ExQtyUnit").attr('readonly', true);
		$(".EngCostPrice").attr('readonly', true);
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, "")); // && (event.which < 46 || event.which > 46 )
			if ((event.which < 48 || event.which > 57 )) {
				event.preventDefault();
			}
		});
		
		$(".numberOnlyT").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, "")); // 
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 ))  {
				event.preventDefault();
			}
		}); 
		
		$(document).on('keyup', '.engCostCls', function(){
			var nomor 	= $(this).data('nomor');
			var price 	= $('#EngCostPrice_'+nomor).val();
			var Total	= $(this).val() * price;
			if($(this).val() > 0){
				$('#unit_'+nomor).html('Set');
				$('#unitV_'+nomor).val('Set');
				if($('#EngCostPriceTot_'+nomor).val() != ''){
					$('#EngCostPriceTot_'+nomor).val(Total);
				}
				else{
					$('#EngCostPriceTot_'+nomor).val('');
					$('#EngCostPrice_'+nomor).val('');
				}
			}
			else{
				$('#unit_'+nomor).html('-');
				$('#unitV_'+nomor).val('-');
				$('#EngCostPriceTot_'+nomor).val('0');
				$('#EngCostPrice_'+nomor).val('0');

			}
			
			var no1x	= $('#no1x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no1x; a++){
				Totalx += getNum($('#EngCostPriceTot_'+a).val());
			}
			$('#total_eng').val(Totalx);
			SumAll();
		});
		
		$(document).on('keyup', '.ExQtyUnit', function(){
			var nomor 	= $(this).data('nomor');
			var price 	= $('#qtyShip_'+nomor).val();
			var Total	= $(this).val() * price;
			if($(this).val() > 0){
				if($('#ExportPrice_'+nomor).val() != ''){
					$('#ExportPrice_'+nomor).val(Total);
				}
				else{
					$('#qtyShip_'+nomor).val('');
					$('#ExportPrice_'+nomor).val('0');
				}
			}
			else{
				$('#qtyShip_'+nomor).val('');
				$('#ExportPrice_'+nomor).val('0');

			}
			var no3x	= $('#no3x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no3x; a++){
				Totalx += getNum($('#ExportPrice_'+a).val());
			}
			$('#total_export').val(Totalx);
			SumAll();
		});
		
		$(document).on('keyup', '.EngCostPrice', function(){
			var nomor = $(this).data('nomor');
			var qty = $('#qtyX_'+nomor).val();
			if(qty > 0){
				var Tot = $(this).val() * qty;
				$('#EngCostPriceTot_'+nomor).val(Tot);
			}
			else{
				$('#EngCostPriceTot_'+nomor).val('0');

			}
			var no1x	= $('#no1x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no1x; a++){
				Totalx += getNum($('#EngCostPriceTot_'+a).val());
			}
			$('#total_eng').val(Totalx);
			SumAll();
		});
		
		$(document).on('keyup', '.ExQty', function(){
			var nomor = $(this).data('nomor');
			var qty = $('#unit3V_'+nomor).val();
			if(qty > 0){
				var Tot = $(this).val() * qty;
				$('#ExportPrice_'+nomor).val(Tot);
			}
			else{
				$('#ExportPrice_'+nomor).val('0');

			}
			var no3x	= $('#no3x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no3x; a++){
				Totalx += getNum($('#ExportPrice_'+a).val());
			}
			$('#total_export').val(Totalx);
			SumAll();
		});
		
		
		
		$(document).on('change', '.optcl', function(){
			var nomor = $(this).data('nomor');
			if($(this).val() != 'N'){
				$('#qtyX_'+nomor).attr('readonly', false);
				$('#EngCostPrice_'+nomor).attr('readonly', false);
				$('#unit_'+nomor).html('-');
				$('#unitV_'+nomor).val('-');
				$('#qtyX_'+nomor).val('');
				$('#EngCostPrice_'+nomor).val('');
				$('#EngCostPriceTot_'+nomor).val('');
			}
			else{
				$('#qtyX_'+nomor).attr('readonly', true);
				$('#EngCostPrice_'+nomor).attr('readonly', true);
				$('#unit_'+nomor).html('-');
				$('#unitV_'+nomor).val('-');
				$('#qtyX_'+nomor).val('');
				$('#EngCostPrice_'+nomor).val('0');
				$('#EngCostPriceTot_'+nomor).val('0');
				
			}
		});
		
		$(document).on('change', '.optc2', function(){
			var nomor 	= $(this).data('nomor');
			var categy	= $(this).data('category');
			var type 	= $(this).data('type');
			var unit3_ 	= "#unit3_"+nomor;
			var unit3V_ = "#unit3V_"+nomor;
			if($(this).val() != 'N'){
				$('#qtyShip_'+nomor).attr('readonly', false);
				$('#unit3V_'+nomor).attr('readonly', false); 
				$('#unit3V_'+nomor).val('');
			}
			else{
				$('#qtyShip_'+nomor).attr('readonly', true);
				$('#unit3V_'+nomor).attr('readonly', true);
				$('#unit3_'+nomor).html();
				$('#unit3V_'+nomor).val(0);
				$('#qtyShip_'+nomor).val('');
				$('#ExportPrice_'+nomor).val(0);
			}
		});
		
		$(document).on('change','.AreaTruck', function(){
			var nomor 	= $(this).data('nomor');
			var data1	= $(this).val();
			var data2	= '#TujuanTruck_'+nomor;
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getTujuan',
				cache: false,
				type: "POST",
				data: "data1="+data1,
				dataType: "json",
				success: function(data){
					$(data2).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(document).on('change','.TujuanTruck', function(){
			var nomor 	= $(this).data('nomor');
			var data1	= $('#AreaTruck_'+nomor).val();
			var data2	= $(this).val();
			var data3	= '#TruckTruck_'+nomor;
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getTruck',
				cache: false,
				type: "POST",
				data: "data1="+data1+"&data2="+data2,
				dataType: "json",
				success: function(data){
					$(data3).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(document).on('change','.TruckTruck', function(){
			var nomor 	= $(this).data('nomor');
			var data1	= $('#AreaTruck_'+nomor).val();
			var data2	= $('#TujuanTruck_'+nomor).val();
			var data3	= $(this).val();
			var data4	= '#TrPrice_'+nomor;
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getPriceTruck',
				cache: false,
				type: "POST",
				data: "data1="+data1+"&data2="+data2+"&data3="+data3,
				dataType: "json",
				success: function(data){
					$(data4).val(getNum(data.price));
				}
			});
		});
		
		$(document).on('keyup', '.QtyTruck', function(){
			var nomor = $(this).data('nomor');
			var price = $('#TrPrice_'+nomor).val();
			if(price > 0){
				var Tot = $(this).val() * price;
				$('#TrPriceSum_'+nomor).val(getNum(Tot));
			}
			else{
				$('#TrPriceSum_'+nomor).val('0');

			}
			var no4x	= $('#no4x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no4x; a++){
				Totalx += getNum($('#TrPriceSum_'+a).val());
			}
			$('#total_lokal').val(Totalx);
			SumAll();
		});
		
		$(document).on('keyup', '.PriceTruck', function(){
			var nomor = $(this).data('nomor');
			var price = $('#TrQty_'+nomor).val();
			if(price > 0){
				var Tot = $(this).val() * price;
				$('#TrPriceSum_'+nomor).val(getNum(Tot));
			}
			else{
				$('#TrPriceSum_'+nomor).val('0');

			}
			var no4x	= $('#no4x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no4x; a++){
				Totalx += getNum($('#TrPriceSum_'+a).val());
			}
			$('#total_lokal').val(Totalx);
			SumAll();
		});
		
		$(document).on('keyup', '.PackingCost', function(){
			var no2x	= $('#no2x').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= no2x; a++){
				Totalx += getNum($('#PackingCost_'+a).val());
			}
			$('#total_packing').val(Totalx);
			SumAll();
		});
		
		$(document).on('keyup', '.persenMat', function(){
			var nomor 	= $(this).data('nomor');
			var qty 	= getNum($('#qty_'+nomor).val());
			var harga	= getNum($('#harga_'+nomor).val());
			var persen	= getNum($(this).val()) / 100;
			var TotalT	= (harga +(harga * persen)) * qty;
			
			$('#hargaTot_'+nomor).val(TotalT.toFixed(2));
			$('#hargaTotL_'+nomor).html(TotalT.toFixed(2));
			
			var nox	= $('#nox').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= nox; a++){
				Totalx += getNum($('#hargaTot_'+a).val());
			}
			$('#total_material').val(Totalx.toFixed(2));
			$('#total_materialx').html(Totalx.toFixed(2));
			SumAll();
			
		});
		
		
		$(document).on('click', '#saved_data', function(e){
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
						var baseurl=base_url + active_controller +'/save_cost_project';
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
										  timer	: 7000
										});
									window.location.href = base_url + active_controller+"/priceProcessCost/"+data.id_bq;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#saved_data').prop('disabled',false);
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
								$('#saved_data').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#saved_data').prop('disabled',false);
					return false;
				  }
			});
		});
		
	
	});
	
	
	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}
	
	function SumAll(){
		var data1 = getNum($('#total_material').val());
		var data2 = getNum($('#total_eng').val());
		var data3 = getNum($('#total_packing').val());
		var data4 = getNum($('#total_export').val());
		var data5 = getNum($('#total_lokal').val());
		var Total = data1 + data2 + data3 + data4 + data5;
		
		$('#total_all').val(Total);
		
	}
	
	
</script>
