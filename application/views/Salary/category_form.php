<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$nm_category	= (!empty($data[0]->nm_category))?$data[0]->nm_category:'';
$urutan	= (!empty($data[0]->urutan))?$data[0]->urutan:'';
?>
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-primary"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>Category Name</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="nm_category" name="nm_category" placeholder="Category Name" value='<?=$nm_category;?>' required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Sort No</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="urutan" name="urutan" placeholder="Sort No" value='<?=$urutan;?>' required>
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