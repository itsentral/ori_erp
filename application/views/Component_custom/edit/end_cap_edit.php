<?php
$this->load->view('include/side_menu'); 
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<div class="box-body">
			<?php
				echo "&nbsp;<a href='".site_url($this->uri->segment(1))."' style='min-width:100px;margin-bottom:-35px;float: right;' class='btn btn-md btn-danger' title='Back To List' data-role='qtip'>Back</a>";
			?>						
			<div class='headerTitleGroup'>GROUP COMPONENT</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm Hide','readonly'=>'readonly'),$header[0]->nm_product);
						echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'Hide'),$header[0]->diameter);
						echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'Hide'),$header[0]->id_product);
						echo form_input(array('id'=>'parent_product','name'=>'parent_product','class'=>'Hide'),$header[0]->parent_product);
					?>	
					<select name='top_typeList' id='top_typeList' class='form-control input-sm' disabled>
						<option value='0'>Select Diameter</option>
						<?php
							foreach($product AS $val => $valx){
								$selx	= ($header[0]->id == $valx['id'])?'selected':'';
								echo "<option value='".$valx['id']."' ".$selx.">".ucfirst(strtolower($valx['nm_product']))."</option>";
							}
						 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Series <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='series' id='series' class='form-control input-sm'>
					<?php
						foreach($series AS $val => $valx){
							$selx	= (substr($header[0]->series, 0,5) == $valx['seriesx'])?'selected':'';
							echo "<option value='".$valx['seriesx']."' ".$selx.">".strtoupper($valx['seriesx'])."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Make Custom ? <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='cust' id='cust' class='form-control input-sm'>
					<?php
						$SelS = (!empty($header[0]->cust))?$header[0]->cust:'C100-1903000';
						foreach($standard AS $val => $valx){
							
							$selXX = ($SelS == $valx['id_customer'])?'selected':'';
							echo "<option value='".$valx['id_customer']."' ".$selXX.">".strtoupper($valx['nm_customer'])."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Keterangan</b></label>
				<div class='col-sm-4'>              
					<?php
						echo form_input(array('type'=>'text','id'=>'ket_plus','name'=>'ket_plus','class'=>'form-control input-sm','placeholder'=>'Isi dengan singkat / kode', 'maxlength'=>'10'), $header[0]->ket_plus);	
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Standard Default<span class='text-red'>*</span></b></label>  
				<div class='col-sm-3'>
					<?php
						echo form_input(array('id'=>'standart_code','name'=>'standart_code','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly'), $header[0]->standart_code);											
					?>
				</div>
				<div class='col-sm-1'>
					<?php
						echo "<button type='button' name='updateDefault' id='updateDefault' class='btn btn-sm btn-success' data-standart_code='".$header[0]->standart_code."' data-parent_product='".str_replace(' ', '-', $header[0]->parent_product)."' data-diameter='".$header[0]->diameter."' data-id_product='".$header[0]->id_product."'>Default</button>";
					?>
				</div>
				
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'></label>  
				<div class='col-sm-4'>              
					<div id='tamp' style='font-weight: bold; background-color: #f1f1f1; padding: 1px 0px 0px 8px;border-radius: 0px 10px 10px 0px;'></div>
				</div>
				
			</div>
			<!-- /////// -->
			<div class='headerTitleGroup'>SPESIFIKASI COMPONENT</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Fluida <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='criminal_barier' id='criminal_barier' class='form-control input-sm'>
					<?php
						foreach($criminal_barier AS $val => $valx){
							$selx	= ($header[0]->criminal_barier == $valx['name'])?'selected':'';
							echo "<option value='".$valx['name']."' ".$selx.">".strtoupper(strtolower($valx['name']))."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Stiffness <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='aplikasi_product' id='aplikasi_product' class='form-control input-sm'>
					<?php
						foreach($aplikasi_product AS $val => $valx){
							$dtApp	= explode(" ", $valx['name']);
							$selx	= (substr($header[0]->stiffness, 2,5) == $dtApp[1])?'selected':'';
							echo "<option value='".$valx['name']."' ".$selx.">".strtoupper($valx['data2'])."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Vacuum Rate <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='vacum_rate' id='vacum_rate' class='form-control input-sm'>
					<?php
						foreach($vacum_rate AS $val => $valx){
							$selx	= ($header[0]->vacum_rate == $valx['data1'])?'selected':'';
							echo "<option value='".$valx['data1']."' ".$selx.">".$valx['name']."</option>";
						}
					 ?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Design Life <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='design_life' id='design_life' class='form-control input-sm'>
					<?php
						foreach($design_life AS $val => $valx){
							$selx	= ($header[0]->design_life == $valx['name'])?'selected':'';
							echo "<option value='".$valx['name']."' ".$selx.">".strtoupper(strtolower($valx['name']))."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Application<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>           
					<?php
					if($header[0]->aplikasi_product == 'ABOVE GROUND'){$selc = 'selected';}else{$selc = '';}
					if($header[0]->aplikasi_product == 'UNDER GROUND'){$selc2 = 'selected';}else{$selc2 = '';}
					?>
					<select id='top_app' name='top_app' class='form-control input-sm'>
						<option value='ABOVE GROUND' <?= $selc;?>>ABOVE GROUND</option>
						<option value='UNDER GROUND' <?= $selc2;?>>UNDER GROUND</option>
					</select>
				</div>
			</div>
			<!-- //// -->
			<div class='headerTitleGroup'>DETAILED ESTIMATION</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Thickness Design | Est <span class='text-red'>*</span></b></label>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('type'=>'hidden','id'=>'length','name'=>'length','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Pipe Length'), floatval($header[0]->panjang));				
						echo form_input(array('id'=>'design','name'=>'design','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Pipe Thickness (Design)'),floatval($header[0]->design));											
					?>	
				</div>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'estimasi','name'=>'estimasi','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly'), $header[0]->est);											
					?>	
				</div>
				
				<label class='label-control col-sm-2'><b>Waste | Min Max</b></label>
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm numberOnly','placeholder'=>'Waste','readonly'=>'readonly'), floatval($header[0]->waste));	
						echo form_input(array('type'=>'hidden','id'=>'toleransi','name'=>'toleransi','class'=>'form-control input-sm','readonly'=>'readonly'), $header[0]->standart_by);
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'min_toleran','name'=>'min_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Mix','readonly'=>'readonly'), $header[0]->min_toleransi);
						echo form_input(array('type'=>'text','id'=>'area','name'=>'area','class'=>'HideCost'), $header[0]->area);
						echo form_input(array('type'=>'text','id'=>'overlap','name'=>'overlap','class'=>'HideCost'));
						// echo form_input(array('type'=>'text','id'=>'ThLin','name'=>'ThLin','class'=>'HideCost'), str_replace(',', '.', $header[0]->liner));
						// echo form_input(array('type'=>'text','id'=>'ThStr','name'=>'ThStr','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'AddLinNum','name'=>'AddLinNum','class'=>'HideCost'), $detLinerNumRows);
						echo form_input(array('type'=>'text','id'=>'AddStrNum','name'=>'AddStrNum','class'=>'HideCost'), $detStructureNumRows);
						echo form_input(array('type'=>'text','id'=>'AddEksNum','name'=>'AddEksNum','class'=>'HideCost'), $detEksternalNumRows);
						echo form_input(array('type'=>'text','id'=>'AddTcNum','name'=>'AddTcNum','class'=>'HideCost'), $detTopNumRows);
						
						echo form_input(array('type'=>'text','id'=>'standart_codex','name'=>'standart_codex','class'=>'HideCost'), $header[0]->standart_code);
						
						echo form_input(array('type'=>'text','id'=>'lin_faktor_veil','lin_faktor_veil'=>'area','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'lin_faktor_veil_add','name'=>'lin_faktor_veil_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'lin_faktor_csm','name'=>'lin_faktor_csm','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'lin_faktor_csm_add','lin_faktor_csm_add'=>'area','class'=>'HideCost'));
						
						echo form_input(array('type'=>'text','id'=>'str_faktor_csm','name'=>'str_faktor_csm','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_csm_add','name'=>'str_faktor_csm_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_wr','name'=>'str_faktor_wr','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_wr_add','name'=>'str_faktor_wr_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_rv','name'=>'str_faktor_rv','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'str_faktor_rv_add','name'=>'str_faktor_rv_add','class'=>'HideCost'));
						
						echo form_input(array('type'=>'text','id'=>'eks_faktor_veil','name'=>'eks_faktor_veil','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'eks_faktor_veil_add','name'=>'eks_faktor_veil_add','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'eks_faktor_csm','name'=>'eks_faktor_csm','class'=>'HideCost'));
						echo form_input(array('type'=>'text','id'=>'eks_faktor_csm_add','name'=>'eks_faktor_csm_add','class'=>'HideCost'));
					
					?>
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'max_toleran','name'=>'max_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Max','readonly'=>'readonly'), $header[0]->max_toleransi);											
					?>	
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Radius</b></label> 
				<div class='col-sm-2'>              
					<?php
						echo form_input(array('id'=>'radius','name'=>'radius','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'OD /mm'), floatval($header[0]->radius));											
					?>	
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<!-- ============================================LINER THICKNESS=========================================== -->
			<!-- ====================================================================================================== -->
			<div class='headerTitle'>LINER THIKNESS / CB</div>
			<input type='text' name='detail_name' id='detail_name' class='HideCost' value='LINER THIKNESS / CB'>
			<div class="box box-primary">
				<div class='col-sm-3' style='margin: 10px 0px 10px 0px;'>
					<b>Thickness Liner : </b>
					<select name='ThLin' id='ThLin' class='form-control input-sm' style='width:100px;'>
						<?php
						foreach(get_list_liner() AS $val => $valx){
							$selx	= ($header[0]->liner == $valx['data1'])?'selected':'';
							echo "<option value='".$valx['data1']."' ".$selx.">".$valx['name']."</option>";
						}
						?>
					</select>
				</div>
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<head>
							<tr class='bg-blue'>
								<th class="text-center" width='15%'>Type</th>
								<th class="text-center">Material</th>
								<th class="text-center" width='8%'>Weight</th>
								<th class="text-center" width='8%'>Layer</th>
								<th class="text-center" width='8%'>Rs.Cont</th>
								<th class="text-center" width='8%'>Thickness</th>
								<th class="text-center" width='8%'>Last Weight</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detLiner AS $val => $valx){
									$no++;
									$ListdetLin	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									// $spaceX	= ($valx['id_ori'] == 'TYP-0001')?'&nbsp;&nbsp;&nbsp;&nbsp;':'';
									if($ListdetLin[0]['category'] == 'RESIN'){
										$valY	= "";
										$layY	= "";
										$ThY	= "";
									}
									else{
										if($ListdetLin[0]['category'] == 'REALESE AGENT'){
											$valY	= "<input type='text' name='ListDetail[".$no."][value]' id='value_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".floatval($valx['value'])."'>";
											$layY	= "";
											$ThY	= "";
										
										}
										else{
											$valY	= "<input type='text' name='ListDetail[".$no."][value]' id='value_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".floatval($valx['value'])."'>";
											$layY	= "<input type='text' name='ListDetail[".$no."][layer]' id='layer_".$no."' data-nomor='".$no."' data-type='".$valx['id_ori']."' class='form-control input-sm numberOnly layer' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
											$ThY	= "<input type='text' name='ListDetail[".$no."][total_thickness]' id='total_thickness_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['total_thickness']."'>";
										}
									}
									
									if($ListdetLin[0]['category'] != 'RESIN' AND $ListdetLin[0]['category'] != 'REALESE AGENT'){
										$RsY	= "";
		
									}
									else{
										$RsY	= "<input type='text' name='ListDetail[".$no."][containing]' id='containing_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['containing']."'>";
									}
									
									// $AddClass	= '';
									// if($valx['id_ori'] == 'TYP-0001' AND $valx['id_ori'] != 'TYP-0008'){
										// $AddClass	= 'resinCls'; 
									// }
									?>
									<tr>
										<td>
											<span id='hideCty_<?=$no;?>'><?= $ListdetLin[0]['category'];?></span>
											<input type='text' name='ListDetail[<?=$no;?>][id_detail]' class='HideCost' id='id_detail_<?=$no;?>' value='<?=$valx['id_detail'];?>'>
											<input type='text' name='ListDetail[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
										</td>
										<td>
											<select name='ListDetail[<?=$no;?>][id_material]' id='id_material_<?=$no;?>' data-nomor='<?=$no;?>' class='form-control input-sm id_material'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetLin AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>
											<input type='text' name='ListDetail[<?=$no;?>][id_material2]' class='HideCost' id='id_material2_<?=$no;?>' value='<?=$valx['id_material'];?>'>
											<input type='text' name='ListDetail[<?=$no;?>][thickness]' class='HideCost' id='thickness_<?=$no;?>' value='<?=$valx['thickness'];?>'>
											<input type='text' name='ListDetail[<?=$no;?>][last_full]' class='HideCost' id='last_full_<?=$no;?>' value='<?=$valx['last_full'];?>'>
											<input type='text' name='ListDetail[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetail[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										</td>
										<td align='center'><?= $valY;?></td>
										<td align='center'><?= $layY;?></td>
										<td align='center'><?= $RsY;?></td>
										<td align='center'><?= $ThY;?></td>
										<td align='center'><input type='text' name='ListDetail[<?=$no;?>][last_cost]' id='last_cost_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						</tbody>
						<head>
							<tr class='bg-blue'>
								<td class="text-center">Type</td>
								<td class="text-center" colspan='3'>Material</td>
								<td class="text-center">Containing</td>
								<td class="text-center">Perse (%)</td>
								<td class="text-center">Last Weight</td>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detLinerPlus AS $val => $valx){
									$no++;
									$ListdetLinPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									
									?>
									<tr>
										<td><?= $ListdetLinPlus[0]['category'];?><input type='hidden' name='ListDetailPlus[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
										<td colspan='3'>
											<select name='ListDetailPlus[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetLinPlus AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>
											<input type='text' name='ListDetailPlus[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetailPlus[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										</td>
										<td align='center'><input type='text' name='ListDetailPlus[<?=$no;?>][containing]' id='Lincontaining_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus[<?=$no;?>][perse]' id='Linperse_<?=$no;?>' class='form-control input-sm numberOnly perse' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus[<?=$no;?>][last_cost]' id='Linlast_cost_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
										
									</tr>
									<?php
								}
							?>
						</tbody>
						<!-- LINER MATERIAL ADD-->
							<?php
							if($detLinerNumRows > 0){
								?>
								<tbody>
									<?php
									$no=0;
									foreach($detLinerAdd AS $val => $valx){
										$no++;
										$ListdetLinAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetLinAdd[0]['category'];?><input type='hidden' name='ListDetailAdd[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
											<td colspan='3'>
												<select name='ListDetailAdd[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
												<?php
													$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
													foreach($ListdetLinAdd AS $vala => $valxa){
														$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
														echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
													}
													echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
												 ?>
												</select>	
											</td>
											<td align='center'><input type='text' name='ListDetailAdd[<?=$no;?>][containing]' id='Addcontaining_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd[<?=$no;?>][perse]' id='Addperse_<?=$no;?>' class='form-control input-sm numberOnly perseLinAdd' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd[<?=$no;?>][last_cost]' id='Addlast_cost_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
										</tr>
										<?php
									}
								?>
								</tbody>
							<?php
							}
						?>
					</table>
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody id='detail_body_liner'></tbody>
					</table>
					<br>
					<button type='button' name='add_liner' id='add_liner' class='btn btn-success btn-sm' style='float:right: width:150px;'>Add Material</button>
					<br>
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody>
							<tr>
								<td width='70%' class="text-center"></td>
								<td width='22%' class="text-left"style='vertical-align: middle;'><b>LINER THICKNESS</b></td>
								<td width='8%' align='center'><input type='text' name='thickLin' id='thickLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['total'];?>'></td>
							</tr>
							<tr>
								<td class="text-center"></td>
								<td class="text-left"style='vertical-align: middle;'><b>MIN LINER THICKNESS</b></td>
								<td align='center'><input type='text' name='minLin' id='minLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['min'];?>'></td>
							</tr>
							<tr>
								<td class="text-center"></td>
								<td class="text-left" style='vertical-align: middle;'><b>MAX LINER THICKNESS</b></td>
								<td align='center'><input type='text' name='maxLin' id='maxLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['max'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='2'></td>
								<td align='center'><input type='text' name='hasilLin' id='hasilLin' class='form-control input-sm' readonly='readonly' style='text-align:center; width:80px;' value='<?= $footer[0]['hasil'];?>'></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class='headerTitle'>STRUCTURE THIKNESS</div>
			<input type='text' name='detail_name2' id='detail_name2' class='HideCost' value='STRUKTUR THICKNESS'>
			<div class="box box-primary">
				<?php
					echo "<b>Thickness Structure : </b>".form_input(array('type'=>'text','id'=>'ThStr','name'=>'ThStr','class'=>'form-control input-sm numberOnly','style'=>'width:150px; text-align:center;','readonly'=>'readonly'),$detStructure[0]['acuhan'] );
				?>
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"> 
						<head>
							<tr class='bg-blue'>
								<th class="text-center"  width='15%'>Type</th>
								<th class="text-center">Material</th>
								<th class="text-center">Weight</th>
								<th class="text-center">Layer</th>
								<th class="text-center">Rs.Cont</th>
								<th class="text-center">Thickness</th>
								<th class="text-center">Last Weight</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detStructure AS $val => $valx){
									$no++;
									$ListdetStructure	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									
									if($ListdetStructure[0]['category'] == 'RESIN'){
										$valY	= "";
										$layY	= "";
										$ThY	= "";
									}
									else{
										$valY	= "<input type='text' name='ListDetail2[".$no."][value]' id='valueStr_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".floatval($valx['value'])."'>";
										$layY	= "<input type='text' name='ListDetail2[".$no."][layer]' id='layerStr_".$no."' data-type='".$valx['id_ori']."' data-nomor='".$no."' class='form-control input-sm numberOnly layerStr' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
										$ThY	= "<input type='text' name='ListDetail2[".$no."][total_thickness]' id='total_thicknessStr_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['total_thickness']."'>";
									}
									
									if($ListdetStructure[0]['category'] <> 'RESIN'){
										$RsY	= "";
									}
									else{
										$RsY	= "<input type='text' name='ListDetail2[".$no."][containing]' id='containingStr_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['containing']."'>";
									}
									?>
									<tr>
										<td>
											<span id='hideCty2_<?=$no;?>'><?= $ListdetStructure[0]['category'];?></span>
											<input type='text' name='ListDetail2[<?=$no;?>][id_detail]' class='HideCost' id='id_detailStr_<?=$no;?>' value='<?=$valx['id_detail'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][id_ori]' class='HideCost' id='id_oriStr_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											
											<input type='text' name='ListDetail2[<?=$no;?>][bw]' class='HideCost' id='bwStr_<?=$no;?>' value='<?=$valx['bw'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][jumlah]' class='HideCost' id='jumlahStr_<?=$no;?>' value='<?=$valx['jumlah'];?>'>
										</td>
										<td>
											<select name='ListDetail2[<?=$no;?>][id_material]' id='id_materialStr_<?=$no;?>' data-nomor='<?=$no;?>' class='form-control input-sm id_materialSTr'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetStructure AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>
											<input type='text' name='ListDetail2[<?=$no;?>][id_material2]' class='HideCost' id='id_materialStr2_<?=$no;?>' value='<?=$valx['id_material'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][thickness]' class='HideCost' id='thicknessStr_<?=$no;?>' value='<?=$valx['thickness'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][last_full]' class='HideCost' id='last_fullStr_<?=$no;?>' value='<?=$valx['last_full'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										</td>
										<td align='center'><?= $valY;?></td>
										<td align='center'><?= $layY;?></td>
										<td align='center'><?= $RsY;?></td>
										<td align='center'><?= $ThY;?></td>
										<td align='center'><input type='text' name='ListDetail2[<?=$no;?>][last_cost]' id='last_costStr_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
							</tbody>
							<head>
								<tr class='bg-blue'>
									<td class="text-center">Type</td>
									<td class="text-center" colspan='3'>Material</td>
									<td class="text-center">Containing</td>
									<td class="text-center">Perse (%)</td>
									<td class="text-center">Last Weight</td>
								</tr>
							</head>
							<tbody>
							<?php
								$no=0;
								foreach($detStructurePlus AS $val => $valx){
									$no++;
									$ListdetStructurePlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td><?= $ListdetStructurePlus[0]['category'];?><input type='hidden' name='ListDetailPlus2[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
										<td colspan='3'>
											<select name='ListDetailPlus2[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetStructurePlus AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
											<input type='text' name='ListDetailPlus2[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetailPlus2[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										</td>
										<td align='center'><input type='text' name='ListDetailPlus2[<?=$no;?>][containing]' id='Lincontaining2_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus2[<?=$no;?>][perse]' id='Linperse2_<?=$no;?>' class='form-control input-sm numberOnly perseStr' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus2[<?=$no;?>][last_cost]' id='Linlast_cost2_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						</tbody>
						<!-- LINER MATERIAL ADD-->
							<?php
							if($detLinerNumRows > 0){
								?>
								<tbody>
									<?php
									$no=0;
									foreach($detStructureAdd AS $val => $valx){
										$no++;
										$ListdetStructureAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetStructureAdd[0]['category'];?><input type='hidden' name='ListDetailAdd2[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
											<td colspan='3'>
												<select name='ListDetailAdd2[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
												<?php
													$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
													foreach($ListdetStructureAdd AS $vala => $valxa){
														$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
														echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
													}
													echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
												 ?>
												</select>	
											</td>
											<td align='center'><input type='text' name='ListDetailAdd2[<?=$no;?>][containing]' id='Addcontaining2_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=$valx['containing'];?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd2[<?=$no;?>][perse]' id='Addperse2_<?=$no;?>' class='form-control input-sm numberOnly perseStrAdd' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd2[<?=$no;?>][last_cost]' id='Addlast_cost2_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
										</tr>
										<?php
									}
								?>
								</tbody>
							<?php
							}
						?>
						</table>
						<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<tbody id='detail_body_strukture'></tbody>
						</table>
						<br>
						<button type='button' name='add_strukture' id='add_strukture' class='btn btn-success btn-sm' style='float:right: width:150px;'>Add Material</button>
						<br>
						<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<tr>
								<td width='70%' class="text-center" colspan='4'></td>
								<td width='22%'class="text-left" colspan='2' style='vertical-align: middle;'><b>STRUCTURE THICKNESS</b></td>
								<td width='8%'align='center'><input type='text' name='thickStr' id='thickStr' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerStructure[0]['total'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='4'></td>
								<td class="text-left" colspan='2' style='vertical-align: middle;'><b>MIN STRUCTURE THICKNESS</b></td>
								<td align='center'><input type='text' name='minStr' id='minStr' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerStructure[0]['min'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='4'></td>
								<td class="text-left" colspan='2' style='vertical-align: middle;'><b>MAX STRUCTURE THICKNESS</b></td>
								<td align='center'><input type='text' name='maxStr' id='maxStr' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerStructure[0]['max'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='6'></td>
								<td align='center'><input type='text' name='hasilStr' id='hasilStr' class='form-control input-sm' readonly='readonly' style='text-align:center; width:80px;' value='<?= $footerStructure[0]['hasil'];?>'></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class='headerTitle'>EXTERNAL THIKNESS</div>
			<input type='text' name='detail_name3' id='detail_name3' class='HideCost' value='EXTERNAL LAYER THICKNESS'>
			<div class="box box-primary">
				<?php
					echo "<b>Thickness External : </b>".form_input(array('type'=>'text','id'=>'ThEks','name'=>'ThEks','class'=>'form-control input-sm numberOnly','style'=>'width:150px; text-align:center;'), $detEksternal[0]['acuhan']);
				?>
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<head>
							<tr class='bg-blue'>
								<th class="text-center"  width='15%'>Type</th>
								<th class="text-center">Material</th>
								<th class="text-center">Weight</th>
								<th class="text-center">Layer</th>
								<th class="text-center">Rs.Cont</th>
								<th class="text-center">Thickness</th>
								<th class="text-center">Last Weight</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detEksternal AS $val => $valx){
									$no++;
									$ListdetEksternal	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									// $spaceX	= ($valx['id_ori'] == 'TYP-0001')?'&nbsp;&nbsp;&nbsp;&nbsp;':'';
									if($ListdetEksternal[0]['category'] == 'RESIN'){
										$valY	= "";
										$layY	= "";
										$ThY	= "";
									}
									else{
										$valY	= "<input type='text' name='ListDetail3[".$no."][value]' id='valueEks_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".floatval($valx['value'])."'>";
										$layY	= "<input type='text' name='ListDetail3[".$no."][layer]' id='layerEks_".$no."' data-nomor='".$no."' data-type='".$valx['id_ori']."' class='form-control input-sm numberOnly layerEks' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
										$ThY	= "<input type='text' name='ListDetail3[".$no."][total_thickness]' id='total_thicknessEks_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['total_thickness']."'>";
									}
									
									if($ListdetEksternal[0]['category'] != 'RESIN'){
										$RsY	= "";
									}
									else{
										$RsY	= "<input type='text' name='ListDetail3[".$no."][containing]' id='containingEks_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['containing']."'>";
									}
									?>
									<tr>
										<td>
											<span id='hideCty3_<?=$no;?>'><?= $ListdetEksternal[0]['category'];?></span>
											<input type='text' name='ListDetail3[<?=$no;?>][id_detail]' class='HideCost' value='<?=$valx['id_detail'];?>'>
											<input type='text' name='ListDetail3[<?=$no;?>][id_ori]' class='HideCost' id='id_oriEks_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
										</td>
										<td>
											<select name='ListDetail3[<?=$no;?>][id_material]' id='id_materialEks_<?=$no;?>' data-nomor='<?=$no;?>' class='form-control input-sm id_materialEks'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetEksternal AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
											<input type='text' name='ListDetail3[<?=$no;?>][id_material2]' class='HideCost' id='id_material2Eks_<?=$no;?>' value='<?=$valx['id_material'];?>'>
											<input type='text' name='ListDetail3[<?=$no;?>][thickness]' class='HideCost' id='thicknessEks_<?=$no;?>' value='<?=$valx['thickness'];?>'>
											<input type='text' name='ListDetail3[<?=$no;?>][last_full]' class='HideCost' id='last_fullEks_<?=$no;?>' value='<?=$valx['last_full'];?>'>
											<input type='text' name='ListDetail3[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetail3[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										</td>
										<td align='center'><?= $valY;?></td>
										<td align='center'><?= $layY;?></td>
										<td align='center'><?= $RsY;?></td>
										<td align='center'><?= $ThY;?></td>
										<td align='center'><input type='text' name='ListDetail3[<?=$no;?>][last_cost]' id='last_costEks_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
							</tbody>
							<head>
								<tr class='bg-blue'>
									<td class="text-center">Type</td>
									<td class="text-center" colspan='3'>Material</td>
									<td class="text-center">Containing</td>
									<td class="text-center">Perse (%)</td>
									<td class="text-center">Last Weight</td>
								</tr>
							</head>
							<tbody>
							<?php
								$no=0;
								foreach($detEksternalPlus AS $val => $valx){
									$no++;
									$ListdetEksternalPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									
									$CONTAINING = (!empty($valx['containing']))?floatval($valx['containing']):0;
									if($CONTAINING == 0){
										$CONTAINING = getContainingEditEstimasi($no);
									}
									?>
									<tr>
										<td><?= $ListdetEksternalPlus[0]['category'];?><input type='hidden' name='ListDetailPlus3[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
										<td colspan='3'>
											<select name='ListDetailPlus3[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetEksternalPlus AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
											<input type='text' name='ListDetailPlus3[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetailPlus3[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										</td>
										<td align='center'><input type='text' name='ListDetailPlus3[<?=$no;?>][containing]' id='Lincontaining3_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= $CONTAINING;?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus3[<?=$no;?>][perse]' id='Linperse3_<?=$no;?>' class='form-control input-sm numberOnly perseEks' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus3[<?=$no;?>][last_cost]' id='Linlast_cost3_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
										
									</tr>
									<?php
								}
							?>
						</tbody>
						<!-- LINER MATERIAL ADD-->
							<?php
							if($detEksternalNumRows > 0){
								?>
								<tbody>
									<?php
									$no=0;
									foreach($detEksternalAdd AS $val => $valx){
										$no++;
										$ListdetStructureAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetStructureAdd[0]['category'];?><input type='hidden' name='ListDetailAdd3[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
											<td colspan='3'>
												<select name='ListDetailAdd3[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
												<?php
													$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
													foreach($ListdetStructureAdd AS $vala => $valxa){
														$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
														echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
													}
													echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
												 ?>
												</select>	
											</td>
											<td align='center'><input type='text' name='ListDetailAdd3[<?=$no;?>][containing]' id='Addcontaining3_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd3[<?=$no;?>][perse]' id='Addperse3_<?=$no;?>' class='form-control input-sm numberOnly perseEksAdd' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd3[<?=$no;?>][last_cost]' id='Addlast_cost3_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
											
											
										</tr>
										<?php
									}
								?>
								</tbody>
							<?php
							}
						?>
						</table>
						<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<tbody id='detail_body_external'></tbody>
						</table>
						<br>
						<button type='button' name='add_external' id='add_external' class='btn btn-success btn-sm' style='float:right: width:150px;'>Add Material</button>
						<br>
						<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<tr>
								<td width='70%' class="text-center" colspan='4'></td>
								<td width='22%'class="text-left" colspan='2' style='vertical-align: middle;'><b>EXTERNAL THICKNESS</b></td>
								<td width='8%' align='center'><input type='text' name='thickEks' id='thickEks' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerEksternal[0]['total'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='4'></td>
								<td class="text-left" colspan='2' style='vertical-align: middle;'><b>MIN EXTERNAL THICKNESS</b></td>
								<td align='center'><input type='text' name='minEks' id='minEks' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerEksternal[0]['min'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='4'></td>
								<td class="text-left" colspan='2' style='vertical-align: middle;'><b>MAX EXTERNAL THICKNESS</b></td>
								<td align='center'><input type='text' name='maxEks' id='maxEks' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerEksternal[0]['max'];?>'></td>
							</tr>
							<tr>
								<td class="text-center" colspan='6'></td>
								<td align='center'><input type='text' name='hasilEks' id='hasilEks' class='form-control input-sm' readonly='readonly' style='text-align:center; width:80px;' value='<?= $footerEksternal[0]['hasil'];?>'></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class='headerTitle'>TOPCOAT</div>
			<input type='text' name='detail_name4' id='detail_name4'  class='HideCost' value='TOPCOAT'>
			<div class="box box-primary">
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
							<head>
								<tr class='bg-blue'>
									<td class="text-center" width='15%'>Type</td>
									<td class="text-center" colspan='3'>Material</td>
									<td class="text-center">Containing</td>
									<td class="text-center">Perse (%)</td>
									<td class="text-center">Last Weight</td>
								</tr>
							</head>
							<tbody>
							<?php
								$no=0;
								foreach($detTopPlus AS $val => $valx){
									$no++;
									$ListdetTopPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' OR a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td><?= $ListdetTopPlus[0]['category'];?><input type='hidden' name='ListDetailPlus4[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
										<td colspan='3'>
											<select name='ListDetailPlus4[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetTopPlus AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
											<input type='text' name='ListDetailPlus4[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
											<input type='text' name='ListDetailPlus4[<?=$no;?>][id_ori2]' class='HideCost' id='id_ori2_<?=$no;?>' value='<?=$valx['id_ori2'];?>'>
										
										</td>
										<td align='center'><input type='text' name='ListDetailPlus4[<?=$no;?>][containing]' id='cont_topcoat_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus4[<?=$no;?>][perse]' id='perse_topcoat_<?=$no;?>' class='form-control input-sm numberOnly perseTc' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
										<td align='center'><input type='text' name='ListDetailPlus4[<?=$no;?>][last_cost]' id='last_topcoat_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
										
									</tr>
									<?php
								}
							?>
							</tbody>
						<!-- LINER MATERIAL ADD-->
							<?php
							if($detTopNumRows > 0){
								?>
								<tbody>
									<?php
									$no=0;
									foreach($detTopAdd AS $val => $valx){
										$no++;
										$ListdetTopAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetTopAdd[0]['category'];?><input type='hidden' name='ListDetailAdd4[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
											<td colspan='3'>
												<select name='ListDetailAdd4[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm'>
												<?php
													$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
													foreach($ListdetTopAdd AS $vala => $valxa){
														$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
														echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
													}
													echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
												 ?>
												</select>	
											</td>
											<td align='center'><input type='text' name='ListDetailAdd4[<?=$no;?>][containing]' id='Addcontaining4_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=$valx['containing'];?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd4[<?=$no;?>][perse]' id='Addperse4_<?=$no;?>' class='form-control input-sm numberOnly perseTcAdd' style='text-align:right; width:80px;' value='<?= floatval($valx['perse']);?>'></td>
											<td align='center'><input type='text' name='ListDetailAdd4[<?=$no;?>][last_cost]' id='Addlast_cost4_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly'  style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
										</tr>
										<?php
									}
								?>
								</tbody>
							<?php
							}
						?>
					</table>
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<tbody id='detail_body_topcoat'></tbody>
					</table>
					<br>
					<button type='button' name='add_topcoat' id='add_topcoat' class='btn btn-success btn-sm' style='float:right: width:150px;'>Add Material</button>
					<br>
				</div>
			</div>
			<?php
				echo "&nbsp;<a href='".site_url('component_custom')."' style='min-width:100px;float: right;' class='btn btn-md btn-danger' title='Back To List' data-role='qtip'>Back</a>";
				echo "&nbsp;<button type='button' name='simpan-bro' style='min-width:100px; margin-bottom:-35px; float: right; margin-right:10px;' id='simpan-bro' class='btn btn-success'>Save</button>";
				
			?>	
		</div>	
	</div>		
		 <!-- modal -->
		<div class="modal fade" id="ModalView">
			<div class="modal-dialog"  style='width:40%; '>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="head_title"></h4>
						</div>
						<div class="modal-body" id="view">
						</div>
						<div class="modal-footer">
						<!--<button type="button" class="btn btn-primary">Save</button>-->
						<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal -->
		
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Component/edit/javascript/end_cap.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<style type="text/css">
	
	label{
		    font-size: small !important;
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
	#app_pipe_chosen{
		width: 100% !important;
	}
	#standard_pipe_chosen{
		width: 100% !important;
	}
	.headerTitle{
		text-align: center;
		background-color: #296753;
		height: 30px;
		font-weight: bold;
		padding-top: 5px;
		margin-bottom: 15px;
		margin-top: 30px;
		color: white;
	}
	
	.headerTitleGroup{
		text-align: center;
		background-color: #47a997;
		height: 30px;
		font-weight: bold;
		padding-top: 5px;
		margin-bottom: 15px;
		margin-top: 30px;
		color: white;
	}
	
	#id_material_3_chosen,
	#id_material_5_chosen,
	#id_material_7_chosen,
	#id_material_9_chosen,
	
	#id_materialStr_2_chosen,
	#id_materialStr_4_chosen,
	#id_materialStr_6_chosen,
	#id_materialStr_8_chosen,
	#id_materialStr_10_chosen,
	#id_materialStr_12_chosen,
	
	#id_materialEks_2_chosen,
	#id_materialEks_4_chosen,
	#id_materialEks_6_chosen,
	#id_materialEks_8_chosen{ 
		display : none;	
	}
	
	#hideCty_3,#hideCty_5,#hideCty_7,#hideCty_9{
		display : none;
	}
	#hideCty2_2,#hideCty2_4,#hideCty2_6,#hideCty2_8,#hideCty2_10,#hideCty2_12{
		display : none;
	}
	#hideCty3_2,#hideCty3_4,#hideCty3_6,#hideCty3_8{
		display : none;
	}
	#perse_topcoat_1{
		display : none; 
	}
</style>
<script>	
	$(document).ready(function(){
		$(document).on('click', '#updateDefault', function(e){
			e.preventDefault();
			$("#head_title").html("<b>DATA DEFAULT "+$(this).data('id_product')+"</b>");
			// $("#view").load(base_url +'index.php/'+ active_controller+'/modalEditEstDefault/'+$(this).data('id_product'));
			$("#view").load(base_url +'index.php/component_custom/modalEditEstDefault/'+$(this).data('standart_code')+'/'+$(this).data('parent_product')+'/'+$(this).data('diameter')+'/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#editDefaultSave', function(){
			var max				= $('#max').val();
			var min				= $('#min').val();
			var plastic_film	= $('#plastic_film').val();
			var waste			= $('#waste').val();
			var overlap			= $('#overlap').val();
			
			if(plastic_film == '' || plastic_film == null || plastic_film == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Plastic Faktor is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;	
			}
			if(waste == '' || waste == null || waste == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Waste is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;	
			}
			if(overlap == '' || overlap == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Overlap is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
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
					var formData  	= new FormData($('#form_proses_bro2')[0]);
					$.ajax({
						url			: base_url+'index.php/json_help/editDefaultEst',
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
									  timer	: 5000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								window.location.href = base_url+active_controller+'/'+data.helpx+'/'+data.id_product;
								
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000,
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
							  timer				: 5000,
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
		
		var parent_product	= $('#parent_product').val();
		var diameter		= $('#diameter').val();
		var standart_code	= $('#standart_code').val();
		var id_product	= 	$('#id_product').val();

		$.ajax({
			url: base_url +'index.php/json_help/getDefaultOri',
			cache: false,
			type: "POST",
			// data: "id_product="+$('#id_product').val(),
			data: "diameter="+diameter+"&standart="+standart_code+"&parent_product="+parent_product+"&id_product="+id_product,
			dataType: "json",
			success: function(data){ 
				$('#waste').val(data.waste);
				$('#overlap').val(data.overlap);
				$('#max_toleran').val(data.maxx);
				$('#min_toleran').val(data.minx);
				$('#containing_1').val(data.plastic_film);
				
				$('#containing_3').val(data.lin_resin_veil);
				$('#containing_5').val(data.lin_resin_veil_add);
				$('#containing_7').val(data.lin_resin_csm);
				$('#containing_9').val(data.lin_resin_csm_add);
				$('#containing_10').val(data.lin_resin);
				
				$('#containingStr_2').val(data.str_resin_csm);
				$('#containingStr_4').val(data.str_resin_csm_add);
				$('#containingStr_6').val(data.str_resin_wr);
				$('#containingStr_8').val(data.str_resin_wr_add);
				$('#containingStr_10').val(data.str_resin_rv);
				$('#containingStr_12').val(data.str_resin_rv_add);
				$('#containingStr_13').val(data.str_resin);
				
				$('#bwStr_9').val(data.str_faktor_rv_bw);  
				$('#jumlahStr_9').val(data.str_faktor_rv_jb); 
				
				$('#bwStr_11').val(data.str_faktor_rv_add_bw);
				$('#jumlahStr_11').val(data.str_faktor_rv_add_jb);
				
				$('#containingEks_2').val(data.eks_resin_veil);
				$('#containingEks_4').val(data.eks_resin_veil_add);
				$('#containingEks_6').val(data.eks_resin_csm);
				$('#containingEks_8').val(data.eks_resin_csm_add);
				
				$('#containingEks_9').val(data.eks_resin);
				
				$('#perse_topcoat_1').val(data.topcoat_resin);
				$('#cont_topcoat_1').val(data.topcoat_resin);
				
				
				$('#lin_faktor_veil').val(data.lin_faktor_veil);
				$('#lin_faktor_veil_add').val(data.lin_faktor_veil_add);
				$('#lin_faktor_csm').val(data.lin_faktor_csm);
				$('#lin_faktor_csm_add').val(data.lin_faktor_csm_add);
				
				$('#str_faktor_csm').val(data.str_faktor_csm);
				$('#str_faktor_csm_add').val(data.str_faktor_csm_add);
				$('#str_faktor_wr').val(data.str_faktor_wr);
				$('#str_faktor_wr_add').val(data.str_faktor_wr_add);
				$('#str_faktor_rv').val(data.str_faktor_rv);
				$('#str_faktor_rv_add').val(data.str_faktor_rv_add);
				
				$('#eks_faktor_veil').val(data.eks_faktor_veil);
				$('#eks_faktor_veil_add').val(data.eks_faktor_veil_add);
				$('#eks_faktor_csm').val(data.eks_faktor_csm);
				$('#eks_faktor_csm_add').val(data.eks_faktor_csm_add);

				ChangeLuasArea();
				ChangeHasil();				
			}
		});
	
				
		$('.HideCost').hide();
		$('.Hide').hide();
		
		var design 	= parseFloat($('#design').val());
		var ThLin 	= parseFloat($('#ThLin').val());
		var ThEks 	= parseFloat($('#ThEks').val());
		var HasilT	= design - (ThLin + ThEks);
		$('#ThStr').val(HasilT.toFixed(2));
		
		$(document).on('change', '#ThLin', function(){
			// alert('Key');
			var liner 		= parseFloat($(this).val());
			var ekternal 	= parseFloat($('#ThEks').val());
			var design 		= parseFloat($('#design').val()); 
			
			var ThStr	= design - (liner + ekternal);
			if(isNaN(ThStr)){
				var ThStr = 0;
			}
			$('#ThStr').val(ThStr.toFixed(2)); 
			ChangeHasil();
		});
		
		$(document).on('keyup', '#ThEks', function(){
			var ekternal 	= parseFloat($(this).val());
			var liner 		= parseFloat($('#ThLin').val());
			var design 		= parseFloat($('#design').val()); 
			
			var ThStr	= design - (liner + ekternal);
			if(isNaN(ThStr)){
				var ThStr = 0;
			}
			$('#ThStr').val(ThStr.toFixed(2)); 
			ChangeHasil();
		});
		
		$(document).on('keyup', '#design', function(){
			var design 	= parseFloat($('#design').val()); 
			var ThLin 	= parseFloat($('#ThLin').val());
			var ThEks	= parseFloat($('#ThEks').val());
			var ThStr	= design - (ThLin + ThEks);
			if(isNaN(ThStr)){
				var ThStr = 0;
			}
			$('#ThStr').val(ThStr.toFixed(2));
			ChangeLuasArea();			
			ChangeHasil();
		});
		
		$(document).on('change', '#standart_code', function(e){
			e.preventDefault();
			var dim				= $('#diameter').val();
			var parent_product		= $('#parent_product').val();
			
			$.ajax({
				url: base_url +'index.php/'+ active_controller+'/getDefault',
				cache: false,
				type: "POST",
				data: "dim="+dim+"&std="+$(this).val()+"&parent_product="+parent_product,
				dataType: "json",
				success: function(data){
					$('#waste').val(data.waste);
					$('#overlap').val(data.overlap);
					$('#max_toleran').val(data.maxx);
					$('#min_toleran').val(data.minx);
					$('#containing_1').val(data.plastic_film);
					
					$('#containing_3').val(data.lin_resin_veil);
					$('#containing_5').val(data.lin_resin_veil_add);
					$('#containing_7').val(data.lin_resin_csm);
					$('#containing_9').val(data.lin_resin_csm_add);
					$('#containing_10').val(data.lin_resin);
					
					$('#containingStr_2').val(data.str_resin_csm);
					$('#containingStr_4').val(data.str_resin_csm_add);
					$('#containingStr_6').val(data.str_resin_wr);
					$('#containingStr_8').val(data.str_resin_wr_add);
					$('#containingStr_10').val(data.str_resin_rv);
					$('#containingStr_12').val(data.str_resin_rv_add);
					
					$('#bwStr_9').val(data.str_faktor_rv_bw);  
					$('#jumlahStr_9').val(data.str_faktor_rv_jb);
					
					$('#bwStr_11').val(data.str_faktor_rv_add_bw);
					$('#jumlahStr_11').val(data.str_faktor_rv_add_jb);
					// $('#str_resin').val(data.str_resin);
					
					$('#containingEks_2').val(data.eks_resin_veil);
					$('#containingEks_4').val(data.eks_resin_veil_add);
					$('#containingEks_6').val(data.eks_resin_csm);
					$('#containingEks_8').val(data.eks_resin_csm_add);
					
					// $('#eks_resin').val(data.eks_resin);
					
					$('#perse_topcoat_1').val(data.topcoat_resin);
					
					
					$('#lin_faktor_veil').val(data.lin_faktor_veil);
					$('#lin_faktor_veil_add').val(data.lin_faktor_veil_add);
					$('#lin_faktor_csm').val(data.lin_faktor_csm);
					$('#lin_faktor_csm_add').val(data.lin_faktor_csm_add);
					
					$('#str_faktor_csm').val(data.str_faktor_csm);
					$('#str_faktor_csm_add').val(data.str_faktor_csm_add);
					$('#str_faktor_wr').val(data.str_faktor_wr);
					$('#str_faktor_wr_add').val(data.str_faktor_wr_add);
					$('#str_faktor_rv').val(data.str_faktor_rv);
					$('#str_faktor_rv_add').val(data.str_faktor_rv_add);
					
					$('#eks_faktor_veil').val(data.eks_faktor_veil);
					$('#eks_faktor_veil_add').val(data.eks_faktor_veil_add);
					$('#eks_faktor_csm').val(data.eks_faktor_csm);
					$('#eks_faktor_csm_add').val(data.eks_faktor_csm_add);
					
					ChangeLuasArea();
					ChangeHasil();
				}
			});
		});	
		
		$(document).on('keyup', '#ThEks', function(){
			ChangeHasil();
		});
		
		$(document).on('click', '#simpan-bro', function(e){
			e.preventDefault(); 
			
			var top_tebal_design	= $('#design').val();
			
			$(this).prop('disabled',true);
			
			if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Thickness Pipe Design is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			
			
			var hasil_linier_thickness 	= $('#hasilLin').val();
			var hasil_linier_thickness2 = $('#hasilStr').val();
			var hasil_linier_thickness3 = $('#hasilEks').val();
			
			if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Thickness is To High or To Low, please check back ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			} 
			
			$('#simpan-bro').prop('disabled',false);
			
			swal({
				  title: "Peringatan !!!",
				  text: "ID Product yang sama akan digantikan inputan ini !!!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonClass: "btn-danger",
				  confirmButtonText: "Ya, Proses",
				  cancelButtonText: "Tidak, Batalkan",
				  closeOnConfirm: false,
				  closeOnCancel: false
				},
				function(isConfirm) {
				  if (isConfirm) {
						loading_spinner();
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/end_cap_edit';
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
									window.location.href = base_url + 'component_custom';
								}
								else if(data.status == 2){
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
								else{
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
					swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
					$('#simpan-bro').prop('disabled',false); 
					return false;
				  }
			});
		});
		
	});
</script>
