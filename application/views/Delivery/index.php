<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
		if($akses_menu['create']=='1'){
		?>
			<a href="<?php echo site_url('delivery/add_deadstok') ?>" class="btn btn-sm btn-danger" style='float:right;'>
				<i class="fa fa-plus"></i> &nbsp;&nbsp;Delivery Deadstok
		  	</a>

			  <a href="<?php echo site_url('delivery/add_field_joint') ?>" class="btn btn-sm btn-success" style='float:right; margin-right:5px;'>
				<i class="fa fa-plus"></i> &nbsp;&nbsp;Delivery Field Joint
		  	</a>

			  <a href="<?php echo site_url('delivery/add_material') ?>" class="btn btn-sm btn-warning" style='float:right; margin-right:5px;'>
				<i class="fa fa-plus"></i> &nbsp;&nbsp;Delivery SO Material
		  	</a>

			<!-- <a href="<?php echo site_url('delivery/add_cutting') ?>" class="btn btn-sm btn-info" style='float:right; margin-right:10px;'>
						<i class="fa fa-plus"></i> &nbsp;&nbsp;Delivery Pipe Cutting
					</a>

					<a href="<?php echo site_url('delivery/add_spool') ?>" class="btn btn-sm btn-primary" style='float:right; margin-right:10px;'>
						<i class="fa fa-plus"></i> &nbsp;&nbsp;Delivery Spool
					</a>
 -->
					<a href="<?php echo site_url('delivery/add_aksesoris') ?>" class="btn btn-sm btn-success" style='float:right; margin-right:5px;'>
						<i class="fa fa-plus"></i> &nbsp;&nbsp;Delivery Accessories
					</a>

					<a href="<?php echo site_url('delivery/create') ?>" class="btn btn-sm btn-primary" style='float:right; margin-right:5px;'>
						<i class="fa fa-plus"></i> &nbsp;&nbsp;Buat Delivery
					</a>		<?php
		}
		?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Nomor Surat Jalan</th>
                    <!-- <th class="text-center no-sort">NO SO</th> -->
                    <th class="text-center no-sort">No Drawing</th>
                    <th class="text-center no-sort">Kode Product</th>
                    <!-- <th class="text-center no-sort">Spool/Loose</th> -->
                    <th class="text-center no-sort">By</th>
                    <th class="text-center no-sort">Date</th>
                    <th class="text-center no-sort">Option</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
 <!-- modal -->
 <div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
	<div class="modal-dialog"  style='width:70%; '>
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
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})
		DataTables2();
		
		$(document).on('click', '.lock_spool', function(e){
			e.preventDefault();
			let spool = $(this).data('spool');
			
			swal({
					title: "Are you sure?",
					text: "Lock Delivery !!!",
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
						var baseurl=base_url + active_controller +'/release_delivery';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: {
								'spool' : spool
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
									window.location.href = base_url + active_controller;
								}
								else{
									swal({
										title	: "Save Failed!",
										text	: data.pesan,
										type	: "warning",
										timer	: 3000
									});
								}
							},
							error: function() {

								swal({
									title				: "Error Message !",
									text				: 'An Error Occured During Process. Please try again..',
									type				: "warning",
									timer				: 3000
								});
							}
						});
					} else {
					swal("Cancelled", "Data can be process again :)", "error");
					return false;
					}
			});
		});

		$(document).on('click', '.edit_print', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>EDIT SURAT JALAN ["+$(this).data('kode_delivery')+"]</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/modal_edit_surat_jalan/'+$(this).data('kode_delivery'),
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
					});
				}
			});
		});
	});

	function DataTables2(status=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
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
				url : base_url + active_controller+'/server_side_delivery',
				type: "post",
				data: function(d){
					d.status = status
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}
</script>
