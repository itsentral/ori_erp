
//key up thickness design 
$(document).on('keyup', '#top_tebal_design', function(){
	changeTop();
});

$(document).on('keyup', '#flange_od', function(){
	changeTop();
});

$(document).on('keyup', '#design_neck_1', function(){
	var designNeck2	= 2 * parseFloat($(this).val());
	if(isNaN(designNeck2)){  var designNeck2=0;}
	$('#design_neck_2').val(designNeck2.toFixed(1));
});

//key up edit top min tolerance
$(document).on('keyup', '#top_min_toleran', function(){
	var liner		= $('#acuhan_1').val();
	var struktur	= $('#acuhan_2').val();
	var external	= $('#acuhan_3').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi		= $('#top_max_toleran').val();
	var tot_lin_thickness	= $('#tot_lin_thickness').val();
	var tot_lin_thickness2	= $('#tot_lin_thickness2').val();
	var tot_lin_thickness3	= $('#tot_lin_thickness3').val(); 
	
	AcuhanMaxMin(liner, struktur, external, MinToleransi, MaxToleransi, tot_lin_thickness, tot_lin_thickness2, tot_lin_thickness3);
});

//key up edit top max tolerance
$(document).on('keyup', '#top_max_toleran', function(){
	var liner		= $('#acuhan_1').val();
	var struktur	= $('#acuhan_2').val();
	var external	= $('#acuhan_3').val();
	var MinToleransi	= $('#top_min_toleran').val();
	var MaxToleransi		= $('#top_max_toleran').val();
	var tot_lin_thickness	= $('#tot_lin_thickness').val();
	var tot_lin_thickness2	= $('#tot_lin_thickness2').val();
	var tot_lin_thickness3	= $('#tot_lin_thickness3').val();
	
	AcuhanMaxMin(liner, struktur, external, MinToleransi, MaxToleransi, tot_lin_thickness, tot_lin_thickness2, tot_lin_thickness3);
});
	
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
	}
});

//keuyp waste
$(document).on('keyup', '#waste', function(){
	var waste			= parseFloat($('#waste').val()) / 100;
	var top_diameter	= parseFloat($('#top_diameter').val());
	var top_thickness	= parseFloat($('#top_tebal_design').val());
	var angle			= parseFloat($('#angle').val());
	var flange_od		= parseFloat($('#flange_od').val());
	var area_neck_1		= parseFloat($('#area_neck_1').val());
	var panjang_neck_1	= parseFloat($('#panjang_neck_1').val());	
	var acuhan_1		= parseFloat($('#acuhan_1').val());
	
	var hasilAch2		= top_thickness - acuhan_1;
	$('#acuhan_2').val(hasilAch2.toFixed(1));
	
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
	
	//External Thickness
	var layer31			= $("#layer_veil3").val();
	var layer32			= $("#layer_veil_add3").val();
	var layer33			= $("#layer_matcsm3").val();
	var layer34			= $("#layer_csm_add3").val();
	var tot_thickness3	= parseFloat($('#tot_lin_thickness3').val());
	
	Hasil(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1);
	Hasil2(tot_thickness2, layer1, layer2, layer3, layer4, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1);
	Hasil3(tot_thickness3, layer31, layer32, layer33, layer34, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1);
	rubaharea();
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
			$('#top_diameter').val(data.pipeD);
			
			var ThisD = data.pipeD;
			if(ThisD <= 450){
				var angkaRoving	= 32/68;
			}
			else if(ThisD > 450){  
				var angkaRoving	= 28/72;
			}
			else{
				var angkaRoving	= 0;
			}
			var angkaVeil	= 9/1;
			var angkaCsm	= 7/3;
			var angkaCsmStr	= 60/40;
			var angkaWr		= 45/55;
			var angkaWrN2	= 5/5;
			
			if(ThisD < 25){
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
			}
			else{
				$('#mirror').hide();
				$('#plactic').show();
				
				$.ajax({
					url: base_url +'index.php/'+ active_controller+'/getPlasticMat',
					cache: false,
					type: "POST",
					// data: "cust="+this.value,
					dataType: "json",
					success: function(data){
						$("#mid_mtl_plastic").html(data.option).trigger("chosen:updated");
					}
				});
			}
			
			//Rooving
			$('#layer_resin25_neck1').val(angkaRoving.toFixed(3));
			$('#layer_resin26_neck1').val(angkaRoving.toFixed(3));
			
			//Veil
			$('#layer_resin1').val(angkaVeil.toFixed(3));
			$('#layer_resin2').val(angkaVeil.toFixed(3));
			$('#layer_resin31').val(angkaVeil.toFixed(3));
			$('#layer_resin32').val(angkaVeil.toFixed(3));
			
			
			//CSM
			$('#layer_resin3').val(angkaCsm.toFixed(3));
			$('#layer_resin4').val(angkaCsm.toFixed(3));
			$('#layer_resin33').val(angkaCsm.toFixed(3));
			$('#layer_resin34').val(angkaCsm.toFixed(3));
			$('#layer_resin21_neck1').val(angkaCsm.toFixed(3));
			$('#layer_resin22_neck1').val(angkaCsm.toFixed(3));
			$('#layer_resin21_neck2').val(angkaCsm.toFixed(3));
			$('#layer_resin22_neck2').val(angkaCsm.toFixed(3));
			
			//CSM Structure
			$('#layer_resin21').val(angkaCsm.toFixed(3));
			$('#layer_resin22').val(angkaCsm.toFixed(3));
			
			//WR
			$('#layer_resin23').val(angkaWrN2.toFixed(3));
			$('#layer_resin24').val(angkaWrN2.toFixed(3));
			$('#layer_resin23_neck1').val(angkaWr.toFixed(3));
			$('#layer_resin24_neck1').val(angkaWr.toFixed(3));
			$('#layer_resin23_neck2').val(angkaWrN2.toFixed(3));
			$('#layer_resin24_neck2').val(angkaWrN2.toFixed(3));
			
			var waste			= parseFloat($('#waste').val()) / 100;
			var top_diameter	= parseFloat($('#top_diameter').val());
			var top_thickness	= parseFloat($('#top_tebal_design').val());
			var flange_od		= parseFloat($('#flange_od').val());
			var area_neck_1		= parseFloat($('#area_neck_1').val());
			var panjang_neck_1	= parseFloat($('#panjang_neck_1').val());	
			var angle			= parseFloat($('#angle').val());

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
			
			//External Thickness
			var layer31			= $("#layer_veil3").val();
			var layer32			= $("#layer_veil_add3").val();
			var layer33			= $("#layer_matcsm3").val();
			var layer34			= $("#layer_csm_add3").val();
			var tot_thickness3	= parseFloat($('#tot_lin_thickness3').val());
			
			
			Hasil(tot_thickness1, layer_veil1, layer_veil2, layer_veil3, layer_veil4, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1, panjang_neck_1);
			Hasil2(tot_thickness2, layer1, layer2, layer3, layer4,top_diameter, top_thickness, waste, angle, flange_od, area_neck_1);
			Hasil3(tot_thickness3, layer31, layer32, layer33, layer34, top_diameter, top_thickness, waste, angle, flange_od, area_neck_1);
			rubaharea();
		}
	});
}); 