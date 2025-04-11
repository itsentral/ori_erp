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
                echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm Hide','readonly'=>'readonly'),$header[0]->nm_product);
                echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'Hide'),$header[0]->id_product);
                echo form_input(array('id'=>'parent_product','name'=>'parent_product','class'=>'Hide'),$header[0]->parent_product);
            ?>						
			<div class='headerTitleGroup'>GROUP COMPONENT</div>
			<div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Customer</b></label>
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
				<label class='label-control col-sm-2'><b>Series <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='series' id='series' class='form-control input-sm'>
					<?php
						foreach($series AS $val => $valx){
							$selx	= ($header[0]->series == $valx['series'])?'selected':'';
							echo "<option value='".$valx['series']."' ".$selx.">".strtoupper($valx['series'])."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
                <label class='label-control col-sm-2'><b>Component Name <span class='text-red'>*</span></b></label>
                <div class='col-sm-4'>
                    <select name='component_list' id='component_list' class='form-control input-sm'>
                    <?php
                        foreach($product AS $val => $valx){
                            $selxv	= ($header[0]->parent_product == $valx['product_parent'])?'selected':'';
                            echo "<option value='".$valx['product_parent']."' ".$selxv.">".strtoupper(strtolower($valx['product_parent']))."</option>";
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
			<!-- //// -->
			<div class='headerTitleGroup'>DETAILED ESTIMATION</div>
			<div class='form-group row'>
                <label class='label-control col-sm-2'><b><span id='id_1'>Inner Dim  </span><span class='text-red'>*</span> <span id='id_2'>| Outter Dim </span><span class='text-red'>*</span></b></label>
                <div class='col-sm-2'>
                    <?php
                        echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Inner Diameter'), $header[0]->diameter);
                    ?>
                </div>
                <div class='col-sm-2'>
                    <?php
                        echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Outer Diameter'), $header[0]->diameter2);
                    ?>
                </div>

                <label class='label-control col-sm-2'><b><span id='id_3'>Thickness  </span><span class='text-red'>*</span></b></label>
                <div class='col-sm-2'>
                    <?php
                        echo form_input(array('type'=>'text','id'=>'design','name'=>'design','class'=>'form-control input-sm numberOnly','placeholder'=>'Thickness'), floatval($header[0]->design));
                    ?>
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
								<th class="text-center" colspan='3'>Material</th>
								<th class="text-center" width='8%'>Layer</th>
								<th class="text-center" width='8%'></th>
								<th class="text-center" width='8%'>Last Weight</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detLiner AS $val => $valx){
									$no++;
									$ListdetLin	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									$layY	= "<input type='text' name='ListDetail[".$no."][layer]' id='layer_".$no."' data-nomor='".$no."' data-type='".$valx['id_ori']."' class='form-control input-sm numberOnly layer' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
									?>
									<tr>
										<td>
											<span id='hideCty_<?=$no;?>'><?= $ListdetLin[0]['category'];?></span>
											<input type='text' name='ListDetail[<?=$no;?>][id_detail]' class='HideCost' id='id_detail_<?=$no;?>' value='<?=$valx['id_detail'];?>'>
											<input type='text' name='ListDetail[<?=$no;?>][id_ori]' class='HideCost' id='id_ori_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
										</td>
										<td colspan='3'>
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
										<td align='center'><?= $layY;?></td>
										<td align='center'></td>
										<td align='center'><input type='text' name='ListDetail[<?=$no;?>][last_cost]' id='last_cost_<?=$no;?>' class='form-control input-sm numberOnly' style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
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
				</div>
			</div>
			
			<div class='headerTitle'>STRUCTURE THIKNESS</div>
			<input type='text' name='detail_name2' id='detail_name2' class='HideCost' value='STRUKTUR THICKNESS'>
			<div class="box box-primary">
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"> 
						<head>
							<tr class='bg-blue'>
								<th class="text-center"  width='15%'>Type</th>
								<th class="text-center" colspan='3'>Material</th>
								<th class="text-center">Layer</th>
								<th class="text-center"></th>
								<th class="text-center">Last Weight</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detStructure AS $val => $valx){
									$no++;
									$ListdetStructure	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									$layY	= "<input type='text' name='ListDetail2[".$no."][layer]' id='layerStr_".$no."' data-type='".$valx['id_ori']."' data-nomor='".$no."' class='form-control input-sm numberOnly layerStr' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
									?>
									<tr>
										<td>
											<span id='hideCty2_<?=$no;?>'><?= $ListdetStructure[0]['category'];?></span>
											<input type='text' name='ListDetail2[<?=$no;?>][id_detail]' class='HideCost' id='id_detailStr_<?=$no;?>' value='<?=$valx['id_detail'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][id_ori]' class='HideCost' id='id_oriStr_<?=$no;?>' value='<?=$valx['id_ori'];?>'>	
										</td>
										<td colspan='3'>
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
										<td align='center'><?= $layY;?></td>
										<td align='center'></td>
										<td align='center'><input type='text' name='ListDetail2[<?=$no;?>][last_cost]' id='last_costStr_<?=$no;?>' class='form-control input-sm numberOnly' style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
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
							if(!empty($detStructureAdd)){
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
											<td align='center'><input type='text' name='ListDetailAdd2[<?=$no;?>][containing]' id='Addcontaining2_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
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
				</div>
			</div>
			
			<div class='headerTitle'>EXTERNAL THIKNESS</div>
			<input type='text' name='detail_name3' id='detail_name3' class='HideCost' value='EXTERNAL LAYER THICKNESS'>
			<div class="box box-primary">
				<div class="box-body" style="">
					<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
						<head>
							<tr class='bg-blue'>
								<th class="text-center"  width='15%'>Type</th>
								<th class="text-center" colspan='3'>Material</th>
								<th class="text-center">Layer</th>
								<th class="text-center"></th>
								<th class="text-center">Last Weight</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detEksternal AS $val => $valx){
									$no++;
									$ListdetEksternal	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' AND a.id_category='".$valx['id_ori2']."' ORDER BY a.nm_material ASC")->result_array();
									$layY	= "<input type='text' name='ListDetail3[".$no."][layer]' id='layerEks_".$no."' data-nomor='".$no."' data-type='".$valx['id_ori']."' class='form-control input-sm numberOnly layerEks' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
										
									?>
									<tr>
										<td>
											<span id='hideCty3_<?=$no;?>'><?= $ListdetEksternal[0]['category'];?></span>
											<input type='text' name='ListDetail3[<?=$no;?>][id_detail]' class='HideCost' value='<?=$valx['id_detail'];?>'>
											<input type='text' name='ListDetail3[<?=$no;?>][id_ori]' class='HideCost' id='id_oriEks_<?=$no;?>' value='<?=$valx['id_ori'];?>'>
										</td>
										<td colspan='3'>
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
										<td align='center'><?= $layY;?></td>
										<td align='center'></td>
										<td align='center'><input type='text' name='ListDetail3[<?=$no;?>][last_cost]' id='last_costEks_<?=$no;?>' class='form-control input-sm numberOnly' style='text-align:right; width:80px;' value='<?=$valx['last_cost'];?>'></td>
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
										<td align='center'><input type='text' name='ListDetailPlus3[<?=$no;?>][containing]' id='Lincontaining3_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
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
											<td align='center'><input type='text' name='ListDetailAdd4[<?=$no;?>][containing]' id='Addcontaining4_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?= floatval($valx['containing']);?>'></td>
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
				echo "&nbsp;<a href='".site_url($this->uri->segment(1))."' style='min-width:100px;float: right;' class='btn btn-md btn-danger' title='Back To List' data-role='qtip'>Back</a>";
				echo "&nbsp;<button type='button' name='simpan-bro' style='min-width:100px; margin-bottom:-35px; float: right; margin-right:10px;' id='simpan-bro' class='btn btn-success'>Save</button>";
				
			?>	
		</div>	
	</div>		
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Component/edit/javascript/custom_est_manual.js'); ?>"></script>
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

	#cont_topcoat_1,
	#perse_topcoat_1{
		display:none;
	}
</style>
<script>	

	$(document).ready(function(){
		$('#last_topcoat_1').removeAttr('readonly');

        var product = $('#component_list').val();
		var product_parent = product.replaceAll(' ', '0_0');
		$.ajax({
			url: base_url +'product/get_custom_product/'+product_parent,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.nomor == 1){
					$('#id_1').html(data.spec1);
					$('#id_2').html(' | '+data.spec2);
					$('#diameter').attr('placeholder',data.spec1);
					$('#diameter2').attr('placeholder',data.spec2);
					$('#diameter2').show();
					// $('#diameter2').val('');
					swal.close();
				}
				
				if(data.nomor == 0){
					$('#id_1').html(data.spec1);
					$('#id_2').html('');
					$('#diameter').attr('placeholder',data.spec1);
					$('#diameter2').hide();
					$('#diameter2').val(0);
					swal.close();
				}
			},
			error: function() {
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
		
		$(document).on('change', '#component_list', function(e){
			var product = $(this).val();
			var product_parent = product.replaceAll(' ', '0_0');
			console.log(product_parent);
			if(product_parent == 0){
				return false;
			}
			loading_spinner();
			
			$.ajax({
				url: base_url +'product/get_custom_product/'+product_parent,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					if(data.nomor == 1){
						$('#id_1').html(data.spec1);
						$('#id_2').html(' | '+data.spec2);
						$('#diameter').attr('placeholder',data.spec1);
						$('#diameter2').attr('placeholder',data.spec2);
						$('#diameter2').show();
						$('#diameter2').val('');
						swal.close();
					}
					
					if(data.nomor == 0){
						$('#id_1').html(data.spec1);
						$('#id_2').html('');
						$('#diameter').attr('placeholder',data.spec1);
						$('#diameter2').hide();
						$('#diameter2').val(0);
						swal.close();
					}
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
			
		});
		
		// if(component_list == 'plate' || component_list == 'joint plate' || component_list == 'taper plate' || component_list == 'square flange' || component_list == 'shim plate' || component_list == 'joint saddle'){
			// $('#id_1').html('Panjang');
			// $('#id_2').html(' | Lebar');
			// $('#diameter').attr('placeholder','Panjang');
			// $('#diameter2').attr('placeholder','Lebar');
			// var dimx1 = 'Panjang';
			// var dimx2 = 'Lebar';
		// }
		// if(component_list == 'rib taper plate' || component_list == 'rib end plate'){
			// $('#id_1').html('Panjang');
			// $('#id_2').html(' | Tinggi');
			// $('#diameter').attr('placeholder','Panjang');
			// $('#diameter2').attr('placeholder','Tinggi');
			// var dimx1 = 'Panjang';
			// var dimx2 = 'Tinggi';
		// }
		// if(component_list == 'rib' || component_list == 'joint rib'){
			// $('#id_1').html('Alas');
			// $('#id_2').html(' | Tinggi');
			// $('#diameter').attr('placeholder','Alas');
			// $('#diameter2').attr('placeholder','Tinggi');
			// var dimx1 = 'Alas';
			// var dimx2 = 'Tinggi';
		// }
		// if(component_list == 'figure 8'){
			// $('#id_1').html('Alas');
			// $('#id_2').html(' | Outer Diameter');
			// $('#diameter').attr('placeholder','Alas');
			// $('#diameter2').attr('placeholder','Outer Diameter');
			// var dimx1 = 'Alas';
			// var dimx2 = 'Outer Diameter';
		// }
		// if(component_list == 'loose flange' || component_list == 'spacer' || component_list == 'puddle flange' || component_list == 'joint puddle flange' || component_list == 'spacer ring' || component_list == 'blind spacer' || component_list == 'blind flange with hole'){ //puddle flange
			// $('#id_1').html('Inner Diameter');
			// $('#id_2').html(' | Outer Diameter');
			// $('#diameter').attr('placeholder','Inner Diameter');
			// $('#diameter2').attr('placeholder','Outer Diameter');
			// var dimx1 = 'Inner Diameter';
			// var dimx2 = 'Outer Diameter';
		// }
		// if(component_list == 'saddle'){
			// $('#id_1').html('Diameter');
			// $('#id_2').html(' | Panjang');
			// $('#diameter').attr('placeholder','Diameter');
			// $('#diameter2').attr('placeholder','Panjang');
			// var dimx1 = 'Diameter';
			// var dimx2 = 'Panjang';
		// }
		// if(component_list == 'inlet cone'){
			// $('#id_1').html('Inner Diameter');
			// $('#id_2').html(' | Tinggi');
			// $('#diameter').attr('placeholder','Inner Diameter');
			// $('#diameter2').attr('placeholder','Tinggi');
			// var dimx1 = 'Inner Diameter';
			// var dimx2 = 'Tinggi';
		// }
		// if(component_list == 'bellmouth'){
			// $('#id_1').html('Diameter 1');
			// $('#id_2').html(' | Diameter 2');
			// $('#diameter').attr('placeholder','Diameter 1');
			// $('#diameter2').attr('placeholder','Diameter 2');
			// var dimx1 = 'Diameter 1';
			// var dimx2 = 'Diameter 2';
		// }
		// if(component_list == 'spectacle blind' || component_list == 'blank and spacer' || component_list == 'end plate'){
			// $('#id_1').html('Diameter');
			// $('#id_2').html('');
			// $('#diameter').attr('placeholder','Diameter');
			// $('#diameter2').hide();
			// $('#diameter2').val(0);
			// var dimx1 = 'Diameter';
			// var dimx2 = '';
		// }

        // $(document).on('change', '#component_list', function(e){
			// var component_list = $(this).val();
            // $('#diameter').val('');
            // $('#diameter2').val('');
            // $('#design').val('');
			// if(component_list == 'plate' || component_list == 'joint plate' || component_list == 'taper plate' || component_list == 'square flange' || component_list == 'shim plate' || component_list == 'joint saddle'){
				// $('#id_1').html('Panjang');
				// $('#id_2').html(' | Lebar');
				// $('#diameter').attr('placeholder','Panjang');
				// $('#diameter2').attr('placeholder','Lebar');
				// var dimx1 = 'Panjang';
				// var dimx2 = 'Lebar';
			// }
			// if(component_list == 'rib taper plate' || component_list == 'rib end plate'){
				// $('#id_1').html('Panjang');
				// $('#id_2').html(' | Tinggi');
				// $('#diameter').attr('placeholder','Panjang');
				// $('#diameter2').attr('placeholder','Tinggi');
				// var dimx1 = 'Panjang';
				// var dimx2 = 'Tinggi';
			// }
			// if(component_list == 'rib' || component_list == 'joint rib'){
				// $('#id_1').html('Alas');
				// $('#id_2').html(' | Tinggi');
				// $('#diameter').attr('placeholder','Alas');
				// $('#diameter2').attr('placeholder','Tinggi');
				// var dimx1 = 'Alas';
				// var dimx2 = 'Tinggi';
			// }
			// if(component_list == 'figure 8'){
				// $('#id_1').html('Alas');
				// $('#id_2').html(' | Outer Diameter');
				// $('#diameter').attr('placeholder','Alas');
				// $('#diameter2').attr('placeholder','Outer Diameter');
				// var dimx1 = 'Alas';
				// var dimx2 = 'Outer Diameter';
			// }
			// if(component_list == 'loose flange' || component_list == 'spacer' || component_list == 'puddle flange' || component_list == 'joint puddle flange' || component_list == 'spacer ring' || component_list == 'blind spacer' || component_list == 'blind flange with hole'){ //puddle flange
				// $('#id_1').html('Inner Diameter');
				// $('#id_2').html(' | Outer Diameter');
				// $('#diameter').attr('placeholder','Inner Diameter');
				// $('#diameter2').attr('placeholder','Outer Diameter');
				// var dimx1 = 'Inner Diameter';
				// var dimx2 = 'Outer Diameter';
			// }
			// if(component_list == 'saddle'){
				// $('#id_1').html('Diameter');
				// $('#id_2').html(' | Panjang');
				// $('#diameter').attr('placeholder','Diameter');
				// $('#diameter2').attr('placeholder','Panjang');
				// var dimx1 = 'Diameter';
				// var dimx2 = 'Panjang';
			// }
			// if(component_list == 'inlet cone'){
				// $('#id_1').html('Inner Diameter');
				// $('#id_2').html(' | Tinggi');
				// $('#diameter').attr('placeholder','Inner Diameter');
				// $('#diameter2').attr('placeholder','Tinggi');
				// var dimx1 = 'Inner Diameter';
				// var dimx2 = 'Tinggi';
			// }
			// if(component_list == 'bellmouth'){
				// $('#id_1').html('Diameter 1');
				// $('#id_2').html(' | Diameter 2');
				// $('#diameter').attr('placeholder','Diameter 1');
				// $('#diameter2').attr('placeholder','Diameter 2');
				// var dimx1 = 'Diameter 1';
				// var dimx2 = 'Diameter 2';
			// }
			// if(component_list == 'spectacle blind' || component_list == 'blank and spacer' || component_list == 'end plate'){
				// $('#id_1').html('Diameter');
				// $('#id_2').html('');
				// $('#diameter').attr('placeholder','Diameter');
				// $('#diameter2').hide();
				// $('#diameter2').val(0);
				// var dimx1 = 'Diameter';
				// var dimx2 = '';
			// }
		// });
				
		$('.HideCost').hide();
		$('.Hide').hide();
		
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
						var baseurl		= base_url + active_controller +'/custom_edit';
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

	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}
</script>
