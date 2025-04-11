<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
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
				<br>
				<input type="hidden" id='tandax' name='tandax'>
				<input type="hidden" id='category' name='category' value='<?=$category;?>'>
				<div class='in_ipp'>
					<div class='form-group row'>		 	 
						<label class='label-control col-sm-2'><b>Nomor PO <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>              
							<select id='no_ipp' name='no_ipp' class='form-control input-sm' style='min-width:200px;'>
								<option value='0'>Nomor PO</option>
								<?php
									foreach($no_po AS $val => $valx){
										echo "<option value='".$valx['no_po']."'>".strtoupper($valx['typ'].' - '.$valx['no_po'].' - '.$valx['ket_name'].', create by: '.strtolower($GET_USER[strtolower($valx['created_by'])]['nm_lengkap']))."</option>";
									}
								?>
							</select>
						</div>
						<label class='label-control col-sm-2'><b>PIC <span class='text-red'>*</span></b></label>
						<div class='col-sm-4'>
							<?php
							 echo form_input(array('id'=>'pic','name'=>'pic','class'=>'form-control input-md','placeholder'=>'PIC'));
							?>
						</div>
					</div>	
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>Warehouse <span class='text-red'>*</span></b></label>
					<div class='col-sm-4'>              
						<select id='gudang_before' name='gudang_before' class='form-control input-sm' style='min-width:200px;'>
							/<!--<option value='0'>Select Gudang</option>-->
							<?php
								foreach($pusat AS $val => $valx){
									echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
								}
							?>
						</select>
					</div>
					<label class='label-control col-sm-2'><b>Note</b></label>
					<div class='col-sm-4'>
						<?php
						 echo form_textarea(array('id'=>'note','name'=>'note','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Note'));
						?>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>Nomor ROS</b></label>
					<div class='col-sm-4'>              
						<select id='no_ros' name='no_ros' class='form-control input-sm' style='min-width:200px;'>
							<option value=''>Nomor ROS</option>
						</select>
					</div>
					<label class='label-control col-sm-2'><b>Date Transaksi</b></label>
					<div class='col-sm-4'>              
						<input type="text" name="tanggal_trans" id="tanggal_trans" class='form-control input-sm' data-role="datepicker_lost" readonly value='<?=date('Y-m-d');?>'>
					</div>
				</div>
				
				<?php
					if($akses_menu['create']=='1'){
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'modalDetail')).' ';
					}
				?>
			</div>
		</div>
		<div class="box-tool pull-right">
			<select id='no_po' name='no_po' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All PO Number</option>
				<?php
					foreach($list_po AS $val => $valx){
						echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp'])."</option>";
					}
				?>
			</select>
			<select id='gudang' name='gudang' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Warehouse</option>
				<?php
					foreach($data_gudang AS $val => $valx){
						echo "<option value='".$valx['id_gudang_ke']."'>".strtoupper($valx['kd_gudang_ke'])."</option>";
					}
				?>
			</select>
		</div>
		<br><br>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">Incoming Date</th>
					<th class="text-center">Warehouse</th>
					<th class="text-center">Sum Barang</th>
					<th class="text-center">PIC</th>
					<th class="text-center">Receiver</th>
					<th class="text-center">Supplier</th>
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
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>

<?php $this->load->view('include/footer'); ?>
<style>
	#tanggal_trans{
		cursor: pointer;
	}
</style>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		var no_po 	= $('#no_po').val();
		var gudang 	= $('#gudang').val();
		var category 		= $('#gudang_before').val();
		DataTables(no_po,gudang,category);
		
		$(document).on('change','#gudang, #no_po', function(e){
			e.preventDefault();
			var no_po 	= $('#no_po').val();
			var gudang 	= $('#gudang').val();
			var category 		= $('#gudang_before').val();
			DataTables(no_po,gudang,category);
		});
	});

	$(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL TRANSACTION</b>");
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

    $(document).on('click', '#modalDetail', function(e){
		e.preventDefault();
		var gudang_before 	= $('#gudang_before').val();
		var no_ipp 			= $('#no_ipp').val();
		var pic 			= $('#pic').val();
		var note 			= $('#note').val();
		var no_ros 			= $('#no_ros').val();
		var tanggal_trans 	= $('#tanggal_trans').val();
		var category 		= $('#category').val();

		if( no_ipp == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'PO Number Not Select, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		if( pic == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'PIC is empty, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		loading_spinner();
		var no_ipp 			= $('#no_ipp').val();
		var gudang_before 	= $('#gudang_before').val();
		$("#head_title2").html("<b>INCOMING TRANSACTION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_incoming',
			data: {
				"tanggal_trans" : tanggal_trans,
				"gudang_before" : gudang_before,
				"no_po" 		: no_ipp,
				"pic" 			: pic,
				"note" 			: note,
				"category" 		: category,
				"no_ros"		: no_ros
			},
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

	$(document).on('click', '#processAjust', function(){
		var gudang_before 	= $('#gudang_before').val();
		var no_ipp 			= $('#no_ipp').val();
		var gudang_after 	= $('#gudang_after').val();

		if( no_ipp == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'IPP Not Select, please input first ...',
			  type	: "warning"
			});
			$('#processAjust').prop('disabled',false);
			return false;
		}

		if( gudang_before == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Origin Warehouse Not Select, please input first ...',
			  type	: "warning"
			});
			$('#processAjust').prop('disabled',false);
			return false;
		}

		if( gudang_after == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Destination Warehouse Not Select, please input first ...',
			  type	: "warning"
			});
			$('#processAjust').prop('disabled',false);
			return false;
		}

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
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_adjustment',
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
							window.location.href = base_url + active_controller+'/material_adjustment';
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
					url			: base_url + active_controller+'/process_incoming',
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
							window.location.href = base_url + active_controller+'/incoming/<?=$uri_back;?>';
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

	$(document).on('click', '#moveMat', function(){
		
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
				var formData  	= new FormData($('#form_move')[0]);
				$.ajax({
					url			: base_url + active_controller+'/move_material',
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
							window.location.href = base_url + active_controller+'/material_adjustment';
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

	function DataTables(no_po=null,gudang=null,category=null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
				url : base_url + active_controller+'/server_side_incoming',
				type: "post",
				data: function(d){
					d.no_po = no_po,
					d.gudang = gudang,
					d.category = category
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

	function DataTables2(gudang1 = null){
		var dataTable = $('#my-grid2').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
				url : base_url + active_controller+'/server_side_move_gudang',
				type: "post",
				data: function(d){
					d.gudang1 = gudang1
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
	$(document).on('change', '#no_ipp', function(){
		var no_ipp	= $('#no_ipp').val();
		$('#no_ros').html('');
		$('#spinner').show();
		$.ajax({
			url			: base_url + 'warehouse/get_ros/'+no_ipp,
			type		: "POST",
			cache		: false,
			dataType	: 'json',
			processData	: false, 
			contentType	: false,
			success		: function(data){
				console.log(data);
				$('#no_ros').html(data.option).trigger("chosen:updated");
				$('#spinner').hide();
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
	});

	
</script>
