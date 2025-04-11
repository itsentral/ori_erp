<?php
$this->load->view('include/side_menu');

?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data" autocomplete='off'> 
<input type='hidden' name='uri' id='uri' value='<?=$uri;?>'>
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
                    <th class='text-center mid' width='28%'>Post Pengeluaran</th>
                    <th class='text-center mid' width='22%'>Nama Barang/Jasa</th>
                    <th class='text-center mid' width='10%'>Waktu</th>
                    <th class='text-center mid' width='10%'>Jadwal Pembayaran</th>
                    <th class='text-center mid' width='10%'>Perkiraan Biaya</th>
					<th class='text-center mid' width='15%'>Baseline</th>
					<?php if(empty($view)){ ?>
					<th class='text-center mid' width='5%'>#</th>
					<?php } ?>
				</tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				if(!empty($data)){
					foreach($data AS $val2 => $valx2){ $id++;
						$bulan = ($valx2['type_bayar'] == 'bulan')?'block':'none';
						$tahun = ($valx2['type_bayar'] == 'tahun')?'block':'none';
						
						echo "<tr class='header_".$id."'>";
						echo "<td align='left'>";
							echo "<select name='detail[".$id."][post_coa]' data-no='".$id."' class='chosen_select form-control input-sm'>";
							foreach($datacoa AS $val => $valx){
								$sel = ($valx2['post_coa'] == $valx['coa'])?'selected':'';
								echo "<option value='".$valx['coa']."' ".$sel.">".strtoupper($valx['coa'])." - ".strtoupper($valx['nama'])."</option>";
							}
							echo "</select>";
						echo "</td>";
						echo "<td align='left'>";
							echo "<input type='hidden' name='detail[".$id."][id]' id='spec_".$id."' class='form-control input-md' value='".strtoupper($valx2['id'])."'>";
							echo "<input type='text' name='detail[".$id."][nama_barang]' id='spec_".$id."' class='form-control input-md' placeholder='Nama Barang/Jasa' value='".strtoupper($valx2['nama_barang'])."'>";
						echo "</td>";
						echo "<td align='left'>";
							echo "<select name='detail[".$id."][type_bayar]' id='type_bayar_".$id."' data-no='".$id."' class='chosen_select form-control input-sm chType'>";
							foreach($type_bayar AS $val => $valx){
								$sel = ($valx2['type_bayar'] == $valx['name'])?'selected':'';
								echo "<option value='".$valx['name']."'".$sel.">".strtoupper($valx['name'])."</option>";
							}
							echo "</select>";
						echo "</td>";
						echo "<td align='left'>";
							echo "<select name='detail[".$id."][jadwal_bayar_bulan]' id='jadwal_bayar_bulan_".$id."' class='chosen_select form-control input-sm'>";
							for($a=1;$a<=28;$a++){
								$sel = ($valx2['jadwal_bayar_bulan'] == $a)?'selected':'';
								echo "<option value='".$a."' ".$sel.">".strtoupper($a)."</option>";
							}
							echo "</select>";
							echo "<input type='text' name='detail[".$id."][jadwal_bayar_tahun]' style='display:".$tahun."' id='jadwal_bayar_tahun_".$id."' class='form-control text-center input-md datepicker' readonly placeholder='Select Date' value='".strtoupper($valx2['jadwal_bayar_tahun'])."'>";
						echo "</td>";
						echo "<td align='left'>";
							echo "<input type='text' name='detail[".$id."][biaya]' class='form-control text-right input-md maskMoney' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' value='".number_format($valx2['biaya'])."'>";
						echo "</td>";
						echo "<td align='left'>";
							echo "<input type='text' name='detail[".$id."][baseline]' id='baseline_".$id."' class='form-control input-md' placeholder='Baseline'  value='".strtoupper($valx2['baseline'])."'>";
						echo "</td>";
						if(empty($view)){
						echo "<td align='center'>";
						echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPartPermanen' data-id='".$valx2['id']."' title='Delete Part'><i class='fa fa-close'></i></button>";
						echo "</td>";
						}
					echo "</tr>";
					}
				}
				?>
				<?php if(empty($view)){ ?>
                <tr id='add_<?=$id;?>'>
                    <td align='left' colspan='7'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>
                </tr>
				<?php } ?>
            </tbody>
        </table>
        <div class='box-footer'>
        <?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back','content'=>'Back'));
            
			if(empty($view)){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'save')).' ';
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
</style>
<script>
	$(document).ready(function(){
		$('.chosen_select').chosen({width: '100%'});
		var angka = $('#angka').val();
		var no;
		for(no=1;no<=angka;no++){
			if($('#type_bayar_'+no).val() == 'bulan'){
				$('#jadwal_bayar_tahun_'+no).hide();
				$('#jadwal_bayar_bulan_'+no+'_chosen').show();
			}
			else{
				$('#jadwal_bayar_tahun_'+no).show();
				$('#jadwal_bayar_bulan_'+no+'_chosen').hide();
			}
		}
	});
	
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller +'/master_rutin';
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
		
		// var id_costcenter	= $('#id_costcenter').val();
		// if(id_costcenter == '0'){
			// swal({
				// title	: "Error Message!",
				// text	: 'Costcenter name empty, select first ...',
				// type	: "warning"
			// });

			// $('#save').prop('disabled',false);
			// return false;
		// }
		
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
				var baseurl		= base_url + active_controller +'/add_master_pembayaran_rutin';
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
							window.location.href = base_url + active_controller +'/master_rutin';
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
