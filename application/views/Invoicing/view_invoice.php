
<div class="nav-tabs-salesorder">
    <div class="tab-content">
        <div class="tab-pane active" id="salesorder">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="col-sm-6 form-horizontal">
					    <div class="row">
                          <div class="form-group">
                              <label for="no_ipp" class="col-sm-4 control-label">No Invoice </font></label>
                              <div class="col-sm-6">
                                 <input type="text" name="no_ipp" id="no_ipp" value="<?php echo $data_header->no_invoice ?>" class="form-control input-sm" readonly>

                              </div>

                          </div>
                        </div>
						<div class="row">
                          <div class="form-group">
                              <label for="no_ipp" class="col-sm-4 control-label">No SO </font></label>
                              <div class="col-sm-6">

                                 <input type="text" name="no_so" id="no_so" value="<?php echo $data_header->so_number ?>" class="form-control input-sm" readonly>

                              </div>

                          </div>
                        </div>
						<div class="row">
                          <div class="form-group ">
                            <?php
                            $tglinv=date('Y-m-d');
                            ?>
                            <label for="tgl_inv" class="col-sm-4 control-label">Tgl Invoice</label>
                            <div class="col-sm-6">

                                 <!--input type="text" name="tanggal_invoice" id="tgl_inv" class="form-control input-sm datepicker" value="<?php // echo $tglinv?>"-->
                                <input type="text" name="tanggal_invoice" id="tgl_inv" class="form-control input-sm" value="<?php echo $data_header->tgl_invoice ?>" readonly>

                            </div>
                          </div>

                        </div>
                        <div class="row">
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Nama Customer </font></label>
                              <div class="col-sm-6">

                                  <input type="hidden" name="id_customer" id="id_customer" class="form-control input-sm" value="<?php  echo $data_header->id_customer?>" readonly>

                                  <input type="text" name="nm_customer" id="nm_customer" class="form-control input-sm" value="<?php  echo $data_header->nm_customer?>" readonly>

                              </div>

                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group">
                            <label for="alamat" class="col-sm-4 control-label">Alamat Customer </font></label>
                            <div class="col-sm-6" >

                              <textarea name="alamatcustomer" class="form-control input-sm" id="alamat" height=100 readonly><?php echo $alamat_cust->alamat ?></textarea>

                            </div>
                          </div>

                        </div>

						<div class="row">
                          <div class="form-group">
                              <!--div class="col-sm-6" style="padding-top: 8px;">
                                  <?php // echo ": ".$data_cust->nm_customer?>
                                  <input type="hidden" name="idcustomer_do" value="<?php // echo $data_cust->id_customer?>">
                                  <input type="hidden" name="nmcustomer_do" value="<?php // echo $data_cust->nm_customer?>">
                              </div-->
                              <label for="jenis_invoice" class="col-sm-4 control-label">Jenis Invoice </font></label>
                              <div class="col-sm-6">

                                    <select id="jenis_invoice" name="jenis_invoice" class="form-control input-sm" style="width: 100%;" tabindex="-1" required readonly>

									<?php
									$jenis = $data_header->jenis_invoice;
									?>

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
                              <div class="col-sm-6">

							        <?php
									$totppn = $data_header->total_ppn;

									if($totppn != 0 ){
										$ppn =1;
									}
									else{
										$ppn =0;
									}
									?>

                                    <select id="ppnselect" name="ppnselect" class="form-control input-sm" style="width: 100%;" required readonly>
                                    <option value="" <?php echo ($ppn == '') ? "selected": "" ?>>SELECT AN OPTION</option>
									<option value="1" <?php echo ($ppn == '1') ? "selected": "" ?>>PPN</option>
									<option value="0"  <?php echo ($ppn == '0') ? "selected": "" ?>>NON PPN</option>
									</select>

                              </div>

                          </div>
                        </div>

                   </div>

                   <div class="col-sm-6 form-horizontal">


					    <div class="form-group ">
                            <label for="idsalesman" class="col-sm-4 control-label">No PO </label>
                            <div class="col-sm-6" style="padding-top: 8px;">

                              <input type="text" name="nomor_po" class="form-control input-sm" id="nomor_po" value="<?php echo $data_header->no_po ?>" readonly>


                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="idsalesman" class="col-sm-4 control-label">F. No Faktur </label>
                            <div class="col-sm-6" style="padding-top: 8px;">

                              <input type="text" name="nomor_faktur" class="form-control input-sm" id="nomor_faktur" value="<?php echo $data_header->no_faktur ?>" readonly>


                            </div>
                        </div>
						<div class="form-group ">
                            <label for="idsalesman" class="col-sm-4 control-label">No Pajak </label>
                            <div class="col-sm-6" style="padding-top: 8px;">

                              <input type="text" name="nomor_pajak" class="form-control input-sm" id="nomor_pajak" value="<?php echo $data_header->no_pajak ?>" readonly>


                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="tgldo" class="col-sm-4 control-label">Term Payment </label>
                            <div class="col-sm-6" style="padding-top: 8px;">


                              <input type="text" name="top" class="form-control input-sm" id="top" value="<?php echo $data_header->payment_term ?>" readonly>


                            </div>
                        </div>
						<div class="form-group ">
                            <label for="tgldo" class="col-sm-4 control-label">Kurs </label>
                            <div class="col-sm-6" style="padding-top: 8px;">


                              <input type="text" name="kurs" class="form-control input-sm" id="kurs" value="<?php echo number_format($data_header->kurs_jual) ?>" readonly>
                            </div>
                        </div>
						<div class="form-group ">
                            <label for="tgldo" class="col-sm-4 control-label">No Delivery </label>
                            <div class="col-sm-4" style="padding-top: 8px;">
                              <?php 
							  $dtdev=$this->db->query("select * from penagihan where id='".$data_header->id_penagihan."'")->row();
							  if(!empty($dtdev)) echo $dtdev->delivery_no;?>
                            </div>
                        </div>
						<!--
						<?php if ($jenis == 'uang muka') {?>
						<div class="form-group">
                              <label for="jenis_invoice" class="col-sm-4 control-label">Persentase UM(%)</font></label>
                              <div class="col-sm-6">
                                 <input type="text" name="um_persen" id="um_persen" class="form-control input-sm" value="<?php echo number_format($so->uang_muka_persen) ?>" readonly >
                              </div>
                        </div>
                        <?php }?>

						<?php if ($jenis == 'progress') {?>
						<div class="form-group">
                              <label for="jenis_invoice" class="col-sm-4 control-label">Persentase Progress</font></label>
                              <div class="col-sm-6">
                                 <input type="text" name="umpersen" id="umpersen" value="<?=number_format($so->persentase_progress); ?>" class="form-control input-sm" readonly>
                              </div>
                        </div>
                        <?php }?>

						<?php if ($jenis == 'progress') {?>
						<div class="form-group">
                              <label for="jenis_invoice" class="col-sm-4 control-label">Uang Muka (%)<font></label>
                              <div class="col-sm-6">
                                 <input type="text" name="persen" id="persen" class="form-control input-sm persen" value="<?=number_format($so->uang_muka_persen); ?>" readonly>
                              </div>
                        </div>
                        <?php }?>
                        -->
                </div>

            </div>

<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='13'><b>PRODUCT</b></td>
		</tr>
		<tr class='bg-bluexyz'>
		    <th class="text-center" width='2%'>#</th>
			<th class="text-center">Product</th>
			<th class="text-center">Nama PO</th>
			<th class="text-center">Desc</th>
			<th class="text-center" width='6%'>Dim 1</th>
			<th class="text-center" width='6%'>Dim 2</th>
			<th class="text-center" width='5%'>Lin</th>
			<th class="text-center" width='5%'>Pre</th>
			<th class="text-center" width='10%'>Specification</th>
			<th class="text-center" width='7%'>Unit Price</th>
			<th class="text-center" width='5%'>Qty</th>
			<th class="text-center" width='6%'>Unit</th>
			<th class="text-center" width='8%'>Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$numb=0;
		$SUM = 0;
		$no = 0;
		foreach($getDetail AS $val => $valx){
			if($valx['harga_total'] > 0){
			$no++;
            $numb++;
			$pr='pr';
			$SUM += $valx['harga_total'];
			?>
			<tr id='tr_<?= $numb;?>' >
			<td align='center'><?=$no;?></td>
			<td ><?= strtoupper($valx['nm_material']);?></td>
			<td ><?= strtoupper($valx['product_cust']);?></td>
			<td ><?= strtoupper($valx['desc']);?></td>
			<td align='right'><?= number_format($valx['dim_1']);?></td>
			<td align='right'><?= number_format($valx['dim_2']);?></td>
			<td align='center'><?= $valx['liner'];?></td>
			<td align='center'>PN <?= number_format($valx['pressure']);?></td>
			<td ><?= strtoupper($valx['spesifikasi']);?></td>
			<td align='right'><?= number_format($valx['harga_satuan'],2);?></td>
			<td align='center'><?= number_format($valx['qty']);?></td>
			<td align='center'><?= strtoupper($valx['unit']);?></td>
			<td align='right'><?= number_format($valx['harga_total'],2);?></td>
			<?php
			}
		}
		?>
		<tr class='FootColor'>
			<td colspan='12'><b>TOTAL COST  OF PRODUCT</b></td>
			<td align='right'><b><?=number_format($SUM,2);?></b></td>
		</tr>
	</tbody>
	<?php
	$non_frp = $this->db->query("SELECT * FROM tr_invoice_detail WHERE kategori_detail='BQ' AND no_invoice ='$no_invoice'")->result_array();

	$SUM_NONFRP = 0;
	if(!empty($non_frp)){
		echo "<tbody>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='13'><b>BILL OF QUANTITY NON FRP</b></td>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
			    echo "<th class='text-center'>#</th>";
				echo "<th class='text-center' colspan='8'>Material Name</th>";
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
			$SUM_NONFRP += $valx['harga_total'];
			?>
			<tr id='tr1_<?= $numb2;?>' >
			<td align='center'>

			</td>
			<td colspan='8'>
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
				$unit2= strtoupper($valx['unit']);
				?>
				<input type="text" class="form-control text-right" id="unit2" name="data2[<?php echo $numb2 ?>][unit2]" value="<?php echo set_value('unit2', isset($unit2) ? $unit2 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			<?php
				$harga_sat2= number_format($valx['harga_satuan'],2);
				$harga_sat2_hidden= round($valx['harga_satuan'],2);
				?>
				<input type="text" class="form-control text-right" id="harga_sat2" name="data2[<?php echo $numb2 ?>][harga_sat2]" value="<?php echo set_value('harga_sat2', isset($harga_sat2) ? $harga_sat2 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control" id="harga_sat2_hidden<?php echo $numb2 ?>" name="data2[<?php echo $numb2 ?>][harga_sat2_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat2_hidden) ? $harga_sat2_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
			<td>
			<?php
				$harga_tot2= number_format($valx['harga_total'],2);
				$harga_tot2_hidden= round($valx['harga_total'],2);
				?>
				<input type="text" class="form-control text-right" id="harga_tot2<?php echo $numb2 ?>" name="data2[<?php echo $numb2 ?>][harga_tot2]" value="<?php echo set_value('harga_tot2', isset($harga_tot2) ? $harga_tot2 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control amount2" id="harga_tot2_hidden<?php echo $numb2 ?>" name="data2[<?php echo $numb2 ?>][harga_tot2_hidden]" value="<?php echo set_value('harga_tot2_hidden', isset($harga_tot2_hidden) ? $harga_tot2_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		<?php

		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='12'><b>TOTAL BILL OF QUANTITY NON FRP</b></td> ";
			?>
			<td align="right">
			<?php
				$total_bq_nf= number_format($SUM_NONFRP,2);
				$total_bq_nf_hidden= round($SUM_NONFRP,2);
				?>
				<input type="text" class="form-control result2 text-right" id="total_bq_nf" name="total_bq_nf" value="<?php echo set_value('total_bq_nf', isset($total_bq_nf) ? $total_bq_nf : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control result2_hidden" id="total_bq_nf_hidden" name="total_bq_nf_hidden" value="<?php echo set_value('total_bq_nf_hidden', isset($total_bq_nf_hidden) ? $total_bq_nf_hidden : ''); ?>" placeholder="Automatic" readonly >

		   </td>
			<?php

		echo "</tr>";
		echo "</tbody>";
	}

	$material = $this->db->query("SELECT * FROM tr_invoice_detail WHERE kategori_detail='MATERIAL' AND no_invoice ='$no_invoice'")->result_array();

	$SUM_MAT = 0;
	if(!empty($material)){
		echo "<tbody>";
			echo "<tr class='bg-blue'>";
				echo "<td class='text-left headX HeaderHr' colspan='13'><b>MATERIAL</b></td>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
			    echo "<th class='text-center'>#</th>";
				echo "<th class='text-center' colspan='8'>Material Name</th>";
				echo "<th class='text-center'>Weight</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		$numb3 =0;
		foreach($material AS $val => $valx){
			$numb3++;
			$SUM_MAT += $valx['harga_total'];
		?>
		<tr id='tr2_<?= $numb3;?>' >
			<td align='center'>

			</td>
			<td colspan='8'>
			    <?php
				$material_name3= strtoupper($valx['nm_material']);
				?>
				<input type="text" class="form-control" id="material_name3" name="data3[<?php echo $numb3 ?>][material_name3]" value="<?php echo set_value('material_name3', isset($material_name3) ? $material_name3 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			   <input type="text" class="form-control qty_material" id="qty3" data-nomor='<?php echo $numb3 ?>'  name="data3[<?php echo $numb3 ?>][qty3]" value="<?php echo set_value('qty3', isset($valx['qty']) ? $valx['qty'] : ''); ?>" placeholder="Automatic" >
            </td>
			<td>
			<?php
				$unit3= strtoupper($valx['unit']);
				?>
				<input type="text" class="form-control" id="unit3" name="data3[<?php echo $numb3 ?>][unit3]" value="<?php echo set_value('unit3', isset($unit3) ? $unit3 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			<?php
				$harga_sat3= number_format($valx['harga_satuan'],2);
				$harga_sat3_hidden= round($valx['harga_satuan'],2);
				?>
				<input type="text" class="form-control text-right" id="harga_sat3" name="data3[<?php echo $numb3 ?>][harga_sat3]" value="<?php echo set_value('harga_sat3', isset($harga_sat3) ? $harga_sat3 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control" id="harga_sat3_hidden<?php echo $numb3 ?>" name="data3[<?php echo $numb3 ?>][harga_sat3_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat3_hidden) ? $harga_sat3_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
			<td>
			<?php
				$harga_tot3= number_format($valx['harga_total'],2);
				$harga_tot3_hidden= round($valx['harga_total'],2);
				?>
				<input type="text" class="form-control text-right" id="harga_tot3<?php echo $numb3 ?>" name="data3[<?php echo $numb3 ?>][harga_tot3]" value="<?php echo set_value('harga_tot3', isset($harga_tot3) ? $harga_tot3 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control amount3" id="harga_tot3_hidden<?php echo $numb3 ?>" name="data3[<?php echo $numb3 ?>][harga_tot3_hidden]" value="<?php echo set_value('harga_tot3_hidden', isset($harga_tot3_hidden) ? $harga_tot3_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		</tr>
		<?php

		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='12'><b>TOTAL MATERIAL</b></td> ";
			?>
			<td align="right">
			<?php
				$total_material= number_format($SUM_MAT,2);
				$total_material_hidden= round($SUM_MAT,2);
				?>
			<input type="text" class="form-control result3 text-right" id="total_material<?php echo $numb3 ?>" name="total_material" value="<?php echo set_value('total_material', isset($total_material) ? $total_material : ''); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control result3_hidden" id="total_material_hidden<?php echo $numb3 ?>" name="total_material_hidden" value="<?php echo set_value('total_material_hidden', isset($total_material_hidden) ? $total_material_hidden : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<?php

		echo "</tr>";
		echo "</tbody>";
	}
	?>
	<?php
	$getEngCost = $this->db->query("SELECT * FROM tr_invoice_detail WHERE kategori_detail='ENGINEERING' AND no_invoice ='$no_invoice'")->result_array();


	$SUM1=0;
	if(!empty($getEngCost)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='13'><b>ENGINEERING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
		    <th class="text-center">#</th>
			<th class="text-center" colspan='8'>Item Product</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Unit</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no1=0;
		$SUM1=0;
		$numb4=0;
		foreach($getEngCost AS $val => $valx){
			$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$Price1 	= (!empty($valx['harga_satuan']))?number_format($valx['harga_satuan'],2):'-';
			$TotalP1 	= (!empty($valx['harga_total']))?number_format($valx['harga_total'],2):'-';
			$SUM1 += $valx['harga_total'];
			$no1++;
			$numb4++;
		?>
		<tr id='tr3_<?= $numb4;?>' >
			<td align='center'>

			</td>
			<td colspan='8'>
			    <?php
				$material_name4= strtoupper($valx['nm_material']);
				?>
				<input type="text" class="form-control" id="material_name4" name="data4[<?php echo $numb4 ?>][material_name4]" value="<?php echo set_value('material_name4', isset($material_name4) ? $material_name4 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			   <input type="text" class="form-control qty_enginering" id="qty4" data-nomor='<?php echo $numb4 ?>'  name="data4[<?php echo $numb4 ?>][qty4]" value="<?php echo set_value('qty4', isset($Qty1) ? $Qty1 : ''); ?>" placeholder="Automatic" >
            </td>
			<td>
			<?php
				$unit4= strtoupper($valx['unit']);
				?>
				<input type="text" class="form-control" id="unit4" name="data4[<?php echo $numb4 ?>][unit4]" value="<?php echo set_value('unit4', isset($unit4) ? $unit4 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			<?php
				$harga_sat4= (!empty($valx['harga_satuan']))?number_format($valx['harga_satuan'],2):'-';
				$harga_sat4_hidden= (!empty($valx['harga_satuan']))?round($valx['harga_satuan'],2):'-';
				?>
				<input type="text" class="form-control text-right" id="harga_sat4" name="data4[<?php echo $numb4 ?>][harga_sat4]" value="<?php echo set_value('harga_sat4', isset($harga_sat4) ? $harga_sat4 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control" id="harga_sat4_hidden<?php echo $numb4 ?>" name="data4[<?php echo $numb4 ?>][harga_sat4_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat4_hidden) ? $harga_sat4_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
			<td>
			<?php
				$harga_tot4= (!empty($valx['harga_total']))?number_format($valx['harga_total'],2):'-';
				$harga_tot4_hidden= (!empty($valx['harga_total']))?round($valx['harga_total'],2):'-';
				?>
				<input type="text" class="form-control text-right" id="harga_tot4<?php echo $numb4 ?>" name="data4[<?php echo $numb4 ?>][harga_tot4]" value="<?php echo set_value('harga_tot4', isset($harga_tot4) ? $harga_tot4 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control amount4" id="harga_tot4_hidden<?php echo $numb4 ?>" name="data4[<?php echo $numb4 ?>][harga_tot4_hidden]" value="<?php echo set_value('harga_tot4_hidden', isset($harga_tot4_hidden) ? $harga_tot4_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		</tr>
		<?php

		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='12'><b>TOTAL ENGINEERING COST</b></td> ";
			?>
			<td align="right">
			<?php
				$total_enginering= number_format($SUM1,2);
				$total_enginering_hidden= round($SUM1,2);
				?>
			<input type="text" class="form-control result4 text-right" id="total_enginering<?php echo $numb4 ?>" name="total_enginering" value="<?php echo set_value('total_enginering', isset($total_enginering) ? $total_enginering : ''); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control result4_hidden" id="total_enginering_hidden<?php echo $numb4 ?>" name="total_enginering_hidden" value="<?php echo set_value('total_enginering_hidden', isset($total_enginering_hidden) ? $total_enginering_hidden : ''); ?>" placeholder="Automatic" readonly >
            </td>
		</tr>
	</tbody>
	<?php
	}

	$getPackCost = $this->db->query("SELECT * FROM tr_invoice_detail WHERE kategori_detail='PACKING' AND no_invoice ='$no_invoice'")->result_array();

	$SUM2=0;
	if(!empty($getPackCost)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='13'><b>PACKING COST</b></td>
		</tr>
		<tr class='bg-bluexyz'>
		    <th class="text-center">#</th>
			<th class="text-center" colspan='10'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody>
		<?php
		$no2=0;
		$SUM2=0;
		$numb5=0;
		foreach($getPackCost AS $val => $valx){
			$no2++;
			$SUM2 += $valx['harga_total'];
			$numb5++;
			?>
			<tr id='tr4_<?= $numb5;?>' >
			<td align='center'>

			</td>
			<td colspan='10'>
			    <?php
				$material_name5= strtoupper($valx['nm_material']);
				?>
				<input type="text" class="form-control" id="material_name5" name="data5[<?php echo $numb5 ?>][material_name5]" value="<?php echo set_value('material_name5', isset($material_name5) ? $material_name5 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<!--<td>
			   <input type="text" class="form-control qty_enginering" id="qty5" data-nomor='<?php echo $numb5 ?>'  name="data5[<?php echo $numb5 ?>][qty5]" value="<?php echo set_value('qty5', isset($Qty1) ? $Qty1 : ''); ?>" placeholder="Automatic" >
            </td>-->
			<td>
			<?php
				$unit5= strtoupper($valx['unit']);
				?>
				<input type="text" class="form-control" id="unit5" name="data5[<?php echo $numb5 ?>][unit5]" value="<?php echo set_value('unit5', isset($unit5) ? $unit5 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<!--<td>
			<?php
				$harga_sat5= (!empty($valx['harga_satuan']))?number_format($valx['harga_satuan'],2):'-';
				$harga_sat5_hidden= (!empty($valx['harga_satuan']))?round($valx['harga_satuan'],2):'-';
				?>
				<input type="text" class="form-control" id="harga_sat5" name="data5[<?php echo $numb5 ?>][harga_sat5]" value="<?php echo set_value('harga_sat5', isset($harga_sat5) ? $harga_sat5 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control" id="harga_sat5_hidden<?php echo $numb5 ?>" name="data5[<?php echo $numb5 ?>][harga_sat5_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat5_hidden) ? $harga_sat5_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>-->
			<td>
			<?php
				$harga_tot5= number_format($valx['harga_total'],2);
				$harga_tot5_hidden= round($valx['harga_total'],2);
				?>
				<input type="text" class="form-control  harga_tot5 text-right" id="harga_tot5<?php echo $numb5 ?>" data-nomor='<?php echo $numb5 ?>' name="data5[<?php echo $numb5 ?>][harga_tot5]" value="<?php echo set_value('harga_tot5', isset($harga_tot5) ? $harga_tot5 : ''); ?>" placeholder="Automatic" >
                <input type="hidden" class="form-control amount5" id="harga_tot5_hidden<?php echo $numb5 ?>" name="data5[<?php echo $numb5 ?>][harga_tot5_hidden]" value="<?php echo set_value('harga_tot5_hidden', isset($harga_tot5_hidden) ? $harga_tot5_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		</tr>
		<?php

		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='12'><b>TOTAL PACKING COST</b></td> ";
			?>
			<td align="right">
			<?php
				$total_packing= number_format($SUM2,2);
				$total_packing_hidden= round($SUM2,2);
				?>
			<input type="text" class="form-control result5 text-right" id="total_packing<?php echo $numb5 ?>" name="total_packing" value="<?php echo set_value('total_packing', isset($total_packing) ? $total_packing : ''); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control result5_hidden" id="total_packing_hidden<?php echo $numb5 ?>" name="total_packing_hidden" value="<?php echo set_value('total_packing_hidden', isset($total_packing_hidden) ? $total_packing_hidden : ''); ?>" placeholder="Automatic" readonly >
            </td>
		</tr>


	</tbody>
	<?php
	}
	$getTruck = $this->db->query("SELECT * FROM tr_invoice_detail WHERE kategori_detail='TRUCKING' AND no_invoice ='$no_invoice'")->result_array();
	$SUM3=0;
	if(!empty($getTruck)){
	?>
	<tbody>
		<tr>
			<td class="text-left headX HeaderHr" colspan='13'><b>TRUCKING</b></td>
		</tr>
		<tr class='bg-bluexyz'>
		    <th class="text-center">#</th>
			<th class="text-center" colspan='7'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Fumigation</th>
			<th class="text-center">Price</th>
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
			$SUM3 += $valx['harga_total'];
			$no3++;
			$numb6++;
			?>
			<tr id='tr5_<?= $numb6;?>' >
			<td align='center'>

			</td>
			<td colspan='7'>
			    <?php
				$material_name6= strtoupper($valx['nm_material']);
				?>
				<input type="text" class="form-control" id="material_name6" name="data6[<?php echo $numb6 ?>][material_name6]" value="<?php echo set_value('material_name6', isset($material_name6) ? $material_name6 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			<?php
				$unit6= strtoupper($valx['unit']);
				?>
				<input type="text" class="form-control" id="unit6" name="data6[<?php echo $numb6 ?>][unit6]" value="<?php echo set_value('unit6', isset($unit6) ? $unit6 : ''); ?>" placeholder="Automatic" readonly >
            </td>
			<td>
			   <input type="text" class="form-control qty_trucking" id="qty6" data-nomor='<?php echo $numb6 ?>'  name="data6[<?php echo $numb6 ?>][qty6]" value="<?php echo set_value('qty6', isset($Qty3) ? $Qty3 : ''); ?>" placeholder="Automatic" >
            </td>
			<td>
			   	<?php
				$fumigasi= $valx['spesifikasi'];
				?>
			   <input type="text" class="form-control fumigasi" id="fumigasi" data-nomor='<?php echo $numb6 ?>'  name="data6[<?php echo $numb6 ?>][fumigasi]" value="<?php echo set_value('fumigasi', isset($fumigasi) ? $fumigasi : ''); ?>" placeholder="Automatic" readonly>
            </td>
			<td>
			<?php
				$harga_sat6= number_format($valx['harga_satuan'],2);
				$harga_sat6_hidden= round($valx['harga_satuan'],2);
				?>
				<input type="text" class="form-control text-right" id="harga_sat6" name="data6[<?php echo $numb6 ?>][harga_sat6]" value="<?php echo set_value('harga_sat6', isset($harga_sat6) ? $harga_sat6 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control" id="harga_sat6_hidden<?php echo $numb6 ?>" name="data6[<?php echo $numb6 ?>][harga_sat6_hidden]" value="<?php echo set_value('harga_sat2_hidden', isset($harga_sat6_hidden) ? $harga_sat6_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
			<td>
			<?php
				$harga_tot6= number_format($valx['harga_total'],2);
				$harga_tot6_hidden= round($valx['harga_total'],2);
				?>
				<input type="text" class="form-control text-right" id="harga_tot6<?php echo $numb6 ?>" name="data6[<?php echo $numb6 ?>][harga_tot6]" value="<?php echo set_value('harga_tot6', isset($harga_tot6) ? $harga_tot6 : ''); ?>" placeholder="Automatic" readonly >
                <input type="hidden" class="form-control amount6" id="harga_tot6_hidden<?php echo $numb6 ?>" name="data6[<?php echo $numb6 ?>][harga_tot6_hidden]" value="<?php echo set_value('harga_tot6_hidden', isset($harga_tot6_hidden) ? $harga_tot6_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		</tr>
		<?php

		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='12'><b>TOTAL TRUCKING</b></td> ";
			?>
			<td align="right">
			<?php
				$total_trucking= number_format($SUM3,2);
				$total_trucking_hidden= round($SUM3,2);
				?>
			<input type="text" class="form-control result6 text-right" id="total_trucking<?php echo $numb6 ?>" name="total_trucking" value="<?php echo set_value('total_trucking', isset($total_trucking) ? $total_trucking : ''); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control result6_hidden" id="total_trucking_hidden<?php echo $numb6 ?>" name="total_trucking_hidden" value="<?php echo set_value('total_trucking_hidden', isset($total_trucking_hidden) ? $total_trucking_hidden : ''); ?>" placeholder="Automatic" readonly >
            </td>
		</tr>

	</tbody>
	<?php
	}
	?>

	<tfoot>
	    <tr class='HeaderHr'>
			<td align='right' colspan='11'>TOTAL</td>
			<td align='center' style='text-align:center;'></td>
			<td align='right' style='text-align:center;'>
			<?php
			$grand_total = number_format($SUM + $SUM2 + $SUM3 /*+ $SUM4*/ + $SUM1 + $SUM_MAT, 2);

			$grand_total_hidden = round($SUM + $SUM2 + $SUM3 /*+ $SUM4*/ + $SUM1 + $SUM_MAT, 2);


			?>
			<input type="text" class="form-control grand_total text-right" id="grand_total" name="grand_total" value="<?php echo set_value('grand_total', isset($grand_total) ? $grand_total : '0'); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control grand_total_hidden" id="grand_total_hidden" name="grand_total_hidden" value="<?php echo set_value('grand_total_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		</tr>
		<?php
//			if(!empty($non_frp)){
				echo "<tr class='HeaderHr'>";
					echo "<td align='right' colspan='11'>DOWN PAYMENT</td>";
					echo "<td align='center' style='text-align:center;'></td>";
					?>
					<?php
					$grand_total_nonfrp = number_format($data_header->total_um, 2);
					$grand_total_hidden_nonfrp = round($data_header->total_um, 2);
					?>
					<td align='right' style='text-align:right;'>
					<input type="text" class="form-control grand_total_nonfrp text-right" id="grand_total_nonfrp" name="grand_total_nonfrp" value="<?php echo set_value('grand_total_nonfrp', isset($grand_total_nonfrp) ? $grand_total_nonfrp : '0'); ?>" placeholder="Automatic" readonly >
					<input type="hidden" class="form-control grand_total_hidden_nonfrp" id="grand_total_hidden_nonfrp" name="grand_total_hidden_nonfrp" value="<?php echo set_value('grand_total_hidden_nonfrp', isset($grand_total_hidden_nonfrp) ? $grand_total_hidden_nonfrp : ''); ?>" placeholder="Automatic" readonly >
					</td>
					<?php
				echo "</tr>";
//			}
		?>
		    <tr class='HeaderHr'>
			<td align='right' colspan='11'>DISKON </td>
			<td align='center' style='text-align:center;'></td>
			<td align='right' style='text-align:center;'>
			<?php
			$tot_diskon = number_format($data_header->total_diskon, 2);

			$tot_diskon_hidden = round($data_header->total_diskon, 2);


			?>
			<input type="text" class="form-control diskon text-right" id="diskon" name="diskon" value="<?php echo set_value('diskon', isset($tot_diskon) ? $tot_diskon : '0'); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control diskon_hidden" id="diskon_hidden" name="diskon_hidden" value="<?php echo set_value('diskon_hidden', isset($tot_diskon_hidden) ? $tot_diskon_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		    </tr>
			<tr class='HeaderHr'>
			<td align='right' colspan='11'>POTONGAN RETENSI </td>
			<td align='center' style='text-align:center;'></td>
			<td align='right' style='text-align:center;'>
			<?php
			$tot_retensi = number_format($data_header->total_retensi, 2);

			$tot_retensi_hidden = round($data_header->total_retensi, 2);


			?>
			<input type="text" class="form-control potongan_retensi text-right" id="potongan_retensi" name="potongan_retensi" value="<?php echo set_value('potongan_retensi', isset($tot_retensi) ? $tot_retensi : '0'); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control potongan_retensi_hidden" id="potongan_retensi_hidden" name="potongan_retensi_hidden" value="<?php echo set_value('potongan_retensi_hidden', isset($tot_retensi_hidden) ? $tot_retensi_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		    </tr>
			<tr class='HeaderHr'>
			<td align='right' colspan='11'>PPN </td>
			<td align='center' style='text-align:center;'></td>
			<td align='right' style='text-align:center;'>
			<?php
			$tot_ppn = number_format($data_header->total_ppn, 2);

			$tot_ppn_hidden = round($data_header->total_ppn, 2);


			?>
			<input type="text" class="form-control ppn text-right" id="ppn" name="ppn" value="<?php echo set_value('ppn', isset($tot_ppn) ? $tot_ppn : '0'); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control ppn_hidden" id="ppn_hidden" name="ppn_hidden" value="<?php echo set_value('ppn_hidden', isset($tot_ppn_hidden) ? $tot_ppn_hidden : '0'); ?>" placeholder="Automatic" readonly >

			</td>
		    </tr>

			<tr class='HeaderHr'>
			<td align='right' colspan='11'>POTONGAN RETENSI PPN </td>
			<td align='center' style='text-align:center;'></td>
			<td align='right' style='text-align:center;'>
			<?php
			$total_retensi2 = number_format($data_header->total_retensi2, 2);
			?>
			<input type="text" class="form-control ppn text-right" id="total_retensi2" name="total_retensi2" value="<?php echo set_value('total_retensi2', isset($total_retensi2) ? $total_retensi2 : '0'); ?>" placeholder="Automatic" readonly >

			</td>
		    </tr>

			 <tr class='HeaderHr'>
			<td align='right' colspan='11'>TOTAL INVOICE</td>
			<td align='center' style='text-align:center;'></td>
			<td align='right' style='text-align:center;'>
			<?php
			$grand_total = number_format($data_header->total_invoice, 2);

			$grand_total_hidden = round($data_header->total_invoice, 2);


			?>
			<input type="text" class="form-control total_invoice text-right" id="total_invoice" name="total_invoice" value="<?php echo set_value('total_invoice', isset($grand_total) ? $grand_total : '0'); ?>" placeholder="Automatic" readonly >
            <input type="hidden" class="form-control total_invoice_hidden" id="total_invoice_hidden" name="total_invoice_hidden" value="<?php echo set_value('total_invoice_hidden', isset($grand_total_hidden) ? $grand_total_hidden : ''); ?>" placeholder="Automatic" readonly >

			</td>
		</tr>

	</tfoot>
</table>

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

		$('#proses_inv').click(function(e){
			  e.preventDefault();
        if ($('#tgl_inv').val() == "") {
          swal({
            title	: "TANGGAL INVOICE TIDAK BOLEH KOSONG!",
            text	: "ISI TANGGAL INVOICE!",
            type	: "danger",
            timer	: 10000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }else {

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

              //var formData 	= $('#form_proses').serialize();
              var formData 	=new FormData($('#form_proses')[0]);
              //console.log(formData);return false;
              var baseurl=base_url + active_controller +'/save_invoice';
              //console.log(baseurl);return false;
              $.ajax({
                url			: baseurl,
                type		: "POST",
                data		: formData,
                cache		: false,
                dataType	: 'json',
                processData	: false,
                contentType	: false,
                success		: function(data){

                  var kode_bast	= data.kode;
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
                    window.location.href = base_url + active_controller+'/create_new';
                  }else{

                    if(data.status == 2){
                      swal({
                        title	: "Save Failed!",
                        text	: data.pesan,
                        type	: "danger",
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
                    type				: "warning",
                    timer				: 7000,
                    showCancelButton	: false,
                    showConfirmButton	: false,
                    allowOutsideClick	: false
                  });
                }
              });
            } else {
              swal("Batal Proses", "Data bisa diproses nanti", "error");
              return false;
            }
          });
        }
		});

	});

	function kembali_inv(){
        window.location.href = base_url + active_controller +'/create_new';
    }

	function delRow(row){
		$('#tr_'+row).remove();
	}
	function delRow2(row){
		$('#tr1_'+row).remove();
	}
	function delRow3(row){
		$('#tr2_'+row).remove();
	}
	function delRow4(row){
		$('#tr3_'+row).remove();
	}
	function delRow5(row){
		$('#tr4_'+row).remove();
	}
	function delRow6(row){
		$('#tr5_'+row).remove();
	}
	function delRow7(row){
		$('#tr6_'+row).remove();
	}


	$(document).on('keyup', '.qty_product', function(){
		var dataNomor = $(this).data('nomor');
		var hargaSat  = $('#harga_sat_hidden_'+dataNomor).val();
		var dataIni	  = $(this).val();
		var total     = parseFloat(hargaSat*dataIni).toFixed(2);

		$('#harga_tot_'+dataNomor).val(num2(total));
		$('#harga_tot_hidden'+dataNomor).val(total);

		fnAlltotal()

	});

	function fnAlltotal(){
	  var total=0
	  var total2=0
		$(".amount1").each(function(){
			 total += parseFloat($(this).val()||0);
			 total2 += parseFloat($(this).val()||0);
		});

		$(".result1").val(num(total));
		$(".result1_hidden").val(num3(total2));

		grandtotal()
		totalInvoice()

	}



	$(document).on('keyup', '.qty_bq', function(){
		var dataNomor = $(this).data('nomor');
		var hargaSat  = $('#harga_sat2_hidden'+dataNomor).val();
		var dataIni	  = $(this).val();
		var total     = parseFloat(hargaSat*dataIni).toFixed(2);

		$('#harga_tot2'+dataNomor).val(num2(total));
		$('#harga_tot2_hidden'+dataNomor).val(total);

		fnAlltotal2()

	});

	function fnAlltotal2(){
	  var total=0
	  var total2=0
		$(".amount2").each(function(){
			 total += parseFloat($(this).val()||0);
			 total2 += parseFloat($(this).val()||0);
		});

		$(".result2").val(num(total));
		$(".result2_hidden").val(num3(total2));

		grandtotal()
		totalInvoice()

	}



	$(document).on('keyup', '.qty_material', function(){
		var dataNomor3 = $(this).data('nomor');
		var hargaSat3  = $('#harga_sat3_hidden'+dataNomor3).val();
		var dataIni3	  = $(this).val();
		var total3     = parseFloat(hargaSat3*dataIni3).toFixed(2);



		$('#harga_tot3'+dataNomor3).val(num2(total3));
		$('#harga_tot3_hidden'+dataNomor3).val(total3);

		fnAlltotal3()

	});

	function fnAlltotal3(){
	  var total31=0
	  var total32=0
		$(".amount3").each(function(){
			 total31 += parseFloat($(this).val()||0);
			 total32 += parseFloat($(this).val()||0);
		});

		$(".result3").val(num(total31));
		$(".result3_hidden").val(num3(total32));

		grandtotal()
		totalInvoice()

	}

	$(document).on('keyup', '.qty_enginering', function(){
		var dataNomor4 = $(this).data('nomor');
		var hargaSat4  = $('#harga_sat4_hidden'+dataNomor4).val();
		var dataIni4	  = $(this).val();
		var total4     = parseFloat(hargaSat4*dataIni4).toFixed(2);

		$('#harga_tot4'+dataNomor4).val(num2(total4));
		$('#harga_tot4_hidden'+dataNomor4).val(total4);

		fnAlltotal4()

	});

	function fnAlltotal4(){
	  var total41=0
	  var total42=0
		$(".amount4").each(function(){
			 total41 += parseFloat($(this).val()||0);
			 total42 += parseFloat($(this).val()||0);
		});

		$(".result4").val(num(total41));
		$(".result4_hidden").val(num3(total42));

		grandtotal()
		totalInvoice()

	}

	$(document).on('blur', '.harga_tot5', function(){
		var dataNomor4 = $(this).data('nomor');
		var hargaSat4  = 1;
		var dataIni4	  = $(this).val();
		var total4     = parseFloat(hargaSat4*dataIni4).toFixed(2);

		$('#harga_tot5'+dataNomor4).val(num2(total4));
		$('#harga_tot5_hidden'+dataNomor4).val(total4);

		fnAlltotal5()

	});

	function fnAlltotal5(){
	  var total51=0
	  var total52=0
		$(".amount5").each(function(){
			 total51 += parseFloat($(this).val()||0);
			 total52 += parseFloat($(this).val()||0);
		});

		$(".result5").val(num(total51));
		$(".result5_hidden").val(num3(total52));

			grandtotal()
			totalInvoice()

	}

	$(document).on('keyup', '.qty_trucking', function(){
		var dataNomor6 = $(this).data('nomor');
		var hargaSat6  = $('#harga_sat6_hidden'+dataNomor6).val();
		var dataIni6	  = $(this).val();
		var total6     = parseFloat(hargaSat6*dataIni6).toFixed(2);

		$('#harga_tot6'+dataNomor6).val(num2(total6));
		$('#harga_tot6_hidden'+dataNomor6).val(total6);

		fnAlltotal6()

	});

	function fnAlltotal6(){
	  var total61=0
	  var total62=0
		$(".amount6").each(function(){
			 total61 += parseFloat($(this).val()||0);
			 total62 += parseFloat($(this).val()||0);
		});

		$(".result6").val(num(total61));
		$(".result6_hidden").val(num3(total62));

		grandtotal()

		totalInvoice()



	}

	$(document).on('keyup', '.qty_lokal', function(){
		var dataNomor7 = $(this).data('nomor');
		var hargaSat7  = $('#harga_sat7_hidden'+dataNomor7).val();
		var dataIni7	  = $(this).val();
		var total7     = parseFloat(hargaSat7*dataIni7).toFixed(2);

		$('#harga_tot7'+dataNomor7).val(num2(total7));
		$('#harga_tot7_hidden'+dataNomor7).val(total7);

		fnAlltotal7()

	});

	function fnAlltotal7(){
	  var total71=0
	  var total72=0



		$(".amount7").each(function(){
			 total71 += parseFloat($(this).val()||0);
			 total72 += parseFloat($(this).val()||0);

		});

		$(".result7").val(num(total71));
		$(".result7_hidden").val(num3(total72));

		grandtotal()

		totalInvoice()

	}


	$(document).on('blur', '.diskon', function(){

		var dataDiskon	  = $(this).val();
		var totalDiskon     = parseFloat(dataDiskon).toFixed(2);

		$('.diskon').val(num2(totalDiskon));
		$('.diskon_hidden').val(totalDiskon);

		totalInvoice()

	});


	$(document).on('blur', '.potongan_retensi', function(){

		var dataRetensi	  = $(this).val();
		var totalRetensi     = parseFloat(dataRetensi).toFixed(2);

		$('.potongan_retensi').val(num2(totalRetensi));
		$('.potongan_retensi_hidden').val(totalRetensi);

		totalInvoice()

	});

	$(document).on('blur', '.ppn', function(){

		var dataPpn	  = $(this).val();
		var totalPpn     = parseFloat(dataPpn).toFixed(2);

		$('.ppn').val(num2(totalPpn));
		$('.ppn_hidden').val(totalPpn);

		totalInvoice()

	});


	function grandtotal() {

			var grandtotal =0
			var result2_hidden1 =0
	        var result7_hidden1 =0
			var result1_hidden  = $('.result1_hidden').val();
		    var result2_hidden  = $('.result2_hidden').val();
		    var result3_hidden  = $('.result3_hidden').val();
			  var result4_hidden  = $('.result4_hidden').val();
			    var result5_hidden  = $('.result5_hidden').val();
				  var result6_hidden  = $('.result6_hidden').val();
				   var result7_hidden  = $('.result7_hidden').val();
				   var diskon_hidden  = $('.diskon_hidden').val();


		if(result2_hidden==null){
		result2_hidden1 = 0;
		}
		else{
		result2_hidden1 = result2_hidden;
		}

		if(result7_hidden==null){
		result7_hidden1 = 0;
		}
		else{
		result7_hidden1 = result7_hidden;
		}

		grandtotal =parseFloat(result1_hidden)+parseFloat(result2_hidden1)+parseFloat(result3_hidden)+parseFloat(result4_hidden)+parseFloat(result5_hidden)+parseFloat(result6_hidden)+parseFloat(result7_hidden1);


		// console.log(result1_hidden)
		// console.log(result2_hidden)
		// console.log(result3_hidden)
		// console.log(result4_hidden)
		// console.log(result5_hidden)
		// console.log(result6_hidden)
		// console.log(result7_hidden1)
		// console.log(grandtotal)


		$(".grand_total").val(num(grandtotal));
		$(".grand_total_hidden").val(num3(grandtotal));

		}


	    function totalInvoice() {
	/*
			var grandtotal =0
			var result2_hidden1 =0
	        var result7_hidden1 =0
			var result1_hidden  = $('.result1_hidden').val();
		    var result2_hidden  = $('.result2_hidden').val();
		    var result3_hidden  = $('.result3_hidden').val();
			  var result4_hidden  = $('.result4_hidden').val();
			    var result5_hidden  = $('.result5_hidden').val();
				  var result6_hidden  = $('.result6_hidden').val();
				   var result7_hidden  = $('.result7_hidden').val();
				   var diskon_hidden  = $('.diskon_hidden').val();
				   var potongan_retensi_hidden  = $('.potongan_retensi_hidden').val();
				   var ppn_hidden  = $('.ppn_hidden').val();


		if(result2_hidden==null){
		result2_hidden1 = 0;
		}
		else{
		result2_hidden1 = result2_hidden;
		}

		if(result7_hidden==null){
		result7_hidden1 = 0;
		}
		else{
		result7_hidden1 = result7_hidden;
		}

		grandtotal =parseFloat(result1_hidden)+parseFloat(result2_hidden1)+parseFloat(result3_hidden)+parseFloat(result4_hidden)+parseFloat(result5_hidden)+parseFloat(result6_hidden)+parseFloat(ppn_hidden)+parseFloat(result7_hidden1)-
		parseFloat(diskon_hidden)-parseFloat(potongan_retensi_hidden);



		console.log(potongan_retensi_hidden)
		console.log(grandtotal)


		$(".total_invoice").val(num(grandtotal));
		$(".total_invoice_hidden").val(num3(grandtotal));
		*/
			var grand_total_hidden=$('#grand_total_hidden').val();
			var diskon_hidden=$('#diskon_hidden').val();
			var potongan_retensi_hidden=$('#potongan_retensi_hidden').val();
			var ppn_hidden=$('#ppn_hidden').val();
			total_invoice=(parseFloat(grand_total_hidden)-parseFloat(diskon_hidden)-parseFloat(potongan_retensi_hidden)+parseFloat(ppn_hidden));
			$(".total_invoice").val(num(total_invoice));
			$(".total_invoice_hidden").val(num3(total_invoice));
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




</script>