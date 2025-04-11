<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
$kurs_usd_dipakai = (!empty($restSO))?$restSO[0]->kurs_usd_dipakai:get_kurs('USD','IDR');
?>
<form action="#" method="POST" id="form_proses_bro" autocomplete='off'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'>IPP Number</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= $getHeader[0]->no_ipp;?><input type='hidden' name='no_ipp' value='<?= $getHeader[0]->no_ipp;?>' ></div>
				<label class='label-control col-sm-2'><b>Base Currency<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<label><input type="radio" name="base_cur" id="base_cur_usd" <?= (!empty($restSO)?($restSO[0]->base_cur=="USD"?" checked":""):" checked");?> value="USD" onclick="set_cur('USD')"> USD </label>
					<label><input type="radio" name="base_cur" id="base_cur_idr" <?= (!empty($restSO)?($restSO[0]->base_cur=="IDR"?" checked":""):"");?> value="IDR" onclick="set_cur('IDR')"> IDR </label>

				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Customer Name</label>
				<div class='col-sm-4'><b>:</b>&nbsp;&nbsp;&nbsp; <?= strtoupper($getHeader[0]->nm_customer);?></div>

				<label class='label-control col-sm-2'><b>Nomor PO <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'no_po','name'=>'no_po','class'=>'form-control input-md','placeholder'=>'Nomor PO'),strtoupper((!empty($restSO[0]->no_po))?$restSO[0]->no_po:''));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'>Project Name <span class='text-red'>*</span></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'project','name'=>'project','class'=>'form-control input-md','placeholder'=>'Project Name'),strtoupper((!empty($restSO[0]->project))?$restSO[0]->project:$getHeader[0]->project));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Tanggal PO <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'tgl_po','name'=>'tgl_po','class'=>'form-control input-md datepicker_max','placeholder'=>'Tanggal PO','readonly'=>'readonly'),(!empty($restSO[0]->tgl_po))?$restSO[0]->tgl_po:'');
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Catatan </b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_textarea(array('id'=>'catatan','name'=>'catatan','class'=>'form-control input-md','placeholder'=>'Catatan','rows'=>'3'),(!empty($restSO[0]->catatan))?$restSO[0]->catatan:'');
					?>
				</div>
				<label class='label-control col-sm-2'><b>Alamat Pengiriman <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					$pengiriman_ipp = get_name('production_delivery','address_delivery','no_ipp',$getHeader[0]->no_ipp);
					 echo form_textarea(array('id'=>'alamat_pengiriman','name'=>'alamat_pengiriman','class'=>'form-control input-md','placeholder'=>'Alamat Pengiriman','rows'=>'3'),(!empty($restSO[0]->alamat_pengiriman))?$restSO[0]->alamat_pengiriman:$pengiriman_ipp);
					?>
				</div>
			</div>
		</div>
		<div class="box-body" style="">
			<div class="table-responsive">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr class='bg-bluexyz'>
						<th class="text-left" colspan='14'>PRODUCT</th>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-left" width='12%'>Item Product</th>
						<th class="text-center" width='9%'>Item Cust</th>
						<th class="text-center" width='8%'>Description</th>
						<th class="text-center" width='5%'>Dim 1</th>
						<th class="text-center" width='5%'>Dim 2</th>
						<th class="text-center" width='5%'>Liner</th>
						<th class="text-center" width='5%'>Pressure</th>
						<th class="text-center" width='7%'>Specification</th>
						<th class="text-center" width='8%'>Unit Price</th>
						<th class="text-center" width='5%'>Qty</th>
						<th class="text-center" width='5%'>Unit</th>
						<th class="text-center" width='9%'>Harga<br />Quotation (USD)</th>
						<th class="text-center" width='9%'>Nilai PO<br />(USD)</th>
						<th class="text-center" width='9%'>Nilai PO<br />(IDR)</th>
					</tr>
				</tbody>
				<tbody>
					<?php
						$SUM = 0;
						$SUM_IDR = 0;
						$no = 0;
						foreach($product AS $val => $valx){
							if($valx['qty'] <> '0'){
								$no++;
								$dataSum = 0;
								$dataDeal = 0;
								if($valx['qty'] <> 0){
									$dataSum	= round($valx['cost'],2);
									$dataDeal	= round($valx['cost'],2);
								}
								
								$SUM += $dataSum;

								if(!empty($valx['deal_usd'])){
									$dataDeal	= round($valx['deal_usd'],2);
								}

								
								$dataSumIdr=($dataSum*$kurs_usd_dipakai);
								$SUM_IDR += $dataSumIdr;

								if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
									$unitT = "Btg";
								}
								else{
									$unitT = "Pcs";
								}

								$get_so 	= $this->db->query("SELECT customer_item, `desc` FROM billing_so_product WHERE id_milik='".$valx['id_milik']."' LIMIT 1")->result();
								$cust_item 	= (!empty($get_so))?$get_so[0]->customer_item:'';
								$desc 		= (!empty($get_so))?$get_so[0]->desc:'';

								echo "<tr class='product_".$no."'>";
									echo "<td>".strtoupper($valx['id_category'])."
											<input type='hidden' name='detail_product[".$no."][id]' value='".$valx['id_milik']."'>
											<input type='hidden' name='detail_product[".$no."][product]' value='".$valx['id_category']."'>
											<input type='hidden' name='detail_product[".$no."][dim1]' value='".$valx['diameter_1']."'>
											<input type='hidden' name='detail_product[".$no."][dim2]' value='".$valx['diameter_2']."'>
											<input type='hidden' name='detail_product[".$no."][liner]' value='".substr($valx['series'],6,5)."'>
											<input type='hidden' name='detail_product[".$no."][pressure]' value='".substr($valx['series'],3,2)."'>
											<input type='hidden' name='detail_product[".$no."][spec]' value='".spec_bq($valx['id_milik'])."'>
											<input type='hidden' name='detail_product[".$no."][price_satuan]' value='".$dataSum / $valx['qty']."'>
											<input type='hidden' name='detail_product[".$no."][qty]' value='".$valx['qty']."'>
											<input type='hidden' name='detail_product[".$no."][unit]' value='".strtolower($unitT)."'>
											<input type='hidden' name='detail_product[".$no."][total_price]' value='".$dataSum."'>

										</td>";
									echo "<td align='left'><input type='text' name='detail_product[".$no."][customer_item]' class='form-control input-sm text-left' value='".$cust_item."'></td>";
									echo "<td align='left'><input type='text' name='detail_product[".$no."][desc]' class='form-control input-sm text-left' value='".$desc."'></td>";
									echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
									echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
									echo "<td align='center'>".substr($valx['series'],6,5)."</td>";
									echo "<td align='center'>".substr($valx['series'],3,2)."</td>";
									echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
									echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
									echo "<td align='center'>".$valx['qty']."</td>";
									echo "<td align='center'>".$unitT."</td>";
									echo "<td align='right'><div id='product_usdori_".$no."'>".number_format($dataSum,2)."</div></td>";
									echo "<td align='right'>
									<input type='text' name='detail_product[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right productSUMusd base_usd divide' id='total_deal_usd_".$no."' onblur='calcusd(".$no.")' value='".($dataDeal)."'></td>";
									echo "<td align='right'>
									<input type='text' name='detail_product[".$no."][total_deal_idr]' data-no='".$no."' class='form-control input-sm text-right productSUMidr base_idr divide' id='total_deal_idr_".$no."' onblur='calcidr(".$no.")' value='".$dataSumIdr."'></td>";
								echo "</tr>";
							}
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL PRODUCT</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_product_usd_awal'>".number_format($SUM,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_product_usd' class='divide'>".($SUM)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_product_idr' class='divide'>".($SUM_IDR)."</div></b></td>";
						echo "</tr>";
						//=========================================MATERIAL=============================================
						?>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='14'>MATERIAL</th>
						</tr>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='8'>Material Name</th>
							<th class="text-center">Weight</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Unit Price</th>
							<th class="text-center">Total Price (USD)</th>
							<th class="text-center">Nilai Deal (USD)</th>
							<th class="text-center">Nilai Deal (IDR)</th>
						</tr>
						<?php
						$SUM_MAT = 0;
						$SUM_MAT_IDR = 0;
						if(!empty($data_material)){
							foreach($data_material AS $val => $valx){
								$no++;

								$price_satuan = $valx['price_total']/$valx['weight'];
								$price_total = round($valx['qty_so'] * $price_satuan,2);

								$SUM_MAT += ($price_total);
								$SUM_MAT_IDR += ($price_total*$kurs_usd_dipakai);

								$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
								$deal_usd = $price_total;
								if(!empty($get_deal_usd)){
									$deal_usd = $get_deal_usd[0]->total_deal_usd;
								}
								$deal_idr=($deal_usd*$kurs_usd_dipakai);

								echo "<tr class='material_".$no."'>";
									echo "<td colspan='8'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['caregory_sub']))."
											<input type='hidden' name='detail_material[".$no."][id]' value='".$valx['id_milik']."'>
											<input type='hidden' name='detail_material[".$no."][price_satuan]' value='".$price_satuan."'>
											<input type='hidden' name='detail_material[".$no."][total_price]' value='".$price_total."'>
											</td>";
									echo "<td align='right'>".number_format($valx['qty_so'],2)."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['option_type']))."</td>";
									echo "<td align='right'>".number_format($price_satuan,2)."</td>";
									echo "<td align='right'><div id='material_usdori_".$no."'>".number_format($price_total,2)."</div></td>";
									echo "<td align='right'>
									<input type='text' name='detail_material[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right materialSUMusd base_usd divide' id='total_deal_usd_".$no."' value='".($deal_usd)."' onblur='calcusd(".$no.")'></td>";
									echo "<td align='right'>
									<input type='text' name='detail_material[".$no."][total_deal_idr]' data-no='".$no."' class='form-control input-sm text-right materialSUMidr base_idr divide' id='total_deal_idr_".$no."' value='".($deal_idr)."' onblur='calcidr(".$no.")'></td>";
								echo "</tr>";
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='14'>Empty data material</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL MATERIAL</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_material_usd_awal'>".number_format($SUM_MAT,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_material_usd' class='divide'>".($SUM_MAT)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_material_idr' class='divide'>".($SUM_MAT_IDR)."</div></b></td>";
						echo "</tr>";
						//=========================================ACCESORIS=============================================
						?>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='14'>ACCESSORIES</th>
						</tr>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='4'>Material Name</th>
							<th class="text-left" colspan='4'>Description</th>
							<th class="text-center">Qty</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Unit Price</th>
							<th class="text-center">Total Price (USD)</th>
							<th class="text-center">Nilai Deal (USD)</th>
							<th class="text-center">Nilai Deal (IDR)</th>
						</tr>
						<?php
						$SUM_ACC = 0;
						$SUM_ACC_IDR = 0;
						if(!empty($data_non_frp)){
							foreach($data_non_frp AS $val => $valx){
								$no++;

								$qty = $valx['qty'];
								$qty_so = $valx['qty_so'];
								$satuan = $valx['option_type'];
								if($valx['category'] == 'plate'){
									$qty = $valx['weight'];
									$qty_so = $valx['qty_so'];
									$satuan = '1';
								}

								$price_satuan = $valx['price_total']/$qty;
								$price_total = $valx['qty_so'] * $price_satuan;

								$SUM_ACC += $price_total;
								$SUM_ACC_IDR += ($price_total*$kurs_usd_dipakai);

								$get_deal_usd = $this->db->select('total_deal_usd, desc')->get_where('billing_so_add', array('id_milik'=>$valx['id_milik2']))->result();
								$deal_usd = round($price_total,2);
								$desc 		= (!empty($get_deal_usd))?$get_deal_usd[0]->desc:'';
								if(!empty($get_deal_usd)){
									$deal_usd = round($get_deal_usd[0]->total_deal_usd,2);
								}
								$deal_idr=($deal_usd*$kurs_usd_dipakai);

								echo "<tr class='material_".$no."'>";
									echo "<td colspan='4'>".get_name_acc($valx['caregory_sub'])."
											<input type='hidden' name='detail_aksesoris[".$no."][id]' value='".$valx['id_milik2']."'>
											<input type='hidden' name='detail_aksesoris[".$no."][category]' value='".$valx['category']."'>
											<input type='hidden' name='detail_aksesoris[".$no."][price_satuan]' value='".$price_satuan."'>
											<input type='hidden' name='detail_aksesoris[".$no."][total_price]' id='val_aksesoris_usdori_".$no."' value='".$price_total."'>
											</td>";
									echo "<td align='left' colspan='4'><input type='text' name='detail_aksesoris[".$no."][desc]' class='form-control input-sm text-left' value='".$desc."'></td>";
									echo "<td align='right'>".number_format($qty_so,2)."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$satuan))."</td>";
									echo "<td align='right'>".number_format($price_satuan,2)."</td>";
									echo "<td align='right'><div id='aksesoris_usdori_".$no."'>".number_format($price_total,2)."</div></td>";
									echo "<td align='right'><input type='text' name='detail_aksesoris[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right aksesorisSUMusd base_usd divide' id='total_deal_usd_".$no."' value='".($deal_usd)."' onblur='calcusd(".$no.")'></td>";
									echo "<td align='right'>
									<input type='text' name='detail_aksesoris[".$no."][total_deal_idr]' data-no='".$no."' class='form-control input-sm text-right aksesorisSUMidr base_idr divide' id='total_deal_idr_".$no."' value='".$deal_idr."' onblur='calcidr(".$no.")'></td>";
								echo "</tr>";
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='14'>Empty data accesoris</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL ACCESSORIES</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_acc_usd_awal'>".number_format($SUM_ACC,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_acc_usd' class='divide'>".($SUM_ACC)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_acc_idr' class='divide'>".($SUM_ACC_IDR)."</div></b></td>";
						echo "</tr>";
						//=========================================ENGINE=============================================
						?>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='14'>ENGINEERING</th>
						</tr>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='8'>Item Product</th>
							<th class="text-center">Qty</th>
							<th class="text-center">Unit</th>
							<th class="text-center">Unit Price</th>
							<th class="text-center">Total Price (USD)</th>
							<th class="text-center">Nilai Deal (USD)</th>
							<th class="text-center">Nilai Deal (IDR)</th>
						</tr>
						<?php
						$SUM_ENG = 0;
						$SUM_ENG_IDR = 0;
						if(!empty($data_eng)){
							foreach($data_eng AS $val => $valx){
								// if($valx['qty'] > 0){
									$no++;
									$SUM_ENG += round($valx['price_total'],2);
									$SUM_ENG_IDR += round(($valx['price_total']*$kurs_usd_dipakai),2);

									$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
									$deal_usd = round($valx['price_total'],2);
									if(!empty($get_deal_usd)){
										$deal_usd = round($get_deal_usd[0]->total_deal_usd,2);
									}
									$deal_idr = ($deal_usd*$kurs_usd_dipakai);
									$qty = $valx['qty'];
									if($valx['caregory_sub'] == 'STRESS ANALISYS'){
										$qty = 1;
									}

									echo "<tr class='engine_".$no."'>";
										echo "<td colspan='8'>".strtoupper($valx['caregory_sub'])."
												<input type='hidden' name='detail_engine[".$no."][id]' value='".$valx['id']."'>
												<input type='hidden' name='detail_engine[".$no."][satuan]' value='".$valx['unit']."'>
												<input type='hidden' name='detail_engine[".$no."][qty]' value='".$qty."'>
												<input type='hidden' name='detail_engine[".$no."][price_satuan]' value='".$valx['price_total']/$qty."'>
												<input type='hidden' name='detail_engine[".$no."][total_price]' value='".$valx['price_total']."'>

												</td>";
										echo "<td align='center'>".number_format($qty)."</td>";
										echo "<td align='center'>".strtoupper($valx['unit'])."</td>";
										echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
										echo "<td align='right'><div id='engine_usdori_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
										echo "<td align='right'><input type='text' name='detail_engine[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right engineSUMusd base_usd divide' id='total_deal_usd_".$no."' value='".($deal_usd)."'></td>";
										echo "<td align='right'>
										<input type='text' name='detail_engine[".$no."][total_deal_idr]' data-no='".$no."' class='form-control input-sm text-right engineSUMidr base_idr divide' id='total_deal_idr_".$no."' value='".($deal_idr)."'></td>";
									echo "</tr>";
								// }
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='14'>Empty data </td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL ENGENERING</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_eng_usd_awal'>".number_format($SUM_ENG,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_eng_usd' class='divide'>".($SUM_ENG)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_eng_idr' class='divide'>".($SUM_ENG_IDR)."</div></b></td>";
						echo "</tr>";
						//=========================================PACKING=============================================
						?>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='14'>PACKING</th>
						</tr>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='10'>Item Product</th>
							<th class="text-center">Type</th>
							<th class="text-center">Total Price (USD)</th>
							<th class="text-center">Nilai Deal (USD)</th>
							<th class="text-center">Nilai Deal (IDR)</th>
						</tr>
						<?php
						$SUM_PACK = 0;
						$SUM_PACK_IDR = 0;
						if(!empty($data_pack)){
							foreach($data_pack AS $val => $valx){
								$no++;
								$SUM_PACK += round($valx['price_total'],2);
								$SUM_PACK_IDR += ($valx['price_total']*$kurs_usd_dipakai);

								$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
								$deal_usd = $valx['price_total'];
								if(!empty($get_deal_usd)){
									$deal_usd = $get_deal_usd[0]->total_deal_usd;
								}
								$deal_idr = ($deal_usd*$kurs_usd_dipakai);

								echo "<tr class='packing_".$no."'>";
									echo "<td colspan='10'>".strtoupper($valx['caregory_sub'])."
											<input type='hidden' name='detail_packing[".$no."][id]' value='".$valx['id']."'>
											<input type='hidden' name='detail_packing[".$no."][satuan]' value='".$valx['option_type']."'>
											<input type='hidden' name='detail_packing[".$no."][total_price]' value='".$valx['price_total']."'>
											</td>";
									echo "<td align='center'>".strtoupper($valx['option_type'])."</td>";
									echo "<td align='right'><div id='packing_usdori_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
									echo "<td align='right'>
									<input type='text' name='detail_packing[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right packingSUMusd base_usd divide' id='total_deal_usd_".$no."' onblur='calcusd(".$no.")' value='".($deal_usd)."'></td>";
									echo "<td align='right'>
									<input type='text' name='detail_packing[".$no."][total_deal_idr]' data-no='".$no."' class='form-control input-sm text-right packingSUMidr base_idr divide' id='total_deal_idr_".$no."' onblur='calcidr(".$no.")' value='".($deal_idr)."'></td>";
								echo "</tr>";
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='14'>Empty data </td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL PACKING</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_pack_usd_awal'>".number_format($SUM_PACK,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_pack_usd' class='divide'>".($SUM_PACK)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_pack_idr' class='divide'>".($SUM_PACK_IDR)."</div></b></td>";
						echo "</tr>";
						//=========================================SHIPPING=============================================
						?>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='14'>SHIPPING</th>
						</tr>
						<tr class='bg-bluexyz'>
							<th class="text-left" colspan='9'>Item Product</th>
							<th class="text-center">Qty</th>
							<th class="text-center">Unit Price</th>
							<th class="text-center">Total Price (USD)</th>
							<th class="text-center">Nilai Deal (USD)</th>
							<th class="text-center">Nilai Deal (IDR)</th>
						</tr>
						<?php
						$SUM_SHIP = 0;
						$SUM_SHIP_IDR = 0;
						if(!empty($data_ship)){
							foreach($data_ship AS $val => $valx){
								$no++;
								$SUM_SHIP += round($valx['price_total'],2);
								$SUM_SHIP_IDR += ($valx['price_total']*$kurs_usd_dipakai);
								$Add = "";
								if($valx['category'] == 'lokal'){
									$Add = strtoupper(" (".get_name('cost_project_detail','kendaraan','id',$valx['id']).") DEST. ".get_name('cost_project_detail','area','id',$valx['id'])." - ".get_name('cost_project_detail','tujuan','id',$valx['id']));
								}

								$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
								$deal_usd = round($valx['price_total'],2);
								if(!empty($get_deal_usd)){
									$deal_usd = round($get_deal_usd[0]->total_deal_usd);
								}
								$deal_idr=($deal_usd*$kurs_usd_dipakai);

								echo "<tr class='shipping_".$no."'>";
									echo "<td colspan='9'>".strtoupper($valx['caregory_sub'].$Add)."
											<input type='hidden' name='detail_shipping[".$no."][id]' value='".$valx['id']."'>
											<input type='hidden' name='detail_shipping[".$no."][qty]' value='".$valx['qty']."'>
											<input type='hidden' name='detail_shipping[".$no."][price_satuan]' value='".$valx['price_total']/$valx['qty']."'>
											<input type='hidden' name='detail_shipping[".$no."][total_price]' value='".$valx['price_total']."'>
											</td>";
									echo "<td align='center'>".number_format($valx['qty'])."</td>";
									echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
									echo "<td align='right'><div id='shipping_usdori_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
									echo "<td align='right'>
									<input type='text' name='detail_shipping[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right shippingSUMusd base_usd divide' id='total_deal_usd_".$no."' onblur='calcusd(".$no.")' value='".($deal_usd)."'></td>";
									echo "<td align='right'>
									<input type='text' name='detail_shipping[".$no."][total_deal_idr]' data-no='".$no."' class='form-control input-sm text-right shippingSUMidr base_idr divide' id='total_deal_idr_".$no."' onblur='calcidr(".$no.")' value='".($deal_idr)."'></td>";
								echo "</tr>";
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='14'>Empty data </td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL SHIPPING</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_ship_usd_awal'>".number_format($SUM_SHIP,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_ship_usd' class='divide'>".($SUM_SHIP)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_ship_idr' class='divide'>".($SUM_SHIP_IDR)."</div></b></td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td colspan='14'>&nbsp</td>";
						echo "</tr>";

						echo "<tr>";
							echo "<td class='sumTotal'><b>TOTAL ALL</b></td>";
							echo "<td class='sumTotal' colspan='6' align='right'></td>";
							echo "<td class='sumTotal' align='center'></td>";
							echo "<td class='sumTotal' colspan='3'></td>";
							echo "<td class='sumTotal' align='right'><b><div id='sum_all_usd_awal' class='divide'>".($SUM + $SUM_ENG + $SUM_PACK + $SUM_SHIP + $SUM_MAT)."</div></b></td>";
							echo "<td class='sumTotal' align='right'><b><div id='sum_all_usd'></div></b></td>";
							echo "<td class='sumTotal' align='right'><b><div id='sum_all_idr'></div></b></td>";
						echo "</tr>";
						$diskon = (!empty($restSO))?$restSO[0]->diskon:'';
						$total_deal_usd = (!empty($restSO))?($restSO[0]->total_deal_usd):($SUM + $SUM_ENG + $SUM_PACK + $SUM_SHIP + $SUM_MAT);
						$total_deal_idr = (!empty($restSO))?($restSO[0]->total_deal_usd*$kurs_usd_dipakai):($SUM_IDR + $SUM_ENG_IDR + $SUM_PACK_IDR + $SUM_SHIP_IDR + $SUM_MAT_IDR);
						echo "<tr>";
							echo "<td class='sumTotal'></td>";
							echo "<td class='sumTotal' colspan='6' align='right'><b>DISKON (%)</b></td>";
							echo "<td class='sumTotal' align='center' colspan='3'><input readonly type='text' name='diskon' id='diskon' style='font-size:14px; font-weight:bold;' class='form-control input-sm text-center' value='".$diskon."' tabindex='-1'></td>";
							echo "<td class='sumTotal'></td>";
							echo "<td class='sumTotal' align='right'><b>NILAI DEAL</b></td>";
							echo "<td class='sumTotal' align='right'><input type='text' name='sum_all_usd_val' id='sum_all_usd_val' style='font-weight:bold; padding-right: inherit;' class='form-control input-sm text-right divide' placeholder='USD' value='".$total_deal_usd."' readonly tabindex='-1'></td>";
							echo "<td class='sumTotal' align='right'><input type='text' name='sum_all_idr_val' id='sum_all_idr_val' style='font-weight:bold; padding-right: inherit;' class='form-control input-sm text-right divide' value='".$total_deal_idr."' readonly tabindex='-1'></td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td colspan='14'>
									<input type='hidden' name='detail_total[1][product_usd_awal]' id='product_usd_awal' value='".$SUM."'>
									<input type='hidden' name='detail_total[1][product_usd]' id='product_usd'>
									<input type='hidden' name='detail_total[1][product_idr]' id='product_idr'>
									<input type='hidden' name='detail_total[1][material_usd_awal]' id='material_usd_awal' value='".$SUM_MAT."'>
									<input type='hidden' name='detail_total[1][material_usd]' id='material_usd'>
									<input type='hidden' name='detail_total[1][material_idr]' id='material_idr'>
									<input type='hidden' name='detail_total[1][acc_usd_awal]' id='acc_usd_awal'>
									<input type='hidden' name='detail_total[1][acc_usd]' id='acc_usd'>
									<input type='hidden' name='detail_total[1][acc_idr]' id='acc_idr' value='".$SUM_ACC."'>
									<input type='hidden' name='detail_total[1][eng_usd_awal]' id='eng_usd_awal' value='".$SUM_ENG."'>
									<input type='hidden' name='detail_total[1][eng_usd]' id='eng_usd'>
									<input type='hidden' name='detail_total[1][eng_idr]' id='eng_idr'>
									<input type='hidden' name='detail_total[1][pack_usd_awal]' id='pack_usd_awal' value='".$SUM_PACK."'>
									<input type='hidden' name='detail_total[1][pack_usd]' id='pack_usd'>
									<input type='hidden' name='detail_total[1][pack_idr]' id='pack_idr'>
									<input type='hidden' name='detail_total[1][ship_usd_awal]' id='ship_usd_awal' value='".$SUM_SHIP."'>
									<input type='hidden' name='detail_total[1][ship_usd]' id='ship_usd'>
									<input type='hidden' name='detail_total[1][ship_idr]' id='ship_idr'>
									<input type='hidden' name='kurs_usd_default' id='kurs_usd_default' value='".(get_kurs('USD','IDR'))."'>
									<input type='hidden' name='sum_all_usd_awal' id='val_sum_all_usd_awal' value='".($SUM + $SUM_ENG + $SUM_PACK + $SUM_SHIP + $SUM_MAT)."'>
									</td>";
						echo "</tr>";

						echo "<tr>";
							echo "<td><b>KURS/EDIT KURS</b></td>";
							echo "<td align='right' colspan='5'><b>1 USD = ".number_format(get_kurs('USD','IDR'))." IDR</b></td>";
							echo "<td align='center'><b></b></td>";
							echo "<td align='center'><input type='text' name='kurs_usd' id='kurs_usd' class='form-control input-sm text-right divide' value='".($kurs_usd_dipakai)."'></td>";
							echo "<td align='center' colspan='6'><input type='hidden' name='kurs_idr' id='kurs_idr' class='form-control input-sm text-right 9' value='".number_format(get_kurs('IDR','USD'),9)."' readonly></td>";
						echo "</tr>";
						// echo "<tr>";
							// echo "<td></td>";
							// echo "<td align='center'><b>1 IDR</b></td>";
							// echo "<td align='right'><b>".number_format(get_kurs('IDR','USD'),9)."</b></td>";
							// echo "<td align='center'><b>USD</b></td>";
							// echo "<td align='center'><b></b></td>";
							// echo "<td align='center'><input type='text' name='kurs_idr' id='kurs_idr' class='form-control input-sm text-right 9' value='".number_format(get_kurs('IDR','USD'),9)."' readonly></td>";
							// echo "<td align='center' colspan='6'></td>";
						// echo "</tr>";
					?>
				</tbody>
			</table>
			</div>
			<!--TOP-->
			<div>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-bluexyz'>
						<th class="text-left" colspan='8'>TERM OF PAYMENT</th>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" width='11%'>Group TOP</th>
						<th class="text-center" width='8%'>Progress (%)</th>
						<th class="text-center" width='11%'>Value (USD)</th>
						<th class="text-center" width='11%'>Value (IDR)</th>
						<th class="text-center" width='22%'>Keterangan</th>
						<th class="text-center" width='10%'>Est Jatuh Tempo</th>
						<th class="text-center" width='22%'>Persyaratan</th>
						<th class="text-center" width='5%'>#</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$id = 0;$ttl_usd=0;$ttl_idr=0;
					if(!empty($data_top)){
						foreach($data_top AS $val => $valx){ $id++;$ttl_usd +=$valx['value_usd'] ;$ttl_idr += $valx['value_idr'];
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
								echo "<select name='detail_po[".$id."][group_top]' class='form-control text-left chosen_select' value='".$id."'>";
									// echo "<option value='0'>Select Group TOP</option>";
									foreach($payment AS $val2 => $valx2){
										$sel = ($valx2['name'] == $valx['group_top'])?'selected':'';
										echo "<option value='".$valx2['name']."' ".$sel.">".strtoupper($valx2['name'])."</option>";
									}
								echo "</select>";
								echo "</td>";
								// echo "<td align='left'><input type='text' name='detail_po[".$id."][term]' class='form-control text-center input-md' value='".$valx['term']."'></td>";
								echo "<td align='left'><input type='text' id='progress_".$id."' name='detail_po[".$id."][progress]' value='".$valx['progress']."' class='form-control input-md text-center maskM progress_term' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
								echo "<td align='left'><input type='text' id='usd_".$id."' name='detail_po[".$id."][value_usd]' value='".number_format($valx['value_usd'],2)."' class='form-control input-md text-right maskM sum_tot_usd' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' readonly></td>";
								echo "<td align='left'><input type='text' id='idr_".$id."' name='detail_po[".$id."][value_idr]' value='".number_format($valx['value_idr'])."' class='form-control input-md text-right maskM sum_tot_idr' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' readonly></td>";
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
					<tr>
						<td align='center'><b>TOTAL</b></td>
						<td align='center'><b><div id='top_progress'></div></b></td>
						<td align='right'><b><div id='top_usd'><?=number_format($ttl_usd,2)?></div></b></td>
						<td align='right'><b><div id='top_idr'><?=number_format($ttl_idr)?></div></b></td>
						<td align='center' colspan='4'></td>
					</tr>
				</tbody>
			</table>
			</div>
			<div class="table-responsive">
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody>
					<tr class='bg-bluexyz'>
						<th class="text-left" colspan='11'>DELIVERY DATE</th>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" width='17%'>Item Product</th>
						<th class="text-center" width='8%'>Dim 1</th>
						<th class="text-center" width='8%'>Dim 2</th>
						<th class="text-center" width='8%'>Liner</th>
						<th class="text-center" width='8%'>Pressure</th>
						<th class="text-center" width='12%'>Specification</th>
						<th class="text-center" width='8%'>Qty</th>
						<th class="text-center" width='8%'>SUM Qty Delivery</th>
						<th class="text-center" width='8%'>Qty Delivery</th>
						<th class="text-center" width='9%'>Delivery Date</th>
						<th class="text-center" width='6%'>#</th>
					</tr>
				</tbody>
				<tbody>
					<?php
						$SUM = 0;
						$no = 0;
						foreach($product AS $val => $valx){
							if($valx['qty'] <> '0'){
								$no++;
								$count 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$getHeader[0]->no_ipp."' AND id_milik='".$valx['id_milik']."'")->num_rows();
								$each 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$getHeader[0]->no_ipp."' AND id_milik='".$valx['id_milik']."'")->result_array();
								$sum 	= $this->db->query("SELECT SUM(qty_delivery) AS total FROM scheduling_master WHERE no_ipp = '".$getHeader[0]->no_ipp."' AND id_milik='".$valx['id_milik']."'")->result();

								$qty_delivery 	= (!empty($each))?$each[0]['qty_delivery']:'';
								$delivery_date 	= (!empty($each))?$each[0]['delivery_date']:'';
								$count 			= (!empty($count))?$count:'1';

								echo "<tr class='baris_".$no."'>";
									echo "<td rowspan='".$count."' class='id_".$no."' >".strtoupper($valx['id_category'])."<input type='hidden' name='detail_delivery[".$no."][id]' value='".$valx['id_milik']."'></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='right'>".number_format($valx['diameter_1'])."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='right'>".number_format($valx['diameter_2'])."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'>".substr($valx['series'],6,5)."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'>".substr($valx['series'],3,2)."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='left'>".spec_bq($valx['id_milik'])."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'><div id='qty_del_".$no."'>".$valx['qty']."</div></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'><div id='tot_qty_del_".$no."'>".$sum[0]->total."</div></td>";
									echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][0][qty_delivery]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center  qty_".$no." qty_deliv' value='".$qty_delivery."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
									echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][0][delivery_date]' class='form-control input-sm text-center datepicker' readonly placeholder='Delivery date' value='".$delivery_date."'></td>";
									echo "<td align='center'>";
											if($valx['qty'] > 1){
												echo "<button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$no."'><i class='fa fa-plus'></i></button>";
											}
									echo "</td>";
								echo "</tr>";

								if($count > 1){
									$nox = 0;
									for($a=2; $a<=$count; $a++){ $nox++;
										echo "<tr>";
											echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][".$nox."][qty_delivery]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center  qty_".$no." qty_deliv' value='".$each[$nox]['qty_delivery']."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
											echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][".$nox."][delivery_date]' class='form-control input-sm text-center datepicker' readonly placeholder='Delivery date' value='".$each[$nox]['delivery_date']."'></td>";
											echo "<td align='center'>";
												echo "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='".$no."'><i class='fa fa-trash'></i></button>";
											echo "</td>";
										echo "</tr>";
									}
								}
							}
						}
						foreach($non_frp_delivery AS $val => $valx){
							if($valx['qty'] <> '0'){
								$no++;
								$count 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$getHeader[0]->no_ipp."' AND id_milik='".$valx['id_milik']."'")->num_rows();
								$each 	= $this->db->query("SELECT * FROM scheduling_master WHERE no_ipp = '".$getHeader[0]->no_ipp."' AND id_milik='".$valx['id_milik']."'")->result_array();
								$sum 	= $this->db->query("SELECT SUM(qty_delivery) AS total FROM scheduling_master WHERE no_ipp = '".$getHeader[0]->no_ipp."' AND id_milik='".$valx['id_milik']."'")->result();

								$qty_delivery 	= (!empty($each))?$each[0]['qty_delivery']:'';
								$delivery_date 	= (!empty($each))?$each[0]['delivery_date']:'';
								$count 			= (!empty($count))?$count:'1';

								$name = get_name('raw_materials','nm_material','id_material',$valx['id_material']);
								$category = 'MATERIAL';
								if($valx['category'] <> 'mat'){
									$name = get_name_acc($valx['id_material']);

									$category = 'BOLT & NUT';
									if($valx['category'] == 'plate'){
										$category = 'PLATE';
									}
									if($valx['category'] == 'gasket'){
										$category = 'GASKET';
									}
									if($valx['category'] == 'lainnya'){
										$category = 'LAINNYA';
									}
								}
								echo "<tr class='baris_".$no."'>";
									echo "<td rowspan='".$count."' class='id_".$no."' >".strtoupper($name)."<input type='hidden' name='detail_delivery[".$no."][id]' value='".$valx['id_milik']."'></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='right'></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='right'></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='left'>".$category."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='left'>".get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan'])."</td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'><div id='qty_del_".$no."'>".$valx['qty']."</div></td>";
									echo "<td rowspan='".$count."' class='id_".$no."' align='center'><div id='tot_qty_del_".$no."'>".$sum[0]->total."</div></td>";
									echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][0][qty_delivery]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center autoNumeric2 qty_".$no." qty_deliv' value='".$qty_delivery."'></td>";
									echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][0][delivery_date]' class='form-control input-sm text-center datepicker' readonly placeholder='Delivery date' value='".$delivery_date."'></td>";
									echo "<td align='center'>";
											if($valx['qty'] > 1){
												echo "<button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$no."'><i class='fa fa-plus'></i></button>";
											}
									echo "</td>";
								echo "</tr>";

								if($count > 1){
									$nox = 0;
									for($a=2; $a<=$count; $a++){ $nox++;
										echo "<tr>";
											echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][".$nox."][qty_delivery]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center autoNumeric2 qty_".$no." qty_deliv' value='".$each[$nox]['qty_delivery']."'></td>";
											echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][".$nox."][delivery_date]' class='form-control input-sm text-center datepicker' readonly placeholder='Delivery date' value='".$each[$nox]['delivery_date']."'></td>";
											echo "<td align='center'>";
												echo "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='".$no."'><i class='fa fa-trash'></i></button>";
											echo "</td>";
										echo "</tr>";
									}
								}
							}
						}
						?>
				</tbody>
			</table>
			</div>
			<div class='form-group row' style='float:right;'>
				<label class='label-control col-sm-1'></label>
				<div class='col-sm-11' style='float:right;'>
					<div id='alert-max' style="font-size: 17px;font-weight: bold;color: red;padding-bottom: 10px;">PROGRESS MELEBIHI 100% !!</div>
					<?php
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'edit_po'));
					?>
					<a href="<?php echo site_url('sales_order') ?>" class="btn btn-md btn-danger">Back</a>
				</div>
			</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style type="text/css">
	.headX{
		background-color: #05b3a3 !important;
		color : white;
	}
	.bg-bluexyz{
		background-color: #05b3a3 !important;
		color : white;
	}

	.sumTotal{
		font-size: 16px;
		color: #056d8d;
		vertical-align: middle !important;
	}
	.sumAwal{
		font-size: 14px;
		color: #0d89d1;
		vertical-align: middle !important;
	}
	.datepicker, .datepicker_max{
		cursor: pointer;
	}
	.clHide{
		display: none;
	}
</style>
<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
<script>
	$('.divide').divide();
	$(document).ready(function(){
		cek_other();
		$('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			minDate: 0
		});
		$('.datepicker_max').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
		$('#alert-max').hide();
		$('.chosen_select').chosen();

		$(document).on('click', '#edit_po', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var no_po	= $('#no_po').val();
			var tgl_po	= $('#tgl_po').val();
			var alamat_pengiriman	= $('#alamat_pengiriman').val();
			var project	= $('#project').val();
			if(project=='' || project==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Project Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#edit_po').prop('disabled',false);
				return false;
			}
			if(no_po=='' || no_po==null){
				swal({
				  title	: "Error Message!",
				  text	: 'PO Number is Empty, please input first ...',
				  type	: "warning"
				});
				$('#edit_po').prop('disabled',false);
				return false;
			}

			if(tgl_po=='' || tgl_po==null){
				swal({
				  title	: "Error Message!",
				  text	: 'PO Date is Empty, please input first ...',
				  type	: "warning"
				});
				$('#edit_po').prop('disabled',false);
				return false;
			}

			if(alamat_pengiriman=='' || alamat_pengiriman==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Alamat Pengiriman is Empty, please input first ...',
				  type	: "warning"
				});
				$('#edit_po').prop('disabled',false);
				return false;
			}

			swal({
				  title: "Are you sure?",
				  text: "You will not be able to process again this data!",
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
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/deal_project';
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
										  timer	: 7000
										});
									window.location.href = base_url + active_controller;
								}
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
									});
								}
								$('#edit_po').prop('disabled',false);
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
								$('#edit_po').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#edit_po').prop('disabled',false);
					return false;
				  }
			});
		});

		//Add TOP
		$(document).on('click', '.addPart', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			// console.log(get_id);
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add_"+id_bef).before(data.header);
					$("#add_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.datepicker').datepicker({
						dateFormat : 'yy-mm-dd',
						changeMonth: true,
						changeYear: true,
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

			change_kurs();
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

		$(document).on('click','.plus', function(){
			var no 		= $(this).data('id');
			// alert($(this).parent().parent().find("td:nth-child(1)").attr('rowspan'));return false;
			var kolom	= parseFloat($(this).parent().parent().find("td:nth-child(1)").attr('rowspan')) + 1;

			$(this).parent().parent().find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8)").attr('rowspan', kolom);

			var Rows	= "<tr>";
				Rows	+= "<td align='center'><input type='text' name='detail_delivery["+no+"][detail]["+kolom+"][qty_delivery]' data-no='"+no+"' data-no2='"+kolom+"' class='form-control input-sm text-center  qty_"+no+" qty_deliv' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
				Rows	+= "<td align='center'><input type='text' name='detail_delivery["+no+"][detail]["+kolom+"][delivery_date]' class='form-control text-center input-sm text-center datepicker' readonly placeholder='Delivery Date'></td>";
				Rows	+= "<td align='center'>";
				Rows	+= "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='"+no+"'><i class='fa fa-trash'></i></button>";
				Rows	+= "</td>";
				Rows	+= "</tr>";
			// alert(Rows);
			$(this).parent().parent().after(Rows);
			$('.datepicker').datepicker({
				dateFormat : 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				minDate: 0
			});
		});

		$(document).on('keyup','.qty_deliv', function(){
			var no 	= $(this).data('no');
			sum_qty_delivery(no);
		});

		$(document).on('click','.delete', function(){
			var no 		= $(this).data('id');
			var kolom	= parseFloat($(".baris_"+no).find("td:nth-child(1)").attr('rowspan')) - 1;
			$(".baris_"+no).find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8)").attr('rowspan', kolom);
			$(this).parent().parent().remove();
			sum_qty_delivery(no);
		});

		$(document).on('keyup', '#eng_usd, #pack_usd, #ship_usd, #material_usd, #acc_usd', function(){
			sum_all();
		});

	});


	function sum_qty_delivery(no = null){
		var SUM = 0;
		var qty = getNum($('#qty_del_'+no).html().split(",").join(""));

		// console.log('.qty_'+no);
		$('.qty_'+no).each(function(){
			var valuex = Number($(this).val().split(",").join(""));
			SUM += valuex;
		});
		$('#tot_qty_del_'+no).html(number_format(SUM,2));

		if(SUM > qty){
			$(".baris_"+no).find("td:nth-child(8)").attr('style','background-color:red;');
			$('#edit_po').hide();
		}else{
			$(".baris_"+no).find("td:nth-child(8)").attr('style','background-color:transparant;');
			$('#edit_po').show();
		}
	}

	function set_cur(currency){
		if(currency=='USD'){
			$(".base_idr").prop("readonly", true);
			$(".base_usd").prop("readonly", false);
			$(".base_idr").attr("tabindex", "-1");
			$(".base_usd").removeAttr("tabindex");
		}else{
			$(".base_idr").prop("readonly", false);
			$(".base_usd").prop("readonly", true);
			$(".base_idr").removeAttr("tabindex");
			$(".base_usd").attr("tabindex", "-1");
		}
	}
	function calcusd(id){
		var kurs=$("#kurs_usd").val();
		var nilai=$("#total_deal_usd_"+id).val();
		$("#total_deal_idr_"+id).val(parseFloat(kurs)*parseFloat(nilai));
		cek_other();
	}
	function calcidr(id){
		var kurs=$("#kurs_usd").val();
		var nilai=$("#total_deal_idr_"+id).val();
		$("#total_deal_usd_"+id).val(parseFloat(nilai)/parseFloat(kurs));
		cek_other();
	}
	function cek_other(){
		sumUsd=0
		$('.base_usd').each(function(){ sumUsd += parseFloat($(this).val()); });
		$("#sum_all_usd_val").val(sumUsd);
		sumIdr=0
		$('.base_idr').each(function(){ sumIdr += parseFloat($(this).val()); });
		$("#sum_all_idr_val").val(sumIdr);
		// PRODUCT
		productSUMusd=0
		$('.productSUMusd').each(function(){ productSUMusd += parseFloat($(this).val()); });
		$("#sum_product_usd").html(productSUMusd);
		$('#product_usd').val(productSUMusd);
		productSUMidr=0
		$('.productSUMidr').each(function(){ productSUMidr += parseFloat($(this).val()); });
		$("#sum_product_idr").html(productSUMidr);
		$('#product_idr').val(productSUMidr);
		// MATERIAL
		materialSUMusd=0
		$('.materialSUMusd').each(function(){ materialSUMusd += parseFloat($(this).val()); });
		$("#sum_material_usd").html(materialSUMusd);
		$('#material_usd').val(materialSUMusd);
		materialSUMidr=0
		$('.materialSUMidr').each(function(){ materialSUMidr += parseFloat($(this).val()); });
		$("#sum_material_idr").html(materialSUMidr);
		$('#material_idr').val(materialSUMidr);
		// ACCESSORIES
		aksesorisSUMusd=0
		$('.aksesorisSUMusd').each(function(){ aksesorisSUMusd += parseFloat($(this).val()); });
		$("#sum_acc_usd").html(aksesorisSUMusd);
		$('#acc_usd').val(aksesorisSUMusd);
		aksesorisSUMidr=0
		$('.aksesorisSUMidr').each(function(){ aksesorisSUMidr += parseFloat($(this).val()); });
		$("#sum_acc_idr").html(aksesorisSUMidr);
		$('#acc_idr').val(aksesorisSUMidr);
		// ENGINEERING
		engineSUMusd=0
		$('.engineSUMusd').each(function(){ engineSUMusd += parseFloat($(this).val()); });
		$("#sum_eng_usd").html(engineSUMusd);
		$('#eng_usd').val(engineSUMusd);
		engineSUMidr=0
		$('.engineSUMidr').each(function(){ engineSUMidr += parseFloat($(this).val()); });
		$("#sum_eng_idr").html(engineSUMidr);
		$('#eng_idr').val(engineSUMidr);
		// PACKING
		packingSUMusd=0
		$('.packingSUMusd').each(function(){ packingSUMusd += parseFloat($(this).val()); });
		$("#sum_pack_usd").html(packingSUMusd);
		$('#pack_usd').val(packingSUMusd);
		packingSUMidr=0
		$('.packingSUMidr').each(function(){ packingSUMidr += parseFloat($(this).val()); });
		$("#sum_pack_idr").html(packingSUMidr);
		$('#pack_idr').val(packingSUMidr);
		// SHIPPING
		shippingSUMusd=0
		$('.shippingSUMusd').each(function(){ shippingSUMusd += parseFloat($(this).val()); });
		$("#sum_ship_usd").html(shippingSUMusd);
		$('#ship_usd').val(shippingSUMusd);
		shippingSUMidr=0
		$('.shippingSUMidr').each(function(){ shippingSUMidr += parseFloat($(this).val()); });
		$("#sum_ship_idr").html(shippingSUMidr);
		$('#ship_idr').val(shippingSUMidr);

		$('.divide').divide();
		set_diskon();
		change_kurs();
	}
	function set_diskon(){
		var sum_all_usd_awal=<?=$total_deal_usd?>;
		var sum_all_usd_val=$("#sum_all_usd_val").val();
		selisih=parseFloat(sum_all_usd_awal)-parseFloat(sum_all_usd_val);
		persen=(parseFloat(selisih)*100/parseFloat(sum_all_usd_awal));
		$("#diskon").val(persen);
	}
	$(document).on('keyup', '#kurs_usd', function(e){
		change_by_diskon_kurs();
		cek_other();
		change_kurs();
	});
	function change_by_diskon_kurs(){
		var kurs=$("#kurs_usd").val();
		if($('#base_cur_usd').is(":checked")){
			$('.base_usd').each(function(){
				ids=$(this).attr("data-no");
				nilailama=$("#total_deal_usd_"+ids).val();
				nilaibaru=(parseFloat(nilailama)*parseFloat(kurs));
				$("#total_deal_idr_"+ids).val(nilaibaru);
			});
		}else{
			$('.base_idr').each(function(){
				ids=$(this).attr("data-no");
				nilailama=$("#total_deal_idr_"+ids).val();
				nilaibaru=(parseFloat(nilailama)/parseFloat(kurs));
				$("#total_deal_usd_"+ids).val(nilaibaru.toFixed(2));
			});
		}
	}
	function term_process(a){
		var total		= getNum($('#sum_all_usd_val').val().split(",").join(""));
		var progress 	= getNum($('#progress_'+a).val().split(",").join(""));
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var current  	= 'USD';

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
		change_kurs();
	}

	function change_kurs(){
		var total		= parseFloat($('#sum_all_usd_val').val());//getNum(.split(",").join(""));
		var kurs		= parseFloat($('#kurs_usd').val());//getNum(.split(",").join(""));
		var current  	= 'USD';
		var SUM_PROGRESS = 0;
		var SUM_USD = 0;
		var SUM_IDR = 0;
		$(".progress_term" ).each(function() {
			var id 		= $(this).attr('id');
			var det_id	= id.split('_');
			var a		= det_id[1];

			var progress 	= getNum($('#progress_'+a).val().split(",").join(""));
			if(current == 'IDR'){
				var tot_idr 	= (progress/100) * total;
				var tot_usd 	= (progress/100) * (total * kurs);
			}
			if(current == 'USD'){
				var tot_usd 	= (progress/100) * total;
				var tot_idr 	= (progress/100) * (total * kurs);
			}
			SUM_PROGRESS += getNum($('#progress_'+a).val().split(",").join(""));
			SUM_USD += tot_usd;
			SUM_IDR += tot_idr;
			$('#usd_'+a).val(number_format(tot_usd,2));
			$('#idr_'+a).val(number_format(tot_idr));
		});

		$('#top_progress').html(number_format(SUM_PROGRESS)+'%');
		$('#top_usd').html(number_format(SUM_USD,2));
		$('#top_idr').html(number_format(SUM_IDR));
	}
<?php
if(!empty($restSO)) {
	echo (($restSO[0]->base_cur=="USD") ? " set_cur('USD')":" set_cur('IDR')");
}else{
	echo "set_cur('USD')";
};?>
</script>
