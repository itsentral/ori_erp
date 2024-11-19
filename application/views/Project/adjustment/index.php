<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class='form-group row'>
			<div class='col-sm-10'>
				<?php if($akses_menu['create']=='1'){ ?>
				<a href="<?php echo site_url($this->uri->segment(1).'/add') ?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add</a>
				<?php } ?>
				<button type='button' class="btn btn-primary btn-sm" title="Download Excel" id='download-excel'> <i class="fa fa-file-excel-o">&nbsp;</i>&nbsp;Download Excel</button>
			</div>
			<div class='col-sm-2'>
				<select name='type' id='type' class='form-control input-sm'>
					<option value='0'>ALL ADJUSTMENT</option>
					<option value='plus'>PLUS</option>
					<option value='minus'>MINUS</option>
					<option value='mutasi'>MUTASI</option>
				</select>
			</div>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">Type</th>
					<th class="text-center">Gudang Dari</th>
					<th class="text-center">Gudang Ke</th>
					<th class="text-center">Material Name</th>
					<th class="text-center">Spec</th>
					<th class="text-center">Qty</th>
					<th class="text-center">PIC</th>
					<th class="text-center">No BA</th>
					<th class="text-center">Ket</th>
					<th class="text-center">Created</th>
					<th class="text-center">Created Date</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
	<!-- modal -->
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:85%; '>
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
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>

<script>
	$(document).ready(function(){
		var type = $('#type').val();
		DataTables(type);
		
		$(document).on('change','#type', function(e){
			e.preventDefault();
			var type = $('#type').val();
			DataTables(type);
		});
	});
	
	$(document).on('click', '#download-excel', function(e){
		e.preventDefault();
		var type 		= $('#type').val();
		var material 	= $('#material').val();
		window.open(base_url + active_controller +'/excel_adjustment/'+type, '_blank');
	});

	$(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>PERMINTAAN PENGECEKAN</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_adjustment/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

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

    

	function DataTables(type = null){
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
			"aaSorting": [[ 1, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_adjustment',
				type: "post",
				data: function(d){
					d.type = type
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
