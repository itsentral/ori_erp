

<div class='note'>
	<p>
		<strong>Info!</strong><br> 
		<span style='color:green;'><b>1. CHECKLIST JIKA MEMILIH TYPE SPOOL.</b></span><br>
		<span style='color:green;'><b>2. DOWNLOAD TEMPLETE EXCEL, KEMUDIAN ISI DATA <span style='color:red;'>(JANGAN MERUBAH STRUKTUR TABLE DI EXCEL)</span></b></span><br>
		<span style='color:green;'><b>3. UPLOAD FILE EXCEL TERSEBUT.</b></span><br>
		<span style='color:green;'><b>4. CHECK UPLOAD SPOOL, UPDATE JIKA TERJADI KESALAHAN.</b></span><br>
		<span style='color:green;'><b>5. SAVE CHOISE <span style='color:red;'>(DATA MASTER SPOOL TIDAK DAPAT DIRUBAH, HANYA BISA DITAMBAH)</span></b></span><br>
	</p>
</div>

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Make your choice</h3>
	</div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Spool ??? <span class='text-red'>*</span></b></label>
			<div class='col-sm-5'>
				<input type='hidden' name='no_ipp' id='no_ipp' value='<?=$no_ipp;?>'>
				<label class="checkbox-inline"><input type="checkbox" value="Y" id='spool' name='spool' <?=$checked;?>> Checklist jika spool.</label>
			</div>
		</div>
		<div class='form-group row choseSP'>		 	 
			<label class='label-control col-sm-2'><b>Upload File <span class='text-red'>*</span></b></label>
			<div class='col-sm-5'>              
				<?php
					echo form_input(array('type'=>'file', 'id'=>'excel_file','name'=>'excel_file','class'=>'form-control-file','autocomplete'=>'off','placeholder'=>'Supplier Name'));											
				?>
			</div>
		</div>
		<div class='form-group row choseSP'>		 	 
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-5'>
				<button type='button' id='import_data' class='btn btn-info'>Upload Template</button>	
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-10'>
				<button type='button' id='save_category' class='btn btn-primary' style='float:right;'>Save Choice</button>	
			</div>
		</div>
	</div>
</div>



<div class="box box-success choseSP">
	<div class="box-header">
		<h3 class="box-title">Check Upload Spool</h3>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class='text-center' width='7%'>Spool</th>
					<th class='text-center' width='10%'>Id Part</th>
					<th class='text-center' width='13%'>Id Product Cust</th>
					<th class='text-center' width='13%'>Nama Product</th>
					<th class='text-center' width='7%'>Dim 1</th>
					<th class='text-center' width='7%'>Dim 2</th>
					<th class='text-center' width='7%'>Thickness</th>
					<th class='text-center' width='8%'>Length/Sudut</th>
					<th class='text-center' width='5%'>SR/LR</th>
					<th class='text-center' width='9%'>Delivery Date</th>
					<th class='text-center' width='14%'>Keterangan</th>
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
							<input type='hidden' name='detail[<?=$val?>][id]' class='form-control input-sm text-center' value='<?=$valx['id'];?>'>
							<input type='text' name='detail[<?=$val?>][spool]' class='form-control input-sm text-center' value='<?=$valx['spool'];?>'>
						</td>
						<td><input type='text' name='detail[<?=$val?>][id_spool]' class='form-control input-sm' value='<?=$valx['id_spool'];?>'></td>
						<td><input type='text' name='detail[<?=$val?>][id_product]' class='form-control input-sm' value='<?=$valx['id_product'];?>'></td>
						<td>
						<select name='detail[<?=$val?>][nm_product]' class='form-control chosen-select'>
							<option value='0'>Select Again<option>
							<?php
							foreach($product AS $vap => $vapx){
								$sel = ($vapx['product_parent'] == $valx['nm_product'])?'selected':'';
								echo "<option value='".$vapx['product_parent']."' ".$sel.">".strtoupper($vapx['product_parent'])."</option>";
							}
							?>
						</select>
						</td>
						<td><input type='text' name='detail[<?=$val?>][d1]' class='form-control input-sm text-right maskMoney' value='<?=number_format($valx['d1']);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>
						<td><input type='text' name='detail[<?=$val?>][d2]' class='form-control input-sm text-right maskMoney' value='<?=number_format($valx['d2']);?>' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>
						<td><input type='text' name='detail[<?=$val?>][thickness]' class='form-control input-sm text-right maskMoney' value='<?=$valx['thickness'];?>'></td>
						<td><input type='text' name='detail[<?=$val?>][length_sudut]' class='form-control input-sm text-right maskMoney' value='<?=number_format($valx['length_sudut']);?>'></td>
						<td><input type='text' name='detail[<?=$val?>][sr_lr]' class='form-control input-sm text-center' value='<?=$valx['sr_lr'];?>'></td>
						<td><input type='text' name='detail[<?=$val?>][delivery_date]' class='form-control input-sm text-center datepicker' value='<?=$valx['delivery_date'];?>' readonly></td>
						<td><input type='text' name='detail[<?=$val?>][keterangan]' class='form-control input-sm' value='<?=$valx['keterangan'];?>'></td>
					</tr>
				<?php 
				}
				?>
			</tbody>
		</table>
		<button type='button' id='update_spool' class='btn btn-success' style='margin-top:10px; float:right;'>Update Spool</button>	
	</div>
</div>



<div class="box box-success choseSP">
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
		$('.chosen-select').chosen({width: '100%'});
		if ($('#spool').is(':checked')) {
			$('.choseSP').show();
		}
		else{
			$('.choseSP').hide();
		}
	});
</script>