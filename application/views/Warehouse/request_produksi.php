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
	<div class="box-body">
		<?php
		if(empty($uri_tanda)){
			?>
		<div class='in_ipp'>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Type Request</b></label>
				<div class='col-sm-6'>              
					<select id='no_ipp' name='no_ipp' class='form-control input-sm'>
						<option value='0'>Select Type Request</option>
						<option value='internal'>PEMAKAIAN INTERNAL</option>
						<option value='ancuran'>ANCURAN</option>
						<option value='reqnonso'>REQUEST NON SO</option>
						<?php
							foreach($no_ipp AS $val => $valx){
								$so_number = $valx['so_number'];
								$nm_project = get_name('production','project','no_ipp',$valx['no_ipp']);
								if($valx['id_product'] == 'tanki'){
									$so_number = $tanki->get_ipp_detail($valx['no_ipp'])['no_so'];
									$nm_project = $tanki->get_ipp_detail($valx['no_ipp'])['nm_project'];
								}
								echo "<option value='".$valx['no_ipp']."'>".strtoupper($so_number.' - '.$valx['no_ipp'].' - '.$nm_project)."</option>";
							}
							foreach ($no_ipp_deadstok as $key => $value) {
								$nm_project = get_name('production','project','no_ipp',$value['no_ipp']);
								echo "<option value='".$value['code_est']."'>EST DEADSTOK - ".strtoupper($value['no_so'].' - '.$value['no_ipp'].' - '.$nm_project)."</option>";
							}
						?>
					</select>
				</div>
			</div>	
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Dari Gudang</b></label>
			<div class='col-sm-4'>              
				<select id='gudang_before_r' name='gudang_before_r' class='form-control input-sm'>
					<option value='0'>Select Gudang</option>
					<?php
						foreach($pusat AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
					?>
				</select>
			</div>
			<div class='in_id'>	 	 
				<label class='label-control col-sm-2'><b>Ke Gudang</b></label>
				<div class='col-sm-4'>              
					<select id='gudang_after_r' name='gudang_after_r' class='form-control input-sm'>
						<option value='0'>Select Gudang</option>
						<?php
							foreach($subgudang AS $val => $valx){
								echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
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
			<div class='form-group row'>
				<div class='col-sm-6'></div>
				<div class='col-sm-2'>
				<select id='no_ipp2' name='no_ipp2' class='form-control input-sm chosen_select'>
					<option value='0'>All IPP Number</option>
					<?php
						foreach($list_ipp_req AS $val => $valx){
							echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp'])."</option>";
						}
					?>
				</select>
				</div>
				<div class='col-sm-2'>
				<select id='pusat' name='pusat' class='form-control input-sm chosen_select'>
					<option value='0'>All Gudang Dari</option>
					<?php
						foreach($pusat AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
					?>
				</select>
				</div>
				<div class='col-sm-2'>
				<select id='subgudang' name='subgudang' class='form-control input-sm chosen_select'>
					<option value='0'>All Gudang Ke</option>
					<?php
						foreach($subgudang AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
						}
					?>
				</select>
				</div>
			</div>
		<br><br>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">No SO</th>
					<th class="text-center">No SPK</th>
					<th class="text-center">Gudang Dari</th>
					<th class="text-center">Gudang Ke</th>
					<th class="text-center">Sum Material</th>
					<th class="text-center">Receiver</th>
					<th class="text-center">Date</th>
					<th class="text-center">Status</th>
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
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.chosen_select').chosen();
		var no_ipp 	= $('#no_ipp2').val();
		var pusat 	= $('#pusat').val();
		var subgudang 	= $('#subgudang').val();
		var uri_tanda 	= $('#uri_tanda').val();
		DataTables(no_ipp,pusat,subgudang,uri_tanda);
		
		$(document).on('change','#pusat, #no_ipp2, #subgudang', function(e){
			e.preventDefault();
			var no_ipp 	= $('#no_ipp2').val();
			var pusat 	= $('#pusat').val();
			var subgudang 	= $('#subgudang').val();
			var uri_tanda 	= $('#uri_tanda').val();
			DataTables(no_ipp,pusat,subgudang,uri_tanda);
		});
		
	});

	$(document).on('keyup', '.checkRequest', function(e){
		var nomor 	= $(this).data('no');
		var request = getNum($(this).val().split(",").join(""))
		var stock 	= getNum($('#stock_'+nomor).html().split(",").join(""))

		if(stock < request && stock > 0){
			$(this).val(number_format(stock,4))
		}

		if(stock < 0){
			$(this).val(0)
		}
	});

	$(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL REQUEST SUBGUDANG</b>");
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

	$(document).on('click', '.history', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>HISTORY PENGELUARAN GUDANG</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_history_subgudang/'+$(this).data('kode_trans')+'/'+$(this).data('tanda'),
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

	$(document).on('click', '.history_print', function(e){
		e.preventDefault();
		let kode_trans 	= $(this).data('kode_trans');
		let update_by 	= $(this).data('update_by');
		let update_date = $(this).data('update_date');

			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/print_request_check',
				data: {
					"kode_trans" 	: kode_trans,
					"update_by" 	: update_by,
					"update_date" 	: update_date
				},
				cache		: false,
				dataType	: 'json',
				success:function(data){
					console.log('Success!!!')
				},
				error: function() {
					swal({
					title		: "Error Message !",
					text		: 'Connection Timed Out ...',
					type		: "warning",
					timer		: 5000
					});
				}
			});
	});
	
	$(document).on('click', '.check', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>KONFIRMASI REQUEST MATERIAL</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_request_check/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);
				$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
				$('.chosen_select').chosen();
				$('.tanggal').datepicker({
					dateFormat : 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					minDate: 0
				});

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

	$(document).on('change', '#no_spk, #category_mat', function(e){
		e.preventDefault();
		let IPP_TANDA 		= $('#IPP_TANDA').val();
		let no_spk 			= $('#no_spk').val();
		let category_mat 	= $('#category_mat').val();
		let no_ipp 			= $('#no_ipp').val();
		let linkTanki 		= (IPP_TANDA != 'IPPT')?'get_detail_spk':'get_detail_spk_tanki';

		// if(IPP_TANDA != 'IPPT'){
			if(no_spk != '0'){
				// loading_spinner();
				$.ajax({
					type:'POST',
					url: base_url + active_controller+'/'+linkTanki,
					data: {
						"no_ipp" 		: no_ipp,
						"no_spk" 		: no_spk,
						"category_mat" 	: category_mat
					},
					cache		: false,
					dataType	: 'json',
					success:function(data){
						$("#budget_material_replace").html(data.option);
						$("#id_milik").val(data.id_milik);
						$("#product_text").val(data.product_name);
						$("#qty_spk").val(data.qty_spk);
						if(data.status == 2){
							swal({
								title	: "Process Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
						title		: "Error Message !",
						text		: 'Connection Timed Out ...',
						type		: "warning",
						timer		: 5000
						});
					}
				});
			}
			else{
				$("#budget_material_replace").html('');
			}
		// }
	});

	$(document).on('click', '.edit_material', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>EDIT REQUEST MATERIAL</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_request_edit/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 5000
				});
			}
		});
	});

    $(document).on('click', '#request', function(e){
		e.preventDefault();
		var gudang_before_r 	= $('#gudang_before_r').val();
		var gudang_after_r 	= $('#gudang_after_r').val();
		var no_ipp 			= $('#no_ipp').val();

		if( no_ipp == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'IPP Number Not Select, please input first ...',
			  type	: "warning"
			});
			$('#request').prop('disabled',false);
			return false;
		}

		if( gudang_before_r == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Gudang Dari Not Select, please input first ...',
			  type	: "warning"
			});
			$('#request').prop('disabled',false);
			return false;
		}
		
		if( gudang_after_r == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Gudang Ke Not Select, please input first ...',
			  type	: "warning"
			});
			$('#request').prop('disabled',false);
			return false;
		}

		loading_spinner();
		$("#head_title2").html("<b>TOTAL MATERIAL REQUEST "+no_ipp.toUpperCase()+"</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_request_produksi/'+no_ipp+'/'+gudang_before_r+'/'+gudang_after_r,
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
		// alert("Development");
		// return false;
		var no_spk 			= $('#no_spk').val();
		
		if( no_spk == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'No SPK is empty, please input first ...',
			  type	: "warning"
			});
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
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_request_produksi',
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
							window.location.href = base_url + active_controller+'/request_produksi';
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
	
	$(document).on('click', '#check_material', function(){
		var berat 			= $('.maskM').val();
		var uri_tanda	 	= $('#uri_tanda').val();
		$('#check_material').prop('disabled',true);
		if( berat == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Request Check is empty, please input first ...',
			  type	: "warning"
			});
			$('#check_material').prop('disabled',false);
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
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/modal_request_check',
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
							
							if(uri_tanda == 'subgudang'){
								window.location.href = base_url + active_controller+'/request_produksi/subgudang';
							}
							else{
								window.location.href = base_url + active_controller+'/request_produksi';
							}
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
							$('#check_material').prop('disabled',false);
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
						$('#check_material').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#check_material').prop('disabled',false);
			return false;
			}
		});
	});

	$(document).on('click', '#edit_material', function(){
		var berat 			= $('.maskM').val();
		var uri_tanda	 	= $('#uri_tanda').val();
		if( berat == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Request Check is empty, please input first ...',
			  type	: "warning"
			});
			$('#edit_material').prop('disabled',false);
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
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/modal_request_edit',
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
							
							if(uri_tanda == 'subgudang'){
								window.location.href = base_url + active_controller+'/request_produksi/subgudang';
							}
							else{
								window.location.href = base_url + active_controller+'/request_produksi';
							}
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '#buat_request', function(){
		// alert("Development");
		// return false;
		let no_ippx = $(this).data('no_ipp')
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
					url			: base_url + active_controller+'/process_buat_request/'+no_ippx,
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
							window.location.href = base_url + active_controller+'/request_produksi';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('change','.changeMaterial', function(e){
		e.preventDefault();
		// var id_material = $(this).val();
		let gudang 	= $('#gudang_before').val()
		let id_material = $(this).parent().parent().find('.changeMaterial').val()
		let stockval 	= $(this).parent().parent().find('.stockval')
		let qtyval 		= $(this).parent().parent().find('.checkRequest')
		if(id_material != '0'){
			$.ajax({
				url: base_url +'outgoing/get_stock',
				type		: "POST",
				dataType	: 'json',
				data: {
					"id_material":id_material,
					"gudang":gudang,
				},
				cache		: false,
				success:function(data){
					stockval.html(number_format(data.stock,4));
					qtyval.val('');
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Timed Out ...',
					type				: "warning",
					timer				: 5000
					});
				}
			});
		}
		else{
			stockval.val('');
			qtyval.val('');
		}
	});

	$(document).on('keyup','.requestBlock', function(e){
		e.preventDefault();
		let sisaRequest = getNum($(this).parent().parent().find('.sisaRequest').html().split(",").join(""))
		let requestBlock = getNum($(this).val().split(",").join(""))
		console.log(sisaRequest)
		console.log(requestBlock)
		if(requestBlock > sisaRequest){
			$(this).val(sisaRequest)
		}
	});

	function DataTables(no_ipp=null,pusat=null,subgudang=null,uri_tanda=null){
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
				url : base_url + active_controller+'/server_side_request_produksi',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp,
					d.pusat = pusat,
					d.subgudang = subgudang,
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
	
	function DataTables2(no_ipp=null, pusat=null){
		var dataTable = $('#my-grid2').DataTable({
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
				url : base_url + active_controller+'/server_side_modal_request_produksi',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp,
					d.pusat = pusat
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

	$(document).on('click','.plus', function(){
		var no 		= $(this).data('id');
		var kolom	= parseFloat($(this).parent().parent().find("td:nth-child(1)").attr('rowspan')) + 1;

		let list_expired = $(this).parent().parent().find(".list_expired").html();
		// console.log(list_expired);
		
		$(this).parent().parent().find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4)").attr('rowspan', kolom);
		
		var Rows	= "<tr>";
			Rows	+= "<td align='center'><input type='text' name='detail["+no+"][detail]["+kolom+"][qty_oke]' data-no='"+no+"' class='form-control input-sm text-right autoNumeric'></td>";
			Rows	+= "<td align='center'><select name='detail["+no+"][detail]["+kolom+"][expired]' class='form-control text-center input-sm chosen_select list_expired'>"+list_expired+"</td>";
			Rows	+= "<td align='center' class='stockExp'></td>";
			Rows	+= "<td align='center'><input type='text' name='detail["+no+"][detail]["+kolom+"][check_keterangan]' data-no='"+no+"' class='form-control input-sm text-left'></td>";
			Rows	+= "<td align='center'>";
			Rows	+= "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='"+no+"'><i class='fa fa-trash'></i></button>";
			Rows	+= "</td>";
			Rows	+= "</tr>";
		// alert(Rows);
		$(this).parent().parent().after(Rows);
		
		$('.autoNumeric').autoNumeric();
		$('.chosen_select').chosen();
	});

	$(document).on('click','.delete', function(){
		var no 		= $(this).data('id');
		var kolom	= parseFloat($(".baris_"+no).find("td:nth-child(1)").attr('rowspan')) - 1;
		$(".baris_"+no).find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4)").attr('rowspan', kolom);
		
		$(this).parent().parent().remove();
	});
</script>
