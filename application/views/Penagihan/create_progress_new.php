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
					<input type='text' name='type' id='type' class='form-control input-md' readonly value='PROGRESS'>
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
					<input type='text' name='kurs' id='kurs' class='form-control input-md maskMoney' value='<?=number_format($kurs);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
                     <?php 
						if(!empty($getHeader[0]->no_ipp)){
							$wilayah = get_name('so_number','wilayah','id_bq', "BQ-".$getHeader[0]->no_ipp);
						}else{
							$wilayah ='-';
						}
					 
					 ?>
					<input type="hidden" id='wilayah' name="wilayah" class="form-control input-sm" value="<?= $wilayah ?>">
				</div>
				<div class='col-sm-2'>
					<input type='text' name='base_cur' id='base_cur' class='form-control input-md' value='<?=($base_cur);?>' readonly>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>PPN | TOP <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
				<?php
				$selects0="";
				$selects1="";
				$selects2="";
				if(isset($penagihan[0]->ppnselect)){
					if($penagihan[0]->ppnselect=="1") $selects1="";
					if($penagihan[0]->ppnselect=="2") $selects2="";
				}
				?>
					<select id="ppnselect" name="ppnselect" class="form-control input-sm chosen_select" required>
						<option value="0" <?=$selects0?>>SELECT AN PPN</option>
						<option value="2" <?=$selects2?>>PPN 11%</option>
						<option value="1" <?=$selects1?>>PPN 10%</option>
						<option value="0" <?=$selects0?>>NON PPN</option>
					</select>
				</div>
				<div class='col-sm-2'>
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

				<label class='label-control col-sm-2'><b>Persentase Progress (%)</b></label>
				<div class='col-sm-3'>
					<?php
					$sisa_progress = 100 - $penagihan[0]->progress_persen;
					$umpersen=(isset($penagihan[0]->persentase)?$penagihan[0]->persentase:0);
					$persen=(isset($penagihan[0]->persen)?$penagihan[0]->persen:0);
					$persen2=(isset($penagihan[0]->persen2)?$penagihan[0]->persen2:0);
					?>
					<input type='text' name='umpersen' id='umpersen' class='form-control input-md maskMoney' maxlength='3' value='<?=$umpersen?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
				</div>
				<div class='col-sm-1'>
					<input type='hidden' name='sudah_progress' id='sudah_progress' class='form-control text-center input-md' value='<?=$sisa_progress;?>' readonly>
					<input type='text' name='progressx' id='progressx' class='form-control text-center input-md' value='<?=$penagihan[0]->progress_persen;?>' readonly>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Uang Muka %</b></label>
				<div class='col-sm-4'>
					<input type="text" name="persen" id="persen" class="form-control input-md persen" value="<?=$uang_muka_persen;?>" readonly>
				</div>
				<label class='label-control col-sm-2'><b>Keterangan</b></label>
				<div class='col-sm-4'>
					<input type="text" name="keterangan" id="keterangan" class="form-control input-md " value="<?=$penagihan[0]->keterangan;?>">
				</div>

				<label class='label-control col-sm-2 hidden'><b>Uang Muka II (%)</b></label>
				<div class='col-sm-4 hidden'>
					<input type="text" name="persen2" id="persen2" class="form-control input-md persen2" value="<?=$persen2;?>" readonly>
					<input type="hidden" name="um_persen2" id="um_persen2" class="form-control input-md" value="<?=$uang_muka_persen2;?>">
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Delivery No</b></label>
				<div class='col-sm-10'>
					<select id="delivery_no" name="delivery_no[]" class="form-control input-sm" multiple>
					<?php
					$dtIPP="'PRO-".str_ireplace(",","','PRO-",$in_ipp)."'";

					$dt_delivery = $this->db->query("SELECT * FROM delivery_product where confirm_date is not null and st_cogs='0' and kode_delivery in (select kode_delivery from delivery_product_detail where id_produksi in (".$dtIPP."))")->result();
					foreach ($dt_delivery as $val => $valx) {
						$selected=" selected";
						if(stripos($penagihan[0]->delivery_no,$valx->kode_delivery)===false) $selected="";
						echo "<option value='" . $valx->kode_delivery . "' ".$selected.">". $valx->nomor_sj.' ['.$valx->kode_delivery.'] '.$valx->project ."</option>";
					}
					?>
					</select>
				</div>
			</div>
<?php
			if ($base_cur=='USD'){
				$this->load->view('Penagihan/progress_usd');
			}else{
				$this->load->view('Penagihan/progress_idr');
			}
?>
			<?php
				if(isset($approval)){
					if($approval!='') echo '<a href="javascript:approve('.$id.')" class="btn btn-info"><i class="fa fa-check"> Update</i></a>';
				}
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin: 0px 0px 5px 5px;','value'=>'Back','content'=>'Back','id'=>'back')).' ';
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

    $(".divide").divide();
	var base_cur='<?=$base_cur?>';
	function showdata(i,row){
		var onoff=true;
		if($("#ck"+i+"_"+row).prop('checked') == true) onoff=false;
		switch(i) {
			case "1":
				$("#qty2_"+row).val("0").change();
				$("#qty2_"+row).prop('disabled', onoff);
			break;
			case "2":
				$("#qty3_"+row).val("0").change();
				$("#qty3_"+row).prop('disabled', onoff);
			break;
			case "3":
				$("#harga_tot4"+row).val("0").change();
				$("#harga_tot4"+row).prop('disabled', onoff);
			break;
			case "4":
				$("#harga_tot5"+row).val("0").change();
				$("#harga_tot5"+row).prop('disabled', onoff);
			break;
			case "5":
				$("#harga_tot6"+row).val("0").change();
				$("#harga_tot6"+row).prop('disabled', onoff);
			break;
			default:
				$("#qty_"+row).val("0").change();
				$("#qty_"+row).prop('disabled', onoff);
		}
	}
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

		$(document).on('keyup','#umpersen', function(){
			let umpersen = getNum($(this).val());
			let umpersen_sisa = getNum($('#sudah_progress').val());

			if(umpersen > umpersen_sisa){
				$(this).val(umpersen_sisa);
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
			else if ($('#jenis_invoice').val()=="uang muka" && $('#um_persen').val()=="") {
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

		$(document).on('keyup', '#um_persen', function(){
			umLoad()
		});

		$(document).on('keyup', '#um_persen2', function(){
			umLoad2()
		});

		$(document).on('keyup change', '.qty_product', function(){
			let dataNomor 	= $(this).data('nomor');
			let sisa 		= getNum($('#qty_belum_'+dataNomor).val().split(",").join(""));
			let dataIni	  	= getNum($(this).val().split(",").join(""));
			if(dataIni > sisa){
				$(this).val(number_format(sisa));
				dataIni	 = sisa;
			}
			let hargaSat  	= $('#harga_sat_'+dataNomor).val();
			let total     	= getNum(hargaSat*dataIni).toFixed(2);
			$('#harga_tot_'+dataNomor).val(num2(total));
			$('#harga_tot_hidden'+dataNomor).val(total);
			fnAlltotal()
		});

		$(document).on('blur', '.harga_tot8', function(){
			let dataNomor8 = $(this).data('nomor');
			let hargaSat8  = 1;
			let dataIni8   = ($(this).val()).split(",").join("");
			let total8     = getNum(hargaSat8*dataIni8).toFixed(2);
			$('#harga_tot8'+dataNomor8).val(num2(total8));
			$('#harga_tot8_hidden'+dataNomor8).val(total8);
			fnAlltotal8()
		});

		$(document).on('keyup change', '.qty_bq', function(){
			let dataNomor = $(this).data('nomor');
			let hargaSat  = $('#harga_sat2'+dataNomor).val();
			let dataIni	  = $(this).val();
			let datasisa=$("#qty2_belum_"+dataNomor).val();
			if(parseFloat(datasisa)<parseFloat(dataIni)) {
				$(this).val(datasisa);
				dataIni=datasisa;
			}
			let total     = getNum(hargaSat*dataIni).toFixed(2);
			$('#harga_tot2'+dataNomor).val((total));
			fnAlltotal2();
		});

		$(document).on('keyup change', '.qty_material', function(){
			let dataNomor3 = $(this).data('nomor');
			let hargaSat3  = $('#harga_sat3'+dataNomor3).val();
			let dataIni3   = $(this).val();			
			let datasisa3=$("#qty3_belum_"+dataNomor3).val();
			if(parseFloat(datasisa3)<parseFloat(dataIni3)) {
				$(this).val(datasisa3);
				dataIni3=datasisa3;
			}
			let total3     = getNum(hargaSat3*dataIni3).toFixed(2);
			$('#harga_tot3'+dataNomor3).val(num2(total3));
			fnAlltotal3()
		});

		$(document).on('blur change', '.harga_tot4', function(){
			let dataNomor4 = $(this).data('nomor');
			let dataIni4   = ($(this).val());
			let datasisa4=$("#harga_tot4_sisa_"+dataNomor4).val();
			if(parseFloat(datasisa4)<parseFloat(dataIni4)) {
				$(this).val(datasisa4);
			}
			fnAlltotal4();
		});

		$(document).on('blur change', '.harga_tot5', function(){
			let dataNomor5 = $(this).data('nomor');
			let dataIni5   = ($(this).val());
			let datasisa5=$("#harga_tot5_sisa_"+dataNomor5).val();
			if(parseFloat(datasisa5)<parseFloat(dataIni5)) {
				$(this).val(datasisa5);
			}
			fnAlltotal5()
		});

		$(document).on('blur change', '.harga_tot6', function(){
			let dataNomor6 = $(this).data('nomor');
			let dataIni6   = ($(this).val());
			let datasisa6=$("#harga_tot6_sisa_"+dataNomor6).val();
			if(parseFloat(datasisa6)<parseFloat(dataIni6)) {
				$(this).val(datasisa6);
			}
			fnAlltotal6()
		});

		$(document).on('keyup', '.qty_lokal', function(){
			let dataNomor7 = $(this).data('nomor');
			let hargaSat7  = $('#harga_sat7_hidden'+dataNomor7).val();
			let dataIni7  = ($(this).val()).split(",").join("");
			let total7     = getNum(hargaSat7*dataIni7).toFixed(2);
			$('#harga_tot7'+dataNomor7).val(num2(total7));
			$('#harga_tot7_hidden'+dataNomor7).val(total7);
			fnAlltotal7()
		});

		$(document).on('blur', '.diskon', function(){
			let dataPpn	  = $('#ppnselect').val();
			let dataDiskon	 = $(this).val();
			let totalDiskon  = getNum(dataDiskon).toFixed(2);
			let grandtotal   = $(".grand_total").val();
			let uangmuka     = $(".down_payment").val();
			$('.diskon').val(num2(totalDiskon));
			if(dataPpn=='1'){
				if(base_cur=='IDR'){
					 totalPpn     = Math.floor(getNum((grandtotal-totalDiskon-uangmuka)*0.1));
				}else{
					 totalPpn     = (getNum((grandtotal-totalDiskon-uangmuka)*0.1));
				}
			}else{
				if(dataPpn=='2'){
					if(base_cur=='IDR'){
						totalPpn     = Math.floor(getNum((grandtotal-totalDiskon-uangmuka)*0.11));
					}else{
						totalPpn     = (getNum((grandtotal-totalDiskon-uangmuka)*0.11));
					}
				}
				else{
				  totalPpn     = 0;
				}
			}
			$('.ppn').val((totalPpn));
			totalInvoice()
		});

		$(document).on('change', '#persen_retensi', function(){
			var grand_total_hidden	= $("#grand_total").val();
			var datRetensi         	= $('#persen_retensi').val();
			totalret=(parseFloat(grand_total_hidden)*parseFloat(datRetensi/100)).toFixed(2);
			$('.potongan_retensi').val(num2(totalret));
			ppn();
		});

		$(document).on('change', '#persen_retensi2', function(){
			var grand_total_hidden	= $("#grand_total").val();
			var datRetensi         	= $('#persen_retensi2').val();
			Retensi2=(parseFloat(grand_total_hidden)*parseFloat(datRetensi/100)).toFixed(2);
			$('.potongan_retensi2').val(number_format(Retensi2,2));
			ppn();
		});

		$(document).on('blur', '.potongan_retensix', function(){
			let dataPpn	  = $('#ppnselect').val();
			let dataRetensi	  = $(this).val();
			let totalRetensi     = getNum(dataRetensi).toFixed(2);
			let grandtotal   = $(".grand_total").val();
			let totalDiskon     = $(".diskon").val();
			let uangmuka     = $(".down_payment").val();
			$('.potongan_retensi').val(num2(totalRetensi));
			if(dataPpn=='1'){
				if(base_cur=='IDR'){
					totalPpn     = Math.floor(getNum((grandtotal-totalDiskon-totalRetensi-uangmuka)*0.1));
				}else{
					totalPpn     = (getNum((grandtotal-totalDiskon-totalRetensi-uangmuka)*0.1));
				}
			}
			else{
				if(dataPpn=='2'){
					if(base_cur=='IDR'){
					  totalPpn     = Math.floor(getNum((grandtotal-totalDiskon-totalRetensi-uangmuka)*0.11));
					}else{
					  totalPpn     = (getNum((grandtotal-totalDiskon-totalRetensi-uangmuka)*0.11));
					}
				}
				else{
				  totalPpn     = 0;
				}
			}
			$('.ppn').val((totalPpn));
			totalInvoice();
		});
/*
		$(document).on('blur', '.ppn', function(){
			let dataPpn	  = $(this).val();
			if(base_cur=='IDR'){
				let totalPpn     = Math.floor(getNum(dataPpn));
			}else{
				let totalPpn     = (getNum(dataPpn));
			}
			$('.ppn').val((totalPpn));
			totalInvoice();
		});
*/
		$(document).on('change', '#ppnselect', function(){
			ppn();
		});
		$(document).on('click','#back', function(){
			window.location.href = base_url + active_controller+"/delivery";
		});
	});


	//FUNCTION

	let umLoad = () => {
		let hargaSat  = getNum($('#harga_tot').val());
		let dataIni	  = getNum($('#um_persen').val());
		let nilai;
		let total;
		let id;
		//PRODUCT
		let SUM = 0;
		$(".changeProduct" ).each(function() {
			id = $(this).data('id');
			let harga_satuan = getNum($('#harga_sat_'+id).val().split(",").join(""));
			let qty_sisa = getNum($('#qty_'+id).val().split(",").join(""));
			nilai 	= Number($(this).val().split(",").join(""));
			// nilai 	= harga_satuan * qty_sisa;
			total   = nilai * (dataIni/100);
			SUM += total;
			$('#harga_tot_'+id).val(num(total));
		});
		$('#tot_product').val(num(SUM));
		//AKSESORIS
		let SUM2 = 0;
		$(".changeAcc").each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM2 += total;
			$('#harga_tot2'+id).val(num(total));
		});
		$('#total_bq_nf').val(num(SUM2));
		//MATERIAL
		let SUM3 = 0;
		$(".changeMat" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM3 += total;
			$('#harga_tot3'+id).val(num(total));
		});
		$('#total_material3').val(num(SUM3));
		//ENGINE
		let SUM4 = 0;
		$(".changeEng" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM4 += total;
			$('#harga_tot4'+id).val(num(total));
		});
		$('#total_enginering1').val(num(SUM4));
		//PACKING
		let SUM5 = 0;
		$(".changePack" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM5 += total;
			$('#harga_tot5'+id).val(num(total));
		});
		$('#total_packing1').val(num(SUM5));
		//SHIPPING E
		let SUM6 = 0;
		$(".changeShip" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM6 += total;
			$('#harga_tot6'+id).val(num(total));
		});
		$('#total_trucking1').val(num(SUM6));
        grandtotal();
		totalInvoice();
	}

	let umLoad2 = () => {
		let hargaSat  = $('#harga_tot_hidden').val();
		let dataIni	  = Number($('#um_persen2').val());
		let nilai;
		let total;
		let id;
		//PRODUCT
		let SUM = 0;
		$(".changeProduct" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM += total;
			$('#harga_tot_'+id).val(num(total));
		});
		$('#tot_product').val(num(SUM));

		//AKSESORIS
		let SUM2 = 0;
		$(".changeAcc").each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM2 += total;
			$('#harga_tot2'+id).val(num(total));
		});
		$('#total_bq_nf').val(num(SUM2));

		//MATERIAL
		let SUM3 = 0;
		$(".changeMat" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM3 += total;
			$('#harga_tot3'+id).val(num(total));
		});
		$('#total_material3').val(num(SUM3));

		//ENGINE
		let SUM4 = 0;
		$(".changeEng" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM4 += total;
			$('#harga_tot4'+id).val(num(total));
		});
		$('#total_enginering1').val(num(SUM4));

		//PACKING
		let SUM5 = 0;
		$(".changePack" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM5 += total;
			$('#harga_tot5'+id).val(num(total));
		});
		$('#total_packing1').val(num(SUM5));

		//SHIPPING E
		let SUM6 = 0;
		$(".changeShip" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM6 += total;
			$('#harga_tot6'+id).val(num(total));
		});
		$('#total_trucking1').val(num(SUM6));
        grandtotal();
		totalInvoice();
	}

	let fnAlltotal = () => {
	  let total=0
		$(".amount1").each(function(){
			 total += getNum($(this).val()||0);
		});
		$(".result1").val(num(total));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal2 = () => {
	  let total=0
		$(".amount2").each(function(){
			 total += getNum($(this).val()||0);
		});
		$(".result2").val(num(total));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal3 = () => {
		let total31=0
		$(".amount3").each(function(){
			 total31 += getNum($(this).val()||0);
		});
		$(".result3").val(num(total31));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal4 = () => {
	  let total41=0
		$(".harga_tot4").each(function(){
			 total41 += getNum($(this).val()||0);
		});
		$(".result4").val(num(total41));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal5 = () => {
		let total51=0
		$(".harga_tot5").each(function(){
			 total51 += getNum($(this).val()||0);
		});
		$(".result5").val(num(total51));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal6 = () => {
		let total61=0
		$(".harga_tot6").each(function(){
			 total61 += getNum($(this).val()||0);
		});
		$(".result6").val(num(total61));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal7 = () => {
		let total71=0
		$(".amount7").each(function(){
			 total71 += getNum($(this).val()||0);
		});
		$(".result7").val(num(total71));
		grandtotal();
		totalInvoice();
	}

	let fnAlltotal8 = () => {
		let total81=0
		$(".amount8").each(function(){
			 total81 += getNum($(this).val()||0);
		});

		$(".result8").val(num(total81));
		grandtotal();
		totalInvoice();
	}

	let grandtotal = () => {
		let dataPpn    = $('#ppnselect').val();
		let result1_hidden1 = 0;
		let result2_hidden1 = 0;
		let result3_hidden1 = 0;
		let result4_hidden1 = 0;
		let result5_hidden1 = 0;
		let result6_hidden1 = 0;
		let result7_hidden1 = 0;
		let result8_hidden1 = 0;

		let result1_hidden  = getNum($('.result1').val());
		let result2_hidden  = getNum($('.result2').val());
		let result3_hidden  = getNum($('.result3').val());
		let result4_hidden  = getNum($('.result4').val());
		let result5_hidden  = getNum($('.result5').val());
		let result6_hidden  = getNum($('.result6').val());
		let result7_hidden  = getNum($('.result7').val());
		let result8_hidden  = getNum($('.result8').val());
		let diskon_hidden  			= getNum($('.diskon').val());
		let potongan_retensi_hidden = getNum($('.potongan_retensi').val());
		let down_payment_hidden     = getNum($('.down_payment').val());
		let uang_muka  				= getNum($('.persen').val());
		let uang_muka2 				= getNum($('.persen2').val());

		result1_hidden1 = result1_hidden==null ? 0 : result1_hidden;
		result2_hidden1 = result2_hidden==null ? 0 : result2_hidden;
		result3_hidden1 = result3_hidden==null ? 0 : result3_hidden;
		result4_hidden1 = result4_hidden==null ? 0 : result4_hidden;
		result5_hidden1 = result5_hidden==null ? 0 : result5_hidden;
		result6_hidden1 = result6_hidden==null ? 0 : result6_hidden;
		result7_hidden1 = result7_hidden==null ? 0 : result7_hidden;
		result8_hidden1 = result8_hidden==null ? 0 : result8_hidden;

		let grandtotal 	= 	getNum(result1_hidden1)
							+ getNum(result2_hidden1)
							+ getNum(result3_hidden1)
							+ getNum(result4_hidden1)
							+ getNum(result5_hidden1)
							+ getNum(result6_hidden1)
							+ getNum(result7_hidden1)
							+ getNum(result8_hidden1);

		let uangmuka   	= 	(getNum(grandtotal * uang_muka)/100);
		let uangmuka2 = 0;
		if(dataPpn=='0'){
			let totalPpn=0;
		}
		if(dataPpn=='1'){
			if(base_cur=='IDR'){
				let totalPpn = Math.floor(getNum((grandtotal - diskon_hidden - potongan_retensi_hidden - uangmuka)*0.1));
			}else{
				let totalPpn = (getNum((grandtotal - diskon_hidden - potongan_retensi_hidden - uangmuka)*0.1));
			}
		}
		if(dataPpn=='2'){
			if(base_cur=='IDR'){
				let totalPpn = Math.floor(getNum((grandtotal - diskon_hidden - potongan_retensi_hidden - uangmuka)*0.11));
			}else{
				let totalPpn = (getNum((grandtotal - diskon_hidden - potongan_retensi_hidden - uangmuka)*0.11));
			}
		}
		$(".down_payment").val(number_format(uangmuka,2));
		$(".down_payment2").val(number_format(uangmuka2,2));
		$(".grand_total").val(number_format(grandtotal,2));
		ppn();
	}

	let ppn = () => {
		let dataPpn                 = $('#ppnselect').val();
		let ppntotal                = 0
		let grandtotal              = $(".grand_total").val();
		let diskon_hidden           = $('.diskon').val();
		let potongan_retensi_hidden = $('.potongan_retensi').val();
		let down_payment_hidden     = $('.down_payment').val()
		let down_payment_hidden2    = $('.down_payment2').val()

		let	diskon_hidden1 				= diskon_hidden==null ? 0 : diskon_hidden;
		let	potongan_retensi_hidden1 	= potongan_retensi_hidden==null ? 0 : potongan_retensi_hidden;
		let	down_payment_hidden1 		= down_payment_hidden==null ? 0 : down_payment_hidden;
		let	down_payment_hidden12 		= down_payment_hidden2==null ? 0 : down_payment_hidden2;
		if(dataPpn=='0'){
			totalPpn=0;
		}
		if(dataPpn=='1'){
			if(base_cur=='IDR'){
				totalPpn = Math.floor(getNum((grandtotal - diskon_hidden1 - potongan_retensi_hidden1 - down_payment_hidden1- down_payment_hidden12)*0.1));
			}else{
				totalPpn = (getNum((grandtotal - diskon_hidden1 - potongan_retensi_hidden1 - down_payment_hidden1- down_payment_hidden12)*0.1));
			}
		}
		if(dataPpn=='2'){
			if(base_cur=='IDR'){
				totalPpn = Math.floor(getNum((grandtotal - diskon_hidden1 - potongan_retensi_hidden1 - down_payment_hidden1- down_payment_hidden12)*0.11));
			}else{
				totalPpn = (getNum((grandtotal - diskon_hidden1 - potongan_retensi_hidden1 - down_payment_hidden1- down_payment_hidden12)*0.11));
			}
		}
		$('.ppn').val(num(totalPpn));
		totalInvoice();
	}

	let totalInvoice = () => {
		let grandtotal 		= 0;
		let result1_hidden1 = 0;
		let result2_hidden1 = 0;
		let result3_hidden1 = 0;
		let result4_hidden1 = 0;
		let result5_hidden1 = 0;
		let result6_hidden1 = 0;
		let result7_hidden1 = 0;
		let result8_hidden1 = 0;
		let potongan_retensi_hidden1 	= 0;
		let down_payment_hidden1 		= 0;
		let down_payment_hidden12 		= 0;
		let result1_hidden  = $('.result1').val();
		let result2_hidden  = $('.result2').val();
		let result3_hidden  = $('.result3').val();
		let result4_hidden  = $('.result4').val();
		let result5_hidden  = $('.result5').val();
		let result6_hidden  = $('.result6').val();
		let result7_hidden  = $('.result7').val();
		let result8_hidden  = $('.result8').val();
		let diskon_hidden  				= $('.diskon').val();
		let potongan_retensi_hidden  	= $('.potongan_retensi').val();
		let potongan_retensi_hidden2  	= $('.potongan_retensi2').val();
		let ppn_hidden  				= $('.ppn').val();
		let down_payment_hidden  		= $('.down_payment').val()
		let down_payment_hidden2  		= $('.down_payment2').val()

		result1_hidden1 = result1_hidden==null ? 0 : result1_hidden;
		result2_hidden1 = result2_hidden==null ? 0 : result2_hidden;
		result3_hidden1 = result3_hidden==null ? 0 : result3_hidden;
		result4_hidden1 = result4_hidden==null ? 0 : result4_hidden;
		result5_hidden1 = result5_hidden==null ? 0 : result5_hidden;
		result6_hidden1 = result6_hidden==null ? 0 : result6_hidden;
		result7_hidden1 = result7_hidden==null ? 0 : result7_hidden;
		result8_hidden1 = result8_hidden==null ? 0 : result8_hidden;

		potongan_retensi_hidden1 	= potongan_retensi_hidden==null ? 0 : potongan_retensi_hidden;
		down_payment_hidden1 		= down_payment_hidden==null ? 0 : down_payment_hidden;
		down_payment_hidden12 		= down_payment_hidden2==null ? 0 : down_payment_hidden2;

		grandtotal 	= 	(getNum(result1_hidden1)
						+ getNum(result2_hidden1)
						+ getNum(result3_hidden1)
						+ getNum(result4_hidden1)
						+ getNum(result5_hidden1)
						+ getNum(result6_hidden1)
						+ getNum(ppn_hidden)
						+ getNum(result7_hidden1)
						+ getNum(result8_hidden1)
						- getNum(diskon_hidden)
						- getNum(potongan_retensi_hidden1)
						- getNum(potongan_retensi_hidden2)
						- getNum(down_payment_hidden1)
						- getNum(down_payment_hidden12));

		$(".total_invoice").val(num(grandtotal));
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

function cek_total_detail(kolom){
	f_grand_total=$("#grand_total").val();
	f_down_payment=$("#down_payment").val();
	f_potongan_retensi=$("#potongan_retensi").val();
	f_ppn=$("#ppn").val();
	f_potongan_retensi2=$("#potongan_retensi2").val();
	f_total_invoice=$("#total_invoice").val();
	if(kolom=="grand_total"){
	}
	if(kolom=="down_payment"){
	}
	if(kolom=="ppn"){
	}
	if(kolom=="potongan_retensi2"){
	}
	if(kolom=="potongan_retensi"){
	}
	if(kolom=="total_invoice"){
	}
}
</script>
