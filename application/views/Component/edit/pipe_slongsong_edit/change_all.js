
function getNum(val) {
   if (isNaN(val) || val == '') {
     return 0;
   }
   return parseFloat(val); 
}

function LuasArea(){
	var diameter	= getNum($('#diameter').val());
	var lengthx		= getNum($('#length').val());
	var waste		= getNum($('#waste').val());
	var estimasi	= Estimasi();
	
	var Luas_Area_Rumus		= ((3.14/1000)*(diameter + estimasi))*(lengthx/1000)*(1+waste);
	if(isNaN(Luas_Area_Rumus)){
		var Luas_Area_Rumus = 0;
	}
	return Luas_Area_Rumus;
}

function Estimasi(){
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	
	var topEST	= thickLin + thickStr;
	if(isNaN(topEST)){
		var topEST = 0;
	}
	return topEST;
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
	var totthicknessStr5	= getNum($('#total_thicknessStr_9').val());
	var totthicknessStr6	= getNum($('#total_thicknessStr_11').val());
	var AllThickStr			= totthicknessStr1 + totthicknessStr2 + totthicknessStr3 + totthicknessStr4 + totthicknessStr5 + totthicknessStr6;
	$('#thickStr').val(AllThickStr.toFixed(4));
}

function ChangeHasil(){
	
	var ThLin		= getNum($('#ThLin').val());
	var ThStr		= getNum($('#ThStr').val());
	var minToleran	= getNum($('#min_toleran').val());
	var maxToleran	= getNum($('#max_toleran').val());
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	
	var minLinThk	= ThLin - (ThLin * minToleran);
	var maxLinThk	= ThLin + (ThLin * maxToleran);
	var minStrThk	= ThStr - (ThStr * minToleran);
	var maxStrThk	= ThStr + (ThStr * maxToleran);
	if(isNaN(minLinThk)){ var minLinThk = 0;}
	if(isNaN(maxLinThk)){ var maxLinThk = 0;}
	if(isNaN(minStrThk)){ var minStrThk = 0;}
	if(isNaN(maxStrThk)){ var maxStrThk = 0;}
	
	if(thickLin < minLinThk){var Hasil1	= "TOO LOW";}
	if(thickLin > maxLinThk){var Hasil1	= "TOO HIGH";}
	if(thickLin > minLinThk && thickLin < maxLinThk){var Hasil1	= "OK";}
	$('#minLin').val(minLinThk.toFixed(4));
	$('#maxLin').val(maxLinThk.toFixed(4));
	// alert(Hasil1);
	$('#hasilLin').val(Hasil1);
	
	if(thickStr < minStrThk){var Hasil2	= "TOO LOW";}
	if(thickStr > maxStrThk){var Hasil2	= "TOO HIGH";}
	if(thickStr > minStrThk && thickStr < maxStrThk){var Hasil2	= "OK";}
	$('#minStr').val(minStrThk.toFixed(4));
	$('#maxStr').val(maxStrThk.toFixed(4));
	$('#hasilStr').val(Hasil2);
}

function ChangeLuasArea(){
	
	var estimasi 	= Estimasi();
	var LuasAreaX 	= LuasArea();
	var LastCoat	= LuasAreaX * 0.25 * 1.2;
	
	if(isNaN(LastCoat)){ var LastCoat = 0;}
	$('#last_topcoat_1').val(LastCoat.toFixed(3));

	$('#estimasi').val(estimasi.toFixed(4));
	$('#area').val(LuasAreaX.toFixed(6));
	
	ChangeAreaToLiner(LuasAreaX);
	ChangeAreaToStr(LuasAreaX);
	
	AllThickness();
}

function LastWeight(){
	var area	= getNum($('#area').val());
	return area;
}

function ChangePlus(Area){
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
	
	var id_material_1 	= $('#id_material_1').val();
	var diameter		= $('#diameter').val();
	if(diameter < 25){var Hit = 800;}else{var Hit = 1350;}
	
	var last_cost_1 	= Area * value_1 * 1.5 * Hit ;
	var last_cost_2 	= (Area * value_2 * layer_2)/1000 ;
	var last_cost_4 	= (Area * value_4 * layer_4)/1000 ;
	var last_cost_6 	= ((Area * value_6 * layer_6)/1000) * 1.1;
	var last_cost_8 	= ((Area * value_8 * layer_8)/1000) * 1.1; 
	var resin3			= last_cost_2 * containing_3;
	var resin5			= last_cost_4 * containing_5;
	var resin7			= last_cost_6 * containing_7;
	var resin9			= last_cost_8 * containing_9;
	
	if(resin3 == 0 && resin5 == 0 && resin7 == 0 && resin9 == 0){
		var resiTot		= 0;
	}
	else{
		var resiTot		= (Area * 1.2 * 0.3) + resin3 + resin5 + resin7 + resin9;
	}
	ChangePlus(resiTot);
	ChangePlusAdd(resiTot);
	
	$('#last_cost_10').val(resiTot.toFixed(3));
	$('#last_full_10').val(resiTot);
	
	$("#last_cost_1").val(last_cost_1.toFixed(3));
	$("#last_cost_2").val(last_cost_2.toFixed(3));
	$("#last_cost_4").val(last_cost_4.toFixed(3));
	$("#last_cost_6").val(last_cost_6.toFixed(3));
	$("#last_cost_8").val(last_cost_8.toFixed(3));
	$("#last_cost_3").val(resin3.toFixed(3));
	$("#last_cost_5").val(resin5.toFixed(3));
	$("#last_cost_7").val(resin7.toFixed(3));
	$("#last_cost_9").val(resin9.toFixed(3));
}

function ChangeAreaToStr(Area){
	var valueStr_1 		= getNum($('#valueStr_1').val());
	var valueStr_3 		= getNum($('#valueStr_3').val());
	var valueStr_5 		= getNum($('#valueStr_5').val());
	var valueStr_7 		= getNum($('#valueStr_7').val());
	var valueStr_9 		= getNum($('#valueStr_9').val());
	var valueStr_11 	= getNum($('#valueStr_11').val());
	
	var layerStr_1 		= getNum($('#layerStr_1').val());
	var layerStr_3 		= getNum($('#layerStr_3').val());
	var layerStr_5 		= getNum($('#layerStr_5').val());
	var layerStr_7 		= getNum($('#layerStr_7').val());
	var layerStr_9 		= getNum($('#layerStr_9').val());
	var layerStr_11 	= getNum($('#layerStr_11').val());
	
	var containingStr_2		= getNum($('#containingStr_2').val());
	var containingStr_4		= getNum($('#containingStr_4').val());
	var containingStr_6		= getNum($('#containingStr_6').val());
	var containingStr_8		= getNum($('#containingStr_8').val());
	var containingStr_10	= getNum($('#containingStr_10').val());
	var containingStr_12	= getNum($('#containingStr_12').val());
	
	var bwStr_9 		= getNum($('#bwStr_9').val());
	var jumlahStr_9 	= getNum($('#jumlahStr_9').val());
	var bwStr_11 		= getNum($('#bwStr_11').val());
	var jumlahStr_11 	= getNum($('#jumlahStr_11').val());
	
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
	
	var last_costStr_1 		= (Area * valueStr_1 * layerStr_1)/1000 ;
	var last_costStr_3 		= (Area * valueStr_3 * layerStr_3)/1000 ;
	var last_costStr_5 		= (Area * valueStr_5 * layerStr_5)/1000 ;
	var last_costStr_7 		= (Area * valueStr_7 * layerStr_7)/1000 ; 
	var last_costStr_9 		= ((valueStr_9 * 0.001 * jumlahStr_9 * 100)/(bwStr_9/10)) * (2/1000) * layerStr_9 * Area * kali2;
	var last_costStr_11 	= ((valueStr_11 * 0.001 * jumlahStr_11 * 100)/(bwStr_11/10)) * (2/1000) * layerStr_11 * Area * kali2;
	
	if(isNaN(last_costStr_9)){var last_costStr_9 = 0;}
	if(isNaN(last_costStr_11)){var last_costStr_11 = 0;}
	
	var resin2			= last_costStr_1 * containingStr_2;
	var resin4			= last_costStr_3 * containingStr_4;
	var resin6			= last_costStr_5 * containingStr_6;
	var resin8			= last_costStr_7 * containingStr_8;
	var resin10			= last_costStr_9 * containingStr_10 * kali;
	var resin12			= last_costStr_11 * containingStr_12 * kali;
	
	var resiTot			= resin2 + resin4 + resin6 + resin8 + resin10 + resin12;
	ChangePlusStr(resiTot); 
	ChangePlusStrAdd(resiTot);
	
	$('#last_costStr_13').val(resiTot.toFixed(3));
	$('#last_fullStr_13').val(resiTot);

	$("#last_costStr_1").val(last_costStr_1.toFixed(3));
	$("#last_costStr_3").val(last_costStr_3.toFixed(3));
	$("#last_costStr_5").val(last_costStr_5.toFixed(3));
	$("#last_costStr_7").val(last_costStr_7.toFixed(3));
	$("#last_costStr_9").val(last_costStr_9.toFixed(3));
	$("#last_costStr_11").val(last_costStr_11.toFixed(3));
	
	$("#last_costStr_2").val(resin2.toFixed(3));
	$("#last_costStr_4").val(resin4.toFixed(3));
	$("#last_costStr_6").val(resin6.toFixed(3));
	$("#last_costStr_8").val(resin8.toFixed(3));
	$("#last_costStr_10").val(resin10.toFixed(3));
	$("#last_costStr_12").val(resin12.toFixed(3));
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
