<?php
$id_bq				= $this->uri->segment(3);
$id_milik			= $this->uri->segment(4);
$id_url				= $this->uri->segment(2);

$sqlBef = "SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id='".$id_milik."' LIMIT 1 ";
$restBef = $this->db->query($sqlBef)->result();
$idproduct = $restBef[0]->id_product;

history('View edit product estimasi '.$restBef[0]->id_category.' : '.$id_bq.' / '.$id_milik.' / '.$idproduct);
// echo $idproduct;
//berdasarkan diamater
$header				= $this->db->query("SELECT a.* FROM bq_component_header a WHERE a.id_bq='".$id_bq."' AND a.id_milik='".$id_milik."' AND a.id_product='".$idproduct."' LIMIT 1 ")->result();
// echo "SELECT a.*, b.id FROM bq_component_header a LEFT JOIN product b ON a.nm_product = b.nm_product WHERE a.id_bq='".$id_bq."' AND a.id_milik='".$id_milik."' LIMIT 1 "; 
$series				= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

$product			= $this->db->query("SELECT * FROM product WHERE parent_product='pipe' AND deleted='N'")->result_array();
$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

$criminal_barier	= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
$aplikasi_product	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
$vacum_rate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
$design_life		= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();
$customer			= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

$detLiner			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerPlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$footer				= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."' AND detail_name='LINER THIKNESS / CB' ")->num_rows();

$detStructure			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructurePlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructureAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructureNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='STRUKTUR THICKNESS' ")->num_rows();
$footerStructure		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();

$detEksternal			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
$detEksternalPlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
$detEksternalAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
$detEksternalNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
$footerEksternal		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

$detTopPlus				= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='TOPCOAT' ")->result_array();
$detTopAdd				= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='TOPCOAT' ")->result_array();
$detTopNumRows			= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_milik='".$id_milik."' AND id_product='".$idproduct."'  AND detail_name='TOPCOAT' ")->num_rows();

			

?>
<form action="#" method="POST" id="form_proses_bro_custom" enctype="multipart/form-data">   
	<div class="box box-primary">
		<div class="box-body">
            <?php
            echo form_input(array('id'=>'url_help','name'=>'url_help','class'=>'Hide'),$this->uri->segment(5)); 
            echo form_input(array('id'=>'penanda','name'=>'penanda','class'=>'Hide')); 
            echo form_input(array('id'=>'help_url','name'=>'help_url','class'=>'Hide'), $id_url); 
    	    echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'form-control input-sm','style'=>'width:40%;font-size:18px;background-color: #e8ea6a;font-weight: bold;','readonly'=>'readonly'),$header[0]->id_product);
            echo form_input(array('id'=>'parent_productx','name'=>'parent_productx','class'=>'form-control input-sm','style'=>'width:40%;font-size:18px;background-color: #e8ea6a;font-weight: bold;','readonly'=>'readonly'),$header[0]->parent_product);
            echo form_input(array('id'=>'id_bq','name'=>'id_bq','class'=>'Hide','readonly'=>'readonly'),$id_bq);
            echo form_input(array('id'=>'id_milik','name'=>'id_milik','class'=>'Hide','readonly'=>'readonly'),$id_milik);
            echo form_input(array('id'=>'series','name'=>'series','class'=>'Hide','readonly'=>'readonly'),$header[0]->series);
            echo form_input(array('id'=>'rev','name'=>'rev','class'=>'Hide','readonly'=>'readonly'),$header[0]->rev);
            echo form_input(array('id'=>'status','name'=>'status','class'=>'Hide','readonly'=>'readonly'),$header[0]->status);
            echo form_input(array('id'=>'sts_price','name'=>'sts_price','class'=>'Hide','readonly'=>'readonly'),$header[0]->sts_price);
            echo form_input(array('id'=>'toleransi','name'=>'toleransi','class'=>'Hide'),$header[0]->standart_by);
            ?>						
			<div class='headerTitleGroup'>GROUP COMPONENT</div>
			<div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Product</b></label>
				<div class='col-sm-4'>    
                    <?php
                        echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm','readonly'=>'readonly'),$header[0]->nm_product);
			        ?> 
				</div>
				<label class='label-control col-sm-2'><b>Series <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>  
                    <?php
						echo form_input(array('type'=>'text','id'=>'series','name'=>'series','class'=>'form-control input-sm','readonly'=>'readonly'), $header[0]->series);	
					?>
				</div>
			</div>
			<div class='form-group row'>		 	 
                <label class='label-control col-sm-2'><b>Component Name <span class='text-red'>*</span></b></label>
                <div class='col-sm-4'>
                    <?php
						echo form_input(array('type'=>'text','id'=>'component_list','name'=>'component_list','class'=>'form-control input-sm','readonly'=>'readonly'), $header[0]->parent_product);	
					?>
                </div>
			</div>
			<!-- //// -->
			<div class='headerTitleGroup'>DETAILED ESTIMATION</div>
			<div class='form-group row'>
                <label class='label-control col-sm-2'><b><span id='id_1'>Inner Dim  </span><span class='text-red'>*</span> <span id='id_2'>| Outter Dim </span><span class='text-red'>*</span></b></label>
                <div class='col-sm-2'>
                    <?php
                        echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Inner Diameter','readonly'=>'readonly'), $header[0]->diameter);
                    ?>
                </div>
                <div class='col-sm-2'>
                    <?php
                        echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-sm','autocomplete'=>'off','placeholder'=>'Outer Diameter','readonly'=>'readonly'), $header[0]->diameter2);
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
											<select name='ListDetail3[<?=$no;?>][id_material]' id='id_materialEks_<?=$no;?>' data-nomor='<?=$no;?>' class='form-control input-sm id_materialEks chosen-select'>
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
											<select name='ListDetailPlus3[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
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
												<select name='ListDetailAdd3[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
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
									<td class="text-center" width='8%'>Containing</td>
									<td class="text-center" width='8%'>Perse (%)</td>
									<td class="text-center" width='8%'>Last Weight</td>
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
											<select name='ListDetailPlus4[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
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
												<select name='ListDetailAdd4[<?=$no;?>][id_material]' id='mid_mtl_plastic' class='form-control input-sm chosen-select'>
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
				echo "&nbsp;<button type='button' name='simpan-bro' style='min-width:100px; margin-bottom:-35px; float: right; margin-right:10px;' id='simpan-bro-custom' class='btn btn-success'>Save</button>";
			?>
            <br>
		</div>	
	</div>		
</form>

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
    #layer_1,
	#perse_topcoat_1{
		display:none;
	}
</style>
<script>	
    swal.close();
    $(".chosen-select").chosen();
    $('.HideCost').hide();
	$('.Hide').hide();
    $("#tamp").hide();
    $('#last_topcoat_1').removeAttr('readonly');
    
    $(document).on('keyup','#last_cost_6', function(){
        var resin 				= getNum($(this).val());
        var persen_katalis 		= getNum($('#Linperse_1').val()/100);
        var persen_sm 			= getNum($('#Linperse_2').val()/100);
        var persen_coblat 		= getNum($('#Linperse_3').val()/100);
        var persen_dma 			= getNum($('#Linperse_4').val()/100);
        var persen_hydroquinone = getNum($('#Linperse_5').val()/100);
        var persen_methanol 	= getNum($('#Linperse_6').val()/100);
        
        var layer_katalis 		= getNum($('#Lincontaining_1').val());
        var layer_sm 			= getNum($('#Lincontaining_2').val());
        var layer_coblat 		= getNum($('#Lincontaining_3').val());
        var layer_dma 			= getNum($('#Lincontaining_4').val());
        var layer_hydroquinone 	= getNum($('#Lincontaining_5').val());
        var layer_methanol 		= getNum($('#Lincontaining_6').val());
        
        $('#Linlast_cost_1').val(RoundUp(resin * persen_katalis * layer_katalis));
        $('#Linlast_cost_2').val(RoundUp(resin * persen_sm * layer_sm));
        $('#Linlast_cost_3').val(RoundUp(resin * persen_coblat * layer_coblat));
        $('#Linlast_cost_4').val(RoundUp(resin * persen_dma * layer_dma));
        $('#Linlast_cost_5').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
        $('#Linlast_cost_6').val(RoundUp(resin * persen_methanol * layer_methanol));
    });

    $(document).on('keyup','#last_costStr_5', function(){
        var resin 				= getNum($(this).val());
        var persen_katalis 		= getNum($('#Linperse2_1').val()/100);
        var persen_sm 			= getNum($('#Linperse2_2').val()/100);
        var persen_coblat 		= getNum($('#Linperse2_3').val()/100);
        var persen_dma 			= getNum($('#Linperse2_4').val()/100);
        var persen_hydroquinone = getNum($('#Linperse2_5').val()/100);
        var persen_methanol 	= getNum($('#Linperse2_6').val()/100);
        
        var layer_katalis 		= getNum($('#Lincontaining2_1').val());
        var layer_sm 			= getNum($('#Lincontaining2_2').val());
        var layer_coblat 		= getNum($('#Lincontaining2_3').val());
        var layer_dma 			= getNum($('#Lincontaining2_4').val());
        var layer_hydroquinone 	= getNum($('#Lincontaining2_5').val());
        var layer_methanol 		= getNum($('#Lincontaining2_6').val());
        
        $('#Linlast_cost2_1').val(RoundUp(resin * persen_katalis * layer_katalis));
        $('#Linlast_cost2_2').val(RoundUp(resin * persen_sm * layer_sm));
        $('#Linlast_cost2_3').val(RoundUp(resin * persen_coblat * layer_coblat));
        $('#Linlast_cost2_4').val(RoundUp(resin * persen_dma * layer_dma));
        $('#Linlast_cost2_5').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
        $('#Linlast_cost2_6').val(RoundUp(resin * persen_methanol * layer_methanol));
    });

    $(document).on('keyup','#last_costEks_5', function(){
        var resin 				= getNum($(this).val());
        var persen_katalis 		= getNum($('#Linperse3_1').val()/100);
        var persen_sm 			= getNum($('#Linperse3_2').val()/100);
        var persen_coblat 		= getNum($('#Linperse3_3').val()/100);
        var persen_dma 			= getNum($('#Linperse3_4').val()/100);
        var persen_hydroquinone = getNum($('#Linperse3_5').val()/100);
        var persen_methanol 	= getNum($('#Linperse3_6').val()/100);
        
        var layer_katalis 		= getNum($('#Lincontaining3_1').val());
        var layer_sm 			= getNum($('#Lincontaining3_2').val());
        var layer_coblat 		= getNum($('#Lincontaining3_3').val());
        var layer_dma 			= getNum($('#Lincontaining3_4').val());
        var layer_hydroquinone 	= getNum($('#Lincontaining3_5').val());
        var layer_methanol 		= getNum($('#Lincontaining3_6').val());
        
        $('#Linlast_cost3_1').val(RoundUp(resin * persen_katalis * layer_katalis));
        $('#Linlast_cost3_2').val(RoundUp(resin * persen_sm * layer_sm));
        $('#Linlast_cost3_3').val(RoundUp(resin * persen_coblat * layer_coblat));
        $('#Linlast_cost3_4').val(RoundUp(resin * persen_dma * layer_dma));
        $('#Linlast_cost3_5').val(RoundUp(resin * persen_hydroquinone * layer_hydroquinone));
        $('#Linlast_cost3_6').val(RoundUp(resin * persen_methanol * layer_methanol));
    });

    $(document).on('keyup','#last_topcoat_1', function(){
        var resin 			= getNum($(this).val());
        var persen_katalis4 = getNum($('#perse_topcoat_2').val()/100);
        var persen_color4 	= getNum($('#perse_topcoat_3').val()/100);
        var persen_tin4 	= getNum($('#perse_topcoat_4').val()/100);
        var persen_chl4 	= getNum($('#perse_topcoat_5').val()/100);
        var persen_stery4 	= getNum($('#perse_topcoat_6').val()/100);
        var persen_wax4 	= getNum($('#perse_topcoat_7').val()/100);
        var persen_mch4 	= getNum($('#perse_topcoat_8').val()/100);
        
        var layer_katalis4 	= getNum($('#cont_topcoat_2').val());
        var layer_color4 	= getNum($('#cont_topcoat_3').val());
        var layer_tin4 		= getNum($('#cont_topcoat_4').val());
        var layer_chl4 		= getNum($('#cont_topcoat_5').val());
        var layer_stery4 	= getNum($('#cont_topcoat_6').val());
        var layer_wax4 		= getNum($('#cont_topcoat_7').val());
        var layer_mch4 		= getNum($('#cont_topcoat_8').val());
        
        $('#last_topcoat_2').val(RoundUp(resin * persen_katalis4 * layer_katalis4));
        $('#last_topcoat_3').val(RoundUp(resin * persen_color4 * layer_color4));
        $('#last_topcoat_4').val(RoundUp(resin * persen_tin4 * layer_tin4));
        $('#last_topcoat_5').val(RoundUp(resin * persen_chl4 * layer_chl4));
        $('#last_topcoat_6').val(RoundUp(resin * persen_stery4 * layer_stery4));
        $('#last_topcoat_7').val(RoundUp(resin * persen_wax4 * layer_wax4));
        $('#last_topcoat_8').val(RoundUp(resin * persen_mch4 * layer_mch4));
    });

    //Liner
    $(document).on('keyup', '.perse', function(){
        var TotResin	= parseFloat($('#last_cost_6').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= TotResin * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    $(document).on('keyup', '.perseLinAdd', function(){
        var TotResin	= parseFloat($('#last_cost_6').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= TotResin * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    //Structure
    $(document).on('keyup', '.perseStr', function(){
        var TotResin	= parseFloat($('#last_costStr_5').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= TotResin * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });
    $(document).on('keyup', '.perseStrAdd', function(){
        var TotResin	= parseFloat($('#last_costStr_5').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= TotResin * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    //Ekternal
    $(document).on('keyup', '.perseEks', function(){
        var TotResin	= parseFloat($('#last_costEks_5').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= TotResin * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    $(document).on('keyup', '.perseEksAdd', function(){
        var TotResin	= parseFloat($('#last_costEks_5').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= TotResin * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    //Top Coat
    $(document).on('keyup', '.perseTc', function(){
        var LastCoat	= parseFloat($('#last_topcoat_1').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= LastCoat * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    $(document).on('keyup', '.perseTcAdd', function(){
        var LastCoat	= parseFloat($('#last_topcoat_1').val());
        var perse		= parseFloat($(this).val() / 100);
        var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
        var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
        var Hasil		= LastCoat * perse * containing;
        if(isNaN(Hasil)){ var Hasil = 0;}
        lastWeight.val(Hasil.toFixed(3));
    });

    $(document).on('change', '.id_material, .id_materialSTr, .id_materialEks', function(){
        var id_material	= $(this).val();
        if(id_material == 'MTL-1903000'){
            $(this).parent().parent().find("td:nth-child(3) input").val('0');
            $(this).parent().parent().find("td:nth-child(5) input").val('0');
        }
        // else{
        //     $(this).parent().parent().find("td:nth-child(3) input").val('');
        //     $(this).parent().parent().find("td:nth-child(5) input").val('');
        // }
    });

    //Matertial Add
    //LINER
    $(document).on('keyup', '.ChangeContaining', function(){
        var total_resin	= $('#last_cost_6').val();
        var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
        var containing	= $(this).val();
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    $(document).on('keyup', '.ChangePerse', function(){
        var total_resin	= $('#last_cost_6').val();
        var containing	= $(this).parent().parent().find("td:nth-child(4) input").val();
        var perse	    = $(this).val() / 100;
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    //Material Add
    //STRUKTURE
    $(document).on('keyup', '.ChangeContainingStr', function(){
        var total_resin	= $('#last_costStr_5').val();
        var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
        var containing	= $(this).val();
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


    });

    $(document).on('keyup', '.ChangePerseStr', function(){
        var total_resin	= $('#last_costStr_5').val();
        var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
        var perse	= $(this).val() / 100;
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    //Material Add
    //EXTERNAL
    $(document).on('keyup', '.ChangeContainingExt', function(){
        var total_resin	= $('#last_costEks_5').val();
        var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
        var containing	= $(this).val();
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    $(document).on('keyup', '.ChangePerseExt', function(){
        var total_resin	= $('#last_costEks_5').val();
        var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
        var perse	= $(this).val()/ 100;
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    $(document).on('keyup', '.ChangeContainingTC', function(){
        var total_resin	= $('#last_topcoat_1').val();
        var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
        var containing	= $(this).val();
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    $(document).on('keyup', '.ChangePerseTC', function(){
        var total_resin	= $('#last_topcoat_1').val();
        var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
        var perse	= $(this).val() / 100;
        var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
        $(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
        $(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    });

    function RoundUp(x){
        var HasilRoundUp = (Math.ceil(x * 1000 ) / 1000).toFixed(3);
        return HasilRoundUp;
    }

    function RoundUp4(x){
        var HasilRoundUp = (Math.ceil(x * 10000 ) / 10000).toFixed(4);
        return HasilRoundUp;
    }

    function RoundUpEST(x){
        var HasilRoundUp = (Math.ceil(x * 1000 ) / 1000).toFixed(3);
        return HasilRoundUp;
    }

    var nomor	= 1;

    $('#add_liner').click(function(e){
        e.preventDefault();
        AppendBaris_Liner(nomor);
        var nilaiAwal	= parseInt($("#numberMax_liner").val());
        var nilaiAkhir	= nilaiAwal + 1;
        $("#numberMax_liner").val(nilaiAkhir);
    });

    $('#add_strukture').click(function(e){
        e.preventDefault();
        AppendBaris_Strukture(nomor);
        var nilaiAwal	= parseInt($("#numberMax_strukture").val());
        var nilaiAkhir	= nilaiAwal + 1;
        $("#numberMax_strukture").val(nilaiAkhir);
    });

    $('#add_external').click(function(e){
        e.preventDefault();
        AppendBaris_External(nomor);
        var nilaiAwal	= parseInt($("#numberMax_external").val());
        var nilaiAkhir	= nilaiAwal + 1;
        $("#numberMax_external").val(nilaiAkhir);
    });

    $('#add_topcoat').click(function(e){
        e.preventDefault();
        AppendBaris_TopCoat(nomor);
        var nilaiAwal	= parseInt($("#numberMax_topcoat").val());
        var nilaiAkhir	= nilaiAwal + 1;
        $("#numberMax_topcoat").val(nilaiAkhir);
    });
            
    function AppendBaris_Liner(intd){
        var nomor	= 1;
        var valuex	= $('#detail_body_liner').find('tr').length;
        if(valuex > 0){
            var akhir	= $('#detail_body_liner tr:last').attr('id');
            var det_id	= akhir.split('_');
            var nomor	= parseInt(det_id[1])+1;
        }

        var Rows	 = "<tr id='trliner_"+nomor+"'>";
            Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
            Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Liner("+nomor+")' title='Delete Record'>Delete</button></div>";
            Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Liner["+nomor+"][last_full]' id='last_full_liner_"+nomor+"' value='0' autocomplete='off'>";
            Rows 	+= 	"</td>";
            Rows	+= 	"<td align='left'  width = '250px'>";
            Rows	+=		"Category<select name='ListDetailAdd_Liner["+nomor+"][id_category]' id='id_category_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td align='left'>";
            Rows	+=		"Material<select name='ListDetailAdd_Liner["+nomor+"][id_material]' id='id_material_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContaining' name='ListDetailAdd_Liner["+nomor+"][containing]' id='containing_liner_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerse' name='ListDetailAdd_Liner["+nomor+"][perse]' id='perse_liner_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td width='150px'>";
            Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_Liner["+nomor+"][last_cost]' id='last_cost_liner_"+nomor+"' value='0' readonly autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= "</tr>";

        $('#detail_body_liner').append(Rows);
        var id_category_liner_ 	= "#id_category_liner_"+nomor;
        var id_material_liner_ 	= "#id_material_liner_"+nomor;
        
        $.ajax({
            url: base_url +'index.php/cust_component/getCategory',
            cache: false,
            type: "POST",
            dataType: "json",
            success: function(data){
                $(id_category_liner_).html(data.option).trigger("chosen:updated");
            }
        });
        
        $(".numberOnly").on("keypress keyup blur",function (event) {
            if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
                event.preventDefault();
            }
        });
        
        $("#id_category_liner_"+nomor+"").on('change', function(e){
            e.preventDefault();
            $.ajax({
                url: base_url +'index.php/cust_component/getMaterial',
                cache: false,
                type: "POST",
                data: "id_category="+$(this).val(),
                dataType: "json",
                success: function(data){
                    $(id_material_liner_).html(data.option).trigger("chosen:updated");
                }
            });
        });
        nomor++;
    }

    function AppendBaris_Strukture(intd){
        var nomor	= 1;
        var valuex	= $('#detail_body_strukture').find('tr').length;
        if(valuex > 0){
            var akhir	= $('#detail_body_strukture tr:last').attr('id');
            var det_id	= akhir.split('_');
            var nomor	= parseInt(det_id[1])+1;
        }

        var Rows	 = "<tr id='trstrukture_"+nomor+"'>";
            Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
            Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Strukture("+nomor+")' title='Delete Record'>Delete</button></div>";
            Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Strukture["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
            Rows 	+= 	"</td>";
            Rows	+= 	"<td align='left'  width = '250px'>";
            Rows	+=		"Category<select name='ListDetailAdd_Strukture["+nomor+"][id_category]' id='id_category_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td align='left'>";
            Rows	+=		"Material<select name='ListDetailAdd_Strukture["+nomor+"][id_material]' id='id_material_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingStr' name='ListDetailAdd_Strukture["+nomor+"][containing]' id='containing_strukture_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseStr' name='ListDetailAdd_Strukture["+nomor+"][perse]' id='perse_strukture_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td width='150px'>";
            Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_Strukture["+nomor+"][last_cost]' id='last_cost_strukture_"+nomor+"' value='0' readonly autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= "</tr>";

        $('#detail_body_strukture').append(Rows);
        var id_category_strukture_ 	= "#id_category_strukture_"+nomor;
        var id_material_strukture_ 	= "#id_material_strukture_"+nomor;
        
        $.ajax({
            url: base_url +'index.php/cust_component/getCategory',
            cache: false,
            type: "POST",
            dataType: "json",
            success: function(data){
                $(id_category_strukture_).html(data.option).trigger("chosen:updated");
            }
        });
        
        $(".numberOnly").on("keypress keyup blur",function (event) {
            if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
                event.preventDefault();
            }
        });
        
        $("#id_category_strukture_"+nomor+"").on('change', function(e){
            e.preventDefault();
            $.ajax({
                url: base_url +'index.php/cust_component/getMaterial',
                cache: false,
                type: "POST",
                data: "id_category="+$(this).val(),
                dataType: "json",
                success: function(data){
                    $(id_material_strukture_).html(data.option).trigger("chosen:updated");
                }
            });
        });
        nomor++;
    }

    function AppendBaris_External(intd){
        var nomor	= 1;
        var valuex	= $('#detail_body_external').find('tr').length;
        if(valuex > 0){
            var akhir	= $('#detail_body_external tr:last').attr('id');
            var det_id	= akhir.split('_');
            var nomor	= parseInt(det_id[1])+1;
        }

        var Rows	 = "<tr id='trexternal_"+nomor+"'>";
            Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
            Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_External("+nomor+")' title='Delete Record'>Delete</button></div>";
            Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_External["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
            Rows 	+= 	"</td>";
            Rows	+= 	"<td align='left'  width = '250px'>";
            Rows	+=		"Category<select name='ListDetailAdd_External["+nomor+"][id_category]' id='id_category_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td align='left'>";
            Rows	+=		"Material<select name='ListDetailAdd_External["+nomor+"][id_material]' id='id_material_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingExt' name='ListDetailAdd_External["+nomor+"][containing]' id='containing_external_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseExt' name='ListDetailAdd_External["+nomor+"][perse]' id='perse_external_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td width='150px'>";
            Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_External["+nomor+"][last_cost]' id='last_cost_external_"+nomor+"' value='0' readonly autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= "</tr>";

        $('#detail_body_external').append(Rows);
        var id_category_external_ 	= "#id_category_external_"+nomor;
        var id_material_external_ 	= "#id_material_external_"+nomor;
        
        $.ajax({
            url: base_url +'index.php/cust_component/getCategory',
            cache: false,
            type: "POST",
            dataType: "json",
            success: function(data){
                $(id_category_external_).html(data.option).trigger("chosen:updated");
            }
        });
        
        $(".numberOnly").on("keypress keyup blur",function (event) {
            if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
                event.preventDefault();
            }
        });
        
        $("#id_category_external_"+nomor+"").on('change', function(e){
            e.preventDefault();
            $.ajax({
                url: base_url +'index.php/cust_component/getMaterial',
                cache: false,
                type: "POST",
                data: "id_category="+$(this).val(),
                dataType: "json",
                success: function(data){
                    $(id_material_external_).html(data.option).trigger("chosen:updated");
                }
            });
        });
        nomor++;
    }

    function AppendBaris_TopCoat(intd){
        var nomor	= 1;
        var valuex	= $('#detail_body_topcoat').find('tr').length;
        if(valuex > 0){
            var akhir	= $('#detail_body_topcoat tr:last').attr('id');
            var det_id	= akhir.split('_');
            var nomor	= parseInt(det_id[1])+1;
        }

        var Rows	 = "<tr id='trtopcoat_"+nomor+"'>";
            Rows 	+= 	"<td align=\"left\" width = '45px' style='vertical-align: middle;'>";
            Rows 	+=		"<div style='text-align: center;'><button type='button' class='btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_TopCoat("+nomor+")' title='Delete Record'>Delete</button></div>";
            Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_TopCoat["+nomor+"][last_full]' id='last_full_topcoat_"+nomor+"' value='0' autocomplete='off'>";
            Rows 	+= 	"</td>";
            Rows	+= 	"<td align='left'  width = '250px'>";
            Rows	+=		"Category<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td align='left'>";
            Rows	+=		"Material<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Comparison<input type='text' class='form-control numberOnly ChangeContainingTC' name='ListDetailAdd_TopCoat["+nomor+"][containing]' id='containing_topcoatadd_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td  width='150px'>";
            Rows	+=		"Percent<input type='text' class='form-control numberOnly ChangePerseTC' name='ListDetailAdd_TopCoat["+nomor+"][perse]' id='perse_topcoatadd_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= 	"<td width='150px'>";
            Rows	+=		"Value /Kg<input type='text' class='form-control Cost' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' id='last_cost_topcoat_"+nomor+"' value='0' readonly autocomplete='off'>";
            Rows	+= 	"</td>";
            Rows	+= "</tr>";

        $('#detail_body_topcoat').append(Rows);
        var id_category_topcoat_ 	= "#id_category_topcoat_"+nomor;
        var id_material_topcoat_ 	= "#id_material_topcoat_"+nomor;
        
        $.ajax({
            url: base_url +'index.php/cust_component/getCategory',
            cache: false,
            type: "POST",
            dataType: "json",
            success: function(data){
                $(id_category_topcoat_).html(data.option).trigger("chosen:updated");
            }
        });
        
        $(".numberOnly").on("keypress keyup blur",function (event) {
            if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
                event.preventDefault();
            }
        });
        
        $("#id_category_topcoat_"+nomor+"").on('change', function(e){
            e.preventDefault();
            $.ajax({
                url: base_url +'index.php/cust_component/getMaterial',
                cache: false,
                type: "POST",
                data: "id_category="+$(this).val(),
                dataType: "json",
                success: function(data){
                    $(id_material_topcoat_).html(data.option).trigger("chosen:updated");
                }
            });
        });
        nomor++;
    }

    //delete add material
    function delRow_Liner(row){
        $('#trliner_'+row).remove();
    }
    function delRow_Strukture(row){
        $('#trstrukture_'+row).remove();
    }
    function delRow_External(row){
        $('#trexternal_'+row).remove();
    }
    function delRow_TopCoat(row){
        $('#trtopcoat_'+row).remove();
    }
    function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
    }
</script>