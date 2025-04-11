
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div class="box box-success">
				<div class="box-header">
					<h3 class="box-title">Request Customer</h3>
				</div>
				<div class="box-body">
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Customer Name <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='id_customerx' id='id_customerx' class='form-control input-md chosen-select' disabled>
								<option>Select An Customer</option>
							 <?php
								foreach($CustList AS $val => $valx){
									$selX	= ($valx['id_customer'] == $RestRequest[0]->id_customer)?'selected':'';
									echo "<option value='".$valx['id_customer']."' ".$selX.">".$valx['nm_customer']."</option>";
								}
							 ?>
							 </select>
							 <?php
							  echo form_input(array('type'=>'hidden','id'=>'id_customer','name'=>'id_customer','class'=>'form-control input-md'), $RestRequest[0]->id_customer);
							
							 ?>
						</div>
						<label class='label-control col-sm-2'><b>Max Min Toleransi <span class='text-red'>*</span></b></label>
						<div class='col-sm-2'>
							<?php
							 echo form_input(array('id'=>'max_tol','name'=>'max_tol','class'=>'form-control input-md numberOnly','placeholder'=>'Max'), floatval($RestRequest[0]->max_tol));
							?>
						</div>
						<div class='col-sm-2'>
							<?php
							 echo form_input(array('id'=>'min_tol','name'=>'min_tol','class'=>'form-control input-md numberOnly','placeholder'=>'Min'), floatval($RestRequest[0]->min_tol));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Project'), $RestRequest[0]->project);
							 echo form_input(array('type'=>'hidden','id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md'), $RestRequest[0]->no_ipp);
							 echo form_input(array('type'=>'hidden','id'=>'ref_ke','name'=>'ref_ke','class'=>'form-control input-md'), $RestRequest[0]->ref_ke);
							
							?>
						</div>
						<label class='label-control col-sm-2'><b>Note</b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'note','name'=>'note','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Note Etc'), $RestRequest[0]->note);
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Validity & Guarantee <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_input(array('id'=>'validity','name'=>'validity','class'=>'form-control input-md','placeholder'=>'Validity & Guarantee'), $RestRequest[0]->validity);
							?>
						</div>
						<label class='label-control col-sm-2'><b>Payment Term <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_input(array('id'=>'payment','name'=>'payment','class'=>'form-control input-md','placeholder'=>'Payment Term'), $RestRequest[0]->payment);
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Referensi Customer/Project <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'ref_cust','name'=>'ref_cust','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Ref Customer/Project'), $RestRequest[0]->ref_cust);
							?>
						</div>
						<label class='label-control col-sm-2'><b>Special Requirements From Customer</b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'syarat_cust','name'=>'syarat_cust','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Special Requirements From Customer'), $RestRequest[0]->syarat_cust);
							?>
						</div>
					</div>
					<div class="box box-danger">
						<div class="box-header">
							<h3 class="box-title">Specification List</h3>
						</div>
						<div class="box-body">
							<!--<button type="button" id='add_sp' style='width:130px; margin-right:0px; margin-bottom:3px; margin-left:5px; float:right;' class="btn btn-success btn-sm">Add Specification</button>-->
							<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
								<thead id='head_table'>
									<tr class='bg-blue'>
										<th class="text-center" style='width: 3%;'>NO</th>
										<th class="text-center" style='width: 7%;'>Product</th>
										<th class="text-center" style='width: 86%;' colspan='6'>Specification</th>
										<th class="text-center" style='width: 4%;'>#</th>
										
									</tr>
								</thead>
								<tbody id='detail_body'>
									<?php
										$no = 0;
										foreach($RestReqCust AS $val => $valx){
											$no++;
											?>
											<tr>
												<td style='text-align: center; vertical-align: middle;'><?= $no;?></td>
												<td style='text-align: center; vertical-align: middle;'>
													<select name='ListDetailEdit[<?= $no;?>][product]' id='product' class='form-control input-sm chosen-select'>
														<option value='FRP' <?= ($valx['product'] == 'FRP')?'selected':'';?>>FRP</option>
														<option value='RPM' <?= ($valx['product'] == 'RPM')?'selected':'';?>>RPM</option>
													</select>
													<input type='hidden' name='ListDetailEdit[<?= $no;?>][id]' value='<?= $valx['id'];?>'>
												</td>
												<td style='text-align: right;' width='13%'>
													<div class='labDet'>RESIN</div>
														<select name='ListDetailEdit[<?= $no;?>][type_resin]' id='type_resin_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='GRP(FRP)' <?= ($valx['type_resin'] == 'GRP(FRP)')?'selected':'';?>>GRP (RFP)</option>
															<option value='GRV' <?= ($valx['type_resin'] == 'GRV')?'selected':'';?>>GRV</option>
														</select>
													<div class='labDet'>VACUM RATE</div>
														<select name='ListDetailEdit[<?= $no;?>][vacum_rate]' id='vacum_rate_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='NON VACUUM' <?= ($valx['vacum_rate'] == 'NON VACUUM')?'selected':'';?>>NON VACUUM</option>
															<option value='HALF VACUUM' <?= ($valx['vacum_rate'] == 'HALF VACUUM')?'selected':'';?>>HALF VACUUM</option>
															<option value='FULL VACUUM' <?= ($valx['vacum_rate'] == 'FULL VACUUM')?'selected':'';?>>FULL VACUUM</option>
														</select>
													<div class='labDet'>DOCUMENT</div>
														<input class='form-check-input docClass' id='document_<?= $no;?>' name='ListDetailEdit[<?= $no;?>][document]' type='checkbox' data-no='<?= $no;?>' value='Y' <?= ($valx['document'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;YES</label>
														<?php $InShow	= ($valx['document'] == 'Y')?'block':'none';?>
													<div id='documentCh_<?= $no;?>' style='display: <?= $InShow;?>;'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][document_1]' id='document_1_<?= $no;?>' class='form-control input-sm' placeholder='Document 1' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['document_1']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][document_2]' id='document_2_<?= $no;?>' class='form-control input-sm' placeholder='Document 2' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['document_2']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][document_3]' id='document_3_<?= $no;?>' class='form-control input-sm' placeholder='Document 3' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['document_3']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][document_4]' id='document_4_<?= $no;?>' class='form-control input-sm' placeholder='Document 4' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['document_4']));?>'>
													</div>
												</td>
												<td style='text-align: right;' width='13%'>
													<div class='labDet'>FLUIDA</div>
														<select name='ListDetailEdit[<?= $no;?>][id_fluida]' id='id_fluida_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<?php
																foreach($restFluida AS $val => $valxF){
																	$selF	= ($valxF['id_fluida'] == $valx['id_fluida'])?'selected':'';
																	echo "<option value='".$valxF['id_fluida']."' ".$selF.">".ucwords(strtolower($valxF['fluida_name']))."</option>";
																}
															?>
														</select>
													<div class='labDet'>TIME LIFE</div>
														<select name='ListDetailEdit[<?= $no;?>][time_life]' id='time_life_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='20' <?= ($valx['time_life'] == '20')?'selected':'';?>>20 Year</option>
															<option value='25' <?= ($valx['time_life'] == '25')?'selected':'';?>>25 Year</option>
															<option value='30' <?= ($valx['time_life'] == '30')?'selected':'';?>>30 Year</option>
														</select>
													<div class='labDet'>CERTIFICATE</div>
														<input class='form-check-input serClass' id='sertifikat_<?= $no;?>' name='ListDetailEdit[<?= $no;?>][sertifikat]' data-no='<?= $no;?>' type='checkbox' value='Y' <?= ($valx['sertifikat'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;YES</label>
														<?php $InShow2	= ($valx['sertifikat'] == 'Y')?'block':'none';?>
													<div id='sertifikatCh_<?= $no;?>' style='display: <?= $InShow2;?>;'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][sertifikat_1]' id='sertifikat_1_<?= $no;?>' class='form-control input-sm' placeholder='Certificate 1' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['sertifikat_1']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][sertifikat_2]' id='sertifikat_2_<?= $no;?>' class='form-control input-sm' placeholder='Certificate 2' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['sertifikat_2']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][sertifikat_3]' id='sertifikat_3_<?= $no;?>' class='form-control input-sm' placeholder='Certificate 3' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['sertifikat_3']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][sertifikat_4]' id='sertifikat_4_<?= $no;?>' class='form-control input-sm' placeholder='Certificate 4' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['sertifikat_4']));?>'>
													</div>					
												</td> 
												<td style='text-align: right;' width='13%'>
													<div class='labDet'>PRESSURE</div>
														<select name='ListDetailEdit[<?= $no;?>][pressure]' id='pressure_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='6' <?= ($valx['pressure'] == '6')?'selected':'';?>>6 Bar</option>
															<option value='8' <?= ($valx['pressure'] == '8')?'selected':'';?>>8 Bar</option>
															<option value='10' <?= ($valx['pressure'] == '10')?'selected':'';?>>10 Bar</option>
															<option value='12' <?= ($valx['pressure'] == '12')?'selected':'';?>>12 Bar</option>
															<option value='16' <?= ($valx['pressure'] == '16')?'selected':'';?>>16 Bar</option>
															<option value='18' <?= ($valx['pressure'] == '18')?'selected':'';?>>18 Bar</option>
															<option value='20' <?= ($valx['pressure'] == '20')?'selected':'';?>>20 Bar</option>
														</select>
													<div class='labDet'>CONDUCTIVE</div>
														<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][konduksi_liner]' type='checkbox' value='Y' <?= ($valx['konduksi_liner'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;LINER</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' type='checkbox' name='ListDetailEdit[<?= $no;?>][konduksi_structure]' value='Y' <?= ($valx['konduksi_structure'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;STR</label><br>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][konduksi_eksternal]' type='checkbox' value='Y' <?= ($valx['konduksi_eksternal'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;EKS</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][konduksi_topcoat]' type='checkbox' value='Y' <?= ($valx['konduksi_topcoat'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;TC</label>

													<div class='labDet'>TESTING</div>
														<input class='form-check-input testClass' id='test_"+intd+"_"+num+"' name='ListDetailEdit[<?= $no;?>][test]' type='checkbox'  data-no='<?= $no;?>' value='Y' <?= ($valx['test'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;YES</label>
														<?php $InShow3	= ($valx['test'] == 'Y')?'block':'none';?>
													<div id='testCh_<?= $no;?>' style='display: <?= $InShow3;?>;'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][test_1]' id='test_1_<?= $no;?>' class='form-control input-sm' placeholder='Testing 1' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['test_1']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][test_2]' id='test_2_<?= $no;?>' class='form-control input-sm' placeholder='Testing 2' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['test_2']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][test_3]' id='test_3_<?= $no;?>' class='form-control input-sm' placeholder='Testing 3' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['test_3']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][test_4]' id='test_4_<?= $no;?>' class='form-control input-sm' placeholder='Testing 4' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['test_4']));?>'>
													</div>
												</td>
												<td style='text-align: right;' width='17%'>
													<div class='labDet'>STIFNESS</div>
													<select name='ListDetailEdit[<?= $no;?>][stifness]' id='stifness_<?= $no;?>' data-no='<?= $no;?>' class='form-control chosen-select input-sm stifness' required>
														<option value='1250' <?= ($valx['stifness'] == '1250')?'selected':'';?>>1250 Pa</option>
														<option value='2500' <?= ($valx['stifness'] == '2500')?'selected':'';?>>2500 Pa</option>
														<option value='5000' <?= ($valx['stifness'] == '5000')?'selected':'';?>>5000 Pa</option>
														<option value='10000' <?= ($valx['stifness'] == '10000')?'selected':'';?>>10000 Pa</option>
													</select>

													<div class='labDet'>FIRE RETARDANT</div><input class='form-check-input' name='ListDetailEdit[<?= $no;?>][tahan_api_liner]' type='checkbox' value='Y' <?= ($valx['tahan_api_liner'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;LINER</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' type='checkbox' name='ListDetailEdit[<?= $no;?>][tahan_api_structure]' value='Y' <?= ($valx['tahan_api_structure'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;STR</label><br>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][tahan_api_eksternal]' type='checkbox' value='Y' <?= ($valx['tahan_api_eksternal'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;EKS</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][tahan_api_topcoat]' type='checkbox' value='Y' <?= ($valx['tahan_api_topcoat'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;TC</label>
													
													<div class='labDet'>RESIN REQ CUSTOMER</div>
													<input type='text' name='ListDetailEdit[<?= $no;?>][resin_req_cust]' id='resin_req_cust_<?= $no;?>' class='form-control input-sm' placeholder='Resin Request Customer' value='<?= ucwords(strtolower($valx['resin_req_cust']));?>'>
												</td>
												<td style='text-align: right;' width='13%'>
													<div class='labDet'>APPLICATION</div>
														<input type='text' class='form-control input-sm' style='text-align: center;' name='ListDetailEdit[<?= $no;?>][aplikasi]' id='aplikasi_<?= $no;?>' autocomplete='off' placeholder='Application' readonly='readonly' value='<?= $valx['aplikasi'];?>'>
													<div class='labDet'>ABRASIVE</div>
														<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][abrasi]' type='checkbox' value='Y' <?= ($valx['abrasi'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;YES</label>

													<div class='labDet'>COLOR</div>
														<input class='form-check-input colorClass' id='color_<?= $no;?>' name='ListDetailEdit[<?= $no;?>][color]'  data-no='<?= $no;?>' type='checkbox' value='Y' <?= ($valx['color'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;YES</label>
														<?php $InShow4	= ($valx['color'] == 'Y')?'block':'none';?>
													<div id='colorCh_<?= $no;?>' style='display: <?= $InShow4;?>;'>
														<select name='ListDetailEdit[<?= $no;?>][color_liner]' id='color_liner_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='0'>Color Liner</option>
															<?php
																foreach($restColor AS $val => $valxF){
																	$selF	= ($valxF['color_name'] == $valx['color_liner'])?'selected':'';
																	echo "<option value='".$valxF['color_name']."' ".$selF.">".ucwords(strtolower($valxF['color_name']))."</option>";
																}
															?>
														</select>
														<select name='ListDetailEdit[<?= $no;?>][color_structure]' id='color_structure_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='0'>Color Str</option>
															<?php
																foreach($restColor AS $val => $valxF){
																	$selF	= ($valxF['color_name'] == $valx['color_structure'])?'selected':'';
																	echo "<option value='".$valxF['color_name']."' ".$selF.">".ucwords(strtolower($valxF['color_name']))."</option>";
																}
															?>
														</select>
														<select name='ListDetailEdit[<?= $no;?>][color_external]' id='color_external_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='0'>Color Eks</option>
															<?php
																foreach($restColor AS $val => $valxF){
																	$selF	= ($valxF['color_name'] == $valx['color_external'])?'selected':'';
																	echo "<option value='".$valxF['color_name']."' ".$selF.">".ucwords(strtolower($valxF['color_name']))."</option>";
																}
															?>
														</select>
														<select name='ListDetailEdit[<?= $no;?>][color_topcoat]' id='color_topcoat_<?= $no;?>' class='form-control input-sm chosen-select' required>
															<option value='0'>Color TC</option>
															<?php
																foreach($restColor AS $val => $valxF){
																	$selF	= ($valxF['color_name'] == $valx['color_topcoat'])?'selected':'';
																	echo "<option value='".$valxF['color_name']."' ".$selF.">".ucwords(strtolower($valxF['color_name']))."</option>";
																}
															?>
														</select>
													</div>
												</td>
												<td style='text-align: right;' width='17%'>
													<div class='labDet'>SATNDARD SPEC</div>
														<input class='form-check-input' type='checkbox' name='ListDetailEdit[<?= $no;?>][std_asme]' value='Y' <?= ($valx['std_asme'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;ASME</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][std_ansi]' type='checkbox' value='Y' <?= ($valx['std_ansi'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;ANSI</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][std_astm]' type='checkbox' value='Y' <?= ($valx['std_astm'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;ASTM</label><br>
														<input class='form-check-input' type='checkbox' name='ListDetailEdit[<?= $no;?>][std_awwa]' value='Y' <?= ($valx['std_awwa'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;AWWA</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][std_bsi]' type='checkbox' value='Y' <?= ($valx['std_bsi'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;BSI</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][std_jis]' type='checkbox' value='Y' <?= ($valx['std_jis'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;JIS</label>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input' name='ListDetailEdit[<?= $no;?>][std_sni]' type='checkbox' value='Y' <?= ($valx['std_sni'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;SNI</label><br>
														&nbsp;&nbsp;&nbsp;&nbsp;<input class='form-check-input etcClass' name='ListDetailEdit[<?= $no;?>][std_etc]'  data-no='<?= $no;?>' id='std_etc_<?= $no;?>' type='checkbox' value='Y' <?= ($valx['std_etc'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;ETC</label><br>
														<?php $InShow5	= ($valx['std_etc'] == 'Y')?'block':'none';?>
													<div id='etcCh_<?= $no;?>' style='display: <?= $InShow5;?>;'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][etc_1]' id='etc_1_<?= $no;?>' class='form-control input-sm' placeholder='Standard Etc 1' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['etc_1']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][etc_2]' id='etc_2_<?= $no;?>' class='form-control input-sm' placeholder='Standard Etc 2' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['etc_2']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][etc_3]' id='etc_3_<?= $no;?>' class='form-control input-sm' placeholder='Standard Etc 3' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['etc_3']));?>'>
														<input type='text' name='ListDetailEdit[<?= $no;?>][etc_4]' id='etc_4_<?= $no;?>' class='form-control input-sm' placeholder='Standard Etc 4' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['etc_4']));?>'>
													</div>
													<textarea name='ListDetailEdit[<?= $no;?>][note]' id='note_<?= $no;?>' rows='2' class='form-control input-sm' placeholder='Note'><?= ucwords(strtolower($valx['note']));?></textarea>

													<div class='labDet'>COLOR REQUEST</div>
														<input class='form-check-input tcClass' name='ListDetailEdit[<?= $no;?>][ck_minat_warna_tc]' type='checkbox'  data-no='<?= $no;?>' value='Y' <?= ($valx['ck_minat_warna_tc'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;TOP COATED</label>
														<input class='form-check-input pigClass' name='ListDetailEdit[<?= $no;?>][ck_minat_warna_pigment]' type='checkbox'  data-no='<?= $no;?>' value='Y' <?= ($valx['ck_minat_warna_pigment'] == 'Y')?'checked':'';?>>
														<label class='form-check-label'>&nbsp;PIGMENTED</label>
														<?php $InShow3tc	= ($valx['ck_minat_warna_tc'] == 'Y')?'block':'none';?>
														<div id='tcCh_<?= $no;?>' style='display: <?= $InShow3tc;?>;'>
															<input type='text' name='ListDetailEdit[<?= $no;?>][minat_warna_tc]' id='minat_warna_tc_<?= $no;?>' class='form-control input-sm' placeholder='Top Coated Color' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['minat_warna_tc']));?>'>
														</div>
														<?php $InShow3pig	= ($valx['ck_minat_warna_pigment'] == 'Y')?'block':'none';?>
														<div id='pigCh_<?= $no;?>' style='display: <?= $InShow3pig;?>;'>
															<input type='text' name='ListDetailEdit[<?= $no;?>][minat_warna_pigment]' id='minat_warna_pigment_<?= $no;?>' class='form-control input-sm' placeholder='Pigmented Color' style='margin-bottom: 3px;' value='<?= ucwords(strtolower($valx['minat_warna_pigment']));?>'>
														</div>				
												</td>
												<td></td>
											</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box box-warning">
				<div class="box-header">
					<h3 class="box-title">Delivery</h3>
				</div>
				<div class="box-body">
					<div class='form-group row'> 
						<label class='label-control col-sm-2'><b>Country of Destination <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='country_code' id='country_code' class='form-control input-md chosen-select'>
							 <?php
								foreach($CountryName AS $val => $valx){
									$selxx = ($valx['country_code'] == $RestShipping[0]->country_code)?'selected':'';
									echo "<option value='".$valx['country_code']."' ".$selxx.">".$valx['country_name']."</option>";
								}
							 ?>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Delivery Date <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							echo form_input(array('id'=>'date_delivery','name'=>'date_delivery','class'=>'form-control input-md','style'=>'cursor:pointer','placeholder'=>'Delivery Date', 'readonly'=>'readonly'), $RestShipping[0]->date_delivery);
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Delivery Address <span class='text-red'>*</span></b></label>
						<div class='col-sm-10'>
							<?php
							 echo form_textarea(array('id'=>'address_delivery','name'=>'address_delivery','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Delivery Address'), strtoupper($RestShipping[0]->address_delivery));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Shipping Method <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='metode_delivery' id='metode_delivery' class='form-control input-md chosen-select'>
							 <?php
								foreach($ShippingName AS $val => $valx){
									$selxx2 = ($valx['shipping_name'] == $RestShipping[0]->metode_delivery)?'selected':'';
									echo "<option value='".$valx['shipping_name']."' ".$selxx2.">".$valx['shipping_name']."</option>";
								}
							 ?>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Packing <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='packing' id='packing' class='form-control input-md chosen-select'>
							 <?php
								foreach($PackningName AS $val => $valx){
									$selxx3 = ($valx['packing_name'] == $RestShipping[0]->packing)?'selected':'';
									echo "<option value='".$valx['packing_name']."' ".$selxx3.">".$valx['packing_name']."</option>";
								}
							 ?>
							 </select>
						</div>
					</div>
					<div id='HideShipping'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'><b>Trucking <span class='text-red'>*</span></b></label>
							<div class='col-sm-3'>
								<select name='truck' id='truck' class='form-control input-md chosen-select'>
									<option>Select An Trucking</option>
								 <?php
									foreach($ShippingName AS $val => $valx){
										echo "<option value='".$valx['shipping_name']."'>".$valx['shipping_name']."</option>";
									}
								 ?>
								 </select>
							</div>
							<div class='col-sm-1'>
								 <?php
									echo form_input(array('id'=>'qty_truck','name'=>'qty_truck','maxlength'=>'3','style'=>'text-align:center;','class'=>'form-control input-md numberOnly','placeholder'=>'Qty','autocomplete'=>'off'));
								?>
							</div>
							<label class='label-control col-sm-2'><b>Vendor <span class='text-red'>*</span></b></label>
							<div class='col-sm-4'>
								<select name='vendor' id='vendor' class='form-control input-md chosen-select'>
									<option>Select An Vendor</option>
								 <?php
									foreach($PackningName AS $val => $valx){
										echo "<option value='".$valx['packing_name']."'>".$valx['packing_name']."</option>";
									}
								 ?>
								 </select>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Handling Equipment <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='alat_berat' id='alat_berat' class='form-control input-md chosen-select'>
								<option value='BY ORI' <?= ($RestShipping[0]->alat_berat == 'BY ORI')?'selected':'';?>>BY ORI</option>
								<option value='BY CUSTOMER' <?= ($RestShipping[0]->alat_berat == 'BY CUSTOMER')?'selected':'';?>>BY CUSTOMER</option>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Instalation <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='isntalasi_by' id='isntalasi_by' class='form-control input-md chosen-select'>
								<option value='BY ORI' <?= ($RestShipping[0]->isntalasi_by == 'BY ORI')?'selected':'';?>>BY ORI</option>
								<option value='BY CUSTOMER' <?= ($RestShipping[0]->isntalasi_by == 'BY CUSTOMER')?'selected':'';?>>BY CUSTOMER</option>
							 </select>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Validity & Guarantee <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							echo form_input(array('id'=>'garansi','name'=>'garansi','maxlength'=>'3','class'=>'form-control input-md numberOnly','placeholder'=>'Validity & Guarantee / Year','autocomplete'=>'off'), $RestShipping[0]->garansi);
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='box-footer'> 
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
<style type="text/css">
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}

	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
	.labDet{
		font-weight: bold;
		margin: 10px 0px 3px 0px;
		text-align: end;
		color: #ea1919;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen-select').chosen();
		// swal({
		  // title	: "Warning!",
		  // text	: 'Program Development Process',
		  // type	: "warning"
		// });
		
		$('#HideShipping').hide();


		$(document).on('change', '.docClass' , function(){
			if(this.checked) {
				$("#documentCh_"+$(this).data('no')).show();
			}
			else{
				$("#documentCh_"+$(this).data('no')).hide();
			}
		});
		
		$(document).on('change', '.serClass' , function(){
			if(this.checked) {
				$("#sertifikatCh_"+$(this).data('no')).show();
			}
			else{
				$("#sertifikatCh_"+$(this).data('no')).hide();
			}
		});
		
		$(document).on('change', '.testClass' , function(){
			if(this.checked) {
				$("#testCh_"+$(this).data('no')).show();
			}
			else{
				$("#testCh_"+$(this).data('no')).hide();
			}
		});
		
		$(document).on('change', '.colorClass' , function(){
			if(this.checked) {
				$("#colorCh_"+$(this).data('no')).show();
			}
			else{
				$("#colorCh_"+$(this).data('no')).hide();
			}
		});
		
		$(document).on('change', '.etcClass' , function(){
			if(this.checked) {
				$("#etcCh_"+$(this).data('no')).show();
			}
			else{
				$("#etcCh_"+$(this).data('no')).hide();
			}
		});

		$(document).on('change', '.tcClass' , function(){
			if(this.checked) {
				$("#tcCh_"+$(this).data('no')).show();
			}
			else{
				$("#tcCh_"+$(this).data('no')).hide();
			}
		});

		$(document).on('change', '.pigClass' , function(){
			if(this.checked) {
				$("#pigCh_"+$(this).data('no')).show();
			}
			else{
				$("#pigCh_"+$(this).data('no')).hide();
			}
		});

		
		$(document).on('change', '.stifness', function(){
			if($(this).val() == '1250'){
				$('#aplikasi_'+$(this).data('no')).val('ABOVE GROUND');
			}
			else if($(this).val() == '2500' || $(this).val() == '5000' || $(this).val() == '10000'){
				$('#aplikasi_'+$(this).data('no')).val('UNDER GROUND');
			}
			else{
				$('#aplikasi_'+$(this).data('no')).val('');
			}
		});
		
		$(document).on('change', '#std_etc', function(){
			if(this.checked){
				$('#StandardHide').show();
			}
			else{
				$('#StandardHide').hide();
			}
		});
		
		$('#date_delivery').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var id_customer			= $('#id_customer').val();
			var project				= $('#project').val();
			var country_code		= $('#country_code').val();
			var date_delivery		= $('#date_delivery').val();
			var address_delivery	= $('#address_delivery').val();
			var metode_delivery		= $('#metode_delivery').val();
			var packing				= $('#packing').val();
			var alat_berat			= $('#alat_berat').val();
			var isntalasi_by		= $('#isntalasi_by').val();
			var garansi				= $('#garansi').val();

			if(id_customer=='' || id_customer==null || id_customer=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Customer name is not chosen, please chose first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(project=='' || project==null || project=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Project is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(max_tol=='' || max_tol==null || max_tol=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Maximal Tolerance is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(min_tol=='' || min_tol==null || min_tol=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Minimal Tolerance is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			//NEW
			if(validity=='' || validity==null ){
				swal({
				  title	: "Error Message!",
				  text	: 'Validity & Guarantee is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(payment=='' || payment==null ){
				swal({
				  title	: "Error Message!",
				  text	: 'Payment Term is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(ref_cust=='' || ref_cust==null ){
				swal({
				  title	: "Error Message!",
				  text	: 'Referensi Customer/Project is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(country_code=='' || country_code==null || country_code=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Country delivery is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(date_delivery=='' || date_delivery==null || date_delivery=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery date is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(address_delivery=='' || address_delivery==null || address_delivery=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery address is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(metode_delivery=='' || metode_delivery==null || metode_delivery=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery methode is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(packing=='' || packing==null || packing=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Delivery packing is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(alat_berat=='' || alat_berat==null || alat_berat=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Handling Equipment is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(isntalasi_by=='' || isntalasi_by==null || isntalasi_by=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Instalation by is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(garansi=='' || garansi==null || garansi=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Validity & Guarantee is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
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
						var formData 	=new FormData($('#form_proses_bro')[0]);
						var baseurl=base_url + active_controller +'/edit_ipp';
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
										  timer	: 7000
										});
									window.location.href = base_url + active_controller;
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000
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
	
	function validateEmail(sEmail) {
		var numericExpression = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if (sEmail.match(numericExpression)) {
			return true;
		}
		else {
			return false;
		}
	}
</script>
