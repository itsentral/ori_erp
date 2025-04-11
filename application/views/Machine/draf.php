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
		<table class="table table-bordered table-striped" id="my-grid">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-center">BQ Number</th>
					<th class="text-center">IPP Number</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Project</th>
					<th class="text-center">BQ By</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView">
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
	<!-- modal -->
	<div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:95%; '>
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
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script>
	$(document).ready(function(){
		DataTables();
		
		// swal({
			// title	: "Development Process",
			// text	: "Edit hanya bisa save draf lagi, belum bisa save BQ...",						
			// type	: "warning"
		// });
	});
	
	$(document).on('click', '#detailBQ', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ IN DRAF ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailBQDraf/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#editBQ', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>EDIT STRUCTURE BQ IN DRAF ["+$(this).data('id_bq')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalEditBQDraf/'+$(this).data('id_bq'));
		$("#ModalView2").modal();
	});
	
	$(".numberOnly").on("keypress keyup blur",function (event) {    
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
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
				url : base_url +'index.php/'+active_controller+'/getDataJSONDraf',
				type: "post",
				data: function(d){
					// d.kode_partner = $('#kode_partner').val()
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
