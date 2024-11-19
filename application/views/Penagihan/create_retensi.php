<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?=$title;?></h3>
			<div class="box-tool pull-right">
				
			</div>
		</div>
		<br>
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>IPP Number</b></label>
				<div class='col-sm-4'>
					<input type='text' name='no_ipp' id='no_ipp' class='form-control input-md' readonly value='<?=$in_ipp;?>'>
					<input type='hidden' name='id' id='id' class='form-control input-md' readonly value='<?=$id;?>'>
				</div>
				<label class='label-control col-sm-2'><b>SO Number</b></label>
				<div class='col-sm-4'>
					<input type='text' name='no_so' id='no_so' class='form-control input-md' readonly value='<?=$in_so;?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Invoice Type</b></label>
				<div class='col-sm-4'>
					<input type='text' name='type' id='type' class='form-control input-md' readonly value='RETENSI'>
				</div>
				<label class='label-control col-sm-2'><b>Invoice Date</b></label>
				<div class='col-sm-4'>
					<input type='text' name='tgl_inv' id='tgl_inv' class='form-control input-md datepicker' readonly value='<?=date('Y-m-d')?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Customer Name</b></label>
				<div class='col-sm-4'>
					<input type='text' name='nm_customer' id='nm_customer' class='form-control input-md' readonly value='<?=$penagihan[0]->customer;?>'>
					<input type='hidden' name='id_customer' id='id_customer' class='form-control input-md' readonly value='<?=$penagihan[0]->kode_customer;?>'>
				</div>
				<label class='label-control col-sm-2'><b>Customer Address</b></label>
				<div class='col-sm-4'>
					<textarea name='cust_address' id='cust_address' class='form-control input-md' rows='3' readonly><?= get_name('customer','alamat','id_customer',$penagihan[0]->kode_customer);?></textarea>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>PO Number <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_po' id='nomor_po' class='form-control input-md' value='<?=$penagihan[0]->no_po;?>'>
				</div>
				<label class='label-control col-sm-2'><b>F. No Faktur <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_faktur' id='nomor_faktur' class='form-control input-md'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No Pajak <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_pajak' id='nomor_pajak' class='form-control input-md'>
				</div>
				<label class='label-control col-sm-2'><b>Kurs <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='kurs' id='kurs' class='form-control input-md maskMoney' value='<?=number_format($kurs);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
					<input type="hidden" id='wilayah' name="wilayah" class="form-control input-sm" value="<?= get_name('so_number','wilayah','id_bq', "BQ-".$getHeader[0]->no_ipp);?>">
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>PPN<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select id="ppnselect" name="ppnselect" class="form-control input-sm chosen_select" required>
						<option value="0">SELECT AN PPN</option>
						<option value="1">PPN</option>
						<option value="0">NON PPN</option>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>TOP <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select id="top" name="top" class="form-control input-sm chosen_select" required>
						<option value="0">SELECT AN TOP</option>
						<?php
						foreach($list_top AS $val => $valx){
							echo "<option value='".$valx['data1']."'>".strtoupper($valx['name'])."</option>";
						}
						?>
					</select>
				</div>
			</div>
			
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<?php
				$SUM=0;
				if(!empty($get_retensi)){
					?>
					<thead>
						<tr class='bg-blue'>
							<td class="text-left headX HeaderHr" colspan='3'><b>RETENSI</b></td>
						</tr>
						<tr class='bg-blue'>
							<th class="text-center" width = '2%'>#</th>
							<th class="text-center">Category</th>
							<th class="text-center" width = '8%'>Total Price</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$SUM=0;
						$nomor=0;
						foreach($get_retensi AS $val => $valx){
							$nomor++;
							$harga_tot6			= number_format($valx['value_usd'],2);
							$harga_tot6_hidden	= round($valx['value_usd'],2);
							$material_name = get_nomor_so($valx['no_po']).' / RETENSI';
							?>
							<tr id='tr5_<?= $nomor;?>' >
								<td align='center'><span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow6(<?= $nomor;?>)'><i class="fa fa-times"></i></a></span></td>
								<td>
									<input type="text" class="form-control" id="material_name6" name="data6[<?=$nomor ?>][material_name6]" value="<?=set_value('material_name6', isset($material_name) ? $material_name : ''); ?>" readonly >
								</td>
								<td>
									<input type="hidden" class="form-control" id="unit6" name="data6[<?=$nomor ?>][unit6]" value="" readonly >
									<input type="text" class="form-control text-right harga_tot6 changeAll maskMoney" id="harga_tot6<?=$nomor ?>" data-nomor='<?=$nomor ?>' readonly name="data6[<?=$nomor ?>][harga_tot6]" value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" >
									<input type="hidden" class="form-control amount6 changeAll" id="harga_tot6_hidden<?=$nomor ?>" name="data6[<?=$nomor ?>][harga_tot6_hidden]" value="<?=set_value('harga_tot6_hidden', isset($harga_tot6_hidden) ? $harga_tot6_hidden : ''); ?>" readonly >
									<input type="hidden" class="form-control changeShip" data-id='<?=$nomor ?>' value="<?=set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>">
								</td>
							</tr>
							<?php
						}
						?>
						<tr id='tr5X' class='FootColor'>
							<td></td>
							<td><b>TOTAL RETENSI</b></td>
							<td align="right">
								<?php
								$total_trucking= number_format($SUM,2);
								$total_trucking_hidden= round($SUM,2);
								?>
								<input type="text" class="form-control text-right result6" id="total_trucking<?=$nomor ?>" name="total_trucking" value="<?=set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" readonly >
								<input type="hidden" class="form-control result6_hidden" id="total_trucking_hidden<?=$nomor ?>" name="total_trucking_hidden" value="<?=set_value('total_trucking_hidden', isset($total_trucking_hidden) ? $total_trucking_hidden : ''); ?>" readonly >
								<input type="hidden" class="form-control changeShipTot" value="<?=set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" readonly >
							</td>
						</tr>
					</tbody>
				<?php } ?>	
				<tfoot>
					<tr class='HeaderHr'>
						<td align='right' colspan='3' height='20px;'></td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>TOTAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right' style='text-align:center;'>
							<?php 
								$grand_total 		= number_format($SUM, 2);
								$grand_total_hidden = round($SUM, 2);
							?>
							<input type="text" class="form-control grand_total text-right" id="grand_total" name="grand_total" value="<?php echo set_value('grand_total', isset($grand_total) ? $grand_total : ''); ?>" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control grand_total_hidden" id="grand_total_hidden" name="grand_total_hidden" value="<?php echo set_value('grand_total_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>DISKON</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<input type="text" class="form-control diskon text-right autoNumeric" id="diskon" name="diskon" placeholder="Diskon">
							<input type="hidden" class="form-control diskon_hidden" id="diskon_hidden" name="diskon_hidden" value="0" placeholder="Automatic" readonly>
						</td>
					</tr>
					
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>PPN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<input type="text" class="form-control ppn text-right" id="ppn" name="ppn" value="0" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control ppn_hidden" id="ppn_hidden" name="ppn_hidden" value="0" placeholder="Automatic" readonly >
						</td>
					</tr>
					<tr class='HeaderHr'>
						<td align='right' colspan='2'><b>TOTAL INVOICE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<?php 
							$grand_total = number_format($SUM, 2);
							$grand_total_hidden = round($SUM, 2);
							?>
							<input type="text" class="form-control total_invoice text-right" id="total_invoice" name="total_invoice" value="<?php echo set_value('total_invoice', isset($grand_total) ? $grand_total : ''); ?>" placeholder="Automatic" readonly >
							<input type="hidden" class="form-control total_invoice_hidden" id="total_invoice_hidden" name="total_invoice_hidden" value="<?php echo set_value('total_invoice_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >
						</td>
					</tr>
				</tfoot>
			</table>
			
			<br>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin: 0px 0px 5px 5px;','value'=>'Back','content'=>'Back','id'=>'back')).' ';
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin: 0px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'proses_inv')).' ';
			?>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<script src="<?=base_url('application/views/Component/general.js'); ?>"></script>
<style>
	.datepicker{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth:true,
			changeYear:true
		});
		
		$(document).on('keyup','#kurs', function(){
			let kurs = $(this).val();
			
			if(kurs == '0' || kurs == ''){
				$(this).val('1');
			}
		});
		
		sumRetensi();
		sumInvoice();
		
		$(document).on('keyup','#diskon', function(){
			sumInvoice();
		});
		
		$(document).on('change','#ppnselect', function(){
			sumInvoice();
		});
		
		$(document).on('click','#proses_inv', function(e){
			 e.preventDefault();
			if ($('#tgl_inv').val() == "") {
				swal({
					title	: "TANGGAL INVOICE TIDAK BOLEH KOSONG!",
					text	: "ISI TANGGAL INVOICE!",
					type	: "warning",
					timer	: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
			else if ($('#kurs').val()=="" && $('#wilayah').val() == 'L') {
				swal({
					title	: "KURS HARUS DI UPDATE!",
					text	: "SILAHKAN UPDATE KURS TERLEBIH DAHULU!",
					type	: "warning",
					timer	: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
				$('#kurs').focus();
			}
			else{
				// alert('Development');
				// return false;
				
				swal({
					title: "Anda Yakin?",
					text: "You will not be able to process again this data!",
					type: "warning",
					showCancelButton: true,
					confirmButtonClass: "btn-danger",
					confirmButtonText: "Ya Lanjutkan",
					cancelButtonText: "Batal",
					closeOnConfirm: false,
					closeOnCancel: false,
					showLoaderOnConfirm: true
				},
				function(isConfirm) {
					if (isConfirm) {
					  var formData 	=new FormData($('#form_proses')[0]);
					  var baseurl=base_url + active_controller +'/create_progress';
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
							  timer	: 15000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							window.location.href = base_url + active_controller;
						  }else{

							if(data.status == 2){
							  swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 10000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							  });
							}else{
							  swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 10000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							  });
							}

						  }
						},
						error: function() {
						  swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',
							type				: "error",
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}
					  });
					}else {
					  swal("Batal Proses", "Data bisa diproses nanti", "error");
					  return false;
					}
				});
			}
		});
	});
	
	let SUM_RETENSI = 0;
	let sumRetensi = () => {
		$('.changeAll').each(function(){
			SUM_RETENSI = getNum($(this).val().split(",").join(""));
		});
		$('#total_trucking1').val(number_format(SUM_RETENSI,2));
		$('#grand_total').val(number_format(SUM_RETENSI,2));
		$('#grand_total_hidden').val(SUM_RETENSI);
		return SUM_RETENSI;
	}
	
	let sumInvoice = () => {
		let ppnselect 	= $('#ppnselect').val();
		
		let sum_retensi = getNum($('#total_trucking1').val().split(",").join(""));
		let diskon 		= getNum($('#diskon').val().split(",").join(""));
		
		let ppn = 0;
		if(ppnselect == '1'){
			ppn = (sum_retensi - diskon) * 10/100;
		}
		$('#ppn').val(number_format(ppn,2));
		$('#ppn_hidden').val(ppn);
		
		let invoice = sum_retensi - diskon + ppn;
		
		$('#total_invoice').val(number_format(invoice,2));
		$('#total_invoice_hidden').val(invoice);
		return invoice;
	}
</script>
