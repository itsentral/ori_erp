
<div class="box-body"> 
	<div class='note hidden'>
		<p>
			<strong>Info!</strong><br> 
			Kurs sesuai <?=$data_rfq[0]->no_rfq;?>, yang diinput di table perbandingan<br>
			<span style='color:green;font-size: 18px;'><b>1 USD = <?=number_format($data_rfq[0]->kurs,2);?> IDR</b></span><br>
		</p>
	</div>
	<br>
	<input type='hidden' name='no_po' value='<?=$data[0]->no_po;?>'>
	<div class='form-group row'>
		<label class='label-control col-sm-1'><b>Supplier</b></label>
		<div class='col-sm-5'>
			<select id='id_supplier' name='id_supplier' class='form-control input-sm chosen_select'>
				<?php
				foreach($listSupplier AS $val => $valx){
					if($valx['id_supplier'] == $data[0]->id_supplier){ 
					$sel = ($valx['id_supplier'] == $data[0]->id_supplier)?'selected':'';
					echo "<option value='".$valx['id_supplier']."' ".$sel.">".strtoupper($valx['nm_supplier'])."</option>";
					}
				}
				?>
			</select>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'><b>Incoterms</b></label>
		<div class='col-sm-3'>
			<?php
			 echo form_input(array('id'=>'incoterms','name'=>'incoterms','class'=>'form-control input-md','placeholder'=>'Incoterms'), strtoupper($data[0]->incoterms));
			?>
		</div>
		<label class='label-control col-sm-1'><b>Request Date</b></label>
		<div class='col-sm-3'>
			<?php
			 echo form_input(array('id'=>'request_date','name'=>'request_date','class'=>'form-control input-md datepicker','placeholder'=>'Request Date','readonly'=>'readonly'), strtoupper($data[0]->tgl_dibutuhkan));
			?>
		</div>
		<label class='label-control col-sm-1'><b>Currency</b></label>
		<div class='col-sm-3'>
			<select id='current1' name='current1' class='form-control input-sm chosen_select' disabled>
				<?php
				$kurs_mata_uang = (!empty($data[0]->mata_uang))?$data[0]->mata_uang:$data_rfq[0]->currency;
				foreach(get_list_kurs2() AS $val => $valx){
					$sel = ($valx['kode_dari'] == $kurs_mata_uang)?'selected':'';
					echo "<option value='".$valx['kode_dari']."' ".$sel.">".$valx['kode_dari']." - ".strtoupper($valx['negara'])."</option>";
				}
				?>
			</select>
			<input type="hidden" name="current" value='<?=$kurs_mata_uang?>''>
			<input type='hidden' id='kurs' value='<?=$data_rfq[0]->kurs;?>'>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'><b>Term Of Payment</b></label>
		<div class='col-sm-3'>
		<?php
			 echo form_input(array('id'=>'top','name'=>'top','class'=>'form-control input-md','placeholder'=>'Term Of Payment'), strtoupper($data[0]->top));
			?>
		</div>
		<label class='label-control col-sm-1'><b>Remarks</b></label>
		<div class='col-sm-3'>
			<?php
			 echo form_textarea(array('id'=>'remarks','name'=>'remarks','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Remarks'), strtoupper($data[0]->remarks));
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'><b>Buyer</b></label>
		<div class='col-sm-3'>
			<?php
			$buyer = (!empty($data[0]->buyer))?strtoupper($data[0]->buyer):strtoupper(get_name('users','nm_lengkap','username',$data[0]->updated_by));
			 echo form_input(array('id'=>'buyer','name'=>'buyer','class'=>'form-control input-md','placeholder'=>'Buyer',
        'readonly' => 'readonly'), $buyer);
			?>
		</div>
		<label class='label-control col-sm-1'><b>Currency Amount Word</b></label>
		<div class='col-sm-3'>
			<?php
			echo form_input(array('id'=>'amount_words','name'=>'amount_words','class'=>'form-control input-md','placeholder'=>'Currency Amount Word'), $data[0]->amount_words);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'>Detail Barang</label>
		<div class='col-sm-11'>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center">Nama Barang</th>
						<th class="text-center" width='10%'>Qty</th>
						<th class="text-center" width='20%'>Price/Unit</th>
						<th class="text-center" width='20%'>Total Price</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$jumlah = count($result);
					$no  = 0;
					$SUM = 0;
					foreach($result AS $val => $valx){ $no++;
						$qty_p = (!empty($valx['qty_po']))?$valx['qty_po']:$valx['qty_purchase'];
						$SUM += $qty_p * $valx['price_ref_sup'];
						echo "<tr>";
							echo "<td align='left'>
									<input name='detail[".$no."][nm_material]' id='nm_material_".$no."' class='form-control input-md ' value='".strtoupper($valx['nm_material'])."' readonly >
									<input type='hidden' name='detail[".$no."][id]' id='id_".$no."' value='".$valx['id']."'>
									
									</td>";
							echo "<td align='center'><input type='hidden' name='detail[".$no."][qty]' id='qty_".$no."' class='form-control text-right input-md maskM ch_qty' value='".$qty_p."' readonly tabindex='-1'>".$qty_p."</td>";
							echo "<td align='right'><input type='hidden' name='detail[".$no."][price]' id='price_".$no."' class='form-control text-right input-sm maskM ch_qty changePrice' value='".$valx['price_ref_sup']."'>".number_format($valx['price_ref_sup'],2)."</td>";
							echo "<td align='right'><input type='text' name='detail[".$no."][totprice]' id='qtytot_".$no."' class='form-control sum_tot text-right input-sm maskM' value='".$qty_p * $valx['price_ref_sup']."' readonly></td>";
						echo "</tr>";
					}
					/*
					echo "<tr>";
						echo "<td align='left' colspan='2'></td>";
						echo "<td align='right'><b>TOTAL PRICE</b></td>";
						echo "<td align='right'><b><div id='total'>".number_format($SUM,2)."</div></b></td>";
					echo "</tr>";
					*/
					?>
					<tr>
                        <td colspan='2'></td>
                        <td class='text-right mid' width='25%'><b>TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'><input type="text" id='total_po'  name='total_po' class='form-control input-sm text-right text-bold autoNumeric' placeholder='Total' readonly value='<?=$SUM;?>'></td>
                    </tr>
                    <tr hidden>
                        <td colspan='2'></td>
                        <td class='text-right mid'><b>DISCOUNT (%)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'><input type="text" id='discount' name='discount' class='form-control input-sm text-right text-bold autoNumeric' placeholder='Discount (%)' value='<?=$data[0]->discount;?>'></td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                        <td class='text-right mid'><b>NET PRICE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'><input type="text" id='net_price' name='net_price' class='form-control input-sm text-right text-bold autoNumeric' readonly placeholder='Net Price'  value='<?=$data[0]->net_price;?>'></td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                        <td class='text-right mid'><b>TAX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'>
                            <select name="tax" id="tax" class='form-control chosen-select'>
                                <?php
                                foreach ($listPPN as $key => $value) {
									$selected = ($value['data1'] == $data[0]->tax)?'selected':'';
                                    echo "<option value='".$value['data1']."' ".$selected.">".$value['name']."</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                        <td class='text-right mid'><b>NET PRICE + TAX&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'><input type="text" id='net_plus_tax' name='net_plus_tax' class='form-control input-sm text-right text-bold autoNumeric' readonly placeholder='Net Price + Tax' value='<?=$data[0]->net_plus_tax;?>'></td>
                    </tr>
                    <tr hidden>
                        <td colspan='2'></td>
                        <td class='text-right mid'><b>DELIVERY COST&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'><input type="text" id='delivery_cost' name='delivery_cost' class='form-control input-sm text-right text-bold autoNumeric' placeholder='Delivery Cost' value='<?=$data[0]->delivery_cost;?>'></td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                        <td class='text-right mid'><b>GRAND TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
                        <td class='mid'><input type="text" id='grand_total' name='grand_total' class='form-control input-sm text-right text-bold autoNumeric' readonly placeholder='Grand Total' value='<?=$data[0]->total_price;?>'></td>
                    </tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'>TOP</label>
		<div class='col-sm-11'>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" width='5%'>Group TOP</th>
						<th class="text-center" width='8%'>Percentase (%)</th>
						<th class="text-center hidden" width='11%'>Value(USD)</th>
						<th class="text-center" width='11%'>Value  <!--(IDR)--></th>
						<th class="text-center" width='25%'>Keterangan</th>
						<th class="text-center" width='10%'>Est Jatuh Tempo</th>
						<th class="text-center" width='25%'>Persyaratan</th>
						<th class="text-center" width='5%'>#</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$id = 0;
					if(!empty($data_top)){
						foreach($data_top AS $val => $valx){ $id++;
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_po[".$id."][group_top]' class='form-control text-left chosen_select' value='".$id."'>";
										echo "<option value='0'>Select Group TOP</option>";
										foreach($payment AS $val2 => $valx2){
											$sel = ($valx2['name'] == $valx['group_top'])?'selected':'';
											echo "<option value='".$valx2['name']."' ".$sel.">".strtoupper($valx2['name'])."</option>";
										}
									echo "</select>";
									echo "<input type='hidden' name='detail_po[".$id."][term]' class='form-control text-center input-md' value='".$valx['term']."'>"; 
								echo "</td>";
								echo "<td align='left'><input type='text' id='progress_".$id."' name='detail_po[".$id."][progress]' value='".number_format($valx['progress'])."' class='form-control input-md text-center maskM progress_term' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
								echo "<td align='left' class='hidden'><input type='text' id='usd_".$id."' name='detail_po[".$id."][value_usd]' value='".number_format($valx['value_usd'],2)."' class='form-control input-md text-right maskM sum_tot_usd' tabindex='-1' readonly></td>";
								echo "<td align='left'><input type='text' id='idr_".$id."' name='detail_po[".$id."][value_idr]' value='".number_format($valx['value_idr'])."' class='form-control input-md text-right maskM sum_tot_idr' tabindex='-1' readonly></td>";
								echo "<td align='left'><input type='text' id='total_harga_".$id."' name='detail_po[".$id."][keterangan]' value='".strtoupper($valx['keterangan'])."' class='form-control input-md text-left'></td>";
								echo "<td align='left'><input type='text' name='detail_po[".$id."][jatuh_tempo]' value='".$valx['jatuh_tempo']."' class='form-control input-md text-center datepicker' readonly></td>";
								echo "<td align='left'><input type='text' name='detail_po[".$id."][syarat]' value='".strtoupper($valx['syarat'])."' class='form-control input-md'></td>";
								echo "<td align='center'>";
								echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
							echo "</tr>";
						}
					}
					?>
					<tr id='add_<?=$id;?>'>
						<td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add TOP'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add TOP</button></td>
						<td align='center' colspan='7'></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-1'></label>
		<div class='col-sm-11'><div id='alert-max' style="font-size: 17px;font-weight: bold;color: red;padding-bottom: 10px;">PROGRESS MELEBIHI 100% !!</div>
			<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'edit_po'));
			?>
		</div>
	</div>
</div>
<style>
	.datepicker{
		cursor: pointer;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}

</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.datepicker').datepicker();
		$('.maskM').autoNumeric('init', {mDec: '2', aPad: false});
		$(".autoNumeric").autoNumeric('init', {mDec: '2', aPad: false});
		$('#alert-max').hide();
		$('.chosen_select').chosen();
		var kurs = $('#current').val();
	});

	$(document).on('change', '#current', function(){
		var kurs = $('#current').val();
		$.ajax({
			url: base_url+'pembelian/get_kurs/'+kurs,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
//	revisi agus kurs=1
//			  $('#kurs').val(data.kurs);
			  $('#kurs').val(1);
			  change_kurs();
			  swal.close();
			},
			error: function() {
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
	
	$(document).on('keyup', '.progress_term', function(){
		var id 		= $(this).attr('id');
		var det_id	= id.split('_');
		var a		= det_id[1];
		term_process(a);
		
		var progress = 0;
		$(".progress_term" ).each(function() {
			progress 	+= getNum($(this).val().split(",").join(""));
		});
		
		if(progress > 100){
			$('#edit_po').hide();
			$('#alert-max').show();
		}
		else{
			$('#edit_po').show();
			$('#alert-max').hide();
		}
	});
	
	$(document).on('click', '.addPart', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		// console.log(get_id);
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];

		$.ajax({
			url: base_url +'pembelian/get_add/'+id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskM').maskMoney();
				$('.datepicker').datepicker({
					dateFormat : 'yy-mm-dd',
					minDate: 0
				});
				swal.close();
			},
			error: function() {
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
		
		var progress = 0;
		$(".progress_term" ).each(function() {
			progress 	+= getNum($(this).val().split(",").join(""));
		});
		
		if(progress > 100){
			$('#edit_po').hide();
			$('#alert-max').show();
		}
		else{
			$('#edit_po').show();
			$('#alert-max').hide();
		}
	});
	
	$(document).on('keyup', '#discount, #delivery_cost, .ch_qty', function(){
        sumTotal();
	});

    $(document).on('change', '#tax', function(){
        sumTotal();
	});

	let sumTotal = () => {
        let discount        =  getNum($('#discount').val().split(",").join(""))
        let tax             =  getNum($('#tax').val().split(",").join(""))
        let delivery_cost   =  getNum($('#delivery_cost').val().split(",").join(""))

		let sum_total = 0
        let total
		let id
		let det_id
		let a
		let qtyPurchase
		let pricePurchase
		let totalPurchase
        $(".changePrice" ).each(function() {
			id 		= $(this).attr('id');
			det_id	= id.split('_');
			a		= det_id[1];
			qtyPurchase 	= Number(getNum($('#qty_'+a).val().split(",").join("")));
			pricePurchase 	= Number(getNum($('#price_'+a).val().split(",").join("")));
			totalPurchase	= qtyPurchase * pricePurchase;

			$('#qtytot_'+a).val(number_format(totalPurchase,2))
			console.log(totalPurchase)
			sum_total 		+= totalPurchase;
		});
		// console.log(sum_total)

        $('#total_po').val(number_format(sum_total,2))
        let net_price = sum_total - (sum_total * discount / 100)
        $('#net_price').val(number_format(net_price,2))
        let net_plus_tax = net_price + (net_price * tax / 100)
        $('#net_plus_tax').val(number_format(net_plus_tax,2))
        let grand_total = net_plus_tax + delivery_cost
        $('#grand_total').val(number_format(grand_total,2))

		change_kurs2();
	}
	
	function term_process(a){
		var total		= getNum($('#grand_total').val().split(",").join(""));
		var progress 	= getNum($('#progress_'+a).val().split(",").join("")); 
		var kurs		= getNum($('#kurs').val().split(",").join(""));


		console.log(total);
		

		var current  	= $('#current').val();
		
		if(current == 'USD'){
			var tot_usd 	= (progress/100) * total;
			var tot_idr 	= (progress/100) * (total * kurs);
		}
		if(current == 'IDR'){
			var tot_idr 	= (progress/100) * total;
			var tot_usd 	= (progress/100) * (total * kurs);
		}
		
		$('#usd_'+a).val(number_format(tot_usd,2));
		$('#idr_'+a).val(number_format(tot_idr,2));
	}
	
	function change_kurs(){
		var total		= getNum($('#grand_total').val().split(",").join(""));
		var kurs		= getNum($('#kurs').val().split(",").join(""));
		var current  	= $('#current').val();
		// alert(current);
		$(".progress_term" ).each(function() {
			var id 		= $(this).attr('id');
			var det_id	= id.split('_');
			var a		= det_id[1];
			
			var progress 	= getNum($('#progress_'+a).val().split(",").join(""));
			// console.log(progress);
			if(current == 'IDR'){
				var tot_idr 	= (progress/100) * total;
				var tot_usd 	= (progress/100) * (total * kurs);
			}
			
			if(current == 'USD'){
				var tot_usd 	= (progress/100) * total;
				var tot_idr 	= (progress/100) * (total * kurs);
			}
			
			$('#usd_'+a).val(number_format(tot_usd,2));
			$('#idr_'+a).val(number_format(tot_idr));
		});
	}
	
	function change_kurs2(){
		var total		= getNum($('#grand_total').val().split(",").join(""));
		var kurs		= getNum($('#kurs').val().split(",").join(""));
		var current  	= $('#current').val();
		// alert(current);
		$(".progress_term" ).each(function() {
			var id 		= $(this).attr('id');
			var det_id	= id.split('_');
			var a		= det_id[1];
			
			var progress 	= getNum($('#progress_'+a).val().split(",").join(""));
			// console.log(progress);
			if(current == 'USD'){
				var tot_usd 	= (progress/100) * total;
				var tot_idr 	= (progress/100) * (total * kurs);
			}
			
			if(current == 'IDR'){
				var tot_idr 	= (progress/100) * total;
				var tot_usd 	= (progress/100) * (total * kurs);
			}
			
			$('#usd_'+a).val(number_format(tot_usd,2));
			$('#idr_'+a).val(number_format(tot_idr));
		});
	}
	
	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}
	
	function number_format (number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	}
</script>