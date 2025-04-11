<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$coa		= (!empty($data[0]->coa))?$data[0]->coa:'';
$keterangan	= (!empty($data[0]->keterangan))?$data[0]->keterangan:'';
$category	= (!empty($data[0]->category))?$data[0]->category:'';
?>
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-primary"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>COA</label>
            </div>
            <div class="col-md-8">
				<?php
				$datacoa[0]	= 'Select An Option';
				echo form_dropdown('coa',$datacoa, $coa, array('id'=>'coa','class'=>'form-control chosen-select','required'=>'required'));
				?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Category</label>
            </div>
            <div class="col-md-8">
				<select id="category" name="category" required class="form-control chosen-select">
				<option value="">Select An Option</option>
				<?php
				foreach($datacategory as $keys => $vals){
					$sel='';
					if($category==$vals->id) $sel='selected';
					echo '<option value="'.$vals->id.'" '.$sel.'>'.$vals->nm_category.'</option>';
				}
				?>
				</select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>DEPARTEMAN / SUB DEPARTEMEN</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value='<?=$keterangan;?>'>
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
	$('.chosen-select').chosen({
		allow_single_deselect	: true,
		search_contains			: true,
		no_results_text			: 'No result found for : ',
		placeholder_text_single	: 'Select an option'
	});
</script>