
<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			<span style='color:green;'><b>MOHON CEK TERLEBIH DAHULU BERAT SATUAN MATERIAL, <span style='color:red;'>UNTUK MENIMINALISIR KESALAHAN</span></b></span><br>
		</p>
	</div>
	<div class="form-group row">
		<div class='col-sm-3 '>
		   <label class='label-control'>Approve Action <span class="text-red">*</span></label>
		   <select name='status' id='status' class='form-control input-md'>
				<option value='0'>Select Action</option>
				<option value='Y'>APPROVE</option>
				<option value='M'>REVISI TO BQ</option>
				<option value='N'>REVISI ESTIMASI PROJECT</option>
			</select>
			<?php
			echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
			?>
		</div>
		<div class='col-sm-4 '>
			<label class='label-control'>Perubahan</label>          
			<?php
				echo form_textarea(array('id'=>'perubahan','name'=>'perubahan','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Perubahan'));
			?>		
		</div>
		<div class='col-sm-5 '>
			<div id='HideReject'>
				<label class='label-control'>Reject Reason</label>          
				<?php
					echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Revision reason'));
				?>		
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class='col-sm-12'>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 0px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'approvedQ')).' ';
			?>
		</div>
	</div>
	<div class="box box-success">
		<div class="box-header">
			<label>A. PIPA FITTING</label>
		</div>
		<div class="box-body">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='4%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='17%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='15%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Weight Unit</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Weight Total</th>
					<!--<th class="text-center" style='vertical-align:middle;' width='8%'>Cycletime</th>-->
					<th class="text-center" style='vertical-align:middle;' width='8%'>Man Hours</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$Sum = 0;
					$nomor = 0;
					if(!empty($result)){
						foreach($result AS $val => $valx){$nomor++;
							$spaces = "";
							$id_delivery = strtoupper($valx['id_delivery']);
							$bgwarna	= "bg-blue";
								
							$SumQty	= $valx['sum_mat'] * $valx['qty'];
							$Sum += $SumQty;
							if($valx['sts_delivery'] == 'CHILD'){
								$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$id_delivery = strtoupper($valx['sub_delivery']);
								$bgwarna	= "bg-green";
							}
							if($valx['man_hours'] <= 0){
								$bc = 'red';
							}
							if($valx['man_hours'] > 0){
								$bc = 'transparant';
							}
							echo "<tr>";
								echo "<td align='center'>".$nomor."</td>";
								echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
								echo "<td align='left' style='padding-left:20px;'>".spec_bq($valx['id'])."</td>";
								echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
								echo "<td align='left'>".$valx['id_product']."</td>";
								echo "<td align='right'>".number_format($valx['sum_mat'], 3)." kg</span></td>";
								echo "<td align='right'>".number_format($SumQty, 3)." kg</span></td>";
								// echo "<td align='right' style='padding-right:20px; background-color:".$bc."'>".$valx['total_time']."</td>";
								echo "<td align='right' style='padding-right:20px; background-color:".$bc."'>".$valx['man_hours']."</td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='7'>Tidak ada product yang ditampilkan atau kemungkinan product kosong.</td>";
						echo "</tr>";
					}
				?>
				<tr>
					<th class="text-center" colspan='6' style='vertical-align:middle;'>Total</th>
					<th class="text-right"><?= number_format($Sum, 3);?> kg</th>
					<th class="text-center"></th>
				</tr>
			</tbody>
		</table>
		</div>
	</div>

	<div class="box box-success">
		<div class="box-header">
			<label>B. MUR BAUT</label>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>#</th>
						<th class="text-center">Material Name</th>
						<th class="text-center" width='38%'>Material</th>
						<th class="text-center" width='9%'>Qty</th>
						<th class="text-center" width='9%'>Unit</th>
						<th class="text-center" width='19%'>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						if(!empty($detail3)){
							foreach($detail3 AS $val => $valx){ $id++;
								$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
								$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
								echo "<tr class='header3_".$id."'>";
									echo "<td align='center'>".$id."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->material)."</td>";
									echo "<td align='center'>".number_format($valx['qty'])."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
									echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "</tr>";
							}
						}else{
							echo "<tr>";
								echo "<td colspan='6'>Tidak ada data yang ditampilkan.</td>";
							echo "</tr>";
						}
					?>
			</table>
		</div>
	</div>

	<div class="box box-success">
		<div class="box-header">
			<label>C. PLATE</label>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>#</th>
						<th class="text-center">Material Name</th>
						<th class="text-center" width='10%'>Ukuran Standart</th>
						<th class="text-center" width='10%'>Standart</th>
						<th class="text-center" width='9%'>Lebar (mm)</th>
						<th class="text-center" width='9%'>Panjang (mm)</th>
						<th class="text-center" width='9%'>Qty</th>
						<th class="text-center" width='9%'>Berat (kg)</th>
						<th class="text-center" width='9%'>Sheet</th>
						<th class="text-center" width='10%'>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						if(!empty($detail4)){
							foreach($detail4 AS $val => $valx){ $id++;
								$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
								echo "<tr class='header4_".$id."'>";
									echo "<td align='center'>".$id."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->ukuran_standart)."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
									echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
									echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
									echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
									echo "<td align='right'>".number_format($valx['berat'],3)."</td>";
									echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
									echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "</tr>";
							}
						}else{
							echo "<tr>";
								echo "<td colspan='10'>Tidak ada data yang ditampilkan.</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="box box-success">
		<div class="box-header">
			<label>D. GASKET</label>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>#</th>
						<th class="text-center">Material Name</th>
						<th class="text-center" width='15%'>Standart</th>
						<th class="text-center" width='12%'>Dimensi</th>
						<th class="text-center" width='9%'>Lebar (mm)</th>
						<th class="text-center" width='9%'>Panjang (mm)</th>
						<th class="text-center" width='9%'>Qty</th>
						<th class="text-center" width='9%'>Unit</th>
						<th class="text-center" width='9%'>Sheet</th>
						<th class="text-center" width='10%'>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						if(!empty($detail4g)){
							foreach($detail4g AS $val => $valx){ $id++;
								$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
								echo "<tr class='header4g_".$id."'>";
									echo "<td align='center'>".$id."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->dimensi)."</td>";
									echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
									echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
									echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
									echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
									echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "</tr>";
							}
						}else{
							echo "<tr>";
								echo "<td colspan='10'>Tidak ada data yang ditampilkan.</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="box box-success">
		<div class="box-header">
			<label>E. LAINNYA</label>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>#</th>
						<th class="text-center">Material Name</th>
						<th class="text-center" width='20%'>Ukuran Standart</th>
						<th class="text-center" width='18%'>Standart</th>
						<th class="text-center" width='9%'>Qty</th>
						<th class="text-center" width='9%'>Unit</th>
						<th class="text-center" width='19%'>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						if(!empty($detail5)){
							foreach($detail5 AS $val => $valx){ $id++;
								$get_detail = $this->db->select('nama, material, spesifikasi, standart, ukuran_standart, dimensi')->get_where('accessories', array('id'=>$valx['id_material']))->result();
								echo "<tr class='header5_".$id."'>";
									echo "<td align='center'>".$id."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi)."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->ukuran_standart)."</td>";
									echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
									echo "<td align='center'>".number_format($valx['qty'])."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
									echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "</tr>";
							}
						}else{
							echo "<tr>";
								echo "<td colspan='7'>Tidak ada data yang ditampilkan.</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="box box-info">
		<div class="box-header">
			<label>F. MATERIAL</label>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>#</th>
						<th class="text-center">Material Name</th>
						<th class="text-center" width='9%'>Qty</th>
						<th class="text-center" width='9%'>Unit</th>
						<th class="text-center" width='19%'>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						if(!empty($detail2)){
							foreach($detail2 AS $val => $valx){ $id++;
			
								echo "<tr class='header_".$id."'>";
									echo "<td align='center'>".$id."</td>"; 
									echo "<td align='left'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['id_material']))."</td>";
									echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
									echo "<td align='center'>".get_name('raw_pieces','kode_satuan','id_satuan','1')."</td>";
									echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "</tr>";
							}
						}else{
							echo "<tr>";
								echo "<td colspan='5'>Tidak ada data yang ditampilkan.</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});
	
	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N' || $(this).val() == 'M'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
			}
		});
		
		$(document).on('click', '#approvedQ', function(){
			let bF				= $('#id_bq').val();
			let validasi = {
				'status' :$('#status').val(),
				'approve_reason' : $('#approve_reason').val(),
				'perubahan' : $('#perubahan').val()
			}
			if(validasi.status == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Action approve belum dipilih ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			if(validasi.status == 'N' && validasi.approve_reason == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Alasan reject masih kosong ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			// if(validasi.perubahan == ''){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Perubahan masih kosong ...',
				  // type	: "warning"
				// });
				// $('#approvedQ').prop('disabled',false);
				// return false;
			// }
			
			swal({
			  title: "Apakah anda yakin ???",
			  text: "Approve strukture BQ",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses !",
			  cancelButtonText: "Tidak, Batalkan !",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url+active_controller+'/AppBQEstNew/'+bF,
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
								window.location.href = base_url + active_controller+'/approve_est';
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