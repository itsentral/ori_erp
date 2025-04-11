<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<!-- <button type='button' class="btn btn-primary btn-md" title="Download Excel" style='float:right; margin-left:5px;' id='download-excel'> <i class="fa fa-file-excel-o">&nbsp;</i>&nbsp;Download Excel</button> -->

			<?php
				if($akses_menu['create']=='1' AND empty($uri_approve)){
			?>
				<a href="<?php echo site_url('mutation/add') ?>" style='float:right;' class="btn btn-md btn-success" id='btn-add'>
					<i class="fa fa-plus"></i> Add
				</a>
			<?php } ?>
			
			<!-- <br><br>
			<select name='material' id='material' class='form-control input-sm'>
				<option value='0'>ALL MATERIAL</option>
				<?php
				foreach($material as $row)
				{
					echo "<option value='".$row->id_material."'>".strtoupper($row->nm_material)."</option>";
				}
				?>
			</select> -->
            <input type="hidden" name='type' id='type' value='0'>
            <input type="hidden" name='material' id='material' value='0'>
            <input type="hidden" name='uri_approve' id='uri_approve' value='<?=$uri_approve;?>'>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center no-sort">Type</th>
					<th class="text-center no-sort">Gudang Dari</th>
					<th class="text-center no-sort">Gudang Ke</th>
					<th class="text-center no-sort">PIC</th>
					<th class="text-center no-sort">No BA</th>
					<!-- <th class="text-center">Sum Qty</th> -->
					<th class="text-center no-sort">By</th>
					<th class="text-center no-sort">Dated</th>
					<th class="text-center no-sort">#</th>
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
<script>
	$(document).ready(function(){
		var type = $('#type').val();
		var material = $('#material').val();
		var uri_approve = $('#uri_approve').val();
		DataTables(type,material,uri_approve);
		
		$(document).on('change','#type, #material', function(e){
			e.preventDefault();
			var type = $('#type').val();
			var material = $('#material').val();
			var uri_approve = $('#uri_approve').val();
			DataTables(type,material,uri_approve);
		});
	});
	
	$(document).on('click', '#download-excel', function(e){
		e.preventDefault();
		var type 		= $('#type').val();
		var material 	= $('#material').val();
		window.open(base_url + active_controller +'/excel_adjustment/'+type+'/'+material, '_blank');
	});

	$(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>PERMINTAAN PENGECEKAN MATERIAL</b>");
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

    

	function DataTables(type = null, material=null, uri_approve=null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
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
				url : base_url + active_controller +'/get_data_json_mutation',
				type: "post",
				data: function(d){
					d.type = type,
					d.material = material,
					d.uri_approve = uri_approve
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
