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
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='6%'>IPP</th>
					<th class="text-center" width='15%'>Customer</th>
					<th class="text-center" width='9%'>Quotation</th>
					<th class="text-center" >Project</th>
					<th class="text-center" width='5%'>Type</th>
					<th class="text-center no-sort" width='6%'>Series</th>
					<th class="text-center no-sort" width='6%'>Weight&nbsp;(Kg)</th>
					<th class="text-center no-sort" width='6%'>Project</th>
					<th class="text-center no-sort" width='4%'>Rev</th>
					<th class="text-center no-sort" width='10%'>Status</th>
					<th class="text-center no-sort" width='16%'>Option</th>
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
<script>
	$(document).ready(function(){
		DataTables();
	});
	
	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL QUOTATION ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail_quotation/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',						
				  type				: "warning",								  
				  timer				: 7000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click', '.detail_material', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>VIEW MATERIAL ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_view_material/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',						
				  type				: "warning",								  
				  timer				: 7000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click', '#viewDT', function(e){
		e.preventDefault();
		$("#head_title").html("<b>DETAIL DATA BQ ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modalviewDT/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',						
				  type				: "warning",								  
				  timer				: 7000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click', '#modal_hist', function(e){
		e.preventDefault();
		$("#head_title").html("<b>HISTORY BQ ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modalHist/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',						
				  type				: "warning",								  
				  timer				: 7000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click', '.ApproveDTNew', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>APPROVE CUSTOMER DEAL ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url+active_controller+'/modal_approve_quotation/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',						
				  type				: "warning",								  
				  timer				: 7000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
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
			"autoWidth": false,
			"processing": true,
			"destroy": true,
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
				url : base_url + active_controller+'/server_side_quotation',
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
