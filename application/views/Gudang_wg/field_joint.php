<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<button type="button" class="btn btn-sm btn-success" id="btn_download" style="float:right;" title="Download"><i class="fa fa-file-excel-o"> &nbsp;Download</i></button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Sales Order</th>
                    <th class="text-center">No IPP</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Qc By</th>
                    <th class="text-center">Qc Date</th>
                    <th class="text-center no-sort">Option</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

 <!-- modal -->
 <div class="modal fade" id="ModalView2">
    <div class="modal-dialog"  style='width:75%; '>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="head_title2"></h4>
                </div>
                <div class="modal-body" id="view2">
                </div>
                <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary">Save</button>-->
                <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->	
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})
		DataTables2();

        $(document).on('click', '.check_real', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>Detail Material</b>");
            $("#view2").load(base_url + active_controller + '/detail_field_joint/'+$(this).data('kode_trans'));
            $("#ModalView2").modal();
        });

		$(document).on('click', '#btn_download', function(e){
			e.preventDefault();
			var Links		= base_url + active_controller+'/excel_field_joint';
			window.open(Links,'_blank');
		});
		
	});

	function DataTables2(status=null){
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
				url : base_url + active_controller+'/server_side_field_joint',
				type: "post",
				data: function(d){
					d.status = status
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
