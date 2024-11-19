
function getNum(val) {
   if (isNaN(val) || val == '') {
     return 0;
   }
   return parseFloat(val);
}

function LuasAreaFlange(){
	var waste			= getNum($('#waste').val()) / 100;
	var top_diameter	= getNum($('#diameter').val());
	var flange_od		= getNum($('#flange_od').val());
	
	var pangkat_od			= Math.pow(flange_od, 2);
	var pangkat_dim			= Math.pow(top_diameter, 2);
	var Luas_Area_Rumus		= 3.14/4 * (pangkat_od - pangkat_dim) / 1000000 * (1+waste);
	if(isNaN(Luas_Area_Rumus)){
		var Luas_Area_Rumus = 0;
	}
	return Luas_Area_Rumus;
}

function LuasAreaNeck1(){
	var waste			= getNum($('#waste_n1').val()) / 100;
	var top_diameter	= getNum($('#diameter').val());
	var length_neck1	= getNum($('#panjang_neck_1').val());
	var est_neck1		= parseFloat($('#thickLin').val()) +  parseFloat($('#thickStrN1').val());
	
	var Luas_Area_Rumus_Neck1		= 3.14 * (top_diameter + est_neck1) * length_neck1 / 1000000 * (1+waste);
	if(isNaN(Luas_Area_Rumus_Neck1)){
		var Luas_Area_Rumus_Neck1 = 0;
	}
	return Luas_Area_Rumus_Neck1;
}

function LuasAreaNeck2(){
	var waste			= getNum($('#waste_n2').val()) / 100;
	var top_diameter	= getNum($('#diameter').val());
	var length_neck1	= getNum($('#panjang_neck_1').val());
	var top_thickness	= parseFloat($('#design').val());
	
	var est_neck1		= parseFloat($('#thickLin').val()) +  parseFloat($('#thickStrN1').val());
	var est_neck2		= parseFloat($('#thickStrN2').val());
	
	var length_neck2	= top_thickness * 3;
	if(isNaN(length_neck2)){
		var length_neck2	= 0;
	}
	
	var Luas_Area_Rumus_Neck2		= 3.14 * (top_diameter + est_neck1 + est_neck2) * length_neck2 / 1000000 * (1+waste);
	if(isNaN(Luas_Area_Rumus_Neck2)){
		var Luas_Area_Rumus_Neck2 = 0;
	}
	return Luas_Area_Rumus_Neck2;
}

function Estimasi(){
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	var thickEks	= getNum($('#thickEks').val());
	
	var topEST	= thickLin + thickStr + thickEks;
	if(isNaN(topEST)){
		var topEST = 0;
	}
	return topEST;
}

function ChangeLuasArea(){
	// console.log('SAmpai JS');
	AllThickness();
	
	var estimasi 	= Estimasi();
	
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	var thickEks	= getNum($('#thickEks').val());
	
	var flange_od		= getNum($('#flange_od').val());
	var panjang_neck_1	= getNum($('#panjang_neck_1').val());
	var panjang_neck_2	= getNum($('#panjang_neck_2').val());
	var est_neck_1		= getNum($('#thickStrN1').val()) + thickLin;
	var est_neck_2		= getNum($('#thickStrN2').val());

	var cont_topcoat_1		= getNum($('#cont_topcoat_1').val());
	
	var LuasArea	= LuasAreaFlange();
	var LuasAreaN1	= getNum(LuasAreaNeck1());
	var LuasAreaN2	= getNum(LuasAreaNeck2());
	
	var LuasAll		= LuasArea + LuasAreaN1 + LuasAreaN2;
	
	var LuasCoat	= LuasArea + LuasAreaN1;
	
	var LastCoat	= LuasCoat * 1.2 * 4 * cont_topcoat_1;
	
	// console.log(LuasAreaN1);
	// console.log(LuasAreaN2);
	
	
	
	if(isNaN(LastCoat)){ var LastCoat = 0;}
	$('#last_topcoat_1').val(LastCoat.toFixed(3));

	$('#estimasi').val(estimasi.toFixed(4));
	$('#est_neck_1').val(est_neck_1.toFixed(4));
	$('#est_neck_2').val(est_neck_2.toFixed(4));
	$('#area').val(LuasArea.toFixed(6));
	$('#area_neck_1').val(LuasAreaN1.toFixed(6));
	$('#area_neck_2').val(LuasAreaN2.toFixed(6));
	
	
	ChangeAreaToLiner(LuasCoat);
	ChangeAreaToStr(LuasArea);
	ChangeAreaToStrN1(LuasAreaN1);
	ChangeAreaToStrN2(LuasAreaN2);
	ChangeAreaToEks(LuasAll);
	
	ChangePlusTopCoat(LastCoat);
	ChangePlusTcAdd(LastCoat);
	
}

function AllThickness(){
	var totthicknessLin1	= getNum($('#total_thickness_2').val());
	var totthicknessLin2	= getNum($('#total_thickness_4').val()); 
	var totthicknessLin3	= getNum($('#total_thickness_6').val());
	var totthicknessLin4	= getNum($('#total_thickness_8').val());
	var AllThickLin			= totthicknessLin1 + totthicknessLin2 + totthicknessLin3 + totthicknessLin4;
	$('#thickLin').val(AllThickLin.toFixed(4));
	
	var totthicknessStr1	= getNum($('#total_thicknessStr_1').val());
	var totthicknessStr2	= getNum($('#total_thicknessStr_3').val());
	var totthicknessStr3	= getNum($('#total_thicknessStr_5').val());
	var totthicknessStr4	= getNum($('#total_thicknessStr_7').val());
	var AllThickStr			= totthicknessStr1 + totthicknessStr2 + totthicknessStr3 + totthicknessStr4;
	$('#thickStr').val(AllThickStr.toFixed(4));
	
	var totthicknessN11		= getNum($('#total_thicknessN1_1').val());
	var totthicknessN12		= getNum($('#total_thicknessN1_3').val());
	var totthicknessN13		= getNum($('#total_thicknessN1_5').val());
	var totthicknessN14		= getNum($('#total_thicknessN1_7').val());
	var totthicknessN15		= getNum($('#total_thicknessN1_9').val());
	var totthicknessN16		= getNum($('#total_thicknessN1_11').val());
	var AllThickN1			= totthicknessN11 + totthicknessN12 + totthicknessN13 + totthicknessN14 + totthicknessN15 + totthicknessN16;
	$('#thickStrN1').val(AllThickN1.toFixed(4));
	
	var totthicknessN21		= getNum($('#total_thicknessN2_1').val());
	var totthicknessN22		= getNum($('#total_thicknessN2_3').val());
	var totthicknessN23		= getNum($('#total_thicknessN2_5').val());
	var totthicknessN24		= getNum($('#total_thicknessN2_7').val());
	var AllThickN2			= totthicknessN21 + totthicknessN22 + totthicknessN23 + totthicknessN24;
	$('#thickStrN2').val(AllThickN2.toFixed(4));
	
	var totthicknessEks1	= getNum($('#total_thicknessEks_1').val());
	var totthicknessEks2	= getNum($('#total_thicknessEks_3').val());
	var totthicknessEks3	= getNum($('#total_thicknessEks_5').val());
	var totthicknessEks4	= getNum($('#total_thicknessEks_7').val());
	var AllThickEks			= totthicknessEks1 + totthicknessEks2 + totthicknessEks3 + totthicknessEks4;
	$('#thickEks').val(AllThickEks.toFixed(4));
}

function ChangeHasil(){
	var ThLin		= getNum($('#ThLin').val());
	var ThStr		= getNum($('#ThStr').val());
	var ThEks		= getNum($('#ThEks').val());
	var ThStrN1		= getNum($('#ThStrN1').val());
	var ThStrN2		= getNum($('#ThStrN2').val());
	var minToleran	= getNum($('#min_toleran').val());
	var maxToleran	= getNum($('#max_toleran').val());
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	var thickEks	= getNum($('#thickEks').val());
	var thickStrN1	= getNum($('#thickStrN1').val());
	var thickStrN2	= getNum($('#thickStrN2').val());
	
	var minLinThk	= ThLin - (ThLin * minToleran);
	var maxLinThk	= ThLin + (ThLin * maxToleran);
	var minStrThk	= ThStr - (ThStr * minToleran);
	var maxStrThk	= ThStr + (ThStr * maxToleran);
	var minEksThk	= ThEks - (ThEks * minToleran);
	var maxEksThk	= ThEks + (ThEks * maxToleran);
	var minStrThkN1	= ThStrN1 - (ThStrN1 * minToleran);
	var maxStrThkN1	= ThStrN1 + (ThStrN1 * maxToleran);
	var minStrThkN2	= ThStrN2 - (ThStrN2 * minToleran);
	var maxStrThkN2	= ThStrN2 + (ThStrN2 * maxToleran);
	
	// console.log(minLinThk);
	// console.log(ThLin);
	// console.log(minToleran);
	
	if(isNaN(minLinThk)){ var minLinThk = 0;}
	if(isNaN(maxLinThk)){ var maxLinThk = 0;}
	if(isNaN(minStrThk)){ var minStrThk = 0;}
	if(isNaN(maxStrThk)){ var maxStrThk = 0;}
	if(isNaN(minEksThk)){ var minEksThk = 0;}
	if(isNaN(maxEksThk)){ var maxEksThk = 0;}
	if(isNaN(minStrThkN1)){ var minStrThkN1 = 0;}
	if(isNaN(maxStrThkN1)){ var maxStrThkN1 = 0;}
	if(isNaN(minStrThkN2)){ var minStrThkN2 = 0;}
	if(isNaN(maxStrThkN2)){ var maxStrThkN2 = 0;}
	
	if(thickLin < minLinThk){var Hasil1	= "TOO LOW";}
	if(thickLin > maxLinThk){var Hasil1	= "TOO HIGH";}
	if(thickLin > minLinThk && thickLin < maxLinThk){var Hasil1	= "OK";}
	$('#minLin').val(minLinThk.toFixed(4));
	$('#maxLin').val(maxLinThk.toFixed(4));
	$('#hasilLin').val(Hasil1);
	
	if(thickStr < minStrThk){var Hasil2	= "TOO LOW";}
	if(thickStr > maxStrThk){var Hasil2	= "TOO HIGH";}
	if(thickStr > minStrThk && thickStr < maxStrThk){var Hasil2	= "OK";}
	$('#minStr').val(minStrThk.toFixed(4));
	$('#maxStr').val(maxStrThk.toFixed(4));
	$('#hasilStr').val(Hasil2);
	
	
	if(thickStrN1 < minStrThkN1){var Hasil2N1	= "TOO LOW";}
	if(thickStrN1 > maxStrThkN1){var Hasil2N1	= "TOO HIGH";}
	if(thickStrN1 > minStrThkN1 && thickStrN1 < maxStrThkN1){var Hasil2N1	= "OK";}
	$('#minStrN1').val(minStrThkN1.toFixed(4));
	$('#maxStrN1').val(maxStrThkN1.toFixed(4));
	$('#hasilStrN1').val(Hasil2N1);
	
	// console.log(Hasil2N1);
	
	if(thickStrN2 < minStrThkN2){var Hasil2N2	= "TOO LOW";}
	if(thickStrN2 > maxStrThkN2){var Hasil2N2	= "TOO HIGH";}
	if(thickStrN2 > minStrThkN2 && thickStrN2 < maxStrThkN2){var Hasil2N2	= "OK";}
	$('#minStrN2').val(minStrThkN2.toFixed(4));
	$('#maxStrN2').val(maxStrThkN2.toFixed(4));
	$('#hasilStrN2').val(Hasil2N2);
	
	if(thickEks < minEksThk){var Hasil3	= "TOO LOW";}
	if(thickEks > maxEksThk){var Hasil3	= "TOO HIGH";}
	if((thickEks > minEksThk && thickEks < maxEksThk) || thickEks == 0){var Hasil3	= "OK";} 
	$('#minEks').val(minEksThk.toFixed(4));
	$('#maxEks').val(maxEksThk.toFixed(4));
	$('#hasilEks').val(Hasil3);
}

function LastWeight(){
	var area	= getNum($('#area').val());
	return area;
}

function ChangePlus(Area){
	// console.log(Area);
	var Con1	= getNum($('#Lincontaining_1').val());
	var Con2	= getNum($('#Lincontaining_2').val());
	var Con3	= getNum($('#Lincontaining_3').val());
	var Con4	= getNum($('#Lincontaining_4').val());
	var Con5	= getNum($('#Lincontaining_5').val());
	var Con6	= getNum($('#Lincontaining_6').val());
	
	var Per1	= getNum($('#Linperse_1').val()) /100;
	var Per2	= getNum($('#Linperse_2').val()) /100;
	var Per3	= getNum($('#Linperse_3').val()) /100;
	var Per4	= getNum($('#Linperse_4').val()) /100;
	var Per5	= getNum($('#Linperse_5').val()) /100;
	var Per6	= getNum($('#Linperse_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangePlusStr(Area){
	var Con1	= getNum($('#Lincontaining2_1').val());
	var Con2	= getNum($('#Lincontaining2_2').val());
	var Con3	= getNum($('#Lincontaining2_3').val());
	var Con4	= getNum($('#Lincontaining2_4').val());
	var Con5	= getNum($('#Lincontaining2_5').val());
	var Con6	= getNum($('#Lincontaining2_6').val());
	
	var Per1	= getNum($('#Linperse2_1').val()) /100;
	var Per2	= getNum($('#Linperse2_2').val()) /100;
	var Per3	= getNum($('#Linperse2_3').val()) /100;
	var Per4	= getNum($('#Linperse2_4').val()) /100;
	var Per5	= getNum($('#Linperse2_5').val()) /100;
	var Per6	= getNum($('#Linperse2_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost2_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost2_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost2_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost2_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost2_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost2_6').val(Math.ceil(Hasil6 * 1000)/1000);
}
function ChangePlusStrN1(Area){
	var Con1	= getNum($('#Lincontaining2N1_1').val());
	var Con2	= getNum($('#Lincontaining2N1_2').val());
	var Con3	= getNum($('#Lincontaining2N1_3').val());
	var Con4	= getNum($('#Lincontaining2N1_4').val());
	var Con5	= getNum($('#Lincontaining2N1_5').val());
	var Con6	= getNum($('#Lincontaining2N1_6').val());
	
	var Per1	= getNum($('#Linperse2N1_1').val()) /100;
	var Per2	= getNum($('#Linperse2N1_2').val()) /100;
	var Per3	= getNum($('#Linperse2N1_3').val()) /100;
	var Per4	= getNum($('#Linperse2N1_4').val()) /100;
	var Per5	= getNum($('#Linperse2N1_5').val()) /100;
	var Per6	= getNum($('#Linperse2N1_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost2N1_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost2N1_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost2N1_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost2N1_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost2N1_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost2N1_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangePlusStrN2(Area){
	var Con1	= getNum($('#Lincontaining2N2_1').val());
	var Con2	= getNum($('#Lincontaining2N2_2').val());
	var Con3	= getNum($('#Lincontaining2N2_3').val());
	var Con4	= getNum($('#Lincontaining2N2_4').val());
	var Con5	= getNum($('#Lincontaining2N2_5').val());
	var Con6	= getNum($('#Lincontaining2N2_6').val());
	
	var Per1	= getNum($('#Linperse2N2_1').val()) /100;
	var Per2	= getNum($('#Linperse2N2_2').val()) /100;
	var Per3	= getNum($('#Linperse2N2_3').val()) /100;
	var Per4	= getNum($('#Linperse2N2_4').val()) /100;
	var Per5	= getNum($('#Linperse2N2_5').val()) /100;
	var Per6	= getNum($('#Linperse2N2_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost2N2_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost2N2_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost2N2_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost2N2_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost2N2_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost2N2_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangePlusEks(Area){
	var Con1	= getNum($('#Lincontaining3_1').val());
	var Con2	= getNum($('#Lincontaining3_2').val());
	var Con3	= getNum($('#Lincontaining3_3').val());
	var Con4	= getNum($('#Lincontaining3_4').val());
	var Con5	= getNum($('#Lincontaining3_5').val());
	var Con6	= getNum($('#Lincontaining3_6').val());
	
	var Per1	= getNum($('#Linperse3_1').val()) /100;
	var Per2	= getNum($('#Linperse3_2').val()) /100;
	var Per3	= getNum($('#Linperse3_3').val()) /100;
	var Per4	= getNum($('#Linperse3_4').val()) /100;
	var Per5	= getNum($('#Linperse3_5').val()) /100;
	var Per6	= getNum($('#Linperse3_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost3_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost3_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost3_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost3_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost3_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost3_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangePlusTopCoat(Area){
	var Con2	= getNum($('#cont_topcoat_2').val());
	var Con3	= getNum($('#cont_topcoat_3').val());
	var Con4	= getNum($('#cont_topcoat_4').val());
	var Con5	= getNum($('#cont_topcoat_5').val());
	var Con6	= getNum($('#cont_topcoat_6').val());
	var Con7	= getNum($('#cont_topcoat_7').val());
	var Con8	= getNum($('#cont_topcoat_8').val());
	
	var Per2	= getNum($('#perse_topcoat_2').val()) /100;
	var Per3	= getNum($('#perse_topcoat_3').val()) /100;
	var Per4	= getNum($('#perse_topcoat_4').val()) /100;
	var Per5	= getNum($('#perse_topcoat_5').val()) /100;
	var Per6	= getNum($('#perse_topcoat_6').val()) /100;
	var Per7	= getNum($('#perse_topcoat_7').val()) /100;
	var Per8	= getNum($('#perse_topcoat_8').val()) /100;
	
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	var Hasil7	= Area * Con7 * Per7;
	var Hasil8	= Area * Con8 * Per8;
	
	$('#last_topcoat_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#last_topcoat_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#last_topcoat_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#last_topcoat_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#last_topcoat_6').val(Math.ceil(Hasil6 * 1000)/1000);
	$('#last_topcoat_7').val(Math.ceil(Hasil7 * 1000)/1000);
	$('#last_topcoat_8').val(Math.ceil(Hasil8 * 1000)/1000);
}

function ChangeAreaToLiner(Area){
	var value_1 		= getNum($('#value_1').val());
	var value_2 		= getNum($('#value_2').val());
	var value_4 		= getNum($('#value_4').val());
	var value_6 		= getNum($('#value_6').val());
	var value_8 		= getNum($('#value_8').val());
	var layer_2 		= getNum($('#layer_2').val());
	var layer_4 		= getNum($('#layer_4').val());
	var layer_6 		= getNum($('#layer_6').val());
	var layer_8 		= getNum($('#layer_8').val());
	var containing_3	= getNum($('#containing_3').val());
	var containing_5	= getNum($('#containing_5').val());
	var containing_7	= getNum($('#containing_7').val());
	var containing_9	= getNum($('#containing_9').val());
	var containing_10	= getNum($('#containing_10').val());
	
	var LinFakVeil		= getNum($("#lin_faktor_veil").val());
	var LinFakVeilAdd	= getNum($("#lin_faktor_veil_add").val());
	var LinFakCsm		= getNum($("#lin_faktor_csm").val());
	var LinFakCsmAdd	= getNum($("#lin_faktor_csm_add").val());
	
	var id_material_1 	= $('#id_material_1').val();
	var diameter		= $('#diameter').val();
	if(diameter < 25){var Hit = 800;}else{var Hit = 1350;}
	
	// console.log(Area);
	// console.log(value_2);
	// console.log(layer_2);
	// console.log(LinFakVeil);
	
	var last_cost_1 	= Area * value_1 * 1.5 * Hit ;
	var last_cost_2 	= ((Area * value_2 * layer_2)/1000)	* LinFakVeil;
	var last_cost_4 	= ((Area * value_4 * layer_4)/1000)	* LinFakVeilAdd;
	var last_cost_6 	= ((Area * value_6 * layer_6)/1000)	* LinFakCsm;
	var last_cost_8 	= ((Area * value_8 * layer_8)/1000)	* LinFakCsmAdd; 
	var resin3			= last_cost_2 * containing_3;
	var resin5			= last_cost_4 * containing_5;
	var resin7			= last_cost_6 * containing_7;
	var resin9			= last_cost_8 * containing_9;
	
	if(resin3 == 0 && resin5 == 0 && resin7 == 0 && resin9 == 0){
		var resiTot		= 0;
	}
	else{
		var resiTot		= (Area * 1.2 * containing_10) + resin3 + resin5 + resin7 + resin9;
	}
	ChangePlus(resiTot);
	ChangePlusAdd(resiTot);
	
	$('#last_cost_10').val(RoundUp(resiTot));
	$('#last_full_10').val(resiTot);
	
	$("#last_cost_1").val(RoundUp(last_cost_1));
	$("#last_cost_2").val(RoundUp(last_cost_2));
	$("#last_cost_4").val(RoundUp(last_cost_4));
	$("#last_cost_6").val(RoundUp(last_cost_6));
	$("#last_cost_8").val(RoundUp(last_cost_8));
	$("#last_cost_3").val(RoundUp(resin3));
	$("#last_cost_5").val(RoundUp(resin5));
	$("#last_cost_7").val(RoundUp(resin7));
	$("#last_cost_9").val(RoundUp(resin9));
}

function ChangeAreaToStr(Area){
	// console.log("AreaSTR"+Area);
	var valueStr_1 		= getNum($('#valueStr_1').val());
	var valueStr_3 		= getNum($('#valueStr_3').val());
	var valueStr_5 		= getNum($('#valueStr_5').val());
	var valueStr_7 		= getNum($('#valueStr_7').val());
	
	var layerStr_1 		= getNum($('#layerStr_1').val());
	var layerStr_3 		= getNum($('#layerStr_3').val());
	var layerStr_5 		= getNum($('#layerStr_5').val());
	var layerStr_7 		= getNum($('#layerStr_7').val());
	
	var containingStr_2		= getNum($('#containingStr_2').val());
	var containingStr_4		= getNum($('#containingStr_4').val());
	var containingStr_6		= getNum($('#containingStr_6').val());
	var containingStr_8		= getNum($('#containingStr_8').val());
	var containingStr_9		= getNum($('#containingStr_9').val());
	
	var StrFakCsm		= getNum($("#str_faktor_csm").val());
	var StrFakCsmAdd	= getNum($("#str_faktor_csm_add").val());
	var StrFakWr		= getNum($("#str_faktor_wr").val());
	var StrFakWrAdd		= getNum($("#str_faktor_wr_add").val());
	
	var diameter		= $('#diameter').val();
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
	
	var last_costStr_1 		= ((Area * valueStr_1 * layerStr_1)/1000) * StrFakCsm ;
	var last_costStr_3 		= ((Area * valueStr_3 * layerStr_3)/1000) * StrFakCsmAdd ;
	var last_costStr_5 		= ((Area * valueStr_5 * layerStr_5)/1000) * StrFakWr ;
	var last_costStr_7 		= ((Area * valueStr_7 * layerStr_7)/1000) * StrFakWrAdd ;
	
	var resin2			= last_costStr_1 * containingStr_2;
	var resin4			= last_costStr_3 * containingStr_4;
	var resin6			= last_costStr_5 * containingStr_6;
	var resin8			= last_costStr_7 * containingStr_8;
	
	var resiTot			= containingStr_9 * (resin2 + resin4 + resin6 + resin8);
	ChangePlusStr(resiTot);
	ChangePlusStrAdd(resiTot);
	
	$('#last_costStr_9').val(RoundUp(resiTot));
	$('#last_fullStr_9').val(resiTot);

	$("#last_costStr_1").val(RoundUp(last_costStr_1));
	$("#last_costStr_3").val(RoundUp(last_costStr_3));
	$("#last_costStr_5").val(RoundUp(last_costStr_5));
	$("#last_costStr_7").val(RoundUp(last_costStr_7));
	
	$("#last_costStr_2").val(RoundUp(resin2));
	$("#last_costStr_4").val(RoundUp(resin4));
	$("#last_costStr_6").val(RoundUp(resin6));
	$("#last_costStr_8").val(RoundUp(resin8));
}

function ChangeAreaToStrN1(Area){
	// console.log("AreaN1"+Area);
	var valueStr_1 		= getNum($('#valueStrN1_1').val());
	var valueStr_3 		= getNum($('#valueStrN1_3').val());
	var valueStr_5 		= getNum($('#valueStrN1_5').val());
	var valueStr_7 		= getNum($('#valueStrN1_7').val());
	var valueStr_9 		= getNum($('#valueStrN1_9').val());
	var valueStr_11 	= getNum($('#valueStrN1_11').val());
	
	var layerStr_1 		= getNum($('#layerStrN1_1').val());
	var layerStr_3 		= getNum($('#layerStrN1_3').val());
	var layerStr_5 		= getNum($('#layerStrN1_5').val());
	var layerStr_7 		= getNum($('#layerStrN1_7').val());
	var layerStr_9 		= getNum($('#layerStrN1_9').val());
	var layerStr_11 	= getNum($('#layerStrN1_11').val()); 
	
	var containingStr_2		= getNum($('#containingStrN1_2').val());
	var containingStr_4		= getNum($('#containingStrN1_4').val());
	var containingStr_6		= getNum($('#containingStrN1_6').val());
	var containingStr_8		= getNum($('#containingStrN1_8').val());
	var containingStr_10	= getNum($('#containingStrN1_10').val());
	var containingStr_12	= getNum($('#containingStrN1_12').val());
	var containingStr_13	= getNum($('#containingStrN1_13').val());
	
	var StrFakCsm		= getNum($("#str_n1_faktor_csm").val());
	var StrFakCsmAdd	= getNum($("#str_n1_faktor_csm_add").val());
	var StrFakWr		= getNum($("#str_n1_faktor_wr").val());
	var StrFakWrAdd		= getNum($("#str_n1_faktor_wr_add").val());
	var StrFakRv		= getNum($("#str_n1_faktor_rv").val());
	var StrFakRvAdd		= getNum($("#str_n1_faktor_rv_add").val());
	
	var bwStr_9 		= getNum($('#bwStrN1_9').val());
	var jumlahStr_9 	= getNum($('#jumlahStrN1_9').val());
	var bwStr_11 		= getNum($('#bwStrN1_11').val());
	var jumlahStr_11 	= getNum($('#jumlahStrN1_11').val());
	
	var diameter		= getNum($('#diameter').val());
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
	
	var last_costStr_1 		= ((Area * valueStr_1 * layerStr_1)/1000) * StrFakCsm ;
	var last_costStr_3 		= ((Area * valueStr_3 * layerStr_3)/1000) * StrFakCsmAdd ;
	var last_costStr_5 		= ((Area * valueStr_5 * layerStr_5)/1000) * StrFakWr ;
	var last_costStr_7 		= ((Area * valueStr_7 * layerStr_7)/1000) * StrFakWrAdd ; 
	var last_costStr_9 		= ((valueStr_9 * 0.001 * jumlahStr_9 * 100)/(bwStr_9/10)) * (2/1000) * layerStr_9 * Area * StrFakRv;
	var last_costStr_11 	= ((valueStr_11 * 0.001 * jumlahStr_11 * 100)/(bwStr_11/10)) * (2/1000) * layerStr_11 * Area * StrFakRvAdd;
	
	// console.log(Area);
	// console.log(valueStr_1);  
	// console.log(layerStr_1);
	// console.log(StrFakCsm);
	
	
	if(isNaN(last_costStr_9)){var last_costStr_9 = 0;}
	if(isNaN(last_costStr_11)){var last_costStr_11 = 0;}
	
	var resin2			= last_costStr_1 * containingStr_2;
	var resin4			= last_costStr_3 * containingStr_4;
	var resin6			= last_costStr_5 * containingStr_6;
	var resin8			= last_costStr_7 * containingStr_8;
	var resin10			= last_costStr_9 * containingStr_10 * kali;
	var resin12			= last_costStr_11 * containingStr_12 * kali;
	
	var resiTot			= containingStr_13 * (resin2 + resin4 + resin6 + resin8 + resin10 + resin12);
	ChangePlusStrN1(resiTot);
	ChangePlusStrAddN1(resiTot);
	
	// console.log(last_costStr_1);
	$('#last_costStrN1_13').val(RoundUp(resiTot));
	$('#last_fullStrN1_13').val(resiTot);

	$("#last_costStrN1_1").val(RoundUp(last_costStr_1));
	$("#last_costStrN1_3").val(RoundUp(last_costStr_3));
	$("#last_costStrN1_5").val(RoundUp(last_costStr_5));
	$("#last_costStrN1_7").val(RoundUp(last_costStr_7));
	$("#last_costStrN1_9").val(RoundUp(last_costStr_9));
	$("#last_costStrN1_11").val(RoundUp(last_costStr_11));
	
	$("#last_costStrN1_2").val(RoundUp(resin2));
	$("#last_costStrN1_4").val(RoundUp(resin4));
	$("#last_costStrN1_6").val(RoundUp(resin6));
	$("#last_costStrN1_8").val(RoundUp(resin8));
	$("#last_costStrN1_10").val(RoundUp(resin10));
	$("#last_costStrN1_12").val(RoundUp(resin12));
}

function ChangeAreaToStrN2(Area){
	var valueStr_1 		= getNum($('#valueStrN2_1').val());
	var valueStr_3 		= getNum($('#valueStrN2_3').val());
	var valueStr_5 		= getNum($('#valueStrN2_5').val());
	var valueStr_7 		= getNum($('#valueStrN2_7').val());
	
	var layerStr_1 		= getNum($('#layerStrN2_1').val());
	var layerStr_3 		= getNum($('#layerStrN2_3').val());
	var layerStr_5 		= getNum($('#layerStrN2_5').val());
	var layerStr_7 		= getNum($('#layerStrN2_7').val());
	
	var containingStr_2		= getNum($('#containingStrN2_2').val());
	var containingStr_4		= getNum($('#containingStrN2_4').val());
	var containingStr_6		= getNum($('#containingStrN2_6').val());
	var containingStr_8		= getNum($('#containingStrN2_8').val());
	var containingStr_9		= getNum($('#containingStrN2_9').val());
	
	var StrFakCsm		= getNum($("#str_n2_faktor_csm").val());
	var StrFakCsmAdd	= getNum($("#str_n2_faktor_csm_add").val());
	var StrFakWr		= getNum($("#str_n2_faktor_wr").val());
	var StrFakWrAdd		= getNum($("#str_n2_faktor_wr_add").val());
	
	
	var diameter		= getNum($('#diameter').val());
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
	
	// console.log(Area);
	// console.log(valueStr_1);
	// console.log(layerStr_1);
	// console.log(StrFakCsm);
	
	
	var last_costStr_1 		= ((Area * valueStr_1 * layerStr_1)/1000) * StrFakCsm ;
	var last_costStr_3 		= ((Area * valueStr_3 * layerStr_3)/1000) * StrFakCsmAdd ;
	var last_costStr_5 		= ((Area * valueStr_5 * layerStr_5)/1000) * StrFakWr ;
	var last_costStr_7 		= ((Area * valueStr_7 * layerStr_7)/1000) * StrFakWrAdd ;
	
	var resin2			= last_costStr_1 * containingStr_2;
	var resin4			= last_costStr_3 * containingStr_4;
	var resin6			= last_costStr_5 * containingStr_6;
	var resin8			= last_costStr_7 * containingStr_8;
	
	var resiTot			= containingStr_9 * (resin2 + resin4 + resin6 + resin8);
	ChangePlusStrN2(resiTot);
	ChangePlusStrAddN2(resiTot);
	
	$('#last_costStrN2_9').val(RoundUp(resiTot));
	$('#last_fullStrN2_9').val(resiTot);

	$("#last_costStrN2_1").val(RoundUp(last_costStr_1));
	$("#last_costStrN2_3").val(RoundUp(last_costStr_3));
	$("#last_costStrN2_5").val(RoundUp(last_costStr_5));
	$("#last_costStrN2_7").val(RoundUp(last_costStr_7));
	
	$("#last_costStrN2_2").val(RoundUp(resin2));
	$("#last_costStrN2_4").val(RoundUp(resin4));
	$("#last_costStrN2_6").val(RoundUp(resin6));
	$("#last_costStrN2_8").val(RoundUp(resin8));
}

function ChangeAreaToEks(Area){
	var valueEks_1 		= getNum($('#valueEks_1').val());
	var valueEks_3 		= getNum($('#valueEks_3').val());
	var valueEks_5 		= getNum($('#valueEks_5').val());
	var valueEks_7 		= getNum($('#valueEks_7').val());
	
	var layerEks_1 		= getNum($('#layerEks_1').val());
	var layerEks_3 		= getNum($('#layerEks_3').val());
	var layerEks_5 		= getNum($('#layerEks_5').val());
	var layerEks_7 		= getNum($('#layerEks_').val());
	
	var containingEks_2	= getNum($('#containingEks_2').val());
	var containingEks_4	= getNum($('#containingEks_4').val());
	var containingEks_6	= getNum($('#containingEks_6').val());
	var containingEks_8	= getNum($('#containingEks_8').val());
	var containingEks_9	= getNum($('#containingEks_9').val());
	
	var EksFakVeil		= getNum($("#eks_faktor_veil").val());
	var EksFakVeilAdd	= getNum($("#eks_faktor_veil_add").val());
	var EksFakCsm		= getNum($("#eks_faktor_csm").val());
	var EksFakCsmAdd	= getNum($("#eks_faktor_csm_add").val());
	
	var last_cost_1 	= ((Area * valueEks_1 * layerEks_1)/1000) * EksFakVeil;
	var last_cost_3 	= ((Area * valueEks_3 * layerEks_3)/1000) * EksFakVeilAdd;
	var last_cost_5 	= ((Area * valueEks_5 * layerEks_5)/1000) * EksFakCsm;
	var last_cost_7 	= ((Area * valueEks_7 * layerEks_7)/1000) * EksFakCsmAdd; 
	
	if(isNaN(last_cost_7)){var last_cost_7 = 0;}
	
	var resin2			= last_cost_1 * containingEks_2;
	var resin4			= last_cost_3 * containingEks_4;
	var resin6			= last_cost_5 * containingEks_6;
	var resin8			= last_cost_7 * containingEks_8;
	
	var resiTot		= containingEks_9 * (resin2 + resin4 + resin6 + resin8);
	ChangePlusEks(resiTot);
	ChangePlusEksAdd(resiTot);
	
	$('#last_costEks_9').val(RoundUp(resiTot));
	$('#last_fullEks_9').val(resiTot);
	
	$("#last_costEks_1").val(RoundUp(last_cost_1));
	$("#last_costEks_3").val(RoundUp(last_cost_3));
	$("#last_costEks_5").val(RoundUp(last_cost_5));
	$("#last_costEks_7").val(RoundUp(last_cost_7));
	
	$("#last_costEks_2").val(RoundUp(resin2));
	$("#last_costEks_4").val(RoundUp(resin4));
	$("#last_costEks_6").val(RoundUp(resin6));
	$("#last_costEks_8").val(RoundUp(resin8));
}

function ChangePlusAdd(Area){
	var AddLinNum	= getNum($('#AddLinNum').val());
	var a;
	for(a=1; a <= AddLinNum; a++){
		var Con		= getNum($('#Addcontaining_'+a).val());
		var Per		= getNum($('#Addperse_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusStrAdd(Area){
	var AddStrNum	= getNum($('#AddStrNum').val());
	var a;
	for(a=1; a <= AddStrNum; a++){
		var Con		= getNum($('#Addcontaining2_'+a).val());
		var Per		= getNum($('#Addperse2_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost2_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusStrAddN1(Area){
	var AddStrNum	= getNum($('#AddStrNum').val());
	var a;
	for(a=1; a <= AddStrNum; a++){
		var Con		= getNum($('#Addcontaining2N1_'+a).val());
		var Per		= getNum($('#Addperse2N1_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost2N1_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusStrAddN2(Area){
	var AddStrNum	= getNum($('#AddStrNum').val());
	var a;
	for(a=1; a <= AddStrNum; a++){
		var Con		= getNum($('#Addcontaining2N2_'+a).val());
		var Per		= getNum($('#Addperse2N2_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost2N2_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusEksAdd(Area){
	var AddEksNum	= getNum($('#AddEksNum').val());
	var a;
	for(a=1; a <= AddEksNum; a++){
		var Con		= getNum($('#Addcontaining3_'+a).val());
		var Per		= getNum($('#Addperse3_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost3_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusTcAdd(Area){
	var AddTcNum	= getNum($('#AddTcNum').val());
	var a;
	for(a=1; a <= AddTcNum; a++){
		var Con		= getNum($('#Addcontaining4_'+a).val());
		var Per		= getNum($('#Addperse4_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost4_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}


//LINER
$(document).on('change', '.id_material', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '10'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_material2_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_ori_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containing_'+NoResin).val();
		var materialRs	= $('#id_material2_'+NoResin);
		
		var lastRes	= $('#last_cost_'+NoResin);
		
		var resinOri	= $('#id_material2_10').val();
		
		var resinX1	= $('#id_material2_3').val();
		var resinX2	= $('#id_material2_5').val();
		var resinX3	= $('#id_material2_7').val();
		var resinX4	= $('#id_material2_9').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri,
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_material2_3').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_material2_5').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_material2_7').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_material2_9').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});


$(document).on('keyup', '.layer', function(){
	
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #value_"+nomor).val());
	var containing		= parseFloat($('#containing_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0003' || type == 'TYP-0004'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}

	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor).val(thick_hide.toFixed(4))
	
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor).val());
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;
	
	console.log(layer);
	console.log(thick_hide);
	console.log(HslTotThick);
	
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
});

$(document).on('keyup', '.perse', function(){
	var TotResin	= parseFloat($('#last_cost_10').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseLinAdd', function(){
	var TotResin	= parseFloat($('#last_cost_10').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});



//STRUCTURE
$(document).on('change', '.id_materialSTr', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '13'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_materialStr2_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_oriStr_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingStr_'+NoResin).val();
		var materialRs	= $('#id_materialStr2_'+NoResin);
		
		var lastRes	= $('#last_costStr_'+NoResin);
		
		var resinOri	= $('#id_materialStr2_9').val();
		
		var resinX1	= $('#id_materialStr2_2').val();
		var resinX2	= $('#id_materialStr2_4').val();
		var resinX3	= $('#id_materialStr2_6').val();
		var resinX4	= $('#id_materialStr2_8').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri, 
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				// BW.val(data.bw);
				// Jumlah.val(data.jumRoov);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_materialStr2_2').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_materialStr2_4').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_materialStr2_6').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_materialStr2_8').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});

$(document).on('keyup', '.layerStr', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #valueStr_"+nomor).val());
	
	var containing		= parseFloat($('#containingStr_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0004' || type == 'TYP-0006'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}
	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val(thick_hide.toFixed(4))
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val());
	var oriMat			= $(this).parent().parent().find("td:nth-child(1) #id_oriStr_"+nomor).val();
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;

	// alert(thick_hide);
	
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
});

$(document).on('keyup', '.perseStr', function(){
	var TotResin	= parseFloat($('#last_costStr_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseStrAdd', function(){
	var TotResin	= parseFloat($('#last_costStr_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//STRUCTURE N1
$(document).on('change', '.id_materialSTrN1', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '13'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_materialStr2N1_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_oriStrN1_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thicknessStrN1_"+nomor);
		
		var BW			= $(this).parent().parent().find("td:nth-child(1) #bwStrN1_"+nomor);
		var Jumlah		= $(this).parent().parent().find("td:nth-child(1) #jumlahStrN1_"+nomor);
		
		var BW2			= $(this).parent().parent().find("td:nth-child(1) #bwStrN1_"+nomor).val();
		var Jumlah2		= $(this).parent().parent().find("td:nth-child(1) #jumlahStrN1_"+nomor).val();
		
		// console.log(BW);
		// console.log(Jumlah);
		
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingStrN1_'+NoResin).val();
		var materialRs	= $('#id_materialStrN1_'+NoResin);
		
		var lastRes	= $('#last_costStrN1_'+NoResin);
		
		var resinOri	= $('#id_materialStrN1_13').val();
		
		var resinX1	= $('#id_materialStrN1_2').val(); 
		var resinX2	= $('#id_materialStrN1_4').val();
		var resinX3	= $('#id_materialStrN1_6').val();
		var resinX4	= $('#id_materialStrN1_8').val();
		var resinX5	= $('#id_materialStrN1_10').val();
		var resinX6	= $('#id_materialStrN1_12').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri+"&bw="+BW2+"&jumlah="+Jumlah2,
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				BW.val(data.bw);
				Jumlah.val(data.jumRoov);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_materialStrN1_2').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_materialStrN1_4').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_materialStrN1_6').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_materialStrN1_8').val(data.resinUt);}
					if(resinX5 != 'MTL-1903000'){$('#id_materialStrN1_10').val(data.resinUt);}
					if(resinX6 != 'MTL-1903000'){$('#id_materialStrN1_12').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});

$(document).on('keyup', '.layerStrN1', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #valueStrN1_"+nomor).val());
	
	var BW				= parseFloat($(this).parent().parent().find("td:nth-child(1) #bwStrN1_"+nomor).val());
	var Jumlah			= parseFloat($(this).parent().parent().find("td:nth-child(1) #jumlahStrN1_"+nomor).val());
	
	var containing		= parseFloat($('#containingStrN1_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0004' || type == 'TYP-0006'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}
	if(type == 'TYP-0005'){
		var thick_hide	= ((berat/1000)/ BW * Jumlah * (2 / 2.56)) + ((berat/1000)/ BW * Jumlah * (2 / 1.2) * containing);
	}
	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thicknessStrN1_"+nomor).val(thick_hide.toFixed(4))
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val());
	var oriMat			= $(this).parent().parent().find("td:nth-child(1) #id_oriStrN1_"+nomor).val();
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;

	// alert(thick_hide);
	
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
});

$(document).on('keyup', '.perseStrN1', function(){
	var TotResin	= parseFloat($('#last_costStrN1_13').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseStrAddN1', function(){
	var TotResin	= parseFloat($('#last_costStrN1_13').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//STRUCTURE N2
$(document).on('change', '.id_materialSTrN2', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '13'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_materialStr2N2_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_oriStrN2_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thicknessStrN2_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingStrN2_'+NoResin).val();
		var materialRs	= $('#id_materialStrN2_'+NoResin);
		
		var lastRes	= $('#last_costStrN2_'+NoResin);
		
		var resinOri	= $('#id_materialStrN1_9').val();
		
		var resinX1	= $('#id_materialStrN1_2').val(); 
		var resinX2	= $('#id_materialStrN1_4').val();
		var resinX3	= $('#id_materialStrN1_6').val();
		var resinX4	= $('#id_materialStrN1_8').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri,
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				BW.val(data.bw);
				Jumlah.val(data.jumRoov);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_materialStrN2_2').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_materialStrN2_4').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_materialStrN2_6').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_materialStrN2_8').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});

$(document).on('keyup', '.layerStrN2', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #valueStrN2_"+nomor).val());
	
	var containing		= parseFloat($('#containingStrN2_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0004' || type == 'TYP-0006'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}
	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thicknessStrN2_"+nomor).val(thick_hide.toFixed(4))
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val());
	var oriMat			= $(this).parent().parent().find("td:nth-child(1) #id_oriStrN2_"+nomor).val();
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;

	// alert(thick_hide);
	
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
});

$(document).on('keyup', '.perseStrN1', function(){
	var TotResin	= parseFloat($('#last_costStrN2_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseStrAddN2', function(){
	var TotResin	= parseFloat($('#last_costStrN2_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//EXTERNAL
$(document).on('change', '.id_materialEks', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '9'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_material2Eks_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_oriEks_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thicknessEks_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingEks_'+NoResin).val();
		var materialRs	= $('#id_material2Eks_'+NoResin);
		
		var lastRes	= $('#last_costEks_'+NoResin);
		
		var resinOri	= $('#id_material2Eks_9').val();
		
		var resinX1	= $('#id_material2Eks_2').val();
		var resinX2	= $('#id_material2Eks_4').val();
		var resinX3	= $('#id_material2Eks_6').val();
		var resinX4	= $('#id_material2Eks_8').val();

		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri,
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_material2Eks_2').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_material2Eks_4').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_material2Eks_6').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_material2Eks_8').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});

$(document).on('keyup', '.layerEks', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #valueEks_"+nomor).val());
	var containing		= parseFloat($('#containingEks_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0003' || type == 'TYP-0004'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}
	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thicknessEks_"+nomor).val(thick_hide.toFixed(4))
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessEks_"+nomor).val());
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
});

$(document).on('keyup', '.perseEks', function(){
	var TotResin	= parseFloat($('#last_costEks_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

$(document).on('keyup', '.perseEksAdd', function(){
	var TotResin	= parseFloat($('#last_costEks_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(RoundUp(Hasil));
});

//TOPCOAT
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




//ADD TAMBAHAN
var nomor	= 1;

$('#add_liner').click(function(e){
	e.preventDefault();
	AppendBaris_Liner(nomor);
});

$(document).on('keyup', '.ChangeContaining', function(){
	var total_resin	= $('#last_cost_10').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerse', function(){
	var total_resin	= $('#last_cost_10').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});


$('#add_strukture').click(function(e){
	e.preventDefault();
	AppendBaris_Strukture(nomor);
});

$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_costStr_9').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_costStr_9').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});

$('#add_strukture_n1').click(function(e){
	e.preventDefault();
	AppendBaris_StruktureN1(nomor);
});

$(document).on('keyup', '.ChangeContainingStrN1', function(){
	var total_resin	= $('#last_costStrN1_13').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStrN1', function(){
	var total_resin	= $('#last_costStrN1_13').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});

$('#add_strukture_n2').click(function(e){
	e.preventDefault();
	AppendBaris_StruktureN2(nomor);
});

$(document).on('keyup', '.ChangeContainingStrN2', function(){
	var total_resin	= $('#last_costStrN2_9').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStrN2', function(){
	var total_resin	= $('#last_costStrN2_9').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});

$('#add_external').click(function(e){
	e.preventDefault();
	AppendBaris_External(nomor);
});

//EXTERNAL
$(document).on('keyup', '.ChangeContainingExt', function(){
	var total_resin	= $('#last_costEks_9').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseExt', function(){
	var total_resin	= $('#last_costEks_9').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val()/ 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(RoundUp(HasilAkhir));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});


$('#add_topcoat').click(function(e){
	e.preventDefault();
	AppendBaris_TopCoat(nomor);
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
		
function AppendBaris_Liner(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_liner').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_liner tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trliner_"+nomor+"'>";
		Rows 	+= 	"<td width = '15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Liner("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Liner["+nomor+"][last_full]' id='last_full_liner_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left' width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_Liner["+nomor+"][id_category]' id='id_category_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_Liner["+nomor+"][id_material]' id='id_material_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContaining' name='ListDetailAdd_Liner["+nomor+"][containing]' id='containing_liner_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerse' name='ListDetailAdd_Liner["+nomor+"][perse]' id='perse_liner_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_Liner["+nomor+"][last_cost]' id='last_cost_liner_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_liner').append(Rows);
	var id_category_liner_ 	= "#id_category_liner_"+nomor;
	var id_material_liner_ 	= "#id_material_liner_"+nomor;
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Strukture("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Strukture["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_Strukture["+nomor+"][id_category]' id='id_category_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_Strukture["+nomor+"][id_material]' id='id_material_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingStr' name='ListDetailAdd_Strukture["+nomor+"][containing]' id='containing_strukture_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseStr' name='ListDetailAdd_Strukture["+nomor+"][perse]' id='perse_strukture_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_Strukture["+nomor+"][last_cost]' id='last_cost_strukture_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_strukture').append(Rows);
	var id_category_strukture_ 	= "#id_category_strukture_"+nomor;
	var id_material_strukture_ 	= "#id_material_strukture_"+nomor;
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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

function AppendBaris_StruktureN1(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_strukture_n1').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_strukture_n1 tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trstruktureN1_"+nomor+"'>";
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_StruktureN1("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_StruktureN1["+nomor+"][last_full]' id='last_full_struktureN1_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_StruktureN1["+nomor+"][id_category]' id='id_category_struktureN1_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_StruktureN1["+nomor+"][id_material]' id='id_material_struktureN1_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingStrN1' name='ListDetailAdd_StruktureN1["+nomor+"][containing]' id='containing_struktureN1_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseStrN1' name='ListDetailAdd_StruktureN1["+nomor+"][perse]' id='perse_struktureN1_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_StruktureN1["+nomor+"][last_cost]' id='last_cost_struktureN1_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_strukture_n1').append(Rows);
	var id_category_strukture_ 	= "#id_category_struktureN1_"+nomor;
	var id_material_strukture_ 	= "#id_material_struktureN1_"+nomor;
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
	
	$("#id_category_struktureN1_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterial',
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

function AppendBaris_StruktureN2(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_strukture_n2').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_strukture_n2 tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trstruktureN2_"+nomor+"'>";
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_StruktureN2("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_StruktureN2["+nomor+"][last_full]' id='last_full_struktureN2_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_StruktureN2["+nomor+"][id_category]' id='id_category_struktureN2_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_StruktureN2["+nomor+"][id_material]' id='id_material_struktureN2_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingStrN2' name='ListDetailAdd_StruktureN2["+nomor+"][containing]' id='containing_struktureN2_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseStrN2' name='ListDetailAdd_StruktureN2["+nomor+"][perse]' id='perse_struktureN1_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_StruktureN2["+nomor+"][last_cost]' id='last_cost_struktureN2_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_strukture_n2').append(Rows);
	var id_category_strukture_ 	= "#id_category_struktureN2_"+nomor;
	var id_material_strukture_ 	= "#id_material_struktureN2_"+nomor;
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
	
	$("#id_category_struktureN2_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterial',
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
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_External("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_External["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_External["+nomor+"][id_category]' id='id_category_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_External["+nomor+"][id_material]' id='id_material_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingExt' name='ListDetailAdd_External["+nomor+"][containing]' id='containing_external_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseExt' name='ListDetailAdd_External["+nomor+"][perse]' id='perse_external_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_External["+nomor+"][last_cost]' id='last_cost_external_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_external').append(Rows);
	var id_category_external_ 	= "#id_category_external_"+nomor;
	var id_material_external_ 	= "#id_material_external_"+nomor;
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_TopCoat("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_TopCoat["+nomor+"][last_full]' id='last_full_topcoat_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingTC' name='ListDetailAdd_TopCoat["+nomor+"][containing]' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseTC' name='ListDetailAdd_TopCoat["+nomor+"][perse]' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_topcoat').append(Rows);
	var id_category_topcoat_ 	= "#id_category_topcoat_"+nomor;
	var id_material_topcoat_ 	= "#id_material_topcoat_"+nomor;
	
	$('.chosen_select').chosen();
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
function delRow_StruktureN1(row){
	$('#trstruktureN1_'+row).remove();
}
function delRow_StruktureN2(row){
	$('#trstruktureN2_'+row).remove();
}
function delRow_External(row){
	$('#trexternal_'+row).remove();
}
function delRow_TopCoat(row){
	$('#trtopcoat_'+row).remove();
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