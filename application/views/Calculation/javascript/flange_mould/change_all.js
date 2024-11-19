
//===============================================================================================================
//================================================LUAS AREA======================================================
	function LuasArea(top_diameter, flange_od, waste){
		var pangkat_od			= Math.pow(flange_od, 2);
		var pangkat_dim			= Math.pow(top_diameter, 2);
		var Luas_Area_Rumus		= 3.14/4 * (pangkat_od - pangkat_dim) / 1000000 * (1+waste);
		if(isNaN(Luas_Area_Rumus)){
			var Luas_Area_Rumus = 0;
		}
		return Luas_Area_Rumus;
	}
	
	function LuasAreaNeck1(top_diameter, length_neck1, est_neck1, waste){
		var Luas_Area_Rumus_Neck1		= 3.14 * (top_diameter + est_neck1) * length_neck1 / 1000000 * (1+waste);
		if(isNaN(Luas_Area_Rumus_Neck1)){
			var Luas_Area_Rumus_Neck1 = 0;
		}
		// console.log(Luas_Area_Rumus_Neck1);
		return Luas_Area_Rumus_Neck1;
	}
	
	function LuasAreaNeck2(top_diameter, length_neck2, est_neck1, est_neck2, waste){
		var Luas_Area_Rumus_Neck2		= 3.14 * (top_diameter + est_neck1 + est_neck2) * length_neck2 / 1000000 * (1+waste);
		if(isNaN(Luas_Area_Rumus_Neck2)){
			var Luas_Area_Rumus_Neck2 = 0;
		}
		// console.log(Luas_Area_Rumus_Neck1);
		return Luas_Area_Rumus_Neck2;
	}
	
	function BeratPlastic(area_neck_1, Luas_Area_Rumus){
		var perkalian = 1350;
		if(top_diameter < 25){ var perkalian = 800; }
		var HasilPlastic		= (Luas_Area_Rumus + area_neck_1) * 0.000025 * 800;
		if(isNaN(HasilPlastic)){
			var HasilPlastic = 0;
		}
		return HasilPlastic;
	}

//===================================================END=========================================================
//===============================================================================================================

function Hasil(a, b, c, d, e, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1){
	var acuhan_1			= parseFloat($('#acuhan_1').val());
	var top_min_toleran		= parseFloat($('#top_min_toleran').val());
	var min_lin_thickness	= acuhan_1 - (acuhan_1*top_min_toleran);
	var max_lin_thickness	= acuhan_1 + (acuhan_1*top_min_toleran);
	
	$('#mix_lin_thickness').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness').val(max_lin_thickness.toFixed(4));
	
	var topEST				= parseFloat($('#tot_lin_thickness').val()) + parseFloat($('#tot_lin_thickness2_neck1').val()) + parseFloat($('#tot_lin_thickness3').val());
		$('#top_tebal_est').val(RoundUpEST(topEST));
	
	var topEST_neck1		= parseFloat($('#tot_lin_thickness').val()) + parseFloat($('#tot_lin_thickness2_neck1').val());
	$('#est_neck_1').val(RoundUpEST(topEST_neck1));

	var micron_plastic 		= parseFloat($('#micron_plastic').val());
	var layer_plastic		= parseFloat($('#layer_plastic').val());
	var weight_veil			= parseFloat($('#weight_veil').val());
	var layer_resin1		= parseFloat($('#layer_resin1').val());
	var weight_veil_add		= parseFloat($('#weight_veil_add').val());
	var layer_resin2		= parseFloat($('#layer_resin2').val());
	var weight_matcsm		= parseFloat($('#weight_matcsm').val());
	var layer_resin3		= parseFloat($('#layer_resin3').val());
	var weight_csm_add		= parseFloat($('#weight_csm_add').val());
	var layer_resin4		= parseFloat($('#layer_resin4').val());
	var layer_resin_tot		= parseFloat($('#layer_resin_tot').val());
	
	var persen_katalis		= parseFloat($('#persen_katalis').val());
	var persen_sm			= parseFloat($('#persen_sm').val());
	var persen_coblat		= parseFloat($('#persen_coblat').val());
	var persen_dma			= parseFloat($('#persen_dma').val());
	var persen_hydroquinone	= parseFloat($('#persen_hydroquinone').val());
	var persen_methanol	= parseFloat($('#persen_methanol').val());
	
	var Luas_Area_Rumus	= LuasArea(top_diameter, flange_od, waste);
		$('#area').val(Luas_Area_Rumus.toFixed(6));
								
	var Luas_Area_RumusNeck1	= LuasAreaNeck1(top_diameter, panjang_neck_1, topEST_neck1, waste);
		$('#area_neck_1').val(RoundUpEST(Luas_Area_RumusNeck1));
	
	var HasilPlastic	= BeratPlastic(area_neck_1, Luas_Area_Rumus);
		$('#hasil_plastic').val(HasilPlastic);
		$('#last_plastic').val(RoundUp(HasilPlastic));
		
	var HasilVeil	= ((Luas_Area_Rumus + area_neck_1) * weight_veil * b)/1000;
		$('#hasil_veil').val(RoundUp10(HasilVeil));
		$('#last_veil').val(RoundUp(HasilVeil));
	
	var Hasillayer_resin1	= parseFloat(HasilVeil) * layer_resin1;
		$('#hasil_resin1').val(RoundUp10(Hasillayer_resin1));
		$('#last_resin1').val(RoundUp(Hasillayer_resin1));
	
	var HasilVeilAdd	= ((Luas_Area_Rumus + area_neck_1) * weight_veil_add * c)/1000;
		$('#hasil_veil_add').val(RoundUp10(HasilVeilAdd));
		$('#last_veil_add').val(RoundUp(HasilVeilAdd));
		
	var Hasillayer_resin12	= parseFloat(HasilVeilAdd) * layer_resin2;
		$('#hasil_resin2').val(RoundUp10(Hasillayer_resin12));
		$('#last_resin2').val(RoundUp(Hasillayer_resin12));
	
	var HasilMatCsm	= ((Luas_Area_Rumus + area_neck_1) * weight_matcsm * d)/1000;
		$('#hasil_matcsm').val(RoundUp10(HasilMatCsm));
		$('#last_matcsm').val(RoundUp(HasilMatCsm));
	
	var Hasillayer_resin13	= parseFloat(HasilMatCsm) * layer_resin3;
		$('#hasil_resin3').val(RoundUp10(Hasillayer_resin13));
		$('#last_resin3').val(RoundUp(Hasillayer_resin13));
	
	var HasilMatCsmAdd	= ((Luas_Area_Rumus + area_neck_1) * weight_csm_add * e)/1000; 
		$('#hasil_csm_add').val(RoundUp10(HasilMatCsmAdd));
		$('#last_csm_add').val(RoundUp(HasilMatCsmAdd));
	
	var Hasillayer_resin14	= parseFloat(HasilMatCsmAdd) * layer_resin4;
		$('#hasil_resin4').val(RoundUp10(Hasillayer_resin14));
		$('#last_resin4').val(RoundUp(Hasillayer_resin14));
	
	var TotalResin	= (Luas_Area_Rumus* 1.2 *layer_resin_tot) + Hasillayer_resin14 + Hasillayer_resin13 + Hasillayer_resin12 + Hasillayer_resin1;
		$('#hasil_resin_tot').val(RoundUp10(TotalResin));
		$('#last_resin_tot').val(RoundUp(TotalResin));
	
	if(TotalResin == '' || TotalResin == 0 || TotalResin == '0' || TotalResin == null){
		var Katalis	= 0;
		var Sm		= 0;
		var Coblat	= 0;
		var Dma		= 0;
		var Hyro	= 0;
		var Methanol= 0;
	}
	else if(TotalResin > 0){
		var Katalis	= 1;
		var Sm		= 1;
		var Coblat	= 0.6;
		var Dma		= 0.4;
		var Hyro	= 0.1;
		var Methanol= 0.9;
	}
	
		$('#layer_katalis').val(Katalis);
		$('#layer_sm').val(Sm);
		$('#layer_coblat').val(Coblat);
		$('#layer_dma').val(Dma);
		$('#layer_hydroquinone').val(Hyro);
		$('#layer_methanol').val(Methanol);
	
	
	var HasilKatalis	= Katalis * (persen_katalis/100) * TotalResin;
		$('#hasil_katalis').val(RoundUp10(HasilKatalis));
		$('#last_katalis').val(RoundUp(HasilKatalis));
	
	var HasilSm			= Sm * (persen_sm/100) * TotalResin;
		$('#hasil_sm').val(RoundUp10(HasilSm));
		$('#last_sm').val(RoundUp(HasilSm));
		
	var HasilCoblat		= Coblat * (persen_coblat/100) * TotalResin;
		$('#hasil_coblat').val(RoundUp10(HasilCoblat));
		$('#last_cobalt').val(RoundUp(HasilCoblat));
		
	var HasilDma		= Dma * (persen_dma/100) * TotalResin;
		$('#hasil_dma').val(RoundUp10(HasilDma));
		$('#last_dma').val(RoundUp(HasilDma));
	
	var HasilHydro		= Hyro * (persen_hydroquinone/100) * TotalResin;
		$('#hasil_hydroquinone').val(RoundUp10(HasilHydro));
		$('#last_hidro').val(RoundUp(HasilHydro));
	
	var HasilMethanol	= Methanol * (persen_methanol/100) * TotalResin;
		$('#hasil_methanol').val(RoundUp10(HasilMethanol));
		$('#last_methanol').val(RoundUp(HasilMethanol));

	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness').val(Hasil);
	
	topcoatLast(Luas_Area_Rumus);
}

function Hasil2(a, b, c, d, e, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1){
	var acuhan_2			= parseFloat($('#acuhan_2').val());
	var top_min_toleran		=  parseFloat($('#top_min_toleran').val());
	var min_lin_thickness	= acuhan_2 - (acuhan_2*top_min_toleran);
	var max_lin_thickness	= acuhan_2 + (acuhan_2*top_min_toleran);
	
	$('#mix_lin_thickness2').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness2').val(max_lin_thickness.toFixed(4));
	
	var topEST				= parseFloat($('#tot_lin_thickness').val()) + parseFloat($('#tot_lin_thickness2_neck1').val()) + parseFloat($('#tot_lin_thickness3').val());
	
	var weight_matcsm2		= parseFloat($('#weight_matcsm2').val());
	var layer_matcsm2		= parseFloat($('#layer_matcsm2').val());
	var weight_csm_add2		= parseFloat($('#weight_csm_add2').val());
	var layer_csm_add2		= parseFloat($('#layer_csm_add2').val());
	var weight_wr2			= parseFloat($('#weight_wr2').val());
	var layer_wr2			= parseFloat($('#layer_wr2').val());
	var weight_wr_add2		= parseFloat($('#weight_wr_add2').val());
	var layer_wr_add2		= parseFloat($('#layer_wr_add2').val());
	
	var layer_resin21		= parseFloat($('#layer_resin21').val());
	var layer_resin22		= parseFloat($('#layer_resin22').val());
	var layer_resin23		= parseFloat($('#layer_resin23').val());
	var layer_resin24		= parseFloat($('#layer_resin24').val());
	
	var persen_katalis2		= parseFloat($('#persen_katalis2').val());
	var persen_sm2			= parseFloat($('#persen_sm2').val());
	var persen_coblat2		= parseFloat($('#persen_coblat2').val());
	var persen_dma2			= parseFloat($('#persen_dma2').val());
	var persen_hydroquinone2	= parseFloat($('#persen_hydroquinone2').val());
	var persen_methanol2	= parseFloat($('#persen_methanol2').val());
	
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	
	var HasilMadCsm			= (Luas_Area_Rumus * weight_matcsm2 * b)/1000;
	var Hasillayer21		= parseFloat(HasilMadCsm) * layer_resin21;
	var HasilMadCsmAdd		= (Luas_Area_Rumus * weight_csm_add2 * c)/1000;
	var Hasillayer22		= parseFloat(HasilMadCsmAdd) * layer_resin22;
	var HasilWr				= (Luas_Area_Rumus * weight_wr2 * d)/1000;
	var Hasillayer23		= parseFloat(HasilWr) * layer_resin23;
	var HasilWrAdd			= (Luas_Area_Rumus * weight_wr_add2 * e)/1000;
	var Hasillayer24		= parseFloat(HasilWrAdd) * layer_resin24;
	
	var TotalResin2			= Hasillayer21 + Hasillayer22 + Hasillayer23 + Hasillayer24;
	
	if(TotalResin2 == '' || TotalResin2 == 0 || TotalResin2 == '0' || TotalResin2 == null){
		var Katalis2	= 0;
		var Sm2			= 0;
		var Coblat2		= 0;
		var Dma2		= 0;
		var Hyro2		= 0;
		var Methanol2	= 0;
	}
	else if(TotalResin2 > 0){
		var Katalis2	= 1;
		var Sm2			= 1;
		var Coblat2		= 0.6;
		var Dma2		= 0.4;
		var Hyro2		= 0.1;
		var Methanol2	= 0.9;
	}
	
	var HasilKatalis2	= Katalis2 * (persen_katalis2/100) * TotalResin2;
	var HasilSm2		= Sm2 * (persen_sm2/100) * TotalResin2;
	var HasilCoblat2	= Coblat2 * (persen_coblat2/100) * TotalResin2;
	var HasilDma2		= Dma2 * (persen_dma2/100) * TotalResin2;
	var HasilHydro2		= Hyro2 * (persen_hydroquinone2/100) * TotalResin2;
	var HasilMethanol2	= Methanol2 * (persen_methanol2/100) * TotalResin2;
	
	//TopCoat
	var ResinCoat		= parseFloat($("#resin41").val());
	
	var persen_katalis4	= parseFloat($("#persen_katalis4").val());
	var persen_color4	= parseFloat($("#persen_color4").val());
	var persen_tin4		= parseFloat($("#persen_tin4").val());
	var persen_chl4		= parseFloat($("#persen_chl4").val());
	var persen_stery4	= parseFloat($("#persen_stery4").val());
	var persen_wax4		= parseFloat($("#persen_wax4").val());
	var persen_mch4		= parseFloat($("#persen_mch4").val());
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2  * ResinCoat * 4);
	
	if(HasilTopCoat == '' || HasilTopCoat == 0 || HasilTopCoat == '0' || HasilTopCoat == null){
		var Katalis4	= 0;
		var Color4		= 0;
		var Tinuvin4	= 0;
		var Chlr4		= 0;
		var Stery4		= 0;
		var Wax4		= 0;
		var MetCh4		= 0;
		var Addv4		= 0;
	}
	else if(HasilTopCoat > 0){
		var Katalis4	= 1;
		var Color4		= 1;
		var Tinuvin4	= 0.1;
		var Chlr4		= 0.9;
		var Stery4		= 0.9;
		var Wax4		= 0.1;
		var MetCh4		= 1;
		var Addv4		= 1;
	}
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
	
	//Sampai Sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness2').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	
	//Hasil Perhitungan Hitam
	$('#hasil_matcsm2').val(RoundUp10(HasilMadCsm));
	$('#last_matcsm2').val(RoundUp(HasilMadCsm));
	$('#hasil_resin21').val(RoundUp10(Hasillayer21));
	$('#last_resin21').val(RoundUp(Hasillayer21));
	
	$('#hasil_csm_add2').val(RoundUp10(HasilMadCsmAdd));
	$('#last_csm_add2').val(RoundUp(HasilMadCsmAdd));
	$('#hasil_resin22').val(RoundUp10(Hasillayer22));
	$('#last_resin22').val(RoundUp(Hasillayer22));
	
	$('#hasil_wr2').val(RoundUp10(HasilWr));
	$('#last_wr2').val(RoundUp(HasilWr));
	$('#hasil_resin23').val(RoundUp10(Hasillayer23));
	$('#last_resin23').val(RoundUp(Hasillayer23));
	
	$('#hasil_wr_add2').val(RoundUp10(HasilWrAdd));
	$('#last_wr_add2').val(RoundUp(HasilWrAdd));
	$('#hasil_resin24').val(RoundUp10(Hasillayer24));
	$('#last_resin24').val(RoundUp(Hasillayer24));
	
	$('#hasil_resin_tot2').val(RoundUp10(TotalResin2));
	$('#last_resin_tot2').val(RoundUp(TotalResin2));
	
	$('#layer_katalis2').val(Katalis2);
	$('#hasil_katalis2').val(RoundUp10(HasilKatalis2));
	$('#last_katalis2').val(RoundUp(HasilKatalis2));
	
	$('#layer_sm2').val(Sm2);
	$('#hasil_sm2').val(RoundUp10(HasilSm2));
	$('#last_sm2').val(RoundUp(HasilSm2));
	
	$('#layer_coblat2').val(Coblat2);
	$('#hasil_coblat2').val(RoundUp10(HasilCoblat2));
	$('#last_cobalt2').val(RoundUp(HasilCoblat2));
	
	$('#layer_dma2').val(Dma2);
	$('#hasil_dma2').val(RoundUp10(HasilDma2));
	$('#last_dma2').val(RoundUp(HasilDma2));
	
	$('#layer_hydroquinone2').val(Hyro2);
	$('#hasil_hydroquinone2').val(RoundUp10(HasilHydro2));
	$('#last_hidro2').val(RoundUp(HasilHydro2));
	
	$('#layer_methanol2').val(Methanol2);
	$('#hasil_methanol2').val(RoundUp10(HasilMethanol2));
	$('#last_methanol2').val(RoundUp(HasilMethanol2));
	
	//TopCoat
	$('#hasil_resin41').val(RoundUp10(HasilTopCoat));
	$('#last_resin41').val(RoundUp(HasilTopCoat));
	
	$('#layer_katalis4').val(Katalis4);
	$('#hasil_katalis4').val(RoundUp10(HasilKatalis4));
	$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	$('#layer_color4').val(Color4);
	$('#hasil_color4').val(RoundUp10(HasilColor4));
	$('#last_color4').val(RoundUp(HasilColor4));
	
	$('#layer_tin4').val(Tinuvin4);
	$('#hasil_tin4').val(RoundUp10(HasilTinuvin4));
	$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	$('#layer_chl4').val(Chlr4);
	$('#hasil_chl4').val(RoundUp10(HasilChlr4));
	$('#last_chl4').val(RoundUp(HasilChlr4));
	
	$('#layer_stery4').val(Stery4);
	$('#hasil_stery4').val(RoundUp10(HasilStery4));
	$('#last_stery4').val(RoundUp(HasilStery4));
	
	$('#layer_wax4').val(Wax4);
	$('#hasil_wax4').val(RoundUp10(HasilWax4));
	$('#last_wax4').val(RoundUp(HasilWax4));
	
	$('#layer_mch4').val(MetCh4);
	$('#hasil_mch4').val(RoundUp10(HasilMetCh4));
	$('#last_mch4').val(RoundUp(HasilMetCh4));
}

function Hasil3(a, b, c, d, e, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1){
	var acuhan_3			= parseFloat($('#acuhan_3').val());
	var top_min_toleran		=  parseFloat($('#top_min_toleran').val());
	var min_lin_thickness	= acuhan_3 - (acuhan_3*top_min_toleran);
	var max_lin_thickness	= acuhan_3 + (acuhan_3*top_min_toleran);
	
	$('#mix_lin_thickness3').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness3').val(max_lin_thickness.toFixed(4));
	
	var topEST				= parseFloat($('#tot_lin_thickness').val()) + parseFloat($('#tot_lin_thickness2_neck1').val()) + parseFloat($('#tot_lin_thickness3').val());
	
	//perhitungan hitam
	var weight_veil3		= parseFloat($('#weight_veil3').val());
	var weight_veil_add3	= parseFloat($('#weight_veil_add3').val());
	var weight_matcsm3		= parseFloat($('#weight_matcsm3').val());
	var weight_csm_add3		= parseFloat($('#weight_csm_add3').val());
	
	var layer_resin31		= parseFloat($('#layer_resin31').val());
	var layer_resin32		= parseFloat($('#layer_resin32').val());
	var layer_resin33		= parseFloat($('#layer_resin33').val());
	var layer_resin34		= parseFloat($('#layer_resin34').val());
	
	var persen_katalis3		= parseFloat($('#persen_katalis3').val());
	var persen_sm3			= parseFloat($('#persen_sm3').val());
	var persen_coblat3		= parseFloat($('#persen_coblat3').val());
	var persen_dma3			= parseFloat($('#persen_dma3').val());
	var persen_hydroquinone3	= parseFloat($('#persen_hydroquinone3').val());
	var persen_methanol3	= parseFloat($('#persen_methanol3').val());
	
	// var pangkat_od			= Math.pow(flange_od, 2);
	// var pangkat_dim			= Math.pow(top_diameter, 2);
	// var Luas_Area_Rumus		= 3.14/4 * (pangkat_od - pangkat_dim) / 1000000 * (1+waste));
	
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	
	var HasilVeil3			= (Luas_Area_Rumus * weight_veil3 * b)/1000;
	var Hasillayer31		= parseFloat(HasilVeil3) * layer_resin31;
	var HasilVeilAdd3		= (Luas_Area_Rumus * weight_veil_add3 * c)/1000;
	var Hasillayer32		= parseFloat(HasilVeilAdd3) * layer_resin32;
	var HasilMadCsm3		= (Luas_Area_Rumus * weight_matcsm3 * d)/1000;
	var Hasillayer33		= parseFloat(HasilMadCsm3) * layer_resin33;
	var HasilMadCsmAdd3		= (Luas_Area_Rumus * weight_csm_add3 * e)/1000;
	var Hasillayer34		= parseFloat(HasilMadCsmAdd3) * layer_resin34;
	
	var TotalResin3			= Hasillayer31 + Hasillayer32 + Hasillayer33 + Hasillayer34;
	
	if(TotalResin3 == '' || TotalResin3 == 0 || TotalResin3 == '0' || TotalResin3 == null){
		var Katalis3	= 0;
		var Sm3			= 0;
		var Coblat3		= 0;
		var Dma3		= 0;
		var Hyro3		= 0;
		var Methanol3	= 0;
	}
	else if(TotalResin3 > 0){
		var Katalis3	= 1;
		var Sm3			= 1;
		var Coblat3		= 0.6;
		var Dma3		= 0.4;
		var Hyro3		= 0.1;
		var Methanol3	= 0.9;
	}
	
	var HasilKatalis3	= Katalis3 * (persen_katalis3/100) * TotalResin3;
	var HasilSm3		= Sm3 * (persen_sm3/100) * TotalResin3;
	var HasilCoblat3	= Coblat3 * (persen_coblat3/100) * TotalResin3;
	var HasilDma3		= Dma3 * (persen_dma3/100) * TotalResin3;
	var HasilHydro3		= Hyro3 * (persen_hydroquinone3/100) * TotalResin3;
	var HasilMethanol3	= Methanol3 * (persen_methanol3/100) * TotalResin3;
	
	//TopCoat
	var ResinCoat		= parseFloat($("#resin41").val());
	
	var persen_katalis4	= parseFloat($("#persen_katalis4").val());
	var persen_color4	= parseFloat($("#persen_color4").val());
	var persen_tin4		= parseFloat($("#persen_tin4").val());
	var persen_chl4		= parseFloat($("#persen_chl4").val());
	var persen_stery4	= parseFloat($("#persen_stery4").val());
	var persen_wax4		= parseFloat($("#persen_wax4").val());
	var persen_mch4		= parseFloat($("#persen_mch4").val());
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2 * ResinCoat * 4);
	
	if(HasilTopCoat == '' || HasilTopCoat == 0 || HasilTopCoat == '0' || HasilTopCoat == null){
		var Katalis4	= 0;
		var Color4		= 0;
		var Tinuvin4	= 0;
		var Chlr4		= 0;
		var Stery4		= 0;
		var Wax4		= 0;
		var MetCh4		= 0;
		var Addv4		= 0;
	}
	else if(HasilTopCoat > 0){
		var Katalis4	= 1;
		var Color4		= 1;
		var Tinuvin4	= 0.1;
		var Chlr4		= 0.9;
		var Stery4		= 0.9;
		var Wax4		= 0.1;
		var MetCh4		= 1;
		var Addv4		= 1;
	}
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
	
	//sampai sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness3').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	
	//Penjumlahan Hitam
	$('#hasil_veil3').val(RoundUp10(HasilVeil3)); 
	$('#last_veil3').val(RoundUp(HasilVeil3));
	$('#hasil_resin31').val(RoundUp10(Hasillayer31));
	$('#last_resin31').val(RoundUp(Hasillayer31));
	
	$('#hasil_veil_add3').val(RoundUp10(HasilVeilAdd3));
	$('#last_veil_add3').val(RoundUp(HasilVeilAdd3));
	$('#hasil_resin32').val(RoundUp10(Hasillayer32));
	$('#last_resin32').val(RoundUp(Hasillayer32));
	
	$('#hasil_matcsm3').val(RoundUp10(HasilMadCsm3));
	$('#last_matcsm3').val(RoundUp(HasilMadCsm3));
	$('#hasil_resin33').val(RoundUp10(Hasillayer33));
	$('#last_resin33').val(RoundUp(Hasillayer33));
	
	$('#hasil_csm_add3').val(RoundUp10(HasilMadCsmAdd3));
	$('#last_csm_add3').val(RoundUp(HasilMadCsmAdd3));
	$('#hasil_resin34').val(RoundUp10(Hasillayer34));
	$('#last_resin34').val(RoundUp(Hasillayer34)); 
	
	$('#hasil_resin_tot3').val(RoundUp10(TotalResin3));
	$('#last_resin_tot3').val(RoundUp(TotalResin3));
	
	$('#layer_katalis3').val(Katalis3);
	$('#hasil_katalis3').val(RoundUp10(HasilKatalis3));
	$('#last_katalis3').val(RoundUp(HasilKatalis3));
	
	$('#layer_sm3').val(Sm3);
	$('#hasil_sm3').val(RoundUp10(HasilSm3));
	$('#last_sm3').val(RoundUp(HasilSm3));
	
	$('#layer_coblat3').val(Coblat3);
	$('#hasil_coblat3').val(RoundUp10(HasilCoblat3));
	$('#last_cobalt3').val(RoundUp(HasilCoblat3));
	
	$('#layer_dma3').val(Dma3);
	$('#hasil_dma3').val(RoundUp10(HasilDma3));
	$('#last_dma3').val(RoundUp(HasilDma3));
	
	$('#layer_hydroquinone3').val(Hyro3);
	$('#hasil_hydroquinone3').val(RoundUp10(HasilHydro3));
	$('#last_hidro3').val(RoundUp(HasilHydro3));
	
	$('#layer_methanol3').val(Methanol3);
	$('#hasil_methanol3').val(RoundUp10(HasilMethanol3));
	$('#last_methanol3').val(RoundUp(HasilMethanol3));
	
	//TopCoat
	$('#hasil_resin41').val(RoundUp10(HasilTopCoat));
	$('#last_resin41').val(RoundUp(HasilTopCoat));
	
	$('#layer_katalis4').val(Katalis4);
	$('#hasil_katalis4').val(RoundUp10(HasilKatalis4));
	$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	$('#layer_color4').val(Color4);
	$('#hasil_color4').val(RoundUp10(HasilColor4));
	$('#last_color4').val(RoundUp(HasilColor4));
	
	$('#layer_tin4').val(Tinuvin4);
	$('#hasil_tin4').val(RoundUp10(HasilTinuvin4));
	$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	$('#layer_chl4').val(Chlr4);
	$('#hasil_chl4').val(RoundUp10(HasilChlr4));
	$('#last_chl4').val(RoundUp(HasilChlr4));
	
	$('#layer_stery4').val(Stery4);
	$('#hasil_stery4').val(RoundUp10(HasilStery4));
	$('#last_stery4').val(RoundUp(HasilStery4));
	
	$('#layer_wax4').val(Wax4);
	$('#hasil_wax4').val(RoundUp10(HasilWax4));
	$('#last_wax4').val(RoundUp(HasilWax4));
	
	$('#layer_mch4').val(MetCh4);
	$('#hasil_mch4').val(RoundUp10(HasilMetCh4));
	$('#last_mch4').val(RoundUp(HasilMetCh4));
}

function Hasil2_neck1(a, b, c, d, e, f, g, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1){
	// alert('masuk');
	var acuhan_2			= parseFloat($('#acuhan_2_neck1').val());
	var top_min_toleran		=  parseFloat($('#top_min_toleran').val());
	var top_max_toleran		=  parseFloat($('#top_max_toleran').val());
	var min_lin_thickness	= acuhan_2 - (acuhan_2*top_min_toleran);
	var max_lin_thickness	= acuhan_2 + (acuhan_2*top_max_toleran);
	
	$('#mix_lin_thickness2_neck1').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness2_neck1').val(max_lin_thickness.toFixed(4));
	
	var topEST_neck1		= parseFloat($('#tot_lin_thickness').val()) + parseFloat(a);
	
	var weight_matcsm2		= parseFloat($('#weight_matcsm2_neck1').val());
	var layer_matcsm2		= parseFloat($('#layer_matcsm2_neck1').val());
	var weight_csm_add2		= parseFloat($('#weight_csm_add2_neck1').val());
	var layer_csm_add2		= parseFloat($('#layer_csm_add2_neck1').val());
	var weight_wr2			= parseFloat($('#weight_wr2_neck1').val());
	var layer_wr2			= parseFloat($('#layer_wr2_neck1').val());
	var weight_wr_add2		= parseFloat($('#weight_wr_add2_neck1').val());
	var layer_wr_add2		= parseFloat($('#layer_wr_add2_neck1').val());
	
	var weight_rooving21	= parseFloat($('#weight_rooving21_neck1').val());
	var penggali_rooving21	= parseFloat($('#penggali_rooving21_neck1').val());
	var bw_rooving21		= parseFloat($('#bw_rooving21_neck1').val());
	var jumlah_rooving21	= parseFloat($('#jumlah_rooving21_neck1').val());
	
	var weight_rooving22	= parseFloat($('#weight_rooving22_neck1').val());
	var penggali_rooving22	= parseFloat($('#penggali_rooving22_neck1').val());
	var bw_rooving22		= parseFloat($('#bw_rooving22_neck1').val());
	var jumlah_rooving22	= parseFloat($('#jumlah_rooving22_neck1').val());
	
	var layer_resin21		= parseFloat($('#layer_resin21_neck1').val());
	var layer_resin22		= parseFloat($('#layer_resin22_neck1').val());
	var layer_resin23		= parseFloat($('#layer_resin23_neck1').val());
	var layer_resin24		= parseFloat($('#layer_resin24_neck1').val());
	var layer_resin25		= parseFloat($('#layer_resin25_neck1').val());
	var layer_resin26		= parseFloat($('#layer_resin26_neck1').val());
	
	var persen_katalis2		= parseFloat($('#persen_katalis2_neck1').val());
	var persen_sm2			= parseFloat($('#persen_sm2_neck1').val());
	var persen_coblat2		= parseFloat($('#persen_coblat2_neck1').val());
	var persen_dma2			= parseFloat($('#persen_dma2_neck1').val());
	var persen_hydroquinone2	= parseFloat($('#persen_hydroquinone2_neck1').val());
	var persen_methanol2	= parseFloat($('#persen_methanol2_neck1').val());
	
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	var Luas_Area_RumusNeck1	= LuasAreaNeck1(top_diameter, panjang_neck_1, topEST_neck1, waste);
		$('#area_neck_1').val(RoundUpEST(Luas_Area_RumusNeck1));
	
	var HasilMadCsm			= (Luas_Area_RumusNeck1 * weight_matcsm2 * b)/1000;
	var Hasillayer21		= parseFloat(HasilMadCsm) * layer_resin21;
	var HasilMadCsmAdd		= (Luas_Area_RumusNeck1 * weight_csm_add2 * c)/1000; 
	var Hasillayer22		= parseFloat(HasilMadCsmAdd) * layer_resin22;
	var HasilWr				= (Luas_Area_RumusNeck1 * weight_wr2 * d)/1000;
	var Hasillayer23		= parseFloat(HasilWr) * layer_resin23;
	var HasilWrAdd			= (Luas_Area_RumusNeck1 * weight_wr_add2 * e)/1000;
	var Hasillayer24		= parseFloat(HasilWrAdd) * layer_resin24;
	
	
	
	var HasilRoof21			= ((weight_rooving21 * 0.001 * jumlah_rooving21 * penggali_rooving21)/(bw_rooving21/10)) * (2/1000) * f * Luas_Area_RumusNeck1;
	
	if(isNaN(HasilRoof21)){
		var HasilRoof21		= 0;
	}
	var Hasillayer25		= parseFloat(HasilRoof21) * layer_resin25;
	
	var HasilRoof22			= ((weight_rooving22 * 0.001 * jumlah_rooving22 * penggali_rooving22)/(bw_rooving22/10)) * (2/1000) * g * Luas_Area_RumusNeck1;
	if(isNaN(HasilRoof22)){
		var HasilRoof22		= 0;
	}
	var Hasillayer26		= parseFloat(HasilRoof22) * layer_resin26;
	
	var TotalResin2			= Hasillayer21 + Hasillayer22 + Hasillayer23 + Hasillayer24 + Hasillayer25 + Hasillayer26;
	
	if(TotalResin2 == '' || TotalResin2 == 0 || TotalResin2 == '0' || TotalResin2 == null){
		var Katalis2	= 0;
		var Sm2			= 0;
		var Coblat2		= 0;
		var Dma2		= 0;
		var Hyro2		= 0;
		var Methanol2	= 0;
	}
	else if(TotalResin2 > 0){
		var Katalis2	= 1;
		var Sm2			= 1;
		var Coblat2		= 0.6;
		var Dma2		= 0.4;
		var Hyro2		= 0.1;
		var Methanol2	= 0.9;
	}
	
	var HasilKatalis2	= Katalis2 * (persen_katalis2/100) * TotalResin2;
	var HasilSm2		= Sm2 * (persen_sm2/100) * TotalResin2;
	var HasilCoblat2	= Coblat2 * (persen_coblat2/100) * TotalResin2;
	var HasilDma2		= Dma2 * (persen_dma2/100) * TotalResin2;
	var HasilHydro2		= Hyro2 * (persen_hydroquinone2/100) * TotalResin2;
	var HasilMethanol2	= Methanol2 * (persen_methanol2/100) * TotalResin2;
	
	//Sampai Sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness2_neck1').val(Hasil);
	$('#est_neck_1').val(RoundUpEST(topEST_neck1));
	
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	
	//Hasil Perhitungan Hitam
	$('#hasil_matcsm2_neck1').val(RoundUp10(HasilMadCsm));
	$('#last_matcsm2_neck1').val(RoundUp(HasilMadCsm));
	$('#hasil_resin21_neck1').val(RoundUp10(Hasillayer21));
	$('#last_resin21_neck1').val(RoundUp(Hasillayer21));
	
	$('#hasil_csm_add2_neck1').val(RoundUp10(HasilMadCsmAdd));
	$('#last_csm_add2_neck1').val(RoundUp(HasilMadCsmAdd));
	$('#hasil_resin22_neck1').val(RoundUp10(Hasillayer22));
	$('#last_resin22_neck1').val(RoundUp(Hasillayer22));
	
	$('#hasil_wr2_neck1').val(RoundUp10(HasilWr));
	$('#last_wr2_neck1').val(RoundUp(HasilWr));
	$('#hasil_resin23_neck1').val(RoundUp10(Hasillayer23));
	$('#last_resin23_neck1').val(RoundUp(Hasillayer23));
	
	$('#hasil_wr_add2_neck1').val(RoundUp10(HasilWrAdd));
	$('#last_wr_add2_neck1').val(RoundUp(HasilWrAdd));
	$('#hasil_resin24_neck1').val(RoundUp10(Hasillayer24));
	$('#last_resin24_neck1').val(RoundUp(Hasillayer24));
	
	$('#hasil_rooving21_neck1').val(RoundUp10(HasilRoof21));
	$('#last_rooving21_neck1').val(RoundUp(HasilRoof21));
	$('#hasil_resin25_neck1').val(RoundUp10(Hasillayer25));
	$('#last_resin25_neck1').val(RoundUp(Hasillayer25));
	
	$('#hasil_rooving22_neck1').val(RoundUp10(HasilRoof22));
	$('#last_rooving22_neck1').val(RoundUp(HasilRoof22));
	$('#hasil_resin26_neck1').val(RoundUp10(Hasillayer26));
	$('#last_resin26_neck1').val(RoundUp(Hasillayer26));
	
	$('#hasil_resin_tot2_neck1').val(RoundUp10(TotalResin2));
	$('#last_resin_tot2_neck1').val(RoundUp(TotalResin2));
	
	$('#layer_katalis2_neck1').val(Katalis2);
	$('#hasil_katalis2_neck1').val(RoundUp10(HasilKatalis2));
	$('#last_katalis2_neck1').val(RoundUp(HasilKatalis2));
	
	$('#layer_sm2_neck1').val(Sm2);
	$('#hasil_sm2_neck1').val(RoundUp10(HasilSm2));
	$('#last_sm2_neck1').val(RoundUp(HasilSm2));
	
	$('#layer_coblat2_neck1').val(Coblat2);
	$('#hasil_coblat2_neck1').val(RoundUp10(HasilCoblat2));
	$('#last_cobalt2_neck1').val(RoundUp(HasilCoblat2));
	
	$('#layer_dma2_neck1').val(Dma2);
	$('#hasil_dma2_neck1').val(RoundUp10(HasilDma2));
	$('#last_dma2_neck1').val(RoundUp(HasilDma2));
	
	$('#layer_hydroquinone2_neck1').val(Hyro2);
	$('#hasil_hydroquinone2_neck1').val(RoundUp10(HasilHydro2));
	$('#last_hidro_neck1').val(RoundUp(HasilHydro2));
	
	$('#layer_methanol2_neck1').val(Methanol2);
	$('#hasil_methanol2_neck1').val(RoundUp10(HasilMethanol2));
	$('#last_methanol2_neck1').val(RoundUp(HasilMethanol2));
}

function Hasil2_neck2(a, b, c, d, e, top_diameter, top_thickness, waste, angle, flange_od, area_neck_2, panjang_neck_2){
	var acuhan_2			= parseFloat($('#acuhan_2_neck2').val());
	var top_min_toleran		=  parseFloat($('#top_min_toleran').val());
	var top_max_toleran		=  parseFloat($('#top_max_toleran').val());
	var min_lin_thickness	= acuhan_2 - (acuhan_2 * top_min_toleran);
	var max_lin_thickness	= acuhan_2 + (acuhan_2 * top_max_toleran);
	
	$('#mix_lin_thickness2_neck2').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness2_neck2').val(max_lin_thickness.toFixed(4));
	
	//estimasi neck 2 sama dengan thickness nya  
	var topEST_neck1		= parseFloat($('#tot_lin_thickness').val()) +  parseFloat($('#tot_lin_thickness2_neck1').val());
	var topEST_neck2		= parseFloat(a);
	
	var weight_matcsm2		= parseFloat($('#weight_matcsm2_neck2').val());
	var layer_matcsm2		= parseFloat($('#layer_matcsm2_neck2').val());
	var weight_csm_add2		= parseFloat($('#weight_csm_add2_neck2').val());
	var layer_csm_add2		= parseFloat($('#layer_csm_add2_neck2').val());
	var weight_wr2			= parseFloat($('#weight_wr2_neck2').val());
	var layer_wr2			= parseFloat($('#layer_wr2_neck2').val());
	var weight_wr_add2		= parseFloat($('#weight_wr_add2_neck2').val());
	var layer_wr_add2		= parseFloat($('#layer_wr_add2_neck2').val());
	
	var layer_resin21		= parseFloat($('#layer_resin21_neck2').val());
	var layer_resin22		= parseFloat($('#layer_resin22_neck2').val());
	var layer_resin23		= parseFloat($('#layer_resin23_neck2').val());
	var layer_resin24		= parseFloat($('#layer_resin24_neck2').val());
	
	var persen_katalis2		= parseFloat($('#persen_katalis2_neck2').val());
	var persen_sm2			= parseFloat($('#persen_sm2_neck2').val());
	var persen_coblat2		= parseFloat($('#persen_coblat2_neck2').val());
	var persen_dma2			= parseFloat($('#persen_dma2_neck2').val());
	var persen_hydroquinone2	= parseFloat($('#persen_hydroquinone2_neck2').val());
	var persen_methanol2	= parseFloat($('#persen_methanol2_neck2').val());
	
	// console.log(top_diameter);
	// console.log(panjang_neck_2);
	// console.log(topEST_neck1);
	// console.log(topEST_neck2);
	// console.log(waste);
	
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	var Luas_Area_RumusNeck2	= LuasAreaNeck2(top_diameter, panjang_neck_2, topEST_neck1, topEST_neck2, waste);
		$('#area_neck_2').val(RoundUpEST(Luas_Area_RumusNeck2));
	
	var HasilMadCsm			= (Luas_Area_RumusNeck2 * weight_matcsm2 * b)/1000;
	var Hasillayer21		= parseFloat(HasilMadCsm) * layer_resin21;
	var HasilMadCsmAdd		= (Luas_Area_RumusNeck2 * weight_csm_add2 * c)/1000; 
	var Hasillayer22		= parseFloat(HasilMadCsmAdd) * layer_resin22;
	var HasilWr				= (Luas_Area_RumusNeck2 * weight_wr2 * d)/1000;
	var Hasillayer23		= parseFloat(HasilWr) * layer_resin23;
	var HasilWrAdd			= (Luas_Area_RumusNeck2 * weight_wr_add2 * e)/1000;
	var Hasillayer24		= parseFloat(HasilWrAdd) * layer_resin24;
	
	// console.log(HasilMadCsm);
	
	
	var TotalResin2			= Hasillayer21 + Hasillayer22 + Hasillayer23 + Hasillayer24;
	
	if(TotalResin2 == '' || TotalResin2 == 0 || TotalResin2 == '0' || TotalResin2 == null){
		var Katalis2	= 0;
		var Sm2			= 0;
		var Coblat2		= 0;
		var Dma2		= 0;
		var Hyro2		= 0;
		var Methanol2	= 0;
	}
	else if(TotalResin2 > 0){
		var Katalis2	= 1;
		var Sm2			= 1;
		var Coblat2		= 0.6;
		var Dma2		= 0.4;
		var Hyro2		= 0.1;
		var Methanol2	= 0.9;
	}
	
	var HasilKatalis2	= Katalis2 * (persen_katalis2/100) * TotalResin2;
	var HasilSm2		= Sm2 * (persen_sm2/100) * TotalResin2;
	var HasilCoblat2	= Coblat2 * (persen_coblat2/100) * TotalResin2;
	var HasilDma2		= Dma2 * (persen_dma2/100) * TotalResin2;
	var HasilHydro2		= Hyro2 * (persen_hydroquinone2/100) * TotalResin2;
	var HasilMethanol2	= Methanol2 * (persen_methanol2/100) * TotalResin2;
	
	//Sampai Sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness2_neck2').val(Hasil);
	$('#est_neck_2').val(RoundUpEST(topEST_neck2));
	
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	
	//Hasil Perhitungan Hitam
	$('#hasil_matcsm2_neck2').val(RoundUp10(HasilMadCsm));
	$('#last_matcsm2_neck2').val(RoundUp(HasilMadCsm));
	$('#hasil_resin21_neck2').val(RoundUp10(Hasillayer21));
	$('#last_resin21_neck2').val(RoundUp(Hasillayer21));
	
	$('#hasil_csm_add2_neck2').val(RoundUp10(HasilMadCsmAdd));
	$('#last_csm_add2_neck2').val(RoundUp(HasilMadCsmAdd));
	$('#hasil_resin22_neck2').val(RoundUp10(Hasillayer22));
	$('#last_resin22_neck2').val(RoundUp(Hasillayer22));
	
	$('#hasil_wr2_neck2').val(RoundUp10(HasilWr));
	$('#last_wr2_neck2').val(RoundUp(HasilWr));
	$('#hasil_resin23_neck2').val(RoundUp10(Hasillayer23));
	$('#last_resin23_neck2').val(RoundUp(Hasillayer23));
	
	$('#hasil_wr_add2_neck2').val(RoundUp10(HasilWrAdd));
	$('#last_wr_add2_neck2').val(RoundUp(HasilWrAdd));
	$('#hasil_resin24_neck2').val(RoundUp10(Hasillayer24));
	$('#last_resin24_neck2').val(RoundUp(Hasillayer24));
	
	$('#hasil_resin_tot2_neck2').val(RoundUp10(TotalResin2));
	$('#last_resin_tot2_neck2').val(RoundUp(TotalResin2));
	
	$('#layer_katalis2_neck2').val(Katalis2);
	$('#hasil_katalis2_neck2').val(RoundUp10(HasilKatalis2));
	$('#last_katalis2_neck2').val(RoundUp(HasilKatalis2));
	
	$('#layer_sm2_neck2').val(Sm2);
	$('#hasil_sm2_neck2').val(RoundUp10(HasilSm2));
	$('#last_sm2_neck2').val(RoundUp(HasilSm2));
	
	$('#layer_coblat2_neck2').val(Coblat2);
	$('#hasil_coblat2_neck2').val(RoundUp10(HasilCoblat2));
	$('#last_cobalt2_neck2').val(RoundUp(HasilCoblat2));
	
	$('#layer_dma2_neck2').val(Dma2);
	$('#hasil_dma2_neck2').val(RoundUp10(HasilDma2));
	$('#last_dma2_neck2').val(RoundUp(HasilDma2));
	
	$('#layer_hydroquinone2_neck2').val(Hyro2);
	$('#hasil_hydroquinone2_neck2').val(RoundUp10(HasilHydro2));
	$('#last_hidro_neck2').val(RoundUp(HasilHydro2));
	
	$('#layer_methanol2_neck2').val(Methanol2);
	$('#hasil_methanol2_neck2').val(RoundUp10(HasilMethanol2));
	$('#last_methanol2_neck2').val(RoundUp(HasilMethanol2));
}

function AreaChange(a, b, c, d, e, top_diameter, top_thickness, waste, topEST, angle, flange_od, area_neck_1){
	var acuhan_1			= parseFloat($('#acuhan_1').val());
	var top_min_toleran		= parseFloat($('#top_min_toleran').val());
	var min_lin_thickness	= acuhan_1 - (acuhan_1*top_min_toleran);
	var max_lin_thickness	= acuhan_1 + (acuhan_1*top_min_toleran);
	
	$('#mix_lin_thickness').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness').val(max_lin_thickness.toFixed(4));
	
	var micron_plastic 		= parseFloat($('#micron_plastic').val());
	var layer_plastic		= parseFloat($('#layer_plastic').val());
	var weight_veil			= parseFloat($('#weight_veil').val());
	var layer_resin1		= parseFloat($('#layer_resin1').val());
	var weight_veil_add		= parseFloat($('#weight_veil_add').val());
	var layer_resin2		= parseFloat($('#layer_resin2').val());
	var weight_matcsm		= parseFloat($('#weight_matcsm').val());
	var layer_resin3		= parseFloat($('#layer_resin3').val());
	var weight_csm_add		= parseFloat($('#weight_csm_add').val());
	var layer_resin4		= parseFloat($('#layer_resin4').val());
	var layer_resin_tot		= parseFloat($('#layer_resin_tot').val());
	
	var persen_katalis		= parseFloat($('#persen_katalis').val());
	var persen_sm			= parseFloat($('#persen_sm').val());
	var persen_coblat		= parseFloat($('#persen_coblat').val());
	var persen_dma			= parseFloat($('#persen_dma').val());
	var persen_hydroquinone	= parseFloat($('#persen_hydroquinone').val());
	var persen_methanol		= parseFloat($('#persen_methanol').val());
	
	var perkalian = 1350;
	if(top_diameter < 25){
		var perkalian = 800;
	}
	
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	
	var HasilPlastic		= BeratPlastic(area_neck_1, Luas_Area_Rumus)
	
	var HasilVeil			= ((Luas_Area_Rumus + area_neck_1) * weight_veil * b)/1000;
	var Hasillayer_resin1	= parseFloat(HasilVeil) * layer_resin1;
	
	var HasilVeilAdd		= ((Luas_Area_Rumus + area_neck_1) * weight_veil_add * c)/1000;
	var Hasillayer_resin12	= parseFloat(HasilVeilAdd) * layer_resin2;
	
	var HasilMatCsm			= ((Luas_Area_Rumus + area_neck_1) * weight_matcsm * d)/1000;
	var Hasillayer_resin13	= parseFloat(HasilMatCsm) * layer_resin3;
	
	var HasilMatCsmAdd		= ((Luas_Area_Rumus + area_neck_1) * weight_csm_add * e)/1000;
	var Hasillayer_resin14	= parseFloat(HasilMatCsmAdd) * layer_resin4;
	
	var TotalResin			= (Luas_Area_Rumus* 1.2 *layer_resin_tot) + Hasillayer_resin14 + Hasillayer_resin13 + Hasillayer_resin12 + Hasillayer_resin1;
	
	if(TotalResin == '' || TotalResin == 0 || TotalResin == '0' || TotalResin == null){
		var Katalis	= 0;
		var Sm		= 0;
		var Coblat	= 0;
		var Dma		= 0;
		var Hyro	= 0;
		var Methanol= 0;
	}
	else if(TotalResin > 0){
		var Katalis	= 1;
		var Sm		= 1;
		var Coblat	= 0.6;
		var Dma		= 0.4;
		var Hyro	= 0.1;
		var Methanol= 0.9;
	}
	
	var HasilKatalis	= Katalis * (persen_katalis/100) * TotalResin;
	var HasilSm			= Sm * (persen_sm/100) * TotalResin;
	var HasilCoblat		= Coblat * (persen_coblat/100) * TotalResin;
	var HasilDma		= Dma * (persen_dma/100) * TotalResin;
	var HasilHydro		= Hyro * (persen_hydroquinone/100) * TotalResin;
	var HasilMethanol	= Methanol * (persen_methanol/100) * TotalResin;
	
	//TopCoat
	var ResinCoat		= parseFloat($("#resin41").val());
	
	var persen_katalis4	= parseFloat($("#persen_katalis4").val());
	var persen_color4	= parseFloat($("#persen_color4").val());
	var persen_tin4		= parseFloat($("#persen_tin4").val());
	var persen_chl4		= parseFloat($("#persen_chl4").val());
	var persen_stery4	= parseFloat($("#persen_stery4").val());
	var persen_wax4		= parseFloat($("#persen_wax4").val());
	var persen_mch4		= parseFloat($("#persen_mch4").val());
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2 * ResinCoat * 4);
	
	if(HasilTopCoat == '' || HasilTopCoat == 0 || HasilTopCoat == '0' || HasilTopCoat == null){
		var Katalis4	= 0;
		var Color4		= 0;
		var Tinuvin4	= 0;
		var Chlr4		= 0;
		var Stery4		= 0;
		var Wax4		= 0;
		var MetCh4		= 0;
		var Addv4		= 0;
	}
	else if(HasilTopCoat > 0){
		var Katalis4	= 1;
		var Color4		= 1;
		var Tinuvin4	= 0.1;
		var Chlr4		= 0.9;
		var Stery4		= 0.9;
		var Wax4		= 0.1;
		var MetCh4		= 1;
		var Addv4		= 1;
	}
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
	
	//Sampai Sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	$('#hasil_plastic').val(RoundUp10(HasilPlastic));
	$('#last_plastic').val(RoundUp(HasilPlastic));
	$('#hasil_veil').val(RoundUp10(HasilVeil));
	$('#last_veil').val(RoundUp(HasilVeil));
	$('#hasil_resin1').val(RoundUp10(Hasillayer_resin1));
	$('#last_resin1').val(RoundUp(Hasillayer_resin1));
	$('#hasil_veil_add').val(RoundUp10(HasilVeilAdd));
	$('#last_veil_add').val(RoundUp(HasilVeilAdd));
	$('#hasil_resin2').val(RoundUp10(Hasillayer_resin12));
	$('#last_resin2').val(RoundUp(Hasillayer_resin12));
	$('#hasil_matcsm').val(RoundUp10(HasilMatCsm));
	$('#last_matcsm').val(RoundUp(HasilMatCsm));
	$('#hasil_resin3').val(RoundUp10(Hasillayer_resin13));
	$('#last_resin3').val(RoundUp(Hasillayer_resin13));
	$('#hasil_csm_add').val(RoundUp10(HasilMatCsmAdd));
	$('#last_csm_add').val(RoundUp(HasilMatCsmAdd));
	$('#hasil_resin4').val(RoundUp10(Hasillayer_resin14));
	$('#last_resin4').val(RoundUp(Hasillayer_resin14));
	
	$('#hasil_resin_tot').val(TotalResin);
	$('#last_resin_tot').val(RoundUp(TotalResin));
	
	$('#layer_katalis').val(Katalis);
	$('#hasil_katalis').val(RoundUp10(HasilKatalis));
	$('#last_katalis').val(RoundUp(HasilKatalis));
	
	$('#layer_sm').val(Sm);
	$('#hasil_sm').val(RoundUp10(HasilSm));
	$('#last_sm').val(RoundUp(HasilSm));
	
	$('#layer_coblat').val(Coblat);
	$('#hasil_coblat').val(RoundUp10(HasilCoblat));
	$('#last_cobalt').val(RoundUp(HasilCoblat));
	
	$('#layer_dma').val(Dma);
	$('#hasil_dma').val(RoundUp10(HasilDma));
	$('#last_dma').val(RoundUp(HasilDma));
	
	$('#layer_hydroquinone').val(Hyro);
	$('#hasil_hydroquinone').val(RoundUp10(HasilHydro));
	$('#last_hidro').val(RoundUp(HasilHydro));
	
	$('#layer_methanol').val(Methanol);
	$('#hasil_methanol').val(RoundUp10(HasilMethanol));
	$('#last_methanol').val(RoundUp(HasilMethanol));
	
	//TopCoat
	$('#hasil_resin41').val(RoundUp10(HasilTopCoat));
	$('#last_resin41').val(RoundUp(HasilTopCoat));
	
	$('#layer_katalis4').val(Katalis4);
	$('#hasil_katalis4').val(RoundUp10(HasilKatalis4));
	$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	$('#layer_color4').val(Color4);
	$('#hasil_color4').val(RoundUp10(HasilColor4));
	$('#last_color4').val(RoundUp(HasilColor4));
	
	$('#layer_tin4').val(Tinuvin4);
	$('#hasil_tin4').val(RoundUp10(HasilTinuvin4));
	$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	$('#layer_chl4').val(Chlr4);
	$('#hasil_chl4').val(RoundUp10(HasilChlr4));
	$('#last_chl4').val(RoundUp(HasilChlr4));
	
	$('#layer_stery4').val(Stery4);
	$('#hasil_stery4').val(RoundUp10(HasilStery4));
	$('#last_stery4').val(RoundUp(HasilStery4));
	
	$('#layer_wax4').val(Wax4);
	$('#hasil_wax4').val(RoundUp10(HasilWax4));
	$('#last_wax4').val(RoundUp(HasilWax4));
	
	$('#layer_mch4').val(MetCh4);
	$('#hasil_mch4').val(RoundUp10(HasilMetCh4));
	$('#last_mch4').val(RoundUp(HasilMetCh4));
}

function AreaChange2(a, b, c, d, e, top_diameter, top_thickness, waste, topEST, angle, flange_od){
	var acuhan_2			= parseFloat($('#acuhan_2').val());
	var top_min_toleran		=  parseFloat($('#top_min_toleran').val());
	var min_lin_thickness	= acuhan_2 - (acuhan_2*top_min_toleran);
	var max_lin_thickness	= acuhan_2 + (acuhan_2*top_min_toleran);
	
	$('#mix_lin_thickness2').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness2').val(max_lin_thickness.toFixed(4));
	
	var topEST				= parseFloat($('#tot_lin_thickness').val()) + parseFloat($('#tot_lin_thickness2_neck1').val()) + parseFloat($('#tot_lin_thickness3').val());
	
	var weight_matcsm2		= parseFloat($('#weight_matcsm2').val());
	var layer_matcsm2		= parseFloat($('#layer_matcsm2').val());
	var weight_csm_add2		= parseFloat($('#weight_csm_add2').val());
	var layer_csm_add2		= parseFloat($('#layer_csm_add2').val());
	var weight_wr2			= parseFloat($('#weight_wr2').val());
	var layer_wr2			= parseFloat($('#layer_wr2').val());
	var weight_wr_add2		= parseFloat($('#weight_wr_add2').val());
	var layer_wr_add2		= parseFloat($('#layer_wr_add2').val());
	
	var layer_resin21		= parseFloat($('#layer_resin21').val());
	var layer_resin22		= parseFloat($('#layer_resin22').val());
	var layer_resin23		= parseFloat($('#layer_resin23').val());
	var layer_resin24		= parseFloat($('#layer_resin24').val());
	
	var persen_katalis2		= parseFloat($('#persen_katalis2').val());
	var persen_sm2			= parseFloat($('#persen_sm2').val());
	var persen_coblat2		= parseFloat($('#persen_coblat2').val());
	var persen_dma2			= parseFloat($('#persen_dma2').val());
	var persen_hydroquinone2	= parseFloat($('#persen_hydroquinone2').val());
	var persen_methanol2	= parseFloat($('#persen_methanol2').val());
	
	// var pangkat_od			= Math.pow(flange_od, 2);
	// var pangkat_dim			= Math.pow(top_diameter, 2);
	// var Luas_Area_Rumus		= 3.14/4 * (pangkat_od - pangkat_dim) / 1000000 * (1+waste));
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	
	var HasilMadCsm			= (Luas_Area_Rumus * weight_matcsm2 * b)/1000;
	var Hasillayer21		= parseFloat(HasilMadCsm) * layer_resin21;
	var HasilMadCsmAdd		= (Luas_Area_Rumus * weight_csm_add2 * c)/1000;
	var Hasillayer22		= parseFloat(HasilMadCsmAdd) * layer_resin22;
	var HasilWr				= (Luas_Area_Rumus * weight_wr2 * d)/1000;
	var Hasillayer23		= parseFloat(HasilWr) * layer_resin23;
	var HasilWrAdd			= (Luas_Area_Rumus * weight_wr_add2 * e)/1000;
	var Hasillayer24		= parseFloat(HasilWrAdd) * layer_resin24;
	
	var TotalResin2			= Hasillayer21 + Hasillayer22 + Hasillayer23 + Hasillayer24;
	
	if(TotalResin2 == '' || TotalResin2 == 0 || TotalResin2 == '0' || TotalResin2 == null){
		var Katalis2	= 0;
		var Sm2			= 0;
		var Coblat2		= 0;
		var Dma2		= 0;
		var Hyro2		= 0;
		var Methanol2	= 0;
	}
	else if(TotalResin2 > 0){
		var Katalis2	= 1;
		var Sm2			= 1;
		var Coblat2		= 0.6;
		var Dma2		= 0.4;
		var Hyro2		= 0.1;
		var Methanol2	= 0.9;
	}
	
	var HasilKatalis2	= Katalis2 * (persen_katalis2/100) * TotalResin2;
	var HasilSm2		= Sm2 * (persen_sm2/100) * TotalResin2;
	var HasilCoblat2	= Coblat2 * (persen_coblat2/100) * TotalResin2;
	var HasilDma2		= Dma2 * (persen_dma2/100) * TotalResin2;
	var HasilHydro2		= Hyro2 * (persen_hydroquinone2/100) * TotalResin2;
	var HasilMethanol2	= Methanol2 * (persen_methanol2/100) * TotalResin2;
	
	//TopCoat
	var ResinCoat		= parseFloat($("#resin41").val());
	
	var persen_katalis4	= parseFloat($("#persen_katalis4").val());
	var persen_color4	= parseFloat($("#persen_color4").val());
	var persen_tin4		= parseFloat($("#persen_tin4").val());
	var persen_chl4		= parseFloat($("#persen_chl4").val());
	var persen_stery4	= parseFloat($("#persen_stery4").val());
	var persen_wax4		= parseFloat($("#persen_wax4").val());
	var persen_mch4		= parseFloat($("#persen_mch4").val());
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2  * ResinCoat * 4);
	
	if(HasilTopCoat == '' || HasilTopCoat == 0 || HasilTopCoat == '0' || HasilTopCoat == null){
		var Katalis4	= 0;
		var Color4		= 0;
		var Tinuvin4	= 0;
		var Chlr4		= 0;
		var Stery4		= 0;
		var Wax4		= 0;
		var MetCh4		= 0;
		var Addv4		= 0;
	}
	else if(HasilTopCoat > 0){
		var Katalis4	= 1;
		var Color4		= 1;
		var Tinuvin4	= 0.1;
		var Chlr4		= 0.9;
		var Stery4		= 0.9;
		var Wax4		= 0.1;
		var MetCh4		= 1;
		var Addv4		= 1;
	}
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
	
	//Sampai Sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness2').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	
	//Hasil Perhitungan Hitam
	$('#hasil_matcsm2').val(RoundUp10(HasilMadCsm));
	$('#last_matcsm2').val(RoundUp(HasilMadCsm));
	$('#hasil_resin21').val(RoundUp10(Hasillayer21));
	$('#last_resin21').val(RoundUp(Hasillayer21));
	
	$('#hasil_csm_add2').val(RoundUp10(HasilMadCsmAdd));
	$('#last_csm_add2').val(RoundUp(HasilMadCsmAdd));
	$('#hasil_resin22').val(RoundUp10(Hasillayer22));
	$('#last_resin22').val(RoundUp(Hasillayer22));
	
	$('#hasil_wr2').val(RoundUp10(HasilWr));
	$('#last_wr2').val(RoundUp(HasilWr));
	$('#hasil_resin23').val(RoundUp10(Hasillayer23));
	$('#last_resin23').val(RoundUp(Hasillayer23));
	
	$('#hasil_wr_add2').val(RoundUp10(HasilWrAdd));
	$('#last_wr_add2').val(RoundUp(HasilWrAdd));
	$('#hasil_resin24').val(RoundUp10(Hasillayer24));
	$('#last_resin24').val(RoundUp(Hasillayer24));
	
	$('#hasil_resin_tot2').val(RoundUp10(TotalResin2));
	$('#last_resin_tot2').val(RoundUp(TotalResin2));
	
	$('#layer_katalis2').val(Katalis2);
	$('#hasil_katalis2').val(RoundUp10(HasilKatalis2));
	$('#last_katalis2').val(RoundUp(HasilKatalis2));
	
	$('#layer_sm2').val(Sm2);
	$('#hasil_sm2').val(RoundUp10(HasilSm2));
	$('#last_sm2').val(RoundUp(HasilSm2));
	
	$('#layer_coblat2').val(Coblat2);
	$('#hasil_coblat2').val(RoundUp10(HasilCoblat2));
	$('#last_cobalt2').val(RoundUp(HasilCoblat2));
	
	$('#layer_dma2').val(Dma2);
	$('#hasil_dma2').val(RoundUp10(HasilDma2));
	$('#last_dma2').val(RoundUp(HasilDma2));
	
	$('#layer_hydroquinone2').val(Hyro2);
	$('#hasil_hydroquinone2').val(RoundUp10(HasilHydro2));
	$('#last_hidro2').val(RoundUp(HasilHydro2));
	
	$('#layer_methanol2').val(Methanol2);
	$('#hasil_methanol2').val(RoundUp10(HasilMethanol2));
	$('#last_methanol2').val(RoundUp(HasilMethanol2));
	
	//TopCoat
	$('#hasil_resin41').val(RoundUp10(HasilTopCoat));
	$('#last_resin41').val(RoundUp(HasilTopCoat));
	
	$('#layer_katalis4').val(Katalis4);
	$('#hasil_katalis4').val(RoundUp10(HasilKatalis4));
	$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	$('#layer_color4').val(Color4);
	$('#hasil_color4').val(RoundUp10(HasilColor4));
	$('#last_color4').val(RoundUp(HasilColor4));
	
	$('#layer_tin4').val(Tinuvin4);
	$('#hasil_tin4').val(RoundUp10(HasilTinuvin4));
	$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	$('#layer_chl4').val(Chlr4);
	$('#hasil_chl4').val(RoundUp10(HasilChlr4));
	$('#last_chl4').val(RoundUp(HasilChlr4));
	
	$('#layer_stery4').val(Stery4);
	$('#hasil_stery4').val(RoundUp10(HasilStery4));
	$('#last_stery4').val(RoundUp(HasilStery4));
	
	$('#layer_wax4').val(Wax4);
	$('#hasil_wax4').val(RoundUp10(HasilWax4));
	$('#last_wax4').val(RoundUp(HasilWax4));
	
	$('#layer_mch4').val(MetCh4);
	$('#hasil_mch4').val(RoundUp10(HasilMetCh4));
	$('#last_mch4').val(RoundUp(HasilMetCh4));
}

function AreaChange3(a, b, c, d, e, top_diameter, top_thickness, waste, topEST, angle, flange_od){
	var acuhan_3			= parseFloat($('#acuhan_3').val());
	var top_min_toleran		=  parseFloat($('#top_min_toleran').val());
	var min_lin_thickness	= acuhan_3 - (acuhan_3*top_min_toleran);
	var max_lin_thickness	= acuhan_3 + (acuhan_3*top_min_toleran);
	
	$('#mix_lin_thickness3').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness3').val(max_lin_thickness.toFixed(4));
	
	var topEST				= parseFloat($('#tot_lin_thickness').val()) + parseFloat($('#tot_lin_thickness2').val()) + parseFloat(a);
	
	//perhitungan hitam
	var weight_veil3		= parseFloat($('#weight_veil3').val());
	var weight_veil_add3	= parseFloat($('#weight_veil_add3').val());
	var weight_matcsm3		= parseFloat($('#weight_matcsm3').val());
	var weight_csm_add3		= parseFloat($('#weight_csm_add3').val());
	
	var layer_resin31		= parseFloat($('#layer_resin31').val());
	var layer_resin32		= parseFloat($('#layer_resin32').val());
	var layer_resin33		= parseFloat($('#layer_resin33').val());
	var layer_resin34		= parseFloat($('#layer_resin34').val());
	
	var persen_katalis3		= parseFloat($('#persen_katalis3').val());
	var persen_sm3			= parseFloat($('#persen_sm3').val());
	var persen_coblat3		= parseFloat($('#persen_coblat3').val());
	var persen_dma3			= parseFloat($('#persen_dma3').val());
	var persen_hydroquinone3	= parseFloat($('#persen_hydroquinone3').val());
	var persen_methanol3	= parseFloat($('#persen_methanol3').val());
	
	// var pangkat_od			= Math.pow(flange_od, 2);
	// var pangkat_dim			= Math.pow(top_diameter, 2);
	// var Luas_Area_Rumus		= 3.14/4 * (pangkat_od - pangkat_dim) / 1000000 * (1+waste));
	var Luas_Area_Rumus 	= LuasArea(top_diameter, flange_od, waste);
	
	var HasilVeil3			= (Luas_Area_Rumus * weight_veil3 * b)/1000;
	var Hasillayer31		= parseFloat(HasilVeil3) * layer_resin31;
	var HasilVeilAdd3		= (Luas_Area_Rumus * weight_veil_add3 * c)/1000;
	var Hasillayer32		= parseFloat(HasilVeilAdd3) * layer_resin32;
	var HasilMadCsm3		= (Luas_Area_Rumus * weight_matcsm3 * d)/1000;
	var Hasillayer33		= parseFloat(HasilMadCsm3) * layer_resin33;
	var HasilMadCsmAdd3		= (Luas_Area_Rumus * weight_csm_add3 * e)/1000;
	var Hasillayer34		= parseFloat(HasilMadCsmAdd3) * layer_resin34;
	
	var TotalResin3			= Hasillayer31 + Hasillayer32 + Hasillayer33 + Hasillayer34;
	
	if(TotalResin3 == '' || TotalResin3 == 0 || TotalResin3 == '0' || TotalResin3 == null){
		var Katalis3	= 0;
		var Sm3			= 0;
		var Coblat3		= 0;
		var Dma3		= 0;
		var Hyro3		= 0;
		var Methanol3	= 0;
	}
	else if(TotalResin3 > 0){
		var Katalis3	= 1;
		var Sm3			= 1;
		var Coblat3		= 0.6;
		var Dma3		= 0.4;
		var Hyro3		= 0.1;
		var Methanol3	= 0.9;
	}
	
	var HasilKatalis3	= Katalis3 * (persen_katalis3/100) * TotalResin3;
	var HasilSm3		= Sm3 * (persen_sm3/100) * TotalResin3;
	var HasilCoblat3	= Coblat3 * (persen_coblat3/100) * TotalResin3;
	var HasilDma3		= Dma3 * (persen_dma3/100) * TotalResin3;
	var HasilHydro3		= Hyro3 * (persen_hydroquinone3/100) * TotalResin3;
	var HasilMethanol3	= Methanol3 * (persen_methanol3/100) * TotalResin3;
	
	//TopCoat
	var ResinCoat		= parseFloat($("#resin41").val());
	
	var persen_katalis4	= parseFloat($("#persen_katalis4").val());
	var persen_color4	= parseFloat($("#persen_color4").val());
	var persen_tin4		= parseFloat($("#persen_tin4").val());
	var persen_chl4		= parseFloat($("#persen_chl4").val());
	var persen_stery4	= parseFloat($("#persen_stery4").val());
	var persen_wax4		= parseFloat($("#persen_wax4").val());
	var persen_mch4		= parseFloat($("#persen_mch4").val());
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2 * ResinCoat * 4);
	
	if(HasilTopCoat == '' || HasilTopCoat == 0 || HasilTopCoat == '0' || HasilTopCoat == null){
		var Katalis4	= 0;
		var Color4		= 0;
		var Tinuvin4	= 0;
		var Chlr4		= 0;
		var Stery4		= 0;
		var Wax4		= 0;
		var MetCh4		= 0;
		var Addv4		= 0;
	}
	else if(HasilTopCoat > 0){
		var Katalis4	= 1;
		var Color4		= 1;
		var Tinuvin4	= 0.1;
		var Chlr4		= 0.9;
		var Stery4		= 0.9;
		var Wax4		= 0.1;
		var MetCh4		= 1;
		var Addv4		= 1;
	}
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
	
	//sampai sini
	if(a < min_lin_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_lin_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_lin_thickness && a < max_lin_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness3').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	$('#area').val(Luas_Area_Rumus.toFixed(6));
	
	//Penjumlahan Hitam
	$('#hasil_veil3').val(RoundUp10(HasilVeil3)); 
	$('#last_veil3').val(RoundUp(HasilVeil3));
	$('#hasil_resin31').val(RoundUp10(Hasillayer31));
	$('#last_resin31').val(RoundUp(Hasillayer31));
	
	$('#hasil_veil_add3').val(RoundUp10(HasilVeilAdd3));
	$('#last_veil_add3').val(RoundUp(HasilVeilAdd3));
	$('#hasil_resin32').val(RoundUp10(Hasillayer32));
	$('#last_resin32').val(RoundUp(Hasillayer32));
	
	$('#hasil_matcsm3').val(RoundUp10(HasilMadCsm3));
	$('#last_matcsm3').val(RoundUp(HasilMadCsm3));
	$('#hasil_resin33').val(RoundUp10(Hasillayer33));
	$('#last_resin33').val(RoundUp(Hasillayer33));
	
	$('#hasil_csm_add3').val(RoundUp10(HasilMadCsmAdd3));
	$('#last_csm_add3').val(RoundUp(HasilMadCsmAdd3));
	$('#hasil_resin34').val(RoundUp10(Hasillayer34));
	$('#last_resin34').val(RoundUp(Hasillayer34)); 
	
	$('#hasil_resin_tot3').val(RoundUp10(TotalResin3));
	$('#last_resin_tot3').val(RoundUp(TotalResin3));
	
	$('#layer_katalis3').val(Katalis3);
	$('#hasil_katalis3').val(RoundUp10(HasilKatalis3));
	$('#last_katalis3').val(RoundUp(HasilKatalis3));
	
	$('#layer_sm3').val(Sm3);
	$('#hasil_sm3').val(RoundUp10(HasilSm3));
	$('#last_sm3').val(RoundUp(HasilSm3));
	
	$('#layer_coblat3').val(Coblat3);
	$('#hasil_coblat3').val(RoundUp10(HasilCoblat3));
	$('#last_cobalt3').val(RoundUp(HasilCoblat3));
	
	$('#layer_dma3').val(Dma3);
	$('#hasil_dma3').val(RoundUp10(HasilDma3));
	$('#last_dma3').val(RoundUp(HasilDma3));
	
	$('#layer_hydroquinone3').val(Hyro3);
	$('#hasil_hydroquinone3').val(RoundUp10(HasilHydro3));
	$('#last_hidro3').val(RoundUp(HasilHydro3));
	
	$('#layer_methanol3').val(Methanol3);
	$('#hasil_methanol3').val(RoundUp10(HasilMethanol3));
	$('#last_methanol3').val(RoundUp(HasilMethanol3));
	
	//TopCoat
	$('#hasil_resin41').val(RoundUp10(HasilTopCoat));
	$('#last_resin41').val(RoundUp(HasilTopCoat));
	
	$('#layer_katalis4').val(Katalis4);
	$('#hasil_katalis4').val(RoundUp10(HasilKatalis4));
	$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	$('#layer_color4').val(Color4);
	$('#hasil_color4').val(RoundUp10(HasilColor4));
	$('#last_color4').val(RoundUp(HasilColor4));
	
	$('#layer_tin4').val(Tinuvin4);
	$('#hasil_tin4').val(RoundUp10(HasilTinuvin4));
	$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	$('#layer_chl4').val(Chlr4);
	$('#hasil_chl4').val(RoundUp10(HasilChlr4));
	$('#last_chl4').val(RoundUp(HasilChlr4));
	
	$('#layer_stery4').val(Stery4);
	$('#hasil_stery4').val(RoundUp10(HasilStery4));
	$('#last_stery4').val(RoundUp(HasilStery4));
	
	$('#layer_wax4').val(Wax4);
	$('#hasil_wax4').val(RoundUp10(HasilWax4));
	$('#last_wax4').val(RoundUp(HasilWax4));
	
	$('#layer_mch4').val(MetCh4);
	$('#hasil_mch4').val(RoundUp10(HasilMetCh4));
	$('#last_mch4').val(RoundUp(HasilMetCh4));
}


//Note: Penjumlahan thickness neck 2
function AreaChange2_neck1(diameter, thickness_neck1, waste, panjang_neck_1){
	var Luas_Area_Rumus	= LuasAreaNeck1(diameter, panjang_neck_1, thickness_neck1, waste);
		$('#area_neck_1').val(RoundUpEST(Luas_Area_Rumus));	
}

function AreaChange2_neck2(top_diameter, length_neck2, est_neck1, est_neck2, waste){
	var Luas_Area_Rumus	= LuasAreaNeck2(top_diameter, length_neck2, est_neck1, est_neck2, waste);
		$('#area_neck_1').val(RoundUpEST(Luas_Area_Rumus));	
}

function rubaharea(){
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	var flange_od		= parseFloat($('#flange_od').val());
	var area_neck_1		= parseFloat($('#area_neck_1').val());
	var topEST			= parseFloat($('#top_tebal_est').val());
	var acuhan_1		= parseFloat($('#acuhan_1').val());
	var hasilAch2		= top_thickness - acuhan_1;
	
	var est_neck1		= parseFloat($('#est_neck_1').val());
	var est_neck2 		= parseFloat($('#est_neck_2').val());
	
	var panjang_neck_1	= parseFloat($('#panjang_neck_1').val());
	var panjang_neck_2	= parseFloat($('#panjang_neck_2').val());

	$('#acuhan_2_neck1').val(hasilAch2.toFixed(1));
	$('#acuhan_2_neck2').val(hasilAch2.toFixed(1));
	
	//Liner Thickness
	var layer_veil1		= $("#layer_veil").val();
	var layer_veil2		= $("#layer_veil_add").val();
	var layer_veil3		= $("#layer_matcsm").val();
	var layer_veil4		= $("#layer_csm_add").val();
	var tot_thickness1	= parseFloat($('#tot_lin_thickness').val());
	
	//Struktur Thickness
	var layer1			= $("#layer_matcsm2").val();
	var layer2			= $("#layer_csm_add2").val();
	var layer3			= $("#layer_wr2").val();
	var layer4			= $("#layer_wr_add2").val();
	var tot_thickness2	= parseFloat($('#tot_lin_thickness2').val());
	
	//Struktur Thickness Neck 1
	var layer1_neck1			= $("#layer_matcsm2_neck1").val();
	var layer2_neck1			= $("#layer_csm_add2_neck1").val();
	var layer3_neck1			= $("#layer_wr2_neck1").val();
	var layer4_neck1			= $("#layer_wr_add2_neck1").val();
	var layer5_neck1			= $("#layer_rooving21_neck1").val();
	var layer6_neck1			= $("#layer_rooving22_neck1").val();
	var tot_thickness2_neck1	= parseFloat($('#tot_lin_thickness2_neck1').val());
	
	//External Thickness
	var layer31			= $("#layer_veil3").val();
	var layer32			= $("#layer_veil_add3").val();
	var layer33			= $("#layer_matcsm3").val();
	var layer34			= $("#layer_csm_add3").val();
	var tot_thickness3	= parseFloat($('#tot_lin_thickness3').val());
	
	AreaChange(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4, top_diameter, top_thickness, waste, topEST, angle, flange_od, area_neck_1);
	AreaChange2(tot_thickness2, layer1, layer2, layer3, layer4,top_diameter, top_thickness, waste, topEST, angle, flange_od, area_neck_1);
	AreaChange3(tot_thickness3, layer31, layer32, layer33, layer34, top_diameter, top_thickness, waste, topEST, angle, flange_od, area_neck_1);
	
	AreaChange2_neck1(top_diameter, tot_thickness2_neck1, waste, panjang_neck_1);
	AreaChange2_neck2(top_diameter, panjang_neck_2, est_neck1, est_neck2, waste);
	
}

function changeTop(){
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	var flange_od		= parseFloat($('#flange_od').val());
	var area_neck_1		= parseFloat($('#area_neck_1').val());
	var acuhan_1		= parseFloat($('#acuhan_1').val());
	var panjang_neck_1	= parseFloat($('#panjang_neck_1').val());	
	
	var panjang_neck_2	= top_thickness * 3;
	if(isNaN(panjang_neck_2)){
		var panjang_neck_2	= 0;
	}
	$('#panjang_neck_2').val(panjang_neck_2.toFixed(3));
	
	var hasilAch2		= top_thickness - acuhan_1;

	$('#acuhan_2_neck1').val(hasilAch2.toFixed(1));
	$('#acuhan_2_neck2').val(hasilAch2.toFixed(1));
	$('#acuhan_2').val(top_thickness);
	
	//Liner Thickness
	var layer_veil1		= $("#layer_veil").val();
	var layer_veil2		= $("#layer_veil_add").val();
	var layer_veil3		= $("#layer_matcsm").val();
	var layer_veil4		= $("#layer_csm_add").val();
	var tot_thickness1	= parseFloat($('#tot_lin_thickness').val());
	
	//Struktur Thickness
	var layer1			= $("#layer_matcsm2").val();
	var layer2			= $("#layer_csm_add2").val();
	var layer3			= $("#layer_wr2").val();
	var layer4			= $("#layer_wr_add2").val();
	var tot_thickness2	= parseFloat($('#tot_lin_thickness2').val());
	
	//Struktur Thickness Neck 1
	var layer1_neck1			= $("#layer_matcsm2_neck1").val();
	var layer2_neck1			= $("#layer_csm_add2_neck1").val();
	var layer3_neck1			= $("#layer_wr2_neck1").val();
	var layer4_neck1			= $("#layer_wr_add2_neck1").val();
	var layer5_neck1			= $("#layer_rooving21_neck1").val();
	var layer6_neck1			= $("#layer_rooving22_neck1").val();
	var tot_thickness2_neck1	= parseFloat($('#tot_lin_thickness2_neck1').val());
	
	//Struktur Thickness Neck 1
	var layer1_neck2			= $("#layer_matcsm2_neck2").val();
	var layer2_neck2			= $("#layer_csm_add2_neck2").val();
	var layer3_neck2			= $("#layer_wr2_neck2").val();
	var layer4_neck2			= $("#layer_wr_add2_neck2").val();
	var tot_thickness2_neck2	= parseFloat($('#tot_lin_thickness2_neck2').val());
	
	//External Thickness
	var layer31			= $("#layer_veil3").val();
	var layer32			= $("#layer_veil_add3").val();
	var layer33			= $("#layer_matcsm3").val();
	var layer34			= $("#layer_csm_add3").val();
	var tot_thickness3	= parseFloat($('#tot_lin_thickness3').val());
	
	Hasil(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1);
	Hasil2(tot_thickness2, layer1, layer2, layer3, layer4, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1);
	Hasil3(tot_thickness3, layer31, layer32, layer33, layer34, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1);
	Hasil2_neck1(tot_thickness2_neck1, layer1_neck1, layer2_neck1, layer3_neck1, layer4_neck1, layer5_neck1, layer6_neck1, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1);
	Hasil2_neck2(tot_thickness2_neck1, layer1_neck1, layer2_neck1, layer3_neck1, layer4_neck1, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1);
	
	rubaharea();
}

//hasil akhir thickness
function AcuhanMaxMin(liner, struktur, external, MinToleransi, MaxToleransi, totThickness1, totThickness2, totThickness3){
	var min_lin_thickness	= parseFloat(liner) - (parseFloat(liner) * parseFloat(MinToleransi));
	var max_lin_thickness	= parseFloat(liner) + (parseFloat(liner) * parseFloat(MaxToleransi));
	
	var min_str_thickness	= parseFloat(struktur) - (parseFloat(struktur) * parseFloat(MinToleransi));
	var max_str_thickness	= parseFloat(struktur) + (parseFloat(struktur) * parseFloat(MaxToleransi));
	
	var min_ext_thickness	= parseFloat(external) - (parseFloat(external) * parseFloat(MinToleransi));
	var max_ext_thickness	= parseFloat(external) + (parseFloat(external) * parseFloat(MaxToleransi));
	
	$('#mix_lin_thickness').val(min_lin_thickness.toFixed(4));
	$('#max_lin_thickness').val(max_lin_thickness.toFixed(4));
	
	if(totThickness1 < min_lin_thickness){
		var Hasil1	= "TOO LOW";
	}
	if(totThickness1 > max_lin_thickness){
		var Hasil1	= "TOO HIGH";
	}
	if(totThickness1 > min_lin_thickness && totThickness1 < max_lin_thickness){
		var Hasil1	= "OK";
	}
	$('#hasil_linier_thickness').val(Hasil1);
	
	$('#mix_lin_thickness2').val(min_str_thickness.toFixed(4));
	$('#max_lin_thickness2').val(max_str_thickness.toFixed(4));
	
	if(totThickness2 < min_str_thickness){
		var Hasil2	= "TOO LOW";
	}
	if(totThickness2 > max_str_thickness){
		var Hasil2	= "TOO HIGH";
	}
	if(totThickness2 > min_str_thickness && totThickness2 < max_str_thickness){
		var Hasil2	= "OK";
	}
	$('#hasil_linier_thickness2').val(Hasil2);
	
	$('#mix_lin_thickness3').val(min_ext_thickness.toFixed(4));
	$('#max_lin_thickness3').val(max_ext_thickness.toFixed(4));
	
	if(totThickness3 < min_ext_thickness){
		var Hasil3	= "TOO LOW";
	}
	if(totThickness3 > max_ext_thickness){
		var Hasil3	= "TOO HIGH";
	}
	if(totThickness3 > min_ext_thickness && totThickness3 < max_ext_thickness){
		var Hasil3	= "OK";
	}
	$('#hasil_linier_thickness3').val(Hasil3);
}

// topcoat
function topcoatLast(Luas_Area_Rumus){
	var ResinCoat		= parseFloat($("#resin41").val());
	var persen_katalis4	= parseFloat($("#persen_katalis4").val());
	var persen_color4	= parseFloat($("#persen_color4").val());
	var persen_tin4		= parseFloat($("#persen_tin4").val());
	var persen_chl4		= parseFloat($("#persen_chl4").val());
	var persen_stery4	= parseFloat($("#persen_stery4").val());
	var persen_wax4		= parseFloat($("#persen_wax4").val());
	var persen_mch4		= parseFloat($("#persen_mch4").val());
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2 * ResinCoat * 4);
		$('#hasil_resin41').val(RoundUp10(HasilTopCoat));
		$('#last_resin41').val(RoundUp(HasilTopCoat));
	
	if(HasilTopCoat == '' || HasilTopCoat == 0 || HasilTopCoat == '0' || HasilTopCoat == null){
		var Katalis4	= 0;
		var Color4		= 0;
		var Tinuvin4	= 0;
		var Chlr4		= 0;
		var Stery4		= 0;
		var Wax4		= 0;
		var MetCh4		= 0;
		var Addv4		= 0;
	}
	else if(HasilTopCoat > 0){
		var Katalis4	= 1;
		var Color4		= 1;
		var Tinuvin4	= 0.1;
		var Chlr4		= 0.9;
		var Stery4		= 0.9;
		var Wax4		= 0.1;
		var MetCh4		= 1;
		var Addv4		= 1;
	}
	
		$('#layer_katalis4').val(Katalis4);
		$('#layer_color4').val(Color4);
		$('#layer_tin4').val(Tinuvin4);
		$('#layer_chl4').val(Chlr4);
		$('#layer_stery4').val(Stery4);
		$('#layer_wax4').val(Wax4);
		$('#layer_mch4').val(MetCh4);
	
	var HasilKatalis4	= Katalis4 * (persen_katalis4/100) * HasilTopCoat;
		$('#hasil_katalis4').val(RoundUp10(HasilKatalis4));
		$('#last_katalis4').val(RoundUp(HasilKatalis4));
	
	var HasilColor4		= Color4 * (persen_color4/100) * HasilTopCoat;
		$('#hasil_color4').val(RoundUp10(HasilColor4));
		$('#last_color4').val(RoundUp(HasilColor4));
		
	var HasilTinuvin4	= Tinuvin4 * (persen_tin4/100) * HasilTopCoat;
		$('#hasil_tin4').val(RoundUp10(HasilTinuvin4));
		$('#last_tin4').val(RoundUp(HasilTinuvin4));
	
	var HasilChlr4		= Chlr4 * (persen_chl4/100) * HasilTopCoat;
		$('#hasil_chl4').val(RoundUp10(HasilChlr4));
		$('#last_chl4').val(RoundUp(HasilChlr4));
	
	var HasilStery4		= Stery4 * (persen_stery4/100) * HasilTopCoat;
		$('#hasil_stery4').val(RoundUp10(HasilStery4));
		$('#last_stery4').val(RoundUp(HasilStery4));
	
	var HasilWax4		= Wax4 * (persen_wax4/100) * HasilTopCoat;
		$('#hasil_wax4').val(RoundUp10(HasilWax4));
		$('#last_wax4').val(RoundUp(HasilWax4));
	
	var HasilMetCh4		= MetCh4 * (persen_mch4/100) * HasilTopCoat;
		$('#hasil_mch4').val(RoundUp10(HasilMetCh4));
		$('#last_mch4').val(RoundUp(HasilMetCh4));
}

// penentuan awal
function getNum(val) {
   if (isNaN(val)) {
     return 0;
   }
   return val;
}

function penentuAwal(){
	//BAGIAN ATAS
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	var flange_od		= parseFloat($('#flange_od').val());
	var area_neck_1		= parseFloat($('#area_neck_1').val());
	var panjang_neck_1	= parseFloat($('#panjang_neck_1').val());
	var panjang_neck_2	= parseFloat($('#panjang_neck_2').val());
	
	//STRUCTURE THICKNESS NECK 1
	var layer1_neck1			= parseFloat($("#layer_matcsm2_neck1").val());
	var layer2_neck1			= parseFloat($("#layer_csm_add2_neck1").val());
	var layer3_neck1			= parseFloat($("#layer_wr2_neck1").val());
	var layer4_neck1			= parseFloat($("#layer_wr_add2_neck1").val());
	var layer5_neck1			= parseFloat($("#layer_rooving21_neck1").val());
	var layer6_neck1			= parseFloat($("#layer_rooving22_neck1").val());
	
	var thickness1Val_neck1 	= parseFloat($('#thickness_matcsm2_neck1').val());
	var thickness2Val_neck1 	= parseFloat($('#thickness_csm_add2_neck1').val());
	var thickness3Val_neck1 	= parseFloat($('#thickness_wr2_neck1').val());
	var thickness4Val_neck1 	= parseFloat($('#thickness_wr_add2_neck1').val());
	var thickness5Val_neck1 	= parseFloat($('#thickness_rooving21_neck1').val());
	var thickness6Val_neck1 	= parseFloat($('#thickness_rooving22_neck1').val());
	
	var thickness1_neck1		= getNum(layer1_neck1) * getNum(thickness1Val_neck1);
	var thickness2_neck1		= getNum(layer2_neck1) * getNum(thickness2Val_neck1);
	var thickness3_neck1		= getNum(layer3_neck1) * getNum(thickness3Val_neck1);
	var thickness4_neck1		= getNum(layer4_neck1) * getNum(thickness4Val_neck1);
	var thickness5_neck1		= getNum(layer5_neck1) * getNum(thickness5Val_neck1);
	var thickness6_neck1		= getNum(layer6_neck1) * getNum(thickness6Val_neck1);
	var tot_thickness_neck1		= thickness1_neck1 + thickness2_neck1 + thickness3_neck1 + thickness4_neck1 + thickness5_neck1 + thickness6_neck1;
	
	//STRUCTURE THICKNESS NECK 2
	var layer1_neck2			= parseFloat($("#layer_matcsm2_neck2").val());
	var layer2_neck2			= parseFloat($("#layer_csm_add2_neck2").val());
	var layer3_neck2			= parseFloat($("#layer_wr2_neck2").val());
	var layer4_neck2			= parseFloat($("#layer_wr_add2_neck2").val());
	
	var thickness1Val_neck2 	= parseFloat($('#thickness_matcsm2_neck2').val());
	var thickness2Val_neck2 	= parseFloat($('#thickness_csm_add2_neck2').val());
	var thickness3Val_neck2 	= parseFloat($('#thickness_wr2_neck2').val());
	var thickness4Val_neck2 	= parseFloat($('#thickness_wr_add2_neck2').val());
	
	var thickness1_neck2		= getNum(layer1_neck2) * getNum(thickness1Val_neck2);
	var thickness2_neck2		= getNum(layer2_neck2) * getNum(thickness2Val_neck2);
	var thickness3_neck2		= getNum(layer3_neck2) * getNum(thickness3Val_neck2);
	var thickness4_neck2		= getNum(layer4_neck2) * getNum(thickness4Val_neck2);
	var tot_thickness_neck2		= thickness1_neck2 + thickness2_neck2 + thickness3_neck2 + thickness4_neck2;
	
	var dataTop = new Array();
		dataTop['waste']				= getNum(waste);
		dataTop['top_diameter']			= getNum(top_diameter);
		dataTop['top_thickness']		= getNum(top_thickness);
		dataTop['panjang_neck_1']		= getNum(panjang_neck_1);
		dataTop['panjang_neck_2']		= getNum(panjang_neck_2);
		dataTop['angle']				= getNum(angle);
		dataTop['flange_od']			= getNum(flange_od);
		dataTop['area_neck_1']			= getNum(area_neck_1);
		dataTop['layer1_neck1']			= getNum(layer1_neck1);
		dataTop['layer2_neck1']			= getNum(layer2_neck1);
		dataTop['layer3_neck1']			= getNum(layer3_neck1);
		dataTop['layer4_neck1']			= getNum(layer4_neck1);
		dataTop['layer5_neck1']			= getNum(layer5_neck1);
		dataTop['layer6_neck1']			= getNum(layer6_neck1);
		dataTop['thickness1_neck1']		= getNum(thickness1_neck1);
		dataTop['thickness2_neck1']		= getNum(thickness2_neck1);
		dataTop['thickness3_neck1']		= getNum(thickness3_neck1);
		dataTop['thickness4_neck1']		= getNum(thickness4_neck1);
		dataTop['thickness5_neck1']		= getNum(thickness5_neck1);
		dataTop['thickness6_neck1']		= getNum(thickness6_neck1);
		dataTop['tot_thickness_neck1']	= getNum(tot_thickness_neck1);
		
		dataTop['layer1_neck2']			= getNum(layer1_neck2);
		dataTop['layer2_neck2']			= getNum(layer2_neck2);
		dataTop['layer3_neck2']			= getNum(layer3_neck2);
		dataTop['layer4_neck2']			= getNum(layer4_neck2);
		dataTop['thickness1_neck2']		= getNum(thickness1_neck2);
		dataTop['thickness2_neck2']		= getNum(thickness2_neck2);
		dataTop['thickness3_neck2']		= getNum(thickness3_neck2);
		dataTop['thickness4_neck2']		= getNum(thickness4_neck2);
		dataTop['tot_thickness_neck2']	= getNum(tot_thickness_neck2);
	return dataTop;
}
