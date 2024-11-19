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
			<input type="hidden" id='gudang' name='gudang' value='<?=$id;?>'>
			<div>
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class='active'><a href="#material" class='material' aria-controls="material" role="tab" data-toggle="tab">Jurnal Open</a></li>
					<li role="presentation"><a href="#approval" class='approval' aria-controls="approval" role="tab" data-toggle="tab">Jurnal Closing</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active table-responsive" id="material"><br>
						<?php
							echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 0px 0px 5px 0px;','content'=>'Closing Jurnal','id'=>'approvedQ'));
						?>
						<br><br>
						<table class="table table-bordered table-striped" id="my-grid" width='100%'>
							<thead class='thead'>
								<tr class='bg-blue'>
									<th class="text-center th">#</th>
									<th class="text-center th no-sort">Tanggal</th>
									<th class="text-center th no-sort">No Trans</th>
									<th class="text-center th no-sort">No SO</th>
									<th class="text-center th no-sort">Product</th>
									<th class="text-center th no-sort">Spec</th>
									<th class="text-center th no-sort">Total Nilai</th>
									<th class="text-center th no-sort"><?=$gudang_awal;?></th>
									<th class="text-center th no-sort"><?=$gudang_akhir;?></th>
									<th class="text-center th no-sort">Closing Date</th>
									<th class="text-center th no-sort"><input type='checkbox' name='chk_all' id='chk_all'></th>
									<!-- <th class="text-center th no-sort">View Jurnal</th>-->
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div role="tabpanel" class="tab-pane table-responsive" id="approval">
						<button type="button" class="btn btn-sm btn-danger pull-right" id="updbtn">Auto Jurnal</button><br />
						<table class="table table-bordered table-striped" id="my-grid2" width='100%'>
							<thead class='thead'>
								<tr class='bg-blue'>
									<th class="text-center th">#</th>
									<th class="text-center th no-sort">Tanggal</th>
									<th class="text-center th no-sort">No Trans</th>
									<th class="text-center th no-sort">No SO</th>
									<th class="text-center th no-sort">Product</th>
									<th class="text-center th no-sort">Spec</th>
									<th class="text-center th no-sort">Total Nilai</th>
									<th class="text-center th no-sort"><?=$gudang_awal;?></th>
									<th class="text-center th no-sort"><?=$gudang_akhir;?></th>
									<th class="text-center th no-sort">Closing Date</th>
									<th class="text-center th no-sort">No SJ</th>
									<th class="text-center th no-sort">View Jurnal
									<input type='checkbox' name='chk_all_2' id='chk_all_2'></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
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
		<div class="modal-dialog"  style='width:100%; '>
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


	<div class="modal modal-default fade" id="dialog-popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:hidden;">
	  <div class="modal-dialog modal-lg" style='width:80%;'>
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-users"></span>&nbsp;Detail Jurnal</h4>
		  </div>
		  <div class="modal-body" id="ModalView">
			...
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
	var autoj="";
	var elements = "";
	var i = 0;
	$("#updbtn").click(function(event) {
		 if(confirm("Update this data??")){
			$('.chk_personal').not(this).prop('checked', false);
			elements = $("input:checkbox:checked");
			i = 0;
			doLoop();
		 }
	});

	function doLoop() {
		if ( i >= elements.length ) {
			autoj="";
			return;
		}
		autoj="autoj";
		var id=$(elements[i]).val();
		var pp = 'WIP - FINISH GOOD';
		var kd = 'JV005';
		$('#spinner').show();
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_product/'+id+'/'+kd+'/'+pp+'/autoj',
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
				$("#row_"+id).html("");
			}
		})
	}

	$(document).ready(function(){
        let id = '<?=$id;?>';
		DataTables(id);
		DataTables2(id);

		$("#chk_all").click(function(){
			$('.chk_personal').not(this).prop('checked', this.checked);
		});
		$("#chk_all_2").click(function(){
			$('.chk_personal_2').not(this).prop('checked', this.checked);
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
						url			: base_url+active_controller+'/closing_jurnal_product',
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
								window.location.href = base_url + active_controller+'/product/'+data.gudang;
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

	});

	function DataTables(id=null){
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
				url : base_url + active_controller+'/server_side_product',
				type: "post",
				data: function(d){
					d.id = id
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

	function DataTables2(id=null){
		var dataTable = $('#my-grid2').DataTable({
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
				url : base_url + active_controller+'/server_side_product_close',
				type: "post",
				data: function(d){
					d.id = id
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

	$(document).on('click', '.view2', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'WIP - FINISH GOOD';
		var kd ='JV005'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_product/'+id+'/'+kd+'/'+pp,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
	});

	$(document).on('click', '.view21', function(){
		$('#spinner').show();
		var id = $(this).data('id_material');
		var pp = 'WIP - FINISH GOOD';
		var kd ='JV005'
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_product/'+id+'/'+kd+'/'+pp+'/autoj',
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
	});
	$(document).on('click', '.view25', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'WIP - FINISH GOOD';
		var kd ='JV005'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_only_jurnal_product/'+id+'/'+kd+'/'+pp,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
	});
	$(document).on('click', '.view3', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'pononstok';
		var kd ='JV006'
		var akses = 'approval_jurnal_po_nonstok';
		var ket ='FINISH GOOD - TRANSIT'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_intransit/'+id+'/'+kd+'/'+ket+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
	});

	$(document).on('click', '.view4', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'pononstok';
		var akses = 'approval_jurnal_po_nonstok';
		var kd ='JV007'
		var ket ='TRANSIT - CUSTOMER'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_incustomer/'+id+'/'+kd+'/'+ket+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);

			}
		})
	});

	$(document).on('click', '.viewfs', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'fg_spooling';
		var akses = 'finish_good_spooling';
		var kd ='JV057'
		var ket ='FINISH GOOD - SPOOLING WIP'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_fgspooling/'+id+'/'+kd+'/'+ket+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);

			}
		})
	});

	$(document).on('click', '.viewsf', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'spooling_fg';
		var akses = 'spooling_finish_good';
		var kd ='JV058'
		var ket ='SPOOLING WIP- FINISH GOOD'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_fgspooling/'+id+'/'+kd+'/'+ket+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);

			}
		})
	});

	$(document).on('click', '.viewfc', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'fg_cutting';
		var akses = 'finish_good_cutting';
		var kd ='JV059'
		var ket ='FINISH GOOD - CUTTING WIP'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_fgcutting/'+id+'/'+kd+'/'+ket+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);

			}
		})
	});
	$(document).on('click', '.viewcf', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'cutting_fg';
		var akses = 'cutting_finish_good';
		var kd ='JV060'
		var ket ='CUTTING WIP - FINISH GOOD'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_cuttingfg/'+id+'/'+kd+'/'+ket+'/'+akses+'/'+total+'/'+id_vendor+'/'+nm_vendor,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);

			}
		})
	});
	
$('#dialog-popup').on('hidden.bs.modal', function (e) {
  if(autoj=="") {
	  $('#my-grid2').DataTable().ajax.reload( null, false );
  }else{
	i++;
	doLoop();
  }
})
</script>
