<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Customer Name<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<select name='id_customer' id='id_customer' class='form-control input-md'>
							<option value=''>Select An Customer</option>
						<?php
							foreach($customer AS $val => $valx){
								echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
							}
						?>
						</select>						
					</div>
				
				<label class='label-control col-sm-2'><b>Machine Name</b></label>
				<div class='col-sm-4'>
					<select name='id_mesin' id='id_mesin' class='form-control input-md'>
							<option value=''>Select An Machine</option>
						<?php
							foreach($machine AS $val => $valx){
								echo "<option value='".$valx['id_mesin']."'>".strtoupper($valx['nm_mesin'])."</option>";
							}
						?>
						</select>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Start Plan Production</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'plan_start_produksi','name'=>'plan_start_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Start Plan Production', 'readonly'=>'readonly', 'style'=>'cursor:pointer;'));											
					?>	
				</div>
				<label class='label-control col-sm-2'><b>End Plan Production</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'plan_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'End Plan Production', 'readonly'=>'readonly', 'style'=>'cursor:pointer;'));											
					?>	
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Information</b></label>
				<div class='col-sm-4'>
					 <?php
						// echo form_hidden('id',$row[0]->kode_divisi);
						echo form_textarea(array('id'=>'ket','name'=>'ket','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Production Information'));
					?>
				</div>
			</div>
			<div class="box box-success">
				<div class="box-header">
					<h3 class="box-title">List Product</h3>
					<input type='hidden' name='numberMax' id='numberMax' value='0'>
					<input type='hidden' name='numberMaxSpool' id='numberMaxSpool' value='0'>
					<input type='hidden' name='numberMaxComp' id='numberMaxComp' value='0'>
					<input type='hidden' name='numberHelp1' id='numberHelp1' value='0'>
					<input type='hidden' name='numberHelp2' id='numberHelp2' value='0'>
				</div>
				<div class="box-body">
					<button type="button" id='add_komponen' style='width:130px; margin-right:0px; margin-bottom:3px; margin-left:5px; float:right;' class="btn btn-success btn-sm">Add Component</button>
					<button type="button" id='add_product' style='width:130px; margin-right:0px; margin-bottom:3px; float:right;' class="btn btn-success btn-sm">Add Spool</button>
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead id='head_table'>
							<tr class='bg-blue'>
								<th class="text-center" style='width: 5%;'class="no-sort">No</th>
								<th class="text-center" style='width: 15%;'>Delivery Type</th>
								<th class="text-center" style='width: 25%;'>Product Type</th>
								<th class="text-center" style='width: 35%;'>Product Name</th>
								<th class="text-center" style='width: 10%;'>Qty</th>
								<th class="text-center" style='width: 10%;'>Option</th>
							</tr>
						</thead>
						<tbody id='detail_body'>
						</tbody>
						<!--
						<tbody id='detail_body_1'>
						</tbody>
						-->
						<tbody id='detail_body_Kosong'>
							<tr>
								<td colspan='7'>Product list empty ...</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		 </div>

		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<style type="text/css">
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#plan_start_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$('#plan_end_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		var nomor	= 1;
		var nomorKQ	= 1;
		
		$('#add_product').click(function(e){
			e.preventDefault();
			var nilaiAwalSpool	= parseInt($("#numberMaxSpool").val());
			var nilaiAkhirSpool	= nilaiAwalSpool + 1;
			$("#numberMaxSpool").val(nilaiAkhirSpool);
			
			AppendBaris(nomor, nilaiAkhirSpool);
			$('#head_table').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax").val(nilaiAkhir);
			$("#detail_body_Kosong").hide();
			$('#simpan-bro').show();
		});
		
		$('#add_komponen').click(function(e){
			e.preventDefault();
			var nilaiAwalComp	= parseInt($("#numberMaxComp").val());
			var nilaiAkhirComp	= nilaiAwalComp + 1;
			$("#numberMaxComp").val(nilaiAkhirComp);
			
			AppendBarisKomponen(nomor, nilaiAkhirComp);
			$('#head_table').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax").val(nilaiAkhir);
			$("#detail_body_Kosong").hide();
			$('#simpan-bro').show();
		});
		

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var id_customer			= $('#id_customer').val();
			var plan_start_produksi	= $('#plan_start_produksi').val();
			var plan_end_produksi	= $('#plan_end_produksi').val();
			var mulai_produksi		= $('#mulai_produksi').val();
			var id_mesin			= $('#id_mesin').val();
			var ket					= $('#ket').val();
			var numberMax			= $('#numberMax').val();

			if(id_customer=='' || id_customer==null || id_customer=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Customer Name, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(id_mesin == '' || id_mesin == null || id_mesin == '-' || id_mesin == '0'){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Machine, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(plan_start_produksi=='' || plan_start_produksi==null || plan_start_produksi=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Start Plan Production, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(plan_end_produksi=='' || plan_end_produksi==null || plan_end_produksi=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'End Plan Production, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(ket == '' || ket == null || ket == '-' || ket == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Information, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(new Date(plan_start_produksi) > new Date(plan_end_produksi)){
				swal({
				  title	: "Error Message!",
				  text	: 'Date of the planned completion of production must be more than the date of the planned start of production ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(numberMax == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Product list is still empty ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			// var intL = 0;
			// var intError = 0;
			// var pesan = '';
			
			// $('#detail_body').find('tr').each(function(){
				// intL++;
				// var findId	= $(this).attr('id');
				// var nomor	= findId.split('_');
				// var qty				= $('#qty_'+nomor[1]).val();
				// var id_product		= $('#id_product_'+nomor[1]).val();
				// var id_category		= $('#id_category_'+nomor[1]).val();
				
				
				// if(qty == '' || qty == 0 || qty == null){
					// intError++;
					// pesan = "Number "+nomor[1]+" : Qty has not empty ...";
				// }
				
				// if(id_product == '' || id_product == 0 || id_product == null){
					// intError++;
					// pesan = "Number "+nomor[1]+" : Product name has not empty ...";
				// }
				
				// if(id_category == '' || id_category == 0 || id_category == null){
					// intError++;
					// pesan = "Number "+nomor[1]+" : Product type has not empty ...";
				// }
			// });
			
			// if(intError > 0){
				// alert(pesan);
				// swal({
					// title				: "Notification Message !",
					// text				: pesan,						
					// type				: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			
			$('#simpan-bro').prop('disabled',false);

			swal({
				  title: "Are you sure?",
				  text: "You will not be able to process again this data!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonClass: "btn-danger",
				  confirmButtonText: "Yes, Process it!",
				  cancelButtonText: "No, cancel process!",
				  closeOnConfirm: false,
				  closeOnCancel: false
				},
				function(isConfirm) {
				  if (isConfirm) {
						loading_spinner();
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/add2';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: formData,
							cache		: false,
							dataType	: 'json',
							processData	: false,
							contentType	: false,
							success		: function(data){
								if(data.status == 1){
									swal({
										  title	: "Save Success!",
										  text	: data.pesan,
										  type	: "success",
										  timer	: 7000
										});
									window.location.href = base_url + active_controller;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#simpan-bro').prop('disabled',false);
							},
							error: function() {

								swal({
								  title				: "Error Message !",
								  text				: 'An Error Occured During Process. Please try again..',
								  type				: "warning",
								  timer				: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});
	});
	
	function delRow(row){
		$('#tr_'+row).remove();
		$('#trBody_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax").val() - 1;
		$("#numberMax").val(updatemax);
		
		var maxLine = $("#numberMax").val();
		if(maxLine == 0){
			// $('#head_table').hide();
			$("#detail_body_Kosong").show();
			// $('#simpan-bro').hide();
		}
	}
	
	function delRowKom(row, row2){
		$('#tr_'+row+'_'+row2).remove();
		$('#trBodySub_'+row+'_'+row2).remove();

		var updatemax	=	$('#numberMax_'+row+'_'+row2).val() - 1;
		$('#numberMax'+row+'_'+row2).val(updatemax);
	}
	
	function delRowKom2(row, row2){
		$('#tr_'+row+'_'+row2).remove();
	}
	
	function delRowKomSub(row, row2, row3){
		$('#tr_'+row+'_'+row2+'_'+row3).remove();
	}
	
	function AppendBarisKomponen(intd, nilComp)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = 	"<tr id='tr_"+nomor+"'>"; 
			Rows	+= 		"<td>";
			Rows	+= 			"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows	+=				"<button type='button' style='min-width:140px; margin-left: 5px;' id='add_component2_"+nomor+"' data-pluskom='"+nomor+"' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Component'>Add Component</button>&nbsp;";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' colspan='3' style='background-color: #232323b0; color: white; text-align: center; vertical-align: bottom; font-size: 16px;'>";
			Rows	+=			"<label>COMPONENT "+nilComp+"</label>";
			Rows	+=			"<input type='hidden' style='color:black !important;' id='numberMax_"+nomor+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows 	+=				"<button type='button' style='min-width:100px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRow("+nomor+")' title='Delete Record'>Deleted Row</button>";
			Rows	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBody_"+nomor+"'>";
			Rows 	+= 		"<td colspan='6' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_body_"+nomor+"'></tbody>";
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		$('#detail_body').append(Rows);
		
		var add_component2_ 	= "#add_component2_"+nomor;
		var numberMax_ 	= "#numberMax_"+nomor;
		
		$(document).on('click', add_component2_, function(e){
			e.preventDefault();
			var dataKom	= $(this).data('pluskom');
			var nilaiAwal	= parseInt($(numberMax_).val());
			var nilaiAkhir	= nilaiAwal + 1;
			$(numberMax_).val(nilaiAkhir);
			
			var nilaiUrut1	= parseInt($('#numberHelp1').val());
			var hasilUrut1	= nilaiUrut1 + 1;
			$('#numberHelp1').val(hasilUrut1);
			AppendBarisKom2(dataKom, nilaiAkhir, nilComp, hasilUrut1);
			$('.chosen_select').chosen({width: '100%'});
		});
		
		nomor++;
	}
	
	function AppendBarisKom2(intd, num, nilComp, hasilUrut1){
		var nomorK	= 1;
		var valuex	= $('#detail_body_'+nomorK).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_'+nomorK+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr_"+intd+"_"+num+"'>"; 
			Rows	+= 		"<td style='width: 5%;'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 15%;' align='center'>"; 
			Rows	+=			"<input type='text' name='ListDetailKompSingle["+hasilUrut1+"][id_delivery]' id='id_delivery_"+intd+"_"+num+"' class='form-control input-sm' value='component_"+nilComp+"'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 25%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSingle["+hasilUrut1+"][id_category]' id='id_category_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 35%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSingle["+hasilUrut1+"][id_product]' id='id_product_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 10%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][qty]' id='qty_"+intd+"_"+num+"' maxlength='3' required autocomplete='off'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 10%;' align=\"center\">";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:100px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRowKom2("+intd+","+num+")' title='Delete Record'>Deleted Row</button>";
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
			
		
		// alert('#detail_body_'+intd);
		// alert(Rows);
		$('#detail_body_'+intd).append(Rows);
		
		var K_id_category = "#id_category_"+intd+"_"+num;
		var K_id_product = "#id_product_"+intd+"_"+num;

		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProduct',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(K_id_category).html(data.option).trigger("chosen:updated");
			}
		});
		
		
		$(K_id_category).on('change', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getProduct',
				cache: false,
				type: "POST",
				data: "category="+$(this).val(),
				dataType: "json",
				success: function(data){
					$(K_id_product).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			$(this).val($(this).val().replace(/[^\d].+/, ""));
			if (event.which < 48 || event.which > 57) {
				event.preventDefault();
			}
		});
		nomorK++;
		num++;
	}
	
	function AppendBaris(intd, NilSpool)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = 	"<tr id='tr_"+nomor+"'>"; 
			Rows	+= 		"<td>";
			Rows	+= 			"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows	+=				"<button type='button' style='min-width:140px; margin-left: 5px;' id='add_component_"+nomor+"' data-pluskom='"+nomor+"' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Component'>Add Component</button>&nbsp;";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' colspan='3' style='background-color: #232323b0; color: white; text-align: center; vertical-align: bottom; font-size: 16px;'>";
			Rows	+=			"<label>SPOOL "+NilSpool+"</label>";
			Rows	+=			"<input type='hidden' style='color:black !important;' id='numberMax_"+nomor+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows 	+=				"<button type='button' style='min-width:100px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRow("+nomor+")' title='Delete Record'>Deleted Row</button>";
			Rows	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBody_"+nomor+"'>";
			Rows 	+= 		"<td colspan='6' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_body_"+nomor+"'></tbody>";
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		$('#detail_body').append(Rows);
		
		var add_component_ 	= "#add_component_"+nomor;
		var numberMax_ 	= "#numberMax_"+nomor;
		
		$(document).on('click', add_component_, function(e){
			e.preventDefault();
			var dataKom	= $(this).data('pluskom');
			var nilaiAwal	= parseInt($(numberMax_).val());
			var nilaiAkhir	= nilaiAwal + 1;
			$(numberMax_).val(nilaiAkhir);
			
			var nilaiUrut1	= parseInt($('#numberHelp1').val());
			var hasilUrut1	= nilaiUrut1 + 1;
			$('#numberHelp1').val(hasilUrut1);
			
			AppendBarisKom(dataKom, nilaiAkhir, NilSpool, hasilUrut1);
			$('.chosen_select').chosen({width: '100%'});
		});
		
		nomor++;
	}
	
	function AppendBarisKom(intd, num, NilSpool, hasilUrut1){
		var nomorK	= 1;
		var valuex	= $('#detail_body_'+nomorK).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_'+nomorK+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr_"+intd+"_"+num+"'>"; 
			Rows	+= 		"<td style='width: 5%;'>";
			Rows	+= 			"<div style='text-align: center;'><input type='hidden' style='color:black !important;' id='numberMax_"+intd+"_"+num+"' value='0'></div>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 15%;' align='center'>";
			Rows	+=			"<button type='button' id='sub_komponen_"+intd+"_"+num+"' data-pluskom1='"+intd+"' data-pluskom2='"+num+"' style='min-width:140px;' class='btn btn-success btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Sub Component'>Add Sub Component</button>";
			Rows	+=			"<input type='text' name='ListDetailKomp["+hasilUrut1+"][id_delivery]' id='id_delivery_"+intd+"_"+num+"' class='form-control input-sm' value='spool_"+NilSpool+"'>";
			Rows	+=			"<input type='text' name='ListDetailKomp["+hasilUrut1+"][sub_delivery]' id='sub_delivery_"+intd+"_"+num+"' class='form-control input-sm' value='subkomponen_"+num+"'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 25%;' align='left'>";
			Rows	+=			"<select name='ListDetailKomp["+hasilUrut1+"][id_category]' id='id_category_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 35%;' align='left'>";
			Rows	+=			"<select name='ListDetailKomp["+hasilUrut1+"][id_product]' id='id_product_"+intd+"_"+num+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 10%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly' style='text-align: center;' name='ListDetailKomp["+hasilUrut1+"][qty]' id='qty_"+intd+"_"+num+"' maxlength='3' required autocomplete='off'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 10%;' align=\"center\">";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:100px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRowKom("+intd+","+num+")' title='Delete Record'>Deleted Row</button>";
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBodySub_"+intd+"_"+num+"'>";
			Rows 	+= 		"<td colspan='6' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_bodysub_"+intd+"_"+num+"'></tbody>";
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		// alert('#detail_body_'+intd);
		// alert(Rows);
		$('#detail_body_'+intd).append(Rows);
		
		var K_id_category = "#id_category_"+intd+"_"+num;
		var K_id_product = "#id_product_"+intd+"_"+num;
		var K_sub_komponen = "#sub_komponen_"+intd+"_"+num;
		var K_NumberMax = "#numberMax_"+intd+"_"+num;
		
		$(document).on('click', K_sub_komponen, function(e){
			e.preventDefault();
			var dataKom1	= $(this).data('pluskom1');
			var dataKom2	= $(this).data('pluskom2');
			
			var nilaiAwal	= parseInt($(K_NumberMax).val());
			var nilaiAkhir	= nilaiAwal + 1;
			$(K_NumberMax).val(nilaiAkhir);
			
			var nilaiUrut2	= parseInt($('#numberHelp2').val());
			var hasilUrut2	= nilaiUrut2 + 1;
			$('#numberHelp2').val(hasilUrut2);
			
			AppendBarisKomSub(dataKom1, dataKom2, nilaiAkhir, NilSpool, hasilUrut2);
			$('.chosen_select').chosen({width: '100%'});
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProduct',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(K_id_category).html(data.option).trigger("chosen:updated");
			}
		});
		
		
		$(K_id_category).on('change', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getProduct',
				cache: false,
				type: "POST",
				data: "category="+$(this).val(),
				dataType: "json",
				success: function(data){
					$(K_id_product).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			$(this).val($(this).val().replace(/[^\d].+/, ""));
			if (event.which < 48 || event.which > 57) {
				event.preventDefault();
			}
		});
		nomorK++;
		num++;
	}
	
	function AppendBarisKomSub(intd, intd2, intd3, NilSpool, hasilUrut2)
	{
		// alert("Ye sampai");
		// return false;
		var nomorK	= 1;
		var valuex	= $('#detail_bodysub_'+intd+'_'+intd2).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_bodysub_'+intd+'_'+intd2+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr_"+intd+"_"+intd2+"_"+intd3+"'>"; 
			Rows	+= 		"<td style='width: 5%;'>";
			Rows	+= 			"<div style='text-align: center;'></div>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 15%;' align='center'>";
			Rows	+=			"<input type='text' name='ListDetailKompSub["+hasilUrut2+"][id_delivery]' id='id_delivery_"+intd+"_"+intd2+"_"+intd3+"' class='form-control input-sm' value='spool_"+NilSpool+"'>";
			Rows	+=			"<input type='text' name='ListDetailKompSub["+hasilUrut2+"][sub_delivery]' id='sub_delivery_"+intd+"_"+intd2+"_"+intd3+"' class='form-control input-sm' value='subkomponen_"+intd2+"'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 25%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub["+hasilUrut2+"][id_category]' id='id_category_"+intd+"_"+intd2+"_"+intd3+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 35%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub["+hasilUrut2+"][id_product]' id='id_product_"+intd+"_"+intd2+"_"+intd3+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 10%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][qty]' id='qty_"+intd+"_"+intd2+"_"+intd3+"' maxlength='3' required autocomplete='off'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 10%;' align=\"center\">";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:100px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRowKomSub("+intd+","+intd2+","+intd3+")' title='Delete Record'>Deleted Row</button>";
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
		
		// alert('#detail_bodysub_'+intd+'_'+intd2);
		// alert(Rows);
		// return false;
		$('#detail_bodysub_'+intd+'_'+intd2).append(Rows);
		
		var KSUb_id_category = "#id_category_"+intd+"_"+intd2+"_"+intd3;
		var KSUb_id_product = "#id_product_"+intd+"_"+intd2+"_"+intd3;
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProduct',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(KSUb_id_category).html(data.option).trigger("chosen:updated");
			}
		});
		
		
		$(KSUb_id_category).on('change', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getProduct',
				cache: false,
				type: "POST",
				data: "category="+$(this).val(),
				dataType: "json",
				success: function(data){
					$(KSUb_id_product).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			$(this).val($(this).val().replace(/[^\d].+/, ""));
			if (event.which < 48 || event.which > 57) {
				event.preventDefault();
			}
		});
		nomorK++;
	}
	
	
</script>
