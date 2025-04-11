<?php
$this->load->view('include/side_menu');

?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data" autocomplete='off'> 
<input type='hidden' name='uri' id='uri' value='<?=$uri;?>'>
<input type='hidden' name='doc_number' id='doc_number' value='<?=$doc_number;?>'>
<input type='hidden' name='angka' id='angka' value='<?=COUNT($data);?>'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
            
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class='text-center mid'>Nama Barang/Jasa</th>
                    <th class='text-center mid' width='10%'>Jadwal Pembayaran</th>
                    <th class='text-center mid' width='10%'>Perkiraan Biaya</th>
					<th class='text-center mid' width='20%'>Keterangan</th>
					<th class='text-center mid' width='15%'>Pembayaran</th>
				</tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				// print_r($data);
				if(!empty($data)){
					$total_biaya = 0;
					$total_payment = 0;
					foreach($data AS $val2 => $valx2){ $id++;
						$tgl_bayar = $valx2['jadwal_bayar'];
						$total_biaya += $valx2['nilai_bayar'];
						$nilai_payment = ($valx2['nilai_payment'] > 0)?$valx2['nilai_payment']:$valx2['nilai_bayar'];
						$total_payment+= $nilai_payment;
						echo "<tr class='header_".$id."'>";
							echo "<td align='left'>";
								echo "<input type='hidden' name='detail[".$id."][id]' class='form-control input-md' value='".strtoupper($valx2['id'])."'>";
								echo "<input type='hidden' name='detail[".$id."][id_budget]' class='form-control input-md' value='".strtoupper($valx2['id_budget'])."'>";
								echo "<input type='text' name='detail[".$id."][nama_barang]' id='nama_barang_".$id."' class='form-control input-md' placeholder='Nama Barang/Jasa' readonly  value='".strtoupper($valx2['nama_barang'])."'>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail[".$id."][jadwal_bayar]' id='jadwal_bayar_".$id."' class='form-control text-center input-md' readonly placeholder='Select Date' value='".$tgl_bayar."'>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail[".$id."][biaya]' class='form-control text-right input-md maskMoney' readonly  placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' value='".number_format($valx2['nilai_bayar'])."'>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail[".$id."][keterangan]' id='keterangan_".$id."' class='form-control input-md' placeholder='Keterangan' readonly   value='".strtoupper($valx2['keterangan'])."'>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail[".$id."][payment]' class='form-control text-right input-md maskMoney payment' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' value='".number_format($valx2['nilai_payment'])."'>";
							echo "</td>";
						echo "</tr>";
					}
					
					echo "<tr class='header_".$id."'>";
						echo "<td align='left' class='mid'><b class='mid'>TOTAL PEMBAYARAN</b></td>";
						echo "<td align='left'></td>";
						echo "<td align='left'>";
							echo "<input type='text' class='form-control text-right input-md' readonly value='".number_format($total_biaya)."'>";
						echo "</td>";
						echo "<td align='left'></td>";
						echo "<td align='left'>";
							echo "<input type='text' name='total_payment' id='total_payment' class='form-control text-right input-md' placeholder='0' readonly value='".number_format($data_header[0]->total_payment)."'>";
						echo "</td>";
					echo "</tr>";
					echo "<tr class='header_".$id."'>";
						echo "<td align='left' class='mid'><b class='mid'>PPN</b></td>";
						echo "<td align='left'><input type='text' name='ppn_val' id='ppn_val' class='form-control text-center input-md' value='11'></td>";
						echo "<td align='left'>";
							$non = (empty($data_header[0]->nilai_ppn))?'selected':'';
							$ppn = (!empty($data_header[0]->nilai_ppn))?'selected':'';
							echo "<select name='ppn_type' id='ppn_type' class='chosen_select form-control input-sm'>";
							echo "<option value='non' ".$non.">NON PPN</option>";
							echo "<option value='ppn' ".$ppn.">PPN</option>";
							echo "</select>";
						echo "</td>";
						echo "<td align='left'></td>";
						echo "<td align='left'>";
							echo "<input type='text' name='ppn' id='ppn' class='form-control text-right input-md' readonly placeholder='0' readonly value='".number_format($data_header[0]->nilai_ppn)."'>";
						echo "</td>";
					echo "</tr>";
					echo "<tr class='header_".$id."'>";
						echo "<td align='left' class='mid'><b class='mid'>SUB PEMBAYARAN</b></td>";
						echo "<td align='left'></td>";
						echo "<td align='left'></td>";
						echo "<td align='left'></td>";
						echo "<td align='left'>";
							echo "<input type='text' name='sub_payment' id='sub_payment' class='form-control text-right input-md' readonly placeholder='0' readonly value='".number_format($data_header[0]->total_payment_ppn)."'>";
						echo "</td>";
					echo "</tr>";
					echo "<tr class='header_".$id."'>";
						echo "<td align='right' class='mid'><b class='mid'>Cash/Bank</b></td>";
						echo "<td align='left' colspan='2'>";
							echo "<select name='coa' id='coa' class='chosen_select form-control input-sm'>";
							echo "<option value='0'>Select Cash/Bank</option>";
							foreach($data_coa AS $val => $valx){
								$sel = ($data_header[0]->coa_bank == $valx['no_perkiraan'])?'selected':'';
								echo "<option value='".$valx['no_perkiraan']."' ".$sel.">".strtoupper($valx['nama_perkiraan'])."</option>";
							}
							echo "</select>";
						echo "</td>";
						echo "<td align='left'></td>";
						echo "<td align='left'></td>";
					echo "</tr>";
				}
				?>
            </tbody>
        </table>
		<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class='text-center mid'>COA</th>
                    <th class='text-center mid' width='10%'>Debit</th>
                    <th class='text-center mid' width='10%'>Kredit</th>
					<th class='text-center mid' width='30%'>Keterangan</th>
					<th class='text-center mid' width='5%'>#</th>
				</tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				if(!empty($data_add)){
					foreach($data_add AS $val => $valx){
						echo "<tr class='header2_".$id."'>";
							echo "<td align='left'>";
								echo "<select name='detail_add[".$id."][post_coa]' data-no='".$id."' class='chosen_select form-control input-sm'>";
								echo "<option value='0'>Select COA</option>";
								foreach($data_coa AS $val2 => $valx2){
									$sel = ($valx['post_coa'] == $valx2['no_perkiraan'])?'selected':'';
									echo "<option value='".$valx2['no_perkiraan']."' ".$sel.">".strtoupper($valx2['nama_perkiraan'])."</option>";
								}
								echo "</select>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail_add[".$id."][debit]' id='debit_".$id."' value='".number_format($valx['debit'])."' class='form-control text-right input-md maskMoney debit' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail_add[".$id."][kredit]' id='kredit_".$id."' value='".number_format($valx['kredit'])."' class='form-control text-right input-md maskMoney kredit' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<input type='text' name='detail_add[".$id."][keterangan]' id='keterangan2_".$id."' value='".strtoupper($valx['keterangan'])."' class='form-control input-md' placeholder='Keterangan'>";
							echo "</td>";
							echo "<td align='center'>";
							if(empty($data_add)){
							echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
							}
							echo "</td>";
						echo "</tr>";
					}
				}
				if($data_header[0]->payment == 'N'){
				?>
				
                <tr id='add_<?=$id;?>'>
                    <td align='left' colspan='7'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>
                </tr>
				<?php } ?>
            </tbody>
        </table>
        <div class='box-footer'>
        <?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back','content'=>'Back'));
			
			if($data_header[0]->payment == 'N'){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'save'));
			}
		?>
        </div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

</form>
<?php $this->load->view('include/footer'); ?>
<style type="text/css">
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
	.datepicker{
		cursor: pointer;
	}
	.mid{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.chosen_select').chosen({width: '100%'});
		$('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd'
		});
		$('.tnd_reason').hide();
		
		$(document).on('change', '#status', function(e){
			var sts = $(this).val();
			if(sts == 'D'){
				$('.tnd_reason').show();
			}
			else{
				$('.tnd_reason').hide();
			}
		});
		
	});
	
	$(document).on('keyup', '.debit', function(e){
		var get_id 		= $(this).parent().parent().attr('class');
		var split_id	= get_id.split('_');
		var id 			= split_id[1];
		$('#kredit_'+id).val('');
	});
	
	$(document).on('keyup', '.kredit', function(e){
		var get_id 		= $(this).parent().parent().attr('class');
		var split_id	= get_id.split('_');
		var id 			= split_id[1];
		$('#debit_'+id).val('');
	});
		
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller +'/payment_request_rutin/payment';
	});
		
	$(document).on('click', '.addPart', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];

		$.ajax({
			url: base_url + active_controller+'/get_add_payment/'+id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskMoney').maskMoney();
				swal.close();
			},
			error: function(){
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
	});
	
	//delete part
	$(document).on('click', '.delPart', function(){
		var get_id 		= $(this).parent().parent().attr('class');
		$("."+get_id).remove();
	});

	//SAVE
	$(document).on('click', '#save', function(e){
		e.preventDefault();
		
		// $('#save').prop('disabled',true);
		
		var coa			= $('#coa').val();
		var sub_payment	= $('#sub_payment').val();
		
		if(coa == '0'){
			swal({
				title	: "Error Message!",
				text	: 'COA empty, select first ...',
				type	: "warning"
			});

			$('#save').prop('disabled',false);
			return false;
		}
		
		if(sub_payment < 1){
			swal({
				title	: "Error Message!",
				text	: 'Total bayar empty, input first ...',
				type	: "warning"
			});

			$('#save').prop('disabled',false);
			return false;
		}
		
		// alert('Save Process Developments'); return false;
		$('#save').prop('disabled',true);
		
		swal({
		  title: "Are you sure?",
		  text: "Save this data ?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Yes, Process it!",
		  cancelButtonText: "No, cancel process!",
		  closeOnConfirm: true,
		  closeOnCancel: false 
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData 	= new FormData($('#form_ct')[0]);
				var baseurl		= base_url + active_controller +'/payment_pembayaran_rutin';
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
							window.location.href = base_url + active_controller +'/payment_request_rutin/payment';
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 3000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save').prop('disabled',false);
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
						$('#save').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save').prop('disabled',false);
			return false;
			}
		});
	});
	
	$(document).on('keyup', '.payment, #ppn_val', function(){
		var ppn_type 	= $('#ppn_type').val();
		sum_payment(ppn_type);
	});
	$(document).on('change', '#ppn_type', function(){
		var ppn_type 	= $(this).val();
		sum_payment(ppn_type);
	});
	
	function sum_payment(ppn_type = null){
		
		var SUM = 0;
		let ppn_val = $('#ppn_val').val()
		$(".payment" ).each(function() {
			SUM += Number($(this).val().split(",").join(""));
		});
		
		if(ppn_type == 'ppn'){
			var ppnx = ppn_val/100;
			$('#ppn').val(number_format(SUM * ppnx));
		}
		if(ppn_type == 'non'){
			$('#ppn').val(0);
		}
		
		var ppn 		= getNum($('#ppn').val().split(",").join(""));
		
		$('#total_payment').val(number_format(SUM));
		$('#sub_payment').val(number_format(SUM + ppn));
	}

</script>
