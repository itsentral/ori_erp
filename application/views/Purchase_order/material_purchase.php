<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['create']=='1'){
				echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Add RFQ','content'=>'Add RFQ','id'=>'addPO')).' ';
			}
		?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort">#</th>
					<th class="text-center">No RFQ</th> 
					<th class="text-center">Suppier</th>
					<th class="text-center no-sort">Material Name</th>
					<!-- <th class="text-center no-sort">Total Request</th> -->
					<th class="text-center no-sort">Create By</th>
					<th class="text-center no-sort">Created Date</th>
					<th class="text-center no-sort">Status</th>
					<th class="text-center no-sort" width='170px'>Option</th>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:50%; '>
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
</form>
<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
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
		$('.maskM').maskMoney(); 
		DataTables();
	});

	$(document).on('click', '#addPO', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>ADD PURCHASE ORDER</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modal_add_po',
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

    $(document).on('click', '.detailMat', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>TOTAL MATERIAL PURCHASE ["+$(this).data('no_rfq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_po/'+$(this).data('no_rfq')+'/'+$(this).data('status'),
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

	$(document).on('click', '.editMat', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>EDIT MATERIAL PURCHASE ["+$(this).data('no_rfq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modal_edit_po/'+$(this).data('no_rfq'),
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

	$(document).on('click', '#savePO', function(){
		var id_supplier = $('#id_supplier').val();
		// console.log(id_supplier);
		if( id_supplier == null){
			swal({
			  title	: "Error Message!",
			  text	: 'Supplier Not Select, please input first ...',
			  type	: "warning"
			});
			$('#savePO').prop('disabled',false);
			return false;
		}

		if($('input[type=checkbox]:checked').length == 0){
			swal({
				title	: "Error Message!",
				text	: 'Checklist Minimal One Component',
				type	: "warning"
			});
			$('#savePO').prop('disabled',false);
			return false;
		}

		swal({ 
			title: "Are you sure?",
			text: "You will be able to process again this data!",
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
					url			: base_url + active_controller+'/save_po',
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
							window.location.href = base_url + active_controller+'/material_purchase';
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
	
	//PRINT
	$(document).on('click', '.edit_po', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>EDIT RFQ PRINT ["+$(this).data('no_rfq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_edit_rfq_print/'+$(this).data('no_rfq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

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

	$(document).on('click', '.edit_po2', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>EDIT SUPPLIER ["+$(this).data('no_rfq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_edit_rfq/'+$(this).data('no_rfq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

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
	
	$(document).on('click', '#edit_po', function(e){
		e.preventDefault();

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
					var formData 	=new FormData($('#form_proses_bro')[0]);
					var baseurl=base_url + active_controller +'/modal_edit_rfq';
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
								window.location.href = base_url + active_controller + '/material_purchase';
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#edit_po').prop('disabled',false);
						},
						error: function() {

							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',
							  type				: "warning",
							  timer				: 7000
							});
							$('#edit_po').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#edit_po').prop('disabled',false);
				return false;
			  }
		});
	});

	//EditPurchase
	$(document).on('click', '#updatePur', function(){

		swal({ 
			title: "Are you sure?",
			text: "You will booking material planning be able to process again this data!",
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
					url			: base_url + active_controller+'/update_po',
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
							window.location.href = base_url + active_controller+'/material_purchase';
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

	$(document).on('click', '.cancelPO', function(){
		var no_po = $(this).data('id_bq');
		// alert(no_po);
		// return false;
		swal({ 
			title: "Are you sure?",
			text: "You will cancel material planning be able to process again this data!",
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
					url			: base_url + active_controller+'/cancel_po/'+no_po,
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
							window.location.href = base_url + active_controller+'/material_purchase';
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

	$(document).on('click', '.delMat', function(){
		var no_pr 		= $(this).data('no_pr');
		var id_material = $(this).data('id_material');
		var no_rfq = $(this).data('no_rfq');
		// alert(no_po);
		// return false;

		swal({ 
			title: "Are you sure?",
			text: "You will delete material planning be able to process again this data!",
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
					url			: base_url + active_controller+'/cancel_sebagian_po/'+no_pr+'/'+id_material+'/'+no_rfq,
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
							// window.location.href = base_url + active_controller+'/material_purchase';
							$("#head_title2").html("<b>EDIT MATERIAL PURCHASE ["+data.no_po+"]</b>");
							$("#view2").load(base_url +'index.php/'+ active_controller+'/modal_edit_po/'+data.no_po);
							$("#ModalView2").modal();
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

	//EditPurchase
	$(document).on('click', '#updateSupplier', function(){

		swal({ 
			title: "Are you sure?",
			text: "You will booking material planning be able to process again this data!",
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
					url			: base_url + active_controller+'/update_rfq_supplier',
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
							window.location.href = base_url + active_controller+'/material_purchase';
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

	$(document).on('click', '.deletePRMaterial', function(){
		var no_pr 		= $(this).data('no_pr');
		var id_material = $(this).data('id_barang');
		var no_rfq = $(this).data('no_rfq');
		// alert(no_po);
		// return false;

		swal({ 
			title: "Are you sure?",
			text: "You will delete material planning be able to process again this data!",
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
					url			: base_url + active_controller+'/cancel_sebagian_rfq/'+no_pr+'/'+id_material+'/'+no_rfq,
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
							DataTables();
							$("#head_title").html("<b>EDIT SUPPLIER ["+data.no_rfq+"]</b>");
							$("#view").load(base_url + active_controller+'/modal_edit_rfq/'+data.no_rfq);
							$("#ModalView").modal();
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

	function DataTables(){
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
				url : base_url + active_controller+'/server_side_po',
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

	function DataTables3(){
		var dataTable = $('#my-grid3').DataTable({
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
				url : base_url + active_controller+'/server_side_list_pr',
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
