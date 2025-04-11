<?php
$this->load->view('include/side_menu');
$active1 = '';
if($value1 == 'bolt nut'){
	$active1 = 'active';
}

$active2 = '';
if($value1 == 'plate'){
	$active2 = 'active';
}

$active3 = '';
if($value1 == 'gasket'){
	$active3 = 'active';
}

$active4 = '';
if($value1 == 'lainnya'){
	$active4 = 'active';
}

// print_r($data_session['JSON_Filter_LAINNYA']).'<br>';


//SESSION
$bold_nama = (!empty($data_session['JSON_Filter_BOLD']['nama']))?$data_session['JSON_Filter_BOLD']['nama']:'';
$bold_brand = (!empty($data_session['JSON_Filter_BOLD']['brand']))?$data_session['JSON_Filter_BOLD']['brand']:'';

$plate_nama = (!empty($data_session['JSON_Filter_PLATE']['nama']))?$data_session['JSON_Filter_PLATE']['nama']:'';
$plate_brand = (!empty($data_session['JSON_Filter_PLATE']['brand']))?$data_session['JSON_Filter_PLATE']['brand']:'';

$gasket_nama = (!empty($data_session['JSON_Filter_GASKET']['nama']))?$data_session['JSON_Filter_GASKET']['nama']:'';
$gasket_brand = (!empty($data_session['JSON_Filter_GASKET']['brand']))?$data_session['JSON_Filter_GASKET']['brand']:'';

$lainnya_nama = (!empty($data_session['JSON_Filter_LAINNYA']['nama']))?$data_session['JSON_Filter_LAINNYA']['nama']:'';
$lainnya_brand = (!empty($data_session['JSON_Filter_LAINNYA']['brand']))?$data_session['JSON_Filter_LAINNYA']['brand']:'';
// echo $lainnya_nama;
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="<?=$active1;?>"><a href="#boltnut" class='boltnut' aria-controls="boltnut" role="tab" data-toggle="tab">Bolt & Nut</a></li>
				<li role="presentation" class="<?=$active2;?>"><a href="#plate" class='plate' aria-controls="plate" role="tab" data-toggle="tab">Plate</a></li>
				<li role="presentation" class="<?=$active3;?>"><a href="#gasket" class='gasket' aria-controls="gasket" role="tab" data-toggle="tab">Gasket</a></li>
				<li role="presentation" class="<?=$active4;?>"><a href="#lainnya" class='lainnya' aria-controls="lainnya" role="tab" data-toggle="tab">Lainnya</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<!--bold and nut-->
				<div role="tabpanel" class="tab-pane <?=$active1;?>" id="boltnut">
					<div class="box-tool pull-right">
						<select name='barang1' id='barang1' class='form-control input-sm chosen-select'>
							<option value='0'>ALL NAME</option>
							<?php
							foreach($name_baut as $val => $valx)
							{
								$selx = ($valx['nama'] == $bold_nama)?'selected':'';
								echo "<option value='".$valx['nama']."' ".$selx.">".strtoupper($valx['nama'])."</option>";
							}
							?>
						</select>
						<select name='brand1' id='brand1' class='form-control input-sm chosen-select'>
							<option value='0'>ALL MATERIAL</option>
							<?php
							foreach($brand_baut as $val => $valx)
							{
								$sel = ($valx['material'] == $bold_brand)?'selected':'';
								echo "<option value='".$valx['material']."' ".$sel.">".strtoupper($valx['material'])."</option>";
							}
							?>
						</select>
						<a href="<?php echo site_url('accessories/add_bold_nut') ?>" class="btn btn-md btn-success" style='margin-top:5px; margin-bottom:10px;'>
							<i class="fa fa-plus"></i> Add
						</a>
					</div>
					<br><br><br>
					<table class="table table-bordered table-striped" id="my-grid_bold_nut" width='100%'>
						<thead width='100%'>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">ID</th>
								<th class="text-center">Name</th>
								<th class="text-center">Material</th> 
								<th class="text-center">Dim (mm)</th> 
								<th class="text-center">Length (mm)</th> 
								<th class="text-center">Std/Tipe</th> 
								<th class="text-center">Radius (mm)</th> 
								<th class="text-center">Unit</th> 
								<th class="text-center">Keterangan</th> 
								<!-- <th class="text-center">Harga</th> -->
								<!-- <th class="text-center no-sort">Last By</th> 
								<th class="text-center no-sort">Last Date</th>  -->
								<th class="text-center no-sort">Option</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<!--plate-->
				<div role="tabpanel" class="tab-pane <?=$active2;?>" id="plate">
					<div class="box-tool pull-right">
						<select name='barang2' id='barang2' class='form-control input-sm chosen-select'>
							<option value='0'>ALL NAME</option>
							<?php
							foreach($name_plate as $val => $valx)
							{
								$sel = ($valx['nama'] == $plate_nama)?'selected':'';
								echo "<option value='".$valx['nama']."' ".$sel.">".strtoupper($valx['nama'])."</option>";
							}
							?>
						</select>
						<select name='brand2' id='brand2' class='form-control input-sm chosen-select'>
							<option value='0'>ALL MATERIAL</option>
							<?php
							foreach($brand_plate as $val => $valx)
							{
								$sel = ($valx['material'] == $plate_brand)?'selected':'';
								echo "<option value='".$valx['material']."' ".$sel.">".strtoupper($valx['material'])."</option>";
							}
							?>
						</select>
						<a href="<?php echo site_url('accessories/add_plate') ?>" class="btn btn-md btn-success" style='margin-top:5px; margin-bottom:10px;'>
							<i class="fa fa-plus"></i> Add
						</a>
					</div>
					<br><br><br>
					<table class="table table-bordered table-striped" id="my-grid_plate" width='100%'>
						<thead width='100%'>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">ID</th>
								<th class="text-center">Name</th>
								<th class="text-center">Material</th> 
								<th class="text-center">Thickness (mm)</th> 
								<th class="text-center">Density (kg/cm3)</th> 
								<th class="text-center">Ukuran Standard</th> 
								<th class="text-center">Standart</th> 
								<th class="text-center">Unit</th> 
								<th class="text-center">Keterangan</th> 
								<!-- <th class="text-center">Harga</th> -->
								<!-- <th class="text-center no-sort">Last By</th> 
								<th class="text-center no-sort">Last Date</th>  -->
								<th class="text-center no-sort">Option</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<!--gasket-->
				<div role="tabpanel" class="tab-pane <?=$active3;?>" id="gasket">
					<div class="box-tool pull-right">
						<select name='barang3' id='barang3' class='form-control input-sm chosen-select'>
							<option value='0'>ALL NAME</option>
							<?php
							foreach($name_gasket as $val => $valx)
							{
								$sel = ($valx['nama'] == $gasket_nama)?'selected':'';
								echo "<option value='".$valx['nama']."' ".$sel.">".strtoupper($valx['nama'])."</option>";
							}
							?>
						</select>
						<select name='brand3' id='brand3' class='form-control input-sm chosen-select'>
							<option value='0'>ALL MATERIAL</option>
							<?php
							foreach($brand_gasket as $val => $valx)
							{
								$sel = ($valx['material'] == $gasket_brand)?'selected':'';
								echo "<option value='".$valx['material']."' ".$sel.">".strtoupper($valx['material'])."</option>";
							}
							?>
						</select>
						<a href="<?php echo site_url('accessories/add_gasket') ?>" class="btn btn-md btn-success" style='margin-top:5px; margin-bottom:10px;'>
							<i class="fa fa-plus"></i> Add
						</a>
					</div>
					<br><br><br>
					<table class="table table-bordered table-striped" id="my-grid_gasket" width='100%'>
						<thead width='100%'>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">ID</th>
								<th class="text-center">Name</th>
								<th class="text-center">Material</th> 
								<th class="text-center">Thickness (mm)</th> 
								<th class="text-center">Ukuran Standart</th> 
								<th class="text-center">Standard</th>
								<th class="text-center">Satuan</th> 
								<th class="text-center">Keterangan</th> 
								<!-- <th class="text-center">Harga</th> -->
								<!-- <th class="text-center no-sort">Last By</th> 
								<th class="text-center no-sort">Last Date</th>  -->
								<th class="text-center no-sort">Option</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<!--lainnya-->
				<div role="tabpanel" class="tab-pane <?=$active4;?>" id="lainnya">
					<div class="box-tool pull-right">
						<select name='barang4' id='barang4' class='form-control input-sm chosen-select'>
							<option value='0'>ALL NAME</option>
							<?php
							foreach($name_lainnya as $val => $valx)
							{
								$sel = ($valx['nama'] == $lainnya_nama)?'selected':'';
								echo "<option value='".$valx['nama']."' ".$sel.">".strtoupper($valx['nama'])."</option>";
							}
							?>
						</select>
						<select name='brand4' id='brand4' class='form-control input-sm chosen-select'>
							<option value='0'>ALL MATERIAL</option>
							<?php
							foreach($brand_lainnya as $val => $valx)
							{
								$sel = ($valx['material'] == $lainnya_brand)?'selected':'';
								echo "<option value='".$valx['material']."' ".$sel.">".strtoupper($valx['material'])."</option>";
							}
							?>
						</select>
						<a href="<?php echo site_url('accessories/add_lainnya') ?>" class="btn btn-md btn-success" style='margin-top:5px; margin-bottom:10px; float:right;'>
							<i class="fa fa-plus"></i> Add
						</a>
						
					</div>
					<br><br><br>
					<table class="table table-bordered table-striped" id="my-grid_lainnya" width='100%'>
						<thead width='100%'>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">ID</th>
								<th class="text-center">Name</th>
								<th class="text-center">Material/Brand</th> 
								<th class="text-center">Dimensi</th> 
								<th class="text-center">Spesifikasi</th> 
								<th class="text-center">Ukuran Standart</th> 
								<th class="text-center">Standart</th> 
								<th class="text-center">Satuan</th> 
								<th class="text-center">Keterangan</th> 
								<!-- <th class="text-center">Harga</th> -->
								<!-- <th class="text-center no-sort">Last By</th> 
								<th class="text-center no-sort">Last Date</th>  -->
								<th class="text-center no-sort">Option</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		
		
		
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<?php $this->load->view('include/footer'); ?>
<style>

#barang4_chosen, #brand4_chosen,#barang3_chosen, #brand3_chosen,#barang2_chosen, #brand2_chosen,#barang1_chosen, #brand1_chosen{
	margin-top: 5px;
	margin-right: 10px;
	width:300px !important;
}

</style>
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen();
		let baut_filter = {
			'barang1' : $('#barang1').val(),
			'brand1' : $('#brand1').val()
		};
		DataTables_bold_nut(baut_filter.barang1, baut_filter.brand1);
		
		let plate_filter = {
			'barang2' : $('#barang2').val(),
			'brand2' : $('#brand2').val()
		};
		DataTables_plate(plate_filter.barang2, plate_filter.brand2);
		
		let gasket_filter = {
			'barang3' : $('#barang3').val(),
			'brand3' : $('#brand3').val()
		};
		DataTables_gasket(gasket_filter.barang3, gasket_filter.brand3);
		
		let lainnya_filter = {
			'barang4' : $('#barang4').val(),
			'brand4' : $('#brand4').val()
		};
		DataTables_lainnya(lainnya_filter.barang4, lainnya_filter.brand4);
		
		$(document).on('change', '#barang1, #brand1', function(){
			let baut_filter = {
				'barang1' : $('#barang1').val(),
				'brand1' : $('#brand1').val()
			};
			DataTables_bold_nut(baut_filter.barang1, baut_filter.brand1);
		});
		
		$(document).on('change', '#barang2, #brand2', function(){
			let plate_filter = {
				'barang2' : $('#barang2').val(),
				'brand2' : $('#brand2').val()
			};
			DataTables_plate(plate_filter.barang2, plate_filter.brand2);
		});
		
		$(document).on('change', '#barang3, #brand3', function(){
			let gasket_filter = {
				'barang3' : $('#barang3').val(),
				'brand3' : $('#brand3').val()
			};
			DataTables_gasket(gasket_filter.barang3, gasket_filter.brand3);
		});
		
		$(document).on('change', '#barang4, #brand4', function(){
			let lainnya_filter = {
				'barang4' : $('#barang4').val(),
				'brand4' : $('#brand4').val()
			};
			DataTables_lainnya(lainnya_filter.barang4, lainnya_filter.brand4);
		});
		
		$(document).on('click', '.deleted', function(){
			var bF	= $(this).data('id');
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
						url			: base_url + active_controller+'/hapus/'+bF,
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
							else{
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
	
		$(document).on('click','.boltnut', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'bolt nut'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
		
		$(document).on('click','.plate', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'plate'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
		
		$(document).on('click','.gasket', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'gasket'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
		
		$(document).on('click','.lainnya', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'lainnya'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
	});

	function DataTables_bold_nut(nama=null,brand=null){
		var dataTable = $('#my-grid_bold_nut').DataTable({
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
				url : base_url + active_controller+'/data_side_bold_nut',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
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
	
	function DataTables_plate(nama=null,brand=null){
		var dataTable = $('#my-grid_plate').DataTable({
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
				url : base_url + active_controller+'/data_side_plate',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
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
	
	function DataTables_gasket(nama=null,brand=null){
		var dataTable = $('#my-grid_gasket').DataTable({
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
				url : base_url + active_controller+'/data_side_gasket',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
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
	
	function DataTables_lainnya(nama=null,brand=null){
		var dataTable = $('#my-grid_lainnya').DataTable({
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
				url : base_url + active_controller+'/data_side_lainnya',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
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
