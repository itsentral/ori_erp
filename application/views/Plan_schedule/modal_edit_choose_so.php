<div class='note'>
	<p>
		<strong>Info!</strong><br> 
		<span style='color:green;'><b>DATA YANG SUDAH DIBUAT SPK TIDAK AKAN TERHAPUS</b></span><br>
	</p>
</div>

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Revisi master spool</h3>
	</div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Upload File <span class='text-red'>*</span></b></label>
			<div class='col-sm-5'>    
				<input type='hidden' name='no_ipp' id='no_ipp' value='<?=$no_ipp;?>'>
				<?php
					echo form_input(array('type'=>'file', 'id'=>'excel_file','name'=>'excel_file','class'=>'form-control-file','autocomplete'=>'off','placeholder'=>'Supplier Name'));											
				?>
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-5'>
				<button type='button' id='import_data2' class='btn btn-info'>Upload Template</button>	
			</div>
		</div>
	</div>
</div>

<?php if(!empty($detail)){?>

<div class="box box-success">
	<div class="box-header">
		<h3 class="box-title">Check Upload Spool</h3>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class='text-center' width='7%'>Spool</th>
					<th class='text-center' width='10%'>Id Part</th>
					<th class='text-center' width='12%'>Id Product Cust</th>
					<th class='text-center' width='13%'>Nama Product</th>
					<th class='text-center' width='7%'>Dim 1</th>
					<th class='text-center' width='7%'>Dim 2</th>
					<th class='text-center' width='7%'>Thickness</th>
					<th class='text-center' width='8%'>Length/Sudut</th>
					<th class='text-center' width='5%'>SR/LR</th>
					<th class='text-center' width='9%'>Delivery Date</th>
					<th class='text-center' width='12%'>Keterangan</th>
					<th class='text-center' width='3%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no = 0;
				foreach($detail AS $val => $valx){
					$no++;
				?>
					<tr>
						<td>
							<input type='hidden' name='detail[<?=$val?>][id]' class='form-control input-sm text-center' value='<?=$valx['id'];?>' disabled>
							<input type='text' name='detail[<?=$val?>][spool]' class='form-control input-sm text-center' value='<?=$valx['spool'];?>' disabled>
						</td>
						<td><input type='text' name='detail[<?=$val?>][id_spool]' class='form-control input-sm' value='<?=$valx['id_spool'];?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][id_product]' class='form-control input-sm' value='<?=$valx['id_product'];?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][nm_product]' class='form-control input-sm' value='<?=$valx['nm_product'];?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][d1]' class='form-control input-sm text-right maskMoney' value='<?=number_format($valx['d1']);?>' disabled data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>
						<td><input type='text' name='detail[<?=$val?>][d2]' class='form-control input-sm text-right maskMoney' value='<?=number_format($valx['d2']);?>' disabled data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>
						<td><input type='text' name='detail[<?=$val?>][thickness]' class='form-control input-sm text-right maskMoney' value='<?=$valx['thickness'];?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][length_sudut]' class='form-control input-sm text-right maskMoney' value='<?=number_format($valx['length_sudut']);?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][sr_lr]' class='form-control input-sm text-center' value='<?=$valx['sr_lr'];?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][delivery_date]' class='form-control input-sm text-center datepicker' value='<?=$valx['delivery_date'];?>' disabled></td>
						<td><input type='text' name='detail[<?=$val?>][keterangan]' class='form-control input-sm' value='<?=$valx['keterangan'];?>' disabled></td>
						<td>
							<?php if($valx['status'] == 'N'){ ?>
							<button type='button' class='btn btn-sm btn-danger deletePermanent' data-id='<?=$valx['id'];?>' title='Delete' style='min-width:70px;'><i class='fa fa-close'></i></button>
							<?php } ?>
						</td>
					</tr>
				<?php 
				}
				?>
				<tr id='add_<?=$val?>'>
					<td></td>
					<td align='left' colspan='11'><button type='button' class='btn btn-sm btn-success addPart' title='Add' style='min-width:70px;'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>
				</tr>
			</tbody>
		</table>
		<button type='button' id='save_new_spool' class='btn btn-success' style='margin-top:10px; float:right;'>Save</button>
	</div>
</div>

<?php } ?>

<div class="box box-success">
	<div class="box-header">
		<h3 class="box-title">Download Template</h3>
	</div>
	<div class="box-body">
		<button type='button' id='download' class='btn btn-warning'>Download Template Excell</button>
	</div>
</div>
<style>
	.datepicker{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.maskMoney').maskMoney();
		$('.datepicker').datepicker({
			showButtonPanel: true,
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
	});
</script>