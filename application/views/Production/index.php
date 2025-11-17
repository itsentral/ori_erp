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
					<th class="text-center">#</th>
					<th class="text-center">IPP</th>
					<th class="text-center">SO Number</th>
					<th class="text-center no-sort">Project</th>
					<th class="text-center">Date</th>
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
		<div class="modal-dialog"  style='width:90%; '>
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
	<div class="modal fade" id="ModalView2"  style='overflow-y: auto;'>
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
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var menu_baru = '<?=$menu_baru;?>';
		DataTables(menu_baru);
	});
	
	let uri_help = '<?=$uri_help;?>';

	$(document).on('click', '.detail_spk', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>PRINT SPK PRODUKSI ["+$(this).data('id_produksi')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalDetail/'+$(this).data('id_produksi')+'/'+$(this).data('menu_baru'),
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
				});
			}
		});
	});
	
	$(document).on('click', '.start_produksi', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>PRODUCTION NOW ["+$(this).data('id_produksi')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalstartPro/'+$(this).data('id_produksi'),
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
				});
			}
		});
	});

	$(document).on('click', '.close_produksi', function(e){
		e.preventDefault();
		loading_spinner();
		let id_produksi = $(this).data('id_produksi')
		$("#head_title").html("<b>CLOSE PRODUKSI ["+id_produksi+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalcloseProduksi/'+id_produksi,
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
				});
			}
		});
	});
	
	$(document).on('click', '#btn_download', function(e){
		e.preventDefault();
		var id_produksi		= $(this).data('id_produksi');
		var Links		= base_url + active_controller+'/ExcelProduksi/'+id_produksi;
		window.open(Links,'_blank');
	});
	
	$(document).on('click', '.spk_mat_acc', function(e){
		e.preventDefault();
		var id_bq		= $(this).data('id_bq');
		var tanda		= $(this).data('tanda');
		var Links		= base_url + active_controller+'/spk_mat_acc/'+id_bq+'/'+tanda;
		window.open(Links,'_blank');
	});
	
	$(document).on('click', '#save_start_produksi', function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var plan_start_produksi	= $('#plan_start_produksi').val();
		var plan_end_produksi	= $('#plan_end_produksi').val();
		var id_mesin			= $('#id_mesin').val();
		// alert('hy');
		if(id_mesin == '' || id_mesin == null || id_mesin == '-' || id_mesin == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Machine, please input first ...',
			  type	: "warning"
			});
			$('#save_start_produksi').prop('disabled',false);
			return false;
		}
		if(plan_start_produksi=='' || plan_start_produksi==null || plan_start_produksi=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Start Plan Production, please input first ...',
			  type	: "warning"
			});
			$('#save_start_produksi').prop('disabled',false);
			return false;
		}
		if(plan_end_produksi=='' || plan_end_produksi==null || plan_end_produksi=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'End Plan Production, please input first ...',
			  type	: "warning"
			});
			$('#save_start_produksi').prop('disabled',false);
			return false;
		}
		
		$('#save_start_produksi').prop('disabled',false);

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
					var formData 	=new FormData($('#form_start_pro')[0]);
					var baseurl=base_url + active_controller +'/UpdateProduksi';
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
									  timer	: 7000
									});
								window.location.href = base_url + active_controller + '/index/new';
							}
							else if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#save_start_produksi').prop('disabled',false);
						},
						error: function() {

							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',
							  type				: "warning",
							  timer				: 7000,
							});
							$('#save_start_produksi').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_start_produksi').prop('disabled',false);
				return false;
			  }
		});
	});

	$(document).on('click', '#save_close_produksi', function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var real_start_produksi	= $('#real_start_produksi').val();
		var real_end_produksi	= $('#real_end_produksi').val();

		if(real_start_produksi=='' || real_start_produksi==null || real_start_produksi=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Aktual mulai produksi, please input first ...',
			  type	: "warning"
			});
			$('#save_close_produksi').prop('disabled',false);
			return false;
		}
		if(real_end_produksi=='' || real_end_produksi==null || real_end_produksi=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Aktual selesai produksi, please input first ...',
			  type	: "warning"
			});
			$('#save_close_produksi').prop('disabled',false);
			return false;
		}
		
		$('#save_close_produksi').prop('disabled',false);

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
					var formData 	=new FormData($('#form_start_pro')[0]);
					var baseurl=base_url + active_controller +'/UpdateCloseProduksi';
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
									  timer	: 5000
									});
								window.location.href = base_url + active_controller + '/index/new';
							}
							else if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#save_close_produksi').prop('disabled',false);
						},
						error: function() {

							swal({
							  title	: "Error Message !",
							  text	: 'An Error Occured During Process. Please try again..',
							  type	: "warning",
							  timer	: 5000,
							});
							$('#save_close_produksi').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_close_produksi').prop('disabled',false);
				return false;
			  }
		});
	});
	
	//turunkan aemua per id_milik
	$(document).on('click', '.turunkanAllSpk', function(){
		var bF	= $(this).data('id_pro_detail');
		var id_produksi	= $(this).data('id_produksi');
		var menu_baru = '<?=$menu_baru;?>';
		// alert(bF);
		// return false;
		swal({
		  title: "Informasi",
		  text: "Turunkan semua SPK ke produksi ?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Turunkan!",
		  cancelButtonText: "Tidak, Batalkan!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/spk_turun_all/'+bF+'/'+id_produksi+'/'+menu_baru,
					type		: "POST",
					data		: "id="+bF,
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
								  timer	: 4000
								}); 
							// window.location.href = base_url+active_controller+'/modalPrint/'+id_produksi;
							$("#head_title").html("<b>DETAIL PRODUCTION ["+data.id_produksi+"]</b>");
							$("#view").load(base_url + active_controller+'/modalDetail/'+data.id_produksi+'/'+data.menu_baru);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning", 
							  timer	: 4000
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 7000,
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
	//Print Merge SPK 1
	$(document).on('click', '.printMerge', function(){
		var nomor		= $(this).data('nomor');
		var id			= $(this).data('id');
		var spk			= $(this).data('spk');
		
		var qty_bef 	= parseFloat($('#qty_bef_'+nomor).val());
		var qty_bef2 	= parseFloat($('#qty_bef2_'+nomor).val());
		var qty_print 	= parseFloat($('#qty_print_'+nomor).val());
		
		// alert(qty_print);
		
		if(spk == '1'){
			if(qty_bef < qty_print){
				swal({
				  title	: "Error Message!",
				  text	: 'Qty melebihi qty spk 1 yang ada ...',
				  type	: "warning"
				});
				return false; 
			}
		}
		
		if(spk == '2'){
			if(qty_bef2 < qty_print){
				swal({
				  title	: "Error Message!",
				  text	: 'Qty melebihi qty spk 2 yang ada ...',
				  type	: "warning"
				});
				return false; 
			}
		}
		
		if(qty_print == 0 || isNaN(qty_print)){
			swal({
			  title	: "Error Message!",
			  text	: 'Qty masih Kosong ...',
			  type	: "warning"
			});
			return false; 
		}
		
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
				// loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/printSPK1MergeNew/'+id+'/'+qty_print+'/'+spk,  
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false, 
					contentType	: false,				
					success		: function(data){								
						if(data.status == 1){
							$("#head_title").html("<b>PRINT SPK ["+data.kode_produksi+"]</b>"); 
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+data.kode_produksi);
							$("#ModalView").modal();
							window.open(base_url + active_controller+'/'+data.spk+'/'+data.kode_produksi+'/'+data.kode_product+'/'+data.product_to+'/'+data.id_delivery+'/'+data.id+'/'+data.id_milik+'/'+data.qty_print,'_blank');
						}
						else if(data.status == 0){
							swal({
							  title	: "Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 7000,
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 7000,
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	//Back To Final Drwing
	$(document).on('click', '.backToFD', function(){
		var id			= $(this).data('id');
		var menu_baru = '<?=$menu_baru;?>';
		// return false;
		swal({
		  title: "Are you sure?",
		  text: "Product ini akan dikembalikan ke final drawing!",
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
				// loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/backToFinalDrawing/'+id+'/'+menu_baru, 
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false, 
					contentType	: false,				
					success		: function(data){								
						if(data.status == 1){
							$("#head_title").html("<b>PRINT SPK ["+data.id_produksi+"]</b>"); 
							$("#view").load(base_url + active_controller+'/modalDetail/'+data.id_produksi+'/'+data.menu_baru);
							$("#ModalView").modal();
						}
						else if(data.status == 0){
							swal({
							  title	: "Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 5000
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 5000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	//Close Booking Material
	$(document).on('click', '.close_booking_mat', function(e){
		e.preventDefault();

		let no_ipp = $(this).data('no_ipp');
		

		swal({
				title: "Are you sure?",
				text: "Close Booking Material!",
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
					var baseurl=base_url +'warehouse/close_booking_material';
					$.ajax({
						url			: baseurl,
						type		: "POST",
						data		: {
							no_ipp : no_ipp
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
								window.location.href = base_url + active_controller+"/index/new";
							}
							else{
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
								title				: "Error Message !",
								text				: 'An Error Occured During Process. Please try again..',
								type				: "warning",
								timer				: 3000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
		});
	});
		
	function DataTables(menu_baru=null){
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
				url : base_url + active_controller+'/server_side_spk_produksi',
				type: "post",
				data: function(d){
					d.menu_baru = menu_baru
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

	$(document).on('click', '#btn_qrcode', function(e){
		e.preventDefault();
		var idmilik = [];
		$('.cqr').each(function(i, obj) {
			if(this.checked){
				idmilik.push($(this).val());
			}
		});
		idmilik=idmilik.join("~")
		console.log(idmilik);
		var Links = base_url + active_controller+'/print_qrcode/'+idmilik;
		window.open(Links,'_blank');
	});
	
	
</script>
