<?php
if($restChkSO < 1){
	?>
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			BQ ini tidak dapat dilakukan approve, please update data.<br>
		</p>
	</div>
	<?php
}
else{
?>

<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info Approve Checklist!</strong><br> 
			Checklist semua atau sebagian, kemudian pilih actionnya. Gunakan checklist header untuk checklist semua.<br>
			<span style='color:green;'><b>MOHON CEK TERLEBIH DAHULU COST SATUAN,<span> <span style='color:red;'>UNTUK MENIMINALISIR KESALAHAN</b></span>
			<br><span style='color:purple;'><b><u>PRODUCT KOSONG WAJIB DIAJUKAN !!!</u></b></span>
			<br>
			<br>
			<span style='color:red;'><b><u><?=COUNT($resultHistory);?>x Perubahan Material</u></b></span> <span class='text-bold' style='color:purple; cursor:pointer;' id='LookChange'>Lihat Perubahan</span>
		</p>
	</div>
	<div id='ChangeMaterial'>
		<table class='table' width='100%' style='font-size: 12px !important;'>
			<tr>
				<th>#</th>
				<th>Layer</th>
				<th>Type</th>
				<th>Material Before</th>
				<th>Material After</th>
				<th>Product</th>
				<th>By</th>
				<th>Date</th>
			</tr>
			<?php
			foreach ($resultHistory as $key => $value) { $key++;
				$NM_MATERIAL = explode(",",$value['id_material_before']);
				$NM_MATERIAL2 = explode(",",$value['id_material_after']);
				$PRODUCT = explode("','",$value['id_milik']);
				echo "<tr>";
					echo "<td>".$key."</td>";
					echo "<td>".strtoupper($value['layer'])."</td>";
					echo "<td>".$value['typeMaterial']."</td>";
					echo "<td>";
						foreach ($NM_MATERIAL as $key2 => $value2) {
							$nm_mat = (!empty($GET_MATERIAL[$value2]['nm_material']))?$GET_MATERIAL[$value2]['nm_material']:'';
							echo $nm_mat."<br>";
						}
					echo "</td>";
					echo "<td>";
						foreach ($NM_MATERIAL2 as $key2 => $value2) {
							$nm_mat = (!empty($GET_MATERIAL[$value2]['nm_material']))?$GET_MATERIAL[$value2]['nm_material']:'';
							echo $nm_mat."<br>";
						}
					echo "</td>";
					echo "<td>";
						foreach ($PRODUCT as $key2 => $value2) {
							$nm_mat = (!empty($GET_PRODUCT[$value2]['product']))?$GET_PRODUCT[$value2]['product']:'';
							echo strtoupper($nm_mat).', '.spec_bq2($value2)."<br>";
						}
					echo "</td>";
					echo "<td>".$value['change_by']."</td>";
					echo "<td>".date('d-M-Y H:i',strtotime($value['change_date']))."</td>";
				echo "</tr>";
			}
			?>
		</table>
	</div>
	<?php
	if($numCheckY > 0){ ?>
	<div class="form-group row">
		<div class='col-sm-3 '>
		   <label class='label-control'>Approve Action</label><br>
		   <select name='status' id='status' class='form-control input-md chosen_select' style='width:100%;'>
				<option value='0'>Select Action</option>
				<option value='Y'>APPROVE</option> 
				<option value='M'>REVISI TO BQ FINAL DRAWING</option>
				<option value='N'>REVISI ESTIMASI PROJECT FINAL DRAWING</option>
			</select><br>
			<?php
			echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
			?>
		</div>
		<div class='col-sm-4 '>
			<div id='HideReject'>
				<label class='label-control'>Reject Reason</label>          
				<?php
					echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Revision reason'));
				?>		
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class='col-sm-12 '>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 0px 5px 0px 0px;','value'=>'Process','content'=>'Process','id'=>'approvedFD_All'));
	?>
		</div>
	</div>
	<?php } ?>
	<div class="form-group row">
		<div class='col-sm-12 '>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"  style='font-size: 12px !important;'>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='13'><b>PRODUCT (FINAL DRAWING)</b></td>
						<td class="text-left headX HeaderHr" colspan='4'><b>APPROVE ACTION</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-left" colspan='2'>ITEM PRODUCT</th>
						<th class="text-right" width='4%'>DIM1</th>
						<th class="text-right" width='4%'>DIM2</th>
						<th class="text-center" width='4%'>LIN</th>
						<th class="text-center" width='4%'>PRE</th>
						<th class="text-left">SPECIFICATION</th>
						<th class="text-right" width='4%'>UNIT PRICE</th>
						<th class="text-center" width='3%'>QTY</th>
						<th class="text-center" width='3%'>UNIT</th>
						<th class="text-right" width='7%'>TOTAL PRICE</th>
						<th class="text-center" width='4%'>QTY<br>(DIAJUKAN)</th>
						<th class="text-right" width='7%'>TOTAL PRICE<br>(DIAJUKAN)</th>
						<th class="text-center" width='3%'>
							<?php
							if($numCheckY > 0){ ?>
							<font size='2'><B><center>
							<input type='checkbox' name='chk_all' id='chk_all'>
							</center></B></font>
							<?php } ?>
						</th>
						<th class="text-left" width='8%'>ACTION</th>
						<th class="text-left" width='10%'>REASON</th>
						<th class="text-center" width='4%'>#</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$SUM = 0;
					$SUM2 = 0;
					$no = 0;
					foreach($getDetail_FD AS $val => $valx){
						$no++;
						$check_FD = check_fd($valx['id']);
						if($check_FD != 'N'){
							$DEAL_PRICE	= $valx['deal_price'];
							$DEAL_UNIT	= 0;
							if($DEAL_PRICE > 0 AND $valx['qty_deal'] > 0){
								$DEAL_UNIT 	= $DEAL_PRICE / $valx['qty_deal'];
							}

							$id_bq_header	= get_name('so_detail_header','id_bq_header','id',$valx['id']);
							$qty_sisa 		= check_status_Y($id_bq_header);
							$price_diajukan = $DEAL_UNIT * $qty_sisa;
							
							$SUM 	+= $DEAL_PRICE;
							$SUM2 	+= $price_diajukan;
							
							if($valx['product'] == 'pipe' OR $valx['product'] == 'pipe slongsong'){
								$unitT = "Btg";
							}
							else{
								$unitT = "Pcs";
							}
							$dist = '';
							if($check_FD == 'Y' AND $qty_sisa > 0){
								$dist = "<center><input type='checkbox' name='check[".$no."]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."' ></center>";
							}
					
							echo "<tr>";
								echo "<td style='vertical-align: middle; 'colspan='2'>".strtoupper($valx['product'])."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($valx['diameter_1'])."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($valx['diameter_2'])."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".substr($valx['series'],6,5)."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".substr($valx['series'],3,2)."</td>";
								echo "<td style='vertical-align: middle; text-align:left;'>".spec_bq2($valx['id'])."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($DEAL_UNIT,2)."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$valx['qty_fd']."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$unitT."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($DEAL_PRICE,2)."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$qty_sisa."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($price_diajukan,2)."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".$dist."</td>";
								echo "<td style='vertical-align: middle; align='center'>";
									if($check_FD == 'Y' AND $qty_sisa > 0){
										echo "<select name='sts_".$no."' id='sts_".$no."' data-nomor='".$no."' class='form-control input-sm chosen_select stsSelect'>
											<option value='0'>Select</option>
											<option value='Y'>Approve</option>
											<option value='M'>Rev. BQ</option>
											<option value='N'>Rev. Est</option>
										</select>";
									}
									else{
										echo "<span style='color:green;'><b>Approved</b></span>";
									}
								echo "</td>";
								echo "<td style='vertical-align: middle; align='left'>";
									if($check_FD == 'Y' AND $qty_sisa > 0){
										echo form_input(array('id'=>'reason_'.$no,'name'=>'reason_'.$no,'class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','placeholder'=>'Reason'));
									}
								echo "</td>";
								echo "<td style='vertical-align: middle; align='center'>";
									if($check_FD == 'Y' AND $qty_sisa > 0){
										echo "<button type='button' class='btn btn-sm btn-success appSatuan' title='Approve Component' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id='".$valx['id']."'><i class='fa fa-check'></i></button>";
									}
								echo "</td>";
							echo "</tr>";
						}
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL COST  OF PRODUCT (FINAL DRAWING)</b></td>
						<td align='right'><b><?= number_format($SUM,2);?></b></td>
						<td></td>
						<td align='right'><b><?= number_format($SUM2,2);?></b></td>
						<td colspan='4'></td>
					</tr>
				</tbody>
				<?php
				$SUM_NONFRP = 0;
				if(!empty($non_frp2)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='13'><b>AKSESORIS (FINAL DRAWING)</b></td>";
							echo "<td class='text-left headX HeaderHr' colspan='4'><b>APPROVE ACTION</b></td>";
							echo "</tr>";
						echo "<tr class='bg-blue'>";
							echo "<th class='text-left' colspan='7'>MATERIAL NAME</th>";
							echo "<th colspan='3' class='text-right'>DEAL SO<br>UNIT PRICE</th>";
							echo "<th class='text-center'>QTY</th>";
							echo "<th class='text-center'>UNIT</th>";
							echo "<th class='text-right'>TOTAL PRICE</th>";
							echo "<th class='text-center'><font size='2'><B><center>
							<input type='checkbox' name='chk_all_aksesoris' id='chk_all_aksesoris'>
							</center></B></font></th>";
							echo "<th class='text-left' width='10%'>ACTION</th>";
							echo "<th class='text-left' width='14%'>REASON</th>";
							echo "<th class='text-center' width='4%'>#</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($non_frp2 AS $val => $valx){ $no++;
						$PRICE_DEAL = $valx['deal_price'];
						$QTY_DEAL = $valx['qty_deal'];
						$PRICE_DEAL_UNIT = 0;
						if($PRICE_DEAL > 0 AND $QTY_DEAL > 0){
							$PRICE_DEAL_UNIT = $PRICE_DEAL / $QTY_DEAL;
						}
						$SUM_NONFRP += $PRICE_DEAL;
						$CHECK_FD_ACC = check_fd_acc($valx['id2']);
						$dist = '';
						if($CHECK_FD_ACC == 'Y'){
							$dist = "<center><input type='checkbox' name='check2[".$no."]' class='chk_personal_acc' data-nomor='".$no."' value='".$valx['id2']."' ></center>";
						}
						$Tanda = "";
						if($QTY_DEAL === null){
							$Tanda = "<br><span class='text-danger'><b>Item tidak ada di Deal Sales Order !</b></span>";
							$QTY_DEAL = $valx['qty_fd'];
						}
						echo "<tr>";
							echo "<td colspan='7'>".strtoupper(get_name_acc($valx['id_material'])).$Tanda."</td>";
							echo "<td colspan='3' align='right'>".number_format($PRICE_DEAL_UNIT,2)."</td>";
							echo "<td align='center'>".number_format($QTY_DEAL)."</td>";
							echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
							echo "<td align='right'>".number_format($PRICE_DEAL,2)."</td>";
							echo "<td style='vertical-align: middle; text-align:right;'>".$dist."</td>";
							echo "<td style='vertical-align: middle; align='center'>";
								if($CHECK_FD_ACC == 'Y'){
									echo "<select name='sts_".$no."' id='sts_".$no."' data-nomor='".$no."' class='form-control input-sm chosen_select stsSelect'>
										<option value='0'>Select</option>
										<option value='Y'>Approve</option>
										<option value='N'>Reject</option>
									</select>";
								}
								else{
									echo "<span style='color:green;'><b>Approved</b></span>";
								}
							echo "</td>";
							echo "<td style='vertical-align: middle; align='left'>";
								if($CHECK_FD_ACC == 'Y'){
									echo form_input(array('id'=>'reason_'.$no,'name'=>'reason_'.$no,'class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','placeholder'=>'Reason'));
								}
							echo "</td>";
							echo "<td style='vertical-align: middle; align='center'>";
								if($CHECK_FD_ACC == 'Y'){
									echo "<button type='button' class='btn btn-sm btn-success app_aksesoris' title='Approve Component' data-nomor='".$no."' data-id_bq='".$valx['id_bq2']."' data-id='".$valx['id2']."'><i class='fa fa-check'></i></button>";
								}
							echo "</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='11'><b>TOTAL AKSESORIS (FINAL DRAWING)</b></td> ";
						echo "<td align='center'><b></b></td> ";
						echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
						echo "<td colspan='4'></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				$SUM_MAT = 0;
				if(!empty($material2)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='13'><b>MATERIAL (FINAL DRAWING)</b></td>";
							echo "<td class='text-left headX HeaderHr' colspan='4'><b>APPROVE ACTION</b></td>";
						echo "</tr>";
						echo "<tr class='bg-blue'>";
							echo "<th class='text-left' colspan='7'>MATERIAL NAME</th>";
							echo "<th class='text-right' colspan='3'>DEAL SO<br>UNIT PRICE</th>";
							echo "<th class='text-center'>QTY</th>";
							echo "<th class='text-center'>UNIT</th>";
							echo "<th class='text-right'>TOTAL PRICE</th>";
							echo "<th class='text-center'><font size='2'><B><center>
							<input type='checkbox' name='chk_all_material' id='chk_all_material'>
							</center></B></font></th>";
							echo "<th class='text-left' width='10%'>ACTION</th>";
							echo "<th class='text-left' width='14%'>REASON</th>";
							echo "<th class='text-center' width='4%'>#</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($material2 AS $val => $valx){ $no++;
						$PRICE_DEAL = $valx['deal_price'];
						$QTY_DEAL = $valx['qty_deal'];
						$PRICE_DEAL_UNIT = 0;
						if($PRICE_DEAL > 0 AND $QTY_DEAL > 0){
						$PRICE_DEAL_UNIT = $PRICE_DEAL / $QTY_DEAL;
						}
						$SUM_MAT += $PRICE_DEAL;
						$CHECK_FD_ACC = check_fd_acc($valx['id2']);
						$dist = '';
						if($CHECK_FD_ACC == 'Y'){
							$dist = "<center><input type='checkbox' name='check2[".$no."]' class='chk_personal_mat' data-nomor='".$no."' value='".$valx['id2']."' ></center>";
						}

						$Tanda = "";
						if($QTY_DEAL === null){
							$Tanda = "<br><span class='text-danger'><b>Item tidak ada di Deal Sales Order !</b></span>";
							$QTY_DEAL = $valx['qty_fd'];
						}
						echo "<tr>";
							echo "<td colspan='7'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material'])).$Tanda."</td>";
							echo "<td align='right' colspan='3'>".number_format($PRICE_DEAL_UNIT,2)."</td>";
							echo "<td align='right'>".number_format($QTY_DEAL,2)."</td>";
							echo "<td align='center'>KG</td>";
							echo "<td align='right'>".number_format($PRICE_DEAL,2)."</td>";
							echo "<td style='vertical-align: middle; text-align:right;'>".$dist."</td>";
							echo "<td style='vertical-align: middle; align='center'>";
								if($CHECK_FD_ACC == 'Y'){
									echo "<select name='sts_".$no."' id='sts_".$no."' data-nomor='".$no."' class='form-control input-sm chosen_select stsSelect'>
										<option value='0'>Select</option>
										<option value='Y'>Approve</option>
										<option value='N'>Reject</option>
									</select>";
								}
								else{
									echo "<span style='color:green;'><b>Approved</b></span>";
								}
							echo "</td>";
							echo "<td style='vertical-align: middle; align='left'>";
								if($CHECK_FD_ACC == 'Y'){
									echo form_input(array('id'=>'reason_'.$no,'name'=>'reason_'.$no,'class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','placeholder'=>'Reason'));
								}
							echo "</td>";
							echo "<td style='vertical-align: middle; align='center'>";
								if($CHECK_FD_ACC == 'Y'){
									echo "<button type='button' class='btn btn-sm btn-success app_aksesoris' title='Approve Component' data-nomor='".$no."' data-id_bq='".$valx['id_bq2']."' data-id='".$valx['id2']."'><i class='fa fa-check'></i></button>";
								}
							echo "</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='12'><b>TOTAL MATERIALL (FINAL DRAWING)</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
						echo "<td colspan='4'></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				?>
			</table>
		</div>
	</div>
	<br>
	<div class="form-group row">
		<div class='col-sm-8 '>	
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tr>
					<th class="text-center" colspan='2' width='22%'></th>
					<th class="text-center" width='6%'></th>
					<th class="text-center" width='6%'></th>
					<th class="text-center" width='6%'></th>
					<th class="text-center" width='8%'></th>
					<th class="text-center" width='17%'></th>
					<th class="text-center" width='10%'></th>
					<th class="text-center" width='7%'></th>
					<th class="text-center" width='8%'></th>
					<th class="text-center" width='11%'></th>
				</tr>
				<?php
				$SUM2x = 0;
				if(!empty($getDetail)){ ?>
					<tbody>
						<tr class='bg-blue'>
							<td class="text-left" colspan='11'><b>PRODUCT SALES ORDER</b></td>
						</tr>
						<tr class='bg-blue'>
							<th class="text-center" colspan='2' width='22%'>Item Product</th>
							<th class="text-center" width='6%'>Dim 1</th>
							<th class="text-center" width='6%'>Dim 2</th>
							<th class="text-center" width='6%'>Liner</th>
							<th class="text-center" width='8%'>Pressure</th>
							<th class="text-center" width='17%'>Specification</th>
							<th class="text-center" width='10%'>Unit Price</th>
							<th class="text-center" width='7%'>Qty</th>
							<th class="text-center" width='8%'>Unit</th>
							<th class="text-center" width='11%'>Total Price (USD)</th>
						</tr>
					</tbody>
					<tbody>
						<?php
						$SUM2x = 0;
						$no = 0;
						foreach($getDetail AS $val => $valx){
							$no++;
							$PRICE_DEAL = $valx['total_deal_usd'];
							$PRICE_UNIT = 0;
							if($valx['qty'] > 0 AND $PRICE_DEAL > 0){
								$PRICE_UNIT = $PRICE_DEAL / $valx['qty'];
							}
							$SUM2x += $PRICE_DEAL;
							
							echo "<tr>";
								echo "<td colspan='2'>".strtoupper($valx['product'])."</td>";
								echo "<td align='right'>".number_format($valx['dim1'])."</td>";
								echo "<td align='right'>".number_format($valx['dim2'])."</td>";
								echo "<td align='center'>".$valx['liner']."</td>";
								echo "<td align='center'>PN".$valx['pressure']."</td>";
								echo "<td align='left'>".spec_bq2($valx['id_milik2'])."</td>";
								echo "<td align='right'>".number_format($PRICE_UNIT, 2)."</td>";
								echo "<td align='center'>".$valx['qty']."</td>";
								echo "<td align='center'>".strtoupper($valx['unit'])."</td>";
								echo "<td align='right'>".number_format($PRICE_DEAL,2)."</td>";
							echo "</tr>";
						}
						?>
						<tr class='FootColor'>
							<td colspan='10'><b>TOTAL COST OF PRODUCT</b></td>
							<td align='right'><b><?= number_format($SUM2x,2);?></b></td>
						</tr>
					</tbody>
				<?php
				}
				$SUM_NONFRP2x = 0;
				if(!empty($non_frp)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='11'><b>BQ NON FRP</b></td>";
						echo "</tr>";
						echo "<tr class='bg-blue'>";
							echo "<th class='text-center' colspan='7'>Material Name</th>";
							echo "<th class='text-center'>Qty</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($non_frp AS $val => $valx){
						$PRICE_DEAL = $valx['price_deal'];
						$PRICE_UNIT = 0;
						if($valx['qty'] > 0){
							$PRICE_UNIT = $valx['price_deal'] / $valx['qty'];
						}
						$SUM_NONFRP2x += $PRICE_DEAL;
						
						echo "<tr>";
							echo "<td colspan='7'>".strtoupper(get_name_acc($valx['id_material']))."</td>";
							echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
							echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
							echo "<td align='right'>".number_format($PRICE_UNIT,2)."</td>";
							echo "<td align='right'>".number_format($PRICE_DEAL,2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='10'><b>TOTAL BQ NON FRP</b></td> ";
						// echo "<td align='center'><b>IDR</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_NONFRP2x,2)."</b></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				$SUM_MAT2x = 0;
				if(!empty($material)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='11'><b>MATERIAL</b></td>";
						echo "</tr>";
						echo "<tr class='bg-blue'>";
							echo "<th class='text-center' colspan='7'>Material Name</th>";
							echo "<th class='text-center'>Weight</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($material AS $val => $valx){
						$PRICE_DEAL = $valx['price_deal'];
						$SUM_MAT2x += $PRICE_DEAL;
						echo "<tr>";
							echo "<td colspan='7'>".strtoupper($valx['nm_material'])."</td>";
							echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
							echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
							echo "<td align='right'>".number_format($PRICE_DEAL/$valx['qty'],2)."</td>";
							echo "<td align='right'>".number_format($PRICE_DEAL,2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='10'><b>TOTAL MATERIAL</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_MAT2x, 2)."</b></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				?>
				<?php
				$SUM1=0;
				if(!empty($getEngCost)){
				?>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>ENGINEERING COST</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" colspan='7'>Item Product</th>
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
					foreach($getEngCost AS $val => $valx){
						$PRICE_DEAL = (!empty($valx['price_deal']))?$valx['price_deal']:$valx['price_total'];
						$UNIT_PRICE = (!empty($valx['price_deal']))?$valx['price_deal']/$valx['qty']:$valx['price_total']/$valx['qty'];
						$SUM1 += $PRICE_DEAL;
						$no1++;
						echo "<tr>";
							echo "<td colspan='7'>".strtoupper($valx['name_test'])."</td>";
							echo "<td align='center'>".number_format($valx['qty'])."</td>";
							echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
							echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
							echo "<td align='right'>".number_format($PRICE_DEAL,2)."</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
						<td align='right'><b><?= number_format($SUM1,2);?></b></td>
					</tr>
				</tbody>
				<?php
				}
				$SUM2=0;
				if(!empty($getPackCost)){
				?>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>PACKING COST</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" colspan='9'>Category</th>
						<th class="text-center">Type</th>
						<th class="text-center">Total Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$no2=0;
					$SUM2=0;
					foreach($getPackCost AS $val => $valx){
						$no2++;
						$PRICE_PACKING = (!empty($valx['price_deal']))?$valx['price_deal']:$valx['price_total'];
						$SUM2 += $PRICE_PACKING;
						echo "<tr>";
							echo "<td colspan='9'>".strtoupper($valx['category']);
							echo "</td>";
							echo "<td align='center'>".strtoupper($valx['jenis']);
							echo "</td>";
							echo "<td align='right'>".number_format($PRICE_PACKING,2);
							echo "</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL PACKING COST</b></td>
						<td align='right'><b><?= number_format($SUM2,2);?></b></td>
					</tr>
				</tbody>
				<?php
				}
				$SUM3=0;
				if(!empty($getTruck)){
				?>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>TRUCKING EXPORT</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" colspan='7'>Category</th>
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
					foreach($getTruck AS $val => $valx){
						$PRICE_EXPORT 	= (!empty($valx['price_deal']))?$valx['price_deal']:$valx['price_total'];
						$SUM3 += $PRICE_EXPORT;
						$no3++;

						$QTY_EXPORT 	= (!empty($valx['qty']))?$valx['qty']:0;
						$PRICE_UNIT = 0;
						if($QTY_EXPORT > 0 AND $PRICE_EXPORT > 0){
							$PRICE_UNIT = $PRICE_EXPORT / $QTY_EXPORT;
						}

						echo "<tr>";
							echo "<td colspan='7'>".strtoupper($valx['category'])."</td>";
							echo "<td align='center'>".$QTY_EXPORT."</td>";
							echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
							echo "<td align='right'>".number_format($PRICE_UNIT,2)."</td>";
							echo "<td align='right'>".number_format($PRICE_EXPORT,2)."</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
						<td align='right'><b><?= number_format($SUM3,2);?></b></td>
					</tr>
				</tbody>
				<?php
				}
				$SUM4=0;
				if(!empty($getVia)){
				?>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>TRUCKING LOKAL</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center">Item Product</th>
						<th class="text-center" colspan='3'>Area</th>
						<th class="text-center" colspan='2'>Tujuan</th>
						<th class="text-center" colspan='2'>Kendaraan</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Price</th>
						<th class="text-center">Total Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$no4=0;
					$SUM4=0;
					foreach($getVia AS $val => $valx){
						$PRICE_LOCAL 	= (!empty($valx['price_deal']))?$valx['price_deal']:$valx['price_total'];
						$SUM4 			+= $PRICE_LOCAL;
						$AREA_DEST 		= ($valx['area'] == '0')?'-':strtoupper($valx['area']);
						$DESTINATION 	= ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
						if(strtolower($valx['category']) == 'via laut' || strtolower($valx['category']) == 'via darat'){
							$KENDARAAN 	= ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
						}
						else{
							$KENDARAAN 	= strtoupper($valx['kendaraan']);
						}
						
						$QTY_LOCAL 	= (!empty($valx['qty']))?$valx['qty']:0;
						$PRICE_UNIT = 0;
						if($QTY_LOCAL > 0 AND $PRICE_LOCAL > 0){
							$PRICE_UNIT = $PRICE_LOCAL / $QTY_LOCAL;
						}
						
						$no4++;
						echo "<tr>";
							echo "<td>".strtoupper($valx['category'])."</td>";
							echo "<td align='left' colspan='3'>".$AREA_DEST."</td>";
							echo "<td align='left' colspan='2'>".$DESTINATION."</td>";
							echo "<td align='left' colspan='2'>".$KENDARAAN."</td>";
							echo "<td align='center'>".$QTY_LOCAL."</td>";
							echo "<td align='right'>";
								echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($PRICE_UNIT,2)."</div>";
							echo "</td>";
							echo "<td align='right'>";
								echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($PRICE_LOCAL,2)."</div>";
							echo "</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
						<td align='right'><b><?= number_format($SUM4,2);?></b></td>
					</tr>
				</tbody>
				<?php
				}
				?>
				<tfoot>
					<tr class='HeaderHr'>
						<th align='left' colspan='10'>TOTAL</th>
						<th align='right' style='text-align:right;'><?=  number_format($SUM2x + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT2x + $SUM_NONFRP2x, 2);?></th>
					</tr>
				</tfoot>
			</table>
		</div>

		<div class='col-sm-4 '>	
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"  style='font-size: 12px !important;'>
				<thead>
					<tr>
						<th class="text-left"></th>
						<th class="text-center" width='10%'></th>
						<th class="text-right" width='25%'></th>
						<th class="text-right" width='25%'></th>
					</tr>
					<tr>
						<td class="text-center headX HeaderHr" colspan='4'><b>SALES ORDER VS FINAL DRAWING</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-left">CATEGORY</th>
						<th class="text-center" width='10%'>KURS</th>
						<th class="text-right" width='25%'>SALES ORDER</th>
						<th class="text-right" width='25%'>FINAL DRAWING</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-left">A. PRODUCT</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM2x,2);?></td>
						<td class="text-right"><?=number_format($SUM,2);?></td>
					</tr>
					<tr>
						<td class="text-left">B. AKSESORIS</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM_NONFRP2x,2);?></td>
						<td class="text-right"><?=number_format($SUM_NONFRP,2);?></td>
					</tr>
					<tr>
						<td class="text-left">C. MATERIAL</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM_MAT2x,2);?></td>
						<td class="text-right"><?=number_format($SUM_MAT,2);?></td>
					</tr>
					<tr>
						<td class="text-left">D. ENGINEERING COST</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM1,2);?></td>
						<td class="text-right"><?=number_format($SUM1,2);?></td>
					</tr>
					<tr>
						<td class="text-left">E. PACKING COST</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM2,2);?></td>
						<td class="text-right"><?=number_format($SUM2,2);?></td>
					</tr>
					<tr>
						<td class="text-left">F. TRUCKING EXPORT COST</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM3,2);?></td>
						<td class="text-right"><?=number_format($SUM3,2);?></td>
					</tr>
					<tr>
						<td class="text-left">G. TRUCKING LOKAL COST</td>
						<td class="text-center">USD</td>
						<td class="text-right"><?=number_format($SUM4,2);?></td>
						<td class="text-right"><?=number_format($SUM4,2);?></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-left" rowspan='2' style='vertical-align:middle;'>TOTAL COST</th>
						<th class="text-center">USD</th>
						<th class="text-right"><?=number_format($SUM2x + $SUM_MAT2x + $SUM1 + $SUM2 + $SUM3 + $SUM4 + $SUM_NONFRP2x,2);?></th>
						<th class="text-right"><?=number_format($SUM + $SUM_MAT + $SUM1 + $SUM2 + $SUM3 + $SUM4 + $SUM_NONFRP,2);?></th>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } ?>
<style>
	.HeaderHr{
		background-color: #0073b7 ;
		color: white;
	}
	
    .chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$(".chosen-select").chosen();
		$(".chosen_select").chosen();
		
		$("#chk_all").click(function(){
			$('.chk_personal').not(this).prop('checked', this.checked);
		});
		
		$("#chk_all_aksesoris").click(function(){
			$('.chk_personal_acc').not(this).prop('checked', this.checked);
		});
		
		$("#chk_all_material").click(function(){
			$('.chk_personal_mat').not(this).prop('checked', this.checked);
		});

		$('#ChangeMaterial').hide();

		$("#LookChange").click(function(){
			$("#ChangeMaterial").slideToggle();
		});
	});
	
	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N' || $(this).val() == 'M'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
			}
		});
		
		$(document).on('change', '.stsSelect', function(){
			var nomor = $(this).data('nomor');
			
			
			if($(this).val() == 'N' || $(this).val() == 'M'){
				$('#reason_'+nomor).attr("readonly", false); 
			}
			else{
				$('#reason_'+nomor).attr("readonly", true); 
			}
		});
		
		$(document).on('click', '.appSatuan', function(){
			var id 		= $(this).data('id');
			var id_bq 	= $(this).data('id_bq');
			var nomor 	= $(this).data('nomor');
			
			if($('#sts_'+nomor).val() == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Action belum dipilih ...',
				  type	: "warning"
				});
				return false;
			}
			
			if($('#sts_'+nomor).val() == 'N' || $('#sts_'+nomor).val() == 'M'){
				var reason = $('#reason_'+nomor).val();
				if(reason == ''){
					swal({
					  title	: "Error Message!",
					  text	: 'Alasan reject masih kosong ...',
					  type	: "warning"
					});
					return false;
				}
				
			}
			// return false;
			swal({
			  title: "Apakah anda yakin ???",
			  text: "Approve Final Drawing",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses !",
			  cancelButtonText: "Tidak, Batalkan !",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/AppBQFDSatuan/'+id_bq+'/'+id+'/'+nomor,
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
								$("#head_title").html("<b>APPROVE FINAL DRAWING ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppFD_new/'+data.id_bq);
								$("#ModalView").modal();
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
								$("#head_title").html("<b>APPROVE FINAL DRAWING ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppFD_new/'+data.id_bq);
								$("#ModalView").modal();
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

		
		$(document).on('click', '.app_aksesoris', function(){
			var id 		= $(this).data('id');
			var id_bq 	= $(this).data('id_bq');
			var nomor 	= $(this).data('nomor');
			
			if($('#sts_'+nomor).val() == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Action belum dipilih ...',
				  type	: "warning"
				});
				return false;
			}
			
			if($('#sts_'+nomor).val() == 'N' || $('#sts_'+nomor).val() == 'M'){
				var reason = $('#reason_'+nomor).val();
				if(reason == ''){
					swal({
					  title	: "Error Message!",
					  text	: 'Alasan reject masih kosong ...',
					  type	: "warning"
					});
					return false;
				}
				
			}
			// return false;
			swal({
			  title: "Apakah anda yakin ???",
			  text: "Approve Final Drawing",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses !",
			  cancelButtonText: "Tidak, Batalkan !",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/approve_fd_aksesoris/'+id_bq+'/'+id+'/'+nomor,
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
								$("#head_title").html("<b>APPROVE FINAL DRAWING ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modalAppFD_new/'+data.id_bq);
								$("#ModalView").modal();
							}
							else if(data.status == 0){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
								$("#head_title").html("<b>APPROVE FINAL DRAWING ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modalAppFD_new/'+data.id_bq);
								$("#ModalView").modal();
							}
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 7000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
	});

</script>