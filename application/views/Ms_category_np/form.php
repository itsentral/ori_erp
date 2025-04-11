<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$nama		= (!empty($data[0]->nama))?$data[0]->nama:'';
?>
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-primary"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>Category Name</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Category Name" value='<?=$nama;?>' required>
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