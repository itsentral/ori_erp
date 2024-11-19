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
			<div class='col-sm-2'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
			<div class='col-sm-10'>
				<button type='button' class='btn btn-sm btn-success' id='btn_download'><i class='fa fa-file-excel-o'></i> Download</button>
			</div>
		</div>
        <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Kode Spool</th>
                    <th class="text-center">No Drawing</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">WIP</th>
                    <th class="text-center">Finish Good</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})

		var date_filter = $('#date_filter').val();
		DataTables2(date_filter);

		$(document).on('change','#date_filter', function(e){
			e.preventDefault();
			var date_filter = $('#date_filter').val();
			DataTables2(date_filter);
		});

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
					var date_filter 	= $('#date_filter').val();
					DataTables2(date_filter);
				}
			}
		});
	});

	$(document).on('click', '#btn_download', function(e){
		e.preventDefault();
		var date_filter 	= $('#date_filter').val();
		var date_filter_ 	= 0;
		if(date_filter != ''){
			var date_filter_ 	= $('#date_filter').val();
		}
		var Links		= base_url + active_controller+'/ExcelWIPSpool/'+date_filter_;
		window.open(Links,'_blank');
	});

	function DataTables2(date_filter=null){
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
				url : base_url + active_controller+'/server_side_spool',
				type: "post",
				data: function(d){
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
