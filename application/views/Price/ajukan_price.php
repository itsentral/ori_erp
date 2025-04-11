<?php
$this->load->view('include/side_menu');
?>   
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br>
		<div class="box-tool pull-right">
			<label>Component : </label>
			<select id='product' name='product' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Component</option>
				<?php
					foreach($listparent AS $val => $valx){
						echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>";
					}
				?>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="my-grid" width="100%" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No.</th>
					<th class="text-center">Product ID</th>
					<th class="text-center">Product Name</th>
					<th class="text-center">Standard By</th>
					<th class="text-center">Application</th>
					<th class="text-center">By</th>
					<th class="text-center">Rev</th>
					<th class="text-center">Status Price</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  // if($row){
					// $int	=0;
					// foreach($row as $datas){
						// $int++;
						// $detail = "";
						// if(strtolower($datas->parent_product) == 'pipe'){
							// $detail = "(".$datas->diameter." x ".$datas->panjang." x ".$datas->design.")";
						// }
						// echo"<tr>";							
							// echo"<td align='center'>$int</td>";
							// echo"<td align='left'>".$datas->id_product."</td>";
							// echo"<td align='left'>".$datas->nm_product." ".$detail."</td>";
							
							// echo"<td align='left'>".strtoupper($datas->standart_toleransi)."</td>";
							// echo"<td align='left'>".$datas->aplikasi_product."</td>";
							// echo"<td align='left'>".ucfirst(strtolower($datas->created_by))."</td>";
							// echo"<td align='center'><span class='badge bg-blue'>".$datas->rev."</span></td>";
							// if($datas->sts_price == 'REGISTERED'){
								// $warna = 'bg-green';
							// }
							// elseif($datas->sts_price == 'UNREGISTERED'){
								// $warna = 'bg-red';
							// }
							// else{
								// $warna = 'bg-blue';
							// }
							// echo"<td align='center'><span class='badge ".$warna."'>".$datas->sts_price."</span></td>";
							// echo"<td align='center'>";
								// echo"<button type='button' id='MatDetail' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";									
								// if($datas->sts_price == 'UNREGISTERED'){
									// if($Arr_Akses['update']=='1'){
										// echo "&nbsp;<button type='button' id='MatPrice' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-success' title='Registered Now' data-role='qtip'><i class='fa fa-edit'></i></button>";										
									// }
								// }
							// echo"</td>";
						// echo"</tr>";
					// }
			  // } 
			  ?> 
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
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
			DataTables();
		});
		
		$(document).on('change','#product', function(e){
			e.preventDefault();
			var product = $(this).val();
			DataTables(product);
		});
	
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$('#printSPK').click(function(e){
			e.preventDefault();
			var id_product	= $(this).data('id_product');
			
			var Links		= base_url +'index.php/'+ active_controller+'/printSPK/'+id_product;
			window.open(Links,'_blank');
		});
		
		$(document).on('click', '#MatDetail', function(e){
			e.preventDefault();
			$("#head_title").html("<b>DETAIL ESTIMATION</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#MatPrice', function(e){
			e.preventDefault();
			$("#head_title").html("<b>SUBMIT ESTIMATION</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalPrice/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
	});
	
	function DataTables(product = null){
		var dataTable = $('#my-grid').DataTable({
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
				url : base_url +'index.php/'+active_controller+'/getDataJSONKomp',
				type: "post",
				data: function(d){
					d.product = $('#product').val()
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
