<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body"> 
		<div class='note'>
			<p>
				<strong>Info!</strong><br> 
				<!-- <button type='button' class='btn btn-sm btn-info'><i class='fa fa-check'></i></button>&nbsp;<button type='button' class='btn btn-sm btn-info'>Save</button> Digunakan untuk menarik estimasi dari <b>Master Product</b><br>
				<button type='button' class='btn btn-sm btn-info' style='background-color: #de14a0; border-color: #de14a0; color:white; margin-top:5px;'><i class='fa fa-check'></i></button>&nbsp;<button type='button' class='btn btn-sm btn-info' style='background-color: #de14a0; border-color: #de14a0; color:white; margin-top:5px;'>Save</button> Digunakan untuk menarik estimasi dari <b>Estimasi Terakhir</b><br> -->
				<span style='color:green;'><b>(NEW) <span id='jumlah_resin'><?=COUNT($countResin);?></span> RESIN :    <span style='color:red;'><span id='nama_resin'><?=strtoupper($listResin);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_veil'><?=COUNT($countVeil);?></span> VEIL :    <span style='color:red;'><span id='nama_veil'><?=strtoupper($listVeil);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_csm'><?=COUNT($countCsm);?></span> CSM (MAT) :    <span style='color:red;'><span id='nama_csm'><?=strtoupper($listCsm);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_wr'><?=COUNT($countWR);?></span> WOVEN ROOVING :    <span style='color:red;'><span id='nama_wr'><?=strtoupper($listWR);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_rooving'><?=COUNT($countRooving);?></span> ROOVING :    <span style='color:red;'><span id='nama_rooving'><?=strtoupper($listRooving);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_catalys'><?=COUNT($countCatalys);?></span> CATALYS :    <span style='color:red;'><span id='nama_catalys'><?=strtoupper($listCatalys);?></span></span></b></span><br>
				<span style='color:green;'><b>(NEW) <span id='jumlah_pigment'><?=COUNT($countPigment);?></span> PIGMENT (COLOR) :    <span style='color:red;'><span id='nama_pigment'><?=strtoupper($listPigment);?></span></span></b></span><br>
				<br>
				<br>
				<span style='color:red;'><b><u><?=COUNT($resultHistory);?>x Perubahan Material</u></b></span> <span class='text-bold' style='color:purple; cursor:pointer;' id='LookChange'>Lihat Perubahan</span>
			</p>
			<input type="hidden" name='TYP0001' value='<?=$dtImplodeResinID;?>'>
			<input type="hidden" name='TYP0003' value='<?=$dtImplodeVeilID;?>'>
			<input type="hidden" name='TYP0004' value='<?=$dtImplodeCsmID;?>'>
			<input type="hidden" name='TYP0006' value='<?=$dtImplodeWRID;?>'>
			<input type="hidden" name='TYP0005' value='<?=$dtImplodeRoovingID;?>'>
			<input type="hidden" name='TYP0002' value='<?=$dtImplodeCatalysID;?>'>
			<input type="hidden" name='TYP0007' value='<?=$dtImplodePigmentID;?>'>
		</div>
		<div id='ChangeMaterial'>
			<table class='table' width='100%' style='font-size: 12px !important;'>
				<tr>
					<th>#</th>
					<th>Layer</th>
					<th>Type</th>
					<th>Material Before</th>
					<th>Material After</th>
					<th>Product</th>
					<th>By</th>
					<th>Date</th>
				</tr>
				<?php
				foreach ($resultHistory as $key => $value) { $key++;
					$NM_MATERIAL = explode(",",$value['id_material_before']);
					$NM_MATERIAL2 = explode(",",$value['id_material_after']);
					$PRODUCT = explode("','",$value['id_milik']);
					echo "<tr>";
						echo "<td>".$key."</td>";
						echo "<td>".strtoupper($value['layer'])."</td>";
						echo "<td>".$value['typeMaterial']."</td>";
						echo "<td>";
							foreach ($NM_MATERIAL as $key2 => $value2) {
								$nm_mat = (!empty($GET_MATERIAL[$value2]['nm_material']))?$GET_MATERIAL[$value2]['nm_material']:'';
								echo $nm_mat."<br>";
							}
						echo "</td>";
						echo "<td>";
							foreach ($NM_MATERIAL2 as $key2 => $value2) {
								$nm_mat = (!empty($GET_MATERIAL[$value2]['nm_material']))?$GET_MATERIAL[$value2]['nm_material']:'';
								echo $nm_mat."<br>";
							}
						echo "</td>";
						echo "<td>";
							foreach ($PRODUCT as $key2 => $value2) {
								$nm_mat = (!empty($GET_PRODUCT[$value2]['product']))?$GET_PRODUCT[$value2]['product']:'';
								echo strtoupper($nm_mat).', '.spec_bq2($value2)."<br>";
							}
						echo "</td>";
						echo "<td>".$value['change_by']."</td>";
						echo "<td>".date('d-M-Y H:i',strtotime($value['change_date']))."</td>";
					echo "</tr>";
				}
				?>
			</table>
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
		<!--
		<span style='color:green;'><b>* Tombol Edit berwarna <span style='color:red;'>Merah</span> dalam process Development, <span style='color:red;'><u>MOHON JANGAN DIGUNAKAN.</u></span> Kolom #</b></span>
		<br>
		<br><br>
		-->
		
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th style='background:none;' width='4%' class='no-sort'><font size='2'><B><center>Ganti Material<br><input type='checkbox' name='chk_all2' id='chk_all2'></center></B></font></th>
					<th style='background:none;' width='4%' class='no-sort'><font size='2'><B><center>Estimasi<br><input type='checkbox' name='chk_all' id='chk_all'></center></B></font></th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>Iso Matric</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>No Unit Delivery</th>
					<th class="text-center" style='vertical-align:middle;' width='9%'>No Component</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>Series</th>
					<th class="text-center" style='vertical-align:middle;' width='13%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='4%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='11%'>Spec</th>
					<!--<th class="text-center" style='vertical-align:middle;' width='2%'>Upload</th>-->
					<th class="text-center" style='vertical-align:middle;' width='26%'>Estimasi</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Option</th>
					
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
						$sqlProduct	= "SELECT a.id_product, a.series, a.cust FROM component_header a INNER JOIN so_detail_header b ON a.series = b.series  WHERE b.id_bq = '".$id_bq."' ".$plusSQL." ".$plusSQL2." AND a.series = '".$valx['series']."' AND a.parent_product='".$valx['id_category']."' GROUP BY a.id_product";
						$restProduct = $this->db->query($sqlProduct)->result_array();
								
						// echo $sqlProduct."<br>";
						echo "<tr id='tr_".$no."'>";
							echo "<td align='center'  style='vertical-align:middle;'>";
							if($valx['approve'] == 'N'){
								echo "<input type='checkbox' name='check2[".$no."]' class='chk_personal2' data-nomor='".$no."' value='".$valx['id']."' >";
							}
							echo "</td>";
							echo "<td align='right' style='vertical-align:middle;'><center>";
							if($valx['approve'] == 'N'){
								echo "<input type='checkbox' name='check[$no]' class='chk_personal' data-nomor='".$no."' value='".$valx['id']."-".$valx['id_milik_bq']."'>";
							}
							echo "</center></td>";
							echo "<td align='center'>".$spaces."".$valx['id_delivery']."</td>";
							echo "<td align='center'>".$spaces."".$valx['sub_delivery']."</td>";
							echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
							echo "<td align='center'>".$spaces."".$valx['series']."</td>";
							echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
							echo "<td align='left' style='padding-left:20px;'>".spec_fd($valx['id'],'so_detail_header')."</td>";
							// echo "<td align='left' style='padding-left:20px;'></td>";
							echo "<td style='vertical-align:middle;' align='center'>"; 
								
								echo "<input type='hidden' name='detailBQ[".$no."][id]' value='".$valx['id']."'>";
								echo "<input type='hidden' name='detailBQ[".$no."][panjang]' value='".floatval($valx['length'])."'>";
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
									echo "<button type='button' class='btn btn-sm btn-primary detail_comp' title='Lihat Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
								}
								
								if($valx['approve'] == 'N'){
									if($valx['id_category'] == 'pipe' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_pipe' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'end cap' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_end_cap' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'blind flange' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_blindflange' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									} 
									if($valx['id_category'] == 'elbow mould' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_elbowmould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'elbow mitter' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_elbowmitter' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'eccentric reducer' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_eccentric_reducer' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'concentric reducer' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_concentric_reducer' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'equal tee mould' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_equal_tee_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'reducer tee mould' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_reducer_tee_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'equal tee slongsong' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_equal_tee_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'reducer tee slongsong' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_reducer_tee_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'flange mould' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_flange_mould' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'flange slongsong' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_flange_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'colar' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_colar' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									if($valx['id_category'] == 'colar slongsong' AND !empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-success edit_colar_slongsong' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									}
									// if($valx['id_category'] == 'field joint' AND !empty($valx['id_product'])){
										// echo "&nbsp;<button type='button' class='btn btn-sm btn-danger' id='edit_field_joint' title='Edit Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-edit'></i></button>";
									// } 
									echo "&nbsp;<button type='button' class='btn btn-sm btn-info update_get_master' title='Update dari master' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-panjang=".floatval($valx['length'])."><i class='fa fa-check'></i></button>";
									if($valx['id_milik_bq'] != NULL){
										// echo "&nbsp;<button type='button' class='btn btn-sm btn-danger get_est_bq' title='Estimasi dari BQ' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-id_milik_bq='".$valx['id_milik']."'><i class='fa fa-close'></i></button>";
										echo "&nbsp;<button type='button' class='btn btn-sm btn-danger update_get_est_bq' style='background-color: #de14a0; border-color: #de14a0; color:white;' title='Update dari estimasi sebelumnya' data-nomor='".$no."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-panjang=".floatval($valx['length'])." data-id_milik_bq='".$valx['id_milik_bq']."'><i class='fa fa-check'></i></button>";
									
									} 
								}
							echo "</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
		<br>
		<?php
			// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'estNow')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','style'=>'min-width:100px; margin-right:10px; float:right; background-color: #de14a0; border-color: #de14a0; color:white;','value'=>"save",'content'=>"Save (Dari Estimasi Sebelumnya)",'id'=>'estNowNewBQ'));
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Save (Dari Master)",'id'=>'estNowNew'));
		?>
	</div>
</div>
<!--
<div class="box box-success">
	<div class="box-header">
		<label>B. BILL OF QUANTITY NON FRP</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Qty</th>
					<th class="text-center" width='15%'>Unit</th>
					<th class="text-center" width='10%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail)){
						foreach($detail AS $val => $valx){ $id++;
							$tand = 'disabled';
							if($valx['approve'] == 'N'){
								$tand = '';
							}
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm' ".$tand.">";
									foreach($jenis_barang AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['code_group'])?'selected':'';
									  echo "<option value='".$valx2['code_group']."' ".$dex.">".strtoupper($valx2['material_name'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' ".$tand.">";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail[".$id."][satuan]' class='chosen-select form-control input-sm' ".$tand.">";
									foreach($satuan AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='center'>";
									if($valx['approve'] == 'N'){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
									}
								echo "</td>";
							echo "</tr>";
						}
					}
				?>
                <tr id='add_<?=$id;?>'>
                    <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
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
-->

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
							$disabled = ($valx['approve'] == 'N')?'':'disabled';

							$get_detail = $this->db->select('material')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							
							echo "<tr class='header3_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_baut[".$id."][id_material]' data-no='".$id."' $disabled class='chosen-select form-control input-sm get_detail_baut'>";
									foreach($jenis_baut AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
										$radx = (!empty($valx2['radius']) AND $valx2['radius'] > 0)?'x '.floatval($valx2['radius']).' R':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama']).' M '.floatval($valx2['diameter']).' x '.floatval($valx2['panjang']).' L '.$radx."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_baut[".$id."][material]' id='bt_material_".$id."' $disabled class='form-control input-md text-left' placeholder='Material' value='".strtoupper($get_detail[0]->material)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_baut[".$id."][qty]' class='form-control input-md text-center maskM' $disabled placeholder='0' value='".number_format($valx['qty'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail_baut[".$id."][satuan]' class='chosen-select form-control input-sm' $disabled>";
									foreach($satuan AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_baut[".$id."][note]' class='form-control input-md text-left' $disabled value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
									if($valx['approve'] == 'N'){
										echo "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
									}
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
							$disabled = ($valx['approve'] == 'N')?'':'disabled';

							$get_detail = $this->db->select('ukuran_standart, standart, thickness, density')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							echo "<tr class='header4_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_plate[".$id."][id_material]' data-no='".$id."' $disabled class='chosen-select form-control input-sm get_detail_plate'>";
									foreach($jenis_plate AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama'].', '.$valx2['material']).' x '.floatval($valx2['thickness'])." T</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][ukuran_standart]' $disabled id='pl_ukuran_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->ukuran_standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][standart]' $disabled id='pl_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][lebar]' $disabled id='pl_lebar_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0' value='".number_format($valx['lebar'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][panjang]' $disabled id='pl_panjang_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0' value='".number_format($valx['panjang'],2)."'>";
									echo "<input type='hidden' name='detail_plate[".$id."][thickness]' $disabled id='pl_thickness_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($get_detail[0]->thickness,2)."'>";
									echo "<input type='hidden' name='detail_plate[".$id."][density]' $disabled id='pl_density_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($get_detail[0]->density,2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][qty]' id='pl_qty_".$id."' $disabled class='form-control input-md text-center maskM get_berat' placeholder='0' value='".number_format($valx['qty'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][berat]' id='pl_berat_".$id."' $disabled class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['berat'],3)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][sheet]' id='pl_sheet_".$id."' $disabled class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['sheet'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_plate[".$id."][note]' class='form-control input-md text-left' $disabled value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
								if($valx['approve'] == 'N'){
									echo "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								}
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
							$disabled = ($valx['approve'] == 'N')?'':'disabled';

							$get_detail = $this->db->select('ukuran_standart, standart, thickness, density, dimensi')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$satuan2		= $this->db->get_where("raw_pieces", array('delete'=>'N','id_satuan'=>$valx['satuan']))->result_array();
							echo "<tr class='header4g_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_gasket[".$id."][id_material]' data-no='".$id."' $disabled class='chosen-select form-control input-sm get_detail_gasket'>";
									foreach($jenis_gasket AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama'].' '.$valx2['dimensi'].', '.$valx2['material']).' x '.floatval($valx2['thickness'])." T</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][ukuran_standart]' $disabled id='gs_ukuran_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][dimensi]' $disabled id='gs_dimensi_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->dimensi)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][lebar]' $disabled id='gs_lebar_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['lebar'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][panjang]' $disabled id='gs_panjang_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['panjang'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][qty]' $disabled id='gs_qty_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][sheet]' $disabled id='gs_sheet_".$id."' class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['sheet'],2)."'>";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail_gasket[".$id."][satuan]' $disabled id='gs_satuan_".$id."' class='chosen-select form-control input-sm'>";
									foreach($satuan2 AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_gasket[".$id."][note]' $disabled class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
								if($valx['approve'] == 'N'){
									echo "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								}
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
							$disabled = ($valx['approve'] == 'N')?'':'disabled';

							$get_detail = $this->db->select('spesifikasi, standart, ukuran_standart')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$satuan2		= $this->db->get_where("raw_pieces", array('delete'=>'N','id_satuan'=>$valx['satuan']))->result_array();
							echo "<tr class='header5_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_lainnya[".$id."][id_material]' $disabled data-no='".$id."' class='chosen-select form-control input-sm get_detail_lainnya'>";
									foreach($jenis_part AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id'])?'selected':'';
									  echo "<option value='".$valx2['id']."' ".$dex.">".strtoupper($valx2['nama'].', '.$valx2['material'].' - '.$valx2['dimensi'].' - '.$valx2['spesifikasi'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][ukuran_standart]' $disabled id='ln_ukuran_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->ukuran_standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][standart]' $disabled id='ln_standart_".$id."' class='form-control input-md text-left' value='".strtoupper($get_detail[0]->standart)."' readonly>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][qty]' $disabled class='form-control input-md text-center maskM' placeholder='0' value='".number_format($valx['qty'])."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
								echo "</td>";
								echo "<td align='left'>"; 
									echo "<select name='detail_lainnya[".$id."][satuan]' $disabled id='ln_satuan_".$id."' class='chosen-select form-control input-sm'>";
									foreach($satuan2 AS $val2 => $valx2){
										$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
										echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['nama_satuan'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_lainnya[".$id."][note]' $disabled class='form-control input-md text-left' value='".strtoupper($valx['note'])."'>";
								echo "</td>";
								echo "<td align='center'>";
								if($valx['approve'] == 'N'){
									echo "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								}
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
							$disabled = ($valx['approve'] == 'N')?'':'disabled';
							echo "<tr class='header_".$id."'>";
								echo "<td align='left'>";
									echo "<select name='detail_material[".$id."][id_material]' data-no='".$id."' class='chosen-select form-control input-sm' $disabled>";
									foreach($raw_material AS $val2 => $valx2){
										$dex = ($valx['id_material'] == $valx2['id_material'])?'selected':'';
									  echo "<option value='".$valx2['id_material']."' ".$dex.">".strtoupper($valx2['nm_material'])."</option>";
									}
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_material[".$id."][qty]' class='form-control input-md text-center maskM' $disabled placeholder='0' value='".number_format($valx['qty'],2)."'>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<select name='detail_material[".$id."][satuan]' class='chosen-select form-control input-sm' $disabled>";
										echo "<option value='1'>KG</option>";
									echo "</select>";
								echo "</td>";
								echo "<td align='left'>";
									echo "<input name='detail_material[".$id."][note]' class='form-control input-md text-left' value='".strtoupper($valx['note'])."' $disabled>";
								echo "</td>";
								echo "<td align='center'>";
								if($valx['approve'] == 'N'){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
								}
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
		$(".chosen-select").chosen();
		$(".maskM").maskMoney();
		$("#chk_all").click(function(){
			$('.chk_personal:checkbox').not(this).prop('checked', this.checked);
		});

		$("#chk_all2").click(function(){
			$('.chk_personal2:checkbox').not(this).prop('checked', this.checked);
		});

		$('#ChangeMaterial').hide();

		$("#LookChange").click(function(){
			$("#ChangeMaterial").slideToggle();
		});
	});
</script>