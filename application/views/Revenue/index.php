<?php
$this->load->view('include/side_menu');
$ArrList = array();
foreach($ListIPP AS $val => $valx){
	$ArrList[$valx['no_ipp']] = $valx['no_ipp'];
}
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<br><br>
			
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center" width='3%'>No</th>
						<th class="text-center" width='5%'>No SO</th>
						<th class="text-center no-sort" width='7%'>Tgl SO</th>
						<th class="text-center" width='13%'>Customer</th>
						<th class="text-center no-sort" width='7%'>Nilai SO ($)</th>
						<th class="text-center no-sort" width='12%'>Option</th>
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
			<div class="modal-dialog"  style='width:30%; '>
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
	.btn{
		margin-top:2px;
	}
</style>
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		DataTables();
	});
	
	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>VIEW SALES ORDER ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_so/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '.deal_so', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>APPROVE SO CUSTOMER DEAL ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_deal_so/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '.del_so', function(){
		var id	= $(this).data('id');
		var id_milik	= $(this).data('id_milik');
		var id_bq = $('#id_bq').val();
		var id_bq_header = $(this).data('id_bq_header');
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Data akan terhapus secara Permanen !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_so/'+id+'/'+id_milik+'/'+id_bq+'/'+id_bq_header,
					type		: "POST",
					data		: "id="+id,
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
							$("#head_title").html("<b>APPROVE SO CUSTOMER DEAL ["+data.id_bq+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_deal_so/'+data.id_bq);
							$("#ModalView").modal();
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '.del_so_mat', function(){
		var id	= $(this).data('id');
		var id_milik	= $(this).data('id_milik');
		var id_bq = $('#id_bq').val();
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Data akan terhapus secara Permanen !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_so_mat/'+id+'/'+id_milik+'/'+id_bq,
					type		: "POST",
					data		: "id="+id,
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
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							$("#head_title").html("<b>APPROVE SO CUSTOMER DEAL ["+data.id_bq+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_deal_so/'+data.id_bq);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000,
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '.del_so_eng_pack_trans', function(){
		var id	= $(this).data('id');
		var id_bq = $('#id_bq').val();
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Data akan terhapus secara Permanen !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_so_eng_pack_trans',
					type		: "POST",
					data		: {
						'id' : id,
						'id_bq' : id_bq
					},
					cache		: false,
					dataType	: 'json',				
					success		: function(data){								
						if(data.status == 1){											
							swal({
								  title	: "Save Success!",
								  text	: data.pesan,
								  type	: "success",
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							$("#head_title").html("<b>APPROVE SO CUSTOMER DEAL ["+data.id_bq+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_deal_so/'+data.id_bq);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000,
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});

	$(document).on('click', '.add_so_eng_pack_trans', function(){
		var id	= $(this).data('id');
		var id_bq = $('#id_bq').val();
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Tambahkan ke Sales Order !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/add_sebagian_so_eng_pack_trans',
					type		: "POST",
					data		: {
						'id' : id,
						'id_bq' : id_bq
					},
					cache		: false,
					dataType	: 'json',				
					success		: function(data){								
						if(data.status == 1){											
							swal({
								  title	: "Save Success!",
								  text	: data.pesan,
								  type	: "success",
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							$("#head_title").html("<b>APPROVE SO CUSTOMER DEAL ["+data.id_bq+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_deal_so/'+data.id_bq);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000,
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
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '#update_qty_so', function(){
		var bF = $('#id_bq').val();
		swal({
		  title: "Are you sure?",
		  text: "Simpan Qty ?",
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
					url			: base_url + active_controller+'/update_qty_so/'+bF,
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
							$("#head_title").html("<b>APPROVE SO CUSTOMER DEAL ["+data.id_bq+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_deal_so/'+data.id_bq);
							$("#ModalView").modal();
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
	
	$(document).on('click', '#ajukanSO', function(){
		var bF				= $('#id_bq').val();
		swal({
		  title: "Are you sure?",
		  text: "Mengajukan Sales Order ?",
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
					url			: base_url + active_controller+'/ajukan_so/'+bF,
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
							window.location.href = base_url + active_controller;
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
	
	$(document).on('click', '#update_cost', function(){
		// var no_ipp_filter = $('#no_ipp_filter').val();
		
		// if(no_ipp_filter == null){
			// swal({
				// title	: "Error Message!",
				// text	: 'Minimal pilih 1 IPP yang akan ditampilkan...',
				// type	: "warning"
			// });
			// return false;
		// }
		
		swal({
		  title: "Update Sales Order ?",
		  text: "Tunggu sampai 'Last Update by ' menunjukan nama user dan update jam sekarang. ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Update!",
		  cancelButtonText: "Tidak, Batalkan!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				// loading_spinner();
				$('#spinnerx').show();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/insert_sales_order',
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
							$('#spinnerx').hide();
							window.location.href = base_url + active_controller;
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
							$('#spinnerx').hide();
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
						$('#spinnerx').hide();
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '.udate_berat', function(){
		var no_ipp	= $(this).data('no_ipp');
		// alert(bF);
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Update berat material sales order !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/update_berat_so/'+no_ipp,
					type		: "POST",
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
								timer	: 3000
							});
							DataTables();
						}
						else{
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000
							});
						}
					},
					error: function() {
						swal({
						  title		: "Error Message !",
						  text		: 'An Error Occured During Process. Please try again..',						
						  type		: "warning",								  
						  timer		: 5000
						});
					}
				});
			} else {
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});

	$(document).on('click', '.change_customer', function(e){
		e.preventDefault();
		var no_ipp	= $(this).data('no_ipp');
		loading_spinner();
		$("#head_title2").html("<b>UPDATE CUSTOMER ["+no_ipp+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/update_customer/'+no_ipp,
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
	
	$(document).on('click', '#change_customer', function(){
		var no_ipp	= $(this).data('no_ipp');
		var old_customer	= $('#old_customer').val();
		var new_customer	= $('#new_customer').val();
		// alert(no_ipp+' Development');
		// return false;
		swal({
		  title: "Apakah anda yakin ?",
		  text: "Ubah Customer !!!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Lanjutkan !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/update_customer',
					type		: "POST",
					data		: {
						'no_ipp' 		: no_ipp,
						'old_customer' 	: old_customer,
						'new_customer' 	: new_customer
					},
					cache		: false,
					dataType	: 'json',				
					success		: function(data){								
						if(data.status == 1){											
							swal({
								title	: "Save Success!",
								text	: data.pesan,
								type	: "success",
								timer	: 3000
							});
							DataTables();
							$('#ModalView2').modal('toggle');
						}
						else{
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000
							});
						}
					},
					error: function() {
						swal({
						  title		: "Error Message !",
						  text		: 'An Error Occured During Process. Please try again..',						
						  type		: "warning",								  
						  timer		: 5000
						});
					}
				});
			} else {
			swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
			return false;
			}
		});
	});
		
	function DataTables(){
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
				url : base_url + active_controller+'/server_side_so', 
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
