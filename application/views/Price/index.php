<?php
$this->load->view('include/side_menu');
?>   
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<!-- Navigasi bar -->
			<div class="box-tool pull-right">
				<!-- <button type='button' id='update_data' style='min-width:100px; float:right;' class="btn btn-sm btn-primary">
					Update
				</button>
				<br><br> -->
				<?php 
				if(!empty($last_by)){
				?>
					<div style='color:red;'><b>Last Update By <span style='color:green;'><?= strtoupper(strtolower($last_by))."</span> On <u>".date('d-m-Y H:i:s', strtotime($last_date));?></u></b></div>
				<?php 
				}
				?>
			</div>
		</div>
		<!-- /.box-header --> 
		<div class="box-body">
			
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#standart" class='standart' aria-controls="standart" role="tab" data-toggle="tab">Standart</a></li>
					<li role="presentation" class=""><a href="#custom" class='custom' aria-controls="custom" role="tab" data-toggle="tab">Non Standart</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="standart"">
						<div class="box-tool pull-right">
							<br>
							<select id='series' name='series'  class='form-control input-sm chosen-select'>
								<option value='0'>All Series</option>
								<?php
									foreach($listseries AS $val => $valx){
										echo "<option value='".$valx['kode_group']."'>".strtoupper($valx['kode_group'])."</option>";
									}
								?>
							</select>
							
							<select id='komponen' name='komponen'  class='form-control input-sm chosen-select'>
								<option value='0'>All Component</option>
								<?php
									foreach($listkomponen AS $val => $valx){
										echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>"; 
									}
								?>
							</select>
						</div>
						<br><br><br>
						<table id="my-grid" class="table table-bordered table-striped" width='100%'>
							<thead>
								<tr class='bg-blue'>
									<th class="text-center">#</th>
									<th class="text-center">Code</th>
									<th class="text-center">Product</th>
									<th class="text-center">Stifness</th>
									<th class="text-center no-sort">Spec</th>
									<!-- <th class="text-center">By</th>
									<th class="text-center">Updated</th> -->
									<!-- <th class="text-center no-sort">Ref</th> -->
									<th class="text-center no-sort">Weight</th>
									<th class="text-center no-sort">Price</th>
									<th class="text-center no-sort">Process</th>
									<th class="text-center no-sort">FOH</th>
									<th class="text-center no-sort">Profit</th>
									<th class="text-center no-sort">#</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div role="tabpanel" class="tab-pane" id="custom"><br>
						<div class="box-tool pull-right">
							<select id='series2' name='series2'  class='form-control input-sm chosen-select'>
								<option value='0'>All Series</option>
								<?php
									foreach($listseries AS $val => $valx){
										echo "<option value='".$valx['kode_group']."'>".strtoupper($valx['kode_group'])."</option>";
									}
								?>
							</select>
							
							<select id='komponen2' name='komponen2'  class='form-control input-sm chosen-select'>
								<option value='0'>All Component</option>
								<?php
									foreach($listkomponen AS $val => $valx){
										echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>"; 
									}
								?>
							</select> 
							
							<select id='cust2' name='cust2'  class='form-control input-sm chosen-select'>
								<option value='0'>All Customer</option>
								<?php
									foreach($cust AS $val => $valx){
										echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
									}
								?>
							</select>
						</div>
						<br><br>
						<table id="my-grid2" class="table table-bordered table-striped" width='100%'>
							<thead>
								<tr class='bg-blue'>
									<th class="text-center">#</th>
									<th class="text-center">Code</th>
									<th class="text-center">Customer</th>
									<th class="text-center">Product</th>
									<th class="text-center">Stifness</th>
									<th class="text-center no-sort">Spec</th>
									<!-- <th class="text-center">By</th>
									<th class="text-center">Updated</th> -->
									<!-- <th class="text-center no-sort">Ref</th> -->
									<th class="text-center no-sort">Weight</th>
									<th class="text-center no-sort">Price</th>
									<th class="text-center no-sort">Process</th>
									<th class="text-center no-sort">FOH</th>
									<th class="text-center no-sort">Profit</th>
									<th class="text-center no-sort">#</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style>

#series2_chosen, #komponen2_chosen{
	width:200px !important;
}
#cust2_chosen{
	width:300px !important;
}

</style>
<script>
	$(document).ready(function(){
		//STANDART
		var series = $('#series').val();
		var komponen = $('#komponen').val();
		DataTables(series, komponen); 

		
		$(document).on('change','#series, #komponen', function(e){
			e.preventDefault();
			var series = $('#series').val();
			var komponen = $('#komponen').val();
			DataTables(series, komponen); 
		});
		
		//CUSTOM
		var series2 = $('#series2').val();
		var komponen2= $('#komponen2').val();
		var cust2 = $('#cust2').val();
		DataTables2(series2, komponen2, cust2); 
		
		$(document).on('change','#series2, #komponen2, #cust2', function(e){
			e.preventDefault();
			var series = $('#series2').val();
			var komponen = $('#komponen2').val();
			var cust = $('#cust2').val();
			DataTables2(series, komponen, cust); 
		});
		

		
		$(document).on('click', '#update_data', function(){
			
			swal({
			  title: "Update Product List ?",
			  text: "Tunggu sampai 'Last Update by ' menunjukan nama user dan update jam sekarang. ",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Update!",
			  cancelButtonText: "Tidak, Batalkan!",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					// loading_spinner();
					$('#spinnerx').show();
					$.ajax({
						url			: base_url + active_controller+'/insert_select_product_list',
						type		: "POST",
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
								$('#spinnerx').hide();
								window.location.href = base_url + active_controller ;
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
								$('#spinnerx').hide();
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
							$('#spinnerx').hide();
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
		$(document).on('click', '.updateData', function(){
			var id_product = $(this).data('id_product');
			// alert(id_product);
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
						url			: base_url+active_controller+'/update_product_list',
						type		: "post",
						data: {
							'id_product' : id_product
						},
						cache		: false,
						dataType	: 'json',				
						success		: function(data){								
							if(data.status == 1){											
								swal({
								  title	: "Save Success!",
								  text	: data.pesan,
								  type	: "success",
								  timer	: 2000
								});
								DataTables(series, komponen);
								DataTables2(series2, komponen2, cust2);
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

	});
	
	function DataTables(series = null, komponen = null){
		var dataTable = $('#my-grid').DataTable({
			// "scrollX": true,
			// "scrollY": "500",
			// "scrollCollapse" : true,
			"serverSide": true,
			"stateSave" : true,
			"processing" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 0, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSONComp2',
				type: "post",
				data: function(d){
					d.series 	= series,
					d.komponen 	= komponen
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

	function DataTables2(series = null, komponen = null, cust = null){
		var dataTable = $('#my-grid2').DataTable({
			// "scrollX": true,
			// "scrollY": "500",
			// "scrollCollapse" : true,
			"serverSide": true,
			"stateSave" : true,
			"processing" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 0, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSONComp2Cust',
				type: "post",
				data: function(d){
					d.series 	= series,
					d.komponen 	= komponen,
					d.cust 		= cust
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
