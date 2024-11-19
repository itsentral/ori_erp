<?php
$qBQdetailHeader 	= "SELECT a.*, a.series, b.no_ipp FROM bq_detail_header a LEFT JOIN bq_header b ON a.id_bq=b.id_bq WHERE a.id_bq = '".$id_bq."' ORDER BY a.id ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();

$ArrBQProduct = array();
foreach($ListCategory AS $val => $valx){
	$ArrBQProduct[$valx['id_category']] = strtoupper($valx['category']);
}

$qListResin 	= "SELECT id_material, nm_material FROM raw_materials WHERE id_category='TYP-0001' ORDER BY nm_material ASC ";
$dataResin		= $this->db->query($qListResin)->result_array();



$satuan			= $this->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY nama_satuan ASC ")->result_array();
$raw_material	= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND `delete`='N' ORDER BY nm_material ASC ")->result_array();
$jenis_barang	= $this->db->query("SELECT * FROM con_nonmat_new WHERE `deleted`='N' AND category_awal='7' ORDER BY material_name ASC ")->result_array();

$detail 		= $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='acc'")->result_array();
$detail2 		= $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='mat'")->result_array();
$detail3 		= $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='baut'")->result_array();
$detail4 		= $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='plate'")->result_array();
$detail4g 		= $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='gasket'")->result_array();
$detail5 		= $this->db->query("SELECT * FROM bq_acc_and_mat WHERE id_bq='".$id_bq."' AND category='lainnya'")->result_array();	

$jenis_baut	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='1' ORDER BY nama ASC ")->result_array();
$jenis_plate	= $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='2' ORDER BY nama ASC ")->result_array();
$jenis_gasket	= $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='3' ORDER BY nama ASC ")->result_array();
$jenis_part	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='4' ORDER BY nama ASC ")->result_array();
	

$ArrResin = array();
foreach($dataResin AS $val => $valx){
	$ArrResin[$valx['id_material']] = strtoupper($valx['nm_material']);
}
$ArrResin[0]	= 'Select Material';


?>
<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body">
		<div class='note'>
			<p>
				<strong>Info!</strong><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_resin'><?=COUNT($countResin);?></span> RESIN :    <span style='color:red;'><span id='nama_resin'><?=strtoupper($listResin);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_veil'><?=COUNT($countVeil);?></span> VEIL :    <span style='color:red;'><span id='nama_veil'><?=strtoupper($listVeil);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_csm'><?=COUNT($countCsm);?></span> CSM (MAT) :    <span style='color:red;'><span id='nama_csm'><?=strtoupper($listCsm);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_wr'><?=COUNT($countWR);?></span> WOVEN ROOVING :    <span style='color:red;'><span id='nama_wr'><?=strtoupper($listWR);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_rooving'><?=COUNT($countRooving);?></span> ROOVING :    <span style='color:red;'><span id='nama_rooving'><?=strtoupper($listRooving);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_catalys'><?=COUNT($countCatalys);?></span> CATALYS :    <span style='color:red;'><span id='nama_catalys'><?=strtoupper($listCatalys);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_pigment'><?=COUNT($countPigment);?></span> PIGMENT (COLOR) :    <span style='color:red;'><span id='nama_pigment'><?=strtoupper($listPigment);?></span></span></b></span><br>
				<!--<span style='color:blue;'><b>(NEW) CHECKLIST PRODUCT, UNTUK MENGGANTI RESIN PRODUCT YANG DIPILIH <span style='color:red;'></span></b></span><br>-->
			</p>
		</div>
		<div class='form-group row'>
			<div class='col-sm-3'>
			<label class='label-control'><b>CATEGORY</b></label>
				<?php
					echo form_dropdown('category_id', $ArrBQProduct, 'TYP-0001', array('id'=>'category_id','class'=>'chosen-select form-control inline-block'));
				?>
			</div>
		</div>
		<div class='form-group row'>
			<div class='col-sm-3'>
			<label class='label-control'><b><span class='label_category'>RESIN</span> LINER</b></label>
				<?php
					echo form_dropdown('resin_liner', $ArrResin, '0', array('id'=>'liner','class'=>'chosen-select form-control inline-block listMaterial'));
					echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary updateResin','data-lyr'=>'liner','style'=>'min-width:100px; margin-top: 5px;','value'=>"Update Liner",'content'=>"Update Liner",'id'=>'btn_liner'));
				?>
			</div>
			<div class='col-sm-3'>
			<label class='label-control'><b><span class='label_category'>RESIN</span> STRUCTURE</b></label>
				<?php
					echo form_dropdown('resin_str', $ArrResin, '0', array('id'=>'str','class'=>'chosen-select form-control inline-block listMaterial'));
					echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary updateResin','data-lyr'=>'str','style'=>'min-width:100px; margin-top: 5px;','value'=>"Update Structure",'content'=>"Update Structure",'id'=>'btn_str'));
				?>
			</div>
			<div class='col-sm-3'>
			<label class='label-control'><b><span class='label_category'>RESIN</span> EXTERNAL</b></label>
				<?php
					echo form_dropdown('resin_eks', $ArrResin, '0', array('id'=>'eks','class'=>'chosen-select form-control inline-block listMaterial'));
					echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary updateResin','data-lyr'=>'eks','style'=>'min-width:100px; margin-top: 5px;','value'=>"Update External",'content'=>"Update External",'id'=>'btn_eks'));
				?>
			</div>
			<div class='col-sm-3'>
			<label class='label-control'><b><span class='label_category'>RESIN</span> TOP COAT</b></label>
				<?php
					echo form_dropdown('resin_tc', $ArrResin, '0', array('id'=>'tc','class'=>'chosen-select form-control inline-block listMaterial'));
					echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary updateResin','data-lyr'=>'tc','style'=>'min-width:100px; margin-top: 5px;','value'=>"Update Top Coat",'content'=>"Update Top Coat",'id'=>'btn_tc'));
				?>
			</div>
		</div>

		<input type='hidden' name='id_bq' value='<?= $id_bq;?>'> 
		<input type='hidden' name='pembeda' id='pembeda' value='<?= $this->uri->segment(4);?>'>
		<input type='hidden' name='no_ipp' value='<?= $qBQdetailRest[0]['no_ipp'];?>'> 
		<br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='3%'><input type='checkbox' name='chk_all' id='chk_all'></th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>Iso Matric</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>No Unit</th>
					<th class="text-center" style='vertical-align:middle;' width='9%'>No Component</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Series</th>
					<th class="text-center" style='vertical-align:middle;' width='16%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='11%'>Spec</th>
					<th class="text-center" style='vertical-align:middle;' width='24%'>Estimasi</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>#</th>
					
				</tr>
			</thead>
			<tbody id='detail_body'>
				<?php
					$no=0;
					foreach($qBQdetailRest AS $val => $valx){
						$no++;
						$spaces = "";
						$id_delivery = strtoupper($valx['id_delivery']);
						$bgwarna	= "bg-blue";
						$nm_cty	= ucwords(strtolower($valx['id_category']));
						if($valx['sts_delivery'] == 'CHILD'){
							$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							$id_delivery = strtoupper($valx['sub_delivery']);
							$bgwarna	= "bg-green";
						}
						
						$plusSQL = " AND a.diameter = '".$valx['diameter_1']."'";
						if($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint' OR $valx['id_category'] == 'frp reducer tee'){
							$plusSQL = " AND a.diameter = '".$valx['diameter_1']."'  AND a.diameter2='".$valx['diameter_2']."'";
						}
						if($valx['id_category'] == 'figure 8'){
							$plusSQL = " AND a.diameter2='".$valx['diameter_2']."'";
						}
						
						$plusSQL2 = "";
						if($valx['id_category'] == 'elbow mould' OR $valx['id_category'] == 'elbow mitter'){
							$plusSQL2 = " AND a.diameter = '".$valx['diameter_1']."'  AND a.angle='".$valx['sudut']."' AND a.type_elbow='".$valx['type']."' ";
						}

						$series = $valx['series'];
						// echo $series."<br>";
						$sqlProduct	= "SELECT a.id_product, a.series, a.cust FROM component_header a INNER JOIN bq_detail_header b ON a.series = b.series  WHERE b.id_bq = '".$id_bq."' ".$plusSQL." ".$plusSQL2." AND a.series = '".$valx['series']."' AND a.parent_product='".$valx['id_category']."' GROUP BY a.id_product";
						$restProduct = $this->db->query($sqlProduct)->result_array();
						// $restNum = $this->db->query($sqlProduct)->num_rows();
						
						// echo $sqlProduct."<br>";
						echo "<tr id='tr_".$no."'>";
							echo "<td align='center'>".$no."</td>";
							echo "<td align='center'><input type='checkbox' name='check[".$no."]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."' ></td>";
							echo "<td align='center'>".$spaces."".$valx['id_delivery']."</td>";
							echo "<td align='center'>".$spaces."".$valx['sub_delivery']."</td>";
							echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
							echo "<td align='center'>".$spaces."".$valx['series']."</td>";
							echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
							echo "<td align='left' style='padding-left:20px;'>".spec_bq($valx['id'])."</td>";
								
							echo "<td style='vertical-align:middle;' align='center'>";
								echo "<input type='hidden' name='detailBQ[".$no."][id]' id='id' value='".$valx['id']."'>";
								echo "<input type='hidden' name='detailBQ[".$no."][panjang]' id='panjang' value='".floatval($valx['length'])."'>";
								echo "<select name='detailBQ[".$no."][id_productx]' id='id_product_".$no."' class='chosen-select form-control inline-block'>";
									echo "<option value=''>Select ".$nm_cty."</option>";
									// if($restNum == 0){echo "<option value='0'>List Empty</option>";}
									foreach($restProduct AS $valP => $valPX){
										$idProduct = $valPX['cust']; 
										$sqtToCust = $this->db->query("SELECT nm_customer FROM customer WHERE id_customer='".$idProduct."'")->result_array();
										$Customer	= (!empty($idProduct))?' ('.$sqtToCust[0]['nm_customer'].')':'';
						
										$selectedX	= ($valx['id_product'] == $valPX['id_product'])?'selected':'';
										echo "<option value='".$valPX['id_product']."' ".$selectedX.">".$valPX['id_product'].$Customer."</option>";
									}
								echo "</select>";
							echo "</td>";
							
							echo "<td align='left'>";
								if(!empty($valx['id_product'])){
									echo "<button type='button' class='btn btn-sm btn-primary' id='detailX' title='Lihat Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
								}
								if($valx['id_category'] == 'pipe' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='editX' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'end cap' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_end_cap' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'blind flange' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_blindflange' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'elbow mould' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_elbowmould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'elbow mitter' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_elbowmitter' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'eccentric reducer' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_eccentric_reducer' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'concentric reducer' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_concentric_reducer' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'equal tee mould' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_equal_tee_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'reducer tee mould' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_reducer_tee_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'equal tee slongsong' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_equal_tee_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'reducer tee slongsong' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_reducer_tee_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'flange mould' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_flange_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'flange slongsong' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_flange_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'colar' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_colar' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'colar slongsong' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_colar_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'field joint' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_field_joint' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'shop joint' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger' id='edit_shop_joint' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if($valx['id_category'] == 'branch joint' AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger' id='edit_branch_joint' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								if(($valx['id_category'] == 'loose flange' 
									OR $valx['id_category'] == 'puddle flange' 
									OR $valx['id_category'] == 'plate'
									OR $valx['id_category'] == 'rib'
									OR $valx['id_category'] == 'support' 
									OR $valx['id_category'] == 'blind spacer'
									OR $valx['id_category'] == 'spacer'
									OR $valx['id_category'] == 'saddle'
									OR $valx['id_category'] == 'joint rib'
									OR $valx['id_category'] == 'joint plate'
									OR $valx['id_category'] == 'joint puddle flange'
									OR $valx['id_category'] == 'figure 8'
									OR $valx['id_category'] == 'spacer ring'
									OR $valx['id_category'] == 'blind flange with hole'
									OR $valx['id_category'] == 'spectacle blind'
									OR $valx['id_category'] == 'blank and spacer'
									OR $valx['id_category'] == 'bellmouth'
									OR $valx['id_category'] == 'lateral tee'
									) AND !empty($valx['id_product'])){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success' id='edit_custom' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
								}
								echo "&nbsp;<button type='button' class='btn btn-sm btn-info updateComp' title='Update Component' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-panjang=".floatval($valx['length'])." data-pembeda='".$this->uri->segment(4)."'><i class='fa fa-check'></i></button>";
								
							echo "</td>"; 
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save",'id'=>'estNowNew')).' ';
		?>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>B. MUR BAUT</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='20%'>Material</th>
					<th class="text-center" width='15%'>Qty</th>
					<th class="text-center" width='15%'>Unit</th>
					<th class="text-center" width='15%'>Keterangan</th>
					<th class="text-center" width='5%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail3)){
						foreach($detail3 AS $val => $valx){ $id++;
							$get_detail = $this->db->select('material')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							
							echo "<tr class='header3_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_baut[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm get_detail_baut'>";
									foreach($jenis_baut AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
										$radx = (!empty($valx2['radius']) AND $valx2['radius'] > 0)?'x '.floatval($valx2['radius']).' R':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama']).' M '.floatval($valx2['diameter']).' x '.floatval($valx2['panjang']).' L '.$radx."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_baut[".$id."][material]' id='bt_material_".$id."' class='form-control input-md text-left' placeholder='Material' value='".strtoupper($get_detail[0]->material)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_baut[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail_baut[".$id."][satuan]' class='chosen-select form-control input-sm'>";
									foreach($satuan AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_baut[".$id."][note]' class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
							echo "</tr>";
						}
					}
				?>
                <tr id='add3_<?=$id;?>'>
                    <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart3' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                    <td align='center'></td>
                    <td align='center'></td> 
					<td align='center'></td> 
					<td align='center'></td> 
                    <td align='center'></td>
                </tr>
            </tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success save_mat_acc','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save")).' ';
		?>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>C. PLATE</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Ukuran Standart</th>
					<th class="text-center" width='10%'>Standart</th>
					<th class="text-center" width='9%'>Lebar (mm)</th>
					<th class="text-center" width='9%'>Panjang (mm)</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Berat (kg)</th>
					<th class="text-center" width='9%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='5%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail4)){
						foreach($detail4 AS $val => $valx){ $id++;
							$get_detail = $this->db->select('ukuran_standart, standart, thickness, density')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							echo "<tr class='header4_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_plate[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm get_detail_plate'>";
									foreach($jenis_plate AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama'].', '.$valx2['material']).' x '.floatval($valx2['thickness'])." T</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][ukuran_standart]' id='pl_ukuran_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->ukuran_standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][standart]' id='pl_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][lebar]' id='pl_lebar_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0' value='".number_format($valx['lebar'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][panjang]' id='pl_panjang_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0' value='".number_format($valx['panjang'],2)."'>";
									echo "<input type='hidden' name='detail_plate[".$id."][thickness]' id='pl_thickness_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($get_detail[0]->thickness,2)."'>";
									echo "<input type='hidden' name='detail_plate[".$id."][density]' id='pl_density_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($get_detail[0]->density,2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][qty]' id='pl_qty_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0' value='".number_format($valx['qty'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][berat]' id='pl_berat_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['berat'],3)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][sheet]' id='pl_sheet_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['sheet'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][note]' class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
							echo "</tr>";
						}
					}
				?>
                <tr id='add4_<?=$id;?>'>
                    <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart4' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                    <td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
                    <td align='center'></td> 
					<td align='center'></td> 
                    <td align='center'></td>
                </tr>
            </tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success save_mat_acc','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save")).' ';
		?>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>D. GASKET</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='12%'>Standart</th>
					<th class="text-center" width='10%'>Dimensi</th>
					<th class="text-center" width='8%'>Lebar (mm)</th>
					<th class="text-center" width='8%'>Panjang (mm)</th>
					<th class="text-center" width='8%'>Qty</th>
					<th class="text-center" width='8%'>Sheet</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='12%'>Keterangan</th>
					<th class="text-center" width='5%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail4g)){
						foreach($detail4g AS $val => $valx){ $id++;
							$get_detail = $this->db->select('ukuran_standart, standart, thickness, density, dimensi')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$satuan2		= $this->db->get_where("raw_pieces", array('delete'=>'N','id_satuan'=>$valx['satuan']))->result_array();
							echo "<tr class='header4g_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_gasket[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm get_detail_gasket'>";
									foreach($jenis_gasket AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama'].' '.$valx2['dimensi'].', '.$valx2['material']).' x '.floatval($valx2['thickness'])." T</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][ukuran_standart]' id='gs_ukuran_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][dimensi]' id='gs_dimensi_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->dimensi)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][lebar]' id='gs_lebar_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['lebar'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][panjang]' id='gs_panjang_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['panjang'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][qty]' id='gs_qty_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][sheet]' id='gs_sheet_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['sheet'],2)."'>";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail_gasket[".$id."][satuan]' id='gs_satuan_".$id."' class='chosen-select form-control input-sm'>";
									foreach($satuan2 AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][note]' class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
							echo "</tr>";
						}
					}
				?>
                <tr id='add4g_<?=$id;?>'>
                    <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart4g' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                    <td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
                    <td align='center'></td> 
					<td align='center'></td> 
                    <td align='center'></td>
                </tr>
            </tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success save_mat_acc','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save")).' ';
		?>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>E. LAINNYA</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Ukuran Standart</th>
					<th class="text-center" width='15%'>Standart</th>
					<th class="text-center" width='15%'>Qty</th>
					<th class="text-center" width='15%'>Unit</th>
					<th class="text-center" width='15%'>Keterangan</th>
					<th class="text-center" width='5%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail5)){
						foreach($detail5 AS $val => $valx){ $id++;
							$get_detail = $this->db->select('spesifikasi, standart, ukuran_standart')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$satuan2		= $this->db->get_where("raw_pieces", array('delete'=>'N','id_satuan'=>$valx['satuan']))->result_array();
							echo "<tr class='header5_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_lainnya[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm get_detail_lainnya'>";
									foreach($jenis_part AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama'].', '.$valx2['material'].' - '.$valx2['dimensi'].' - '.$valx2['spesifikasi'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][ukuran_standart]' id='ln_ukuran_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->ukuran_standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][standart]' id='ln_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail_lainnya[".$id."][satuan]' id='ln_satuan_".$id."' class='chosen-select form-control input-sm'>";
									foreach($satuan2 AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][note]' class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
							echo "</tr>";
						}
					}
				?>
                <tr id='add5_<?=$id;?>'>
                    <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart5' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                    <td align='center'></td>
					<td align='center'></td>
					<td align='center'></td>
                    <td align='center'></td> 
					<td align='center'></td> 
                    <td align='center'></td>
                </tr>
            </tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success save_mat_acc','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save")).' ';
		?>
	</div>
</div>

<div class="box box-info">
	<div class="box-header">
		<label>F. MATERIAL</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Qty</th>
					<th class="text-center" width='15%'>Unit</th>
					<th class="text-center" width='30%'>Keterangan</th>
					<th class="text-center" width='10%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail2)){
						foreach($detail2 AS $val => $valx){ $id++;
		
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_material[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm'>";
									foreach($raw_material AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id_material'])?'selected':'';
									  echo "<option value='".$valx2['id_material']."' ".$dex.">".strtoupper($valx2['nm_material'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_material[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<select name='detail_material[".$id."][satuan]' class='chosen-select form-control input-sm'>";
										echo "<option value='1'>KG</option>";
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_material[".$id."][note]' class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								echo "</td>";
							echo "</tr>";
						}
					}
				?>
                <tr id='add2_<?=$id;?>'>
                    <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart2' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                    <td align='center'></td>
                    <td align='center'></td> 
					<td align='center'></td> 
                    <td align='center'></td>
                </tr>
            </tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info save_mat_acc','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save")).' ';
		?>
	</div>
</div>
<style type="text/css">
	.modal-dialog{
		overflow: auto !important;
	}
	
	label{
		    font-size: small !important;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen-select').chosen({width: '100%'});
		$('.maskM').maskMoney();
		
		$("#chk_all").click(function(){
			$('.chk_personal').not(this).prop('checked', this.checked);
		});
		
		//SAVE NEW ADA DEFAULTNYA
		$(document).on('click', '#estNowNew', function(){
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			var data_url = $('#pembeda').val();
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
					$.ajax({
						url			: base_url + active_controller+'/updateBQNew',
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
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
								$("#view").load(base_url + active_controller+'/modalEstBQ/'+data.id_bqx+'/'+data.pembeda);
								$("#ModalView").modal();
								
								
							}
							else if(data.status == 0){
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
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
		//SAVE NEW ADA DEFAULTNYA
		$(document).on('click', '.save_mat_acc', function(){
			var intL = 0;
			var intError = 0;
			var pesan = '';
			
			var data_url = $('#pembeda').val();
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
					$.ajax({
						url			: base_url + active_controller+'/save_mat_acc',
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
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
								$("#view").load(base_url + active_controller+'/modalEstBQ/'+data.id_bqx+'/'+data.pembeda);
								$("#ModalView").modal();
								
								
							}
							else if(data.status == 0){
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
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		//SAVE NEW ADA DEFAULTNYA
		$(document).on('click', '.updateResin', function(){
			var layer = $(this).data('lyr');
			var material = $("#"+layer).val();
			// var product_id = $("#product_id").val();

			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist milimal satu terlebih dahulu',
					type	: "warning"
				});
				return false;
			}

			if(material == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Resin not selected, please select first ...',
				  type	: "warning"
				});
				return false;
			}

			// alert(layer+'/'+material);
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
					$.ajax({
						url			: base_url+active_controller+'/update_resin/'+material+'/'+layer,
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
								// window.location.href = base_url + active_controller+'/'+data_url;
								
								$('#jumlah_resin').html(data.jumlah_resin);
								$('#nama_resin').html(data.nama_resin);
								
								$('#jumlah_veil').html(data.jumlah_veil);
								$('#nama_veil').html(data.nama_veil);
								
								$('#jumlah_csm').html(data.jumlah_csm);
								$('#nama_csm').html(data.nama_csm); 
								
								$('#jumlah_wr').html(data.jumlah_wr);
								$('#nama_wr').html(data.nama_wr);
								
								$('#jumlah_rooving').html(data.jumlah_rooving);
								$('#nama_rooving').html(data.nama_rooving);
								
								$('#jumlah_catalys').html(data.jumlah_catalys);
								$('#nama_catalys').html(data.nama_catalys);
								
								$('#jumlah_pigment').html(data.jumlah_pigment);
								$('#nama_pigment').html(data.nama_pigment);

								// $("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
								// $("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bqx+'/'+data.pembeda);
								// $("#ModalView").modal();
								
								
							}
							else if(data.status == 0){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
						},
						error: function() {
							swal({
							  title				: "Error Message !", 
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 7000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
		$(document).on('click', '.addPart', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add_"+id_bef).before(data.header);
					$("#add_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
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

		$(document).on('click', '.addPart2', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add2/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add2_"+id_bef).before(data.header);
					$("#add2_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
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

		$(document).on('click', '.delPart', function(){
			var get_id 		= $(this).parent().parent().attr('class');
			$("."+get_id).remove();
		});
		
		//NEW
		$(document).on('click', '.addPart3', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add3/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add3_"+id_bef).before(data.header);
					$("#add3_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
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

		$(document).on('click', '.addPart4', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add4/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add4_"+id_bef).before(data.header);
					$("#add4_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
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
		
		$(document).on('click', '.addPart4g', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add4g/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add4g_"+id_bef).before(data.header);
					$("#add4g_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
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

		$(document).on('click', '.addPart5', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url + active_controller+'/get_add5/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add5_"+id_bef).before(data.header);
					$("#add5_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
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
	
		$(document).on('change', '.get_detail_lainnya', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url + active_controller+'/get_detail_lainnya/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#ln_ukuran_standart_"+id_bef).val(data.ukuran_standart);
					$("#ln_standart_"+id_bef).val(data.standart);
					// $("#ln_satuan_"+id_bef+"_chosen option[value="+data.satuan+"]").attr('selected','selected');
					$("#ln_satuan_"+id_bef).html("<option value='"+data.satuan+"'>"+data.satuan_view+"</option>").trigger("chosen:updated");
					swal.close();
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

		$(document).on('change', '.get_detail_plate', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url + active_controller+'/get_detail_plate/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#pl_ukuran_standart_"+id_bef).val(data.ukuran_standart);
					$("#pl_standart_"+id_bef).val(data.standart);
					$("#pl_thickness_"+id_bef).val(data.thickness);
					$("#pl_density_"+id_bef).val(data.density);

					if(data.satuan == '1'){
						$("#pl_qty_"+id_bef).attr('readonly', true)
						$("#pl_berat_"+id_bef).attr('readonly', false)
					}
					else{
						$("#pl_berat_"+id_bef).attr('readonly', true)
						$("#pl_qty_"+id_bef).attr('readonly', false)
					}

					get_berat_plate(id_bef);
					swal.close();
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
		
		$(document).on('change', '.get_detail_gasket', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url + active_controller+'/get_detail_gasket/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#gs_ukuran_standart_"+id_bef).val(data.standart);
					$("#gs_dimensi_"+id_bef).val(data.dimensi);
					$("#gs_satuan_"+id_bef).html("<option value='"+data.satuan+"'>"+data.satuan_view+"</option>").trigger("chosen:updated");
					swal.close();
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
		
		$(document).on('change', '.get_detail_baut', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url + active_controller+'/get_detail_baut/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#bt_material_"+id_bef).val(data.material);
					swal.close();
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

		$(document).on('keyup', '.get_berat', function(){
			var get_id 		= $(this).parent().parent().attr('class');
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			get_berat_plate(id_bef);
		});
	
	});
	
	$(document).on('change', '#category_id', function(){
		loading_spinner();
		var category_id = $(this).val()
		var item_cost = $('.listMaterial')
		var label_category = $('.label_category')

		$.ajax({
			url: base_url + active_controller+'/get_material/'+category_id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(item_cost).html(data.option).trigger("chosen:updated");
				$(label_category).html(data.nama);
				swal.close();
			},
			error: function() {
				swal({
					title	: "Error Message !",
					text	: 'Connection Time Out. Please try again..',
					type	: "warning",
					timer	: 3000
				});
			}
		});
	});
	
	function get_berat_plate(id){
		var panjang 	= getNum($('#pl_panjang_'+id).val());
		var thickness 	= getNum($('#pl_thickness_'+id).val());
		var lebar 		= getNum($('#pl_lebar_'+id).val());
		var density 	= getNum($('#pl_density_'+id).val());
		var qty 		= getNum($('#pl_qty_'+id).val());

		var berat = panjang * thickness * lebar * density * qty;

		$('#pl_berat_'+id).val(number_format(berat,3));
	}

	function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

	function number_format (number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	}
</script>