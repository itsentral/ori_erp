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
					<th class="text-center no-sort">#</th>
					<th class="text-center no-sort">IPP</th>
					<th class="text-center no-sort">Customer</th>
					<th class="text-center no-sort">Project</th>
					<th class="text-center no-sort">Type</th>
					<th class="text-center no-sort">Series</th>
					<th class="text-center no-sort">Rev</th>
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
	<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:99%; '>
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
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:80%; '>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView3" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title3"></h4>
					</div>
					<div class="modal-body" id="view3">
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
	<div class="modal fade" id="ModalView4" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:40%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title4"></h4>
					</div>
					<div class="modal-body" id="view4">
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
		DataTables();
	});
	
	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ IN FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_bq/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '.view_data', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL DATA BQ IN FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/view_data/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '.detail_data', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_material/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('length'),
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
	
	$(document).on('click', '.edit_est', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>ESTIMATION STRUCTURE BQ IN FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_est_bq/'+$(this).data('id_bq'),
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
	
	$(document).on('click', '.detail_comp', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>DETAIL ESTIMATION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_product_est/'+$(this).data('id_bq')+'/'+$(this).data('id_milik'),
			success:function(data){
				$("#ModalView3").modal();
				$("#view3").html(data);

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
	
	$(document).on('click', '.ajukan_final_drawing', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ESTIMATION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/ajukan_fd_parsial/'+$(this).data('id_bq'),
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
	
	//SAVE NEW ADA DEFAULTNYA
	$(document).on('click', '.updateResin', function(){
		var layer = $(this).data('lyr');
		var material = $("#"+layer).val();
		// var product_id = $("#product_id").val();

		if($('.chk_personal2:checked').length == 0){
			swal({
				title	: "Error Message!",
				text	: 'Checklist milimal satu terlebih dahulu',
				type	: "warning"
			});
			return false;
		}

		if(material == '0'){
			swal({
				title	: "Error Message!",
				text	: 'Material not selected, please select first ...',
				type	: "warning"
			});
			return false;
		}

		// alert(layer+'/'+material);
		// return false;
		
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
					url			: base_url+active_controller+'/update_resin/'+material+'/'+layer,
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
									timer	: 3000
								});
							
							$('#jumlah_resin').html(data.jumlah_resin);
							$('#nama_resin').html(data.nama_resin);
							
							$('#jumlah_veil').html(data.jumlah_veil);
							$('#nama_veil').html(data.nama_veil);
							
							$('#jumlah_csm').html(data.jumlah_csm);
							$('#nama_csm').html(data.nama_csm); 
							
							$('#jumlah_wr').html(data.jumlah_wr);
							$('#nama_wr').html(data.nama_wr);
							
							$('#jumlah_rooving').html(data.jumlah_rooving);
							$('#nama_rooving').html(data.nama_rooving);
							
							$('#jumlah_catalys').html(data.jumlah_catalys);
							$('#nama_catalys').html(data.nama_catalys);
							
							$('#jumlah_pigment').html(data.jumlah_pigment);
							$('#nama_pigment').html(data.nama_pigment);
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 3000
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !", 
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 3000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
	//SAVE NEW BQ
	$(document).on('click', '#estNowNewBQ', function(){
		
		if($('.chk_personal:checked').length == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Checklist Minimal One Component',
			  type	: "warning"
			});
			$('#estNowNewBQ').prop('disabled',false);
			return false;
		}
		
		var intL = 0;
		var intError = 0;
		var pesan = '';
		
		var data_url = $('#pembeda').val();
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
					url			: base_url + active_controller+'/update_est_get_last',
					type		: "POST",
					data		: formData,
					// data:{
							// 'id_milikx' : id_milik
						// },
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
							// window.location.href = base_url + active_controller+'/'+data_url;
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bqx+'/'+data.pembeda);
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
	
	//SAVE NEW ADA DEFAULTNYA
	$(document).on('click', '#estNowNew', function(){
		
		if($('.chk_personal:checked').length == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Checklist Minimal One Component',
			  type	: "warning"
			});
			$('#estNowNew').prop('disabled',false);
			return false;
		}
		
		var intL = 0;
		var intError = 0;
		var pesan = '';
		
		var data_url = $('#pembeda').val();
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
					url			: base_url + active_controller+'/update_est_get_master',
					type		: "POST",
					data		: formData,
					// data:{
							// 'id_milikx' : id_milik
						// },
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
							// window.location.href = base_url + active_controller+'/'+data_url;
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bqx+'/'+data.pembeda);
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
	
	//SAVE NEW ADA DEFAULTNYA
	$(document).on('click', '.save_mat_acc', function(){
		var intL = 0;
		var intError = 0;
		var pesan = '';
		
		var data_url = $('#pembeda').val();
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
					url			: base_url + active_controller+'/update_mat_acc',
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
								  timer	: 5000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bqx+'/'+data.pembeda);
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
		
	$(document).on('click', '.addPart', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];

		$.ajax({
			url: base_url +'machine/get_add/'+id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskM').maskMoney();
				swal.close();
			},
			error: function() {
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
	});

	$(document).on('click', '.addPart2', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];

		$.ajax({
			url: base_url +'machine/get_add2/'+id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add2_"+id_bef).before(data.header);
				$("#add2_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskM').maskMoney();
				swal.close();
			},
			error: function() {
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
	});

	$(document).on('click', '.delPart', function(){
		var get_id 		= $(this).parent().parent().attr('class');
		$("."+get_id).remove();
	});	
		
	$(document).on('click', '.update_get_master', function(){
		var id_bq = $(this).data('id_bq');
		var id_milik = $(this).data('id_milik');
		var panjang = $(this).data('panjang');
		var nomor = $(this).data('nomor');
		var product = $("#id_product_"+nomor).val();
		
		if(product == '' || product == null || product == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Product is Empty, please input first ...',
			  type	: "warning"
			});
			$('.update_get_master').prop('disabled',false);
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
				// var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/update_satuan_est_master/'+id_bq+'/'+id_milik+'/'+panjang+'/'+product,
					type		: "POST",
					// data		: formData,
					// data:{
							// 'id_bq' : id_bq
						// },
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
							
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ IN FINAL DRAWING ["+data.id_bqx+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bqx);
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
	
	$(document).on('click', '.update_get_est_bq', function(){
		var id_bq = $(this).data('id_bq');
		var id_milik = $(this).data('id_milik');
		var panjang = $(this).data('panjang');
		var nomor = $(this).data('nomor');
		var product = $("#id_product_"+nomor).val();
		var id_milik_bq = $(this).data('id_milik_bq');
		
		if(product == '' || product == null || product == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Product is Empty, please input first ...',
			  type	: "warning"
			});
			$('.update_get_est_bq').prop('disabled',false);
			return false;	
		} 

		// alert(id_milik);
		// alert(id_milik_bq);
		// return false;
		
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
				// var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/update_satuan_est_bq/'+id_bq+'/'+id_milik+'/'+panjang+'/'+product+'/'+id_milik_bq,
					type		: "POST",
					// data		: formData,
					// data:{
							// 'id_bq' : id_bq
						// },
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
							
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ IN FINAL DRAWING ["+data.id_bqx+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bqx);
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
		
	$(document).on('click', '.back_to_fd_est_bq', function(){
		var bq		= $(this).data('id_bq');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Kembali ke Structure BQ untuk revisi",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/back_to_fd_est_bq/'+bq,
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller + '/fd_estimasi';
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});	
	
	$(document).on('click', '.ajukanSat', function(){
		var nomor		= $(this).data('nomor');
		var bq			= $(this).data('id_bq');
		var id_milik	= $(this).data('id_milik');
		var berat		= $("#berat_"+nomor).val();
		var product		= $("#product_"+nomor).val();
		var qtyrelease	= $("#qtyrelease_"+id_milik).val();
		var cutting		= $(this).parent().parent().find('.chk_personal_cutting:checked').val();

		let cutting2 = $('#cut_'+id_milik).val();
		
		if(cutting2 == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Cutting plan belum dipilih !',
			  type	: "warning"
			});
			return false;
		}
		
		if(berat <= 0 || berat == ''){
			if(product != 'product kosong'){
				swal({
				  title	: "Error Message!",
				  text	: 'Berat product masih kosong, please check ...',
				  type	: "warning"
				});
				return false;
			}
		}

		if(qtyrelease < 1){
			swal({
				title	: "Error Message!",
				text	: 'Qty release masih kosong, please input ...',
				type	: "warning"
			});
			return false;
		}
		// console.log(cutting)
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan sebagian final drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/ajukan_satuan_product',
					type		: "POST",
					data		: {
						'bq' : bq,
						'id_milik' : id_milik,
						'cutting' : cutting2,
						'qtyrelease' : qtyrelease
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
								
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 3000
							});
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
							$("#ModalView").modal();
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 3000
						});
					}
				});
			} else {
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
		
	$(document).on('click', '#ajukanEst', function(){
		if($('.chk_personal:checked').length == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Checklist Minimal One Component',
			  type	: "warning"
			});
			$('#ajukanEst').prop('disabled',false);
			return false;
		}
		
		let nomor = 0;
		let id_milik;
		let cutting2
		let status = true
		$('.chk_personal:checked').each(function(){
			nomor++;
			id_milik = $(this).data('id_milik')
			// console.log(id_milik)
			cutting2 = $('#cut_'+id_milik).val()
			if(cutting2 == 0){
				status = false
			}
		});

		if(status == false){
			swal({
			  title	: "Error Message!",
			  text	: 'Cutting plan belum ada yang belum dipilih !',
			  type	: "warning"
			});
			return false;
		}
		
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan sebagian dipilih final drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData 	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/ajukan_all_product',
					type		: "POST",
					cache		: false,
					dataType	: 'json',
					data		: formData,
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
								
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
							$("#ModalView").modal();
						}
						else{
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '#ajukan_mat', function(){
		if($('.chk_personal2:checked').length == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Checklist Minimal One Component',
			  type	: "warning"
			});
			$('#ajukan_mat').prop('disabled',false);
			return false;
		}
		
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan sebagian dipilih final drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData 	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/ajukan_all_material',
					type		: "POST",
					cache		: false,
					dataType	: 'json',
					data		: formData,
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
								
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url  + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '#ajukan_mat2', function(){
		if($('.chk_personal3:checked').length == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Checklist Minimal One Component',
			  type	: "warning"
			});
			$('#ajukan_mat2').prop('disabled',false);
			return false;
		}
		
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan sebagian dipilih final drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData 	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/ajukan_all_acc',
					type		: "POST",
					cache		: false,
					dataType	: 'json',
					data		: formData,
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
								
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url  + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});	
		
	$(document).on('click', '.ajukanSatMat', function(){
		var nomor		= $(this).data('nomor');
		var bq			= $(this).data('id_bq');
		var id_milik	= $(this).data('id_milik');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan sebagian final drawing",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/ajukan_satuan_material/'+bq+'/'+id_milik,
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
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
							$("#head_title").html("<b>DETAIL ESTIMATION</b>");
							$("#view").load(base_url + active_controller+'/ajukan_fd_parsial/'+data.id_bq);
							$("#ModalView").modal();
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
		
	$(document).on('click', '.close_parsial', function(){
		var bq		= $(this).data('id_bq');
		// alert('Development Process !!!');
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Close Parsial Status !",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/close_parsial/'+bq,
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller + '/fd_estimasi';
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
	
	
	
	
	
	
	
	
	$(document).on('click', '.edit_pipe', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION PIPE</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_pipe/'+$(this).data('id_bq')+'/'+$(this).data('id_milik'));
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_end_cap', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION END CAP</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_end_cap/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_blindflange', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION BLIND FLANGE</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_blindflange/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_pipeslongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION PIPE SLONGSONG</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_pipeslongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_elbowmould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION ELBOW MOULD</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_elbowmould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_elbowmitter', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION ELBOW MITTER</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_elbowmitter/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_eccentric_reducer', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION ECCENTRIC REDUCER</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_eccentric_reducer/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_concentric_reducer', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION CONCENTRIC REDUCER</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_concentric_reducer/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_equal_tee_mould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION EQUAL TEE MOULD</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_equal_tee_mould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_reducer_tee_mould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION REDUCER TEE MOULD</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_reducer_tee_mould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_equal_tee_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION EQUAL TEE SLONGSONG</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_equal_tee_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_reducer_tee_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION REDUCER TEE SLONGSONG</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_reducer_tee_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_flange_mould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION FLANGE MOULD</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_flange_mould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_flange_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION FLANGE SLONGSONG</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_flange_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_colar', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION COLAR</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_colar/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_colar_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION COLAR SLONGSONG</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_colar_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	$(document).on('click', '.edit_field_joint', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION FLIED JOINT</b>");
		$("#view3").load(base_url + active_controller+'/modalFD_field_joint/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	
	
	
	$(document).on('click', '.ajuAppFD', function(){
		var bq		= $(this).data('id_bq');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan Estimasi BQ untuk di approve",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url+active_controller+'/ajukanAppFD/'+bq,
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller + '/fd_estimasi';
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
	
	//back to bq
	
	
	//=========================================================================================================================
	//====================================================SAVE EDIT PROJECT====================================================
	//=========================================================================================================================
	
	//REDUCER TEE SLONGSONG
	$(document).on('click', '#simpan-bro-reducerteeslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_reducerteeslongsong')[0]);
					var baseurl		= base_url +'/edit_fd/reducer_tee_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
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
							$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-reducerteeslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//REDUCER TEE MOULD
	$(document).on('click', '#simpan-bro-reducerteemould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteemould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteemould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteemould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-reducerteemould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_reducerteemould')[0]);
					var baseurl		= base_url +'/edit_fd/reducer_tee_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-reducerteemould').prop('disabled',false);
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
							$('#simpan-bro-reducerteemould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-reducerteemould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//FLANGE SLONGSONG
	$(document).on('click', '#simpan-bro-flangeslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangeslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangeslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangeslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-flangeslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_flangeslongsong')[0]);
					var baseurl		= base_url +'/edit_fd/flange_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-flangeslongsong').prop('disabled',false);
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
							$('#simpan-bro-flangeslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-flangeslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//FLANGE MOULD
	$(document).on('click', '#simpan-bro-flangemould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangemould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangemould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangemould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-flangemould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_flangemould')[0]);
					var baseurl		= base_url +'/edit_fd/flange_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-flangemould').prop('disabled',false);
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
							$('#simpan-bro-flangemould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-flangemould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//EQUAL TEE SLONGSONG
	$(document).on('click', '#simpan-bro-equalteeslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-equalteeslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_equalteeslongsong')[0]);
					var baseurl		= base_url +'/edit_fd/equal_tee_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-equalteeslongsong').prop('disabled',false);
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
							$('#simpan-bro-equalteeslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-equalteeslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//EQUAL TEE MOULD
	$(document).on('click', '#simpan-bro-equalteemould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteemould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteemould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteemould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-equalteemould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_equalteemould')[0]);
					var baseurl		= base_url +'/edit_fd/equal_tee_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-equalteemould').prop('disabled',false);
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
							$('#simpan-bro-equalteemould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-equalteemould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//END CAP
	$(document).on('click', '#simpan-bro-endcap', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-endcap').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-endcap').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-endcap').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-endcap').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_endcap')[0]);
					var baseurl		= base_url +'/edit_fd/end_cap_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-endcap').prop('disabled',false);
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
							$('#simpan-bro-endcap').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-endcap').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//ECCENTRIC
	$(document).on('click', '#simpan-bro-eccentric', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-eccentric').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-eccentric').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-eccentric').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-eccentric').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_eccentric')[0]);
					var baseurl		= base_url +'edit_fd/eccentric_reducer_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-eccentric').prop('disabled',false);
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
							$('#simpan-bro-eccentric').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-eccentric').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//CONCENTRIC
	$(document).on('click', '#simpan-bro-concentric', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-concentric').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-concentric').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-concentric').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-concentric').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_concentric')[0]);
					var baseurl		= base_url +'edit_fd/concentric_reducer_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-concentric').prop('disabled',false);
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
							$('#simpan-bro-concentric').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-concentric').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//COLAR SLONGSONG
	$(document).on('click', '#simpan-bro-colarslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colarslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colarslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-colarslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-colarslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_colarslongsong')[0]);
					var baseurl		= base_url +'/edit_fd/colar_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-colarslongsong').prop('disabled',false);
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
							$('#simpan-bro-colarslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-colarslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//COLAR
	
	
	$(document).on('click', '#simpan-bro-colar', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colar').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colar').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-colar').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-colar').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_colar')[0]);
					var baseurl		= base_url +'/edit_fd/colar_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-colar').prop('disabled',false);
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
							$('#simpan-bro-colar').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-colar').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//BLIND FLANGE
	$(document).on('click', '#simpan-bro-blindflange', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-blindflange').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-blindflange').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-blindflange').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-blindflange').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_blindflange')[0]);
					var baseurl		= base_url +'/edit_fd/blind_flange_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-blindflange').prop('disabled',false);
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
							$('#simpan-bro-blindflange').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-blindflange').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//ELBOW MITTER
	$(document).on('click', '#simpan-bro-elbowmitter', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmitter').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmitter').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmitter').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-elbowmitter').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_elbowmitter')[0]);
					var baseurl		= base_url +'/edit_fd/elbow_mitter_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-elbowmitter').prop('disabled',false);
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
							$('#simpan-bro-elbowmitter').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-elbowmitter').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//ELBOW MOULD
	$(document).on('click', '#simpan-bro-elbowmould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-elbowmould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_elbowmould')[0]);
					var baseurl		= base_url +'/edit_fd/elbow_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-elbowmould').prop('disabled',false);
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
							$('#simpan-bro-elbowmould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-elbowmould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//PIPE
	$(document).on('click', '#simpan-bro-pipe', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-pipe').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-pipe').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-pipe').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-pipe').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_pipe')[0]);
					var baseurl		= base_url +'/edit_fd/pipe_bq'; 
					$.ajax({
						url			: baseurl,
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
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url + active_controller+'/modal_est_bq/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-pipe').prop('disabled',false);
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
							$('#simpan-bro-pipe').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-pipe').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//NEW
		$(document).on('click', '.addPart3', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url +'machine/get_add3/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add3_"+id_bef).before(data.header);
					$("#add3_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('click', '.addPart4', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url +'machine/get_add4/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add4_"+id_bef).before(data.header);
					$("#add4_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});
		
		$(document).on('click', '.addPart4g', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url +'machine/get_add4g/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add4g_"+id_bef).before(data.header);
					$("#add4g_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('click', '.addPart5', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('id');
			var split_id	= get_id.split('_');
			var id 		= parseInt(split_id[1])+1;
			var id_bef 	= split_id[1];

			$.ajax({
				url: base_url +'machine/get_add5/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#add5_"+id_bef).before(data.header);
					$("#add5_"+id_bef).remove();
					$('.chosen_select').chosen({width: '100%'});
					$('.maskM').maskMoney();
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});
	
		$(document).on('change', '.get_detail_lainnya', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url +'machine/get_detail_lainnya/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#ln_ukuran_standart_"+id_bef).val(data.ukuran_standart);
					$("#ln_standart_"+id_bef).val(data.standart);
					// $("#ln_satuan_"+id_bef+"_chosen option[value="+data.satuan+"]").attr('selected','selected');
					$("#ln_satuan_"+id_bef).html("<option value='"+data.satuan+"'>"+data.satuan_view+"</option>").trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('change', '.get_detail_plate', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url +'machine/get_detail_plate/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#pl_ukuran_standart_"+id_bef).val(data.ukuran_standart);
					$("#pl_standart_"+id_bef).val(data.standart);
					$("#pl_thickness_"+id_bef).val(data.thickness);
					$("#pl_density_"+id_bef).val(data.density);

					get_berat_plate(id_bef);
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});
		
		$(document).on('change', '.get_detail_gasket', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url +'machine/get_detail_gasket/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#gs_ukuran_standart_"+id_bef).val(data.standart);
					$("#gs_dimensi_"+id_bef).val(data.dimensi);
					$("#gs_satuan_"+id_bef).html("<option value='"+data.satuan+"'>"+data.satuan_view+"</option>").trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});
		
		$(document).on('change', '.get_detail_baut', function(){
			loading_spinner();
			var get_id 		= $(this).parent().parent().attr('class');
			// console.log(get_id); return false;
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			var id 			= $(this).val();

			$.ajax({
				url: base_url +'machine/get_detail_baut/'+id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#bt_material_"+id_bef).val(data.material);
					swal.close();
				},
				error: function() {
					swal({
						title				: "Error Message !",
						text				: 'Connection Time Out. Please try again..',
						type				: "warning",
						timer				: 3000,
						showCancelButton	: false,
						showConfirmButton	: false,
						allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('keyup', '.get_berat', function(){
			var get_id 		= $(this).parent().parent().attr('class');
			var split_id	= get_id.split('_');
			var id_bef 		= split_id[1];
			get_berat_plate(id_bef);
		});

		$(document).on('change', '#category_id', function(){
			loading_spinner();
			var category_id = $(this).val()
			var item_cost = $('.listMaterial')
			var label_category = $('.label_category')

			$.ajax({
				url: base_url +'machine/get_material/'+category_id,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(item_cost).html(data.option).trigger("chosen:updated");
					$(label_category).html(data.nama);
					swal.close();
				},
				error: function() {
					swal({
						title	: "Error Message !",
						text	: 'Connection Time Out. Please try again..',
						type	: "warning",
						timer	: 3000
					});
				}
			});
		});
	
	
	function get_berat_plate(id){
		var panjang 	= getNum($('#pl_panjang_'+id).val());
		var thickness 	= getNum($('#pl_thickness_'+id).val());
		var lebar 		= getNum($('#pl_lebar_'+id).val());
		var density 	= getNum($('#pl_density_'+id).val());
		var qty 		= getNum($('#pl_qty_'+id).val());

		var berat = panjang * thickness * lebar * density * qty;

		$('#pl_berat_'+id).val(number_format(berat,3));
	}

	function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

	function number_format (number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	}
	
	//=========================================================================================================================
	//====================================================END EDIT PROJECT=====================================================
	//=========================================================================================================================
		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": true,
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
				url : base_url + active_controller+'/server_side_fd_est',
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
