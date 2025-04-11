<?php
if($restChkSO < 1){
	?>
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			IPP ini tidak dapat dilakukan approve, please update data.<br>
		</p>
	</div>
	<?php
}
else{
?>
<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			<b>PRODUCT KOSONG HARUS DIAJUKAN !!!</b><br>
		</p>
	</div>
	<div class="form-group row">
		<?php
		echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
		echo form_input(array('type'=>'hidden','id'=>'numR'),$NumBaris);
		?>
	</div>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%' class='no-sort'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Cost</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$SUM = 0;
				$SumX = 0;
				$no = 0;
				if(!empty($qBQdetailRest)){
					foreach($qBQdetailRest AS $val => $valx){ $no++;
						$SUM += $valx['cost'];
						$cost = 0;
						if($valx['cost'] <> 0 OR $valx['cost'] <> 0){
							$cost = $valx['cost']/$valx['qty'];
						}
						echo "<tr>";
							echo "<td align='right' class='so_style_list'><center>".$no."<input type='hidden' id='est_harga_".$no."' value='".$cost."'></center></td>";
							echo "<td align='left' class='so_style_list'>".strtoupper($valx['id_category'])."</td>";
							echo "<td align='left' class='so_style_list'>".spec_bq($valx['id_milik'])."</td>";
							echo "<td align='center' class='so_style_list'>
									<input type='hidden' name='UpQtySo[".$no."][id]' style='text-align: center; width:100%;' value='".$valx['id']."'>
									<input type='text' name='UpQtySo[".$no."][qty]' class='form-control text-center input-sm maskM chQty' style='text-align: center; width:100%;' data-no='".$no."' value='".$valx['qty']."' autocomplete='off' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
									<input type='hidden' name='UpQtySo[".$no."][id_bq_header]' value='".$valx['id_bq_header']."'>
									<input type='hidden' name='UpQtySo[".$no."][id_delivery]' value='".$valx['id_delivery']."'>
									<input type='hidden' name='UpQtySo[".$no."][series]' value='".$valx['series']."'>
									<input type='hidden' name='UpQtySo[".$no."][sub_delivery]' value='".$valx['sub_delivery']."'>
									<input type='hidden' name='UpQtySo[".$no."][sts_delivery]' value='".$valx['sts_delivery']."'>
									<input type='hidden' name='UpQtySo[".$no."][id_category]' value='".$valx['id_category']."'>
									<input type='hidden' name='UpQtySo[".$no."][diameter_1]' value='".$valx['diameter_1']."'>
									<input type='hidden' name='UpQtySo[".$no."][diameter_2]' value='".$valx['diameter_2']."'>
									<input type='hidden' name='UpQtySo[".$no."][length]' value='".$valx['length']."'>
									<input type='hidden' name='UpQtySo[".$no."][thickness]' value='".$valx['thickness']."'>
									<input type='hidden' name='UpQtySo[".$no."][sudut]' value='".$valx['sudut']."'>
									<input type='hidden' name='UpQtySo[".$no."][id_standard]' value='".$valx['id_standard']."'>
									<input type='hidden' name='UpQtySo[".$no."][type]' value='".$valx['type']."'>
									</td>";
							echo "<td align='left' class='so_style_list'>".$valx['id_product']."</span></td>";
							echo "<td align='right' class='so_style_list'><div id='sumAk_".$no."'>".number_format($valx['cost'], 2)."</div></span></td>";
							echo "<td align='center' class='so_style_list'><button type='button' data-id='".$valx['id']."' data-id_bq_header='".$valx['id_bq_header']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so' title='Delete From SO / Back To Quotation' data-role='qtip'><i class='fa fa-trash'></i></button></span></td>";
						echo "</tr>";
					}
				}
			?>
			<!--
			<tr>
				<th class="text-right"></th>
				<th class="text-left" colspan='4' style='vertical-align:middle;'>TOTAL COST PRODUCT</th>
				<th class="text-right"><div id='sumSO'><?= number_format($SUM, 2);?></div></th>
				<th class="text-right"></th>
			</tr>
			-->
			<?php
				//material
				$SUM_MAT = 0;
				if(!empty($rest_material)){
					foreach($rest_material AS $val => $valx){ $no++;
						$SUM_MAT += $valx['price_total'];
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$valx['price_total']/$valx['qty']."'></td>";
							echo "<td colspan='2'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
							echo "<td align='right'>
									<input type='hidden' name='UpQtySoMat[".$no."][id]' value='".$valx['id']."'>
									<input type='text' name='UpQtySoMat[".$no."][qty]' class='form-control text-right input-sm maskM chQty' style='width:100%;' data-no='".$no."' value='".number_format($valx['qty'],2)."'>
							</td>";
							echo "<td align='left'>KG</td>";
							echo "<td align='right'><div id='sumAk_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
							echo "<td align='center'><button type='button' data-id='".$valx['id']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so_mat' title='Delete From SO / Back To Quotation' data-role='qtip'><i class='fa fa-trash'></i></button></td>";
						echo "</tr>";
					}
					// echo "<tr>";
						// echo "<th class='text-right'></th>";
						// echo "<th class='text-left' colspan='4' style='vertical-align:middle;'>TOTAL COST MATERIAL</th>";
						// echo "<th class='text-right'>".number_format($SUM_MAT, 2)."</th>";
						// echo "<th class='text-right'></th>";
					// echo "</tr>";
				}
			?>
			
			<?php
				//material
				$SUM_MAT_NON = 0;
				if(!empty($rest_acc)){
					foreach($rest_acc AS $val => $valx){ $no++;
						$SUM_MAT_NON += $valx['price_total'];
						
						$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
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
						$satuan = $valx['satuan'];
						if($valx['category'] == 'plate'){
							$qty = $valx['berat'];
							$satuan = '1';
						}
						
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$valx['price_total']."'></td>";
							echo "<td colspan='2'>".$nama_acc."</td>";
							echo "<td align='right'>
									<input type='hidden' name='UpQtySoMat[".$no."][id]' value='".$valx['id']."'>
									<input type='text' name='UpQtySoMat[".$no."][qty]' class='form-control text-right input-sm maskM chQty' style='width:100%;' data-no='".$no."' value='".number_format($valx['qty'],2)."'>
							</td>";
							echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
							echo "<td align='right'><div id='sumAk_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
							echo "<td align='center'><button type='button' data-id='".$valx['id']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so_mat' title='Delete From SO / Back To Quotation' data-role='qtip'><i class='fa fa-trash'></i></button></td>";
						echo "</tr>";
					}
					// echo "<tr>";
						// echo "<th class='text-right'></th>";
						// echo "<th class='text-left' colspan='4' style='vertical-align:middle;'>TOTAL COST NON FRP</th>";
						// echo "<th class='text-right'>".number_format($SUM_MAT_NON, 2)."</th>";
						// echo "<th class='text-right'></th>";
					// echo "</tr>";
				}
			?>
			
			<?php
				//engine
				if(!empty($data_eng)){
					foreach($data_eng AS $val => $valx){ $no++;
						$harga_total = $valx['price_total'];
						$harga_satuan = $valx['price_total']/$valx['qty'];
						if($valx['sts_so'] == 'N'){
							$harga_total = 0;
							$harga_satuan = 0;
						}
						$SUM_MAT_NON += $harga_total;
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$harga_satuan."'></td>";
							echo "<td colspan='2'>".strtoupper($valx['category'].' - '.$valx['caregory_sub'])."</td>";
							echo "<td align='right'>
									<input type='hidden' name='EngPackTrans[".$no."][id]' value='".$valx['id']."'>
									<input type='text' name='EngPackTrans[".$no."][qty]' class='form-control text-right input-sm maskM chQty' style='width:100%;' data-no='".$no."' value='".number_format($valx['qty'])."'  data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
							</td>";
							echo "<td align='left'>UNIT</td>";
							echo "<td align='right'><div id='sumAk_".$no."'>".number_format($harga_total,2)."</div></td>";
							if($valx['sts_so'] == 'Y'){
								echo "<td align='center'><button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-danger del_so_eng_pack_trans' title='Delete From SO' data-role='qtip'><i class='fa fa-trash'></i></button></td>";
							}
							if($valx['sts_so'] == 'N'){
								echo "<td align='center'><button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-success add_so_eng_pack_trans' title='Add To SO' data-role='qtip'><i class='fa fa-plus'></i></button></td>";
							}
						echo "</tr>";
					}
				}

				//packing
				if(!empty($data_pack)){
					foreach($data_pack AS $val => $valx){ $no++;
						$harga_total = $valx['price_total'];
						$harga_satuan = $valx['price_total']/1;
						if($valx['sts_so'] == 'N'){
							$harga_total = 0;
							$harga_satuan = 0;
						}
						$SUM_MAT_NON += $harga_total;
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$harga_satuan."'></td>";
							echo "<td colspan='2'>".strtoupper($valx['category'].' - '.$valx['caregory_sub'].' / '.$valx['option_type'])."</td>";
							echo "<td align='right'>
									<input type='hidden' name='EngPackTrans[".$no."][id]' value='".$valx['id']."'>
									<input type='hidden' name='EngPackTrans[".$no."][qty]' class='form-control text-right input-sm maskM chQty' style='width:100%;' data-no='".$no."' value='".number_format(1)."'>
							</td>";
							echo "<td align='left'></td>";
							echo "<td align='right'><div id='sumAk_".$no."'>".number_format($harga_total,2)."</div></td>";
							if($valx['sts_so'] == 'Y'){
								echo "<td align='center'><button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-danger del_so_eng_pack_trans' title='Delete From SO' data-role='qtip'><i class='fa fa-trash'></i></button></td>";
							}
							if($valx['sts_so'] == 'N'){
								echo "<td align='center'><button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-success add_so_eng_pack_trans' title='Add To SO' data-role='qtip'><i class='fa fa-plus'></i></button></td>";
							}
						echo "</tr>";
					}
				}

				//transport
				if(!empty($data_ship)){
					foreach($data_ship AS $val => $valx){ $no++;
						$harga_total = $valx['price_total'];
						$harga_satuan = $valx['price_total']/$valx['qty'];
						if($valx['sts_so'] == 'N'){
							$harga_total = 0;
							$harga_satuan = 0;
						}
						$SUM_MAT_NON += $harga_total;

						$Add = "";
						if($valx['category'] == 'lokal' AND $valx['caregory_sub'] == 'VIA DARAT'){
							$Add = strtoupper("".get_name('truck','nama_truck','id',$valx['kendaraan']).", DEST. ".$valx['area']." - ".$valx['tujuan']);
						}
						if($valx['caregory_sub'] != 'VIA DARAT'){
							$Add = strtoupper($valx['kendaraan']);
						}
						echo "<tr>";
							echo "<td align='center' class='so_style_list'>".$no."<input type='hidden' id='est_harga_".$no."' value='".$harga_satuan."'></td>";
							echo "<td colspan='2'>".strtoupper($valx['category'].' - '.$valx['caregory_sub'])."</td>";
							echo "<td align='right'>
									<input type='hidden' name='EngPackTrans[".$no."][id]' value='".$valx['id']."'>
									<input type='text' name='EngPackTrans[".$no."][qty]' class='form-control text-right input-sm maskM chQty' style='width:100%;' data-no='".$no."' value='".number_format($valx['qty'])."'  data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
							</td>";
							echo "<td align='left'>".$Add."</td>";
							echo "<td align='right'><div id='sumAk_".$no."'>".number_format($harga_total,2)."</div></td>";
							if($valx['sts_so'] == 'Y'){
								echo "<td align='center'><button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-danger del_so_eng_pack_trans' title='Delete From SO' data-role='qtip'><i class='fa fa-trash'></i></button></td>";
							}
							if($valx['sts_so'] == 'N'){
								echo "<td align='center'><button type='button' data-id='".$valx['id']."' class='btn btn-sm btn-success add_so_eng_pack_trans' title='Add To SO' data-role='qtip'><i class='fa fa-plus'></i></button></td>";
							}
							echo "</tr>";
					}
				}
			?>
			
			<tr>
				<th class="text-right"></th>
				<th class="text-left" colspan='4' style='vertical-align:middle;'>TOTAL COST</th>
				<th class="text-right"><div id='sumSO2'><?= number_format($SUM + $SUM_MAT + $SUM_MAT_NON, 2);?></div></th>
				<th class="text-right"></th>
			</tr>
		</tbody>
	</table>
	<?php
		if(!empty($qBQdetailRest)){
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:120px; float:right; margin: 10px 0px 0px 5px;','value'=>'Ajukan SO','content'=>'Ajukan SO','id'=>'ajukanSO')).' ';
		}
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:120px; float:right; margin: 10px 0px 0px 0px;','value'=>'Save qty SO','content'=>'Save Qty SO','id'=>'update_qty_so')).' ';
	?>
</div>
<?php } ?>
<style>
	.so_style_list{
		vertical-align:middle;
		padding-left:20px;
	}
</style>

<script>
	swal.close();
	$('.maskM').maskMoney();
	
	$(document).on('keyup','.chQty', function(){
		var nomor 	= $(this).data('no');
		var numR 	= $('#numR').val();
		var qty 	= getNum($(this).val().split(",").join(""));
		var perPro 	= getNum($('#est_harga_'+nomor).val());
		var sumPro 	= getNum(qty * perPro);
		$('#sumAk_'+nomor).html(number_format(sumPro,2));
		var a;
		var TotalSum = 0;
		for(a=1;a<=numR;a++){
			var getT = getNum($('#sumAk_'+a).html().split(",").join(""));
			TotalSum += getT;
		}
		var SUM2 = getNum(TotalSum);
		$('#sumSO2').html(number_format(SUM2,2));
	});
	



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