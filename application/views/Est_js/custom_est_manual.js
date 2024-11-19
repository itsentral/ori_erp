
$(document).on('keyup','#last_resin_tot', function(){
	var resin 				= getNum($(this).val());
	var persen_katalis 		= getNum($('#persen_katalis').val()/100);
	var persen_sm 			= getNum($('#persen_sm').val()/100);
	var persen_coblat 		= getNum($('#persen_coblat').val()/100);
	var persen_dma 			= getNum($('#persen_dma').val()/100);
	var persen_hydroquinone = getNum($('#persen_hydroquinone').val()/100);
	var persen_methanol 	= getNum($('#persen_methanol').val()/100);
	
	var layer_katalis 		= getNum($('#layer_katalis').val());
	var layer_sm 			= getNum($('#layer_sm').val());
	var layer_coblat 		= getNum($('#layer_coblat').val());
	var layer_dma 			= getNum($('#layer_dma').val());
	var layer_hydroquinone 	= getNum($('#layer_hydroquinone').val());
	var layer_methanol 		= getNum($('#layer_methanol').val());
	
	$('#last_katalis').val(RoundUp(resin * persen_katalis * layer_katalis));
	$('#last_sm').val(RoundUp(resin * persen_sm * layer_sm));
	$('#last_cobalt').val(RoundUp(resin * persen_coblat * layer_coblat));
	$('#last_dma').val(RoundUp(resin * persen_dma * layer_dma));
	$('#last_hidro').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
	$('#last_methanol').val(RoundUp(resin * persen_methanol * layer_methanol));
});

$(document).on('keyup','#last_resin_tot2', function(){
	var resin 				= getNum($(this).val());
	var persen_katalis 		= getNum($('#persen_katalis2').val()/100);
	var persen_sm 			= getNum($('#persen_sm2').val()/100);
	var persen_coblat 		= getNum($('#persen_coblat2').val()/100);
	var persen_dma 			= getNum($('#persen_dma2').val()/100);
	var persen_hydroquinone = getNum($('#persen_hydroquinone2').val()/100);
	var persen_methanol 	= getNum($('#persen_methanol2').val()/100);
	
	var layer_katalis 		= getNum($('#layer_katalis2').val());
	var layer_sm 			= getNum($('#layer_sm2').val());
	var layer_coblat 		= getNum($('#layer_coblat2').val());
	var layer_dma 			= getNum($('#layer_dma2').val());
	var layer_hydroquinone 	= getNum($('#layer_hydroquinone2').val());
	var layer_methanol 		= getNum($('#layer_methanol2').val());
	
	$('#last_katalis2').val(RoundUp(resin * persen_katalis * layer_katalis));
	$('#last_sm2').val(RoundUp(resin * persen_sm * layer_sm));
	$('#last_cobalt2').val(RoundUp(resin * persen_coblat * layer_coblat));
	$('#last_dma2').val(RoundUp(resin * persen_dma * layer_dma));
	$('#last_hidro2').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
	$('#last_methanol2').val(RoundUp(resin * persen_methanol * layer_methanol));
});

$(document).on('keyup','#last_resin_tot3', function(){
	var resin 				= getNum($(this).val());
	var persen_katalis 		= getNum($('#persen_katalis3').val()/100);
	var persen_sm 			= getNum($('#persen_sm3').val()/100);
	var persen_coblat 		= getNum($('#persen_coblat3').val()/100);
	var persen_dma 			= getNum($('#persen_dma3').val()/100);
	var persen_hydroquinone = getNum($('#persen_hydroquinone3').val()/100);
	var persen_methanol 	= getNum($('#persen_methanol3').val()/100);
	
	var layer_katalis 		= getNum($('#layer_katalis3').val());
	var layer_sm 			= getNum($('#layer_sm3').val());
	var layer_coblat 		= getNum($('#layer_coblat3').val());
	var layer_dma 			= getNum($('#layer_dma3').val());
	var layer_hydroquinone 	= getNum($('#layer_hydroquinone3').val());
	var layer_methanol 		= getNum($('#layer_methanol3').val());
	
	$('#last_katalis3').val(RoundUp(resin * persen_katalis * layer_katalis));
	$('#last_sm3').val(RoundUp(resin * persen_sm * layer_sm));
	$('#last_cobalt3').val(RoundUp(resin * persen_coblat * layer_coblat));
	$('#last_dma3').val(RoundUp(resin * persen_dma * layer_dma));
	$('#last_hidro3').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
	$('#last_methanol3').val(RoundUp(resin * persen_methanol * layer_methanol));
});

$(document).on('keyup','#last_resin41', function(){
	var resin 			= getNum($(this).val());
	var persen_katalis4 = getNum($('#persen_katalis4').val()/100);
	var persen_color4 	= getNum($('#persen_color4').val()/100);
	var persen_tin4 	= getNum($('#persen_tin4').val()/100);
	var persen_chl4 	= getNum($('#persen_chl4').val()/100);
	var persen_stery4 	= getNum($('#persen_stery4').val()/100);
	var persen_wax4 	= getNum($('#persen_wax4').val()/100);
	var persen_mch4 	= getNum($('#persen_mch4').val()/100);
	
	var layer_katalis4 	= getNum($('#layer_katalis4').val());
	var layer_color4 	= getNum($('#layer_color4').val());
	var layer_tin4 		= getNum($('#layer_tin4').val());
	var layer_chl4 		= getNum($('#layer_chl4').val());
	var layer_stery4 	= getNum($('#layer_stery4').val());
	var layer_wax4 		= getNum($('#layer_wax4').val());
	var layer_mch4 		= getNum($('#layer_mch4').val());
	
	$('#last_katalis4').val(RoundUp(resin * persen_katalis4 * layer_katalis4));
	$('#last_color4').val(RoundUp(resin * persen_color4 * layer_color4));
	$('#last_tin4').val(RoundUp(resin * persen_tin4 * layer_tin4));
	$('#last_chl4').val(RoundUp(resin * persen_chl4 * layer_chl4));
	$('#last_stery4').val(RoundUp(resin * persen_stery4 * layer_stery4));
	$('#last_wax4').val(RoundUp(resin * persen_wax4 * layer_wax4));
	$('#last_mch4').val(RoundUp(resin * persen_mch4 * layer_mch4));
});

$(document).on('keyup','.perseTC', function(){
	topcoatLast();
});

$(document).on('change', '#external_layer', function(){
	if($(this).val() == 'N'){ 
		$("#mid_mtl_veil3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin31hide").val('MTL-1903000');
		$("#layer_veil3").val('0');
		$("#last_veil3").val('0');
		
		$("#mid_mtl_veil_add3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin32hide").val('MTL-1903000');
		$("#layer_veil_add3").val('0');
		$("#last_veil_add3").val('0');
		
		$("#mid_mtl_matcsm3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin33hide").val('MTL-1903000');
		$("#layer_matcsm3").val('0');
		$("#last_matcsm3").val('0');
		
		$("#mid_mtl_csm_add3 option:selected").val('MTL-1903000').html('NONE MATERIAL').trigger("chosen:updated");
		$("#layer_resin34hide").val('MTL-1903000');
		$("#layer_csm_add3").val('0');
		$("#last_csm_add3").val('0');
		
		$("#last_resin_tot3").val('0');
		
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
		$("#last_veil3").val('');
		
		$("#mid_mtl_veil_add3 option:selected").html('Select An Veil Add').trigger("chosen:updated"); 
		$("#layer_resin32hide").val('');
		$("#layer_veil_add3").val('');
		$("#last_veil_add3").val('');
		
		$("#mid_mtl_matcsm3 option:selected").html('Select An MAT/CSM').trigger("chosen:updated"); 
		$("#layer_resin33hide").val('');
		$("#layer_matcsm3").val('');
		$("#last_matcsm3").val('');
		
		$("#mid_mtl_csm_add3 option:selected").html('Select An MAT/CSM').trigger("chosen:updated"); 
		$("#layer_resin34hide").val('');
		$("#layer_csm_add3").val('');
		$("#last_csm_add3").val('');
		
		$("#last_resin_tot3").val('');
		
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


$("#tamp").hide();

$(document).on('change', '#mid_mtl_plastic', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#last_plastic').val(0);
	}
	else{
		$('#last_plastic').val('');
	}
});

$(document).on('change', '#mid_mtl_veil', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_veil').val(0);
		$('#last_veil').val(0);
	}
	else{
		$('#layer_veil').val('');
		$('#last_veil').val('');
	}
});

$(document).on('change', '#mid_mtl_veil_add', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_veil_add').val(0);
		$('#last_veil_add').val(0);
	}
	else{
		$('#layer_veil_add').val('');
		$('#last_veil_add').val('');
	}
});

$(document).on('change', '#mid_mtl_matcsm', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_matcsm').val(0);
		$('#last_matcsm').val(0);
	}
	else{
		$('#layer_matcsm').val('');
		$('#last_matcsm').val('');
	}
});

$(document).on('change', '#mid_mtl_csm_add', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_csm_add').val(0);
		$('#last_csm_add').val(0);
	}
	else{
		$('#layer_csm_add').val('');
		$('#last_csm_add').val('');
	}
});

$(document).on('change', '#mid_mtl_resin_tot', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#last_resin_tot').val(0);
	}
	else{
		$('#last_resin_tot').val('');
	}
});
//OnKeyUp bawah Total Resin
$(document).on('keyup', '#persen_katalis', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot').val();
	var layer_katalis	= $('#layer_katalis').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_katalis);
	$('#last_katalis').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_sm', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot').val();
	var layer_sm	= $('#layer_sm').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_coblat', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot').val();
	var layer_coblat	= $('#layer_coblat').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_dma', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot').val();
	var layer_dma		= $('#layer_dma').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_hydroquinone', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#last_resin_tot').val();
	var layer_hydroquinone	= $('#layer_hydroquinone').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_methanol', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#last_resin_tot').val();
	var layer_methanol		= $('#layer_methanol').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_methanol);
	$('#last_methanol').val(RoundUp(Hasil));
});

//Matertial Add
//LINER
$(document).on('keyup', '.ChangeContaining', function(){
	var total_resin	= $('#last_resin_tot').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerse', function(){
	var total_resin	= $('#last_resin_tot').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});


$(document).on('change', '#mid_mtl_matcsm2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_matcsm2').val(0);
		$('#last_matcsm2').val(0);
	}
	else{
		$('#layer_matcsm2').val('');
		$('#last_matcsm2').val('');
	}
});

$(document).on('change', '#mid_mtl_csm_add2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_csm_add2').val(0);
		$('#last_csm_add2').val(0);
	}
	else{
		$('#layer_csm_add2').val('');
		$('#last_csm_add2').val('');
	}
});

$(document).on('change', '#mid_mtl_wr2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_wr2').val(0);
		$('#last_wr2').val(0);
	}
	else{
		$('#layer_wr2').val('');
		$('#last_wr2').val('');
	}
});

$(document).on('change', '#mid_mtl_wr_add2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_wr_add2').val(0);
		$('#last_wr_add2').val(0);
	}
	else{
		$('#layer_wr_add2').val('');
		$('#last_wr_add2').val('');
	}
});

$(document).on('change', '#mid_mtl_rv2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_rv2').val(0);
		$('#last_rv2').val(0);
	}
	else{
		$('#layer_rv2').val('');
		$('#last_rv2').val('');
	}
});

$(document).on('change', '#mid_mtl_rv_add2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_rv_add2').val(0);
		$('#last_rv_add2').val(0);
	}
	else{
		$('#layer_rv_add2').val('');
		$('#last_rv_add2').val('');
	}
});

$(document).on('change', '#mid_mtl_resin_tot2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#last_resin_tot2').val(0);
	}
	else{
		$('#last_resin_tot2').val('');
	}
});
//OnKeyUp bawah Total Resin
//STRUKTURE
$(document).on('keyup', '#persen_katalis2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot2').val();
	var layer_katalis	= $('#layer_katalis2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_katalis);
	$('#last_katalis2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_sm2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot2').val();
	var layer_sm	= $('#layer_sm2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_coblat2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot2').val();
	var layer_coblat	= $('#layer_coblat2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_dma2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot2').val();
	var layer_dma		= $('#layer_dma2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_hydroquinone2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#last_resin_tot2').val();
	var layer_hydroquinone	= $('#layer_hydroquinone2').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_methanol2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#last_resin_tot2').val();
	var layer_methanol		= $('#layer_methanol2').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_methanol);
	$('#last_methanol2').val(RoundUp(Hasil));
});

//Material Add
//STRUKTURE
$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_resin_tot2').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_resin_tot2').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});

//OnKeyUp bawah Total Resin
//EXTERNAL
$(document).on('keyup', '#persen_katalis3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot3').val();
	var layer_katalis	= $('#layer_katalis3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_katalis);
	$('#last_katalis3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_sm3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot3').val();
	var layer_sm	= $('#layer_sm3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_coblat3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot3').val();
	var layer_coblat	= $('#layer_coblat3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_dma3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#last_resin_tot3').val();
	var layer_dma		= $('#layer_dma3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_hydroquinone3', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#last_resin_tot3').val();
	var layer_hydroquinone	= $('#layer_hydroquinone3').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_methanol3', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#last_resin_tot3').val();
	var layer_methanol		= $('#layer_methanol3').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_methanol);
	$('#last_methanol3').val(RoundUp(Hasil));
});


$(document).on('change', '#mid_mtl_veil3', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_veil3').val(0);
		$('#last_veil3').val(0);
	}
	else{
		$('#layer_veil3').val('');
		$('#last_veil3').val('');
	}
});

$(document).on('change', '#mid_mtl_veil_add3', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_veil_add3').val(0);
		$('#last_veil_add3').val(0);
	}
	else{
		$('#layer_veil_add3').val('');
		$('#last_veil_add3').val('');
	}
});

$(document).on('change', '#mid_mtl_matcsm3', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_matcsm3').val(0);
		$('#last_matcsm3').val(0);
	}
	else{
		$('#layer_matcsm3').val('');
		$('#last_matcsm3').val('');
	}
});

$(document).on('change', '#mid_mtl_csm_add3', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#layer_csm_add3').val(0);
		$('#last_csm_add3').val(0);
	}
	else{
		$('#layer_csm_add3').val('');
		$('#last_csm_add3').val('');
	}
});

$(document).on('change', '#mid_mtl_resin_tot3', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#last_resin_tot3').val(0);
	}
	else{
		$('#last_resin_tot3').val('');
	}
});

$(document).on('change', '#mid_mtl_resin41', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#last_resin41').val(0);
	}
	else{
		$('#last_resin41').val('');
	}
});
//Material Add
//EXTERNAL
$(document).on('keyup', '.ChangeContainingExt', function(){
	var total_resin	= $('#last_resin_tot3').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseExt', function(){
	var total_resin	= $('#last_resin_tot3').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val()/ 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangeContainingTC', function(){
	var total_resin	= $('#last_resin41').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseTC', function(){
	var total_resin	= $('#last_resin41').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

function topcoatLast(){
	var HasilTopCoat	= getNum($("#last_resin41").val());
	var persen_katalis4	= getNum($("#persen_katalis4").val());
	var persen_color4	= getNum($("#persen_color4").val());
	var persen_tin4		= getNum($("#persen_tin4").val());
	var persen_chl4		= getNum($("#persen_chl4").val());
	var persen_stery4	= getNum($("#persen_stery4").val());
	var persen_wax4		= getNum($("#persen_wax4").val());
	var persen_mch4		= getNum($("#persen_mch4").val());

	var Katalis4	= 1;
	var Color4		= 1;
	var Tinuvin4	= 0.1;
	var Chlr4		= 0.9;
	var Stery4		= 0.9;
	var Wax4		= 0.1;
	var MetCh4		= 1;
	var Addv4		= 1;
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
		$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
		$('#last_color4').val(RoundUp(HasilColor4));
		
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
		$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
		$('#last_chl4').val(RoundUp(HasilChlr4));
	
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
		$('#last_stery4').val(RoundUp(HasilStery4));
	
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
		$('#last_wax4').val(RoundUp(HasilWax4));
	
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
		$('#last_mch4').val(RoundUp(HasilMetCh4));
}

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
	
    $('.chosen_select').chosen();
	
	
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_liner_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
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
                $('.chosen_select').chosen();
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
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_strukture_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
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
                $('.chosen_select').chosen();
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
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_external_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
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
                $('.chosen_select').chosen();
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
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_topcoat_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
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
                $('.chosen_select').chosen();
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