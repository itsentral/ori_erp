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
				<label class='label-control col-sm-2'><b>Material ID / Accurate ID <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('id'=>'idmaterial','name'=>'idmaterial','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'ID Material', 'style' => 'text-transform: uppercase'));
					?>
				</div>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('id'=>'id_accurate','name'=>'id_accurate','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'ID Accurate'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Material Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_material','name'=>'nm_material','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Type Material Name'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Trade Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_dagang','name'=>'nm_dagang','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Trade Name'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Internasional Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_international','name'=>'nm_international','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Internasional Name'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Type Material <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_category' id='id_category' class='form-control input-md'>
						<option value=''>Select An Material</option>
					<?php
						foreach($data_type AS $val => $valx){
							echo "<option value='".$valx['id_category']."'>".ucwords(strtolower($valx['category']))."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Min Stock (Day) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'safety_stock','name'=>'safety_stock','class'=>'form-control input-md maskM','autocomplete'=>'off','placeholder'=>'Min Stock','data-decimal'=>".", 'data-thousand'=>"" , 'data-precision'=>"0", 'data-allow-zero'=>""));
					?>
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2' hidden><b>Kilogram per kemasan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4' hidden>
					<?php
						echo form_input(array('id'=>'satuan_kg','name'=>'satuan_kg','class'=>'form-control input-md autoNumeric','autocomplete'=>'off','placeholder'=>'Piece Of Kilogram'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Unit Measurement <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<select name='id_satuan' id='id_satuan' class='form-control input-md'>
						<option value=''>Select An Unit</option>
					<?php
						foreach($data_pieces AS $val => $valx){
							echo "<option value='".$valx['id_satuan']."'>".ucwords(strtolower($valx['nama_satuan']))." (".ucwords(strtolower($valx['kode_satuan'])).")</option>";
						}
					 ?>
					</select>
				</div>
				<div class='col-sm-2'>
					<?php
						echo form_input(array('id'=>'nilai_konversi','name'=>'nilai_konversi','class'=>'form-control input-md autoNumeric','autocomplete'=>'off','placeholder'=>'Conversion Value'));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Max Stock (Day)</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'max_stock','name'=>'max_stock','class'=>'form-control input-md maskM','autocomplete'=>'off','placeholder'=>'Max Stock','data-decimal'=>".", 'data-thousand'=>"" , 'data-precision'=>"0", 'data-allow-zero'=>""));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Unit Packing <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<select name='id_packing' id='id_packing' class='form-control input-md'>
						<option value=''>Select An Unit Packing</option>
					<?php
						foreach($data_pieces_pack AS $val => $valx){
							echo "<option value='".$valx['id_satuan']."'>".ucwords(strtolower($valx['nama_satuan']))." (".ucwords(strtolower($valx['kode_satuan'])).")</option>";
						}
					 ?>
					</select>
				</div>
				<div class='col-sm-2'></div>
				<label class='label-control col-sm-2'><b>Kebutuhan Per Bulan (Kg)</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'kg_per_bulan','name'=>'kg_per_bulan','class'=>'form-control input-md autoNumeric','autocomplete'=>'off','placeholder'=>'Kebutuhan Per Bulan'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Description <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_textarea(array('id'=>'descr','name'=>'descr','class'=>'form-control input-md','rows'=>'2','cols'=>'75','autocomplete'=>'off','placeholder'=>'Description'));
					?>
				</div>
				
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Warehouse <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_warehouse' id='id_warehouse' class='form-control input-md'>
					<?php
						foreach($getWarehouse AS $val => $valx){
							$selected=($valx['id'] == 2)?'selected':'';
							echo "<option value='".$valx['id']."' ".$selected.">".strtoupper(strtolower($valx['nm_gudang']))."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<div class='col-sm-9'>

					<input type='hidden' name='numberMax' id='numberMax' value='0'>
					<input type='hidden' name='numberMax_en' id='numberMax_en' value='0'>
					<input type='hidden' name='numberMax_bq' id='numberMax_bq' value='0'>
				</div>
			</div>
			<div class="box box-warning">
				<div class="box-header">
					<h3 class="box-title"><b>STANDART ENGINEERING</b></h3>
				</div>
				<button type="button" id='add_en' style='width:130px; margin-right:11px; margin-bottom:3px; float:right;' class="btn btn-success">Add Standard Eng</button>
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead id='head_table_en'>
							<tr class='bg-blue'>
								<th class="text-center" class="no-sort" width="50px">No</th>
								<th class="text-center">Standart Engineering Name</th>
								<th class="text-center" style='width: 240px;'>Standard Engineering Value</th>
								<th class="text-center" style='width: 250px;'>Descr Engineering</th>
								<th class="text-center" style='width: 80px;'>Flag</th>
								<th class="text-center" style='width: 80px;'>Option</th>
							</tr>
						</thead>
						<tbody id='detail_body_en'></tbody>
						<tbody id='detail_body_enKosong'>
							<tr>
								<td colspan='7'>Please enter standard engineering data. Click Add Standard Eng ...</td>
							</tr>
						</tbody>
						<tbody class='selectTy'>
							<tr>
								<td colspan='7'>Please select the Material Type first ...</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="box box-warning">
				<div class="box-header">
					<h3 class="box-title"><b>STANDART BQ</b></h3>
				</div>
				<button type="button" id='add_bq' style='width:130px; margin-right:11px; margin-bottom:3px; float:right;' class="btn btn-success">Add Standard BQ</button>
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<thead id='head_table_bq'>
							<tr class='bg-blue'>
								<th class="text-center" class="no-sort" width="50px">No</th>
								<th class="text-center">Standart BQ Name</th>
								<th class="text-center" style='width: 240px;'>Standard BQ Value</th>
								<th class="text-center" style='width: 250px;'>Descr BQ</th>
								<th class="text-center" style='width: 80px;'>Flag</th>
								<th class="text-center" style='width: 80px;'>Option</th>
							</tr>
						</thead>
						<tbody id='detail_body_bq'></tbody>
						<tbody id='detail_body_bqKosong'>
							<tr>
								<td colspan='7'>Please enter standard bq data. Click Add Standard Bq ...</td>
							</tr>
						</tbody>
						<tbody class='selectTy'>
							<tr>
								<td colspan='7'>Please select the Material Type first ...</td>
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
	#id_category_chosen{
		width: 100% !important;
	}
	#id_satuan_chosen{
		width: 100% !important;
	}
	.tgl{
		cursor:pointer;
	}
</style>
<script>

	$('.tgl').datepicker({
		dateFormat : 'yy-mm-dd',
		minDate: 0,
		changeMonth: true,
        changeYear: true,
	});
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('#detail_body_enKosong').hide();
		$('#detail_body_bqKosong').hide();
		$('#add_en').hide();
		$('#add_bq').hide();

		var nomor	= 1;

		$(document).on('change', '#id_category', function(){
			$('#add_en').show();
			$('#add_bq').show();
			$('#detail_body_enKosong').show();
			$('#detail_body_bqKosong').show();
			$('.selectTy').hide();
			if($(this).val() == ''){
				$('#add_en').hide();
				$('#add_bq').hide();
				$('#detail_body_enKosong').hide();
				$('#detail_body_bqKosong').hide();
				$('.selectTy').show();
			}
		});

		$('#add_en').click(function(e){
			e.preventDefault();
			AppendBaris_en(nomor);
			$('#head_table_en').show();
			$('.chosen_select').chosen({width: '100%'});

			var nilaiAwal	= parseInt($("#numberMax_en").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax_en").val(nilaiAkhir);
			$("#detail_body_enKosong").hide();
		});

		$('#add_bq').click(function(e){
			e.preventDefault();
			AppendBaris_bq(nomor);
			$('#head_table_bq').show();
			$('.chosen_select').chosen({width: '100%'});

			var nilaiAwal	= parseInt($("#numberMax_bq").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax_bq").val(nilaiAkhir);
			$("#detail_body_bqKosong").hide();
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			var idmaterial				= $('#idmaterial').val();
			var nm_material				= $('#nm_material').val();
			var nm_dagang					= $('#nm_dagang').val();
			var nm_international		= $('#nm_international').val();
			var id_category				= $('#id_category').val();
			var id_satuan				= $('#id_satuan').val();
			var id_packing				= $('#id_packing').val();
			var satuan_kg				= $('#satuan_kg').val();
			var saldo_kg				= $('#saldo_kg').val();
			var nilai_konversi			= $('#nilai_konversi').val();
			var safety_stock			= $('#safety_stock').val();

			$(this).prop('disabled',true);

			if(idmaterial=='' || idmaterial==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Material ID is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;

			}
			if(nm_material=='' || nm_material==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Material Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;

			}
			if(nm_dagang == '' || nm_dagang == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Trade Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(nm_international == '' || nm_international == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Internasional Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_category=='' || id_category==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Type Material Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;

			}
			if(safety_stock == '' || safety_stock == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Min Stock Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_satuan == '' || id_satuan == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Unit is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(nilai_konversi == '' || nilai_konversi == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Conversion Value is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_packing == '' || id_packing == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Unit Packing is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}

			

			//validasi supplier
			var intL = 0;
			var intError = 0;
			var pesan = '';

			$('#detail_body_bq').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var id_category_standard_bq	= $('#id_category_standard_bq_'+nomor[1]).val();
				var nilai_standard_bq		= $('#nilai_standard_bq_'+nomor[1]).val();
				var descr_bq				= $('#descr_bq_'+nomor[1]).val();


				if(descr_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description BQ number has not empty ...";
				}
				if(nilai_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : standard value BQ number has not empty ...";
				}
				if(id_category_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard BQ number has not been chosen ...";
				}
			});

			$('#detail_body_en').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var id_category_standard_en	= $('#id_category_standard_en_'+nomor[1]).val();
				var nilai_standard_en		= $('#nilai_standard_en_'+nomor[1]).val();
				var descr_en				= $('#descr_en_'+nomor[1]).val();


				if(descr_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description ENG number has not empty ...";
				}
				if(nilai_standard_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : standard value ENG number has not empty ...";
				}
				if(id_category_standard_en == '0' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard ENG number has not been chosen ...";
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

			// alert('Success Validate');
			$('#simpan-bro').prop('disabled',false);
			// return false;

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
					  // alert($('#numberMax_bq').val());
					  // return false;
						loading_spinner();
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/add2';
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
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									window.location.href = base_url + active_controller;
								}
								if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								}
								if(data.status == 3){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
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

	function delRow_En(row){
		$('#tren_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax_en").val() - 1;
		$("#numberMax_en").val(updatemax);

		var maxLine = $("#numberMax_en").val();
		if(maxLine == 0){
			$("#detail_body_enKosong").show();
		}
	}

	function delRow_Bq(row){
		$('#trbq_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax_bq").val() - 1;
		$("#numberMax_bq").val(updatemax);

		var maxLine = $("#numberMax_bq").val();
		if(maxLine == 0){
			$("#detail_body_bqKosong").show();
		}
	}

	function AppendBaris_en(intd)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body_en').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_en tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='tren_"+nomor+"'>";
			Rows	+= 	"<td>";
			Rows	+= 		"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail_en["+nomor+"][id_category_standard_en]' id='id_category_standard_en_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Standard</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' style='text-align: right;' name='ListDetail_en["+nomor+"][nilai_standard_en]' id='nilai_standard_en_"+nomor+"' data-decimal='.' data-thousand='' data-prefix='' data-precision='0' data-allow-zero='true' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail_en["+nomor+"][descr_en]' id='descr_en_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail_en["+nomor+"][flag_active_en]' value='Y' id='flag_active_en_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' id='del_acc' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_En("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_en').append(Rows);
		// $("#nilai_standard_en_"+nomor).mask('?999999999999');

		var id_category_standard_en = "#id_category_standard_en_"+nomor;
		// console.log(accID);
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getId_category_standard_en',
			cache: false,
			type: "POST",
			data: "id_category="+$("#id_category").val(),
			dataType: "json",
			success: function(data){
				$(id_category_standard_en).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}

	function AppendBaris_bq(intd)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body_bq').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_bq tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='trbq_"+nomor+"'>";
			Rows	+= 	"<td>";
			Rows	+= 		"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail_bq["+nomor+"][id_category_standard_bq]' id='id_category_standard_bq_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Standard</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' style='text-align: right;' name='ListDetail_bq["+nomor+"][nilai_standard_bq]' id='nilai_standard_bq_"+nomor+"' data-decimal='.' data-thousand='' data-prefix='' data-precision='0' data-allow-zero='true' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail_bq["+nomor+"][descr_bq]' id='descr_bq_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail_bq["+nomor+"][flag_active_bq]' value='Y' id='flag_active_bq_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' id='del_acc' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Bq("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_bq').append(Rows);
		// $("#nilai_standard_bq_"+nomor).mask('?999999999999');

		var id_category_standard_bq = "#id_category_standard_bq_"+nomor;
		// console.log(accID);
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getId_category_standard_bq',
			cache: false,
			type: "POST",
			data: "id_category="+$("#id_category").val(),
			dataType: "json",
			success: function(data){
				$(id_category_standard_bq).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}
</script>
