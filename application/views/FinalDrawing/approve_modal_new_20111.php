<?php
$id_bq = $this->uri->segment(3);

$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
$getHeader	= $this->db->query($qSupplier)->result();

$qMatr 		= SQL_FD($id_bq);					
$getDetail	= $this->db->query($qMatr)->result_array();
// echo $qMatr;
$qMatrSO 		= SQL_SO($id_bq);		
$getDetailSO	= $this->db->query($qMatrSO)->result_array();

$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y' ORDER BY a.id ASC ";
$getEngCost	= $this->db->query($engC)->result_array();

$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' AND b.price_total != 0 ORDER BY a.id ASC ";
$getPackCost	= $this->db->query($engCPC)->result_array();
// echo $engCPC;
$gTruck 	= "SELECT a.*, b.* FROM list_shipping a INNER JOIN cost_project_detail b ON CONCAT_WS(' ',a.shipping_name, a.type)=b.caregory_sub WHERE a.flag = 'Y' AND b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y' AND b.price_total != 0 ORDER BY a.urut ASC ";
$getTruck	= $this->db->query($gTruck)->result_array();

$engCPCV 	= "SELECT
					b.*,
					c.* 
				FROM
					cost_project_detail b
					LEFT JOIN truck c ON b.kendaraan = c.id 
				WHERE
					 b.category = 'lokal' 
					AND b.id_bq = '".$id_bq."' 
					AND b.price_total <> 0
				ORDER BY
					b.id ASC ";
$getVia	= $this->db->query($engCPCV)->result_array();

$checkSO 	= "	SELECT * FROM production WHERE no_ipp = '".$ipp."' AND (`status`='WAITING APPROVE FINAL DRAWING' OR `status`='PARTIAL PROCESS') ";
$restChkSO	= $this->db->query($checkSO)->num_rows();
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
			<strong>Info!</strong><br> 
			Pilih Action sesuai component yang disetujui, kemudian approve (checklist hijau).<br>
		</p>
		<p>
			<strong>Info Approve Checklist!</strong><br> 
			Checklist semua atau sebagian, kemudian pilih actionnya. Gunakan checklist header untuk checklist semua.<br>
			<span style='color:green;'><b>MOHON CEK TERLEBIH DAHULU COST SATUAN,<span> <span style='color:red;'>UNTUK MENIMINALISIR KESALAHAN</b></span>
		</p>
	</div>
	<div class="form-group row">
		<div class='col-sm-5 '>
		</div>
		<div class='col-sm-3 '>
		   <label class='label-control'>Approve Action</label><br>
		   <select name='status' id='status' class='form-control input-md ' style='width:100%;'>
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
	<div class="form-group row">
		<div class='col-sm-5 '>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%" style='font-size: 12px !important;'>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>PRODUCT SALES ORDER</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='2' width='22%'>Item Product</th>
						<th class="text-center" width='6%'>Dim 1</th>
						<th class="text-center" width='6%'>Dim 2</th>
						<th class="text-center" width='6%'>Liner</th>
						<th class="text-center" width='8%'>Pressure</th>
						<th class="text-center">Specification</th>
						<th class="text-center" width='10%'>Unit Price</th>
						<th class="text-center" width='7%'>Qty</th>
						<th class="text-center" width='8%'>Unit</th>
						<th class="text-center" width='11%'>Total Price</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$SUM = 0;
					$no = 0;
					foreach($getDetailSO AS $val => $valx){
						$no++;
						// $NegoPersen 	= (!empty($valx['nego']))?$valx['nego']:'10';
						$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
							
						$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
						$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
						
						$est_harga 	= ($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan']) / $valx['qty'];
						$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
						$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
						
						$nego		= $HrgTot * ($NegoPersen/100);
						$dataSum	= $HrgTot + $nego;
						
						$SUM += $dataSum; 
						
						if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
							$dim = number_format($valx['diameter_1'])." x ".number_format($valx['length'])." x ".floatval($valx['thickness']);
						}
						elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
							$dim = number_format($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".floatval($valx['sudut']);
						}
						elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
							$dim = number_format($valx['diameter_1'])." x ".number_format($valx['diameter_2'])." x ".floatval($valx['thickness']);
						}
						elseif($valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong' OR $valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
							$dim = number_format($valx['diameter_1'])." x ".floatval($valx['thickness']);
						}
						elseif($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' ){
							$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length']);
						}
						else{$dim = "belum di set";} 
						
						if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
							$unitT = "Btg";
						}
						else{
							$unitT = "Pcs";
						}
						echo "<tr>";
							echo "<td colspan='2'>".strtoupper($valx['parent_product'])."</td>";
							echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
							echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
							echo "<td align='center'>".$valx['liner']."</td>";
							echo "<td align='center'>".$valx['pressure']."</td>";
							echo "<td align='left'>".$dim."</td>";
							echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
							echo "<td align='center'>".$valx['qty']."</td>";
							echo "<td align='center'>".$unitT."</td>";
							echo "<td align='right'>".number_format($dataSum,2)."</td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
						<td align='right'><b><?= number_format($SUM,2);?></b></td>
					</tr>
				</tbody>
				<?php
				$SUM1=0;
				if(!empty($getEngCost)){
				?>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>ENGINEERING COST</b></td>
					</tr>
					<tr class='bg-bluexyz'>
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
						$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
						$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
						$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
						$SUM1 += $valx['price_total'];
						$no1++;
						echo "<tr>";
							echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
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
					<tr class='bg-bluexyz'>
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
						$SUM2 += $valx['price_total'];
						echo "<tr>";
							echo "<td colspan='9'>".strtoupper($valx['name']);
							echo "</td>";
							echo "<td align='center'>".strtoupper($valx['option_type']);
							echo "</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2);
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
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='6'>Category</th>
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
							echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
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
					<tr class='bg-bluexyz'>
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
						$SUM4 += $valx['price_total'];
						$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
						$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
						$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
						
						$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
						
						$no4++;
						echo "<tr>";
							echo "<td>".strtoupper($valx['caregory_sub'])."</td>";
							echo "<td align='center' colspan='3'>".$Areax."</td>";
							echo "<td align='center' colspan='2'>".$Tujuanx."</td>";
							echo "<td align='center' colspan='2'>".$Kendaraanx."</td>";
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
						<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1, 2);?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		
		<div class='col-sm-7 '>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"  style='font-size: 12px !important;'>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>PRODUCT FINAL DRAWING</b></td>
						<td class="text-left headX HeaderHr" colspan='4'><b>APPROVE ACTION</b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='2'>Item Product</th>
						<th class="text-center" width='5%'>Dim1</th>
						<th class="text-center" width='5%'>Dim2</th>
						<th class="text-center" width='4%'>Lin</th>
						<th class="text-center" width='4%'>Pre</th>
						<th class="text-center">Specification</th>
						<th class="text-center" width='7%'>Unit Price</th>
						<th class="text-center" width='4%'>Qty</th>
						<th class="text-center" width='6%'>Unit</th>
						<th class="text-center" width='8%'>Total Price</th>
						<th class="text-center" width='3%'><font size='2'><B><center><input type='checkbox' name='chk_all' id='chk_all'></center></B></font></th>
						<th class="text-center" width='10%'>Action</th>
						<th class="text-center" width='14%'>Reason</th>
						<th class="text-center" width='4%'>#</th>
					</tr>
				</tbody>
				<tbody>
					<?php
					$SUM = 0;
					$no = 0;
					foreach($getDetail AS $val => $valx){
						$no++;
						if(check_fd($valx['id']) != 'N'){
							// $NegoPersen 	= (!empty($valx['nego']))?$valx['nego']:'10';
							$NegoPersen 	= (!empty($valx['nego']))?'0':'0';
								
							$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
							$extra 		= (!empty($valx['extra']))?$valx['extra']:15; 
							
							$est_harga 	= ($valx['est_harga2']+$valx['cost_process']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan']) / $valx['qty'];
							$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
							$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
							
							$nego		= $HrgTot * ($NegoPersen/100);
							$dataSum	= $HrgTot + $nego;
							
							$SUM += $dataSum;
							
							if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
								$unitT = "Btg";
							}
							else{
								$unitT = "Pcs";
							}
							$dist = '';
							if(check_fd($valx['id']) == 'Y'){
								$dist = "<center><input type='checkbox' name='check[".$no."]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."' ></center>";
							}
					
							echo "<tr>";
								echo "<td style='vertical-align: middle; 'colspan='2'>".strtoupper($valx['parent_product'])."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($valx['diameter_1'])."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($valx['diameter_2'])."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$valx['liner']."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$valx['pressure']."</td>";
								echo "<td style='vertical-align: middle; text-align:left;'>".spec_fd($valx['id'], 'so_detail_header')."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($dataSum / $valx['qty'],2)."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$valx['qty']."</td>";
								echo "<td style='vertical-align: middle; text-align:center;'>".$unitT."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".number_format($dataSum,2)."</td>";
								echo "<td style='vertical-align: middle; text-align:right;'>".$dist."</td>";
								echo "<td style='vertical-align: middle; align='center'>";
									if(check_fd($valx['id']) == 'Y'){
										echo "<select name='sts_".$no."' id='sts_".$no."' data-nomor='".$no."' class='form-control input-sm stsSelect'>
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
									if(check_fd($valx['id']) == 'Y'){
										echo form_input(array('id'=>'reason_'.$no,'name'=>'reason_'.$no,'class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly','placeholder'=>'Reason'));
									}
								echo "</td>";
								echo "<td style='vertical-align: middle; align='center'>";
									if(check_fd($valx['id']) == 'Y'){
										echo "<button type='button' class='btn btn-sm btn-success appSatuan' title='Approve Component' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id='".$valx['id']."'><i class='fa fa-check'></i></button>";
									}
								echo "</td>";
							echo "</tr>";
						}
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
						<td align='right'><b><?= number_format($SUM,2);?></b></td>
						<td colspan='3'></td>
					</tr>
				</tbody>
				<?php
				$SUM1=0;
				if(!empty($getEngCost)){
				?>
				<tbody>
					<tr>
						<td class="text-left headX HeaderHr" colspan='11'><b>ENGINEERING COST</b></td>
						<td class="text-left headX HeaderHr" colspan='3'><b></b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='7'>Item Product</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Unit</th>
						<th class="text-center">Price</th>
						<th class="text-center">Total Price</th>
						<th colspan='3'></th>
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
							echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
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
							echo "<td colspan='3'></td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL ENGINEERING COST</b></td>
						<td align='right'><b><?= number_format($SUM1,2);?></b></td>
						<td colspan='3'></td>
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
						<td class="text-left headX HeaderHr" colspan='3'><b></b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='9'>Category</th>
						<th class="text-center">Type</th>
						<th class="text-center">Total Price</th>
						<th colspan='3'></th>
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
							echo "<td colspan='9'>".strtoupper($valx['name']);
							echo "</td>";
							echo "<td align='center'>".strtoupper($valx['option_type']);
							echo "</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2);
							echo "</td>";
							echo "<td colspan='3'></td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL PACKING COST</b></td>
						<td align='right'><b><?= number_format($SUM2,2);?></b></td>
						<td colspan='3'></td>
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
						<td class="text-left headX HeaderHr" colspan='3'><b></b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center" colspan='6'>Category</th>
						<th class="text-center">Type</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Fumigation</th>
						<th class="text-center">Price</th>
						<th class="text-center">Total Price</th>
						<th colspan='3'></th>
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
							echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
							echo "</td>";
							echo "<td align='center'>".strtoupper($valx['type'])."</td>";
							echo "<td align='center'>".$Qty3."</td>";
							echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
							echo "<td align='right'>".number_format($valx['price'],2)."</td>";
							echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
							echo "<td colspan='3'></td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
						<td align='right'><b><?= number_format($SUM3,2);?></b></td>
						<td colspan='3'></td>
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
						<td class="text-left headX HeaderHr" colspan='3'><b></b></td>
					</tr>
					<tr class='bg-bluexyz'>
						<th class="text-center">Item Product</th>
						<th class="text-center" colspan='3'>Area</th>
						<th class="text-center" colspan='2'>Tujuan</th>
						<th class="text-center" colspan='2'>Kendaraan</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Price</th>
						<th class="text-center">Total Price</th>
						<th colspan='3'></th>
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
						$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
						
						$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
						
						$no4++;
						echo "<tr>";
							echo "<td>".strtoupper($valx['caregory_sub'])."</td>";
							echo "<td align='center' colspan='3'>".$Areax."</td>";
							echo "<td align='center' colspan='2'>".$Tujuanx."</td>";
							echo "<td align='center' colspan='2'>".$Kendaraanx."</td>";
							echo "<td align='center'>".$Qty4."</td>";
							echo "<td align='right'>";
								echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
							echo "</td>";
							echo "<td align='right'>";
								echo "<div id='unit_".$no4."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
							echo "</td>"; 
							echo "<td colspan='3'></td>";
						echo "</tr>";
					}
					?>
					<tr class='FootColor'>
						<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
						<td align='right'><b><?= number_format($SUM4,2);?></b></td>
						<td colspan='3'></td>
					</tr>
				</tbody>
				<?php
				}
				?>
				<tfoot>
					<tr class='HeaderHr'>
						<th align='left' colspan='10'>TOTAL</th>
						<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1, 2);?></th>
						<th colspan='3'></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	
	<?php
		// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'min-width:100px; float:right; margin: 10px 0px 5px 0px;','value'=>'Please Dont Try','content'=>'Please Dont Try','id'=>'approvedFDNew')).' ';
	?>
</div>
<?php } ?>
<style>
	.HeaderHr{
		background-color: #0073b7 ;
		color: white;
	}
	
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

</style>
<script>
	$(document).ready(function(){
		swal.close();
		$(".chosen-select").chosen();
		$("#chk_all").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
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
								$("#head_title").html("<b>APPROVE FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppFD/'+data.id_bq);
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
								$("#head_title").html("<b>APPROVE FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppFD/'+data.id_bq);
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
		
		
	});

</script>