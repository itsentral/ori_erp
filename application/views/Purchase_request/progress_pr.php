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
					<th class="text-center">No</th>
					<th class="text-center">Tgl PR Approved</th>
					<th class="text-center">No PR</th>
					<th class="text-center">Asal Permintaan</th>
					<th class="text-center">Untuk Kebutuhan</th>
					<th class="text-center" width='15%'>Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  <div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
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
</form>
<?php $this->load->view('include/footer'); ?>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<style>
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
</style>
<script>
	$(document).ready(function(){
        DataTables();
    });
	
	$(document).on('click', '.detailPR', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL PURCHASE REQUEST ["+$(this).data('no_pr')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_progress_pr/'+$(this).data('no_pr'),
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
    
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"oLanguage": {
				"sSearch": "<b>Search : </b>",
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
			"aaSorting": [[ 1, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_progress_pr',
				type: "post",
				// data: function(d){
					// d.gudang = $('#gudang').val()
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
