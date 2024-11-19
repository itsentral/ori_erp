<?php
$no_ipp = $this->uri->segment(3);

$qRequest 		= "	SELECT * FROM production WHERE no_ipp = '".$no_ipp."' ";
$RestRequest	= $this->db->query($qRequest)->result();

$qReqCust 		= "	SELECT * FROM production_req_customer WHERE no_ipp = '".$no_ipp."' ";
$RestReqCust	= $this->db->query($qReqCust)->result();

$qShipping 		= "	SELECT * FROM production_delivery WHERE no_ipp = '".$no_ipp."' ";
$RestShipping	= $this->db->query($qShipping)->result();

$qCountry		= "SELECT * FROM country WHERE country_code='".$RestShipping[0]->country_code."'";
$restCountry	= $this->db->query($qCountry)->result();

$qFluida		= "SELECT * FROM list_fluida WHERE id_fluida='".$RestReqCust[0]->id_fluida."'";
$restFluida	= $this->db->query($qFluida)->result();

// echo "<pre>";
// print_r($RestRequest);
// print_r($RestReqCust);
// print_r($RestShipping);
// echo "</pre>";
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Request Customer</h3>
	</div>
	<div class="box-body">
		
		<?php
			if($RestRequest[0]->status == 'CANCELED'){
		?>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Cancel Reason</b></label>
			<div class='col-sm-10'>
				<?php
				 echo form_textarea(array('id'=>'sts_reason','name'=>'sts_reason','class'=>'form-control input-sm','rows'=>'2','cols'=>'75','placeholder'=>'Note Etc','readonly'=>'readonly'),ucfirst(strtolower($RestRequest[0]->status_reason)));
				?>
			</div>
		</div>
		<?php } ?>
		<div class="box box-danger">
			<div class="box-header">
				<h3 class="box-title">Specification</h3>
			</div>
			<div class="box-body">
				
			</div>
		</div>
	</div>
</div>
<div class="box box-warning">
	<div class="box-header">
		<h3 class="box-title">Shipping</h3>
	</div>
	<div class="box-body">
		
	</div>
</div>
<div class="box box-success">
	<div class="box-header">
		<h3 class="box-title">Custom Customer</h3>
	</div>
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Application</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'aplikasi','name'=>'aplikasi','class'=>'form-control input-sm','placeholder'=>'Application','readonly'=>'readonly'),strtoupper($RestReqCust[0]->aplikasi));
				?>
			</div>
			<label class='label-control col-sm-2'><b>Fluida</b></label>
			<div class='col-sm-4'>
				 <?php
					echo form_input(array('id'=>'id_fluida','name'=>'id_fluida','class'=>'form-control input-sm','placeholder'=>'Fluida','readonly'=>'readonly'),strtoupper($restFluida[0]->fluida_name));
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Standard Spec</b></label>
			<div class='col-sm-10'>
				<?php
					echo form_input(array('id'=>'standard_spec','name'=>'standard_spec','class'=>'form-control input-sm','placeholder'=>'Standard Spec','readonly'=>'readonly'),strtoupper($RestReqCust[0]->standard_spec));
				?>
			</div>
		</div>
		<div id='StandardHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'standard_1','name'=>'standard_1','class'=>'form-control input-sm','placeholder'=>'Entry Standard 1','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->standard_1)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'standard_2','name'=>'standard_2','class'=>'form-control input-sm','placeholder'=>'Entry Standard 2','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->standard_2)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'standard_3','name'=>'standard_3','class'=>'form-control input-sm','placeholder'=>'Entry Standard 3','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->standard_3)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'standard_4','name'=>'standard_4','class'=>'form-control input-sm','placeholder'=>'Entry Standard 4','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->standard_4)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Document</b></label>
			<div class='col-sm-10'>
				<select name='document' id='document' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->document == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->document == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='DocumentHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'document_1','name'=>'document_1','class'=>'form-control input-sm','placeholder'=>'Entry Document 1','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->document_1)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'document_2','name'=>'document_2','class'=>'form-control input-sm','placeholder'=>'Entry Document 2','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->document_2)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'document_3','name'=>'document_3','class'=>'form-control input-sm','placeholder'=>'Entry Document 3','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->document_3)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'document_4','name'=>'document_4','class'=>'form-control input-sm','placeholder'=>'Entry Document 4','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->document_4)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Certificate</b></label>
			<div class='col-sm-10'>
				<select name='sertifikat' id='sertifikat' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->sertifikat == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->sertifikat == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='SertifikatHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'sertifikat_1','name'=>'sertifikat_1','class'=>'form-control input-sm','placeholder'=>'Entry Certificate 1','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->sertifikat_1)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'sertifikat_2','name'=>'sertifikat_2','class'=>'form-control input-sm','placeholder'=>'Entry Certificate 2','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->sertifikat_2)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'sertifikat_3','name'=>'sertifikat_3','class'=>'form-control input-sm','placeholder'=>'Entry Certificate 3','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->sertifikat_3)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'sertifikat_4','name'=>'sertifikat_4','class'=>'form-control input-sm','placeholder'=>'Entry Certificate 4','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->sertifikat_4)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Color</b></label>
			<div class='col-sm-10'>
				<select name='color' id='color' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->color == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->color == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='ColorHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'color_liner','name'=>'color_liner','class'=>'form-control input-sm','placeholder'=>'Color Liner','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->color_liner)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'color_structure','name'=>'color_structure','class'=>'form-control input-sm','placeholder'=>'Color Structure','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->color_structure)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'color_external','name'=>'color_external','class'=>'form-control input-sm','placeholder'=>'Color External','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->color_external)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'color_topcoat','name'=>'color_topcoat','class'=>'form-control input-sm','placeholder'=>'Color Topcoat','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->color_topcoat)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Testing</b></label>
			<div class='col-sm-10'>
				<select name='test' id='test' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->test == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->test == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='TestingHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'test_1','name'=>'test_1','class'=>'form-control input-sm','placeholder'=>'Entry Test 1','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->test_1)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'test_2','name'=>'test_2','class'=>'form-control input-sm','placeholder'=>'Entry Test 2','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->test_2)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'test_3','name'=>'test_3','class'=>'form-control input-sm','placeholder'=>'Entry Test 3','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->test_3)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'test_4','name'=>'test_4','class'=>'form-control input-sm','placeholder'=>'Entry Test 4','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->test_4)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Abrasive</b></label>
			<div class='col-sm-10'>
				<select name='abrasi' id='abrasi' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->abrasi == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->abrasi == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='AbrasiHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'abrasi_liner','name'=>'abrasi_liner','class'=>'form-control input-sm','placeholder'=>'Abrasive Liner','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->abrasi_liner)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'abrasi_structure','name'=>'abrasi_structure','class'=>'form-control input-sm','placeholder'=>'Abrasive Structure','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->abrasi_structure)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'abrasi_ekternal','name'=>'abrasi_ekternal','class'=>'form-control input-sm','placeholder'=>'Abrasive External','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->abrasi_ekternal)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'abrasi_topcoat','name'=>'abrasi_topcoat','class'=>'form-control input-sm','placeholder'=>'Abrasive Topcoat','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->abrasi_topcoat)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Conductive</b></label>
			<div class='col-sm-10'>
				<select name='konduksi' id='konduksi' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->konduksi == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->konduksi == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='KonduksiHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'konduksi_liner','name'=>'konduksi_liner','class'=>'form-control input-sm','placeholder'=>'Conductive Liner','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->konduksi_liner)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'konduksi_structure','name'=>'konduksi_structure','class'=>'form-control input-sm','placeholder'=>'Conductive Structure','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->konduksi_structure)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'konduksi_eksternal','name'=>'konduksi_eksternal','class'=>'form-control input-sm','placeholder'=>'Conductive External','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->konduksi_eksternal)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'konduksi_topcoat','name'=>'konduksi_topcoat','class'=>'form-control input-sm','placeholder'=>'Conductive Topcoat','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->konduksi_topcoat)));
					?>
				</div>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Fire Retardant</b></label>
			<div class='col-sm-10'>
				<select name='tahan_api' id='tahan_api' class='form-control input-sm' disabled>
					<option value='N' <?= ($RestReqCust[0]->tahan_api == 'N')?"selected":'';?> >NO</option>
					<option value='Y' <?= ($RestReqCust[0]->tahan_api == 'Y')?"selected":'';?> >YES</option>
				 </select>
			</div>
		</div>
		<div id='FireHide'>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'tahan_api_liner','name'=>'tahan_api_liner','class'=>'form-control input-sm','placeholder'=>'Fire Retardant Liner','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->tahan_api_liner)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'tahan_api_structure','name'=>'tahan_api_structure','class'=>'form-control input-sm','placeholder'=>'Fire Retardant Structure','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->tahan_api_structure)));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'tahan_api_eksternal','name'=>'tahan_api_eksternal','class'=>'form-control input-sm','placeholder'=>'Fire Retardant External','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->tahan_api_eksternal)));
					?>
				</div>
				<div class='col-sm-5'>
					<?php
					echo form_input(array('id'=>'tahan_api_topcoat','name'=>'tahan_api_topcoat','class'=>'form-control input-sm','placeholder'=>'Fire Retardant Topcoat','readonly'=>'readonly'),ucfirst(strtolower($RestReqCust[0]->tahan_api_topcoat)));
					?>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function(){
		var standard_spec 	= $('#standard_spec').val();
		var document 		= $('#document').val();
		var color 			= $('#color').val();
		var test 			= $('#test').val();
		var sertifikat 		= $('#sertifikat').val();
		var abrasi 			= $('#abrasi').val();
		var konduksi 		= $('#konduksi').val();
		var tahan_api 		= $('#tahan_api').val();
		
		if(standard_spec != 'S-NON-01'){
			$('#StandardHide').hide();
		}
		if(document == 'N'){
			$('#DocumentHide').hide();
		}
		if(color == 'N'){
			$('#ColorHide').hide();
		}
		if(test == 'N'){
			$('#TestingHide').hide();
		}
		if(sertifikat == 'N'){
			$('#SertifikatHide').hide();
		}
		if(abrasi == 'N'){
			$('#AbrasiHide').hide();
		}
		if(konduksi == 'N'){
			$('#KonduksiHide').hide();
		}
		if(tahan_api == 'N'){
			$('#FireHide').hide();
		}
	});
</script>