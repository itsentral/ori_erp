<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary" style='margin-right: 17px;'>
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<button type='button' class='btn btn-md btn-success' id='add_currency'>Add Currency</button>
		</div>
	</div>
	<div class="box-body">
		<input type='hidden' name='no_rfq' class='form-control input-md' value='<?=$this->uri->segment(3);?>'>
		<?php
		$no = 0;
		foreach($result AS $val => $valx){ $no++;
			$flag 		= get_name('supplier', 'id_negara', 'id_supplier', $valx['id_supplier']);
			$sel_local 	= ($flag == 'IDN')?'selected':'';
			$sel_import = ($flag <> 'IDN')?'selected':'';
			
			if(!empty($valx['lokasi'])){
				$sel_local 	= ($valx['lokasi'] == 'local')?'selected':'';
				$sel_import = ($valx['lokasi'] == 'import')?'selected':'';
			}

			$sel_usd = ($valx['currency'] == 'USD')?'selected':'';
			$sel_idr = ($valx['currency'] == 'IDR')?'selected':'';
			
			$alamatSUP = (!empty($valx['alamat_supplier']))?$valx['alamat_supplier']:get_name('supplier', 'alamat', 'id_supplier', $valx['id_supplier']);
			
			$query 	= "	SELECT 
							a.*, 
							b.price_ref_purchase,
							b.price_from_supplier,
							c.tanggal
						FROM 
							tran_material_rfq_detail a 
							LEFT JOIN raw_materials b ON a.id_material = b.id_material 
							LEFT JOIN tran_material_pr_detail c ON a.no_rfq = c.no_rfq 
						WHERE 
							a.hub_rfq='".$valx['hub_rfq']."'
							AND a.id_material = c.id_material
						GROUP BY a.id_material ORDER BY a.id DESC
							";
			$res 	= $this->db->query($query)->result_array();
			?>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-6'><b style='font-size: 16px;'><?=$no.'. '.$valx['nm_supplier'];?></b></label>
			</div>
			<div class='form-group row'>		 	 
				<div class='col-sm-2'>
					<select id='lokasi_<?=$no;?>' name='Header[<?=$no;?>][lokasi]' class='form-control input-md chosen-select'>
						<option value='local' <?=$sel_local;?>>LOCAL</option>
						<option value='import' <?=$sel_import;?>>IMPORT</option>
					</select>
				</div>
				<div class='col-sm-4'></div>
				<div class='col-sm-1'><label>Currency</label></div>
				<div class='col-sm-2'>
					<select id='currency_<?=$no;?>' name='Header[<?=$no;?>][currency]' class='form-control input-md chosen-select changeCurrency' data-no='<?=$no;?>'>
						<?php
						foreach ($currency as $key => $value) {
							$selected = ($valx['currency'] == $value['kode'])?'selected':'';
							echo "<option value='".$value['kode']."' ".$selected.">".$value['kode']."</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<div class='col-sm-6'>
					<textarea id='alamat_<?=$no;?>' class='form-control input-md' name='Header[<?=$no;?>][alamat]' rows='3' placeholder='Supplier Address'><?=strtoupper($alamatSUP);?></textarea>
					<input type='hidden' name='Header[<?=$no;?>][id]' class='form-control input-md' value='<?=$valx['id'];?>'>
				</div>
				<div class='col-sm-1 hidden'><label>Kurs</label></div>
				<div class='col-sm-2 hidden'>
					<input type='text' id='kurs_<?=$no;?>' name='Header[<?=$no;?>][kurs]' class='form-control input-md autoNumeric2 changeKurs' data-no='<?=$no;?>' value='<?=($valx['kurs']==""?1:$valx['kurs']);?>'>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<div class='col-sm-12'>
					<table class="table table-bordered table-striped" width='100%'>
						<thead>
							<tr class='bg-blue'>
								<th class="text-center mid">Material Name</th>
								<th class="text-center mid" width='7%'>Price Ref</th>
								<th class="text-center mid" width='10%'>Price From Supplier</th>
								<!--<th class="text-center mid hidden" width='10%'>Harga (IDR)</th>-->
								<th class="text-center mid" width='7%'>Qty PR</th>
								<th class="text-center mid" width='7%'>Unit</th>
								<th class="text-center mid" width='7%'>MOQ (Kg)</th>
								<th class="text-center mid" width='7%'>Lead Time (Day)</th>
								<th class="text-center mid" width='10%'>Tanggal Dibutuhkan</th>
								<th class="text-center mid" width='12%'>Total Harga</th>
							</tr>
						</thead>
					<tbody>
						<?php
						$no2 = 0;
						$SUM_HARGA = 0;
						foreach($res AS $val2 => $valx2){ $no2++;
							$nm_material = $valx2['nm_material'];
							$satuan = 'KG';
							if($valx2['category'] == 'acc'){
								$nm_material = get_name_acc($valx2['id_material']);
								$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$valx2['idmaterial']);
								if(empty($valx2['idmaterial'])){
									$nm_material = $valx2['nm_material'];
								}
							}
							echo "<tr>";
								echo "<td class='mid'>".$nm_material."</td>";
								echo "<td class='mid' align='right'>".number_format($valx2['price_from_supplier'],2)."</td>";
								echo "<td class='mid' align='right'><input type='text' name='Detail[".$no."][detail][".$no2."][price_ref_sup]' class='form-control text-center input-md autoNumeric2 price_sub_".$no." changeKurs' value='".$valx2['price_ref_sup']."' data-no='".$no."'></td>";
								echo "<td class='mid hidden' align='right'><input type='text' name='Detail[".$no."][detail][".$no2."][harga_idr]' class='form-control text-right input-md autoNumeric2 harga_idr' readonly value='".$valx2['harga_idr']."'></td>";
								echo "<td class='mid' align='center'><input type='text' name='Detail[".$no."][detail][".$no2."][qty]' class='form-control text-center input-md autoNumeric2 qty_pr' value='".$valx2['qty']."' data-no='".$no."'></td>";
								echo "<td class='mid' align='left'>";
								echo "<select name='Detail[".$no."][detail][".$no2."][satuan]' class='form-control input-md'>";
								foreach ($list_satuan as $key => $value) {
									$selected = ($value['id_satuan'] == $valx2['satuan'])?'selected':'';
									echo "<option value='".$value['id_satuan']."' ".$selected.">".strtoupper($value['kode_satuan'])."</option>";
								}
								echo "</select>";
								echo "</td>";
								echo "<td class='mid' align='right'><input type='text' name='Detail[".$no."][detail][".$no2."][moq]' class='form-control text-center input-md autoNumeric2' value='".$valx2['moq']."'></td>";
								echo "<td class='mid' align='right'>
										<input type='text' name='Detail[".$no."][detail][".$no2."][lead_time]' class='form-control text-center input-md autoNumeric2' value='".$valx2['lead_time']."'>
										<input type='hidden' name='Detail[".$no."][detail][".$no2."][id]' class='form-control input-md' value='".$valx2['id']."'>
										<input type='hidden' name='Detail[".$no."][detail][".$no2."][price_ref]' class='form-control input-md' value='".$valx2['price_ref_purchase']."'>
										<input type='hidden' name='Detail[".$no."][detail][".$no2."][tgl_dibutuhkan]' class='form-control input-md' value='".$valx2['tanggal']."'>
										</td>";
								echo "<td class='mid' align='center'>".date('d-M-Y', strtotime($valx2['tanggal']))."</td>";
								echo "<td class='mid' align='right'><input type='text' name='Detail[".$no."][detail][".$no2."][total_harga]' class='form-control text-right input-md autoNumeric2 tot_harga_idr' value='".$valx2['total_harga']."'readonly></td>";
							echo "</tr>";

							$SUM_HARGA += $valx2['total_harga'];
						}
						echo "<tr>";
							echo "<td colspan='8' class='text-right text-bold mid'>TOTAL PRICE</td>";
							echo "<td class='mid' align='right'><input type='text' class='form-control text-right input-md autoNumeric2 sum_harga_idr_".$no."' value='".$SUM_HARGA."' readonly></td>";
						echo "</tr>";
						?>
					</tbody>
				</table>
				</div>
			</div>
		<?php } ?>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-danger','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Back','id'=>'back')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 5px 5px 0px;','value'=>'Create','content'=>'Save','id'=>'save')).' ';
		?>
	</div>
 </div>
  <!-- /.box -->

  <!-- modal -->
  <div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:50%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title2"></h4>
					</div>
					<div class="modal-body" id="view2">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->

</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.mid{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$(".autoNumeric2").autoNumeric('init', {mDec: '2', aPad: false});
	});

	$(document).on('click', '#add_currency', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>ADD CURRENCY</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_add_currency',
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);
				swal.close()
			},
			error: function() {
				swal({
				  title	: "Error Message !",
				  text	: 'Connection Timed Out ...',
				  type	: "warning",
				  timer	: 5000
				});
			}
		});
	});

	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller+'/perbandingan';
	});

	$(document).on('click', '#save', function(e){
		e.preventDefault();

		swal({ 
			title: "Are you sure?",
			text: "You will save be able to process again this data!",
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
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/add_perbandingan',
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
							window.location.href = base_url + active_controller+'/perbandingan';
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

	$(document).on('change', '.changeCurrency', function(){
		var no_ke = $(this).data('no');
		changeKurs(no_ke);
	});

	$(document).on('keyup', '.changeKurs, .qty_pr', function(){
		var no_ke = $(this).data('no');
		changeKurs(no_ke);
	});

	let changeKurs = (no_ke) => {
		let currency 	= $('#currency_'+no_ke).val();
		let kurs 		= getNum($('#kurs_'+no_ke).val().split(",").join(""));
		// console.log(currency)
		// console.log(kurs)
		// console.log(no_ke)
		let price_sub
		let harga
		let tot_harga
		let qty_pr
		let sum_total = 0
		$(".price_sub_"+no_ke).each(function() {
        	price_sub = getNum($(this).val().split(",").join(""));
			qty_pr = getNum($(this).parent().parent().find('.qty_pr').val().split(",").join(""));
			// console.log(price_sub)
			// console.log(qty_pr)
			harga = price_sub
			if(currency == 'USD'){
				harga = price_sub * kurs
			}
			
			tot_harga = harga * qty_pr
			$(this).parent().parent().find('.harga_idr').val((harga))
			$(this).parent().parent().find('.tot_harga_idr').val((tot_harga))
			
			sum_total += Number(tot_harga);
 		});
		$('.sum_harga_idr_'+no_ke).val((sum_total))
	}

	$(document).on('click', '#save_currency', function(e){
		e.preventDefault();

		swal({ 
			title: "Are you sure?",
			text: "You will save be able to process again this data!",
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
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/modal_add_currency',
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
							window.location.href = base_url + active_controller+'/perbandingan';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000,
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

</script>
