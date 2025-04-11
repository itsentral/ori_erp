<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$mata_uang	= (!empty($data[0]->mata_uang))?$data[0]->mata_uang:'USD';
$tanggal	= (!empty($data[0]->tanggal))?$data[0]->tanggal:date("Y-m-d");
$kurs		= (!empty($data[0]->kurs))?$data[0]->kurs:'1';
$currency	= $this->db->query("SELECT a.kode, a.mata_uang, a.negara FROM currency a ORDER BY a.mata_uang ASC")->result_array();

?>
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-primary"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>Currency</label>
            </div>
            <div class="col-md-8">
				<select name='mata_uang' id='mata_uang' class='form-control input-md'>
					<option value=''>Select An Currency</option>
				<?php
					foreach($currency AS $val => $valx){
						echo "<option value='".$valx['kode']."'".($valx['kode']==$mata_uang?' selected':'').">".strtoupper($valx['kode'])." - [".strtoupper($valx['negara'])."]</option>";
					}
				 ?>
				</select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Date</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Date" value='<?=$tanggal;?>' required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Rate</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control divide" id="kurs" name="kurs" placeholder="Rate" value='<?=$kurs;?>' required>
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
<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
<script>
    swal.close();
	$('.datepicker').datepicker({ dateFormat: 'yy-mm-dd'});
	$('.divide').divide();

</script>