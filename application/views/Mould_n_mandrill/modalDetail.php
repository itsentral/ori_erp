<?php
$id = $this->uri->segment(3);
// echo $id;
$product	= $this->db->query("SELECT * FROM product_parent WHERE estimasi='Y' ORDER BY product_parent ASC")->result_array();

$getData	= $this->db->query("SELECT * FROM mould_mandrill WHERE id='".$id."'")->row();
?>

<div class="box box-success">
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Komponen <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='product_parent' id='product_parent' class='form-control input-md' disabled>
						<option value='0'>Pilih Komponent</option>
						<?php
							foreach($product AS $val => $valx){
								$selx = ($getData->product_parent == $valx['product_parent'])?'selected':'';
								echo "<option value='".strtolower($valx['product_parent'])."' ".$selx.">".strtoupper($valx['product_parent'])."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Dimensi <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'dimensi','name'=>'dimensi','class'=>'form-control input-md numberOnly','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Dimensi'),$getData->dimensi);											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Diameter 1 <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'diameter','name'=>'diameter','class'=>'form-control input-md numberOnly','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Diameter 1'),$getData->diameter);											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Diameter 2 <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'diameter2','name'=>'diameter2','class'=>'form-control input-md numberOnly','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Diameter 2'),$getData->diameter2);											
						?>		
				</div>
			</div>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Harga (USD) <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'harga','name'=>'harga','class'=>'form-control input-md numberOnly','readonly'=>'readonly','autocomplete'=>'off','placeholder'=>'Harga (USD)'),$getData->harga);											
						?>		
				</div>
				<label class='label-control col-sm-2'><b>Est Pemakaian (Pcs)</b></label>
				<div class='col-sm-4'>             
						<?php
							echo form_input(array('id'=>'est_pakai','name'=>'est_pakai','class'=>'form-control input-md numberOnly','readonly'=>'readonly','placeholder'=>'Est Pemakaian (Pcs)'),$getData->est_pakai);											
						?>		
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Biaya Per Pcs (USD)</b></label>
				<div class='col-sm-4'>            
						<?php
							echo form_input(array('id'=>'biaya_per_pcs','name'=>'biaya_per_pcs','class'=>'form-control input-md','readonly'=>'readonly','placeholder'=>'Biaya Per Pcs (IDR)'),$getData->biaya_per_pcs);											
						?>		
				</div>
			</div>			
		</div>
	 </div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>