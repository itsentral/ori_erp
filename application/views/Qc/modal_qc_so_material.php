
<?php
$no_so	= (!empty($header))?$header[0]['no_ipp']:'';
$no_spk	= (!empty($header))?$header[0]['no_spk']:'';
$qty	= (!empty($header))?$header[0]['qty']:'';
?>
<input type='hidden' name='kode_trans' value='<?= $kode_trans;?>'>
<div class="box box-primary">
	<div class="box-header">
	
	<div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>IPP</b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'no_so','name'=>'no_so','class'=>'form-control input-md','readonly'=>'true'),$no_so);											
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
			<label class='label-control col-sm-2'><b>Qty Total</b></label>
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
					<th class="text-center" width='20%'>ID Material</th>
					<th class="text-center">Nama Material</th>
					<th class="text-center" width='20%'>Qty (Kg)</th>
				</tr>
			</thead>
			<tbody>

			<?php
			foreach ($result as $key2 => $value2) { $key2++;
				?>  
					<tr>
						<td class="text-center"><?= $key2;?></td>
						<td class="text-center"><?=$value2['id_material'];?></td>
						<td class="text-left"><?=$value2['nm_material'];?></td>
						<td class="text-right"><?=$value2['qty'];?></td>
					</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	<div>
	<div class='box-footer'>
		<button type='button' id='sendCheck' class='btn btn-md btn-success' style='float:right; margin-left:10px;'><b>Release To FG</b></button>
		<button type='button' id='rejectCheck' class='btn btn-md btn-danger' style='float:right; margin-left:10px;'><b>Reject QC</b></button>
	</div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>