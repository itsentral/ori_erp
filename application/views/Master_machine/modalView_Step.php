<?php
$id = $this->uri->segment(3);
// echo $id;
$satuan			= $this->db->query("SELECT * FROM raw_pieces WHERE flag_active='Y' ORDER BY kode_satuan ASC")->result_array();


$getData		= $this->db->query("SELECT * FROM machine WHERE id_mesin='".$id."'")->row();


?>
<div class="box box-success">
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Nomor Mesin <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'no_mesin','name'=>'no_mesin','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Nomor Mesin'), $getData->no_mesin);											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Nama Mesin <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Nama Mesin'), $getData->nm_mesin);											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Kapasitas <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'capacity','name'=>'capacity','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Kapasitas'), $getData->capacity);											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Satuan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='unit' id='unit' class='form-control input-md' disabled>
						<option value='0'>Pilih Satuan</option>
						<?php
							foreach($satuan AS $val => $valx){
								$selc = ($getData->unit == strtolower($valx['kode_satuan']))?'selected':'';
								echo "<option value='".strtolower($valx['kode_satuan'])."' ".$selc.">".strtolower($valx['kode_satuan'])."</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Harga Mesin (USD) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'machine_price','name'=>'machine_price','class'=>'form-control input-md numberOnly','readonly'=>'readonly','placeholder'=>'Harga Mesin (USD)'), floatval($getData->machine_price));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Est. Pemanfaatan (Tahun) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'utilization_estimate','name'=>'utilization_estimate','class'=>'form-control input-md numberOnly','readonly'=>'readonly','placeholder'=>'Est. Pemanfaatan (Tahun)'), $getData->utilization_estimate);											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Depresiasi /Bulan (USD)</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'depresiation_per_month','name'=>'depresiation_per_month','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Depresiasi /Bulan (USD)'), floatval($getData->depresiation_per_month));											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Biaya Mesin /Jam (USD)</b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'machine_cost_per_hour','name'=>'machine_cost_per_hour','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Biaya Mesin /Jam (USD)'), floatval($getData->machine_cost_per_hour));											
						?>		
				</div>
			</div>			
		</div>
		<!-- /.box-body -->
	 </div>
</div>

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
	#unit_chosen{
		width: 100% !important;
	}
</style>
