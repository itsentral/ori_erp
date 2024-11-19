<?php
$this->load->view('include/side_menu'); 
// echo"<pre>";print_r($row);
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Category Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'category','name'=>'category','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Menu Name'), $row[0]['category']);	
						echo form_input(array('type'=>'hidden','id'=>'id_category','name'=>'id_category','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Menu Name'), $row[0]['id_category']);								
					?>		
				</div>
				<label class='label-control col-sm-2'><b>Status Category <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<div class="input-group">
						<?php
							$active		= ($row[0]['flag_active'] =='Y')?TRUE:FALSE;
							$data = array(
									'name'          => 'flag_active',
									'id'            => 'flag_active',
									'value'         => 'Y',
									'checked'       => $active,
									'class'         => 'input-md'
							);
							echo form_checkbox($data).'&nbsp;&nbsp;Yes';
						?>
					</div>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Description<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_textarea(array('id'=>'descr','name'=>'descr','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Description type material'), $row[0]['descr']);											
					?>	
				</div>
			</div>
			
			<button type="button" id='edit_standard' style='width:150px; margin-right: 11px; margin-bottom: 5px; float:right;' class="btn btn-warning">Edit Standard</button>
			<button type="button" id='cancel_edit_standard' style='width:150px; margin-right: 11px; margin-bottom: 5px; float:right;' class="btn btn-danger">Cancel Edit Standard</button>
			<button type="button" id='update_edit_standard' style='width:150px; margin-right: 11px; margin-bottom: 5px; float:right;' class="btn btn-primary">Update Edit Standard</button>
			<h4 style='margin-left: 11px;'>Engineering Standard</h4>
			<div class="box-body" style="">
				<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_enEdit'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center"  style='width: 400px;'>Standard Name</th>
							<th class="text-center">Descr</th>
							<th class="text-center" style='width: 100px;'>Flag</th>
						</tr>
					</thead>
					<tbody id='detail_body_enEd'>
						<?php
							$number = 0;
							foreach($detailEn AS $val =>$valx){
								$number++;
								$status	= 'Active';
								$class	= 'bg-green';
								if($valx['flag_active'] == 'N'){
									$class	= 'bg-red';
									$status	= 'Not Active';
								}
								?>
								<tr id="trenEd_<?= $number;?>">
									<td><?= $number;?></td>
									<td>
										<div class='dataR'><?= ucwords(strtolower($valx['nm_category_standard']));?></div>
										<div class='dataE'>
											<input type='text' class='form-control' name='EdListDetail_en[<?=$number;?>][nm_category_standard]' id='Ednm_category_standard_en_<?= $number;?>' value='<?= ucwords(strtolower($valx['nm_category_standard']));?>'>
											<input type='hidden' class='form-control' name='EdListDetail_en[<?=$number;?>][id_category]' value='<?= $valx['id_category'];?>'>
											<input type='hidden' class='form-control' name='EdListDetail_en[<?=$number;?>][id_category_standard]' value='<?= $valx['id_category_standard'];?>'>
										</div>
									</td>
									<td>
										<div class='dataR'><?= ucfirst($valx['descr']);?></div>
										<div class='dataE'><input type='text' class='form-control' name='EdListDetail_en[<?=$number;?>][descr]' id='Eddescr_en_<?= $number;?>' value='<?= ucfirst($valx['descr']);?>'></div>
									</td>
									<td align='center'>
										<div class='dataR'><span class='badge <?=$class;?>'><?= $status;?></span></div>
										<div class='dataE'>
											<?php
												$active		= ($valx['flag_active'] =='Y')?TRUE:FALSE;
												$data = array(
														'name'          => "EdListDetail_en[".$number."][flag_active]",
														'id'            => "Edflag_active_en_".$number."",
														'value'         => 'Y',
														'checked'       => $active,
														'class'         => 'input-sm'
												);
												echo form_checkbox($data).'&nbsp;&nbsp;Yes';
											?>
										</div>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<h4 style='margin-left: 11px;'>BQ Standard</h4>
			
			<div class="box-body" style="">
				<table id="my-grid_bq" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_bqEdit'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center"  style='width: 400px;'>Standard Name</th>
							<th class="text-center">Descr</th>
							<th class="text-center" style='width: 100px;'>Flag</th>
						</tr>
					</thead>
					<tbody id='detail_body_bqEd'>
						<?php
							$number = 0;
							foreach($detailBQ AS $val =>$valx){
								$number++;
								$status	= 'Active';
								$class	= 'bg-green';
								if($valx['flag_active'] == 'N'){
									$class	= 'bg-red';
									$status	= 'Not Active';
								}
								?>
								<tr id="trbqEd_<?= $number;?>">
									<td><?= $number;?></td>
									<td>
										<div class='dataR'><?= ucwords(strtolower($valx['nm_category_standard']));?></div>
										<div class='dataE'>
											<input type='text' class='form-control' name='EdListDetail_bq[<?=$number;?>][nm_category_standard]' id='Ednm_category_standard_bq_<?= $number;?>' value='<?= ucwords(strtolower($valx['nm_category_standard']));?>'>
											<input type='hidden' class='form-control' name='EdListDetail_bq[<?=$number;?>][id_category]' value='<?= $valx['id_category'];?>'>
											<input type='hidden' class='form-control' name='EdListDetail_bq[<?=$number;?>][id_category_standard]' value='<?= $valx['id_category_standard'];?>'>
										</div>
									</td>
									<td>
										<div class='dataR'><?= ucfirst($valx['descr']);?></div>
										<div class='dataE'><input type='text' class='form-control' name='EdListDetail_bq[<?=$number;?>][descr]' id='Eddescr_bq_<?= $number;?>' value='<?= ucfirst($valx['descr']);?>'></div>
									</td>
									<td align='center'>
										<div class='dataR'><span class='badge <?=$class;?>'><?= $status;?></span></div>
										<div class='dataE'>
											<?php
												$active		= ($valx['flag_active'] =='Y')?TRUE:FALSE;
												$data = array(
														'name'          => "EdListDetail_bq[".$number."][flag_active]",
														'id'            => "Edflag_active_bq_".$number."",
														'value'         => 'Y',
														'checked'       => $active,
														'class'         => 'input-sm'
												);
												echo form_checkbox($data).'&nbsp;&nbsp;Yes';
											?>
										</div>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<br>			
			<div class='form-group row'>		 	 
				<div class='col-sm-4'>    
					<button type="button" id='add_en' style='width:130px; margin-left: 11px;' class="btn btn-success">Add Standard En</button>
					<input type='hidden' name='numberMax_en' id='numberMax_en' value='0'>
					<button type="button" id='add_bq' style='width:130px; margin-left: 5px;' class="btn btn-success">Add Standard Bq</button>
					<input type='hidden' name='numberMax_bq' id='numberMax_bq' value='0'>
				</div>
			</div>
			<div class="box-body" style="">
				<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_en'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center"  style='width: 400px;'>Standard Engineering Name</th>
							<th class="text-center">Descr</th>
							<th class="text-center" style='width: 100px;'>Flag</th>
							<th class="text-center" style='width: 70px;'>Option</th>
						</tr>
					</thead>
					<tbody id='detail_body_en'>
					</tbody>
				</table>
			</div>
			<div class="box-body" style="">
				<table id="my-grid_bq" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_bq'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center"  style='width: 400px;'>Standard BQ Name</th>
							<th class="text-center">Descr</th>
							<th class="text-center" style='width: 100px;'>Flag</th>
							<th class="text-center" style='width: 70px;'>Option</th>
						</tr>
					</thead>
					<tbody id='detail_body_bq'>
					</tbody>
				</table>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro', 'style'=>'margin-left:10px; width:75px;')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()', 'style'=>'margin-left:10px; width:75px;'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.dataE').hide();
		$('#update_edit_standard').hide();
		$('#cancel_edit_standard').hide();
		$('#head_table_en').hide();
		$('#head_table_bq').hide();
		var nomor	= 1;
		
		$('#add_en').click(function(e){
			e.preventDefault();
			// console.log(nomor);
			AppendBaris_En(nomor);
			$('#head_table_en').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax_en").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax_en").val(nilaiAkhir);
			$('#simpan-bro').show();
		});
		
		$('#add_bq').click(function(e){
			e.preventDefault();
			// console.log(nomor);
			AppendBaris_Bq(nomor);
			$('#head_table_bq').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax_bq").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax_bq").val(nilaiAkhir);
			$('#simpan-bro').show();
		});
		
		$(document).on('click', '#edit_standard', function(){
			$('#update_edit_standard').show();
			$('#cancel_edit_standard').show();
			$('#edit_standard').hide();
			$('#simpan-bro').hide();
			$('.dataE').show();
			$('.dataR').hide();
		});
		
		$(document).on('click', '#cancel_edit_standard', function(){
			$('#update_edit_standard').hide();
			$('#cancel_edit_standard').hide();
			$('#edit_standard').show();
			$('#simpan-bro').show();
			$('.dataR').show();
			$('.dataE').hide();
		});
		//Update standard
		$(document).on('click', '#update_edit_standard', function(e){
			e.preventDefault();
			var intL = 0;
			var intError = 0;
			var pesan = '';
			//validasi standard BQ
			$('#detail_body_bqEd').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var descr_bq	= $('#Eddescr_bq_'+nomor[1]).val();
				var nm_category_standard_bq		= $('#Ednm_category_standard_bq_'+nomor[1]).val();
				
				if(descr_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description BQ number has not empty ...";
				}
				if(nm_category_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard BQ number has not empty ...";
				}
			});
			
			//validasi datndard ENG
			$('#detail_body_enEd').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var descr_en	= $('#Eddescr_en_'+nomor[1]).val();
				var nm_category_standard_en		= $('#Ednm_category_standard_en_'+nomor[1]).val();
				
				if(descr_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description enggenering number has not empty ...";
				}
				if(nm_category_standard_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard enggenering number has not empty ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$('#update_edit_standard').prop('disabled',false);
				return false;
			}
			
			$('#update_edit_standard').prop('disabled',false);
			
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
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/updateDetail/'+$('#id_category').val();
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
									$('#update_edit_standard').prop('disabled',false);
								}
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
								$('#update_edit_standard').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#update_edit_standard').prop('disabled',false);
					return false;
				  }
			});
		});
		
		
		//Update AND SAVE TAMBAHAN
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			var category	= $('#category').val();
			var descr		= $('#descr').val();
			$(this).prop('disabled',true);
			if(category=='' || category==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Type Material Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			
			if(descr == '' || descr == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Description Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			//validasi standard BQ
			$('#detail_body_bq').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var descr_bq	= $('#descr_bq_'+nomor[1]).val();
				var nm_category_standard_bq		= $('#nm_category_standard_bq_'+nomor[1]).val();
				
				if(descr_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description BQ number has not empty ...";
				}
				if(nm_category_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard BQ number has not empty ...";
				}
			});
			
			//validasi datndard ENG
			$('#detail_body_en').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var descr_en	= $('#descr_en_'+nomor[1]).val();
				var nm_category_standard_en		= $('#nm_category_standard_en_'+nomor[1]).val();
				
				if(descr_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description enggenering number has not empty ...";
				}
				if(nm_category_standard_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard enggenering number has not empty ...";
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
				  closeOnConfirm: true,
				  closeOnCancel: false
				},
				function(isConfirm) {
				  if (isConfirm) {
						loading_spinner();
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/edit';
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
								else if(data.status == 2){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									$('#simpan-bro').prop('disabled',false);
								}
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
			$('#head_table_en').hide();
			// $('#simpan-bro').hide();
		}
	}
	
	function delRow_Bq(row){
		$('#trbq_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax_bq").val() - 1;
		$("#numberMax_bq").val(updatemax);
		
		var maxLine = $("#numberMax_bq").val();
		if(maxLine == 0){
			$('#head_table_bq').hide();
			// $('#simpan-bro').hide();
		}
	}

	function AppendBaris_En(intd)
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
			Rows	+=		"<select name='ListDetail_en["+nomor+"][nm_category_standard]' id='nm_category_standard_en_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Standard</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail_en["+nomor+"][descr]' id='descr_en_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail_en["+nomor+"][flag_active]' value='Y' id='flag_active_en_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_En("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_en').append(Rows);
		var nm_category_standard_en_ = "#nm_category_standard_en_"+nomor;
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			// data: "merk="+$("#merk").val()+"&model="+$("#model").val(),
			dataType: "json",
			success: function(data){
				$(nm_category_standard_en_).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}
	
	function AppendBaris_Bq(intd)
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
			Rows	+=		"<select name='ListDetail_bq["+nomor+"][nm_category_standard]' id='nm_category_standard_bq_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Standard</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail_bq["+nomor+"][descr]' id='descr_bq_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail_bq["+nomor+"][flag_active]' value='Y' id='flag_active_bq_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Bq("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_bq').append(Rows);
		var nm_category_standard_bq_ = "#nm_category_standard_bq_"+nomor;
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getStandard',
			cache: false,
			type: "POST",
			// data: "merk="+$("#merk").val()+"&model="+$("#model").val(),
			dataType: "json",
			success: function(data){
				$(nm_category_standard_bq_).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}
</script>
