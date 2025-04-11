<?php
$this->load->view('include/side_menu');

?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data" autocomplete='off'> 
<input type='hidden' name='uri' id='uri' value='<?=$uri;?>'>
<input type='hidden' name='tanda' id='tanda' value='<?=$tanda;?>'>
<input type='hidden' name='approve' id='approve' value='<?=$approve;?>'>
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
		<?php
		if($approve == 'approve'){
		?>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Approve <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>
					<select name='status' id='status' class='form-control input-md'>
						<option value='0'>Select Approve</option>
						<option value='Y'>Approve</option>
						<option value='D'>Reject</option>
					</select>
				</div>
				<div class='col-sm-2'>
					
				</div>
				<label class='label-control col-sm-2 tnd_reason'><b>Reason  <span class='text-red'>*</span></b></label>
				<div class='col-sm-4 tnd_reason'>
					<?php
						echo form_textarea(array('id'=>'reason','name'=>'reason','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Reason'));
					?>
				</div>
			</div>
		<?php
		}
		?>
        <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class='text-center mid' width='22%'>Nama Barang/Jasa</th>
                    <th class='text-center mid' width='10%'>Jadwal Pembayaran</th>
                    <th class='text-center mid' width='10%'>Perkiraan Biaya</th>
					<th class='text-center mid' width='15%'>Keterangan</th>
					<?php if(empty($view)){ ?>
					<th class='text-center mid' width='5%'>#</th>
					<?php } ?>
				</tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				// print_r($data);
				if(!empty($data)){
					foreach($data AS $val2 => $valx2){ $id++;
						if($tanda != 'RPY'){
							$tgl_bayar = date('Y-m').'-'.$valx2['jadwal_bayar_bulan'];
							if($valx2['tipe'] == 'tahun'){
								$tgl_bayar = date('Y').'-'.date('m-d',strtotime($valx2['jadwal_bayar_tahun']));
							}
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<input type='hidden' name='detail[".$id."][id_budget]' class='form-control input-md' value='".strtoupper($valx2['id'])."'>";
									echo "<input type='text' name='detail[".$id."][nama_barang]' id='nama_barang_".$id."' class='form-control input-md' placeholder='Nama Barang/Jasa' value='".strtoupper($valx2['nama'])."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input type='text' name='detail[".$id."][jadwal_bayar]' id='jadwal_bayar_".$id."' class='form-control text-center input-md datepicker' readonly placeholder='Select Date' value='".$tgl_bayar."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input type='text' name='detail[".$id."][biaya]' class='form-control text-right input-md maskMoney' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' value='".number_format($valx2['nilai'])."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input type='text' name='detail[".$id."][keterangan]' id='keterangan_".$id."' class='form-control input-md' placeholder='Keterangan'  value='".strtoupper($valx2['keterangan'])."'>";
								echo "</td>";
								if(empty($view)){
								echo "<td align='center'>";
								echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' data-id='".$valx2['id']."' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
								}
							echo "</tr>";
						}
						if($tanda == 'RPY'){
							$tgl_bayar = $valx2['jadwal_bayar'];
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<input type='hidden' name='detail[".$id."][id]' class='form-control input-md' value='".strtoupper($valx2['id'])."'>";
									echo "<input type='hidden' name='detail[".$id."][id_budget]' class='form-control input-md' value='".strtoupper($valx2['id_budget'])."'>";
									echo "<input type='text' name='detail[".$id."][nama_barang]' id='nama_barang_".$id."' class='form-control input-md' placeholder='Nama Barang/Jasa' value='".strtoupper($valx2['nama_barang'])."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input type='text' name='detail[".$id."][jadwal_bayar]' id='jadwal_bayar_".$id."' class='form-control text-center input-md datepicker' readonly placeholder='Select Date' value='".$tgl_bayar."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input type='text' name='detail[".$id."][biaya]' class='form-control text-right input-md maskMoney' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' value='".number_format($valx2['nilai_bayar'])."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input type='text' name='detail[".$id."][keterangan]' id='keterangan_".$id."' class='form-control input-md' placeholder='Keterangan'  value='".strtoupper($valx2['keterangan'])."'>";
								echo "</td>";
								if(empty($view)){
								echo "<td align='center'>";
								echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' data-id='".$valx2['id']."' title='Delete Part' disabled><i class='fa fa-close'></i></button>";
								echo "</td>";
								}
							echo "</tr>";
						}
					}
				}
				?>
            </tbody>
        </table>
        <div class='box-footer'>
        <?php
			if($approve != 'approve'){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back','content'=>'Back'));
            }
			
			if($approve == 'approve'){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back2','content'=>'Back'));
            }
			
			if(empty($view) OR $approve == 'approve'){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'save'));
            }
			// if($approve == 'approve'){
				// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'approve'));
            // }
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
	
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller +'/payment_request_rutin';
	});
	
	$(document).on('click', '#back2', function(e){
		window.location.href = base_url + active_controller +'/payment_request_rutin/approve';
	});
	
	$(document).on('change', '.chType', function(e){
		var valuex = $(this).val();
		var no = $(this).data('no');
		
		if(valuex == 'bulan'){
			$('#jadwal_bayar_tahun_'+no).hide();
			$('#jadwal_bayar_bulan_'+no+'_chosen').show();
		}
		else{
			$('#jadwal_bayar_tahun_'+no).show();
			$('#jadwal_bayar_bulan_'+no+'_chosen').hide();
		}
	});
		
	$(document).on('click', '.addPart', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		var dept = $('#uri').val();
		// console.log(get_id);
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];

		$.ajax({
			url: base_url + active_controller+'/get_add/'+id,
			cache: false,
			type: "POST",
			data: {
				"dept" 	: dept
			},
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskMoney').maskMoney();
				$('.datepicker').datepicker({
					dateFormat : 'yy-mm-dd'
				});
				$('#jadwal_bayar_tahun_'+data.id).hide();
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
	
	$(document).on('click', '.delPartPermanen', function(e){
		e.preventDefault();
		var id 				= $(this).data('id');
		var uri 			= $('#uri').val();
		
		swal({
		  title: "Are you sure?",
		  text: "Delete permanent this data ?",
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
				$.ajax({
					url: base_url + active_controller+'/delete_permanent/'+id+'/'+uri,
					type: "POST",
					// data: function(d){
						// d.id 			= id,
						// d.uri			= uri
					// },
					dataType: "json",
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
							window.location.href = base_url + active_controller +'/add_master_pembayaran_rutin/'+data.uri;
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
		} 
		else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save').prop('disabled',false);
			return false;
			}
		});
	});

	//SAVE
	$(document).on('click', '#save', function(e){
		e.preventDefault();
		// alert('Tahan'); return false;
		// $('#save').prop('disabled',true);
		
		var approve	= $('#approve').val();
		if(approve != ''){
			var status	= $('#status').val();
			if(status == '0'){
				swal({
					title	: "Error Message!",
					text	: 'Status empty, select first ...',
					type	: "warning"
				});

				$('#save').prop('disabled',false);
				return false;
			}
		}
		
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
				var baseurl		= base_url + active_controller +'/add_request_pembayaran_rutin';
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
							window.location.href = base_url + active_controller +'/payment_request_rutin/'+data.tanda;
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

</script>
