<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
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
					<label><input type="radio" name="base_cur" id="base_cur_usd" <?= ($getHeader[0]->base_cur=="USD"?" checked":"");?> value="USD" onclick="set_cur('USD')"> USD </label>
					<label><input type="radio" name="base_cur" id="base_cur_idr" <?= ($getHeader[0]->base_cur=="IDR"?" checked":"");?> value="IDR" onclick="set_cur('IDR')"> IDR </label>

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
						$no = 0;
						foreach($product AS $val => $valx){
							if($valx['qty'] <> '0'){
								$no++;
								$dataSum = 0;
								if($valx['qty'] <> 0){
									$dataSum	= $valx['cost'];
								}
								$SUM += $dataSum;
								
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
											<input type='hidden' name='detail_product[".$no."][total_deal_idr]' id='val_product_idr_".$no."'>
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
									echo "<td align='right'><input type='text' name='detail_product[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right productSUM productAll' id='val_product_usd_".$no."'><div id='product_usd_".$no."' class='product_usd clHide'>".number_format($dataSum,2)."</div></td>";
									echo "<td align='right'><div id='product_idr_".$no."'></div></td>";
								echo "</tr>";
							}
						}
						echo "<tr>";
							echo "<td class='sumAwal' colspan='11'><b>TOTAL PRODUCT</b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_product_usd_awal'>".number_format($SUM,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_product_usd'>".number_format($SUM,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_product_idr'></div></b></td>";
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
						if(!empty($data_material)){
							foreach($data_material AS $val => $valx){
								$no++;

								$price_satuan = $valx['price_total']/$valx['weight'];
								$price_total = $valx['qty_so'] * $price_satuan;

								$SUM_MAT += $price_total;
								
								$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
								$deal_usd = $price_total;
								if(!empty($get_deal_usd)){
									$deal_usd = $get_deal_usd[0]->total_deal_usd;
								}
								
								echo "<tr class='material_".$no."'>";
									echo "<td colspan='8'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['caregory_sub']))."
											<input type='hidden' name='detail_material[".$no."][id]' value='".$valx['id_milik']."'>
											<input type='hidden' name='detail_material[".$no."][price_satuan]' value='".$price_satuan."'>
											<input type='hidden' name='detail_material[".$no."][total_price]' value='".$price_total."'>
											<input type='hidden' name='detail_material[".$no."][total_deal_idr]' id='val_material_idr_".$no."'>
									
											</td>";
									echo "<td align='right'>".number_format($valx['qty_so'],2)."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['option_type']))."</td>";
									echo "<td align='right'>".number_format($price_satuan,2)."</td>";
									echo "<td align='right'><div id='material_usdori_".$no."'>".number_format($price_total,2)."</div></td>";
									echo "<td align='right'><input type='text' name='detail_material[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right materialSUM productAll' id='val_material_usd_".$no."' value='".number_format($deal_usd,2)."'><div id='material_usd_".$no."' class='material_usd clHide'>".number_format($deal_usd,2)."</div></td>";
									echo "<td align='right'><div id='material_idr_".$no."'></div></td>";
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
							echo "<td class='sumAwal' align='right'><b><div id='sum_material_usd'>".number_format($SUM_MAT,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_material_idr'></div></b></td>";
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
								
								$get_deal_usd = $this->db->select('total_deal_usd, desc')->get_where('billing_so_add', array('id_milik'=>$valx['id_milik2']))->result();
								$deal_usd = $price_total;
								$desc 		= (!empty($get_deal_usd))?$get_deal_usd[0]->desc:'';
								if(!empty($get_deal_usd)){
									$deal_usd = $get_deal_usd[0]->total_deal_usd;
								}
								
								echo "<tr class='material_".$no."'>";
									echo "<td colspan='4'>".get_name_acc($valx['caregory_sub'])."
											<input type='hidden' name='detail_aksesoris[".$no."][id]' value='".$valx['id_milik2']."'>
											<input type='hidden' name='detail_aksesoris[".$no."][category]' value='".$valx['category']."'>
											<input type='hidden' name='detail_aksesoris[".$no."][price_satuan]' value='".$price_satuan."'>
											<input type='hidden' name='detail_aksesoris[".$no."][total_price]' id='val_aksesoris_usdori_".$no."' value='".$price_total."'>
											<input type='hidden' name='detail_aksesoris[".$no."][total_deal_idr]' id='val_aksesoris_idr_".$no."'>
											
											</td>";
									echo "<td align='left' colspan='4'><input type='text' name='detail_aksesoris[".$no."][desc]' class='form-control input-sm text-left' value='".$desc."'></td>";
									echo "<td align='right'>".number_format($qty_so,2)."</td>";
									echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$satuan))."</td>";
									echo "<td align='right'>".number_format($price_satuan,2)."</td>";
									echo "<td align='right'><div id='aksesoris_usdori_".$no."'>".number_format($price_total,2)."</div></td>";
									echo "<td align='right'><input type='text' name='detail_aksesoris[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right aksesorisSUM productAll' id='val_aksesoris_usd_".$no."' value='".number_format($deal_usd,2)."'><div id='aksesoris_usd_".$no."' class='aksesoris_usd clHide'>".number_format($deal_usd,2)."</div></td>";
									echo "<td align='right'><div id='aksesoris_idr_".$no."' class='aksesoris_idr'></div></td>";
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
							echo "<td class='sumAwal' align='right'><b><div id='sum_acc_usd'>".number_format($SUM_ACC,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_acc_idr'></div></b></td>";
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
						if(!empty($data_eng)){
							foreach($data_eng AS $val => $valx){
								// if($valx['qty'] > 0){
									$no++;
									$SUM_ENG += $valx['price_total'];
									
									$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
									$deal_usd = $valx['price_total'];
									if(!empty($get_deal_usd)){
										$deal_usd = $get_deal_usd[0]->total_deal_usd;
									}
									
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
												<input type='hidden' name='detail_engine[".$no."][total_deal_idr]' id='val_engine_idr_".$no."'>
												</td>";
										echo "<td align='center'>".number_format($qty)."</td>";
										echo "<td align='center'>".strtoupper($valx['unit'])."</td>";
										echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
										echo "<td align='right'><div id='engine_usdori_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
										echo "<td align='right'><input type='text' name='detail_engine[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right engineSUM productAll' id='val_engine_usd_".$no."' value='".number_format($deal_usd,2)."'><div id='engine_usd_".$no."' class='engine_usd clHide'>".number_format($deal_usd,2)."</div></td>";
										echo "<td align='right'><div id='engine_idr_".$no."'></div></td>";
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
							echo "<td class='sumAwal' align='right'><b><div id='sum_eng_usd'>".number_format($SUM_ENG,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_eng_idr'></div></b></td>";
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
						if(!empty($data_pack)){
							foreach($data_pack AS $val => $valx){
								$no++;
								$SUM_PACK += $valx['price_total'];
								
								$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
								$deal_usd = $valx['price_total'];
								if(!empty($get_deal_usd)){
									$deal_usd = $get_deal_usd[0]->total_deal_usd;
								}
								
								echo "<tr class='packing_".$no."'>";
									echo "<td colspan='10'>".strtoupper($valx['caregory_sub'])."
											<input type='hidden' name='detail_packing[".$no."][id]' value='".$valx['id']."'>
											<input type='hidden' name='detail_packing[".$no."][satuan]' value='".$valx['option_type']."'>
											<input type='hidden' name='detail_packing[".$no."][total_price]' value='".$valx['price_total']."'>
											<input type='hidden' name='detail_packing[".$no."][total_deal_idr]' id='val_packing_idr_".$no."'>
											</td>";
									echo "<td align='center'>".strtoupper($valx['option_type'])."</td>";
									echo "<td align='right'><div id='packing_usdori_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
									echo "<td align='right'><input type='text' name='detail_packing[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right packingSUM productAll' id='val_packing_usd_".$no."' value='".number_format($deal_usd,2)."'><div id='packing_usd_".$no."' class='packing_usd clHide'>".number_format($deal_usd,2)."</div></td>";
									echo "<td align='right'><div id='packing_idr_".$no."'></div></td>";
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
							echo "<td class='sumAwal' align='right'><b><div id='sum_pack_usd'>".number_format($SUM_PACK,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_pack_idr'></div></b></td>";
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
						if(!empty($data_ship)){
							foreach($data_ship AS $val => $valx){
								$no++;
								$SUM_SHIP += $valx['price_total'];
								$Add = "";
								if($valx['category'] == 'lokal'){
									$Add = strtoupper(" (".get_name('cost_project_detail','kendaraan','id',$valx['id']).") DEST. ".get_name('cost_project_detail','area','id',$valx['id'])." - ".get_name('cost_project_detail','tujuan','id',$valx['id']));
								}
								
								$get_deal_usd = $this->db->select('total_deal_usd')->get_where('billing_so_add', array('id_milik'=>$valx['id']))->result();
								$deal_usd = $valx['price_total'];
								if(!empty($get_deal_usd)){
									$deal_usd = $get_deal_usd[0]->total_deal_usd;
								}
								
								
								echo "<tr class='shipping_".$no."'>";
									echo "<td colspan='9'>".strtoupper($valx['caregory_sub'].$Add)."
											<input type='hidden' name='detail_shipping[".$no."][id]' value='".$valx['id']."'>
											<input type='hidden' name='detail_shipping[".$no."][qty]' value='".$valx['qty']."'>
											<input type='hidden' name='detail_shipping[".$no."][price_satuan]' value='".$valx['price_total']/$valx['qty']."'>
											<input type='hidden' name='detail_shipping[".$no."][total_price]' value='".$valx['price_total']."'>
											<input type='hidden' name='detail_shipping[".$no."][total_deal_idr]' id='val_shipping_idr_".$no."'>
											</td>";
									echo "<td align='center'>".number_format($valx['qty'])."</td>";
									echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
									echo "<td align='right'><div id='shipping_usdori_".$no."'>".number_format($valx['price_total'],2)."</div></td>";
									echo "<td align='right'><input type='text' name='detail_shipping[".$no."][total_deal_usd]' data-no='".$no."' class='form-control input-sm text-right shippingSUM productAll' id='val_shipping_usd_".$no."' value='".number_format($deal_usd,2)."'><div id='shipping_usd_".$no."' class='shipping_usd clHide'>".number_format($deal_usd,2)."</div></td>";
									echo "<td align='right'><div id='shipping_idr_".$no."'></div></td>";
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
							echo "<td class='sumAwal' align='right'><b><div id='sum_ship_usd'>".number_format($SUM_SHIP,2)."</div></b></td>";
							echo "<td class='sumAwal' align='right'><b><div id='sum_ship_idr'></div></b></td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td colspan='14'>&nbsp</td>";
						echo "</tr>";
						
						echo "<tr>";
							echo "<td class='sumTotal'><b>TOTAL ALL</b></td>";
							echo "<td class='sumTotal' colspan='6' align='right'></td>";
							echo "<td class='sumTotal' align='center'></td>";
							echo "<td class='sumTotal' colspan='3'></td>";
							echo "<td class='sumTotal' align='right'><b><div id='sum_all_usd_awal'>".number_format($SUM + $SUM_ENG + $SUM_PACK + $SUM_SHIP + $SUM_MAT, 2)."</div></b></td>";
							echo "<td class='sumTotal' align='right'><b><div id='sum_all_usd'></div></b></td>";
							echo "<td class='sumTotal' align='right'><b><div id='sum_all_idr'></div></b></td>";
						echo "</tr>";
						$diskon = (!empty($restSO))?$restSO[0]->diskon:'';
						$total_deal_usd = (!empty($restSO))?number_format($restSO[0]->total_deal_usd,2):number_format($SUM + $SUM_ENG + $SUM_PACK + $SUM_SHIP + $SUM_MAT, 2);
						echo "<tr>";
							echo "<td class='sumTotal'></td>";
							echo "<td class='sumTotal' colspan='6' align='right'><b>DISKON (%)</b></td>";
							echo "<td class='sumTotal' align='center'><input readonly type='text' name='diskon' id='diskon' style='font-size:14px; font-weight:bold;' class='form-control input-sm text-center' value='".$diskon."' tabindex='-1'></td>";
							echo "<td class='sumTotal' colspan='3'></td>";
							echo "<td class='sumTotal' align='right'><b>NILAI DEAL</b></td>";
							echo "<td class='sumTotal' align='right'><input type='text' name='sum_all_usd_val' id='sum_all_usd_val' style='font-size:14px; font-weight:bold; padding-right: inherit;' class='form-control input-sm text-right maskMoney' placeholder='USD' value='".$total_deal_usd."'></td>";
							echo "<td class='sumTotal' align='right'><input type='text' name='sum_all_idr_val' id='sum_all_idr_val' style='font-size:14px; font-weight:bold; padding-right: inherit;' class='form-control input-sm text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' readonly></td>";
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
									
									<input type='hidden' name='kurs_usd_default' id='kurs_usd_default' value='".number_format(get_kurs('USD','IDR'))."'>
									<input type='hidden' name='sum_all_usd_awal' id='val_sum_all_usd_awal' value='".number_format($SUM + $SUM_ENG + $SUM_PACK + $SUM_SHIP + $SUM_MAT,2)."'>
									
									</td>";
						echo "</tr>";
						$kurs_usd_dipakai = (!empty($restSO))?$restSO[0]->kurs_usd_dipakai:get_kurs('USD','IDR');
						echo "<tr>";
							echo "<td><b>KURS/EDIT KURS</b></td>";
							echo "<td align='right' colspan='5'><b>1 USD = ".number_format(get_kurs('USD','IDR'))." IDR</b></td>";
							echo "<td align='center'><b></b></td>";
							echo "<td align='center'><input type='text' name='kurs_usd' id='kurs_usd' class='form-control input-sm text-right maskMoney' value='".number_format($kurs_usd_dipakai)."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='center' colspan='6'><input type='hidden' name='kurs_idr' id='kurs_idr' class='form-control input-sm text-right maskMoney9' value='".number_format(get_kurs('IDR','USD'),9)."' readonly></td>";
						echo "</tr>";
						// echo "<tr>";
							// echo "<td></td>";
							// echo "<td align='center'><b>1 IDR</b></td>";
							// echo "<td align='right'><b>".number_format(get_kurs('IDR','USD'),9)."</b></td>";
							// echo "<td align='center'><b>USD</b></td>";
							// echo "<td align='center'><b></b></td>";
							// echo "<td align='center'><input type='text' name='kurs_idr' id='kurs_idr' class='form-control input-sm text-right maskMoney9' value='".number_format(get_kurs('IDR','USD'),9)."' readonly></td>";
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
					$id = 0;
					if(!empty($data_top)){
						foreach($data_top AS $val => $valx){ $id++;
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
						<td align='right'><b><div id='top_usd'></div></b></td>
						<td align='right'><b><div id='top_idr'></div></b></td>
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
									echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][0][qty_delivery]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center maskMoney qty_".$no." qty_deliv' value='".$qty_delivery."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
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
											echo "<td align='left'><input type='text' name='detail_delivery[".$no."][detail][".$nox."][qty_delivery]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center maskMoney qty_".$no." qty_deliv' value='".$each[$nox]['qty_delivery']."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
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

<script>
	$(document).ready(function(){
		$(".autoNumeric2").autoNumeric('init', {mDec: '2', aPad: false});
		$('.maskMoney9').maskMoney({precision: 9});
		$('.maskMoney').maskMoney();
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
		$('.maskM, .productAll').maskMoney();
		$('#alert-max').hide();
		$('.chosen_select').chosen();
		change_product();
		change_kurs();
		change_material();
		change_aksesoris();
		change_engine();
		change_packing();
		change_shipping();
		sum_all(null);
		//, #diskon
		$(document).on('keyup', '#kurs_usd', function(e){
			change_by_diskon_kurs();
			change_kurs();
		});
		
		$(document).on('keyup', '#sum_all_usd_val', function(e){
			var nilai_deal = getNum($(this).val().split(",").join(""));
			var nilai_awal = getNum($('#sum_all_usd_awal').html().split(",").join(""));
			
			var diskon = (nilai_awal - nilai_deal) / nilai_awal * 100;
			
			$('#diskon').val(number_format(diskon,2));

			change_by_diskon_kurs('tanda');
			change_kurs();
		});
		
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
					$('.maskM').maskMoney();
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
		
		$(document).on('keyup', '.productSUM', function(){
			var a 		= $(this).data('no');
			changeProduct(a);
		});

		$(document).on('keyup', '.materialSUM', function(){
			var a 		= $(this).data('no');
			changeMaterial(a);
		});

		$(document).on('keyup', '.aksesorisSUM', function(){
			var a 		= $(this).data('no');
			changeAksesoris(a);
		});

		$(document).on('keyup', '.engineSUM', function(){
			var a 		= $(this).data('no');
			changeEngine(a);
		});

		$(document).on('keyup', '.packingSUM', function(){
			var a 		= $(this).data('no');
			changePacking(a);
		});

		$(document).on('keyup', '.shippingSUM', function(){
			var a 		= $(this).data('no');
			changeShipping(a);
		});
		
		$(document).on('click','.plus', function(){
			var no 		= $(this).data('id');
			// alert($(this).parent().parent().find("td:nth-child(1)").attr('rowspan'));return false;
			var kolom	= parseFloat($(this).parent().parent().find("td:nth-child(1)").attr('rowspan')) + 1;
			
			$(this).parent().parent().find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8)").attr('rowspan', kolom);
			
			var Rows	= "<tr>";
				Rows	+= "<td align='center'><input type='text' name='detail_delivery["+no+"][detail]["+kolom+"][qty_delivery]' data-no='"+no+"' data-no2='"+kolom+"' class='form-control input-sm text-center maskMoney qty_"+no+" qty_deliv' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
				Rows	+= "<td align='center'><input type='text' name='detail_delivery["+no+"][detail]["+kolom+"][delivery_date]' class='form-control text-center input-sm text-center datepicker' readonly placeholder='Delivery Date'></td>";
				Rows	+= "<td align='center'>";
				Rows	+= "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='"+no+"'><i class='fa fa-trash'></i></button>";
				Rows	+= "</td>";
				Rows	+= "</tr>";
			// alert(Rows);
			$(this).parent().parent().after(Rows);
			
			$('.maskMoney').maskMoney();
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

	function change_product(tanda=null){
		//kurs
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;

		var SUM_PRODUCT_USD = 0;
		var SUM_PRODUCT_IDR = 0;
		$('.product_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			
			var nilai 				= Number($('#product_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#product_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#product_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_product_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_product_idr_'+nomor).val(nilai_idr);
			
			SUM_PRODUCT_USD += nilai_usd_diskon;
			SUM_PRODUCT_IDR += nilai_idr;
		});

		$('#sum_product_usd').html(number_format(SUM_PRODUCT_USD,2));
		$('#sum_product_idr').html(number_format(SUM_PRODUCT_IDR));

		$('#product_usd').val(SUM_PRODUCT_USD);
		$('#product_idr').val(SUM_PRODUCT_IDR);

		// sum_all(tanda);
	}
	
	function change_material(tanda=null){
		//kurs
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;

		var SUM_MATERIAL_USD = 0;
		var	SUM_MATERIAL_IDR = 0;
		$('.material_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#material_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#material_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#material_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_material_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_material_idr_'+nomor).val(nilai_idr);
			
			SUM_MATERIAL_USD += nilai_usd_diskon;
			SUM_MATERIAL_IDR += nilai_idr;
		});

		$('#sum_material_usd').html(number_format(SUM_MATERIAL_USD,2));
		$('#sum_material_idr').html(number_format(SUM_MATERIAL_IDR));

		$('#material_usd').val(SUM_MATERIAL_USD);
		$('#material_idr').val(SUM_MATERIAL_IDR);

		// sum_all(tanda);
	}

	function change_aksesoris(tanda=null){
		//kurs
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;

		var SUM_AKSESORIS_AWAL = 0;
		var SUM_AKSESORIS_USD = 0;
		var	SUM_AKSESORIS_IDR = 0;
		$('.aksesoris_idr').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#val_aksesoris_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai_usd - (nilai_usd * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			// $('#aksesoris_usdori_'+nomor).html(number_format(nilai_usd,2));
			$('#aksesoris_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#aksesoris_idr_'+nomor).html(number_format(nilai_idr));
			
			// $('#val_aksesoris_usdori_'+nomor).val(number_format(nilai_usd,2));
			$('#val_aksesoris_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_aksesoris_idr_'+nomor).val(nilai_idr);
			
			// SUM_AKSESORIS_AWAL += nilai_usd;
			SUM_AKSESORIS_USD += nilai_usd_diskon;
			SUM_AKSESORIS_IDR += nilai_idr;
		});

		// $('#sum_acc_usd_awal').html(number_format(SUM_AKSESORIS_AWAL,2));
		$('#sum_acc_usd').html(number_format(SUM_AKSESORIS_USD,2));
		$('#sum_acc_idr').html(number_format(SUM_AKSESORIS_IDR));

		// $('#acc_usd_awal').val(SUM_AKSESORIS_AWAL);
		$('#acc_usd').val(SUM_AKSESORIS_USD);
		$('#acc_idr').val(SUM_AKSESORIS_IDR);

		// sum_all(tanda);
	}

	function change_engine(tanda=null){
		//kurs
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;

		var SUM_ENGINE_USD = 0;
		var	SUM_ENGINE_IDR = 0;
		$('.engine_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#engine_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#engine_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#engine_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_engine_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_engine_idr_'+nomor).val(nilai_idr);
			
			SUM_ENGINE_USD += nilai_usd_diskon;
			SUM_ENGINE_IDR += nilai_idr;
		});

		$('#sum_eng_usd').html(number_format(SUM_ENGINE_USD,2));
		$('#sum_eng_idr').html(number_format(SUM_ENGINE_IDR));

		$('#eng_usd').val(SUM_ENGINE_USD);
		$('#eng_idr').val(SUM_ENGINE_IDR);

		// sum_all(tanda);
	}

	function change_packing(tanda=null){
		//kurs
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;

		var SUM_PACKING_USD = 0;
		var	SUM_PACKING_IDR = 0;
		$('.packing_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#packing_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#packing_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#packing_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_packing_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_packing_idr_'+nomor).val(nilai_idr);
			
			SUM_PACKING_USD += nilai_usd_diskon;
			SUM_PACKING_IDR += nilai_idr;
		});

		$('#sum_pack_usd').html(number_format(SUM_PACKING_USD,2));
		$('#sum_pack_idr').html(number_format(SUM_PACKING_IDR));

		$('#pack_usd').val(SUM_PACKING_USD);
		$('#pack_idr').val(SUM_PACKING_IDR);

		// sum_all(tanda);
	}

	function change_shipping(tanda=null){
		//kurs
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;

		var SUM_SHIPPING_USD = 0;
		var	SUM_SHIPPING_IDR = 0;
		$('.shipping_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#shipping_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#shipping_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#shipping_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_shipping_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_shipping_idr_'+nomor).val(nilai_idr);
			
			SUM_SHIPPING_USD += nilai_usd_diskon;
			SUM_SHIPPING_IDR += nilai_idr;
		});

		$('#sum_ship_usd').html(number_format(SUM_SHIPPING_USD,2));
		$('#sum_ship_idr').html(number_format(SUM_SHIPPING_IDR));

		$('#ship_usd').val(SUM_SHIPPING_USD);
		$('#ship_idr').val(SUM_SHIPPING_IDR);

		// sum_all(tanda);
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
	
	function changeProduct(a){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var valuex		= getNum($('#val_product_usd_'+a).val().split(",").join(""));
		var total_kurs	= kurs * valuex;
		$('#product_idr_'+a).html(number_format(total_kurs));
		$('#val_product_idr_'+a).val(number_format(total_kurs));
		
		//PRODUCT
		var SUM_PRODUCT_USD = 0;
		var SUM_PRODUCT_IDR = 0;
		$('.productSUM').each(function(){
			var nomor 				= $(this).data('no');
			var nilai 				= Number($('#val_product_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			
			$('#product_usd_'+nomor).html(number_format(nilai_usd,2));
			$('#product_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_product_idr_'+nomor).val(nilai_idr);
			
			SUM_PRODUCT_USD += nilai_usd;
			SUM_PRODUCT_IDR += nilai_idr;
		});
		$('#sum_product_usd').html(number_format(SUM_PRODUCT_USD,2));
		$('#sum_product_idr').html(number_format(SUM_PRODUCT_IDR));
		$('#product_usd').val(SUM_PRODUCT_USD);
		$('#product_idr').val(SUM_PRODUCT_IDR);
		
		sum_all();
	}

	function changeMaterial(a){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var valuex		= getNum($('#val_material_usd_'+a).val().split(",").join(""));
		var total_kurs	= kurs * valuex;
		$('#material_idr_'+a).html(number_format(total_kurs));
		$('#val_material_idr_'+a).val(number_format(total_kurs));
		
		//MATERIAL
		var SUM_MATERIAL_USD = 0;
		var	SUM_MATERIAL_IDR = 0;
		$('.materialSUM').each(function(){
			var nomor 				= $(this).data('no');
			var nilai 				= Number($('#val_material_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			// console.log(nilai_idr);
			$('#material_usd_'+nomor).html(number_format(nilai_usd,2));
			$('#material_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_material_idr_'+nomor).val(nilai_idr);
			
			SUM_MATERIAL_USD += nilai_usd;
			SUM_MATERIAL_IDR += nilai_idr;
		});
		$('#sum_material_usd').html(number_format(SUM_MATERIAL_USD,2));
		$('#sum_material_idr').html(number_format(SUM_MATERIAL_IDR));
		$('#material_usd').val(SUM_MATERIAL_USD);
		$('#material_idr').val(SUM_MATERIAL_IDR);
		
		sum_all();
	}

	function changeAksesoris(a){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var valuex		= getNum($('#val_aksesoris_usd_'+a).val().split(",").join(""));
		var total_kurs	= kurs * valuex;
		$('#aksesoris_idr_'+a).html(number_format(total_kurs));
		$('#val_aksesoris_idr_'+a).val(number_format(total_kurs));
		//AKSESORIS
		var SUM_AKSESORIS_USD = 0;
		var	SUM_AKSESORIS_IDR = 0;
		$('.aksesorisSUM').each(function(){
			var nomor 				= $(this).data('no');
			var nilai 				= Number($('#val_aksesoris_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			
			$('#aksesoris_usd_'+nomor).html(number_format(nilai_usd,2));
			$('#aksesoris_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_aksesoris_idr_'+nomor).val(nilai_idr);
			
			SUM_AKSESORIS_USD += nilai_usd;
			SUM_AKSESORIS_IDR += nilai_idr;
		});
		$('#sum_acc_usd').html(number_format(SUM_AKSESORIS_USD,2));
		$('#sum_acc_idr').html(number_format(SUM_AKSESORIS_IDR));
		$('#acc_usd').val(SUM_AKSESORIS_USD);
		$('#acc_idr').val(SUM_AKSESORIS_IDR);
		
		sum_all();
	}

	function changeEngine(a){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var valuex		= getNum($('#val_engine_usd_'+a).val().split(",").join(""));
		var total_kurs	= kurs * valuex;
		$('#engine_idr_'+a).html(number_format(total_kurs));
		$('#val_engine_idr_'+a).val(number_format(total_kurs));
		//ENGINE
		var SUM_ENGINE_USD = 0;
		var	SUM_ENGINE_IDR = 0;
		$('.engineSUM').each(function(){
			var nomor 				= $(this).data('no');
			var nilai 				= Number($('#val_engine_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			
			$('#engine_usd_'+nomor).html(number_format(nilai_usd,2));
			$('#engine_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_engine_idr_'+nomor).val(nilai_idr);
			
			SUM_ENGINE_USD += nilai_usd;
			SUM_ENGINE_IDR += nilai_idr;
		});
		$('#sum_eng_usd').html(number_format(SUM_ENGINE_USD,2));
		$('#sum_eng_idr').html(number_format(SUM_ENGINE_IDR));

		$('#eng_usd').val(SUM_ENGINE_USD);
		$('#eng_idr').val(SUM_ENGINE_IDR);
		
		sum_all();
	}

	function changePacking(a){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var valuex		= getNum($('#val_packing_usd_'+a).val().split(",").join(""));
		var total_kurs	= kurs * valuex;
		$('#packing_idr_'+a).html(number_format(total_kurs));
		$('#val_packing_idr_'+a).val(number_format(total_kurs));
		//PACKING
		var SUM_PACKING_USD = 0;
		var	SUM_PACKING_IDR = 0;
		$('.packingSUM').each(function(){
			var nomor 				= $(this).data('no');
			var nilai 				= Number($('#val_packing_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			
			$('#packing_usd_'+nomor).html(number_format(nilai_usd,2));
			$('#packing_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_packing_idr_'+nomor).val(nilai_idr);
			
			SUM_PACKING_USD += nilai_usd;
			SUM_PACKING_IDR += nilai_idr;
		});
		$('#sum_pack_usd').html(number_format(SUM_PACKING_USD,2));
		$('#sum_pack_idr').html(number_format(SUM_PACKING_IDR));

		$('#pack_usd').val(SUM_PACKING_USD);
		$('#pack_idr').val(SUM_PACKING_IDR);
		
		sum_all();
	}

	function changeShipping(a){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var valuex		= getNum($('#val_shipping_usd_'+a).val().split(",").join(""));
		var total_kurs	= kurs * valuex;
		$('#shipping_idr_'+a).html(number_format(total_kurs));
		$('#val_shipping_idr_'+a).val(number_format(total_kurs));
		//SHIPPING
		var SUM_SHIPPING_USD = 0;
		var	SUM_SHIPPING_IDR = 0;
		$('.shippingSUM').each(function(){
			var nomor 				= $(this).data('no');
			var nilai 				= Number($('#val_shipping_usd_'+nomor).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			
			$('#shipping_usd_'+nomor).html(number_format(nilai_usd,2));
			$('#shipping_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_shipping_idr_'+nomor).val(nilai_idr);
			
			SUM_SHIPPING_USD += nilai_usd;
			SUM_SHIPPING_IDR += nilai_idr;
		});
		$('#sum_ship_usd').html(number_format(SUM_SHIPPING_USD,2));
		$('#sum_ship_idr').html(number_format(SUM_SHIPPING_IDR));

		$('#ship_usd').val(SUM_SHIPPING_USD);
		$('#ship_idr').val(SUM_SHIPPING_IDR);

		sum_all();
	}
	
	function sum_all(tanda = null){
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		
		var sum_product_usd_awal 	= getNum($('#sum_product_usd_awal').html().split(",").join(""));
		var sum_material_usd_awal 	= getNum($('#sum_material_usd_awal').html().split(",").join(""));
		var sum_acc_usd_awal 		= getNum($('#sum_acc_usd_awal').html().split(",").join(""));
		var sum_eng_usd_awal 		= getNum($('#sum_eng_usd_awal').html().split(",").join(""));
		var sum_pack_usd_awal 		= getNum($('#sum_pack_usd_awal').html().split(",").join(""));
		var sum_ship_usd_awal 		= getNum($('#sum_ship_usd_awal').html().split(",").join(""));

		var total = sum_product_usd_awal + sum_material_usd_awal + sum_acc_usd_awal + sum_eng_usd_awal + sum_pack_usd_awal + sum_ship_usd_awal;

		$('#sum_all_usd_awal').html(number_format(total,2));
		$('#val_sum_all_usd_awal').val(number_format(total,2));

		var SUM_PRODUCT_DEAL_USD = 0;
		var SUM_PRODUCT_DEAL_IDR = 0;
		$('.productAll').each(function(){
			var nilai 				= Number($(this).val().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_idr			= nilai_usd * kurs;
			
			SUM_PRODUCT_DEAL_USD += nilai_usd;
			SUM_PRODUCT_DEAL_IDR += nilai_idr;

			// console.log(nilai_usd)
		});
		
		$('#sum_all_usd').html(number_format(SUM_PRODUCT_DEAL_USD,2));
		$('#sum_all_idr').html(number_format(SUM_PRODUCT_DEAL_IDR));
		console.log(SUM_PRODUCT_DEAL_USD)
		if(tanda == null){
			$('#sum_all_usd_val').val(number_format(SUM_PRODUCT_DEAL_USD,2));
		}

		// $('#sum_all_usd_val').val(number_format(SUM_PRODUCT_DEAL_USD,2));
		$('#sum_all_idr_val').val(number_format(SUM_PRODUCT_DEAL_IDR));
		
		var nilai_awal = getNum($('#sum_all_usd_awal').html().split(",").join(""));
		var diskon = (nilai_awal - SUM_PRODUCT_DEAL_USD) / nilai_awal * 100;
		
		$('#diskon').val(number_format(diskon,2));
		change_kurs();
	}
	
	function change_kurs(){
		var total		= getNum($('#sum_all_usd_val').val().split(",").join(""));
		var kurs		= getNum($('#kurs_usd').val().split(",").join(""));
		var current  	= 'USD';
		var SUM_PROGRESS = 0;
		var SUM_USD = 0;
		var SUM_IDR = 0;
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

	function change_by_diskon_kurs(tanda = null){
		var kurs_usd 	= getNum($('#kurs_usd').val().split(",").join(""));
		var kurs_idr 	= getNum($('#kurs_idr').val().split(",").join(""));
		var diskon 		= getNum($('#diskon').val().split(",").join(""));
		var diskon_d 	= diskon / 100;
		
		var SUM_PRODUCT_USD = 0;
		var SUM_PRODUCT_IDR = 0;
		$('.product_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			
			var nilai 				= Number($('#product_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#product_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#product_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_product_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_product_idr_'+nomor).val(nilai_idr);
			
			SUM_PRODUCT_USD += nilai_usd_diskon;
			SUM_PRODUCT_IDR += nilai_idr;
		});

		$('#sum_product_usd').html(number_format(SUM_PRODUCT_USD,2));
		$('#sum_product_idr').html(number_format(SUM_PRODUCT_IDR));

		$('#product_usd').val(SUM_PRODUCT_USD);
		$('#product_idr').val(SUM_PRODUCT_IDR);

		var SUM_MATERIAL_USD = 0;
		var	SUM_MATERIAL_IDR = 0;
		$('.material_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#material_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#material_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#material_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_material_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_material_idr_'+nomor).val(nilai_idr);
			
			SUM_MATERIAL_USD += nilai_usd_diskon;
			SUM_MATERIAL_IDR += nilai_idr;
		});

		$('#sum_material_usd').html(number_format(SUM_MATERIAL_USD,2));
		$('#sum_material_idr').html(number_format(SUM_MATERIAL_IDR));

		$('#material_usd').val(SUM_MATERIAL_USD);
		$('#material_idr').val(SUM_MATERIAL_IDR);
		
		var SUM_AKSESORIS_USD = 0;
		var	SUM_AKSESORIS_IDR = 0;
		$('.aksesoris_idr').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#aksesoris_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai_usd - (nilai_usd * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#aksesoris_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#aksesoris_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_aksesoris_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_aksesoris_idr_'+nomor).val(nilai_idr);
			
			SUM_AKSESORIS_USD += nilai_usd_diskon;
			SUM_AKSESORIS_IDR += nilai_idr;
		});

		$('#sum_acc_usd').html(number_format(SUM_AKSESORIS_USD,2));
		$('#sum_acc_idr').html(number_format(SUM_AKSESORIS_IDR));

		$('#acc_usd').val(SUM_AKSESORIS_USD);
		$('#acc_idr').val(SUM_AKSESORIS_IDR);

		var SUM_ENGINE_USD = 0;
		var	SUM_ENGINE_IDR = 0;
		$('.engine_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#engine_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#engine_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#engine_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_engine_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_engine_idr_'+nomor).val(nilai_idr);
			
			SUM_ENGINE_USD += nilai_usd_diskon;
			SUM_ENGINE_IDR += nilai_idr;
		});

		$('#sum_eng_usd').html(number_format(SUM_ENGINE_USD,2));
		$('#sum_eng_idr').html(number_format(SUM_ENGINE_IDR));

		$('#eng_usd').val(SUM_ENGINE_USD);
		$('#eng_idr').val(SUM_ENGINE_IDR);

		var SUM_PACKING_USD = 0;
		var	SUM_PACKING_IDR = 0;
		$('.packing_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#packing_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#packing_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#packing_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_packing_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_packing_idr_'+nomor).val(nilai_idr);
			
			SUM_PACKING_USD += nilai_usd_diskon;
			SUM_PACKING_IDR += nilai_idr;
		});

		$('#sum_pack_usd').html(number_format(SUM_PACKING_USD,2));
		$('#sum_pack_idr').html(number_format(SUM_PACKING_IDR));

		$('#pack_usd').val(SUM_PACKING_USD);
		$('#pack_idr').val(SUM_PACKING_IDR);

		var SUM_SHIPPING_USD = 0;
		var	SUM_SHIPPING_IDR = 0;
		$('.shipping_usd').each(function(){
			var get_id 				= $(this).attr('id');
			var split_id			= get_id.split('_');
			var nomor 				= split_id[2];
			// console.log(nomor)
			var nilai 				= Number($('#shipping_usdori_'+nomor).html().split(",").join(""));
			var nilai_usd			= nilai;
			var nilai_usd_diskon	= nilai - (nilai * diskon_d);
			var nilai_idr			= nilai_usd_diskon * kurs_usd;
			
			$('#shipping_usd_'+nomor).html(number_format(nilai_usd_diskon,2));
			$('#shipping_idr_'+nomor).html(number_format(nilai_idr));
			
			$('#val_shipping_usd_'+nomor).val(number_format(nilai_usd_diskon,2));
			$('#val_shipping_idr_'+nomor).val(nilai_idr);
			
			SUM_SHIPPING_USD += nilai_usd_diskon;
			SUM_SHIPPING_IDR += nilai_idr;
		});

		$('#sum_ship_usd').html(number_format(SUM_SHIPPING_USD,2));
		$('#sum_ship_idr').html(number_format(SUM_SHIPPING_IDR));

		$('#ship_usd').val(SUM_SHIPPING_USD);
		$('#ship_idr').val(SUM_SHIPPING_IDR);

		//SUM
		var SUM_PRODUCT_DEAL_USD = SUM_PRODUCT_USD + SUM_MATERIAL_USD + SUM_AKSESORIS_USD + SUM_ENGINE_USD + SUM_PACKING_USD + SUM_SHIPPING_USD;
		var SUM_PRODUCT_DEAL_IDR = SUM_PRODUCT_IDR + SUM_MATERIAL_IDR + SUM_AKSESORIS_IDR + SUM_ENGINE_IDR + SUM_PACKING_IDR + SUM_SHIPPING_IDR;
		$('#sum_all_usd').html(number_format(SUM_PRODUCT_DEAL_USD,2));
		$('#sum_all_idr').html(number_format(SUM_PRODUCT_DEAL_IDR));
		
		if(tanda == null){
			$('#sum_all_usd_val').val(number_format(SUM_PRODUCT_DEAL_USD,2));
		}
		$('#sum_all_idr_val').val(number_format(SUM_PRODUCT_DEAL_IDR));

	}
	function set_cur(currency){
	}
</script>
