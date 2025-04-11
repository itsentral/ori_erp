<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class="box-tool pull-right">
			<select id='cust' name='cust' class='form-control input-sm' style='max-width:400px;'>
				<option value=''>All Material</option>
				<?php
					foreach($cust AS $val => $valx){
						echo "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
					}
				?>
			</select>

            &nbsp; <button type='button' class='btn btn-md btn-primary' id='download_excel'><i class='fa fa-file-excel-o'></i>&nbsp;&nbsp;Download</button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class="table-responsive"> -->
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Nama material</th>
					<th class="text-center" width='10%'>Price Ref. Mat.</th>
                    <th class="text-center" width='10%'>Expired</th>
                    <th class="text-center" width='10%'>Price From Sup.</th>
                    <th class="text-center" width='10%'>Expired</th>
                    <th class="text-center" width='10%'>Updated By</th>
					<th class="text-center" width='10%'>Updated Date</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<!-- </div> -->
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var cust = $('#cust').val();
		DataTables(cust);
	});
	
	$(document).on('change','#cust', function(){
		var cust = $('#cust').val();
		DataTables(cust);
	});

	$(document).on('click','#download_excel', function(){
		var id_material = $('#cust').val();
		var Link	= base_url + active_controller +'/excel_hist_material/'+id_material;
		window.open(Link);

	});
		
	function DataTables(cust=null){
		var dataTable = $('#my-grid').DataTable({
            // dom: 'Bfrtip',
            // buttons: [
            //     'excel'
            // ],
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"processing": true,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 1, "desc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_hist_material',
				type: "post",
				data: function(d){
					d.cust = cust
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
