<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<div class="box-tool pull-right">
				<?php
				// echo site_url('penagihan/add');
					?>
					<a href="<?php
					if($delivery=='instalasi'){
						echo site_url('penagihan/add_new_instalasi');
					}
					if($delivery=='delivery'){
						echo site_url('penagihan/add_new_progress');
					}
					if($delivery==''){
						echo site_url('penagihan/add_new');
					}
					?>" class="btn btn-sm btn-success" style='float:right;' id='btn-add'> <i class="fa fa-plus"></i> &nbsp;&nbsp;Add Plan Tagih</a>
					<?php	
				?><br><br>
				<select name='no_so' id='no_so' class='form-control input-sm' style='width:150px; float:right;'>
					<option value='0'>ALL NUMBER SO</option>
					<?php
					foreach($list_so AS $val => $valx){
						echo "<option value='".$valx['id_penagihan']."'>".strtoupper($valx['so_number'])."</option>";
					}
					?>
				</select>
					
				<select name='customer' id='customer' class='form-control input-sm' style='width:300px; float:right;'>
					<option value='0'>ALL CUSTOMER</option>
					<?php
					foreach($list_cust AS $val => $valx){
						echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
					}
					?>
				</select>
			</div>
		</div>
		
		<div class="box-body">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">#</th>
						<th class="text-center">ID Tagih</th> 
						<th class="text-center">No SO</th> 
						<th class="text-center">No PO</th>
						<th class="text-center">Customer</th>
						<th class="text-center">Plan Tagih</th>
						<th class="text-center">Value USD</th>
						<th class="text-center">Value IDR</th>
						<th class="text-center">Type</th>
						<th class="text-center">Status</th>
						<th class="text-center no_sort">Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
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
		let filter = {
			'customer' : $('#customer').val(),
			'no_so' : $('#no_so').val()
		};
		
		DataTables(filter.customer, filter.no_so);
		
		$(document).on('change','#customer, #no_so', function(){
			let filter = {
				'customer' : $('#customer').val(),
				'no_so' : $('#no_so').val()
			};
			DataTables(filter.customer, filter.no_so);
		});
	});
	
	function DataTables(customer=null, no_so=null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"oLanguage": {
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
//				url : base_url + active_controller+'/server_side_penagihan',
				url : base_url + active_controller+'/server_side_penagihan_new',
				type: "post",
				data: function(d){
					d.customer = customer,
					d.delivery = '<?=$delivery?>',
					d.no_so = no_so
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
</script>
