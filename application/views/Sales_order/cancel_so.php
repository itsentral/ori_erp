<?php
$this->load->view('include/side_menu');
?>

<div class="box box-primary">
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'>IPP Number</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= $getHeader[0]->no_ipp;?><input type='hidden' id='no_ipp' name='no_ipp' value='<?= $getHeader[0]->no_ipp;?>' ></div>
				<label class='label-control col-sm-2'>Customer Name</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= strtoupper($getHeader[0]->nm_customer);?></div>
			</div>
		</div>
	<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%' class='no-sort'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty Total</th>
				<th class="text-center" style='vertical-align:middle;'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$SUM = 0;
				$SumX = 0;
				$no = 0;
				if(!empty($qBQdetailRest)){
					foreach($qBQdetailRest AS $val => $valx){ $no++;
						$SUM += $valx['cost'];
						$cost = 0;
						echo "<tr>";
							echo "<td align='right' class='so_style_list'><center>".$no."<input type='hidden' id='est_harga_".$no."' value='".$cost."'></center></td>";
							echo "<td align='left' class='so_style_list'>".strtoupper($valx['id_category'])."</td>";
							echo "<td align='left' class='so_style_list'>".spec_bq($valx['id_milik'])."</td>";
							echo "<td align='center' class='so_style_list'>".$valx['qty']."</td>";
							echo "<td align='left' class='so_style_list'>".$valx['id_product']."</span></td>";
							if(($valx['so_sts']) == 'Y'){
								$BTN_HAPUS = "<button type='button' data-id='".$valx['id']."' data-id_bq_header='".$valx['id_bq_header']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so' title='Delete From SO'><i class='fa fa-trash'></i></button>";
							}
							else{
								$BTN_HAPUS = "";
							}
							echo "<td align='center' class='so_style_list'>$BTN_HAPUS</td>";
						echo "</tr>";
					}
				}
			?>
			<?php
				//material
				$SUM_MAT = 0;
				if(!empty($rest_material)){
					foreach($rest_material AS $val => $valx){ $no++;
						$SUM_MAT += $valx['price_total'];
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$valx['price_total']/$valx['qty']."'></td>";
							echo "<td colspan='2'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
							echo "<td align='right'>".number_format($valx['qty'],2)."
									<input type='hidden' name='UpQtySoMat[".$no."][id]' value='".$valx['id']."'>
							</td>";
							echo "<td align='left'>KG</td>";
							echo "<td align='center'><button type='button' data-id='".$valx['id']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so_mat' title='Delete From SO'><i class='fa fa-trash'></i></button></td>";
						echo "</tr>";
					}
				}
			?>			
			<?php
				//material
				$SUM_MAT_NON = 0;
				if(!empty($rest_acc)){
					foreach($rest_acc AS $val => $valx){ $no++;
						$SUM_MAT_NON += $valx['price_total'];
						
						$qty = $valx['qty'];
						$satuan = $valx['satuan'];
						if($valx['category'] == 'plate'){
							$qty = $valx['berat'];
							$satuan = '1';
						}
						
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$valx['price_total']."'></td>";
							echo "<td colspan='2'>".get_name_acc($valx['id_material'])."</td>";
							echo "<td align='right'>".number_format($valx['qty'],2)."
									<input type='hidden' name='UpQtySoMat[".$no."][id]' value='".$valx['id']."'></td>";
							echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
							echo "<td align='center'><button type='button' data-id='".$valx['id']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so_mat' title='Delete From SO / Back To Quotation' data-role='qtip'><i class='fa fa-trash'></i></button></td>";
						echo "</tr>";
					}
				}
			?>
			
			<?php
				//engine
				if(!empty($data_eng)){
					foreach($data_eng AS $val => $valx){ $no++;
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."</td>";
							echo "<td colspan=2>".strtoupper($valx['category'].' - '.$valx['caregory_sub'])."</td>";
							echo "<td align='right'>".number_format($valx['qty'])."
									<input type='hidden' name='EngPackTrans[".$no."][id]' value='".$valx['id']."'>
							</td>";
							echo "<td align='left'>UNIT</td><td align='center'>";
							if($valx['sts_so'] == 'Y'){
								echo "<button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-danger del_so_eng_pack_trans' title='Delete From SO'><i class='fa fa-trash'></i></button>";
							}
						echo "</td></tr>";
					}
				}
				//packing
				if(!empty($data_pack)){
					foreach($data_pack AS $val => $valx){ $no++;
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."</td>";
							echo "<td colspan=2>".strtoupper($valx['category'].' - '.$valx['caregory_sub'].' / '.$valx['option_type'])."</td>";
							echo "<td align='right'>".number_format(1)."
									<input type='hidden' name='EngPackTrans[".$no."][id]' value='".$valx['id']."'>
							</td>";
							echo "<td align='left'></td><td align='center'>";
							if($valx['sts_so'] == 'Y'){
								echo "<button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-danger del_so_eng_pack_trans' title='Delete From SO' data-role='qtip'><i class='fa fa-trash'></i></button>";
							}
						echo "</td></tr>";
					}
				}

				//transport
				if(!empty($data_ship)){
					foreach($data_ship AS $val => $valx){ $no++;
						$Add = "";
						if($valx['category'] == 'lokal' AND $valx['caregory_sub'] == 'VIA DARAT'){
							$Add = strtoupper("".get_name('truck','nama_truck','id',$valx['kendaraan']).", DEST. ".$valx['area']." - ".$valx['tujuan']);
						}
						if($valx['caregory_sub'] != 'VIA DARAT'){
							$Add = strtoupper($valx['kendaraan']);
						}
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."</td>";
							echo "<td colspan=2>".strtoupper($valx['category'].' - '.$valx['caregory_sub'])."</td>";
							echo "<td align='right'>".number_format($valx['qty'])."
									<input type='hidden' name='EngPackTrans[".$no."][id]' value='".$valx['id']."'>
							</td>";
							echo "<td align='left'>".$Add."</td><td align='center'>";
							if($valx['sts_so'] == 'Y'){
								echo "<button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-danger del_so_eng_pack_trans' title='Delete From SO'><i class='fa fa-trash'></i></button>";
							}
							echo "</td></tr>";
					}
				}
			?>
		</tbody>
	</table><br />
	<a href="<?=base_url("sales_order")?>" class="btn btn-md btn-default">Back</a>
	</div>
</div>
<style>
	.so_style_list{
		vertical-align:middle;
		padding-left:20px;
	}
</style>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).on('click', '.del_so', function(){
		var id	= $(this).data('id');
		var id_milik	= $(this).data('id_milik');
		var id_bq = $('#no_ipp').val();
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Data akan terhapus secara Permanen !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_so/'+id+'/'+id_milik+'/'+id_bq,
					type		: "POST",
					data		: "id="+id,
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
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								window.location.href = base_url + active_controller+'/cancel_so/'+id_bq;
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000,
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '.del_so_mat', function(){
		var id	= $(this).data('id');
		var id_milik	= $(this).data('id_milik');
		var id_bq = $('#no_ipp').val();
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Data akan terhapus secara Permanen !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_so_mat/'+id+'/'+id_milik+'/'+id_bq,
					type		: "POST",
					data		: "id="+id,
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
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								window.location.href = base_url + active_controller+'/cancel_so/'+id_bq;
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000,
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '.del_so_eng_pack_trans', function(){
		var id	= $(this).data('id');
		var id_bq = $('#no_ipp').val();
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Data akan terhapus secara Permanen !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_so_eng_pack_trans',
					type		: "POST",
					data		: {
						'id' : id,
						'id_bq' : id_bq
					},
					cache		: false,
					dataType	: 'json',				
					success		: function(data){								
						if(data.status == 1){											
							swal({
								  title	: "Save Success!",
								  text	: data.pesan,
								  type	: "success",
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								window.location.href = base_url + active_controller+'/cancel_so/'+id_bq;
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000,
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});

	
</script>