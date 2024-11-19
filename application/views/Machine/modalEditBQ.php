<?php
$id_bq 			= $this->uri->segment(3);

$qBQHeader		= "SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$restBQHeader	= $this->db->query($qBQHeader)->result_array();

$qBQdetHeader 	= "SELECT * FROM bq_detail_header WHERE id_bq = '".$id_bq."' ORDER BY id ASC";
$qBQdetRest		= $this->db->query($qBQdetHeader)->result_array();
$qBQdetRestVal	= $this->db->query($qBQdetHeader)->num_rows();

$sqlSup			= "SELECT * FROM list_standard ORDER BY urut ASC";
$restSup		= $this->db->query($sqlSup)->result_array();

$sqlProduct		= "SELECT * FROM product_parent ORDER BY product_parent ASC";
$restProduct	= $this->db->query($sqlProduct)->result_array();

$ListSeries		= $this->db->query("SELECT * FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series")->result_array();
	$dtListArray = array();
	foreach($ListSeries AS $val => $valx){
		$dtListArray[$val] = $valx['series'];
	}
	$dtImplode	= implode(",", $dtListArray);

// echo $qBQdetHeader;
// echo "<pre>";
// print_r($restProduct);
// echo "</pre>";
?>
	<input type='hidden' name='numberMax' id='numberMax' value='0'>
	<div class="box-body">
		<input type='hidden' name='seriesList' id='seriesList' value='<?= $dtImplode;?>'>
		<input type='hidden' name='pembeda' id='pembeda' value='<?= $this->uri->segment(4);?>'>
		<input type='hidden'name='id_bq' id='id_bq' value='<?= $id_bq;?>'>
		<input type='hidden'id='numRows' name='numRows' value='<?= $qBQdetRestVal;?>'>

		<button type='button' class='btn btn-sm btn-danger' id='del_multiple' style='float:right;'>Delete Multiple</button>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='3%'>No</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Series</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Iso Matric</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Unit Dev</th>
					<th class="text-center" style='vertical-align:middle;' width='18%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Dim 1</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Dim 2</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Length</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Thick</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Angle</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Standard</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Type</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='4%'>Del</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'><center><input type='checkbox' name='chk_all' id='chk_all'></center></th>
				</tr>
			</thead>
			<tbody id='detail_bodyTop'>
				<?php
					$no = 0;
					foreach($qBQdetRest AS $val => $valx){
						$no++;
						
						$selx1 = ($valx['type'] == '0')?'selected':'';
						$selx2 = ($valx['type'] == 'SR')?'selected':'';
						$selx3 = ($valx['type'] == 'LR')?'selected':'';
						
						$plusStyle = "";
						if($valx['sts_delivery'] == 'CHILD'){
							$plusStyle	= "style='background-color: khaki;'";
						}
						?>
						<tr id='tr_<?= $no;?>' <?= $plusStyle;?>>
							<td align='center'><?= $no; ?></td>
							<td align='center'><?= $valx['series']; ?></td>
							<td align='center'><?= $valx['id_delivery']; ?></td>
							<td align='center'><?= $valx['sub_delivery']; ?>
								<input type='hidden' name='DetailBq[<?= $no;?>][sts_delivery]' value='<?= $valx['sts_delivery'];?>'>
								<input type='hidden' name='DetailBq[<?= $no;?>][id]' value='<?= $valx['id'];?>'>
								<input type='hidden' name='DetailBq[<?= $no;?>][sub_delivery]' value='<?= $valx['sub_delivery'];?>'>
								<input type='hidden' name='DetailBq[<?= $no;?>][id_delivery]' value='<?= $valx['id_delivery'];?>'>
								<input type='hidden' name='DetailBq[<?= $no;?>][series]' value='<?= $valx['series'];?>'>
								<input type='hidden' name='DetailBq[<?= $no;?>][id_category]' id='id_category_<?= $no;?>' value='<?= $valx['id_category'];?>'>
							</td>
							<!--
							<td>
								<select name='DetailBq[<?= $no;?>][id_category]' id='id_category_<?= $no;?>' class='chosen_select form-control inline-block productXC' data-urut='<?= $no;?>'>
									<?php
									// foreach($restProduct AS $val3 => $valx3){
										// $selxc = ($valx['id_category'] == $valx3['product_parent'])?'selected':'';
										// echo "<option value='".$valx3['product_parent']."' ".$selxc.">".strtoupper($valx3['product_parent'])."</option>";
									// }
									?>
								</select>
							</td>
							--> 
							<td align='left'><?= strtoupper($valx['id_category']); ?></td> 
							
							<td><input type='text' name='DetailBq[<?= $no;?>][diameter_1]' id='diameter_1_<?= $no;?>' class='form-control numberOnly width_long' value='<?= floatval($valx['diameter_1']);?>' maxlength='4'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][diameter_2]' id='diameter_2_<?= $no;?>' class='form-control numberOnly width_long' value='<?= floatval($valx['diameter_2']);?>' maxlength='4'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][length]' id='length_<?= $no;?>' class='form-control numberOnly width_long' width_long value='<?= floatval($valx['length']);?>' maxlength='7'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][thickness]' id='thickness_<?= $no;?>' class='form-control numberOnly width_long' value='<?= floatval($valx['thickness']);?>' maxlength='6'></td>
							<td><input type='text' name='DetailBq[<?= $no;?>][sudut]' id='sudut_<?= $no;?>' class='form-control numberOnly width_long' value='<?= floatval($valx['sudut']);?>' maxlength='4'></td>
							<td>
								<select name='DetailBq[<?= $no;?>][id_standard]' id='id_standard_<?= $no;?>' class='chosen_select form-control inline-block'>
									<option value='0'>Standard</option>
									<?php
									foreach($restSup AS $val2 => $valx2){
										$selx = ($valx['id_standard'] == $valx2['id_standard'])?'selected':'';
										echo "<option value='".$valx2['id_standard']."' ".$selx.">".strtoupper($valx2['nm_standard'])."</option>";
									}
									?>
								</select>
							</td>
							<td>
								<select name='DetailBq[<?= $no;?>][type]' id='type_<?= $no;?>' class='chosen_select form-control inline-block'>
									<option value='0' <?= $selx1;?>>Type</option>
									<option value='SR' <?= $selx2;?>>Short Rad</option>
									<option value='LR' <?= $selx3;?>>Long Rad</option>
								</select>
							</td>
							<td><input type='text' name='DetailBq[<?= $no;?>][qty]' id='qty_<?= $no;?>' class='form-control numberOnly width_long' value='<?= floatval($valx['qty']);?>' maxlength='5'></td>
							<td><button type='button' data-id_bq_header='<?= $valx['id_bq_header']; ?>' data-id='<?= $valx['id']; ?>' class='btn btn-danger btn-sm del' data-toggle='tooltip' data-placement='bottom'><i class='fa fa-trash'></i></button></td>
							<td class='text-center'><input type='checkbox' name='check[<?= $no;?>]' class='chk_personal' value='<?= $valx['id']; ?>'></td>
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
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
		?>
	</div>
<style>
	td{
		vertical-align:middle;
	}
	.width_long{
		width: 80px; !important;
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

		$("#chk_all").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
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
		var KS_id_standard 	= "#id_standard_"+a+"_chosen";
		var KS_type 		= "#type_"+a+"_chosen";
		var KS_qty 			= "#qty_"+a;
		
		if(KS_id_category == 'pipe' || KS_id_category == 'pipe slongsong' || KS_id_category == 'saddle'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).hide();
			$(KS_length).show();
			$(KS_thickness).show();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'end cap' || KS_id_category == 'blind flange with hole'){
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
		else if(KS_id_category == 'concentric reducer' || KS_id_category == 'joint puddle flange' || KS_id_category == 'eccentric reducer' || KS_id_category == 'spacer ring' || KS_id_category == 'figure 8'){
			$(KS_diameter_1).show();
			$(KS_diameter_2).show();
			$(KS_thickness).show();
			$(KS_length).hide();
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
		}
		else if(KS_id_category == 'reducer tee mould' || KS_id_category == 'reducer tee slongsong' || KS_id_category == 'plate' || KS_id_category == 'rib' || KS_id_category == 'joint rib' || KS_id_category == 'loose flange' || KS_id_category == 'spacer' || KS_id_category == 'blind spacer'){
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
		else if(KS_id_category == 'shop joint' || KS_id_category == 'field joint'  || KS_id_category == 'spectacle blind' || KS_id_category == 'blank and spacer'){
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
		else if(KS_id_category == 'product kosong'){
			$(KS_diameter_1).show();
			$(KS_diameter_1).val(1);
			$(KS_length).hide();
			$(KS_diameter_2).show();
			$(KS_diameter_2).val(1);
			$(KS_thickness).show();
			$(KS_thickness).val(1);
			$(KS_sudut).hide();
			$(KS_id_standard).hide();
			$(KS_type).hide();
			$(KS_qty).val(1);
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
	
	
	$('#simpan-bro').click(function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		
		var intL = 0;
		var intError = 0;
		var pesan = '';
		
		$('#detail_bodyTop').find('tr').each(function(){
			intL++;
			var findId	= $(this).attr('id');
			var nomor	= findId.split('_');
			var qty			= $('#qty_'+nomor[1]).val();
			var sudut		= $('#sudut_'+nomor[1]).val();
			var thickness	= $('#thickness_'+nomor[1]).val();
			var length		= $('#length_'+nomor[1]).val();
			var diameter_2	= $('#diameter_2_'+nomor[1]).val();
			var diameter_1	= $('#diameter_1_'+nomor[1]).val();
			
			
			if(qty == '' || qty == 0 || qty == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Qty has not empty ...";
			}
			if(sudut == '' ||  sudut == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Angle type has not empty ...";
			}
			if(thickness == '' || thickness == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Thickness type has not empty ...";
			}
			if(length == '' || length == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Length type has not empty ...";
			}
			if(diameter_2 == '' || diameter_2 == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Diameter 2 type has not empty ...";
			}
			if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Diameter 1 type has not empty ...";
			}
		});
		
		if(intError > 0){
			swal({
				title	: "Notification Message !",
				text	: pesan,						
				type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;
		}
		
		$('#simpan-bro').prop('disabled',false);
		
		var data_url = $('#pembeda').val();
		
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
					var formData 	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url + active_controller +'/BqUpdated'; 
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
								window.location.href = base_url + active_controller +'/'+data_url;
							}
							else if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#simpan-bro').prop('disabled',false);
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
							$('#simpan-bro').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#simpan-bro').prop('disabled',false);
				return false;
			  }
		});
	});
	
	function AppendBaris(intd)
	{
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
			Rows	+=		"<input type='text' class='form-control numberOnly width_long' name='ListDetail["+nomor+"][thickness]' id='thicknessx_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Thick'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_long' name='ListDetail["+nomor+"][sudut]' id='sudutx_"+nomor+"' required autocomplete='off' maxlength='4' placeholder='Angle'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_standard]' id='id_standardx_"+nomor+"' class='form-control inline-block'></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<select name='ListDetail["+nomor+"][type]' id='typex_"+nomor+"' class='form-control inline-block'>";
			Rows	+=			"<option value='0'>Type</option>";
			Rows	+=			"<option value='SR'>Short Rad</option>";
			Rows	+=			"<option value='LR'>Long Rad</option>";
			Rows	+=		"</select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control numberOnly width_long' name='ListDetail["+nomor+"][qty]' id='qtyx_"+nomor+"' required autocomplete='off' maxlength='5' placeholder='Qty'>";
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
		var KS_qty 			= "#qtyx_"+nomor;
			
		$(document).on('change', id_category, function(){
			
			if($(this).val() == 'pipe' || $(this).val() == 'pipe slongsong' || $(this).val() == 'saddle'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'blind flange'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'flange mould' || $(this).val() == 'flange slongsong' || $(this).val() == 'colar' || $(this).val() == 'colar slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'concentric reducer' || $(this).val() == 'eccentric reducer' || $(this).val() == 'joint puddle flange' || $(this).val() == 'figure 8' || $(this).val() == 'spacer ring'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide(); 
				$(KS_type).hide();
			}
			else if($(this).val() == 'reducer tee mould' || $(this).val() == 'reducer tee slongsong' || $(this).val() == 'plate' || $(this).val() == 'rib' || $(this).val() == 'joint rib' || $(this).val() == 'loose flange' | $(this).val() == 'puddle flange' || $(this).val() == 'spacer' || $(this).val() == 'blind spacer'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'equal tee mould' || $(this).val() == 'equal tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'branch joint'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'shop joint' || $(this).val() == 'field joint' || $(this).val() == 'end cap'|| $(this).val() == 'blind flange with hole' || $(this).val() == 'spectacle blind' || $(this).val() == 'blank and spacer'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'puddle flange'){ 
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'loose flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).show();
				$(KS_type).hide();
			}
			else if($(this).val() == 'elbow mould' || $(this).val() == 'elbow mitter'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
			}
			else if($(this).val() == 'product kosong'){
				$(KS_diameter_1).show();
				$(KS_diameter_1).val(1);
				$(KS_length).hide();
				$(KS_diameter_2).show();
				$(KS_diameter_2).val(1);
				$(KS_thickness).show();
				$(KS_thickness).val(1);
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(KS_qty).val(1);
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