<?php
$this->load->view('include/side_menu');
// print_r(listSO_ByDeliveryMaterial_Add());
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class="box-tool pull-left">
           
		</div>
		<div class="box-tool pull-right">
			<select name='kode_delivery' id='kode_delivery' class='form-control input-sm chosen-select' style='width:250px; float:right; margin-top:10px;'>
                <option value='0'>Buat Baru Delivery</option>
				<?php
				foreach ($data_spool as $key => $value) {
					echo "<option value='".$value['kode_delivery']."'>Tambahkan ke Delivery : ".$value['kode_delivery']."</option>";
				}
				?>
            </select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <a href="<?php echo site_url('delivery') ?>" class="btn btn-sm btn-danger" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        <button type='button' class='btn btn-sm btn-success' style='float:right; margin-bottom:10px;' id='make_spool'><i class='fa fa-truck'></i>&nbsp;Buat Delivery</button>
        <br>
		<h3>Material SO</h3>
		<div class='row'>
			<div class="col-sm-3">
				<select name='no_so' id='no_so' class='form-control input-sm'>
					<option value='0'>ALL SALES ORDER</option>
					<?php
					foreach(listSO_ByDeliveryMaterial_Add() as $val => $valx)
					{
						if($valx['no_so'] != '0'){
							echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_so'])."</option>";
						}
					}
					?>
				</select>
			</div>
		</div>
		<br>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">No Trans</th>
                    <th class="text-center">No SO</th>
                    <th class="text-center">Material Name</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Tujuan Outgoing</th>
                    <th class="text-center">Tgl Relese SO</th>
                    <th class="text-center">Berat (kg)</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
		<h3>Material Field & Branch Joint</h3>
		<div class='row'>
			<div class="col-sm-3">
				<select name='no_ipp' id='no_ipp' class='form-control input-sm'>
					<option value='0'>ALL SALES ORDER</option>
					<?php
					foreach($no_sales_order as $val => $valx)
					{
						echo "<option value='".$valx['id_bq']."'>".strtoupper($valx['so_number'])."</option>";
					}
					?>
				</select>
			</div>
			<div class="col-sm-3">
				<select name='id_milik' id='id_milik' class='form-control input-sm'>
					<option value='0'>ALL NO SPK</option>
					<?php
					foreach($no_spk_list as $val => $valx)
					{
						echo "<option value='".$valx['id_milik']."'>".strtoupper($valx['no_spk'])."</option>";
					}
					?>
				</select>
			</div>
			<div class="col-sm-6"></div>
		</div>
		<br>
		<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">No Trans</th>
                    <th class="text-center">No SO</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">Material Name</th>
                    <!-- <th class="text-center">Category</th> -->
                    <th class="text-center">Tujuan Outgoing</th>
                    <th class="text-center">Tgl Outgoing</th>
                    <th class="text-center">Berat (kg)</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>	
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})
		var no_so = $('#no_so').val()
		DataTables(no_so);

		$(document).on('change','#no_so', function(){
			var no_so = $('#no_so').val()
			DataTables(no_so);
		});

		var no_ipp = $('#no_ipp').val()
		var id_milik = $('#id_milik').val()
		DataTables2(no_ipp,id_milik);

		$(document).on('change','#no_ipp, #id_milik', function(){
			var no_ipp = $('#no_ipp').val()
			var id_milik = $('#id_milik').val()
			DataTables2(no_ipp,id_milik);
		});

		$(document).on('change','.chk_material', function(){
			let id_milik 	= $(this).val()
			let no_ipp 		= ''
			let qty 		= $(this).data('qty')
			let check 		= $(this).is(":checked")
			
			// console.log(check);
			// return false;
			if(check === true || check === false){
				$.ajax({
					url: base_url + active_controller+'/changeDeliveryTemp',
					cache: false,
					type: "POST",
					data: {
						'id_milik'	:id_milik,
						'no_ipp'	:no_ipp,
						'qty'		:qty,
						'check'		:check,
						'category'	:'material',
					},
					dataType: "json",
					success: function(data){
						console.log(data.pesan)
					}
				});
			}
		});

		$(document).on('change','.chk_joint', function(){
			let id_milik 	= $(this).val()
			let no_ipp 		= $(this).data('no_ipp')
			let qty 		= $(this).data('qty')
			let check 		= $(this).is(":checked")
			
			// console.log(check);
			// return false;
			if(check === true || check === false){
				$.ajax({
					url: base_url + active_controller+'/changeDeliveryTemp',
					cache: false,
					type: "POST",
					data: {
						'id_milik'	:id_milik,
						'no_ipp'	:no_ipp,
						'qty'		:qty,
						'check'		:check,
						'category'	:'field',
					},
					dataType: "json",
					success: function(data){
						console.log(data.pesan)
					}
				});
			}
		});

		$(document).on('click', '#make_spool', function(){
			
			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist product minimal 1',
					type	: "warning"
				});
				return false;
			}
			// return false;
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
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/create_material',  
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){
								swal({
									title	: "Success!",
									text	: 'Succcess Process!',
									type	: "success",
									timer	: 3000
								});
								window.location.href = base_url + active_controller;
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
									type	: "warning",
									timer	: 3000
								});
							}
						},
						error: function() {
							swal({
							title		: "Error Message !",
							text		: 'An Error Occured During Process. Please try again..',						
							type		: "warning",								  
							timer		: 3000
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

	function DataTables(no_so=null){
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
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_so_material',
				type: "post",
				data: function(d){
					d.no_so = no_so
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

	function DataTables2(no_ipp=null,id_milik=null){
		var dataTable = $('#my-grid2').DataTable({
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
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_so_material_joint',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp,
					d.id_milik = id_milik
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
