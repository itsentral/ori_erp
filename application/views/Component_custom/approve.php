<?php
$this->load->view('include/side_menu'); 
?> 
<form action="#" method="POST" id="form_proses_bro"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body" style="overflow-x:auto;">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th width='3px' class="text-center">No.</th>
					<th class="text-center">Product ID</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Spesifikasi</th>
					<th class="text-center">Stifness</th>
					<th class="text-center">Service Fluide</th>
					
					<th class="text-center">Est</th>
					<th class="text-center">Rev</th>
					<th class="text-center">Status</th>
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
						$kode1 = substr($datas->id_product, 0,3);
						$kode2 = substr($datas->id_product, 8,6);
						echo"<tr>";							
							echo"<td align='center'>".$int."</td>";
							echo"<td align='left'>".$datas->id_product."</td>"; 
							echo"<td align='left'>".strtoupper(strtolower($datas->nm_customer))."</td>";
							echo"<td align='left'>".$datas->nm_product." ".$detail."</td>";
							echo"<td align='center'>".$datas->stiffness."</td>";
							echo"<td align='left'>".ucwords(strtolower($datas->criminal_barier))."</td>";
							
							echo"<td align='left'>".ucfirst(strtolower($datas->created_by))."</td>";
							echo"<td align='center'>".$datas->rev."</td>";
								if($datas->status == 'WAITING APPROVAL'){
									$class	= 'bg-orange';
								}
								else{
									$class	= 'bg-green';
								}
							echo"<td align='center'><span class='badge ".$class."'>".ucwords(strtolower($datas->status))."</span></td>";
							echo"<td align='center'>";
								echo"<button type='button' id='MatDetail' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";									
								//echo"&nbsp;<a href=''id='printSPK' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-success' title='Print SPK' data-role='qtip'><i class='fa fa-print'></i></button>";									
								//echo "&nbsp;<a href='".base_url('calculation/printSPK/'.$datas->id_product)."' class='btn btn-sm btn-primary' target='_blank' title='Print SPK' data-role='qtip'><i class='fa fa-print'></i></a>";
								if($akses_menu['approve']=='1'){
									echo"&nbsp;<button id='approved' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' data-idcategory='".$datas->id_product."' class='btn btn-sm btn-primary' title='Approve Now' data-role='qtip'><i class='fa fa-check'></i></button>";
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
</form>
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
			$("#head_title").html("<b>DETAIL ESTIMATION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#approved', function(e){
			e.preventDefault();
			$("#head_title").html("<b>APPROVE ESTIMATION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalApprove/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
	});
	
</script>
