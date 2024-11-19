<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-left">
			<input type='hidden' id='engine' name='engine' value='<?=$engine;?>'>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='4%'>No</th>
					<th class="text-center" width='7%'>IPP</th>
					<th class="text-center" width='7%'>No SO</th>
					<th class="text-center" width='18%'>Customer</th>
					<th class="text-center" width='24%'>Project</th>
					<th class="text-center" width='5%'>Type</th>
					<th class="text-center" width='5%'>Rev</th>
					<th class="text-center" width='8%'>Progress</th>
					<th class="text-center" width='10%'>Status</th>
					<th class="text-center" width='12%'>Option</th>
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
		<div class="modal-dialog"  style='width:95%; '>
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
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var engine = $('#engine').val();
		DataTables(engine);
		
		
		// console.log(engine);
		$(document).on('change','#spool', function(){
			if ($('#spool').is(':checked')) {
				$('.choseSP').show();
			}
			else{
				$('.choseSP').hide();
			}
		});
		
		$(document).on('click','.addPart', function(){
			var get_id 		= $(this).parent().parent().attr('id');
            var split_id	= get_id.split('_');
            var id 			= parseInt(split_id[1])+1;
            var id_bef 		= split_id[1];
			
			Append_add(id,id_bef);
		});
		
		$(document).on('click', '.detail_so', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL ["+$(this).data('id_bq')+"]</b>");
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
		
		$(document).on('click', '.choose_so', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title2").html("<b>TENTUKAN CATEGORY ["+$(this).data('id_bq')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modal_choose_so/'+$(this).data('id_bq'),
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
		
		$(document).on('click', '.edit_choose_so', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title2").html("<b>UPLOAD ULANG ["+$(this).data('id_bq')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modal_edit_choose_so/'+$(this).data('id_bq'),
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
		
		$(document).on('click', '#download', function(){
			var no_ipp = $('#no_ipp').val();
			var Link	= base_url + active_controller +'/temp_format/'+no_ipp;
			window.open(Link);
		});
		
		$(document).on('click', '.detailDT', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title2").html("<b>DETAIL ["+$(this).data('id_product')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url +'machine/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('length'),
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
		
		$(document).on('click', '.detailX', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title2").html("<b>DETAIL ["+$(this).data('id_product')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url +'machine/modalDetailX/'+$(this).data('id_bq')+'/'+$(this).data('id_milik'),
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
		
		$(document).on('click', '#import_data', function(e){
			e.preventDefault();
			var excel_file = $('#excel_file').val();
			if(excel_file == '' || excel_file == null){
				swal({
				  title	: "Error Message!",
				  text	: 'File upload is Empty, please choose file first...',
				  type	: "warning"
				});
				// $('#simpan-bro').prop('disabled',false);
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
					var baseurl		= base_url + active_controller +'/import_data';
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
								window.location.href = base_url + active_controller +'/so/'+data.engine;
							}
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#import_data').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#import_data').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#import_data').prop('disabled',false);
				return false;
			  }
			});
		});
		
		$(document).on('click', '#import_data2', function(e){
			e.preventDefault();
			var excel_file = $('#excel_file').val();
			if(excel_file == '' || excel_file == null){
				swal({
				  title	: "Error Message!",
				  text	: 'File upload is Empty, please choose file first...',
				  type	: "warning"
				});
				// $('#simpan-bro').prop('disabled',false);
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
					var baseurl		= base_url + active_controller +'/import_data2';
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
								window.location.href = base_url + active_controller +'/so/'+data.engine;
							}
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#import_data2').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#import_data2').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#import_data2').prop('disabled',false);
				return false;
			  }
			});
		});
		
		$(document).on('click', '#update_spool', function(e){
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
					var formData  	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url + active_controller +'/update_spool';
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
								window.location.href = base_url + active_controller +'/so/'+data.engine;
							}
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#update_spool').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#update_spool').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#update_spool').prop('disabled',false);
				return false;
			  }
			});
		});
		
		$(document).on('click', '#save_new_spool', function(e){
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
					var formData  	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url + active_controller +'/save_new_spool';
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
								window.location.href = base_url + active_controller +'/so/'+data.engine;
							}
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#save_new_spool').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save_new_spool').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_new_spool').prop('disabled',false);
				return false;
			  }
			});
		});
		
		$(document).on('click', '#save_category', function(e){
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
					var formData  	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url + active_controller +'/save_category';
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
								window.location.href = base_url + active_controller +'/so/'+data.engine;
							}
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#save_category').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save_category').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_category').prop('disabled',false);
				return false;
			  }
			});
		});
		
		$(document).on('click', '.delPart', function(){
            $(this).parent().parent().remove();
        });
		
		$(document).on('click', '.deletePermanent', function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var no_ipp = $('#no_ipp').val();
			
			swal({
			  title: "Are you sure?",
			  text: "Data akan langsung terhapus Permanent !!!",
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
					var baseurl		= base_url + active_controller +'/delete_spool_satuan/'+id+'/'+no_ipp;
					$.ajax({
						url			: baseurl,
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
								loading_spinner();
								$("#head_title2").html("<b>UPLOAD ULANG ["+$(this).data('id_bq')+"]</b>");
								$.ajax({
									type:'POST',
									url: base_url + active_controller+'/modal_edit_choose_so/'+data.no_ipp,
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
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
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
	});
		
	function DataTables(engine=null){
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
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
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
				url : base_url + active_controller+'/server_side_schedule_so/'+engine,
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
	
	function Append_add(id, id_bef){

        var Rows	 = 	"<tr>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][spool]' class='form-control input-md text-center'></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][id_spool]' class='form-control input-md'></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][id_product]' class='form-control input-md'></td>";
			Rows	+= 		"<td><select name='detail_add["+id+"][nm_product]' id='nm_product_"+id+"' class='form-control chosen-select'></select></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][d1]' class='form-control input-md text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][d2]' class='form-control input-md text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][thickness]' class='form-control input-md text-right maskMoney'></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][length_sudut]' class='form-control input-md text-right maskMoney'></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][sr_lr]' class='form-control input-md text-center'></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][delivery_date]' class='form-control input-md text-center datepicker' readonly></td>";
			Rows	+= 		"<td><input type='text' name='detail_add["+id+"][keterangan]' class='form-control input-md'></td>";
			Rows	+= 		"<td><button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-trash'></i></button></td>";
			Rows	+= 	"</tr>";
			Rows	+= 	"<tr id='add_"+id+"'>";
			Rows	+= 		"<td></td>";
			Rows	+= 		"<td align='left' colspan='11'><button type='button' class='btn btn-sm btn-success addPart' title='Add' style='min-width:70px;'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</button></td>";
			Rows	+= 	"</tr>";

		$("#add_"+id_bef).before(Rows);
        $("#add_"+id_bef).remove();
        $('.maskMoney').maskMoney();
		$('.datepicker').datepicker({
			showButtonPanel: true,
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
		
		var id_product = $("#nm_product_"+id);
		
		$.ajax({
			url: base_url + active_controller+'/get_product',
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(id_product).html(data.option).trigger("chosen:updated");
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
		
		$('.chosen-select').chosen({width: '100%'});
		
		
		
	}
	
</script>
