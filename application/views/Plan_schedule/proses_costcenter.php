
<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-left">
			<input type='hidden' id='no_ipp' name='no_ipp' value='<?=$no_ipp;?>'>
		</div>
	</div>
	<div class="box-body">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='4%'>No</th>
					<th class="text-center" style='vertical-align:middle;' width='16%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>No Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;'>Id Spool</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Must Finish</th>
					<th class="text-center" style='vertical-align:middle;' width='20%'>Costcenter</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$Sum = 0;
					$no = 0;
					if(!empty($detail)){
						foreach($detail AS $val => $valx){ $no++;
							$spaces = "";
							$id_delivery = strtoupper($valx['id_delivery']);
							$bgwarna	= "bg-blue";
								
							if($valx['sts_delivery'] == 'CHILD'){
								$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$id_delivery = strtoupper($valx['sub_delivery']);
								$bgwarna	= "bg-green";
							}
							
							$get_list = $this->db->select('id_spool')->get_where('master_spool_use',array('id_milik'=>$valx['id_milik']))->result_array();
							$dtListArray = array();
							foreach($get_list AS $val2 => $valx2){
								$dtListArray[$val2] = $valx2['id_spool'];
							}
							$dtImplode	= "(".implode(",", $dtListArray).")";
							
							$dtImplode2	= "";
							
							if(!empty($get_list)){
								$get_spool = $this->db->query("SELECT id_spool FROM master_spool WHERE id IN ".$dtImplode." ")->result_array();
								
								$dtListArray2 = array();
								foreach($get_spool AS $val3 => $valx3){
									$dtListArray2[$val3] = $valx3['id_spool'];
								}
								$dtImplode2	= implode(", ", $dtListArray2);
							}
							
							$sql_det2 = "SELECT b.*, a.id AS id_dept, a.name AS nm_dept FROM scheduling_data b LEFT JOIN hris.departments a ON b.costcenter=a.id WHERE b.no_ipp='".$no_ipp."' AND b.id_milik='".$valx['id_milik']."'";
							$query_detail2 = $this->db->query($sql_det2)->result_array();
							
							$optx = "";
							foreach($query_detail2 AS $val3 => $valx3){
								if(!empty($valx3['costcenter'])){
									$optx .= "<option value='".$valx3['id_dept']."' selected>".strtoupper($valx3['nm_dept'])."</option>";
								}
							}
							$optionx		= (!empty($query_detail2))?$optx:'';
							
							$check_spool = $this->db->query("SELECT id_spool FROM master_spool WHERE no_ipp='".$no_ipp."' ")->result_array();
							
							$minDate = "";
							if(!empty($get_list)){
								if(empty($valx['must_finish'])){
									if(!empty($check_spool)){
										$min_date = $this->db->query("SELECT must_finish AS must_finish FROM master_spool WHERE id IN ".$dtImplode." AND must_finish IS NOT NULL ")->result();
										if(!empty($min_date)){
											$minDate = $min_date[0]->must_finish;
										}
									}
								}
							}
							if(!empty($valx['must_finish'])){
								$minDate = $valx['must_finish'];
							}
							
							$disabled = ($valx['sts_plan'] == 'Y')?'disabled':'';
							
							echo "<tr>";
								echo "<td align='center'>".$no."
										<input type='hidden' name='detail[".$no."][id_milik]' value='".$valx['id_milik']."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][no_ipp]' value='".$no_ipp."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][no_komponen]' value='".$valx['no_komponen']."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][product]' value='".$valx['id_category']."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][id_product]' value='".$valx['id_product']."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][dimensi]' value='".spec_bq($valx['id_milik'])."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][qty]' value='".$valx['qty']."' ".$disabled.">
										<input type='hidden' name='detail[".$no."][id_spool]' value='".$dtImplode2."' ".$disabled.">
										
										
										</td>";
								echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
								echo "<td align='left'>".$valx['no_komponen']."</td>";
								echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
								echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
								echo "<td align='left'>".$dtImplode2."</td>";
								echo "<td align='center'><input type='text' name='detail[".$no."][must_finish]' class='form-control input-sm text-center datepicker' value='".$minDate."' readonly ".$disabled."></td>";
								echo "<td align='left'>"; 
									echo "<select name='detail[".$no."][costcenter][]' class='form-control js-data-example-ajax' style='width:100%;' multiple ".$disabled.">".$optionx."</select>";
								echo "</td>";	
								echo "<td align='center'>"; 
									if($valx['sts_plan'] == 'N'){
										echo "<button type='button' class='btn btn-sm btn-success approved' data-id_milik='".$valx['id_milik']."' ".$disabled."><i class='fa fa-check'></i></button>";
									}
								echo "</td>";								
							echo "</tr>";
						}
					}
				?>
			</tbody>
		</table>
		<br>
		<a href="<?php echo site_url('plan_schedule/so') ?>" style='margin-top:5px; float:right;' class="btn btn-md btn-danger">Back</a>
		<button type='button' id='save_final' class='btn btn-success' style='margin-top:5px; margin-right:5px; float:right;'>Save Final</button>
		<button type='button' id='save_temp' class='btn btn-primary' style='margin-top:5px; margin-right:5px; float:right;'>Save Temp</button>	
	</div>
</div>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/select2/select2.min.css'); ?>"> 
<!--<link rel="stylesheet" href="<?php echo base_url('assets/select2/select2-bootstrap.css'); ?>"> -->
<script src="<?php echo base_url('assets/select2/select2.min.js') ?>"></script>
<style>
	.datepicker{
		cursor:pointer;
	}
	.vam{
		vertical-align:middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.chosen-container').remove();
		$('.datepicker').datepicker({
			showButtonPanel: true,
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
		
		$('.js-data-example-ajax').select2({
			multiple: true,
			ajax: {
				url: base_url + active_controller +'/dropdown_costcenter',
				dataType: 'json',
				type: "POST",
				delay: 250,
				processResults: function (data) {
					return {
						results: $.map(data.items, function (item) {
							return {
								text: item.text,
								id: item.id
							}
						})
					};
				}
			}
		});
		
		$(document).on('click', '.approved', function(e){
			e.preventDefault();
			var id_milik = $(this).data('id_milik');
			var no_ipp = $('#no_ipp').val();
			
			swal({
			  title: "Are you sure?",
			  text: "Approve ke produksi !!!",
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
					var baseurl		= base_url + active_controller +'/approve_satuan_product/'+id_milik+'/'+no_ipp;
					$.ajax({
						url			: baseurl,
						type		: "POST",
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
								
								window.location.href = base_url + active_controller +'/proses_costcenter/'+data.no_ipp;
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
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
		
		$(document).on('click', '#save_final', function(e){
			e.preventDefault();
			
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
					var baseurl		= base_url + active_controller +'/proses_costcenter/1';
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
								window.location.href = base_url + active_controller +'/so';
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#save_final').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save_final').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_final').prop('disabled',false);
				return false;
			  }
			});
		});
		
		$(document).on('click', '#save_temp', function(e){
			e.preventDefault();
			
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
					var baseurl		= base_url + active_controller +'/proses_costcenter';
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
								window.location.href = base_url + active_controller +'/so';
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#save_temp').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save_temp').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_temp').prop('disabled',false);
				return false;
			  }
			});
		});
		
	});
	
</script>