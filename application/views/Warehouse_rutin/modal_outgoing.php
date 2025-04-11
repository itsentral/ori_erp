
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
	<br>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<input type="hidden" name='tanggal_trans' id='tanggal_trans' value='<?= $tanggal_trans;?>'>
	<input type="hidden" name='sales_order_project' id='sales_order_project' value='<?= $sales_order_project;?>'>
	<div class="box-tool pull-right">
		<select id='category' name='category' class='form-control input-sm chosen-select'>
			<option value='0'>All Category</option>
			<?php
				foreach($data_gudang AS $val => $valx){
					echo "<option value='".$valx['id']."'>".strtoupper($valx['category'])."</option>";
				}
			?>
		</select>
	</div>
	<br><br>
	<table id="my-grid2" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Category</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Spesifikasi</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Stock</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody></tbody>
	</table>
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'request_material'));
	?>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
	
	.chosen-container{
		min-width: 200px !important;
		text-align : left !important;
	}

</style> 
<script>
	$(document).ready(function(){
        swal.close();
		$('.maskM').maskMoney();
		$('.chosen-select').chosen({'width':'100%'});
		$('.autoNumeric').autoNumeric();
		
		var pusat 		= $('#gudang_before').val();
		var category 		= $('#category').val();
		DataTables2(pusat, category);
		
		$(document).on('change','#category', function(e){
			e.preventDefault();
			var pusat 		= $('#gudang_before').val();
			var category 		= $('#category').val();
			DataTables2(pusat, category);
		});
    });
</script>