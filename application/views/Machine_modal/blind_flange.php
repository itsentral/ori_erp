<?php
$id_bq				= $this->uri->segment(3);
$id_milik			= $this->uri->segment(4);
$id_url				= $this->uri->segment(2);

$sqlBef = "SELECT * FROM bq_detail_header WHERE id_bq='".$id_bq."' AND id='".$id_milik."' LIMIT 1 ";
$restBef = $this->db->query($sqlBef)->result();
$idproduct = $restBef[0]->id_product; 

history('View edit product estimasi blind flange : '.$id_bq.' / '.$id_milik.' / '.$idproduct);
// echo $idproduct;
//berdasarkan diamater
$header				= $this->db->query("SELECT a.* FROM bq_component_header a WHERE a.id_bq='".$id_bq."' AND a.id_product='".$idproduct."' AND a.id_milik='".$id_milik."' LIMIT 1 ")->result();

$series				= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();

$product			= $this->db->query("SELECT * FROM product WHERE parent_product='blind flange' AND deleted='N'")->result_array();
$ListResinSystem	= $this->db->query("SELECT * FROM list_help WHERE group_by ='resin_system' AND sts='Y'")->result_array();
$ListPressure		= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
$ListLiner			= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

$criminal_barier	= $this->db->query("SELECT * FROM list_help WHERE group_by ='fluida' AND sts='Y'")->result_array();
$aplikasi_product	= $this->db->query("SELECT * FROM list_help WHERE group_by ='app' AND sts='Y'")->result_array();
$vacum_rate			= $this->db->query("SELECT * FROM list_help WHERE group_by ='vacum_rate' AND sts='Y'")->result_array();
$design_life		= $this->db->query("SELECT * FROM list_help WHERE group_by ='design_life' AND sts='Y'")->result_array();
$customer			= $this->db->query("SELECT id_customer, nm_customer FROM customer ORDER BY nm_customer ASC")->result_array();

$detLiner			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerPlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$footer				= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->result_array();
$detLinerNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."' AND detail_name='LINER THIKNESS / CB' ")->num_rows();

$detStructure			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructurePlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructureAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();
$detStructureNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->num_rows();
$footerStructure		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='STRUKTUR THICKNESS' ")->result_array();

$detEksternal			= $this->db->query("SELECT * FROM bq_component_detail WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
$detEksternalPlus		= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
$detEksternalAdd		= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();
$detEksternalNumRows	= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->num_rows();
$footerEksternal		= $this->db->query("SELECT * FROM bq_component_footer WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='EXTERNAL LAYER THICKNESS' ")->result_array();

$detTopPlus				= $this->db->query("SELECT * FROM bq_component_detail_plus WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='TOPCOAT' ")->result_array();
$detTopAdd				= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='TOPCOAT' ")->result_array();
$detTopNumRows			= $this->db->query("SELECT * FROM bq_component_detail_add WHERE id_bq='".$id_bq."' AND id_product='".$idproduct."' AND id_milik='".$id_milik."'  AND detail_name='TOPCOAT' ")->num_rows();


?>
<form action="#" method="POST" id="form_proses_bro_blindflange" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-body">	
		<?php
		echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'form-control input-sm','style'=>'width:40%;font-size:18px;background-color: #e8ea6a;font-weight: bold;','readonly'=>'readonly'),$header[0]->id_product);
		echo form_input(array('id'=>'parent_productx','name'=>'parent_productx','class'=>'form-control input-sm','style'=>'width:40%;font-size:18px;background-color: #e8ea6a;font-weight: bold;','readonly'=>'readonly'),$header[0]->parent_product);
		
		?>
		<div class='headerTitleGroup'>GROUP COMPONENT</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Diameter <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>              
				<?php
					echo form_input(array('id'=>'top_type','name'=>'top_type','class'=>'form-control input-sm','readonly'=>'readonly'),$header[0]->nm_product);
					echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'Hide','readonly'=>'readonly'),$header[0]->diameter);
					// echo form_input(array('id'=>'id_product','name'=>'id_product','class'=>'Hide'),$header[0]->id_product);
					echo form_input(array('id'=>'id_bq','name'=>'id_bq','class'=>'Hide','readonly'=>'readonly'),$id_bq);
					echo form_input(array('id'=>'id_milik','name'=>'id_milik','class'=>'Hide','readonly'=>'readonly'),$id_milik);
					echo form_input(array('id'=>'series','name'=>'series','class'=>'Hide','readonly'=>'readonly'),$header[0]->series);
					echo form_input(array('id'=>'rev','name'=>'rev','class'=>'Hide','readonly'=>'readonly'),$header[0]->rev);
					echo form_input(array('id'=>'status','name'=>'status','class'=>'Hide','readonly'=>'readonly'),$header[0]->status);
					echo form_input(array('id'=>'sts_price','name'=>'sts_price','class'=>'Hide','readonly'=>'readonly'),$header[0]->sts_price);
					echo form_input(array('id'=>'toleransi','name'=>'toleransi','class'=>'Hide','readonly'=>'readonly'),$header[0]->standart_by);
					echo form_input(array('id'=>'url_help','name'=>'url_help','class'=>'Hide','readonly'=>'readonly'),$this->uri->segment(5)); 
					echo form_input(array('id'=>'penanda','name'=>'penanda','class'=>'Hide','readonly'=>'readonly')); 
					echo form_input(array('id'=>'help_url','name'=>'help_url','class'=>'Hide'), $id_url); 
				?>
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
		<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Standard Default<span class='text-red'>*</span></b></label>  
			<div class='col-sm-3'> 
				<!--
				<select name='standart_code' id='standart_code' class='form-control input-sm'>
					<option value='0'>List Empty</option>
				</select>
				-->
				<?php
					echo form_input(array('id'=>'standart_code','name'=>'standart_code','class'=>'form-control input-sm','autocomplete'=>'off','readonly'=>'readonly'), $header[0]->standart_code);		
					echo form_input(array('id'=>'standart_code2','name'=>'standart_code2','class'=>'form-control input-sm Hide','autocomplete'=>'off'));						
				?>
			</div>
			<div class='col-sm-1'>
				<?php
					echo "<button type='button' name='updateDefault' id='updateDefault' class='btn btn-sm btn-success' data-id_milik='".$id_milik."' data-standart_code='".$header[0]->standart_code."' data-parent_product='".$header[0]->parent_product."' data-diameter='".$header[0]->diameter."' data-id_product='".$header[0]->id_product."'>Default</button>";
				?>	
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2 tampx'><b>Set Standard Default<span class='text-red tampx'>*</span></b></label>  
			<div class='col-sm-4'> 
				<select name='standart_codex2' id='standart_codex2' class='form-control input-sm tampx'>
					<option value='0'>List Empty</option>
				</select>
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'></label>  
			<div class='col-sm-4'>              
				<div class='tamp' style='font-weight: bold; background-color: #f1f1f1; padding: 1px 0px 0px 8px;border-radius: 0px 10px 10px 0px;'></div>
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
			<label class='label-control col-sm-2'><b>Thickness Design | Est  <span class='text-red'>*</span></b></label>
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
					echo form_input(array('type'=>'text','id'=>'waste','name'=>'waste','class'=>'form-control input-sm numberOnly','placeholder'=>'Waste','readonly'=>'readonly'));	
					
					echo form_input(array('type'=>'text','id'=>'area','name'=>'area','class'=>'HideCost'), $header[0]->area);
					echo form_input(array('type'=>'text','id'=>'ThLin','name'=>'ThLin','class'=>'HideCost'), str_replace(',', '.', $header[0]->liner));
					
					echo form_input(array('type'=>'text','id'=>'parent_product','name'=>'parent_product','class'=>'HideCost'), $header[0]->parent_product);
					// echo form_input(array('type'=>'text','id'=>'ThStr','name'=>'ThStr','class'=>'HideCost'));
					echo form_input(array('type'=>'text','id'=>'AddLinNum','name'=>'AddLinNum','class'=>'HideCost'), $detLinerNumRows);
					echo form_input(array('type'=>'text','id'=>'AddStrNum','name'=>'AddStrNum','class'=>'HideCost'), $detStructureNumRows);
					echo form_input(array('type'=>'text','id'=>'AddEksNum','name'=>'AddEksNum','class'=>'HideCost'), $detEksternalNumRows);
					echo form_input(array('type'=>'text','id'=>'AddTcNum','name'=>'AddTcNum','class'=>'HideCost'), $detTopNumRows);
					
					echo form_input(array('type'=>'text','id'=>'standart_code','name'=>'standart_code','class'=>'HideCost'), $header[0]->standart_code);
						
					
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
					echo form_input(array('id'=>'min_toleran','name'=>'min_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Mix','readonly'=>'readonly'));											
				?>
			</div>
			<div class='col-sm-1'> 
				<?php
					echo form_input(array('id'=>'max_toleran','name'=>'max_toleran','class'=>'form-control input-sm numberOnly','autocomplete'=>'off','placeholder'=>'Max','readonly'=>'readonly'));											
				?>	
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Flange OD  <span class='text-red'>*</span></b></label>
			<div class='col-sm-2'>              
				<?php
					echo form_input(array('id'=>'flange_od','name'=>'flange_od','class'=>'form-control input-sm','autocomplete'=>'off'), floatval($header[0]->flange_od));
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
										$layY	= "<input type='text' name='ListDetail[".$no."][layer]' id='layer_".$no."' data-type='".$valx['id_ori']."' data-nomor='".$no."' class='form-control input-sm numberOnly layer' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
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
												foreach($ListdetLinAdd AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
										</td>
										<td align='center'><input type='text' name='ListDetailAdd[<?=$no;?>][containing]' id='Addcontaining_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=floatval($valx['containing']);?>'></td>
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
							<td width='70%' class="text-center" colspan='4'></td>
							<td width='22%' class="text-left" colspan='2' style='vertical-align: middle;'><b>LINER THICKNESS</b></td>
							<td width='8%' align='center'><input type='text' name='thickLin' id='thickLin' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footer[0]['total'];?>'></td>
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
									$RsY	= "<input type='text' name='ListDetail2[".$no."][containing]' id='containingStr_".$no."' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='".floatval($valx['containing'])."'>";
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
												foreach($ListdetStructureAdd AS $vala => $valxa){
													$selx	= ($valx['id_material'] == $valxa['id_material'])?'selected':'';
													echo "<option value='".$valxa['id_material']."' ".$selx.">".strtoupper($valxa['nm_material'])."</option>";
												}
												echo "<option value='MTL-1903000' ".$selx2.">NONE MATERIAL</option>";
											 ?>
											</select>	
										</td>
										<td align='center'><input type='text' name='ListDetailAdd2[<?=$no;?>][containing]' id='Addcontaining2_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=floatval($valx['containing']);?>'></td>
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
					<tbody>
						<tr>
							<td width='70%' class="text-center" colspan='4'></td>
							<td width='22%' class="text-left" colspan='2' style='vertical-align: middle;'><b>STRUCTURE THICKNESS</b></td>
							<td width='8%' align='center'><input type='text' name='thickStr' id='thickStr' class='form-control input-sm' readonly='readonly' style='text-align:right; width:80px;' value='<?= $footerStructure[0]['total'];?>'></td>
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
									$layY	= "<input type='text' name='ListDetail3[".$no."][layer]' id='layerEks_".$no."' data-type='".$valx['id_ori']."' data-nomor='".$no."' class='form-control input-sm numberOnly layerEks' style='text-align:right; width:80px;' value='".floatval($valx['layer'])."'>";
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
									<td align='center'><input type='text' name='ListDetailPlus3[<?=$no;?>][containing]' id='Lincontaining3_<?=$no;?>' class='form-control input-sm numberOnly perseEksC'  style='text-align:right; width:80px;' value='<?=floatval($valx['containing']);?>'></td>
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
										<td align='center'><input type='text' name='ListDetailAdd3[<?=$no;?>][containing]' id='Addcontaining3_<?=$no;?>' class='form-control input-sm numberOnly perseEksC' readonly='readonly' style='text-align:right; width:80px;' value='<?=floatval($valx['containing']);?>'></td>
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
					<tbody>
						<tr>
							<td width='70%' class="text-center" colspan='4'></td>
							<td width='22%' class="text-left" colspan='2' style='vertical-align: middle;'><b>EXTERNAL THICKNESS</b></td>
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
									<td align='center'><input type='text' name='ListDetailPlus4[<?=$no;?>][containing]' id='cont_topcoat_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=floatval($valx['containing']);?>'></td>
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
										<td align='center'><input type='text' name='ListDetailAdd4[<?=$no;?>][containing]' id='Addcontaining4_<?=$no;?>' class='form-control input-sm numberOnly' readonly='readonly' style='text-align:right; width:80px;' value='<?=floatval($valx['containing']);?>'></td>
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
			// echo "&nbsp;<a href='".site_url($this->uri->segment(1))."' style='min-width:100px;float: right;' class='btn btn-md btn-danger' title='Back To List' data-role='qtip'>Back</a>";
			echo "&nbsp;<button type='button' style='min-width:100px; float: right; margin-right:10px;' id='simpan-bro-blindflange' class='btn btn-success'>Save</button>";
			
		?>
		<br>			
	</div>	
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

	
	#hideCty_3,#hideCty_5,#hideCty_7,#hideCty_9{
		display : none;
	}
	#hideCty2_2,#hideCty2_4,#hideCty2_6,#hideCty2_8,#hideCty2_10,#hideCty2_12{
		display : none;
	}
	#hideCty3_2,#hideCty3_4,#hideCty3_6,#hideCty3_8{
		display : none;
	}
	#cont_topcoat_1, #perse_topcoat_1, #containingEks_9, #containingStr_13, #containingStr_13{
		display : none; 
	}
</style>
<script>	
	$(document).ready(function(){
		swal.close();
		$(".chosen-select").chosen();
		
		$(document).on('click', '#updateDefault', function(e){
			e.preventDefault();
			$("#head_title4").html("<b>DATA DEFAULT "+$(this).data('id_product')+"</b>");
			// $("#view").load(base_url +'index.php/component_custom/modalEditEstDefault/'+$(this).data('id_product'));
			$("#view4").load(base_url +'index.php/machine/modalEditEstDefault/'+$(this).data('id_milik')+'/'+$('#help_url').val());
			$("#ModalView4").modal();
		});
		
		$('.ToleranSt').hide();
		$('.HideCost').hide();
		$('.Hide').hide();
		
		var parent_product	= $('#parent_product').val();
		var diameter		= $('#diameter').val();
		var standart_code	= $('#standart_code').val();
		var id_milik	= $('#id_milik').val();
		
		$.ajax({
			url: base_url +'index.php/json_help/getDefaultEditBq',
			cache: false,
			type: "POST", 
			// data: "id_product="+$('#id_product').val(),
			data: "diameter="+diameter+"&standart="+standart_code+"&parent_product="+parent_product+"&id_milik="+id_milik,
			dataType: "json",
			success: function(data){
				$('#penanda').val(data.hasilx);
				// console.log(data.hasilx);
				if(data.hasilx > 0){
					$('#updateDefault').show();
					$(".tamp").show();
					$(".tampx").show();
					// $("#standart_code").html(data.option).trigger("chosen:updated");
					$(".tamp").html("<p>"+data.tamp+"</p>");
					$(".tamp").css("color", data.color);
					$(".tamp").fadeOut(2000);
					$(".tampx").fadeOut(2000);
					
					$('#standart_code2').val(data.standart);
					
					$('#waste').val(data.waste);
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
				if(data.hasilx < 1){
					$('#updateDefault').hide();
					$(".tamp").show();
					$(".tampx").show();
					// $("#standart_code").html(data.option).trigger("chosen:updated");
					$(".tamp").html("<p>"+data.tamp+"</p>");
					$(".tamp").css("color", data.color);
					$(".tamp").fadeOut(2000);
					
					$.ajax({
						url: base_url +'index.php/component_custom/getStandartCode',
						cache: false,
						type: "POST",
						data: "dim="+data.pipeD+"&parent_product="+data.product,
						dataType: "json",
						success: function(data){
							$("#standart_codex2").html(data.option).trigger("chosen:updated");
						}
					});
				}
				
				
			}
		});
		
		$(document).on('change', '#standart_codex2', function(e){
			e.preventDefault();
			var dim				= $('#diameter').val();
			var parent_product	= $('#parent_product').val();
			$('#standart_code').val($(this).val());
			$('#standart_code2').val($(this).val());
			$.ajax({
				url: base_url +'index.php/json_help/getDefaultOri',
				cache: false,
				type: "POST",
				data: "diameter="+dim+"&standart="+$(this).val()+"&parent_product="+parent_product,
				dataType: "json",
				success: function(data){
					$('#waste').val(data.waste);
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
		
		var design 	= parseFloat($('#design').val());
		var ThLin 	= parseFloat($('#ThLin').val());
		var ThEks 	= parseFloat($('#ThEks').val());
		var HasilT	= design - (ThLin + ThEks);
		$('#ThStr').val(HasilT.toFixed(2));
		
		$(document).on('keyup', '#ThEks', function(){
			var ekternal 	= parseFloat($(this).val());
			var liner 		= parseFloat($('#ThLin').val());
			var design 		= parseFloat($('#design').val()); 
			
			var ThStr	= design - (liner + ekternal);
			if(isNaN(ThStr)){
				var ThStr = 0;
			}
			$('#ThStr').val(ThStr); 
			ChangeHasil();
		});
		   
		$(document).on('keyup', '#design', function(){
			var design 	= parseFloat($('#design').val()); 
			var ThLin 	= parseFloat($('#ThLin').val());
			var ekternal 	= parseFloat($('#ThEks').val());
			
			var ThStr	= design - (ThLin + ekternal);
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
		
		$(document).on('keyup', '#ThEks', function(){
			ChangeHasil();
		});
		
		$(document).on('keyup', '#flange_od', function(){
			ChangeLuasArea();
		});
		
		
		
	});


function getNum(val) {
   if (isNaN(val) || val == '') {
     return 0;
   }
   return parseFloat(val);
}

function LuasArea(){
	var waste		= getNum($('#waste').val()) / 100;
	var od			= getNum($('#flange_od').val());
	
	var Luas_Area_Rumus		= (3.14/4)*((Math.pow(od,2))/1000000)*(1+waste);

	if(isNaN(Luas_Area_Rumus)){
		var Luas_Area_Rumus = 0;
	}
	return Luas_Area_Rumus;
}

function Estimasi(){
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	var thickEks	= getNum($('#thickEks').val());
	
	var topEST	= thickLin + thickStr + thickEks;
	if(isNaN(topEST)){
		var topEST = 0;
	}
	return topEST;
}

function ChangeLuasArea(){
	AllThickness();
	
	var estimasi 	= Estimasi();
	var resinTC		= getNum($('#perse_topcoat_1').val()); 
	
	var LuasAreaX 	= LuasArea();
	var LastCoat	= LuasAreaX * resinTC * 1.2;
	
	if(isNaN(LastCoat)){ var LastCoat = 0;}
	$('#last_topcoat_1').val(LastCoat.toFixed(3));

	$('#estimasi').val(estimasi.toFixed(4));
	$('#area').val(LuasAreaX.toFixed(6));
	
	ChangeAreaToLiner(LuasAreaX);
	ChangeAreaToStr(LuasAreaX);
	ChangeAreaToEks(LuasAreaX);
	
	ChangePlusTopCoat(LastCoat);
	ChangePlusTcAdd(LastCoat);
	
}

function AllThickness(){
	var totthicknessLin1	= getNum($('#total_thickness_2').val());
	var totthicknessLin2	= getNum($('#total_thickness_4').val());
	var totthicknessLin3	= getNum($('#total_thickness_6').val());
	var totthicknessLin4	= getNum($('#total_thickness_8').val());
	var AllThickLin			= totthicknessLin1 + totthicknessLin2 + totthicknessLin3 + totthicknessLin4;
	$('#thickLin').val(AllThickLin.toFixed(4));
	
	var totthicknessStr1	= getNum($('#total_thicknessStr_1').val());
	var totthicknessStr2	= getNum($('#total_thicknessStr_3').val());
	var totthicknessStr3	= getNum($('#total_thicknessStr_5').val());
	var totthicknessStr4	= getNum($('#total_thicknessStr_7').val());
	var AllThickStr			= totthicknessStr1 + totthicknessStr2 + totthicknessStr3 + totthicknessStr4;
	$('#thickStr').val(AllThickStr.toFixed(4));
	
	var totthicknessEks1	= getNum($('#total_thicknessEks_1').val());
	var totthicknessEks2	= getNum($('#total_thicknessEks_3').val());
	var totthicknessEks3	= getNum($('#total_thicknessEks_5').val());
	var totthicknessEks4	= getNum($('#total_thicknessEks_7').val());
	var AllThickEks			= totthicknessEks1 + totthicknessEks2 + totthicknessEks3 + totthicknessEks4;
	$('#thickEks').val(AllThickEks.toFixed(4));
}

function ChangeHasil(){
	
	var ThLin		= getNum($('#ThLin').val());
	var ThStr		= getNum($('#ThStr').val());
	var ThEks		= getNum($('#ThEks').val());
	var minToleran	= getNum($('#min_toleran').val());
	var maxToleran	= getNum($('#max_toleran').val());
	var thickLin	= getNum($('#thickLin').val());
	var thickStr	= getNum($('#thickStr').val());
	var thickEks	= getNum($('#thickEks').val());
	
	var minLinThk	= ThLin - (ThLin * minToleran);
	var maxLinThk	= ThLin + (ThLin * maxToleran);
	var minStrThk	= ThStr - (ThStr * minToleran);
	var maxStrThk	= ThStr + (ThStr * maxToleran);
	var minEksThk	= ThEks - (ThEks * minToleran);
	var maxEksThk	= ThEks + (ThEks * maxToleran);
	if(isNaN(minLinThk)){ var minLinThk = 0;}
	if(isNaN(maxLinThk)){ var maxLinThk = 0;}
	if(isNaN(minStrThk)){ var minStrThk = 0;}
	if(isNaN(maxStrThk)){ var maxStrThk = 0;}
	if(isNaN(minEksThk)){ var minEksThk = 0;}
	if(isNaN(maxEksThk)){ var maxEksThk = 0;}
	
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
	
	if(thickEks < minEksThk){var Hasil3	= "TOO LOW";}
	if(thickEks > maxEksThk){var Hasil3	= "TOO HIGH";}
	if((thickEks > minEksThk && thickEks < maxEksThk) || thickEks == 0){var Hasil3	= "OK";}
	$('#minEks').val(minEksThk.toFixed(4));
	$('#maxEks').val(maxEksThk.toFixed(4));
	// console.log(Hasil3);
	$('#hasilEks').val(Hasil3);
}

function LastWeight(){
	var area	= getNum($('#area').val());
	return area;
}

function ChangePlus(Area){
	// console.log(Area);
	var Con1	= getNum($('#Lincontaining_1').val());
	var Con2	= getNum($('#Lincontaining_2').val());
	var Con3	= getNum($('#Lincontaining_3').val());
	var Con4	= getNum($('#Lincontaining_4').val());
	var Con5	= getNum($('#Lincontaining_5').val());
	var Con6	= getNum($('#Lincontaining_6').val());
	
	var Per1	= getNum($('#Linperse_1').val()) /100;
	var Per2	= getNum($('#Linperse_2').val()) /100;
	var Per3	= getNum($('#Linperse_3').val()) /100;
	var Per4	= getNum($('#Linperse_4').val()) /100;
	var Per5	= getNum($('#Linperse_5').val()) /100;
	var Per6	= getNum($('#Linperse_6').val()) /100;
	
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
	var Con1	= getNum($('#Lincontaining2_1').val());
	var Con2	= getNum($('#Lincontaining2_2').val());
	var Con3	= getNum($('#Lincontaining2_3').val());
	var Con4	= getNum($('#Lincontaining2_4').val());
	var Con5	= getNum($('#Lincontaining2_5').val());
	var Con6	= getNum($('#Lincontaining2_6').val());
	
	var Per1	= getNum($('#Linperse2_1').val()) /100;
	var Per2	= getNum($('#Linperse2_2').val()) /100;
	var Per3	= getNum($('#Linperse2_3').val()) /100;
	var Per4	= getNum($('#Linperse2_4').val()) /100;
	var Per5	= getNum($('#Linperse2_5').val()) /100;
	var Per6	= getNum($('#Linperse2_6').val()) /100;
	
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

function ChangePlusEks(Area){
	var Con1	= getNum($('#Lincontaining3_1').val());
	var Con2	= getNum($('#Lincontaining3_2').val());
	var Con3	= getNum($('#Lincontaining3_3').val());
	var Con4	= getNum($('#Lincontaining3_4').val());
	var Con5	= getNum($('#Lincontaining3_5').val());
	var Con6	= getNum($('#Lincontaining3_6').val());
	
	var Per1	= getNum($('#Linperse3_1').val()) /100;
	var Per2	= getNum($('#Linperse3_2').val()) /100;
	var Per3	= getNum($('#Linperse3_3').val()) /100;
	var Per4	= getNum($('#Linperse3_4').val()) /100;
	var Per5	= getNum($('#Linperse3_5').val()) /100;
	var Per6	= getNum($('#Linperse3_6').val()) /100;
	
	var Hasil1	= Area * Con1 * Per1;
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	
	$('#Linlast_cost3_1').val(Math.ceil(Hasil1 * 1000)/1000);
	$('#Linlast_cost3_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#Linlast_cost3_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#Linlast_cost3_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#Linlast_cost3_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#Linlast_cost3_6').val(Math.ceil(Hasil6 * 1000)/1000);
}

function ChangePlusTopCoat(Area){
	var Con2	= getNum($('#cont_topcoat_2').val());
	var Con3	= getNum($('#cont_topcoat_3').val());
	var Con4	= getNum($('#cont_topcoat_4').val());
	var Con5	= getNum($('#cont_topcoat_5').val());
	var Con6	= getNum($('#cont_topcoat_6').val());
	var Con7	= getNum($('#cont_topcoat_7').val());
	var Con8	= getNum($('#cont_topcoat_8').val());
	
	var Per2	= getNum($('#perse_topcoat_2').val()) /100;
	var Per3	= getNum($('#perse_topcoat_3').val()) /100;
	var Per4	= getNum($('#perse_topcoat_4').val()) /100;
	var Per5	= getNum($('#perse_topcoat_5').val()) /100;
	var Per6	= getNum($('#perse_topcoat_6').val()) /100;
	var Per7	= getNum($('#perse_topcoat_7').val()) /100;
	var Per8	= getNum($('#perse_topcoat_8').val()) /100;
	
	var Hasil2	= Area * Con2 * Per2;
	var Hasil3	= Area * Con3 * Per3;
	var Hasil4	= Area * Con4 * Per4;
	var Hasil5	= Area * Con5 * Per5;
	var Hasil6	= Area * Con6 * Per6;
	var Hasil7	= Area * Con7 * Per7;
	var Hasil8	= Area * Con8 * Per8;
	
	$('#last_topcoat_2').val(Math.ceil(Hasil2 * 1000)/1000);
	$('#last_topcoat_3').val(Math.ceil(Hasil3 * 1000)/1000);
	$('#last_topcoat_4').val(Math.ceil(Hasil4 * 1000)/1000);
	$('#last_topcoat_5').val(Math.ceil(Hasil5 * 1000)/1000);
	$('#last_topcoat_6').val(Math.ceil(Hasil6 * 1000)/1000);
	$('#last_topcoat_7').val(Math.ceil(Hasil7 * 1000)/1000);
	$('#last_topcoat_8').val(Math.ceil(Hasil8 * 1000)/1000);
}

function ChangeAreaToLiner(Area){
	var value_1 		= getNum($('#value_1').val());
	var value_2 		= getNum($('#value_2').val());
	var value_4 		= getNum($('#value_4').val());
	var value_6 		= getNum($('#value_6').val());
	var value_8 		= getNum($('#value_8').val());
	var layer_2 		= getNum($('#layer_2').val());
	var layer_4 		= getNum($('#layer_4').val());
	var layer_6 		= getNum($('#layer_6').val());
	var layer_8 		= getNum($('#layer_8').val());
	var containing_3	= getNum($('#containing_3').val());
	var containing_5	= getNum($('#containing_5').val());
	var containing_7	= getNum($('#containing_7').val());
	var containing_9	= getNum($('#containing_9').val());
	var containing_10	= getNum($('#containing_10').val());
	
	var LinFakVeil		= getNum($("#lin_faktor_veil").val());
	var LinFakVeilAdd	= getNum($("#lin_faktor_veil_add").val());
	var LinFakCsm		= getNum($("#lin_faktor_csm").val());
	var LinFakCsmAdd	= getNum($("#lin_faktor_csm_add").val());
	
	var id_material_1 	= $('#id_material_1').val();
	var diameter		= $('#diameter').val();
	if(diameter < 25){var Hit = 800;}else{var Hit = 1350;}
	
	var last_cost_1 	= Area * value_1 * 1.5 * Hit ;
	var last_cost_2 	= ((Area * value_2 * layer_2)/1000)	* LinFakVeil;
	var last_cost_4 	= ((Area * value_4 * layer_4)/1000)	* LinFakVeilAdd;
	var last_cost_6 	= ((Area * value_6 * layer_6)/1000)	* LinFakCsm;
	var last_cost_8 	= ((Area * value_8 * layer_8)/1000)	* LinFakCsmAdd; 
	var resin3			= last_cost_2 * containing_3;
	var resin5			= last_cost_4 * containing_5;
	var resin7			= last_cost_6 * containing_7;
	var resin9			= last_cost_8 * containing_9;
	
	if(resin3 == 0 && resin5 == 0 && resin7 == 0 && resin9 == 0){
		var resiTot		= 0;
	}
	else{
		var resiTot		= (Area * 1.2 * containing_10) + resin3 + resin5 + resin7 + resin9;
	}
	ChangePlus(resiTot);
	ChangePlusAdd(resiTot);
	
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
	var valueStr_1 		= getNum($('#valueStr_1').val());
	var valueStr_3 		= getNum($('#valueStr_3').val());
	var valueStr_5 		= getNum($('#valueStr_5').val());
	var valueStr_7 		= getNum($('#valueStr_7').val());
	
	var layerStr_1 		= getNum($('#layerStr_1').val());
	var layerStr_3 		= getNum($('#layerStr_3').val());
	var layerStr_5 		= getNum($('#layerStr_5').val());
	var layerStr_7 		= getNum($('#layerStr_7').val());
	
	var containingStr_2		= getNum($('#containingStr_2').val());
	var containingStr_4		= getNum($('#containingStr_4').val());
	var containingStr_6		= getNum($('#containingStr_6').val());
	var containingStr_8		= getNum($('#containingStr_8').val());
	
	var StrFakCsm		= getNum($("#str_faktor_csm").val());
	var StrFakCsmAdd	= getNum($("#str_faktor_csm_add").val());
	var StrFakWr		= getNum($("#str_faktor_wr").val());
	var StrFakWrAdd		= getNum($("#str_faktor_wr_add").val());
	
	var diameter		= $('#diameter').val();
	var kali = 1; 
	if(diameter < 150){
		var kali = 1.3; 
	}
	
	var kali2 = 1; 
	if(diameter < 150){
		var kali2 = 1.2; 
	}
	else if(diameter >= 200 && diameter <= 350){
		var kali2 = 1.12; 
	}
	
	var last_costStr_1 		= ((Area * valueStr_1 * layerStr_1)/1000) * StrFakCsm ;
	var last_costStr_3 		= ((Area * valueStr_3 * layerStr_3)/1000) * StrFakCsmAdd ;
	var last_costStr_5 		= ((Area * valueStr_5 * layerStr_5)/1000) * StrFakWr ;
	var last_costStr_7 		= ((Area * valueStr_7 * layerStr_7)/1000) * StrFakWrAdd ; 
	
	var resin2			= last_costStr_1 * containingStr_2;
	var resin4			= last_costStr_3 * containingStr_4;
	var resin6			= last_costStr_5 * containingStr_6;
	var resin8			= last_costStr_7 * containingStr_8;
	
	var resiTot			= resin2 + resin4 + resin6 + resin8;
	ChangePlusStr(resiTot);
	ChangePlusStrAdd(resiTot);
	
	$('#last_costStr_9').val(resiTot.toFixed(3));
	$('#last_fullStr_9').val(resiTot);

	$("#last_costStr_1").val(last_costStr_1.toFixed(3));
	$("#last_costStr_3").val(last_costStr_3.toFixed(3));
	$("#last_costStr_5").val(last_costStr_5.toFixed(3));
	$("#last_costStr_7").val(last_costStr_7.toFixed(3));
	
	$("#last_costStr_2").val(resin2.toFixed(3));
	$("#last_costStr_4").val(resin4.toFixed(3));
	$("#last_costStr_6").val(resin6.toFixed(3));
	$("#last_costStr_8").val(resin8.toFixed(3));
}

function ChangeAreaToEks(Area){
	var valueEks_1 		= getNum($('#valueEks_1').val());
	var valueEks_3 		= getNum($('#valueEks_3').val());
	var valueEks_5 		= getNum($('#valueEks_5').val());
	var valueEks_7 		= getNum($('#valueEks_7').val());
	
	var layerEks_1 		= getNum($('#layerEks_1').val());
	var layerEks_3 		= getNum($('#layerEks_3').val());
	var layerEks_5 		= getNum($('#layerEks_5').val());
	var layerEks_7 		= getNum($('#layerEks_').val());
	
	var containingEks_2	= getNum($('#containingEks_2').val());
	var containingEks_4	= getNum($('#containingEks_4').val());
	var containingEks_6	= getNum($('#containingEks_6').val());
	var containingEks_8	= getNum($('#containingEks_8').val());
	
	var EksFakVeil		= getNum($("#eks_faktor_veil").val());
	var EksFakVeilAdd	= getNum($("#eks_faktor_veil_add").val());
	var EksFakCsm		= getNum($("#eks_faktor_csm").val());
	var EksFakCsmAdd	= getNum($("#eks_faktor_csm_add").val());
	
	var last_cost_1 	= ((Area * valueEks_1 * layerEks_1)/1000) * EksFakVeil;
	var last_cost_3 	= ((Area * valueEks_3 * layerEks_3)/1000) * EksFakVeilAdd;
	var last_cost_5 	= ((Area * valueEks_5 * layerEks_5)/1000) * EksFakCsm;
	var last_cost_7 	= ((Area * valueEks_7 * layerEks_7)/1000) * EksFakCsmAdd; 
	
	if(isNaN(last_cost_7)){var last_cost_7 = 0;}
	
	var resin2			= last_cost_1 * containingEks_2;
	var resin4			= last_cost_3 * containingEks_4;
	var resin6			= last_cost_5 * containingEks_6;
	var resin8			= last_cost_7 * containingEks_8;
	
	var resiTot		= resin2 + resin4 + resin6 + resin8;
	ChangePlusEks(resiTot);
	ChangePlusEksAdd(resiTot);
	
	$('#last_costEks_9').val(resiTot.toFixed(3));
	$('#last_fullEks_9').val(resiTot);
	
	$("#last_costEks_1").val(last_cost_1.toFixed(3));
	$("#last_costEks_3").val(last_cost_3.toFixed(3));
	$("#last_costEks_5").val(last_cost_5.toFixed(3));
	$("#last_costEks_7").val(last_cost_7.toFixed(3));
	
	$("#last_costEks_2").val(resin2.toFixed(3));
	$("#last_costEks_4").val(resin4.toFixed(3));
	$("#last_costEks_6").val(resin6.toFixed(3));
	$("#last_costEks_8").val(resin8.toFixed(3));
}

function ChangePlusAdd(Area){
	var AddLinNum	= getNum($('#AddLinNum').val());
	var a;
	for(a=1; a <= AddLinNum; a++){
		var Con		= getNum($('#Addcontaining_'+a).val());
		var Per		= getNum($('#Addperse_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusStrAdd(Area){
	var AddStrNum	= getNum($('#AddStrNum').val());
	var a;
	for(a=1; a <= AddStrNum; a++){
		var Con		= getNum($('#Addcontaining2_'+a).val());
		var Per		= getNum($('#Addperse2_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost2_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusEksAdd(Area){
	var AddEksNum	= getNum($('#AddEksNum').val());
	var a;
	for(a=1; a <= AddEksNum; a++){
		var Con		= getNum($('#Addcontaining3_'+a).val());
		var Per		= getNum($('#Addperse3_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost3_'+a).val(Math.ceil(Hasil * 1000)/1000);
	}
}

function ChangePlusTcAdd(Area){
	var AddTcNum	= getNum($('#AddTcNum').val());
	var a;
	for(a=1; a <= AddTcNum; a++){
		var Con		= getNum($('#Addcontaining4_'+a).val());
		var Per		= getNum($('#Addperse4_'+a).val()) /100;
		var Hasil	= Area * Con * Per;
		$('#Addlast_cost4_'+a).val(Math.ceil(Hasil * 1000)/1000);
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
			url: base_url +'index.php/component_custom/getMaterialx',
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
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});


$(document).on('keyup', '.layer', function(){
	
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #value_"+nomor).val());
	var containing		= parseFloat($('#containing_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0003' || type == 'TYP-0004'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}

	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor).val(thick_hide.toFixed(4))
	
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thickness_"+nomor).val());
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;

	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
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
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingStr_'+NoResin).val();
		var materialRs	= $('#id_materialStr2_'+NoResin);
		
		var lastRes	= $('#last_costStr_'+NoResin);
		
		var resinOri	= $('#id_materialStr2_9').val();
		
		var resinX1	= $('#id_materialStr2_2').val();
		var resinX2	= $('#id_materialStr2_4').val();
		var resinX3	= $('#id_materialStr2_6').val();
		var resinX4	= $('#id_materialStr2_8').val();
		// alert(id_ori);
		// return false;
		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
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
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});

$(document).on('keyup', '.layerStr', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #valueStr_"+nomor).val());
	
	var containing		= parseFloat($('#containingStr_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0004' || type == 'TYP-0006'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}
	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val(thick_hide.toFixed(4))
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessStr_"+nomor).val());
	var oriMat			= $(this).parent().parent().find("td:nth-child(1) #id_oriStr_"+nomor).val();
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;

	// alert(thick_hide);
	
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
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

//EXTERNAL
$(document).on('change', '.id_materialEks', function(){
	var nomor 		= $(this).data('nomor');
	if(nomor != '9'){
		var id_material	= $(this).val();
		var diameter	= $("#diameter").val();
		var helpX		= $(this).parent().parent().find("td:nth-child(3) input");
		var layer		= $(this).parent().parent().find("td:nth-child(4) input");
		var thickness	= $(this).parent().parent().find("td:nth-child(5) input");
		var thick		= $(this).parent().parent().find("td:nth-child(6) input");
		var lastCost	= $(this).parent().parent().find("td:nth-child(7) input");
		var material2	= $(this).parent().parent().find("td:nth-child(2) #id_material2Eks_"+nomor);
		var id_ori		= $(this).parent().parent().find("td:nth-child(1) #id_oriEks_"+nomor).val();
		var thick_hide	= $(this).parent().parent().find("td:nth-child(2) #thicknessEks_"+nomor);
		var NoResin		= $(this).data('nomor') + 1;
		var resin		= $('#containingEks_'+NoResin).val();
		var materialRs	= $('#id_material2Eks_'+NoResin);
		
		var lastRes	= $('#last_costEks_'+NoResin);
		
		var resinOri	= $('#id_material2Eks_9').val();
		
		var resinX1	= $('#id_material2Eks_2').val();
		var resinX2	= $('#id_material2Eks_4').val();
		var resinX3	= $('#id_material2Eks_6').val();
		var resinX4	= $('#id_material2Eks_8').val();

		$.ajax({
			url: base_url +'index.php/component_custom/getMaterialx',
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
					if(resinX1 != 'MTL-1903000'){$('#id_material2Eks_2').val(data.resinUt);}
					if(resinX2 != 'MTL-1903000'){$('#id_material2Eks_4').val(data.resinUt);}
					if(resinX3 != 'MTL-1903000'){$('#id_material2Eks_6').val(data.resinUt);}
					if(resinX4 != 'MTL-1903000'){$('#id_material2Eks_8').val(data.resinUt);}
				}
				
				if(data.layer == 0){
					thick.val("0.0000");
					lastCost.val("0.000"); 
					lastRes.val("0.000"); 
				}
				ChangeLuasArea();
				ChangeHasil();
			}
		});
	}
});

$(document).on('keyup', '.layerEks', function(){
	var nomor 			= $(this).data('nomor');
	var nomorPlus		= parseFloat(nomor) + 1;
	
	var type 			= $(this).data('type');
	var berat			= parseFloat($(this).parent().parent().find("td:nth-child(3) #valueEks_"+nomor).val());
	var containing		= parseFloat($('#containingEks_'+nomorPlus).val());
	
	var thick_hide = 0;
	if(type == 'TYP-0003' || type == 'TYP-0004'){
		var thick_hide	= (berat/1000/2.56)+(berat/1000/1.2*containing);
	}
	
	if(isNaN(thick_hide)){ var thick_hide = 0;}
	$(this).parent().parent().find("td:nth-child(2) #thicknessEks_"+nomor).val(thick_hide.toFixed(4))
	
	var layer			= parseFloat($(this).val());
	// var thick_hide		= parseFloat($(this).parent().parent().find("td:nth-child(2) #thicknessEks_"+nomor).val());
	var tot_thickness	= $(this).parent().parent().find("td:nth-child(6) input");
	var lastWeight		= $(this).parent().parent().find("td:nth-child(7) input");
	var HslTotThick		= layer * thick_hide;
	
	if(isNaN(HslTotThick)){ var HslTotThick = 0;}
	tot_thickness.val(HslTotThick.toFixed(4));
	
	ChangeLuasArea();
	ChangeHasil();
});

$(document).on('keyup', '.perseEks', function(){
	var TotResin	= parseFloat($('#last_costEks_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(Hasil.toFixed(3));
});

$(document).on('keyup', '.perseEksAdd', function(){
	var TotResin	= parseFloat($('#last_costEks_9').val());
	
	var perse		= parseFloat($(this).val() / 100);
	var containing	= parseFloat($(this).parent().parent().find("td:nth-child(3) input").val());
	var lastWeight	= $(this).parent().parent().find("td:nth-child(5) input");
	
	var Hasil		= TotResin * perse * containing;
	
	if(isNaN(Hasil)){ var Hasil = 0;}
	lastWeight.val(Hasil.toFixed(3));
});

//TOPCOAT
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


//ADD TAMBAHAN
var nomor	= 1;

$('#add_liner').click(function(e){
	e.preventDefault();
	AppendBaris_Liner(nomor);
});

$(document).on('keyup', '.ChangeContaining', function(){
	var total_resin	= $('#last_cost_10').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerse', function(){
	var total_resin	= $('#last_cost_10').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});


$('#add_strukture').click(function(e){
	e.preventDefault();
	AppendBaris_Strukture(nomor);
});

$(document).on('keyup', '.ChangeContainingStr', function(){
	var total_resin	= $('#last_costStr_9').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);


});

$(document).on('keyup', '.ChangePerseStr', function(){
	var total_resin	= $('#last_costStr_9').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val() / 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);

});

$('#add_external').click(function(e){
	e.preventDefault();
	AppendBaris_External(nomor);
});

//EXTERNAL
$(document).on('keyup', '.ChangeContainingExt', function(){
	var total_resin	= $('#last_costEks_9').val();
	var perse		= $(this).parent().parent().find("td:nth-child(5) input").val() / 100;
	var containing	= $(this).val();
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});

$(document).on('keyup', '.ChangePerseExt', function(){
	var total_resin	= $('#last_costEks_9').val();
	var containing		= $(this).parent().parent().find("td:nth-child(4) input").val();
	var perse	= $(this).val()/ 100;
	
	var HasilAkhir	= getNum(total_resin) * getNum(perse) * getNum(containing);
	
	$(this).parent().parent().find("td:nth-child(6) input").val(HasilAkhir.toFixed(3));
	$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
});


$('#add_topcoat').click(function(e){
	e.preventDefault();
	AppendBaris_TopCoat(nomor);
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
		
function AppendBaris_Liner(intd){
	var nomor	= 1;
	var valuex	= $('#detail_body_liner').find('tr').length;
	if(valuex > 0){
		var akhir	= $('#detail_body_liner tr:last').attr('id');
		var det_id	= akhir.split('_');
		var nomor	= parseInt(det_id[1])+1;
	}

	var Rows	 = "<tr id='trliner_"+nomor+"'>";
		Rows 	+= 	"<td width = '15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Liner("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Liner["+nomor+"][last_full]' id='last_full_liner_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td align='left' width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_Liner["+nomor+"][id_category]' id='id_category_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_Liner["+nomor+"][id_material]' id='id_material_liner_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContaining' name='ListDetailAdd_Liner["+nomor+"][containing]' id='containing_liner_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td  width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerse' name='ListDetailAdd_Liner["+nomor+"][perse]' id='perse_liner_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_Liner["+nomor+"][last_cost]' id='last_cost_liner_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_liner').append(Rows);
	var id_category_liner_ 	= "#id_category_liner_"+nomor;
	var id_material_liner_ 	= "#id_material_liner_"+nomor;
	
	
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_Strukture("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_Strukture["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_Strukture["+nomor+"][id_category]' id='id_category_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_Strukture["+nomor+"][id_material]' id='id_material_strukture_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingStr' name='ListDetailAdd_Strukture["+nomor+"][containing]' id='containing_strukture_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseStr' name='ListDetailAdd_Strukture["+nomor+"][perse]' id='perse_strukture_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_Strukture["+nomor+"][last_cost]' id='last_cost_strukture_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_strukture').append(Rows);
	var id_category_strukture_ 	= "#id_category_strukture_"+nomor;
	var id_material_strukture_ 	= "#id_material_strukture_"+nomor;
	
	
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_External("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_External["+nomor+"][last_full]' id='last_full_strukture_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_External["+nomor+"][id_category]' id='id_category_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_External["+nomor+"][id_material]' id='id_material_external_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingExt' name='ListDetailAdd_External["+nomor+"][containing]' id='containing_external_"+nomor+"' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseExt' name='ListDetailAdd_External["+nomor+"][perse]' id='perse_external_"+nomor+"' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_External["+nomor+"][last_cost]' id='last_cost_external_"+nomor+"' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_external').append(Rows);
	var id_category_external_ 	= "#id_category_external_"+nomor;
	var id_material_external_ 	= "#id_material_external_"+nomor;
	
	
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
		Rows 	+= 	"<td width='15%'>";
		Rows 	+=		"<div style='text-align: left;'><button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' data-placement='bottom' onClick='delRow_TopCoat("+nomor+")' title='Delete Record'>Delete Record</button></div>";
		Rows	+=		"<input type='hidden' class='form-control Full' name='ListDetailAdd_TopCoat["+nomor+"][last_full]' id='last_full_topcoat_"+nomor+"' value='0' autocomplete='off'>";
		Rows 	+= 	"</td>";
		Rows	+= 	"<td width='20%'>";
		Rows	+=		"<select name='ListDetailAdd_TopCoat["+nomor+"][id_category]' id='id_category_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>Select An Category</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td align='left'>";
		Rows	+=		"<select name='ListDetailAdd_TopCoat["+nomor+"][id_material]' id='id_material_topcoat_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangeContainingTC' name='ListDetailAdd_TopCoat["+nomor+"][containing]' value='0' placeholder='Containing' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm numberOnly ChangePerseTC' name='ListDetailAdd_TopCoat["+nomor+"][perse]' value='0' placeholder='Percent' autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= 	"<td width='8%'>";
		Rows	+=		"<input type='text' style='text-align: right;' class='form-control input-sm Cost' name='ListDetailAdd_TopCoat["+nomor+"][last_cost]' value='0' readonly autocomplete='off'>";
		Rows	+= 	"</td>";
		Rows	+= "</tr>";

	$('#detail_body_topcoat').append(Rows);
	var id_category_topcoat_ 	= "#id_category_topcoat_"+nomor;
	var id_material_topcoat_ 	= "#id_material_topcoat_"+nomor;
	
	
	
	$.ajax({
		url: base_url +'index.php/component_custom/getCategory',
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
			url: base_url +'index.php/component_custom/getMaterial',
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
</script>