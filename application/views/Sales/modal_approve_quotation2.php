
<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			Checklist komponen untuk menjadikan <b>Sales Order</b>, <u>dengan catatan Approve Action 'APPROVE'</u><br>
			<span style='color:green;'><b>MOHON CEK TERLEBIH COST SATUAN, UNTUK MENIMINALISIR KESALAHAN</b></span><br>
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
	<!-- <div class="table-responsive">
	
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" colspan='2' width='16%'>Product</th>
				<th class="text-center" width='6%'>Dim1</th>
				<th class="text-center" width='6%'>Dim2</th>
				<th class="text-center" width='6%'>Lin</th>
				<th class="text-center" width='8%'>Pre</th>
				<th class="text-center" width='15%'>Spesifikasi</th>
				<th class="text-center" width='10%'>Weight</th>
				<th class="text-center" width='7%'>Qty</th>
				<th class="text-center" width='8%'>Unit</th>
				<th class="text-center" width='10%'>Cost/Unit</th>
				<th class="text-center" width='10%'>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$SUM = 0;
			$no = 0;
			$SUM_MAT = 0;
			foreach($getDetail AS $val => $valx){
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
				$SUM_MAT += $valx['est_material'];
				echo "<tr>";
					echo "<td colspan='2'>".strtoupper($valx['id_category'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".substr($valx['series'],6,5)."</td>";
					echo "<td align='center'>".substr($valx['series'],3,2)."</td>";
					echo "<td align='left'>".spec_bq($valx['id'])."</td>";
					echo "<td align='right'>".number_format($valx['est_material'],3)." Kg</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($dataSum,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='7'><b>TOTAL OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM_MAT,3);?> Kg</b></td>
				<td colspan='3'></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		
		<?php
		$SUM_NONFRP = 0;
		if(!empty($non_frp)){
			echo "<tbody>";
				echo "<tr class='bg-blue'>";
					echo "<td class='text-left headX HeaderHr' colspan='12'><b>BQ NON FRP</b></td>";
				echo "</tr>";
				echo "<tr class='bg-blue'>";
					echo "<th class='text-center' colspan='8'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			foreach($non_frp AS $val => $valx){
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
					echo "<td colspan='8'>".$nama_acc."</td>";
					echo "<td align='right'>".number_format($qty,2)."</td>";
					echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
					echo "<td align='right'>".number_format($valx['price_total']/$qty,2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='11'><b>TOTAL BQ NON FRP</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_MAT = 0;
		if(!empty($material)){
			echo "<tbody>";
				echo "<tr class='bg-blue'>";
					echo "<td class='text-left headX HeaderHr' colspan='12'><b>MATERIAL</b></td>";
				echo "</tr>";
				echo "<tr class='bg-blue'>";
					echo "<th class='text-center' colspan='8'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			foreach($material AS $val => $valx){
				$SUM_MAT += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='8'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
					echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
					echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='11'><b>TOTAL MATERIAL</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
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
		<tfoot>
			<tr class='HeaderHr'>
				<th align='left' colspan='11'>TOTAL</th>
				<th align='right' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP, 2);?></th>
			</tr>
		</tfoot>
	</table>
	</div> -->
	<?php
	//berat pipa
	$data_berat_pipa = $this->db
		->select('
		SUM(a.est_material) AS berat_pipa, 
		SUM(a.est_harga) AS biaya_pipa,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_pipa_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_pipa_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_pipa_ga,
		SUM(a.unit_price * a.qty) AS biaya_pipa_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_pipa_profit,
		SUM(a.total_price) AS biaya_pipa_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_pipa_allow,
		SUM(a.total_price_last) AS biaya_pipa_sp
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->where('b.type_costing','pipa')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->berat_pipa:0;
	$biaya_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa:0;
	$biaya_pipa_mp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_mp:0;
	$biaya_pipa_foh = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_foh:0;
	$biaya_pipa_ga = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_ga:0;
	$biaya_pipa_dasar = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_dasar:0;
	$biaya_pipa_profit = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_profit:0;
	$biaya_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp:0;
	$kg_pipa_bp = 0;
	if($berat_pipa <> 0){
		$kg_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp/$data_berat_pipa[0]->berat_pipa:0;
	}
	$biaya_pipa_allow = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_allow:0;
	$biaya_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp:0;
	$kg_pipa_sp = 0;
	if($berat_pipa <> 0){
		$kg_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp/$data_berat_pipa[0]->berat_pipa:0;
	}
	//berat flange
	$data_berat_flange = $this->db
		->select('
		SUM(a.est_material) AS berat_flange, 
		SUM(a.est_harga) AS biaya_flange,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_flange_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_flange_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_flange_ga,
		SUM(a.unit_price * a.qty) AS biaya_flange_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_flange_profit,
		SUM(a.total_price) AS biaya_flange_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_flange_allow,
		SUM(a.total_price_last) AS biaya_flange_sp
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->where('b.type_costing','flange')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->berat_flange:0;
	$biaya_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange:0;
	$biaya_flange_mp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_mp:0;
	$biaya_flange_foh = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_foh:0;
	$biaya_flange_ga = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_ga:0;
	$biaya_flange_dasar = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_dasar:0;
	$biaya_flange_profit = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_profit:0;
	$biaya_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp:0;
	$kg_flange_bp = 0;
	if($berat_flange <> 0){
		$kg_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp/$data_berat_flange[0]->berat_flange:0;
	}
	$biaya_flange_allow = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_allow:0;
	$biaya_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp:0;
	$kg_flange_sp = 0;
	if($berat_flange <> 0){
		$kg_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp/$data_berat_flange[0]->berat_flange:0;
	}
	//berat fitting
	$data_berat_fitting = $this->db
		->select('
		SUM(a.est_material) AS berat_fitting, 
		SUM(a.est_harga) AS biaya_fitting,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_fitting_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_fitting_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_fitting_ga,
		SUM(a.unit_price * a.qty) AS biaya_fitting_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_fitting_profit,
		SUM(a.total_price) AS biaya_fitting_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_fitting_allow,
		SUM(a.total_price_last) AS biaya_fitting_sp
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->where('b.type_costing',NULL)
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->berat_fitting:0;
	$biaya_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting:0;
	$biaya_fitting_mp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_mp:0;
	$biaya_fitting_foh = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_foh:0;
	$biaya_fitting_ga = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_ga:0;
	$biaya_fitting_dasar = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_dasar:0;
	$biaya_fitting_profit = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_profit:0;
	$biaya_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp:0;
	$kg_fitting_bp = 0;
	if($berat_fitting <> 0){
		$kg_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp/$data_berat_fitting[0]->berat_fitting:0;
	}
	$biaya_fitting_allow = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_allow:0;
	$biaya_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp:0;
	$kg_fitting_sp = 0;
	if($berat_fitting <> 0){
		$kg_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp/$data_berat_fitting[0]->berat_fitting:0;
	}
	//berat bw
	$data_berat_bnw = $this->db
		->select('
		SUM(a.est_material) AS berat_bnw, 
		SUM(a.est_harga) AS biaya_bnw,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_bnw_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_bnw_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_bnw_ga,
		SUM(a.unit_price * a.qty) AS biaya_bnw_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_bnw_profit,
		SUM(a.total_price) AS biaya_bnw_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_bnw_allow,
		SUM(a.total_price_last) AS biaya_bnw_sp
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->where('b.type_costing','bw')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->berat_bnw:0;
	$biaya_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw:0;
	$biaya_bnw_mp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_mp:0;
	$biaya_bnw_foh = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_foh:0;
	$biaya_bnw_ga = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_ga:0;
	$biaya_bnw_dasar = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_dasar:0;
	$biaya_bnw_profit = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_profit:0;
	$biaya_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp:0;
	$kg_bnw_bp = 0;
	if($berat_bnw <> 0){
		$kg_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp/$data_berat_bnw[0]->berat_bnw:0;
	}
	$biaya_bnw_allow = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_allow:0;
	$biaya_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp:0;
	$kg_bnw_sp = 0;
	if($berat_bnw <> 0){
		$kg_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp/$data_berat_bnw[0]->berat_bnw:0;
	}
	
	//berat field joint
	$data_berat_field = $this->db
		->select('
		SUM(a.est_material) AS berat_field, 
		SUM(a.est_harga) AS biaya_field,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_field_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_field_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_field_ga,
		SUM(a.unit_price * a.qty) AS biaya_field_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_field_profit,
		SUM(a.total_price) AS biaya_field_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_field_allow,
		SUM(a.total_price_last) AS biaya_field_sp
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->where('b.type_costing','field')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_field = (!empty($data_berat_field))?$data_berat_field[0]->berat_field:0;
	$biaya_field = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field:0;
	$biaya_field_mp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_mp:0;
	$biaya_field_foh = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_foh:0;
	$biaya_field_ga = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_ga:0;
	$biaya_field_dasar = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_dasar:0;
	$biaya_field_profit = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_profit:0;
	$biaya_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp:0;

	$kg_field_bp = 0;
	if($berat_field <> 0){
		$kg_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp/$data_berat_field[0]->berat_field:0;
	}
	$biaya_field_allow = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_allow:0;
	$biaya_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp:0;
	
	$kg_field_sp = 0;
	if($berat_field <> 0){
		$kg_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp/$data_berat_field[0]->berat_field:0;
	}

	$berat_total = $berat_pipa + $berat_flange + $berat_fitting + $berat_bnw + $berat_field;
	$biaya_total = $biaya_pipa + $biaya_flange + $biaya_fitting + $biaya_bnw + $biaya_field;
	$biaya_total_mp = $biaya_pipa_mp + $biaya_flange_mp + $biaya_fitting_mp + $biaya_bnw_mp + $biaya_field_mp;
	$biaya_total_foh = $biaya_pipa_foh + $biaya_flange_foh + $biaya_fitting_foh + $biaya_bnw_foh + $biaya_field_foh;
	$biaya_total_ga = $biaya_pipa_ga + $biaya_flange_ga + $biaya_fitting_ga + $biaya_bnw_ga + $biaya_field_ga;
	$biaya_total_dasar = $biaya_pipa_dasar + $biaya_flange_dasar + $biaya_fitting_dasar + $biaya_bnw_dasar + $biaya_field_dasar;
	$biaya_total_profit = $biaya_pipa_profit + $biaya_flange_profit + $biaya_fitting_profit + $biaya_bnw_profit + $biaya_field_profit;
	$biaya_total_bp = $biaya_pipa_bp + $biaya_flange_bp + $biaya_fitting_bp + $biaya_bnw_bp + $biaya_field_bp;
	$kg_total_bp = 0;
	if($berat_total <> 0){
	$kg_total_bp = $biaya_total_bp / $berat_total;
	}
	$biaya_total_allow = $biaya_pipa_allow + $biaya_flange_allow + $biaya_fitting_allow + $biaya_bnw_allow + $biaya_field_allow;
	$biaya_total_sp = $biaya_pipa_sp + $biaya_flange_sp + $biaya_fitting_sp + $biaya_bnw_sp + $biaya_field_sp;
	$kg_total_sp = 0;
	if($berat_total <> 0){
	$kg_total_sp = $biaya_total_sp / $berat_total;
	}
	?>
	<!-- <div class="table-responsive"> -->
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>HARGA PENAWARAN</th>
					<th class="text-right" id='harga_penawaran2'></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>TOTAL COST</th>
					<th class="text-right" id='total_cost2'></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='6'>NET PROFIT</th>
					<th class="text-center" id='net_profit_persen2'></th>
					<th class="text-right" id='net_profit_cost2'></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th colspan='8'>A. FRP</th>
				</tr>
				<tr class='bg-blue'>
					<!-- <th class="text-left">Item</th>
					<th class="text-left">Deskripsi</th>
					<th class="text-center">Pipa</th>
					<th class="text-center">Flange</th>
					<th class="text-center">Fitting</th>
					<th class="text-center">B&W</th>
					<th class="text-center">Field Joint</th>
					<th class="text-center">Total</th> -->
					<th class="text-left" width='15%'>Item</th>
					<th class="text-left" width='25%'>Deskripsi</th>
					<th class="text-center" width='10%'>Pipa</th>
					<th class="text-center" width='10%'>Flange</th>
					<th class="text-center" width='10%'>Fitting</th>
					<th class="text-center" width='10%'>B&W</th>
					<th class="text-center" width='10%'>Field Joint</th>
					<th class="text-center" width='10%'>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Nama Resin</td>
					<td>Resin Yang digunakan</td>
					<td><?=$resin_pipa;?></td>
					<td><?=$resin_flange;?></td>
					<td><?=$resin_fitting;?></td>
					<td><?=$resin_bw;?></td>
					<td><?=$resin_field;?></td>
					<td class='text-right'>#</td>
				</tr>
				<tr>
					<td>Harga Resin/Kg</td>
					<td>Harga resin yang digunakan untuk estimasi</td>
					<td class='text-left'><?=$harga_resin_pipa;?></td>
					<td class='text-left'><?=$harga_resin_flange;?></td>
					<td class='text-left'><?=$harga_resin_fitting;?></td>
					<td class='text-left'><?=$harga_resin_bw;?></td>
					<td class='text-left'><?=$harga_resin_field;?></td>
					<td class='text-right'>#</td>
				</tr>
				<tr style='background-color: bisque;'>
					<td>Berat Material</td>
					<td></td>
					<td class='text-right'><?=number_format($berat_pipa,3);?></td>
					<td class='text-right'><?=number_format($berat_flange,3);?></td>
					<td class='text-right'><?=number_format($berat_fitting,3);?></td>
					<td class='text-right'><?=number_format($berat_bnw,3);?></td>
					<td class='text-right'><?=number_format($berat_field,3);?></td>
					<td class='text-right'><?=number_format($berat_total,3);?></td>
				</tr>
				<tr>
					<td>Biaya Material</td>
					<td>Biaya material sesuai dengan total kebutuhan material yang diestimasi engineering</td>
					<td class='text-right'><?=number_format($biaya_pipa,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw,3);?></td>
					<td class='text-right'><?=number_format($biaya_field,3);?></td>
					<td class='text-right'><?=number_format($biaya_total,3);?></td>
				</tr>
				<tr>
					<td>Biaya MP & Utilities</td>
					<td>Biaya direct labour dan indirect labour, depresiasi mesin, biaya mold mandrill dan consumable produksi</td>
					<td class='text-right'><?=number_format($biaya_pipa_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_mp,3);?></td>
				</tr>
				<tr>
					<td>Biaya FOH</td>
					<td>Biaya depresiasi FOH dan consumable FOH</td>
					<td class='text-right'><?=number_format($biaya_pipa_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_foh,3);?></td>
				</tr>
				<tr>
					<td>Biaya General Admin</td>
					<td>Biaya gaji non produksi, tagihan rutin (listrik, air, telp, internet dll), sales dan general admin</td>
					<td class='text-right'><?=number_format($biaya_pipa_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_ga,3);?></td>
				</tr>
				<tr>
					<td>Biaya Dasar</td>
					<td>Total biaya</td>
					<td class='text-right'><?=number_format($biaya_pipa_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_dasar,3);?></td>
				</tr>
				<tr>
					<td>Profit</td>
					<td>Nilai Profit</td>
					<td class='text-right'><?=number_format($biaya_pipa_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_profit,3);?></td>
				</tr>
				<tr>
					<td>Bottom Price</td>
					<td>Biaya Dasar + Profit</td>
					<td class='text-right'><?=number_format($biaya_pipa_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_bp,3);?></td>
				</tr>
				<tr style='background-color: #d7d7d7;'>
					<td>$/Kg (dari Bottom Price)</td>
					<td>Bottom price / Berat material</td>
					<td class='text-right'><?=number_format($kg_pipa_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_flange_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_fitting_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_bnw_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_field_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_total_bp,3);?></td>
				</tr>
				<tr>
					<td>Allowance</td>
					<td>Persentase untuk ruang negosiasi sales</td>
					<td class='text-right'><?=number_format($biaya_pipa_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_allow,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td>Selling Price</td>
					<td>Bottom price + Allowance</td>
					<td class='text-right'><?=number_format($biaya_pipa_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_sp,3);?></td>
				</tr>
				<tr style='background-color: #d7d7d7;'>
					<td>$/Kg (Selling Price)</td>
					<td>Selling Price / Berat material</td>
					<td class='text-right'><?=number_format($kg_pipa_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_flange_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_fitting_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_bnw_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_field_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_total_sp,3);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<?php
			//BAUT
			$data_berat_baut = $this->db
					->select('
					SUM(a.fumigasi) AS harga_baut, 
					SUM(a.price - a.fumigasi) AS profit_baut,
					SUM(a.price) AS bp_baut,
					SUM(a.price_total - a.price) AS allow_baut,
					SUM(a.price_total) AS sp_baut
					')
				->from('laporan_revised_etc a')
				->where('a.category','baut')
				->where('a.revised_no',$revised_no)
				->where('a.id_bq',$id_bq)
				->get()
				->result();
			$harga_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->harga_baut:0;
			$profit_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->profit_baut:0;
			$bp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->bp_baut:0;
			$allow_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->allow_baut:0;
			$sp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->sp_baut:0;

			//GASKET
			$data_berat_gasket = $this->db
								->select('
								SUM(a.fumigasi) AS harga_gasket, 
								SUM(a.price - a.fumigasi) AS profit_gasket,
								SUM(a.price) AS bp_gasket,
								SUM(a.price_total - a.price) AS allow_gasket,
								SUM(a.price_total) AS sp_gasket
								')
								->from('laporan_revised_etc a')
								->where('a.category','gasket')
								->where('a.revised_no',$revised_no)
								->where('a.id_bq',$id_bq)
								->get()
								->result();
			$harga_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->harga_gasket:0;
			$profit_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->profit_gasket:0;
			$bp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->bp_gasket:0;
			$allow_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->allow_gasket:0;
			$sp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->sp_gasket:0;

			//PLATE
			$data_berat_plate = $this->db
					->select('
					SUM(a.fumigasi) AS harga_plate, 
					SUM(a.price - a.fumigasi) AS profit_plate,
					SUM(a.price) AS bp_plate,
					SUM(a.price_total - a.price) AS allow_plate,
					SUM(a.price_total) AS sp_plate
					')
				->from('laporan_revised_etc a')
				->where('a.category','plate')
				->where('a.revised_no',$revised_no)
				->where('a.id_bq',$id_bq)
				->get()
				->result();
			$harga_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->harga_plate:0;
			$profit_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->profit_plate:0;
			$bp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->bp_plate:0;
			$allow_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->allow_plate:0;
			$sp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->sp_plate:0;

			//LAINNYA
			$data_berat_lainnya = $this->db
					->select('
					SUM(a.fumigasi) AS harga_lainnya, 
					SUM(a.price - a.fumigasi) AS profit_lainnya,
					SUM(a.price) AS bp_lainnya,
					SUM(a.price_total - a.price) AS allow_lainnya,
					SUM(a.price_total) AS sp_lainnya
					')
				->from('laporan_revised_etc a')
				->where('a.category','lainnya')
				->where('a.revised_no',$revised_no)
				->where('a.id_bq',$id_bq)
				->get()
				->result();
			$harga_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->harga_lainnya:0;
			$profit_lainnya = (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->profit_lainnya:0;
			$bp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->bp_lainnya:0;
			$allow_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->allow_lainnya:0;
			$sp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->sp_lainnya:0;

			//MATERIAL
			$data_berat_material = $this->db
					->select('
					SUM(a.fumigasi) AS harga_material, 
					SUM(a.price - a.fumigasi) AS profit_material,
					SUM(a.price) AS bp_material,
					SUM(a.price_total - a.price) AS allow_material,
					SUM(a.price_total) AS sp_material
					')
				->from('laporan_revised_etc a')
				->where('a.category','aksesoris')
				->where('a.revised_no',$revised_no)
				->where('a.id_bq',$id_bq)
				->get()
				->result();
			$harga_material 	= (!empty($data_berat_material))?$data_berat_material[0]->harga_material:0;
			$profit_material = (!empty($data_berat_material))?$data_berat_material[0]->profit_material:0;
			$bp_material 	= (!empty($data_berat_material))?$data_berat_material[0]->bp_material:0;
			$allow_material 	= (!empty($data_berat_material))?$data_berat_material[0]->allow_material:0;
			$sp_material 	= (!empty($data_berat_material))?$data_berat_material[0]->sp_material:0;

			$harga_total 	= $harga_baut + $harga_gasket + $harga_plate + $harga_lainnya + $harga_material;
			$profit_total 	= $profit_baut + $profit_gasket + $profit_plate + $profit_lainnya + $profit_material;
			$bp_total 		= $bp_baut + $bp_gasket + $bp_plate + $bp_lainnya + $bp_material;
			$allow_total 	= $allow_baut + $allow_gasket + $allow_plate + $allow_lainnya + $allow_material;
			$sp_total 		= $sp_baut + $sp_gasket + $sp_plate + $sp_lainnya + $sp_material;

			?>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th colspan='8'>B. NON FRP</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-left">Item</th>
					<th class="text-left">Deskripsi</th>
					<th class="text-center">Material</th>
					<th class="text-center">Bolt & Nut</th>
					<th class="text-center">Gasket</th>
					<th class="text-center">Plate</th>
					<th class="text-center">Lainnya</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Harga Costing</td>
					<td>Harga yang didapat dari supplier</td>
					<td class="text-right"><?=number_format($harga_material,3);?></td>
					<td class="text-right"><?=number_format($harga_baut,3);?></td>
					<td class="text-right"><?=number_format($harga_gasket,3);?></td>
					<td class="text-right"><?=number_format($harga_plate,3);?></td>
					<td class="text-right"><?=number_format($harga_lainnya,3);?></td>
					<td class="text-right"><?=number_format($harga_total,3);?></td>
				</tr>
				<tr>
					<td>Profit</td>
					<td>Nilai Profit</td>
					<td class="text-right"><?=number_format($profit_material,3);?></td>
					<td class="text-right"><?=number_format($profit_baut,3);?></td>
					<td class="text-right"><?=number_format($profit_gasket,3);?></td>
					<td class="text-right"><?=number_format($profit_plate,3);?></td>
					<td class="text-right"><?=number_format($profit_lainnya,3);?></td>
					<td class="text-right"><?=number_format($profit_total,3);?></td>
				</tr>
				<tr>
					<td>Bottom Price</td>
					<td>Biaya Dasar + Profit</td>
					<td class="text-right"><?=number_format($bp_material,3);?></td>
					<td class="text-right"><?=number_format($bp_baut,3);?></td>
					<td class="text-right"><?=number_format($bp_gasket,3);?></td>
					<td class="text-right"><?=number_format($bp_plate,3);?></td>
					<td class="text-right"><?=number_format($bp_lainnya,3);?></td>
					<td class="text-right"><?=number_format($bp_total,3);?></td>
				</tr>
				<tr>
					<td>Allowance</td>
					<td>Persentase untuk ruang negosiasi sales</td>
					<td class="text-right"><?=number_format($allow_material,3);?></td>
					<td class="text-right"><?=number_format($allow_baut,3);?></td>
					<td class="text-right"><?=number_format($allow_gasket,3);?></td>
					<td class="text-right"><?=number_format($allow_plate,3);?></td>
					<td class="text-right"><?=number_format($allow_lainnya,3);?></td>
					<td class="text-right"><?=number_format($allow_total,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td>Selling Price</td>
					<td>Bottom price + Allowance</td>
					<td class="text-right"><?=number_format($sp_material,3);?></td>
					<td class="text-right"><?=number_format($sp_baut,3);?></td>
					<td class="text-right"><?=number_format($sp_gasket,3);?></td>
					<td class="text-right"><?=number_format($sp_plate,3);?></td>
					<td class="text-right"><?=number_format($sp_lainnya,3);?></td>
					<td class="text-right"><?=number_format($sp_total,3);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<?php
			$data_packing = $this->db
						->select('
						SUM(a.price_total) AS harga_packing
						')
						->from('laporan_revised_etc a')
						->where('a.category','packing')
						->where('a.revised_no',$revised_no)
						->where('a.id_bq',$id_bq)
						->get()
						->result();
			$harga_packing = (!empty($data_packing))?$data_packing[0]->harga_packing:0;

			$data_transport = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_transport
							  ')
							->from('laporan_revised_etc a')
							->where("(a.category = 'export' OR a.category = 'lokal')")
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
			$harga_transport = (!empty($data_transport))?$data_transport[0]->harga_transport:0;

			$data_engine = $this->db
								->select('
								SUM(a.price_total) AS harga_engine
								')
								->from('laporan_revised_etc a')
								->where('a.category','engine')
								->where('a.revised_no',$revised_no)
								->where('a.id_bq',$id_bq)
								->get()
								->result();
			$harga_engine = (!empty($data_engine))?$data_engine[0]->harga_engine:0;

			$data_other = $this->db
								->select('
								SUM(a.price_total) AS harga_other
								')
								->from('laporan_revised_etc a')
								->where('a.category','other')
								->where('a.revised_no',$revised_no)
								->where('a.id_bq',$id_bq)
								->get()
								->result();
			$harga_other = (!empty($data_other))?$data_other[0]->harga_other:0;
			?>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th colspan='8'>C. PACKING & TRANSPORT</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-left" colspan='7'>Kategori</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Packing</td>
					<td class="text-right"><?=number_format($harga_packing,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Transportasi</td>
					<td class="text-right"><?=number_format($harga_transport,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Engineering</td>
					<td class="text-right"><?=number_format($harga_engine,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Other</td>
					<td class="text-right"><?=number_format($harga_other,3);?></td>
				</tr>
				<tr>
					<td colspan='7'>&nbsp;</td>
					<td></td>
				</tr>
			</tbody>
			<?php
			$harga_penawaran 	= $biaya_total_sp + $sp_total + $harga_packing + $harga_transport + $harga_engine + $harga_other;
			$harga_cost			= $biaya_total_dasar + $harga_total + $harga_packing + $harga_transport + $harga_engine + $harga_other;
			$net_profit			= $harga_penawaran - $harga_cost;
			$net_persent		= 0;
			if($net_profit > 0 AND $harga_penawaran > 0){
				$net_persent		= $net_profit / $harga_penawaran * 100;
			}
			?>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-left" colspan='7'>HARGA PENAWARAN</th>
					<th class="text-right" id='harga_penawaran'><?=number_format($harga_penawaran,3);?></th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-left" colspan='7'>TOTAL COST</th>
					<th class="text-right" id='total_cost'><?=number_format($harga_cost,3);?></th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-left" colspan='6'>NET PROFIT</th>
					<th class="text-right" id='net_profit_persen'><?=number_format($net_persent,2);?> %</th>
					<th class="text-right" id='net_profit_cost'><?=number_format($net_profit,3);?></th>
				</tr>
			</thead>
		</table>
	<!-- </div> -->
</div>
<style>
	
</style>
<script>
	swal.close();
	
	$("#chk_all").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
	
	$(document).ready(function(){
		var harga_penawaran 	= $('#harga_penawaran').html();
		var total_cost 			= $('#total_cost').html();
		var net_profit_persen 	= $('#net_profit_persen').html();
		var net_profit_cost 	= $('#net_profit_cost').html();

		$('#harga_penawaran2').html(harga_penawaran);
		$('#total_cost2').html(total_cost);
		$('#net_profit_persen2').html(net_profit_persen);
		$('#net_profit_cost2').html(net_profit_cost);

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
						url			: base_url+active_controller+'/AppCostNew_Report/'+bF,
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
								window.location.href = base_url + active_controller+'/approve_quotation';
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