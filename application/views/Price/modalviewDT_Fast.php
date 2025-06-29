
<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body"> 
		<div class='note'>
			<p>
				<strong>Info!</strong><br> 
				(NEW) <b>Update man hours</b>, otomatis update.<br>
				<span><i class='fa fa-level-down text-success'></i></span> Update <b>cycletime</b> yang kosong.<br>
			</p>
		</div>
		<button type='button' id='agusDetail' class='btn btn-sm bg-teal' data-id_bq='<?=$id_bq;?>' style='float:right;'>All Component</button>
		<a href="<?php echo site_url('price/excel_price_project/'.$id_bq) ?>" target='_blank' class="btn btn-sm btn-info" style='float:right; margin-right:10px;'>
			<i class="fa fa-file-excel-o"></i> Excel
		 </a>
		<a href="<?php echo site_url('price/print_project_costing/'.$id_bq) ?>" target='_blank' class="btn btn-sm btn-primary" id='btn-add' style='float:right; margin-right:10px;'>
			<i class="fa fa-print"></i> Print Detail
		 </a>
		<button type='button' id='update_price_mat' class='btn btn-sm btn-danger' style='float:right; margin-right:10px;' data-id_bq='<?=$id_bq;?>'>Update Price <b><u>Material Add</u></b> From Master</button>
		<button type='button' id='update_price_bq' class='btn btn-sm btn-warning' style='float:right; margin-right:10px;' data-id_bq='<?=$id_bq;?>'>Update Price <b><u>Non FRP</u></b> From Master</button>
		<button type='button' id='update_price' class='btn btn-sm btn-success' style='float:right; margin-right:10px;' data-id_bq='<?=$id_bq;?>'>Update Price <b><u>Material Pipa Fitting</u></b> From Master</button>
		<!-- <button type='button' id='update_price_ipp' class='btn btn-sm bg-purple' style='float:right; margin-right:10px;' data-id_bq='<?=$id_bq;?>'>Update Price <b><u>Material Project</u></b></button> -->
		<br><br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<!--
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='3%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='18%'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>Material Est (Kg)</th>
					<th class="text-center" style='vertical-align:middle;' width='4%'>Material Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Process Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Direct Labour</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Indirect Labour</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Consu- mable</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Machine Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Mould Mandrill</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Cnsmble FOH</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>Depresiasi FOH</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>By Gaji Non Pro</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>By Non Pro</th>
					<th class='text-center' style='vertical-align:middle;' width='4%'>By Rutin Bln</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Detail</th>
				</tr>
				-->
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='12%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Material Est (Kg)</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Material Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='10%'>Process Cost</th>
					<th class='text-center' style='vertical-align:middle;' width='10%'>SUM_COGS</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Detail</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Man Hours</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$SUM_BERAT = 0;
					$SUM_PRICE = 0;
					$SUM_PROCESS = 0;
					$SUM_COGS = 0;
					$No = 0;
					if(!empty($result)){
						foreach($result AS $val => $valx){
							$No++;
							$spaces = "";
							$bgwarna = 'bg-blue';

							$ID 						= $valx['id'];
							$id_category 				= $valx['id_category'];
							$id_milik 					= $valx['id'];
							$length 					= $valx['length'];
							$id_product 				= $valx['id_product'];
							$qty 						= $valx['qty'];
							$man_power					= $valx['man_power'];
							$man_hours					= $valx['man_hours'];
							$id_mesin					= $valx['id_mesin'];
							$total_time					= $valx['total_time'];

							$SUMMARY = getEstimasi_Product($id_milik,$id_category);
							
							$TotalBerat	= (!empty($SUMMARY['est_mat']))?$SUMMARY['est_mat'] * $qty:0;
							$SUM_BERAT 	+= $TotalBerat;
							
							$TotalPrice	= (!empty($SUMMARY['est_price']))?$SUMMARY['est_price'] * $qty:0;
							$SUM_PRICE += $TotalPrice;

							$direct_labour 				= $man_hours * $valx['pe_direct_labour'] * $qty;
							$indirect_labour 			= $man_hours * $valx['pe_indirect_labour'] * $qty;
							$machine 					= $total_time * $valx['pe_machine'] * $qty;
							$mould_mandrill 			= $valx['pe_mould_mandrill'] * $qty;
							$consumable 				= $TotalBerat * $valx['pe_consumable'];

							$cost_process 				= $direct_labour + $indirect_labour + $machine + $mould_mandrill + $consumable;

							$foh_consumable 			= ($cost_process + $TotalPrice) * ($valx['pe_foh_consumable']/100);
							$foh_depresiasi 			= ($cost_process + $TotalPrice) * ($valx['pe_foh_depresiasi']/100);
							$biaya_gaji_non_produksi 	= ($cost_process + $TotalPrice) * ($valx['pe_biaya_gaji_non_produksi']/100);
							$biaya_non_produksi 		= ($cost_process + $TotalPrice) * ($valx['pe_biaya_non_produksi']/100);
							$biaya_rutin_bulanan 		= ($cost_process + $TotalPrice) * ($valx['pe_biaya_rutin_bulanan']/100);

							$TotalCost		= $direct_labour + $indirect_labour + $machine + $mould_mandrill + $consumable + $foh_consumable + $foh_depresiasi + $biaya_gaji_non_produksi + $biaya_non_produksi + $biaya_rutin_bulanan;
							$SUM_PROCESS 	+= $TotalCost;
							
							$COGS 			= $TotalCost + $TotalPrice;
							$SUM_COGS 		+= $COGS;
							
							if($id_category == 'pipe' OR $id_category == 'pipe slongsong'){
								$lengthX = (floatval($length));
							}
							else{
								$lengthX = (floatval($length));
							}
							
							echo "<tr>";
								echo "<td align='center'>".$No."</td>";
								echo "<td align='left'>".$spaces."".strtoupper($id_category)."</td>";
								echo "<td align='left'>".$spaces."".spec_bq($id_milik)."</td>";
								echo "<td align='center'><span class='badge ".$bgwarna."'>".$qty."</span></td>";
								echo "<td align='left'>".$id_product."</span></td>";
								echo "<td align='right'>".number_format($TotalBerat, 3)."</span></td>";
								echo "<td align='right'>".number_format($TotalPrice, 2)."</span></td>";
								echo "<td align='right'><a class='detail_process_cost2' style='cursor:pointer;' 
															data-id_milik='".$id_milik."' 
															data-id_bq='".str_replace('BQ-','',$id_bq)."' 
															data-id_product='".$id_product."'
															data-direct_labour='".$direct_labour."'
															data-indirect_labour='".$indirect_labour."'
															data-consumable='".$consumable."'
															data-machine='".$machine."'
															data-mould_mandrill='".$mould_mandrill."'
															data-foh_consumable='".$foh_consumable."'
															data-foh_depresiasi='".$foh_depresiasi."'
															data-biaya_gaji_non_produksi='".$biaya_gaji_non_produksi."'
															data-biaya_non_produksi='".$biaya_non_produksi."'
															data-biaya_rutin_bulanan='".$biaya_rutin_bulanan."'
															data-man_power='".$man_power."'
															data-man_hours='".$man_hours."'
															data-id_mesin='".$id_mesin."'
															data-total_time='".$total_time."'
															>".number_format($TotalCost, 2)."</a></td>";
								echo "<td align='right'>".number_format($COGS, 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['direct_labour'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['indirect_labour'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['consumable'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['machine'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['mould_mandrill'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['foh_consumable'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['foh_depresiasi'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['biaya_gaji_non_produksi'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['biaya_non_produksi'], 2)."</span></td>";
								// echo "<td align='right'>".number_format($valx['biaya_rutin_bulanan'], 2)."</span></td>";
								echo "<td align='center'>";
									if($tanda_cost == 'cost_control'){
										echo "<button class='btn btn-sm btn-success' id='MatDetailCost' title='Detail Cost ".$id_milik."' data-id_product='".$id_product."' data-id_milik='".$id_milik."'><i class='fa fa-eye'></i></button>";
										echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/printCostControl/'.$id_product.'/'.$id_milik.'/'.$id_bq)."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
									}
									else{
										echo "<button class='btn btn-sm btn-warning' id='MatDetail' title='Detail BQ ".$id_milik."' data-id_product='".$id_product."' data-id_milik='".$id_milik."' data-id_bq='".$id_bq."' data-qty='".$qty."' data-length='".$lengthX."'><i class='fa fa-eye'></i></button>";
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success update_cycle' title='Update Cycle Time' data-id_milik='".$id_milik."' data-id_bq='".$id_bq."'><i class='fa fa-level-down'></i></button>";
									}
								echo "</td>";	
								echo "<td align='center'>";
								echo "<input type='text' class='form-control input-sm text-right update_mh autoNumeric2 text-bold' data-id_bq='".$id_bq."' data-id_milik='".$id_milik."' data-manpower='".$man_power."' value='".$man_hours."' >";
								echo "<span class='sts_mh'><i><span class='status_mh'>Status</span></i><span>";
								echo "</td>";						
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='10'>Tidak ada product yang ditampilkan</td>";
						echo "</tr>";
					}
				?>
				<tr>
					<th class="text-center" colspan='5' style='vertical-align:middle;'>Total</th>
					<th class="text-right"><?= number_format($SUM_BERAT, 3);?></th>
					<th class="text-right"><?= number_format($SUM_PRICE, 2);?></th>
					<?php
					echo "<th class='text-right'>".number_format($SUM_PROCESS, 2)."</th>";
					echo "<th class='text-right'>".number_format($SUM_COGS, 2)."</th>";
					?>
					<!--
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-center"></th>
					-->
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>B. MUR BAUT</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='5%'>ID Program</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='5%'>Qty</th>
					<th class="text-center" width='5%'>Unit</th>
					<th class="text-center" width='17%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
					<th class="text-center" width='7%'>Expired Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail3)){
						foreach($detail3 AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							$SUM += $valx['total_price'];
							echo "<tr class='header3_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='center'>".$valx['id_material']."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
								$EXPIRED = (!empty($valx['expired_date']))?date('d-M-Y',strtotime($valx['expired_date'])):'';
								echo "<td align='center'>".$EXPIRED."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='7'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
							echo "<td align='right'></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>C. PLATE</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='5%'>ID Program</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='7%'>Lebar (mm)</th>
					<th class="text-center" width='7%'>Panjang (mm)</th>
					<th class="text-center" width='5%'>Qty</th>
					<th class="text-center" width='7%'>Berat (kg)</th>
					<th class="text-center" width='5%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
					<th class="text-center" width='7%'>Expired Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail4)){
						foreach($detail4 AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							$SUM += $valx['total_price'];
							echo "<tr class='header4_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='center'>".$valx['id_material']."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='right'>".number_format($valx['berat'],3)."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
								$EXPIRED = (!empty($valx['expired_date']))?date('d-M-Y',strtotime($valx['expired_date'])):'';
								echo "<td align='center'>".$EXPIRED."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='10'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
							echo "<td align='right'></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='12'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>D. GASKET</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='5%'>ID Program</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='7%'>Lebar (mm)</th>
					<th class="text-center" width='7%'>Panjang (mm)</th>
					<th class="text-center" width='5%'>Qty</th>
					<th class="text-center" width='5%'>Unit</th>
					<th class="text-center" width='7%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
					<th class="text-center" width='7%'>Expired Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail4g)){
						foreach($detail4g AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							$SUM += $valx['total_price'];
							echo "<tr class='header4g_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='center'>".$valx['id_material']."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
								$EXPIRED = (!empty($valx['expired_date']))?date('d-M-Y',strtotime($valx['expired_date'])):'';
								echo "<td align='center'>".$EXPIRED."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='10'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
							echo "<td align='right'></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='12'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>E. LAINNYA</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='5%'>ID Program</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='5%'>Qty</th>
					<th class="text-center" width='5%'>Unit</th>
					<th class="text-center" width='17%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
					<th class="text-center" width='7%'>Expired Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail5)){
						foreach($detail5 AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							$SUM += $valx['total_price'];
							echo "<tr class='header5_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='center'>".$valx['id_material']."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
								$EXPIRED = (!empty($valx['expired_date']))?date('d-M-Y',strtotime($valx['expired_date'])):'';
								echo "<td align='center'>".$EXPIRED."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='7'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
							echo "<td align='right'></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-info">
	<div class="box-header">
		<label>F. MATERIAL</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center" width='7%'>Unit</th>
					<th class="text-center" width='17%'>Keterangan</th>
					<th class="text-center" width='7%'>Unit Price</th>
					<th class="text-center" width='7%'>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					$SUM = 0;
					if(!empty($detail2)){
						foreach($detail2 AS $val => $valx){ $id++;
							$SUM += $valx['total_price'];
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>"; 
								echo "<td align='left'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['id_material']))."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
								echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
								echo "<td align='right'>".number_format($valx['total_price'],2)."</td>";
							echo "</tr>";
						}
						echo "<tr>";
							echo "<td align='center' colspan='6'></td>";
							echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td colspan='7'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function(){
		swal.close();
		let myName = 'Sinto';
		console.log(`My Name is ${myName}`);
		$(".autoNumeric2").autoNumeric('init', {mDec: '2', aPad: false});
		$('.sts_mh').hide()
	});
		
	$(document).on('click', '#detailDT', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url + active_controller+'/modalDetailDT/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '#MatDetailCost', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url + active_controller+'/modalDetailMatCost/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '#update_price_ipp', function(e){
		e.preventDefault();
		loading_spinner();
		let id_bq = $(this).data('id_bq');
		$("#head_title2").html("<b>UPDATE PRICE THIS IPP</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/update_harga_this_ipp/'+id_bq,
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 5000
				});
			}
		});
	});



</script>