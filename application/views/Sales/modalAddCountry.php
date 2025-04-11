<?php


?>


<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr>
					<td class="text-left">Country Name</td>
					<td class="text-left">
						<select id='country' name='country' class='chosen_select form-control inline-block' style='width:100% !important;'>
							<option value='0'>Select Country</option>
							<?php
								foreach($result AS $val => $valx){
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
</style>
<script>
	swal.close();
	$(document).ready(function(){
		// $('.chosen_select').chosen();
	});
</script>

