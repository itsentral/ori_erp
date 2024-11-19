<?php
$id_bq = $this->uri->segment(3);

?>

<div class="box-body" style="width:100%;">
    <input type='hidden' name='id_bq' id='id_bq' value='<?=$id_bq;?>'>
    <table class="table table-bordered table-striped" id="my-grid2" width='100%'>
        <thead>
            <tr class='bg-blue'>
                <th class="text-center">Option</th> 
				<th class="text-center">Tgl Release</th>
                <th class="text-center">IPP</th>
                <th class="text-center">Customer</th>
                <th class="text-center no-sort">Rev</th>
				<th class="text-center no-sort">Perubahan</th>
				<th class="text-center no-sort">Permintaan Revisi</th>
                <th class="text-center no-sort">COGS (USD)</th>
                <th class="text-center no-sort">Est Material (Kg)</th>
				<!--
                <th class="text-center no-sort">est_cost_material</th>
                <th class="text-center no-sort">direct_labour</th>
                <th class="text-center no-sort">indirect_labour</th>
                <th class="text-center no-sort">machine</th>
                <th class="text-center no-sort">mould_mandrill</th>
                <th class="text-center no-sort">consumable</th>
                <th class="text-center no-sort">foh_consumable</th>
                <th class="text-center no-sort">foh_depresiasi</th>
                <th class="text-center no-sort">biaya_gaji_non_produksi</th>
                <th class="text-center no-sort">biaya_non_produksi</th>
                <th class="text-center no-sort">biaya_rutin_bulanan</th>
				-->
            </tr> 
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
	$(document).ready(function(){
        var id_bq = $('#id_bq').val();
        DataTables_engine(id_bq);
		swal.close();
	});
</script>