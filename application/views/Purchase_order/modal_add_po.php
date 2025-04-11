

<div class="box box-primary">
    <div class="box-body">
        <br>
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Supplier Name</b></label>
            <div class='col-sm-4'>              
                <select id='id_supplier' name='id_supplier[]' class='form-control input-sm chosen-select' multiple>
                    <?php
                        foreach($supList AS $val => $valx){
                            echo "<option value='".$valx['id_supplier']."'>".strtoupper($valx['nm_supplier'])."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <?php
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Create PO','content'=>'Create','id'=>'savePO')).' ';
		?>
        <br><br>
        <div class="box box-success">
            <br>
            <table class="table table-bordered table-striped" id="my-grid3" width='100%'>
                <thead>
                    <tr class='bg-blue'>
                        <th class="text-center no-sort" width='3%'>#</th>
                        <th class="text-center" width='7%'>No PR</th> 
                        <th class="text-center" width='7%'>Tanggal PR</th>
                        <th class="text-center">Material Name</th>
                        <th class="text-center" width='10%'>Category</th>
                        <th class="text-center" width='6%'>MOQ</th>
                        <th class="text-center" width='6%'>Qty (Kg)</th>
						<th class="text-center" width='7%'>Tanggal<br>Dibutuhkan</th>
						<th class="text-center" width='7%'>Request By</th>
						<th class="text-center" width='10%'>Request Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
	</div>
</div>
<style>
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
    swal.close();
    $(document).ready(function(){
        DataTables3();
		$('.chosen-select').chosen();
	});
	
	$(document).on('click','#back', function(){
		window.location.href = base_url + active_controller + '/perbandingan';
	});
</script>