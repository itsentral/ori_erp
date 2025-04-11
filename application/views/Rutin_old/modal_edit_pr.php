<?php
$code_group = (!empty($result))?$result[0]->id_material:''; 
$satuan = (!empty($result))?$result[0]->satuan:''; 
$purchase = (!empty($result))?number_format($result[0]->purchase):''; 
$tanggal = (!empty($result))?$result[0]->tanggal:''; 
$no_pengajuan = (!empty($result))?$result[0]->no_pengajuan:''; 

$disabled = '';
if(!empty($view)){
	$disabled = 'disabled';
}

?>
<div class="box-body">
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Nama Material <span class='text-red'>*</span></b></label>
			<div class='col-sm-10'>
				<select name='id_material' id='id_material' class='form-control input-md chosen-select' <?=$disabled;?>>
				 <?php
					foreach(get_list_rutin() AS $val => $valx){
						$selX	= ($valx['code_group'] == $code_group)?'selected':'';
						echo "<option value='".$valx['code_group']."' ".$selX.">(".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal'])).')  '.strtoupper($valx['material_name']." - ".$valx['spec'])."</option>";
					}
				 ?>
				 </select>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Qty Purchase <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
				echo form_input(array('id'=>'purchase','name'=>'purchase','class'=>'form-control input-md maskM','data-decimal'=>'.', 'data-thousand'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'',$disabled=>$disabled), $purchase);
				echo form_input(array('type'=>'hidden','id'=>'no_pengajuan','name'=>'no_pengajuan','class'=>'form-control input-md'), $no_pengajuan);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Unit <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<select name='satuan' id='satuan' class='form-control input-md chosen-select' <?=$disabled;?>>
				 <?php
					foreach(get_satuan() AS $val => $valx){
						$selX	= ($valx['id_satuan'] == $satuan)?'selected':'';
						echo "<option value='".$valx['id_satuan']."' ".$selX.">".strtoupper($valx['kode_satuan'])."</option>";
					}
				 ?>
				 </select>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Tgl Dibutuhkan <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
				 echo form_input(array('id'=>'tanggal','name'=>'tanggal','class'=>'form-control input-md tanggal','readonly'=>'readonly',$disabled=>$disabled),$tanggal);
				?>
			</div>
		</div>
		<?php
		if(empty($view)){
		?>
		<div class='form-group row'>
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-4'>
				<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px;','content'=>'Save','id'=>'save_edit'));
				?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<style>
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
	.tanggal{
		cursor: pointer;
	}	
</style>
<script>
    swal.close();
	$(document).ready(function(){
		$('.chosen-select').chosen();
		$('.maskM').maskMoney();
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			minDate: 0
		});
	});
</script>