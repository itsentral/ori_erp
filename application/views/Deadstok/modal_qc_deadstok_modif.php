
<?php
$no_ipp	= (!empty($result))?$result[0]['no_ipp']:'';
$no_so	= (!empty($result))?$result[0]['no_so']:'';
$no_spk	= (!empty($result))?$result[0]['no_spk']:'';
$qty	= (!empty($result))?COUNT($result):'';
?>
<div class="box box-primary">
	<div class="box-header">
	
	<div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>SO / IPP</b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'no_so','name'=>'no_so','class'=>'form-control input-md','readonly'=>'true'),$no_so.' / '.$no_ipp);											
					echo form_input(array('type'=>'hidden','id'=>'kode','name'=>'kode','class'=>'form-control input-md','readonly'=>'true'),$kode);										
				?>		
			</div>
		</div>
        <div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>No SPK</b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'no_spk','name'=>'no_spk','class'=>'form-control input-md','readonly'=>'true'),$no_spk);											
				?>		
			</div>
		</div>
        <div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Qty</b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'qty','name'=>'qty','class'=>'form-control input-md','readonly'=>'true'),$qty);											
				?>		
			</div>
		</div>
		<table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='10%'>#</th>
					<th class="text-center" width='50%'>Product</th>
					<th class="text-center">Spec</th>
				</tr>
			</thead>
			<tbody>

			<?php
			foreach ($result as $key2 => $value2) { $key2++;
				?>  
					<tr>
						<td class="text-center"><?= $key2;?></td>
						<td class="text-left"><?=$value2['product_name'];?></td>
						<td class="text-left"><?=$value2['product_spec'];?></td>
					</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	<div>
	<div class='box-footer'>
		<button type='button' id='sendCheck_modif' class='btn btn-md btn-success' style='float:right; margin-left:10px;'><b>Release To FG</b></button>
		<button type='button' id='rejectCheck_modif' class='btn btn-md btn-danger' style='float:right; margin-left:10px;'><b>Reject QC</b></button>
	</div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>