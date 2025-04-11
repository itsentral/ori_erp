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
			<div class='form-group row'><br>
				<label class='label-control col-sm-2'><b>Dari Gudang</b></label>
				<div class='col-sm-4'>              
					<select id='gudang_before' name='gudang_before' class='form-control input-sm' style='min-width:200px;'>
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
						<select id='gudang_after' name='gudang_after' class='form-control input-sm' style='min-width:200px;'>
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
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Date Transaksi</b></label>
				<div class='col-sm-4'>              
					<input type="text" name="tanggal_trans" id="tanggal_trans" class='form-control input-sm' data-role="datepicker_lost" readonly value='<?=date('Y-m-d');?>'>
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
				<option value='0'>All Gudang Dari</option>
				<?php
					foreach($pusat AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
					}
				?>
			</select>
			<select id='subgudang' name='subgudang' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Gudang Ke</option>
				<?php
					foreach($subgudang AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
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
					<th class="text-center">Gudang Dari</th>
					<th class="text-center">Gudang Ke</th>
					<th class="text-center">Sum Material</th>
					<th class="text-center">Receiver</th>
					<th class="text-center">Created Date</th>
					<th class="text-center no-sort">Status</th>
					<th class="text-center no-sort">Option</th>
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
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
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
		
	});

	$(document).on('keyup', '.checkRequest', function(e){
		var nomor 	= $(this).data('no');
		var nomor2 	= $(this).data('no2');
		var request 	= getNum($(this).val().split(",").join(""))
		var stock 		= getNum($('#stock_'+nomor).vals().split(",").join(""))
		var konversi 	= getNum($('#konversi_'+nomor).val().split(",").join(""))
		// console.log(request)
		// console.log(stock)
		// console.log(konversi)
		if(stock < request && stock > 0){
			$(this).val(number_format(stock,4))
			$('#cstk_'+nomor+'_'+nomor2).val(stock*konversi)
			// console.log('1')
		}
		if(stock >= request && stock > 0){
			$('#cstk_'+nomor+'_'+nomor2).val(request*konversi)
			// console.log('2')
		}
		if(stock < 0){
			$(this).val(0)
			$('#cstk_'+nomor+'_'+nomor2).val(0)
			// console.log('3s')
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
	
	$(document).on('click', '.check', function(e){
		e.preventDefault();
		// alert("not finished");
		// return false;
		loading_spinner();
		$("#head_title2").html("<b>KONFIRMASI REQUEST MATERIAL</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_request_check/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);
				$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});

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

	$(document).on('click', '.createspk', function(e){
		e.preventDefault();
		// alert("not finished");
		// return false;
		loading_spinner();
		$("#head_title2").html("<b>CREATE SPK</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_create_spk_req/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);
				$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
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

	$(document).on('click', '.edit_material', function(e){
		e.preventDefault();
		// alert("not finished");
		// return false;
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
				  timer				: 5000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click','.plus', function(){
		var no 		= $(this).data('id');
		var material = $(this).data('material');
		var gudang 	= $(this).data('gudang');
		var kolom	= parseFloat($(this).parent().parent().find("td:nth-child(1)").attr('rowspan')) + 1;
		let list_expired = $(this).parent().parent().find(".list_expired").html();
		$(this).parent().parent().find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5)").attr('rowspan', kolom);
		
		var Rows	= "<tr>";
			Rows	+= "<td align='center'>";
			Rows	+= "<input type='text' name='detail["+no+"][detail]["+kolom+"][qty_pack]' id='cstkpack_"+no+"_"+kolom+"'  data-no2='"+kolom+"' data-no='"+no+"' class='form-control input-sm text-center autoNumeric checkRequest'>";
			Rows	+= "<input type='hidden' name='detail["+no+"][detail]["+kolom+"][qty_oke]' id='cstk_"+no+"_"+kolom+"'  data-no2='"+kolom+"' data-no='"+no+"' class='form-control input-sm text-right autoNumeric checkRequest'>";
			Rows	+= "</td>";
			Rows	+= "<td align='center'><select id='sel_"+no+"_"+kolom+"' name='detail["+no+"][detail]["+kolom+"][expired]' class='form-control input-sm chosen_select'>"+list_expired+"</select></td>";
			// Rows	+= "<td align='center'></td>";
			Rows	+= "<td align='center'><input type='text' name='detail["+no+"][detail]["+kolom+"][check_keterangan]' data-no='"+no+"' class='form-control input-sm text-left'></td>";
			Rows	+= "<td align='center'>";
			Rows	+= "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='"+no+"'><i class='fa fa-trash'></i></button>";
			Rows	+= "</td>";
			Rows	+= "</tr>";
		// alert(Rows);
		$(this).parent().parent().after(Rows);
		var kol = "#sel_"+no+"_"+kolom;
		
		$('.autoNumeric').autoNumeric();
		$('.chosen_select').chosen();
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
	});
	
	$(document).on('click','.delete', function(){
		var no 		= $(this).data('id');
		var kolom	= parseFloat($(".baris_"+no).find("td:nth-child(1)").attr('rowspan')) - 1;
		$(".baris_"+no).find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5)").attr('rowspan', kolom);
		
		$(this).parent().parent().remove();
	});

    $(document).on('click', '#request', function(e){
		e.preventDefault();
		var gudang_before 	= $('#gudang_before').val();
		var gudang_after 	= $('#gudang_after').val();
		var tanggal_trans 	= $('#tanggal_trans').val();

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
			  text	: 'Gudang Ke Not Select, please input first ...',
			  type	: "warning"
			});
			$('#request').prop('disabled',false);
			return false;
		}

		loading_spinner();
		$("#head_title2").html("<b>TOTAL MATERIAL REQUEST</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_request_material/'+gudang_before+'/'+gudang_after+'/'+tanggal_trans,
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
					url			: base_url + active_controller+'/process_request_material',
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
							window.location.href = base_url + active_controller+'/request_subgudang';
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
		$('#check_material').prop('disabled',true);
		var berat 			= $('.maskM').val();
		var uri_tanda	 	= $('#uri_tanda').val();
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
							
							if(uri_tanda == 'pusat'){
								window.location.href = base_url + active_controller+'/request_subgudang/pusat';
							}
							else{
								window.location.href = base_url + active_controller+'/request_subgudang';
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
							
							if(uri_tanda == 'pusat'){
								window.location.href = base_url + active_controller+'/request_subgudang/pusat';
							}
							else{
								window.location.href = base_url + active_controller+'/request_subgudang';
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

	//REJECT PERMINTAAN
	$(document).on('click', '.cancel_request', function(){
		let kode_trans 			= $(this).data('kode_trans')
		let filter_pusat 		= $('#pusat').val();
		let filter_subgudang 	= $('#subgudang').val();
		let filter_uri_tanda 	= $('#uri_tanda').val();
		
		swal({
			title: "Are you sure?",
			text: "Cancel this request!",
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
				$.ajax({
					url			: base_url + active_controller+'/cancel_request',
					type		: "POST",
					data		: {
						'kode_trans' : kode_trans,
						'filter_pusat' : filter_pusat,
						'filter_subgudang' : filter_subgudang,
						'filter_uri_tanda' : filter_uri_tanda
					},
					cache		: false,
					dataType	: 'json',				
					success		: function(data){								
						if(data.status == 1){											
							swal({
								title	: "Save Success!",
								text	: data.pesan,
								type	: "success",
								timer	: 7000
							});
							DataTables(data.filter_pusat,data.filter_subgudang,data.filter_uri_tanda);
						}
						else{
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
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000
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
				url : base_url + active_controller+'/server_side_request_material',
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
	
	$(document).on('change','.qtypack, .ket', function(){
		var no 				= $(this).data('no');
		var id 				= $('#id_'+no).val();
		var konversi 		= $('#konversi_'+no).val();
		var packing 		= $('#sudah_requestpack_'+no).val();
		var sudah_request 	= 0;
		if(packing > 0 && konversi > 0){
			var sudah_request 	= packing * konversi;
		}
		$('#sudah_request_'+no).val(sudah_request);
		var ket_request		= $('#ket_request_'+no).val();

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

	$(document).on('click', '#create_spk', function(){
		$('#create_spk').prop('disabled',true);

		if($('.lotList:checked').length == 0){
			swal({
				title	: "Error Message!",
				text	: 'Checklist Minimal 1',
				type	: "warning"
			});
			$('#create_spk').prop('disabled',false);
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
					url			: base_url + active_controller+'/modal_create_spk_req',
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
									timer	: 7000
								});
							window.location.href = base_url + active_controller+'/request_subgudang/pusat';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
							$('#create_spk').prop('disabled',false);
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000
						});
						$('#create_spk').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#create_spk').prop('disabled',false);
			return false;
			}
		});
	});

	$(document).on('click', '.edit_material_new', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>EDIT REQUEST MATERIAL</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_request_edit_new/'+$(this).data('kode_trans'),
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

	$(document).on('click', '#edit_material_new', function(){
		var berat 			= $('.maskM').val();
		var uri_tanda	 	= $('#uri_tanda').val();
		if( berat == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Request Check is empty, please input first ...',
			  type	: "warning"
			});
			$('#edit_material_new').prop('disabled',false);
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
					url			: base_url + active_controller+'/modal_request_edit_new',
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
									timer	: 7000
								});
							
							window.location.href = base_url + active_controller+'/request_subgudang';
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
	
	function DataTables2(pusat=null, subgudang=null){
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
			"aLengthMenu": [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],
			"ajax":{
				url : base_url + active_controller+'/server_side_modal_request_material',
				type: "post",
				data: function(d){
					d.pusat = pusat,
					d.subgudang = subgudang
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
