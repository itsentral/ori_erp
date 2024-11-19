
	<input type='hidden' name='numberMax' id='numberMax' value='0'>
	<div class="box-body">
		<input type='hidden' name='seriesList' id='seriesList' value='<?= $dtImplode;?>'>
		<input type='hidden' name='pembeda' id='pembeda' value='<?= $this->uri->segment(4);?>'>
		<input type='hidden'name='id_bq' id='id_bq' value='<?= $id_bq;?>'>
		<input type='hidden'id='numRows' name='numRows' value='<?= $qBQdetRestVal;?>'>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='9%'>Series</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Iso Matric</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Unit Dev</th>
					<th class="text-center" style='vertical-align:middle;'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Dim 1</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Dim 2</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Length</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Thick</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Angle</th>
					<th class="text-center" style='vertical-align:middle;' width='9%'>Standard</th>
					<th class="text-center" style='vertical-align:middle;' width='9%'>Type</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='4%'>Del</th>
				</tr>
			</thead>
			<tbody id='detail_bodyTop'>
				<?php
					$no = 0;
					foreach($qBQdetRest AS $val => $valx){
						$no++;
						$readonly = '';
						if($valx['approve'] != 'N'){
							$readonly = 'disabled'; 
						}
						
						
						$selx1 = ($valx['type'] == '0')?'selected':'';
						$selx2 = ($valx['type'] == 'SR')?'selected':'';
						$selx3 = ($valx['type'] == 'LR')?'selected':'';
						
						$plusStyle = "";
						if($valx['sts_delivery'] == 'CHILD'){
							$plusStyle	= "style='background-color: khaki;'";
						}
						?>
						<tr id='tr_<?= $no;?>' <?= $plusStyle;?>>
							<!--<td align='center'><?= $no; ?></td>-->
							<td align='center'><?= $valx['series']; ?></td>
							<td align='center'><?= $valx['id_delivery']; ?></td>
							<td align='center'><?= $valx['sub_delivery']; ?>
								<input type='hidden' name='DetailBq[<?= $no;?>][id_bq_header]' value='<?= $valx['id_bq_header'];?>' <?=$readonly;?>>
								<input type='hidden' name='DetailBq[<?= $no;?>][sts_delivery]' value='<?= $valx['sts_delivery'];?>' <?=$readonly;?>>
								<input type='hidden' name='DetailBq[<?= $no;?>][id]' value='<?= $valx['id'];?>' <?=$readonly;?>>
								<input type='hidden' name='DetailBq[<?= $no;?>][sub_delivery]' value='<?= $valx['sub_delivery'];?>' <?=$readonly;?>>
								<input type='hidden' name='DetailBq[<?= $no;?>][id_delivery]' value='<?= $valx['id_delivery'];?>' <?=$readonly;?>>
								<input type='hidden' name='DetailBq[<?= $no;?>][series]' value='<?= $valx['series'];?>' <?=$readonly;?>>
								<input type='hidden' name='DetailBq[<?= $no;?>][id_category]' id='id_category_<?= $no;?>' value='<?= $valx['id_category'];?>' <?=$readonly;?>>
							</td>
		
							<td align='left'><?= strtoupper($valx['id_category']); ?></td> 
							<td><input type='text' name='DetailBq[<?= $no;?>][diameter_1]' id='diameter_1_<?= $no;?>' class='form-control numberOnly width_long' <?=$readonly;?> value='<?= floatval($valx['diameter_1']);?>' maxlength='4'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][diameter_2]' id='diameter_2_<?= $no;?>' class='form-control numberOnly width_long' <?=$readonly;?> value='<?= floatval($valx['diameter_2']);?>' maxlength='4'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][length]' id='length_<?= $no;?>' class='form-control numberOnly width_long' <?=$readonly;?> value='<?= floatval($valx['length']);?>' maxlength='7'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][thickness]' id='thickness_<?= $no;?>' class='form-control numberOnly width_short' <?=$readonly;?> value='<?= floatval($valx['thickness']);?>' maxlength='6'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][sudut]' id='sudut_<?= $no;?>' class='form-control numberOnly width_short' <?=$readonly;?> value='<?= floatval($valx['sudut']);?>' maxlength='4'></td>
							<td>
								<?php
								if($valx['approve'] != 'N'){
									echo "<input type='text' name='DetailBq[".$no."][id_standard]' id='id_standard_".$no."' class='form-control' ".$readonly." value='".$valx['id_standard']."'>";
								}
								else{
								?>
								<select name='DetailBq[<?= $no;?>][id_standard]' id='id_standard_<?= $no;?>' class=' form-control inline-block'>
									<option value='0'>Standard</option>
									<?php
									foreach($restSup AS $val2 => $valx2){
										$selx = ($valx['id_standard'] == $valx2['id_standard'])?'selected':'';
										echo "<option value='".$valx2['id_standard']."' ".$selx.">".strtoupper($valx2['nm_standard'])."</option>";
									}
									?>
								</select>
								<?php } ?>
							</td>
							<td>
								<?php
								if($valx['approve'] != 'N'){
									echo "<input type='text' name='DetailBq[".$no."][type]' id='type_".$no."' class='form-control' ".$readonly." value='".$valx['type']."'>";
								}
								else{
								?>
								<select name='DetailBq[<?= $no;?>][type]' id='type_<?= $no;?>' class=' form-control inline-block'>
									<option value='0' <?= $selx1;?>>Type</option>
									<option value='SR' <?= $selx2;?>>Short Rad</option>
									<option value='LR' <?= $selx3;?>>Long Rad</option>
								</select>
								<?php } ?>
							</td>
							<td><input type='text' name='DetailBq[<?= $no;?>][qty]' id='qty_<?= $no;?>' class='form-control numberOnly width_short'  <?=$readonly;?> value='<?= floatval($valx['qty']);?>' maxlength='4'></td>
							<td>
								<?php
								if($valx['approve'] == 'N'){
									echo "<button type='button' data-id_bq_header='".$valx['id_bq_header']."' data-id='".$valx['id']."' class='btn btn-danger btn-sm del' data-toggle='tooltip' data-placement='bottom'>Del</button>";
								}
								?>
							</td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="box box-success">
		<div class="box-header">
			<h3 class="box-title"><b>ADD BQ</b></h3>		
		</div>
		<button type="button" id='add' style='width:130px; margin-right:11px; margin-bottom:3px; float:right;' class="btn btn-success btn-sm">Add BQ</button>
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" style='vertical-align:middle;' width='9%'>Series</th>
						<th class="text-center" style='vertical-align:middle;' width='7%'>Iso Matric</th>
						<th class="text-center" style='vertical-align:middle;' width='8%'>Unit Dev</th>
						<th class="text-center" style='vertical-align:middle;' width='20%'>Component</th>
						<th class="text-center" style='vertical-align:middle;' width='5%'>Dim 1</th>
						<th class="text-center" style='vertical-align:middle;' width='5%'>Dim 2</th>
						<th class="text-center" style='vertical-align:middle;' width='7%'>Length</th>
						<th class="text-center" style='vertical-align:middle;' width='7%'>Thick</th>
						<th class="text-center" style='vertical-align:middle;' width='5%'>Angle</th>
						<th class="text-center" style='vertical-align:middle;' width='9%'>Standard</th>
						<th class="text-center" style='vertical-align:middle;' width='9%'>Type</th>
						<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
						<th class="text-center" style='vertical-align:middle;' width='4%'>Del</th>
					</tr>
				</thead>
				<tbody id='detail_body'>
				</tbody>
				<tbody id='detail_bodyKosong'>
					<tr>
						<td colspan='7'>Data masih kosong. Click Add BQ...</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class='box-footer'>
		<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'update_bq')).' ';
		?>
	</div>
<style>
	td{
		vertical-align:middle;
	}
	.width_long{
		width: 80px; !important;
	}
	.width_short{
		width: 60px; !important;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>

<script>
	
	$(document).ready(function(){
		swal.close();
		$('.chosen_select').chosen({width: '100%'});
	});
			
	$(".numberOnly").on("keypress keyup blur",function (event) {    
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
	var nomor	= 1;
	$('#add').click(function(e){
		e.preventDefault();
		AppendBaris(nomor);
		$('#head_table').show();
		// $('.chosen_select').chosen({width: '100%'});
		var nilaiAwal	= parseInt($("#numberMax").val());
		var nilaiAkhir	= nilaiAwal + 1;
		$("#numberMax").val(nilaiAkhir);
		$("#detail_bodyKosong").hide();
		$('#simpan-bro').show();
	});
	
	var numRows = $('#numRows').val();
	var a;
	for(a=1; a <= numRows; a++){
		var KS_id_category 	= $("#id_category_"+a).val();
		var KS_diameter_1 	= "#diameter_1_"+a;
		var KS_diameter_2 	= "#diameter_2_"+a;
		var KS_length 		= "#length_"+a;
		var KS_thickness 	= "#thickness_"+a;
		var KS_sudut 		= "#sudut_"+a;
		var KS_id_standard 	= "#id_standard_"+a;
		var KS_type 		= "#type_"+a;
		
		if(KS_id_category == 'pipe' || KS_id_category == 'pipe slongsong'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).hide();
			$(KS_length).show();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'end cap'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).hide();
			$(KS_length).hide();
			$(KS_sudut).hide();
			$(KS_thickness).show();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'blind flange'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).hide();
			$(KS_length).hide();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'flange mould' || KS_id_category == 'colar' || KS_id_category == 'colar slongsong' || KS_id_category == 'flange slongsong'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).hide();
			$(KS_length).hide();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'concentric reducer' || KS_id_category == 'eccentric reducer' || KS_id_category == 'reducer tee mould' || KS_id_category == 'reducer tee slongsong' || KS_id_category == 'joint puddle flange'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).show();
			$(KS_thickness).show();
			$(KS_length).hide();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'equal tee mould' || KS_id_category == 'equal tee slongsong'){
			$(KS_diameter_1).show();
			$(KS_length).hide();
			$(KS_diameter_2).hide();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'branch joint'){
			$(KS_diameter_1).show();
			$(KS_length).hide();
			$(KS_diameter_2).show();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'shop joint' || KS_id_category == 'field joint'){
			$(KS_diameter_1).show();
			$(KS_length).hide();
			$(KS_diameter_2).hide();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'puddle flange'){
			$(KS_diameter_1).show();
			$(KS_length).hide();
			$(KS_diameter_2).hide();
			$(KS_thickness).hide();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'loose flange'){
			$(KS_diameter_1).show();
			$(KS_length).hide();
			$(KS_diameter_2).hide();
			$(KS_thickness).hide();
			$(KS_sudut).hide();
			$(KS_id_standard).show();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'elbow mould' || KS_id_category == 'elbow mitter'){
			$(KS_diameter_1).show();
			$(KS_length).hide();
			$(KS_diameter_2).hide();
			$(KS_thickness).show();
			$(KS_sudut).show();
			$(KS_id_standard).hide();
			$(KS_type).show();
		}
		else{
			$(KS_diameter_1).show();
			$(KS_diameter_2).show();
			$(KS_length).show();
			$(KS_thickness).show();
			$(KS_sudut).show();
			$(KS_id_standard).show();
			$(KS_type).show();
		}
	}
	
	function AppendBaris(intd){
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='trX_"+nomor+"'>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][series]' id='seriesx_"+nomor+"' class='chosen_select form-control inline-block'></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_delivery]' id='id_deliveryx_"+nomor+"' class='chosen_select form-control inline-block'></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][sub_delivery]' id='sub_deliveryx_"+nomor+"' class='chosen_select form-control inline-block'></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_category]' id='id_categoryx_"+nomor+"' class='chosen_select form-control inline-block'></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_long' name='ListDetail["+nomor+"][diameter_1]' id='diameter_1x_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Dim1'>";
			Rows	+=		"<input type='hidden' class='form-control' name='ListDetail["+nomor+"][sts_delivery]' id='sts_deliveryx_"+nomor+"' required autocomplete='off' value='PARENT'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_long' name='ListDetail["+nomor+"][diameter_2]' id='diameter_2x_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Dim2'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_long' name='ListDetail["+nomor+"][length]' id='lengthx_"+nomor+"' required autocomplete='off' maxlength='8' placeholder='Length'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_short' name='ListDetail["+nomor+"][thickness]' id='thicknessx_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Thick'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_short' name='ListDetail["+nomor+"][sudut]' id='sudutx_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Angle'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_standard]' id='id_standardx_"+nomor+"' class=' form-control inline-block'></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][type]' id='typex_"+nomor+"' class=' form-control inline-block'>";
			Rows	+=			"<option value='0'>Type</option>";
			Rows	+=			"<option value='SR'>Short Rad</option>";
			Rows	+=			"<option value='LR'>Long Rad</option>";
			Rows	+=		"</select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_short' name='ListDetail["+nomor+"][qty]' id='qtyx_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Qty'>";
			Rows	+= 	"</td>";
			Rows 	+= 	"<td align=\"left\">";
			Rows 	+=		"<div style='text-align: center;'><button type='button' id='del_acc' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRowX("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows 	+= 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body').append(Rows);
		$('.chosen_select').chosen();
		
		var id_standard = "#id_standardx_"+nomor;
		var id_category = "#id_categoryx_"+nomor;
		var series = "#seriesx_"+nomor;
		var id_delivery = "#id_deliveryx_"+nomor;
		var sub_delivery = "#sub_deliveryx_"+nomor;
		// console.log(accID);
		
		$(document).on('change', series, function(){
			$.ajax({
				// url: base_url +'index.php/'+active_controller+'/getDeliveryM',
				url: base_url +'index.php/'+active_controller+'/getDeliveryMX',
				cache: false,
				type: "POST",
				data: "series="+$(this).val()+"&id_bq="+$("#id_bq").val(),
				dataType: "json",
				success: function(data){
					$(id_delivery).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(document).on('change', id_delivery, function(){
			$.ajax({
				// url: base_url +'index.php/'+active_controller+'/getSubDeliveryM',
				url: base_url +'index.php/'+active_controller+'/getSubDeliveryMX',
				cache: false,
				type: "POST",
				data: "series="+$(series).val()+"&id_delivery="+$(id_delivery).val()+"&id_bq="+$("#id_bq").val(),
				dataType: "json",
				success: function(data){
					$(sub_delivery).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(id_standard).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProduct',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(id_category).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			// url: base_url +'index.php/'+active_controller+'/getSeriesM',
			url: base_url +'index.php/'+active_controller+'/getSeries',
			cache: false,
			type: "POST",
			data: "series="+$("#seriesList").val(),
			dataType: "json",
			success: function(data){
				$(series).html(data.option).trigger("chosen:updated");
			}
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {  
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		
		var KS_id_category 	= $("#id_categoryx_"+nomor).val();
		var KS_diameter_1 	= "#diameter_1x_"+nomor;
		var KS_diameter_2 	= "#diameter_2x_"+nomor;
		var KS_length 		= "#lengthx_"+nomor;
		var KS_thickness 	= "#thicknessx_"+nomor;
		var KS_sudut 		= "#sudutx_"+nomor;
		var KS_id_standard 	= "#id_standardx_"+nomor;
		var KS_type 		= "#typex_"+nomor;
			
		$(document).on('change', id_category, function(){
			var Komponent = $(id_category).val();
			if(Komponent == 'pipe' || Komponent == 'pipe slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'end cap'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_thickness).show();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'blind flange'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'flange mould' || Komponent == 'colar' || Komponent == 'colar slongsong' || Komponent == 'flange slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'concentric reducer' || Komponent == 'eccentric reducer' || Komponent == 'reducer tee mould' || Komponent == 'reducer tee slongsong' || Komponent == 'joint puddle flange'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'equal tee mould' || Komponent == 'equal tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'branch joint'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'shop joint' || Komponent == 'field joint'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'puddle flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if(Komponent == 'loose flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).show();
				$(KS_type).hide();
			}
			else if(Komponent == 'elbow mould' || Komponent == 'elbow mitter'){ 
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
			}
			else{
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).show();
				$(KS_type).show();
			}
		});
		
		nomor++;
	}
	
	function delRowX(row){
		$('#trX_'+row).remove();
	}
	
</script>