
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
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class='text-center vam' width='11%'>Spool</th>
					<th class='text-center vam' width='15%'>Nama Product</th>
					<th class='text-center vam' width='6%'>D1</th>
					<th class='text-center vam' width='6%'>D2</th>
					<th class='text-center vam' width='6%'>Thickness</th>
					<th class='text-center vam' width='6%'>Length/<br>Sudut</th>
					<th class='text-center vam' width='4%'>SR/LR</th>
					<th class='text-center vam' width='8%'>Delivery<br>Date</th>
					<th class='text-center vam' width='6%'>Dim<br>Check</th>
					<th class='text-center vam' width='6%'>Length<br>Check</th>
					<th class='text-center vam' width='29%'>Estimasi<br>(Ketik Diameter)</th>
					<!--<th class='text-center vam' width='10%'>Must<br>Finish Date</th>-->
				</tr>
			</thead>
			<tbody>
				<?php
				$no = 0;
				if(!empty($detail)){
				foreach($detail AS $val => $valx){
					$no++;
					$query_detail = $this->db->query("SELECT a.* FROM master_spool a WHERE a.no_ipp='".$no_ipp."' AND a.spool='".$valx['spool']."' ORDER BY a.id_spool ASC")->result_array();
					?>
						<tr id='spool_<?=$valx['spool'];?>'>
							<td class='vam'><b><?=$valx['spool'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><!--<button type='button' data-id='<?=$val;?>' id='add_spool_<?=$valx['spool'];?>' class='btn btn-sm btn-success show_add'>SHOW</button>--></td>
							<td colspan='11'></td>
						</tr>
					<?php 
					foreach($query_detail AS $val2 => $valx2){
						
						$sr_lr 			= (!empty($valx2['sr_lr']))?strtoupper($valx2['sr_lr']):'-';
						
						$sql_det2 = "SELECT b.* FROM master_spool_use b LEFT JOIN master_spool a ON b.id_spool=a.id WHERE a.no_ipp='".$no_ipp."' AND b.id_spool='".$valx2['id']."'";
						$query_detail2 = $this->db->query($sql_det2)->result_array();
						
						$optx = "";
						foreach($query_detail2 AS $val3 => $valx3){
							$plus = "";
							if($valx2['nm_product'] == 'pipe'){
								$plus = " LENGTH-".number_format($valx3['length']);
							}
							$optx .= "<option value='".$valx3['id_use']."' selected>".$valx3['id_product'].$plus."</option>";
						}
						
						$optionx		= (!empty($query_detail2))?$optx:'';
						
						$classPlus = ($valx2['nm_product'] == 'pipe')?'js-data-example-ajax_pipe':'js-data-example-ajax_non';
						
						$disabled = ($valx2['status'] == 'Y')?'disabled':'';
						
						echo "<tr id='detail_spool_".$val."' class='hide_detail spal_".$valx2['id']." hide_show_".$valx['spool']."'>";
							echo "<td>&nbsp;&nbsp;&nbsp;".$valx2['id_spool']."</td>";
							echo "<td>".strtoupper($valx2['nm_product'])."</td>";
							echo "<td align='right'><input type='text' name='detail[".$valx2['id']."][d1]' class='form-control text-right maskMoney' ".$disabled." value='".number_format($valx2['d1'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='right'><input type='text' name='detail[".$valx2['id']."][d2]' class='form-control text-right maskMoney' ".$disabled." value='".number_format($valx2['d2'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='right'><input type='text' name='detail[".$valx2['id']."][thickness]' class='form-control text-right maskMoney' ".$disabled." value='".number_format($valx2['thickness'],2)."'></td>";
							echo "<td align='right'><input type='text' name='detail[".$valx2['id']."][length_sudut]' class='form-control text-right maskMoney' ".$disabled." value='".number_format($valx2['length_sudut'],2)."'></td>";
							echo "<td align='center'>".$sr_lr."</td>";
							echo "<td align='center'>".date('d-m-Y', strtotime($valx2['delivery_date']))."</td>";
							if($valx2['max_dim'] == $valx2['d1']){
								$color = '#00ff00';
							}
							else{
								$color = 'red';
							}
							echo "<td align='right' style='background-color:".$color.";'>";
								echo "<span id='max_".$valx2['id']."'>".number_format($valx2['max_dim'])."</span>"; 
							echo "</td>";
							$color2 = 'transparant';
							if($valx2['max_length'] == $valx2['length_sudut'] AND $valx2['nm_product'] == 'pipe' AND $valx2['max_dim'] > 0){
								$color2 = '#00ff00';
							}
							if(round($valx2['max_length']) <> round($valx2['length_sudut']) AND $valx2['nm_product'] == 'pipe' AND $valx2['max_dim'] > 0){
								$color2 = 'red';
							}
							if($valx2['nm_product'] <> 'pipe' AND $valx2['max_dim'] > 0){
								$color2 = '#00ff00';
							}
							echo "<td align='right' style='background-color:".$color2.";'>";
								echo "<span id='length_".$valx2['id']."'>".number_format($valx2['max_length'])."</span>"; 
							echo "</td>";
							echo "<td>";
								echo "<select name='estimasi_".$valx2['id']."' id='".$valx2['id']."' data-nm_product='".$valx2['nm_product']."' data-dim1='".$valx2['d1']."' data-dim2='".$valx2['d2']."' data-sr_lr='".$valx2['sr_lr']."' ".$disabled." class='form-control js-data-example-ajax ".$classPlus."' style='width:100%;' multiple>".$optionx."</select>";
							// echo "</td>";
							// echo "<td>";
								echo "<input type='hidden' name='detail[".$valx2['id']."][must_finish]' class='form-control text-center datepicker' readonly value='".$valx2['must_finish']."' ".$disabled.">";
								echo "<input type='hidden' name='detail[".$valx2['id']."][id]' class='form-control text-center' value='".$valx2['id']."' ".$disabled.">";
							echo "</td>";
						echo "</tr>";
					}
				}
				}else{
					echo "<tr>";
						echo "<td colspan='12'>Tidak data yang ditampilkan</td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
		<a href="<?php echo site_url('plan_schedule/so') ?>" style='margin-top:5px; float:right;' class="btn btn-md btn-danger">Back</a>
		<?php if(!empty($detail)){ ?>
		<button type='button' id='save_schedule' class='btn btn-success' style='margin-top:5px; margin-right:5px; float:right;'>Save</button>	
		<?php } ?>
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
		swal.close();
		var no_ipp = $('#no_ipp').val();
		// $('.hide_detail').hide();
		$('.chosen-container').remove();
		$('.maskMoney').maskMoney();
		$('.datepicker').datepicker({
			showButtonPanel: true,
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
		
		$(document).on('click','.show_add', function(){
			var id = $(this).attr('id');
			var det_id	= id.split('_');
			var nomor	= det_id[2];
			
			$('.hide_show_'+nomor).slideToggle('slow');
			var htmL = $(this).html();
			// console.log(htmL)
			if(htmL == 'SHOW'){ $(this).html('HIDE') }
			if(htmL == 'HIDE'){ $(this).html('SHOW') }
			// alert(nomor);
		});
		
		$(".js-data-example-ajax" ).each(function() {
			var id_val = $(this).attr('id');
			$.ajax({
				url			: base_url + active_controller+'/get_update_estimasi_auto',
				type		: "POST",
				data: {
					"id_val" 	: id_val,
				},
				cache		: false,
				dataType	: 'json',
				success		: function(data){
					if(data.status == 1){
						$('#max_'+data.id_spool).html(data.max_dim);
						$('#length_'+data.id_spool).html(data.max_length);
						$('#class_'+data.id_spool).html(data.class);
						if(data.dim2x == data.dim1x){
							$(".spal_"+data.id_spool).find("td:nth-child(9)").attr('style','background-color:#00ff00;');
						}
						else{
							$(".spal_"+data.id_spool).find("td:nth-child(9)").attr('style','background-color:red;');
						}
						
						
						if(data.max_length2 == data.length){
							$(".spal_"+data.id_spool).find("td:nth-child(10)").attr('style','background-color:#00ff00;');
						}
						else{
							$(".spal_"+data.id_spool).find("td:nth-child(10)").attr('style','background-color:red;');
						}
					}
					window.location.href = base_url + active_controller +'/proses_jadwal/'+no_ipp;
				}
				// error: function() {
					// swal({
					  // title				: "Error Message !",
					  // text				: 'Connection Timed Out ...',
					  // type				: "warning",
					  // timer				: 5000,
					  // showCancelButton	: false,
					  // showConfirmButton	: false,
					  // allowOutsideClick	: false
					// });
				// }
			});
		});
		

		
		$('.js-data-example-ajax').on('select2:select', function (e) {
			var data = e.params.data;
			var id_val = $(this).attr('id');
			$.ajax({
				url			: base_url + active_controller+'/get_update_estimasi',
				type		: "POST",
				data: {
					"id_val" 	: id_val,
					"data" 		: data,
				},
				cache		: false,
				dataType	: 'json',
				success		: function(data){
					if(data.status == 1){
						$('#max_'+data.id_spool).html(data.max_dim);
						$('#length_'+data.id_spool).html(data.max_length);
						$('#class_'+data.id_spool).html(data.class);
						if(data.dim2x == data.dim1x){
							$(".spal_"+data.id_spool).find("td:nth-child(9)").attr('style','background-color:#00ff00;');
						}
						else{
							$(".spal_"+data.id_spool).find("td:nth-child(9)").attr('style','background-color:red;');
						}
						
						
						if(data.max_length2 == data.length){
							$(".spal_"+data.id_spool).find("td:nth-child(10)").attr('style','background-color:#00ff00;');
						}
						else{
							$(".spal_"+data.id_spool).find("td:nth-child(10)").attr('style','background-color:red;');
						}
					}
				},
				error: function() {
					swal({
					  title				: "Error Message !",
					  text				: 'Connection Timed Out ...',
					  type				: "warning",
					  timer				: 5000,
					  showCancelButton	: false,
					  showConfirmButton	: false,
					  allowOutsideClick	: false
					});
				}
			});
		});
		$('.js-data-example-ajax').on('select2:unselecting', function (e) {
			var id_val 	= $(this).attr('id');
			var data 	= $(this).val();
			
			$.ajax({
				url			: base_url + active_controller+'/get_remove_estimasi',
				type		: "POST",
				data: {
					"id_val" : id_val,
					"data" 	: data,
				},
				cache		: false,
				dataType	: 'json',
				success		: function(data){
					if(data.status == 1){
						$('#max_'+data.id_spool).html(0);
						$('#length_'+data.id_spool).html(0);
						$('#'+data.id_spool).val([]).change();
						$(".spal_"+data.id_spool).find("td:nth-child(9)").attr('style','background-color:red;');
						$(".spal_"+data.id_spool).find("td:nth-child(10)").attr('style','background-color:transparant;');
					}
				},
				error: function() {
					swal({
					  title				: "Error Message !",
					  text				: 'Connection Timed Out ...',
					  type				: "warning",
					  timer				: 5000,
					  showCancelButton	: false,
					  showConfirmButton	: false,
					  allowOutsideClick	: false
					});
				}
			});
		});
		$('.js-data-example-ajax_pipe').select2({
			multiple: true,
			maximumSelectionLength: 1,
			ajax: {
				url: base_url + active_controller +'/dropdown_estimasi_pipe/'+no_ipp,
				dataType: 'json',
				type: "POST",
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						nm_product: $(this).data('nm_product'),
						dim1: $(this).data('dim1'),
						dim2: $(this).data('dim2'),
						sr_lr: $(this).data('sr_lr')
					};
				},
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
		
		$('.js-data-example-ajax_non').select2({
			multiple: true,
			maximumSelectionLength: 1,
			ajax: {
				url: base_url + active_controller +'/dropdown_estimasi/'+no_ipp,
				dataType: 'json',
				type: "POST",
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						nm_product: $(this).data('nm_product'),
						dim1: $(this).data('dim1'),
						dim2: $(this).data('dim2'),
						sr_lr: $(this).data('sr_lr')
					};
				},
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

		if ($('#spool').is(':checked')) {
			$('.choseSP').show();
		}
		else{
			$('.choseSP').hide();
		}
		
		$(document).on('click', '#save_schedule', function(e){
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
					var baseurl		= base_url + active_controller +'/save_schedule';
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
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#save_schedule').prop('disabled',false);
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
							$('#save_schedule').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_schedule').prop('disabled',false);
				return false;
			  }
			});
		});
		
		
	});
</script>