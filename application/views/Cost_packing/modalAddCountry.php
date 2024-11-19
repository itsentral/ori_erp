<?php

$getKomp		= $this->db->query("SELECT a.iso3, a.`name` FROM country_all a LEFT JOIN country b ON a.iso3 = b.country_code WHERE b.country_code IS NULL AND a.iso3 IS NOT NULL ORDER BY a.`name` ASC ")->result_array();

?>


<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr>
					<td class="text-left">Country Name</td>
					<td class="text-left">
						<select id='country' name='country' class='chosen_select form-control inline-block' style='min-width:200px;'>
							<option value='0'>Select Country</option>
							<?php
								foreach($getKomp AS $val => $valx){
									echo "<option value='".$valx['iso3']."'>".strtoupper($valx['name'])."</option>"; 
								}
							?>
						</select> 
					</td>
				</tr>
			</tbody> 
		</table><br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'addPSave')).' ';
		?>
	</div>
</div>

<style>
	.inSp{
		text-align: center;
	}
	.inSpL{
		text-align: left;
	}

</style>

