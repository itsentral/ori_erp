
<div class="box-body"> 
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			<button type='button' class='btn btn-sm btn-info'><i class='fa fa-check'></i></button>&nbsp;<button type='button' class='btn btn-sm btn-info'>Save</button> Digunakan untuk menarik estimasi dari <b>Master Product</b><br>
			<button type='button' class='btn btn-sm btn-info' style='background-color: #de14a0; border-color: #de14a0; color:white; margin-top:5px;'><i class='fa fa-check'></i></button>&nbsp;<button type='button' class='btn btn-sm btn-info' style='background-color: #de14a0; border-color: #de14a0; color:white; margin-top:5px;'>Save</button> Digunakan untuk menarik estimasi dari <b>Estimasi Terakhir</b><br>
			Melakukan check list, kemudian klik tombol Save <b>Sesuai Kebutuhan</b>
		</p>
	</div>
	<input type='hidden' name='id_bq' value='<?= $id_bq;?>'> 
	<input type='hidden' name='pembeda' id='pembeda' value='<?= $this->uri->segment(4);?>'>
	<input type='hidden' name='no_ipp' value='<?= $qBQdetailRest[0]['no_ipp'];?>'>  
	<!--
	<span style='color:green;'><b>* Tombol Edit berwarna <span style='color:red;'>Merah</span> dalam process Development, <span style='color:red;'><u>MOHON JANGAN DIGUNAKAN.</u></span> Kolom #</b></span>
	<br>
	<br><br>
	-->
	
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th style='background:none;' width='4%' class='no-sort'><font size='2'><B><center><input type='checkbox' name='chk_all' id='chk_all'></center></B></font></th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Iso Matric</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>No Unit Delivery</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>No Component</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Series</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Spec</th>
				<!--<th class="text-center" style='vertical-align:middle;' width='2%'>Upload</th>-->
				<th class="text-center" style='vertical-align:middle;' width='26%'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Option</th>
				
			</tr>
		</thead>
		<tbody id='detail_body'>
			<?php
				$no=0;
				foreach($qBQdetailRest AS $val => $valx){
					$no++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					$nm_cty	= ucwords(strtolower($valx['id_category'])); 
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					
					$plusSQL = " AND a.diameter = '".$valx['diameter_1']."'";
					if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
						$plusSQL = " AND a.diameter = '".$valx['diameter_1']."'  AND a.diameter2='".$valx['diameter_2']."'";
					}
					if($valx['id_category'] == 'figure 8'){
						$plusSQL = " AND a.diameter2='".$valx['diameter_2']."'";
					}
					
					$plusSQL2 = "";
					if($valx['id_category'] == 'elbow mould' OR $valx['id_category'] == 'elbow mitter'){
						$plusSQL2 = " AND a.diameter = '".$valx['diameter_1']."'  AND a.angle='".$valx['sudut']."' AND a.type_elbow='".$valx['type']."' ";
					}

					$series = $valx['series'];
					// echo $series."<br>";
					$sqlProduct	= "SELECT a.id_product, a.series, a.cust FROM component_header a INNER JOIN bq_detail_header b ON a.series = b.series  WHERE b.id_bq = '".$id_bq."' ".$plusSQL." ".$plusSQL2." AND a.series = '".$valx['series']."' AND a.parent_product='".$valx['id_category']."' GROUP BY a.id_product";
					$restProduct = $this->db->query($sqlProduct)->result_array();
					$restNum = $this->db->query($sqlProduct)->num_rows();
							
					// echo $sqlProduct."<br>";
					echo "<tr id='tr_".$no."'>";
						echo "<td align='right' style='vertical-align:middle;'><center><input type='checkbox' name='check[$no]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."-".$valx['id_milik_bq']."'></center></td>";
						echo "<td align='center'>".$spaces."".$valx['id_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['sub_delivery']."</td>";
						echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
						echo "<td align='center'>".$spaces."".$valx['series']."</td>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left' style='padding-left:20px;'>".spec_fd($valx['id'],'so_detail_header')."</td>";
						// echo "<td align='left' style='padding-left:20px;'></td>";
						echo "<td style='vertical-align:middle;' align='center'>"; 
							
							echo "<input type='hidden' name='detailBQ[".$no."][id]' value='".$valx['id']."'>";
							echo "<input type='hidden' name='detailBQ[".$no."][panjang]' value='".floatval($valx['length'])."'>";
							echo "<select name='detailBQ[".$no."][id_productx]' id='id_product_".$no."' class='chosen-select form-control inline-block'>";
								echo "<option value=''>Select ".$nm_cty."</option>";
								// if($restNum == 0){echo "<option value='0'>List Empty</option>";}
								foreach($restProduct AS $valP => $valPX){ 
									$idProduct = $valPX['cust']; 
									$sqtToCust = $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$idProduct."'")->result_array();
									$Customer	= (!empty($idProduct))?' ('.$sqtToCust[0]['nm_customer'].')':'';
					
									$selectedX	= ($valx['id_product'] == $valPX['id_product'])?'selected':'';
									echo "<option value='".$valPX['id_product']."' ".$selectedX.">".$valPX['id_product'].$Customer."</option>";
								}
							echo "</select>";
						echo "</td>";
						
						echo "<td align='left'>";
							if(!empty($valx['id_product'])){
								echo "<button type='button' class='btn btn-sm btn-primary detail_comp' title='Lihat Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
							}
							if($valx['id_category'] == 'pipe' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_pipe' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'end cap' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_end_cap' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'blind flange' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_blindflange' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							} 
							if($valx['id_category'] == 'elbow mould' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_elbowmould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'elbow mitter' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_elbowmitter' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'eccentric reducer' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_eccentric_reducer' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'concentric reducer' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_concentric_reducer' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'equal tee mould' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_equal_tee_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'reducer tee mould' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_reducer_tee_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'equal tee slongsong' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_equal_tee_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'reducer tee slongsong' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_reducer_tee_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'flange mould' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_flange_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'flange slongsong' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_flange_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'colar' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_colar' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							if($valx['id_category'] == 'colar slongsong' AND !empty($valx['id_product'])){
								echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_colar_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							}
							// if($valx['id_category'] == 'field joint' AND !empty($valx['id_product'])){
								// echo "&nbsp;<button type='button' class='btn btn-sm btn-danger' id='edit_field_joint' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
							// } 
							echo "&nbsp;<button type='button' class='btn btn-sm btn-info updateComp' title='Update Component' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-panjang=".floatval($valx['length'])."><i class='fa fa-check'></i></button>";
							if($valx['id_milik_bq'] != NULL){
								// echo "&nbsp;<button type='button' class='btn btn-sm btn-danger get_est_bq' title='Estimasi dari BQ' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-id_milik_bq='".$valx['id_milik']."'><i class='fa fa-close'></i></button>";
								echo "&nbsp;<button type='button' class='btn btn-sm btn-danger get_est_bq' style='background-color: #de14a0; border-color: #de14a0; color:white;' title='Estimasi dari BQ' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-panjang=".floatval($valx['length'])." data-id_milik_bq='".$valx['id_milik_bq']."'><i class='fa fa-check'></i></button>";
							
							} 
						echo "</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
	<br>
	<?php
		// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'estNow')).' ';
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','style'=>'min-width:100px; margin-right:10px; float:right; background-color: #de14a0; border-color: #de14a0; color:white;','value'=>"save",'content'=>"Save",'id'=>'estNowNewBQ')).' ';
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save",'id'=>'estNowNew')).' ';
	?>
</div>
<style type="text/css">
	.modal-dialog{
		overflow: auto !important;
	}
	
	label{
		    font-size: small !important;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
	<!--
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
	-->
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$(".chosen-select").chosen();
		
		$("#chk_all").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
		
		//SAVE NEW ADA DEFAULTNYA
		$(document).on('click', '#estNowNew', function(){
			
			if($('input[type=checkbox]:checked').length == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Checklist Minimal One Component',
				  type	: "warning"
				});
				$('#estNowNew').prop('disabled',false);
				return false;
			}
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			var data_url = $('#pembeda').val();
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
						url			: base_url+'index.php/'+active_controller+'/updateBQNew',
						type		: "POST",
						data		: formData,
						// data:{
								// 'id_milikx' : id_milik
							// },
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
								// window.location.href = base_url + active_controller+'/'+data_url;
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bqx+'/'+data.pembeda);
								$("#ModalView").modal();
								
								
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

		//SAVE NEW BQ
		$(document).on('click', '#estNowNewBQ', function(){
			
			if($('input[type=checkbox]:checked').length == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Checklist Minimal One Component',
				  type	: "warning"
				});
				$('#estNowNewBQ').prop('disabled',false);
				return false;
			}
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			var data_url = $('#pembeda').val();
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
						url			: base_url+'index.php/'+active_controller+'/updateBQNewBQ',
						type		: "POST",
						data		: formData,
						// data:{
								// 'id_milikx' : id_milik
							// },
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
								// window.location.href = base_url + active_controller+'/'+data_url;
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bqx+'/'+data.pembeda);
								$("#ModalView").modal();
								
								
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