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
				</div>
				<label class='label-control col-sm-2'><b>SO Number</b></label>
				<div class='col-sm-4'>
					<input type='text' name='no_so' id='no_so' class='form-control input-md' readonly value='<?=$in_so;?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Invoice Type</b></label>
				<div class='col-sm-4'>
					<input type='text' name='type' id='type' class='form-control input-md' readonly value='UANG MUKA'>
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
					<input type='text' name='nomor_po' id='nomor_po' class='form-control input-md' value='<?=$penagihan[0]->no_po;?>'  readonly required>
				</div>
				<label class='label-control col-sm-2'><b>F. No Faktur <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_faktur' id='nomor_faktur' class='form-control input-md' value="<?=$penagihan[0]->no_faktur;?>" required>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No Pajak <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type='text' name='nomor_pajak' id='nomor_pajak' class='form-control input-md' value="<?=$penagihan[0]->no_pajak;?>" required>
				</div>
				<label class='label-control col-sm-2'><b>Kurs <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<input type='text' name='kurs' id='kurs' class='form-control input-md' value='<?=number_format($kurs,2);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
					<input type="hidden" id='wilayah' name="wilayah" class="form-control input-sm" value="-">
				</div>
				<div class='col-sm-2'>
					<input type='text' name='base_cur' id='base_cur' class='form-control input-md' value='<?=($base_cur);?>' readonly>
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
			<?php
			$um1 = $uang_muka_persen2;

			if($um1 < 1){?>
				<div class='form-group row'>
					  <label for="jenis_invoice" class="col-sm-2 control-label">Persentase UM<span class='text-red'>*</span></font></label>
					  <div class="col-sm-4">
						 <input type="text" name="um_persen" id="um_persen" class="form-control input-md autoNumeric" value="<?=$penagihan[0]->persentase?>">
					  </div>
					<label class='label-control col-sm-2'><b>Keterangan</b></label>
					<div class='col-sm-4'>
						<input type="text" name="keterangan" id="keterangan" class="form-control input-md " value="<?=$penagihan[0]->keterangan;?>">
					</div>	
				</div>
			<?php
			}

			if($um1 > 0){?>
				<div class='form-group row hidden'>
						<label for="jenis_invoice" class="col-sm-2 control-label">Persentase UM I <span class='text-red'>*</span></font></label>
						<div class="col-sm-4">
							<input type="text" name="um_persen" id="um_persen" class="form-control input-md persen" value="<?php  echo $uang_muka_persen ?>" readonly>
						</div>
						<label for="jenis_invoice" class="col-sm-2 control-label">Persentase UM II </font></label>
						<div class="col-sm-4">
							<input type="text" name="um_persen2" id="um_persen2" class="form-control input-md" value="">
						</div>
				</div>

			<?php
			}
			if ($base_cur=='USD'){
				$this->load->view('Penagihan/um_usd');
			}else{
				$this->load->view('Penagihan/um_idr');
			}
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
	var base_cur='<?=$base_cur?>';
	$('.divide').divide();	
	$(document).ready(function(){
		$('.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth:true,
				changeYear:true
			});
		// umLoad();

		$(document).on('keyup','#kurs', function(){
			let kurs = $(this).val();
			if(kurs == '0' || kurs == ''){
				$(this).val('1');
			}
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
			else if ($('#type').val()=="UANG MUKA" && $('#um_persen').val()=="") {
				swal({
					title	: "PERSENTASE UM HARUS DIISI!",
					text	: "SILAHKAN ISI PERSENTASE UM TERLEBIH DAHULU!",
					type	: "warning",
					timer	: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
				$('#um_persen').focus();
			}
			else{
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

		$(document).on('keyup', '#um_persen', function(){
			umLoad()
		});

		$(document).on('change', '#ppnselect', function(){
			ppn();
		});

		$(document).on('click','#back', function(){
			window.location.href = base_url + active_controller;
		});
	});
	let umLoad = () => {
        grandtotal();
		totalInvoice();
	}
	let grandtotal = () => {

		let dataIni	  = getNum($('#um_persen').val());		
		let dataPpn		= $('#ppnselect').val();
		let total_so	= $("#total_so").val();
		let grandtotal= total_so * (dataIni/100);

		$(".grand_total").val(number_format(grandtotal,2));
		ppn();
	}

	let ppn = () => {
		let dataPpn                 = $('#ppnselect').val();
		let grandtotal              = $("#grand_total").val();

		totalPpn=0;
		if(dataPpn==1){
			if(base_cur=='IDR'){
				totalPpn=Math.floor(getNum((grandtotal)*0.1));
			}else{
				totalPpn=(getNum((grandtotal)*0.1));
			}
		}
		if(dataPpn==2){
			if(base_cur=='IDR'){
				totalPpn=Math.floor(getNum((grandtotal)*0.11));
			}else{
				totalPpn=(getNum((grandtotal)*0.11));
			}
		}
		$('.ppn').val(num(totalPpn));
		totalInvoice();
	}

	let totalInvoice = () => {
		let grandtotal 		= $("#grand_total").val();
		let ppn  				= $('.ppn').val();
		grandtotal 	= 	(getNum(grandtotal) + getNum(ppn));
		$(".total_invoice").val((grandtotal));
	}

	function num(n) {
      return (n).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

	function num2(n) {
      return (n).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

	function num3(n) {
      return (n).toFixed(2);
    }
</script>
