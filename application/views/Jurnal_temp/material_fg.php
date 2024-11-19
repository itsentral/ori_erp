<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
		<div class="box-body">
			<div>
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class='active'><a href="#material" class='material' aria-controls="material" role="tab" data-toggle="tab">Jurnal Open</a></li>
					<li role="presentation"><a href="#approval" class='approval' aria-controls="approval" role="tab" data-toggle="tab">Jurnal Closing</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="material"><br>
						<?php
							echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 0px 0px 5px 0px;','content'=>'Closing Jurnal','id'=>'approvedQ'));
						?>
						<br><br>
						<div class="table-responsive">
						<table class="table table-bordered table-striped" id="my-grid" width='100%'>
							<thead class='thead'>
								<tr class='bg-blue'>
									<th class="text-center th">#</th>
									<th class="text-center th no-sort">Tanggal</th>
									<th class="text-center th no-sort">No Lap. Produksi</th>
									<th class="text-center th no-sort">No SO</th>
									<th class="text-center th no-sort">Product</th>
									<th class="text-center th no-sort">Spec</th>
									<th class="text-center th no-sort">Nilai Material</th>
									<th class="text-center th no-sort">Material</th>
									<!-- <th class="text-center th no-sort">Material Name</th>
									<th class="text-center th no-sort">Qty</th>
									<th class="text-center th no-sort">Cost Book</th>
									<th class="text-center th no-sort">Total Nilai</th>
									<th class="text-center th no-sort"><?=$gudang_awal;?></th>
									<th class="text-center th no-sort"><?=$gudang_akhir;?></th> -->
									<th class="text-center th no-sort">By</th>
									<th class="text-center th no-sort">Dated</th>
									<th class="text-center th no-sort"><input type='checkbox' name='chk_all' id='chk_all'></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="approval"><br>
						<div class="table-responsive">
						<table class="table table-bordered table-striped" id="my-grid2" width='100%'>
							<thead class='thead'>
								<tr class='bg-blue'>
								<th class="text-center th">#</th>
									<th class="text-center th no-sort">Tanggal</th>
									<th class="text-center th no-sort">No Lap. Produksi</th>
									<th class="text-center th no-sort">No SO</th>
									<th class="text-center th no-sort">Product</th>
									<th class="text-center th no-sort">Spec</th>
									<th class="text-center th no-sort">Nilai Material</th>
									<th class="text-center th no-sort">Material</th>
									<!-- <th class="text-center th no-sort">Material Name</th>
									<th class="text-center th no-sort">Qty</th>
									<th class="text-center th no-sort">Cost Book</th>
									<th class="text-center th no-sort">Total Nilai</th>
									<th class="text-center th no-sort"><?=$gudang_awal;?></th>
									<th class="text-center th no-sort"><?=$gudang_akhir;?></th> -->
									<th class="text-center th no-sort">Closing Date</th>
									<th class="text-center th no-sort">Kode</th>
									<th class="text-center th no-sort">View Jurnal</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						</div>
					</div>
				</div>

			</div>
				
		</div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
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
	
	
	<div class="modal modal-default fade" id="dialog-popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg" style='width:100%;' role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-users"></span>&nbsp;Detail Jurnal</h4>
		  </div>
		  <div class="modal-body" id="ModalView">
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">
			<span class="glyphicon glyphicon-remove"></span>  Close</button>
			 
		 </div>
	    </div>
	  </div>
	
	</div>
  

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		DataTables();
		DataTables2();

		$("#chk_all").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});

		$(document).on('click', '#approvedQ', function(){
			
			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist data closing',
					type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
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
						url			: base_url+active_controller+'/closing_jurnal_material_fg',
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
								window.location.href = base_url + active_controller+'/material_fg';
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

		$(document).on('click', '.detail_material', function(){
			var id_milik 	= $(this).data('id_milik');
			var id_spk 		= $(this).data('id_spk');
			$("#head_title2").html("<b>DETAIL MATERIAL</b>");
			loading_spinner();
			$.ajax({
				type:'POST',
				url:base_url+active_controller+'/modal_detail_material_fg/'+id_milik+'/'+id_spk,
				success:function(data){
					$("#ModalView2").modal();
					$("#view2").html(data);
				},
				error: function() {
					swal({
					title	: "Error Message !",
					text	: 'Connection Timed Out ...',
					type	: "warning",
					timer	: 5000,
					});
				}
			})
		});

	});
		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"lengthChange": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url + active_controller+'/server_side_material_fg',
				type: "post",
				// data: function(d){
				// 	d.id = id
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

	function DataTables2(){
		var dataTable2 = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"lengthChange": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url + active_controller+'/server_side_material_close_fg',
				type: "post",
				// data: function(d){
				// 	d.id = id
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
	
	$(document).on('click', '.view2', function(){
		var id_milik = $(this).data('id_milik');
		var total_sum = $(this).data('total_sum');
		var id_spk = $(this).data('id_spk');
		var tanggal = $(this).data('tanggal');
		var pp = 'MATERIAL - FINISH GOOD';
		var kd ='JV004'
		var akses = 'material_fg';
		loading_spinner();
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_material_fg/',
			data:{'id_milik':id_milik,'total_sum':total_sum,'id_spk':id_spk,'tanggal':tanggal,'pp':pp,'kd':kd,'akses':akses},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
				swal.close();
			}
		})
	});

		$(document).on('click', '.view9', function(){
		var id_milik = $(this).data('id_milik');
		var total_sum = $(this).data('total_sum');
		var id_spk = $(this).data('id_spk');
		var tanggal = $(this).data('tanggal');
		var pp = 'MATERIAL - FINISH GOOD';
		var kd ='JV004'
		var akses = 'material_fg';
		loading_spinner();
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_material_fg_history/',
			data:{'id_milik':id_milik,'total_sum':total_sum,'id_spk':id_spk,'tanggal':tanggal,'pp':pp,'kd':kd,'akses':akses},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
				swal.close();
			}
		})
	});
$('#dialog-popup').on('hidden.bs.modal', function (e) {
  $('#my-grid2').DataTable().ajax.reload( null, false )
})
	
</script>
