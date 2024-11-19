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
			  <a href="<?php echo site_url('cost_packing/add_profit') ?>" class="btn btn-sm btn-success" id='btn-add'>
				<i class="fa fa-plus"></i> Add Cost Packing 
			  </a>
		  <?php
			}
		  ?>
		</div>
		<br><br>
		<div class="box-tool pull-left">
		<select id='komponen' name='komponen' class='form-control input-sm' style='min-width:200px;'>
			<option value='0'>All Component</option>
			<?php
				foreach($listkomponen AS $val => $valx){
					echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>"; 
				}
			?>
		</select> 
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width ='8%'>No.</th>
					<th class="text-center">Product</th>
					<th class="text-center">Diameter 1</th>
					<th class="text-center">Diameter 1</th>
					<th class="text-center">Price</th>
					<th class="text-center">Update By</th>
					<th class="text-center">Update Time</th>
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
<style>
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
</style>
<script>
	$(document).ready(function(){
		$(document).ready(function(){
			var komponen = $('#komponen').val();
			DataTables(komponen);
		});
		
		$(document).on('change','#komponen', function(){
			var komponen = $(this).val();
			DataTables(komponen);
		});
		
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#deleteID', function(){
			var bF	= $(this).data('id');
			var parent	= $(this).data('product');
			var d1	= $(this).data('d1');
			var d2	= $(this).data('d2');
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
						url			: base_url+'index.php/'+active_controller+'/hapus_profit/'+bF+'/'+parent+'/'+d1+'/'+d2,
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
								window.location.href = base_url + active_controller + '/index';
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
	
	function DataTables(komponen = null){

		var dataTable = $('#example1').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
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
				url : base_url +'index.php/'+active_controller+'/getDataJSON',
				type: "post",
				data: function(d){
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
	
</script>
