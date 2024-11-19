<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr>
					<td class="text-left">Truck Name</td>
					<td class="text-left">
						<?php
                            echo form_input(array('id'=>'nama_truck','name'=>'nama_truck','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Nama Truck'));											
                        ?>
					</td>
				</tr>
			</tbody> 
		</table><br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'addPSave')).' ';
		?>
	</div>
</div>

