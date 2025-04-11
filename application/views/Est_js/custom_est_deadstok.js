
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
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_Liner["+nomor+"][id_category]' id='id_category_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_Liner["+nomor+"][id_material]' id='id_material_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control numberOnly5' name='ListDetailAdd_Liner["+nomor+"][last_cost]' id='last_cost_liner_"+nomor+"' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_liner').append(Rows);
	var id_category_liner_ 	= "#id_category_liner_"+nomor;
	var id_material_liner_ 	= "#id_material_liner_"+nomor;
	
    $('.chosen_select').chosen();
	$(".numberOnly5").autoNumeric('init', {mDec: '5', aPad: false});
	
	
	$.ajax({
		url: base_url + 'cust_component/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_liner_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
		}
	});
	
	$("#id_category_liner_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url + 'cust_component/getMaterial',
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
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_Strukture["+nomor+"][id_category]' id='id_category_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_Strukture["+nomor+"][id_material]' id='id_material_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control numberOnly5' name='ListDetailAdd_Strukture["+nomor+"][last_cost]' id='last_cost_strukture_"+nomor+"' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_strukture').append(Rows);
	var id_category_strukture_ 	= "#id_category_strukture_"+nomor;
	var id_material_strukture_ 	= "#id_material_strukture_"+nomor;
	
	$('.chosen_select').chosen();
	$(".numberOnly5").autoNumeric('init', {mDec: '5', aPad: false});
	
	$.ajax({
		url: base_url + 'cust_component/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_strukture_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
		}
	});
	
	$("#id_category_strukture_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url + 'cust_component/getMaterial',
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
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_External["+nomor+"][id_category]' id='id_category_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_External["+nomor+"][id_material]' id='id_material_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control numberOnly5' name='ListDetailAdd_External["+nomor+"][last_cost]' id='last_cost_external_"+nomor+"' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_external').append(Rows);
	var id_category_external_ 	= "#id_category_external_"+nomor;
	var id_material_external_ 	= "#id_material_external_"+nomor;
	
	$('.chosen_select').chosen();
	$(".numberOnly5").autoNumeric('init', {mDec: '5', aPad: false});
	
	$.ajax({
		url: base_url + 'cust_component/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_external_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
		}
	});
	
	$("#id_category_external_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url + 'cust_component/getMaterial',
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
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left'  width = '250px'>";
		Rows	+=		"Category<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"Material<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='150px'>";
		Rows	+=		"Value /Kg<input type='text' class='form-control numberOnly5' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' id='last_cost_topcoat_"+nomor+"' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_topcoat').append(Rows);
	var id_category_topcoat_ 	= "#id_category_topcoat_"+nomor;
	var id_material_topcoat_ 	= "#id_material_topcoat_"+nomor;
	
	$('.chosen_select').chosen();
	$(".numberOnly5").autoNumeric('init', {mDec: '5', aPad: false});
	
	$.ajax({
		url: base_url + 'cust_component/getCategory',
		cache: false,
		type: "POST",
		dataType: "json",
		success: function(data){
			$(id_category_topcoat_).html(data.option).trigger("chosen:updated");
            $('.chosen_select').chosen();
		}
	});
	
	$("#id_category_topcoat_"+nomor+"").on('change', function(e){
		e.preventDefault();
		$.ajax({
			url: base_url + 'cust_component/getMaterial',
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