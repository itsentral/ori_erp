<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-danger','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 5px;','content'=>'Clear Propose Request','id'=>'autoDelete'));
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-primary','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Update Otomatis','id'=>'autoUpdate'));
		?>
		</div><br><br>
		<div class="box-tool pull-left">
			<select id='inventory' name='inventory' class='form-control input-sm chosen-select' style='min-width:150px; float:left; margin-bottom: 5px;'>
				<option value='0'>ALL INVENTORY TYPE</option>
				<?php
					foreach($inventory AS $val => $valx){
						echo "<option value='".$valx['id']."'>".strtoupper($valx['category'])."</option>";
					}
				?>
			</select>
			<select id='in_gudang' name='in_gudang' class='form-control input-sm chosen-select' style='width:200px; float:left; margin-bottom: 5px;'>
				<!-- <option value='0'>PILIH INCOMING GUDANG</option> -->
				<option value='indirect'>INDIRECT</option>
				<option value='project'>PROJECT</option>
			</select>
		</div>
		<div class="box-tool pull-right">
				<label for="tgl_butuh"><b>Tanggal Dibutuhkan</b></label>
				<?php
					$tgl_now = date('Y-m-d');
					$tgl_next_month = date('Y-m-'.'20', strtotime('+1 month', strtotime($tgl_now)));
					echo form_input(array('id'=>'tgl_butuh','name'=>'tgl_butuh','class'=>'form-control input-md text-center datepicker changeSaveDate','readonly'=>'readonly','placeholder'=>'Tanggal Dibutuhkan'),$tgl_next_month);
				?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-1'><b>Budget</b></label>
			<div class='col-sm-2'>             
					<?php
						echo form_input(array('id'=>'budget','name'=>'budget','class'=>'form-control input-md text-right','autocomplete'=>'off','readonly'=>'readonly'), number_format($TotalBudget));											
					?>		
			</div>
			<label class='label-control col-sm-1'><b>Total Pengajuan</b></label>
			<div class='col-sm-2'>             
					<?php
						echo form_input(array('id'=>'totalpr','name'=>'totalpr','class'=>'form-control input-md text-right','autocomplete'=>'off','readonly'=>'readonly'),number_format($TotalPR));											
					?>		
			</div>
		</div>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center">Nama Barang</th>
					<th class="text-center">Spesifikasi</th>
					<th class="text-center">Inventory Type</th> 
					<th class="text-center no-sort">Stock</th>
					<th class="text-center no-sort">Keb.1 Bln</th>
					<th class="text-center no-sort">Max Stock</th>
					<th class="text-center no-sort" width='7%'>Propose Purchase</th>
					<th class="text-center no-sort">Unit</th>
					<th class="text-center no-sort" width='8%'>Spec</th>
					<th class="text-center no-sort" width='8%'>Info</th>
					<th class="text-center no-sort" width='8%'>Price Ref.</th>
					<th class="text-center no-sort" width='8%'>Total Price</th>
					<!-- <th class="text-center no-sort" width='8%'>Tanggal Dibutuhkan</th> -->
					<!-- <th class="text-center no-sort" width='3%'>Option</th> -->
				</tr>
			</thead>
			<tbody></tbody>
		</table><br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'min-width:100px; float:right; margin: 5px 5px 5px 5px;','content'=>'Back','id'=>'back')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Purchase Request','id'=>'saveRequest')).' ';
		?>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<?php $this->load->view('include/footer'); ?>

<script>
	$(document).ready(function(){
		var inventory 		= $('#inventory').val();
		var in_gudang 		= $('#in_gudang').val();
		DataTables(inventory,in_gudang);
		
		$(document).on('change','#inventory,#in_gudang', function(e){
			e.preventDefault();
			var inventory 		= $('#inventory').val();
			var in_gudang 		= $('#in_gudang').val();

			$.ajax({
				url			: base_url + active_controller+'/changeBudget/'+inventory+'/'+in_gudang,
				type		: "POST",
				cache		: false,
				dataType	: 'json',
				success		: function(data){
					if(data.status == 1){
						$('#budget').val(data.totalbudget);
						$('#totalpr').val(data.totalpr);
						DataTables(data.inventory,data.in_gudang);
					}
					else{
						console.log('Update Failed!')
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
			
			DataTables(inventory,in_gudang);
		});
		$('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd',
			//minDate: 0
		});
	});
	
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller+'/pr_rutin';
	});
	
	$(document).on('click','.save_pr', function(){
		var nomor 		= $(this).data('no');
		var id_material = $(this).data('code_group');
		var purchase 	= $('#purchase_'+nomor).val().split(",").join("");
		var tanggal 	= $('#tanggal_'+nomor).val();
		var satuan 		= $('#satuan_'+nomor).val();
	
		if(purchase==''|| purchase=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Qty Purchase is empty, please input first ...',
			  type	: "warning"
			});
			return false;
		}
		
		if(satuan==''|| satuan=='0'){
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
					url			: base_url + active_controller+'/save_rutin',
					type		: "POST",
					data: {
						"id_material" 	: id_material,
						"purchase" 		: purchase,
						"tanggal" 		: tanggal,
						"satuan" 		: satuan
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
							window.location.href = base_url + active_controller+'/warehouse_rutin';
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

	$(document).on('change','.changeSave', function(){
		var nomor 		= $(this).data('no');
		var id_material = $(this).data('code_group');
		var purchase 	= getNum($('#purchase_'+nomor).val().split(",").join(""));
		// var tanggal 	= $('#tanggal_'+nomor).val();
		var tanggal 	= $('#tgl_butuh').val();
		var inventory 	= $('#inventory').val();
		var satuan 		= $('#satuan_'+nomor).val();
		var spec 		= $('#spec_'+nomor).val();
		var info 		= $('#info_'+nomor).val();
		var price 		= getNum($('#price_'+nomor).val().split(",").join(""));

		var stockNow 	= getNum($('#stockNow_'+nomor).text().split(",").join(""));
		var maxstockNow = getNum($('#maxstockNow_'+nomor).text().split(",").join(""));
		var maxpropose 	= maxstockNow - stockNow

		if(maxpropose > 0){
			if(purchase > maxpropose){
				$('#purchase_'+nomor).val(number_format(maxpropose))
				$('#noted_'+nomor).text('Max Propose: '+maxpropose)
			}
			else{
				$('#noted_'+nomor).text('')
			}
		}
		else{
			if(info == ''){
				$('#noted_'+nomor).text('Info diisi dulu!')
				$('#purchase_'+nomor).val(0)
			}
			else{
				$('#noted_'+nomor).text('')
			}
		}

		$('#tprice_'+nomor).val(number_format(purchase*price));
		
		$.ajax({
			url			: base_url + active_controller+'/save_rutin_change',
			type		: "POST",
			data: {
				"inventory" 	: inventory,
				"id_material" 	: id_material,
				"purchase" 		: purchase,
				"tanggal" 		: tanggal,
				"spec" 			: spec,
				"info" 			: info,
				"satuan" 		: satuan,
				"price" 		: price
			},
			cache		: false,
			dataType	: 'json',
			success		: function(data){
				console.log(data.pesan)
				$('#totalpr').val(data.totalpr);
			},
			error: function() {
				console.log('error connection serve !')
			}
		});
	});

	$(document).on('change','.changeSaveDate', function(){
		var tanggal 	= $('#tgl_butuh').val();
		
		$.ajax({
			url			: base_url + active_controller+'/save_rutin_change_date',
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

	$(document).on('click','#autoUpdate', function(){
		var inventory = $('#inventory').val();
		var in_gudang = $('#in_gudang').val()
	
		if(inventory=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Filter category terlebih dahulu ...',
			  type	: "warning"
			});
			return false;
		}
		swal({
			title: "Are you sure?",
			text: "Update otomatis kebutuhan !!!",
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
					url			: base_url + active_controller+'/auto_update_rutin/'+inventory+'/'+in_gudang,
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
							$('#totalpr').val(data.totalpr);
							// window.location.href = base_url + active_controller+'/warehouse_rutin';
							DataTables(data.inventory,data.in_gudang);
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

	$(document).on('click','#autoDelete', function(){
		var inventory 		= $('#inventory').val();
		var in_gudang = $('#in_gudang').val()
		// if(inventory=='0'){
		// 	swal({
		// 	  title	: "Error Message!",
		// 	  text	: 'Filter category terlebih dahulu ...',
		// 	  type	: "warning"
		// 	});
		// 	return false;
		// }
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
					url			: base_url + active_controller+'/clear_update_rutin/'+inventory+'/'+in_gudang,
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
								$('#totalpr').val(data.totalpr);
							// window.location.href = base_url + active_controller+'/warehouse_rutin';
							DataTables(data.inventory,data.in_gudang);
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
		var inventory 		= $('#inventory').val();
		var in_gudang 		= $('#in_gudang').val();
	
		if(inventory=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Filter category terlebih dahulu ...',
			  type	: "warning"
			});
			return false;
		}

		if(in_gudang=='0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Incoming Gudang Belum dilipih !',
			  type	: "warning"
			});
			return false;
		}

		swal({
			title: "Are you sure?",
			text: "Membuat semua PR Rutin !!!",
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
					url			: base_url + active_controller+'/save_pr_rutin_all/'+inventory,
					type		: "POST",
					data : {
						'in_gudang' : in_gudang
					},
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
							window.location.href = base_url + active_controller+'/warehouse_rutin';
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

	function DataTables(inventory = null, in_gudang=null){
		var dataTable = $('#my-grid').DataTable({
			"scrollCollapse" : true,
			"serverSide": true,
			"processing" : true,
			"stateSave" : true,
			"bAutoWidth": true,
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
				url : base_url + active_controller+'/data_side_warehouse_rutin',
				type: "post",
				data: function(d){
					d.inventory = inventory,
					d.in_gudang = in_gudang
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
