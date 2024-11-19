<?php
	$id_product = $this->uri->segment(3);
	// echo $id_product;
	$qHeader		= "SELECT * FROM component_header WHERE id_product='".$id_product."'";
	$qDetail1		= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail2		= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$qDetail3		= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000' AND a.id_category <> 'TYP-0001'";
	$detailResin1	= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin2	= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$detailResin3	= "SELECT a.*, b.price_ref_estimation FROM component_detail a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_category ='TYP-0001' ORDER BY a.id_detail DESC LIMIT 1 ";
	$qDetailPlus1	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus2	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus3	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS' AND a.id_material <> 'MTL-1903000'";
	$qDetailPlus4	= "SELECT a.*, b.price_ref_estimation FROM component_detail_plus a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='TOPCOAT' AND a.id_material <> 'MTL-1903000'";
	$qDetailAdd1	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='LINER THIKNESS / CB'";
	$qDetailAdd2	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='STRUKTUR THICKNESS'";
	$qDetailAdd3	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='EXTERNAL LAYER THICKNESS'";
	$qDetailAdd4	= "SELECT a.*, b.price_ref_estimation FROM component_detail_add a LEFT JOIN raw_materials b ON a.id_material = b.id_material WHERE a.id_product='".$id_product."' AND a.detail_name='TOPCOAT'";
	
	$restHeader		= $this->db->query($qHeader)->result_array();
	$restDetail1	= $this->db->query($qDetail1)->result_array();
	$restDetail2	= $this->db->query($qDetail2)->result_array();
	$restDetail3	= $this->db->query($qDetail3)->result_array();
	$numRows3		= $this->db->query($qDetail3)->num_rows();
	$restResin1			= $this->db->query($detailResin1)->result_array();
	$restResin2			= $this->db->query($detailResin2)->result_array();
	$restResin3			= $this->db->query($detailResin3)->result_array();
	$restDetailPlus1	= $this->db->query($qDetailPlus1)->result_array();
	$restDetailPlus2	= $this->db->query($qDetailPlus2)->result_array();
	$restDetailPlus3	= $this->db->query($qDetailPlus3)->result_array();
	$restDetailPlus4	= $this->db->query($qDetailPlus4)->result_array();
	$NumDetailPlus4		= $this->db->query($qDetailPlus4)->num_rows();
	$restDetailAdd1		= $this->db->query($qDetailAdd1)->result_array();
	$restDetailAdd2		= $this->db->query($qDetailAdd2)->result_array();
	$restDetailAdd3		= $this->db->query($qDetailAdd3)->result_array();
	$restDetailAdd4		= $this->db->query($qDetailAdd4)->result_array();
	$NumDetailAdd1		= $this->db->query($qDetailAdd1)->num_rows();
	$NumDetailAdd2		= $this->db->query($qDetailAdd2)->num_rows();
	$NumDetailAdd3		= $this->db->query($qDetailAdd3)->num_rows();
	$NumDetailAdd4		= $this->db->query($qDetailAdd4)->num_rows();
	
	$qCustomer			= "SELECT nm_customer, produk_jual FROM customer WHERE id_customer='".$restHeader[0]['standart_by']."' ";
	$restCustomer		= $this->db->query($qCustomer)->result_array();

?>
	<div class="box">
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr>
						<td class="text-left"><u>Component ID</u></td>
						<td class="text-left" colspan='5'><b><?= $id_product; ?></b><input type='hidden' name='id_product' id='id_product' value='<?= $id_product;?>'></td>
					</tr>
					<tr>
						<td class="text-left"><u>Component GROUP</u></td>
						<td class="text-left" colspan='5'><?= strtoupper($restHeader[0]['parent_product']." || ".$restHeader[0]['resin_sistem']." || ".$restHeader[0]['pressure']." BAR || ".$restHeader[0]['diameter']." MM || ".$restHeader[0]['liner']." MM | ".$restHeader[0]['stiffness']." || ".$restHeader[0]['criminal_barier']." || ".$restHeader[0]['vacum_rate']." || ".$restHeader[0]['aplikasi_product']); ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Custom By</u></td>
						<td class="text-left" colspan='5'><?= $restCustomer[0]['nm_customer']; ?></td>
					</tr>
					<tr>
						<td class="text-left" width='20%'><u>Product Name</u></td>
						<td class="text-left" width='20%'><?= strtoupper($restHeader[0]['nm_product']); ?></td>
						<td class="text-left" width='15%'><u>Diameter</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['diameter']; ?> mm</td>
						<td class="text-left" width='15%'><u>Length</u></td>
						<td class="text-left" width='15%'><?= $restHeader[0]['panjang']; ?> mm</td>
					</tr>
					<tr>
						<td class="text-left"><u>Standard Tolerance By</u></td>
						<td class="text-left"><?= strtoupper($restHeader[0]['standart_toleransi']); ?></td>
						<td class="text-left"><u>Max</u></td>
						<td class="text-left"><?= $restHeader[0]['max_toleransi']; ?></td>
						<td class="text-left"><u>Min</u></td>
						<td class="text-left"><?= $restHeader[0]['min_toleransi']; ?></td>
					</tr>
					<tr>
						<td class="text-left"><u>Product Application</u></td>
						<td class="text-left"><?= strtoupper($restHeader[0]['aplikasi_product']); ?></td>
						<td class="text-left"><u>Thickness Pipe (Design)</u></td>
						<td class="text-left"><?= $restHeader[0]['design']; ?></td>
						<td class="text-left"><u>Thickness Pipe (EST)</u></td>
						<td class="text-left"><?= $restHeader[0]['est']; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="box box-primary">
			<div class="box-body" style="">
				<div class="box-header">
				<h3 class="box-title">Approve Price Per Material</h3>		
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class='form-group row'>		 	 
						<label class='label-control col-sm-2'><b>Approve <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>             
								<select name='status' id='status' class='form-control input-md'>
									<option value='0'>Select Action</option>
									<option value='Y'>APPROVE</option>
									<option value='N'>REJECT</option>
								</select>
						</div>
						<div id='HideReject'>
							<label class='label-control col-sm-2'><b>Reject Reason <span class='text-red'>*</span></b></label>
							<div class='col-sm-4'>            
									<?php
										echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Reject reason'));											
																					
									
									?>		
							</div>
						</div>
					</div>		
				</div>
				<div class='box-footer' align='right'>
					<?php
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px;','value'=>'save','content'=>'Save','id'=>'approvedQ')).' ';
					?>
				</div>
			</div>
		</div>
	</div>
	
	<script>
	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
				$('#approve_reason').val('')
			}
		});
		
		$(document).on('click', '#approvedQ', function(){
			var bF				= $('#id_product').val();
			var status 			= $('#status').val();
			var approve_reason 	= $('#approve_reason').val();
			
			if(status == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Action approve belum dipilih ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			if(status == 'N' && approve_reason == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Alasan reject masih kosong ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
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
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/approveMat/'+bF,
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
								window.location.href = base_url + active_controller+'/app_mat';
							}
							else if(data.status == 0){
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
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
	});
		
	
	</script>