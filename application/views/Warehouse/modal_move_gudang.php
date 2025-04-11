
<form action="#" method="POST" id="form_move" enctype="multipart/form-data"> 
<div class="box box-primary">
    <input type="hidden" name='gudang1' id='gudang1' value='<?=$this->uri->segment(3);?>'>
    <input type="hidden" name='gudang2' id='gudang2' value='<?=$this->uri->segment(4);?>'>
    <?php
        echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Move Material','content'=>'Move Material','id'=>'moveMat')).' ';
    ?>
    <br>
    <div class="box-body">
        <br>
        <table class="table table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center no-sort" width='5%'>#</th>
                    <th class="text-center" width='20%'>Id Material</th> 
                    <th class="text-center">Material Name</th>
                    <th class="text-center" width='10%'>Qty Stock</th>
                    <th class="text-center" width='10%'>Qty Booking</th>
                    <th class="text-center" width='10%'>Qty Move</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
</div>
</form>

<script>
    swal.close();
    $(document).ready(function(){
        $('.maskM').maskMoney();
        var gudang1 = $("#gudang1").val();
        // alert(gudang1);
		DataTables2(gudang1);
	});
</script>