<?php
$this->load->view('include/side_menu'); 
?>   
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
	
		<?php
			if($akses_menu['create']=='1'){		?>
			  <a href="<?php echo site_url('cost_packing/add_trucking') ?>" class="btn btn-sm btn-success" id='btn-add'>
				<i class="fa fa-plus"></i> Add Cost Trucking
			  </a>
		  <?php
			}
		  ?>
		</div>
		<br><br><br>
		<div class='form-group row'>
			<div class='col-sm-2'>
				<select id='category' name='category' class='form-control input-sm chosen-select'>
					<option value='0'>All Category</option>
					<?php
						foreach($category AS $val => $valx){
							echo "<option value='".$valx['category']."'>".strtoupper($valx['category'])."</option>"; 
						}
					?>
				</select>
			</div>
			<div class='col-sm-3'>
				<select id='area' name='area' class='form-control input-sm chosen-select'>
					<option value='0'>All Area</option>
					<?php
						foreach($area AS $val => $valx){
							echo "<option value='".$valx['area']."'>".strtoupper($valx['area'])."</option>"; 
						}
					?>
				</select>
			</div>
			<div class='col-sm-3'>
				<select id='dest' name='dest' class='form-control input-sm chosen-select'>
					<option value='0'>All Destination</option>
					<?php
						foreach($dest AS $val => $valx){
							echo "<option value='".$valx['tujuan']."'>".strtoupper($valx['tujuan'])."</option>"; 
						}
					?>
				</select> 
			</div>
			<div class='col-sm-4'>
				<select id='truck' name='truck' class='form-control input-sm chosen-select'>
					<option value='0'>All Truck</option>
					<?php
						foreach($truck AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper($valx['nama_truck'])."</option>"; 
						}
					?>
				</select> 
			</div>
		</div>
		<div class='form-group row'>
			<div class='col-sm-2'>
				<select id='prov' name='prov' class='form-control input-sm chosen-select'>
					<option value='0'>All Provinsi</option>
					<?php
						foreach($provinsi AS $val => $valx){
							echo "<option value='".$valx['id_prov']."'>".strtoupper($valx['nama'])."</option>"; 
						}
					?>
				</select>
			</div>
			<div class='col-sm-3'>
				<select id='kota' name='kota' class='form-control input-sm chosen-select'>
					<option value='0'>All Kab/Kota</option>
					<?php
						foreach($kabupaten AS $val => $valx){
							echo "<option value='".$valx['id_kab']."'>".strtoupper($valx['nama'])."</option>"; 
						}
					?>
				</select>
			</div>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-left">Category</th> 
					<th class="text-left">Area</th>
					<th class="text-left">Destination</th>
					<th class="text-left">Provinsi</th>
					<th class="text-left">Kab/Kota</th>
                    <th class="text-left">Truck</th>
                    <th class="text-center">Price</th>
					<!-- <th class="text-center" width='8%'>Updated</th>
					<th class="text-center" width='10%'>Update Time</th> -->
					<th class="text-center">Option</th>
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
        $('.chosen-select').chosen();

		$(document).ready(function(){
			var category = $('#category').val();
            var area = $('#area').val();
            var dest = $('#dest').val();
            var truck = $('#truck').val();
			var prov = $('#prov').val();
            var kota = $('#kota').val();
			DataTables(category,area,dest,truck,prov,kota);
		});
		
		$(document).on('change','#category, #area, #dest, #truck, #prov, #kota', function(){
			var category = $('#category').val();
            var area = $('#area').val();
            var dest = $('#dest').val();
            var truck = $('#truck').val();
            var prov = $('#prov').val();
            var kota = $('#kota').val();
			DataTables(category,area,dest,truck,prov,kota);
		});
		
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#deleteID', function(){
			var bF	= $(this).data('id');
			// alert(bF);
			// return false;
			swal({
			  title: "Are you sure?",
			  text: "Data akan terhapus secara permanen ?",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					$.ajax({
						url			: base_url + active_controller+'/hapus_export/'+bF,
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
								window.location.href = base_url + active_controller + '/export';
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
	
	function DataTables(category = null, area = null, dest = null, truck = null, prov = null, kota = null){

		var dataTable = $('#example1').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"processing": true,
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
				url : base_url + active_controller+'/getDataJSONTrucking',
				type: "post",
				data: function(d){
					d.category 	= category,
                    d.area 	= area,
                    d.dest 	= dest,
                    d.truck = truck,
                    d.prov = prov,
                    d.kota = kota
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
