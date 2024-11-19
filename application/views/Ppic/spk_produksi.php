<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center" width='10%'>IPP</th>
					<th class="text-center" width='10%'>SO Number</th>
					<th class="text-center">Project</th>
					<th class="text-center" width='10%'>Date</th>
					<th class="text-center no-sort" width='13%'>Status</th>
					<th class="text-center no-sort" width='8%'>Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:90%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
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
		DataTables();
	});

	$(document).on('click', '.detail_spk', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>PRINT SPK PRODUKSI ["+$(this).data('id_produksi')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_spk/'+$(this).data('id_produksi'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 5000,
				});
			}
		});
	});

    $(document).on('click', '#btn_download', function(e){
        e.preventDefault();
        var id_produksi		= $(this).data('id_produksi');
        var Links		= base_url+'production/ExcelProduksi/'+id_produksi;
        window.open(Links,'_blank');
    });
    
    $(document).on('click', '.spk_mat_acc', function(e){
        e.preventDefault();
        var id_bq		= $(this).data('id_bq');
        var tanda		= $(this).data('tanda');
        var Links		= base_url+'production/spk_mat_acc/'+id_bq+'/'+tanda;
        window.open(Links,'_blank');
    });

    $(document).on('click', '.printMerge', function(e){
		e.preventDefault();
		var id_uniq		= $(this).data('id_uniq');
		var id_produksi		= $(this).data('id_produksi');
		var Links		= base_url + active_controller+'/print_spk_produksi_satuan/'+id_uniq+'/'+id_produksi;
		window.open(Links,'_blank');
	});
		
	function DataTables(){
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
				url : base_url + active_controller+'/server_side_spk_produksi',
				type: "post",
				// data: function(d){
				// 	d.menu_baru = menu_baru
				// },
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
