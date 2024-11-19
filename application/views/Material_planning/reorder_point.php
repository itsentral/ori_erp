<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-danger','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 5px;','content'=>'Clear Propose Request','id'=>'autoDelete'));
		?>
		</div><br><br>
		<div class="box-tool pull-right">
				<label for="tgl_butuh"><b>Tanggal Dibutuhkan</b></label>
				<?php
					$tgl_now = date('Y-m-d');
					$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
					echo form_input(array('id'=>'tgl_butuh','name'=>'tgl_butuh','class'=>'form-control input-md text-center tgl changeSaveDate','readonly'=>'readonly','placeholder'=>'Tanggal Dibutuhkan'),$tgl_next_month);
				?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center mid" width='3%'>#</th>
					<th class="text-center mid" width='10%'>Id Material</th>
					<th class="text-center mid">Material</th>
					<th class="text-center mid" width='6%'>Category</th>
					<!-- <th class="text-center mid" width='6%'>Stock Actual</th> -->
					
					<th class="text-center mid" width='8%'>Stock Free</th>
					<th class="text-center mid" width='8%'>Min Stock</th>
					<th class="text-center mid" width='8%'>Max Stock</th>
					<!-- <th class="text-center mid" width='8%'>QTR Order</th> -->
					<th class="text-center mid" width='8%'>Min Order</th>
					<th class="text-center mid" width='8%'>PR On Process</th>
					<th class="text-center mid no-sort" width='8%'>Propose Request</th>
					<!-- <th class="text-center mid no-sort" width='8%'>Tgl Dibutuhkan</th> -->
					<!-- <th class="text-center mid no-sort" width='3%'>#</th> -->
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Purchase Request','id'=>'saveRequest')).' ';
		?>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.tgl{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
        DataTables();
        $('.maskM').maskMoney();
		$('.tgl').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
    });
	
	$(document).on('click','.save_pr', function(){
		var nomor 		= $(this).data('no');
		var id_material = $(this).data('id_material');
		var purchase 	= $('#purchase_'+nomor).val().split(",").join("");
		var tanggal 	= $('#tgl_butuh').val();
		
		var moq 			= $('#moq_'+nomor).val();
		var reorder_point 	= $('#reorder_point_'+nomor).val();
		var sisa_avl 		= $('#sisa_avl_'+nomor).val();
		var book_per_month 	= $('#book_per_month_'+nomor).val();
	
		if(purchase==''|| purchase=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Qty Purchase is empty, please input first ...',
			  type	: "warning"
			});
			return false;
		}
		
		if(tanggal=='' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Date digunakan is empty, please input first ...',
			  type	: "warning"
			});
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
				$.ajax({
					url			: base_url + active_controller+'/save_reorder_point',
					type		: "POST",
					data: {
						"id_material" 	: id_material,
						"purchase" 		: purchase,
						"tanggal" 		: tanggal,
						"moq" 			: moq,
						"reorder_point"	: reorder_point,
						"sisa_avl" 		: sisa_avl,
						"book_per_month": book_per_month
					},
					cache		: false,
					dataType	: 'json',
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
							window.location.href = base_url + active_controller+'/reorder_point';
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
    //NEW SAVE ALL
	$(document).on('change','.changeSave', function(){
		var nomor 		= $(this).data('no');
		var id_material = $(this).data('id_material');
		var purchase 	= $('#purchase_'+nomor).val().split(",").join("");
		var moq 			= $('#moq_'+nomor).val().split(",").join("");
		var reorder_point 	= $('#reorder_point_'+nomor).val().split(",").join("");
		var sisa_avl 		= $('#sisa_avl_'+nomor).val().split(",").join("");
		var book_per_month 	= $('#book_per_month_'+nomor).val().split(",").join("");
		var tanggal 	= $('#tgl_butuh').val();
		
		$.ajax({
			url			: base_url + active_controller+'/save_reorder_change',
			type		: "POST",
			data: {
				"id_material" 	: id_material,
				"purchase" 		: purchase,
				"moq" 			: moq,
				"reorder_point" : reorder_point,
				"sisa_avl" 		: sisa_avl,
				"book_per_month": book_per_month,
				"tanggal" 		: tanggal
			},
			cache		: false,
			dataType	: 'json',
			success		: function(data){
				console.log(data.pesan)
			},
			error: function() {
				console.log('error connection serve !')
			}
		});
	});

	$(document).on('change','.changeSaveDate', function(){
		var tanggal 	= $('#tgl_butuh').val();
		
		$.ajax({
			url			: base_url + active_controller+'/save_reorder_change_date',
			type		: "POST",
			data: {
				"tanggal" 		: tanggal
			},
			cache		: false,
			dataType	: 'json',
			success		: function(data){
				console.log(data.pesan)
			},
			error: function() {
				console.log('error connection serve !')
			}
		});
	});

	$(document).on('click','#autoDelete', function(){
		
		swal({
			title: "Are you sure?",
			text: "Clear All Propose Request !!!",
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
					url			: base_url + active_controller+'/clear_update_reorder',
					type		: "POST",
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
							window.location.href = base_url + active_controller+'/reorder_point';
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

	$(document).on('click','#saveRequest', function(){
		
		swal({
			title: "Are you sure?",
			text: "Membuat semua Propose Material !!!",
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
					url			: base_url + active_controller+'/save_reorder_all',
					type		: "POST",
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
							window.location.href = base_url + active_controller+'/reorder_point';
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

		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 2, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_reorder_point',
				type: "post",
				// data: function(d){
					// d.gudang = $('#gudang').val()
				// },
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
