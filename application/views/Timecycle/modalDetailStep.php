<?php

$parent_product 	= str_replace('_', ' ', $this->uri->segment(3));
$standart_code 		= str_replace('_', ' ', $this->uri->segment(4));
// echo $id;
$get_Data			= $this->db->query("SELECT * FROM product_parent WHERE estimasi='Y' ORDER BY product_parent ASC")->result_array();
$get_Std			= $this->db->query("SELECT * FROM help_default_name ORDER BY nm_default ASC")->result_array();

$getData			= $this->db->query("SELECT * FROM cycle_time_step WHERE parent_product='".$parent_product."' AND standart_code='".$standart_code."'")->row();
$getDataArr			= $this->db->query("SELECT * FROM cycle_time_step WHERE parent_product='".$parent_product."' AND standart_code='".$standart_code."' AND `delete`='N' ORDER BY urutan ASC")->result_array();
// echo "SELECT * FROM cycle_time_step WHERE parent_product='".$parent_product."' AND standart_code='".$standart_code."'";
?>

<div class="box box-success">
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Komponen <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='product_parent' id='product_parent' class='form-control input-md' disabled>
						<?php
							foreach($get_Data AS $val => $valx){
								$selx2 = ($getData->parent_product == $valx['product_parent'])?'selected':'';
								echo "<option value='".strtolower($valx['product_parent'])."' ".$selx2.">".strtoupper($valx['product_parent'])."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Standart <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'> 
					<select name='standart_code' id='standart_code' class='form-control input-md' disabled>
						<?php
							foreach($get_Std AS $val => $valx){
								$selx = ($getData->standart_code == $valx['nm_default'])?'selected':'';
								echo "<option value='".strtoupper($valx['nm_default'])."' ".$selx.">".strtoupper($valx['nm_default'])."</option>";
							}
						?>
					</select>
				</div>
			</div>
					
		</div>
	 </div>
	 <div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table_enEdit'>
					<tr class='bg-blue'>
						<th class="text-center" >No</th>
						<th class="text-center">Urutan Step</th>
						<th class="text-center" >Nama Step</th>
					</tr>
				</thead>
				<tbody id='detail_body_Ed'>
					<?php
						$number = 0;
						foreach($getDataArr AS $val =>$valx){
							$number++;
							?> 
							<tr>
								<td align='center'><?= $number;?></td>
								<td> STEP KE <?= $valx['urutan'];?></td>
								<td><?= $valx['step'];?></td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
					
		</div>
	 </div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>