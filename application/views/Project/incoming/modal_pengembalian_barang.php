
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box-body">
		<input type="hidden" name='no_po' id='no_po' value='<?= $no_po;?>'>
		<input type="hidden" name='gudang' id='gudang' value='<?= $gudang;?>'>
		<input type="hidden" name='tanggal_trans' id='tanggal_trans' value='<?= $tanggal_trans;?>'>
		<input type="hidden" name='pic' id='pic' value='<?= $pic;?>'>
		<input type="hidden" name='note' id='note' value='<?= $note;?>'>
		<input type="hidden" name='no_ros' id='no_ros' value='<?= $no_ros;?>'>
		<table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>Tanggal Transaksi</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?= date('d F Y',strtotime($tanggal_trans));?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>PIC</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$pic;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle; height:10px;'></td>
				<td class="text-left" style='vertical-align:middle;'></td>
				<td class="text-left" style='vertical-align:middle;'></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Surat Jalan <span class='text-danger'>*</span></td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><input type="text" name='no_surat_jalan' id='no_surat_jalan' style="width:250px;" class='form-control input-sm'></td>
			</tr>
			
		</thead>
	</table><br>
		<table id="my-grid2" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='20%'>Category</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Code</th>
					<th class="text-center" style='vertical-align:middle;'>Nama Barang</th>
					<th class="text-center" style='vertical-align:middle;' width='13%'>Spec</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Brand</th>
					<th class="text-center" style='vertical-align:middle;' width='7%'>Qty</th> 
					<th class="text-center" style='vertical-align:middle;' width='17%'>Keterangan</th> 
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'ProcessPengembalianBarang'));
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
		DataTables2();
    });
</script>