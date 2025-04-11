<div class="box box-primary">
    <div class="box-body">
        <br>
		<?php
		if(strtolower($kebutuhan) == 'project'){
			?>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Sumber</b></label>
				<div class='col-sm-4'>              
					: &nbsp;&nbsp;<?=$no_ipp2;?>
				</div>
				<label class='label-control col-sm-2'><b>Tanggal Dibutuhkan</b></label>
				<div class='col-sm-4'>              
				: &nbsp;&nbsp;<?=(!empty($tgl_butuh) AND $tgl_butuh != '0000-00-00')?date('d F Y', strtotime($tgl_butuh)):'-';?>
				</div>
				<!-- <label class='label-control col-sm-2'><b>Asal Permintaan</b></label>
				<div class='col-sm-4'>              
					<?=$no_ipp;?>
				</div> -->
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Project</b></label>
				<div class='col-sm-4'>              
				: &nbsp;&nbsp;<?=strtoupper($project);?>
				</div>
				<!-- <label class='label-control col-sm-2'><b>Kebutuhan</b></label>
				<div class='col-sm-4'>              
					<?=$kebutuhan;?>
				</div> -->
			</div>
			<br>
			<input type='hidden' id='no_ipp' name='no_ipp' value='<?=$no_ipp2;?>'>
			<input type='hidden' id='tanda' name='tanda' value='<?=$tanda;?>'>
			<input type='hidden' id='id_user' name='id_user' value='<?=$id_user;?>'>
			<input type='hidden' id='tgl_butuh' name='tgl_butuh' value='<?=$tgl_butuh;?>'>
		
			<table class="table table-bordered table-striped" id="my-grid3" width='100%'>	
				<thead>
					<tr class='bg-blue'>
						<th colspan='14'>MATERIAL</th>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" style='vertical-align:middle;' rowspan='2'>No</th>
						<th class="text-center" style='vertical-align:middle;' rowspan='2'>Material Name</th>
						<th class="text-center" style='vertical-align:middle;' rowspan='2'>Catgeory</th>
						<th class="text-center" style='vertical-align:middle;' colspan='4'>Ambil Dari Stock Free</th>
						<th class="text-center" style='vertical-align:middle;' colspan='4'>PR Material</th>
						<th class="text-center" style='vertical-align:middle;' colspan='3'>Approval</th>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center mid" style='width:6% !important;'>Estimasi</th>
						<th class="text-center mid" style='width:6% !important;'>Stock Free</th>
						<th class="text-center mid" style='width:6% !important;'>Use Stock</th>
						<th class="text-center mid" style='width:6% !important;'>Sisa Stock Free</th>

						<th class="text-center mid" style='width:6% !important;'>Min Stock</th>
						<th class="text-center mid" style='width:6% !important;'>Max Stock</th>
						<!-- <th class="text-center mid" style='width:6% !important;'>Kg/bulan</th> -->
						<th class="text-center mid" style='width:6% !important;'>Min Order</th>
						<th class="text-center mid" style='width:6% !important;'>Qty PR</th>

						<!-- <th class="text-center mid" style='width:6% !important;'>Tanggal Dibutuhkan</th> -->
						<th class="text-center mid" style='width:6% !important;' rowspan='2'>Rev Qty</th>
						<!-- <th class="text-center mid" style='width:10% !important;' rowspan='2'>Keterangan</th> -->
						<th class="text-center mid" style='width:7% !important;' rowspan='2'>Reject</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no  = 0;
					foreach($result AS $val => $valx){ $no++;
						$safetystock 	= get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']);
						$kg_per_bulan 	= get_max_field('raw_materials', 'kg_per_bulan', 'id_material', $valx['id_material']);
						$max_stock 		= get_max_field('raw_materials', 'max_stock', 'id_material', $valx['id_material']);
						
						$reorder 		= ($safetystock/30) * $kg_per_bulan;
						$max_stock2 	= ($max_stock/30) * $kg_per_bulan;
						echo "<tr>";
							echo "<td align='center'>".$no."</td>";
							echo "<td align='left'>".$valx['nm_material']."</td>";
							echo "<td align='left'>".get_name('raw_materials', 'nm_category', 'id_material', $valx['id_material'])."</td>";
							
							echo "<td align='right'>".number_format($valx['jumlah_mat'],2)."</td>";
							echo "<td align='right'>".number_format($valx['qty_stock'] - $valx['qty_booking'],2)."</td>";
							echo "<td align='right'>".number_format($valx['use_stock'],2)."</td>";
							echo "<td align='right'>".number_format($valx['qty_stock'] - $valx['qty_booking'] - $valx['use_stock'],2)."</td>";

							echo "<td align='right'>".number_format($reorder,2)."</td>";
							echo "<td align='right'>".number_format($max_stock2,2)."</td>";
							// echo "<td align='right'>".number_format($kg_per_bulan,2)."</td>";
							echo "<td align='right'>".number_format($valx['moq_m'])."</td>";
							echo "<td align='right'>".number_format($valx['qty_request'])."</td>";
							// echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
							echo "<td align='center'>
								<input type='hidden' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
								<input type='hidden' name='detail[".$no."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='detail[".$no."][idmaterial]' value='".$valx['idmaterial']."'>
								<input type='hidden' name='detail[".$no."][nm_material]' value='".$valx['nm_material']."'>
								<input type='hidden' name='detail[".$no."][moq]' value='".$valx['moq_m']."'>
								<input type='hidden' name='detail[".$no."][qty_request]' value='".$valx['qty_request']."'>
								<input type='hidden' name='detail[".$no."][reorder_point]' value='".$valx['reorder_point']."'>
								<input type='hidden' name='detail[".$no."][book_per_month]' value='".$valx['book_per_month']."'>
								<input type='hidden' name='detail[".$no."][sisa_avl]' value='".$valx['sisa_avl']."'>
								<input type='hidden' name='detail[".$no."][tanggal]' value='".$valx['tanggal']."'>
								<input type='text' name='detail[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-right maskM' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							// echo "<td align='center'>
							// 		<input type='hidden' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
							// 		</td>";
							echo "<td align='center'>
									<button type='button'class='btn btn-sm btn-success appPR' title='Approve PR' data-nomor='".$no."' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-check'></i></button>
									<button type='button'class='btn btn-sm btn-danger rejectPR' title='Reject PR' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-close'></i></button>
									</td>";
						echo "</tr>";
					}
					if(empty($result)){
						echo "<tr>";
							echo "<td colspan='14'>Tidak ada data.</td>";
						echo "</tr>";
					}
					?>
				</tbody>
					<tr>
						<td colspan='13'>
							<?php
								if(!empty($result)){
									echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Approve Material','id'=>'app_pr')).' ';
								}
							?>
						</td>
					</tr>
				<thead>
					<tr class='bg-blue'>
						<th colspan='14'>NON FRP</th>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center no-sort">#</th>
						<th class="text-center" colspan='6'>Material Name</th>
						<th class="text-center" colspan='2'>Category</th>
						<th class="text-center">Unit</th>
						<th class="text-center">Qty PR</th>
						
						<!-- <th class="text-center">Tanggal Dibutuhkan</th> -->
						<th class="text-center">Rev Qty</th>
						<!-- <th class="text-center">Keterangan</th> -->
						<th class="text-center">Reject</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no  = 0;
					if(!empty($non_frp)){
						foreach($non_frp AS $val => $valx){ $no++;
							
							$satuan = $valx['satuan'];
							if($valx['idmaterial'] == '2'){
								$satuan = '1';
							}

							$nm_acc = get_name_acc($valx['id_material']);
							if($nm_acc == 'Not found'){
								$nm_acc = strtoupper($valx['nm_material']);
							}
							
							echo "<tr>";
								echo "<td align='center'>".$no."</td>";
								echo "<td align='left' colspan='6'>".$nm_acc."</td>";
								echo "<td align='left' colspan='2'>".strtoupper(get_name('accessories_category', 'category', 'id', $valx['idmaterial']))."</td>";
								echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
								echo "<td align='right'>".number_format($valx['purchase'])."</td>";
								// echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
								
								echo "<td align='center'>
									<input type='hidden' name='detail_acc[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
									<input type='hidden' name='detail_acc[".$no."][id_material]' value='".$valx['id_material']."'>
									<input type='hidden' name='detail_acc[".$no."][idmaterial]' value='".$valx['idmaterial']."'>
									<input type='hidden' name='detail_acc[".$no."][nm_material]' value='".$valx['nm_material']."'>
									<input type='hidden' name='detail_acc[".$no."][moq]' value='".$valx['moq']."'>
									<input type='hidden' name='detail_acc[".$no."][qty_request]' value='".$valx['qty_request']."'>
									<input type='hidden' name='detail_acc[".$no."][reorder_point]' value='".$valx['reorder_point']."'>
									<input type='hidden' name='detail_acc[".$no."][book_per_month]' value='".$valx['book_per_month']."'>
									<input type='hidden' name='detail_acc[".$no."][sisa_avl]' value='".$valx['sisa_avl']."'>
									<input type='hidden' name='detail_acc[".$no."][tanggal]' value='".$valx['tanggal']."'>
									<input type='hidden' name='detail_acc[".$no."][satuan]' value='".$valx['satuan']."'>
									<input type='hidden' name='detail_acc[".$no."][id]' value='".$valx['id']."'>
									<input type='text' name='detail_acc[".$no."][qty_revisi]' id='tot_rev_acc_".$no."' class='form-control input-sm text-right maskM' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
								// echo "<td align='center'>
								// 		<input type='text' name='detail_acc[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
								// 		</td>";
								// <button type='button'class='btn btn-sm btn-success appPR_acc' title='Approve PR' data-nomor='".$no."' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-check'></i></button>

								echo "<td align='center'>
										<button type='button'class='btn btn-sm btn-danger rejectPR_acc' title='Reject PR' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-close'></i></button>
										</td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr><td colspan='9'>Data not found</td></tr>";
					}
					?>
					<tr>
						<td colspan='13'>
							<?php
								if(!empty($non_frp)){
									echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Approve Accessories','id'=>'app_pr_acc')).' ';
								}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
		}

		//BASEON STOCK
		if(strtolower($kebutuhan) <> 'project'){
			?>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Asal Permintaan</b></label>
				<div class='col-sm-4'>              
					: &nbsp;&nbsp;<?=$no_ipp;?>
				</div>
				<label class='label-control col-sm-2'><b>Tanggal Dibutuhkan</b></label>
				<div class='col-sm-4'>              
				: &nbsp;&nbsp;<?=date('d F Y', strtotime($tgl_butuh));?>
				</div>
				<!-- <label class='label-control col-sm-2'><b>Asal Permintaan</b></label>
				<div class='col-sm-4'>              
					<?=$no_ipp;?>
				</div> -->
			</div>
			<div class='form-group row'>		 	 
				<!-- <label class='label-control col-sm-2'><b>Project</b></label>
				<div class='col-sm-4'>              
				: &nbsp;&nbsp;<?=strtoupper($project);?>
				</div> -->
				<!-- <label class='label-control col-sm-2'><b>Kebutuhan</b></label>
				<div class='col-sm-4'>              
					<?=$kebutuhan;?>
				</div> -->
			</div>
			<br>
			<input type='hidden' id='no_ipp' name='no_ipp' value='<?=$no_ipp2;?>'>
			<input type='hidden' id='tanda' name='tanda' value='<?=$tanda;?>'>
			<input type='hidden' id='id_user' name='id_user' value='<?=$id_user;?>'>
		
			<table class="table table-bordered table-striped" id="my-grid3" width='100%'>	
				<thead>
					<tr class='bg-blue'>
						<th colspan='14'>MATERIAL</th>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" style='vertical-align:middle;'>No</th>
						<th class="text-center" style='vertical-align:middle;'>Material Name</th>
						<th class="text-center" style='vertical-align:middle;'>Catgeory</th>

						<th class="text-center mid" style='width:6% !important;'>Stock Free</th>
						<th class="text-center mid" style='width:6% !important;'>Min Stock</th>
						<th class="text-center mid" style='width:6% !important;'>Max Stock</th>
						<!-- <th class="text-center mid" style='width:6% !important;'>Kg/bulan</th> -->
						<th class="text-center mid" style='width:6% !important;'>Min Order</th>
						<th class="text-center mid" style='width:6% !important;'>PR On Progress</th>
						<th class="text-center mid" style='width:6% !important;'>Qty PR</th>

						<!-- <th class="text-center mid" style='width:6% !important;'>Tanggal Dibutuhkan</th> -->
						<th class="text-center mid" style='width:6% !important;'>Rev Qty</th>
						<!-- <th class="text-center mid" style='width:10% !important;' rowspan='2'>Keterangan</th> -->
						<th class="text-center mid" style='width:7% !important;'>Reject</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no  = 0;
					foreach($result AS $val => $valx){ $no++;
						$safetystock 	= get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']);
						$kg_per_bulan 	= get_max_field('raw_materials', 'kg_per_bulan', 'id_material', $valx['id_material']);
						$max_stock 		= get_max_field('raw_materials', 'max_stock', 'id_material', $valx['id_material']);
						
						$reorder 		= ($safetystock/30) * $kg_per_bulan;
						$max_stock2 	= ($max_stock/30) * $kg_per_bulan;
						echo "<tr>";
							echo "<td align='center'>".$no."</td>";
							echo "<td align='left'>".$valx['nm_material']."</td>";
							echo "<td align='left'>".get_name('raw_materials', 'nm_category', 'id_material', $valx['id_material'])."</td>";
							
							echo "<td align='right'>".number_format($valx['qty_stock'] - $valx['qty_booking'],2)."</td>";
							echo "<td align='right'>".number_format($reorder,2)."</td>";
							echo "<td align='right'>".number_format($max_stock2,2)."</td>";
							// echo "<td align='right'>".number_format($kg_per_bulan,2)."</td>";
							echo "<td align='right'>".number_format($valx['moq_m'])."</td>";
							echo "<td align='right'>".number_format(get_qty_pr($valx['id_material']),2)."</td>";
							echo "<td align='right'>".number_format($valx['qty_request'],2)."</td>";
							// echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
							echo "<td align='center'>
								<input type='hidden' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
								<input type='hidden' name='detail[".$no."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='detail[".$no."][idmaterial]' value='".$valx['idmaterial']."'>
								<input type='hidden' name='detail[".$no."][nm_material]' value='".$valx['nm_material']."'>
								<input type='hidden' name='detail[".$no."][moq]' value='".$valx['moq_m']."'>
								<input type='hidden' name='detail[".$no."][qty_request]' value='".$valx['qty_request']."'>
								<input type='hidden' name='detail[".$no."][reorder_point]' value='".$valx['reorder_point']."'>
								<input type='hidden' name='detail[".$no."][book_per_month]' value='".$valx['book_per_month']."'>
								<input type='hidden' name='detail[".$no."][sisa_avl]' value='".$valx['sisa_avl']."'>
								<input type='hidden' name='detail[".$no."][tanggal]' value='".$valx['tanggal']."'>
								<input type='text' name='detail[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-center autoNumeric2' style='width:100%;'></td>";
							// echo "<td align='center'>
							// 		<input type='hidden' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
							// 		</td>";
							echo "<td align='center'>
									<button type='button'class='btn btn-sm btn-success appPR' title='Approve PR' data-nomor='".$no."' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-check'></i></button>
									<button type='button'class='btn btn-sm btn-danger rejectPR' title='Reject PR' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-close'></i></button>
									</td>";
						echo "</tr>";
					}
					if(empty($result)){
						echo "<tr>";
							echo "<td colspan='14'>Tidak ada data.</td>";
						echo "</tr>";
					}
					?>
				</tbody>
					<tr>
						<td colspan='13'>
							<?php
								if(!empty($result)){
									echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Approve Material','id'=>'app_pr')).' ';
								}
							?>
						</td>
					</tr>
			</table>
			<?php
		}
		?>


		<!-- <table class="table table-bordered table-striped" id="my-grid3" width='100%'>	
			<thead>
				<tr class='bg-blue'>
					<th colspan='14'>MATERIAL</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center" style='width:3% !important;'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" style='width:9% !important;'>Category</th>
					<th class="text-center" style='width:6% !important;'>Stock Actual (Kg)</th>
					<th class="text-center" style='width:6% !important;'>Stock Free (Kg)</th>
					<th class="text-center" style='width:6% !important;'>Safety Stock (Kg)</th>
					<th class="text-center" style='width:6% !important;'>Max Stock (Kg)</th>
					<th class="text-center" style='width:6% !important;'>Kg/bulan</th>
					<th class="text-center" style='width:6% !important;'>MOQ</th>
					<th class="text-center" style='width:6% !important;'>Qty (Kg)</th>
					<th class="text-center" style='width:6% !important;'>Tanggal Dibutuhkan</th>
					<th class="text-center" style='width:6% !important;'>Rev Qty (Kg)</th>
					<th class="text-center" style='width:10% !important;'>Keterangan</th>
					<th class="text-center" style='width:3% !important;'>Reject</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				foreach($result AS $val => $valx){ $no++;
					$safetystock 	= get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']);
					$kg_per_bulan 	= get_max_field('raw_materials', 'kg_per_bulan', 'id_material', $valx['id_material']);
					$max_stock 		= get_max_field('raw_materials', 'max_stock', 'id_material', $valx['id_material']);
					
					$reorder 		= ($safetystock/30) * $kg_per_bulan;
					$max_stock2 	= ($max_stock/30) * $kg_per_bulan;
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material']."</td>";
						echo "<td align='left'>".get_name('raw_materials', 'nm_category', 'id_material', $valx['id_material'])."</td>";
						echo "<td align='right'>".number_format($valx['qty_stock'],2)."</td>";
						echo "<td align='right'>".number_format($valx['qty_stock'] - $valx['qty_booking'],2)."</td>";
						echo "<td align='right'>".number_format($reorder,2)."</td>";
						echo "<td align='right'>".number_format($max_stock2,2)."</td>";
						echo "<td align='right'>".number_format($kg_per_bulan,2)."</td>";
						echo "<td align='right'>".number_format($valx['moq_m'])."</td>";
						echo "<td align='right'>".number_format($valx['qty_request'])."</td>";
						echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
						echo "<td align='center'><input type='text' name='detail[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-right maskM' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
						echo "<td align='center'>
								<input type='text' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
								<input type='hidden' name='detail[".$no."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='detail[".$no."][idmaterial]' value='".$valx['idmaterial']."'>
								<input type='hidden' name='detail[".$no."][nm_material]' value='".$valx['nm_material']."'>
								<input type='hidden' name='detail[".$no."][moq]' value='".$valx['moq_m']."'>
								<input type='hidden' name='detail[".$no."][qty_request]' value='".$valx['qty_request']."'>
								<input type='hidden' name='detail[".$no."][reorder_point]' value='".$valx['reorder_point']."'>
								<input type='hidden' name='detail[".$no."][book_per_month]' value='".$valx['book_per_month']."'>
								<input type='hidden' name='detail[".$no."][sisa_avl]' value='".$valx['sisa_avl']."'>
								<input type='hidden' name='detail[".$no."][tanggal]' value='".$valx['tanggal']."'>
								</td>";
						echo "<td align='center'><button type='button'class='btn btn-sm btn-danger rejectPR' title='Reject PR' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-close'></i></button></td>";
					echo "</tr>";
				}
				if(empty($result)){
					echo "<tr>";
						echo "<td colspan='14'>Tidak ada data.</td>";
					echo "</tr>";
				}
				?>
			</tbody>
			<thead>
				<tr class='bg-blue'>
					<th colspan='14'>NON FRP</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center no-sort">#</th>
					<th class="text-center" colspan='6'>Material Name</th>
					<th class="text-center">Category</th>
					<th class="text-center">Qty</th>
					<th class="text-center">Unit</th>
					<th class="text-center">Tanggal Dibutuhkan</th>
					<th class="text-center">Rev Qty</th>
					<th class="text-center">Keterangan</th>
					<th class="text-center">Reject</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				if(!empty($non_frp)){
					foreach($non_frp AS $val => $valx){ $no++;
						
						$satuan = $valx['satuan'];
						if($valx['idmaterial'] == '2'){
							$satuan = '1';
						}
						
						echo "<tr>";
							echo "<td align='center'>".$no."</td>";
							echo "<td align='left' colspan='6'>".get_name_acc($valx['id_material'])."</td>";
							echo "<td align='left'>".strtoupper(get_name('accessories_category', 'category', 'id', $valx['idmaterial']))."</td>";
							echo "<td align='right'>".number_format($valx['purchase'])."</td>";
							echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
							echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
							
							echo "<td align='center'><input type='text' name='detail_acc[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-right maskM' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='center'>
									<input type='text' name='detail_acc[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
									<input type='hidden' name='detail_acc[".$no."][id_material]' value='".$valx['id_material']."'>
									<input type='hidden' name='detail_acc[".$no."][idmaterial]' value='".$valx['idmaterial']."'>
									<input type='hidden' name='detail_acc[".$no."][nm_material]' value='".$valx['nm_material']."'>
									<input type='hidden' name='detail_acc[".$no."][moq]' value='".$valx['moq']."'>
									<input type='hidden' name='detail_acc[".$no."][qty_request]' value='".$valx['qty_request']."'>
									<input type='hidden' name='detail_acc[".$no."][reorder_point]' value='".$valx['reorder_point']."'>
									<input type='hidden' name='detail_acc[".$no."][book_per_month]' value='".$valx['book_per_month']."'>
									<input type='hidden' name='detail_acc[".$no."][sisa_avl]' value='".$valx['sisa_avl']."'>
									<input type='hidden' name='detail_acc[".$no."][tanggal]' value='".$valx['tanggal']."'>
									<input type='hidden' name='detail_acc[".$no."][id]' value='".$valx['id']."'>
									</td>";
							echo "<td align='center'><button type='button'class='btn btn-sm btn-danger rejectPR_acc' title='Reject PR' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-close'></i></button></td>";
						echo "</tr>";
					}
				}
				else{
					echo "<tr><td colspan='9'>Data not found</td></tr>";
				}
				?>
			</tbody>
		</table> -->
		
	</div>
</div>
<style>
.mid{
	vertical-align: middle !important;
}
</style>
<script>
    swal.close();
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.autoNumeric2').autoNumeric('init', {mDec: '2', aPad: false});
	});
</script>