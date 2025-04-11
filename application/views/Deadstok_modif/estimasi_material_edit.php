<?php
$this->load->view('include/side_menu'); 
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<div class="box-body">
            <div class='headerTitle'>DETAIL PRODUCT</div>
            <input type="hidden" name='id_deadstok' id='id_deadstok' value='<?=$kode;?>'>
            <div class='form-group row'>
                <div class="col-md-12">
                    <table width='80%'>
                        <tr>
                            <td width='20%'>Sales Order</td>
                            <td width='1%'>:</td>
                            <td><?=$HeaderDeadstok[0]['no_so'];?></td>
                        </tr>
                        <tr>
                            <td>IPP</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['no_ipp'];?></td>
                        </tr>
                        <tr>
                            <td>No SPK</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['no_spk'];?></td>
                        </tr>
                        <tr>
                            <td>Product Deadstok</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['product_name'];?></td>
                        </tr>
                        <tr>
                            <td>Spec Deadstok</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['product_spec'];?></td>
                        </tr>
                        <tr>
                            <td>Qty</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['qty'];?></td>
                        </tr>
                        <tr>
                            <td>Proses</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['proses'];?></td>
                        </tr>
                    </table>
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
								<th class="text-center" width='8%'>Berat</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detLiner AS $val => $valx){
									$no++;
									$ListdetLin	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td>
											<span id='hideCty_<?=$no;?>'><?= $ListdetLin[0]['category'];?></span>
											<input type='text' name='ListDetail[<?=$no;?>][id]' class='HideCost' id='id_detail_<?=$no;?>' value='<?=$valx['id'];?>'>
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
										</td>
										<td align='center'><input type='text' name='ListDetail[<?=$no;?>][last_cost]' id='last_cost_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						</tbody>
						<head>
							<tr class='bg-blue'>
								<td class="text-center">Type</td>
								<td class="text-center">Material</td>
								<td class="text-center">Berat</td>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detLinerPlus AS $val => $valx){
									$no++;
									$ListdetLinPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' ORDER BY a.nm_material ASC")->result_array();
									
									?>
									<tr>
										<td><?= $ListdetLinPlus[0]['category'];?><input type='hidden' name='ListDetailPlus[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
										<td>
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
										</td>
										<td align='center'><input type='text' name='ListDetailPlus[<?=$no;?>][last_cost]' id='Linlast_cost_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						<!-- LINER MATERIAL ADD-->
							<?php
							if(!empty($detLinerAdd)){
									$no=0;
									foreach($detLinerAdd AS $val => $valx){
										$no++;
										$ListdetLinAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetLinAdd[0]['category'];?><input type='hidden' name='ListDetailAdd[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
											<td >
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
											<td align='center'><input type='text' name='ListDetailAdd[<?=$no;?>][last_cost]' id='Addlast_cost_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
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
								<th class="text-center" width='15%'>Type</th>
								<th class="text-center">Material</th>
								<th class="text-center" width='8%'>Berat</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detStructure AS $val => $valx){
									$no++;
									$ListdetStructure	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td>
											<span id='hideCty2_<?=$no;?>'><?= $ListdetStructure[0]['category'];?></span>
											<input type='text' name='ListDetail2[<?=$no;?>][id]' class='HideCost' id='id_detailStr_<?=$no;?>' value='<?=$valx['id'];?>'>
											<input type='text' name='ListDetail2[<?=$no;?>][id_ori]' class='HideCost' id='id_oriStr_<?=$no;?>' value='<?=$valx['id_ori'];?>'>	
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
										</td>
										<td align='center'><input type='text' name='ListDetail2[<?=$no;?>][last_cost]' id='last_costStr_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
							</tbody>
							<head>
								<tr class='bg-blue'>
									<td class="text-center">Type</td>
									<td class="text-center">Material</td>
									<td class="text-center">Berat</td>
								</tr>
							</head>
							<tbody>
							<?php
								$no=0;
								foreach($detStructurePlus AS $val => $valx){
									$no++;
									$ListdetStructurePlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td><?= $ListdetStructurePlus[0]['category'];?><input type='hidden' name='ListDetailPlus2[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
										<td>
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
										</td>
										<td align='center'><input type='text' name='ListDetailPlus2[<?=$no;?>][last_cost]' id='Linlast_cost2_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						<!-- LINER MATERIAL ADD-->
							<?php
							if(!empty($detStructureAdd)){
								$no=0;
									foreach($detStructureAdd AS $val => $valx){
										$no++;
										$ListdetStructureAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetStructureAdd[0]['category'];?><input type='hidden' name='ListDetailAdd2[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
											<td>
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
											<td align='center'><input type='text' name='ListDetailAdd2[<?=$no;?>][last_cost]' id='Addlast_cost2_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
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
								<th class="text-center" width='15%'>Type</th>
								<th class="text-center">Material</th>
								<th class="text-center" width='8%'>Berat</th>
							</tr>
						</head>
						<tbody>
							<?php
								$no=0;
								foreach($detEksternal AS $val => $valx){
									$no++;
									$ListdetEksternal	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."'ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td>
											<span id='hideCty3_<?=$no;?>'><?= $ListdetEksternal[0]['category'];?></span>
											<input type='text' name='ListDetail3[<?=$no;?>][id]' class='HideCost' value='<?=$valx['id'];?>'>
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
										</td>
										<td align='center'><input type='text' name='ListDetail3[<?=$no;?>][last_cost]' id='last_costEks_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
							</tbody>
							<head>
								<tr class='bg-blue'>
									<td class="text-center">Type</td>
									<td class="text-center">Material</td>
									<td class="text-center">Berat</td>
								</tr>
							</head>
							<tbody>
							<?php
								$no=0;
								foreach($detEksternalPlus AS $val => $valx){
									$no++;
									$ListdetEksternalPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td><?= $ListdetEksternalPlus[0]['category'];?><input type='hidden' name='ListDetailPlus3[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
										<td>
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
										</td>
										<td align='center'><input type='text' name='ListDetailPlus3[<?=$no;?>][last_cost]' id='Linlast_cost3_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						<!-- LINER MATERIAL ADD-->
							<?php
							if(!empty($detEksternalAdd)){
									$no=0;
									foreach($detEksternalAdd AS $val => $valx){
										$no++;
										$ListdetStructureAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetStructureAdd[0]['category'];?><input type='hidden' name='ListDetailAdd3[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
											<td>
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
											<td align='center'><input type='text' name='ListDetailAdd3[<?=$no;?>][last_cost]' id='Addlast_cost3_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
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
									<td class="text-center">Material</td>
									<td class="text-center" width='8%'>Berat</td>
								</tr>
							</head>
							<tbody>
							<?php
								$no=0;
								foreach($detTopPlus AS $val => $valx){
									$no++;
									$ListdetTopPlus	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_ori']."' ORDER BY a.nm_material ASC")->result_array();
									?>
									<tr>
										<td><?= $ListdetTopPlus[0]['category'];?><input type='hidden' name='ListDetailPlus4[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
										<td>
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
										</td>
										<td align='center'><input type='text' name='ListDetailPlus4[<?=$no;?>][last_cost]' id='last_topcoat_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
									</tr>
									<?php
								}
							?>
						<!-- LINER MATERIAL ADD-->
							<?php
							if(!empty($detTopAdd)){
									$no=0;
									foreach($detTopAdd AS $val => $valx){
										$no++;
										$ListdetTopAdd	= $this->db->query("SELECT a.*, b.category FROM raw_materials a LEFT JOIN raw_categories b ON a.id_category=b.id_category WHERE a.id_category='".$valx['id_category']."' ORDER BY a.nm_material ASC")->result_array();
										?>
										<tr>
											<td><?= $ListdetTopAdd[0]['category'];?><input type='hidden' name='ListDetailAdd4[<?=$no;?>][id]' value='<?=$valx['id'];?>'></td>
											<td>
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
											<td align='center'><input type='text' name='ListDetailAdd4[<?=$no;?>][last_cost]' id='Addlast_cost4_<?=$no;?>' class='form-control input-sm numberOnly5' style='text-align:right;' value='<?=$valx['last_cost'];?>'></td>
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
<script src="<?php echo base_url('application/views/Est_js/custom_est_deadstok.js'); ?>"></script>
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
        $(".numberOnly5").autoNumeric('init', {mDec: '5', aPad: false});
				
		$('.HideCost').hide();
		$('.Hide').hide();
		
		$(document).on('click', '#simpan-bro', function(e){
			e.preventDefault(); 

			$('#simpan-bro').prop('disabled',false);
			
			swal({
                title: "Are you sure?",
				  text: "You will not be able to process again this data!",
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
						var baseurl		= base_url + active_controller +'/estimasi_edit';
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
										});
                                        window.location.href = base_url + 'est_modifikasi_deadstok';
								}
								else if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000,
									});
								}
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000,
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
