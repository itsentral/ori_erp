<?php
$id_bq = $this->uri->segment(3);

?>

<div class="box-body" style="width:100%;">
    <input type='hidden' name='id_bq' id='id_bq' value='<?=$id_bq;?>'>
    <table class="table table-bordered table-striped" id="my-grid2" width='100%'>
        <thead>
            <tr class='bg-blue'>
                <th class="text-center valign-middle" width='80px'>Option</th> 
                <th class="text-center valign-middle" width='120px'>no_ipp</th>
                <th class="text-center valign-middle" width='240px'>customer</th>
                <th class="text-center valign-middle no-sort">rev</th> 
				<th class="text-center valign-middle no-sort" width='240px'>Perubahan</th>
				<th class="text-center valign-middle no-sort" width='240px'>Permintaan Revisi</th>
				<th class="text-center valign-middle no-sort">total_project</th>
                <th class="text-center valign-middle no-sort">Material Est (Kg)</th>
                <th class="text-center valign-middle no-sort">Material Cost ($)</th>
                <th class="text-center valign-middle no-sort">Direct Labour ($)</th>
                <th class="text-center valign-middle no-sort">Indirect Labour ($)</th>
                <th class="text-center valign-middle no-sort">Machine Cost ($)</th>
                <th class="text-center valign-middle no-sort">Mould Mandrill FOH ($)</th>
                <th class="text-center valign-middle no-sort">Consumable ($)</th>
                <th class="text-center valign-middle no-sort">Consumable FOH ($)</th>
                <th class="text-center valign-middle no-sort">Depresiasi FOH ($)</th>
                <th class="text-center valign-middle no-sort">Gaji Non Produksi (FNA, PCH, HRGA) ($)</th>
                <th class="text-center valign-middle no-sort">Biaya Admin ($)</th>
                <th class="text-center valign-middle no-sort">Biaya Bulanan (Listrik, Air, Tlp, internet) ($)</th>
                <th class="text-center valign-middle no-sort">Enggenering Cost</th>
                <th class="text-center valign-middle no-sort">Packing Cost</th>
                <th class="text-center valign-middle no-sort">Trucking Cost</th>
                <th class="text-center valign-middle no-sort">Profit</th>
                <th class="text-center valign-middle no-sort">Allowance</th>
            </tr> 
        </thead>
        <tbody></tbody>
    </table>
</div>
<style>
	.valign-middle{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
        var id_bq = $('#id_bq').val();
        DataTables_costing(id_bq);
		swal.close();
	});
</script>