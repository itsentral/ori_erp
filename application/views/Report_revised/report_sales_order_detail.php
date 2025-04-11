<?php
$id_bq = $this->uri->segment(3);

?>

<div class="box-body" style="width:100%;">
    <input type='hidden' name='id_bq' id='id_bq' value='<?=$id_bq;?>'>
    <table class="table table-bordered table-striped" id="my-grid3" width='100%'>
        <thead>
            <tr class='bg-blue'>
                <th class="text-center" width='150px'>product</th>
                <th class="text-center" width='370px'>id_product</th>
                <th class="text-center no-sort" width='80'>series</th>
                <th class="text-center no-sort">diameter_1</th>
                <th class="text-center no-sort">diameter_2</th>
                <th class="text-center no-sort">qty</th>
                <th class="text-center no-sort">est_material</th>
                <th class="text-center no-sort">est_cost_material</th>
                <!-- <th class="text-center no-sort">unit_price</th>
                <th class="text-center no-sort">total_price</th> -->
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
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
	$(document).ready(function(){
        var id_bq = $('#id_bq').val();
        DataTables_so(id_bq);
		swal.close();
	});
</script>