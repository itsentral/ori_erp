<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <div class='form-group row'>
            <div class='col-sm-12'>
                <table class="table table-bordered table-striped" id="my-grid2" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center">#</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">NO SO</th>
                            <th class="text-center">NO SPK</th>
                            <th class="text-center">PRODUCT</th>
                            <th class="text-center">JENIS TRANS</th>
                            <th class="text-center">ID TRANS</th>
                            <th class="text-center">NO TRANS</th>
                            <th class="text-center">KODE DELIVERY</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">NILAI IN CUSTOMER</th>
							<th class="text-center">Material Name</th>
							<th class="text-center">Qty/Berat</th>
                            <th class="text-center">Costbook</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#range_picker2').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

		$('#range_picker2').val('');

        let range2 = $('#range_picker2').val();
		var tgl_awal2 	= '0';
		var tgl_akhir2 	= '0';
		if(range2 != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal2 	= sPLT[0];
			var tgl_akhir2 	= sPLT[1];
		}
		DataTables2(tgl_awal2,tgl_akhir2);
	});

    $('#range_picker2').on('apply.daterangepicker', function(ev, picker) {
		let range = $('#range_picker2').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		DataTables2(tgl_awal,tgl_akhir);
	});

	$('#range_picker2').on('cancel.daterangepicker', function(ev, picker) {
		let range = $('#range_picker2').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		DataTables2(tgl_awal,tgl_akhir);
	});

	$(document).on('click', '#download_excel_header2', function(){
		let range = $('#range_picker2').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range == ''){
			alert('Range date wajib diisi !!!')
			return false
		}
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		var Links		= base_url + active_controller+'/excel_report_subgudang2/'+tgl_awal+'/'+tgl_akhir;
		window.open(Links,'_blank');
	});

    function DataTables2(tgl_awal=null,tgl_akhir=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/get_data_json_data_erp_incustomer',
				type: "post",
				data: function(d){
					d.tgl_awal = tgl_awal,
					d.tgl_akhir = tgl_akhir
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
