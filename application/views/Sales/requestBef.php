<?php
$this->load->view('include/side_menu');
// echo"<pre>";print_r($CustList);
// echo "</pre>";
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Request Customer</h3>
				</div>
				<div class="box-body">
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Customer Name <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='id_customer' id='id_customer' class='form-control input-md'>
								<option>Select An Customer</option>
							 <?php
								foreach($CustList AS $val => $valx){
									echo "<option value='".$valx['id_customer']."'>".$valx['nm_customer']."</option>";
								}
							 ?>
							 </select>
						</div>
						<!--
						<label class='label-control col-sm-2'><b>Status Identification<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='sts_request' id='sts_request' class='form-control input-md'>
								<option value=''>Select An Identification</option>
								<option value='new'>NEW</option>
								<option value='revisi'>REVISION</option>
							 </select>
						</div>
						-->
					</div>
					<div class='form-group row'>
						
						<label class='label-control col-sm-2'><b>Resin Type <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='type' id='type' class='form-control input-md'>
								<option value=''>Select An Resin Type</option>
								<option value='GRP (FRP)'>GRP (RFP)</option>
								<option value='GRV'>GRV</option>
							 </select>
						</div>
						
						<label class='label-control col-sm-2'><b>Product <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							 <?php
								// echo form_textarea(array('id'=>'product','name'=>'product','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Product'));
							?>
							<select name='product' id='product' class='form-control input-md'>
								<option value=''>Select An Product</option>
								<option value='FRP'>FRP</option>
								<option value='RPM'>RPM</option>
							 </select>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Vacum Rate <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							// echo form_input(array('id'=>'time_life','name'=>'time_life','class'=>'form-control input-md numberOnly','placeholder'=>'Life Time /Year','autocomplete'=>'off'));
							?>
							<select name='vacum_rate' id='vacum_rate' class='form-control input-md'>
								<option value=''>Select An Vacum Rate</option>
								<option value='NON VACUUM'>NON VACUUM</option>
								<option value='HALF VACUUM'>HALF VACUUM</option>
								<option value='FULL VACUUM'>FULL VACUUM</option>
							 </select>
						</div>
						
						<label class='label-control col-sm-2'><b>Life Time <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							// echo form_input(array('id'=>'time_life','name'=>'time_life','class'=>'form-control input-md numberOnly','placeholder'=>'Life Time /Year','autocomplete'=>'off'));
							?>
							<select name='time_life' id='time_life' class='form-control input-md'>
								<option value=''>Select An Life Time</option>
								<option value='20'>20 Year</option>
								<option value='25'>25 Year</option>
								<option value='30'>30 Year</option>
							 </select>
						</div>
					</div>
					<!--
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Stifness <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
								// echo form_input(array('id'=>'stifness','name'=>'stifness','class'=>'form-control input-md numberOnly','placeholder'=>'Stifness (Kekakuan) /Pa','autocomplete'=>'off'));
							?>
							<select name='stifness' id='stifness' class='form-control input-md'>
								<option value=''>Select An Stifness</option>
								<option value='1250'>1250 Pa</option>
								<option value='2500'>2500 Pa</option>
								<option value='5000'>5000 Pa</option>
								<option value='10000'>10000 Pa</option>
							 </select>
						</div>
						
						<label class='label-control col-sm-2'><b>Pressure <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							// echo form_input(array('id'=>'pressure','name'=>'pressure','class'=>'form-control input-md numberOnly','placeholder'=>'Pressure (Tekanan) /Bar','autocomplete'=>'off'));
							?>
							<select name='pressure' id='pressure' class='form-control input-md'>
								<option value=''>Select An Pressure</option>
								<option value='6'>6 Bar</option>
								<option value='8'>8 Bar</option>
								<option value='10'>10 Bar</option>
								<option value='12'>12 Bar</option>
								<option value='16'>16 Bar</option>
								<option value='18'>18 Bar</option>
								<option value='20'>20 Bar</option>
							 </select>
						</div>
					</div>
					-->
					<div class='form-group row'>
						<!--
						<label class='label-control col-sm-2'><b>Stifness <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							 <div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name='st_1250' id="st_1250" value="Y">
								<label class="form-check-label">1250 Pa</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='st_2500' type="checkbox" id="st_2500" value="Y">
								<label class="form-check-label">2500 Pa</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='st_5000' type="checkbox" id="st_5000" value="Y">
								<label class="form-check-label">5000 Pa</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='st_10000' type="checkbox" id="st_10000" value="Y">
								<label class="form-check-label">10000 Pa</label>
							</div>
						</div>
						-->
						<label class='label-control col-sm-2'><b>Stifness <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
								// echo form_input(array('id'=>'stifness','name'=>'stifness','class'=>'form-control input-md numberOnly','placeholder'=>'Stifness (Kekakuan) /Pa','autocomplete'=>'off'));
							?>
							<select name='stifness' id='stifness' class='form-control input-md'>
								<option value=''>Select An Stifness</option>
								<option value='1250'>1250 Pa</option>
								<option value='2500'>2500 Pa</option>
								<option value='5000'>5000 Pa</option>
								<option value='10000'>10000 Pa</option>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Pressure <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							 <div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name='pres_6' id="pres_6" value="Y">
								<label class="form-check-label">6 Bar</label>
								&nbsp;&nbsp;<input class="form-check-input" name='pres_8' type="checkbox" id="pres_8" value="Y">
								<label class="form-check-label">8 Bar</label>
								&nbsp;&nbsp;<input class="form-check-input" name='pres_10' type="checkbox" id="pres_10" value="Y">
								<label class="form-check-label">10 Bar</label>
								&nbsp;&nbsp;<input class="form-check-input" name='pres_12' type="checkbox" id="pres_12" value="Y">
								<label class="form-check-label">12 Bar</label>
								&nbsp;&nbsp;<input class="form-check-input" name='pres_16' type="checkbox" id="pres_16" value="Y">
								<label class="form-check-label">16 Bar</label>
								&nbsp;&nbsp;<input class="form-check-input" name='pres_18' type="checkbox" id="pres_18" value="Y">
								<label class="form-check-label">18 Bar</label>
								&nbsp;&nbsp;<input class="form-check-input" name='pres_20' type="checkbox" id="pres_20" value="Y">
								<label class="form-check-label">20 Bar</label>
							</div> 
						</div>
					</div>
					<div class='form-group row'>
						
						<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Project'));
							?>
						</div>
						<label class='label-control col-sm-2'><b>Note</b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_textarea(array('id'=>'note','name'=>'note','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Note Etc'));
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="box box-success">
				<div class="box-header">
					<h3 class="box-title">Custom Customer</h3>
				</div>
				<div class="box-body">
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Fluida <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='id_fluida' id='id_fluida' class='form-control input-md'>
								<option value=''>Select An Fluida</option>
							 <?php
								foreach($FluidaName AS $val => $valx){
									echo "<option value='".$valx['id_fluida']."'>".$valx['fluida_name']."</option>";
								}
							 ?>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Application<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<!--
							<select name='aplikasi' id='aplikasi' class='form-control input-md'>
								<option value=''>Select An Application</option> -->
							 <?php
								echo form_input(array('id'=>'aplikasi','name'=>'aplikasi','class'=>'form-control input-md','placeholder'=>'Application','autocomplete'=>'off', 'readonly'=>'readonly'));
								// foreach($AppName AS $val => $valx){
									// echo "<option value='".$valx['nm_category']."'>".strtoupper($valx['nm_category'])."</option>";
								// }
							 ?>
							 <!--
							 </select>
							 -->
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Standard Spec<span class='text-red'>*</span></b></label>
						<div class='col-sm-10'>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name='std_asme' id="std_asme" value="Y">
								<label class="form-check-label">ASME</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_ansi' type="checkbox" id="std_ansi" value="Y">
								<label class="form-check-label">ANSI</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_astm' type="checkbox" id="std_astm" value="Y">
								<label class="form-check-label">ASTM</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_awwa' type="checkbox" id="std_awwa" value="Y">
								<label class="form-check-label">AWWA</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_bsi' type="checkbox" id="std_bsi" value="Y">
								<label class="form-check-label">BSI</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_jis' type="checkbox" id="std_jis" value="Y">
								<label class="form-check-label">JIS</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_sni' type="checkbox" id="std_sni" value="Y">
								<label class="form-check-label">SNI</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" name='std_etc' type="checkbox" id="std_etc" value="Y">
								<label class="form-check-label">ETC</label>
							</div> 
						</div>
					</div>
					<!--
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Standard Spec<span class='text-red'>*</span></b></label>
						<div class='col-sm-10'>
							<select name='standard_spec' id='standard_spec' class='form-control input-md'>
								<option value=''>Select An Standard Spec</option>
							 <?php
								// foreach($StdName AS $val => $valx){
									// echo "<option value='".$valx['id_standard']."'>".$valx['nm_standard']."</option>";
								// }
							 ?>
							 </select>
						</div>
					</div>
					-->
					<div id='StandardHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'standard_1','name'=>'standard_1','class'=>'form-control input-md','placeholder'=>'Entry Standard 1'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'standard_2','name'=>'standard_2','class'=>'form-control input-md','placeholder'=>'Entry Standard 2'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'standard_3','name'=>'standard_3','class'=>'form-control input-md','placeholder'=>'Entry Standard 3'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'standard_4','name'=>'standard_4','class'=>'form-control input-md','placeholder'=>'Entry Standard 4'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Document</b></label>
						<div class='col-sm-10'>
							<select name='document' id='document' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='DocumentHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'document_1','name'=>'document_1','class'=>'form-control input-md','placeholder'=>'Entry Document 1'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'document_2','name'=>'document_2','class'=>'form-control input-md','placeholder'=>'Entry Document 2'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'document_3','name'=>'document_3','class'=>'form-control input-md','placeholder'=>'Entry Document 3'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'document_4','name'=>'document_4','class'=>'form-control input-md','placeholder'=>'Entry Document 4'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Certificate</b></label>
						<div class='col-sm-10'>
							<select name='sertifikat' id='sertifikat' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='SertifikatHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'sertifikat_1','name'=>'sertifikat_1','class'=>'form-control input-md','placeholder'=>'Entry Certificate 1'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'sertifikat_2','name'=>'sertifikat_2','class'=>'form-control input-md','placeholder'=>'Entry Certificate 2'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'sertifikat_3','name'=>'sertifikat_3','class'=>'form-control input-md','placeholder'=>'Entry Certificate 3'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'sertifikat_4','name'=>'sertifikat_4','class'=>'form-control input-md','placeholder'=>'Entry Certificate 4'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Color</b></label>
						<div class='col-sm-10'>
							<select name='color' id='color' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='ColorHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'color_liner','name'=>'color_liner','class'=>'form-control input-md','placeholder'=>'Color Liner'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'color_structure','name'=>'color_structure','class'=>'form-control input-md','placeholder'=>'Color Structure'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'color_external','name'=>'color_external','class'=>'form-control input-md','placeholder'=>'Color External'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'color_topcoat','name'=>'color_topcoat','class'=>'form-control input-md','placeholder'=>'Color Topcoat'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Testing</b></label>
						<div class='col-sm-10'>
							<select name='test' id='test' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='TestingHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'test_1','name'=>'test_1','class'=>'form-control input-md','placeholder'=>'Entry Test 1'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'test_2','name'=>'test_2','class'=>'form-control input-md','placeholder'=>'Entry Test 2'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'test_3','name'=>'test_3','class'=>'form-control input-md','placeholder'=>'Entry Test 3'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'test_4','name'=>'test_4','class'=>'form-control input-md','placeholder'=>'Entry Test 4'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Abrasive</b></label>
						<div class='col-sm-10'>
							<select name='abrasi' id='abrasi' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='AbrasiHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'abrasi_liner','name'=>'abrasi_liner','class'=>'form-control input-md','placeholder'=>'Abrasive Liner'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'abrasi_structure','name'=>'abrasi_structure','class'=>'form-control input-md','placeholder'=>'Abrasive Structure'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'abrasi_ekternal','name'=>'abrasi_ekternal','class'=>'form-control input-md','placeholder'=>'Abrasive External'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'abrasi_topcoat','name'=>'abrasi_topcoat','class'=>'form-control input-md','placeholder'=>'Abrasive Topcoat'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Conductive</b></label>
						<div class='col-sm-10'>
							<select name='konduksi' id='konduksi' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='KonduksiHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'konduksi_liner','name'=>'konduksi_liner','class'=>'form-control input-md','placeholder'=>'Conductive Liner'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'konduksi_structure','name'=>'konduksi_structure','class'=>'form-control input-md','placeholder'=>'Conductive Structure'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'konduksi_eksternal','name'=>'konduksi_eksternal','class'=>'form-control input-md','placeholder'=>'Conductive External'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'konduksi_topcoat','name'=>'konduksi_topcoat','class'=>'form-control input-md','placeholder'=>'Conductive Topcoat'));
								?>
							</div>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Fire Retardant</b></label>
						<div class='col-sm-10'>
							<select name='tahan_api' id='tahan_api' class='form-control input-md'>
								<option value='N' selected>NO</option>
								<option value='Y'>YES</option>
							 </select>
						</div>
					</div>
					<div id='FireHide'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'tahan_api_liner','name'=>'tahan_api_liner','class'=>'form-control input-md','placeholder'=>'Fire Retardant Liner'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'tahan_api_structure','name'=>'tahan_api_structure','class'=>'form-control input-md','placeholder'=>'Fire Retardant Structure'));
								?>
							</div>
						</div>
						<div class='form-group row'>
							<label class='label-control col-sm-2'></label>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'tahan_api_eksternal','name'=>'tahan_api_eksternal','class'=>'form-control input-md','placeholder'=>'Fire Retardant External'));
								?>
							</div>
							<div class='col-sm-5'>
								<?php
								echo form_input(array('id'=>'tahan_api_topcoat','name'=>'tahan_api_topcoat','class'=>'form-control input-md','placeholder'=>'Fire Retardant Topcoat'));
								?>
							</div>
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
						<label class='label-control col-sm-2'><b>Country of Destination<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='country_code' id='country_code' class='form-control input-md'>
								<option>Select An Country Destination</option>
							 <?php
								foreach($CountryName AS $val => $valx){
									echo "<option value='".$valx['country_code']."'>".$valx['country_name']."</option>";
								}
							 ?>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Delivery Date<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							echo form_input(array('id'=>'date_delivery','name'=>'date_delivery','class'=>'form-control input-md','placeholder'=>'Delivery Date', 'readonly'=>'readonly'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Delivery Address<span class='text-red'>*</span></b></label>
						<div class='col-sm-10'>
							<?php
							 echo form_textarea(array('id'=>'address_delivery','name'=>'address_delivery','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Delivery Address'));
							?>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Shipping Method<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='metode_delivery' id='metode_delivery' class='form-control input-md'>
								<option>Select An Shipping</option>
							 <?php
								foreach($ShippingName AS $val => $valx){
									echo "<option value='".$valx['shipping_name']."'>".$valx['shipping_name']."</option>";
								}
							 ?>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Packing<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='packing' id='packing' class='form-control input-md'>
								<option>Select An Packing</option>
							 <?php
								foreach($PackningName AS $val => $valx){
									echo "<option value='".$valx['packing_name']."'>".$valx['packing_name']."</option>";
								}
							 ?>
							 </select>
						</div>
					</div>
					<div id='HideShipping'>
						<div class='form-group row'>
							<label class='label-control col-sm-2'><b>Trucking<span class='text-red'>*</span></b></label>
							<div class='col-sm-3'>
								<select name='truck' id='truck' class='form-control input-md'>
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
							<label class='label-control col-sm-2'><b>Vendor<span class='text-red'>*</span></b></label>
							<div class='col-sm-4'>
								<select name='vendor' id='vendor' class='form-control input-md'>
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
						<label class='label-control col-sm-2'><b>Handling Equipment<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='alat_berat' id='alat_berat' class='form-control input-md'>
								<option value='' selected>Select An Handling</option>
								<option value='by ori'>BY ORI</option>
								<option value='by customer'>BY CUSTOMER</option>
							 </select>
						</div>
						<label class='label-control col-sm-2'><b>Instalation<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<select name='isntalasi_by' id='isntalasi_by' class='form-control input-md'>
								<option value='' selected>Select An Instalation</option>
								<option value='by ori'>BY ORI</option>
								<option value='by customer'>BY CUSTOMER</option>
							 </select>
						</div>
					</div>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>Validity & Guarantee<span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							echo form_input(array('id'=>'garansi','name'=>'garansi','maxlength'=>'3','class'=>'form-control input-md numberOnly','placeholder'=>'Validity & Guarantee / Year','autocomplete'=>'off'));
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
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
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#StandardHide').hide();
		$('#DocumentHide').hide();
		$('#SertifikatHide').hide();
		$('#ColorHide').hide();
		$('#TestingHide').hide();
		$('#AbrasiHide').hide();
		$('#KonduksiHide').hide();
		$('#FireHide').hide();
		$('#HideShipping').hide();
		
		$(document).on('change', '#stifness', function(){
			if($(this).val() == '1250'){
				$('#aplikasi').val('ABOVE GROUND');
			}
			else if($(this).val() == '2500' || $(this).val() == '5000' || $(this).val() == '10000'){
				$('#aplikasi').val('UNDER GROUND');
			}
			else{
				$('#aplikasi').val('');
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
		
		$(document).on('change', '#document', function(){
			if($(this).val() == 'Y'){
				$('#DocumentHide').show();
			}
			else{
				$('#DocumentHide').hide();
			}
		});
		
		$(document).on('change', '#sertifikat', function(){
			if($(this).val() == 'Y'){
				$('#SertifikatHide').show();
			}
			else{
				$('#SertifikatHide').hide();
			}
		});
		
		$(document).on('change', '#color', function(){
			if($(this).val() == 'Y'){
				$('#ColorHide').show();
			}
			else{
				$('#ColorHide').hide();
			}
		});
		
		$(document).on('change', '#test', function(){
			if($(this).val() == 'Y'){
				$('#TestingHide').show();
			}
			else{
				$('#TestingHide').hide();
			}
		});
		
		$(document).on('change', '#abrasi', function(){
			if($(this).val() == 'Y'){
				$('#AbrasiHide').show();
			}
			else{
				$('#AbrasiHide').hide();
			}
		});
		
		$(document).on('change', '#konduksi', function(){
			if($(this).val() == 'Y'){
				$('#KonduksiHide').show();
			}
			else{
				$('#KonduksiHide').hide();
			}
		});
		
		$(document).on('change', '#tahan_api', function(){
			if($(this).val() == 'Y'){
				$('#FireHide').show();
			}
			else{
				$('#FireHide').hide();
			}
		});
		
		
		$('#date_delivery').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			$(this).val($(this).val().replace(/[^\d].+/, ""));
			if (event.which < 48 || event.which > 57) {
				event.preventDefault();
			}
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var id_customer			= $('#id_customer').val();
			// var sts_request			= $('#sts_request').val();
			var project				= $('#project').val();
			var product				= $('#product').val();
			var type				= $('#type').val();
			var time_life			= $('#time_life').val();
			var stifness			= $('#stifness').val();
			// var pressure			= $('#pressure').val();
			var vacum_rate			= $('#vacum_rate').val();
			var aplikasi			= $('#aplikasi').val();
			var id_fluida			= $('#id_fluida').val();
			// var standard_spec		= $('#standard_spec').val();
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
			// if(sts_request=='' || sts_request==null || sts_request=='-'){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Status is not chosen, please chosen first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(project=='' || project==null || project=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Project is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(product=='' || product==null || product=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Product is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(type=='' || type==null || type=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Type is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(time_life=='' || time_life==null || time_life=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Time Life is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(stifness=='' || stifness==null || stifness=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Stifness is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(pressure=='' || pressure==null || pressure=='-'){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Pressure is empty, please input first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
			if(vacum_rate=='' || vacum_rate==null || vacum_rate=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Vacum rate is empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(aplikasi=='' || aplikasi==null || aplikasi=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Application product is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			if(id_fluida=='' || id_fluida==null || id_fluida=='-'){
				swal({
				  title	: "Error Message!",
				  text	: 'Fluida is not chosen, please chosen first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			// if(standard_spec=='' || standard_spec==null || standard_spec=='-'){
				// swal({
				  // title	: "Error Message!",
				  // text	: 'Standard is not chosen, please chosen first ...',
				  // type	: "warning"
				// });
				// $('#simpan-bro').prop('disabled',false);
				// return false;
			// }
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
						var baseurl=base_url + active_controller +'/request';
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
