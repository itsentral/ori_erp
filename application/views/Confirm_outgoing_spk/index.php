<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<input type='hidden' id='uri_tanda' value='<?=$uri_tanda;?>'>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		
		<div class="box-tool pull-right">
			<select id='pusat' name='pusat' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Gudang Dari</option>
				<?php
					foreach($pusat AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
					}
				?>
			</select>
			<select id='subgudang' name='subgudang' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Gudang Ke</option>
				<?php
					foreach($subgudang AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
					}
				?>
			</select>
		</div>
		<br><br>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">Date Transaction</th>
					<th class="text-center">Gudang Dari</th>
					<th class="text-center">Gudang Ke</th>
					<th class="text-center">Created</th>
					<th class="text-center">Dated</th>
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
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:85%; '>
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
	#tanggal_trans{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		var pusat 		= $('#pusat').val();
		var subgudang 	= $('#subgudang').val();
		DataTables(pusat,subgudang);
		
		$(document).on('change','#pusat, #subgudang', function(e){
			e.preventDefault();
			var pusat 		= $('#pusat').val();
			var subgudang 	= $('#subgudang').val();
			DataTables(pusat,subgudang);
		});

		$(document).on('click', '.createspk', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title2").html("<b>CREATE SPK</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modal_confirm_spk/'+$(this).data('kode_trans'),
				success:function(data){
					$("#ModalView2").modal();
					$("#view2").html(data);
					$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
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

		$(document).on('click', '.detailspk', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title2").html("<b>DETIL CONFIRM</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modal_confirm_spk/'+$(this).data('kode_trans')+'/view',
				success:function(data){
					$("#ModalView2").modal();
					$("#view2").html(data);
					$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
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

		$(document).on('click', '#create_spk', function(){
			$('#create_spk').prop('disabled',true);

			
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
					var formData  	= new FormData($('#form_adjustment')[0]);
					$.ajax({
						url			: base_url + active_controller+'/modal_confirm_spk',
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
								window.location.href = base_url + active_controller
							}
							else if(data.status == 0){
								swal({
									title	: "Save Failed!",
									text	: data.pesan,
									type	: "warning",
									timer	: 7000
								});
								$('#create_spk').prop('disabled',false);
							}
						},
						error: function() {
							swal({
								title	: "Error Message !",
								text	: 'An Error Occured During Process. Please try again..',						
								type	: "warning",								  
								timer	: 7000
							});
							$('#create_spk').prop('disabled',false);
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#create_spk').prop('disabled',false);
				return false;
				}
			});
		});

		$(document).on('keypress', '#qr_code', function(e) {
			const input = $(this)
			if (e.keyCode == '13') {
				var formData = new FormData($('#form_adjustment')[0]);
				$.ajax({
					url: base_url + active_controller + '/check_qr',
					type: "POST",
					data: formData,
					cache: false,
					dataType: 'json',
					processData: false,
					contentType: false,
					success: function(data) {
						if (data.status == 1) {
							swal({
								title: "Success!",
								text: data.pesan,
								type: "success",
								timer: 3000
							});

							$('#request_'+data.id_spk).val(data.qty_spk)

							$('.notif').fadeIn('slow').html(`
								<div class="alert alert-info" role="alert">
								<h4 class="alert-heading">Scan Berhasil!</h4>
								<p>` + input.val() + `</p>
								</div>
								`)

							input.val('').focus();
							setTimeout(function() {
								$('.notif').fadeToggle('slow')
							}, 7000)
							// window.location.href = base_url + active_controller;
						} else {
							swal({
								title: "Failed!",
								text: data.pesan,
								type: "warning",
								timer: 3000
							});

							$('.notif').fadeIn('slow').html(`
								<div class="alert alert-warning" role="alert">
								<h4 class="alert-heading">Product salah!</h4>
								<p>` + data.pesan + `</p>
								</div>
								`)

							input.val('').focus();
							setTimeout(function() {
								$('.notif').fadeToggle('slow')
							}, 7000)
						}
					},
					error: function() {
						swal({
							title: "Error Message !",
							text: 'An Error Occured During Process. Please try again..',
							type: "error",
							timer: 3000
						});
					}
				});
			}
		})
		
	});


	function DataTables(pusat=null,subgudang=null){
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
				url : base_url + active_controller+'/server_side_outgoing_spk',
				type: "post",
				data: function(d){
					d.pusat = pusat,
					d.subgudang = subgudang,
					d.tanda = 'request subgudang'
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
