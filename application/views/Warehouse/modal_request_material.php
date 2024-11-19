
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box-body">
		<input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
		<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
		<input type="hidden" name='tanggal_trans' id='tanggal_trans' value='<?= $tanggal_trans;?>'>
		<h4>Tanggal Transaksi : <?= date('d F Y',strtotime($tanggal_trans));?></h4>
		<table id="my-grid2" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
					<th class="text-center" style='vertical-align:middle;'>Material Name</th>
					<th class="text-center" style='vertical-align:middle;' width='15%'>Stock Pusat (Kg)</th>
					<th class="text-center" style='vertical-align:middle;' width='15%'>Stock SubGudang (Kg)</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Request</th>
					<th class="text-center" style='vertical-align:middle;' width='20%'>Keterangan</th> 
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
</style> 
<script>
	$(document).ready(function(){
        swal.close();
		$('.maskM').maskMoney();
		
		var pusat 		= $('#gudang_before').val();
		var subgudang 		= $('#gudang_after').val();
		DataTables2(pusat,subgudang);
    });
</script>