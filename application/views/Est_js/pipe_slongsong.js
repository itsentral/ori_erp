
//===============================================================================================================
//================================================LUAS AREA======================================================
	function LuasArea(){
		var waste			= getNum($('#waste').val()) / 100;
		var diameter		= getNum($('#diameter').val());
		var top_length		= getNum($('#top_length').val());
		var topEST			= getNum($('#tot_lin_thickness').val()) + getNum($('#tot_lin_thickness2').val()); 
		
		var Luas_Area_Rumus		= ((3.14/1000)*(diameter + topEST)) *  (1+waste) * (top_length/1000);

		if(isNaN(Luas_Area_Rumus)){
			var Luas_Area_Rumus = 0;
		}
		// console.log(Luas_Area_Rumus);
		return Luas_Area_Rumus;
	}

//===================================================END=========================================================
//===============================================================================================================

function Hasil(a, b, c, d, e){
	AcuhanMaxMin();
	
	var liner			= $('#acuhan_1').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val(); 
		
	var min_lin_thickness	= parseFloat(liner) - (parseFloat(liner) * parseFloat(MinToleransi));
	var max_lin_thickness	= parseFloat(liner) + (parseFloat(liner) * parseFloat(MaxToleransi));
	
	var topEST				= getNum($('#tot_lin_thickness2').val()) + getNum(a);
	
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
	var diameter		= getNum($('#diameter').val());
	
	var perkalian = 1350;
	if(diameter < 25){
		var perkalian = 800;
	}

	var Luas_Area_Rumus		= LuasArea();

	var HasilPlastic		= (Luas_Area_Rumus * micron_plastic * perkalian * layer_plastic);
	
	var HasilVeil			= ((Luas_Area_Rumus * weight_veil * b)/1000);
	var Hasillayer_resin1	= getNum(HasilVeil) * layer_resin1;
	
	var HasilVeilAdd		= ((Luas_Area_Rumus * weight_veil_add * c)/1000);
	var Hasillayer_resin12	= getNum(HasilVeilAdd) * layer_resin2;
	
	var HasilMatCsm			= ((Luas_Area_Rumus * weight_matcsm * d)/1000) * 1.1;
	var Hasillayer_resin13	= getNum(HasilMatCsm) * layer_resin3;
	
	var HasilMatCsmAdd		= ((Luas_Area_Rumus * weight_csm_add * e)/1000) * 1.1;
	var Hasillayer_resin14	= getNum(HasilMatCsmAdd) * layer_resin4;
	
	var TotalResin			= (Luas_Area_Rumus* 1.2 *0.3) + Hasillayer_resin14 + Hasillayer_resin13 + Hasillayer_resin12 + Hasillayer_resin1;
	
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
}

function Hasil2(a, b, c, d, e, f, g){
	AcuhanMaxMin();
	
	var struktur		= $('#acuhan_2').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val();
	
	var min_str_thickness	= parseFloat(struktur) - (parseFloat(struktur) * parseFloat(MinToleransi));
	var max_str_thickness	= parseFloat(struktur) + (parseFloat(struktur) * parseFloat(MaxToleransi));
	
	var topEST				= getNum($('#tot_lin_thickness').val()) + getNum(a);
	
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
	
	var kali2 = 1; 
	if(diameter < 150){
		var kali2 = 1.2; 
	}
	else if(diameter >= 200 && diameter <= 350){
		var kali2 = 1.12; 
	}
	
	var HasilMadCsm			= (Luas_Area_Rumus * weight_matcsm2 * b)/1000;
	var HasilMadCsmAdd		= (Luas_Area_Rumus * weight_csm_add2 * c)/1000;
	var HasilWr				= ((Luas_Area_Rumus * weight_wr2 * d)/1000) ;
	var HasilWrAdd			= ((Luas_Area_Rumus * weight_wr_add2 * e)/1000) ;
	var HasilRoof21			= ((weight_rooving21 * 0.001 * jumlah_rooving21 * 100)/(bw_rooving21/10)) * (2/1000) * f * Luas_Area_Rumus * kali2;
	var HasilRoof22			= ((weight_rooving22 * 0.001 * jumlah_rooving22 * 100)/(bw_rooving22/10)) * (2/1000) * g * Luas_Area_Rumus * kali2;
	
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
	
	var TotalResin2			= (Hasillayer21) + (Hasillayer22) + (Hasillayer23) + (Hasillayer24)  + (Hasillayer25)  + (Hasillayer26);
	
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
}

function changeTop(){
	var waste			= getNum($('#waste').val());
	var top_diameter	= getNum($('#diameter').val());
	var top_thickness	= getNum($('#top_tebal_design').val());
	var topEST			= getNum($('#tot_lin_thickness').val()) + getNum($('#tot_lin_thickness2').val());
	var acuhan_1		= getNum($('#acuhan_1').val());
	
	var hasilAch2		= top_thickness - (acuhan_1);
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
	
	Hasil(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4);
	Hasil2(tot_thickness2, layer1, layer2, layer3, layer4, layer5, layer6);
}

//hasil akhir thickness
function AcuhanMaxMin(){
	var liner			= $('#acuhan_1').val();
	var struktur		= $('#acuhan_2').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi	= $('#top_max_toleran').val();
	var totThickness1	= $('#tot_lin_thickness').val();
	var totThickness2	= $('#tot_lin_thickness2').val();
		
	var min_lin_thickness	= getNum(liner) - (getNum(liner) * getNum(MinToleransi));
	var max_lin_thickness	= getNum(liner) + (getNum(liner) * getNum(MaxToleransi));
	
	var min_str_thickness	= getNum(struktur) - (getNum(struktur) * getNum(MinToleransi));
	var max_str_thickness	= getNum(struktur) + (getNum(struktur) * getNum(MaxToleransi));
	
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


//change type
$(document).on('change', '#top_typeList', function(e){
	// e.preventDefault();
	var id	= $(this).val();
	$.ajax({
		url: base_url +'index.php/'+active_controller+'/getDiameter',
		cache: false,
		type: "POST",
		data: "id="+$(this).val(),
		dataType: "json",
		success: function(data){
			$('#top_type').val(data.pipeN);
			$('#diameter').val(data.pipeD);
			$('#waste').val(data.wasted);
			
			var ThisD = data.pipeD;
			if(ThisD < 400){
				var angkaRoving	= 32/68;
			}
			else if(ThisD > 350){  
				var angkaRoving	= 28/72;
			}
			else{
				var angkaRoving	= 0;
			}
			var angkaVeil	= 9/1;
			var angkaCsm	= 7/3;
			var angkaWr		= 45/55;
			
			// if(ThisD < 25){
				$('#mirror').show();
				$('#plactic').hide();
				
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
			// }

			//Rooving
			$('#layer_resin25').val(angkaRoving.toFixed(3));
			$('#layer_resin26').val(angkaRoving.toFixed(3));			
			
			//Veil
			$('#layer_resin1').val(angkaVeil.toFixed(3));
			$('#layer_resin2').val(angkaVeil.toFixed(3));
			
			//CSM
			$('#layer_resin3').val(angkaCsm.toFixed(3));
			$('#layer_resin4').val(angkaCsm.toFixed(3));
			$('#layer_resin21').val(angkaCsm.toFixed(3));
			$('#layer_resin22').val(angkaCsm.toFixed(3));
			
			//WR
			$('#layer_resin23').val(angkaWr.toFixed(3));
			$('#layer_resin24').val(angkaWr.toFixed(3));
			
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
	$('#last_katalis').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_sm', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_sm	= $('#layer_sm').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_coblat', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_coblat	= $('#layer_coblat').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_dma', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot').val();
	var layer_dma		= $('#layer_dma').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_hydroquinone', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot').val();
	var layer_hydroquinone	= $('#layer_hydroquinone').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_methanol', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot').val();
	var layer_methanol		= $('#layer_methanol').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_methanol);
	$('#last_methanol').val(Hasil.toFixed(3));
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
			$('#bw_rooving21').val(data.bw);
			$('#jumlah_rooving21').val(data.jumRoov);
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
			$('#bw_rooving22').val(data.bw);
			$('#jumlah_rooving22').val(data.jumRoov);
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
	$('#last_katalis2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_sm2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_sm	= $('#layer_sm2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_sm);
	$('#last_sm2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_coblat2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_coblat	= $('#layer_coblat2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_coblat);
	$('#last_cobalt2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_dma2', function(){
	var nilai			= $(this).val();
	var hasil_resin_tot = $('#hasil_resin_tot2').val();
	var layer_dma		= $('#layer_dma2').val();
	var Hasil			= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_dma);
	$('#last_dma2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_hydroquinone2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2').val();
	var layer_hydroquinone	= $('#layer_hydroquinone2').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_hydroquinone);
	$('#last_hidro2').val(Hasil.toFixed(3));
});
$(document).on('keyup', '#persen_methanol2', function(){
	var nilai				= $(this).val();
	var hasil_resin_tot 	= $('#hasil_resin_tot2').val();
	var layer_methanol		= $('#layer_methanol2').val();
	var Hasil				= (getNum(nilai)/100) * getNum(hasil_resin_tot) * getNum(layer_methanol);
	$('#last_methanol2').val(Hasil.toFixed(3));
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