<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<input type='hidden' id='uri_tanda' value='<?=$uri_tanda;?>'>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body"><br>
		<?php
		if(empty($uri_tanda)){
			?>
			<div class='form-group row'>
				<div class='in_id'>	 	 
					<label class='label-control col-sm-2'><b>Destination Costcenter</b></label>
					<div class='col-sm-4'>              
						<select id='gudang_after' name='gudang_after' class='form-control input-sm' style='min-width:200px;'>
							<option value='0'>Select Costcenter</option>
							<?php
								foreach($subgudang AS $val => $valx){
									echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_costcenter'])."</option>";
								}
							?> 
						</select>
					</div>
				</div>
				<label class='label-control col-sm-2'><b>Date Transaksi</b></label>
				<div class='col-sm-4'>              
					<input type="text" name="tanggal_trans" id="tanggal_trans" class='form-control input-sm' data-role="datepicker_lost" readonly value='<?=date('Y-m-d');?>'>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Gudang</b></label>
				<div class='col-sm-4'>              
					<select id='gudang_before' name='gudang_before' class='form-control input-sm' style='min-width:200px;'>
						<!--<option value='0'>Select Gudang</option>-->
						<?php
							foreach($pusat AS $val => $valx){
								echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row' id='so_project'>
				<div class='in_id'>	 	 
					<label class='label-control col-sm-2'><b>Sales Order</b></label>
					<div class='col-sm-4'>              
						<select id='sales_order_project' name='sales_order_project' class='form-control input-sm' style='min-width:200px;'>
							<option value='0'>Select Sales Order</option>
							<?php
								foreach($so_number AS $val => $valx){
									echo "<option value='".$valx['so_number']."'>".strtoupper($valx['so_number'].' - '.$valx['project'])."</option>";
								}
							?> 
						</select>
					</div>
				</div>
			</div>
			<?php
				if($akses_menu['create']=='1'){
					echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'request'));
				}
			?>
			<br><br><br>
			<?php
		}
		?>
		<div class="box-tool pull-right">
			
			<select id='pusat' name='pusat' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Gudang</option>
				<?php
					foreach($pusat AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
					}
				?>
			</select>
			<select id='subgudang' name='subgudang' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Costcenter</option>
				<?php
					foreach($subgudang AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_costcenter'])."</option>";
					}
				?>
			</select>
		</div>
		<br><br>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">Date Transaction</th>
					<th class="text-center">Warehouse</th>
					<th class="text-center">Costcenter</th>
					<th class="text-center">Qty</th>
					<th class="text-center">Receiver</th>
					<th class="text-center">Create Date</th>
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
<style>
	#tanggal_trans{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.autoNumeric').autoNumeric();
		$('#so_project').hide()
		
		var pusat 		= $('#pusat').val();
		var subgudang 	= $('#subgudang').val();
		var uri_tanda 	= $('#uri_tanda').val();
		DataTables(pusat,subgudang,uri_tanda);
		
		$(document).on('change','#pusat, #subgudang', function(e){
			e.preventDefault();
			var pusat 		= $('#pusat').val();
			var subgudang 	= $('#subgudang').val();
			var uri_tanda 	= $('#uri_tanda').val();
			DataTables(pusat,subgudang,uri_tanda);
		});

		$(document).on('change','#gudang_after', function(e){
			e.preventDefault();
			var gudang 		= $(this).val();
			if(gudang == '17'){
				$('#so_project').show()
			}
			else{
				$('#so_project').hide()
			}
			
		});
		
	});

	$(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL TRANSACTION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_adjustment/'+$(this).data('kode_trans')+'/'+$(this).data('tanda'),
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

    $(document).on('click', '#request', function(e){
		e.preventDefault();
		var gudang_before 	= $('#gudang_before').val();
		var gudang_after 	= $('#gudang_after').val();
		var tanggal_trans 	= $('#tanggal_trans').val();
		var sales_order_project = $('#sales_order_project').val()

		if( gudang_before == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Gudang Dari Not Select, please input first ...',
			  type	: "warning"
			});
			$('#request').prop('disabled',false);
			return false;
		}
		
		if( gudang_after == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Costcenter Not Select, please input first ...',
			  type	: "warning"
			});
			$('#request').prop('disabled',false);
			return false;
		}

		loading_spinner();
		$("#head_title2").html("<b>OUTGOING TRANSACTION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_outgoing',
			data: {
				"sales_order_project" : sales_order_project,
				"tanggal_trans" : tanggal_trans,
				"gudang_before" : gudang_before,
				"gudang_after" 	: gudang_after
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

	$(document).on('click', '#request_material', function(){

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
					url			: base_url + active_controller+'/process_outgoing',
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
							window.location.href = base_url + active_controller+'/outgoing';
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

	function DataTables(pusat=null,subgudang=null,uri_tanda=null){
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
				url : base_url + active_controller+'/server_side_outgoing',
				type: "post",
				data: function(d){
					d.pusat = pusat,
					d.subgudang = subgudang,
					d.tanda = 'request subgudang',
					d.uri_tanda = uri_tanda
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
	
	$(document).on('change','.qty, .ket', function(){
		var no 				= $(this).data('no');
		var id 				= $('#id_'+no).val();
		var sudah_request 	= getNum($('#sudah_request_'+no).val().split(",").join(""));
		var ket_request		= $('#ket_request_'+no).val();
		var stock			= getNum($('#stock_'+no).html().split(",").join(""));

		if(stock < 0){
			$('#sudah_request_'+no).val(0);
			sudah_request = 0
		}

		if(sudah_request > stock && stock >= 0){
			$('#sudah_request_'+no).val(number_format(stock,2));
			sudah_request = stock
		}
		
		$.ajax({
				url			: base_url + active_controller+'/save_temp_mutasi',
				type		: "POST",
				data		: {
					"id" 			: id,
					"sudah_request" : sudah_request,
					"ket_request" 	: ket_request
				},
				cache		: false
		});
	});
	
	function DataTables2(pusat=null, category=null){
		var dataTable = $('#my-grid2').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_modal_outgoing',
				type: "post",
				data: function(d){
					d.pusat = pusat,
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

	
</script>
