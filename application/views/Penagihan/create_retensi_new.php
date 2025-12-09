<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data" autocomplete='off'>
<input type="hidden" name="persen" id="persen" value="0"> 
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
					<input type='hidden' name='base_cur' id='base_cur' class='form-control input-md' readonly value='<?=$base_cur;?>'>
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
					<input type='text' name='tgl_inv' id='tgl_inv' class='form-control input-md datepicker' readonly value='<?=$penagihan[0]->tgl_invoice;?>'>
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
					<input type='text' name='nomor_po' id='nomor_po' class='form-control input-md' value='<?=$penagihan[0]->no_po;?>' readonly required>
				</div>
				<label class='label-control col-sm-2'><b>F. No Faktur <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_faktur' id='nomor_faktur' class='form-control input-md' value='<?=$penagihan[0]->no_faktur;?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No Pajak <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_pajak' id='nomor_pajak' class='form-control input-md' value='<?=$penagihan[0]->no_pajak;?>'>
				</div>
				<label class='label-control col-sm-2'><b>Kurs <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='kurs' id='kurs' class='form-control input-md' value='<?=number_format($kurs,2);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
					<input type="hidden" id='wilayah' name="wilayah" class="form-control input-sm" value="<?= substr($in_ipp,-1) ?>">
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>PPN<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
				<?php
				$selects0="";
				$selects1="";
				$selects2="";
				$selects3="";
				if(isset($penagihan[0]->ppnselect)){
					if($penagihan[0]->ppnselect=="1") $selects1="selected";
					if($penagihan[0]->ppnselect=="2") $selects2="selected";
					if($penagihan[0]->ppnselect=="0") $selects3="selected";
				}else{
					$selects0="selected";
				}
				?>
					<select id="ppnselect" name="ppnselect" class="form-control input-sm chosen_select" required>
						<option value="0" <?=$selects0?>>SELECT AN PPN</option>
						<option value="2" <?=$selects2?>>PPN 11%</option>
						<option value="1" <?=$selects1?>>PPN 10%</option>
						<option value="0" <?=$selects3?>>NON PPN</option>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>TOP <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select id="top" name="top" class="form-control input-sm chosen_select" required>
						<option value="0">SELECT AN TOP</option>
						<?php
						foreach($list_top AS $val => $valx){
							$selects='';
							if(isset($penagihan[0]->payment_term)){
								if($penagihan[0]->payment_term==$valx['data1']) $selects=' selected';
							}							
							echo "<option value='".$valx['data1']."' ".$selects.">".strtoupper($valx['name'])."</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Keterangan</b></label>
				<div class='col-sm-4'>
					<input type="text" name="keterangan" id="keterangan" class="form-control input-md " value="<?=$penagihan[0]->keterangan;?>">
				</div>
			</div>


<?php      print_r($base_cur);
			exit;
			if ($base_cur=='USD'){
				$this->load->view('Penagihan/retensi_usd');
			}else{
				$this->load->view('Penagihan/retensi_idr');
			}
?>			
			<br>
			<?php
				if(isset($approval)){
					if($approval!='') echo '<a href="javascript:approve('.$id.')" class="btn btn-info"><i class="fa fa-check"> Update</i></a>';
				}
				echo ' &nbsp; <a href="#" onclick="javascript:back()" class="btn btn-default btn-md">Back</a> &nbsp; ';
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin: 0px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'proses_inv')).' ';
			?>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<script src="<?=base_url('application/views/Component/general.js'); ?>"></script>
<style>.datepicker{cursor:pointer;}</style>
<script>
<?php
if(isset($approval)){
	if($approval!='') echo '$("#form_proses :input").prop("disabled", true);';
}
?>
	$('.divide').divide();	
	function approve(id){		
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
			  var baseurl=base_url + active_controller +'/create_invoice_new/'+id;
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
					  timer	: 15000,
					  showCancelButton	: false,
					  showConfirmButton	: false,
					  allowOutsideClick	: false
					});
					window.location.href = base_url + active_controller ;
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
	var base_cur='<?=$base_cur?>';
	$(document).ready(function(){
		$(document).on('click','#back', function(){
			window.location.href = base_url + active_controller;
		});

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
					  var baseurl=base_url + active_controller +'/create_progress_new';
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
	let delRow6 = (row) => {
		$('#tr5_'+row).remove();
		retensiall=0;
		$('.changeAll').each(function(){
			retensiall += getNum($(this).val().split(",").join(""));
		});
		$('#total_trucking').val(number_format(retensiall,2));
		$('#grand_total').val(number_format(retensiall,2));
		$('#grand_total_hidden').val(retensiall);
		sumInvoice();

	}	
	let SUM_RETENSI = 0;
	let sumRetensi = () => {
		$('.changeAll').each(function(){
			SUM_RETENSI += getNum($(this).val().split(",").join(""));
		});
		$('#total_trucking').val(number_format(SUM_RETENSI,2));
		$('#grand_total').val(number_format(SUM_RETENSI,2));
		$('#grand_total_hidden').val(SUM_RETENSI);
		return SUM_RETENSI;
	}
	
	let sumInvoice = () => {
		let ppnselect 	= $('#ppnselect').val();
		
		let sum_retensi = getNum($('#total_trucking').val().split(",").join(""));
		let diskon 		= getNum($('#diskon').val().split(",").join(""));
		
		ppn = 0;
		if(ppnselect == '2'){
			if(base_cur=='IDR'){
				ppn = Math.floor((sum_retensi - diskon) * 11/100);
			}else{
				ppn = ((sum_retensi - diskon) * 11/100);
			}
		}
		if(ppnselect == '1'){
			if(base_cur=='IDR'){
				ppn = Math.floor((sum_retensi - diskon) * 10/100);
			}else{
				ppn = ((sum_retensi - diskon) * 10/100);
			}
		}
		$('#ppn').val((ppn));
		$('#ppn_hidden').val(ppn);
		
		let invoice = sum_retensi - diskon + ppn;
		
		$('#total_invoice').val(number_format(invoice,2));
		$('#total_invoice_hidden').val(invoice);
		return invoice;
	}
</script>
