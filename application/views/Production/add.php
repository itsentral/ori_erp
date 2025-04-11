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
				<label class='label-control col-sm-2'><b>SO Number<span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>
						<?php
						echo form_input(array('id'=>'so_number','name'=>'so_number','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'SO Number'));											
					?>							
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
				<label class='label-control col-sm-2'><b>Project</b></label>
				<div class='col-sm-4'>
					 <?php
						echo form_textarea(array('id'=>'nm_project','name'=>'nm_project','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Project Information'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Information</b></label>
				<div class='col-sm-4'>
					 <?php
						echo form_textarea(array('id'=>'ket','name'=>'ket','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Production Information'));
					?>
				</div>
			</div>
			<div class="box box-success">
				<div class="box-header">
					<h3 class="box-title">List Product</h3>
					<input type='hidden' name='numberMax' id='numberMax' value='0'>
				</div>
				<div class="box-body">
					<button type="button" id='add_product' style='width:130px; margin-right:0px; margin-bottom:3px; float:right;' class="btn btn-success btn-sm">Add Product</button>
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead id='head_table'>
							<tr class='bg-blue'>
								<th class="text-center" class="no-sort" width="50px">No</th>
								<th class="text-center" style='width: 150px;'>Delivery Type</th>
								<th class="text-center" style='width: 200px;'>Product Type</th>
								<th class="text-center">Product Name</th>
								<th class="text-center" style='width: 100px;'>Qty</th>
								<th class="text-center" style='width: 75px;'>Option</th>
							</tr>
						</thead>
						<tbody id='detail_body'>
						</tbody>
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
		
		$('#add_product').click(function(e){
			e.preventDefault();
			// console.log(nomor);
			AppendBaris(nomor);
			$('#head_table').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax").val(nilaiAkhir);
			$("#detail_body_Kosong").hide();
			$('#simpan-bro').show();
			// if($("#numberMax").val(nilaiAkhir) != 0 && $('#numberMax_en').val() != 0 && $('#numberMax_bq').val() != 0){
				// $('#simpan-bro').show();
			// }
		});
		

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var no_ipp				= $('#so_number').val();
			var plan_start_produksi	= $('#plan_start_produksi').val();
			var plan_end_produksi	= $('#plan_end_produksi').val();
			var mulai_produksi		= $('#mulai_produksi').val();
			var id_mesin			= $('#id_mesin').val();
			var ket					= $('#ket').val();
			var nm_project			= $('#nm_project').val();
			var numberMax			= $('#numberMax').val();

			if(no_ipp=='' || no_ipp==null || no_ipp=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'SO Number, please input first ...',
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
			if(nm_project == '' || nm_project == null || nm_project == '-' || nm_project == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Project Name, please input first ...',
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
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			$('#detail_body').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var qty				= $('#qty_'+nomor[1]).val();
				var id_product		= $('#id_product_'+nomor[1]).val();
				var id_category		= $('#id_category_'+nomor[1]).val();
				
				
				if(qty == '' || qty == 0 || qty == null){
					intError++;
					pesan = "Number "+nomor[1]+" : Qty has not empty ...";
				}
				
				if(id_product == '' || id_product == 0 || id_product == null){
					intError++;
					pesan = "Number "+nomor[1]+" : Product name has not empty ...";
				}
				
				if(id_category == '' || id_category == 0 || id_category == null){
					intError++;
					pesan = "Number "+nomor[1]+" : Product type has not empty ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
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
						var baseurl=base_url + active_controller +'/add';
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
	
	function AppendBaris(intd)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='tr_"+nomor+"'>"; 
			Rows	+= 	"<td>";
			Rows	+= 		"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_delivery]' id='id_delivery_"+nomor+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Delivery</option></select>";
			// Rows	+=		"<input type='text' name='ListDetail["+nomor+"][id_delivery]' id='id_delivery_"+nomor+"' class='form-control input-sm' value='spool_"+nomor+"'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_category]' id='id_category_"+nomor+"' class='chosen_select form-control inline-block' required><option value='0'>Select An Type Product</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_product]' id='id_product_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control input-sm numberOnly' style='text-align: center;' name='ListDetail["+nomor+"][qty]' id='qty_"+nomor+"' maxlength='3' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body').append(Rows);
		
		var id_category_ = "#id_category_"+nomor;
		var id_delivery_ = "#id_delivery_"+nomor;
		var id_product_ = "#id_product_"+nomor;
		
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeProduct',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(id_category_).html(data.option).trigger("chosen:updated");
			}
		});
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getTypeDelivery',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(id_delivery_).html(data.option).trigger("chosen:updated");
			}
		});
		
		$(id_category_).on('change', function(e){
			e.preventDefault();
			// alert($(this).val());
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getProduct',
				cache: false,
				type: "POST",
				data: "category="+$(this).val(),
				dataType: "json",
				success: function(data){
					$(id_product_).html(data.option).trigger("chosen:updated");
				}
			});
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			$(this).val($(this).val().replace(/[^\d].+/, ""));
			if (event.which < 48 || event.which > 57) {
				event.preventDefault();
			}
			// if($(this).val() == ''){
				// $(this).val(0);
			// }
		});
		nomor++;
	}
</script>
