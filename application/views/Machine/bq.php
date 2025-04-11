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
				<label class='label-control col-sm-2'><b>IPP Number<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='no_ipp' id='no_ipp' class='form-control input-md'>
						<option value=''>Select An IPP</option> 
					<?php
						foreach($ListIPP AS $val => $valx){
							echo "<option value='".$valx['no_ipp']."'>".$valx['no_ipp']."</option>";
						}
					?>
					</select>						
				</div>
				
				<label class='label-control col-sm-2'><b>Order Type<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='order_type' id='order_type' class='form-control input-md'>
					<?php
						foreach($ListOrder AS $val => $valx){
							echo "<option value='".$valx['name']."'>".$valx['name']."</option>";
						}
					?>
					</select>						
				</div>
			</div>
			
			<div class="box box-success">
				<div class="box-header">
					<h3 class="box-title">List Product</h3>
					<input type='hidden' name='numberMax' id='numberMax' value='0'>
					<input type='hidden' name='numberHelpHide' id='numberHelpHide' value='0'> 
					<input type='hidden' name='numberMaxSpool' id='numberMaxSpool' value='0'> 
					<input type='hidden' name='numberMaxComp' id='numberMaxComp' value='0'>
					<input type='hidden' name='numberHelp1' id='numberHelp1' value='0'>
					<input type='hidden' name='numberHelp2' id='numberHelp2' value='0'>
				</div>
				<div class="box-body">
					<button type="button" id='add_komponen' style='width:130px; margin-right:0px; margin-bottom:3px; margin-left:5px; float:right;' class="btn btn-success btn-sm">COMPONENT</button>
					<!--<button type="button" id='add_product' style='width:130px; margin-right:0px; margin-bottom:3px; margin-left:5px; float:right;' class="btn btn-warning btn-sm">SPOOL</button>-->
					<button type="button" id='add_isomatric' style='width:130px; margin-right:0px; margin-bottom:3px; margin-left:5px; float:right;' class="btn btn-primary btn-sm">ISOMETRIC</button>

					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead id='head_table'>
							<tr class='bg-blue'>
								<th class="text-center" style='width: 10%;'>#</th>
								<th class="text-center" style='width: 20%;'>Component</th>
								<th class="text-center" style='width: 53%;'>Specification</th>  
								<th class="text-center" style='width: 7%;'>Qty</th>
								<th class="text-center" style='width: 7%;'>Option</th>
							</tr>
						</thead>
						<tbody id='detail_body' class='detail_body'></tbody>
						<tbody id='detail_body_Kosong'>
							<tr>
								<td colspan='5'>Product list empty ...</td>
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
	#no_ipp_chosen,
	#order_type_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
	
</style>
<script>
	$(document).ready(function(){
		$('#add_komponen').hide();
		$('#component').hide();
		
		$('#order_type').change(function(e){
			if($(this).val() == "SPOOL"){
				$('#add_isomatric').show();
				// $('#add_product').show();
				$('#add_komponen').hide();
			}
			else if($(this).val() == "LOOSE"){
				$('#add_isomatric').hide();
				// $('#add_product').hide();
				$('#add_komponen').show();
			}
			else if($(this).val() == "SPOOL & LOOSE"){
				$('#add_isomatric').show();
				// $('#add_product').show();
				$('#add_komponen').show();
			}
			
			$('.delAll').remove();
			$("#numberMax").val('0');
			$("#numberHelpHide").val('0');
			$("#numberMaxSpool").val('0');
			$("#numberMaxComp").val('0');
			$("#numberHelp1").val('0');
			$("#numberHelp2").val('0');
			$("#detail_body_Kosong").show();
		});
		
		$(document).on('change', '.spool', function(){
			$(this).parent().parent().find("td:nth-child(3) input").val('');
			$(this).parent().parent().find("td:nth-child(4) input").val('');
			$(this).parent().parent().find("td:nth-child(5) input").val('');
			$(this).parent().parent().find("td:nth-child(6) input").val('');
			$(this).parent().parent().find("td:nth-child(7) input").val('');
			$(this).parent().parent().find("td:nth-child(8) input").val('');
			$(this).parent().parent().find("td:nth-child(9) input").val('');
			$(this).parent().parent().find("td:nth-child(10) input").val('');
			// $('.diameter_1').val('');
			// $('.diameter_2').val('');
			// $('.length').val('');
			// $('.thickness').val('');
			// $('.sudut').val('');
			// $('.id_standard').val('');
			// $('.type').val('');
			// $('.qty').val('');
		});
		
		var nomor	= 1;
		var nomorKQ	= 1;
		
		$('#add_isomatric').click(function(e){
			e.preventDefault();
			var nilaiAwalSpool	= parseInt($("#numberMaxSpool").val());
			var nilaiAkhirSpool	= nilaiAwalSpool + 1;
			$("#numberMaxSpool").val(nilaiAkhirSpool);
			
			AppendIsomatric(nilaiAkhirSpool);
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
			
			AppendBarisKomponen(nomorKQ, nilaiAkhirComp);
			$('#head_table').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax").val(nilaiAkhir); 
			$("#numberHelpHide").val('1'); 
			$("#detail_body_Kosong").hide();
			// $("#add_komponen").hide();
			$('#simpan-bro').show();
		});
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var no_ipp		= $('#no_ipp').val();
			var numberMax	= $('#numberMax').val();

			if(no_ipp=='' || no_ipp==null || no_ipp=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'IPP Number, please input first ...',
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
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			var numberHelp2 = $('#numberHelp2').val();
			
			$('#detail_body').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				// alert(nomor[1]);
				var id_category		= $('#id_category_'+nomor[1]).val();
				var spool			= $('.product_'+nomor[1]).val();
				var qty				= $('.qty_'+nomor[1]).val();
				
				var diameter_1		= $('.diameter_1_'+nomor[1]).val();
				var diameter_2		= $('.diameter_2_'+nomor[1]).val();
				var length			= $('.length_'+nomor[1]).val();
				var thickness		= $('.thickness_'+nomor[1]).val();
				var sudut			= $('.sudut_'+nomor[1]).val();
				var id_standard		= $('.id_standard_'+nomor[1]).val();
				var type			= $('.type_'+nomor[1]).val();
				
				if(qty == '' || qty == 0 || qty == null){
					intError++;
					pesan = "Qty has not empty ...";
				}
				
				if(spool == '' || spool == 0 || spool == null){
					intError++;
					pesan = "Type product has not empty ...";
				}
				//pipe
				if(spool == 'pipe' || spool == 'pipe slongsong'){
					if(thickness == '' || thickness == 0 || thickness == null){
						intError++;
						pesan = "Thickness has not empty ...";
					}
					if(length == '' || length == 0 || length == null){
						intError++;
						pesan = "Length has not empty ...";
					}
					if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
						intError++;
						pesan = "Diameter has not empty ...";
					}
				}
				//blind flange
				if(spool == 'blind flange' || spool == 'end cap' || spool == 'equal tee mould' || spool == 'equal tee slongsong' || spool == 'flange mould' || spool == 'flange slongsong' || spool == 'shop join'){
					if(thickness == '' || thickness == 0 || thickness == null){
						intError++;
						pesan = "Thickness has not empty ...";
					}
					if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
						intError++;
						pesan = "Diameter has not empty ..."; 
					}
				}
				//branch point
				if(spool == 'branch point' || spool == 'concentric reducer' || spool == 'eccentric reducer' || spool == 'reducer tee mould' || spool == 'reducer tee slongsong'){
					if(thickness == '' || thickness == 0 || thickness == null){
						intError++;
						pesan = "Thickness has not empty ...";
					}
					if(diameter_2 == '' || diameter_2 == 0 || diameter_2 == null){
						intError++;
						pesan = "Diameter 2 has not empty ...";
					}
					if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
						intError++;
						pesan = "Diameter has not empty ...";
					}
				}
				//colar
				if(spool == 'colar' || spool == 'loose flange'){
					if(id_standard == '' || id_standard == 0 || id_standard == null){
						intError++;
						pesan = "Standard has not empty ...";
					}
					if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
						intError++;
						pesan = "Diameter has not empty ...";
					}
				}
				//elbow mitter
				if(spool == 'elbow mitter' || spool == 'elbow mould'){
					if(type == '' || type == 0 || type == null){
						intError++;
						pesan = "Type has not empty ...";
					}
					if(sudut == '' || sudut == 0 || sudut == null){
						intError++;
						pesan = "Corner has not empty ...";
					}
					if(thickness == '' || thickness == 0 || thickness == null){
						intError++;
						pesan = "Thickness has not empty ...";
					}
					if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
						intError++;
						pesan = "Diameter has not empty ...";
					}
				}
				//puddle flange
				if(spool == 'puddle flange'){
					if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
						intError++;
						pesan = "Diameter has not empty ...";
					}
				}
			
				if(id_category == '' || id_category == 0 || id_category == null){
					// alert('masih');
					intError++;
					pesan = "Series has not empty ...";
					$('#simpan-bro').prop('disabled',false);
					return false;
				}
			});
			
			if(intError > 0){
				swal({
					title	: "Notification Message !",
					text	: pesan,						
					type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			// swal({
			  // title	: "Error Message!",
			  // text	: 'Masih proses pengerjaan ...',
			  // type	: "warning"
			// });
			// $('#simpan-bro').prop('disabled',false);
			// return false;
			
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
						var baseurl=base_url + active_controller +'/bq';
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
	
	
	//=============================================================================================================================================
	//=============================================================================================================================================
	//============================================================== ISOMATRIC ====================================================================
	//=============================================================================================================================================
	//=============================================================================================================================================
	
	function AppendIsomatric(NilSpool){
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = 	"<tr id='tr1_"+nomor+"' class='delAll'>";
			Rows	+= 		"<td align='center' style='padding: 0px !important; vertical-align: middle !important;'>";
			Rows	+=			"<button type='button' style='min-width:100px; margin-left: 5px;' id='add_component_"+nomor+"' data-pluskom='"+nomor+"' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Component'>Add Spool</button>&nbsp;";
			Rows	+=			"<input type='hidden' style='color:black !important; width:50px;' id='numberMax_"+nomor+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' style='vertical-align: bottom; font-size: 16px;'>";
			Rows	+=			"<select id='id_category_"+nomor+"' class='chosen_select form-control inline-block series' required><option value='0'>Select An Series</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' colspan='2' style='background-color: #235d80bd; color: white; vertical-align: bottom; text-align: center;  font-size: 16px;'>";
			Rows	+=			"<label>ISOMETRIC "+NilSpool+"</label>"; 
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows 	+=			"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delIsomatric("+nomor+")' title='Delete Record'>Del Row</button>";
			Rows	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBody1_"+nomor+"' class='delAll'>";
			Rows 	+= 		"<td colspan='5' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_isomatric_"+nomor+"' class='detail_body'></tbody>"; 
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		$('#detail_body').append(Rows);
		
		var add_component_ 	= "#add_component_"+nomor;
		var numberMax_ 		= "#numberMax_"+nomor;
		var ID_Category 	= "#id_category_"+nomor;
		var DetailISO		= "#detail_isomatric_"+nomor;
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getSeries',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(ID_Category).html(data.option).trigger("chosen:updated");
			}
		});
		
		$(document).on('change', ID_Category, function(e){
			e.preventDefault();
			$(DetailISO).empty();
		});
		
		$(document).on('click', add_component_, function(e){
			e.preventDefault();
			var Series 		= $(ID_Category).val();
			if(Series == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Series Not Selected...',
				  type	: "warning"
				});
				return false;
			}
			var dataKom		= $(this).data('pluskom');
			var nilaiAwal	= parseInt($(numberMax_).val());
			
			var nilaiAkhir	= nilaiAwal + 1;
			$(numberMax_).val(nilaiAkhir);
			
			var nilaiUrut1	= parseInt($('#numberHelp1').val());
			var hasilUrut1	= nilaiUrut1 + 1;
			$('#numberHelp1').val(hasilUrut1);
			// alert(Series);
			AppendSpool(dataKom, nilaiAkhir, NilSpool, Series);
			$('.chosen_select').chosen({width: '100%'});
		});
		
		nomor++;
	}
	
	function AppendSpool(nil1, nil2, NilSpool, Series){
		// alert(Series);
		var nomorK	= 1;
		var valuex	= $('#detail_isomatric_'+nomorK).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_isomatric_'+nomorK+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr2_"+nil1+"_"+nil2+"' class='delAll'>";
			Rows	+= 		"<td align='center' style='padding: 0px !important; vertical-align: middle !important; width: 10%;'>";
			Rows	+=			"<button type='button' style='min-width:100px; margin-left: 5px;' id='add_component_"+nil1+"_"+nil2+"' data-pluskom1='"+nil1+"' data-pluskom2='"+nil2+"' class='btn btn-success btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Component'>Add Comp</button>&nbsp;";
			Rows	+=			"<input type='hidden' style='color:black !important; width:50px;' id='numberMax_"+nil1+"_"+nil2+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' colspan='3' style='background-color: #232323b0; color: white; vertical-align: bottom; text-align: center;  font-size: 16px; width: 83%;'>";
			Rows	+=			"<label>SPOOL "+nil2+"</label>"; 
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center' style='width: 7%;'>";
			Rows 	+=			"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delSpool("+nil1+","+nil2+")' title='Delete Record'>Del Row</button>";
			Rows	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBody2_"+nil1+"_"+nil2+"' class='delAll'>";
			Rows 	+= 		"<td colspan='5' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_spool_"+nil1+"_"+nil2+"' class='detail_body'></tbody>"; 
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		// alert(nil1);
		$('#detail_isomatric_'+nil1).append(Rows);
		
		var add_component_ 	= "#add_component_"+nil1+"_"+nil2;
		var numberMax_ 		= "#numberMax_"+nil1+"_"+nil2;
		
		
		
		$(document).on('click', add_component_, function(e){
			e.preventDefault();
			var dataKom1	= $(this).data('pluskom1');
			var dataKom2	= $(this).data('pluskom2');
			
			var nilaiAwal	= parseInt($(numberMax_).val());
			var nilaiAkhir	= nilaiAwal + 1;
			$(numberMax_).val(nilaiAkhir);
			
			var nilaiUrut2	= parseInt($('#numberHelp2').val());
			var hasilUrut2	= nilaiUrut2 + 1;
			$('#numberHelp2').val(hasilUrut2);
			
			// var nilaiUrut2	= parseInt($('#numberHelp1').val());
			// var hasilUrut2	= nilaiUrut2 + 1;
			// $('#numberHelp1').val(hasilUrut2);
			
			AppendComponent(dataKom1, dataKom2, nilaiAkhir, NilSpool, hasilUrut2, Series);
			$('.chosen_select').chosen({width: '100%'});
		});
		nomorK++;
	}
	
	function AppendComponent(nil1, nil2, nil3, NilSpool, hasilUrut2, Series){
		var nomorK	= 1;
		var valuex	= $("#detail_spool_"+nil1+"_"+nil2).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_spool_'+nil1+"_"+nil2+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr3_"+nil1+"_"+nil2+"_"+nil3+"' class='delAll'>";
			Rows	+= 		"<td style='width: 10%;' align='center'>";
			Rows	+=			"<button type='button' id='sub_komponen_"+nil1+"_"+nil2+"_"+nil3+"' data-pluskom1='"+nil1+"' data-pluskom2='"+nil2+"' data-pluskom3='"+nil3+"' style='min-width:100px;' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Sub Component'>Add Sub Comp.</button>"; 
			Rows	+=			"<input type='hidden' name='ListDetailKomp["+hasilUrut2+"][id_delivery]' id='id_delivery_"+nil1+"_"+nil2+"_"+nil3+"' class='form-control input-sm' value='PS-"+NilSpool+"'>";
			Rows	+=			"<input type='hidden' name='ListDetailKomp["+hasilUrut2+"][sub_delivery]' id='sub_delivery_"+nil1+"_"+nil2+"_"+nil3+"' class='form-control input-sm' value='PS-"+NilSpool+"-"+nil2+"'>";
			Rows	+=			"<input type='hidden' name='ListDetailKomp["+hasilUrut2+"][sts_delivery]' id='sts_delivery_"+nil1+"_"+nil2+"_"+nil3+"' class='form-control input-sm' value='PARENT'>";
			Rows	+=			"<input type='hidden' name='ListDetailKomp["+hasilUrut2+"][series]' id='series_"+nil1+"_"+nil2+"_"+nil3+"' class='form-control input-sm' value='"+Series+"'>";
			Rows	+=			"<input type='hidden' style='color:black !important; width:50px;' id='numberMax_"+nil1+"_"+nil2+"_"+nil3+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 20%;' align='left'>";
			Rows	+=			"<select name='ListDetailKomp["+hasilUrut2+"][id_category]' id='id_category_"+nil1+"_"+nil2+"_"+nil3+"' class='chosen_select form-control inline-block product_"+hasilUrut2+"' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align='left'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_1_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKomp["+hasilUrut2+"][diameter_1]' id='diameter_1_"+nil1+"_"+nil2+"_"+nil3+"' required autocomplete='off' placeholder='Dim'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align='left'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_2_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKomp["+hasilUrut2+"][diameter_2]' id='diameter_2_"+nil1+"_"+nil2+"_"+nil3+"' required autocomplete='off' placeholder='Dim 2'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align='left'>";
			Rows	+=			"<input type='text' class='form-control numberOnly length_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKomp["+hasilUrut2+"][length]' id='length_"+nil1+"_"+nil2+"_"+nil3+"' required autocomplete='off' placeholder='Length'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align='left'>";
			Rows	+=			"<input type='text' class='form-control numberOnly thickness_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKomp["+hasilUrut2+"][thickness]' id='thickness_"+nil1+"_"+nil2+"_"+nil3+"' required autocomplete='off' placeholder='Thick'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align='left'>";
			Rows	+=			"<input type='text' class='form-control numberOnly sudut_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKomp["+hasilUrut2+"][sudut]' id='sudut_"+nil1+"_"+nil2+"_"+nil3+"' required autocomplete='off' placeholder='Corner'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKomp["+hasilUrut2+"][id_standard]' id='id_standard_"+nil1+"_"+nil2+"_"+nil3+"' class='chosen_select form-control inline-block id_standard_"+hasilUrut2+"' required><option value='0'>Standard</option></select>";
			Rows	+= 		"</td>"; 
			Rows	+= 		"<td style='width: 9%;' align='left'>";  
			Rows	+=			"<select name='ListDetailKomp["+hasilUrut2+"][type]' id='type_"+nil1+"_"+nil2+"_"+nil3+"' class='chosen_select form-control inline-block type_"+hasilUrut2+"' required><option value='0'>Type</option><option value='SR'>Short Rad</option><option value='LR'>Long Rad</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly qty_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKomp["+hasilUrut2+"][qty]' id='qty_"+nil1+"_"+nil2+"_"+nil3+"' maxlength='4' required autocomplete='off' placeholder='Qty'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align=\"center\">";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delKomponent("+nil1+","+nil2+","+nil3+")' title='Delete Record'>Del Row</button>";  
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBody3_"+nil1+"_"+nil2+"_"+nil3+"' class='delAll'>";
			Rows 	+= 		"<td colspan='12' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_bodysub_"+nil1+"_"+nil2+"_"+nil3+"' class='detail_body'></tbody>";
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		// alert('#detail_body_'+intd);
		// alert(Rows);
		// $('.displayed').hide();
		$('#detail_spool_'+nil1+"_"+nil2).append(Rows);
		
		var K_id_category 	= "#id_category_"+nil1+"_"+nil2+"_"+nil3;
		var K_sub_komponen 	= "#sub_komponen_"+nil1+"_"+nil2+"_"+nil3;
		var K_NumberMax 	= "#numberMax_"+nil1+"_"+nil2+"_"+nil3;
		var K_id_standard 	= "#id_standard_"+nil1+"_"+nil2+"_"+nil3;
		
		$(K_sub_komponen).hide();
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(K_id_standard).html(data.option).trigger("chosen:updated");
			}
		});
		
		$(document).on('click', K_sub_komponen, function(e){
			e.preventDefault();
			var dataKom1	= $(this).data('pluskom1');
			var dataKom2	= $(this).data('pluskom2');
			var dataKom3	= $(this).data('pluskom3');
			
			var nilaiAwal	= parseInt($(K_NumberMax).val());
			var nilaiAkhir	= nilaiAwal + 1;
			$(K_NumberMax).val(nilaiAkhir);
			
			var nilaiUrut2	= parseInt($('#numberHelp2').val());
			var hasilUrut2	= nilaiUrut2 + 1;
			$('#numberHelp2').val(hasilUrut2);
			
			AppendSlongsong(dataKom1, dataKom2, dataKom3, nilaiAkhir, NilSpool, hasilUrut2, Series);
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
		
		var KS_diameter_1 	= "#diameter_1_"+nil1+"_"+nil2+"_"+nil3;
		var KS_diameter_2 	= "#diameter_2_"+nil1+"_"+nil2+"_"+nil3;
		var KS_length 		= "#length_"+nil1+"_"+nil2+"_"+nil3;
		var KS_thickness 	= "#thickness_"+nil1+"_"+nil2+"_"+nil3;
		var KS_sudut		= "#sudut_"+nil1+"_"+nil2+"_"+nil3;
		var KS_id_standard 	= "#id_standard_"+nil1+"_"+nil2+"_"+nil3+"_chosen";
		var KS_type 		= "#type_"+nil1+"_"+nil2+"_"+nil3+"_chosen";
		
		var KS_BodySub		= "#detail_bodysub_"+nil1+"_"+nil2+"_"+nil3;
		
		$(K_id_category).on('change', function(e){
			// alert(K_sub_komponen);
			e.preventDefault();
			if($(this).val() == 'pipe'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'end cap'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_thickness).show();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'blind flange'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'flange mould'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'flange slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'concentric reducer'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'eccentric reducer'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'reducer tee mould'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'reducer tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'equal tee mould'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'equal tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'branch point'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'shop join'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'colar'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).show();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'puddle flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'loose flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).show();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'elbow mould'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'elbow mitter'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
				$(K_sub_komponen).show();
			}
			else{
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).show();
				$(KS_type).show();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		nomorK++;
		nil3++;
	}
	
	function AppendSlongsong(intd, intd2, intd3, intd4, NilSpool, hasilUrut2, Series)
	{
		// alert("Ye sampai");
		// return false;
		var nomorK	= 1;
		var valuex	= $('#detail_bodysub_'+intd+'_'+intd2+'_'+intd3).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_bodysub_'+intd+'_'+intd2+'_'+intd3+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='tr4_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='delAll'>";
			Rows	+= 		"<td style='width: 10%;' align='center'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub["+hasilUrut2+"][id_delivery]' id='id_delivery_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='form-control input-sm' value='PS-"+NilSpool+"'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub["+hasilUrut2+"][sub_delivery]' id='sub_delivery_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='form-control input-sm' value='PS-"+NilSpool+"-"+intd2+"'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub["+hasilUrut2+"][sts_delivery]' id='sts_delivery_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='form-control input-sm' value='CHILD'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub["+hasilUrut2+"][series]' id='series_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='form-control input-sm' value='"+Series+"'>";
			
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 20%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub["+hasilUrut2+"][id_category]' id='id_category_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='chosen_select form-control inline-block product_"+hasilUrut2+"' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_1_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][diameter_1]' id='diameter_1_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' required autocomplete='off' placeholder='Dim'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_2_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][diameter_2]' id='diameter_2_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' required autocomplete='off' placeholder='Dim 2'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly length_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][length]' id='length_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' required autocomplete='off' placeholder='Length'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly thickness_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][thickness]' id='thickness_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' required autocomplete='off' placeholder='Thick'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly sudut_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][sudut]' id='sudut_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' required autocomplete='off' placeholder='Corner'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub["+hasilUrut2+"][id_standard]' id='id_standard_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='chosen_select form-control inline-block id_standard_"+hasilUrut2+"' required><option value='0'>Standard</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub["+hasilUrut2+"][type]' id='type_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' class='chosen_select form-control inline-block type_"+hasilUrut2+"' required><option value='0'>Type</option><option value='SR'>Short Rad</option><option value='LR'>Long Rad</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly qty_"+hasilUrut2+"' style='text-align: center;' name='ListDetailKompSub["+hasilUrut2+"][qty]' id='qty_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"' maxlength='4' required autocomplete='off' placeholder='Qty'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align=\"center\" class='delAll'>";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delSubKomp("+intd+","+intd2+","+intd3+","+intd4+")' title='Delete Record'>Del Row</button>";
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
		
		// alert('#detail_bodysub_'+intd+'_'+intd2+'_'+intd3);
		// alert(Rows);
		// return false;
		$('#detail_bodysub_'+intd+'_'+intd2+'_'+intd3).append(Rows);
		
		var KSUb_id_category = "#id_category_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KSUb_id_product = "#id_product_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KSub_id_standard = "#id_standard_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KS_diameter_1 	= "#diameter_1_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KS_diameter_2 	= "#diameter_2_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KS_length 		= "#length_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KS_thickness 	= "#thickness_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KS_sudut		= "#sudut_"+intd+"_"+intd2+"_"+intd3+"_"+intd4;
		var KS_id_standard 	= "#id_standard_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"_chosen";
		var KS_type 		= "#type_"+intd+"_"+intd2+"_"+intd3+"_"+intd4+"_chosen";
		
		$(KSUb_id_category).on('change', function(e){
			e.preventDefault();
			if($(this).val() == 'pipe slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'flange slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'reducer tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'equal tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'elbow mitter'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
			}
			else{
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).show();
				$(KS_type).show();
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProductSub',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(KSUb_id_category).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(KSub_id_standard).html(data.option).trigger("chosen:updated");
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
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		nomorK++;
	}
	// delete isomatrik
	function delIsomatric(row){
		$('#tr1_'+row).remove();
		$('#trBody1_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax").val() - 1;
		$("#numberMax").val(updatemax);
		
		var HelpHide = $("#numberHelpHide").val(); 
		
		var maxLine = $("#numberMax").val();
		if(maxLine == 0){
			$("#detail_body_Kosong").show();
		}
	}
	
	function delSpool(row, row2){
		$('#tr2_'+row+'_'+row2).remove();
		$('#trBody2_'+row+'_'+row2).remove();
	}
	function delKomponent(row, row2, row3){
		$('#tr3_'+row+'_'+row2+'_'+row3).remove();
		$('#trBody3_'+row+'_'+row2+'_'+row3).remove();
	}
	function delSubKomp(row, row2, row3, row4){
		$('#tr4_'+row+'_'+row2+'_'+row3+'_'+row4).remove();
	}
	
	//=============================================================================================================================================
	//=============================================================================================================================================
	//============================================================== COMPONENT ====================================================================
	//=============================================================================================================================================
	//=============================================================================================================================================
	function AppendBarisKomponen(intd, nilComp)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = 	"<tr id='trX1_"+nomor+"' class='delAll'>"; 
			Rows	+= 		"<td align='center' style='padding: 0px !important; vertical-align: middle !important;'>";
			Rows	+=				"<button type='button' style='min-width:100px; margin-left: 5px;' id='add_component2_"+nomor+"' data-pluskom='"+nomor+"' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Component'>Add Comp.</button>&nbsp;";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left'>";
			Rows	+=			"<select id='id_category_"+nomor+"' class='chosen_select form-control inline-block series' required><option value='0'>Select An Series</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='left' colspan='2' style='background-color: #232323b0; color: white; text-align: center; vertical-align: bottom; font-size: 16px;'>";
			Rows	+=			"<label>COMPONENT</label>";
			Rows	+=			"<input type='hidden' style='color:black !important;' id='numberMax_"+nomor+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td align='center'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRowX("+nomor+")' title='Delete Record'>Del Row</button>";
			Rows	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBodyX1_"+nomor+"' class='delAll'>";
			Rows 	+= 		"<td colspan='5' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detail_body_"+nomor+"' class='detail_body'></tbody>";
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
		
		$('#detail_body').append(Rows);
		
		var add_component2_ 	= "#add_component2_"+nomor;
		var numberMax_ 			= "#numberMax_"+nomor;
		var ID_Category 		= "#id_category_"+nomor;
		var DetKomp 			= "#detail_body_"+nomor;

		$(document).on('change', ID_Category, function(e){
			e.preventDefault();
			$(DetKomp).empty();
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getSeries',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(ID_Category).html(data.option).trigger("chosen:updated");
			}
		});
		
		$(document).on('click', add_component2_, function(e){
			e.preventDefault();
			var SeriesComp 	= $(ID_Category).val();
			if(SeriesComp == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Series Not Selected...',
				  type	: "warning"
				});
				return false;
			}
			var dataKom	= $(this).data('pluskom');
			var nilaiAwal	= parseInt($(numberMax_).val());
			var nilaiAkhir	= nilaiAwal + 1;
			$(numberMax_).val(nilaiAkhir);
			
			// var nilaiUrut1	= parseInt($('#numberHelp1').val());
			// var hasilUrut1	= nilaiUrut1 + 1;
			// $('#numberHelp1').val(hasilUrut1);
			
			var nilaiUrut1	= parseInt($('#numberHelp2').val());
			var hasilUrut1	= nilaiUrut1 + 1;
			$('#numberHelp2').val(hasilUrut1);
			
			
			// console.log(nilaiUrut1);
			// console.log(hasilUrut1);
			AppendBarisKom2(dataKom, nilaiAkhir, nilComp, hasilUrut1, SeriesComp);
			$('.chosen_select').chosen({width: '100%'});
		});
		
		nomor++;
	}
	
	function AppendBarisKom2(intd, num, nilComp, hasilUrut1, SeriesComp){
		var nomorK	= 1;
		var valuex	= $('#detail_body_'+nomorK).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_'+nomorK+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}

		var Rows	 = 	"<tr id='trX2_"+intd+"_"+num+"' class='delAll'>"; 
			Rows	+= 		"<td style='width: 10%;' align='center'>"; 
			Rows	+=			"<button type='button' id='sub_komponen_komp_"+intd+"_"+num+"' data-pluskom1='"+intd+"' data-pluskom2='"+num+"' style='min-width:100px;' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='Add Sub Component'>Add Sub Comp.</button>"; 
			Rows	+=			"<input type='hidden' name='ListDetailKompSingle["+hasilUrut1+"][id_delivery]' id='id_delivery_"+intd+"_"+num+"' class='form-control input-sm' value='CP-"+nilComp+"'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSingle["+hasilUrut1+"][sub_delivery]' id='sub_delivery_"+intd+"_"+num+"' class='form-control input-sm' value='CP-"+nilComp+"-1'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSingle["+hasilUrut1+"][sts_delivery]' id='sts_delivery_"+intd+"_"+num+"' class='form-control input-sm' value='PARENT'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSingle["+hasilUrut1+"][series]' id='series_"+intd+"_"+num+"' class='form-control input-sm' value='"+SeriesComp+"'>";
			Rows	+=			"<input type='hidden' style='color:black !important; width:50px;' id='numberMaxKomp_"+intd+"_"+num+"' value='0'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 20%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSingle["+hasilUrut1+"][id_category]' id='id_category_"+intd+"_"+num+"' class='chosen_select form-control inline-block product_"+hasilUrut1+"' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_1_"+hasilUrut1+"' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][diameter_1]' id='diameter_1_"+intd+"_"+num+"' required autocomplete='off' placeholder='Dim'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_2_"+hasilUrut1+"' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][diameter_2]' id='diameter_2_"+intd+"_"+num+"' required autocomplete='off' placeholder='Dim 2'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly length_"+hasilUrut1+"' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][length]' id='length_"+intd+"_"+num+"' required autocomplete='off' placeholder='Length'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly thickness_"+hasilUrut1+"' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][thickness]' id='thickness_"+intd+"_"+num+"' required autocomplete='off' placeholder='Thick'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly sudut_"+hasilUrut1+"' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][sudut]' id='sudut_"+intd+"_"+num+"' required autocomplete='off' placeholder='Corner'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSingle["+hasilUrut1+"][id_standard]' id='id_standard_"+intd+"_"+num+"' class='chosen_select form-control inline-block id_standard_"+hasilUrut1+"' required><option value='0'>Standard</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSingle["+hasilUrut1+"][type]' id='type_"+intd+"_"+num+"' class='chosen_select form-control inline-block type_"+hasilUrut1+"' required><option value='0'>Type</option><option value='SR'>Short Rad</option><option value='LR'>Lomg Rad</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>"; 
			Rows	+=			"<input type='text' class='form-control numberOnly qty_"+hasilUrut1+"' style='text-align: center;' name='ListDetailKompSingle["+hasilUrut1+"][qty]' id='qty_"+intd+"_"+num+"' maxlength='4' required autocomplete='off' placeholder='Qty'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align=\"center\">";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delRowKom2("+intd+","+num+")' title='Delete Record'>Del Row</button>";
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='trBodyX2_"+intd+"_"+num+"' class='delAll' class='delAll'>";
			Rows 	+= 		"<td colspan='12' style='padding: 0px;'>";
			Rows	+= 			"<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>";
			Rows 	+= 				"<tbody id='detailbodysubkomp_"+intd+"_"+num+"' class='detail_body'></tbody>";
			Rows 	+= 			"</table>";
			Rows 	+= 		"</td>";	
			Rows	+= 	"</tr>";
			
		
		// alert('#detail_body_'+intd);
		// alert(Rows);
		// return false;
		$('#detail_body_'+intd).append(Rows);
		
		var K_id_category 	= "#id_category_"+intd+"_"+num;
		var K_id_product 	= "#id_product_"+intd+"_"+num;
		var K_id_standard 	= "#id_standard_"+intd+"_"+num;
		var K_sub_komponen 	= "#sub_komponen_komp_"+intd+"_"+num;
		var K_NumberMax 	= "#numberMaxKomp_"+intd+"_"+num;
		
		var KS_diameter_1 	= "#diameter_1_"+intd+"_"+num;
		var KS_diameter_2 	= "#diameter_2_"+intd+"_"+num;
		var KS_length 		= "#length_"+intd+"_"+num;
		var KS_thickness 	= "#thickness_"+intd+"_"+num;
		var KS_sudut		= "#sudut_"+intd+"_"+num;
		var KS_id_standard 	= "#id_standard_"+intd+"_"+num+"_chosen";
		var KS_type 		= "#type_"+intd+"_"+num+"_chosen";
		
		var KS_BodySub		= "#detail_bodysubkomp_"+intd+"_"+num;
		
		$(K_sub_komponen).hide();
		
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
			
			AppendSlongsongComp(dataKom1, dataKom2, nilaiAkhir, nilComp, hasilUrut1, SeriesComp);
			$('.chosen_select').chosen({width: '100%'});
		});
		
		$(K_id_category).on('change', function(e){
			e.preventDefault();
			if($(this).val() == 'pipe'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'end cap'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_thickness).show();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'blind flange'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'flange mould'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'concentric reducer'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'eccentric reducer'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'reducer tee mould'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'equal tee mould'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'branch point'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'shop join'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'colar'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).show();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'puddle flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'loose flange'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).show();
				$(KS_type).hide();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else if($(this).val() == 'flange slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'reducer tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'equal tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'elbow mitter'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
				$(K_sub_komponen).show();
			}
			else if($(this).val() == 'elbow mould'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
			else{
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).show();
				$(KS_type).show();
				$(K_sub_komponen).hide();
				$(KS_BodySub).empty();
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(K_id_standard).html(data.option).trigger("chosen:updated");
			}
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
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		nomorK++;
		num++;
	}
	
	
	
	function AppendSlongsongComp(intd, intd2, intd3, NilSpool, hasilUrut2, SeriesComp)
	{
		// alert("Ye sampai");
		// return false;
		var nomorK	= 1;
		var valuex	= $('#detailbodysubkomp_'+intd+'_'+intd2).find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detailbodysubkomp_'+intd+'_'+intd2+' tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomorK	= parseInt(det_id[2])+1;
		}
		// alert(valuex);
		// alert(akhir);
		// return false;
		var Rows	 = 	"<tr id='trX3_"+intd+"_"+intd2+"_"+intd3+"' class='delAll'>";
			Rows	+= 		"<td style='width: 10%;' align='center'>"; 
			Rows	+=			"<input type='hidden' name='ListDetailKompSub2["+hasilUrut2+"][id_delivery]' id='id_delivery_"+intd+"_"+intd2+"_"+intd3+"' class='form-control input-sm' value='CP-"+NilSpool+"'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub2["+hasilUrut2+"][sub_delivery]' id='sub_delivery_"+intd+"_"+intd2+"_"+intd3+"' class='form-control input-sm' value='CP-"+NilSpool+"-1'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub2["+hasilUrut2+"][sts_delivery]' id='sts_delivery_"+intd+"_"+intd2+"_"+intd3+"' class='form-control input-sm' value='CHILD'>";
			Rows	+=			"<input type='hidden' name='ListDetailKompSub2["+hasilUrut2+"][series]' id='series_"+intd+"_"+intd2+"_"+intd3+"' class='form-control input-sm' value='"+SeriesComp+"'>"; 
			
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 20%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub2["+hasilUrut2+"][id_category]' id='id_category_"+intd+"_"+intd2+"_"+intd3+"' class='chosen_select form-control inline-block spool' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_1' style='text-align: center;' name='ListDetailKompSub2["+hasilUrut2+"][diameter_1]' id='diameter_1_"+intd+"_"+intd2+"_"+intd3+"' required autocomplete='off' placeholder='Dim'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly diameter_2' style='text-align: center;' name='ListDetailKompSub2["+hasilUrut2+"][diameter_2]' id='diameter_2_"+intd+"_"+intd2+"_"+intd3+"' required autocomplete='off' placeholder='Dim 2'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly length' style='text-align: center;' name='ListDetailKompSub2["+hasilUrut2+"][length]' id='length_"+intd+"_"+intd2+"_"+intd3+"' required autocomplete='off' placeholder='Length'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly thickness' style='text-align: center;' name='ListDetailKompSub2["+hasilUrut2+"][thickness]' id='thickness_"+intd+"_"+intd2+"_"+intd3+"' required autocomplete='off' placeholder='Thick'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly sudut' style='text-align: center;' name='ListDetailKompSub2["+hasilUrut2+"][sudut]' id='sudut_"+intd+"_"+intd2+"_"+intd3+"' required autocomplete='off' placeholder='Corner'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub2["+hasilUrut2+"][id_standard]' id='id_standard_"+intd+"_"+intd2+"_"+intd3+"' class='chosen_select form-control inline-block id_standard' required><option value='0'>Standard</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 9%;' align='left'>";
			Rows	+=			"<select name='ListDetailKompSub2["+hasilUrut2+"][type]' id='type_"+intd+"_"+intd2+"_"+intd3+"' class='chosen_select form-control inline-block type' required><option value='0'>Type</option><option value='SR'>Short Rad</option><option value='LR'>Long Rad</option></select>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;'>";
			Rows	+=			"<input type='text' class='form-control numberOnly qty' style='text-align: center;' name='ListDetailKompSub2["+hasilUrut2+"][qty]' id='qty_"+intd+"_"+intd2+"_"+intd3+"' maxlength='4' required autocomplete='off' placeholder='Qty'>";
			Rows	+= 		"</td>";
			Rows	+= 		"<td style='width: 7%;' align=\"center\" class='delAll'>";
			Rows 	+=			"<div style='text-align: center;'>";
			Rows 	+=				"<button type='button' style='min-width:70px;' class='btn btn-danger btn-sm' data-toggle='tooltip' data-placement='bottom' onClick='delKompSub("+intd+","+intd2+","+intd3+")' title='Delete Record'>Del Row</button>";
			Rows	+= 			"</div>";
			Rows 	+= 		"</td>";
			Rows	+= 	"</tr>";
		
		// alert('#detailbodysubkomp_'+intd+'_'+intd2);
		// alert(Rows);
		// return false;
		$('#detailbodysubkomp_'+intd+'_'+intd2).append(Rows);
		
		var KSUb_id_category = "#id_category_"+intd+"_"+intd2+"_"+intd3;
		var KSUb_id_product = "#id_product_"+intd+"_"+intd2+"_"+intd3;
		var KSub_id_standard = "#id_standard_"+intd+"_"+intd2+"_"+intd3;
		var KS_diameter_1 	= "#diameter_1_"+intd+"_"+intd2+"_"+intd3;
		var KS_diameter_2 	= "#diameter_2_"+intd+"_"+intd2+"_"+intd3;
		var KS_length 		= "#length_"+intd+"_"+intd2+"_"+intd3;
		var KS_thickness 	= "#thickness_"+intd+"_"+intd2+"_"+intd3;
		var KS_sudut		= "#sudut_"+intd+"_"+intd2+"_"+intd3;
		var KS_id_standard 	= "#id_standard_"+intd+"_"+intd2+"_"+intd3+"_chosen";
		var KS_type 		= "#type_"+intd+"_"+intd2+"_"+intd3+"_chosen";
		
		$(KSUb_id_category).on('change', function(e){
			e.preventDefault();
			if($(this).val() == 'pipe slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'flange slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).hide();
				$(KS_length).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'reducer tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_thickness).show();
				$(KS_length).hide();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'equal tee slongsong'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).hide();
				$(KS_id_standard).hide();
				$(KS_type).hide();
			}
			else if($(this).val() == 'elbow mitter'){
				$(KS_diameter_1).show();
				$(KS_length).hide();
				$(KS_diameter_2).hide();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).hide();
				$(KS_type).show();
			}
			else{
				$(KS_diameter_1).show();
				$(KS_diameter_2).show();
				$(KS_length).show();
				$(KS_thickness).show();
				$(KS_sudut).show();
				$(KS_id_standard).show();
				$(KS_type).show();
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProductSub',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(KSUb_id_category).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(KSub_id_standard).html(data.option).trigger("chosen:updated");
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
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		nomorK++;
	}
	
	//delete komponene header
	function delRowX(row){
		$('#trX1_'+row).remove();
		$('#trBodyX1_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax").val() - 1;
		$("#numberMax").val(updatemax);
		
		$("#numberMaxComp").val(0);
		$("#numberHelp1").val(0);
		
		var HelpHide = $("#numberHelpHide").val(); 
		
		var maxLine = $("#numberMax").val();
		if(maxLine == 0){
			$("#detail_body_Kosong").show();
		}
		
		if(HelpHide == 1){
			$("#add_komponen").show();
			$("#numberHelpHide").val('0');
		}
	}
	
	//delete komponent
	function delRowKom2(row, row2){
		$('#trX2_'+row+'_'+row2).remove();
		$('#trBodyX2_'+row+'_'+row2).remove();
	}
	
	//delete komponent slongsong
	function delKompSub(row, row2, row3){
		$('#trX3_'+row+'_'+row2+'_'+row3).remove();
	}
</script>
