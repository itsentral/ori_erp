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
				  <a href="<?php echo site_url('Mould_n_mandrill/add') ?>" class="btn btn-sm btn-success" id='btn-add'>
					<i class="fa fa-plus"></i> New Mould & Mandrill
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
						<th width='5%' class="text-center" style='vertical-align: middle;'>No</th>
						<th width='16%' class="text-center" style='vertical-align: middle;'>Product</th>
						<th width='9%' class="text-center" style='vertical-align: middle;'>Diameter 1</th>
						<th width='9%' class="text-center" style='vertical-align: middle;'>Diameter 2</th>
						<th width='9%' class="text-center" style='vertical-align: middle;'>Dimensi</th>
						<th width='10%' class="text-center" style='vertical-align: middle;'>Harga (USD)</th>
						<th width='18%' class="text-center" style='vertical-align: middle;'>Estimasi Pemakaian (Pcs)</th>
						<th width='12%' class="text-center" style='vertical-align: middle;'>Biaya /Pcs  (USD)</th>
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
			<div class="modal-dialog"  style='width:85%; '>
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


		$(document).on('click', '#deleteM', function(){
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

		$(document).on('click', '#viewM', function(e){
			// loading_spinner();
			var id = $(this).data('id');
			e.preventDefault();
			$("#head_title").html("<b>DETAIL MOULD & MANDRILL</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+id);
			$("#ModalView").modal();
		});

		$(document).on('click', '#editM', function(e){
			// loading_spinner();
			var id = $(this).data('id');
			e.preventDefault();
			$("#head_title").html("<b>EDIT MOULD & MANDRILL</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalEdit/'+id);
			$("#ModalView").modal();
		});

		

	});

	function DataTables(){

		var dataTable = $('#example1').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
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
				url : base_url +'index.php/'+active_controller+'/getDataJSON',
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
