<?php
$tanggal = $this->uri->segment(3);

$qSupplier	= "	SELECT * FROM laporan_per_hari WHERE `date` = '".$tanggal."' ";
$row		= $this->db->query($qSupplier)->result_array();
?>

<div class="box-body" style="width:100%;">
    <input type='hidden' name='tanggal' id='tanggal' value='<?=$tanggal;?>'>
    <table class="table table-bordered table-striped" id="my-grid2">
        <thead>
            <tr class='bg-blue'>
                <th rowspan='2' class="text-center valignmid no-sort" width='10px'>#</th>
                <th rowspan='2' class="text-center valignmid" width='80px'>IPP</th>
                <th rowspan='2' class="text-center valignmid" width='80px'>SO</th>
                <th rowspan='2' class="text-center valignmid" width='100px'>ID Category</th>
                <th rowspan='2' class="text-center valignmid" width='350px'>ID Product</th>
                <th rowspan='2' class="text-center valignmid">No SPK</th>
                <th rowspan='2' class="text-center valignmid">Dim 1</th>
                <th rowspan='2' class="text-center valignmid">Dim 2</th>
                <th rowspan='2' class="text-center valignmid">Pressure</th>
                <th rowspan='2' class="text-center valignmid">Liner</th>
                <th rowspan='2' class="text-center valignmid">Qty</th>
                <th rowspan='2' class="text-center valignmid">Revenue</th>
                <th rowspan='2' class="text-center valignmid no-sort">Est Material (kg)</th>
                <th rowspan='2' class="text-center valignmid no-sort">Est Price ($)</th>
                <th rowspan='2' class="text-center valignmid no-sort">Aktual Material (kg)</th>
                <th rowspan='2' class="text-center valignmid no-sort">Aktual Price ($)</th>
				<th class="text-center no-sort valignmid" rowspan='2'>Direct Labour</th>
				<th class="text-center no-sort valignmid" rowspan='2'>Indirect Labour</th>
				<th class="text-center no-sort valignmid" rowspan='2'>Consumable</th>
				<th class="text-center no-sort valignmid" colspan='5'>FOH</th>
				<th class="text-center no-sort valignmid" rowspan='2'>Sales & Marketing</th>
				<th class="text-center no-sort valignmid" rowspan='2'>Umum & Admin</th>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center no-sort valignmid">Machine Cost</th>
				<th class="text-center no-sort valignmid">Mold mandril Cost</th>
				<th class="text-center no-sort valignmid">Depreciation FOH</th>
				<th class="text-center no-sort valignmid">Factory Overhead</th>
				<th class="text-center no-sort valignmid">Salary Factory Management</th>
			</tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
	$(document).ready(function(){
        var tanggal = $('#tanggal').val();
        DataTables2(tanggal);
		swal.close();
	});

    function DataTables2(tanggal=null){
		var dataTable = $('#my-grid2').DataTable({
            "scrollX": true,
			"scrollY": "500",
			"scrollCollapse" : true,
			// "autoWidth": true,
			"serverSide": true,
			"processing": true,
			"stateSave" : true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSONDetail', 
				type: "post",
				data: function(d){
					d.tanggal = $('#tanggal').val()
                    // d.tahun = $('#tahun').val()
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
</script>