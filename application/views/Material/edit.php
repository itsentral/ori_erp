<?php
$this->load->view('include/side_menu'); 
// echo"<pre>";print_r($row);
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Material Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'nm_material','name'=>'nm_material','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material Name','readonly'=>'readonly'), $row[0]['nm_material']);	
						echo form_input(array('type'=>'hidden','id'=>'id_material','name'=>'id_material','class'=>'form-control input-md'), $row[0]['id_material']);
					?>	
				</div>
				<label class='label-control col-sm-2'><b>Trade Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'nm_dagang','name'=>'nm_dagang','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Trade Name'), $row[0]['nm_dagang']);											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Internasional Name <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'nm_international','name'=>'nm_international','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Internasional Name'),$row[0]['nm_international']);											
					?>		
				</div>
				<label class='label-control col-sm-2'><b>Type Material <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('type'=>'hidden','id'=>'id_category','name'=>'id_category','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Internasional Name'),$row[0]['id_category']);											
					?>
					<select name='id_categoryX' id='id_categoryX' class='form-control input-md' disabled>
						<option value=''>Select An Material</option>
					<?php
						// $data_type[0]='Select An Material';
						// echo form_dropdown('id_category',$data_type, 0, array('id'=>'id_category','class'=>'form-control input-sm'));
						foreach($data_type AS $val => $valx){
							$sel = ($row[0]['id_category'] == $valx['id_category'])?'selected':'';
							echo "<option value='".$valx['id_category']."' ".$sel.">".ucwords(strtolower($valx['category']))."</option>";
						}
					 ?>
					</select>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Pieces Type <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_satuan' id='id_satuan' class='form-control input-md'>
						<option value=''>Select An Pieces</option>
					<?php
						foreach($data_pieces AS $val => $valx){
							$sel = ($row[0]['id_satuan'] == $valx['id_satuan'])?'selected':'';
							echo "<option value='".$valx['id_satuan']."' ".$sel.">".ucwords(strtolower($valx['nama_satuan']))." (".ucwords(strtolower($valx['kode_satuan'])).")</option>";
						}
					 ?>
					</select>	
				</div>
				<label class='label-control col-sm-2'><b>Conversion Value <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<?php
						echo form_input(array('id'=>'nilai_konversi','name'=>'nilai_konversi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Conversion Value', 'data-decimal'=>'.', 'data-thousand'=>'', 'data-prefix'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'true'), $row[0]['nilai_konversi']);											
					?>
				</div>
			</div>	
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Price Ref Estimation <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_input(array('id'=>'price_ref_estimation','name'=>'price_ref_estimation','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Price Ref Estimation', 'data-decimal'=>'.', 'data-thousand'=>'', 'data-prefix'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'true'), $row[0]['price_ref_estimation']);											
					?>
				</div>
				<label class='label-control col-sm-2'><b>Price Ref Purchase <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>          
					<?php
						echo form_input(array('id'=>'price_ref_purchase','name'=>'price_ref_purchase','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Price Ref Purchase', 'data-decimal'=>'.', 'data-thousand'=>'', 'data-prefix'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'true'), $row[0]['price_ref_purchase']);											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Description <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
					<?php
						echo form_textarea(array('id'=>'descr','name'=>'descr','class'=>'form-control input-md','rows'=>'3','cols'=>'75','autocomplete'=>'off','placeholder'=>'Description'), $row[0]['descr']);											
					?>
				</div>
				<label class='label-control col-sm-2'><b>Status Material <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<div class="input-group">
						<?php
							$active		= ($row[0]['flag_active'] =='Y')?TRUE:FALSE;
							$data = array(
									'name'          => 'flag_active',
									'id'            => 'flag_active',
									'value'         => 'Y',
									'checked'       => $active,
									'class'         => 'input-md'
							);
							echo form_checkbox($data).'&nbsp;&nbsp;Yes';
						?>
					</div>	
				</div>
			</div>
			<button type="button" id='edit_standard' style='width:150px; margin-right: 11px; margin-bottom: 5px; float:right;' class="btn btn-warning">Edit</button>
			<button type="button" id='cancel_edit_standard' style='width:150px; margin-right: 11px; margin-bottom: 5px; float:right;' class="btn btn-danger">Cancel Edit</button>
			<button type="button" id='update_edit_standard' style='width:150px; margin-right: 11px; margin-bottom: 5px; float:right;' class="btn btn-primary">Update Edit</button>
			<h4 style='margin-left: 11px;'>Supplier</h4>
			<div class="box-body" style="">
				<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_enEdit'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center">Supplier</th>
							<th class="text-center" style='width: 150px;'>Price</th>
							<th class="text-center" style='width: 150px;'>Valid Until</th>
							<th class="text-center" style='width: 250px;'>Descr</th>
							<th class="text-center" style='width: 100px;'>Flag</th>
						</tr>
					</thead>
					<tbody id='detail_body_Ed'>
						<?php
							$number = 0;
							foreach($Supply AS $val =>$valx){
								$number++;
								$status	= 'Active';
								$class	= 'bg-green';
								if($valx['flag_active'] == 'N'){
									$class	= 'bg-red';
									$status	= 'Not Active';
								}
								?> 
								<tr id="trspEd_<?= $number;?>">
									<td class='midAlign'><?= $number;?></td>
									<td>
										<input type='hidden' name='EdListDetail_sp[<?=$number;?>][id_supplier_material]' value='<?= ucfirst($valx['id_supplier_material']);?>'>
										<input type='hidden' name='EdListDetail_sp[<?=$number;?>][id_supplier]' value='<?= ucfirst($valx['id_supplier']);?>'>
										<input type='hidden' name='EdListDetail_sp[<?=$number;?>][nm_supplier]' value='<?= ucfirst($valx['nm_supplier']);?>'>
										<input type='hidden' name='EdListDetail_sp[<?=$number;?>][id_material]' value='<?= ucfirst($valx['id_material']);?>'>
										<input type='hidden' name='EdListDetail_sp[<?=$number;?>][nm_material]' value='<?= ucfirst($valx['nm_material']);?>'>
										<select name='EdListDetail_sp[<?=$number;?>][id_supplier]' id='Edid_supplier_<?= $number;?>' class='chosen_select form-control inline-block chosRest' disabled>
										<?php
												foreach($ListSup AS $valL => $valLx){
													$sel=($valx['id_supplier'] == $valLx['id_supplier'])?'selected':'';
													echo "<option value='".$valLx['id_supplier']."' ".$sel.">".$valLx['nm_supplier']."</option>";
												}
											?>
										</select>
									</td>
									<td>
										<div class='dataR' align='right'><?= number_format(floatval($valx['price']));?></div>
										<div class='dataE'><input type='text' style="text-align: right;" class='form-control angkaX' name='EdListDetail_sp[<?=$number;?>][price]' id='Edprice_sp_<?= $number;?>' value='<?= ucfirst($valx['price']);?>' data-decimal='.' data-thousand='' data-prefix='' data-precision='0' data-allow-zero='true'></div>
									</td>
									<td>
										<div class='dataR' align='center'><?= date('d F Y', strtotime($valx['valid_until']));?></div>
										<div class='dataE'><input type='text' style="text-align: center;" style='cursor: pointer;' class='form-control valid_until' name='EdListDetail_sp[<?=$number;?>][valid_until]' id='Edvalid_until_sp_<?= $number;?>' value='<?= ucfirst($valx['valid_until']);?>' readonly></div>
									</td>
									<td>
										<div class='dataR'><?= ucfirst($valx['descr']);?></div>
										<div class='dataE'><input type='text' class='form-control' name='EdListDetail_sp[<?=$number;?>][descr]' id='Eddescr_sp_<?= $number;?>' value='<?= ucfirst($valx['descr']);?>'></div>
									</td>
									<td align='center'>
										<div class='dataR'><span class='badge <?=$class;?>'><?= $status;?></span></div>
										<div class='dataE'>
											<?php
												$active		= ($valx['flag_active'] =='Y')?TRUE:FALSE;
												$data = array(
														'name'          => "EdListDetail_sp[".$number."][flag_active]",
														'id'            => "Edflag_active_sp_".$number."",
														'value'         => 'Y',
														'checked'       => $active,
														'class'         => 'input-sm'
												);
												echo form_checkbox($data).'&nbsp;&nbsp;Yes';
											?>
										</div>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<h4 style='margin-left: 11px;'>Engineering Standard</h4>
			<div class="box-body" style="">
				<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_enEdit'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center">Standart ENG Name</th>
							<th class="text-center" style='width: 300px;'>Standard ENG Value</th>
							<th class="text-center" style='width: 250px;'>Descr ENG</th>
							<th class="text-center" style='width: 100px;'>Flag ENG</th>
						</tr>
					</thead>
					<tbody id='detail_body_enEd'>
						<?php
							$number = 0;
							foreach($detailEn AS $val =>$valx){
								$number++;
								$status	= 'Active';
								$class	= 'bg-green';
								if($valx['flag_active'] == 'N'){
									$class	= 'bg-red';
									$status	= 'Not Active';
								}
								?>
								<tr id="trenEd_<?= $number;?>">
									<td class='midAlign'><?= $number;?></td>
									<td>
										<input type='hidden' name='EdListDetail_en[<?=$number;?>][id_standard]' value='<?= ucfirst($valx['id_standard']);?>'>	
										<select name='EdListDetail_en[<?=$number;?>][id_category_standard]' id='Edid_category_standard_en_<?= $number;?>' class='chosen_select form-control inline-block' disabled>
										
										<?php
												foreach($detailEn AS $valL => $valLx){
													$sel=($valx['id_category_standard'] == $valLx['id_category_standard'])?'selected':'';
													echo "<option value='".$valLx['id_category_standard']."' ".$sel.">".$valLx['nm_standard']."</option>";
												}
											?>
										</select>
									</td>
									<td>
										<div class='dataR' style='margin-right:80px;' align='right'><?= ucfirst($valx['nilai_standard']);?></div>
										<div class='dataE'><input type='text'style="text-align: right;" class='form-control AngkaXY' name='EdListDetail_en[<?=$number;?>][nilai_standard]' id='Ednilai_standard_en_<?= $number;?>' value='<?= ucfirst($valx['nilai_standard']);?>'></div>
									</td>
									<td>
										<div class='dataR'><?= ucfirst($valx['descr']);?></div>
										<div class='dataE'><input type='text' class='form-control' name='EdListDetail_en[<?=$number;?>][descr]' id='Eddescr_en_<?= $number;?>' value='<?= ucfirst($valx['descr']);?>'></div>
									</td>
									<td align='center'>
										<div class='dataR'><span class='badge <?=$class;?>'><?= $status;?></span></div>
										<div class='dataE'>
											<?php
												$active		= ($valx['flag_active'] =='Y')?TRUE:FALSE;
												$data = array(
														'name'          => "EdListDetail_en[".$number."][flag_active]",
														'id'            => "Edflag_active_en_".$number."",
														'value'         => 'Y',
														'checked'       => $active,
														'class'         => 'input-sm'
												);
												echo form_checkbox($data).'&nbsp;&nbsp;Yes';
											?>
										</div>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<h4 style='margin-left: 11px;'>BQ Standard</h4>
			
			<div class="box-body" style="">
				<table id="my-grid_bq" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_bqEdit'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="50px">No</th>
							<th class="text-center">Standart BQ Name</th>
							<th class="text-center" style='width: 300px;'>Standard BQ Value</th>
							<th class="text-center" style='width: 250px;'>Descr BQ</th>
							<th class="text-center" style='width: 100px;'>Flag BQ</th>
						</tr>
					</thead>
					<tbody id='detail_body_bqEd'>
						<?php
							$number = 0;
							foreach($detailBQ AS $val =>$valx){
								$number++;
								$status	= 'Active';
								$class	= 'bg-green';
								if($valx['flag_active'] == 'N'){
									$class	= 'bg-red';
									$status	= 'Not Active';
								}
								?>
								<tr id="trbqEd_<?= $number;?>">
									<td class='midAlign'><?= $number;?></td>
									<td>
										<input type='hidden' name='EdListDetail_bq[<?=$number;?>][id_standard]' value='<?= ucfirst($valx['id_standard']);?>'>	
										<select name='EdListDetail_bq[<?=$number;?>][id_category_standard]' id='iEdd_category_standard_bq_<?= $number;?>' class='chosen_select form-control inline-block' disabled>
										
										<?php
											foreach($detailBQ AS $valL => $valLx){
												$sel=($valx['id_category_standard'] == $valLx['id_category_standard'])?'selected':'';
												echo "<option value='".$valLx['id_category_standard']."' ".$sel.">".$valLx['nm_standard']."</option>";
											}
										?>
										</select>
									</td>
									<td>
										<div class='dataR' style='margin-right:80px;' align='right'><?= ucfirst($valx['nilai_standard']);?></div>
										<div class='dataE'><input type='text' style="text-align: right;" class='form-control AngkaXY' name='EdListDetail_bq[<?=$number;?>][nilai_standard]' id='Ednilai_standard_bq_<?= $number;?>' value='<?= ucfirst($valx['nilai_standard']);?>'></div>
									</td>
									<td>
										<div class='dataR'><?= ucfirst($valx['descr']);?></div>
										<div class='dataE'><input type='text' class='form-control' name='EdListDetail_bq[<?=$number;?>][descr]' id='Eddescr_bq_<?= $number;?>' value='<?= ucfirst($valx['descr']);?>'></div>
									</td>
									<td align='center'>
										<div class='dataR'><span class='badge <?=$class;?>'><?= $status;?></span></div>
										<div class='dataE'>
											<?php
												$active		= ($valx['flag_active'] =='Y')?TRUE:FALSE;
												$data = array(
														'name'          => "EdListDetail_bq[".$number."][flag_active]",
														'id'            => "Edflag_active_bq_".$number."",
														'value'         => 'Y',
														'checked'       => $active,
														'class'         => 'input-sm'
												);
												echo form_checkbox($data).'&nbsp;&nbsp;Yes';
											?>
										</div>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<br>
			<div class='form-group row'>		 	 
				<div class='col-sm-9'>             
					<button type="button" id='add' style='width:130px; margin-left:10px;' class="btn btn-success">Add Supplier</button>
					<button type="button" id='add_en' style='width:130px; margin-left:5px;' class="btn btn-success">Add Category Eng</button>
					<button type="button" id='add_bq' style='width:130px; margin-left:5px;' class="btn btn-success">Add Category BQ</button>
					<input type='hidden' name='numberMax' id='numberMax' value='0'>
					<input type='hidden' name='numberMax_en' id='numberMax_en' value='0'>
					<input type='hidden' name='numberMax_bq' id='numberMax_bq' value='0'>
				</div>
			</div>
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="10px">No</th>
							<th class="text-center">Supplier</th>
							<th class="text-center" style='width: 110px;'>Price</th>
							<th class="text-center" style='width: 130px;'>Valid Until</th>
							<th class="text-center" style='width: 250px;'>Descr</th>
							<th class="text-center" style='width: 70px;'>Flag</th>
							<th class="text-center" style='width: 70px;'>Opt</th>
						</tr>
					</thead>
					<tbody id='detail_body'>
					</tbody>
				</table>
			</div>
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_en'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="10px">No</th>
							<th class="text-center">Standart ENG Name</th>
							<th class="text-center" style='width: 200px;'>Standard ENG Value</th>
							<th class="text-center" style='width: 250px;'>Descr ENG</th>
							<th class="text-center" style='width: 70px;'>Flag ENG</th>
							<th class="text-center" style='width: 70px;'>Opt ENG</th>
						</tr>
					</thead>
					<tbody id='detail_body_en'>
					</tbody>
				</table>
			</div> 
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<thead id='head_table_bq'>
						<tr class='bg-blue'>
							<th class="text-center" class="no-sort" width="10px">No</th>
							<th class="text-center">Standart BQ Name</th>
							<th class="text-center" style='width: 200px;'>Standard BQ Value</th>
							<th class="text-center" style='width: 250px;'>Descr BQ</th>
							<th class="text-center" style='width: 70px;'>Flag BQ</th>
							<th class="text-center" style='width: 70px;'>Opt BQ</th>
						</tr>
					</thead>
					<tbody id='detail_body_bq'>
					</tbody>
				</table>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','style'=>'width:100px; margin-left:10px;','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','style'=>'width:100px; margin-left:10px;','content'=>'Back','onClick'=>'javascript:back()'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<style type="text/css">
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
	.midAlign{
		text-align: center;
		vertical-align: middle;
	}
	#id_categoryX_chosen{
		width: 100% !important;
	}
	#id_satuan_chosen{
		width: 100% !important;
	}
	.chosRest{
		width: 100% !important;
	}
</style>
<script>
	
	$('.valid_until').datepicker({
		format : 'yyyy-mm-dd',
		startDate: 'now'
	});
	$(document).ready(function(){
		$('#price_ref_purchase').maskMoney();
		$('#price_ref_estimation').maskMoney();
		$('#nilai_konversi').maskMoney();
		$('.angkaX').maskMoney();
		$('#head_table').hide();
		$('#head_table_en').hide();
		$('#head_table_bq').hide();
		// $('#simpan-bro').hide();
		$('.dataE').hide();
		$('#update_edit_standard').hide();
		$('#cancel_edit_standard').hide();
		$(".AngkaXY").mask('?999999999999');
		var nomor	= 1;
	
		$('#add').click(function(e){
			e.preventDefault();
			console.log(nomor);
			AppendBaris(nomor);
			$('#head_table').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax").val(nilaiAkhir);
			$('#simpan-bro').show();
			// if($("#numberMax").val(nilaiAkhir) != 0 && $('#numberMax_en').val() != 0 && $('#numberMax_bq').val() != 0){
				// $('#simpan-bro').show();
			// }
		});
		
		$('#add_en').click(function(e){
			e.preventDefault();
			console.log(nomor);
			AppendBaris_en(nomor);
			$('#head_table_en').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax_en").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax_en").val(nilaiAkhir);
			$('#simpan-bro').show();
			// if($('#numberMax').val() != 0 && $("#numberMax_en").val(nilaiAkhir) != 0 && $('#numberMax_bq').val() != 0){
				// $('#simpan-bro').show();
			// }
		});
		
		$('#add_bq').click(function(e){
			e.preventDefault();
			console.log(nomor);
			AppendBaris_bq(nomor);
			$('#head_table_bq').show();
			$('.chosen_select').chosen({width: '100%'});
			
			var nilaiAwal	= parseInt($("#numberMax_bq").val());
			var nilaiAkhir	= nilaiAwal + 1;
			$("#numberMax_bq").val(nilaiAkhir);
			$('#simpan-bro').show();
			// if($('#numberMax').val() != 0 && $('#numberMax_en').val() != 0 && $("#numberMax_bq").val(nilaiAkhir) != 0){
				// $('#simpan-bro').show();
			// }
		});
		
		$(document).on('click', '#edit_standard', function(){
			$('#update_edit_standard').show();
			$('#cancel_edit_standard').show();
			$('#edit_standard').hide();
			$('#simpan-bro').hide();
			$('.dataE').show();
			$('.dataR').hide();
		});
		
		$(document).on('click', '#cancel_edit_standard', function(){
			$('#update_edit_standard').hide();
			$('#cancel_edit_standard').hide();
			$('#edit_standard').show();
			$('#simpan-bro').show();
			$('.dataR').show();
			$('.dataE').hide();
		});
		
		$(document).on('click', '#update_edit_standard', function(){
			// $('#update_edit_standard').prop('disabled',true);
			// $('#simpan-bro').show();
			
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			$('#detail_body_bqEd').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var nilai_standard_bq		= $('#Ednilai_standard_bq_'+nomor[1]).val();
				var descr_bq				= $('#Eddescr_bq_'+nomor[1]).val();
				
				
				if(descr_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Description BQ number has not empty ...";
				}
				if(nilai_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Standard value BQ number has not empty ...";
				}
			});
			
			$('#detail_body_enEd').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var nilai_standard_en		= $('#Ednilai_standard_en_'+nomor[1]).val();
				var descr_en				= $('#Eddescr_en_'+nomor[1]).val();
				
				
				if(descr_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Description ENG number has not empty ...";
				}
				if(nilai_standard_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Standard value ENG number has not empty ...";
				}
			});
			
			$('#detail_body_Ed').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var price		= $('#Edprice_sp_'+nomor[1]).val();
				var valid_until	= $('#Edvalid_until_sp_'+nomor[1]).val();
				var descr		= $('#Eddescr_sp_'+nomor[1]).val();
				
				if(descr == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Description number has not empty ...";
				}
				if(valid_until == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Valid until number has not empty ...";
				}
				if(price == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : Price number has not empty ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$('#update_edit_standard').prop('disabled',false);
				return false;
			}
			
			// alert('Success Validate');
			$('#simpan-bro').prop('disabled',false);
			
			// alert("Bisa ya ..."); 
			// return false;
			
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
						var baseurl		= base_url + active_controller +'/editData';
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
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									window.location.href = base_url + active_controller;
								}
								if(data.status == 2){
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
								$('#simpan-bro').prop('disabled',false);
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
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			var nm_material				= $('#nm_material').val();
			var nm_dagang				= $('#nm_dagang').val();
			var nm_international		= $('#nm_international').val();
			var id_category				= $('#id_category').val();
			var id_satuan				= $('#id_satuan').val();
			var nilai_konversi			= $('#nilai_konversi').val();
			var price_ref_estimation	= $('#price_ref_estimation').val();
			var price_ref_purchase		= $('#price_ref_purchase').val();
			var descr					= $('#descr').val();
			
			$(this).prop('disabled',true);
			
			if(nm_material=='' || nm_material==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Material Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(nm_dagang == '' || nm_dagang == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Trade Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(nm_international == '' || nm_international == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Internasional Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_category=='' || id_category==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Type Material Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
				
			}
			if(id_satuan == '' || id_satuan == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Pieces Type is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(nilai_konversi == '' || nilai_konversi == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Conversion Value is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(price_ref_estimation=='' || price_ref_estimation==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Price Ref Estimation is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(price_ref_purchase == '' || price_ref_purchase == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Price Ref Purchase is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(descr == '' || descr == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Description Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			//validasi supplier
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			$('#detail_body_bq').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var id_category_standard_bq	= $('#id_category_standard_bq_'+nomor[1]).val();
				var nilai_standard_bq		= $('#nilai_standard_bq_'+nomor[1]).val();
				var descr_bq				= $('#descr_bq_'+nomor[1]).val();
				
				
				if(descr_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description BQ number has not empty ...";
				}
				if(nilai_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : standard value BQ number has not empty ...";
				}
				if(id_category_standard_bq == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard BQ number has not been chosen ...";
				}
			});
			
			$('#detail_body_en').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var id_category_standard_en	= $('#id_category_standard_en_'+nomor[1]).val();
				var nilai_standard_en		= $('#nilai_standard_en_'+nomor[1]).val();
				var descr_en				= $('#descr_en_'+nomor[1]).val();
				
				
				if(descr_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description ENG number has not empty ...";
				}
				if(nilai_standard_en == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : standard value ENG number has not empty ...";
				}
				if(id_category_standard_en == '0' ){
					intError++;
					pesan = "Number "+nomor[1]+" : category standard ENG number has not been chosen ...";
				}
			});
			
			$('#detail_body').find('tr').each(function(){
				intL++;
				var findId	= $(this).attr('id');
				var nomor	= findId.split('_');
				var id_supplier	= $('#id_supplier_'+nomor[1]).val();
				var price		= $('#price_'+nomor[1]).val();
				var valid_until	= $('#valid_until_'+nomor[1]).val();
				var descr		= $('#descr_'+nomor[1]).val();
				
				if(descr == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : description number has not empty ...";
				}
				if(valid_until == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : valid until number has not empty ...";
				}
				if(price == '' ){
					intError++;
					pesan = "Number "+nomor[1]+" : price number has not empty ...";
				}
				if(id_supplier == '0' ){
					intError++;
					pesan = "Number "+nomor[1]+" : supplier number has not been chosen ...";
				}
			});
			
			if(intError > 0){
				// alert(pesan);
				swal({
					title				: "Notification Message !",
					text				: pesan,						
					type				: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			// alert('Success Validate');
			$('#simpan-bro').prop('disabled',false);
			// return false;
			
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
						var baseurl		= base_url + active_controller +'/edit';
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
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									window.location.href = base_url + active_controller;
								}
								if(data.status == 2){
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
								$('#simpan-bro').prop('disabled',false);
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
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});
	});
	
	function delRow(row){
		$('#tr_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax").val() - 1;
		$("#numberMax").val(updatemax);
		
		var maxLine = $("#numberMax").val();
		if(maxLine == 0){
			$('#head_table').hide();
			// $('#simpan-bro').hide();
		}
	}
	
	function delRow_En(row){
		$('#tren_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax_en").val() - 1;
		$("#numberMax_en").val(updatemax);
		
		var maxLine = $("#numberMax_en").val();
		if(maxLine == 0){
			$('#head_table_en').hide();
			// $('#simpan-bro').hide();
		}
	}
	
	function delRow_Bq(row){
		$('#trbq_'+row).remove();
		// row = 0;
		var updatemax	=	$("#numberMax_bq").val() - 1;
		$("#numberMax_bq").val(updatemax);
		
		var maxLine = $("#numberMax_bq").val();
		if(maxLine == 0){
			$('#head_table_bq').hide();
			// $('#simpan-bro').hide();
		}
	}

	function AppendBaris(intd)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='tr_"+nomor+"'>";
			Rows	+= 	"<td>";
			Rows	+= 		"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail["+nomor+"][id_supplier]' id='id_supplier_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Supplier</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' style='text-align: right;' name='ListDetail["+nomor+"][price]' id='price_"+nomor+"' data-decimal='.' data-thousand='' data-prefix='' data-precision='0' data-allow-zero='true' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control valid_until' style='cursor: pointer;' name='ListDetail["+nomor+"][valid_until]' id='valid_until_"+nomor+"' required autocomplete='off' readonly>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail["+nomor+"][descr]' id='descr_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail["+nomor+"][flag_active]' value='Y' id='flag_active_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' id='del_acc' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body').append(Rows);
		$("#price_"+nomor).maskMoney();
		$('.valid_until').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		
		var id_supplier_ = "#id_supplier_"+nomor;
		// console.log(accID);
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getSupplierED',
			cache: false,
			type: "POST",
			data: "id_material="+$("#id_material").val(),
			dataType: "json",
			success: function(data){
				$(id_supplier_).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}
	
	function AppendBaris_en(intd)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body_en').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_en tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='tren_"+nomor+"'>";
			Rows	+= 	"<td>";
			Rows	+= 		"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail_en["+nomor+"][id_category_standard_en]' id='id_category_standard_en_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Standard</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' style='text-align: right;' name='ListDetail_en["+nomor+"][nilai_standard_en]' id='nilai_standard_en_"+nomor+"' data-decimal='.' data-thousand='' data-prefix='' data-precision='0' data-allow-zero='true' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail_en["+nomor+"][descr_en]' id='descr_en_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail_en["+nomor+"][flag_active_en]' value='Y' id='flag_active_en_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' id='del_acc' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_En("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_en').append(Rows);
		$("#nilai_standard_en_"+nomor).mask('?999999999999');
		
		var id_category_standard_en = "#id_category_standard_en_"+nomor;
		// console.log(accID);
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getId_category_standard_enED',
			cache: false,
			type: "POST",
			data: "id_category="+$("#id_category").val()+"&id_material="+$('#id_material').val(),
			dataType: "json",
			success: function(data){
				$(id_category_standard_en).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}
	
	function AppendBaris_bq(intd)
	{
		var nomor	= 1;
		var valuex	= $('#detail_body_bq').find('tr').length;
		if(valuex > 0){
			var akhir	= $('#detail_body_bq tr:last').attr('id');
			var det_id	= akhir.split('_');
			var nomor	= parseInt(det_id[1])+1;
		}

		var Rows	 = "<tr id='trbq_"+nomor+"'>";
			Rows	+= 	"<td>";
			Rows	+= 		"<div style='text-align: center;'>"+nomor+"</div>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td align='left'>";
			Rows	+=		"<select name='ListDetail_bq["+nomor+"][id_category_standard_bq]' id='id_category_standard_bq_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Standard</option></select>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' style='text-align: right;' name='ListDetail_bq["+nomor+"][nilai_standard_bq]' id='nilai_standard_bq_"+nomor+"' data-decimal='.' data-thousand='' data-prefix='' data-precision='0' data-allow-zero='true' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='text' class='form-control' name='ListDetail_bq["+nomor+"][descr_bq]' id='descr_bq_"+nomor+"' required autocomplete='off'>";
			Rows	+= 	"</td>";
			Rows	+= 	"<td>";
			Rows	+=		"<input type='checkbox' class='form-check-input' name='ListDetail_bq["+nomor+"][flag_active_bq]' value='Y' id='flag_active_bq_"+nomor+"' checked><label class='form-check-label'>Active</label>";
			Rows	+= 	"</td>";
			Rows += 	"<td align=\"left\">";
			Rows +=		"<div style='text-align: center;'><button type='button' id='del_acc' class='but-det btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Bq("+nomor+")' title='Delete Record'><i class='fa fa-times-circle'></i></button></div>";
			Rows += 	"</td>";
			Rows	+= "</tr>";

		$('#detail_body_bq').append(Rows);
		$("#nilai_standard_bq_"+nomor).mask('?999999999999');
		
		var id_category_standard_bq = "#id_category_standard_bq_"+nomor;
		// console.log(accID);
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getId_category_standard_bqED',
			cache: false,
			type: "POST",
			data: "id_category="+$("#id_category").val()+"&id_material="+$('#id_material').val(),
			dataType: "json",
			success: function(data){
				$(id_category_standard_bq).html(data.option).trigger("chosen:updated");
			}
		});
		nomor++;
	}
</script>
