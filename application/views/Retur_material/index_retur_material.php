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
		<div class="box box-success">
			<div class="box-body">
				<div class='in_ipp'>
					<div class='form-group row'>
						<label class='label-control col-sm-2'><b>PO No</b></label>
						<div class='col-sm-4'>
							<select id='no_ipp' name='no_ipp' class='form-control input-sm' style='min-width:200px;'>
							<option value="">Select PO Number</option>
								<?php
									foreach($no_po AS $val => $valx){
										$disabled = '';
										$label_add = '';
										if($valx['status1'] == 'N'){
											$disabled = 'disabled';
											$label_add = ' - (WAITING APPROVAL)';
										}
										echo "<option value='".$valx['no_po']."' ".$disabled.">".strtoupper($valx['no_po'].$label_add)." - ".$valx['nm_supplier']."</option>";
									}
								?>
							</select>
						</div>
					<label class='label-control col-sm-2'><b>Warehouse</b></label>
					<div class='col-sm-4'>
						<select id='gudang_before' name='gudang_before' class='form-control input-sm' style='min-width:200px;'>
							<option value='0'>Select Warehouse</option>
							<?php
								foreach($pusat AS $val => $valx){
									echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<?php
					if($akses_menu['create']=='1'){
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'modalDetail')).' ';
					}
				?>
			</div>
		</div>
		<hr>
		<div class="box-tool pull-right">
			<select id='no_po' name='no_po' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All PO Number</option>
				<?php
					foreach($list_po AS $val => $valx){
						echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp'])."</option>";
					}
				?>
			</select>
		</div>
		<br><br>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No Trans</th>
					<th class="text-center">No PO</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Keterangan</th>
					<th class="text-center">Tanggal Retur</th>
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
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>

<?php $this->load->view('include/footer'); ?>
</style>
<script>
	$(document).ready(function(){

		var no_po 	= $('#no_po').val();
		DataTables(no_po);
	});


    $(document).on('click', '#modalDetail', function(e){
		e.preventDefault();
		var gudang_before 	= $('#gudang_before').val();
		var no_ipp 			= $('#no_ipp').val();

		if( no_ipp == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'PO Number Not Select, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		if( gudang_before == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Warehouse Not Select, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		loading_spinner();
		var no_ipp 			= $('#no_ipp').val();
		var gudang_before 	= $('#gudang_before').val();

		$("#head_title2").html("<b>RETUR MATERIAL</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_retur_material',
			type		: "POST",
			data: {
				"no_ipp" 		: no_ipp,
				"gudang_before" : gudang_before,
			},
			cache		: false,
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

	$(document).on('click', '#saveINMaterial', function(){

		swal({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_retur_material',
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false,
					contentType	: false,
					success		: function(data){
						if(data.status == 1){
							swal({
									title	: "Save Success!",
									text	: data.pesan,
									type	: "success",
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller+'/index';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',
							type				: "warning",
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	function DataTables(no_po=null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_retur_material',
				type: "post",
				data: function(d){
					d.no_po = no_po
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
</script>
