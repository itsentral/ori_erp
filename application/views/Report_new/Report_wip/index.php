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
        <h5>Data Group</h5>
        <div class='form-group row'>
            <div class='col-sm-4'>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control float-right text-center" id="range_picker2" placeholder='Select range date' readonly value='' style='width:300px;'>
                </div>
            </div>
            <div class='col-sm-8'>
                <button type='button' class='btn btn-md btn-primary' id='download_excel_header2'  title='Excel'>Download</i></button>
            </div>
        </div>
        <div class='form-group row'>
            <div class='col-sm-12'>
                <table class="table table-bordered table-striped" id="my-grid2" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center">#</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">NO SO</th>
                            <th class="text-center">PRODUCT</th>
                            <th class="text-center">ID TRANS</th>
                            <th class="text-center">NO TRANS</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">NILAI WIP</th>
                            <th class="text-center">MATERIAL</th>
                            <th class="text-center">WIP DIRECT</th>
                            <th class="text-center">WIP INDIRECT</th>
                            <th class="text-center">WIP CONSUMBALE</th>
                            <th class="text-center">WIP FOH</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <h5>Data Detail</h5>
        <div class='form-group row'>
            <div class='col-sm-4'>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control float-right text-center" id="range_picker" placeholder='Select range date' readonly value='' style='width:300px;'>
                </div>
            </div>
            <div class='col-sm-8'>
                <button type='button' class='btn btn-md btn-primary' id='download_excel_header'  title='Excel'>Download</i></button>
            </div>
        </div>
        <div class='form-group row'>
            <div class='col-sm-12'>
                <table class="table table-bordered table-striped" id="my-grid" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center">#</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">NO SO</th>
                            <th class="text-center">PRODUCT</th>
                            <th class="text-center">ID TRANS</th>
                            <th class="text-center">NO TRANS</th>
                            <th class="text-center">NM MATERIAL</th>
                            <th class="text-center">BERAT</th>
                            <th class="text-center">COSTBOOK</th>
                            <th class="text-center">TOTAL</th>
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
		$('#range_picker,#range_picker2').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

		$('#range_picker').val('');
		$('#range_picker2').val('');
        
		let range = $('#range_picker').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		DataTables(tgl_awal,tgl_akhir);

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
	
	$('#range_picker').on('apply.daterangepicker', function(ev, picker) {
		let range = $('#range_picker').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		DataTables(tgl_awal,tgl_akhir);
	});

	$('#range_picker').on('cancel.daterangepicker', function(ev, picker) {
		let range = $('#range_picker').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		DataTables(tgl_awal,tgl_akhir);
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

	$(document).on('click', '#download_excel_header', function(){
		let range = $('#range_picker').val();
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
		var Links		= base_url + active_controller+'/excel_report_subgudang/'+tgl_awal+'/'+tgl_akhir;
		window.open(Links,'_blank');
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
		
	function DataTables(tgl_awal=null,tgl_akhir=null){
		var dataTable = $('#my-grid').DataTable({
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
				url : base_url + active_controller+'/get_data_json_spk_produksi_progress',
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
				url : base_url + active_controller+'/get_data_json_spk_produksi_progress2',
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
