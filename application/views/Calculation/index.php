<?php
$this->load->view('include/side_menu'); 
?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No.</th>
					<th class="text-center">Product ID</th>
					<th class="text-center">Product Name</th>
					<th class="text-center">Product Type</th>
					<th class="text-center">Standard By</th>
					<th class="text-center">Application</th>
					<th class="text-center">Created By</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  if($row){
					$int	=0;
					foreach($row as $datas){
						$int++;
						$detail = "";
						if(strtolower($datas->parent_product) == 'pipe'){
							$detail = "(".$datas->diameter." x ".$datas->panjang." x ".$datas->design.")";
						}
						echo"<tr>";							
							echo"<td align='center'>$int</td>";
							echo"<td align='center'>".$datas->id_product."</td>"; 
							echo"<td align='left'>".$datas->nm_product." ".$detail."</td>";
							echo"<td align='left'>".ucwords(strtolower($datas->parent_product))."</td>";
							echo"<td align='center'>".strtoupper($datas->standart_toleransi)."</td>";
							echo"<td align='center'>".$datas->aplikasi_product."</td>";
							echo"<td align='left'>".ucfirst(strtolower($datas->created_by))."</td>";
							echo"<td align='center'>";
								echo"<button type='button' id='MatDetail' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";									
								//echo"&nbsp;<a href=''id='printSPK' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-success' title='Print SPK' data-role='qtip'><i class='fa fa-print'></i></button>";									
								//echo "&nbsp;<a href='".base_url('calculation/printSPK/'.$datas->id_product)."' class='btn btn-sm btn-primary' target='_blank' title='Print SPK' data-role='qtip'><i class='fa fa-print'></i></a>";
								if($akses_menu['delete']=='1'){
									echo"&nbsp;<button id='del_type' data-idcategory='".$datas->id_product."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></button>";
								}
							echo"</td>";
						echo"</tr>";
					}
			  }
			  ?>
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:95%; '>
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

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
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
			$("#head_title").html("<b>DETAIL ESTIMATION ["+$(this).data('nm_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#del_type', function(){
			var bF	= $(this).data('idcategory');
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
			  closeOnConfirm: false,
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
	
</script>
