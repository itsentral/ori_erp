<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class='form-group row'>
            <div class='col-sm-2'>
                <select name="status" id="status" class='form-control'>
                    <option value="pipe">Pipe</option>
                    <option value="fitting">Fitting</option>
                    <option value="cutting">Cutting</option>
					<option value="field_joint">Field Joint</option>
					<option value="spool">Spool</option>
                    <!-- <option value="tanki">Tanki</option> -->
                </select>
            </div>
			<div class='col-sm-2'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
			<div class='col-sm-8'>
				<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
			</div>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">No SO</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">Product</th>
                    <th class="text-center no-sort">Customer</th>
                    <th class="text-center no-sort">Project</th>
                    <th class="text-center no-sort">Spec</th>
					<th class="text-center no-sort">QTY</th>
					<th class="text-center no-sort">Value</th>
					<th class="text-center no-sort">Total Value</th>
					<th class="text-center no-sort">Kode Spool</th>
					<th class="text-center no-sort">No Drawing</th>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.datepicker{
		cursor:pointer;
	}
</style>
<script>
	$('input[type="text"][data-role="datepicker2"]').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth:true,
		changeYear:true,
		maxDate:'-1d',
		showButtonPanel: true,
		closeText: 'Clear',
			onClose: function (dateText, inst) {
			if ($(window.event.srcElement).hasClass('ui-datepicker-close'))
			{
				document.getElementById(this.id).value = '';
				var status 			= $('#status').val();
				var date_filter 	= $('#date_filter').val();
				DataTables2(status, date_filter);
			}
		}
	});

	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		var status 			= $('#status').val();
		var date_filter 	= $('#date_filter').val();
		var date_filter_ 	= 0;
		if(date_filter != ''){
			var date_filter_ 	= $('#date_filter').val();
		}
		var Links		= base_url + active_controller+'/excel_gudang_fg/'+date_filter_+'/'+status;
		window.open(Links,'_blank');
	});

	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})

		let status 		= $('#status').val();
		var date_filter = $('#date_filter').val();
		DataTables2(status, date_filter);

		$(document).on('change','#date_filter, #status', function(e){
			e.preventDefault();
			let status 		= $('#status').val();
			var date_filter = $('#date_filter').val();
			DataTables2(status, date_filter);
		});

		$(document).on('click', '.look_history', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title").html("<b>DETAIL</b>");
            $("#view").load(base_url + active_controller + '/detail_berat/'+$(this).data('kode_spk')+'/'+$(this).data('id_production_detail')+'/'+$(this).data('category')+'/'+$(this).data('qty')+'/'+status+'/'+$(this).data('length')+'/'+$(this).data('length_awal'));
            $("#ModalView").modal();
        });
	});


	function DataTables2(status=null, date_filter=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
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
				url : base_url + active_controller+'/server_side_fg',
				type: "post",
				data: function(d){
					d.status = status,
					d.date_filter = date_filter
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}
</script>
