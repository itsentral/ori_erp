<?php
// print_r($id_bq);
// exit;

$CH = substr($id_bq, 6,2);
$no_ipp = substr($id_bq, 3,10);
if($CH == '19' OR $CH == '20'){
	$no_ipp = substr($id_bq, 3,9);
}
	
$data_header = $this->db->query("SELECT * FROM so_bf_header WHERE no_ipp ='".$no_ipp."'")->row();
$cust        =  $this->db->query("SELECT * FROM production WHERE no_ipp = '".$no_ipp."'")->row(); 
$alamat_cust =  $this->db->query("SELECT * FROM customer WHERE id_customer = '".$cust->id_customer."'")->row();
$so          =  $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$no_ipp."'")->row();
$top        =  $this->db->query("SELECT * FROM billing_top WHERE no_po = '".$no_ipp."'")->row();  

$SUM=0;
$SUM1=0;
$SUM2=0;
$SUM3=0;
$SUM4=0;
$SUM_MAT=0;
$SUM_NONFRP=0;
?>
<form method="POST" id="form_proses">
	<div class="nav-tabs-salesorder">
		<div class="tab-content">
			<div class="tab-pane active" id="salesorder">
				<div class="box box-primary">
					<div class="box-body">
						<div class="col-sm-6 form-horizontal">
							<div class="row">
								<div class="form-group">
									<label for="no_ipp" class="col-sm-4 control-label">IPP Number </font></label>
									<div class="col-sm-8">
										<input type="hidden" name="id" id="id" value="<?php echo $id ?>" class="form-control input-sm" readonly>
										<input type="text" name="no_ipp" id="no_ipp" value="<?php echo $data_header->no_ipp ?>" class="form-control input-md" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="no_ipp" class="col-sm-4 control-label">SO Number </font></label>
									<div class="col-sm-8">
										<input type="text" name="no_so" id="no_so" value="<?php echo $data_header->so_number ?>" class="form-control input-md" readonly>
									</div>
								</div>
							</div>
							<div class="row">
							  <div class="form-group ">
								<?php
								$tglinv=date('Y-m-d');
								?>
								<label for="tgl_inv" class="col-sm-4 control-label">Invoice Date</label>
								<div class="col-sm-8">
								 
									 <!--input type="text" name="tanggal_invoice" id="tgl_inv" class="form-control input-sm datepicker" value="<?php // echo $tglinv?>"-->
									<input type="text" name="tanggal_invoice" id="tgl_inv" class="form-control input-md" value="<?php echo date('Y-m-d') ?>" readonly>
								 
								</div>
							  </div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-sm-4 control-label">Customer Name </font></label>
									<div class="col-sm-8">
										<input type="hidden" name="id_customer" id="id_customer" class="form-control input-md" value="<?php  echo $cust->id_customer?>" readonly>
										<input type="text" name="nm_customer" id="nm_customer" class="form-control input-md" value="<?php  echo $cust->nm_customer?>" readonly>
								   </div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="alamat" class="col-sm-4 control-label">Customer Address </font></label>
									<div class="col-sm-8" >
										<textarea name="alamatcustomer" class="form-control input-md" id="alamat" rows='3' readonly><?php echo $alamat_cust->alamat ?></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
								  <label for="jenis_invoice" class="col-sm-4 control-label">Invoice Type </font></label>
									<div class="col-sm-8">
										<select id="jenis_invoice" name="jenis_invoice" class="form-control input-md" style="width: 100%;" tabindex="-1" required readonly>
											<option value="uang muka" <?php echo ($jenis == 'uang muka') ? "selected": "" ?>>Uang Muka</option>
											<option value="progress"  <?php echo ($jenis == 'progress') ? "selected": "" ?>>Progress</option>
											<option value="retensi"   <?php echo ($jenis == 'retensi') ? "selected": "" ?>>Retensi</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="ppnselect" class="col-sm-4 control-label">PPN </font></label>
									<div class="col-sm-8">
										<select id="ppnselect" name="ppnselect" class="form-control input-sm chosen_select" style="width: 100%;" required>
											<option value="0">SELECT AN OPTION</option>
											<option value="1">PPN</option>
											<option value="0">NON PPN</option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-6 form-horizontal">
							<div class="form-group ">
								<label for="idsalesman" class="col-sm-4 control-label">PO Number </label>
								<div class="col-sm-8" style="padding-top: 8px;">
									<input type="text" name="nomor_po" class="form-control input-md" id="nomor_po" value="<?= $so->no_po;?>">
									<input type="hidden" id='wilayah' name="wilayah" class="form-control input-sm" value="<?= get_name('so_number','wilayah','so_number', $data_header->so_number);?>">
								</div>
							</div>
							<div class="form-group ">
								<label for="idsalesman" class="col-sm-4 control-label">F. No Faktur </label>
								<div class="col-sm-8" style="padding-top: 8px;">
									<input type="text" name="nomor_faktur" class="form-control input-md" id="nomor_faktur" value="<?php //echo " ".$data_cust->npwp?>">
								</div>
							</div>
							<div class="form-group ">
								<label for="idsalesman" class="col-sm-4 control-label">No Pajak </label>
								<div class="col-sm-8" style="padding-top: 8px;">
									<input type="text" name="nomor_pajak" class="form-control input-md" id="nomor_pajak" value="<?php //echo " ".$data_cust->npwp?>">
								</div>
							</div>
							<div class="form-group ">
								<label for="tgldo" class="col-sm-4 control-label">Term Payment </label>
								<div class="col-sm-8" style="padding-top: 8px;">
									<select id="top" name="top" class="form-control input-sm chosen_select" style="width: 100%;" required>
										<option value="0">SELECT AN TOP</option>
										<?php
										foreach($list_top AS $val => $valx){
											echo "<option value='".$valx['data1']."'>".strtoupper($valx['name'])."</option>";
										}
										?>
									</select>
									
									
									<!--<input type="text" name="top" class="form-control input-sm" id="top" value="<?php // echo $records[0]['detail_data'][0]['top']; ?>" >-->
								</div>
							</div>
							<div class="form-group ">
								<label for="tgldo" class="col-sm-4 control-label">Kurs </label>
								<div class="col-sm-8" style="padding-top: 8px;">
									<input type="text" name="kurs" class="form-control input-md" id="kurs" value="<?= $so->kurs_usd_dipakai;?>" > 
								</div>
							</div>
							<?php 
							$um1 = $so->uang_muka_persen;
							
							if($jenis == 'uang muka' && $um1 < 1){?>
								<div class="form-group">
									  <label for="jenis_invoice" class="col-sm-4 control-label">Persentase UM I</font></label>
									  <div class="col-sm-8">
										 <input type="text" name="um_persen" id="um_persen" class="form-control input-md autoNumeric" value="<?php  echo $top->progress ?>">
									  </div>
								</div>
							<?php 
							}
							
							if($jenis == 'uang muka' && $um1 !=0){?>
								<div class="form-group">
										<label for="jenis_invoice" class="col-sm-4 control-label">Persentase UM I</font></label>
										<div class="col-sm-8">
											<input type="text" name="um_persen" id="um_persen" class="form-control input-md persen" value="<?php  echo $so->uang_muka_persen ?>" readonly>
										</div>
								</div>
								<div class="form-group">
										<label for="jenis_invoice" class="col-sm-4 control-label">Persentase UM II</font></label>
										<div class="col-sm-8">
											<input type="text" name="um_persen2" id="um_persen2" class="form-control input-md" value="0">
										</div>
								</div>
							
							<?php 
							}
							if($jenis == 'progress'){?>
								<div class="form-group">
									  <label for="jenis_invoice" class="col-sm-4 control-label">Persentase Progress</font></label>
									  <div class="col-sm-8">
										 <input type="text" name="umpersen" id="umpersen" value="<?php  echo $so->persentase_progress ?>" class="form-control input-md">
									  </div>
								</div>
							<?php 
							}
							
							if ($jenis == 'progress'){?>
								<div class="form-group">
									<label for="jenis_invoice" class="col-sm-4 control-label">Uang Muka I (%)<font></label>
									<div class="col-sm-8">
									<input type="text" name="persen" id="persen" class="form-control input-md persen" value="<?php  echo $so->uang_muka_persen ?>" readonly> 
									</div>
								</div>
								<div class="form-group">
									<label for="jenis_invoice" class="col-sm-4 control-label">Uang Muka II (%)<font></label>
									<div class="col-sm-8">
										<input type="text" name="persen2" id="persen2" class="form-control input-md persen2" value="<?php  echo $so->uang_muka_persen2 ?>" readonly> 
										<input type="hidden" name="um_persen2" id="um_persen2" class="form-control input-md" value="<?php  echo $so->uang_muka_persen2 ?>">
									</div>
								</div>
							<?php 
							}?>
						</div> 
					</div>
				
					<?php     
					if($jenis=='retensi')
					{
						?>
						<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<thead>
								<tr>
									<td class="text-left headX HeaderHr" colspan='12'><b>RETENSI</b></td>
								</tr>
								<tr class='bg-bluexyz'>
									<th class="text-center" width='8%'></th>
									<th class="text-center" colspan='9'>Item Product</th>
									<th class="text-center">Unit</th> 
									<th class="text-center">Total Price</th> 
								</tr>
							</thead>
							<tbody>
								<?php
								$no3=0;
								$SUM3=0;
								$numb8=0;
								foreach($getTruck AS $val => $valx){
									$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
									$SUM3 	+= $so->retensi;
									$no3++;
									$numb8++;
									?>
									<tr id='tr7_<?= $numb8;?>' >
										<td align='center'>
											<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow8(<?= $numb8;?>)'><i class="fa fa-times"></i>
											</a></span>
										</td>
										<td colspan='9'>
											<?php
											$material_name8= strtoupper('RETENSI');
											?>
											<input type="text" class="form-control" id="material_name8" name="data8[<?php echo $numb8 ?>][material_name8]" value="<?php echo set_value('material_name8', isset($material_name8) ? $material_name8 : ''); ?>" placeholder="Automatic" readonly >
										</td>
										<td>
											<?php
											$unit8= strtoupper('-');
											?>
											<input type="text" class="form-control" id="unit8" name="data8[<?php echo $numb8 ?>][unit8]" value="<?php echo set_value('unit8', isset($unit8) ? $unit8 : ''); ?>" placeholder="Automatic" readonly >
										</td>
										<td>
											<?php
											$harga_tot8= number_format($so->retensi,2);
											$harga_tot8_hidden= round($so->retensi,2);
											?>
											<input type="text" class="form-control harga_tot8" id="harga_tot8<?php echo $numb8 ?>" data-nomor='<?php echo $numb8 ?>' name="data8[<?php echo $numb8 ?>][harga_tot8]" value="<?php echo set_value('harga_tot8', isset($harga_tot8) ? $harga_tot8 : ''); ?>" placeholder="Automatic" >
											<input type="hidden" class="form-control amount8" id="harga_tot8_hidden<?php echo $numb8 ?>" name="data8[<?php echo $numb8 ?>][harga_tot8_hidden]" value="<?php echo set_value('harga_tot8_hidden', isset($harga_tot8_hidden) ? $harga_tot8_hidden : ''); ?>" placeholder="Automatic" readonly >
									
										</td>
									</tr>
								<?php	
								}
								?>
								<tr id='tr7X class='FootColor'>
									<td colspan='11'><b>TOTAL RETENSI</b></td>
									<td align="right">
										<?php
											$total_trucking= number_format($SUM3,2);
											$total_trucking_hidden= round($SUM3,2);
											?>
										<input type="text" class="form-control result8" id="total_trucking<?php echo $numb8 ?>" name="total_trucking" value="<?php echo set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" placeholder="Automatic" readonly >
										<input type="hidden" class="form-control result8_hidden" id="total_trucking_hidden<?php echo $numb8 ?>" name="total_trucking_hidden" value="<?php echo set_value('total_trucking_hidden', isset($total_trucking_hidden) ? $total_trucking_hidden : ''); ?>" placeholder="Automatic" readonly >
									</td>
								</tr>	
							</tbody>
						</table>
						<?php
					}
					else
					{
						?>
						<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<thead>
								<tr>
									<td class="text-left headX HeaderHr" colspan='14'><b>PRODUCT</b></td>
								</tr>
								<tr class='bg-bluexyz'>
									<th class="text-center" width='2%'></th>
									<th class="text-center" colspan='2'>Item Product</th>
									<th class="text-center" width='7%'>Dim 1</th>
									<th class="text-center" width='7%'>Dim 2</th>
									<th class="text-center" width='6%'>Lin</th>
									<th class="text-center" width='6%'>Pre</th>
									<th class="text-center" width='12%'>Specification</th>
									<th class="text-center" width='8%'>Unit Price</th>
									<th class="text-center" width='6%'>Qty Total</th>
									<th class="text-center" width='6%'>Qty Belum Inv</th>
									<th class="text-center" width='6%'>Qty Inv</th>
									<th class="text-center" width='7%'>Unit</th>
									<th class="text-center" width='10%'>Total Price (USD)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$numb=0;
								$SUM = 0;
								$no = 0;
								foreach($getDetail AS $val => $valx){
									$no++;
									$numb++;
									$pr		= 'pr';
									$numb1	= $pr.$numb;
									
									$NegoPersen = (!empty($valx['nego']))?'0':'0';
									$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
									$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
									$est_harga 	= 0;
									$dataSum 	= 0;
									
									if($valx['qty'] <> 0){
										$HrgTot  	= $valx['total_deal_usd'];
										$dataSum	= $HrgTot;
									}

									$SUM += $dataSum;
									
									if($valx['product'] == 'pipe' OR $valx['product'] == 'pipe slongsong'){
										$unitT = "Btg";
									}
									else{
										$unitT = "Pcs";
									}
									
									?>			
									<tr id='tr_<?= $numb;?>' >
										<td align='center'>
											<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow(<?= $numb;?>)'><i class="fa fa-times"></i></a></span>
										</td>
										<td colspan='2'>
											<input type="hidden" class="form-control input-sm" id="material_name1_<?= $numb;?>" name="data1[<?php echo $numb ?>][material_name1]" value="<?php echo strtoupper($valx['product']); ?>" readonly >
											<input type="text" class="form-control input-sm" id="product_cust<?= $numb;?>" name="data1[<?php echo $numb ?>][product_cust]" value="<?php echo strtoupper($valx['customer_item']); ?>" readonly >
										</td>
										<td><input type="text" class="form-control input-sm" id="diameter_1_<?= $numb;?>" name="data1[<?php echo $numb ?>][diameter_1]" value="<?php echo $valx['dim1']; ?>" readonly ></td>
										<td><input type="text" class="form-control input-sm" id="diameter_2_<?= $numb;?>" name="data1[<?php echo $numb ?>][diameter_2]" value="<?php echo $valx['dim2']; ?>" readonly ></td>
										<td><input type="text" class="form-control input-sm" id="liner_<?= $numb;?>" name="data1[<?php echo $numb ?>][liner]" value="<?php echo $valx['liner']; ?>" readonly ></td>
										<td><input type="text" class="form-control input-sm" id="pressure_<?= $numb;?>" name="data1[<?php echo $numb ?>][pressure]" value="<?php echo $valx['pressure']; ?>" readonly ></td>
										<td><input type="text" class="form-control input-sm" id="id_milik_<?= $numb;?>" name="data1[<?php echo $numb ?>][id_milik]" value="<?php echo spec_bq($valx['id_milik']); ?>" readonly ></td>
										<td>
											<?php 
											$harga_sat=round($dataSum / $valx['qty'],2);
											?>
											<input type='hidden' name="data1[<?php echo $numb ?>][id]" value='<?=$valx['id'];?>'>
											<input type='hidden' name="data1[<?php echo $numb ?>][qty_sudah]" value='<?=$valx['qty_inv'];?>'>
											<input type="text" class="form-control input-sm text-right" id="harga_sat_<?= $numb;?>" name="data1[<?php echo $numb ?>][harga_sat]" value="<?php echo set_value('harga_sat', isset($harga_sat) ? number_format($harga_sat,2) : ''); ?>" readonly >
											<input type="hidden" class="form-control input-sm" id="harga_sat_hidden_<?php echo $numb ?>" name="data1[<?php echo $numb ?>][harga_sat_hidden]" value="<?php echo set_value('harga_sat_hidden', isset($harga_sat) ? $harga_sat : ''); ?>" readonly >
										</td>
										<td><input type="text" class="form-control input-sm text-center" id="qty_ori_<?= $numb;?>" data-nomor='<?php echo $numb ?>' name="data1[<?php echo $numb ?>][qty_ori]" value="<?php echo $valx['qty']; ?>" readonly></td>
										<td><input type="text" class="form-control input-sm text-center" id="qty_belum_<?= $numb;?>" data-nomor='<?php echo $numb ?>' name="data1[<?php echo $numb ?>][qty_belum]" value="<?php echo $valx['qty'] - $valx['qty_inv']; ?>" readonly></td>
										<td><input type="text" class="form-control input-sm qty_product text-center" id="qty_<?= $numb;?>" data-nomor='<?php echo $numb ?>' name="data1[<?php echo $numb ?>][qty]"></td>
										<td><input type="text" class="form-control input-sm text-center" id="unit1_<?= $numb;?>" name="data1[<?php echo $numb ?>][unit1]" value="<?php echo $unitT; ?>" readonly ></td>
										<td>
											<?php 
											$harga_tot=number_format($dataSum,2);
											$harga_tot2=round($dataSum,2);
											?>
											<input type="text" class="form-control text-right" id="harga_tot_<?php echo $numb ?>" name="data1[<?php echo $numb ?>][harga_tot]" value="<?php echo set_value('harga_tot', isset($harga_tot) ? $harga_tot : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control amount1" id="harga_tot_hidden<?php echo $numb ?>" name="data1[<?php echo $numb ?>][harga_tot_hidden]" value="<?php echo set_value('harga_tot_hidden', isset($harga_tot2) ? $harga_tot2 : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control changeProduct" data-id='<?php echo $numb ?>' value="<?php echo set_value('harga_tot', isset($harga_tot) ? $harga_tot : ''); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
								<?php			
								}
								?>
								<tr class='FootColor'>
									<td colspan='13'><b>TOTAL COST  OF PRODUCT</b></td>
									<td align='center'>
										<?php 
										$tot_product=number_format($SUM,2);
										$tot_product2=round($SUM,2);
										?>
										<input type="text" class="form-control input-sm result1 text-right" id="tot_product" name="tot_product" value="<?php echo set_value('tot_product', isset($tot_product) ? $tot_product : ''); ?>" placeholder="Automatic" readonly >
										<input type="hidden" class="form-control result1_hidden" id="tot_product_hidden" name="tot_product_hidden" value="<?php echo set_value('tot_product_hidden', isset($tot_product2) ? $tot_product2 : ''); ?>" placeholder="Automatic" readonly >
										<input type="hidden" class="form-control result1  changeProductTot" value="<?php echo set_value('tot_product', isset($tot_product) ? $tot_product : ''); ?>" placeholder="Automatic" readonly >
										
									</td>
								</tr>
							</tbody>
							<?php
							$SUM_NONFRP = 0;
							if(!empty($non_frp)){
								echo "<tbody>";
									echo "<tr class='bg-blue'>";
										echo "<td class='text-left headX HeaderHr' colspan='14'><b>BILL OF QUANTITY NON FRP</b></td>";
									echo "</tr>";
									echo "<tr class='bg-bluexyz'>";
										echo "<th class='text-center'></th>";
										echo "<th class='text-center' colspan='9'>Material Name</th>";
										echo "<th class='text-center'>Qty</th>";
										echo "<th class='text-center'>Unit</th>";
										echo "<th class='text-center'>Unit Price</th>";
										echo "<th class='text-center'>Total Price</th>";
									echo "</tr>";
								echo "</tbody>";
								echo "<tbody class='body_x'>";
									$numb2 =0;
									foreach($non_frp AS $val => $valx){
										$numb2++;
										$SUM_NONFRP += $valx['total_deal_usd'];
										?>
										<tr id='tr1_<?= $numb2;?>' >
											<td align='center'>
												<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow2(<?= $numb2;?>)'><i class="fa fa-times"></i>
												</a></span>
											</td>
											<td colspan='9'>
												<?php
												$material_name2= strtoupper($valx['nm_material']);
												?>
												<input type="text" class="form-control" id="material_name2" name="data2[<?php echo $numb2 ?>][material_name2]" value="<?php echo set_value('material_name2', isset($material_name2) ? $material_name2 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td>
											   <input type="text" class="form-control qty_bq text-right" data-nomor='<?php echo $numb2 ?>' id="qty2" name="data2[<?php echo $numb2 ?>][qty2]" value="<?php echo set_value('qty2', isset($valx['qty']) ? $valx['qty'] : ''); ?>" placeholder="Automatic" >
											</td>
											<td>
												<?php
												$unit2= strtoupper($valx['satuan']);
												?>
												<input type="text" class="form-control text-center" id="unit2" name="data2[<?php echo $numb2 ?>][unit2]" value="<?php echo set_value('unit2', isset($unit2) ? $unit2 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td>
												<?php
												$harga_sat2= number_format($valx['total_deal_usd']/$valx['qty']);
												$harga_sat2_hidden= round($valx['total_deal_usd']/$valx['qty'],2);
												?>
												<input type="text" class="form-control text-right" id="harga_sat2" name="data2[<?php echo $numb2 ?>][harga_sat2]" value="<?php echo set_value('harga_sat2', isset($harga_sat2) ? $harga_sat2 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control" id="harga_sat2_hidden<?php echo $numb2 ?>" name="data2[<?php echo $numb2 ?>][harga_sat2_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat2_hidden) ? $harga_sat2_hidden : ''); ?>" placeholder="Automatic" readonly >
									  
											</td>
											<td>
												<?php
												$harga_tot2= number_format($valx['total_deal_usd']);
												$harga_tot2_hidden= round($valx['total_deal_usd'],2);
												?>
												<input type="text" class="form-control text-right" id="harga_tot2<?php echo $numb2 ?>" name="data2[<?php echo $numb2 ?>][harga_tot2]" value="<?php echo set_value('harga_tot2', isset($harga_tot2) ? $harga_tot2 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control amount2" id="harga_tot2_hidden<?php echo $numb2 ?>" name="data2[<?php echo $numb2 ?>][harga_tot2_hidden]" value="<?php echo set_value('harga_tot2_hidden', isset($harga_tot2_hidden) ? $harga_tot2_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changeAcc" data-id='<?php echo $numb2 ?>' value="<?php echo set_value('harga_tot2', isset($harga_tot2) ? $harga_tot2 : ''); ?>" placeholder="Automatic" readonly >
												
											</td>
										</tr>
										<?php
									}
									?>
									<tr class='FootColor'>
										<td colspan='12'><b>TOTAL BILL OF QUANTITY NON FRP</b></td>
										<td align='center'></td> 
										<td align="right">
											<?php
											$total_bq_nf= number_format($SUM_NONFRP);
											$total_bq_nf_hidden= round($SUM_NONFRP,2);
											?>
											<input type="text" class="form-control result2 text-right" id="total_bq_nf" name="total_bq_nf" value="<?php echo set_value('total_bq_nf', isset($total_bq_nf) ? $total_bq_nf : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control result2_hidden" id="total_bq_nf_hidden" name="total_bq_nf_hidden" value="<?php echo set_value('total_bq_nf_hidden', isset($total_bq_nf_hidden) ? $total_bq_nf_hidden : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control result2 changeAccTot" value="<?php echo set_value('total_bq_nf', isset($total_bq_nf) ? $total_bq_nf : ''); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
								</tbody>
							<?php
							}
							
							$SUM_MAT = 0;
							if(!empty($material)){
								echo "<thead>";
									echo "<tr class='bg-blue'>";
										echo "<td class='text-left headX HeaderHr' colspan='14'><b>MATERIAL</b></td>";
									echo "</tr>";
									echo "<tr class='bg-bluexyz'>";
										echo "<th class='text-center'></th>";
										echo "<th class='text-center' colspan='9'>Material Name</th>";
										echo "<th class='text-center'>Weight</th>";
										echo "<th class='text-center'>Unit</th>";
										echo "<th class='text-center'>Unit Price</th>";
										echo "<th class='text-center'>Total Price</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody class='body_x'>";
									$numb3 =0;
									foreach($material AS $val => $valx){
										$numb3++;
										$SUM_MAT += $valx['total_deal_usd'];
										?>
										<tr id='tr2_<?= $numb3;?>' >
											<td align='center'>
												<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow3(<?= $numb3;?>)'><i class="fa fa-times"></i>
												</a></span>
											</td>
											<td colspan='9'>
												<?php
												$material_name3= strtoupper($valx['nm_material']);
												?>
												<input type="text" class="form-control" id="material_name3_<?= $numb3;?>" name="data3[<?php echo $numb3 ?>][material_name3]" value="<?php echo set_value('material_name3', isset($material_name3) ? $material_name3 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td>
											   <input type="text" class="form-control qty_material text-right" id="qty3_<?= $numb3;?>" data-nomor='<?php echo $numb3 ?>'  name="data3[<?php echo $numb3 ?>][qty3]" value="<?php echo set_value('qty3', isset($valx['qty']) ? $valx['qty'] : ''); ?>" placeholder="Automatic" >
											</td>
											<td>
												<?php
												$unit3= strtoupper($valx['satuan']);
												?>
												<input type="text" class="form-control text-center" id="unit3_<?= $numb3;?>" name="data3[<?php echo $numb3 ?>][unit3]" value="<?php echo set_value('unit3', isset($unit3) ? $unit3 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td>
												<?php
												$harga_sat3= number_format($valx['total_deal_usd']/$valx['qty'],2);
												$harga_sat3_hidden= round($valx['total_deal_usd']/$valx['qty'],2);
												?>
												<input type="text" class="form-control text-right text-right" id="harga_sat3_<?= $numb3;?>" name="data3[<?php echo $numb3 ?>][harga_sat3]" value="<?php echo set_value('harga_sat3', isset($harga_sat3) ? $harga_sat3 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control" id="harga_sat3_hidden<?php echo $numb3 ?>" name="data3[<?php echo $numb3 ?>][harga_sat3_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat3_hidden) ? $harga_sat3_hidden : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td>
												<?php
												$harga_tot3= number_format($valx['total_deal_usd'],2);
												$harga_tot3_hidden= round($valx['total_deal_usd'],2);
												?>
												<input type="text" class="form-control text-right" id="harga_tot3<?php echo $numb3 ?>" name="data3[<?php echo $numb3 ?>][harga_tot3]" value="<?php echo set_value('harga_tot3', isset($harga_tot3) ? $harga_tot3 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control amount3" id="harga_tot3_hidden<?php echo $numb3 ?>" name="data3[<?php echo $numb3 ?>][harga_tot3_hidden]" value="<?php echo set_value('harga_tot3_hidden', isset($harga_tot3_hidden) ? $harga_tot3_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changeMat" data-id='<?php echo $numb3 ?>' value="<?php echo set_value('harga_tot3', isset($harga_tot3) ? $harga_tot3 : ''); ?>" placeholder="Automatic" readonly >
												
											</td>
										</tr>
									<?php
									}
									?>
									<tr class='FootColor'>
										<td colspan='13'><b>TOTAL MATERIAL</b></td>
										<td align="right">
											<?php
											$total_material= number_format($SUM_MAT,2);
											$total_material_hidden= round($SUM_MAT,2);
											?>
											<input type="text" class="form-control result3 text-right" id="total_material<?php echo $numb3 ?>" name="total_material" value="<?php echo set_value('total_material', isset($total_material) ? $total_material : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control result3_hidden" id="total_material_hidden<?php echo $numb3 ?>" name="total_material_hidden" value="<?php echo set_value('total_material_hidden', isset($total_material_hidden) ? $total_material_hidden : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control result3 changeMatTot" value="<?php echo set_value('total_material', isset($total_material) ? $total_material : ''); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
								</tbody>
							<?php
							}
							
							$SUM=0;
							$SUM1=0;
							$SUM2=0;
							$SUM3=0;
							$SUM4=0;
							if($jenis !='uang muka'){
								$SUM1=0;
								if(!empty($getEngCost)){
								?>
									<thead>
										<tr>
											<td class="text-left headX HeaderHr" colspan='14'><b>ENGINEERING COST</b></td>
										</tr>
										<tr class='bg-bluexyz'>
											<th class="text-center" width='6%'></th>
											<th class="text-center" colspan='11'>Item Product</th>
											<th class="text-center">Unit</th>
											<th class="text-center">Total Price</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$no1=0;
										$SUM1=0;
										$numb4=0;
										foreach($getEngCost AS $val => $valx){
											$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
											$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
											$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
											$SUM1 += $valx['eng_usd'];
											$no1++;
											$numb4++;
											?>
											<tr id='tr3_<?= $numb4;?>' >
												<td align='center'>
													<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow4(<?= $numb4;?>)'><i class="fa fa-times"></i>
													</a></span>
												</td>
												<td colspan='11'>
													<?php
													$material_name4= strtoupper('ENGINERING COST');
													?>
													<input type="text" class="form-control" id="material_name4" name="data4[<?php echo $numb4 ?>][material_name4]" value="<?php echo set_value('material_name4', isset($material_name4) ? $material_name4 : ''); ?>" placeholder="Automatic" readonly >
												</td>
												<!--<td>
												   <input type="text" class="form-control qty_enginering" id="qty4"   name="data4[<?php echo $numb4 ?>][qty4]" value="<?php echo set_value('qty4', isset($Qty1) ? $Qty1 : ''); ?>" placeholder="Automatic" readonly>
												</td>-->
												<td>
												<?php
													$unit4= strtoupper('-');
													?>
													<input type="text" class="form-control" id="unit4" name="data4[<?php echo $numb4 ?>][unit4]" value="<?php echo set_value('unit4', isset($unit4) ? $unit4 : ''); ?>" placeholder="Automatic" readonly >
												</td>
												<!--<td>
												<?php
													$harga_sat4= (!empty($valx['price']))?number_format($valx['price'],2):'-';
													$harga_sat4_hidden= (!empty($valx['price']))?round($valx['price'],2):'-';
													?>
													<input type="text" class="form-control" id="harga_sat4" name="data4[<?php echo $numb4 ?>][harga_sat4]" value="<?php echo set_value('harga_sat4', isset($harga_sat4) ? $harga_sat4 : ''); ?>" placeholder="Automatic" readonly >
													<input type="hidden" class="form-control" id="harga_sat4_hidden<?php echo $numb4 ?>" name="data4[<?php echo $numb4 ?>][harga_sat4_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat4_hidden) ? $harga_sat4_hidden : ''); ?>" placeholder="Automatic" readonly >
												</td>-->
												<td>
												<?php
													$harga_tot4= (!empty($valx['eng_usd']))?number_format($valx['eng_usd'],2):'-';
													$harga_tot4_hidden= (!empty($valx['eng_usd']))?round($valx['eng_usd'],2):'-';
													?>
													<input type="text" class="form-control harga_tot4 changeAll" id="harga_tot4<?php echo $numb4 ?>" data-nomor='<?php echo $numb4 ?>' name="data4[<?php echo $numb4 ?>][harga_tot4]" value="<?php echo set_value('harga_tot4', isset($harga_tot4) ? $harga_tot4 : ''); ?>" placeholder="Automatic" >
													<input type="hidden" class="form-control amount4 changeAll" id="harga_tot4_hidden<?php echo $numb4 ?>" name="data4[<?php echo $numb4 ?>][harga_tot4_hidden]" value="<?php echo set_value('harga_tot4_hidden', isset($harga_tot4_hidden) ? $harga_tot4_hidden : ''); ?>" placeholder="Automatic" readonly >
													<input type="hidden" class="form-control changeEng" data-id='<?php echo $numb4 ?>' value="<?php echo set_value('harga_tot4', isset($harga_tot4) ? $harga_tot4 : ''); ?>" placeholder="Automatic" >
												</td>
											</tr>
										<?php
										}
										?>
										<tr id='tr3X' class='FootColor'>
											<td colspan='13'><b>TOTAL ENGINEERING COST</b></td>
											<td align="right">
												<?php
												$total_enginering= number_format($SUM1,2);
												$total_enginering_hidden= round($SUM1,2);
													?>
												<input type="text" class="form-control result4 changeAll" id="total_enginering<?php echo $numb4 ?>" name="total_enginering" value="<?php echo set_value('total_enginering', isset($total_enginering) ? $total_enginering : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control result4_hidden changeAll" id="total_enginering_hidden<?php echo $numb4 ?>" name="total_enginering_hidden" value="<?php echo set_value('total_enginering_hidden', isset($total_enginering_hidden) ? $total_enginering_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changeEngTot" value="<?php echo set_value('total_enginering', isset($total_enginering) ? $total_enginering : ''); ?>" placeholder="Automatic" readonly >
											</td>
										</tr>
									</tbody>
								<?php
								}
								$SUM2=0;
								if(!empty($getPackCost)){
									?>
									<thead>
										<tr>
											<td class="text-left headX HeaderHr" colspan='14'><b>PACKING COST</b></td>
										</tr>
										<tr class='bg-bluexyz'>
											<th class="text-center" width='6%'></th>
											<th class="text-center" colspan='11'>Category</th>
											<th class="text-center">Type</th>
											<th class="text-center">Total Price</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$no2=0;
										$SUM2=0;
										$numb5=0;
										foreach($getPackCost AS $val => $valx){
											$no2++;
											$SUM2 += $valx['pack_usd'];
											$numb5++;
											?>
											<tr id='tr4_<?= $numb5;?>' >
											<td align='center'>
												<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow5(<?= $numb5;?>)'><i class="fa fa-times"></i>
												</a></span>
											</td>
											<td colspan='11'>
												<?php
												$material_name5= strtoupper('PACKING COST');
												?>
												<input type="text" class="form-control" id="material_name5" name="data5[<?php echo $numb5 ?>][material_name5]" value="<?php echo set_value('material_name5', isset($material_name5) ? $material_name5 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<!--<td>
											   <input type="text" class="form-control qty_enginering" id="qty5" data-nomor='<?php echo $numb5 ?>'  name="data5[<?php echo $numb5 ?>][qty5]" value="<?php echo set_value('qty5', isset($Qty1) ? $Qty1 : ''); ?>" placeholder="Automatic" >
											</td>-->
											<td>
											<?php
												$unit5= strtoupper('-');
												?>
												<input type="text" class="form-control" id="unit5" name="data5[<?php echo $numb5 ?>][unit5]" value="<?php echo set_value('unit5', isset($unit5) ? $unit5 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<!--<td>
											<?php
												$harga_sat5= (!empty($valx['price']))?number_format($valx['price'],2):'-';
												$harga_sat5_hidden= (!empty($valx['price']))?round($valx['price'],2):'-';
												?>
												<input type="text" class="form-control" id="harga_sat5" name="data5[<?php echo $numb5 ?>][harga_sat5]" value="<?php echo set_value('harga_sat5', isset($harga_sat5) ? $harga_sat5 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control" id="harga_sat5_hidden<?php echo $numb5 ?>" name="data5[<?php echo $numb5 ?>][harga_sat5_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat5_hidden) ? $harga_sat5_hidden : ''); ?>" placeholder="Automatic" readonly >
									  
											</td>-->
											<td>
											<?php
												$harga_tot5= number_format($valx['pack_usd'],2);
												$harga_tot5_hidden= round($valx['pack_usd'],2);
												?>
												<input type="text" class="form-control  harga_tot5" id="harga_tot5<?php echo $numb5 ?>" data-nomor='<?php echo $numb5 ?>' name="data5[<?php echo $numb5 ?>][harga_tot5]" value="<?php echo set_value('harga_tot5', isset($harga_tot5) ? $harga_tot5 : ''); ?>" placeholder="Automatic" >
												<input type="hidden" class="form-control amount5" id="harga_tot5_hidden<?php echo $numb5 ?>" name="data5[<?php echo $numb5 ?>][harga_tot5_hidden]" value="<?php echo set_value('harga_tot5_hidden', isset($harga_tot5_hidden) ? $harga_tot5_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changePack" data-id='<?php echo $numb5 ?>' value="<?php echo set_value('harga_tot5', isset($harga_tot5) ? $harga_tot5 : ''); ?>" placeholder="Automatic" >
												
											</td>
										</tr>
										<?php
										}
										?>
										<tr id='tr4X' class='FootColor'>
											<td colspan='13'><b>TOTAL PACKING COST</b></td>
											<td align="right">
												<?php
												$total_packing= number_format($SUM2,2);
												$total_packing_hidden= round($SUM2,2);
												?>
												<input type="text" class="form-control result5 changeAll" id="total_packing<?php echo $numb5 ?>" name="total_packing" value="<?php echo set_value('total_packing', isset($total_packing) ? $total_packing : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control result5_hidden changeAll" id="total_packing_hidden<?php echo $numb5 ?>" name="total_packing_hidden" value="<?php echo set_value('total_packing_hidden', isset($total_packing_hidden) ? $total_packing_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control result5 changePackTot"value="<?php echo set_value('total_packing', isset($total_packing) ? $total_packing : ''); ?>" placeholder="Automatic" readonly >
											</td>
										</tr>
									</tbody>
								<?php
								}
								
								$SUM3=0;
								if(!empty($getTruck)){
									?>
									<tbody>
										<tr>
											<td class="text-left headX HeaderHr" colspan='14'><b>TRUCKING</b></td>
										</tr>
										<tr class='bg-bluexyz'>
											<th class="text-center" width='6%'></th>
											<th class="text-center" colspan='11'>Category</th>
											<th class="text-center">Type</th>
											<th class="text-center">Total Price</th>
										</tr>
									</tbody>
									<tbody>
										<?php
										$no3=0;
										$SUM3=0;
										$numb6=0;
										foreach($getTruck AS $val => $valx){
											$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
											$SUM3 += $valx['ship_usd'];
											$no3++;
											$numb6++;
											?>
											<tr id='tr5_<?= $numb6;?>' >
											<td align='center'>
												<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow6(<?= $numb6;?>)'><i class="fa fa-times"></i>
												</a></span>
											</td>
											<td colspan='11'>
												<?php
												$material_name6= strtoupper('SHIPPING COST');
												?>
												<input type="text" class="form-control" id="material_name6" name="data6[<?php echo $numb6 ?>][material_name6]" value="<?php echo set_value('material_name6', isset($material_name6) ? $material_name6 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td>
											<?php
												$unit6= strtoupper('-');
												?>
												<input type="text" class="form-control" id="unit6" name="data6[<?php echo $numb6 ?>][unit6]" value="<?php echo set_value('unit6', isset($unit6) ? $unit6 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<!--<td>
											   <input type="text" class="form-control qty_trucking" id="qty6" data-nomor='<?php echo $numb6 ?>'  name="data6[<?php echo $numb6 ?>][qty6]" value="<?php echo set_value('qty6', isset($Qty3) ? $Qty3 : ''); ?>" placeholder="Automatic" readonly>
											</td>
											<td>
												<?php
												$fumigasi= number_format(0,2);
												?>
											   <input type="text" class="form-control fumigasi" id="fumigasi" data-nomor='<?php echo $numb6 ?>'  name="data6[<?php echo $numb6 ?>][fumigasi]" value="<?php echo set_value('fumigasi', isset($fumigasi) ? $fumigasi : ''); ?>" placeholder="Automatic" readonly>
											</td>		
											<td>
											<?php
												$harga_sat6= number_format(0,2);
												$harga_sat6_hidden= round(0,2);
												?>
												<input type="text" class="form-control harga_sat6" id="harga_sat6" name="data6[<?php echo $numb6 ?>][harga_sat6]" value="<?php echo set_value('harga_sat6', isset($harga_sat6) ? $harga_sat6 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control" id="harga_sat6_hidden<?php echo $numb6 ?>" name="data6[<?php echo $numb6 ?>][harga_sat6_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat6_hidden) ? $harga_sat6_hidden : ''); ?>" placeholder="Automatic" readonly >
									  
											</td>-->	
											<td>
											<?php
												$harga_tot6= number_format($valx['ship_usd'],2);
												$harga_tot6_hidden= round($valx['ship_usd'],2);
												?>
												<input type="text" class="form-control harga_tot6 changeAll" id="harga_tot6<?php echo $numb6 ?>" data-nomor='<?php echo $numb6 ?>' name="data6[<?php echo $numb6 ?>][harga_tot6]" value="<?php echo set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" placeholder="Automatic" >
												<input type="hidden" class="form-control amount6 changeAll" id="harga_tot6_hidden<?php echo $numb6 ?>" name="data6[<?php echo $numb6 ?>][harga_tot6_hidden]" value="<?php echo set_value('harga_tot6_hidden', isset($harga_tot6_hidden) ? $harga_tot6_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changeShip" data-id='<?php echo $numb6 ?>' value="<?php echo set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" placeholder="Automatic" >
												
											</td>
										</tr>
										<?php
										}
										?>
										<tr id='tr5X' class='FootColor'>
											<td colspan='13'><b>TOTAL TRUCKING</b></td>
											<td align="right">
											<?php
												$total_trucking= number_format($SUM3,2);
												$total_trucking_hidden= round($SUM3,2);
												?>
												<input type="text" class="form-control result6 changeAll" id="total_trucking<?php echo $numb6 ?>" name="total_trucking" value="<?php echo set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control result6_hidden changeAll" id="total_trucking_hidden<?php echo $numb6 ?>" name="total_trucking_hidden" value="<?php echo set_value('total_trucking_hidden', isset($total_trucking_hidden) ? $total_trucking_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changeShipTot" value="<?php echo set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" placeholder="Automatic" readonly >
											</td>
										</tr>
									</tbody>
								<?php
								}
								$SUM4=0;
								if(!empty($getVia)){
									?>
									<tbody>
										<tr>
											<td class="text-left headX HeaderHr" colspan='14'><b>TRUCKING LOKAL</b></td>
										</tr>
										<tr class='bg-bluexyz'>
											<th class="text-center" width='6%'></th>
											<th class="text-center">Item Product</th>
											<th class="text-center" colspan='3'>Area</th>
											<th class="text-center" colspan='3'>Tujuan</th>
											<th class="text-center" colspan='3'>Kendaraan</th>
											<th class="text-center">Qty</th>
											<th class="text-center">Price</th>
											<th class="text-center">Total Price</th>
										</tr>
									</tbody>
									<tbody>
										<?php
										$no4=0;
										$SUM4=0;
										$numb7=0;
										foreach($getVia AS $val => $valx){
											$SUM4 += $valx['price_total'];
											$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
											$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
											if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
												$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
											}
											else{
												$Kendaraanx = strtoupper($valx['kendaraan']);
											}
											
											$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
											
											$numb7++;
											$no4++;
											
											?>
											<tr id='tr6_<?= $numb7;?>' >
											<td align='center'>
												<span><a class="text-red" href="javascript:void(0)" title="No Deal" onClick='delRow7(<?= $numb7;?>)'><i class="fa fa-times"></i>
												</a></span>
											</td>
											<td>
												<?php
												$material_name7= strtoupper($valx['caregory_sub']);
												?>
												<input type="text" class="form-control" id="material_name7" name="data7[<?php echo $numb7 ?>][material_name7]" value="<?php echo set_value('material_name7', isset($material_name7) ? $material_name7 : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td align='right' colspan='3'>
												<input type="text" class="form-control" id="area" name="data7[<?php echo $numb7 ?>][area]" value="<?php echo set_value('area', isset($Areax) ? $Areax : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td align='right' colspan='3'>
												<input type="text" class="form-control" id="tujuan" name="data7[<?php echo $numb7 ?>][tujuan]" value="<?php echo set_value('tujuan', isset($Tujuanx) ? $Tujuanx : ''); ?>" placeholder="Automatic" readonly >
											</td>
											<td align='center' colspan='3'>
												<input type="text" class="form-control" id="kendaraan" name="data7[<?php echo $numb7 ?>][kendaraan]" value="<?php echo set_value('kendaraan', isset($Kendaraanx) ? $Kendaraanx : ''); ?>" placeholder="Automatic" readonly >
											</td>
											
											<td>
											   <input type="text" class="form-control qty_lokal" id="qty7" data-nomor='<?php echo $numb7 ?>'  name="data7[<?php echo $numb7 ?>][qty7]" value="<?php echo set_value('qty7', isset($Qty4) ? $Qty4 : ''); ?>" placeholder="Automatic" >
											</td>
												
											<td>
											<?php
												$harga_sat7= number_format($valx['price'],2);
												$harga_sat7_hidden= round($valx['price'],2);
												?>
												<input type="text" class="form-control" id="harga_sat7" name="data7[<?php echo $numb7 ?>][harga_sat7]" value="<?php echo set_value('harga_sat7', isset($harga_sat7) ? $harga_sat7 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control" id="harga_sat7_hidden<?php echo $numb7 ?>" name="data7[<?php echo $numb7 ?>][harga_sat7_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat7_hidden) ? $harga_sat7_hidden : ''); ?>" placeholder="Automatic" readonly >
									  
											</td>
											<td>
											<?php
												$harga_tot7= number_format($valx['price_total'],2);
												$harga_tot7_hidden= round($valx['price_total'],2);
												?>
												<input type="text" class="form-control changeAll" id="harga_tot7<?php echo $numb7 ?>" name="data7[<?php echo $numb7 ?>][harga_tot7]" value="<?php echo set_value('harga_tot7', isset($harga_tot7) ? $harga_tot7 : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control amount7 changeAll" id="harga_tot7_hidden<?php echo $numb7 ?>" name="data7[<?php echo $numb7 ?>][harga_tot7_hidden]" value="<?php echo set_value('harga_tot7_hidden', isset($harga_tot7_hidden) ? $harga_tot7_hidden : ''); ?>" placeholder="Automatic" readonly >
												<input type="hidden" class="form-control changeShipL" data-id='<?php echo $numb7 ?>' value="<?php echo set_value('harga_tot7', isset($harga_tot7) ? $harga_tot7 : ''); ?>" placeholder="Automatic" readonly >
												
											</td>
										</tr>
										<?php
											
										}
										echo "<tr class='FootColor'>";
											echo "<td colspan='13'><b>TOTAL TRUCKING LOKAL</b></td> ";
											?>
											<td align="right">
												<?php
												$total_lokal= number_format($SUM4,2);
												$total_lokal_hidden= round($SUM4,2);
												?>
											<input type="text" class="form-control result7 changeAll" id="total_lokal<?php echo $numb7 ?>" name="total_lokal" value="<?php echo set_value('total_lokal', isset($total_lokal) ? $total_lokal : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control result7_hidden changeAll" id="total_lokal_hidden<?php echo $numb7 ?>" name="total_lokal_hidden" value="<?php echo set_value('total_lokal_hidden', isset($total_lokal_hidden) ? $total_lokal_hidden : ''); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control changeShipL" value="<?php echo set_value('total_lokal', isset($total_lokal) ? $total_lokal : ''); ?>" placeholder="Automatic" readonly >
											
											</td>
										</tr>
											
											
									</tbody>
								<?php
								}
							}
					}

							$down_payment = number_format(0,2);
							$down_payment_hidden = round(0,2);
							?>
									
							<tfoot>
								<tr class='HeaderHr'>
									<td align='right' colspan='12'>TOTAL </td>
									<td align='center' style='text-align:center;'></td>
									<td align='right' style='text-align:center;'>
										<?php 
											$grand_total 		= number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);
											$grand_total_hidden = round($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);
										?>
										<input type="text" class="form-control grand_total text-right" id="grand_total" name="grand_total" value="<?php echo set_value('grand_total', isset($grand_total) ? $grand_total : ''); ?>" placeholder="Automatic" readonly >
										<input type="hidden" class="form-control grand_total_hidden" id="grand_total_hidden" name="grand_total_hidden" value="<?php echo set_value('grand_total_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >
									</td>
								</tr>
								<?php 
								if ($jenis == 'progress') {
									$dp        			= $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$no_ipp."'")->row(); 
									$down_payment 		= number_format(0,2);
									$down_payment_hidden= round(0,2);
									?>
									<tr class='HeaderHr'>
										<td align='right' colspan='12'>DOWN PAYMENT I</td>
										<td align='center'></td>
										<td align='right'>
											<input type="text" class="form-control down_payment text-right" id="down_payment" name="down_payment" value="<?php echo set_value('down_payment', isset($down_payment) ? $down_payment : '0'); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control down_payment_hidden" id="down_payment_hidden" name="down_payment_hidden" value="<?php echo set_value('down_payment_hidden', isset($down_payment_hidden) ? $down_payment_hidden : '0'); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
									<tr class='HeaderHr'>
										<td align='right' colspan='12'>DOWN PAYMENT II</td>
										<td align='center'></td>
										<td align='right'>
											<input type="text" class="form-control down_payment2 text-right" id="down_payment2" name="down_payment2" value="<?php echo set_value('down_payment2', isset($down_payment) ? $down_payment : '0'); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control down_payment_hidden2" id="down_payment_hidden2" name="down_payment_hidden2" value="<?php echo set_value('down_payment_hidden2', isset($down_payment_hidden) ? $down_payment_hidden : '0'); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
								<?php 
								}	
								
								if ($jenis == 'uang muka') {
									$dp        			=  $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$no_ipp."'")->row(); 
									$down_payment 		= number_format(0,2);
									$down_payment_hidden= round(0,2);
									?>
									<tr class='HeaderHr'>
										<td align='right' colspan='12'>DOWN PAYMENT I </td>
										<td align='center'></td>
										<td align='right'>
											<input type="text" class="form-control down_payment text-right" id="down_payment" name="down_payment" value="<?php echo set_value('down_payment', isset($down_payment) ? $down_payment : '0'); ?>" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control down_payment_hidden" id="down_payment_hidden" name="down_payment_hidden" value="<?php echo set_value('down_payment_hidden', isset($down_payment_hidden) ? $down_payment_hidden : '0'); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
								<?php 
								}	
								?>
								
								<tr class='HeaderHr'>
									<td align='right' colspan='12'>DISKON </td>
									<td align='center'></td>
									<td align='right'>
										<input type="text" class="form-control diskon text-right" id="diskon" name="diskon" value="0" placeholder="Automatic"  >
										<input type="hidden" class="form-control diskon_hidden" id="diskon_hidden" name="diskon_hidden" value="0" placeholder="Automatic" readonly >
									</td>
								</tr>

								<?php 
								if($jenis!='retensi') {
									$ret       =  $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$no_ipp."'")->row(); 
									$retum 	= $ret->retensi_um;
									?>
									<tr class='HeaderHr'>
										<td align='right' colspan='12'>POTONGAN RETENSI </td>
										<td align='center' style='text-align:center;'>
											<select id="persen_retensi" name="persen_retensi" class="form-control input-sm text-right" style="width: 100%;" tabindex="-1" required readonly>
												<option value="0"></option>			
												<option value="5">5 %</option>
												<option value="10">10 %</option>
												<option value="15">15 %</option>
												<option value="20">20 %</option>
											</select>
										</td>
										<td align='right'>
											<input type="text" class="form-control potongan_retensi text-right" id="potongan_retensi" name="potongan_retensi" value="0" placeholder="Automatic"  readonly>
											<input type="hidden" class="form-control potongan_retensi_hidden" id="potongan_retensi_hidden" name="potongan_retensi_hidden" value="0" placeholder="Automatic" readonly >
											<input type="hidden" class="form-control retensi_um" id="retensi_um" name="retensi_um" value="<?php echo set_value('retensi_um', isset($retum) ? $retum : '0'); ?>" placeholder="Automatic" readonly >
										</td>
									</tr>
								<?php 
								}		
								?>
								
								<tr class='HeaderHr'>
									<td align='right' colspan='12'>PPN </td>
									<td align='center'></td>
									<td align='right'>
										<input type="text" class="form-control ppn text-right" id="ppn" name="ppn" value="0" placeholder="Automatic" readonly >
										<input type="hidden" class="form-control ppn_hidden" id="ppn_hidden" name="ppn_hidden" value="0" placeholder="Automatic" readonly >
									</td>
								</tr>
								
								<?php 
								if($jenis!='retensi') {
									$ret       =  $this->db->query("SELECT * FROM billing_so WHERE no_ipp = '".$no_ipp."'")->row(); 
									$retum 	= $ret->retensi_um;
									?>
									<tr class='HeaderHr'>
										<td align='right' colspan='12'>POTONGAN RETENSI PPN</td>
										<td align='center' style='text-align:center;'>
											<select id="persen_retensi2" name="persen_retensi2" class="form-control input-sm text-right" style="width: 100%;" tabindex="-1" required readonly>
												<option value="0"></option>			
												<option value="5">5 %</option>
												<option value="10">10 %</option>
												<option value="15">15 %</option>
												<option value="20">20 %</option>
											</select>
										</td>
										<td align='right'>
											<input type="text" class="form-control potongan_retensi2 text-right" id="potongan_retensi2" name="potongan_retensi2" value="0" readonly>
											<input type="hidden" class="form-control potongan_retensi_hidden2" id="potongan_retensi_hidden2" name="potongan_retensi_hidden2" value="0" readonly >
											<input type="hidden" class="form-control retensi_um2" id="retensi_um2" name="retensi_um2" value="<?php echo set_value('retensi_um', isset($retum) ? $retum : '0'); ?>" readonly >
										</td>
									</tr>
								<?php 
								}		
								?>
								
								<tr class='HeaderHr'>
									<td align='right' colspan='12'>TOTAL INVOICE</td>
									<td align='center'>
									<td align='right'>
										<?php 
										$grand_total = number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT -$down_payment_hidden, 2);
										$grand_total_hidden = round($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT - $down_payment_hidden, 2);
										?>
										<input type="text" class="form-control total_invoice text-right" id="total_invoice" name="total_invoice" value="<?php echo set_value('total_invoice', isset($grand_total) ? $grand_total : ''); ?>" placeholder="Automatic" readonly >
										<input type="hidden" class="form-control total_invoice_hidden" id="total_invoice_hidden" name="total_invoice_hidden" value="<?php echo set_value('total_invoice_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >
									</td>
								</tr>
							</tfoot>
						</table>


					<div class="text-right">
						<div class="box active">
							<div class="box-body">
								<button class="btn btn-danger" onclick="kembali_inv()" type="button"><i class="fa fa-refresh"></i><b> Kembali</b></button>
								<button class="btn btn-primary" type="button" id="proses_inv"><i class="fa fa-save"></i><b> Simpan Data Invoice</b></button>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</form>

<style>
	.HeaderHr{
		background-color: #ce4c00;
		color: white;
	}

	.bg-bluexyz{
		background-color: #05b3a3 !important;
		color : white;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen_select').chosen({width: '100%'});
		$('.autoNumeric').autoNumeric();
		umLoad();
		
		$(document).on('keyup','#kurs', function(){
			var kurs = $(this).val();
			
			if(kurs == '0' || kurs == ''){
				$(this).val('1');
			}
		});
		
		$('#proses_inv').click(function(e){
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
					  var baseurl=base_url + active_controller +'/save_invoice';
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
		
		$(document).on('keyup', '.qty_product', function(){
			var dataNomor = $(this).data('nomor');
			var hargaSat  = $('#harga_sat_hidden_'+dataNomor).val();
			var dataIni	  = $(this).val();
			var total     = getNum(hargaSat*dataIni).toFixed(2);

			$('#harga_tot_'+dataNomor).val(num2(total));
			$('#harga_tot_hidden'+dataNomor).val(total);
			
			fnAlltotal()
		});
		
		$(document).on('blur', '.harga_tot8', function(){
			var dataNomor8 = $(this).data('nomor');
			var hargaSat8  = 1;//$('#harga_sat8_hidden'+dataNomor8).val();
			var dataIni8	  = $(this).val();
			var total8     = getNum(hargaSat8*dataIni8).toFixed(2);
			
			$('#harga_tot8'+dataNomor8).val(num2(total8));
			$('#harga_tot8_hidden'+dataNomor8).val(total8);
			 
			fnAlltotal8()
		});
		
		$(document).on('keyup', '.qty_bq', function(){
			var dataNomor = $(this).data('nomor');
			var hargaSat  = $('#harga_sat2_hidden'+dataNomor).val();
			var dataIni	  = $(this).val();
			var total     = getNum(hargaSat*dataIni).toFixed(2);

			$('#harga_tot2'+dataNomor).val(num2(total));
			$('#harga_tot2_hidden'+dataNomor).val(total);
			
			fnAlltotal2()
		});
		
		$(document).on('keyup', '.qty_material', function(){
			var dataNomor3 = $(this).data('nomor');
			var hargaSat3  = $('#harga_sat3_hidden'+dataNomor3).val();
			var dataIni3	  = $(this).val();
			var total3     = getNum(hargaSat3*dataIni3).toFixed(2);

			$('#harga_tot3'+dataNomor3).val(num2(total3));
			$('#harga_tot3_hidden'+dataNomor3).val(total3);
			
			fnAlltotal3()
		});
		
		$(document).on('blur', '.harga_tot4', function(){
			var dataNomor4 = $(this).data('nomor');
			var hargaSat4  = 1;//$('#harga_sat4_hidden'+dataNomor4).val();
			var dataIni4	  = $(this).val();
			var total4     = getNum(hargaSat4*dataIni4).toFixed(2);
			
			$('#harga_tot4'+dataNomor4).val(num2(total4));
			$('#harga_tot4_hidden'+dataNomor4).val(total4);
			 
			fnAlltotal4()
		});
		
		$(document).on('blur', '.harga_tot5', function(){
			var dataNomor4 = $(this).data('nomor');
			var hargaSat4  = 1;
			var dataIni4	  = $(this).val();
			var total4     = getNum(hargaSat4*dataIni4).toFixed(2);
			
			$('#harga_tot5'+dataNomor4).val(num2(total4));
			$('#harga_tot5_hidden'+dataNomor4).val(total4);
			 
			fnAlltotal5()
		});
		
		$(document).on('blur', '.harga_tot6', function(){
			var dataNomor6 = $(this).data('nomor');
			var hargaSat6  = 1;//$('#harga_sat6_hidden'+dataNomor6).val();
			var dataIni6	  = $(this).val();
			var total6     = getNum(hargaSat6*dataIni6).toFixed(2);
			
			$('#harga_tot6'+dataNomor6).val(num2(total6));
			$('#harga_tot6_hidden'+dataNomor6).val(total6);
			 
			fnAlltotal6()
		});
		
		$(document).on('keyup', '.qty_lokal', function(){
			var dataNomor7 = $(this).data('nomor');
			var hargaSat7  = $('#harga_sat7_hidden'+dataNomor7).val();
			var dataIni7	  = $(this).val();
			var total7     = getNum(hargaSat7*dataIni7).toFixed(2);
			
			$('#harga_tot7'+dataNomor7).val(num2(total7));
			$('#harga_tot7_hidden'+dataNomor7).val(total7);
			 
			fnAlltotal7()
		});
		
		$(document).on('blur', '.diskon', function(){
			var dataPpn	  = $('#ppnselect').val();
			var dataDiskon	  = $(this).val();
			var totalDiskon     = getNum(dataDiskon).toFixed(2);
			var grandtotal   = $(".grand_total_hidden").val();
			var uangmuka     = $(".down_payment_hidden").val();
			
			$('.diskon').val(num2(totalDiskon));
			$('.diskon_hidden').val(totalDiskon);
			
			if(dataPpn==1){
			var totalPpn     = getNum((grandtotal-totalDiskon-uangmuka)*0.1).toFixed(2);			
			}
			else{
			var totalPpn     = getNum(0).toFixed(2);	
			}
			
			$('.ppn').val(num2(totalPpn));
			$('.ppn_hidden').val(totalPpn);
			 
			totalInvoice()
		});
		
		$(document).on('change', '#persen_retensi', function(){  
			var um1                = $('#persen').val();
			var um2                = $('#persen2').val();		  			
			var retensi_um         = $('#retensi_um').val();		   
			var datRetensi         = $('#persen_retensi').val();
			var totalRetensi       = 0
			var result1_hidden1 = 0
			var result2_hidden1 = 0
			var result3_hidden1 = 0
			var result4_hidden1 = 0
			var result5_hidden1 = 0
			var result6_hidden1 = 0
			var result7_hidden1 = 0
			var result8_hidden1 = 0
			
			var result1_hidden  = $('.result1_hidden').val();
			var result2_hidden  = $('.result2_hidden').val();
			var result3_hidden  = $('.result3_hidden').val();
			var result4_hidden  = $('.result4_hidden').val();
			var result5_hidden  = $('.result5_hidden').val();
			var result6_hidden  = $('.result6_hidden').val();
			var result7_hidden  = $('.result7_hidden').val();
			var result8_hidden  = $('.result8_hidden').val();
			
			$('#potongan_retensi2').val(0);
			$('#potongan_retensi_hidden2').val(0);
			
			if($('#potongan_retensi2').val == 0){
				$("#persen_retensi2 option[value='0']").attr('selected',true);
			}
			
			if(result1_hidden==null){
				result1_hidden1 = 0;
			}
			else{
				result1_hidden1 = result1_hidden;
			}
			
			if(result2_hidden==null){
				result2_hidden1 = 0;
			}
			else{
				result2_hidden1 = result2_hidden;
			}
			if(result3_hidden==null){
				result3_hidden1 = 0;
			}
			else{
				result3_hidden1 = result3_hidden;
			}
			
			if(retensi_um > 0){
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100)*(100/100 - getNum(um2/100)).toFixed(2);
			}
			else{
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100).toFixed(2);
			}
			
			var totalret   = totalRetensi.toFixed(2);
			 
			$('.potongan_retensi').val(num2(totalret));
			$('.potongan_retensi_hidden').val(totalret);
			
			totalInvoice();
		});
		
		$(document).on('change', '#persen_retensi2', function(){
			   
			var um1                = $('#persen').val();
			var um2                = $('#persen2').val();		  			
			var retensi_um         = $('#retensi_um').val();		   
			var datRetensi         = $('#persen_retensi2').val();
			var totalRetensi       = 0
			var result1_hidden1 = 0
			var result2_hidden1 = 0
			var result3_hidden1 = 0
			var result4_hidden1 = 0
			var result5_hidden1 = 0
			var result6_hidden1 = 0
			var result7_hidden1 = 0
			var result8_hidden1 = 0
			
			var result1_hidden  = $('.result1_hidden').val();
			var result2_hidden  = $('.result2_hidden').val();
			var result3_hidden  = $('.result3_hidden').val();
			var result4_hidden  = $('.result4_hidden').val();
			var result5_hidden  = $('.result5_hidden').val();
			var result6_hidden  = $('.result6_hidden').val();
			var result7_hidden  = $('.result7_hidden').val();
			var result8_hidden  = $('.result8_hidden').val();
			
			$('#potongan_retensi').val(0);
			$('#potongan_retensi_hidden').val(0);
			
			if($('#potongan_retensi').val == 0){
				$("#persen_retensi option[value='0']").attr('selected',true);
			}
			
		
			if(result1_hidden==null){
				result1_hidden1 = 0;
			}
			else{
				result1_hidden1 = result1_hidden;
			}
			
			if(result2_hidden==null){
				result2_hidden1 = 0;
			}
			else{
				result2_hidden1 = result2_hidden;
			}
			
			if(result3_hidden==null){
				result3_hidden1 = 0;
			}
			else{
				result3_hidden1 = result3_hidden;
			}
			
			if(retensi_um > 0){
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100)*(100/100 - getNum(um2/100)).toFixed(2);
			}
			else{
				totalRetensi    = (getNum(result1_hidden1)+getNum(result2_hidden1)+getNum(result3_hidden1))*getNum(datRetensi/100).toFixed(2);
			}
			
			var totalret   	= totalRetensi;
			var retensiPPN  = totalRetensi * 10/100;
			
			var Retensi2	= totalret - retensiPPN;

			$('.potongan_retensi2').val(number_format(Retensi2,2));
			$('.potongan_retensi_hidden2').val(Retensi2);
			
			totalInvoice();
		});
		
		$(document).on('blur', '.potongan_retensix', function(){
			
			var dataPpn	  = $('#ppnselect').val();
			var dataRetensi	  = $(this).val();
			var totalRetensi     = getNum(dataRetensi).toFixed(2);
			var grandtotal   = $(".grand_total_hidden").val();
			var totalDiskon     = $(".diskon_hidden").val();
			var uangmuka     = $(".down_payment_hidden").val();
			
			$('.potongan_retensi').val(num2(totalRetensi));
			$('.potongan_retensi_hidden').val(totalRetensi);
			
			if(dataPpn==1){
			var totalPpn     = getNum((grandtotal-totalDiskon-totalRetensi-uangmuka)*0.1).toFixed(2);			
			}
			else{
			var totalPpn     = getNum(0).toFixed(2);	
			}
			
			$('.ppn').val(num2(totalPpn));
			$('.ppn_hidden').val(totalPpn);
			 
			totalInvoice();
		});
		
		$(document).on('blur', '.ppn', function(){
			
			var dataPpn	  = $(this).val();
			var totalPpn     = getNum(dataPpn).toFixed(2);
			
			$('.ppn').val(num2(totalPpn));
			$('.ppn_hidden').val(totalPpn);
			 
			totalInvoice();
		});
		
		$(document).on('change', '#ppnselect', function(){
			ppn();
		});
		
	});
	
	function kembali_inv(){
        window.location.href = base_url + active_controller +'/create_new';
    }
	
	function delRow(row){
		$('#tr_'+row).remove();
		grandtotal();		
		totalInvoice();
	}
	
	function delRow2(row){
		$('#tr1_'+row).remove();
		grandtotal();		
		totalInvoice();
	}
	
	function delRow3(row){
		$('#tr2_'+row).remove();
		grandtotal();		
		totalInvoice();
	}
	
	function delRow4(row){
		$('#tr3_'+row).remove();
		$('#tr3X').remove();
		grandtotal();		
		totalInvoice();
	}
	
	function delRow5(row){
		$('#tr4_'+row).remove();
		$('#tr4X').remove();
		grandtotal();		
		totalInvoice();
	}
	
	function delRow6(row){
		$('#tr5_'+row).remove();
		$('#tr5X').remove();
		grandtotal();	
		totalInvoice();
	}
	
	function delRow7(row){
		$('#tr6_'+row).remove();
		grandtotal();		
		totalInvoice();
	}
	
	function delRow8(row){
		$('#tr7_'+row).remove();
		grandtotal();		
		totalInvoice();
	}
	
    function umLoad(){	
		var hargaSat  = getNum($('#harga_tot_hidden').val());
		var dataIni	  = getNum($('#um_persen').val());
		var nilai;
		var total;
		var id;
		//PRODUCT
		var SUM = 0;
		$(".changeProduct" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM += total;
			$('#harga_tot_'+id).val(num(total));
			$('#harga_tot_hidden'+id).val(total.toFixed(2));
		});
		$('#tot_product').val(num(SUM));
		$('#tot_product_hidden').val(SUM.toFixed(2));

		//AKSESORIS
		var SUM2 = 0;
		$(".changeAcc").each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM2 += total;
			$('#harga_tot2'+id).val(num(total));
			$('#harga_tot2_hidden'+id).val(total.toFixed(2));
		});
		$('#total_bq_nf').val(num(SUM2));
		$('#total_bq_nf_hidden').val(SUM2.toFixed(2));

		//MATERIAL
		var SUM3 = 0;
		$(".changeMat" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM3 += total;
			$('#harga_tot3'+id).val(num(total));
			$('#harga_tot3_hidden'+id).val(total.toFixed(2));
		});
		$('#total_material3').val(num(SUM3));
		$('#total_material_hidden3').val(SUM3.toFixed(2));

		//ENGINE
		var SUM4 = 0;
		$(".changeEng" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM4 += total;
			$('#harga_tot4'+id).val(num(total));
			$('#harga_tot4_hidden'+id).val(total.toFixed(2));
		});
		$('#total_enginering1').val(num(SUM4));
		$('#total_enginering_hidden1').val(SUM4.toFixed(2));

		//PACKING
		var SUM5 = 0;
		$(".changePack" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM5 += total;
			$('#harga_tot5'+id).val(num(total));
			$('#harga_tot5_hidden'+id).val(total.toFixed(2));
		});
		$('#total_packing1').val(num(SUM5));
		$('#total_packing_hidden1').val(SUM5.toFixed(2));

		//SHIPPING E
		var SUM6 = 0;
		$(".changeShip" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM6 += total;
			$('#harga_tot6'+id).val(num(total));
			$('#harga_tot6_hidden'+id).val(total.toFixed(2));
		});
		$('#total_trucking1').val(num(SUM6));
		$('#total_trucking_hidden1').val(SUM6.toFixed(2));

		//SHIPPING L
		var SUM7 = 0;
		$(".changeShipL" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM7 += total;
			$('#harga_tot7'+id).val(num(total));
			$('#harga_tot7_hidden'+id).val(total.toFixed(2));
		});
		$('#total_lokal1').val(num(SUM7));
		$('#total_lokal_hidden1').val(SUM7.toFixed(2));
		
		// var total_grand = SUM + SUM2+ SUM3+ SUM4+ SUM5+ SUM6+ SUM7;
		
		// $('#grand_total').val(num(total_grand));
		// $('#grand_total_hidden').val(total_grand.toFixed(2));

        grandtotal()		
		totalInvoice()
	}

	function umLoad2(){	
		var hargaSat  = $('#harga_tot_hidden').val();
		var dataIni	  = Number($('#um_persen2').val());
		// var total     = getNum(hargaSat*dataIni/100).toFixed(2);
		
		// $('#harga_tot').val(num2(total));
		// $('#harga_tot_hidden').val(total);
		
		// console.log(hargaSat);
		// console.log(dataIni);
		// console.log(total);

		var nilai;
		var total;
		var id;
		//PRODUCT
		var SUM = 0;
		$(".changeProduct" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM += total;
			$('#harga_tot_'+id).val(num(total));
			$('#harga_tot_hidden'+id).val(total.toFixed(2));
		});
		$('#tot_product').val(num(SUM));
		$('#tot_product_hidden').val(SUM.toFixed(2));

		//AKSESORIS
		var SUM2 = 0;
		$(".changeAcc").each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM2 += total;
			$('#harga_tot2'+id).val(num(total));
			$('#harga_tot2_hidden'+id).val(total.toFixed(2));
		});
		$('#total_bq_nf').val(num(SUM2));
		$('#total_bq_nf_hidden').val(SUM2.toFixed(2));

		//MATERIAL
		var SUM3 = 0;
		$(".changeMat" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM3 += total;
			$('#harga_tot3'+id).val(num(total));
			$('#harga_tot3_hidden'+id).val(total.toFixed(2));
		});
		$('#total_material3').val(num(SUM3));
		$('#total_material_hidden3').val(SUM3.toFixed(2));

		//ENGINE
		var SUM4 = 0;
		$(".changeEng" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM4 += total;
			$('#harga_tot4'+id).val(num(total));
			$('#harga_tot4_hidden'+id).val(total.toFixed(2));
		});
		$('#total_enginering1').val(num(SUM4));
		$('#total_enginering_hidden1').val(SUM4.toFixed(2));

		//PACKING
		var SUM5 = 0;
		$(".changePack" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM5 += total;
			$('#harga_tot5'+id).val(num(total));
			$('#harga_tot5_hidden'+id).val(total.toFixed(2));
		});
		$('#total_packing1').val(num(SUM5));
		$('#total_packing_hidden1').val(SUM5.toFixed(2));

		//SHIPPING E
		var SUM6 = 0;
		$(".changeShip" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM6 += total;
			$('#harga_tot6'+id).val(num(total));
			$('#harga_tot6_hidden'+id).val(total.toFixed(2));
		});
		$('#total_trucking1').val(num(SUM6));
		$('#total_trucking_hidden1').val(SUM6.toFixed(2));

		//SHIPPING L
		var SUM7 = 0;
		$(".changeShipL" ).each(function() {
			id = $(this).data('id');
			nilai 	= Number($(this).val().split(",").join(""));
			total   = nilai * (dataIni/100);
			SUM7 += total;
			$('#harga_tot7'+id).val(num(total));
			$('#harga_tot7_hidden'+id).val(total.toFixed(2));
		});
		$('#total_lokal1').val(num(SUM7));
		$('#total_lokal_hidden1').val(SUM7.toFixed(2));
		
		// var total_grand = SUM + SUM2+ SUM3+ SUM4+ SUM5+ SUM6+ SUM7;
		
		// $('#grand_total').val(num(total_grand));
		// $('#grand_total_hidden').val(total_grand.toFixed(2));

        grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal(){
	  var total=0
	  var total2=0
		$(".amount1").each(function(){
			 total += getNum($(this).val()||0);
			 total2 += getNum($(this).val()||0);
		});
		
		$(".result1").val(num(total));
		$(".result1_hidden").val(num3(total2));
		
		grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal2(){
	  var total=0
	  var total2=0
		$(".amount2").each(function(){
			 total += getNum($(this).val()||0);
			 total2 += getNum($(this).val()||0);
		});
		
		$(".result2").val(num(total));
		$(".result2_hidden").val(num3(total2));
		
		grandtotal()		
		totalInvoice()
	}

	function fnAlltotal3(){
		var total31=0
		var total32=0
		$(".amount3").each(function(){
			 total31 += getNum($(this).val()||0);
			 total32 += getNum($(this).val()||0);
		});
		
		$(".result3").val(num(total31));
		$(".result3_hidden").val(num3(total32));
		
		grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal4(){
	  var total41=0
	  var total42=0
		$(".amount4").each(function(){
			 total41 += getNum($(this).val()||0);
			 total42 += getNum($(this).val()||0);
		});
		
		$(".result4").val(num(total41));
		$(".result4_hidden").val(num3(total42));
		
		grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal5(){
		var total51=0
		var total52=0
		$(".amount5").each(function(){
			 total51 += getNum($(this).val()||0);
			 total52 += getNum($(this).val()||0);
		});
		
		$(".result5").val(num(total51));
		$(".result5_hidden").val(num3(total52));
		
		grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal6(){
		var total61=0
		var total62=0
		$(".amount6").each(function(){
			 total61 += getNum($(this).val()||0);
			 total62 += getNum($(this).val()||0);
		});
		
		$(".result6").val(num(total61));
		$(".result6_hidden").val(num3(total62));
		
		grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal7(){
		var total71=0
		var total72=0

		$(".amount7").each(function(){
			 total71 += getNum($(this).val()||0);
			 total72 += getNum($(this).val()||0); 
		});
		
		$(".result7").val(num(total71));
		$(".result7_hidden").val(num3(total72));
		
		grandtotal()		
		totalInvoice()
	}
	
	function fnAlltotal8(){
		var total81=0
		var total82=0
		$(".amount8").each(function(){
			 total81 += getNum($(this).val()||0);
			 total82 += getNum($(this).val()||0);
		});
		
		$(".result8").val(num(total81));
		$(".result8_hidden").val(num3(total82));
		
		grandtotal()		
		totalInvoice()
	}
		
	function grandtotal() {
		var dataPpn    = $('#ppnselect').val();
		var result1_hidden1 = 0;
		var result2_hidden1 = 0;
		var result3_hidden1 = 0;
		var result4_hidden1 = 0;
		var result5_hidden1 = 0;
		var result6_hidden1 = 0;
		var result7_hidden1 = 0;
		var result8_hidden1 = 0;
		
		var result1_hidden  = getNum($('.result1_hidden').val());
		var result2_hidden  = getNum($('.result2_hidden').val());
		var result3_hidden  = getNum($('.result3_hidden').val());
		var result4_hidden  = getNum($('.result4_hidden').val());
		var result5_hidden  = getNum($('.result5_hidden').val());
		var result6_hidden  = getNum($('.result6_hidden').val());
		var result7_hidden  = getNum($('.result7_hidden').val());
		var result8_hidden  = getNum($('.result8_hidden').val());
		var diskon_hidden  			= getNum($('.diskon_hidden').val());
		var potongan_retensi_hidden = getNum($('.potongan_retensi_hidden').val());
		var down_payment_hidden     = getNum($('.down_payment_hidden').val());
		var uang_muka  				= getNum($('.persen').val());
		var uang_muka2 				= getNum($('.persen2').val());
				   
				   
		if(result1_hidden==null){
			result1_hidden1 = 0;
		}
		else{
			result1_hidden1 = result1_hidden;
		}
		
	 	if(result2_hidden==null){
			result2_hidden1 = 0;
		}
		else{
			result2_hidden1 = result2_hidden;
		}
		
		if(result3_hidden==null){
			result3_hidden1 = 0;
		}
		else{
			result3_hidden1 = result3_hidden;
		}
		
		if(result4_hidden==null){
			result4_hidden1 = 0;
		}
		else{
			result4_hidden1 = result4_hidden;
		}
		
        if(result5_hidden==null){
			result5_hidden1 = 0;
		}
		else{
			result5_hidden1 = result5_hidden;
		}
	  	
        if(result6_hidden==null){
			result6_hidden1 = 0;
		}
		else{
			result6_hidden1 = result6_hidden;
		}
		
		if(result7_hidden==null){
			result7_hidden1 = 0;
		}
		else{
			result7_hidden1 = result7_hidden;
		}
		
		if(result8_hidden==null){
			result8_hidden1 = 0;
		}
		else{
			result8_hidden1 = result8_hidden;
		}

		var grandtotal 	= 	getNum(result1_hidden1)
							+ getNum(result2_hidden1)
							+ getNum(result3_hidden1)
							+ getNum(result4_hidden1)
							+ getNum(result5_hidden1)
							+ getNum(result6_hidden1)
							+ getNum(result7_hidden1)
							+ getNum(result8_hidden1);
		
		console.log(result1_hidden1)
		console.log(result2_hidden1)
		console.log(result3_hidden1)
		console.log(uang_muka)
		
		var uangmuka   	= 	(
							getNum(result1_hidden1)
							+ getNum(result2_hidden1)
							+ getNum(result3_hidden1)
							)
							* getNum(uang_muka/100);
		   
	    var uangmuka2  	= 	(
							getNum(result1_hidden1)
							+ getNum(result2_hidden1)
							+ getNum(result3_hidden1))
							*
							( getNum(uang_muka2/100) - (getNum(uang_muka/100) * getNum(uang_muka2/100)) );
		
		
		if(dataPpn==1){
			var totalPpn     = getNum((grandtotal - diskon_hidden - potongan_retensi_hidden - down_payment_hidden - uangmuka)*0.1);			
		}
		else{
			var totalPpn     = 0;	
		}
		
		console.log(uangmuka)
		console.log(uangmuka2)
		console.log(grandtotal)

		$(".down_payment").val(number_format(uangmuka,2));
		$(".down_payment_hidden").val(num3(uangmuka));
		
		$(".down_payment2").val(number_format(uangmuka2,2));
		$(".down_payment_hidden2").val(num3(uangmuka2));
		
		$(".grand_total").val(number_format(grandtotal,2));
		$(".grand_total_hidden").val(num3(grandtotal));
		
		ppn();
	}
		
	function ppn() {  
		var dataPpn                 = $('#ppnselect').val();
		var ppntotal                = 0
		var grandtotal              = $(".grand_total_hidden").val();
		var diskon_hidden           = $('.diskon_hidden').val();
		var potongan_retensi_hidden = $('.potongan_retensi_hidden').val();
		var down_payment_hidden     = $('.down_payment_hidden').val()
		var down_payment_hidden2    = $('.down_payment_hidden2').val()
		 
		if(diskon_hidden==null){
			diskon_hidden1 = 0;
		}
		else{
			diskon_hidden1 = diskon_hidden;
		}
		
	 	if(potongan_retensi_hidden==null){
			potongan_retensi_hidden1 = 0;
		}
		else{
			potongan_retensi_hidden1 = potongan_retensi_hidden;
		}
		
		if(down_payment_hidden==null){
			down_payment_hidden1 = 0;
		}
		else{
			down_payment_hidden1 = down_payment_hidden;
		}
		
		if(down_payment_hidden2==null){
			down_payment_hidden12 = 0;
		}
		else{
			down_payment_hidden12 = down_payment_hidden2;
		}
  
		if(dataPpn==1){
			var totalPpn     = getNum((grandtotal - diskon_hidden1 - potongan_retensi_hidden1 - down_payment_hidden1- down_payment_hidden12)*0.1);			
		}
		else{
			var totalPpn     = 0;	
		}

		$('.ppn').val(num(totalPpn));
		$('.ppn_hidden').val(num3(totalPpn));
		totalInvoice()
	}
	
	function totalInvoice() {
		var grandtotal 		= 0;
		var result1_hidden1 = 0;
		var result2_hidden1 = 0;
		var result3_hidden1 = 0;
		var result4_hidden1 = 0;
		var result5_hidden1 = 0;
		var result6_hidden1 = 0;
		var result7_hidden1 = 0;
		var result8_hidden1 = 0;
		var potongan_retensi_hidden1 	= 0;
		var down_payment_hidden1 		= 0;
		var down_payment_hidden12 		= 0;
		var result1_hidden  = $('.result1_hidden').val();
		var result2_hidden  = $('.result2_hidden').val();
		var result3_hidden  = $('.result3_hidden').val();
		var result4_hidden  = $('.result4_hidden').val();
		var result5_hidden  = $('.result5_hidden').val();
		var result6_hidden  = $('.result6_hidden').val();
		var result7_hidden  = $('.result7_hidden').val();
		var result8_hidden  = $('.result8_hidden').val();
		var diskon_hidden  				= $('.diskon_hidden').val();
		var potongan_retensi_hidden  	= $('.potongan_retensi_hidden').val();
		var potongan_retensi_hidden2  	= $('.potongan_retensi_hidden2').val();
		var ppn_hidden  				= $('.ppn_hidden').val();
		var down_payment_hidden  		= $('.down_payment_hidden').val()
		var down_payment_hidden2  		= $('.down_payment_hidden2').val()
			   
		if(result1_hidden==null){
			result1_hidden1 = 0;
		}
		else{
			result1_hidden1 = result1_hidden;
		}
		
		if(result2_hidden==null){
			result2_hidden1 = 0;
		}
		else{
			result2_hidden1 = result2_hidden;
		}
		
		if(result3_hidden==null){
			result3_hidden1 = 0;
		}
		else{
			result3_hidden1 = result3_hidden;
		}
		
		if(result4_hidden==null){
			result4_hidden1 = 0;
		}
		else{
			result4_hidden1 = result4_hidden;
		}
		
		if(result5_hidden==null){
			result5_hidden1 = 0;
		}
		else{
			result5_hidden1 = result5_hidden;
		}
	  	
		if(result6_hidden==null){
			result6_hidden1 = 0;
		}
		else{
			result6_hidden1 = result6_hidden;
		}
		
		if(result7_hidden==null){
			result7_hidden1 = 0;
		}
		else{
			result7_hidden1 = result7_hidden;
		}
		
		if(result8_hidden==null){
			result8_hidden1 = 0;
		}
		else{
			result8_hidden1 = result8_hidden;
		}
		
		if(potongan_retensi_hidden==null){
			potongan_retensi_hidden1 = 0;
		}
		else{
			potongan_retensi_hidden1 = potongan_retensi_hidden;
		}
		
		if(down_payment_hidden==null){
			down_payment_hidden1 = 0;
		}
		else{
			down_payment_hidden1 = down_payment_hidden;
		}
		
		if(down_payment_hidden2==null){
			down_payment_hidden12 = 0;
		}
		else{
			down_payment_hidden12 = down_payment_hidden2;
		}
				   
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
		$(".total_invoice_hidden").val(num3(grandtotal));
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