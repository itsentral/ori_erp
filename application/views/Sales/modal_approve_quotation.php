
<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			Checklist komponen untuk menjadikan <b>Sales Order</b>, <u>dengan catatan Approve Action 'APPROVE'</u><br>
			<span style='color:green;'><b>MOHON CEK TERLEBIH COST SATUAN, UNTUK MENIMINALISIR KESALAHAN</b></span><br>
			<span style='color:green;'><b><u>PRODUCT KOSONG WAJIB DI APPROVE !</u></b></span>
		</p>
	</div>
	<div class="form-group row">
		<div class='col-sm-4 '>
		   <label class='label-control'>Approve Action</label>
		   <select name='status' id='status' class='form-control input-md'>
				<option value='0'>Select Action</option>
				<option value='Y'>APPROVE</option>
				<option value='N'>REVISI TO ENGINEERING</option>
				<option value='X'>REVISI TO COSTING</option>
			</select>
			<?php
			echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
			?>
		</div>
		<div class='col-sm-8 '>
			<div id='HideReject'>
				<label class='label-control'>Reject Reason</label>          
				<?php
					echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Revision reason'));
				?>		
			</div>
		</div>
		
	</div>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 0px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'approvedQ')).' ';
	?>
	<div class="table-responsive">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th style='background:none;' width='7%' class='no-sort'><font size='2'><B><center><input type='checkbox' name='chk_all' id='chk_all'></center></B></font></th>
				<!--<th class="text-center" style='vertical-align:middle;' width='6%'>Chk</th>-->
				<th class="text-center" style='vertical-align:middle;' width='17%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Weight</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Price Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Price Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no = 0;
				$Sum 	= 0;
				$SumX 	= 0;
				foreach($qBQdetailRest AS $val => $valx){ $no++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']); 
					$bgwarna	= "bg-blue";

					$Sum 	+= $valx['total_price_last'] / $valx['qty'];
					$SumX 	+= $valx['total_price_last'];
					
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					
					$dist = '';
					$ks_check = "<input type='checkbox' name='check[$no]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."' ".$dist.">";
					if($valx['so_sts'] == 'Y'){
						$dist = 'disabled';
						$ks_check = "<b style='color:green;'>&#10003; SO</b>";
					}
					echo "<tr>";
						echo "<td align='right' style='vertical-align:middle;'><center>".$ks_check."</center></td>";
						// echo "<td align='right' style='vertical-align:middle;'><center><input type='checkbox' name='check[$no]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."' ".$dist."></center></td>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='left'>".$spaces."".spec_bq($valx['id'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right'>".number_format($valx['est_material'],3)."</td>";
						echo "<td align='left'>KG</td>";
						echo "<td align='right'>".number_format($valx['total_price_last'] / $valx['qty'], 2)."</span></td>";
						echo "<td align='right'>".number_format($valx['total_price_last'], 2)."</span></td>";
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-left"></th>
				<th class="text-left" colspan='7' style='vertical-align:middle;'>TOTAL COST PRODUCT</th>
				<th class="text-right">
					<?= number_format($SumX, 2);?>
					<input type='hidden' name='total_kg' value='<?= number_format($Sum, 2);?>'>
					<input type='hidden' name='total_cost' value='<?= number_format($SumX, 2);?>'>
				</th>
			</tr>
			<?php
				//material
				$SUM_MAT = 0;
				$no2 = 0;
				if(!empty($material)){
					foreach($material AS $val => $valx){ $no2++;
						$dist = '';
						$ks_check2 = "<input type='checkbox' name='check2[$no2]' class='chk_personal' data-nomor='".$no2."' value='".$valx['id2']."' ".$dist.">";
					
						if($valx['so_sts'] == 'Y'){
							$dist = 'disabled';
							$ks_check2 = "<b style='color:green;'>&#10003; SO</b>";
						}
						$SUM_MAT += $valx['price_total'];
						echo "<tr>";
							echo "<td align='right' style='vertical-align:middle;'><center>".$ks_check2."</center></td>";
							// echo "<td align='right' style='vertical-align:middle;'><center><input type='checkbox' name='check2[$no2]' class='chk_personal' data-nomor='".$no2."' value='".$valx['id2']."' ".$dist."></center></td>";
							echo "<td colspan='4'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
							echo "<td align='right'>".number_format($valx['qty'],3)."</td>";
							echo "<td align='left'>KG</td>";
							echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td></td>";
						echo "<td colspan='7'><b>TOTAL COST MATERIAL</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
					echo "</tr>";
				}
				// echo "<tr class='FootColor'>";
					// echo "<td></td>";
					// echo "<td colspan='6'><b>TOTAL COST (USD)</b></td> ";
					// echo "<td align='right'><b>".number_format($SUM_MAT + $SumX, 2)."</b></td>";
				// echo "</tr>";
				// echo "<tr class='FootColor'>";
					// echo "<td></td>";
					// echo "<td colspan='7'><b>&nbsp;</b></td> ";
				// echo "</tr>";
				//material
				$SUM_NONFRP = 0;
				$no2 = 0;
				if(!empty($non_frp)){
					foreach($non_frp AS $val => $valx){ $no2++;
						$dist = '';
						$ks_check2 = "<input type='checkbox' name='check2[$no2]' class='chk_personal' data-nomor='".$no2."' value='".$valx['id2']."' ".$dist.">";
						if($valx['so_sts'] == 'Y'){
							$dist = 'disabled';
							$ks_check2 = "<b style='color:green;'>&#10003; SO</b>";
						}
						$SUM_NONFRP += $valx['price_total'];
						
						$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
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
						$satuan = $valx['option_type'];
						if($valx['category'] == 'plate'){
							$qty = $valx['weight'];
							$satuan = '1';
						}
						
						echo "<tr>";
							echo "<td align='right' style='vertical-align:middle;'><center>".$ks_check2."</center></td>";
							echo "<td colspan='4'>".$nama_acc."</td>";
							echo "<td align='right'>".number_format($qty,2)."</td>";
							echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
							echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td></td>";
						echo "<td colspan='7'><b>TOTAL COST BQ NON FRP</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_NONFRP, 2)."</b></td>";
					echo "</tr>";
					
				}
				echo "<tr class='FootColor'>";
					echo "<td></td>";
					echo "<td colspan='7'><b>TOTAL COST</b></td> ";
					echo "<td align='right'><b>".number_format($SUM_MAT + $SumX + $SUM_NONFRP, 2)."</b></td>";
				echo "</tr>";
			?>
			
		</tbody>
	</table>
	</div>
	<br>
	<div class="table-responsive">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr>
				<th class="text-center" colspan='2' width='16%'></th>
				<th class="text-center" width='6%'></th>
				<th class="text-center" width='6%'></th>
				<th class="text-center" width='6%'></th>
				<th class="text-center" width='8%'></th>
				<th class="text-center" width='15%'></th>
				<th class="text-center" width='10%'></th>
				<th class="text-center" width='7%'></th>
				<th class="text-center" width='8%'></th>
				<th class="text-center" width='10%'></th>
				<th class="text-center" width='10%'></th>
			</tr>
		</thead>
		<!--
		<tbody>
			<?php
			// $SUM = 0;
			// $no = 0;
			// $SUM_MAT = 0;
			// foreach($getDetail AS $val => $valx){
				// $no++;
				// $dataSum = 0;
				// if($valx['qty'] <> 0){
					// $dataSum	= $valx['cost'];
				// }
				// $SUM += $dataSum;
				
				// if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					// $unitT = "Btg";
				// }
				// else{
					// $unitT = "Pcs";
				// }
				// $SUM_MAT += $valx['est_material'];
				// echo "<tr>";
					// echo "<td colspan='2'>".strtoupper($valx['id_category'])."</td>";
					// echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					// echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					// echo "<td align='center'>".substr($valx['series'],6,5)."</td>";
					// echo "<td align='center'>".substr($valx['series'],3,2)."</td>";
					// echo "<td align='left'>".spec_bq($valx['id'])."</td>";
					// echo "<td align='right'>".number_format($valx['est_material'],3)." Kg</td>";
					// echo "<td align='center'>".$valx['qty']."</td>";
					// echo "<td align='center'>".$unitT."</td>";
					// echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					// echo "<td align='right'>".number_format($dataSum,2)."</td>";
				// echo "</tr>";
			// }
			?>
			<tr class='FootColor'>
				<td colspan='7'><b>TOTAL OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM_MAT,3);?> Kg</b></td>
				<td colspan='3'></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		-->
		<?php
		// $SUM_NONFRP = 0;
		// if(!empty($non_frp)){
			// echo "<tbody>";
				// echo "<tr class='bg-blue'>";
					// echo "<td class='text-left headX HeaderHr' colspan='12'><b>BQ NON FRP</b></td>";
				// echo "</tr>";
				// echo "<tr class='bg-blue'>";
					// echo "<th class='text-center' colspan='8'>Material Name</th>";
					// echo "<th class='text-center'>Qty</th>";
					// echo "<th class='text-center'>Unit</th>";
					// echo "<th class='text-center'>Unit Price</th>";
					// echo "<th class='text-center'>Total Price</th>";
				// echo "</tr>";
			// echo "</tbody>";
			// echo "<tbody class='body_x'>";
			// foreach($non_frp AS $val => $valx){
				// $SUM_NONFRP += $valx['price_total'];
				
				// $get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
				// $radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
				// $nama_acc = "";
				// if($valx['category'] == 'baut'){
					// $nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
				// }
				// if($valx['category'] == 'plate'){
					// $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
				// }
				// if($valx['category'] == 'gasket'){
					// $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
				// }
				// if($valx['category'] == 'lainnya'){
					// $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
				// }
					
				// $qty = $valx['qty'];
				// $satuan = $valx['option_type'];
				// if($valx['category'] == 'plate'){
					// $qty = $valx['weight'];
					// $satuan = '1';
				// }
				// echo "<tr>";
					// echo "<td colspan='8'>".$nama_acc."</td>";
					// echo "<td align='right'>".number_format($qty,2)."</td>";
					// echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
					// echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
					// echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				// echo "</tr>";
			// }
			// echo "<tr class='FootColor'>";
				// echo "<td colspan='11'><b>TOTAL BQ NON FRP</b></td> ";
				// echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
			// echo "</tr>";
			// echo "</tbody>";
		// }
		// $SUM_MAT = 0;
		// if(!empty($material)){
			// echo "<tbody>";
				// echo "<tr class='bg-blue'>";
					// echo "<td class='text-left headX HeaderHr' colspan='12'><b>MATERIAL</b></td>";
				// echo "</tr>";
				// echo "<tr class='bg-blue'>";
					// echo "<th class='text-center' colspan='8'>Material Name</th>";
					// echo "<th class='text-center'>Weight</th>";
					// echo "<th class='text-center'>Unit</th>";
					// echo "<th class='text-center'>Unit Price</th>";
					// echo "<th class='text-center'>Total Price</th>";
				// echo "</tr>";
			// echo "</tbody>";
			// echo "<tbody class='body_x'>";
			// foreach($material AS $val => $valx){
				// $SUM_MAT += $valx['price_total'];
				// echo "<tr>";
					// echo "<td colspan='8'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
					// echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
					// echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					// echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
					// echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				// echo "</tr>";
			// }
			// echo "<tr class='FootColor'>";
				// echo "<td colspan='11'><b>TOTAL MATERIAL</b></td> ";
				// echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			// echo "</tr>";
			// echo "</tbody>";
		// }
		
		$SUM1=0;
		if(!empty($getEngCost)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>ENGINEERING COST</b></td>
			</tr>
			<tr  class='bg-blue'>
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
			foreach($getEngCost AS $val => $valx){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='8'>".strtoupper($valx['name'])."</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL ENGINEERING COST</b></td>
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$SUM2=0;
		if(!empty($getPackCost)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>PACKING COST</b></td>
			</tr>
			<tr  class='bg-blue'>
				<th class="text-center" colspan='10'>Category</th>
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
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='10'>".strtoupper($valx['name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL PACKING COST</b></td>
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$SUM3=0;
		if(!empty($getTruck)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr  class='bg-blue'>
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
			foreach($getTruck AS $val => $valx){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$no3++;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper($valx['shipping_name']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['type'])."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		$SUM4=0;
		if(!empty($getVia)){
		?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left" colspan='12'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr  class='bg-blue'>
				<th class="text-center">Item Product</th>
				<th class="text-center" colspan='3'>Area</th>
				<th class="text-center" colspan='2'>Tujuan</th>
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
				
				$no4++;
				echo "<tr>";
					echo "<td>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td align='left' colspan='3'>".$Areax."</td>";
					echo "<td align='left' colspan='2'>".$Tujuanx."</td>";
					echo "<td align='left' colspan='3'>".$Kendaraanx."</td>";
					echo "<td align='center'>".$Qty4."</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='11'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<?php 
		$SUM_OTHER = 0;
		if(!empty($otherArray)) { ?>
		<tbody>
			<tr class='bg-blue'>
				<td class="text-left headX HeaderHr" colspan='12'><b>OTHER</b></td>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center" colspan='9'>Description</th>
				<th class="text-center">Unit Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Price</th>
			</tr>
		</tbody>
		<tbody>
			<?php
			$nomor = 0;
			
			foreach ($otherArray as $key => $value) { $nomor++;
				$SUM_OTHER += $value['price_total'];
				echo "<tr>";
					echo "<td align='left' colspan='9'>".$value['caregory_sub']."</td>";
					echo "<td align='right'>".number_format($value['price'],2)."</td>";
					echo "<td align='center'>".number_format($value['qty'],2)."</td>";
					echo "<td align='right'>".number_format($value['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<th colspan='11'>TOTAL OTHER</th>
				<th class="text-right"><?=number_format($SUM_OTHER,2);?></th>
			</tr>
		</tbody>
		<?php } ?>
		<tfoot>
			<tr class='HeaderHr'>
				<th align='left' colspan='11'>TOTAL</th>
				<th align='right' style='text-align:right;'><?=  number_format($SumX + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP + $SUM_OTHER, 2);?></th>
			</tr>
			<?php
				// if(!empty($non_frp)){
					// echo "<tr class='HeaderHr'>";
						// echo "<th align='left' colspan='10'></th>";
						// echo "<th align='center' style='text-align:center;'>IDR</th>";
						// echo "<th align='right' style='text-align:right;'>".number_format($SUM_NONFRP, 2)."</th>";
					// echo "</tr>";
				// }
			?>
		</tfoot>
	</table>
	</div>
</div>
<style>
	
</style>
<script>
	swal.close();
	
	$("#chk_all").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
	
	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N' || $(this).val() == 'X'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
			}
		});
		
		$(document).on('click', '#approvedQ', function(){
			var bF				= $('#id_bq').val();
			var status 			= $('#status').val();
			var approve_reason 	= $('#approve_reason').val();
			
			if(status == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Action approve belum dipilih ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			if(status == 'N' && approve_reason == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Alasan reject masih kosong ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			if(status == 'Y'){
				if($('.chk_personal:checked').length == 0){
					swal({
					  title	: "Error Message!",
					  text	: 'Checklist Component Deal',
					  type	: "warning"
					});
					$('#approvedQ').prop('disabled',false);
					return false;
				}
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
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url+active_controller+'/AppCostNew/'+bF,
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
								window.location.href = base_url + active_controller+'/quotation';
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
		
		
	});

</script>