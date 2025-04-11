<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Upload Template</h3>
    </div>
    <div class="box-body">
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Upload File <span class='text-red'>*</span></b></label>
            <div class='col-sm-5'>              
                <?php
                    echo form_input(array('type'=>'file', 'id'=>'excel_file','name'=>'excel_file','class'=>'form-control-file','autocomplete'=>'off','placeholder'=>'Supplier Name'));											
                ?>
            </div>
        </div>
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'></label>
            <div class='col-sm-5'>
                <button type='button' id='uploadEx' class='btn btn-primary'>Upload</button>	
            </div>
        </div>
    </div>
</div>