<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<div class="box-tool pull-right">
			<?php
				if($akses_menu['create']=='1'){		?>
				  <a href="<?php echo site_url('Master_machine/add') ?>" class="btn btn-sm btn-success" id='btn-add'>
					<i class="fa fa-plus"></i> New Machine
				  </a>
			  <?php
				}
			  ?>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table id="example1" width='100%' class="table table-bordered table-striped">
				<thead>
					<tr class='bg-blue'>
						<th width='5%' class="text-center" style='vertical-align: middle;'>#</th>
						<th width='9%' class="text-center" style='vertical-align: middle;'>No. Mesin</th>
						<th width='19%' class="text-center" style='vertical-align: middle;'>Mesin</th>
						<th width='10%' class="text-center" style='vertical-align: middle;'>Kapasitas</th>
						<th width='5%' class="text-center" style='vertical-align: middle;'>Unit</th>
						<th width='10%' class="text-center" style='vertical-align: middle;'>Harga Mesin (USD)</th>
						<th width='10%' class="text-center" style='vertical-align: middle;'>Estimasi Pemanfaatan (Tahun)</th>
						<th width='10%' class="text-center" style='vertical-align: middle;'>Depresiasi /Bulan  (USD)</th>
						<th width='10%' class="text-center" style='vertical-align: middle;'>Biaya Mesin /Jam (USD)</th>
						<th width='12%' class="text-center" style='vertical-align: middle;'>Option</th>
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
			<div class="modal-dialog"  style='width:85%;'  style='overflow-y: auto;'> 
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="head_title"></h4>
						</div>
						<div class="modal-body" id="view">
						</div>
						<div class="modal-footer">
						<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="ModalView2">
			<div class="modal-dialog"  style='width:30%;'  style='overflow-y: auto;'>
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

		$(document).ready(function(){
			var series = $('#series').val();
			var komponen = $('#komponen').val();
			DataTables();
		});


		$('#btn-add').click(function(){
			loading_spinner();
		});

		$(document).on('click', '#MatDetail', function(e){
			e.preventDefault();
			$("#head_title").html("<b>DETAIL ESTIMATION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});

		$(document).on('click', '#del_type', function(){
			var bF	= $(this).data('id');
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
						url			: base_url+'index.php/'+active_controller+'/hapus/'+bF,
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
				swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
				return false;
				}
			});
		});

		$(document).on('click', '#StepDetail', function(e){
			var id = $(this).data('id');
			e.preventDefault();
			$("#head_title").html("<b>DETAIL MACHINE</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalView_Step/'+id);
			$("#ModalView").modal();
		});

		$(document).on('click', '#StepEdit', function(e){
			var id = $(this).data('id');
			e.preventDefault();
			$("#head_title").html("<b>DETAIL STEP</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalEdit_Step/'+id);
			$("#ModalView").modal();
		});


		$(document).on('click', '#addPSave', function(){
			var standart_code			= $('#standart_codex').val();

			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Default Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
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
						url			: base_url+'index.php/'+active_controller+'/addPSave',
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
								// window.location.href = base_url + active_controller+'/'+data_url;
								$("#ModalView2").modal('hide');
								$("#head_title").html("<b>ADD DEFAULT</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAdd_Step/');
								$("#ModalView").modal();


							}
							else{
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

		$(document).on('click', '#addStepSave', function(){
			var step_name			= $('#step_name').val();

			if(step_name == '' || step_name == null || step_name == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Step Input is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
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
						url			: base_url+'index.php/'+active_controller+'/addStepSave_Master',
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
								// window.location.href = base_url + active_controller+'/'+data_url;
								$("#ModalView2").modal('hide');
								$("#head_title").html("<b>ADD DEFAULT</b>");
								/*$("#select_step").each(function() {

								});*/
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAdd_Step/');
								$("#ModalView").modal();


							}
							else{
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

		$(document).on('click', '#addDefaultSave', function(){
			var komponen		= $('#komponen').val();
			var standart_code	= $('#standart_code').val();
			var diameter		= $('#diameter').val();
			var diameter2		= $('#diameter2').val();
			var pn				= $('#pn').val();
			var liner				= $('#liner').val();
			var standard_length	= $('#standard_length').val();

			if(komponen == '' || komponen == null || komponen == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Product Namex is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;
			}
			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Default Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;
			}
			if(diameter == '' || diameter == null || diameter == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;
			}
			if(diameter2 == '' || diameter2 == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter 2 is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;
			}
			if(pn == '' || pn == null || pn == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Plessure is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;
			}
			if(liner == '' || liner == null || liner == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Liner is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
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
						url			: base_url+'index.php/'+active_controller+'/addStepSave',
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
								window.location.href = base_url + active_controller+'/standard_step';
								// $("#ModalView2").modal('hide');
								// $("#head_title").html("<b>ADD DEFAULT</b>");
								// $("#view").load(base_url +'index.php/'+ active_controller+'/modalAddDefault/');
								// $("#ModalView").modal();


							}
							else{
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

		$(document).on('click', '#editDefaultSave', function(){
			var komponen		= $('#komponen').val();
			var standart_code	= $('#standart_code').val();
			var diameter		= $('#diameter').val();
			var diameter2		= $('#diameter2').val();
			var pn				= $('#pn').val();
			var liner				= $('#liner').val();
			var standard_length	= $('#standard_length').val();

			if(komponen == '' || komponen == null || komponen == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Product Namex is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;
			}
			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Default Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;
			}
			if(diameter == '' || diameter == null || diameter == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;
			}
			if(diameter2 == '' || diameter2 == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter 2 is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;
			}
			if(pn == '' || pn == null || pn == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Plessure is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;
			}
			if(liner == '' || liner == null || liner == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Liner is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
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
						url			: base_url+'index.php/'+active_controller+'/editStepSave',
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
								window.location.href = base_url + active_controller+'/standard_step';
								// $("#ModalView2").modal('hide');
								// $("#head_title").html("<b>ADD DEFAULT</b>");
								// $("#view").load(base_url +'index.php/'+ active_controller+'/modalAddDefault/');
								// $("#ModalView").modal();


							}
							else{
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

	function DataTables(){

		var dataTable = $('#example1').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"processing": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url+active_controller+'/getDataJSON',
				type: "post",
				data: function(d){
					d.sts_mesin 	= 'Y'
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
