<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$nomor_po	= (!empty($data[0]->nomor_po))?$data[0]->nomor_po:'';
$id_customer= (!empty($data[0]->id_customer))?$data[0]->id_customer:'';
$keterangan = (!empty($data[0]->keterangan))?$data[0]->keterangan:'';
$tanggal_po = (!empty($data[0]->tanggal_po))?$data[0]->tanggal_po:'';
?>
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-primary"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>Nomor PO <span class='text-red'>*</span></b></label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="nomor_po" name="nomor_po" placeholder="Nomor PO" value='<?=$nomor_po;?>' required>
            </div>
        </div>
        <div class="form-group row">
			<label class='label-control col-sm-4'><b>Customer <span class='text-red'>*</span></b></label>
			<div class='col-sm-8'>
				<select name='id_customer' id='id_customer' class='form-control input-md chosen-select' required>
				 <option value=''>Select A Customer</option>
				 <?php
					foreach($customer AS $valx){
						$selected='';
						if($valx->id_customer==$id_customer) $selected=' selected';
						echo "<option value='".$valx->id_customer."'". $selected.">".strtoupper($valx->nm_customer)."</option>";
					}
				 ?>
				 </select>
			</div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Tanggal PO</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="tanggal_po" name="tanggal_po" placeholder="Tanggal PO" value='<?=$tanggal_po;?>' required>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label>Keterangan</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value='<?=$keterangan;?>' required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4"></div>
            <div class="col-md-8">
                <button type="button" class="btn btn-primary" name="save" id="save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<style>
.chosen-container.chosen-container-single {
    width: 400px !important; /* or any value that fits your needs */
}
</style>
<script>
    swal.close();
	$('.chosen-select').chosen({
		allow_single_deselect	: true,
		search_contains			: true,
		no_results_text			: 'No result found for : ',
		placeholder_text_single	: 'Select an option'
	});
	$('#tanggal_po').datepicker({
		dateFormat : 'yy-mm-dd'
	});
</script>