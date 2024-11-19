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
		<table class="table table-bordered table-striped" id="my-grid" width="100%">
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
	
	$(document).on('click', '.edit', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>EDIT STRUCTURE BQ IN FINAL DRAWING ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_edit_bq/'+$(this).data('id_bq')+'/'+$(this).data('ciri'),
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
	
	//DELETE SEBAGIAN BQ
	$(document).on('click', '.del', function(){
		var bF	= $(this).data('id');
		var bF2	= $(this).data('id_bq_header');
		// alert(bF);
		// return false;
		swal({
		  title: "Are you sure?",
		  text: "Data akan terhapus secara permanen ?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Yes, Process it!",
		  cancelButtonText: "No, cancel process!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/delete_sebagian_bq/'+bF+'/'+bF2,
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								
							$("#head_title").html("<b>EDIT STRUCTURE BQ ["+data.id_bq+"]</b>");
							$.ajax({
								type:'POST',
								url: base_url + active_controller+'/modal_edit_bq/'+data.id_bq,
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
	
	//SAVE EDIT BQ
	$(document).on('click', '#update_bq', function(e){
		e.preventDefault();
		// alert('Des');
		$(this).prop('disabled',true);
		
		var intL = 0;
		var intError = 0;
		var pesan = '';
		
		$('#detail_bodyTop').find('tr').each(function(){
			intL++;
			var findId		= $(this).attr('id');
			var nomor		= findId.split('_');
			var qty			= $('#qty_'+nomor[1]).val();
			var sudut		= $('#sudut_'+nomor[1]).val();
			var thickness	= $('#thickness_'+nomor[1]).val();
			var length		= $('#length_'+nomor[1]).val();
			var diameter_2	= $('#diameter_2_'+nomor[1]).val();
			var diameter_1	= $('#diameter_1_'+nomor[1]).val();
			
			if(qty == '' || qty == 0 || qty == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Qty has not empty ...";
			}
			if(sudut == '' ||  sudut == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Angle type has not empty ...";
			}
			if(thickness == '' || thickness == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Thickness type has not empty ...";
			}
			if(length == '' || length == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Length type has not empty ...";
			}
			if(diameter_2 == '' || diameter_2 == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Diameter 2 type has not empty ...";
			}
			if(diameter_1 == '' || diameter_1 == 0 || diameter_1 == null){
				intError++;
				pesan = "Number "+nomor[1]+" : Diameter 1 type has not empty ...";
			}
		});
		
		if(intError > 0){
			swal({
				title	: "Notification Message !",
				text	: pesan,						
				type	: "warning"
			});
			$('#update_bq').prop('disabled',false);
			return false;
		}
		
		$('#update_bq').prop('disabled',false);
		
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
					var formData 	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url + active_controller +'/update_bq';
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
								window.location.href = base_url + active_controller;
							}
							else if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#update_bq').prop('disabled',false);
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
							$('#update_bq').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#update_bq').prop('disabled',false);
				return false;
			  }
		});
	});
	
	$(document).on('click', '.ajukan', function(){
		var bq		= $(this).data('id_bq');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Menuju ke proses estimasi ...",
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
					url			: base_url + active_controller+'/ajukan_bq/'+bq,
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
		
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
				url : base_url + active_controller+'/server_side_fd_bq',
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
