<?php

$parent_product 	= str_replace('_', ' ', $this->uri->segment(3));
$standart_code 		= str_replace('_', ' ', $this->uri->segment(4));
// echo $id;
$get_Data			= $this->db->query("SELECT * FROM product_parent WHERE estimasi='Y' ORDER BY product_parent ASC")->result_array();
$get_Std			= $this->db->query("SELECT * FROM help_default_name ORDER BY nm_default ASC")->result_array();

$sqlSup				= "SELECT * FROM cycletime_step ORDER BY step_name ASC";
$restSup			= $this->db->query($sqlSup)->result_array();

$getData			= $this->db->query("SELECT * FROM cycle_time_step WHERE parent_product='".$parent_product."' AND standart_code='".$standart_code."'")->row();
$getDataArr			= $this->db->query("SELECT * FROM cycle_time_step WHERE parent_product='".$parent_product."' AND standart_code='".$standart_code."' AND `delete`='N' ORDER BY urutan ASC")->result_array();
// echo "SELECT * FROM cycle_time_step WHERE parent_product='".$parent_product."' AND standart_code='".$standart_code."'";
?>

<div class="box box-success">
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Komponen <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='product_parent' id='product_parent' class='form-control input-md'>
						<?php
							foreach($get_Data AS $val => $valx){
								$selx2 = ($getData->parent_product == $valx['product_parent'])?'selected':'';
								echo "<option value='".strtolower($valx['product_parent'])."' ".$selx2.">".strtoupper($valx['product_parent'])."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Standart <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'> 
					<select name='standart_code' id='standart_code' class='form-control input-md'>
						<?php
							foreach($get_Std AS $val => $valx){
								$selx = ($getData->standart_code == $valx['nm_default'])?'selected':'';
								echo "<option value='".strtoupper($valx['nm_default'])."' ".$selx.">".strtoupper($valx['nm_default'])."</option>";
							}
						?>
					</select>
				</div>
			</div>
					
		</div>
	 </div>
	 <div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table_enEdit'>
					<tr class='bg-blue'>
						<th class="text-center" width='10%'>No</th>
						<th class="text-center" width='20%'>Urutan Step</th>
						<th class="text-center" >Nama Step</th>
					</tr>
				</thead>
				<tbody id='detail_body_Ed'>
					<?php
						$number = 0;
						foreach($getDataArr AS $val =>$valx){
							$number++;
							?> 
							<tr id='trliner_<?=$number;?>'>
								<td align='center'><button type='button' class='btn btn-danger btn-sm del_record ' data-nomor='<?=$number;?>' title='Delete Record'>Delete Record</button></td>
								<td>STEP NAME</td>
								<td>
									<select name='ListStep[<?=$number?>][step]' class='form-control input-md'>
										<?php
											foreach($restSup AS $val2 => $valx2){
												$selx = ($valx['step'] == $valx2['step_name'])?'selected':'';
												echo "<option value='".strtoupper($valx2['step_name'])."' ".$selx.">".strtoupper($valx2['step_name'])."</option>";
											}
										?>
									</select>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
					
		</div>
		<div class="box-body" style="">
			<table id="my-grid_liner" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody id='detail_body_liner'>
				</tbody>
			</table>
		</div>
		<button type='button' name='add_liner' id='add_liner' class='btn btn-success btn-md' style='width:100px; margin-left: 10px;'>Add Step</button> 
		<br>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'width:100px;','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			?>
		</div>
	 </div>
</div>
<script>
	$(document).ready(function(){ 
		swal.close();
	});
	
	$('#simpan-bro').click(function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var product_parent	= $('#product_parent').val();
		var standart_code	= $('#standart_code').val();
		
		if(product_parent=='0' || product_parent==null){
			swal({
			  title	: "Error Message!",
			  text	: 'Component is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;	
		}
		if(standart_code=='0' || standart_code==null){
			swal({
			  title	: "Error Message!",
			  text	: 'Standart is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;	
		}

		
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
					var baseurl		= base_url + active_controller +'/edit_step';
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
								window.location.href = base_url + active_controller +'/step';
							}
							else{ 
								if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								else{
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
	
	var nomor	= 100;
	

	$('#add_liner').click(function(e){
		e.preventDefault();
		AppendBaris_Liner(nomor);
	});

	
	$(document).on('click','.del_record', function(){
		$('#trliner_'+$(this).data('nomor')).remove();
	});
		
	
	function AppendBaris_Liner(intd){
		var nomor	= 100;
		var valuex	= $('#detail_body_liner').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_liner tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='trliner_"+nomor+"'>";
			Rows 	+= 	"<td align='center' width='10%'>";
			Rows 	+=		"<div><button type='button' class='btn btn-danger btn-sm del_record ' data-nomor='"+nomor+"' title='Delete Record'>Delete Record</button></div>";
			Rows 	+= 	"</td>";
			Rows	+= 	"<td width='20%' style='vertical-align: middle;'>";
			Rows	+=		"STEP NAME";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListStep["+nomor+"][step]' id='step_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
			Rows	+= 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_liner').append(Rows);
		var step 	= "#step_"+nomor;
		
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getCategory',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(step).html(data.option).trigger("chosen:updated");
			}
		});
		
		nomor++;
	}
</script>