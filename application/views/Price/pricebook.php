<?php
$this->load->view('include/side_menu'); 
?>

<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<select name='gudang' id='gudang' class='form-control input-sm' style='width:150px; float:right;'>
				<option value='0'>Select Gudang</option>
				<?php
				foreach($list_gudang AS $val => $valx){
					echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
				}
				?>
			</select>

			
		</div>
		
	</div>
	
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th width='7%' class="text-center valign-middle">Id Material</th>
					<th width='14%' class="text-center valign-middle">Material Name</th>
					<th width='7%' class="text-center valign-middle">Pricebook</th>
					<th width='14%' class="text-center valign-middle">Update Date</th>
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
	.valign-middle{
		vertical-align: middle!important;
	}
</style>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		var status = 1;
		var gudang = $('#gudang').val();
		DataTables(status,gudang);
		
		$(document).on('change','#gudang', function(e){
			e.preventDefault();
			var status = 1;
			var gudang = $('#gudang').val();
			DataTables(status,gudang);
		});
	});
	
	
		
	function DataTables(status = null, gudang=null){
		var dataTable = $('#my-grid').DataTable({
			"scrollY": "1000",
			"scrollCollapse" : true,
			"serverSide": true,
			"processing": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"dom": 'Blfrtip',
			"buttons": [
				{
                "extend": 'excel',
            }],			
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 1000,
			"aLengthMenu": [[1000, 2000, 5000, 10000, 15000], [1000, 2000, 5000, 10000, 15000]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON/pricebook',
				type: "post",
				data: function(d){
					d.status = status,
					d.gudang = gudang
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
