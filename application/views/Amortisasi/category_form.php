<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$nm_category	= (!empty($data[0]->nm_category))?$data[0]->nm_category:'';
$coa	= (!empty($data[0]->coa))?$data[0]->coa:'';
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
                <label>COA</label>
            </div>
            <div class="col-md-8">
				<?php
				$dataaset[0]	= 'Select An Option';
				echo form_dropdown('coa',$dataaset, $coa, array('id'=>'coa','class'=>'form-control chosen-select','required'=>'required'));
				?>
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
</script>