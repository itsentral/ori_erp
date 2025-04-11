<?php
$id_bq				= $this->uri->segment(3);
$id_milik			= $this->uri->segment(4);

$header				= $this->db->query("SELECT a.*, b.id FROM bq_component_header a LEFT JOIN product b ON a.nm_product = b.nm_product WHERE a.id_bq='".$id_bq."' AND a.id_milik='".$id_milik."' LIMIT 1 ")->result();

$series				= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

$product			= $this->db->query("SELECT * FROM product WHERE parent_product='pipe slongsong' AND deleted='N'")->result_array();
$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

$criminal_barier	= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
$aplikasi_product	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
$vacum_rate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
$design_life		= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();
$customer			= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

$detLiner			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerPlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$footer				= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->num_rows();

$detStructure			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructurePlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructureAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructureNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->num_rows();
$footerStructure		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
?>

<div class="box box-primary">
	<div class="box-body">				
		<div class='headerTitleGroup'>GROUP COMPONENT</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<?php
					echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm Hide','readonly'=>'readonly'),$header[0]->nm_product);
					echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'Hide'),$header[0]->diameter);
					echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'Hide'),$header[0]->id_product);
					echo form_input(array('id'=>'id_bq','name'=>'id_bq','class'=>'Hide'),$id_bq);
					echo form_input(array('id'=>'id_milik','name'=>'id_milik','class'=>'Hide'),$id_milik);
					echo form_input(array('id'=>'series','name'=>'series','class'=>'Hide'),$header[0]->series);
					echo form_input(array('id'=>'rev','name'=>'rev','class'=>'Hide'),$header[0]->rev);
					echo form_input(array('id'=>'status','name'=>'status','class'=>'Hide'),$header[0]->status);
					echo form_input(array('id'=>'sts_price','name'=>'sts_price','class'=>'Hide'),$header[0]->sts_price);
					echo form_input(array('id'=>'toleransi','name'=>'toleransi','class'=>'Hide'),$header[0]->standart_by); 
					echo form_input(array('id'=>'url_help','name'=>'url_help','class'=>'Hide'),$this->uri->segment(5));
				?>	
				<select name='top_typeList' id='top_typeList' class='form-control input-sm chosen-select' disabled>
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
				<select name='seriesX' id='seriesX' class='form-control input-sm chosen-select' disabled>
				<?php
					foreach($series AS $val => $valx){
						$selx	= ($header[0]->series == $valx['kode_group'])?'selected':'';
						echo "<option value='".$valx['kode_group']."' ".$selx.">".strtoupper($valx['kode_group'])."</option>";
					}
				 ?>
				</select>
			</div>
		</div>
		<!-- /////// -->
		<div class='headerTitleGroup'>SPESIFIKASI COMPONENT</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Fluida <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<select name='criminal_barier' id='criminal_barier' class='form-control input-sm chosen-select'>
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
				<select name='aplikasi_product' id='aplikasi_product' class='form-control input-sm chosen-select'>
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
				<select name='vacum_rate' id='vacum_rate' class='form-control input-sm chosen-select'>
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
				<select name='design_life' id='design_life' class='form-control input-sm chosen-select'>
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
				<select id='top_app' name='top_app' class='form-control input-sm chosen-select'>
					<option value='ABOVE GROUND' <?= $selc;?>>ABOVE GROUND</option>
					<option value='UNDER GROUND' <?= $selc2;?>>UNDER GROUND</option>
				</select>
			</div>
		</div>
		<!-- //// -->
		<div class='headerTitleGroup'>DETAILED ESTIMATION</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Standard Tolerance By <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<select name='toleransiX' id='toleransiX' class='form-control input-sm' disabled>
				<?php
					foreach($customer AS $val => $valx){
						$selx	= ($header[0]->standart_by == $valx['id_customer'])?'selected':'';
						echo "<option value='".$valx['id_customer']."' ".$selx.">".strtoupper(strtolower($valx['nm_customer']))."</option>";
					}
				 ?>
				</select>
			</div>
			
			<label class='label-control col-sm-2'><b>Waste | Thickness (EST)</b></label>
			<div class='col-sm-2'>              
				<?php
					echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm numberOnly','placeholder'=>'Waste','readonly'=>'readonly'), floatval($header[0]->waste));	
				?>	
			</div>
			<div class='col-sm-2'>              
				<?php 
					echo form_input(array('id'=>'estimasi','name'=>'estimasi','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly'), $header[0]->est);
					echo form_input(array('type'=>'text','id'=>'area','name'=>'area','class'=>'HideCost'), $header[0]->area);
					echo form_input(array('type'=>'text','id'=>'ThLin','name'=>'ThLin','class'=>'HideCost'), str_replace(',', '.', $header[0]->liner));
					// echo form_input(array('type'=>'text','id'=>'ThStr','name'=>'ThStr','class'=>'HideCost'));
					echo form_input(array('type'=>'text','id'=>'AddLinNum','name'=>'AddLinNum','class'=>'HideCost'), $detLinerNumRows);
					echo form_input(array('type'=>'text','id'=>'AddStrNum','name'=>'AddStrNum','class'=>'HideCost'), $detStructureNumRows);
				
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Thickness Design <span class='text-red'>*</span></b></label>
			<div class='col-sm-2'>              
				<?php
					echo form_input(array('type'=>'hidden','id'=>'length','name'=>'length','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Pipe Length'), floatval($header[0]->panjang));				
					echo form_input(array('id'=>'design','name'=>'design','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Pipe Thickness (Design)'),floatval($header[0]->design));											
				?>	
			</div>
			<div class='col-sm-2'>              
				
			</div>
			
			<div class='ToleranSt'>
				<label class='label-control col-sm-2'><b>Min | Max Standard <span class='text-red'>*</span></b></label>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'min_toleran','name'=>'min_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Mix'), $header[0]->min_toleransi);											
					?>	
				</div>
				<div class='col-sm-1'>              
					<?php
						echo form_input(array('id'=>'max_toleran','name'=>'max_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Max'), $header[0]->max_toleransi);											
					?>	
				</div>
			</div>
		</div>
		<!-- ====================================================================================================== -->
		<!-- ============================================LINER THICKNESS=========================================== -->
		<!-- ====================================================================================================== -->
		<div class='headerTitle'>LINER THIKNESS / CB</div>
		<input type='text' name='detail_name' id='detail_name' class='HideCost' value='LINER THIKNESS / CB'>
		<div class="box box-primary">
			<div class="box-body" style="">
				<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
					<head>
						<tr class='bg-blue'>
							<th class="text-center" width='15%'>Type</th>
							<th class="text-center">Material</th>
							<th class="text-center" width='7%'>Weight</th>
							<th class="text-center" width='7%'>Layer</th>
							<th class="text-center" width='7%'>Rs.Cont</th>
							<th class="text-center" width='7%'>Thickness</th>
							<th class="text-center" width='7%'>Last Weight</th>
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
										$layY	= "<input type='text' name='ListDetail[".$no."][layer]' id='layer_".$no."' data-nomor='".$no."' class='form-control input-sm numberOnly layer' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
										$ThY	= "<input type='text' name='ListDetail[".$no."][total_thickness]' id='total_thickness_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".$valx['total_thickness']."'>";
									}
								}
								
								if($ListdetLin[0]['category'] != 'RESIN'){
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
										<select name='ListDetail[<?=$no;?>][id_material]' id='id_material_<?=$no;?>' data-nomor='<?=$no;?>' class='form-control input-sm id_material chosen-select'>
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
								$ListdetLinPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
								
								?>
								<tr>
									<td><?= $ListdetLinPlus[0]['category'];?><input type='hidden' name='ListDetailPlus[<?=$no;?>][id_detail]' value='<?=$valx['id_detail'];?>'></td>
									<td colspan='3'>
										<select name='ListDetailPlus[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
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
											<select name='ListDetailAdd[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetLinPlus AS $vala => $valxa){
													$selx	= ($ListdetLinAdd[0]['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
										</td>
										<td align='center'><input type='text' name='ListDetailAdd[<?=$no;?>][containing]' id='Addcontaining_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=$valx['containing'];?>'></td>
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
					<tbody>
						<tr>
							<td class="text-center" colspan='4'></td>
							<td class="text-left" colspan='2' style='vertical-align: middle;'><b>LINER THICKNESS</b></td>
							<td align='center'><input type='text' name='thickLin' id='thickLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['total'];?>'></td>
						</tr>
						<tr>
							<td class="text-center" colspan='4'></td>
							<td class="text-left" colspan='2' style='vertical-align: middle;'><b>MIN LINER THICKNESS</b></td>
							<td align='center'><input type='text' name='minLin' id='minLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['min'];?>'></td>
						</tr>
						<tr>
							<td class="text-center" colspan='4'></td>
							<td class="text-left" colspan='2' style='vertical-align: middle;'><b>MAX LINER THICKNESS</b></td>
							<td align='center'><input type='text' name='maxLin' id='maxLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['max'];?>'></td>
						</tr>
						<tr>
							<td class="text-center" colspan='6'></td>
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
							<th class="text-center" width='15%'>Type</th>
							<th class="text-center">Material</th>
							<th class="text-center" width='7%'>Weight</th>
							<th class="text-center" width='7%'>Layer</th>
							<th class="text-center" width='7%'>Rs.Cont</th>
							<th class="text-center" width='7%'>Thickness</th>
							<th class="text-center" width='7%'>Last Weight</th>
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
									$layY	= "<input type='text' name='ListDetail2[".$no."][layer]' id='layerStr_".$no."' data-nomor='".$no."' class='form-control input-sm numberOnly layerStr' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
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
										<select name='ListDetail2[<?=$no;?>][id_material]' id='id_materialStr_<?=$no;?>' data-nomor='<?=$no;?>' class='form-control input-sm id_materialSTr chosen-select'>
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
										<select name='ListDetailPlus2[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
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
											<select name='ListDetailAdd2[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
											<?php
												$selx2	= ($valx['id_material'] == "MTL-1903000")?'selected':'';
												foreach($ListdetStructurePlus AS $vala => $valxa){
													$selx	= ($ListdetStructureAdd[0]['id_material'] == $valxa['id_material'])?'selected':'';
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
					<tbody>
						<tr>
							<td class="text-center" colspan='4'></td>
							<td class="text-left" colspan='2' style='vertical-align: middle;'><b>STRUCTURE THICKNESS</b></td>
							<td align='center'><input type='text' name='thickStr' id='thickStr' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerStructure[0]['total'];?>'></td>
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
		<?php
			// echo "&nbsp;<a href='".site_url($this->uri->segment(1))."' style='min-width:100px;float: right;' class='btn btn-md btn-danger' title='Back To List' data-role='qtip'>Back</a>";
			echo "&nbsp;<button type='button' name='simpan-bro' style='min-width:100px; float: right; margin-right:10px;' id='simpan-bro' class='btn btn-success'>Save</button>";
			
		?>
		<br>			
	</div>	
</div>

<style type="text/css">
	
	label{
		    font-size: small !important;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
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
	#id_materialEks_8_chosen,
	#containingStr_13,
	#containingEks_9{ 
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
	#cont_topcoat_1,#perse_topcoat_1{
		display : none; 
	}
</style>
<script>	
	$(document).ready(function(){ 
		// swal({
			// title	: "Pemberitahuan!",
			// text	: 'Development Save Process',
			// type	: "warning"
		// });
		$(".chosen-select").chosen();
		
		$('.ToleranSt').hide();
		$('.HideCost').hide();
		$('.Hide').hide();
		
		$('#min_toleran').val('0.125');
		$('#max_toleran').val('0.125');
		
		var design 	= parseFloat($('#design').val());
		var ThLin 	= parseFloat($('#ThLin').val());
		$('#ThStr').val(design - ThLin);
		
		$(document).on('change', '#toleransi', function(){
			if($(this).val() != 'C100-1903000'){
				$('.ToleranSt').show();
			}
			else{
				$('.ToleranSt').hide();	
				$('#min_toleran').val('0.125');
				$('#max_toleran').val('0.125');
			}
		});
		
		$(document).on('change', '#series', function(){
			var series = $(this).val();
			var linerEx 	= series.split('-');
			var liner 		= parseFloat(linerEx[1]);
			var design 		= parseFloat($('#design').val());		
			
			var ThStr	= design - liner;
			if(isNaN(ThStr)){
				var ThStr = 0;
			}
			$('#ThStr').val(ThStr); 
			$('#ThLin').val(liner); 
			ChangeHasil();
		});
		   
		$(document).on('keyup', '#design', function(){
			var design 	= parseFloat($('#design').val()); 
			var ThLin 	= parseFloat($('#ThLin').val());
			
			var ThStr	= design - ThLin ;
			if(isNaN(ThStr)){
				var ThStr = 0;
			}
			$('#ThStr').val(ThStr); 
			ChangeHasil();
		});
		
		$(document).on('keyup', '#min_toleran', function(){
			ChangeHasil();;
		});
		
		$(document).on('keyup', '#max_toleran', function(){
			ChangeHasil();
		});
		
		$(document).on('keyup', '#length', function(){
			ChangeLuasArea();
		});
		
		$(document).on('click', '#simpan-bro', function(e){
			e.preventDefault(); 
			
			var top_tebal_design	= $('#design').val();
			var top_max_toleran		= $('#max_toleran').val();
			var top_min_toleran		= $('#min_toleran').val();
			
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
			if(top_max_toleran == '' || top_max_toleran == null || top_max_toleran == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Min Tolerance is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(top_min_toleran == '' || top_min_toleran == null || top_min_toleran == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Max Tolerance is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			
			
			var hasil_linier_thickness 	= $('#hasilLin').val();
			var hasil_linier_thickness2 = $('#hasilStr').val();
			
			if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' ){
				swal({
				  title	: "Error Message!",
				  text	: 'Thickness is To High or To Low, please check back ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			} 
			
			$('#simpan-bro').prop('disabled',false);
			
			// swal({
				  // title	: "Error Message!",
				  // text	: 'Penyimpanan belum disiapkan',
				  // type	: "warning"
				// });
			// return false;
			
			swal({
				  title	: "Peringatan !!!",
				  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
				  type	: "warning",
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
						var baseurl		= base_url + active_controller +'/pipe_slongsong_edit_bq';
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
									// window.location.href = base_url + active_controller +'/revisi_est';
									$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
									$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
									$("#ModalView").modal();
									$("#ModalView3").modal('hide');
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
	

function LuasArea(diameter, estimasi, length, waste){
	var Luas_Area_Rumus		= ((3.14/1000)*(diameter + estimasi))*(length/1000)*(1+waste);
	if(isNaN(Luas_Area_Rumus)){
		var Luas_Area_Rumus = 0;
	}
	return Luas_Area_Rumus;
}

function Estimasi(thickLin, thickStr){
	var topEST	= thickLin + thickStr;
	if(isNaN(topEST)){
		var topEST = 0;
	}
	return topEST;
}

function ChangeHasil(){
	var ThLin		= parseFloat($('#ThLin').val());
	var ThStr		= parseFloat($('#ThStr').val());
	var minToleran	= parseFloat($('#min_toleran').val());
	var maxToleran	= parseFloat($('#max_toleran').val());
	var thickLin	= parseFloat($('#thickLin').val());
	var thickStr	= parseFloat($('#thickStr').val()); 

	HasilThickness(ThLin, ThStr, minToleran, maxToleran, thickLin, thickStr);
}

function HasilThickness(ThLin, ThStr, minToleran, maxToleran, thickLin, thickStr){
	var minLinThk	= ThLin - (ThLin * minToleran);
	var maxLinThk	= ThLin + (ThLin * maxToleran);
	var minStrThk	= ThStr - (ThStr * minToleran);
	var maxStrThk	= ThStr + (ThStr * maxToleran);
	if(isNaN(minLinThk)){ var minLinThk = 0;}
	if(isNaN(maxLinThk)){ var maxLinThk = 0;}
	if(isNaN(minStrThk)){ var minStrThk = 0;}
	if(isNaN(maxStrThk)){ var maxStrThk = 0;}
	
	if(thickLin < minLinThk){var Hasil1	= "TOO LOW";}
	if(thickLin > maxLinThk){var Hasil1	= "TOO HIGH";}
	if(thickLin > minLinThk && thickLin < maxLinThk){var Hasil1	= "OK";}
	$('#minLin').val(minLinThk.toFixed(4));
	$('#maxLin').val(maxLinThk.toFixed(4));
	// alert(Hasil1);
	$('#hasilLin').val(Hasil1);
	
	if(thickStr < minStrThk){var Hasil2	= "TOO LOW";}
	if(thickStr > maxStrThk){var Hasil2	= "TOO HIGH";}
	if(thickStr > minStrThk && thickStr < maxStrThk){var Hasil2	= "OK";}
	$('#minStr').val(minStrThk.toFixed(4));
	$('#maxStr').val(maxStrThk.toFixed(4));
	$('#hasilStr').val(Hasil2);
}

function ChangeLuasArea(){
	var diameter	= parseFloat($('#diameter').val());
	var length		= parseFloat($('#length').val());
	var waste		= parseFloat($('#waste').val());
	
	var thickLin	= parseFloat($('#thickLin').val());
	var thickStr	= parseFloat($('#thickStr').val());
	
	var estimasi 	= Estimasi(thickLin, thickStr);
	
	var LuasAreaX 	= LuasArea(diameter, estimasi, length, waste);
	var LastCoat	= LuasAreaX * 0.25 * 1.2;
	
	if(isNaN(LastCoat)){ var LastCoat = 0;}
	$('#last_topcoat_1').val(LastCoat.toFixed(3));

	$('#estimasi').val(estimasi.toFixed(4));
	$('#area').val(LuasAreaX.toFixed(6));
	
	
	ChangeAreaToLiner(LuasAreaX);
	ChangeAreaToStr(LuasAreaX);
}

function LastWeight(){
	var area	= parseFloat($('#area').val());
	return area;
}

function ChangePlus(Area){
	var Con1	= parseFloat($('#Lincontaining_1').val());
	var Con2	= parseFloat($('#Lincontaining_2').val());
	var Con3	= parseFloat($('#Lincontaining_3').val());
	var Con4	= parseFloat($('#Lincontaining_4').val());
	var Con5	= parseFloat($('#Lincontaining_5').val());
	var Con6	= parseFloat($('#Lincontaining_6').val());
	
	var Per1	= parseFloat($('#Linperse_1').val()) /100;
	var Per2	= parseFloat($('#Linperse_2').val()) /100;
	var Per3	= parseFloat($('#Linperse_3').val()) /100;
	var Per4	= parseFloat($('#Linperse_4').val()) /100;
	var Per5	= parseFloat($('#Linperse_5').val()) /100;
	var Per6	= parseFloat($('#Linperse_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangePlusStr(Area){
	var Con1	= parseFloat($('#Lincontaining2_1').val());
	var Con2	= parseFloat($('#Lincontaining2_2').val());
	var Con3	= parseFloat($('#Lincontaining2_3').val());
	var Con4	= parseFloat($('#Lincontaining2_4').val());
	var Con5	= parseFloat($('#Lincontaining2_5').val());
	var Con6	= parseFloat($('#Lincontaining2_6').val());
	
	var Per1	= parseFloat($('#Linperse2_1').val()) /100;
	var Per2	= parseFloat($('#Linperse2_2').val()) /100;
	var Per3	= parseFloat($('#Linperse2_3').val()) /100;
	var Per4	= parseFloat($('#Linperse2_4').val()) /100;
	var Per5	= parseFloat($('#Linperse2_5').val()) /100;
	var Per6	= parseFloat($('#Linperse2_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost2_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost2_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost2_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost2_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost2_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost2_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangeAreaToLiner(Area){
	var value_1 		= parseFloat($('#value_1').val());
	var value_2 		= parseFloat($('#value_2').val());
	var value_4 		= parseFloat($('#value_4').val());
	var value_6 		= parseFloat($('#value_6').val());
	var value_8 		= parseFloat($('#value_8').val());
	var layer_2 		= parseFloat($('#layer_2').val());
	var layer_4 		= parseFloat($('#layer_4').val());
	var layer_6 		= parseFloat($('#layer_6').val());
	var layer_8 		= parseFloat($('#layer_8').val());
	var containing_3	= parseFloat($('#containing_3').val());
	var containing_5	= parseFloat($('#containing_5').val());
	var containing_7	= parseFloat($('#containing_7').val());
	var containing_9	= parseFloat($('#containing_9').val());
	
	var id_material_1 	= $('#id_material_1').val();
	var diameter		= $('#diameter').val();
	if(diameter < 25){var Hit = 800;}else{var Hit = 1350;}
	
	var last_cost_1 	= Area * value_1 * 1.5 * Hit ;
	var last_cost_2 	= (Area * value_2 * layer_2)/1000 ;
	var last_cost_4 	= (Area * value_4 * layer_4)/1000 ;
	var last_cost_6 	= (Area * value_6 * layer_6)/1000 ;
	var last_cost_8 	= (Area * value_8 * layer_8)/1000 ; 
	var resin3			= last_cost_2 * containing_3;
	var resin5			= last_cost_4 * containing_5;
	var resin7			= last_cost_6 * containing_7;
	var resin9			= last_cost_8 * containing_9;
	
	var resiTot		= (Area * 1.2 * 0.5) + resin3 + resin5 + resin7 + resin9;
	ChangePlus(resiTot);
	
	$('#last_cost_10').val(resiTot.toFixed(3));
	$('#last_full_10').val(resiTot);
	
	$("#last_cost_1").val(last_cost_1.toFixed(3));
	$("#last_cost_2").val(last_cost_2.toFixed(3));
	$("#last_cost_4").val(last_cost_4.toFixed(3));
	$("#last_cost_6").val(last_cost_6.toFixed(3));
	$("#last_cost_8").val(last_cost_8.toFixed(3));
	$("#last_cost_3").val(resin3.toFixed(3));
	$("#last_cost_5").val(resin5.toFixed(3));
	$("#last_cost_7").val(resin7.toFixed(3));
	$("#last_cost_9").val(resin9.toFixed(3));
}

function ChangeAreaToStr(Area){
	var valueStr_1 		= parseFloat($('#valueStr_1').val());
	var valueStr_3 		= parseFloat($('#valueStr_3').val());
	var valueStr_5 		= parseFloat($('#valueStr_5').val());
	var valueStr_7 		= parseFloat($('#valueStr_7').val());
	var valueStr_9 		= parseFloat($('#valueStr_9').val());
	var valueStr_11 	= parseFloat($('#valueStr_11').val());
	
	var layerStr_1 		= parseFloat($('#layerStr_1').val());
	var layerStr_3 		= parseFloat($('#layerStr_3').val());
	var layerStr_5 		= parseFloat($('#layerStr_5').val());
	var layerStr_7 		= parseFloat($('#layerStr_7').val());
	var layerStr_9 		= parseFloat($('#layerStr_9').val());
	var layerStr_11 	= parseFloat($('#layerStr_11').val());
	
	var containingStr_2		= parseFloat($('#containingStr_2').val());
	var containingStr_4		= parseFloat($('#containingStr_4').val());
	var containingStr_6		= parseFloat($('#containingStr_6').val());
	var containingStr_8		= parseFloat($('#containingStr_8').val());
	var containingStr_10	= parseFloat($('#containingStr_10').val());
	var containingStr_12	= parseFloat($('#containingStr_12').val());
	
	var bwStr_9 		= parseFloat($('#bwStr_9').val());
	var jumlahStr_9 	= parseFloat($('#jumlahStr_9').val());
	var bwStr_11 		= parseFloat($('#bwStr_11').val());
	var jumlahStr_11 	= parseFloat($('#jumlahStr_11').val());
	
	var last_costStr_1 		= (Area * valueStr_1 * layerStr_1)/1000 ;
	var last_costStr_3 		= (Area * valueStr_3 * layerStr_3)/1000 ;
	var last_costStr_5 		= (Area * valueStr_5 * layerStr_5)/1000 ;
	var last_costStr_7 		= (Area * valueStr_7 * layerStr_7)/1000 ; 
	var last_costStr_9 		= ((valueStr_9 * 0.001 * jumlahStr_9 * 100)/(bwStr_9/10)) * (2/1000) * layerStr_9 * Area;
	var last_costStr_11 	= ((valueStr_11 * 0.001 * jumlahStr_11 * 100)/(bwStr_11/10)) * (2/1000) * layerStr_11 * Area;
	
	if(isNaN(last_costStr_9)){var last_costStr_9 = 0;}
	if(isNaN(last_costStr_11)){var last_costStr_11 = 0;}
	
	var resin2			= last_costStr_1 * containingStr_2;
	var resin4			= last_costStr_3 * containingStr_4;
	var resin6			= last_costStr_5 * containingStr_6;
	var resin8			= last_costStr_7 * containingStr_8;
	var resin10			= last_costStr_9 * containingStr_10;
	var resin12			= last_costStr_11 * containingStr_12;
	
	var resiTot			= resin2 + resin4 + resin6 + resin8 + resin10 + resin12;
	ChangePlusStr(resiTot);
	
	$('#last_costStr_13').val(resiTot.toFixed(3));
	$('#last_fullStr_13').val(resiTot);

	$("#last_costStr_1").val(last_costStr_1.toFixed(3));
	$("#last_costStr_3").val(last_costStr_3.toFixed(3));
	$("#last_costStr_5").val(last_costStr_5.toFixed(3));
	$("#last_costStr_7").val(last_costStr_7.toFixed(3));
	$("#last_costStr_9").val(last_costStr_9.toFixed(3));
	$("#last_costStr_11").val(last_costStr_11.toFixed(3));
	
	$("#last_costStr_2").val(resin2.toFixed(3));
	$("#last_costStr_4").val(resin4.toFixed(3));
	$("#last_costStr_6").val(resin6.toFixed(3));
	$("#last_costStr_8").val(resin8.toFixed(3));
	$("#last_costStr_10").val(resin10.toFixed(3));
	$("#last_costStr_12").val(resin12.toFixed(3));
}

function ChangePlusAdd(Area){
	var AddLinNum	= parseFloat($('#AddLinNum').val());
	var a;
	for(a=1; a <= AddLinNum; a++){
		var Con		= parseFloat($('#Addcontaining_'+a).val());
		var Per		= parseFloat($('#Addperse_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusStrAdd(Area){
	var AddStrNum	= parseFloat($('#AddStrNum').val());
	var a;
	for(a=1; a <= AddStrNum; a++){
		var Con		= parseFloat($('#Addcontaining2_'+a).val());
		var Per		= parseFloat($('#Addperse2_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost2_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

//LINER
$(document).on('change', '.id_material', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '10'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_material2_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_ori_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containing_'+NoResin).val();
		var materialRs	= $('#id_material2_'+NoResin);
		
		var lastRes	= $('#last_cost_'+NoResin);
		
		var resinOri	= $('#id_material2_10').val();
		
		var resinX1	= $('#id_material2_3').val();
		var resinX2	= $('#id_material2_5').val();
		var resinX3	= $('#id_material2_7').val();
		var resinX4	= $('#id_material2_9').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri,
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_material2_3').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_material2_5').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_material2_7').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_material2_9').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
			}
		});
	}
});

$(document).on('keyup', '.layer', function(){
	
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	var layer			= parseFloat($(this).val());
	var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor).val());
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;
	
	var containing		= parseFloat($('#containing_'+nomorPlus).val());
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	var totthicknessLin1	= parseFloat($('#total_thickness_2').val());
	var totthicknessLin2	= parseFloat($('#total_thickness_4').val());
	var totthicknessLin3	= parseFloat($('#total_thickness_6').val());
	var totthicknessLin4	= parseFloat($('#total_thickness_8').val());
	var AllThick			= totthicknessLin1 + totthicknessLin2 + totthicknessLin3 + totthicknessLin4;
	$('#thickLin').val(AllThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
	
	var weight		= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var area		= LastWeight();
	var HlastWeight	= (area * weight * layer)/1000;
	if(isNaN(HlastWeight)){ var HlastWeight = 0;}
	lastWeight.val(HlastWeight.toFixed(3));

	var LastResin	= HlastWeight * containing; 
	$('#last_cost_'+nomorPlus).val(LastResin.toFixed(3));
	
	//resin Group
	var resin1 	= parseFloat($('#last_cost_3').val());
	var resin2 	= parseFloat($('#last_cost_5').val());
	var resin3 	= parseFloat($('#last_cost_7').val());
	var resin4	= parseFloat($('#last_cost_9').val());
	var containning	= parseFloat($('#containing_10').val());
	var resiTot	= (area * 1.2 * containning) + resin1 + resin2 + resin3 + resin4; 
	
	$('#last_cost_10').val(resiTot.toFixed(3));
	$('#last_full_10').val(resiTot);
	
	ChangePlus(resiTot);
	ChangePlusAdd(resiTot);
});

$(document).on('keyup', '.perse', function(){
	var TotResin	= parseFloat($('#last_cost_10').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(Hasil.toFixed(3));
});

$(document).on('keyup', '.perseLinAdd', function(){
	var TotResin	= parseFloat($('#last_cost_10').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(Hasil.toFixed(3));
});



//STRUCTURE
$(document).on('change', '.id_materialSTr', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '13'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_materialStr2_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_oriStr_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor);
		var BW			= $(this).parent().parent().find("td:nth-child(1) #bwStr_"+nomor);
		var Jumlah		= $(this).parent().parent().find("td:nth-child(1) #jumlahStr_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingStr_'+NoResin).val();
		var materialRs	= $('#id_materialStr2_'+NoResin);
		
		var lastRes	= $('#last_costStr_'+NoResin);
		
		var resinOri	= $('#id_materialStr2_13').val();
		
		var resinX1	= $('#id_materialStr2_2').val();
		var resinX2	= $('#id_materialStr2_4').val();
		var resinX3	= $('#id_materialStr2_6').val();
		var resinX4	= $('#id_materialStr2_8').val();
		var resinX5	= $('#id_materialStr2_10').val();
		var resinX6	= $('#id_materialStr2_12').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/'+active_controller+'/getMaterialx',
			cache: false,
			type: "POST",
			data: "id_material="+id_material+"&diameter="+diameter+"&resin="+resin+"&id_ori="+id_ori+"&resinOri="+resinOri,
			dataType: "json",
			success: function(data){
				helpX.val(data.weight);
				layer.val(data.layer);
				thick_hide.val(data.thickness);
				material2.val(data.resinUt);
				materialRs.val(data.resin);
				BW.val(data.bw);
				Jumlah.val(data.jumRoov);
				if(data.resinAk == 'Y'){
					if(resinX1 != 'MTL-1903000'){$('#id_materialStr2_2').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_materialStr2_4').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_materialStr2_6').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_materialStr2_8').val(data.resinUt);}
					if(resinX5 != 'MTL-1903000'){$('#id_materialStr2_10').val(data.resinUt);}
					if(resinX6 != 'MTL-1903000'){$('#id_materialStr2_12').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
			}
		});
	}
});

$(document).on('keyup', '.layerStr', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	var layer			= parseFloat($(this).val());
	var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val());
	var BW				= parseFloat($(this).parent().parent().find("td:nth-child(1) #bwStr_"+nomor).val());
	var Jumlah			= parseFloat($(this).parent().parent().find("td:nth-child(1) #jumlahStr_"+nomor).val());
	var oriMat			= $(this).parent().parent().find("td:nth-child(1) #id_oriStr_"+nomor).val();
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;

	// alert(thick_hide);
	var containing		= parseFloat($('#containingStr_'+nomorPlus).val());
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	var totthicknessLin1	= parseFloat($('#total_thickness_1').val());
	var totthicknessLin2	= parseFloat($('#total_thickness_3').val());
	var totthicknessLin3	= parseFloat($('#total_thickness_5').val());
	var totthicknessLin4	= parseFloat($('#total_thickness_7').val());
	var totthicknessLin5	= parseFloat($('#total_thickness_9').val());
	var totthicknessLin6	= parseFloat($('#total_thickness_11').val());
	
	var AllThick			= totthicknessLin1 + totthicknessLin2 + totthicknessLin3 + totthicknessLin4 + totthicknessLin5 + totthicknessLin6;
	$('#thickStr').val(AllThick.toFixed(4));
	ChangeLuasArea();
	ChangeHasil();
	var weight		= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var area		= LastWeight();
	
	if(oriMat == 'TYP-0005'){
		var HlastWeight	= ((weight * 0.001 * Jumlah * 100)/(BW/10)) * (2/1000) * layer * area;
		//100 = pengali rooving
	}
	else{
		var HlastWeight	= (area * weight * layer)/1000;	
	}
	
	
	if(isNaN(HlastWeight)){ var HlastWeight = 0;}
	lastWeight.val(HlastWeight.toFixed(3));
	
	var LastResin	= HlastWeight * containing;
	$('#last_costStr_'+nomorPlus).val(LastResin.toFixed(3));
	
	//resin Group
	var resin1 	= parseFloat($('#last_costStr_2').val());
	var resin2 	= parseFloat($('#last_costStr_4').val());
	var resin3 	= parseFloat($('#last_costStr_6').val());
	var resin4	= parseFloat($('#last_costStr_8').val());
	var resin5 	= parseFloat($('#last_costStr_10').val());
	var resin6	= parseFloat($('#last_costStr_12').val());
	
	var resiTot	= resin1 + resin2 + resin3 + resin4 + resin5 + resin6; 
	
	$('#last_costStr_13').val(resiTot.toFixed(3));
	$('#last_fullStr_13').val(resiTot);
	
	ChangePlusStr(resiTot);
	ChangePlusStrAdd(resiTot);
});

$(document).on('keyup', '.perseStr', function(){
	var TotResin	= parseFloat($('#last_costStr_13').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(Hasil.toFixed(3));
});

$(document).on('keyup', '.perseStrAdd', function(){
	var TotResin	= parseFloat($('#last_costStr_13').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(Hasil.toFixed(3));
});	

</script>