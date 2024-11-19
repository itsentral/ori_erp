<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<button type='button' class='btn btn-sm btn-primary' id='download_excel_header' style='float:right;' title='Excel'><i class='fa fa-file-excel-o'> &nbsp;Download Excel</i></button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class='form-group row'>
			<div class='col-sm-4'>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">
							<i class="far fa-calendar-alt"></i>
						</span>
					</div>
					<input type="text" class="form-control float-right" id="range_picker" placeholder='Select range date' readonly value=''>
				</div>
			</div>	
		</div>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center" width='10%'>IPP</th>
					<th class="text-center" width='10%'>SO</th>
					<th class="text-center">Project</th>
					<th class="text-center" width='10%'>Tanggal Mulai</th>
					<th class="text-center" width='10%'>Tanggal Delivery</th>
					<th class="text-center no-sort" width='7%'>Progress</th>
					<th class="text-center no-sort" width='13%'>Status</th>
					<th class="text-center no-sort" width='7%'>Option</th>
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
		<div class="modal-dialog"  style='width:80%; '>
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
		$('#range_picker').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

		$('#range_picker').val('');
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

	$(document).on('click', '#print', function(e){
		e.preventDefault();
		var id_produksi		= $(this).data('id_produksi');
		var Links		= base_url + active_controller+'/print_progress_produksi/'+id_produksi;
		window.open(Links,'_blank');
	});

	$(document).on('click', '.download_excel', function(){
		var id_produksi		= $(this).data('id_produksi');
		var no_so		= $(this).data('no_so');
		
		var Links		= base_url + active_controller+'/progress_produksi_excel/'+id_produksi+'/'+no_so;
		window.open(Links,'_blank');
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
		var Links		= base_url + active_controller+'/data_progress_produksi_excel/'+tgl_awal+'/'+tgl_akhir;
		window.open(Links,'_blank');
	});
	
	$(document).on('click', '.detail_spk', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>PROGRESS PRODUKSI ["+$(this).data('id_produksi')+"]["+$(this).data('no_so')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_progress/'+$(this).data('id_produksi'),
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
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
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
				url : base_url + active_controller+'/server_side_spk_produksi_progress',
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
