

//===============================================================================================================
	
	function LuasArea(){
	
		var waste			= parseFloat($('#waste').val()) / 100;
		var top_diameter	= parseFloat($('#diameter').val());
		var diameter2		= parseFloat($('#diameter2').val());
		var panjang			= parseFloat($('#panjang').val());
		
		var pangkat1			= Math.pow(panjang, 2);
		var help1				= (top_diameter - diameter2)/2;
		var pangkat2			= Math.pow(help1, 2);
		var help2				= pangkat1 + pangkat2;
		var pangkat3			= Math.pow(help2, 0.5);
		
		// console.log(pangkat3);
		// console.log(top_diameter);
		// console.log(diameter2);
		// console.log(waste);
		
		var Luas_Area_Rumus		= 3.14 * pangkat3 * ((top_diameter/2)+(diameter2/2)) / 1000000 * (1+waste);
		
		// console.log(Luas_Area_Rumus);
		if(isNaN(Luas_Area_Rumus)){var Luas_Area_Rumus = 0;}
		$("#area").val(Luas_Area_Rumus.toFixed(4));
		  
		return Luas_Area_Rumus;
	}

//===================================================END=========================================================
//===============================================================================================================

function Hasil(a, b, c, d, e, LinFakVeil, LinFakVeilAdd, LinFakCsm, LinFakCsmAdd){
	AcuhanMaxMin();
	
	var liner			= $('#acuhan_1').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val(); 
		
	var min_lin_thickness	= parseFloat(liner) - (parseFloat(liner) * parseFloat(MinToleransi));
	var max_lin_thickness	= parseFloat(liner) + (parseFloat(liner) * parseFloat(MaxToleransi));
	
	var topEST				= getNum($('#tot_lin_thickness2').val()) + getNum($('#tot_lin_thickness3').val()) + getNum(a);
	
	var micron_plastic 		= getNum($('#micron_plastic').val());
	var layer_plastic		= getNum($('#layer_plastic').val());
	var weight_veil			= getNum($('#weight_veil').val());
	var layer_resin1		= getNum($('#layer_resin1').val());
	var weight_veil_add		= getNum($('#weight_veil_add').val());
	var layer_resin2		= getNum($('#layer_resin2').val());
	var weight_matcsm		= getNum($('#weight_matcsm').val());
	var layer_resin3		= getNum($('#layer_resin3').val());
	var weight_csm_add		= getNum($('#weight_csm_add').val());
	var layer_resin4		= getNum($('#layer_resin4').val());
	var layer_resin_tot		= getNum($('#layer_resin_tot').val());
	
	var persen_katalis		= getNum($('#persen_katalis').val());
	var persen_sm			= getNum($('#persen_sm').val());
	var persen_coblat		= getNum($('#persen_coblat').val());
	var persen_dma			= getNum($('#persen_dma').val());
	var persen_hydroquinone	= getNum($('#persen_hydroquinone').val());
	var persen_methanol		= getNum($('#persen_methanol').val());
	var diameter			= getNum($('#diameter').val());
	
	var perkalian = 1350;
	if(diameter < 25){
		var perkalian = 800;
	}

	var Luas_Area_Rumus		= LuasArea();

	var HasilPlastic		= (Luas_Area_Rumus * micron_plastic * perkalian * layer_plastic);
	
	var HasilVeil			= ((Luas_Area_Rumus * weight_veil * b)/1000) * LinFakVeil;
	var Hasillayer_resin1	= getNum(HasilVeil) * layer_resin1;
	
	var HasilVeilAdd		= ((Luas_Area_Rumus * weight_veil_add * c)/1000) * LinFakVeilAdd;
	var Hasillayer_resin12	= getNum(HasilVeilAdd) * layer_resin2;
	
	var HasilMatCsm			= ((Luas_Area_Rumus * weight_matcsm * d)/1000) * LinFakCsm;
	var Hasillayer_resin13	= getNum(HasilMatCsm) * layer_resin3;
	
	var HasilMatCsmAdd		= ((Luas_Area_Rumus * weight_csm_add * e)/1000) * LinFakCsmAdd;
	var Hasillayer_resin14	= getNum(HasilMatCsmAdd) * layer_resin4;
	
	var TotalResin			= (Luas_Area_Rumus* 1.2 * layer_resin_tot) + Hasillayer_resin14 + Hasillayer_resin13 + Hasillayer_resin12 + Hasillayer_resin1;
	
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
	
	//Hasil Perhitungan Hitam
	$('#area').val(Luas_Area_Rumus);
	$('#hasil_plastic').val(HasilPlastic);
	$('#last_plastic').val(RoundUp(HasilPlastic));
	$('#hasil_veil').val(RoundUp4(HasilVeil));
	$('#last_veil').val(RoundUp(HasilVeil));
	$('#hasil_resin1').val(RoundUp4(Hasillayer_resin1));
	$('#last_resin1').val(RoundUp(Hasillayer_resin1));
	$('#hasil_veil_add').val(RoundUp4(HasilVeilAdd));
	$('#last_veil_add').val(RoundUp(HasilVeilAdd));
	$('#hasil_resin2').val(RoundUp4(Hasillayer_resin12));
	$('#last_resin2').val(RoundUp(Hasillayer_resin12));
	$('#hasil_matcsm').val(RoundUp4(HasilMatCsm));
	$('#last_matcsm').val(RoundUp(HasilMatCsm));
	$('#hasil_resin3').val(RoundUp4(Hasillayer_resin13));
	$('#last_resin3').val(RoundUp(Hasillayer_resin13));
	$('#hasil_csm_add').val(RoundUp4(HasilMatCsmAdd));
	$('#last_csm_add').val(RoundUp(HasilMatCsmAdd));
	$('#hasil_resin4').val(RoundUp4(Hasillayer_resin14));
	$('#last_resin4').val(RoundUp(Hasillayer_resin14));
	
	$('#hasil_resin_tot').val(TotalResin);
	$('#last_resin_tot').val(RoundUp(TotalResin));
	
	$('#layer_katalis').val(Katalis);
	$('#hasil_katalis').val(RoundUp4(HasilKatalis));
	$('#last_katalis').val(RoundUp(HasilKatalis));
	
	$('#layer_sm').val(Sm);
	$('#hasil_sm').val(RoundUp4(HasilSm));
	$('#last_sm').val(RoundUp(HasilSm));
	
	$('#layer_coblat').val(Coblat);
	$('#hasil_coblat').val(RoundUp4(HasilCoblat));
	$('#last_cobalt').val(RoundUp(HasilCoblat));
	
	$('#layer_dma').val(Dma);
	$('#hasil_dma').val(RoundUp4(HasilDma));
	$('#last_dma').val(RoundUp(HasilDma));
	
	$('#layer_hydroquinone').val(Hyro);
	$('#hasil_hydroquinone').val(RoundUp4(HasilHydro));
	$('#last_hidro').val(RoundUp(HasilHydro));
	
	$('#layer_methanol').val(Methanol);
	$('#hasil_methanol').val(RoundUp4(HasilMethanol));
	$('#last_methanol').val(RoundUp(HasilMethanol));
	
	topcoatLast();
}

function Hasil2(a, b, c, d, e, f, g, StrFakCsm, StrFakCsmAdd, StrFakWr, StrFakWrAdd, StrFakRv, StrFakRvAdd){
	AcuhanMaxMin();
	
	var struktur		= $('#acuhan_2').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val();
	
	var min_str_thickness	= parseFloat(struktur) - (parseFloat(struktur) * parseFloat(MinToleransi));
	var max_str_thickness	= parseFloat(struktur) + (parseFloat(struktur) * parseFloat(MaxToleransi));
	
	var topEST				= getNum($('#tot_lin_thickness').val()) + getNum($('#tot_lin_thickness3').val()) + getNum(a);
	
	var weight_matcsm2		= getNum($('#weight_matcsm2').val());
	var layer_matcsm2		= getNum($('#layer_matcsm2').val());
	var weight_csm_add2		= getNum($('#weight_csm_add2').val());
	var layer_csm_add2		= getNum($('#layer_csm_add2').val());
	var weight_wr2			= getNum($('#weight_wr2').val());
	var layer_wr2			= getNum($('#layer_wr2').val());
	var weight_wr_add2		= getNum($('#weight_wr_add2').val());
	var layer_wr_add2		= getNum($('#layer_wr_add2').val());
	
	var weight_rooving21	= getNum($('#weight_rooving21').val());
	var penggali_rooving21	= getNum($('#penggali_rooving21').val());
	var bw_rooving21		= getNum($('#bw_rooving21').val());
	var jumlah_rooving21	= getNum($('#jumlah_rooving21').val());
	
	var weight_rooving22	= getNum($('#weight_rooving22').val());
	var penggali_rooving22	= getNum($('#penggali_rooving22').val());
	var bw_rooving22		= getNum($('#bw_rooving22').val());
	var jumlah_rooving22	= getNum($('#jumlah_rooving22').val());
	
	var layer_resin21		= getNum($('#layer_resin21').val());
	var layer_resin22		= getNum($('#layer_resin22').val());
	var layer_resin23		= getNum($('#layer_resin23').val());
	var layer_resin24		= getNum($('#layer_resin24').val());
	var layer_resin25		= getNum($('#layer_resin25').val());
	var layer_resin26		= getNum($('#layer_resin26').val());
	var str_resin		= getNum($('#str_resin').val());
	
	var persen_katalis2		= getNum($('#persen_katalis2').val());
	var persen_sm2			= getNum($('#persen_sm2').val());
	var persen_coblat2		= getNum($('#persen_coblat2').val());
	var persen_dma2			= getNum($('#persen_dma2').val());
	var persen_hydroquinone2	= getNum($('#persen_hydroquinone2').val());
	var persen_methanol2	= getNum($('#persen_methanol2').val());
	var diameter			= getNum($('#diameter').val());
	
	var Luas_Area_Rumus		= LuasArea();
	
	var kali = 1; 
	if(diameter < 150){
		var kali = 1.3; 
	}
	
	var HasilMadCsm			= ((Luas_Area_Rumus * weight_matcsm2 * b)/1000) * StrFakCsm;
	var HasilMadCsmAdd		= ((Luas_Area_Rumus * weight_csm_add2 * c)/1000) * StrFakCsmAdd;
	var HasilWr				= ((Luas_Area_Rumus * weight_wr2 * d)/1000) * StrFakWr;
	var HasilWrAdd			= ((Luas_Area_Rumus * weight_wr_add2 * e)/1000) * StrFakWrAdd;
	var HasilRoof21			= ((weight_rooving21 * 0.001 * jumlah_rooving21 * 100)/(bw_rooving21/10)) * (2/1000) * f * Luas_Area_Rumus * StrFakRv;
	var HasilRoof22			= ((weight_rooving22 * 0.001 * jumlah_rooving22 * 100)/(bw_rooving22/10)) * (2/1000) * g * Luas_Area_Rumus * StrFakRvAdd;
	
	if(isNaN(HasilRoof21)){
		var HasilRoof21		= 0;
	}
		
	if(isNaN(HasilRoof22)){
		var HasilRoof22		= 0;
	}
	
	var Hasillayer21		= getNum(HasilMadCsm) * layer_resin21;
	var Hasillayer22		= getNum(HasilMadCsmAdd) * layer_resin22;
	var Hasillayer23		= getNum(HasilWr) * layer_resin23;
	var Hasillayer24		= getNum(HasilWrAdd) * layer_resin24;
	var Hasillayer25		= getNum(HasilRoof21) * layer_resin25 * kali;
	var Hasillayer26		= getNum(HasilRoof22) * layer_resin26 * kali;
	
	var TotalResin2			= str_resin * (Hasillayer21 + Hasillayer22 + Hasillayer23 + Hasillayer24  + Hasillayer25  + Hasillayer26);
	
	// console.log(TotalResin2);
	
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
	
	if(a < min_str_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_str_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_str_thickness && a < max_str_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness2').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	
	$('#area').val(Luas_Area_Rumus);
	
	//Hasil Perhitungan Hitam
	$('#hasil_matcsm2').val(RoundUp4(HasilMadCsm));
	$('#last_matcsm2').val(RoundUp(HasilMadCsm));
	$('#hasil_resin21').val(RoundUp4(Hasillayer21));
	$('#last_resin21').val(RoundUp(Hasillayer21));
	
	$('#hasil_csm_add2').val(RoundUp4(HasilMadCsmAdd));
	$('#last_csm_add2').val(RoundUp(HasilMadCsmAdd));
	$('#hasil_resin22').val(RoundUp4(Hasillayer22));
	$('#last_resin22').val(RoundUp(Hasillayer22));
	
	$('#hasil_wr2').val(RoundUp4(HasilWr));
	$('#last_wr2').val(RoundUp(HasilWr));
	$('#hasil_resin23').val(RoundUp4(Hasillayer23));
	$('#last_resin23').val(RoundUp(Hasillayer23));
	
	$('#hasil_wr_add2').val(RoundUp4(HasilWrAdd));
	$('#last_wr_add2').val(RoundUp(HasilWrAdd));
	$('#hasil_resin24').val(RoundUp4(Hasillayer24));
	$('#last_resin24').val(RoundUp(Hasillayer24));
	
	
	$('#hasil_rooving21').val(RoundUp4(HasilRoof21));
	$('#last_rooving21').val(RoundUp(HasilRoof21));
	$('#hasil_resin25').val(RoundUp4(Hasillayer25));
	$('#last_resin25').val(RoundUp(Hasillayer25));
	
	$('#hasil_rooving22').val(RoundUp4(HasilRoof22));
	$('#last_rooving22').val(RoundUp(HasilRoof22));
	$('#hasil_resin26').val(RoundUp4(Hasillayer26));
	$('#last_resin26').val(RoundUp(Hasillayer26));
	
	$('#hasil_resin_tot2').val(RoundUp4(TotalResin2));
	$('#last_resin_tot2').val(RoundUp(TotalResin2));
	
	$('#layer_katalis2').val(Katalis2);
	$('#hasil_katalis2').val(RoundUp4(HasilKatalis2));
	$('#last_katalis2').val(RoundUp(HasilKatalis2));
	
	$('#layer_sm2').val(Sm2);
	$('#hasil_sm2').val(RoundUp4(HasilSm2));
	$('#last_sm2').val(RoundUp(HasilSm2));
	
	$('#layer_coblat2').val(Coblat2);
	$('#hasil_coblat2').val(RoundUp4(HasilCoblat2));
	$('#last_cobalt2').val(RoundUp(HasilCoblat2));
	
	$('#layer_dma2').val(Dma2);
	$('#hasil_dma2').val(RoundUp4(HasilDma2));
	$('#last_dma2').val(RoundUp(HasilDma2));
	
	$('#layer_hydroquinone2').val(Hyro2);
	$('#hasil_hydroquinone2').val(RoundUp4(HasilHydro2));
	$('#last_hidro2').val(RoundUp(HasilHydro2));
	
	$('#layer_methanol2').val(Methanol2);
	$('#hasil_methanol2').val(RoundUp4(HasilMethanol2));
	$('#last_methanol2').val(RoundUp(HasilMethanol2));
	
	topcoatLast();
}

function Hasil3(a, b, c, d, e, EksFakVeil, EksFakVeilAdd, EksFakCsm, EksFakCsmAdd){
	AcuhanMaxMin();
	
	var external		= $('#acuhan_3').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val();
	
	var min_ext_thickness	= parseFloat(external) - (parseFloat(external) * parseFloat(MinToleransi));
	var max_ext_thickness	= parseFloat(external) + (parseFloat(external) * parseFloat(MaxToleransi));
	
	var topEST				= getNum($('#tot_lin_thickness').val()) + getNum($('#tot_lin_thickness2').val()) + getNum(a);
	
	var weight_veil3		= getNum($('#weight_veil3').val());
	var weight_veil_add3	= getNum($('#weight_veil_add3').val());
	var weight_matcsm3		= getNum($('#weight_matcsm3').val());
	var weight_csm_add3		= getNum($('#weight_csm_add3').val());
	
	var layer_resin31		= getNum($('#layer_resin31').val());
	var layer_resin32		= getNum($('#layer_resin32').val());
	var layer_resin33		= getNum($('#layer_resin33').val());
	var layer_resin34		= getNum($('#layer_resin34').val());
	var eks_resin		= getNum($('#eks_resin').val());
	
	var persen_katalis3		= getNum($('#persen_katalis3').val()); 
	var persen_sm3			= getNum($('#persen_sm3').val());
	var persen_coblat3		= getNum($('#persen_coblat3').val());
	var persen_dma3			= getNum($('#persen_dma3').val());
	var persen_hydroquinone3	= getNum($('#persen_hydroquinone3').val());
	var persen_methanol3	= getNum($('#persen_methanol3').val());
	
	var Luas_Area_Rumus		= LuasArea();
	
	var HasilVeil3			= ((Luas_Area_Rumus * weight_veil3 * b)/1000) * EksFakVeil;
	var HasilVeilAdd3		= ((Luas_Area_Rumus * weight_veil_add3 * c)/1000) * EksFakVeilAdd;
	var HasilMadCsm3		= ((Luas_Area_Rumus * weight_matcsm3 * d)/1000) * EksFakCsm;
	var HasilMadCsmAdd3		= ((Luas_Area_Rumus * weight_csm_add3 * e)/1000) * EksFakCsmAdd;
	
	
	var Hasillayer31		= getNum(HasilVeil3) * layer_resin31;
	var Hasillayer32		= getNum(HasilVeilAdd3) * layer_resin32;
	var Hasillayer33		= getNum(HasilMadCsm3) * layer_resin33;
	var Hasillayer34		= getNum(HasilMadCsmAdd3) * layer_resin34;
	
	var TotalResin3			= eks_resin * (Hasillayer31 + Hasillayer32 + Hasillayer33 + Hasillayer34);
	
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
	
	if(a < min_ext_thickness){
		var Hasil	= "TOO LOW";
	}
	if(a > max_ext_thickness){
		var Hasil	= "TOO HIGH";
	}
	if(a > min_ext_thickness && a < max_ext_thickness){
		var Hasil	= "OK";
	}
	$('#hasil_linier_thickness3').val(Hasil);
	$('#top_tebal_est').val(RoundUpEST(topEST));
	
	$('#area').val(Luas_Area_Rumus);
	//Penjumlahan Hitam
	$('#hasil_veil3').val(RoundUp4(HasilVeil3)); 
	$('#last_veil3').val(RoundUp(HasilVeil3));
	$('#hasil_resin31').val(RoundUp4(Hasillayer31));
	$('#last_resin31').val(RoundUp(Hasillayer31));
	
	$('#hasil_veil_add3').val(RoundUp4(HasilVeilAdd3));
	$('#last_veil_add3').val(RoundUp(HasilVeilAdd3));
	$('#hasil_resin32').val(RoundUp4(Hasillayer32));
	$('#last_resin32').val(RoundUp(Hasillayer32));
	
	$('#hasil_matcsm3').val(RoundUp4(HasilMadCsm3));
	$('#last_matcsm3').val(RoundUp(HasilMadCsm3));
	$('#hasil_resin33').val(RoundUp4(Hasillayer33));
	$('#last_resin33').val(RoundUp(Hasillayer33));
	
	$('#hasil_csm_add3').val(RoundUp4(HasilMadCsmAdd3));
	$('#last_csm_add3').val(RoundUp(HasilMadCsmAdd3));
	$('#hasil_resin34').val(RoundUp4(Hasillayer34));
	$('#last_resin34').val(RoundUp(Hasillayer34)); 
	
	$('#hasil_resin_tot3').val(RoundUp4(TotalResin3));
	$('#last_resin_tot3').val(RoundUp(TotalResin3));
	
	$('#layer_katalis3').val(Katalis3);
	$('#hasil_katalis3').val(RoundUp4(HasilKatalis3));
	$('#last_katalis3').val(RoundUp(HasilKatalis3));
	
	$('#layer_sm3').val(Sm3);
	$('#hasil_sm3').val(RoundUp4(HasilSm3));
	$('#last_sm3').val(RoundUp(HasilSm3));
	
	$('#layer_coblat3').val(Coblat3);
	$('#hasil_coblat3').val(RoundUp4(HasilCoblat3));
	$('#last_cobalt3').val(RoundUp(HasilCoblat3));
	
	$('#layer_dma3').val(Dma3);
	$('#hasil_dma3').val(RoundUp4(HasilDma3));
	$('#last_dma3').val(RoundUp(HasilDma3));
	
	$('#layer_hydroquinone3').val(Hyro3);
	$('#hasil_hydroquinone3').val(RoundUp4(HasilHydro3));
	$('#last_hidro3').val(RoundUp(HasilHydro3));
	
	$('#layer_methanol3').val(Methanol3);
	$('#hasil_methanol3').val(RoundUp4(HasilMethanol3));
	$('#last_methanol3').val(RoundUp(HasilMethanol3));
	
	topcoatLast();
}

function changeTop(){
	var waste			= getNum($('#waste').val());
	var top_diameter	= getNum($('#diameter').val());
	var top_thickness	= getNum($('#top_tebal_design').val());
	var topEST			= getNum($('#tot_lin_thickness').val()) + getNum($('#tot_lin_thickness2').val()) + getNum($('#tot_lin_thickness3').val());
	var acuhan_1		= getNum($('#acuhan_1').val());
	var acuhan_3		= getNum($('#acuhan_3').val());
	
	var hasilAch2		= top_thickness - (acuhan_1 + acuhan_3);
	$('#acuhan_2').val(hasilAch2.toFixed(2));
	//Liner Thickness
	var layer_veil1		= getNum($("#layer_veil").val());
	var layer_veil2		= getNum($("#layer_veil_add").val());
	var layer_veil3		= getNum($("#layer_matcsm").val());
	var layer_veil4		= getNum($("#layer_csm_add").val());
	var tot_thickness1	= getNum($('#tot_lin_thickness').val());
	
	//Struktur Thickness
	var layer1			= getNum($("#layer_matcsm2").val());
	var layer2			= getNum($("#layer_csm_add2").val());
	var layer3			= getNum($("#layer_wr2").val());
	var layer4			= getNum($("#layer_wr_add2").val());
	var layer5			= getNum($("#layer_rooving21").val());
	var layer6			= getNum($("#layer_rooving22").val());
	var tot_thickness2	= getNum($('#tot_lin_thickness2').val());
	
	//External Thickness
	var layer31			= getNum($("#layer_veil3").val());
	var layer32			= getNum($("#layer_veil_add3").val());
	var layer33			= getNum($("#layer_matcsm3").val());
	var layer34			= getNum($("#layer_csm_add3").val());
	var tot_thickness3	= getNum($('#tot_lin_thickness3').val());
	
	
	var LinFakVeil		= getNum($("#lin_faktor_veil").val());
	var LinFakVeilAdd	= getNum($("#lin_faktor_veil_add").val());
	var LinFakCsm		= getNum($("#lin_faktor_csm").val());
	var LinFakCsmAdd		= getNum($("#lin_faktor_csm_add").val());
	
	var StrFakCsm		= getNum($("#str_faktor_csm").val());
	var StrFakCsmAdd		= getNum($("#str_faktor_csm_add").val());
	var StrFakWr		= getNum($("#str_faktor_wr").val());
	var StrFakWrAdd		= getNum($("#str_faktor_wr_add").val());
	var StrFakRv		= getNum($("#str_faktor_rv").val());
	var StrFakRvAdd		= getNum($("#str_faktor_rv_add").val());
	
	var EksFakVeil		= getNum($("#eks_faktor_veil").val());
	var EksFakVeilAdd		= getNum($("#eks_faktor_veil_add").val());
	var EksFakCsm		= getNum($("#eks_faktor_csm").val());
	var EksFakCsmAdd		= getNum($("#eks_faktor_csm_add").val());
	
	Hasil(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4, LinFakVeil, LinFakVeilAdd, LinFakCsm, LinFakCsmAdd);
	Hasil2(tot_thickness2, layer1, layer2, layer3, layer4, layer5, layer6, StrFakCsm, StrFakCsmAdd, StrFakWr, StrFakWrAdd, StrFakRv, StrFakRvAdd);
	Hasil3(tot_thickness3, layer31, layer32, layer33, layer34, EksFakVeil, EksFakVeilAdd, EksFakCsm, EksFakCsmAdd);
}

function getBw(bw_rooving, jumlah_rooving){ 
	var waste			= getNum($('#waste').val());
	var top_diameter	= getNum($('#diameter').val());
	var top_thickness	= getNum($('#top_tebal_design').val());
	var topEST			= getNum($('#tot_lin_thickness').val()) + getNum($('#tot_lin_thickness2').val()) + getNum($('#tot_lin_thickness3').val());
	var acuhan_1		= getNum($('#acuhan_1').val());
	var acuhan_3		= getNum($('#acuhan_3').val());
	
	var hasilAch2		= top_thickness - (acuhan_1 + acuhan_3);
	$('#acuhan_2').val(hasilAch2.toFixed(2));
	//Liner Thickness
	var layer_veil1		= getNum($("#layer_veil").val());
	var layer_veil2		= getNum($("#layer_veil_add").val());
	var layer_veil3		= getNum($("#layer_matcsm").val());
	var layer_veil4		= getNum($("#layer_csm_add").val());
	var tot_thickness1	= getNum($('#tot_lin_thickness').val());
	
	//Struktur Thickness
	var layer1			= getNum($("#layer_matcsm2").val());
	var layer2			= getNum($("#layer_csm_add2").val());
	var layer3			= getNum($("#layer_wr2").val());
	var layer4			= getNum($("#layer_wr_add2").val());
	var layer5			= getNum($("#layer_rooving21").val());
	var layer6			= getNum($("#layer_rooving22").val());
	var tot_thickness2	= getNum($('#tot_lin_thickness2').val());
	
	//External Thickness
	var layer31			= getNum($("#layer_veil3").val());
	var layer32			= getNum($("#layer_veil_add3").val());
	var layer33			= getNum($("#layer_matcsm3").val());
	var layer34			= getNum($("#layer_csm_add3").val());
	var tot_thickness3	= getNum($('#tot_lin_thickness3').val());
	
	
	var LinFakVeil		= getNum($("#lin_faktor_veil").val());
	var LinFakVeilAdd	= getNum($("#lin_faktor_veil_add").val());
	var LinFakCsm		= getNum($("#lin_faktor_csm").val());
	var LinFakCsmAdd		= getNum($("#lin_faktor_csm_add").val());
	
	var StrFakCsm		= getNum($("#str_faktor_csm").val());
	var StrFakCsmAdd		= getNum($("#str_faktor_csm_add").val());
	var StrFakWr		= getNum($("#str_faktor_wr").val());
	var StrFakWrAdd		= getNum($("#str_faktor_wr_add").val());
	var StrFakRv		= getNum($("#str_faktor_rv").val());
	var StrFakRvAdd		= getNum($("#str_faktor_rv_add").val());
	
	var EksFakVeil		= getNum($("#eks_faktor_veil").val());
	var EksFakVeilAdd		= getNum($("#eks_faktor_veil_add").val());
	var EksFakCsm		= getNum($("#eks_faktor_csm").val());
	var EksFakCsmAdd		= getNum($("#eks_faktor_csm_add").val());
	
	Hasil(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4, LinFakVeil, LinFakVeilAdd, LinFakCsm, LinFakCsmAdd);
	Hasil2(tot_thickness2, layer1, layer2, layer3, layer4, layer5, layer6, StrFakCsm, StrFakCsmAdd, StrFakWr, StrFakWrAdd, StrFakRv, StrFakRvAdd);
	Hasil3(tot_thickness3, layer31, layer32, layer33, layer34, EksFakVeil, EksFakVeilAdd, EksFakCsm, EksFakCsmAdd);
}

//hasil akhir thickness
function AcuhanMaxMin(){
	var liner			= $('#acuhan_1').val();
	var struktur		= $('#acuhan_2').val();
	var external		= $('#acuhan_3').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val();
	var totThickness1	= $('#tot_lin_thickness').val();
	var totThickness2	= $('#tot_lin_thickness2').val();
	var totThickness3	= $('#tot_lin_thickness3').val(); 
		
	var min_lin_thickness	= getNum(liner) - (getNum(liner) * getNum(MinToleransi));
	var max_lin_thickness	= getNum(liner) + (getNum(liner) * getNum(MaxToleransi));
	
	var min_str_thickness	= getNum(struktur) - (getNum(struktur) * getNum(MinToleransi));
	var max_str_thickness	= getNum(struktur) + (getNum(struktur) * getNum(MaxToleransi));
	
	var min_ext_thickness	= getNum(external) - (getNum(external) * getNum(MinToleransi));
	var max_ext_thickness	= getNum(external) + (getNum(external) * getNum(MaxToleransi));
	
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
function topcoatLast(){
	var ResinCoat		= getNum($("#resin41").val());
	var persen_katalis4	= getNum($("#persen_katalis4").val());
	var persen_color4	= getNum($("#persen_color4").val());
	var persen_tin4		= getNum($("#persen_tin4").val());
	var persen_chl4		= getNum($("#persen_chl4").val());
	var persen_stery4	= getNum($("#persen_stery4").val());
	var persen_wax4		= getNum($("#persen_wax4").val());
	var persen_mch4		= getNum($("#persen_mch4").val());
	var persen_coblat4	= getNum($("#persen_coblat4").val());
	var persen_dma4		= getNum($("#persen_dma4").val());

	var Luas_Area_Rumus		= LuasArea();
	
	var HasilTopCoat	= (Luas_Area_Rumus * 1.2 *ResinCoat);
	
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
		var Coblat4		= 0;
		var Dma4		= 0;
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
		var Coblat4		= 0.6;
		var Dma4		= 0.4;
	}
	
		$('#layer_katalis4').val(Katalis4);
		$('#layer_color4').val(Color4);
		$('#layer_tin4').val(Tinuvin4);
		$('#layer_chl4').val(Chlr4);
		$('#layer_stery4').val(Stery4);
		$('#layer_wax4').val(Wax4);
		$('#layer_mch4').val(MetCh4);
		$('#layer_coblat4').val(Coblat4);
		$('#layer_dma4').val(Dma4);
	
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

		var HasilCoblat4		= Coblat4 * (persen_coblat4/100) * HasilTopCoat;
		$('#hasil_coblat4').val(RoundUp10(HasilCoblat4));
		$('#last_cobalt4').val(RoundUp(HasilCoblat4));

	var HasilDma4		= Dma4 * (persen_dma4/100) * HasilTopCoat;
		$('#hasil_dma4').val(RoundUp10(HasilDma4));
		$('#last_dma4').val(RoundUp(HasilDma4));
}

// penentuan awal
function getNum(val) {
   if (isNaN(val) || val == '') {
     return 0;
   }
   return parseFloat(val);
}

function linerLayer(){
	var layer_veil			= getNum($('#layer_veil').val());
	var thickness_veil 		= getNum($('#thickness_veil').val());
	var thickness1			= layer_veil * thickness_veil;
	
	var layer_veil_add		= getNum($('#layer_veil_add').val());
	var thickness_veil_add 	= getNum($('#thickness_veil_add').val());
	var thickness2			= layer_veil_add * thickness_veil_add;
	
	var layer_matcsm		= getNum($('#layer_matcsm').val());
	var thickness_matcsm 	= getNum($('#thickness_matcsm').val());
	var thickness3			= layer_matcsm * thickness_matcsm;
	
	var layer_csm_add		= getNum($('#layer_csm_add').val());
	var thickness_csm_add 	= getNum($('#thickness_csm_add').val());
	var thickness4			= layer_csm_add * thickness_csm_add;

	var tot_thick_lin		=  getNum(thickness1) +  getNum(thickness2) +  getNum(thickness3) +  getNum(thickness4);
	
	$('#totthick_veil').val(getNum(thickness1).toFixed(4));
	$('#totthick_veil_add').val(getNum(thickness2).toFixed(4));
	$('#totthick_matcsm').val(getNum(thickness3).toFixed(4));
	$('#totthick_csm_add').val(getNum(thickness4).toFixed(4));
	
	$('#tot_lin_thickness').val(tot_thick_lin.toFixed(4));
}

function structureLayer(){
	var layer_matcsm2		= getNum($('#layer_matcsm2').val());
	var thickness_matcsm2 	= getNum($('#thickness_matcsm2').val());
	var thickness1			= layer_matcsm2 * thickness_matcsm2;
	
	var layer_csm_add2		= getNum($('#layer_csm_add2').val());
	var thickness_csm_add2 	= getNum($('#thickness_csm_add2').val());
	var thickness2			= layer_csm_add2 * thickness_csm_add2;
	
	var layer_wr2			= getNum($('#layer_wr2').val());
	var thickness_wr2 		= getNum($('#thickness_wr2').val());
	var thickness3			= layer_wr2 * thickness_wr2;
	
	var layer_wr_add2		= getNum($('#layer_wr_add2').val());
	var thickness_wr_add2 	= getNum($('#thickness_wr_add2').val());
	var thickness4			= layer_wr_add2 * thickness_wr_add2;
	
	var layer5				= parseFloat($("#layer_rooving21").val());
	var layer6				= parseFloat($("#layer_rooving22").val());
	
	var thickness5Val		= parseFloat($('#thickness_rooving21').val());
	var thickness6Val		= parseFloat($('#thickness_rooving22').val());
	
	var thickness5			= getNum(layer5) * getNum(thickness5Val);
	var thickness6			= getNum(layer6) * getNum(thickness6Val);

	var tot_thick_str		=  getNum(thickness1) +  getNum(thickness2) +  getNum(thickness3) +  getNum(thickness4) +  getNum(thickness5) +  getNum(thickness6);
	
	$('#totthick_matcsm2').val(getNum(thickness1).toFixed(4));
	$('#totthick_csm_add2').val(getNum(thickness2).toFixed(4));
	$('#totthick_wr2').val(getNum(thickness3).toFixed(4));
	$('#totthick_wr_add2').val(getNum(thickness4).toFixed(4));
	$('#totthick_rooving21').val(getNum(thickness5).toFixed(4));
	$('#totthick_rooving22').val(getNum(thickness6).toFixed(4));
	
	$('#tot_lin_thickness2').val(tot_thick_str.toFixed(4));
}

function eksternalLayer(){
	var layer_veil			= getNum($('#layer_veil3').val());
	var thickness_veil 		= getNum($('#thickness_veil3').val());
	var thickness1			= layer_veil * thickness_veil;
	
	var layer_veil_add		= getNum($('#layer_veil_add3').val());
	var thickness_veil_add 	= getNum($('#thickness_veil_add3').val());
	var thickness2			= layer_veil_add * thickness_veil_add;
	
	var layer_matcsm		= getNum($('#layer_matcsm3').val());
	var thickness_matcsm 	= getNum($('#thickness_matcsm3').val());
	var thickness3			= layer_matcsm * thickness_matcsm;
	
	var layer_csm_add		= getNum($('#layer_csm_add3').val());
	var thickness_csm_add 	= getNum($('#thickness_csm_add3').val());
	var thickness4			= layer_csm_add * thickness_csm_add;

	var tot_thick_lin		=  getNum(thickness1) +  getNum(thickness2) +  getNum(thickness3) +  getNum(thickness4);
	
	$('#totthick_veil3').val(getNum(thickness1).toFixed(4));
	$('#totthick_veil_add3').val(getNum(thickness2).toFixed(4));
	$('#totthick_matcsm3').val(getNum(thickness3).toFixed(4));
	$('#totthick_csm_add3').val(getNum(thickness4).toFixed(4));
	
	$('#tot_lin_thickness3').val(tot_thick_lin.toFixed(4));
}
	
//change custom by
$(document).on('change', '#customer', function(e){
	e.preventDefault();
	$.ajax({
		url: base_url +'index.php/'+ active_controller+'/getTolerance',
		cache: false,
		type: "POST",
		data: "cust="+this.value,
		dataType: "json",
		success: function(data){
			$("#top_toleran").html(data.option).trigger("chosen:updated");
		}
	});
});

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

//change standard tolerance
$(document).on('change', '#top_toleran', function(){
	if($(this).val() != 'C100-1903000'){
		$('.ToleranSt').show();
	}
	else{
		$('.ToleranSt').hide();	
		$('#top_min_toleran').val('0.125');
		$('#top_max_toleran').val('0.125');
		AcuhanMaxMin();
	}
});


$("#tamp").hide();
//change type
$(document).on('change', '#top_typeList', function(e){
	// e.preventDefault();
	var id	= $(this).val();
	var parent	= "parent";
	var parent_product = $('#parent_product').val();
	$("#tamp").hide();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getDiameter',
		cache: false,
		type: "POST",
		data: "id="+$(this).val()+"&parent="+parent,
		dataType: "json",
		success: function(data){
			$('#top_type').val(data.pipeN); 
			$('#diameter').val(data.pipeD);
			$('#diameter2').val(data.pipeD2);
			$('#parent_product').val(data.product);
			// alert(data.product);
			$('#mirror').show();
			$('#plactic').hide();
			
			var panjang			= 2.5 * (data.pipeD - data.pipeD2);
		
			$("#panjang").val(panjang.toFixed(1));
			
			
			$.ajax({
				url: base_url +'index.php/'+ active_controller+'/getMirrorMat',
				cache: false,
				type: "POST",
				// data: "cust="+this.value,
				dataType: "json",
				success: function(data){
					$("#mid_mtl_plastic").html(data.option).trigger("chosen:updated");
				}
			});
			
			if(active_controller == 'component_custom'){
				$.ajax({
					url: base_url +'index.php/'+ active_controller+'/getStandartCode',
					cache: false,
					type: "POST",
					data: "dim="+data.pipeD+"&parent_product="+data.product+"&dim2="+data.pipeD2,
					dataType: "json",
					success: function(data){
						$("#tamp").show();
						$("#standart_code").html(data.option).trigger("chosen:updated");
						$("#tamp").html("<p>"+data.tamp+"</p>");
						$("#tamp").css("color", data.color);
						$("#tamp").fadeOut(2000);
					}
				});
			}
			else if(active_controller == 'component'){
				$.ajax({
					url: base_url +'index.php/'+ active_controller+'/getDefault',
					cache: false,
					type: "POST",
					data: "dim="+data.pipeD+"&parent_product="+data.product+"&dim2="+data.pipeD2,
					dataType: "json",
					success: function(data){ 
						$('#waste').val(data.waste);
						$('#top_max_toleran').val(data.maxx);
						$('#top_min_toleran').val(data.minx);
						$('#layer_plastic').val(data.plastic_film);
						
						$('#layer_resin1').val(data.lin_resin_veil);
						$('#layer_resin2').val(data.lin_resin_veil_add);
						$('#layer_resin3').val(data.lin_resin_csm);
						$('#layer_resin4').val(data.lin_resin_csm_add);
						$('#layer_resin_tot').val(data.lin_resin);
						
						$('#layer_resin21').val(data.str_resin_csm);
						$('#layer_resin22').val(data.str_resin_csm_add);
						$('#layer_resin23').val(data.str_resin_wr);
						$('#layer_resin24').val(data.str_resin_wr_add);
						$('#layer_resin25').val(data.str_resin_rv);
						$('#layer_resin26').val(data.str_resin_rv_add);
						
						$('#bw_rooving21').val(data.str_faktor_rv_bw);  
						$('#jumlah_rooving21').val(data.str_faktor_rv_jb);
						
						$('#bw_rooving22').val(data.str_faktor_rv_add_bw);
						$('#jumlah_rooving22').val(data.str_faktor_rv_add_jb);
						$('#str_resin').val(data.str_resin);
						
						$('#layer_resin31').val(data.eks_resin_veil);
						$('#layer_resin32').val(data.eks_resin_veil_add);
						$('#layer_resin33').val(data.eks_resin_csm);
						$('#layer_resin34').val(data.eks_resin_csm_add);
						
						$('#eks_resin').val(data.eks_resin);
						
						$('#resin41').val(data.topcoat_resin);
						
						
						$('#lin_faktor_veil').val(data.lin_faktor_veil);
						$('#lin_faktor_veil_add').val(data.lin_faktor_veil_add);
						$('#lin_faktor_csm').val(data.lin_faktor_csm);
						$('#lin_faktor_csm_add').val(data.lin_faktor_csm_add);
						
						$('#str_faktor_csm').val(data.str_faktor_csm);
						$('#str_faktor_csm_add').val(data.str_faktor_csm_add);
						$('#str_faktor_wr').val(data.str_faktor_wr);
						$('#str_faktor_wr_add').val(data.str_faktor_wr_add);
						$('#str_faktor_rv').val(data.str_faktor_rv);
						$('#str_faktor_rv_add').val(data.str_faktor_rv_add);
						
						$('#eks_faktor_veil').val(data.eks_faktor_veil);
						$('#eks_faktor_veil_add').val(data.eks_faktor_veil_add);
						$('#eks_faktor_csm').val(data.eks_faktor_csm);
						$('#eks_faktor_csm_add').val(data.eks_faktor_csm_add);						
					}
				});
			}
			
			
			
			changeTop();
		}
	});
	
	
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

//liner change
$(document).on('keyup', '#layer_veil', function(){
	linerLayer();
	changeTop();
});

$(document).on('keyup', '#layer_veil_add', function(){
	linerLayer();
	changeTop();
});

$(document).on('keyup', '#layer_matcsm', function(){
	linerLayer();
	changeTop();
});

$(document).on('keyup', '#layer_csm_add', function(){
	linerLayer();
	changeTop();  
});

//OnKeyUp bawah Total Resin
$(document).on('keyup', '#persen_katalis', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_katalis	= $('#layer_katalis').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_katalis);
	$('#last_katalis').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_sm', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_sm	= $('#layer_sm').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_coblat', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_coblat	= $('#layer_coblat').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_dma', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_dma		= $('#layer_dma').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_hydroquinone', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot').val();
	var layer_hydroquinone	= $('#layer_hydroquinone').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_methanol', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot').val();
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

$(document).on('keyup', '#layer_matcsm2', function(){
	structureLayer();
	changeTop();
});

$(document).on('keyup', '#layer_csm_add2', function(){
	structureLayer();
	changeTop();
});

$(document).on('keyup', '#layer_wr2', function(){
	structureLayer();
	changeTop();
});

$(document).on('keyup', '#layer_wr_add2', function(){
	structureLayer();
	changeTop();
});

$(document).on('keyup', '#layer_rooving21', function(){
	structureLayer();
	changeTop();
});

$(document).on('keyup', '#layer_rooving22', function(){
	structureLayer();
	changeTop();
});

//OnKeyUp bawah Total Resin
//STRUKTURE
$(document).on('keyup', '#persen_katalis2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_katalis	= $('#layer_katalis2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_katalis);
	$('#last_katalis2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_sm2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_sm	= $('#layer_sm2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_coblat2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_coblat	= $('#layer_coblat2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_dma2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_dma		= $('#layer_dma2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_hydroquinone2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2').val();
	var layer_hydroquinone	= $('#layer_hydroquinone2').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro2').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_methanol2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2').val();
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

$(document).on('keyup', '#layer_veil3', function(){
	eksternalLayer();
	changeTop();
});

$(document).on('keyup', '#layer_veil_add3', function(){
	eksternalLayer();
	changeTop();
});

$(document).on('keyup', '#layer_matcsm3', function(){
	eksternalLayer();
	changeTop();
});

$(document).on('keyup', '#layer_csm_add3', function(){
	eksternalLayer();
	changeTop();
});  

//OnKeyUp bawah Total Resin
//EXTERNAL
$(document).on('keyup', '#persen_katalis3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot3').val();
	var layer_katalis	= $('#layer_katalis3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_katalis);
	$('#last_katalis3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_sm3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot3').val();
	var layer_sm	= $('#layer_sm3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_coblat3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot3').val();
	var layer_coblat	= $('#layer_coblat3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_dma3', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot3').val();
	var layer_dma		= $('#layer_dma3').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_hydroquinone3', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot3').val();
	var layer_hydroquinone	= $('#layer_hydroquinone3').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro3').val(RoundUp(Hasil));
});
$(document).on('keyup', '#persen_methanol3', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot3').val();
	var layer_methanol		= $('#layer_methanol3').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_methanol);
	$('#last_methanol3').val(RoundUp(Hasil));
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