$(document).on('change', '#external_layer', function(){
	if($(this).val() == 'N'){
		$("#mid_mtl_veil3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin31hide").val('MTL-1903000');
		$("#layer_veil3").val('0');

		$("#mid_mtl_veil_add3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin32hide").val('MTL-1903000');
		$("#layer_veil_add3").val('0');

		$("#mid_mtl_matcsm3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin33hide").val('MTL-1903000');
		$("#layer_matcsm3").val('0');

		$("#mid_mtl_csm_add3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin34hide").val('MTL-1903000');
		$("#layer_csm_add3").val('0');

		$("#mid_mtl_resin_tot3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#mid_mtl_katalis3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#mid_mtl_sm3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#mid_mtl_cobalt3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#mid_mtl_dma3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#mid_mtl_hydro3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#mid_mtl_methanol3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#hasil_linier_thickness3").val('OK');
	}
	else{
		$("#mid_mtl_veil3 option:selected").html('Select An Veil').trigger("chosen:updated");
		$("#layer_resin31hide").val('');
		$("#layer_veil3").val('');

		$("#mid_mtl_veil_add3 option:selected").html('Select An Veil Add').trigger("chosen:updated");
		$("#layer_resin32hide").val('');
		$("#layer_veil_add3").val('');

		$("#mid_mtl_matcsm3 option:selected").html('Select An MAT/CSM').trigger("chosen:updated");
		$("#layer_resin33hide").val('');
		$("#layer_matcsm3").val('');

		$("#mid_mtl_csm_add3 option:selected").html('Select An MAT/CSM').trigger("chosen:updated");
		$("#layer_resin34hide").val('');
		$("#layer_csm_add3").val('');

		$("#mid_mtl_resin_tot3 option:selected").html('Select An Resin').trigger("chosen:updated");
		$("#mid_mtl_katalis3 option:selected").html('Select An Katalis').trigger("chosen:updated");
		$("#mid_mtl_sm3 option:selected").html('Select An SM').trigger("chosen:updated");
		$("#mid_mtl_cobalt3 option:selected").html('Select An Cobalt').trigger("chosen:updated");
		$("#mid_mtl_dma3 option:selected").html('Select An DMA').trigger("chosen:updated");
		$("#mid_mtl_hydro3 option:selected").html('Select An Hydroquinone').trigger("chosen:updated");
		$("#mid_mtl_methanol3 option:selected").html('Select An Methanol').trigger("chosen:updated");
		$("#hasil_linier_thickness3").val('');
	}
});

//Change Material
$(document).on('change', '#mid_mtl_plastic', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getMicronPlastic',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&top_diameter="+$("#diameter").val(),
		dataType: "json",
		success: function(data){
			$('#micron_plastic').val(data.micron);
		}
	});
});

$(document).on('change', '#mid_mtl_veil', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getVeil',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin1="+$('#layer_resin1').val(),
		dataType: "json",
		success: function(data){
			$('#weight_veil').val(data.micron);
			$('#thickness_veil').val(RoundUp4(data.thickness));
			$('#layer_veil').val(data.layer);
			$('#layer_resin1hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_veil_add', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getVeil2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin2="+$('#layer_resin2').val(),
		dataType: "json",
		success: function(data){
			$('#weight_veil_add').val(data.micron);
			$('#thickness_veil_add').val(RoundUp4(data.thickness));
			$('#layer_veil_add').val(data.layer);
			$('#layer_resin2hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_matcsm', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin3="+$('#layer_resin3').val(),
		dataType: "json",
		success: function(data){
			$('#weight_matcsm').val(data.micron);
			$('#thickness_matcsm').val(RoundUp4(data.thickness));
			$('#layer_matcsm').val(data.layer);
			$('#layer_resin3hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_csm_add', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin4').val(),
		dataType: "json",
		success: function(data){
			$('#weight_csm_add').val(data.micron);
			$('#thickness_csm_add').val(RoundUp4(data.thickness));
			$('#layer_csm_add').val(data.layer);
			$('#layer_resin4hide').val(data.resin);
		}
	});
});

//resin all
$(document).on('change', '#mid_mtl_resin_tot', function(){
	if($("#mid_mtl_veil").val() != 'MTL-1903000'){
		$("#layer_resin1hide").val($(this).val());
	}
	if($("#mid_mtl_veil_add").val() != 'MTL-1903000'){
		$("#layer_resin2hide").val($(this).val());
	}
	if($("#mid_mtl_matcsm").val() != 'MTL-1903000'){
		$("#layer_resin3hide").val($(this).val());
	}
	if($("#mid_mtl_csm_add").val() != 'MTL-1903000'){
		$("#layer_resin4hide").val($(this).val());
	}
});

//Matertial Add
//LINER
$(document).on('keyup', '.ChangeContaining', function(){
	var total_resin	= $('#last_resin_tot').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerse', function(){
	var total_resin	= $('#last_resin_tot').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});

$(document).on('change', '#mid_mtl_resin_tot2', function(){
	if($("#mid_mtl_matcsm2").val() != 'MTL-1903000'){
		$("#layer_resin21hide").val($(this).val());
	}
	if($("#mid_mtl_csm_add2").val() != 'MTL-1903000'){
		$("#layer_resin22hide").val($(this).val());
	}
	if($("#mid_mtl_wr2").val() != 'MTL-1903000'){
		$("#layer_resin23hide").val($(this).val());
	}
	if($("#mid_mtl_wr_add2").val() != 'MTL-1903000'){
		$("#layer_resin24hide").val($(this).val());
	}
	if($("#mid_mtl_rooving21").val() != 'MTL-1903000'){
		$("#layer_resin25hide").val($(this).val());
	}
	if($("#mid_mtl_rooving22").val() != 'MTL-1903000'){
		$("#layer_resin26hide").val($(this).val());
	}

});

$(document).on('change', '#mid_mtl_matcsm2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsmX',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin3="+$('#layer_resin21').val(),
		dataType: "json",
		success: function(data){
			$('#weight_matcsm2').val(data.micron);
			$('#thickness_matcsm2').val(RoundUp4(data.thickness));
			$('#layer_matcsm2').val(data.layer);
			// $("#mid_mtl_resin21").html(data.option).trigger("chosen:updated");
			$('#layer_resin21hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_csm_add2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin22').val(),
		dataType: "json",
		success: function(data){
			$('#weight_csm_add2').val(data.micron);
			$('#thickness_csm_add2').val(RoundUp4(data.thickness));
			$('#layer_csm_add2').val(data.layer);
			$('#layer_resin22hide').val(data.resin);
		}
	});
});


$(document).on('change', '#mid_mtl_wr2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin23').val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr2').val(data.weight);
			$('#thickness_wr2').val(RoundUp4(data.thickness));
			$('#layer_wr2').val(data.layer);
			$("#mid_mtl_resin23").html(data.option).trigger("chosen:updated");
			$('#layer_resin23hide').val(data.resin);
		}
	});
});


$(document).on('change', '#mid_mtl_wr_add2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin24').val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr_add2').val(data.weight);
			$('#thickness_wr_add2').val(RoundUp4(data.thickness));
			$('#layer_wr_add2').val(data.layer);
			$('#layer_resin24hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_rooving21', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getRooving',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin="+$('#layer_resin25').val()+"&diameter="+$('#diameter').val(),
		dataType: "json",
		success: function(data){
			$('#weight_rooving21').val(data.weight);
			$('#thickness_rooving21').val(RoundUp4(data.thickness));
			// $('#bw_rooving21').val(data.bw);
			// $('#jumlah_rooving21').val(data.jumRoov);
			$('#layer_rooving21').val(data.layer);
			$('#layer_resin25hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_rooving22', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getRooving',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin="+$('#layer_resin26').val()+"&diameter="+$('#diameter').val(),
		dataType: "json",
		success: function(data){
			$('#weight_rooving22').val(data.weight);
			$('#thickness_rooving22').val(RoundUp4(data.thickness));
			// $('#bw_rooving22').val(data.bw);
			// $('#jumlah_rooving22').val(data.jumRoov);
			$('#layer_rooving22').val(data.layer);
			$('#layer_resin26hide').val(data.resin);
		}
	});
});


//Material Add
//STRUKTURE
$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_resin_tot2').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_resin_tot2').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});


$(document).on('keyup', '#acuhan_3', function(){
	var liner			= getNum($('#acuhan_1').val());
	var external		= getNum($('#acuhan_3').val());

	var top_thickness	= getNum($('#top_tebal_design').val());
	var struktur		= top_thickness - (liner + external);
	$('#acuhan_2').val(struktur.toFixed(2));

	AcuhanMaxMin();
});

$(document).on('change', '#mid_mtl_resin_tot3', function(){
	if($("#mid_mtl_veil3").val() != 'MTL-1903000'){
		$("#layer_resin31hide").val($(this).val());
	}
	if($("#mid_mtl_veil_add3").val() != 'MTL-1903000'){
		$("#layer_resin32hide").val($(this).val());
	}
	if($("#mid_mtl_matcsm3").val() != 'MTL-1903000'){
		$("#layer_resin33hide").val($(this).val());
	}
	if($("#mid_mtl_csm_add3").val() != 'MTL-1903000'){
		$("#layer_resin34hide").val($(this).val());
	}
});

$(document).on('change', '#mid_mtl_veil3', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getVeil',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin1="+$('#layer_resin31').val(),
		dataType: "json",
		success: function(data){
			$('#weight_veil3').val(data.micron);
			$('#thickness_veil3').val(RoundUp4(data.thickness));
			$('#layer_veil3').val(data.layer);
			$('#layer_resin31hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_veil_add3', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getVeil2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin2="+$('#layer_resin32').val(),
		dataType: "json",
		success: function(data){
			$('#weight_veil_add3').val(data.micron);
			$('#thickness_veil_add3').val(RoundUp4(data.thickness));
			$('#layer_veil_add3').val(data.layer);
			$('#layer_resin32hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_matcsm3', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin3="+$('#layer_resin33').val(),
		dataType: "json",
		success: function(data){
			$('#weight_matcsm3').val(data.micron);
			$('#thickness_matcsm3').val(RoundUp4(data.thickness));
			$('#layer_matcsm3').val(data.layer);
			$('#layer_resin33hide').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_csm_add3', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin34').val(),
		dataType: "json",
		success: function(data){
			$('#weight_csm_add3').val(data.micron);
			$('#thickness_csm_add3').val(RoundUp4(data.thickness));
			$('#layer_csm_add3').val(data.layer);
			$('#layer_resin34hide').val(data.resin);
		}
	});
});

//Material Add
//EXTERNAL
$(document).on('keyup', '.ChangeContainingExt', function(){
	var total_resin	= $('#last_resin_tot3').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseExt', function(){
	var total_resin	= $('#last_resin_tot3').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val()/ 100;

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangeContainingTC', function(){
	var total_resin	= $('#last_resin41').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseTC', function(){
	var total_resin	= $('#last_resin41').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;

	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);

	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

function RoundUp(x){
	var HasilRoundUp = (Math.ceil(x * 1000 ) / 1000).toFixed(3);
	return HasilRoundUp;
}

function RoundUp4(x){
	var HasilRoundUp = (Math.ceil(x * 10000 ) / 10000).toFixed(4);
	return HasilRoundUp;
}

function RoundUpEST(x){
	var HasilRoundUp = (Math.ceil(x * 1000 ) / 1000).toFixed(3);
	return HasilRoundUp;
}

var nomor	= 1;

$('#add_liner').click(function(e){
	e.preventDefault();
	AppendBaris_Liner(nomor);

	var nilaiAwal	= parseInt($("#numberMax_liner").val());
	var nilaiAkhir	= nilaiAwal + 1;
	$("#numberMax_liner").val(nilaiAkhir);
});

$('#add_strukture').click(function(e){
	e.preventDefault();
	AppendBaris_Strukture(nomor);

	var nilaiAwal	= parseInt($("#numberMax_strukture").val());
	var nilaiAkhir	= nilaiAwal + 1;
	$("#numberMax_strukture").val(nilaiAkhir);
});

$('#add_external').click(function(e){
	e.preventDefault();
	AppendBaris_External(nomor);

	var nilaiAwal	= parseInt($("#numberMax_external").val());
	var nilaiAkhir	= nilaiAwal + 1;
	$("#numberMax_external").val(nilaiAkhir);
});

$('#add_topcoat').click(function(e){
	e.preventDefault();
	AppendBaris_TopCoat(nomor);

	var nilaiAwal	= parseInt($("#numberMax_topcoat").val());
	var nilaiAkhir	= nilaiAwal + 1;
	$("#numberMax_topcoat").val(nilaiAkhir);
});

function AppendBaris_Liner(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_liner').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_liner tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trliner_"+nomor+"'>";
		Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Liner("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Liner["+nomor+"][last_full]' id='last_full_liner_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_Liner["+nomor+"][id_category]' id='id_category_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_Liner["+nomor+"][id_material]' id='id_material_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContaining' name='ListDetailAdd_Liner["+nomor+"][containing]' id='containing_liner_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerse' name='ListDetailAdd_Liner["+nomor+"][perse]' id='perse_liner_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_Liner["+nomor+"][last_cost]' id='last_cost_liner_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_liner').append(Rows);
	var id_category_liner_ 	= "#id_category_liner_"+nomor;
	var id_material_liner_ 	= "#id_material_liner_"+nomor;



	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_liner_).html(data.option).trigger("chosen:updated");
		}
	});

	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});

	$("#id_category_liner_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterial',
			cache: false,
			type: "POST",
			data: "id_category="+$(this).val(),
			dataType: "json",
			success: function(data){
				$(id_material_liner_).html(data.option).trigger("chosen:updated");
			}
		});
	});
	nomor++;
}

function AppendBaris_Strukture(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_strukture').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_strukture tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trstrukture_"+nomor+"'>";
		Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Strukture("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Strukture["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_Strukture["+nomor+"][id_category]' id='id_category_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_Strukture["+nomor+"][id_material]' id='id_material_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingStr' name='ListDetailAdd_Strukture["+nomor+"][containing]' id='containing_strukture_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseStr' name='ListDetailAdd_Strukture["+nomor+"][perse]' id='perse_strukture_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_Strukture["+nomor+"][last_cost]' id='last_cost_strukture_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_strukture').append(Rows);
	var id_category_strukture_ 	= "#id_category_strukture_"+nomor;
	var id_material_strukture_ 	= "#id_material_strukture_"+nomor;



	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_strukture_).html(data.option).trigger("chosen:updated");
		}
	});

	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});

	$("#id_category_strukture_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterial',
			cache: false,
			type: "POST",
			data: "id_category="+$(this).val(),
			dataType: "json",
			success: function(data){
				$(id_material_strukture_).html(data.option).trigger("chosen:updated");
			}
		});
	});
	nomor++;
}

function AppendBaris_External(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_external').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_external tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trexternal_"+nomor+"'>";
		Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_External("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_External["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_External["+nomor+"][id_category]' id='id_category_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_External["+nomor+"][id_material]' id='id_material_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingExt' name='ListDetailAdd_External["+nomor+"][containing]' id='containing_external_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseExt' name='ListDetailAdd_External["+nomor+"][perse]' id='perse_external_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_External["+nomor+"][last_cost]' id='last_cost_external_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_external').append(Rows);
	var id_category_external_ 	= "#id_category_external_"+nomor;
	var id_material_external_ 	= "#id_material_external_"+nomor;



	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_external_).html(data.option).trigger("chosen:updated");
		}
	});

	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});

	$("#id_category_external_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterial',
			cache: false,
			type: "POST",
			data: "id_category="+$(this).val(),
			dataType: "json",
			success: function(data){
				$(id_material_external_).html(data.option).trigger("chosen:updated");
			}
		});
	});
	nomor++;
}

function AppendBaris_TopCoat(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_topcoat').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_topcoat tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trtopcoat_"+nomor+"'>";
		Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_TopCoat("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_TopCoat["+nomor+"][last_full]' id='last_full_topcoat_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingTC' name='ListDetailAdd_TopCoat["+nomor+"][containing]' id='containing_topcoat_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseTC' name='ListDetailAdd_TopCoat["+nomor+"][perse]' id='perse_topcoat_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' id='last_cost_topcoat_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_topcoat').append(Rows);
	var id_category_topcoat_ 	= "#id_category_topcoat_"+nomor;
	var id_material_topcoat_ 	= "#id_material_topcoat_"+nomor;



	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_topcoat_).html(data.option).trigger("chosen:updated");
		}
	});

	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});

	$("#id_category_topcoat_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterial',
			cache: false,
			type: "POST",
			data: "id_category="+$(this).val(),
			dataType: "json",
			success: function(data){
				$(id_material_topcoat_).html(data.option).trigger("chosen:updated");
			}
		});
	});
	nomor++;
}


//delete add material
function delRow_Liner(row){
	$('#trliner_'+row).remove();
	// row = 0;
	var updatemax	=	$("#numberMax_liner").val() - 1;
	$("#numberMax_liner").val(updatemax);

	var maxLine = $("#numberMax_liner").val();
}
function delRow_Strukture(row){
	$('#trstrukture_'+row).remove();
	// row = 0;
	var updatemax	=	$("#numberMax_strukture").val() - 1;
	$("#numberMax_strukture").val(updatemax);

	var maxLine = $("#numberMax_strukture").val();
}
function delRow_External(row){
	$('#trexternal_'+row).remove();
	// row = 0;
	var updatemax	=	$("#numberMax_external").val() - 1;
	$("#numberMax_external").val(updatemax);

	var maxLine = $("#numberMax_external").val();
}
function delRow_TopCoat(row){
	$('#trtopcoat_'+row).remove();
	// row = 0;
	var updatemax	=	$("#numberMax_topcoat").val() - 1;
	$("#numberMax_topcoat").val(updatemax);

	var maxLine = $("#numberMax_topcoat").val();
}
