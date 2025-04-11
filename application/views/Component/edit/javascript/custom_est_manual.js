
$(document).on('keyup','#last_cost_6', function(){
	var resin 				= getNum($(this).val());
	var persen_katalis 		= getNum($('#Linperse_1').val()/100);
	var persen_sm 			= getNum($('#Linperse_2').val()/100);
	var persen_coblat 		= getNum($('#Linperse_3').val()/100);
	var persen_dma 			= getNum($('#Linperse_4').val()/100);
	var persen_hydroquinone = getNum($('#Linperse_5').val()/100);
	var persen_methanol 	= getNum($('#Linperse_6').val()/100);
	
	var layer_katalis 		= getNum($('#Lincontaining_1').val());
	var layer_sm 			= getNum($('#Lincontaining_2').val());
	var layer_coblat 		= getNum($('#Lincontaining_3').val());
	var layer_dma 			= getNum($('#Lincontaining_4').val());
	var layer_hydroquinone 	= getNum($('#Lincontaining_5').val());
	var layer_methanol 		= getNum($('#Lincontaining_6').val());
	
	$('#Linlast_cost_1').val(RoundUp(resin * persen_katalis * layer_katalis));
	$('#Linlast_cost_2').val(RoundUp(resin * persen_sm * layer_sm));
	$('#Linlast_cost_3').val(RoundUp(resin * persen_coblat * layer_coblat));
	$('#Linlast_cost_4').val(RoundUp(resin * persen_dma * layer_dma));
	$('#Linlast_cost_5').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
	$('#Linlast_cost_6').val(RoundUp(resin * persen_methanol * layer_methanol));
});

$(document).on('keyup','#last_costStr_7', function(){
	var resin 				= getNum($(this).val());
	var persen_katalis 		= getNum($('#Linperse2_1').val()/100);
	var persen_sm 			= getNum($('#Linperse2_2').val()/100);
	var persen_coblat 		= getNum($('#Linperse2_3').val()/100);
	var persen_dma 			= getNum($('#Linperse2_4').val()/100);
	var persen_hydroquinone = getNum($('#Linperse2_5').val()/100);
	var persen_methanol 	= getNum($('#Linperse2_6').val()/100);
	
	var layer_katalis 		= getNum($('#Lincontaining2_1').val());
	var layer_sm 			= getNum($('#Lincontaining2_2').val());
	var layer_coblat 		= getNum($('#Lincontaining2_3').val());
	var layer_dma 			= getNum($('#Lincontaining2_4').val());
	var layer_hydroquinone 	= getNum($('#Lincontaining2_5').val());
	var layer_methanol 		= getNum($('#Lincontaining2_6').val());
	
	$('#Linlast_cost2_1').val(RoundUp(resin * persen_katalis * layer_katalis));
	$('#Linlast_cost2_2').val(RoundUp(resin * persen_sm * layer_sm));
	$('#Linlast_cost2_3').val(RoundUp(resin * persen_coblat * layer_coblat));
	$('#Linlast_cost2_4').val(RoundUp(resin * persen_dma * layer_dma));
	$('#Linlast_cost2_5').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
	$('#Linlast_cost2_6').val(RoundUp(resin * persen_methanol * layer_methanol));
});

$(document).on('keyup','#last_costEks_5', function(){
	var resin 				= getNum($(this).val());
	var persen_katalis 		= getNum($('#Linperse3_1').val()/100);
	var persen_sm 			= getNum($('#Linperse3_2').val()/100);
	var persen_coblat 		= getNum($('#Linperse3_3').val()/100);
	var persen_dma 			= getNum($('#Linperse3_4').val()/100);
	var persen_hydroquinone = getNum($('#Linperse3_5').val()/100);
	var persen_methanol 	= getNum($('#Linperse3_6').val()/100);
	
	var layer_katalis 		= getNum($('#Lincontaining3_1').val());
	var layer_sm 			= getNum($('#Lincontaining3_2').val());
	var layer_coblat 		= getNum($('#Lincontaining3_3').val());
	var layer_dma 			= getNum($('#Lincontaining3_4').val());
	var layer_hydroquinone 	= getNum($('#Lincontaining3_5').val());
	var layer_methanol 		= getNum($('#Lincontaining3_6').val());
	
	$('#Linlast_cost3_1').val(RoundUp(resin * persen_katalis * layer_katalis));
	$('#Linlast_cost3_2').val(RoundUp(resin * persen_sm * layer_sm));
	$('#Linlast_cost3_3').val(RoundUp(resin * persen_coblat * layer_coblat));
	$('#Linlast_cost3_4').val(RoundUp(resin * persen_dma * layer_dma));
	$('#Linlast_cost3_5').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
	$('#Linlast_cost3_6').val(RoundUp(resin * persen_methanol * layer_methanol));
});

$(document).on('keyup','#last_topcoat_1', function(){
	var resin 			= getNum($(this).val());
	var persen_katalis4 = getNum($('#perse_topcoat_2').val()/100);
	var persen_color4 	= getNum($('#perse_topcoat_3').val()/100);
	var persen_tin4 	= getNum($('#perse_topcoat_4').val()/100);
	var persen_chl4 	= getNum($('#perse_topcoat_5').val()/100);
	var persen_stery4 	= getNum($('#perse_topcoat_6').val()/100);
	var persen_wax4 	= getNum($('#perse_topcoat_7').val()/100);
	var persen_mch4 	= getNum($('#perse_topcoat_8').val()/100);
	
	var layer_katalis4 	= getNum($('#cont_topcoat_2').val());
	var layer_color4 	= getNum($('#cont_topcoat_3').val());
	var layer_tin4 		= getNum($('#cont_topcoat_4').val());
	var layer_chl4 		= getNum($('#cont_topcoat_5').val());
	var layer_stery4 	= getNum($('#cont_topcoat_6').val());
	var layer_wax4 		= getNum($('#cont_topcoat_7').val());
	var layer_mch4 		= getNum($('#cont_topcoat_8').val());
	
	$('#last_topcoat_2').val(RoundUp(resin * persen_katalis4 * layer_katalis4));
	$('#last_topcoat_3').val(RoundUp(resin * persen_color4 * layer_color4));
	$('#last_topcoat_4').val(RoundUp(resin * persen_tin4 * layer_tin4));
	$('#last_topcoat_5').val(RoundUp(resin * persen_chl4 * layer_chl4));
	$('#last_topcoat_6').val(RoundUp(resin * persen_stery4 * layer_stery4));
	$('#last_topcoat_7').val(RoundUp(resin * persen_wax4 * layer_wax4));
	$('#last_topcoat_8').val(RoundUp(resin * persen_mch4 * layer_mch4));
});

//Liner
$(document).on('keyup', '.perse', function(){
	var TotResin	= parseFloat($('#last_cost_6').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});
$(document).on('keyup', '.perseLinAdd', function(){
	var TotResin	= parseFloat($('#last_cost_6').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//Structure
$(document).on('keyup', '.perseStr', function(){
	var TotResin	= parseFloat($('#last_costStr_7').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});
$(document).on('keyup', '.perseStrAdd', function(){
	var TotResin	= parseFloat($('#last_costStr_7').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//Ekternal
$(document).on('keyup', '.perseEks', function(){
	var TotResin	= parseFloat($('#last_costEks_5').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseEksAdd', function(){
	var TotResin	= parseFloat($('#last_costEks_5').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//Top Coat
$(document).on('keyup', '.perseTc', function(){
	var LastCoat	= parseFloat($('#last_topcoat_1').val());

	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= LastCoat * perse * containing;

	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseTcAdd', function(){
	var LastCoat	= parseFloat($('#last_topcoat_1').val());

	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= LastCoat * perse * containing;

	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});


$(document).on('keyup','.perseTC', function(){
	topcoatLast();
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

//Matertial Add
//LINER
$(document).on('keyup', '.ChangeContaining', function(){
	var total_resin	= $('#last_cost_6').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerse', function(){
	var total_resin	= $('#last_cost_6').val();
	var containing	= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	    = $(this).val() / 100;
	
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

$(document).on('change', '#mid_mtl_resin_tot2', function(){
	var id_material	= $(this).val();
	if(id_material == 'MTL-1903000'){
		$('#last_resin_tot2').val(0);
	}
	else{
		$('#last_resin_tot2').val('');
	}
});

//Material Add
//STRUKTURE
$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_costStr_5').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_costStr_5').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

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
	var total_resin	= $('#last_costEks_5').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseExt', function(){
	var total_resin	= $('#last_costEks_5').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val()/ 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangeContainingTC', function(){
	var total_resin	= $('#last_topcoat_1').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseTC', function(){
	var total_resin	= $('#last_topcoat_1').val();
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
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Liner("+nomor+")' title='Delete Record'>Delete</button></div>";
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
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Strukture("+nomor+")' title='Delete Record'>Delete</button></div>";
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
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_External("+nomor+")' title='Delete Record'>Delete</button></div>";
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
		Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_TopCoat("+nomor+")' title='Delete Record'>Delete</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_TopCoat["+nomor+"][last_full]' id='last_full_topcoat_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingTC' name='ListDetailAdd_TopCoat["+nomor+"][containing]' id='containing_topcoatadd_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='150px'>";
		Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseTC' name='ListDetailAdd_TopCoat["+nomor+"][perse]' id='perse_topcoatadd_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
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
}
function delRow_Strukture(row){
	$('#trstrukture_'+row).remove();
}
function delRow_External(row){
	$('#trexternal_'+row).remove();
}
function delRow_TopCoat(row){
	$('#trtopcoat_'+row).remove();
}