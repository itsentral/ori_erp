<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);


?>
<form action="#" method="POST" id="form_proses_bro" autocomplete='off'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'>IPP Number</label>
				<div class='col-sm-4'><?= $getHeader[0]->no_ipp;?></div>
				<label class='label-control col-sm-2'>Project Name</label>
				<div class='col-sm-4'><?= strtoupper($getHeader[0]->project);?></div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Customer Name</label>
				<div class='col-sm-4'><?= strtoupper($getHeader[0]->nm_customer);?></div>
				<label class='label-control col-sm-2'>Series</label>
				<div class='col-sm-4'><?= $series;?></div>
				
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Kurs</label>
				<div class='col-sm-4'>
					<input type='hidden' class='form-control input-md' name='job_number' placeholder='JOB Number' id='job_number' value='<?= (!empty($getHeader[0]->job_number))?strtoupper($getHeader[0]->job_number):'';?>'>
					<input type='text' class='form-control input-md maskMoney' name='kurs' placeholder='Kurs' id='kurs' value='<?= (!empty($getHeader[0]->kurs))?number_format($getHeader[0]->kurs):'';?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
					</div>
				<label class='label-control col-sm-2'>Quotation Number <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='quo_number' placeholder='Quotation Number' id='quo_number' value='<?= (!empty($getHeader[0]->quo_number))?strtoupper($getHeader[0]->quo_number):'';?>'></div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Subject <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='subject' placeholder='Subject' id='subject' value='<?= (!empty($getHeader[0]->subject))?strtoupper($getHeader[0]->subject):'';?>'></div>
				<label class='label-control col-sm-2'>Jangka Waktu Penawaran <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='jangka_waktu_penawaran' placeholder='Jangka Waktu Penawaran' id='jangka_waktu_penawaran' value='<?= (!empty($getHeader[0]->jangka_waktu_penawaran))?strtoupper($getHeader[0]->jangka_waktu_penawaran):'';?>'></div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Product <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='product' placeholder='Product' id='product' value='<?= (!empty($getHeader[0]->product))?strtoupper($getHeader[0]->product):'';?>'></div>
				<label class='label-control col-sm-2'>Garansi Product <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='garansi_porduct' placeholder='Garansi Product' id='garansi_porduct' value='<?= (!empty($getHeader[0]->garansi_porduct))?strtoupper($getHeader[0]->garansi_porduct):'';?>'></div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Jenis Pengiriman <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='pengiriman' placeholder='Jenis Pengiriman' id='pengiriman' value='<?= (!empty($getHeader[0]->pengiriman))?strtoupper($getHeader[0]->pengiriman):'';?>'></div>
				<label class='label-control col-sm-2'>Tahapan Pembayaran <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='tahap_pembayaran' placeholder='Tahapan Pembayaran' id='tahap_pembayaran' value='<?= (!empty($getHeader[0]->tahap_pembayaran))?strtoupper($getHeader[0]->tahap_pembayaran):'';?>'></div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Attn <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='attn' placeholder='Attn' id='attn' value='<?= (!empty($getHeader[0]->attn))?strtoupper($getHeader[0]->attn):'';?>'></div>
				<label class='label-control col-sm-2'>Waktu Pengiriman <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='waktu_pengiriman' placeholder='Waktu Pengiriman' id='waktu_pengiriman' value='<?= (!empty($getHeader[0]->waktu_pengiriman))?strtoupper($getHeader[0]->waktu_pengiriman):'';?>'></div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Sales <span class="text-red">*</span></label>
				<div class='col-sm-4'><input type='text' class='form-control input-md' name='sales' placeholder='Sales' id='sales' value='<?= (!empty($getHeader[0]->sales))?strtoupper($getHeader[0]->sales):'';?>'></div>
			</div>
		</div>
		<!-- INPUTAN -->
		<input type='text' class='THide' name='id_bq' id='id_bq' value='BQ-<?= $getHeader[0]->no_ipp;?>'>
		<input type='text' class='THide' name='no_ipp' id='no_ipp' value='<?= $getHeader[0]->no_ipp;?>'>
		<input type='text' class='THide' name='project' id='project' value='<?= $getHeader[0]->project;?>'>
		<input type='text' class='THide' name='customer' id='customer' value='<?= $getHeader[0]->nm_customer;?>'>
		<input type='text' class='THide' name='series' id='series' value='<?= $dtImplode;?>'>
		<div class="box-body" style="">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tr>
					<th class="text-center" colspan='2' width='16%'></th>
					<th class="text-center" width='6%'></th>
					<th class="text-center" width='6%'></th>
					<th class="text-center" width='6%'></th>
					<th class="text-center" width='8%'></th>
					<th class="text-center" width='15%'></th>
					<th class="text-center" width='10%'></th>
					<th class="text-center" width='8%'></th>
					<th class="text-center" width='7%'></th>
					<th class="text-center" width='9%'></th>
					<th class="text-center" width='11%'></th>
				</tr>
				<?php
				$SUM = 0;
				if(!empty($getDetail)){ ?>
					<tbody>
						<tr>
							<td class="text-left headX" colspan='12'><b>PRODUCT</b></td>
						</tr>
						<tr class='bg-bluexyz'>
							<th class="text-center" colspan='2' width='16%'>Item Product</th>
							<th class="text-center" width='6%'>Dim 1</th>
							<th class="text-center" width='6%'>Dim 2</th>
							<th class="text-center" width='6%'>Liner</th>
							<th class="text-center" width='8%'>Pressure</th>
							<th class="text-center" width='15%'>Specification</th>
							<th class="text-center" width='10%'>Qty</th>
							<th class="text-center" width='8%'>Unit</th>
							<th class="text-center" width='7%'>Weight (Kg)</th>
							<th class="text-center" width='9%'>Unit Price</th>
							<th class="text-center" width='11%'>Total Price (USD)</th>
						</tr>
					</tbody>
					<tbody>
						<?php
						$SUM = 0;
						$no = 0;
						foreach($getDetail AS $val => $valx){
							$no++;
							$dataSum = 0;
							if($valx['qty'] <> 0){
								$dataSum	= $valx['cost'];
							}
							$SUM += $dataSum;
							
							if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
								$unitT = "Btg";
							}
							else{
								$unitT = "Pcs";
							}
							echo "<tr>";
								echo "<td colspan='2'>".strtoupper($valx['id_category'])."</td>";
								echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
								echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
								echo "<td align='center'>".substr($valx['series'],6,5)."</td>";
								echo "<td align='center'>".substr($valx['series'],3,2)."</td>";
								echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
								echo "<td align='center'>".$valx['qty']."</td>";
								echo "<td align='center'>".$unitT."</td>";
								echo "<td align='right'>".number_format($valx['est_material'],2)." Kg</td>";
								echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
								echo "<td align='right'>".number_format($dataSum,2)."</td>";
							echo "</tr>";
						}
						?>
						<tr class='FootColor'>
							<td colspan='11'><b>TOTAL OF PRODUCT</b></td>
							<td align='right'><b><?= number_format($SUM,2);?></b></td>
						</tr>
					</tbody>
				<?php
				}
				$SUM_NONFRP = 0;
				if(!empty($non_frp)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='12'><b>BQ NON FRP</b></td>";
						echo "</tr>";
						echo "<tr class='bg-bluexyz'>";
							echo "<th class='text-center' colspan='8'>Material Name</th>";
							echo "<th class='text-center'>Qty</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($non_frp AS $val => $valx){
						$SUM_NONFRP += $valx['price_total'];
						
						$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
						$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
						$nama_acc = "";
						if($valx['category'] == 'baut'){
							$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
						}
						if($valx['category'] == 'plate'){
							$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
						}
						if($valx['category'] == 'gasket'){
							$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
						}
						if($valx['category'] == 'lainnya'){
							$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
						}
							
						$qty = $valx['qty'];
						$satuan = $valx['option_type'];
						if($valx['category'] == 'plate'){
							$qty = $valx['weight'];
							$satuan = '1';
						}
						echo "<tr>";
							echo "<td colspan='8'>".$nama_acc."</td>";
							echo "<td align='right'>".number_format($qty,2)."</td>";
							echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
							echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='11'><b>TOTAL BQ NON FRP</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				$SUM_MAT = 0;
				if(!empty($material)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='12'><b>MATERIAL</b></td>";
						echo "</tr>";
						echo "<tr class='bg-bluexyz'>";
							echo "<th class='text-center' colspan='8'>Material Name</th>";
							echo "<th class='text-center'>Weight</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($material AS $val => $valx){
						$SUM_MAT += $valx['price_total'];
						echo "<tr>";
							echo "<td colspan='8'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
							echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
							echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
							echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='11'><b>TOTAL MATERIAL</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				?>
			</table>
		</div>
		<div class='box-footer' style='float:right;'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','id'=>'saved_data','value'=>'simpan','content'=>'Simpan'));
			?>
			<a href="<?php echo site_url('sales/quotation/') ?>" class="btn btn-md btn-danger">Back</a> 
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<style type="text/css">
	.headX{
		background-color: #05b3a3 !important;
		color : white;
	}
	.bg-bluexyz{
		background-color: #05b3a3 !important;
		color : white;
	}
	.FootColor{
		background-color: #91cbd4 !important;
	}
</style>

<script>
	$(document).ready(function(){
		$(".THide").hide();
		// $(".unitEngCost").html('-');
		$(".unitExport").html();
		$(".engCostCls").attr('readonly', true);
		$(".ExQty").attr('readonly', true);
		$(".ExQtyUnit").attr('readonly', true);
		$(".EngCostPrice").attr('readonly', true);
		
		var nox	= $('#nox').val();
		var Totalx	= 0;
		var a;
		for(a=1; a <= nox; a++){
			Totalx += getNum($('#hargaTot_'+a).val());
		}
		$('#total_material').val(Totalx.toFixed(2));
		$('#total_materialx').html(Totalx.toFixed(2));
		
		$(document).on('keyup', '.persenNego', function(){
			
			$datac = $(this).val();
			
			if($datac > 10){
				$(this).val(10);
				$datac = 10;
			}
			
			var nomor 	= $(this).data('nomor');
			var qty 	= getNum($('#qty_'+nomor).val());
			var harga	= getNum($('#harga_'+nomor).val()); 
			var persen	= getNum($datac) / 100;
			var TotalT	= (harga +(harga * persen)) * qty;
			
			$('#hargaTot_'+nomor).val(TotalT.toFixed(2));
			$('#hargaTotL_'+nomor).html(TotalT.toFixed(2));
			
			var nox	= $('#nox').val();
			var Totalx	= 0;
			var a;
			for(a=1; a <= nox; a++){
				Totalx += getNum($('#hargaTot_'+a).val());
			}
			$('#total_material').val(Totalx.toFixed(2));
			$('#total_materialx').html(Totalx.toFixed(2));
		});
		
		
		$(document).on('click', '#saved_data', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			
			var quo_number 		= $('#quo_number').val();
			var subject 		= $('#subject').val();
			var jangka_waktu_penawaran = $('#jangka_waktu_penawaran').val();
			var product 		= $('#product').val();
			var garansi_porduct = $('#garansi_porduct').val();
			var pengiriman 		= $('#pengiriman').val();
			var tahap_pembayaran 	= $('#tahap_pembayaran').val();
			var attn 				= $('#attn').val();
			var waktu_pengiriman 	= $('#waktu_pengiriman').val();
			var sales 				= $('#sales').val();
			
			if(quo_number==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Quotation Number is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(subject==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Subject is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(jangka_waktu_penawaran==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Jangka Waktu Penawaran is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(product==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Product is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(garansi_porduct==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Garansi Product is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(pengiriman==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Jenis Pengiriman is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(tahap_pembayaran==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Tahapan Pembayaran is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(attn==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Attn is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(waktu_pengiriman==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Waktu pengiriman is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
				return false;
			}
			
			if(sales==''){
				swal({
				  title	: "Error Message!",
				  text	: 'Sales name is empty ...',
				  type	: "warning"
				});
				$('#saved_data').prop('disabled',false);
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
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/save_edit_penawaran_sales';
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
									window.location.href = base_url +"sales/quotation/";
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#saved_data').prop('disabled',false);
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
								$('#saved_data').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#saved_data').prop('disabled',false);
					return false;
				  }
			});
		});
		
	
	});
	
	
	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}
	
</script>
