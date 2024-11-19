// document.writeln("<script src='change_all.js'></script>");

//OnKeyUp bawah Total Resin
//STRUKTURE
$(document).on('keyup', '#persen_katalis2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_katalis	= $('#layer_katalis2').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_katalis);
	$('#last_katalis2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_sm2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_sm	= $('#layer_sm2').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_sm);
	$('#last_sm2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_coblat2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_coblat	= $('#layer_coblat2').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_coblat);
	$('#last_cobalt2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_dma2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_dma		= $('#layer_dma2').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_dma);
	$('#last_dma2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_hydroquinone2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2').val();
	var layer_hydroquinone	= $('#layer_hydroquinone2').val();
	var Hasil				= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_hydroquinone);
	$('#last_hidro2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_methanol2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2').val();
	var layer_methanol		= $('#layer_methanol2').val();
	var Hasil				= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_methanol);
	$('#last_methanol2').val(Hasil.toFixed(3));
});

//Material Add
//STRUKTURE
$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_resin_tot2').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val();
	var containing	= $(this).val();
	
	var HasilAkhir	= parseFloat(total_resin) * parseFloat(perse) * parseFloat(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_resin_tot2').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val();
	
	var HasilAkhir	= parseFloat(total_resin) * parseFloat(perse) * parseFloat(containing);
	
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

$(document).on('keyup', '#layer_matcsm2', function(){
	var layer_veil	= $(this).val();
	var layer2		= $("#layer_csm_add2").val();
	var layer3		= $("#layer_wr2").val();
	var layer4		= $("#layer_wr_add2").val();
	
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	
	var thickness_veil 	= $('#thickness_matcsm2').val();
	var tot_thickness	= layer_veil*thickness_veil;

	var thickness2		= parseFloat($('#totthick_csm_add2').val());
	var thickness3		= parseFloat($('#totthick_wr2').val());
	var thickness4		= parseFloat($('#totthick_wr_add2').val());
	var tot_thickness2	= tot_thickness + thickness2 + thickness3 + thickness4;
	
	$('#tot_lin_thickness2').val(tot_thickness2.toFixed(4));
	$('#totthick_matcsm2').val(tot_thickness.toFixed(4));
	
	Hasil2(tot_thickness2, layer_veil, layer2, layer3, layer4, top_diameter, top_thickness, waste, angle);
	rubaharea();
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

$(document).on('keyup', '#layer_csm_add2', function(){
	var layer_veil		= $(this).val();
	var layer1		= $("#layer_matcsm2").val();
	var layer3		= $("#layer_wr2").val();
	var layer4		= $("#layer_wr_add2").val();
	
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	
	var thickness_veil 	= $('#thickness_csm_add2').val();
	var tot_thickness	= layer_veil*thickness_veil;
	
	var thickness1		= parseFloat($('#totthick_matcsm2').val());
	var thickness3		= parseFloat($('#totthick_wr2').val());
	var thickness4		= parseFloat($('#totthick_wr_add2').val());
	var tot_thickness2	= tot_thickness + thickness1 + thickness3 + thickness4;
	
	$('#tot_lin_thickness2').val(tot_thickness2.toFixed(4));
	$('#totthick_csm_add2').val(tot_thickness.toFixed(4));
	
	Hasil2(tot_thickness2, layer1, layer_veil, layer3, layer4, top_diameter, top_thickness, waste, angle);
	rubaharea();
});



$(document).on('change', '#mid_mtl_wr2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resinutama="+$('#mid_mtl_resin21').val(),
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

$(document).on('keyup', '#layer_wr2', function(){
	var layer_veil		= $(this).val();
	var layer1		= $("#layer_matcsm2").val();
	var layer2		= $("#layer_csm_add2").val();
	var layer4		= $("#layer_wr_add2").val();
	
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	
	var thickness_veil 	= $('#thickness_wr2').val();
	var tot_thickness	= layer_veil*thickness_veil;
	
	var thickness1		= parseFloat($('#totthick_matcsm2').val());
	var thickness2		= parseFloat($('#totthick_csm_add2').val());
	var thickness4		= parseFloat($('#totthick_wr_add2').val());
	var tot_thickness2	= tot_thickness + thickness1 + thickness2 + thickness4;
	
	$('#tot_lin_thickness2').val(tot_thickness2.toFixed(4));
	$('#totthick_wr2').val(tot_thickness.toFixed(4));
	
	Hasil2(tot_thickness2, layer1, layer2, layer_veil, layer4, top_diameter, top_thickness, waste, angle);
	rubaharea();
});

$(document).on('change', '#mid_mtl_wr_add2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr_add2').val(data.weight);
			$('#thickness_wr_add2').val(RoundUp4(data.thickness));
			$('#layer_wr_add2').val(data.layer);
			$('#layer_resin24hide').val(data.resin);
		}
	});
});

$(document).on('keyup', '#layer_wr_add2', function(){
	var layer_veil		= $(this).val();
	var layer1		= $("#layer_matcsm2").val();
	var layer2		= $("#layer_csm_add2").val();
	var layer3		= $("#layer_wr2").val();
	
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	
	var thickness_veil 	= $('#thickness_wr_add2').val();
	var tot_thickness	= layer_veil*thickness_veil;
	
	var thickness1		= parseFloat($('#totthick_matcsm2').val());
	var thickness2		= parseFloat($('#totthick_csm_add2').val());
	var thickness3		= parseFloat($('#totthick_wr2').val());
	var tot_thickness2	= tot_thickness + thickness1 + thickness2 + thickness3;
	
	$('#tot_lin_thickness2').val(tot_thickness2.toFixed(4));
	$('#totthick_wr_add2').val(tot_thickness.toFixed(4));
	
	Hasil2(tot_thickness2, layer1, layer2, layer3, layer_veil, top_diameter, top_thickness, waste, angle);
	rubaharea();
});


//====================================================================================================================================
//=============================================STRUCTURE THICKNESS NECK 1=============================================================
//====================================================================================================================================

$(document).on('keyup', '#persen_katalis2_neck1', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2_neck1').val();
	var layer_katalis	= $('#layer_katalis2_neck1').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_katalis);
	$('#last_katalis2_neck1').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_sm2_neck1', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2_neck1').val();
	var layer_sm		= $('#layer_sm2_neck1').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_sm);
	$('#last_sm2_neck1').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_coblat2_neck1', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2_neck1').val();
	var layer_coblat	= $('#layer_coblat2_neck1').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_coblat);
	$('#last_cobalt2_neck1').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_dma2_neck1', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2_neck1').val();
	var layer_dma		= $('#layer_dma2_neck1').val();
	var Hasil			= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_dma);
	$('#last_dma2_neck1').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_hydroquinone2_neck1', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2_neck1').val();
	var layer_hydroquinone	= $('#layer_hydroquinone2_neck1').val();
	var Hasil				= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_hydroquinone);
	$('#last_hidro2_neck1').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_methanol2_neck1', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2_neck1').val();
	var layer_methanol		= $('#layer_methanol2_neck1').val();
	var Hasil				= (parseFloat(nilai)/100) * parseFloat(hasil_resin_tot) * parseFloat(layer_methanol);
	$('#last_methanol2_neck1').val(Hasil.toFixed(3));
});

//Material Add
//STRUKTURE
$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_resin_tot2_neck1').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val();
	var containing	= $(this).val();
	
	var HasilAkhir	= parseFloat(total_resin) * parseFloat(perse) * parseFloat(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_resin_tot2_neck1').val();
	var containing	= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val();
	
	var HasilAkhir	= parseFloat(total_resin) * parseFloat(perse) * parseFloat(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('change', '#mid_mtl_resin_tot2_neck1', function(){
	if($("#mid_mtl_matcsm2_neck1").val() != 'MTL-1903000'){
		$("#layer_resin21hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_csm_add2_neck1").val() != 'MTL-1903000'){
		$("#layer_resin22hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_wr2_neck1").val() != 'MTL-1903000'){
		$("#layer_resin23hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_wr_add2_neck1").val() != 'MTL-1903000'){
		$("#layer_resin24hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_rooving21_neck1").val() != 'MTL-1903000'){
		$("#layer_resin25hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_rooving22_neck1").val() != 'MTL-1903000'){
		$("#layer_resin26hide_neck1").val($(this).val());
	}
});

$(document).on('change', '#mid_mtl_matcsm2_neck1', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsmX',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin3="+$('#layer_resin21_neck1').val(),
		dataType: "json",
		success: function(data){
			$('#weight_matcsm2_neck1').val(data.micron);
			$('#thickness_matcsm2_neck1').val(RoundUp4(data.thickness));
			$('#layer_matcsm2_neck1').val(data.layer);
			$('#layer_resin21hide_neck1').val(data.resin);
		}
	});
});

$(document).on('keyup', '#layer_matcsm2_neck1', function(){
	var jTop	= penentuAwal();
	// console.log(jTop);
	$('#tot_lin_thickness2_neck1').val(jTop.tot_thickness_neck1.toFixed(4));
	$('#totthick_matcsm2_neck1').val(jTop.thickness1_neck1.toFixed(4));
	Hasil2_neck1(jTop.tot_thickness_neck1, jTop.layer1_neck1, jTop.layer2_neck1, jTop.layer3_neck1, jTop.layer4_neck1, jTop.layer5_neck1, jTop.layer6_neck1, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_1, jTop.panjang_neck_1);
	rubaharea();
});

$(document).on('keyup', '#layer_csm_add2_neck1', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck1').val(jTop.tot_thickness_neck1.toFixed(4));
	$('#totthick_csm_add2_neck1').val(jTop.thickness2_neck1.toFixed(4));
	Hasil2_neck1(jTop.tot_thickness_neck1, jTop.layer1_neck1, jTop.layer2_neck1, jTop.layer3_neck1, jTop.layer4_neck1, jTop.layer5_neck1, jTop.layer6_neck1, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_1, jTop.panjang_neck_1);
	rubaharea();
});

$(document).on('keyup', '#layer_wr2_neck1', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck1').val(jTop.tot_thickness_neck1.toFixed(4));
	$('#totthick_wr2_neck1').val(jTop.thickness3_neck1.toFixed(4));
	Hasil2_neck1(jTop.tot_thickness_neck1, jTop.layer1_neck1, jTop.layer2_neck1, jTop.layer3_neck1, jTop.layer4_neck1, jTop.layer5_neck1, jTop.layer6_neck1, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_1, jTop.panjang_neck_1);
	rubaharea();
});

$(document).on('keyup', '#layer_wr_add2_neck1', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck1').val(jTop.tot_thickness_neck1.toFixed(4));
	$('#totthick_wr_add2_neck1').val(jTop.thickness4_neck1.toFixed(4));
	Hasil2_neck1(jTop.tot_thickness_neck1, jTop.layer1_neck1, jTop.layer2_neck1, jTop.layer3_neck1, jTop.layer4_neck1, jTop.layer5_neck1, jTop.layer6_neck1, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_1, jTop.panjang_neck_1);
	rubaharea();
});

$(document).on('keyup', '#layer_rooving21_neck1', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck1').val(jTop.tot_thickness_neck1.toFixed(4));
	$('#totthick_rooving21_neck1').val(jTop.thickness5_neck1.toFixed(4));
	Hasil2_neck1(jTop.tot_thickness_neck1, jTop.layer1_neck1, jTop.layer2_neck1, jTop.layer3_neck1, jTop.layer4_neck1, jTop.layer5_neck1, jTop.layer6_neck1, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_1, jTop.panjang_neck_1);
	rubaharea();
});

$(document).on('keyup', '#layer_rooving22_neck1', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck1').val(jTop.tot_thickness_neck1.toFixed(4));
	$('#totthick_rooving22_neck1').val(jTop.thickness6_neck1.toFixed(4));
	Hasil2_neck1(jTop.tot_thickness_neck1, jTop.layer1_neck1, jTop.layer2_neck1, jTop.layer3_neck1, jTop.layer4_neck1, jTop.layer5_neck1, jTop.layer6_neck1, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_1, jTop.panjang_neck_1);
	rubaharea();
});

$(document).on('change', '#mid_mtl_csm_add2_neck1', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin22_neck1').val(),
		dataType: "json",
		success: function(data){
			$('#weight_csm_add2_neck1').val(data.micron);
			$('#thickness_csm_add2_neck1').val(RoundUp4(data.thickness));
			$('#layer_csm_add2_neck1').val(data.layer);
			$('#layer_resin22hide_neck1').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_wr2_neck1', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resinutama="+$('#mid_mtl_resin21_neck1').val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr2_neck1').val(data.weight);
			$('#thickness_wr2_neck1').val(RoundUp4(data.thickness));
			$('#layer_wr2_neck1').val(data.layer);
			$("#mid_mtl_resin23_neck1").html(data.option).trigger("chosen:updated");
			$('#layer_resin23hide_neck1').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_wr_add2_neck1', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr_add2_neck1').val(data.weight);
			$('#thickness_wr_add2_neck1').val(RoundUp4(data.thickness));
			$('#layer_wr_add2_neck1').val(data.layer);
			$('#layer_resin24hide_neck1').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_rooving21_neck1', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getRooving',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin="+$('#layer_resin25_neck1').val(),
		dataType: "json",
		success: function(data){
			$('#weight_rooving21_neck1').val(data.weight);
			$('#thickness_rooving21_neck1').val(RoundUp4(data.thickness));
			$('#bw_rooving21_neck1').val(data.bw);
			$('#jumlah_rooving21_neck1').val(data.jumRoov);
			$('#layer_rooving21_neck1').val(data.layer);
			$('#layer_resin25hide_neck1').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_rooving22_neck1', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getRooving',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin="+$('#layer_resin26_neck1').val(),
		dataType: "json",
		success: function(data){
			$('#weight_rooving22_neck1').val(data.weight);
			$('#thickness_rooving22_neck1').val(RoundUp4(data.thickness));
			$('#bw_rooving22_neck1').val(data.bw);
			$('#jumlah_rooving22_neck1').val(data.jumRoov);
			$('#layer_rooving22_neck1').val(data.layer);
			$('#layer_resin26hide_neck1').val(data.resin);
		}
	});
});

//====================================================NECK 2==========================================================
$(document).on('change', '#mid_mtl_resin_tot2_neck2', function(){
	if($("#mid_mtl_matcsm2_neck2").val() != 'MTL-1903000'){
		$("#layer_resin21hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_csm_add2_neck2").val() != 'MTL-1903000'){
		$("#layer_resin22hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_wr2_neck2").val() != 'MTL-1903000'){
		$("#layer_resin23hide_neck1").val($(this).val());
	}
	if($("#mid_mtl_wr_add2_neck2").val() != 'MTL-1903000'){
		$("#layer_resin24hide_neck1").val($(this).val());
	}
});

$(document).on('change', '#mid_mtl_matcsm2_neck2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsmX',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin3="+$('#layer_resin21_neck2').val(),
		dataType: "json",
		success: function(data){
			$('#weight_matcsm2_neck2').val(data.micron);
			$('#thickness_matcsm2_neck2').val(RoundUp4(data.thickness));
			$('#layer_matcsm2_neck2').val(data.layer);
			$('#layer_resin21hide_neck2').val(data.resin);
		}
	});
});

$(document).on('keyup', '#layer_matcsm2_neck2', function(){
	var jTop	= penentuAwal();
	// console.log(jTop);
	$('#tot_lin_thickness2_neck2').val(jTop.tot_thickness_neck2.toFixed(4));
	$('#totthick_matcsm2_neck2').val(jTop.thickness1_neck2.toFixed(4));
	Hasil2_neck2(jTop.tot_thickness_neck2, jTop.layer1_neck2, jTop.layer2_neck2, jTop.layer3_neck2, jTop.layer4_neck2, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_2, jTop.panjang_neck_2);
	rubaharea();
});

$(document).on('keyup', '#layer_csm_add2_neck2', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck2').val(jTop.tot_thickness_neck2.toFixed(4));
	$('#totthick_csm_add2_neck2').val(jTop.thickness2_neck2.toFixed(4));
	Hasil2_neck2(jTop.tot_thickness_neck2, jTop.layer1_neck2, jTop.layer2_neck2, jTop.layer3_neck2, jTop.layer4_neck2, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_2, jTop.panjang_neck_2);
	rubaharea();
});

$(document).on('keyup', '#layer_wr2_neck2', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck2').val(jTop.tot_thickness_neck2.toFixed(4));
	$('#totthick_wr2_neck2').val(jTop.thickness3_neck2.toFixed(4));
	Hasil2_neck2(jTop.tot_thickness_neck2, jTop.layer1_neck2, jTop.layer2_neck2, jTop.layer3_neck2, jTop.layer4_neck2, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_2, jTop.panjang_neck_2);
	rubaharea();
});

$(document).on('keyup', '#layer_wr_add2_neck2', function(){
	var jTop	= penentuAwal();
	$('#tot_lin_thickness2_neck2').val(jTop.tot_thickness_neck2.toFixed(4));
	$('#totthick_wr_add2_neck2').val(jTop.thickness4_neck2.toFixed(4));
	Hasil2_neck2(jTop.tot_thickness_neck2, jTop.layer1_neck2, jTop.layer2_neck2, jTop.layer3_neck2, jTop.layer4_neck2, jTop.top_diameter, jTop.top_thickness, jTop.waste, jTop.angle, jTop.flange_od, jTop.area_neck_2, jTop.panjang_neck_2);
	rubaharea();
});

$(document).on('change', '#mid_mtl_csm_add2_neck2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCsm2',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resin4="+$('#layer_resin22_neck2').val(),
		dataType: "json",
		success: function(data){
			$('#weight_csm_add2_neck2').val(data.micron);
			$('#thickness_csm_add2_neck2').val(RoundUp4(data.thickness));
			$('#layer_csm_add2_neck2').val(data.layer);
			$('#layer_resin22hide_neck2').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_wr2_neck2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val()+"&resinutama="+$('#mid_mtl_resin21_neck2').val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr2_neck2').val(data.weight);
			$('#thickness_wr2_neck2').val(RoundUp4(data.thickness));
			$('#layer_wr2_neck2').val(data.layer);
			$("#mid_mtl_resin23_neck2").html(data.option).trigger("chosen:updated");
			$('#layer_resin23hide_neck2').val(data.resin);
		}
	});
});

$(document).on('change', '#mid_mtl_wr_add2_neck2', function(){
	var id_material	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getWoodR',
		cache: false,
		type: "POST",
		data: "id_material="+$(this).val(),
		dataType: "json",
		success: function(data){
			$('#weight_wr_add2_neck2').val(data.weight);
			$('#thickness_wr_add2_neck2').val(RoundUp4(data.thickness));
			$('#layer_wr_add2_neck2').val(data.layer);
			$('#layer_resin24hide_neck2').val(data.resin);
		}
	});
});
