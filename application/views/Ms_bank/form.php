<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$bank		= (!empty($data[0]->bank))?$data[0]->bank:'';
$rekening	= (!empty($data[0]->rekening))?$data[0]->rekening:'';
$nama		= (!empty($data[0]->nama))?$data[0]->nama:'';
?>
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-primary"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>Nama Bank</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="bank" name="bank" placeholder="Bank" value='<?=$bank;?>' required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Nomor Rekening</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="rekening" name="rekening" placeholder="Nomor Rekening" value='<?=$rekening;?>' required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Nama Pemilik Rekening</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Pemilik Rekening" value='<?=$nama;?>' required>
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
<script>
    swal.close();
</script>