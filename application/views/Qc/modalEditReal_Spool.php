
<?php
$start_time				= (!empty($get_spk))?$get_spk[0]->start_time:'';
$finish_time			= (!empty($get_spk))?$get_spk[0]->finish_time:'';
$cycletime				= (!empty($get_spk))?$get_spk[0]->cycletime:'';
$total_time				= (!empty($get_spk))?$get_spk[0]->total_time:'';
$productivity			= (!empty($get_spk))?$get_spk[0]->productivity:'';
$upload_spk				= (!empty($get_spk))?$get_spk[0]->upload_spk:'';
$next_process			= (!empty($get_spk))?$get_spk[0]->next_process:'';
?>
<input type='hidden' name='spool_induk' value='<?= $spool_induk;?>'>
<div class="box box-primary">
	<div class="box-header">
	
	<div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Product</b></label>
			<div class='col-sm-10'>             
				<?php
					echo form_textarea(array('id'=>'product_code','name'=>'product_code','rows'=>3,'class'=>'form-control input-md','readonly'=>'true'),$kode_product);											
				?>		
			</div>
		</div>
		<div class='form-group row' hidden>		 	 
			<label class='label-control col-sm-2'><b>Start</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='start_time' name='start_time' class='form-control input-md datetimepicker' placeholder='Start Produksi' value='<?=$start_time;?>'>
			</div>
			<label class='label-control col-sm-2'><b>Cycletime | Total Time</b></label>
			<div class='col-sm-2'>             
				<input type='text' id='cycletime' name='cycletime' class='form-control input-md autoNumeric change_product' placeholder='Cycletime' value='<?=$cycletime;?>'>
			</div>
			<div class='col-sm-2'>             
				<input type='text' id='total_time' name='total_time' class='form-control input-md autoNumeric change_product' placeholder='Total Time' value='<?=$total_time;?>'>	
			</div>
		</div>
		<div class='form-group row' hidden>		 	 
			<label class='label-control col-sm-2'><b>Finish</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='finish_time' name='finish_time' class='form-control input-md datetimepicker' placeholder='Finish Produksi' value='<?=$finish_time;?>'>		
			</div>
			<label class='label-control col-sm-2'><b>Productivity</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='productivity' name='productivity' class='form-control input-md' placeholder='Productivity' readonly value='<?=$productivity;?>'>	
			</div>
		</div>
		<div class='form-group row' hidden>		 	 
			<label class='label-control col-sm-2'><b>Upload SPK</b></label>
			<div class='col-sm-4 text-right'>             
				<input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload SPK'>
				<?php if(!empty($upload_spk)){ ?>
				<a href='#' target='_blank' title='Download' data-role='qtip'>Download</a>
				<?php } ?>	
			</div>
			<label class='label-control col-sm-2'><b>Next Process</b></label>
			<div class='col-sm-4'>             
				<select name='next_process' class='form-control input-md chosen_select'>
					<option value='0'>Select Next Process</option>
					<?php
					foreach ($costcenter as $key => $value) {
						$selc = ($next_process == $value['id_costcenter'])?'selected':'';
						echo "<option value='".$value['id_costcenter']."' ".$selc.">".strtoupper($value['nm_costcenter'])."</option>";
					}
					?>
				</select>	
			</div>
		</div>
		<br>
		<table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='10%'>#</th>
					<th class="text-center" width='20%'>No Spool</th>
					<th class="text-center" width='20%'>Daycode</th>
					<th class="text-center" width='30%'>Keterangan</th>
					<th class="text-center" width='20%'>Upload Checksheet Inspeksi</th>
				</tr>
			</thead>
			<tbody>

			
			<?php
			foreach ($result as $key2 => $value2) { $key2++;
				$result2 = $this->db
								->select('a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
								->join('production_detail b','a.id_pro=b.id','left')
								->get_where('spool_group a', 
									array(
										'a.spool_induk'=>$spool_induk,
										'a.kode_spool'=>$value2['kode_spool']
										)
									)->result_array();


				$download_spool = '';
				if(!empty($value2['sp_group_inspeksi'])){
				$download_spool = "<a href='#' target='_blank' title='Download' data-role='qtip'>Download</a>";
				}
				?>  
					<tr>
						<td class="text-center"><?= $key2;?></td>
						<td class="text-center"><?=$value2['kode_spool'];?></td>
						<?php
						echo "<td><input type='text' name='detail_spool[".$value2['kode_spool']."][sp_daycode]' class='form-control input-md' placeholder='Daycode' value='".$value2['sp_group_daycode']."'></td>";
						echo "<td><input type='text' name='detail_spool[".$value2['kode_spool']."][sp_ket]' class='form-control input-md' placeholder='Keterangan' value='".$value2['sp_group_keterangan']."'></td>";
						echo "<td class='text-right'>
								<input type='hidden' name='detail_spool[".$value2['kode_spool']."][id]' class='form-control input-md' value='".$value2['kode_spool']."'>
								<input type='file' name='inspeksi_spool_".$value2['kode_spool']."' class='form-control input-md'>
								".$download_spool."
								</td>";
						?>
					</tr>
					<tr>
						<td colspan='5'>
							<table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
								<thead>
									<tr class='bg-blue'>
										<th class="text-center">#</th>
										<th class="text-center">IPP</th>
										<th class="text-center">Product</th>
										<th class="text-center">Spec</th>
										<th class="text-center">Lenght</th>
										<th class="text-center">Product Code</th>
										<th class="text-center">No SPK</th>
										<th class="text-center">No Drawing</th>
										<th class="text-center" hidden>Keterangan</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($result2 as $key => $value) { $key++;
										$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
										
										$IMPLODE = explode('.', $value['product_code']);
										$product_code = $IMPLODE[0].'.'.$value['product_ke'].$CUTTING_KE;
										$download = '';
										if(!empty($value['sp_inspeksi'])){
										$download = "<a href='#' target='_blank' title='Download' data-role='qtip'>Download</a>";
										}

										$nm_product = ($value['type_product'] == 'tanki')?$value['product_tanki']:$value['id_category'];
										$spec = ($value['type_product'] == 'tanki')?$tanki_model->get_spec($value['id_milik']):spec_bq2($value['id_milik']);

										echo "<tr>";
											echo "<td align='center'>".$key."</td>";
											echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
											echo "<td align='left'>".strtoupper($nm_product)."</td>";
											echo "<td align='left'>".$spec."</td>";
											echo "<td align='center'>".$value['length']."</td>";
											echo "<td align='left'>".$product_code."</td>";
											echo "<td align='center'>".$value['no_spk']."</td>";
											echo "<td align='left'>".$value['no_drawing']."</td>";
											echo "<td hidden>
													<select name='detail[".$value['id']."_".$value['sts']."][sp_status]' class='form-control input-md chosen_select'>
														<option value='1'>OKE</option>
													</select>
													<input type='text' name='detail[".$value['id']."_".$value['sts']."][sp_daycode]' class='form-control input-md' placeholder='Daycode' value='".$value['sp_daycode']."'>
													<input type='text' name='detail[".$value['id']."_".$value['sts']."][sp_ket]' class='form-control input-md' placeholder='Keterangan' value='".$value['sp_keterangan']."'>
													<input type='hidden' name='detail[".$value['id']."_".$value['sts']."][id]' class='form-control input-md' value='".$value['id']."-".$value['sts']."'>
													<input type='file' name='inspeksi_".$value['id']."-".$value['sts']."' class='form-control input-md'>
													".$download."
													</td>";
										echo "</tr>";
									}
									?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	<div>
	<div class='box-footer'>
		<button type='button' id='sendCheck' class='btn btn-md btn-success' style='float:right; margin-left:10px;'><b>Release To FG</b></button>
		<button type='button' id='uploadTemp' class='btn btn-md btn-primary' style='float:right;'><b>Save Draf</b></button>
	</div>
</div>
<!-- <div class="box box-danger">
	<div class="box-header">
		<h5 class="box-title">Reject Spool</h5>
	<div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Reason</b></label>
			<div class='col-sm-10'>             
				<textarea id='reason_reject' name='reason_reject' class='form-control input-md' rows='3' placeholder='Reject Reason'></textarea>
			</div>
		</div>
	</div>
	<div class='box-footer'>
		<button type='button' id='rejectSpool' class='btn btn-md btn-danger' style='float:right;'><b>Reject Spool</b></button>
	</div>
</div> -->
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen_select').chosen({
			width : '100%'
		});
		$('.autoNumeric').autoNumeric();
		$('.datetimepicker').datetimepicker();
	});
</script>