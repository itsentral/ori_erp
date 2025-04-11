<?php
$this->load->view('include/side_menu'); 
?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<select name='product_parent' id='product_parent' class='form-control input-sm' style='width:300px;'>
				<option value='0'>All Product</option>
				<?php
				foreach($product_parent AS $val => $valx){
					echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
				}
				?>
			</select>
			<select name='pn' id='pn' class='form-control input-sm' style='width:150px;'>
				<option value='0'>All Pressure</option>
				<?php
				foreach($pressure AS $val => $valx){
					$KdPressure		= sprintf('%02s',$valx['name']);
					echo "<option value='".$valx['name']."'>PN ".ucfirst(strtolower($KdPressure))."</option>";
				}
				?>
			</select>
			<select name='liner' id='liner' class='form-control input-sm' style='width:150px;'>
				<option value='0'>All Liner</option>
				<?php
				foreach($liner AS $val => $valx){
					echo "<option value='".$valx['name']."'>".ucfirst(strtolower($valx['name']))." mm</option>";
				}
				?>
			</select><br><br>		
		<?php
			if($akses_menu['create']=='1'){		?>
			  <a href="<?php echo site_url('component_group/add_cycletime') ?>" class="btn btn-sm btn-success" style='float:right;' id='btn-add'>
				<i class="fa fa-plus"></i> &nbsp;Add Cycletime
			  </a>
		  <?php
			}
		  ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="my-grid" class="table table-bordered table-striped table-responsive" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No.</th>
					<th class="text-center">Code</th>
					<th class="text-center">Product</th>
					<th class="text-center">Diameter</th>
					<th class="text-center">Diameter 2</th>
					<th class="text-center">Pressure</th>
					<th class="text-center">Liner</th>
					<th class="text-center">MP</th>
					<th class="text-center">Time</th>
					<th class="text-center">Man Hours</th>
					<th class="text-center">Mesin</th>
					<th class="text-center">Last By</th>
					<th class="text-center">Last Date</th>
					<th class="text-center">#</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var product_parent = $('#product_parent').val();
		var pn = $('#pn').val();
		var liner = $('#liner').val();
		DataTables(product_parent, pn, liner);
		
		$(document).on('change','#product_parent, #pn, #liner', function(e){
			e.preventDefault();
			var product_parent = $('#product_parent').val();
			var pn = $('#pn').val();
			var liner = $('#liner').val();
			DataTables(product_parent, pn, liner);
		});
		
		
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#del_satuan', function(){
			var bF	= $(this).data('id');
			// alert(bF);
			// return false;
			swal({
			  title: "Are you sure?",
			  text: "Delete this data ?",
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
						url			: base_url+'index.php/'+active_controller+'/hapus/'+bF,
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
	
	
	function DataTables(product_parent = null, pn = null, liner = null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"processing": true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
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
				url : base_url + active_controller+'/server_side_cycletime', 
				type: "post",
				data: function(d){
					d.product_parent= product_parent,
					d.pn 			= pn,
					d.liner 		= liner
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
